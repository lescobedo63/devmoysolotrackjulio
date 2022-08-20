<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  global $servidor, $bd, $usuario, $contrasenia;
  $db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);

  function logAccionDespacho($db,$descripcion,$idTrabajador,$placa){
    $user = $_SESSION['usuario'];
    $inserta = $db->prepare("INSERT INTO `logaccion` (`id`, `descripcion`, `idTrabajador`,`placa`,`usuario`, `fecha`) VALUES (NULL, :descripcion, :idTrabajador,:placa,:usuario, NOW())");
    $inserta->bindParam(':descripcion',$descripcion);
    $inserta->bindParam(':idTrabajador',$idTrabajador);
    $inserta->bindParam(':placa',$placa);
    $inserta->bindParam(':usuario',$user);
    $inserta->execute();
  };

function eliminaDsctoTercero($db,$fchDespacho,$correlativo){
  $elimina = $db->prepare("DELETE FROM ocurrencia WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `tipoOcurrencia` = 'terceroMoy'");
  $elimina->bindParam(':fchDespacho',$fchDespacho);
  $elimina->bindParam(':correlativo',$correlativo);
  $elimina->execute();
    
  $elimina = $db->prepare("DELETE FROM ocurrenciaconsulta WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `tipoOcurrencia` = 'terceroMoy'");
  $elimina->bindParam(':fchDespacho',$fchDespacho);
  $elimina->bindParam(':correlativo',$correlativo);
  $elimina->execute();
}

function buscarTodosLosDespachos($db) { //esta no se está usando
  $consulta = $db->prepare("SELECT `despacho`.fchDespacho, `despacho`.correlativo, hraInicio, hraFin, placa, valorServicio, concat(idCliente,' - ',`cliente`.nombre ) as idCliente, cuenta, concluido, concat(idConductor,' - ' ,`trabajador`.apPaterno,' ', `trabajador`.apMaterno,', ', nombres) as idConductor  ,count(`ocurrencia`.fchDespacho) as ocurrencias FROM trabajador, cliente, despacho left join ocurrencia on `despacho`.fchDespacho = `ocurrencia`.fchDespacho AND `despacho`.correlativo = `ocurrencia`.correlativo WHERE cliente.idRuc = despacho.idCliente AND trabajador.idTrabajador = despacho.idConductor group by  despacho.fchDespacho, despacho.correlativo ORDER BY fchDespacho DESC, correlativo ASC");
	$consulta->execute();
	return $consulta->fetchAll();
}

/*
function buscarTodosLosDespachosDelDiaoNoAtendidos($db) {
  $consulta = $db->prepare("SELECT `despacho`.`fchDespacho`, `despacho`.`correlativo`, `despacho`.`hraInicio`, `despacho`.`hraFin`, `despacho`.`fchCreacion`, `despacho`.`placa`, `despacho`.`valorServicio`, concat(`despacho`.`idCliente`,' - ',`cliente`.`nombre` ) as idCliente, `despacho`.idCliente as id, `nombre` , `despacho`.`cuenta`, `despacho`.`concluido`, `despacho`.`pagado`, concat(trabajador.`idTrabajador`,' - ' ,`apPaterno`,' ',`apMaterno`,', ', `nombres`) as idConductor ,count(`ocurrencia`.`fchDespacho`) as ocurrencias, `despachoguiaporte`.`guiaPorte`, `despacho`.`pagoEnPlanilla`, `despachovehiculotercero`.`docPagoTercero`,  `despachovehiculotercero`.`docTercero`,`docpagotercero`.`nroDocLiq`, `docpagotercero`.`estado`, sum(if(despachopersonal.pagado = 'Md',1,0)) AS marcado  ,  `despacho`.usuarioGrabaFin, `despacho`.usuario,

    `cobrarDespachos`.codigo AS despCodigo, `cobrarDespachos`.docCobranza AS despDocCob, `cobrarDespachos`.tipoDoc AS despTipoDoc, `cobrarDespachos`.estado AS despEstado,
     `cobrarAuxAdic`.codigo AS auxAdicCodigo, `cobrarAuxAdic`.docCobranza AS auxAdicDocCob, `cobrarAuxAdic`.tipoDoc AS auxAdicTipoDoc, `cobrarAuxAdic`.estado AS auxAdicEstado,
    `cobrarHrasAdic`.codigo AS hrasAdicCodigo, `cobrarHrasAdic`.docCobranza AS hrasAdicDocCob, `cobrarHrasAdic`.tipoDoc AS hrasAdicTipoDoc, `cobrarHrasAdic`.estado AS hrasAdicEstado 

    FROM `trabajador`, `cliente`, `despachopersonal`, `despacho` 
    left join `ocurrencia` ON `despacho`.`fchDespacho` = `ocurrencia`.`fchDespacho` AND `despacho`.`correlativo` = `ocurrencia`.`correlativo` 
    left join `despachoguiaporte` ON  `despachoguiaporte`.`fchDespacho`=  `despacho`.`fchDespacho` AND  `despachoguiaporte`.`correlativo`=  `despacho`.`correlativo` 
    left join `despachovehiculotercero` ON `despachovehiculotercero`.`fchDespacho`= `despacho`.`fchDespacho` AND `despachovehiculotercero`.`correlativo`= `despacho`.`correlativo`
    
    left join (SELECT cobrar.codigo, cobrar.fchDespacho, cobrar.correlativo, cobrar.docCobranza, cobrar.tipoDoc, doccobranza.estado FROM `despachodetallesporcobrar` AS cobrar LEFT JOIN  doccobranza ON cobrar.docCobranza = doccobranza.docCobranza AND cobrar.tipoDoc = doccobranza.tipoDoc WHERE cobrar.codigo = 'Despacho' AND  cobrar.fchDespacho >= '$fchAntigua') AS cobrarDespachos ON  `cobrarDespachos`.`fchDespacho`= `despacho`.`fchDespacho` AND `cobrarDespachos`.`correlativo`=  `despacho`.`correlativo` 

    left join (SELECT cobrar.codigo, cobrar.fchDespacho, cobrar.correlativo, cobrar.docCobranza, cobrar.tipoDoc, doccobranza.estado FROM `despachodetallesporcobrar` AS cobrar LEFT JOIN  doccobranza ON cobrar.docCobranza = doccobranza.docCobranza AND cobrar.tipoDoc = doccobranza.tipoDoc WHERE cobrar.codigo = 'AuxAdic' AND DATEDIFF(curdate(),cobrar.fchDespacho) <= 365) AS cobrarAuxAdic ON  `cobrarAuxAdic`.`fchDespacho`= `despacho`.`fchDespacho` AND `cobrarAuxAdic`.`correlativo`=  `despacho`.`correlativo` 

    left join (SELECT cobrar.codigo, cobrar.fchDespacho, cobrar.correlativo, cobrar.docCobranza, cobrar.tipoDoc, doccobranza.estado FROM `despachodetallesporcobrar` AS cobrar LEFT JOIN  doccobranza ON cobrar.docCobranza = doccobranza.docCobranza AND cobrar.tipoDoc = doccobranza.tipoDoc WHERE cobrar.codigo = 'HrasAdic' AND DATEDIFF(curdate(),cobrar.fchDespacho) <= 365) AS cobrarHrasAdic ON  `cobrarHrasAdic`.`fchDespacho`= `despacho`.`fchDespacho` AND `cobrarHrasAdic`.`correlativo`=  `despacho`.`correlativo` 

    left join docpagotercero ON `despachovehiculotercero`.`docPagoTercero`= `docpagotercero`.`docPagoTercero` 

    WHERE `despachopersonal`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachopersonal`.`correlativo` = `despacho`.`correlativo` AND  `cliente`.`idRuc` = `despacho`.`idCliente` AND `despachopersonal`.`tipoRol` = 'Conductor' AND trabajador.idTrabajador = despachopersonal.idTrabajador  AND (`despacho`.`fchDespacho` = curdate() or `despacho`. `concluido` = 'No' )   AND DATEDIFF(curdate(),despacho.fchDespacho) <= 365 group by  `despacho`.`fchDespacho`, `despacho`.`correlativo` ORDER BY `despacho`.`fchDespacho` DESC, `despacho`.`correlativo` ASC");
	$consulta->execute();
	return $consulta->fetchAll();
}
*/

function buscarTodosLosDespachosDelDiaoNoAtendidos($db){
  $fchAntigua = restaDias(365);
  $consulta = $db->prepare("SELECT `despacho`.`fchDespacho`, `despacho`.`correlativo`, `despacho`.`hraInicio`, `despacho`.`hraFin`, `despacho`.`fchCreacion`, `despacho`.`placa`, `despacho`.`valorServicio`, concat(`despacho`.`idCliente`,' - ',`cliente`.`nombre` ) as idCliente, `despacho`.idCliente as id, `nombre` , `despacho`.`cuenta`, `despacho`.`concluido`, `despacho`.`pagado`, despachopersonal.idConductor ,count(`ocurrencia`.`fchDespacho`) as ocurrencias, `despachoguiaporte`.`guiaPorte`, `despacho`.`pagoEnPlanilla`, `despachovehiculotercero`.`docPagoTercero`,  `despachovehiculotercero`.`docTercero`,`docpagotercero`.`nroDocLiq`, `docpagotercero`.`estado`, sum(if(despachopersonal.pagado = 'Md',1,0)) AS marcado  ,  `despacho`.usuarioGrabaFin, `despacho`.usuario, `cobrarDespachos`.codigo AS despCodigo, `cobrarDespachos`.docCobranza AS despDocCob, `cobrarDespachos`.tipoDoc AS despTipoDoc, `cobrarDespachos`.estado AS despEstado, `cobrarAuxAdic`.codigo AS auxAdicCodigo, `cobrarAuxAdic`.docCobranza AS auxAdicDocCob, `cobrarAuxAdic`.tipoDoc AS auxAdicTipoDoc, `cobrarAuxAdic`.estado AS auxAdicEstado, `cobrarHrasAdic`.codigo AS hrasAdicCodigo, `cobrarHrasAdic`.docCobranza AS hrasAdicDocCob, `cobrarHrasAdic`.tipoDoc AS hrasAdicTipoDoc, `cobrarHrasAdic`.estado AS hrasAdicEstado ,  `clientecuentaproducto`.nombProducto , `despacho`.tipoServicioPago

    FROM  `cliente`,  `despacho` 
    left join (SELECT despachopersonal.fchDespacho, despachopersonal.correlativo, concat(despachopersonal.idTrabajador, '-', apPaterno, ' ', apMaterno, ', ', nombres) AS idConductor, despachopersonal.pagado, despachopersonal.tipoRol FROM despachopersonal, trabajador WHERE despachopersonal.idTrabajador = trabajador.idTrabajador AND despachopersonal.tipoRol = 'Conductor'  AND despachopersonal.fchDespacho >= '$fchAntigua') As despachopersonal ON despachopersonal.fchDespacho = despacho.fchDespacho ANd despachopersonal.correlativo = despacho.correlativo

    left join `ocurrencia` ON `despacho`.`fchDespacho` = `ocurrencia`.`fchDespacho` AND `despacho`.`correlativo` = `ocurrencia`.`correlativo` 
    
    left join `despachoguiaporte` ON  `despachoguiaporte`.`fchDespacho`=  `despacho`.`fchDespacho` AND  `despachoguiaporte`.`correlativo`=  `despacho`.`correlativo` 

    left join `despachovehiculotercero` ON `despachovehiculotercero`.`fchDespacho`= `despacho`.`fchDespacho` AND `despachovehiculotercero`.`correlativo`= `despacho`.`correlativo`
    
    left join (SELECT cobrar.codigo, cobrar.fchDespacho, cobrar.correlativo, cobrar.docCobranza, cobrar.tipoDoc, doccobranza.estado FROM `despachodetallesporcobrar` AS cobrar LEFT JOIN  doccobranza ON cobrar.docCobranza = doccobranza.docCobranza AND cobrar.tipoDoc = doccobranza.tipoDoc WHERE cobrar.codigo = 'Despacho' AND  cobrar.fchDespacho >= '$fchAntigua') AS cobrarDespachos ON  `cobrarDespachos`.`fchDespacho`= `despacho`.`fchDespacho` AND `cobrarDespachos`.`correlativo`=  `despacho`.`correlativo` 

    left join (SELECT cobrar.codigo, cobrar.fchDespacho, cobrar.correlativo, cobrar.docCobranza, cobrar.tipoDoc, doccobranza.estado FROM `despachodetallesporcobrar` AS cobrar LEFT JOIN  doccobranza ON cobrar.docCobranza = doccobranza.docCobranza AND cobrar.tipoDoc = doccobranza.tipoDoc WHERE cobrar.codigo = 'AuxAdic' AND  cobrar.fchDespacho >= '$fchAntigua') AS cobrarAuxAdic ON  `cobrarAuxAdic`.`fchDespacho`= `despacho`.`fchDespacho` AND `cobrarAuxAdic`.`correlativo`=  `despacho`.`correlativo` 

    left join (SELECT cobrar.codigo, cobrar.fchDespacho, cobrar.correlativo, cobrar.docCobranza, cobrar.tipoDoc, doccobranza.estado FROM `despachodetallesporcobrar` AS cobrar LEFT JOIN  doccobranza ON cobrar.docCobranza = doccobranza.docCobranza AND cobrar.tipoDoc = doccobranza.tipoDoc WHERE cobrar.codigo = 'HrasAdic' AND  cobrar.fchDespacho >= '$fchAntigua') AS cobrarHrasAdic ON  `cobrarHrasAdic`.`fchDespacho`= `despacho`.`fchDespacho` AND `cobrarHrasAdic`.`correlativo`=  `despacho`.`correlativo` 

    left join docpagotercero ON `despachovehiculotercero`.`docPagoTercero`= `docpagotercero`.`docPagoTercero` 

    left join `clientecuentaproducto` ON  `clientecuentaproducto`.idProducto = `despacho`.idProducto 

    WHERE   `cliente`.`idRuc` = `despacho`.`idCliente`  AND (`despacho`.`fchDespacho` = curdate() or `despacho`. `concluido` = 'No' )   AND despacho.fchDespacho >= '$fchAntigua' group by  `despacho`.`fchDespacho`, `despacho`.`correlativo` ORDER BY `despacho`.`fchDespacho` DESC, `despacho`.`correlativo` ASC");
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarTodosLosDespachosSoloPersonalDelDiaoNoAtendidos($db){
  //$consulta = $db->prepare("SELECT `despacho`.`fchDespacho`, `despacho`.`correlativo`, `despacho`.`hraInicio`, `despacho`.`hraFin`, `despacho`.`fchCreacion`, `despacho`.`placa`, `despacho`.`valorServicio`, concat(`despacho`.`idCliente`,' - ',`cliente`.`nombre` ) as idCliente, `despacho`.idCliente as id, `nombre` , `despacho`.`cuenta`, `despacho`.`concluido`, `despacho`.`pagado`,count(`ocurrencia`.`fchDespacho`) as ocurrencias, `despachoguiaporte`.`guiaPorte` , `despacho`.`pagoEnPlanilla` , `despachovehiculotercero`.`docPagoTercero`, `despachovehiculotercero`.`docTercero`, `docpagotercero`.`nroDocLiq`, `docpagotercero`.`estado`,  `despacho`.usuarioGrabaFin, `despacho`.usuario, FROM `cliente`, `despacho` left join `ocurrencia` ON `despacho`.`fchDespacho` = `ocurrencia`.`fchDespacho` AND `despacho`.`correlativo` = `ocurrencia`.`correlativo`, `despacho` as desp left join `despachoguiaporte` ON `despachoguiaporte`.`fchDespacho`=  `desp`.`fchDespacho` AND  `despachoguiaporte`.`correlativo`=  `desp`.`correlativo`, `despacho` as despTercero left join `despachovehiculotercero` ON  `despachovehiculotercero`.`fchDespacho`=  `despTercero`.`fchDespacho` AND `despachovehiculotercero`.`correlativo`=  `despTercero`.`correlativo`  left join docpagotercero ON `despachovehiculotercero`.`docPagoTercero`= `docpagotercero`.`docPagoTercero`  WHERE `cliente`.`idRuc` = `despacho`.`idCliente`  AND (`despacho`.`fchDespacho` = curdate() or `despacho`. `concluido` = 'No' )  AND  `despacho`.`fchDespacho`=  `desp`.`fchDespacho` AND  `despacho`.`correlativo`=  `desp`.`correlativo` AND  left(`despacho`.`cuenta`,12) = 'SoloPersonal'  AND `despacho`.`fchDespacho`= `despTercero`.`fchDespacho` AND `despacho`.`correlativo`= `despTercero`.`correlativo`  group by  `despacho`.`fchDespacho`, `despacho`.`correlativo`ORDER BY `despacho`.`fchDespacho` DESC, `despacho`.`correlativo` ASC");

  $consulta = $db->prepare("SELECT `despacho`.`fchDespacho`, `despacho`.`correlativo`, `despacho`.`hraInicio`, `despacho`.`hraFin`, `despacho`.`fchCreacion`, `despacho`.`placa`, `despacho`.`valorServicio`, concat(`despacho`.`idCliente`,' - ',`cliente`.`nombre` ) as idCliente, `despacho`.idCliente as id, `nombre` , `despacho`.`cuenta`, `despacho`.`concluido`, `despacho`.`pagado`,count(`ocurrencia`.`fchDespacho`) as ocurrencias, `despachoguiaporte`.`guiaPorte` , `despacho`.`pagoEnPlanilla` , `despachovehiculotercero`.`docPagoTercero`, `despachovehiculotercero`.`docTercero`, `docpagotercero`.`nroDocLiq`, `docpagotercero`.`estado`,  `despacho`.usuarioGrabaFin, `despacho`.usuario FROM `cliente`, `despacho` left join `ocurrencia` ON `despacho`.`fchDespacho` = `ocurrencia`.`fchDespacho` AND `despacho`.`correlativo` = `ocurrencia`.`correlativo`, `despacho` as desp left join `despachoguiaporte` ON `despachoguiaporte`.`fchDespacho`=  `desp`.`fchDespacho` AND  `despachoguiaporte`.`correlativo`=  `desp`.`correlativo`, `despacho` as despTercero left join `despachovehiculotercero` ON  `despachovehiculotercero`.`fchDespacho`=  `despTercero`.`fchDespacho` AND `despachovehiculotercero`.`correlativo`=  `despTercero`.`correlativo`  left join docpagotercero ON `despachovehiculotercero`.`docPagoTercero`= `docpagotercero`.`docPagoTercero`  WHERE `cliente`.`idRuc` = `despacho`.`idCliente`  AND (`despacho`.`fchDespacho` = curdate() or `despacho`. `concluido` = 'No' )  AND  `despacho`.`fchDespacho`=  `desp`.`fchDespacho` AND  `despacho`.`correlativo`=  `desp`.`correlativo` AND ( `despacho`.`tipoServicioPago` = 'SoloPersonal' or  left(`despacho`.`cuenta`,12) = 'SoloPersonal' ) AND `despacho`.`fchDespacho`= `despTercero`.`fchDespacho` AND `despacho`.`correlativo`= `despTercero`.`correlativo`  group by  `despacho`.`fchDespacho`, `despacho`.`correlativo`ORDER BY `despacho`.`fchDespacho` DESC, `despacho`.`correlativo` ASC");
	$consulta->execute();
	return $consulta->fetchAll(); 
}

function  buscarDespachoGuia($db,$guiaPorte) {
  $consulta = $db->prepare("SELECT `despacho`.`fchDespacho`, `despacho`.`correlativo`, `despacho`.`hraInicio`, `despacho`.`hraFin`, `despacho`.`placa`, `despacho`.`valorServicio`, concat(`despacho`.`idCliente`,' - ',`cliente`.`nombre` ) as idCliente, `despacho`.idCliente as id, `nombre` , `despacho`.`cuenta`, `despacho`.`concluido`, concat(trabajador.`idTrabajador`,' - ' ,`apPaterno`,' ',`apMaterno`,', ', `nombres`) as idConductor ,count(`ocurrencia`.`fchDespacho`) as ocurrencias, `despachoguiaporte`.`guiaPorte`      FROM `trabajador`, `cliente`, `despachopersonal`, `despacho` left join `ocurrencia` ON `despacho`.`fchDespacho` = `ocurrencia`.`fchDespacho` AND `despacho`.`correlativo` = `ocurrencia`.`correlativo`, `despacho` as desp left join `despachoguiaporte` ON  `despachoguiaporte`.`fchDespacho`=  `desp`.`fchDespacho` AND  `despachoguiaporte`.`correlativo`=  `desp`.`correlativo`    WHERE `cliente`.`idRuc` = `despacho`.`idCliente` AND `despachopersonal`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachopersonal`.`correlativo` = `despacho`.`correlativo` AND `despachopersonal`.`tipoRol` = 'Conductor' AND trabajador.idTrabajador = despachopersonal.idTrabajador  AND   `despacho`.`fchDespacho`=  `desp`.`fchDespacho` AND  `despacho`.`correlativo`=  `desp`.`correlativo`  AND  `despachoguiaporte`.`guiaPorte` = :guiaPorte    group by  `despacho`.`fchDespacho`, `despacho`.`correlativo` ORDER BY `despacho`.`fchDespacho` DESC, `despacho`.`correlativo` ASC");
  $consulta->bindParam(':guiaPorte',$guiaPorte);
  $consulta->execute();
	return $consulta->fetchAll();
};

function  buscarDespachos($db,$anhio,$mes,$dia,$correlativo,$guiaPorte,$placa,$conductor,$idRuc, $cuenta, $fchFin, $liquid = null,$docLiq = null, $tipoCuenta = NULL){

  $fchAntigua = correDias("$anhio-$mes-$dia",-200);
  $whereCob = "";

  $whereGuiaPorte = ($guiaPorte == NULL)?'':' AND  `despachoguiaporte`.`guiaPorte` = :guiaPorte ' ; 
  $wherePlaca = ($placa == NULL)?'':' AND  `despacho`.`placa` = :placa ' ;

  if ($anhio == NULL){
    $whereAnhio = '' ;
    $whereCob .= " AND  year(`cobrar`.`fchDespacho`) = '".substr($fchAntigua,0,4)."' ";
  } else {
    $whereAnhio = ' AND  year(`despacho`.`fchDespacho`) = :anhio ';
    $whereCob .= " AND  year(`cobrar`.`fchDespacho`) = '$anhio' ";
  }

  if ($mes == NULL){
    $whereMes = '' ;
    $whereCob .= " AND  year(`cobrar`.`fchDespacho`) = '".substr($fchAntigua,5,2)."' ";
  } else {
    $whereMes = ' AND  month(`despacho`.`fchDespacho`) = :mes ' ;
    $whereCob .= " AND  month(`cobrar`.`fchDespacho`) = '$mes' ";
  }

  if ($dia == NULL){
    $whereDia = '' ;
    $whereCob .= " AND  day(`cobrar`.`fchDespacho`) = '".substr($fchAntigua,8,2)."' ";
  } else {
    $whereDia = ' AND  day(`despacho`.`fchDespacho`) = :dia ' ;
    $whereCob .= " AND  day(`cobrar`.`fchDespacho`) = '$dia' ";
  }

  if ($anhio == NULL && $mes == NULL && $dia == NULL) $whereCob =  " AND  `cobrar`.`fchDespacho` >= '$fchAntigua' ";


  $whereCorrelativo = ($correlativo == NULL)?'':' AND  `despacho`.`correlativo`= :correlativo ' ;
  $whereCuenta = ($cuenta == NULL)?'':' AND  `despacho`.`cuenta` Like :cuenta ' ;
  $whereCliente = ($idRuc == NULL)?'':' AND  `cliente`.`idRuc` Like :idRuc ' ;
  //$whereConductor = ($conductor == NULL)?'':' AND  `trabajador`.`idTrabajador` Like :conductor ' ;

  $having = ($conductor == NULL)?"":"  HAVING despachopersonal.idTrabajador = '$conductor'  ";

  $whereTipoCuenta = ($tipoCuenta == NULL)?'':' AND  `despacho`.`tipoServicioPago` Like :tipoCuenta ' ;
  if($correlativo == NULL AND $fchFin != NULL ){
     $whereAnhio = " AND `despacho`.`fchDespacho` >=  '$anhio-$mes-$dia' AND `despacho`.`fchDespacho`  <=  '$fchFin'  ";
     //echo "condicion $whereAnhio";     
     $whereMes = '';
     $whereDia = '';

     $whereCob =  " AND `cobrar`.`fchDespacho` >=  '$anhio-$mes-$dia' AND `cobrar`.`fchDespacho`  <=  '$fchFin' ";

  };
  $whereLiquid = ($liquid == NULL)?'':' AND  `despachovehiculotercero`.`docPagoTercero` = :liquid ';
  $whereDocLiq = ($docLiq == NULL)?'':' AND  `docpagotercero`.`nroDocLiq` = :docLiq ';

  $whereCuentaCli = "";

  if (isset($_SESSION['cliente']) && $_SESSION['cliente'] != ''){
    $cadena = "";
    $arrayCuentas = $_SESSION['cuentas'];
    foreach ($arrayCuentas as $valor) {
      if ($cadena == ''){
        $cadena .= "'".$valor."'";
      } else
        $cadena .= ",'".$valor."'";
    }
    if ($cadena != ''){
      $whereCuentaCli = " AND despacho.cuenta IN (".$cadena.")";
      $_SESSION['cuentaJs'] = $cadena;
    }
  }

  $consulta = $db->prepare("SELECT `despacho`.`fchDespacho`, `despacho`.`correlativo`, `despacho`.`hraInicio`, `despacho`.`hraFin`,`despacho`.`fchCreacion`, `despacho`.`placa`, `despacho`.`valorServicio`, concat(`despacho`.`idCliente`,' - ',`cliente`.`nombre` ) as idCliente, `despacho`.idCliente as id, `nombre` , `despacho`.`cuenta`, `despacho`.`concluido`, idConductor ,count(distinct `ocurrencia`.`descripcion`) as ocurrencias, `despachoguiaporte`.`guiaPorte`, `despacho`.`pagado`, `despacho`.`pagoEnPlanilla`, `despacho`.`ptoOrigen`, `despacho`.`idSedeOrigen`, `despacho`.`ptoDestino`, `despachovehiculotercero`.`docPagoTercero`, `despachovehiculotercero`.`docTercero`, `docpagotercero`.`nroDocLiq`, `docpagotercero`.`estado`, `despacho`.`observacion`, count(distinct `despPer`.idTrabajador) AS contAux, `despacho`.observCliente , sum(if(`despachopersonal`.pagado = 'Md',1,0)) AS marcado ,  `despacho`.usuarioGrabaFin, `despacho`.usuario, `cobrarDespachos`.codigo AS despCodigo, `cobrarDespachos`.docCobranza AS despDocCob, `cobrarDespachos`.tipoDoc AS despTipoDoc, `cobrarDespachos`.estado AS despEstado, `cobrarAuxAdic`.codigo AS auxAdicCodigo, `cobrarAuxAdic`.docCobranza AS auxAdicDocCob, `cobrarAuxAdic`.tipoDoc AS auxAdicTipoDoc, `cobrarAuxAdic`.estado AS auxAdicEstado, `cobrarHrasAdic`.codigo AS hrasAdicCodigo, `cobrarHrasAdic`.docCobranza AS hrasAdicDocCob, `cobrarHrasAdic`.tipoDoc AS hrasAdicTipoDoc, `cobrarHrasAdic`.estado AS hrasAdicEstado  ,  `clientecuentaproducto`.nombProducto  , `despacho`.tipoServicioPago, `despachopersonal`.idTrabajador, `despacho`.m3
    FROM  `cliente`, `despacho` left join `ocurrencia` ON `despacho`.`fchDespacho` = `ocurrencia`.`fchDespacho` AND `despacho`.`correlativo`= `ocurrencia`.`correlativo` left join `despachoguiaporte` ON `despachoguiaporte`.`fchDespacho`= `despacho`.`fchDespacho` AND `despachoguiaporte`.`correlativo`= `despacho`.`correlativo` left join `despachovehiculotercero` ON `despachovehiculotercero`.`fchDespacho`= `despacho`.`fchDespacho` AND `despachovehiculotercero`.`correlativo`= `despacho`.`correlativo` 

    left join (SELECT despachopersonal.fchDespacho, despachopersonal.correlativo, concat(despachopersonal.idTrabajador, '-', apPaterno, ' ', apMaterno, ', ', nombres) AS idConductor, despachopersonal.pagado, despachopersonal.tipoRol, trabajador.idTrabajador FROM despachopersonal, trabajador WHERE despachopersonal.idTrabajador = trabajador.idTrabajador AND despachopersonal.tipoRol = 'Conductor'  AND despachopersonal.fchDespacho >= '$fchAntigua' ) As despachopersonal ON despachopersonal.fchDespacho = despacho.fchDespacho ANd despachopersonal.correlativo = despacho.correlativo

    left join (SELECT cobrar.codigo, cobrar.fchDespacho, cobrar.correlativo, cobrar.docCobranza, cobrar.tipoDoc, doccobranza.estado FROM `despachodetallesporcobrar` AS cobrar LEFT JOIN  doccobranza ON cobrar.docCobranza = doccobranza.docCobranza AND cobrar.tipoDoc = doccobranza.tipoDoc WHERE cobrar.codigo = 'Despacho' $whereCob) AS cobrarDespachos ON  `cobrarDespachos`.`fchDespacho`= `despacho`.`fchDespacho` AND `cobrarDespachos`.`correlativo`=  `despacho`.`correlativo` 

    left join (SELECT cobrar.codigo, cobrar.fchDespacho, cobrar.correlativo, cobrar.docCobranza, cobrar.tipoDoc, doccobranza.estado FROM `despachodetallesporcobrar` AS cobrar LEFT JOIN  doccobranza ON cobrar.docCobranza = doccobranza.docCobranza AND cobrar.tipoDoc = doccobranza.tipoDoc WHERE cobrar.codigo = 'AuxAdic'  $whereCob) AS cobrarAuxAdic ON  `cobrarAuxAdic`.`fchDespacho`= `despacho`.`fchDespacho` AND `cobrarAuxAdic`.`correlativo`=  `despacho`.`correlativo` 

    left join (SELECT cobrar.codigo, cobrar.fchDespacho, cobrar.correlativo, cobrar.docCobranza, cobrar.tipoDoc, doccobranza.estado FROM `despachodetallesporcobrar` AS cobrar LEFT JOIN  doccobranza ON cobrar.docCobranza = doccobranza.docCobranza AND cobrar.tipoDoc = doccobranza.tipoDoc WHERE cobrar.codigo = 'HrasAdic' $whereCob) AS cobrarHrasAdic ON  `cobrarHrasAdic`.`fchDespacho`= `despacho`.`fchDespacho` AND `cobrarHrasAdic`.`correlativo`=  `despacho`.`correlativo` 

    left join `docpagotercero` ON `despachovehiculotercero`.`docPagoTercero`= `docpagotercero`.`docPagoTercero` 

    left join (SELECT despachopersonal.fchDespacho, despachopersonal.correlativo, despachopersonal.idTrabajador, despachopersonal.pagado, despachopersonal.tipoRol FROM despachopersonal, trabajador WHERE despachopersonal.idTrabajador = trabajador.idTrabajador AND despachopersonal.tipoRol IN ('Auxiliar','AuxAdic')  AND despachopersonal.fchDespacho >= '$fchAntigua' ) As despPer ON despPer.fchDespacho = despacho.fchDespacho ANd despPer.correlativo = despacho.correlativo

       left join `clientecuentaproducto` ON  `clientecuentaproducto`.idProducto = `despacho`.idProducto 

    WHERE `cliente`.`idRuc` = `despacho`.`idCliente`  $whereCuentaCli $whereAnhio $whereMes $whereDia $wherePlaca $whereGuiaPorte $whereCorrelativo $whereCuenta $whereCliente $whereLiquid $whereDocLiq $whereTipoCuenta group by `despacho`.`fchDespacho`, `despacho`.`correlativo` $having ORDER BY `despacho`.`fchDespacho` DESC, `despacho`.`correlativo` ASC  ");
  
  if ($whereGuiaPorte !='')
    $consulta->bindParam(':guiaPorte',$guiaPorte);
  if ($wherePlaca !='')
    $consulta->bindParam(':placa',$placa);  
  if ($whereAnhio !='' AND $fchFin == NULL )
    $consulta->bindParam(':anhio',$anhio);  
  if ($whereMes !='')
    $consulta->bindParam(':mes',$mes);
  if ($whereDia !='')
    $consulta->bindParam(':dia',$dia);
  if ($whereCorrelativo !='')
    $consulta->bindParam(':correlativo',$correlativo);
  if ($whereCuenta !='')
    $consulta->bindParam(':cuenta',$cuenta);  
  if ($whereCliente !='')
    $consulta->bindParam(':idRuc',$idRuc);          
  //if ($whereConductor !='')
    //$consulta->bindParam(':conductor',$conductor);
  if ($whereLiquid !='')
    $consulta->bindParam(':liquid',$liquid);
  if ($whereDocLiq !='')
    $consulta->bindParam(':docLiq',$docLiq);
  if ($whereTipoCuenta !='')
    $consulta->bindParam(':tipoCuenta',$tipoCuenta);
              
            
  $consulta->execute();
	return $consulta->fetchAll();
};

function  buscarDespachosSoloPersonal($db,$anhio,$mes,$dia,$correlativo,$idRuc,$cuenta,$fchFin, $liquid = null,$docLiq = null){

  $whereAnhio = ($anhio == NULL)?'':' AND  year(`despacho`.`fchDespacho`) = :anhio ' ;
  $whereMes = ($mes == NULL)?'':' AND  month(`despacho`.`fchDespacho`) = :mes ' ;
  $whereDia = ($dia == NULL)?'':' AND  day(`despacho`.`fchDespacho`) = :dia ' ;
  $whereCorrelativo = ($correlativo == NULL)?'':' AND  `despacho`.`correlativo`= :correlativo ' ;
  $whereCliente = ($idRuc == NULL)?'':' AND  `cliente`.`idRuc` Like :idRuc ' ;
  $whereCuenta = ($cuenta == NULL)?'':' AND  `despacho`.`cuenta` Like :cuenta ' ;
  if($correlativo == NULL AND $fchFin != NULL ){
     $whereAnhio = " AND `despacho`.`fchDespacho` >=  '$anhio-$mes-$dia' AND `despacho`.`fchDespacho`  <=  '$fchFin'  ";
     $whereMes = '';
     $whereDia = '';
  }; 
  $whereLiquid = ($liquid == NULL)?'':' AND  `despachovehiculotercero`.`docPagoTercero` = :liquid ';
  $whereDocLiq = ($docLiq == NULL)?'':' AND  `docpagotercero`.`nroDocLiq` = :docLiq ';
  $whereCuentaCli = "";
  if (isset($_SESSION['cliente']) && $_SESSION['cliente'] != ''){
    $cadena = "";
    $arrayCuentas = $_SESSION['cuentas'];
    foreach ($arrayCuentas as $valor) {
      if ($cadena == ''){
        $cadena .= "'".$valor."'";
      } else
        $cadena .= ",'".$valor."'";
    }
    if ($cadena != ''){
      $whereCuentaCli = " AND despacho.cuenta IN (".$cadena.")";
      $_SESSION['cuentaJs'] = $cadena;
    }
  }

    $consulta = $db->prepare("SELECT `despacho`.`fchDespacho`, `despacho`.`correlativo`, `despacho`.`hraInicio`, `despacho`.`hraFin`, `despacho`.`fchCreacion`, `despacho`.`valorServicio`, concat(`despacho`.`idCliente`,' - ',`cliente`.`nombre` ) as idCliente, `despacho`.idCliente as id, `nombre` , `despacho`.`cuenta`, `despacho`.`concluido`,count(distinct `ocurrencia`.`descripcion`) as ocurrencias, `despacho`.`pagado`, `despachoguiaporte`.`guiaPorte`, `despacho`.`pagoEnPlanilla`, `despacho`.`ptoOrigen`, `despacho`.`ptoDestino`,  `despachovehiculotercero`.`docPagoTercero` , `despachovehiculotercero`.`docTercero`, `docpagotercero`.`nroDocLiq`, `docpagotercero`.`estado`,  count(distinct despPer.idTrabajador) AS contAux,  `despacho`.observCliente, sum(if(despachopersonal.pagado = 'Md',1,0)) AS marcado, `despacho`.usuarioGrabaFin, `despacho`.usuario FROM `cliente`, `despachopersonal`, `despacho`  left join `ocurrencia` ON `despacho`.`fchDespacho` = `ocurrencia`.`fchDespacho` AND `despacho`.`correlativo` = `ocurrencia`.`correlativo`,  `despacho` as desp left join `despachoguiaporte` ON  `despachoguiaporte`.`fchDespacho`=  `desp`.`fchDespacho` AND  `despachoguiaporte`.`correlativo`=  `desp`.`correlativo` , `despacho` as despTercero left join `despachovehiculotercero` ON  `despachovehiculotercero`.`fchDespacho`=  `despTercero`.`fchDespacho` AND `despachovehiculotercero`.`correlativo`=  `despTercero`.`correlativo`  left join docpagotercero ON `despachovehiculotercero`.`docPagoTercero`= `docpagotercero`.`docPagoTercero` , `despacho` as despTrabaj left join despachopersonal AS despPer ON `despTrabaj`.`fchDespacho` = `despPer`.`fchDespacho` AND `despTrabaj`.`correlativo` = `despPer`.`correlativo` AND `despPer`.tipoRol IN ('Auxiliar','AuxAdic') WHERE `despachopersonal`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachopersonal`.`correlativo` = `despacho`.`correlativo` AND despacho.fchDespacho =  desp.fchDespacho AND despacho.correlativo =  desp.correlativo  AND `cliente`.`idRuc` = `despacho`.`idCliente` AND (`despacho`.`tipoServicioPago` = 'SoloPersonal' or  left(`despacho`.`cuenta`,12) = 'SoloPersonal'  ) AND `despacho`.`fchDespacho`= `despTercero`.`fchDespacho` AND `despacho`.`correlativo`= `despTercero`.`correlativo` AND `despacho`.`fchDespacho`= `despTrabaj`.`fchDespacho` AND `despacho`.`correlativo`= `despTrabaj`.`correlativo` $whereCuentaCli $whereAnhio $whereMes $whereDia  $whereCorrelativo $whereCliente $whereCuenta  $whereLiquid $whereDocLiq  group by  `despacho`.`fchDespacho`, `despacho`.`correlativo` ORDER BY `despacho`.`fchDespacho` DESC, `despacho`.`correlativo` ASC");
  
  if ($whereAnhio !='' AND $fchFin == NULL )
    $consulta->bindParam(':anhio',$anhio);  
  if ($whereMes !='')
    $consulta->bindParam(':mes',$mes);
  if ($whereDia !='')
    $consulta->bindParam(':dia',$dia);
  if ($whereCorrelativo !='')
    $consulta->bindParam(':correlativo',$correlativo);
  if ($whereCliente !='')
    $consulta->bindParam(':idRuc',$idRuc);
  if ($whereCuenta !='')
    $consulta->bindParam(':cuenta',$cuenta);                     
  if ($whereLiquid !='')
    $consulta->bindParam(':liquid',$liquid);
  if ($whereDocLiq !='')
    $consulta->bindParam(':docLiq',$docLiq);
  $consulta->execute();
	return $consulta->fetchAll();
};

function buscarDespachoDetallesTipoRol($db,$fchDespacho,$correlativo,$tipoRol){
  //$consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `idTrabajador`, `valorRol`, `valorAdicional`, `tipoRol` FROM `despachopersonal` WHERE  `fchDespacho`= :fchDespacho AND `correlativo` = :correlativo AND `tipoRol` = :tipoRol");
  $consulta = $db->prepare("SELECT `idTrabajador`, `pagado`  FROM `despachopersonal` WHERE  `fchDespacho`= :fchDespacho AND `correlativo` = :correlativo AND `tipoRol` = :tipoRol");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
	$consulta->bindParam(':correlativo',$correlativo);
	$consulta->bindParam(':tipoRol',$tipoRol);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarDespacho($db,$fchDespacho,$correlativo){
  $consulta = $db->prepare("SELECT `despacho`.`fchDespacho`, `despacho`.`correlativo`, `despacho`.`movilAsignado`, `despacho`.`usuarioAsignado`, `despacho`.`observacion`, `despacho`.idProducto, `guiaCliente`, `hraInicio`, `hraFin`, `despacho`.`placa`, `despacho`.`m3`, `valorServicio`, `igvServicio`, `idCliente`, `cuenta`, `cliente`.rznSocial,  `despacho`.`zonaDespacho`, `tipoServicioPago`, `nroGuias`, `nroDespachos`, `concluido`, `kmInicio`, `kmInicioCliente`, `kmFinCliente`, `hraFinCliente`, `lugarFinCliente`, `kmFin`, `docCobranza`, `despacho`.`pagado`, `despachopersonal`.`idTrabajador`, `despachopersonal`.`valorRol`, Concat(`trabajador`.`apPaterno`,' ', `trabajador`.`apMaterno`,' ', `trabajador`.`nombres`) as nombreCompleto, `despacho`.`zonaDespacho`, `ptoOrigen`, `tipoDestino`, `ptoDestino`, `despachovehiculotercero`.guiaTrTercero, `despachovehiculotercero`.docPagoTercero, `nroAuxiliares`, `nroAuxiliaresAdic`, `costo`, `topeServicioHraNormal`, `costoHraExtra`, `toleranCobroHraExtra`, `valorConductor`, `valorAuxiliar`, `nroAuxiliaresCuenta`, `valorAuxAdicional`, `usarMaster`, `tpoExtraHras`, `despacho`.usuario , `despacho`.usuarioGrabaFin, `despacho`.modoCreacion  FROM `cliente`,`despachopersonal`, `trabajador`, `despacho` LEFT JOIN despachovehiculotercero ON `despacho`.`fchDespacho` =  `despachovehiculotercero`.`fchDespacho` AND `despacho`.`correlativo` =  `despachovehiculotercero`.`correlativo` WHERE  `cliente`.idRuc = `despacho`.idCliente AND `despacho`.`fchDespacho` = `despachopersonal`.`fchDespacho` AND `despacho`.`correlativo` = `despachopersonal`.`correlativo` AND `despachopersonal`.`tipoRol` = 'conductor' AND `despachopersonal`.`idTrabajador` = `trabajador`.`idTrabajador` AND  `despacho`.fchDespacho = :fchDespacho AND `despacho`.correlativo = :correlativo ");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
	$consulta->bindParam(':correlativo',$correlativo);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarInfoDespacho($db,$fchDespacho,$correlativo){
  $consulta = $db->prepare("SELECT `despacho`.`fchDespacho`, `despacho`.`correlativo`, `despacho`.`observacion`, `guiaCliente`, `hraInicio`, `hraInicioBase`,  `despacho`.`fchDespachoFin`, `fchDespachoFinCli`,`hraFin`, `despacho`.`placa`, `valorServicio`, `igvServicio`, `idCliente`, `cuenta`, `correlCuenta`, `tipoServicioPago`, `nroGuias`, `nroDespachos`, `usaReten`, `concluido`, `kmInicio`, `kmInicioCliente`, `kmFinCliente`, `hraFinCliente`, `lugarFinCliente`, `kmFin`, despacho.`docCobranza`, `despacho`.`pagado`, `despacho`.`zonaDespacho`, `ptoOrigen`, `idSedeOrigen`, `tipoDestino`, `ptoDestino`, `despachovehiculotercero`.guiaTrTercero, `despachovehiculotercero`.docPagoTercero,  `idCliente`, `cuenta`, `nroAuxiliares`, `nroAuxiliaresAdic`, `costo`, `topeServicioHraNormal`, `costoHraExtra`, `toleranCobroHraExtra`, `valorConductor`, `valorAuxiliar`, `nroAuxiliaresCuenta`, `valorAuxAdicional`, `usarMaster`, `tpoExtraHras`, `cliente`.nombre, `recorridoEsperado`, `dporcobrar`.tipoDoc AS tipoDocCobrar,  `dporcobrar`.docCobranza AS docCobranzaCobrar ,`usuarioGrabaFin`, `despacho`.terceroConPersonalMoy, `despacho`.idProducto, `despacho`.`modoCreacion`, `despacho`.hraNormCondDespacho , `despacho`.tolerHraCondDespacho, `despacho`.valHraAdicCondDespacho, `despacho`.hraNormAuxDespacho, `despacho`.tolerHraAuxDespacho, `despacho`.valHraAdicAuxDespacho FROM `cliente`, `despacho` LEFT JOIN despachovehiculotercero ON `despacho`.`fchDespacho` =  `despachovehiculotercero`.`fchDespacho` AND `despacho`.`correlativo` =  `despachovehiculotercero`.`correlativo` LEFT JOIN (SELECT tipoDoc, docCobranza, fchDespacho, correlativo FROM despachodetallesporcobrar WHERE tipoDoc != '' AND fchDespacho = :fchDespacho AND correlativo = :correlativo GROUP BY  fchDespacho, correlativo ) AS dporcobrar ON `dporcobrar`.fchDespacho = `despacho`.fchDespacho AND `dporcobrar`.correlativo = `despacho`.correlativo WHERE `cliente`.idRuc = `despacho`.idCliente AND `despacho`.fchDespacho = :fchDespacho AND `despacho`.correlativo = :correlativo");

  $consulta->bindParam(':fchDespacho',$fchDespacho);
  $consulta->bindParam(':correlativo',$correlativo);
  $consulta->execute();
  return $consulta->fetchAll();
}


function buscarPlacaUltimoKilometraje($db,$fchDespacho,$placa){
  $consulta = $db->prepare("SELECT `kmInicio`, `kmInicioCliente`, `kmFinCliente`,`hraFinCliente`, `lugarFinCliente`, `kmFin`, `fchDespacho`, `correlativo`  FROM `despacho` WHERE `fchDespacho` <= :fchDespacho AND `placa` LIKE :placa AND (`kmFin` IS NOT NULL OR `kmFinCliente` IS NOT NULL) ORDER BY `despacho`.`fchDespacho`  DESC Limit 1 ");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
	$consulta->bindParam(':placa',$placa);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarDespachoSoloPersonal($db,$fchDespacho,$correlativo){
  $consulta = $db->prepare("SELECT `despacho`.`fchDespacho`, `despacho`.`correlativo`, `despacho`.`observacion`,  `guiaCliente`, `hraInicio`, `hraFin`, `placa`, `valorServicio`, `igvServicio`, `idCliente`, `cuenta`,`tipoServicioPago`, `concluido`, `kmInicio`, `kmFin`, `docCobranza`, `despacho`.`pagado`, '' as `idTrabajador`, ''  as nombreCompleto, '' AS guiaTrTercero, NULL AS `docPagoTercero` FROM `despacho` WHERE `despacho`.fchDespacho = :fchDespacho AND `despacho`.correlativo = :correlativo ");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
	$consulta->bindParam(':correlativo',$correlativo);
	$consulta->execute();
	return $consulta->fetchAll();  
}


function buscarPeaje($db,$fchDespacho,$correlativo){
  $consulta = $db->prepare("SELECT `montoTotal`, `idTrabajador`, `fchPago`  FROM `ocurrenciaconsulta` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `tipo` LIKE 'peaje'");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
	$consulta->bindParam(':correlativo',$correlativo);
	$consulta->execute();
	return $consulta->fetchAll(); 
}

function buscarCostoDespachoPersonal($db,$fchDespacho,$correlativo,$tipoRol){
  $consulta = $db->prepare("SELECT `valorRol` FROM `despachopersonal`  WHERE `despachopersonal`.fchDespacho = :fchDespacho AND `despachopersonal`.correlativo = :correlativo AND `tipoRol` = :tipoRol order by `valorRol` Limit 1");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
	$consulta->bindParam(':correlativo',$correlativo);
	$consulta->bindParam(':tipoRol',$tipoRol);
	$consulta->execute();
	return $consulta->fetchAll();  
}


function buscarGuiasPorte($db,$fchDespacho,$correlativo){
  $consulta = $db->prepare("SELECT guiaPorte  FROM `despachoguiaporte` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
	$consulta->bindParam(':correlativo',$correlativo);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarDespachoRipley($db,$fchDespacho,$correlativo){
  $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `guias`, `despachos`, `zona`, `tipoServicioPago`, `costo`, `costoHraExtra`, `tpoExtraHras`  FROM `despachoripley` WHERE `despachoripley`.`fchDespacho` = :fchDespacho AND `despachoripley`.`correlativo` = :correlativo");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
	$consulta->bindParam(':correlativo',$correlativo);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarTodosInvolucrados($db,$fchDespacho,$correlativo){
  $consulta = $db->prepare("SELECT `despachopersonal`.idTrabajador, `despachopersonal`.tipoRol, concat(`trabajador`.nombres, ' ' , `trabajador`.apPaterno,' ', `trabajador`.apMaterno) as nombreCompleto, `trabajador`.categTrabajador , `despachopersonal`.valorRol, `despachopersonal`.valorAdicional, `despachopersonal`.fchDespacho, `despachopersonal`.trabHraInicio, `despachopersonal`.trabFchDespachoFin, `despachopersonal`.trabHraFin, `despachopersonal`.esReten FROM `despachopersonal`, `trabajador`  WHERE `despachopersonal`.idTrabajador = `trabajador`.idTrabajador  AND  fchDespacho = :fchDespacho AND correlativo = :correlativo ");
	$consulta->bindParam(':fchDespacho',$fchDespacho);
	$consulta->bindParam(':correlativo',$correlativo);
	$consulta->execute();
	return $consulta->fetchAll();
}

function buscarDeudasOcurrencias($db,$inicioQ1,$finQ1,$inicioQ2,$finQ2,$inicioQ3,$finQ3,$inicioQ4,$finQ4,$fchAtras,$idTrabajador){
  $consulta = $db->prepare("SELECT sum(if(`fchPago`>= '$fchAtras'  AND `fchPago`<= '$finQ1',`montoCuota`,0)) AS `Q1`,  sum(if(`fchPago`>= '$inicioQ2'  AND `fchPago`<= '$finQ2',`montoCuota`,0)) AS `Q2`, sum(if(`fchPago`>= '$inicioQ3'  AND `fchPago`<= '$finQ3',`montoCuota`,0)) AS `Q3`, sum(if(`fchPago`>= '$inicioQ4'  AND `fchPago`<= '$finQ4',`montoCuota`,0)) AS `Q4`  FROM `ocurrenciaconsulta` WHERE `tipoOcurrencia` LIKE 'Descuento' AND `pagado` = 'No' AND `idTrabajador` = '$idTrabajador' AND ( `fchPago`>= '$fchAtras' AND  `fchPago`<= '$finQ4')");
  $consulta->execute();
	return $consulta->fetchAll();
}

function buscarDeudasOtras($db,$inicioQ1,$finQ1,$inicioQ2,$finQ2,$inicioQ3,$finQ3,$inicioQ4,$finQ4,$fchAtras,$idTrabajador){
  $consulta = $db->prepare("SELECT sum(if(`fchPago`>= '$fchAtras'  AND  `fchPago`<= '$finQ1',`montoCuota`,0)) AS Q1, sum(if(`fchPago`>= '$inicioQ2'  AND `fchPago`<= '$finQ2',`montoCuota`,0)) AS Q2, sum(if(`fchPago`>= '$inicioQ3'  AND `fchPago`<= '$finQ3',`montoCuota`,0)) AS Q3, sum(if(`fchPago`>= '$inicioQ4'  AND `fchPago`<= '$finQ4',`montoCuota`,0)) AS Q4  FROM `prestamodetalle` WHERE   `pagado`= 'No' AND `tipoItem` IN ('DsctoTrabaj','DsctoUnif','Prestamo') AND `idTrabajador` LIKE '$idTrabajador' AND (`fchPago`>= '$fchAtras'  AND `fchPago`<= '$finQ4')");
  $consulta->execute();
	return $consulta->fetchAll();
};


function buscarCoordinador($db,$idCliente){
  $consulta = $db->prepare("SELECT `idCoordinador` FROM `cliente` WHERE `idRuc` = :idCliente");
  $consulta->bindParam(':idCliente',$idCliente);
  $consulta->execute();
	return $consulta->fetchAll();
}

function cobrosDespachosPendientes($db,$idCliente,$fchIni,$fchFin,$tipoReg,$regTipoDoc,$estadoDoc,$nroDoc,$placa,$cuenta,$monto){
  $where = "";
  $whereAux = "  AND `despachodetallesporcobrar`.pagado = 'No'  " ;
  if ($fchIni != NULL )
    if($fchFin == NULL)
      $where .= " AND `despacho`.`fchDespacho`= :fchIni ";
    else
      $where .= " AND (`despacho`.`fchDespacho`>= :fchIni AND `despacho`.`fchDespacho`<= :fchFin)";
  if ($tipoReg != NULL)
    $where .=  " AND `despachodetallesporcobrar`.`codigo` = :tipoReg ";
  if ($regTipoDoc != NULL){
    $where .=  " AND `despachodetallesporcobrar`.`tipoDoc` = :tipoDoc ";
    $whereAux = "  AND `despachodetallesporcobrar`.pagado = 'Em'  " ;
  }
    
  if ($estadoDoc != NULL)
    $where .=  " AND `estado` = :estadoDoc ";
  if ($nroDoc != NULL){
    $where .=  " AND  `despachodetallesporcobrar`.`docCobranza` = :nroDoc ";
    $whereAux = "  AND `despachodetallesporcobrar`.pagado = 'Em'  " ;
  }
  if ($placa != NULL)
    $where .=  " AND  `despacho`.`placa` = :placa ";
  if ($cuenta != NULL)
    $where .=  " AND  `despacho`.`cuenta` = :cuenta ";
  if ($monto != NULL)
    $where .=  " AND  `despachodetallesporcobrar`.`costoUnit` = :monto ";


  //$consulta = $db->prepare("SELECT `despacho`.`fchDespacho`, `despacho`.`correlativo`, `despacho`.`placa`, `despacho`.`cuenta`, `despachodetallesporcobrar`.`codigo`, `despachodetallesporcobrar`.`costoUnit`, `despachodetallesporcobrar`.`cantidad`, `despacho`.`hraInicio`, `despacho`.`hraFin`,`despachodetallesporcobrar`.`docCobranza`, `despachodetallesporcobrar`.`tipoDoc`, `doccobranza`.`estado`, `despacho`.`nroDespachos`, `despacho`.`observacion`, kmInicio, kmInicioCliente, kmFin, kmFinCliente, m3, tipoServicioPago, hraFinCliente, m3Facturable, ptoOrigen ,tipoDestino, ptoDestino, idSedeOrigen FROM `despacho` LEFT JOIN  vehiculo  ON   despacho.placa = vehiculo.idPlaca  , `despachodetallesporcobrar` LEFT JOIN `doccobranza` ON `despachodetallesporcobrar`.`docCobranza` = `doccobranza`.`docCobranza` AND `despachodetallesporcobrar`.`tipoDoc` = `doccobranza`.`tipoDoc` WHERE   `despachodetallesporcobrar`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachodetallesporcobrar`.`correlativo`  = `despacho`.`correlativo` AND `idCliente` = :idCliente $whereAux $where ORDER BY  `despacho`.`fchDespacho` DESC Limit 100 ");


  $consulta = $db->prepare("SELECT `despacho`.`fchDespacho`, `despacho`.`correlativo`, `despacho`.`placa`, `despacho`.`cuenta`, `despachodetallesporcobrar`.`codigo`, `despachodetallesporcobrar`.`costoUnit`, `despachodetallesporcobrar`.`cantidad`, `despacho`.`hraInicio`, `despacho`.`hraFin`,`despachodetallesporcobrar`.`docCobranza`, `despachodetallesporcobrar`.`tipoDoc`, `doccobranza`.`estado`, `despacho`.`nroDespachos`, `despacho`.`observacion`, kmInicio, kmInicioCliente, kmFin, kmFinCliente, m3, tipoServicioPago, hraFinCliente, `vehiculo`.m3Facturable, ptoOrigen ,tipoDestino, ptoDestino, idSedeOrigen, `clicuepro`.nombProducto  FROM `despacho` LEFT JOIN  vehiculo  ON `despacho`.placa = `vehiculo`.idPlaca 

    LEFT JOIN clientecuentaproducto AS clicuepro ON `clicuepro`.idProducto = `despacho`.idProducto 

   , `despachodetallesporcobrar` LEFT JOIN `doccobranza` ON `despachodetallesporcobrar`.`docCobranza` = `doccobranza`.`docCobranza` AND `despachodetallesporcobrar`.`tipoDoc` = `doccobranza`.`tipoDoc` WHERE   `despachodetallesporcobrar`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachodetallesporcobrar`.`correlativo`  = `despacho`.`correlativo` AND despacho.`idCliente` = :idCliente $whereAux $where ORDER BY  `despacho`.`fchDespacho` DESC Limit 300 ");


  $consulta->bindParam(':idCliente',$idCliente);
  if ($fchIni != NULL ) $consulta->bindParam(':fchIni',$fchIni);
  if ($fchFin != NULL ) $consulta->bindParam(':fchFin',$fchFin);
  if ($tipoReg != NULL ) $consulta->bindParam(':tipoReg',$tipoReg);
  if ($regTipoDoc != NULL ) $consulta->bindParam(':tipoDoc',$regTipoDoc);
  if ($estadoDoc != NULL ) $consulta->bindParam(':estadoDoc',$estadoDoc);
  if ($nroDoc != NULL ) $consulta->bindParam(':nroDoc',$nroDoc);
  if ($placa != NULL ) $consulta->bindParam(':placa',$placa);
  if ($cuenta != NULL ) $consulta->bindParam(':cuenta',$cuenta);
  if ($monto != NULL ) $consulta->bindParam(':monto',$monto);
  
  $consulta->execute();
	return $consulta->fetchAll(); 
}

function cobrosDespachosMarcados($db,$idCliente,$fchIni,$fchFin,$tipoReg,$placa,$cuenta,$monto){
  $where = ""; 
  if ($fchIni != NULL )
    if($fchFin == NULL)
      $where .= " AND `despacho`.`fchDespacho`= :fchIni ";
    else
      $where .= " AND (`despacho`.`fchDespacho`>= :fchIni AND `despacho`.`fchDespacho`<= :fchFin)";
  if ($tipoReg != NULL)
    $where .=  " AND `despachodetallesporcobrar`.`codigo` = :tipoReg ";
  if ($placa != NULL)
    $where .=  " AND  `despacho`.`placa` = :placa ";
  if ($cuenta != NULL)
    $where .=  " AND  `despacho`.`cuenta` = :cuenta ";
  if ($monto != NULL)
    $where .=  " AND  `despachodetallesporcobrar`.`costoUnit` = :monto ";

  $consulta = $db->prepare("SELECT `despacho`.`fchDespacho`, `despacho`.`correlativo`, `despacho`.`placa`, `despacho`.`cuenta`, `despachodetallesporcobrar`.`codigo`, `despachodetallesporcobrar`.`costoUnit`, `despachodetallesporcobrar`.`cantidad`, `despacho`.`hraInicio`, `despacho`.`hraFin`,`despachodetallesporcobrar`.`docCobranza`, `despachodetallesporcobrar`.`tipoDoc`, `doccobranza`.`estado`, `despacho`.`nroDespachos`, `despacho`.`observacion`, kmInicio, kmInicioCliente, kmFin, kmFinCliente, m3, tipoServicioPago, hraFinCliente, m3Facturable, ptoOrigen ,tipoDestino, ptoDestino , idSedeOrigen FROM `despacho` LEFT JOIN  vehiculo ON   despacho.placa = vehiculo.idPlaca , `despachodetallesporcobrar` LEFT JOIN `doccobranza` ON `despachodetallesporcobrar`.`docCobranza` = `doccobranza`.`docCobranza` AND `despachodetallesporcobrar`.`tipoDoc` = `doccobranza`.`tipoDoc` WHERE   `despachodetallesporcobrar`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachodetallesporcobrar`.`correlativo`  = `despacho`.`correlativo` AND `idCliente` = :idCliente  AND `despachodetallesporcobrar`.pagado = 'Md' $where ORDER BY `despacho`.`fchDespacho` DESC Limit 150 ");

  $consulta->bindParam(':idCliente',$idCliente);
  if ($fchIni != NULL ) $consulta->bindParam(':fchIni',$fchIni);
  if ($fchFin != NULL ) $consulta->bindParam(':fchFin',$fchFin);
  if ($tipoReg != NULL ) $consulta->bindParam(':tipoReg',$tipoReg);
  //if ($regTipoDoc != NULL ) $consulta->bindParam(':tipoDoc',$regTipoDoc);
  if ($placa != NULL ) $consulta->bindParam(':placa',$placa);
  if ($cuenta != NULL ) $consulta->bindParam(':cuenta',$cuenta);
  if ($monto != NULL ) $consulta->bindParam(':monto',$monto);
  
  $consulta->execute();
  return $consulta->fetchAll(); 
}


function buscarZonasRipley($db){
  $consulta = $db->prepare("SELECT `zona` FROM `zonaripley`");
  $consulta->execute();
	return $consulta->fetchAll();
};

function buscarZonasDespacho($db){
  $consulta = $db->prepare("SELECT `zona` FROM `zonadespacho`");
  $consulta->execute();
  return $consulta->fetchAll();
}; 

function cobrosDocumentosCobranza($db,$idCliente,$estado,$docCobranza = NULL, $tipoDoc = NULL, $importe = NULL, $nroPreliquid = NULL){
  $whereEstado = " AND `doccobranza`.estado = '$estado' ";
  $where = "";
  if($estado == 'Todos')  $whereEstado = "";
  if ($docCobranza != NULL) $where .= " AND `doccobranza`.`docCobranza` LIKE :docCobranza ";
  if ($tipoDoc != NULL) $where .= " AND  `doccobranza`.`tipoDoc` = :tipoDoc ";
  if ($nroPreliquid != NULL) $where .= " AND  `doccobranza`.`preliquidacion` LIKE :nroPreliquid ";


  if ($importe != NULL) $having = "HAVING `montoTot` = '$importe' "; else $having = "";

  //echo "idCliente: $idCliente, docCobranza: $docCobranza, tipoDoc: $tipoDoc, nroPreliquid: $nroPreliquid, where: $where, whereEstado: $whereEstado  ";
  $consulta = $db->prepare("SELECT `doccobranza`.`docCobranza`, `doccobranza`.`tipoDoc`,`doccobranza`.`fchCreacion`,  count(`despachodetallesporcobrar`.`docCobranza`) as cantidad , sum(costoUnit * cantidad) AS montoTot , doccobranza.`estado`,  docnotas.`docCobranza` as docNota, docnotas.`tipoDoc` as tipoNota , docnotas.`monto` as montoNota, fchEmitida, fchPresentada, fchCancelada, `doccobranza`.preliquidacion, `doccobranza`.fchPreliquid, fchDetraccion, `doccobranza`.detraccion, `doccobranza`.tipoDetraccion, `doccobranza`.constanciaDetraccion, `doccobranza`.nroOperacion FROM  `despachodetallesporcobrar`, `doccobranza` LEFT JOIN docnotas ON `doccobranza`.`docCobranza` = docnotas.docRelacionado AND `doccobranza`.`tipoDoc` = 'Factura' , `despacho`  WHERE `doccobranza`.`docCobranza` = `despachodetallesporcobrar`.`docCobranza` AND `doccobranza`.`tipoDoc` = `despachodetallesporcobrar`.`tipoDoc` AND `despacho`.`fchDespacho` =  `despachodetallesporcobrar`.`fchDespacho` AND `despacho`.`correlativo` =  `despachodetallesporcobrar`.`correlativo` $whereEstado $where AND `despacho`.`idCliente` = :idCliente GROUP BY `doccobranza`.`docCobranza`, `doccobranza`.`tipoDoc` $having ORDER BY `doccobranza`.`fchCreacion` DESC");
  $consulta->bindParam(':idCliente',$idCliente);
  if ($docCobranza != NULL ) $consulta->bindParam(':docCobranza',$docCobranza);
  if ($tipoDoc != NULL ) $consulta->bindParam(':tipoDoc',$tipoDoc);
  if ($nroPreliquid != NULL ) $consulta->bindParam(':nroPreliquid',$nroPreliquid);
  $consulta->execute();
	return $consulta->fetchAll(); 
};

function cobroDocumentocobranza($db,$nroDoc,$tipoDoc){
  $consulta = $db->prepare("SELECT `docCobranza`, `tipoDoc`, `estado`, `observacion`, `chkObserv`, `chkPlaca`, `chkFecha`, `chkHraIni`,`chkHraFin`, `chkGT`, `chkValTotal`, `chkObsDesp`, `chkConductor`, `chkHrasTrab`, `chkM3`, `chkHraExtra`, `chkValHraExtra`, `chkValAuxAdic`, `chkValDesp`, `chkTiendas`, `chkAux01`, `chkAux02`, `chkAuxAdic`, `chkHojaRuta`, `chkKmInicio`, `chkKmFin`, `chkKmRecorr`, `chkGuiasDesp`,  `fchEmitida` FROM `doccobranza` WHERE `docCobranza` = :nroDoc AND `tipoDoc` = :tipoDoc");
  $consulta->bindParam(':nroDoc',$nroDoc);
  $consulta->bindParam(':tipoDoc',$tipoDoc);
  $consulta->execute();
	return $consulta->fetchAll();
}

function generaNroPreliquid($db,$cliente){
  $anhio = Date("Y");
  $nvoId = "";
  $consulta = $db->prepare("SELECT docCobranza FROM  `doccobranza` WHERE tipoDoc = 'Preliquidacion'  ORDER BY docCobranza DESC limit 1");
  $consulta->execute();
  $result = $consulta->fetchAll();  
  foreach($result as $fila) {
    $ultId = $fila['docCobranza'];
    $anhioId = substr($ultId, 0,4);
    if ($anhioId == $anhio){
      $nvoId = 1*$ultId + 1;
    }
  }
  if ($nvoId == "")  $nvoId = $anhio."000001";
  return $nvoId;
}


function buscarDespachosAgrupados($db,$nroDoc,$tipoDoc){
  $consulta = $db->prepare("SELECT `despacho`.`fchDespacho`, `despacho`.`correlativo`, `guiaCliente`, `hraInicio`, `hraFin`, `placa`, `despacho`.`observacion`, `despachodetallesporcobrar`.`costoUnit`, `despachodetallesporcobrar`.`codigo`,  `despachodetallesporcobrar`.`cantidad`, `igvServicio`, `idCliente`, `cuenta`, `concluido` FROM `despacho` ,`despachodetallesporcobrar`  WHERE `despacho`.`fchDespacho` = `despachodetallesporcobrar`.`fchDespacho` AND `despacho`.`correlativo` = `despachodetallesporcobrar`.`correlativo` AND `despachodetallesporcobrar`.`docCobranza` = :nroDoc AND `despachodetallesporcobrar`.`tipoDoc` = :tipoDoc");
  $consulta->bindParam(':nroDoc',$nroDoc);
  $consulta->bindParam(':tipoDoc',$tipoDoc);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarDespachosDocum($db,$nroDoc,$tipoDoc,$placa = 'No'){
  if ($placa == 'Si'){
    $groupPlaca = ", placa";
    $wherePlaca = " AND `despachodetallesporcobrar`.`codigo` = 'Despacho' ";
  } else {
    $groupPlaca = "";
    $wherePlaca = " AND `despachodetallesporcobrar`.`codigo` != 'Despacho' ";
  } 
  $consulta = $db->prepare("SELECT count(*) AS cant, codigo, min(`despacho`.`fchDespacho`) AS minimo, max(`despacho`.`fchDespacho`) AS maximo, `despachodetallesporcobrar`.`codigo`, sum(`despachodetallesporcobrar`.`costoUnit`) AS total, `despachodetallesporcobrar`.`cantidad`, placa, `igvServicio`, `idCliente`, `cuenta`, `concluido` FROM `despacho` ,`despachodetallesporcobrar`  WHERE `despacho`.`fchDespacho` =  `despachodetallesporcobrar`.`fchDespacho` AND `despacho`.`correlativo` =  `despachodetallesporcobrar`.`correlativo` AND `despachodetallesporcobrar`.`docCobranza` = :nroDoc AND `despachodetallesporcobrar`.`tipoDoc` = :tipoDoc $wherePlaca GROUP BY codigo $groupPlaca");
  $consulta->bindParam(':nroDoc',$nroDoc);
  $consulta->bindParam(':tipoDoc',$tipoDoc);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarDespachoBoleta($db,$nroDoc,$tipoDoc){
  $consulta = $db->prepare("SELECT count(*) AS cant, codigo, min(`despacho`.`fchDespacho`) AS minimo, max(`despacho`.`fchDespacho`) AS maximo, `despachodetallesporcobrar`.`codigo`, sum(`despachodetallesporcobrar`.`costoUnit`) AS total, `despachodetallesporcobrar`.`cantidad`, placa, `igvServicio`, `idCliente`, `cuenta`, `concluido` FROM `despacho` ,`despachodetallesporcobrar`  WHERE `despacho`.`fchDespacho` =  `despachodetallesporcobrar`.`fchDespacho` AND `despacho`.`correlativo` =  `despachodetallesporcobrar`.`correlativo` AND `despachodetallesporcobrar`.`docCobranza` = :nroDoc AND `despachodetallesporcobrar`.`tipoDoc` = :tipoDoc  GROUP BY codigo ");
  $consulta->bindParam(':nroDoc',$nroDoc);
  $consulta->bindParam(':tipoDoc',$tipoDoc);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarClienteBoleta($db,$nroDoc,$tipoDoc){
  $consulta = $db->prepare("SELECT `nombCli`, `direcCli` FROM `doccobranzaadicional` WHERE `docCobranza` = :nroDoc AND `tipoDoc` = :tipoDoc ");
  $consulta->bindParam(':nroDoc',$nroDoc);
  $consulta->bindParam(':tipoDoc',$tipoDoc);
  $consulta->execute();
  return $consulta->fetchAll();
}
function buscarDespachosDetalle($db,$nroDoc,$tipoDoc){
  $consulta = $db->prepare("SELECT  despacho.fchDespacho, despacho.correlativo, codigo, placa, `costoUnit`, `despachodetallesporcobrar`.`cantidad`,  `igvServicio`, `idCliente`, `cuenta`, `concluido` , despachopersonal.idTrabajador , concat(trabajador.nombres,' ', trabajador.apPaterno,' ', trabajador.apMaterno) AS nombCompleto, despacho.hraInicio, despacho.hraFin, SUBTIME(despacho.hraFin, despacho.hraInicio) AS trabajadas, dimInteriorLargo * dimInteriorAncho* dimInteriorAlto AS dimension FROM `despacho` ,`despachopersonal`,trabajador, `despachodetallesporcobrar`, vehiculo WHERE  `despacho`.fchDespacho = `despachopersonal`.fchDespacho AND  `despacho`.`correlativo` =  `despachopersonal`.`correlativo` AND despachopersonal.tipoRol = 'conductor' AND despachoPersonal.idTrabajador = trabajador.idTrabajador AND despacho.placa = vehiculo.idPlaca  AND `despacho`.`fchDespacho` =  `despachodetallesporcobrar`.`fchDespacho` AND `despacho`.`correlativo` =  `despachodetallesporcobrar`.`correlativo` AND `despachodetallesporcobrar`.`docCobranza` = :nroDoc AND `despachodetallesporcobrar`.`tipoDoc` = :tipoDoc  AND `despachodetallesporcobrar`.`codigo` = 'Despacho'");
  $consulta->bindParam(':nroDoc',$nroDoc);
  $consulta->bindParam(':tipoDoc',$tipoDoc);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarNota($db,$nroDoc,$tipoDoc){
  $consulta = $db->prepare("SELECT `docCobranza`, `tipoDoc`, `docRelacionado`, `observacion`, `monto`, `estado`, `fchCreacion`  FROM `docnotas` WHERE `docCobranza` LIKE :nroDoc AND `tipoDoc` LIKE :tipoNota");
  $consulta->bindParam(':nroDoc',$nroDoc);
  $consulta->bindParam(':tipoNota',$tipoDoc);
  $consulta->execute();
  return $consulta->fetchAll();
}

/*function confirmarOperacion($db,$nroDoc,$tipoDoc){
  $consulta = $db->prepare("UPDATE `doccobranza` SET `estado` = 'Pagado', `fchConfirmacion` = NOW() WHERE `docCobranza` = :nroDoc AND `tipoDoc` = :tipoDoc");
  $consulta->bindParam(':nroDoc',$nroDoc);
  $consulta->bindParam(':tipoDoc',$tipoDoc);
  $consulta->execute();
  return 1;

}
*/

function buscarDespachosPorMarcaQuincena($db,$anhio,$mes,$quin,$pagado,$dni){
  $whereQuincena = ($quin==1)?" AND fchPago = '$anhio-$mes-14' ":" AND  fchPago = '$anhio-$mes-28'  ";
  $wherePagado = ($pagado=='-')?"  ":" AND `pagado` = '$pagado' ";
  //echo "Pagado $wherePagado";
  $whereDni = ($dni == '')?"":" AND `despachopersonal`.`idTrabajador`= '$dni' ";
  $consulta = $db->prepare("SELECT count(*) as cantidad,  `despachopersonal`.`idTrabajador`, concat(`apPaterno`,' ',`apMaterno`,', ',`nombres`) AS nombreCompleto , `pagado`,  `anhio`, `mes`, `fchPago` FROM `despachopersonal`, `trabajador`  WHERE `despachopersonal`.`idTrabajador` = `trabajador`.`idTrabajador`  $whereQuincena  $wherePagado $whereDni  Group by `idTrabajador`, `pagado` ORDER BY `apPaterno`    ");
  //$consulta->bindParam(':dni',$dni);
  //$consulta->bindParam(':mes',$mes);
  //$consulta->bindParam(':anhio',$anhio);
	$consulta->execute();
	return $consulta->fetchAll();

}


function buscarDespachosConfirmadosPorCuenta($db,$anhio,$mes,$quin,$cliente,$cuenta){
  $whereQuincena = ($quin==1)?" AND fchPago = '$anhio-$mes-14' ":" AND  fchPago = '$anhio-$mes-28'  ";
  $whereCliente = ($cliente=='')?'':" AND `cliente`.`nombre` = '$cliente' ";
  $whereCuenta = ($cuenta=='')?'':" AND `cuenta` = '$cuenta' ";
  $consulta = $db->prepare("SELECT `cliente`.`nombre`, `cuenta`,`despachopersonal`.`fchDespacho`, `despachopersonal`.`correlativo`,`despacho`.`placa`, count(`despachopersonal`.`pagado`) as personal ,  `despacho`.`pagado`,`despacho`.`costo`,`despacho`.`costoTotal`, `fchPago`, `anhio`, `mes`   FROM `despachopersonal`, `despacho`, `cliente`  WHERE `despacho`.`idCliente`= `cliente`.`idRuc` AND `despachopersonal`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachopersonal`.`correlativo` = `despacho`.`correlativo` AND `despachopersonal`.`pagado` = 'Si'  $whereQuincena  $whereCliente  $whereCuenta  GROUP BY `despacho`.`fchDespacho`, `despacho`.`correlativo` ORDER BY `nombre`,`cuenta`, `fchDespacho`, `correlativo` ");  
	$consulta->execute();
	return $consulta->fetchAll();
} 


function editaValor($db,$fchDespacho,$correlativo,$idTrabajador,$valorRol){
  $actualiza = $db->prepare("UPDATE `despachopersonal` SET `valorRol` = :valorRol, `usuario` = :usuario,  `fchActualizacion` = curdate() WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `idTrabajador` = :idTrabajador");
  $actualiza->bindParam(':correlativo',$correlativo);
  $actualiza->bindParam(':fchDespacho',$fchDespacho);
  $actualiza->bindParam(':valorRol',$valorRol);
  $actualiza->bindParam(':idTrabajador',$idTrabajador);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute(); 
}

function generaCodOcurrencia($db){
  $anhio = DATE('Y');
  $consulta = $db->prepare("SELECT `codOcurrencia` FROM `ocurrencia` order by `codOcurrencia` desc LIMIT 1");
  $consulta->execute();
  $resultado = $consulta->fetchAll();
  foreach($resultado as $item) {
    $ultCod = $item['codOcurrencia'];
    $anhioMov = substr($ultCod,1,4);
    $nroMov   = 1*substr($ultCod,5,6);
    if ($anhio == $anhioMov)
      return "O".$anhioMov.substr("000000".(1+$nroMov),-6);
  }
  return "O".$anhio."000001";

};

function buscarOcurrencias($db,$fchDespacho,$correlativo){
  $consulta = $db->prepare("SELECT `tipoOcurrencia`, `descripcion`, `montoTotal`, `montoDistribuir`  FROM `ocurrencia` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
  $consulta->bindParam(':correlativo',$correlativo);
  $consulta->execute();
  return $consulta->fetchAll();
};

function insertaEnOcurrencia($db,$fchDespacho,$correlativo,$tipoOcurrencia,$descripcion,$mntTotal,$mntDistribuir,$codOcurrencia,$tipoDoc,$nroDoc){
  $usuario = $_SESSION["usuario"];

  //echo "$fchDespacho, $correlativo, $tipoOcurrencia, $descripcion, $mntTotal, $mntDistribuir, $codOcurrencia, $tipoDoc, $nroDoc";

  $subTipoOcurrencia = "";
  //echo "tipo ocurrencia  $tipoOcurrencia, parte 0* ".substr($tipoOcurrencia,0,9)."*";
  if (substr($tipoOcurrencia,0,9) == "Descuento" ){
    $subTipoOcurrencia = substr($tipoOcurrencia,10,100);
    $tipoOcurrencia = "Descuento";
  }

  //echo "Subtipo $subTipoOcurrencia ";
  
  //$descripcion = utf8_decode($descripcion);
  //echo "<br>DESCRIPCION ".$descripcion."<br>";
  $inserta = $db->prepare("INSERT INTO `ocurrencia` (`fchDespacho`, `correlativo`, `tipoOcurrencia`, subTipoOcurrencia, `descripcion`, `montoTotal`, `montoDistribuir`, `codOcurrencia`, `tipoDoc`, `nroDoc`, `usuario`, `fchCreacion`, `hraCreacion`) VALUES (:fchDespacho, :correlativo, :tipoOcurrencia, :subTipoOcurrencia, :descripcion, :mntTotal, :mntDistribuir, :codOcurrencia, :tipoDoc, :nroDoc, :usuario, curdate(), curtime())");
  $inserta->bindParam(':fchDespacho',$fchDespacho);
  $inserta->bindParam(':correlativo',$correlativo);
  $inserta->bindParam(':tipoOcurrencia',$tipoOcurrencia);
  $inserta->bindParam(':subTipoOcurrencia',$subTipoOcurrencia);  

  $inserta->bindParam(':descripcion',$descripcion);
  $inserta->bindParam(':mntTotal',$mntTotal);
  $inserta->bindParam(':mntDistribuir',$mntDistribuir);
  $inserta->bindParam(':codOcurrencia',$codOcurrencia);
  $inserta->bindParam(':tipoDoc',$tipoDoc);
  $inserta->bindParam(':nroDoc',$nroDoc);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();

  $actualiza = $db->prepare("UPDATE `ocurrencia` SET `descripcion` = replace(`descripcion`,'&#209;','Ñ'), `descripcion` = replace(`descripcion`,'&#241;','ñ')  WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo  AND `tipoOcurrencia` = :tipoOcurrencia AND `fchCreacion` = curdate()");
  $actualiza->bindParam(':fchDespacho',$fchDespacho);
  $actualiza->bindParam(':correlativo',$correlativo);
  $actualiza->bindParam(':tipoOcurrencia',$tipoOcurrencia);
  $actualiza->execute();   
  sleep(5);
};

  function generaCorrOcurrencia($db, $fchDespacho, $correlativo ){
    $consulta = $db->prepare("SELECT corrOcurrencia FROM `ocurrenciatercero` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ORDER BY `corrOcurrencia` DESC LIMIT 1 ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);

    $consulta->execute();
    $resultado = $consulta->fetchAll();
    foreach($resultado as $item) {
      $corrOcurrencia = $item['corrOcurrencia'];
      return (1*$corrOcurrencia + 1);
    }
    return "1";
  }

function insertaEnOcurrenciaTercero($db,$fchDespacho,$correlativo,$auxTipoOcurrencia,$descripcion,$mntTotal,$idTercero){
  if ($auxTipoOcurrencia == 'terceroMoy'){
    $tipoOcurrencia = $auxTipoOcurrencia;
    $tipoConcepto = "Descuento proveedor";
  } 
  else {
    $tipoOcurrencia = strtok($auxTipoOcurrencia, "|");
    $tipoConcepto =  strtok("|");
  }

  $corrOcurrencia = generaCorrOcurrencia($db, $fchDespacho, $correlativo );

  $usuario = $_SESSION["usuario"];
  //echo " $fchDespacho,  $correlativo, $tipoOcurrencia  $descripcion, $mntTotal  ,$idTercero ";
  $inserta = $db->prepare("INSERT INTO `ocurrenciatercero` (`fchDespacho`, `correlativo`, `corrOcurrencia`, `tipoOcurrencia`,`tipoConcepto`, `descripcion`, `montoTotal`, `idTercero`, `usuario`, `fchCreacion`, `hraCreacion`) VALUES (:fchDespacho, :correlativo, :corrOcurrencia , :tipoOcurrencia ,  :tipoConcepto ,  :descripcion,:mntTotal, :idTercero,  :usuario, curdate(), curtime())");
  $inserta->bindParam(':fchDespacho',$fchDespacho);
  $inserta->bindParam(':correlativo',$correlativo);
  $inserta->bindParam(':tipoOcurrencia',$tipoOcurrencia);
  $inserta->bindParam(':corrOcurrencia',$corrOcurrencia);
  $inserta->bindParam(':tipoConcepto',$tipoConcepto);
  $inserta->bindParam(':descripcion',$descripcion);
  $inserta->bindParam(':mntTotal',$mntTotal);
  $inserta->bindParam(':idTercero',$idTercero);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();

  //echo "Para ocurrencia tercero $fchDespacho, $correlativo, $tipoOcurrencia, $corrOcurrencia, $tipoConcepto, $descripcion, $mntTotal, $idTercero, $usuario";

  $actualiza = $db->prepare("UPDATE `ocurrenciatercero` SET `descripcion` = replace(`descripcion`,'&#209;','Ñ'), `descripcion` = replace(`descripcion`,'&#241;','ñ')  WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo  AND `tipoOcurrencia` = :tipoOcurrencia AND `fchCreacion` = curdate()");
  $actualiza->bindParam(':fchDespacho',$fchDespacho);
  $actualiza->bindParam(':correlativo',$correlativo);
  $actualiza->bindParam(':tipoOcurrencia',$tipoOcurrencia);
  $actualiza->execute();

  return $inserta->rowCount(); 
}


function subirArchivo($db,$abrir){
  $consulta = "TRUNCATE TABLE _cargarosen";
  $db->query($consulta);
  while(!feof($abrir)){
    $linea = fgets($abrir);
    $enPartes = explode(",",$linea);
    $nro = isset($enPartes['0'])?$enPartes['0']:'';
    $guiaMoy = isset($enPartes['1'])?$enPartes['1']:'';
    $guiaRosen = isset($enPartes['2'])?$enPartes['2']:'';
    $auxConductor = explode("-",isset($enPartes['3'])?$enPartes['3']:'');
    $idConductor = $auxConductor[0];
    $nombConductor = isset($auxConductor[1])?$auxConductor[1]:"";
    $placa = isset($enPartes['4'])?$enPartes['4']:'';
    $nroCarga = isset($enPartes['5'])?$enPartes['5']:'';
    $nombClie = isset($enPartes['6'])?$enPartes['6']:'';
    $cuenta =  isset($enPartes['7'])?$enPartes['7']:'';
    $auxDistrito = explode("-",isset($enPartes['8'])?$enPartes['8']:'');
    $idDistrito = $auxDistrito[0];
    $nombDistrito = isset($auxDistrito[1])?$auxDistrito[1]:"";
    $hraLlegARosen = isset($enPartes['9'])?$enPartes['9']:'';
    $hraSalRosen = isset($enPartes['10'])?$enPartes['10']:'';
    $hraLLegAlClien = isset($enPartes['11'])?$enPartes['11']:'';
    $hraSalDelClien = isset($enPartes['12'])?$enPartes['12']:'';
    $hraFin = isset($enPartes['13'])?$enPartes['13']:'';
    $observ = isset($enPartes['14'])?$enPartes['14']:'';
    $coordi = isset($enPartes['15'])?$enPartes['15']:'';
    $inserta = $db->prepare("INSERT INTO `_cargarosen` (`nro`, `guiaPorteMoy`, `guiaRosen`, `idConductor`, `nombConductor`, `placa`, `nroCarga`, `nombCliente`, `cuenta`, `idDistrito`, `nombDistrito`, `hraLlegadaARosen`, `hraSalidaRosen`, `hraLlegadaAlClien`, `hraSalidaDelClien`, `hraFinal`, `observ`, `coordinado`) VALUES ('$nro', '$guiaMoy', '$guiaRosen', '$idConductor', '$nombConductor', '$placa', '$nroCarga','$nombClie', '$cuenta', '$idDistrito','$nombDistrito', '$hraLlegARosen', '$hraSalRosen', '$hraLLegAlClien', '$hraSalDelClien', '$hraFin', '$observ', '$coordi')");
    $inserta->execute();
  }
  $nroCargados = $db->exec("DELETE FROM `_cargarosen` WHERE nro < 1");
  $nroTrabNoExiste = $db->exec("UPDATE `_cargarosen` LEFT JOIN trabajador ON idConductor = idTrabajador SET obsTrabajador = IF( estadoTrabajador =  'Inactivo',  'Conductor Inactivo', 'No Existe Conductor' ) WHERE idTrabajador IS NULL OR estadoTrabajador =  'Inactivo'");
  $nroPlacaNoExiste = $db->exec("UPDATE `_cargarosen` LEFT JOIN vehiculo ON placa = idPlaca  SET obsPlaca = 'No Existe Placa' WHERE idPlaca Is NULL");
  /*
  $nroPlacaNoExiste = $db->exec("  UPDATE `_cargarosen` LEFT JOIN vehiculo ON placa = idPlaca SET obsPlaca =  IF( `dimInteriorLargo` *  `dimInteriorAncho` *  `dimInteriorAlto` <=0 , 'Vehículo con dimensiones incompletas',  'No Existe Placa' )  WHERE idPlaca IS NULL OR (`dimInteriorLargo` *  `dimInteriorAncho` *  `dimInteriorAlto` <=0)");
  */
  return "$nroCargados-$nroTrabNoExiste-$nroPlacaNoExiste";
}

function buscarSubidosRosen($db){
  $sql = 'SELECT `nro`, `guiaPorteMoy`, `guiaRosen`, `idConductor`, `nombConductor`, `placa`, `nroCarga`, `nombCliente`, `cuenta`, `idDistrito`, `nombDistrito`, `hraLlegadaARosen`, `hraSalidaRosen`, `hraLlegadaAlClien`, `hraSalidaDelClien`, `hraFinal`, `observ`, `coordinado`, `obsTrabajador`, `obsPlaca` FROM `_cargarosen`';
  return $db->query($sql);
}


function guardaRegistroDespacho($db, $fchDespacho,$correlativo,$hraDespacho,$placa,$guia,$valorServicio,$igvServicio,$idCliente,$cuenta,$topeServicioHraNormal,$tipoServicioPago,$valorConductor,$valorAuxiliar, $costoHraExtra,$toleranCobroHraExtra,$nroAuxiliaresCuenta,$valorAuxAdicional,$usarMaster,$recorridoEsperado,$usaReten,$modoCreacion,$kmInicio,$fchCreacion,$usuario){

  $inserta = $db->prepare("INSERT INTO despacho (fchDespacho, correlativo, hraInicio, placa, guiaCliente ,valorServicio, igvServicio, idCliente, cuenta, tipoServicioPago, topeServicioHraNormal, valorConductor, valorAuxiliar, costoHraExtra, toleranCobroHraExtra, nroAuxiliaresCuenta, valorAuxAdicional, usarMaster, recorridoEsperado, usaReten, modoCreacion, kmInicio, usuario, fchCreacion) VALUES ( :fchDespacho, :correlativo, :hraDespacho, :placa, :guiaCliente, :valorServicio, :igvServicio, :idCliente, :cuenta,  :tipoServicioPago,:topeServicioHraNormal, :valorConductor, :valorAuxiliar, :costoHraExtra, :toleranCobroHraExtra, :nroAuxiliaresCuenta, :valorAuxAdicional, :usarMaster, :recorridoEsperado, :usaReten, :modoCreacion, :kmInicio, :usuario,:fchCreacion)");
  $inserta->bindParam(':fchDespacho',$fchDespacho);
  $inserta->bindParam(':correlativo',$correlativo);
  $inserta->bindParam(':hraDespacho',$hraDespacho);
  $inserta->bindParam(':placa',$placa);
  $inserta->bindParam(':guiaCliente',$guia);
  $inserta->bindParam(':valorServicio',$valorServicio);
  $inserta->bindParam(':igvServicio',$igvServicio);
  $inserta->bindParam(':idCliente',$idCliente);
  $inserta->bindParam(':cuenta',$cuenta);
  $inserta->bindParam(':topeServicioHraNormal',$topeServicioHraNormal);
  $inserta->bindParam(':tipoServicioPago',$tipoServicioPago);

  $inserta->bindParam(':valorConductor',$valorConductor);
  $inserta->bindParam(':valorAuxiliar',$valorAuxiliar);
  $inserta->bindParam(':costoHraExtra',$costoHraExtra);
  $inserta->bindParam(':toleranCobroHraExtra',$toleranCobroHraExtra);
  $inserta->bindParam(':nroAuxiliaresCuenta',$nroAuxiliaresCuenta);
  $inserta->bindParam(':valorAuxAdicional',$valorAuxAdicional);
  $inserta->bindParam(':usarMaster',$usarMaster);
  $inserta->bindParam(':recorridoEsperado',$recorridoEsperado);
  $inserta->bindParam(':usaReten',$usaReten);
  $inserta->bindParam(':modoCreacion',$modoCreacion);
  $inserta->bindParam(':kmInicio',$kmInicio);

  $inserta->bindParam(':fchCreacion',$fchCreacion);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();
  return $inserta->rowCount();
}

function nuevoCorrelativoDespacho($db,$fchDespacho){
  $correlativo = 1;
  $buscaCorrelativo = $db->prepare("SELECT `correlativo` FROM `despacho` WHERE `fchDespacho` = :fchDespacho order by `correlativo` desc limit 1");
  $buscaCorrelativo->bindParam(':fchDespacho',$fchDespacho);
  $buscaCorrelativo->execute();
  foreach ($buscaCorrelativo as $arrayCorrelativo){
    $correlativo = $arrayCorrelativo['correlativo'] + 1;
  };
  return $correlativo;
}

function guardaRegistroDespachoPersonal($db,$fchDespacho,$correlativo,$idTrabajador,$valorRol,$tipoRol,$trabHraInicio, $trabHraFin){
  $inserta = $db->prepare("INSERT INTO despachopersonal (fchDespacho, correlativo, idTrabajador, valorRol, tipoRol,`trabHraInicio`, `trabHraFin`, trabFchDespachoFin) VALUES ( :fchDespacho, :correlativo,  :idTrabajador, :valorRol, :tipoRol, :trabHraInicio, :trabHraFin, :fchDespacho)");
  $inserta->bindParam(':fchDespacho',$fchDespacho);
  $inserta->bindParam(':correlativo',$correlativo);
  $inserta->bindParam(':idTrabajador',$idTrabajador);
  $inserta->bindParam(':valorRol',$valorRol);
  $inserta->bindParam(':tipoRol',$tipoRol);
  $inserta->bindParam(':trabHraInicio',$trabHraInicio);
  $inserta->bindParam(':trabHraFin',$trabHraFin);
  $inserta->execute();
  return $inserta->rowCount();
}

function guardaDespachoRosen($db,$conductor,$placa,$cuenta,$hraDespacho,$guiaRosen,$porcIgv,$fchDespacho,$usuario){
 // $usuario = $_SESSION["usuario"];
  $fchCreacion = Date("Y-m-d");

  $correlativo = nuevoCorrelativoDespacho($db,$fchDespacho);
  $idCliente = '20258886420'; //Rosen
  
  $auxDataCuenta = buscarClienteCuenta($db,$idCliente,$cuenta);
  foreach ($auxDataCuenta as $itemCuenta){
    $recorridoEsperado = $itemCuenta['kilometrajeEsperado'];
    $valorServicio  = $itemCuenta['valorServicio'];
    $topeServicioHraNormal = $itemCuenta['topeServicioHraNormal'];
    $tipoServicioPago  = $itemCuenta['tipoServicioPago'];
    $valorConductor = $itemCuenta['valorConductor'];
    $valorAuxiliar = $itemCuenta['valorAuxiliar'];

    $costoHraExtra = $itemCuenta['valorServicioHraExtra'];
    $toleranCobroHraExtra = $itemCuenta['toleranCobroHraExtra'];
    $nroAuxiliaresCuenta = $itemCuenta['nroAuxiliares'];
    $valorAuxAdicional = $itemCuenta['valorAuxAdicional'];
    $usarMaster = $itemCuenta['usarMaster'];    

  }
  $igvServicio = $porcIgv * $valorServicio;
  $kmInicio = null;

  $nro = guardaRegistroDespacho($db, $fchDespacho,$correlativo,$hraDespacho,$placa,$guiaRosen,$valorServicio,$igvServicio,$idCliente,$cuenta,$topeServicioHraNormal,$tipoServicioPago,$valorConductor,$valorAuxiliar, $costoHraExtra,$toleranCobroHraExtra,$nroAuxiliaresCuenta,$valorAuxAdicional,$usarMaster,$recorridoEsperado,'No','CargaRosen',$kmInicio,$fchCreacion,$usuario);

  if ($nro == 1 ){
    //verificar si es solo personal
    if (substr($cuenta,0,12) != 'SoloPersonal'){
     $tempo =  guardaRegistroDespachoPersonal($db,$fchDespacho,$correlativo,$conductor,$valorConductor,'Conductor');

    }
    //******************************
  }
  return $nro;
  //echo "$fchDespacho - $correlativo - $hraDespacho - $placa - $guiaRosen - $porcIgv </br>  ";
}

function procesaInfo($db,$porcIgv,$fch,$usuario){
  $consulta = $db->prepare("SELECT `nro`, `guiaPorteMoy`, `guiaRosen`, `idConductor`, `placa`, `nroCarga`, `nombCliente`, `cuenta`, `idDistrito`, `hraLlegadaARosen`,  `hraSalidaRosen`, `hraLLegadaAlClien`, `hraSalidaDelClien`, `observ`, `coordinado` FROM `_cargarosen` WHERE `obsTrabajador` = '' AND `obsPlaca` = '' ORDER BY  `placa`, `hraSalidaRosen`");
  $consulta->execute();
  $rstRosen = $consulta->fetchAll();
  $guiaMoyAnt = "";
  $primeraVez = 1; //Es por el encabezado
  $cant = 0;
  foreach($rstRosen as $item) {
    $guiaMoy  = $item['guiaPorteMoy'];
    $idDistrito = $item['idDistrito'];//El distrito donde se hace la entrega
    if ($guiaMoy != $guiaMoyAnt){
      if ($primeraVez == 0){
        $cant = $cant + guardaDespachoRosen($db,$conductor,$placa,$cuenta,$hraLlegARosen,$guiaRosen,$porcIgv,$fch,$usuario);
      }
      $conductor = $item['idConductor'];
      $placa  = $item['placa'];
      $cuenta = $item['cuenta'];
      $hraLlegARosen = $item['hraLlegadaARosen'];
      $guiaRosen = $item['guiaRosen'];
      $primeraVez = 0;
    } else {
      $guiaRosen .= " ".$item['guiaRosen'];
    }
    $guiaMoyAnt = $guiaMoy;
  }
  $cant = $cant + guardaDespachoRosen($db,$conductor,$placa,$cuenta,$hraLlegARosen,$guiaRosen,$porcIgv,$fch,$usuario);
  $rpta = "Se han generado $cant despachos el día $fch";
  return $rpta;
};

function buscarDiasTrabajados($db,$fchIni,$fchFin,$dni,$oper,$dias,$categ){
  $where = "";
  if ($dni != '')
    $where .= " AND `despachopersonal`.`idTrabajador` = :dni ";

  $having = "HAVING 1";
  if ($categ != '')
    $having .= " AND `categoria` = :categ ";
  if ($dias != '')
    $having .= " AND dias $oper :dias ";

  $q1 = substr($fchIni,0,8)."14";
  $q2 = substr($fchIni,0,8)."28";

  $consulta = $db->prepare("SELECT `despachopersonal`.`idTrabajador`, concat(`apPaterno`,' ' ,`apMaterno`,', ',`nombres`) AS `nombCompleto` , trabajador.tipoTrabajador, trabajador.minimoSemanal, count(distinct `fchDespacho`) AS `dias`,  count(`fchDespacho`) AS `despachos`,  if(quincenadetalle.categoria IS NULL, q2.categoria,  quincenadetalle.categoria) AS categoria FROM `despachopersonal`, `trabajador` LEFT JOIN quincenadetalle ON `trabajador`.idTrabajador = quincenadetalle.idTrabajador AND quincenadetalle.quincena = '$q1'   LEFT JOIN quincenadetalle AS q2 ON `trabajador`.idTrabajador = q2.idTrabajador AND q2.quincena = '$q2'  WHERE `despachopersonal`.`idTrabajador` =  `trabajador`.idTrabajador AND (`fchDespacho` >= :fchIni AND `fchDespacho` <= :fchFin ) AND `despachopersonal`.`idTrabajador` != '' $where GROUP BY `despachopersonal`.`idTrabajador` $having ORDER BY `nombCompleto`");


  $consulta->bindParam(':fchIni',$fchIni);
  $consulta->bindParam(':fchFin',$fchFin);
  if ($dni != '')
    $consulta->bindParam(':dni',$dni);
  if ($dias != '')
    $consulta->bindParam(':dias',$dias);
  if ($categ != '')
    $consulta->bindParam(':categ',$categ);

  $consulta->execute();
  return $consulta->fetchAll();
}


function buscarAuxiliaresDespacho($db,$fchDespacho,$correlativo){
  $consulta = $db->prepare("SELECT concat(`nombres`,' ',apPaterno,' ', apMaterno) As `nombCompleto` FROM `despachopersonal`, trabajador WHERE `despachopersonal`.idTrabajador =  `trabajador`.idTrabajador AND `despachopersonal`.`fchDespacho` = :fchDespacho AND `despachopersonal`.`correlativo` = :correlativo AND `tipoRol` IN ('Auxiliar','AuxAdic')");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
  $consulta->bindParam(':correlativo',$correlativo);
  $consulta->execute();
  return $consulta->fetchAll();
}

function insertaDetallesPorCobrar($db,$fchDespacho,$correlativo,$codigo,$ctoUnitario,$cantidad, $observDetalle = null ,$arrData = NULL){

  /*echo "<pre>";
  print_r($arrData);
  echo "</pre>";*/
  $usuario = $_SESSION['usuario'];
  if ($arrData == NULL){
    $docCobranza = NULL;
    $pagado = "No";
    $tipoDoc = "";
  } else {
    $docCobranza = $arrData['docCobranza'];
    $pagado = ($arrData['pagado'] == '')?"No":$arrData['pagado'];
    $tipoDoc = $arrData['tipoDoc'];
  }

  $inserta = $db->prepare("INSERT INTO `despachodetallesporcobrar` (`fchDespacho`, `correlativo`, `codigo`, `costoUnit`, `cantidad`, `docCobranza`, `pagado`, `observDetallePorCobrar`, `tipoDoc`, `usuario`, `fchCreacion`) VALUES (:fchDespacho, :correlativo, :codigo, :ctoUnitario, :cantidad, :docCobranza, :pagado, :observDetalle ,:tipoDoc, :usuario, curdate());");
  $inserta->bindParam(':fchDespacho',$fchDespacho);
  $inserta->bindParam(':correlativo',$correlativo);
  $inserta->bindParam(':codigo',$codigo);
  $inserta->bindParam(':ctoUnitario',$ctoUnitario);
  $inserta->bindParam(':cantidad',$cantidad);

  $inserta->bindParam(':docCobranza',$docCobranza);
  $inserta->bindParam(':pagado',$pagado);
  $inserta->bindParam(':observDetalle',$observDetalle);
  $inserta->bindParam(':tipoDoc',$tipoDoc);

  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();
  //echo "SE EJECUTO $fchDespacho,$correlativo,$codigo,$ctoUnitario,$cantidad $usuario, $docCobranza, $pagado, $tipoDoc ";
}

function gruposCobranza($db,$nroDoc,$tipoDoc){
  $consulta = $db->prepare("SELECT count(*) AS cant, min(`despacho`.`fchDespacho`) AS minimo, max(`despacho`.`fchDespacho`) AS maximo, `detalles`.`codigo`, sum(`detalles`.`costoUnit`* `detalles`.`cantidad`) AS total, sum(`detalles`.`cantidad`) AS cantidad, placa, `idCliente`, `cuenta`, tipoServicioPago ,`concluido` FROM `despacho`, `despachodetallesporcobrar` AS detalles WHERE `despacho`.`fchDespacho` =  `detalles`.`fchDespacho` AND `despacho`.`correlativo` = `detalles`.`correlativo` AND `detalles`.`docCobranza` = :nroDoc AND `detalles`.`tipoDoc` = :tipoDoc GROUP BY codigo , placa, cuenta");
  $consulta->bindParam(':nroDoc',$nroDoc);
  $consulta->bindParam(':tipoDoc',$tipoDoc);        
  $consulta->execute();
  return $consulta->fetchAll();
}

function generaDetalleDocCobranza($db,$nroDoc,$tipoDoc,$corr,$cantidad, $precUnit, $total, $descrip){
  $usuario = $_SESSION['usuario'];
  //echo "$usuario, $nroDoc,$tipoDoc,$corr,$cantidad, $precUnit, $total, $descrip";
  $inserta = $db->prepare("INSERT INTO `doccobranzadetalle` (`docCobranza`, `tipoDoc`, `corr`, `cant`, `descrip`, `precUnit`, `creacUsuario`, `creacFch`) VALUES (:nroDoc, :tipoDoc, :corr, :cantidad, :descrip, :precUnit, :usuario, now());");
  $inserta->bindParam(':nroDoc',$nroDoc);
  $inserta->bindParam(':tipoDoc',$tipoDoc);
  $inserta->bindParam(':corr',$corr);
  $inserta->bindParam(':cantidad',$cantidad);
  $inserta->bindParam(':descrip',$descrip);
  $inserta->bindParam(':precUnit',$precUnit);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();
}

function insertarCargaDespachos($db,$registros){
  $nroCargados = $db->exec($registros);
}

function completarCargaDespachos($db){
  $usuario = $_SESSION['usuario'];
  $nro = $db->exec("UPDATE `_cargadespachos$usuario` SET conductor = substr(tempConductor,1,  locate('-',tempConductor,1) -1);
                    UPDATE `_cargadespachos$usuario` SET aux01 = substr(tempAux01,1,  locate('-',tempAux01,1) -1);
                    UPDATE `_cargadespachos$usuario` SET aux02 = substr(tempAux02,1,  locate('-',tempAux02,1) -1);
                    UPDATE `_cargadespachos$usuario` SET aux03 = substr(tempAux03,1,  locate('-',tempAux03,1) -1);
                    UPDATE `_cargadespachos$usuario` SET aux04 = substr(tempAux04,1,  locate('-',tempAux04,1) -1);
                    UPDATE `_cargadespachos$usuario` SET aux05 = substr(tempAux05,1,  locate('-',tempAux05,1) -1);
                    UPDATE `_cargadespachos$usuario` SET auxReten = substr(tempAuxReten,1,  locate('-',tempAuxReten,1) -1);");
}  

function actualizaComentariosCargaDespachos($db,$idCliente){
  $usuario = $_SESSION['usuario'];
  $limiteKm = 300;
  $limiteDiasRetrasoCarga = 10;  //5;
  $nroNoCuenta = $db->exec("UPDATE `_cargadespachos$usuario` AS carga LEFT JOIN  clientecuenta ON `clientecuenta`.idCliente  = '$idCliente' AND `clientecuenta`.tipoServicio  = `carga`.cuenta SET obsCuenta = 'No Existe Cuenta' WHERE  tipoServicio IS NULL");
  $nroSinPlaca = $db->exec("UPDATE `_cargadespachos$usuario` AS carga SET obsPlaca = 'Sin Placa' WHERE placa = '' ");
  $nroNoPlaca = $db->exec("UPDATE `_cargadespachos$usuario` AS carga LEFT JOIN vehiculo ON `carga`.placa = `vehiculo`.idPlaca  SET obsPlaca = 'No existe placa' WHERE `carga`.placa != '' AND idPlaca IS  NULL");
  $nroSinConductor = $db->exec("UPDATE  `_cargadespachos$usuario` AS carga SET `obsConductor` = 'Sin Conductor' WHERE `carga`.conductor = '' ");
  $nroNoConductor = $db->exec("UPDATE  `_cargadespachos$usuario` AS carga LEFT JOIN trabajador ON `carga`.conductor = `trabajador`.idTrabajador AND tipoTrabajador IN ('Conductor','Coordinador') SET `obsConductor` = 'No Existe IdConductor'  WHERE `carga`.conductor != '' AND idtrabajador is null;");
  $nroNoActivoConductor = $db->exec("UPDATE  `_cargadespachos$usuario` AS carga LEFT JOIN trabajador ON  `trabajador`.estadoTrabajador = 'Activo' AND tipoTrabajador IN ('Conductor','Coordinador')  SET `obsConductor` = 'Conductor Inactivo'  WHERE `carga`.conductor != '' AND `carga`.conductor = `trabajador`.idTrabajador AND idtrabajador is null");
  $nroNoAux01 = $db->exec("UPDATE  `_cargadespachos$usuario` AS carga LEFT JOIN trabajador ON `carga`.aux01 = `trabajador`.idTrabajador SET `obsAux01` = 'No Existe IdAuxiliar'  WHERE `carga`.aux01 != '' AND idtrabajador is null;");
  $nroNoActivoAux01 = $db->exec("UPDATE  `_cargadespachos$usuario` AS carga LEFT JOIN trabajador ON `trabajador`.estadoTrabajador = 'Activo' SET `obsAux01` = 'Personal Inactivo'  WHERE `carga`.aux01 != '' AND `carga`.aux01 = `trabajador`.idTrabajador AND idtrabajador is null");

  $nroNoAux02 = $db->exec("UPDATE `_cargadespachos$usuario` AS carga LEFT JOIN trabajador ON `carga`.aux02 = `trabajador`.idTrabajador SET `obsAux02` = 'No Existe Auxiliar'  WHERE `carga`.aux02 != '' AND idtrabajador is null;");
  $nroNoActivoAux02 = $db->exec("UPDATE  `_cargadespachos$usuario` AS carga LEFT JOIN trabajador ON `trabajador`.estadoTrabajador = 'Activo' SET `obsAux02` = 'Personal Inactivo' WHERE  `carga`.aux02 != '' AND `carga`.aux02 = `trabajador`.idTrabajador AND idtrabajador is null");

  $nroNoAux03 = $db->exec("UPDATE  `_cargadespachos$usuario` AS carga LEFT JOIN trabajador ON `carga`.aux03 = `trabajador`.idTrabajador SET `obsAux03` = 'No Existe IdAuxiliar'  WHERE `carga`.aux03 != '' AND idtrabajador is null;");
  $nroNoActivoAux03 = $db->exec("UPDATE  `_cargadespachos$usuario` AS carga LEFT JOIN trabajador ON `trabajador`.estadoTrabajador = 'Activo' SET `obsAux03` = 'Personal Inactivo'  WHERE `carga`.aux03 != '' AND `carga`.aux03 = `trabajador`.idTrabajador AND idtrabajador is null");

  $nroNoAux04 = $db->exec("UPDATE  `_cargadespachos$usuario` AS carga LEFT JOIN trabajador ON `carga`.aux04 = `trabajador`.idTrabajador SET `obsAux04` = 'No Existe IdAuxiliar'  WHERE `carga`.aux04 != '' AND idtrabajador is null;");
  $nroNoActivoAux04 = $db->exec("UPDATE  `_cargadespachos$usuario` AS carga LEFT JOIN trabajador ON `trabajador`.estadoTrabajador = 'Activo' SET `obsAux04` = 'Personal Inactivo'  WHERE `carga`.aux04 != '' AND `carga`.aux04 = `trabajador`.idTrabajador AND idtrabajador is null");

  $nroNoAux05 = $db->exec("UPDATE  `_cargadespachos$usuario` AS carga LEFT JOIN trabajador ON `carga`.aux05 = `trabajador`.idTrabajador SET `obsAux05` = 'No Existe IdAuxiliar'  WHERE `carga`.aux05 != '' AND idtrabajador is null;");
  $nroNoActivoAux05 = $db->exec("UPDATE  `_cargadespachos$usuario` AS carga LEFT JOIN trabajador ON `trabajador`.estadoTrabajador = 'Activo' SET `obsAux05` = 'Personal Inactivo'  WHERE `carga`.aux05 != '' AND `carga`.aux05 = `trabajador`.idTrabajador AND idtrabajador is null");

  $nroNoAuxReten = $db->exec("UPDATE  `_cargadespachos$usuario` AS carga LEFT JOIN trabajador ON `carga`.auxReten = `trabajador`.idTrabajador SET `obsAuxReten` = 'No Existe IdAuxiliar'  WHERE `carga`.auxReten != '' AND idtrabajador is null;");
  $nroNoActivoAuxReten = $db->exec("UPDATE  `_cargadespachos$usuario` AS carga LEFT JOIN trabajador ON `trabajador`.estadoTrabajador = 'Activo' SET `obsAuxReten` = 'Personal Inactivo'  WHERE `carga`.auxReten != '' AND `carga`.auxReten = `trabajador`.idTrabajador AND idtrabajador is null");
  $nroFueraRangoKmInicio = $db->exec("UPDATE `_cargadespachos$usuario`, vehiculo SET obsKmInicio = 'Fuera de Rango'  WHERE `_cargadespachos$usuario`.placa = `vehiculo`.idPlaca AND abs( kmUltimaMedicion - kmInicio) > $limiteKm");
  $nroNoKmInicio = $db->exec("UPDATE  `_cargadespachos$usuario` AS carga SET `obsKmInicio` = 'Falta Km Inicio'  WHERE `carga`.kmInicio <= 0 ");
  $nroMalogradoKmInicio = $db->exec("UPDATE  `_cargadespachos$usuario` AS carga SET `obsKmInicio` = 'MALOGRADO'  WHERE `carga`.kmInicio = 'MALOGRADO' ");
  
  $nroFueraRango = $db->exec("UPDATE `_cargadespachos$usuario` SET obsFch = 'Fuera de Rango' WHERE NOT( datediff(curdate(),`fchDespacho`) < $limiteDiasRetrasoCarga AND datediff(curdate(),`fchDespacho`) >= 0)");

  return $nroNoCuenta."-".$nroSinPlaca."-".$nroNoPlaca."-".$nroSinConductor."-".$nroNoConductor."-".$nroNoActivoConductor."-".$nroNoAux01."-".$nroNoActivoAux01."-".$nroNoAux02."-".$nroNoActivoAux02."-".$nroNoAux03."-".$nroNoActivoAux03."-".$nroNoAux04."-".$nroNoActivoAux04."-".$nroNoAux05."-".$nroNoActivoAux05."-".$nroNoAuxReten."-".$nroNoActivoAuxReten."-".$nroNoKmInicio."-".$nroFueraRangoKmInicio."-".$nroMalogradoKmInicio."-".$nroFueraRango;
}

function verificarCargaDespachos($db,$idCliente){
  $usuario = $_SESSION['usuario'];
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE obsCuenta = 'No Existe Cuenta'");
  $nroNoCuenta = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE obsPlaca = 'Sin Placa'");
  $nroSinPlaca = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE obsPlaca = 'No existe placa'");
  $nroNoPlaca = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE `obsConductor` = 'Sin Conductor' ");
  $nroSinConductor = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE `obsConductor` = 'No Existe IdConducto' ");
  $nroNoConductor = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE `obsConductor` = 'Conductor Inactivo' ");
  $nroNoActivoConductor = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE `obsAux01` = 'No Existe IdAuxiliar'");
  $nroNoAux01 = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE `obsAux01` = 'Personal Inactivo'");
  $nroNoActivoAux01 = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE `obsAux02` = 'No Existe Auxiliar'");
  $nroNoAux02 = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE `obsAux02` = 'Personal Inactivo'");
  $nroNoActivoAux02 = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE `obsAux03` = 'No Existe IdAuxiliar'");
  $nroNoAux03 = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE `obsAux03` = 'Personal Inactivo'");
  $nroNoActivoAux03 = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE `obsAux04` = 'No Existe IdAuxiliar'");
  $nroNoAux04 = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE `obsAux04` = 'Personal Inactivo'");
  $nroNoActivoAux04 = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE `obsAux05` = 'No Existe IdAuxiliar'");
  $nroNoAux05 = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE `obsAux05` = 'Personal Inactivo'");
  $nroNoActivoAux05 = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE `obsAuxReten` = 'No Existe IdAuxiliar'");
  $nroNoAuxReten = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE `obsAuxReten` = 'Personal Inactivo'");
  $nroNoActivoAuxReten = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE `obsKmInicio` = 'Falta Km Inicio'");
  $nroNoKmInicio = $rst->rowCount();
  $rst = $db->query("SELECT * FROM `_cargadespachos$usuario` WHERE `obsFch` = 'Fuera de Rango'");
  $nroFchFueraRango = $rst->rowCount();

  return $nroNoCuenta."-".$nroSinPlaca."-".$nroNoPlaca."-".$nroSinConductor."-".$nroNoConductor."-".$nroNoActivoConductor."-".$nroNoAux01."-".$nroNoActivoAux01."-".$nroNoAux02."-".$nroNoActivoAux02."-".$nroNoAux03."-".$nroNoActivoAux03."-".$nroNoAux04."-".$nroNoActivoAux04."-".$nroNoAux05."-".$nroNoActivoAux05."-".$nroNoAuxReten."-".$nroNoActivoAuxReten."-".$nroNoKmInicio."-".$nroFchFueraRango;
}

function buscarRegistrosCargaDespachos($db,$tbl,$soloValidos = 'No'){
  $usuario = $_SESSION['usuario'];
  if ($tbl == 'carga')
    $tbl = "_cargadespachos$usuario";
  if ($soloValidos == 'Si'){
    $where = " WHERE `obsFch` = '' AND `obsCuenta` = '' AND ( (obsPlaca = '' AND obsConductor IN('','No es predictivo')) OR (obsPlaca = 'Sin Placa' AND obsConductor = 'Sin Conductor' AND substr(cuenta,1,12) = 'SoloPersonal')) AND  `obsAux01` IN('','No es predictivo') AND `obsAux02` IN('','No es predictivo') AND `obsAux03` IN('','No es predictivo') AND `obsAux04` IN('','No es predictivo') AND `obsAux05` IN('','No es predictivo') ";
  } else {
    $where = '';
  }

  $consulta = $db->prepare("SELECT `nro`, `fchDespacho`, `hraDespacho`, `cuenta`, `placa`, `tempConductor`, `tempAux01`, `tempAux02`, `tempAux03`, `tempAux04`, `tempAux05`, `tempAuxReten`, `kmInicio`, `idCliente`, `conductor`, `aux01`, `aux02`, `aux03`, `aux04`, `aux05`, `auxReten`, `obsFch`, `obsCuenta`, `obsPlaca`, `obsConductor`, `obsAux01`, `obsAux02`, `obsAux03`, `obsAux04`, `obsAux05` , `obsAuxReten` , `obsKmInicio` FROM `$tbl` $where");
  $consulta->execute();
  return $consulta->fetchAll();
}

function cantidadRegistrosCarga($db, $tbl){
  $consulta = $db->prepare("SELECT count(*) FROM `$tbl`");
  $consulta->execute();
  return $consulta->fetchColumn();
}

function procesaNuevosDespachos($db,$porcIgv,$id){
  $usuario = $_SESSION['usuario'];
  $fchCreacion = Date("Y-m-d");
  $rstCargaDespachos = buscarRegistrosCargaDespachos($db,$id,'Si');
  $cant = count($rstCargaDespachos);
  $i = $nroEncab = 0;
  $nro = $nroPersonal = 0;
  foreach($rstCargaDespachos as $item) {
    $fchDespacho = $item['fchDespacho'];
    $correlativo = nuevoCorrelativoDespacho($db,$fchDespacho);
    $hraDespacho = $item['hraDespacho'];
    $cuenta = $item['cuenta'];
    $idCliente = $item['idCliente'];
    $placa = ($item['placa'] == '')?NULL:$item['placa'];
    $kmInicio = ($item['kmInicio'] == '')?NULL:$item['kmInicio'];
    $auxReten = $item['auxReten'];
    $usaReten = ($auxReten == "")?"No":"Si";
    $auxDataCuenta = buscarClienteCuenta($db,$idCliente,$cuenta);
    foreach ($auxDataCuenta as $itemCuenta){
      $recorridoEsperado = $itemCuenta['kilometrajeEsperado'];
      $valorServicio  = $itemCuenta['valorServicio'];
      $topeServicioHraNormal = $itemCuenta['topeServicioHraNormal'];
      $tipoServicioPago  = $itemCuenta['tipoServicioPago'];
      $valorConductor = $itemCuenta['valorConductor'];
      $valorAuxiliar = $itemCuenta['valorAuxiliar'];
      $costoHraExtra = $itemCuenta['valorServicioHraExtra'];
      $toleranCobroHraExtra = $itemCuenta['toleranCobroHraExtra'];
      $nroAuxiliaresCuenta = $itemCuenta['nroAuxiliares'];
      $valorAuxAdicional = $itemCuenta['valorAuxAdicional'];
      $usarMaster = $itemCuenta['usarMaster'];
    }
    $guia = "";
    $igvServicio = $porcIgv * $valorServicio;
    $registro = guardaRegistroDespacho($db, $fchDespacho,$correlativo,$hraDespacho,$placa,$guia,$valorServicio,$igvServicio,$idCliente,$cuenta,$topeServicioHraNormal,$tipoServicioPago,$valorConductor,$valorAuxiliar, $costoHraExtra,$toleranCobroHraExtra,$nroAuxiliaresCuenta,$valorAuxAdicional,$usarMaster,$recorridoEsperado,$usaReten,$id,$kmInicio,$fchCreacion,$usuario);
    $nro = $nro + $registro;

    if ($registro == 1){
      $conductor = $item['conductor'];
      $aux01 = $item['aux01'];
      $aux02 = $item['aux02'];
      $aux03 = $item['aux03'];
      $aux04 = $item['aux04'];
      $aux05 = $item['aux05'];
      if ($conductor != '') 
        $nroPersonal = $nroPersonal + guardaRegistroDespachoPersonal($db,$fchDespacho,$correlativo,$conductor,$valorConductor,'Conductor');
      if ($aux01 != '')
        $nroPersonal = $nroPersonal + guardaRegistroDespachoPersonal($db,$fchDespacho,$correlativo,$aux01,$valorAuxiliar,'Auxiliar');
      if ($aux02 != '')
        $nroPersonal = $nroPersonal + guardaRegistroDespachoPersonal($db,$fchDespacho,$correlativo,$aux02,$valorAuxiliar,'Auxiliar');
      if ($aux03 != '')
        $nroPersonal = $nroPersonal + guardaRegistroDespachoPersonal($db,$fchDespacho,$correlativo,$aux03,$valorAuxiliar,'Auxiliar');
      if ($aux04 != '')
        $nroPersonal = $nroPersonal + guardaRegistroDespachoPersonal($db,$fchDespacho,$correlativo,$aux04,$valorAuxiliar,'Auxiliar');
      if ($aux05 != '')
        $nroPersonal = $nroPersonal + guardaRegistroDespachoPersonal($db,$fchDespacho,$correlativo,$aux05,$valorAuxiliar,'Auxiliar');
      if ($auxReten != '')
        $nroPersonal = $nroPersonal + guardaRegistroDespachoPersonal($db,$fchDespacho,$correlativo,$auxReten,$valorAuxiliar,'Auxiliar');

    }
  }
  actualizarArchivoCarga($db);

  return "Se han generado $nro registros de despacho y $nroPersonal registros de personal<br/>";
}


function detallesDocCobranza($db,$nroDoc,$tipoDoc){
  $consulta = $db->prepare("SELECT `docCobranza`, `tipoDoc`, `corr`, `cant`, `descrip`, `precUnit`, `mostrarPrecioUnit` FROM `doccobranzadetalle` WHERE `docCobranza` LIKE :nroDoc AND `tipoDoc` LIKE :tipoDoc");
  $consulta->bindParam(':nroDoc',$nroDoc);
  $consulta->bindParam(':tipoDoc',$tipoDoc);        
  $consulta->execute();
  return $consulta->fetchAll();
}

function actualizaDocDetalle($db,$nroDoc,$tipoDoc,$corr,$cant,$descrip,$precUnit,$checkPUnit){
  if($checkPUnit == "") $checkPUnit = NULL;
  $actualiza = $db->prepare("UPDATE `doccobranzadetalle` SET `cant` = :cant, `descrip` = :descrip, `precUnit` = :precUnit, mostrarPrecioUnit = :checkPUnit WHERE `docCobranza` = :nroDoc AND `tipoDoc` = :tipoDoc AND `corr` = :corr;");
  $actualiza->bindParam(':cant',$cant);
  $actualiza->bindParam(':descrip',$descrip);
  $actualiza->bindParam(':precUnit',$precUnit);
  $actualiza->bindParam(':checkPUnit',$checkPUnit);
  $actualiza->bindParam(':nroDoc',$nroDoc);
  $actualiza->bindParam(':tipoDoc',$tipoDoc);
  $actualiza->bindParam(':corr',$corr);
  $actualiza->execute();   
}

function actualizaObserv($db,$fchDespacho,$correlativo,$observ){
  $actualiza = $db->prepare("UPDATE `despacho` SET `observacion` = :observ WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo;");
  $actualiza->bindParam(':fchDespacho',$fchDespacho);
  $actualiza->bindParam(':correlativo',$correlativo);
  $actualiza->bindParam(':observ',$observ);
  $actualiza->execute();
}

function renombrarTablaDespacho($db,$idCliente){

  require 'librerias/PHPMailer6_0_5/src/Exception.php';
  require 'librerias/PHPMailer6_0_5/src/PHPMailer.php';
  require 'librerias/PHPMailer6_0_5/src/SMTP.php';
  $fecha = Date("Y_m_d");
  $usuario = $_SESSION['usuario'];
  $nombreActual = "_cargadespachos$usuario";
  $nombreNuevo  = "_$fecha$usuario$idCliente"."despachos";
  $nro = $db->exec("RENAME TABLE $nombreActual TO $nombreNuevo");
  if ($nro !== false){
    $nro = $db->exec("INSERT INTO `_cargadespachospanel` (`fchCreacion`, `creador`, `idCliente`, `hraSubido`, `estado`, `fchUltEstado`, `usuUltEstado`) VALUES (curdate(), '$usuario', '$idCliente', curtime(), 'cargado', curdate(), '$usuario')");
    if ($nro == 1){
      /** Error reporting */
      error_reporting(E_ALL);
      ini_set('display_errors', TRUE);
      ini_set('display_startup_errors', TRUE);
      date_default_timezone_set('Europe/London');

      define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

      date_default_timezone_set('Europe/London');

      /** Include PHPExcel */
      require_once 'librerias/classesPHPExcel/PHPExcel.php';
      //..\librerias\classesPHPExcel


      // Crear el objeto PHPExcel
      $objPHPExcel = new PHPExcel();

      // Configurar propiedades del Documento
      $descrip = "Carga de Despachos";
      $objPHPExcel->getProperties()->setCreator("Luis Escobedo")
                ->setLastModifiedBy("Luis Escobedo")
                ->setTitle($descrip)
                ->setSubject($descrip)
                ->setDescription($descrip)
                ->setKeywords($descrip)
                ->setCategory($descrip);


      //Primera Hoja
      $objPHPExcel->setActiveSheetIndex(0);
      $sheet = $objPHPExcel->getActiveSheet();
      $sheet->setCellValue('A1', "Cliente")
                  ->setCellValue('B1', $idCliente)
                  ->setCellValue('A2', "FchDespacho")
                  ->setCellValue('B2', "HraDespacho")
                  ->setCellValue('C2', "Cuenta")
                  ->setCellValue('D2', "Placa")
                  ->setCellValue('E2', "Conductor")
                  ->setCellValue('F2', "Auxiliar1")
                  ->setCellValue('G2', "Auxiliar2")
                  ->setCellValue('H2', "Auxiliar3")
                  ->setCellValue('I2', "Auxiliar4")
                  ->setCellValue('J2', "Auxiliar5")
                  ->setCellValue('K2', "Aux Retén")
                  ->setCellValue('L2', "Km. Inicio");

      $sheet->setTitle('Formato');
      $sheet->getColumnDimension('A')->setWidth(14);
      $sheet->getColumnDimension('B')->setWidth(14);
      $sheet->getColumnDimension('C')->setWidth(20);
      $sheet->getColumnDimension('D')->setWidth(14);
      $sheet->getColumnDimension('E')->setWidth(35);
      $sheet->getColumnDimension('F')->setWidth(35);
      $sheet->getColumnDimension('G')->setWidth(35);
      $sheet->getColumnDimension('H')->setWidth(0);
      $sheet->getColumnDimension('I')->setWidth(0);
      $sheet->getColumnDimension('J')->setWidth(0);
      $sheet->getColumnDimension('K')->setWidth(35);
      $sheet->getColumnDimension('L')->setWidth(12);
      
      $datos = buscarItemsCarga($db,$nombreNuevo);
      $fila = 3;
      foreach ($datos as $item) {
        $sheet->SetCellValue('A'.$fila, $item['fchDespacho']);
        $sheet->SetCellValue('B'.$fila, $item['hraDespacho']);
        $sheet->SetCellValue('C'.$fila, $item['cuenta']);
        $sheet->SetCellValue('D'.$fila, $item['placa']);
        $sheet->SetCellValue('E'.$fila, $item['conductor']);  
        $sheet->SetCellValue('F'.$fila, $item['aux01']);
        $sheet->SetCellValue('G'.$fila, $item['aux02']);
        $sheet->SetCellValue('H'.$fila, $item['aux03']);
        $sheet->SetCellValue('I'.$fila, $item['aux04']);
        $sheet->SetCellValue('J'.$fila, $item['aux05']);
        $sheet->SetCellValue('K'.$fila, $item['auxReten']);
        $sheet->SetCellValue('L'.$fila, $item['kmInicio']);
        $fila++;
      }

      // get the content
      @ob_start();
      $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
      $writer->save("php://output");
      $data = @ob_get_contents();
      @ob_end_clean();
       

      //ob_end_clean();
      $from = "alertas@inversionesmoy.com.pe"; 
      $subject = "Envío carga de Despachos"; 
      $message = "Se le envían los datos que usted ha validado y subido al sistema<br>
      <b>Favor, ver adjunto</b>"; 
      $email   = $_SESSION['email'];

      // a random hash will be necessary to send mixed content
      $separator = md5(time());

      // carriage return type (we use a PHP end of line constant)
      $eol = PHP_EOL;

      // attachment name
      /*$filename = "planilla$dni-$mes-$anhio.pdf";
      $pdfdoc = $pdf->Output("", "S");*/

      $mail = new PHPMailer();
      $mail->IsSMTP();
      $mail->SMTPAuth = true;

      $mail->Host = "localhost";
      $mail->Username = "alertas@inversionesmoy.com.pe";
      $mail->Password = "Alertas20297421035";

      $mail->From = "alertas@inversionesmoy.com.pe";
      $mail->FromName = "Alertas";

      $mail->Subject = "Envío Carga de Despachos";
      $mail->AddAddress($email,"carga");

      //$mail->AddStringAttachment($pdfdoc, $filename);
      $mail->AddStringAttachment($data, $nombreNuevo.'.xlsx');
      $mail->MsgHTML($message);

      if(!$mail->Send()) {
        echo "Error, no se realizó el envío: " . $mail->ErrorInfo;
      } else {
        echo "Mensaje Enviado!";
      }
      return "<b>BIEN</b>. Proceso culminado satisfactoriamente<br/>";


    } else {
      return "<b>ERROR</b>. Se renombró el archivo, pero no se pudo registrar en el panel<br/>";
    }
  } else {
    return "<b>ERROR</b>. No se pudo renombrar la tabla, ya ha procesado este cliente el día de hoy con su usuario<br/>";
  }

}

function buscarUltimasCargasDespachos($db){
  $consulta = $db->prepare("SELECT `idCarga`,`_cargadespachospanel`.`fchCreacion`, `creador`, `idCliente`, concat(`idCliente`,' ',nombre) AS cliente, `hraSubido`, `fchAlSistema`, `hraAlSistema`, `usuAlSistema`, `_cargadespachospanel`.`estado` FROM `_cargadespachospanel`, cliente WHERE `_cargadespachospanel`.idCliente = `cliente`.idRuc ORDER BY `idCarga` DESC LIMIT 50 ");
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarItemsCarga($db,$nombreTabla){
  $consulta = $db->prepare("SELECT `nro`, `fchDespacho`, `hraDespacho`, `cuenta`, `placa`, `tempConductor`, `tempAux01`, `tempAux02`, `tempAux03`, `tempAux04`, `tempAux05`, `tempAuxReten`, `idCliente`, `conductor`, `aux01`, `aux02`, `aux03`, `aux04`, `aux05`, `auxReten`, `kmInicio`, `obsFch`,`obsCuenta`, `obsPlaca`, `obsConductor`, `obsAux01`, `obsAux02`, `obsAux03`, `obsAux04`, `obsAux05`, `obsAuxReten`, `obsKmInicio` FROM `$nombreTabla`");
  $consulta->execute();
  return $consulta->fetchAll();
}

function verificaPredictivo($db,$id){
  $limiteDiasRetrasoCarga = 10;
  $limiteKm = 300;
  $nro = $db->exec("UPDATE  `$id` AS tblData LEFT JOIN  predictivotrabajador ON `tblData`.conductor =  `predictivotrabajador`.idTrabajador AND  `tblData`.idCliente = `predictivotrabajador`.idCliente SET obsConductor = if (idTrabajador IS NULL AND conductor != '','No es predictivo' ,'');
UPDATE `$id` AS tblData LEFT JOIN  predictivotrabajador ON `tblData`.aux01 = `predictivotrabajador`.idTrabajador AND  `tblData`.idCliente = `predictivotrabajador`.idCliente SET obsAux01 = if (idTrabajador IS NULL AND aux01 != '','No es predictivo' ,'');
UPDATE `$id` AS tblData LEFT JOIN  predictivotrabajador ON `tblData`.aux02 = `predictivotrabajador`.idTrabajador AND  `tblData`.idCliente = `predictivotrabajador`.idCliente SET obsAux02 = if (idTrabajador IS NULL AND aux02 != '','No es predictivo' ,'');
UPDATE `$id` AS tblData LEFT JOIN  predictivotrabajador ON `tblData`.aux03 = `predictivotrabajador`.idTrabajador AND  `tblData`.idCliente = `predictivotrabajador`.idCliente SET obsAux03 = if (idTrabajador IS NULL AND aux03 != '','No es predictivo' ,'');
UPDATE `$id` AS tblData LEFT JOIN  predictivotrabajador ON `tblData`.aux04 = `predictivotrabajador`.idTrabajador AND  `tblData`.idCliente = `predictivotrabajador`.idCliente SET obsAux04 = if (idTrabajador IS NULL AND aux04 != '','No es predictivo' ,'');
UPDATE `$id` AS tblData LEFT JOIN  predictivotrabajador ON `tblData`.aux05 = `predictivotrabajador`.idTrabajador AND  `tblData`.idCliente = `predictivotrabajador`.idCliente SET obsAux05 = if (idTrabajador IS NULL AND aux05 != '','No es predictivo' ,'');
UPDATE `$id` AS tblData LEFT JOIN  predictivotrabajador ON `tblData`.auxReten = `predictivotrabajador`.idTrabajador AND  `tblData`.idCliente = `predictivotrabajador`.idCliente SET obsAuxReten = if (idTrabajador IS NULL AND auxReten != '','No es predictivo' ,'');
UPDATE `$id` AS tblData SET obsFch = if(( datediff(curdate(),`fchDespacho`) < $limiteDiasRetrasoCarga AND  datediff(curdate(),`fchDespacho`) >= 0),'','Fuera de Rango');
UPDATE `$id` AS tblData, vehiculo SET  obsKmInicio =  if(kmInicio = 'MALOGRADO','MALOGRADO',  if(kmInicio <= 0,'Falta Km Inicio', if(abs( kmUltimaMedicion - kmInicio) > $limiteKm,'Fuera de Rango' ,'') )  )  WHERE placa = `vehiculo`.idPlaca ;
");

}

function actualizarArchivoCarga($db){
  $idCarga = $_SESSION['idCarga'];
  $usuario = $_SESSION['usuario'];
  $actualiza = $db->prepare("UPDATE `_cargadespachospanel` SET `estado` = 'generado', fchAlSistema = curdate(), hraAlSistema = curtime(), usuAlSistema = :usuario, fchUltEstado = curdate(), usuUltEstado = :usuario WHERE `_cargadespachospanel`.`idCarga` = :idCarga");
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->bindParam(':idCarga',$idCarga);
  $actualiza->execute();
}

function eliminaTablaDeCargaDespachos($db,$tbl,$idCarga){
  //echo $tbl;
  $usuario = $_SESSION['usuario'];
  $nro = $db->exec("DROP TABLE $tbl");
  if ($nro !== false){
    $nro = $db->exec("UPDATE `_cargadespachospanel` SET `estado` = 'eliminado', fchUltEstado = curdate(), usuUltEstado = '$usuario'  WHERE idCarga = '$idCarga'");

    return "Se eliminaron datos<br/>";
  } else {
    return "No se eliminaron datos<br/>";
  }
}


function insertaRegistroAdelanto($db,$fchDespacho,$correlativo,$idTrabajador,$nroDoc){
  $usuario = $_SESSION['usuario'];
  $inserta = $db->prepare("INSERT INTO `despachopersonaladelanto` (`fchDespacho`, `correlativo`, `idTrabajador`, `nroDoc`, `creacUsuario`, `creacFch`) VALUES (:fchDespacho, :correlativo, :idTrabajador, :nroDoc, :usuario, now());");

  $inserta->bindParam(':fchDespacho',$fchDespacho);
  $inserta->bindParam(':correlativo',$correlativo);
  $inserta->bindParam(':idTrabajador',$idTrabajador);
  $inserta->bindParam(':nroDoc',$nroDoc);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();

  $actualiza = $db->prepare("UPDATE `despacho` SET observacion = concat(observacion,' ','Se adelanto pago a $idTrabajador ($nroDoc-$usuario) ') WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");

  //$actualiza = $db->prepare("UPDATE `despacho` SET observacion = concat(observacion,' ','Se adelanto pago a') WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");

  $actualiza->bindParam(':fchDespacho',$fchDespacho);
  $actualiza->bindParam(':correlativo',$correlativo);  
  $actualiza->execute();
}

function buscarGT($db,$fchDespacho,$correlativo){
  $consulta = $db->prepare("SELECT `guiaPorte` FROM `despachoguiaporte` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
  $consulta->bindParam(':correlativo',$correlativo);
  $consulta->execute();
  $resultado = $consulta->fetchAll();
  $cadena = "";

  foreach ($resultado as $item) {
    $cadena .= ($cadena !="")?" ":"";
    $cadena .= $item['guiaPorte'];
  }
  return $cadena;
}

function buscarTripulacion($db,$fchDespacho,$correlativo){
  $consulta = $db->prepare("SELECT concat(apPaterno,' ',apMaterno,', ',nombres) AS nombCompleto FROM `despachopersonal`, trabajador WHERE  tipoRol = 'Auxiliar' AND `despachopersonal`.idTrabajador = `trabajador`.idTrabajador  AND `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
  $consulta->bindParam(':correlativo',$correlativo);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarTiendas($db,$fchDespacho,$correlativo){
  $consulta = $db->prepare("SELECT `despacho`.`idCliente`, `cuenta`, `correlLocal`, `nombUbicacion`, `descripcion` FROM `despachoubicacion`, `despacho`, `clienteubicacion` WHERE `despacho`.`fchDespacho` = `despachoubicacion`.`fchDespacho` AND `despacho`.`correlativo` = `despachoubicacion`.`correlativo` AND `clienteubicacion`.`idCliente` = `despacho`.`idCliente` AND `clienteubicacion`.`correlativo` = `correlLocal` AND `despachoubicacion`.`fchDespacho` =  :fchDespacho AND `despachoubicacion`.`correlativo` = :correlativo");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
  $consulta->bindParam(':correlativo',$correlativo);
  $consulta->execute();
  return $consulta->fetchAll();
}


function insertaDespUbicacion($db,$fchDespacho,$correlativo,$ubicacion){
  $usuario = $_SESSION['usuario'];
  $inserta = $db->prepare("INSERT INTO `despachoubicacion` (`fchDespacho`,`correlativo`,`correlLocal`,`usuario`,`fchCreacion`) VALUES (:fchDespacho, :correlativo, :ubicacion, :usuario, curdate())");
  $inserta->bindParam(':fchDespacho',$fchDespacho);
  $inserta->bindParam(':correlativo',$correlativo);
  $inserta->bindParam(':ubicacion',$ubicacion);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();
  return $inserta->rowCount();
}

function eliminaDespUbicaciones($db,$fchDespacho,$correlativo){
  $elimina = $db->prepare("DELETE FROM despachoubicacion WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
  $elimina->bindParam(':fchDespacho',$fchDespacho);
  $elimina->bindParam(':correlativo',$correlativo);
  $elimina->execute();
  return $elimina->rowCount();
}

function buscarDespUbicaciones($db,$fchDespacho,$correlativo){
  $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `correlLocal` FROM `despachoubicacion` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
  $consulta->bindParam(':correlativo',$correlativo);
  $consulta->execute();
  return $consulta->fetchAll();
}

function insertaPrestSustento($db,$nroDoc,$tipoDoc,$docum){
  $usuario = $_SESSION['usuario'];
  $inserta = $db->prepare("INSERT INTO `prestsustento` (`codigo`, `tipoDoc`, `nroDocum`, `creacUsuario`, `creacFch`) VALUES (:codigo, :tipoDoc, :nroDocum, :usuario, now());");
  $inserta->bindParam(':codigo',$nroDoc);//Cuidado con el cruce
  $inserta->bindParam(':tipoDoc',$tipoDoc);
  $inserta->bindParam(':nroDocum',$docum);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();
}

function buscarOtrosConceptosCobrar($db, $estado = NULL){
  if ($estado == NULL) $where = ""; else $where = " AND estadoCobrarOtros = 'Activo' ";  
  $consulta = $db->prepare("SELECT `idConcepto`, `concepto`, `creacUsuario`, `creacFch` FROM `despachocobrarotros` WHERE 1 $where");
  $consulta->execute();
  return $consulta->fetchAll();
}

function eliminaDetallesPorCobrar($db,$fchDespacho,$correlativo,$codigo){
  $elimina = $db->prepare("DELETE FROM `despachodetallesporcobrar` WHERE  `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `codigo` = :codigo");
  $elimina->bindParam(':fchDespacho',$fchDespacho);
  $elimina->bindParam(':correlativo',$correlativo);
  $elimina->bindParam(':codigo',$codigo);
  $elimina->execute();

  if ($elimina->rowCount() == 1){
      $descripcion = "Se eliminó registro en despachodetallesporcobrar fchDespacho $fchDespacho, correlativo $correlativo, código $codigo";
      logAccionDespacho($db,$descripcion,NULL,NULL);
  }

  return $elimina->rowCount();
}


function buscarRegistrosPorCobrar($db,$fchDespacho,$correlativo){ 
  $consulta = $db->prepare("SELECT `codigo`,  `docCobranza`, `tipoDoc`, `pagado`  FROM `despachodetallesporcobrar` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
  $consulta->bindParam(':correlativo',$correlativo);
  $consulta->execute();
  return $consulta->fetchAll();
}

function modificarRegCobranza($db,$fchDespacho,$correlativo,$codigo,$nvoTipoDoc,$nvoNroDoc,$nvoEstadoDoc,$nvoRuc,$nvoNombCLi,$nvoDirCLi){
  //echo "CODIGO 1: $codigo";
  $codigo = utf8_decode($codigo);
  //echo "CODIGO 2: $codigo";
  //echo "fchDespacho $fchDespacho, correlativo $correlativo, codigo $codigo, nvoTipoDoc $nvoTipoDoc,nvoNroDoc $nvoNroDoc,nvoEstadoDoc $nvoEstadoDoc";
  $actualiza = $db->prepare("UPDATE `despachodetallesporcobrar` SET `tipoDoc` = :nvoTipoDoc, docCobranza = :nvoNroDoc, pagado = :nvoEstadoDoc WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `codigo` = :codigo");

  $actualiza->bindParam(':fchDespacho',$fchDespacho);
  $actualiza->bindParam(':correlativo',$correlativo);
  $actualiza->bindParam(':codigo',$codigo);
  $actualiza->bindParam(':nvoTipoDoc',$nvoTipoDoc);
  $actualiza->bindParam(':nvoNroDoc',$nvoNroDoc);
  $actualiza->bindParam(':nvoEstadoDoc',$nvoEstadoDoc);
  $actualiza->execute();
  return $actualiza->rowCount();

}

function crearDocumCobranza($db, $idCliente,$nombCliente, $nvoTipoDoc, $nvoNroDoc, $estado, $nvoFchDoc, $docCobranza, $tipoDoc, $fchCreacion){
  $usuario = $_SESSION['usuario'];
  //echo "$usuario,$idCliente, $nvoTipoDoc, $nvoNroDoc, $estado, $nvoFchDoc, $docCobranza, $tipoDoc, $fchCreacion";
  $inserta = $db->prepare("INSERT INTO `doccobranza` (`docCobranza`, `tipoDoc`, `estado`, `usuario`, `fchEmitida`,`cobIdCliente`,`nombCliente`,`fchCreacion`, `hraCreacion`) VALUES (:nvoNroDoc, :nvoTipoDoc, :estado, :usuario, :nvoFchDoc, :idCliente, :nombCliente, CURDATE(), CURTIME())"); 
  $inserta->bindParam(':nvoNroDoc',$nvoNroDoc);
  $inserta->bindParam(':nvoTipoDoc',$nvoTipoDoc);
  $inserta->bindParam(':nvoFchDoc',$nvoFchDoc);
  $inserta->bindParam(':estado',$estado);
  $inserta->bindParam(':idCliente',$idCliente);
  $inserta->bindParam(':nombCliente',$nombCliente);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();

  if ($tipoDoc == 'Preliquidacion' ){
    $actualiza = $db->prepare("UPDATE `doccobranza` SET `preliquidacion` = :docCobranza , `fchPreliquid` = :fchCreacion WHERE `doccobranza`.`docCobranza` = :nvoNroDoc AND `doccobranza`.`tipoDoc` = :nvoTipoDoc");
    $actualiza->bindParam(':docCobranza',$docCobranza);
    $actualiza->bindParam(':fchCreacion',$fchCreacion);
    $actualiza->bindParam(':nvoNroDoc',$nvoNroDoc);
    $actualiza->bindParam(':nvoTipoDoc',$nvoTipoDoc);
    $actualiza->execute();
  }
  return $inserta->rowCount();
}

function buscarQuincenas($db,$fchIni = NULL, $fchFin = NULL){
  if ($fchIni != NULL && $fchFin != NULL)     $where = "  WHERE `quincena` BETWEEN :fchIni AND :fchFin ";
  else $where = " ORDER BY `quincena` DESC LIMIT 24 ";

  $consulta = $db->prepare("SELECT `quincena` FROM `quincena` $where");
  if ($fchIni != NULL && $fchFin != NULL){
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->bindParam(':fchFin',$fchFin);
  }
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarDiasTrabPorQuincena($db, $fchIni, $fchFin,  $itemQuincenas){
  $cadQry = " SELECT quincenadetalle.idTrabajador, concat(apPaterno,' ',apMaterno,' ',nombres) AS nombCompleto,  ";
  $principio = "Si";

  $cadParte01 = $cadParte02 = "";
  foreach ($itemQuincenas as $item){
    $fchQuincena = $item['quincena'];
    if ($principio == 'Si') {
      $principio = 'No';
    } else {
      $cadParte01 .= ",";
      $cadParte02 .= ",";
    }
    $cadParte01 .= " sum(if(quincena='$fchQuincena',if(categoria ='Ef', 1,if( categoria = '5ta', 2, 3 ) ), null)) AS `cat$fchQuincena`, `desp$fchQuincena`, `dias$fchQuincena` ";

    $cadParte02 .= " sum( if (`fchPago`= '$fchQuincena',1,0)) AS `desp$fchQuincena` , count(DISTINCT  if (`fchPago`= '$fchQuincena', fchDespacho, NULL) ) AS `dias$fchQuincena` ";
  }

  $cadCompleta = $cadQry.$cadParte01." FROM (SELECT `idTrabajador`, ".$cadParte02." FROM `despachopersonal` WHERE  `fchPago` BETWEEN '$fchIni' AND '$fchFin' GROUP BY idTrabajador ) As t2, `quincenadetalle`, trabajador  WHERE  trabajador.idTrabajador = t2.idTrabajador AND trabajador.idTrabajador = quincenadetalle.idTrabajador AND  quincena BETWEEN '$fchIni' AND '$fchFin' GROUP BY idTrabajador ORDER BY apPaterno, apMaterno, nombres ";

  $consulta = $db->prepare($cadCompleta);
  $consulta->execute();
  return $consulta->fetchAll();

}

function buscarDesPuntos($db,$fchDespacho,$correlativo){
  $consulta = $db->prepare("SELECT `idPunto`, `idProgramacion`, `idRuta`, `ordenPunto`, `fchDespacho`, `correlativo`, `correlPunto`, `tipoPunto`, `nombComprador`, `idDistrito`, `distrito`, `provincia`, `direccion`, `nroGuiaPorte`, `guiaCliente`, `guiaMoy`, `estado`, `subEstado`, `hraLlegada`, `hraSalida`, `observacion`, `fchPunto`, `referencia`, `telfReferencia`, `idCarga`, `observCargaPunto`, `adic_01`, `adic_02`, `adic_03`, `foto_1`, `foto_2`, `foto_3`, `foto_4`, `punto`, `placa`, `km`, `creacFch`, `creacUsuario`, `editaFch`, `editaUsuario` FROM `despachopuntos` WHERE fchDespacho = :fchDespacho AND correlativo = :correlativo ORDER BY `ordenPunto`  ASC ");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
  $consulta->bindParam(':correlativo',$correlativo);
  $consulta->execute();
  return $consulta->fetchAll();

}

function guardaRegistroDespachoPuntos($db,$fchDespacho,$correlativo, $correlPunto, $tipoPunto, $nombComprador, $distrito, $direccion, $nroGuiaPorte, $estado, $hraLlegada, $hraSalida, $observacion, $foto_1, $foto_2){

  $inserta = $db->prepare("INSERT INTO `despachopuntos` (`fchDespacho`, `correlativo`, `correlPunto`, `tipoPunto`, `nombComprador`, `distrito`, `direccion`, `nroGuiaPorte`, `estado`, `hraLlegada`, `hraSalida`, `observacion`, `foto_1`, `foto_2`, `creacFch`) VALUES (:fchDespacho, :correlativo, :correlPunto, :tipoPunto, :nombComprador, :distrito, :direccion, :nroGuiaPorte, :estado, :hraLlegada, :hraSalida, :observacion, :foto_1, :foto_2, CURRENT_DATE())");
    $inserta->bindParam(':fchDespacho',$fchDespacho);
    $inserta->bindParam(':correlativo',$correlativo);
    $inserta->bindParam(':correlPunto',$correlPunto);
    $inserta->bindParam(':tipoPunto',$tipoPunto);
    $inserta->bindParam(':nombComprador',$nombComprador);
    $inserta->bindParam(':distrito',$distrito);
    $inserta->bindParam(':direccion',$direccion);
    $inserta->bindParam(':nroGuiaPorte',$nroGuiaPorte);
    $inserta->bindParam(':estado',$estado);
    $inserta->bindParam(':hraLlegada',$hraLlegada);
    $inserta->bindParam(':hraSalida',$hraSalida);
    $inserta->bindParam(':observacion',$observacion);
    $inserta->bindParam(':foto_1',$foto_1);
    $inserta->bindParam(':foto_2',$foto_2);
    $inserta->execute();
    return $inserta->rowCount();

}

function addNroGuia($db,$fchDespacho,$correlativo, $tipoPunto, $nroGuiaPorte){
  $usuario = $_SESSION['usuario'];
  if ($tipoPunto == 'descarga'){
    $actualiza = $db->prepare("UPDATE `despacho` SET `guiaCliente` = concat(guiaCliente,' ', '$nroGuiaPorte' ) WHERE `fchDespacho` = :FchDespacho AND `correlativo` = :correlativo");
    $actualiza->bindParam(':fchDespacho',$fchDespacho);
    $actualiza->bindParam(':correlativo',$correlativo);
    $actualiza->execute();
  } else if ($tipoPunto == 'carga'){
    $arrAux = explode(" ", $nroGuiaPorte);

    foreach ($arrAux as $key => $guia) {
      $actualiza = $db->prepare("INSERT INTO `despachoguiaporte` (`guiaPorte`, `fchDespacho`, `correlativo`, `usuario`, `fchCreacion`) VALUES (:nroGuiaPorte, :fchDespacho, :correlativo, :usuario , curdate());");
      $actualiza->bindParam(':fchDespacho',$fchDespacho);
      $actualiza->bindParam(':correlativo',$correlativo);
      $actualiza->bindParam(':nroGuiaPorte',$guia);
      $actualiza->bindParam(':usuario',$usuario);
      $actualiza->execute();
    }
  }
}


function cobrosDocumentosCobranzaCalculado($db,$idCliente,$estado,$docCobranza = NULL, $tipoDoc = NULL){
  $whereEstado = " AND `doccobranza`.estado = '$estado' ";
  $where = "";
  if($estado == 'Todos')  $whereEstado = "";
  if ($docCobranza != NULL) $where .= " AND  `doccobranza`.`docCobranza` = :docCobranza ";
  if ($tipoDoc != NULL) $where .= " AND  `doccobranza`.`tipoDoc` = :tipoDoc ";

  //$consulta = $db->prepare("SELECT `doccobranza`.`docCobranza`, `doccobranza`.`tipoDoc`,`doccobranza`.`fchCreacion`,  count(`despachodetallesporcobrar`.`docCobranza`) as cantidad , sum(costoUnit * cantidad) AS montoTot , doccobranza.`estado`,  docnotas.`docCobranza` as docNota, docnotas.`tipoDoc` as tipoNota , docnotas.`monto` as montoNota, fchEmitida, fchPresentada, fchCancelada, `doccobranza`.preliquidacion, `doccobranza`.fchPreliquid, fchDetraccion, `doccobranza`.detraccion, `doccobranza`.tipoDetraccion, `doccobranza`.constanciaDetraccion, `doccobranza`.nroOperacion, t1.montoPagado , if(t1.montoPagado IS NULL,  sum(costoUnit * cantidad),  sum(costoUnit * cantidad) -t1.montoPagado)  AS resto     FROM  `despachodetallesporcobrar`, `doccobranza` LEFT JOIN docnotas ON `doccobranza`.`docCobranza` = docnotas.docRelacionado AND `doccobranza`.`tipoDoc` = 'Factura' LEFT JOIN (SELECT docCobranza, tipoDoc, sum(montoPagado) AS montoPagado FROM doccobr_rel_ingbco GROUP BY docCobranza, tipoDoc ) AS t1 ON t1.docCobranza = doccobranza.docCobranza AND t1.tipoDoc = doccobranza.tipoDoc , `despacho` WHERE `doccobranza`.`docCobranza` = `despachodetallesporcobrar`.`docCobranza` AND `doccobranza`.`tipoDoc` = `despachodetallesporcobrar`.`tipoDoc` AND `despacho`.`fchDespacho` =  `despachodetallesporcobrar`.`fchDespacho` AND `despacho`.`correlativo` =  `despachodetallesporcobrar`.`correlativo`   $whereEstado $where  AND `despacho`.`idCliente` = :idCliente  GROUP BY `doccobranza`.`docCobranza`, `doccobranza`.`tipoDoc` HAVING resto > 0.5 ORDER BY `doccobranza`.`fchCreacion` DESC");

  //$consulta = $db->prepare("SELECT `doccobranza`.`docCobranza`, `doccobranza`.`tipoDoc`,`doccobranza`.`fchCreacion`,  count(`despachodetallesporcobrar`.`docCobranza`) as cantidad , sum(costoUnit * cantidad) AS montoTot, doccobranza.`estado`, docnotas.`docCobranza` as docNota, docnotas.`tipoDoc` as tipoNota , docnotas.`monto` as montoNota, fchEmitida, fchPresentada, fchCancelada, `doccobranza`.preliquidacion, `doccobranza`.fchPreliquid, fchDetraccion, `doccobranza`.detraccion, `doccobranza`.tipoDetraccion, `doccobranza`.constanciaDetraccion, `doccobranza`.nroOperacion, t1.montoPagado , if(t1.montoPagado IS NULL, sum(costoUnit * cantidad), sum(costoUnit * cantidad) -t1.montoPagado)  AS resto  FROM  `despachodetallesporcobrar`, `doccobranza` LEFT JOIN docnotas ON `doccobranza`.`docCobranza` = docnotas.docRelacionado AND `doccobranza`.`tipoDoc` = 'Factura' LEFT JOIN (SELECT docCobranza, tipoDoc, sum(montoPagado) AS montoPagado FROM doccobr_rel_ingbco GROUP BY docCobranza, tipoDoc ) AS t1 ON t1.docCobranza = doccobranza.docCobranza AND t1.tipoDoc = doccobranza.tipoDoc , `despacho` WHERE `doccobranza`.`idPreliquid` = `despachodetallesporcobrar`.`idPreliquid` AND `despacho`.`fchDespacho` =  `despachodetallesporcobrar`.`fchDespacho` AND `despacho`.`correlativo` =  `despachodetallesporcobrar`.`correlativo`   $whereEstado $where  AND `despacho`.`idCliente` = :idCliente  GROUP BY `doccobranza`.`docCobranza`, `doccobranza`.`tipoDoc` HAVING resto > 0.5 ORDER BY `doccobranza`.`fchCreacion` DESC");

    $consulta = $db->prepare("SELECT `doccobranza`.`docCobranza`, `doccobranza`.`tipoDoc`,`doccobranza`.`fchCreacion`,  count(`despachodetallesporcobrar`.`docCobranza`) as cantidad ,  `doccobranza`.fchDocCobranza, `doccobranza`.mntDoc AS montoTot, `doccobranza`.`estado`, `docnotas`.`docCobranza` as docNota, `docnotas`.`tipoDoc` as tipoNota , `docnotas`.`monto` as montoNota, fchEmitida, fchPresentada, fchCancelada, `doccobranza`.preliquidacion, `doccobranza`.fchPreliquid, fchDetraccion, `doccobranza`.detraccion, `doccobranza`.tipoDetraccion, `doccobranza`.constanciaDetraccion, `doccobranza`.nroOperacion, t1.montoPagado , if(t1.montoPagado IS NULL, sum(costoUnit * cantidad), sum(costoUnit * cantidad) -t1.montoPagado)  AS resto  FROM  `despachodetallesporcobrar`, `doccobranza` LEFT JOIN docnotas ON `doccobranza`.`docCobranza` = docnotas.docRelacionado AND `doccobranza`.`tipoDoc` = 'Factura' LEFT JOIN (SELECT docCobranza, tipoDoc, sum(montoPagado) AS montoPagado FROM doccobr_rel_ingbco GROUP BY docCobranza, tipoDoc ) AS t1 ON t1.docCobranza = `doccobranza`.docCobranza AND t1.tipoDoc = `doccobranza`.tipoDoc , `despacho` WHERE `doccobranza`.`idPreliquid` = `despachodetallesporcobrar`.`idPreliquid` AND `despacho`.`fchDespacho` =  `despachodetallesporcobrar`.`fchDespacho` AND `despacho`.`correlativo` =  `despachodetallesporcobrar`.`correlativo`   $whereEstado $where  AND `despacho`.`idCliente` = :idCliente  GROUP BY `doccobranza`.`docCobranza`, `doccobranza`.`tipoDoc` HAVING resto > 0.5 ORDER BY `doccobranza`.`fchCreacion` DESC");

  $consulta->bindParam(':idCliente',$idCliente);
  if ($docCobranza != NULL ) $consulta->bindParam(':docCobranza',$docCobranza);
  if ($tipoDoc != NULL ) $consulta->bindParam(':tipoDoc',$tipoDoc);
  $consulta->execute();
  return $consulta->fetchAll(); 
};

function generaCorrelDesp($db, $fchDespacho){
  $consulta = $db->prepare("SELECT `correlativo`  FROM `despacho` WHERE `fchDespacho` = :fchDespacho order by `correlativo` desc limit 1");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
  $consulta->execute();
  foreach ($consulta as $arrayCorrelativo){
    $correlativo = $arrayCorrelativo['correlativo'] + 1;
  };
  if(!isset($correlativo)) $correlativo = 1;//se ejecuta para el primer registro del día
  return $correlativo;
}


function insertTripulac($db, $fchDespacho, $correlativo, $idTrabajador, $valorRol, $tipoRol, $esReten = "No" ){
  $inserta = $db->prepare("INSERT INTO despachopersonal (fchDespacho, correlativo, idTrabajador, valorRol, tipoRol, esReten) VALUES ( :fchDespacho, :correlativo,  :idTrabajador, :valorRol, :tipoRol, :esReten)");
  $inserta->bindParam(':fchDespacho',$fchDespacho);
  $inserta->bindParam(':correlativo',$correlativo);
  $inserta->bindParam(':idTrabajador',$idTrabajador);
  $inserta->bindParam(':valorRol',$valorRol);
  $inserta->bindParam(':tipoRol',$tipoRol);
  $inserta->bindParam(':esReten',$esReten);
  $inserta->execute();
  return $inserta->rowCount();

}
//function insertaDespachoYTripulacion($db, $porcIgv, $terceroConPersonalMoy, $usaReten, $placa, $dataCuenta, $dataPost){
function insertaDespachoYTripulacion($db, $porcIgv, $terceroConPersonalMoy, $usaReten, $placa, $dataPost){
  $usuario = $_SESSION["usuario"];

  $hraDespacho = $dataPost['txtHraDespacho'];
  $conductor = $dataPost['cmbConductor'];
  //$auxReten = explode("-",$usaReten);
  //$usaReten = $auxReten['0'];
  $cliente = $dataPost['cmbCliente'];
  $idCliente = strtok($cliente, ':');
  $usaRetenAux = ""; //Para mantener compatibilidad
  $fchDespacho = $dataPost['txtFchDespacho'];
  $fchDespachoFin = $dataPost['txtFchDespacho'];
  $correlativo = generaCorrelDesp($db, $fchDespacho);
  $auxCuenta = $dataPost['cmbCuenta'];
  $correlCuenta = strtok($auxCuenta, ':');
  $cuenta = strtok(':');
  $tipoCuenta = $dataPost['tipoCuenta'];

  $sinValor    = "";
  $fchCreacion = Date("Y-m-d");
  $arrAuxiliares = array();
  $nroAuxiliares = 0;
  $auxAuxiliares = $dataPost['txtAuxiliares'];
  $auxAuxiliares = preg_replace( '/\n/', '@', $auxAuxiliares );
  //echo $auxAuxiliares;
    
  $dataAuxiliar = strtok($auxAuxiliares, "@");
  while ( $dataAuxiliar != "") {
    $nroAuxiliares++;
    //$dni = substr($dataAuxiliar, 0 , 8);
    $dni = substr($dataAuxiliar,0, strpos($dataAuxiliar, "-"));
    $arrAuxiliares[] = $dni;
    $dataAuxiliar = strtok("@");
  }

/*  $nombreCuenta = $item['nombreCuenta'];  */

  $inserta = $db->prepare("INSERT INTO `despacho` (`fchDespacho`, `correlativo`, `hraInicio`, `hraInicioBase`,  `placa`, fchDespachoFin, fchDespachoFinCli,  `idCliente`, `cuenta`,  `correlCuenta`,  `nroAuxiliares`,   `usaReten`, `kmInicio`, `kmInicioCliente`,  `ptoOrigen`, `tipoDestino`, `ptoDestino`, `pagado`, `pagoEnPlanilla`, `terceroConPersonalMoy`, `usuario`, `fchCreacion`, `modoCreacion`, `tipoServicioPago`) VALUES (:fchDespacho, :correlativo, :hraDespacho, :hraDespacho, :placa,  :fchDespachoFin, :fchDespachoFinCli,  :idCliente, :cuenta, :correlCuenta, :nroAuxiliares,  :usaReten, :kmInicio, :kmInicioCliente,  :ptoOrigen, :tipoDestino, :ptoDestino, 'No', 'No', :terceroConPersonalMoy, :usuario, :fchCreacion, 'Nuevo Despacho', :tipoCuenta)");
  $inserta->bindParam(':fchDespacho',$fchDespacho);
  $inserta->bindParam(':correlativo',$correlativo);
  $inserta->bindParam(':hraDespacho',$hraDespacho);
  $inserta->bindParam(':placa',$placa);
  $inserta->bindParam(':idCliente',$idCliente);
  $inserta->bindParam(':cuenta',$cuenta);
  $inserta->bindParam(':correlCuenta',$correlCuenta);
  $inserta->bindParam(':nroAuxiliares',$nroAuxiliares);
  $inserta->bindParam(':usaReten',$usaRetenAux);
  $inserta->bindParam(':kmInicio',$sinValor);
  $inserta->bindParam(':kmInicioCliente',$sinValor);
  $inserta->bindParam(':ptoOrigen',$sinValor);
  $inserta->bindParam(':tipoDestino',$sinValor);
  $inserta->bindParam(':ptoDestino',$sinValor);
  $inserta->bindParam(':terceroConPersonalMoy',$terceroConPersonalMoy);  
  $inserta->bindParam(':usuario',$usuario);
  $inserta->bindParam(':fchCreacion',$fchCreacion);
  $inserta->bindParam(':fchDespachoFin',$fchDespacho);
  $inserta->bindParam(':fchDespachoFinCli',$fchDespacho);
  $inserta->bindParam(':tipoCuenta',$tipoCuenta);

  $inserta->execute();
  
  $cantDespacho = $inserta->rowCount();

  //echo "Cantidad : $cant";
  $cantTrip = $valConductor = $valAuxiliar = 0;
  if ($cantDespacho == 1){
    if ($tipoCuenta != 'SoloPersonal'){
      $cantTrip = insertTripulac($db, $fchDespacho, $correlativo, $conductor, $valConductor, 'Conductor' );
    }

    foreach ($arrAuxiliares as $key => $value) {
      $cantTrip = insertTripulac($db, $fchDespacho, $correlativo, $value, $valAuxiliar, 'Auxiliar' );
    }

    if ($usaReten != "No"){
      $cantTrip = insertTripulac($db, $fchDespacho, $correlativo, $usaReten, $valAuxiliar, 'Auxiliar', 'Si');
    }
  }

  return $cantDespacho + $cantTrip;
}

function editaDespachoYTripulacion($db, $porcIgv, $terceroConPersonalMoy, $usaReten, $placa, $dataCuenta, $dataPost){
  $usuario = $_SESSION["usuario"];
  $hraDespacho = $dataPost['txtHraDespacho'];
  $conductor = $dataPost['cmbConductor'];
  //$auxReten = explode("-",$usaReten);
  //$usaReten = $auxReten['0'];
  $cliente = $_POST['cmbCliente'];
  $idCliente = strtok($cliente, ':');
  $idProducto = $_POST['cmbCuenta'];
  $fchDespacho = $dataPost['txtFchDespacho'];
  $fchDespachoFin = $dataPost['txtFchDespacho'];
  $correlativo = generaCorrelDesp($db, $fchDespacho);

  foreach ($dataCuenta as $item){
    $nombreCuenta = $item['nombreCuenta'];
    $m3Facturable = $item['m3Facturable'];
    $puntos  =  $item['puntos'];
    $zona = $item['zona'];
    $precioServ = $item['precioServ'];
    $kmEsperado = $item['kmEsperado'];
    $tolerKmEsperado = $item['tolerKmEsperado'];
    $valKmAdic = $item['valKmAdic'];
    $hrasNormales  =  $item['hrasNormales'];
    $tolerHrasNormales = $item['tolerHrasNormales'];
    $valHraAdic = $item['valHraAdic'];
    $hraIniEsperado = $item['hraIniEsperado'];
    $valAdicHraIniEsper = $item['valAdicHraIniEsper'];
    $hraFinEsperado = $item['hraFinEsperado'];
    $valAdicHraFinEsper = $item['valAdicHraFinEsper'];
    $nroAuxiliaresCuenta  =  $item['nroAuxiliares'];
    $valAuxiliarAdic = $item['valAuxiliarAdic'];
    $cobrarPeaje = $item['cobrarPeaje'];
    $cobrarRecojoDevol = $item['cobrarRecojoDevol'];
    $valConductor = $item['valConductor'];
    $valAuxiliar = $item['valAuxiliar'];
    $usarMaster = $item['usoMaster'];
    $valUnidTercCCond  =  $item['valUnidTercCCond'];
    $valUnidTercSCond = $item['valUnidTercSCond'];
    $hrasNormalTerc = $item['hrasNormalTerc'];
    $tolerHrasNormalTerc = $item['tolerHrasNormalTerc'];
    $valHraExtraTerc = $item['valHraExtraTerc'];
    $valKmAdicTerc = $item['valKmAdicTerc'];

    //echo "IdCLiente $idCliente   CUENTA  $cuenta  TOPE   $topeServicioHraNormal";
  }

  $igvServicio = $precioServ * $porcIgv;
  $sinValor    = "";
  $fchCreacion = Date("Y-m-d");
  $arrAuxiliares = array();
  $nroAuxiliares = 0;
  $auxAuxiliares = $_POST['txtAuxiliares'];
  $auxAuxiliares = preg_replace( '/\n/', '@', $auxAuxiliares );
  //echo $auxAuxiliares;
    
  $dataAuxiliar = strtok($auxAuxiliares, "@");
  while ( $dataAuxiliar != "") {
    $nroAuxiliares++;
    $dni = substr($dataAuxiliar, 0 , 8);
    //echo "*$dni*";
    $arrAuxiliares[] = $dni;
    $dataAuxiliar = strtok("@");
  }



  $nroAuxiliaresAdic = ($nroAuxiliares > $nroAuxiliaresCuenta)?$nroAuxiliares - $nroAuxiliaresCuenta:0;



  $inserta = $db->prepare("REPLACE INTO `despacho` (`fchDespacho`, `correlativo`, `hraInicio`,  `placa`, `m3`, fchDespachoFin, `valorServicio`, `igvServicio`, `idCliente`, `cuenta`,  `nroAuxiliares`, `nroAuxiliaresAdic`, `topeServicioHraNormal`, `costoHraExtra`, `valorConductor`, `valorAuxiliar`, `nroAuxiliaresCuenta`, `valorAuxAdicional`, `usarMaster`,  `usaReten`, `kmInicio`, `kmInicioCliente`,  `recorridoEsperado`, `zonaDespacho`, `ptoOrigen`, `tipoDestino`, `ptoDestino`, `pagado`, `pagoEnPlanilla`, `terceroConPersonalMoy`, `usuario`, `fchCreacion`, `modoCreacion`, `idProducto`, `valKmAdic`, `hraIniEsperado`, `valAdicHraIniEsper`, `hraFinEsperado`, `valAdicHraFinEsper`, `cobrarPeaje`, `cobrarRecojoDevol`, `valUnidTercCCond`, `valUnidTercSCond`, `hrasNormalTerc`, `tolerHrasNormalTerc`, `valHraExtraTerc`, `valKmAdicTerc`, `tolerKmEsperado`) VALUES (:fchDespacho, :correlativo, :hraDespacho, :placa, :m3, :fchDespachoFin, :valorServicio, :igvServicio, :idCliente, :cuenta, :nroAuxiliares, :nroAuxiliaresAdic, :hrasNormales, :valHraAdic, :valConductor, :valAuxiliar, :nroAuxiliaresCuenta, :valAuxiliarAdic, :usarMaster, :usaReten, :kmInicio, :kmInicioCliente, :kmEsperado, :zona, :ptoOrigen, :tipoDestino, :ptoDestino, 'No', 'No', :terceroConPersonalMoy, :usuario, :fchCreacion, 'Nuevo Despacho', :idProducto, :valKmAdic, :hraIniEsperado, :valAdicHraIniEsper, :hraFinEsperado, :valAdicHraFinEsper, :cobrarPeaje, :cobrarRecojoDevol, :valUnidTercCCond, :valUnidTercSCond, :hrasNormalTerc, :tolerHrasNormalTerc, :valHraExtraTerc, :valKmAdicTerc, :tolerKmEsperado)");
  $inserta->bindParam(':fchDespacho',$fchDespacho);
  $inserta->bindParam(':correlativo',$correlativo);
  $inserta->bindParam(':hraDespacho',$hraDespacho);
  $inserta->bindParam(':placa',$placa);
  $inserta->bindParam(':m3',$m3Facturable);
  $inserta->bindParam(':valorServicio',$precioServ);
  $inserta->bindParam(':igvServicio',$igvServicio);
  $inserta->bindParam(':idCliente',$idCliente);
  $inserta->bindParam(':cuenta',$nombreCuenta);
  $inserta->bindParam(':nroAuxiliares',$nroAuxiliares);
  $inserta->bindParam(':nroAuxiliaresAdic',$nroAuxiliaresAdic);
  $inserta->bindParam(':hrasNormales',$hrasNormales);
  $inserta->bindParam(':valHraAdic',$valHraAdic);
  $inserta->bindParam(':valConductor',$valConductor);
  $inserta->bindParam(':valAuxiliar',$valAuxiliar);
  $inserta->bindParam(':nroAuxiliaresCuenta',$nroAuxiliaresCuenta);
  $inserta->bindParam(':valAuxiliarAdic',$valAuxiliarAdic);
  $inserta->bindParam(':usarMaster',$usarMaster);
  $inserta->bindParam(':usaReten',$usaReten);
  $inserta->bindParam(':kmInicio',$sinValor);
  $inserta->bindParam(':kmInicioCliente',$sinValor);
  $inserta->bindParam(':kmEsperado',$kmEsperado);
  $inserta->bindParam(':zona',$zona);
  $inserta->bindParam(':ptoOrigen',$sinValor);
  $inserta->bindParam(':tipoDestino',$sinValor);
  $inserta->bindParam(':ptoDestino',$sinValor);
  $inserta->bindParam(':terceroConPersonalMoy',$terceroConPersonalMoy);  
  $inserta->bindParam(':usuario',$usuario);
  $inserta->bindParam(':fchCreacion',$fchCreacion);
  $inserta->bindParam(':idProducto',$idProducto);
  $inserta->bindParam(':valKmAdic',$valKmAdic);
  $inserta->bindParam(':hraIniEsperado',$hraIniEsperado);
  $inserta->bindParam(':valAdicHraIniEsper',$valAdicHraIniEsper);
  $inserta->bindParam(':hraFinEsperado',$hraFinEsperado);
  $inserta->bindParam(':valAdicHraFinEsper',$valAdicHraFinEsper);
  $inserta->bindParam(':cobrarPeaje',$cobrarPeaje);
  $inserta->bindParam(':cobrarRecojoDevol',$cobrarRecojoDevol);
  $inserta->bindParam(':valUnidTercCCond',$valUnidTercCCond);
  $inserta->bindParam(':valUnidTercSCond',$valUnidTercSCond);
  $inserta->bindParam(':hrasNormalTerc',$hrasNormalTerc);
  $inserta->bindParam(':tolerHrasNormalTerc',$tolerHrasNormalTerc);
  $inserta->bindParam(':valHraExtraTerc',$valHraExtraTerc);
  $inserta->bindParam(':valKmAdicTerc',$valKmAdicTerc);
  $inserta->bindParam(':tolerKmEsperado',$tolerKmEsperado);
  $inserta->bindParam(':fchDespachoFin',$fchDespacho);
  $inserta->execute();
  
  $cantDespacho = $inserta->rowCount();
  
  elimTripul($db,$fchDespacho, $correlativo);

  $cantTrip = 0;
  if ($cantDespacho == 1){
    if (substr($nombreCuenta,0,12) != 'SoloPersonal'){
      $cantTrip = insertTripulac($db, $fchDespacho, $correlativo, $conductor, $valConductor, 'Conductor' );
    }

    foreach ($arrAuxiliares as $key => $value) {
      //echo "Auxiliar  $value <br> ";
      $cantTrip = insertTripulac($db, $fchDespacho, $correlativo, $value, $valAuxiliar, 'Auxiliar' );
    }

    if ($usaReten != "No"){
      //echo "ingresó por aquí";
      $cantTrip = insertTripulac($db, $fchDespacho, $correlativo, $usaReten, $valAuxiliar, 'Auxiliar', 'Si');
    }
  }

  return $cantDespacho + $cantTrip;
}

function elimTripul($db,$fchDespacho, $correlativo){
  $elimina = $db->prepare("DELETE FROM despachopersonal WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
  $elimina->bindParam(':fchDespacho',$fchDespacho);
  $elimina->bindParam(':correlativo',$correlativo);
  $elimina->execute();
}

function buscarDespachoAnt($db, $fchDespacho, $hraInicio, $placa){
  $consulta = $db->prepare("SELECT `hraInicioBase`, `hraFin`, `kmInicio`, `kmInicioCliente`, `kmFinCliente`, `hraFinCliente`, `lugarFinCliente`, `kmFin` FROM `despacho` WHERE concat(`fchDespacho`,' ',hraInicio) <= '$fchDespacho $hraInicio' AND `placa` = :placa AND concluido = 'Si' ORDER BY fchDespacho DESC LIMIT 1 ");
  $consulta->bindParam(':placa',$placa);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarDespachosPorEstadoNoConcluido($db){
  $consulta = $db->prepare("SELECT fchDespacho, DATEDIFF(curdate(),fchDespacho) AS dif, estadoDespacho, `correlativo`, `idProgramacion`, `idCliente`, nombre ,`cuenta`, usuarioAsignado FROM despacho, cliente WHERE despacho.idCliente = cliente.idRuc AND NOT (estadoDespacho = 'Terminado' AND concluido = 'Si') ORDER BY FIELD (estadoDespacho,'Programado','EnRuta','Terminado') ASC  ");
  $consulta->execute();
  return $consulta->fetchAll();  
}




?>
