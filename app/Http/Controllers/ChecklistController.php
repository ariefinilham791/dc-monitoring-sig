<?php

namespace App\Http\Controllers;

use App\Exports\ChecklistLogExport;
use App\Models\ChecklistRound;
use App\Models\Server;
use App\Models\ServerRoundCheck;
use App\Models\ServerRoundCheckItem;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ChecklistController extends Controller
{
    public function index()
    {
        $year = (int) date('Y');
        $month = (int) date('n');
        $round = ChecklistRound::firstOrCreateFor($year, $month);

        $servers = Server::orderBy('sort_order')->orderBy('hostname')->get();
        $round->load('serverRoundChecks.server');

        $roundChecksByServer = $round->serverRoundChecks->keyBy('server_id');

        foreach ($servers as $server) {
            $rc = $roundChecksByServer->get($server->id);
            if (! $rc) {
                $rc = $round->serverRoundChecks()->create([
                    'server_id' => $server->id,
                    'status' => ServerRoundCheck::STATUS_PENDING,
                ]);
                $round->serverRoundChecks->push($rc);
                $roundChecksByServer->put($server->id, $rc);
            }
            $server->setRelation('currentRoundCheck', $rc);
        }

        $totalServers = $servers->count();
        $completed = $round->serverRoundChecks->where('status', ServerRoundCheck::STATUS_COMPLETED)->count();
        $pending = $totalServers - $completed;

        $okCount = 0;
        $warningCount = 0;
        $errorCount = 0;
        foreach ($round->serverRoundChecks as $rc) {
            $rc->load('checkItems');
            foreach ($rc->checkItems as $item) {
                match ($item->result) {
                    ServerRoundCheckItem::RESULT_OK => $okCount++,
                    ServerRoundCheckItem::RESULT_WARNING => $warningCount++,
                    ServerRoundCheckItem::RESULT_ERROR => $errorCount++,
                    default => null,
                };
            }
        }

        return view('account.checklist.index', compact(
            'round', 'servers', 'totalServers', 'completed', 'pending',
            'okCount', 'warningCount', 'errorCount'
        ));
    }

    public function fill(ServerRoundCheck $serverRoundCheck)
    {
        $serverRoundCheck->load(['server.components.componentType', 'checklistRound', 'checkItems']);
        $server = $serverRoundCheck->server;
        $round = $serverRoundCheck->checklistRound;
        $itemsByComponent = $serverRoundCheck->checkItems->keyBy(fn ($item) => (int) $item->server_component_id);

        return view('account.checklist.fill', [
            'serverRoundCheck' => $serverRoundCheck,
            'server' => $server,
            'round' => $round,
            'itemsByComponent' => $itemsByComponent,
        ]);
    }

    public function store(Request $request, ServerRoundCheck $serverRoundCheck)
    {
        $serverRoundCheck->load('server.components');
        $components = $serverRoundCheck->server->components;

        $rules = [];
        foreach ($components as $c) {
            $rules["result.{$c->id}"] = 'required|in:pending,ok,warning,error';
            $rules["used_pct.{$c->id}"] = 'nullable|numeric|min:0|max:100';
            $rules["free_pct.{$c->id}"] = 'nullable|numeric|min:0|max:100';
            $rules["notes.{$c->id}"] = 'nullable|string|max:1000';
        }
        $request->validate($rules);

        foreach ($components as $c) {
            ServerRoundCheckItem::updateOrCreate(
                [
                    'server_round_check_id' => $serverRoundCheck->id,
                    'server_component_id' => $c->id,
                ],
                [
                    'result' => $request->input("result.{$c->id}", 'pending'),
                    'used_pct' => $request->input("used_pct.{$c->id}") !== '' && $request->input("used_pct.{$c->id}") !== null
                        ? (float) $request->input("used_pct.{$c->id}") : null,
                    'free_pct' => $request->input("free_pct.{$c->id}") !== '' && $request->input("free_pct.{$c->id}") !== null
                        ? (float) $request->input("free_pct.{$c->id}") : null,
                    'notes' => $request->input("notes.{$c->id}"),
                ]
            );
        }

        $serverRoundCheck->update([
            'status' => ServerRoundCheck::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        return redirect()->route('checklist.index')
            ->with('success', 'Checklist untuk ' . $serverRoundCheck->server->hostname . ' berhasil disimpan.');
    }

    public function history()
    {
        $rounds = ChecklistRound::withCount('serverRoundChecks')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(20);

        return view('account.checklist.history', compact('rounds'));
    }

    public function log(Request $request)
    {
        $roundId = $request->get('round_id');
        $rounds = ChecklistRound::orderBy('year', 'desc')->orderBy('month', 'desc')->get();

        $round = null;
        $serverRoundChecksPaginator = null;

        if ($roundId) {
            $round = ChecklistRound::find($roundId);
            if (! $round) {
                return redirect()->route('checklist.log')->with('error', 'Round tidak ditemukan.');
            }
            $serverRoundChecksPaginator = ServerRoundCheck::query()
                ->where('checklist_round_id', $round->id)
                ->with(['server.components.componentType', 'checkItems.serverComponent.componentType'])
                ->join('servers', 'servers.id', '=', 'server_round_checks.server_id')
                ->orderBy('servers.hostname')
                ->select('server_round_checks.*')
                ->paginate(10)
                ->withQueryString();
        } else {
            $round = $rounds->first();
            if ($round) {
                $serverRoundChecksPaginator = ServerRoundCheck::query()
                    ->where('checklist_round_id', $round->id)
                    ->with(['server.components.componentType', 'checkItems.serverComponent.componentType'])
                    ->join('servers', 'servers.id', '=', 'server_round_checks.server_id')
                    ->orderBy('servers.hostname')
                    ->select('server_round_checks.*')
                    ->paginate(10)
                    ->withQueryString();
            }
        }

        return view('account.checklist.log', compact('rounds', 'round', 'serverRoundChecksPaginator'));
    }

    public function exportLog(Request $request)
    {
        $roundId = $request->get('round_id');
        if (! $roundId) {
            return redirect()->route('checklist.log')->with('error', 'Pilih periode terlebih dahulu.');
        }
        $round = ChecklistRound::with('serverRoundChecks.checkItems')->find($roundId);
        if (! $round) {
            return redirect()->route('checklist.log')->with('error', 'Periode tidak ditemukan.');
        }
        $totalItems = $round->serverRoundChecks->sum(fn ($src) => $src->checkItems->count());
        if ($totalItems === 0) {
            return redirect()->route('checklist.log', ['round_id' => $round->id])
                ->with('error', 'Belum ada data checklist untuk periode ini. Isi checklist dulu.');
        }
        $attributeColumns = ChecklistLogExport::buildAttributeColumns($round);
        $export = new ChecklistLogExport($round, $attributeColumns);
        $safePeriod = preg_replace('/[^a-zA-Z0-9\-]/', '-', $round->period_label);
        $safePeriod = trim(preg_replace('/-+/', '-', $safePeriod), '-');
        $fileName = 'Data-Center-Daily-Monitoring-' . ($safePeriod ?: $round->year . '-' . str_pad((string) $round->month, 2, '0', STR_PAD_LEFT)) . '.xlsx';
        return Excel::download($export, $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }
}
