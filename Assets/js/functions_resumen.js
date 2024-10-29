let divLoading = document.querySelector("#divLoading");
let checkbox = document.querySelector("#diasSemanales");

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp() {
    fntNewResumen();
    fntBase();
    $(function () {
        $('[data-bs-toggle="popover"]').popover({
            container: "body",
            trigger: "focus",
            html: true
        });
    });

}

//CONSULTA SI HAY UNA BASE REGISTRADA
async function fntBase()
{
    const formData = new FormData();
    formData.append('idRuta', ruta);
    //divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Base/getBase', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(!json.base){
            let total = parseInt(document.querySelector("#totalResumen").firstChild.textContent);
            fntTotalUltimoResumenCerrado(total);
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
//TRAE EL TOTAL DEL ULTIMO RESUMEN CON ESTADO 1
async function fntTotalUltimoResumenCerrado(total)
{
    const formData = new FormData();
    formData.append('idRuta', ruta);
    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Resumen/getResumenUltimo', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            fntRegistrarBaseResumenAnterior(json.base, total);
        }else{
            Swal.fire("Atención!", json.msg, "warning");
            /*Toast.fire({
                icon: "warning",
                title: "Ocurrió un error en el Servidor"
            });*/
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
//REGISTRAR LA BASE CON EL TOTAL DEL RESUMEN ANTERIOR
async function fntRegistrarBaseResumenAnterior(base, total)
{
    divLoading.style.display = "flex";
    try {
        const formData = new FormData();
        formData.append('base', base);
        let resp = await fetch(base_url + '/Base/setBaseResumenAnterior', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
        json = await resp.json();
        if(json.status) {
            document.querySelector("#totalResumen").firstChild.textContent = base + total;
            document.querySelector("#baseResumen").firstChild.textContent = base;
            document.querySelector('#carteraResumen').textContent = json.carteraResumen;
            document.querySelector('#cajaResumen').textContent = json.cajaResumen;
            document.querySelector('#ultimosResumenes').innerHTML = json.ultimosResumenes;
            $(function () {
                $('[data-bs-toggle="popover"]').popover({
                    container: "body",
                    trigger: "focus",
                    html: true
                })
            });
        } else {
            Swal.fire("Error", json.msg , "error");
            /*Toast.fire({
                icon: "warning",
                title: "Ocurrió un error en el Servidor"
            });*/
            console.log(json.msg);
        }
    } catch (error) {
        Swal.fire("Error", "La sesión expiró, recarga la página para entrar nuevamente" , "error");
        /*Toast.fire({
            icon: "error",
            title: "Sua sesión expiró, recarga la página para entrar nuevamente"
        });*/
        console.log(error);
    }
    divLoading.style.display = "none";
    return false;
}

//OBTIENE EL ID DEL RESUMEN
function fntNewResumen()
{
    if(document.querySelector("#formResumen")){
        let formResumen = document.querySelector("#formResumen");
        formResumen.onsubmit = function(e)
        {
            e.preventDefault();
            let estado = document.querySelector('#status').value;
            let mensaje = estado == 1 ? 'Registrar' : 'Corregir';
            Swal.fire({
                title: mensaje + " Resumen",
                text: "¿Realmente quiere " + mensaje + " el Resumen?",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#d9a300",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, " + mensaje + "!",
                cancelButtonText: "No, cancelar!",
            }).then((result) => {
            if (result.isConfirmed) {
                fntRegistrarResumen();
            }
            });
        }
    }
}
//RESISTRAR RESUMEN
async function fntRegistrarResumen()
{
    divLoading.style.display = "flex";
    try {
        const data = new FormData(formResumen);
        let resp = await fetch(base_url + '/Resumen/setResumen', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: data
        });
        json = await resp.json();
        if(json.status) {
            Swal.fire({
                title: json.msg,
                text: '',
                icon: "success",
                confirmButtonColor: "#d9a300",
                confirmButtonText: "Continuar",
            }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
            });
        } else {
            Swal.fire("Atención!", json.msg, "warning");
            /*Toast.fire({
                icon: "warning",
                title: "Ocurrió un error en el Servidor"
            });*/
            console.log(json.msg);
        }
    } catch (error) {
        Swal.fire("Error", "La sesión expiró, recarga la página para entrar nuevamente" , "error");
        /*Toast.fire({
            icon: "error",
            title: "Sua sesión expiró, recarga la página para entrar nuevamente"
        });*/
        console.log(error);
    }
    divLoading.style.display = "none";
    return false;
}

//DATERANGEPICKER 
function fntViewDetalleResumen()
{
    $('#modalDetalleResumen').modal('show');
    $('#fechaResumen').daterangepicker({
        "autoUpdateInput": false,
        "locale": {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "applyLabel": "Aplicar",
            "cancelLabel": "Cancelar",
            "daysOfWeek": [
                "Dom",
                "Seg",
                "Ter",
                "Qua",
                "Qui",
                "Sex",
                "Sab"
            ],
            "monthNames": [
                "Janeiro",
                "Fevereiro",
                "Março",
                "Abil",
                "Maio",
                "Junho",
                "Julho",
                "Agosto",
                "Setembro",
                "Outubro",
                "Novembro",
                "Dezembro"
            ],
            "firstDay": 1
        }
    });

    $('#fechaResumen').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });

    $('#fechaResumen').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
}
//INFORMACIÓN DEL BUSCADOR DATERANGEPICKER
async function fntSearchResumenD()
{
    let fecha = document.querySelector("#fechaResumen").value;
    if(fecha == "")
    {
        Swal.fire("Error", "Seleccione la fecha", "error");
        return false;
    }

    const formData = new FormData();
    formData.append('fecha', fecha);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Resumen/getResumenD', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        arrResumen = json.resumenD;

        $(function () {
            $('[data-bs-toggle="popover"]').popover({
                container: "body",
                trigger: "focus",
                html: true
            })
        });

        document.querySelector("#datosResumenD").innerHTML = arrResumen;
        document.querySelector("#divResumenD").classList.remove("d-none");
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
    if(document.querySelector("#formPrestamos"))
    {
        document.querySelector('#idPrestamo').value ="";  
        document.querySelector('#titleModal').innerHTML = "Nuevo Préstamo";
        document.querySelector('#btnText').innerHTML = "Registrar"; 
        document.querySelector("#formPrestamos").reset();

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
            $('#listClientes').select2({
                disabled: false
            });
            fntRegistrarPrestamo();
        }
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
            document.querySelector('#prestamoResumen').textContent = json.resumen.ventas;
            document.querySelector('#totalResumen').textContent = json.resumen.total;
            document.querySelector('#idResumen').value = json.resumen.idresumen;
            document.querySelector('#carteraResumen').textContent = json.carteraResumen;
            document.querySelector('#cajaResumen').textContent = json.cajaResumen;
            document.querySelector('#ultimosResumenes').innerHTML = json.ultimosResumenes;
            $(function () {
                $('[data-bs-toggle="popover"]').popover({
                    container: "body",
                    trigger: "focus",
                    html: true
                })
            });
            $('#modalFormPrestamo').modal("hide");
            formPrestamos.reset();
            $('#listClientes').val(null).trigger('change');
            $('#listFormato').val(null).trigger('change');
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

//VISTA PARA CREAR GASTO
function fntNewGasto()
{
    $('#modalFormGastos').modal('show');

    if(document.querySelector("#formGasto")){
        let formGasto = document.querySelector("#formGasto");
        formGasto.onsubmit = function(e)
        {
            e.preventDefault();
            let strNombre = formGasto.children[1].children[1].value;
            let strValor = formGasto.children[2].children[1].value;

            if(strNombre == '' || strValor == '')
            {
                Swal.fire("Atención", "Todos los campos son obligatorios.", "error");
                return false;
            }

            let ElementsValid = document.getElementsByClassName("valid");
            for (let i = 0; i < ElementsValid.length; i++) {
                if(ElementsValid[i].classList.contains('is-invalid')){
                    Swal.fire("Atención!", "Por favor verifique los campos en rojo.", "error");
                    return false;
                }
            }

            fntRegistrarGasto();
        }
    }
}
//REGISTRAR GASTO  
async function fntRegistrarGasto()
{ 
    divLoading.style.display = "flex";
    try {
        const data = new FormData(formGasto);
        let resp = await fetch(base_url + '/Gastos/setGastos', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: data
        });
        json = await resp.json();
        if(json.status) {
            document.querySelector('#gastosResumen').textContent = json.resumen.gastos;
            document.querySelector('#totalResumen').textContent = json.resumen.total;
            document.querySelector('#idResumen').value = json.resumen.idresumen;
            document.querySelector('#carteraResumen').textContent = json.carteraResumen;
            document.querySelector('#cajaResumen').textContent = json.cajaResumen;
            document.querySelector('#ultimosResumenes').innerHTML = json.ultimosResumenes;
            $(function () {
                $('[data-bs-toggle="popover"]').popover({
                    container: "body",
                    trigger: "focus",
                    html: true
                })
            });
            $('#modalFormGastos').modal("hide");
            formGasto.reset();
            //Swal.fire("Roles de usuario", json.msg ,"success");
            Toast.fire({
                icon: "success",
                title: json.msg
            });
        } else {
            Swal.fire("Error", json.msg, "error");
            /*Toast.fire({
                icon: "warning",
                title: "Ocurrió un error en el Servidor"
            });*/
            console.log(json.msg);
        }
    } catch (error) {
        Swal.fire("Error", "La sesión expiró, recarga la página para entrar nuevamente" , "error");
        /*Toast.fire({
            icon: "error",
            title: "Sua sesión expiró, recarga la página para entrar nuevamente"
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

//VISTA PARA EDITAR LA BASE
function fntEditBase()
{
    $('#modalFormBase').modal('show');

    if(document.querySelector("#formBase")){
        let formBase = document.querySelector("#formBase");
        formBase.onsubmit = function(e)
        {
            e.preventDefault();

            let ElementsValid = document.getElementsByClassName("valid");
            for (let i = 0; i < ElementsValid.length; i++) {
                if(ElementsValid[i].classList.contains('is-invalid')){
                    Swal.fire("Atención!", "Por favor verifique los campos en rojo.", "error");
                    return false;
                }
            }

            fntEditarBase();
        }
    }
}
//ACTUALIZAR BASE
async function fntEditarBase()
{
    divLoading.style.display = "flex";
    try {
        const data = new FormData(formBase);
        let resp = await fetch(base_url + '/Base/setBaseUpdate', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: data
        });
        json = await resp.json();
        if(json.status) {
            Swal.fire({
                title: json.msg,
                text: '',
                icon: "success",
                confirmButtonColor: "#d9a300",
                confirmButtonText: "Continuar",
            }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
            });

        } else {
            Swal.fire("Error", json.msg , "error");
            /*Toast.fire({
                icon: "warning",
                title: "Ocurrió un error en el Servidor"
            });*/
            console.log(json.msg);
        }
    } catch (error) {
        Swal.fire("Error", "La sesión expiró, recarga la página para entrar nuevamente" , "error");
        /*Toast.fire({
            icon: "error",
            title: "Sua sesión expiró, recarga la página para entrar nuevamente"
        });*/
        console.log(error);
    }
    divLoading.style.display = "none";
    return false;
}

//VISTA PARA ELIMINAR LA BASE 
function fntDelBase(idbase)
{
    Swal.fire({
        title: " Base",
        text: "¿Realmente quiere la Base?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d9a300",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, Eliminar!",
        cancelButtonText: "No, cancelar!",
    }).then((result) => {
    if (result.isConfirmed) {
        fntElminarBase(idbase);
    }
    });
}
//ELIMINAR BASE
async function fntElminarBase(idbase)
{
    const formData = new FormData();
    formData.append('idBase', idbase);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Base/delBase', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            //Swal.fire("Eliminar!", json.msg , "success");
            Swal.fire({
                title: json.msg,
                text: '',
                icon: "success",
                confirmButtonColor: "#d9a300",
                confirmButtonText: "Continuar",
            }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
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

//VISTA PARA VER LA LISTA DE PRÉSTAMOS
async function fntViewPrestamos()
{
    const formData = new FormData();
    formData.append('idRuta', ruta);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Prestamos/getPrestamosFecha', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });

        json = await resp.json();

        let trPrestamo = '';
        json.forEach(function(prestamo) {
            trPrestamo += `
                <tr>
                    <td>${prestamo.cliente}</td>    
                    <td>${prestamo.monto}</td>
                    <td>${prestamo.hora}</td>
                    <td class="fst-italic">${prestamo.usuario}</td>
                </tr>`
            ;
        });
        if(trPrestamo){

            document.querySelector("#tbodyPrestamos").innerHTML = trPrestamo;
        } else {
            document.querySelector("#tbodyPrestamos").innerHTML = '<tr><td class="fst-italic" style="text-align: center;" colspan="2">Sin Préstamos</td></tr>';
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

//VISTA PARA VER LA LISTA DE GASTOS
async function fntViewGastos()
{
    const formData = new FormData();
    formData.append('idRuta', ruta);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Gastos/getGastosFecha', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });

        json = await resp.json();

        let trGasto = '';
        json.forEach(function(gasto) {
            trGasto += `
                <tr>
                    <td>${gasto.nombre}</td>    
                    <td>${gasto.monto}</td>
                    <td>${gasto.hora}</td>
                    <td class="fst-italic">${gasto.usuario}</td>
                </tr>`
            ;
        });
        if(trGasto){

            document.querySelector("#tbodyGastos").innerHTML = trGasto;
        } else {
            document.querySelector("#tbodyGastos").innerHTML = '<tr><td class="fst-italic" style="text-align: center;" colspan="4">Sin Gastos</td></tr>';
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

//VISTA PARA VER LA BASE
// async function fntViewBase()
// {
//     const formData = new FormData();
//     formData.append('idRuta', ruta);

//     divLoading.style.display = "flex";
//     try {
//         let resp = await fetch(base_url+'/Base/getBase', {
//             method: 'POST',
//             mode: 'cors',
//             cache: 'no-cache',
//             body: formData
//         });

//         json = await resp.json();

//         let trGasto = '';
//         json.forEach(function(gasto) {
//             trGasto += `
//                 <tr>
//                     <td>${gasto.nombre}</td>    
//                     <td>${gasto.monto}</td>
//                 </tr>`
//             ;
//         });
//         if(trGasto){

//             document.querySelector("#tbodyGastos").innerHTML = trGasto;
//         } else {
//             document.querySelector("#tbodyGastos").innerHTML = '<tr><td class="fst-italic" style="text-align: center;" colspan="2">Sin Gastos</td></tr>';
//         }
//     } catch (error) {
//         Swal.fire("Error", "La sesión expiró, recarga la página para entrar nuevamente" , "error");
//         /*Toast.fire({
//             icon: "error",
//             title: "Ocurrió un error interno"
//         });*/
//         console.log(error);
//     }
//     divLoading.style.display = "none";
//     return false;
//}


//EDITAR EL PRÉSTAMO
// async function fntEditInfo(idprestamo)
// {
//     document.querySelector('#titleModal').innerHTML = "Editar Préstamo";
//     document.querySelector('#btnText').innerHTML = "Actualizar";

//     const formData = new FormData();
//     formData.append('idPrestamo', idprestamo);

//     divLoading.style.display = "flex";
//     try {
//         let resp = await fetch(base_url + '/Prestamos/getPrestamo', {
//             method: 'POST',
//             mode: 'cors',
//             cache: 'no-cache',
//             body: formData
//         });
    
//         json = await resp.json();
    
//         if(json.status)
//         {
//             let cliente = json.data.nombres.toUpperCase() + ' - ' + json.data.apellidos;
//             let optionCliente = '<option value="' + json.data.personaid + '">' + cliente + '</option>';

//             document.querySelector("#idPrestamo").value = json.data.idprestamo;
//             document.querySelector("#listClientes").innerHTML = optionCliente;
//             document.querySelector('#txtMonto').value = json.data.monto;
//             document.querySelector('#txtTaza').value = json.data.taza;
//             document.querySelector('#txtPlazo').value = json.data.plazo;
//             document.querySelector('#listFormato').value = json.data.formato;

//             if(json.data.formato == 1) {
//                 checkbox.disabled = false;
//             } else if(json.data.formato == 2 || json.data.formato == 3) {
//                 checkbox.disabled = true;
//             }

//             $('#listClientes').select2({
//                 dropdownParent: $('#modalFormPrestamo'),
//                 placeholder: 'Seleccione un Formato',
//                 language: {
//                     noResults: function() {
//                         return "No hay resultado";        
//                     },
//                     searching: function() {
//                         return "Buscando...";
//                     }
//                 },
//                 disabled: true
//             });

//             $('#listFormato').select2({
//                 dropdownParent: $('#modalFormPrestamo'),
//                 language: {
//                     noResults: function() {
//                         return "No hay resultado";        
//                     },
//                     searching: function() {
//                         return "Buscando...";
//                     }
//                 }
//             });
            
//             $('#modalFormPrestamo').modal('show');
//         }else{
//             Swal.fire("Error", json.msg, "error");
//             /*Toast.fire({
//                 icon: "error",
//                 title: "Ocurrió un error interno"
//             });*/
//             console.log(json.msg);
//         }
//     } catch (error) {
//         Swal.fire("Error", "La sesión expiró, recarga la página para entrar nuevamente" , "error");
//         /*Toast.fire({
//             icon: "error",
//             title: "Ocurrió un error interno"
//         });*/
//         console.log(error);
//     }
//     divLoading.style.display = "none";
//     return false;
// }