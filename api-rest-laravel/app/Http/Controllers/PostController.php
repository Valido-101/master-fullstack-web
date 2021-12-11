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
        $this->middleware('api.auth', ['except' => ['index','show', 'getImage']]);
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
            $user = $this->getIdentity($request);
            
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
                
                //Conseguir usuario identificado
                $user = $this->getIdentity($request);

                //Conseguir el registro
                $post = Post::where('id', $id)
                        ->where('user_id', $user->id)
                        ->first();

                if (!empty($post) && is_object($post)) {
                    
                    //Buscar el registro
                    $post->updateorCreate($params_array);
                    
                    $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post,
                    'changes' => $params_array
                    ];
                    
                }

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
    
    public function destroy($id, Request $request) {
        
        //Conseguir usuario identificado
        $user = $this->getIdentity($request);
               
        //Conseguir el registro
        $post = Post::where('id', $id)
                ->where('user_id', $user->id)
                ->first();
        
        if (!empty($post)) {
            //Borrarlo
            $post->delete();

            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post
            ];
            
        }else{
            
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'El post no existe'
            ];
            
        }
        
        return response()->json($data, $data['code']);
        
    }
    
    public function upload(Request $request){
        
        //Recoger imagen de la petición
        $image = $request->file('file0');
        
        //Validar imagen
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);
        
        //Guardar imagen
        if (!$image || $validate->fails()) {
            
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir la imagen'
            ];
            
        }else{
            
            $image_name = time().$image->getClientOriginalName();
            
            \Storage::disk('images')->put($image_name, \File::get($image));
            
            $data = [
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            ];
            
        }
        
        //Devolver respuesta
        return response()->json($data, $data['code']);
        
    }
    
    public function getImage($fileName){
        
        //Comprobar si existe el fichero
        $isset = \Storage::disk('images')->exists($fileName);
        
        if ($isset) {
            
            //Conseguir la imagen
            $file = \Storage::disk('images')->get($fileName);
            
            //Devolver la imagen
            return new Response($file, 200);
        }else{
            
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La imagen no existe'
            ];
            
        }
        
        return response()->json($data, $data['code']);
        
        
    }
    
    private function getIdentity(Request $request){
        
        //Conseguir usuario identificado
        $jwtAuth = new JwtAuth();
        $token = $request -> header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);
        
        return $user;
        
    }
}
