<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['name', 'address', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function servers()
    {
        return $this->hasMany(Server::class, 'location_id');
    }
}
