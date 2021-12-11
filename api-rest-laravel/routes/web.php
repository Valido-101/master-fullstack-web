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

//Cargando clases
use App\Http\Middleware\ApiAuthMiddleware;

Route::get('/', function () {
    return view('welcome');
});

/*
//Nueva ruta en la web que recibe un parámetro en la url
Route::get('/page-2/{nombre?}', function ($nombre = null){
    
    //Instanciamos e inicializamos una variable
    $texto = '<h2>Texto desde una ruta: </h2>';
    //Le concatenamos el parámetro
    $texto .= 'Nombre: '.$nombre;
    
    //Creamos una variable con una estructura parecida a un Map para pasársela como parámtero a la vista page2.blade.php
    $data = array( 'texto' => $texto );
    
    return view('page2', $data);
});

//Nueva ruta en la que usamos el controlador que creamos y llamamos específicamente al método index
Route::get('/pruebas/animales', 'PruebasController@index');
Route::get('/testOrm', 'PruebasController@testOrm');
Route::get('/testOrm', 'PruebasController@testOrm');*/

//RUTAS DE LA API

//Una API se encarga de tener controladores que devuelven una información o realizan una acción cuando se
//acceden a ciertas rutas

    //RUTAS DE PRUEBA
    /*Route::get('/usuario/pruebas', 'UserController@pruebas');
    Route::get('/categoria/pruebas', 'CategoryController@pruebas');
    Route::get('/entrada/pruebas', 'PostController@pruebas');*/
    
    /*Métodos HTTP comunes:
     * 
     *  GET: Conseguir datos o recursos
     *  POST: Guardar datos o recursos o hacer lógica desde un formulario
     *  PUT: Actualizar recursos o datos
     *  DELETE: Eliminar datos o recursos
     * 
     *  Una API REST usa sólo los métodos GET y POST, una API RESTFUL usa los cuatro métodos
     * 
     */
    
    //Rutas del controlador de usuario
    Route::post('/api/register', 'UserController@register');
    Route::post('/api/login', 'UserController@login');
    Route::put('/api/user/update', 'UserController@update');
    Route::post('/api/user/upload', 'UserController@upload')->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
    Route::get('/api/user/avatar/{filename}', 'UserController@getImage');
    Route::get('/api/user/detail/{id}', 'UserController@detail');
    
    //Rutas del controlador de categorías
    //Este tipo de ruta te crea toda una serie de rutas nuevas basadas en el controlador
    //Para ver todas las rutas de la api ejecutar el comando php artisan route:list en el directorio del proyecto
    Route::resource('api/category', 'CategoryController');
    
    //Rutas del controlador de entradas
    Route::resource('api/post', 'PostController');
    
    //Ruta para subir imagen de post
    Route::post('api/post/upload', 'PostController@upload');
    Route::get('api/image/{filename}', 'PostController@getImage');