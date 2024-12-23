let tableGastos;
let divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp() {
    fntTableGastos();
    fntNewGasto();
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
            {"data":"monto"},
            {"data":"hora"},
            {"data":"options"}
        ],

        "columnDefs": [
            {"className": "dt-center", "targets": "_all"}
        ],
        
        "responsive":"true",
        "bDestroy": true,
        "iDisplayLength": 50
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
            $("#graficaMesGastos").html(json.graficaMes);
            $("#graficaAnioGastos").html(json.graficaAnio);
            //Swal.fire("Gastos", json.msg ,"success");
            Toast.fire({
                icon: "success",
                title: json.msg
            });
        } else {
            Swal.fire("Error", json.msg , "error");
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

//EDITAR GASTO
async function fntEditInfo(idgasto)
{
    document.querySelector('#titleModal').innerHTML = "Actualizar Gasto";
    document.querySelector('#btnText').innerHTML = "Actualizar";

    const formData = new FormData();
    formData.append('idGasto', idgasto);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url + '/Gastos/getGasto', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status)
        {
            document.querySelector("#idGasto").value = json.data.idgasto;
            document.querySelector('#txtNombre').value = json.data.nombre;
            document.querySelector('#txtValor').value = json.data.monto;
            $('#modalFormGastos').modal('show');
            
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

//ELIMINAR GASTO
function fntDelInfo(idgasto)
{
    Swal.fire({
        title: "Eliminar Gasto",
        text: "¿Realmente quiere eliminar el Gasto?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d9a300",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
    }).then((result) => {
    if (result.isConfirmed) {
        fntDeleteGasto(idgasto);
    }
    });
}

async function fntDeleteGasto(idgasto)
{
    const formData = new FormData();
    formData.append('idGasto', idgasto);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Gastos/delGasto', {
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
                tableGastos.ajax.reload(null, false);
                $("#graficaMesGastos").html(json.graficaMes);
                $("#graficaAnioGastos").html(json.graficaAnio);
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
    document.querySelector('#idGasto').value ="";
    document.querySelector('#titleModal').innerHTML = "Nuevo Gasto";
    document.querySelector('#btnText').innerHTML = "Registrar";
    document.querySelector("#formGasto").reset();
    $('#modalFormGastos').modal('show');
}

/** GRÁFICA **/
//INFORMACIÓN DE CADA PUNTO DE LA GRÁGICA DEL GASTO
async function fntInfoChartGasto(fecha) 
{
    let fechaFormateada = fecha.join("-");

    const formData = new FormData();
    formData.append('fecha', fechaFormateada);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Gastos/getDatosGraficaGasto', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            
            let tdAnotaciones = json.data;
            let fecha = json.fecha;
            
            document.querySelector("#listgraficaGasto").innerHTML = tdAnotaciones;
            document.querySelector("#dateGastoGrafica").textContent = fecha;

            $('#modalViewGastoGrafica').modal('show');
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
async function fntSearchGastosMes()
{
    let fecha = document.querySelector(".gastosMes").value;
    if(fecha == "")
    {
        Swal.fire("", "Selecione el mes y el año", "error");
        return false;
    }

    const formData = new FormData();
    formData.append('fecha', fecha);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Gastos/gastosMes', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.text();
    
        $("#graficaMesGastos").html(json);
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

//BUSCADOR ANUAL
async function fntSearchGastosAnio(){
    let anio = document.querySelector(".gastosAnio").value;
    if(anio == ""){
        Swal.fire("", "Digite el Año" , "error");
        return false;
    }else{

        const formData = new FormData();
        formData.append('anio', anio);

        divLoading.style.display = "flex";
        try {
            let resp = await fetch(base_url+'/Gastos/gastosAnio', {
                method: 'POST',
                mode: 'cors',
                cache: 'no-cache',
                body: formData
            });
        
            json = await resp.text();
        
            $("#graficaAnioGastos").html(json);
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

//DATERANGEPICKER
function fntViewDetalleGastos()
{
    $('#modalDetalleGastos').modal('show');
    $('#fechaGastos').daterangepicker({
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

    $('#fechaGastos').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });

    $('#fechaGastos').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
}
//INFORMACIÓN DEL BUSCADOR DATERANGEPICKER
async function fntSearchGastosD()
{
    let fecha = document.querySelector("#fechaGastos").value;
    if(fecha == "")
    {
        Swal.fire("Error", "Seleccione la fecha", "error");
        return false;
    }

    const formData = new FormData();
    formData.append('fecha', fecha);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Gastos/getGastosD', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        arrGastos = json.gastosD;
        totalG = json.totalGastos;

        $(function () {
            $('[data-bs-toggle="popover"]').popover({
                container: "body",
                trigger: "focus",
                html: true
            })
        });

        document.querySelector("#datosGastosD").innerHTML = arrGastos;
        document.querySelector("#markGastos").innerHTML = totalG;
        document.querySelector("#divGastosD").classList.remove("d-none");
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