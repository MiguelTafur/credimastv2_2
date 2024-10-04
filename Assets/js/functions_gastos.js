let tableGastos;
let divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp() {
    fntTableGastos();
}

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
