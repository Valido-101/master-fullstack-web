<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller {

    public function pruebas(Request $request) {
        return "Acción de pruebas de USER-CONTROLLER";
    }

    public function register(Request $request) {

        //Recoger los datos del usuario por post

        $json = $request->input('json', null);

        $params = json_decode($json); //Lo almacena en un objeto, no usaremos esta variable, es de prueba

        $params_array = json_decode($json, true); //Lo almacena en un array

        //Si params_array viene nulo es que no se ha hecho bien la decodificación
        if (!empty($params) && !empty($params_array)) {

            //Limpiar datos
            $params_array = array_map('trim', $params_array);

            //Validar los datos
            //Los tipos de validaciones se pueden ver en la api de laravel

            $validate = \Validator::make($params_array, [
                        'name' => 'required|alpha',//Este campo es obligatorio y tiene que ser alfanumérico
                        'surname' => 'required|alpha',
                        'email' => 'required|email|unique:users',//Obligatorio, comprobación especial de email y además se asegura de que este campo se único
                        'password' => 'required'
            ]);

            if ($validate->fails()) {

                //La validación ha fallado
                
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'mensaje' => 'El usuario no se ha creado',
                    'errors' => $validate->errors()
                );

                return response()->json($data, 400);
            } else {
                
                //Cifrar la contraseña
                //               algoritmo de cifrado, elemento a cifrar
                $password = hash('sha256', $params_array['password']);                
                
                //Crear el usuario
                
                $user = new User();
                
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $password;
                $user->role = 'ROLE_USER';
                
                //Guardar usuario
                
                $user->save();
                
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'mensaje' => 'El usuario se ha creado correctamente',
                    'user' => $user
                );
            }
        }
        else {
            $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'mensaje' => 'Los datos introducidos no son correctos'
                );
        }

        //Devuelve un json con información de error
        return response()->json($data, $data['code']);
    }

    public function login(Request $request) {
        
        //Tiramos del Json Web Token que hemos creado
        $jwtAuth = new \JwtAuth();
        
        //Recibir datos por POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        
        //Validar datos
        $validate = \Validator::make($params_array, [
            'email' => 'required|email',//Obligatorio, comprobación especial de email
            'password' => 'required'
        ]);
        
        if ($validate->fails()) {

            //La validación ha fallado

            $signup = array(
                'status' => 'error',
                'code' => 404,
                'mensaje' => 'Login incorrecto',
                'errors' => $validate->errors()
            );
        }else{
            //Cifrar password
            $pwd = hash('sha256', $params->password);            
            
            //Devolver datos con token si el parámetro gettoken no está vacío
            if (!empty($params->gettoken)) {
                $signup = $jwtAuth->signup($params->email, $pwd, true);
            }else{//Devolver datos sin token porque el parámetro gettoken está vacío
                $signup = $jwtAuth->signup($params->email, $pwd);
            }
        }
        
        //No podemos devolver un objeto, tiene que ser una respuesta en json
        return response()->json($signup, 200);
    }
    
    public function update(Request $request){
        
        $token = $request->header('Authorization');
        
        $jwtAuth = new \JwtAuth();
        
        $checkToken = $jwtAuth->checkToken($token);
        
        if ($checkToken) {
            echo "<h1>Login correcto</h1>";
        }else{
            echo "<h1>Login INCORRECTO</h1>";
        }
        
        die();
    }

}
