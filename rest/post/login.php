<?php
// FICHERO: rest/post/login.php

$METODO = $_SERVER['REQUEST_METHOD'];
// EL METODO DEBE SER POST. SI NO LO ES, NO SE HACE NADA.
if($METODO<>'POST') exit();
// PETICIONES POST ADMITIDAS:
//   rest/login/
// Los parámetros serán el usuario y la contraseña.
// Si el login es correcto se devuelve el login y la clave (key)

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
$pwd = mysqli_real_escape_string( $link, $PARAMS['pwd']);
if($PARAMS['usu']=='')
{
  $RESPONSE_CODE = 401;
  $R = array('resultado' => 'error', 'descripcion' => 'login no correcto');
}
else
{
  // Se comprueba que el login sea correcto:
  try{
    // ******** INICIO DE TRANSACCION **********
    mysqli_query($link, "BEGIN");
    $mysql = "select * from USUARIO where LOGIN='" . $usu . "'";
    if( $res = mysqli_query( $link, $mysql ) )
    {
      $row = mysqli_fetch_assoc( $res );

      if( mysqli_num_rows($res)>0 && $row['PASSWORD'] == $pwd )
      {
          $tiempo = time();
          $key = md5( $pwd . date('YmdHis', $tiempo) );
          $mysql  = 'update USUARIO set CLAVE="' . $key . '", TIEMPO="' . date('Y-m-d H:i:s', $tiempo);
          $mysql .= '" where LOGIN="' . $usu . '"';
          if( $res = mysqli_query( $link, $mysql ) )
          {
            $R = array('resultado' => 'ok', 'clave' => $key,
                       'login' => $usu, 'nombre' => $row['NOMBRE']);
          }
          else
          {
            $RESPONSE_CODE = 500;
            $error = 'Se ha producido un error en el servidor.';
            $R = array('resultado' => 'error', 'descripción' => $error);
            throw new Exception($error);
          }
        }
        else
        {
            $RESPONSE_CODE = 401;
            $R = array('resultado' => 'error', 'descripción' => 'login no correcto');
        }

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