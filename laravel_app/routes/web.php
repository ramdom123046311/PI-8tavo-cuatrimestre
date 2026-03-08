<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/dashboard');
});

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});

Route::get('/admin/verification', function () {
    return view('admin.verification');
});

Route::get('/admin/security', function () {
    return view('admin.security');
});

Route::get('/admin/users', function () {
    return view('admin.users');
});

Route::get('/admin/articles', function () {
    return view('admin.articles');
});
