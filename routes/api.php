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

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    
    
    
});

Route::group(['middleware' => 'jwt.auth', 'prefix' => 'v1'], function () {
    Route::post('create', 'EmpresaController@createUser');
    Route::group(['prefix' => '/empresa'], function () {
        Route::post('/crearUsuarioSW', 'EmpresaController@crearUsuarioSW');
        
    });
    
    Route::group(['prefix' => '/precp'], function () {
        Route::post('/{codigo}', 'PreCPController@show');
        Route::get('/', 'PreCPController@index');

    });
    Route::group(['prefix' => '/articulos'], function () {
        Route::post('/', 'ArticulosController@store');
        Route::get('/', 'ArticulosController@index');
        Route::get('/Concepto', 'ArticulosController@indexConcepto');
        Route::get('/{id}', 'ArticulosController@show');
        Route::get('/busqueda/{data}', 'ArticulosController@buscar');
        Route::get('/Concepto/{data}', 'ArticulosController@buscarConcepto');
        Route::put('/{id}', 'ArticulosController@edit');        
    });
    Route::group(['prefix' => '/clientes'], function () {
        Route::post('/', 'ClientesController@store');
        Route::get('/', 'ClientesController@index');
        Route::get('/{id}', 'ClientesController@show');
        Route::get('/busqueda/{data}', 'ClientesController@buscar');
        Route::put('/{id}', 'ClientesController@edit');        
    });

    Route::group(['prefix' => '/personal'], function () {
        Route::post('/', 'PersonalController@store');
        Route::get('/', 'PersonalController@index');
        Route::get('/{id}', 'PersonalController@show');
        Route::get('/busqueda/{data}', 'PersonalController@buscar');
        Route::put('/{id}', 'PersonalController@edit');        
    });

    Route::group(['prefix' => '/polizas'], function () {
        Route::get('/', 'AseguradorasController@indexPoliza');
        Route::post('/', 'AseguradorasController@poliza');
        Route::get('/{id}', 'AseguradorasController@showPoliza');
    });

    Route::group(['prefix' => '/aseguradoras'], function () {
        Route::post('/', 'AseguradorasController@store');
        Route::get('/', 'AseguradorasController@index');
        Route::get('/{id}', 'AseguradorasController@show');
        Route::get('/busqueda/{data}', 'AseguradorasController@buscar');
        Route::get('/buscar/a', 'AseguradorasController@buscaTodo');
        Route::put('/{id}', 'AseguradorasController@edit');        
    });
    Route::group(['prefix' => '/vehiculos'], function () {
        Route::post('/', 'VehiculosController@store');
        Route::get('/', 'VehiculosController@index');
        Route::get('/{id}', 'VehiculosController@show');
        Route::get('/busqueda/{data}', 'VehiculosController@buscar');
        Route::put('/{id}', 'VehiculosController@edit');        
    });
    Route::group(['prefix' => '/cp'], function () {
        Route::post('/', 'CPController@store');
        Route::get('/', 'CPController@index');
        Route::get('/idccp', 'CPController@generateIdCCP');
    });  
      
    Route::group(['prefix' => '/pagos'], function () {
        Route::post('/', 'PagosController@checkout');
        Route::get('/', 'CPController@index');
    }); 
    
});


Route::group(['middleware' => 'api', 'prefix' => 'v1'], function () {
    Route::group(['prefix' => '/empresa'], function () {
        Route::post('/', 'EmpresaController@store');
        Route::get('/obtenerUsuarios', 'EmpresaController@obtenerUsuariosSW');
        Route::get('/', 'EmpresaController@index');
        Route::get('/{id}', 'EmpresaController@show');
        
    });
    Route::group(['prefix' => '/empresa'], function () {
        Route::post('/asignarTimbres/{id}', 'EmpresaController@asignarTimbres');
        
    });
    Route::group(['prefix' => '/regimenfiscal'], function () {
        Route::get('/', 'RegimenFiscalController@index');
    });
    Route::group(['prefix' => '/pais'], function () {
        Route::get('/', 'PaisController@index');
    });
    Route::group(['prefix' => '/sat'], function () {
        Route::get('/cp/{cp}', 'SatController@cp');
        Route::get('/cp/pais/{cp}', 'SatController@cpPais');
        Route::get('/coloniasat/{cp}', 'SatController@coloniasSat');
        Route::get('/localidadsat/{cp}', 'SatController@localidadSat');
        Route::get('/claveunidadpeso/{search?}', 'SatController@unidadPeso');
        Route::get('/claveprodserv/{search?}', 'SatController@claveprodserv');
        Route::get('/claveunidad/{search?}', 'SatController@claveunidad');
        Route::get('/clavematpeligroso/{search?}', 'SatController@clavematpeligroso');
        Route::get('/tipoembalajesat/{search?}', 'SatController@tipoembalajesat');
        Route::get('/subtiporemolque', 'SatController@subtiporemolque');
        Route::get('/configautotransporte', 'SatController@configautotransporte');
        Route::get('/partetransporte', 'SatController@partetransporte');
        Route::get('/tipopermiso', 'SatController@tipopermiso');
        Route::get('/clavemoneda', 'SatController@clavemoneda');
        Route::get('/usocfdi', 'SatController@usocfdi');
    });
    Route::group(['prefix' => '/precp'], function () {
        Route::post('/', 'PreCPController@store');
        Route::post('/getXML/a', 'PreCPController@getXML');
    });
    Route::group(['prefix' => '/software'], function () {
        Route::post('/', 'SoftwareController@store');
    });


    
    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });
});
