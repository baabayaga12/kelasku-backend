<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TestController;
use App\Http\Controllers\API\AttemptController;
use App\Http\Controllers\API\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Simple health check route (no DB, no middleware) to verify the app is up.
Route::get('/health', function () {
    return response('OK', 200);
});

// Route to serve images from storage, handling URL encoded filenames
Route::get('/storage/images/{filename}', function ($filename) {
    $decodedFilename = urldecode($filename);
    $path = storage_path('app/public/images/' . $decodedFilename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path, [
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('filename', '.*');

// Auth routes without CSRF
// NOTE: login/register moved to routes/api.php so they run under the stateless `api` middleware

// NOTE: Public API routes are defined in routes/api.php to keep them stateless