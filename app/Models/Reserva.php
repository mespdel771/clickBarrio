<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo que representa una reserva realizada por un usuario.
 *
 * @property int $id
 * @property int $user_id
 * @property-read float $total Importe total calculado de la reserva.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Reserva extends Model
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
     * Obtiene el usuario que realizó esta reserva.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene los artículos (líneas de producto) incluidos en la reserva.
     *
     * @return HasMany<ReservaItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(ReservaItem::class);
    }

    /**
     * Calcula el total de la reserva al vuelo sumando (cantidad * precio) de sus líneas.
     *
     * @return float
     */
    public function getTotalAttribute(): float
    {
        return round($this->items->sum(function ($item) {
            return $item->cantidad * $item->precio;
        }), 2);
    }
}
