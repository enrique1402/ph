

var obj; // variable que guarda el objeto XMLHttpRequest

function crearObjAjax(){
	var xmlhttp;
	if(window.XMLHttpRequest) { // Objeto nativo
		xmlhttp = new XMLHttpRequest(); // Se obtiene el nuevo objeto
	} else if (window.ActiveXObject) { // IE(Windows): objecto ActiveX
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	return xmlhttp;
}

function peticionGET(url){
	
	obj = crearObjAjax();
	if(obj) { // Si se ha creado el objeto, se completa la petición ...
		obj.onreadystatechange = procesarCambio; // función callback: procesarCambio
		obj.open("GET", url, true); // Se crea petición GET a url, asíncrona ("true")
		obj.send(); // Se envía la petición
		alert("Objeto creado");
	}
	else{
		alert("Error creando objeto");//Prueba  
	}
}

function peticionLoginPOST()
{
	var usu = document.getElementById("login").value;
	var pwd = document.getElementById("password").value;
	
	var Url = "../rest/login/";
	
	var misdatos="usu="+usu+"&pwd="+pwd;
	//Url += usu;
	obj = new crearObjAjax();
	alert(obj.responseText ); 

	obj.onreadystatechange = procesarCambio; // función callback: procesarCambio
	obj.open("POST", Url, true);
	obj.setRequestHeader("Content-Type","application/x-www-form-urlencoded");	
	obj.send(misdatos); 

	alert(obj.responseText ); 
	
}

function devuelveRutas() {
	num=6;

	var url = "../rest/ruta/?u="+num;
	peticionGET(url);
}

function procesarCambio(){
	if(obj.readyState == 4){ // valor 4: respuesta recibida y lista para ser procesada
		alert(obj.status);

		if(obj.status == 200){ // El valor 200 significa "OK"
		// Aquí se procesa lo que se haya devuelto:
		/********************************************************************************************************/
			alert("OK");
		//document.getElementById("miDiv").innerHTML = obj.responseText;
		} else alert("Hubo un problema con los datos devueltos"); // ERROR
	}
}


