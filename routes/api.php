<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::get('Solicitudes/Empleado/{codEmpleado}', 'SolicitudFondosController@API_listarSOFDeEmpleado');
Route::get('Solicitudes/{codSolicitud}', 'SolicitudFondosController@API_getSOF');


Route::get('Rendiciones/Empleado/{codEmpleado}', 'RendicionGastosController@API_listarRENDeEmpleado');
Route::get('Rendiciones/{codRendicion}', 'RendicionGastosController@API_getREN');

Route::get('Reposiciones/Empleado/{codEmpleado}', 'ReposicionGastosController@API_listarREPDeEmpleado');
Route::get('Reposiciones/{codReposicion}', 'ReposicionGastosController@API_getREP');


Route::get('Requerimientos/Empleado/{codEmpleado}', 'RequerimientoBSController@API_listarREQDeEmpleado');
Route::get('Requerimientos/{codRequerimiento}', 'RequerimientoBSController@API_getREQ');

 
 //https://medium.com/@gilbertoparedes/rest-api-laravel-utilizando-passport-para-autenticaci%C3%B3n-con-aplicaciones-ionic-82e8f30e5862
 Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'Auth\AuthController@login')->name('login');
    Route::post('register', 'Auth\AuthController@register');
    
    Route::group(['middleware' => 'auth:api'], function() {
      Route::get('logout', 'Auth\AuthController@logout');
      Route::get('user', 'Auth\AuthController@user');
    });
});