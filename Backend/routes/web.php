<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



// Route::get('/test', function () {
//     return view('test');
// });

// use App\Http\Controllers\ProjectController;

// Route::get('/test', [ProjectController::class, 'showImage']);

