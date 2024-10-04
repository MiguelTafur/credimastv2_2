
let tableClientes;
let divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp() {
    fntTableClientes();
    fntNewCliente();
}

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
        "iDisplayLength": 50,
        "order":[[0,"desc"]]  
    });
}

function fntNewCliente()
{
    if(document.querySelector("#formCliente")){
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

            fntGuardarCliente();
        }
    }
}

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
            document.querySelector("#txtApellido").value = json.data.apellidos;
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

function openModal()
{
    document.querySelector('#idCliente').value ="";
    document.querySelector('#btnText').innerHTML ="Registrar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Cliente";
    document.querySelector("#formCliente").reset();
    $('#modalFormCliente').modal('show');
}