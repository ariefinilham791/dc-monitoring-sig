<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerChecklistItem extends Model
{
    protected $table = 'server_checklist_items';

    protected $fillable = [
        'server_id',
        'title',
        'is_checked',
        'sort_order',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
