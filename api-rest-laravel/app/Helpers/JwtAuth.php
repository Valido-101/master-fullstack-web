<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth{
    
    public $key;
    
    public function __construct() {
        $this->key = 'esta_es_la_clave_super_secreta';
    }
    
    public function signup($email, $password, $getToken = null){
        
        //Buscar si existe el usuario por las credenciales
        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();
        
        //Comprobar si son correctas
        $signup = false;
        
        if(is_object($user)){
            $signup = true;
        }
        
        //Generar el token con los datos del usuario identificado
        if($signup){
            
            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'iat' => time(),//Momento en el que se ha expedido el token
                'exp' => time() + (7 * 24 * 60 * 60)//Tiempo que tardará el token en caducar
            );
            
            $jwt = JWT::encode($token, $this->key, 'HS256');//Token codificado
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);//Información sin codificar
            
            if(is_null($getToken)){
                $data = $jwt;
            }else{
                $data = $decoded;
            }
        }
        else{
            $data = array(
                'status' => 'error',
                'message' => 'Login incorrecto'
            );
        }
        
        
        
        //Devolver los datos decodificados o el token en función de un parámetro
        
        return $data;
    }
    
}
