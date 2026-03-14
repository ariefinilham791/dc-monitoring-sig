<?php

namespace App\Exports;

use App\Models\ChecklistRound;
use App\Models\ServerRoundCheckItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ChecklistLogExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected ChecklistRound $round,
        protected array $attributeColumns = []
    ) {
    }

    public function headings(): array
    {
        $base = ['Server', 'OS / Fungsi', 'IP', 'Komponen'];
        $attrHeaders = array_column($this->attributeColumns, 'name');
        $rest = ['Status', 'Used %', 'Free %', 'Catatan'];
        return array_merge($base, $attrHeaders, $rest);
    }

    public function collection(): Collection
    {
        $rows = collect();
        $this->round->load([
            'serverRoundChecks.server',
            'serverRoundChecks.checkItems.serverComponent.componentType',
        ]);

        foreach ($this->round->serverRoundChecks as $src) {
            $server = $src->server;
            $hostname = $server->hostname ?? '';
            $os = (string) ($server->os ?? '');
            $ip = $server->ip_address ?? '';

            foreach ($src->checkItems as $item) {
                $comp = $item->serverComponent;
                $displayName = $comp->display_name ?? '';

                $attrValues = [];
                foreach ($this->attributeColumns as $col) {
                    $slug = $col['slug'];
                    $val = $comp->values[$slug] ?? '';
                    $attrValues[] = is_scalar($val) ? (string) $val : '';
                }

                $statusLabel = ServerRoundCheckItem::resultLabels()[$item->result] ?? $item->result;
                $usedPct = $item->used_pct !== null ? (string) $item->used_pct : '';
                $freePct = $item->free_pct !== null ? (string) $item->free_pct : '';
                $notes = (string) ($item->notes ?? '');

                $rows->push(array_merge(
                    [$hostname, $os, $ip, $displayName],
                    $attrValues,
                    [$statusLabel, $usedPct, $freePct, $notes]
                ));
            }
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
