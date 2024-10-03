@extends('adminlte::page')

@section('title', 'Inicio')

@section('content_header')
    <h1 class="text-center"><b>Usuarios registrados:<b></h1>
@stop

@section('content')

    {{-- <x-app-layout> --}}
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <table class="table table-image" id="datatable-anuncios">
                        <thead>
                            <tr>
                                <th scope="col">id</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Correo</th>
                                <th scope="col">rol</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <th scope="row">{{ $user->id }}</th>
                                    <td>
                                        <h3>{{ $user->name }}</h3>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->rol_id == 1 ? 'Administrador' : 'Anunciador' }}</td>
                                    <td>{{ $user->estado == 1 ? 'Activo' : 'Inactivo' }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a
                                                class="btn btn-warning br-9 mx-1"href="{{ route('anuncios.edit', $user->id) }}" style="color: #fff;">
                                                <i class="fas fa-pen" style="color: #fff;"></i>
                                            </a>
                                            @if ($user->estado == 1 && $user->usuario_premium == 0)
                                            <a class="btn btn-warning" href="javascript:void(0);" onclick="indicarPremium({{ $user->id }}, 1)" style="color: #fff;" title="Anuncio Top"><i class="far fa-star" style="color: #fff;"></i></a>
                                            @endif
                                            @if ($user->id == 1 && $user->usuario_premium == 1)
                                            <a class="btn btn-danger" href="javascript:void(0);" onclick="indicarPremium({{ $user->id }}, 0)" style="color: #fff;" title="Remover Top"><i class="far fa-star" style="color: #fff;"></i></a>
                                            @endif
                                            <br />
                                            @if ($user->estado == 2)
                                                <a class="btn btn-info br-9 mx-1"href="javascript:void(0);"
                                                    onclick="cambiarEstadoAnuncio({{ $user->id }}, 1)">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </a>
                                            @endif
                                            @if ($user->estado == 1)
                                                <a class="btn btn-danger br-9 mx-1"href="javascript:void(0);"
                                                    onclick="cambiarEstadoAnuncio({{ $user->id }}, 2)">
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
    {{-- </x-app-layout> --}}
@stop

@section('css')
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
    <script src="{{ asset('js/admin/usuarios.js') }}"></script>
    <script>
        var dt = new DataTable('#datatable-anuncios', {
            order: [
                [0, 'desc']
            ]
        });
        dt.column(0).visible(false);
    </script>
@stop
