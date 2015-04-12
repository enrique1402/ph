var obj; // variable que guarda el objeto XMLHttpRequest
var objFoto;
var objComentario;

function crearObjAjax(){
	var xmlhttp;
	if(window.XMLHttpRequest) { // Objeto nativo
		xmlhttp = new XMLHttpRequest(); // Se obtiene el nuevo objeto
	} else if (window.ActiveXObject) { // IE(Windows): objecto ActiveX
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	return xmlhttp;
}


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

function devuelveGETSincrono(url){
	aux = crearObjAjax();
	if(obj){
		aux.open("GET", url, false);
		aux.send();
	}
	else{
		"Error creando el objeto";
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
					var textLi1 = document.createTextNode(ruta.FECHA);
					li1.appendChild(textLi1);

					var li2 = document.createElement("li");
					var textLi2 = document.createTextNode(ruta.DISTANCIA);
					li2.appendChild(textLi2);

					var li3 = document.createElement("li");

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
					//document.getElementsByClassName("titulo")[i].innerHTML = ruta.DIFICULTAD;
					/*
					<a href="ruta.html">				
						<figure id="ruta1">
							<img src="imagenes/ruta1.jpg" alt="Imagen de la ruta" class="foto">
							<figcaption>
								<h2 class="titulo">Titulo ruta 1</h2>
								<ul>
									<li class="fecha">dd/mm/aa</li>
									<li class="distancia">x Km</li>
									<li class="dificultad">
										<span class="icon-star" data-value="1" title="1 estrellas"></span>
										<span class="icon-star" data-value="2" title="2 estrellas"></span>
										<span class="icon-star" data-value="3" title="3 estrellas"></span>
										<span class="icon-star" data-value="4" title="4 estrellas"></span>
										<span class="icon-star" data-value="5" title="5 estrellas"></span>
									</li>
									<li class="numFotos">N fotos disponibles</li>
									<li class="numComentario">n comentarios disponibles</li>
								</ul>
							</figcaption>
						</figure>
					</a>
					*/
}

function parsearJSON(objeto){
	var devuelve;
	if(window.JSON){
		devuelve = window.JSON.parse(objeto);
	}else{
		devuelve = eval( '(' + objeto + ')' );
	}

	return devuelve;
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
					/*document.getElementsByClassName("titulo")[i].innerHTML = ruta.NOMBRE;
					document.getElementsByClassName("fecha")[i].innerHTML = ruta.FECHA;
					document.getElementsByClassName("distancia")[i].innerHTML = ruta.DISTANCIA+" km";
					document.getElementsByClassName("numFotos")[i].innerHTML = numFotos + " fotos disponibles";
					document.getElementsByClassName("numComentario")[i].innerHTML = numComentario + " comentarios disponibles";*/
					
					generarCodigoRuta(ruta, numFotos, numComentario, rutaFoto);

				}
				else{
					alert("No existe la ruta "+i)
				}
			}
		//document.getElementById("miDiv").innerHTML = obj.responseText;
		} else alert("Hubo un problema con los datos devueltos"); // ERROR
	}
}
