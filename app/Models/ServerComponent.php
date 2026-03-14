<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerComponent extends Model
{
    protected $table = 'server_components';

    protected $fillable = [
        'server_id',
        'component_type_id',
        'label',
        'values',
        'name',
        'type',
        'status',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'values' => 'array',
        'sort_order' => 'integer',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function componentType()
    {
        return $this->belongsTo(ComponentType::class, 'component_type_id');
    }

    /** Display name: type name + label, e.g. "Disk - Disk 0" */
    public function getDisplayNameAttribute(): string
    {
        if ($this->componentType) {
            return trim($this->componentType->name . ' - ' . ($this->label ?? ''));
        }
        return $this->label ?? $this->name ?? 'Component #' . $this->id;
    }

    /** Spesifikasi singkat dari atribut yang sudah diisi beserta nama atribut, e.g. "Capacity: 900GB, Serial: 123" */
    public function getSpecSummaryAttribute(): string
    {
        $values = $this->values ?? [];
        if (empty($values) || ! is_array($values)) {
            return '';
        }
        $type = $this->componentType;
        $attrs = $type && is_array($type->attributes) ? $type->attributes : [];
        $ordered = [];
        foreach ($attrs as $attr) {
            $name = $attr['name'] ?? '';
            $slug = $attr['slug'] ?? \Illuminate\Support\Str::slug($name);
            if ($slug !== '' && isset($values[$slug]) && (string) $values[$slug] !== '') {
                $label = $name !== '' ? $name : $slug;
                $ordered[] = $label . ': ' . $values[$slug];
            }
        }
        if ($ordered !== []) {
            return implode(', ', $ordered);
        }
        $fallback = [];
        foreach ($values as $k => $v) {
            if ((string) $v !== '') {
                $fallback[] = $k . ': ' . $v;
            }
        }
        return implode(', ', $fallback);
    }
}
