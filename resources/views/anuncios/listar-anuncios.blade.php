@extends('adminlte::page')

@section('title', 'Inicio')

@section('content_header')
    <h1 class="text-center"><b>Mis anuncios:<b></h1>
@stop

@section('content')
    <x-app-layout>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <table class="table table-image" id="datatable-anuncios">
                        <thead>
                            <tr>
                                <th scope="col">id</th>
                                <th scope="col"></th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Categoria</th>
                                <th scope="col">Usuario</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Fecha/hora<br>Creacion</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($anuncios as $anuncio)
                                <tr>
                                    <th scope="row">{{ $anuncio->id_anuncio }}</th>
                                    <td class="w-25">
                                        <img src="{{ asset($anuncio->imagen_principal) }}" class="img-fluid img-thumbnail"
                                            alt="Sheep">
                                    </td>
                                    <td>
                                        <h3>{{ $anuncio->nombre_apodo }}</h3>
                                        <p>{{ $anuncio->titulo }}</p>
                                    </td>
                                    <td>{{ $anuncio->categorias }}</td>
                                    <td>{{ $anuncio->name }}</td>
                                    <td>{{ $anuncio->estado_nombre }}</td>
                                    <td>{{ $anuncio->fecha_creacion }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a
                                                class="btn btn-warning br-9 mx-1"href="{{ route('anuncios.edit', $anuncio->id_anuncio) }}" style="color: #fff;">
                                                <i class="fas fa-pen" style="color: #fff;"></i>
                                            </a>
                                            @if ($anuncio->id_estado == 1 && Auth::user()->usuario_premium == 1 && $anuncio->premium == NULL)
                                            <a class="btn btn-warning" href="javascript:void(0);" onclick="indicarPremium({{ $anuncio->id_anuncio }}, 1)" style="color: #fff;" title="Anuncio Top"><i class="far fa-star" style="color: #fff;"></i></a>
                                            @endif
                                            @if ($anuncio->id_estado == 1 && Auth::user()->usuario_premium == 1 && $anuncio->premium == 1)
                                            <a class="btn btn-danger" href="javascript:void(0);" onclick="indicarPremium({{ $anuncio->id_anuncio }}, 0)" style="color: #fff;" title="Remover Top"><i class="far fa-star" style="color: #fff;"></i></a>
                                            @endif
                                            <br />
                                            @if ($anuncio->id_estado == 2)
                                                <a class="btn btn-info br-9 mx-1"href="javascript:void(0);"
                                                    onclick="cambiarEstadoAnuncio({{ $anuncio->id_anuncio }}, 1)">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </a>
                                            @endif
                                            @if ($anuncio->id_estado == 1)
                                                <a class="btn btn-danger br-9 mx-1"href="javascript:void(0);"
                                                    onclick="cambiarEstadoAnuncio({{ $anuncio->id_anuncio }}, 2)">
                                                    <i class="fas fa-ban"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-app-layout>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <style>
        .container {
            padding: 2rem 0rem;
        }

        h4 {
            margin: 2rem 0rem 1rem;
        }

        .table-image {

            td,
            th {
                vertical-align: middle;
                text-align: center;
            }
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('js/admin/anuncios.js') }}"></script>
    <script>
        var dt = new DataTable('#datatable-anuncios', {
            order: [
                [0, 'desc']
            ]
        });
        dt.column(0).visible(false);
    </script>
@stop
