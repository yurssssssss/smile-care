<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DentistController;
use Illuminate\Support\Facades\Route;

/*
 Root — redirect to login if guest
*/
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    return match(auth()->user()->role) {
        'admin'   => redirect()->route('admin.dashboard'),
        'dentist' => redirect()->route('dentist.dashboard'),
        default   => redirect()->route('patient.home'),
    };
});

Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/admin/register',  [AuthController::class, 'showAdminRegister'])->name('admin.register.show');
Route::post('/admin/register', [AuthController::class, 'adminRegister'])->name('admin.register');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
| Patient routes
*/
Route::middleware(['auth', 'patient'])->group(function () {
    Route::get('/patient/dashboard',                           [AppointmentController::class, 'index'])->name('patient.home');
    Route::get('/appointments/create',                         [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments/step/{step}',                   [AppointmentController::class, 'step'])->name('appointments.step');
    Route::post('/appointments',                               [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/cancel',         [AppointmentController::class, 'cancel'])->name('appointments.cancel');
});

Route::get('/appointments/slots', [AppointmentController::class, 'takenSlots'])
    ->middleware('auth')
    ->name('appointments.slots');

/*
| Admin routes
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',                                   [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/appointments',                                [AdminController::class, 'appointments'])->name('appointments');
    Route::patch('/appointments/{appointment}/status',         [AdminController::class, 'updateStatus'])->name('appointments.status');
    Route::get('/patients',                                    [AdminController::class, 'patients'])->name('patients');

    Route::get('/dentists',                                    [AdminController::class, 'dentists'])->name('dentists');
    Route::post('/dentists',                                   [AdminController::class, 'storeDentist'])->name('dentists.store');
    Route::post('/dentists/{dentist}/account',                 [AdminController::class, 'createDentistAccount'])->name('dentists.account');
    Route::patch('/dentists/{dentist}/toggle',                 [AdminController::class, 'toggleDentist'])->name('dentists.toggle');
    Route::patch('/users/{user}/password',                     [AdminController::class, 'resetDentistPassword'])->name('users.password');
});

/*
| Dentist routes
*/
Route::middleware(['auth', 'dentist'])->prefix('dentist')->name('dentist.')->group(function () {
    Route::get('/dashboard',                                   [DentistController::class, 'dashboard'])->name('dashboard');
    Route::get('/appointments',                                [DentistController::class, 'appointments'])->name('appointments');
    Route::patch('/appointments/{appointment}/action',         [DentistController::class, 'appointmentAction'])->name('appointments.action');
    Route::get('/patients',                                    [DentistController::class, 'patients'])->name('patients');
});

// Profile routes (all roles)
Route::middleware('auth')->group(function () {
    Route::get('/profile',          [AuthController::class, 'showProfile'])->name('profile');
    Route::post('/profile',         [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password',[AuthController::class, 'updatePassword'])->name('profile.password');
});
