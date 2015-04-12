<?php
// FICHERO: rest/post/ruta.php

$METODO = $_SERVER['REQUEST_METHOD'];
// EL METODO DEBE SER POST. SI NO LO ES, NO SE HACE NADA.
if($METODO<>'POST') exit();
// PETICIONES POST ADMITIDAS:
//   rest/ruta/

// =================================================================================
// =================================================================================
// INCLUSION DE LA CONEXION A LA BD
require_once('../configbd.php');
// =================================================================================
// =================================================================================

$PARAMS   = $_POST;
$FICHEROS = $_FILES;

// =================================================================================
// CREAR THUMBNAIL
// =================================================================================
function crearThumbnail($uploaddir, $uploaddirthumbnails, $anchoImgThumbnail, $filename)
{
  if(preg_match('/[.](jpg)$/', $filename)) {
      $im = imagecreatefromjpeg($uploaddir . $filename);
  } else if (preg_match('/[.](gif)$/', $filename)) {
      $im = imagecreatefromgif($uploaddir . $filename);
  } else if (preg_match('/[.](png)$/', $filename)) {
      $im = imagecreatefrompng($uploaddir . $filename);
  }
  $ox = imagesx($im); // ancho de la imagen
  $oy = imagesy($im); // alto de la imagen
  $nx = $anchoImgThumbnail;
  $ny = floor($oy * ($anchoImgThumbnail / $ox));
  $nm = imagecreatetruecolor($nx, $ny);
  imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
  if(!file_exists($uploaddirthumbnails)) {
    if(!mkdir($uploaddirthumbnails)) {
      die("Hubo un problema al subir la foto.");
    }
  }
  imagejpeg($nm, $uploaddirthumbnails . $filename);
  return true;
}

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
// Se supone que si llega aquí es porque todo ha ido bien y tenemos los datos correctos:
$PARAMS      = $_POST;
$clave       = mysqli_real_escape_string( $link, $PARAMS['clave']);
$login       = mysqli_real_escape_string( $link, $PARAMS['login']);
$fecha       = mysqli_real_escape_string( $link, $PARAMS['fecha']);
$nombre      = mysqli_real_escape_string( $link, $PARAMS['nombre']);
$recorrido   = mysqli_real_escape_string( $link, nl2br($PARAMS['recorrido'],false));
$descripcion = mysqli_real_escape_string( $link, nl2br($PARAMS['descripcion'],false));
$dificultad  = mysqli_real_escape_string( $link, $PARAMS['dificultad']);
$distancia   = mysqli_real_escape_string( $link, $PARAMS['distancia']);
$piesdefoto  = $PARAMS['piefoto'];
$FICHEROS = $_FILES;

if( !comprobarSesion($login,$clave) )
  $R = array('resultado' => 'ok', 'descripcion' => 'Tiempo de sesión agotado.');
else
{
    // =================================================================================
    // Primero se guarda la salida:
    // ... se inicia la transacción
    try{
      mysqli_query($link, "BEGIN");
      $mysql  = "insert into RUTA(FECHA,NOMBRE,RECORRIDO,DESCRIPCION,DIFICULTAD,DISTANCIA,LOGIN) ";
      $mysql .= "values('" . $fecha . "','" . $nombre . "','" . $recorrido . "','" . $descripcion;
      $mysql .= "','" . $dificultad . "','" . $distancia . "','" . $login . "')";
      if( mysqli_query($link,$mysql) )
      { // Se han insertado los datos de la ruta.
        // Se saca el id de la ruta y se insertan las fotos ...
        $mysql = "select * from RUTA order by ID desc limit 0,1";
        if( $res = mysqli_query($link,$mysql) )
        {
          $ruta = mysqli_fetch_assoc($res);
          $IDRUTA = $ruta['ID'];
          // ================================================
          // Ahora se insertan las fotos:
          // ================================================
          for($i=0;$i<count($FICHEROS['fotos']['name']);$i++)
          {
            // **********************************************
            if(!empty( $FICHEROS['fotos']['tmp_name'][$i]) )
            {
              $mysql  = 'insert into FOTO(DESCRIPCION,ID_RUTA) values(';
              $mysql .= '"' . mysqli_real_escape_string( $link, $piesdefoto[$i] ) . '",';
              $mysql .= $IDRUTA . ')';
              if( mysqli_query($link, $mysql))
              {
                $mysql = "select ID from FOTO order by ID desc limit 0,1";
                if( $res=mysqli_query($link, $mysql) )
                {
                  $foto = mysqli_fetch_assoc($res);
                  $ext = pathinfo($FICHEROS['fotos']['name'][$i], PATHINFO_EXTENSION);
                  $uploadfile = $uploaddir . $foto['ID'] . '.' . $ext;
                  // ------------------------------------------------------------
                  // Se actualiza el nombre del fichero asociado a la foto:
                  $mysql = 'update FOTO set ARCHIVO="' . $foto['ID'] . '.' . $ext .'" where ID=' . $foto['ID'];
                  mysqli_query($link, $mysql);
                  // ------------------------------------------------------------
                  if(move_uploaded_file($FICHEROS['fotos']['tmp_name'][$i], $uploadfile))
                  {
                    crearThumbnail($uploaddir, $uploaddirthumbnails, $final_width_of_image, $foto['ID'] . '.' . $ext);
                  } else {
                    echo "¡Posible ataque de carga de archivos!\n";
                  }
                }
              } // if( mysqli_query($link, $mysql))
            }
            // **********************************************
          }
        }
        $R = array('resultado' => 'ok', 'idruta' => $IDRUTA);
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
} // if( !comprobarSesion($login,$clave) )

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
try {
    // Here: everything went ok. So before returning JSON, you can setup HTTP status code too
    http_response_code($RESPONSE_CODE);
    print json_encode($R);
}
catch (SomeException $ex) {
    $rtn = array("id", "3", "error", "something wrong happened");
    http_response_code(500);
    print json_encode($rtn);
}
// =================================================================================


?>