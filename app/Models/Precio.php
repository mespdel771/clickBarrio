<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo que representa el precio de un producto en una tienda específica.
 *
 * @property int $id
 * @property int $producto_id
 * @property int $tienda_id
 * @property float $precio
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Precio extends Model
{
    /**
     * Atributos que se pueden asignar de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'producto_id',
        'tienda_id',
        'precio',
    ];

    /**
     * Obtiene el producto al que pertenece este precio.
     *
     * @return BelongsTo<Producto, $this>
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Obtiene la tienda a la que pertenece este precio.
     *
     * @return BelongsTo<Tienda, $this>
     */
    public function tienda(): BelongsTo
    {
        return $this->belongsTo(Tienda::class);
    }
}
