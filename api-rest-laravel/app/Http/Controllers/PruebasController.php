<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//Implementación del modelo Post
use App\Post;
//Implementación del modelo Category
use App\Category;

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
    
    public function testOrm()
    {
        /*
        //Obtenemos todos los posts de la bbdd (llamamos al modelo y este al método all, que funciona como
        // un select *)
        $posts = Post::all();
        
        //Recorremos cada post obtenido en un foreach
        foreach($posts as $post)
        {
            //Mostramos por pantalla El título, el nombre del usuario y el de la categoría y el contenido
            //De esta forma se concatena el valor de la propiedad de la variable con un texto plano
            echo "<h1>".$post->title."</h1>";
            //De esta forma se puede meter el valor directamente sin concatenar, usando {}
            echo "<span style='color:gray;'>{$post->user->name} - {$post->category->name}</span>";
            echo "<p>".$post->content."</p>";
            echo "<hr>";
        }
        * 
        */
        
        //Aquí hacemos lo mismo que arriba sólo que también accedemos a los posts de cada categoría,
        //esto es posible gracias al modelo ORM y la función posts del modelo de Category
        $categories = Category::all();
        
        foreach($categories as $category)
        {
            echo "<h1>{$category->name}</h1>";
            
            foreach($category->posts as $post)
            {
                echo "<h3>".$post->title."</h3>";
                echo "<span style='color:gray;'>{$post->user->name} - {$post->category->name}</span>";
                echo "<p>".$post->content."</p>";
            }
            
            echo "<hr>";
        }
        
        die();
    }
}
