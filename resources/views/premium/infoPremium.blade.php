@extends('adminlte::page')

@section('title', 'Anuncios')

@section('content_header')
    <h1 class="text-center"><b>Esta es la información de tu paquete premium<b></h1>
@stop

@section('content')

<x-app-layout>
    <div class="card">
        <div class='card-body' id="body-premium">
            <div class="row mx-auto">
                <div class="container center">
                    <h2>paquete premium:</h2>
                    <p>{{ $nombre_paquete }}</p>
                    <br>
                    <h2>Referencia de pago:</h2>
                    <p>{{ $referencia_pago }}</p>
                    <br>
                    <h2>Premium hasta:</h2>
                    <p>{{ $premium_hasta }}</p>
                    <br>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop
