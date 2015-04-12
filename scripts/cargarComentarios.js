var obj; // variable que guarda el objeto XMLHttpRequest
var objFoto;
var objComentario;


function devuelveComentarios() {
	num=10;

	var url = "rest/comentario/?u="+num;

	obj = crearObjAjax();
	if(obj) { // Si se ha creado el objeto, se completa la petición ...
		obj.onreadystatechange = procesarCambioComent; // función callback: procesarCambio
		obj.open("GET", url, true); // Se crea petición GET a url, asíncrona ("true")
		obj.send(); // Se envía la petición
	}
}


function generarCodigoComentario(comentario){

	var art = document.createElement("article");

	var a = document.createElement("a");
	var enlaceA = document.createAttribute("href");
	enlaceA.value="ruta.html";
	a.setAttributeNode(enlaceA);

	var h2 = document.createElement("h2");
	var textH2 = document.createTextNode(comentario.TITULO);
	h2.appendChild(textH2);

	var p = document.createElement("p");
	var textP = document.createTextNode(comentario.TEXTO);
	p.appendChild(textP);
	var claseA = document.createAttribute("class");
	claseA.value="comment";
	p.setAttributeNode(claseA);

	var p2 = document.createElement("p");
	var textP2 = document.createTextNode(comentario.LOGIN + " - " + comentario.FECHA);
	p2.appendChild(textP2);

	a.appendChild(h2);
	a.appendChild(p);
	a.appendChild(p2);

	art.appendChild(a);

	document.getElementById("comentarios").appendChild(art);
}

function procesarCambioComent(){
	if(obj.readyState == 4){ // valor 4: respuesta recibida y lista para ser procesada
		if(obj.status == 200){ // El valor 200 significa "OK"
		// Aquí se procesa lo que se haya devuelto:
		/********************************************************************************************************/
			var comentario;

			var comentarios = parsearJSON(obj.responseText);//Usa los métodos ya definidos en cargarRutas
			//Borramos los anteriores comentarios que hubieran
			var seccion=document.getElementById("comentarios");

			while (seccion.firstChild) {
    			seccion.removeChild(seccion.firstChild);
			}

			for(var i=0; i<comentarios.length; i++){
				comentario = comentarios[i];
				//ID FECHA NOMBRE RECORRIDO DESCRIPCION LOGIN DIFICULTAD DISTANCIA
				if(comentario!=null){
																								
					generarCodigoComentario(comentario);

				}
			}

		}
	}
}