<?php
// FICHERO: rest/post/login.php

$METODO = $_SERVER['REQUEST_METHOD'];
// EL METODO DEBE SER POST. SI NO LO ES, NO SE HACE NADA.
if($METODO<>'POST') exit();
// PETICIONES POST ADMITIDAS:
//   rest/registro/

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
// Se supone que si llega aquí es porque todo ha ido bien y tenemos los datos correctos:
$PARAMS      = $_POST;
// Se pillan el usuario y el login:
$usu = mysqli_real_escape_string( $link, $PARAMS['usu']);
$pwd = mysqli_real_escape_string( $link, $PARAMS['pwd1']);
$nombre = mysqli_real_escape_string( $link, $PARAMS['nombre']);
$email = mysqli_real_escape_string( $link, $PARAMS['email']);

if($PARAMS['usu']=='')
{
  $RESPONSE_CODE = 401;
  $R = array('resultado' => 'error', 'descripcion' => 'login no correcto');
}
else
{
  try{
    // ******** INICIO DE TRANSACCION **********
    mysqli_query($link, 'BEGIN');
    $mysql  = 'insert into USUARIO(LOGIN,PASSWORD,NOMBRE,EMAIL) values("';
    $mysql .= $usu . '","' . $pwd . '","' . $nombre . '","' . $email . '")';
    if( mysqli_query( $link, $mysql ) )
    {
      $R = array('resultado' => 'ok', 'login' => $usu);
    }
    else
    {
      $R = array('resultado' => 'error', 'descripcion' => 'No se ha podido hacer el registro');
    }
    // ******** FIN DE TRANSACCION **********
    mysqli_query($link, "COMMIT");
  } catch(Exception $e){
    // Se ha producido un error, se cancela la transacción.
    mysqli_query($link, "ROLLBACK");
  }
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
    $rtn = array('resultado' => 'error', 'descripción' => "Se ha producido un error");
    http_response_code(500);
    print json_encode($rtn);
}
// =================================================================================
?>