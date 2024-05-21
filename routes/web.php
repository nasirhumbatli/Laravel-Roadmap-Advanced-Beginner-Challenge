<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\{
    ProfileController,
    UserController,
    ClientController,
    ProjectController,
    TaskController,
};

Route::redirect('/', '/login');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::resource('users', UserController::class)->only('index', 'edit', 'update', 'destroy');
    Route::resources([
        'clients' => ClientController::class,
        'projects' => ProjectController::class,
        'tasks' => TaskController::class,
    ]);
    Route::singleton('profile', ProfileController::class)->only('edit', 'update', 'destroy')->destroyable();
});

require __DIR__.'/auth.php';
