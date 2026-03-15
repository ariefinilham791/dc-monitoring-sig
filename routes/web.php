<?php

use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\ComponentTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'))->name('root');
    Route::get('/account/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/account/checklist', [ChecklistController::class, 'index'])->name('checklist.index');
    Route::get('/account/checklist/history', [ChecklistController::class, 'history'])->name('checklist.history');
    Route::get('/account/checklist/log', [ChecklistController::class, 'log'])->name('checklist.log');
    Route::get('/account/checklist/log/export', [ChecklistController::class, 'exportLog'])->name('checklist.log.export')->middleware('throttle:5,1');
    Route::get('/account/checklist/fill/{serverRoundCheck}', [ChecklistController::class, 'fill'])->name('checklist.fill');
    Route::post('/account/checklist/fill/{serverRoundCheck}', [ChecklistController::class, 'store'])->name('checklist.store');
    Route::resource('account/component-type', ComponentTypeController::class)->names('component-type')->except(['show']);
    Route::resource('account/server', ServerController::class)->names('server');
    Route::post('account/server/{server}/components', [ServerController::class, 'storeComponent'])->name('server.components.store');
    Route::get('account/server/{server}/components/{server_component}/edit', [ServerController::class, 'editComponent'])->name('server.components.edit');
    Route::put('account/server/{server}/components/{server_component}', [ServerController::class, 'updateComponent'])->name('server.components.update');
    Route::delete('account/server/{server}/components/bulk', [ServerController::class, 'destroyComponentsBulk'])->name('server.components.destroy.bulk');
    Route::delete('account/server/{server}/components/{server_component}', [ServerController::class, 'destroyComponent'])->name('server.components.destroy');
    Route::post('account/server/{server}/checklist-items', [ServerController::class, 'storeChecklistItem'])->name('server.checklist.store');
    Route::patch('account/server/{server}/checklist-items/{server_checklist_item}/toggle', [ServerController::class, 'toggleChecklistItem'])->name('server.checklist.toggle');
    Route::delete('account/server/{server}/checklist-items/{server_checklist_item}', [ServerController::class, 'destroyChecklistItem'])->name('server.checklist.destroy');
    Route::get('/account/settings', fn () => view('account.settings'))->name('settings');
});
