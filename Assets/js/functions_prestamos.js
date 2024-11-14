
let tablePrestamos;
let divLoading = document.querySelector("#divLoading");
let checkbox = document.querySelector("#diasSemanales");

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp() {
    fntTablePrestamos();
    fntNewPrestamo();
    $(function () {
        $('[data-bs-toggle="popover"]').popover({
            container: "body",
            trigger: "focus",
            html: true
        })
    });
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

//TABLA DE LOS PRESTAMOS
function fntTablePrestamos()
{
    tablePrestamos = $('#tablePrestamos').DataTable( 
    {
        "aProcessing":"true",
        "aServerSide":"true",
        "language": {
            // "url": "https://cdn.datatables.net/plug-ins/2.1.7/i18n/es-CO.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "ajax":{
            "url": " "+base_url+"/Prestamos/getPrestamos",
            "dataSrc":""
        },
        "columns":[
            {
                "className": "dt-start",
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
        "bDestroy": "true",
        "iDisplayLength": 50,
        "order":[[2,"desc"]] 
    });

    function format(d)
    {
        let inicio = d.datecreatedFormat;
        let final = d.fechavenceFormat;
        let monto = d.monto;
        let taza = d.taza;
        let plazo = d.plazo;
        let total = monto+(monto*(taza*0.01));
        let parcela = (total / d.intPlazo);
        let pagado = d.pagado == null ? 0 : d.pagado;
        let saldo = d.saldo;
        let pendiente = d.pendiente;
        let cancelado = d.cancelado;
        let formato = d.formato;
        let dia = '';
        if(formato == 2) {
            dia = '<li class="list-group-item d-flex justify-content-center align-items-center">'+
                'DIA DE PAGO'+
                '<span class="badge text-bg-secondary rounded-pill ms-2">' + d.diaPago.toUpperCase() + '</span>'+
            '</li>';
        } else {
            dia = '';
        }

        return '<ul class="list-group">'+
            '<li class="list-group-item d-flex justify-content-center align-items-center active">'+
                'INFORMACIÓN DEL PRÉSTAMO'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-center align-items-center">'+
                'SALDO'+
                '<span class="badge text-bg-danger rounded-pill ms-2">' + saldo + '</span>'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-center align-items-center">'+
                'PAGADO'+
                '<span class="badge text-bg-success rounded-pill ms-2">' + pagado + '</span>'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-center align-items-center">'+
                'PARCELAS PENDIENTES'+
                '<span class="badge text-bg-secondary rounded-pill ms-2">' + pendiente + '</span>'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-center align-items-center">'+
                'PARCELAS CANCELADAS'+
                '<span class="badge text-bg-secondary rounded-pill ms-2">' + cancelado + '</span>'+
            '</li>'+
            '<li">'+
                '<hr class="border border-secondary border-2 opacity-75">'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-center align-items-center">'+
                'FECHA INICIO'+
                '<span class="badge text-bg-secondary rounded-pill ms-2">' + inicio + '</span>'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-center align-items-center">'+
                'FECHA VENCIMIENTO'+
                '<span class="badge text-bg-secondary rounded-pill ms-2">' + final + '</span>'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-center align-items-center">'+
                'CRÉDITO'+
                '<span class="badge text-bg-secondary rounded-pill ms-2">' + monto + '</span>'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-center align-items-center">'+
                'TASA DE INTERÉS'+
                '<span class="badge text-bg-secondary rounded-pill ms-2">' + taza + '%</span>'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-center align-items-center">'+
                'TOTAL A PAGAR'+
                '<span class="badge text-bg-secondary rounded-pill ms-2">' + total + '</span>'+
            '</li>'+
            '<li class="list-group-item d-flex justify-content-center align-items-center">'+
                'PLAZO'+
                '<span class="badge text-bg-secondary rounded-pill ms-2">' + plazo + '</span>'+
            '</li>'+
            dia +
            '<li class="list-group-item d-flex justify-content-center align-items-center">'+
                'VALOR PARCELA'+
                '<span class="badge text-bg-secondary rounded-pill ms-2">' + parcela + '</span>'+
            '</li>'+

            
        '</ul>';
    }

    tablePrestamos.on('click', 'td.dt-start', function () {
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
    tablePrestamos.on("init.dt", function()
    {
        iconosPrestamos();
    });
}

function iconosPrestamos()
{
    for (let i = 0; i < tablePrestamos.rows().count(); i++)
    {
        let row = tablePrestamos.row(i);
        let fechaInicio = row.data().datecreated;
        let fechaFinal = row.data().datefinal;
        let vencimiento = row.data().diasVence;
        let id = row.data().idprestamo;

        //console.log(row.data());

        if(fechaInicio == fechaActual)
        {
            //$(row.node()).addClass("table-success");
            document.querySelector("#div-" + id).classList.remove("d-none");
            document.querySelector("#div-" + id).classList.add("text-success");
        }

        if(fechaFinal != null)
        {
            //$(row.node()).addClass("table-warning");
            document.querySelector("#div-" + id).classList.remove("d-none");
            document.querySelector("#div-" + id).classList.add("text-secondary");
        }

        if(vencimiento == false && fechaFinal == null)
        {
            //$(row.node()).addClass("table-warning");
            document.querySelector("#div-" + id).classList.remove("d-none");
            document.querySelector("#div-" + id).classList.add("text-warning");
        }
        if(vencimiento == "vencido" && fechaFinal == null)
        {
            //$(row.node()).addClass("table-danger");
            document.querySelector("#div-" + id).classList.remove("d-none");
            document.querySelector("#div-" + id).classList.add("text-danger");
        }
    }
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

//REGISTRAR EL PRÉSTAMO
function fntNewPrestamo()
{
    if(document.querySelector("#formPrestamos"))
    {
        $('#listFormato').on("change", function(e) {
            const options = e.target.value;
            if(options == 1) {
                checkbox.disabled = false;
            } else if(options == 2 || options == 3) {
                checkbox.disabled = true;
            }
        });
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
    
            $('#listClientes').select2({
                disabled: false
            });
            fntRegistrarPrestamo();
        }
    }
}
async function fntRegistrarPrestamo()
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
            
            tablePrestamos.ajax.reload(() => iconosPrestamos());
            $('#modalFormPrestamo').modal("hide");
            formPrestamos.reset();
            document.querySelector('#valorActivo').textContent = json.valorActivo;
            document.querySelector('#cobradoEstimado').textContent = json.cobradoEstimado;
            $('#listClientes').val(null).trigger('change');
            $('#listFormato').val(null).trigger('change');

            $("#graficaMesPrestamos").html(json.graficaMes);
            $("#graficaAnioPrestamos").html(json.graficaAnio);
            //Swal.fire("Préstamos", json.msg ,"success");
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

// EDITAR EL PRÉSTAMO
async function fntEditInfo(idprestamo, renovar)
{
    document.querySelector('#titleModal').innerHTML = renovar == 2 ? "Editar Préstamo" : "Renovar Préstamo";
    document.querySelector('#btnText').innerHTML = renovar == 2 ? "Actualizar" : "Renovar";
    document.querySelector('#btnClienteNuevo').classList.add("d-none");

    const formData = new FormData();
    formData.append('idPrestamo', idprestamo);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url + '/Prestamos/getPrestamo', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status)
        {
            let cliente = json.data.nombres.toUpperCase() + ' - ' + json.data.apellidos;
            let optionCliente = '<option value="' + json.data.personaid + '">' + cliente + '</option>';

            document.querySelector("#idPrestamo").value = renovar == 2 ? json.data.idprestamo : "";
            document.querySelector("#listClientes").innerHTML = optionCliente;
            document.querySelector('#txtMonto').value = json.data.monto;
            document.querySelector('#txtTaza').value = json.data.taza;
            document.querySelector('#txtPlazo').value = json.data.plazo;
            document.querySelector('#listFormato').value = json.data.formato;
            document.querySelector("#renovar").value = renovar;


            if(json.data.formato == 1) {
                checkbox.disabled = false;
            } else if(json.data.formato == 2 || json.data.formato == 3) {
                checkbox.disabled = true;
            }

            $('#listClientes').select2({
                dropdownParent: $('#modalFormPrestamo'),
                placeholder: 'Seleccione un Formato',
                language: {
                    noResults: function() {
                        return "No hay resultado";        
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                },
                disabled: true
            });

            $('#listFormato').select2({
                dropdownParent: $('#modalFormPrestamo'),
                language: {
                    noResults: function() {
                        return "No hay resultado";        
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                }
            });
            
            $('#modalFormPrestamo').modal('show');
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

//ELIMINAR PRÉSTAMOS
function fntDelInfo(idpersona)
{
    Swal.fire({
        title: "Eliminar Préstamo",
        text: "¿Realmente quiere eliminar el Préstamo?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d9a300",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
    }).then((result) => {
    if (result.isConfirmed) {
        fntDeletePrestamo(idpersona);
    }
    });
}

async function fntDeletePrestamo(idprestamo)
{
    const formData = new FormData();
    formData.append('idPrestamo', idprestamo);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Prestamos/delPrestamo', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            if(json.statusAnterior)
            {
                Swal.fire({
                    title: json.msg,
                    text: 'El resumen ha sido eliminado debido a que no contiene más datos',
                    icon: "warning",
                    confirmButtonColor: "#d9a300",
                    confirmButtonText: "Continuar",
                }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
                });
            } else {
                //Swal.fire("Eliminar!", json.msg , "success");
                tablePrestamos.ajax.reload(() => iconosPrestamos());
                document.querySelector('#valorActivo').textContent = json.valorActivo;
                document.querySelector('#cobradoEstimado').textContent = json.cobradoEstimado;
                $("#graficaMesPrestamos").html(json.graficaMes);
                $("#graficaAnioPrestamos").html(json.graficaAnio);
                Toast.fire({
                    icon: "success",
                    title: json.msg
                });
            }
            
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

//RENOVAR PRÉSTAMO
function fntRenovar(idprestamo)
{
    let renovar = 1;
    fntEditInfo(idprestamo, renovar);

}

//REGISTRAR CLIENTE EN LA SECCIÓN PRESTAMOS
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

            fntRegistrarClientePrestamo();
        }
    }
}
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

//VALIDACIÓN AL REGISTRAR PAGO
function fntNewPagoPrestamo(idprestamo)
{
    let pagoPrestamo = document.querySelector('#txtPagoPrestamo-'+idprestamo).value;

    if(pagoPrestamo == ""){
        Swal.fire("Atención!", "Debes ingresar un valor.", "error");
        return false;
    }

    let ElementsValid = document.getElementsByClassName("valid");
    for (let i = 0; i < ElementsValid.length; i++) {
        if(ElementsValid[i].classList.contains('is-invalid')){
            Swal.fire("Atencion!", "Por favor verifique los campos en rojo.", "error");
            return false;
        }
    }

    fntRegistrarPagoPrestamo(idprestamo, pagoPrestamo);    
}
//REGISTRAR PAGO
async function fntRegistrarPagoPrestamo(idprestamo, pagoprestamo)
{
    const formData = new FormData();
    formData.append('idPrestamo', idprestamo);
    formData.append('pagoPrestamo', pagoprestamo);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Pagos/setPago', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            //Swal.fire("Eliminar!", json.msg , "success");
            tablePrestamos.ajax.reload(() => iconosPrestamos());
            document.querySelector('#valorActivo').textContent = json.valorActivo;
            document.querySelector('#cobradoEstimado').textContent = json.cobradoEstimado;
            $("#graficaMesCobrado").html(json.graficaMes);
            $("#graficaAnioCobrado").html(json.graficaAnio);

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
//REGISTRAR VARIOS PAGOS
async function fntPayAll()
{
    let txtPago = document.getElementsByClassName("inputPago");
    for (let i = 0; i < txtPago.length; i++) {
        if(txtPago[i].value != ""){
            idpago = txtPago[i].id.split("-")[1];
            let pago = {
                'pago': txtPago[i].value,
                'id': idpago
            }

            let datos = JSON.stringify(pago);
    
            const formData = new FormData();
            formData.append('datos', datos);
    
            divLoading.style.display = "flex";
            try {
                let resp = await fetch(base_url+'/Pagos/setPayAll', {
                    method: 'POST',
                    mode: 'cors',
                    cache: 'no-cache',
                    body: formData
                });
            
                json = await resp.json();
            
                if(json.status){
                    //Swal.fire("Eliminar!", json.msg , "success");
                    document.querySelector('#valorActivo').textContent = json.valorActivo;
                    document.querySelector('#cobradoEstimado').textContent = json.cobradoEstimado;
                    $("#graficaMesCobrado").html(json.graficaMes);
                    $("#graficaAnioCobrado").html(json.graficaAnio);
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
        }
    }
    tablePrestamos.ajax.reload(() => iconosPrestamos());
}

//MUESTRA UN ARRAY DE PAGAMENTOS 
async function fntViewPagamentos(idprestamo)
{
    const formData = new FormData();
    formData.append('idPrestamo', idprestamo);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Pagos/getPagos', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            document.querySelector("#cptCliente").parentNode.classList.remove("d-none");
            document.querySelector("#tbodyPagamentos").innerHTML = json.pagos;
            document.querySelector("#cptCliente").innerHTML = json.cliente;
            $(function () {
                $('[data-bs-toggle="popover"]').popover({
                    container: "body",
                    trigger: "focus",
                    html: true
                })
            });

        }else{
            document.querySelector("#cptCliente").parentNode.classList.add("d-none");
            document.querySelector("#tbodyPagamentos").innerHTML = '<tr><td class="fst-italic" style="text-align: center;" colspan="4">Sin pagamentos</td><tr>';
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

//ALERTA PARA ELIMINAR EL PAGO
function fntDelInfoPago(idpago, idprestamo)
{
    Swal.fire({
        title: "Eliminar pago",
        text: "¿Realmente quiere eliminar el pago?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d9a300",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
    }).then((result) => {
    if (result.isConfirmed) {
        fntDeletePago(idpago, idprestamo);
    }
    });
}
//ELIMINA EL PAGO
async function fntDeletePago(idpago, idprestamo)
{
    const formData = new FormData();
    formData.append('idPago', idpago);
    formData.append('idPrestamo', idprestamo);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Pagos/delPago', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            if(json.statusAnterior)
            {
                Swal.fire({
                    title: json.msg,
                    text: 'El resumen ha sido eliminado debido a que no contiene más datos',
                    icon: "warning",
                    confirmButtonColor: "#d9a300",
                    confirmButtonText: "Continuar",
                }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
                });
            } else {
                //Swal.fire("Eliminar!", json.msg , "success");
                tablePrestamos.ajax.reload(() => iconosPrestamos());
                document.querySelector('#valorActivo').textContent = json.valorActivo;
                document.querySelector('#cobradoEstimado').textContent = json.cobradoEstimado;
                $("#graficaMesCobrado").html(json.graficaMes);
                $("#graficaAnioCobrado").html(json.graficaAnio);
                Toast.fire({
                    icon: "success",
                    title: json.msg
                });
            }
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
    document.querySelector('#idPrestamo').value ="";  
    document.querySelector('#renovar').value ="";  
    document.querySelector('#titleModal').innerHTML = "Nuevo Préstamo";
    document.querySelector('#btnText').innerHTML = "Registrar"; 
    document.querySelector('#btnClienteNuevo').classList.remove("d-none")
    document.querySelector("#formPrestamos").reset();

    fntClientesPrestamo();

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

    $('#modalFormPrestamo').modal('show');
}

/** GRÁFICAS **/
//INFORMACIÓN DE CADA PUNTO DE LA GRÁGICA DEL PRÉSTAMO
async function fntInfoChartPrestamo(fecha) 
{
    let fechaFormateada = fecha.join("-");

    const formData = new FormData();
    formData.append('fecha', fechaFormateada);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Prestamos/getDatosGraficaPrestamo', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            
            let tdAnotaciones = json.data;
            let fecha = json.fecha;
            
            document.querySelector("#listgraficaPrestamo").innerHTML = tdAnotaciones;
            document.querySelector("#datePrestamoGrafica").textContent = fecha;

            $('#modalViewPrestamoGrafica').modal('show');
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

//BUSCADOR MENSUAL DE VENTAS
async function fntSearchPrestamosMes()
{
    let fecha = document.querySelector(".prestamosMes").value;
    if(fecha == "")
    {
        Swal.fire("", "Selecione el mes y el año", "error");
        return false;
    }

    const formData = new FormData();
    formData.append('fecha', fecha);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Prestamos/prestamosMes', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.text();
    
        $("#graficaMesPrestamos").html(json);
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

//BUSCADOR ANUAL DE VENTAS
async function fntSearchPrestamosAnio(){
    let anio = document.querySelector(".prestamosAnio").value;
    if(anio == ""){
        Swal.fire("", "Digite el Año" , "error");
        return false;
    }else{

        const formData = new FormData();
        formData.append('anio', anio);

        divLoading.style.display = "flex";
        try {
            let resp = await fetch(base_url+'/Prestamos/prestamosAnio', {
                method: 'POST',
                mode: 'cors',
                cache: 'no-cache',
                body: formData
            });
        
            json = await resp.text();
        
            $("#graficaAnioPrestamos").html(json);
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

//DATERANGEPICKER VENTAS
function fntViewDetallePrestamos()
{
    $('#modalDetallePrestamos').modal('show');
    $('#fechaPrestamos').daterangepicker({
        "autoUpdateInput": false,
        "locale": {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "applyLabel": "Aplicar",
            "cancelLabel": "Cancelar",
            "daysOfWeek": [
                "Dom",
                "Seg",
                "Ter",
                "Qua",
                "Qui",
                "Sex",
                "Sab"
            ],
            "monthNames": [
                "Janeiro",
                "Fevereiro",
                "Março",
                "Abil",
                "Maio",
                "Junho",
                "Julho",
                "Agosto",
                "Setembro",
                "Outubro",
                "Novembro",
                "Dezembro"
            ],
            "firstDay": 1
        }
    });

    $('#fechaPrestamos').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });

    $('#fechaPrestamos').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
}
//INFORMACIÓN DEL BUSCADOR DE PRÉSTAMOS DATERANGEPICKER
async function fntSearchPrestamosD(tipo)
{
    let fecha = document.querySelector("#fechaPrestamos").value;
    
    if(fecha == "")
    {
        Swal.fire("Error", "Seleccione la fecha", "error");
        return false;
    }

    const formData = new FormData();
    formData.append('fecha', fecha);

    divLoading.style.display = "flex";
    try {
        formData.append('prestamo', tipo);
            let resp = await fetch(base_url+'/Prestamos/getPrestamosD', {
                method: 'POST',
                mode: 'cors',
                cache: 'no-cache',
                body: formData
            });
    
        json = await resp.json();
    
        arrPrestamos = json.prestamosD;
        totalP = json.totalPrestamos;

        $(function () {
            $('[data-bs-toggle="popover"]').popover({
                container: "body",
                trigger: "focus",
                html: true
            })
        });

        document.querySelector("#datosPrestamosD").innerHTML = arrPrestamos;
        document.querySelector("#markPrestamos").innerHTML = totalP;
        document.querySelector("#divPrestamosD").classList.remove("d-none");
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

//INFORMACIÓN DE CADA PUNTO DE LA GRÁGICA DEL COBRADO
async function fntInfoChartCobrado(fecha) 
{
    let fechaFormateada = fecha.join("-");

    const formData = new FormData();
    formData.append('fecha', fechaFormateada);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Pagos/getDatosGraficaCobrado', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            
            let tdAnotaciones = json.data;
            let fecha = json.fecha;
            
            document.querySelector("#listgraficaCobrado").innerHTML = tdAnotaciones;
            document.querySelector("#dateCobradoGrafica").textContent = fecha;

            $('#modalViewCobradoGrafica').modal('show');
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

//BUSCADOR MENSUAL DE COBRADO   
async function fntSearchCobradoMes()
{
    let fecha = document.querySelector(".cobradoMes").value;
    if(fecha == "")
    {
        Swal.fire("", "Selecione el mes y el año", "error");
        return false;
    }

    const formData = new FormData();
    formData.append('fecha', fecha);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Prestamos/cobradoMes', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.text();
    
        $("#graficaMesCobrado").html(json);
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

//BUSCADOR ANUAL DE COBRADO
async function fntSearchCobradoAnio(){
    let anio = document.querySelector(".cobradoAnio").value;
    if(anio == ""){
        Swal.fire("", "Digite el Año" , "error");
        return false;
    }else{

        const formData = new FormData();
        formData.append('anio', anio);

        divLoading.style.display = "flex";
        try {
            let resp = await fetch(base_url+'/Prestamos/cobradoAnio', {
                method: 'POST',
                mode: 'cors',
                cache: 'no-cache',
                body: formData
            });
        
            json = await resp.text();
        
            $("#graficaAnioCobrado").html(json);
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

//DATERANGEPICKER COBRADO
function fntViewDetalleCobrado()
{
    $('#modalDetalleCobrado').modal('show');
    $('#fechaCobrado').daterangepicker({
        "autoUpdateInput": false,
        "locale": {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "applyLabel": "Aplicar",
            "cancelLabel": "Cancelar",
            "daysOfWeek": [
                "Dom",
                "Seg",
                "Ter",
                "Qua",
                "Qui",
                "Sex",
                "Sab"
            ],
            "monthNames": [
                "Janeiro",
                "Fevereiro",
                "Março",
                "Abil",
                "Maio",
                "Junho",
                "Julho",
                "Agosto",
                "Setembro",
                "Outubro",
                "Novembro",
                "Dezembro"
            ],
            "firstDay": 1
        }
    });

    $('#fechaCobrado').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });

    $('#fechaCobrado').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
}
//INFORMACIÓN DEL BUSCADOR DE COBRADO DATERANGEPICKER
async function fntSearchCobradoD()
{
    let fecha = document.querySelector("#fechaCobrado").value;
    
    if(fecha == "")
    {
        Swal.fire("Error", "Seleccione la fecha", "error");
        return false;
    }

    const formData = new FormData();
    formData.append('fecha', fecha);

    divLoading.style.display = "flex";
    try {
            let resp = await fetch(base_url+'/Prestamos/getCobradoD', {
                method: 'POST',
                mode: 'cors',
                cache: 'no-cache',
                body: formData
            });
    
        json = await resp.json();
    
        arrCobrado = json.cobradoD;
        totalC = json.totalCobrado;

        $(function () {
            $('[data-bs-toggle="popover"]').popover({
                container: "body",
                trigger: "focus",
                html: true
            })
        });

        document.querySelector("#datosCobradoD").innerHTML = arrCobrado;
        document.querySelector("#markCobrado").innerHTML = totalC;
        document.querySelector("#divCobradoD").classList.remove("d-none");
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

async function accion()
{
    const formData = new FormData();
    formData.append('idRuta', ruta);

    //divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Prestamos/accion', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
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

async function accionPrestamos()
{
    const formData = new FormData();
    formData.append('idRuta', ruta);

    //divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Prestamos/accionPrestamos', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
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

async function accionPrestamosUsuario()
{
    const formData = new FormData();
    formData.append('idRuta', ruta);

    //divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Prestamos/accionPrestamosUsuario', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
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