@extends('adminlte::page')

@section('title', 'Inicio')

@section('content_header')
    <h1 class="text-center"><b>Mundoscort.es<b></h1>
@stop

@section('content')
    <h5 class="text-center word-break">¡Hola!, <b>{{ Auth::user()->name }}</b> Desde aquí podrás crear tus anuncios, renovarlos, unirte a un plan premium y realizarles un seguimiento</h5>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
@stop
