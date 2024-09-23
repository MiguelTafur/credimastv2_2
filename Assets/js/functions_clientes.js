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
    tableClientes = $('#tableClientes').dataTable( 
    {
        "aProcessing":true,
        "aServerSide":true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
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
        "iDisplayLength": 20,
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

            if(strIdentificacion == '' || strNombre == '' || strApellido == '' || intTelefono == '' || strDireccion1 == '' || strDireccion2 == '')
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

            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Clientes/setCliente';
            let formData = new FormData(formCliente);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200)
                {
                    let json = JSON.parse(request.responseText);
                    if(json.status)
                    {
                        $('#modalFormCliente').modal("hide");
                        formCliente.reset();
                        Swal.fire("Clientes", json.msg, "success");
                        tableClientes.ajax.reload(null, false);
                        
                    }else{
                        swal("Error", json.msg, "error");
                    }
                }
                divLoading.style.display = "none";
                return false;
            }
        }
    }
}

function openModal()
{
    //document.querySelector('#idUsuario').value ="";
    //document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    //document.querySelector('#btnText').innerHTML ="Registrar";
    //document.querySelector('#titleModal').innerHTML = "Nuevo Cliente";
    //document.querySelector("#formCliente").reset();
    $('#modalFormCliente').modal('show');
}