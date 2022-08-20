<?php
global $servidor, $bd, $usuario, $contrasenia;
$db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);

/*function buscarClienteCuentas($db,$idCliente){
	$consulta = $db->prepare("SELECT * FROM clientecuenta WHERE idCliente = :idCliente ");
	//$consulta = $db->prepare("SELECT * FROM clientecuenta WHERE idCliente = '20307328471' ");
	$consulta->bindParam(':idCliente',$idCliente);
	$consulta->execute();
	return $consulta->fetchAll();
}
*/


function buscarAuxiliaresOcupados($db,$fchDespacho){

  $consulta = $db->prepare("SELECT `despachopersonal`.idTrabajador  FROM `despachopersonal`,`despacho` WHERE `despachopersonal`.fchDespacho = `despacho`.fchDespacho AND `despachopersonal`.correlativo = `despacho`.correlativo AND `despacho`.concluido = 'No' AND `despacho`.fchDespacho = :fchDespacho");
  $consulta->bindParam(':fchDespacho',$fchDespacho);	
	$consulta->execute();
	return $consulta->fetchAll();

}





?>
