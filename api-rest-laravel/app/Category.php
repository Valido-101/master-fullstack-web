<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //Tabla a la que pertenece este modelo
    protected $table = 'categories';
    
    //Relación 1:N
    //Devuelve todos los posts que se encuentran en esta categoría
    public function posts()
    {
        return $this->hasMany('App\Post');
    }
}
