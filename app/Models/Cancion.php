<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cancion extends Model
{
    //
    // app/Models/Cancion.php
        use HasFactory;

    protected $fillable = [
        'titulo',
        'autor',
        'letra',
        'created_by',
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cultos()
    {
        return $this->belongsToMany(Culto::class)->withPivot('estructura', 'tonalidad')->withTimestamps();
    }



}
