<?php
  global $servidor, $bd, $usuario, $contrasenia;
  $db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);

  function buscarProgramConfirmadoNoEnviado($db, $nroDias = 5){ 
  	$consulta = $db->prepare("SELECT `id`, `fchDespacho`, `cliente`.nombre AS nombCliente, `correlCuenta`, `cuenta`, `idProducto`, `nombProducto`, `tipoCuenta`, `hraInicioEsperada`, `lugarInicio`, `valorServicio`, `placa`, `movilAsignado`, `observacion`, `estadoProgram`, `programdespacho`.`creacUsuario`, `programdespacho`.`creacFch`, `programdespacho`.`editaUsuario`, `programdespacho`.`editaFch`  FROM `programdespacho`, cliente WHERE `programdespacho`.idCliente = `cliente`.idRuc AND  (DATEDIFF( curdate(), fchDespacho) >= 0 AND   DATEDIFF(curdate(), fchDespacho ) <= '$nroDias' )  AND `estadoProgram` LIKE 'Confirmado'");
 	  $consulta->execute(); 
 	  return $consulta->fetchAll();
  }





?>
