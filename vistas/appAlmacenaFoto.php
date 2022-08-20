<?php
  //header('Content-type : bitmap; charset=utf-8');

  /*require_once '../librerias/config.ini.php';
  require_once '../librerias/conectar.php';
  require_once '../librerias/varios.php';
  require '../modelos/movilModelo.php';
  logCadena($db,'dato','observacion');
*/  

  //$cadena2 = json_decode(utf8_decode($_REQUEST["encoded_string"]));
  //$cadena3 = serialize($cadena2);
  //logCadena($db,$_REQUEST["image_name"],'999-prueba');

  if(isset($_REQUEST["encoded_string"])){
    $encoded_string = $_REQUEST["encoded_string"];
    $image_name = $_REQUEST["image_name"];

    $fchDespacho = substr($image_name,0,10);
    //echo $fchDespacho.", ";
    $posIniCorrel =  strpos($image_name ,'-',9 );
    // echo $posIniCorrel."****";
    $posAux = strrpos($image_name, "-");
    $posAux2 = strrpos($image_name, "_");
    //echo $posAux." ".$posAux2;
    $correl = substr ($image_name ,$posIniCorrel +1 , $posAux - $posIniCorrel -1 );
    $corrPunto = substr ($image_name ,$posAux +1 , $posAux2 - $posAux -1 );

    $decoded_string = base64_decode($encoded_string);
    $path = '../imagenes/data/puntosdespacho/'.$image_name;
    if (!is_file($path)){
      //echo "lo graba";
      $file = fopen($path, 'wb');
      $is_written = fwrite($file, $decoded_string);
      fclose($file);
      require_once '../librerias/config.ini.php';
      require_once '../librerias/conectar.php';
      require_once '../librerias/varios.php';
      require '../modelos/movilModelo.php';
      logCadena($db,$image_name,'observacion');
      if($is_written > 0){
        insertaImagen($db,$fchDespacho,$correl, $corrPunto ,$image_name);
      }
    } else {
      //echo "Ya existe el archivo";
    }

  }
 

?>
