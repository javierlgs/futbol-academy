<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'sede_id', 'telefono', 
        'cedula', 'foto', 'activo', 'ultimo_acceso'
    ];

    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

    public function sede() {
        return $this->belongsTo(Sede::class);
    }
    public function asignaciones(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(AsignacionEntrenador::class, 'user_id');
}
}