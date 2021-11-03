<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//Esta controlador se ha creado desde el cmd, en la ruta del proyecto api-rest-laravel, con el comando 
//php artisan make:controller <nombreControlador>

//Esta clase sirve para que se llame desde el web.php (router) y puede crear métodos que llamen a vistas
//con parámetros
class PruebasController extends Controller
{
    public function index() {
        $titulo = 'Animales';
        $animales = ['Perro', 'Gato', 'Tigre'];
        
        return view('pruebas.index', array(
            'titulo' => $titulo,
            'animales' => $animales
        ));
    }
}
