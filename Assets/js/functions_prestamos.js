
let tablePrestamos;
let divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp() {
    fntTablePrestamos();
    fntNewPrestamo();
}

function fntTablePrestamos()
{
    tablePrestamos = $('#tablePrestamos').DataTable( 
    {
        "aProcessing":true,
        "aServerSide":true,
        "language": {
            // "url": "https://cdn.datatables.net/plug-ins/2.1.7/i18n/es-CO.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "ajax":{
            "url": " "+base_url+"/Prestamos/getPrestamos",
            "dataSrc":""
        },
        "columns":[
            {
                "className": "dt-start",
                "orderable": "false",
                "data": "null",
                "defaultContent": "",
                "render": function(){
                    return '<i class="bi bi-eye fs-3" role="button"></i>'
                }
            },
            {"data":"cliente"},
            {"data":"pagamento"},
            {"data":"options"}
        ],
        /*"columnDefs": [
            {"className": "dt-center", "targets": "_all"}
        ],*/
        
        "responsive":"true",
        "bDestroy": true,
        "iDisplayLength": 20 
    });

    function format(d)
    {
        return '<table>' +
            '<tr>' +
                '<td>Cliente:<td>' +
                '<td>' + d.cliente + '<td>';
    }

    tablePrestamos.on('click', 'td.dt-start', function () {
        var tr = $(this).parents('tr');
        var tdi = tr.find("i.bi");
        var row = tablePrestamos.row(tr);
     
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('show');
            tdi.first().removeClass("bi-eye-slash");
            tdi.first().addClass("bi-eye");
        }
        else {
            // Open this row (the format() function would return the data to be shown)
            row.child(format(row.data())).show();
            tr.addClass('show');
            tdi.first().removeClass("bi-eye");
            tdi.first().addClass("bi-eye-slash");
        }
    });
}

//TRAER  TODOS LOS CLIENTES EN EL SELECT
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
                    }
                });
            }
        }
    }
}

//REGISTRAR EL PRÉSTAMO
function fntNewPrestamo()
{
    if(document.querySelector("#formPrestamos"))
    {
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
}

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
            tablePrestamos.ajax.reload(null, false);
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

//REGISTRAR CLIENTE EN LA SECCIÓN PRESTAMOS
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
            let strApellido = document.querySelector('#txtApellido').value;
            let intTelefono = document.querySelector('#txtTelefono').value;
            let strDireccion1 = document.querySelector('#txtDireccion1').value;
            let strDireccion2 = document.querySelector('#txtDireccion2').value;

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

function openModal()
{
    //document.querySelector("#divPrestamosFinalizados").classList.add("d-none");
    //document.querySelector("#divNuevoPrestamo").classList.remove("d-none"); 
    //document.querySelector("#divViewResumen").classList.add('d-none');
    //document.querySelector("#btnPayAll").classList.add('d-none');
    /*if(document.querySelector("#divTablasPrestamos"))
    {
        document.querySelector("#divTablasPrestamos").classList.add('d-none');
    }
    if(document.querySelector("#resumenPendiente"))
    {
        document.querySelector("#resumenPendiente").classList.add('d-none');   
    }*/

    fntClientesPrestamo();

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

    $('#modalFormPrestamo').modal('show');
}