<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo que representa un producto del catálogo.
 *
 * @property int $id
 * @property int $categoria_id
 * @property string $denominacion
 * @property string|null $imagen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Producto extends Model
{
    /**
     * Atributos que se pueden asignar de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'categoria_id',
        'denominacion',
        'imagen',
    ];

    /**
     * Obtiene la categoría a la que pertenece el producto.
     *
     * @return BelongsTo<Categoria, $this>
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Obtiene los distintos precios asociados a este producto.
     *
     * @return HasMany<Precio, $this>
     */
    public function precios(): HasMany
    {
        return $this->hasMany(Precio::class);
    }

    /**
     * Obtiene los registros de inventario (stock) del producto por tienda.
     *
     * @return HasMany<Stock, $this>
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Obtiene las tiendas en las que está disponible el producto, incluyendo su precio en la tabla pivote.
     *
     * @return BelongsToMany<Tienda, $this>
     */
    public function tiendas(): BelongsToMany
    {
        // Incluye el campo 'precio' de la tabla intermedia 'precios'
        return $this->belongsToMany(
            Tienda::class,
            'precios',
            'producto_id',
            'tienda_id'
        )->withPivot('precio');
    }
}
