<?php


namespace App\Models;

// Clases base y relaciones de Eloquent
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo que representa el carrito de compras de un usuario.
 *
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Carrito extends Model
{
    /**
     * Atributos que se pueden asignar de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
    ];

    /**
     * Obtiene el usuario propietario de este carrito.
     *
     * @return BelongsTo<User, $this>
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Obtiene los artículos (líneas de producto) contenidos en el carrito.
     *
     * @return HasMany<CarritoItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(CarritoItem::class);
    }
}
