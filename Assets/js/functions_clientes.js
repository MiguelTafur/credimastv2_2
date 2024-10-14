
let tableClientes;
let divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp() {
    fntTableClientes();
    fntNewCliente();
}

//MOSTRAR DATEPICKER EN EL BUSCADOR
$('.date-picker').datepicker( {
    closeText: 'Cerrar',
    prevText: '<Ant',
    nextText: 'Sig>',
    currentText: 'Hoy',
    monthNames: ['1 -', '2 -', '3 -', '4 -', '5 -', '6 -', '7 -', '8 -', '9 -', '10 -', '11 -', '12 -'],
    monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
    changeMonth: true,
    changeYear: true,
    showButtonPanel: true,
    dateFormat: 'MM yy',
    showDays: false,
    onClose: function(dateText, inst) {
        $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
    }
});

function fntTableClientes()
{
    tableClientes = $('#tableClientes').DataTable( 
    {
        "aProcessing":true,
        "aServerSide":true,
        "language": {
            //"url": "https://cdn.datatables.net/plug-ins/2.1.7/i18n/es-CO.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "ajax":{
            "url": " "+base_url+"/Clientes/getClientes",
            "dataSrc":""
        },
        "columns":[
            {"data":"nombres"},
            {"data":"apellidos"},
            {"data":"telefono"},
            {"data":"options"}
        ],
        
        "responsive":"true",
        "bDestroy": true,
        "iDisplayLength": 50 
    });
}

//VALIDANDO LA INFORMACIÓN PARA REGISTRAR UN CLIENTE
function fntNewCliente()
{
    if(document.querySelector("#formCliente")){
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

            fntGuardarCliente();
        }
    }
}
//REGISTRAR CLIENTE
async function fntGuardarCliente()
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
            tableClientes.ajax.reload(null, false);
            $('#modalFormCliente').modal("hide");
            formCliente.reset();
            $("#graficaMesClientes").html(json.graficaMes);
            $("#graficaAnioClientes").html(json.graficaAnio);
            //Swal.fire("Roles de usuario", json.msg ,"success");
            Toast.fire({
                icon: "success",
                title: json.msg
            });
        } else {
            Swal.fire("Error", json.msg, "error");
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

//INFORMACIÓN CLIENTE
async function fntViewInfo(idpersona)
{
    const formData = new FormData();
    formData.append('idPersona', idpersona);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Clientes/getCliente', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            document.querySelector("#celIdentificacion").innerHTML = json.data.identificacion;
            document.querySelector("#celNombres").innerHTML = json.data.nombres;
            document.querySelector("#celApellidos").innerHTML = json.data.apellidos;
            document.querySelector("#celTelefono").innerHTML = json.data.telefono;
            document.querySelector("#celDireccion1").innerHTML = json.data.direccion1;
            document.querySelector("#celDireccion2").innerHTML = json.data.direccion2;
            document.querySelector("#celFechaRegistro").innerHTML = json.data.fechaRegistro;
            
            if(json.data.prestamos === undefined)
            {
                document.querySelector("#celPrestamos").innerHTML = 0;
            } else {
                document.querySelector("#celPrestamos").innerHTML = json.data.prestamos;
            }

            $('#modalViewCliente').modal('show');
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

//EDITAR CLIENTE
async function fntEditInfo(idpersona)
{
    document.querySelector('#titleModal').innerHTML = "Actualizar Cliente";
    document.querySelector('#btnText').innerHTML = "Actualizar";

    const formData = new FormData();
    formData.append('idPersona', idpersona);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Clientes/getCliente', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status)
        {
            document.querySelector("#idCliente").value = json.data.idpersona;
            document.querySelector("#txtIdentificacion").value = json.data.identificacion;
            document.querySelector("#txtNombre").value = json.data.nombres;
            document.querySelector("#txtNegocio").value = json.data.apellidos;
            document.querySelector("#txtTelefono").value = json.data.telefono;
            document.querySelector("#txtDireccion1").value = json.data.direccion1;
            document.querySelector("#txtDireccion2").value = json.data.direccion2;
            
            $('#modalFormCliente').modal('show');
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

//ALERTA PARA ELIMINAR CLIENTE
function fntDelInfo(idpersona)
{
    Swal.fire({
        title: "Eliminar Cliente",
        text: "¿Realmente quiere eliminar el Cliente?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d9a300",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
    }).then((result) => {
    if (result.isConfirmed) {
        fntDeleteCliente(idpersona);
    }
    });
}
//ELIMINAR CLIENTE
async function fntDeleteCliente(idpersona)
{
    const formData = new FormData();
    formData.append('idPersona', idpersona);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Clientes/delCliente', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            //Swal.fire("Eliminar!", json.msg , "success");
            tableClientes.ajax.reload(null, false);
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

/** GRÁFICA **/
//INFORMACIÓN DE CADA PUNTO DE LA GRÁGICA DEL CLIENTE
async function fntInfoChartPersona(fecha) 
{
    let fechaFormateada = fecha.join("-");

    const formData = new FormData();
    formData.append('fecha', fechaFormateada);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Clientes/getDatosGraficaPersona', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            
            let tdAnotaciones = json.data;
            let fecha = json.fecha;
            
            document.querySelector("#listgraficaPersona").innerHTML = tdAnotaciones;
            document.querySelector("#datePersonaGrafica").textContent = fecha;

            $('#modalViewPersonaGrafica').modal('show');
        }else{
            // Swal.fire("Error", json.msg, "error");
            Toast.fire({
                icon: "error",
                title: "Sin datos"
            });
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

//BUSCADOR MENSUAL
async function fntSearchClientesMes()
{
    let fecha = document.querySelector(".clientesMes").value;
    if(fecha == "")
    {
        Swal.fire("", "Selecione el mes y el año", "error");
        return false;
    }

    const formData = new FormData();
    formData.append('fecha', fecha);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Clientes/clientesMes', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.text();
    
        $("#graficaMesClientes").html(json);
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

//GRÁFICA ANUAL
async function fntSearchClientesAnio(){
    let anio = document.querySelector(".clientesAnio").value;
    if(anio == ""){
        swal("", "Digite el Año" , "error");
        return false;
    }else{

        const formData = new FormData();
        formData.append('anio', anio);

        divLoading.style.display = "flex";
        try {
            let resp = await fetch(base_url+'/Clientes/clientesAnio', {
                method: 'POST',
                mode: 'cors',
                cache: 'no-cache',
                body: formData
            });
        
            json = await resp.text();
        
            $("#graficaAnioClientes").html(json);
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
}

function openModal()
{
    document.querySelector('#idCliente').value ="";
    document.querySelector('#btnText').innerHTML ="Registrar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Cliente";
    document.querySelector("#formCliente").reset();
    $('#modalFormCliente').modal('show');
}