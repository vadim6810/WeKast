<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     *
     */
    public function comments()
    {
        return $this->hasMany('App\Model\Presentation');
    }
}
