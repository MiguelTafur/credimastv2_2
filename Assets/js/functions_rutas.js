let tableRutas;
let divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function()
{
    iniciarApp();
});

function iniciarApp() 
{
    fntTableRutas();
    fntNewRuta();
}

function fntTableRutas() 
{
    tableRutas = $('#tableRutas').DataTable( 
    {
        "aProcessing":true,
        "aServerSide":true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax":{
            "url": " "+base_url+"/Rutas/getRutas",
            "dataSrc":""
        },
        "columns":[
            {"data":"nombre"},
            {"data":"codigo"},
            {"data":"pagamento"},
            {"data":"options"}
        ],
        
        "responsive":"true",
        "bDestroy": true,
        "iDisplayLength": 20,
        "order":[[2,"desc"]]  
    });
}

function fntNewRuta()
{
    if(document.querySelector("#formRuta")){
        let formRuta = document.querySelector("#formRuta");
        formRuta.onsubmit = function(e)
        {
            e.preventDefault();
            let strNombre = document.querySelector('#txtNombre').value;
            let strCodigo = document.querySelector('#txtCodigo').value;

            if(strNombre === '' || strCodigo === '')
            {
                swal("Atención", "Escribe un nombre.", "error");
                return false;
            }

            let ElementsValid = document.getElementsByClassName("valid");
            for (let i = 0; i < ElementsValid.length; i++) {
                if(ElementsValid[i].classList.contains('is-invalid')){
                    swal("Atención!", "Por favor verifique los campos en rojo.", "error");
                    return false;
                }
            }

            fntGuardarRuta();
        }
    }
}

async function fntGuardarRuta()
{
    divLoading.style.display = "flex";
    try {
        const data = new FormData(formRuta);
        let resp = await fetch(base_url + '/Rutas/setRutas', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: data
        });
        json = await resp.json();
        if(json.status) {
            tableRutas.ajax.reload(null, false);
            $('#modalFormRutas').modal("hide");
            formRuta.reset();
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

async function fntEditInfo(idruta)
{
    document.querySelector('#titleModal').innerHTML = "Actualizar Ruta";
    document.querySelector('#btnText').innerHTML = "Actualizar";

    divLoading.style.display = "flex";
    try {
        const formData = new FormData();
        formData.append('idRuta', idruta);
        let resp = await fetch(base_url+'/Rutas/getRuta', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            document.querySelector("#idRuta").value = json.data.idruta;
            document.querySelector("#txtCodigo").value = json.data.codigo;
            document.querySelector("#txtNombre").value = json.data.nombre;
            $('#modalFormRutas').modal('show');
        }else{
            Swal.fire("Error", "Ocurrió un error en el Servidor" , "error");
            /*Toast.fire({
                icon: "error",
                title: "Ocurrió un error interno"
            });*/
            console.log(error);
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

function fntDelInfo(idruta)
{
    Swal.fire({
        title: "Eliminar Ruta",
        text: "¿Realmente quiere eliminar la Ruta?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d9a300",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
    }).then((result) => {
        if (result.isConfirmed) {
            fntEliminarRuta(idruta);
        }
    });
}

async function fntEliminarRuta(idruta)
{
    const formData = new FormData();
    formData.append('idRuta', idruta);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Rutas/delRuta', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status)
        {
            //Swal.fire("Eliminar!", json.msg , "success");
            Toast.fire({
                icon: "success",
                title: json.msg
            });
            tableRutas.ajax.reload(null, false);
        }else{
            Swal.fire("Error", "Ocurrió un error en el Servidor" , "error");
            /*Toast.fire({
                icon: "error",
                title: "Ocurrió un error en el Servidor"
            });*/
            console.log(error);
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

function openModal()
{
    document.querySelector('#idRuta').value ="";
    document.querySelector("#formRuta").reset();
    $('#modalFormRutas').modal('show');
}