@extends('adminlte::page')

@section('title', 'Perfil')

@section('content_header')
    <h2 class="font-semibold text-xl text-dark leading-tight">
        {{ __('Perfil') }}
    </h2>
@stop

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Sección de actualización de información del perfil -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Sección de actualización de contraseña -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Mostrar solo si el rol es Admin (rol_id == 1) -->
            {{-- @if (Auth::user()->rol_id == 1)
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            @endif --}}
        </div>
    </div>
@stop

@section('css')
    {{-- Aquí puedes agregar tus hojas de estilo personalizadas --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    {{-- Aquí puedes agregar tus scripts personalizados --}}
@stop
