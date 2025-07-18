<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // app/Models/User.php

    public function cultosCreados()
    {
        return $this->hasMany(Culto::class, 'created_by');
    }

    public function asignaciones()
    {
        return $this->hasMany(RolCulto::class);
    }

    public function cultosAsignados()
    {
        return $this->belongsToMany(Culto::class, 'rol_cultos')
                    ->withPivot('rol', 'instrumento')
                    ->withTimestamps();
    }

    public function tieneRolEnCulto($cultoId, $rol)
    {
        return $this->asignaciones()
            ->where('culto_id', $cultoId)
            ->where('rol', $rol)
            ->exists();
    }

}
