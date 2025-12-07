<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
// routes/web.php
require __DIR__.'/api.php';




Route::get('/', function () {
    return view('welcome');
});


