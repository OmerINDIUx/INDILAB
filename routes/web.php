<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

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
    return view('home');
})->name('home');

Route::get('/index', function () {
    return redirect()->route('home');
});

Route::get('/who-we-are', function () {
    return view('who-we-are');
})->name('who-we-are');

Route::get('/what-we-do', [ProjectController::class, 'index'])->name('what-we-do');

Route::get('/our-approach', function () {
    return view('our-approach');
})->name('our-approach');

Route::get('/coming-soon', function () {
    return view('coming-soon');
})->name('coming-soon');

Route::get('/privacy-policy', function () {
    return view('aviso-privasidad');
})->name('privacy');

Route::resource('work', ProjectController::class)->parameters([
    'work' => 'project'
])->middleware('auth')->except(['index', 'show']);

// Public Routes (Excluded from Resource above)
Route::get('/work', [ProjectController::class, 'index'])->name('work.index');
Route::get('/work/{project}', [ProjectController::class, 'show'])->name('work.show');

// Auth Routes
Route::get('/login', [App\Http\Controllers\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\LoginController::class, 'logout'])->name('logout');

// Admin Routes (Protected)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Media Library Routes
    Route::get('/media', [App\Http\Controllers\MediaController::class, 'index'])->name('media.index');
    Route::post('/media', [App\Http\Controllers\MediaController::class, 'store'])->name('media.store');
    Route::delete('/media/{media}', [App\Http\Controllers\MediaController::class, 'destroy'])->name('media.destroy');
});
