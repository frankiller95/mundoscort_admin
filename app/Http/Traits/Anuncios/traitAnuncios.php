<?php


namespace App\Http\Traits\Anuncios;

use App\Models\AnuncioCategoria;
use App\Models\AnuncioFormaPago;
use App\Models\Anuncios;
use App\Models\Categorias;
use App\Models\FormasPago;
use App\Models\Nacionalidades;
use App\Models\Provincias;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        }

        $disponibilidades = implode(",", $request->disponibilidad);

        // Crear el anuncio
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
        $anuncio->estado = 1;
        $anuncio->save();

        // Guardar las relaciones
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

        return response()->json([
            'success' => true,
            'message' => 'Anuncio agregado con éxito',
            'anuncio' => $anuncio->id
        ], 201);
    } */

    public function createOrUpdateAnuncio(Request $request, $id = null)
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

        $process = "create";

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
            $anuncio->precio = isset($request->precio) ? $request->precio : '';
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


            $process = "update";

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

        return response()->json([
            'success' => true,
            'message' => $id ? 'Anuncio actualizado con éxito' : 'Anuncio agregado con éxito',
            'anuncio' => $anuncio->id,
            'proceso' => $id ? 'update' : 'create'
        ], 201);
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
            DB::raw("GROUP_CONCAT(categorias.categoria ORDER BY categorias.categoria SEPARATOR ', ') AS categorias")
        ])
            ->leftJoin('estados', 'anuncios.estado', '=', 'estados.id')
            ->leftJoin('anuncio_categoria', 'anuncios.id', '=', 'anuncio_categoria.anuncio_id')
            ->leftJoin('categorias', 'anuncio_categoria.categoria_id', '=', 'categorias.id');
            /* ->where('anuncios.estado', 1) */
            /* ->where('anuncio_categoria.estado', 1)
            ->where('categorias.estado', 1) */
        if (Auth::user()->rol_id == 2) {
            $anuncios = $anuncios->where('anuncios.id_usuario', Auth::user()->id);
        }
        $anuncios = $anuncios->groupBy('anuncios.id', 'anuncios.imagen_principal', 'anuncios.titulo', 'anuncios.nombre_apodo', 'estados.estado_nombre', 'anuncios.fecha_creacion', 'anuncios.estado')
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
            'disponibilidad'
        )->where('id', $id)->first();

        $formas_pago = AnuncioFormaPago::select('forma_pago_id')->where('estado', '=', 1)->where('anuncio_id', $id)->get();

        $anuncio_categorias = AnuncioCategoria::select('categoria_id')->where('estado', '=', 1)->where('anuncio_id', $id)->get();

        return view('anuncios.index', ['provincias' => $provincias, 'nacionalidades' => $nacionalidades, 'formas_pagos' => $formas_pagos, 'categorias' => $categorias, 'anuncio' => $anuncio, 'anuncio_formas_pagos' => $formas_pago, 'anuncio_categorias' => $anuncio_categorias]);
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
}
