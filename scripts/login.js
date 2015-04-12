var obj; // variable que guarda el objeto XMLHttpRequest
var formdata;

function crearObjAjax(){
	var xmlhttp;
	if(window.XMLHttpRequest) { // Objeto nativo
		xmlhttp = new XMLHttpRequest(); // Se obtiene el nuevo objeto
	} else if (window.ActiveXObject) { // IE(Windows): objecto ActiveX
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	return xmlhttp;
}

function peticionLoginPOST() {
	
	//var url = "rest/login/";
	var url = "../rest/post/login.php"
	formdata = new FormData(document.getElementById("formLogin"));
	obj = crearObjAjax();
	if (obj) { // Si se ha creado el objeto, se completa la petición ...
		// Argumentos:
		var login = document.getElementById("login").value;
		var pass = document.getElementById("password").value;
		var args="usu="+login+"&pwd="+pass;
		alert(url);
		formdata.append(login, pass);
		// Se establece la función (callback) a la que llamar cuando cambie el estado:
		obj.onreadystatechange = procesarCambio; // función callback: procesarCambio
		obj.open("POST", url, true); // Se crea petición POST a url, asíncrona ("true")
		// Es necesario especificar la cabecera "Content-type" para peticiones POST
		obj.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		obj.send(formdata); // Se envía la petición
	}else{
		alert("Error");
	}
}

function procesarCambio(){

	if(obj.readyState == 4){ // valor 4: respuesta recibida y lista para ser procesada
		alert(obj.responseText);
		if(obj.status == 200){ // El valor 200 significa "OK"
		// Aquí se procesa lo que se haya devuelto:
		/********************************************************************************************************/
			alert("aaaaa");
		//document.getElementById("miDiv").innerHTML = obj.responseText;
		} else alert("Hubo un problema con los datos devueltos"); // ERROR
	}
}