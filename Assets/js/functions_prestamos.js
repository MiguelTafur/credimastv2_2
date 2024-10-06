
let tablePrestamos;
let divLoading = document.querySelector("#divLoading");
let checkbox = document.querySelector("#diasSemanales");

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp() {
    fntTablePrestamos();
    fntNewPrestamo();
    //fntNewPagoPrestamo();
}

//TABLA DE LOS PRESTAMOS
function fntTablePrestamos()
{
    tablePrestamos = $('#tablePrestamos').DataTable( 
    {
        "aProcessing":"true",
        "aServerSide":"true",
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
        "bDestroy": "true",
        "iDisplayLength": 50,
        "order":[[2,"desc"]] 
    });

    function format(d)
    {
        let inicio = d.datecreatedFormat;
        let final = d.fechavenceFormat;
        let monto = d.monto;
        let taza = d.taza;
        let plazo = d.plazo;
        let total = monto+(monto*(taza*0.01));
        let parcela = (total / d.intPlazo);
        let pagado = d.pagado == null ? 0 : d.pagado;
        let saldo = d.saldo;
        let pendiente = d.pendiente;
        let cancelado = d.cancelado;

        return '<ul class="list-group">'+
            '<li class="list-group-item d-flex justify-content-center align-items-center active">'+
                'INFORMACIÓN DEL PRÉSTAMO'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-between align-items-center">'+
                'FECHA INICIO'+
                '<span class="badge text-bg-secondary rounded-pill">' + inicio + '</span>'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-between align-items-center">'+
                'FECHA VENCIMIENTO'+
                '<span class="badge text-bg-secondary rounded-pill">' + final + '</span>'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-between align-items-center">'+
                'CRÉDITO'+
                '<span class="badge text-bg-secondary rounded-pill">' + monto + '</span>'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-between align-items-center">'+
                'TASA DE INTERÉS'+
                '<span class="badge text-bg-secondary rounded-pill">' + taza + '%</span>'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-between align-items-center">'+
                'TOTAL A PAGAR'+
                '<span class="badge text-bg-secondary rounded-pill">' + total + '</span>'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-between align-items-center">'+
                'PLAZO'+
                '<span class="badge text-bg-secondary rounded-pill">' + plazo + '</span>'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-between align-items-center">'+
                'VALOR PARCELA'+
                '<span class="badge text-bg-secondary rounded-pill">' + parcela + '</span>'+
            '</li>'+
            '<li">'+
                '<hr class="border border-secondary border-2 opacity-75">'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-between align-items-center">'+
                'SALDO'+
                '<span class="badge text-bg-secondary rounded-pill">' + saldo + '</span>'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-between align-items-center">'+
                'PAGADO'+
                '<span class="badge text-bg-secondary rounded-pill">' + pagado + '</span>'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-between align-items-center">'+
                'PARCELAS PENDIENTES'+
                '<span class="badge text-bg-secondary rounded-pill">' + pendiente + '</span>'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-between align-items-center">'+
                'PARCELAS CANCELADAS'+
                '<span class="badge text-bg-secondary rounded-pill">' + cancelado + '</span>'+
            '</li>'+
        '</ul>';
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

//REGISTRAR EL PRÉSTAMO
function fntNewPrestamo()
{
    if(document.querySelector("#formPrestamos"))
    {
        $('#listFormato').on("change", function(e) {
            const options = e.target.value;
            if(options == 1) {
                checkbox.disabled = false;
            } else if(options == 2 || options == 3) {
                checkbox.disabled = true;
            }
        });
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
    
            $('#listClientes').select2({
                disabled: false
            });
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

// EDITAR EL PRÉSTAMO
async function fntEditInfo(idprestamo)
{
    document.querySelector('#titleModal').innerHTML = "Actualizar Préstamo";
    document.querySelector('#btnText').innerHTML = "Actualizar";

    const formData = new FormData();
    formData.append('idPrestamo', idprestamo);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url + '/Prestamos/getPrestamo', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status)
        {
            let cliente = json.data.nombres.toUpperCase() + ' - ' + json.data.apellidos;
            let optionCliente = '<option value="' + json.data.personaid + '">' + cliente + '</option>';

            document.querySelector("#idPrestamo").value = json.data.idprestamo;
            document.querySelector("#listClientes").innerHTML = optionCliente;
            document.querySelector('#txtMonto').value = json.data.monto;
            document.querySelector('#txtTaza').value = json.data.taza;
            document.querySelector('#txtPlazo').value = json.data.plazo;
            document.querySelector('#listFormato').value = json.data.formato;

            if(json.data.formato == 1) {
                checkbox.disabled = false;
            } else if(json.data.formato == 2 || json.data.formato == 3) {
                checkbox.disabled = true;
            }

            $('#listClientes').select2({
                dropdownParent: $('#modalFormPrestamo'),
                placeholder: 'Seleccione un Formato',
                language: {
                    noResults: function() {
                        return "No hay resultado";        
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                },
                disabled: true
            });

            $('#listFormato').select2({
                dropdownParent: $('#modalFormPrestamo'),
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
        }else{
            Swal.fire("Error", json.msg, "error");
            /*Toast.fire({
                icon: "error",
                title: "Ocurrió un error interno"
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
            let strApellido = document.querySelector('#txtNegocio').value;
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

//CREA EL PAGO
function fntNewPagoPrestamo(idprestamo)
{
    let pagoPrestamo = document.querySelector('#txtPagoPrestamo-'+idprestamo).value;

    if(pagoPrestamo == ""){
        Swal.fire("Atención!", "Debes ingresar un valor.", "error");
        return false;
    }

    let ElementsValid = document.getElementsByClassName("valid");
    for (let i = 0; i < ElementsValid.length; i++) {
        if(ElementsValid[i].classList.contains('is-invalid')){
            Swal.fire("Atencion!", "Por favor verifique los campos en rojo.", "error");
            return false;
        }
    }

    fntRegistrarPagoPrestamo(idprestamo, pagoPrestamo);    
}
async function fntRegistrarPagoPrestamo(idprestamo, pagoprestamo)
{
    const formData = new FormData();
    formData.append('idPrestamo', idprestamo);
    formData.append('pagoPrestamo', pagoprestamo);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Pagos/setPago', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            //Swal.fire("Eliminar!", json.msg , "success");
            tablePrestamos.ajax.reload(null, false);
            Toast.fire({
                icon: "success",
                title: json.msg
            });
        }else{
            Swal.fire("Error", json.msg, "error");
            /*Toast.fire({
                icon: "error",
                title: "Ocurrió un error"
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

//ELIMINAR PRÉSTAMOS
function fntDelInfo(idpersona)
{
    Swal.fire({
        title: "Eliminar Préstamo",
        text: "¿Realmente quiere eliminar el Préstamo?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d9a300",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
    }).then((result) => {
    if (result.isConfirmed) {
        fntDeletePrestamo(idpersona);
    }
    });
}

async function fntDeletePrestamo(idprestamo)
{
    const formData = new FormData();
    formData.append('idPrestamo', idprestamo);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Prestamos/delPrestamo', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            if(json.statusAnterior)
            {
                Swal.fire({
                    title: json.msg,
                    text: 'El resumen ha sido eliminado debido a que no contiene más datos',
                    icon: "warning",
                    confirmButtonColor: "#d9a300",
                    confirmButtonText: "Continuar",
                }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
                });
            } else {
                //Swal.fire("Eliminar!", json.msg , "success");
                tablePrestamos.ajax.reload(null, false);
                Toast.fire({
                    icon: "success",
                    title: json.msg
                });
            }
            
        }else{
            Swal.fire("Error", json.msg, "error");
            /*Toast.fire({
                icon: "error",
                title: "Ocurrió un error"
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

//MUESTRA UN ARRAY DE PAGAMENTOS 
async function fntViewPagamentos(idprestamo)
{
    const formData = new FormData();
    formData.append('idPrestamo', idprestamo);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Pagos/getPagos', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            document.querySelector("#tbodyPagamentos").innerHTML = json.pagos;
        }else{
            document.querySelector("#tbodyPagamentos").innerHTML = '<tr><td class="fst-italic" style="text-align: center;" colspan="3">Sin pagamentos</td><tr>';
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

//ELIMINAR PAGAMENTO
function fntDelInfoPago(idpago, idprestamo)
{
    Swal.fire({
        title: "Eliminar pago",
        text: "¿Realmente quiere eliminar el pago?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d9a300",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
    }).then((result) => {
    if (result.isConfirmed) {
        fntDeletePago(idpago, idprestamo);
    }
    });
}
async function fntDeletePago(idpago, idprestamo)
{
    const formData = new FormData();
    formData.append('idPago', idpago);
    formData.append('idPrestamo', idprestamo);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Pagos/delPago', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            if(json.statusAnterior)
            {
                Swal.fire({
                    title: json.msg,
                    text: 'El resumen ha sido eliminado debido a que no contiene más datos',
                    icon: "warning",
                    confirmButtonColor: "#d9a300",
                    confirmButtonText: "Continuar",
                }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
                });
            } else {
                //Swal.fire("Eliminar!", json.msg , "success");
                tablePrestamos.ajax.reload(null, false);
                Toast.fire({
                    icon: "success",
                    title: json.msg
                });
            }
        }else{
            Swal.fire("Error", json.msg, "error");
            /*Toast.fire({
                icon: "error",
                title: "Ocurrió un error"
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
    document.querySelector('#idPrestamo').value ="";  
    document.querySelector('#titleModal').innerHTML = "Nuevo Préstamo";
    document.querySelector('#btnText').innerHTML = "Registrar"; 
    document.querySelector("#formPrestamos").reset();

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

async function accion()
{
    const formData = new FormData();
    formData.append('idRuta', ruta);

    //divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Prestamos/accion', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            Toast.fire({
                icon: "success",
                title: json.msg
            });
            
        }else{
            Swal.fire("Error", json.msg, "error");
            /*Toast.fire({
                icon: "error",
                title: "Ocurrió un error"
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

async function accionPrestamos()
{
    const formData = new FormData();
    formData.append('idRuta', ruta);

    //divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Prestamos/accionPrestamos', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            Toast.fire({
                icon: "success",
                title: json.msg
            });
            
        }else{
            Swal.fire("Error", json.msg, "error");
            /*Toast.fire({
                icon: "error",
                title: "Ocurrió un error"
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

async function accionPrestamosUsuario()
{
    const formData = new FormData();
    formData.append('idRuta', ruta);

    //divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Prestamos/accionPrestamosUsuario', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            Toast.fire({
                icon: "success",
                title: json.msg
            });
            
        }else{
            Swal.fire("Error", json.msg, "error");
            /*Toast.fire({
                icon: "error",
                title: "Ocurrió un error"
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