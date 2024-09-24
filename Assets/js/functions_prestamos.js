
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
            {
                "className": "dt-center",
                "orderable": "false",
                "data": "null",
                "defaultContent": "",
                "render": function(){
                    return '<i class="bi bi-eye fs-3" role="button"></i>'
                }
            },
            {"data":"cliente"},
            {"data":"pagamento"},
            {"data":"options"}
        ],
        /*"columnDefs": [
            {"className": "dt-center", "targets": "_all"}
        ],*/
        
        "responsive":"true",
        "bDestroy": true,
        "iDisplayLength": 20,
        "order":[[0,"desc"]]  
    });

    function format(d)
    {
        return '<table>' +
            '<tr>' +
                '<td>Cliente:<td>' +
                '<td>' + d.cliente + '<td>';
    }

    tablePrestamos.on('click', 'td.dt-center', function () {
        var tr = $(this).parents('tr');
        var tdi = tr.find("i.bi");
        var row = tablePrestamos.row(tr);
     
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('show');
            tdi.first().removeClass("bi-eye-slash");
            tdi.first().addClass("bi-eye");
        }
        else {
            // Open this row (the format() function would return the data to be shown)
            row.child(format(row.data())).show();
            tr.addClass('show');
            tdi.first().removeClass("bi-eye");
            tdi.first().addClass("bi-eye-slash");
        }
    });

}