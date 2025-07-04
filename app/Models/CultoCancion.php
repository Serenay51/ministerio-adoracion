<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CultoCancion extends Model
{
    // app/Models/CultoCancion.php

    public function culto()
    {
        return $this->belongsTo(Culto::class);
    }

    public function cancion()
    {
        return $this->belongsTo(Cancion::class);
    }


}
