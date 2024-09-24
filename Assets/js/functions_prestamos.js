
let tablePrestamos;
let divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp() {
    fntTablePrestamos();
    //fntNewPrestamo();
}

function fntTablePrestamos()
{
    tablePrestamos = $('#tablePrestamos').DataTable( 
    {
        "aProcessing":true,
        "aServerSide":true,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/2.1.7/i18n/es-CO.json"
            // "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "ajax":{
            "url": " "+base_url+"/Prestamos/getPrestamos",
            "dataSrc":""
        },
        "columns":[
            {"data":"cliente"},
            {"data":"pagamento"},
            {"data":"options"}
        ],
        
        "responsive":"true",
        "bDestroy": true,
        "iDisplayLength": 20,
        "order":[[0,"desc"]]  
    });
}