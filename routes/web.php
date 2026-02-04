<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Client\InvoiceController as ClientInvoiceController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('invoices', AdminInvoiceController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('invoices/{id}/pdf', [AdminInvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::resource('users', AdminUserController::class);

    // Admin Chat Config
    Route::get('chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::get('chat/{user}', [App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');
});

Route::middleware(['auth'])->group(function () {
    // Shared Chat API
    Route::get('/chat/messages/{userId}', [App\Http\Controllers\ChatController::class, 'fetchMessages']);
    Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'store']);
});

Route::middleware(['auth'])->name('client.')->group(function () {
    Route::get('my-invoices', [ClientInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('my-invoices/{id}', [ClientInvoiceController::class, 'show'])->name('invoices.show');
    Route::get('my-invoices/{id}/success', [ClientInvoiceController::class, 'paymentSuccess'])->name('invoices.payment_success');
    Route::get('my-invoices/{id}/pdf', [ClientInvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::get('chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
});
