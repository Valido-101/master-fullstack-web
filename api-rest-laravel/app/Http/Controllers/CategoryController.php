<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller {
    
    public function __construct() {
        $this->middleware('api.auth', ['except' => ['index','show']]);
    }

    public function index() {
        //Devolvemos todas las categorías (Select * from categories)
        $categories = Category::all();

        return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'categories' => $categories
        ]);
    }

    public function show($id) {

        $category = Category::find($id);

        if (is_object($category)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'category' => $category
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'La  categoría no existe'
            );
        }

        return response()->json($data, $data['code']);
    }
    
    public function store(Request $request){
        //Recoger los datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        
        if (!empty($params_array)) {
            
            //Validar los datos
        $validate = \Validator::make($params_array, [
            'name' => 'required'
        ]);
        
        //Guardar la categoría
        if ($validate->fails()) {
            
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha guardado la categoría'
            ];
            
        }else{
            $category = new Category();
            $category->name = $params_array['name'];
            $category->save();
            
            $data = [
                'code' => 200,
                'status' => 'success',
                'category' => $category
            ];
        }
            
        }else{
            
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha enviado una categoría'
            ];
            
        }
        
        //Devolver resultado
        return response()->json($data, $data['code']);
    }

}
