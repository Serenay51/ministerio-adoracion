<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolCulto extends Model
{
    // app/Models/RolCulto.php
    protected $fillable = ['user_id', 'culto_id', 'rol', 'instrumento'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function culto()
    {
        return $this->belongsTo(Culto::class);
    }

}
