<?php
global $servidor, $bd, $usuario, $contrasenia;
$db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);

function buscarTodosLosVehiculos($db){
	$consulta = $db->prepare("SELECT * FROM vehiculo");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarTodosLosVehiculosActivos($db,$bloquear = 'No'){
  $whereBloquear = ($bloquear == 'Si')?" AND ( ( `dimInteriorLargo` * `dimInteriorAncho` * `dimInteriorAlto` = 0 AND datediff( curdate( ) , `fchCreacion` ) <15) OR `dimInteriorLargo` * `dimInteriorAncho` * `dimInteriorAlto` > 0)  ":""; 
  $consulta = $db->prepare("SELECT * FROM vehiculo WHERE `estado` = 'Activo' $whereBloquear ");
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarVehiculo($db,$idPlaca){
	//$consulta = $db->prepare("SELECT `vehiculo`.`idPlaca`, `nroVehiculo`, `estado`, `propietario`, `rznSocial`, `nroMotor`, `nroSerie`, `dimLargo`, `dimAlto`, `dimAncho`, `dimInteriorLargo`, `dimInteriorAncho`, `dimInteriorAlto`, `m3Facturable`, `capCombustible`, `rendimiento`, `eficCombustible`, `combustibleFrec`, `checkAlertas`, `marca`, `modelo`, `anioFabricacion`, `color`, `carroceria`, `clase`, `pesoSeco`, `pesoUtil`, `pesoBruto`, `pesoUtilReal`, `descripcion`, `kmUltimaMedicion`, `costoDia`,`grupoRendimiento` , base, cilindros, cc ,hp, nroRuedas, llantas, imgVehiculo, pulg, puertaCarr, terceroConPersonalMoy, modoAcuerdo, considerarPropio, if(`vehiculodueno`.nombreCompleto is null, rznSocial,`vehiculodueno`.nombreCompleto) AS nombreCompleto FROM `vehiculo` LEFT JOIN `vehiculotercero` ON `vehiculo`.`idPlaca` = `vehiculotercero`.`idPlaca` LEFT JOIN vehiculodueno ON `vehiculo`.rznSocial = vehiculodueno.`documento` WHERE  `vehiculo`.`idPlaca` =  :idPlaca");
  $consulta = $db->prepare("SELECT `vehiculo`.`idPlaca`, `nroVehiculo`, `estado`, `propietario`, `rznSocial`, `nroMotor`, `nroSerie`, `dimLargo`, `dimAlto`, `dimAncho`, `dimInteriorLargo`, `dimInteriorAncho`, `dimInteriorAlto`, `m3Facturable`, `capCombustible`, `rendimiento`, `eficCombustible`, `combustibleFrec`, `checkAlertas`, `marca`, `modelo`, `anioFabricacion`, `color`, `carroceria`, `clase`, `pesoSeco`, `pesoUtil`, `pesoBruto`, `pesoUtilReal`, `descripcion`, `kmUltimaMedicion`, `costoDia`,`grupoRendimiento` , base, cilindros, cc ,hp, nroRuedas, llantas, imgVehiculo, pulg, puertaCarr, terceroConPersonalMoy, considerarPropio, idEficSolesKm, if(`vehiculodueno`.nombreCompleto is null, rznSocial,`vehiculodueno`.nombreCompleto) AS nombreCompleto, `vehiculotercero`.modoAcuerdo FROM `vehiculo` LEFT JOIN `vehiculotercero` ON `vehiculo`.`idPlaca` = `vehiculotercero`.`idPlaca` LEFT JOIN vehiculodueno ON `vehiculo`.rznSocial = vehiculodueno.`documento` WHERE  `vehiculo`.`idPlaca` =  :idPlaca");
	$consulta->bindParam(':idPlaca',$idPlaca);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarFotoMercaderia1($db,$idmerca){
  //$consulta = $db->prepare("SELECT SUBSTRING_INDEX(`foto`, '/', -2) AS fotito FROM `incidencias_mercaderia` WHERE `idmerca` = :idmerca");
  $consulta = $db->prepare("SELECT `foto1` FROM `incidencias_mercaderia` WHERE `idmerca` = :idmerca");
	$consulta->bindParam(':idmerca',$idmerca);
	$consulta->execute();
	return $consulta->fetchColumn();
}
function buscarFotoMercaderia2($db,$idmerca){
  //$consulta = $db->prepare("SELECT SUBSTRING_INDEX(`foto`, '/', -2) AS fotito FROM `incidencias_mercaderia` WHERE `idmerca` = :idmerca");
  $consulta = $db->prepare("SELECT `foto2` FROM `incidencias_mercaderia` WHERE `idmerca` = :idmerca");
	$consulta->bindParam(':idmerca',$idmerca);
	$consulta->execute();
	return $consulta->fetchColumn();
}
function buscarFotoMercaderia3($db,$idmerca){
  //$consulta = $db->prepare("SELECT SUBSTRING_INDEX(`foto`, '/', -2) AS fotito FROM `incidencias_mercaderia` WHERE `idmerca` = :idmerca");
  $consulta = $db->prepare("SELECT `foto3` FROM `incidencias_mercaderia` WHERE `idmerca` = :idmerca");
	$consulta->bindParam(':idmerca',$idmerca);
	$consulta->execute();
	return $consulta->fetchColumn();
}

function buscarFotoChoque1($db,$idincidencia){
  //$consulta = $db->prepare("SELECT SUBSTRING_INDEX(`foto`, '/', -2) AS fotito FROM `incidencias_mercaderia` WHERE `idmerca` = :idmerca");
  $consulta = $db->prepare("SELECT `foto1` FROM `incidencias_vehiculo` WHERE `idincidencia` = :idincidencia");
	$consulta->bindParam(':idincidencia',$idincidencia);
	$consulta->execute();
	return $consulta->fetchColumn();
}
function buscarFotoChoque2($db,$idincidencia){
  //$consulta = $db->prepare("SELECT SUBSTRING_INDEX(`foto`, '/', -2) AS fotito FROM `incidencias_mercaderia` WHERE `idmerca` = :idmerca");
  $consulta = $db->prepare("SELECT `foto2` FROM `incidencias_vehiculo` WHERE `idincidencia` = :idincidencia");
	$consulta->bindParam(':idincidencia',$idincidencia);
	$consulta->execute();
	return $consulta->fetchColumn();
}
function buscarFotoChoque3($db,$idincidencia){
  //$consulta = $db->prepare("SELECT SUBSTRING_INDEX(`foto`, '/', -2) AS fotito FROM `incidencias_mercaderia` WHERE `idmerca` = :idmerca");
  $consulta = $db->prepare("SELECT `foto3` FROM `incidencias_vehiculo` WHERE `idincidencia` = :idincidencia");
	$consulta->bindParam(':idincidencia',$idincidencia);
	$consulta->execute();
	return $consulta->fetchColumn();
}


function buscarFotoTelefono($db,$idtelefono){
  //$consulta = $db->prepare("SELECT SUBSTRING_INDEX(`foto`, '/', -2) AS fotito FROM `incidencias_mercaderia` WHERE `idmerca` = :idmerca");
  $consulta = $db->prepare("SELECT `foto` FROM `incidencias_telefono` WHERE `idtelefono` = :idtelefono");
	$consulta->bindParam(':idtelefono',$idtelefono);
	$consulta->execute();
	return $consulta->fetchColumn();
}

function buscarFotoPapeleta($db,$idinfraccion){
  //$consulta = $db->prepare("SELECT SUBSTRING_INDEX(`foto`, '/', -2) AS fotito FROM `incidencias_mercaderia` WHERE `idmerca` = :idmerca");
  $consulta = $db->prepare("SELECT `foto` FROM `incidencias_infraccion` WHERE `idinfraccion` = :idinfraccion");
	$consulta->bindParam(':idinfraccion',$idinfraccion);
	$consulta->execute();
	return $consulta->fetchColumn();
}

function buscarFotoPeaje($db, $idpeaje)
{
  //$consulta = $db->prepare("SELECT SUBSTRING_INDEX(`foto`, '/', -2) AS fotito FROM `incidencias_mercaderia` WHERE `idmerca` = :idmerca");
  $consulta = $db->prepare("SELECT `foto` FROM `peajes` WHERE `idpeaje` = :idpeaje");
  $consulta->bindParam(':idpeaje', $idpeaje);
  $consulta->execute();
  return $consulta->fetchColumn();
}

function buscarFotoAbastecimiento($db,$idabastecimiento,$foto){
  //$consulta = $db->prepare("SELECT SUBSTRING_INDEX(`foto`, '/', -2) AS fotito FROM `incidencias_mercaderia` WHERE `idmerca` = :idmerca");
  if($foto == "foto1"){
    $consulta = $db->prepare("SELECT `foto1` FROM `incidencias_abastecimiento` WHERE `idabastecimiento` = :idabastecimiento");
  }
  else{
    $consulta = $db->prepare("SELECT `foto2` FROM `incidencias_abastecimiento` WHERE `idabastecimiento` = :idabastecimiento");
  }
	$consulta->bindParam(':idabastecimiento',$idabastecimiento);
	$consulta->execute();
	return $consulta->fetchColumn();
}

function buscarGruposRendimiento($db){
	$consulta = $db->prepare("SELECT `grupoRendimiento` FROM `vehiculogruporendimiento`");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarVehiculos($db,$query,$limite = null){
  if($limite)
    $whereLimite = "LIMIT $limite";
  else
    $whereLimite = '';  
	$consulta = $db->prepare("SELECT `idPlaca`, `nroVehiculo`, `estado`, `propietario`, `rznSocial`, `nroMotor`, `nroSerie`, `dimLargo`, `dimAlto`, `dimAncho`, `dimInteriorLargo`, `dimInteriorAncho`, `dimInteriorAlto`, `m3Facturable`,`capCombustible`, `rendimiento`, `grupoRendimiento`, `eficCombustible`, `combustibleFrec`, `checkAlertas`, `marca`, `modelo`, `anioFabricacion`, `color`, `carroceria`, `puertaCarr`, `clase`, `base`, `cilindros`, `cc`, `hp`, `nroRuedas`, `llantas`, `imgVehiculo`, `pulg`, `pesoSeco`, `pesoUtil`, `pesoBruto`, `pesoUtilReal`, `descripcion`, `kmUltimaMedicion`,  vehiculo.`usuario`,  vehiculo.`fchCreacion`, `usuarioUltimoCambio`, `fchUltimoCambio`, `documento`, `nombreCompleto`, `nroTelefono`, `eMail`, `bancoNombre`, `bancoCuentaNro`, `bancoCuentaTipo`, `bancoCuentaMoneda`, `cuentaDetraccion` FROM vehiculo left join vehiculodueno ON  vehiculo.rznSocial = vehiculodueno.documento $query $whereLimite");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarVehiculoLicencias($db,$idPlaca)  {
  $consulta = $db->prepare("SELECT * FROM vehiculolicencias WHERE `idPlaca` = :idPlaca AND `estado` = 'activo' ");
	$consulta->bindParam(':idPlaca',$idPlaca);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarLicenciasVehiculosDefinicion($db, $estadoLic = NULL){
  $where = "";
  if ($estadoLic != NULL) $where = " AND estadoLic = :estadoLic";
  $consulta = $db->prepare("SELECT `nombLicencia`, `plazo`, `descripLicencia` FROM `vehiculolicenciasdefinir` WHERE 1 $where ");

  if ($estadoLic != NULL) $consulta->bindParam(':estadoLic',$estadoLic);
	$consulta->execute();
	return $consulta->fetchAll();
}

function verificarVehiculoOcupado($db,$idPlaca,$fchDespacho){
	$consulta = $db->prepare("SELECT count(*) FROM `despacho` WHERE `concluido` = 'No' AND `fchDespacho` = :fchDespacho And `placa` = :idPlaca ");	
	$consulta->bindParam(':fchDespacho',$fchDespacho);
	$consulta->bindParam(':idPlaca',$idPlaca);	
	$consulta->execute();
	return $consulta->fetchColumn();
}

function buscarDatosParaCombustible($db,$idPlaca,$tipoRol,$fecha){
  //$fecha = (!isset($fecha))?' curdate() ':'';
  $consulta = $db->prepare("SELECT `despacho`.`placa`,`despacho`.`cuenta`,`despacho`.`concluido`, `despacho`.`fchDespacho`, `despacho`.`correlativo`,  `despachopersonal`.`idTrabajador`, concat(`trabajador`.nombres,' ',`trabajador`.apPaterno,' ',`trabajador`.apMaterno) as nombreCompleto  FROM `despacho`, `despachopersonal`, `trabajador` where `despacho`.fchDespacho = `despachopersonal`.fchDespacho AND `despacho`.correlativo = `despachopersonal`.correlativo AND `despachopersonal`.tipoRol = :tipoRol AND `despachopersonal`.idTrabajador = `trabajador`.idTrabajador   AND  `despachopersonal`.`fchDespacho` = '$fecha' AND  `despacho`.`placa` = :idPlaca order by `despacho`.fchDespacho, `despacho`.correlativo desc limit 1");
	$consulta->bindParam(':idPlaca',$idPlaca);
	$consulta->bindParam(':tipoRol',$tipoRol);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarDatosAbastecimientoAnterior($db,$idPlaca){
  $consulta = $db->prepare("SELECT  `fchCreacion`, `hraCreacion`, `tiempo`, `idPlaca`, `galones` , `correlativo`,`kmActual`,`grifo`, `grifero` FROM `combustible` WHERE `idPlaca` = :idPlaca order by tiempo desc limit 1");
	$consulta->bindParam(':idPlaca',$idPlaca);
	$consulta->execute();
	return $consulta->fetchAll();  
}



function eliminaAbastecimientoAnterior($db,$idPlaca){
  $elimina = $db->prepare("DELETE FROM `combustible` WHERE `idPlaca` =:idPlaca order by tiempo desc limit 1");
  $elimina->bindParam(':idPlaca',$idPlaca);
	$elimina->execute();

};



function buscarAtencionesAbastecimientoAnterior($db,$idPlaca,$tiempo){
  $consulta = $db->prepare("SELECT  `despacho`.`fchDespacho`,  `despacho`.`correlativo`, `idCliente`, cliente.nombre, `cuenta`,`hraInicio`, `hraFin`,`kmInicio`, `kmFin`, `kmFin` - `kmInicio` as recorrido ,  concat(`trabajador`.nombres, ' ', `trabajador`.apPaterno ) as conductor, `concluido` FROM `despacho`, `despachopersonal`, `cliente`,`trabajador` WHERE `despacho`.`fchDespacho` = `despachopersonal`.`fchDespacho` AND `despacho`.`correlativo` = `despachopersonal`.`correlativo` AND `despachopersonal`.`tipoRol` = 'Conductor' AND `despachopersonal`.`idTrabajador` = `trabajador`.`idTrabajador` AND `despacho`.idCliente = `cliente`.idRuc   AND `placa` = :idPlaca AND concat(`despacho`.fchDespacho,' ',hraInicio) >= '$tiempo'");
  
	$consulta->bindParam(':idPlaca',$idPlaca);
	$consulta->execute();
	return $consulta->fetchAll();  
}  

function buscarAbastecimiento($db,$fchIni,$fchFin,$grifo,$nroVale,$placa,$servicio){
  //$whereGrifo = ($grifo=="")?"":" AND `grifo` = '$grifo'  ";  
  $whereGrifo = ($grifo=="")?"":" AND `grifo` = :grifo  ";
  $whereNroVale = ($nroVale=="")?"":" AND `nroVale` = :nroVale  ";
  $wherePlaca = ($placa=="")?"":" AND `idPlaca` = :placa  ";
  $whereServicio = ($servicio=="")?"":" AND `servicio` = :servicio  ";
  //$whereChofer = ($chofer =="") ?"":" AND `chofer` = :chofer "; Guarda nombres no ids
  //if ($grifo == '') $whereGrifo = " ";
  //else  $whereGrifo = " AND `grifo` = '$grifo'  ";  
  //echo $servicio;
  //$consulta = $db->prepare("SELECT `fchCreacion`, `grifo`,`nroVale`, `idPlaca`,`kmActual` - `recorrido` AS `kmAnterior` ,`kmActual`, `precioGalon`, `galones`, `precioGalon`*`galones` AS `total`, `recorrido`, `recorrido`/`galones` AS `rendimiento`, `hraCreacion`, `servicio` ,`chofer`, `grifero`, `observacion`  FROM `combustible` WHERE (`fchCreacion` >= :fchIni AND `fchCreacion` <= :fchFin ) $whereGrifo $whereNroVale $wherePlaca $whereServicio ");

  $consulta = $db->prepare("SELECT `fchCreacion`, `grifo`,`nroVale`, `idPlaca`,`kmActual` - `recorrido` AS `kmAnterior` ,`kmActual`, `precioGalon`, `galones`, `total`, `recorrido`, `recorrido`/`galones` AS `rendimiento`, `hraCreacion`, `servicio` ,`chofer`, `grifero`, `observacion`  FROM `combustible` WHERE (`fchCreacion` >= :fchIni AND `fchCreacion` <= :fchFin ) $whereGrifo $whereNroVale $wherePlaca $whereServicio ");
	$consulta->bindParam(':fchIni',$fchIni);
	$consulta->bindParam(':fchFin',$fchFin);
	if ($whereGrifo !='')
    $consulta->bindParam(':grifo',$grifo);  
	if ($whereNroVale !='')
    $consulta->bindParam(':nroVale',$nroVale);
  if ($wherePlaca !='')
    $consulta->bindParam(':placa',$placa);    
  if ($whereServicio !='')
    $consulta->bindParam(':servicio',$servicio);
  //if ($whereChofer !='')
//    $consulta->bindParam(':chofer',$chofer);  
    
	$consulta->execute();
	return $consulta->fetchAll();
};


function calcularRendimientoCombustiblePeriodo($db,$fchIni,$fchFin){
  $consulta = $db->prepare("SELECT `tiempo`, `combustible`.`idPlaca`, count(*) as `tanqueadas`, sum(`galones`) as `consumoGalones`, sum(`precioGalon`*`galones`) as `gastoSoles`, sum(`recorrido`) as `recorridoTotal`, sum(`recorrido`)/ sum(`galones`) as `rendActualOld` , `rendimiento`,  sum(`recorrido`)/ sum(`galones`) - `rendimiento` as `diferenciaOld`, avg(`recorrido`/`galones`) as `rendActual`, avg(`recorrido`/`galones`) - `rendimiento` as `diferencia`   FROM `combustible`, `vehiculo` WHERE  `combustible`.idPlaca = `vehiculo`.idPlaca AND  (`combustible`.`fchCreacion` >= :fchIni AND `combustible`.`fchCreacion` <= :fchFin) group by `idPlaca` order by `combustible`.`fchCreacion`,`combustible`.`idPlaca`");
	$consulta->bindParam(':fchIni',$fchIni);
	$consulta->bindParam(':fchFin',$fchFin);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarDuenos($db){
  $consulta = $db->prepare("SELECT `documento`, `nombreCompleto` FROM `vehiculodueno` ORDER BY `nombreCompleto` ");
  $consulta->execute();
	return $consulta->fetchAll();
}

function  buscarCombustibleTercero($db,$nroDocTercero){
  $consulta = $db->prepare("SELECT `fchCreacion`, `hraCreacion`, `tiempo`, `combustible`.`idPlaca`,`combustible`.`precioGalon`, `galones` , `correlativo`,`kmActual` FROM `combustible`, `vehiculotercero` WHERE `combustible`.`idPlaca` = `vehiculotercero`.`idPlaca`  AND ( `pagado` is null or `pagado` != 'Si' ) AND `vehiculotercero`.`documento` = :docTercero order by tiempo desc");
  $consulta->bindParam(':docTercero',$nroDocTercero);
  $consulta->execute();
	return $consulta->fetchAll();  
}

function buscarTipoAlerta($db,$tipoUso){
  $consulta = $db->prepare("SELECT `idAlerta`,  `descripcion`, `plazo`, `unidad` FROM `alertastipo` WHERE `tipoUso` = :tipoUso");
  $consulta->bindParam(':tipoUso',$tipoUso);
  $consulta->execute();
	return $consulta->fetchAll();

};


function buscarVehiculoMantenimientos($db,$idPlaca){
  $consulta = $db->prepare("SELECT `idPlaca`, `marcaInicio`, `plazo`, `unidad`, `alerta`, `marcaFin`, `observacion` FROM `vehiculomantenimiento` WHERE `idPlaca` = :idPlaca AND `estado` = 'Activo' ");  
	$consulta->bindParam(':idPlaca',$idPlaca);
	$consulta->execute();
	return $consulta->fetchAll();  
};

function buscarHistoricoalertas($db,$placa,$alerta,$estado,$fchCreacion,$limite){

  $limite = str_replace(";","",$limite);
  $limite = str_replace("'","",$limite);

   if($placa)
    $wherePlaca = " AND `idPlaca` = :placa ";
  else
    $wherePlaca = '';
  
  if($alerta)
    $whereAlerta = " AND `alerta` = :alerta ";
  else
    $whereAlerta = '';
	
  if($estado)
    $whereEstado = " AND `estado` = :estado ";
  else
    $whereEstado = '';
    
  if($fchCreacion)
    $wherefchCreacion = " AND `fchCreacion` = :fchCreacion ";
  else
    $wherefchCreacion = '';

 if($limite)
    $whereLimite = "LIMIT $limite";
  else
    $whereLimite = '';	
  
  $consulta = $db->prepare("SELECT `idPlaca`,`plazo`,`unidad`,`alerta`,`marcaFin`,`estado` ,`observacion`,`usuario`,`fchCreacion` FROM `vehiculomantenimiento` WHERE 1 $wherePlaca  $whereAlerta  $whereEstado  $wherefchCreacion  $whereLimite ");  
	if ($placa) $consulta->bindParam(':placa',$placa);
	if ($alerta) $consulta->bindParam(':alerta',$alerta);
	if ($estado) $consulta->bindParam(':estado',$estado);
	if ($fchCreacion) $consulta->bindParam(':fchCreacion',$fchCreacion);
	//if ($limite) $consulta->bindParam(':limite',$limite);
  $consulta->execute();
	return $consulta->fetchAll();  
};

function actualizaVehiculoLicencias($db,$idPlaca,$fchInicio,$nombre,$usuario){
  $actualiza = $db->prepare("UPDATE `vehiculolicencias` SET `estado` = 'Inactivo', `fchUltimoCambio` = curdate(), `usuarioUltimoCambio` = '$usuario'  WHERE `idPlaca` = '$idPlaca' AND `fchInicio` = '$fchInicio' AND `nombre` = '$nombre' ");
  $actualiza->execute();
  return 1;
}

function actualizaVehiculoMantenimiento($db,$idPlaca,$alerta,$marcaFin,$fchAtendido,$aceiteMotor,$aceiteCaja,$aceiteCorona,$filtroPetroleo,$filtroAceite,$filtroAire,$filtroSeparador,$filtroAceiteTr,$cambiosOtros,$atencionMarcaKm,$personaResponsable,$usuario,$costoTotal = null,$idProveedor = null,$tipoDoc = null,$nroDoc = null){
  //echo "$idPlaca  $alerta   $marcaFin  $marcaFin,  $fchAtendido,  $aceiteMotor,  $aceiteCaja,  $aceiteCorona, $filtroPetroleo, $filtroAceite, $filtroAire, $filtroSeparador, $filtroAceiteTr, $cambiosOtros, $atencionMarcaKm, $personaResponsable, $usuario ";
  //Busco última marca de atención
  //******************************
  $consulta = $db->prepare("SELECT `marcaAtendido` FROM `vehiculomantenimiento` WHERE `idPlaca` LIKE :idPlaca AND `alerta` = 'aceite' ORDER BY `fchAtendido` DESC limit 1");
  $consulta->bindParam(':idPlaca',$idPlaca);
  $consulta->execute();
  $rst = $consulta->fetchAll();
  foreach($rst as $item) {
    $marcaAtendido = $item['marcaAtendido'];
  }
  $recorrido = $atencionMarcaKm - $marcaAtendido ;
  //Fin de busco última marca de atención
  //*************************************

  $actualiza = $db->prepare("UPDATE `vehiculomantenimiento` SET `estado` = 'Inactivo', `fchAtendido` = :fchAtendido, `recorrido` = :recorrido, `cambioAceiteMotor` = :aceiteMotor, `cambioAceiteCaja` = :aceiteCaja, `cambioAceiteCorona` = :aceiteCorona, `cambioFiltroPetroleo` = :filtroPetroleo, `cambioFiltroAceite` = :filtroAceite, `cambioFiltroAire` = :filtroAire, `cambioFiltroSeparador` = :filtroSeparador, `cambioFiltroAceiteTr` = :filtroAceiteTr , `cambiosOtros` = :cambiosOtros, `marcaAtendido` = :marcaAtendido, `personaResponsable` = :personaResponsable , `fchUltimoCambio` = curdate(), `usuarioUltimoCambio` = :usuario,   costoTotal = :costoTotal, idProveedor = :idProveedor, tipoDoc  = :tipoDoc , nroDoc  = :nroDoc WHERE `idPlaca` = :idPlaca AND `alerta` = :alerta AND `marcaFin` = :marcaFin ");
  $actualiza->bindParam(':idPlaca',$idPlaca);
  $actualiza->bindParam(':alerta',$alerta);
  $actualiza->bindParam(':marcaFin',$marcaFin);
  $actualiza->bindParam(':fchAtendido',$fchAtendido);    //
  $actualiza->bindParam(':recorrido',$recorrido);    //
  $actualiza->bindParam(':aceiteMotor',$aceiteMotor);    //
  $actualiza->bindParam(':aceiteCaja',$aceiteCaja);      //
  $actualiza->bindParam(':aceiteCorona',$aceiteCorona);  //
  $actualiza->bindParam(':filtroPetroleo',$filtroPetroleo); 
  $actualiza->bindParam(':filtroAceite',$filtroAceite);
  $actualiza->bindParam(':filtroAire',$filtroAire);
  $actualiza->bindParam(':filtroSeparador',$filtroSeparador);
  $actualiza->bindParam(':filtroAceiteTr',$filtroAceiteTr);
  $actualiza->bindParam(':cambiosOtros',$cambiosOtros);
  $actualiza->bindParam(':marcaAtendido',$atencionMarcaKm);
  $actualiza->bindParam(':personaResponsable',$personaResponsable);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->bindParam(':costoTotal',$costoTotal);
  $actualiza->bindParam(':idProveedor',$idProveedor);
  $actualiza->bindParam(':tipoDoc',$tipoDoc);
  $actualiza->bindParam(':nroDoc',$nroDoc);  
  $actualiza->execute();
};

function buscarCambiosAceite($db,$fchIni,$fchFin,$idPlaca){
  //echo "placa $idPlaca fchIni $fchIni  fchfin $fchFin";
  if($idPlaca ==''){
    $consulta = $db->prepare("SELECT `idPlaca`, `marcaInicio`, `marcaFin`, `marcaAtendido`, `fchAtendido`, `recorrido`, `plazo`, `unidad`, `alerta`, `marcaFin`, `estado`, `observacion`, `usuario`, `fchCreacion`, `fchUltimoCambio` FROM `vehiculomantenimiento` WHERE `alerta` LIKE 'Aceite' AND (`fchCreacion` >= :fchCreacionIni AND `fchCreacion` <= :fchCreacionFin) ");
  } else {
    $consulta = $db->prepare("SELECT `idPlaca`, `marcaInicio`, `marcaFin`, `marcaAtendido`, `fchAtendido`, `recorrido`, `plazo`, `unidad`, `alerta`, `marcaFin`, `estado`, `observacion`, `usuario`, `fchCreacion`, `fchUltimoCambio` FROM `vehiculomantenimiento` WHERE `alerta` LIKE 'Aceite' AND (`fchCreacion` >= :fchCreacionIni AND `fchCreacion` <= :fchCreacionFin) AND `idPlaca` = :placa ");
    $consulta->bindParam(':placa',$idPlaca);    
  }
    
  //$consulta->bindParam(':idPlaca',$idPlaca);
  $consulta->bindParam(':fchCreacionIni',$fchIni);
  $consulta->bindParam(':fchCreacionFin',$fchFin);
	$consulta->execute();
	return $consulta->fetchAll();

}

function  buscarAbastecPeriodos($db,$fchIni,$fchFin,$placa){
  $consulta = "SELECT `nroVale`, `combustible`.`fchCreacion`, `chofer`, `combustible`.`idPlaca`, `servicio`, `kmActual`, `galones`, `precioGalon`, `recorrido`, `recorrido`/`galones` AS `rendActual`, `rendimiento`, `grifo`, `grifero`, `observacion`, `hraCreacion`, `correlativo`, `auxiliar`, `tiempo`, `marcaCombustible`, `vehiculo`.`rznSocial`, `combustible`.`usuario`, `vehiculodueno`.`nombreCompleto` ,  `vehiculo`.`grupoRendimiento`     FROM `combustible`, `vehiculo` LEFT JOIN  `vehiculodueno` ON  `vehiculo`.`rznSocial` = `vehiculodueno`.`documento` WHERE `combustible`.`idPlaca` =  `vehiculo`.`idPlaca`  AND ";

  if ($placa == NULL){
    $consulta = $db->prepare("$consulta (`combustible`.`fchCreacion` >= :fchIni AND `combustible`.`fchCreacion` <= :fchFin) ORDER BY `combustible`.`idPlaca`, `combustible`.`fchCreacion`");
  } else {
    $consulta = $db->prepare("$consulta (`combustible`.`fchCreacion` >= :fchIni AND `combustible`.`fchCreacion` <= :fchFin) AND `combustible`.`idPlaca` = :placa ORDER BY `combustible`.`idPlaca`, `combustible`.`fchCreacion`");
    $consulta->bindParam(':placa',$placa);
  }
  $consulta->bindParam(':fchIni',$fchIni);
  $consulta->bindParam(':fchFin',$fchFin);
  $consulta->execute();
  return $consulta->fetchAll();
}

function  buscaServiciosEntreAbastecim($db,$placa,$iniFch,$iniHra,$finFch,$finHra){
  $consulta = $db->prepare("SELECT concat(apPaterno,' ',apMaterno,', ',nombres) AS nombCompleto,    concat(`despacho`.`fchDespacho`,'-',`hraInicio`) AS fchHra, `despacho`.`fchDespacho`, `despacho`.`correlativo`,  `hraInicio`, `hraFin`, `placa`, `kmInicio`, `kmFin`, `kmFin`- `kmInicio` AS `recorrido`, `cliente`.`nombre`, `cuenta`  FROM `despacho`, `cliente`, `despachopersonal`, `trabajador`  WHERE `despachopersonal`.`idTrabajador`= `trabajador`.`idTrabajador` AND `despacho`.`fchDespacho` = `despachopersonal`.`fchDespacho` AND `despacho`.`correlativo` = `despachopersonal`.`correlativo` AND `despachopersonal`.`tipoRol` = 'Conductor' AND `despacho`.`idCliente` = `cliente`.`idRuc` AND (concat(`despacho`.`fchDespacho`,'-',`hraInicio`) >= concat(:iniFch,'-',:iniHra)) AND (concat(`despacho`.`fchDespacho`,'-',`hraInicio`) <= concat(:finFch,'-',:finHra) ) AND (`despacho`.`fchDespacho`>= :iniFch AND `despacho`.`fchDespacho`<= :finFch) AND `placa` = :placa  ");
  
  $consulta->bindParam(':iniFch',$iniFch);
  $consulta->bindParam(':iniHra',$iniHra);
  $consulta->bindParam(':finFch',$finFch);
  $consulta->bindParam(':finHra',$finHra);
  $consulta->bindParam(':placa',$placa);
  $consulta->execute();
  return $consulta->fetchAll();
};
 
 
function abastecimientos($db,$placa,$fchIni,$fchFin){
  $consulta = $db->prepare("SELECT `fchCreacion`, `recorrido`, `galones`, `recorrido`/`galones` AS `rendimiento`   FROM `combustible` WHERE `idPlaca` LIKE :placa AND  `fchCreacion` >= :fchIni AND `fchCreacion` <= :fchFin");
  $consulta->bindParam(':placa',$placa);
  $consulta->bindParam(':fchIni',$fchIni);
  $consulta->bindParam(':fchFin',$fchFin);
  $consulta->execute();
  return $consulta->fetchAll();
}


function abastecimientosPromedioMes($db,$placa,$fchIni,$fchFin){
  $consulta = $db->prepare("SELECT count(*) AS `cantidad`,   concat( year( `fchCreacion` ) , '-', right( concat( '0', month( `fchCreacion` ) ) , 2 ) ) AS `fcha` , avg(`recorrido`/`galones` ) AS `promedio` FROM `combustible` WHERE  `idPlaca` LIKE :placa  AND (`fchCreacion` >= :fchIni AND `fchCreacion` <= :fchFin ) GROUP BY concat(year(`fchCreacion`),'-',month(`fchCreacion`)) ORDER BY `fchCreacion`");
  $consulta->bindParam(':placa',$placa);
  $consulta->bindParam(':fchIni',$fchIni);
  $consulta->bindParam(':fchFin',$fchFin);
  $consulta->execute();
  return $consulta->fetchAll();
};

function buscarPlacas($db,$fchIni,$fchFin, $grupoRend = NULL){
  if ($grupoRend != NULL){
    $where = "AND grupoRendimiento = '$grupoRend' ";
  } else {
    $where = "";
  }
  $consulta = $db->prepare("SELECT  distinct `combustible`.`idPlaca`, concat(`combustible`.`idPlaca`,'->',`vehiculo`.`rendimiento`) AS concatenado, `grupoRendimiento`  FROM `combustible`, `vehiculo`  WHERE `combustible`.`idPlaca` = `vehiculo`.`idPlaca` $where AND  (`combustible`.`fchCreacion` >= :fchIni AND `combustible`.`fchCreacion` <= :fchFin ) ORDER BY `combustible`.`fchCreacion`, `combustible`.`idPlaca` ");
  $consulta->bindParam(':fchIni',$fchIni);
  $consulta->bindParam(':fchFin',$fchFin);
  $consulta->execute();
  return $consulta->fetchAll();
}
 
function buscarDscto($db,$fchDespacho,$correlativo){
  $consulta = $db->prepare("SELECT `montoTotal` FROM `ocurrencia` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `tipoOcurrencia` = 'terceroMoy' ");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
  $consulta->bindParam(':correlativo',$correlativo);
  $consulta->execute();
  return $consulta->fetchAll();
};

function buscarMantenimientoPeriodos($db,$fchIni,$fchFin,$placa,$estado= 'Todos'){
  $cadConsulta = "SELECT `vehiculomantenimiento`.`idPlaca`, `marcaInicio`,`alerta`, `marcaFin`, `marcaAtendido`,`fchAtendido`, `vehiculomantenimiento`.`estado`, `observacion`, `vehiculomantenimiento`.`fchCreacion`,`vehiculomantenimiento`.cambioAceiteMotor ,`vehiculomantenimiento`.cambioAceiteCaja, `vehiculomantenimiento`.cambioAceiteCorona, `vehiculomantenimiento`.cambioFiltroPetroleo, `vehiculomantenimiento`.cambioFiltroAceite, `vehiculomantenimiento`.cambioFiltroAire, `vehiculomantenimiento`.cambioFiltroSeparador, `vehiculomantenimiento`.cambioFiltroAceiteTr, `vehiculomantenimiento`.cambiosOtros, `vehiculomantenimiento`.personaResponsable, `vehiculomantenimiento`.usuario, `vehiculomantenimiento`.`recorrido`  FROM `vehiculomantenimiento`, vehiculo WHERE `vehiculomantenimiento`.idPlaca =  vehiculo.idPlaca AND checkAlertas = 'Si' AND vehiculo.estado = 'activo' AND alerta = 'Aceite' AND (`vehiculomantenimiento`.`fchCreacion` >= :fchIni AND `vehiculomantenimiento`.`fchCreacion`  <= :fchFin)";

  if ($placa != NULL)
    $cadConsulta .= " AND `vehiculomantenimiento`.`idPlaca` = :placa ";

  if ($estado != 'Todos')
    $cadConsulta .= " AND `vehiculomantenimiento`.`estado` = :estado ";

  $cadConsulta .= " ORDER BY idPlaca, FchCreacion";  
  $consulta = $db->prepare("$cadConsulta ");
    
  $consulta->bindParam(':fchIni',$fchIni);
  $consulta->bindParam(':fchFin',$fchFin);
  if ($placa != NULL)
    $consulta->bindParam(':placa',$placa);
  if ($estado != 'Todos')
    $consulta->bindParam(':estado',$estado);

  $consulta->execute();
  return $consulta->fetchAll();
}

function insertaRegEnTrabajadorLicencias($db,$dni,$marcaIni,$marcaFin,$alerta,$plazo,$observacion,$escaneo = NULL){
  $usuario = $_SESSION["usuario"];
  $inserta = $db->prepare("INSERT INTO `trabajadorlicencias` (`idTrabajador`, `fchInicio`, `nombre`, `fchFin`, `plazo`, `observacion`, `escaneo`, `usuario`, `fchCreacion`) VALUES (:dni, :fchInicio, :nombre, :fchFin, :plazo, :observacion, :escaneo, :usuario,CURDATE())");
  $inserta->bindParam(':dni',$dni);
  $inserta->bindParam(':fchInicio',$marcaIni);
  $inserta->bindParam(':plazo',$plazo);
  $inserta->bindParam(':nombre',$alerta);
  $inserta->bindParam(':fchFin',$marcaFin);
  $inserta->bindParam(':observacion',$observacion);
  $inserta->bindParam(':escaneo',$escaneo);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();
}

function inactivarVehiculos($db){
  $actualiza = $db->prepare("UPDATE vehiculo SET estado = 'Inactivo', fchUltCronJob = now()  WHERE estado = 'Activo' AND considerarPropio != 'Si' AND idPlaca NOT IN ( SELECT `placa` FROM `despacho` WHERE placa IS NOT NULL AND datediff( curdate() , `fchDespacho` ) <=180 group by placa)");
  $actualiza->execute();
  return $actualiza->rowCount();
}

function buscarListadoVehiculosReporte($db,$estado = null, $rzonSocial = null){
  if ($estado == null)
    $where = " ";
  else 
    $where = " AND vehiculo.estado = :estado ";
  $consulta = $db->prepare("SELECT base, `vehiculo`.idPlaca, clase, marca, modelo, anioFabricacion, color, nroMotor, nroSerie, cilindros,`cc`, hp, nroRuedas, llantas, pulg, pesoUtil, dimInteriorLargo, dimInteriorAncho, dimInteriorAlto, dimInteriorLargo * dimInteriorAncho * dimInteriorAlto AS vol, carroceria, puertaCarr, if (rznSocial= 'INVERSIONES MOY S.A.C.', rznSocial, `vehiculodueno`.nombreCompleto) AS rznSocial, propietario, `vehMtc`.fchFin AS Mtc, `vehMtc`.entidadRespons AS MtcEntidad, `vehSoat`.fchFin AS Soat, `vehSoat`.entidadRespons AS SoatEntidad, `vehRc`.fchFin AS Rc, `vehRc`.entidadRespons AS RcEntidad  FROM vehiculo left join vehiculolicencias AS vehMtc ON `vehiculo`.idPlaca = `vehMtc`.idPlaca AND `vehMtc`.nombre = 'Cert. M.T.C.' AND `vehMtc`.estado = 'Activo' left join vehiculolicencias AS vehSoat ON `vehiculo`.idPlaca = `vehSoat`.idPlaca AND `vehSoat`.nombre = 'SOAT' AND `vehSoat`.estado = 'Activo' left join vehiculolicencias AS vehRc ON `vehiculo`.idPlaca = `vehRc`.idPlaca AND `vehRc`.nombre = 'Seguro RC' AND `vehRc`.estado = 'Activo' left join vehiculodueno ON `vehiculo`.rznSocial = `vehiculodueno`.documento  WHERE 1 $where");

  if ($estado != null)
    $consulta->bindParam(':estado',$estado);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarUltimoManten($db,$idPlaca,$alerta){
  $consulta = $db->prepare("SELECT `marcaFin`, `marcaAtendido`, `fchAtendido`, `usuario`, `fchCreacion`  FROM `vehiculomantenimiento` WHERE `idPlaca` LIKE :placa AND `alerta` = :alerta AND `estado` = 'Inactivo' ORDER BY 1*`marcaAtendido` DESC LIMIT 1");
  $consulta->bindParam(':placa',$idPlaca);
  $consulta->bindParam(':alerta',$alerta);
  $consulta->execute();
  $aux = $consulta->fetchAll();
  $cant = count($aux);
  if ($cant == 1){
    foreach ($aux as $item) {
      return $item['marcaAtendido'];
    }
  } else {
    $consulta = $db->prepare("SELECT `marcaFin`, `marcaAtendido`, `fchAtendido`, `usuario`, `fchCreacion`  FROM `vehiculomantenimiento` WHERE `idPlaca` LIKE :placa AND `alerta` = :alerta  ORDER BY 1*`marcaFin` DESC LIMIT 1");
    $consulta->bindParam(':placa',$idPlaca);
    $consulta->bindParam(':alerta',$alerta);
    $consulta->execute();
    $aux = $consulta->fetchAll();
    foreach ($aux as $item) {
      return $item['marcaFin'];
    }
  }
}

function buscarRecorridos($db,$fchIni,$fchFin,$placa = null,$idCliente = null,$propietario = null){
  $where = "";
  if ($placa != null) $where .= " AND despacho.placa IN ($placa) ";
  if ($idCliente != null) $where .= " AND idCliente = :idCliente ";
  if ($propietario != null) $where .= " AND propietario like :propietario ";

  $consulta = $db->prepare("SELECT `fchDespacho`, `placa`,`idCliente`,nombre, `cuenta`, `hraInicio`, `hraFin`,`kmInicio`, `kmInicioCliente`, `kmFinCliente`, `hraFinCliente`, `lugarFinCliente`, `kmFin`, `recorridoEsperado` , if (kmFinCliente IS NULL, kmFin, kmFinCliente) -  if (kmInicioCliente = 0, kmInicio,kmInicioCliente)  AS recorridoServicio, kmFin - kmInicio AS recorridoTotal, usuarioGrabaFin,  propietario FROM `despacho`, cliente , vehiculo  WHERE despacho.placa = vehiculo.idPlaca AND  `despacho`.idCliente = `cliente`.idRuc AND (`fchDespacho` >= :fchIni AND `fchDespacho` <= :fchFin ) AND cuenta NOT LIKE 'soloPersonal%' $where ORDER BY  fchDespacho, hraInicio, placa");
  $consulta->bindParam(':fchIni',$fchIni);
  $consulta->bindParam(':fchFin',$fchFin);

  if ($idCliente != null) $consulta->bindParam(':idCliente',$idCliente);
  if ($propietario != null) $consulta->bindParam(':propietario',$propietario);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarVehiculoDocsVarios($db,$id = NULL,$idPlaca = NULL, $estado = 'Mostrar' ){
  $where = " estado = 'Mostrar'  ";
  if ($id != NULL) $where .= " AND  `id` = :id  ";
  if ($idPlaca != NULL) $where .= " AND  `idPlaca` = :idPlaca  ";


  $consulta = $db->prepare("SELECT `id`, `idPlaca`, `nombre`, `estado`, `descripcion`, `escaneo`, `usuario`, `fchCreacion`, `usuarioUltimoCambio`, `fchUltimoCambio` FROM `vehiculodocsvarios` WHERE $where ");
  if ($id != NULL) $consulta->bindParam(':id',$id);
  if ($idPlaca != NULL) $consulta->bindParam(':idPlaca',$idPlaca);
  $consulta->execute();
  return $consulta->fetchAll();
}

function generaIdDocumAdjVehiculo($db){
  $consulta = $db->prepare("SELECT `id` FROM `vehiculodocsvarios` ORDER BY `id` DESC limit 1 ");
  $consulta->execute();
  $auxNro =  $consulta->fetchAll();
  $nro = "";
  foreach ($auxNro as $item) {
    $nro = $item['id'];
  }
  $anhioActual = date("y");
  $anhioNro = substr($nro, 0,2);
  if ($anhioNro == $anhioActual)
    $sgteNro = $nro*1+1;
  else
    $sgteNro = $anhioActual."000001";
  return $sgteNro;
}

function insertaRegEnVehiculoDocsVarios($db,$id,$idPlaca,$nombre,$descripcion,$escaneo){
  $usuario = $_SESSION['usuario'];
  //echo "$id,$dni,$nombre,$descripcion,$escaneo,$usuario";
  $inserta = $db->prepare("INSERT INTO `vehiculodocsvarios` (`id`, `idPlaca`, `nombre`, `descripcion`, `escaneo`, `usuario`, `fchCreacion`) VALUES (:id, :idPlaca, :nombre, :descripcion, :escaneo, :usuario, curdate());");
  $inserta->bindParam(':id',$id);
  $inserta->bindParam(':idPlaca',$idPlaca);
  $inserta->bindParam(':nombre',$nombre);
  $inserta->bindParam(':descripcion',$descripcion);
  $inserta->bindParam(':escaneo',$escaneo);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();
}

function editaRegEnVehiculoDocsVarios($db,$id,$nombre,$descripcion){
  $usuario = $_SESSION['usuario'];
  //echo "$id,$nombre,$descripcion, $usuario  ";
  $actualiza = $db->prepare("UPDATE `vehiculodocsvarios` SET `nombre` = :nombre, `descripcion` = :descripcion, `usuarioUltimoCambio` = :usuario, `fchUltimoCambio` = curdate() WHERE `id` = :id");
  $actualiza->bindParam(':id',$id);
  $actualiza->bindParam(':nombre',$nombre);
  $actualiza->bindParam(':descripcion',$descripcion);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();
}

function eliminaRegEnVehiculoDocsVarios($db,$id,$escaneo){

  $elimina = $db->prepare("DELETE FROM `vehiculodocsvarios` WHERE `id` = :id");
  $elimina->bindParam(':id',$id);
  $elimina->execute();
}

function opcionesEnum($db,$tabla,$campo, $valor = NULL){
  $opciones = "";
  $sql = 'SHOW COLUMNS FROM '.$tabla.' WHERE field="'.$campo.'"';
  $row = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
  foreach(explode("','",substr($row['Type'],6,-2)) AS $option) {
    $opciones .= "<option ".(($valor == $option)?" SELECTED ":"").">$option</option>";
  }
  return $opciones;
}

function buscarVehiculosOpciones($db, $considerarPropio = NULL ,$estado = NULL){
  $where = " 1 ";
  if($considerarPropio != NULL) $where .= " AND considerarPropio = :considerarPropio ";
  if($estado != NULL) $where .= " AND vehiculo.estado = :estado ";

  //$consulta = $db->prepare("SELECT vehiculo.`idPlaca`, `nroVehiculo`, `estado`, `propietario`, `rznSocial`, `considerarPropio`, `nroMotor`, `nroSerie`, `rendimiento`, `grupoRendimiento`, `eficCombustible`, `combustibleFrec`, `checkAlertas`, kmAnterior FROM `vehiculo` LEFT JOIN ( SELECT `idPlaca`, max(`kmActual`) AS kmAnterior FROM `combustible` Group BY idPlaca ORDER BY `combustible`.`idPlaca` ASC ) AS t1 ON `vehiculo`.idPlaca = `t1`.idPlaca  WHERE  $where ");

  $consulta = $db->prepare("SELECT vehiculo.`idPlaca`, `nroVehiculo`, vehiculo.`estado`, `propietario`, `rznSocial`, `considerarPropio`, `nroMotor`, `nroSerie`, `rendimiento`, `grupoRendimiento`, `eficCombustible`, `combustibleFrec`, `checkAlertas`, `kmAnterior`, `idEficSolesKm` , `vehefisolkm`.valorEsperado FROM `vehiculo` LEFT JOIN ( select `com`.idPlaca, `com`.kmActual AS kmAnterior FROM combustible AS com , ( SELECT `idPlaca`, max(tiempo) AS tpoAnterior FROM `combustible` Group BY idPlaca) AS t2 WHERE `com`.tiempo = `t2`.tpoAnterior AND `com`.idPlaca = `t2`.idPlaca ) AS t1 ON `vehiculo`.idPlaca = `t1`.idPlaca LEFT JOIN vehiculoeficsoleskm AS vehefisolkm ON vehiculo.`idEficSolesKm` = `vehefisolkm`.id  WHERE $where ");


  if ($considerarPropio != NULL) $consulta->bindParam(':considerarPropio',$considerarPropio);
  if ($estado != NULL) $consulta->bindParam(':estado',$estado);
  $consulta->execute();
  return $consulta->fetchAll();
}

function cargarAbastecimientos($db){
  $usuario = $_SESSION['usuario'];
  $nroTotal = 0;
  

  $losCampos = " INSERT INTO `combustible` (`fchCreacion`, `hraCreacion`, tiempo, `idPlaca`, `correlativo`, `chofer`, `kmActual`, `recorrido`, `marcaCombustible`, `nroVale`, `precioGalon`, `precioGalonAnt`, `total`, `galones`, `grifo`, `grifero`, `dniConductor`, `producto`, `autoriza` , `ratioSolesKm`, `observacion`, `pagado`, `docPagoTercero`, `docPagoTerTipo`, `usuario`, `fchIngreso`, `usuarioUltimoCambio`, `fchUltimoCambio`) VALUES ";

  $separador = $cadena = "";
  $primero = "Si";
  $contador = 1;
  foreach ($_SESSION["arrDataCombust"] as $key => $dataCombustible) {
    //var_dump($dataCombustible);
    
    $fecha = Date("Y-m-d");

    $fchHra = $dataCombustible["0"]." ".$dataCombustible["1"];

    if ($primero == "Si"){
      $primero = "No";
      continue;
    }

    $ratioSolesKm = $dataCombustible["16"] == "" ? 0 : $dataCombustible["16"];
    
    $cadena .= "$separador('".$dataCombustible["0"]."', '".$dataCombustible["1"]."', '$fchHra', '".$dataCombustible["2"]."', 0, '".$dataCombustible["4"]."', '".$dataCombustible["10"]."', '".$dataCombustible["13"]."', 0, '".$dataCombustible["5"]."', '".$dataCombustible["6"]."', 0, '".$dataCombustible["7"]."', '".$dataCombustible["8"]."', '".$dataCombustible["12"]."', '', '".$dataCombustible["3"]."','".$dataCombustible["9"]."','".$dataCombustible["11"]."',".$ratioSolesKm." ,'', NULL, NULL, NULL, '$usuario', now(), '$usuario', '$fecha')";

    if ($contador % 100 == 0){
      $cadCompleta = $losCampos.$cadena;
      $nro = $db->exec($cadCompleta);
      //echo $cadCompleta."<br>";
      $nroTotal = $nroTotal + $nro;
      $contador = 1;
      $separador = $cadena = "";
    } else {
      $separador = " ,";
      $contador++;

    }
  
    //echo "<br>";
  }

  if ($cadena != ""){
    $cadCompleta = $losCampos.$cadena;
    //echo $cadCompleta."<br>";
    $nro = $db->exec($cadCompleta);
    $nroTotal = $nroTotal + $nro;

  }

  if ($nroTotal == 0){
    $_SESSION['msj'] = "ERROR. No se insertaron registros";
    $_SESSION['colorMsj'] = 'rojo';
  } else {
    $_SESSION['msj'] = "Se insertaron ".$nroTotal." registros.";
    $_SESSION['colorMsj'] = 'verde';
  }
}

function buscarEficSolesKm($db,$estado = NULL){
  $where = ($estado == NULL)?"":" WHERE estado = :estado ";

  $consulta = $db->prepare("SELECT `id`, `tipoCombust`, `nombre`, `descripcion`, `valorEsperado`, `estado`, `usuario`, `fchCreacion` FROM `vehiculoeficsoleskm` $where ");

  if ($estado != NULL) $consulta->bindParam(':estado',$estado);
  $consulta->execute();
  return $consulta->fetchAll(); 

}




?>
