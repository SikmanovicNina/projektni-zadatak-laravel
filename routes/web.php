<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    event(new \App\Events\BooksFetchedEvent('hello from Web.php', 1));

    return view('welcome');
});
