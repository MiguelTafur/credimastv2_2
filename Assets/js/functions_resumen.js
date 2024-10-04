let divLoading = document.querySelector("#divLoading");
let checkbox = document.querySelector("#diasSemanales");

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp() {
    
}

//TRAER TODOS LOS CLIENTES EN EL SELECT
function fntClientesPrestamo()
{
    if(document.querySelector("#listClientes")){
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url+'/Clientes/getSelectClientes';
        request.open("POST",ajaxUrl,true);
        request.send();

        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
                document.querySelector('#listClientes').innerHTML = request.responseText;
                $('#listClientes').select2({
                    dropdownParent: $('#modalFormPrestamo'),
                    placeholder: 'Seleccione un Cliente',
                    language: {
                        noResults: function() {
                            return "No hay resultado";        
                        },
                        searching: function() {
                            return "Buscando...";
                        }
                    },
                    disabled: false
                });
            }
        }
    }
}

//VISTA PARA CREAR UN PRÉSTAMO
function fntNewVenta()
{
    $('#listFormato').select2({
        dropdownParent: $('#modalFormPrestamo'),
        placeholder: 'Seleccione un Formato',
        language: {
            noResults: function() {
                return "No hay resultado";        
            },
            searching: function() {
                return "Buscando...";
            }
        }
    });

    fntClientesPrestamo();

    $('#listFormato').on("change", function(e) {
        const options = e.target.value;
        if(options == 1) {
            checkbox.disabled = false;
        } else if(options == 2 || options == 3) {
            checkbox.disabled = true;
        }
    });
    
    $('#modalFormPrestamo').modal('show');

    let formPrestamos = document.querySelector("#formPrestamos");
    formPrestamos.onsubmit = function(e){
        e.preventDefault();

        let intCliente = document.querySelector('#listClientes').value;
        let intMonto = document.querySelector('#txtMonto').value;
        let intTaza = document.querySelector('#txtTaza').value;
        let intPlazo = document.querySelector('#txtPlazo').value;
        let intFormato = document.querySelector('#listFormato').value;

        if(intCliente == "" || intMonto == "" || intTaza == "" || intPlazo == "" || intFormato == ""){
            Swal.fire("Atención", "Todos los campos son obligatorios.", "error");
            return false;
        }

        let ElementsValid = document.getElementsByClassName("valid");
        for (let i = 0; i < ElementsValid.length; i++) {
            if(ElementsValid[i].classList.contains('is-invalid')){
                Swal.fire("Atencion!", "Por favor verifique los campos en rojo.", "error");
                return false;
            }
        }
        fntRegistrarPrestamo();
    }
}
//REGISTRAR PRÉSTAMO
async function fntRegistrarPrestamo()
{
    divLoading.style.display = "flex";
    try {
        const data = new FormData(formPrestamos);
        let resp = await fetch(base_url + '/Prestamos/setPrestamo', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: data
        });
        json = await resp.json();
        if(json.status) {
            $('#modalFormPrestamo').modal("hide");
            formPrestamos.reset();
            $('#listClientes').val(null).trigger('change');
            $('#listFormato').val(null).trigger('change');
            //Swal.fire("Roles de usuario", json.msg ,"success");
            Toast.fire({
                icon: "success",
                title: json.msg
            });
        } else {
            Swal.fire("Error", json.msg , "error");
            /*Toast.fire({
                icon: "warning",
                title: json.msg
            });*/
            console.log(json.msg);
        }
    } catch (error) {
        Swal.fire("Error", "La sesión expiró, recarga la página para entrar nuevamente" , "error");
        /*Toast.fire({
            icon: "error",
            title: "Ocurrió un error interno"
        });*/
        console.log(error);
    }
    divLoading.style.display = "none";
    return false;
}

//VISTA PARA CREAR UN CLIENTE EN LA SECCIÓN RESUMEN
function fntNewClientePrestamo()
{   
    if(document.querySelector("#formCliente"))
    {
        $('#modalFormCliente').modal('show');

        let formCliente = document.querySelector("#formCliente");
        formCliente.onsubmit = function(e)
        {
            e.preventDefault();
            let strIdentificacion = document.querySelector('#txtIdentificacion').value;
            let strNombre = document.querySelector('#txtNombre').value;
            let strApellido = document.querySelector('#txtNegocio').value;
            let intTelefono = document.querySelector('#txtTelefono').value;
            let strDireccion1 = document.querySelector('#txtDireccion1').value;

            if(strIdentificacion == '' || strNombre == '' || strApellido == '' || intTelefono == '' || strDireccion1 == '')
            {
                Swal.fire("Atención", "Todos los campos son obligatorios.", "error");
                return false;
            }

            let ElementsValid = document.getElementsByClassName("valid");
            for (let i = 0; i < ElementsValid.length; i++) {
                if(ElementsValid[i].classList.contains('is-invalid')){
                    Swal.fire("Atencion!", "Por favor verifique los campos en rojo.", "error");
                    return false;
                }
            }

            fntRegistrarClientePrestamo();
        }
    }
        
}
//REGISTRAR CLIENTE
async function fntRegistrarClientePrestamo()
{
    divLoading.style.display = "flex";
    try {
        const data = new FormData(formCliente);
        let resp = await fetch(base_url + '/Clientes/setCliente', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: data
        });
        json = await resp.json();
        if(json.status) {
            $('#modalFormCliente').modal("hide");
            formCliente.reset();
            fntClientesPrestamo();

            //Swal.fire("Roles de usuario", json.msg ,"success");
            Toast.fire({
                icon: "success",
                title: json.msg
            });
        } else {
            Swal.fire("Error", json.msg , "error");
            /*Toast.fire({
                icon: "warning",
                title: json.msg
            });*/
            console.log(json.msg);
        }
    } catch (error) {
        Swal.fire("Error", "La sesión expiró, recarga la página para entrar nuevamente" , "error");
        /*Toast.fire({
            icon: "error",
            title: "Ocurrió un error interno"
        });*/
        console.log(error);
    }
    divLoading.style.display = "none";
    return false;
}