function hacerLogin(){
	if(window.sessionStorage){
		var frm = document.querySelectorAll("form")[0];
		//sessionStorage.setItem("login", frm.login.value);
		//sessionStorage.setItem("pass", frm.pass.value);
		localStorage.clear();
		alert("hey");
		alert(sessionStorage.getItem("login")+sessionStorage.getItem("pass"));
	}
	else{
		alert("Tu navegador no soporta Web Storage");
	}
}
function comprobarLogin(){
	if(typeof(Storage)!="undefined"){		
		if(window.sessionStorage.login!=undefined){
			//si esta logueado
			alert(sessionStorage.login);
			//alert(localStorage.getItem("login")+localStorage.getItem("pass"));

			var enlacesOcultos = document.getElementsByClassName("noLog");
			for(int i=0;i<enlacesOcultos.length;i++){
				enlacesOcultos[i].style.display = "none";
			}
		}else{
			//si no esta logueado
			alert("No esta logueado");
			var enlacesOcultos = document.getElementsByClassName("log");
			enlacesOcultos.style.display = "none";
			var enlacesVisibles = document.getElementsByClassName("noLog");
			for(int i=0;i<enlacesVisibles.length;i++){
				enlacesVisibles[i].style.display = "inline";
			}
			//anyado enlace`para cerrar sesion
			var capa = document.getElementById("nav");
			var enlace = document.createElement("a");
			enlace.innerHTML = "Cerrar sesión";
			enlace.href = "index.html";
			capa.appendChild(enlace);		
		}
	}
	else{
		alert("Tu navegador no soporta Web Storage");
	}
}