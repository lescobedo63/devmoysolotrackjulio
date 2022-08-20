<?php
global $servidor, $bd, $usuario, $contrasenia;
$db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);

function licenciasObligatorias($db){
  $consulta = $db->prepare("SELECT `nombLicencia` FROM `vehiculolicenciasdefinir` WHERE `obligatorio` = 'Si'");                 
  $consulta->execute();
  return $consulta->fetchAll();
};

function verificaLicenciaActiva($db,$licencia){
  $consulta = $db->prepare("SELECT `vehiculo`.idPlaca, `vehiculolicencias`.estado  FROM `vehiculo` LEFT JOIN `vehiculolicencias` ON `vehiculo`.idPlaca = `vehiculolicencias`.idPlaca AND `vehiculolicencias`.nombre = :licencia AND `vehiculolicencias`.estado = 'Activo' WHERE vehiculo.checkAlertas = 'Si' AND vehiculo.estado = 'Activo'");
  $consulta->bindParam(':licencia',$licencia);
  $consulta->execute();
  return $consulta->fetchAll();
};

function mantenimientosObligatorios($db){
  $consulta = $db->prepare("SELECT `idAlerta` FROM `alertastipo` WHERE `tipoUso` = 'Vehiculo' AND obligatorio = 'Si'");                 
  $consulta->execute();
  return $consulta->fetchAll();
};

function verificaMantenimientoActivo($db,$alerta){
  $consulta = $db->prepare("SELECT `vehiculo`.idPlaca, `vehiculomantenimiento`.estado  FROM `vehiculo` LEFT JOIN `vehiculomantenimiento` ON `vehiculo`.idPlaca = `vehiculomantenimiento`.idPlaca AND `vehiculomantenimiento`.alerta = :alerta AND `vehiculomantenimiento`.estado = 'Activo' WHERE vehiculo.checkAlertas = 'Si' AND vehiculo.estado = 'Activo'");
  $consulta->bindParam(':alerta',$alerta);
  $consulta->execute();
  return $consulta->fetchAll();
};

function buscarDataIcnluye($db){
  $consulta = $db->prepare("SELECT * FROM `gestion_sst_covid`");
  $consulta->execute();
  return $consulta->fetchAll();
};

function marcarVehicMoy($db){
  $auxLicencias = licenciasObligatorias($db);
  foreach ($auxLicencias as $item){
    $licencia = $item['nombLicencia'];
    $linea = verificaLicenciaActiva($db,$licencia);
    foreach ($linea as $itemLicencia){
      $placa = $itemLicencia['idPlaca'];
      $estado = ($itemLicencia['estado']== null)?'Inactivo':'Activo';
      //if ($placa == 'A2R-846' ) echo "Placa $placa Licencia $licencia  Estado $estado ";

      if (!isset($_SESSION[$placa."@"]) || $_SESSION[$placa."@"]=='Activo' ){

        $_SESSION[$placa."@"] =  $estado;
        //if ($placa == 'A2R-846' ) echo "--->".$_SESSION[$placa."@"];
      }
    }
  }

  $auxManten = mantenimientosObligatorios($db);
  foreach ($auxManten as $item){
    $alerta = $item['idAlerta'];
    $linea = verificaMantenimientoActivo($db,$alerta);
    foreach ($linea as $itemManten){
      $placa = $itemManten['idPlaca'];
      $estado = ($itemManten['estado']== null)?'Inactivo':'Activo';
      //if ($placa == 'A2R-846' ) echo "Placa $placa Alerta $alerta  Estado $estado ";
      if (!isset($_SESSION[$placa."@"]) || $_SESSION[$placa."@"]=='Activo' )
        $_SESSION[$placa."@"] =  $estado;
    } 
  }
  //print_r($_SESSION);
}


function buscarTodasLasCategorias($db){
	$consulta = $db->prepare("SELECT * FROM categoria");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarAdvertenciasSolicitudesAbastecimiento($db){
  $consulta = $db->prepare("SELECT fecha, placa, conductor, grifo, estado FROM solicitud_abastecimiento WHERE estado != 'Abastecido'");
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarEntidadPensiones($db,$nombre = NULL){
	$where = ($nombre == NULL)?"":" WHERE nombre = :nombre  ";
	$consulta = $db->prepare("SELECT * FROM pension $where");
	if ($nombre != NULL) $consulta->bindParam(':nombre',$nombre);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarTodosLosTelefonos($db){
	$consulta = $db->prepare("SELECT * FROM telefono ");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarAdvertenciasVehiculos($db){
	$consulta = $db->prepare("SELECT vehlic.`idPlaca`, vehlic.`fchInicio`, vehlic.`nombre`, vehlic.`fchFin`, datediff(vehlic.`fchFin`, curdate()) as falta, `veh`.estado AS estadoVeh FROM `vehiculolicencias` AS vehlic, vehiculo AS veh WHERE `vehlic`.idPlaca = `veh`.idPlaca AND vehlic.`estado` = 'activo' AND `veh`.estado = 'Activo' AND datediff(vehlic.`fchFin`, curdate()) <= vehlic.`plazo` order by falta");
	//$consulta->bindParam(':limite',$advertencias);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscaralertasusuarios($db){
    $consulta = $db->prepare("SELECT `dni`, `t`.`apPaterno`, `t`.`apMaterno`, `t`.`nombres`, `a`.`nivel` from `alertasresponsable` as a,`trabajador` as `t` where `a`.`dni`=`t`.`idtrabajador`");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscaralertasgerencialusuarios($db){
    $consulta = $db->prepare("SELECT `dni`, `t`.`apPaterno`, `t`.`apMaterno`, `t`.`nombres`, `a`.`nivel` from `alertasgerencial` as a,`trabajador` as `t` where `a`.`dni`=`t`.`idtrabajador`");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarResponsablesAlertasGerencial($db){
  $consulta = $db->prepare("SELECT CONCAT(nombres,' ', apPaterno,' ',apMaterno) AS nombreCompleto, `trabajador`.`email`, `nivel` FROM `alertasgerencial`, `trabajador` WHERE `alertasgerencial`.`dni` =  `trabajador`.`idTrabajador`");
  $consulta->execute();
	return $consulta->fetchAll();
};


function buscarAdvertenciasVehiculosMantenimientoKms($db){

	$consulta = $db->prepare("SELECT `veh`.`idPlaca`, `veh`.`kmUltimaMedicion` as ultMarca, `plazo`,`alerta`, `vehman`.`marcaFin` as marcaFin, `vehman`.`marcaFin` - `kmUltimaMedicion` as falta , veh.estado FROM `vehiculomantenimiento` AS vehman, `vehiculo` AS veh WHERE `vehman`.`idPlaca` = `veh`.`idPlaca` AND veh.estado = 'Activo' AND `vehman`.`estado` = 'Activo' AND `unidad` = 'kms' AND considerarPropio = 'Si' having marcaFin - ultMarca <= plazo ORDER BY `falta`"); 
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarAdvertenciasVehiculosMantenimientoDias($db, $alerta = null){
/*	if ($alerta == null){
	  $consulta = $db->prepare("SELECT `vehiculo`.`idPlaca`, `plazo`,`alerta`, `vehiculomantenimiento`.`marcaFin`, datediff(`vehiculomantenimiento`.`marcaFin`, curdate()) as falta FROM `vehiculomantenimiento`, `vehiculo` WHERE `vehiculomantenimiento`.`idPlaca` = `vehiculo`.`idPlaca` AND `vehiculomantenimiento`.`estado` = 'Activo' AND `unidad` = 'Días' AND datediff(`vehiculomantenimiento`.`marcaFin`, curdate()) <= plazo order by falta");
	} else {
	  $consulta = $db->prepare("SELECT  `vehiculo`.`idPlaca`,  `plazo`,`alerta`, `vehiculomantenimiento`.`marcaFin`, datediff(`vehiculomantenimiento`.`marcaFin`, curdate()) as falta FROM `vehiculomantenimiento`, `vehiculo`  WHERE `vehiculomantenimiento`.`idPlaca` = `vehiculo`.`idPlaca` AND `vehiculomantenimiento`.`estado` = 'Activo' AND `unidad` = 'Días' AND `alerta` = '$alerta' AND datediff(`vehiculomantenimiento`.`marcaFin`, curdate()) <= plazo order by falta");
	}
*/
	$where = ($alerta == null) ? "": " And alerta = '$alerta' ";
	$consulta = $db->prepare("SELECT `veh`.`idPlaca`, `plazo`,`alerta`, `vehman`.`marcaFin`, datediff(`vehman`.`marcaFin`, curdate()) as falta FROM `vehiculomantenimiento` AS vehman, `vehiculo` AS veh WHERE `vehman`.`idPlaca` = `veh`.`idPlaca` AND `vehman`.`estado` = 'Activo' AND `veh`.`estado` = 'Activo' AND `unidad` = 'Días'  $where  AND datediff(`vehman`.`marcaFin`, curdate()) <= plazo AND considerarPropio = 'Si' order by falta");



	
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarAdvertenciasTrabajador($db,$advertencias){
	$consulta = $db->prepare("SELECT `idTrabajador`, `fchInicio`, `nombre`, `fchFin`, `plazo`, datediff(`fchFin`, curdate()) as falta FROM `trabajadorlicencias` WHERE  datediff(`fchFin`, curdate()) <= :limite order by falta");
	$consulta->bindParam(':limite',$advertencias);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarOnomasticos($db){
	$consulta = $db->prepare("SELECT concat(`nombres`,' ',`apPaterno`,' ', `apMaterno`) as nombreCompleto , `fchNacimiento` FROM `trabajador` WHERE  month(`fchNacimiento`) = month(curdate())  AND `estadoTrabajador` = 'Activo'  order by month(`fchNacimiento`), day(`fchNacimiento`)");
	$consulta->execute();
	return $consulta->fetchAll();
}


function buscarResponsablesAlertas($db){
  $consulta = $db->prepare("SELECT CONCAT(nombres,' ', apPaterno,' ',apMaterno) AS nombreCompleto, `trabajador`.`email`, `nivel` FROM `alertasresponsable`, `trabajador` WHERE `alertasresponsable`.`dni` =  `trabajador`.`idTrabajador`");
  $consulta->execute();
	return $consulta->fetchAll();
};

function buscarTodasLasMudanzas($db){
	$consulta = $db->prepare("SELECT * FROM clientenn WHERE id = 'Mudanza' ");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarDatosMudanza($db,$tipoServicio){
	$consulta = $db->prepare("SELECT * FROM clientenn WHERE id = 'Mudanza' AND tipoServicio = :tipoServicio ");
	$consulta->bindParam(':tipoServicio',$tipoServicio);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarLicenciasDefinir($db){
	$consulta = $db->prepare("SELECT *  FROM `vehiculolicenciasdefinir`");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarNombLicencia($db,$nombLicencia){
	$consulta = $db->prepare("SELECT *  FROM `vehiculolicenciasdefinir` WHERE `nombLicencia` LIKE :nombLicencia ");
	$consulta->bindParam(':nombLicencia',$nombLicencia);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarTodoGrupoRendimiento($db){
	$consulta = $db->prepare("SELECT *  FROM `vehiculogruporendimiento`");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarGrupoRendimiento($db,$grupoRendimiento){
	$consulta = $db->prepare("SELECT *  FROM `vehiculogruporendimiento` WHERE `grupoRendimiento` LIKE :grupoRendimiento ");
	$consulta->bindParam(':grupoRendimiento',$grupoRendimiento);
	$consulta->execute();
	return $consulta->fetchAll();
 }
 
function buscarEstadosTelefonos($db){
	$consulta = $db->prepare("SHOW COLUMNS FROM telefono LIKE 'estado' ");
	$consulta->execute();
	$array = $consulta->fetch(PDO::FETCH_ASSOC);
	//echo "ARREGLO ".$array;
	//$array = explode("','",preg_replace("/(enum|set)\('(.+?)'\)/","\\2",$array['Type']));
  $array = explode("','", preg_replace("/(enum)\('(.+?)'\)/","\\2",$array['Type']) );
	  //return explode("','",preg_replace("/(enum|set)\('(.+?)'\)/","\\2",$tempo[0]));
return $array;
}
/*
function buscarSucursalesRipley($db){
	$consulta = $db->prepare("SELECT * FROM `sucursalripley`");
	$consulta->execute();
	return $consulta->fetchAll();
}*/

/*
function buscarZonasRipley($db){
	$consulta = $db->prepare("SELECT * FROM `zonaripley`");
	$consulta->execute();
	return $consulta->fetchAll();
}*/
function buscarTodosLosUsuarios($db){
	$consulta = $db->prepare("SELECT `idUsuario`,`admin`, `edicion`, `ingreso`, `consulta`, `nombre`, `dni`,`planilla`, `admCostos`, `admArt`, `admPlanilla`,  `admUnif`, `plame`, `admMovil`, `tipoUsuario`, `cargaDespachos`, `seguridad`, `invitado`, `fchCreacion`, `fchVencimiento` FROM `usuario`");
  $consulta->execute();
	return $consulta->fetchAll();
}

function buscarDatosUsuario($db,$idUsuario){
  $consulta = $db->prepare("SELECT `nombre`, `contrasenia`, `admin`, `admCostos`, `admArt`, `dni`,  `edicion`, `ingreso`, `consulta`, `planilla`,`admPlanilla`,`asignDespacho`, `admUnif`,  `plame`,  `admMovil`, `tipoUsuario`, `cargaDespachos`,  `seguridad`, `invitado`, `email`, `fchVencimiento`, `estado` FROM `usuario` WHERE `idUsuario` = :idUsuario  ");
  $consulta->bindParam(':idUsuario',$idUsuario);
  $consulta->execute();
	return $consulta->fetchAll();
};

function buscarTerceros($db){
  $consulta = $db->prepare("SELECT `documento`, `nombreCompleto`, `estadoTercero` FROM `vehiculodueno` ORDER BY `nombreCompleto`");
  $consulta->execute();
	return $consulta->fetchAll(); 
}

function buscarDatosTercero($db,$documento){
  $consulta = $db->prepare("SELECT `documento`, `nombreCompleto`, `nroTelefono`,`eMail`, `bancoNombre`, `bancoCuentaNro`, `bancoCuentaTipo`, `bancoCuentaMoneda` , `cuentaDetraccion`, `estadoTercero` FROM `vehiculodueno` WHERE `documento` =  :documento");
  $consulta->bindParam(':documento',$documento);
  $consulta->execute();
	return $consulta->fetchAll();
}

function buscarListadoMercaderiaDReporte($db,$fchIni,$fchFin){
  if($fchIni == '' && $fchFin == ''){
    $consulta = $db->prepare("SELECT * FROM incidencias_mercaderia WHERE MONTH(fecha_registro) = MONTH(CURDATE())");
    $consulta->execute();
  }
  else if($fchIni != '' && $fchFin == ''){
    $consulta = $db->prepare("SELECT * FROM incidencias_mercaderia WHERE LEFT((fecha_registro),10) = :fchIni");
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->execute();
  }
  else if($fchIni == '' && $fchFin != ''){
    $consulta = $db->prepare("SELECT * FROM incidencias_mercaderia WHERE LEFT((fecha_registro),10) = :fchFin");
    $consulta->bindParam(':fchFin',$fchFin);
    $consulta->execute();
  }
  else if($fchIni != '' && $fchFin != ''){
    $consulta = $db->prepare("SELECT * FROM incidencias_mercaderia WHERE LEFT((fecha_registro),10) >= :fchIni AND LEFT((fecha_registro),10) <= :fchFin");
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->bindParam(':fchFin',$fchFin);
    $consulta->execute();
  }
  return $consulta->fetchAll();
}
function buscarListadoChoqueReporte($db,$fchIni,$fchFin){

  if($fchIni == '' && $fchFin == ''){
    $consulta = $db->prepare("SELECT * FROM incidencias_vehiculo WHERE MONTH(fecha_registro) = MONTH(CURDATE())");
    $consulta->execute();
  }
  else if($fchIni != '' && $fchFin == ''){
    $consulta = $db->prepare("SELECT * FROM incidencias_vehiculo WHERE LEFT((fecha_registro),10) = :fchIni");
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->execute();
  }
  else if($fchIni == '' && $fchFin != ''){
    $consulta = $db->prepare("SELECT * FROM incidencias_vehiculo WHERE LEFT((fecha_registro),10) = :fchFin");
    $consulta->bindParam(':fchFin',$fchFin);
    $consulta->execute();
  }
  else if($fchIni != '' && $fchFin != ''){
    $consulta = $db->prepare("SELECT * FROM incidencias_vehiculo WHERE LEFT((fecha_registro),10) >= :fchIni AND LEFT((fecha_registro),10) <= :fchFin");
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->bindParam(':fchFin',$fchFin);
    $consulta->execute();
  }
  return $consulta->fetchAll();
}

function buscarListadoTelefonoRReporte($db,$fchIni,$fchFin){
  if($fchIni == '' && $fchFin == ''){
    $consulta = $db->prepare("SELECT * FROM incidencias_telefono WHERE MONTH(fecha_registro) = MONTH(CURDATE())");
    $consulta->execute();
  }
  else if($fchIni != '' && $fchFin == ''){
    $consulta = $db->prepare("SELECT * FROM incidencias_telefono WHERE LEFT((fecha_registro),10) = :fchIni");
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->execute();
  }
  else if($fchIni == '' && $fchFin != ''){
    $consulta = $db->prepare("SELECT * FROM incidencias_telefono WHERE LEFT((fecha_registro),10) = :fchFin");
    $consulta->bindParam(':fchFin',$fchFin);
    $consulta->execute();
  }
  else if($fchIni != '' && $fchFin != ''){
    $consulta = $db->prepare("SELECT * FROM incidencias_telefono WHERE LEFT((fecha_registro),10) >= :fchIni AND LEFT((fecha_registro),10) <= :fchFin");
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->bindParam(':fchFin',$fchFin);
    $consulta->execute();
  }
  return $consulta->fetchAll();
}
function buscarListadoTelefonoSusReporte($db,$fchIni,$fchFin){

  if($fchIni == '' && $fchFin == ''){
    $consulta = $db->prepare("SELECT * FROM sustento_telefono WHERE MONTH(fecha_registro) = MONTH(CURDATE())");
    $consulta->execute();
  }
  else if($fchIni != '' && $fchFin == ''){
    $consulta = $db->prepare("SELECT * FROM sustento_telefono WHERE LEFT((fecha_registro),10) = :fchIni");
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->execute();
  }
  else if($fchIni != '' && $fchFin != ''){
    $consulta = $db->prepare("SELECT * FROM sustento_telefono WHERE LEFT((fecha_registro),10) >= :fchIni AND LEFT((fecha_registro),10) <= :fchFin");
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->bindParam(':fchFin',$fchFin);
    $consulta->execute();
  }
  return $consulta->fetchAll();
}
function buscarListadoPapeletaReporte($db,$fchIni,$fchFin){

  if($fchIni == '' && $fchFin == ''){
    $consulta = $db->prepare("SELECT * FROM incidencias_infraccion WHERE MONTH(fecha_registro) = MONTH(CURDATE())");
    $consulta->execute();
  }
  else if($fchIni != '' && $fchFin == ''){
    $consulta = $db->prepare("SELECT * FROM incidencias_infraccion WHERE LEFT((fecha_registro),10) = :fchIni");
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->execute();
  }
  else if($fchIni == '' && $fchFin != ''){
    $consulta = $db->prepare("SELECT * FROM incidencias_infraccion WHERE LEFT((fecha_registro),10) = :fchFin");
    $consulta->bindParam(':fchFin',$fchFin);
    $consulta->execute();
  }
  else if($fchIni != '' && $fchFin != ''){
    $consulta = $db->prepare("SELECT * FROM incidencias_infraccion WHERE LEFT((fecha_registro),10) >= :fchIni AND LEFT((fecha_registro),10) <= :fchFin");
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->bindParam(':fchFin',$fchFin);
    $consulta->execute();
  }
  return $consulta->fetchAll();
}


function buscarListadoAbastecimientoReporte($db,$fchIni,$fchFin){

  if($fchIni == '' && $fchFin == ''){
    $consulta = $db->prepare("SELECT ia.fecha, ia.placa, concat(t1.apPaterno, ' ', t1.apMaterno, ', ', t1.nombres) AS conductor, ia.grifo, ia.tipo_combustible, ia.kilometraje_anterior, ia.kilometraje_actual, ia.und_medida, ia.cantidad, ia.importe, ia.estado, ia.modoCreacion, ia.fecha_registro, concat(t2.apPaterno, ' ', t2.apMaterno, ', ', t2.nombres) AS conductorRegistro, ia.fecha_edicion, ia.usuario FROM incidencias_abastecimiento ia INNER JOIN trabajador t1 ON ia.idConductor = t1.idTrabajador INNER JOIN trabajador t2 ON ia.idTrabajador = t2.idTrabajador WHERE MONTH(fecha_registro) = MONTH(CURDATE())");
    $consulta->execute();
  }
  else if($fchIni != '' && $fchFin == ''){
    $consulta = $db->prepare("SELECT ia.fecha, ia.placa, concat(t1.apPaterno, ' ', t1.apMaterno, ', ', t1.nombres) AS conductor, ia.grifo, ia.tipo_combustible, ia.kilometraje_anterior, ia.kilometraje_actual, ia.und_medida, ia.cantidad, ia.importe, ia.estado, ia.modoCreacion, ia.fecha_registro, concat(t2.apPaterno, ' ', t2.apMaterno, ', ', t2.nombres) AS conductorRegistro, ia.fecha_edicion, ia.usuario FROM incidencias_abastecimiento ia INNER JOIN trabajador t1 ON ia.idConductor = t1.idTrabajador INNER JOIN trabajador t2 ON ia.idTrabajador = t2.idTrabajador WHERE LEFT((fecha_registro),10) = :fchIni");
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->execute();
  }
  else if($fchIni != '' && $fchFin != ''){
    $consulta = $db->prepare("SELECT ia.fecha, ia.placa, concat(t1.apPaterno, ' ', t1.apMaterno, ', ', t1.nombres) AS conductor, ia.grifo, ia.tipo_combustible, ia.kilometraje_anterior, ia.kilometraje_actual, ia.und_medida, ia.cantidad, ia.importe, ia.estado, ia.modoCreacion, ia.fecha_registro, concat(t2.apPaterno, ' ', t2.apMaterno, ', ', t2.nombres) AS conductorRegistro, ia.fecha_edicion, ia.usuario FROM incidencias_abastecimiento ia INNER JOIN trabajador t1 ON ia.idConductor = t1.idTrabajador INNER JOIN trabajador t2 ON ia.idTrabajador = t2.idTrabajador WHERE LEFT((fecha_registro),10) >= :fchIni AND LEFT((fecha_registro),10) <= :fchFin");
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->bindParam(':fchFin',$fchFin);
    $consulta->execute();
  }
  return $consulta->fetchAll();
}


function infoPorMes($db,$mes,$anho){
	$consulta = $db->prepare("SELECT trabajador.`idTrabajador`,concat(nombres,' ', apPaterno) as nombre , cliente.nombre as nombreCli, despacho.placa , sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 1, valorRol + valorAdicional,0)) as dia01, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 2, valorRol + valorAdicional,0)) as dia02, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 3, valorRol + valorAdicional,0)) as dia03, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 4, valorRol + valorAdicional,0)) as dia04, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 5, valorRol + valorAdicional,0)) as dia05, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 6, valorRol + valorAdicional,0)) as dia06, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 7, valorRol + valorAdicional,0)) as dia07, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 8, valorRol + valorAdicional,0)) as dia08, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 9, valorRol + valorAdicional,0)) as dia09, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 10, valorRol + valorAdicional,0)) as dia10, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 11, valorRol + valorAdicional,0)) as dia11, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 12, valorRol + valorAdicional,0)) as dia12, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 13, valorRol + valorAdicional,0)) as dia13, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 14, valorRol + valorAdicional,0)) as dia14, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 15, valorRol + valorAdicional,0)) as dia15, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 16, valorRol + valorAdicional,0)) as dia16, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 17, valorRol + valorAdicional,0)) as dia17, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 18, valorRol + valorAdicional,0)) as dia18, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 19, valorRol + valorAdicional,0)) as dia19, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 20, valorRol + valorAdicional,0)) as dia20, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 21, valorRol + valorAdicional,0)) as dia21, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 22, valorRol + valorAdicional,0)) as dia22, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 23, valorRol + valorAdicional,0)) as dia23, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 24, valorRol + valorAdicional,0)) as dia24, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 25, valorRol + valorAdicional,0)) as dia25, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 26, valorRol + valorAdicional,0)) as dia26, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 27, valorRol + valorAdicional,0)) as dia27, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 28, valorRol + valorAdicional,0)) as dia28, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 29, valorRol + valorAdicional,0)) as dia29, sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 30, valorRol + valorAdicional ,0))  as dia30 ,sum(if(month(despachopersonal.fchDespacho) = :mes and day(despachopersonal.fchDespacho) = 31 , valorRol + valorAdicional,0))  as dia31  FROM `despachopersonal`, despacho, trabajador, cliente WHERE cliente.idRuc = despacho.idCliente AND trabajador.idTrabajador = despachopersonal.idTrabajador AND despacho.fchDespacho = despachopersonal.fchDespacho AND despacho.correlativo = despachopersonal.correlativo AND year(despachopersonal.fchDespacho) = :anho AND concluido = 'si' group by `idTrabajador`,idCliente");
  $consulta->bindParam(':mes',$mes);
  $consulta->bindParam(':anho',$anho);
	$consulta->execute();
	return $consulta->fetchAll();
}

function infoPorQuincena($db,$mes,$anhio,$quin){
  $whereDia = ($quin == '0')?" AND day(despacho.fchDespacho) <= '15' ":" AND day(despacho.fchDespacho) > '15' ";
  if($quin == '0')
    $linea = "";
  else  
    $linea = ", sum(if(day(dper.fchDespacho) = $quin + 16, valorRol ,0)) as dia16, sum(if(day(dper.fchDespacho) = $quin +16, valorAdicional ,0)) as diaAd16, sum(if(day(dper.fchDespacho) = $quin + 16, 1 ,0)) as Cantdia16 ";

  //$consulta = $db->prepare("SELECT trabajador.`idTrabajador`,concat(apPaterno,' ',apMaterno,' ',nombres) as nombre, tipoTrabajador, categTrabajador, `licenciaCategoria`,`cuenta`, cliente.nombre as nombreCli, despacho.placa ,sum(if(day(dper.fchDespacho) = $quincena + 1, valorRol ,0)) as dia01, sum(if(day(dper.fchDespacho) = $quincena + 1, 1 ,0)) as Cantdia01, sum(if(day(dper.fchDespacho) = $quincena + 2, valorRol ,0)) as dia02, sum(if(day(dper.fchDespacho) = $quincena + 2, 1 ,0)) as Cantdia02, sum(if(day(dper.fchDespacho) = $quincena + 3, valorRol ,0)) as dia03, sum(if(day(dper.fchDespacho) = $quincena + 3, 1 ,0)) as Cantdia03, sum(if(day(dper.fchDespacho) = $quincena + 4, valorRol ,0)) as dia04, sum(if(day(dper.fchDespacho) = $quincena + 4, 1 ,0)) as Cantdia04, sum(if(day(dper.fchDespacho) = $quincena + 5, valorRol ,0)) as dia05, sum(if(day(dper.fchDespacho) = $quincena + 5, 1 ,0)) as Cantdia05, sum(if(day(dper.fchDespacho) = $quincena + 6, valorRol ,0)) as dia06, sum(if(day(dper.fchDespacho) = $quincena + 6, 1 ,0)) as Cantdia06, sum(if(day(dper.fchDespacho) = $quincena + 7, valorRol ,0)) as dia07, sum(if(day(dper.fchDespacho) = $quincena + 7, 1 ,0)) as Cantdia07, sum(if(day(dper.fchDespacho) = $quincena + 8, valorRol ,0)) as dia08, sum(if(day(dper.fchDespacho) = $quincena + 8, 1 ,0)) as Cantdia08, sum(if(day(dper.fchDespacho) = $quincena + 9, valorRol ,0)) as dia09, sum(if(day(dper.fchDespacho) = $quincena + 9, 1 ,0)) as Cantdia09, sum(if(day(dper.fchDespacho) = $quincena + 10, valorRol ,0)) as dia10, sum(if(day(dper.fchDespacho) = $quincena + 10, 1 ,0)) as Cantdia10,sum(if(day(dper.fchDespacho) = $quincena + 11, valorRol ,0)) as dia11, sum(if(day(dper.fchDespacho) = $quincena + 11, 1 ,0)) as Cantdia11,sum(if(day(dper.fchDespacho) = $quincena + 12, valorRol ,0)) as dia12, sum(if(day(dper.fchDespacho) = $quincena + 12, 1 ,0)) as Cantdia12,sum(if(day(dper.fchDespacho) = $quincena + 13, valorRol ,0)) as dia13, sum(if(day(dper.fchDespacho) = $quincena + 13, 1 ,0)) as Cantdia13,sum(if(day(dper.fchDespacho) = $quincena + 14, valorRol ,0)) as dia14, sum(if(day(dper.fchDespacho) = $quincena + 14, 1 ,0)) as Cantdia14,sum(if(day(dper.fchDespacho) = $quincena + 15, valorRol ,0)) as dia15, sum(if(day(dper.fchDespacho) = $quincena + 15, 1 ,0)) as Cantdia15  $linea FROM `despachopersonal` AS dper, despacho, trabajador, cliente WHERE cliente.idRuc = despacho.idCliente AND trabajador.idTrabajador = dper.idTrabajador AND despacho.fchDespacho = dper.fchDespacho AND despacho.correlativo = dper.correlativo $whereDia AND  month(dper.fchDespacho) = :mes AND year(dper.fchDespacho) = :anho AND (tipoRol ='Conductor' OR tipoRol ='Auxiliar' OR tipoRol ='Adicional')  AND concluido = 'si' group by `idTrabajador`,`idCliente`,`cuenta`, `placa` order by apPaterno, idTrabajador ");

  $consulta = $db->prepare("SELECT trabajador.`idTrabajador`,concat(apPaterno,' ',apMaterno,' ',nombres) as nombre, tipoTrabajador, categTrabajador, `licenciaCategoria`,`cuenta`, cliente.nombre as nombreCli, despacho.placa , sum(if(day(dper.fchDespacho) = $quin + 1, valorRol ,0)) as dia01, sum(if(day(dper.fchDespacho) = $quin + 1, valorAdicional ,0)) as diaAd01, sum(if(day(dper.fchDespacho) = $quin + 1, 1 ,0)) as Cantdia01, sum(if(day(dper.fchDespacho) = $quin + 2, valorRol ,0)) as dia02, sum(if(day(dper.fchDespacho) = $quin + 2, valorAdicional ,0)) as diaAd02, sum(if(day(dper.fchDespacho) = $quin + 2, 1 ,0)) as Cantdia02, sum(if(day(dper.fchDespacho) = $quin + 3, valorRol ,0)) as dia03, sum(if(day(dper.fchDespacho) = $quin + 3, valorAdicional ,0)) as diaAd03, sum(if(day(dper.fchDespacho) = $quin + 3, 1 ,0)) as Cantdia03, sum(if(day(dper.fchDespacho) = $quin + 4, valorRol ,0)) as dia04, sum(if(day(dper.fchDespacho) = $quin + 4, valorAdicional ,0)) as diaAd04, sum(if(day(dper.fchDespacho) = $quin + 4, 1 ,0)) as Cantdia04, sum(if(day(dper.fchDespacho) = $quin + 5, valorRol ,0)) as dia05, sum(if(day(dper.fchDespacho) = $quin + 5, valorAdicional ,0)) as diaAd05, sum(if(day(dper.fchDespacho) = $quin + 5, 1 ,0)) as Cantdia05, sum(if(day(dper.fchDespacho) = $quin + 6, valorRol ,0)) as dia06, sum(if(day(dper.fchDespacho) = $quin + 6, valorAdicional ,0)) as diaAd06, sum(if(day(dper.fchDespacho) = $quin + 6, 1 ,0)) as Cantdia06, sum(if(day(dper.fchDespacho) = $quin + 7, valorRol ,0)) as dia07, sum(if(day(dper.fchDespacho) = $quin + 7, valorAdicional ,0)) as diaAd07, sum(if(day(dper.fchDespacho) = $quin + 7, 1 ,0)) as Cantdia07, sum(if(day(dper.fchDespacho) = $quin + 8, valorRol ,0)) as dia08, sum(if(day(dper.fchDespacho) = $quin + 8, valorAdicional ,0)) as diaAd08, sum(if(day(dper.fchDespacho) = $quin + 8, 1 ,0)) as Cantdia08, sum(if(day(dper.fchDespacho) = $quin + 9, valorRol ,0)) as dia09, sum(if(day(dper.fchDespacho) = $quin + 9, valorAdicional ,0)) as diaAd09, sum(if(day(dper.fchDespacho) = $quin + 9, 1 ,0)) as Cantdia09, sum(if(day(dper.fchDespacho) = $quin + 10, valorRol ,0)) as dia10, sum(if(day(dper.fchDespacho) = $quin +10, valorAdicional ,0)) as diaAd10,sum(if(day(dper.fchDespacho) = $quin + 10, 1,0)) as Cantdia10,sum(if(day(dper.fchDespacho) = $quin + 11, valorRol ,0)) as dia11, sum(if(day(dper.fchDespacho) = $quin +11, valorAdicional ,0)) as diaAd11,sum(if(day(dper.fchDespacho) = $quin + 11, 1,0)) as Cantdia11,sum(if(day(dper.fchDespacho) = $quin + 12, valorRol ,0)) as dia12, sum(if(day(dper.fchDespacho) = $quin +12, valorAdicional ,0)) as diaAd12,sum(if(day(dper.fchDespacho) = $quin + 12, 1,0)) as Cantdia12,sum(if(day(dper.fchDespacho) = $quin + 13, valorRol ,0)) as dia13, sum(if(day(dper.fchDespacho) = $quin +13, valorAdicional ,0)) as diaAd13,sum(if(day(dper.fchDespacho) = $quin + 13, 1,0)) as Cantdia13,sum(if(day(dper.fchDespacho) = $quin + 14, valorRol ,0)) as dia14, sum(if(day(dper.fchDespacho) = $quin +14, valorAdicional ,0)) as diaAd14,sum(if(day(dper.fchDespacho) = $quin + 14, 1,0)) as Cantdia14,sum(if(day(dper.fchDespacho) = $quin + 15, valorRol ,0)) as dia15, sum(if(day(dper.fchDespacho) = $quin +15, valorAdicional ,0)) as diaAd15,sum(if(day(dper.fchDespacho) = $quin + 15, 1,0)) as Cantdia15  $linea FROM `despachopersonal` AS dper, despacho, trabajador, cliente WHERE cliente.idRuc = despacho.idCliente AND trabajador.idTrabajador = dper.idTrabajador AND despacho.fchDespacho = dper.fchDespacho AND despacho.correlativo = dper.correlativo $whereDia AND  month(dper.fchDespacho) = :mes AND year(dper.fchDespacho) = :anho AND (tipoRol ='Conductor' OR tipoRol ='Auxiliar' OR tipoRol ='Adicional')  AND concluido = 'si' group by `idTrabajador`,`idCliente`,`cuenta`, `placa` order by apPaterno, idTrabajador ");

  $consulta->bindParam(':mes',$mes);
  $consulta->bindParam(':anho',$anhio);
	$consulta->execute();
	return $consulta->fetchAll();
}

function verificaQuincena($db,$anhio,$quincena){
  $mes = strtok($quincena,"-");
  $aux = strtok("-");
  $dia = ($aux == '1')?'14':'28';
  $fchQuincena = $anhio."-".$mes."-".$dia;
  
  $consulta = $db->prepare("SELECT `quincena`, `estadoQuincena`, `fchCreacion`, `usuario`, `nombreProceso`  FROM `quincena` WHERE `quincena` = :quincena AND `estadoQuincena` = 'Cerrado' ");
  $consulta->bindParam(':quincena',$fchQuincena);
  $consulta->execute();
  return $consulta->fetchAll();
}

function eliminaHorasExtraCero($db){
  $elimina = $db->prepare("DELETE FROM `prestamo` WHERE monto = 0 AND `tipoItem` LIKE 'hraextra' AND `fchCreacion` >= '2012-10-15' AND entregado = 'No'");     
  $elimina->execute();
};

function buscarHuerfanosPrestamoDetalle($db){
  $consulta = $db->prepare("SELECT `prestamodetalle`.`monto` , `prestamodetalle`.`idTrabajador`, `prestamodetalle`.`descripcion`, `prestamodetalle`.`tipoItem` , `prestamodetalle`.`fchCreacion`  FROM `prestamodetalle` LEFT JOIN prestamo ON `prestamodetalle`.`idTrabajador` = `prestamo`.`idTrabajador` AND `prestamodetalle`.`monto` = `prestamo`.`monto` AND `prestamodetalle`.`descripcion` = `prestamo`.`descripcion` AND `prestamodetalle`.`tipoItem` = `prestamo`.`tipoItem` AND `prestamodetalle`.`fchCreacion` = `prestamo`.`fchCreacion` WHERE `prestamo`.`monto` IS NULL AND `prestamodetalle`.`fchCreacion` >= '2012-11-01'  AND `prestamodetalle`.`pagado` != 'Si' GROUP BY `prestamodetalle`.`monto` ,  `prestamodetalle`.`idTrabajador` , `prestamodetalle`.`descripcion` , `prestamodetalle`.`tipoItem` , `prestamodetalle`.`fchCreacion`");
  $consulta->execute();
  return $consulta->fetchAll();
}

function insertaEntidadPension($db,$nombre,$comisionFlujo,$comisionMixta,$primaSeg,$porcentObligat){
  $usuario = $_SESSION["usuario"];
  $inserta = $db->prepare("INSERT INTO `pension` (`nombre`, `comisionFlujo`, `comisionMixta`, `primaSeg`, `porcentObligat`, `usuario`, `fchCreacion`) VALUES (:nombre, :comisionFlujo, :comisionMixta, :primaSeg, :porcentObligat, :usuario, curdate());");

  $inserta->bindParam(':nombre',$nombre);
  $inserta->bindParam(':comisionFlujo',$comisionFlujo);
  $inserta->bindParam(':comisionMixta',$comisionMixta);
  $inserta->bindParam(':primaSeg',$primaSeg);
  $inserta->bindParam(':porcentObligat',$porcentObligat);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();
  return $inserta->rowCount();
}

function modificaEntidadPension($db,$nombre,$comisionFlujo,$comisionMixta,$primaSeg,$porcentObligat){
  $usuario = $_SESSION["usuario"];
  $actualiza = $db->prepare("UPDATE `pension` SET `comisionFlujo` = :comisionFlujo, `comisionMixta` = :comisionMixta, `primaSeg` = :primaSeg, `porcentObligat` = :porcentObligat,`fchUltimoCambio` = curdate(), `usuarioUltimoCambio` = :usuario WHERE `nombre` = :nombre");
  $actualiza->bindParam(':nombre',$nombre);
  $actualiza->bindParam(':comisionFlujo',$comisionFlujo);
  $actualiza->bindParam(':comisionMixta',$comisionMixta);
  $actualiza->bindParam(':primaSeg',$primaSeg);
  $actualiza->bindParam(':porcentObligat',$porcentObligat);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();
  return $actualiza->rowCount();
}

function buscarPlacasActivasSinUsar($db,$atraso = 2){
  $consulta = $db->prepare("SELECT placa, max(fchDespacho) AS fchDespacho, DATEDIFF( curdate(), max(fchDespacho)) AS ultimaVez, rznSocial, `vehiculodueno`.nombreCompleto FROM `despacho`, vehiculo LEFT JOIN vehiculodueno ON `vehiculo`.rznSocial = `vehiculodueno`.documento  WHERE `despacho`.placa = `vehiculo`.idPlaca AND `vehiculo`.estado = 'Activo' AND placa is not null AND `fchDespacho` >= DATE_SUB(CURDATE(), INTERVAL 30 Day) GROUP BY placa HAVING ultimaVez > :atraso  ORDER BY `ultimaVez`  DESC");
  $consulta->bindParam(':atraso',$atraso);
  $consulta->execute();
	return $consulta->fetchAll();
}

function  buscarUsuPermisos($db, $idUsuario){
  $consulta = $db->prepare("SELECT `id`, `idUsuario`, `nombPermiso`, `descripPermiso`, `nivel`, `creacUsuario`, `creacFch`, `editaUsuario` FROM `usupermisos` WHERE idUsuario = :idUsuario");
  $consulta->bindParam(':idUsuario',$idUsuario);
  $consulta->execute();
  $aux = $consulta->fetchAll();
  $arrAux = array();
  foreach ($aux as $key => $permiso) {
    $arrAux[$permiso['nombPermiso']] = $permiso['nivel'];
  }
  return $arrAux;
}

function buscarTrabajActivosSinPredet($db){
  $consulta = $db->prepare("SELECT `trab`.idTrabajador, `trab`.categTrabajador, `trab`.estadoTrabajador, concat(apPaterno, ' ', apMaterno, ', ', nombres ) AS nombCompleto, `trabdoc`.estado, `trab`.fchCreacion, `trab`.usuario FROM `trabajador` AS trab LEFT JOIN `trabajdocumentos` AS trabdoc ON `trab`.idTrabajador = `trabdoc`.idTrabajador AND `trabdoc`.estado = 'Predeterminado' WHERE `estadoTrabajador` IN ('Activo','Latente') AND `trabdoc`.estado IS NULL");
  //$consulta->bindParam(':limite',$advertencias);
  $consulta->execute();
  return $consulta->fetchAll();


}



?>
