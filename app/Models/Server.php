<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $fillable = [
        'location_id',
        'hostname',
        'ip_address',
        'os',
        'server_type',
        'physical_status',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public const SERVER_TYPE_PHYSICAL = 'physical';
    public const SERVER_TYPE_VIRTUAL = 'virtual';
    public const SERVER_TYPE_CLOUD = 'cloud';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_DECOMMISSIONED = 'decommissioned';
    public const STATUS_INACTIVE = 'inactive';

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function components()
    {
        return $this->hasMany(ServerComponent::class)->orderBy('sort_order')->orderBy('id');
    }

    public function checklistItems()
    {
        return $this->hasMany(ServerChecklistItem::class)->orderBy('id');
    }

    public function serverRoundChecks()
    {
        return $this->hasMany(ServerRoundCheck::class)->orderBy('checklist_round_id', 'desc');
    }

    public static function serverTypeLabels(): array
    {
        return [
            self::SERVER_TYPE_PHYSICAL => 'Physical',
            self::SERVER_TYPE_VIRTUAL => 'Virtual',
            self::SERVER_TYPE_CLOUD => 'Cloud',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_MAINTENANCE => 'Maintenance',
            self::STATUS_DECOMMISSIONED => 'Decommissioned',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }
}
