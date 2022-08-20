<?php
  session_start();
  //
  $nombre_fichero = './librerias/varios.php';

  if (file_exists($nombre_fichero)) {
    require_once './librerias/varios.php';
  };

  global $servidor, $bd, $usuario, $contrasenia;
  $db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);

  function buscarInfoTrabajadores($db,$idTrabajador = NULL){
    $where = ($idTrabajador == NULL)?"":" AND trabajador.idTrabajador = :idTrabajador "; 
    $consulta = $db->prepare("SELECT `idTrabajador`, `fchCaducidad`, `tipoTrabajador`, `categTrabajador`, `ruc`, `modoSueldo`, `estadoTrabajador`, `apPaterno`, `apMaterno`, `nombres` FROM `trabajador` WHERE estadoTrabajador = 'Activo'  $where ");
    if ($idTrabajador != NULL) $consulta->bindParam(':idTrabajador',$idTrabajador);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function buscarInfoUbicaciones($db,$idUbicacion = NULL){
    $where = ($idUbicacion == NULL)?"":" AND idUbicacion = :idUbicacion "; 
    $consulta = $db->prepare("SELECT `idUbicacion` , `descripcion` , `codPostal` , `idZona` , `fchCreacion` FROM `ubicacion` WHERE 1  $where ");
    if ($idUbicacion != NULL) $consulta->bindParam(':idUbicacion',$idUbicacion);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function buscarInfoVehiculos($db,$idPlaca = NULL){
    $where = ($idPlaca == NULL)?"":" AND idPlaca = :idPlaca "; 
    $consulta = $db->prepare("SELECT `idPlaca`, `nroVehiculo`, `estado`, `propietario`, `rznSocial`, `dimLargo`, `dimAlto`, `dimAncho`, `dimInteriorLargo`, `dimInteriorAncho`, `dimInteriorAlto`, `m3Facturable`, `kmUltimaMedicion`   FROM `vehiculo` WHERE `estado` LIKE 'activo'  $where ");
    if ($idPlaca != NULL) $consulta->bindParam(':idPlaca',$idPlaca);
    $consulta->execute();
    return $consulta->fetchAll();
  }

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

function verificarUsuario($db,$usuario,$pass){
  $nombre = "";
  $success = false;
  $crearDesdeMovil = false;
  $consulta = $db->prepare("SELECT * FROM usuario WHERE idUsuario = :iduser AND tipoUsuario IN ('Movil','Mixto') Limit 1 ");
  $consulta->bindParam(':iduser',$usuario);
  $consulta->execute();
  $fila = $consulta->fetch();
  if (empty($fila['idUsuario'])){
    $error = "Lo lamento, hay un error en su usuario";
    $statusCode = "";
  } else {
    $dbHash = $fila['contrasenia'];
    //echo "FILA 1".$dbHash;
    if (crypt($pass, $dbHash) == $dbHash){
      $fchVenc = $fila['fchVencimiento'];
      //echo "Vencimiento".$fchVenc.", Actual ".Date("Y-m-d");
      if ($fchVenc >= Date("Y-m-d")){
        $nombre = utf8_encode($fila['nombre']);
        $error = "";
        $success = true;
        $statusCode = "200";
      } else {
        $error = "Usuario vencido";  
        $statusCode = "";
      }
      if($fila['crearDesdeMovil'] == 'Si') $crearDesdeMovil = True;
    } else {
      $error = "Usuario no autentificado";
      $statusCode = "";
    }
  };
  $rpta = array(
    "nombre" => $nombre,
    "error"  => $error,
    "statusCode" => $statusCode,
    "success"=> $success,
    "crearDespachos" => $crearDesdeMovil,
    "token" =>  ""
    );
  return $rpta;
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

//Se sigue usando???
function generarNuevoCorrelPtoEntrega($db,$idProgramacion){
  $consulta = $db->prepare("SELECT correlPunto FROM `despachopuntos` WHERE `idProgramacion` = :idProgramacion ");
  $consulta->bindParam(':idProgramacion',$idProgramacion);
  $consulta->execute();
  $aux = $consulta->fetchAll();
  $nvoCorrel = 1;
  foreach ($aux as $item) {
    $ultNumero = $item['correlPunto'];
    $nvoCorrel = 1*$ultNumero + 1;
  }
  return $nvoCorrel;
}

function insertaPuntoEntrega($db,$idProgramacion,$nombComprador,$distrito, $guiaCliente, $estado,  $hraLlegada, $hraSalida, $observacion, $tipoPunto, $guiaMoy){
  $idPunto = generarNuevoIdPunto($db);
  $correlPunto = generarNuevoCorrelPtoEntrega($db,$idProgramacion);
  //echo "idPunto $idPunto, correlpunto $correlPunto ";
  $inserta = $db->prepare("INSERT INTO `despachopuntos` (`idPunto`,`idProgramacion`,`idRuta`, `correlPunto`, `nombComprador`, `distrito`,  `guiaCliente`,  `guiaMoy`, `estado`, `tipoPunto`, `hraLlegada`, `hraSalida`, `observacion`, `creacModo`, `creacFch`) VALUES ( :idPunto, :idProgramacion, :idRuta, '$correlPunto', :nombComprador, :distrito, :guiaCliente, :guiaMoy, :estado, :tipoPunto, :hraLlegada, :hraSalida, :observacion, 'Trackmoy' , now())");

  $inserta->bindParam(':idPunto',$idPunto);
  $inserta->bindParam(':idRuta',$idPunto);
  $inserta->bindParam(':idProgramacion',$idProgramacion);
  $inserta->bindParam(':nombComprador',$nombComprador);
  $inserta->bindParam(':distrito',$distrito);
  $inserta->bindParam(':guiaCliente',$guiaCliente);
  $inserta->bindParam(':guiaMoy',$guiaMoy);
  $inserta->bindParam(':estado',$estado);
  $inserta->bindParam(':tipoPunto',$tipoPunto);
  $inserta->bindParam(':hraLlegada',$hraLlegada);
  $inserta->bindParam(':hraSalida',$hraSalida);
  $inserta->bindParam(':observacion',$observacion);
  $inserta->execute();
  if ($inserta->rowCount() == 0){
    return 0;
  } else {
    //Completa campos
    $actualiza = $db->prepare("UPDATE  despachopuntos despun, despacho AS des SET `despun`.fchDespacho = `des`.fchDespacho , `despun`.correlativo = `des`.correlativo WHERE `des`.idProgramacion = `despun`.idProgramacion AND `des`.idProgramacion = :idProgramacion ");
    $actualiza->bindParam(':idProgramacion',$idProgramacion);
    $actualiza->execute();
    //Devuelve el idPunto
    return $idPunto;
  }
}

//Se sigue usando???
//function modificaInicioDespacho($db,$idProgramacion, $cliente, $cuenta, $kmInicioBase, $kmFinBase, $hraInicioBase, $hraFinBase, $kmInicioCliente, $kmFinCliente, $hraInicioCliente, $hraFinCliente, $observacion, $usuario, $estado = NULL){
function modificaInicioDespacho($db,$idProgramacion, $cliente, $cuenta, $producto, $horaInicio, $movilAsignado, $placa, $estado, $observacion, $usuario){
  $actualiza = $db->prepare("UPDATE `despacho` SET `hraInicio` = :horaInicio, `idCliente` = :cliente, `idProducto` = :producto, `cuenta` = :cuenta, movilAsignado = :movilAsignado, placa = :placa,  estadoDespacho = :estado, observacion = :observacion, `usuario` = :usuario  WHERE `idProgramacion` = :idProgramacion ");

  $actualiza->bindParam(':idProgramacion',$idProgramacion);
  $actualiza->bindParam(':cliente',$cliente);
  $actualiza->bindParam(':cuenta',$cuenta);
  $actualiza->bindParam(':producto',$producto);
  $actualiza->bindParam(':horaInicio',$horaInicio);
  $actualiza->bindParam(':movilAsignado',$movilAsignado);
  $actualiza->bindParam(':placa',$placa);
  $actualiza->bindParam(':estado',$estado);
  $actualiza->bindParam(':observacion',$observacion);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();
  return $actualiza->rowCount();
}

function modificaPuntoEntrega($db, $idProgramacion, $idPunto, $nombComprador, $distrito, $guiaCliente, $estado, $subestado, $hraLlegada, $hraSalida, $ordenPunto, $observacion, $tipoPunto = '', $longRecib = '', $latRecib = '', $guiaMoy = ''){
  $actualiza = $db->prepare("UPDATE `despachopuntos` SET idProgramacion = :idProgramacion, nombComprador = :nombComprador, `estado` = :estado, `subestado` = :subestado, distrito = :distrito, `hraLlegada` = :hraLlegada, `hraSalida` = :hraSalida, `tipoPunto` = :tipoPunto, guiaCliente = :guiaCliente, `ordenPunto` = :ordenPunto, guiaMoy = :guiaMoy, longRecib = :longRecib, latRecib = :latRecib, `observacion` = :observacion  WHERE `idPunto` = :idPunto");

  $actualiza->bindParam(':idProgramacion',$idProgramacion);
  $actualiza->bindParam(':idPunto',$idPunto);
  $actualiza->bindParam(':nombComprador',$nombComprador);
  $actualiza->bindParam(':distrito',$distrito);
  $actualiza->bindParam(':guiaCliente',$guiaCliente);
  $actualiza->bindParam(':guiaMoy',$guiaMoy);
  $actualiza->bindParam(':tipoPunto',$tipoPunto);
  $actualiza->bindParam(':estado',$estado);
  $actualiza->bindParam(':subestado',$subestado);
  $actualiza->bindParam(':hraLlegada',$hraLlegada);
  $actualiza->bindParam(':hraSalida',$hraSalida);
  $actualiza->bindParam(':ordenPunto',$ordenPunto);
  $actualiza->bindParam(':longRecib',$longRecib);
  $actualiza->bindParam(':latRecib',$latRecib);
  $actualiza->bindParam(':observacion',$observacion);
  $actualiza->execute();
  return $actualiza->rowCount();
}

function eliminaPuntoEntrega($db,$idProgramacion,$idPunto){
  //echo "$idProgramacion,$idPunto";
  $elimina = $db->prepare("DELETE FROM `despachopuntos` WHERE `idProgramacion` = :idProgramacion AND `idPunto` = :idPunto");
  $elimina->bindParam(':idProgramacion',$idProgramacion);
  $elimina->bindParam(':idPunto',$idPunto);
  $elimina->execute();
  return $elimina->rowCount();
}

//Ya no se usa???
  function insertaImagen($db,$fchDespacho,$correlativo, $correlPunto ,$name){
    $nroFoto = substr($name,-2);
    $actualiza = $db->prepare("UPDATE `movildespachopuntos` SET `foto$nroFoto` = :name  WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `correlPunto` = :correlPunto");
    $actualiza->bindParam(':fchDespacho',$fchDespacho);
    $actualiza->bindParam(':correlativo',$correlativo);
    $actualiza->bindParam(':correlPunto',$correlPunto);
    $actualiza->bindParam(':name',$name);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function buscarDataDespacho($db,$fchDespacho = NULL,$correlativo = NULL){
    $where = "";
    $where .= ($fchDespacho == NULL)?"":" AND fchDespacho = :fchDespacho ";
    $where .= ($correlativo == NULL)?"":" AND correlativo = :correlativo ";
    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `guiaCliente`, `hraInicio`, `fchDespachoFin`, `hraFin`, `placa`, `m3`, `valorServicio`, `igvServicio`, `idCliente`, `cuenta`, `tipoServicioPago`, `nroAuxiliares`, `nroAuxiliaresAdic`, `costo`, `topeServicioHraNormal`, `costoHraExtra`, `toleranCobroHraExtra`, `valorConductor`, `valorAuxiliar`, `nroAuxiliaresCuenta`, `valorAuxAdicional`, `usarMaster`, `tpoExtraHras`, `costoTotal`, `nroGuias`, `nroDespachos`, `usaReten`, `concluido`, `hraInicioBase`, `kmInicio`, `kmInicioCliente`, `kmFinCliente`, `ptoDestino`, `kmFin`, `hraFinCliente`, `estado` FROM `despacho`  WHERE 1 $where ");
    if($fchDespacho != NULL) $consulta->bindParam(':fchDespacho',$fchDespacho);
    if($correlativo != NULL) $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function buscarDataTripulacion($db,$fchDespacho = NULL,$correlativo = NULL, $tipoRol = NULL){
    $where = "";
    $where .= ($fchDespacho == NULL)?"":" AND fchDespacho = :fchDespacho ";
    $where .= ($correlativo == NULL)?"":" AND correlativo = :correlativo ";
    $where .= ($tipoRol == NULL)?"":" AND tipoRol = :tipoRol ";
    
    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, despachopersonal.`idTrabajador`, `valorRol`, `valorAdicional`, `trabFchDespachoFin`, `trabHraInicio`, `trabHraFin`, `tipoRol`, `estadoPer`, `pagado`, `fchPago`, `anhio`, `mes`, `fchDesmarca`, despachopersonal.`usuario`, `fchActualizacion`, despachopersonal.`ultProceso`, despachopersonal.`ultProcesoFch`, concat(`trabajador`.apPaterno,' ',`trabajador`.apMaterno,', ',`trabajador`.nombres) AS nombCompleto FROM `despachopersonal`, trabajador WHERE `despachopersonal`.idTrabajador = `trabajador`.idTrabajador  $where ");
    if($fchDespacho != NULL) $consulta->bindParam(':fchDespacho',$fchDespacho);
    if($correlativo != NULL) $consulta->bindParam(':correlativo',$correlativo);
    if($tipoRol != NULL) $consulta->bindParam(':tipoRol',$tipoRol);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function guardaDataRegistroDespacho($db, $fchDespacho,$correlativo,$hraInicio,$placa, $m3, $fchDespachoFin,$guia,$valorServicio,$igvServicio,$idCliente,$cuenta,$topeServicioHraNormal,$tipoServicioPago,$valorConductor,$valorAuxiliar, $valorServicioHraExtra,$toleranCobroHraExtra,$nroAuxiliaresCuenta,$valorAuxAdicional,$usarMaster,$recorridoEsperado,$usaReten,$modoCreacion,$kmInicio,$fchCreacion, $kmInicioCliente, $kmFinCliente, $kmFin, $hraInicioBase, $hraFinCliente, $hraFin, $ptoDestino, $observacion, $usuario){

    $inserta = $db->prepare("INSERT INTO despacho (`fchDespacho`, `correlativo`, hraInicio, placa, m3, fchDespachoFin, guiaCliente ,valorServicio, igvServicio, idCliente , cuenta, tipoServicioPago, topeServicioHraNormal, valorConductor, valorAuxiliar, costoHraExtra, toleranCobroHraExtra, nroAuxiliaresCuenta, valorAuxAdicional, usarMaster, recorridoEsperado, usaReten, modoCreacion, kmInicio,   kmInicioCliente, kmFinCliente, kmFin, hraInicioBase, hraFinCliente, hraFin, ptoDestino, concluido, observacion, usuario, fchCreacion) VALUES ( :fchDespacho, :correlativo,  :hraInicio, :placa, :m3, :fchDespachoFin, :guiaCliente, :valorServicio, :igvServicio, :idCliente, :cuenta, :tipoServicioPago, :topeServicioHraNormal, :valorConductor, :valorAuxiliar , :costoHraExtra, :toleranCobroHraExtra, :nroAuxiliaresCuenta, :valorAuxAdicional, :usarMaster, :recorridoEsperado, :usaReten, :modoCreacion, :kmInicio, :kmInicioCliente, :kmFinCliente, :kmFin, :hraInicioBase, :hraFinCliente, :hraFin, :ptoDestino, 'Si' , :observacion, :usuario, :fchCreacion)");
    $inserta->bindParam(':fchDespacho',$fchDespacho);
    $inserta->bindParam(':correlativo',$correlativo);
    $inserta->bindParam(':hraInicio',$hraInicio);
    $inserta->bindParam(':placa',$placa);
    $inserta->bindParam(':m3',$m3);
    $inserta->bindParam(':fchDespachoFin',$fchDespachoFin);
    $inserta->bindParam(':guiaCliente',$guia);
    $inserta->bindParam(':valorServicio',$valorServicio);
    $inserta->bindParam(':igvServicio',$igvServicio);
    $inserta->bindParam(':idCliente',$idCliente);
    $inserta->bindParam(':cuenta',$cuenta);
    $inserta->bindParam(':tipoServicioPago',$tipoServicioPago);
    $inserta->bindParam(':topeServicioHraNormal',$topeServicioHraNormal);
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
    $inserta->bindParam(':kmInicioCliente',$kmInicioCliente);
    $inserta->bindParam(':kmFinCliente',$kmFinCliente);
    $inserta->bindParam(':kmFin',$kmFin);
    $inserta->bindParam(':hraInicioBase',$hraInicioBase);
    $inserta->bindParam(':hraFinCliente',$hraFinCliente);
    $inserta->bindParam(':hraFin',$hraFin);
    $inserta->bindParam(':ptoDestino',$ptoDestino);
    $inserta->bindParam(':observacion',$observacion);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->bindParam(':fchCreacion',$fchCreacion);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function guardarLog($db, $deviceId, $descripcion, $usuario, $fecha){
    $inserta = $db->prepare("INSERT INTO `movillogerrores` (`deviceId`, `descripcion`, `usuario`, `fecha`, `creacFch`) VALUES ( :deviceId, :descripcion, :usuario, :fecha, CURRENT_DATE());");
    $inserta->bindParam(':deviceId',$deviceId);
    $inserta->bindParam(':descripcion',$descripcion);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->bindParam(':fecha',$fecha);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function generaCorrDespacho($db, $fchDespacho = NULL){
    if ($fchDespacho == NULL) $fchDespacho = Date("Y-m-d");
    $buscaCorrelativo = $db->prepare("SELECT `correlativo`  FROM `despacho` WHERE `fchDespacho` = :fchDespacho order by `correlativo` desc limit 1");
    $buscaCorrelativo->bindParam(':fchDespacho',$fchDespacho);
    $buscaCorrelativo->execute();
    foreach ($buscaCorrelativo as $arrayCorrelativo){
      $correlativo = $arrayCorrelativo['correlativo'] + 1;
    };
    if(!isset($correlativo)) $correlativo = 1;
    return $correlativo;
  }


  function logCadena($db,$cadRecibida,$observacion, $usuario = 'desconocido'){
    $inserta = $db->prepare("INSERT INTO `movillogrecibido` ( `cadenaRecibida`, `observacion`,  `usuario`, `creacFch`) VALUES ( '$cadRecibida','$observacion','$usuario',now())");
    $inserta->execute();
  }

  function logCadenaEnviada($db,$cadRecibida,$cadEnviada,$observacion, $usuario = 'desconocido'){
    $inserta = $db->prepare("INSERT INTO `movillogenviado` ( `cadenaRecibida`,`cadenaEnviada`, `observacion`,  `usuario`, `creacFch`) VALUES ( '$cadRecibida','$cadEnviada','$observacion','$usuario',now())");
    $inserta->execute();
  }

  //Ya no se usa???
  function  buscarMovilDespachosEliminar($db){
    $elimina = $db->prepare("DELETE FROM `movildespacho` WHERE `estado` IN ('Eliminar','Validado') ORDER BY `fchDespacho` DESC ");
    $elimina->execute();
    return $elimina->rowCount();
  }

  //Ya no se usa???
  function verificaAccesoDespacho($db, $placa, $idCliente, $idProgramacion, $fchDespacho){

    //$fchDespacho = '2019-11-13';
    $where = ($idProgramacion == "") ? "": " AND despacho.idProgramacion = '$idProgramacion' ";

    $consulta = $db->prepare("SELECT `despacho`.correlativo, `despacho`.estadoDespacho, despacho.`usuario`, count(`despachopuntos`.fchDespacho) AS cantPuntos FROM `despacho` LEFT JOIN despachopuntos ON `despacho`.fchDespacho = `despachopuntos`.fchDespacho AND `despacho`.correlativo = `despachopuntos`.correlativo WHERE despacho.`fchDespacho` = :fchDespacho AND `idCliente` LIKE :idCliente AND `despacho`.placa = :placa $where GROUP BY despacho.`fchDespacho`, despacho.`correlativo`  ");

    $consulta->bindParam(':placa',$placa);
    $consulta->bindParam(':idCliente',$idCliente);
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function verificaIntervaloKmAbast($db, $fchDespacho, $correlativo, $km, $modo = 'movil'){
    $kmDiferencia = 100;
    $dataDespacho = buscarDataDespacho($db,$fchDespacho,$correlativo);
    foreach ($dataDespacho as $key => $item) {
      $kmInicio = $item['kmInicio'];
      $kmInicioCliente = $item['kmInicioCliente'];
      $kmFinCliente = $item['kmFinCliente'];
      $kmFin =  $item['kmFin'];
    }

    if ($modo == 'movil'){
      if ($kmFinCliente >0 AND $km - $kmFinCliente > $kmDiferencia ) $msj = "Error kilometraje abastecimiento muy alto";
      else if ($kmInicioCliente >0 AND $km - $kmInicioCliente > $kmDiferencia ) $msj = "Error kilometraje abastecimiento muy alto";
      else if ($kmInicio >0 AND $km - $kmInicio > $kmDiferencia ) $msj = "Error kilometraje abastecimiento muy alto";
      else if ($kmInicio - $km > $kmDiferencia ) $msj = "Error kilometraje abastecimiento muy bajo";
      else $msj = "Ok";
    } else if ($modo == 'web'){
      if ($kmFinCliente >0 AND $km - $kmFinCliente > $kmDiferencia ) $msj = "Error kilometraje abastecimiento muy alto";
      else if ($kmInicioCliente >0 AND $km - $kmInicioCliente > $kmDiferencia ) $msj = "Error kilometraje abastecimiento muy alto";
      else if ($kmInicio >0 AND $km - $kmInicio > $kmDiferencia ) $msj = "Error kilometraje abastecimiento muy alto";
      else if ($kmInicio - $km > $kmDiferencia ) $msj = "Error kilometraje abastecimiento muy bajo";
      else $msj = "Ok";
    }
    return $msj;
  }

  function guardarAbast($db, $fchDespacho, $correlativo, $correlAbast, $grifo, $hra, $km, $galones, $precio, $nroVale, $grifero, $conductor, $observacion, $idPlaca ){
    $usuario = isset($_SESSION['usuario'])?$_SESSION['usuario']:"NoReconoce";
    $precioGalon = $precio / $galones;
    $tiempo = $fchDespacho." ".$hra;
    $verifica = verificaIntervaloKmAbast($db,$fchDespacho,$correlativo,$km,'movil');
    $inserta = $db->prepare("INSERT INTO `movilcombustible` (`fchCreacion`, `correlAbast`, `hraCreacion`, `tiempo`, `idPlaca`, `correlativo`, `chofer`,  `kmActual`, `nroVale`, `precioGalon`,  `total`, `galones`, `grifo`, `grifero`, `observacion`,  `usuario`, `fchIngreso`) VALUES (:fchDespacho, :correlAbast, :hra, :tiempo, :idPlaca, :correlativo, :conductor, :km, :nroVale, :precioGalon, :total, :galones, :grifo, :grifero, :observacion,  :usuario, now())");
    $inserta->bindParam(':fchDespacho',$fchDespacho);
    $inserta->bindParam(':correlativo',$correlativo);
    $inserta->bindParam(':correlAbast',$correlAbast);
    $inserta->bindParam(':grifo',$grifo);
    $inserta->bindParam(':hra',$hra);
    $inserta->bindParam(':km',$km);
    $inserta->bindParam(':galones',$galones);
    $inserta->bindParam(':nroVale',$nroVale);
    $inserta->bindParam(':precioGalon',$precioGalon);
    $inserta->bindParam(':total',$precio);
    $inserta->bindParam(':grifero',$grifero);
    $inserta->bindParam(':observacion',$observacion);
    $inserta->bindParam(':idPlaca',$idPlaca);
    $inserta->bindParam(':tiempo',$tiempo);
    $inserta->bindParam(':conductor',$conductor);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function generarNuevoCorrelCombustible($db,$fchDespacho,$correlativo){
    $consulta = $db->prepare("SELECT correlAbast FROM `movilcombustible` WHERE `fchCreacion` = :fchDespacho AND `correlativo` = :correlativo ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $aux = $consulta->fetchAll();
    $nvoCorrel = 1;
    foreach ($aux as $item) {
      $ultNumero = $item['correlAbast'];
      $nvoCorrel = 1*$ultNumero + 1;
    }
    return $nvoCorrel;
  }

  function editarAbastecimiento($db, $fchDespacho, $correlativo, $correlAbast, $grifo, $hra, $km, $galones, $precio, $nroVale, $grifero, $observacion, $modo = 'movil' , $validado = 'No'){
    $usuario = isset($_SESSION['usuario'])?$_SESSION['usuario']:"NoReconoce";
    $precioGalon = $precio / $galones;
    $tiempo = $fchDespacho." ".$hra;
    $rpta = verificaIntervaloKmAbast($db, $fchDespacho, $correlativo, $km, $modo);
    if ($rpta == 'Ok'){
      $actualiza = $db->prepare("UPDATE `movilcombustible` SET  grifo = :grifo, hraCreacion = :hra, galones = :galones ,
        total = :total, precioGalon = :precioGalon, nroVale = :nroVale, grifero = :grifero, `kmActual` = :km, observacion = :observacion, `validado` = :validado , `usuarioUltimoCambio` = :usuario WHERE `fchCreacion` = :fchDespacho AND `correlAbast` = :correlAbast AND `correlativo` = :correlativo");
      $actualiza->bindParam(':fchDespacho',$fchDespacho);
      $actualiza->bindParam(':correlativo',$correlativo);
      $actualiza->bindParam(':correlAbast',$correlAbast);
      $actualiza->bindParam(':grifo',$grifo);
      $actualiza->bindParam(':hra',$hra);
      $actualiza->bindParam(':km',$km);
      $actualiza->bindParam(':galones',$galones);
      $actualiza->bindParam(':total',$precio);
      $actualiza->bindParam(':precioGalon',$precioGalon);
      $actualiza->bindParam(':nroVale',$nroVale);
      $actualiza->bindParam(':grifero',$grifero);
      $actualiza->bindParam(':observacion',$observacion);
      $actualiza->bindParam(':validado',$validado);
      $actualiza->bindParam(':usuario',$usuario);
      $actualiza->execute();
      return $actualiza->rowCount();
    } else {
      return 0;
    }
  }

  function eliminaAbastecimiento($db,$fchDespacho,$correlativo,$correlAbast){
    $actualiza = $db->prepare("DELETE FROM `movilcombustible` WHERE `fchCreacion` = :fchDespacho AND `correlAbast` = :correlAbast AND `correlativo` = :correlativo");
    $actualiza->bindParam(':fchDespacho',$fchDespacho);
    $actualiza->bindParam(':correlativo',$correlativo);
    $actualiza->bindParam(':correlAbast',$correlAbast);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  //////////////////////////////////////////
  //////rutinas nuevas y ya revisadas //////
  function buscarPuntosEntrega($db,$fchDespacho = NULL,$correlativo = NULL){
    $where = "";
    $where .= ($fchDespacho == NULL)?"":" AND fchDespacho = :fchDespacho ";
    $where .= ($correlativo == NULL)?"":" AND correlativo = :correlativo ";
    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `correlPunto`, `idPunto`, `idRuta`, `ordenPunto` , `tipoPunto`, `nombComprador`, `distrito`, `provincia`, `direccion`, `nroGuiaPorte`, `guiaCliente`, `estado`, `hraLlegada`, `hraSalida`, `idProgramacion`, `idDistrito`, `guiaMoy`, `subEstado`, `fchPunto`, `referencia`, `telfReferencia`, `idCarga`, `observCargaPunto`, `creacUsuario`, `editaFch`, `editaUsuario`, `observacion`, `adic_01`, `adic_02`, `adic_03`, `foto_1`, `foto_2`, `foto_3`, `foto_4`, `punto`, `placa`, `km`, `longitud`, `latitud`, `creacFch`  FROM `despachopuntos` WHERE 1 $where ");
    if($fchDespacho != NULL) $consulta->bindParam(':fchDespacho',$fchDespacho);
    if($correlativo != NULL) $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function buscarInfoTripulacion($db, $fchDespacho, $correlativo){
    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, desper.`idTrabajador`, `valorRol`, `valorAdicional`, `valorHraExtra`, `trabFchDespachoFin`, `trabHraInicio`, `trabHraFin`, `tipoRol`, `pagado`, `fchPago`, `anhio`, `mes`, `fchDesmarca`, desper.`usuario`, `fchActualizacion`, `observPersonal`, `ultProceso`, `ultProcesoFch`, `ultProcesoUsuario`, concat(nombres,' ',apPaterno, ' ', apMaterno) AS nombCompleto, `trab`.esMaster, `trab`.precioMaster, `trab`.categTrabajador  FROM `despachopersonal` AS desper, trabajador AS trab WHERE `desper`.idTrabajador = `trab`.idTrabajador AND `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function verificaEstadoDespacho($db,$idProgramacion){
    $consulta = $db->prepare("SELECT `estadoDespacho`  FROM `despacho`  WHERE `idProgramacion` = :idProgramacion ");
    $consulta->bindParam(':idProgramacion',$idProgramacion);
    $consulta->execute();
    $aux = $consulta->fetchAll();
    $estado = "";
    foreach ($aux as $item) {
      $estado = $item['estadoDespacho'];
    }
    return $estado;
  }


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


  function insertaDespacho($db, $fchDespacho, $correlativo, $idProgramacion ,$hraDespacho, $placa, $idCliente, $correlCuenta, $cuenta, $tipoCuenta, $idProducto = '000000' ,$nroAuxiliares = '',  $usaReten = '', $terceroConPersonalMoy = '', $usuarioAsignado = 'NoDefinido', $movilAsignado = '' , $observacion = '', $usuario = 'Movil'){
    $fchCreacion = Date("Y-m-d");
    $sinValor = "";

    //echo "fchdespacho $fchDespacho, correltivo $correlativo, idprogram $idProgramacion ,hra despacho $hraDespacho, placa $placa , idCli $idCliente , cuenta $cuenta, correlcuenta $correlCuenta , idProducto $idProducto  ,nroauxiliares $nroAuxiliares , usa reten $usaReten , terceroconpersmoy $terceroConPersonalMoy , tipoCuenta $tipoCuenta ,  usuario $usuario ";
    $inserta = $db->prepare("INSERT INTO `despacho` (`fchDespacho`, `correlativo`, `idProgramacion`, `hraInicio`, `hraInicioBase`, `placa`, fchDespachoFin, fchDespachoFinCli, `idCliente`, `cuenta`, `correlCuenta`, `idProducto`, `nroAuxiliares`, `usaReten`, `observacion`, `kmInicio`, `kmInicioCliente`, `ptoOrigen`, `tipoDestino`, `ptoDestino`, `pagado`, `pagoEnPlanilla`, `terceroConPersonalMoy`, `movilAsignado`, `usuarioAsignado`, `usuario`, `fchCreacion`, `modoCreacion`, `tipoServicioPago` ) VALUES (:fchDespacho, :correlativo, :idProgramacion, :hraDespacho, :hraDespacho, :placa,  :fchDespachoFin, :fchDespachoFinCli, :idCliente, :cuenta, :correlCuenta, :idProducto, :nroAuxiliares, :usaReten, :observacion, :kmInicio, :kmInicioCliente,  :ptoOrigen, :tipoDestino, :ptoDestino, 'No', 'No', :terceroConPersonalMoy, :movilAsignado, :usuarioAsignado, :usuario, :fchCreacion, 'ProgramTrackMoy', :tipoCuenta)");

    $inserta->bindParam(':fchDespacho',$fchDespacho);
    $inserta->bindParam(':correlativo',$correlativo);
    $inserta->bindParam(':idProgramacion',$idProgramacion);
    $inserta->bindParam(':hraDespacho',$hraDespacho);
    $inserta->bindParam(':placa',$placa);
    $inserta->bindParam(':fchDespachoFin',$fchDespacho);
    $inserta->bindParam(':fchDespachoFinCli',$fchDespacho);
    $inserta->bindParam(':idCliente',$idCliente);
    $inserta->bindParam(':cuenta',$cuenta);
    $inserta->bindParam(':observacion',$observacion);
    $inserta->bindParam(':correlCuenta',$correlCuenta);
    $inserta->bindParam(':idProducto',$idProducto);
    $inserta->bindParam(':nroAuxiliares',$nroAuxiliares);
    $inserta->bindParam(':usaReten',$usaReten);
    $inserta->bindParam(':kmInicio',$sinValor);
    $inserta->bindParam(':kmInicioCliente',$sinValor);
    $inserta->bindParam(':ptoOrigen',$sinValor);
    $inserta->bindParam(':tipoDestino',$sinValor);
    $inserta->bindParam(':ptoDestino',$sinValor);
    $inserta->bindParam(':terceroConPersonalMoy',$terceroConPersonalMoy);
    $inserta->bindParam(':movilAsignado',$movilAsignado);
    $inserta->bindParam(':usuarioAsignado',$usuarioAsignado);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->bindParam(':fchCreacion',$fchCreacion);
    $inserta->bindParam(':tipoCuenta',$tipoCuenta);
    $inserta->execute();
    if ($inserta->rowCount() == 1)
      return 1;
    else
      return "Error";
  };

  function insertaTripulacion($db,$fchDespacho,$correlativo,$idTrabajador,$tipoRol, $hraInicioBase, $hraFinBase, $usuario = 'Desconocido'){
    $inserta = $db->prepare("INSERT INTO despachopersonal (fchDespacho, correlativo, idTrabajador,  tipoRol, `trabFchDespachoFin`, `trabHraInicio`, `trabHraFin`, `usuario`, ultProcesoFch) VALUES ( :fchDespacho, :correlativo,  :idTrabajador, :tipoRol, :fchDespacho ,:hraInicioBase , :hraFinBase, :usuario, curdate() )");
    $inserta->bindParam(':fchDespacho',$fchDespacho);
    $inserta->bindParam(':correlativo',$correlativo);
    $inserta->bindParam(':idTrabajador',$idTrabajador);
    $inserta->bindParam(':tipoRol',$tipoRol);
    $inserta->bindParam(':hraInicioBase',$hraInicioBase);
    $inserta->bindParam(':hraFinBase',$hraFinBase);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function eliminaTripulacion($db,$fchDespacho,$correlativo,$idTrabajador = NULL,$tipoRol = NULL){
    $where = "";
    if ($idTrabajador != NULL) $where .= " AND `idTrabajador` = :idTrabajador ";
    if ($tipoRol != NULL) $where .= " AND `tipoRol` = :tipoRol ";
    $elimina = $db->prepare("DELETE FROM `despachopersonal` WHERE  `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo  $where ");
    $elimina->bindParam(':fchDespacho',$fchDespacho);
    $elimina->bindParam(':correlativo',$correlativo);
    if ($idTrabajador != NULL) $elimina->bindParam(':idTrabajador',$idTrabajador);
    if ($tipoRol != NULL) $elimina->bindParam(':tipoRol',$tipoRol);
    $elimina->execute();
    return $elimina->rowCount();
  }

  function buscarInfoClienteCuentas($db, $idCliente = NULL){
    $where = ($idCliente == NULL)?"":" AND cliente.idRuc = :idCliente ";
    $consulta = $db->prepare("SELECT clicuepro.`idCliente`, cliente.nombre, cliente.rznSocial, clicuepro.`correlativo`, clicuenew.nombreCuenta ,  `idProducto`, `nombProducto`, `tipoProducto`, `estadoProducto` FROM `clientecuentaproducto` AS clicuepro, clientecuentanew AS clicuenew, cliente WHERE clicuepro.idCliente = cliente.idRuc AND clicuepro.idCliente = clicuenew.idCliente AND clicuenew.correlativo = clicuepro.correlativo AND estadoCliente = 'Activo' AND estadoProducto = 'Activo' $where ORDER BY nombre, correlativo ");
    if ($idCliente != NULL) $consulta->bindParam(':idCliente',$idCliente);
    $consulta->execute();
    return $consulta->fetchAll();
  }
  
  function buscarInfoPuntosOrigen($db){
    $consulta = $db->prepare("SELECT `idSede`, `descripcion`, `idUbicacion` FROM `ubisede`");
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function buscarInfoAsignableDespacho($db, $usuario = NULL){
    $where = ($usuario == NULL) ? "": " AND  usu.idUsuario = :usuario ";
    //$consulta = $db->prepare("SELECT usu.`idUsuario`, `trab`.idTrabajador, `trab`.apPaterno, `trab`.apMaterno, `trab`.nombres, `trab`.tipoTrabajador   FROM `usuario` AS usu, trabajador AS trab WHERE  `usu`.dni = `trab`.idTrabajador AND `trab`.estadoTrabajador = 'Activo' AND usu.`asignDespacho` = 'Si' AND usu.`estado` = 'Activo' AND `usu`.fchVencimiento >= curdate() $where ");

    $consulta = $db->prepare("SELECT usu.`idUsuario`, `trab`.idTrabajador, `trab`.apPaterno, `trab`.apMaterno, `trab`.nombres, `trab`.tipoTrabajador , `telefono`.idNroTelefono FROM `usuario` AS usu, trabajador AS trab LEFT JOIN telefono ON substring(`telefono`.conductor,1,LENGTH(`trab`.idTrabajador)) LIKE `trab`.idTrabajador WHERE `usu`.dni = `trab`.idTrabajador AND `trab`.estadoTrabajador = 'Activo' AND usu.`asignDespacho` = 'Si' AND usu.`estado` = 'Activo' AND `usu`.fchVencimiento >= curdate() $where ");

    if ($usuario != NULL) $consulta->bindParam(':usuario',$usuario);
    $consulta->execute();
    return ($usuario == NULL) ? $consulta->fetchAll() : $consulta->fetch();
  }

  function idNvoProgramDespacho($db, $fechaDespacho, $horaInicio, $placa, $cliente, $cuenta, $producto){
    $usuario = "desde movil";
    $estadoProgram = "EnProceso";
    $observacion = "Creado desde el movil";
    $inserta = $db->prepare("INSERT INTO `programdespacho` ( `fchDespacho`, `idCliente`, `correlCuenta`, `cuenta`, `idProducto`, `nombProducto`, `estadoProgram`, `placa`, `observacion`, `creacUsuario`, `creacFch`) VALUES ( :fchDespacho, :idCliente, :correlCuenta, :cuenta, :idProducto, :nombProducto, :estadoProgram, :placa, :observacion, :usuario, curdate());");

    $inserta->bindParam(':fchDespacho',$fechaDespacho);
    $inserta->bindParam(':idCliente',$cliente);
    $inserta->bindParam(':placa',$placa);
    $inserta->bindParam(':correlCuenta',$cuenta);
    $inserta->bindParam(':cuenta',$cuenta);
    $inserta->bindParam(':idProducto',$producto);
    $inserta->bindParam(':nombProducto',$producto);
    $inserta->bindParam(':estadoProgram',$estadoProgram);
    $inserta->bindParam(':observacion',$observacion);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $db->lastInsertId();
  }

  function  insertaDespachoEnProgramDespacho($db, $id, $fchDespacho, $hraInicio, $placa, $idCliente, $correlCuenta, $idProducto){
    $usuario = "usuarioMovil";
    $inserta = $db->prepare("INSERT INTO `programdespacho` ( `id`, `fchDespacho`, `idCliente`, `correlCuenta`, `idProducto`, `hraInicioEsperada`, `placa`,  `observacion`, `estadoProgram`, `creacUsuario`, `creacFch`) VALUES (:id, :fchDespacho, :idCliente, :correlCuenta, :idProducto , :hraInicio,  :placa, 'creado desde movil', 'CreaMovil', :usuario, curdate())");

    $inserta->bindParam(':id',$id);
    $inserta->bindParam(':fchDespacho',$fchDespacho);
    $inserta->bindParam(':hraInicio',$hraInicio);
    $inserta->bindParam(':idCliente',$idCliente);
    $inserta->bindParam(':placa',$placa);
    $inserta->bindParam(':correlCuenta',$correlCuenta);
    $inserta->bindParam(':idProducto',$idProducto);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    if ($inserta->rowCount() == 1)
      return 1;
    else
      return "Error";
  }

  function  actualizaProgramDespacho($db,$idProgramacion, $cliente, $cuenta, $producto, $horaInicio, $movilAsignado, $placa, $estado, $observacion, $usuarioAsignado, $usuario){

    $actualiza = $db->prepare("UPDATE `programdespacho` SET `idCliente` = :cliente, `correlCuenta` = :cuenta, `idProducto` = :producto, `hraInicioEsperada` = :horaInicio, `movilAsignado` = :movilAsignado, `placa` = :placa, `observacion` = :observacion, usuarioAsignado = :usuarioAsignado, `editaUsuario` = :usuario, `editaFch` = curdate() WHERE `id` = :idProgramacion ");
    $actualiza->bindParam(':idProgramacion',$idProgramacion);
    $actualiza->bindParam(':cliente',$cliente);
    $actualiza->bindParam(':cuenta',$cuenta);
    $actualiza->bindParam(':producto',$producto);
    $actualiza->bindParam(':horaInicio',$horaInicio);
    $actualiza->bindParam(':movilAsignado',$movilAsignado);
    $actualiza->bindParam(':placa',$placa);
    $actualiza->bindParam(':observacion',$observacion);
    $actualiza->bindParam(':usuarioAsignado',$usuarioAsignado);
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function buscarProgramCoordinador($db,$coordinador,$fchIni,$fchFin){

    //$consulta = $db->prepare("SELECT des.`fchDespacho`, des.`correlativo`, `idProgramacion`, `guiaCliente`, `hraInicio`, `fchDespachoFinCli`, `fchDespachoFin`, `hraFin`, `placa`, `considerarPropio`, `m3`,  `idCliente`, `cuenta`, `correlCuenta`, `tipoServicioPago`, `nroAuxiliares`,  `usarMaster`, `tpoExtraHras`, `costoTotal`,`usaReten`, `concluido`, `hraInicioBase`, `kmInicio`, `kmInicioCliente`, `kmFinCliente`, `hraFinCliente`, `lugarFinCliente`, `kmFin`, `ptoOrigen`, `idOrigen`, `idSedeOrigen`, `tipoDestino`, `ptoDestino`, `observacion`,  `estadoDespacho`, `idProducto`,  `movilAsignado`, group_concat(concat(idTrabajador,':',tipoRol )) AS tripulacion FROM `despacho` AS des LEFT JOIN  despachopersonal AS desper ON `des`.fchDespacho = `desper`.fchDespacho AND `des`.correlativo = `desper`.correlativo   WHERE des.`fchDespacho` BETWEEN :fchIni AND :fchFin AND des.`usuario` LIKE :usuario AND `modoCreacion` LIKE 'Programacion' AND `estadoDespacho` = 'Programado' GROUP BY idProgramacion");

    $consulta = $db->prepare("SELECT des.`fchDespacho`, des.`correlativo`, des.`idProgramacion`, despun.`guiaCliente`, `hraInicio`, `fchDespachoFinCli`, `fchDespachoFin`, `hraFin`, des.`placa`, `considerarPropio`, `m3`,  `idCliente`, `cuenta`, `correlCuenta`, `tipoServicioPago`, `nroAuxiliares`,  `usarMaster`, `tpoExtraHras`, `costoTotal`,`usaReten`, `concluido`, `hraInicioBase`, `kmInicio`, `kmInicioCliente`, `kmFinCliente`, `hraFinCliente`, `lugarFinCliente`, `kmFin`, `ptoOrigen`, `idOrigen`, `idSedeOrigen`, `tipoDestino`, `ptoDestino`, des.`observacion`,  `estadoDespacho`, `idProducto`,  `movilAsignado`, `usuarioAsignado`, group_concat(concat(idTrabajador,':',tipoRol )) AS tripulacion, group_concat(concat(`despun`.idPunto,':',ordenPunto,':',tipoPunto,':',hraLlegada,':',estado,':',subEstado)) AS puntos FROM `despacho` AS des  LEFT JOIN  despachopersonal AS desper ON `des`.fchDespacho = `desper`.fchDespacho AND `des`.correlativo = `desper`.correlativo  LEFT JOIN  despachopuntos AS despun ON `des`.idProgramacion = `despun`.idProgramacion  WHERE des.`fchDespacho` BETWEEN :fchIni AND :fchFin AND des.`usuario` LIKE :usuario AND `modoCreacion` IN ('Programacion', 'ProgramTrackMoy') AND `estadoDespacho` = 'Programado' GROUP BY idProgramacion");
    $consulta->bindParam(':usuario',$coordinador);
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->bindParam(':fchFin',$fchFin);
    $consulta->execute();
    return $consulta->fetchAll();
  }


  function buscarDataProgramada($db, $fchDespacho, $usuAsignado){

    $consulta = $db->prepare("SELECT des.`fchDespacho`, des.`correlativo`, des.`idProgramacion`,  des.`guiaCliente`, `hraInicio`, `fchDespachoFinCli`, `fchDespachoFin`, `hraFin`, des.`placa`, `considerarPropio`, `m3`,  `idCliente`, `cuenta`, `correlCuenta`, `tipoServicioPago`, `nroAuxiliares`,  `usarMaster`, `tpoExtraHras`, `costoTotal`,`usaReten`, `concluido`, `hraInicioBase`, `kmInicio`, `kmInicioCliente`, `kmFinCliente`, `hraFinCliente`, `lugarFinCliente`, `kmFin`, `ptoOrigen`, `idOrigen`, `idSedeOrigen`, `tipoDestino`, `ptoDestino`, des.`observacion`,  `estadoDespacho`, `idProducto`,  `movilAsignado`, group_concat(concat(idTrabajador,':',tipoRol )) AS tripulacion, group_concat(concat(`despun`.idPunto,'|',ordenPunto,'|',tipoPunto,'|',hraLlegada,'|',estado,'|',subEstado)) AS puntos FROM `despacho` AS des  LEFT JOIN  despachopersonal AS desper ON `des`.fchDespacho = `desper`.fchDespacho AND `des`.correlativo = `desper`.correlativo  LEFT JOIN  despachopuntos AS despun ON `des`.idProgramacion = `despun`.idProgramacion  WHERE des.`fchDespacho` = :fchDespacho AND des.`usuarioAsignado` LIKE :usuario AND `modoCreacion` IN ('Programacion','ProgramTrackMoy')  AND estadoDespacho IN('EnRuta')  GROUP BY idProgramacion");
    $consulta->bindParam(':usuario',$usuAsignado);
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function cambiarEstadoDespacho($db,$idProgramacion,$fchDespacho, $correlativo, $estado){
    /* Comentado por ahora
    $actualiza = $db->prepare("UPDATE `despacho` SET `estadoDespacho` = :estado WHERE `idProgramacion` = :idProgramacion AND fchDespacho = :fchDespacho AND correlativo = :correlativo ");*/
    $actualiza = $db->prepare("UPDATE `despacho` SET `estadoDespacho` = :estado WHERE `idProgramacion` = :idProgramacion");
    $actualiza->bindParam(':idProgramacion',$idProgramacion);
    //Comentado por ahora
    //$actualiza->bindParam(':fchDespacho',$fchDespacho);
    //$actualiza->bindParam(':correlativo',$correlativo);
    $actualiza->bindParam(':estado',$estado);
    $actualiza->execute();

    if ($actualiza->rowCount() == 1 && $estado == "Terminado"){
      echo calcularFinDespacho($db, $idProgramacion);
    }
    return $actualiza->rowCount();
  }

  function verificarDatosDespachoParaCerrar($db,$idProgramacion){
    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `idProgramacion`, `concluido`, `modoCreacion`, `estadoDespacho` FROM `despacho` WHERE idProgramacion = :idProgramacion ");
    $consulta->bindParam(':idProgramacion',$idProgramacion);
    $consulta->execute();
    $auxData = $consulta->fetch();
    if ($auxData["concluido"] == "Si") $cerrar = "No";
    else $cerrar = "Si";
    return $cerrar;
  }

  function verificarDatosPuntosParaCerrar($db,$idProgramacion){
    $consulta = $db->prepare("SELECT count(*) AS cant FROM `despachopuntos` WHERE `idProgramacion` = :idProgramacion AND estado = 'Pendiente' ");
    $consulta->bindParam(':idProgramacion',$idProgramacion);
    $consulta->execute();
    $auxData = $consulta->fetch();
    if ($auxData["cant"] == 0) $cerrar = "Si";
    else $cerrar = "Puntos";
    return $cerrar; 
  }

  function verificarDatosKilometraje($db, $idProgramacion, $kmInicio, $kmInicioCliente, $kmFinCliente, $kmFin){
    /*
    SELECT `fchDespacho`, `correlativo`, `idProgramacion`, placa, `concluido`, `modoCreacion`, `estadoDespacho`, kmUltimaMedicion FROM `despacho` LEFT JOIN vehiculo ON vehiculo.idPlaca = despacho.placa WHERE idProgramacion = '0000053472' 
    */
    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `idProgramacion`, placa, `concluido`, `modoCreacion`, `estadoDespacho`, kmUltimaMedicion, rznSocial, despacho.`considerarPropio` FROM `despacho` LEFT JOIN vehiculo ON `vehiculo`.idPlaca = `despacho`.placa WHERE idProgramacion = :idProgramacion ");
    $consulta->bindParam(':idProgramacion',$idProgramacion);
    $consulta->execute();
    $auxData = $consulta->fetch();

    $kmUltiMedicion = $auxData["kmUltimaMedicion"];
    $considerarPropio = $auxData["considerarPropio"];
    $cerrar = "No";

    if(($kmInicioCliente - $kmInicio >= 0 && $kmInicioCliente - $kmInicio <= 150) &&
       ($kmFinCliente - $kmInicioCliente >= 0 && $kmFinCliente - $kmInicioCliente <= 150 ) &&
       ($kmFin - $kmFinCliente >= 0 && $kmFin - $kmFinCliente <= 150 )) $cerrar = "Si";
    else $cerrar = "No";

    if(abs($kmUltiMedicion - $kmInicio) > 150 ) $cerrar = "No";

    if($considerarPropio != 'Si') $cerrar = "Si";
    return $cerrar;
  }


  function llenarCamposCerrarDespacho($db, $idProgramacion, $hraInicioBase, $hraFinCliente, $fchDespachoFinCli, $fchDespachoFin, $hraFin, $kmInicio, $kmInicioCliente, $kmFinCliente, $lugarFinCliente, $kmFin, $ptoOrigen, $idSedeOrigen, $observacion, $usaReten = "No", $usuario = 'Desconocido'){
    //$usuario = $_SESSION["usuario"];
    $observacion = utf8_decode($observacion);
    //echo "OBSERV2 $observacion";
    $actualiza = $db->prepare("UPDATE `despacho` SET `fchDespachoFinCli` = :fchDespachoFinCli, `fchDespachoFin` = :fchDespachoFin, `hraInicioBase` = :hraInicioBase,`hraFin` = :hraFin, `kmInicio` = :kmInicio, `kmInicioCliente` = :kmInicioCliente, `kmFinCliente` = :kmFinCliente, `hraFinCliente` = :hraFinCliente, `lugarFinCliente` = :lugarFinCliente, `kmFin` = :kmFin, `ptoOrigen` = :ptoOrigen,`idSedeOrigen` = :idSedeOrigen, observacion = :observacion , concluido = 'No', usaReten = :usaReten, modoTerminado = 'Trackmoy', fchTerminado = curdate(), usuarioTerminado = :usuarioTerminado WHERE `idProgramacion` = :idProgramacion ");
    $actualiza->bindParam(':idProgramacion',$idProgramacion);
    $actualiza->bindParam(':fchDespachoFinCli',$fchDespachoFinCli);
    $actualiza->bindParam(':fchDespachoFin',$fchDespachoFin);
    $actualiza->bindParam(':hraInicioBase',$hraInicioBase);
    $actualiza->bindParam(':hraFin',$hraFin);
    $actualiza->bindParam(':kmInicio',$kmInicio);
    $actualiza->bindParam(':kmInicioCliente',$kmInicioCliente);
    $actualiza->bindParam(':kmFinCliente',$kmFinCliente);
    $actualiza->bindParam(':hraFinCliente',$hraFinCliente);
    $actualiza->bindParam(':lugarFinCliente',$lugarFinCliente);
    $actualiza->bindParam(':kmFin',$kmFin);
    $actualiza->bindParam(':ptoOrigen',$ptoOrigen);
    $actualiza->bindParam(':idSedeOrigen',$idSedeOrigen);
    $actualiza->bindParam(':observacion',$observacion);
    $actualiza->bindParam(':usaReten',$usaReten);
    $actualiza->bindParam(':usuarioTerminado',$usuario);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function llenarGuiasMoy($db, $idProgramacion, $guiasMoy, $usuario = 'Desconocido'){
    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo` FROM `despacho` WHERE `idProgramacion` = :idProgramacion LIMIT 1");
    $consulta->bindParam(':idProgramacion',$idProgramacion);
    $consulta->execute();
    $auxData = $consulta->fetch();
    $fchDespacho = $auxData["fchDespacho"];
    $correlativo = $auxData["correlativo"];
    $arrGuias = explode(",",$guiasMoy);

    $elimina = $db->prepare("DELETE `despachoguiaporte` WHERE  `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
    $elimina->bindParam(':fchDespacho',$fchDespacho);
    $elimina->bindParam(':correlativo',$correlativo);
    $elimina->execute();

    foreach ($arrGuias as $key => $guiaMoy) {
      $guiaMoy = trim($guiaMoy);
      $inserta = $db->prepare("INSERT INTO `despachoguiaporte` (`guiaPorte`, `fchDespacho`, `correlativo`, `usuario`, `fchCreacion`) VALUES (:guiaMoy, :fchDespacho, :correlativo, :usuario, curdate())");
      $inserta->bindParam(':guiaMoy',$guiaMoy);
      $inserta->bindParam(':fchDespacho',$fchDespacho);
      $inserta->bindParam(':correlativo',$correlativo);
      $inserta->bindParam(':usuario',$usuario);
      $inserta->execute();
    }
  }

  function completarCalculosDelDespacho($db, $idProgramacion){
    //$usuario = $_SESSION["usuario"];
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

    //Se completan datos del vehículo
    $actualiza = $db->prepare("UPDATE `despacho` AS `dest`, ( SELECT  `nroVehiculo`, `propietario`,  `considerarPropio`,  `capCombustible`, `idEficSolesKm`, `combustibleFrec`, `kmUltimaMedicion` FROM `vehiculo` WHERE `idPlaca` = :placa ) AS `src` SET  `dest`.`considerarpropio` = `src`.`considerarPropio`  WHERE `dest`.`idProgramacion` = :idProgramacion");
    $actualiza->bindParam(':placa',$placa);
    $actualiza->bindParam(':idProgramacion',$idProgramacion);
    $actualiza->execute();

    $actualiza = $db->prepare("UPDATE `despacho` AS `dest`, ( SELECT count(*) As tripulacion, sum(if(tipoRol = 'Auxiliar', 1, 0)) AS nroAuxiliares, sum(if(tipoRol = 'Conductor', 1, 0)) AS nroConductor FROM despachopersonal WHERE fchDespacho = :fchDespacho AND correlativo = :correlativo  ) AS `src` SET  `dest`.`nroAuxiliares` = `src`.`nroAuxiliares`, `dest`.`nroAuxiliaresAdic` =  if( `src`.`nroAuxiliares` -  `dest`.`nroAuxiliaresCuenta` >= 0,   `src`.`nroAuxiliares` -  `dest`.`nroAuxiliaresCuenta`, 0)   WHERE `dest`.`idProgramacion` = :idProgramacion");
    $actualiza->bindParam(':fchDespacho',$fchDespacho);
    $actualiza->bindParam(':correlativo',$correlativo);
    $actualiza->bindParam(':idProgramacion',$idProgramacion);
    $actualiza->execute();
  }

  function buscarDataDespachoIdProgram($db,$idProgramacion){
    $consulta = $db->prepare("SELECT  `fchDespacho`, `correlativo`, `idProgramacion`, `guiaCliente`, `hraInicio`, `fchDespachoFinCli`, `fchDespachoFin`, `hraFin`, `placa`, `considerarPropio`, `m3`, `valorServicio`, `igvServicio`, `idCliente`, `cuenta`, `correlCuenta`, `tipoServicioPago`, `nroAuxiliares`, `nroAuxiliaresAdic`, `costo`, `topeServicioHraNormal`, `hrasNormales`, `tolerHrasNormales`, `costoHraExtra`, `toleranCobroHraExtra`, `valorConductor`, `hraNormCondDespacho`, `tolerHraCondDespacho`, `valHraAdicCondDespacho`, `valorAuxiliar`, `hraNormAuxDespacho`, `tolerHraAuxDespacho`, `valHraAdicAuxDespacho`, `nroAuxiliaresCuenta`, `valorAuxAdicional`, `usarMaster`, `tpoExtraHras`, `costoTotal`, `nroGuias`, `nroDespachos`, `usaReten`, `concluido`, `hraInicioBase`, `kmInicio`, `kmInicioCliente`, `kmFinCliente`, `hraFinCliente`, `lugarFinCliente`, `kmFin`, `recorridoEsperado`, `zonaDespacho`, `ptoOrigen`, `idOrigen`, `idSedeOrigen`, `tipoDestino`, `ptoDestino`, `observacion`, `observCliente`, `docCobranza`, `tipoDoc`, `pagado`, `pagoEnPlanilla`, `terceroConPersonalMoy`, `contarDespacho`, `superCuenta`, des.`usuario`, des.`fchCreacion`, `modoCreacion`, `estadoDespacho`, `usuarioGrabaFin`, `fchGrabaFin`, `hraGrabaFin`, `usuarioCreaCobranza`, `fchCreaCobranza`, `idProducto`, `valKmAdic`, `hraIniEsperado`, `valAdicHraIniEsper`, `hraFinEsperado`, `valAdicHraFinEsper`, `cobrarPeaje`, `cobrarRecojoDevol`, `valUnidTercCCond`, `valUnidTercSCond`, `hrasNormalTerc`, `tolerHrasNormalTerc`, `valHraExtraTerc`, `valKmAdicTerc`, `tolerKmEsperado`, `movilAsignado`, `editaUsuarioDesp`, `cliente`.nombre, valAuxTerceroDesp FROM `despacho` AS des, cliente  WHERE `des`.idCliente = `cliente`.idRuc AND `des`.idProgramacion = :idProgramacion ");
    $consulta->bindParam(':idProgramacion',$idProgramacion);
    $consulta->execute();
    return $consulta->fetch();
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

  function calcularFinDespacho($db,$idProgramacion){
    $valorHraExtra = 0;
    $valorAdicional = 0;
    $rpta = completarCalculosDelDespacho($db, $idProgramacion);

    $dataDespacho = buscarDataDespachoIdProgram($db, $idProgramacion);

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
    $hrasNormalTerc  = $dataDespacho["hrasNormalTerc"];
    $tolerHrasNormalTerc  = $dataDespacho["tolerHrasNormalTerc"];
    $valHraExtraTerc = $dataDespacho["valHraExtraTerc"];
    $placa =  $dataDespacho["placa"];
    $usuario  =  $dataDespacho["usuario"];
    $observacion = $dataDespacho["observacion"];

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
    ////////////////////////////////////////
    //Regenerar los documentos de cobranza//
    ////////////////////////////////////////
    //Elimina documentos de cobranza si ya los había
    $elimina = $db->prepare("DELETE FROM `despachodetallesporcobrar` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
    $elimina->bindParam(':fchDespacho',$fchDespacho);
    $elimina->bindParam(':correlativo',$correlativo);
    $elimina->execute();
    //$observacion = "";
    insertaDetallesPorCobrar($db,$fchDespacho,$correlativo,'Despacho',$valorServicio,1, $usuario,$observacion);

    $tpoExtraHrasDecimalTrab = 0;
    if ($hrasNormales != '00:00:00'){
      //Cálculo de las horas extra
      //$hrasNormales = substr("00".$hrasNormales,-2).":00:00";
      $duracionhhmmss = restahhmmss($hraIniFch,$hraFinCalculo);
      $tpoExtraHrasDecimalTrab = difEnHorasDecim($hrasNormales,$duracionhhmmss);
      if($toleranCobroHraExtra == "" || $toleranCobroHraExtra == null) $toleranCobroHraExtra = '00:00:00';
      $arrHraTolerancia = explode(":", $toleranCobroHraExtra);
      $tiempoToleranciaEnSegundos = $arrHraTolerancia[0]*3600 + $arrHraTolerancia[1]*60 + $arrHraTolerancia[2];
      $tpoExtraHrasDecimalTrab = ($tpoExtraHrasDecimalTrab <= ($tiempoToleranciaEnSegundos/60))?0:$tpoExtraHrasDecimalTrab;

      if ($tpoExtraHrasDecimalTrab > 0){
        insertaDetallesPorCobrar($db,$fchDespacho,$correlativo,'HrasAdic',$costoHraExtra,$tpoExtraHrasDecimalTrab, $usuario, $observacion);

        $actualiza = $db->prepare("UPDATE `despacho` SET `tpoExtraHras` = :tpoExtraHrasDecimalTrab WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
        $actualiza->bindParam(':tpoExtraHrasDecimalTrab',$tpoExtraHrasDecimalTrab);
        $actualiza->bindParam(':fchDespacho',$fchDespacho);
        $actualiza->bindParam(':correlativo',$correlativo);
        $actualiza->execute();
      }
    }

    if ($nroAuxiliaresAdic > 0) insertaDetallesPorCobrar($db,$fchDespacho,$correlativo,'AuxAdic',$valorAuxAdicional,$nroAuxiliaresAdic, $usuario, $observacion);

    ////////////////////////////////////////////
    //Regenerar horas extra de la tripulación //
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


      $hraIniCalculoTrab = $fchTrabInicio." ".$hraTrabInicio;
      $hraFinCalculoTrab = $fchTrabFin." ".$hraTrabFin;
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


      $duracionTrabhhmmss = restahhmmss($hraIniCalculoTrab, $hraFinCalculoTrab);
      $tpoExtraHrasDecimalTrab = difEnHorasDecim($hrasEsperadas,$duracionTrabhhmmss);
      if($categTrabajador != "Tercero"){
/*        echo "
        duracionhhmmss => $duracionhhmmss<br>
        tpoExtraHrasDecimalTrab => $tpoExtraHrasDecimalTrab<br>
        topeaConsiderar         => $topeaConsiderar<br> ";
*/

        $arrAux = explode(":", $topeaConsiderar);
        $tiempoToleranciaEnSegundos = $arrAux[0]*3600 + $arrAux[1]*60 + $arrAux[2];
        $tpoExtraHrasDecimalTrab = ($tpoExtraHrasDecimalTrab <= ($tiempoToleranciaEnSegundos/60))?0:$tpoExtraHrasDecimalTrab;

  /*      echo "
        tiempoToleranciaEnSegundos => $tiempoToleranciaEnSegundos<br>
        tpoExtraHrasDecimalTrab => $tpoExtraHrasDecimalTrab<br>
        topeaConsiderar         => $topeaConsiderar<br>
        valHraAdicCondDespacho  => $valHraAdicCondDespacho<br>
        valHraAdicAuxDespacho   => $valHraAdicAuxDespacho ";       
*/
        if ($tpoExtraHrasDecimalTrab >0 AND $valorHraAdic > 0 AND $topeaConsiderar != '0:00' AND $tipoRol != 'Coordinador'){
          $monto = round($tpoExtraHrasDecimalTrab * $valorHraAdic,2);
          $descripHraExtra = "Servicio $nombreCliente $fchDespacho-$correlativo";
          //Todavía no sé si va aquí algo
          //insertaRegEnPrestamo($db, $idTrabajador,$descripHraExtra,$monto,1,$fchDespacho,'HraExtra',$usuario); 
        }
        if($tipoRol == 'Conductor'){
          $valorHraExtra = $tpoExtraHrasDecimalTrab * $valHraAdicCondDespacho;
        } else if($tipoRol == 'Auxiliar'){
          $valorHraExtra = $tpoExtraHrasDecimalTrab * $valHraAdicAuxDespacho;
        }
        //Precio master
        if($usarMaster == "Si"){
          if($esMaster == "Si" && $valorRol < $precioMaster  ) $valorRol = $precioMaster;
        }
      } else {
        $valorRol = $valorAdicional = $valorHraExtra = 0;
      }

      $rpta = completarCalculosTrabajador($db, $fchDespacho, $correlativo, $idTrabajador, $tipoRol, $valorRol, $valorAdicional, $valorHraExtra, $hraTrabFin, $fchTrabFin, $usuario);
    }

    //////////////////////////////////////////
    /// GENERANDO PAGO AL TERCERO SI LO ES ///
    //////////////////////////////////////////
    //elimina info sobre liquidacion a tercero si es que hay
    $esUnTercero = "No";
    $elimina = $db->prepare("DELETE FROM `despachovehiculotercero` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
    $elimina->bindParam(':fchDespacho',$fchDespacho);
    $elimina->bindParam(':correlativo',$correlativo);
    $elimina->execute();

    $elimina = $db->prepare("DELETE FROM `ocurrenciatercero` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
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
      $tpoExtraHrasDecimalTrabTerc = difEnHorasDecim($hrasNormalTerc,$duracionhhmmss);
      if($tolerHrasNormalTerc == "" || $tolerHrasNormalTerc == null) $tolerHrasNormalTerc = '00:00:00';
      $arrHraTolerancia = explode(":", $tolerHrasNormalTerc);
      $tiempoToleranciaEnSegundos = $arrHraTolerancia[0]*3600 + $arrHraTolerancia[1]*60 + $arrHraTolerancia[2];
      $tpoExtraHrasDecimalTrabTerc = ($tpoExtraHrasDecimalTrabTerc <= ($tiempoToleranciaEnSegundos/60))?0:$tpoExtraHrasDecimalTrabTerc;

      if ($tpoExtraHrasDecimalTrabTerc >0 AND $valHraExtraTerc > 0 ){
        $valHraExtraTercero = round($tpoExtraHrasDecimalTrabTerc * $valHraExtraTerc,2);
        $cantidad = insertaEnOcurrenciaTercero($db, $fchDespacho, $correlativo, 'HraExtra|A favor proveedor', $tpoExtraHrasDecimalTrabTerc, $placa, $valHraExtraTercero);
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
    //return "PRUEBA DE RECEPCION"; 
  }

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
    //$actualiza->bindParam(':idTrabajador',$idTrabajador);
  }

  function manejarCombustible($db, $cadena2){
    foreach ($cadena2 as $campo => $data) {
      $$campo = $data;
      //echo "Item $campo valor $data<br>"; 
    }

    $fchCreacion = Date("Y-m-d");
    $aux = explode(":",$hora);
    $hra = ($aux[0] == '' )?'00':$aux[0];
    $min = ($aux[1] == '' )?'00':$aux[1];
    $seg = ($aux[2] == '' )?'00':$aux[2];
    $hora = $hra.":".$min.":".$seg;
    $tpoTotal = $fchCreacion." ".$hora;
    $galones  =  round($precioTot/$precioGal,6) ;
    $correlativo = "";

    if ($accion == 'crear'){
      $inserta = $db->prepare("INSERT INTO `combustible` (`fchCreacion`, `hraCreacion`, `tiempo`, `idPlaca`, `correlativo`, `chofer`,  `kmActual`, `nroVale`, `precioGalon`,  `galones`,`total`, `marcaCombustible`, `grifo`, `grifero`, `observacion`, `fchUltimoCambio`, `usuario`, `fchIngreso`) VALUES (:fchCreacion, :hraCreacion, :tpoTotal, :idPlaca, :correlativo, :chofer,  :kmActual, :nroVale, :precioGalon, :galones, :total, :marca, :grifo, :grifero, :observacion, curdate(), :usuario, now())");
      $inserta->bindParam(':fchCreacion',$fchCreacion);
      $inserta->bindParam(':hraCreacion',$hora);
      $inserta->bindParam(':tpoTotal',$tpoTotal);
      $inserta->bindParam(':idPlaca',$placa);
      $inserta->bindParam(':correlativo',$correlativo);
      $inserta->bindParam(':chofer',$conductor);
      $inserta->bindParam(':kmActual',$km);
      $inserta->bindParam(':nroVale',$nroVale);
      $inserta->bindParam(':precioGalon',$precioGal);
      $inserta->bindParam(':galones',$galones);
      $inserta->bindParam(':total',$precioTot);
      $inserta->bindParam(':marca',$marcaComb);
      $inserta->bindParam(':grifo',$grifo);
      $inserta->bindParam(':grifero',$grifero);
      $inserta->bindParam(':observacion',$observacion);
      $inserta->bindParam(':usuario',$usuario);
      $inserta->execute();
      if($inserta->rowCount() == 1){
        $actualiza = $db->prepare("UPDATE `combustible` set `recorrido` = (:kmActual -  `kmActual`), usuarioUltimoCambio = :usuario, fchUltimoCambio = curdate()  WHERE `idPlaca` = :idPlaca AND tiempo <> :tpoTotal order by tiempo desc limit 1");
        $actualiza->bindParam(':kmActual',$km);
        $actualiza->bindParam(':idPlaca',$placa);
        $actualiza->bindParam(':tpoTotal',$tpoTotal);
        $actualiza->bindParam(':usuario',$usuario);
        $actualiza->execute();
        $actualiza = $db->prepare("UPDATE `vehiculo` SET `kmUltimaMedicion` = if(`kmUltimaMedicion` < :kmActual, :kmActual, `kmUltimaMedicion` ) , `usuarioUltimoCambio` = :usuario , `fchUltimoCambio` = curdate() WHERE `vehiculo`.`idPlaca` = :idPlaca");
        $actualiza->bindParam(':idPlaca',$placa);
        $actualiza->bindParam(':usuario',$usuario);
        $actualiza->bindParam(':kmActual',$km);
        $actualiza->execute();
        if($actualiza->rowCount() == 1){
          $msj = "Ok";
        } else {
          $msj = "Se insertó abastecimiento pero no se actualizaron los datos del vehículo";
        }
      } else {
        $msj = "No se insertó el registro de combustible";
      }
      return utf8_encode($hora."|".$placa."|".$msj);

    } else if ($accion == 'editar'){ 
      $actCombust = $db->prepare("UPDATE `combustible` SET `hraCreacion` = :hraCreacion, `tiempo` = :tpoTotal, `idPlaca` = :idPlaca, `correlativo` = :correlativo, `chofer` = :chofer, `kmActual` = :kmActual, `nroVale` = :nroVale, `precioGalon` = :precioGalon, `total` = :total, `marcaCombustible` = :marca, `galones` = :galones, `grifo` = :grifo, `grifero` = :grifero, `usuarioUltimoCambio` = :usuario, `fchUltimoCambio` = curdate(), `observacion` = :observacion WHERE `fchCreacion` = :fchCreacion AND `hraCreacion` = :hraCreacion AND `idPlaca` = :idPlaca");
      $actCombust->bindParam(':fchCreacion',$fchCreacion);
      $actCombust->bindParam(':hraCreacion',$hora);
      $actCombust->bindParam(':tpoTotal',$tpoTotal);
      $actCombust->bindParam(':idPlaca',$placa);
      $actCombust->bindParam(':correlativo',$correlativo);
      $actCombust->bindParam(':chofer',$conductor);
      $actCombust->bindParam(':kmActual',$km);
      $actCombust->bindParam(':nroVale',$nroVale);
      $actCombust->bindParam(':precioGalon',$precioGal);
      $actCombust->bindParam(':galones',$galones);
      $actCombust->bindParam(':total',$precioTot);
      $actCombust->bindParam(':marca',$marcaComb);
      $actCombust->bindParam(':grifo',$grifo);
      $actCombust->bindParam(':grifero',$grifero);
      $actCombust->bindParam(':observacion',$observacion);
      $actCombust->bindParam(':usuario',$usuario);
      $actCombust->execute();

      if($actCombust->rowCount() == 1){
        $actualiza = $db->prepare("UPDATE `combustible` set `recorrido` = (:kmActual -  `kmActual`), usuarioUltimoCambio = :usuario, fchUltimoCambio = curdate()  WHERE `idPlaca` = :idPlaca AND tiempo <> :tpoTotal order by tiempo desc limit 1");
        $actualiza->bindParam(':kmActual',$km);
        $actualiza->bindParam(':idPlaca',$placa);
        $actualiza->bindParam(':tpoTotal',$tpoTotal);
        $actualiza->bindParam(':usuario',$usuario);
        $actualiza->execute();

        $actualiza = $db->prepare("UPDATE `vehiculo` SET `kmUltimaMedicion` = if(`kmUltimaMedicion` < :kmActual, :kmActual, `kmUltimaMedicion` ) , `usuarioUltimoCambio` = :usuario , `fchUltimoCambio` = curdate() WHERE `vehiculo`.`idPlaca` = :idPlaca");
        $actualiza->bindParam(':idPlaca',$placa);
        $actualiza->bindParam(':usuario',$usuario);
        $actualiza->bindParam(':kmActual',$km);
        $actualiza->execute();

        if($actualiza->rowCount() == 1){
          $msj = "Ok";
        } else {
          $msj = "Se actualizó abastecimiento pero no se actualizan los datos del vehículo";
        }
      } else {
        $msj = "No se actualizó el registro de combustible";
      }
      return utf8_encode($hora."|".$placa."|".$msj);
    }
  }

  function buscarEstadosPuntos($db){
    $consulta = $db->prepare("SELECT `estado`, `subestado`, `creacFch`, `creacUsuario`, `editaFch`, `editaUsuario` FROM `despachopuntosestados`");
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function buscarInfoCuenta($db,$idCliente,$correlativo){
    $consulta = $db->prepare("SELECT `idCliente`, `correlativo`, `nombreCuenta`, `estadoCuenta`, `tipoCuenta`, `paraMovil`, `creacUsuario`, `creacFch`, `editUsuario`, `editFch` FROM `clientecuentanew` WHERE `idCliente` LIKE :idCliente AND `correlativo` LIKE :correlativo ");
    $consulta->bindParam(':idCliente',$idCliente);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    return $consulta->fetch(PDO::FETCH_ASSOC);
  }

  function completaCamposDespacho($db,$idProgramacion){ 


  }

?>
