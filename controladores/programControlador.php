<?php


  function programDespachos2() {
  	//Fue el primer intento
    require 'vistas/programDespacho.php';
  }


  function programDespachos() {
    require 'vistas/programarDespachos.php';
  }

  function listarTelefonos(){
    require 'librerias/config.ini.php';
    $arrOperadores = explode(",",$operadoresTelf);
  	require 'vistas/listarTelefonos.php';
  }


?>
