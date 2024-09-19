let tableRoles;
let divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp() {
    fntTableRoles();
    fntNewRol();
}

//TABLA ROLES
function fntTableRoles() 
{
    tableRoles = $('#tableRoles').DataTable( {
        "aProcessing":true,
        "aServerSide":true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax":{
            "url": " "+base_url+"/Roles/getRoles",
            "dataSrc":""
        },
        "columns":[
            {"data":"idrol"},
            {"data":"nombrerol"},
            {"data":"descripcion"},
            {"data":"status"},
            {"data":"options"}
        ],
        "responsive":"true",
        "bDestroy": true,
        "iDisplayLength": 20,
        "order":[[0,"desc"]]  
    });
}

//CREAR Y ACTUALIZAR ROL
function fntNewRol() 
{
    if(document.querySelector("#formRol")){
        let formRol = document.querySelector("#formRol");
        formRol.onsubmit = function(e) 
        {
            e.preventDefault();
            fntGurdarRol();
        }
    }
}

//PETICION FETCH PARA CREAR Y ACTUALIZAR ROL
async function fntGurdarRol()
{
    let strNombre = document.querySelector('#txtNombre').value;
    let strDescripcion = document.querySelector('#txtDescripcion').value;
    let intStatus = document.querySelector('#listStatus').value;

    if(strNombre == '' || strDescripcion == '' || intStatus == '')
    {
        Swal.fire("Atención", "Todos los campos son obligatorios." , "error");
        return false;
    }

    let ElementsValid = document.getElementsByClassName("valid");
    for (let i = 0; i < ElementsValid.length; i++) 
        {
        if(ElementsValid[i].classList.contains('is-invalid'))
        {
            Swal.fire("Atencion!", "Por favor verifique los campos en rojo.", "error");
            /*Toast.fire({
                icon: "warning",
                title: "Por favor verifique los campos en rojo"
            });*/
            return false;
        }
    }
    divLoading.style.display = "flex";
    try {
        const data = new FormData(formRol);
        let resp = await fetch(base_url+'/Roles/setRol', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: data
        });
        json = await resp.json();
        if(json.status) {
            $('#modalFormRol').modal("hide");
            formRol.reset();
            //Swal.fire("Roles de usuario", objData.msg ,"success");
            tableRoles.ajax.reload(null, false);    
            Toast.fire({
                icon: "success",
                title: json.msg
            });
        } else {
            //Swal.fire("Error", objData.msg , "error");
            Toast.fire({
                icon: "error",
                title: json.msg
            });
        }
    } catch (error) {
        Toast.fire({
            icon: "error",
            title: "Ocurrió un error: " + error
        });
    }
    divLoading.style.display = "none";
    return false;
}

//PETICION FETCH PARA MOSTRAR INFORMACION DE ACTUALIZAR ROL
async function fntEditRol(idrol)
{
    document.querySelector('#titleModal').innerHTML ="Actualizar Rol";
    document.querySelector('#btnActionForm').classList.replace("btn-success", "btn-primary");
    document.querySelector('#btnText').textContent ="Actualizar";

    /**** PETICION CON FETCH ****/
    //divLoading.style.display = "flex";
    const formData = new FormData();
    formData.append('idRol', idrol);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Roles/getRol', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            document.querySelector("#idRol").value = json.data.idrol;
            document.querySelector("#txtNombre").value = json.data.nombrerol;
            document.querySelector("#txtDescripcion").value = json.data.descripcion;
    
            if(json.data.status == 1){
                document.querySelector("#listStatus").value = 1;
            }else{
                document.querySelector("#listStatus").value = 2;
            }

            $('#listStatus').select2({
                dropdownParent: $('#modalFormRol'),
                placeholder: 'Seleccione un Estado',
                language: {
                    noResults: function() {
                        return "No hay resultado";        
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                }
            });
            
            $('#modalFormRol').modal('show');
        }else{
            //Swal.fire("Error", json.msg , "error");
            Toast.fire({
                icon: "error",
                title: "Ocurrió un error interno"
            });
            console.log(error);
        }
    } catch (error) {
        Toast.fire({
            icon: "error",
            title: "Ocurrió un error interno"
        });
        console.log(error);
    }
    divLoading.style.display = "none";
    return false;
}

//ELIMINAR ROL
function fntDelRol(idrol)
{
    Swal.fire({
        title: "Eliminar Rol",
        text: "¿Realmente quiere eliminar el Rol?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d9a300",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
    }).then((result) => {
    if (result.isConfirmed) {
        fntDeleteRol(idrol);
    }
    });
}

//PETICION FETCH PARA ELIMINAR ROL
async function fntDeleteRol(idrol)
{
    const formData = new FormData();
    formData.append('idRol', idrol);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Roles/delRol', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            //Swal.fire("Eliminar!", objData.msg , "success");
            tableRoles.ajax.reload(null, false);
            Toast.fire({
                icon: "success",
                title: json.msg
            });
        }else{
            //Swal.fire("Error", json.msg , "error");
            Toast.fire({
                icon: "error",
                title: "Ocurrió un error"
            });
            console.log(error);
        }
    } catch (error) {
        Toast.fire({
            icon: "error",
            title: "Ocurrió un error interno"
        });
        console.log(error);
    }
    divLoading.style.display = "none";
    return false;
}

async function fntPermisos(idrol)
{
    const formData = new FormData();
    formData.append('idrol', idrol);
    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Permisos/getPermisosRol', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });

        texto = await resp.text();
        document.querySelector('#contentAjax').innerHTML = texto;
        $('.modalPermisos').modal('show');
        document.querySelector('#formPermisos').addEventListener('submit',fntSavePermisos,false);
    } catch (error) {
        Toast.fire({
            icon: "error",
            title: "Ocurrió un error: " + error
        });
    }
    divLoading.style.display = "none";
    return false;
}

function fntSavePermisos(e)
{
    e.preventDefault();
    let formElement = document.querySelector("#formPermisos");
    fntGuardarPermiso(formElement);
}

async function fntGuardarPermiso(formElement)
{
    divLoading.style.display = "flex";
    try {
        const data = new FormData(formElement);
        let resp = await fetch(base_url+'/Permisos/setPermisos', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: data
        });

        json = await resp.json();
            if(json.status) {
                //Swal.fire("Permisos de usuario", objData.msg , "success");
                Toast.fire({
                    icon: "success",
                    title: json.msg
                });
            } else {
                //Swal.fire("Error", objData.msg , "error");
                Toast.fire({
                    icon: "error",
                    title: objData.msg
                });
            }
    } catch (error) {
        Toast.fire({
            icon: "error",
            title: "Ocurrió un error: " + error
        });
    }
    divLoading.style.display = "none";
    return false;
}

//MODAL DE CREAR Y ACTUALIZAR ROL
function openModal() {
    document.querySelector('#idRol').value ="";
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-success");
    document.querySelector('#btnText').innerHTML ="Registrar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Rol";
    document.querySelector("#formRol").reset();

    $('#listStatus').val(null).trigger('change');
    $('#listStatus').select2({
        dropdownParent: $('#modalFormRol'),
        placeholder: 'Seleccione un Estado',
        language: {
            noResults: function() {
                return "No hay resultado";        
            },
            searching: function() {
                return "Buscando...";
            }
        }
    });

    $('#modalFormRol').modal('show');
}