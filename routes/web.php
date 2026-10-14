<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodoListController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(auth()->check() ? route('lists.index') : route('login'));
});

Route::middleware(['auth', 'profile.complete'])->group(function () {
    Route::get('/overview', [TodoListController::class, 'overview'])->name('lists.overview');
    Route::get('/lists', [TodoListController::class, 'index'])->name('lists.index');
    Route::post('/lists', [TodoListController::class, 'store'])->name('lists.store');
    Route::get('/lists/{list}', [TodoListController::class, 'show'])->name('lists.show');
    Route::patch('/lists/{list}', [TodoListController::class, 'update'])->name('lists.update');
    Route::delete('/lists/{list}', [TodoListController::class, 'destroy'])->name('lists.destroy');

    Route::post('/lists/{list}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
});

require __DIR__.'/auth.php';
