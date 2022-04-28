<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


          Route::post('/register','ApiUserController@register');
          Route::post('/login','ApiUserController@login');
      
          Route::get('/categories','ApiCategoryController@index');
          Route::post('/categories','ApiCategoryController@store');

    
      Route::middleware('is_api_user')->group(function(){

          Route::get('/logout','ApiUserController@logout');
          Route::get('/myjobs','ApiJobController@myjobs');
          Route::get('/jobs','ApiJobController@index');
          Route::post('/jobs','ApiJobController@store');
          Route::get('/jobs/{id}','ApiJobController@show');
          Route::post('jobs/{id}','ApiJobController@edite');
          Route::delete('jobs/{id}','ApiJobController@delete');
          Route::post('/contacts','ApiContactController@store');
          Route::delete('/contacts/{id}','ApiContactController@delete');
          Route::post('/contracts','ApiContractController@store');
      });
          Route::get('/mycontracts','ApiContractController@mycontracts');
          Route::delete('/owncontacts/{id}', 'ApiContactController@owndelete');
          Route::get('contractsforuser/{id}','ApiContractController@contractforuser');
          Route::get('/jobuser/{id}','ApiJobController@getjobsforuser');
          Route::get('/contracts/{id}','ApiContractController@show');
          Route::get('joboffers/{id}','ApiOfferController@offersByJobID');
          Route::get('/verify/offers/{id}','ApiOfferController@verifyoffer');
          Route::get('offers/{id}','ApiOfferController@show');

   Route::middleware('is_api_admin')->group(function(){
         Route::get('/users','ApiUserController@index');
         Route::get('/users/{id}','ApiUserController@show');
         Route::post('/users/{id}','ApiUserController@edite');
         Route::delete('/users/{id}','ApiUserController@delete');
         Route::get('/dashboard','ApiUserController@dashboard');
         Route::delete('/categories/{id}','ApiCategoryController@delete');
         Route::post('/categories/{id}','ApiCategoryController@edit');
         Route::get('offers','ApiOfferController@index'); 
         Route::get('/contacts','ApiContactController@index');
         Route::get('/admins','ApiUserController@admins');
         Route::get('/freelancers','ApiUserController@freelancers');
         Route::get('/ordinaryusers','ApiUserController@ordinaryusers');
         Route::get('/latestfivejobs','ApiJobController@latestfivejobs');
         Route::get('/contracts','ApiContractController@index');
  });

  Route::middleware('is_api_freelancer')->group(function(){
    Route::post('offers','ApiOfferController@store');
    Route::delete('offers/{id}','ApiOfferController@delete');
  });

  


  // Route::get('login/google', 'ApiUserController@google');
  // Route::get('login/google/callback', 'ApiUserController@googleCallback');

  // Route::get('login/facebook', 'ApiUserController@facebook');
  // Route::get('login/facebook/callback', 'ApiUserController@facebookCallback');

  Route::group(['middleware' => ['web']], function () {
      // your routes here
      
  // Route::get('login/github', 'ApiUserController@github');
  // Route::get('login/github/callback', 'ApiUserController@githubCallback');
 

  Route::get('login/{google}', 'ApiUserController@google');
  Route::get('login/{google}/callback', 'ApiUserController@googleCallback');
 

});
