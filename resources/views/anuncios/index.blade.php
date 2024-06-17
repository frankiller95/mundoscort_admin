@extends('adminlte::page')

@section('title', 'Inicio')

@section('content_header')
    <h1 class="text-center"><b>Aquí puedes crear tus anuncios:<b></h1>
@stop

@section('content')
    <x-app-layout>
        <div class="card">
            <div class='card-body' id="body-anuncios">
                <div class="row mx-auto">
                    <div class="col-lg-4">
                        <label class="form-label">Titulo<span class="text-danger">*</span></label>
                        <input type="text" name="titulo" id="titulo" class="form-control"
                            value="{{ $anuncio->titulo ?? '' }}" required>
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Imagen<span class="text-danger">*</span></label>
                        <input type="file" name="file" id="imagen" class="form-control" accept="image/*"
                            @if (!isset($anuncio->imagen_principal)) required @endif>
                        @if (isset($anuncio->imagen_principal))
                            <img src="{{ asset($anuncio->imagen_principal ?? '') }}" alt="Imagen principal"
                                class="img-thumbnail mt-2" width="150">
                        @endif
                    </div>

                    <div class="col-lg-4">
                        <label>Localización<span class="text-danger">*</span></label>
                        <select class="select-form" id="id_localizacion" name="id_localizacion" style="width:100%" required>
                            <option value=''>Indica la localización</option>
                            @foreach ($provincias as $item)
                                <option value="{{ $item->id_provincia }}"
                                    {{ isset($anuncio->id_localizacion) && $item->id_provincia == $anuncio->id_localizacion? 'selected' : '' }}>
                                    {{ $item->provincia }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mx-auto mt-4">
                    <div class="col-lg-4">
                        <label for="descripcion" class="form-label">Descripción<span class="text-danger">*</span></label>
                        <textarea name="descripcion" id="descripcion" cols="30" rows="5" class="form-control" required>{{ $anuncio->descripcion ?? '' }}</textarea>
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Edad<span class="text-danger">*</span></label>
                        <input type="number" name="edad" id="edad" class="form-control"
                            value="{{ $anuncio->edad ?? '' }}" required>
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Nombre o apodo<span class="text-danger">*</span></label>
                        <input type="text" name="nombre_apodo" id="nombre_apodo" class="form-control"
                            value="{{ $anuncio->nombre_apodo ?? '' }}" required>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-lg-4">
                        <label>Nacionalidad<span class="text-danger">*</span></label>
                        <select class="select-form" id="id_nacionalidad" name="id_nacionalidad" style="width:100%" required>
                            <option value=''>Indica la nacionalidad</option>
                            @foreach ($nacionalidades as $item)
                                <option value="{{ $item->id_nacionalidad }}"
                                    {{ isset($anuncio->id_nacionalidad) && $item->id_nacionalidad == $anuncio->id_nacionalidad? 'selected' : '' }}>
                                    {{ $item->nacionalidad }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Precio</label>
                        <input type="text" name="precio" id="precio" class="form-control"
                            value="{{ $anuncio->precio ?? '' }}" onkeypress="return isNumberKey(event)"
                            placeholder="El valor del precio debe ser en euros">
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Telefono<span class="text-danger">*</span></label>
                        <input type="text" name="telefono" id="telefono" class="form-control"
                            value="{{ $anuncio->telefono ?? '' }}" onkeypress="return isNumberKey(event)"
                            placeholder="El numero debe tener el indicativo" required>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-lg-4">
                        <label class="form-label">Zona de la ciudad<span class="text-danger">*</span></label>
                        <input type="text" name="zona_de_ciudad" id="zona_de_ciudad" class="form-control"
                            value="{{ $anuncio->zona_de_ciudad ?? '' }}" required>
                    </div>

                    <div class="col-lg-4">
                        <label>Formas de pago<span class="text-danger">*</span></label>
                        <br>
                        @foreach ($formas_pagos as $item)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" value="{{ $item->id_forma_pago }}"
                                    id="pago_{{ $item->id_forma_pago }}" name="forma_pago[]"
                                    @if (isset($anuncio_formas_pagos) &&
                                            in_array($item->id_forma_pago, $anuncio_formas_pagos->pluck('forma_pago_id')->toArray())) checked @endif>
                                <label class="form-check-label" for="pago_{{ $item->id_forma_pago }}">
                                    {{ $item->forma_pago }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    {{-- <div class="col-lg-4">
                            <label>Disponibilidad<span class="text-danger">*</span></label>
                            <br>
                            @foreach (['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabados', 'Domingos'] as $key => $dia)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="{{ $key + 1 }}" id="dispo_{{ strtolower($dia) }}" name="disponibilidad[]" {{ in_array($key + 1, json_decode($anuncio->disponibilidad)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="dispo_{{ strtolower($dia) }}">
                                        {{ $dia }}
                                    </label>
                                </div>
                            @endforeach
                        </div> --}}
                    <div class="col-lg-4">
                        <label>Disponibilidad<span class="text-danger">*</span></label>
                        <br>
                        @php
                            $disponibilidadArray = isset($anuncio->disponibilidad)
                                ? explode(',', $anuncio->disponibilidad)
                                : [];
                        @endphp
                        @foreach (['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabados', 'Domingos'] as $key => $dia)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" value="{{ $key + 1 }}"
                                    id="dispo_{{ strtolower($dia) }}" name="disponibilidad[]"
                                    {{ in_array($key + 1, $disponibilidadArray) ? 'checked' : '' }}>
                                <label class="form-check-label" for="dispo_{{ strtolower($dia) }}">
                                    {{ $dia }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-lg-4">
                        <label class="form-label">Profesión</label>
                        <input type="text" name="profesion" id="profesion" class="form-control"
                            value="{{ $anuncio->profesion ?? '' }}">
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Peso <span class="text-danger">*</span></label>
                        <input type="text" name="peso" id="peso" class="form-control"
                            value="{{ $anuncio->peso ?? '' }}" placeholder="Peso en Kg"
                            onkeypress="return isNumberKey(event)" required>
                    </div>

                    {{-- <div class="col-lg-4">
                            <label>Categoria<span class="text-danger">*</span></label>
                            <br>
                            @foreach ($categorias as $categoria)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="{{ $categoria->id_categoria }}" id="cate_{{ $categoria->id_categoria }}" name="categorias[]" {{ in_array($categoria->id_categoria, $anuncio_categorias->pluck('categoria_id')->toArray()) ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="cate_{{ $categoria->id_categoria }}">
                                        {{ $categoria->categoria_nombre }}
                                    </label>
                                </div>
                            @endforeach
                        </div> --}}
                    <div class="col-lg-4">
                        <label>Categoria<span class="text-danger">*</span></label>
                        <br>
                        @foreach ($categorias as $categoria)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" value="{{ $categoria->id_categoria }}"
                                    id="cate_{{ $categoria->id_categoria }}" name="categorias[]"
                                    @if (isset($anuncio_categorias) &&
                                            in_array($categoria->id_categoria, $anuncio_categorias->pluck('categoria_id')->toArray())) checked @endif>
                                <label class="form-check-label" for="cate_{{ $categoria->id_categoria }}">
                                    {{ $categoria->categoria_nombre }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                </div>

                <div class="row mt-4">
                    <div class="col-lg-4">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="whatsaap" id="usar_whatsaap"
                                name="usar_contactos[]" {{ isset($anuncio->url_whatsaap) ? 'checked' : '' }}>
                            <label class="form-check-label" for="usar_whatsaap">
                                Contactar por Whatsaap
                            </label>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="telegram" id="usar_telegram"
                                name="usar_contactos[]" {{ isset($anuncio->url_telegram) ? 'checked' : '' }}>
                            <label class="form-check-label" for="usar_telegram">
                                Contactar por Telegram
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row mt-1">
                    <div class="col-lg-4">
                        <div id="zone_whatsaap" style="display: {{ isset($anuncio->url_whatsaap) ? 'block' : 'none' }}">
                            <label class="form-label">Url Whatsaap</label>
                            <input type="text" name="url_whatsaap" id="url_whatsaap" class="form-control"
                                value="{{ $anuncio->url_whatsaap ?? '' }}" placeholder="Ej: +348285647899">
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div id="zone_telegram" style="display: {{ isset($anuncio->url_telegram) ? 'block' : 'none' }}">
                            <label class="form-label">Url Telegram</label>
                            <input type="text" name="url_telegram" id="url_telegram" class="form-control"
                                value="{{ $anuncio->url_telegram ?? '' }}" placeholder="Ej: @userTelgram">
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <x-primary-button class="ms-4"
                        onclick="agregarAnuncio({{ isset($anuncio->id_anuncio) ? $anuncio->id_anuncio : null }})">
                        {{ isset($anuncio->id_anuncio) ? __('Actualizar el anuncio') : __('Agregar el anuncio') }}
                    </x-primary-button>
                </div>
            </div>
        </div>
    </x-app-layout>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script src="{{ asset('js/admin/anuncios.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            function isNumberKey(evt) {
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if (charCode != 8 && charCode != 0 && (charCode < 48 || charCode > 57)) {
                    evt.preventDefault();
                    return false;
                }
                return true;
            }
            // Función para mostrar u ocultar un elemento basado en su ID
            function toggleVisibility(container, id, isVisible) {
                var elementContainer = document.getElementById(container);
                var element = document.getElementById(id);
                console.log('elementContainer', elementContainer);
                console.log('element', element);
                if (isVisible) {
                    elementContainer.style.display = ""; // Mostrar
                    element.value = '';
                } else {
                    elementContainer.style.display = "none"; // Ocultar
                    element.value = '';
                }
            }

            // Checkbox de WhatsApp
            var whatsappCheckbox = document.getElementById('usar_whatsaap');
            // Checkbox de Telegram
            var telegramCheckbox = document.getElementById('usar_telegram');

            // Evento para el checkbox de WhatsApp
            whatsappCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    toggleVisibility('zone_whatsaap', 'url_whatsaap', true); // Mostrar si está checked
                } else {
                    toggleVisibility('zone_whatsaap', 'url_whatsaap', false); // Ocultar si está unchecked
                }
            });

            // Evento para el checkbox de Telegram
            telegramCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    toggleVisibility('zone_telegram', 'url_telegram', true); // Mostrar si está checked
                } else {
                    toggleVisibility('zone_telegram', 'url_telegram', false); // Ocultar si está unchecked
                }
            });
        });
    </script>
@stop
