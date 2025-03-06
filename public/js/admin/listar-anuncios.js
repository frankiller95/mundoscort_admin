"use strict";

let token = $('meta[name="csrf-token"]').attr("content");

let hostUrl = window.location.origin;
if (hostUrl == 'https://mundoscort.com.es' || hostUrl == 'https://mundoscort.com.es/') {
    hostUrl = window.location.origin + "/login";
}
hostUrl = window.location.origin + "/login";

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
