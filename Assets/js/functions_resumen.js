let divLoading = document.querySelector("#divLoading");
let checkbox = document.querySelector("#diasSemanales");

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp() {
    fntNewResumen();
    fntBase();
}

//CONSULTA SI HAY UNA BASE REGISTRADA
async function fntBase()
{
    const formData = new FormData();
    formData.append('idRuta', ruta);
    divLoading.style.display = "flex";
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
//REGISTRAR BASE CON EL TOTAL DEL RESUMEN ANTERIOR
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
function fntNewVenta(prestamo, total)
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
        fntRegistrarPrestamo(prestamo, total);
    }
}
//REGISTRAR PRÉSTAMO
async function fntRegistrarPrestamo(prestamo, total)
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
            let monto = parseInt(formPrestamos.children[2].children[1].value);
            document.querySelector('#prestamoResumen').textContent = prestamo + monto;
            document.querySelector('#totalResumen').textContent = total - monto;
            document.querySelector('#idResumen').value = json.idresumen;
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
function fntNewGasto(gasto, total)
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

            fntRegistrarGasto(gasto, total);
        }
    }
}
//REGISTRAR GASTO  
async function fntRegistrarGasto(gasto, total)
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
            let monto = parseInt(formGasto.children[2].children[1].value);
            document.querySelector('#gastosResumen').textContent = gasto + monto;
            document.querySelector('#totalResumen').textContent = total - monto;
            document.querySelector('#idResumen').value = json.idresumen;
            $('#modalFormGastos').modal("hide");
            formGasto.reset();
            //Swal.fire("Roles de usuario", json.msg ,"success");
            Toast.fire({
                icon: "success",
                title: json.msg
            });
        } else {
            Swal.fire("Error", "Ocurrió un error en el Servidor" , "error");
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

//VISTA PARA CREAR LA BASE
function fntNewBase(total = null)
{
    $('#modalFormBase').modal('show');

    if(document.querySelector("#formBase")){
        let formBase = document.querySelector("#formBase");
        formBase.onsubmit = function(e)
        {
            e.preventDefault();

            let intValor = document.querySelector("#txtValor").value;

            if(intValor == '')
            {
                Swal.fire("Atención", "El valor es obligatorio.", "error");
                return false;
            }

            let ElementsValid = document.getElementsByClassName("valid");
            for (let i = 0; i < ElementsValid.length; i++) {
                if(ElementsValid[i].classList.contains('is-invalid')){
                    Swal.fire("Atención!", "Por favor verifique los campos en rojo.", "error");
                    return false;
                }
            }

            fntRegistrarBase(total);
        }
    }
}
//REGISTRAR BASE
async function fntRegistrarBase(total)
{
    divLoading.style.display = "flex";
    try {
        const data = new FormData(formBase);
        let resp = await fetch(base_url + '/Base/setBase', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: data
        });
        json = await resp.json();
        if(json.status) {
            if(!total){
                location.reload();
            } else {
                let monto = parseInt(formBase.children[1].children[1].value);
                document.querySelector('#baseResumen').textContent = monto;
                document.querySelector('#totalResumen').textContent = total + monto;
                document.querySelector('#idResumen').value = json.idresumen;
                $('#modalFormBase').modal("hide");
                formBase.reset();
                //Swal.fire("Roles de usuario", json.msg ,"success");
                Toast.fire({
                    icon: "success",
                    title: json.msg
                });
            }
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

//EDITAR BASE

