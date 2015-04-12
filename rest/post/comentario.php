<?php
// FICHERO: rest/post/comentario.php

$METODO = $_SERVER['REQUEST_METHOD'];
// EL METODO DEBE SER POST. SI NO LO ES, NO SE HACE NADA.
if($METODO<>'POST') exit();
// PETICIONES POST ADMITIDAS:
//   rest/comentario/

// =================================================================================
// =================================================================================
// INCLUSION DE LA CONEXION A LA BD
   require_once('../configbd.php');
// =================================================================================
// =================================================================================

// =================================================================================
// CONFIGURACION DE SALIDA JSON
// =================================================================================
header("Access-Control-Allow-Orgin: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json");
// =================================================================================
// Se prepara la respuesta
// =================================================================================
$R = []; // Almacenará el resultado.
$RESPONSE_CODE = 200; // código de respuesta por defecto: 200 - OK
// =================================================================================
// =================================================================================
$PARAMS = $_POST;
$login  = mysqli_real_escape_string( $link, $PARAMS['login']);
$clave  = mysqli_real_escape_string( $link, $PARAMS['clave']);
$titulo = mysqli_real_escape_string( $link, $PARAMS['titulo']);
$texto  = mysqli_real_escape_string( $link, nl2br($PARAMS['texto'],false));
$idruta  = mysqli_real_escape_string( $link, $PARAMS['idruta']);

if( !comprobarSesion($login,$clave) )
  $R = array('resultado' => 'ok', 'descripcion' => 'Tiempo de sesión agotado.');
else
{
	try{
		mysqli_query($link, 'BEGIN'); // Inicio de transacción
		$mysql  = 'insert into COMENTARIO(TITULO,TEXTO,LOGIN,ID_RUTA) values("' . $titulo;
		$mysql .= '","' . $texto . '","' . $login . '",' . $idruta . ')';
		if(mysqli_query($link, $mysql))
		{
			$mysql = 'select MAX(ID) as ID from COMENTARIO';
			if( $res = mysqli_query($link,$mysql) )
      {
        $row = mysqli_fetch_assoc($res);
        $ID = $row['ID'];
	  		$R = array('resultado' => 'ok', 'idcomentario' => $ID);
	  	}
		}
		else
		{
			$RESPONSE_CODE = 500;
	    $R = array('resultado' => 'error', 'descripcion' => 'Error de servidor.');
		}
   mysqli_query($link, "COMMIT");
  }catch(Exception $e){
      mysqli_query($link, "ROLLBACK");
  }
}



// =================================================================================
// SE HACE LA CONSULTA
// =================================================================================
if( count($R)==0 && $res = mysqli_query( $link, $mysql ) )
{
  if( substr($mysql, 0, 6) == "select" )
  {
    while( $row = mysqli_fetch_assoc( $res ) )
      $R[] = $row;
    mysqli_free_result( $res );
  }
  else $R[] = $res;
}
// =================================================================================
// SE CIERRA LA CONEXION CON LA BD
// =================================================================================
mysqli_close($link);
// =================================================================================
// SE DEVUELVE EL RESULTADO DE LA CONSULTA
// =================================================================================
//echo json_encode($R);
// =================================================================================
// CON CODIGO DE ERROR:
// =================================================================================

   try {
        // Here: everything went ok. So before returning JSON, you can setup HTTP status code too
        // $rtn = array("id", "3", "name", "John");
        http_response_code($RESPONSE_CODE);
        print json_encode($R);
    }
    catch (SomeException $ex) {
        //$rtn = array("id", "3", "error", "something wrong happened");
        http_response_code(500);
        print json_encode($rtn);
    }
// =================================================================================



exit();
// =================================================================================
// SE CREA LA CONSULTA SQL EN FUNCION DE LOS PARAMETROS RECIBIDOS
// =================================================================================
switch($PARAMS['op'])
{
	case 1: 	// PEDIR DATOS DE LIBROS.
				  	// Parámetros:
						// - isbn: Optativo. Si se indica, sólo devuelve la información del libro con el
	        	//         isbn indicado, si existe. No haría caso al resto de parámetros, si los hubiera.
				  	// - i: Optativo. Indica registro inicial.
						// - c: Optativo. Indica cantidad de registros a devolver a partir del registro i.
			$mysql = 'select * from LIBRO';
			if(isset($PARAMS['isbn']))
				$mysql .= ' where isbn="' . mysqli_real_escape_string( $link, $PARAMS['isbn']) . '"'; // Escapa los caracteres especiales
			else
			{
				if( isset($PARAMS['c']) )
				{
					$mysql .= ' LIMIT ';
					if( isset($PARAMS['i']) )
						$mysql .= mysqli_real_escape_string($link, $PARAMS['i']);
					else $mysql .= '0';
					$mysql .= ',' . mysqli_real_escape_string($link, $PARAMS['c']);
				}
			}
		break;
	case 2: 	// PEDIR TODA LA INFORMACIÓN DE UN USUARIO.
				  	// Parámetros:
				  	// - u: Login de usuario.
				  	// - p: Password del usuario.
			$mysql  = 'select * from USUARIO where ';
			if( isset($PARAMS['u']) and isset($PARAMS['p']) )
			  $mysql .= 'login="' . mysqli_real_escape_string($link, $PARAMS['u']) . '" and password="' . mysqli_real_escape_string($link, $PARAMS['p']) . '"' ;
			else $mysql .= 'false';
		break;
	case 3: 	// PEDIR COMENTARIOS.
				  	// Parámetros:
				  	// - u: Optativo. Login del usuario por el que filtrar los comentarios.
						// - i: Optativo. Para pedir los comentarios de un libro concreto, es su isbn.
						// - c: Optativo. Cantidad de comentarios a devolver.
			$mysql  = 'select c.*, l.titulo,u.foto from COMENTARIO c, LIBRO l, USUARIO u ';
			$mysql .= 'where c.isbn=l.isbn and c.usuario=u.login';
			if( isset($PARAMS['u']) )
				$mysql .= ' and c.usuario="' . mysqli_real_escape_string($link, $PARAMS['u']) . '"' ;
			if( isset($PARAMS['i']) )
				$mysql .= ' and c.isbn="' . mysqli_real_escape_string($link, $PARAMS['i']) . '"' ;
			$mysql .= ' order by fecha desc';
			if( isset($PARAMS['c']) )
				$mysql .= ' limit 0,' . mysqli_real_escape_string($link, $PARAMS['c']);
		break;
	case 10:	// HACER LOGIN DE UN USUARIO.
						// Parámetros:
				  	// - u: Login del usuario
						// - p: Password del usuario
			$mysql  = 'select login, dni, password, nombre, apellidos, email, foto from USUARIO where login="' . mysqli_real_escape_string($link, $PARAMS['u']) . '"';
			if( $res = mysqli_query( $link, $mysql ) )
			{
				$R[] = mysqli_fetch_assoc( $res );
				if( $R[0]['password']==$PARAMS['p'] ) // login OK!!
					$_SESSION['login']=$R[0]['login'];
				else $R = array( "error"=>"Usuario/contraseña erróneo"); // El password no era correcto
			}
		break;
	case 11: 	// PEDIR LIBROS AL AZAR
					  // Parámetros:
						// - c: Indica cantidad de registros aleatorios a devolver.
			$mysql  = 'select * from LIBRO order by rand()';
			if( isset($PARAMS['c']) )	$mysql .= 'limit ' . mysqli_real_escape_string($link, $PARAMS['c']);
		break;
	case 20:	// REGISTRO DE NUEVO USUARIO
			$fichero = '';
			$login = mysqli_real_escape_string($link, $PARAMS['login']);

			$mysql  = 'insert into USUARIO(login,dni,nombre,apellidos,email,password) ';
			$mysql .= ' values(';
			$mysql .= '"' . $login . '","' . mysqli_real_escape_string($link, $PARAMS['dni']);
			$mysql .= '","' . mysqli_real_escape_string($link, $PARAMS['nombre']) . '","' . mysqli_real_escape_string($link, $PARAMS['apellidos']);
			$mysql .= '","' . mysqli_real_escape_string($link, $PARAMS['email']) . '","' . mysqli_real_escape_string($link, $PARAMS['password']);
			$mysql .= '")';
			if( $res = mysqli_query( $link, $mysql ) )
			{
				if(!empty($_FILES["foto"]) and $_FILES["foto"]["error"] == 0)
				{
					$fichero = guardarImagen($_FILES['foto'], $_PATH_FOTOS_USUARIOS, $login);
					if($fichero=='')
					{
						$R = array( "error"=>"No se pudo guardar el fichero");
					}
					else
					{
						$mysql = 'update USUARIO set foto="' . $fichero . '" where login="' . $login . '"';
						$res = mysqli_query( $link, $mysql );
					}
				}
				$_SESSION['login'] = $PARAMS['login'];
				$mysql  = 'select login, dni, nombre, apellidos, email, foto from USUARIO ';
				$mysql .= 'where login="' . $login . '"';
			}
			else $R = array( "error"=>"No se pudieron guardar los datos");
		break;
	case 21:	// MODIFICACION DE DATOS DEL USUARIO
			if( !isset($_SESSION['login']) ) exit();
			$fichero = '';
			$login = $_SESSION['login'];

			$mysql  = 'update USUARIO set dni="' . mysqli_real_escape_string($link,$PARAMS['dni']) . '", ';
			$mysql .= 'nombre="' . mysqli_real_escape_string($link,$PARAMS['nombre']) . '",';
			$mysql .= 'apellidos="' . mysqli_real_escape_string($link,$PARAMS['apellidos']) . '",';
			$mysql .= 'email="' . mysqli_real_escape_string($link,$PARAMS['email']) . '"';
			if( strlen($PARAMS['password'])>0 )
				$mysql .= ', password="' . mysqli_real_escape_string($link,$PARAMS['password']) . '"';
			$mysql .=' where login="' . $login . '"';
			if( $res = mysqli_query( $link, $mysql ) )
			{
				if(!empty($_FILES["foto"]) && $_FILES["foto"]["error"] == 0)
				{
					$fichero = guardarImagen($_FILES['foto'], $_PATH_FOTOS_USUARIOS, $login);
					if($fichero=='')
					{
						$R = array( "error"=>"No se pudo guardar el fichero");
					}
					else
					{
						$mysql = 'update USUARIO set foto="' . $fichero . '" where login="' . $login . '"';
						$res = mysqli_query( $link, $mysql );
					}
				}
				$mysql  = 'select login, dni, nombre, apellidos, email, foto from USUARIO ';
				$mysql .= 'where login="' .  $login . '"';
			}
			else $R = array( "error"=>"No se pudieron modificar los datos");
		break;
	case 22: 	// PREGUNTAR DISPONIBILIDAD DE LOGIN
						// Parámetros:
						// - u: Cadena de texto escrita en el campo login del formulario.
			if(isset($PARAMS['u']))
				$login = mysqli_real_escape_string($link,$PARAMS['u']);
			else
				$login = '';
			$mysql = 'select login from USUARIO where login="' . $login . '"';
		break;
	case 25: // REGISTRO DE NUEVO LIBRO
			if( !isset($_SESSION['login']) ) exit();
			$error = false;
			$mysql  = 'insert into LIBRO(isbn,titulo,autor,anyo,editorial,precio,formato,resumen) ';
			$mysql .= 'values(';
			$mysql .= '"' . mysqli_real_escape_string($link, $PARAMS['isbn']) . '","' . mysqli_real_escape_string($link, $PARAMS['titulo']);
			$mysql .= '","' . mysqli_real_escape_string($link, $PARAMS['autor']) . '","' . mysqli_real_escape_string($link, $PARAMS['anyo']);
			$mysql .= '","' . mysqli_real_escape_string($link, $PARAMS['editorial']) . '","' . mysqli_real_escape_string($link, $PARAMS['precio']);
			$mysql .= '","' . mysqli_real_escape_string($link, $PARAMS['formato']) . '","' . mysqli_real_escape_string($link, $PARAMS['resumen']);
			$mysql .= '")';
			if( $res = mysqli_query( $link, $mysql ) )
			{
				if(!empty($_FILES["foto"]) && $_FILES["foto"]["error"] == 0)
				{
					$target_path = $_PATH_PORTADAS_LIBROS;
					$fileName     = $_FILES["foto"]["name"]; 			// Nombre del archivo
					$fileTmpLoc   = $_FILES["foto"]["tmp_name"]; 	// Nombre del archivo en la carpeta temporal de php
					$fileType     = $_FILES["foto"]["type"]; 			// Tipo de archivo
					$fileSize     = $_FILES["foto"]["size"]; 			// Tamaño en bytes
					$fileErrorMsg = $_FILES["foto"]["error"]; 		// 0 para false (no hay error) ... y 1 para true (hay error)
					$ext = pathinfo($fileName, PATHINFO_EXTENSION);
					$allowed = array('jpg','png','gif');
					if ( ! in_array( $ext, $allowed ) )
				    unlink($fileTmpLoc); // Elimina el fichero subido de la carpeta temporal de php
				  else
				  {
						$target_path = $target_path . mysqli_real_escape_string($link, $PARAMS['isbn']) . '.' . $ext;
						if(file_exists($target_path))
							unlink( $target_path );
						if( move_uploaded_file( $fileTmpLoc, $target_path ) )
						{
							$nombreImagen = mysqli_real_escape_string($link, $PARAMS['isbn']) . '.' . $ext;
							$mysql = 'update LIBRO set imagen="' . $nombreImagen . '" where isbn="' . mysqli_real_escape_string($link, $PARAMS['isbn']) . '"' ;
							$res = mysqli_query( $link, $mysql );
						}
			   	}
			   }
				$mysql  = 'select * from LIBRO where isbn="' . mysqli_real_escape_string($link, $PARAMS['isbn']) . '"';
			}
			else $R = array( "error"=>"No se pudo guardar el registro");
		break;
	case 30: 	// BUSCAR LIBROS
						// Parámetros:
						// - t: Cadena de texto escrita en el campo de título
						// - a: Cadena de texto escrita en el campo de autor
						// - y: Valor escrito en el campo de año
						// - f: Formatos por los que filtrar, separados por espacios en blanco
			$mysql = 'select * from LIBRO ';
			if( isset($PARAMS['t']) or isset($PARAMS['a']) or isset($PARAMS['y']) or isset($PARAMS['f']) )
			{
				$and = false;
				$mysql .= ' where ';
				if(isset($PARAMS['t']))
				{
					$mysql .= 'titulo like "%' . mysqli_real_escape_string($link, $PARAMS['t']) . '%"';
					$and = true;
				}
				if(isset($PARAMS['a']))
				{
					if($and) $mysql .= ' and ';
					$mysql .= 'autor like "%' . mysqli_real_escape_string($link, $PARAMS['a']) . '%"';
					$and = true;
				}
				if(isset($PARAMS['y']))
				{
					if($and) $mysql .= ' and ';
					$mysql .= 'anyo=' . mysqli_real_escape_string($link, $PARAMS['y']);
					$and = true;
				}
				if(isset($PARAMS['f']))
				{
					if($and) $mysql .= ' and ';
					$valores = explode(" ", mysqli_real_escape_string($link, $PARAMS['f']));
					$mysql .= 'formato in(';
					foreach ($valores as $key => $value) {
						$mysql .= '"' . $value . '",';
					}
					$mysql = substr($mysql, 0, strlen($mysql) - 1) . ')';
					$and = true;
				}
			}
			$mysql .= ' order by titulo';
		break;
	case 40: 	// INSERTAR COMENTARIO
				if( !isset($_SESSION['login']) or !(isset($PARAMS['u']) and isset($PARAMS['t']) and isset($PARAMS['i']) and isset($PARAMS['v']) ) )
					exit();
				$mysql  = 'insert into COMENTARIO(usuario,texto,isbn,valoracion) ';
				$mysql .= 'values("' . mysqli_real_escape_string($link, $PARAMS['u']) . '","' . mysqli_real_escape_string($link, $PARAMS['t']) . '","';
				$mysql .= mysqli_real_escape_string($link, $PARAMS['i']) . '","' . mysqli_real_escape_string($link, $PARAMS['v']) . '")';
				if( $res = mysqli_query( $link, $mysql ) )
				{
					$mysql  = 'update LIBRO set numVotos=numVotos+1,valoracionTotal=valoracionTotal+' . mysqli_real_escape_string($link, $PARAMS['v']);
					$mysql .= ' where isbn="' . mysqli_real_escape_string($link, $PARAMS['i']) . '"';
				}
		break;
	case 101: // HACER LOGOUT
			if( isset($_SESSION['login']) && $_SESSION['login']==$PARAMS['u'])
			{
				unset($_SESSION['login']);
				$R = array( "ok"=>"logout ok!");
			}
			else $R = array( "error"=>"No se pudo hacer logout");
		break;
	case 200: // PEDIR FORMATOS PARA DATALIST
			$mysql = 'select distinct formato from LIBRO';
		break;
	case 201: // PEDIR AUTORES PARA DATALIST
			$mysql = 'select distinct autor from LIBRO';
		break;
	case 202: // PEDIR EDITORIALES PARA DATALIST
			$mysql = 'select distinct editorial from LIBRO';
		break;
}
// =================================================================================
// SE HACE LA CONSULTA
// =================================================================================
if( count($R)==0 && $res = mysqli_query( $link, $mysql ) )
{
  if( substr($mysql, 0, 6) == "select" )
  {
    while( $row = mysqli_fetch_assoc( $res ) )
      $R[] = $row;
    mysqli_free_result( $res );
  }
  else $R[] = $res;
}
// =================================================================================
// SE CIERRA LA CONEXION CON LA BD
// =================================================================================
mysqli_close($link);
// =================================================================================
// SE DEVUELVE EL RESULTADO DE LA CONSULTA
// =================================================================================
echo json_encode($R);
?>