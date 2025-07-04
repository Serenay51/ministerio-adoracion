<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Culto extends Model
{
    // app/Models/Culto.php
    protected $fillable = ['fecha', 'descripcion', 'created_by'];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function canciones()
    {
        return $this->belongsToMany(Cancion::class)
            ->withPivot('estructura', 'tonalidad', 'orden') // <-- asegurate que tonalidad esté acá
            ->withTimestamps()
            ->orderBy('pivot_orden');
    }

    public function rolCultos()
    {
        return $this->hasMany(RolCulto::class);
    }

}
