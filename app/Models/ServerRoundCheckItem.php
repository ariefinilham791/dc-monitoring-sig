<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerRoundCheckItem extends Model
{
    protected $table = 'server_round_check_items';

    protected $fillable = ['server_round_check_id', 'server_component_id', 'result', 'used_pct', 'free_pct', 'notes'];

    protected $casts = [
        'used_pct' => 'decimal:2',
        'free_pct' => 'decimal:2',
    ];

    public const RESULT_PENDING = 'pending';
    public const RESULT_OK = 'ok';
    public const RESULT_WARNING = 'warning';
    public const RESULT_ERROR = 'error';

    public function serverRoundCheck()
    {
        return $this->belongsTo(ServerRoundCheck::class, 'server_round_check_id');
    }

    public function serverComponent()
    {
        return $this->belongsTo(ServerComponent::class, 'server_component_id');
    }

    public static function resultLabels(): array
    {
        return [
            self::RESULT_PENDING => 'Belum dicek',
            self::RESULT_OK => 'OK',
            self::RESULT_WARNING => 'Warning',
            self::RESULT_ERROR => 'Error',
        ];
    }
}
