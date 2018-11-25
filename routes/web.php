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

//Route::get('/', function () {
//    return view('welcome');
//});

Route::group(['namespace'=>'Api'], function(){

    Route::post('/login', ['uses'=>'AuthController@login']);

    Route::group(['middleware'=>['TokenCheck']], function(){
        Route::get('/getBusinesses', ['uses'=>'BusinessController@getBusinesses']);
        Route::get('/getBusiness', ['uses'=>'BusinessController@getBusiness']);
        Route::get('/getBusinessCategories', ['uses'=>'BusinessCategoryController@getBusinessCategories']);
        Route::get('/generateCode', ['uses'=>'PaymentsController@generateCode']);
        Route::get('/checkCode', ['uses'=>'PaymentsController@checkCode']);
    });

});