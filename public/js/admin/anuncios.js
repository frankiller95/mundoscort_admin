"use strict";

let token = $('meta[name="csrf-token"]').attr("content");

let hostUrl = window.location.origin;
if (hostUrl == 'https://mundoscort.com.es' || hostUrl == 'https://mundoscort.com.es/') {
    hostUrl = window.location.origin + "/login";
}

hostUrl = window.location.origin + "/login";

const limpiarCampos = () => {
    document.getElementById('titulo').value = '';
    document.getElementById('imagen').value = '';
    document.getElementById('id_localizacion').value = '';
    document.getElementById('descripcion').value = '';
    document.getElementById('edad').value = '';
    document.getElementById('nombre_apodo').value = '';
    document.getElementById('id_nacionalidad').value = '';
    document.getElementById('precio').value = '';
    document.getElementById('telefono').value = '';
    document.getElementById('zona_de_ciudad').value = '';
    document.getElementById('profesion').value = '';
    document.getElementById('peso').value = '';
    document.getElementById('url_whatsaap').value = '';
    document.getElementById('url_telegram').value = '';

    // Si tienes select2 o algún otro plugin de selección, puede que necesites refrescar el select
    $('#id_localizacion').val('').trigger('change');
    $('#id_nacionalidad').val('').trigger('change');

    // Limpiar checkboxes y otros campos adicionales si es necesario
    document.querySelectorAll('input[type=checkbox]').forEach(el => el.checked = false);
};

const agregarAnuncio = (id = '') => {
    // Obtener los elementos del formulario
    const titulo = document.getElementById("titulo").value;
    const imagen = document.getElementById("imagen").files[0];
    const localizacion = document.getElementById("id_localizacion").value;
    const descripcion = document.getElementById("descripcion").value;
    const edad = document.getElementById("edad").value;
    const nombreApodo = document.getElementById("nombre_apodo").value;
    const nacionalidad = document.getElementById("id_nacionalidad").value;
    const precio = document.getElementById("precio").value;
    const telefono = document.getElementById("telefono").value;
    const zonaCiudad = document.getElementById("zona_de_ciudad").value;
    const profesion = document.getElementById("profesion").value;
    const peso = document.getElementById("peso").value;

    if (
        !titulo || titulo == '' ||
        !localizacion || localizacion == '' ||
        !descripcion || descripcion == '' ||
        !edad || edad == '' ||
        !nombreApodo || nombreApodo == '' ||
        !nacionalidad || nacionalidad == '' ||
        !telefono || telefono == '' ||
        !zonaCiudad || zonaCiudad == ''
    ) {
        /* alert('Por favor complete todos los campos obligatorios marcados con *');
        return; */
        Swal.fire({
            icon: "error",
            title: "Oops...",
            html: 'Por favor complete todos los campos obligatorios marcados con <span class="text-danger">*</span>',
        });

        return 0;
    }

    if (id == null && !imagen) {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            html: 'La imagen es obligatoria al crear un anuncio <span class="text-danger">*</span>',
        });

        return 0;
    }

    // Obtener las formas de pago
    const formasPago = [];
    document
        .querySelectorAll('input[name="forma_pago[]"]:checked')
        .forEach((checkbox) => {
            formasPago.push(checkbox.value);
        });

    // Obtener la disponibilidad
    const disponibilidad = [];
    document
        .querySelectorAll('input[name="disponibilidad[]"]:checked')
        .forEach((checkbox) => {
            disponibilidad.push(checkbox.value);
        });

    // Obtener las categorías
    const categorias = [];
    document
        .querySelectorAll('input[name="categorias[]"]:checked')
        .forEach((checkbox) => {
            categorias.push(checkbox.value);
        });

    // Obtener URL de WhatsApp y Telegram si están seleccionados
    const usarWhatsApp = document.getElementById("usar_whatsaap").checked;
    const usarTelegram = document.getElementById("usar_telegram").checked;
    const urlWhatsApp = usarWhatsApp
        ? document.getElementById("url_whatsaap").value
        : "";
    const urlTelegram = usarTelegram
        ? document.getElementById("url_telegram").value
        : "";

    // Crear el FormData para enviar
    const formData = new FormData();
    formData.append("titulo", titulo);
    formData.append("file", imagen);
    formData.append("id_localizacion", localizacion);
    formData.append("descripcion", descripcion);
    formData.append("edad", edad);
    formData.append("nombre_apodo", nombreApodo);
    formData.append("id_nacionalidad", nacionalidad);
    formData.append("precio", precio);
    formData.append("telefono", telefono);
    formData.append("zona_de_ciudad", zonaCiudad);
    formData.append("profesion", profesion);
    formData.append("peso", peso);
    formasPago.forEach((pago) => formData.append("forma_pago[]", pago));
    disponibilidad.forEach((dispo) =>
        formData.append("disponibilidad[]", dispo)
    );
    categorias.forEach((categoria) =>
        formData.append("categorias[]", categoria)
    );
    formData.append("url_whatsaap", urlWhatsApp);
    formData.append("url_telegram", urlTelegram);
    formData.append("id", id);

    let urlRest = '';
    if (id == '' || id == null) {
        urlRest = hostUrl + "/admin/guardar_anuncio";
    } else {
        urlRest = hostUrl + "/admin/actualizar_anuncio/" + id;
    }
    // Enviar el formulario usando fetch
    fetch(urlRest, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": token,
        },
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                /* alert('Anuncio agregado con éxito'); */
                Swal.fire({
                    icon: "success",
                    title: "Felicidades...!",
                    text: `${data.message}`,
                });

                if (data.proceso == "create") {
                    limpiarCampos();
                } else {
                    setTimeout(function () {
                        location.reload();
                    }, 3000);
                }
            } else {
                /* alert('Hubo un error al agregar el anuncio'); */
                Swal.fire({
                    icon: "error",
                    title: "ooppss...",
                    text: "Hubo un error al agregar el anuncio",
                });
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            Swal.fire({
                icon: "error",
                title: "ooppss...",
                text: "Hubo un error al agregar el anuncio",
            });
        });
};

const cambiarEstadoAnuncio = (id, estado) => {
    Swal.fire({
        title: "¿Estas seguro?",
        text: estado == 1 ? "Si confirmas el anuncio sera activado." : "Si confirmas el anuncio sera desactivado.",
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: "Si, estoy seguro.",
        confirmButtonColor: '#dc3545',
        denyButtonText: `Cancelar`,
        cancelButtonText: `Cancelar`
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            if (!id || !estado) {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    html: 'Error en el proceso'
                });

                return 0;
            }

            const formData = new FormData();
            formData.append('id', id);
            formData.append('estado', estado);
            fetch(hostUrl + '/admin/update_estado', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        /* alert('Anuncio agregado con éxito'); */
                        Swal.fire({
                            icon: "info",
                            title: "Estado Actualizado...!",
                            text: `${data.message}`
                        });

                        setTimeout(function () {
                            location.reload();
                        }, 4000);


                    } else {
                        /* alert('Hubo un error al agregar el anuncio'); */
                        Swal.fire({
                            icon: "error",
                            title: "ooppss...",
                            text: 'Hubo un error en el proceso, contacta el administrador o intenta nuevamente'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: "error",
                        title: "ooppss...",
                        text: 'Hubo un error en el proceso, contacta el administrador o intenta nuevamente'
                    });
                });
        } else if (result.isDenied) {
            Swal.fire("Changes are not saved", "", "info");
        }
    });
}

const indicarPremium = (id, estado) => {

    const formData = new FormData();
    formData.append('id', id);
    formData.append('estado', estado)
    fetch(hostUrl + '/admin/set_anuncio_premium', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token
        },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                /* alert('Anuncio agregado con éxito'); */
                Swal.fire({
                    icon: "success",
                    title: `${data.title}`,
                    text: `${data.message}`
                });

                setTimeout(function () {
                    location.reload();
                }, 4000);


            } else {
                /* alert('Hubo un error al agregar el anuncio'); */
                Swal.fire({
                    icon: "error",
                    title: "ooppss...",
                    text: 'Hubo un error en el proceso, contacta el administrador o intenta nuevamente'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: "error",
                title: "ooppss...",
                text: 'Hubo un error en el proceso, contacta el administrador o intenta nuevamente'
            });
        });

}

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 8 && charCode != 0 && (charCode < 48 || charCode > 57)) {
        evt.preventDefault();
        return false;
    }
    return true;
}
