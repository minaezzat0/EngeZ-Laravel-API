<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/test', function () {
    return "sssdssadmin";
});
Route::get('/home', function () {
    return view('home');
});

Route::get('/register','Usercontroller@register');

Route::post('/registeration','Usercontroller@registeration');
 


// Route::middleware('isadmin')->group(function(){
   
   
// });


    Route::get('/add/author', 'AuthorController@addauthor');
    Route::post('store/author', 'AuthorController@storeauthor');
    
    Route::get('/add/post', 'PostController@addpost');
    Route::post('store/post', 'PostController@storepost');
     
    Route::get('/show/posts', 'PostController@showposts');
    Route::get('/show/post/{id}', 'PostController@showpost');
    
    
    Route::get('/delete/post/{id}', 'PostController@deletepost');
    
    Route::get('/update/post/{id}', 'PostController@updatepost');
    Route::post('updated/post/{id}', 'PostController@storeupdatedpost');
    
    


Route::get('/login','Usercontroller@login');

Route::post('/handlelogin','Usercontroller@handlelogin');


Route::get('/users/facebook','Usercontroller@facebook');
Route::get('/users/facebookcallback','Usercontroller@facebookcallback');
