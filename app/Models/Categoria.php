<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo que representa una categoría para clasificar tiendas y productos.
 *
 * @property int $id
 * @property string $denominacion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Categoria extends Model
{
    /**
     * Atributos que se pueden asignar de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = ['denominacion'];

    /**
     * Obtiene las tiendas asociadas a esta categoría.
     *
     * @return HasMany<Tienda, $this>
     */
    public function tiendas(): HasMany
    {
        return $this->hasMany(Tienda::class);
    }

    /**
     * Obtiene los productos asociados a esta categoría.
     *
     * @return HasMany<Producto, $this>
     */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }
}
