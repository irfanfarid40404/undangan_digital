<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\App\CatalogController;
use App\Http\Controllers\App\CheckoutController;
use App\Http\Controllers\App\DesignController;
use App\Http\Controllers\App\OrderStatusController;
use App\Http\Controllers\App\OrderWizardController;
use App\Http\Controllers\App\NotificationController;
use App\Http\Controllers\App\PaymentController;
use App\Http\Controllers\App\ProfileController;
use App\Http\Controllers\App\UserDashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\InvitationWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InvitationWebController::class, 'home'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/flow-failed', [InvitationWebController::class, 'flowFailed'])->name('flow.failed');

Route::middleware('auth')->prefix('app')->name('user.')->group(function () {
    Route::get('/dashboard', UserDashboardController::class)->name('dashboard');
    Route::get('/catalog', CatalogController::class)->name('catalog');
    Route::get('/design/{slug?}', DesignController::class)->name('design');

    Route::get('/order/form', [OrderWizardController::class, 'create'])->name('order.form');
    Route::post('/orders', [OrderWizardController::class, 'store'])->name('orders.store');

    Route::get('/checkout', CheckoutController::class)->name('checkout');
    Route::get('/payment', [PaymentController::class, 'show'])->name('payment');
    Route::post('/payment', [PaymentController::class, 'store'])->name('payment.store');
    Route::post('/payment/demo-paid', [PaymentController::class, 'demoPaid'])->name('payment.demo-paid');

    Route::get('/orders', [OrderStatusController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderStatusController::class, 'show'])->name('orders.show');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Notifications
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'delete'])->name('notifications.delete');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

    Route::get('/products', [AdminProductController::class, 'index'])->name('products');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments');
    Route::post('/payment-methods/{methodCode}', [AdminPaymentController::class, 'saveMethod'])->name('payment-methods.save');
    Route::post('/payments/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('payments.verify');
    Route::post('/payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');

    Route::get('/reports', AdminReportController::class)->name('reports');
});
