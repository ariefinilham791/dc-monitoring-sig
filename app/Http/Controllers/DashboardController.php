<?php

namespace App\Http\Controllers;

use App\Models\ChecklistRound;
use App\Models\ComponentType;
use App\Models\Location;
use App\Models\Server;
use App\Models\ServerRoundCheck;
use App\Models\ServerRoundCheckItem;

class DashboardController extends Controller
{
    public function index()
    {
        $totalServers = Server::count();
        $totalLocations = Location::count();
        $totalComponentTypes = ComponentType::count();

        $year = (int) date('Y');
        $month = (int) date('n');
        $round = ChecklistRound::firstOrCreateFor($year, $month);
        $servers = Server::orderBy('sort_order')->orderBy('hostname')->get();
        $round->load('serverRoundChecks');
        $roundChecksByServer = $round->serverRoundChecks->keyBy('server_id');
        foreach ($servers as $server) {
            if (! $roundChecksByServer->has($server->id)) {
                $round->serverRoundChecks()->create([
                    'server_id' => $server->id,
                    'status' => ServerRoundCheck::STATUS_PENDING,
                ]);
            }
        }
        $round->load('serverRoundChecks');
        $totalInRound = $round->serverRoundChecks->count();
        $completed = $round->serverRoundChecks->where('status', ServerRoundCheck::STATUS_COMPLETED)->count();
        $pending = $totalInRound - $completed;

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

        $pendingChecks = $round->serverRoundChecks()
            ->where('status', ServerRoundCheck::STATUS_PENDING)
            ->with('server')
            ->orderBy('id')
            ->limit(5)
            ->get();

        $serversByLocation = Location::withCount('servers')
            ->orderBy('name')
            ->get()
            ->map(fn ($loc) => ['label' => $loc->name, 'count' => $loc->servers_count]);

        $serversByType = collect([
            ['label' => 'Physical', 'count' => Server::where('server_type', 'physical')->count()],
            ['label' => 'Virtual', 'count' => Server::where('server_type', 'virtual')->count()],
            ['label' => 'Cloud', 'count' => Server::where('server_type', 'cloud')->count()],
        ]);

        return view('account.dashboard', [
            'totalServers' => $totalServers,
            'totalLocations' => $totalLocations,
            'totalComponentTypes' => $totalComponentTypes,
            'round' => $round,
            'totalInRound' => $totalInRound,
            'completed' => $completed,
            'pending' => $pending,
            'okCount' => $okCount,
            'warningCount' => $warningCount,
            'errorCount' => $errorCount,
            'pendingChecks' => $pendingChecks,
            'serversByLocation' => $serversByLocation,
            'serversByType' => $serversByType,
        ]);
    }
}
