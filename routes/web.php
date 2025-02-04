<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Categories\CategoryList;
use App\Livewire\Posts\PostList;
use App\Livewire\CollegeList;
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
    return view('welcome');
});

Route::get('/categories', CategoryList::class)->name('categories');
Route::get('/posts', PostList::class)->name('posts');
Route::get('/colleges', CollegeList::class)->name('colleges');  
