<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//List Persons
Route::get('persons','PersonController@index');

//Show single person
Route::get('person/{id}','PersonController@show');

//create new person
Route::post('person','PersonController@store');

//search
Route::get('person/search/{fname?}/{lname?}',[
        'as' => 'search', 'uses' => 'PersonController@search'
    ]);


//filter by age
Route::get('person/agefilter/{maxAge}','PersonController@agefilter');

//update
Route::put('person','PersonController@store');

//delete person
Route::delete('person/{id}','PersonController@destroy');