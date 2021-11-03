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

Route::get('/', function () {
    return view('welcome');
});

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
