<?php
global $servidor, $bd, $usuario, $contrasenia;
$db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);

function buscarTodosLosClientes($db, $estadoCliente = NULL){
  $where = $estadoCliente == NULL?"":" WHERE estadoCliente = :estadoCliente ";
	//$consulta = $db->prepare("SELECT * FROM cliente");
	$consulta = $db->prepare("SELECT `cliente`.`idRuc`, `rznSocial`, `nombre`, `dirCalleNro`, `dirDistrito`, `actividad`, `idCoordinador`,  `clientecontacto`.`idNombreCompleto` , `clientecontacto`.`telefono`   FROM `cliente` LEFT JOIN  `clientecontacto` ON `cliente`.`idRuc` = `clientecontacto`.`idRuc` AND `clientecontacto`.`telefono` is not null $where Group By `cliente`.`idRuc` ORDER BY `nombre`"); 
	if ($estadoCliente != NULL) $consulta->bindParam(':estadoCliente',$estadoCliente); 
  $consulta->execute();
	return $consulta->fetchAll();
}

function buscarClientesActivos($db){
/*	$consulta = $db->prepare("SELECT `cliente`.`idRuc`, `rznSocial`, `nombre`, `dirCalleNro`, `dirDistrito`, `actividad`, `idCoordinador`,  `clientecontacto`.`idNombreCompleto` , `clientecontacto`.`telefono`   FROM `cliente` LEFT JOIN  `clientecontacto` ON `cliente`.`idRuc` = `clientecontacto`.`idRuc` AND `clientecontacto`.`telefono` is not null WHERE  `cliente`.`estadoCliente` = 'activo'  Group By `cliente`.`idRuc` ORDER BY `nombre`"); 
	$consulta->execute();
	return $consulta->fetchAll();
*/

  $consulta = $db->prepare("SELECT `cliente`.idRuc, rznSocial, nombre, dirCalleNro, `cliente`.dirDistrito, actividad, maximoHras, idCoordinador, concat(`trabajador`.nombres,' ',`trabajador`.apPaterno,' ',`trabajador`.apMaterno) as nombreCompleto , estadoCliente, categCliente, pagWeb, fax, diasPago, `cliente`.formaPago, `cliente`.bancoNombre, `cliente`.bancoCuentaNro, `cliente`.bancoCuentaTipo, `cliente`.idCondicion, `cliente`.bancoCuentaMoneda, `clicondpag`.nroDias, `clicondpag`.nombCondicion FROM cliente LEFT JOIN trabajador ON `cliente`.idCoordinador = `trabajador`.idTrabajador LEFT JOIN clientecondicionespago AS clicondpag ON `cliente`.idCondicion = `clicondpag`.idCondicion WHERE  `cliente`.`estadoCliente` = 'activo'  ");
  $consulta->bindParam(':id',$id);
  $consulta->execute();
  return $consulta->fetchAll();

}
/*
function buscarClientesActivos($db){
  $consulta = $db->prepare("SELECT `cliente`.`idRuc`, `rznSocial`, `nombre`, `dirCalleNro`, `dirDistrito`, `actividad`, `idCoordinador`,  `clientecontacto`.`idNombreCompleto` , `clientecontacto`.`telefono`   FROM `cliente` LEFT JOIN  `clientecontacto` ON `cliente`.`idRuc` = `clientecontacto`.`idRuc` AND `clientecontacto`.`telefono` is not null WHERE  `cliente`.`estadoCliente` = 'activo'  Group By `cliente`.`idRuc` ORDER BY `nombre`"); 
  $consulta->execute();
  return $consulta->fetchAll();
}
*/

function buscarCliente($db,$id){
	//$consulta = $db->prepare("SELECT cliente.idRuc, rznSocial, nombre, dirCalleNro, `cliente`.dirDistrito, actividad, maximoHras, idCoordinador, concat(`trabajador`.nombres,' ',`trabajador`.apPaterno,' ',`trabajador`.apMaterno) as nombreCompleto , estadoCliente, categCliente, pagWeb, fax, diasPago, `cliente`.formaPago, `cliente`.bancoNombre, `cliente`.bancoCuentaNro, `cliente`.bancoCuentaTipo, `cliente`.bancoCuentaMoneda, idNombreCompleto, telefono FROM cliente LEFT JOIN trabajador ON cliente.idCoordinador = trabajador.idTrabajador LEFT JOIN clientecontacto ON cliente.idRuc = clientecontacto.idRuc WHERE cliente.idRuc = :id  ");
    $consulta = $db->prepare("SELECT `cliente`.idRuc, rznSocial, nombre, dirCalleNro, `cliente`.dirDistrito, actividad, maximoHras, idCoordinador, concat(`trabajador`.nombres,' ',`trabajador`.apPaterno,' ',`trabajador`.apMaterno) as nombreCompleto , estadoCliente, categCliente, pagWeb, fax, diasPago, `cliente`.formaPago, `cliente`.bancoNombre, `cliente`.bancoCuentaNro, `cliente`.bancoCuentaTipo, `cliente`.idCondicion, `cliente`.bancoCuentaMoneda, `clicondpag`.nroDias, `clicondpag`.nombCondicion FROM cliente LEFT JOIN trabajador ON `cliente`.idCoordinador = `trabajador`.idTrabajador LEFT JOIN clientecondicionespago AS clicondpag ON `cliente`.idCondicion = `clicondpag`.idCondicion WHERE `cliente`.idRuc = :id ");
	$consulta->bindParam(':id',$id);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarClienteCuentas($db,$id, $estado = null){
  $where = ($estado != null )?" AND estadoCuenta = '$estado' ":"";
	$consulta = $db->prepare("SELECT * FROM clientecuenta WHERE idCliente = :id $where ");
	$consulta->bindParam(':id',$id);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarClienteCuenta($db,$id,$tipoServicio){
	$consulta = $db->prepare("SELECT `clientecuenta`.`idCliente`,  `clientecuenta`.`tipoServicio`, `clientecuenta`.`tipoServicioPago`, `valorServicio`, `valorServicioHraExtra`, `topeServicioHraNormal`, `toleranCobroHraExtra`, `valorConductor`, `valorAuxiliar`, `nroAuxiliares`, `valorAuxAdicional`, `usarMaster`, `kilometrajeEsperado`, `estadoCuenta`, `paraMovil`,  `clientecuenta`.`creacUsuario`,  `clientecuenta`.`creacFch`, `clientecuenta`.`editUsuario`, `clientecuenta`.`editFch`, clientecuentainfoadic.`opciones` FROM `clientecuenta` LEFT JOIN clientecuentainfoadic ON  `clientecuenta`.`idCliente` =  clientecuentainfoadic.`idCliente` AND  `clientecuenta`.`tipoServicio` =  clientecuentainfoadic.`tipoServicio`  WHERE  `clientecuenta`.idCliente = :id AND  `clientecuenta`.tipoServicio = :tipoServicio ");
	$consulta->bindParam(':id',$id);
	$consulta->bindParam(':tipoServicio',$tipoServicio);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarClienteContactos($db,$id){
	$consulta = $db->prepare("SELECT * FROM clientecontacto WHERE idRuc = :id  ");
	$consulta->bindParam(':id',$id);
	$consulta->execute();
	return $consulta->fetchAll();
}

/* 
Eliminarla si ya no se va a utilizar
function buscarInfoClienteCuenta($db){
  $consulta = $db->prepare("SELECT concat(`idCliente`,'-', `tipoServicio`,'-',`nombre`) AS clienteCuenta FROM `clientecuenta`, cliente WHERE `clientecuenta`.idCliente = `cliente`.`idRuc`");
  $consulta->execute();
  return $consulta->fetchAll();
}
*/

function buscarContacto($db,$nombre){
  $consulta=$db->prepare("SELECT `idNombreCompleto`, `cargo`, `telefono`, `email`, `idRuc` FROM `clientecontacto` WHERE `idNombreCompleto` = :nombre");
  $consulta->bindParam(':nombre',$nombre);
  $consulta->execute();
  return $consulta->fetchAll();
}

function generaCorrelativo($db,$idCliente){
  $consulta = $db->prepare("SELECT `correlativo` FROM `clienteubicacion` WHERE `idCliente` = :idCliente ORDER BY `correlativo` DESC LIMIT 1 ");
  $consulta->bindParam(':idCliente',$idCliente);
  $consulta->execute();
  $aux = $consulta->fetchAll();
  $sgteCorrel = 1;
  foreach ($aux as $item) {
    $sgteCorrel = 1*$item['correlativo'] + 1;
  }
  return $sgteCorrel;
}

function buscarCliUbicaciones($db,$id,$correlativo = NULL){
  $where = $correlativo == NULL?"":" AND correlativo =  :correlativo ";
  $consulta = $db->prepare("SELECT `idCliente`, `correlativo`, `nombUbicacion`, `descripcion`, `creacUsuario`, `creacFch`  FROM `clienteubicacion` WHERE `idCliente`= :id $where ");
  $consulta->bindParam(':id',$id);
  if ($correlativo != NULL) $consulta->bindParam(':correlativo',$correlativo);
  $consulta->execute();
  return $consulta->fetchAll();
}

function insertaUbicacion($db,$idCliente,$nombre,$descripcion){
  $correl =  generaCorrelativo($db,$idCliente);
  $usuario = $_SESSION["usuario"];
  //Intenta generar documento
  $inserta = $db->prepare("INSERT INTO `clienteubicacion` (`idCliente`, `correlativo`, `nombUbicacion`, `descripcion`, `creacUsuario`, `creacFch`) VALUES (:idCliente, :correl, :nombre, :descripcion, :usuario, now())");
  $inserta->bindParam(':idCliente',$idCliente);
  $inserta->bindParam(':correl',$correl);
  $inserta->bindParam(':nombre',$nombre);
  $inserta->bindParam(':descripcion',$descripcion);
  $inserta->bindParam(':usuario',$usuario);  
  $inserta->execute();  
  return $inserta->rowCount();
}

function modificaUbicacion($db,$idCliente,$correl,$nombre,$descripcion){
  $usuario = $_SESSION["usuario"];
  //echo "$idCliente, $correl, $nombre, $descripcion, $usuario";
  $actualiza = $db->prepare("UPDATE `clienteubicacion` SET nombUbicacion = :nombre, `descripcion` = :descripcion , editaUsuario = :usuario, editaFch = curdate() WHERE `idCliente` = :idCliente AND `correlativo` = :correl");
  $actualiza->bindParam(':idCliente',$idCliente);
  $actualiza->bindParam(':correl',$correl);
  $actualiza->bindParam(':nombre',$nombre);
  $actualiza->bindParam(':descripcion',$descripcion);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();  
  return $actualiza->rowCount();
}

function eliminaCliUbicacion($db,$idCliente,$correl){
  $elimina = $db->prepare("DELETE FROM `clienteubicacion` WHERE `idCliente` = :idCliente AND `correlativo` = :correl");
  $elimina->bindParam(':idCliente',$idCliente);
  $elimina->bindParam(':correl',$correl);
  $elimina->execute();  
  return $elimina->rowCount();
}


function buscarDespachosRipleyNoVerificados($db,$cuenta){
  //$consulta = $db->prepare("SELECT * FROM `despacho`  WHERE `idCliente` = '20337564373' and concluido = 'Si' AND `cuenta` = :cuenta AND (`fchDespacho`,`correlativo` ) NOT IN (SELECT `fchDespacho`,`correlativo` FROM `despachoripley`)");
  $consulta = $db->prepare("SELECT `despacho`.`fchDespacho`, `despacho`.`correlativo`, `despacho`.`hraInicio`, `despacho`.`placa`,  `despacho`.`idCliente`, `despacho`.`cuenta`,  `despachoripley`.`guias`, `despachoripley`.`despachos`,`despachoripley`.`zona`, `despachoripley`.`costo` FROM `despacho` LEFT JOIN `despachoripley` ON `despacho`.`fchDespacho` = `despachoripley`.`fchDespacho` AND `despacho`.`correlativo` = `despachoripley`.`correlativo`   WHERE `idCliente` = '20337564373' and concluido = 'Si' AND `cuenta` = :cuenta AND pagado = 'No'");
	$consulta->bindParam(':cuenta',$cuenta);
	$consulta->execute();
	return $consulta->fetchAll();  
}

function buscarSucursalesRipley($db){
  $consulta = $db->prepare("SELECT * FROM `sucursalripley`");
	$consulta->execute();
	return $consulta->fetchAll();  
}

function buscarDespachosRipleyAjustar($db){
  $consulta = $db->prepare("SELECT `despachoripley`.`fchDespacho`, `despacho`.`placa`, sum(`subTotal`) as subtotal FROM `despachoripley`, `despacho` WHERE `despachoripley`.`fchDespacho` =  `despacho`.`fchDespacho` AND   `despachoripley`.`correlativo` = `despacho`.`correlativo` AND ajustado = 'No' AND (`despacho`.cuenta = 'express' OR `despacho`.cuenta = 'corporativo'  OR `despacho`.cuenta = 'despacho a domicilio'  OR `despacho`.cuenta = 'express' ) GROUP BY `despachoripley`.`fchDespacho`, `despacho`.`placa`  ");
	$consulta->execute();
	return $consulta->fetchAll();    
}

function buscarDespachosRipleyParaLiquidar($db,$cuenta,$fchIni,$fchFin){
//solo para pruebas
  //$fchIni = '2010-07-01';
  //$fchFin = '2010-07-30';
  $consulta = $db->prepare("SELECT `despachoripley`.`fchDespacho`, `despachoripley`.`correlativo`, `despachoripley`.`guias`, `despachoripley`.`despachos`, `despachoripley`.`sucursal`, `despacho`.`placa`, `despachoripley`.`subtotal`,  `despacho`.`cuenta` FROM `despachoripley`,`despacho`  WHERE `despachoripley`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachoripley`.`correlativo` = `despacho`.`correlativo`  AND `idFactura` IS NULL AND `despacho`.`cuenta` = :cuenta AND (`despachoripley`.`fchDespacho` >= :fchIni AND `despachoripley`.`fchDespacho` <= :fchFin )");
  $consulta->bindParam(':cuenta',$cuenta);
  $consulta->bindParam(':fchIni',$fchIni);
  $consulta->bindParam(':fchFin',$fchFin);
  $consulta->execute();
	return $consulta->fetchAll();
}

function buscarExisteOtroDespacho($db,$idCliente,$fchDespacho,$hraInicio){
  $consulta = $db->prepare("SELECT count(*) as cantidad FROM `despacho` WHERE `fchDespacho` = :fchDespacho  AND `hraInicio` = :hraInicio AND `idCliente` = :idCliente");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
  $consulta->bindParam(':hraInicio',$hraInicio);
  $consulta->bindParam(':idCliente',$idCliente);
  $consulta->execute();
	return $consulta->fetchAll();
}

function buscarTodosLosTerceros($db, $estadoTercero = NULL){
  $where = $estadoTercero == NULL ?"":" AND estadoTercero = :estadoTercero  "; 
	$consulta = $db->prepare("SELECT `documento`, `nombreCompleto` FROM `vehiculodueno` WHERE 1 $where ORDER BY `nombreCompleto` ");
  if ($estadoTercero != NULL) $consulta->bindParam(':estadoTercero',$estadoTercero);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarPagosPendientesTerceros($db,$nroDoc,$pagado,$placa = null,$codDespacho = null,$cliente = null,$conductor = null,$ctoDia = null){
  $where = $having = "";
  if ($placa != null) $where .= " AND despacho.placa = :placa ";
  if ($codDespacho != null){
    $arregloCodigo = explode("-", $codDespacho);
    $anhio = $arregloCodigo[0];
    $where .= " AND year(despacho.fchDespacho) = :anhio ";
    if (isset($arregloCodigo[1])){
      $mes = $arregloCodigo[1];
      $where .= " AND month(despacho.fchDespacho) = :mes ";
      if (isset($arregloCodigo[2])){
        $dia = $arregloCodigo[2];
        $where .= " AND dayofmonth(despacho.fchDespacho) = :dia ";
        if (isset($arregloCodigo[3])){
          $corr = $arregloCodigo[3];
          $where .= " AND despacho.correlativo = :corr ";
        }
      }
    }  
  }
  if ($cliente != null) $where .= " AND despacho.idCliente = :cliente ";
  if ($conductor != null) $having .= " AND despachopersonal.idTrabajador = :idTrabajador ";
  if ($ctoDia != null) $where .= " AND desvehter.costoDia = :ctoDia ";


  //$consulta = $db->prepare("SELECT `desvehter`.`placa`, `docTercero`, `desvehter`.`fchDespacho` , `desvehter`.`correlativo`, `despacho`.`idCliente`, `cliente`.`nombre`, `despacho`.cuenta, concat( `cliente`.`nombre`,' / ', `despacho`.cuenta) AS clienteCuenta , `despacho`.m3, `despacho`.tipoDestino, `despacho`.ptoDestino, GROUP_CONCAT( CONCAT(`despachopersonal`.tipoRol,'->',`trabajador`.`nombres`,' ',`trabajador`.`apPaterno`,' ',`trabajador`.`apMaterno`)) as tripulacion, SUM(if(`despachopersonal`.tipoRol = 'Conductor' AND `despachopersonal`.idTrabajador != '00000001' ,1,0)) AS cantConductor, SUM(if(`despachopersonal`.tipoRol = 'Auxiliar',1,0)) AS cantAuxiliar , `desvehter`.`costoDia`, `desvehter`.`fchPago`, `desvehter`.`pagado`, `desvehter`.`fchPago`, `desvehter`.docPagoTercero  FROM `despachovehiculotercero` AS desvehter , `despacho`, `cliente`, `despachopersonal`, `trabajador`  WHERE `desvehter`.`fchDespacho` = `despacho`.`fchDespacho` AND `desvehter`.`correlativo` = `despacho`.`correlativo` AND `despacho`.`fchDespacho` = `despachopersonal`.`fchDespacho` AND `despacho`.`correlativo` = `despachopersonal`.`correlativo` AND `despachopersonal`.`tipoRol` != 'Coordinador'  AND `trabajador`.`idTrabajador` =  `despachopersonal`.`idTrabajador` AND `desvehter`.`pagado` = :pagado AND `despacho`.`idCliente` = `cliente`.`idRuc` AND docPagoTercero is null AND `desvehter`.`docTercero` = :nroDoc $where  GROUP BY `desvehter`.fchDespacho, `desvehter`.correlativo  ORDER BY `desvehter`.`fchDespacho` DESC , `desvehter`.`correlativo`, `desvehter`.`placa` limit 70 ");


  $consulta = $db->prepare("SELECT `desvehter`.`placa`, `desvehter`.`docTercero`, `desvehter`.`fchDespacho` , `desvehter`.`correlativo`, `despacho`.`idCliente`, `cliente`.`nombre`, `despacho`.cuenta, concat( `cliente`.`nombre`,' / ', `despacho`.cuenta) AS clienteCuenta , `despacho`.m3, `despacho`.tipoDestino, `despacho`.ptoDestino, `desvehter`.`costoDia`, `desvehter`.`fchPago`, `desvehter`.`pagado`, `desvehter`.`fchPago`, `desvehter`.docPagoTercero, GROUP_CONCAT( CONCAT(`despachopersonal`.tipoRol,'->',`trabajador`.`nombres`,' ',`trabajador`.`apPaterno`,' ',`trabajador`.`apMaterno`)) as tripulacion, SUM(if(`despachopersonal`.tipoRol = 'Conductor' AND `despachopersonal`.idTrabajador != '00000001' ,1,0)) AS cantConductor, SUM(if(`despachopersonal`.tipoRol = 'Auxiliar',1,0)) AS cantAuxiliar FROM

   `despachovehiculotercero` AS desvehter, `cliente`, `despacho`

   LEFT JOIN despachopersonal ON despacho.fchDespacho = despachopersonal.fchDespacho AND  despacho.correlativo = despachopersonal.correlativo  AND `despachopersonal`.`tipoRol` != 'Coordinador' 

   LEFT JOIN trabajador ON despachopersonal.idTrabajador = trabajador.idTrabajador

WHERE `desvehter`.`fchDespacho` = `despacho`.`fchDespacho` AND `desvehter`.`correlativo` = `despacho`.`correlativo` AND `desvehter`.`pagado` = :pagado AND `despacho`.`idCliente` = `cliente`.`idRuc` AND docPagoTercero is null AND `desvehter`.`docTercero` = :nroDoc $where  

GROUP BY `desvehter`.fchDespacho, `desvehter`.correlativo $having  ORDER BY `desvehter`.`fchDespacho` DESC , `desvehter`.`correlativo`, `desvehter`.`placa` limit 70 ");





  $consulta->bindParam(':nroDoc',$nroDoc); 
  $consulta->bindParam(':pagado',$pagado);
  if ($placa != null) $consulta->bindParam(':placa',$placa);
  if ($codDespacho != null){
    $consulta->bindParam(':anhio', $anhio );
    if (isset($arregloCodigo[1])){
      $consulta->bindParam(':mes', $mes);
      if (isset($arregloCodigo[2])){
        $consulta->bindParam(':dia', $dia);
        if (isset($arregloCodigo[3])){
          $consulta->bindParam(':corr', $corr);  
        }
      }     
    } 
  }
  if ($cliente != null) $consulta->bindParam(':cliente',$cliente);
  if ($conductor != null) $consulta->bindParam(':idTrabajador',$conductor);
  if ($ctoDia != null) $consulta->bindParam(':ctoDia',$ctoDia);
    
  $consulta->execute();
	return $consulta->fetchAll();
};

function buscarDocsPagosTerceros($db,$nroDoc,$documPago = null, $nroDocLiq = NULL){
  $where = "";
  if ($documPago != null) $where .= " AND `docpagotercero`.`docPagoTercero` like :documPago  ";
  if ($nroDocLiq != null) $where .= " AND `docpagotercero`.`nroDocLiq` like :nroDocLiq  ";
  $consulta = $db->prepare("SELECT count(`docpagotercero`.`docPagoTercero`) as cantidad, sum(despachovehiculotercero.costoDia) AS total , vehiculodueno.nombreCompleto, `docpagotercero`.`docPagoTercero`, `estado`, `docpagotercero`.`usuario`, `docpagotercero`.`fchCreacion`, `docpagotercero`.`fchDocLiq`, `docpagotercero`.fchCancelacion, `docpagotercero`.formaPago, `nro_Oper_o_Cheque`, `docpagotercero`.fchCreacion, `docpagotercero`.tipoDocLiq, `docpagotercero`.nroDocLiq FROM  `docpagotercero` LEFT JOIN `despachovehiculotercero` ON `despachovehiculotercero`.`docPagoTercero` = `docpagotercero`.`docPagoTercero` AND `despachovehiculotercero`.`docPagoTercero` NOT IN ('2011-00001','2012-00001'), vehiculodueno WHERE vehiculodueno.documento = docpagotercero.docTercero AND `docpagotercero`.`docTercero` = :docTercero $where group by `docpagotercero`.`docPagoTercero` ORDER BY  `docpagotercero`.`docPagoTercero` DESC LIMIT 50");

  $consulta->bindParam(':docTercero',$nroDoc); 
  if ($documPago != null) $consulta->bindParam(':documPago',$documPago); 
  if ($nroDocLiq != null) $consulta->bindParam(':nroDocLiq',$nroDocLiq); 

  $consulta->execute();
	return $consulta->fetchAll();  
}

/*
function buscarDatosTercero($db,$nroDocTercero){
	$consulta = $db->prepare("SELECT `documento`, `nombreCompleto`, `bancoNombre`, `bancoCuentaNro`, `bancoCuentaTipo`, `bancoCuentaMoneda` FROM `vehiculodueno` WHERE `documento` = :docTercero ");
  $consulta->bindParam(':docTercero',$nroDocTercero);   
	$consulta->execute();
	return $consulta->fetchAll();
}*/

function buscarAbastecimientosCobrarTerceros($db,$nroDoc,$marca,$fchAb = null, $placaAb = null, $condAb = null, $grifoAb = null){
  $where = "";
  if ($fchAb != null)  $where .= " AND `combustible`.`fchCreacion` = :fchAb ";
  if ($placaAb != null)$where .= " AND `combustible`.`idPlaca` = :placaAb ";
  if ($condAb != null) $where .= " AND `combustible`.`chofer` = :condAb ";
  if ($grifoAb != null)$where .= " AND `combustible`.`grifo` = :grifoAb ";
  if ($marca == NULL){
   $consulta = $db->prepare("SELECT `combustible`.`fchCreacion`, `combustible`.`hraCreacion`, `tiempo`, `combustible`.`idPlaca`, `correlativo`, `chofer`, `auxiliar`, `servicio`, `vehiculo`.`rznSocial` , `grifo`, `galones`,`precioGalon`, `galones`*`precioGalon` as `precioTotal`, nroVale   FROM `combustible`, `vehiculo` WHERE `combustible`.`idPlaca` = `vehiculo`.`idPlaca` AND `vehiculo`.`rznSocial` = :docTercero AND docPagoTercero IS NULL $where  ORDER BY  `combustible`.`fchCreacion` DESC, `combustible`.`hraCreacion`  limit 30");
 } else {
    $consulta = $db->prepare("SELECT `combustible`.`fchCreacion`, `combustible`.`hraCreacion`, `tiempo`, `combustible`.`idPlaca`, `correlativo`, `chofer`, `auxiliar`, `servicio`, `vehiculo`.`rznSocial` , `grifo`, `galones`, `precioGalon`, `galones`*`precioGalon` as `precioTotal`, nroVale   FROM `combustible`, `vehiculo` WHERE `combustible`.`idPlaca` = `vehiculo`.`idPlaca` AND `vehiculo`.`rznSocial` = :docTercero AND docPagoTercero = :docPagoTercero $where ORDER BY  `combustible`.`fchCreacion` DESC, `combustible`.`hraCreacion`  limit 30");
  $consulta->bindParam(':docPagoTercero',$marca);
 }
  $consulta->bindParam(':docTercero',$nroDoc);
  if ($fchAb != null) $consulta->bindParam(':fchAb',$fchAb);
  if ($placaAb != null)$consulta->bindParam(':placaAb',$placaAb);
  if ($condAb != null) $consulta->bindParam(':condAb',$condAb);
  if ($grifoAb != null)$consulta->bindParam(':grifoAb',$grifoAb);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarOtrosCobrarTerceros($db,$nroDoc,$marca = null,$fch= null,$placa= null,$tipo= null,$nroDocReg= null){
  $where = "";
  if ($fch != null)  $where .= " AND `ccmovimientos`.`fchEvento` = :fch ";
  if ($placa != null) $where .= " AND `ccmovimientos`.`idPlaca` = :placa ";
  if ($tipo != null) $where .= " AND `tipoDoc` = :tipo ";
  if ($nroDocReg != null) $where .= " AND `nroDoc` = :nroDocReg ";

  if ($marca == NULL){
    $consulta = $db->prepare("SELECT `ccmovimencab`.`fchEvento`, `grupo`, `subGrupo`, `item`, `ccitem`.`descripcion` as ccItem , `ccmovimientos`.`idPlaca`, `ccmovimientos`.`descripcion`, `ccmovimencab`.`tipoDoc`, `ccmovimencab`.`nroDoc`, `ccmovimientos`.`monto`, `notas`, `ccmovimientos`.nroOrden, `ccmovimientos`.correlativo FROM `vehiculo`, `ccmovimencab`, `ccmovimientos` LEFT JOIN `ccitem` ON `ccmovimientos`.`item` = `ccitem`.`idItem` WHERE `ccmovimencab`.idMovimEncab = `ccmovimientos`.nroOrden AND `ccmovimientos`.`idPlaca` = `vehiculo`.`idPlaca` AND `ccmovimientos`.`docPagoTercero` IS NULL AND `vehiculo`.`rznSocial` = :nroDoc $where ORDER BY `fchEvento` DESC LIMIT 30 ");  


  } else {
    $consulta = $db->prepare("SELECT `ccmovimencab`.`fchEvento`, `grupo`, `subGrupo`, `item`, `ccitem`.`descripcion` as ccItem , `ccmovimientos`.`idPlaca`, `ccmovimientos`.`descripcion`, `ccmovimencab`.`tipoDoc`, `ccmovimencab`.`nroDoc`, `ccmovimientos`.`monto`, `notas`, `ccmovimientos`.nroOrden, `ccmovimientos`.correlativo , `ccmovimientos`.docAnexo  FROM `vehiculo`, `ccmovimencab`, `ccmovimientos` LEFT JOIN `ccitem` ON `ccmovimientos`.`item` = `ccitem`.`idItem` WHERE `ccmovimencab`.idMovimEncab = `ccmovimientos`.nroOrden AND `ccmovimientos`.`idPlaca` = `vehiculo`.`idPlaca` AND `ccmovimientos`.`docPagoTercero` = :docPagoTercero  AND `vehiculo`.`rznSocial` = :nroDoc $where ORDER BY `fchEvento` DESC LIMIT 30");


   $consulta->bindParam(':docPagoTercero',$marca);
  }
  $consulta->bindParam(':nroDoc',$nroDoc);
  if ($fch != null) $consulta->bindParam(':fch',$fch);
  if ($placa != null)$consulta->bindParam(':placa',$placa);
  if ($tipo != null) $consulta->bindParam(':tipo',$tipo);
  if ($nroDocReg != null)$consulta->bindParam(':nroDocReg',$nroDocReg);
  //$consulta->bindParam(':docPagoTercero',$marca);
  $consulta->execute();
  return $consulta->fetchAll();
}


function procesarMarcas($db,$fchDespacho,$correlativo,$marca,$placa,$nroDoc = null){
  //MARCA LOS DESPACHOS CON VEHICULOS DE TERCEROS
  $actualiza = $db->prepare("UPDATE `despachovehiculotercero` SET `pagado` = :marca WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
  $actualiza->bindParam(':fchDespacho',$fchDespacho);
  $actualiza->bindParam(':correlativo',$correlativo);  
  $actualiza->bindParam(':marca',$marca);
  $actualiza->execute();
  
  //MARCA LOS ABASTECIMIENTOS RELACIONADOS CON LA FEChA Y LA PLACA
  //$marcaTer = $marca.'Ter';
  //echo "pagado $marca, fchdespacho $fchDespacho, correlativo $correlativo, nroDoc $nroDoc ";
  //$descripcion = "personal Moy $placa";
  //procesarMarcasPersonalMoy($db,$fchDespacho,$correlativo,$descripcion,$nroDoc,$marca);
  procesarOcurrenciasTercero($db,$fchDespacho,$correlativo,$nroDoc,$marca);

  $marca = ($marca == 'Md')?'0':NULL;
  $actualiza = $db->prepare("UPDATE `combustible` SET `docPagoTercero` = :marca WHERE `fchCreacion` = :fchDespacho  AND `idPlaca` = :placa");
  $actualiza->bindParam(':fchDespacho',$fchDespacho);
  $actualiza->bindParam(':placa',$placa);
  $actualiza->bindParam(':marca',$marca);
  $actualiza->execute();
  
  $actualiza = $db->prepare("UPDATE `ccmovimientos` SET `docPagoTercero` = :marca WHERE `fchEvento` = :fchEvento AND `idPlaca` = :placa ");
  $actualiza->bindParam(':fchEvento',$fchDespacho);
  $actualiza->bindParam(':placa',$placa);    
  $actualiza->bindParam(':marca',$marca);
  $actualiza->execute();

};

function procesarMarcasAbastecimiento($db,$fchCreacion,$hraCreacion,$marca,$placa){
  if ($marca == 'NULL'){
    $actualiza = $db->prepare("UPDATE `combustible` SET `docPagoTercero` = NULL WHERE `fchCreacion` = :fchCreacion AND `hraCreacion` = :hraCreacion AND `idPlaca` = :placa");
 } else {
    $actualiza = $db->prepare("UPDATE `combustible` SET `docPagoTercero` = :marca WHERE `fchCreacion` = :fchCreacion AND `hraCreacion` = :hraCreacion AND `idPlaca` = :placa");
    $actualiza->bindParam(':marca',$marca);
 }
  $actualiza->bindParam(':fchCreacion',$fchCreacion);
  $actualiza->bindParam(':hraCreacion',$hraCreacion);
  $actualiza->bindParam(':placa',$placa);  
  $actualiza->execute();
}


function procesarMarcasCobrarOtros($db,$nroOrden,$correlativo,$marca){
  $actualiza = $db->prepare("UPDATE `ccmovimientos` SET `docPagoTercero` = :marca WHERE `nroOrden` = :nroOrden AND `correlativo` = :correlativo ");
  $actualiza->bindParam(':marca',$marca);
  $actualiza->bindParam(':nroOrden',$nroOrden);
  $actualiza->bindParam(':correlativo',$correlativo);
  $actualiza->execute();  
}


function procesarGenerarDocumento($db,$nroDoc,$idDocumento,$tipoDocLiq,$nroDocLiq,$fchDocLiq, $tipoDocAnexo = NULL, $idDocAnexo = NULL){
  //echo "NdoDoc $nroDoc,  Documento $nuevoDocumento  ";
  $usuario = $_SESSION["usuario"];
  //Intenta generar documento
  $inserta = $db->prepare("INSERT INTO `docpagotercero` (`docPagoTercero`, `docTercero`, `tipoDocLiq`, `nroDocLiq`, `fchDocLiq`,  `tipoDocAnexo`, `idDocAnexo`, `estado`, `usuario`, `fchCreacion`, `hraCreacion`) VALUES (:nuevoDocumento, :nroDoc, :tipoDocLiq, :nroDocLiq , :fchDocLiq , :tipoDocAnexo, :idDocAnexo, 'pendiente', :usuario, CURDATE(), CURTIME());");
  $inserta->bindParam(':nuevoDocumento',$idDocumento);
  $inserta->bindParam(':nroDoc',$nroDoc);
  $inserta->bindParam(':tipoDocLiq',$tipoDocLiq);
  $inserta->bindParam(':nroDocLiq',$nroDocLiq);
  $inserta->bindParam(':fchDocLiq',$fchDocLiq);
  $inserta->bindParam(':tipoDocAnexo',$tipoDocAnexo);
  $inserta->bindParam(':idDocAnexo',$idDocAnexo);
  $inserta->bindParam(':usuario',$usuario);  
  $inserta->execute();  
  $count = $inserta->rowCount(); 
  if ($count == 1){ 
    //Coloca el nro. de documento en tabla despachovehiculotercero
    $actualiza = $db->prepare("UPDATE `despachovehiculotercero` SET `docPagoTercero` = :nuevoDocumento WHERE `pagado` = 'Md'  AND `docTercero` = :nroDoc AND `docPagoTercero` Is Null ");
    $actualiza->bindParam(':nuevoDocumento',$idDocumento);
    $actualiza->bindParam(':nroDoc',$nroDoc);  
    $actualiza->execute();   
    //Coloca el nro. de documento en tabla combustible
    $actualiza = $db->prepare("UPDATE `combustible`,`vehiculo` SET `docPagoTercero` = :nuevoDocumento WHERE `docPagoTercero` = '0' AND `combustible`.`idPlaca` =  `vehiculo`.`idPlaca` AND rznSocial = :nroDoc ");
    $actualiza->bindParam(':nuevoDocumento',$idDocumento);
    $actualiza->bindParam(':nroDoc',$nroDoc);  
    $actualiza->execute();   
    //Coloca el nro. de documento en tabla ccmovimientos
    $actualiza = $db->prepare("UPDATE `ccmovimientos`,`vehiculo` SET `docPagoTercero` = :nuevoDocumento WHERE  `docPagoTercero` = '0' AND `ccmovimientos`.`idPlaca` =  `vehiculo`.`idPlaca` AND rznSocial = :nroDoc ");
    $actualiza->bindParam(':nuevoDocumento',$idDocumento);
    $actualiza->bindParam(':nroDoc',$nroDoc);  
    $actualiza->execute();

    //Coloca el nro. de documento en tabla ocurrenciatercero
    $actualiza = $db->prepare("UPDATE `ocurrenciatercero` SET `docPagoTercero` = :nuevoDocumento WHERE  `docPagoTercero` IS null AND `pagado` =  'Md' AND idTercero = :nroDoc");
    $actualiza->bindParam(':nuevoDocumento',$idDocumento);
    $actualiza->bindParam(':nroDoc',$nroDoc);  
    $actualiza->execute();

    //Coloca el nro. de documento en tabla liqterceromisc
    $actualiza = $db->prepare("UPDATE `liqterceromisc` SET `docPagoTercero` = :nuevoDocumento WHERE  `docPagoTercero` IS null AND `pagado` =  'Md' AND idTercero = :nroDoc");
    $actualiza->bindParam(':nuevoDocumento',$idDocumento);
    $actualiza->bindParam(':nroDoc',$nroDoc);  
    $actualiza->execute();

    //Coloca el nro. de documento en tabla ccmovimientos
    $actualiza = $db->prepare("UPDATE `liqterceromisc`,`vehiculo` SET `docPagoTercero` = :nuevoDocumento WHERE  `docPagoTercero` = '0' AND `liqterceromisc`.`idPlaca` =  `vehiculo`.`idPlaca` AND rznSocial = :nroDoc ");
    $actualiza->bindParam(':nuevoDocumento',$idDocumento);
    $actualiza->bindParam(':nroDoc',$nroDoc);  
    $actualiza->execute();

    return 1;
  } else {
    return 0;
  }
}

function cierraLiquidTercero($db,$nroLiquidTercero,$fchCancel,$formaPago,$nro){
  $usuario = $_SESSION["usuario"];
  $nro = ($nro == '')?null:$nro;
  $actualiza = $db->prepare("UPDATE `docpagotercero` SET `estado` = 'cancelado', fchCancelacion = :fchCancelacion, formaPago = :formaPago, nro_Oper_o_Cheque = :nro, usuarioCerroLiquid = '$usuario'   WHERE `docPagoTercero` = :docPagoTercero");
  $actualiza->bindParam(':docPagoTercero',$nroLiquidTercero);
  $actualiza->bindParam(':fchCancelacion',$fchCancel);
  $actualiza->bindParam(':formaPago',$formaPago);
  $actualiza->bindParam(':nro',$nro);
  $actualiza->execute();
}

function eliminaLiquidacion($db,$docLiq){
  $elimina = $db->prepare("DELETE FROM  `docpagotercero` WHERE `docPagoTercero` = :docLiq");
  $elimina->bindParam(':docLiq',$docLiq);
  $elimina->execute();  
  $count = $elimina->rowCount(); 
  if ($count == 1){ 
    //Coloca el nro. de documento en tabla despachovehiculotercero
    $actualiza = $db->prepare("UPDATE `despachovehiculotercero` SET `docPagoTercero` = null WHERE `docPagoTercero` = :nuevoDocumento ");
    $actualiza->bindParam(':nuevoDocumento',$docLiq);
    $actualiza->execute();   
    //Coloca el nro. de documento en tabla combustible
    $actualiza = $db->prepare("UPDATE `combustible` SET `docPagoTercero` = '0' WHERE `docPagoTercero` =  :nuevoDocumento ");
    $actualiza->bindParam(':nuevoDocumento',$docLiq);
    $actualiza->execute();   
    //Coloca el nro. de documento en tabla ccmovimientos
    $actualiza = $db->prepare("UPDATE `ccmovimientos` SET `docPagoTercero` = '0' WHERE  `docPagoTercero` = :nuevoDocumento");
    $actualiza->bindParam(':nuevoDocumento',$docLiq);
    $actualiza->execute();

    $actualiza = $db->prepare("UPDATE `ocurrenciatercero` SET `docPagoTercero` = null WHERE  `docPagoTercero` = :nuevoDocumento");
    $actualiza->bindParam(':nuevoDocumento',$docLiq);
    $actualiza->execute();

    $actualiza = $db->prepare("UPDATE `liqterceromisc` SET `docPagoTercero` = '0' WHERE  `docPagoTercero` = :nuevoDocumento");
    $actualiza->bindParam(':nuevoDocumento',$docLiq);
    $actualiza->execute();
    return 1;
  }   

};

  function buscarDespachosLiquidacion($db,$docLiquidacion){
    //$consulta = $db->prepare("SELECT `despachovehiculotercero`.`fchDespacho`, `despachovehiculotercero`.`correlativo`, `despachovehiculotercero`.`placa`, `costoDia`, `despacho`.m3 As capM3,  `cliente`.`nombre` AS cliente,  concat(`trabajador`.`apPaterno`,' ', `trabajador`.`apMaterno`,', ', `trabajador`.`nombres`) AS conductor, `kmInicio`, `kmInicioCliente`, `kmFin`, `kmFinCliente`, despacho.`zonaDespacho`, `despachovehiculotercero`.guiaTrTercero, `trabajador`.`categTrabajador`, `despacho`.cuenta  FROM `despachovehiculotercero`, `vehiculo`, `despacho`, `cliente`,  `despachopersonal`, `trabajador`  WHERE  `despachovehiculotercero`.`placa` = `vehiculo`.`idPlaca` AND  `despachovehiculotercero`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachovehiculotercero`.`correlativo` = `despacho`.`correlativo` AND  `despacho`.`idCliente` =  `cliente`.`idRuc` AND  `despacho`.`fchDespacho` =  `despachopersonal`.`fchDespacho`  AND  `despacho`.`correlativo` =  `despachopersonal`.`correlativo` AND  `despachopersonal`.`tipoRol` = 'Conductor' AND  `trabajador`.`idTrabajador` =  `despachopersonal`.`idTrabajador`  AND `docPagoTercero` = :docPagoTercero  ORDER BY `placa`, `fchDespacho`");

    $consulta = $db->prepare("SELECT `despachovehiculotercero`.`fchDespacho`, `despachovehiculotercero`.`correlativo`, `despachovehiculotercero`.`placa`, `costoDia`, `despacho`.m3 As capM3,  `cliente`.`nombre` AS cliente,  concat(`trabajador`.`apPaterno`,' ', `trabajador`.`apMaterno`,', ', `trabajador`.`nombres`) AS conductor, `kmInicio`, `kmInicioCliente`, `kmFin`, `kmFinCliente`, despacho.`zonaDespacho`, `despachovehiculotercero`.guiaTrTercero, `trabajador`.`categTrabajador`, `despacho`.cuenta FROM `despachovehiculotercero`, `vehiculo`, `cliente`,`despacho` 
 LEFT JOIN despachopersonal ON despacho.fchDespacho = despachopersonal.fchDespacho AND  despacho.correlativo = despachopersonal.correlativo  AND `despachopersonal`.`tipoRol` = 'Conductor' 
 LEFT JOIN trabajador ON despachopersonal.idTrabajador = trabajador.idTrabajador
 WHERE  `despachovehiculotercero`.`placa` = `vehiculo`.`idPlaca` AND  `despachovehiculotercero`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachovehiculotercero`.`correlativo` = `despacho`.`correlativo` AND  `despacho`.`idCliente` =  `cliente`.`idRuc` AND `docPagoTercero` = :docPagoTercero
 ORDER BY `placa`, `fchDespacho`");
    $consulta->bindParam(':docPagoTercero',$docLiquidacion);  
    $consulta->execute();
	  return $consulta->fetchAll();
  };

function buscarDocLiquidacion($db,$docLiquidacion){
  $consulta = $db->prepare("SELECT  `tipoDocLiq`, `nroDocLiq`, `docTercero`, `estado`, `fchCancelacion`, `formaPago`, `nro_Oper_o_Cheque` ,`fchDocLiq` , `nombreCompleto`, `eMail`, `bancoNombre`, `bancoCuentaNro`, `bancoCuentaTipo`, `bancoCuentaMoneda`, `cuentaDetraccion`, `tipoDocAnexo`, `idDocAnexo`  FROM `docpagotercero`, `vehiculodueno`  WHERE `docpagotercero`.`docTercero` = `vehiculodueno`.documento AND `docPagoTercero` = :docPagoTercero");
  $consulta->bindParam(':docPagoTercero',$docLiquidacion);  
  $consulta->execute();
	return $consulta->fetchAll();
}

function buscarGastosLiquidacion($db,$docLiquidacion){
  $consulta = $db->prepare("SELECT `ccmovimencab`.`fchEvento`, `item`, `ccmovimientos`.`descripcion` AS observ, `ccitem`.`descripcion`, `ccmovimencab`.`tipoDoc`, `ccmovimencab`.`nroDoc`, `idPlaca`, `ccmovimencab`.`monto`, `ccmovimencab`.`igv`, `ccmovimientos`.`docAnexo`  FROM `ccmovimencab`, `ccmovimientos` LEFT JOIN `ccitem` ON `ccmovimientos`.`item` = `ccitem`.`idItem` WHERE  `ccmovimencab`.idMovimEncab = `ccmovimientos`.nroOrden  AND `docPagoTercero` = :docPagoTercero");


  $consulta->bindParam(':docPagoTercero',$docLiquidacion);  
  $consulta->execute();
	return $consulta->fetchAll();  
}


function buscarCombustibleLiquid($db,$docLiquidacion){
  $consulta = $db->prepare("SELECT `fchCreacion`, `hraCreacion`, `idPlaca`, `servicio`, `kmActual`, `recorrido`, `marcaCombustible`, `nroVale`, `chofer`, `precioGalon`, `galones`, `grifo`,  `observacion`, `docPagoTercero` FROM `combustible` WHERE `docPagoTercero` = :docPagoTercero");
  $consulta->bindParam(':docPagoTercero',$docLiquidacion);  
  $consulta->execute();
	return $consulta->fetchAll();
}

function buscarPersMoyLiquid($db,$docLiquidacion){
  $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `tipoOcurrencia`, `tipoConcepto`, `descripcion`, `montoTotal`, `tipoOcurrencia`, `docAnexo`  FROM `ocurrenciatercero` WHERE `docPagoTercero` = :docPagoTercero");
  $consulta->bindParam(':docPagoTercero',$docLiquidacion);  
  $consulta->execute();
  return $consulta->fetchAll();
}



function buscarDatosPagoAgrupados($db,$docPagoTercero){
	$consulta = $db->prepare("SELECT `despachovehiculotercero`.`placa`, `docTercero`, `despachovehiculotercero`.`fchDespacho` , `despachovehiculotercero`.`correlativo`, `despacho`.`idCliente`, `cliente`.`nombre`,   CONCAT(`trabajador`.`nombres`,' ',`trabajador`.`apPaterno`,' ',`trabajador`.`apMaterno`) as nombreCompleto, `despachovehiculotercero`.`costoDia`, `despachovehiculotercero`.`fchPago`, `despachovehiculotercero`.`pagado`, `despachovehiculotercero`.`fchPago`, `despachovehiculotercero`.docPagoTercero  FROM `despachovehiculotercero`, `despacho`, `cliente`, `despachopersonal`, `trabajador`  WHERE `despachovehiculotercero`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachovehiculotercero`.`correlativo` = `despacho`.`correlativo` AND `despacho`.`fchDespacho` = `despachopersonal`.`fchDespacho` AND `despacho`.`correlativo` = `despachopersonal`.`correlativo` AND `despachopersonal`.`tipoRol` = 'Conductor'  AND `trabajador`.`idTrabajador` =  `despachopersonal`.`idTrabajador` AND `despachovehiculotercero`.`pagado` = 'No'  AND `despacho`.`idCliente` = `cliente`.`idRuc` AND  `despachovehiculotercero`.`docPagoTercero` = :docPagoTercero");
  $consulta->bindParam(':docPagoTercero',$docPagoTercero);  
  $consulta->execute();
	return $consulta->fetchAll();
}


function buscarUltimoCodigo($db){
  $anhio = date("Y");
  $consulta = $db->prepare("SELECT right(`docPagoTercero`,5) as nro FROM `docpagotercero` WHERE left(`docPagoTercero`,4) = :anhio order by  `docPagoTercero` desc limit 1");
  $consulta->bindParam(':anhio',$anhio);  
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarUltimoIdPreliquidCobranza($db){
  $anhio = date("Y");
  $consulta = $db->prepare("SELECT right(`id`,5) as nro FROM `preliquidacioncobranza` WHERE left(`id`,4) = :anhio order by  `id` desc limit 1");
  $consulta->bindParam(':anhio',$anhio);  
  $consulta->execute();
  return $consulta->fetchAll();


}

function buscarTerceroConPersonalMoy($db,$nroDoc,$pagado = 'No'){
  $consulta = $db->prepare("SELECT * FROM `ocurrenciatercero` WHERE `idTercero` = :nroDoc AND `docPagoTercero` IS NULL  AND pagado = :pagado ORDER BY fchDespacho DESC");
  $consulta->bindParam(':nroDoc',$nroDoc);  
  $consulta->bindParam(':pagado',$pagado);
  $consulta->execute();
  return $consulta->fetchAll(); 
}

function procesarMarcasPersonalMoy($db,$fchDespacho,$correlativo,$descripcion,$nroDoc,$valor){
  $actualiza = $db->prepare("UPDATE `ocurrenciatercero` SET `pagado` = :valor WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `tipoOcurrencia` = 'terceroMoy' AND `descripcion` = :descripcion AND `idTercero` = :nroDoc ");
  $actualiza->bindParam(':fchDespacho',$fchDespacho);
  $actualiza->bindParam(':correlativo',$correlativo);
  $actualiza->bindParam(':descripcion',$descripcion);
  $actualiza->bindParam(':nroDoc',$nroDoc);
  $actualiza->bindParam(':valor',$valor);  
  $actualiza->execute();
}

function procesarMarcasOcurrenciaMoy($db,$fchDespacho,$correlativo,$corrOcurrencia,$valor){
  $actualiza = $db->prepare("UPDATE `ocurrenciatercero` SET `pagado` = :valor WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `corrOcurrencia` = :corrOcurrencia ");
  $actualiza->bindParam(':fchDespacho',$fchDespacho);
  $actualiza->bindParam(':correlativo',$correlativo);
  $actualiza->bindParam(':corrOcurrencia',$corrOcurrencia);
  $actualiza->bindParam(':valor',$valor);  
  $actualiza->execute();
}

function buscarLiquidTercero($db,$nroLiquidTercero){
  $consulta = $db->prepare("SELECT `docPagoTercero`, `tipoDocLiq`, `nroDocLiq`, `fchDocLiq`, `docTercero`, nombreCompleto FROM `docpagotercero`, `vehiculodueno` WHERE `docpagotercero`.`docTercero` =  `vehiculodueno`.documento AND  `docPagoTercero` LIKE :nroLiquidTercero");
  $consulta->bindParam(':nroLiquidTercero',$nroLiquidTercero);
  $consulta->execute();
  return $consulta->fetchAll(); 
}

function procesarMarcaCobrarDespacho($db,$fchDespacho,$correlativo,$codigo,$marca){
  $actualiza = $db->prepare("UPDATE `despachodetallesporcobrar` SET `pagado` = :marca WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `codigo` = :codigo");
  $actualiza->bindParam(':fchDespacho',$fchDespacho);
  $actualiza->bindParam(':correlativo',$correlativo);
  $actualiza->bindParam(':codigo',$codigo);
  $actualiza->bindParam(':marca',$marca);
  $actualiza->execute();

}

function insertaCuenta($db,$idCliente,$tipoServicioPago, $nombreServicio, $precioServicio, $topeHraNormal, $precioHraExtra, $tolerHraExtra, $kilometrajeEsperado, $nroAuxiliares,  $precioAuxAdic, $valorConductor,  $valorAuxiliar , $usarMaster){
  $usuario = $_SESSION['usuario'];

  //echo "$idCliente, $tipoServicioPago, $nombreServicio, $precioServicio, $topeHraNormal, $precioHraExtra, $tolerHraExtra, $kilometrajeEsperado, $nroAuxiliares,  $precioAuxAdic, $valorConductor,  $valorAuxiliar , $usarMaster, $usuario";

  $inserta = $db->prepare("INSERT INTO clientecuenta (`idCliente`, `tipoServicio`, `tipoServicioPago`, `valorServicio`, `valorServicioHraExtra`, `topeServicioHraNormal`,`toleranCobroHraExtra`, `nroAuxiliares`, `valorConductor`, `valorAuxiliar`, `valorAuxAdicional` , `usarMaster`,`kilometrajeEsperado`, `creacUsuario`,`creacFch`) VALUES (:id, :tipoServicio, :tipoServicioPago, :valorServicio, :valorServicioHraExtra, :topeServicioHraNormal, :toleHraExtra, :nroAuxiliares, :valorConductor, :valorAuxiliar, :valorAuxAdicional, :usarMaster, :kilometrajeEsperado, :creacUsuario, now())");
    
  $inserta->bindParam(':id',$idCliente);
  $inserta->bindParam(':tipoServicioPago',$tipoServicioPago);
  $inserta->bindParam(':tipoServicio',$nombreServicio);
  $inserta->bindParam(':valorServicio',$precioServicio);
  $inserta->bindParam(':topeServicioHraNormal',$topeHraNormal);
  $inserta->bindParam(':valorServicioHraExtra',$precioHraExtra);
  $inserta->bindParam(':toleHraExtra',$tolerHraExtra);
  $inserta->bindParam(':kilometrajeEsperado',$kilometrajeEsperado);
  $inserta->bindParam(':nroAuxiliares',$nroAuxiliares);
  $inserta->bindParam(':valorAuxAdicional',$precioAuxAdic); 
  $inserta->bindParam(':valorConductor',$valorConductor);
  $inserta->bindParam(':valorAuxiliar',$valorAuxiliar);
  $inserta->bindParam(':usarMaster',$usarMaster);
  $inserta->bindParam(':creacUsuario',$usuario);
  $inserta->execute();
 // echo "Hay que guardar el registro  $dni, $nombre, $fchNac, $sexo";
 
    /*if($tipoServicioPago == 'Hora' OR $tipoServicioPago == 'Puntos DDC' ){
      echo  "tipo servicio  $tipoServicioPago";
      $inserta = $db->prepare("INSERT INTO clientecuentainfoadic (`idCliente`, `tipoServicio`, `valorServicioHraExtra`, `topeServicioHraNormal`) VALUES (:id, :tipoServicio, :valorServicioHraExtra, :topeServicioHraNormal)");
      $inserta->bindParam(':tipoServicio',$tipoServicio);
      //$inserta->bindParam(':tipoServicioPago',$tipoServicioPago);
      $inserta->bindParam(':valorServicioHraExtra',$valHraExtra);
      $inserta->bindParam(':topeServicioHraNormal',$topeHraNormal);
      $inserta->bindParam(':id',$id);
      $inserta->execute();
    }*/
    return $inserta->rowCount();
}

function modificaCuenta($db,$idCliente,$tipoServicioPago, $nombreServicio, $precioServicio, $topeHraNormal, $precioHraExtra, $tolerHraExtra, $kilometrajeEsperado, $nroAuxiliares,  $precioAuxAdic, $valorConductor,  $valorAuxiliar , $usarMaster,$estadoCuenta){
  $usuario = $_SESSION['usuario'];

  $actualiza = $db->prepare("UPDATE `clientecuenta` SET `valorServicio` = :valorServicio , `valorConductor` = :valorConductor , `valorAuxiliar` = :valorAuxiliar , `valorAuxAdicional` = :valorAuxAdicional , `tipoServicioPago` = :tipoServicioPago, `valorServicioHraExtra` = :valorServicioHraExtra, `topeServicioHraNormal` = :topeServicioHraNormal, `toleranCobroHraExtra`= :toleHraExtra , `usarMaster` = :usarMaster , `kilometrajeEsperado`= :kilometrajeEsperado, `nroAuxiliares`= :nroAuxiliares, `estadoCuenta`= :estadoCuenta, editUsuario = :usuario, editFch = curdate()  WHERE `idCliente` = :idCliente AND `tipoServicio` = :tipoServicio ");
   
  $actualiza->bindParam(':idCliente',$idCliente);
  $actualiza->bindParam(':tipoServicio',$nombreServicio);
  $actualiza->bindParam(':valorServicio',$precioServicio);
  $actualiza->bindParam(':valorConductor',$valorConductor);
  $actualiza->bindParam(':valorAuxiliar',$valorAuxiliar);
  $actualiza->bindParam(':valorAuxAdicional',$precioAuxAdic);
  $actualiza->bindParam(':tipoServicioPago',$tipoServicioPago);
  $actualiza->bindParam(':valorServicioHraExtra',$precioHraExtra);
  $actualiza->bindParam(':topeServicioHraNormal',$topeHraNormal);
  $actualiza->bindParam(':toleHraExtra',$tolerHraExtra);
  $actualiza->bindParam(':usarMaster',$usarMaster);
  $actualiza->bindParam(':kilometrajeEsperado',$kilometrajeEsperado);
  $actualiza->bindParam(':nroAuxiliares',$nroAuxiliares);
  $actualiza->bindParam(':estadoCuenta',$estadoCuenta);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();

  return $actualiza->rowCount();
}

function eliminaRegCuenta($db,$id,$tipoServicio){
  $elimina = $db->prepare("DELETE FROM `clientecuenta` WHERE `idCliente` = :idCliente AND `tipoServicio` = :tipoServicio");
  $elimina->bindParam(':idCliente',$id);
  $elimina->bindParam(':tipoServicio',$tipoServicio);
  $elimina->execute();
  return $elimina->rowCount();
}


function insertaInfoAdic($db,$id,$tipoServicioPago,$nombreServicio,$opciones){
  $inserta = $db->prepare("INSERT INTO `clientecuentainfoadic` (`idCliente`, `tipoServicio`, `tipoServicioPago`, `opciones`) VALUES (:id, :tipoServicio, :tipoServicioPago, :opciones)");
  $inserta->bindParam(':id',$id);
  $inserta->bindParam(':tipoServicioPago',$tipoServicioPago);
  $inserta->bindParam(':tipoServicio',$nombreServicio);
  $inserta->bindParam(':opciones',$opciones);
  $inserta->execute();
  return $inserta->rowCount();
}

function  modificaInfoAdic($db,$id,$tipoServicioPago,$nombreServicio,$opciones){
  $actualiza = $db->prepare("REPLACE INTO `clientecuentainfoadic` (`idCliente`, `tipoServicio`, `tipoServicioPago`, `opciones`) VALUES (:id, :tipoServicio, :tipoServicioPago, :opciones)");  
  $actualiza->bindParam(':id',$id);
  $actualiza->bindParam(':tipoServicio',$nombreServicio);
  $actualiza->bindParam(':tipoServicioPago',$tipoServicioPago);
  $actualiza->bindParam(':opciones',$opciones);
  $actualiza->execute();
  return $actualiza->rowCount();
}

function eliminaInfoAdic($db,$id,$tipoServicio){
  $inserta = $db->prepare("DELETE FROM `clientecuentainfoadic` WHERE `idCliente` = :id AND  `tipoServicio` = :tipoServicio ");
  $inserta->bindParam(':id',$id);
  $inserta->bindParam(':tipoServicio',$tipoServicio);
  $inserta->execute();
  return $inserta->rowCount();
}

function actualizarEstadoDocCobranza($db,$docCobranza,$tipoDoc,$estado,$fch){
  $usuario = $_SESSION['usuario'];
  $actualiza = $db->prepare("UPDATE `doccobranza` SET `estado` = :estado, fch$estado = :fch, editUsuario = :usuario, editFch = curdate() WHERE `docCobranza` = :docCobranza AND `tipoDoc` = :tipoDoc");
  $actualiza->bindParam(':docCobranza',$docCobranza);
  $actualiza->bindParam(':tipoDoc',$tipoDoc);
  $actualiza->bindParam(':estado',$estado);
  $actualiza->bindParam(':fch',$fch);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();
  //return $actualiza->rowCount();
  return "$usuario, $docCobranza, $tipoDoc, $estado, $fch";
}

function buscarDatosPendientesCobranza($db,$docCobranza,$tipoDoc){
  $consulta = $db->prepare("SELECT despacho.`fchDespacho`, despacho.`correlativo`, `guiaCliente`, `placa`, `valorServicio`, `igvServicio`, despacho.`idCliente`, `cuenta`, `tipoServicioPago`, despacho.`nroAuxiliares`, `nroAuxiliaresAdic`, `costo`, `topeServicioHraNormal`, `costoHraExtra`, `toleranCobroHraExtra`, `valorConductor`, `valorAuxiliar`, `nroAuxiliaresCuenta`, `valorAuxAdicional`, `tpoExtraHras`, `nroGuias`, `nroDespachos`, `usaReten`, `concluido`, `hraFinCliente`, `lugarFinCliente`, `recorridoEsperado`, `zonaDespacho`, `ptoOrigen`, `tipoDestino`, `ptoDestino`, `observacion`, `observCliente`,  `hraInicio`, `hraFin` AS `hraFinMoy`, if (hraFinCliente = '00:00:00', `despacho`.`hraFin`, `hraFinCliente` ) AS `hraFin`, `despacho`.`kmInicio` AS kmIniMoy, `despacho`.`kmFin` AS `kmFinMoy`, if (`kmInicioCliente` > 0, `kmInicioCliente`, `kmInicio` ) As `kmInicio`, if (`kmFinCliente` > 0, `kmFinCliente`, kmFin) As kmFin, if (kmFinCliente >0, kmFinCliente , kmFin) -if (kmInicioCliente > 0, kmInicioCliente , kmInicio ) as Recorrido, substring(TIMEDIFF( if (hraFinCliente = '00:00:00',concat(`despacho`.`fchDespachoFin`,' ', `despacho`.`hraFin`), concat( `despacho`.fchDespachoFinCli  ,' ', `hraFinCliente`)),concat(`despacho`.fchDespacho  ,' ', `hraInicio`)),1,8) AS hrasTrab,  apPaterno, apMaterno, nombres, concat(apPaterno,' ',apMaterno,', ', nombres) AS nombCompleto, dimInteriorAlto,  dimInteriorAncho, dimInteriorLargo, round(dimInteriorAlto*dimInteriorAncho*dimInteriorLargo,2) AS dimInteriorM3x, dataDetalle, observDetallePorCobrar , `despacho`.idProducto, `clicuepro`.m3Facturable  AS dimInteriorM3 FROM (SELECT fchDespacho, correlativo, docCobranza, tipoDoc, GROUP_CONCAT(concat(codigo,'|',costoUnit,'|',cantidad) ORDER BY codigo) AS dataDetalle, GROUP_CONCAT(observDetallePorCobrar) AS observDetallePorCobrar FROM `despachodetallesporcobrar` WHERE  `docCobranza` LIKE :docCobranza AND `tipoDoc` LIKE :tipoDoc AND codigo IN ('Despacho', 'HrasAdic', 'AuxAdic') GROUP BY `fchDespacho`, `correlativo` ) AS t1,  despacho   LEFT JOIN despachopersonal ON `despacho`.fchDespacho = `despachopersonal`.fchDespacho AND `despacho`.correlativo = `despachopersonal`.correlativo AND tipoRol = 'conductor' LEFT JOIN trabajador ON `despachopersonal`.idTrabajador = `trabajador`.idTrabajador  LEFT JOIN vehiculo ON `despacho`.placa = idPlaca  LEFT JOIN `clientecuentaproducto` AS clicuepro ON despacho.idProducto = clicuepro.idProducto   WHERE `despacho`.fchDespacho = `t1`.fchDespacho  AND `despacho`.correlativo = `t1`.correlativo AND `t1`.`docCobranza` = :docCobranza AND `t1`.`tipoDoc` = :tipoDoc ORDER BY  despacho.`fchDespacho`, `placa`");


 // $consulta = $db->prepare("SELECT despacho.`fchDespacho`, despacho.`correlativo`, `guiaCliente`, `placa`, `valorServicio`, `igvServicio`, `idCliente`, `cuenta`, `tipoServicioPago`, `nroAuxiliares`, `nroAuxiliaresAdic`, `costo`, `topeServicioHraNormal`, `costoHraExtra`, `toleranCobroHraExtra`, `valorConductor`, `valorAuxiliar`, `nroAuxiliaresCuenta`, `valorAuxAdicional`, `tpoExtraHras`, `nroGuias`, `nroDespachos`, `usaReten`, `concluido`, `hraFinCliente`, `lugarFinCliente`, `recorridoEsperado`, `zonaDespacho`, `ptoOrigen`, `tipoDestino`, `ptoDestino`, `observacion`, `observCliente`,  `hraInicio`, `hraFin` AS `hraFinMoy`, if (hraFinCliente = '00:00:00', `despacho`.`hraFin`, `hraFinCliente` ) AS `hraFin`, `despacho`.`kmInicio` AS kmIniMoy, `despacho`.`kmFin` AS `kmFinMoy`, if (`kmInicioCliente` > 0, `kmInicioCliente`, `kmInicio` ) As `kmInicio`, if (`kmFinCliente` > 0, `kmFinCliente`, kmFin) As kmFin, if (kmFinCliente >0, kmFinCliente , kmFin) -if (kmInicioCliente > 0, kmInicioCliente , kmInicio ) as Recorrido, substring(TIMEDIFF( if (hraFinCliente = '00:00:00',concat(`despacho`.`fchDespachoFin`,' ', `despacho`.`hraFin`), concat( `despacho`.fchDespachoFinCli  ,' ', `hraFinCliente`)),concat(`despacho`.fchDespacho  ,' ', `hraInicio`)),1,8) AS hrasTrab,  apPaterno, apMaterno, nombres, concat(apPaterno,' ',apMaterno,', ', nombres) AS nombCompleto, dimInteriorAlto,  dimInteriorAncho, dimInteriorLargo, round(dimInteriorAlto*dimInteriorAncho*dimInteriorLargo,2) AS dimInteriorM3, dataDetalle, observDetallePorCobrar FROM (SELECT fchDespacho, correlativo, docCobranza, tipoDoc, GROUP_CONCAT(concat(codigo,'|',costoUnit,'|',cantidad) ORDER BY codigo) AS dataDetalle, GROUP_CONCAT(observDetallePorCobrar) AS observDetallePorCobrar FROM `despachodetallesporcobrar` WHERE `docCobranza` LIKE :docCobranza AND `tipoDoc` LIKE :tipoDoc AND codigo IN ('Despacho', 'HrasAdic', 'AuxAdic') GROUP BY `fchDespacho`, `correlativo` ) AS t1, despacho LEFT JOIN despachopersonal ON `despacho`.fchDespacho = `despachopersonal`.fchDespacho AND `despacho`.correlativo = `despachopersonal`.correlativo AND tipoRol = 'conductor' LEFT JOIN trabajador ON `despachopersonal`.idTrabajador = `trabajador`.idTrabajador  LEFT JOIN vehiculo ON `despacho`.placa = idPlaca WHERE `t1`.fchDespacho = `despacho`.fchDespacho AND `t1`.correlativo = `despacho`.correlativo AND `t1`.`docCobranza` = :docCobranza AND `t1`.`tipoDoc` = :tipoDoc  ORDER BY  despacho.`fchDespacho`, `placa`");


  $consulta->bindParam(':docCobranza',$docCobranza);  
  $consulta->bindParam(':tipoDoc',$tipoDoc);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarDatosPendientesCobranzaOtros($db,$docCobranza,$tipoDoc){
  $consulta = $db->prepare("SELECT despacho.`fchDespacho`, despacho.`correlativo`, `guiaCliente`, `placa`, `valorServicio`, `igvServicio`, `idCliente`, `cuenta`, `tipoServicioPago`, `nroAuxiliares`, `nroAuxiliaresAdic`, `costo`, `topeServicioHraNormal`, `costoHraExtra`, `toleranCobroHraExtra`, `valorConductor`, `valorAuxiliar`, `nroAuxiliaresCuenta`, `valorAuxAdicional`, `tpoExtraHras`, `nroGuias`, `nroDespachos`, `usaReten`, `concluido`, `hraFinCliente`, `lugarFinCliente`, `recorridoEsperado`, `zonaDespacho`, `ptoOrigen`, `tipoDestino`, `ptoDestino`, `observacion`, `observCliente`,  `hraInicio`, `hraFin` AS `hraFinMoy`, if (hraFinCliente = '00:00:00', `despacho`.`hraFin`, `hraFinCliente` ) AS `hraFin`, `despacho`.`kmInicio` AS kmIniMoy, `despacho`.`kmFin` AS `kmFinMoy`, if (`kmInicioCliente` > 0, `kmInicioCliente`, `kmInicio` ) As `kmInicio`, if (`kmFinCliente` > 0, `kmFinCliente`, kmFin) As kmFin, if (kmFinCliente >0, kmFinCliente , kmFin) -if (kmInicioCliente > 0, kmInicioCliente , kmInicio ) as Recorrido, TIMEDIFF( if (hraFinCliente = '00:00:00', `despacho`.`hraFin`, `hraFinCliente`), `hraInicio`) AS hrasTrab,  apPaterno, apMaterno, nombres, concat(apPaterno,' ',apMaterno,', ', nombres) AS nombCompleto, dimInteriorAlto,  dimInteriorAncho, dimInteriorLargo, round(dimInteriorAlto*dimInteriorAncho*dimInteriorLargo,2) AS dimInteriorM3, dataDetalle, observDetallePorCobrar FROM (SELECT fchDespacho, correlativo, docCobranza, tipoDoc, GROUP_CONCAT(concat(codigo,'|',costoUnit,'|',cantidad) ORDER BY codigo) AS dataDetalle, GROUP_CONCAT(observDetallePorCobrar) AS observDetallePorCobrar FROM `despachodetallesporcobrar` WHERE `docCobranza` LIKE :docCobranza AND `tipoDoc` LIKE :tipoDoc AND codigo NOT IN ('Despacho', 'HrasAdic', 'AuxAdic') GROUP BY `fchDespacho`, `correlativo` ) AS t1, despacho LEFT JOIN despachopersonal ON `despacho`.fchDespacho = `despachopersonal`.fchDespacho AND `despacho`.correlativo = `despachopersonal`.correlativo AND tipoRol = 'conductor' LEFT JOIN trabajador ON `despachopersonal`.idTrabajador = `trabajador`.idTrabajador  LEFT JOIN vehiculo ON `despacho`.placa = idPlaca WHERE `t1`.fchDespacho = `despacho`.fchDespacho AND `t1`.correlativo = `despacho`.correlativo AND `t1`.`docCobranza` = :docCobranza AND `t1`.`tipoDoc` = :tipoDoc  ORDER BY  despacho.`fchDespacho`, `placa`");
  $consulta->bindParam(':docCobranza',$docCobranza);  
  $consulta->bindParam(':tipoDoc',$tipoDoc);
  $consulta->execute();
  return $consulta->fetchAll();
}


function inactivarClientes($db){
  $actualiza = $db->prepare("UPDATE `cliente`, (SELECT  `idCliente`, DATEDIFF(curdate(),max( `fchDespacho`)) AS dif FROM `despacho`, cliente WHERE  `despacho`.idCliente = `cliente`.idRuc AND estadoCliente = 'activo'  GROUP BY idCliente HAVING dif > 60) AS t1 SET `cliente`.estadoCliente = 'inactivo' WHERE `cliente`.idRuc = `t1`.idCliente");
  $actualiza->execute();
  return  $actualiza->rowCount();
}

function registraPagoDetraccion($db,$nroDoc,$tipoDoc,$fchPago,$monto,$constancia,$tipoDetraccion){
  $actualiza = $db->prepare("UPDATE `doccobranza` SET `detraccion` = :monto, `fchDetraccion` = :fchPago, `constanciaDetraccion` = :constancia, tipoDetraccion = :tipoDetraccion, fchRegDetraccion = curdate() WHERE `docCobranza` = :nroDoc AND `tipoDoc` = :tipoDoc");
  $actualiza->bindParam(':nroDoc',$nroDoc);
  $actualiza->bindParam(':tipoDoc',$tipoDoc);
  $actualiza->bindParam(':fchPago',$fchPago);
  $actualiza->bindParam(':monto',$monto);
  $actualiza->bindParam(':constancia',$constancia);
  $actualiza->bindParam(':tipoDetraccion',$tipoDetraccion);
  $actualiza->execute();
  return  $actualiza->rowCount();
}

function insertaIngresoBanco($db,$idCliente,$banco,$nroOper,$monto,$fchOper,$descrip){
  //echo "idCli $idCliente, banco $banco, nro oper $nroOper, monto $monto, fchOper $fchOper, descrip $descrip";
  $usuario = $_SESSION['usuario'];
  $inserta = $db->prepare("INSERT INTO `doccobranzaingbanco` (`nroOperacion`, `banco`, `idCliente`, `monto`, `fchTransaccion`, `observacion`, `creacFch`, `creacUsuario`) VALUES (:nroOperacion ,:banco, :idCliente, :monto, :fchOper, :descrip, NOW(), :usuario)");
    
  $inserta->bindParam(':idCliente',$idCliente);
  $inserta->bindParam(':banco',$banco);
  $inserta->bindParam(':nroOperacion',$nroOper);
  $inserta->bindParam(':monto',$monto);
  $inserta->bindParam(':fchOper',$fchOper);
  $inserta->bindParam(':descrip',$descrip);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();
  return $inserta->rowCount();  
}

function actualizaIngresoBancoEnDocCobranza($db, $tipoDoc, $nroDoc, $banco, $nroOper, $fchOper, $idCliente, $estado = 'Presentada'){
  $actualizaAdic = "";
  if ($tipoDoc == "Factura"){
    if ($estado == 'Cancelada') $actualizaAdic = " fchCancelada = :fchCancelada , ";
    $actualiza = $db->prepare("UPDATE `doccobranza` SET estado = :estado, `nroOperacion` = :nroOper, `banco` = :banco, $actualizaAdic `fchBancoRegistro` = now() WHERE `docCobranza` = :nroDoc AND `tipoDoc` = :tipoDoc");
    $actualiza->bindParam(':nroDoc',$nroDoc);
    $actualiza->bindParam(':tipoDoc',$tipoDoc);
    $actualiza->bindParam(':estado',$estado);
    $actualiza->bindParam(':banco',$banco);
    $actualiza->bindParam(':nroOper',$nroOper); 
    if ($estado == 'Cancelada') $actualiza->bindParam(':fchCancelada',$fchOper); 

  } else if ($tipoDoc == "OFactura"){
    $actualiza = $db->prepare("UPDATE `docfacturacliente` SET `nroOperacion` = :nroOper, `banco` = :banco WHERE `nroFactura` = :nroDoc AND `idCliente` = :idCliente");
    $actualiza->bindParam(':nroDoc',$nroDoc);
    $actualiza->bindParam(':idCliente',$idCliente);
    $actualiza->bindParam(':banco',$banco);
    $actualiza->bindParam(':nroOper',$nroOper);  

  } else if ($tipoDoc == "OCredito" || $tipoDoc == "ODebito"  || $tipoDoc == "OAnticipo" ){
    $tipoDoc = substr($tipoDoc, 1,10);
    $actualiza = $db->prepare("UPDATE `docnotas` SET `nroOperacion` = :nroOper, `banco` = :banco WHERE `docCobranza` = :nroDoc AND `tipoDoc` = :tipoDoc");
    $actualiza->bindParam(':nroDoc',$nroDoc);
    $actualiza->bindParam(':tipoDoc',$tipoDoc);
    $actualiza->bindParam(':banco',$banco);
    $actualiza->bindParam(':nroOper',$nroOper); 

  }

  //$actualiza->bindParam(':constancia',$constancia);
  $actualiza->execute();
  return  $actualiza->rowCount();
}

function buscarFactCliente($db,$cliente, $nroFactura = NULL){
  $where = ($nroFactura == NULL)?"":" AND nroFactura = :nroFactura";

  $consulta = $db->prepare("SELECT `nroFactura`, `idCliente`, `fchFactura`, `monto`, `glosa`, `nroOperacion`, `banco`, `creacUsuario`, `creacFch`, `editaUsuario` FROM `docfacturacliente` WHERE `idCliente` LIKE :cliente $where ");
  $consulta->bindParam(':cliente',$cliente);  
  if ($nroFactura != NULL)  $consulta->bindParam(':nroFactura',$nroFactura);
  $consulta->execute();
  return $consulta->fetchAll();
}

function insertaNuevaFactCli($db,$cliente,$nroFactura,$fchFactura, $monto, $observ){
  $usuario = $_SESSION['usuario'];
  $inserta = $db->prepare("INSERT INTO `docfacturacliente` (`nroFactura`, `idCliente`, `fchFactura`, `monto`, `glosa`, `creacUsuario`, `creacFch`) VALUES (:nroFactura, :cliente, :fchFactura, :monto, :observ, :usuario, CURRENT_DATE())");
  $inserta->bindParam(':nroFactura',$nroFactura);
  $inserta->bindParam(':cliente',$cliente);
  $inserta->bindParam(':fchFactura',$fchFactura);
  $inserta->bindParam(':monto',$monto);
  $inserta->bindParam(':observ',$observ);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();
  return $inserta->rowCount();
}

function modificaFactCli($db,$cliente,$nroFactura,$monto,$glosa){
  $usuario = $_SESSION['usuario'];
  $actualiza = $db->prepare("UPDATE `docfacturacliente` SET `monto` = :monto, `glosa` = :glosa, editaUsuario = :usuario WHERE `nroFactura` = :nroFactura AND `idCliente` = :cliente");
  $actualiza->bindParam(':nroFactura',$nroFactura);
  $actualiza->bindParam(':cliente',$cliente);
  //$actualiza->bindParam(':fchFactura',$fchFactura);
  $actualiza->bindParam(':monto',$monto);
  $actualiza->bindParam(':glosa',$glosa);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();
  return $actualiza->rowCount();
}

function eliminaFactCliente($db,$cliente,$nroFactura){
  $elimina = $db->prepare("DELETE FROM `docfacturacliente` WHERE `nroFactura` = :nroFactura AND `idCliente` = :cliente ");
  $elimina->bindParam(':nroFactura',$nroFactura);
  $elimina->bindParam(':cliente',$cliente);
  $elimina->execute();
  return $elimina->rowCount();
}

function buscarOtrosDocumsCobranza($db,$idCliente){
  $consulta = $db->prepare("SELECT `docCobranza`, `tipoDoc`, `fchDoc`, `idCliente`, `monto`, `observacion`, `nroOperacion`, `banco` FROM `docnotas` WHERE nroOperacion IS NULL AND idCliente LIKE :idCliente UNION SELECT `nroFactura`, 'Factura' AS tipoDoc, `fchFactura`, `idCliente`, `monto`, `glosa`, `nroOperacion`, `banco` FROM `docfacturacliente` WHERE  nroOperacion IS NULL AND  idCliente LIKE :idCliente ");
  $consulta->bindParam(':idCliente',$idCliente);
  $consulta->execute();
  return $consulta->fetchAll();  
}


function buscarFacturaPorEstado($db,$estado = 'Todos', $idCliente = NULL){
  $where = "";
  if ($estado == 'Pendientes') $where .= " AND `estado` != 'Cancelada' ";
  if ($idCliente != NULL) $where .= " AND cobIdCliente = :idCliente ";
  $consulta = $db->prepare("SELECT `docCobranza`, `tipoDoc`, `estado`, `fchEmitida`, `fchPresentada`, `fchCancelada`, `preliquidacion`, `fchPreliquid`, `detraccion`, `fchDetraccion`, `fchRegDetraccion`, `constanciaDetraccion`, `nroOperacion`, `banco`, `fchBancoRegistro`, `cobIdCliente`, `nombCliente` FROM `doccobranza` WHERE fchCreacion >= '2016-04-01'  $where ORDER BY fchCreacion");
  if ($idCliente != NULL) $consulta->bindParam(':idCliente',$idCliente);
  $consulta->execute();
  return $consulta->fetchAll();  

}

function insertaDocNota($db, $docCobranza, $tipoDoc, $nroDoc, $idCliente, $monto, $observacion, $fchDoc = NULL){
  $usuario = $_SESSION['usuario'];
  $fchDoc = ($fchDoc == NULL)?Date("Y-m-d"):$fchDoc;
  $inserta = $db->prepare("INSERT INTO `docnotas` (`docCobranza`, `tipoDoc`, `fchDoc`, `idCliente`, `docRelacionado`,`observacion`,`monto`, `estado`, `usuario`, `fchCreacion`, `hraCreacion`) VALUES (:docCobranza, :tipoDoc, :fchDoc, :idCliente, :docRelacionado, :observacion, :monto, NULL, :usuario, CURDATE(), CURTIME());");
  $inserta->bindParam(':docCobranza',$docCobranza);
  $inserta->bindParam(':tipoDoc',$tipoDoc);
  $inserta->bindParam(':docRelacionado',$nroDoc);
  $inserta->bindParam(':observacion',$observacion);
  $inserta->bindParam(':idCliente',$idCliente);
  $inserta->bindParam(':fchDoc',$fchDoc);
  $inserta->bindParam(':monto',$monto);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();
}  

function generaIdAnticipo($db){
  $consulta = $db->prepare("SELECT `docCobranza`  FROM `docnotas` WHERE `tipoDoc` LIKE 'Anticipo' ORDER BY `docCobranza` DESC limit 1 ");
  $consulta->execute();
  $auxNro =  $consulta->fetchAll();
  $nro = "";
  foreach ($auxNro as $item) {
    $nro = $item['docCobranza'];
  }
  $anhioActual = date("y");
  $anhioNro = substr($nro, 0,2);
  if ($anhioNro == $anhioActual)
    $sgteNro = $nro*1+1;
  else
    $sgteNro = $anhioActual."000001";
  return $sgteNro;
}


function crearAnticipo($db,$idCliente,$mntAnticipo,$observacion){
  $idAnticipo = generaIdAnticipo($db);
  insertaDocNota($db, $idAnticipo,"Anticipo", "", $idCliente, $mntAnticipo, $observacion);
  $idAnticipo = generaIdAnticipo($db);
  insertaDocNota($db, $idAnticipo,"Anticipo", "", $idCliente, -1*$mntAnticipo, $observacion);
}

function  generaDocCobrRelIngBanco($db,$docCobranza,$tipoDoc,$monto,$nroOper,$banco,$fchTransac,$idCliente){
  $usuario = $_SESSION['usuario'];
  $inserta = $db->prepare("INSERT INTO `doccobr_rel_ingbco` (`docCobranza`, `tipoDoc`, `nroOperacion`, `banco`, `montoPagado`, `creacFch`, `creacUsuario`) VALUES (:docCobranza, :tipoDoc, :nroOper, :banco, :monto, CURRENT_DATE(), :usuario)");
  $inserta->bindParam(':docCobranza',$docCobranza);
  $inserta->bindParam(':tipoDoc',$tipoDoc);
  $inserta->bindParam(':nroOper',$nroOper);
  $inserta->bindParam(':banco',$banco);
  $inserta->bindParam(':monto',$monto);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();
  return $inserta->rowCount();

}

function actualizaDocNota($db, $docCobranza, $tipoDoc, $nroOper, $banco){
  //$tipoDoc = substr($tipoDoc, 1,10);
  echo "$docCobranza, $tipoDoc, $nroOper, $banco";
  $actualiza = $db->prepare("UPDATE `docnotas` SET `nroOperacion` = :nroOper, `banco` = :banco WHERE `docCobranza` = :docCobranza AND `tipoDoc` = :tipoDoc");
  $actualiza->bindParam(':docCobranza',$docCobranza);
  $actualiza->bindParam(':tipoDoc',$tipoDoc);
  $actualiza->bindParam(':banco',$banco);
  $actualiza->bindParam(':nroOper',$nroOper);
  $actualiza->execute();
  return $actualiza->rowCount();

}

function actualizaFacturaCliente($db, $nroDoc, $idCliente, $banco, $nroOper){
  $actualiza = $db->prepare("UPDATE `docfacturacliente` SET `nroOperacion` = :nroOper, `banco` = :banco WHERE `nroFactura` = :nroDoc AND `idCliente` = :idCliente");
  $actualiza->bindParam(':nroDoc',$nroDoc);
  $actualiza->bindParam(':idCliente',$idCliente);
  $actualiza->bindParam(':banco',$banco);
  $actualiza->bindParam(':nroOper',$nroOper);
  $actualiza->execute();
  return $actualiza->rowCount();
}

function desmarcarFactCli($db, $nroOper, $banco){
  $actualiza = $db->prepare("UPDATE `docfacturacliente` SET `nroOperacion` = NULL, `banco` = NULL WHERE`nroOperacion` = :nroOper AND `banco` = :banco");
  $actualiza->bindParam(':banco',$banco);
  $actualiza->bindParam(':nroOper',$nroOper);
  $actualiza->execute();
  return $actualiza->rowCount();
}

function desmarcarNotas($db, $nroOper, $banco){
  $actualiza = $db->prepare("UPDATE `docnotas` SET `nroOperacion` = NULL, `banco` = NULL WHERE`nroOperacion` = :nroOper AND `banco` = :banco");
  $actualiza->bindParam(':banco',$banco);
  $actualiza->bindParam(':nroOper',$nroOper);
  $actualiza->execute();
  return $actualiza->rowCount();
}

function eliminarPagoIngreso($db,$nroOper, $banco){
  $elimina = $db->prepare("DELETE FROM `doccobranzaingbanco` WHERE `nroOperacion` = :nroOper AND `banco` = :banco");
  $elimina->bindParam(':banco',$banco);
  $elimina->bindParam(':nroOper',$nroOper);
  $elimina->execute();
  return $elimina->rowCount();
}

function actualizaEstadoFacturas($db){
  $actualiza = $db->prepare("UPDATE doccobranza SET estado = 'Presentada', fchCanceladaAux = fchCancelada, fchCancelada = NULL WHERE estado = 'Cancelada'");
  $actualiza->execute();
  $nro1 = $actualiza->rowCount();

  $actualiza = $db->prepare("UPDATE doccobranza, (SELECT doccobranza.`docCobranza`, doccobranza.`tipoDoc`, `estado`, doccobranza.`nroOperacion`, doccobranza.`banco`, sum(`despachodetallesporcobrar`.costoUnit * `despachodetallesporcobrar`.cantidad) AS mntDetallesPorCobrar, montoPagado FROM despachodetallesporcobrar, `doccobranza` LEFT JOIN (SELECT `docCobranza`, `tipoDoc`, sum(`montoPagado`) AS montoPagado FROM `doccobr_rel_ingbco` GROUP BY `docCobranza`, `tipoDoc`) AS t1 ON `doccobranza`.docCobranza = `t1`.docCobranza AND `doccobranza`.tipoDoc = `t1`.tipoDoc WHERE `doccobranza`.docCobranza = `despachodetallesporcobrar`.docCobranza AND `doccobranza`.tipoDoc = `despachodetallesporcobrar`.tipoDoc GROUP BY doccobranza.`docCobranza`, doccobranza.`tipoDoc` HAVING abs(mntDetallesPorCobrar - montoPagado) < 1  ) AS t2 SET `doccobranza`.estado = 'Cancelada', fchCancelada = if(fchCanceladaAux IS NULL,curdate(),fchCanceladaAux), fchCanceladaAux = NULL WHERE `doccobranza`.docCobranza = `t2`.docCobranza AND `doccobranza`.tipoDoc = `t2`.tipoDoc");
  $actualiza->execute();
  $nro2 = $actualiza->rowCount();
  $total = $nro1 + $nro2;
  return $total;
}

function buscarDataCobranzaDespachos($db,$docCobranza, $tipoDoc){
  $consulta = $db->prepare("SELECT doccobranza.`docCobranza`, doccobranza.`tipoDoc`, `estado`, `observacion`, `chkFecha`, `chkObserv`, `chkPlaca`, `chkHraIni`, `chkHraFin`, `chkGT`, `chkObsDesp`, `chkConductor`, `chkValDesp`, `chkHrasTrab`, `chkM3`, `chkHraExtra`, `chkValHraExtra`, `chkValAuxAdic`, `chkValTotal`, `chkTiendas`, `chkAux01`, `chkAux02`, `chkAuxAdic`, `chkHojaRuta`, `chkGuiasDesp`, `chkKmInicio`, `chkKmFin`, `chkKmRecorr`, `fchConfirmacion`, `fchEmitida`, `fchPresentada`, `fchCancelada`, `fchCanceladaAux`, `preliquidacion`, `fchPreliquid`, `detraccion`, `tipoDetraccion`, `fchDetraccion`, `fchRegDetraccion`, `constanciaDetraccion`, doccobr_rel_ingbco.`tipoDoc`, doccobr_rel_ingbco.`nroOperacion`, doccobr_rel_ingbco.`banco`, doccobr_rel_ingbco.`montoPagado`,  doccobr_rel_ingbco.`creacFch`, doccobr_rel_ingbco.`creacUsuario`, `fchBancoRegistro`, `cobIdCliente`, `nombCliente`, `usuario`, `fchCreacion`, `hraCreacion` FROM `doccobranza` LEFT JOIN doccobr_rel_ingbco ON doccobranza.docCobranza = doccobr_rel_ingbco.docCobranza  WHERE  doccobranza.`docCobranza` LIKE :docCobranza AND doccobranza.`tipoDoc` LIKE :tipoDoc");
  $consulta->bindParam(':docCobranza',$docCobranza);
  $consulta->bindParam(':tipoDoc',$tipoDoc);
  $consulta->execute();
  return $consulta->fetchAll();
}

function procesarOcurrenciasTercero($db,$fchDespacho,$correlativo,$nroDoc,$marca){
  $actualiza = $db->prepare("UPDATE `ocurrenciatercero` SET `pagado` = :marca WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo  ");
  $actualiza->bindParam(':fchDespacho',$fchDespacho);
  $actualiza->bindParam(':correlativo',$correlativo);
  $actualiza->bindParam(':marca',$marca);  
  $actualiza->execute();
}

function generaCorrCliCuenta($db,$id){
  $consulta = $db->prepare("SELECT `correlativo` FROM `clientecuentanew` WHERE `idCliente` LIKE :id ORDER BY correlativo DESC LIMIT 1 ");
  $consulta->bindParam(':id',$id);
  $consulta->execute();
  $resultado = $consulta->fetchAll();
  $correl = '001';
  foreach($resultado as $item) {
    $ultCorrel = $item['correlativo'];
    $correl =   substr("000".(1*$ultCorrel + 1)  , -3);
  }
  return $correl;
}

function generaIdProducto($db){
  $consulta = $db->prepare("SELECT idProducto FROM `clientecuentaproducto` ORDER BY idProducto DESC LIMIT 1  ");
  $consulta->execute();
  $resultado = $consulta->fetchAll();
  $idProducto = '00000001';
  foreach($resultado as $item) {
    $ultId = $item['idProducto'];
    $idProducto =   substr("00000000".(1*$ultId + 1)  , -8);
  }
  return $idProducto;
}

function crearClienteCuenta($db, $id,  $correl, $nombre, $estadoCuenta, $paraMovil, $tipoCuenta, $usuario){
  //$usuario = $_SESSION["usuario"];
  $existe = buscarClientesCuentas03($db, $id, $correl);
  if(count($existe) == 1){
    $actualiza = $db->prepare("UPDATE  `clientecuentanew` SET  `nombreCuenta` = :nombreCuenta, `estadoCuenta` = :estadoCuenta, `paraMovil` = :paraMovil, `tipoCuenta` = :tipoCuenta, editUsuario = :usuario, editFch = curdate() WHERE `idCliente` = :idCliente AND `correlativo` = :correlativo");
    $actualiza->bindParam(':idCliente',$id);
    $actualiza->bindParam(':correlativo',$correl);
    $actualiza->bindParam(':nombreCuenta',$nombre);
    $actualiza->bindParam(':estadoCuenta',$estadoCuenta);
    $actualiza->bindParam(':paraMovil',$paraMovil);
    $actualiza->bindParam(':tipoCuenta',$tipoCuenta);
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->execute();
    $cant =  $actualiza->rowCount();
    return $cant."|".$correl."|Editar";
  } else {
    $correl = $correl == "" ? generaCorrCliCuenta($db,$id) : $correl ;
    $inserta = $db->prepare("REPLACE INTO `clientecuentanew` (`idCliente`, `correlativo`, `nombreCuenta`, `estadoCuenta`, `paraMovil`, `tipoCuenta`, `creacUsuario`, `creacFch`) VALUES (:id, :correl, :nombre, :estadoCuenta, :paraMovil,  :tipoCuenta, :usuario, now())");
    $inserta->bindParam(':id',$id);
    $inserta->bindParam(':correl',$correl);
    $inserta->bindParam(':nombre',$nombre);
    $inserta->bindParam(':estadoCuenta',$estadoCuenta);
    $inserta->bindParam(':paraMovil',$paraMovil);
    $inserta->bindParam(':tipoCuenta',$tipoCuenta);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    $cant =  $inserta->rowCount();
    return $cant."|".$correl."|Crear";
  }

}

function crearEditarClienteCuentaProducto($db, $datos){
  if ($datos['idPdto'] == 'Nuevo Producto' ){
    $idPdto = generaIdProducto($db);
    $cadNuevo01 = " `creacUsuario`, `creacFch`, ";
    $cadNuevo02 = " :usuario , now(), ";

    $inserta = $db->prepare("INSERT INTO `clientecuentaproducto` (`idProducto`, `idCliente`, `correlativo`, `nombProducto`, `m3Facturable`, `puntos`, `idZona`,  `zona`, `estadoProducto`, `precioServ`, `kmEsperado`, `tolerKmEsperado`, `valKmAdic`, `hrasNormales`, `tolerHrasNormales`, `valHraAdic`, `hraIniEsperado`, `tolerHraIniEsperado`, `valAdicHraIniEsper`, `hraFinEsperado`, `tolerHraFinEsperado`, `valAdicHraFinEsper`, `nroAuxiliares`, `valAuxiliarAdic`, `cobrarPeaje`, `cobrarRecojoDevol`, `valConductor`, `valAuxiliar`, `usoMaster`, `valUnidTercCCond`, `valUnidTercSCond`, `hrasNormalTerc`, `tolerHrasNormalTerc`, `valHraExtraTerc`, `valKmAdicTerc`, `creacUsuario`, `creacFch`,  `editUsuario`, `editFch`, hraNormalConductor, tolerHraCond, valHraAdicCond, hraNormalAux, tolerHraAux, valHraAdicAux, valAuxTercero, contarDespacho, superCuenta, tipoProducto ) VALUES
    (:idPdto, :idCliente, :correl, :nombProducto, :m3Facturable, :puntos, :idZona, :zona,  :estadoProducto, :precioServ, :kmEsperado, :tolerKmEsperado, :valKmAdic, :hrasNormales, :tolerHrasNormales, :valHraAdic, :hraIniEsperado, :tolerHraIniEsperado, :valAdicHraIniEsper, :hraFinEsperado, :tolerHraFinEsperado, :valAdicHraFinEsper, :nroAuxiliares, :valAuxiliarAdic, :cobrarPeaje, :cobrarRecojoDevol, :valConductor, :valAuxiliar, :usoMaster, :valUnidTercCCond, :valUnidTercSCond, :hrasNormalTerc, :tolerHrasNormalTerc, :valHraExtraTerc, :valKmAdicTerc, :usuario , now(), :usuario, curdate(), :hraNormalConductor, :tolerHraCond, :valHraAdicCond, :hraNormalAux, :tolerHraAux, :valHraAdicAux, :valAuxTercero, :contarDespacho, :superCuenta, :tipoProducto )");

      $inserta->bindParam(':idCliente',$datos['idCliente']);
      $inserta->bindParam(':correl',$datos['correl']);
      $inserta->bindParam(':tipoProducto',$datos['tipoProducto']);

  } else {
    $idPdto = $datos['idPdto'];

    $inserta = $db->prepare("UPDATE `clientecuentaproducto` SET 
       `nombProducto` = :nombProducto, `m3Facturable` = :m3Facturable, `puntos` = :puntos , `idZona` = :idZona,  `zona` = :zona, `estadoProducto` = :estadoProducto , `precioServ` = :precioServ, `kmEsperado` = :kmEsperado, `tolerKmEsperado` = :tolerKmEsperado, `valKmAdic` = :valKmAdic, `hrasNormales` = :hrasNormales, `tolerHrasNormales` = :tolerHrasNormales, `valHraAdic` = :valHraAdic, `hraIniEsperado` = :hraIniEsperado, `tolerHraIniEsperado` = :tolerHraIniEsperado, `valAdicHraIniEsper` = :valAdicHraIniEsper, `hraFinEsperado` = :hraFinEsperado, `tolerHraFinEsperado` = :tolerHraFinEsperado, `valAdicHraFinEsper` = :valAdicHraFinEsper, `nroAuxiliares` = :nroAuxiliares, `valAuxiliarAdic` = :valAuxiliarAdic, `cobrarPeaje` = :cobrarPeaje, `cobrarRecojoDevol` = :cobrarRecojoDevol, `valConductor` = :valConductor, `valAuxiliar` = :valAuxiliar, `usoMaster` = :usoMaster, `valUnidTercCCond` = :valUnidTercCCond, `valUnidTercSCond` = :valUnidTercSCond, `hrasNormalTerc` = :hrasNormalTerc, `tolerHrasNormalTerc` = :tolerHrasNormalTerc, `valHraExtraTerc` = :valHraExtraTerc, `valKmAdicTerc` = :valKmAdicTerc,  `editUsuario` = :usuario, `editFch` = curdate(), hraNormalConductor = :hraNormalConductor, tolerHraCond = :tolerHraCond, valHraAdicCond = :valHraAdicCond, hraNormalAux = :hraNormalAux, tolerHraAux = :tolerHraAux, valHraAdicAux = :valHraAdicAux, valAuxTercero = :valAuxTercero , contarDespacho = :contarDespacho, superCuenta = :superCuenta  WHERE idProducto =  :idPdto ");

  }

  $inserta->bindParam(':idPdto',$idPdto);
  $inserta->bindParam(':nombProducto',$datos['nombProducto']);
  $inserta->bindParam(':m3Facturable',$datos['m3Facturable']);
  $inserta->bindParam(':puntos',$datos['puntos']);
  $inserta->bindParam(':idZona',$datos['idZona']);
  $inserta->bindParam(':zona',$datos['zona']);
  $inserta->bindParam(':estadoProducto',$datos['estadoProducto']);
  $inserta->bindParam(':precioServ',$datos['precioServ']);
  $inserta->bindParam(':kmEsperado',$datos['kmEsperado']);
  $inserta->bindParam(':tolerKmEsperado',$datos['tolerKmEsperado']);
  $inserta->bindParam(':valKmAdic',$datos['valKmAdic']);
  $inserta->bindParam(':hrasNormales',$datos['hrasNormales']);
  $inserta->bindParam(':tolerHrasNormales',$datos['tolerHrasNormales']);
  $inserta->bindParam(':valHraAdic',$datos['valHraAdic']);
  $inserta->bindParam(':hraIniEsperado',$datos['hraIniEsperado']);
  $inserta->bindParam(':tolerHraIniEsperado',$datos['tolerHraIniEsperado']);
  $inserta->bindParam(':valAdicHraIniEsper',$datos['valAdicHraIniEsper']);
  $inserta->bindParam(':hraFinEsperado',$datos['hraFinEsperado']);
  $inserta->bindParam(':tolerHraFinEsperado',$datos['tolerHraFinEsperado']);
  $inserta->bindParam(':valAdicHraFinEsper',$datos['valAdicHraFinEsper']);
  $inserta->bindParam(':nroAuxiliares',$datos['nroAuxiliares']);
  $inserta->bindParam(':valAuxiliarAdic',$datos['valAuxiliarAdic']);

  $inserta->bindParam(':cobrarPeaje',$datos['cobrarPeaje']);
  $inserta->bindParam(':cobrarRecojoDevol',$datos['cobrarRecojoDevol']);
  $inserta->bindParam(':valConductor',$datos['valConductor']);
  $inserta->bindParam(':valAuxiliar',$datos['valAuxiliar']);
  $inserta->bindParam(':usoMaster',$datos['usoMaster']);
  $inserta->bindParam(':valUnidTercCCond',$datos['valUnidTercCCond']);

  $inserta->bindParam(':valUnidTercSCond',$datos['valUnidTercSCond']);
  $inserta->bindParam(':hrasNormalTerc',$datos['hrasNormalTerc']);
  $inserta->bindParam(':tolerHrasNormalTerc',$datos['tolerHrasNormalTerc']);
  $inserta->bindParam(':valHraExtraTerc',$datos['valHraExtraTerc']);
  $inserta->bindParam(':valKmAdicTerc',$datos['valKmAdicTerc']);
  $inserta->bindParam(':usuario',$datos['usuario']);

  $inserta->bindParam(':hraNormalConductor',$datos['hraNormalConductor']);
  $inserta->bindParam(':tolerHraCond',$datos['tolerHraConductor']);
  $inserta->bindParam(':valHraAdicCond',$datos['valHraExtraCond']);
  $inserta->bindParam(':hraNormalAux',$datos['hraNormalAux']);
  $inserta->bindParam(':tolerHraAux',$datos['tolerHraAux']);
  $inserta->bindParam(':valHraAdicAux',$datos['valHraExtraAux']);
  $inserta->bindParam(':valAuxTercero',$datos['valAuxiliarTercero']);

  $inserta->bindParam(':contarDespacho',$datos['valContarDespacho']);
  $inserta->bindParam(':superCuenta',$datos['valSuperCuenta']);

  $inserta->execute();
  return $inserta->rowCount();
  
}

function cargarCmbPdtos($db, $idCliente, $correl){
  //$consulta = $db->prepare("SELECT `idProducto`, `nombProducto` FROM `clientecuentaproducto` WHERE estadoProducto = 'Activo' AND `idCliente` LIKE :idCliente AND `correlativo` LIKE :correl ");
  $consulta = $db->prepare("SELECT `idProducto`, `nombProducto` FROM `clientecuentaproducto` WHERE  `idCliente` LIKE :idCliente AND `correlativo` LIKE :correl ");
  $consulta->bindParam(':idCliente',$idCliente);
  $consulta->bindParam(':correl',$correl);
  $consulta->execute();
  $resultado = $consulta->fetchAll();
  $cadena = "<option>Nuevo Producto</option>";
  foreach($resultado as $item) {
    $cadena .= "<option  value = '".$item['idProducto']."'>".$item['nombProducto']."</option>";
  }
  echo $cadena;
}

function buscarDataPdto($db, $idProducto){
  $consulta = $db->prepare("SELECT `idProducto`, `idCliente`, `correlativo`, `nombProducto`, `m3Facturable`, `puntos`, `idZona`, `zona`, `estadoProducto`, `precioServ`, `kmEsperado`, `tolerKmEsperado`, `valKmAdic`, `hrasNormales`, `tolerHrasNormales`, `valHraAdic`, `hraIniEsperado`,  `tolerHraIniEsperado`, `valAdicHraIniEsper`, `hraFinEsperado`, `tolerHraFinEsperado`, `valAdicHraFinEsper`, `nroAuxiliares`, `valAuxiliarAdic`, `cobrarPeaje`, `cobrarRecojoDevol`, `valConductor`, `valAuxiliar`, `usoMaster`, `valUnidTercCCond`, `valUnidTercSCond`, `hrasNormalTerc`, `tolerHrasNormalTerc`, `valHraExtraTerc`, `valKmAdicTerc`, `creacUsuario`, `creacFch`,  hraNormalConductor, tolerHraCond, valHraAdicCond, hraNormalAux, tolerHraAux, valHraAdicAux, valAuxTercero, contarDespacho, superCuenta FROM `clientecuentaproducto` WHERE `idProducto` = :idProducto ");
  $consulta->bindParam(':idProducto',$idProducto);
  $consulta->execute();
  $resultado = $consulta->fetchAll();
  $cadena = "";
  foreach($resultado as $item) {
    $cadena = $item['nombProducto']."|".$item['m3Facturable']."|".$item['puntos']."|".$item['idZona']."|".$item['precioServ']."|".$item['kmEsperado']."|".$item['tolerKmEsperado']."|".$item['valKmAdic']."|".$item['hrasNormales']."|".$item['tolerHrasNormales']."|".$item['valHraAdic']."|".$item['hraIniEsperado']."|".$item['valAdicHraIniEsper']."|".$item['hraFinEsperado']."|".$item['valAdicHraFinEsper']."|".$item['nroAuxiliares']."|".$item['valAuxiliarAdic']."|".$item['cobrarPeaje']."|".$item['cobrarRecojoDevol']."|".$item['valConductor']."|".$item['valAuxiliar']."|".$item['usoMaster']."|".$item['valUnidTercCCond']."|".$item['valUnidTercSCond']."|".$item['hrasNormalTerc']."|".$item['tolerHrasNormalTerc']."|".$item['valHraExtraTerc']."|".$item['valKmAdicTerc']."|".$item['tolerHraIniEsperado']."|".$item['tolerHraFinEsperado'] ."|".$item['estadoProducto']."|".$item['hraNormalConductor']."|".$item['tolerHraCond']."|".$item['valHraAdicCond']."|".$item['hraNormalAux']."|".$item['tolerHraAux']."|".$item['valHraAdicAux']."|".$item['valAuxTercero'] ."|".$item['contarDespacho']."|".$item['superCuenta']."|".$item['creacUsuario']."|".$item['creacFch'] ;
  }
  echo $cadena;

}

function buscarClientesCuentas02($db,$idCliente, $corr = NULL, $estadoCuenta = NULL){
  $where = "";
  $where .= $corr == NULL ? "":" AND correlativo = :corr ";
  $where .= $estadoCuenta == NULL ? "":" AND estadoCuenta = :estadoCuenta ";
  $consulta = $db->prepare("SELECT `idCliente`, `correlativo`, `tipoCuenta`, `nombreCuenta`, `estadoCuenta`, `paraMovil`, `creacUsuario`, `creacFch`, `editUsuario`, `editFch` FROM `clientecuentanew` WHERE `idCliente` LIKE :idCliente $where ");
  $consulta->bindParam(':idCliente',$idCliente);
  if ($corr != NULL) $consulta->bindParam(':corr',$corr);
  if ($estadoCuenta != NULL) $consulta->bindParam(':estadoCuenta',$estadoCuenta);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarClientesCuentas03($db,$idCliente, $corr = NULL, $estadoCuenta = NULL){
  //Esta funcion ha sido añadida por problmas a verificar si existía una cuenta
  $where = "";
  $where .= $corr == NULL ? " AND correlativo = '' ":" AND correlativo = :corr ";
  $where .= $estadoCuenta == NULL ? "":" AND estadoCuenta = :estadoCuenta ";
  $consulta = $db->prepare("SELECT `idCliente`, `correlativo`, `tipoCuenta`, `nombreCuenta`, `estadoCuenta`, `paraMovil`, `creacUsuario`, `creacFch`, `editUsuario`, `editFch` FROM `clientecuentanew` WHERE `idCliente` LIKE :idCliente $where ");
  $consulta->bindParam(':idCliente',$idCliente);
  if ($corr != NULL) $consulta->bindParam(':corr',$corr);
  if ($estadoCuenta != NULL) $consulta->bindParam(':estadoCuenta',$estadoCuenta);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarDataPdtos($db, $idCliente = NULL, $estadoProducto = NULL, $idProducto = NULL){
  $where = "";
  $where .= $idCliente == NULL ? "":" AND  `clientecuentaproducto`.`idCliente` = :idCliente";
  $where .= $idProducto == NULL ? "":" AND  `idProducto` = :idProducto";

  $consulta = $db->prepare("SELECT `idProducto` , `clientecuentaproducto`.`idCliente` , `clientecuentaproducto`.`correlativo` ,  `clientecuentanew`.nombreCuenta , `nombProducto` , `m3Facturable` , `puntos` , `zona` , `estadoProducto` , `precioServ` , `kmEsperado` , `tolerKmEsperado` , `valKmAdic` , `hrasNormales` , `tolerHrasNormales` , `valHraAdic` , `hraIniEsperado` , `valAdicHraIniEsper` , `hraFinEsperado` , `valAdicHraFinEsper` , `nroAuxiliares` , `valAuxiliarAdic` , `cobrarPeaje` , `cobrarRecojoDevol` , `valConductor` , `valAuxiliar` , `usoMaster` , `valUnidTercCCond` , `valUnidTercSCond` , `hrasNormalTerc` , `tolerHrasNormalTerc` , `valHraExtraTerc` , `valKmAdicTerc` , `clientecuentaproducto`.`creacUsuario` , `clientecuentaproducto`.`creacFch` FROM `clientecuentaproducto` , clientecuentanew WHERE `clientecuentaproducto`.idCliente = `clientecuentanew`.idCliente AND `clientecuentaproducto`.correlativo = `clientecuentanew`.correlativo AND `clientecuentanew`.estadoCuenta = 'Activo' $where ");
  if ($idCliente != NULL) $consulta->bindParam(':idCliente',$idCliente);
  if ($idProducto != NULL) $consulta->bindParam(':idProducto',$idProducto);
  $consulta->execute();
  return $consulta->fetchAll();
}

function eliminaClienteCuenta02($db, $id, $corr){
  $elimina = $db->prepare("DELETE FROM `clientecuentanew` WHERE `idCliente` = :idCliente AND `correlativo` = :correlativo");
  $elimina->bindParam(':idCliente',$id);
  $elimina->bindParam(':correlativo',$corr);
  $elimina->execute();
  return  $elimina->rowCount();
}



  function buscarClientesSuperCuentas($db,$idCliente, $superCuenta = null, $estado = null){

    $where = "";
    $where .= $superCuenta == NULL ? "":" AND superCuenta = :superCuenta ";
    $where .= $estado == NULL ? "":" AND estado = :estado ";
    $consulta = $db->prepare("SELECT `idCliente`, `superCuenta`, `estado`, `creacFch`, `creacUsuario`, `editaFch`, `editaUsuario` FROM `clientesupercuenta` WHERE idCliente = :idCliente $where ");
    $consulta->bindParam(':idCliente',$idCliente);
    if ($superCuenta != NULL) $consulta->bindParam(':superCuenta',$superCuenta);
    if ($estado != NULL) $consulta->bindParam(':estado',$estado);
    $consulta->execute();
    return $consulta->fetchAll();

  }

  function buscarCondicionesDePago($db,$estado = NULL){
    $where = ($estado != NULL) ? " AND estadoCondicion = :estado " : "" ;

    $consulta = $db->prepare("SELECT `idCondicion`, `nombCondicion`, `descripCondicion`, `nroDias`, `estadoCondicion`, `creacUsuario`, `creacFch`, `editaUsuario`, `editaFch` FROM `clientecondicionespago` WHERE 1  $where ");
    if ($estado != NULL) $consulta->bindParam(':estado',$estado);
    $consulta->execute();
    return $consulta->fetchAll();
  }

?>
