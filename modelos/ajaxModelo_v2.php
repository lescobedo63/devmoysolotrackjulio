<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  function generaNroVale($db){
    $consulta = $db->prepare("SELECT `nroDoc` FROM `ccmovimencab` WHERE tipoDoc = '00' AND nroDoc like 'Moy%' ORDER BY `idMovimEncab` DESC limit 1 ");
    $consulta->execute();
    $auxNro =  $consulta->fetchAll();
    foreach ($auxNro as $item) {
      $nro = $item['nroDoc'];
    }

    $consulta = $db->prepare("SELECT `nroDoc` FROM `ocurrencia` WHERE tipoDoc = '00' AND nroDoc like 'Moy%' ORDER BY `nroDoc` DESC limit 1 ");
    $consulta->execute();
    $auxNro =  $consulta->fetchAll();
    foreach ($auxNro as $item) {
      $nro2 = $item['nroDoc'];
    }
    if ($nro2 > $nro) $nro = $nro2;
    $anhioActual = date("Y");
    $inicioCadena = "Moy".$anhioActual."-";
    $inicioUltNro = substr($nro,0,8);
    $ultNro = substr($nro,-6);
    if ($inicioCadena != $inicioUltNro)
      $ultNro = 0;
    $auxSgte = $ultNro*1 + 1;

    $sgteNro = $inicioCadena.substr('00000'.$auxSgte,-6);
    return $sgteNro;
  };

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

    $actualiza = $db->prepare("UPDATE `ocurrencia` SET `descripcion` = replace(`descripcion`,'&#209;','�'), `descripcion` = replace(`descripcion`,'&#241;','�')  WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo  AND `tipoOcurrencia` = :tipoOcurrencia AND `fchCreacion` = curdate()");
    $actualiza->bindParam(':fchDespacho',$fchDespacho);
    $actualiza->bindParam(':correlativo',$correlativo);
    $actualiza->bindParam(':tipoOcurrencia',$tipoOcurrencia);
    $actualiza->execute();   
    sleep(5);
  };

  function generaMovilCorrOcurrencia($db, $fchDespacho, $correlativo ){
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

  function buscarInfoVehiculos($db,$idPlaca = NULL){
    $where = ($idPlaca == NULL)?"":" AND idPlaca = :idPlaca "; 
    $consulta = $db->prepare("SELECT `idPlaca`, `nroVehiculo`, `estado`, `propietario`, `rznSocial`, `dimLargo`, `dimAlto`, `dimAncho`, `dimInteriorLargo`, `dimInteriorAncho`, `dimInteriorAlto`, `m3Facturable`, `kmUltimaMedicion`   FROM `vehiculo` WHERE `estado` LIKE 'activo'  $where ");
    if ($idPlaca != NULL) $consulta->bindParam(':idPlaca',$idPlaca);
    $consulta->execute();
    return $consulta->fetchAll();
  }


  function insertaEnOcurrenciaTercero($db, $fchDespacho, $correlativo, $auxTipoOcurrencia, $observHraExtraTercero, $placa, $mntTotal){
    $usuario = $_SESSION["usuario"];
    $descripcion = "$auxTipoOcurrencia: $observHraExtraTercero $placa";
    $tipoOcurrencia = strtok($auxTipoOcurrencia, "|");
    $tipoConcepto =  strtok("|");
    $corrOcurrencia = generaMovilCorrOcurrencia($db, $fchDespacho, $correlativo );
    $datosVehiculo = buscarInfoVehiculos($db,$placa);
    foreach ($datosVehiculo as $key => $value) {
      $idTercero = $value['rznSocial'];
    }

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
  }

  function restahhmmss($inicio, $fin){
    if (strtotime($fin) > strtotime($inicio)){
      $dif=strtotime($fin) - strtotime($inicio) ;
      $hra = floor($dif/3600);
      $min = floor(($dif % 3600 )/60 );
      $hra = substr("00".$hra,-2);
      $min = substr("00".$min,-2);
      $dif = $hra.":".$min.":00";
    }
    else
      $dif = "00:00:00";  
    return $dif;
  }

  function esUnaHora($cadena){
    $arrPartes = explode(":",$cadena);
    if ((!isset($arrPartes[3]) ||  $arrPartes[3] == "") &&  
      is_numeric($arrPartes[0]) && is_numeric($arrPartes[1]) && is_numeric($arrPartes[2]) &&  
      ($arrPartes[0] == "00" OR (1*$arrPartes[0] > 0 AND 1* $arrPartes[0] <= 23))
       AND ( $arrPartes[1] == "00" OR (1*$arrPartes[1] > 0 AND 1* $arrPartes[1] <= 59))
     AND ( $arrPartes[2] == "00" OR (1*$arrPartes[2] > 0 AND 1* $arrPartes[2] <= 59))
    )
      return 1;
    else
      return 0;
  }

  function algunosDatosTrabajador($db, $idTrabajador){
    $consulta = $db->prepare("SELECT `idTrabajador`, `estadoTrabajador`, `categTrabajador`, `esMaster`, `precioMaster` FROM `trabajador` WHERE `idTrabajador` = '$idTrabajador' ");
    $consulta->execute();
    return $consulta->fetch();
  }
  //////////////////////

  function completarCalculosTrabajador($db, $fchDespacho, $correlativo, $idTrabajador, $tipoRol, $valorRol, $valorAdicional, $valorHraExtra, $hraTrabFin, $fchTrabFin, $usuario = 'UsuarioMovil'){
    $actualiza = $db->prepare("UPDATE `despachopersonal` SET `valorRol` = :valorRol, `valorAdicional` = :valorAdicional, `valorHraExtra` = :valorHraExtra, `trabFchDespachoFin` = :fchTrabFin, `trabHraFin` = :hraTrabFin, ultProcesoUsuario = :usuario, ultProceso= 'Desde trackmoy', ultProcesoFch = curdate() WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `idTrabajador` = :idTrabajador AND `tipoRol` = :tipoRol");
    $actualiza->bindParam(':valorRol',$valorRol);
    $actualiza->bindParam(':valorAdicional',$valorAdicional);
    $actualiza->bindParam(':valorHraExtra',$valorHraExtra);
    $actualiza->bindParam(':fchTrabFin',$fchTrabFin);
    $actualiza->bindParam(':hraTrabFin',$hraTrabFin);
    $actualiza->bindParam(':fchDespacho',$fchDespacho);
    $actualiza->bindParam(':correlativo',$correlativo);
    $actualiza->bindParam(':idTrabajador',$idTrabajador);
    $actualiza->bindParam(':tipoRol',$tipoRol);
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function difEnHorasDecim($hra1,$hra2){
    if ($hra2 > $hra1){ 
      $aux1 = explode(":",$hra1);
      $aux2 = explode(":",$hra2);
      $segundos1 = 3600*$aux1[0] + 60*$aux1[1] + $aux1[2];
      $segundos2 = 3600*$aux2[0] + 60*$aux2[1] + $aux2[2];

      $dif = ($segundos2 - $segundos1)/3600;
    } else
      $dif = 0;
    return $dif; 
  }

  function buscarInfoTripulacion($db, $fchDespacho, $correlativo){
    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, desper.`idTrabajador`, `valorRol`, `valorAdicional`, `valorHraExtra`, `trabFchDespachoFin`, `trabHraInicio`, `trabHraFin`, `tipoRol`, `pagado`, `fchPago`, `anhio`, `mes`, `fchDesmarca`, desper.`usuario`, `fchActualizacion`, `observPersonal`, `ultProceso`, `ultProcesoFch`, `ultProcesoUsuario`, concat(nombres,' ',apPaterno, ' ', apMaterno) AS nombCompleto, `trab`.esMaster, `trab`.precioMaster, `trab`.categTrabajador  FROM `despachopersonal` AS desper, trabajador AS trab WHERE `desper`.idTrabajador = `trab`.idTrabajador AND `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function insertaDetallesPorCobrar($db,$fchDespacho,$correlativo,$codigo,$ctoUnitario,$cantidad, $usuario = 'Desconocido', $observDetalle = null ,$arrData = NULL){
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
  }


  function buscarDataDespachoIdProgram($db,$idProgramacion){
    $consulta = $db->prepare("SELECT  `fchDespacho`, `correlativo`, `idProgramacion`, `guiaCliente`, `hraInicio`, `fchDespachoFinCli`, `fchDespachoFin`, `hraFin`, `placa`, `considerarPropio`, `m3`, `valorServicio`, `igvServicio`, `idCliente`, `cuenta`, `correlCuenta`, `tipoServicioPago`, `nroAuxiliares`, `nroAuxiliaresAdic`, `costo`, `topeServicioHraNormal`, `hrasNormales`, `tolerHrasNormales`, `costoHraExtra`, `toleranCobroHraExtra`, `valorConductor`, `hraNormCondDespacho`, `tolerHraCondDespacho`, `valHraAdicCondDespacho`, `valorAuxiliar`, `hraNormAuxDespacho`, `tolerHraAuxDespacho`, `valHraAdicAuxDespacho`, `nroAuxiliaresCuenta`, `valorAuxAdicional`, `usarMaster`, `tpoExtraHras`, `costoTotal`, `nroGuias`, `nroDespachos`, `usaReten`, `concluido`, `hraInicioBase`, `kmInicio`, `kmInicioCliente`, `kmFinCliente`, `hraFinCliente`, `lugarFinCliente`, `kmFin`, `recorridoEsperado`, `zonaDespacho`, `ptoOrigen`, `idOrigen`, `idSedeOrigen`, `tipoDestino`, `ptoDestino`, `observacion`, `observCliente`, `docCobranza`, `tipoDoc`, `pagado`, `pagoEnPlanilla`, `terceroConPersonalMoy`, `contarDespacho`, `superCuenta`, des.`usuario`, des.`fchCreacion`, `modoCreacion`, `estadoDespacho`, `usuarioGrabaFin`, `fchGrabaFin`, `hraGrabaFin`, `usuarioCreaCobranza`, `fchCreaCobranza`, `idProducto`, `valKmAdic`, `hraIniEsperado`, `valAdicHraIniEsper`, `hraFinEsperado`, `valAdicHraFinEsper`, `cobrarPeaje`, `cobrarRecojoDevol`, `valUnidTercCCond`, `valUnidTercSCond`, `hrasNormalTerc`, `tolerHrasNormalTerc`, `valHraExtraTerc`, `valKmAdicTerc`, `tolerKmEsperado`, `movilAsignado`, `editaUsuarioDesp`, `cliente`.nombre, valAuxTerceroDesp, usuarioAsignado FROM `despacho` AS des, cliente  WHERE des.idCliente = cliente.idRuc AND des.idProgramacion = :idProgramacion ");
    $consulta->bindParam(':idProgramacion',$idProgramacion);
    $consulta->execute();
    return $consulta->fetch();
    
  }

  function completarCalculosDelDespacho($db, $idProgramacion){
    $dataDespacho = buscarDataDespachoIdProgram($db, $idProgramacion);
    $idProducto   = $dataDespacho["idProducto"];
    $placa        = $dataDespacho["placa"];
    $fchDespacho  = $dataDespacho["fchDespacho"];
    $correlativo  = $dataDespacho["correlativo"];

    //echo "ID PRODUCTO $idProducto, placa $placa, correlativo $correlativo";

    $actualiza = $db->prepare("UPDATE `despacho` AS `dest`, ( SELECT `clicue`.nombreCuenta, `clicue`.tipoCuenta, clicuepro.`idProducto`, clicuepro.`idCliente`, clicuepro.`correlativo`, clicuepro.`nombProducto`, clicuepro.`m3Facturable`, clicuepro.`puntos`, clicuepro.`idZona`, clicuepro.`zona`, clicuepro.`tipoProducto`, clicuepro.`estadoProducto`, clicuepro.`precioServ`, clicuepro.`kmEsperado`, clicuepro.`tolerKmEsperado`, clicuepro.`valKmAdic`, clicuepro.`hrasNormales`, clicuepro.`tolerHrasNormales`, clicuepro.`valHraAdic`, clicuepro.`hraIniEsperado`, clicuepro.`tolerHraIniEsperado`, clicuepro.`valAdicHraIniEsper`, clicuepro.`hraFinEsperado`, clicuepro.`tolerHraFinEsperado`, clicuepro.`valAdicHraFinEsper`, clicuepro.`nroAuxiliares`, clicuepro.`valAuxiliarAdic`, clicuepro.`cobrarPeaje`, clicuepro.`cobrarRecojoDevol`, clicuepro.`valConductor`, clicuepro.`hraNormalConductor`, clicuepro.`tolerHraCond`, clicuepro.`valHraAdicCond`, clicuepro.`valAuxiliar`, clicuepro.`hraNormalAux`, clicuepro.`tolerHraAux`, clicuepro.`valHraAdicAux`, clicuepro.`usoMaster`, clicuepro.`valUnidTercCCond`, clicuepro.`valUnidTercSCond`, clicuepro.`hrasNormalTerc`, clicuepro.`tolerHrasNormalTerc`, clicuepro.`valHraExtraTerc`, clicuepro.`valKmAdicTerc`, clicuepro.`valAuxTercero`, clicuepro.`contarDespacho`, clicuepro.`superCuenta` FROM  `clientecuentaproducto` AS clicuepro , clientecuentanew AS clicue WHERE `clicuepro`.idCliente = `clicue`.idCliente AND `clicuepro`.correlativo = `clicue`.correlativo AND idProducto =  :idProducto) AS `src` SET `dest`.`m3` = `src`.`m3Facturable`, `dest`.hrasNormales = `src`.hrasNormales, `dest`.tolerHrasNormales = `src`.tolerHrasNormales, `dest`.hraNormCondDespacho = `src`.hraNormalConductor, `dest`.tolerHraCondDespacho = `src`.tolerHraCond,  `dest`.valHraAdicCondDespacho = `src`.valHraAdicCond, `dest`.hraNormAuxDespacho = `src`.hraNormalAux, `dest`.tolerHraAuxDespacho = `src`.tolerHraAux, `dest`.valHraAdicAuxDespacho = `src`.valHraAdicAux, `dest`.valorAuxAdicional = `src`.valAuxiliarAdic, `dest`.tipoServicioPago = `src`.tipoProducto, `dest`.topeServicioHraNormal = `src`.`tolerHraAux`, `dest`.tolerHraCondDespacho = `src`.`tolerHraCond`, `dest`.valorAuxiliar = `src`.`valAuxiliar`, `dest`.valHraAdicAuxDespacho = `src`.`valHraAdicAux`, `dest`.nroAuxiliaresCuenta = `src`.`nroAuxiliares`, `dest`.valorConductor = `src`.`valConductor`, `dest`.costoHraExtra = `src`.`valHraAdic`, `dest`.recorridoEsperado = `src`.`kmEsperado`, `dest`.valorServicio = `src`.precioServ, `dest`.valHraAdicCondDespacho = `src`.`valHraAdicCond`, `dest`.valorAuxAdicional = src.`valAuxiliarAdic`, `dest`.superCuenta = src.`superCuenta`, `dest`.igvServicio = round(0.18*`src`.precioServ,2), `dest`.usarMaster = `src`.usoMaster, `dest`.hraIniEsperado = `src`.hraIniEsperado, `dest`.hraFinEsperado = `src`.hraFinEsperado,  `dest`.tolerKmEsperado = `src`.tolerKmEsperado, `dest`.valUnidTercCCond = `src`.valUnidTercCCond, `dest`.valUnidTercSCond = `src`.valUnidTercSCond,  `dest`.hrasNormalTerc = `src`.hrasNormalTerc, `dest`.tolerHrasNormalTerc = `src`.tolerHrasNormalTerc, `dest`.valHraExtraTerc = `src`.valHraExtraTerc, `dest`.valKmAdicTerc = `src`.valKmAdicTerc, `dest`.tolerHraCondDespacho = `src`.tolerHraCond, `dest`.valKmAdic = `src`.valKmAdic, `dest`.valAdicHraIniEsper = `src`.valAdicHraIniEsper, `dest`.tolerHraAuxDespacho = `src`.tolerHraAux, `dest`.zonaDespacho = `src`.zona, `dest`.valAdicHraFinEsper = `src`.valAdicHraFinEsper, `dest`.contarDespacho = `src`.contarDespacho, `dest`.cobrarPeaje = `src`.cobrarPeaje, `dest`.cobrarRecojoDevol = `src`.cobrarRecojoDevol, `dest`.cuenta = `src`.nombreCuenta, `dest`.puntosDesp = `src`.puntos, `dest`.valAuxTerceroDesp = `src`.valAuxTercero, `dest`.idZonaDespacho = `src`.idZona, dest.`tpoExtraHras` = 0 WHERE `dest`.`idProgramacion` = :idProgramacion");

    $actualiza->bindParam(':idProducto',$idProducto);
    $actualiza->bindParam(':idProgramacion',$idProgramacion);
    $actualiza->execute();

    //Se completan datos del veh�culo
    $actualiza = $db->prepare("UPDATE `despacho` AS `dest`, ( SELECT  `nroVehiculo`, `propietario`,  `considerarPropio`,  `capCombustible`, `idEficSolesKm`, `combustibleFrec`, `kmUltimaMedicion` FROM `vehiculo` WHERE `idPlaca` = :placa ) AS `src` SET  `dest`.`considerarpropio` = `src`.`considerarPropio`  WHERE `dest`.`idProgramacion` = :idProgramacion");
    $actualiza->bindParam(':placa',$placa);
    $actualiza->bindParam(':idProgramacion',$idProgramacion);
    $actualiza->execute();

    //Se completan datos de la tripulacion
    //echo "fchDespacho: $fchDespacho, correlativo: $correlativo, idProgramacion: $idProgramacion";
    $actualiza = $db->prepare("UPDATE `despacho` AS `dest`, ( SELECT count(*) As tripulacion, sum(if(tipoRol = 'Auxiliar', 1, 0)) AS nroAuxiliares, sum(if(tipoRol = 'Auxiliar' AND esReten = 'Si' , 1, 0)) AS nroAuxReten, sum(if(tipoRol = 'Conductor', 1, 0)) AS nroConductor FROM despachopersonal WHERE fchDespacho = :fchDespacho AND correlativo = :correlativo  ) AS `src` SET  `dest`.`nroAuxiliares` = `src`.`nroAuxiliares`, `dest`.nroAuxiliaresReten = `src`.nroAuxReten,   `dest`.`nroAuxiliaresAdic` =  if( (`src`.`nroAuxiliares` - `src`.nroAuxReten ) -  `dest`.`nroAuxiliaresCuenta` >= 0,   `src`.`nroAuxiliares` - `src`.nroAuxReten -  `dest`.`nroAuxiliaresCuenta`, 0)   WHERE `dest`.`idProgramacion` = :idProgramacion");
    $actualiza->bindParam(':fchDespacho',$fchDespacho);
    $actualiza->bindParam(':correlativo',$correlativo);
    $actualiza->bindParam(':idProgramacion',$idProgramacion);
    $actualiza->execute();
    return 1;
  }

  function calcularFinDespacho($db,$idProgramacion){
    $valorHraExtra = 0;
    $valorAdicional = 0;
    $rpta = completarCalculosDelDespacho($db, $idProgramacion);

    $dataDespacho = buscarDataDespachoIdProgram($db, $idProgramacion);
    //var_dump($dataDespacho);

    $fchDespacho       = $dataDespacho['fchDespacho'];
    $correlativo       = $dataDespacho['correlativo'];
    $valorServicio     = $dataDespacho['valorServicio'];
    $kmInicio          = $dataDespacho['kmInicio'];
    $kmInicioCliente   = $dataDespacho['kmInicioCliente'];
    $kmFinCliente      = $dataDespacho['kmFinCliente'];
    $kmFin             = $dataDespacho['kmFin'];
    $hrasNormales      = $dataDespacho['hrasNormales'];
    $lugarFinCliente   = $dataDespacho['lugarFinCliente'];
    $hraInicio         = $dataDespacho["hraInicio"];
    $hraFin            = $dataDespacho["hraFin"];
    $hraInicioBase     = $dataDespacho["hraInicioBase"];
    $hraFinCliente     = $dataDespacho["hraFinCliente"];
    $fchDespachoFin    = $dataDespacho["fchDespachoFin"];
    $fchDespachoFinCli = $dataDespacho["fchDespachoFinCli"];
    $usaReten          = $dataDespacho["usaReten"];
    $usarMaster        = $dataDespacho["usarMaster"];
    $toleranCobroHraExtra  = $dataDespacho["toleranCobroHraExtra"];
    $costoHraExtra     = $dataDespacho["costoHraExtra"];
    $nroAuxiliaresAdic = $dataDespacho["nroAuxiliaresAdic"];
    $valorAuxAdicional = $dataDespacho["valorAuxAdicional"];
    $nombreCliente     = $dataDespacho["nombre"];

    $hraNormCondDespacho   = $dataDespacho["hraNormCondDespacho"];
    $tolerHraCondDespacho  = $dataDespacho["tolerHraCondDespacho"];
    $hraNormAuxDespacho    = $dataDespacho["hraNormAuxDespacho"];
    $tolerHraAuxDespacho   = $dataDespacho["tolerHraAuxDespacho"];
    $valHraAdicCondDespacho = $dataDespacho["valHraAdicCondDespacho"];
    $valHraAdicAuxDespacho  = $dataDespacho["valHraAdicAuxDespacho"];

    $valorConductor = $dataDespacho["valorConductor"];
    $valorAuxiliar  = $dataDespacho["valorAuxiliar"];

    $valUnidTercCCond  = $dataDespacho["valUnidTercCCond"];
    $valUnidTercSCond  = $dataDespacho["valUnidTercSCond"];
    $hrasNormalTerc    = $dataDespacho["hrasNormalTerc"];
    $tolerHrasNormalTerc = $dataDespacho["tolerHrasNormalTerc"];
    $valHraExtraTerc   = $dataDespacho["valHraExtraTerc"];
    $placa =  $dataDespacho["placa"];
    $usuario  =  $dataDespacho["usuario"];

    //Nuevos
    $valKmAdicTerc   = $dataDespacho["valKmAdicTerc"];
    $tolerKmEsperado = $dataDespacho["tolerKmEsperado"];
    $valAuxTerceroDesp = $dataDespacho["valAuxTerceroDesp"];
      
    //Se preparan campos adicionales
    $hraIniFch = $fchDespacho." ".$hraInicio;
    if ($lugarFinCliente == NULL){
      $hraFinCalculoSolo = $hraFin;
      $fchDespachoFinCalculo = $fchDespachoFin;
    } else if ($lugarFinCliente == "Base"){
      $hraFinCalculoSolo = $hraFin;
      $fchDespachoFinCalculo = $fchDespachoFin;
    } else {
      $hraFinCalculoSolo = $hraFinCliente;
      $fchDespachoFinCalculo = $fchDespachoFinCli;
    }
    $hraFinCalculo = $fchDespachoFinCalculo." ".$hraFinCalculoSolo;
    //echo "INICIO $hraIniFch , FIN $hraFinCalculo";

    ////////////////////////////////////////
    //Regenerar los documentos de cobranza//
    ////////////////////////////////////////
    //Elimina documentos de cobranza si ya los hab�a
    $elimina = $db->prepare("DELETE FROM `despachodetallesporcobrar` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
    $elimina->bindParam(':fchDespacho',$fchDespacho);
    $elimina->bindParam(':correlativo',$correlativo);
    $elimina->execute();
    $observacion = "";
    insertaDetallesPorCobrar($db,$fchDespacho,$correlativo,'Despacho',$valorServicio,1, $usuario,$observacion);

    $tpoExtraHrasDecimalTrab = 0;
    if ($hrasNormales != '00:00:00'){
      //C�lculo de las horas extra
      //$hrasNormales = substr("00".$hrasNormales,-2).":00:00";
      $duracionhhmmss = restahhmmss($hraIniFch,$hraFinCalculo);
      $tpoExtraHrasDecimalTrab = difEnHorasDecim($hrasNormales,$duracionhhmmss);
      if($toleranCobroHraExtra == "" || $toleranCobroHraExtra == null) $toleranCobroHraExtra = '00:00:00';
      $arrHraTolerancia = explode(":", $toleranCobroHraExtra);
      $tiempoToleranciaEnSegundos = $arrHraTolerancia[0]*3600 + $arrHraTolerancia[1]*60 + $arrHraTolerancia[2];
      $tpoExtraHrasDecimalTrab = ($tpoExtraHrasDecimalTrab <= ($tiempoToleranciaEnSegundos/60))?0:$tpoExtraHrasDecimalTrab;

      if ($tpoExtraHrasDecimalTrab > 0){
        insertaDetallesPorCobrar($db,$fchDespacho,$correlativo,'HrasAdic',$costoHraExtra,$tpoExtraHrasDecimalTrab, $usuario);

        $actualiza = $db->prepare("UPDATE `despacho` SET `tpoExtraHras` = :tpoExtraHrasDecimalTrab WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
        $actualiza->bindParam(':tpoExtraHrasDecimalTrab',$tpoExtraHrasDecimalTrab);
        $actualiza->bindParam(':fchDespacho',$fchDespacho);
        $actualiza->bindParam(':correlativo',$correlativo);
        $actualiza->execute();
      }
    }

    if ($nroAuxiliaresAdic > 0) insertaDetallesPorCobrar($db,$fchDespacho,$correlativo,'AuxAdic',$valorAuxAdicional,$nroAuxiliaresAdic, $usuario);

    ////////////////////////////////////////////
    //Regenerar horas extra de la tripulaci�n //
    ////////////////////////////////////////////
    //Elimina info sobre Hras Extra
    $descripHraExtra = "Servicio $nombreCliente $fchDespacho-$correlativo";
    $elimina = $db->prepare("DELETE FROM prestamo WHERE `tipoItem` = 'HraExtra' AND `descripcion` like '$descripHraExtra'");
    $elimina->execute();


    $actualiza = $db->prepare("UPDATE `despachopersonal` SET `trabHraInicio` = if (`trabHraInicio` = '00:00:00' OR `trabHraInicio` IS NULL, '$hraInicio', `trabHraInicio` ) , `trabHraFin` = if (`trabHraFin` = '00:00:00' OR `trabHraFin` IS NULL, '$hraFinCalculoSolo', `trabHraFin` ), usuario = :usuario  WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
    $actualiza->bindParam(':fchDespacho',$fchDespacho);
    $actualiza->bindParam(':correlativo',$correlativo);
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->execute();

    $categConductor = "";
    $contAuxiliaresTerceros = 0;


    //Recalcula hras extra si las hay y verifica el tema del master
    $auxTripulacion = buscarInfoTripulacion($db, $fchDespacho, $correlativo);
    foreach ($auxTripulacion as $key => $value) {
      $idTrabajador  = $value["idTrabajador"];
      $fchTrabInicio = $value["fchDespacho"];
      $hraTrabInicio = $value["trabHraInicio"];
      $fchTrabFin    = $value["trabFchDespachoFin"];
      $hraTrabFin    = $value["trabHraFin"];
      $tipoRol       = $value["tipoRol"];
      $valorRol      = $value["valorRol"];
      $categTrabajador   = $value["categTrabajador"];
      $esMaster      = $value["esMaster"];
      $precioMaster  = $value["precioMaster"];

      if ($tipoRol == "Conductor") $categConductor = $categTrabajador;

      $hraIniCalculo = $fchTrabInicio." ".$hraTrabInicio;
      $hraFinCalculo = $fchTrabFin." ".$hraTrabFin;
      if ($tipoRol == "Conductor"){
        $hrasEsperadas   = $hraNormCondDespacho;
        $topeaConsiderar = $tolerHraCondDespacho;
        $valorHraAdic    = $valHraAdicCondDespacho;
        $valorRol        = $valorConductor;
      } else if ($tipoRol == "Auxiliar") {
        $hrasEsperadas   = $hraNormAuxDespacho;
        $topeaConsiderar = $tolerHraAuxDespacho;
        $valorHraAdic    = $valHraAdicAuxDespacho;
        $valorRol        = $valorAuxiliar;
        if($categTrabajador == "Tercero") $contAuxiliaresTerceros++;
      } else {
        $hrasEsperadas   = 0;
        $topeaConsiderar = 0;
        $valorHraAdic    = 0;
        $valorRol        = 0;
      }

      if($categTrabajador != "Tercero"){
        $duracionhhmmss = restahhmmss($hraIniCalculo, $hraFinCalculo);
        $tpoExtraHrasDecimalTrab = difEnHorasDecim($hrasEsperadas,$duracionhhmmss);

        $arrAux = explode(":", $topeaConsiderar);
        $tiempoToleranciaEnSegundos = $arrAux[0]*3600 + $arrAux[1]*60 + $arrAux[2];
        $tpoExtraHrasDecimalTrab = ($tpoExtraHrasDecimalTrab <= ($tiempoToleranciaEnSegundos/60))?0:$tpoExtraHrasDecimalTrab;

        if ($tpoExtraHrasDecimalTrab >0 AND $valorHraAdic > 0 AND $topeaConsiderar != '0:00' AND $tipoRol != 'Coordinador'){
          $monto = round($tpoExtraHrasDecimalTrab * $valorHraAdic,2);
          $descripHraExtra = "Servicio $nombreCliente $fchDespacho-$correlativo";
          //Todav�a no s� si va aqu� algo
          //insertaRegEnPrestamo($db, $idTrabajador,$descripHraExtra,$monto,1,$fchDespacho,'HraExtra',$usuario); 
        }
        if($tipoRol == 'Conductor'){
          $valorHraExtra = $tpoExtraHrasDecimalTrab * $valHraAdicCondDespacho;
        } else if($tipoRol == 'Auxiliar'){
          $valorHraExtra = $tpoExtraHrasDecimalTrab * $valHraAdicAuxDespacho;
        }
      } else {
        $valorRol = 0;
        $valorAdicional = 0;
        $valorHraExtra  = 0;
      }

      //C�lculo pago de trabajadores
      if($usarMaster == "Si"){
        if($esMaster == "Si" && $valorRol < $precioMaster  ) $valorRol = $precioMaster;
      }

      $rpta = completarCalculosTrabajador($db, $fchDespacho, $correlativo, $idTrabajador, $tipoRol, $valorRol, $valorAdicional, $valorHraExtra, $hraTrabFin, $fchTrabFin, $usuario);

    }
    /////////////
    /////////////

    //////////////////////////////////////////
    /// GENERANDO PAGO AL TERCERO SI LO ES ///
    //////////////////////////////////////////
    //elimina info sobre liquidacion a tercero si es que hay
    $esUnTercero = "No";
    $elimina = $db->prepare("DELETE FROM `despachovehiculotercero` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
    $elimina->bindParam(':fchDespacho',$fchDespacho);
    $elimina->bindParam(':correlativo',$correlativo);
    $elimina->execute();
    //Verifica y procesa si la unidad pertenece a un tercero
    $consulta = $db->prepare("SELECT `documento`, `costoDia`, `modoAcuerdo`  FROM `vehiculotercero` WHERE `idPlaca` = :placa");
    $consulta->bindParam(':placa',$placa); 
    $consulta->execute();
    $consultaPropietario = $consulta->fetchAll();

    //Calculo datos si es tercero
    /////////////////////////////    
    foreach($consultaPropietario as $item){
      $propietario = $item['documento'];
      $modoAcuerdo = $item['modoAcuerdo'];
      //$costoDia = $item['costoDia']; No se utiliza

      $guiaTrTercero = "";

      if ($categConductor == "Tercero") {
        $valUnidadTercero = $valUnidTercCCond;
        $pagoBaseConductor = 0;
        $observConductor = "Es un Conductor Tercero";
        $observPlaca = "Precio con Conductor";
      } else {
        $valUnidadTercero = $valUnidTercSCond;
        $observPlaca = "Precio sin Conductor";
      }

      $inserta = $db->prepare("INSERT INTO `despachovehiculotercero` (`fchDespacho`, `correlativo`, `placa`, `costoDia`, `docTercero`, `pagado`, `observPlacaTer`, `guiaTrTercero`, `modoAcuerdo`, `fchCreacion`, `usuario`) VALUES (:fchDespacho, :correlativo, :placa, :costoDia, :docTercero, 'No', :observPlacaTer , :guiaTrTercero, :modoAcuerdo ,curdate(), :usuario)");
      $inserta->bindParam(':fchDespacho',$fchDespacho);
      $inserta->bindParam(':correlativo',$correlativo);
      $inserta->bindParam(':placa',$placa);
      $inserta->bindParam(':costoDia',$valUnidadTercero);
      $inserta->bindParam(':guiaTrTercero',$guiaTrTercero);
      $inserta->bindParam(':modoAcuerdo',$modoAcuerdo);
      $inserta->bindParam(':observPlacaTer',$observPlaca);
      $inserta->bindParam(':docTercero',$propietario);
      $inserta->bindParam(':usuario',$usuario);
      $inserta->execute();
      $esUnTercero = "Si";

      //// Adicionales ////
      /////////////////////
      $tpoExtraHrasDecimalTrab = difEnHorasDecim($hrasNormalTerc,$duracionhhmmss);
      if($tolerHrasNormalTerc == "" || $tolerHrasNormalTerc == null) $tolerHrasNormalTerc = '00:00:00';
      $arrHraTolerancia = explode(":", $tolerHrasNormalTerc);
      $tiempoToleranciaEnSegundos = $arrHraTolerancia[0]*3600 + $arrHraTolerancia[1]*60 + $arrHraTolerancia[2];
      $tpoExtraHrasDecimalTrab = ($tpoExtraHrasDecimalTrab <= ($tiempoToleranciaEnSegundos/60))?0:$tpoExtraHrasDecimalTrab;

      if ($tpoExtraHrasDecimalTrab >0 AND $valHraExtraTerc > 0 ){
        $valHraExtraTercero = round($tpoExtraHrasDecimalTrab * $valHraExtraTerc,2);
        $cantidad = insertaEnOcurrenciaTercero($db, $fchDespacho, $correlativo, 'HraExtra|A favor proveedor', $tpoExtraHrasDecimalTrab, $placa, $valHraExtraTercero);
      }
      
      //Revisando esta parte
      if($contAuxiliaresTerceros > 0){
        $cantidad = insertaEnOcurrenciaTercero($db, $fchDespacho, $correlativo, 'Personal Adicional|A favor proveedor', $contAuxiliaresTerceros, $placa, $valAuxTerceroDesp);
      }
      




    }
    //Actualiza nuevo marcador final en kilometraje
    if ($kmFin == null)
      $kmFinVeh = $kmFinCliente;
    else if ($kmFinCliente == null)
      $kmFinVeh = $kmFin;
    else if ($kmFin > $kmFinCliente)
      $kmFinVeh = $kmFin;
    else
      $kmFinVeh = $kmFinCliente;

    $actualiza = $db->prepare("UPDATE `vehiculo` SET `kmUltimaMedicion` =  if(`kmUltimaMedicion` < '$kmFinVeh', :kmFin, `kmUltimaMedicion`), `usuarioUltimoCambio` = :usuario , `fchUltimoCambio` = curdate()  WHERE `vehiculo`.`idPlaca` = :placa");
    $actualiza->bindParam(':placa',$placa);
    $actualiza->bindParam(':kmFin',$kmFinVeh);
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->execute();

    /////////////
    /////////////
    /////////////


    //return "PRUEBA DE RECEPCION"; 
  }

  //////////////////////

  global $servidor, $bd, $usuario, $contrasenia;
  $db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);
  $db->exec("SET NAMES 'utf8';");

  function logAccion($db,$descripcion,$idTrabajador,$placa){
    $user = $_SESSION['usuario'];
    $inserta = $db->prepare("INSERT INTO `logaccion` (`id`, `descripcion`, `idTrabajador`,`placa`,`usuario`, `fecha`) VALUES (NULL, :descripcion, :idTrabajador,:placa,:usuario, NOW())");
    $inserta->bindParam(':descripcion',$descripcion);
    $inserta->bindParam(':idTrabajador',$idTrabajador);
    $inserta->bindParam(':placa',$placa);
    $inserta->bindParam(':usuario',$user);
    $inserta->execute();
  };

  function tercerosOrdenP($db){
    $consulta = $db->prepare("SELECT `documento`, `tipoDocumento`, `nombreCompleto`, t1.cant, if(t1.cant IS NULL,0,1) AS prioridad FROM `vehiculodueno` left join (SELECT docTercero, count( * ) AS cant FROM `despachovehiculotercero` WHERE `docPagoTercero` IS NULL AND DATEDIFF( curdate( ) , fchDespacho ) <120 GROUP BY docTercero ) AS t1 ON documento = t1.docTercero WHERE  `vehiculodueno`.estadoTercero = 'Activo' ORDER BY prioridad DESC, nombreCompleto");
    $consulta->execute();
    $terceros = $consulta->fetchAll();

    echo "<option>-</option>";
    foreach($terceros as $item) { 
      echo "<option>";
      echo $item['documento']."-".$item['tipoDocumento']."-".$item['nombreCompleto']." --> Referencia: ".$item['cant'];
      echo "</option>";
     };
  }

  function insertaDocIdentidad($db,$dataPost){
    $usuario = $_SESSION["usuario"];
    $fecha = Date("Y-m-d");
    $idTrabajador = utf8_decode($_POST['idTrabajador']);

    $auxTipoDocTrab = utf8_decode($_POST['cmbTipoDocIdentidad']);
    $tipoDocTrab = strtok($auxTipoDocTrab, "|");
    $codPlame = strtok("|");

    $nroDocTrab = utf8_decode($_POST['txtNvoDocIdentidad']);
    $fchIni = utf8_decode($_POST['txtFchIniDocIdentidad']);
    $fchFin = utf8_decode($_POST['txtFchFinDocIdentidad']);
    $estado = utf8_decode($_POST['cmbEstadoDocIdentidad']);
    $observacion = utf8_decode($_POST['txtObserv']); 

    if ($_FILES["imagen"]["error"] > 0){
      return 0;
    } else {
      $permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png","application/pdf");
      $limite_kb = 1000;
      //echo $_FILES['imagen']['type'];
      if (in_array($_FILES['imagen']['type'], $permitidos) && $_FILES['imagen']['size'] <= $limite_kb * 1024){
        //$nombreActual = $_FILES['imagen']['name'];   
        $auxNombre = $idTrabajador.$tipoDocTrab.$nroDocTrab.$fecha;
        $arrayBuscar = array(".", " ");
        $nombreArch =  str_replace($arrayBuscar, '_', $auxNombre);
        $auxExtension = explode('.',$_FILES['imagen']['name'] );
        $extension = end($auxExtension);
        $escaneo =  "$nombreArch.$extension";
        $ruta = "../imagenes/data/trabajador/".$escaneo;
        //echo $ruta;
        if (!file_exists($ruta)){
          $resultado = @move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta);
          if ($resultado){
            $inserta = $db->prepare("INSERT INTO `trabajdocumentos`(`nroDocTrab`, `tipoDocTrab`, `fchIni`, `fchFin`, `codPlame`, `idTrabajador`, `adjunto`, `observacion`,  `creacUsuario`,  `estado`) VALUES (:nroDocTrab,:tipoDocTrab, :fchIni, :fchFin, :codPlame, :idTrabajador, :adjunto, :observacion, :usuario, :estado)");
            $inserta->bindParam(':nroDocTrab',$nroDocTrab);  
            $inserta->bindParam(':tipoDocTrab',$tipoDocTrab);
            $inserta->bindParam(':fchIni',$fchIni);
            $inserta->bindParam(':fchFin',$fchFin);
            $inserta->bindParam(':codPlame',$codPlame);
            $inserta->bindParam(':observacion',$observacion);
            $inserta->bindParam(':idTrabajador',$idTrabajador);
            $inserta->bindParam(':adjunto',$ruta);
            $inserta->bindParam(':usuario',$usuario);
            $inserta->bindParam(':estado',$estado);
            $inserta->execute();
            return $inserta->rowCount();
          } else {
            return -2; //ocurrio un error al mover el archivo
          }
        } else {
          return -3; // este archivo existe";
        }
      } else {
        return -4; //echo "archivo no permitido, es tipo de archivo prohibido o excede el tamano de $limite_kb Kilobytes";
      }
    }
  }

  function buscarDocIdentidad($db){
    $tipoDoc = $_POST["tipoDoc"]; //(this).attr("tipoDoc")
    $nroDoc = $_POST["nroDoc"]; //$(this).attr("nroDoc")
    $fchIni = $_POST["fchIni"]; //$(this).attr("fchIni")

    //echo "$tipoDoc , $nroDoc , $fchIni ";

    $consulta = $db->prepare("SELECT `nroDocTrab`, `tipoDocTrab`, `fchIni`, `fchFin`, `idTrabajador`, `adjunto`, `observacion`, `creacFch`, `creacUsuario`, `editaFch`, `editaUsuario`, `estado`  FROM `trabajdocumentos` WHERE `nroDocTrab` LIKE :nroDocTrab AND `tipoDocTrab` = :tipoDocTrab AND `fchIni` = :fchIni");
    
    $consulta->bindParam(':nroDocTrab',$nroDoc);
    $consulta->bindParam(':tipoDocTrab',$tipoDoc);
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->execute();
    return $consulta->fetch();
  }


  function editaDocIdentidad($db){
    $usuario = $_SESSION["usuario"];
    $fecha = Date("Y-m-d");
    $tipoDocTrab = utf8_decode($_POST['txtEditaTipoDocIdentidad']);
    $nroDocTrab = utf8_decode($_POST['txtEditaDocIdentidad']);
    $fchIni = utf8_decode($_POST['fchIniEditaDocIdentidad']);
    $estado = utf8_decode($_POST['cmbEditaEstadoDocIdentidad']);
    $observacion = utf8_decode($_POST['txtEditaObserv']);
    $rutaOriginal = utf8_decode($_POST['rutaEditaOriginal']);
    $idTrabajador = utf8_decode($_POST['idEditaTrabajador']);    
    //echo "ADJUNTO ".$_FILES['imagenEdita']['error']."***";

    if ($_FILES["imagenEdita"]["error"] > 0 && $_FILES["imagenEdita"]["error"] != 4){
      return 0;
    } else if ($_FILES["imagenEdita"]["error"] == 4) {

      $actualiza = $db->prepare("UPDATE `trabajdocumentos` SET `estado` = :estado, `observacion` = :observacion, editaUsuario = :usuario, editaFch = curdate() WHERE `nroDocTrab` = :nroDocTrab AND `tipoDocTrab` = :tipoDocTrab AND `fchIni` = :fchIni");
      $actualiza->bindParam(':nroDocTrab',$nroDocTrab);  
      $actualiza->bindParam(':tipoDocTrab',$tipoDocTrab);
      $actualiza->bindParam(':fchIni',$fchIni);
      $actualiza->bindParam(':observacion',$observacion);
      $actualiza->bindParam(':usuario',$usuario);
      $actualiza->bindParam(':estado',$estado);
      $actualiza->execute();
      return $actualiza->rowCount();

    } else {

      $permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png","application/pdf");
      $limite_kb = 500;
      //echo $_FILES['imagen']['type'];
      if (in_array($_FILES['imagenEdita']['type'], $permitidos) && $_FILES['imagenEdita']['size'] <= $limite_kb * 1024){
        //$nombreActual = $_FILES['imagen']['name'];   
        $auxNombre = $idTrabajador.$tipoDocTrab.$nroDocTrab.$fecha;
        $arrayBuscar = array(".", " ");
        $nombreArch =  str_replace($arrayBuscar, '_', $auxNombre);
        $auxExtension = explode('.',$_FILES['imagenEdita']['name'] );
        $extension = end($auxExtension);
        $escaneo =  "$nombreArch.$extension";
        if (file_exists($rutaOriginal)){
          //$rutaOriginal ="/imagenes/data/trabajador/40151314PAS658745212018-10-06.jpg";
          //echo $rutaOriginal;
          unlink($rutaOriginal);
        };
        $ruta = "../imagenes/data/trabajador/".$escaneo;
        //echo $ruta;
        if (!file_exists($ruta)){
          $resultado = @move_uploaded_file($_FILES["imagenEdita"]["tmp_name"], $ruta);
          if ($resultado){
            $actualiza = $db->prepare("UPDATE `trabajdocumentos` SET `estado` = :estado, `observacion` = :observacion, adjunto = :ruta, editaUsuario = :usuario, editaFch = curdate() WHERE `nroDocTrab` = :nroDocTrab AND `tipoDocTrab` = :tipoDocTrab AND `fchIni` = :fchIni");
            $actualiza->bindParam(':nroDocTrab',$nroDocTrab);  
            $actualiza->bindParam(':tipoDocTrab',$tipoDocTrab);
            $actualiza->bindParam(':fchIni',$fchIni);
            $actualiza->bindParam(':observacion',$observacion);
            $actualiza->bindParam(':usuario',$usuario);
            $actualiza->bindParam(':ruta',$ruta);
            $actualiza->bindParam(':estado',$estado);
            $actualiza->execute();
            return 1; //$actualiza->rowCount();
          } else {
            return -2; //ocurrio un error al mover el archivo
          }
        } else {
          return -3; // este archivo existe";
        }
      } else {
        return -4; //echo "archivo no permitido, es tipo de archivo prohibido o excede el tamano de $limite_kb Kilobytes";
      }
    }
  }


  function generarPagoTercero($db){
    $usuario = $_SESSION["usuario"];
    $fecha = Date("Y-m-d");
    $idDocumento = utf8_decode($_POST['idDocumento']);
    $nroDoc = utf8_decode($_POST['nroDoc']);


    $inserta = $db->prepare("INSERT INTO `docpagotercero` (`docPagoTercero`, `docTercero`, `estado`, `usuario`, `fchCreacion`, `hraCreacion`) VALUES (:nuevoDocumento, :nroDoc,  'pendiente', :usuario, CURDATE(), CURTIME());");
    $inserta->bindParam(':nuevoDocumento',$idDocumento);
    $inserta->bindParam(':nroDoc',$nroDoc);
    $inserta->bindParam(':usuario',$usuario);  
    $inserta->execute();
    return $inserta->rowCount();
  }

  function generarPagoTercElemento($db){
    $usuario = $_SESSION["usuario"];
    $fecha = Date("Y-m-d");
    $idDocumento = utf8_decode($_POST['idDocumento']);
    $atributosElem = utf8_decode($_POST['atributosElem']);

    $auxAtributos = explode("|",$atributosElem);

    $ubicaDestino = $auxAtributos[0];
    $tablaOrigen = $auxAtributos[1];
    $fchDelRegistro = $auxAtributos[3];
    if ($ubicaDestino == 'tblVPFactura') $docPagoTerTipo = "FACT";
    else if ($ubicaDestino == 'tblVPNotaCredito') $docPagoTerTipo = "NCRD";
    else if ($ubicaDestino == 'tblVPFactMoy') $docPagoTerTipo = "FMOY";

    if ($tablaOrigen == 'despachos'){
      $auxDatoAdic01 = explode("-", $auxAtributos[4]);

      if($auxDatoAdic01[1] == "0"){
        $correlativo = $auxDatoAdic01[0];

        $actualiza = $db->prepare("UPDATE  `despachovehiculotercero` SET `docPagoTerTipo` = :docPagoTerTipo,  `docPagoTercero` = :docPagoTercero  WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
        $actualiza->bindParam(':fchDespacho',$fchDelRegistro);  
        $actualiza->bindParam(':correlativo',$correlativo);
        $actualiza->bindParam(':docPagoTerTipo',$docPagoTerTipo);
        $actualiza->bindParam(':docPagoTercero',$idDocumento);
        $actualiza->execute();

      } else {

        $correlativo = $auxDatoAdic01[0];
        $corrOcurrencia = $auxDatoAdic01[1];

        $actualiza = $db->prepare("UPDATE `ocurrenciatercero` SET `docPagoTercero` = :docPagoTercero, `docPagoTerTipo` = :docPagoTerTipo WHERE `fchDespacho` =:fchDespacho AND `correlativo` = :correlativo AND `corrOcurrencia` = :corrOcurrencia ");
        $actualiza->bindParam(':fchDespacho',$fchDelRegistro);  
        $actualiza->bindParam(':correlativo',$correlativo);
        $actualiza->bindParam(':docPagoTerTipo',$docPagoTerTipo);
        $actualiza->bindParam(':docPagoTercero',$idDocumento);
        $actualiza->bindParam(':corrOcurrencia',$corrOcurrencia);
        $actualiza->execute();

        
      }

    } else if ($tablaOrigen == 'miscelaneos'){
      $idMiscelaneo = $auxAtributos[4];

      $actualiza = $db->prepare("UPDATE `liqterceromisc` SET `docPagoTercero` = :docPagoTercero, docPagoTerTipo = :docPagoTerTipo  WHERE `id` = :idMiscelaneo ");
      $actualiza->bindParam(':idMiscelaneo',$idMiscelaneo);
      $actualiza->bindParam(':docPagoTerTipo',$docPagoTerTipo);
      $actualiza->bindParam(':docPagoTercero',$idDocumento);
      $actualiza->execute();

    } else if ($tablaOrigen == 'abastOtros'){
      $arrayIdAux = explode( "@", $auxAtributos[5]);

      if ($auxAtributos[2] == 'Vale'){
        $fchIdAux = $arrayIdAux[0];
        $hraIdAux = $arrayIdAux[1];
        $placaIdAux = $arrayIdAux[2];

        $nroVale = $auxAtributos[4];
        $idPlaca = $auxAtributos[5];

        $actualiza = $db->prepare("UPDATE `combustible` SET `docPagoTercero` = :docPagoTercero, `docPagoTerTipo` = :docPagoTerTipo WHERE `fchCreacion` = :fchCreacion  AND `idPlaca` = :idPlaca AND `hraCreacion` = :hraCreacion ");
        $actualiza->bindParam(':fchCreacion',$fchIdAux);
        $actualiza->bindParam(':docPagoTerTipo',$docPagoTerTipo);
        $actualiza->bindParam(':docPagoTercero',$idDocumento);
        $actualiza->bindParam(':idPlaca',$placaIdAux);
        $actualiza->bindParam(':hraCreacion',$hraIdAux);

        $actualiza->execute();
      } else if ($auxAtributos[2] == '12' || $auxAtributos[2] == '01'){
        $nroOrdenIdAux = $arrayIdAux[0];
        $correlIdAux = $arrayIdAux[1];

        $actualiza = $db->prepare("UPDATE `ccmovimientos` SET `docPagoTercero` = :docPagoTercero, `docPagoTerTipo` = :docPagoTerTipo WHERE `nroOrden` = :nroOrden  AND `correlativo` = :correlativo ");
        $actualiza->bindParam(':nroOrden',$nroOrdenIdAux);
        $actualiza->bindParam(':correlativo',$correlIdAux);
        $actualiza->bindParam(':docPagoTerTipo',$docPagoTerTipo);
        $actualiza->bindParam(':docPagoTercero',$idDocumento);
        $actualiza->execute();
      }
    }
    return 1; //  $idDocumento."-".$atributosElem;

  }


  function buscarPagoTerceroDetalle($db, $docPagoTerTipo, $docPagoTercero){
    $consulta = $db->prepare("SELECT desvehter.`fchDespacho`, desvehter.`correlativo`, desvehter.`placa`, `costoDia`,  `docPagoTercero`, `docPagoTerTipo`, desvehter.`pagado`, 'Despacho' AS tipo, `cliente`.nombre, `des`.cuenta , concat(`des`.m3,' m3')  AS observ, `desvehter`.modoAcuerdo, 'desvehter' AS origen, `conductor`.nombConductor, `des`.`comentario` 
      FROM `despachovehiculotercero` AS desvehter, cliente , despacho AS des LEFT JOIN 
      (SELECT fchDespacho, correlativo, `desper`.idTrabajador,
       concat(nombres,' ',apPaterno,' ',apMaterno, if(categTrabajador = 'Tercero', ' (Tercero)', ' (Moy)')  ) as nombConductor, '' FROM trabajador,  `despachopersonal` AS desper WHERE  `trabajador`.idTrabajador = `desper`.idTrabajador AND `tipoRol` LIKE 'Conductor') AS conductor  ON `des`.fchDespacho = `conductor`.fchDespacho AND `des`.correlativo = `conductor`.correlativo
         WHERE  desvehter.`fchDespacho` = des.`fchDespacho` AND desvehter.`correlativo` = des.`correlativo`  AND `des`.idCliente = `cliente`.idRuc AND desvehter.`docPagoTercero` LIKE :docPagoTercero AND `desvehter`.docPagoTerTipo = :docPagoTerTipo
      UNION
      SELECT `fchEvento` , 'correl', idPlaca, monto,  `docPagoTercero`, `docPagoTerTipo`, pagado, tipoMisc, 'nombre' AS nombre, 'cuenta' AS cuenta, descripcion AS observ, '' AS modoAcuerdo, 'liqtermis' AS origen, '' AS nombConductor, ''
      FROM `liqterceromisc` WHERE `docPagoTercero` LIKE :docPagoTercero AND docPagoTerTipo = :docPagoTerTipo
      UNION
      SELECT `movenc`.fchEvento, 'correl', `movim`.idPlaca, `movim`.monto, movim.`docPagoTercero`, movim.`docPagoTerTipo`, `movim`.pagado, 'Movim' AS tipo, 'nombre' AS nombre, `movim`.nroOrden AS cuenta, `movim`.descripcion AS observ, '' AS modoAcuerdo, 'ccmovim' AS origen, '' AS nombConductor, ''
      FROM `ccmovimencab` AS movenc , `ccmovimientos` AS movim 
      WHERE  `movenc`.idMovimEncab =  `movim`.nroOrden AND movim.`docPagoTercero` = :docPagoTercero AND `docPagoTerTipo` = :docPagoTerTipo
      UNION
      SELECT ocuter.`fchDespacho` , `ocuter`.`correlativo` , `des`.placa, `montoTotal`,  ocuter.`docPagoTercero` , ocuter.`docPagoTerTipo`,  ocuter.`pagado`,  `tipoOcurrencia`,  `cliente`.nombre, `des`.cuenta, descripcion AS descripcion,'' AS modoAcuerdo, 'ocurrencia' AS origen, '' AS nombConductor, '' FROM `ocurrenciatercero` AS ocuter , despacho AS des, cliente WHERE  ocuter.`fchDespacho` = des.`fchDespacho` AND ocuter.`correlativo` = des.`correlativo`  AND `des`.idCliente = `cliente`.idRuc AND  ocuter.`docPagoTercero` = :docPagoTercero AND `ocuter`.docPagoTerTipo =  :docPagoTerTipo
      UNION
      SELECT `fchCreacion`, `correlativo` AS correl, `idPlaca`, `total` AS monto, `docPagoTercero`, `docPagoTerTipo`, `pagado`, 'Combust' AS tipo, 'nombre' AS nombre, 'cuenta' AS cuenta, `observacion` AS observ, '' AS modoAcuerdo, 'combust' AS origen, '' AS nombConductor, '' FROM `combustible` WHERE `docPagoTercero` LIKE :docPagoTercero AND `docPagoTerTipo` = :docPagoTerTipo ORDER BY placa ASC, fchDespacho ASC, FIELD (tipo,'Despacho') DESC");

    $consulta->bindParam(':docPagoTercero',$docPagoTercero);
    $consulta->bindParam(':docPagoTerTipo',$docPagoTerTipo);
    //$consulta->bindParam(':idTrabajador',$idTrabajador);
    //$consulta->bindParam(':id',$id);
    $consulta->execute();
    return $consulta->fetchAll();

  }

  function insertaNuevaOcurrencia($db){
    $fchDespacho = $_POST['txtFchDespacho'];
    $correlativo = $_POST['txtCorrelativo'];
    //$descripcion = $_POST['cmbTipoOcurrencia'].": ".$_POST['txtDescripcion']." ".$_POST['placa'];
    $descripcion = $_POST['cmbTipoOcurrencia'].": "." ".$_POST['placa'];
    $monto = $_POST['txtMonto'];
    $tercero = $_POST['tercero'];
    $auxTipoOcurrencia = $_POST['cmbTipoOcurrencia'];

    $tipoOcurrencia = strtok($auxTipoOcurrencia, "|");
    $tipoConcepto =  strtok("|");

    $consulta = $db->prepare("SELECT corrOcurrencia FROM `ocurrenciatercero` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ORDER BY `corrOcurrencia` DESC LIMIT 1 ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);

    $consulta->execute();
    $resultado = $consulta->fetchAll();
    $corrOcurrencia = 1;
    foreach($resultado as $item) {
      $corrOcurrencia = (1*$item['corrOcurrencia'] + 1);
    }
    $descripcion = $descripcion."|".$_POST['txtDescripcion'];
    $usuario = $_SESSION["usuario"];
    $inserta = $db->prepare("INSERT INTO `ocurrenciatercero` (`fchDespacho`, `correlativo`, `corrOcurrencia`, `tipoOcurrencia`,`tipoConcepto`, `descripcion`, `montoTotal`, `idTercero`, `usuario`, `fchCreacion`, `hraCreacion`) VALUES (:fchDespacho, :correlativo, :corrOcurrencia , :tipoOcurrencia ,  :tipoConcepto ,  :descripcion,:mntTotal, :idTercero,  :usuario, curdate(), curtime())");
    $inserta->bindParam(':fchDespacho',$fchDespacho);
    $inserta->bindParam(':correlativo',$correlativo);
    $inserta->bindParam(':tipoOcurrencia',$tipoOcurrencia);
    $inserta->bindParam(':corrOcurrencia',$corrOcurrencia);
    $inserta->bindParam(':tipoConcepto',$tipoConcepto);
    $inserta->bindParam(':descripcion',$descripcion);
    $inserta->bindParam(':mntTotal',$monto);
    $inserta->bindParam(':idTercero',$tercero);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();

    return  $inserta->rowCount();
  }


  function cambiaRuc($db){
    $paraVerificarLaTabla = $_POST["cambiaRucTipo"];
    $fchDespacho = $_POST["cambiaRucFchDespacho"];
    $correlativo = $_POST["cambiaRucCorrelDespacho"];
    $auxNuevoRuc = $_POST["txtProveedorAjax"];
    $nuevoRuc = strtok($auxNuevoRuc, "-");
    $corrOcurrencia = $_POST["cambiaRucCorrelRegistro"];

    if ($paraVerificarLaTabla == "Despacho"){

      $actualiza = $db->prepare("UPDATE `despachovehiculotercero` SET `docTercero` = :nuevoRuc WHERE `fchDespacho` = :fchDespacho AND `correlativo` =  :correlativo ");
      $actualiza->bindParam(':fchDespacho',$fchDespacho);
      $actualiza->bindParam(':correlativo',$correlativo);
      $actualiza->bindParam(':nuevoRuc',$nuevoRuc);
      $actualiza->execute();
      return  $actualiza->rowCount();
    } elseif ($corrOcurrencia != "0") {

      $actualiza = $db->prepare("UPDATE `ocurrenciatercero` SET `idTercero` = :nuevoRuc WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `corrOcurrencia` = :corrOcurrencia ");
      $actualiza->bindParam(':fchDespacho',$fchDespacho);
      $actualiza->bindParam(':correlativo',$correlativo);
      $actualiza->bindParam(':corrOcurrencia',$corrOcurrencia);
      $actualiza->bindParam(':nuevoRuc',$nuevoRuc);
      $actualiza->execute();
      return  $actualiza->rowCount();
    }

    return $paraVerificarLaTabla;
  }

  function actualizaMonto($db){
    $fchDespacho = $_POST["fchDespacho"];
    $correlativo = $_POST["correlDespacho"];
    $correlRegistro = $_POST["correlRegistro"];
    $monto = $_POST["monto"];

    if ($correlRegistro == '0'){

      $actualiza = $db->prepare("UPDATE `despachovehiculotercero` SET `costoDia` = :monto WHERE `fchDespacho` = :fchDespacho AND `correlativo` =  :correlativo ");
      $actualiza->bindParam(':fchDespacho',$fchDespacho);
      $actualiza->bindParam(':correlativo',$correlativo);
      $actualiza->bindParam(':monto',$monto);
      $actualiza->execute();

      return  $actualiza->rowCount();

    }
  }

  function actualizaComentario($db){
    $fchDespacho = $_POST["fchDespacho"];
    $correlativo = $_POST["correlDespacho"];
    $correlRegistro = $_POST["correlRegistro"];
    $comentario = $_POST["comentario"];

    if ($correlRegistro == '0'){

      $actualiza = $db->prepare("UPDATE `despacho` SET `comentario` = :comentario WHERE `fchDespacho` = :fchDespacho AND `correlativo` =  :correlativo ");
      $actualiza->bindParam(':fchDespacho',$fchDespacho);
      $actualiza->bindParam(':correlativo',$correlativo);
      $actualiza->bindParam(':comentario',$comentario);
      $actualiza->execute();

      return  $actualiza->rowCount();

    }
  }

  function eliminaOcurrencia($db){
    $fchDespacho = $_POST['txtElimFchDespacho'];
    $correlativo = $_POST['txtElimCorrelDespacho'];
    $corrOcurrencia = $_POST['txtElimCorrelRegistro'];

    $elimina = $db->prepare("DELETE FROM  `ocurrenciatercero` WHERE fchDespacho = :fchDespacho AND correlativo = :correlativo AND corrOcurrencia = :corrOcurrencia ");
    $elimina->bindParam(':fchDespacho',$fchDespacho);
    $elimina->bindParam(':correlativo',$correlativo);
    $elimina->bindParam(':corrOcurrencia',$corrOcurrencia);
    $elimina->execute();
    return  $elimina->rowCount();
  }

  function actualizaMontoLiqTercero($db){
    $nroDoc = $_POST["nroDoc"];
    $monto = $_POST["monto"];

    $actualiza = $db->prepare("UPDATE `liqterceromisc` SET `monto` = :monto WHERE `id` = :nroDoc ");
    $actualiza->bindParam(':nroDoc',$nroDoc);
    $actualiza->bindParam(':monto',$monto);
    $actualiza->execute();
    return  $actualiza->rowCount();
  }


  function eliminaTercMiscelaneo($db){
    $nroDoc = $_POST["txtElimNroDoc"];

    $elimina = $db->prepare("DELETE FROM `liqterceromisc` WHERE `id` = :nroDoc");
    $elimina->bindParam(':nroDoc',$nroDoc);
    $elimina->execute();

    if ($elimina->rowCount() == 1){
      $descripcion = utf8_encode("Se elimin� registro de liqterceromisc $nroDoc");
      logAccion($db,$descripcion, NULL, NULL);
    }
    return  $elimina->rowCount();
  }

  function marcaMasivaDespachos($db){
    $fchIni = $_POST["txtMasivoFchInicio"];
    $fchFin = $_POST["txtMasivoFchFin"];
    $docTercero = $_POST['masivoTercero'];

    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `placa`, `costoDia`, `docTercero`, `docPagoTercero`, `docPagoTerTipo`, `pagado`, `guiaTrTercero`, `observPlacaTer`, `fchPago`, `fchCreacion`, `usuario`, `usuarioPagado`  FROM `despachovehiculotercero` WHERE `fchDespacho` between :fchIni AND :fchFin AND docTercero = :docTercero AND docPagoTercero IS NULL");
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->bindParam(':fchFin',$fchFin);
    $consulta->bindParam(':docTercero',$docTercero);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function editaDocPagoTercero($db){
    $docPagoTercero = $_POST["idEditaDoc"];
    $tipoDocLiq = $_POST["txtTipoDoc"] == "" ? NULL : $_POST["txtTipoDoc"];
    $nroDocLiq = $_POST["txtNroDoc"] == "" ? NULL : $_POST["txtNroDoc"];
    $nroNotaCredito = $_POST["txtNroNotaCredito"];
    $nroFactMoy = $_POST["txtFactMoy"];
    $estado = $_POST["cmbEstado"];

    $actualiza = $db->prepare("UPDATE `docpagotercero` SET tipoDocLiq = :tipoDocLiq,  nroDocLiq = :nroDocLiq,`nroNotaCredito` = :nroNotaCredito, `nroFactMoy` = :nroFactMoy, estado = :estado WHERE `docPagoTercero` = :docPagoTercero");
    $actualiza->bindParam(':nroNotaCredito',$nroNotaCredito);
    $actualiza->bindParam(':nroFactMoy',$nroFactMoy);
    $actualiza->bindParam(':docPagoTercero',$docPagoTercero);
    $actualiza->bindParam(':tipoDocLiq',$tipoDocLiq);
    $actualiza->bindParam(':nroDocLiq',$nroDocLiq);
    $actualiza->bindParam(':estado',$estado);
    $actualiza->execute();
    return  $actualiza->rowCount();
  }

  function eliminaDocPagoTercero($db){
    $docPagoTercero = $_POST["idElimDoc"];

    $actualiza = $db->prepare("UPDATE `despachovehiculotercero` SET `docPagoTercero` = NULL , `docPagoTerTipo` = NULL WHERE docPagoTercero = :docPagoTercero AND pagado != 'Si'");
    $actualiza->bindParam(':docPagoTercero',$docPagoTercero);
    $actualiza->execute();
    $cantDespachos = $actualiza->rowCount();

    $actualiza = $db->prepare("UPDATE `combustible` SET `docPagoTercero` = NULL , `docPagoTerTipo` = NULL WHERE docPagoTercero = :docPagoTercero AND pagado != 'Si'");
    $actualiza->bindParam(':docPagoTercero',$docPagoTercero);
    $actualiza->execute();
    $cantCombustible = $actualiza->rowCount();

    $actualiza = $db->prepare("UPDATE `liqterceromisc` SET `docPagoTercero` = NULL , `docPagoTerTipo` = NULL WHERE docPagoTercero = :docPagoTercero AND pagado != 'Si'");
    $actualiza->bindParam(':docPagoTercero',$docPagoTercero);
    $actualiza->execute();
    $cantLiqTercero = $actualiza->rowCount();

    $actualiza = $db->prepare("UPDATE `ccmovimientos` SET `docPagoTercero` = NULL , `docPagoTerTipo` = NULL WHERE docPagoTercero = :docPagoTercero AND pagado != 'Si'");
    $actualiza->bindParam(':docPagoTercero',$docPagoTercero);
    $actualiza->execute();
    $cantCcmovim = $actualiza->rowCount();

    $elimina = $db->prepare("DELETE FROM `docpagotercero` WHERE `docPagoTercero` = :docPagoTercero");
    $elimina->bindParam(':docPagoTercero',$docPagoTercero);
    $elimina->execute();
    $cantEliminados =  $elimina->rowCount();

    $arreglo = Array(
      "cantDespachos"   => $cantDespachos,
      "cantCombustible" => $cantCombustible,
      "cantLiqTercero"  => $cantLiqTercero,
      "cantCcmovim"     => $cantCcmovim,
      "cantEliminados"  => $cantEliminados,
    );
    return $arreglo;
  }

  function generaIdRegMisc($db){
    $anhio = DATE('Y');
    $consulta = $db->prepare("SELECT `id` FROM `liqterceromisc` ORDER BY `id` desc LIMIT 1");
    $consulta->execute();
    $resultado = $consulta->fetchAll();
    foreach($resultado as $item) {
      $ultId = $item['id'];
      $anhioReg = substr($ultId,0,4);
      $nroReg   = 1*substr($ultId,4,6);
      if ($anhio == $anhioReg)
        return $anhioReg.substr("000000".(1+$nroReg),-6);
    }
    return $anhio."000001";
  }

  function addNuevoMisc($db){
    $id = generaIdRegMisc($db);
    $tipoMisc = $_POST["cmbTipoMisc"];

    $fchEvento =  $_POST["txtFchEventoMisc"];
    $placa =  $_POST["txtPlacaMisc"];
    $monto =  $_POST["txtMontoMisc"];
    $descrip =  $_POST["txtDescripMisc"];
    $idMvEncab = NULL;
    $correlMv = NULL;
    $usuario = $_SESSION["usuario"];

    $inserta = $db->prepare("INSERT INTO `liqterceromisc` (`id`, `fchEvento`, `descripcion`, `idPlaca`, `monto`, `tipoMisc`,`idMvEncab`, `correlMv`,`usuario`, `fchCreacion`) VALUES (:id, :fchEvento, :descrip, :placa, :monto, :tipoMisc, :idMvEncab, :correlMv, :usuario, curdate());");
    $inserta->bindParam(':id',$id);
    $inserta->bindParam(':fchEvento',$fchEvento);
    $inserta->bindParam(':descrip',$descrip);
    $inserta->bindParam(':placa',$placa);
    $inserta->bindParam(':monto',$monto);
    $inserta->bindParam(':idMvEncab',$idMvEncab);
    $inserta->bindParam(':correlMv',$correlMv);
    $inserta->bindParam(':tipoMisc',$tipoMisc);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function eliminaOtrosGastos($db){
    $tipo  = $_POST['txtTipoIdAux'];
    $idAux = $_POST['txtIdAux'];

    if ($tipo == 'Vale'){
      $fchCreacion = strtok($idAux, "@");
      $hraCreacion = strtok("@");
      $idPlaca     = strtok("@");

      $elimina = $db->prepare("DELETE FROM `combustible` WHERE `fchCreacion` = :fchCreacion AND `hraCreacion` = :hraCreacion AND `idPlaca` = :idPlaca ");
      $elimina->bindParam(':fchCreacion',$fchCreacion);
      $elimina->bindParam(':hraCreacion',$hraCreacion);
      $elimina->bindParam(':idPlaca',$idPlaca);
      $elimina->execute();

      if ($elimina->rowCount() == 1){
        $descripcion = utf8_encode("Se elimin� registro de combustible $fchCreacion-$hraCreacion-$idPlaca");
        logAccion($db,$descripcion, NULL, $idPlaca);
      }

      return  $elimina->rowCount();

    } else {
      $nroOrden = strtok($idAux, "@");
      $correlativo = strtok("@");
      //ECHO "nro orden $nroOrden, correlativo $correlativo "; 

      $consulta = $db->prepare("SELECT `ccmovimencab`.monto AS montoEncab, `ccmovimientos`.monto FROM `ccmovimencab`, `ccmovimientos` WHERE  `ccmovimencab`.idMovimEncab = `ccmovimientos`.nroOrden AND `ccmovimientos`.nroOrden = :nroOrden AND `ccmovimientos`.correlativo = :correlativo ");
      $consulta->bindParam(':nroOrden',$nroOrden);
      $consulta->bindParam(':correlativo',$correlativo);
      $consulta->execute();
      $auxData = $consulta->fetchAll();

      $montoEncab = $auxData["0"]["montoEncab"];
      $monto = $auxData["0"]["monto"];
      //echo "montoEncab $montoEncab, monto $monto ";
    
      $elimina = $db->prepare("DELETE FROM ccmovimientos WHERE nroOrden = :nroOrden AND correlativo = :correlativo");
      $elimina->bindParam(':nroOrden',$nroOrden);
      $elimina->bindParam(':correlativo',$correlativo);
      $elimina->execute();
      if ($elimina->rowCount() != 1){
        return 0;
      } else {

        if ($elimina->rowCount() == 1){
          $descripcion = utf8_encode("Se elimin� registro de ccmovimientos $nroOrden-$correlativo");
          logAccion($db,$descripcion, NULL, NULL);
        }

        if ($monto == $montoEncab){
          $elimina = $db->prepare("DELETE FROM ccmovimencab WHERE idMovimEncab = :idMovimEncab ");
          $elimina->bindParam(':idMovimEncab',$nroOrden);
          $elimina->execute();

          if($elimina->rowCount() == 1){
            $descripcion = utf8_encode("Se elimin� registro de ccmovimencab $nroOrden");
            logAccion($db,$descripcion, NULL, NULL);
          }
          return  $elimina->rowCount();
        } else {
          $actualiza = $db->prepare("UPDATE ccmovimencab SET monto = monto - $monto, montoOriginal = montoOriginal - $monto WHERE idMovimEncab = :idMovimEncab ");
          $actualiza->bindParam(':idMovimEncab',$nroOrden);
          $actualiza->execute();

          if($actualiza->rowCount() == 1){
            $descripcion = utf8_encode("Se actualiz� registro de ccmovimencab $nroOrden. A $montoEncab se le rest� $monto");
            logAccion($db,$descripcion, NULL, NULL);
          }
          return $actualiza->rowCount();
        }
      }
    }
  }

  function elimOCTercero($db){
    $id = $_POST['txtElimOCTercero'];

    $actualiza = $db->prepare("UPDATE `despachovehiculotercero` SET `docPagoTercero` = NULL, `docPagoTerTipo` = NULL WHERE `docPagoTercero` LIKE :id AND (pagado IS NULL OR pagado = 'No' ) ");
    $actualiza->bindParam(':id',$id);
    $actualiza->execute();
    $actDespVehTerc = $actualiza->rowCount();

    $actualiza = $db->prepare("UPDATE `ccmovimientos` SET `docPagoTercero` = NULL, `docPagoTerTipo` = NULL WHERE `docPagoTercero` LIKE :id AND (pagado IS NULL OR pagado = 'No' ) ");
    $actualiza->bindParam(':id',$id);
    $actualiza->execute();
    $actCcmovim = $actualiza->rowCount();

    $actualiza = $db->prepare("UPDATE `combustible` SET `docPagoTercero` = NULL, `docPagoTerTipo` = NULL WHERE `docPagoTercero` LIKE :id AND (pagado IS NULL OR pagado = 'No' ) ");
    $actualiza->bindParam(':id',$id);
    $actualiza->execute();
    $actCombust = $actualiza->rowCount();

    $actualiza = $db->prepare("UPDATE `liqterceromisc` SET `docPagoTercero` = NULL, `docPagoTerTipo` = NULL WHERE `docPagoTercero` LIKE :id AND (pagado IS NULL OR pagado = 'No' ) ");
    $actualiza->bindParam(':id',$id);
    $actualiza->execute();
    $actLiqTercMisc = $actualiza->rowCount();

    $actualiza = $db->prepare("UPDATE `ocurrenciatercero` SET `docPagoTercero` = NULL, `docPagoTerTipo` = NULL WHERE `docPagoTercero` LIKE :id AND (pagado IS NULL OR pagado = 'No' ) ");
    $actualiza->bindParam(':id',$id);
    $actualiza->execute();
    $actLiqTercMisc = $actualiza->rowCount();


    $elimina = $db->prepare("DELETE FROM `docpagotercero` WHERE `docPagoTercero` LIKE :id AND estado = 'pendiente'");
    $elimina->bindParam(':id',$id);
    $elimina->execute();
    //$actLiqTercMisc = $elimina->rowCount();

    return $elimina->rowCount();

  }

  function elimDocIdentidad($db){

    $tipoDocTrab = $_POST['txtElimTipoDocIdentidad'];
    $nroDocTrab = $_POST['txtElimDocIdentidad'];
    $fchIni = $_POST['txtElimFchIniDocIdentidad'];
    $adjunto =  $_POST['adjuntoElim'];
    $idTrabajador =  $_POST['idElimTrabajador'];

    $elimina = $db->prepare("DELETE FROM `trabajdocumentos` WHERE `nroDocTrab` = :nroDocTrab AND `tipoDocTrab` = :tipoDocTrab AND `fchIni` = :fchIni");
    $elimina->bindParam(':tipoDocTrab',$tipoDocTrab);
    $elimina->bindParam(':nroDocTrab',$nroDocTrab);
    $elimina->bindParam(':fchIni',$fchIni);
    $elimina->execute();

    if ($elimina->rowCount() == 1){
      //$ruta = "../imagenes/data/trabajador/".$adjunto;
      if (file_exists($adjunto)) unlink($adjunto);
    }

    if($elimina->rowCount() > 0){
      $descripcion = utf8_encode("Se elimin� el documento de identidad $nroDocTrab de tipo $tipoDocTrab y con fchInicio $fchIni. Este documento pertenec�a al trabajador $idTrabajador");
      logAccion($db,$descripcion, $idTrabajador, NULL);
    }

    return $elimina->rowCount();
  }

  function buscarDocPagoTercero($db,$docPagoTercero){
    $consulta = $db->prepare("SELECT `docPagoTercero` , `tipoDocumento`, `tipoDocLiq` , `nroDocLiq` , `fchDocLiq` , `docTercero` , `estado` , `tipoDocAnexo` , `idDocAnexo` , `nroNotaCredito` , `nroFactMoy` , `fchCancelacion` , `formaPago` , `nro_Oper_o_Cheque` , `usuarioCerroLiquid` , `docpagoter`.`usuario` , `docpagoter`.`fchCreacion` , `docpagoter`.`hraCreacion`, nombreCompleto, cuentaDetraccion, bancoNombre, bancoCuentaNro, eMail FROM `docpagotercero` AS docpagoter, vehiculodueno AS vehdue WHERE `docpagoter`.docTercero = `vehdue`.documento AND docpagoter.`docPagoTercero` LIKE :docPagoTercero");
    
    $consulta->bindParam(':docPagoTercero',$docPagoTercero);
    $consulta->execute();
    return $consulta->fetch();
  }

  function buscarDespacho($db){
    $fchDespacho = $_POST["fchDespacho"];
    $correlativo = $_POST["correlativo"];

    $consulta = $db->prepare("SELECT des.`fchDespacho`, des.`correlativo`, `idProgramacion`, `guiaCliente`, `hraInicio`, `fchDespachoFinCli`, `fchDespachoFin`, `hraFin`, des.`placa`, `considerarPropio`, `m3`, `valorServicio`, `igvServicio`, `idCliente`, `cuenta`, `correlCuenta`, `tipoServicioPago`, `nroAuxiliares`, `nroAuxiliaresAdic`, `costo`, `topeServicioHraNormal`, `hrasNormales`, `tolerHrasNormales`, `costoHraExtra`, `toleranCobroHraExtra`, `valorConductor`, `hraNormCondDespacho`, `tolerHraCondDespacho`, `valHraAdicCondDespacho`, `valorAuxiliar`, `hraNormAuxDespacho`, `tolerHraAuxDespacho`, `valHraAdicAuxDespacho`, `nroAuxiliaresCuenta`, `valorAuxAdicional`, `usarMaster`, `tpoExtraHras`, `costoTotal`, `nroGuias`, `nroDespachos`, `usaReten`, `concluido`, `hraInicioBase`, `kmInicio`, `kmInicioCliente`, `kmFinCliente`, `hraFinCliente`, `lugarFinCliente`, `kmFin`, `recorridoEsperado`, `zonaDespacho`, `ptoOrigen`, `idOrigen`, `idSedeOrigen`, `tipoDestino`, `ptoDestino`, `observacion`, `observCliente`, cobDes.`docCobranza` AS cobDespacho, cobDes.`tipoDoc` AS tipoDocCobDesp, des.`pagado`, `pagoEnPlanilla`, `terceroConPersonalMoy`, `contarDespacho`, `superCuenta`, des.`usuario`, des.`fchCreacion`, `modoCreacion`, `estadoDespacho`, `usuarioGrabaFin`, `fchGrabaFin`, `hraGrabaFin`, cobDes.`usuarioCreaCobranza` AS usuCreaCobDesp, cobDes.`fchCreaCobranza` AS fchCreaCobDesp, `idProducto`, `valKmAdic`, `hraIniEsperado`, `valAdicHraIniEsper`, `hraFinEsperado`, `valAdicHraFinEsper`, `cobrarPeaje`, `cobrarRecojoDevol`, `valUnidTercCCond`, `valUnidTercSCond`, `hrasNormalTerc`, `tolerHrasNormalTerc`, `valHraExtraTerc`, `valKmAdicTerc`, `tolerKmEsperado`, `movilAsignado`, `desper`.valorRol AS valConductor, `desper`.pagado AS pagadoConductor , group_concat( `desper2`.valorRol ) AS valAuxiliar,  group_concat( `desper2`.pagado ) AS pagadoAuxiliar, `desvehter`.pagado AS pagadoVehTer, group_concat(concat(cobOtros.`codigo`, ' ',cobOtros.`docCobranza`, ' ', cobOtros.`tipoDoc` )) AS cobOtros FROM `despacho` AS des LEFT JOIN despachopersonal AS desper ON `des`.fchDespacho = `desper`.fchDespacho AND `des`.correlativo = `desper`.correlativo AND `desper`.tipoRol = 'Conductor' LEFT JOIN  despachopersonal AS desper2 ON `des`.fchDespacho = `desper2`.fchDespacho AND `des`.correlativo = `desper2`.correlativo AND `desper2`.tipoRol = 'Auxiliar' LEFT JOIN `despachovehiculotercero` AS desvehter ON `des`.fchDespacho = `desvehter`.fchDespacho AND `des`.correlativo = `desvehter`.correlativo LEFT JOIN despachodetallesporcobrar AS cobDes ON `des`.fchDespacho = `cobDes`.fchDespacho AND `des`.correlativo = `cobDes`.correlativo AND `cobDes`.codigo = 'Despacho'  LEFT JOIN despachodetallesporcobrar AS cobOtros ON `des`.fchDespacho = `cobOtros`.fchDespacho AND `des`.correlativo = `cobOtros`.correlativo AND `cobOtros`.codigo != 'Despacho' WHERE des.`fchDespacho` = :fchDespacho AND des.`correlativo` = :correlativo ");

    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    return $consulta->fetch();

  }

  function buscarCliCuentas($db){
    $idCliente = $_POST["idCliente"];
    $estadoCuenta = $_POST["estadoCuenta"];

    $consulta = $db->prepare("SELECT `idCliente`, `correlativo`, `nombreCuenta`, `estadoCuenta`, `tipoCuenta`, `paraMovil` FROM `clientecuentanew` WHERE idCliente = :idCliente AND estadoCuenta = :estadoCuenta ");

    $consulta->bindParam(':idCliente',$idCliente);
    $consulta->bindParam(':estadoCuenta',$estadoCuenta);
    $consulta->execute();
    $auxData = $consulta->fetchAll();

    $cadena = "<option>-</option>";

    foreach ($auxData as $key => $value) {
      $cadena .= "<option value = '".$value["correlativo"]."' >".$value["nombreCuenta"]."</option>";
    }

    return $cadena;

  }

  function buscarCliCueProd($db){
    $correlativo = $_POST["correlativo"];
    $idProducto = $_POST["idProducto"];
    $idCliente = $_POST["idCliente"];
    $estadoProducto = $_POST["estadoProducto"];
    $consulta = $db->prepare("SELECT `idProducto`, `idCliente`, `correlativo`, `nombProducto`, `m3Facturable`, `puntos`, `idZona`, `zona`, `tipoProducto`, `estadoProducto`, `precioServ`, `kmEsperado`, `tolerKmEsperado`, `valKmAdic`, `hrasNormales`, `tolerHrasNormales` FROM `clientecuentaproducto` WHERE `idCliente` LIKE :idCliente AND `correlativo` LIKE :correlativo AND estadoProducto = :estadoProducto ");

    $consulta->bindParam(':idCliente',$idCliente);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->bindParam(':estadoProducto',$estadoProducto);
    $consulta->execute();
    $auxData = $consulta->fetchAll();
    $cadena = "<option>-</option>";
    foreach ($auxData as $key => $value){
      $cadena .= "<option value = '".$value["idProducto"]."' ";
      if ($idProducto == $value["idProducto"])  $cadena .= " SELECTED ";
      $cadena .= " >".$value["nombProducto"]."</option>";
    }

    return $cadena;
  }

  function buscarDataIdProducto($db){
    $idProducto = $_POST["idProducto"];
    $consulta = $db->prepare("SELECT `idProducto`, `idCliente`, `correlativo`, `nombProducto`, `m3Facturable`, `puntos`, `idZona`, `zona`, `tipoProducto`, `estadoProducto`, `precioServ`, `kmEsperado`, `tolerKmEsperado`, `valKmAdic`, `hrasNormales`, `tolerHrasNormales`, `valHraAdic`, `hraIniEsperado`, `tolerHraIniEsperado`, `valAdicHraIniEsper`, `hraFinEsperado`, `tolerHraFinEsperado`, `valAdicHraFinEsper`, `nroAuxiliares`, `valAuxiliarAdic`, `cobrarPeaje`, `cobrarRecojoDevol`, `valConductor`, `hraNormalConductor`, `tolerHraCond`, `valHraAdicCond`, `valAuxiliar`, `hraNormalAux`, `tolerHraAux`, `valHraAdicAux`, `usoMaster`, `valUnidTercCCond`, `valUnidTercSCond`, `hrasNormalTerc`, `tolerHrasNormalTerc`, `valHraExtraTerc`, `valKmAdicTerc`, `valAuxTercero`, `contarDespacho`, `superCuenta`, `creacUsuario`, `creacFch`, `editUsuario`, `editFch` FROM `clientecuentaproducto` WHERE `idProducto` LIKE :idProducto ");

    $consulta->bindParam(':idProducto',$idProducto);
    $consulta->execute();
    return  $consulta->fetch();


  }

  function insertaNuevaGuiaPorte($db){
    $user = $_SESSION['usuario'];
    $guiaPorte = $_POST["txtNvoGuiaPorteNroSerie"]."-".$_POST["txtNvoGuiaPorteCorrelativo"];
    $fchDespacho = $_POST["fchDespachoNvoGuiaPte"];
    $correlativo = $_POST["correlativoNvoGuiaPte"];

    $inserta = $db->prepare("INSERT INTO `despachoguiaporte` (`guiaPorte`, `fchDespacho`, `correlativo`, `usuario`, `fchCreacion`) VALUES (:guiaPorte, :fchDespacho, :correlativo, :usuario, curdate()) ");
    $inserta->bindParam(':guiaPorte',$guiaPorte);
    $inserta->bindParam(':fchDespacho',$fchDespacho);
    $inserta->bindParam(':correlativo',$correlativo);
    $inserta->bindParam(':usuario',$user);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function editaGuiaPorte($db){
    $user = $_SESSION['usuario'];
    $guiaPorte = $_POST["txtEditaGuiaPorteNroSerie"]."-".$_POST["txtEditaGuiaPorteCorrelativo"];
    $guiaPorteIni = $_POST["guiaPorteIni"];

    $actualiza = $db->prepare("UPDATE `despachoguiaporte` SET `guiaPorte` = :guiaPorte, `editaUsuario` = :usuario WHERE `guiaPorte` = :guiaPorteIni ");
    $actualiza->bindParam(':guiaPorte',$guiaPorte);
    $actualiza->bindParam(':guiaPorteIni',$guiaPorteIni);
    $actualiza->bindParam(':usuario',$user);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function eliminaGuiaPorte($db){
    $guiaPorte = $_POST["guiaPorteIniElim"];

    $elimina = $db->prepare("DELETE FROM `despachoguiaporte` WHERE `guiaPorte` = :guiaPorte ");
    $elimina->bindParam(':guiaPorte',$guiaPorte);
    $elimina->execute();
    return $elimina->rowCount();
  }

  function buscarDataPlacaAbast($db){
    $placa = $_POST["placa"];
    $fecha = $_POST["fecha"];
    $consulta = $db->prepare("SELECT IF(ve.tipoCombust IS NULL, 'Falta definir el tipo de combustible', ve.tipoCombust) AS combustible, IF(MAX(ia.kilometraje_actual) IS NULL, 0, MAX(ia.kilometraje_actual)) AS kilometraje 
    FROM incidencias_abastecimiento ia INNER JOIN vehiculo v ON v.idPlaca = :placa INNER JOIN vehiculoeficsoleskm ve ON ve.id = v.idEficSolesKm WHERE ia.placa = :placa AND ia.fecha < :fecha");
    $consulta->bindParam(':placa',$placa);
    $consulta->bindParam(':fecha',$fecha);
    $consulta->execute();
    return $consulta->fetch();
  }

  function tCombustible($db){
    $consulta = $db->prepare("SELECT DISTINCT tipoCombustible FROM `grifo_tcombustible`");
    $consulta->bindParam(':idCliente',$idCliente);
    $consulta->execute();
    $cadena = "<option>SELECCIONE EL COMBUSTIBLE</option>";
    $data = $consulta->fetchAll();
    foreach ($data as $key => $value) {
      $cadena .= "<option>".$value['tipoCombustible']."</option>";
    }
    return $cadena;
  }

  function uMedida($db){
    $grifo = $_POST["grifo"];
    $combustible = $_POST["combustible"];
    if($grifo != '' && $combustible != ''){
      $consulta = $db->prepare("SELECT gt.undMedida FROM grifo_tcombustible gt JOIN (SELECT ruc FROM grifos g WHERE g.nombre = :grifo) AS g WHERE gt.ruc = g.ruc AND tipoCombustible = :combustible");
      $consulta->bindParam(':grifo',$grifo);
      $consulta->bindParam(':combustible',$combustible);
      $consulta->execute();
      $data = $consulta->fetchAll();
      foreach ($data as $key => $value) {
        $cadena = $value['undMedida'];
      }
      return $cadena;
    }
    else{
      $consulta = $db->prepare("SELECT DISTINCT undMedida FROM `grifo_tcombustible`");
      $consulta->bindParam(':idCliente',$idCliente);
      $consulta->execute();
      $cadena = "<option>SELECCIONE LA UNIDAD DE MEDIDA</option>";
      $data = $consulta->fetchAll();
      foreach ($data as $key => $value) {
        $cadena .= "<option>".$value['undMedida']."</option>";
      }
      return $cadena;
    }
  }

  function grifos($db){
    $combustible = $_POST["combustible"];
    if($combustible != ''){
      $consulta = $db->prepare("SELECT g.nombre FROM grifo_tcombustible gt INNER JOIN grifos g ON gt.ruc = g.ruc WHERE tipoCombustible = :combustible");
      $consulta->bindParam(':combustible',$combustible);
    }
    else{
      $consulta = $db->prepare("SELECT DISTINCT nombre FROM `grifos`");
    }
    $consulta->execute();
    $cadena = "<option>SELECCIONE EL GRIFO</option>";
    $data = $consulta->fetchAll();
    foreach ($data as $key => $value) {
      $cadena .= "<option>".$value['nombre']."</option>";
    }
    return $cadena;
  }


  function generaCorrelPunto($db,$fchDespacho, $correlativo){
    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `ordenPunto`, `tipoPunto`, `nombComprador`, `distrito`, `direccion`, `nroGuiaPorte`, `estado`, `hraLlegada`, `hraSalida`, `observacion`, `foto_1`, `foto_2`, `foto_3`, `foto_4`, `punto`, `placa`, `km`, `creacFch` FROM `despachopuntos` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ORDER BY ordenPunto DESC LIMIT 1 ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $data = $consulta->fetchAll();
    $ordenPunto = 1;
    foreach ($data as $key => $value) {
      $ordenPunto = $value['ordenPunto'] + 1;
    }
    return $ordenPunto;
  }

  function nuevoTCombustible($db){
    $usuario = $_SESSION["usuario"];
    $ruc = $_POST["rucGrifoNvoTipoCombustible"];
    $tipoCombustible = $_POST["txtTCombustibleNTC"];
    $undMedida = $_POST["txtUMedidaNTC"];
    $precio = $_POST["txtPrecioNTC"];
    $inserta = $db->prepare("INSERT INTO `grifo_tcombustible` (`tipoCombustible`, `undMedida`, `precio`, `ruc`, `usuario`) VALUES (:tipoCombustible, :undMedida, :precio, :ruc, :usuario)");
    $inserta->bindParam(':ruc',$ruc);
    $inserta->bindParam(':tipoCombustible',$tipoCombustible);
    $inserta->bindParam(':undMedida',$undMedida);
    $inserta->bindParam(':precio',$precio);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function editarTCombustible($db){
    $usuario = $_SESSION["usuario"];
    $idTCombustible = $_POST["idTipoCombustibleEditar"];
    $precio = $_POST["txtPrecioNTCEditar"];
    $actualiza = $db->prepare("UPDATE `grifo_tcombustible` SET `precio` = :precio, `usuario` = :usuario WHERE `idTCombustible` = :idTCombustible");
    $actualiza->bindParam(':idTCombustible',$idTCombustible);
    $actualiza->bindParam(':precio',$precio);
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function eliminarTCombustible($db){
    $idTCombustible = $_POST['idTipoCombustibleEliminar'];
    $elimina = $db->prepare("DELETE FROM `grifo_tcombustible` WHERE `idTCombustible` = :idTCombustible");
    $elimina->bindParam(':idTCombustible',$idTCombustible);
    $elimina->execute();
    return  $elimina->rowCount();
  }

  function nuevoNPeaje($db){
    $usuario = $_SESSION["usuario"];
    $id_redpeaje = $_POST["id_redpeaje"];
    $nombrepeaje = $_POST["txtNPeaje"];
    $direccion = $_POST["txtDireccion"];
    $inserta = $db->prepare("INSERT INTO `nombrepeaje` (`nombrepeaje`, `direccion`, `id_redpeaje`, `usuario_registro`) VALUES (:nombrepeaje, :direccion, :id_redpeaje, :usuario_registro)");
    $inserta->bindParam(':nombrepeaje',$nombrepeaje);
    $inserta->bindParam(':direccion',$direccion);
    $inserta->bindParam(':id_redpeaje',$id_redpeaje);
    $inserta->bindParam(':usuario_registro',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function editarNPeaje($db){
    $usuario = $_SESSION["usuario"];
    $id_nombrepeaje = $_POST["id_nombrepeajeEditar"];
    $nombrepeaje = $_POST["txtNPeajeEditar"];
    $direccion = $_POST["txtDireccionEditar"];
    $actualiza = $db->prepare("UPDATE `nombrepeaje` SET `nombrepeaje` = :nombrepeaje, `direccion` = :direccion, `usuario_edicion` = :usuario_edicion WHERE `id_nombrepeaje` = :id_nombrepeaje");
    $actualiza->bindParam(':id_nombrepeaje',$id_nombrepeaje);
    $actualiza->bindParam(':nombrepeaje',$nombrepeaje);
    $actualiza->bindParam(':direccion',$direccion);
    $actualiza->bindParam(':usuario_edicion',$usuario);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function eliminarNPeaje($db){
    $id_nombrepeaje = $_POST['id_nombrepeajeEliminar'];
    $elimina = $db->prepare("DELETE FROM `nombrepeaje` WHERE `id_nombrepeaje` = :id_nombrepeaje");
    $elimina->bindParam(':id_nombrepeaje',$id_nombrepeaje);
    $elimina->execute();
    return  $elimina->rowCount();
  }

  function generarNuevoIdPunto($db){
    $consulta = $db->prepare("SELECT idPunto FROM `despachopuntos` ORDER BY idPunto DESC LIMIT 1 ");
    $consulta->execute();
    $aux = $consulta->fetchAll();
    $nvoIdPunto = 1;
    foreach ($aux as $item) {
      $ultNumero = $item['idPunto'];
      $nvoIdPunto = 1*$ultNumero + 1;
    }
    return $nvoIdPunto;
  }


  function nuevoPunto($db){
    $usuario = $_SESSION["usuario"];
    $idPunto     = generarNuevoIdPunto($db);
    $fchDespacho =  $_POST["fchDespachoNvoPunto"];  $correlativo = $_POST["correlativoNvoPunto"];
    $idProgramacion =  $_POST["idProgramacionNvoPunto"];
    $creacModo = $_POST["estadoDespNvoPunto"];
    $idRuta  =  $idProgramacion;
    $ordenPunto = generaCorrelPunto($db,$fchDespacho, $correlativo);

    $tipoPunto   = $_POST["cmbNuevoTipoPunto"];     $hraEsperada = $_POST["txtHraEsperada"];
    $guiaCliente = $_POST["txtGuiaCliente"];        $nombComprador = $_POST["txtNuevoNombCli"];
    $direccion   = $_POST["txtDirCliente"];         $referencia  = $_POST["txtReferencia"];
    $auxDistrito = $_POST["txtNuevoDistCli"];       $idDistrito = strtok($auxDistrito, "-");
    $distrito = strtok("-");
    $provincia  = $_POST["txtNuevoProvCli"];        $telefono = $_POST["txtNuevoTelfCli"];
    $idCarga  = $_POST["txtNuevoIdCargaCli"];       $estado = $_POST["cmbNuevoPuntoEstado"];
    $subEstado= $_POST["cmbNuevoPuntoSubestado"];   $observacion = $_POST["txtObservacion"];

    ////////////////
    $inserta = $db->prepare("INSERT INTO `despachopuntos` (`idPunto`, `idProgramacion`, `idRuta`, `fchDespacho`, `correlativo`, `ordenPunto`, `tipoPunto`, `nombComprador`, `idDistrito`, `distrito`, `provincia`, `direccion`, `guiaCliente`, `estado`, `subEstado`, `hraLlegada`, `observacion`, `fchPunto`, `referencia`, `telfReferencia`, `idCarga`,  `creacModo` ,  `creacFch`, `creacUsuario`) VALUES (:idPunto, :idProgramacion, :idRuta, :fchDespacho, :correlativo, :ordenPunto, :tipoPunto, :nombComprador, :idDistrito, :distrito, :provincia, :direccion, :guiaCliente, :estado, :subEstado, :hraLlegada, :observacion, :fchPunto, :referencia, :telfRef, :idCarga, :creacModo, now(), :usuario)");

    //echo " idPunto $idPunto, fchDespacho $fchDespacho, correlativo $correlativo, correlPunto $correlPunto, tipoPunto $tipoPunto, nombComprador $nombComprador, idDistrito $idDistrito,   distrito $distrito, provincia $provincia, direccion $direccion, guiaCliente $guiaCliente, estado $estado, subEstado $subEstado, hraLlegada $hraEsperada, observacion $observacion, fchPunto $fchDespacho, referencia $referencia, telfRef $telefono, idCarga $idCarga, usuario $usuario";

    ////////////////
    $inserta->bindParam(':idPunto',$idPunto);         $inserta->bindParam(':fchDespacho',$fchDespacho);
    $inserta->bindParam(':idProgramacion',$idProgramacion); $inserta->bindParam(':idRuta',$idRuta);
    $inserta->bindParam(':correlativo',$correlativo); $inserta->bindParam(':ordenPunto',$ordenPunto);
    $inserta->bindParam(':tipoPunto',$tipoPunto);     $inserta->bindParam(':nombComprador',$nombComprador);
    $inserta->bindParam(':idDistrito',$idDistrito);   $inserta->bindParam(':distrito',$distrito);
    $inserta->bindParam(':provincia',$provincia);     $inserta->bindParam(':direccion',$direccion);
    $inserta->bindParam(':guiaCliente',$guiaCliente); $inserta->bindParam(':estado',$estado);
    $inserta->bindParam(':subEstado',$subEstado);     $inserta->bindParam(':hraLlegada',$hraEsperada);
    $inserta->bindParam(':observacion',$observacion); $inserta->bindParam(':fchPunto',$fchDespacho);
    $inserta->bindParam(':referencia',$referencia);   $inserta->bindParam(':telfRef',$telefono);
    $inserta->bindParam(':idCarga',$idCarga);         $inserta->bindParam(':usuario',$usuario);
    $inserta->bindParam(':creacModo',$creacModo);
    $inserta->execute();
    return $inserta->rowCount();

  }

  function editaPunto($db){
    $usuario = $_SESSION['usuario'];
    $idPunto = $_POST["idPuntoEdita"];
    //$correlativo = $_POST["correlativoEditaPunto"];
    //$correlPunto = $_POST["correlPuntoEditaPunto"];
    $tipoPunto  = $_POST["cmbEditaTipoPunto"];
    $nombComprador = $_POST["txtEditaPuntoNombCli"];
    $auxDistrito = $_POST["txtEditaPuntoDist"];
    $idDistrito = strtok($auxDistrito, "-");
    $distrito   = strtok("-");
    $direccion  = "";
    $nroGuiaPorte = "";
    $estado     = $_POST["cmbEditaPuntoEstado"];
    $subEstado  = $_POST["cmbEditaPuntoSubestado"];  
    $hraLlegada = $_POST["txtEditaHraLlegada"];
    $hraSalida  = $_POST["txtEditaHraSalida"];
    $observacion = $_POST["txtEditaObservacion"];
    $editaModo  = $_POST["estadoDespEditatPto"];
    $foto_1     = "";
    $foto_2     = "";

    //echo "usuario $usuario, idPunto $idPunto, tipoPunto $tipoPunto, nombComprador $nombComprador, auxDistrito $auxDistrito, idDistrito $idDistrito, distrito $distrito, direccion $direccion, nroGuiaPorte $nroGuiaPorte, estado $estado, subEstado $subEstado, hraLlegada $hraLlegada, hraSalida $hraSalida, observacion $observacion, foto_1 $foto_1, $foto_2 $foto_2";

    $actualiza = $db->prepare("UPDATE `despachopuntos` SET `tipoPunto` = :tipoPunto, `nombComprador` = :nombComprador, `idDistrito` = `idDistrito`, `distrito` = `distrito`, `direccion` = `direccion`, `nroGuiaPorte` = `nroGuiaPorte`, `estado` = :estado, `subEstado` = :subEstado, `hraLlegada` = :hraLlegada, `hraSalida` = :hraSalida, `foto_1` = :foto_1, `foto_2` = :foto_2, `observacion` = :observacion, editaModo = :editaModo, editaFch = curdate(), editaUsuario = :usuario WHERE `idPunto` = :idPunto ");
    $actualiza->bindParam(':idPunto',$idPunto);
    $actualiza->bindParam(':tipoPunto',$tipoPunto);
    $actualiza->bindParam(':nombComprador',$nombComprador);
    //$actualiza->bindParam(':idDistrito',$idDistrito);
    //$actualiza->bindParam(':distrito',$distrito);
    //$actualiza->bindParam(':direccion',$direccion);
    $actualiza->bindParam(':estado',$estado);
    $actualiza->bindParam(':subEstado',$subEstado);
    $actualiza->bindParam(':hraLlegada',$hraLlegada);
    $actualiza->bindParam(':hraSalida',$hraSalida);
    $actualiza->bindParam(':observacion',$observacion);
    $actualiza->bindParam(':editaModo',$editaModo);
    $actualiza->bindParam(':foto_1',$foto_1);
    $actualiza->bindParam(':foto_2',$foto_2);
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function eliminaPunto($db){
    $idPunto = $_POST['txtEliminaIdPunto'];

    $tipoPunto = $_POST['txtEliminaPuntoTipo'];
    $nombClientePunto = $_POST['txtEliminaPuntoNombCli'];
    $distritoPunto = $_POST['txtEliminaPuntoDist'];



    //$correlativo = $_POST['correlativoEliminaPunto'];
    //$correlPunto = $_POST['txtEliminaCorrelPunto'];
    $elimina = $db->prepare("DELETE FROM `despachopuntos` WHERE `idPunto` = :idPunto ");
    $elimina->bindParam(':idPunto',$idPunto);
    //$elimina->bindParam(':correlativo',$correlativo);
    //$elimina->bindParam(':correlPunto',$correlPunto);
    $elimina->execute();
    return  $elimina->rowCount();
    if ($elimina->rowCount() == 1){
      $descripcion = utf8_encode("Se elimin� un punto: $idPunto - $tipoPunto, $nombClientePunto, $distritoPunto");
      logAccion($db,$descripcion, NULL, NULL);
    }
    return  $elimina->rowCount();
  }

  function puntosEstados($db){
    $consulta = $db->prepare("SELECT DISTINCT estado FROM `despachopuntosestados` ");
    $consulta->execute();
    $cadena = "<option>-</option>";
    $data = $consulta->fetchAll();
    foreach ($data as $key => $value) {
      $cadena .= "<option>".$value['estado']."</option>";
    }
    return $cadena;
  }

  function puntosSubestados($db){
    $estado = $_POST["elegido"];

    $consulta = $db->prepare("SELECT estado, subestado FROM `despachopuntosestados` WHERE `estado` LIKE :estado ");
    $consulta->bindParam(':estado',$estado);
    $consulta->execute();
    $cadena = "<option>-</option>";
    $data = $consulta->fetchAll();
    foreach ($data as $key => $value) {
      $cadena .= "<option>".$value['subestado']."</option>";
    }
    return $cadena;
  }

  function buscarDespPuntos($db){
    /*$fchDespacho = $_POST["fchDespacho"];
    $correlativo = $_POST["correlativo"];
    $correlPunto = $_POST["correlPunto"];*/
    $idPunto = $_POST["idPunto"];
    $consulta = $db->prepare("SELECT `idPunto`, `fchDespacho`, `correlativo`, `correlPunto`, `tipoPunto`, `nombComprador`, `idDistrito`, `distrito`, `direccion`, `nroGuiaPorte`, `estado`, `subEstado`, `hraLlegada`, `hraSalida`, `observacion`, `foto_1`, `foto_2`, `foto_3`, `foto_4`, `punto`, `placa`, `km` FROM `despachopuntos` WHERE `idPunto` = :idPunto  ");

    $consulta->bindParam(':idPunto',$idPunto);
    /*$consulta->bindParam(':correlativo',$correlativo);
    $consulta->bindParam(':correlPunto',$correlPunto);*/
    $consulta->execute();
    return $consulta->fetch();

  }

  function editaDespacho($db){
    $usuario = $_SESSION['usuario'];
    //OJO, necesito el nivel de acceso del usuario
    
    $fchDespacho = $_POST["fchDespachoEditaDesp"]; //ok
    $correlativo = $_POST["correlativoEditaDesp"]; //ok
    $m3 = $_POST["txtEditaM3Facturable"]; //ok
    $hraIniBase = $_POST["txtEditaHraIniBase"];
    $kmInicio = $_POST["txtEditaKmInicio"]; //ok
    $hraInicio = $_POST["txtEditaHraInicio"];
    $kmInicioCliente = $_POST["txtEditaKmInicioCliente"];  //ok
    //echo "$hraIniBase, $kmInicio, $hraInicio, $kmInicioCliente ";
    $fchDespFinCli = $_POST["txtEditaFchDespFinCli"];
    $hraFinCli = $_POST["txtEditaHraFinCli"];
    $kmFinCli = $_POST["txtEditaKmFinCli"]; //ok
    $fchDespFin = $_POST["txtEditaFchDespFin"];
    $hraFin = $_POST["txtEditaHraFin"];
    $kmFin = $_POST["txtEditaKmFin"];  //ok

    //echo "$fchDespFinCli, $hraFinCli, $kmFinCli, $fchDespFin, $hraFin, $kmFin";
    $valorServ = $_POST["txtEditaValorServ"];
    $cambioValorServ = $_POST["txtEditaCambioValorServ"];
    $valorConductor = $_POST["txtEditaValorConductor"];
    $cambioValorConductor = $_POST["txtEditaCambioValorConductor"];
    $valorAuxiliar = $_POST["txtEditaValorAuxiliar"];
    $cambioValorAuxiliar = $_POST["txtEditaCambioValorAuxiliar"];

    $placa = $_POST["txtEditaPlaca"];
    $auxCliente = $_POST["txtEditaCliente"]; //debe separarse el idCliente
    $idCuenta = $_POST["cmbEditaCliCuenta"];
    $idProducto = $_POST["cmbEditaCliProducto"];
    $tipoServicio = $_POST["txtEditaTipoServicio"];
    $fchDespIniMoy = $_POST["txtEditaFchDespIniMoy"];
    $hraIniBase = $_POST["txtEditaHraIniBase"];
    $kmInicio = $_POST["txtEditaKmInicio"];
    $fchDesp1erPto = $_POST["txtEditaFchDesp1erPto"];

    /*
    aqu� tendr�a que ir todo un tema de validaciones
    */
    $pagoConductor = $_POST["pagoConductor"];
    $pagoAuxiliares = $_POST["pagoAuxiliares"];
    $vehiculoTercero = $_POST["vehiculoTercero"];
    $cobroDespacho = $_POST["cobroDespacho"];
    $cobroOtros = $_POST["cobroOtros"];
    /*  */

    if ($cambioValorServ == "")  $valorServicio = $valorServ; else $valorServicio = $cambioValorServ;
    
    /*
      Cambio en el despacho
      Se tendr�a que validar que solo cambie caracter�sticas generales del viaje como kilometraje recorrido, horas de inicio y finde todo el viaje, pero ya no cambia info de la tripulaci�n.
      No debe de haberse generado pago a terceros y ni cobranzas.  
    */

    if($vehiculoTercero == "Si" || $cobroDespacho != "" || $cobroOtros != ''){
      return -1;//No se puede

    } else {

      $actualiza = $db->prepare("UPDATE `despacho` SET m3 = :m3, `valorServicio` = :valorServicio , `kmInicio` = :kmInicio, `kmInicioCliente` = :kmInicioCliente, `kmFinCliente` = :kmFinCli, `kmFin` = :kmFin, editaUsuarioDesp = :usuario WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
      $actualiza->bindParam(':m3',$m3);
      $actualiza->bindParam(':valorServicio',$valorServicio);
      $actualiza->bindParam(':kmInicio',$kmInicio);
      $actualiza->bindParam(':kmInicioCliente',$kmInicioCliente);
      $actualiza->bindParam(':kmFinCli',$kmFinCli);
      $actualiza->bindParam(':kmFin',$kmFin);
      $actualiza->bindParam(':usuario',$usuario);
      $actualiza->bindParam(':fchDespacho',$fchDespacho);
      $actualiza->bindParam(':correlativo',$correlativo);

      $actualiza->execute();
      return $actualiza->rowCount();
    }


  }

  function insertaTrip($db){
    $usuario      = $_SESSION["usuario"];
    $fchDespacho  = $_POST['fchDespachoNvoTrip'];
    $correlativo  = $_POST['correlativoNvoTrip'];
    $auxTrabajador = $_POST['txtNvoTrabajador'];
    $tipoRol      = $_POST['cmbNvoTrabTipoRol'];
    $trabHraInicio =  $_POST['txtNvoTrabHraInicio'];
    $trabFchDespachoFin =  $_POST['txtNvoTrabFchFin'];
    $trabHraFin   =  $_POST['txtNvoTrabHraFin'];
    $observPersonal=  $_POST['txtObservacion'];
    $modoCreacDp=  $_POST['estadoDespNvoTrip'];

    $idTrabajador = strtok($auxTrabajador , "-");

    ////////////
    //echo "fchDespacho: $fchDespacho, correlativo: $correlativo, idTrabajador: $idTrabajador, trabFchDespachoFin: $trabFchDespachoFin, trabHraInicio: $trabHraInicio, trabHraFin: $trabHraFin, tipoRol: $tipoRol, usuario: $usuario"; 
    ////////////

    $inserta = $db->prepare("INSERT INTO `despachopersonal` (`fchDespacho`, `correlativo`, `idTrabajador`,  `trabFchDespachoFin`, `trabHraInicio`, `trabHraFin`, `tipoRol`, `pagado`,  `usuario`,  `ultProceso`, `ultProcesoFch`, `ultProcesoUsuario`, `observPersonal`, creacFchDp, creacUsuDp, modoCreacDp) VALUES (:fchDespacho, :correlativo, :idTrabajador, :trabFchDespachoFin, :trabHraInicio, :trabHraFin, :tipoRol, 'No', :usuario, 'desde despacho2', curdate(), :usuario, :observPersonal, now(), :usuario, :modoCreacDp)");
    $inserta->bindParam(':fchDespacho',$fchDespacho);
    $inserta->bindParam(':correlativo',$correlativo);
    $inserta->bindParam(':idTrabajador',$idTrabajador);
    $inserta->bindParam(':trabFchDespachoFin',$trabFchDespachoFin);
    $inserta->bindParam(':trabHraInicio',$trabHraInicio);
    $inserta->bindParam(':trabHraFin',$trabHraFin);
    $inserta->bindParam(':tipoRol',$tipoRol);
    $inserta->bindParam(':observPersonal',$observPersonal);
    $inserta->bindParam(':modoCreacDp',$modoCreacDp);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return  $inserta->rowCount();
  }

  function buscarDatosTripulante($db){
    $idTrabajador = $_POST["idTrabajador"]; 
    $fchDespacho  = $_POST["fchDespacho"];
    $correlativo  = $_POST["correlativo"];

    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `desper`.`idTrabajador`, `valorRol`, `valorAdicional`, `valorHraExtra`, `trabFchDespachoFin`, `trabHraInicio`, `trabHraFin`, `tipoRol`, `pagado`, `fchPago`, `anhio`, `mes`, `fchDesmarca`, desper.`usuario`, `fchActualizacion`, `observPersonal`, concat(desper.`idTrabajador`, '-',apPaterno,' ',apMaterno,', ',nombres ) AS nombCompleto, concat(apPaterno,' ',apMaterno,', ',nombres ) AS apellNomb, `ultProceso`, `ultProcesoFch`, `ultProcesoUsuario` FROM `despachopersonal` AS desper, trabajador WHERE `desper`.idTrabajador = `trabajador`.idTrabajador AND desper.`fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND desper.`idTrabajador` LIKE :idTrabajador LIMIT 1");

    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->bindParam(':idTrabajador',$idTrabajador);
    $consulta->execute();
    return $consulta->fetch();
  }

  function editaTrip($db){
    /*echo "<pre>";
    print_r($_POST);
    echo "</pre>";*/
    $ultProcesoUsuario = $_SESSION["usuario"];
    $fchDespacho   = $_POST["fchDespachoEditaTrip"];
    $correlativo   = $_POST["correlativoEditaTrip"];
    $auxTrabajador = $_POST['txtEditaTrabajador'];
    $tipoRol       = $_POST['cmbEditaTrabTipoRol'];
    $trabHraInicio = $_POST['txtEditaTrabHraInicio'];
    $trabFchDespachoFin = $_POST['txtEditaTrabFchFin'];
    $trabHraFin    = $_POST['txtEditaTrabHraFin'];
    $observPersonal= $_POST['txtEditaObservTrip'];
    $tipoRolOrig   = $_POST['tipoRolOrig'];
    $idTrabOrig   = $_POST['idTrabOrig'];
    $ultProceso   = $_POST["estadoDespEditaTrip"];
    $idTrabajador = strtok($auxTrabajador , "-");

    ////////////
    $actualiza = $db->prepare("UPDATE `despachopersonal` SET `idTrabajador` = :idTrabajador, `observPersonal` = :observPersonal, tipoRol = :tipoRol, trabFchDespachoFin = :trabFchDespachoFin, trabHraInicio = :trabHraInicio, trabHraFin = :trabHraFin, ultProcesoUsuario = :ultProcesoUsuario, ultProceso = :ultProceso, ultProcesoFch = curdate()  WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `idTrabajador` = :idTrabOrig AND `tipoRol` = :tipoRolOrig ");

    $actualiza->bindParam(':observPersonal',$observPersonal);
    $actualiza->bindParam(':tipoRol',$tipoRol);
    $actualiza->bindParam(':trabFchDespachoFin',$trabFchDespachoFin);
    $actualiza->bindParam(':fchDespacho',$fchDespacho);
    $actualiza->bindParam(':correlativo',$correlativo);
    $actualiza->bindParam(':idTrabajador',$idTrabajador);
    $actualiza->bindParam(':trabHraInicio',$trabHraInicio);
    $actualiza->bindParam(':trabHraFin',$trabHraFin);
    $actualiza->bindParam(':tipoRolOrig',$tipoRolOrig);
    $actualiza->bindParam(':idTrabOrig',$idTrabOrig);
    $actualiza->bindParam(':ultProceso',$ultProceso);
    $actualiza->bindParam(':ultProcesoUsuario',$ultProcesoUsuario);
    $actualiza->execute();
    return  $actualiza->rowCount();
  }

  function eliminaTrip($db){
    $fchDespacho = $_POST['fchDespachoElimTrab'];
    $correlativo = $_POST['correlativoElimTrab'];
    $idTrabajador = $_POST['idTrabElimTrab'];
    $tipoRol = $_POST['txtElimTrabTipoRol']; 
    //echo " $fchDespacho, $correlativo, $idTrabajador, $tipoRol";   

    $elimina = $db->prepare("DELETE FROM `despachopersonal` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `idTrabajador` = :idTrabajador AND `tipoRol` = :tipoRol ");
    $elimina->bindParam(':fchDespacho',$fchDespacho);
    $elimina->bindParam(':correlativo',$correlativo);
    $elimina->bindParam(':idTrabajador',$idTrabajador);
    $elimina->bindParam(':tipoRol',$tipoRol);
    $elimina->execute();

    if ($elimina->rowCount() == 1){
      $descripcion = utf8_encode("Se elimin� registro de despachopersonal fch Despacho: $fchDespacho, correlativo: $correlativo, idTrabajador: $idTrabajador, tipoRol: $tipoRol");
      logAccion($db,$descripcion, NULL, NULL);
    }
    return  $elimina->rowCount();
  }

  function despachosDestinos($db){
    $idPunto = $_POST['idPunto'];
    $consulta = $db->prepare("SELECT `despacho`.idProgramacion, `despacho`.correlativo, `despacho`.placa FROM despacho,  ( SELECT `des`.idProgramacion, `des`.idCliente, `des`.fchDespacho  FROM `despachopuntos` despun, despacho des WHERE despun.idProgramacion = `des`.idProgramacion AND despun.`idPunto` = :idPunto) AS t1 WHERE `despacho`.idCliente = `t1`.idCliente AND `despacho`.fchDespacho = `t1`.fchDespacho AND despacho.estadoDespacho IN ('Programado', 'EnRuta') AND `despacho`.idProgramacion != `t1`.idProgramacion ");
    $consulta->bindParam(':idPunto',$idPunto);
    $consulta->execute();
    $auxData = $consulta->fetchAll();

    echo "<option>-</option>";
    foreach($auxData as $item) { 
      echo "<option>";
      echo $item['idProgramacion']."-".$item['placa'];
      echo "</option>";
     };
  }

  function traspasarPunto($db){
    $idPunto = $_POST["txtTraspasaIdPunto"];
    $auxDestino = $_POST["cmbTraspasarDespachoDestino"];
    $idProgramDestino = strtok($auxDestino, "-");

    $actualiza = $db->prepare("UPDATE `despachopuntos` SET `idProgramacion` = :idProgramDestino WHERE `idPunto` = :idPunto ");
    $actualiza->bindParam(':idPunto',$idPunto);
    $actualiza->bindParam(':idProgramDestino',$idProgramDestino);
    $actualiza->execute();
    return $actualiza->rowCount();  
  }

  function verificarEstadoDueno($db,$dataPost){
    //$estado = $dataPost["estado"];
    $idPlaca = $dataPost["idPlaca"];

    //echo $idPlaca;

    $consulta = $db->prepare("SELECT `veh`.idPlaca, `veh`.estado, `vehdue`.nombreCompleto, `vehdue`.estadoTercero FROM `vehiculo` AS veh, vehiculodueno AS vehdue WHERE `veh`.rznSocial = `vehdue`.documento AND `veh`.idPlaca = :idPlaca");
    $consulta->bindParam(':idPlaca',$idPlaca);
    $consulta->execute();
    return $consulta->fetch();

  }

  function buscarAsignarTelefono($db,$dato){
    $datos = array();
    $consulta = $db->prepare("SELECT concat(`idTrabajador`,'-',`apPaterno`,' ',`apMaterno`,', ',`nombres`) as dato FROM `trabajador` as dato WHERE `tipoTrabajador` IN ('conductor','coordinador','administrativo')  AND `apPaterno` LIKE '$dato%' OR `idTrabajador` LIKE '$dato%' OR `nombres` LIKE '$dato%'");
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => utf8_encode($item['dato']));
    }
    return $datos;
  }

  function buscarDataCombust($db, $fchIni, $fchFin){
    $where = "";
    if ($fchIni <> ""){
      if ($fchFin == "") $where = " `cmb`.fchCreacion >= '$fchIni' ";
      else $where = " `cmb`.fchCreacion BETWEEN '$fchIni' AND '$fchFin'";
    }  else if ($fchFin != "") $where = " `cmb`.fchCreacion  <= '$fchFin'";

    $consulta = $db->prepare("SELECT `cmb`.idPlaca, substring(`cmb`.fchCreacion,1,7) AS anhioMes, `cmb`.fchCreacion, kmActual, recorrido, nroVale, precioGalon, `cmb`.total, `cmb`.ratioSolesKm, round(total/recorrido,3) AS eficSoles, galones, grifo, dniConductor, producto, autoriza, `veh`.estado, `veh`.rznSocial, `veh`.m3Facturable FROM combustible AS cmb, vehiculo AS veh WHERE `cmb`.idPlaca = `veh`.idPlaca AND $where ");
  
    $consulta->execute();
    return $consulta->fetchAll();

  }

  function buscarDataDespachosxMes($db, $fchIni, $fchFin){
    $where = "";
    if ($fchIni <> ""){
      if ($fchFin == "") $where = " AND fchDespacho >= '$fchIni' ";
      else $where = " AND fchDespacho BETWEEN '$fchIni' AND '$fchFin'";
    }  else if ($fchFin != "") $where = " AND fchDespacho  <= '$fchFin'";

    $consulta = $db->prepare("SELECT count(*) AS cantDespachos, substring(fchDespacho,1,7) AS anhioMes, placa FROM `despacho` WHERE placa IS NOT NULL $where GROUP BY placa, substring(fchDespacho,1,7) ");
    $consulta->execute();
    return $consulta->fetchAll();

  }

  function buscarDataEficSolesKm($db, $fchIni, $fchFin, $limMin = 100, $limMax = 500){
    $where = "";
    if ($fchIni <> ""){
      if ($fchFin == "") $where = " AND cmb.fchCreacion >= '$fchIni' ";
      else $where = " AND cmb.fchCreacion BETWEEN '$fchIni' AND '$fchFin'";
    }  else if ($fchFin != "") $where = " AND cmb.fchCreacion  <= '$fchFin'";

    $consulta = $db->prepare("SELECT count(*) AS cant, `cmb`.idPlaca, sum( if (recorrido > $limMax, $limMax, if(recorrido < 5, $limMin, recorrido  )) ) AS recorrido , sum( precioGalon), sum(`cmb`.total), `cmb`.ratioSolesKm,  round(sum(total) /sum( if (recorrido > $limMax, $limMax, if(recorrido < 5, $limMin, recorrido  )) ),3) AS eficSoles, sum(galones), producto, `veh`.estado, `veh`.rznSocial FROM combustible AS cmb, vehiculo AS veh WHERE `cmb`.idPlaca = `veh`.idPlaca $where GROUP BY idPlaca ");
    $consulta->execute();
    return $consulta->fetchAll();

  };

  function cambiarEstadoDespacho($db, $dataPost){
    $idProgramacion = $dataPost["id"];
    $estadoDespAnt  = $dataPost["es1"];
    $estadoDespacho = $dataPost["es2"];

    $actualiza = $db->prepare("UPDATE `despacho` SET estadoDespacho = :estadoDespacho  WHERE `idProgramacion` = :idProgramacion AND estadoDespacho = :estadoDespAnt");

    $actualiza->bindParam(':idProgramacion',$idProgramacion);
    $actualiza->bindParam(':estadoDespacho',$estadoDespacho);
    $actualiza->bindParam(':estadoDespAnt',$estadoDespAnt);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function validarIngresoAuxiliares($db, $dataPost){
    $auxAuxiliares = $dataPost["auxAuxiliares"];

    $auxAuxiliares = preg_replace( '/\n/', '@', $auxAuxiliares );
    //echo $auxAuxiliares;

    $continuar = "Si";
    
    $dataAuxiliar = strtok($auxAuxiliares, "@");
    while ( $dataAuxiliar != "" && $continuar == "Si" ) {
      $dni = substr($dataAuxiliar,0, strpos($dataAuxiliar, "-"));
      //$arrAuxiliares[] = $dni;

      $consulta = $db->prepare("SELECT count(*) AS cant FROM `trabajador` WHERE `idTrabajador` LIKE :dni AND estadoTrabajador = 'Activo'  ");
      $consulta->bindParam(':dni',$dni);
      $consulta->execute();
      $aux = $consulta->fetch();
      if ($aux["cant"] == 0 ){
        $continuar = "No";

      } 
      //return $aux["cant"];
      $dataAuxiliar = strtok("@");
    }
    return $continuar."|".$dni;
  }

  function buscarInfoParaCerrarForzoso($db, $dataPost){
    $idProgramacion = $dataPost["idProgramacion"];
    $arrData = array(); 
    $fchDespacho = $dataPost["fchDespacho"];
    $correlativo = $dataPost["correlativo"];
    $consulta = $db->prepare("SELECT `fchDespacho`, des.`correlativo`, `idProgramacion`, `cli`.rznSocial, concat(des.`idCliente`,'-',`cli`.rznSocial ) AS rznCliente , `guiaCliente`, `hraInicio`, `fchDespachoFinCli`, `fchDespachoFin`, `hraFin`, `placa`, `considerarPropio`, `m3`, `valorServicio`, `igvServicio`, des.`idCliente`, `cuenta`, `correlCuenta`, `tipoServicioPago`, des.`nroAuxiliares`, `nroAuxiliaresAdic`, `costo`, `topeServicioHraNormal`, des.`hrasNormales`, des.`tolerHrasNormales`, `costoHraExtra`, `toleranCobroHraExtra`, `valorConductor`, `hraNormCondDespacho`, `tolerHraCondDespacho`, `valHraAdicCondDespacho`, `valorAuxiliar`, `hraNormAuxDespacho`, `tolerHraAuxDespacho`, `valHraAdicAuxDespacho`, `nroAuxiliaresCuenta`, `valorAuxAdicional`, `usarMaster`, `tpoExtraHras`, `costoTotal`, `nroGuias`, `nroDespachos`, `usaReten`, `concluido`, `hraInicioBase`, `kmInicio`, `kmInicioCliente`, `kmFinCliente`, `hraFinCliente`, `lugarFinCliente`, `kmFin`, `recorridoEsperado`, `zonaDespacho`, `idZonaDespacho`, `tolerHraIniEsperadoDesp`, `tolerHraFinEsperadoDesp`, `valAuxTerceroDesp`, `puntosDesp`, `ptoOrigen`, `idOrigen`, `idSedeOrigen`, `tipoDestino`, `ptoDestino`, `observacion`, `observCliente`, `docCobranza`, `tipoDoc`, `pagado`, `pagoEnPlanilla`, `terceroConPersonalMoy`, des.`contarDespacho`, des.`superCuenta`, des.`usuario`, des.`fchCreacion`, `modoCreacion`, `estadoDespacho`, `usuarioGrabaFin`, `fchGrabaFin`, `hraGrabaFin`, `usuarioCreaCobranza`, `fchCreaCobranza`, des.`idProducto`, clicuepro.`nombProducto`, des.`valKmAdic`, des.`hraIniEsperado`, des.`valAdicHraIniEsper`, des.`hraFinEsperado`, des.`valAdicHraFinEsper`, des.`cobrarPeaje`, des.`cobrarRecojoDevol`, des.`valUnidTercCCond`, des.`valUnidTercSCond`, des.`hrasNormalTerc`, des.`tolerHrasNormalTerc`, des.`valHraExtraTerc`, des.`valKmAdicTerc`, des.`tolerKmEsperado`, des.`movilAsignado`, `editaUsuarioDesp`, `usuarioAsignado`,

       `modoTerminado`, `modoConcluido`, `fchTerminado`, `usuarioTerminado`

     FROM `despacho` AS des, cliente AS cli, clientecuentaproducto AS clicuepro WHERE `des`.idProducto = `clicuepro`.idProducto AND `des`.idCliente = `cli`.idRuc AND des.`fchDespacho` = :fchDespacho AND des.`correlativo` = :correlativo ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $dataDesp = $consulta->fetch();

    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, desper.`idTrabajador`, concat(desper.`idTrabajador`, '-', apPaterno,' ', apMaterno,', ', nombres) AS tripulante , `valorRol`, `esReten`, `valorAdicional`, `valorHraExtra`, `trabFchDespachoFin`, `trabHraInicio`, `trabHraFin`, `tipoRol`, `pagado`, `fchPago`, `anhio`, `mes`, `fchDesmarca`, desper.`usuario`, `fchActualizacion`, `observPersonal`, `ultProceso`, `ultProcesoFch`, `ultProcesoUsuario` FROM `despachopersonal` AS desper, trabajador AS trab  WHERE `desper`.idTrabajador = `trab`.idTrabajador AND `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $dataTrip = $consulta->fetchAll();

    $consulta = $db->prepare("SELECT `idPunto`, `idProgramacion`, `idRuta`, `ordenPunto`, `fchDespacho`, `correlativo`, `correlPunto`, `tipoPunto`, `nombComprador`, `idDistrito`, `distrito`, `provincia`, `direccion`, `nroGuiaPorte`, `guiaCliente`, `guiaMoy`, `estado`, `subEstado`, `hraLlegada`, `hraSalida`, `observacion`, `fchPunto`, `referencia`, `telfReferencia`, `idCarga`, `observCargaPunto`, `adic_01`, `adic_02`, `adic_03`, `foto_1`, `foto_2`, `foto_3`, `foto_4`, `punto`, `placa`, `km`, `creacFch`, `creacUsuario`, `editaFch`, `editaUsuario` FROM `despachopuntos` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $dataPtos = $consulta->fetchAll();

    $consulta = $db->prepare("SELECT `guiaPorte`, `fchDespacho`, `correlativo`, `usuario`, `fchCreacion`, `editaUsuario`, `editaFch` FROM `despachoguiaporte`  WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $dataGuias = $consulta->fetchAll();
    
    $idCliente = $dataDesp["idCliente"];
    $consulta = $db->prepare("SELECT `idCliente`, `correlativo`, `nombreCuenta`, `estadoCuenta`, `tipoCuenta`, `paraMovil`, `creacUsuario`, `creacFch`, `editUsuario`, `editFch` FROM `clientecuentanew` WHERE `idCliente` LIKE :idCliente AND `estadoCuenta` = 'Activo' ");
    $consulta->bindParam(':idCliente', $idCliente);
    $consulta->execute();
    $dataCuentas = $consulta->fetchAll();

    //Productos
    $correlCuenta = $dataDesp["correlCuenta"];
    $consulta = $db->prepare("SELECT `idProducto`, `idCliente`, `correlativo`, `nombProducto`, `m3Facturable`, `puntos`, `idZona`, `zona`, `tipoProducto`, `estadoProducto`, `precioServ`, `kmEsperado`, `tolerKmEsperado`, `valKmAdic`, `hrasNormales`, `tolerHrasNormales`, `valHraAdic`, `hraIniEsperado`, `tolerHraIniEsperado`, `valAdicHraIniEsper`, `hraFinEsperado`, `tolerHraFinEsperado`, `valAdicHraFinEsper`, `nroAuxiliares`, `valAuxiliarAdic`, `cobrarPeaje`, `cobrarRecojoDevol`, `valConductor`, `hraNormalConductor`, `tolerHraCond`, `valHraAdicCond`, `valAuxiliar`, `hraNormalAux`, `tolerHraAux`, `valHraAdicAux`, `usoMaster`, `valUnidTercCCond`, `valUnidTercSCond`, `hrasNormalTerc`, `tolerHrasNormalTerc`, `valHraExtraTerc`, `valKmAdicTerc`, `valAuxTercero`, `contarDespacho`, `superCuenta` FROM `clientecuentaproducto` WHERE estadoProducto = 'Activo' AND `idCliente` LIKE :idCliente AND `correlativo` LIKE :correlativo");
    $consulta->bindParam(':idCliente', $idCliente);
    $consulta->bindParam(':correlativo', $correlCuenta);
    $consulta->execute();
    $dataCtaProds = $consulta->fetchAll();

    $consulta = $db->prepare("SELECT `ubisede`.`idSede`, `ubisede`.`descripcion`, concat(`ubisede`.`idUbicacion`,' - ',`ubicacion`.`descripcion`) AS descripUbi,`ubisede`.`idUbicacion`, `ubisede`.`creacUsuario`, `ubisede`.`creacFch`, `ubizona`.`descripcion` AS descripZona, `ubisede`.creacUsuario  FROM `ubisede`, `ubicacion`, `ubizona` WHERE `ubisede`.`IdUbicacion` = `ubicacion`.`IdUbicacion` AND `ubicacion`.`IdZona` = `ubizona`.`IdZona` ");  
    $consulta->execute();
    $dataSedes = $consulta->fetchAll();

    $cadenaSedes = "<option></option>";
    $ptoOrigen = $dataDesp["idSedeOrigen"];
    $sedeElegida = "";
    foreach($dataSedes as $itemPtoOrigen) {
      $origenOpcion  = $itemPtoOrigen['idSede'];
      $origenDescrip = $itemPtoOrigen['descripcion'];
      $origenDistrito = $itemPtoOrigen['idUbicacion'];    
      $opcion =  ($ptoOrigen == $origenOpcion)?' selected':'';
      if($ptoOrigen == $origenOpcion){
        $opcion =  ' selected';
        $sedeElegida = $origenOpcion.'|'.$origenDistrito.'->'.$origenDescrip;
      } else {
        $option = '';
      }
      $cadenaSedes .= "<option  $opcion  value = '$origenOpcion|$origenDistrito' >".$origenDescrip;
      $cadenaSedes .= "</option>";
    }

    ///////////////
    $cadenaCuentas = "";
    foreach($dataCuentas as $itemCuenta) {
      //$idCliente = $_itemCuenta["idCliente"];
      $correlItemCuenta = $itemCuenta["correlativo"];
      $nombreCuenta = $itemCuenta["nombreCuenta"];
      $tipoCuenta = $itemCuenta["tipoCuenta"];

      if($correlCuenta == $correlItemCuenta){
        $opcion =  ' selected';
      } else {
        $opcion = '';
      }
      $cadenaCuentas .= "<option  $opcion  value = '$correlItemCuenta|$nombreCuenta|$tipoCuenta' >".$correlItemCuenta."|".$nombreCuenta."|".$tipoCuenta;
      $cadenaCuentas .= "</option>";
    }

    $idProducto = $dataDesp["idProducto"];
    $cadenaCtaPdtos = "";
    foreach($dataCtaProds as $itemPdto) {
      $idItemProducto = $itemPdto["idProducto"];
      $nombProducto = $itemPdto["nombProducto"];
      $m3Facturable = $itemPdto["m3Facturable"];

      if($idProducto == $idItemProducto){
        $opcion =  ' selected';
      } else {
        $opcion = '';
      }
      $cadenaCtaPdtos .= "<option  $opcion  value = '$idItemProducto' >".$idItemProducto."|".$nombProducto."|".$m3Facturable. " m3";
      $cadenaCtaPdtos .= "</option>";
    }

    /////////////////

    $lugarFinCli = $dataDesp["lugarFinCliente"];
    $cadenaLugarFin = "<option ".($lugarFinCli == null ? " SELECTED " : "" )." value = '-'>-</option>";
    $cadenaLugarFin .= "<option ".($lugarFinCli == 'Base' ? " SELECTED " : "" )." value = 'Base'>Fin Base Cli</option>";
    $cadenaLugarFin .= "<option ".($lugarFinCli == 'UltCliente' ? " SELECTED " : "" )." value = 'UltCliente'>Ultimo Cli</option>";

    //$cadEncabTrip = "<table width = '100%'><thead><tr><th width = '250 px'>Trabajador</th><th width = '80 px'>Tipo Rol</th><th width = '80 px'>Fch Inicio</th><th width = '80 px'>Hra Inicio</th><th width = '80 px'>Fch Fin</th><th>Hra Fin</th></tr></thead>";

    $cadEncabTrip = "<table width = '100%' border = '0px'><thead><tr><th width = '250 px'>Trabajador</th><th width = '65 px'>Tipo Rol</th><th width = '65 px'>Fch Inicio</th><th width = '55 px'>Hra Inicio</th><th width = '65 px'>Fch Fin</th><th width = '55 px'>Hra Fin</th><th width = '60 px'>Valor Rol </th><th width = '60 px'>Valor Adicional</th><th>Valor HraExtra</th></tr></thead>";

    $cadenaValTrip = utf8_encode("<thead><tr><th>Trabajador</th><th>Tipo Rol</th><th>Fch. Inicio</th><th>Hr. Inicio</th><th>Fch Fin</th><th>Hr. Fin</th><th>Val Rol</th><th>Val Adic</th><th>Val Hr. Extra</th><th>Acci�n</th></tr></thead>");
    $cadenaValTrip .= "<tbody>";

    $cadenaTrip = "<tbody>";
    //$cadenaValTrip = "<tbody>";
    $cadenaCmbReten = ""; //Ya no va ESTO "<option value = 'No'>No</option>";

    //$nroAuxSinReten = 0;//Esto se debe quitar
    $nroAuxTotal = $nroAuxReten = 0;
    if(count($dataTrip) > 0){
      foreach ($dataTrip as $key => $trip){
        $fchIni = "<td>".$trip["fchDespacho"]."</td>";
        $fchFin = "<td>".$trip["trabFchDespachoFin"]."</td>";
        if($trip["trabHraInicio"] == NULL){
          $hraInicio = "<td class = 'rojo'>Hra. Primer Cli.</td>";
        } else {
          $hraInicio = "<td>".$trip["trabHraInicio"]."</td>";
        }
        if($trip["trabHraFin"] == NULL){
          $hraFin = "<td class = 'rojo'>Hra. Ult. Cli.</td>";
        } else {
          $hraFin = "<td>".$trip["trabHraFin"]."</td>";
        }
        //$cadenaTrip .= "<tr><td>".$trip["tripulante"]."</td><td>".$trip["tipoRol"]."</td>$hraInicio$hraFin</tr>";

        $cadenaTrip .= "<tr><td>".$trip["tripulante"]."</td><td>".$trip["tipoRol"]."</td>$fchIni$hraInicio$fchFin$hraFin<td align = 'right' >".$trip["valorRol"]."</td><td align = 'right' >".$trip["valorAdicional"]."</td><td align = 'right' >".$trip["valorHraExtra"]."</td></tr>";

        $cadenaValTrip .= "<tr><td>".$trip["tripulante"]."</td><td>".$trip["tipoRol"]."</td>$fchIni$hraInicio$fchFin$hraFin<td>".$trip["valorRol"]."</td><td>".$trip["valorAdicional"]."</td><td>".$trip["valorHraExtra"]."</td><td><img border='0' src='imagenes/lapiz.png' class = 'editar' width='14' height='14' align='left' f=".$trip["fchDespacho"]." c= ".$trip["correlativo"]." id= ".$trip["idTrabajador"]." ></td></tr>";

        if ($trip["tipoRol"] == 'Auxiliar'){
          //$nroAuxSinReten++;
          $nroAuxTotal++;
          if($trip["esReten"] == 'Si'){
            $nroAuxReten++;
            $selected = "Selected";
          } else {
            $selected = "";
          }
          //$selected = ($trip["esReten"] == 'Si') ? "Selected" : ""; 
          $cadenaCmbReten .= "<option value = '".$trip["idTrabajador"]."' $selected  >".$trip["tripulante"]."</option>";
        }  
      }

    } else {
      $cadenaTrip .= "<tr><td class = 'rojo' colspan = '4' align = 'center' >POR LO MENOS SE REQUIERE UN TRIPULANTE</td></tr>";
    }

    //if( $dataDesp["usaReten"] != "" AND $dataDesp["usaReten"] != "No" AND $nroAuxSinReten > 0 ) $nroAuxSinReten--;

    $cadenaValTrip .= "</tbody>";

    $cadenaValTrip .= utf8_encode("<tfoot><tr><th>Trabajador</th><th>Tipo Rol</th><th>Fch. Inicio</th><th>Hr. Inicio</th><th>Fch Fin</th><th>Hr. Fin</th><th>Val Rol</th><th>Val Adic</th><th>Val Hr. Extra</th><th>Acci�n</th></tr></tfoot>");

    $cadenaTrip .= "</tbody></table>";

    $cadTripTot = $cadEncabTrip.$cadenaTrip;
    $cadValTripTot = $cadenaValTrip;

    ///////////////
    $cadenaGuias = utf8_encode("<table width = '100%'><tr><th width = '250 px'>Gu�a Porte</th><th>Usuario</th></tr>");
    if(count($dataGuias) > 0){
      foreach ($dataGuias AS $key => $guia){
        $cadenaGuias .= "<tr><td>".utf8_encode($guia["guiaPorte"])."</td><td>".utf8_encode($guia["usuario"])."</td></tr>";
      }
    } else {
        $cadenaGuias .= "<tr><td class = 'amarillo' colspan = '2' align = 'center' >NO HAY GUIAS REGISTRADAS</td></tr>";
    }
    
    $cadenaGuias .= "</table>";


////////////////////

    $cadenaPtos = "<table width = '100%'><tr><th width = '70 px'>Tipo Punto</th><th width = '140 px'>Nombre Cliente</th><th width = '64 px'>Hr. Llegada</th><th width = '64 px'>Hr. Salida</th><th width = '80 px'>Estado</th><th>Subestado</th></tr>";

    if(count($dataPtos) > 0){
      foreach ($dataPtos as $key => $ptos){
        if($ptos["estado"] == "" OR $ptos["estado"] == "Pendiente" ){
          $estado = "<td class = 'rojo'>No Entregado</td>";
          $subEstado = "<td class = 'rojo'>Rechaza Compra</td>";
        } else {
          $estado = "<td>".$ptos["estado"]."</td>";
          $subEstado = "<td>".$ptos["subEstado"]."</td>";
        }
        $cadenaPtos .= "<tr><td>".$ptos["tipoPunto"]."</td><td>".$ptos["nombComprador"]."</td><td>".$ptos["hraLlegada"]."</td><td>".$ptos["hraSalida"]."</td>$estado$subEstado</tr>";
      }

    } else {
      $cadenaPtos .= "<tr><td class = 'amarillo' colspan = '6' align = 'center' >NO HAY PUNTOS CARGADOS</td></tr>";
    }

    $cadenaPtos .= "</table>";

    if ($dataDesp["lugarFinCliente"] == 'UltCliente'){
      $hraFinCalculo = $dataDesp["hraFinCliente"];
      $kmFacturable  = $dataDesp["kmFinCliente"] - $dataDesp["kmInicioCliente"];
    } else {
      $hraFinCalculo = $dataDesp["hraFin"];
      $kmFacturable  = $dataDesp["kmFin"] - $dataDesp["kmInicioCliente"];
    }

    $duracionhhmmss = restahhmmss($dataDesp["hraInicio"], $hraFinCalculo);

    if(!isset($_SESSION[$dataDesp["placa"].'@'])) $_SESSION[$dataDesp["placa"].'@'] = 'NoTiene';

    ////////////////////
    $arrData = array(
      "fchDespacho" => $dataDesp["fchDespacho"],
      "correlativo" => $dataDesp["correlativo"],
      "fchDespachoFinCli" => $dataDesp["fchDespachoFinCli"],
      "fchDespachoFin" => $dataDesp["fchDespachoFin"],
      "idProgramacion" => $dataDesp["idProgramacion"],
      "idCliente"   => $dataDesp["idCliente"],
      "placa"       => $dataDesp["placa"],
      "hraInicio"   => $dataDesp["hraInicio"],
      "correlCuenta"   => $dataDesp["correlCuenta"],
      "correl_cuenta" => $dataDesp["correlCuenta"]."|".$dataDesp["cuenta"]."|".$dataDesp["tipoServicioPago"],
      "producto"    => $dataDesp["idProducto"]."|".$dataDesp["nombProducto"]."|".$dataDesp["m3"]." m3",
      "rznCliente"  => $dataDesp["rznCliente"],
      "usuario"     => $dataDesp["usuario"],
      "fchCreacion" => $dataDesp["fchCreacion"],
      "hraInicioBase" => $dataDesp["hraInicioBase"],
      "hraInicio"   => $dataDesp["hraInicio"],
      "hraFinCliente" => $dataDesp["hraFinCliente"],
      "hraFin"      => $dataDesp["hraFin"],
      "kmInicio"    => $dataDesp["kmInicio"],
      "kmInicioCliente" => $dataDesp["kmInicioCliente"],
      "kmFinCliente" => $dataDesp["kmFinCliente"],
      "kmFin"       => $dataDesp["kmFin"],
      "lugarFinCliente" => $dataDesp["lugarFinCliente"],
      "estadoDespacho" => $dataDesp["estadoDespacho"],
      "duracionhhmmss" => $duracionhhmmss,
      "tripulantes" => $cadTripTot,
      "validarTrip" => $cadValTripTot,
      "puntos"      => $cadenaPtos,
      "sedes"       => $cadenaSedes,
      "sedeElegida" => $sedeElegida,
      "lugarFinCli" => $cadenaLugarFin,
      "guias"    => $cadenaGuias,
      "cuentas"  => $cadenaCuentas,
      "ctaPdtos" => $cadenaCtaPdtos,
      "observacion" => $dataDesp["observacion"],
      "hrasNormales" => $dataDesp["hrasNormales"],
      "kmEsperado"  => $dataDesp["recorridoEsperado"],
      "reten" => $cadenaCmbReten,
      "nroAuxTotal" => $nroAuxTotal,
      "nroAuxReten" => $nroAuxReten,
      "nroAuxSinReten" => $nroAuxTotal - $nroAuxReten,
      "nroAuxiliaresCuenta" => $dataDesp["nroAuxiliaresCuenta"],
      "valorAuxAdicional" => $dataDesp["valorAuxAdicional"],
      "valKmAdic" => $dataDesp["valKmAdic"],
      "usaReten" =>  $dataDesp["usaReten"],
      "valAuxTerceroDesp" =>  $dataDesp["valAuxTerceroDesp"],
      "valHraExtraTerc" =>  $dataDesp["valHraExtraTerc"],

      "modoCreacion" =>  $dataDesp["modoCreacion"],
      "modoTerminado" =>  $dataDesp["modoTerminado"],
      "modoConcluido" =>  $dataDesp["modoConcluido"],
      "fchTerminado" =>  $dataDesp["fchTerminado"],
      "usuarioTerminado" =>  $dataDesp["usuarioTerminado"],
      "fchGrabaFin" =>  $dataDesp["fchGrabaFin"],
      "hraGrabaFin" =>  $dataDesp["hraGrabaFin"],
      "usuarioGrabaFin" =>  $dataDesp["usuarioGrabaFin"],
      "kmFacturable" => $kmFacturable,
      "alertaPlaca" => $_SESSION[$dataDesp["placa"].'@']
      
    );
    return $arrData;
  }

  function cerrarForzoso($db, $dataPost){
    //Validaciones previas
    $fchCreacion = $dataPost["txtCFfchCreacion"];
    $fchDespacho = $dataPost["txtCFfchDespacho"];
    $correlativo = $dataPost["txtCFcorrelativo"];
    $cliente  = $dataPost["txtCFcliente"];
    $cuenta   = $dataPost["txtCFcuenta"];
    $producto = $dataPost["txtCFproducto"];
    //echo "Correlativo: $correlativo, Cliente: $cliente, Cuenta: $cuenta, Producto: $producto ";
    $usuario = $_SESSION["usuario"];
    $idCliente  = strtok($cliente, "-");
    $corrCuenta = strtok($cuenta, "|");
    $nombCuenta = strtok("|");
    $tipoCuenta = strtok("|");
    $idProducto = strtok($producto, "|");

    //Validar cuenta
    $consulta = $db->prepare("SELECT count(*) AS cant FROM `clientecuentanew` WHERE `idCliente` LIKE :idCliente AND `correlativo` LIKE :correlativo AND `tipoCuenta` LIKE :tipoCuenta ");
    $consulta->bindParam(':idCliente',$idCliente);
    $consulta->bindParam(':correlativo',$corrCuenta);
    $consulta->bindParam(':tipoCuenta',$tipoCuenta);
    $consulta->execute();
    $dataCta = $consulta->fetch();
    //echo "Cant Cuenta ".$dataCta["cant"].", ";
    if ($dataCta["cant"] != 1) return "Error. Hay inconsistencia en los datos de la cuenta.";

    //Validar producto
    $consulta = $db->prepare("SELECT count(*) AS cant FROM `clientecuentaproducto` WHERE `idProducto` = :idProducto AND `idCliente` LIKE :idCliente ");
    $consulta->bindParam(':idCliente',$idCliente);
    $consulta->bindParam(':idProducto',$idProducto);
    $consulta->execute();
    $dataPdto = $consulta->fetch();
    //echo "Cant Producto ".$dataPdto["cant"].", ";
    if ($dataPdto["cant"] != 1) return "Error. El producto indicado no existe para este cliente.";

    //Validar tripulaci�n
    $consulta = $db->prepare("SELECT sum(if(tipoRol = 'Auxiliar',1,0)) AS nroAuxiliares, sum(if(tipoRol = 'Conductor',1,0)) AS nroConductores FROM `despachopersonal` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo LIMIT 1 ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $dataDes = $consulta->fetch();
    //echo "Cant Conductor ".$dataDes["nroConductores"].", Cant Auxiliares ".$dataDes["nroAuxiliares"];

    if($tipoCuenta == "Normal" && $dataDes["nroConductores"] != 1 ) return "Error. Un despacho 'Normal' debe tener un y solo un conductor";

    if($tipoCuenta == "SoloPersonal" && $dataDes["nroConductores"] > 0 ) return "Error. Un despacho 'SoloPersonal' no debe contener conductor";

    if($tipoCuenta == "SoloPersonal" && $dataDes["nroAuxiliares"] <= 0 ) return "Error. Un despacho 'SoloPersonal' debe contener auxiliares";

    $idProgramacion= $dataPost["txtCFidProgramacion"];
    //$usuario   = $dataPost["txtCFusuario"];
    $placa  = $dataPost["txtCFplaca"] == ""?NULL:$dataPost["txtCFplaca"];
    $hraInicioBase = $dataPost["txtCFhraInicioBase"];
    $kmInicio  = $dataPost["txtCFkmInicio"];
    $hraInicio = $dataPost["txtCFhraInicio"];
    $kmInicioCliente= $dataPost["txtCFkmInicioCliente"];
    $hraFinCliente = $dataPost["txtCFhraFinCliente"];
    $kmFinCliente  = $dataPost["txtCFkmFinCliente"];
    $hraFin    = $dataPost["txtCFhraFin"];
    $kmFin     = $dataPost["txtCFkmFin"];
    $observacion   = $dataPost["txtCFobservacion"];
    $puntoOrigen   = $dataPost["cmbCFPuntoOrigen"];
    $lugarFinCliente = $dataPost["cmbCFFinCli"];
    $fchFinCli = $dataPost["txtCFfchFinCli"];
    $fchFin    = $dataPost["txtCFfchFin"];
    $usaReten  = ""; //Lo dejo para mantener compatibilidad $dataPost["cmbCFReten"];

    $idSedeOrigen = strtok($puntoOrigen, "|");
    $ptoOrigen = strtok("|");
    $estadoDespacho  = "Terminado";

    $hraIniTrab = $hraInicio;
    $hraFinTrab = $hraFinCliente;
    $modoTerminado = "Cerrar Forzoso";
    //echo "hraInicio: $hraInicio, hraFin: $hraFin, hraInicioBase: $hraInicioBase, kmInicio: $kmInicio, kmInicioCliente: $kmInicioCliente, kmFinCliente: $kmFinCliente, hraFinCliente: $hraFinCliente, kmFin: $kmFin, fchDespacho: $fchDespacho, correlativo: $correlativo";
  
    $actualiza = $db->prepare("UPDATE `despacho` SET `hraInicio` = :hraInicio, `hraFin` = :hraFin, `hraInicioBase` = :hraInicioBase, `kmInicio` = :kmInicio, `kmInicioCliente` = :kmInicioCliente, `kmFinCliente` = :kmFinCliente, `hraFinCliente` = :hraFinCliente, `kmFin` = :kmFin, estadoDespacho = :estadoDespacho, fchDespachoFinCli = :fchDespachoFinCli, fchDespachoFin = :fchDespachoFin, `lugarFinCliente` = :lugarFinCliente, `ptoOrigen` = :ptoOrigen, `idSedeOrigen` = :idSedeOrigen, `usaReten` = :usaReten, modoTerminado = :modoTerminado, usuarioTerminado = :usuarioTerminado, fchTerminado = curdate(), fchGrabaFin = curdate(), hraGrabaFin = curtime()  WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
    $actualiza->bindParam(':hraInicio',$hraInicio);
    $actualiza->bindParam(':hraFin',$hraFin);
    $actualiza->bindParam(':hraInicioBase',$hraInicioBase);
    $actualiza->bindParam(':kmInicio',$kmInicio);
    $actualiza->bindParam(':kmInicioCliente',$kmInicioCliente);
    $actualiza->bindParam(':kmFinCliente',$kmFinCliente);
    $actualiza->bindParam(':hraFinCliente',$hraFinCliente);
    $actualiza->bindParam(':kmFin',$kmFin);
    $actualiza->bindParam(':estadoDespacho',$estadoDespacho);
    $actualiza->bindParam(':usaReten',$usaReten);
    $actualiza->bindParam(':lugarFinCliente',$lugarFinCliente);
    $actualiza->bindParam(':idSedeOrigen',$idSedeOrigen);
    $actualiza->bindParam(':ptoOrigen',$ptoOrigen);
    $actualiza->bindParam(':usuarioTerminado',$usuario);
    $actualiza->bindParam(':modoTerminado', $modoTerminado);
    //Esto hay que verificar cambios
    $actualiza->bindParam(':fchDespachoFinCli',$fchFinCli);
    $actualiza->bindParam(':fchDespachoFin',$fchFin);
    $actualiza->bindParam(':fchDespacho',$fchDespacho);
    $actualiza->bindParam(':correlativo',$correlativo);
    $actualiza->execute();
    //return $actualiza->rowCount();
  
    ///   ///   ///   ///   ///   ///   ///
    // Actualizar datos del producto seleccionado
    ///   ///   ///   ///   ///   ///   ///
    $actualiza = $db->prepare("UPDATE `despacho` AS `dest`, ( SELECT `clicue`.nombreCuenta, `clicue`.tipoCuenta, clicuepro.`idProducto`, clicuepro.`idCliente`, clicuepro.`correlativo`, clicuepro.`nombProducto`, clicuepro.`m3Facturable`, clicuepro.`puntos`, clicuepro.`idZona`, clicuepro.`zona`, clicuepro.`tipoProducto`, clicuepro.`estadoProducto`, clicuepro.`precioServ`, clicuepro.`kmEsperado`, clicuepro.`tolerKmEsperado`, clicuepro.`valKmAdic`, clicuepro.`hrasNormales`, clicuepro.`tolerHrasNormales`, clicuepro.`valHraAdic`, clicuepro.`hraIniEsperado`, clicuepro.`tolerHraIniEsperado`, clicuepro.`valAdicHraIniEsper`, clicuepro.`hraFinEsperado`, clicuepro.`tolerHraFinEsperado`, clicuepro.`valAdicHraFinEsper`, clicuepro.`nroAuxiliares`, clicuepro.`valAuxiliarAdic`, clicuepro.`cobrarPeaje`, clicuepro.`cobrarRecojoDevol`, clicuepro.`valConductor`, clicuepro.`hraNormalConductor`, clicuepro.`tolerHraCond`, clicuepro.`valHraAdicCond`, clicuepro.`valAuxiliar`, clicuepro.`hraNormalAux`, clicuepro.`tolerHraAux`, clicuepro.`valHraAdicAux`, clicuepro.`usoMaster`, clicuepro.`valUnidTercCCond`, clicuepro.`valUnidTercSCond`, clicuepro.`hrasNormalTerc`, clicuepro.`tolerHrasNormalTerc`, clicuepro.`valHraExtraTerc`, clicuepro.`valKmAdicTerc`, clicuepro.`valAuxTercero`, clicuepro.`contarDespacho`, clicuepro.`superCuenta` FROM  `clientecuentaproducto` AS clicuepro , clientecuentanew AS clicue WHERE `clicuepro`.idCliente = `clicue`.idCliente AND `clicuepro`.correlativo = `clicue`.correlativo AND idProducto = :idProducto) AS `src` SET  `dest`.`idProducto` = `src`.`idProducto`, `dest`.`correlCuenta` = `src`.`correlativo`, `dest`.`m3` = `src`.`m3Facturable`, `dest`.hrasNormales = `src`.hrasNormales, `dest`.tolerHrasNormales = `src`.tolerHrasNormales, `dest`.hraNormCondDespacho = `src`.hraNormalConductor, `dest`.tolerHraCondDespacho = `src`.tolerHraCond,  `dest`.valHraAdicCondDespacho = `src`.valHraAdicCond, `dest`.hraNormAuxDespacho = `src`.hraNormalAux, `dest`.tolerHraAuxDespacho = `src`.tolerHraAux, `dest`.valHraAdicAuxDespacho = `src`.valHraAdicAux, `dest`.valorAuxAdicional = `src`.valAuxiliarAdic, `dest`.tipoServicioPago = `src`.tipoProducto, `dest`.topeServicioHraNormal = `src`.`tolerHraAux`, `dest`.tolerHraCondDespacho = `src`.`tolerHraCond`, `dest`.valorAuxiliar = `src`.`valAuxiliar`, `dest`.valHraAdicAuxDespacho = `src`.`valHraAdicAux`, `dest`.nroAuxiliaresCuenta = `src`.`nroAuxiliares`, `dest`.valorConductor = `src`.`valConductor`, `dest`.costoHraExtra = `src`.`valHraAdic`, `dest`.recorridoEsperado = `src`.`kmEsperado`, `dest`.valorServicio = `src`.precioServ, `dest`.valHraAdicCondDespacho = `src`.`valHraAdicCond`, `dest`.valorAuxAdicional = src.`valAuxiliarAdic`, `dest`.superCuenta = src.`superCuenta`, `dest`.igvServicio = round(0.18*`src`.precioServ,2), `dest`.usarMaster = `src`.usoMaster, `dest`.hraIniEsperado = `src`.hraIniEsperado, `dest`.hraFinEsperado = `src`.hraFinEsperado,  `dest`.tolerKmEsperado = `src`.tolerKmEsperado, `dest`.valUnidTercCCond = `src`.valUnidTercCCond, `dest`.valUnidTercSCond = `src`.valUnidTercSCond,  `dest`.hrasNormalTerc = `src`.hrasNormalTerc, `dest`.tolerHrasNormalTerc = `src`.tolerHrasNormalTerc, `dest`.valHraExtraTerc = `src`.valHraExtraTerc, `dest`.valKmAdicTerc = `src`.valKmAdicTerc, `dest`.tolerHraCondDespacho = `src`.tolerHraCond, `dest`.valKmAdic = `src`.valKmAdic, `dest`.valAdicHraIniEsper = `src`.valAdicHraIniEsper, `dest`.tolerHraAuxDespacho = `src`.tolerHraAux, `dest`.zonaDespacho = `src`.zona, `dest`.valAdicHraFinEsper = `src`.valAdicHraFinEsper, `dest`.contarDespacho = `src`.contarDespacho, `dest`.cobrarPeaje = `src`.cobrarPeaje, `dest`.cobrarRecojoDevol = `src`.cobrarRecojoDevol, `dest`.cuenta = `src`.nombreCuenta, `dest`.puntosDesp = `src`.puntos, `dest`.valAuxTerceroDesp = `src`.valAuxTercero, `dest`.idZonaDespacho = `src`.idZona WHERE `dest`.`idProgramacion` = :idProgramacion");


    $actualiza->bindParam(':idProducto',$idProducto);
    $actualiza->bindParam(':idProgramacion',$idProgramacion);
    $actualiza->execute();
    ///   ///   ///

    $actualiza = $db->prepare("UPDATE `despachopersonal` SET `trabHraInicio` = if(`trabHraInicio` IS NULL OR `trabHraInicio` = '00:00:00', :hraIniTrab, `trabHraInicio`), `trabHraFin` =  if(`trabHraFin` IS NULL OR `trabHraFin` = '00:00:00', :hraFinTrab, `trabHraFin`), esReten = 'No' WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
    $actualiza->bindParam(':hraIniTrab',$hraIniTrab);
    $actualiza->bindParam(':hraFinTrab',$hraFinTrab);
    $actualiza->bindParam(':fchDespacho',$fchDespacho);
    $actualiza->bindParam(':correlativo',$correlativo);
    $actualiza->execute();
    //return $actualiza->rowCount();

    if (isset($dataPost['cmbCFReten'])){
      foreach ($dataPost['cmbCFReten'] as $key => $idTrabajador) {
        $actualiza = $db->prepare("UPDATE `despachopersonal` SET `esReten` = 'Si' WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND idTrabajador = :idTrabajador ");
        $actualiza->bindParam(':fchDespacho',$fchDespacho);
        $actualiza->bindParam(':correlativo',$correlativo);
        $actualiza->bindParam(':idTrabajador',$idTrabajador);
        $actualiza->execute();
      }
    }


    $actualiza = $db->prepare("UPDATE `despachopuntos` SET `estado` = if(`estado` = 'Pendiente', 'No Entregado', `estado`) , subEstado = if(`subEstado` IS NULL OR `subEstado` = '', 'Rechaza Compra', `subEstado` ) WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
    $actualiza->bindParam(':fchDespacho',$fchDespacho);
    $actualiza->bindParam(':correlativo',$correlativo);
    $actualiza->execute();

    ///////////////////////////////////
    ////// PARA COMPLETAR CAMPOS //////
    ///////////////////////////////////
    calcularFinDespacho($db, $idProgramacion);
//$usaReten 
//    $placa    = $dataPost["txtCFplaca"];
 //   $hraInicio     = $dataPost["txtCFhraInicio"];
  //  $observacion   = $dataPost["txtCFobservacion"];
   // $ptoOrigen    = strtok("|");
    ////////////////////////////////////
    return 1;

  }

  function buscarInfoPagosyCobranzas($db, $dataPost){
    $idProgramacion = $dataPost["idProgramacion"];
    $fchDespacho = $dataPost["fchDespacho"];
    $correlativo = $dataPost["correlativo"];
    $arrData = array();

    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `codigo`, `costoUnit`, `cantidad`, `docCobranza`, `tipoDoc`, `pagado`, `observDetallePorCobrar`, `usuario`, `fchCreacion`, `usuarioCreaCobranza`, `fchCreaCobranza`  FROM `despachodetallesporcobrar` WHERE  `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $dataCobranzas = $consulta->fetchAll();

    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `placa`, `modoAcuerdo`, `costoDia`, `docTercero`, `docPagoTercero`, `docPagoTerTipo`, `pagado`, `guiaTrTercero`, `observPlacaTer`, `fchPago`, `fchCreacion`, `usuario`, `usuarioPagado` FROM `despachovehiculotercero` WHERE  `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $dataPagos = $consulta->fetchAll();

    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, desper.`idTrabajador`, concat(desper.`idTrabajador` ,'-' , `trab`.apPaterno, ' ', `trab`.apMaterno, ' ', `trab`.nombres ) AS tripulante, `valorRol`, concat(desper.`tipoRol` ,'/' , `trab`.categTrabajador) AS tipos , `valorAdicional`, `valorHraExtra`, `trabFchDespachoFin`, `trabHraInicio`, `trabHraFin`, `tipoRol`, `pagado`, `fchPago`, `anhio`, `mes`, `fchDesmarca`, `fchValManual`, desper.`usuario`, `fchActualizacion`, `observPersonal`, `ultProceso`, `ultProcesoFch`, `ultProcesoUsuario` FROM `despachopersonal` AS desper, trabajador AS trab WHERE `desper`.idTrabajador = `trab`.idTrabajador AND `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $dataTrip = $consulta->fetchAll();
    
    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `tipoOcurrencia`, `descripcion`, `montoTotal`, `subTipoOcurrencia`, `montoDistribuir`, `tipoDoc`, `nroDoc`, `codOcurrencia`, `montoTotal` - `montoDistribuir` FROM `ocurrencia` WHERE  `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $dataOcurren = $consulta->fetchAll();

    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `corrOcurrencia`, `tipoOcurrencia`, `tipoConcepto`, `descripcion`, `montoTotal`, `idTercero`, `fchPago`, `pagado`, `docPagoTercero`, `docPagoTerTipo` FROM `ocurrenciatercero` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $dataOcurrTerc = $consulta->fetchAll();

    $cadenaTrip = "<thead><tr><th>Trabajador</th><th>Rol</th><th>Hra Ini</th><th>Fch Fin</th><th>Hra Fin</th><th>Valor Rol</th><th>Valor Adicional</th><th>Valor Hra. Extra</th><th>Acciones</th></tr></thead>";

    $cadDataIngEgr = "<table width = '700px' >";
    $cadDataIngEgr .=  utf8_encode("<thead><tr><th width = '140px'>Concepto</th><th width = '60px'>Monto</th><th width = '190px' >Info. Proceso</th><th>Observaci�n</th></tr></thead>");

    $cadAuxDataTrip = "";
    $totDataTrip = 0;

    foreach ($dataTrip as $key => $value) {
      $cadenaTrip .= "<tr>";
      $cadenaTrip .= "<td width = '250px'>".$value["tripulante"]."</td>";
      $cadenaTrip .= "<td width = '135px'><input size = '16' id = 'vTrol".$value["idTrabajador"]."' name = 'vTrol".$value["idTrabajador"]."' value = '".$value["tipos"]."' readonly ></td>";
      $cadenaTrip .= "<td width = '80px'><input size = '9' id = 'vHrIni".$value["idTrabajador"]."' name = 'vHrIni".$value["idTrabajador"]."' value = '".$value["trabHraInicio"]."' readonly ></td>";
      $cadenaTrip .= "<td width = '90px'><input size = '11' id = 'vFcFin".$value["idTrabajador"]."' name = 'vFcFin".$value["idTrabajador"]."' value = '".$value["trabFchDespachoFin"]."' readonly ></td>";
      $cadenaTrip .= "<td width = '75px'><input size = '8' id = 'vHrFin".$value["idTrabajador"]."' name = 'vHrFin".$value["idTrabajador"]."' value = '".$value["trabHraFin"]."' readonly ></td>";

      $cadenaTrip .= "<td width = '55px'><input size = '5' id = 'vRol".$value["idTrabajador"]."' name = 'vRol".$value["idTrabajador"]."' value = '".$value["valorRol"]."' readonly ></td>";
      $cadenaTrip .= "<td width = '55px'><input size = '5' id = 'vAdic".$value["idTrabajador"]."' name = 'vAdic".$value["idTrabajador"]."' value = '".$value["valorAdicional"]."' readonly ></td>";
      $cadenaTrip .= "<td width = '55px'><input size = '5' id = 'vHEx".$value["idTrabajador"]."' name = 'vHEx".$value["idTrabajador"]."' value = '".$value["valorHraExtra"]."' readonly ></td>";
      $cadenaTrip .= "<td><img class = 'editar' border='0' src='imagenes/lapiz.png' width='13' height='13' align='left' id = ".$value["idTrabajador"]." ><img class = 'refrescar' border='0' src='imagenes/btnRefrescarT.png' width='13' height='13' align='left' id = ".$value["idTrabajador"]." fd = '$fchDespacho' c = '$correlativo' trl = '".$value["tipoRol"]."'  ></td>";

      $cadenaTrip .= "</tr>";

      $valTotTripulante = round($value["valorRol"]+ $value["valorAdicional"] + $value["valorHraExtra"],2);
      $totDataTrip += $valTotTripulante;

      $cadAuxDataTrip .= "<tr><td>".$value["idTrabajador"]."-".$value["tipoRol"]."</td><td align = 'right' >".number_format($valTotTripulante,2)."</td><td> Pagado: ".$value["pagado"]." - ".$value["fchPago"]."</td><td>".$value["observPersonal"]."</td></tr>";
    }

    $hraAdicCant = 0;
    $hraAdicCtoUnit = 0;
    $precioBaseServicio = 0;
    $auxAdicCtoUnit = 0;
    $auxAdicCant = 0;
    $cadAuxDataCobra = "";
    $totDataCobranzas = 0;
    $totDataEgrTerc = 0;

    foreach ($dataCobranzas as $key => $value) {
      if($value["codigo"] == "Despacho"){
        $precioBaseServicio = $value["costoUnit"];
      } else if($value["codigo"] == "HrasAdic"){
        $hraAdicCtoUnit = $value["costoUnit"];
        $hraAdicCant    = $value["cantidad"];
      } else if($value["codigo"] == "AuxAdic"){
        $auxAdicCtoUnit = $value["costoUnit"];
        $auxAdicCant    = $value["cantidad"];
      }

      $mntDetalle = round(($value["costoUnit"]*$value["cantidad"]),2);
      $totDataCobranzas += $mntDetalle;

      $cadAuxDataCobra .= "<tr><td>".$value["codigo"]."</td><td align = 'right'>".number_format($mntDetalle,2)."</td><td>Cobrado: ".$value["pagado"]." - ".$value["docCobranza"]." - ".$value["fchCreaCobranza"]."</td><td>".$value["observDetallePorCobrar"]."</td></tr>";
    }
    $cadAuxDataCobra .= "<tr><td align = 'right' ><b>TOTAL INGRESOS</b></td><td align = 'right'><b>".number_format($totDataCobranzas,2)."</b></td><td align = 'right' ><b>100.00 %</b></td></tr>";
    
    $modoAcuerdo = "";
    $costoDia    = 0;
    $unidadTercero = 0;

    $cadAuxDataPagosTerc = "";
    foreach ($dataPagos as $key => $value) {
      $unidadTercero = 1;
      $modoAcuerdo = $value["modoAcuerdo"];
      $costoDia    = $value["costoDia"];
      $totDataEgrTerc += $costoDia;
      $cadAuxDataPagosTerc .= "<tr><td>".$value["docTercero"]."-".$value["placa"]."</td><td align = 'right'>".number_format($costoDia,2)."</td><td>Pagado: ".$value["pagado"]." - ".$value["fchPago"]."</td><td>".$value["observPlacaTer"]."</td></tr>";
    }
    //$cadAuxDataPagosTerc .= "<tr><td align = 'right' ><b>Tot. Pago Tercero</b></td><td align = 'right'><b>".number_format($totDataEgrTerc,2)."</b></td></tr>";
    

    $cadDataIngEgr .= $cadAuxDataCobra;
    $cadDataIngEgr .= $cadAuxDataTrip;

    $porcTrip = ($totDataCobranzas == 0 ) ? 0 : round(100 * $totDataTrip / $totDataCobranzas,2);

    $cadDataIngEgr .= "<tr><td align = 'right' ><b>Tot. Pago Tripulante</b></td><td align = 'right'><b>".number_format($totDataTrip,2)."</b></td><td align = 'right'><b>".number_format($porcTrip,2)." %</b></td></tr>";

    $cadDataIngEgr .= $cadAuxDataPagosTerc;

    $porcPagosTer = ($totDataCobranzas == 0 ) ? 0 : round(100 * $totDataEgrTerc / $totDataCobranzas,2);

    $cadDataIngEgr .= "<tr><td align = 'right' ><b>Tot. Pago Tercero</b></td><td align = 'right'><b>".number_format($totDataEgrTerc,2)."</b></td><td align = 'right' ><b>".number_format($porcPagosTer,2)." %</b></td></tr>";

    $cadDataIngEgr .= "<tr><td align = 'right' ><b>TOTAL EGRESOS</b></td><td align = 'right'><b>".number_format($totDataEgrTerc + $totDataTrip,2)."</b></td><td align = 'right'><b>".($porcTrip + $porcPagosTer)." %</b></td></tr>";

    $cadDataIngEgr .= "<tr><td align = 'right' ><b>SALDO</b></td><td align = 'right'><b>".number_format($totDataCobranzas - ($totDataEgrTerc + $totDataTrip)  ,2)."</b></td><td align = 'right'><b>".(100 - ($porcTrip + $porcPagosTer))." %</b></td></tr>";

    $cadDataIngEgr .= "</table>";

    //Sobre las ocurrencias
    $cadOcurrenTodo = "";
    $cadOcurrenDesp = "<table width = '700'>";
    $cadOcurrenDesp .= utf8_encode("<thead><tr><th colspan = '6'>Ocurrencias del Despacho</th></th>");
    $cadOcurrenDesp .= utf8_encode("<tr><th width = '75'>Tipo</th><th width = '75'>SubTipo</th><th width = '260'>Descripci�n</th><th width = '50'>Mnt Total</th><th width = '50'>Mnt a Distribuir</th><th>Nro. Doc</th></thead> <tbody>"); 
    foreach ($dataOcurren as $key => $value) {
      $cadOcurrenDesp .= utf8_encode("<tr><td>".$value["tipoOcurrencia"]."</td><td>".$value["subTipoOcurrencia"]."</td><td>".$value["descripcion"]."</td><td align = 'right' >".number_format($value["montoTotal"],2)."</td><td align = 'right' >".number_format($value["montoDistribuir"],2)."</td><td>".$value["nroDoc"]."</td></tr>");
    }
    $cadOcurrenDesp .= "</tbody></table>";

    $cadOcurrenTerc = "<table width = '700'>";
    $cadOcurrenTerc .= utf8_encode("<thead><tr><th colspan = '6'>Ocurrencias Tercero</th></th>");
    $cadOcurrenTerc .= utf8_encode("<tr><th width = '110'>Tipo</th><th width = '250'>Descripci�n</th><th width = '50'>Mnt Total</th><th width = '50'>Pagado</th><th width = '60'>Fch Pago</th><th>Doc Pago Tercero</th></thead> <tbody>");

    $contHraExtra = $ocurrHraExtraMntTotal = 0;
    $contPersAdicional = $ocurrPersAdicMntTotal = 0;
    $ocurrHraExtraCant = $ocurrPersAdicCant = 0;
    foreach ($dataOcurrTerc as $key => $value) {
      /*
      Esta parte est� en desarrollo, todav�a faltan m�s pruebas*/
      /*
      if($value["tipoOcurrencia"] == 'HraExtra' && $contHraExtra == 0 ){
        $ocurrHraExtraMntTotal = $value["montoTotal"];
        $cad01 =  $value["descripcion"];
        $pos = strpos($cad01, "eedor:");
        $nvoInicio = $pos + 7;
        $nvaCad = substr($cad01 , $nvoInicio);
        $ocurrHraExtraCant = strtok($nvaCad," ");

        $contHraExtra = 1;
      } else if($value["tipoOcurrencia"] == 'Personal Adicional' && $contPersAdicional == 0 ){
        $ocurrPersAdicMntTotal = $value["montoTotal"];
        $cad01 =  $value["descripcion"];
        $pos = strpos($cad01, "eedor:");
        $nvoInicio = $pos + 7;
        $nvaCad = substr($cad01 , $nvoInicio);
        $ocurrPersAdicCant = strtok($nvaCad," ");

        $contPersAdicional = 1;
      } else {
        $cadOcurrenTerc .= utf8_encode("<tr><td>".$value["tipoOcurrencia"]." - ".$value["tipoConcepto"]."</td><td>".$value["descripcion"]."</td><td align = 'right' >".number_format($value["montoTotal"],2)."</td><td>".$value["pagado"]."</td><td>".$value["fchPago"]."</td><td>".$value["docPagoTercero"]."</td></tr>");
      }
      */
      
      $cadOcurrenTerc .= utf8_encode("<tr><td>".$value["tipoOcurrencia"]." - ".$value["tipoConcepto"]."</td><td>".$value["descripcion"]."</td><td align = 'right' >".number_format($value["montoTotal"],2)."</td><td>".$value["pagado"]."</td><td>".$value["fchPago"]."</td><td>".$value["docPagoTercero"]."</td></tr>");
      
    }

    $cadOcurrenTerc .= "</tbody></table>";
    $cadOcurrenTodo = $cadOcurrenDesp.$cadOcurrenTerc;

    $arrData = array(
      "unidadTercero"  => $unidadTercero,
      "costoDia"    => $costoDia,
      "modoAcuerdo" => $modoAcuerdo,
      "hraAdicCant" => $hraAdicCant,
      "hraAdicCtoUnit" => $hraAdicCtoUnit,
      "auxAdicCtoUnit" => $auxAdicCtoUnit,
      "auxAdicCant" => $auxAdicCant,
      "precioBaseServicio" => $precioBaseServicio,
      "cadenaTrip"  => $cadenaTrip,
      "cadDataIngEgr" => $cadDataIngEgr,
      "cadOcurrenTodo" => $cadOcurrenTodo,
      "totDataTrip" => $totDataTrip,
      "totDataEgrTerc" => $totDataEgrTerc,
      "totDataSaldo" => $totDataCobranzas - ($totDataEgrTerc + $totDataTrip),
      "ocurrHraExtraMntTotal" => $ocurrHraExtraMntTotal,
      "ocurrPersAdicMntTotal" => $ocurrPersAdicMntTotal,
      "ocurrHraExtraCant" => $ocurrHraExtraCant,
      "ocurrPersAdicCant" => $ocurrPersAdicCant
    );
    
    return $arrData;
  }

  function guardarValTripulante($db, $dataPost){

    if(!esUnaHora($dataPost["txtValTrabHraInicio"])){
      return "Error. La hora de inicio ingresada no es una hora";
    };

    if(!esUnaHora($dataPost["txtValTrabHraFin"])){
      return "Error. La hora de fin ingresada no es una hora";
    };

    if(!is_numeric( $dataPost["txtValTrabValorRol"] )){
      return "Error. El valor rol ingresado no es un n�mero";
    }

    if(!is_numeric( $dataPost["txtValTrabValorAdic"] )){
      return "Error. El valor adicional ingresado no es un n�mero";
    }

    if(!is_numeric( $dataPost["txtValTrabValorHrExtra"] )){
      return "Error. El valor hora extra ingresado no es un n�mero";
    }

    if (isset($dataPost['chkPrioridad']) && $dataPost['chkPrioridad'] == '1'){
      $fchValManual = '0000-00-00';
    } else{
      $fchValManual = Date("Y-m-d");
    }

    $actualiza = $db->prepare("UPDATE `despachopersonal` SET `valorRol` = :valorRol, `valorAdicional` = :valorAdicional, `valorHraExtra` = :valorHraExtra, `trabFchDespachoFin` = :trabFchDespachoFin, `trabHraInicio` = :trabHraInicio, `trabHraFin` = :trabHraFin, fchValManual = :fchValManual , observPersonal = :observPersonal WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `idTrabajador` = :idTrabajador AND `tipoRol` = :tipoRol");
    $actualiza->bindParam(':fchDespacho',$dataPost["fchDespValTrip"]);
    $actualiza->bindParam(':correlativo',$dataPost["correlValTrip"]);
    $actualiza->bindParam(':idTrabajador',$dataPost["txtValIdTrab"]);
    $actualiza->bindParam(':tipoRol',$dataPost["txtValTrabTipoRol"]);

    $actualiza->bindParam(':valorRol',$dataPost["txtValTrabValorRol"]);
    $actualiza->bindParam(':valorAdicional',$dataPost["txtValTrabValorAdic"]);
    $actualiza->bindParam(':valorHraExtra',$dataPost["txtValTrabValorHrExtra"]);
    $actualiza->bindParam(':trabFchDespachoFin',$dataPost["txtValTrabFchFin"]);
    $actualiza->bindParam(':trabHraInicio',$dataPost["txtValTrabHraInicio"]);
    $actualiza->bindParam(':trabHraFin',$dataPost["txtValTrabHraFin"]);
    $actualiza->bindParam(':fchValManual',$fchValManual);
    $actualiza->bindParam(':observPersonal',$dataPost["txtValObservTrip"]);
    $actualiza->execute();
    return $actualiza->rowCount();

  }

  function refrescarValDespDatosGen($db){
    $msj        = "";
    $auxCuenta   = $_POST["valCuenta"];
    $auxProducto = $_POST["valProducto"];
    $idPlaca    = $_POST["valPlaca"];
    $cambioPlaca = $_POST["cambioPlaca"];
    ////
    $cambioValHraAdic = $_POST["cambioValHraAdic"];
    $fchDespacho = $_POST["valFchDesp"];
    $hraIniBase = $_POST["valHraIniBase"];
    $hraInicio  = $_POST["valHraIni"];
    $lugarFinCliente = $_POST["valLugFinCli"];
    $hraFinCliente = $_POST["valHraFinCli"];
    $hraFin     = $_POST["valHraFin"];
    $fchDespachoFin = $_POST["valFchFin"];
    $fchDespachoFinCli = $_POST["valFchFinCli"];
    $kmIni   = $_POST["valKmInicio"] != "" ? $_POST["valKmInicio"] : 0; //  $_POST["valKmInicio"];
    $kmIniCli = $_POST["valKmIniCli"] != "" ? $_POST["valKmIniCli"] : 0; 
    $kmFin   = $_POST["valKmFin"] != "" ? $_POST["valKmFin"] : 0;
    $kmFinCli   = $_POST["valKmFinCli"] != "" ? $_POST["valKmFinCli"] : 0;

    if ($lugarFinCliente  == 'UltCliente' ){
      $kmFacturable = $kmFinCli - $kmIniCli;
    } else {
      $kmFacturable = $kmFin - $kmIniCli;
    }

    $kmTotal = $kmFin - $kmIniCli;

    $valHraAdic = $_POST["valHraAdic"];
    //$valFchDespacho = $_POST["valFchDespacho"];
    $valCorrelativo = $_POST["valCorrel"];

    ////
    $correlCuenta = strtok($auxCuenta, "|");
    $idProducto  = strtok($auxProducto, "|");

    //Validar producto
    $consulta = $db->prepare("SELECT count(*) AS cant, `idProducto`, `idCliente`, `correlativo`, `nombProducto`, `m3Facturable`, `puntos`, `idZona`, `zona`, `tipoProducto`, `estadoProducto`, `precioServ`, `kmEsperado`, `tolerKmEsperado`, `valKmAdic`, `hrasNormales`, `tolerHrasNormales`, `valHraAdic`, `hraIniEsperado`, `tolerHraIniEsperado`, `valAdicHraIniEsper`, `hraFinEsperado`, `tolerHraFinEsperado`, `valAdicHraFinEsper`, `nroAuxiliares`, `valAuxiliarAdic`, `cobrarPeaje`, `cobrarRecojoDevol`, `valConductor`, `hraNormalConductor`, `tolerHraCond`, `valHraAdicCond`, `valAuxiliar`, `hraNormalAux`, `tolerHraAux`, `valHraAdicAux`, `usoMaster`, `valUnidTercCCond`, `valUnidTercSCond`, `hrasNormalTerc`, `tolerHrasNormalTerc`, `valHraExtraTerc`, `valKmAdicTerc`, `valAuxTercero`, `contarDespacho`, `superCuenta` FROM `clientecuentaproducto` WHERE `idProducto` = :idProducto AND `correlativo` LIKE :correlativo ");
    $consulta->bindParam(':idProducto',$idProducto);
    $consulta->bindParam(':correlativo',$correlCuenta);
    $consulta->execute();
    $dataPdto = $consulta->fetch();
    if ($dataPdto["cant"] != 1) $msj = "Error. El producto indicado no existe para este cliente.";

    //Datos del vehiculo
    $consulta = $db->prepare("SELECT `vehter`.`idPlaca`, `nroVehiculo`, `estado`, `propietario`, `rznSocial`, `considerarPropio`, `m3Facturable`, `capCombustible`, `rendimiento`, `grupoRendimiento`, `eficCombustible`, `idEficSolesKm`, `combustibleFrec`, `descripcion`, `kmUltimaMedicion`, `vehter`.costoDia FROM `vehiculo` AS veh LEFT JOIN `vehiculotercero` AS vehter ON `veh`.idPlaca = `vehter`.idPlaca WHERE `veh`.idPlaca = :idPlaca");
    $consulta->bindParam(':idPlaca',$idPlaca);
    $consulta->execute();
    $dataPlaca = $consulta->fetch();

    //Datos de los auxiliares
    $consulta = $db->prepare("SELECT count(*) AS cant, sum(if(`trabajador`.categTrabajador = 'Tercero',1,0)) AS cantTerc FROM despachopersonal AS desper LEFT JOIN trabajador ON `desper`.idTrabajador = `trabajador`.idTrabajador WHERE tipoRol = 'Auxiliar' AND fchDespacho = :fchDespacho AND correlativo = :correlativo ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$valCorrelativo);
    $consulta->execute();
    $dataAuxiliares = $consulta->fetch();
    $esPlacaPropia = ($dataPlaca["rznSocial"] == "INVERSIONES MOY S.A.C." ) ? "Si":"No";
    $nroAuxDesp    = $dataAuxiliares["cant"];

    if ($esPlacaPropia == "No"){
      //Verificar dato del conductor
      $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, desper.`idTrabajador`, `valorRol`, `valorAdicional`, `valorHraExtra`, `trabFchDespachoFin`, `trabHraInicio`, `trabHraFin`, `tipoRol`, `trabajador`.categTrabajador FROM despachopersonal AS desper LEFT JOIN trabajador ON `desper`.idTrabajador = `trabajador`.idTrabajador WHERE tipoRol = 'Conductor' AND fchDespacho = :fchDespacho AND correlativo = :correlativo ");
      $consulta->bindParam(':fchDespacho',$fchDespacho);
      $consulta->bindParam(':correlativo',$valCorrelativo);
      $consulta->execute();
      $dataConductor = $consulta->fetch();

      if($dataConductor["categTrabajador"] == 'Tercero' ){
        $valUnidadTercero = $dataPdto["valUnidTercCCond"];
      } else {
        $valUnidadTercero = $dataPdto["valUnidTercSCond"];
      }
      $nroAuxDesTerc = $dataAuxiliares["cantTerc"];
    } else {
      $valUnidadTercero = 0;
      $nroAuxDesTerc = 0;
    }
    $valTercPersAdicTotal = $nroAuxDesTerc * $dataPdto["valAuxTercero"];

    $hrasNormales = $dataPdto["hrasNormales"];
    $toleranCobroHraExtra = $dataPdto["tolerHrasNormales"];
    $valKmAdic  = $dataPdto["valKmAdic"];
    $valAuxiliarAdic = $dataPdto["valAuxiliarAdic"];
    $valHraAdic =  ($cambioValHraAdic == 'Si') ? $valHraAdic : $dataPdto["valHraAdic"];
    $hraIniFch = $fchDespacho." ".$hraInicio;

    if ($lugarFinCliente == NULL){
      $hraFinCalculoSolo = $hraFin;
      $fchDespachoFinCalculo = $fchDespachoFin;
    } else if ($lugarFinCliente == "Base"){
      $hraFinCalculoSolo = $hraFin;
      $fchDespachoFinCalculo = $fchDespachoFin;
    } else {
      $hraFinCalculoSolo = $hraFinCliente;
      $fchDespachoFinCalculo = $fchDespachoFinCli;
    }
    //echo "lugarFinCliente: $lugarFinCliente, hraFinCalculoSolo: $hraFinCalculoSolo, fchDespachoFinCalculo $fchDespachoFinCalculo";
    $hraFinCalculo = $fchDespachoFinCalculo." ".$hraFinCalculoSolo;

    $duracionhhmmss = restahhmmss($hraIniFch,$hraFinCalculo);
    $tpoExtraHrasDecimalTrab = difEnHorasDecim($hrasNormales,$duracionhhmmss);
    //echo "hrasNormales  $hrasNormales, duracionhhmmss  $duracionhhmmss, tpoExtraHrasDecimalTrab  $tpoExtraHrasDecimalTrab";
    if($toleranCobroHraExtra == "" || $toleranCobroHraExtra == null) $toleranCobroHraExtra = '00:00:00';
    $arrHraTolerancia = explode(":", $toleranCobroHraExtra);
    $tiempoToleranciaEnSegundos = $arrHraTolerancia[0]*3600 + $arrHraTolerancia[1]*60 + $arrHraTolerancia[2];
    $tpoExtraHrasDecimalTrab = ($tpoExtraHrasDecimalTrab <= ($tiempoToleranciaEnSegundos/3600))?0:$tpoExtraHrasDecimalTrab;

    $kmAdicional = ($kmFacturable - ($dataPdto["kmEsperado"]+$dataPdto["tolerKmEsperado"]) > 0) ? $kmFacturable - $dataPdto["kmEsperado"]:0;
    $valTotKmAdic = $kmAdicional * $valKmAdic;
    //echo "tpoExtraHrasDecimalTrab: $tpoExtraHrasDecimalTrab, valHraAdic $valHraAdic";
    $valTotValHraAdic = $tpoExtraHrasDecimalTrab * $valHraAdic;

    ///////////////// C�lculos Adicionales Tercero 
    $hrasNormalTerc = $dataPdto["hrasNormalTerc"];
    //echo "hras Normales $hrasNormales, hras Nor Terc: $hrasNormalTerc";
    $tolerHrasNormalTerc = $dataPdto["tolerHrasNormalTerc"];
    $valHraExtraTerc = $dataPdto["valHraExtraTerc"];
    $tpoExtraHrasDecimalTerc = difEnHorasDecim($hrasNormalTerc,$duracionhhmmss);
    if($tolerHrasNormalTerc == "" || $tolerHrasNormalTerc == null) $tolerHrasNormalTerc = '00:00:00';
    $arrHraTolerancia = explode(":", $tolerHrasNormalTerc);
    $tiempoToleranciaEnSegundos = $arrHraTolerancia[0]*3600 + $arrHraTolerancia[1]*60 + $arrHraTolerancia[2];
    $tpoExtraHrasDecimalTerc = ($tpoExtraHrasDecimalTerc <= ($tiempoToleranciaEnSegundos/3600))?0:$tpoExtraHrasDecimalTerc;

    /*
    $kmAdicional = ($kmTotal - ($dataPdto["kmEsperado"]+$dataPdto["tolerKmEsperado"]) > 0) ? $kmTotal - $dataPdto["kmEsperado"]:0;
    $valTotKmAdic = $kmAdicional * $valKmAdic;
    */

    $valTercHraExtraTotal = $tpoExtraHrasDecimalTerc * $valHraExtraTerc;
    //////////////////////

    //Sobre los auxiliares adicionales
    $nroAuxiliaresCuenta = $dataPdto["nroAuxiliares"];
    $valAuxiliarAdic  = $dataPdto["valAuxiliarAdic"];

    $arrData = array(
      "mensaje"  => $msj,
      "precioBaseServicio" => $dataPdto["precioServ"],
      "hrasNormales" => $hrasNormales,
      "kmEsperado" => $dataPdto["kmEsperado"],
      "valHraAdic" => $valHraAdic,
      "valUnidadTercero" => $valUnidadTercero,
      "esPlacaPropia"    => $esPlacaPropia,
      "tpoExtraHrasDecimalTrab" => $tpoExtraHrasDecimalTrab,
      "kmTotal"  => $kmTotal,
      "kmAdicional"  => $kmAdicional,
      "valKmAdic"    => $valKmAdic,
      "valTotKmAdic" => $valTotKmAdic,
      "duracionhhmmss" => $duracionhhmmss,
      "valTotValHraAdic" => $valTotValHraAdic,
      "nroAuxiliaresCuenta" => $nroAuxiliaresCuenta,
      "valAuxiliarAdic" => $valAuxiliarAdic,
      "nroAuxDesp" => $nroAuxDesp,
      "valTercHraExtraTotal" => $valTercHraExtraTotal,
      "valDurAdicTerc" => $tpoExtraHrasDecimalTerc,
      "valHraAdicTercAdic" => $valHraExtraTerc,
      "valTercPersAdic" => $nroAuxDesTerc,
      "valTercValorAuxTerc" => $dataPdto["valAuxTercero"], 
      "valTercPersAdicTotal" => $valTercPersAdicTotal,
      "kmFacturable" => $kmFacturable,

// =  * 

      /*"modoAcuerdo" => $modoAcuerdo,
      "hraAdicCtoUnit" => $hraAdicCtoUnit,*/
    );
    return $arrData;
  }

  function buscarDataTrabajador($db){
    $idTrabajador = $_POST["idTrabajador"];

    $consulta = $db->prepare("SELECT `idTrabajador`, `tipoDocTrab`, `nroDocTrab`, `fchCaducidad`, `tipoTrabajador`, `categTrabajador`, `ruc`, `modoSueldo`, `estadoTrabajador`, `apPaterno`, `apMaterno`, `nombres`, `fchNacimiento`, `imgTrabajador`, `telfMovil`, `dirCalleyNro`, `dirDistrito`, `dirProvincia`, `dirDepartamento`, `dirTelefono`, `dirObservacion`, `dirNro`, `dirUrb`, `telfAdicional`, `telfReferencia`, `telfRefContacto`, `telfRefParentesco`, `sexo`, `apPaternoPadre`, `apMaternoPadre`, `nombresPadre`, `ocupacionPadre`, `apPaternoMadre`, `apMaternoMadre`, `nombresMadre`, `ocupacionMadre`, `apPaternoConyu`, `apMaternoConyu`, `ocupacionConyu`, `eMail`, `nombreConyuge`, `estadoCivil`, `estadoCivilDesde`, `nroHijos`, `sec01Anhio`, `sec01Grado`, `sec01Centro`, `sec02Anhio`, `sec02Grado`, `sec02Centro`, `sup01Desde`, `sup01Hasta`, `sup01Carrera`, `sup01Centro`, `sup01Avance`, `sup01Grado`, `sup02Desde`, `sup02Hasta`, `sup02Carrera`, `sup02Centro`, `sup02Avance`, `sup02Grado`, `exp01Empresa`, `exp01Cargo`, `exp01Sueldo`, `exp01Desde`, `exp01Hasta`, `exp01Jefe`, `exp01JefePuesto`, `exp01Telf`, `exp02Empresa`, `exp02Cargo`, `exp02Sueldo`, `exp02Desde`, `exp02Hasta`, `exp02Jefe`, `exp02JefePuesto`, `exp02Telf`, `exp03Empresa`, `exp03Cargo`, `exp03Sueldo`, `exp03Desde`, `exp03Hasta`, `exp03Jefe`, `exp03JefePuesto`, `exp03Telf`, `laboroEnMoy`, `laboroEnMoyPuesto`, `laboroEnMoySucursal`, `laboroEnMoyMotivoCese`, `laboroEnMoyFchCese`, `familiarEnMoy`, `familiarEnMoyNombre`, `familiarEnMoyParentesco`, `emergenciaNombre`, `emergenciaParentesco`, `emergenciaTelfFijo`, `emergenciaTelfCelular`, `saludGrupoSanguineo`, `saludTieneEnfCronica`, `saludTieneAlergia`, `saludAlergia`, `saludEnfCronica`, `gradoInstruccion`, `licenciaNro`, `licenciaVigencia`, `licenciaCategoria`, `modoContratacion`, `bancoNombre`, `bancoNroCuenta`, `bancoTipoCuenta`, `bancoMoneda`, `remuneracionBasica`, `asignacionFamiliar`, `minimoSemanal`, `cajaChica`, `renta5ta`, `diasVacacAnual`, `manejaLiquidacion`, `fondoGarantia`, `fondoGaranRetenido`, `esMaster`, `precioMaster`, `deseaDscto`, `deseaDsctoOnp`, `entidadPension`, `tipoComision`, `asumeEmpresa`, `descontarSeguro`, `cuspp`, `codTrabajador`, `formaPago`, `tallaCamisa`, `tallaPantalon`, `tallaBotas`, `tallaPolo`, `tallaCasaca`, `usuario`, `fchCreacion`, `usuarioUltimoCambio`, `fchUltimoCambio` FROM `trabajador` WHERE idTrabajador  = :idTrabajador ");
    $consulta->bindParam(':idTrabajador',$idTrabajador);
    $consulta->execute();
    return $consulta->fetch();

  }

  function guardarValidaDespacho($db){
    $usuario = $_SESSION["usuario"];
    /*echo "<pre>";
    print_r($_POST);
    echo "</pre>";*/
    //Los datos de la tripulaci�n. Si se cambi� manualmente queda con su valor, en caso contrario se recalcula.  
    foreach ($_POST as $campo => $dato) {
      //echo "KEY $key<br>";  vHEx  vTrol
      if(substr($campo,0,5) == "vTrol" ){
        $idTrabajador = substr($campo,5,50);
        $auxTipo = $dato;
        $vTrol = strtok($auxTipo, "/");
      } elseif(substr($campo,0,6) == "vHrIni" ){
        //$idTrabajador = substr($campo,6,50);
        $vHrIni = $dato;
      } else if(substr($campo,0,6) == "vFcFin" ){
        $vFcFin = $dato;
      } else if(substr($campo,0,6) == "vHrFin" ){
        $vHrFin = $dato;
      } else if(substr($campo,0,4) == "vRol" ){
        $vRol = $dato;
      } else if(substr($campo,0,5) == "vAdic" ){
        $vAdic = $dato;
      } else if(substr($campo,0,4) == "vHEx" ){
        $vHEx = $dato;
        $rpta = completarCalculosTrabajador($db, $txtValFchDespacho, $txtValCorrelativo, $idTrabajador, $vTrol, $vRol, $vAdic, $vHEx, $vHrFin, $vFcFin, $usuario);
        //echo "IdTrabajador $idTrabajador, Valor Rol $vRol";
        if($vTrol == 'Conductor'){
          $datos = algunosDatosTrabajador($db, $idTrabajador);
          $categConductor = $datos['categTrabajador'];
          $esMaster     = $datos['esMaster'];
          $precioMaster = $datos['precioMaster'];
        }
        //echo "RPTA $rpta";
      } else {
        $$campo = $dato;
      }
    }
/*
    echo "Info de los retenes";
    echo "<pre>";
    print_r($cmbValReten);

    foreach( $cmbValReten AS $valor ){
      echo "Valor $valor <br>";

    }
    echo "</pre>";
*/
    
    $correlCuenta  = strtok($txtValCuenta, "|");
    $cuenta = strtok("|");
    $tipoServicioPago = strtok("|");
    $idProducto = strtok($txtValProducto, "|");
    $idSedeOrigen = strtok($cmbValPuntoOrigen, "|");
    $ptoOrigen = strtok("|");
    $txtValPlaca = $txtValPlaca == "" ? NULL : $txtValPlaca;
    
    $actualiza = $db->prepare("UPDATE `despacho` SET `hraInicio` = :hraInicio, `hraFin` = :hraFin, `hraInicioBase` = :hraInicioBase, `kmInicio` = :kmInicio, `kmInicioCliente` = :kmInicioCliente, `kmFinCliente` = :kmFinCliente, `hraFinCliente` = :hraFinCliente, `kmFin` = :kmFin, placa = :placa, estadoDespacho = :estadoDespacho, fchDespachoFinCli = :fchDespachoFinCli, fchDespachoFin = :fchDespachoFin, `lugarFinCliente` = :lugarFinCliente, `correlCuenta` = :correlCuenta, `cuenta` = :cuenta, `tipoServicioPago` = :tipoServicioPago, `ptoOrigen` = :ptoOrigen, `idSedeOrigen` = :idSedeOrigen, `idProducto` = :idProducto, concluido = 'Si',  `tpoExtraHras` = 0, `observacion` = :observacion, usuarioGrabafin = :usuarioGrabafin, fchGrabaFin = curdate(), hraGrabaFin = curtime(), modoConcluido = 'Despachos2'  WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
    
    $actualiza->bindParam(':hraInicio',$txtValHraInicio);
    $actualiza->bindParam(':hraFin',$txtValHraFin);
    $actualiza->bindParam(':hraInicioBase',$txtValHraInicioBase);
    $actualiza->bindParam(':kmInicio',$txtValKmInicio);
    $actualiza->bindParam(':kmInicioCliente',$txtValKmInicioCliente);
    $actualiza->bindParam(':kmFinCliente',$txtValKmFinCliente);
    $actualiza->bindParam(':hraFinCliente',$txtValHraFinCliente);
    $actualiza->bindParam(':kmFin',$txtValKmFin);
    $actualiza->bindParam(':estadoDespacho',$txtValEstadoDespacho);
    $actualiza->bindParam(':lugarFinCliente',$cmbValLugarFinCli);
    $actualiza->bindParam(':fchDespachoFinCli',$txtValFchFinCli);
    $actualiza->bindParam(':usuarioGrabafin',$usuario);
    $actualiza->bindParam(':fchDespachoFin',$txtValFchFin);
    $actualiza->bindParam(':fchDespacho',$txtValFchDespacho);
    $actualiza->bindParam(':correlativo',$txtValCorrelativo);
    $actualiza->bindParam(':correlCuenta',$correlCuenta);
    $actualiza->bindParam(':cuenta',$cuenta);
    $actualiza->bindParam(':tipoServicioPago',$tipoServicioPago);
    $actualiza->bindParam(':placa',$txtValPlaca);
    $actualiza->bindParam(':idProducto',$idProducto);
    $actualiza->bindParam(':observacion',$txtValObservacion);
    $actualiza->bindParam(':idSedeOrigen',$idSedeOrigen);
    $actualiza->bindParam(':ptoOrigen',$ptoOrigen);
    //$actualiza->bindParam(':usaReten',$usaReten);
    $actualiza->execute();
    $actualizDesp = $actualiza->rowCount();

    //Actualizar caracteristicas del producto
      ///   ///   ///   ///   ///   ///   ///
    // Actualizar datos del producto seleccionado
    ///   ///   ///   ///   ///   ///   ///
    $actualiza = $db->prepare("UPDATE `despacho` AS `dest`, ( SELECT `clicue`.nombreCuenta, `clicue`.tipoCuenta, clicuepro.`idProducto`, clicuepro.`idCliente`, clicuepro.`correlativo`, clicuepro.`nombProducto`, clicuepro.`m3Facturable`, clicuepro.`puntos`, clicuepro.`idZona`, clicuepro.`zona`, clicuepro.`tipoProducto`, clicuepro.`estadoProducto`, clicuepro.`precioServ`, clicuepro.`kmEsperado`, clicuepro.`tolerKmEsperado`, clicuepro.`valKmAdic`, clicuepro.`hrasNormales`, clicuepro.`tolerHrasNormales`, clicuepro.`valHraAdic`, clicuepro.`hraIniEsperado`, clicuepro.`tolerHraIniEsperado`, clicuepro.`valAdicHraIniEsper`, clicuepro.`hraFinEsperado`, clicuepro.`tolerHraFinEsperado`, clicuepro.`valAdicHraFinEsper`, clicuepro.`nroAuxiliares`, clicuepro.`valAuxiliarAdic`, clicuepro.`cobrarPeaje`, clicuepro.`cobrarRecojoDevol`, clicuepro.`valConductor`, clicuepro.`hraNormalConductor`, clicuepro.`tolerHraCond`, clicuepro.`valHraAdicCond`, clicuepro.`valAuxiliar`, clicuepro.`hraNormalAux`, clicuepro.`tolerHraAux`, clicuepro.`valHraAdicAux`, clicuepro.`usoMaster`, clicuepro.`valUnidTercCCond`, clicuepro.`valUnidTercSCond`, clicuepro.`hrasNormalTerc`, clicuepro.`tolerHrasNormalTerc`, clicuepro.`valHraExtraTerc`, clicuepro.`valKmAdicTerc`, clicuepro.`valAuxTercero`, clicuepro.`contarDespacho`, clicuepro.`superCuenta` FROM  `clientecuentaproducto` AS clicuepro , clientecuentanew AS clicue WHERE `clicuepro`.idCliente = `clicue`.idCliente AND `clicuepro`.correlativo = `clicue`.correlativo AND idProducto = :idProducto) AS `src` SET  `dest`.`idProducto` = `src`.`idProducto`, `dest`.`correlCuenta` = `src`.`correlativo`, `dest`.`m3` = `src`.`m3Facturable`, `dest`.hrasNormales = `src`.hrasNormales, `dest`.tolerHrasNormales = `src`.tolerHrasNormales, `dest`.hraNormCondDespacho = `src`.hraNormalConductor, `dest`.tolerHraCondDespacho = `src`.tolerHraCond, `dest`.valHraAdicCondDespacho = `src`.valHraAdicCond, `dest`.hraNormAuxDespacho = `src`.hraNormalAux, `dest`.tolerHraAuxDespacho = `src`.tolerHraAux, `dest`.valHraAdicAuxDespacho = `src`.valHraAdicAux, `dest`.valorAuxAdicional = `src`.valAuxiliarAdic, `dest`.tipoServicioPago = `src`.tipoProducto, `dest`.topeServicioHraNormal = `src`.`tolerHraAux`, `dest`.valorAuxiliar = `src`.`valAuxiliar`, `dest`.nroAuxiliaresCuenta = `src`.`nroAuxiliares`, `dest`.valorConductor = `src`.`valConductor`, `dest`.costoHraExtra = `src`.`valHraAdic`, `dest`.recorridoEsperado = `src`.`kmEsperado`, `dest`.valorServicio = `src`.precioServ, `dest`.superCuenta = src.`superCuenta`, `dest`.igvServicio = round(0.18*`src`.precioServ,2), `dest`.usarMaster = `src`.usoMaster, `dest`.hraIniEsperado = `src`.hraIniEsperado, `dest`.hraFinEsperado = `src`.hraFinEsperado,  `dest`.tolerKmEsperado = `src`.tolerKmEsperado, `dest`.valUnidTercCCond = `src`.valUnidTercCCond, `dest`.valUnidTercSCond = `src`.valUnidTercSCond,  `dest`.hrasNormalTerc = `src`.hrasNormalTerc, `dest`.tolerHrasNormalTerc = `src`.tolerHrasNormalTerc, `dest`.valHraExtraTerc = `src`.valHraExtraTerc, `dest`.valKmAdicTerc = `src`.valKmAdicTerc, `dest`.valKmAdic = `src`.valKmAdic, `dest`.valAdicHraIniEsper = `src`.valAdicHraIniEsper, `dest`.zonaDespacho = `src`.zona, `dest`.valAdicHraFinEsper = `src`.valAdicHraFinEsper, `dest`.contarDespacho = `src`.contarDespacho, `dest`.cobrarPeaje = `src`.cobrarPeaje, `dest`.cobrarRecojoDevol = `src`.cobrarRecojoDevol, `dest`.cuenta = `src`.nombreCuenta, `dest`.puntosDesp = `src`.puntos, `dest`.valAuxTerceroDesp = `src`.valAuxTercero, `dest`.idZonaDespacho = `src`.idZona WHERE dest.`fchDespacho` = :fchDespacho AND dest.`correlativo` = :correlativo ");

    $actualiza->bindParam(':idProducto',$idProducto);
    $actualiza->bindParam(':fchDespacho',$txtValFchDespacho);
    $actualiza->bindParam(':correlativo',$txtValCorrelativo);    
    $actualiza->execute();
    ///   ///   ///

    //Actualiza costo de los despachos
    $elimina = $db->prepare("DELETE FROM `despachodetallesporcobrar` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `codigo` = 'Despacho' ");
    $elimina->bindParam(':fchDespacho',$txtValFchDespacho);
    $elimina->bindParam(':correlativo',$txtValCorrelativo);
    $elimina->execute();

    if($valPrecioBaseOriginal <> $txtValPrecBase2){
      $descripcion = "Se ha modificado el precio del despacho $txtValFchDespacho-$txtValCorrelativo de $valPrecioBaseOriginal a $txtValPrecBase2";
      logAccion($db, $descripcion, '', $txtValPlaca);
    }

    if($txtValPrecBase2 > 0){
      $inserta = $db->prepare("INSERT INTO `despachodetallesporcobrar` (`fchDespacho`, `correlativo`, `codigo`, `costoUnit`, `cantidad`, `pagado`, `observDetallePorCobrar`, `usuario`, `fchCreacion`) VALUES (:fchDespacho, :correlativo, 'Despacho', :costoUnit, 1, 'No', :observDetallePorCobrar, :usuario, curdate())");
      $inserta->bindParam(':fchDespacho',$txtValFchDespacho);
      $inserta->bindParam(':correlativo',$txtValCorrelativo);
      $inserta->bindParam(':costoUnit',$txtValPrecBase2);
      $inserta->bindParam(':observDetallePorCobrar',$txtValObservacion);
      $inserta->bindParam(':usuario',$usuario);
      $inserta->execute();

    } else {
      $descripcion = utf8_encode("El despacho $txtValFchDespacho-$txtValCorrelativo no gener� documento de cobranza por que el precio era cero. Precio original: $valPrecioBaseOriginal");
      logAccion($db, $descripcion, '', $txtValPlaca);
    }

    //Actualizando horas adicionales
    $elimina = $db->prepare("DELETE FROM `despachodetallesporcobrar` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `codigo` = 'HrasAdic' ");
    $elimina->bindParam(':fchDespacho',$txtValFchDespacho);
    $elimina->bindParam(':correlativo',$txtValCorrelativo);
    $elimina->execute();

    if($txtValTpoAdic > 0){
      $inserta = $db->prepare("INSERT INTO `despachodetallesporcobrar` (`fchDespacho`, `correlativo`, `codigo`, `costoUnit`, `cantidad`, `pagado`, `observDetallePorCobrar`, `usuario`, `fchCreacion`) VALUES (:fchDespacho, :correlativo, 'HrasAdic', :costoUnit, :cantidad, 'No', :observDetallePorCobrar, :usuario, curdate())");
      $inserta->bindParam(':fchDespacho',$txtValFchDespacho);
      $inserta->bindParam(':correlativo',$txtValCorrelativo);
      $inserta->bindParam(':costoUnit',$txtValHraAdic);
      $inserta->bindParam(':cantidad',$txtValTpoAdic);
      $inserta->bindParam(':observDetallePorCobrar',$txtValObservacion);
      $inserta->bindParam(':usuario',$usuario);
      $inserta->execute();

      $actHraExtra = $db->prepare("UPDATE `despacho` SET `tpoExtraHras` = :tpoExtraHras  WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
      $actHraExtra->bindParam(':tpoExtraHras',$txtValTpoAdic);
      $actHraExtra->bindParam(':fchDespacho',$txtValFchDespacho);
      $actHraExtra->bindParam(':correlativo',$txtValCorrelativo);
      $actHraExtra->execute();
    }

    if($txtValValorKmAdic > 0 && $txtValKmAdicional > 0){
      $inserta = $db->prepare("INSERT INTO `despachodetallesporcobrar` (`fchDespacho`, `correlativo`, `codigo`, `costoUnit`, `cantidad`, `pagado`, `observDetallePorCobrar`, `usuario`, `fchCreacion`) VALUES (:fchDespacho, :correlativo, 'KmExceso', :costoUnit, :cantidad, 'No', $observDetallePorCobrar, :usuario, curdate())");
      $inserta->bindParam(':fchDespacho',$txtValFchDespacho);
      $inserta->bindParam(':correlativo',$txtValCorrelativo);
      $inserta->bindParam(':costoUnit',$txtValValorKmAdic);
      $inserta->bindParam(':cantidad',$txtValKmAdicional);
      $inserta->bindParam(':observDetallePorCobrar',$txtValObservacion);
      $inserta->bindParam(':usuario',$usuario);
      $inserta->execute();
    }

    //Actualizar si hay un veh�culo de tercero involucrado
    //Elimino registro de pago de vehiculo tercero
    $elimina = $db->prepare("DELETE FROM `despachovehiculotercero` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
    $elimina->bindParam(':fchDespacho',$txtValFchDespacho);
    $elimina->bindParam(':correlativo',$txtValCorrelativo);
    $elimina->execute();

    //Verificar si la placa pertenece a un tercero
    $consulta = $db->prepare("SELECT vehiculo.`idPlaca`, `nroVehiculo`, `estado`, `propietario`, `rznSocial`, `considerarPropio`, `rendimiento`, `grupoRendimiento`, `eficCombustible`, `idEficSolesKm`, `costoDia`, `documento`, `terceroConPersonalMoy`, `modoAcuerdo` FROM `vehiculo` LEFT JOIN vehiculotercero ON `vehiculo`.idPlaca = `vehiculotercero`.idPlaca WHERE `vehiculo`.idPlaca = :idPlaca ");
    $consulta->bindParam(':idPlaca',$txtValPlaca);
    $consulta->execute();
    $auxData = $consulta->fetch();
    $esUnTercero = ($auxData["costoDia"] == NULL ) ? "No" : "Si";
    if($esUnTercero == "Si"){
      $docTercero = $auxData["rznSocial"]; // strtok($txtValCliente,"-");
      //Verificar datos sobre el conductor
      if($categConductor == "Tercero") {
        //$observConductor = "Es un Conductor Tercero";
        $observPlaca = "Precio con Conductor";
      } else {
        $observPlaca = "Precio sin Conductor";
      }

      $inserta = $db->prepare("INSERT INTO `despachovehiculotercero` (`fchDespacho`, `correlativo`, `placa`, `modoAcuerdo`, `costoDia`, `docTercero`, `pagado`, `observPlacaTer`, `fchCreacion`, `usuario`) VALUES (:fchDespacho, :correlativo, :placa, :modoAcuerdo, :costoDia, :docTercero, 'No', :observPlacaTer, curdate(), :usuario)");
      $inserta->bindParam(':fchDespacho',$txtValFchDespacho);
      $inserta->bindParam(':correlativo',$txtValCorrelativo);
      $inserta->bindParam(':placa',$txtValPlaca);
      $inserta->bindParam(':modoAcuerdo',$auxData["modoAcuerdo"]);
      $inserta->bindParam(':costoDia',$txtValTotUnTer);
      $inserta->bindParam(':docTercero',$docTercero);
      $inserta->bindParam(':observPlacaTer',$observPlaca);
      $inserta->bindParam(':usuario',$usuario);
      $inserta->execute();
      
      //// Adicionales ////
      /////////////////////
      $elimina = $db->prepare("DELETE FROM ocurrenciatercero WHERE fchDespacho = :fchDespacho AND correlativo = :correlativo AND tipoOcurrencia IN ('HraExtra','Personal Adicional') AND tipoConcepto = 'A favor proveedor'");
      $elimina->bindParam(':fchDespacho',$txtValFchDespacho);
      $elimina->bindParam(':correlativo',$txtValCorrelativo);
      $elimina->execute();

      if($txtValDurAdicTerc > 0 && $txtValHraAdicTercAdic > 0 ){
        $cantidad = insertaEnOcurrenciaTercero($db, $txtValFchDespacho, $txtValCorrelativo, 'HraExtra|A favor proveedor', $txtValDurAdicTerc, $txtValPlaca, $txtValHraAdicTercAdic);
      }

      //Revisando esta parte
      if($txtValTercPersAdic > 0 && $txtValTercValorAuxTerc > 0  ){
        $cantidad = insertaEnOcurrenciaTercero($db, $txtValFchDespacho, $txtValCorrelativo, 'Personal Adicional|A favor proveedor', $txtValTercPersAdic, $txtValPlaca, $txtValTercValorAuxTerc);
      }

    }

    //echo "COSTO DIA ".$auxData["costoDia"].", ES UN TERCERO  $esUnTercero";
    //Actualizando auxiliares adicionales
    //Actualizando horas adicionales
    $elimina = $db->prepare("DELETE FROM `despachodetallesporcobrar` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `codigo` = 'AuxAdic' ");
    $elimina->bindParam(':fchDespacho',$txtValFchDespacho);
    $elimina->bindParam(':correlativo',$txtValCorrelativo);
    $elimina->execute();

    $actualiza = $db->prepare("UPDATE `despachopersonal` SET  esReten = 'No' WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
    $actualiza->bindParam(':fchDespacho',$txtValFchDespacho);
    $actualiza->bindParam(':correlativo',$txtValCorrelativo);
    $actualiza->execute();

    $nroAuxReten = 0;
    if (isset($cmbValReten)){
      foreach ($cmbValReten as $key => $idTrabajador) {
        $nroAuxReten++;
        $actualiza = $db->prepare("UPDATE `despachopersonal` SET `esReten` = 'Si' WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND idTrabajador = :idTrabajador ");
        $actualiza->bindParam(':fchDespacho',$txtValFchDespacho);
        $actualiza->bindParam(':correlativo',$txtValCorrelativo);
        $actualiza->bindParam(':idTrabajador',$idTrabajador);
        $actualiza->execute();
      }
    }

    $consulta =$db->prepare("SELECT count(*) AS cant FROM despachopersonal WHERE tipoRol = 'Auxiliar' AND `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
    $consulta->bindParam(':fchDespacho',$txtValFchDespacho);
    $consulta->bindParam(':correlativo',$txtValCorrelativo);
    $consulta->execute();
    $dataDesPer = $consulta->fetch();
    $nroAuxiliares = $dataDesPer['cant'];

$consulta = $db->prepare("SELECT `fchDespacho`, des.`correlativo`, `idProgramacion`, `cli`.rznSocial, concat(des.`idCliente`,'-',`cli`.rznSocial ) AS rznCliente , `guiaCliente`, `hraInicio`, `fchDespachoFinCli`, `fchDespachoFin`, `hraFin`, `placa`, `considerarPropio`, `m3`, `valorServicio`, `igvServicio`, des.`idCliente`, `cuenta`, `correlCuenta`, `tipoServicioPago`, des.`nroAuxiliares`, `nroAuxiliaresAdic`, `costo`, `topeServicioHraNormal`, des.`hrasNormales`, des.`tolerHrasNormales`, `costoHraExtra`, `toleranCobroHraExtra`, `valorConductor`, `hraNormCondDespacho`, `tolerHraCondDespacho`, `valHraAdicCondDespacho`, `valorAuxiliar`, `hraNormAuxDespacho`, `tolerHraAuxDespacho`, `valHraAdicAuxDespacho`, `nroAuxiliaresCuenta`, `valorAuxAdicional`, `usarMaster`, `tpoExtraHras`, `costoTotal`, `nroGuias`, `nroDespachos`, `usaReten`, `concluido`, `hraInicioBase`, `kmInicio`, `kmInicioCliente`, `kmFinCliente`, `hraFinCliente`, `lugarFinCliente`, `kmFin`, `recorridoEsperado`, `zonaDespacho`, `idZonaDespacho`, `tolerHraIniEsperadoDesp`, `tolerHraFinEsperadoDesp`, `valAuxTerceroDesp`, `puntosDesp`, `ptoOrigen`, `idOrigen`, `idSedeOrigen`, `tipoDestino`, `ptoDestino`, `observacion`, `observCliente`, `docCobranza`, `tipoDoc`, `pagado`, `pagoEnPlanilla`, `terceroConPersonalMoy`, des.`contarDespacho`, des.`superCuenta`, des.`usuario`, des.`fchCreacion`, `modoCreacion`, `estadoDespacho`, `usuarioGrabaFin`, `fchGrabaFin`, `hraGrabaFin`, `usuarioCreaCobranza`, `fchCreaCobranza`, des.`idProducto`, clicuepro.`nombProducto`, des.`valKmAdic`, des.`hraIniEsperado`, des.`valAdicHraIniEsper`, des.`hraFinEsperado`, des.`valAdicHraFinEsper`, des.`cobrarPeaje`, des.`cobrarRecojoDevol`, des.`valUnidTercCCond`, des.`valUnidTercSCond`, des.`hrasNormalTerc`, des.`tolerHrasNormalTerc`, des.`valHraExtraTerc`, des.`valKmAdicTerc`, des.`tolerKmEsperado`, des.`movilAsignado`, `editaUsuarioDesp`, `usuarioAsignado`, `modoTerminado`, `modoConcluido`, `fchTerminado`, `usuarioTerminado`  FROM `despacho` AS des, cliente AS cli, clientecuentaproducto AS clicuepro WHERE `des`.idProducto = `clicuepro`.idProducto AND `des`.idCliente = `cli`.idRuc AND des.`fchDespacho` = :fchDespacho AND des.`correlativo` = :correlativo ");
  $consulta->bindParam(':fchDespacho',$txtValFchDespacho);
  $consulta->bindParam(':correlativo',$txtValCorrelativo);$consulta->execute();
  $dataDesp = $consulta->fetch();
  $nroAuxCuenta = $dataDesp["nroAuxiliaresCuenta"];
  $valorAuxAdicional = $dataDesp["valorAuxAdicional"];

  if($nroAuxiliares - $nroAuxReten - $nroAuxCuenta > 0)
    $nroAuxAdic = $nroAuxiliares - $nroAuxReten - $nroAuxCuenta ;
  else
    $nroAuxAdic = 0;

  if($nroAuxAdic > 0 ){
      $inserta = $db->prepare("INSERT INTO `despachodetallesporcobrar` (`fchDespacho`, `correlativo`, `codigo`, `costoUnit`, `cantidad`, `pagado`, `observDetallePorCobrar`, `usuario`, `fchCreacion`) VALUES (:fchDespacho, :correlativo, 'AuxAdic', :costoUnit, :cantidad, 'No', :observDetallePorCobrar, :usuario, curdate())");
      $inserta->bindParam(':fchDespacho',$txtValFchDespacho);
      $inserta->bindParam(':correlativo',$txtValCorrelativo);      
      $inserta->bindParam(':costoUnit',$valorAuxAdicional);
      $inserta->bindParam(':cantidad',$nroAuxAdic);
      $inserta->bindParam(':observDetallePorCobrar',$txtValObservacion);
      $inserta->bindParam(':usuario',$usuario);
      $inserta->execute();
    }
    return $actualizDesp;
    
  }

  function ctaPdtos($db){
    $idCliente = $_POST["idCliente"];
    $auxCorrelCuenta = $_POST["idCuenta"];
    $correlCuenta = strtok($auxCorrelCuenta, "|");

    $consulta = $db->prepare("SELECT `idProducto`, `idCliente`, `correlativo`, `nombProducto`, `m3Facturable`, `puntos`, `idZona`, `zona`, `tipoProducto`, `estadoProducto`, `precioServ`, `kmEsperado`, `tolerKmEsperado`, `valKmAdic`, `hrasNormales`, `tolerHrasNormales`, `valHraAdic`, `hraIniEsperado`, `tolerHraIniEsperado`, `valAdicHraIniEsper`, `hraFinEsperado`, `tolerHraFinEsperado`, `valAdicHraFinEsper`, `nroAuxiliares`, `valAuxiliarAdic`, `cobrarPeaje`, `cobrarRecojoDevol`, `valConductor`, `hraNormalConductor`, `tolerHraCond`, `valHraAdicCond`, `valAuxiliar`, `hraNormalAux`, `tolerHraAux`, `valHraAdicAux`, `usoMaster`, `valUnidTercCCond`, `valUnidTercSCond`, `hrasNormalTerc`, `tolerHrasNormalTerc`, `valHraExtraTerc`, `valKmAdicTerc`, `valAuxTercero`, `contarDespacho`, `superCuenta` FROM `clientecuentaproducto` WHERE `idCliente` LIKE :idCliente AND `correlativo` LIKE :correlativo");
    $consulta->bindParam(':idCliente', $idCliente);
    $consulta->bindParam(':correlativo', $correlCuenta);
    $consulta->execute();
    $data = $consulta->fetchAll();
    $cadenaCtaPdtos = "<option>-</option>";
    /*foreach ($data as $key => $value) {
      $cadena .= "<option>".$value['subestado']."</option>";
    }*/

    foreach($data as $itemPdto) {
      $idItemProducto = $itemPdto["idProducto"];
      $nombProducto = $itemPdto["nombProducto"];
      $m3Facturable = $itemPdto["m3Facturable"];

      $cadenaCtaPdtos .= "<option  value = '$idItemProducto' >".$idItemProducto."|".$nombProducto."|".$m3Facturable. " m3";
      $cadenaCtaPdtos .= "</option>";
    }
    return $cadenaCtaPdtos;
  }


  function prepararTripulacionDespacho($db){
    $fchDespacho = $_POST["fchDespacho"];
    $correlativo = $_POST["correlativo"];

    $dataTrip = buscarInfoTripulacion($db, $fchDespacho, $correlativo);

    $cantTrip = count($dataTrip);
    $i = 1;

    $cadenaTrip = "<thead><tr>
                        <th width = '55' >Rol</th><th width = '250' >Trabajador</th>
                        <th width = '45' >Porc</th><th width = '50' >#Partes</th><th>Fch Ini</th>
                      </tr></thead>";
    $cadenaTrip .= "<tbody>";
    foreach ($dataTrip as $key => $value) {
      $idTrabajador = $value["idTrabajador"];
      $nombCompleto = $value["nombCompleto"];
      $categTrabaj = $value["categTrabajador"];
      $rol = $value["tipoRol"];

      $cadenaTrip .= "<tr>";
      $cadenaTrip .= "<td>$rol</td><td>$nombCompleto</td>";
      $cadenaTrip .= "<td align = 'center'><input style='padding: 3px; width: 35px' type = 'text' name = 'porc$i' id = 'porc$i'></td>";
      $cadenaTrip .= "<td align = 'center'><input style='padding: 3px; width: 35px' type = 'text' name = 'part$i' id = 'part$i'></td>";
      $cadenaTrip .= "<td align = 'center'><input class = 'fchOcurrenciaTrip' style='padding: 3px; width: 80px' type = 'text' name = 'fchI$i' id = 'fchI$i'></td>";
      $cadenaTrip .= "<input type='hidden' name= 'idTrab$i' value = '$idTrabajador' >";
      $cadenaTrip .= "</tr>";
      $i++;
    }
    $cadenaTrip .= "</tbody>";
    $cadenaTrip .= "<input type='hidden' name= 'cantTrip' value = '$cantTrip' >";
    echo $cadenaTrip;
  }

  function guardarOcurrenciaDespacho($db){
    /*echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    */

    $usuario = $_SESSION["usuario"];

    $fchDespacho = $_POST['fchDespachoOcurr'];
    $correlativo = $_POST['correlativoOcurr'];
    $descripcion = $_POST['txtDescripcion'];
    $mntTotal = $_POST['txtMntTotal'];
    $mntDistribuir = $_POST['txtMntDistribuir'];
    $totalInvolucrados = $_POST['cantTrip'];
    $tipoOcurrencia = $_POST['cmbTipoOcurrencia'];
    $subTipoItem = isset($_POST['subTipoItem']) ?$_POST['subTipoItem'] : '' ;

    $tipoDoc = $_POST['cmbTipoDoc'];
    if ($tipoDoc == '00' ){
      $nroDoc = generaNroVale($db);
    } else {
      $nroDoc  = $_POST['txtNroDoc'];
    }

    $codOcurrencia = generaCodOcurrencia($db);

    $codSubTipoItem = "";
    if ($tipoOcurrencia == 'Descuento'){
      if ($subTipoItem == 'Dscto por Responsab') $codSubTipoItem = 'DsctoResp';
      else if ($subTipoItem == 'Dscto por Documento') $codSubTipoItem = 'DsctoDocum';
      else if ($subTipoItem == 'Dscto por Papeleta') $codSubTipoItem  = 'DsctoPapel';
      else if ($subTipoItem == 'Dscto Otros') $codSubTipoItem  = 'DsctoOtros';
      $auxTipoItem = $tipoOcurrencia."|".$codSubTipoItem;
    } else {
      $auxTipoItem = $tipoOcurrencia;
    }

    //Grabando ocurrencia
    //echo "$fchDespacho,$correlativo,$auxTipoItem,$descripcion,$mntTotal,$mntDistribuir,$codOcurrencia,$tipoDoc,$nroDoc";
    insertaEnOcurrencia($db,$fchDespacho,$correlativo,$auxTipoItem,$descripcion,$mntTotal,$mntDistribuir,$codOcurrencia,$tipoDoc,$nroDoc);

    for ($i = 1; $i <= $totalInvolucrados; $i++ ){
    //$idxTrab = 'idTrabajador'.$i;
      $porcentaje = $_POST['porc'.$i];
      if (!empty($porcentaje)){
        $idTrabajador = $_POST['idTrab'.$i];
        $nroPartes = $_POST['part'.$i];
        $fch = $_POST['fchI'.$i];
        $mntCuota = round($mntDistribuir*($porcentaje/100)/$nroPartes,2);
        for ($j = 1; $j <= $nroPartes; $j++ ){
          $anio = substr($fch,0,4);
          $mes = substr($fch,5,2);
          $dia = substr($fch,8,2);               
          $inserta = $db->prepare("INSERT INTO `ocurrenciaconsulta` (`fchDespacho`, `correlativo`, `tipoOcurrencia`, `subTipoOcurrencia`, `descripcion`, `montoTotal`, `nroCuota`,`nroCuotas`, `idTrabajador`, `fchPago`, `tipo`, `montoCuota`,  `codOcurrencia`,`usuario`, `fchCreacion`, `hraCreacion`) VALUES (:fchDespacho, :correlativo, :tipoOcurrencia , :subTipoOcurrencia , :descripcion,:mntTotal, :nroCuota, :nroCuotas, :idTrabajador, :fchPago, 'pago', :mntCuota,:codOcurrencia,  :usuario, curdate(), curtime())");
          $inserta->bindParam(':fchDespacho',$fchDespacho);
          $inserta->bindParam(':correlativo',$correlativo);
          $inserta->bindParam(':tipoOcurrencia',$tipoOcurrencia);
          $inserta->bindParam(':subTipoOcurrencia',$codSubTipoItem);
          $inserta->bindParam(':descripcion',$descripcion);
          $inserta->bindParam(':mntTotal',$mntTotal);
          $inserta->bindParam(':nroCuota',$j);
          $inserta->bindParam(':nroCuotas',$nroPartes);
          $inserta->bindParam(':idTrabajador',$idTrabajador);
          $inserta->bindParam(':fchPago',$fch);
          $inserta->bindParam(':mntCuota',$mntCuota);
          $inserta->bindParam(':codOcurrencia',$codOcurrencia);
          $inserta->bindParam(':usuario',$usuario);
          $inserta->execute();
          $fch = date("Y-m-d",mktime(0,0,0,$mes,$dia+15,$anio));  
        }
        $actualiza = $db->prepare("UPDATE `ocurrenciaconsulta` SET `descripcion` = replace(`descripcion`,'&#209;','�'),   descripcion = replace(descripcion,'&#241;','�')  WHERE `fchDespacho` = :fchDespacho AND  `correlativo` = :correlativo AND  `tipoOcurrencia` = :tipoOcurrencia ");
        $actualiza->bindParam(':fchDespacho',$fchDespacho);
        $actualiza->bindParam(':correlativo',$correlativo);
        $actualiza->bindParam(':tipoOcurrencia',$tipoOcurrencia);
        $actualiza->execute();
              
      //echo "Involucrado $i id $idTrabajador   porcentaje $porcentaje   partes $nroPartes  fchInicio $fchIni  <br>";
      }  
    }
    return 1;
  }

  function subirFotoServidor($db){
    
    echo "<pre>";print_r($_FILES);echo "</pre>";
    
    $idTrabajador = $_POST["idFotoTrabajador"];
    if(isset($_FILES["nuevaFoto"]["tmp_name"])){
      if($_FILES["nuevaFoto"]["type"] == "image/jpeg"){
        $foto = imagecreatefromjpeg($_FILES["nuevaFoto"]["tmp_name"]);
        $rutaCortaFoto = "imagenes/data/trabajador/".$idTrabajador.".jpg";
        $imagen = $idTrabajador.".jpg";
        $rutaCompletaFoto = getcwd()."/../".$rutaCortaFoto;
        unlink($rutaCompletaFoto);
        $resultado = imagejpeg($foto,$rutaCompletaFoto);
      } else if($_FILES["nuevaFoto"]["type"] == "image/png"){
        $foto = imagecreatefrompng($_FILES["nuevaFoto"]["tmp_name"]);
        $rutaCortaFoto = "imagenes/data/trabajador/".$idTrabajador.".png";
        $imagen = $idTrabajador.".png";
        $rutaCompletaFoto = getcwd()."/../".$rutaCortaFoto;
        unlink($rutaCompletaFoto);
        $resultado = imagepng($foto,$rutaCompletaFoto);
      }
      echo "ruta completo foto: $rutaCompletaFoto";
    }

    if($resultado === true){

      //UPDATE `trabajador` SET `imgTrabajador` = 'imgTrabajador' WHERE `trabajador`.`idTrabajador` = '10593655';
      $actualiza = $db->prepare("UPDATE `trabajador` SET `imgTrabajador` = :imagen WHERE `idTrabajador` = :idTrabajador ");
      $actualiza->bindParam(':imagen',$imagen);
      $actualiza->bindParam(':idTrabajador',$idTrabajador);
      $actualiza->execute();
      return 1;// $actualiza->rowCount();

    
    }

    return 0;
    //imagenes/data/trabajador/anonymous.png

  }


  function  elimDespacho($db){
    $fchDespacho = $_POST['txtElimFchDespacho'];
    $correlativo = $_POST['txtElimCorrelativo'];
    $idProgramacion = $_POST['txtElimIdProgramacion'];
    $placa = 
    $idTrabajador = "";

    $elimina = $db->prepare("DELETE FROM despacho WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND idProgramacion = :idProgramacion");
    $elimina->bindParam(':fchDespacho',$fchDespacho);
    $elimina->bindParam(':correlativo',$correlativo);
    $elimina->bindParam(':idProgramacion',$idProgramacion);
    $elimina->execute();
    if($elimina->rowCount() == 1 ){
      $descripcion = "Se ha eliminado el despacho $fchDespacho-$correlativo.";
      logAccion($db, $descripcion, $idTrabajador, $placa);

      $elimina = $db->prepare("DELETE FROM despachodetallesporcobrar WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
      $elimina->bindParam(':fchDespacho',$fchDespacho);
      $elimina->bindParam(':correlativo',$correlativo);
      $elimina->execute();
      if($elimina->rowCount() >0 ){
        $descripcion = "En el proceso de eliminar el despacho $fchDespacho-$correlativo, ha eliminado ".$elimina->rowCount()." detalles por cobrar relacionados al mismo.";
        logAccion($db, $descripcion, $idTrabajador, $placa);
      }

      $elimina = $db->prepare("DELETE FROM ocurrencia WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `tipoOcurrencia` = 'terceroMoy'");
      $elimina->bindParam(':fchDespacho',$fchDespacho);
      $elimina->bindParam(':correlativo',$correlativo);
      $elimina->execute();
      if($elimina->rowCount() >0 ){
        $descripcion = "En el proceso de eliminar el despacho $fchDespacho-$correlativo, ha eliminado ".$elimina->rowCount()." registro(s) en la tabla ocurrencia del tipo 'terceroMoy' relacionados al mismo";
        logAccion($db, $descripcion, $idTrabajador, $placa);
      }

      $elimina = $db->prepare("DELETE FROM ocurrenciaconsulta WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `tipoOcurrencia` = 'terceroMoy'");
      $elimina->bindParam(':fchDespacho',$fchDespacho);
      $elimina->bindParam(':correlativo',$correlativo);
      $elimina->execute();
      if($elimina->rowCount() >0 ){
        $descripcion = "En el proceso de eliminar el despacho $fchDespacho-$correlativo, ha eliminado ".$elimina->rowCount()." registro(s) en la tabla ocurrenciaconsulta del tipo 'terceroMoy' relacionados al mismo";
        logAccion($db, $descripcion, $idTrabajador, $placa);
      }

      $elimina = $db->prepare("DELETE FROM despachopersonal WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
      $elimina->bindParam(':fchDespacho',$fchDespacho);
      $elimina->bindParam(':correlativo',$correlativo);
      $elimina->execute();
      if($elimina->rowCount() >0 ){
        $descripcion = "En el proceso de eliminar el despacho $fchDespacho-$correlativo, ha eliminado ".$elimina->rowCount()." registro(s) en despachopersonal";
        logAccion($db, $descripcion, $idTrabajador, $placa);
      }

      $elimina = $db->prepare("DELETE FROM despachoguiaporte WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
      $elimina->bindParam(':fchDespacho',$fchDespacho);
      $elimina->bindParam(':correlativo',$correlativo);
      $elimina->execute();
      if($elimina->rowCount() >0 ){
        $descripcion = "En el proceso de eliminar el despacho $fchDespacho-$correlativo, ha eliminado ".$elimina->rowCount()." registro(s) en la tabla despachoguiaporte relacionados al mismo";
        logAccion($db, $descripcion, $idTrabajador, $placa);
      }

      $elimina = $db->prepare("DELETE FROM despachopuntos WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
      $elimina->bindParam(':fchDespacho',$fchDespacho);
      $elimina->bindParam(':correlativo',$correlativo);
      $elimina->execute();
      if($elimina->rowCount() >0 ){
        $descripcion = "En el proceso de eliminar el despacho $fchDespacho-$correlativo, ha eliminado ".$elimina->rowCount()." registro(s) en la tabla despachopuntos";
        logAccion($db, $descripcion, $idTrabajador, $placa);
      }

      $elimina = $db->prepare("DELETE FROM despachoripley WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
      $elimina->bindParam(':fchDespacho',$fchDespacho);
      $elimina->bindParam(':correlativo',$correlativo);
      $elimina->execute();
      if($elimina->rowCount() >0 ){
        $descripcion = "En el proceso de eliminar el despacho $fchDespacho-$correlativo, ha eliminado ".$elimina->rowCount()." registro(s) en la tabla despachoripley";
        logAccion($db, $descripcion, $idTrabajador, $placa);
      }

      $elimina = $db->prepare("DELETE FROM despachoubicacion WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
      $elimina->bindParam(':fchDespacho',$fchDespacho);
      $elimina->bindParam(':correlativo',$correlativo);
      $elimina->execute();
      if($elimina->rowCount() >0 ){
        $descripcion = "En el proceso de eliminar el despacho $fchDespacho-$correlativo, ha eliminado ".$elimina->rowCount()." registro(s) en la tabla despachoubicacion relacionados al mismo";
        logAccion($db, $descripcion, $idTrabajador, $placa);
      }

      $elimina = $db->prepare("DELETE FROM despachovehiculotercero WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
      $elimina->bindParam(':fchDespacho',$fchDespacho);
      $elimina->bindParam(':correlativo',$correlativo);
      $elimina->execute();
      if($elimina->rowCount() >0 ){
        $descripcion = "En el proceso de eliminar el despacho $fchDespacho-$correlativo, ha eliminado ".$elimina->rowCount()." registro(s) en la tabla despachovehiculotercero relacionados al mismo";
        logAccion($db, $descripcion, $idTrabajador, $placa);
      }

      $elimina = $db->prepare("DELETE FROM `ocurrenciatercero` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
      $elimina->bindParam(':fchDespacho',$fchDespacho);
      $elimina->bindParam(':correlativo',$correlativo);
      $elimina->execute();
      if($elimina->rowCount() >0 ){
        $descripcion = "En el proceso de eliminar el despacho $fchDespacho-$correlativo, ha eliminado ".$elimina->rowCount()." registro(s) en la tabla ocurrenciatercero relacionados al mismo";
        logAccion($db, $descripcion, $idTrabajador, $placa);
      }
  
      $descrip = "$fchDespacho-$correlativo";
      $elimina = $db->prepare("DELETE FROM `prestamo` WHERE `tipoItem`= 'HraExtra' AND `descripcion` like '%$descrip'");
      $elimina->execute();
      if($elimina->rowCount() >0 ){
        $descripcion = "En el proceso de eliminar el despacho $fchDespacho-$correlativo, ha eliminado ".$elimina->rowCount()." registro(s) en la tabla prestamo del tipo 'HraExtra' relacionados al mismo";
        logAccion($db, $descripcion, $idTrabajador, $placa);
      }
      return 1;
    } else {
      return 0;
    }
  }

  function validarPasarEstadoAnterior($db){
    /*echo "<pre>";
    print_r($_POST);
    echo "</pre>";*/
    $fchDespacho = $_POST['fchDespacho'];
    $correlativo = $_POST['correlativo'];
    $idProgramacion = $_POST['idProgramacion'];
    $deshabilitar = "No";

    ///Personal
    $consulta = $db->prepare("SELECT count(*) AS cant FROM `despachopersonal` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND pagado != 'No' ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $auxData = $consulta->fetch();
    if($auxData["cant"] == 0 ) $rptaPersonal = "Puede proceder";
    else {
      $rptaPersonal = utf8_encode("Pago a personal tramitado, no puede proceder");
      $deshabilitar = "Si";
    }

    ///Detalles por cobrar
    $consulta = $db->prepare("SELECT count(*) AS cant FROM despachodetallesporcobrar WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND pagado != 'No' ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $auxData = $consulta->fetch();
    if($auxData["cant"] == 0 ) $rptaCobranza = "Puede proceder";
    else {
      $rptaCobranza = utf8_encode("Cobro de detalles tramitado, no puede proceder");
      $deshabilitar = "Si";
    }
    ///Veh�culo tercero
    $consulta = $db->prepare("SELECT count(*) AS cant FROM despachovehiculotercero WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND pagado != 'No' ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $auxData = $consulta->fetch();
    if($auxData["cant"] == 0 ) $rptaVehTer = "Puede proceder";
    else {
      $rptaVehTer = utf8_encode("Pago veh�culo tercero tramitado, no puede proceder");
      $deshabilitar = "Si";
    }

    ///Ocurrencia
    $consulta = $db->prepare("SELECT count(*) AS cant FROM ocurrenciaconsulta WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND pagado != 'No' ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $auxData = $consulta->fetch();
    if($auxData["cant"] == 0 ) $rptaOcurr = "Puede proceder";
    else {
      $rptaOcurr = "Ocurrencias tramitadas, no puede proceder";
      $deshabilitar = "Si";
    }

    ///Ocurrencia Tercero
    $consulta = $db->prepare("SELECT count(*) AS cant FROM ocurrenciatercero WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND pagado != 'No' ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $auxData = $consulta->fetch();
    if($auxData["cant"] == 0 ) $rptaOcurrTer = "Puede proceder";
    else {
      $rptaOcurrTer = "Ocurrencias tercero tramitadas, no puede proceder";
      $deshabilitar = "Si";
    }


    $arrData = array(
      "rptaPersonal" => $rptaPersonal,
      "rptaCobranza" => $rptaCobranza,
      "rptaVehTer"  => $rptaVehTer,
      "rptaOcurr"   => $rptaOcurr,
      "rptaOcurrTer" => $rptaOcurrTer,
      "deshabilitar" => $deshabilitar

      /*"modoAcuerdo" => $modoAcuerdo,
      "hraAdicCtoUnit" => $hraAdicCtoUnit,*/
    );
    return $arrData;
  }

  function pasarValidadoATerminado($db){

    $fchDespacho = $_POST['txtFchDepachoVaT'];
    $correlativo = $_POST['txtCorrelativoVaT'];

    $fchDespValid = $_POST['fchDespachoVaT'];
    $correlValid = $_POST['correlativoVaT'];

    if( ($fchDespacho == $fchDespValid  ) && ($correlativo == $correlValid) ){
      $actualiza = $db->prepare("UPDATE `despacho` SET `concluido` = 'No' WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
      $actualiza->bindParam(':fchDespacho',$fchDespacho);
      $actualiza->bindParam(':correlativo',$correlativo);
      $actualiza->execute();
      if($actualiza->rowCount() == 1 ){
        $descripcion = "Se ha vuelto de Validado a Terminado el despacho $fchDespacho-$correlativo, idProgramacion ".$_POST["txtIdProgramacionVaT"];
        logAccion($db, $descripcion, NULL, NULL);
      }
      return $actualiza->rowCount();
    } else return -1;

  }

  function getIdCotizacion($db){
    $consulta = $db->prepare("SELECT id_cotizacion FROM `cotizacion` ORDER BY id_cotizacion DESC LIMIT 1");
    $consulta->execute();
    $data = $consulta->fetchAll();
    $id_cotizacion = "";
    foreach ($data as $key => $value) {
      $id_cotizacion = $value['id_cotizacion'];
    }
    $id_cotizacionNuevo = Date("Y") . "-" . Date("m") . "001";
    if($id_cotizacion != ""){
      $partsIdCotizacion = explode("-", $id_cotizacion);
      $correlativo = substr($partsIdCotizacion[1], -3);
      if(strlen($correlativo+1) == 1){
        $ceros = "00";
      }
      else if(strlen($correlativo+1) == 2){
        $ceros = "0";
      }
      else if(strlen($correlativo+1) == 3){
        $ceros = "";
      }
      $id_cotizacionNuevo = Date("Y") . "-" . Date("m") . $ceros . ($correlativo + 1);
    }
    return $id_cotizacionNuevo;
  }

  function getEmail($db){
    $usuario = $_POST['usuario'];
    if($usuario == ""){
      $usuario = $_SESSION['usuario'];
    }
    $consulta = $db->prepare("SELECT `dni` FROM `usuario` WHERE `idUsuario` = :usuario LIMIT 1");
    $consulta->bindParam(':usuario',$usuario);
    $consulta->execute();
    $dataDni = $consulta->fetchAll();
    $dni = '';
    foreach($dataDni as $item) {
    	$dni = $item['dni'];
    }

    $consulta = $db->prepare("SELECT `eMail` FROM `trabajador` WHERE `idTrabajador` = :dni LIMIT 1");
    $consulta->bindParam(':dni',$dni);
    $consulta->execute();
    $dataEmail = $consulta->fetchAll();
    $eMail = '';
    foreach($dataEmail as $item) {
    	$eMail = $item['eMail'];
    }
    return $eMail;
  }

  function contactosCliente($db){
    $idCliente = $_POST["idCliente"];
    $consulta = $db->prepare("SELECT idNombreCompleto FROM `clientecontacto` WHERE idRuc = :idCliente");
    $consulta->bindParam(':idCliente',$idCliente);
    $consulta->execute();
    $cadena = "<option>Seleccione el contacto</option>";
    $data = $consulta->fetchAll();
    foreach ($data as $key => $value) {
      $cadena .= "<option>".$value['idNombreCompleto']."</option>";
    }
    return $cadena;
  }

  function buscarDataServicios($db){
    $id_cotizacion = $_POST["id_cotizacion"];
    $consulta = $db->prepare("SELECT direccionSalida, referenciaSalida, fechaSalida, horaSalida, contactoSalida, telefonoSalida, emailSalida, direccionLlegada, referenciaLlegada, fechaLlegada, horaLlegada, contactoLlegada, telefonoLlegada, emailLlegada, direccionRetorno, referenciaRetorno, fechaRetorno, horaRetorno, contactoRetorno, telefonoRetorno, emailRetorno, detalles FROM `cotiz_servicios` WHERE id_cotizacion = :id_cotizacion");
    $consulta->bindParam(':id_cotizacion',$id_cotizacion);
    $consulta->execute();
    $arreglo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    return $arreglo;
  }

  function buscarDataCliente($db){
    $idCliente = $_POST["idCliente"];
    $consulta = $db->prepare("SELECT concat(dirCalleNro, ' - ', dirDistrito) AS direccion, formaPago FROM `cliente` WHERE idRuc = :idCliente");
    $consulta->bindParam(':idCliente',$idCliente);
    $consulta->execute();
    return $consulta->fetch();
  }

  function buscarDataContactoCliente($db){
    $idNombreCompleto = $_POST["idNombreCompleto"];
    $consulta = $db->prepare("SELECT telefono, email FROM `clientecontacto` WHERE idNombreCompleto = :idNombreCompleto");
    $consulta->bindParam(':idNombreCompleto',$idNombreCompleto);
    $consulta->execute();
    return $consulta->fetch();
  }

  function nvoIdOT($db){
    $fecha = $_POST["fecha"];
    $arrPartes = explode("-",$fecha);
    $anio = $arrPartes[0];
    $consulta = $db->prepare("SELECT (RIGHT((orden_trabajo), 4) * 1) AS orden_trabajo FROM historial_mantenimiento WHERE YEAR(fecha) = :anio ORDER BY orden_trabajo DESC LIMIT 1");
    $consulta->bindParam(':anio',$anio);
    $consulta->execute();
    $data = $consulta->fetchAll();
    $orden_trabajo = "";
    foreach ($data as $key => $value) {
      $orden_trabajo = $value['orden_trabajo'];
    }
    $orden_trabajoNuevo = "OT" . substr($anio, -2) . "-" . "0001";
    if($orden_trabajo != ""){
      $correlativo = $orden_trabajo + 1;
      $length = strlen($correlativo);
      $ceros = "";
      if($length == 1){
        $ceros = "000";
      }
      else if($length == 2){
        $ceros = "00";
      }
      else if($length == 3){
        $ceros = "0";
      }
      $orden_trabajoNuevo = "OT" . substr($anio, -2) . "-" . $ceros . $correlativo;
    }
    return $orden_trabajoNuevo;
  }

  function buscarDataIncluye($db){
    $id_cotizacion = $_POST["id_cotizacion"];
    $consulta = $db->prepare("SELECT g.descripcion FROM `cotiz_incluye` c INNER JOIN gestion_sst_covid g ON g.id = c.id_incluye WHERE c.id_cotizacion = :id_cotizacion");
    $consulta->bindParam(':id_cotizacion',$id_cotizacion);
    $consulta->execute();
    $arreglo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    return $arreglo;
  }

  function talleres($db){
    $consulta = $db->prepare("SELECT CONCAT(id_taller, ' - ', nombre) AS taller FROM `talleres`");
    $consulta->execute();
    $cadena = "<option>Seleccione el taller</option>";
    $data = $consulta->fetchAll();
    foreach ($data as $key => $value) {
      $cadena .= "<option>".$value['taller']."</option>";
    }
    return $cadena;
  }

  function buscarDataServicio($db){
    $id_servicio = $_POST["id_servicio"];
    $consulta = $db->prepare("SELECT clase_servicio, tipo_mantenimiento FROM `servicios` WHERE id_servicio = :id_servicio");
    $consulta->bindParam(':id_servicio',$id_servicio);
    $consulta->execute();
    return $consulta->fetch();
  }

  function buscarClientesSelect($db){
    $consulta = $db->prepare("SELECT `documento`, `tipoDocumento`, `nombreCompleto`, t1.cant, if(t1.cant IS NULL,0,1) AS prioridad FROM `vehiculodueno` left join (SELECT docTercero, count( * ) AS cant FROM `despachovehiculotercero` WHERE `docPagoTercero` IS NULL AND DATEDIFF( curdate( ) , fchDespacho ) <120 GROUP BY docTercero ) AS t1 ON documento = t1.docTercero WHERE  `vehiculodueno`.estadoTercero = 'Activo' ORDER BY prioridad DESC, nombreCompleto");
    $consulta->execute();
    $terceros = $consulta->fetchAll();

    echo "<option>-</option>";
    foreach($terceros as $item) { 
      echo "<option>";
      echo $item['documento']."-".$item['tipoDocumento']."-".$item['nombreCompleto']." --> Referencia: ".$item['cant'];
      echo "</option>";
     };
  }

  function buscarDetPreliquid($db){
    $fchDespacho = $_POST["fchDespacho"];
    $correlativo = $_POST["correlativo"];
    $codigo = $_POST["codigo"];
    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `codigo`, `costoUnit`, `cantidad`, `docCobranza`, `tipoDoc`, `pagado`, `idPreliquid`, `observDetallePorCobrar`, `usuario`, `fchCreacion`, `usuarioCreaCobranza`, `fchCreaCobranza` FROM `despachodetallesporcobrar` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `codigo` = :codigo");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->bindParam(':codigo',$codigo);
    $consulta->execute();
    return $consulta->fetch();
  }
  function buscarDataInfraccion($db){
    $infraccionD = $_POST["infraccionD"];
    $consulta = $db->prepare("SELECT infraccion,monto,descuento FROM `infracciones` WHERE falta = :infraccionD");
    $consulta->bindParam(':infraccionD',$infraccionD);
    $consulta->execute();
    return $consulta->fetch();
  }
  function buscarDataTelf($db){
    $telefono = $_POST["telefono"];
    $consulta = $db->prepare("SELECT nombres FROM `sustento_telefono` where numero = :telefono  order by fecha_registro desc limit 1");
    $consulta->bindParam(':telefono',$telefono);
    $consulta->execute();
    return $consulta->fetch();
  }
  function buscarComisariaTelf($db){
    $distrito = $_POST["distrito"];
    $consulta = $db->prepare("SELECT `comisaria` AS dato FROM `comisaria` WHERE `distrito`=:distrito ");
    $consulta->bindParam(':distrito',$distrito);
    $consulta->execute();
    $cadena = "<option>SELECCIONE EL GRIFO</option>";
    $comisarias = $consulta->fetchAll();
    foreach ($comisarias as $key => $value) {
      $cadena .= "<option>".$value['dato']."</option>";
    }
    return $cadena;
  }

?>
