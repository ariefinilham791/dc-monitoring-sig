<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerRoundCheck extends Model
{
    protected $fillable = ['server_id', 'checklist_round_id', 'status', 'completed_at'];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function checklistRound()
    {
        return $this->belongsTo(ChecklistRound::class, 'checklist_round_id');
    }

    public function checkItems()
    {
        return $this->hasMany(ServerRoundCheckItem::class, 'server_round_check_id');
    }
}
