<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo que representa una tienda o comercio registrado en la plataforma.
 * Implementa borrado lógico (SoftDeletes) para ocultar tiendas eliminadas sin romper reservas históricas.
 *
 * @property int $id
 * @property int $categoria_id
 * @property int $user_id
 * @property string $nombre
 * @property string|null $persona_contacto
 * @property string|null $telefono
 * @property string|null $email
 * @property string|null $direccion
 * @property string|null $imagen
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Tienda extends Model
{
    // Activa el borrado lógico: oculta automáticamente las tiendas eliminadas en el catálogo y listados
    use SoftDeletes;

    /**
     * Nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'tiendas';

    /**
     * Atributos que se pueden asignar de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'categoria_id',
        'user_id',
        'nombre',
        'persona_contacto',
        'telefono',
        'email',
        'direccion',
        'imagen',
    ];

    /**
     * Obtiene el usuario gestor propietario de la tienda.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Obtiene la categoría a la que pertenece la tienda.
     *
     * @return BelongsTo<Categoria, $this>
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Obtiene los productos disponibles en la tienda, incluyendo su precio en la tabla pivote.
     *
     * @return BelongsToMany<Producto, $this>
     */
    public function productos(): BelongsToMany
    {
        // Incluye el precio guardado en la tabla intermedia 'precios'
        return $this->belongsToMany(
            Producto::class,
            'precios',
            'tienda_id',
            'producto_id'
        )->withPivot('precio');
    }

    /**
     * Obtiene los registros de precios definidos para esta tienda.
     *
     * @return HasMany<Precio, $this>
     */
    public function precios(): HasMany
    {
        return $this->hasMany(Precio::class);
    }

    /**
     * Obtiene los registros de inventario (stock) de esta tienda.
     *
     * @return HasMany<Stock, $this>
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }
}
