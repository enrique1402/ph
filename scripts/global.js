function hacerLogin(){
	if(window.sessionStorage){
		alert("hey");
		var frm = document.querySelectorAll("form")[0];
		sessionStorage.setItem("login", frm.login.value);
		sessionStorage.setItem("pass", frm.pass.value);
		//localStorage.clear();
		
		alert(sessionStorage.getItem("login")+sessionStorage.getItem("pass"));
	}
	else{
		alert("Tu navegador no soporta Web Storage");
	}
}
function crearNav(c){ 
	if(c==1){ 
		//nav para usuario logueado
		//Indice
		var nav = document.getElementById("nav");
		var enlaceIndice = document.createElement("a");
		enlaceIndice.innerHTML = "Índice";
		enlaceIndice.href = "index.html"
		var logoIndice = document.createElement("span");
		logoIndice.classList.add("icon-home");
		enlaceIndice.appendChild(logoIndice);
		//Rutas
		var enlaceRutas = document.createElement("a");
		enlaceRutas.innerHTML = "Rutas";
		enlaceRutas.href = "rutas.html";
		var logoRutas = document.createElement("span");
		logoRutas.classList.add("icon-paper-plane");
		enlaceRutas.appendChild(logoRutas);
		//Nueva ruta
		var enlaceAnyadir = document.createElement("a");
		enlaceAnyadir.innerHTML ="Añadir ruta";
		enlaceAnyadir.href = "nueva_ruta.html";
		var logoAnyadir = document.createElement("span");
		logoAnyadir.classList.add("icon-plus");
		enlaceAnyadir.appendChild(logoAnyadir);
		//Cerrar sesion
		var enlaceCerrar = document.createElement("a");
		enlaceCerrar.innerHTML = "Cerrar sesión";
		enlaceCerrar.href = "index.html";
		enlaceCerrar.limpiar = function(){
			sessionStorage.clear();
		}
		enlaceCerrar.onclick = enlaceCerrar.limpiar;

		nav.appendChild(enlaceIndice);
		nav.appendChild(enlaceRutas);
		nav.appendChild(enlaceAnyadir);
		nav.appendChild(enlaceCerrar);
	}else if(c==0){
		//nav para usuario NO logueado
		//Indice
		var nav = document.getElementById("nav");
		var enlaceIndice = document.createElement("a");
		enlaceIndice.innerHTML = "Índice";
		enlaceIndice.href = "index.html"
		var logoIndice = document.createElement("span");
		logoIndice.classList.add("icon-home");
		enlaceIndice.appendChild(logoIndice);
		//Login
		var enlaceLogin = document.createElement("a");
		enlaceLogin.innerHTML = "Login";
		enlaceLogin.href = "login.html";
		var logoLogin = document.createElement("span");
		logoLogin.classList.add("icon-export");
		enlaceLogin.appendChild(logoLogin);
		//Registro
		var enlaceRegistro = document.createElement("a");
		enlaceRegistro.innerHTML = "Registro";
		enlaceRegistro.href = "registro.html";
		var logoRegistro = document.createElement("span");
		logoRegistro.classList.add("icon-edit");
		enlaceRegistro.appendChild(logoRegistro);
		//Rutas
		var enlaceRutas = document.createElement("a");
		enlaceRutas.innerHTML = "Rutas";
		enlaceRutas.href = "rutas.html";
		var logoRutas = document.createElement("span");
		logoRutas.classList.add("icon-paper-plane");
		enlaceRutas.appendChild(logoRutas);

		nav.appendChild(enlaceIndice);
		nav.appendChild(enlaceLogin);
		nav.appendChild(enlaceRegistro);
		nav.appendChild(enlaceRutas);
	}
}
function comprobarLogin(){
	//try{
		if(typeof(Storage)!="undefined"){		
			alert(window.sessionStorage.length);
			//if(window.sessionStorage.login!=undefined){
			if(window.sessionStorage.length != 0){
				//si esta logueado
				crearNav(1);
				alert("Está logueado como "+sessionStorage.login);
			}else{
				//si no esta logueado
				if(window.location.href.contains("nueva_ruta.html")){
					alert("entro");
					window.location = "index.html";					
				}
				alert("llego aqui");
				crearNav(0);
				alert("No esta logueado");
				//alert(location.href.contains("index.html"));
			}
		}
		else{
			alert("Tu navegador no soporta Web Storage");
		}
	//}catch(e){
	//	alert(e);
	//}
}