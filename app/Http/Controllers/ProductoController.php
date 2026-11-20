<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Precio;
use App\Models\Producto;
use App\Models\Stock;
use App\Models\Tienda;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Controlador encargado de gestionar el catálogo de productos, su asignación a tiendas,
 * precios e inventarios (stock), aplicando control de acceso según el rol del usuario.
 */
class ProductoController extends Controller
{
    /**
     * Muestra el listado paginado de productos filtrado según el rol del usuario autentificado:
     * - Administrador: visualiza todos los productos del catálogo global.
     * - Gestor: visualiza los productos asignados a su tienda o tiendas y los productos disponibles de su categoría para incorporar.
     * - Usuario: visualiza únicamente los productos que disponen de stock activo.
     *
     * @return View
     */
    public function index(): View
    {
        // Obtiene el usuario autentificado.
        $user = auth()->user();
        $tiendas = collect();

              // Comprueba si el usuario tiene tiendas asignadas en la BD
        $userIdClean = intval($user->id);
        $tieneTiendasGestor = Tienda::where('user_id', $userIdClean)->exists();

        // Administrador
        if ($user->hasRole('administrador')) {
            $productos = Producto::with([
                'categoria',
                'stocks.tienda',
                'precios.tienda',
            ])
            ->orderBy('denominacion')
            ->paginate(10);

            $productosDisponibles = collect();
        }
            // Gestor (carga todas las tiendas asignadas al gestor)
     elseif ($tieneTiendasGestor || $user->hasRole('gestor')) {
         // Obtiene todas las tiendas activas asignadas a este gestor
         $tiendas = Tienda::where('user_id', $userIdClean)
             ->orderBy('nombre')
             ->get();

         $idsTiendasGestor = $tiendas->pluck('id')->toArray();

         // Búsqueda de los IDs de los productos que tienen precio en cualquiera de sus tiendas
         $idsProductosEnMisTiendas = !empty($idsTiendasGestor)
             ? DB::table('precios')
                 ->whereIn('tienda_id', $idsTiendasGestor)
                 ->pluck('producto_id')
                 ->unique()
                 ->toArray()
             : [];

         // Productos activos en sus tiendas
         if (!empty($idsProductosEnMisTiendas)) {
             $productos = Producto::whereIn('id', $idsProductosEnMisTiendas)
                 ->with([
                     'categoria',
                     'stocks' => function ($q) use ($idsTiendasGestor) {
                         $q->whereIn('tienda_id', $idsTiendasGestor)->with('tienda');
                     },
                     'precios' => function ($q) use ($idsTiendasGestor) {
                         $q->whereIn('tienda_id', $idsTiendasGestor)->with('tienda');
                     },
                 ])
                 ->orderBy('denominacion')
                 ->paginate(10);
         } else {
             // Si sus tiendas no tienen productos todavía, paginador vacío nativo
             $productos = new LengthAwarePaginator([], 0, 10);
         }

               // Productos disponibles de las categorías de sus tiendas para añadir
         $categoriasGestor = $tiendas->pluck('categoria_id')->unique()->filter()->toArray();
         $queryDisponibles = Producto::query();

         if (!empty($categoriasGestor)) {
             $queryDisponibles->whereIn('categoria_id', $categoriasGestor);
         }

         // Excluye los productos que ya están en todas las tiendas del gestor y evita duplicados por nombre
         $productosDisponibles = $queryDisponibles->with(['categoria'])
             ->orderBy('denominacion')
             ->get()
             ->filter(function ($prod) use ($idsTiendasGestor) {
                 $tiendasDelProducto = DB::table('precios')
                     ->where('producto_id', $prod->id)
                     ->whereIn('tienda_id', $idsTiendasGestor)
                     ->distinct('tienda_id')
                     ->count('tienda_id');

                 return $tiendasDelProducto < count($idsTiendasGestor);
             })
             ->unique(fn ($prod) => mb_strtolower(trim($prod->denominacion)))
             ->values();
     }
        // Usuario común
        else {
            // Obtiene solo productos que tengan existencias reales en tiendas activas (no eliminadas)
            $productos = Producto::whereHas('stocks', function ($q) {
                $q->where('stock', '>', 0)->whereHas('tienda');
            })
            ->with([
                'categoria',
                'stocks' => function ($q) {
                    $q->where('stock', '>', 0)->whereHas('tienda');
                },
                'stocks.tienda',
                'precios.tienda',
            ])
            ->orderBy('denominacion')
            ->paginate(10);

            $productosDisponibles = collect();
        }

        // Devuelve vista de productos.
        return view(
            'productos.index',
            compact(
                'productos',
                'productosDisponibles',
                'tiendas'
            )
        );

    }

    /**
     * Muestra el formulario para crear un nuevo producto verificando previamente la Policy
     * y filtrando las tiendas y categorías disponibles según el rol del usuario autentificado:
     * - Administrador: acceso total a todas las categorías y tiendas del sistema.
     * - Gestor: acceso únicamente a sus tiendas asignadas y sus correspondientes categorías.
     *
     * @return View
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException Si el usuario no tiene permiso para crear productos.
     */
    public function create(): View
    {
        // Verificación mediante la Policy si el usuario actual tiene permisos globales para crear productos.
        $this->authorize(
            'create',
            Producto::class
        );

        $user = auth()->user();

        // Rol administrador: Acceso total a todos los datos del sistema.
        if ($user->hasRole('administrador')) {
            $categorias = Categoria::orderBy('denominacion')->get();
            $tiendas = Tienda::orderBy('nombre')->get();
        }
        // Rol gestor: Acceso únicamente a su tienda.
        elseif ($user->hasRole('gestor')) {
            // Obtiene únicamente las tiendas asignadas al gestor autenticado.
            $tiendas = Tienda::where('user_id', $user->id)
                ->orderBy('nombre')
                ->get();

            $idsCategorias = $tiendas
                ->pluck('categoria_id')
                ->unique();

            $categorias = Categoria::whereIn('id', $idsCategorias)
                ->orderBy('denominacion')
                ->get();
        } else {
            abort(403);
        }

        // Devuelve vista de creación de producto.
        return view(
            'productos.create',
            compact(
                'categorias',
                'tiendas'
            )
        );
    }

    /**
     * Valida y almacena un nuevo producto en la base de datos junto con su imagen 
     * y sus relaciones de precio y stock en las tiendas seleccionadas.
     *
     * @param Request $request Solicitud HTTP con los datos del producto, imagen opcional y configuración por tienda.
     * @return RedirectResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException Si el usuario no tiene permiso para crear productos.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize(
            'create',
            Producto::class
        );

        //  Validación inicial de los campos básicos del producto.
        $request->validate([
            'denominacion' => 'required|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tiendas'      => 'nullable|array',
        ]);

        $user = auth()->user();
        $datosTiendas = $request->input('tiendas', []);
        $tiendasValidadas = [];

        //  Validación dinámica de los controles gráficos de las tiendas.
        if (!empty($datosTiendas)) {
            // Si el usuario es gestor, obtenemos solo sus tiendas asignadas por seguridad.
            $tiendasPermitidas = $user->hasRole('gestor')
                ? Tienda::where('user_id', $user->id)->pluck('id')->toArray()
                : [];

            foreach ($datosTiendas as $tiendaId => $valores) {
                // Si el checkbox 'activo' está marcado, validamos sus campos obligatoriamente.
                if (isset($valores['activo']) && $valores['activo'] == '1') {
                    // Seguridad: El gestor no puede inyectar IDs de tiendas ajenas.
                    if ($user->hasRole('gestor') && !in_array($tiendaId, $tiendasPermitidas)) {
                        abort(403, 'No tienes permisos sobre una de las tiendas seleccionadas.');
                    }

                    // Validamos manualmente cada input gráfico asociado a la tienda activa.
                    if (!isset($valores['precio']) || !is_numeric($valores['precio']) || $valores['precio'] < 0.01) {
                        return redirect()->back()->withInput()->withErrors([
                            'tiendas' => 'El precio para las tiendas seleccionadas debe ser un número mayor que 0.',
                        ]);
                    }

                    if (!isset($valores['stock']) || filter_var($valores['stock'], FILTER_VALIDATE_INT) === false || $valores['stock'] < 0) {
                        return redirect()->back()->withInput()->withErrors([
                            'tiendas' => 'El stock para las tiendas seleccionadas debe ser un número entero mayor o igual a 0.',
                        ]);
                    }

                    // Guardamos en el array limpio listo para la base de datos.
                    $tiendasValidadas[$tiendaId] = [
                        'precio' => $valores['precio'],
                        'stock'  => $valores['stock'],
                    ];
                }
            }
        }

        //  Subir imagen al disco público si el archivo existe.
        $rutaImagen = null;
        if ($request->hasFile('imagen')) {
            $rutaImagen = $request->file('imagen')->store('productos', 'public');
        }

        //  Creación del producto (Protección contra asignación masiva).
        $producto = Producto::create([
            'denominacion' => $request->input('denominacion'),
            'categoria_id' => $request->input('categoria_id'),
            'imagen'       => $rutaImagen,
        ]);

        //  Guardar relaciones con las tiendas validadas.
        // Pasamos al método los datos limpios y filtrados para evitar QueryExceptions.
        $this->guardarTiendas(
            $producto,
            ['tiendas' => $tiendasValidadas]
        );

        return redirect()
            ->route('productos.index')
            ->with(
                'success',
                'Producto creado correctamente'
            );
    }

    /**
     * Incorpora un producto existente del catálogo global a todas las tiendas del gestor autentificado,
     * inicializando su stock en 0 y su precio base en 0.01 antes de redirigir a su configuración.
     *
     * @param Producto $producto Instancia del producto que el gestor desea añadir a su catálogo.
     * @return RedirectResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException Si el usuario no tiene permiso para añadir el producto al catálogo.
     */
    public function addToCatalog(Producto $producto): RedirectResponse
    {
        // Autentificación de usuario.
        $user = auth()->user();

        $this->authorize('addToCatalog', $producto);

        // Obtiene todas las tiendas que maneja este gestor.
        $tiendas = Tienda::where('user_id', $user->id)->get();

        foreach ($tiendas as $tienda) {
            // Crea o actualiza el stock inicial en 0.
            Stock::updateOrCreate(
                [
                    'producto_id' => $producto->id,
                    'tienda_id'   => $tienda->id,
                ],
                [
                    'stock' => 0,
                ]
            );

            // Inicializa el precio en 0.01 para cumplir con la restricción de precios mayores que 0.
            Precio::updateOrCreate(
                [
                    'producto_id' => $producto->id,
                    'tienda_id'   => $tienda->id,
                ],
                [
                    'precio' => 0.01,
                ]
            );
        }

        return redirect()
            ->route('productos.edit', $producto)
            ->with('success', 'Producto añadido a tu catálogo. Ahora puedes configurar su precio y stock reales.');
    }

    /**
     * Muestra el formulario de edición de un producto filtrando categorías y tiendas
     * (asignadas y disponibles) según el rol del usuario autentificado.
     *
     * @param Producto $producto Instancia del producto a editar.
     * @return View
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException Si el usuario no tiene permiso para editar el producto.
     */
    public function edit(Producto $producto): View
    {
        $this->authorize('update', $producto);

        // Autentificación de usuario.
        $user = auth()->user();

        // Tiendas según Rol.
        if ($user->hasRole('administrador')) {
            $tiendasBase = Tienda::orderBy('nombre')->get();
            $categorias = Categoria::orderBy('denominacion')->get();
        } elseif ($user->hasRole('gestor')) {
            $tiendasBase = Tienda::where('user_id', $user->id)
                ->orderBy('nombre')
                ->get();

            if ($tiendasBase->isEmpty()) {
                abort(403);
            }

            $categorias = Categoria::whereIn(
                'id',
                $tiendasBase->pluck('categoria_id')
            )
            ->orderBy('denominacion')
            ->get();
        } else {
            abort(403);
        }

             // Cargar relaciones del producto.
        $producto->load(['precios', 'stocks', 'categoria']);

        // Mapea precios y stocks indexados por tienda_id entero para precargar el formulario sin fallos
        $preciosPorTienda = $producto->precios->keyBy(fn ($item) => (int) $item->tienda_id);
        $stocksPorTienda  = $producto->stocks->keyBy(fn ($item) => (int) $item->tienda_id);

        // Tiendas ya asignadas al producto.
        $idsAsignadas = $stocksPorTienda->keys()
            ->merge($preciosPorTienda->keys())
            ->unique()
            ->values()
            ->toArray();

        $tiendasAsignadas = $tiendasBase->whereIn('id', $idsAsignadas);
        $tiendasDisponibles = $tiendasBase
            ->where('categoria_id', $producto->categoria_id)
            ->whereNotIn('id', $idsAsignadas)
            ->values();

        return view('productos.edit', [
            'producto'           => $producto,
            'categorias'         => $categorias,
            'tiendasAsignadas'   => $tiendasAsignadas,
            'tiendasDisponibles' => $tiendasDisponibles,
            'preciosPorTienda'   => $preciosPorTienda,
            'stocksPorTienda'    => $stocksPorTienda,
        ]);
    }
    /**
     * Valida y actualiza los datos de un producto existente, su imagen en almacenamiento
     * y sus relaciones de precio y stock en las tiendas permitidas para el usuario.
     *
     * @param Request $request Solicitud HTTP con los nuevos datos del producto, imagen opcional y configuración por tienda.
     * @param Producto $producto Instancia del producto a actualizar.
     * @return RedirectResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException Si el usuario no tiene permiso para actualizar el producto.
     */
          public function update(Request $request, Producto $producto): RedirectResponse
    {
        $this->authorize(
            'update',
            $producto
        );

        // Validación de datos.
        $datos = $request->validate([
            'denominacion'     => 'required|string|max:255',
            'categoria_id'     => 'required|exists:categorias,id',
            'imagen'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tiendas'          => 'nullable|array',
            'tiendas.*.precio' => 'nullable|numeric|min:0',
            'tiendas.*.stock'  => 'nullable|integer|min:0',
        ]);

        // Autentificación de usuario.
        $user = auth()->user();

        // Validación de permisos y obligatoriedad de precio y stock para el gestor.
        if ($user->hasRole('gestor')) {
            $tiendasPermitidas = Tienda::where('user_id', $user->id)
                ->pluck('id')
                ->toArray();

            $tiendasFormulario = $request->input('tiendas', []);
            $algunaTiendaConfigurada = false;

            foreach ($tiendasFormulario as $tiendaId => $valores) {
                if (!in_array($tiendaId, $tiendasPermitidas)) {
                    abort(403);
                }

                $estaActiva = (isset($valores['activo']) && $valores['activo'] == '1')
                    || (!array_key_exists('activo', $valores) && (isset($valores['precio']) || isset($valores['stock'])));

                if ($estaActiva) {
                    $precio = $valores['precio'] ?? null;
                    $stock  = $valores['stock'] ?? null;

                    if ($precio === null || $precio === '' || (float) $precio <= 0) {
                        return back()
                            ->withInput()
                            ->withErrors(["tiendas.{$tiendaId}.precio" => 'Debes indicar un precio obligatorio mayor que 0.']);
                    }

                    if ($stock === null || $stock === '' || (int) $stock < 1) {
                        return back()
                            ->withInput()
                            ->withErrors(["tiendas.{$tiendaId}.stock" => 'Debes indicar un stock obligatorio de al menos 1 unidad.']);
                    }

                    $algunaTiendaConfigurada = true;
                }
            }

            // Impide guardar si el gestor no ha activado y configurado precio y stock en ninguna de sus tiendas
            if (!$algunaTiendaConfigurada) {
                return back()
                    ->withInput()
                    ->withErrors(['tiendas' => 'Debes activar al menos una tienda e indicar obligatoriamente su precio y stock.']);
            }
        }

        // Actualizar imagen.
        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }

            $datos['imagen'] = $request
                ->file('imagen')
                ->store('productos', 'public');
        }

        // Actualizar producto.
        $producto->update([
            'denominacion' => $datos['denominacion'],
            'categoria_id' => $datos['categoria_id'],
            'imagen'       => $datos['imagen'] ?? $producto->imagen,
        ]);

        $this->guardarTiendas(
            $producto,
            $datos
        );

        return redirect()
            ->route('productos.index')
            ->with(
                'success',
                'Producto actualizado correctamente'
            );
    }


    /**
     * Elimina un producto junto con su imagen almacenada en disco y sus registros asociados
     * de precios y stock, previa autorización mediante Policy.
     *
     * @param Producto $producto Instancia del producto a eliminar.
     * @return RedirectResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException Si el usuario no tiene permiso para eliminar el producto.
     */    /**
     * Elimina un producto si no tiene reservas históricas asociadas, o muestra aviso 
     * respetando la restricción ON DELETE RESTRICT de la base de datos.
     *
     * @param Producto $producto Instancia del producto a eliminar.
     * @return RedirectResponse
     */
    public function destroy(Producto $producto): RedirectResponse
    {
        $this->authorize(
            'delete',
            $producto
        );

        // Impide eliminar el producto si ya figura en alguna reserva histórica
        if (\App\Models\ReservaItem::where('producto_id', $producto->id)->exists()) {
            return redirect()
                ->route('productos.index')
                ->with('error', 'No se puede eliminar este producto porque tiene compras o reservas registradas.');
        }

        // Elimina la imagen almacenada en disco si existe
        if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
            Storage::disk('public')->delete($producto->imagen);
        }

        // Elimina precios y stocks sin ventas asociadas
        $producto->precios()->delete();
        $producto->stocks()->delete();

        // Elimina el producto de la base de datos
        $producto->delete();

        return redirect()
            ->route('productos.index')
            ->with(
                'success',
                'Producto eliminado correctamente'
            );
    }

        /**
     * Retira un producto de todas las tiendas del gestor autenticado:
     * - Si el producto tiene ventas históricas en esa tienda, deja su stock en 0 (respetando ON DELETE RESTRICT).
     * - Si nunca se ha vendido en esa tienda, elimina sus registros de precios y stocks.
     *
     * @param Producto $producto Instancia del producto a retirar de las tiendas del gestor.
     * @return RedirectResponse
     */
    public function removeFromCatalog(Producto $producto): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->hasRole('gestor')) {
            abort(403);
        }

        // Obtiene los IDs de todas las tiendas activas pertenecientes a este gestor
        $idsTiendasGestor = Tienda::where('user_id', $user->id)
            ->pluck('id')
            ->toArray();

        $teniaVentas = false;

        foreach ($idsTiendasGestor as $tiendaId) {
            $tieneReservas = \App\Models\ReservaItem::where('producto_id', $producto->id)
                ->where('tienda_id', $tiendaId)
                ->exists();

            if ($tieneReservas) {
                // Mantiene el precio histórico y pone el stock a 0 para ocultarlo de la venta
                Stock::where('producto_id', $producto->id)
                    ->where('tienda_id', $tiendaId)
                    ->update(['stock' => 0]);

                $teniaVentas = true;
            } else {
                // Elimina limpiamente precio y stock en esa tienda
                Precio::where('producto_id', $producto->id)
                    ->where('tienda_id', $tiendaId)
                    ->delete();

                Stock::where('producto_id', $producto->id)
                    ->where('tienda_id', $tiendaId)
                    ->delete();
            }
        }

        $mensaje = $teniaVentas
            ? 'El producto tenía ventas históricas en tus tiendas, por lo que su stock se ha dejado a 0 para conservar el historial.'
            : 'Producto retirado correctamente de tus tiendas.';

        return redirect()
            ->route('productos.index')
            ->with('success', $mensaje);
    }


    /**
     * Sincroniza los precios y el stock de un producto para las tiendas activas en el formulario,
     * protegiendo las filas de 'precios' que ya tengan ventas históricas en 'reserva_items'.
     *
     * @param Producto $producto Instancia del producto al que se le asignan los datos.
     * @param array<string, mixed> $datos Arreglo validado que contiene la clave 'tiendas' con precios y stocks.
     * @return void
     */
    private function guardarTiendas(Producto $producto, array $datos): void
    {
        $user = auth()->user();
        $tiendasFormulario = request()->input('tiendas', []);
        $tiendasActivasIds = [];

        //  Procesa únicamente las tiendas que el usuario ha dejado marcadas como activas
        foreach ($tiendasFormulario as $tiendaId => $data) {
            $estaActiva = isset($data['activo']) && $data['activo'] == '1';

            // En create() los datos ya vienen filtrados sin clave 'activo', así que también los aceptamos
            if ($estaActiva || (!array_key_exists('activo', $data) && (isset($data['precio']) || isset($data['stock'])))) {
                $tiendasActivasIds[] = (int) $tiendaId;

                Precio::updateOrCreate(
                    [
                        'producto_id' => $producto->id,
                        'tienda_id'   => $tiendaId,
                    ],
                    [
                        'precio' => $data['precio'] ?? 0,
                    ]
                );

                Stock::updateOrCreate(
                    [
                        'producto_id' => $producto->id,
                        'tienda_id'   => $tiendaId,
                    ],
                    [
                        'stock' => $data['stock'] ?? 0,
                    ]
                );
            }
        }

        //  Determina qué tiendas puede gestionar este usuario para no afectar a tiendas ajenas ni eliminadas (SoftDeletes)
        $tiendasGestionablesIds = $user->hasRole('gestor')
            ? Tienda::where('user_id', $user->id)->pluck('id')->toArray()
            : Tienda::pluck('id')->toArray();

        $tiendasADesactivar = array_diff($tiendasGestionablesIds, $tiendasActivasIds);

        //  Para cada tienda desmarcada, elimina su oferta si no tiene ventas o pone el stock a 0 si tiene reservas
        foreach ($tiendasADesactivar as $tiendaId) {
            $tieneReservas = \App\Models\ReservaItem::where('producto_id', $producto->id)
                ->where('tienda_id', $tiendaId)
                ->exists();

            if ($tieneReservas) {
                // Mantiene la fila en 'precios' para cumplir ON DELETE RESTRICT y deja el stock en 0
                Stock::where('producto_id', $producto->id)
                    ->where('tienda_id', $tiendaId)
                    ->update(['stock' => 0]);
            } else {
                // Si nunca se ha vendido en esa tienda, elimina limpiamente su precio y stock
                Precio::where('producto_id', $producto->id)
                    ->where('tienda_id', $tiendaId)
                    ->delete();

                Stock::where('producto_id', $producto->id)
                    ->where('tienda_id', $tiendaId)
                    ->delete();
            }
        }
    }
}
