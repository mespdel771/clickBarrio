<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo que representa una línea de producto (artículo) dentro de una reserva.
 *
 * @property int $id
 * @property int $reserva_id
 * @property int $tienda_id
 * @property int $producto_id
 * @property int $cantidad
 * @property float $precio
 * @property-read float $importe Importe total calculado de la línea (cantidad * precio).
 * @property-read float $importe_real Alias del importe total calculado de la línea.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ReservaItem extends Model
{
    /**
     * Atributos que se pueden asignar de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reserva_id',
        'tienda_id',
        'producto_id',
        'cantidad',
        'precio',
    ];

    /**
     * Obtiene la reserva a la que pertenece este artículo.
     *
     * @return BelongsTo<Reserva, $this>
     */
    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class);
    }

    /**
     * Obtiene la tienda asociada a este artículo de la reserva,
     * incluyendo aquellas tiendas que hayan sido eliminadas lógicamente (SoftDeletes).
     *
     * @return BelongsTo<Tienda, $this>
     */
    public function tienda(): BelongsTo
    {
        return $this->belongsTo(Tienda::class)->withTrashed();
    }

    /**
     * Obtiene el producto asociado a este registro.
     *
     * @return BelongsTo<Producto, $this>
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Calcula el importe de la línea al vuelo (cantidad * precio).
     *
     * @return float
     */
    public function getImporteAttribute(): float
    {
        return round($this->cantidad * $this->precio, 2);
    }

    /**
     * Alias del atributo importe por compatibilidad con las vistas.
     *
     * @return float
     */
    public function getImporteRealAttribute(): float
    {
        return $this->importe;
    }
}
