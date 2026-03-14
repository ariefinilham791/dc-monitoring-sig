<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistRound extends Model
{
    protected $fillable = ['year', 'month', 'name'];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
    ];

    public function serverRoundChecks()
    {
        return $this->hasMany(ServerRoundCheck::class, 'checklist_round_id');
    }

    public function getPeriodLabelAttribute(): string
    {
        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return ($months[$this->month] ?? $this->month) . ' ' . $this->year;
    }

    public static function firstOrCreateFor(int $year, int $month): self
    {
        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $name = ($months[$month] ?? (string) $month) . ' ' . $year;
        return self::firstOrCreate(
            ['year' => $year, 'month' => $month],
            ['name' => $name]
        );
    }
}
