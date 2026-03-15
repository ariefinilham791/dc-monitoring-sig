<?php

namespace App\Exports;

use App\Models\ChecklistRound;
use App\Models\Server;
use App\Models\ServerRoundCheckItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ChecklistLogExport implements FromCollection, WithHeadings, WithEvents
{
    public const TITLE_ROW = 'IT DATA CENTER Daily Monitoring';

    /** Jumlah kolom komponen per slot. */
    protected int $maxComponents = 0;

    /** Per slot: true = tampilkan %Terpakai & %Kosong, false = hanya status + catatan (Hanya status). */
    protected array $slotHasPct = [];

    public function __construct(
        protected ChecklistRound $round,
        protected array $attributeColumns = []
    ) {
        $this->round->load([
            'serverRoundChecks.server.components.componentType',
            'serverRoundChecks.checkItems.serverComponent.componentType',
        ]);
        foreach ($this->round->serverRoundChecks as $src) {
            $n = $src->server->components->count();
            if ($n > $this->maxComponents) {
                $this->maxComponents = $n;
            }
        }
        for ($i = 0; $i < $this->maxComponents; $i++) {
            $this->slotHasPct[$i] = false;
        }
        foreach ($this->round->serverRoundChecks as $src) {
            $comps = $src->server->components;
            foreach ($comps as $idx => $comp) {
                if ($idx >= $this->maxComponents) {
                    break;
                }
                $type = $comp->componentType;
                if ($type && empty($type->status_only)) {
                    $this->slotHasPct[$idx] = true;
                }
            }
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->insertNewRowBefore(1, 1);
                $sheet->setCellValue('A1', self::TITLE_ROW);
                $lastCol = $sheet->getHighestColumn();
                $sheet->mergeCells('A1:' . $lastCol . '1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension(1)->setRowHeight(22);
                $sheet->getStyle('A2:' . $lastCol . '2')->getFont()->setBold(true);
            },
        ];
    }

    public function headings(): array
    {
        $base = ['HOSTNAME', 'IP Address', 'Date', 'PHYSICAL STATUS'];
        for ($i = 0; $i < $this->maxComponents; $i++) {
            $n = $i + 1;
            $base[] = "Komponen {$n}";
            $base[] = "Result {$n}";
            if (! empty($this->slotHasPct[$i])) {
                $base[] = "%Terpakai {$n}";
                $base[] = "%Kosong {$n}";
            }
        }
        return $base;
    }

    public function collection(): Collection
    {
        $rows = collect();
        $roundDate = sprintf('%04d-%02d-01', $this->round->year, $this->round->month);
        $resultLabels = ServerRoundCheckItem::resultLabels();

        foreach ($this->round->serverRoundChecks as $src) {
            $server = $src->server;
            $hostname = $server->hostname ?? '';
            $ip = $server->ip_address ?? '';
            $physicalStatus = $server->physical_status ? (Server::statusLabels()[$server->physical_status] ?? $server->physical_status) : '';

            $completedAt = $src->completed_at;
            $dateStr = $completedAt ? $completedAt->format('Y-m-d') : $roundDate;

            $row = [$hostname, $ip, $dateStr, $physicalStatus];

            $itemsByComponent = $src->checkItems->keyBy(fn ($item) => (int) $item->server_component_id);
            $componentOrder = $server->components->pluck('id')->toArray();

            for ($i = 0; $i < $this->maxComponents; $i++) {
                $compId = $componentOrder[$i] ?? null;
                $item = $compId !== null ? $itemsByComponent->get($compId) : null;
                $comp = $item?->serverComponent;

                $compName = $comp ? ($comp->display_name ?? $comp->label ?? '') : '';
                $result = $item ? ($resultLabels[$item->result] ?? $item->result) : '';
                $usedPct = $item && $item->used_pct !== null ? (string) $item->used_pct : '';
                $freePct = $item && $item->free_pct !== null ? (string) $item->free_pct : '';

                if ($comp && $item) {
                    $typeSlug = strtolower(trim($comp->componentType->slug ?? ''));
                    $typeName = strtolower(trim($comp->componentType->name ?? ''));
                    $isVolumeLike = in_array($typeSlug, ['volume', 'disk', 'storage'], true) || in_array($typeName, ['volume', 'disk', 'storage'], true);
                    if ($isVolumeLike) {
                        $vals = $comp->values ?? [];
                        $cap = $vals['capacity'] ?? $vals['size'] ?? $vals['size_gb'] ?? null;
                        $capacityStr = $cap !== null && (string) $cap !== '' ? (string) $cap : null;
                        if ($capacityStr !== null && is_numeric($capacityStr)) {
                            $capacityStr .= 'GB';
                        }
                        $label = trim($comp->label ?? $comp->name ?? '');
                        $parts = [$label ?: $compName];
                        if ($capacityStr !== null) {
                            $parts[] = 'capacity ' . $capacityStr;
                        }
                        if (count($parts) > 1) {
                            $compName = implode(' ', $parts);
                        }
                    }
                }

                $row[] = $compName;
                $row[] = $result;
                if (! empty($this->slotHasPct[$i])) {
                    $row[] = $usedPct;
                    $row[] = $freePct;
                }
            }

            $rows->push($row);
        }

        return $rows;
    }

    /** Build attribute columns (name + slug) from all component types in this round. */
    public static function buildAttributeColumns(ChecklistRound $round): array
    {
        $round->load('serverRoundChecks.checkItems.serverComponent.componentType');
        $seen = [];
        $columns = [];
        foreach ($round->serverRoundChecks as $src) {
            foreach ($src->checkItems as $item) {
                $type = $item->serverComponent?->componentType;
                if (! $type || ! is_array($type->attributes)) {
                    continue;
                }
                foreach ($type->attributes as $attr) {
                    $name = $attr['name'] ?? '';
                    $slug = $attr['slug'] ?? \Illuminate\Support\Str::slug($name);
                    if ($slug !== '' && ! isset($seen[$slug])) {
                        $seen[$slug] = true;
                        $columns[] = ['name' => $name ?: $slug, 'slug' => $slug];
                    }
                }
            }
        }
        return $columns;
    }
}
