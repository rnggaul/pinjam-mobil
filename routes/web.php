<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SecurityController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/history', function () {
    return view('history');
})->middleware(['auth', 'verified'])->name('history');



Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [BookingController::class, 'index'])->name('dashboard');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/history', [BookingController::class, 'history'])->name('history');
    Route::post('/booking/start/{booking}', [BookingController::class, 'startBooking'])->name('booking.start');
    Route::post('/booking/finish/{booking}', [BookingController::class, 'finishBooking'])->name('booking.finish');



    Route::middleware('admin')->group(function () {
        //Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.index');
        Route::resource('divisi', DivisiController::class);
        Route::resource('kendaraan', KendaraanController::class);

        Route::get('/admin/bookings', [AdminBookingController::class, 'index'])->name('admin.booking.index');
        Route::post('/admin/bookings/update/{booking}', [AdminBookingController::class, 'updateStatus'])->name('admin.booking.updateStatus');
        Route::get('/admin/bookings/history', [AdminBookingController::class, 'history'])->name('admin.booking.history');
        Route::get('/admin/bookings/export', [AdminBookingController::class, 'exportHistory'])->name('admin.booking.export');
    });
    Route::middleware('security')->group(function () {
        // Halaman utama security (menampilkan daftar + filter)
        Route::get('/security/dashboard', [SecurityController::class, 'index'])->name('security.dashboard');

        // Rute untuk memproses form KM Awal
        Route::post('/security/start/{booking}', [SecurityController::class, 'startBooking'])->name('security.start');

        // Rute untuk memproses form KM Akhir
        Route::post('/security/finish/{booking}', [SecurityController::class, 'finishBooking'])->name('security.finish');
    });
});

require __DIR__ . '/auth.php';
