<?php

use App\Models\ServerRoundCheck;
use App\Models\ServerRoundCheckItem;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('user:set-password {email=admin@company.com} {password=password123}', function () {
    $email = $this->argument('email');
    $password = $this->argument('password');

    $user = User::where('email', $email)->first();
    if (! $user) {
        $this->error("User dengan email [{$email}] tidak ditemukan.");
        return 1;
    }

    $user->password = $password;
    $user->save();

    $this->info("Password untuk {$email} berhasil diubah.");
    $this->line("Login dengan: email = {$email}, password = {$password}");
    return 0;
})->purpose('Set password user (default: admin@company.com / password123)');

Artisan::command('checklist:clear', function () {
    if (! $this->confirm('Hapus SEMUA data checklist (log isian per server per periode)? Data tidak bisa dikembalikan.')) {
        $this->info('Dibatalkan.');
        return 0;
    }
    $items = ServerRoundCheckItem::count();
    $checks = ServerRoundCheck::count();
    DB::transaction(function () {
        ServerRoundCheckItem::query()->delete();
        ServerRoundCheck::query()->delete();
    });
    $this->info("Berhasil dihapus: {$items} item checklist, {$checks} round check.");
    $this->comment('Periode (checklist_rounds) tetap ada; isian per server dikosongkan.');
    return 0;
})->purpose('Hapus semua data checklist (item + round check), biar pakai data baru');
