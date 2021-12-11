<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;

class PostController extends Controller
{
    public function __construct() {
        //Pide autenticación para todos los métodos excepto index() y show()
        $this->middleware('api.auth', ['except' => ['index','show']]);
    }
    
    /**
     *  Método que devuelve todos los posts
     * 
     *  @return json con los datos de la respuesta
     */
    public function index(){
        
        //Usamos el orm para obtener los posts de la bbdd
        $posts = Post::all()->load('category');//Con load() especificamos que devuelva también la información de la categoría relacionada con este post
        
        //Devolvemos la respuesta en un json
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'posts' => $posts
        ], 200);
        
    }
    
    /**
     *  Método que devuelve un post concreto
     * 
     *  @param int $id id del post a devolver
     * 
     *  @return json con los datos de la respuesta
     */
    public function show($id){
        
        //Buscamos el post con el id especificado
        $post = Post::find($id);
        
        //Si encuentra un post...
        if (is_object($post)) {
            
            //Volvemos a recuperarlo con la información de la categoría a la que pertenece
            $post = Post::find($id)->load('category');//si no lo hacemos así y no encuentra un post, falla
            
            $data = [
            'code' => 200,
            'status' => 'success',
            'posts' => $post
            ];
            
        }else{
            
            $data = [
            'code' => 404,
            'status' => 'error',
            'message' => 'La entrada no existe'
            ];
            
        }
        
        return response()->json($data, $data['code']);
        
    }
}
