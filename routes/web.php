<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManajerController;
use App\Http\Controllers\ProfileController;

// Redirect root to dashboard if logged in, else show landing page
Route::get('/', function () {
    if (auth()->check()) {
        $activeRole = session('active_role', auth()->user()->role);
        if ($activeRole === 'karyawan') return redirect()->route('karyawan.dashboard');
        if ($activeRole === 'admin' || $activeRole === 'hr') return redirect()->route('admin.dashboard');
        if ($activeRole === 'manajer') return redirect()->route('manajer.dashboard');
    }
    return view('welcome');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', function () {
        return redirect()->route('login')->with('info', 'Registrasi mandiri tidak tersedia. Akun karyawan didaftarkan oleh HR.');
    })->name('register');
    Route::post('/register', function () {
        return redirect()->route('login');
    })->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Shared Profile Routes (All authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Karyawan (Employee) Routes
Route::middleware(['auth', 'role:karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/dashboard', [KaryawanController::class, 'dashboard'])->name('dashboard');
    Route::post('/komentar', [KaryawanController::class, 'storeKomentar'])->name('komentar.store');
    Route::get('/absensi', [KaryawanController::class, 'absensi'])->name('absensi');
    Route::post('/absensi', [KaryawanController::class, 'storeAbsensi'])->name('absensi.store');
    Route::post('/absensi/pengajuan', [KaryawanController::class, 'storePengajuanIzin'])->name('absensi.pengajuan.store');
    Route::get('/tasks', [KaryawanController::class, 'tasks'])->name('tasks');
    Route::get('/tasks/{id}', [KaryawanController::class, 'taskDetail'])->name('task.detail');
    Route::post('/tasks/{taskId}/checklist/{checklistId}/toggle', [KaryawanController::class, 'toggleChecklist'])->name('task.checklist.toggle');
    Route::post('/tasks/{id}/submit', [KaryawanController::class, 'submitWork'])->name('task.submit');
});

// Admin / HR Routes - READ ONLY monitoring + user management
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/monitoring', [AdminController::class, 'monitoring'])->name('monitoring');
    Route::get('/absensi', [AdminController::class, 'absensi'])->name('absensi');
    Route::post('/absensi/pengajuan/{id}/approve', [AdminController::class, 'approveIzin'])->name('absensi.pengajuan.approve');
    Route::post('/absensi/pengajuan/{id}/reject', [AdminController::class, 'rejectIzin'])->name('absensi.pengajuan.reject');

    // CRUD Users (Karyawan & Manajer)
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.destroy');
});

// Manajer (Manager) Routes - Full task management + evaluation
Route::middleware(['auth', 'role:manajer'])->prefix('manajer')->name('manajer.')->group(function () {
    Route::get('/dashboard', [ManajerController::class, 'dashboard'])->name('dashboard');

    // Task CRUD (Manajer manages tasks)
    Route::get('/tasks', [ManajerController::class, 'tasks'])->name('tasks');
    Route::post('/tasks', [ManajerController::class, 'storeTask'])->name('tasks.store');
    Route::put('/tasks/{id}', [ManajerController::class, 'updateTask'])->name('tasks.update');
    Route::delete('/tasks/{id}', [ManajerController::class, 'deleteTask'])->name('tasks.destroy');

    // Performance & Evaluation
    Route::get('/performance', [ManajerController::class, 'performance'])->name('performance');
    Route::get('/performance/employee/{id}', [ManajerController::class, 'employeeDetail'])->name('employee.detail');
    Route::get('/tasks/{id}/evaluate', [ManajerController::class, 'showEvaluate'])->name('task.evaluate');
    Route::post('/tasks/{id}/evaluate', [ManajerController::class, 'storeEvaluate'])->name('task.storeEvaluation');
    Route::get('/evaluations', [ManajerController::class, 'evaluations'])->name('evaluations');
});
