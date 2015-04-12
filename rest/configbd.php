<?php
// =================================================================================
// INCLUSION DE FUNCIONES EXTERNAS
// =================================================================================
require_once('funciones.php');
// =================================================================================
// =================================================================================
// CONFIGURACION DE ACCESO A LA BASE DE DATOS:
// =================================================================================
$_server   = "127.0.0.1:3306";
$_dataBase = "excursionismo";
$_user     = "ph2";
$_password = "ph2";
// =================================================================================
// SE ABRE LA CONEXION A LA BD
// =================================================================================
$link =  mysqli_connect($_server, $_user, $_password, $_dataBase);
if (mysqli_connect_errno()) {
  printf("Fallo en la conexión: %s\n", mysqli_connect_error());
  exit();
}
// =================================================================================
// SE CONFIGURA EL JUEGO DE CARACTERES DE LA CONEXION A UTF-8
// =================================================================================
mysqli_set_charset($link, 'utf8');
// =================================================================================
// =================================================================================

// =================================================================================
// Comprueba si el usuario está logueado y la clave es válida:
// =================================================================================
function comprobarSesion($login, $clave)
{
	global $link;
	global $tiempo_de_sesion;
  $valorRet = false;
  $mysql  = 'select * from USUARIO where LOGIN="' . $login . '"';
  $mysql .= ' and CLAVE="' . $clave . '"';
  $mysql .= ' and UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(TIEMPO)<' . $tiempo_de_sesion;
  if( $res = mysqli_query($link, $mysql) )
  {
    if(mysqli_num_rows($res)==1) $valorRet = true;
  }
  else
  {
    $RESPONSE_CODE = 500;
    print json_encode( array('resultado' => 'error', 'descripcion' => 'Error de servidor.') );
    exit();
  }
  return $valorRet;
}
// =================================================================================
?>