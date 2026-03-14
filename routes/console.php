<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

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
