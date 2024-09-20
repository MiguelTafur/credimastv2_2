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

            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Rutas/setRutas';
            let formData = new FormData(formRuta);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200)
                {
                    let json = JSON.parse(request.responseText);
                    if(json.status)
                    {
                        tableRutas.ajax.reload(null, false);
                        //Swal.fire("Roles de usuario", json.msg ,"success");
                        Toast.fire({
                            icon: "success",
                            title: json.msg
                        });
                        $('#modalFormRutas').modal("hide");
                        formRuta.reset();
                    }else{
                        Swal.fire("Error", json.msg , "error");
                        /*Toast.fire({
                            icon: "warning",
                            title: json.msg
                        });*/
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
    document.querySelector('#idRuta').value ="";
    document.querySelector("#formRuta").reset();
    $('#modalFormRutas').modal('show');
}