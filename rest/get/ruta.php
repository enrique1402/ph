<?php
// FICHERO: rest/get/ruta.php
// =================================================================================
// =================================================================================
// INCLUSION DE LA CONEXION A LA BD
   require_once('../configbd.php');
// =================================================================================
// =================================================================================
$METODO = $_SERVER['REQUEST_METHOD'];
// EL METODO DEBE SER GET. SI NO LO ES, NO SE HACE NADA.
if($METODO<>'GET') exit();
// PETICIONES GET ADMITIDAS:
//   rest/ruta/{ID_RUTA}  -> devuelve toda la información de la ruta
//	 rest/ruta/?u={número}	-> devuelve las últimas 'número' rutas, ordenadas de más a menos recientes
//	 rest/ruta/?t={título}
//	 rest/ruta/?r={recorrido}
//	 rest/ruta/?d={descripción}
//   rest/ruta/?fi={aaaa-mm-dd}&ff={aaaa-mm-dd}  -> rutas entre las dos fechas
//   rest/ruta/?di={dd.dd}&df={dd.dd}  -> rutas entre las dos distancias
//   rest/ruta/?dfi={d}&dff={d}  -> rutas entre las dos dificultades

$RECURSO = array();
$RECURSO = explode("/", $_GET['prm']);
$PARAMS = array_slice($_GET, 1, count($_GET) - 1,true);
$ID = $RECURSO[0];
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
if(is_numeric($ID))
{ // Se debe devolver la información de la ruta
	$mysql  = 'select * from RUTA where ID=' . mysqli_real_escape_string($link,$ID);
}
else
{ // Se utilizan parámetros
	if(isset($PARAMS['u']) && is_numeric($PARAMS['u'])){
		$mysql  = 'select r.*,';
		$mysql .= '(select f.ARCHIVO from FOTO f where f.ID_RUTA=r.ID order by f.ID LIMIT 0,1) as ARCHIVO,';
		$mysql .= '(select count(*) from FOTO f where f.ID_RUTA=r.ID) as NFOTOS,';
		$mysql .= '(select count(*) from COMENTARIO c where c.ID_RUTA=r.ID) as NCOMENTARIOS';
		$mysql .= ' FROM RUTA r order by r.FECHA desc LIMIT 0,' . mysqli_real_escape_string($link,$PARAMS['u']);
	}
	else
	{
		$mysql  = 'select r.*, ';
		$mysql .= '(select f.ARCHIVO from FOTO f where f.ID_RUTA=r.ID order by f.ID LIMIT 0,1) as ARCHIVO, ';
		$mysql .= '(select count(*) from FOTO f where f.ID_RUTA=r.ID) as NFOTOS, ';
		$mysql .= '(select count(*) from COMENTARIO c where c.ID_RUTA=r.ID) as NCOMENTARIOS ';
		$mysql .= 'from RUTA r where false';
		if(isset($PARAMS['fi']) && isset($PARAMS['ff']) && es_fecha($PARAMS['fi']) )
		{
			$mysql .= ' or FECHA between "' . mysqli_real_escape_string($link,$PARAMS['fi']);
			$mysql .= '" and "' . mysqli_real_escape_string($link,$PARAMS['ff']) . '"';
		}
		if(isset($PARAMS['di']) && isset($PARAMS['df']) && is_numeric($PARAMS['di']) && is_numeric($PARAMS['df']) )
		{
			$mysql .= ' or DISTANCIA between "' . mysqli_real_escape_string($link,$PARAMS['di']);
			$mysql .= '" and "' . mysqli_real_escape_string($link,$PARAMS['df']) . '"';
		}
		if(isset($PARAMS['dfi']) && isset($PARAMS['dff']) && is_numeric($PARAMS['dfi']) && is_numeric($PARAMS['dff']) )
		{
			$mysql .= ' or DIFICULTAD between ' . mysqli_real_escape_string($link,$PARAMS['dfi']);
			$mysql .= ' and ' . mysqli_real_escape_string($link,$PARAMS['dff']);
		}
		if( isset($PARAMS['t']) )
			$mysql .= ' or NOMBRE like "%' . mysqli_real_escape_string($link,$PARAMS['t']) . '%"';
		if( isset($PARAMS['r']) )
			$mysql .= ' or RECORRIDO like "%' . mysqli_real_escape_string($link,$PARAMS['r']) . '%"';
		if( isset($PARAMS['d']) )
			$mysql .= ' or DESCRIPCION like "%' . mysqli_real_escape_string($link,$PARAMS['d']) . '%"';
	}
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