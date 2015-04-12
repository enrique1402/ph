<?php
// ============================================================
// PARAMETROS DE CONFIGURACION
// ============================================================
$uploaddir = '../../fotos/';
$uploaddirthumbnails = $uploaddir . 'thumbnails/' ;
$final_width_of_image = 240; // en píxeles
$tiempo_de_sesion = 1800000; // 30 minutos * 60 segundos = 1800 segundos

function es_fecha( $str ){
    $stamp = strtotime( $str );
    if (!is_numeric($stamp) || !preg_match("^\d{4}-\d{2}-\d{2}^", $str))
        return FALSE;
    $month = date( 'm', $stamp );
    $day   = date( 'd', $stamp );
    $year  = date( 'Y', $stamp );
    if (checkdate($month, $day, $year))
        return TRUE;
    return FALSE;
}
?>