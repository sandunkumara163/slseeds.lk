<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController as CustomerProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use Illuminate\Support\Facades\Route;

// Customer Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [CustomerProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [CustomerProductController::class, 'show'])->name('products.show');

// Customer Protected Routes
Route::get('/lang/{lang}', [\App\Http\Controllers\LanguageController::class, 'switchLang'])->name('lang.switch');

// PayHere Routes
Route::post('/payhere/notify', [\App\Http\Controllers\PayHereController::class, 'notify'])->name('payhere.notify');
Route::get('/payhere/return', [\App\Http\Controllers\PayHereController::class, 'returnPage'])->name('payhere.return');
Route::get('/payhere/cancel', [\App\Http\Controllers\PayHereController::class, 'cancelPage'])->name('payhere.cancel');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\CustomerOrderController::class, 'index'])->name('dashboard');
    Route::post('/orders/{order}/cancel', [\App\Http\Controllers\CustomerOrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{order}/receipt', [\App\Http\Controllers\OrderReceiptController::class, 'download'])->name('orders.receipt');

    // Cart Routes
    Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [\App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');

    // Checkout Routes
    Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout/process', [\App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/{order}/payhere', [\App\Http\Controllers\CheckoutController::class, 'payhere'])->name('checkout.payhere');
    Route::get('/checkout/success/{order}', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');

    // Review Routes
    Route::post('/products/{product}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Wishlist Routes
    Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}/toggle', [\App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Customer Chat
    Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/messages', [\App\Http\Controllers\ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/messages', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::put('/chat/messages/{message}', [\App\Http\Controllers\ChatController::class, 'editMessage'])->name('chat.edit');
    Route::delete('/chat/messages/{message}', [\App\Http\Controllers\ChatController::class, 'deleteMessage'])->name('chat.delete');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class);
    Route::resource('products', AdminProductController::class);
    Route::delete('/product-images/{image}', [AdminProductController::class, 'deleteImage'])->name('products.images.destroy');
    
    // Notifications
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // Admin Reviews
    Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{review}/reply', [\App\Http\Controllers\Admin\ReviewController::class, 'reply'])->name('reviews.reply');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Admin Chat
    Route::get('/chats', [\App\Http\Controllers\Admin\ChatController::class, 'index'])->name('chats.index');
    Route::post('/chats/{conversation}/messages', [\App\Http\Controllers\Admin\ChatController::class, 'sendMessage'])->name('chats.messages.store');
    Route::delete('/chats/{conversation}', [\App\Http\Controllers\Admin\ChatController::class, 'deleteChat'])->name('chats.destroy');
    Route::put('/messages/{message}', [\App\Http\Controllers\Admin\ChatController::class, 'editMessage'])->name('messages.update');
    Route::delete('/messages/{message}', [\App\Http\Controllers\Admin\ChatController::class, 'deleteMessage'])->name('messages.destroy');

    // Admin Orders
    Route::get('/orders/export/csv', [\App\Http\Controllers\Admin\OrderController::class, 'exportCsv'])->name('orders.exportCsv');
    Route::get('/orders/export/pdf', [\App\Http\Controllers\Admin\OrderController::class, 'exportPdf'])->name('orders.exportPdf');
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [\App\Http\Controllers\Admin\OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/orders/{order}/receipt', [\App\Http\Controllers\OrderReceiptController::class, 'download'])->name('orders.receipt');

    // Admin Customers
    Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class);

    // Admin Stock
    Route::get('/stock', [\App\Http\Controllers\Admin\StockController::class, 'index'])->name('stock.index');
    Route::put('/stock/{product}', [\App\Http\Controllers\Admin\StockController::class, 'update'])->name('stock.update');

    // Admin Financial
    Route::get('/financial', [\App\Http\Controllers\Admin\FinancialController::class, 'index'])->name('financial.index');
    Route::get('/financial/export/pdf', [\App\Http\Controllers\Admin\FinancialController::class, 'exportPdf'])->name('financial.export.pdf');
    Route::get('/financial/export/excel', [\App\Http\Controllers\Admin\FinancialController::class, 'exportExcel'])->name('financial.export.excel');
});

require __DIR__.'/auth.php';





