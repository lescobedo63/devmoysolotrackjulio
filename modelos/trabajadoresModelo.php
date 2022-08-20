<?php
global $servidor, $bd, $usuario, $contrasenia;
$db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);

  function diferDias($date1,$date2){
    if (empty($date1) or ($date1 == '0000-00-00')) {$date1 = Date("Y-m-d");}
    if (empty($date2) or ($date2 == '0000-00-00')) {$date2 = Date("Y-m-d");}
    $segs = strtotime($date1)-strtotime($date2);
    $dDias = intval($segs/86400);
    return $dDias;
  }

  function retrocedeFecha($fecha,$cambioDias){
   $partesFecha = explode("-",$fecha);
   return date("Y-m-d", mktime(0, 0, 0, $partesFecha[1],$partesFecha[2] + $cambioDias, $partesFecha[0]));
  }

  function showID($db, $escaneo){
    $consulta = $db->prepare("SELECT id, idTrabajador FROM trabajadordocsvarios WHERE escaneo = :escaneo");
    $consulta->bindParam(':escaneo',$escaneo);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function listarTrabajadoresSendBoletas($db, $action){
    if($action == "boletas"){
      $consulta = $db->prepare("SELECT idTrabajador FROM `trabajador` WHERE estadoTrabajador != 'Inactivo' AND categTrabajador = '5ta'");
    }
    else if($action == "documentos"){
      $consulta = $db->prepare("SELECT idTrabajador FROM `trabajador` WHERE estadoTrabajador != 'Inactivo' AND categTrabajador != 'Tercero' AND categTrabajador != ''");
    }
    else if($action == "coordinador"){
      $consulta = $db->prepare("SELECT idTrabajador FROM `trabajador` WHERE estadoTrabajador != 'Inactivo' AND categTrabajador != 'Tercero' AND categTrabajador != '' AND tipoTrabajador = 'Coordinador'");
    }
    else if($action == "conductor"){
      $consulta = $db->prepare("SELECT idTrabajador FROM `trabajador` WHERE estadoTrabajador != 'Inactivo' AND categTrabajador != 'Tercero' AND categTrabajador != '' AND tipoTrabajador = 'Conductor'");
    }
    else if($action == "auxiliar"){
      $consulta = $db->prepare("SELECT idTrabajador FROM `trabajador` WHERE estadoTrabajador != 'Inactivo' AND categTrabajador != 'Tercero' AND categTrabajador != '' AND tipoTrabajador = 'Auxiliar'");
    }
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function listarBoletasTrabajador($db, $dni, $mes, $anio, $tema, $periodo){
    if($tema == "pago"){
      $consulta = $db->prepare("SELECT id, nombre, descripcion, escaneo FROM `trabajadordocsvarios` WHERE idTrabajador = :dni AND SUBSTRING_INDEX(SUBSTRING_INDEX(descripcion, ' ', 1), ' ', -1) = :mes AND SUBSTRING_INDEX(SUBSTRING_INDEX(descripcion, ' ', 2), ' ', -1) = :anio");
      $consulta->bindParam(':dni',$dni);
      $consulta->bindParam(':mes',$mes);
      $consulta->bindParam(':anio',$anio);
    }
    else if($tema == "cts"){
      $consulta = $db->prepare("SELECT id, nombre, descripcion, escaneo FROM `trabajadordocsvarios` WHERE idTrabajador = :dni AND descripcion = :periodo");
      $consulta->bindParam(':dni',$dni);
      $consulta->bindParam(':periodo',$periodo);
    }
    else if($tema == "utilidades"){
      $consulta = $db->prepare("SELECT id, nombre, descripcion, escaneo FROM `trabajadordocsvarios` WHERE idTrabajador = :dni AND descripcion = :anio");
      $consulta->bindParam(':dni',$dni);
      $consulta->bindParam(':anio',$anio);
    }
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function showDocsTrabajadores($db,$descripcion){
  	$consulta = $db->prepare("SELECT `escaneo` FROM `trabajadordocsvarios` WHERE `descripcion` = :descripcion");
  	$consulta->bindParam(':descripcion',$descripcion);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function deleteMasivoDocsTrabajadores($db,$descripcion){
	  $elimina = $db->prepare("DELETE FROM `trabajadordocsvarios` WHERE `descripcion` = :descripcion");
  	$elimina->bindParam(':descripcion',$descripcion);
  	$elimina->execute();
  }


function buscarConductoresPredictivo($db,$placa,$idCliente){
	//Por ahora no se va a considerar el cliente pero se puede añadir
	$consulta = $db->prepare("SELECT `trabajador`.`idTrabajador`, `fchCaducidad`, `tipoTrabajador`, `categTrabajador`, `modoSueldo`, `estadoTrabajador`, `apPaterno`, `apMaterno`, `nombres`, `fchNacimiento`, `imgTrabajador`, `telfMovil`, `dirCalleyNro`, `dirDistrito`, `dirProvincia`, `dirDepartamento`, `dirTelefono`, `dirObservacion`, `eMail`, `nombreConyuge`, `estadoCivil`, `gradoInstruccion`, `licenciaNro`, `licenciaVigencia`, `licenciaCategoria`, `modoContratacion`, `bancoNombre`, `bancoNroCuenta`, `bancoTipoCuenta`, `bancoMoneda`, `remuneracionBasica`, `asignacionFamiliar`, `cajaChica`, `renta5ta`, `manejaLiquidacion`, `fondoGarantia`, `fondoGaranRetenido`, `esMaster`, `precioMaster`, `deseaDscto`, `deseaDsctoOnp`, `entidadPension`, `tipoComision`, `asumeEmpresa`, `cuspp`, `codTrabajador`, `formaPago`, `tallaCamisa`, `tallaPantalon`, `tallaBotas`, `trabajador`.`usuario`, `trabajador`.`fchCreacion`, `trabajador`.`usuarioUltimoCambio`, `trabajador`.`fchUltimoCambio`, `trabDni`.fchFin, if (DATEDIFF(`trabDni`.fchFin, curdate())> 0,'1','0') AS `bienDni`, `trabLic`.fchFin, if (DATEDIFF(`trabLic`.fchFin, curdate())>0,'1','0') AS `bienLic` FROM predictivotrabajador,  trabajador LEFT JOIN `trabajdocumentos` AS trabDni ON `trabajador`.idTrabajador = `trabDni`.idTrabajador  AND  `trabDni`.estado = 'Predeterminado' LEFT JOIN `trabajadorlicencias` AS trabLic ON `trabajador`.idTrabajador = `trabLic`.idTrabajador AND `trabLic`.nombre = 'LicConducir' AND `trabLic`.estado = 'Activo' WHERE (tipoTrabajador = 'Conductor' OR tipoTrabajador = 'Coordinador') AND estadoTrabajador = 'Activo' AND `predictivotrabajador`.idTrabajador = `trabajador`.idTrabajador  AND `predictivotrabajador`.placa = :placa AND `predictivotrabajador`.tipoRol = 'Conductor' ORDER BY apPaterno, apMaterno ASC");

		//$consulta = $db->prepare("SELECT `trabajador`.`idTrabajador`, `fchCaducidad`, `tipoTrabajador`, `categTrabajador`, `modoSueldo`, `estadoTrabajador`, `apPaterno`, `apMaterno`, `nombres`, `fchNacimiento`, `imgTrabajador`, `telfMovil`, `dirCalleyNro`, `dirDistrito`, `dirProvincia`, `dirDepartamento`, `dirTelefono`, `dirObservacion`, `eMail`, `nombreConyuge`, `estadoCivil`, `gradoInstruccion`, `licenciaNro`, `licenciaVigencia`, `licenciaCategoria`, `modoContratacion`, `bancoNombre`, `bancoNroCuenta`, `bancoTipoCuenta`, `bancoMoneda`, `remuneracionBasica`, `asignacionFamiliar`, `cajaChica`, `renta5ta`, `manejaLiquidacion`, `fondoGarantia`, `fondoGaranRetenido`, `esMaster`, `precioMaster`, `deseaDscto`, `deseaDsctoOnp`, `entidadPension`, `tipoComision`, `asumeEmpresa`, `cuspp`, `codTrabajador`, `formaPago`, `tallaCamisa`, `tallaPantalon`, `tallaBotas`, `trabajador`.`usuario`, `trabajador`.`fchCreacion`, `trabajador`.`usuarioUltimoCambio`, `trabajador`.`fchUltimoCambio`, `trabDni`.fchFin, if (DATEDIFF(`trabDni`.fchFin, curdate())>0,'1','0') AS `bienDni`, `trabLic`.fchFin, if (DATEDIFF(`trabLic`.fchFin, curdate())>0,'1','0') AS `bienLic`  FROM predictivotrabajador,  trabajador LEFT JOIN `trabajadorlicencias` AS trabDni ON `trabajador`.idTrabajador = `trabDni`.idTrabajador AND `trabDni`.nombre = 'dni' AND  `trabDni`.estado = 'Activo' LEFT JOIN `trabajadorlicencias` AS trabLic ON `trabajador`.idTrabajador = `trabLic`.idTrabajador AND `trabLic`.nombre = 'LicConducir' AND `trabLic`.estado = 'Activo' WHERE (tipoTrabajador = 'Conductor' OR tipoTrabajador = 'Coordinador') AND estadoTrabajador = 'Activo' AND `predictivotrabajador`.idTrabajador = `trabajador`.idTrabajador  AND `predictivotrabajador`.placa = :placa AND `predictivotrabajador`.tipoRol = 'Conductor' ORDER BY apPaterno, apMaterno ASC");



	$consulta->bindParam(':placa',$placa);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarTodosLosConductores($db){
	$consulta = $db->prepare("SELECT `trabajador`.`idTrabajador`, `fchCaducidad`, `tipoTrabajador`, `categTrabajador`, `modoSueldo`, `estadoTrabajador`, `apPaterno`, `apMaterno`, `nombres`, `fchNacimiento`, `imgTrabajador`, `telfMovil`, `dirCalleyNro`, `dirDistrito`, `dirProvincia`, `dirDepartamento`, `dirTelefono`, `dirObservacion`, `eMail`, `nombreConyuge`, `estadoCivil`, `gradoInstruccion`, `licenciaNro`, `licenciaVigencia`, `licenciaCategoria`, `modoContratacion`, `bancoNombre`, `bancoNroCuenta`, `bancoTipoCuenta`, `bancoMoneda`, `remuneracionBasica`, `asignacionFamiliar`, `cajaChica`, `renta5ta`, `manejaLiquidacion`, `fondoGarantia`, `fondoGaranRetenido`, `esMaster`, `precioMaster`, `deseaDscto`, `deseaDsctoOnp`, `entidadPension`, `tipoComision`, `asumeEmpresa`, `cuspp`, `codTrabajador`, `formaPago`, `tallaCamisa`, `tallaPantalon`, `tallaBotas`, `trabajador`.`usuario`, `trabajador`.`fchCreacion`, `trabajador`.`usuarioUltimoCambio`, `trabajador`.`fchUltimoCambio`, `trabDni`.fchFin, if (DATEDIFF(`trabDni`.fchFin, curdate())>0,'1','0') AS `bienDni`, `trabLic`.fchFin, if (DATEDIFF(`trabLic`.fchFin, curdate())>0,'1','0') AS `bienLic` FROM trabajador LEFT JOIN  `trabajdocumentos` AS trabDni ON `trabajador`.idTrabajador = `trabDni`.idTrabajador  AND  `trabDni`.estado = 'Predeterminado' LEFT JOIN `trabajadorlicencias` AS trabLic ON `trabajador`.idTrabajador = `trabLic`.idTrabajador AND `trabLic`.nombre = 'LicConducir' AND `trabLic`.estado = 'Activo' WHERE (tipoTrabajador = 'Conductor' OR tipoTrabajador = 'Coordinador') AND estadoTrabajador = 'Activo' GROUP BY `trabajador`.`idTrabajador` ORDER BY apPaterno, apMaterno ASC");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarTodosLosTrabajadores($db){
	$consulta = $db->prepare("SELECT * FROM trabajador ORDER BY apPaterno, apMaterno, nombres ");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarTodosLosTrabActivos($db){
	$consulta = $db->prepare("SELECT * FROM trabajador WHERE estadoTrabajador = 'Activo'  ORDER BY apPaterno, apMaterno, nombres ");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarTodosLosTrabActivosMail($db){
	$consulta = $db->prepare("SELECT * FROM `trabajador` WHERE `estadoTrabajador` = 'Activo' AND  ( `eMail` is not null AND `eMail` != '' ) ORDER BY `apPaterno`, `apMaterno`, `nombres` ");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarTrabajadores($db,$query){
  if ($query == ' WHERE 1 ') $query = " WHERE `estadoTrabajador` = 'Activo'  ";
	$consulta = $db->prepare("SELECT `idTrabajador`, tipoDocTrab, nroDocTrab , `fchCaducidad`, `tipoTrabajador`, `categTrabajador`, `asumeEmpresa`, `modoSueldo`, `estadoTrabajador`, `apPaterno`, `apMaterno`, `nombres`, `fchNacimiento`, `imgTrabajador`,`telfMovil`, `dirCalleyNro`, `dirDistrito`, `dirProvincia`, `dirDepartamento`, `dirTelefono`, `dirObservacion`, `eMail`, `nombreConyuge`, `estadoCivil`, `gradoInstruccion`, `licenciaNro`, `licenciaVigencia`, `licenciaCategoria`, `modoContratacion`, `bancoNombre`, `bancoNroCuenta`, `bancoTipoCuenta`, `bancoMoneda`, `remuneracionBasica`, `asignacionFamiliar`, `cajaChica`, `renta5ta` ,`fondoGarantia`, `esMaster`, `precioMaster`, `deseaDscto`, `entidadPension`, `cuspp`, `codTrabajador`, `formaPago`, `tallaCamisa`, `tallaPantalon`, `tallaBotas`, concat(`trabajador`.`apPaterno`,' ',`trabajador`.`apMaterno`,', ', `trabajador`.`nombres` ) as `nombreCompleto`, `usuario`, `fchCreacion`,if(fchNacimiento is null,Year(curdate()), YEAR(CURDATE())-YEAR(`fchNacimiento`) + IF(DATE_FORMAT(CURDATE(),'%m-%d') > DATE_FORMAT(`fchNacimiento`,'%m-%d'), 0, -1)) AS `edad` , bancoNombre, bancoNroCuenta, bancoTipoCuenta FROM trabajador $query ORDER BY `apPaterno`, `apMaterno`, `nombres` ");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarConductor($db,$dni){
	$consulta = $db->prepare("SELECT * FROM trabajador WHERE idTrabajador = :dni ");
	$consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarModosContratacion($db){
	$consulta = $db->prepare("SELECT * FROM `auxiliar` WHERE tipo = 'CNT' ");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarConductorHijos($db,$dni){
	$consulta = $db->prepare("SELECT * FROM hijostrabajador WHERE idTrabajador = :dni  ");
	$consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarConductorIngresos($db,$dni){
	$consulta = $db->prepare("SELECT * FROM altasbajas WHERE idTrabajador = :dni  ");
	$consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarConductorTelefonos($db,$dni){
	$consulta = $db->prepare("SELECT * FROM trabajadortelefono WHERE idTrabajador = :dni  ");
	$consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarCategorias($db){
	$consulta = $db->prepare("SELECT * FROM categoria");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarTelefonosLibres($db)
{
	$consulta = $db->prepare("SELECT * FROM telefono WHERE estado = 'Libre'");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarDetallesPagoTrabajador($db,$dni,$fchInicio,$fchFin){
  $consulta = $db->prepare("SELECT concat(despachopersonal.`fchDespacho`,'-', despachopersonal.`correlativo`) as codigo , concat(despacho.idCliente,':', cliente.nombre) as cliente , despacho.cuenta, despacho.hraInicio , `valorRol`, `tipoRol` FROM `despachopersonal`, despacho, cliente  WHERE cliente.idRuc = despacho.idCliente AND  despacho.fchDespacho = despachopersonal.fchDespacho AND despacho.correlativo = despachopersonal.correlativo AND (despachopersonal.fchDespacho>= :fchInicio AND despachopersonal.fchDespacho <= :fchFin) AND concluido = 'Si' AND `idTrabajador` = :dni order by Codigo ");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':fchInicio',$fchInicio);
  $consulta->bindParam(':fchFin',$fchFin);  
	$consulta->execute();
	return $consulta->fetchAll();   
}

function buscarTrabajadorIngresosDelMes($db,$dni,$fchInicio){
  $mes = date('m',strtotime($fchInicio));
  $anio = date('Y',strtotime($fchInicio));
  $consulta = $db->prepare("SELECT `despacho`.`concluido`, `despachopersonal`.`idTrabajador`, `despachopersonal`.`fchDespacho`, sum(`valorRol` + `valorAdicional`) as diario  FROM `despachopersonal` , `despacho` WHERE `despachopersonal`.fchDespacho = `despacho`.fchDespacho AND `despachopersonal`.correlativo = `despacho`.correlativo AND `despacho`.concluido = 'Si' AND `despachopersonal`.idTrabajador = :dni  AND month(`despachopersonal`.`fchDespacho`) = '$mes' AND year(`despachopersonal`.`fchDespacho`) = '$anio' group by  `fchDespacho` order by `fchDespacho`");
  $consulta->bindParam(':dni',$dni);
  $consulta->execute();
  return $consulta->fetchAll();  
}

function buscarUsoCajaChicaDelMes($db,$dni,$fchInicio){
  $mes = date('m',strtotime($fchInicio));
  $anio = date('Y',strtotime($fchInicio));
  
  $consulta = $db->prepare("SELECT `idTrabajador`, sum(`monto`) as total  FROM `trabajadorreportegastos` WHERE `idTrabajador` = :dni AND MONTH(`fchGasto`) = '$mes' AND YEAR(`fchGasto`) = '$anio'");  
  $consulta->bindParam(':dni',$dni);
  $consulta->execute();
  return $consulta->fetchAll();  
}

function basicoPorDia($db,$dni){
  $consulta = $db->prepare("SELECT `idTrabajador`, (`remuneracionBasica`+`asignacionFamiliar`)/30 as basicoDia FROM `trabajador` WHERE `idTrabajador` = :dni");
  $consulta->bindParam(':dni',$dni);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarTrabajador($db,$dni){
	$consulta = $db->prepare("SELECT `idTrabajador`, `tipoDocTrab`,  `tipoTrabajador`, `categTrabajador`, `modoSueldo` , `apPaterno`, `apMaterno`, `nombres`, concat( `nombres`,' ',`apPaterno`,' ', `apMaterno`) as nombreCompleto , `ruc`, `estadoTrabajador`, `fchNacimiento`, `imgTrabajador`, `telfMovil`, `dirCalleyNro`, `dirDistrito`, `dirProvincia`, `dirDepartamento`, `dirTelefono`, `dirObservacion`,  `dirNro`, `dirUrb`, `telfAdicional`, `telfReferencia`, `telfRefContacto`, `telfRefParentesco`, `sexo`, `apPaternoPadre`, `apMaternoPadre`, `nombresPadre`, `ocupacionPadre`, `apPaternoMadre`, `apMaternoMadre`, `nombresMadre`, `ocupacionMadre`, `apPaternoConyu`, `apMaternoConyu`, `ocupacionConyu`, `eMail`, `nombreConyuge`, `estadoCivil`, `estadoCivilDesde`, `nroHijos`, `sec01Anhio`, `sec01Grado`, `sec01Centro`, `sec02Anhio`, `sec02Grado`, `sec02Centro`, `sup01Desde`, `sup01Hasta`, `sup01Carrera`, `sup01Centro`, `sup01Avance`, `sup01Grado`, `sup02Desde`, `sup02Hasta`, `sup02Carrera`, `sup02Centro`, `sup02Avance`, `sup02Grado`, `exp01Empresa`, `exp01Cargo`, `exp01Sueldo`, `exp01Desde`, `exp01Hasta`, `exp01Jefe`, `exp01JefePuesto`, `exp01Telf`, `exp02Empresa`, `exp02Cargo`, `exp02Sueldo`, `exp02Desde`, `exp02Hasta`, `exp02Jefe`, `exp02JefePuesto`, `exp02Telf`, `exp03Empresa`, `exp03Cargo`, `exp03Sueldo`, `exp03Desde`, `exp03Hasta`, `exp03Jefe`, `exp03JefePuesto`, `exp03Telf`, `laboroEnMoy`, `laboroEnMoyPuesto`, `laboroEnMoySucursal`, `laboroEnMoyMotivoCese`, `laboroEnMoyFchCese`, `familiarEnMoy`, `familiarEnMoyNombre`, `familiarEnMoyParentesco`, `emergenciaNombre`, `emergenciaParentesco`, `emergenciaTelfFijo`, `emergenciaTelfCelular`, `saludGrupoSanguineo`, `saludTieneEnfCronica`, `saludTieneAlergia`, `saludAlergia`, `saludEnfCronica`, `gradoInstruccion`, `licenciaNro`, `licenciaVigencia`, `licenciaCategoria`, `modoContratacion`, `bancoNombre`, `bancoNroCuenta`, `bancoTipoCuenta`, `bancoMoneda`, `remuneracionBasica`, `asignacionFamiliar`, `cajaChica`, `renta5ta`, `diasVacacAnual`, `manejaLiquidacion`, `fondoGarantia`, `fondoGaranRetenido`, `esMaster`, `precioMaster`, `deseaDscto`, `deseaDsctoOnp`, `entidadPension`, `tipoComision`, `trabajador`.idPoliza, `asumeEmpresa`, `cuspp`, `codTrabajador`, `movilidadAsignadaMes`, `bonoProducMes`, `formaPago`, `tallaCamisa`, `tallaPantalon`, `tallaBotas`, `tallaPolo`, `tallaCasaca`, `comisionFlujo`, `comisionMixta`, `primaSeg`, `porcentObligat`, `descontarSeguro`, `descontarComisionAfp`, `descontarAporteAfp`, `polizas`.valorPoliza  FROM `trabajador` LEFT JOIN pension ON `pension`.nombre = `trabajador`.entidadPension LEFT JOIN polizas ON `polizas`.idPoliza = `trabajador`.idPoliza  WHERE idTrabajador =  :dni ");
	$consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarTrabajadorHijos($db,$dni)
{
	$consulta = $db->prepare("SELECT * FROM hijostrabajador WHERE idTrabajador = :dni  ");
	$consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarTrabajadorIngresos($db,$dni)
{
	$consulta = $db->prepare("SELECT * FROM altasbajas WHERE idTrabajador = :dni  ");
	$consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll();
}

function verificarTrabajadorOcupado($db,$idTrabajador,$fchDespacho){
	$consulta = $db->prepare("SELECT count(*) FROM `despachopersonal`,`despacho` WHERE `despachopersonal`.fchDespacho = `despacho`.fchDespacho AND `despachopersonal`.correlativo = `despacho`.correlativo AND `despacho`.concluido = 'No' AND `despacho`.fchDespacho = :fchDespacho And `idTrabajador` = :idTrabajador ");	
	$consulta->bindParam(':fchDespacho',$fchDespacho);
	$consulta->bindParam(':idTrabajador',$idTrabajador);	
	$consulta->execute();
	return $consulta->fetchColumn();
}

function buscarTodosLosAuxiliares($db){
	$consulta = $db->prepare("SELECT * FROM trabajador WHERE (tipoTrabajador = 'Auxiliar' OR tipoTrabajador = 'Conductor' OR tipoTrabajador = 'Coordinador')  AND estadoTrabajador = 'Activo'   ORDER BY apPaterno, apMaterno ASC");
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarTodosLosAuxiliaresConAlerta($db,$fchDespacho,$hraDespacho){
	$consulta = $db->prepare("SELECT trabajador.`idTrabajador`, `estadoTrabajador`, `apPaterno`, `apMaterno`, `nombres`, `despachopersonal`.tipoRol, `despacho`.hraInicio, `despacho`.hraFin FROM trabajador LEFT JOIN despachopersonal ON `trabajador`.idTrabajador = `despachopersonal`.idTrabajador AND `despachopersonal`.fchDespacho = '$fchDespacho' LEFT JOIN despacho ON `despachopersonal`.fchDespacho = `despacho`.fchDespacho AND `despachopersonal`.correlativo = `despacho`.correlativo AND (`despacho`.hraInicio <= '$hraDespacho' AND `despacho`.hraFin >= '$hraDespacho') WHERE  (tipoTrabajador = 'Auxiliar' OR tipoTrabajador = 'Conductor' OR tipoTrabajador = 'Coordinador') AND estadoTrabajador = 'Activo' GROUP BY trabajador.`idTrabajador`  ORDER BY apPaterno, apMaterno ASC");
	$consulta->execute();
	return $consulta->fetchAll();
};

function buscarAuxiliaresPredictivo($db,$fchDespacho,$hraDespacho,$placa){
	$consulta = $db->prepare("SELECT trabajador.`idTrabajador`, `estadoTrabajador`, `apPaterno`, `apMaterno`, `nombres`, `despachopersonal`.tipoRol, `despacho`.hraInicio, `despacho`.hraFin FROM predictivotrabajador, trabajador LEFT JOIN despachopersonal ON `trabajador`.idTrabajador = `despachopersonal`.idTrabajador AND `despachopersonal`.fchDespacho = '$fchDespacho' LEFT JOIN despacho ON `despachopersonal`.fchDespacho = `despacho`.fchDespacho AND `despachopersonal`.correlativo = `despacho`.correlativo AND (`despacho`.hraInicio <= '$hraDespacho' AND `despacho`.hraFin >= '$hraDespacho') WHERE  `trabajador`.idTrabajador = `predictivotrabajador`.idTrabajador AND  (tipoTrabajador = 'Auxiliar' OR tipoTrabajador = 'Conductor' OR tipoTrabajador = 'Coordinador') AND estadoTrabajador = 'Activo' AND `predictivotrabajador`.placa = :placa GROUP BY trabajador.`idTrabajador` ORDER BY apPaterno, apMaterno ASC");
	$consulta->bindParam(':placa',$placa);
	$consulta->execute();
	return $consulta->fetchAll();
};

function buscarTrabajadorUltimaAltaActiva($db,$dni){
  $consulta = $db->prepare("SELECT * FROM `altasbajas` WHERE `idTrabajador` = :dni AND `fchBaja` is null limit 1");
  $consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll(); 
}

function buscarTodasLasCuentas($db){
  $consulta = $db->prepare("SELECT * FROM `cuentacontab` WHERE tipocuenta = 'PLA' ORDER BY orden");
	$consulta->execute();
	return $consulta->fetchAll();
};

//parece que no se va a usar
function buscarVariablesPlanilla($db){
  $consulta = $db->prepare("SELECT  `identificador`, `tipo`, `valor`, `descripcion` FROM `auxiliar` WHERE `tipo` LIKE 'PLA'");
	$consulta->execute();
	return $consulta->fetchAll();
};
//parece que no se va a usar

function buscarInfoAfp($db){
  $consulta = $db->prepare("SELECT `nombre`, `porcent`, `porcentMixto`, `comisionFlujo`, `comisionMixta`, `primaSeg`, `porcentObligat` FROM `pension`");
	$consulta->execute();
	return $consulta->fetchAll();
}


function buscarOtros($db,$dni,$mes,$anhio,$quin){
if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `idTrabajador`, `fchPago`, sum(if(`tipoItem`= 'Gratificacion',`monto`,0)) AS pagoGratificacion, sum(if(`tipoItem`= 'Vacaciones',`monto`,0)) AS pagoVacaciones  , sum(if(`tipoItem`= 'CTS',`monto`,0)) AS pagoCts, sum(if(`tipoItem`= 'Pension',`monto`,0)) AS pagoPension , sum(if(`tipoItem`= 'EsSalud',`monto`,0)) AS pagoEsSalud, sum(if(`tipoItem`= 'Prestamo',`monto`,0)) AS reciboPrestamo, sum(if(`tipoItem`= 'Reembolso',`monto`,0)) AS reembolso, sum(if(`tipoItem`= 'Bono',`monto`,0)) AS bono , sum(if(`tipoItem`= 'Movilidad',`monto`,0)) AS movilidad , sum(if(`tipoItem`= 'Adelanto',`monto`,0)) AS adelanto , sum(if(`tipoItem`= 'Indemnizacion',`monto`,0)) AS pagoIndemnizacion , sum(if(`tipoItem`= 'Participacion',`monto`,0)) AS pagoParticipacion , sum(if(`tipoItem`= 'AdelantoQ',`monto`,0)) AS pagoAdelantoQ   FROM `prestamo` WHERE `fchQuincena` = :fchQuincena AND  `entregado`= 'Md' AND `idTrabajador` = :dni ");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':fchQuincena',$fchQuincena);
  $consulta->execute();
  return $consulta->fetchAll();
}



/*function buscarOtros($db,$dni,$mes,$anhio){
  $consulta = $db->prepare("SELECT `anho`, `mes`, `cuenta`, `idTrabajador`, `monto`  FROM `planilla` WHERE `anho` = :anhio AND `mes` = :mes AND `idTrabajador` = :dni");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':mes',$mes);
  $consulta->bindParam(':anhio',$anhio);
	$consulta->execute();
	return $consulta->fetchAll();  
}*/

function buscarDespachosTrabajador($db,$dni,$mes,$anhio,$quin){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `despachopersonal`.`fchDespacho`, `despachopersonal`.`correlativo`, `despachopersonal`.`idTrabajador`, `despachopersonal`.`valorRol`, `despachopersonal`.`valorAdicional`, `despachopersonal`.`tipoRol`,  `despacho`.`placa`, `cliente`.`nombre`,  `despachopersonal`.`pagado`,  `despacho`.`cuenta`,  `cliente`.`idRuc`,  `despachopersonal`.`anhio`,  `despachopersonal`.`mes`    FROM `despachopersonal`, `despacho`, cliente WHERE `despachopersonal`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachopersonal`.`correlativo` = `despacho`.`correlativo` AND  `despacho`.`idCliente` = `cliente`.`idRuc`  AND  `despachopersonal`.`idTrabajador` = :dni AND ( (`despachopersonal`.`pagado` = 'Md' AND fchPago = '$quincena' ) OR `despachopersonal`.`pagado` = 'No')  ");
  $consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarDespachosTrabajadorMarcado($db,$dni,$mes,$anhio,$quin){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `despachopersonal`.`fchDespacho`, `despachopersonal`.`correlativo`, `despachopersonal`.`idTrabajador`, `despachopersonal`.`valorRol`, `despachopersonal`.`valorAdicional`, `despachopersonal`.`tipoRol`,  `despacho`.`placa`, `cliente`.`nombre`,  `despachopersonal`.`pagado`,  `despacho`.`cuenta`, `despacho`.`hraInicio`,  `despacho`.`hraFin`,    `cliente`.`idRuc`,  `despachopersonal`.`anhio`,  `despachopersonal`.`mes`    FROM `despachopersonal`, `despacho`, `cliente` WHERE `despachopersonal`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachopersonal`.`correlativo` = `despacho`.`correlativo` AND  `despacho`.`idCliente` = `cliente`.`idRuc`  AND  `despachopersonal`.`idTrabajador` = :dni AND `despachopersonal`.`pagado` = 'Md' AND fchPago = '$quincena' ");
  $consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarOcurrenciasTrabajadorMarcado($db,$dni,$mes,$anhio,$quin,$tipoOcurr){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `ocurrencia`.`descripcion`, `ocurrenciaconsulta`.`montoTotal`, `nroCuota`, `nroCuotas`, `montoCuota`  FROM `ocurrencia` , `ocurrenciaconsulta` WHERE `ocurrencia`.`fchDespacho` = `ocurrenciaconsulta`.`fchDespacho` AND `ocurrencia`.`correlativo` = `ocurrenciaconsulta`.`correlativo` AND `ocurrencia`.`tipoOcurrencia` = `ocurrenciaconsulta`.`tipoOcurrencia`  AND `ocurrencia`.`descripcion` = `ocurrenciaconsulta`.`descripcion`  AND `ocurrencia`.`montoTotal` = `ocurrenciaconsulta`.`montoTotal`   AND  `pagado` = 'Md' AND `ocurrenciaconsulta`.`tipoOcurrencia` = :tipoOcurr  AND `idTrabajador` = :dni  AND `fchQuincena` = '$quincena' ");
  
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':tipoOcurr',$tipoOcurr);
	$consulta->execute();
	return $consulta->fetchAll();
}


function buscarOcurrenciasTrabajadorSi($db,$dni,$mes,$anhio,$quin,$tipoOcurr){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `ocurrencia`.`descripcion`, `ocurrenciaconsulta`.`montoTotal`, `nroCuota`, `nroCuotas`, `montoCuota`  FROM `ocurrencia` , `ocurrenciaconsulta` WHERE `ocurrencia`.`fchDespacho` = `ocurrenciaconsulta`.`fchDespacho` AND `ocurrencia`.`correlativo` = `ocurrenciaconsulta`.`correlativo` AND `ocurrencia`.`tipoOcurrencia` = `ocurrenciaconsulta`.`tipoOcurrencia`  AND `ocurrencia`.`descripcion` = `ocurrenciaconsulta`.`descripcion`  AND `ocurrencia`.`montoTotal` = `ocurrenciaconsulta`.`montoTotal`   AND  `pagado` = 'Si' AND `ocurrenciaconsulta`.`tipoOcurrencia` = :tipoOcurr  AND `idTrabajador` = :dni  AND `fchQuincena` = '$quincena' ");
  
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':tipoOcurr',$tipoOcurr);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarVariosTrabajadorMarcado($db,$dni,$mes,$anhio,$quin,$tipoItem){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `prestamodetalle`.`monto`, `prestamo`.`descripcion`, `nroCuotas`, `nroCuota`, `prestamodetalle`.`idTrabajador`, `prestamodetalle`.`tipoItem`, `prestamodetalle`.`fchPago`, `montoCuota`  FROM `prestamodetalle`,`prestamo`  WHERE  `prestamodetalle`.`idTrabajador` = `prestamo`.`idTrabajador`  AND `prestamodetalle`.`fchCreacion` = `prestamo`.`fchCreacion` AND `prestamodetalle`.`monto` = `prestamo`.`monto` AND `prestamodetalle`.`tipoItem` = `prestamo`.`tipoItem`   AND  `prestamodetalle`.`idTrabajador` = :dni AND `prestamodetalle`.`tipoItem` = :tipoItem AND `prestamodetalle`.`fchQuincena` = '$quincena' AND `pagado` = 'Md'");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':tipoItem',$tipoItem);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarVariosTrabajadorSi($db,$dni,$mes,$anhio,$quin,$tipoItem){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `prestamodetalle`.`monto`, `prestamo`.`descripcion`, `nroCuotas`, `nroCuota`, `prestamodetalle`.`idTrabajador`, `prestamodetalle`.`tipoItem`, `prestamodetalle`.`fchPago`, `montoCuota`  FROM `prestamodetalle`,`prestamo`  WHERE  `prestamodetalle`.`idTrabajador` = `prestamo`.`idTrabajador`  AND `prestamodetalle`.`fchCreacion` = `prestamo`.`fchCreacion` AND `prestamodetalle`.`monto` = `prestamo`.`monto` AND `prestamodetalle`.`tipoItem` = `prestamo`.`tipoItem`   AND  `prestamodetalle`.`idTrabajador` = :dni AND `prestamodetalle`.`tipoItem` = :tipoItem AND `prestamodetalle`.`fchQuincena` = '$quincena' AND `pagado` = 'Si'");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':tipoItem',$tipoItem);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarVariosUnicoTrabajadorMarcado($db,$dni,$mes,$anhio,$quin,$tipoItem){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  if($tipoItem == 'Varios'){//Esta parte se debe arreglar 
    $consulta = $db->prepare("SELECT `descripcion`, `nroCuotas`, `monto` FROM `prestamo`  WHERE  `idTrabajador` = :dni AND (`prestamo`.`tipoItem` = 'xx' OR `tipoItem` = 'yy'  ) AND `fchQuincena` = '$quincena' AND `entregado` = 'Md'");
    $consulta->bindParam(':dni',$dni);
  	$consulta->execute();
  
  } else if ($tipoItem == 'Pension'){
  	$consulta = $db->prepare("SELECT `prestamo`.`descripcion`, `nroCuotas`, `monto` FROM `prestamo`  WHERE  `idTrabajador` = :dni AND `prestamo`.`tipoItem` IN ('Pension','PnsNacONP','PnsPriComis','PnsPriPrima','PnsPriAport')  AND `prestamo`.`fchQuincena` = '$quincena' AND `entregado` = 'Md'");
    $consulta->bindParam(':dni',$dni);
  	$consulta->execute();

  } else if ($tipoItem == 'HraExtra'){
  	$consulta = $db->prepare("SELECT  concat(fchDespacho,' - ',correlativo, ' - Hora Extra Calculada') AS  `descripcion`, '1' AS `nroCuotas`, valorHraExtra AS `monto` FROM `despachopersonal` WHERE  `idTrabajador` = :dni AND `fchPago` = '$quincena' AND valorHraExtra > 0 AND `pagado` = 'Md'");

  	//$consulta = $db->prepare("SELECT `prestamo`.`descripcion`, `nroCuotas`, `monto` FROM `prestamo`  WHERE  `idTrabajador` = :dni AND `prestamo`.`tipoItem` IN ('Pension','PnsNacONP','PnsPriComis','PnsPriPrima','PnsPriAport')  AND `prestamo`.`fchQuincena` = '$quincena' AND `entregado` = 'Md'");
    $consulta->bindParam(':dni',$dni);
  	$consulta->execute();

  } else {
    $consulta = $db->prepare("SELECT `prestamo`.`descripcion`, `nroCuotas`, `monto` FROM `prestamo`  WHERE  `idTrabajador` = :dni AND `prestamo`.`tipoItem` = :tipoItem AND `prestamo`.`fchQuincena` = '$quincena' AND `entregado` = 'Md'");
    $consulta->bindParam(':dni',$dni);
    $consulta->bindParam(':tipoItem',$tipoItem);
  	$consulta->execute();
	}
	return $consulta->fetchAll();
}

function buscarVariosUnicoTrabajadorSi($db,$dni,$mes,$anhio,$quin,$tipoItem){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  if($tipoItem == 'Varios'){
    $consulta = $db->prepare("SELECT `descripcion`, `nroCuotas`, `monto` FROM `prestamo`  WHERE  `idTrabajador` = :dni AND (`prestamo`.`tipoItem` = 'devGarantia' OR `tipoItem` = 'DomFeriado'  ) AND `fchQuincena` = '$quincena' AND `entregado` = 'Si'");
    $consulta->bindParam(':dni',$dni);
  	$consulta->execute();
  
  } else {
    $consulta = $db->prepare("SELECT `prestamo`.`descripcion`, `nroCuotas`, `monto` FROM `prestamo`  WHERE  `idTrabajador` = :dni AND `prestamo`.`tipoItem` = :tipoItem AND `prestamo`.`fchQuincena` = '$quincena' AND `entregado` = 'Si'");
    $consulta->bindParam(':dni',$dni);
    $consulta->bindParam(':tipoItem',$tipoItem);
  	$consulta->execute();
	}
	return $consulta->fetchAll();
}

function buscarPagosPendientesTrabajador($db,$dni,$mes,$anhio,$quin){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `ocurrenciaconsulta`.`fchDespacho`, `ocurrenciaconsulta`.`correlativo`, `ocurrenciaconsulta`.`montoTotal`, `nroCuota`, `idTrabajador`, `fchPago`, `montoCuota`, `anhio`, `mes`, `pagado`, `ocurrenciaconsulta`.`tipoOcurrencia`, `ocurrencia`.`descripcion`   FROM `ocurrencia`, `ocurrenciaconsulta` WHERE `ocurrencia`.`fchDespacho` = `ocurrenciaconsulta`.`fchDespacho` AND `ocurrencia`.`correlativo` = `ocurrenciaconsulta`.`correlativo` AND `ocurrencia`.`tipoOcurrencia` = `ocurrenciaconsulta`.`tipoOcurrencia` AND `ocurrencia`.`descripcion` = `ocurrenciaconsulta`.`descripcion` AND `ocurrencia`.`montoTotal` = `ocurrenciaconsulta`.`montoTotal`    AND ( `ocurrenciaconsulta`.`tipoOcurrencia` = 'Caja Chica' OR `ocurrenciaconsulta`.`tipoOcurrencia` = 'Movilidad'  OR `ocurrenciaconsulta`.`tipoOcurrencia` = 'Descuento' OR `ocurrenciaconsulta`.`tipoOcurrencia` = 'Devolucion'  OR `ocurrenciaconsulta`.`tipoOcurrencia` = 'Gastos Varios' ) AND `idTrabajador` = :dni    AND (`pagado` = 'No' OR (`pagado` = 'Md'  AND `fchQuincena` = '$quincena'))");
  $consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarPrestamosPendientesTrabajador($db,$dni,$mes,$anhio,$quin){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';  
  $consulta = $db->prepare("SELECT `prestamodetalle`.`monto`, `nroCuota`, `prestamodetalle`.`idTrabajador`, `prestamodetalle`.`fchPago`, `montoCuota`, `prestamodetalle`.`fchCreacion`, `prestamodetalle`.`anhio`, `prestamodetalle`.`mes`, `pagado`, `prestamodetalle`.`tipoItem`, `prestamodetalle`.`descripcion`, `prestamo`.`codigo` FROM `prestamodetalle`, `prestamo` WHERE `prestamodetalle`.`idTrabajador` = `prestamo`.`idTrabajador` AND `prestamodetalle`.`descripcion` = `prestamo`.`descripcion` AND  `prestamodetalle`.`tipoItem` = `prestamo`.`tipoItem` AND `prestamodetalle`.`monto` = `prestamo`.`monto` AND `prestamodetalle`.`fchCreacion` = `prestamo`.`fchCreacion`  AND `prestamodetalle`.`idTrabajador` = :dni AND ((`pagado` = 'No' )   or (`pagado` = 'Md' AND `prestamodetalle`.`fchQuincena` = '$quincena'))");
  $consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarPrestamosPedidosTrabajador($db,$dni,$mes,$anhio,$quin,$dataItemsTrabajador = NULL){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `idTrabajador`, `descripcion`, `monto`, `nroCuotas`, `entregado` , `fchCreacion`, `fchPago`, `tipoItem` FROM `prestamo` WHERE `idTrabajador` = :dni AND tipoitem IN ($dataItemsTrabajador)  AND (entregado = 'No' OR (`entregado` = 'Md' AND `fchQuincena` = '$quincena')) ");
  $consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarHorasExtraTrabajador($db, $dni, $mes, $anhio, $quin){
	$quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT fchDespacho, 'HraExtra' As tipoItem,  valorHraExtra, concat( fchDespacho, ' ', correlativo ) AS descripcion, fchPago, pagado  FROM `despachopersonal` WHERE valorHraExtra >0 and `idTrabajador` LIKE :dni AND (`pagado` = 'No' or ( pagado = 'Md' AND fchPago = '$quincena' )) ");
  $consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarBonoDiarioTrabajador($db,$dni,$mes,$anhio,$quin){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `idTrabajador`, `descripcion`, `monto`, `nroCuotas`, `entregado` , `fchCreacion` FROM `prestamo` WHERE `idTrabajador` = :dni AND tipoitem = 'BonoDiario' AND `entregado` = 'Md' AND `fchQuincena` = '$quincena' ");
  $consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll();
}


function buscarTodosLosPrestamosPendientesTrabajador($db,$dni){
  $consulta = $db->prepare("SELECT  `prestamo`.`descripcion`, `prestamo`.`fchCreacion`, `prestamodetalle`.`monto`, `nroCuota`, `prestamodetalle`.`idTrabajador`, `prestamodetalle`.`fchPago`, `montoCuota` FROM `prestamodetalle`,  `prestamo`  WHERE `prestamodetalle`.`idTrabajador` =   `prestamo`.`idTrabajador`  AND `prestamodetalle`.`tipoItem` = `prestamo`.`tipoItem` AND `prestamodetalle`.`descripcion` =   `prestamo`.`descripcion` AND `prestamodetalle`.`monto` =   `prestamo`.`monto` AND `prestamodetalle`.`idTrabajador` = :dni AND `pagado` = 'No'");
  $consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarPrestamosPendientesTrabajadorConfirmados($db,$dni,$mes,$anhio){
  $consulta = $db->prepare("SELECT `monto`, `nroCuota`, `idTrabajador`, `fchPago`, `montoCuota`,`fchCreacion`, `anhio`, `mes`, `pagado` FROM `prestamodetalle` WHERE  `idTrabajador` = :dni  AND `pagado` = 'Md' AND anhio = :anhio AND mes = :mes ");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':mes',$mes);
  $consulta->bindParam(':anhio',$anhio);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarPrestamosPedidosTrabajadorConfirmados($db,$dni,$mes,$anhio){
  $consulta = $db->prepare("SELECT `idTrabajador`, `descripcion`, `monto`, `nroCuotas`, `fchPago`, `fchCreacion` FROM `prestamo` WHERE `idTrabajador` = :dni AND entregado = 'Md' AND anhio = :anhio AND mes = :mes");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':mes',$mes);
  $consulta->bindParam(':anhio',$anhio);
	$consulta->execute();
	return $consulta->fetchAll();                               
}

function buscarDespachosTrabajadorConfirmados($db,$dni,$mes,$anhio){ //revisar
  $consulta = $db->prepare("SELECT `despachopersonal`.`fchDespacho`, `despachopersonal`.`correlativo`, `despachopersonal`.`idTrabajador`, `despachopersonal`.`valorRol`, `despachopersonal`.`valorAdicional`, `despachopersonal`.`tipoRol`,  `despacho`.`placa`, `cliente`.`nombre`,  `despachopersonal`.`pagado`, anhio, mes FROM `despachopersonal`, `despacho`, cliente WHERE `despachopersonal`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachopersonal`.`correlativo` = `despacho`.`correlativo` AND  `despacho`.`idCliente` = `cliente`.`idRuc`  AND  `despachopersonal`.`idTrabajador` = :dni AND ( (`despachopersonal`.`mes` = :mes  AND `despachopersonal`.`anhio` = :anhio) AND `despachopersonal`.`pagado` = 'Md')      ");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':mes',$mes);
  $consulta->bindParam(':anhio',$anhio);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarPagosPendientesTrabajadorConfirmados($db,$dni,$mes,$anhio,$tipoOcurrencia){
  $consulta = $db->prepare("SELECT `ocurrenciaconsulta`.`fchDespacho`, `ocurrenciaconsulta`.`correlativo`, `ocurrenciaconsulta`.`montoTotal`, `nroCuota`, `idTrabajador`, `fchPago`, `montoCuota`, `anhio`, `mes`, `pagado`, `tipoOcurrencia`  FROM `ocurrenciaconsulta` WHERE  `tipoOcurrencia` = :tipoOcurrencia AND `idTrabajador` = :dni  AND `pagado` = 'Md' AND anhio = :anhio AND mes = :mes");
  //$consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `montoTotal`, `nroCuota`, `idTrabajador`, `fchPago`, `montoCuota`, `anhio`, `mes`, `pagado` FROM `ocurrenciaconsulta` WHERE tipo = 'pago' AND  `idTrabajador` = :dni AND `pagado` = 'No' AND ((YEAR(`fchPago`) <  :anhio AND MONTH(`fchPago`) < :mes  ) or (YEAR(`fchPago`) =  :anhio AND MONTH(`fchPago`) = :mes  )) ");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':mes',$mes);
  $consulta->bindParam(':anhio',$anhio);
  $consulta->bindParam(':tipoOcurrencia',$tipoOcurrencia);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarOcurrenciasAgrupadas($db,$dni,$mes,$anhio){
  $consulta = $db->prepare("SELECT count(*) as Cantidad, sum(`montoTotal`) as monto ,`tipoOcurrencia`, `montoTotal`  FROM `ocurrenciaconsulta` WHERE `idTrabajador` = :dni AND `anhio`= :anhio AND `mes` = :mes AND `pagado` = 'Md'  group by `tipoOcurrencia`");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':mes',$mes);
  $consulta->bindParam(':anhio',$anhio);
  $consulta->execute();
	return $consulta->fetchAll();
}

function buscarPrestamosAgrupados($db,$dni,$mes,$anhio){
  $consulta = $db->prepare("SELECT count(*) as cantidad,  `monto`, `nroCuota`, `idTrabajador`, `fchPago`, sum(`montoCuota`) as montoT , `anhio`, `mes`, `pagado`FROM `prestamodetalle` WHERE `idTrabajador` = :dni AND `anhio` = :anhio AND `mes`= :mes AND `pagado` = 'Md'");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':mes',$mes);
  $consulta->bindParam(':anhio',$anhio);
  $consulta->execute();
	return $consulta->fetchAll();
}

function buscarDatosPlanillaAnhioMes($db,$dni,$anhio,$mes){
  $consulta = $db->prepare("SELECT `anho`, `mes`, `planilla`.`cuenta`, `idTrabajador`, `monto`, `columna`, `descripcion` FROM `planilla`, `cuentacontab`   WHERE `planilla`.`cuenta` = `cuentacontab`.`cuenta` AND  `anho`= :anhio AND  `mes` = :mes AND `idTrabajador` = :dni order by orden");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':mes',$mes);
  $consulta->bindParam(':anhio',$anhio);
	$consulta->execute();
	return $consulta->fetchAll();  
};

function addDatoPlanilla($db,$anhio,$mes,$idTrabajador,$cuenta,$monto,$usuario){
  $actualiza = $db->prepare("UPDATE `planilla` SET `monto` = :monto WHERE `anho` = :anhio AND `mes` = :mes AND `cuenta` = :cuenta AND `idTrabajador` = :idTrabajador");
  $actualiza->bindParam(':monto',$monto);
  $actualiza->bindParam(':anhio',$anhio);
  $actualiza->bindParam(':mes',$mes);
  $actualiza->bindParam(':cuenta',$cuenta);
  $actualiza->bindParam(':idTrabajador',$idTrabajador);
  $actualiza->execute();     
  $cont = $actualiza->rowCount();
  if($cont == 0){
    $insertar = $db->prepare("INSERT INTO `planilla` (`anho`, `mes`, `cuenta`, `idTrabajador`, `monto`, `usuario`, `fchCreacion`) VALUES (:anhio, :mes, :cuenta, :idTrabajador, :monto, :usuario, CURDATE())");
    $insertar->bindParam(':monto',$monto);
    $insertar->bindParam(':anhio',$anhio);
    $insertar->bindParam(':mes',$mes);
    $insertar->bindParam(':cuenta',$cuenta);
    $insertar->bindParam(':idTrabajador',$idTrabajador);
    $insertar->bindParam(':usuario',$usuario);
    $insertar->execute();
    return 1;
  }
}


function addDato($db,$anhio,$mes,$idTrabajador,$tipo,$descripcion,$monto,$quincena,$usuario){
  $actualiza = $db->prepare("UPDATE `prestamo` SET `monto` = :monto, `entregado` = 'Md', `fchPago`= :fchPago, `fchQuincena`= :fchPago  WHERE `anhio` = :anhio AND `mes` = :mes AND `tipoItem` = :tipo AND `idTrabajador` = :idTrabajador");
  $actualiza->bindParam(':monto',$monto);
  $actualiza->bindParam(':anhio',$anhio);
  $actualiza->bindParam(':mes',$mes);
  $actualiza->bindParam(':tipo',$tipo);
  $actualiza->bindParam(':fchPago',$quincena);
  
  $actualiza->bindParam(':idTrabajador',$idTrabajador);
  //$actualiza->bindParam(':fchPago',$quincena);
  $actualiza->execute();     
  $cont = $actualiza->rowCount();
  if($cont == 0){
    $insertar = $db->prepare("INSERT INTO `prestamo` (`anhio`, `mes`, `tipoItem`, `descripcion`,`idTrabajador`, `entregado`, `monto`, `fchPago`,`fchQuincena`, `usuario`, `fchCreacion`) VALUES (:anhio, :mes, :tipo,:descripcion, :idTrabajador,'Md', :monto, :fchPago, :fchPago ,:usuario, CURDATE())");
    $insertar->bindParam(':monto',$monto);
    $insertar->bindParam(':anhio',$anhio);
    $insertar->bindParam(':mes',$mes);
    $insertar->bindParam(':tipo',$tipo);
    $insertar->bindParam(':fchPago',$quincena);
    $insertar->bindParam(':descripcion',$descripcion);
    $insertar->bindParam(':idTrabajador',$idTrabajador);
    $insertar->bindParam(':usuario',$usuario);
    $insertar->execute();
    return 1;
  }
}

function buscarDetalleTrabajoTrabajador($db,$fchIni,$fchFin,$correl,$trabajador,$placa,$cliente,$tipoRol,$orden,$pagado,$cuenta,$guiaPorte,$valorRol,$valAdic,$usaReten,$observ,$limite){
  $where = "";
  $limite = str_replace(";","",$limite);
  $limite = str_replace("'","",$limite);
  if($correl)   $where .= " AND `despacho`.`correlativo` = :correl ";
  if($placa)    $where .= " AND `despacho`.`placa` = :placa ";
  if($cliente)  $where .= " AND `cliente`.`idRuc` = :cliente ";
  if($pagado)   $where .= " AND `despachopersonal`.`pagado` = :pagado ";
  if($valorRol) $where .= " AND `valorRol` = :valorRol ";
  if($valAdic)  $where .= " AND `valorAdicional` = :valAdic ";
  if($usaReten) $where .= " AND `usaReten`= despachopersonal.idTrabajador ";
  if($observ)   $where .= " AND `observacion` Like :observ ";
  
  if($limite)  
    $whereLimite = "LIMIT $limite";
  else
    $whereLimite = '';

  if($trabajador) $where .= " AND `trabajador`.`idTrabajador` = :idTrabajador ";      
  if($tipoRol)    $where .= " AND `despachopersonal`.`tipoRol` = :tipoRol ";
  if($cuenta)     $where .= " AND `despacho`.`cuenta` like :cuenta ";
  if($guiaPorte){
    $fromGuiaPorte = " , `despachoguiaporte` ";
    $whereGuiaPorte = "  AND `despacho`.`fchDespacho` = `despachoguiaporte`.`fchDespacho` AND `despacho`.`correlativo` = `despachoguiaporte`.`correlativo` AND `despachoguiaporte`.`guiaPorte` = :guiaPorte ";
  } else {
    $fromGuiaPorte = '';
    $whereGuiaPorte = '';
  }
  
  if($orden == 'placa' ){
    $orden = " ORDER BY `despacho`.`placa` ";
  } else if($orden == 'cuenta' ){
    $orden = " ORDER BY `despacho`.`cuenta` ";
  } else if($orden == 'fecha' ){
    $orden = " ORDER BY `despacho`.`fchDespacho` ";
  } else {
    $orden = "order by `despachopersonal`.fchDespacho, `despachopersonal`.correlativo, `despachopersonal`.tipoRol Desc ";
  }     
        
  //$consulta = $db->prepare("SELECT `despachopersonal`.`fchDespacho`, `despachopersonal`.`correlativo`, `nombre`, `cuenta`,`hraInicio`, `hraFin` AS `hraFinMoy`, if (hraFinCliente = '00:00:00', `despacho`.`hraFin`, `hraFinCliente`  ) AS `hraFin`, `placa`, `despacho`.`kmInicio` AS kmIniMoy, `despacho`.`kmFin` AS `kmFinMoy`, if (`kmInicioCliente` >0, `kmInicioCliente`, `kmInicio` ) As `kmInicio`, if (`kmFinCliente` >0, `kmFinCliente`, kmFin ) As kmFin, if (kmFinCliente >0, kmFinCliente , kmFin ) -if (kmInicioCliente >0, kmInicioCliente , kmInicio ) as Recorrido, concat(apPaterno,' ',apMaterno,' ',nombres) as nombreCompleto, `idCliente`, valorRol, valorAdicional, tipoRol, `despachopersonal`.`idTrabajador`, `despachopersonal`.`pagado`,despachopersonal.`fchPago`, `despacho`.`observacion`, `despacho`.`recorridoEsperado`, `guiaCliente`, `usaReten`, `trabFchDespachoFin`, `trabHraInicio`, `trabHraFin` , `prestamo`.descripcion,  `prestamo`.monto FROM `despacho`, `trabajador`, `cliente`, `despachopersonal` LEFT JOIN prestamo ON `prestamo`.idTrabajador = `despachopersonal`.idTrabajador AND `tipoItem` = 'HraExtra' AND trim(right(descripcion,13)) = concat(`despachopersonal`.fchDespacho,'-',`despachopersonal`.correlativo )  $fromGuiaPorte  WHERE  `cliente`.`idRuc` =`despacho`.`idCliente` AND `despacho`.`fchDespacho` = `despachopersonal`.`fchDespacho` AND `despacho`.`correlativo` = `despachopersonal`.`correlativo` AND `despachopersonal`.`idTrabajador` =`trabajador`.`idTrabajador`  AND `despachopersonal`.`fchDespacho` >= :fchIni AND `despachopersonal`.`fchDespacho` <= :fchFin AND  `despachopersonal`.`tipoRol` != 'Coordinador' $where $whereGuiaPorte $orden $whereLimite");

  $consulta = $db->prepare("SELECT `despachopersonal`.`fchDespacho`, `despachopersonal`.`correlativo`, `nombre`, `cuenta`,`hraInicio`, `hraFin` AS `hraFinMoy`, `hraInicioBase`, if (hraFinCliente = '00:00:00', `despacho`.`hraFin`, `hraFinCliente`  ) AS `hraFin`, `placa`, `despacho`.`kmInicio` AS kmIniMoy, `despacho`.`kmFin` AS `kmFinMoy`, if (`kmInicioCliente` >0, `kmInicioCliente`, `kmInicio` ) As `kmInicio`, if (`kmFinCliente` >0, `kmFinCliente`, kmFin ) As kmFin, if (kmFinCliente >0, kmFinCliente , kmFin ) -if (kmInicioCliente >0, kmInicioCliente , kmInicio ) as Recorrido, concat(apPaterno,' ',apMaterno,' ',nombres) as nombreCompleto, `idCliente`, valorRol, valorAdicional, tipoRol, `despachopersonal`.`idTrabajador`, `despachopersonal`.`pagado`,despachopersonal.`fchPago`, `despacho`.`observacion`, `despacho`.`recorridoEsperado`, `guiaCliente`, `usaReten`, `trabFchDespachoFin`, `trabHraInicio`, `trabHraFin`, valorHraExtra AS  monto, `despacho`.m3 FROM `despacho`, `trabajador`, `cliente`, `despachopersonal` $fromGuiaPorte  WHERE  `cliente`.`idRuc` =`despacho`.`idCliente` AND `despacho`.`fchDespacho` = `despachopersonal`.`fchDespacho` AND `despacho`.`correlativo` = `despachopersonal`.`correlativo` AND `despachopersonal`.`idTrabajador` =`trabajador`.`idTrabajador`  AND `despachopersonal`.`fchDespacho` >= :fchIni AND `despachopersonal`.`fchDespacho` <= :fchFin AND  `despachopersonal`.`tipoRol` != 'Coordinador' $where $whereGuiaPorte $orden $whereLimite");


  $consulta->bindParam(':fchIni',$fchIni);
  $consulta->bindParam(':fchFin',$fchFin);
  if($correl) $consulta->bindParam(':correl',$correl);
  if($placa)  $consulta->bindParam(':placa',$placa);
  if($cliente)$consulta->bindParam(':cliente',$cliente);
  if($trabajador) $consulta->bindParam(':idTrabajador',$trabajador);
  if($tipoRol)$consulta->bindParam(':tipoRol',$tipoRol);
  if($pagado) $consulta->bindParam(':pagado',$pagado);
  if($valorRol) $consulta->bindParam(':valorRol',$valorRol);
  if($valAdic)  $consulta->bindParam(':valAdic',$valAdic);
  if($cuenta) $consulta->bindParam(':cuenta',$cuenta);
  if($guiaPorte) $consulta->bindParam(':guiaPorte',$guiaPorte);
  if($observ){
    $observ = "%".$observ."%";
    $consulta->bindParam(':observ',$observ);
  } 
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarDespachosTrabajadorPagados($db,$dni,$mes,$anhio,$quin){ //revisar
  $whereQuincena = ($quin==1)?" AND fchPago <= '$anhio-$mes-15' ":" AND  fchPago > '$anhio-$mes-15'  ";
  $consulta = $db->prepare("SELECT `despachopersonal`.`fchDespacho`, `despachopersonal`.`correlativo`, `despachopersonal`.`idTrabajador`, `despachopersonal`.`valorRol`, `despachopersonal`.`valorAdicional`, `despachopersonal`.`tipoRol`,  `despacho`.`placa`, `cliente`.`nombre`, `despacho`.`cuenta`, `despacho`.`hraInicio`,  `despacho`.`hraFin`, `despachopersonal`.`pagado` FROM `despachopersonal`, `despacho`, `cliente` WHERE `despachopersonal`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachopersonal`.`correlativo` = `despacho`.`correlativo` AND  `despacho`.`idCliente` = `cliente`.`idRuc`  AND  `despachopersonal`.`idTrabajador` = :dni AND (`despachopersonal`.`mes` = :mes  AND `despachopersonal`.`anhio` = :anhio) AND `despachopersonal`.`pagado` = 'Si'  $whereQuincena  ");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':mes',$mes);
  $consulta->bindParam(':anhio',$anhio);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarPagosPendientesTrabajadorPagados($db,$dni,$mes,$anhio,$quin){
  $whereQuincena = ($quin==1)?"  AND fchQuincena = '$anhio-$mes-14' ":" AND  fchQuincena =  '$anhio-$mes-28'   ";
  $consulta = $db->prepare("SELECT `ocurrenciaconsulta`.`fchDespacho`, `ocurrenciaconsulta`.`correlativo`, `ocurrenciaconsulta`.`montoTotal`, `nroCuota`, `idTrabajador`, `fchPago`, `montoCuota`, `anhio`, `mes`, `pagado`, `tipoOcurrencia`, `descripcion`  FROM `ocurrenciaconsulta` WHERE ( `tipoOcurrencia` = 'Caja Chica' OR `tipoOcurrencia` = 'Movilidad'  OR `tipoOcurrencia` = 'Descuento' OR `tipoOcurrencia` = 'Devolucion'  OR `tipoOcurrencia` = 'Gastos Varios') AND  `idTrabajador` = :dni  AND `pagado` = 'Si' AND anhio = :anhio AND mes = :mes $whereQuincena ");
  //$consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `montoTotal`, `nroCuota`, `idTrabajador`, `fchPago`, `montoCuota`, `anhio`, `mes`, `pagado` FROM `ocurrenciaconsulta` WHERE tipo = 'pago' AND  `idTrabajador` = :dni AND `pagado` = 'No' AND ((YEAR(`fchPago`) <  :anhio AND MONTH(`fchPago`) < :mes  ) or (YEAR(`fchPago`) =  :anhio AND MONTH(`fchPago`) = :mes  )) ");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':mes',$mes);
  $consulta->bindParam(':anhio',$anhio);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarPagosPendientesTrabajadorPagadosPorTipoOcurrencia($db,$dni,$mes,$anhio,$quin,$tipoOcurrencia){
  $whereQuincena = ($quin==1)?" AND fchQuincena = '$anhio-$mes-14' ":" AND  fchQuincena =  '$anhio-$mes-28'  ";
  //echo 
  $consulta = $db->prepare("SELECT `ocurrenciaconsulta`.`fchDespacho`, `ocurrenciaconsulta`.`correlativo`, `ocurrenciaconsulta`.`montoTotal`, `ocurrencia`.`descripcion`, `nroCuota`, `nroCuotas`,`idTrabajador`, `fchPago`, `montoCuota`, `anhio`, `mes`, `pagado`, `ocurrenciaconsulta`.`tipoOcurrencia`  FROM `ocurrenciaconsulta`, `ocurrencia` WHERE `ocurrenciaconsulta`.`fchDespacho` =  `ocurrencia`.`fchDespacho` AND `ocurrenciaconsulta`.`correlativo` =  `ocurrencia`.`correlativo`  AND `ocurrenciaconsulta`.`tipoOcurrencia` =  `ocurrencia`.`tipoOcurrencia` AND `ocurrenciaconsulta`.`descripcion` =  `ocurrencia`.`descripcion` AND `ocurrenciaconsulta`.`montoTotal` =  `ocurrencia`.`montoTotal`  AND   `ocurrenciaconsulta`.`tipoOcurrencia` = :tipoOcurrencia  AND  `idTrabajador` = :dni  AND `pagado` = 'Si' AND anhio = :anhio AND mes = :mes $whereQuincena ");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':mes',$mes);
  $consulta->bindParam(':anhio',$anhio);
  $consulta->bindParam(':tipoOcurrencia',$tipoOcurrencia);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarPrestamosPendientesTrabajadorPagados($db,$dni,$mes,$anhio,$quin){  //porque aqui no se considera el ajuste??
  //$whereQuincena = ($quin==1)?" AND fchPago <= '$anhio-$mes-15' ":" AND  fchPago > '$anhio-$mes-15'  ";
  $whereQuincena = ($quin==1)?" AND `prestamodetalle`.`fchQuincena` = '$anhio-$mes-14' ":" AND  `prestamodetalle`.`fchQuincena` = '$anhio-$mes-28' ";
  $consulta = $db->prepare("SELECT `prestamodetalle`.`monto`, `nroCuota`,`nroCuotas`, `prestamodetalle`.`idTrabajador`, `prestamodetalle`.`fchPago`, `prestamodetalle`.`tipoItem`, `montoCuota`,`prestamodetalle`.`fchCreacion`, `prestamodetalle`.`anhio`, `prestamodetalle`.`mes`, `pagado`, `prestamo`.`descripcion` , `prestamo`.`nroCuotas` FROM `prestamodetalle`, `prestamo`  WHERE `prestamodetalle`.`monto` =`prestamo`.`monto` AND  `prestamodetalle`.`idTrabajador` =`prestamo`.`idTrabajador`  AND  `prestamodetalle`.`descripcion` =`prestamo`.`descripcion`  AND `prestamodetalle`.`fchCreacion` = `prestamo`.`fchCreacion`  AND  `prestamodetalle`.`tipoitem` =`prestamo`.`tipoItem`  AND `prestamodetalle`.`tipoItem` != 'Ajuste' AND `prestamodetalle`.`idTrabajador` = :dni AND `pagado` = 'Si' AND  `prestamodetalle`.anhio= :anhio AND `prestamodetalle`.mes = :mes  $whereQuincena ");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':mes',$mes);
  $consulta->bindParam(':anhio',$anhio);
	$consulta->execute();
	return $consulta->fetchAll();
}


function buscarAjustesPendientesTrabajadorPagados($db,$dni,$mes,$anhio,$quin){
  //$whereQuincena = ($quin==1)?" AND fchPago <= '$anhio-$mes-15' ":" AND  fchPago > '$anhio-$mes-15'  ";
  $whereQuincena = ($quin==1)?" AND `prestamodetalle`.`fchQuincena` = '$anhio-$mes-14' ":" AND  `prestamodetalle`.`fchQuincena` = '$anhio-$mes-28'  ";
  $consulta = $db->prepare("SELECT `prestamodetalle`.`monto`, `nroCuota`, `prestamodetalle`.`idTrabajador`, `prestamodetalle`.`fchPago`, `prestamodetalle`.`tipoItem`, `montoCuota`,`prestamodetalle`.`fchCreacion`, `prestamodetalle`.`anhio`, `prestamodetalle`.`mes`, `pagado`, `prestamo`.`descripcion` , `prestamo`.`nroCuotas` FROM `prestamodetalle`, `prestamo`  WHERE `prestamodetalle`.`monto` =`prestamo`.`monto` AND `prestamodetalle`.`idTrabajador` =`prestamo`.`idTrabajador` AND `prestamodetalle`.`descripcion` =`prestamo`.`descripcion` AND  `prestamodetalle`.`tipoitem` =`prestamo`.`tipoItem`  AND `prestamodetalle`.`tipoItem` = 'Ajuste' AND `prestamodetalle`.`idTrabajador` = :dni AND `pagado` = 'Si' AND  `prestamodetalle`.anhio= :anhio AND `prestamodetalle`.mes = :mes  $whereQuincena ");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':mes',$mes);
  $consulta->bindParam(':anhio',$anhio);
	$consulta->execute();
	return $consulta->fetchAll();
}


function buscarPrestamosPendientesTrabajadorPagadosAjuste($db,$dni,$mes,$anhio,$quin){
  //$whereQuincena = ($quin==1)?" AND fchPago <= '$anhio-$mes-15' ":" AND  fchPago > '$anhio-$mes-15'  ";
  $whereQuincena = ($quin==1)?" AND fchQuincena = '$anhio-$mes-14' ":" AND  fchQuincena = '$anhio-$mes-28'  ";
  $consulta = $db->prepare("SELECT `monto`, `nroCuota`, `idTrabajador`, `fchPago`, `tipoItem`,  `descripcion`,`montoCuota`,`fchCreacion`, `anhio`, `mes`, `pagado` FROM `prestamodetalle` WHERE  `tipoItem` = 'Ajuste' AND `idTrabajador` = :dni AND `pagado` = 'Si' AND  anhio=  :anhio AND mes =  :mes   $whereQuincena ");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':mes',$mes);
  $consulta->bindParam(':anhio',$anhio);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarPrestamosPedidosTrabajadorPagados($db,$dni,$mes,$anhio,$quin,$dataItemsTrabajador = NULL){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `idTrabajador`, `descripcion`, `monto`, `nroCuotas`, `entregado`, `tipoItem`, `fchCreacion` FROM `prestamo` WHERE `idTrabajador` = :dni AND tipoitem IN ($dataItemsTrabajador)  AND entregado = 'Si' AND  `fchQuincena` = '$quincena'");
  $consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll();
}



function buscarHorasExtraTrabajadorPagados($db, $dni, $mes, $anhio, $quin){
	$quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT fchDespacho, 'HraExtra' As tipoItem,  valorHraExtra, concat( fchDespacho, ' ', correlativo ) AS descripcion, fchPago, pagado  FROM `despachopersonal` WHERE valorHraExtra >0 and `idTrabajador` LIKE :dni AND   pagado = 'Si' AND fchPago = '$quincena' ");
  $consulta->bindParam(':dni',$dni);
	$consulta->execute();
	return $consulta->fetchAll();
}



function buscarPrestamosPedidosTrabajadorPagadosPorTipoItem($db,$dni,$mes,$anhio,$quin,$tipoItem){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `idTrabajador`, `descripcion`, `monto`, `nroCuotas`, `entregado`, `tipoItem`, `fchCreacion` FROM `prestamo` WHERE `idTrabajador` = :dni  AND `entregado` = 'Si' AND  `fchQuincena` = '$quincena' AND `tipoItem` = :tipoItem ");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':tipoItem',$tipoItem);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarPrestamosPedidosTrabajadorPagadosTodosLosTipoItem($db,$dni,$mes,$anhio,$quin){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  //$consulta = $db->prepare("SELECT `idTrabajador`, `descripcion`,  if(tipoItem  = 'Inasistencia', -1*monto,  `monto`) AS monto , `nroCuotas`, `entregado`, `tipoItem`, `fchCreacion` FROM `prestamo` WHERE `idTrabajador` = :dni  AND `entregado` = 'Si' AND  `fchQuincena` = '$quincena' AND tipoItem IN ('Prestamo', 'Reembolso', 'Bono', 'Movilidad', 'BonoDiario', 'DevolGarantia', 'DomFeriado', 'Vacaciones', 'HraExtra', 'ServAdicional', 'DescMedico' ,'Inasistencia')  ORDER BY  `tipoItem` ASC ");

  $consulta = $db->prepare("SELECT `idTrabajador` , `descripcion` , if( tipoItem = 'Inasistencia', -1 * monto, `monto` ) AS monto, `nroCuotas` , `entregado` , `tipoItem` , `fchCreacion` FROM `prestamo` WHERE `idTrabajador` = :dni AND `entregado` = 'Si' AND `fchQuincena` = '$quincena' AND tipoItem IN ('Prestamo', 'Reembolso', 'Bono', 'Movilidad', 'BonoDiario', 'DevolGarantia', 'DomFeriado', 'Vacaciones', 'HraExtra', 'ServAdicional', 'DescMedico', 'LicGoce', 'Inasistencia','MovilAsig')

UNION

SELECT idTrabajador, concat( 'Servicio ', fchDespacho, '-', correlativo ) AS descripcion, valorHraExtra AS monto, 1 AS nroCuotas, pagado AS entregado, 'HraExtra' AS tipoItem, ultProcesoFch AS fchCreacion FROM despachopersonal WHERE idTrabajador = :dni AND fchPago = '$quincena' AND pagado = 'Si' AND valorHraExtra > 0

ORDER BY tipoItem");

  $consulta->bindParam(':dni',$dni);
  //$consulta->bindParam(':tipoItem',$tipoItem);
	$consulta->execute();
	return $consulta->fetchAll();
}


function buscarPrestamosPedidosTrabajadorPagadosTodosLosTipoItemMenos($db,$dni,$mes,$anhio,$quin){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';

  if ($quincena >= '2017-04-14'){
  	return buscarPrestamosPedidosTrabajadorPagadosTodosLosTipoItemMenosContab($db,$dni,$mes,$anhio,$quin);
  } else {
	  $consulta = $db->prepare("SELECT `idTrabajador`, `descripcion`, `monto`, `nroCuotas`, `entregado`, `tipoItem`, `fchCreacion` FROM `prestamo` WHERE `idTrabajador` = :dni  AND `entregado` = 'Si' AND `fchQuincena` = '$quincena' AND tipoitem IN ('Pension','PnsNacONP','PnsPriComis','PnsPriPrima','PnsPriAport','EsSalud', 'Adelanto') ORDER BY  `tipoItem` ASC ");
	  $consulta->bindParam(':dni',$dni);
	  //$consulta->bindParam(':tipoItem',$tipoItem);
		$consulta->execute();
		return $consulta->fetchAll();  	
  }
}


function buscarPrestamosPedidosTrabajadorPagadosTodosLosTipoItemMenosContab($db,$dni,$mes,$anhio,$quin){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT idTrabajador, `planillaitems`.descripcion, valor AS monto, '1' As nroCuotas, 'Si' AS entregado, `quincenadetallecontab`.id AS tipoItem, fchQuincena AS fchCreacion  FROM `quincenadetallecontab`, planillaitems WHERE `quincenadetallecontab`.id  = `planillaitems`.id AND `fchQuincena` = '$quincena' AND `idTrabajador` LIKE :dni AND `quincenadetallecontab`.`id` IN ('aporte','comision','seguro','onp')
  UNION
    SELECT `idTrabajador`, `descripcion`, `monto`, `nroCuotas`, `entregado`, `tipoItem`, `fchCreacion` FROM `prestamo` WHERE `idTrabajador` = :dni  AND `entregado` = 'Si' AND `fchQuincena` = '$quincena' AND tipoitem IN ( 'Adelanto') ");
  $consulta->bindParam(':dni',$dni);
  //$consulta->bindParam(':tipoItem',$tipoItem);
	$consulta->execute();
	return $consulta->fetchAll();
}


function buscarPrestamosPedidosTrabajadorPagadosTodosLosTipoItemMenosAdelanto($db,$dni,$mes,$anhio,$quin){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `idTrabajador`, `descripcion`, `monto`, `nroCuotas`, `entregado`, `tipoItem`, `fchCreacion` FROM `prestamo` WHERE `idTrabajador` = :dni  AND `entregado` = 'Si' AND  `fchQuincena` = '$quincena' AND tipoitem = 'Adelanto'  ORDER BY  `tipoItem` ASC ");
  $consulta->bindParam(':dni',$dni);
  //$consulta->bindParam(':tipoItem',$tipoItem);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarPrestamosPedidosTrabajadorPagadosTodosLosTipoItemMenosPensionyEsSalud($db,$dni,$mes,$anhio,$quin){
  $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `idTrabajador`, `descripcion`, `monto`, `nroCuotas`, `entregado`, `tipoItem`, `fchCreacion` FROM `prestamo` WHERE `idTrabajador` = :dni  AND `entregado` = 'Si' AND  `fchQuincena` = '$quincena' AND (tipoitem = 'Pension' OR tipoitem = 'EsSalud') ORDER BY  `tipoItem` ASC ");
  $consulta->bindParam(':dni',$dni);
  //$consulta->bindParam(':tipoItem',$tipoItem);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarTrabajadorVencimientos($db,$dni){
  $consulta = $db->prepare("SELECT `idTrabajador`, `fchInicio`, `nombre`, `fchFin`, `escaneo`, `observacion` FROM `trabajadorlicencias` WHERE `idTrabajador` = :dni AND estado = 'Activo' ");
  $consulta->bindParam(':dni',$dni);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarTrabajadorDocsVarios($db,$id = NULL,$dni = NULL, $estado = 'Mostrar' ){
	$where = " estado = 'Mostrar'  ";
	if ($id != NULL) $where .= " AND  `id` = :id  ";
	if ($dni != NULL) $where .= " AND  `idTrabajador` = :dni  ";


	$consulta = $db->prepare("SELECT `id`, `idTrabajador`, `nombre`, `estado`, `descripcion`, `escaneo`, `usuario`, `fchCreacion`, `usuarioUltimoCambio`, `fchUltimoCambio` FROM `trabajadordocsvarios` WHERE $where ");
  if ($id != NULL) $consulta->bindParam(':id',$id);
  if ($dni != NULL) $consulta->bindParam(':dni',$dni);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarTrabajadorDocsVariosId($db,$id){
	$consulta = $db->prepare("SELECT `id`, `idTrabajador`, `nombre`, `estado`, `descripcion`, `escaneo`, `usuario`, `fchCreacion`, `usuarioUltimoCambio`, `fchUltimoCambio` FROM `trabajadordocsvarios` WHERE `idTrabajador` = :dni AND estado = 'Mostrar' ");
  $consulta->bindParam(':dni',$dni);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarTrabajadorUnVencimiento($db,$dni,$fchInicio,$nombre){
  $consulta = $db->prepare("SELECT `idTrabajador`, `fchFin`, `observacion`, `escaneo` FROM `trabajadorlicencias` WHERE `idTrabajador`= :dni AND fchInicio= :fchInicio AND nombre= :nombre ");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':fchInicio',$fchInicio);
  $consulta->bindParam(':nombre',$nombre);
  $consulta->execute();
  return $consulta->fetchAll();
}

function resumenQuincenaDetalle($db,$anhio,$mes,$quin,$idTrabajador){
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28';
  if ($idTrabajador == null)
     $whereTrab = '';
  else
     $whereTrab = " AND `quincenadetalle`.`idTrabajador` = '$idTrabajador'  ";

  $consulta = $db->prepare("SELECT `quincenadetalle`.`idTrabajador`, `categoria`, `quincenadetalle`.`modoSueldo`, `quincenadetalle`.`renta5ta`, `valor`, `diferencia`, `remVacac`, `cts`, `pension`, `esSalud`,concat(`trabajador`.`apPaterno`,' ',`trabajador`.`apMaterno`,', ', `trabajador`.`nombres`) as `nombreCompleto`, `quincenadetalle`.`totPagar`, `quincenadetalle`.`remunBasica`, `quincenadetalle`.`pagoAsigFamiliar`, `quincenadetalle`.`fchCreacion`, `quincenadetalle`.`pagoAsigFamiliar`, `quincenadetalle`.`usuario`, `trabajador`.bancoNombre, `trabajador`.bancoNroCuenta, `trabajador`.bancoTipoCuenta, `trabajador`.tipoDocTrab, `tradoc`.nroDocTrab AS nroDocTrabPred, `tradoc`.tipoDocTrab AS tipoDocTrabPred  FROM `quincenadetalle`, `trabajador` LEFT JOIN trabajdocumentos AS tradoc ON `trabajador`.idTrabajador = `tradoc`.idTrabajador AND `tradoc`.estado = 'Predeterminado' WHERE `quincenadetalle`.`idTrabajador` = `trabajador`.`idTrabajador` AND `quincena` = :fchQuincena  $whereTrab  ORDER BY `trabajador`.`apPaterno` ,`trabajador`.`apMaterno`, `trabajador`.`nombres`");
  $consulta->bindParam(':fchQuincena',$fchQuincena);
  $consulta->execute();
  return $consulta->fetchAll();
}


function resumenDespachos($db,$anhio,$mes,$quin){
  if($quin == '1') $fchPago = $anhio.'-'.$mes.'-14';
  else  $fchPago = $anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `despachopersonal`.`idTrabajador`, concat(`trabajador`.`apPaterno`,' ',`trabajador`.`apMaterno`,', ', `trabajador`.`nombres` ) as nombreCompleto, count(*) as viajes , sum(`valorRol`) as valorRol, `despachopersonal`.`fchPago`, `categTrabajador`, `modoSueldo`, sum(valorHraExtra) AS valorHraExtra FROM `despachopersonal`  , trabajador WHERE `despachopersonal`.`idTrabajador` = `trabajador`.`idTrabajador` AND  `despachopersonal`.`pagado`= 'Si' AND `despachopersonal`.`fchPago` = :fchPago  group by  `trabajador`.`idTrabajador` order by `trabajador`.`apPaterno` ");
  $consulta->bindParam(':fchPago',$fchPago);
  $consulta->execute();
  return $consulta->fetchAll();
}

function resumenOcurrencia($db,$anhio,$mes,$quin){
  if($quin == '1') $fchPago = $anhio.'-'.$mes.'-14';
  else  $fchPago = $anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `idTrabajador`, `fchPago`, sum(if(`tipoOcurrencia`= 'Movilidad',`montoCuota`,0)) AS pagoMovilidad , sum(if(`tipoOcurrencia`= 'Caja Chica',`montoCuota`,0)) AS pagoCajaChica , sum(if(`tipoOcurrencia`= 'Descuento',`montoCuota`,0)) AS pagoDescuento FROM `ocurrenciaconsulta` WHERE `fchQuincena` = :fchPago AND `pagado`= 'Si' group by  `idTrabajador` order by `ocurrenciaconsulta`.`idTrabajador` ");
  $consulta->bindParam(':fchPago',$fchPago);
  $consulta->execute();
  return $consulta->fetchAll();     
}


function resumenVarios($db,$anhio,$mes,$quin){
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `idTrabajador`, `fchPago`, sum(if(`tipoItem`= 'Gratificacion',`monto`,0)) AS pagoGratificacion
    , sum(if(`tipoItem`= 'Vacaciones',`monto`,0)) AS pagoVacaciones  , sum(if(`tipoItem`= 'CTS',`monto`,0)) AS pagoCts
    , sum(if(`tipoItem`= 'PnsNacONP',`monto`,0)) AS pnsNacONP    , sum(if(`tipoItem`= 'PnsPriComis',`monto`,0)) AS pnsPriComis
    , sum(if(`tipoItem`= 'PnsPriPrima',`monto`,0)) AS pnsPriPrima, sum(if(`tipoItem`= 'PnsPriAport',`monto`,0)) AS pnsPriAport
    , sum(if(`tipoItem`= 'EsSalud',`monto`,0)) AS pagoEsSalud    , sum(if(`tipoItem`= 'Prestamo',`monto`,0)) AS reciboPrestamo
    , sum(if(`tipoItem`= 'Reembolso',`monto`,0)) AS reembolso    , sum(if(`tipoItem`= 'Bono',`monto`,0)) AS bono
    , sum(if(`tipoItem`= 'Movilidad',`monto`,0)) AS movilidad    , sum(if(`tipoItem`= 'Adelanto',`monto`,0)) AS adelanto
    , sum(if(`tipoItem`= 'Indemnizacion',`monto`,0)) AS pagoIndemnizacion , sum(if(`tipoItem`= 'Participacion',`monto`,0)) AS pagoParticipacion 
    , sum(if(`tipoItem`= 'AdelantoQ',`monto`,0)) AS pagoAdelantoQ, sum(if(`tipoItem`= 'DevolGarantia',`monto`,0)) AS devGarantia
    , sum(if(`tipoItem`= 'DomFeriado',`monto`,0)) AS domFeriado  , sum(if(`tipoItem`= 'HraExtra',`monto`,0)) AS hraExtra
    , sum(if(`tipoItem`= 'AsigFam',`monto`,0)) AS asigFam        , sum(if(`tipoItem`= 'ServAdicional',`monto`,0)) AS servAdicional
    , sum(if(`tipoItem`= 'DescMedico',`monto`,0)) AS descMedico  , sum(if(`tipoItem`= 'LicGoce',`monto`,0)) AS licGoce
    , sum(if(`tipoItem`= 'MovilAsig',`monto`,0)) AS movilAsig    , sum(if(`tipoItem`= 'Inasistencia',`monto`,0)) AS inasistencia
    , sum(if(`tipoItem`= 'BonoProducMes',`monto`,0)) AS bonoProducMes 
      FROM `prestamo` WHERE `fchQuincena` = :fchQuincena AND  `entregado`= 'Si' group by `idTrabajador` order by `idTrabajador` ");
  $consulta->bindParam(':fchQuincena',$fchQuincena);
  $consulta->execute();
  return $consulta->fetchAll();
}


function resumenPagoPrestamos($db,$anhio,$mes,$quin){
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `idTrabajador`, sum(if(`tipoItem` = 'Prestamo',`montoCuota`,0)) as pagoPrestamo,  sum(if(`tipoItem` = 'FondoGarantia',`montoCuota`,0)) as fondoGarantia,  sum(if(`tipoItem` = 'Compra',`montoCuota`,0)) as compra  , sum(if( substring(`tipoItem`,1,5)  = 'Dscto',`montoCuota`,0)) as dsctoTrabaj , sum(if(`tipoItem` = 'Ajuste',`montoCuota`,0)) as ajuste FROM `prestamodetalle`  WHERE `fchQuincena` = :fchQuincena AND  `pagado`= 'Si' group by `idTrabajador` order by `idTrabajador` ");
  $consulta->bindParam(':fchQuincena',$fchQuincena);
  $consulta->execute();
  return $consulta->fetchAll();
}


function detallesPagoDiario($db, $idTrab, $mes, $anhio, $quin ){
  if($quin == '1') $fchPago = $anhio.'-'.$mes.'-14';
  else  $fchPago = $anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `fchDespacho`, `despachopersonal`.`idTrabajador`, count(`despachopersonal`.`idTrabajador`) as Cantidad , sum(`valorRol`), modoSueldo, (remuneracionBasica + asignacionFamiliar)/30 as minimoDiario, if(modoSueldo= 'Fijo', if(sum(`valorRol`) > (remuneracionBasica + asignacionFamiliar)/30,sum(`valorRol`), (remuneracionBasica + asignacionFamiliar)/30)   ,sum(`valorRol`) ) as Diario,  if(modoSueldo= 'Fijo', if(sum(`valorRol`) > (remuneracionBasica + asignacionFamiliar)/30, sum(`valorRol`) - (remuneracionBasica + asignacionFamiliar)/30 ,0 )   ,0) as Diferencia FROM `despachopersonal` , `trabajador` WHERE  `despachopersonal`.`idTrabajador` = `trabajador`.`idTrabajador`   AND   `despachopersonal`.`idTrabajador` = :idTrab AND `pagado` = 'Si' AND `fchPago`= :fchPago  Group by `fchDespacho` order by `fchDespacho`  ");
  $consulta->bindParam(':idTrab',$idTrab);
  $consulta->bindParam(':fchPago',$fchPago);
  $consulta->execute();
  return $consulta->fetchAll();
}


function resumenDespachosMarcados($db,$anhio,$mes,$quin){
  if($quin == '1') $fchPago = $anhio.'-'.$mes.'-14';
  else  $fchPago = $anhio.'-'.$mes.'-28';
  //echo "FchPago $fchPago AÃ±o $anhio  Mes $mes ";
  
  $consulta = $db->prepare("SELECT `despachopersonal`.`idTrabajador`, concat(`trabajador`.`apPaterno`,' ',`trabajador`.`apMaterno`,', ', `trabajador`.`nombres` ) as nombreCompleto, count(*) as viajes,  count(distinct `fchDespacho`) AS `dias`, sum(`valorRol`+ valorAdicional) as valorRol, sum(valorHraExtra) AS valorHraExtra, `despachopersonal`.`fchPago`, `categTrabajador`, `modoSueldo` FROM `despachopersonal`, trabajador WHERE `despachopersonal`.`idTrabajador` = `trabajador`.`idTrabajador` AND  `despachopersonal`.`pagado`= 'Md' AND `despachopersonal`.`fchPago` = :fchPago  group by  `trabajador`.`idTrabajador` order by `trabajador`.`apPaterno` ");
  $consulta->bindParam(':fchPago',$fchPago);
  $consulta->execute();
  return $consulta->fetchAll();
}


function resumenOcurrenciaMarcados($db,$anhio,$mes,$quin, $idTrabajador = NULL){
	$where = ($idTrabajador == NULL)?"":" AND idTrabajador = :idTrabajador ";
  if($quin == '1') $fchPago = $anhio.'-'.$mes.'-14';
  else  $fchPago = $anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `idTrabajador`, `fchPago`, sum(if(`tipoOcurrencia`= 'Movilidad',`montoCuota`,0)) AS pagoMovilidad , sum(if(`tipoOcurrencia`= 'Caja Chica',`montoCuota`,0)) AS pagoCajaChica , sum(if(`tipoOcurrencia`= 'Descuento',`montoCuota`,0)) AS pagoDescuento FROM `ocurrenciaconsulta` WHERE `fchQuincena` = :fchPago AND `pagado`= 'Md' $where group by  `idTrabajador` order by `ocurrenciaconsulta`.`idTrabajador` ");
  $consulta->bindParam(':fchPago',$fchPago);
  if($idTrabajador != NULL) $consulta->bindParam(':idTrabajador',$idTrabajador);
  $consulta->execute();
  return $consulta->fetchAll();     
}


function resumenVariosMarcados($db,$anhio,$mes,$quin){
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28';
  $consulta = $db->prepare("SELECT `idTrabajador`, `fchPago`, sum(if(`tipoItem`= 'Gratificacion',`monto`,0)) AS pagoGratificacion
    , sum(if(`tipoItem`= 'Vacaciones',`monto`,0)) AS pagoVacaciones  , sum(if(`tipoItem`= 'CTS',`monto`,0)) AS pagoCts
    , sum(if(`tipoItem`= 'PnsNacONP',`monto`,0)) AS pnsNacONP, sum(if(`tipoItem`= 'PnsPriComis',`monto`,0)) AS pnsPriComis
    , sum(if(`tipoItem`= 'PnsPriPrima',`monto`,0)) AS pnsPriPrima, sum(if(`tipoItem`= 'PnsPriAport',`monto`,0)) AS pnsPriAport
    , sum(if(`tipoItem`= 'EsSalud',`monto`,0)) AS pagoEsSalud, sum(if(`tipoItem`= 'Prestamo',`monto`,0)) AS reciboPrestamo
    , sum(if(`tipoItem`= 'Reembolso',`monto`,0)) AS reembolso, sum(if(`tipoItem`= 'Bono',`monto`,0)) AS bono
    , sum(if(`tipoItem`= 'Movilidad',`monto`,0)) AS movilidad, sum(if(`tipoItem`= 'Adelanto',`monto`,0)) AS adelanto
    , sum(if(`tipoItem`= 'Indemnizacion',`monto`,0)) AS pagoIndemnizacion , sum(if(`tipoItem`= 'Participacion',`monto`,0)) AS pagoParticipacion
    , sum(if(`tipoItem`= 'AdelantoQ',`monto`,0)) AS pagoAdelantoQ , sum(if(`tipoItem`= 'DevolGarantia',`monto`,0)) AS devGarantia
    , sum(if(`tipoItem`= 'DomFeriado',`monto`,0)) AS domFeriado,  sum(if(`tipoItem`= 'HraExtra',`monto`,0)) AS hraExtra
    , sum(if(`tipoItem`= 'AsigFam',`monto`,0)) AS asigFam,  sum(if(`tipoItem`= 'ServAdicional',`monto`,0)) AS servAdicional
    , sum(if(`tipoItem`= 'DescMedico',`monto`,0)) AS descMedico, sum(if(`tipoItem`= 'LicGoce',`monto`,0)) AS licGoce
    , sum(if(`tipoItem`= 'MovilAsig',`monto`,0)) AS movilAsig, sum(if(`tipoItem`= 'Inasistencia',`monto`,0)) AS inasistencia
    , sum(if(`tipoItem`= 'BonoProducMes',`monto`,0)) AS bonoProducMes FROM `prestamo` WHERE `fchQuincena` = :fchQuincena AND  `entregado`= 'Md' group by `idTrabajador` order by `idTrabajador` ");
  $consulta->bindParam(':fchQuincena',$fchQuincena);
  $consulta->execute();
  return $consulta->fetchAll();
}


function resumenDsctosAporteEtcContab($db,$anhio,$mes,$quin, $idTrabajador = NULL){
	if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28';
  $where = ($idTrabajador == NULL)?"":" AND `idTrabajador` = :idTrabajador "; 
	$consulta = $db->prepare("SELECT `quincenadetallecontab`.`idTrabajador`,  sum(if(`quincenadetallecontab`.`id` = 'esSaludVid', valor, 0 )) AS esSaludVid,      sum(if(`quincenadetallecontab`.`id` = 'onp', valor, 0 )) AS onp, sum(if(`quincenadetallecontab`.`id` = 'comision' or `quincenadetallecontab`.`id` = 'comisVac' , valor, 0 )) AS comision , sum(if(`quincenadetallecontab`.`id` = 'aporte' or `quincenadetallecontab`.`id` = 'aporteVac' , valor, 0 )) AS aporte, sum(if(`quincenadetallecontab`.`id` = 'seguro' or `quincenadetallecontab`.`id` = 'seguroVac', valor, 0 )) AS seguro , sum(if(`quincenadetallecontab`.`id` = 'renta5ta', valor, 0 )) AS renta5ta , sum(if(`quincenadetallecontab`.`id` = 'bonoProd', valor, 0 )) AS bonoProd  , sum(if(`quincenadetallecontab`.`id` = 'remVacac', valor, 0 )) AS remVacac  FROM `quincenadetallecontab`, planillaitems WHERE  `quincenadetallecontab`.`id` =  planillaitems.id  AND `fchQuincena` = :fchQuincena AND planillaitems.grupoNro IN ('1','4','2') $where GROUP BY idTrabajador ");
	$consulta->bindParam(':fchQuincena',$fchQuincena);
  if($idTrabajador != NULL) $consulta->bindParam(':idTrabajador',$idTrabajador);
  $consulta->execute();
  return $consulta->fetchAll();
}

function resumenPagoVacProgram($db,$anhio,$mes,$quin, $idTrabajador = NULL){
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28';
  $where = ($idTrabajador == NULL)?"":" AND `idTrabajador` = :idTrabajador "; 

  $consulta = $db->prepare("SELECT `quincenadetallecontab`.`idTrabajador`, sum(if(`quincenadetallecontab`.`id` = 'adelQ' AND `quincenadetallecontab`.`ultProceso` LIKE 'autoVac%' , valor, 0 )) AS adelQPagoVac FROM `quincenadetallecontab`, planillaitems WHERE `quincenadetallecontab`.`id` = planillaitems.id AND `fchQuincena` = :fchQuincena AND planillaitems.grupoNro IN ('3') $where GROUP BY idTrabajador ");
  $consulta->bindParam(':fchQuincena',$fchQuincena);
  if($idTrabajador != NULL) $consulta->bindParam(':idTrabajador',$idTrabajador);
  $consulta->execute();
  return $consulta->fetchAll();


}

function resumenPagoPrestamosMarcados($db,$anhio,$mes,$quin, $idTrabajador = NULL){
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28';

  $where = ($idTrabajador == NULL)?"":" AND idTrabajador = :idTrabajador "; 
  $consulta = $db->prepare("SELECT `idTrabajador`, sum(if(`tipoItem` = 'Prestamo',`montoCuota`,0)) as pagoPrestamo,  sum(if(`tipoItem` = 'FondoGarantia',`montoCuota`,0)) as fondoGarantia,  sum(if(`tipoItem` = 'Compra',`montoCuota`,0)) as compra , sum(if( substring(`tipoItem`,1,5)  = 'Dscto',`montoCuota`,0)) as dsctoTrabaj , sum(if(`tipoItem` = 'Ajuste',`montoCuota`,0)) as ajuste FROM `prestamodetalle`  WHERE `fchQuincena` = :fchQuincena AND  `pagado`= 'Md' $where  group by `idTrabajador` order by `idTrabajador` ");
  $consulta->bindParam(':fchQuincena',$fchQuincena);
  if($idTrabajador != NULL) $consulta->bindParam(':idTrabajador',$idTrabajador);
  $consulta->execute();
  return $consulta->fetchAll();
}


function resumenPagoPrestamosMarcadosContab($db,$anhio,$mes,$quin, $idTrabajador = NULL){
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28';

  $where = ($idTrabajador == NULL)?"":" AND idTrabajador = :idTrabajador "; 
  $consulta = $db->prepare("SELECT `idTrabajador`, sum(if(`tipoItem` = 'Prestamo',`montoCuota`,0)) as pagoPrestamo,  sum(if(`tipoItem` = 'FondoGarantia',`montoCuota`,0)) as fondoGarantia,  sum(if(`tipoItem` = 'Compra',`montoCuota`,0)) as compra , sum(if( `tipoItem`  = 'DsctoTrabaj',`montoCuota`,0)) as dsctoTrabaj , sum(if(`tipoItem` = 'Ajuste',`montoCuota`,0)) as ajuste , sum(if(`tipoItem` = 'DsctoUnif',`montoCuota`,0)) as dsctoUnif FROM `prestamodetalle`  WHERE `fchQuincena` = :fchQuincena AND  `pagado`= 'Md'  $where  group by `idTrabajador` order by `idTrabajador` ");
  $consulta->bindParam(':fchQuincena',$fchQuincena);
  if($idTrabajador != NULL) $consulta->bindParam(':idTrabajador',$idTrabajador);
  $consulta->execute();
  return $consulta->fetchAll();
}


function autoMarcadoDespachoPersonal($db,$fchIni,$fchFin,$nombreProceso,$usuario){
  $anhio = $_SESSION['Anhio'];
  $aux  = $_SESSION['Quincena'];
  $mes  = strtok($aux, '-');
  $quin = strtok('-');
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28'; 
  //funciona actualmente $actualiza = $db->prepare("UPDATE `despachopersonal`, `trabajador`, `despacho` set `despachopersonal`.`pagado` = 'Md', `fchPago` = '$fchQuincena', `anhio` = '$anhio', `mes` = '$mes', `ultProcesoUsuario` = :usuario, `ultProceso` = :nombreProceso,  `ultProcesoFch` = curdate() WHERE `despachopersonal`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachopersonal`.`correlativo` = `despacho`.`correlativo` AND `despachopersonal`.`idTrabajador` = `trabajador`.`idTrabajador` AND `trabajador`.`estadoTrabajador` = 'activo' AND `despachopersonal`.`pagado` = 'No' AND `tipoRol` != 'Coordinador' AND (`despachopersonal`.`fchDespacho` >= :fchIni AND `despachopersonal`.`fchDespacho` <= :fchFin ) AND concluido = 'Si' ");
  $actualiza = $db->prepare("UPDATE `despachopersonal`, `trabajador`, `despacho` set `despachopersonal`.`pagado` = 'Md', `fchPago` = '$fchQuincena', `anhio` = '$anhio', `mes` = '$mes', `ultProcesoUsuario` = :usuario, `ultProceso` = :nombreProceso,  `ultProcesoFch` = curdate() WHERE `despachopersonal`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachopersonal`.`correlativo` = `despacho`.`correlativo` AND `despachopersonal`.`idTrabajador` = `trabajador`.`idTrabajador` AND `trabajador`.`estadoTrabajador` = 'activo' AND `despachopersonal`.`pagado` = 'No' AND `tipoRol` != 'Coordinador' AND (`despachopersonal`.`fchDespacho` >= :fchIni AND `despachopersonal`.`fchDespacho` <= :fchFin ) AND `concluido` = 'Si' AND `fchDesmarca` IS NULL");
  $actualiza->bindParam(':fchIni',$fchIni);
  $actualiza->bindParam(':fchFin',$fchFin);
  $actualiza->bindParam(':nombreProceso',$nombreProceso);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();
}

function autoMarcadoPrestamoHraExtra($db,$fchIni,$fchFin,$nombreProceso,$usuario){//Se está implementando
  $anhio = $_SESSION['Anhio'];
  $aux = $_SESSION['Quincena'];
  $mes = strtok($aux, '-');
  $quin = strtok('-');
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28'; 
  //$actualiza = $db->prepare("UPDATE `prestamo`, `trabajador` set `entregado` = 'Md', `fchQuincena` = '$fchQuincena', `anhio` = '$anhio', `mes` = '$mes', `ultProcesoUsuario` = :usuario, `ultProceso` = :nombreProceso,  `ultProcesoFch` = curdate()  WHERE  `prestamo`.`idTrabajador` = `trabajador`.`idTrabajador` AND `trabajador`.`estadoTrabajador` = 'activo' AND `entregado` = 'No'  AND `tipoItem` = 'HraExtra'  AND `fchPago` >= :fchIni AND `fchPago` <= :fchFin");
  $actualiza = $db->prepare("UPDATE `prestamo`, `trabajador` set `entregado` = 'Md', `fchQuincena` = '$fchQuincena', `anhio` = '$anhio', `mes` = '$mes', `ultProcesoUsuario` = :usuario, `ultProceso` = :nombreProceso,  `ultProcesoFch` = curdate()  WHERE  `prestamo`.`idTrabajador` = `trabajador`.`idTrabajador` AND `trabajador`.`estadoTrabajador` = 'activo' AND `entregado` = 'No'  AND `tipoItem` = 'HraExtra'  AND (`fchPago` >= :fchIni AND `fchPago` <= :fchFin) AND `fchDesmarca` IS NULL ");

  $actualiza->bindParam(':fchIni',$fchIni);
  $actualiza->bindParam(':fchFin',$fchFin);
  $actualiza->bindParam(':nombreProceso',$nombreProceso);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();
}

function autoMarcadoPrestamo($db,$fchIni,$fchFin,$nombreProceso,$usuario){
  $anhio = $_SESSION['Anhio'];
  $aux = $_SESSION['Quincena'];
  $mes = strtok($aux, '-');
  $quin = strtok('-');
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28'; 
  $actualiza = $db->prepare("UPDATE `prestamo`, `trabajador` set `entregado` = 'Md', `fchQuincena` = '$fchQuincena', `anhio` = '$anhio', `mes` = '$mes', `ultProcesoUsuario` = :usuario, `ultProceso` = :nombreProceso,  `ultProcesoFch` = curdate()  WHERE  `prestamo`.`idTrabajador` = `trabajador`.`idTrabajador` AND `trabajador`.`estadoTrabajador` = 'activo' AND `entregado` = 'No' AND (`fchPago` >= :fchIni AND `fchPago` <= :fchFin) AND `fchDesmarca` IS NULL");

  $actualiza->bindParam(':fchIni',$fchIni);
  $actualiza->bindParam(':fchFin',$fchFin);
  $actualiza->bindParam(':nombreProceso',$nombreProceso);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();
  
 // $actualiza = $db->prepare("UPDATE  `prestamodetalle`, `trabajador` set `pagado` = 'Md', `fchQuincena` = '$fchQuincena', `anhio` = '$anhio', `mes` = '$mes', `ultProcesoUsuario` = :usuario, `ultProceso` = :nombreProceso,  `ultProcesoFch` = curdate()  WHERE  `prestamodetalle`.`idTrabajador` = `trabajador`.`idTrabajador` AND `trabajador`.`estadoTrabajador` = 'activo' AND `pagado` = 'No' AND `fchPago` >= :fchIni AND `fchPago` <= :fchFin");

  $actualiza = $db->prepare("UPDATE  `prestamodetalle`, `trabajador` set `pagado` = 'Md', `fchQuincena` = '$fchQuincena', `anhio` = '$anhio', `mes` = '$mes', `ultProcesoUsuario` = :usuario, `ultProceso` = :nombreProceso,  `ultProcesoFch` = curdate()  WHERE  `prestamodetalle`.`idTrabajador` = `trabajador`.`idTrabajador` AND `trabajador`.`estadoTrabajador` = 'activo' AND `pagado` = 'No' AND (`fchPago` >= :fchIni AND `fchPago` <= :fchFin) AND `fchDesmarca` IS NULL ");
  $actualiza->bindParam(':fchIni',$fchIni);
  $actualiza->bindParam(':fchFin',$fchFin);
  $actualiza->bindParam(':nombreProceso',$nombreProceso);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();
}



function autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,$tipoItem){
  $usuario = $_SESSION['usuario'];
  $anhio = $_SESSION['Anhio'];
  $aux = $_SESSION['Quincena'];
  $mes = strtok($aux, '-');
  $quin = strtok('-');
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28'; 
  $actualiza = $db->prepare("UPDATE `prestamo`, `trabajador` set `entregado` = 'Md', `fchQuincena` = '$fchQuincena', `anhio` = '$anhio', `mes` = '$mes', `ultProcesoUsuario` = :usuario, `ultProceso` = :nombreProceso,  `ultProcesoFch` = curdate()  WHERE  `prestamo`.`idTrabajador` = `trabajador`.`idTrabajador` AND `trabajador`.`estadoTrabajador` = 'activo' AND `entregado` = 'No' AND (`fchPago` >= :fchIni AND `fchPago` <= :fchFin) AND tipoItem = :tipoItem AND `fchDesmarca` IS NULL");

  $actualiza->bindParam(':fchIni',$fchIni);
  $actualiza->bindParam(':fchFin',$fchFin);
  $actualiza->bindParam(':tipoItem',$tipoItem);
  $actualiza->bindParam(':nombreProceso',$nombreProceso);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();
  
 // $actualiza = $db->prepare("UPDATE  `prestamodetalle`, `trabajador` set `pagado` = 'Md', `fchQuincena` = '$fchQuincena', `anhio` = '$anhio', `mes` = '$mes', `ultProcesoUsuario` = :usuario, `ultProceso` = :nombreProceso,  `ultProcesoFch` = curdate()  WHERE  `prestamodetalle`.`idTrabajador` = `trabajador`.`idTrabajador` AND `trabajador`.`estadoTrabajador` = 'activo' AND `pagado` = 'No' AND `fchPago` >= :fchIni AND `fchPago` <= :fchFin");

  if ($tipoItem == 'Prestamo' || $tipoItem == 'Ajuste' || $tipoItem == 'Compra' || $tipoItem == 'DsctoTrabaj' || $tipoItem == 'DsctoUnif' || $tipoItem == 'FondoGarantia' ){
	  $actualiza = $db->prepare("UPDATE  `prestamodetalle`, `trabajador` set `pagado` = 'Md', `fchQuincena` = '$fchQuincena', `anhio` = '$anhio', `mes` = '$mes', `ultProcesoUsuario` = :usuario, `ultProceso` = :nombreProceso,  `ultProcesoFch` = curdate()  WHERE  `prestamodetalle`.`idTrabajador` = `trabajador`.`idTrabajador` AND `trabajador`.`estadoTrabajador` = 'activo' AND `pagado` = 'No' AND (`fchPago` >= :fchIni AND `fchPago` <= :fchFin) AND tipoItem = :tipoItem AND `fchDesmarca` IS NULL ");
	  $actualiza->bindParam(':fchIni',$fchIni);
	  $actualiza->bindParam(':fchFin',$fchFin);
	  $actualiza->bindParam(':tipoItem',$tipoItem);
	  $actualiza->bindParam(':nombreProceso',$nombreProceso);
	  $actualiza->bindParam(':usuario',$usuario);
	  $actualiza->execute();
  }
}



function autoMarcadoOcurrencia($db,$fchIni,$fchFin,$nombreProceso,$usuario){
  $anhio = $_SESSION['Anhio'];
  $aux = $_SESSION['Quincena'];
  $mes = strtok($aux, '-');
  $quin = strtok('-');
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28'; 
    
  $actualiza = $db->prepare("UPDATE `ocurrenciaconsulta`, `trabajador` set `pagado` = 'Md', `fchQuincena` = '$fchQuincena', `anhio` = '$anhio', `mes` = '$mes', `ultProcesoUsuario` = :usuario, `ultProceso` = :nombreProceso,  `ultProcesoFch` = curdate() WHERE `ocurrenciaconsulta`.`idTrabajador` = `trabajador`.`idTrabajador` AND `trabajador`.`estadoTrabajador` = 'activo' AND `pagado` = 'No' AND `fchPago` >= :fchIni AND `fchPago` <= :fchFin AND `fchDesmarca` is null ");
  $actualiza->bindParam(':fchIni',$fchIni);
  $actualiza->bindParam(':fchFin',$fchFin);
  $actualiza->bindParam(':nombreProceso',$nombreProceso);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();
}

function autoMarcadoOcurrenciaTipoOcurrencia($db,$fchIni,$fchFin,$nombreProceso,$tipoOcurrencia){
	$usuario = $_SESSION['usuario'];
  $anhio = $_SESSION['Anhio'];
  $aux = $_SESSION['Quincena'];
  $mes = strtok($aux, '-');
  $quin = strtok('-');
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28'; 
    
  $actualiza = $db->prepare("UPDATE `ocurrenciaconsulta`, `trabajador` set `pagado` = 'Md', `fchQuincena` = '$fchQuincena', `anhio` = '$anhio', `mes` = '$mes', `ultProcesoUsuario` = :usuario, `ultProceso` = :nombreProceso,  `ultProcesoFch` = curdate() WHERE `ocurrenciaconsulta`.`idTrabajador` = `trabajador`.`idTrabajador` AND `trabajador`.`estadoTrabajador` = 'activo' AND `pagado` = 'No' AND `fchPago` >= :fchIni AND `fchPago` <= :fchFin AND tipoOcurrencia = :tipoOcurrencia AND `fchDesmarca` is null ");
  $actualiza->bindParam(':fchIni',$fchIni);
  $actualiza->bindParam(':fchFin',$fchFin);
  $actualiza->bindParam(':nombreProceso',$nombreProceso);
  $actualiza->bindParam(':tipoOcurrencia',$tipoOcurrencia);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();
}

function insertaRegEnPrestamo($db, $idTrabajador,$descripcion,$monto,$nroPartes,$fch,$tipoItem,$usuario,$nroDoc = null,$categ= NULL){

  $subTipoItem = "";
  if (substr($tipoItem,0,11) == "DsctoTrabaj" ){ 
  	$subTipoItem = substr($tipoItem,12,100);
	  $tipoItem = "DsctoTrabaj";
  }

  $inserta = $db->prepare("INSERT INTO `prestamo` (`idTrabajador`, `descripcion`, `monto`, `nroCuotas`, `codigo`, `fchPago`, `tipoItem`, `subTipoItem`,`categTrabaj`, `usuario`, `fchCreacion`, `hraCreacion`) VALUES (:idTrabajador, :descripcion, :monto, :nroCuotas, :nroDoc, :fchPago , :tipoItem, :subTipoItem, :categTrabaj , :usuario, CURDATE(), CURTIME());");
  $inserta->bindParam(':idTrabajador',$idTrabajador);
	$inserta->bindParam(':descripcion',$descripcion);
	$inserta->bindParam(':monto',$monto);
	$inserta->bindParam(':nroCuotas',$nroPartes);
	$inserta->bindParam(':nroDoc',$nroDoc);
	$inserta->bindParam(':fchPago',$fch);
	$inserta->bindParam(':tipoItem',$tipoItem);
	$inserta->bindParam(':subTipoItem',$subTipoItem);
	$inserta->bindParam(':categTrabaj',$categ);
	$inserta->bindParam(':usuario',$usuario);
	$inserta->execute();
	//No funcionó, me quedé probando aquí
	$actualiza = $db->prepare("UPDATE `prestamo` SET `descripcion` = replace(`descripcion`,'&#209;','Ñ'),   `descripcion` = replace(`descripcion`,'&#241;','ñ')  WHERE `idTrabajador` = :idTrabajador AND  `fchCreacion` = curdate() AND  `monto` = :monto  AND `tipoItem` = :tipoItem ");
	$actualiza->bindParam(':idTrabajador',$idTrabajador);
	$actualiza->bindParam(':monto',$monto);
	$actualiza->bindParam(':tipoItem',$tipoItem);
	$actualiza->execute();
};

function insertaRegPrestamoAuto($db,$idTrabajador,$anhio,$mes,$descripcion,$monto,$nroCuotas,$fchQuincena,$tipoItem,$nombreProceso, $entregado = 'Md'){
	$usuario = $_SESSION['usuario'];
	$inserta = $db->prepare("INSERT INTO `prestamo` (`idTrabajador`, `descripcion`,`tipoItem`, `monto`, `nroCuotas`,`entregado`, `anhio`, `mes`, `fchPago`, `fchQuincena`, `usuario`, `fchCreacion`, `hraCreacion`,`ultProceso` ) VALUES (:idTrabajador, :descripcion, :tipoItem,:monto, :nroCuotas, :entregado, :anhio, :mes, '$fchQuincena' , '$fchQuincena' , :usuario, CURDATE(), CURTIME(),:ultProceso);");
  $inserta->bindParam(':idTrabajador',$idTrabajador);
  $inserta->bindParam(':descripcion',$descripcion);
  $inserta->bindParam(':tipoItem',$tipoItem);
  $inserta->bindParam(':nroCuotas',$nroCuotas);
  $inserta->bindParam(':entregado',$entregado);
  $inserta->bindParam(':anhio',$anhio);
  $inserta->bindParam(':mes',$mes);
  $inserta->bindParam(':monto',$monto);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->bindParam(':ultProceso',$nombreProceso);
  $inserta->execute(); 
}

function generaInfoSobreDespachoPersonal($db,$fchDespacho,$correlativo,$idTrab,$valorRol,$tipoRol,$tpoHrasExDecTrab,$topeServicioHraNormal,$nombreCliente,$usuario,$hraIni, $hraFin, $fchDespachoFin){
  $inserta = $db->prepare("INSERT INTO despachopersonal (fchDespacho, correlativo, idTrabajador, valorRol, tipoRol, trabHraInicio, trabHraFin, trabFchDespachoFin) VALUES ( :fchDespacho, :correlativo,  :idTrabajador, :valorRol, :tipoRol, :hraIni, :hraFin, :fchDespachoFin )");
  $inserta->bindParam(':fchDespacho',$fchDespacho);
  $inserta->bindParam(':correlativo',$correlativo);
  $inserta->bindParam(':idTrabajador',$idTrab);
  $inserta->bindParam(':valorRol',$valorRol);
  $inserta->bindParam(':tipoRol',$tipoRol);
  $inserta->bindParam(':hraIni',$hraIni);
  $inserta->bindParam(':hraFin',$hraFin);
  $inserta->bindParam(':fchDespachoFin',$fchDespachoFin);
  $inserta->execute();
  //echo "POR AQUI $tpoHrasExDecTrab   $valorRol   $topeServicioHraNormal ";
  if ($tpoHrasExDecTrab >0 AND $valorRol > 0 AND $topeServicioHraNormal != '0:00' AND $tipoRol != 'Coordinador'){
  	$monto = round($tpoHrasExDecTrab * $valorRol / $topeServicioHraNormal,2);
    $descripHraExtra = "Servicio $nombreCliente $fchDespacho-$correlativo";
    insertaRegEnPrestamo($db, $idTrab,$descripHraExtra,$monto,1,$fchDespacho,'HraExtra',$usuario);	
  }
}

function verificaAsignacionFamiliar($db, $idTrabajador, $fchQuincena){
	/* 201012 La asignación familiar se pasa a pagar en la segunda quincena
	No se está utilizando
	*/
	$consulta = $db->prepare("SELECT count(*) AS cant FROM `prestamo` WHERE `idTrabajador` LIKE :idTrabajador AND `tipoItem` LIKE 'AsigFam' AND `entregado` = 'Si' AND `fchQuincena` = :fchQuincena ");
  $consulta->bindParam(':idTrabajador',$idTrabajador);
  $consulta->bindParam(':fchQuincena',$fchQuincena);
  $consulta->execute();
  $auxData =$consulta->fetchAll();

  $cant = 0;
  foreach ($auxData as $key => $value) {
  	$cant = $value['cant'];
  }
  return $cant;
}


function autoMarcadoEsSaludyPensionPartes($db,$nombreProceso,$parte){
  //$usuario = $_SESSION['usuario'];
  $anhio = $_SESSION['Anhio'];   //toma los datos de año y quincena
  $aux = $_SESSION['Quincena'];  //de las variables de sesión
  $valorMovDiaAux = $_SESSION['valorMovDiaAux'];
  $valorMovDiaCon = $_SESSION['valorMovDiaCon'];
  $mes = strtok($aux, '-');
  $quin = strtok('-');
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28'; 
  $infoAFPs = buscarInfoAfp($db); 
  foreach($infoAFPs as $afp) {       //se esta haciendo innecesario
    $nombre = $afp['nombre'];
    $comFlujo["$nombre"]  = $afp['comisionFlujo'];
    $comMixta["$nombre"]  = $afp['comisionMixta'];
    $primaSeg["$nombre"]  = $afp['primaSeg'];
    $porcOblig["$nombre"] = $afp['porcentObligat'];
  }
  //$items = buscarTodosLosTrabActivos($db);
  $items = buscarTrabajActivosQuincena($db, '5ta');
  if (count($items) >0){

  	//Esta parte procesa datos de manera masiva para luego ser recalculados para cada trabajador
  	if ($parte == "EsSalud"){
      $elimina = $db->prepare("DELETE FROM `prestamo` WHERE anhio = :anhio AND `mes` LIKE :mes AND tipoItem IN ('EsSalud') ANd fchQuincena = '$fchQuincena'");
      $elimina->bindParam('anhio',$anhio);
      $elimina->bindParam('mes',$mes);  
      $elimina->execute();
  	} else if ($parte == "Pension"){
  	  $elimina = $db->prepare("DELETE FROM `prestamo` WHERE anhio = :anhio AND `mes` LIKE :mes AND tipoItem IN ('PnsNacONP', 'PnsPriComis', 'PnsPriPrima', 'PnsPriAport', 'MovilSuped', 'DiasMovilSuped', 'BonifVarias', 'HaberBasico', 'TotalGravable') AND fchQuincena = '$fchQuincena' AND descripcion NOT LIKE 'Vacaciones.%'  ");
      $elimina->bindParam('anhio',$anhio);
      $elimina->bindParam('mes',$mes);  
      $elimina->execute();

      //$dataPosibleMovilSupedPrestamo = paraMovilSupedPrestamo($db,$fchQuincena);
      //$dataPosibleMovilSupedOcurrCon = paraMovilSupedOcurrenciaConsulta($db,$fchQuincena);
      $dataTrabajosHechos = resumenQuincenaPlanillaDespachos($db,$fchQuincena,'Md','5ta');

      //OJO, falta el tratamiento del ajuste
      $arrDataTrabAuxiliar = array();
      foreach ($dataTrabajosHechos as $item) {
      	$idTrabajador = $item['idTrabajador'];
      	$arrDataTrabAuxiliar[$idTrabajador]['diasTrabaj'] = $item['diasTrabaj'];
      	$arrDataTrabAuxiliar[$idTrabajador]['valorRol'] = $item['valorRol'];
      	$arrDataTrabAuxiliar[$idTrabajador]['posibleMovilSuped'] = 0;
      	//$arrDataTrabAuxiliar[$idTrabajador]['paraBonoProduct'] = 0;
      }

      $cant = $db->exec("DELETE FROM  `quincenadetallecontab` WHERE fchQuincena = '$fchQuincena'  ");

  	} else if ($parte == "AsigFam"){
  	  $elimina = $db->prepare("DELETE FROM `prestamo` WHERE anhio = :anhio AND `mes` LIKE :mes AND tipoItem IN ('AsigFam') AND fchQuincena = '$fchQuincena'");
      $elimina->bindParam('anhio',$anhio);
      $elimina->bindParam('mes',$mes);  
      $elimina->execute();
  	}


  	foreach ($items as $item) {
  	  $idTrabajador = $item['idTrabajador'];
      $entidadPension = $item['entidadPension'];
      $deseaDscto =   $item['deseaDscto'];
      $tipoTrabajador = $item['tipoTrabajador'];
      $valorMovilDia = ($tipoTrabajador == 'Auxiliar')?$valorMovDiaAux:$valorMovDiaCon;
      $diasTrabaj = isset($arrDataTrabAuxiliar[$idTrabajador]['diasTrabaj'])?$arrDataTrabAuxiliar[$idTrabajador]['diasTrabaj']:0;

      if ($parte == 'EsSalud'){
      	$esSalud = $_SESSION['esSalud'];
  	    if($item['deseaDscto'] == 'Si'){
	      	/* 201012 Los administrativos pasan a ser igual que los demás en cuanto a pago quincenal
	      $dsctoEsSalud = ($item['tipoTrabajador'] == 'Administrativo')?$esSalud:$esSalud*0.5;*/
	        $dsctoEsSalud = $esSalud*0.5;
	      } else 
	        $dsctoEsSalud = 0;
	      $dsctoEsSalud = round($dsctoEsSalud,2);
	      if ($dsctoEsSalud > 0){
	      	insertaRegPrestamoAuto($db, $idTrabajador,$anhio,$mes,'dsctoEsSalud',$dsctoEsSalud,'1',$fchQuincena,'EsSalud',$nombreProceso);
	    }
      } else if ($parte == 'Pension'){
      	//echo "ingreso";
      	$rpta = calcularQuincenaContab($db,$idTrabajador,$anhio, $mes, $quin, $comFlujo, $comMixta, $primaSeg, $porcOblig);
      	/*echo "<pre>";
      	print_r($rpta);
      	echo "</pre>";*/

  	  } else if ($parte == 'AsigFam') {  //Creo que hay que cqambiarlo
		    if (isset($item['remunBasica'])){
		      $asigFam = $item['pagoAsigFamiliar']; 
		    } else {
		      $asigFam = 0;
		    }
		    if ($asigFam > 0){
		    	/* 201012 La asignación familiar se pasa a pagar en la segunda quincena
		    	if ($quin == '1' || ($quin == '2' && verificaAsignacionFamiliar($db, $idTrabajador, $anhio.'-'.$mes.'-14') == 0) ){
		        insertaRegPrestamoAuto($db, $idTrabajador,$anhio,$mes,'Asign Familiar',$asigFam,'1',$fchQuincena,'AsigFam',$nombreProceso);
		     	}*/
		     	if ($quin == '2' ){
		        insertaRegPrestamoAuto($db, $idTrabajador,$anhio,$mes,'Asign Familiar',$asigFam,'1',$fchQuincena,'AsigFam',$nombreProceso);
		      }  

		    }
      }
  	  # code...
  	}
  } else {
  	echo "Error, no hay datos";
  }

}

function autoMarcadoGenerarQuincena($db,$nombreProceso,$quinFchIni,$quinFchFin){
  $user =  $_SESSION['usuario'];
  $anhio = $_SESSION['Anhio'];
  $aux = $_SESSION['Quincena'];
  $mes = strtok($aux, '-');
  $quin = strtok('-');
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28';
  
  //echo  " $fchQuincena, $user,   $nombreProceso";
  $inserta = $db->prepare("INSERT INTO `quincena` (`quincena`, `quinFchIni`, `quinFchFin`, `estadoQuincena`, `fchCreacion`, `usuario`, `nombreProceso`) VALUES ('$fchQuincena', :quinFchIni, :quinFchFin,'Md', NOW(), :usuario, :nombreProceso)");
  $inserta->bindParam(':usuario',$user);
  $inserta->bindParam(':quinFchIni',$quinFchIni);
  $inserta->bindParam(':quinFchFin',$quinFchFin);

  $inserta->bindParam(':nombreProceso',$nombreProceso);
  $inserta->execute();
}

function cambiarEstadoQuincena($db,$nombreProceso,$estado){
  $user =  $_SESSION['usuario'];
  $anhio = $_SESSION['Anhio'];
  $aux = $_SESSION['Quincena'];
  $mes = strtok($aux, '-');
  $quin = strtok('-');
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28';
  //echo  " $fchQuincena, $user, $nombreProceso";
  $inserta = $db->prepare("UPDATE `quincena` SET `estadoQuincena` = :estado, `ultCambioUsuario` = :usuario, `ultCambioProceso` = :nombreProceso  WHERE `quincena` = :fchQuincena");
  $inserta->bindParam(':fchQuincena',$fchQuincena);
  $inserta->bindParam(':estado',$estado);
  $inserta->bindParam(':usuario',$user);
  $inserta->bindParam(':nombreProceso',$nombreProceso);
  $inserta->execute();
}

function autoMarcadoConfirmarDespachoPersonal($db,$nombreProceso,$usuario){
  $anhio = $_SESSION['Anhio'];
  $aux = $_SESSION['Quincena'];
  $mes = strtok($aux, '-');
  $quin = strtok('-');
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28'; 
  $actualiza = $db->prepare("UPDATE `despachopersonal` set `pagado` = 'Si', `ultProceso` = :nombreProceso, `ultProcesoFch` = curdate() , `ultProcesoUsuario` = :usuario WHERE `pagado` = 'Md' AND `fchPago` = '$fchQuincena'");
  $actualiza->bindParam(':nombreProceso',$nombreProceso);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();  
}

function autoMarcadoConfirmarPrestamo($db,$nombreProceso,$usuario){
  $anhio = $_SESSION['Anhio'];
  $aux = $_SESSION['Quincena'];
  $mes = strtok($aux, '-');
  $quin = strtok('-');
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28';
  
  $actualiza = $db->prepare("UPDATE `prestamo` set `entregado` = 'Si', `ultProceso` = :nombreProceso, `ultProcesoFch` = curdate() , `ultProcesoUsuario` = :usuario WHERE `entregado` = 'Md' AND `fchQuincena` = '$fchQuincena'");
  $actualiza->bindParam(':nombreProceso',$nombreProceso);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();
  
  $actualiza = $db->prepare("UPDATE  `prestamodetalle` set `pagado` = 'Si', `ultProceso` = :nombreProceso, `ultProcesoFch` = curdate() , `ultProcesoUsuario` = :usuario WHERE `pagado` = 'Md' AND `fchQuincena` = '$fchQuincena'");
  
  $actualiza->bindParam(':nombreProceso',$nombreProceso);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();
}

function autoMarcadoConfirmarOcurrencia($db,$nombreProceso,$usuario){
  $anhio = $_SESSION['Anhio'];
  $aux = $_SESSION['Quincena'];
  $mes = strtok($aux, '-');
  $quin = strtok('-');
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28'; 
  $actualiza = $db->prepare("UPDATE  `ocurrenciaconsulta` set `pagado` = 'Si', `ultProceso` = :nombreProceso, `ultProcesoFch` = curdate() , `ultProcesoUsuario` = :usuario WHERE `pagado` = 'Md' AND `fchQuincena` = '$fchQuincena'");
  $actualiza->bindParam(':nombreProceso',$nombreProceso);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();
}

function eliminaPrestamo($db,$idTrabajador,$tipoItem,$monto,$fchCreacion,$descripcion){
  //$user = $_SESSION['usuario'];
  if ($tipoItem == "Prestamo" || $tipoItem == "Adelanto"){
    $elimina = $db->prepare("DELETE prestsustento FROM prestsustento, `prestamo` WHERE `prestsustento`.codigo = `prestamo`.codigo AND `idTrabajador` = :idTrabajador AND `tipoItem` = :tipoItem AND `monto` = :monto AND `fchCreacion` = :fchCreacion AND `descripcion` = :descripcion");
    $elimina->bindParam(':idTrabajador',$idTrabajador);
    $elimina->bindParam(':tipoItem',$tipoItem);
    $elimina->bindParam(':monto',$monto);
    $elimina->bindParam(':fchCreacion',$fchCreacion);
    $elimina->bindParam(':descripcion',$descripcion);  
    $elimina->execute();
  }

  $elimina = $db->prepare("DELETE FROM `prestamo` WHERE `idTrabajador` = :idTrabajador AND `tipoItem` = :tipoItem AND `monto` = :monto AND `fchCreacion` = :fchCreacion  AND `descripcion` = :descripcion ");
  $elimina->bindParam(':idTrabajador',$idTrabajador);
  $elimina->bindParam(':tipoItem',$tipoItem);
  $elimina->bindParam(':monto',$monto);
  $elimina->bindParam(':fchCreacion',$fchCreacion);
  $elimina->bindParam(':descripcion',$descripcion);  
  $elimina->execute();  
  logAccion($db,"Se eliminó $tipoItem, descripcion $descripcion de monto $monto",$idTrabajador,null);

  if ($tipoItem == 'Prestamo' || $tipoItem == 'FondoGarantia' || $tipoItem == 'Compra' || $tipoItem == 'Ajuste' || $tipoItem == 'DsctoTrabaj' || $tipoItem == 'DsctoUnif'  ){
  	$elimina = $db->prepare("DELETE FROM `prestamodetalle` WHERE `idTrabajador` = :idTrabajador AND `tipoItem` = :tipoItem AND `monto` = :monto AND `fchCreacion` = :fchCreacion  AND `descripcion` = :descripcion ");
    $elimina->bindParam(':idTrabajador',$idTrabajador);
    $elimina->bindParam(':tipoItem',$tipoItem);
    $elimina->bindParam(':monto',$monto);
    $elimina->bindParam(':fchCreacion',$fchCreacion);
    $elimina->bindParam(':descripcion',$descripcion);  
    $elimina->execute();
  }
}

function eliminaPrestamoDetalle($db,$dni,$tipoItem,$monto,$fchCreacion,$descripcion){
  $elimina = $db->prepare("DELETE FROM `prestamodetalle` WHERE `monto` = :monto AND `idTrabajador` = :idTrabajador AND `descripcion` = :descripcion AND `tipoItem` = :tipoItem  AND `fchCreacion` = :fchCreacion");
  $elimina->bindParam(':monto',$monto);
  $elimina->bindParam(':idTrabajador',$dni);
  $elimina->bindParam(':descripcion',$descripcion);
  $elimina->bindParam(':tipoItem',$tipoItem);
  $elimina->bindParam(':fchCreacion',$fchCreacion);
  $elimina->execute();
  if($elimina->rowCount() > 0 )
    logAccion($db,"Se eliminó detalles de $tipoItem, descripcion $descripcion de monto $monto",$dni,null);  
};


function eliminaOcurrenciaDetalle($db,$fchDespacho,$correlativo,$monto,$tipoOcurrencia,$descripcion){
  $elimina = $db->prepare("DELETE  FROM `ocurrenciaconsulta` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `tipoOcurrencia` LIKE :tipoOcurrencia AND `descripcion` LIKE :descripcion AND `montoTotal` = :monto");
  $elimina->bindParam(':fchDespacho',$fchDespacho);
  $elimina->bindParam(':correlativo',$correlativo);
  $elimina->bindParam(':monto',$monto);
  $elimina->bindParam(':descripcion',$descripcion);
  $elimina->bindParam(':tipoOcurrencia',$tipoOcurrencia);
  $elimina->execute();

};

function eliminaOcurrencia($db,$fchDespacho,$correlativo,$monto,$tipoOcurrencia,$descripcion){
  $elimina = $db->prepare("DELETE  FROM `ocurrencia` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `tipoOcurrencia` LIKE :tipoOcurrencia AND `descripcion` LIKE :descripcion AND `montoTotal` = :monto");
  $elimina->bindParam(':fchDespacho',$fchDespacho);
  $elimina->bindParam(':correlativo',$correlativo);
  $elimina->bindParam(':monto',$monto);
  $elimina->bindParam(':descripcion',$descripcion);
  $elimina->bindParam(':tipoOcurrencia',$tipoOcurrencia);
  $elimina->execute();


};


function modificaEsSalud($db,$dni,$valor){
  $actualiza = $db->prepare("UPDATE `trabajador` SET `deseaDscto` = :valor WHERE `trabajador`.`idTrabajador` = :dni");
  $actualiza->bindParam(':dni',$dni);
  $actualiza->bindParam(':valor',$valor);
  $actualiza->execute();
  logAccion($db,"Se modificó deseaDscto a $valor",$dni,null);
};

function logAccion($db,$descripcion,$idTrabajador,$placa){
  $user = $_SESSION['usuario'];
  $inserta = $db->prepare("INSERT INTO `logaccion` (`id`, `descripcion`, `idTrabajador`,`placa`,`usuario`, `fecha`) VALUES (NULL, :descripcion, :idTrabajador,:placa,:usuario, NOW())");
  $inserta->bindParam(':descripcion',$descripcion);
  $inserta->bindParam(':idTrabajador',$idTrabajador);
  $inserta->bindParam(':placa',$placa);
  $inserta->bindParam(':usuario',$user);
  $inserta->execute();  
};


function buscaPagadosEnPrestamo($db,$dni,$monto,$descripcion,$tipoItem,$fchCreacion){
  //echo "datos $dni, $monto, $descripcion, $tipoItem, $fchCreacion";
  $consulta = $db->prepare("SELECT  `monto`, `nroCuota`, `idTrabajador`, `descripcion`, `tipoItem`,`fchCreacion`  FROM `prestamodetalle` WHERE `monto` = :monto AND `idTrabajador` = :idTrabajador AND `descripcion` = :descripcion AND `tipoItem` = :tipoItem  AND `fchCreacion` = :fchCreacion AND `pagado` = 'Si'");
  $consulta->bindParam(':monto',$monto);
  $consulta->bindParam(':idTrabajador',$dni);
  $consulta->bindParam(':descripcion',$descripcion);
  $consulta->bindParam(':tipoItem',$tipoItem);
  $consulta->bindParam(':fchCreacion',$fchCreacion);
  $consulta->execute();
  return $consulta->fetchAll();     
};


function buscaPagadosEnOcurrencia($db,$fchDespacho,$correlativo,$monto,$tipoOcurrencia,$descripcion){
  $consulta = $db->prepare("SELECT `idTrabajador`,`fchPago`  FROM `ocurrenciaconsulta` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `tipoOcurrencia` LIKE :tipoOcurrencia AND `descripcion` LIKE :descripcion AND `montoTotal` = :monto AND `pagado` = 'Si'");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
  $consulta->bindParam(':correlativo',$correlativo);
  $consulta->bindParam(':monto',$monto);
  $consulta->bindParam(':descripcion',$descripcion);
  $consulta->bindParam(':tipoOcurrencia',$tipoOcurrencia);  
  $consulta->execute();
  return $consulta->fetchAll();     
}


function buscarHistoricoDescuentosVarios($db,$dni,$tipoItem = null,$fchCreacion = null,$monto = null,$pagado = null,$descripcion = null,$fchQuincena = null,$limite = null){ 
  $limite = str_replace(";","",$limite);
  $limite = str_replace("'","",$limite);
  if ($dni == null)
    $whereDni = '';
  else
    $whereDni = " AND `prestamodetalle`.`idTrabajador`  = :dni ";
  
  if ($tipoItem == null)
    $whereTipoItem = '';
  else {
  	$arrEliminar = array('*',';','Select','Show','?','=');
  	$tipoItem = str_replace($arrEliminar, '', $tipoItem);
  	$whereTipoItem = "  AND `prestamodetalle`.`tipoItem` IN ($tipoItem) ";
  }
  
  if ($fchCreacion == null)
    $whereFchCreacion = '';
  else
    $whereFchCreacion = " AND `prestamodetalle`.`fchCreacion` = :fchCreacion ";
  
  if ($monto == null)
    $whereMonto = '';
  else
    $whereMonto = " AND `prestamodetalle`.`monto` = :monto ";
  
  
  if ($descripcion == null)
    $whereDescrip = '';
  else {
    $novalidos = array(";","'");
    $descripcion =  str_replace($novalidos,"",$descripcion);
    $whereDescrip = " AND `prestamodetalle`.`descripcion` like '$descripcion%'";
  }
  
  if ($fchQuincena == null)
    $whereFchQuincena = '';
  else
    $whereFchQuincena = " AND `prestamodetalle`.`fchQuincena` = :fchQuincena ";
  
    
  if ($pagado == null)
    $wherePagado = '';
  else
    $wherePagado = " AND `pagado` = :pagado ";  
  
  if($limite)
    $whereLimite = "LIMIT $limite";
  else
    $whereLimite = '';  
  
  $consulta = $db->prepare("SELECT `prestamodetalle`.`monto` , `nroCuota` , `prestamodetalle`.`idTrabajador` , concat( apPaterno, ' ', apMaterno, ' ', nombres ) AS nombCompleto, `prestamodetalle`.`descripcion` , `prestamodetalle`.`tipoItem` , `prestamodetalle`.`fchCreacion` , `prestamodetalle`.`fchPago` , `montoCuota` , `pagado`, `prestamodetalle`.`fchQuincena`, `prestamo`.codigo FROM prestamo, `prestamodetalle`, `trabajador` WHERE `prestamo`.idTrabajador = `prestamodetalle`.idTrabajador AND `prestamo`.descripcion = `prestamodetalle`.descripcion AND `prestamo`.tipoItem = `prestamodetalle`.tipoItem AND `prestamo`.monto = `prestamodetalle`.monto AND `prestamo`.fchCreacion = `prestamodetalle`.fchCreacion AND `prestamodetalle`.`idTrabajador` = `trabajador`.`idTrabajador` $whereDni $whereTipoItem $whereDescrip $whereMonto $wherePagado $whereFchCreacion $whereFchQuincena ORDER BY nombCompleto, `prestamodetalle`.fchCreacion, monto, tipoItem, descripcion, nroCuota $whereLimite");
  
  if ($dni != null)
    $consulta->bindParam(':dni',$dni);
  /*
  if ($tipoItem != null)
    $consulta->bindParam(':tipoItem',$tipoItem);
  */
  if ($fchCreacion != null)
    $consulta->bindParam(':fchCreacion',$fchCreacion);
  
  
  if ($monto != null)
    $consulta->bindParam(':monto',$monto);  
  
  
  if ($fchQuincena != null)
    $consulta->bindParam(':fchQuincena',$fchQuincena);
  
  
  if ($pagado != null)
    $consulta->bindParam(':pagado',$pagado);
    
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarHistoricoocurrencias($db,$dni,$tipoOcurrencia = null,$fchCreacion = null,$monto = null,$pagado = null,$descripcion = null,$fchQuincena = null,$fchDespacho = null,$limite = null){
  $limite = str_replace(";","",$limite);
  $limite = str_replace("'","",$limite);
  if ($dni == null)
    $whereDni = '';
  else
    $whereDni = " AND `ocurrenciaconsulta`.`idTrabajador`  = :dni ";
  
  if ($tipoOcurrencia == null)
    $whereTipoOcurrencia = '';
  else {
  	$arrEliminar = array('*',';','Select','Show','?','=');
  	$tipoOcurrencia = str_replace($arrEliminar, '', $tipoOcurrencia);
  	$whereTipoOcurrencia = "  AND `ocurrenciaconsulta`.`tipoOcurrencia` IN ($tipoOcurrencia) ";
  }
  
  if ($fchCreacion == null)
    $whereFchCreacion = '';
  else
    $whereFchCreacion = " AND `ocurrenciaconsulta`.`fchCreacion` = :fchCreacion ";
  
  if ($monto == null)
    $whereMonto = '';
  else
    $whereMonto = " AND `montoCuota` = :monto ";
  
  if ($descripcion == null)
    $whereDescrip = '';
  else {
    $novalidos = array(";","'");
    $descripcion =  str_replace($novalidos,"",$descripcion);
    $whereDescrip = " AND `descripcion` like '$descripcion%'";
  }
  
  if ($fchQuincena == null)
    $whereFchQuincena = '';
  else
    $whereFchQuincena = " AND `ocurrenciaconsulta`.`fchQuincena` = :fchQuincena ";
  
  if ($fchDespacho == null)
    $whereFchDespacho = '';
  else
    $whereFchDespacho = " AND `ocurrenciaconsulta`.`fchDespacho` = :fchDespacho ";
    
  if ($pagado == null)
    $wherePagado = '';
  else
    $wherePagado = " AND `pagado` = :pagado ";  
  
  if($limite)
    $whereLimite = "LIMIT $limite";
  else
    $whereLimite = '';  

  $consulta = $db->prepare("SELECT `ocurrenciaconsulta`.`fchDespacho`, `trabajador`.`idTrabajador`, `ocurrenciaconsulta`.`correlativo`, `ocurrenciaconsulta`.`tipoOcurrencia`, `ocurrenciaconsulta`.`descripcion`, `ocurrenciaconsulta`.`montoTotal`,`nroCuota`,`nroCuotas`,`pagado`, `montoCuota`, concat(apPaterno,' ',apMaterno,' ',nombres) AS nombCompleto, `fchQuincena`, `fchPago`, `ocurrenciaconsulta`.`fchCreacion`, `ocurrencia`.`tipoDoc` , `ocurrencia`.`nroDoc` from ocurrencia, `ocurrenciaconsulta`, `trabajador` where  ocurrencia.fchDespacho = `ocurrenciaconsulta`.fchDespacho AND  ocurrencia.correlativo = `ocurrenciaconsulta`.correlativo AND  ocurrencia.tipoOcurrencia = `ocurrenciaconsulta`.tipoOcurrencia AND  ocurrencia.descripcion = `ocurrenciaconsulta`.descripcion AND ocurrencia.montoTotal = `ocurrenciaconsulta`.montoTotal AND `ocurrenciaconsulta`.`idTrabajador`=`trabajador`.`idTrabajador`  $whereDni $whereTipoOcurrencia $whereDescrip $whereMonto $wherePagado $whereFchCreacion $whereFchQuincena $whereFchDespacho ORDER BY nombCompleto, `ocurrenciaconsulta`.fchDespacho, montoCuota, tipoOcurrencia, descripcion, nroCuota $whereLimite");
  
  //$consulta = $db->prepare("SELECT `fchDespacho`, `trabajador`.`idTrabajador`, `correlativo`, `tipoOcurrencia`,`descripcion`,`montoTotal`,`nroCuota`,`nroCuotas`,`pagado`, `montoCuota`, concat(apPaterno,' ',apMaterno,' ',nombres) AS nombCompleto, `fchQuincena`, `fchPago`, `ocurrenciaconsulta`.`fchCreacion` from `ocurrenciaconsulta`, `trabajador` where `ocurrenciaconsulta`.`idTrabajador`=`trabajador`.`idTrabajador` $whereDni $whereTipoOcurrencia $whereDescrip $whereMonto $wherePagado $whereFchCreacion $whereFchQuincena $whereFchDespacho ORDER BY nombCompleto, `ocurrenciaconsulta`.fchDespacho, montoCuota, tipoOcurrencia, descripcion, nroCuota $whereLimite");

  if ($dni != null)
    $consulta->bindParam(':dni',$dni);  
  /*if ($tipoOcurrencia != null)
    $consulta->bindParam(':tipoOcurrencia',$tipoOcurrencia);*/
  if ($fchCreacion != null)
    $consulta->bindParam(':fchCreacion',$fchCreacion);
  if ($monto != null)
    $consulta->bindParam(':monto',$monto);  
  
  //if ($descripcion != null)
  //  $consulta->bindParam(':descrip',$descripcion);
  
  if ($fchQuincena != null)
    $consulta->bindParam(':fchQuincena',$fchQuincena);
  if ($fchDespacho != null)
    $consulta->bindParam(':fchDespacho',$fchDespacho);
  if ($pagado != null)
    $consulta->bindParam(':pagado',$pagado);
    
  $consulta->execute();
  return $consulta->fetchAll();
}

function generarQuincenaDetalle($db,$nombreProceso,&$fchQuincena){
  $anhio = $_SESSION['Anhio'];
  $aux = $_SESSION['Quincena'];
  $mes = strtok($aux, '-');
  $quin = strtok('-');
  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28';

  //Elimina cualquier registro previo
  $elimina = $db->prepare("DELETE FROM `quincenadetalle` WHERE `quincena` = :quincena ");
  $elimina->bindParam(':quincena',$fchQuincena);
  $elimina->execute(); 


  //$lineaTrabajadoresActivos = buscarTrabajadores($db,' WHERE 1 ');    //verif en trabajador
  $lineaTrabajadoresActivos = buscarTrabEstadoQuincena($db); //verif en trabajador
  $lineaDespachos  = resumenDespachosMarcados($db,$anhio,$mes,$quin); //verif en despachopersonal
  $lineaOcurrencia = resumenOcurrenciaMarcados($db,$anhio,$mes,$quin);//verif en ocurrenciaconsulta
  $lineaVarios = resumenVariosMarcados($db,$anhio,$mes,$quin);              //verif en prestamo
  $lineaPagoPrestamos = resumenPagoPrestamosMarcados($db,$anhio,$mes,$quin);//verif en prestamodetalle

  $lineaContab = resumenDsctosAporteEtcContab($db,$anhio,$mes,$quin);    //verif en quincenadetallecontab
  
  // REaliza los cálculos para la quincena para poder cerrarla 
  $data[] = array();
	foreach($lineaTrabajadoresActivos as $item){
	  $idTrab = $item['idTrabajador'];
	  $data[$idTrab]['nombreCompleto'] = $item['nombreCompleto'];
	  $data[$idTrab]['categoria'] = $item['categTrabajador'];
	  $data[$idTrab]['modoSueldo'] = $item['modoSueldo'];
	  $data[$idTrab]['asumeEmpresa'] = $item['asumeEmpresa'];
	  $data[$idTrab]['renta5ta'] = $item['renta5ta']/2; //Esto es lo que se está añadiendo 130118
	  $data[$idTrab]['viajes'] = $data[$idTrab]['valor'] = $data[$idTrab]['hraExtra'] = 0;
	  $data[$idTrab]['pagoMovilidad'] = $data[$idTrab]['pagoCajaChica'] = $data[$idTrab]['pagoDescuento'] = $data[$idTrab]['pagoGratificacion'] = $data[$idTrab]['pagoVacaciones'] = $data[$idTrab]['pagoCts'] = 0;
	  $data[$idTrab]['pagoPension'] = $data[$idTrab]['pagoEsSalud'] = $data[$idTrab]['pagoPrestamo'] = 0;
	  $data[$idTrab]['reciboPrestamo'] = $data[$idTrab]['reembolso'] = $data[$idTrab]['bono'] = 0;
	  $data[$idTrab]['movilidadSuped'] = $data[$idTrab]['adelanto'] = $data[$idTrab]['fondoGarantia'] = 0;
	  $data[$idTrab]['compra'] = $data[$idTrab]['indemnizParticip'] = $data[$idTrab]['devGarantia'] = 0;  
	  $data[$idTrab]['domFeriado'] = $data[$idTrab]['dsctoTrabaj'] = $data[$idTrab]['ajuste'] = 0;
	  $data[$idTrab]['asigFamiliar'] = 0; // ($item['categTrabajador'] == '5ta' && ($item['modoSueldo'] == 'Preferencial' || $item['modoSueldo'] == 'Variable' )?$item['asignacionFamiliar']/2:0); //add 140818
	  $data[$idTrab]['servAdicional'] = $data[$idTrab]['descMedico'] = $data[$idTrab]['inasistencia'] = 0;
    $data[$idTrab]['diasTrabaj'] = $data[$idTrab]['ajuste'] = 0;
	  $data[$idTrab]['licGoce'] = $data[$idTrab]['remVacac'] = 0;
	  $data[$idTrab]['modoContratacion'] = $item['modoContratacion'];
	  $data[$idTrab]['tipoTrabajador'] = $item['tipoTrabajador'];
	  $data[$idTrab]['entidadPension'] = $item['entidadPension'];
	}
	
	foreach($lineaDespachos as $item) {
    $idTrab = $item['idTrabajador'];
	  $data[$idTrab]['nombreCompleto'] = $item['nombreCompleto'];
	  $data[$idTrab]['categoria'] = $item['categTrabajador'];
	  $data[$idTrab]['viajes'] = $item['viajes'];
	  //$data[$idTrab]['valor'] = $item['valorRol'];
    //CAmbiado para hacer coincidir
    if($data[$idTrab]['categoria'] != 'Practicante') $data[$idTrab]['valor'] = $item['valorRol'];
	  $data[$idTrab]['diasTrabaj'] = $item['dias'];
	  $data[$idTrab]['hraExtra'] += $item['valorHraExtra'];
	  //echo "Valor :".$data[$idTrab]['valor'];
	}
	
	foreach($lineaOcurrencia as $item) {
	  $idTrab = $item['idTrabajador'];
	  $data[$idTrab]['pagoMovilidad'] = $item['pagoMovilidad'];
	  $data[$idTrab]['pagoCajaChica'] = $item['pagoCajaChica'];
	  $data[$idTrab]['pagoDescuento'] = $item['pagoDescuento'];
	}
	
	foreach($lineaVarios as $item) {
	  $idTrab = $item['idTrabajador'];
	  $data[$idTrab]['pagoGratificacion'] = $item['pagoGratificacion'];
	  $data[$idTrab]['pagoVacaciones'] = $item['pagoVacaciones'];
	  $data[$idTrab]['pagoPension'] = $item['pnsNacONP'] + $item['pnsPriComis'] + $item['pnsPriPrima'] + $item['pnsPriAport'];
	  $data[$idTrab]['pagoEsSalud'] = $item['pagoEsSalud'];
	  $data[$idTrab]['reciboPrestamo'] = $item['reciboPrestamo'];
	  $data[$idTrab]['reembolso'] = $item['reembolso'];
	  $data[$idTrab]['bono'] = $item['bono'] + $item['bonoProducMes'];
	  $data[$idTrab]['movilidadSuped'] = $item['movilidad'] + $item['movilAsig'];
	  $data[$idTrab]['adelanto'] = $item['adelanto'];
	  $data[$idTrab]['indemnizParticip'] = $item['pagoParticipacion'] + $item['pagoIndemnizacion']; //edit 111216 
	  $data[$idTrab]['devGarantia'] = $item['devGarantia'];//add 111216
	  $data[$idTrab]['domFeriado'] = $item['domFeriado'];  //add 111216
	  $data[$idTrab]['hraExtra'] += $item['hraExtra'];
	  $data[$idTrab]['asigFamiliar'] = $item['asigFam'];

      //170116 se añade para solucionar diferencias al cerrar
	  $data[$idTrab]['servAdicional'] = $item['servAdicional'];
	  $data[$idTrab]['descMedico'] = $item['descMedico'];
	  $data[$idTrab]['inasistencia'] = $item['inasistencia'];
	  $data[$idTrab]['licGoce'] = $item['licGoce'];
	}
		
	foreach($lineaPagoPrestamos as $item) {
	  $idTrab = $item['idTrabajador'];
	  $data[$idTrab]['pagoPrestamo'] = $item['pagoPrestamo'];
	  $data[$idTrab]['fondoGarantia'] = $item['fondoGarantia'];
	  $data[$idTrab]['compra'] = $item['compra'];
	  $data[$idTrab]['dsctoTrabaj'] = $item['dsctoTrabaj'];
	  $data[$idTrab]['ajuste'] = $item['ajuste'];
    //Añadido LAEM 220106
    if (isset($item['dsctoUnif'])) $data[$idTrab]['dsctoTrabaj'] += $item['dsctoUnif'];
	}

	//Aqui viene valores que se jalan por calculo para contabilidad
	foreach($lineaContab as $item) {
	  $idTrab = $item['idTrabajador'];
	  $data[$idTrab]['pagoPension'] = $item['onp'] + $item['comision'] + $item['seguro'] + $item['aporte'];
	  $data[$idTrab]['pagoEsSalud'] = $item['esSaludVid'];
	  $data[$idTrab]['renta5ta'] = $item['renta5ta'];
    $data[$idTrab]['bonoProd'] = $item['bonoProd']; //Se usa? LAEM 220106
    $data[$idTrab]['remVacac'] = $item['remVacac'];//Añadido LAEM 220106
	  //$data[$idTrab]['movilidadSuped'] = $item['movilidad'];
	}
	//Fin de aqui viene valores ...
		
  $diferencia = $totDiferencia = 0;
  $totTotDiario = $totTotDif = $totTotDias =   $totEsSalud = 0; // $totTotDif ya no debe ir ??
	$totValor =  $totMovilidad = $totCajaChica = $totPension = $totTotAPagar  = $totReciboPrest = $totDescuento =  0;
	$totReembolso = $totBono = $totMovilidadSuped  = $totFondoGarantia = $totPagoPrestyAdel = $totCompra = $totHraExtra = 0;
	$totGratificacion = $totVacaciones = $totIndemPartic = $totAjuste = 0;
	$totDevGarantia = $totDomFeriado = 0; // add 111216
  $totServAdicional = $totDescMedico = $totInasistencia = $totLicGoce = 0;

	$contador = 0;
	$totRenta5ta = 0; //Esto es lo que se está añadiendo 130118
	$totAsigFamiliar = 0;
	foreach($lineaTrabajadoresActivos as $item) {
	  if ($item['categTrabajador'] == 'Tercero') continue;
	  $idTrab = $item['idTrabajador'];
	  $contador++;
	  $totDias = 0;
	  $totDiario =  $totDif = $minimoDiario = 0;  // $totDif ya no debe ir ??
	  $totMovilidad += $data[$idTrab]['pagoMovilidad'];    
	  $totCajaChica += $data[$idTrab]['pagoCajaChica'];
	  $totRenta5ta += $data[$idTrab]['renta5ta'];   //Esto es lo que se está añadiendo 130118
	  $totDescuento += $data[$idTrab]['pagoDescuento'] + $data[$idTrab]['dsctoTrabaj'];                         
	  $totGratificacion += $data[$idTrab]['pagoGratificacion'];
	  $totVacaciones += $data[$idTrab]['pagoVacaciones'];
	  $totReciboPrest += $data[$idTrab]['reciboPrestamo'];
	  $totReembolso += $data[$idTrab]['reembolso'];
	  $totIndemPartic += $data[$idTrab]['indemnizParticip'];
	  $totDevGarantia += $data[$idTrab]['devGarantia']; // add 111216
    $totDomFeriado += $data[$idTrab]['domFeriado'];   // add 111216	  
	  $totPension += $data[$idTrab]['pagoPension'];
	  $totEsSalud += $data[$idTrab]['pagoEsSalud'];
	  $totPagoPrestyAdel += $data[$idTrab]['pagoPrestamo'] + $data[$idTrab]['adelanto'];
	  $totFondoGarantia += $data[$idTrab]['fondoGarantia'];
	  $totCompra  += $data[$idTrab]['compra'];
	  $totBono +=  $data[$idTrab]['bono'];
	  $totMovilidadSuped +=  $data[$idTrab]['movilidadSuped'];//esto es solo movilidad del supervisor
	  //$totAdelanto +=  ;
	  //$totDsctoTrabaj += ;
	  $totAjuste += $data[$idTrab]['ajuste'];
	  $totHraExtra += $data[$idTrab]['hraExtra'];   
    $totTotDiario += $totDiario;
	  $totTotDias += $totDias;
	  $totAsigFamiliar += $data[$idTrab]['asigFamiliar'];
    
    if($data[$idTrab]['modoSueldo'] == 'Preferencial') {
      $pagoEsSalud = 0;
      $pagoPension = 0;
    } else if ($item['categTrabajador'] == '5ta' && $data[$idTrab]['modoSueldo'] == 'Fijo' && isset($item['remuneracionBasica'])){
        $auxTrabajado = $data[$idTrab]['valor'];
        /*  201012 Los administrativos pasan a ser igual que los demás en cuanto a pago quincenal
        $data[$idTrab]['valor'] =  ($item['tipoTrabajador'] == 'Administrativo')?$item['remuneracionBasica']:$item['remuneracionBasica']/2;*/
        $data[$idTrab]['valor'] = $item['remuneracionBasica']/2;
        $pagoEsSalud = $data[$idTrab]['pagoEsSalud'];
        $pagoPension = $data[$idTrab]['pagoPension'];
        $renta5ta = $data[$idTrab]['renta5ta'];
    } else if ($data[$idTrab]['modoSueldo'] == 'Variable') { 
      if ($data[$idTrab]['asumeEmpresa'] == 'Si') {
        $pagoEsSalud = 0;
        $pagoPension = 0;
        $renta5ta = 0;
      } else {
        $pagoEsSalud = $data[$idTrab]['pagoEsSalud'];
        $pagoPension = $data[$idTrab]['pagoPension'];
        $renta5ta = $data[$idTrab]['renta5ta'];
      }
    } else {
        $pagoEsSalud = $data[$idTrab]['pagoEsSalud'];
        $pagoPension = $data[$idTrab]['pagoPension'];
        $renta5ta = $data[$idTrab]['renta5ta'];
    }

    //Es un ajuste para una nueva categoría de trabajador  
    if($item['categTrabajador'] == 'Practicante'){
      $data[$idTrab]['valor'] = $item['remuneracionBasica']/2;
      $pagoEsSalud = 0;
      $pagoPension = 0;
      $renta5ta = 0;
    }

    $remVacac = isset($data[$idTrab]['remVacac']) ? $data[$idTrab]['remVacac'] : 0;

    if($item['categTrabajador'] == '5ta' && $item['modoSueldo'] == 'Fijo'  && isset($item['remuneracionBasica'])){
      if ($auxTrabajado > round( ($item['remuneracionBasica'] + $item['asignacionFamiliar'])/2 - ($pagoPension + $pagoEsSalud + $renta5ta),2) ){
        $diferencia = $auxTrabajado + $pagoPension + $pagoEsSalud + $renta5ta - ($item['remuneracionBasica'] + $item['asignacionFamiliar'])/2; 
      } else if($auxTrabajado > $data[$idTrab]['valor'])
        $diferencia = $auxTrabajado - $data[$idTrab]['valor'] ;
      else
        $diferencia = 0;


    } else $diferencia = 0;

    if ($item['modoSueldo'] == 'Fijo' && $item['tipoTrabajador'] == 'Administrativo') $diferencia = 0;


    //Ajuste para el trabajador que sale de vacaciones LAEM 210106
    if ( $data[$idTrab]['remVacac'] > 0){
      $diferencia = 0;
      $data[$idTrab]['valor'] = 0;
    }

    $totDiferencia += $diferencia;
    
    $totValor += $data[$idTrab]['valor'];  
    $totAPagar =   $data[$idTrab]['valor'] + $diferencia + $data[$idTrab]['pagoMovilidad'] + $data[$idTrab]['pagoCajaChica'] + $data[$idTrab]['pagoGratificacion'] + $data[$idTrab]['reembolso'] + $data[$idTrab]['bono'] + $data[$idTrab]['movilidadSuped'] + $data[$idTrab]['indemnizParticip'] + $data[$idTrab]['devGarantia'] + $data[$idTrab]['domFeriado'] + $data[$idTrab]['servAdicional'] + $data[$idTrab]['descMedico'] + $data[$idTrab]['licGoce'] + $remVacac + $data[$idTrab]['ajuste'] + $data[$idTrab]['hraExtra'] + $data[$idTrab]['asigFamiliar'] - ( $data[$idTrab]['pagoDescuento'] + $data[$idTrab]['pagoCts'] + $pagoEsSalud + $pagoPension + $data[$idTrab]['renta5ta']+ $data[$idTrab]['pagoPrestamo'] + $data[$idTrab]['fondoGarantia'] + $data[$idTrab]['compra'] + $data[$idTrab]['dsctoTrabaj'] + $data[$idTrab]['adelanto'] +  $data[$idTrab]['inasistencia']); // edit 140818 //180812

   $totTotAPagar += $totAPagar;

   $user = $_SESSION['usuario'];  
   
  $inserta = $db->prepare("INSERT INTO `quincenadetalle` (`quincena`, `idTrabajador`, `tipoTrabajador`, `categoria`, `modoSueldo`, `entidadPension`, `modoContratacion`, `remunBasica`, `asigFamiliar`, `pagoAsigFamiliar`, `renta5ta`, `diasTrabaj`, `valor`,`diferencia`, `remVacac`, `cts`, `pension`, `esSalud`, `totPagar`, `fchCreacion`, `usuario`, `nombreProceso`) VALUES (:quincena, :idTrabajador, :tipoTrabajador, :categoria, :modoSueldo, :entidadPension, :modoContratacion, :remunBasica, :asigFamiliar, :pagoAsigFamiliar ,:renta5ta, :diasTrabaj, :valor, :diferencia , :remVacac, :cts, :pension, :esSalud, :totPagar, NOW(), :usuario, :nombreProceso)");
  
  $inserta->bindParam(':quincena',$fchQuincena);
  $inserta->bindParam(':idTrabajador',$idTrab);
  $inserta->bindParam(':tipoTrabajador',$data[$idTrab]['tipoTrabajador']);
  $inserta->bindParam(':categoria',$data[$idTrab]['categoria']);
  $inserta->bindParam(':modoSueldo',$data[$idTrab]['modoSueldo']);
  $inserta->bindParam(':entidadPension',$data[$idTrab]['entidadPension']);
  $inserta->bindParam(':modoContratacion',$data[$idTrab]['modoContratacion']);
  $inserta->bindParam(':remunBasica',$item['remuneracionBasica']);
  $inserta->bindParam(':asigFamiliar',$item['asignacionFamiliar']);
  $inserta->bindParam(':pagoAsigFamiliar',$data[$idTrab]['asigFamiliar']);
  $inserta->bindParam(':renta5ta',$data[$idTrab]['renta5ta']);
  $inserta->bindParam(':diasTrabaj',$data[$idTrab]['diasTrabaj']);
  $inserta->bindParam(':valor',$data[$idTrab]['valor']);
  $inserta->bindParam(':diferencia',$diferencia);
  $inserta->bindParam(':remVacac',$data[$idTrab]['remVacac']);
  $inserta->bindParam(':cts',$data[$idTrab]['pagoCts']);
  $inserta->bindParam(':pension',$data[$idTrab]['pagoPension']);
  $inserta->bindParam(':esSalud',$data[$idTrab]['pagoEsSalud']);
  $inserta->bindParam(':totPagar',$totAPagar);
  $inserta->bindParam(':nombreProceso',$nombreProceso);
   
  //$inserta->bindParam(':placa',$placa);
  $inserta->bindParam(':usuario',$user);
  $inserta->execute();
  }   
};

function  buscarHrasExtra($db,$idTrab,$idCliente,$cuenta,$pagado,$anho,$mes,$dia){
  $fch = $anho."-".$mes."-".$dia;
  $fchAtras = retrocedeFecha($fch,-60);
  $where = " AND year(`despacho`.`fchDespacho`) = '$anho' AND month(`despacho`.`fchDespacho`) = '$mes' ";
  if ($dia <= '15')
  	$where .= " And  day(`despacho`.`fchDespacho`) <= 15 ";
  else
  	$where .= " And  day(`despacho`.`fchDespacho`) > 15 ";

  if ($idTrab != '')
   	$where .= " And  `prestamo`.`idTrabajador` = :idTrab ";
  if ($idCliente != '')
  	$where .= " And  `despacho`.`idCliente` = :idCliente ";
  if ($cuenta != '')
  	$where .= " And  `despacho`.`cuenta` like :cuenta ";
  if ($pagado != '')
  	$where .= " And  `prestamo`.`entregado` = :pagado ";
  
  $consulta = $db->prepare("SELECT `prestamo`.`idTrabajador`, concat( `appaterno`, ' ', `apmaterno`, ' ', `nombres` ) AS `nombreCompleto`, `descripcion`, `tipoItem`, `monto`, `prestamo`.`fchCreacion`, `hraCreacion`, `fchDespacho`, `correlativo`, `guiaCliente`, `idCliente`, `cuenta`, `cliente`.`nombre`, `prestamo`.`entregado`,`prestamo`.`fchQuincena`, `despacho`.`hraInicio`, `despacho`.`hraFinCliente`, `despacho`.`hraFin` FROM `prestamo` LEFT JOIN `despacho` ON concat( `despacho`.`fchDespacho`, '-', `despacho`.`correlativo` ) = trim( right( `descripcion`, 13 ))  AND `despacho`.`fchDespacho` >= '$fchAtras'  LEFT JOIN `cliente` ON `cliente`.`idruc` = `despacho`.`idcliente`, `trabajador` WHERE `trabajador`.`idtrabajador` = `prestamo`.`idtrabajador` AND `tipoItem` LIKE 'hraExtra' $where ");
  //$consulta->bindParam(':fchIni',$fchIni);
  if ($idTrab != '') $consulta->bindParam(':idTrab',$idTrab);
  if ($idCliente != '') $consulta->bindParam(':idCliente',$idCliente);
  if ($cuenta != '') $consulta->bindParam(':cuenta',$cuenta);
  if ($pagado != '') $consulta->bindParam(':pagado',$pagado);
  //if ($fchDespacho != '') $consulta->bindParam(':fchDespacho',$fchDespacho);  
  $consulta->execute();
  return $consulta->fetchAll();
}


function procesoInsertarEnPrestamo($db,$usuario,$idTrabajador,$descripcion,$monto,$nroPartes,$fch,$tipoItem,$opcion,$nroDoc,$categ=NULL){ 
  $mntCuota = round($monto/$nroPartes,2);
 
  $subTipoItem = "";
	if (substr($tipoItem,0,11) == "DsctoTrabaj" ){ 
	 	$subTipoItem = substr($tipoItem,12,100);
	  $tipoItem = "DsctoTrabaj";
	}

  if ($tipoItem != 'Prestamo' AND $tipoItem != 'FondoGarantia' AND $tipoItem != 'Compra' AND $tipoItem != 'Ajuste' AND $tipoItem != 'DsctoTrabaj' AND $tipoItem != 'DsctoUnif'  ){//ojo que la condicion esta invertida
    insertaRegEnPrestamo($db, $idTrabajador,$descripcion,$monto,$nroPartes,$fch,$tipoItem,$usuario,$nroDoc,$categ);     
  } else { //Esta es la opcion para los que se dividen en varias partes como por ejemplo un prestamo o una compra
    insertaRegEnPrestamo($db, $idTrabajador,$descripcion,$monto,$nroPartes,null,$tipoItem,$usuario,$nroDoc,$categ); 

    for ($j = 1; $j <= $nroPartes; $j++ ){
      $anio = substr($fch,0,4);
      $mes = substr($fch,5,2);
      $dia = substr($fch,8,2);

      $inserta = $db->prepare("INSERT INTO `prestamodetalle` (`monto`, `nroCuota`, `idTrabajador`, `tipoItem`, `subTipoItem`, `descripcion`, `fchPago`, `montoCuota`, `pagado`, `usuario`, `fchCreacion`, `hraCreacion`) VALUES (:monto, :nroCuota, :idTrabajador, :tipoItem, :subTipoItem, :descripcion, :fchPago, :mntCuota, 'No', :usuario, CURDATE(), CURTIME());");
      $inserta->bindParam(':monto',$monto);
      //$inserta->bindParam(':correlativo',$correlativo);
      //$inserta->bindParam(':mntTotal',$mntTotal);
      $inserta->bindParam(':nroCuota',$j);
      $inserta->bindParam(':idTrabajador',$idTrabajador);
      $inserta->bindParam(':tipoItem',$tipoItem);
      $inserta->bindParam(':subTipoItem',$subTipoItem);
      $inserta->bindParam(':descripcion',$descripcion);
      $inserta->bindParam(':fchPago',$fch);
      $inserta->bindParam(':mntCuota',$mntCuota);
      $inserta->bindParam(':usuario',$usuario);
      $inserta->execute();
      $fch = date("Y-m-d",mktime(0,0,0,$mes,$dia+15,$anio));
    }     
    $actualiza = $db->prepare("UPDATE `prestamodetalle` SET `descripcion` = replace(`descripcion`,'&#209;','Ñ'), `descripcion` = replace(`descripcion`,'&#241;','ñ')  WHERE `idTrabajador` = :idTrabajador AND  `fchCreacion` = curdate() AND  `monto` = :monto  AND `tipoItem` = :tipoItem ");
    $actualiza->bindParam(':idTrabajador',$idTrabajador);
    $actualiza->bindParam(':monto',$monto);
    $actualiza->bindParam(':tipoItem',$tipoItem);
    $actualiza->execute();   
  }  
  $cadena=($opcion == 'datosplanilla')?"controlador=trabajadores&accion=verificarplanilla&dni=$dni":"controlador=trabajadores&accion=listar";
  
}

function actualizaUnVencimTrab($db,$dni,$fchInicio,$nombre){
  $actualiza = $db->prepare("UPDATE `trabajadorlicencias` SET `estado` = 'Inactivo' WHERE `idTrabajador` = :dni AND `fchInicio` = :fchInicio AND `nombre` = :nombre");
  $actualiza->bindParam(':dni',$dni);
  $actualiza->bindParam(':fchInicio',$fchInicio);
  $actualiza->bindParam(':nombre',$nombre);
  $actualiza->execute();
}

function eliminaUnVencimTrab($db,$dni,$fchInicio,$tipoAlerta){

  $actual = buscarTrabajadorUnVencimiento($db,$dni,$fchInicio,$tipoAlerta);
  foreach ($actual as $item){
    $archivo = "imagenes/data/trabajador/".$item['escaneo'];
    unlink($archivo);
  }

  $elimina = $db->prepare("DELETE FROM `trabajadorlicencias` WHERE `idTrabajador` = :dni AND `fchInicio` = :fchInicio AND `nombre` = :tipoAlerta");
  $elimina->bindParam(':dni',$dni);
  $elimina->bindParam(':fchInicio',$fchInicio);
  $elimina->bindParam(':tipoAlerta',$tipoAlerta);
  $elimina->execute();

  if ($elimina->rowCount() > 0){
  	$descripcion = utf8_encode("Se eliminó el vencimiento de la licencia del trabajador $dni, con fecha inicio $fchInicio");
  	logAccion($db,$descripcion, $dni,null);  
  }
}

function buscarAlertasLicenciasVencidas($db){

	$consulta = $db->prepare("SELECT `trablic`.`idTrabajador`, concat(nombres,' ',apPaterno,' ',apMaterno) as nombCompleto ,`trablic`.`fchInicio`, `trablic`.`nombre`, `fchFin`, datediff(`fchFin`, curdate()) as falta, `trablic`.`plazo`, `alertastipo`.`descripcion` FROM `trabajadorlicencias` AS trablic, `trabajador`, `alertastipo` WHERE `trablic`.idTrabajador = `trabajador`.idTrabajador AND    `trablic`.`nombre` = `alertastipo`.`idAlerta` AND
       datediff(`fchFin`, curdate()) <= `trablic`.plazo AND    `trablic`.`estado` = 'Activo' AND    `trabajador`.`estadoTrabajador` = 'Activo'
    UNION 
    SELECT `trabdocs`.`idTrabajador`, concat(nombres,' ',apPaterno,' ',apMaterno) as nombCompleto ,`trabdocs`.`fchIni`, `trabdocs`.`tipoDocTrab`, `fchFin`, datediff(`fchFin`, curdate()) as falta, `trabajtiposdocum`.`plazo`, `trabajtiposdocum`.`descripTipoDoc` FROM  `trabajdocumentos` AS trabdocs, `trabajador`, `trabajtiposdocum` WHERE `trabdocs`.idTrabajador = `trabajador`.idTrabajador AND  `trabdocs`.`tipoDocTrab` = `trabajtiposdocum`.`idTipoDoc` AND
       datediff(`fchFin`, curdate()) <= `trabajtiposdocum`.plazo AND   `trabdocs`.`estado` IN ('Activo','Predeterminado') AND
      `trabajador`.`estadoTrabajador` = 'Activo' ORDER BY falta");



  //$consulta = $db->prepare("SELECT `trablic`.`idTrabajador`, concat(nombres,' ',apPaterno,' ',apMaterno) as nombCompleto ,`trablic`.`fchInicio`, `trablic`.`nombre`, `fchFin`, datediff(`fchFin`, curdate()) as falta, `trablic`.`plazo`, `alertastipo`.`descripcion` FROM `trabajadorlicencias` AS trablic, `trabajador`, `alertastipo` WHERE `trablic`.idTrabajador = `trabajador`.idTrabajador AND `trablic`.`nombre` = `alertastipo`.`idAlerta` AND datediff(`fchFin`, curdate()) <= `trablic`.plazo AND `trablic`.`estado` = 'Activo' AND `trabajador`.`estadoTrabajador` = 'Activo' ORDER BY falta ");
  $consulta->execute();
  return $consulta->fetchAll();
}



function buscarUltDespacho($db,$idTrab){
  $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `pagado` FROM `despachopersonal` WHERE `idTrabajador` LIKE :idTrab ORDER BY `fchDespacho` DESC Limit 1");
  $consulta->bindParam(':idTrab',$idTrab);
  $consulta->execute();
  return $consulta->fetchAll();
};

function cambiarTrabEstado($db,$idTrabajador,$estado){
  $actualiza = $db->prepare("UPDATE `trabajador` SET `estadoTrabajador` = :estado  WHERE `idTrabajador` = :idTrabajador ");
  $actualiza->bindParam(':idTrabajador',$idTrabajador);
  $actualiza->bindParam(':estado',$estado);
  $actualiza->execute();
};

function verificarTrabajadorVigencia($db,$dni,$nombre){

	if ($nombre == "DNI"){

		$consulta = $db->prepare("SELECT  `fchFin`  FROM `trabajdocumentos` WHERE `idTrabajador` = :dni AND `estado` = 'Predeterminado'");
    $consulta->bindParam(':dni',$dni);
    //$consulta->bindParam(':nombre',$nombre);
    $consulta->execute();
    $rst = $consulta->fetchAll();
    $fchFin = '0';
    foreach($rst as $item) {
    	$fchFin = $item['fchFin'];
    }
    if ($fchFin == '0' || $fchFin < Date("Y-m-d"))
    	return 0;
    else
    	return 1;


	} else {
		$consulta = $db->prepare("SELECT  `fchFin`  FROM `trabajadorlicencias` WHERE `idTrabajador` = :dni AND `nombre` = :nombre AND `estado` = 'Activo'");
    $consulta->bindParam(':dni',$dni);
    $consulta->bindParam(':nombre',$nombre);
    $consulta->execute();
    $rst = $consulta->fetchAll();
    $fchFin = '0';
    foreach($rst as $item) {
    	$fchFin = $item['fchFin'];
    }
    if ($fchFin == '0' || $fchFin < Date("Y-m-d"))
    	return 0;
    else
    	return 1;
  }


}

function buscarDsctosPendientes($db,$dni,$fchPago = null,$tipoItem = null,$nroCuota = NULL,$mntCuota = NULL,$mntTotal = NULL,$descrip = NULL){
  $where = " AND estadoTrabajador = 'Activo' ";
  $whereTipoOcurrencia = $whereTipoItem =  $whereNroCuota = $whereNroCuotaPrs ="";
  $tipoItem = str_replace(array("*", ";","?","\t","\r","SHOW","UPDATE","RESTART"),"", $tipoItem);
  $fchPago = str_replace(array("*", ";","?","\t","\r","SHOW","UPDATE","RESTART"),"", $fchPago);
  $nroCuota = str_replace(array("*", ";","?","\t","\r","SHOW","UPDATE","RESTART"),"", $nroCuota);
  $mntCuota = str_replace(array("*", ";","?","\t","\r","SHOW","UPDATE","RESTART"),"", $mntCuota);
  $mntTotal = str_replace(array("*", ";","?","\t","\r","SHOW","UPDATE","RESTART"),"", $mntTotal);
  $descrip = str_replace(array("*", ";","?","\t","\r","SHOW","UPDATE","RESTART"),"", $descrip);
 
  if ($dni != ''){
  	$where .= " AND trabajador.idTrabajador = :dni ";
  }
  if ($tipoItem != null){
  	 $whereTipoOcurrencia = " AND `tipoOcurrencia` LIKE '$tipoItem' ";
  	 $whereTipoItem = "AND tipoItem LIKE '$tipoItem'";
  }
  if ($fchPago != null){
  	 $where .= " AND `fchPago` <=  '$fchPago' ";
  }

  if ($nroCuota != NULL){
  	$whereNroCuota .= " AND `nroCuota` = '$nroCuota'";
  	$whereNroCuotaPrs .= " AND `nroCuotas` = '$nroCuota' ";
  }

  if ($mntCuota != NULL){
  	$whereNroCuota .= " AND `montoCuota` = '$mntCuota'";
  	$whereNroCuotaPrs .= " AND `monto` = '$nroCuota' ";
  }

  if ($mntTotal != NULL){
  	$whereTipoOcurrencia = " AND `montoTotal` LIKE '$mntTotal' ";
  	$whereTipoItem = "AND monto LIKE '$mntTotal'";
  }

  if ($descrip != NULL){
  	$whereTipoOcurrencia = " AND `descripcion` LIKE '%$descrip%' ";
  	$whereTipoItem = "AND descripcion LIKE '%$descrip%'";
  }

  $consulta = $db->prepare("(SELECT concat(apPaterno,' ', apMaterno,' ',nombres) AS nombCompleto, `ocurrenciaconsulta`.`idTrabajador`,  estadoTrabajador, `fchPago`, `nroCuota`, `montoCuota`, `montoTotal`, `tipoOcurrencia` AS tipo, `descripcion`, `pagado` FROM `ocurrenciaconsulta`, trabajador WHERE `ocurrenciaconsulta`.idTrabajador = trabajador.idTrabajador  AND `tipoOcurrencia` LIKE 'descuento' AND `pagado` != 'Si' $where $whereTipoOcurrencia $whereNroCuota )
  union
  (SELECT concat(apPaterno,' ', apMaterno,' ',nombres) AS nombCompleto, `prestamodetalle`.`idTrabajador`, estadoTrabajador,`fchPago`, `nroCuota`,  `montoCuota`,  `monto` AS montoTotal,  `tipoItem` AS tipo,  `descripcion`, `pagado`  FROM `prestamodetalle`, trabajador WHERE `prestamodetalle`.idTrabajador = trabajador.idTrabajador  AND `tipoItem` IN ('prestamo', 'FondoGarantia', 'DsctoTrabaj', 'DsctoUnif','Compra') AND pagado != 'Si' $where $whereTipoItem $whereNroCuota)
  union
  (SELECT concat(apPaterno,' ', apMaterno,' ',nombres) AS nombCompleto,`prestamo`.`idTrabajador`,  estadoTrabajador,`fchPago`, `nroCuotas` AS `nroCuota`,  monto AS `montoCuota`, `monto` AS montoTotal,  `tipoItem` AS tipo,  `descripcion`, entregado AS `pagado`  FROM `prestamo`, trabajador WHERE `prestamo`.idTrabajador = trabajador.idTrabajador  AND `tipoItem` IN ('adelanto','reembolso') AND entregado  != 'Si' $where $whereTipoItem $whereNroCuotaPrs )
  ORDER BY nombCompleto, fchPago");

  if ($dni != '')
    $consulta->bindParam(':dni',$dni);

  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarTrabajVacac($db, $dni = null){
  $campos = "SELECT `altasbajas`.`idTrabajador`, concat(apPaterno, ' ', apMaterno, ' ', nombres) AS nombCompleto, estadoTrabajador , `fchAlta`, `fchBaja`, `vacacTotalAnhio` ";
  if ($dni == null){
  	$consulta = $db->prepare(" $campos FROM `altasbajas`, `trabajador` WHERE  `altasbajas`.idTrabajador = `trabajador`.idTrabajador AND estadoTrabajador = 'Activo'  AND( FchBaja IS NULL OR FchBaja > curdate() ) ORDER BY apPaterno, apMaterno, nombres");

  }	else {
  	$consulta = $db->prepare(" $campos FROM `altasbajas`, `trabajador` WHERE  `altasbajas`.idTrabajador = `trabajador`.idTrabajador  AND  `altasbajas`.`idTrabajador` = :dni  ORDER BY apPaterno, apMaterno, nombres");
  	$consulta->bindParam(':dni',$dni);
  }
  $consulta->execute();
  return $consulta->fetchAll();
}

function  generaNvoIdVacac($db){
  $anhio = Date("Y");
  $nvoId = "";
  $consulta = $db->prepare("SELECT `idVacaciones` FROM `trabajadorvacaciones` ORDER BY `idVacaciones` DESC limit 1");
  $consulta->execute();
  $result = $consulta->fetchAll();
  // Se obtiene el resultado de la consulta
  foreach($result as $fila) {
    $ultId = $fila['idVacaciones'];
    $anhioId = substr($ultId, 0,4);
    if ($anhioId == $anhio){
      $nvoId = 1*$ultId + 1;
    }
  }
  if ($nvoId == "")  $nvoId = $anhio."000001";
  return $nvoId;
}

function insertaDetalleVacac($db,$idVacac, $idTrab, $fchVacacIni, $fchInicio, $fchFin, $fchQuincena, $monto, $observacion, $usuario){
  $dias = diferDias($fchFin,$fchInicio);
  $inserta = $db->prepare("INSERT INTO `trabajadorvacaciones` (`idVacaciones`, `idTrabajador`, `fchVacacIni`, `fchInicio`, `fchFin`, `fchQuincena`, `monto`, `fchAprob`, `observacion`,`usuAprob`, `creacFch`, `creacUsuario`) VALUES (:idVacac, :idTrab, :fchVacacIni, :fchInicio, :fchFin, :fchQuincena, :monto, now(), :observacion, :usuario, curdate(), :usuario)");
  $inserta->bindParam(':idVacac',$idVacac);
  $inserta->bindParam(':idTrab',$idTrab);
  $inserta->bindParam(':fchVacacIni',$fchVacacIni);
  $inserta->bindParam(':fchInicio',$fchInicio);
  $inserta->bindParam(':fchFin',$fchFin);
  $inserta->bindParam(':fchQuincena',$fchQuincena);
  $inserta->bindParam(':monto',$monto);
  $inserta->bindParam(':observacion',$observacion);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();

  $actualiza = $db->prepare("UPDATE `trabajperiodvacac` SET `vacacTomado` = `vacacTomado` + $dias + 1, estadoPeriodo = if(`vacacTomado` = vacacTotal,'Cerrado',estadoPeriodo ) WHERE `idTrabajador` = :idTrab AND fchVacacIni = :fchVacacIni" );
  $actualiza->bindParam(':idTrab',$idTrab);
  $actualiza->bindParam(':fchVacacIni',$fchVacacIni);
  $actualiza->execute();

  return $inserta->rowCount();

}

function buscarDetallesVacaciones($db,$dni= null){
  $where = ($dni == null)?'':' AND  trabajadorvacaciones.`idTrabajador` = :dni ';

  $consulta = $db->prepare("SELECT `idVacaciones` , `trabajadorvacaciones`.`idTrabajador` , concat( apPaterno, ' ', apMaterno, ', ', nombres ) AS nombCompleto, `fchAlta` , `fchInicioPer` , `fchInicio` , `fchFin` , DATEDIFF( `fchFin` , `fchInicio` ) +1 AS dias, fchAprob, usuAprob FROM `trabajadorvacaciones` , trabajador WHERE `trabajadorvacaciones`.idTrabajador = trabajador.idTrabajador $where ORDER BY apPaterno, apMaterno, nombres, fchAlta");

  if ($dni != null)
  	$consulta->bindParam(':dni',$dni);
  $consulta->execute();
  return $consulta->fetchAll();
}


function editaDetalleVacac($db,$idVacac,$observacion){
  /*
  $registro = buscarDetalleVacac($db,$idVacac);
  foreach($registro as $fila) {
  	$idTrab =  $fila['idTrabajador'];
  	$fchVacacIni = $fila['fchVacacIni'];
    $fchInicio = $fila['fchInicio'];
    $fchFin = $fila['fchFin'];
    $dias = diferDias($fchFin,$fchInicio) + 1;
  }

  $actualiza = $db->prepare("UPDATE `trabajperiodvacac` SET `vacacTomado` = `vacacTomado` - $dias  WHERE `idTrabajador` = :idTrab AND `fchVacacIni` = :fchVacacIni");
  $actualiza->bindParam(':idTrab',$idTrab);
  $actualiza->bindParam(':fchVacacIni',$fchVacacIni);
  $actualiza->execute();
  */
  $actualiza = $db->prepare("UPDATE `trabajadorvacaciones` SET `observacion` = :observacion WHERE `idVacaciones` = :idVacac");
  $actualiza->bindParam(':idVacac',$idVacac);
  $actualiza->bindParam(':observacion',$observacion);
  $actualiza->execute();
}


function buscarVacacionesPendientes($db){
  $consulta = $db->prepare("SELECT concat(apPaterno,' ',apMaterno, ', ', nombres) AS nombCompleto, `trabajperiodvacac`.`idTrabajador`, categTrabajador , `fchVacacIni`, datediff(curdate(),fchVacacIni) AS diasTrab, `vacacTotal`, `vacacTomado`, (`vacacTotal` - `vacacTomado`) AS quedan FROM `trabajperiodvacac`, trabajador WHERE `trabajperiodvacac`.idTrabajador = trabajador.idTrabajador AND datediff(curdate(),fchVacacIni) > 182 AND (`vacacTotal` - `vacacTomado`) > 0 AND categTrabajador = '5ta' ORDER BY apPaterno, apMaterno, nombres");
  $consulta->execute();
  return $consulta->fetchAll(); 
}

function buscarDetalleVacac($db,$idVacac){
  $consulta = $db->prepare("SELECT `idVacaciones`, `idTrabajador`, `fchVacacIni`, `fchInicio`, `fchFin`, `fchAprob`, `observacion`, `usuAprob`, `creacFch`, `creacUsuario` FROM `trabajadorvacaciones` WHERE `idVacaciones` = :idVacac ");
  $consulta->bindParam(':idVacac',$idVacac);
  $consulta->execute();
  return $consulta->fetchAll(); 
}

function eliminaDetalleVacac($db,$idVacac,$usuario){
  $registro = buscarDetalleVacac($db,$idVacac);
  foreach($registro as $fila) {
  	$idTrab =  $fila['idTrabajador'];
  	$fchVacacIni = $fila['fchVacacIni'];
    $fchInicio = $fila['fchInicio'];
    $fchFin = $fila['fchFin'];
    $dias = diferDias($fchFin,$fchInicio) + 1;
  }

  $actualiza = $db->prepare("UPDATE `trabajperiodvacac` SET `vacacTomado` = `vacacTomado` - $dias  WHERE `idTrabajador` = :idTrab AND `fchVacacIni` = :fchVacacIni");
  $actualiza->bindParam(':idTrab',$idTrab);
  $actualiza->bindParam(':fchVacacIni',$fchVacacIni);
  $actualiza->execute();

  $elimina = $db->prepare("DELETE FROM `trabajadorvacaciones` WHERE `trabajadorvacaciones`.`idVacaciones` = :idVacac");
  $elimina->bindParam(':idVacac',$idVacac);
  $elimina->execute();	
}

function buscarConductoresCapac($db){
  $consulta = $db->prepare("SELECT `trabajador`.idTrabajador, concat(nombres,' ',apPaterno,' ',apMaterno) As nombCompleto,  if (categTrabajador = 'Tercero', 'Tercero','') AS tercero  , `trablic`.fchFin, datediff(`trablic`.fchFin, curdate()) As dif, `trablic`.plazo, if (`trablic`.fchFin is null, 'No está Capacitado o no está Activa la Alerta', if (datediff(`trablic`.fchFin, curdate())> `trablic`.plazo,'OK','Límite por vencer')) As observ  FROM `trabajador` LEFT JOIN trabajadorlicencias AS trablic ON `trabajador`.idTrabajador = `trablic`.idTrabajador AND `trablic`.nombre = 'CapacConducir' AND  `trablic`.estado = 'Activo' WHERE `licenciaCategoria` LIKE 'A-II-B' AND `estadoTrabajador` = 'activo' HAVING observ != 'OK' ORDER BY tercero, apPaterno, ApMaterno");
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarListadoTrabajadoresReporte($db,$estado = null, $dni = null){
  if ($estado == null)
    $where = " ";
  else 
    $where = " AND trabajador.estadoTrabajador = :estado ";
  $consulta = $db->prepare("SELECT max(`altasbajas`.fchInicio) AS fchInicio, `tipoTrabajador`, `trabajador`.`idTrabajador`,concat( `apPaterno`,' ',`apMaterno`,' ', `nombres`) AS nombCompleto, `cuspp`, `estadoCivil`, concat( `dirCalleyNro`,' ', `dirDistrito`,' ', `dirProvincia`,' ', `dirDepartamento`) AS dirCompleta,   `fchNacimiento`, `dniVig`.fchFin AS dniVig , `licVig`.fchFin AS licVig , `licCap`.fchFin AS licCap,  `dirTelefono`, `telfMovil`,'fecha retiro' FROM `trabajador` LEFT JOIN `trabajdocumentos` AS dniVig ON `trabajador`.idTrabajador = `dniVig`.idTrabajador  AND  `dniVig`.estado = 'Predeterminado' LEFT JOIN `trabajadorlicencias` AS licVig  ON `trabajador`.idTrabajador = `licVig`.idTrabajador AND `licVig`.nombre = 'licconducir' AND `licVig`.estado = 'activo' LEFT JOIN `trabajadorlicencias` AS licCap ON `trabajador`.idTrabajador = `licCap`.idTrabajador AND `licCap`.nombre = 'CapacConducir' AND `licCap`.estado = 'activo' LEFT JOIN `altasbajas` ON `trabajador`.idTrabajador = `altasbajas`.idTrabajador AND `altasbajas`.estado = 'Activo' WHERE 1 $where GROUP BY  `trabajador`.`idTrabajador`");

  //$consulta = $db->prepare("SELECT max(`altasbajas`.fchInicio) AS fchInicio, `tipoTrabajador`, `trabajador`.`idTrabajador`,concat( `apPaterno`,' ',`apMaterno`,' ', `nombres`) AS nombCompleto, `cuspp`, `estadoCivil`, concat( `dirCalleyNro`,' ', `dirDistrito`,' ', `dirProvincia`,' ', `dirDepartamento`) AS dirCompleta,   `fchNacimiento`, dniVig.fchFin AS dniVig , licVig.fchFin AS licVig , licCap.fchFin AS licCap,  `dirTelefono`, `telfMovil`,'fecha retiro' FROM `trabajador` LEFT JOIN `trabajadorlicencias` AS dniVig  ON `trabajador`.idTrabajador = `dniVig`.idTrabajador AND `dniVig`.nombre = 'dni' AND `dniVig`.estado = 'activo' LEFT JOIN `trabajadorlicencias` AS licVig  ON `trabajador`.idTrabajador = `licVig`.idTrabajador AND `licVig`.nombre = 'licconducir' AND `licVig`.estado = 'activo' LEFT JOIN `trabajadorlicencias` AS licCap ON `trabajador`.idTrabajador = `licCap`.idTrabajador AND `licCap`.nombre = 'CapacConducir' AND `licCap`.estado = 'activo' LEFT JOIN `altasbajas` ON trabajador.idTrabajador = altasbajas.idTrabajador AND altasbajas.estado = 'Activo' WHERE 1 $where GROUP BY  `trabajador`.`idTrabajador`");

  if ($estado != null)
    $consulta->bindParam(':estado',$estado);
  $consulta->execute();
  return $consulta->fetchAll();
 }

	function buscarTipoPrendas($db){
	  $consulta = $db->prepare("SELECT `identificador` FROM `auxiliar` WHERE `tipo` LIKE 'UNF'");
	  $consulta->execute();
	  return $consulta->fetchAll();
	} 

	function insertaEntregaPrenda($db,$dni,$cmbPrenda,$fchEntrega,$observacion){
		$usuario = $_SESSION['usuario'];
    //echo "$dni,$cmbPrenda,$fchEntrega,$observacion,$usuario";

	  $insertar = $db->prepare("INSERT INTO `trabajadoruniforme` (`idDetalle`, `idTrabajador`, `fchEntrega`, `observacion`, `creacFch`, `creacUsuario`) VALUES (:idDetalle,:dni, :fchEntrega, :observacion, CURDATE(),:usuario)");
	  $insertar->bindParam(':idDetalle',$cmbPrenda);
	  $insertar->bindParam(':dni',$dni);
	  $insertar->bindParam(':fchEntrega',$fchEntrega);
	  $insertar->bindParam(':observacion',$observacion);
	  $insertar->bindParam(':usuario',$usuario);
	  $insertar->execute();
	}

	function buscarPrendas($db,$dni,$limite = null){
		if ($limite == null)
			$lim = "";
		else
			$lim = " LIMIT $limite ";

		$consulta = $db->prepare("SELECT  `id`, `idDetalle`, `idTrabajador`, `fchEntrega`, `observacion`, `creacFch`, `creacUsuario`  FROM `trabajadoruniforme` WHERE `idTrabajador` LIKE :dni ORDER BY `creacFch` DESC $lim");
    $consulta->bindParam(':dni',$dni);
    $consulta->execute();
    return $consulta->fetchAll();
	} ;

	function buscarEntrega($db,$id){
	  $consulta = $db->prepare("SELECT  `id`, `idDetalle`, `idTrabajador`, `fchEntrega`, `observacion`, `creacFch`, `creacUsuario`  FROM `trabajadoruniforme` WHERE `id` LIKE :id");
    $consulta->bindParam(':id',$id);
    $consulta->execute();
    return $consulta->fetchAll();
	}

	function actualizaPrenda($db,$id,$fchEntrega,$observ){
	  $actualiza = $db->prepare("UPDATE `trabajadoruniforme` SET fchEntrega = :fchEntrega, `observacion` = :observ WHERE `trabajadoruniforme`.`id` = :id");
	  $actualiza->bindParam(':id',$id);
	  $actualiza->bindParam(':fchEntrega',$fchEntrega);
    $actualiza->bindParam(':observ',$observ);
    $actualiza->execute();
	};

	function actualizaFotoTrab($db,$nombre){
		
	}

	function buscarEntregaPrendas($db,$dni = NULL, $fchEntrega= NULL, $prenda = null, $usuario = null){
		$where = "";
		if ($dni != NULL) $where .= " AND  `trabajadoruniforme`.`idTrabajador` like :dni ";
		if ($fchEntrega != NULL) $where .= " AND  `trabajadoruniforme`.`fchEntrega` like :fchEntrega ";
		if ($prenda != NULL) $where .= " AND  `trabajadoruniforme`.`idDetalle` like :prenda ";
		if ($usuario != NULL) $where .= " AND  `trabajadoruniforme`.`creacUsuario` like :usuario ";
		$consulta = $db->prepare("SELECT  `trabajadoruniforme`.`id`,`trabajadoruniforme`.`idDetalle`, `trabajadoruniforme`.`idTrabajador`, `trabajadoruniforme`.`fchEntrega`,  `trabajadoruniforme`.`observacion`, concat(apPaterno,' ',apMaterno,', ', nombres) AS nombCompleto, `trabajadoruniforme`.`creacFch`, `trabajadoruniforme`.`creacUsuario` FROM `trabajadoruniforme`, trabajador WHERE `trabajadoruniforme`.idTrabajador = `trabajador`.idTrabajador $where ORDER BY `idTrabajador`, `fchEntrega`");
		if ($dni != null) $consulta->bindParam(':dni',$dni);
		if ($fchEntrega != null) $consulta->bindParam(':fchEntrega',$fchEntrega);
		if ($prenda != null) $consulta->bindParam(':prenda',$prenda);
		if ($usuario != null) $consulta->bindParam(':usuario',$usuario);
		$consulta->execute();
    return $consulta->fetchAll();
	}

	function buscarDetallesTblPrestamo($db,$anhio,$mes,$quin,$tipoItem,$marca){
		if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
    else  $fchQuincena = $anhio.'-'.$mes.'-28';

    if($tipoItem == 'Movilidad'){
      $consulta = $db->prepare("SELECT prestamo.`idTrabajador`, concat (apPaterno,' ',apMaterno,', ',nombres) AS nombCompleto, categTrabajador, tipoTrabajador, `descripcion`,`monto`, `fchPago`, `anhio`, `mes`, `entregado` , `fchQuincena` FROM `prestamo`, `trabajador` WHERE `prestamo`.idTrabajador = `trabajador`.idTrabajador AND ( `tipoItem` LIKE 'movilAsig' OR `tipoItem` LIKE :tipoItem) AND `entregado` = :marca AND `fchQuincena` = :fchQuincena ORDER BY `apPaterno`, apMaterno, idTrabajador");
    } else {
      $consulta = $db->prepare("SELECT prestamo.`idTrabajador`, concat (apPaterno,' ',apMaterno,', ',nombres) AS nombCompleto, categTrabajador, tipoTrabajador, `descripcion`,`monto`, `fchPago`, `anhio`, `mes`, `entregado` , `fchQuincena` FROM `prestamo`, `trabajador` WHERE `prestamo`.idTrabajador = `trabajador`.idTrabajador AND `tipoItem` LIKE :tipoItem AND `entregado` = :marca AND `fchQuincena` = :fchQuincena ORDER BY `apPaterno`, apMaterno, idTrabajador");
    }
    
    $consulta->bindParam(':fchQuincena',$fchQuincena);
    $consulta->bindParam(':tipoItem',$tipoItem);
    $consulta->bindParam(':marca',$marca);
    $consulta->execute();
    return $consulta->fetchAll();
	}


	function buscarDetallesHraExtra($db,$anhio,$mes,$quin,$marca){//Es nuevo para las horas extra 20180712
		if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
    else  $fchQuincena = $anhio.'-'.$mes.'-28';

    $consulta = $db->prepare("SELECT  `despachopersonal`.`idTrabajador`, concat(apPaterno,' ', apMaterno,', ', nombres) AS nombCompleto,  categTrabajador, tipoTrabajador, 'Cálculo automático' AS descripcion, valorHraExtra AS monto, `despachopersonal`.fchDespacho AS  fchPago, anhio, mes,  `despachopersonal`.pagado AS entregado, fchPago AS fchQuincena, `cliente`.nombre, `despacho`.observacion FROM `despacho`, `despachopersonal`, `trabajador`, `cliente` WHERE `despacho`.fchDespacho =  `despachopersonal`.fchDespacho AND `despacho`.correlativo = `despachopersonal`.correlativo AND `despacho`.idCliente = `cliente`.idRuc AND`despachopersonal`.idTrabajador = `trabajador`.idTrabajador AND valorHraExtra > 0 AND `despachopersonal`.`pagado` = :marca AND `fchPago` = :fchQuincena ORDER BY nombCompleto, idTrabajador");
    $consulta->bindParam(':fchQuincena',$fchQuincena);
    $consulta->bindParam(':marca',$marca);
    $consulta->execute();
    return $consulta->fetchAll();


/*
    $consulta = $db->prepare("SELECT prestamo.`idTrabajador`, concat (apPaterno,' ',apMaterno,', ',nombres) AS nombCompleto, categTrabajador, tipoTrabajador, `descripcion`,`monto`, `fchPago`, `anhio`, `mes`, `entregado` , `fchQuincena` FROM `prestamo`, `trabajador` WHERE `prestamo`.idTrabajador = `trabajador`.idTrabajador AND `tipoItem` LIKE :tipoItem AND `entregado` = :marca AND `fchQuincena` = :fchQuincena ORDER BY `apPaterno`, apMaterno, idTrabajador");
    $consulta->bindParam(':fchQuincena',$fchQuincena);
    $consulta->bindParam(':tipoItem',$tipoItem);
    $consulta->bindParam(':marca',$marca);
    $consulta->execute();
    return $consulta->fetchAll();*/
	}

  function buscarDetallesTblDespachosPersonal($db,$anhio,$mes,$quin,$marca){
  	if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
    else  $fchQuincena = $anhio.'-'.$mes.'-28';
    $consulta = $db->prepare("SELECT `despachopersonal`.`fchDespacho`, `despachopersonal`.`correlativo`, `despachopersonal`.`idTrabajador`, `valorRol`, `valorAdicional`, `tipoRol`, concat(apPaterno,' ', apMaterno,', ', nombres) AS nombCompleto, categTrabajador, tipoTrabajador, cuenta , `despacho`.idCliente, `cliente`.nombre, `despacho`.observacion FROM `despacho`, `despachopersonal`, `trabajador`, `cliente` WHERE `despacho`.fchDespacho =  `despachopersonal`.fchDespacho AND `despacho`.correlativo = `despachopersonal`.correlativo AND `despacho`.idCliente = `cliente`.idRuc AND`despachopersonal`.idTrabajador = `trabajador`.idTrabajador  AND `despachopersonal`.`pagado` = :marca AND `fchPago` = :fchQuincena ORDER BY nombCompleto, idTrabajador");
    $consulta->bindParam(':fchQuincena',$fchQuincena);
    $consulta->bindParam(':marca',$marca);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function buscarDetallesOcurrenciaConsulta($db,$anhio,$mes,$quin,$tipoItem,$marca){
  	if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
    else  $fchQuincena = $anhio.'-'.$mes.'-28';
    $consulta = $db->prepare("SELECT `ocurrenciaconsulta`.idTrabajador,  `ocurrenciaconsulta`.`fchDespacho`,  `ocurrenciaconsulta`.`correlativo`,  `descripcion`, `montoTotal`, `nroCuota`, `nroCuotas`,  `tipo`, `montoCuota`, `anhio`, `mes`,  `ocurrenciaconsulta`.`pagado`, `fchQuincena`,  `hraCreacion`,  concat(apPaterno,' ', apMaterno,', ', nombres) AS nombCompleto, categTrabajador, tipoTrabajador,  cuenta , `despacho`.idCliente, `cliente`.nombre FROM  `trabajador`,`ocurrenciaconsulta`, `despacho`, cliente WHERE `ocurrenciaconsulta`.fchDespacho = `despacho`.fchDespacho AND  `ocurrenciaconsulta`.correlativo = `despacho`.correlativo AND `despacho`.idCliente = `cliente`.idRuc   AND  `ocurrenciaconsulta`.idTrabajador = `trabajador`.idTrabajador  AND `tipoOcurrencia` LIKE :tipoItem  AND `ocurrenciaconsulta`.`pagado` = :marca AND `fchQuincena` = :fchQuincena ORDER BY nombCompleto, `trabajador`.idTrabajador");
    $consulta->bindParam(':fchQuincena',$fchQuincena);
    $consulta->bindParam(':tipoItem',$tipoItem);
    $consulta->bindParam(':marca',$marca);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function buscarDetallesAdelantoPrestamo($db,$anhio,$mes,$quin,$marca){
  	if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
    else  $fchQuincena = $anhio.'-'.$mes.'-28';
    $consulta = $db->prepare("SELECT `prestamo`.idTrabajador, descripcion, tipoItem, monto ,monto AS montoCuota, nroCuotas AS nroCuota, categTrabajador, tipoTrabajador,  concat(apPaterno,' ', apMaterno,', ', nombres) AS nombCompleto FROM prestamo, trabajador WHERE prestamo.idTrabajador = trabajador.idTrabajador AND tipoItem = 'Adelanto' AND entregado = :marca AND fchQuincena =  :fchQuincena UNION SELECT prestamodetalle.idTrabajador, descripcion, tipoItem, monto,montoCuota, nroCuota, categTrabajador, tipoTrabajador,  concat(apPaterno,' ', apMaterno,', ', nombres) AS nombCompleto FROM prestamodetalle, trabajador WHERE prestamodetalle.idTrabajador = trabajador.idTrabajador AND tipoItem = 'Prestamo' AND pagado =  :marca AND fchQuincena =  :fchQuincena  ORDER BY nombCompleto, idTrabajador");
    $consulta->bindParam(':fchQuincena',$fchQuincena);
    $consulta->bindParam(':marca',$marca);
    $consulta->execute();
    return $consulta->fetchAll();
  }
 
  function buscarDetallesTblPrestamoDetalle($db,$anhio,$mes,$quin,$tipoItem,$marca){
 	  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
    else  $fchQuincena = $anhio.'-'.$mes.'-28';
    $consulta = $db->prepare("SELECT `monto`, `nroCuota`, `prestamodetalle`.`idTrabajador`,tipoItem, `descripcion`, `montoCuota`,  concat(apPaterno,' ', apMaterno,', ', nombres) AS nombCompleto, categTrabajador, tipoTrabajador  FROM `prestamodetalle`, trabajador WHERE  `prestamodetalle`.idTrabajador = `trabajador`.idTrabajador AND `tipoItem` LIKE :tipoItem AND `pagado` =  :marca AND `fchQuincena` = :fchQuincena ORDER BY nombCompleto, idTrabajador");
    $consulta->bindParam(':fchQuincena',$fchQuincena);
    $consulta->bindParam(':tipoItem',$tipoItem);
    $consulta->bindParam(':marca',$marca);
    $consulta->execute();
    return $consulta->fetchAll();
 }


  function  buscarDetallesMovilidadNuevoModelo($db,$anhio,$mes,$quin,$marca){
 	  if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
    else  $fchQuincena = $anhio.'-'.$mes.'-28';
    $consulta = $db->prepare("SELECT `ocurrenciaconsulta`.tipoOcurrencia, `ocurrenciaconsulta`.idTrabajador, concat(apPaterno,' ', apMaterno,', ', nombres) AS nombCompleto, `ocurrenciaconsulta`.`fchDespacho`, `ocurrenciaconsulta`.`correlativo`, `descripcion`, `montoTotal`, `nroCuota`, `nroCuotas`, `montoCuota`, `anhio`, `mes`, `ocurrenciaconsulta`.`pagado`, `fchQuincena`, categTrabajador,  tipoTrabajador , `cliente`.nombre, '' AS fchPago, cuenta FROM `trabajador`,`ocurrenciaconsulta`, `despacho`, cliente WHERE `ocurrenciaconsulta`.fchDespacho = `despacho`.fchDespacho AND `ocurrenciaconsulta`.correlativo = `despacho`.correlativo AND `despacho`.idCliente = `cliente`.idRuc AND `ocurrenciaconsulta`.idTrabajador = `trabajador`.idTrabajador AND `tipoOcurrencia` LIKE 'movilidad' AND `ocurrenciaconsulta`.`pagado` = :marca AND `fchQuincena` = :fchQuincena
    UNION
    SELECT `prestamo`.tipoItem, prestamo.`idTrabajador`, concat (apPaterno,' ',apMaterno,', ',nombres) AS nombCompleto , '' AS fchDespacho , '' As correlativo , `descripcion`, monto AS montoTotal, '1' AS nroCuota, '1' AS nroCuotas, monto AS montoCuota,`anhio`, `mes`,  `entregado` ,  `fchQuincena`, categTrabajador, tipoTrabajador, 'No relacionado a un despacho' AS `nombre`, `fchPago`, '' AS cuenta  FROM `prestamo`, `trabajador` WHERE `prestamo`.idTrabajador = `trabajador`.idTrabajador AND `tipoItem` IN ('bono','movilidad','hraExtra','VRol','Pref') AND `entregado` = :marca AND `fchQuincena` = :fchQuincena ORDER BY nombCompleto, idTrabajador");
    
    $consulta->bindParam(':fchQuincena',$fchQuincena);
    $consulta->bindParam(':marca',$marca);
    $consulta->execute();
    return $consulta->fetchAll();
 }



 function  buscarDetallesOcurrenciaConsultaPrestamoDetalle($db,$anhio,$mes,$quin,$tipoOcurrencia,$tipoPrestamo,$marca){
 	if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
    else  $fchQuincena = $anhio.'-'.$mes.'-28';
    //$consulta = $db->prepare("SELECT  `prestamodetalle`.idTrabajador,  '' AS `fchDespacho`, '' AS  `correlativo`,  `descripcion`, '' AS `montoTotal`, `nroCuota`,  '' AS `nroCuotas`, tipoItem AS `tipo`, `montoCuota`, `anhio`, `mes`,  `prestamodetalle`.`pagado`, `fchQuincena`,   concat(apPaterno,' ', apMaterno,', ', nombres) AS nombCompleto, categTrabajador, tipoTrabajador, 'Descuento no relacionado a un despacho' AS cuenta , '' AS idCliente, 'No relacionado a un despacho' AS nombre FROM `prestamodetalle`, `trabajador` WHERE `prestamodetalle`.idTrabajador =  `trabajador`.idTrabajador AND `tipoItem` LIKE :tipoPrestamo AND `pagado` = :marca AND `fchQuincena` = :fchQuincena UNION SELECT `ocurrenciaconsulta`.idTrabajador, `ocurrenciaconsulta`.`fchDespacho` , `ocurrenciaconsulta`.`correlativo` , `descripcion` , `montoTotal` , `nroCuota` , `nroCuotas` , `tipo` , `montoCuota` , `anhio` , `mes` , `ocurrenciaconsulta`.`pagado` , `fchQuincena` , concat( apPaterno, ' ', apMaterno, ', ', nombres ) AS nombCompleto, categTrabajador, tipoTrabajador, cuenta, `despacho`.idCliente, `cliente`.nombre FROM `ocurrenciaconsulta` , trabajador, despacho, cliente WHERE `ocurrenciaconsulta`.fchDespacho = `despacho`.fchDespacho AND `ocurrenciaconsulta`.correlativo = `despacho`.correlativo AND `despacho`.idCliente = `cliente`.idRuc AND `ocurrenciaconsulta`.idTrabajador = `trabajador`.idTrabajador AND `tipoOcurrencia` LIKE :tipoOcurrencia AND `ocurrenciaconsulta`.`pagado` = :marca AND `fchQuincena` = :fchQuincena  ORDER BY nombCompleto, idTrabajador");
    $consulta = $db->prepare("SELECT  `prestamodetalle`.idTrabajador,  '' AS `fchDespacho`, '' AS  `correlativo`,  `descripcion`, '' AS `montoTotal`, `nroCuota`,  '' AS `nroCuotas`, tipoItem AS `tipo`, `montoCuota`, `anhio`, `mes`,  `prestamodetalle`.`pagado`, `fchQuincena`,   concat(apPaterno,' ', apMaterno,', ', nombres) AS nombCompleto, categTrabajador, tipoTrabajador, 'Descuento no relacionado a un despacho' AS cuenta , '' AS idCliente, 'No relacionado a un despacho' AS nombre FROM `prestamodetalle`, `trabajador` WHERE `prestamodetalle`.idTrabajador =  `trabajador`.idTrabajador AND `tipoItem` IN ($tipoPrestamo) AND `pagado` = :marca AND `fchQuincena` = :fchQuincena UNION SELECT `ocurrenciaconsulta`.idTrabajador, `ocurrenciaconsulta`.`fchDespacho` , `ocurrenciaconsulta`.`correlativo` , `descripcion` , `montoTotal` , `nroCuota` , `nroCuotas` , `tipo` , `montoCuota` , `anhio` , `mes` , `ocurrenciaconsulta`.`pagado` , `fchQuincena` , concat( apPaterno, ' ', apMaterno, ', ', nombres ) AS nombCompleto, categTrabajador, tipoTrabajador, cuenta, `despacho`.idCliente, `cliente`.nombre FROM `ocurrenciaconsulta` , trabajador, despacho, cliente WHERE `ocurrenciaconsulta`.fchDespacho = `despacho`.fchDespacho AND `ocurrenciaconsulta`.correlativo = `despacho`.correlativo AND `despacho`.idCliente = `cliente`.idRuc AND `ocurrenciaconsulta`.idTrabajador = `trabajador`.idTrabajador AND `tipoOcurrencia` LIKE :tipoOcurrencia AND `ocurrenciaconsulta`.`pagado` = :marca AND `fchQuincena` = :fchQuincena  ORDER BY nombCompleto, idTrabajador");
    
    $consulta->bindParam(':fchQuincena',$fchQuincena);
    $consulta->bindParam(':tipoOcurrencia',$tipoOcurrencia);
    //$consulta->bindParam(':tipoPrestamo',$tipoPrestamo);
    $consulta->bindParam(':marca',$marca);
    $consulta->execute();
    return $consulta->fetchAll();
 }

function buscarHistoricoAFavor($db,$idTrabajador){
   $consulta = $db->prepare("SELECT `prestamo`.`fchCreacion`, prestamo.`idTrabajador`,  concat(apPaterno, ' ', apMaterno,' ', nombres) AS nombre,  prestamo.`descripcion`, prestamo.`tipoItem`,  prestamo.`monto`,  prestamo.`nroCuotas`, if(`prestamo`.tipoItem = 'ajuste',`prestamodetalle`.fchPago, `prestamo`.fchPago ) AS fchPago, if (`prestamo`.tipoItem = 'ajuste',`prestamodetalle`.nroCuota, 1 ) AS nroCuota,  if (`prestamo`.tipoItem = 'ajuste',`prestamodetalle`.fchQuincena,`prestamo`.fchQuincena ) AS fchQuincena, if (`prestamo`.tipoItem = 'ajuste',`prestamodetalle`.pagado,`prestamo`.entregado ) AS pagado, if (`prestamo`.tipoItem = 'ajuste',`prestamodetalle`.montoCuota, `prestamo`.monto ) AS montoCuota, prestamo.codigo  FROM trabajador, prestamo LEFT JOIN prestamodetalle ON prestamo.`idTrabajador` = prestamodetalle.`idTrabajador` AND  prestamo.`tipoItem` = prestamodetalle.`tipoItem` AND  prestamo.`descripcion` = prestamodetalle.`descripcion` AND  prestamo.`fchCreacion` = prestamodetalle.`fchCreacion` AND prestamo.`monto` = prestamodetalle.`monto` WHERE `prestamo`.idtrabajador = `trabajador`.idTrabajador AND `prestamo`.tipoItem IN ('reembolso','ajuste','devolgarantia','vacaciones','adelanto') AND prestamo.`idTrabajador` = :idTrabajador
union
SELECT `ocurrenciaconsulta`.fchCreacion,  `ocurrenciaconsulta`.idTrabajador,  concat(apPaterno, ' ', apMaterno,' ', nombres) AS nombre,  `ocurrenciaconsulta`.descripcion,  tipoOcurrencia AS tipoItem,  montoTotal AS monto,  nroCuotas, `ocurrenciaconsulta`.fchPago, nroCuota, fchQuincena, pagado, montoCuota, 'codigo' AS codigo   FROM `ocurrenciaconsulta`, trabajador WHERE `ocurrenciaconsulta`.idTrabajador =  `trabajador`.idTrabajador AND `tipoOcurrencia` LIKE 'devolucion' AND `ocurrenciaconsulta`.idTrabajador = :idTrabajador");
    $consulta->bindParam(':idTrabajador',$idTrabajador);
    $consulta->execute();
    return $consulta->fetchAll();
 }

function generaIdDocumAdj($db){
  $consulta = $db->prepare("SELECT `id` FROM  `trabajadordocsvarios` ORDER BY `id` DESC limit 1 ");
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

function insertaRegEnTrabajadorDocsVarios($db,$id,$dni,$nombre,$descripcion,$escaneo){
	$usuario = $_SESSION['usuario'];
  //echo "$id,$dni,$nombre,$descripcion,$escaneo,$usuario";
	$inserta = $db->prepare("INSERT INTO `trabajadordocsvarios` (`id`, `idTrabajador`, `nombre`, `descripcion`, `escaneo`, `usuario`, `fchCreacion`) VALUES (:id, :dni, :nombre, :descripcion, :escaneo, :usuario, curdate());");
	$inserta->bindParam(':id',$id);
	$inserta->bindParam(':dni',$dni);
	$inserta->bindParam(':nombre',$nombre);
	$inserta->bindParam(':descripcion',$descripcion);
	$inserta->bindParam(':escaneo',$escaneo);
	$inserta->bindParam(':usuario',$usuario);
	$inserta->execute();
}

function editaRegEnTrabajadorDocsVarios($db,$id,$nombre,$descripcion){
	$usuario = $_SESSION['usuario'];
	//echo "$id,$nombre,$descripcion, $usuario  ";
	$actualiza = $db->prepare("UPDATE `trabajadordocsvarios` SET `nombre` = :nombre, `descripcion` = :descripcion, `usuarioUltimoCambio` = :usuario, `fchUltimoCambio` = curdate() WHERE `id` = :id");
	$actualiza->bindParam(':id',$id);
	$actualiza->bindParam(':nombre',$nombre);
  $actualiza->bindParam(':descripcion',$descripcion);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();
}

function eliminaRegEnTrabajadorDocsVarios($db,$id){
  $elimina = $db->prepare("DELETE FROM `trabajadordocsvarios` WHERE `id` = :id");
	$elimina->bindParam(':id',$id);
  $elimina->execute();
}

function listadoTrabajadoresMes($db,$anhio,$mes){ 
  $quin1 = $anhio."-".$mes."-14";
  $quin2 = $anhio."-".$mes."-28";
  $consulta = $db->prepare("SELECT quincenadetalle.`idTrabajador`, `categoria` FROM `quincenadetalle`, trabajador WHERE trabajador.idTrabajador = quincenadetalle.idTrabajador AND `quincena` in  ('$quin1', '$quin2') GROUP BY  quincenadetalle.`idTrabajador`  ORDER BY `trabajador`.`apPaterno` ,`trabajador`.`apMaterno`, `trabajador`.`nombres`");
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarOtrosRegistros($db,$idTrab){
	$consulta = $db->prepare("SELECT count(*) AS cant FROM prestamo WHERE idTrabajador = :idTrab AND tipoItem IN ('Ajuste','Bono','DevolGarantia','DomFeriado','HraExtra','Movilidad','Reembolso') AND entregado IN ('No','Md') AND fchCreacion >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) union SELECT count(*) AS cant FROM prestamodetalle  WHERE idTrabajador = :idTrab AND tipoItem IN ('DsctoTrabaj','DsctoUnif', 'Compra') AND pagado IN ('No','Md') AND fchCreacion >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) union SELECT count(*) AS cant FROM ocurrenciaconsulta WHERE tipoOcurrencia IN ('Descuento', 'Devolucion', 'Gastos Varios', 'Movilidad') AND idTrabajador = :idTrab AND pagado IN ('No','Md') AND fchCreacion >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) ");

	$consulta->bindParam(':idTrab',$idTrab);
  $consulta->execute();
  $aux =  $consulta->fetchAll();
  $cant = 0;
  foreach ($aux as $item) {
    $cant += $item['cant'];
  }
  return $cant;
}


function generaDocInternoPrestamo($db){
  $consulta = $db->prepare("SELECT `codigo` FROM `prestamo` WHERE codigo like 'IntMoy%' ORDER BY `codigo` DESC limit 1 ");
  $consulta->execute();
  $auxNro =  $consulta->fetchAll();
  $nro = 0;
  foreach ($auxNro as $item) {
    $nro = $item['codigo'];
  }
  $anhioActual = date("Y");
  $inicioCadena = "IntMoy".$anhioActual."-";
  $inicioUltNro = substr($nro,0,11);
  $ultNro = substr($nro,-6);
  if ($inicioCadena != $inicioUltNro)
    $ultNro = 0;
  $auxSgte = $ultNro*1 + 1;

  $sgteNro = $inicioCadena.substr('00000'.$auxSgte,-6);
  return $sgteNro;
}

function buscarParaLiquidar($db,$dni){
	$consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`,                `tipoOcurrencia`, `descripcion`, `montoTotal`, `nroCuota`, `nroCuotas`, `montoCuota`,`pagado`, `fchPago`, `fchQuincena` FROM `ocurrenciaconsulta` WHERE tipoOcurrencia IN ('Caja Chica','Descuento','Gastos Varios') AND pagado != 'Si' AND `idTrabajador` LIKE :idTrabajador
UNION
SELECT 'fchDespacho', 'correlativo',`prestamodetalle`.`tipoItem` AS `tipoOcurrencia`,`prestamodetalle`.`descripcion`, `prestamodetalle`.`monto` AS  `montoTotal`,`nroCuota`, `nroCuotas`,`montoCuota`, `pagado`, `prestamodetalle`.`fchPago`,  `prestamodetalle`.`fchQuincena` FROM `prestamodetalle`, prestamo WHERE prestamo.idTrabajador = `prestamodetalle`.idTrabajador AND prestamo.descripcion = `prestamodetalle`.descripcion AND  prestamo.tipoItem = `prestamodetalle`.tipoItem  AND  prestamo.monto = `prestamodetalle`.monto AND `prestamodetalle`.`tipoItem` IN ('Compra','DsctoTrabaj','DsctoUnif','Prestamo') AND `pagado` != 'Si' AND `prestamodetalle`.`idTrabajador` like :idTrabajador ");

  $consulta->bindParam(':idTrabajador',$dni);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarDevFondoGarantia($db,$dni){
	$consulta = $db->prepare("SELECT `idTrabajador`, `descripcion`, `tipoItem`, `monto`,`montoCuota`, `nroCuota`, `pagado`, `fchPago`,`fchQuincena`, codAux , fchcreacion FROM `prestamodetalle` WHERE `tipoItem` IN ('FondoGarantia') AND codAux IS NULL AND  `idTrabajador` like :idTrabajador ORDER BY pagado ");
  $consulta->bindParam(':idTrabajador',$dni);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarTrabajadorDespachoPersonal($db,$fchDespacho,$correlativo,$idTrabajador){
	$consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `idTrabajador`, `valorRol`, `valorAdicional`, `trabFchDespachoFin`, `trabHraInicio`, `trabHraFin`, `tipoRol`, `pagado`, `fchPago`, `anhio`, `mes`, `fchDesmarca`, `usuario`, `fchActualizacion`, `ultProceso`, `ultProcesoFch`, `ultProcesoUsuario` FROM `despachopersonal` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `idTrabajador` = :idTrabajador AND tipoRol != 'Coordinador' ");
	$consulta->bindParam(':fchDespacho',$fchDespacho);
  $consulta->bindParam(':correlativo',$correlativo);
  $consulta->bindParam(':idTrabajador',$idTrabajador);
  $consulta->execute();
  return $consulta->fetchAll();
}

function insertarAlta($db,$idTrabajador,$fchInicio,$fchFin,$tipo,$regimen,$observ){
	$usuario = $_SESSION['usuario'];
	//echo "$idTrabajador,$fchInicio,$duracion,$tipo,$regimen,$diasVacac,$observ,$usuario";
  $insertar = $db->prepare("INSERT INTO `altasbajas` (`idTrabajador`, `fchInicio`, `tipo`,`regimen`, `fchFin`,  `observacion`, `creacUsuario`, `creacFch`) VALUES (:idTrabajador, :fchInicio, :tipo, :regimen, :fchFin, :observ, :usuario, now());");
  $insertar->bindParam(':idTrabajador',$idTrabajador);
  $insertar->bindParam(':fchInicio',$fchInicio);
  $insertar->bindParam(':fchFin',$fchFin);
  $insertar->bindParam(':tipo',$tipo);
  $insertar->bindParam(':regimen',$regimen);
  $insertar->bindParam(':observ',$observ);
  $insertar->bindParam(':usuario',$usuario);
  $insertar->execute();
  return $insertar->rowCount(); 
}

function eliminaAltaBaja($db,$dni,$fchInicio,$tipo){
	$elimina = $db->prepare("DELETE FROM `altasbajas` WHERE `idTrabajador` = :dni AND `fchInicio` = :fchInicio AND `tipo` = :tipo ");
  $elimina->bindParam(':dni',$dni);
  $elimina->bindParam(':fchInicio',$fchInicio);
  $elimina->bindParam(':tipo',$tipo);
  $elimina->execute();
  return $elimina->rowCount();
}

function inactivarAltas($db,$dni,$fchInicio){
	$actualiza = $db->prepare("UPDATE `altasbajas` SET estado = 'Inactivo' WHERE fchInicio < :fchInicio AND idTrabajador = :dni ");
	$actualiza->bindParam(':dni',$dni);
  $actualiza->bindParam(':fchInicio',$fchInicio);
  $actualiza->execute();
  return $actualiza->rowCount();
}

function buscarTrabajadoresQueLesCorrespondeVacaciones($db){
	$consulta = $db->prepare("SELECT `altasbajas`.idTrabajador, `trabajador`.diasVacacAnual , min(fchInicio), DATE_ADD( min(fchInicio), INTERVAL 1 YEAR) AS fchVacacIni, DATE_SUB(DATE_ADD( min(fchInicio), INTERVAL 2 YEAR), INTERVAL 1 DAY) AS fchVacacLimite , DATEDIFF(curdate(),min(fchInicio)) AS diasTrans FROM `altasbajas`, trabajador WHERE `altasbajas`.`idTrabajador` = trabajador.idTrabajador AND `estado` LIKE 'Activo' AND tipo != 'Baja' GROUP BY `idTrabajador` HAVING diasTrans > 360  ");
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarTrabajadoresRequierenAltas($db){
	$consulta = $db->prepare("SELECT `trabajador`.idTrabajador AS idTrab, `altasbajas`.idTrabajador, `trabajador`.apPaterno, `trabajador`.apMaterno, `trabajador`.nombres FROM `trabajador` LEFT JOIN altasbajas ON `trabajador`.idTrabajador = `altasbajas`.idTrabajador AND tipo IN ('Alta','Renovac') AND estado = 'activo' WHERE `categTrabajador` = '5ta' AND `estadoTrabajador` = 'Activo' AND `altasbajas`.idTrabajador IS NULL GROUP BY `trabajador`.idTrabajador ORDER BY `trabajador`.`apPaterno` ASC  ");
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarTrabajadoresConVacac($db){
	$consulta = $db->prepare("SELECT `t1`.idTrabajador, fchVacacIni, fchVacacLimite, vacacTotal, vacacTomado, estadoPeriodo, apPaterno, apMaterno, nombres FROM ( SELECT * FROM `trabajperiodvacac` ORDER BY idTrabajador, fchVacacIni DESC LIMIT 99999999) AS t1, trabajador WHERE `t1`.idTrabajador = `trabajador`.idTrabajador GROUP BY `t1`.idTrabajador ");
  $consulta->execute();
  return $consulta->fetchAll();
}

function insertarPeriodoVacacional($db,$idTrabajador,$fchVacacIni,$fchVacacLimite,$vacacTotal){
  $insertar = $db->prepare("INSERT INTO trabajperiodvacac(idTrabajador, fchVacacIni, fchVacacLimite, vacacTotal, creacFch) VALUES (:idTrabajador, :fchVacacIni, :fchVacacLimite, :vacacTotal,  now());");
  $insertar->bindParam(':idTrabajador',$idTrabajador);
  $insertar->bindParam(':fchVacacIni',$fchVacacIni);
  $insertar->bindParam(':fchVacacLimite',$fchVacacLimite);
  $insertar->bindParam(':vacacTotal',$vacacTotal);
  $insertar->execute();
  return $insertar->rowCount(); 
}

function buscarAltasPorVencer($db, $limite = 30){
	$consulta = $db->prepare("SELECT t1.*, concat(nombres, ' ', apPaterno,' ',apMaterno) AS nombCompleto , DATEDIFF(fchFin, curdate()) AS falta FROM ( SELECT * FROM `altasbajas` WHERE estado = 'Activo' ORDER BY idTrabajador, fchInicio DESC LIMIT 99999999 ) AS t1, trabajador WHERE t1.idTrabajador = trabajador.idTrabajador GROUP BY t1.idTrabajador HAVING DATEDIFF(fchFin, curdate()) <=  :limite ");
	$consulta->bindParam(':limite',$limite);
  $consulta->execute();
  return $consulta->fetchAll();
}


function buscaUnIdDespachoHaciaAtras($db,$dni,$fchEnt){
  //Similar a buscaIdDespachoTercero de prendas modelo
  $fchIni = date('Y-m-d',time()-(20*24*60*60));  //20 días hacia atrás
  $consulta = $db->prepare("SELECT placa, `despachovehiculotercero`.fchDespacho, `despachovehiculotercero`.correlativo FROM `despachovehiculotercero` , `despachopersonal` WHERE  `despachovehiculotercero`.fchDespacho = `despachopersonal`.fchDespacho AND  `despachovehiculotercero`.correlativo = `despachopersonal`.correlativo AND despachovehiculotercero.`fchDespacho` >= '$fchIni' AND despachovehiculotercero.`fchDespacho` <= :fchEnt AND `despachovehiculotercero`.`docPagoTercero` IS NULL AND `idTrabajador` LIKE :dni order by fchDespacho Desc,correlativo LIMIT 1");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':fchEnt',$fchEnt);
  $consulta->execute();
  return $consulta->fetchAll();
}



function calcularQuincenaContab($db,$idTrabajador,$anhio, $mes, $quin, $comFlujo, $comMixta, $primaSeg, $porcOblig){
	if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28';

$arrData = array(
      'hrsExtra25' => array(
          'cod01' => '621102',	'cod02' => '0105',	'valor' => 0
      ),
      'hrsExtra35' => array(
          'cod01' => '611102',	'cod02' => '0106',	'valor' => 0
      ),
      'remDomFer' => array(
          'cod01' => '621201',	'cod02' => '0115',	'valor' => 0
      ),
      'vacTruncas' => array(
          'cod01' => '621503',	'cod02' => '0114',	'valor' => 0
      ),
      'remVacac' => array(
          'cod01' => '621501',	'cod02' => '0118',	'valor' => 0
      ),
      'habBasico' => array(
          'cod01' => '621101',	'cod02' => '0121',	'valor' => 0
      ),
      'asigFam' => array(
          'cod01' => '621103',	'cod02' => '0201',	'valor' => 0
      ),
      'asigVacac' => array(
          'cod01' => '621502',	'cod02' => '0210',	'valor' => 0
      ),
      'bonifCts' => array(
          'cod01' => '629101',	'cod02' => '0305',	'valor' => 0
      ),
      'servAdic' => array(
          'cod01' => '621301',	'cod02' => '1004',	'valor' => 0
      ),
      'bonifVar' => array(
          'cod01' => '621301',	'cod02' => '1004',	'valor' => 0
      ),
      'subsidio' => array(
          'cod01' => '162902',	'cod02' => '0916',	'valor' => 0
      ),
      'licGoce' => array(
          'cod01' => '621105',	'cod02' => '0907',	'valor' => 0
      ),
      'inasisten' => array(
          'cod01' => '621101',	'cod02' => '0705',	'valor' => 0
      ),
      'bonifExtra' => array(
          'cod01' => '621401',	'cod02' => '0312',	'valor' => 0
      ),
      'bonifExPro' => array(
          'cod01' => '621401',	'cod02' => '0313',	'valor' => 0
      ),
      'gratiFPyNv' => array(
          'cod01' => '621401',	'cod02' => '0406',	'valor' => 0
      ),
      'gratiFPPro' => array(
          'cod01' => '621401',	'cod02' => '0407',	'valor' => 0
      ),
      'bonoAlim' => array(
          'cod01' => '621109',	'cod02' => '0917',	'valor' => 0
      ),
      'bonoProd' => array(
          'cod01' => '621104',	'cod02' => '0902',	'valor' => 0
      ),
      'canastaNav' => array(
          'cod01' => '621107',	'cod02' => '0903',	'valor' => 0
      ),
      'ctsMayNov' => array(
          'cod01' => '629101',	'cod02' => '0904',	'valor' => 0
      ),
      'movilSuped' => array(
          'cod01' => '621106',	'cod02' => '0909',	'valor' => 0
      ),
      'utilidad' => array(
          'cod01' => '621108',	'cod02' => '0911',	'valor' => 0
      ),
      'bonoPrefer' => array(
          'cod01' => '621109',	'cod02' => '1005',	'valor' => 0
      ),
      'devFondoG' => array(
          'cod01' => '411201',	'cod02' => '1001',	'valor' => 0
      ),
      'adelQ' => array(
          'cod01' => '141201',	'cod02' => '0701',	'valor' => 0
      ),
      'adelVacac' => array(
          'cod01' => '411501',	'cod02' => '0706',	'valor' => 0
      ),
      'adelGratif' => array(
          'cod01' => '411401',	'cod02' => '0706',	'valor' => 0
      ),
      'adelCts' => array(
          'cod01' => '415101',	'cod02' => '0706',	'valor' => 0
      ),
      'adelCanNav' => array(
          'cod01' => '419101',	'cod02' => '0706',	'valor' => 0
      ),
      'adelUtil' => array(
          'cod01' => '413101',	'cod02' => '0706',	'valor' => 0
      ),
      'dsctoResp' => array(
          'cod01' => '759901',	'cod02' => '0706',	'valor' => 0
      ),
      'dsctoPrest' => array(
          'cod01' => '141101',	'cod02' => '0706',	'valor' => 0
      ),
      'dsctoCompra' => array(
          'cod01' => '122103',	'cod02' => '0706',	'valor' => 0
      ),
      'dsctoFndGar' => array(
          'cod01' => '411201',	'cod02' => '0706',	'valor' => 0
      ),
      'esSaludVid' => array(
          'cod01' => '403501',	'cod02' => '0604',	'valor' => 0
      ),
      'renta5ta' => array(
          'cod01' => '401731',	'cod02' => '0605',	'valor' => 0
      ),
      'onp' => array(
          'cod01' => '403201',	'cod02' => '0607',	'valor' => 0
      ),
      'comision' => array(
          'cod01' => '407101',	'cod02' => '0601',	'valor' => 0
      ),
      'seguro' => array(
          'cod01' => '407101',	'cod02' => '0606',	'valor' => 0
      ),
      'aporte' => array(
          'cod01' => '407101',	'cod02' => '0608',	'valor' => 0
      ),
      'sumaEsS' => array(
          'cod01' => '403101',	'cod02' => '0804',	'valor' => 0
      ),
      'polizDeSeg' => array(
          'cod01' => '182141',  'cod02' => '0803',  'valor' => 0
      ),
      'sobreElTrabajador' => array(
          'diasTrabaj' => 0,	'hrsTrabaj' => '',	'nombreComp' => '',
          'viajes' => 0,			'valorRol' => 0,		'categTrabaj' => '',
          'modoSueldo' => '', 'idtrabajador' => '',
          'remuneracionBasica' =>0,	'asignacionFamiliar' => 0
      ),
      'totales' => array(
          'ingresoGravable' => 0,
          'ingresoNoGravable' => 0,
          'egresosOtros' => 0,
          'egresosDctosPorAporte' => 0,
      ),
  );


  $dataEnPrestamo = resumenQuincenaPlanillaVarios($db,$fchQuincena,'Md', $idTrabajador);
  $dataEnPrestamoDetalle = resumenPagoPrestamosMarcadosContab($db,$anhio,$mes,$quin, $idTrabajador);
  $dataEnOcurrConsulta = resumenOcurrenciaMarcados($db,$anhio,$mes,$quin, $idTrabajador);
  $dataTrabajosHechos = resumenQuincenaPlanillaDespachos($db,$fchQuincena,'Md', NULL, $idTrabajador);
  $dataTrabajador = buscarTrabajador($db,$idTrabajador);

  $arrData['dsctoPrest']['valor'] = 0;

  foreach ($dataEnPrestamo as $key => $item) {
  	//Ingreso Gravable
  	$arrData['remDomFer']['valor'] = $item['domFeriado'];
  	$arrData['servAdic']['valor'] = $item['reembolso'] + $item['servAdicional'];
    //$arrData['bonifVar']['valor'] = $item['hraExtra'];
    $arrData['subsidio']['valor'] = $item['descMedico'];

    $arrData['licGoce']['valor'] = $item['licGoce'];
    $arrData['inasisten']['valor'] = $item['inasistencia'];

    //Ingreso No Gravable
    $arrData['bonoProd']['valor'] = $item['bono'] + $item['bonoProducMes'];
    $arrData['devFondoG']['valor'] = $item['devGarantia'];
    $arrData['movilSuped']['valor'] += $item['movilidad'] + $item['movilAsig']  ;

    //Otros Dsctos No Deduc
    $arrData['dsctoPrest']['valor'] += $item['adelanto'];
    $arrData['esSaludVid']['valor'] += $item['pagoEsSaludVida'];
  }

  foreach ($dataEnPrestamoDetalle as $key => $item){

    /*if ($idTrabajador == '76903849'){
    	echo  "Dscto Resp".$arrData['dsctoResp']['valor'].", Dscto Prestamo ".$arrData['dsctoPrest']['valor'].", Uniformes ".$item['dsctoUnif'].", Dscto trabajador ".$item['dsctoTrabaj'].", Pago Prestamo".$item['pagoPrestamo'];
    }*/
  	//Otros Dsctos No Deduc
  	$arrData['dsctoResp']['valor'] = $item['dsctoUnif'];
    $arrData['dsctoPrest']['valor'] += $item['dsctoTrabaj'] + $item['pagoPrestamo'];
    $arrData['dsctoCompra']['valor'] = $item['compra'];
    $arrData['dsctoFndGar']['valor'] = $item['fondoGarantia'];

  }

  foreach ($dataEnOcurrConsulta as $key => $item) {
  	//Ingreso No Gravable
    $arrData['movilSuped']['valor'] += $item['pagoMovilidad'];

    //Otros Dsctos No Deduc
  	$arrData['dsctoPrest']['valor'] += $item['pagoDescuento'];
  }

  foreach ($dataTrabajosHechos as $key => $item) {
  	$arrData['sobreElTrabajador']['diasTrabaj']  = $item['diasTrabaj'];
  	$arrData['sobreElTrabajador']['nombreComp']  = $item['nombreCompleto'];
  	$arrData['sobreElTrabajador']['viajes']  = $item['viajes'];
  	$arrData['sobreElTrabajador']['hrsTrabaj']  = $item['hrsTrabaj'];
  	$arrData['sobreElTrabajador']['valorRol']  = $item['valorRol'];
  	$arrData['sobreElTrabajador']['categTrabaj']  = $item['categTrabajador'];
  	$arrData['sobreElTrabajador']['modoSueldo']  = $item['modoSueldo'];
  	$arrData['bonifVar']['valor'] += $item['valorHraExtra'];

  }

  foreach ($dataTrabajador as $key => $item) {
  	/* 201012 La asignación familiar se pasa a pagar en la segunda quincena
  	if ($quin == '1' || ($quin == '2' && verificaAsignacionFamiliar($db, $idTrabajador, $anhio.'-'.$mes.'-14') == 0) ){
  		$asigFam = $item['asignacionFamiliar'];
		} else {
			$asigFam = 0;
		}
		*/

		if ($quin == '2' ){
  		$asigFam = $item['asignacionFamiliar'];
      $arrData['polizDeSeg']['valor'] = round($item['valorPoliza'] * $item['remuneracionBasica']/100,2) ;
		} else {
			$asigFam = 0;
      //$arrData['polizDeSeg']['valor'] = $item['valorPoliza'];
		}

  	$arrData['sobreElTrabajador']['idTrabajador']  = $idTrabajador;
    /*  201012 Los administrativos pasan a ser igual que los demás en cuanto a pago quincenal
    $arrData['sobreElTrabajador']['remuneracionQuin']  =  ($item['tipoTrabajador'] == 'Administrativo')?$item['remuneracionBasica']:$item['remuneracionBasica']/2;*/
    $arrData['sobreElTrabajador']['remuneracionQuin']  = $item['remuneracionBasica']/2;
  	$arrData['sobreElTrabajador']['asignacionFamQuin']  = $asigFam;
  	$arrData['sobreElTrabajador']['entidadPension']  = $item['entidadPension'];
  	$arrData['sobreElTrabajador']['tipoComision']  = $item['tipoComision'];
  	$arrData['sobreElTrabajador']['tipoTrabajador']  = $item['tipoTrabajador'];
  	$arrData['sobreElTrabajador']['descontarSeguro']  = $item['descontarSeguro'];
  	$arrData['sobreElTrabajador']['descontarComisionAfp']  = $item['descontarComisionAfp'];
    $arrData['sobreElTrabajador']['descontarAporteAfp']  = $item['descontarAporteAfp'];

    /*  201012 Los administrativos pasan a ser igual que los demás en cuanto a pago quincenal
  	$arrData['renta5ta']['valor'] =  ($item['tipoTrabajador'] == 'Administrativo')?$item['renta5ta']:$item['renta5ta']/2; */
  	$arrData['renta5ta']['valor'] = $item['renta5ta']/2; 

  	$entidadPension = $item['entidadPension'];
    
    if(isset($entidadPension)){
    	if ($item['tipoComision'] == 'Flujo' )
    	  $porPriComis = $comFlujo["$entidadPension"];
    	else if ($item['tipoComision'] == 'Mixto' )
    	  $porPriComis = $comMixta["$entidadPension"];
    	else
    	  $porPriComis = 0;
    	$porPriPrima = $primaSeg["$entidadPension"];
    	$porPriAport = $porcOblig["$entidadPension"];
    } else {
      $porPriComis = $porPriPrima = $porPriAport = 0;
    }

    if ($item['modoSueldo'] == 'Fijo'){
    	  /* 201012 Los administrativos pasan a ser igual que los demás en cuanto a pago quincenal
	      $haberBasico = ($item['tipoTrabajador'] == 'Administrativo')?$item['remuneracionBasica']:$item['remuneracionBasica']/2;*/
	      $haberBasico = $item['remuneracionBasica']/2;
	      $saldoValorRol = ($arrData['sobreElTrabajador']['valorRol'] - $haberBasico >0)?($arrData['sobreElTrabajador']['valorRol'] - $haberBasico):0;//Verificar este calculo	      
	  } else {
	    if ($arrData['sobreElTrabajador']['remuneracionQuin'] < $arrData['sobreElTrabajador']['valorRol']){
	    	$haberBasico = $item['remuneracionBasica']/2;
	     	$saldoValorRol = ($arrData['sobreElTrabajador']['valorRol'] - $haberBasico >0)?($arrData['sobreElTrabajador']['valorRol'] - $haberBasico):0;//Verificar este calculo
	    } else {
	     	$costoDia = $arrData['sobreElTrabajador']['remuneracionQuin'] / 15;
	     	$haberBasico = round($costoDia * $arrData['sobreElTrabajador']['diasTrabaj'],2);
	     	$saldoValorRol = ($arrData['sobreElTrabajador']['valorRol'] - $haberBasico >0)?($arrData['sobreElTrabajador']['valorRol'] - $haberBasico):0;//Verificar este calculo
	     	//insertaRegPrestamoAuto($db,$idTrabajador,$anhio,$mes,'Costo Dia',$costoDia,'1',$fchQuincena,'CostoDia',$nombreProceso);
	    }
	  }
  }

  $haberBasico = round($haberBasico,2);


  $arrData['habBasico']['valor'] = $haberBasico;
  $arrData['asigFam']['valor'] = $arrData['sobreElTrabajador']['asignacionFamQuin'];

  //Calculo de la movilidad supeditada
  $topeMovilSuped = isset ($_SESSION['topeMovilSuped'])?$_SESSION['topeMovilSuped']:0;
  $auxMovilSupedOriginal = $arrData['movilSuped']['valor'];

  $auxSaldoMovilSuped = ($auxMovilSupedOriginal > $topeMovilSuped)?$auxMovilSupedOriginal-$topeMovilSuped:0;
  $auxMovilSuped = $saldoValorRol + $arrData['movilSuped']['valor'];
  $auxBonifVar = 0;

  //if ($idTrabajador == '46151123' ) echo "saldo valor rol  $saldoValorRol  sobre movilidad $auxMovilSupedOriginal > $topeMovilSuped, aux movil suped   $auxMovilSuped, haber basico $haberBasico  ";

  if ($auxMovilSuped > $haberBasico){
  	if ($haberBasico <= $topeMovilSuped){
  		$arrData['movilSuped']['valor'] = $haberBasico;
  	  $auxBonifVar = $auxMovilSuped - $haberBasico;
  	  $arrData['bonifVar']['valor'] += $auxBonifVar;
  	} else if ($auxMovilSuped > $topeMovilSuped){
  	  $arrData['movilSuped']['valor'] = $topeMovilSuped;
  	  $auxBonifVar = $auxMovilSuped - $topeMovilSuped;
  	  $arrData['bonifVar']['valor'] += $auxBonifVar;
  	} else {
  	  $arrData['movilSuped']['valor'] = $auxMovilSuped;	
  	}
  } else {
  	if ($auxMovilSuped > $topeMovilSuped){
  	  $arrData['movilSuped']['valor'] = $topeMovilSuped;
  	  $auxBonifVar = $auxMovilSuped - $topeMovilSuped;
  	  $arrData['bonifVar']['valor'] += $auxBonifVar;
  	} else {
  	  $arrData['movilSuped']['valor'] = $auxMovilSuped;	
  	}
  }


  $ingresoGravable = 
  $arrData['hrsExtra25']['valor'] +  $arrData['hrsExtra35']['valor'] + $arrData['remDomFer']['valor'] + 
  $arrData['vacTruncas']['valor'] +  $arrData['remVacac']['valor'] + $arrData['habBasico']['valor'] + 
  $arrData['asigFam']['valor'] +  $arrData['asigVacac']['valor'] + $arrData['bonifCts']['valor'] + 
  $arrData['servAdic']['valor'] +  $arrData['bonifVar']['valor'] + $arrData['licGoce']['valor'] - 
  $arrData['inasisten']['valor'] ;

  if ($arrData['sobreElTrabajador']['entidadPension'] != 'ONP') $ingresoGravable += $arrData['subsidio']['valor'];
  

  $dsctoComis = round($porPriComis * $ingresoGravable /100,2);
  $dsctoPrimaSeg = round($porPriPrima * $ingresoGravable /100,2);
  $dsctoAportObl = round($porPriAport * $ingresoGravable /100,2);

  if ($arrData['sobreElTrabajador']['descontarSeguro'] == 'No') $dsctoPrimaSeg = 0;
  if ($arrData['sobreElTrabajador']['descontarComisionAfp'] == 'No') $dsctoComis = 0;
  if ($arrData['sobreElTrabajador']['descontarAporteAfp'] == 'No') $dsctoAportObl = 0; //Por ahora no

  if ($entidadPension == 'ONP'){
  	$arrData['onp']['valor'] = $dsctoAportObl;
  } else {
   	$arrData['comision']['valor'] = $dsctoComis;
   	$arrData['seguro']['valor'] = $dsctoPrimaSeg;
   	$arrData['aporte']['valor'] = $dsctoAportObl;
  }

  $egresosDctosPorAporte =
  $arrData['esSaludVid']['valor'] +  $arrData['renta5ta']['valor'] + $arrData['onp']['valor'] + 
  $arrData['comision']['valor'] +  $arrData['seguro']['valor'] + $arrData['aporte']['valor'] + 
  $arrData['sumaEsS']['valor'];

  if ($item['modoSueldo'] == 'Fijo'){

	}	else if ($item['modoSueldo'] == 'Preferencial'){
		$arrData['bonoPrefer']['valor'] = ($egresosDctosPorAporte > 0)?$egresosDctosPorAporte:0;
	}

  //$arrData['movilSuped']['valor'] = $saldoValorRol;


  $ingresoNoGravable =
  $arrData['bonifExtra']['valor'] +  $arrData['bonifExPro']['valor'] + $arrData['gratiFPyNv']['valor'] + 
  $arrData['gratiFPPro']['valor'] +  $arrData['bonoAlim']['valor'] + $arrData['bonoProd']['valor'] + 
  $arrData['canastaNav']['valor'] +  $arrData['ctsMayNov']['valor'] + $arrData['movilSuped']['valor'] + 
  $arrData['utilidad']['valor'] +  $arrData['bonoPrefer']['valor'] + $arrData['devFondoG']['valor'] ;

  $egresosOtros =
  $arrData['adelQ']['valor'] +  $arrData['adelVacac']['valor'] + $arrData['adelGratif']['valor'] + 
  $arrData['adelCts']['valor'] +  $arrData['adelCanNav']['valor'] + $arrData['adelUtil']['valor'] + 
  $arrData['dsctoResp']['valor'] +  $arrData['dsctoPrest']['valor'] + $arrData['dsctoCompra']['valor'] + 
  $arrData['dsctoFndGar']['valor'];

  $arrData['totales']['ingresoGravable'] = $ingresoGravable;
  $arrData['totales']['ingresoNoGravable'] = $ingresoNoGravable;
  $arrData['totales']['egresosOtros'] = $egresosOtros;
  $arrData['totales']['egresosDctosPorAporte'] = $egresosDctosPorAporte;

  /*
  if ($idTrabajador == '71127281'){
  	echo "<pre>";
  	print_r($arrData);
  	echo "</pre>";
  }
  */  

  $cant = insertaDataQuincenaContab($db,$anhio,$mes,$quin,$arrData);
  return $cant;
}

function  insertaDataQuincenaContab($db,$anhio,$mes,$quin,$arrData){
  $idTrabajador = $arrData['sobreElTrabajador']['idTrabajador'];
	if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
  else  $fchQuincena = $anhio.'-'.$mes.'-28';


	$cant = $db->exec("DELETE FROM  `quincenadetallecontab` WHERE fchQuincena = '$fchQuincena' AND idTrabajador = '$idTrabajador' ");
	//Fin de

  $cont = 0;
  $primero = "Si";
  $data = "";
	foreach ($arrData as $key => $value) {
		if ($key == 'sobreElTrabajador' || $key == 'totales' ) continue;
    $cadena = "";
    $codContable = $value['cod01'];
    $codPlame = $value['cod02'];
    $valor = $value['valor'];

    if ($valor == 0) continue; //Para que considere los valores en cero
    if ($primero == "Si"){
    	$primero = "No";
    } else {
    	$data .= ",";
    }

    $data .= "('$fchQuincena','$idTrabajador','$key','$codContable','$codPlame','$valor', curdate()) ";
    
    if ($key == 'LicGoce')  echo " codContable $codContable  codPlame $codPlame valor $valor";


	}

	$cadCompleta = "INSERT INTO `quincenadetallecontab` (`fchQuincena` ,`idTrabajador` ,`id` ,`codContable` ,`codPlame` ,`valor` ,`creacFch`) VALUES $data ";
  $cant = $db->exec($cadCompleta);

  if ($quin == '2'){
    $fchQuinAnt = substr($fchQuincena, 0,8)."14";
    $cadCompleta = "INSERT INTO `quincenadetallecontab` (`fchQuincena` ,`idTrabajador` ,`id` ,`codContable` ,`codPlame` ,`valor` ,`creacFch`, ultProceso) SELECT '$fchQuincena', `t2`.idTrabajador, 'polizDeSeg', '182141', '0803', `t3`.valorPoliza, curdate(), 'faltante' FROM ( SELECT `quidet`.idTrabajador, `quidet`.categoria, `t1`.valor FROM `quincenadetalle` AS quidet LEFT JOIN ( SELECT fchQuincena, idTrabajador, id, valor FROM `quincenadetallecontab` WHERE fchQuincena = '$fchQuincena' AND id = 'polizDeSeg' ) AS t1 ON `quidet`.idTrabajador = `t1`.idTrabajador WHERE `quidet`.categoria = '5ta' AND quincena = '$fchQuinAnt' AND `t1`.valor IS NULL ) AS t2 , ( SELECT idTrabajador, categTrabajador, `trab`.idPoliza, valorPoliza FROM trabajador AS trab, polizas WHERE categTrabajador = '5ta' AND `trab`.idPoliza = `polizas`.idPoliza ) AS t3 WHERE `t2`.idTrabajador = `t3`.idTrabajador";
    $cant = $db->exec($cadCompleta);  	
  }

  $cadCompleta = "DELETE FROM `quincenadetallecontab` WHERE id = 'AsigFam' AND valor = 0 AND fchQuincena = '$fchQuincena' ";
  $cant = $db->exec($cadCompleta);

	return 1;
}

  function verCasosEspeciales($db,$dni,$anhio,$mes,$quin){
  	$fchQuincena = ($quin == '1')?"$anhio-$mes-14":"$anhio-$mes-28";
  	$nombreProceso = "Caso Especial $fchQuincena";
  	$dataTrabajador = buscarTrabajador($db,$dni);
  	foreach ($dataTrabajador as $key => $item) {
      $remuneracionQuin = $item['remuneracionBasica']/2;
    	$asignacionFamQuin  = $item['asignacionFamiliar'];
    	$entidadPension  = $item['entidadPension'];
    	$tipoComision    = $item['tipoComision'];
    	$estadoTrabaj    = $item['estadoTrabajador'];
    }

    $elimina = $db->prepare("DELETE FROM `prestamo` WHERE idTrabajador = :dni AND anhio = :anhio AND `mes` LIKE :mes AND tipoItem IN ('AsigFam') AND fchQuincena = '$fchQuincena'");
    $elimina->bindParam('dni',$dni);
    $elimina->bindParam('anhio',$anhio);
    $elimina->bindParam('mes',$mes);  
    $elimina->execute();

    if ($asignacionFamQuin > 0 && $estadoTrabaj == 'Activo' ){
	  	  if ($quin == '2'){
		      insertaRegPrestamoAuto($db, $dni,$anhio,$mes,'Asign Familiar',$asignacionFamQuin,'1',$fchQuincena,'AsigFam',$nombreProceso);
		    }

		  }
  }

  function completarAdelQ($db,$anhioMes){
  	$qry = "INSERT INTO quincenadetallecontab (fchQuincena, idTrabajador, id, codContable, codPlame, valor )  (SELECT '".$anhioMes."28', idTrabajador, 'adelQ', '141201', '0701', totPagar FROM `quincenadetalle` WHERE `quincena` = '".$anhioMes."14' AND `categoria` LIKE '5ta')";

  	//echo $qry; 
  	$db->exec($qry);

  }

  function buscarDeudasPendientesDelTrabajador($db){
  	$consulta = $db->prepare("SELECT * FROM (SELECT ocurrencon.subTipoOcurrencia AS subTipo, t1.idTrabajador, ocurrencon.montoTotal AS monto , ocurrencon.nroCuotas, ocurrencon.nroCuota, ocurrencon.fchPago, t1.nombCompleto, ocurrencon.tipoOcurrencia, ocurrencon.descripcion, ocurrencon.montoCuota,  ocurrencon.pagado , estadoTrabajador , 'Ocurrencia' AS origen FROM (SELECT ocucon.idTrabajador, concat(apPaterno,' ', apMaterno,' ', nombres) AS nombCompleto, fchDespacho, correlativo, tipoOcurrencia, descripcion, montoTotal, if(estadoTrabajador = 'Inactivo', 'SinUso', estadoTrabajador) AS estadoTrabajador  FROM `ocurrenciaconsulta` AS ocucon, trabajador WHERE ocucon.idTrabajador = trabajador.idTrabajador AND ocucon.tipoOcurrencia IN ('Descuento') AND `pagado` != 'Si' GROUP BY fchDespacho, correlativo, tipoOcurrencia, descripcion, montoTotal ,idTrabajador) AS t1, ocurrenciaconsulta AS ocurrencon WHERE ocurrencon.fchDespacho = t1.fchDespacho AND ocurrencon.correlativo = t1.correlativo AND ocurrencon.tipoOcurrencia = t1.tipoOcurrencia AND ocurrencon.idTrabajador = t1.idTrabajador AND ocurrencon.descripcion = t1.descripcion AND ocurrencon.montoTotal = t1.montoTotal 
UNION
SELECT  if(predet.tipoItem = 'DsctoTrabaj', predet.subTipoItem,predet.tipoItem) AS subTipo , t1.idTrabajador, t1.monto, t1.nroCuotas, predet.nroCuota, predet.fchPago, t1.nombCompleto, predet.tipoItem , predet.descripcion, predet.montoCuota, predet.pagado, estadoTrabajador , 'AsigTrab' AS origen FROM (SELECT predet.`idTrabajador`, concat(apPaterno, ' ', apMaterno, ' ', nombres) AS nombCompleto, prestamo.`descripcion`, prestamo.`tipoItem`, prestamo.`monto`, prestamo.nroCuotas, if(estadoTrabajador = 'Inactivo', 'SinUso', estadoTrabajador) AS estadoTrabajador  FROM `prestamodetalle` AS predet, prestamo ,trabajador WHERE predet.idTrabajador = prestamo.idTrabajador AND predet.descripcion = prestamo.descripcion AND predet.tipoItem = prestamo.tipoItem AND predet.monto = prestamo.monto AND predet.idTrabajador = trabajador.idTrabajador AND prestamo.tipoItem IN ('DsctoTrabaj', 'DsctoUnif', 'Compra', 'Prestamo') AND predet.pagado != 'Si' GROUP BY idTrabajador, descripcion, tipoItem, monto) AS t1, prestamodetalle AS predet WHERE t1.idTrabajador = predet.idTrabajador AND t1.descripcion = predet.descripcion AND t1.tipoItem = predet.tipoItem AND t1.monto = predet.monto) AS t2 order by idTrabajador ");
    $consulta->execute();
    return $consulta->fetchAll();

  }

  function buscarPredeterminado($db,$idTrabajador){
  	$consulta = $db->prepare("SELECT nroDocTrab, tipoDocTrab  FROM `trabajdocumentos` WHERE `idTrabajador` LIKE :idTrabajador AND `estado` = 'Predeterminado'");
  	$consulta->bindParam(':idTrabajador',$idTrabajador);
    $consulta->execute();
    $aux = $consulta->fetchAll();
    if ($aux){
      foreach ($aux as $key => $value) {
      	$salida = $value["tipoDocTrab"]."-".$value["nroDocTrab"];
      }    	
    } else {
    	$salida = "No";
    }
    //echo "Salida ".$salida;
    return $salida;
  }

  function buscarTrabajTipoDoc($db, $estadoTipoDoc = NULL){
   	$where = " 1 ";
  	if ($estadoTipoDoc != NULL) $where .= " AND  `estadoTipoDoc` = :estadoTipoDoc  ";

  	$consulta = $db->prepare("SELECT `idTipoDoc`, `descripTipoDoc`, `codPlame`, `estadoTipoDoc`, `creacUsuario`, `creacFch`, `editaUsuario`, `editaFch`  FROM `trabajtiposdocum` WHERE  $where ");
    if ($estadoTipoDoc != NULL) $consulta->bindParam(':estadoTipoDoc',$estadoTipoDoc);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function buscarIdTrabDeNroDoc($db, $nroDocTrab){
  	$consulta = $db->prepare("SELECT `idTrabajador` FROM `trabajdocumentos` WHERE nroDocTrab = :nroDocTrab ");
    $consulta->bindParam(':nroDocTrab',$nroDocTrab);
    $consulta->execute();
    $aux = $consulta->fetchAll();
    foreach ($aux as $key => $value) {
    	return $value["idTrabajador"];
    }
  }

  function buscarTrabajadoresOpciones($db, $tipoTrabajador = NULL, $estado = NULL){
  	//Esta es la que se debe modificar para la nueva opcion de carga
  	$where = " 1 ";
  	if($tipoTrabajador != NULL) $where .= " AND tipoTrabajador = :tipoTrabajador ";
  	if($estado != NULL) $where .= " AND estadoTrabajador = :estado ";

  	//echo "TIPO ROL $tipoTrabajador, ESTADO $estado";
  	$consulta = $db->prepare("SELECT `idTrabajador`, `tipoDocTrab`, `nroDocTrab`, `fchCaducidad`, `tipoTrabajador`, `categTrabajador`, `ruc`, `modoSueldo`, `estadoTrabajador`, `apPaterno`, `apMaterno`, `nombres`, `fchNacimiento`,`bancoNombre`, `bancoNroCuenta`, `bancoTipoCuenta`, `bancoMoneda`, `remuneracionBasica`, `asignacionFamiliar`, `minimoSemanal`, `cajaChica`, `renta5ta`, `diasVacacAnual`, `manejaLiquidacion`, `fondoGarantia`, `fondoGaranRetenido`, `esMaster`, `precioMaster`, `deseaDscto`, `deseaDsctoOnp`, `entidadPension`, `tipoComision`, `asumeEmpresa`, `descontarSeguro`, `cuspp`, `codTrabajador`, `formaPago`, `tallaCamisa`, `tallaPantalon`, `tallaBotas`, `tallaPolo`, `tallaCasaca`, `usuario`, `fchCreacion`, `usuarioUltimoCambio`, `fchUltimoCambio` FROM `trabajador` WHERE  $where ");
    if ($tipoTrabajador != NULL) $consulta->bindParam(':tipoTrabajador',$tipoTrabajador);
    if ($estado != NULL) $consulta->bindParam(':estado',$estado);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function buscarPolizas($db){
    $consulta = $db->prepare("SELECT `idPoliza`, `nombPoliza`, `valorPoliza`, `descripPoliza`, `estadoPoliza`, `creacUsuario`, `creacFch`, `editaUsuario`, `editaFch` FROM `polizas` WHERE estadoPoliza = 'Activo' ");
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function contratos5taFaltantes($db){
    $consulta = $db->prepare("SELECT `trabajador`.idTrabajador , concat(apPaterno,' ', apMaterno,' ', nombres) AS nombCompleto, tipoTrabajador, `trabajcontratos`.idTrabajador As trabContratos FROM `trabajador` LEFT JOIN trabajcontratos ON `trabajador`.idTrabajador = `trabajcontratos`.idTrabajador AND `trabajcontratos`.estado = 'Activo' WHERE `categTrabajador` = '5ta' AND `estadoTrabajador` = 'Activo' AND `trabajcontratos`.idTrabajador IS NULL ORDER BY `nombCompleto` ASC  ");
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function buscarContratosFaltantes($db){
    $consulta = $db->prepare("SELECT `trabajador`.`idTrabajador`, `apPaterno`, `apMaterno`, `nombres`, tipoTrabajador, idContrato FROM `trabajador` LEFT JOIN (SELECT * FROM trabajcontratos WHERE estado = 'Activo') AS t1 ON `trabajador`.idTrabajador = `t1`.idTrabajador WHERE `categTrabajador` = '5ta' AND `estadoTrabajador` = 'Activo' AND idContrato IS NULL ");
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function buscarVacacionesPorProgramar($db){
    //LAEM 211106
    $consulta = $db->prepare("SELECT idVacacion, `trabajcontratovacacs`.idContrato, fchPuedeIniciar, diasUsados , DATEDIFF( fchPuedeIniciar, now()) AS diferencia, if(DATEDIFF( fchPuedeIniciar, now()) <0 , 'Vencido', 'Por vencer') AS observ , `trabajcontratovacacs`.estado, apPaterno, apMaterno, nombres, concat(apPaterno,' ', apMaterno,', ',nombres) AS nombCompleto FROM `trabajcontratovacacs`, trabajcontratos, trabajador WHERE `trabajcontratovacacs`.`idContrato`= `trabajcontratos`.idContrato AND `trabajador`.idTrabajador = `trabajcontratos`.idTrabajador AND `trabajcontratovacacs`.estado = 'Pendiente' AND DATEDIFF( fchPuedeIniciar, now()) < 30 ORDER BY `diferencia` DESC ");
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function buscarInfoVacacUsadas($db, $idSalida){
    $consulta = $db->prepare("SELECT `idSalida`, `trabcon`.`idTrabajador`, `trabcon`.`regimen`, concat(`nombres`, ' ', `apPaterno`, ' ', `apMaterno`) AS `nombCompleto` , `trabconvac`.`idContrato`, `trabconvacusad`.`idVacacion`, `trabconvacusad`.`fchInicio`, `trabconvacusad`.`fchFin`, `trabconvacusad`.`fchQuincIniCalculo`, `trabconvacusad`.`fchQuincFinCalculo`, `pagoVacac`, `trabconvacusad`.`estado`, `fchAprob`, `usuAprob`, `trabconvacusad`.`creacUsuario`, `trabconvacusad`.`creacFch`, `trabconvacusad`.`editaUsuario`, `trabconvacusad`.`editaFch`, `trabajdocumentos`.nroDocTrab, `trabajdocumentos`.`tipoDocTrab` , `remuneracionBasica`, `asignacionFamiliar`, `entidadPension`, `tipoComision`,  sum(`porcentObligat` + `primaSeg` + if( `tipoComision` = 'Mixto', `comisionMixta`, `comisionFlujo` ) ) AS `porcPension`, `trabconvac`.`fchIniPeriodo`, `trabconvac`.`fchPuedeIniciar`  FROM `trabajcontratovacacusadas` AS `trabconvacusad`, `trabajcontratovacacs` AS `trabconvac`, `trabajcontratos` AS `trabcon`, `pension`, `trabajador` AS `trab` LEFT JOIN `trabajdocumentos` ON `trab`.`idTrabajador` = `trabajdocumentos`.`idTrabajador` AND `trabajdocumentos`.`estado` = 'Predeterminado' WHERE  `trab`.`entidadPension` = `pension`.nombre AND `trab`.idTrabajador = `trabcon`.idTrabajador AND `trabcon`.idContrato = `trabconvac`.idContrato AND `trabconvacusad`.idVacacion = `trabconvac`.idVacacion AND idSalida = :idSalida "); 
    $consulta->bindParam(':idSalida',$idSalida);
    $consulta->execute();
    return $consulta->fetch();
  }

  function sumaIngGravPorMes($db, $idTrabajador, $fchIni, $fchFin){
    $consulta = $db->prepare("SELECT substring(fchQuincena,1,7) AS mes, fchQuincena, quindet.id, idTrabajador, sum(valor) AS pagoMesVacac FROM quincenadetallecontab AS quindet, planillaitems AS plaitem WHERE plaitem.id = quindet.id AND grupoNro = '1' AND plaitem.id NOT IN ('inasisten') AND fchQuincena BETWEEN :fchIni AND :fchFin AND idTrabajador = :idTrabajador GROUP BY substring(fchQuincena,1,7)");
    $consulta->bindParam(':idTrabajador',$idTrabajador);
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->bindParam(':fchFin',$fchFin);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function losDetallesVacaciones($db, $idTrabajador, $fchIni, $fchFin){
    $consulta = $db->prepare("SELECT substring(`fchQuincena`,1,7) AS mes, `idTrabajador`, sum(`valor`) AS pagoMesVacac FROM `quincenadetallecontab` AS `quindet`, `planillaitems` AS `plaitem` WHERE plaitem.id = quindet.id AND `grupoNro` = '1' AND plaitem.id NOT IN ('licGoce','inasisten') AND `fchQuincena` BETWEEN :fchIni AND :fchFin AND `idTrabajador` = :idTrabajador GROUP BY substring(`fchQuincena`,1,7)");
    $consulta->bindParam(':idTrabajador',$idTrabajador);
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->bindParam(':fchFin',$fchFin);
    $consulta->execute();
    return $consulta->fetchAll();
  }

?>
