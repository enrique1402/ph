var obj; // variable que guarda el objeto XMLHttpRequest
var objFoto;
var objComentario;


function devuelveRutas() {
	num=6;

	var url = "rest/ruta/?u="+num;

	obj = crearObjAjax();
	if(obj) { // Si se ha creado el objeto, se completa la petición ...
		obj.onreadystatechange = procesarCambio; // función callback: procesarCambio
		obj.open("GET", url, true); // Se crea petición GET a url, asíncrona ("true")
		obj.send(); // Se envía la petición
	}
}



function generarCodigoRuta(ruta, numFotos, numComents, rutaFoto){
	/**GENERACION DE CODIGO**/
	var nodo = document.createElement("a");
	var enlace = document.createAttribute("href");     
	enlace.value = "ruta.html";                          
	nodo.setAttributeNode(enlace); 

		var fig = document.createElement("figure");
		var idFig = document.createAttribute("id");
		idFig.value="ruta"+ruta.ID;
		fig.setAttributeNode(idFig); 

			var img = document.createElement("img");
			var srcImg = document.createAttribute("src");
			srcImg.value=rutaFoto;
			var altImg = document.createAttribute("alt");
			altImg.value="Imagen de "+ruta.NOMBRE;
			var classImg = document.createAttribute("class");
			classImg.value="foto";
			img.setAttributeNode(srcImg);
			img.setAttributeNode(altImg);
			img.setAttributeNode(classImg);

			var figCap = document.createElement("figcaption");

				var h2 = document.createElement("h2");
				var textH2 = document.createTextNode(ruta.NOMBRE);
				h2.appendChild(textH2);

				var ul = document.createElement("ul");

					var li1 = document.createElement("li");
					var textLi1 = document.createTextNode("Fecha: "+ruta.FECHA);
					li1.appendChild(textLi1);

					var li2 = document.createElement("li");
					var textLi2 = document.createTextNode("Distancia: "+ruta.DISTANCIA+" km");
					li2.appendChild(textLi2);

					var li3 = document.createElement("li");
					var textLi3 = document.createTextNode("Dificultad: ");
					li3.appendChild(textLi3);

					//Estrellas rellenas
					for(var i=1; i<=parseInt(ruta.DIFICULTAD); i++){
						var span = document.createElement('span');
						var classSpan = document.createAttribute("class");
						classSpan.value="icon-star";
						var dv = document.createAttribute("data-value");
						dv.value=i;
						var title = document.createAttribute("title");
						title.value=i + " estrellas";

						span.setAttributeNode(classSpan);
						span.setAttributeNode(dv);
						span.setAttributeNode(title);

						li3.appendChild(span);
					}

					//Estrellas vacias
					for(var i=1; i<=5-parseInt(ruta.DIFICULTAD); i++){
						var span = document.createElement('span');
						var classSpan = document.createAttribute("class");
						classSpan.value="icon-star-empty";
						var dv = document.createAttribute("data-value");
						dv.value=i;
						var title = document.createAttribute("title");
						title.value=i + " estrellas";

						span.setAttributeNode(classSpan);
						span.setAttributeNode(dv);
						span.setAttributeNode(title);

						li3.appendChild(span);
					}

					var li4 = document.createElement("li");
					var textLi4 = document.createTextNode(numFotos + " fotos disponibles");
					li4.appendChild(textLi4);
					
					var li5 = document.createElement("li");
					var textLi5 = document.createTextNode(numComents + " comentarios disponibles");
					li5.appendChild(textLi5);
	
	ul.appendChild(li1);
	ul.appendChild(li2);
	ul.appendChild(li3);
	ul.appendChild(li4);
	ul.appendChild(li5);

	figCap.appendChild(h2);
	figCap.appendChild(ul);
	
	fig.appendChild(img);
	fig.appendChild(figCap);
	
	nodo.appendChild(fig);

	document.getElementById("rutas").appendChild(nodo);

}

function procesarCambio(){
	if(obj.readyState == 4){ // valor 4: respuesta recibida y lista para ser procesada
		if(obj.status == 200){ // El valor 200 significa "OK"
		// Aquí se procesa lo que se haya devuelto:
		/********************************************************************************************************/
			var ruta;
			var rutas = parsearJSON(obj.responseText);
			//Borramos las anteriores rutas que hubieran
			var seccion=document.getElementById("rutas");
			while (seccion.firstChild) {
    			seccion.removeChild(seccion.firstChild);
			}

			for(var i=0; i<rutas.length; i++){
				ruta = rutas[i];
				//ID FECHA NOMBRE RECORRIDO DESCRIPCION LOGIN DIFICULTAD DISTANCIA
				if(ruta!=null){
					idRuta = ruta.ID;
					var urlFotos = "rest/foto/?idr="+idRuta;
					devuelveGETSincrono(urlFotos);						
					if(aux.readyState==4 && aux.status==200){
						var arrayFotos = parsearJSON(aux.responseText);
						var rutaFoto = arrayFotos[0].ARCHIVO;
						rutaFoto = "imagenes/ruta"+rutaFoto;
						var numFotos=arrayFotos.length;						
					}
					else{
						numFotos = 0;
						rutaFoto = "imagenes/logo.png";
					}											
					
					var urlComentario = "rest/comentario/?idr="+idRuta;
					devuelveGETSincrono(urlComentario);

					if(aux.readyState==4 && aux.status==200){
						var arrayComentario = parsearJSON(aux.responseText);
						var numComentario=arrayComentario.length;
					}else{
						numComentario = 0;
					}											
					
					generarCodigoRuta(ruta, numFotos, numComentario, rutaFoto);

				}
			}

			devuelveComentarios();
		}
	}
}

window.onLoad=devuelveRutas();