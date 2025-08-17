<?php

use App\Http\Controllers\user\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\user\UserController;
use App\Http\Controllers\admin\AdminUserController;
use App\Http\Controllers\admin\AdminCourtsController;
use App\Http\Controllers\admin\AdminBookingController;
use App\Http\Controllers\admin\AdminScheduleController;
use App\Http\Controllers\admin\AdminPaymentController;
use App\Http\Controllers\admin\AdminStatusController;
use App\Http\Controllers\user\CourtsController;
use App\Http\Controllers\user\BookingsController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\admin\AdminLaporanController;
use App\Models\BasketCourt;
use Illuminate\Support\Facades\Mail;
use App\Models\Court;

// Landing page dengan auth check
Route::get('/', function () {
    if (!auth()->check()) {
        $courts = BasketCourt::where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('welcome', compact('courts'));
    }

    if (auth()->user()->role_id === 1) {
        return app(AdminController::class)->index();
    }

    return app(UserController::class)->index();
})->name('home');

// User routes group
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::prefix('user')->name('user.')->group(function () {
        // Profile Management
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
            Route::put('password', [ProfileController::class, 'updatePassword'])->name('password.update');
        });

        // Courts Management
        Route::prefix('courts')->controller(CourtsController::class)->group(function () {
            Route::get('/', 'index')->name('courts.index');
            Route::get('/{court}', 'show')->name('courts.show');
        });

        // Booking Management
        Route::prefix('bookings')->controller(BookingsController::class)->group(function () {
            Route::get('/', 'index')->name('bookings.index');
            Route::get('/keranjang', 'keranjang')->name('bookings.keranjang');
            Route::post('/{court}/store', 'store')->name('bookings.store');
            Route::post('/checkout', 'checkout')->name('bookings.checkout');

            // Tambahkan route payment sebelum route dengan parameter
            Route::get('/payment', 'payment')->name('bookings.payment');
            Route::post('/process-payment', 'processPayment')->name('bookings.process-payment');

            // Route dengan parameter harus berada di bawah
            Route::get('/{booking}', 'show')->name('bookings.show');
            Route::delete('/{booking}', 'destroy')->name('bookings.destroy');
            Route::get('/{booking}/schedules', 'getSchedules')->name('bookings.schedules');
            Route::post('/{booking}/schedule', 'updateSchedule')->name('bookings.update-schedule');
            Route::put('/{booking}/reschedule', 'reschedule')->name('bookings.reschedule');
        });
    });
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('user', AdminUserController::class)->names([
        'index' => 'user',
        'store' => 'user.store',
        'update' => 'user.update',
        'destroy' => 'user.destroy',
    ]);

    Route::resource('court', AdminCourtsController::class)->names([
        'index' => 'court',
        'store' => 'court.store',
        'update' => 'court.update',
        'destroy' => 'court.destroy',
    ]);

    Route::resource('booking', AdminBookingController::class)->names([
        'index' => 'booking',
        'store' => 'booking.store',
        'update' => 'booking.update',
        'destroy' => 'booking.destroy',
    ]);

    Route::resource('schedule', AdminScheduleController::class)->names([
        'index' => 'schedule',
        'store' => 'schedule.store',
        'update' => 'schedule.update',
        'destroy' => 'schedule.destroy',
    ]);

    Route::resource('payment', AdminPaymentController::class)->names([
        'index' => 'payment',
        'store' => 'payment.store',
        'update' => 'payment.update',
        'destroy' => 'payment.destroy',
    ]);

    Route::resource('status', AdminStatusController::class)->names([
        'index' => 'status',
        'store' => 'status.store',
        'update' => 'status.update',
        'destroy' => 'status.destroy',
    ]);

    Route::resource('laporan', AdminLaporanController::class)->only(['index'])->names([
        'index' => 'laporan',
    ]);
    Route::get('/laporan/export', [AdminLaporanController::class, 'export'])->name('laporan.export');

    Route::post('/booking/member', [AdminBookingController::class, 'storeMember'])
        ->name('booking.store-member');

    Route::put('/admin/schedule/{schedule}', [AdminScheduleController::class, 'update'])->name('admin.schedule.update');

    Route::delete('/schedule/{schedule}/recurring', [AdminScheduleController::class, 'destroyRecurring'])
        ->name('schedule.destroy.recurring');

    // Routes untuk fitur libur lapangan
    Route::prefix('court')->name('court.')->group(function () {
        Route::post('/holiday', [AdminCourtsController::class, 'setHoliday'])->name('setHoliday');
        Route::post('/holiday/remove', [AdminCourtsController::class, 'removeHoliday'])->name('removeHoliday');
    });
});

Route::get('/api/courts/{court}', function (BasketCourt $court) {
    return response()->json([
        'id' => $court->id,
        'name' => $court->name,
        'location' => $court->location,
        'price_per_hour' => $court->price_per_hour,
        'description' => $court->description,
        'image' => $court->image
    ]);
});

Route::get('/test-email', function () {
    try {
        Mail::raw('Test email dari Bendella Basket', function ($message) {
            $message->to('ddsa47245@gmail.com')
                   ->subject('Test Email');
        });
        return 'Email terkirim!';
    } catch (\Exception $e) {
        \Log::error('Test email error: ' . $e->getMessage());
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/test-smtp', function() {
    try {
        $transport = new \Swift_SmtpTransport(
            config('mail.mailers.smtp.host'),
            config('mail.mailers.smtp.port'),
            config('mail.mailers.smtp.encryption')
        );
        $transport->setUsername(config('mail.mailers.smtp.username'));
        $transport->setPassword(config('mail.mailers.smtp.password'));
        
        $mailer = new \Swift_Mailer($transport);
        $mailer->getTransport()->start();
        
        return "SMTP connection successful!";
    } catch (\Exception $e) {
        return "SMTP connection failed: " . $e->getMessage();
    }
});

// Route untuk mengambil available slots
Route::get('/api/courts/{court}/available-slots', [AdminBookingController::class, 'getAvailableSlots']);
// Route untuk menyimpan member booking
Route::post('/admin/booking/member', [AdminBookingController::class, 'storeMember'])->name('admin.booking.member.store');

require __DIR__ . '/auth.php';
