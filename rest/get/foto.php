<?php
// FICHERO: rest/get/foto.php

$METODO = $_SERVER['REQUEST_METHOD'];
// EL METODO DEBE SER GET. SI NO LO ES, NO SE HACE NADA.
if($METODO<>'GET') exit();
// PETICIONES GET ADMITIDAS:
//   rest/foto/{ID_FOTO}  -> devuelve toda la información de la foto
//   rest/foto/?idr={ID_RUTA}  -> devuelve un array con todas las fotos de la ruta cuyo id es el que se le pasa

// =================================================================================
// =================================================================================
// INCLUSION DE LA CONEXION A LA BD
   require_once('../configbd.php');
// =================================================================================
// =================================================================================
$RECURSO = array();
$RECURSO = explode("/", $_GET['prm']);
$PARAMS = array_slice($_GET, 1, count($_GET) - 1,true);
$IDFOTO = $RECURSO[0];
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
$mysql='';
// =================================================================================
if(is_numeric($IDFOTO))
{ // Se debe devolver la información de la foto
	$mysql  = 'select * from FOTO where ID=' . mysqli_real_escape_string($link,$IDFOTO);
}
elseif(isset($PARAMS['idr']) && is_numeric($PARAMS['idr']))
{ // se piden todas las fotos de una salida
	$mysql = 'select * from FOTO where ID_RUTA=' . mysqli_real_escape_string($link,$PARAMS['idr']);
}
else
{
	$RESPONSE_CODE = 400; // Los parámetros no son correctos
	$R = array("resultado" => "error", "codigo", "2", "error", "Los parámetros no son correctos");
}
// =================================================================================
// SE HACE LA CONSULTA
// =================================================================================
if( strlen($mysql)>0 && count($R)==0 && $res = mysqli_query( $link, $mysql ) )
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
if(strlen($mysql)<1)
{
	$R = array("resultado" => "error", "codigo", "2", "descripcion", "Los parámetros no son correctos.");
  $RESPONSE_CODE = 500;
}
http_response_code($RESPONSE_CODE);
print json_encode($R);
?>