<?php

use App\Http\Controllers\PortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortalController::class, 'home'])->name('home');
Route::match(['get', 'post'], '/login', [PortalController::class, 'login'])->name('login');
Route::get('/logout', [PortalController::class, 'logout'])->name('logout');
Route::match(['get', 'post'], '/register-pharmacy', [PortalController::class, 'registerPharmacy'])->name('register.pharmacy');

Route::get('/admin/dashboard', [PortalController::class, 'adminDashboard'])->name('admin.dashboard');
Route::match(['get', 'post'], '/admin/orders', [PortalController::class, 'adminOrders'])->name('admin.orders');
Route::post('/admin/orders/{tracking}/complete', [PortalController::class, 'completeOrder'])->name('admin.orders.complete');
Route::match(['get', 'post'], '/admin/pharmacies', [PortalController::class, 'adminPharmacies'])->name('admin.pharmacies');
Route::post('/admin/pharmacies/{nif}/delete', [PortalController::class, 'deletePharmacy'])->name('admin.pharmacies.delete');
Route::match(['get', 'post'], '/admin/employees', [PortalController::class, 'adminEmployees'])->name('admin.employees');
Route::match(['get', 'post'], '/admin/tracking', [PortalController::class, 'adminTracking'])->name('admin.tracking');
Route::get('/admin/settings', [PortalController::class, 'adminSettings'])->name('admin.settings');
Route::get('/admin/inventory', [PortalController::class, 'adminInventory'])->name('admin.inventory');
Route::get('/admin/reports', [PortalController::class, 'adminReports'])->name('admin.reports');
Route::get('/admin/payments', [PortalController::class, 'adminPayments'])->name('admin.payments');

Route::get('/commercial/dashboard', [PortalController::class, 'commercialDashboard'])->name('commercial.dashboard');
Route::match(['get', 'post'], '/delivery-manager/dashboard', [PortalController::class, 'deliveryManagerDashboard'])->name('delivery-manager.dashboard');
Route::match(['get', 'post'], '/delivery-person/dashboard', [PortalController::class, 'deliveryPersonDashboard'])->name('delivery-person.dashboard');
Route::match(['get','post'], '/pharmacy/dashboard', [PortalController::class, 'pharmacyDashboard'])->name('pharmacy.dashboard');
Route::match(['get', 'post'], '/stock/dashboard', [PortalController::class, 'stockDashboard'])->name('stock.dashboard');
