<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Modelo que representa a un usuario registrado en el sistema.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Atributos que se pueden asignar de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Atributos que deben permanecer ocultos en la serialización (arrays/JSON).
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Obtiene los atributos que deben ser convertidos a tipos nativos.
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

    /**
     * Ejecuta acciones al inicializar el modelo, asignando el rol por defecto al crear un usuario.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::created(function (User $user) {
            $user->assignRole('usuario');
        });
    }

    /**
     * Obtiene las tiendas gestionadas por este usuario.
     *
     * @return HasMany<Tienda, $this>
     */
    public function tiendas(): HasMany
    {
        return $this->hasMany(Tienda::class, 'user_id');
    }

    /**
     * Obtiene el carrito de compras asignado al usuario.
     *
     * @return HasOne<Carrito, $this>
     */
    public function carrito(): HasOne
    {
        return $this->hasOne(Carrito::class);
    }

    /**
     * Comprueba si el usuario tiene asignado el rol de administrador.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('administrador');
    }
}
