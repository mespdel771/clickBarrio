<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo que representa el inventario (stock) disponible de un producto en una tienda.
 *
 * @property int $id
 * @property int $tienda_id
 * @property int $producto_id
 * @property int $stock
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Stock extends Model
{
    /**
     * Atributos que se pueden asignar de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tienda_id',
        'producto_id',
        'stock',
    ];

    /**
     * Obtiene la tienda a la que pertenece este registro de stock.
     *
     * @return BelongsTo<Tienda, $this>
     */
    public function tienda(): BelongsTo
    {
        return $this->belongsTo(Tienda::class);
    }

    /**
     * Obtiene el producto al que pertenece este registro de stock.
     *
     * @return BelongsTo<Producto, $this>
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
