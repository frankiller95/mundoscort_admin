<?php


namespace App\Http\Traits\Anuncios;

use App\Models\AnuncioCategoria;
use App\Models\AnuncioFormaPago;
use App\Models\Anuncios;
use App\Models\Categorias;
use App\Models\FormasPago;
use App\Models\Nacionalidades;
use App\Models\Provincias;
use App\Models\RelImagenesAnuncios;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

trait traitAnuncios
{
    public function index()
    {
        $provincias = Provincias::where('estado', 1)->select('id AS id_provincia', 'provincia')->get();
        $nacionalidades = Nacionalidades::where('estado', 1)->select('id AS id_nacionalidad', 'nacionalidad')->get();
        $formas_pagos = FormasPago::where('estado', 1)->select('id AS id_forma_pago', 'forma_pago')->get();
        $categorias = Categorias::where('estado', 1)->select('id AS id_categoria', 'categoria AS categoria_nombre')->get();
        return view('anuncios.index', ['provincias' => $provincias, 'nacionalidades' => $nacionalidades, 'formas_pagos' => $formas_pagos, 'categorias' => $categorias]);
    }

    /* public function createAnuncio(Request $request)
    {
        if ($request->file('file')) {
            $nombre_img = pathinfo($request->file('file')->getClientOriginalName())['filename'];
            $img = $request->file('file');
            $nombreimagen = Str::slug($nombre_img) . "." . $img->getClientOriginalExtension();
            $img->move(public_path('/img/anuncios/'), $nombreimagen);
            $ruta =  '/img/anuncios/' . $nombreimagen;
        } else {
            $ruta = $request->existing_image_path ?? null; // Use existing image path if no new image is uploaded
        }

        $disponibilidades = implode(",", $request->disponibilidad);

        $slug = $this->createSlug($request->titulo);

        // Create new anuncio
        $anuncio = new Anuncios();
        $anuncio->titulo = $request->titulo;
        $anuncio->imagen_principal = $ruta;
        $anuncio->id_localizacion = $request->id_localizacion;
        $anuncio->edad = $request->edad;
        $anuncio->nombre_apodo = $request->nombre_apodo;
        $anuncio->id_nacionalidad = $request->id_nacionalidad;
        $anuncio->precio = isset($request->precio) ? $request->precio : null;
        $anuncio->telefono = $request->telefono;
        $anuncio->zona_de_ciudad = $request->zona_de_ciudad;
        $anuncio->disponibilidad = $disponibilidades;
        $anuncio->profesion = $request->profesion;
        $anuncio->peso = $request->peso;
        $anuncio->url_whatsaap = $request->url_whatsaap;
        $anuncio->url_telegram = $request->url_telegram;
        $anuncio->descripcion = $request->descripcion;
        $anuncio->premium = Auth::user()->usuario_premium == 1 ? 1 : 0;
        $anuncio->slug = $slug;
        $anuncio->fecha_creacion = Carbon::now();
        $anuncio->fecha_reactivacion = null;
        $anuncio->id_usuario = Auth::user()->id;
        $anuncio->estado = 1;
        $anuncio->save();

        foreach ($request->forma_pago as $forma_pago) {
            $anuncio_forma_pago = new AnuncioFormaPago();
            $anuncio_forma_pago->anuncio_id = $anuncio->id;
            $anuncio_forma_pago->forma_pago_id = $forma_pago;
            $anuncio_forma_pago->estado = 1;
            $anuncio_forma_pago->save();
        }

        foreach ($request->categorias as $categoria) {
            $anuncio_categoria = new AnuncioCategoria();
            $anuncio_categoria->anuncio_id = $anuncio->id;
            $anuncio_categoria->categoria_id = $categoria;
            $anuncio_categoria->estado = 1;
            $anuncio_categoria->save();
        }

        // Guardar imágenes adicionales
        if ($request->has('imagenes_adicionales')) {

            $img_adicionales_cargar = count($request->imagenes_adicionales);

            if ($img_adicionales_cargar <= 5) {

                foreach ($request->imagenes_adicionales as $imagen) {
                    $nombreImagen = basename($imagen); // Extrae el nombre del archivo
                    $rutaOrigen = storage_path('app/public/anuncios/' . $nombreImagen);
                    $rutaDestino = public_path("img/anuncios/{$anuncio->id}/{$nombreImagen}");

                    // Asegurar que la carpeta destino existe
                    if (!File::exists(public_path("img/anuncios/{$anuncio->id}"))) {
                        File::makeDirectory(public_path("img/anuncios/{$anuncio->id}"), 0755, true);
                    }
                    // Mover la imagen si existe en el almacenamiento
                    if (File::exists($rutaOrigen)) {
                        File::move($rutaOrigen, $rutaDestino);

                        // Guardar la imagen en la BD
                        RelImagenesAnuncios::create([
                            'id_anuncio' => $anuncio->id,
                            'path_imagen' => "/img/anuncios/{$anuncio->id}/{$nombreImagen}",
                            'nombre_imagen' => $nombreImagen,
                        ]);
                    }

                }

            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se permte un maximo de 5 imagenes',
                ]);
            }

        }

        return response()->json([
            'success' => true,
            'message' => 'Anuncio agregado con éxito',
            'anuncio' => $anuncio->id,
            'proceso' => 'create'
        ], 201);
    } */

    public function createAnuncio(Request $request)
    {
        DB::beginTransaction(); // Iniciar la transacción
        try {
            if ($request->file('file')) {
                $nombre_img = pathinfo($request->file('file')->getClientOriginalName())['filename'];
                $img = $request->file('file');
                $nombreimagen = Str::slug($nombre_img) . '.' . $img->getClientOriginalExtension();
                $img->move(public_path('/img/anuncios/'), $nombreimagen);
                $ruta = '/img/anuncios/' . $nombreimagen;
            } else {
                $ruta = $request->existing_image_path ?? null;
            }

            $disponibilidades = implode(",", $request->disponibilidad);
            $slug = $this->createSlug($request->titulo);

            // Crear anuncio
            $anuncio = new Anuncios();
            $anuncio->fill([
                'titulo' => $request->titulo,
                'imagen_principal' => $ruta,
                'id_localizacion' => $request->id_localizacion,
                'edad' => $request->edad,
                'nombre_apodo' => $request->nombre_apodo,
                'id_nacionalidad' => $request->id_nacionalidad,
                'precio' => $request->precio ?? null,
                'telefono' => $request->telefono,
                'zona_de_ciudad' => $request->zona_de_ciudad,
                'disponibilidad' => $disponibilidades,
                'profesion' => $request->profesion,
                'peso' => $request->peso,
                'url_whatsaap' => $request->url_whatsaap,
                'url_telegram' => $request->url_telegram,
                'descripcion' => $request->descripcion,
                'premium' => Auth::user()->usuario_premium == 1 ? 1 : 0,
                'slug' => $slug,
                'fecha_creacion' => Carbon::now(),
                'fecha_reactivacion' => null,
                'id_usuario' => Auth::id(),
                'estado' => 1
            ]);
            $anuncio->save();

            foreach ($request->forma_pago as $forma_pago) {
                AnuncioFormaPago::create([
                    'anuncio_id' => $anuncio->id,
                    'forma_pago_id' => $forma_pago,
                    'estado' => 1
                ]);
            }

            foreach ($request->categorias as $categoria) {
                AnuncioCategoria::create([
                    'anuncio_id' => $anuncio->id,
                    'categoria_id' => $categoria,
                    'estado' => 1
                ]);
            }

            if ($request->has('imagenes_adicionales')) {
                $img_adicionales_cargar = count($request->imagenes_adicionales);
                if ($img_adicionales_cargar > 5) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Solo se permite un máximo de 5 imágenes',
                    ], 400);
                }

                foreach ($request->imagenes_adicionales as $imagen) {
                    $nombreImagen = basename($imagen);
                    $rutaOrigen = storage_path('app/public/anuncios/' . $nombreImagen);
                    $rutaDestino = public_path("img/anuncios/{$anuncio->id}/{$nombreImagen}");

                    if (!File::exists(public_path("img/anuncios/{$anuncio->id}"))) {
                        File::makeDirectory(public_path("img/anuncios/{$anuncio->id}"), 0755, true);
                    }

                    if (File::exists($rutaOrigen)) {
                        File::move($rutaOrigen, $rutaDestino);
                        RelImagenesAnuncios::create([
                            'id_anuncio' => $anuncio->id,
                            'path_imagen' => "/img/anuncios/{$anuncio->id}/{$nombreImagen}",
                            'nombre_imagen' => $nombreImagen,
                        ]);
                    }
                }
            }

            DB::commit(); // Confirmar la transacción

            return response()->json([
                'success' => true,
                'message' => 'Anuncio agregado con éxito',
                'anuncio' => $anuncio->id,
                'proceso' => 'create'
            ], 201);
        } catch (Exception $e) {
            DB::rollBack(); // Revertir cambios si hay un error
            Log::error('Error al crear anuncio: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al crear el anuncio. Inténtalo de nuevo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /* public function updateAnuncio(Request $request, $id = null)
    {
        if ($request->file('file')) {
            $nombre_img = pathinfo($request->file('file')->getClientOriginalName())['filename'];
            $img = $request->file('file');
            $nombreimagen = Str::slug($nombre_img) . "." . $img->getClientOriginalExtension();
            $img->move(public_path('/img/anuncios/'), $nombreimagen);
            $ruta =  '/img/anuncios/' . $nombreimagen;
        } else {
            $ruta = $request->existing_image_path ?? null; // Use existing image path if no new image is uploaded
        }

        $disponibilidades = implode(",", $request->disponibilidad);

        if ($id) {
            // Update existing anuncio
            $anuncio = Anuncios::findOrFail($id);
            $anuncio->titulo = $request->titulo;
            if ($ruta) {
                $anuncio->imagen_principal = $ruta;
            }
            $anuncio->id_localizacion = $request->id_localizacion;
            $anuncio->edad = $request->edad;
            $anuncio->nombre_apodo = $request->nombre_apodo;
            $anuncio->id_nacionalidad = $request->id_nacionalidad;
            $anuncio->precio = isset($request->precio) ? $request->precio : null;
            $anuncio->telefono = $request->telefono;
            $anuncio->zona_de_ciudad = $request->zona_de_ciudad;
            $anuncio->disponibilidad = $disponibilidades;
            $anuncio->profesion = $request->profesion;
            $anuncio->peso = $request->peso;
            $anuncio->url_whatsaap = $request->url_whatsaap;
            $anuncio->url_telegram = $request->url_telegram;
            $anuncio->descripcion = $request->descripcion;
            $anuncio->fecha_reactivacion = Carbon::now();
            $anuncio->save();
        } else {
            // Create new anuncio
            $anuncio = new Anuncios();
            $anuncio->titulo = $request->titulo;
            $anuncio->imagen_principal = $ruta;
            $anuncio->id_localizacion = $request->id_localizacion;
            $anuncio->edad = $request->edad;
            $anuncio->nombre_apodo = $request->nombre_apodo;
            $anuncio->id_nacionalidad = $request->id_nacionalidad;
            $anuncio->precio = isset($request->precio) ? $request->precio : '';
            $anuncio->telefono = $request->telefono;
            $anuncio->zona_de_ciudad = $request->zona_de_ciudad;
            $anuncio->disponibilidad = $disponibilidades;
            $anuncio->profesion = $request->profesion;
            $anuncio->peso = $request->peso;
            $anuncio->url_whatsaap = $request->url_whatsaap;
            $anuncio->url_telegram = $request->url_telegram;
            $anuncio->descripcion = $request->descripcion;
            $anuncio->fecha_creacion = Carbon::now();
            $anuncio->fecha_reactivacion = null;
            $anuncio->id_usuario = Auth::user()->id;
            $anuncio->estado = 1;
            $anuncio->save();
        }

        // Guardar las relaciones
        if ($id) {
            // Eliminate existing relationships for update
            AnuncioFormaPago::where('anuncio_id', $anuncio->id)->delete();
            AnuncioCategoria::where('anuncio_id', $anuncio->id)->delete();
        }

        foreach ($request->forma_pago as $forma_pago) {
            $anuncio_forma_pago = new AnuncioFormaPago();
            $anuncio_forma_pago->anuncio_id = $anuncio->id;
            $anuncio_forma_pago->forma_pago_id = $forma_pago;
            $anuncio_forma_pago->estado = 1;
            $anuncio_forma_pago->save();
        }

        foreach ($request->categorias as $categoria) {
            $anuncio_categoria = new AnuncioCategoria();
            $anuncio_categoria->anuncio_id = $anuncio->id;
            $anuncio_categoria->categoria_id = $categoria;
            $anuncio_categoria->estado = 1;
            $anuncio_categoria->save();
        }

        // Guardar imágenes adicionales
        if ($request->has('imagenes_adicionales')) {

            $img_adicionales_cargar = count($request->imagenes_adicionales);

            $cantidad_img_anuncios = RelImagenesAnuncios::where('id_anuncio', $id)->where('estado', 1)->count();

            if (($cantidad_img_anuncios + $img_adicionales_cargar) < 5) {

                foreach ($request->imagenes_adicionales as $imagen) {
                    $nombreImagen = basename($imagen); // Extrae el nombre del archivo
                    $rutaOrigen = storage_path('app/public/anuncios/' . $nombreImagen);
                    $rutaDestino = public_path("img/anuncios/{$anuncio->id}/{$nombreImagen}");

                    // Asegurar que la carpeta destino existe
                    if (!File::exists(public_path("img/anuncios/{$anuncio->id}"))) {
                        File::makeDirectory(public_path("img/anuncios/{$anuncio->id}"), 0755, true);
                    }
                    // Mover la imagen si existe en el almacenamiento
                    if (File::exists($rutaOrigen)) {
                        File::move($rutaOrigen, $rutaDestino);

                        // Guardar la imagen en la BD
                        RelImagenesAnuncios::create([
                            'id_anuncio' => $anuncio->id,
                            'path_imagen' => "/img/anuncios/{$anuncio->id}/{$nombreImagen}",
                            'nombre_imagen' => $nombreImagen,
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se permte un maximo de 5 imagenes',
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => $id ? 'Anuncio actualizado con éxito' : 'Anuncio agregado con éxito',
            'anuncio' => $anuncio->id,
            'proceso' => $id ? 'update' : 'create'
        ], 201);
    } */

    public function updateAnuncio(Request $request, $id = null)
    {
        DB::beginTransaction();
        try {
            if ($request->file('file')) {
                $nombre_img = pathinfo($request->file('file')->getClientOriginalName())['filename'];
                $img = $request->file('file');
                $nombreimagen = Str::slug($nombre_img) . '.' . $img->getClientOriginalExtension();
                $img->move(public_path('/img/anuncios/'), $nombreimagen);
                $ruta = '/img/anuncios/' . $nombreimagen;
            } else {
                $ruta = $request->existing_image_path ?? null;
            }

            $disponibilidades = implode(",", $request->disponibilidad);

            if ($id) {
                $anuncio = Anuncios::findOrFail($id);
            } else {
                $anuncio = new Anuncios();
                $anuncio->fecha_creacion = Carbon::now();
                $anuncio->id_usuario = Auth::id();
                $anuncio->estado = 1;
            }

            $anuncio->fill($request->only([
                'titulo',
                'id_localizacion',
                'edad',
                'nombre_apodo',
                'id_nacionalidad',
                'precio',
                'telefono',
                'zona_de_ciudad',
                'profesion',
                'peso',
                'url_whatsaap',
                'url_telegram',
                'descripcion'
            ]));
            if ($ruta) {
                $anuncio->imagen_principal = $ruta;
            }
            $anuncio->disponibilidad = $disponibilidades;
            $anuncio->fecha_reactivacion = Carbon::now();
            $anuncio->save();

            if ($id) {
                AnuncioFormaPago::where('anuncio_id', $anuncio->id)->delete();
                AnuncioCategoria::where('anuncio_id', $anuncio->id)->delete();
            }

            foreach ($request->forma_pago as $forma_pago) {
                AnuncioFormaPago::create(['anuncio_id' => $anuncio->id, 'forma_pago_id' => $forma_pago, 'estado' => 1]);
            }
            foreach ($request->categorias as $categoria) {
                AnuncioCategoria::create(['anuncio_id' => $anuncio->id, 'categoria_id' => $categoria, 'estado' => 1]);
            }

            if ($request->has('imagenes_adicionales')) {
                $img_adicionales_cargar = count($request->imagenes_adicionales);
                $cantidad_img_anuncios = RelImagenesAnuncios::where('id_anuncio', $anuncio->id)->where('estado', 1)->count();

                if (($cantidad_img_anuncios + $img_adicionales_cargar) >= 5) {
                    return response()->json(['success' => false, 'message' => 'Solo se permite un máximo de 5 imágenes'], 400);
                }

                foreach ($request->imagenes_adicionales as $imagen) {
                    $nombreImagen = basename($imagen);
                    $rutaOrigen = storage_path('app/public/anuncios/' . $nombreImagen);
                    $rutaDestino = public_path("img/anuncios/{$anuncio->id}/{$nombreImagen}");

                    if (!File::exists(public_path("img/anuncios/{$anuncio->id}"))) {
                        File::makeDirectory(public_path("img/anuncios/{$anuncio->id}"), 0755, true);
                    }
                    if (File::exists($rutaOrigen)) {
                        File::move($rutaOrigen, $rutaDestino);
                        RelImagenesAnuncios::create([
                            'id_anuncio' => $anuncio->id,
                            'path_imagen' => "/img/anuncios/{$anuncio->id}/{$nombreImagen}",
                            'nombre_imagen' => $nombreImagen,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $id ? 'Anuncio actualizado con éxito' : 'Anuncio agregado con éxito',
                'anuncio' => $anuncio->id,
                'proceso' => $id ? 'update' : 'create'
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar anuncio: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al actualizar el anuncio. Inténtalo de nuevo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function createSlug($title)
    {
        // Reemplazar espacios en blanco por guiones
        $slug = Str::slug($title, '-');

        // Convertir todo el string a minúsculas
        $slug = strtolower($slug);

        return $slug;
    }

    public function listarAnuncios()
    {
        $anuncios = Anuncios::select([
            'anuncios.id AS id_anuncio',
            'anuncios.imagen_principal',
            'anuncios.titulo',
            'anuncios.nombre_apodo',
            'estados.estado_nombre',
            'anuncios.estado AS id_estado',
            'anuncios.fecha_creacion',
            'anuncios.premium',
            'users.name',
            DB::raw("GROUP_CONCAT(categorias.categoria ORDER BY categorias.categoria SEPARATOR ', ') AS categorias")
        ])
            ->leftJoin('estados', 'anuncios.estado', '=', 'estados.id')
            ->leftJoin('anuncio_categoria', 'anuncios.id', '=', 'anuncio_categoria.anuncio_id')
            ->leftJoin('categorias', 'anuncio_categoria.categoria_id', '=', 'categorias.id')
            ->leftJoin('users', 'anuncios.id_usuario', '=', 'users.id');
        if (Auth::user()->rol_id == 2) {
            $anuncios = $anuncios->where('anuncios.id_usuario', Auth::user()->id);
        }
        $anuncios = $anuncios->groupBy('anuncios.id', 'anuncios.imagen_principal', 'anuncios.titulo', 'anuncios.nombre_apodo', 'estados.estado_nombre', 'anuncios.fecha_creacion', 'anuncios.estado', 'anuncios.premium', 'users.name')
            ->orderBy('anuncios.id', 'desc')
            ->get();

        return view('anuncios.listar-anuncios', ['anuncios' => $anuncios]);
    }

    function edit($id)
    {
        $provincias = Provincias::where('estado', 1)->select('id AS id_provincia', 'provincia')->get();
        $nacionalidades = Nacionalidades::where('estado', 1)->select('id AS id_nacionalidad', 'nacionalidad')->get();
        $formas_pagos = FormasPago::where('estado', 1)->select('id AS id_forma_pago', 'forma_pago')->get();
        $categorias = Categorias::where('estado', 1)->select('id AS id_categoria', 'categoria AS categoria_nombre')->get();

        $anuncio = Anuncios::select(
            'id AS id_anuncio',
            'titulo',
            'imagen_principal',
            'id_localizacion',
            'descripcion',
            'edad',
            'nombre_apodo',
            'id_nacionalidad',
            'precio',
            'telefono',
            'zona_de_ciudad',
            'profesion',
            'peso',
            'url_whatsaap',
            'url_telegram',
            'premium',
            'disponibilidad'
        )->where('id', $id)->first();

        $formas_pago = AnuncioFormaPago::select('forma_pago_id')->where('estado', '=', 1)->where('anuncio_id', $id)->get();

        $anuncio_categorias = AnuncioCategoria::select('categoria_id')->where('estado', '=', 1)->where('anuncio_id', $id)->get();

        $imagenes_adicionales = RelImagenesAnuncios::select('id', 'path_imagen', 'nombre_imagen')->where('id_anuncio', $id)->where('estado', 1)->get();

        return view('anuncios.index', ['provincias' => $provincias, 'nacionalidades' => $nacionalidades, 'formas_pagos' => $formas_pagos, 'categorias' => $categorias, 'anuncio' => $anuncio, 'anuncio_formas_pagos' => $formas_pago, 'anuncio_categorias' => $anuncio_categorias, 'imagenes_adicionales' => $imagenes_adicionales]);
    }

    function changeEstadoAnuncio(Request $request)
    {
        Anuncios::find($request->id)->update(['estado' => $request->estado]);
        AnuncioFormaPago::where('anuncio_id', $request->id)->update(['estado' => $request->estado]);
        AnuncioCategoria::where('anuncio_id', $request->id)->update(['estado' => $request->estado]);

        return response()->json([
            'success' => true,
            'message' => $request->estado == 1 ? 'Anuncio activado con exito.' : 'El anuncio fue desactivado correctamente'
        ], 200);
    }

    function updateAnuncioPremium(Request $request)
    {

        Anuncios::find($request->id)->update(['premium' => $request->estado == 0 ? NULL : $request->estado]);

        return response()->json([
            'success' => true,
            'title' => $request->estado == 1 ? '¡Felicidades!!!' : 'Proceso completado.',
            'message' => $request->estado == 1 ? 'El anuncio se actualizo a premium.' : 'El anuncio ya no es premium.'
        ], 200);
    }

    public function uploadImages(Request $request)
    {

        $path = $request->file('file')->store('public/anuncios');

        return response()->json(['path' => $path], 200);
    }

    public function deleteImage(Request $request)
    {
        $rel_imagen = RelImagenesAnuncios::find($request->id_imagen);

        $rel_imagen->estado = 0;

        $rel_imagen->save();

        return response()->json([
            'success' => true,
            'message' => 'La imagen fue eliminada correctamente'
        ], 200);
    }
}
