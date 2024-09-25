let tableUsuarios;
let divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp() {
    fntTableUsuarios();
    fntRolesUsuario();
    fntRutasUsuario();
    fntNewUsuario();
}

//TABLA USUARIOS
function fntTableUsuarios() 
{
    tableUsuarios = $('#tableUsuarios').DataTable( {
        "aProcessing":true,
        "aServerSide":true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax":{
            "url": " "+base_url+"/Usuarios/getUsuarios",
            "dataSrc":""
        },
        "columns":[
            {"data":"nombrerol"},
            {"data":"nombres"},
            {"data":"email_user"},
            {"data":"codigoruta"},
            {"data":"status"},
            {"data":"options"}
        ],
        "responsive":"true",
        "bDestroy": true,
        "iDisplayLength": 20,
        "order":[[1,"asc"]]  
    });
}

//TRAER TODOS LOS ROLES
async function fntRolesUsuario()
{
    if(document.querySelector('#listRolid'))
    {
        let resp = await fetch(base_url + '/Roles/getSelectRoles', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache'
        });

        texto = await resp.text();
        document.querySelector('#listRolid').innerHTML = texto;
    }
}

//TRAER TODAS LAS RUTAS
async function fntRutasUsuario()
{
    if(document.querySelector('#listRuta'))
    {
        let resp = await fetch(base_url + '/Usuarios/getRutas', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache'
        });

        texto = await resp.text();
        document.querySelector('#listRuta').innerHTML = texto;
    }
}

//TRAE UN USUARIO EN ESPECÍFICO
async function fntViewUsuario(idpersona)
{
    const formData = new FormData();
    formData.append('idPersona', idpersona);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Usuarios/getUsuario', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            let estadoUsuario  = json.data.status == 1 ? 
            '<span class="badge bg-success">Activo</span>' : 
            '<span class="badge bg-danger">Inactivo</span>';

            document.querySelector("#celNombres").innerHTML = json.data.nombres;
            document.querySelector("#celEmail").innerHTML = json.data.email_user;
            document.querySelector("#celTipoUsuario").innerHTML = json.data.nombrerol;
            document.querySelector("#celEstado").innerHTML = estadoUsuario;
            document.querySelector("#celFechaRegistro").innerHTML = json.data.fechaRegistro;
            
            $('#modalViewUser').modal('show');
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

async function fntEditUsuario(idpersona)
{
    document.querySelector('#titleModal').innerHTML = "Actualizar Usuario";
    document.querySelector('#btnText').innerHTML = "Actualizar";

    const formData = new FormData();
    formData.append('idPersona', idpersona);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Usuarios/getUsuario', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status)
        {
            document.querySelector("#idUsuario").value = json.data.idpersona;
            document.querySelector("#txtNombre").value = json.data.nombres;
            document.querySelector("#txtEmail").value = json.data.email_user;
            document.querySelector("#listRolid").value = json.data.idrol;
            document.querySelector("#listRuta").value = json.data.codigoruta;
            if(json.data.status == 1){
                document.querySelector("#listStatus").value = 1;
            }else{
                document.querySelector("#listStatus").value = 2;
            }
            $('#listStatus').select2({
                dropdownParent: $('#modalFormUsuario'),
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
            $('#listRolid').select2({
                dropdownParent: $('#modalFormUsuario'),
                placeholder: 'Seleccione un Rol',
                language: {
                    noResults: function() {
                        return "No hay resultado";        
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                }
            });
            $('#listRuta').select2({
                dropdownParent: $('#modalFormUsuario'),
                placeholder: 'Seleccione una Ruta',
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
            $('#modalFormUsuario').modal('show');
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

//REGISTRAR USUARIO
function fntNewUsuario()
{
    if(document.querySelector("#formUsuario"))
    {
        let formUsuario = document.querySelector("#formUsuario");
        formUsuario.onsubmit = function(e)
        {
            e.preventDefault();
            fntGurdarUsuario();
        }
    }
}

//PETICION FETCH PARA CREAR Y ACTUALIZAR ROL
async function fntGurdarUsuario()
{
    $('#listRuta').select2({
        dropdownParent: $('#modalFormUsuario'),
        placeholder: 'Seleccione una Ruta',
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
    let strNombre = document.querySelector('#txtNombre').value;
    let strEmail = document.querySelector('#txtEmail').value;
    let intTipoUsuario = document.querySelector('#listRolid').value;
    let intStatus = document.querySelector('#listStatus').value;
    let intRuta = document.querySelector('#listRuta').value;

    if(strNombre == '' || strEmail == '' || intStatus == '' || intTipoUsuario == '' || intRuta == '')
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
        const data = new FormData(formUsuario);
        let resp = await fetch(base_url + '/Usuarios/setUsuario', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: data
        });
        json = await resp.json();
        if(json.status) 
        {
            tableUsuarios.ajax.reload(null, false);
            $('#modalFormUsuario').modal("hide");
            formUsuario.reset();
            //Swal.fire("Roles de usuario", json.msg ,"success");
            Toast.fire({
                icon: "success",
                title: json.msg
            });
        } else {
            Swal.fire("Error", json.msg, "error");
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

function fntDelUsuario(idpersona)
{
    Swal.fire({
        title: "Eliminar Usuario",
        text: "¿Realmente quiere eliminar el Usuario?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d9a300",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
    }).then((result) => {
    if (result.isConfirmed) {
        fntDeleteUsuario(idpersona);
    }
    });
}

async function fntDeleteUsuario(idpersona)
{
    const formData = new FormData();
    formData.append('idPersona', idpersona);

    divLoading.style.display = "flex";
    try {
        let resp = await fetch(base_url+'/Usuarios/delUsuario', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
    
        json = await resp.json();
    
        if(json.status){
            //Swal.fire("Eliminar!", json.msg , "success");
            tableUsuarios.ajax.reload(null, false);
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

//MODAL DE CREAR Y ACTUALIZAR ROL
function openModal() {
    document.querySelector('#idUsuario').value ="";
    document.querySelector('#btnText').innerHTML ="Registrar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Usuario";
    document.querySelector("#formUsuario").reset();

    $('#listStatus').select2({
        dropdownParent: $('#modalFormUsuario'),
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

    $('#listRuta').select2({
        dropdownParent: $('#modalFormUsuario'),
        placeholder: 'Seleccione una Ruta',
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

    $('#listRolid').select2({
        dropdownParent: $('#modalFormUsuario'),
        placeholder: 'Seleccione un Rol',
        language: {
            noResults: function() {
                return "No hay resultado";        
            },
            searching: function() {
                return "Buscando...";
            }
        }
    });

    $('#listRolid').val(null).trigger('change');
    $('#listRuta').val(null).trigger('change');

    $('#modalFormUsuario').modal('show');
}