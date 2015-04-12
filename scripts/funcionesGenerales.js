
function parsearJSON(objeto){
	var devuelve;
	if(window.JSON){
		devuelve = window.JSON.parse(objeto);
	}else{
		devuelve = eval( '(' + objeto + ')' );
	}
	return devuelve;
}

function crearObjAjax(){
	var xmlhttp;
	if(window.XMLHttpRequest) { // Objeto nativo
		xmlhttp = new XMLHttpRequest(); // Se obtiene el nuevo objeto
	} else if (window.ActiveXObject) { // IE(Windows): objecto ActiveX
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	return xmlhttp;
}

function devuelveGETSincrono(url){
	aux = crearObjAjax();
	if(obj){
		aux.open("GET", url, false);
		aux.send();
	}	
}

