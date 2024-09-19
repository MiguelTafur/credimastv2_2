var divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp() {
    fntLogin();
}

function fntLogin()
{
    if(document.querySelector("#formLogin"))
    {
        let formLogin = document.querySelector("#formLogin");
        formLogin.onsubmit = function(e)
        {
            e.preventDefault();
            let strRuta = document.querySelector('#txtRuta').value;
            let intCodigo = document.querySelector('#txtCodigo').value;
            let strEmail = document.querySelector('#txtEmail').value;
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
            if(intCodigo == "" || strRuta == "" || strEmail == "")
            {
                Swal.fire("Por favor", "Completa todos los campos", "error");
                return false;
            }else{
                fntLoginUsuario();
            }
        }
    }
}

async function fntLoginUsuario() {
    divLoading.style.display = "flex";
    try {
        const data = new FormData(formLogin);
        let resp = await fetch(base_url+'/Login/loginUser', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: data
        });
        json = await resp.json();
        if(json.status) {
            //Swal.fire("Roles de usuario", objData.msg ,"success");
            Toast.fire({
                icon: "success",
                title: "Bienvenido a CREDIMAST"
            });
            setInterval(() => {
                window.location = base_url+'/prestamos';    
            }, 1500);
            
        } else {
            Swal.fire("Atención", json.msg, "error");
            /*Toast.fire({
                icon: "error",
                title: json.msg
            });*/
        }
    } catch (error) {
        Swal.fire("Atención", "Ocurrió un error: " + error, "error");
        /*Toast.fire({
            icon: "error",
            title: "Ocurrió un error: " + error
        });*/
    }
    divLoading.style.display = "none";
    return false;
}