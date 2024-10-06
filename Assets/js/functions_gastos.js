let tableGastos;
let divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp() {
    fntTableGastos();
    fntNewGasto();
}

//TABLA DE LOS GASTOS
function fntTableGastos()
{
    tableGastos = $('#tableGastos').DataTable( 
    {
        "aProcessing":true,
        "aServerSide":true,
        "language": {
            //"url": "https://cdn.datatables.net/plug-ins/2.1.7/i18n/es-CO.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "ajax":{
            "url": " "+base_url+"/Gastos/getGastos",
            "dataSrc":""
        },
        "columns":[
            {"data":"datecreated"},
            {"data":"nombre"},
            {"data":"valor"},
            {"data":"options"}
        ],
        
        "responsive":"true",
        "bDestroy": true,
        "iDisplayLength": 50,
        "order":[[0,"desc"]]  
    });
}

//VISTA PARA CREAR GASTO
function fntNewGasto()
{
    if(document.querySelector("#formGasto")){
        let formGasto = document.querySelector("#formGasto");
        formGasto.onsubmit = function(e)
        {
            e.preventDefault();
            let strNombre = document.querySelector('#txtNombre').value;
            let strValor = document.querySelector('#txtValor').value;

            if(strNombre === '' || strValor === '')
            {
                swal("Atención", "Todos los campos son obligatorios.", "error");
                return false;
            }

            let ElementsValid = document.getElementsByClassName("valid");
            for (let i = 0; i < ElementsValid.length; i++) {
                if(ElementsValid[i].classList.contains('is-invalid')){
                    swal("Atención!", "Por favor verifique los campos en rojo.", "error");
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
            tableGastos.ajax.reload(null, false);
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

function openModal()
{
    document.querySelector('#idGasto').value ="";
    document.querySelector("#formGasto").reset();
    $('#modalFormGastos').modal('show');
}
