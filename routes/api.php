<?php

use Illuminate\Support\Facades\Route;

// Import your custom API controllers
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AuthController;

// ─── PUBLIC RESORT ENDPOINTS ───
// Maps to: POST http://localhost:8000/api/bookings
Route::post('/bookings', [BookingController::class, 'store']);

// ─── ADMIN PORTAL ENDPOINTS ───
// Maps to: POST http://localhost:8000/api/admin/login
Route::post('/admin/login', [AuthController::class, 'login']);

// Maps to: GET http://localhost:8000/api/admin/overview
Route::get('/admin/overview', [AdminDashboardController::class, 'getOverview']);

// Maps to: PATCH http://localhost:8000/api/admin/bookings/{id}/status
Route::patch('/admin/bookings/{id}/status', [AdminDashboardController::class, 'updateStatus']);