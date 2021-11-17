<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //Tabla a la que pertenece este modelo
    protected $table = 'posts';
    
    //Relación 1:N
    //Devuelve el usuario al que pertenece este post
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    
    //Relación 1:N
    //Devuelve la categoría a la que pertenece este post
    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id');
    }
}
