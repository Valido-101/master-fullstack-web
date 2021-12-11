<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Helpers\JwtAuth;

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
    
    public function store(Request $request){
        
        //Recoger datos por POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        
        if (!empty($params_array)) {
            
            //Conseguir usuario identificado
            $jwtAuth = new JwtAuth();
            $token = $request -> header('Authorization', null);
            $user = $jwtAuth->checkToken($token, true);
            
            //Validar datos
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',
                'image' => 'required'
            ]);
            
            if ($validate->fails()) {
                
                $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'Faltan datos'
                ];
                
            }else{
                
                //Guardar entrada
                $post = new Post();
                
                $post->user_id = $user->id;
                $post->category_id = $params_array['category_id'];//En el curso se saca de $params
                $post->title = $params_array['title'];
                $post->content = $params_array['content'];
                $post->image = $params_array['image'];
                
                $post->save();
                
                $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post
                ];
                
            }
            
        }else{
            
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'Datos no enviados'
                ];
            
        }
        
        //Devolver respuesta
        return response()->json($data, $data['code']);
        
    }
    
    public function update($id, Request $request){
        
        //Recoger datos por POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        
        if (!empty($params_array)) {
            
            //Validar datos
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required'
            ]);

            if ($validate->fails()) {

                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Faltan datos'
                ];

            }else{

                //Eliminar lo que no queremos actualizar
                unset($params_array['id']);
                unset($params_array['user_id']);
                unset($params_array['created_at']);
                unset($params_array['user']);

                //Actualizar el registro en concreto
                $post = Post::where('id', $id)->updateOrCreate($params_array);//Nos devuelve el post con la información completa tras actualizarse

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post,
                    'changes' => $params_array
                ];

            }
            
        }
        else{
            
            $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Datos enviados incorrectamente'
                ];
            
        }
        
        return response()->json($data, $data['code']);
        
    }
}
