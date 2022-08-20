<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  global $servidor, $bd, $usuario, $contrasenia;
  $db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);
  //$db->exec("SET NAMES 'utf8';");

  function logAccion($db,$descripcion,$idTrabajador,$placa){
    $user = $_SESSION['usuario'];
    $inserta = $db->prepare("INSERT INTO `logaccion` (`id`, `descripcion`, `idTrabajador`,`placa`,`usuario`, `fecha`) VALUES (NULL, :descripcion, :idTrabajador,:placa,:usuario, NOW())");
    $inserta->bindParam(':descripcion',$descripcion);
    $inserta->bindParam(':idTrabajador',$idTrabajador);
    $inserta->bindParam(':placa',$placa);
    $inserta->bindParam(':usuario',$user);
    $inserta->execute();
  };

  function buscarDatosVehiculo($db, $placa){
    $consulta = $db->prepare("SELECT `rznSocial`, `considerarPropio` FROM `vehiculo` WHERE `idPlaca` LIKE :placa ");
    $consulta->bindParam(':placa',$placa);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function buscarCodPlameEnPlanilla($db, $idTrabajador, $codPlame, $fchInicio, $fchUltDiaTrab){
    $consulta = $db->prepare("SELECT fchQuincena, idTrabajador, valor FROM `quincenadetallecontab` WHERE fchQuincena between :fchInicio AND :fchUltDiaTrab AND `codPlame` IN ($codPlame) AND idTrabajador = :idTrabajador ORDER BY `fchQuincena` ASC ");
    $consulta->bindParam(':idTrabajador',$idTrabajador);
    //$consulta->bindParam(':codPlame',$codPlame);
    $consulta->bindParam(':fchInicio',$fchInicio);
    $consulta->bindParam(':fchUltDiaTrab',$fchUltDiaTrab);
    $consulta->execute();
    return $consulta->fetchAll();

  }

  function eliminarAbast($db,$dataPost) {
    $idabastecimiento = $dataPost['id'];
    $fecha = $dataPost['fecha'];
    $placa = $dataPost['placa'];
    $conductor = $dataPost['conductor'];
    $foto1 = $dataPost['foto1'];
    $foto2 = $dataPost['foto2'];
    $response = 0;
    $dataUpdate = "";

    $elimina = $db->prepare("DELETE FROM `incidencias_abastecimiento` WHERE `idabastecimiento` = :idabastecimiento");
    $elimina->bindParam(':idabastecimiento',$idabastecimiento);
    $elimina->execute();

    if($elimina->rowCount() == 1){
      if ($foto1 != "") {
        unlink("../..".$foto1);
      }
      if ($foto2 != "") {
        unlink("../..".$foto2);
      }
      $response += $elimina->rowCount();
      $consulta = $db->prepare("(SELECT idabastecimiento AS data FROM `incidencias_abastecimiento` WHERE placa = :placa AND fecha >= :fecha ORDER BY `incidencias_abastecimiento`.`kilometraje_actual` ASC LIMIT 1) UNION (SELECT kilometraje_actual AS data FROM `incidencias_abastecimiento` WHERE placa = :placa AND fecha <= :fecha ORDER BY kilometraje_actual DESC LIMIT 1)");
      $consulta->bindParam(':fecha', $fecha);
      $consulta->bindParam(':placa', $placa);
      $consulta->execute();
      $data = $consulta->fetchAll();

      $vueltas = 0;
      foreach ($data as $key => $value) {
        $vueltas += 1;
        $dataUpdate .= $value['data'] . "_";
      }

      if ($dataUpdate != "" && $vueltas == 2) {
        $partsDataUpdate = explode("_", $dataUpdate);
        $id = $partsDataUpdate[0];
        $kmact = $partsDataUpdate[1];
        $actualiza = $db->prepare("UPDATE `incidencias_abastecimiento` SET `kilometraje_anterior` = :kmact WHERE `idabastecimiento` = :id");
        $actualiza->bindParam(':id',$id);
        $actualiza->bindParam(':kmact',$kmact);
        $actualiza->execute();
        $response += $actualiza->rowCount();
      }

      $descripcion = utf8_encode("Se eliminó registro de abastecimiento | Fecha: " . $fecha . " | Placa: " . $placa . " | Conductor: " . $conductor . ".");
      logAccion($db, $descripcion, "", "");
    }

    return $response;
  }


  function crearTaller($db,$dataPost){
    $usuario = $_SESSION["usuario"];
    $ruc = $dataPost['ruc'];
    $rzsocial = utf8_decode($dataPost['rzsocial']);
    $nombre = utf8_decode($dataPost['nombre']);
    $contacto = $dataPost['contacto'];
    $correo = $dataPost['correo'];
    $celular = $dataPost['celular'];
    $tipo = utf8_decode($dataPost['tipo']);
    $inserta = $db->prepare("INSERT INTO `talleres` (`ruc`, `rzn_social`, `nombre`, `contacto`, `correo`, `celular`, `tipo`, `usuario_registro`) VALUES (:ruc, :rzsocial, :nombre, :contacto, :correo, :celular, :tipo, :usuario);");
    $inserta->bindParam(':ruc',$ruc);
    $inserta->bindParam(':rzsocial',$rzsocial);
    $inserta->bindParam(':nombre',$nombre);
    $inserta->bindParam(':contacto',$contacto);
    $inserta->bindParam(':correo',$correo);
    $inserta->bindParam(':celular',$celular);
    $inserta->bindParam(':tipo',$tipo);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function crear_editarInfracion($db,$dataPost){
    $usuario = $_SESSION["usuario"];
    $id = $dataPost['id'];
    $falta = $dataPost['falta'];
    $calificacion = $dataPost['calificacion'];
    $monto = $dataPost['monto'];
    $descuento = $dataPost['descuento'];
    $infraccion = $dataPost['infraccion'];
    $sancion = $dataPost['sancion'];
    $puntos = $dataPost['puntos'];
    $m_preventiva = $dataPost['m_preventiva'];
    $solidario = $dataPost['solidario'];
    $modo = $dataPost['modo'];
    $result = 0;

    if ($modo == "insert") {
      $inserta = $db->prepare("INSERT INTO `infracciones` (`falta`, `infraccion`, `calificacion`, `monto`, `descuento`, `sancion`, `puntos`, `medida_preventiva`, `solidario`, `usuario_registro`) VALUES (:falta, :infraccion , :calificacion , :monto , :descuento , :sancion , :puntos , :m_preventiva , :solidario, :usuario);");
      $inserta->bindParam(':falta', $falta);
      $inserta->bindParam(':infraccion', $infraccion);
      $inserta->bindParam(':calificacion', $calificacion);
      $inserta->bindParam(':monto', $monto);
      $inserta->bindParam(':descuento', $descuento);
      $inserta->bindParam(':sancion', $sancion);
      $inserta->bindParam(':puntos', $puntos);
      $inserta->bindParam(':m_preventiva', $m_preventiva);
      $inserta->bindParam(':solidario', $solidario);
      $inserta->bindParam(':usuario', $usuario);
      $inserta->execute();
      $result = $inserta->rowCount();
    } else if ($modo == "update") {
      $actualiza = $db->prepare("UPDATE `infracciones` SET `falta` = :falta, `infraccion` = :infraccion, `calificacion` = :calificacion, `monto` = :monto, `descuento` = :descuento, `sancion` = :sancion, `puntos` = :puntos, `medida_preventiva` = :m_preventiva, `solidario` = :solidario, `usuario_edicion` = :usuario WHERE `idinfraccion` = :id");
      $actualiza->bindParam(':id', $id);
      $actualiza->bindParam(':falta', $falta);
      $actualiza->bindParam(':infraccion', $infraccion);
      $actualiza->bindParam(':calificacion', $calificacion);
      $actualiza->bindParam(':monto', $monto);
      $actualiza->bindParam(':descuento', $descuento);
      $actualiza->bindParam(':sancion', $sancion);
      $actualiza->bindParam(':puntos', $puntos);
      $actualiza->bindParam(':m_preventiva', $m_preventiva);
      $actualiza->bindParam(':solidario', $solidario);
      $actualiza->bindParam(':usuario', $usuario);
      $actualiza->execute();
      $result = $actualiza->rowCount();
      if ($result == 0) {
        $result = 2;
      }
    }
    return $result;
  }


  function crearMantenimiento($db,$dataPost) {
    $usuario = $_SESSION["usuario"];
    $orden_trabajo = $dataPost['orden_trabajo'];
    $fecha = $dataPost['fecha'];
    $placa = $dataPost['placa'];
    $kilometraje = $dataPost['kilometraje'];
    $servicios = $dataPost['servicios'];

    $inserta = $db->prepare("INSERT INTO `historial_mantenimiento` (`orden_trabajo`,`fecha`, `placa`, `kilometraje`, `usuario`) VALUES (:orden_trabajo, :fecha, :placa, :kilometraje, :usuario);");
    $inserta->bindParam(':orden_trabajo', $orden_trabajo);
    $inserta->bindParam(':fecha', $fecha);
    $inserta->bindParam(':placa', $placa);
    $inserta->bindParam(':kilometraje', $kilometraje);
    $inserta->bindParam(':usuario', $usuario);
    $inserta->execute();
    $result = $inserta->rowCount();

    if ($result == 1) {
      for($i = 0; $i < sizeof($servicios); $i++) {
        $id_servicio = $servicios[$i]['id_servicio'];
        $repuestos = $servicios[$i]['repuestos'];
        $id_taller = $servicios[$i]['id_taller'];
        $comentario = $servicios[$i]['comentario'];

        $inserta = $db->prepare("INSERT INTO `servicio_mantenimiento` (`orden_trabajo`,`id_servicio`, `repuestos`, `id_taller`, `comentario`) VALUES (:orden_trabajo, :id_servicio, :repuestos, :id_taller, :comentario);");
        $inserta->bindParam(':orden_trabajo', $orden_trabajo);
        $inserta->bindParam(':id_servicio', $id_servicio);
        $inserta->bindParam(':repuestos', $repuestos);
        $inserta->bindParam(':id_taller', $id_taller);
        $inserta->bindParam(':comentario', $comentario);
        $inserta->execute();
        $result = $inserta->rowCount();
      }
    }

    return $result;
  }

  function buscarServicio($db,$dato){
    $datos = array();
    $consulta = $db->prepare("SELECT CONCAT(id_servicio, ' - ', des_servicio) AS dato FROM `servicios` WHERE CONCAT(id_servicio, ' - ', des_servicio) like '%$dato%'");
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => utf8_encode($item['dato']));
    } 
    return $datos;
  }

  function crearServ($db,$dataPost){
    $usuario = $_SESSION["usuario"]; 
    //$id =  nvoIdEficiencia($db); // "120";
    $descripcion = utf8_decode($dataPost['descripcion']);
    $clase = utf8_decode($dataPost['clase']);
    $tipo = utf8_decode($dataPost['tipo']);
    $inserta = $db->prepare("INSERT INTO `servicios` (`des_servicio`, `clase_servicio`, `tipo_mantenimiento`, `usuario`) VALUES (:descripcion, :clase, :tipo, :usuario);");
    $inserta->bindParam(':descripcion',$descripcion);
    $inserta->bindParam(':clase',$clase);
    $inserta->bindParam(':tipo',$tipo);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function editarServ($db,$dataPost) {

    $id = $dataPost['id'];
    $descripcion = utf8_decode($dataPost['descripcion']);
    $clase = utf8_decode($dataPost['clase']);
    $tipo = utf8_decode($dataPost['tipo']);

    $actualiza = $db->prepare("UPDATE `servicios` SET `des_servicio`= :descripcion, `clase_servicio` = :clase, `tipo_mantenimiento` = :tipo WHERE `id_servicio` = :id");

      $actualiza->bindParam(':id',$id);
      $actualiza->bindParam(':descripcion',$descripcion);
      $actualiza->bindParam(':clase',$clase);
      $actualiza->bindParam(':tipo',$tipo);
      $actualiza->execute();
      return $actualiza->rowCount();
  }

  function eliminarServ($db, $dataPost){
    $id = $dataPost['id'];
    $elimina = $db->prepare(" DELETE FROM `servicios` WHERE `id_servicio` = :id ");
    $elimina->bindParam(':id',$id);
    $elimina->execute();
    return $elimina->rowCount();
  }

  function editarChecklist($db,$dataPost) {

    $fecha = $dataPost['fecha'];
    $placa = $dataPost['placa'];
    $kmact = $dataPost['kmact'];

    $actualiza = $db->prepare("UPDATE `checkListMantenimiento` SET `km_actual_checklist_mantenimiento`= :kmact WHERE `fecha_registro` = :fecha AND `placa` = :placa");

      $actualiza->bindParam(':fecha',$fecha);
      $actualiza->bindParam(':kmact',$kmact);
      $actualiza->bindParam(':placa',$placa);
      $actualiza->execute();
      return $actualiza->rowCount();
  }

  function crearRedPeaje($db, $dataPost)
  {
    $usuario = $_SESSION["usuario"];
    $redpeaje = utf8_decode($dataPost['redpeaje']);
    $inserta = $db->prepare("INSERT INTO `redpeaje` (`redpeaje`, `usuario_registro`) VALUES (:redpeaje, :usuario);");
    $inserta->bindParam(':redpeaje', $redpeaje);
    $inserta->bindParam(':usuario', $usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function editarRedPeaje($db, $dataPost)
  {
    $usuario = $_SESSION["usuario"];
    $id_redpeaje = $dataPost['id_redpeaje'];
    $redpeaje = $dataPost['redpeaje'];
    $actualiza = $db->prepare("UPDATE `redpeaje` SET `redpeaje`= :redpeaje, `usuario_edicion` = :usuario WHERE `id_redpeaje` = :id_redpeaje");
    $actualiza->bindParam(':id_redpeaje', $id_redpeaje);
    $actualiza->bindParam(':redpeaje', $redpeaje);
    $actualiza->bindParam(':usuario', $usuario);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function crearGrifo($db,$dataPost){
    $usuario = $_SESSION["usuario"];
    $ruc = utf8_decode($dataPost['ruc']);
    $nombre = utf8_decode($dataPost['nombre']);
    $rsocial = utf8_decode($dataPost['rsocial']);
    $contacto = utf8_decode($dataPost['contacto']);
    $correo = utf8_decode($dataPost['correo']);
    $celular = utf8_decode($dataPost['celular']);
    $inserta = $db->prepare("INSERT INTO `grifos` (`ruc`, `nombre`, `rznSocial`, `contacto`, `correo`, `telefono`, `usuario`) VALUES (:ruc, :nombre, :rsocial, :contacto, :correo, :celular, :usuario);");
    $inserta->bindParam(':ruc',$ruc);
    $inserta->bindParam(':nombre',$nombre);
    $inserta->bindParam(':rsocial',$rsocial);
    $inserta->bindParam(':contacto',$contacto);
    $inserta->bindParam(':correo',$correo);
    $inserta->bindParam(':celular',$celular);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function editarGrifo($db,$dataPost) {
    $usuario = $_SESSION["usuario"];
    $ruc = $dataPost['ruc'];
    $contacto = $dataPost['contacto'];
    $correo = $dataPost['correo'];
    $celular = $dataPost['celular'];
    $actualiza = $db->prepare("UPDATE `grifos` SET `contacto`= :contacto, `correo` = :correo, `telefono` = :celular, `usuario` = :usuario WHERE `ruc` = :ruc");
    $actualiza->bindParam(':ruc',$ruc);
    $actualiza->bindParam(':contacto',$contacto);
    $actualiza->bindParam(':correo',$correo);
    $actualiza->bindParam(':celular',$celular);
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function crearUsuOcu($db,$dataPost){
    $usuario = utf8_decode($dataPost['usuario']);
    $pass = utf8_decode($dataPost['password']);
    $email = utf8_decode($dataPost['email']);
    $dni = utf8_decode($dataPost['dni']);
    $nombres = utf8_decode($dataPost['nombres']);
    $tipo = utf8_decode($dataPost['tipo']);
    $inserta = $db->prepare("INSERT INTO `usuario_incidencia` (`usuario`, `password`, `email`, `idTrabajador`, `nombres`, `tipoTrabajador`, `estado`) VALUES (:usuario, :pass, :email, :dni, :nombres, :tipo, 'Activo');");
    //$inserta->bindParam(':id',$id);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->bindParam(':pass',$pass);
    $inserta->bindParam(':email',$email);
    $inserta->bindParam(':dni',$dni);
    $inserta->bindParam(':nombres',$nombres);
    $inserta->bindParam(':tipo',$tipo);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function editarUsuOcu($db,$dataPost) {
    $id = $dataPost['id'];
    $pass = utf8_decode($dataPost['password']);
    $estado = utf8_decode($dataPost['estado']);

    $actualiza = $db->prepare("UPDATE `usuario_incidencia` SET `password`= :pass, `estado` = :estado WHERE `id` = :id");

    $actualiza->bindParam(':id',$id);
    $actualiza->bindParam(':pass',$pass);
    $actualiza->bindParam(':estado',$estado);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function buscarTrabajador($db,$dato,$estado = 'Todos'){
    if ($estado == "Todos") $where = ""; else $where = " AND estadoTrabajador = '$estado' ";
    $datos = array();
    $consulta = $db->prepare("SELECT concat(`idTrabajador`,'-',`apPaterno`,' ',`apMaterno`,', ',`nombres`) as dato FROM `trabajador` WHERE `apPaterno` LIKE '$dato%' OR `idTrabajador` LIKE '$dato%' OR `nombres` LIKE '$dato%' $where ");
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => utf8_encode($item['dato']));
    }
    return $datos;
  }

  function generaAjaxCorrOcurrencia($db, $fchDespacho, $correlativo ){
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


  function insertaAjaxEnOcurrenciaTercero($db, $fchDespacho, $correlativo, $auxTipoOcurrencia, $observHraExtraTercero, $placa, $mntTotal){
    $descripcion = "$auxTipoOcurrencia: $observHraExtraTercero $placa";
    $tipoOcurrencia = strtok($auxTipoOcurrencia, "|");
    $tipoConcepto =  strtok("|");

    $corrOcurrencia = generaAjaxCorrOcurrencia($db, $fchDespacho, $correlativo );

    $datosVehiculo = buscarDatosVehiculo($db, $placa);
    foreach ($datosVehiculo as $key => $value) {
      $idTercero = $value['rznSocial'];
    }
    $usuario = $_SESSION["usuario"];
    //echo " $fchDespacho,  $correlativo, $tipoOcurrencia, $corrOcurrencia, $tipoConcepto, $descripcion, $mntTotal  ,$idTercero, $usuario ";
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


  function nuevoCorrelativoDespachoAjax($db,$fchDespacho){
    $correlativo = 1;
    $buscaCorrelativo = $db->prepare("SELECT `correlativo` FROM `despacho` WHERE `fchDespacho` = :fchDespacho order by `correlativo` desc limit 1");
    $buscaCorrelativo->bindParam(':fchDespacho',$fchDespacho);
    $buscaCorrelativo->execute();
    foreach ($buscaCorrelativo as $arrayCorrelativo){
      $correlativo = $arrayCorrelativo['correlativo'] + 1;
    };
    return $correlativo;
  }

  function guardaRegistroDespachoPersonalAjax($db,$fchDespacho,$correlativo,$idTrabajador,$valorRol,$tipoRol,$trabHraInicio, $trabHraFin){
    $usuario = $_SESSION["usuario"];
    $modoCreacDp = "Preprogramacion";
    $inserta = $db->prepare("INSERT INTO despachopersonal (fchDespacho, correlativo, idTrabajador, valorRol, tipoRol,`trabHraInicio`, `trabHraFin`, trabFchDespachoFin, creacFchDp, creacUsuDp, modoCreacDp) VALUES ( :fchDespacho, :correlativo,  :idTrabajador, :valorRol, :tipoRol, :trabHraInicio, :trabHraFin, :fchDespacho, now(), :usuario, :modoCreacDp )");
    $inserta->bindParam(':fchDespacho',$fchDespacho);
    $inserta->bindParam(':correlativo',$correlativo);
    $inserta->bindParam(':idTrabajador',$idTrabajador);
    $inserta->bindParam(':valorRol',$valorRol);
    $inserta->bindParam(':tipoRol',$tipoRol);
    $inserta->bindParam(':trabHraInicio',$trabHraInicio);
    $inserta->bindParam(':trabHraFin',$trabHraFin);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->bindParam(':modoCreacDp',$modoCreacDp);
    $inserta->execute();
    return $inserta->rowCount();
  }


  function guardaRegistroDespachoAjax($db, $fchDespacho,$correlativo,$hraDespacho,$placa,$guia,$valorServicio,$igvServicio,$idCliente,$cuenta, $correlCuenta,$topeServicioHraNormal,$tipoServicioPago,$valorConductor,$valorAuxiliar, $costoHraExtra,$tolerHrasNormales,$nroAuxiliaresCuenta,$valorAuxAdicional,$usarMaster,$recorridoEsperado,$usaReten,$modoCreacion,$kmInicio, $idProducto, $fchCreacion, $movil,$usuAsignado = '', $usuario = '', $id = ''){

    if ($placa == ""){
      $placa = NULL;
      $considerarPropio = NULL;
    } else {
      $datosVehiculo = buscarDatosVehiculo($db, $placa);
      foreach ($datosVehiculo as $key => $value) {
        $considerarPropio = $value['considerarPropio'];
      }
    }

   //echo "fchDespacho: $fchDespacho, correlativo: $correlativo, hraDespacho: $hraDespacho, hraInicioBase: $hraDespacho, placa: $placa, guiaCliente: $guia, valorServicio: $valorServicio, igvServicio: $igvServicio, idCliente: $idCliente, cuenta: $cuenta, correlCuenta: $correlCuenta, topeServicioHraNormal: $topeServicioHraNormal, tipoServicioPago: $tipoServicioPago, valorConductor: $valorConductor, valorAuxiliar: $valorAuxiliar, costoHraExtra: $costoHraExtra, tolerHrasNormales: $tolerHrasNormales, nroAuxiliaresCuenta: $nroAuxiliaresCuenta, valorAuxAdicional: $valorAuxAdicional, usarMaster: $usarMaster, recorridoEsperado: $recorridoEsperado, usaReten: $usaReten, modoCreacion: $modoCreacion, kmInicio: $kmInicio, movilAsignado: $movil, usuAsignado: $usuAsignado, id: $id, idProducto: $idProducto, fchCreacion: $fchCreacion, usuario: $usuario";
    $inserta = $db->prepare("INSERT INTO despacho (fchDespacho, correlativo, hraInicio, hraInicioBase, placa, considerarPropio, guiaCliente ,valorServicio, igvServicio, idCliente, cuenta, correlCuenta, tipoServicioPago, topeServicioHraNormal, valorConductor, valorAuxiliar, costoHraExtra, tolerHrasNormales, nroAuxiliaresCuenta, valorAuxAdicional, usarMaster, recorridoEsperado, usaReten, modoCreacion, idProducto, kmInicio, movilAsignado, usuarioAsignado, idProgramacion, usuario, fchCreacion) VALUES ( :fchDespacho, :correlativo, :hraDespacho, :hraInicioBase, :placa, :considerarPropio, :guiaCliente, :valorServicio, :igvServicio, :idCliente, :cuenta, :correlCuenta, :tipoServicioPago, :topeServicioHraNormal, :valorConductor, :valorAuxiliar, :costoHraExtra, :tolerHrasNormales, :nroAuxiliaresCuenta, :valorAuxAdicional, :usarMaster, :recorridoEsperado, :usaReten, :modoCreacion, :idProducto, :kmInicio, :movilAsignado, :usuAsignado, :id, :usuario,:fchCreacion)");
    $inserta->bindParam(':fchDespacho',$fchDespacho);
    $inserta->bindParam(':correlativo',$correlativo);
    $inserta->bindParam(':hraDespacho',$hraDespacho);
    $inserta->bindParam(':hraInicioBase',$hraDespacho);
    $inserta->bindParam(':placa',$placa);
    $inserta->bindParam(':considerarPropio',$considerarPropio);
    $inserta->bindParam(':guiaCliente',$guia);
    $inserta->bindParam(':valorServicio',$valorServicio);
    $inserta->bindParam(':igvServicio',$igvServicio);
    $inserta->bindParam(':idCliente',$idCliente);
    $inserta->bindParam(':cuenta',$cuenta);
    $inserta->bindParam(':correlCuenta',$correlCuenta);
    $inserta->bindParam(':topeServicioHraNormal',$topeServicioHraNormal);
    $inserta->bindParam(':tipoServicioPago',$tipoServicioPago);

    $inserta->bindParam(':valorConductor',$valorConductor);
    $inserta->bindParam(':valorAuxiliar',$valorAuxiliar);
    $inserta->bindParam(':costoHraExtra',$costoHraExtra);
    $inserta->bindParam(':tolerHrasNormales',$tolerHrasNormales);
    $inserta->bindParam(':nroAuxiliaresCuenta',$nroAuxiliaresCuenta);
    $inserta->bindParam(':valorAuxAdicional',$valorAuxAdicional);
    $inserta->bindParam(':usarMaster',$usarMaster);
    $inserta->bindParam(':recorridoEsperado',$recorridoEsperado);
    $inserta->bindParam(':usaReten',$usaReten);
    $inserta->bindParam(':modoCreacion',$modoCreacion);
    $inserta->bindParam(':kmInicio',$kmInicio);
    $inserta->bindParam(':movilAsignado',$movil);
    $inserta->bindParam(':usuAsignado',$usuAsignado);

    $inserta->bindParam(':id',$id);
    $inserta->bindParam(':idProducto',$idProducto);
    $inserta->bindParam(':fchCreacion',$fchCreacion);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();

    return $inserta->rowCount();
  }

  function buscarDataProductoAjax($db,$idProducto){
    $consulta = $db->prepare("SELECT `idProducto`, `idCliente`, `correlativo`, `nombProducto`, `m3Facturable`, `puntos`, `idZona`, `zona`, `tipoProducto`, `estadoProducto`, `precioServ`, `kmEsperado`, `tolerKmEsperado`, `valKmAdic`, `hrasNormales`, `tolerHrasNormales`, `valHraAdic`, `hraIniEsperado`, `tolerHraIniEsperado`, `valAdicHraIniEsper`, `hraFinEsperado`, `tolerHraFinEsperado`, `valAdicHraFinEsper`, `nroAuxiliares`, `valAuxiliarAdic`, `cobrarPeaje`, `cobrarRecojoDevol`, `valConductor`, `hraNormalConductor`, `tolerHraCond`, `valHraAdicCond`, `valAuxiliar`, `hraNormalAux`, `tolerHraAux`, `valHraAdicAux`, `usoMaster`, `valUnidTercCCond`, `valUnidTercSCond`, `hrasNormalTerc`, `tolerHrasNormalTerc`, `valHraExtraTerc`, `valKmAdicTerc`, `valAuxTercero`, `creacUsuario`, `creacFch` FROM `clientecuentaproducto` WHERE idProducto = :idProducto ");

    $consulta->bindParam(':idProducto',$idProducto);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function verificarTripulacion($db,$fchDespacho,$hraInicioEsperada,$idTrabajador, $id){
    $consulta = $db->prepare("SELECT count(*) AS cant FROM `programdespacho` AS prodes , programdesppers AS proper WHERE `prodes`.id = `proper`.idProgram AND `prodes`.fchDespacho = :fchDespacho AND `prodes`.hraInicioEsperada = :hraInicioEsperada AND `proper`.idTrabajador = :idTrabajador AND prodes.id != :id ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':hraInicioEsperada',$hraInicioEsperada);
    $consulta->bindParam(':idTrabajador',$idTrabajador);
    $consulta->bindParam(':id',$id);
    $consulta->execute();
    $aux = $consulta->fetchAll();
    return ($aux[0]['cant'] == 0)?"Si":"No";
  }

  function verificarUsuarioAsignado($db, $usuAsignado, $idConductor, $auxAuxiliares, $fchDespacho, $hraInicioEsperada){

    $consulta = $db->prepare("SELECT usu.`idUsuario`, `trab`.idTrabajador, `trab`.tipoTrabajador FROM `usuario` AS usu, trabajador AS trab WHERE `usu`.dni = `trab`.idTrabajador AND `trab`.estadoTrabajador = 'Activo' AND usu.`asignDespacho` = 'Si' AND usu.`estado` = 'Activo' AND `usu`.fchVencimiento >= curdate() AND `tipoTrabajador` IN ('Conductor','Auxiliar','Coordinador') AND `usu`.idUsuario = :idUsuario ");
    $consulta->bindParam(':idUsuario',$usuAsignado);
    /*$consulta->bindParam(':hraInicioEsperada',$hraInicioEsperada);
    $consulta->bindParam(':idTrabajador',$idTrabajador);
    $consulta->bindParam(':id',$id);*/
    $consulta->execute();
    $aux = $consulta->fetch();

    $laRpta = "Error no identificado";

    if($aux["idTrabajador"] == ""){
      $laRpta = "Error. El usuario asignado no existe";
    } else {
      if($aux["idTrabajador"] == $idConductor)
        $laRpta = "Si";
      else {
        //echo "ArrAuxiliares ".$auxAuxiliares;

        $arrAuxiliares = array();
        $auxAuxiliares = preg_replace( '/\n/', '@', $auxAuxiliares );
        $dataAuxiliar = strtok($auxAuxiliares, "@");
        $continuar = "Si";
        while ( $dataAuxiliar != "" && $continuar == "Si") {
          $dni = substr($dataAuxiliar,0, strpos($dataAuxiliar, "-"));
          if ($aux["idTrabajador"] == $dni){
            $laRpta = "Si";
            $continuar = "No";
          }
          $dataAuxiliar = strtok("@");
        }

        if ($continuar == "Si"){
          $laRpta = "Error. El usuario asignado no es parte de la tripulación";
        }
      }
    }


    //echo "la rpta $laRpta, Id trabajador antes".$aux["idTrabajador"];

    if ($laRpta == "Si"){
      //echo "Id trabajadore".$aux["idTrabajador"];

      $consulta = $db->prepare("SELECT `idTrabajador` FROM despachopersonal WHERE fchDespacho = :fchDespacho AND trabHraInicio = :hraInicioEsperada AND `idTrabajador` = :idTrabajador ");
      $consulta->bindParam(':idTrabajador',$aux["idTrabajador"]);
      $consulta->bindParam(':fchDespacho',$fchDespacho);
      $consulta->bindParam(':hraInicioEsperada',$hraInicioEsperada);
      $consulta->execute();
      $aux2 = $consulta->fetch();
      if($aux2){
        if($aux2["idTrabajador"] == $aux["idTrabajador"]){
          $laRpta = "Error. EL usuario asignado ya está relacionado a otro despacho en este día y hora.";
        }        
      }
    }

    //echo "La Respuesta ".$laRpta;

    return utf8_decode($laRpta);

  }

  function actualizarEstadoProgram($db,$id,$estado){
    $actualiza = $db->prepare("UPDATE `programdespacho` SET `estadoProgram` = :estado WHERE `id` = :id; ");
    $actualiza->bindParam(':id',$id);
    $actualiza->bindParam(':estado',$estado);
    $actualiza->execute();
  }

  function buscarTodosInvolucrados($db,$fchDespacho,$correlativo){
    $consulta = $db->prepare("SELECT `despachopersonal`.idTrabajador, `despachopersonal`.tipoRol, concat(`trabajador`.nombres, ' ' , `trabajador`.apPaterno,' ', `trabajador`.apMaterno) as nombreCompleto, `trabajador`.categTrabajador ,  `despachopersonal`.valorRol, `despachopersonal`.valorAdicional, `despachopersonal`.valorHraExtra, `despachopersonal`.fchDespacho, `despachopersonal`.trabHraInicio, `despachopersonal`.trabFchDespachoFin, `despachopersonal`.trabHraFin    FROM `despachopersonal`, `trabajador`  WHERE `despachopersonal`.idTrabajador = `trabajador`.idTrabajador  AND  fchDespacho = :fchDespacho AND correlativo = :correlativo ");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function algunosDatosTrabajador($db, $idTrabajador){
    $consulta = $db->prepare("SELECT `idTrabajador`, `estadoTrabajador`, `categTrabajador`, `esMaster`, `precioMaster` FROM `trabajador` WHERE `idTrabajador` = '$idTrabajador' ");
    $consulta->execute();
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
  }


  function algunosDatosClienteCuentaProducto($db,$idProducto){
    $consulta = $db->prepare("SELECT * FROM `clientecuentaproducto` WHERE `idProducto` LIKE :idProducto ");
    $consulta->bindParam(':idProducto',$idProducto);
    $consulta->execute();
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
  }

  function insertaDetallesPorCobrar($db,$fchDespacho,$correlativo,$codigo,$ctoUnitario,$cantidad, $observDetalle = null, $observServicio = null ,$arrData = NULL){

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

      $observServicio = preg_replace( '/\n/', '@', $observServicio );
      $dataAuxiliar = strtok($observServicio, "@");
      $observFiltradoServicio = "";
      while ( $dataAuxiliar != "") {
        if ($codigo == 'Despacho' && substr($dataAuxiliar, 0, 12) == "Precio Base:")
          $observFiltradoServicio .= $usuario." ".$dataAuxiliar.". ";
        elseif ($codigo == 'AuxAdic' && substr($dataAuxiliar, 0, 12) == "Auxiliar Adi")
          $observFiltradoServicio .= $usuario." ".$dataAuxiliar.". ";
        elseif ($codigo == 'HrasAdic' && substr($dataAuxiliar, 0, 12) == "Hora Adicion")
          $observFiltradoServicio .= $usuario." ".$dataAuxiliar.". ";
        elseif ($codigo == 'KmsAdic' && substr($dataAuxiliar, 0, 12) == "Km Adicional")
          $observFiltradoServicio .= $usuario." ".$dataAuxiliar.". ";
        $dataAuxiliar = strtok("@");
      }

    
    if ($observFiltradoServicio != "")
      $observDetalle = $observFiltradoServicio.". ".$observDetalle;

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


  function generaCorrelPunto($db,$fchDespacho, $correlativo){
  	$consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `correlPunto`, `tipoPunto`, `nombComprador`, `distrito`, `direccion`, `nroGuiaPorte`, `estado`, `hraLlegada`, `hraSalida`, `observacion`, `foto_1`, `foto_2`, `foto_3`, `foto_4`, `punto`, `placa`, `km`, `creacFch` FROM `despachopuntos` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ORDER BY correlPunto DESC LIMIT 1 ");
  	$consulta->bindParam(':fchDespacho',$fchDespacho);
  	$consulta->bindParam(':correlativo',$correlativo);
  	$consulta->execute();
  	$data = $consulta->fetchAll();
    $correlPunto = 1;
    foreach ($data as $key => $value) {
      $correlPunto = $value['correlPunto'] + 1;
    }
    return $correlPunto;
  }


  function insertaPunto($db,$dataPost){
  	$fchDespacho = $dataPost['fchDespacho']; 
  	$correlativo = $dataPost['correlativo'];

    if ($dataPost['correlPunto'] == '' ) $correlPunto = generaCorrelPunto($db,$fchDespacho, $correlativo);
    else $correlPunto = $dataPost['correlPunto'];
  	$nombre = utf8_decode($dataPost['nombre']);
  	$tipoPunto = $dataPost['tipoPunto']; 
    $auxDistrito = utf8_decode($dataPost['distrito']);
    $idDistrito = strtok($auxDistrito, "-");
    $distrito = strtok("-");
    $guiaPorte = $dataPost['guiaPorte'];
    $estadoPunto = $dataPost['estadoPunto'];

    $direccion = "";

    $inserta = $db->prepare("REPLACE INTO `despachopuntos` (`fchDespacho`, `correlativo`, `correlPunto`, `tipoPunto`, `nombComprador`, `idDistrito`,`distrito`, `direccion`, `nroGuiaPorte`,  `estado`, `creacFch`) VALUES (:fchDespacho, :correlativo, :correlPunto, :tipoPunto, :nombComprador, :idDistrito, :distrito, :direccion, :guiaPorte, :estadoPunto, NOW());");
    $inserta->bindParam(':fchDespacho',$fchDespacho);
    $inserta->bindParam(':correlativo',$correlativo);
    $inserta->bindParam(':correlPunto',$correlPunto);
    $inserta->bindParam(':tipoPunto',$tipoPunto);
    $inserta->bindParam(':nombComprador',$nombre);
    $inserta->bindParam(':idDistrito',$idDistrito);
    $inserta->bindParam(':distrito',$distrito);
    $inserta->bindParam(':direccion',$direccion);
    $inserta->bindParam(':guiaPorte',$guiaPorte);
    $inserta->bindParam(':estadoPunto',$estadoPunto);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function eliminaPunto($db,$dataPost){
    $fchDespacho = $dataPost['fchDespacho']; 
    $correlativo = $dataPost['correlativo']; 
    $correlPunto = $dataPost['correlPunto'];

    $elimina = $db->prepare("DELETE FROM `despachopuntos` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `correlPunto` = :correlPunto");
    $elimina->bindParam(':fchDespacho',$fchDespacho);
    $elimina->bindParam(':correlativo',$correlativo);
    $elimina->bindParam(':correlPunto',$correlPunto);
    $elimina->execute();
    return $elimina->rowCount();
  }

  function recuperaPunto($db,$dataPost){
    $fchDespacho = $dataPost['fchDespacho']; 
    $correlativo = $dataPost['correlativo']; 
    $correlPunto = $dataPost['correlPunto'];

    $consulta = $db->prepare("SELECT * FROM `despachopuntos` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `correlPunto` = :correlPunto");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->bindParam(':correlPunto',$correlPunto);
    $consulta->execute();
    $dataAux = $consulta->fetchAll();

    foreach ($dataAux as $key => $value) {
      $nombComprador = $value['nombComprador'];
      $distrito = $value['idDistrito']."-".$value['distrito'];
      $nroGuiaPorte = $value['nroGuiaPorte'];
      $tipoPunto = $value['tipoPunto'];
    }

    return $nombComprador."|".$distrito."|".$nroGuiaPorte."|".$tipoPunto."|";
  }

  function buscaDatosParaValidar($db,$dataPost){
    $fchDespacho = $dataPost['fchDespacho']; 
    $correlativo = $dataPost['correlativo']; 
    $idCliente = $dataPost['idCliente']; 
    $idCuenta  = $dataPost['idCuenta'];
    $idDistrOrigen = $dataPost['idDistrOrigen'];

    
    //$correlPunto = $dataPost['correlPunto'];

    $consulta = $db->prepare("SELECT * FROM `despachopuntos` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `correlPunto` = :correlPunto");
    $consulta->bindParam(':fchDespacho',$fchDespacho);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->bindParam(':correlPunto',$correlPunto);
    $consulta->execute();
    $dataAux = $consulta->fetchAll();

    foreach ($dataAux as $key => $value) {
      $nombComprador = $value['nombComprador'];
      $distrito = $value['distrito'];
      $nroGuiaPorte = $value['nroGuiaPorte'];
      $tipoPunto = $value['tipoPunto'];
    }

    return $nombComprador."|".$distrito."|".$nroGuiaPorte."|".$tipoPunto."|";
  }

  function buscarDistritos($db,$dato){
    $datos = array();
    $consulta = $db->prepare("SELECT concat(idubicacion,'-', descripcion) AS dato FROM `ubicacion` WHERE descripcion LIKE '%$dato%'");
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => utf8_encode($item['dato']));
    } 
    return $datos;
  }

  function procesarDatosDespacho($db,$dataPost){
    $datos = $datos2 =  $datos3 = array();

    $valorHrasAdic = $cantHrasAdic = $valorDespacho = $cantDespacho = 0;
    $valorAuxAdic = $cantAuxAdic = $cantKmsAdic = $valorKmsAdic = 0;

    $idCliente = $dataPost['idCliente'];
    $correlCuenta = $dataPost['correlCuenta'];
    $placaM3Fact = $dataPost['placaM3Fact']; 
    $fchDespacho = $dataPost['fchDespacho']; 
    $correlativo = $dataPost['correlativo']; 

    $idDistrOrigen = $dataPost['idDistrOrigen'];
    $idProductoIni = $dataPost['idProducto']; 
    $modoCreacion = $dataPost['modoCreacion'];

    /*$consulta = $db->prepare("SELECT * FROM `despacho` WHERE fchDespacho = '$fchDespacho' AND correlativo = '$correlativo' ");
    $consulta->execute();
    $dataDespacho = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach ($dataDespacho as $key => $value) {
      $valorServicio = $value['valorServicio'];
    }
    */

    $consulta = $db->prepare("SELECT * FROM `despachodetallesporcobrar` WHERE fchDespacho = '$fchDespacho' AND correlativo = '$correlativo' ");
    $consulta->execute();
    $dataDespachoDetallesxCobrar = $consulta->fetchAll(PDO::FETCH_ASSOC);

    foreach ($dataDespachoDetallesxCobrar as $key => $value) {
      if ($value['codigo'] == 'HrasAdic'){
        $valorHrasAdic = round($value['costoUnit'] * $value['cantidad'],2);
        $cantHrasAdic = $value['cantidad'];
      } else if ($value['codigo'] == 'Despacho'){
        $valorDespacho = round($value['costoUnit'] * $value['cantidad'],2);
        $cantDespacho = $value['cantidad'];
      } else if ($value['codigo'] == 'AuxAdic'){
        $valorAuxAdic = round($value['costoUnit'] * $value['cantidad'],2);
        $cantAuxAdic = $value['cantidad'];
      } else if ($value['codigo'] == 'KmsAdic'){
        $valorKmsAdic = round($value['costoUnit'] * $value['cantidad'],2);
        $cantKmsAdic = $value['cantidad'];
      }
    }



    $consulta = $db->prepare("SELECT ptoOrigen, t1.idDistrito AS ptoDestino, distaKm, distaMax, descripcion AS zonaDescrip, idDetalle AS zonaId, t2.cant FROM (SELECT idDistrito, '$idDistrOrigen' As ptoOrigen , ubidista.distaKm FROM `despachopuntos`, ubidista WHERE  ubidista.idOrigen = '$idDistrOrigen' AND despachopuntos.idDistrito = ubidista.idDestino AND fchDespacho = '$fchDespacho' AND correlativo = '$correlativo' ORDER BY distaKm DESC LIMIT 1) as t1 , ubidetalle, (SELECT count(*) AS cant FROM `despachopuntos` WHERE  fchDespacho = '$fchDespacho' AND correlativo = '$correlativo'  ) AS t2 WHERE ubidetalle.distaMax < t1.distaKm ORDER BY distaMax DESC LIMIT 1 ");
    $consulta->execute();
    $dataAnalisisPuntos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    if (count($dataAnalisisPuntos) == 0){
      $zonaIdDespacho = "0001";
      $cantPuntos = 1;
    } else {
      foreach ($dataAnalisisPuntos as $key => $value) {
        $zonaIdDespacho = $value['zonaId'];
        $cantPuntos =  $value['cant'];
      }
    }


    //Extrae datos si es un tercero
    //===============================
    $consulta = $db->prepare("SELECT corrOcurrencia, tipoOcurrencia, tipoConcepto, descripcion, montoTotal, pagado FROM `ocurrenciatercero` WHERE fchDespacho = '$fchDespacho' AND correlativo = '$correlativo' AND tipoOcurrencia IN ('HraExtra', 'Personal Adicional', 'Apoyo Personal') ");
    $consulta->execute();
    $dataOcurrenciaTercero = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $hraExtraVal = $persAdicVal = $apoyoPersVal = 0;
    $hraExtraPagado = $hraExtraDescrip = $hraExtraCorrel = $persAdicPagado = $persAdicDescrip = $persAdicCorrel = $apoyoPersPagado = $apoyoPersDescrip = $apoyoPersCorrel = "";


    foreach ($dataOcurrenciaTercero as $key => $value) {
      if($value['tipoOcurrencia'] == 'HraExtra'){
        $hraExtraVal = $value['montoTotal'];
        $hraExtraPagado = $value['pagado'];
        $hraExtraDescrip = $value['descripcion'];
        $hraExtraCorrel = $value['corrOcurrencia'];
      } else if($value['tipoOcurrencia'] == 'Personal Adicional'){
        $persAdicVal = $value['montoTotal'];
        $persAdicPagado = $value['pagado'];
        $persAdicDescrip = $value['descripcion'];
        $persAdicCorrel = $value['corrOcurrencia'];
      } else if($value['tipoOcurrencia'] == 'Apoyo Personal'){
        $apoyoPersVal = $value['montoTotal'];
        $apoyoPersPagado = $value['pagado'];
        $apoyoPersDescrip = $value['descripcion'];
        $apoyoPersCorrel = $value['corrOcurrencia'];
      }
    }

  //  echo "$hraExtraVal = $persAdicVal = $apoyoPersVal = $hraExtraPagado = $hraExtraDescrip = $hraExtraCorrel = $persAdicPagado = $persAdicDescrip = $persAdicCorrel = $apoyoPersPagado = $apoyoPersDescrip = $apoyoPersCorrel ";

    $consulta = $db->prepare("SELECT `placa`, `costoDia`, `docTercero`, `docPagoTercero`, `pagado`, `guiaTrTercero`, `observPlacaTer`, `fchPago`, `fchCreacion`, `usuario`, `usuarioPagado` FROM `despachovehiculotercero` WHERE fchDespacho = '$fchDespacho' AND correlativo = '$correlativo' ");
    $consulta->execute();
    $dataDespVehicTercero = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $ValUnidadTercero =  $observPlaca = "";

    foreach ($dataDespVehicTercero as $key => $value) {
       $ValUnidadTercero = $value['costoDia'];
       $observPlaca = $value['observPlacaTer'];
    };


    $consulta = $db->prepare("SELECT * FROM `clientecuentaproducto` WHERE `idCliente` LIKE '$idCliente' AND `correlativo` LIKE '$correlCuenta' AND estadoProducto = 'Activo' ORDER BY m3Facturable , zona , puntos ");
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    $m3Elegido = $zonaElegida = $puntosElegido = "";
    $cant = count($todo);

    $contador = 0;


    $opcionesProductos = "";
    foreach ($todo as $key => $value) {
      $contador++;
      if ($value['m3Facturable'] < $placaM3Fact && $contador < $cant ) continue;
      else if ($m3Elegido == '') $m3Elegido = $value['m3Facturable'];
      if ($m3Elegido == $value['m3Facturable'] || ($contador == $cant && count($datos) == 0 ) ){
        
            $datos[$value['idProducto']] = array(
              'm3Facturable' => $value['m3Facturable'],
              'zona' => $value['zona'],
              'puntos' => $value['puntos'],
            );

      } else break;
    }

    $contador = 0;
    $cant = count($datos);
    foreach ($datos as $key => $value) {
      $contador++;
      if ($value['zona'] < $zonaIdDespacho && $contador < $cant) continue;
      elseif ($zonaElegida == "")  $zonaElegida = $value['zona'];
      if ($zonaElegida == $value['zona'] || ($contador == $cant && count($datos2) == 0 ) ){
        $datos2[$key] = array(
          'm3Facturable' => $value['m3Facturable'],
          'zona' => $value['zona'],
          'puntos' => $value['puntos'],
        );
      } else break;  
    }

    if ($modoCreacion == 'Programacion'){
      $idProducto = $idProductoIni;
    } else {
      $contador = 0;
      $cant = count($datos2);
      foreach ($datos2 as $key => $value) {
        $contador++;
        if ($value['puntos'] < $cantPuntos && $contador < $cant) continue;
        elseif ($puntosElegido == "")  $puntosElegido = $value['puntos'];
        if ($puntosElegido == $value['puntos'] || ($contador == $cant && count($datos3) == 0 ) ){
          $idProducto = $key;
        }  else break;
      }
    }

    foreach ($todo as $key => $value) {
      $selected = $value['idProducto'] == $idProducto ? " SELECTED " : ""; 
      $opcionesProductos .= "<option $selected value = '".$value['idProducto']."' >".$value['nombProducto']."- ".$value['m3Facturable']." m3 - ".$value['idZona']." - ".$value['zona']." - ".$value['puntos'] ." puntos </option>";
    }

    $arreglo = Array(
      "precioServ"       => $valorDespacho,
      "kmAdicional"      => $cantKmsAdic,
      "kmAdicionalValor" => $valorKmsAdic,
      "tiempoAdicEnHoras" => $cantHrasAdic,
      "tiempoAdicEnHorasValor" => $valorHrasAdic,
      "nroAuxiliarAdic" => $cantAuxAdic,
      "nroAuxiliarAdicValor" => $valorAuxAdic,
      "opcionesProductos" => $opcionesProductos,
      "hraExtraVal"       => $hraExtraVal,
      "persAdicVal"       => $persAdicVal,
      "apoyoPersVal"      => $apoyoPersVal,
      "ValUnidadTercero"  => $ValUnidadTercero,
      "observPlaca"       => $observPlaca,
    );
    return json_encode($arreglo);
  }


  function calculoVistaPrevia($db,$dataPost){
    $observConductor = $valUnidadTercero = $observPlaca = "";
    $totCobrar = $totPersonal = $totTercero = 0;
    $fchDespacho = $dataPost['fchDespacho'];
    $hraPrimerPunto = $dataPost['hraPrimerPunto'];
    $kmPrimerPunto  = $dataPost['kmPrimerPunto'];
    $hrUltPunto = $dataPost['hrUltPunto'];
    $kmUltPunto = $dataPost['kmUltPunto'];
    $idProducto = $dataPost['idProducto'];
    $fchDespachoFin = $dataPost['fchDespachoFin'];
    $docConductor = $dataPost['conductor'];
    $placa = $dataPost['placa'];
    $tipoCuenta = $dataPost['tipoCuenta'];
    $nroAuxiliares = 0;
    $auxAuxiliares = $dataPost['auxiliares'];
    $auxAuxiliares = preg_replace( '/\n/', '@', $auxAuxiliares );  
    $dataAuxiliar = strtok($auxAuxiliares, "@");
    $continuar = "Si";
    while ( $dataAuxiliar != "" && $continuar == "Si" ) {
      $nroAuxiliares++;
      $dni = substr($dataAuxiliar, 0 , 8);
      $arrAuxiliares[] = $dni;

      $consulta = $db->prepare("SELECT count(*) AS cant FROM `trabajador` WHERE `idTrabajador` LIKE :dni AND estadoTrabajador = 'Activo'  ");
      $consulta->bindParam(':dni',$dni);
      $consulta->execute();
      $aux = $consulta->fetch();
      if ($aux["cant"] == 0 ){
        $continuar = "No";
      } 

      $dataAuxiliar = strtok("@");
    }


    if ($continuar == "Si"){



      if ($tipoCuenta == "SoloPersonal"){
        $categConductor = ""; // $value['categTrabajador'];
        $esMaster = "No"; // $value['esMaster'];
        $precioMaster = 0;
        $rznSocial = 'INVERSIONES MOY S.A.C.';//Solo por compatibilidad


      } else if ($tipoCuenta == "SoloVehiculo"){
        $categConductor = ""; // $value['categTrabajador'];
        $esMaster = "No"; // $value['esMaster'];
        $precioMaster = 0;
        $rznSocial = '';//Solo por compatibilidad


      } else {

        $datos = algunosDatosTrabajador($db, $docConductor);
        foreach ($datos as $key => $value) {
          $categConductor = $value['categTrabajador'];
          $esMaster = $value['esMaster'];
          $precioMaster = $value['precioMaster'];
        }

        $consulta = $db->prepare("SELECT `estado`, `rznSocial` FROM `vehiculo` WHERE `idPlaca` = '$placa' ");
        $consulta->execute();
        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);

        foreach ($datos as $key => $value) {
          $rznSocial = $value['rznSocial'];
        }
      }

      $todo = algunosDatosClienteCuentaProducto($db,$idProducto);

      foreach ($todo as $key => $value) {
        $precioServ = $value['precioServ'];
        $kmEsperado = $value['kmEsperado'];
        $tolerKmEsperado = $value['tolerKmEsperado'];
        $valKmAdic = $value['valKmAdic'];
        $hrasNormales = $value['hrasNormales'];
        $tolerHrasNormales = $value['tolerHrasNormales'];
        $valHraAdic = $value['valHraAdic'];
        $nroAuxiliarPdto = $value['nroAuxiliares'];
        $valAuxiliarAdic = $value['valAuxiliarAdic'];
        $usoMaster =  $value['usoMaster'];
        $pagoBaseConductor = $value['valConductor'];
        $pagoBaseAuxiliar =  $value['valAuxiliar'];
        $valUnidTercCCond = $value['valUnidTercCCond'];
        $valUnidTercSCond = $value['valUnidTercSCond'];
        $hraNormalConductor = $value['hraNormalConductor'];
        $tolerHraCond = $value['tolerHraCond'];
        $valHraAdicCond = $value['valHraAdicCond'];
        $hraNormalAux = $value['hraNormalAux'];
        $tolerHraAux = $value['tolerHraAux'];
        $valHraAdicAux = $value['valHraAdicAux'];

        $hrasNormalTerc = $value['hrasNormalTerc'];
        $tolerHrasNormalTerc = $value['tolerHrasNormalTerc'];
        $valHraExtraTerc = $value['valHraExtraTerc'];
        $valAuxTercero = $value['valAuxTercero'];//Se extrae para siguiente operacion

      }

      $consulta = $db->prepare("SELECT *  FROM `clientecuentapdtootros` WHERE `idProducto` LIKE '$idProducto' ");
      $consulta->execute();
      $dataPdtoOtros = $consulta->fetchAll(PDO::FETCH_ASSOC);

      $totCobrar += $precioServ;

      //Calcula el valor de kilometraje adicional
      ///////////////////////////////////////////
      $kmAdicional = ($kmUltPunto - $kmPrimerPunto) - ($kmEsperado + $tolerKmEsperado) <= 0 ? 0 : $kmUltPunto - $kmPrimerPunto - $kmEsperado;
      $kmAdicionalValor = round($kmAdicional * $valKmAdic,2);
      $totCobrar += $kmAdicionalValor;

      //Calcula el valor de horas adicionales
      ///////////////////////////////////////
      $arrHraNormales = explode(":", $hrasNormales);
      $arrHraTolerancia = explode(":", $tolerHrasNormales);
      $tiempoEstimadoEnSegundos = $arrHraNormales[0]*3600 + $arrHraNormales[1]*60 + $arrHraNormales[2];
      $tiempoToleranciaEnSegundos = $arrHraTolerancia[0]*3600 + $arrHraTolerancia[1]*60 + $arrHraTolerancia[2];
      //echo "tiempoEstimadoEnSegundos $tiempoEstimadoEnSegundos";
      
      $momentoInicio = "$fchDespacho $hraPrimerPunto";
      $momentoFin = "$fchDespachoFin $hrUltPunto";
      $date1 = new DateTime($momentoInicio);
      $date2 = new DateTime($momentoFin);
      $diff = $date1->diff($date2);
      $tiempoEnSegundos = ( ($diff->days * 24 ) * 3600 ) + ( $diff->h * 3600 ) + ( $diff->i * 60 ) + $diff->s;
      $tiempoAdicEnSegundos = $tiempoEnSegundos - ($tiempoEstimadoEnSegundos + $tiempoToleranciaEnSegundos) <= 0 ? 0 : $tiempoEnSegundos - $tiempoEstimadoEnSegundos;
      $tiempoAdicEnHoras = $tiempoAdicEnSegundos / 3600;
      $tiempoAdicEnHorasValor = round($tiempoAdicEnHoras * $valHraAdic,2);

      $totCobrar += $tiempoAdicEnHorasValor;

      //Calcula el Auxiliar Adicional
      ///////////////////////////////
      $nroAuxiliarAdic = ($nroAuxiliares <= $nroAuxiliarPdto) ? 0 : $nroAuxiliares - $nroAuxiliarPdto;
      $nroAuxiliarAdicValor  = round($nroAuxiliarAdic * $valAuxiliarAdic,2);

      $totCobrar += $nroAuxiliarAdicValor;

      //Calculo datos si es tercero
      /////////////////////////////
      if ($rznSocial == 'INVERSIONES MOY S.A.C.' ) {
        $valUnidadTercero = 0;
      } else {
        if ($categConductor == "Tercero") {
          $valUnidadTercero = $valUnidTercCCond;
          $pagoBaseConductor = 0;
          $observConductor = "Es un Conductor Tercero";
          $observPlaca = "Precio con Conductor";
        } else {
          $valUnidadTercero = $valUnidTercSCond;
          $observPlaca = "Precio sin Conductor";
        }
      }

      $totTercero += $valUnidadTercero;

      //Calcula el valor de horas Adicionales Tercero
      $arrHraNormales = explode(":", $hrasNormalTerc);
      $arrHraTolerancia = explode(":", $tolerHrasNormalTerc);
      $tiempoEstimadoEnSegundos = $arrHraNormales[0]*3600 + $arrHraNormales[1]*60 + $arrHraNormales[2];
      $tiempoToleranciaEnSegundos = $arrHraTolerancia[0]*3600 + $arrHraTolerancia[1]*60 + $arrHraTolerancia[2];
      $tiempoAdicEnSegTerc = $tiempoEnSegundos - ($tiempoEstimadoEnSegundos + $tiempoToleranciaEnSegundos) <= 0 ? 0 : $tiempoEnSegundos - $tiempoEstimadoEnSegundos;
      $tiempoAdicEnHorasTerc = $tiempoAdicEnSegTerc / 3600;
      $tiempoAdicHrsTercValor = round($tiempoAdicEnHorasTerc * $valHraExtraTerc,2);

      $hrasAdic = floor($tiempoAdicEnSegTerc / 3600);
      $minsAdic = floor(($tiempoAdicEnSegTerc % 3600) /60 );
      $segsAdic = ($tiempoAdicEnSegTerc % 3600) % 60 ;
      $tpoAdicEnHraMinSeg =  substr("00".$hrasAdic,-2).":".substr("00".$minsAdic,-2).":".substr("00".$segsAdic,-2);

      $totTercero += $tiempoAdicHrsTercValor;

      //Casos especiales
      $mntTotal = 0;
      if (count($dataPdtoOtros) > 0){
        $cadOtros = "<table id = 'tblOtros' ><tr><th width = '120'>Nombre</th><th width = '80'>Tipo</th><th width = '80'>Tolerancia</th><th width = '80'>Cto. Unit.</th><th width = '80'>Monto</th><th width = '90'>Que se Cobra</th></tr>";

        foreach ($dataPdtoOtros as $key => $value) {

          if ($value['tipo'] == 'Horas' ){
            $arrAuxHraToler = explode(":", $value['tolerancia']);
            $tiempoAuxTolerEnSegundos = $arrAuxHraToler[0]*3600 + $arrAuxHraToler[1]*60 + $arrAuxHraToler[2];
            if ($tiempoEnSegundos > $tiempoAuxTolerEnSegundos ){
              $costoSegundos = $value['costoUnidad'] / 3600;
              $queCobrar =  $tiempoEnSegundos - $tiempoAuxTolerEnSegundos;
              $mntACobrar = $queCobrar * $costoSegundos ;
              $horas = floor($queCobrar/3600);
              $minutos = floor(($queCobrar-($horas*3600))/60);
              $segundos = $queCobrar-($horas*3600)-($minutos*60);
              $queCobrarEnFormato =  $horas.":".$minutos.":".$segundos;
            } else {
              $mntACobrar = 0;
              $queCobrarEnFormato = "00:00:00";
            }
          } elseif ($value['tipo'] == 'Paquete' ) {
            $mntACobrar = 0;
            $queCobrarEnFormato = 0;

          } elseif ($value['tipo'] == 'Kms' ) {
            if ( $kmUltPunto - $kmPrimerPunto >  $value['tolerancia'] ){
              $queCobrarEnFormato =  $kmUltPunto - $kmPrimerPunto -  $value['tolerancia'] ;
              $mntACobrar = round($queCobrarEnFormato * $value['costoUnidad'],2);
            }
          } elseif ($value['tipo'] == 'Peso' ) {
            $mntACobrar = 0;
            $queCobrarEnFormato = 0;     
          }
          $mntTotal += $mntACobrar;
          $cadOtros .= "<tr><td>".$value['nombre']."</td><td>".$value['tipo']."</td><td align = 'right'>".$value['tolerancia']."</td><td align = 'right'>".number_format($value['costoUnidad'],2)."</td><td align = 'right'>".number_format($mntACobrar,2)."</td><td align = 'right'>$queCobrarEnFormato</td>";    
        }
        $cadOtros .= "</table>";
        $cadOtros .= str_repeat("&nbsp;", 65)."Total de Otros Conceptos <input name = 'mntTotalOtros' id = 'mntTotalOtros' size = '8' readonly value = ".$mntTotal.">";
      } else {
        $cadOtros = "Total de Otros Conceptos <input name = 'mntTotalOtros' id = 'mntTotalOtros' size = '8' value = '0' readonly>";
      }
      $totCobrar += $mntTotal;

      $cadResumen = "<table><tr><th width = '90' >A Cobrar</th><td width = '90' align = 'right' >".number_format($totCobrar,2)."</td><td width = '80' align = 'right'>100.00%</td></tr>";

      if ($totCobrar == 0) $porcTercero = 0 ;
      else $porcTercero = $totTercero/$totCobrar*100;

      if ($porcTercero < 20 ) $color = 'verde';
      else if ($porcTercero < 40) $color = 'amarillo';
      else $color = 'rojo'; 
      $cadResumen .= "<tr><th>Tercero</th><td align = 'right' >".number_format($totTercero,2)."</td><td align = 'right' class = '$color' >". number_format($porcTercero,2) ."%</td></tr>";

    /*
      $netoAntesOtrosGastos = $totCobrar - $totPersonal - $totTercero;
      $porcAntesOtrosGastos = $netoAntesOtrosGastos/$totCobrar*100;
      if ($porcAntesOtrosGastos < 10 ) $color = 'rojo';
      else if ($porcAntesOtrosGastos < 40) $color = 'amarillo';
      else $color = 'verde'; 
      $cadResumen .= "<tr><th>Neto A.O.G.</th><td align = 'right' >".number_format($netoAntesOtrosGastos,2)."</td><td align = 'right' class = '$color' >". number_format($porcAntesOtrosGastos,2) ."%</td></tr>";
    */

      $cadResumen .= "<tr><th>Personal</th><td id = 'totPersonal'  align = 'right'></td></tr>";
      $cadResumen .= "</table>";

      $arreglo = Array(
        "precioServ"       => $precioServ,
        "kmAdicional"      => $kmAdicional,
        "kmAdicionalValor" => $kmAdicionalValor,
        "tiempoAdicEnHoras" => $tiempoAdicEnHoras,
        "tiempoAdicEnHorasValor" => $tiempoAdicEnHorasValor,
        "nroAuxiliarAdic" => $nroAuxiliarAdic,
        "nroAuxiliarAdicValor" => $nroAuxiliarAdicValor,
        "pagoBaseConductor" => $pagoBaseConductor,
        "pagoBaseAuxiliar" => $pagoBaseAuxiliar,
        //"pagoAdicConductor" => $pagoAdicConductor,
        //"pagoAdicAuxiliar" => $pagoAdicAuxiliar,
        "valUnidadTercero" => $valUnidadTercero,
        "tiempoAdicHrsTercValor" => $tiempoAdicHrsTercValor,
        "tpoAdicEnHraMinSeg" => $tpoAdicEnHraMinSeg,
        "valAuxTercero" => $valAuxTercero,
        "observConductor" => $observConductor,
        "observPlaca" => $observPlaca,
        "cadOtros" => $cadOtros,
        "cadResumen" => $cadResumen,
        "hraNormalConductor" => $hraNormalConductor,
        "tolerHraCond" => $tolerHraCond,
        "valHraAdicCond" => $valHraAdicCond,
        "hraNormalAux" => $hraNormalAux,
        "tolerHraAux" => $tolerHraAux,
        "valHraAdicAux" => $valHraAdicAux,
        "usoMaster" => $usoMaster,
        "totCobrar" => $totCobrar,
        "totTercero" => $totTercero,
      );

      return json_encode($arreglo);
    } else {
      $arreglo = Array(
        "dni"       => $dni
      );
      return json_encode($arreglo);
    }

    
  /*
    $cadena = $precioServ."|".$kmAdicional."|".$kmAdicionalValor;  
    return $cadena;
    */

  }

  function generaCorrelOtro($db,$idProducto){

    $consulta = $db->prepare("SELECT correlativo  FROM `clientecuentapdtootros` WHERE `idProducto` = :idProducto ORDER BY `correlativo` DESC LIMIT 1 ");
    $consulta->bindParam(':idProducto',$idProducto); 
    $consulta->execute();
    $data = $consulta->fetchAll();
    $correlOtro = 1;
    foreach ($data as $key => $value) {
      $correlOtro = $value['correlativo'] + 1;
    }
    return $correlOtro;
  }



  function insertaOtro($db,$dataPost){
    $usuario = $_SESSION['usuario'];
    $idProducto = $dataPost['idProducto']; 
    $tipo = $dataPost['tipo'];
    $nombre = $dataPost['nombre']; 
    $tolerancia = $dataPost['tolerancia'];
    $costoPorUnidad = $dataPost['costoPorUnidad'];

    $nvoCorrelativo =  ($dataPost['correlOtro'] == "" ) ? generaCorrelOtro($db,$idProducto) :  $dataPost['correlOtro'];

    $inserta = $db->prepare("REPLACE INTO `clientecuentapdtootros` (`correlativo`, `idProducto`, `nombre`, `tipo`, `tolerancia`, `costoUnidad`, `creacUsuario`) VALUES (:correlativo, :idProducto, :nombre, :tipo, :tolerancia, :costoPorUnidad, :usuario);");
    $inserta->bindParam(':correlativo',$nvoCorrelativo);
    $inserta->bindParam(':idProducto',$idProducto);
    $inserta->bindParam(':tipo',$tipo);
    $inserta->bindParam(':nombre',$nombre);
    $inserta->bindParam(':tolerancia',$tolerancia);
    $inserta->bindParam(':costoPorUnidad',$costoPorUnidad);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function recuperaOtro($db,$dataPost){
    $idProducto  = $dataPost['idProducto']; 
    $correlativo = $dataPost['correlOtro']; 
   
    $consulta = $db->prepare("SELECT * FROM `clientecuentapdtootros` WHERE `idProducto` = :idProducto AND `correlativo` = :correlativo");
    $consulta->bindParam(':idProducto',$idProducto);
    $consulta->bindParam(':correlativo',$correlativo);
    $consulta->execute();
    $dataAux = $consulta->fetchAll();

    foreach ($dataAux as $key => $value) {
      $nombre = $value['nombre'];
      $tipo = $value['tipo'];
      $tolerancia = $value['tolerancia'];
      $costoUnidad = $value['costoUnidad'];
    }

    $arreglo = Array(
      "nombre"     => $nombre,
      "tipo"       => $tipo,
      "tolerancia" => $tolerancia,
      "costoUnidad"=> $costoUnidad,
    );

    return json_encode($arreglo);
  }

  function elimOtro($db,$dataPost){
    $idProducto = $dataPost['idProducto']; 
    $correlativo = $dataPost['correlOtro']; 

    $elimina = $db->prepare("DELETE FROM `clientecuentapdtootros` WHERE `correlativo` = :correlativo AND  `idProducto` = :idProducto");
    $elimina->bindParam(':idProducto',$idProducto);
    $elimina->bindParam(':correlativo',$correlativo);
    $elimina->execute();
    return $elimina->rowCount();
  }

  function insertTripulac02($db, $fchDespacho, $correlativo, $idTrabajador, $valorRol, $tipoRol, $trabFchDespachoFin, $trabHraInicio, $trabHraFin, $valorAdicional, $valorHraExtra, $observPersonal, $esReten){
    $usuario = $_SESSION['usuario'];
    $modoCreacDp = "Miscelaneo";

    $inserta = $db->prepare("INSERT INTO despachopersonal (fchDespacho, correlativo, idTrabajador, valorRol, tipoRol,  valorAdicional, valorHraExtra , trabFchDespachoFin, trabHraInicio, trabHraFin, esReten, observPersonal, usuario, fchActualizacion, creacFchDp, creacUsuDp, modoCreacDp) VALUES ( :fchDespacho, :correlativo, :idTrabajador, :valorRol, :tipoRol, :valorAdicional, :valorHraExtra, :trabFchDespachoFin, :trabHraInicio, :trabHraFin, :esReten, :observPersonal, :usuario, curdate(), now(), :usuario, :modoCreacDp )");
    $inserta->bindParam(':fchDespacho',$fchDespacho);
    $inserta->bindParam(':correlativo',$correlativo);
    $inserta->bindParam(':idTrabajador',$idTrabajador);
    $inserta->bindParam(':valorRol',$valorRol);
    $inserta->bindParam(':tipoRol',$tipoRol);

    $inserta->bindParam(':valorAdicional',$valorAdicional);
    $inserta->bindParam(':valorHraExtra',$valorHraExtra);
    $inserta->bindParam(':trabFchDespachoFin',$trabFchDespachoFin);
    $inserta->bindParam(':trabHraInicio',$trabHraInicio);
    $inserta->bindParam(':trabHraFin',$trabHraFin);
    $inserta->bindParam(':esReten',$esReten);
    $inserta->bindParam(':observPersonal',$observPersonal);
    $inserta->bindParam(':modoCreacDp',$modoCreacDp);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();

    return $inserta->rowCount();
  }

function generaDocsDespacho($db, $dataPost){
  $usuario = $_SESSION['usuario'];
  $fchDespacho = $dataPost['fchDespacho']; 
  $correlativo = $dataPost['correlativo'];
  $guiaPorte = $dataPost['guiaPorte'];
  $guiaCliente = $dataPost['guiaCliente'];
  $guiaTrTercero = $dataPost['guiaTercero'];
  $precioBaseServ = $dataPost['precioBaseServ'];
  $fchFinCli = $dataPost['fchFinCli'];
  $fchFinDespacho = $dataPost['fchFinDespacho'];

  $auxAuxiliares = $dataPost['auxAuxiliares'];
  $porcIgv = $dataPost['porcIgv'];
  $auxConductor = $dataPost['auxConductor'];
  $idConductor = strtok($auxConductor, " - ");
  $arrGuiaPorte = explode(" ", $guiaPorte);
  //$arrGuiaCliente = explode(" ", $guiaCliente);
  //$arrGuiaTercero = explode(" ", $guiaTercero);
  $nroAuxiliares = 0;
  $auxAuxiliares = preg_replace( '/\n/', '@', $auxAuxiliares );
  //echo $auxAuxiliares;
  $auxReten = $dataPost["auxReten"];
  if ($auxReten != '-') $usaReten = strtok($auxReten,"-");
  else $usaReten = "";

  $hraInicio = $dataPost['hraInicio'];
  $hraInicioBase = $dataPost['hraIniBase'];
  $hraFinBase = $dataPost['hraFinBase'];
  $hraFinCli = $dataPost['hraFinCli'];

  $kmIniMoy = $dataPost['kmIniMoy'];
  $kmIniCli = $dataPost['kmIniCli'];
  $kmFinCli = $dataPost['kmFinCli'];
  $kmFinMoy = $dataPost['kmFinMoy'];
  $auxAdicValor = $dataPost['auxAdicValor'];
  $auxAdic = $dataPost['auxAdic'];
  $hraAdicValor = $dataPost['hraAdicValor'];
  $hraAdic = $dataPost['hraAdic'];
  $kmAdicValor = $dataPost['kmAdicValor'];
  $kmAdicional = $dataPost['kmAdicional'];
  $observDespacho = utf8_decode($dataPost['observDespacho']);
  $observServicio = $dataPost['observServicio'];
  $idSedeOrigen = $dataPost['idSedeOrigen'];
  $ptoOrigen = $dataPost['ptoOrigen'];
  $lugarFin = $dataPost['lugarFin'];
  $observPlaca = $dataPost["observPlaca"];
  $valUnidadTercero = $dataPost["valUnidadTercero"];
  
  $valHraExtraTercero = $dataPost["valHraExtraTercero"];
  $observHraExtraTercero = $dataPost["observHraExtraTercero"];
  $valAuxTercero = $dataPost["valAuxTercero"];
  $observAuxTercero = $dataPost["observAuxTercero"]; 
  $valApoyoPersonalTercero = $dataPost["valApoyoPersonalTercero"];
  $observApoyoPersonalTercero = $dataPost["observApoyoPersonalTercero"];

  $idProducto = $dataPost['idProducto'];
  $totAuxiliares = $dataPost['totAuxiliares'];
  $correlCuenta = $dataPost['correlCuenta'];
  $auxCuenta = $dataPost['auxCuenta'];
  $tipoCuenta = strtok($auxCuenta, ":");
  $cuenta = strtok(":");

  if ($tipoCuenta == "SoloPersonal"){
    $placa = NULL;
    $considerarPropio = NULL;

  } else {
    $auxVehiculo =  $dataPost['auxVehiculo'];
    $placa =  strtok($auxVehiculo, "|");
    $sinUso =  strtok("|");
    $considerarPropio =  strtok("|"); 
  }

  //Datos estandar del producto
  $todo = algunosDatosClienteCuentaProducto($db,$idProducto);

  $idTrabajador = "";//Para el logaccion
  
  foreach ($todo as $key => $value) {
    $ctaPdtoPrecioServ = $value['precioServ'];
    $ctaPdtoKmEsperado = $value['kmEsperado'];
    $ctaPdtoTolerKmEsperado = $value['tolerKmEsperado'];
    $ctaPdtoValKmAdic = $value['valKmAdic'];
    $ctaPdtoHrasNormales = $value['hrasNormales'];
    $ctaPdtoTolerHrasNormales = $value['tolerHrasNormales'];
    $ctaPdtoValHraAdic = $value['valHraAdic'];
    $ctaPdtoNroAuxiliarPdto = $value['nroAuxiliares'];
    $ctaPdtoValAuxiliarAdic = $value['valAuxiliarAdic'];
    $ctaPdtoUsoMaster = $value['usoMaster'];
    $ctaPdtoPagoBaseConductor = $value['valConductor'];
    $ctaPdtoPagoBaseAuxiliar = $value['valAuxiliar'];
    $ctaPdtoValUnidTercCCond = $value['valUnidTercCCond'];
    $ctaPdtoValUnidTercSCond = $value['valUnidTercSCond'];
    $ctaPdtoM3Facturable = $value['m3Facturable'];
    $ctaPdtoCostoHraExtra = $value['valHraAdic'];

    $ctaPdtoHraNormalConductor = $value['hraNormalConductor'];
    $ctaPdtoTolerHraCond = $value['tolerHraCond'];
    $ctaPdtoValHraAdicCond = $value['valHraAdicCond'];
    $ctaPdtoHraNormalAux = $value['hraNormalAux'];
    $ctaPdtoTolerHraAux = $value['tolerHraAux'];
    $ctaPdtoValHraAdicAux = $value['valHraAdicAux'];

    $contarDespacho = $value['contarDespacho'];
    $superCuenta =  $value['superCuenta'];

  }


  if ($_SESSION['puedeModificar'] == 'Si' || $_SESSION['especialGuiaPorte'] == "Si" ){
    //Registrar las guias porte
    $elimina = $db->prepare("DELETE FROM `despachoguiaporte` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo ");
    $elimina->bindParam(':fchDespacho',$fchDespacho);
    $elimina->bindParam(':correlativo',$correlativo);
    $elimina->execute();

    if($elimina->rowCount() >0 ){
      $descripcion = "Un cambio del despacho $fchDespacho-$correlativo requirió eliminar registros en la tabla despachoguiaporte. Se han eliminado ".$elimina->rowCount()." registro(s) en dicha tabla";
      logAccion($db, $descripcion, $idTrabajador, $placa);
    }

    foreach ($arrGuiaPorte as $key => $value) {
      $inserta = $db->prepare("INSERT INTO `despachoguiaporte` (`guiaPorte`, `fchDespacho`, `correlativo`, `usuario`, `fchCreacion`) VALUES (:guiaPorte, :fchDespacho, :correlativo, :usuario, CURDATE())");
      $inserta->bindParam(':guiaPorte',$value);
      $inserta->bindParam(':fchDespacho',$fchDespacho);
      $inserta->bindParam(':correlativo',$correlativo);
      $inserta->bindParam(':usuario',$usuario);
      $inserta->execute();
    }

  }

  if ($_SESSION['puedeModificar'] == 'Si') {
    //data de tripulación
    //Eliminando Tripulación
    $elimina = $db->prepare("DELETE FROM  `despachopersonal` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
    $elimina->bindParam(':fchDespacho',$fchDespacho);
    $elimina->bindParam(':correlativo',$correlativo);
    $elimina->execute();

    if($elimina->rowCount() >0 ){
      $descripcion = "Un cambio del despacho $fchDespacho-$correlativo requirió eliminar registros en la tabla despachopersonal. Se han eliminado ".$elimina->rowCount()." registro(s) en dicha tabla";
      logAccion($db, $descripcion, $idTrabajador, $placa);
    }

    $cadena = $fchDespacho."-".$correlativo;
    $elimina = $db->prepare("DELETE FROM `prestamo` WHERE `tipoItem` LIKE 'hraExtra' AND descripcion LIKE '%$cadena' AND fchPago = '$fchDespacho' ");
    $elimina->execute();
  }
  

  $igvServicio = round($porcIgv * $precioBaseServ,2);
  $modoTerminado = "No Hubo";
  $modoConcluido = "Miscelaneo";
  $usuarioTerminado = "No Hubo";

  $actualiza = $db->prepare("UPDATE `despacho` SET `valorServicio` = :valorServicio, fchDespachoFinCli = :fchFinCli, fchDespachoFin = :fchFinDespacho , hraFin = :hraFinBase, igvServicio = :igvServicio, nroAuxiliares = :nroAuxiliares, nroAuxiliaresAdic = :nroAuxiliaresAdic, hraInicio = :hraInicio, hraInicioBase = :hraInicioBase, hraFinCliente = :hraFinCliente, m3 = :m3, usaReten = :usaReten, zonaDespacho = :zonaDespacho, kmInicio = :kmInicio, kmInicioCliente = :kmInicioCliente, kmFinCliente = :kmFinCliente, kmFin = :kmFin, idSedeOrigen = :idSedeOrigen, ptoOrigen = :ptoOrigen, lugarFinCliente = :lugarFin, idProducto = :idProducto, hrasNormales = :hrasNormales, costoHraExtra = :costoHraExtra, tolerHrasNormales = :tolerHrasNormales, valorConductor = :valorConductor, valorAuxiliar = :valorAuxiliar , nroAuxiliaresCuenta = :nroAuxiliaresCuenta, valorAuxAdicional = :valorAuxAdicional, usarMaster = :usarMaster, tpoExtraHras = :tpoExtraHras, correlCuenta = :correlCuenta , cuenta = :cuenta, placa = :placa, tipoServicioPago = :tipoCuenta, concluido = 'Si', estadoDespacho = 'Terminado', observacion = :observacion, guiaCliente = :guiaCliente, hraNormCondDespacho = :hraNormCondDespacho, tolerHraCondDespacho = :tolerHraCondDespacho, valHraAdicCondDespacho = :valHraAdicCondDespacho, hraNormAuxDespacho = :hraNormAuxDespacho, tolerHraAuxDespacho = :tolerHraAuxDespacho , valHraAdicAuxDespacho = :valHraAdicAuxDespacho, contarDespacho = :contarDespacho, superCuenta = :superCuenta,  considerarPropio = :considerarPropio, `usuarioGrabaFin` = :usuario, `fchGrabaFin` = curdate(), `hraGrabaFin` = curtime(), modoTerminado = :modoTerminado, modoConcluido = :modoConcluido, usuarioTerminado = :usuarioTerminado, fchTerminado = curdate()  WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
  $actualiza->bindParam(':fchDespacho',$fchDespacho);
  $actualiza->bindParam(':correlativo',$correlativo);
  $actualiza->bindParam(':fchFinCli',$fchFinCli);
  $actualiza->bindParam(':valorServicio',$precioBaseServ);
  $actualiza->bindParam(':igvServicio',$igvServicio);
  $actualiza->bindParam(':nroAuxiliares',$totAuxiliares);
  $actualiza->bindParam(':nroAuxiliaresAdic',$auxAdic);
  $actualiza->bindParam(':hraInicio', $hraInicio);
  $actualiza->bindParam(':hraInicioBase', $hraInicioBase);
  $actualiza->bindParam(':hraFinCliente', $hraFinCli);
  $actualiza->bindParam(':hraFinBase',$hraFinBase);
  $actualiza->bindParam(':usaReten',$usaReten);
  $actualiza->bindParam(':kmInicio', $kmIniMoy);
  $actualiza->bindParam(':kmInicioCliente', $kmIniCli);
  $actualiza->bindParam(':kmFinCliente', $kmFinCli);
  $actualiza->bindParam(':kmFin',$kmFinMoy);
  $actualiza->bindParam(':idSedeOrigen', $idSedeOrigen);
  $actualiza->bindParam(':ptoOrigen', $ptoOrigen);
  $actualiza->bindParam(':m3', $ctaPdtoM3Facturable);
  $actualiza->bindParam(':zonaDespacho', $zonaDespacho);
  $actualiza->bindParam(':lugarFin', $lugarFin);
  $actualiza->bindParam(':hrasNormales', $ctaPdtoHrasNormales);
  $actualiza->bindParam(':costoHraExtra', $ctaPdtoCostoHraExtra);
  $actualiza->bindParam(':tolerHrasNormales', $ctaPdtoTolerHrasNormales);
  $actualiza->bindParam(':valorConductor', $ctaPdtoPagoBaseConductor);
  $actualiza->bindParam(':valorAuxiliar', $ctaPdtoPagoBaseAuxiliar);

  $actualiza->bindParam(':idProducto', $idProducto);

  $actualiza->bindParam(':nroAuxiliaresCuenta', $ctaPdtoNroAuxiliarPdto);
  $actualiza->bindParam(':valorAuxAdicional', $ctaPdtoValAuxiliarAdic);
  $actualiza->bindParam(':usarMaster', $ctaPdtoUsoMaster);
  $actualiza->bindParam(':tpoExtraHras', $hraAdic);
  $actualiza->bindParam(':correlCuenta', $correlCuenta);
  $actualiza->bindParam(':cuenta', $cuenta);
  $actualiza->bindParam(':tipoCuenta', $tipoCuenta);
  $actualiza->bindParam(':placa', $placa);
  $actualiza->bindParam(':observacion', $observDespacho);
  $actualiza->bindParam(':guiaCliente', $guiaCliente);
  $actualiza->bindParam(':fchFinDespacho', $fchFinDespacho);

  $actualiza->bindParam(':hraNormCondDespacho', $ctaPdtoHraNormalConductor);
  $actualiza->bindParam(':tolerHraCondDespacho', $ctaPdtoTolerHraCond);
  $actualiza->bindParam(':valHraAdicCondDespacho', $ctaPdtoValHraAdicCond);
  $actualiza->bindParam(':hraNormAuxDespacho', $ctaPdtoHraNormalAux);
  $actualiza->bindParam(':tolerHraAuxDespacho', $ctaPdtoTolerHraAux);
  $actualiza->bindParam(':valHraAdicAuxDespacho', $ctaPdtoValHraAdicAux);

  $actualiza->bindParam(':considerarPropio', $considerarPropio);
  $actualiza->bindParam(':contarDespacho', $contarDespacho);
  $actualiza->bindParam(':superCuenta', $superCuenta);

  $actualiza->bindParam(':modoTerminado', $modoTerminado);
  $actualiza->bindParam(':modoConcluido', $modoConcluido);
  $actualiza->bindParam(':usuarioTerminado', $usuarioTerminado);

  $actualiza->bindParam(':usuario', $usuario);



  $actualiza->execute();

  //print_r($actualiza->errorInfo());

  if ($kmFinMoy == null)
    $kmFinVeh = $kmFinCli;
  else if ($kmFinCli == null)
    $kmFinVeh = $kmFinMoy;
  else if ($kmFinMoy > $kmFinCli)
    $kmFinVeh = $kmFinMoy;
  else
    $kmFinVeh = $kmFinCli;

  $actualiza = $db->prepare("UPDATE `vehiculo` SET `kmUltimaMedicion` =  if(`kmUltimaMedicion` < '$kmFinVeh', :kmFin, `kmUltimaMedicion`), `usuarioUltimoCambio` = :usuario , `fchUltimoCambio` = curdate()  WHERE `vehiculo`.`idPlaca` = :placa");
  $actualiza->bindParam(':placa',$placa);
  $actualiza->bindParam(':kmFin',$kmFinVeh);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();

  // `guiaCliente`,   `nroAuxiliaresAdic`, `costo`, `topeServicioHraNormal`, `costoHraExtra`, `toleranCobroHraExtra`,  `costoTotal`, `nroGuias`, `nroDespachos`, `lugarFinCliente`, `recorridoEsperado`,  `ptoOrigen`, `tipoDestino`, `ptoDestino`, `observacion`, `observCliente`, `docCobranza`, `tipoDoc`,  `terceroConPersonalMoy`, `usuario`, `fchCreacion`, `modoCreacion`, `usuarioGrabaFin`, `fchGrabaFin`, `hraGrabaFin`, `usuarioCreaCobranza`, `fchCreaCobranza`,  `valKmAdic`, `hraIniEsperado`, `valAdicHraIniEsper`, `hraFinEsperado`, `valAdicHraFinEsper`, `cobrarPeaje`, `cobrarRecojoDevol`, `valUnidTercCCond`, `valUnidTercSCond`, `hrasNormalTerc`, `tolerHrasNormalTerc` 

  //////////////////////////////////////////
  //// GENERANDO LOS DOCUMENTOS DE COBRANZA
  //////////////////////////////////////////
  //elimina info sobre detalles de cobro

  if ($_SESSION['puedeModificar'] == 'Si' || $_SESSION['especialCobranza'] == 'Si'){
    $elimina = $db->prepare("DELETE FROM `despachodetallesporcobrar` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
    $elimina->bindParam(':fchDespacho',$fchDespacho);
    $elimina->bindParam(':correlativo',$correlativo);
    $elimina->execute();

    if($elimina->rowCount() >0 ){
      $descripcion = "Un cambio del despacho $fchDespacho-$correlativo requirió eliminar registros en la tabla despachodetallesporcobrar. Se han eliminado ".$elimina->rowCount()." registro(s) en dicha tabla";
      logAccion($db, $descripcion, $idTrabajador, $placa);
    }

    insertaDetallesPorCobrar($db,$fchDespacho,$correlativo,'Despacho',$precioBaseServ,1,$observDespacho, $observServicio);

    if ($auxAdic > 0 ){
      $auxAdicValUnit = round($auxAdicValor / $auxAdic,2);
      insertaDetallesPorCobrar($db,$fchDespacho,$correlativo,'AuxAdic',$auxAdicValUnit,$auxAdic,$observDespacho, $observServicio);    
    }

    if ($hraAdic > 0 ){
      $hraAdicValUnit = round($hraAdicValor / $hraAdic,2);
      insertaDetallesPorCobrar($db,$fchDespacho,$correlativo,'HrasAdic',$hraAdicValUnit,$hraAdic,$observDespacho, $observServicio);    
    }

    if ($kmAdicional > 0 && $kmAdicValor > 0 ){
      $kmAdicValUnit = round($kmAdicValor / $kmAdicional,2);
      insertaDetallesPorCobrar($db,$fchDespacho,$correlativo,'KmsAdic',$kmAdicValUnit,$kmAdicional,$observDespacho, $observServicio);    
    }
  }

  //////////////////////////////////////////
  /// GENERANDO PAGO AL TERCERO SI LO ES ///
  //////////////////////////////////////////
  //elimina info sobre liquidacion a tercero si es que hay

  if ($_SESSION['puedeModificar'] == 'Si' ||  $_SESSION['especialLiquidacion'] == 'Si' ){

    $esUnTercero = "No";
    $elimina = $db->prepare("DELETE FROM `despachovehiculotercero` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
    $elimina->bindParam(':fchDespacho',$fchDespacho);
    $elimina->bindParam(':correlativo',$correlativo);
    $elimina->execute();

    if($elimina->rowCount() >0 ){
      $descripcion = "Un cambio del despacho $fchDespacho-$correlativo requirió eliminar registros en la tabla despachovehiculotercero. Se han eliminado ".$elimina->rowCount()." registro(s) en dicha tabla";
      logAccion($db, $descripcion, $idTrabajador, $placa);
    }

    $elimina = $db->prepare("DELETE FROM `ocurrenciatercero` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND tipoOcurrencia IN ('Apoyo Personal','Personal Adicional','HraExtra') ");
    $elimina->bindParam(':fchDespacho',$fchDespacho);
    $elimina->bindParam(':correlativo',$correlativo);
    $elimina->execute();

    if($elimina->rowCount() >0 ){
      $descripcion = "Un cambio del despacho $fchDespacho-$correlativo requirió eliminar registros en la tabla ocurrenciatercero de tipoOcurrencia 'Apoyo Personal, Personal Adicional y HraExtra'. Se han eliminado ".$elimina->rowCount()." registro(s) en dicha tabla";
      logAccion($db, $descripcion, $idTrabajador, $placa);
    }

    //Verifica y procesa si la unidad pertenece a un tercero
    $consulta = $db->prepare("SELECT `documento`, `costoDia`, `modoAcuerdo`  FROM `vehiculotercero` WHERE `idPlaca` = :placa");
    $consulta->bindParam(':placa',$placa); 
    $consulta->execute();
    $consultaPropietario = $consulta->fetchAll();

    /*echo "<pre>";
    print_r($consultaPropietario);
    echo "</pre>";*/
    
    foreach($consultaPropietario as $item){
      $propietario = $item['documento'];
      $modoAcuerdo = $item['modoAcuerdo'];
      //$costoDia = $item['costoDia'];  
      $inserta = $db->prepare("INSERT INTO `despachovehiculotercero` (`fchDespacho`, `correlativo`, `placa`,  `modoAcuerdo`, `costoDia`, `docTercero`, `pagado`, `guiaTrTercero`, `observPlacaTer`, `fchCreacion`, `usuario`) VALUES (:fchDespacho, :correlativo, :placa, :modoAcuerdo, :costoDia, :docTercero, 'No', :guiaTrTercero, :observPlaca, curdate(), :usuario)");
      $inserta->bindParam(':fchDespacho',$fchDespacho);
      $inserta->bindParam(':correlativo',$correlativo);
      $inserta->bindParam(':placa',$placa);
      $inserta->bindParam(':modoAcuerdo',$modoAcuerdo);
      $inserta->bindParam(':costoDia',$valUnidadTercero);
      $inserta->bindParam(':guiaTrTercero',$guiaTrTercero);
      $inserta->bindParam(':docTercero',$propietario);
      $inserta->bindParam(':observPlaca',$observPlaca);
      $inserta->bindParam(':usuario',$usuario);
      $inserta->execute();
      $esUnTercero = "Si";
    }

    if ($valHraExtraTercero > 0){
      $cantidad = insertaAjaxEnOcurrenciaTercero($db, $fchDespacho, $correlativo, 'HraExtra|A favor proveedor', $observHraExtraTercero, $placa, $valHraExtraTercero);
    }

    if ($valAuxTercero > 0){
      $cantidad = insertaAjaxEnOcurrenciaTercero($db, $fchDespacho, $correlativo, 'Personal Adicional|A favor proveedor', $observAuxTercero, $placa, $valAuxTercero);
    }

    if ($valApoyoPersonalTercero > 0){
      $cantidad = insertaAjaxEnOcurrenciaTercero($db, $fchDespacho, $correlativo, 'Apoyo Personal|A favor proveedor', $observAuxTercero, $placa, $valApoyoPersonalTercero);     
    }
  }
}

function recuperaValDespacho($db, $dataPost){
  $usuario = $_SESSION['usuario'];
  $fchDespacho = $dataPost['fchDespacho']; 
  $correlativo = $dataPost['correlativo'];

  $precioServ = $kmAdicional = $kmAdicionalValor = $tiempoAdicEnHoras = $tiempoAdicEnHorasValor = 0;
  $nroAuxiliarAdic = $nroAuxiliarAdicValor = 0;

  $consulta = $db->prepare("SELECT * FROM `despacho` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
  $consulta->bindParam(':correlativo',$correlativo);
  $consulta->execute();
  $dataAuxDespacho = $consulta->fetchAll();

  foreach ($dataAuxDespacho as $key => $value) {
    $precioServ = $value['valorServicio'];
    $kmAdicionalValor = $value['valKmAdic'];
    $usaReten = $value['usaReten'];
 //   $tolerancia = $value['tolerancia'];
   // $costoUnidad = $value['costoUnidad'];
  }

  $consulta = $db->prepare("SELECT * FROM `despachodetallesporcobrar` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
  $consulta->bindParam(':correlativo',$correlativo);
  $consulta->execute();
  $dataAuxDetallesPorCobrar = $consulta->fetchAll();


  foreach ($dataAuxDetallesPorCobrar as $key => $value) {
    if ($value['codigo'] == 'KmsAdic'){
      $kmAdicional = $value['cantidad'];
      $kmAdicionalValor = $value['costoUnit']*$kmAdicional;
    } elseif ($value['codigo'] == 'HrasAdic') {
      $tiempoAdicEnHoras = $value['cantidad'];
      $tiempoAdicEnHorasValor = $value['costoUnit']*$tiempoAdicEnHoras;
    } elseif ($value['codigo'] == 'AuxAdic') {
      $nroAuxiliarAdic = $value['cantidad'];
      $nroAuxiliarAdicValor = $value['costoUnit']*$nroAuxiliarAdic;
    }
  }

  $consulta = $db->prepare("SELECT despachopersonal.*, concat(`trabajador`.nombres,' ', `trabajador`.apPaterno,' ',`trabajador`.apMaterno) AS nombCompleto FROM `despachopersonal`, `trabajador` WHERE `despachopersonal`.idTrabajador = `trabajador`.idTrabajador AND `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo");
  $consulta->bindParam(':fchDespacho',$fchDespacho);
  $consulta->bindParam(':correlativo',$correlativo);
  $consulta->execute();
  $dataAuxDespachoPersonal = $consulta->fetchAll();

  $idConductor = "";

  foreach ($dataAuxDespachoPersonal as $key => $value) {
    if ($value['tipoRol'] == 'Conductor' ){
      $idConductor = $value['idTrabajador'];
    } else if ($usaReten != $value['idTrabajador']) {
      $cadAuxiliares .= $value['nombCompleto']."<br>"; 
    }
  }


  $arreglo = Array(
    "precioServ"       => $precioServ,
    "kmAdicional"      => $kmAdicional,
    "kmAdicionalValor" => $kmAdicionalValor,
    "tiempoAdicEnHoras" => $tiempoAdicEnHoras,
    "tiempoAdicEnHorasValor" => $tiempoAdicEnHorasValor,
    "nroAuxiliarAdic" => $nroAuxiliarAdic,
    "nroAuxiliarAdicValor" => $nroAuxiliarAdicValor,
  );

  return json_encode($arreglo);
}


function calcVistaPrevTrabaj($db, $dataPost){
  $pagoAuxTercero = 0;
  $idTrab = $dataPost['idTrab']; 
  $auxTipoRol = $dataPost['rol'];

  $tipoRol = strtok($auxTipoRol, "/");
  $categTrabajador = strtok("/");
  $fchIni = $dataPost['fchIni']; 
  $hraIni = $dataPost['hraIni'];

  $fchFin = $dataPost['fchFin']; 
  $hraFin = $dataPost['hraFin'];

  $pagoBaseConductor = $dataPost['pagoBaseConductor']; 
  $pagoBaseAuxiliar  = $dataPost['pagoBaseAuxiliar'];

  $hraNormalConductor = $dataPost['hraNormalConductor'];
  $tolerHraCond = $dataPost['tolerHraCond'];
  $valHraAdicCond = $dataPost['valHraAdicCond'];
  $hraNormalAux = $dataPost['hraNormalAux'];
  $tolerHraAux = $dataPost['tolerHraAux'];
  $valHraAdicAux = $dataPost['valHraAdicAux'];
  $usoMaster = $dataPost['usoMaster'];
  $valAuxTercero = $dataPost['valAuxTercero'];

  $dataTrabajador = algunosDatosTrabajador($db, $idTrab);
  $esMaster = $dataTrabajador[0]['esMaster'];
  $precioMaster =  $dataTrabajador[0]['precioMaster'];

  if ($tipoRol == 'Conductor'){
    $hraNormal = $hraNormalConductor;
    $tolerancia = $tolerHraCond;
    $pago = $pagoBaseConductor;
    $pagoSegAdicional = $valHraAdicCond/3600;
  } else {
    $hraNormal = $hraNormalConductor;
    $tolerancia = $tolerHraCond;
    $pago = $pagoBaseAuxiliar;
    $pagoSegAdicional = $valHraAdicAux/3600;
  }
  $msj = "";
  if ($esMaster == 'Si' && $usoMaster == 'Si' && $pago < $precioMaster){
    $pago = $precioMaster;
    $msj = "Se usa precio Master";
  }

  //No pagar al tercero
  if ($categTrabajador == 'Tercero' ){
    $pago = 0;
    $pagoSegAdicional = 0;
    if ($tipoRol == 'Auxiliar') $pagoAuxTercero = $valAuxTercero;
  }

  $arrHraNormales = explode(":", $hraNormal);
  $arrHraTolerancia = explode(":", $tolerancia);
  $tiempoEstimadoEnSegundos = $arrHraNormales[0]*3600 + $arrHraNormales[1]*60 + $arrHraNormales[2];
  //echo "TIEMPO $tiempoEstimadoEnSegundos";
  $tiempoToleranciaEnSegundos = $arrHraTolerancia[0]*3600 + $arrHraTolerancia[1]*60 + $arrHraTolerancia[2];
  $momentoInicio = "$fchIni $hraIni";
  $momentoFin = "$fchFin $hraFin";

  //echo "tiempoEstimadoEnSegundos $tiempoEstimadoEnSegundos";
  //echo "tiempoToleranciaEnSegundos $tiempoToleranciaEnSegundos";
  //echo "$momentoInicio $momentoFin";
  $date1 = new DateTime($momentoInicio);
  $date2 = new DateTime($momentoFin);
  $diff = $date1->diff($date2);

  $tiempoEnSegundos = ( ($diff->days * 24 ) * 3600 ) + ( $diff->h * 3600 ) + ( $diff->i * 60 ) + $diff->s;
  $tiempoAdicEnSegundos = $tiempoEnSegundos - ($tiempoEstimadoEnSegundos + $tiempoToleranciaEnSegundos) <= 0 ? 0 : $tiempoEnSegundos - $tiempoEstimadoEnSegundos;
  //$tiempoAdicEnHoras = $tiempoAdicEnSegundos / 3600;
  //echo "TiempoEnSegundos  $tiempoEnSegundos";
  //echo "pagoSegAdicional $pagoSegAdicional  ";
  //$pagoAdic = $tiempoAdicEnSegundos * $pago/$tiempoEstimadoEnSegundos; cambio 180307
  $pagoAdic = round($tiempoAdicEnSegundos * $pagoSegAdicional,2);//cambio 180307
  //echo "TRABAJADOR $idTrab, MASTER $esMaster, PAGO $pago, PAGO ADICIONAL $pagoAdic, MENSAJE $msj ";


  $arreglo = Array(
    "pagoBaseConductor" => $pagoBaseConductor,
    "tiempo"    => $tiempoEstimadoEnSegundos,
    "pago"      => $pago,
    "pagoAdic"  => $pagoAdic,
    "msj"       => $msj,
    "pagoAuxTercero"    => $pagoAuxTercero,
    
  );

  return json_encode($arreglo);

}


function generarDocsTrabaj($db, $dataPost){
  
  $fchDespacho = $dataPost['fchDespacho']; 
  $correlativo = $dataPost['correlativo'];
  $idTrab = $dataPost['idTrab'];
  $auxTipoRol = $dataPost['rol'];
  $tipoRol = strtok($auxTipoRol, "/");

  $fchIni = $dataPost['fchIni'];
  $hraIni = $dataPost['hraIni'];
  $fchFin = $dataPost['fchFin'];
  $hraFin = $dataPost['hraFin'];
  $valorRol = $dataPost['valorRol'];
  $valorHraExtra = $dataPost['valorHraExtra'];
  $valorAdic = $dataPost['valorAdic'];
  $observPersonal = $dataPost['observPersonal'];
  $esReten = $dataPost["esReten"];
  insertTripulac02($db, $fchDespacho, $correlativo, $idTrab, $valorRol, $tipoRol, $fchFin, $hraIni, $hraFin, $valorAdic, $valorHraExtra, $observPersonal, $esReten);

}

function generarTripulacionVistaPrevia($db, $dataPost){
  $hraInicio = $dataPost['hraInicio'];
  $fchFinCli = $dataPost['fchFinCli'];
  $hraFinCli = $dataPost['hraFinCli'];

  $fchDespacho = $dataPost['fchDespacho']; 
  $correlativo = $dataPost['correlativo'];

  $idConductor = $dataPost['idConductor'];
  $nombConductor = trim(substr($dataPost['nombConductor'],strpos($dataPost['nombConductor'], '-') +1 ) );
  $auxAuxiliares = $dataPost['auxiliares'];
  $reten = $dataPost['reten'];
  $nombReten = $dataPost['nombReten'];

  //echo "CONDUCTOR  $idConductor";

  $arrTripulacion = array();
  if ($idConductor != NULL && $idConductor != '' && $idConductor != '-' ){
    $idConductorAux = $idConductor."-Conductor";
    $arrTripulacion[$idConductorAux]['tipoRol'] = 'Conductor';
    $arrTripulacion[$idConductorAux]['nombre'] = $nombConductor;
  }

  $auxAuxiliares = preg_replace( '/\n/', '@', $auxAuxiliares );
  $nroAuxiliares = 0;
  $dataAuxiliar = strtok($auxAuxiliares, "@");
  while ( $dataAuxiliar != "") {
    $nroAuxiliares++;
    $dni = substr($dataAuxiliar,0, strpos($dataAuxiliar,"-"))."-Auxiliar";
    $arrTripulacion[$dni]['tipoRol'] = 'Auxiliar';
    $arrTripulacion[$dni]['nombre'] = trim(substr($dataAuxiliar,strpos($dataAuxiliar, '-') +1 ) );
    $dataAuxiliar = strtok("@");
  }

  if ($reten != NULL && $reten != ''){
    $arrTripulacion[$reten.'-Auxiliar']['tipoRol'] = 'Auxiliar';
    $arrTripulacion[$reten.'-Auxiliar']['nombre'] =
    trim(substr($nombReten,strpos($nombReten, '-') +1 ) );
  }

  /*
  echo "<pre>";
  print_r($arrTripulacion);
  echo "</pre>";
  */  

  $datosTripulacionAlmacenada = buscarTodosInvolucrados($db,$fchDespacho, $correlativo);
  $losAuxiliares = $cadTripulacion = "";
  $totAuxiliares = $totValores = 0;

  /*Ya nose utliza esto para la hra extra
  $datosHrasExtra = buscarSiHayHraExtra($db,$fchDespacho, $correlativo);
  if (count($datosHrasExtra)){
    foreach ($datosHrasExtra as $key => $value) {
      $idTrabajador = $value['idTrabajador'];
      $arrHraExtra[$idTrabajador] = $value['monto'];
    }
  }
  */


  $cadTripulacion = "<tr><th width = '65'>Id Trabaj</th><th width = '120'>Nombre</th><th width = '65'>Rol</th><th width = '85'>FchIni/HraIni</th><th width = '100'>FchFin/HraFin</th><th width = '115'>Valor/ Hra. Extra/ Val.Adic</th><th>Observación</th></tr>";

  foreach ($datosTripulacionAlmacenada as $key => $value) {
    //echo "idTrab :".$value['idTrabajador']."  tipoRol ".$value['tipoRol']." val adicional  ".$value['valorAdicional'];
    //Ya no se almacena aqui la hra extra if(!isset($arrHraExtra[$value['idTrabajador']])) $arrHraExtra[$value['idTrabajador']] = 0;
    if ($value['tipoRol'] == 'Conductor' ){
      $idConductor = $value['idTrabajador'];
      $nombConductor = $value['nombreCompleto'];
    } elseif ($value['tipoRol'] == 'Auxiliar') {
      $losAuxiliares .= $value['idTrabajador']."-".$value['nombreCompleto']."\n" ;
    }

    if ( !(isset($arrTripulacion[$value['idTrabajador'].'-'.$value['tipoRol']]['tipoRol']) && $arrTripulacion[$value['idTrabajador'].'-'.$value['tipoRol']]['tipoRol'] == $value['tipoRol']) ) continue;
    unset($arrTripulacion[$value['idTrabajador'].'-'.$value['tipoRol']]);

    if ($value['trabHraInicio'] == NULL)
      $trabHraIni = $hraInicio;
    else
      $trabHraIni = $value['trabHraInicio'];

    if ($value['trabFchDespachoFin'] == '0000-00-00')
      $trabFchDespachoFin = $fchFinCli;
    else
      $trabFchDespachoFin = $value['trabFchDespachoFin'];

    if ($value['trabHraFin'] == NULL)
      $trabHraFin = $hraFinCli;
    else
      $trabHraFin = $value['trabHraFin'];


    if ($value['tipoRol'] == 'Auxiliar') $totAuxiliares++;
    $totValores += $value['valorRol'] + $value['valorAdicional'];
    $cadTripulacion .= "<tr><td>".$value['idTrabajador']."</td><td>".$value['nombreCompleto']."</td><td>".$value['tipoRol']."/".$value['categTrabajador']."</td>";

    $cadTripulacion .= "<td><input type='text'  class = 'fchIni' name='vistaFchIni".$value['tipoRol'].$value['idTrabajador']."' id = 'vistaFchIni".$value['tipoRol'].$value['idTrabajador']."' value ='".$value['fchDespacho']."' readonly size = '10'><input class = 'hraIni' type='text' name='vistaHraIni".$value['tipoRol'].$value['idTrabajador']."' id = 'vistaHraIni".$value['tipoRol'].$value['idTrabajador']."' value ='".$trabHraIni."' readonly size = '7'><img id='imgEditHraIni".$value['tipoRol'].$value['idTrabajador']."' border='0' src='imagenes/candadoCerrado.png' width='9' height='11' ></td>";

    $cadTripulacion .= "<td><input type='text' class = 'fchFin' name='vistaFchFin".$value['tipoRol'].$value['idTrabajador']."' id = 'vistaFchFin".$value['tipoRol'].$value['idTrabajador']."' value ='".$trabFchDespachoFin."' readonly size = '10'><img id='imgEditFchFin".$value['tipoRol'].$value['idTrabajador']."' border='0' src='imagenes/candadoCerrado.png' width='9' height='11' ><input type='text' class = 'hraFin' name='vistaHraFin".$value['tipoRol'].$value['idTrabajador']."' id = 'vistaHraFin".$value['tipoRol'].$value['idTrabajador']."' value ='".$trabHraFin."' readonly size = '7'><img id='imgEditHraFin".$value['tipoRol'].$value['idTrabajador']."' border='0' src='imagenes/candadoCerrado.png' width='9' height='11' ></td>";

    $cadTripulacion .= "<td><input type='text'  class = 'sinTitulo valorRol' Title = 'Valor Rol'  name= 'vistaValorRol".$value['tipoRol'].$value['idTrabajador']."' id = 'vistaValorRol".$value['tipoRol'].$value['idTrabajador']."' value ='".$value['valorRol']."' readonly size = '3'><img class = 'imgEditValRol' id='imgEditValRol".$value['tipoRol'].$value['idTrabajador']."' border='0' src='imagenes/candadoCerrado.png' width='9' height='11' ><input type='text'  class = 'sinTitulo valorHraExtra' Title = 'Valor Hra Extra' name= 'vistaValorHraExtra".$value['tipoRol'].$value['idTrabajador']."' id = 'vistaValorHraExtra".$value['tipoRol'].$value['idTrabajador']."' value ='".$value['valorHraExtra']."' readonly size = '3'><input type='text'  class = 'sinTitulo valorAdic' Title = 'Valor Adicional' name='vistaValorAdic".$value['tipoRol'].$value['idTrabajador']."' id = 'vistaValorAdic".$value['tipoRol'].$value['idTrabajador']."' value ='".$value['valorAdicional']."' readonly size = '3'><img class = 'imgEditValAdi'  id='imgEditValAdi".$value['tipoRol'].$value['idTrabajador']."' border='0' src='imagenes/candadoCerrado.png' width='9' height='11' ></td>";


    $cadTripulacion .= "<td><textarea  rows='1' cols='50' class = 'observPersonal' name= 'vistaObserv".$value['tipoRol'].$value['idTrabajador']."' id = 'vistaObserv".$value['tipoRol'].$value['idTrabajador']."' readonly></textarea></td></tr>";
  }

  foreach ($arrTripulacion as $key => $registro) {
    if (substr($key,0,2) == "No") continue;
    $key = strtok($key, "-");
    $dataTrabajador = algunosDatosTrabajador($db, $key);
    foreach ($dataTrabajador as $indice => $unTrabajador) {
      $categTrabajador = $unTrabajador['categTrabajador'];
    }
    $totAuxiliares++;
    
    $cadTripulacion .= "<tr><td>".$key."</td><td>".$registro['nombre']."</td><td>".$registro['tipoRol']."/".$categTrabajador."</td>";

    $cadTripulacion .= "<td><input type='text' class = 'fchIni' name='vistaFchIni".$registro['tipoRol'].$key."' id = 'vistaFchIni".$registro['tipoRol'].$key."' value ='".$fchDespacho."' readonly size = '10'><input class = 'hraIni' type='text' name='vistaHraIni".$registro['tipoRol'].$key."' id = 'vistaHraIni".$registro['tipoRol'].$key."' value ='$hraInicio' readonly size = '7'><img id='imgEditHraIni".$registro['tipoRol'].$key."' border='0' src='imagenes/candadoCerrado.png' width='9' height='11' ></td>";

    $cadTripulacion .= "<td><input type='text' class = 'fchFin' name='vistaFchFin".$registro['tipoRol'].$key."' id = 'vistaFchFin".$registro['tipoRol'].$key."' value ='$fchFinCli' readonly size = '10'><img id='imgEditFchFin".$registro['tipoRol'].$key."' border='0' src='imagenes/candadoCerrado.png' width='9' height='11' ><input type='text' class = 'hraFin' name='vistaHraFin".$registro['tipoRol'].$key."' id = 'vistaHraFin".$registro['tipoRol'].$key."' value ='$hraFinCli' readonly size = '7'><img id='imgEditHraFin".$registro['tipoRol'].$key."' border='0' src='imagenes/candadoCerrado.png' width='9' height='11' ></td>";

    $cadTripulacion .= "<td><input  class = 'sinTitulo valorRol' Title = 'Valor Rol' type='text' name= 'vistaValorRol".$registro['tipoRol'].$key."' id = 'vistaValorRol".$registro['tipoRol'].$key."' value ='0' readonly size = '3'><img  class = 'imgEditValRol' id='imgEditValRol".$registro['tipoRol'].$key."' border='0' src='imagenes/candadoCerrado.png' width='9' height='11' ><input type='text'  class = 'sinTitulo valorHraExtra' Title = 'Valor Hra Extra' name= 'vistaValorHraExtra".$registro['tipoRol'].$key."' id = 'vistaValorHraExtra".$registro['tipoRol'].$key."' value ='0' readonly size = '3'><input type='text' class = 'sinTitulo valorAdic' Title = 'Valor Adicional' name='vistaValorAdic".$registro['tipoRol'].$key."' id = 'vistaValorAdic".$registro['tipoRol'].$key."' value ='0' readonly size = '3'><img class = 'imgEditValAdi' id='imgEditValAdi".$registro['tipoRol'].$key."' border='0' src='imagenes/candadoCerrado.png' width='9' height='11' ></td>";

    $cadTripulacion .= "<td><textarea  rows='1' cols='50' class = 'observPersonal' name= 'vistaObserv".$registro['tipoRol'].$key."' id = 'vistaObserv".$registro['tipoRol'].$key."' readonly></textarea></td></tr>";

  }

  $cadTripulacion .= "<tr><td colspan = '3'>TOTAL DE AUXILIARES (Incluye Retén)</td><td><input name = 'totAuxiliares' id = 'totAuxiliares' value = '".$totAuxiliares."' readonly size = '4'></td><td>TOTAL VALORES</td><td>  <input type='text' name= 'totValores' id = 'totValores' value ='".$totValores."' readonly size = '6'></td></tr>";

  return $cadTripulacion;
}

  function buscarSiHayHraExtra($db,$fchDespacho, $correlativo){
    $cadena = $fchDespacho."-".$correlativo;
    $consulta = $db->prepare("SELECT idTrabajador, monto, entregado, fchQuincena FROM `prestamo` WHERE `tipoItem` LIKE 'hraExtra' AND descripcion LIKE '%$cadena' AND fchPago = '$fchDespacho' ");
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function insertaHraExtraEnPrestamo($db, $idTrabajador, $fchDespacho, $correlativo, $valorHraExtra, $observPersonal){
    $usuario = $_SESSION['usuario'];
    $cadena = "Servicio $fchDespacho-$correlativo";

    //echo "$idTrabajador, $fchDespacho, $correlativo, $valorHraExtra, $observPersonal";

    $inserta = $db->prepare("INSERT INTO `prestamo` (`idTrabajador`, `descripcion`, `tipoItem`,  `monto`, `nroCuotas`, `entregado`, `fchPago`, `usuario`, `fchCreacion`, `hraCreacion`, `usuarioUltimoCambio`, `fchUltimoCambio`, `ultProceso`, `ultProcesoFch`, `ultProcesoUsuario`) VALUES (:idTrabajador, '$cadena', 'HraExtra', :valorHraExtra, '1', 'No', :fchDespacho, :usuario, curdate(), curtime(), :usuario, curdate(), :observPersonal , curdate(), :usuario);");

    $inserta->bindParam(':idTrabajador',$idTrabajador);
    $inserta->bindParam(':valorHraExtra',$valorHraExtra);
    $inserta->bindParam(':fchDespacho',$fchDespacho);    
    $inserta->bindParam(':usuario',$usuario);
    $inserta->bindParam(':observPersonal',$observPersonal);
    $inserta->execute();
  }

  function buscarCuentaProducto($db,$dato,$idCli){
    $datos = array();
    //$consulta = $db->prepare("SELECT concat(idubicacion,'-', descripcion) AS dato FROM `ubicacion` WHERE descripcion LIKE '%$dato%'");

    $consulta = $db->prepare("SELECT concat(concat(clicue.correlativo, '|', clicuepro.idProducto),'|', nombreCuenta,'|', clicuepro.nombProducto, '|', clicue.tipoCuenta, '|',clicuepro.precioServ) AS dato  FROM `clientecuentanew` AS clicue , clientecuentaproducto AS clicuepro WHERE clicue.idCliente = clicuepro.idCliente AND clicue.correlativo = clicuepro.correlativo  AND clicue.`idCliente` LIKE '$idCli' AND (nombreCuenta LIKE '%$dato%' OR nombProducto LIKE '%$dato%' OR clicue.tipoCuenta LIKE '%$dato%' )");

    
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => utf8_encode($item['dato']));
    } 
    return $datos;
  }


  function buscarTelefonos($db,$dato){
    $datos = array();
    $consulta = $db->prepare("SELECT `idNroTelefono` AS dato FROM `telefono` WHERE `idNroTelefono` LIKE '%$dato%'");
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => utf8_encode($item['dato']));
    } 
    return $datos;
  }

  function subirAProduccion($db,$dataPost){
    $id =  $dataPost['id'];
    $idProducto = $dataPost['idProducto'];
    $idCliente = $dataPost['idCliente'];
    $fchDespacho = $dataPost['fch'];
    $hraDespacho = $dataPost['hra'];
    $correlCuenta = $dataPost['correlCuenta'];
    $cuenta = $dataPost['cuenta'];
    $tipoServicioPago = $dataPost['tipoCuenta'];
    $movil = $dataPost['movil'];
    $placa = $dataPost['placa'];
    $idConductor = $dataPost['idConductor'];
    $auxAuxiliares = $dataPost['auxAuxiliares'];
    $porcIgv =  $dataPost['porcIgv'];
    $placa = ($placa == "")?NULL:$placa;

    $correlativo = nuevoCorrelativoDespachoAjax($db,$fchDespacho);
    $guia = "";
    $usaReten = "";
    $fchCreacion = Date("Y-m-d");
    $usuario = $_SESSION['usuario'];
    $costoHraExtra = $kmInicio = 0;   //???
    $modoCreacion = "Programacion";
    $dataProducto = buscarDataProductoAjax($db,$idProducto);

    foreach ($dataProducto as $key => $value) {
      $valorServicio = $value['precioServ'];
      $igvServicio = $valorServicio * $porcIgv;
      $topeServicioHraNormal = $value['hrasNormales'];
      $valorConductor = $value['valConductor'];
      $valorAuxiliar = $value['valAuxiliar'];
      $tolerHrasNormales = $value['tolerHrasNormales'];
      $nroAuxiliaresCuenta = $value['nroAuxiliares'];
      $valorAuxAdicional = $value['valAuxiliarAdic'];
      $usarMaster = $value['usoMaster'];
      $recorridoEsperado = $value['kmEsperado'];
      $costoHraExtra = $value['valHraAdic'];

      
    }

    //echo "$fchDespacho, $correlativo, $hraDespacho, $placa, $guia, $valorServicio, $igvServicio, $idCliente, $cuenta, $correlCuenta, $topeServicioHraNormal, $tipoServicioPago, $valorConductor, $valorAuxiliar, $costoHraExtra, $tolerHrasNormales, $nroAuxiliaresCuenta, $valorAuxAdicional, $usarMaster, $recorridoEsperado, $usaReten, $modoCreacion, $kmInicio, $idProducto, $fchCreacion, $usuario";

    $resultado = guardaRegistroDespachoAjax($db, $fchDespacho, $correlativo, $hraDespacho, $placa, $guia, $valorServicio, $igvServicio, $idCliente, $cuenta, $correlCuenta, $topeServicioHraNormal, $tipoServicioPago, $valorConductor, $valorAuxiliar, $costoHraExtra, $tolerHrasNormales, $nroAuxiliaresCuenta, $valorAuxAdicional, $usarMaster, $recorridoEsperado, $usaReten, $modoCreacion, $kmInicio, $idProducto, $fchCreacion, $movil, $usuario);

    if ($resultado == 1){
      if ($tipoServicioPago == 'Normal'){
        guardaRegistroDespachoPersonalAjax($db,$fchDespacho,$correlativo,$idConductor,$valorConductor,'Conductor',$hraDespacho, '00:00:00');
      }
      if ($tipoServicioPago == 'Normal' || $tipoServicioPago == 'SoloPersonal'){
        $auxAuxiliares = preg_replace( '/\n/', '@', $auxAuxiliares );
        $dataAuxiliar = strtok($auxAuxiliares, "@");
        $nroAuxiliares = 0;
        while ( $dataAuxiliar != "") {
          $nroAuxiliares++;
          $dni = substr($dataAuxiliar, 0 , 8);
          //echo "DNI $dni, ";
          $arrAuxiliares[] = $dni;
          $dataAuxiliar = strtok("@");
          guardaRegistroDespachoPersonalAjax($db,$fchDespacho,$correlativo,$dni,$valorAuxiliar,'Auxiliar',$hraDespacho, '00:00:00');
        }
      }
      actualizarEstadoProgram($db,$id,'Procesado');
    }
    return $resultado;
  }

  function buscarPlaca($db,$dato,$estado = "Todos"){
    if ($estado == "Todos") $where = ""; else $where = " AND estado = '$estado' ";

    $datos = array();
    $consulta = $db->prepare("SELECT `idPlaca` AS dato FROM `vehiculo` WHERE `idPlaca`  like '$dato%' $where");
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => utf8_encode($item['dato']));
    } 
    return $datos;
  }

  function buscarConductor($db,$dato,$estado = 'Todos'){
    if ($estado == "Todos") $where = ""; else $where = " AND estadoTrabajador = '$estado' ";
    $datos = array();
    $consulta = $db->prepare("SELECT concat(`idTrabajador`,'-',`apPaterno`,' ',`apMaterno`,', ',`nombres`) as dato FROM `trabajador` as dato WHERE `tipoTrabajador` IN ('Conductor','Coordinador')  AND (`apPaterno` LIKE '$dato%' OR `idTrabajador` LIKE '$dato%' OR `nombres` LIKE '$dato%') $where ");
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => utf8_encode($item['dato']));
    }
    return $datos;
  }

  function buscarConductorAbast($db,$dato){
    $datos = array();
    $consulta = $db->prepare("SELECT concat(`idTrabajador`,'-',`apPaterno`,' ',`apMaterno`,', ',`nombres`) as dato FROM `trabajador` as dato WHERE `tipoTrabajador` IN ('Conductor','Auxiliar','Coordinador')  AND (`apPaterno` LIKE '%$dato%' OR `idTrabajador` LIKE '%$dato%' OR `nombres` LIKE '%$dato%') AND estadoTrabajador = 'Activo'");
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => utf8_encode($item['dato']));
    }
    return $datos;
  }

  function buscarCapacitador($db,$dato){
    $datos = array();
    $consulta = $db->prepare("SELECT concat(`idTrabajador`,'-',`apPaterno`,' ',`apMaterno`,', ',`nombres`) as dato FROM `trabajador` as dato WHERE `tipoTrabajador` = 'Administrativo'  AND (`apPaterno` LIKE '%$dato%' OR `idTrabajador` LIKE '%$dato%' OR `nombres` LIKE '%$dato%') AND estadoTrabajador = 'Activo'");
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => utf8_encode($item['dato']));
    }
    return $datos;
  }

  function validarAntesDeGuardar($db,$dataPost){
    $id = $dataPost['id'];
    $auxIdCliente = $dataPost['idCliente'];
    $idCliente = strtok($auxIdCliente, "-");
    $fchDespacho = $dataPost['fchDespacho'];
    $auxIdConductor = $dataPost['idConductor'];
    $idConductor = strtok($auxIdConductor, "-");
    $auxAuxiliares = $dataPost['auxIdAuxiliares'];
    $hraInicioEsperada = $dataPost['hraInicioEsperada'];
    $placa = $dataPost['placa'];
    $correlCuenta = $dataPost['correlCuenta'];
    $idProducto = $dataPost['idProducto'];
    $tipoCuenta = $dataPost['tipoCuenta'];
    $usuAsignado = $dataPost['usuAsignado'];

    $consulta = $db->prepare("SELECT idRuc, nombre, `clicuepro`.idProducto FROM `cliente` LEFT JOIN clientecuentaproducto AS clicuepro ON `cliente`.idRuc = `clicuepro`.idCliente AND `clicuepro`.correlativo = :correlCuenta AND `clicuepro`.idProducto = :idProducto AND `clicuepro`.tipoProducto = :tipoCuenta WHERE `idRuc` LIKE :idCliente ");
    $consulta->bindParam(':idCliente',$idCliente);
    $consulta->bindParam(':tipoCuenta',$tipoCuenta);
    $consulta->bindParam(':idProducto',$idProducto);
    $consulta->bindParam(':correlCuenta',$correlCuenta);
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $bienIdCliente = $bienProducto = "No";
    $bienAuxiliar  = $bienPlaca    = "Si";
    if (count($todo) == 1 ) $bienIdCliente = "Si";
    if (count($todo) == 1 && $todo[0]['idProducto'] != NULL) $bienProducto = "Si";
    $bienConductor = verificarTripulacion($db,$fchDespacho,$hraInicioEsperada,$idConductor,$id);

    //$bienUsuAsignado = "";
    $bienUsuAsignado = verificarUsuarioAsignado($db, $usuAsignado, $idConductor, $auxAuxiliares, $fchDespacho, $hraInicioEsperada);

    //echo "$usuAsignado, $idConductor, <br>$auxAuxiliares<br>, $fchDespacho, $hraInicioEsperada";
    $arrAuxiliares = array();
    $auxAuxiliares = preg_replace( '/\n/', '@', $auxAuxiliares );
    $dataAuxiliar = strtok($auxAuxiliares, "@");
    //echo "<br>$dataAuxiliar";
    while ( $dataAuxiliar != "") {
      $dni = substr($dataAuxiliar,0, strpos($dataAuxiliar, "-"));
      $bienAuxiliar = verificarTripulacion($db,$fchDespacho,$hraInicioEsperada,$dni,$id);
      if ($bienAuxiliar == "No") break;
      $dataAuxiliar = strtok("@");
    }

    if ($tipoCuenta == 'Normal' || $tipoCuenta == 'SoloVehiculo'){
      $consulta = $db->prepare("SELECT count(*) AS cant FROM `programdespacho` WHERE `fchDespacho` = :fchDespacho AND `hraInicioEsperada` = :hraInicioEsperada AND `placa` LIKE :placa AND id != :id ");
      $consulta->bindParam(':fchDespacho',$fchDespacho);
      $consulta->bindParam(':hraInicioEsperada',$hraInicioEsperada);
      $consulta->bindParam(':placa',$placa);
      $consulta->bindParam(':id',$id);
      $consulta->execute();
      $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
      if ( $todo[0]['cant'] > 0 ) $bienPlaca = "No"; else $bienPlaca = "Si";
    }

    //echo "A ver ".$bienUsuAsignado;
    $arreglo = Array(
      "sobreCliente"   => $bienIdCliente,
      "sobreProducto"  => $bienProducto,
      "sobreConductor" => $bienConductor,
      "sobreAuxiliares" => $bienAuxiliar,
      "sobrePlaca" => $bienPlaca,
      "sobreUsuAsignado" => $bienUsuAsignado
    );

/*
    echo "<pre>";
    print_r($arreglo);
    echo "</pre>";
    */

    return json_encode($arreglo);
  }

  function predictivoProgram($db,$dataPost){
    $usuario = $_SESSION['usuario'];
    $where = " 1 ";
    $filtro = $dataPost['filtro'];
    $auxIdCliente = $dataPost['idCliente'];
    $idCliente = strtok($auxIdCliente, "-");
    $fchAnterior = $dataPost['fchAnterior'];
    $fchNueva = $dataPost['fchNueva'];

    if ($filtro == "Todos"){
      $where .= "";
    } else if($filtro == "EnProceso, Confirmado y Procesado"){
      $where .= " AND estadoProgram IN ('EnProceso', 'Confirmado', 'Procesado')";
    } else if($filtro == "Confirmado"){
      $where .= " AND estadoProgram IN ('Confirmado')";
    } else if($filtro == "Procesado"){
      $where .= " AND estadoProgram IN ('Procesado')";
    }

    if ($idCliente != "") $where .= " AND idCliente = '$idCliente' ";
    $where .= "  AND fchDespacho = '$fchAnterior' ";

    $consulta = $db->prepare("SELECT * FROM programdespacho WHERE $where ");
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);

    /*
    echo "<pre>";
    print_r($todo);
    echo "</pre>";
    */

    foreach ($todo as $key => $value) {
      $id      = $value['id'];
      $correlCuenta = $value['correlCuenta'];
      $cuenta  =  $value['cuenta'];
      $idProducto   = $value['idProducto'];
      $nombProducto = $value['nombProducto'];
      $tipoCuenta   = $value['tipoCuenta'];
      $hraInicioEsperada = $value['hraInicioEsperada'];
      $lugarInicio  = $value['lugarInicio']; 
      $valorServicio = $value['valorServicio'];
      $placa = $value['placa'];
      $movilAsignado = $value['movilAsignado'];
      $observacion   = $value['observacion'];
      $idClienteDespacho = $value['idCliente'];
      $usuAsignado = $value['usuAsignado'];
      $estadoProgram = "EnProceso";

      $inserta = $db->prepare("INSERT INTO `programdespacho` ( `fchDespacho`, `idCliente`, `correlCuenta`, `cuenta`, `idProducto`, `nombProducto`, `tipoCuenta`, `hraInicioEsperada`, `lugarInicio`, `valorServicio`, `movilAsignado`, `usuAsignado`, `estadoProgram`, `placa`, `observacion`, `creacUsuario`, `creacFch`) VALUES ( :fchDespacho, :idCliente, :correlCuenta, :cuenta, :idProducto, :nombProducto, :tipoCuenta, :hraInicioEsperada, '', :valorServicio, :movilAsignado, :usuAsignado, :estadoProgram, :placa, :observacion, :usuario, curdate());");

      $inserta->bindParam(':fchDespacho',$fchNueva);
      $inserta->bindParam(':idCliente',$idClienteDespacho);
      $inserta->bindParam(':correlCuenta',$correlCuenta);
      $inserta->bindParam(':cuenta',$cuenta);
      $inserta->bindParam(':idProducto',$idProducto);
      $inserta->bindParam(':nombProducto',$nombProducto);
      $inserta->bindParam(':tipoCuenta',$tipoCuenta);
      $inserta->bindParam(':valorServicio',$valorServicio);
      $inserta->bindParam(':hraInicioEsperada',$hraInicioEsperada);
      $inserta->bindParam(':movilAsignado',$movilAsignado);
      $inserta->bindParam(':usuAsignado',$usuAsignado);
      $inserta->bindParam(':estadoProgram',$estadoProgram);
      $inserta->bindParam(':placa',$placa);
      $inserta->bindParam(':observacion',$observacion);
      $inserta->bindParam(':usuario',$usuario);

      $inserta->execute();

      $row = $db->lastInsertId();  //El id que se relaciona con la tripulacion

      $consulta = $db->prepare("SELECT * FROM programdesppers WHERE idProgram = '$id' ");
      $consulta->execute();
      $todoTrip = $consulta->fetchAll(PDO::FETCH_ASSOC);

     /* echo "<pre>";
      print_r($todoTrip);
      echo "</pre>";
*/
      
      foreach ($todoTrip as $key => $item) {
        $idTrabajador = $item['idTrabajador'];
        $valorRol     = $item['valorRol'];
        $valorAdicional = $item['valorAdicional'];
        $tipoRol = $item['tipoRol'];
        $ultProceso = "program predictiva"; 
        $ultProcesoFch = Date("Y-m-d");
        $ultProcesoUsuario =  $usuario;

        //echo "$idTrabajador, $valorRol, $valorAdicional, $tipoRol, $ultProceso, $ultProcesoFch, $ultProcesoUsuario";

        $inserta = $db->prepare("INSERT INTO `programdesppers` (`idProgram`, `idTrabajador`, `valorRol`, `valorAdicional`, `tipoRol`, `ultProceso`, `ultProcesoFch`, `ultProcesoUsuario`) VALUES (:row, :idTrabajador, '0', '0', :tipoRol, :ultProceso, curdate(), :usuario);");
        $inserta->bindParam(':row',$row);
        $inserta->bindParam(':idTrabajador',$idTrabajador);
        $inserta->bindParam(':tipoRol',$tipoRol);
        $inserta->bindParam(':ultProceso',$ultProceso);
        $inserta->bindParam(':usuario',$usuario);
        $inserta->execute();

      }
    }
  }


  function buscarCuenta($db,$dato,$idCli){
    $datos = array();
    //$consulta = $db->prepare("SELECT concat(idubicacion,'-', descripcion) AS dato FROM `ubicacion` WHERE descripcion LIKE '%$dato%'");

    $consulta = $db->prepare("SELECT concat(correlativo, '|', nombreCuenta, '|', tipoCuenta) AS dato  FROM `clientecuentanew`  WHERE `idCliente` LIKE '$idCli' AND (nombreCuenta LIKE '%$dato%' OR tipoCuenta LIKE '%$dato%' )");

    
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => utf8_encode($item['dato']));
    } 
    return $datos;
  }


  //buscarCuenta($db,$_GET['term'],$_GET['idCli'],$_GET['idCuenta'])
  function buscarProducto($db,$dato,$idCli, $idCuenta){
    $datos = array();
    $consulta = $db->prepare("SELECT concat(idProducto, '|', nombProducto, '|', precioServ) AS dato  FROM `clientecuentaproducto`  WHERE estadoProducto = 'Activo' AND `idCliente` LIKE '$idCli' ANd correlativo = '$idCuenta' AND nombProducto LIKE '%$dato%' ");
    
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => utf8_encode($item['dato']));
    } 
    return $datos;
  }

  function creaEditaRegProgram($db,$dataPost){
    $usuario = $_SESSION['usuario'];
    $id = $dataPost['id'];
    $idCliente  = $dataPost['idCliente'];
    $idProducto = $dataPost['idProducto'];
    $fchDespacho = $dataPost['fchDespacho'];
    $hraInicioEsperada = $dataPost['hraIniEsperada'];
    //$movilAsignado = $dataPost['movilAsignado'];
    $nombProducto = $dataPost['nombProducto'];
    $valorServicio = $dataPost['precioServ'];
    $idConductor = $dataPost['idConductor'];
    $correlCuenta = $dataPost['correlCuenta'];
    $cuenta = $dataPost['nombCuenta'];
    $tipoCuenta = $dataPost['tipoCuenta'];
    $placa =  $dataPost['placa'];
    $estadoProgram = $dataPost['estadoProgram'];
    $auxAuxiliares = $dataPost['auxAuxiliares'];
    $observacion = utf8_decode($dataPost['observacion']);
    $usuAsignado = $dataPost['usuAsignado'];

    $consulta = $db->prepare("SELECT idNroTelefono FROM telefono WHERE conductor LIKE '$idConductor%' ");
    $consulta->execute();
    $auxTelfAsig = $consulta->fetch();

    $movilAsignado = $auxTelfAsig["idNroTelefono"];

    if($movilAsignado === NULL) $movilAsignado = "";

    //echo "MOVIL  $movilAsignado";


    
    if ($tipoCuenta == "SoloPersonal") $placa = "";

    if ($id == 'Lo genera el sistema'){
      //echo "id $id , idCliente $idCliente, idProducto $idProducto , fchDespacho $fchDespacho , hraInicioEsperada $hraInicioEsperada , usuAsignado $usuAsignado , movilAsignado $movilAsignado , nombProducto $nombProducto , valorServicio $valorServicio , idConductor $idConductor , correlCuenta $correlCuenta , cuenta $cuenta , tipoCuenta $tipoCuenta , placa $placa, estadoProgram $estadoProgram , $auxAuxiliares, $observacion, $usuario"; 

      $inserta = $db->prepare("INSERT INTO `programdespacho` ( `fchDespacho`, `idCliente`, `correlCuenta`, `cuenta`, `idProducto`, `nombProducto`, `tipoCuenta`, `hraInicioEsperada`, `lugarInicio`, `valorServicio`, `movilAsignado`, `usuAsignado`, `estadoProgram`, `placa`, `observacion`, `creacUsuario`, `creacFch`) VALUES ( :fchDespacho, :idCliente, :correlCuenta, :cuenta, :idProducto, :nombProducto, :tipoCuenta, :hraInicioEsperada, '', :valorServicio, :movilAsignado, :usuAsignado, :estadoProgram, :placa, :observacion, :usuario, curdate());");

      $inserta->bindParam(':fchDespacho',$fchDespacho);
      $inserta->bindParam(':idCliente',$idCliente);
      $inserta->bindParam(':correlCuenta',$correlCuenta);
      $inserta->bindParam(':cuenta',$cuenta);
      $inserta->bindParam(':idProducto',$idProducto);
      $inserta->bindParam(':nombProducto',$nombProducto);
      $inserta->bindParam(':tipoCuenta',$tipoCuenta);
      $inserta->bindParam(':valorServicio',$valorServicio);
      $inserta->bindParam(':hraInicioEsperada',$hraInicioEsperada);
      $inserta->bindParam(':movilAsignado',$movilAsignado);
      $inserta->bindParam(':usuAsignado',$usuAsignado);
      $inserta->bindParam(':estadoProgram',$estadoProgram);
      $inserta->bindParam(':placa',$placa);
      $inserta->bindParam(':observacion',$observacion);
      $inserta->bindParam(':usuario',$usuario);
      $inserta->execute();

      if ($inserta->rowCount() == 1) $devolver = 1;

      $row = $db->lastInsertId(); //Para relacionarlo con el despacho

      if($idConductor != ""){
        $tipoRol = 'Conductor';
        $inserta = $db->prepare("INSERT INTO `programdesppers` (`idProgram`, `idTrabajador`, `valorRol`, `valorAdicional`, `tipoRol`, `ultProceso`, `ultProcesoFch`, `ultProcesoUsuario`) VALUES (:row, :idTrabajador, '0', '0', :tipoRol, '', curdate(), :usuario);");
        $inserta->bindParam(':row',$row);
        $inserta->bindParam(':idTrabajador',$idConductor);
        $inserta->bindParam(':tipoRol',$tipoRol);
        $inserta->bindParam(':usuario',$usuario);
        $inserta->execute();
      }

      $arrAuxiliares = array();
      $auxAuxiliares = preg_replace( '/\n/', '@', $auxAuxiliares );
      $dataAuxiliar = strtok($auxAuxiliares, "@");
      while ( $dataAuxiliar != "") {
        $dni = substr($dataAuxiliar,0, strpos($dataAuxiliar, "-"));
        $tipoRol = 'Auxiliar';
        $inserta = $db->prepare("INSERT INTO `programdesppers` (`idProgram`, `idTrabajador`, `valorRol`, `valorAdicional`, `tipoRol`, `ultProceso`, `ultProcesoFch`, `ultProcesoUsuario`) VALUES (:row, :idTrabajador, '0', '0', :tipoRol, '', curdate(), :usuario);");

        $inserta->bindParam(':row',$row);
        $inserta->bindParam(':idTrabajador',$dni);
        $inserta->bindParam(':tipoRol',$tipoRol);
        $inserta->bindParam(':usuario',$usuario);
        $inserta->execute();
        $dataAuxiliar = strtok("@");
      }

    } else {

      $actualizaData = "";
      $actualizaData .=  isset($_POST['placa'])? ", `placa` = :placa ":"";

      $actualiza = $db->prepare("UPDATE `programdespacho` SET fchDespacho = :fchDespacho, `idCliente` = :idCliente, `correlCuenta` = :correlCuenta,  `cuenta` = :cuenta,  `idProducto` = :idProducto, `nombProducto` = :nombProducto, `hraInicioEsperada` = :hraInicioEsperada, `movilAsignado` = :movilAsignado,  `usuAsignado` = :usuAsignado, `estadoProgram` = :estadoProgram , `tipoCuenta` = :tipoCuenta, `observacion` = :observacion, `valorServicio` = :valorServicio, editaUsuario = :editaUsuario, editaFch = curdate() $actualizaData WHERE `id` = :id;");

      $actualiza->bindParam(':id',$id);
      $actualiza->bindParam(':fchDespacho',$fchDespacho);
      $actualiza->bindParam(':idCliente',$idCliente);
      $actualiza->bindParam(':correlCuenta',$correlCuenta);
      $actualiza->bindParam(':cuenta',$cuenta);
      $actualiza->bindParam(':idProducto',$idProducto);
      $actualiza->bindParam(':nombProducto',$nombProducto);
      $actualiza->bindParam(':hraInicioEsperada',$hraInicioEsperada);
      $actualiza->bindParam(':movilAsignado',$movilAsignado);
      $actualiza->bindParam(':usuAsignado',$usuAsignado);
      $actualiza->bindParam(':estadoProgram',$estadoProgram);
      if ( isset($placa)) $actualiza->bindParam(':placa',$placa);
      $actualiza->bindParam(':tipoCuenta',$tipoCuenta);
      $actualiza->bindParam(':valorServicio',$valorServicio);
      $actualiza->bindParam(':observacion',$observacion);
      $actualiza->bindParam(':editaUsuario',$usuario);
      $actualiza->execute();

      if ($actualiza->rowCount() == 1) $devolver = 2; 

      $row = $id;

      $elimina = $db->prepare("DELETE FROM `programdesppers` WHERE `idProgram` = :idProgram AND tipoRol = 'Conductor' ");
      $elimina->bindParam(':idProgram',$row);
      $elimina->execute();

      $elimina = $db->prepare("DELETE FROM `programdesppers` WHERE `idProgram` = :idProgram AND tipoRol = 'Auxiliar' ");
      $elimina->bindParam(':idProgram',$row);
      $elimina->execute();

      if (isset($idConductor)){
        $tipoRol = 'Conductor';
        $inserta = $db->prepare("INSERT INTO `programdesppers` (`idProgram`, `idTrabajador`, `valorRol`, `valorAdicional`, `tipoRol`, `ultProceso`, `ultProcesoFch`, `ultProcesoUsuario`) VALUES (:row, :idTrabajador, '0', '0', :tipoRol, '', curdate(), :usuario);");
        $inserta->bindParam(':row',$row);
        $inserta->bindParam(':idTrabajador',$idConductor);
        $inserta->bindParam(':tipoRol',$tipoRol);
        $inserta->bindParam(':usuario',$usuario);
        $inserta->execute();
      }

      if (isset($auxAuxiliares)){
        $auxAuxiliares = preg_replace( '/\n/', '@', $auxAuxiliares );
        $dataAuxiliar = strtok($auxAuxiliares, "@");
        while ( $dataAuxiliar != "") {
          $dni = substr($dataAuxiliar,0, strpos($dataAuxiliar, "-"));
          $tipoRol = 'Auxiliar';
          $inserta = $db->prepare("INSERT INTO `programdesppers` (`idProgram`, `idTrabajador`, `valorRol`, `valorAdicional`, `tipoRol`, `ultProceso`, `ultProcesoFch`, `ultProcesoUsuario`) VALUES (:row, :idTrabajador, '0', '0', :tipoRol, '', curdate(), :usuario);");
          $inserta->bindParam(':row',$row);
          $inserta->bindParam(':idTrabajador',$dni);
          $inserta->bindParam(':tipoRol',$tipoRol);
          $inserta->bindParam(':usuario',$usuario);
          $inserta->execute();
          $dataAuxiliar = strtok("@");
        }
      }
    }
    return $devolver;
  }

  function buscarRegProgram($db,$dataPost){
    $id = $dataPost['id'];

    $consulta = $db->prepare("SELECT  `id`, `fchDespacho`, `idCliente`, `correlCuenta`, `cuenta`, `idProducto`, `nombProducto`, `tipoCuenta`, `hraInicioEsperada`, `lugarInicio`, `valorServicio`, `placa`, `usuAsignado`, `movilAsignado`, `observacion`, `estadoProgram`, `creacFch` FROM programdespacho WHERE id = :id");

    $consulta->bindParam(':id',$id);
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $arreglo = Array(
      "fchDespacho"  => $todo[0]['fchDespacho'],
      "idCliente"    => $todo[0]['idCliente'],
      "correlCuenta" => $todo[0]['correlCuenta'],
      "cuenta"       => $todo[0]['cuenta'],
      "idProducto"   => $todo[0]['idProducto'],
      "nombProducto" => $todo[0]['nombProducto'],
      "tipoCuenta"   => $todo[0]['tipoCuenta'],
      "hraInicioEsperada" => $todo[0]['hraInicioEsperada'],
      "lugarInicio"  => $todo[0]['lugarInicio'],
      "valorServicio"     => $todo[0]['valorServicio'],
      "placa" => $todo[0]['placa'],
      "usuAsignado" => $todo[0]['usuAsignado'],      
      "movilAsignado" => $todo[0]['movilAsignado'],
      "observacion"  => utf8_encode($todo[0]['observacion']),
      "estadoProgram" => $todo[0]['estadoProgram'],
      "creacFch"     => $todo[0]['creacFch'],
    );

    return json_encode($arreglo);

  }

  function subirAProduccion2($db,$dataPost){
    
    require '../librerias/PHPMailer6_0_5/src/Exception.php';
    require '../librerias/PHPMailer6_0_5/src/PHPMailer.php';
    require '../librerias/PHPMailer6_0_5/src/SMTP.php';

    $usuario = $_SESSION['usuario'];
    $porcIgv =  $dataPost['porcIgv'];
    $esAdmin =  $dataPost['esAdmin'];
    $email =  $dataPost['email'];

    if($esAdmin == "No") $where = " AND creacUsuario = '$usuario' "; else $where = "";

    $consulta = $db->prepare("SELECT `id`, `fchDespacho`, `idCliente`, `correlCuenta`, `cuenta`, `idProducto`, `nombProducto`, `tipoCuenta`, `hraInicioEsperada`, `lugarInicio`, `valorServicio`, `placa`, `movilAsignado`, `usuAsignado`, `observacion`, `estadoProgram`, `creacFch` FROM `programdespacho` WHERE `fchDespacho` >= curdate() AND `estadoProgram` LIKE 'Confirmado' $where ");

    $consulta->execute();
    $despachos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $cadenaProduccion = "<table  width = '700'><tr><th width = '120'>Fch Despacho</th><th width = '55'>Correl</th><th width = '80'>Placa</th><th width = '120'>Cliente</th><th>Cuenta</th></tr>";

    foreach ($despachos as $key => $unDespacho) {
      $id = $unDespacho['id'];
      $fchDespacho = $unDespacho['fchDespacho'];
      $correlativo = nuevoCorrelativoDespachoAjax($db,$fchDespacho);
      $idProducto =  $unDespacho['idProducto'];
      $idCliente =  $unDespacho['idCliente'];
      $placa =  $unDespacho['placa'];
      $cuenta =  $unDespacho['cuenta'];
      $correlCuenta =  $unDespacho['correlCuenta'];
      $tipoServicioPago =  $unDespacho['tipoCuenta'];
      $hraDespacho =  $unDespacho['hraInicioEsperada'];
      $movil =  $unDespacho['movilAsignado'];
      $usuAsignado =  $unDespacho['usuAsignado'];
      $guia = "";
      $usaReten = "";
      $fchCreacion = Date("Y-m-d");
      $usuario = $_SESSION['usuario'];
      $costoHraExtra = $kmInicio = 0;   //???
      $modoCreacion = "Programacion";
      $dataProducto = buscarDataProductoAjax($db,$idProducto);

      $totalRegistros = 0;

      foreach ($dataProducto AS $value) {
        $valorServicio = $value['precioServ'];
        $igvServicio = $valorServicio * $porcIgv;
        $topeServicioHraNormal = $value['hrasNormales'];
        $valorConductor = $value['valConductor'];
        $valorAuxiliar = $value['valAuxiliar'];
        $tolerHrasNormales = $value['tolerHrasNormales'];
        $nroAuxiliaresCuenta = $value['nroAuxiliares'];
        $valorAuxAdicional = $value['valAuxiliarAdic'];
        $usarMaster = $value['usoMaster'];
        $recorridoEsperado = $value['kmEsperado'];
      }

      // echo "$fchDespacho, $correlativo, $hraDespacho, $placa, $guia, $valorServicio, $igvServicio, $idCliente, $cuenta, $correlCuenta, $topeServicioHraNormal, $tipoServicioPago, $valorConductor, $valorAuxiliar, $costoHraExtra, $tolerHrasNormales, $nroAuxiliaresCuenta, $valorAuxAdicional, $usarMaster, $recorridoEsperado, $usaReten, $modoCreacion, $kmInicio, $idProducto, $fchCreacion, $usuario";

      $resultado = guardaRegistroDespachoAjax($db, $fchDespacho, $correlativo, $hraDespacho, $placa, $guia, $valorServicio, $igvServicio, $idCliente, $cuenta, $correlCuenta, $topeServicioHraNormal, $tipoServicioPago, $valorConductor, $valorAuxiliar, $costoHraExtra, $tolerHrasNormales, $nroAuxiliaresCuenta, $valorAuxAdicional, $usarMaster, $recorridoEsperado, $usaReten, $modoCreacion, $kmInicio, $idProducto, $fchCreacion, $movil, $usuAsignado, $usuario, $id);


      if ($resultado == 1){
        $totalRegistros++;

        $consulta2 = $db->prepare("SELECT `idProgram`, `idTrabajador`, `valorRol`, `valorAdicional`, `tipoRol`, `ultProceso`, `ultProcesoFch`, `ultProcesoUsuario`  FROM `programdesppers` WHERE `idProgram` = :id ");
        $consulta2->bindParam(':id',$id);
        $consulta2->execute();
        $tripulacion = $consulta2->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tripulacion AS $regTrip) {
          $idTrip = $regTrip['idTrabajador'];
          $valorRol = $regTrip['valorRol'];
          $tipoRol = $regTrip['tipoRol'];
          
          //guardaRegistroDespachoPersonalAjax($db, $fchDespacho, $correlativo, $idTrip, $valorRol, $tipoRol,$hraDespacho, '00:00:00');
          guardaRegistroDespachoPersonalAjax($db, $fchDespacho, $correlativo, $idTrip, $valorRol, $tipoRol, NULL, NULL);
        }
        actualizarEstadoProgram($db,$id,'Procesado');

        $cadenaProduccion .= "<tr><td>$fchDespacho</td><td>$correlativo</td><td>$placa</td><td>$idCliente</td><td>$cuenta</td></tr>";

      }
    }
    $cadenaProduccion .= "</table>";

    
    if ($email != "No"){
      // a random hash will be necessary to send mixed content
      $separator = md5(time());
      $mail = new PHPMailer();
      $mail->IsSMTP();
      $mail->SMTPAuth = true;
      $mail->SMTPAutoTLS = false;
      $mail->Host = "127.0.0.1";
      $mail->Port = 25;
      $mail->Username = "alertas@inversionesmoy.com.pe";
      $mail->Password = "Alertas20297421035";
      $mail->From = "alertas@inversionesmoy.com.pe";
      $mail->FromName = "Alertas";
      $mail->Subject = "Envío Carga de Programación Previa";
      $mail->AddAddress($email,$email);
      $mail->MsgHTML($cadenaProduccion);
      if(!$mail->Send()) {
        $sobreEMail = "Email no pudo ser enviado".$mail->ErrorInfo;
      } else {
        $sobreEMail = "Se envió email de Confirmación";
      }
    } else {
      $sobreEMail = "No tiene email para enviar Confirmación";
    }
    return "Se subieron $totalRegistros. $sobreEMail";

  }

  function validarProveeVehiculoAntesDeGuardar($db,$dataPost){
    $documento = $dataPost['documento'];
    $opcionRegistro =  $dataPost['opcionRegistro'];

    $bienDocumento = "Si"; $nombreProvee = "";
    if ($opcionRegistro == 'Crear'){
      $consulta = $db->prepare("SELECT `documento`, `nombreCompleto`  FROM `vehiculodueno` WHERE `documento` LIKE :documento ");
      $consulta->bindParam(':documento',$documento);
      $consulta->execute();
      $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);

      if (count($todo) == 1 ){
        $bienDocumento = "No";
        $nombreProvee = $todo[0]['nombreCompleto'];
      }
    } 

    $arreglo = Array(
      "bienDocumento"   => $bienDocumento,
      "nombreProvee"  => $nombreProvee,
    );

    return json_encode($arreglo);
  }

  function creaEditaProveeVehiculo($db,$dataPost){
    $usuario = $_SESSION["usuario"];
    $opcionRegistro = $dataPost['opcionRegistro'];
    $nombre = utf8_decode($dataPost['txtNombre']);
    $telefono = $dataPost['txtTelefono'];
    $eMail = $dataPost['txtEmail'];
    $documento = $dataPost['txtId'];
    $tipoDocumento = $dataPost['cmbTipoDocum'];
    $bcoNombre = utf8_decode($dataPost['txtBancoNombre']);
    $bcoNroCta = $dataPost['txtBancoNroCuenta'];
    $bcoCtaTipo = $dataPost['txtBancoCtaTipo'];
    $bcoCtaMoneda = $dataPost['txtBancoCtaMoneda'];
    $ctaDetraccion = $dataPost['txtCtaDetraccion'];
    $estadoTercero = $dataPost['cmbEstado'];
    $clasificacion = $dataPost['cmbClasific'];
    $plxs = $dataPost['plxs'];

    
    if ($opcionRegistro == "Crear"){
      $inserta = $db->prepare("INSERT INTO `vehiculodueno` (`documento`, `nombreCompleto`,`tipoDocumento`, `nroTelefono`, `eMail`, `estadoTercero`, `clasificacion`, `bancoNombre`, `bancoCuentaNro`, `bancoCuentaTipo`, `bancoCuentaMoneda`,`cuentaDetraccion`, `usuario`, `fchCreacion`) VALUES (:documento, :nombreCompleto, :tipoDocumento, :nroTelefono, :eMail, :estadoTercero, :clasificacion, :bcoNombre, :bcoNroCta, :bcoCtaTipo, :bcoCtaMoneda, :ctaDetraccion, :usuario, CURDATE())");
      $inserta->bindParam(':documento',$documento);
      $inserta->bindParam(':nombreCompleto',$nombre);
      $inserta->bindParam(':tipoDocumento',$tipoDocumento);
      $inserta->bindParam(':nroTelefono',$telefono);
      $inserta->bindParam(':eMail',$eMail);
      $inserta->bindParam(':estadoTercero',$estadoTercero);
      $inserta->bindParam(':clasificacion',$clasificacion);
      $inserta->bindParam(':bcoNombre',$bcoNombre);
      $inserta->bindParam(':bcoNroCta',$bcoNroCta);
      $inserta->bindParam(':bcoCtaTipo',$bcoCtaTipo);
      $inserta->bindParam(':bcoCtaMoneda',$bcoCtaMoneda);
      $inserta->bindParam(':ctaDetraccion',$ctaDetraccion);  
      $inserta->bindParam(':usuario',$usuario);
      $inserta->execute();
/*
      if ($_FILES["archAdjunto"]["error"] == 0){
        $auxExtension = explode('.',$_FILES['archAdjunto']['name'] );
        $extension = end($auxExtension);
        $fichaRuc  = "imagenes/data/proveedor/fichaRuc".$documento.".$extension";
        $resultado = @move_uploaded_file($_FILES["archAdjunto"]["tmp_name"],"../".$fichaRuc);
        $actualiza = $db->prepare("UPDATE `vehiculodueno` SET fichaRuc = :fichaRuc WHERE documento = :documento ");
        $actualiza->bindParam(':documento',$documento);
        $actualiza->bindParam(':fichaRuc',$fichaRuc);
        $actualiza->execute();
      }
*/
      /////////////////
      $resultAdjunto = 0;
      if ($_FILES["archAdjunto"]["error"] == 0){
        $permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png","application/pdf");
        $limite_kb = 1000;
        if (in_array($_FILES['archAdjunto']['type'], $permitidos) && $_FILES['archAdjunto']['size'] <= $limite_kb * 1024){
          $auxExtension = explode('.',$_FILES['archAdjunto']['name'] );
          $extension = end($auxExtension);
          $fichaRuc  = "imagenes/data/proveedor/fichaRuc".$documento.".$extension";
          //echo $ruta;
          if (!file_exists($fichaRuc)){
            $resultado = @move_uploaded_file($_FILES["archAdjunto"]["tmp_name"],"../".$fichaRuc);
            //$resultado = @move_uploaded_file($_FILES["imgContrato"]["tmp_name"], $ruta);
            $actualiza = $db->prepare("UPDATE `vehiculodueno` SET fichaRuc = :fichaRuc WHERE documento = :documento ");
            $actualiza->bindParam(':documento',$documento);
            $actualiza->bindParam(':fichaRuc',$fichaRuc);
            $actualiza->execute();
            $resultAdjunto = 1;
          } else {
            $resultAdjunto = -1;
            //return -3; // este archivo existe";
          }
        } else {
          $resultAdjunto = -2;
        }        
      }
      /////////////////
      $response = $inserta->rowCount()."|$resultAdjunto";
      return $response;
    } elseif ($opcionRegistro == 'Editar') {

      //echo "documento: $documento, nombreCompleto: $nombre, nroTelefono: $telefono, eMail: $eMail, estadoTercero: $estadoTercero, clasificacion: $clasificacion, bcoNombre: $bcoNombre, bcoNroCta: $bcoNroCta, bcoCtaTipo: $bcoCtaTipo, bcoCtaMoneda: $bcoCtaMoneda, ctaDetraccion',$ctaDetraccion, usuario: $usuario ";

      $actualiza = $db->prepare("UPDATE `vehiculodueno` SET `nombreCompleto` = :nombre, `tipoDocumento` = :tipoDocumento, `nroTelefono` = :telefono, `eMail` = :eMail, `estadoTercero` = :estadoTercero, `clasificacion` = :clasificacion, `bancoNombre` = :bcoNombre, `bancoCuentaNro` = :bancoCuentaNro, `bancoCuentaTipo` = :bancoCuentaTipo, `bancoCuentaMoneda` = :bancoCuentaMoneda, `cuentaDetraccion` = :cuentaDetraccion, `editaUsuario` = :usuario ,`editaFch` = curdate()  WHERE `documento` = :documento; ");
      $actualiza->bindParam(':documento',$documento);
      $actualiza->bindParam(':nombre',$nombre);
      $actualiza->bindParam(':tipoDocumento',$tipoDocumento);
      $actualiza->bindParam(':telefono',$telefono);
      $actualiza->bindParam(':eMail',$eMail);
      $actualiza->bindParam(':estadoTercero',$estadoTercero);
      $actualiza->bindParam(':clasificacion',$clasificacion);
      $actualiza->bindParam(':bcoNombre',$bcoNombre);
      $actualiza->bindParam(':bancoCuentaNro',$bancoNroCuenta);
      $actualiza->bindParam(':bancoCuentaTipo',$bcoCtaTipo);
      $actualiza->bindParam(':bancoCuentaMoneda',$bcoCtaMoneda);
      $actualiza->bindParam(':cuentaDetraccion',$ctaDetraccion);
      $actualiza->bindParam(':usuario',$usuario);

      $actualiza->execute();
      $nroRegs = $actualiza->rowCount();
      if($nroRegs == 1 && $estadoTercero == 'Inactivado' ){
        $actualiza = $db->prepare("UPDATE vehiculo SET estado = 'Inactivo', usuarioUltimoCambio = :usuario, fchUltimoCambio = curdate() WHERE idPlaca IN ($plxs) ");
        $actualiza->bindParam(':usuario',$usuario);
        $actualiza->execute();
        $auxActualiza = $actualiza->rowCount();
      }

      $resultAdjunto = 0;
      if ($_FILES["archAdjunto"]["error"] == 0){
        $permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png","application/pdf");
        $limite_kb = 1000;
        if (in_array($_FILES['archAdjunto']['type'], $permitidos) && $_FILES['archAdjunto']['size'] <= $limite_kb * 1024){
          $auxExtension = explode('.',$_FILES['archAdjunto']['name'] );
          $extension = end($auxExtension);
          $fichaRuc  = "imagenes/data/proveedor/fichaRuc".$documento.".$extension";
          //echo $ruta;
          if (!file_exists($fichaRuc)){
            $resultado = @move_uploaded_file($_FILES["archAdjunto"]["tmp_name"],"../".$fichaRuc);
            //$resultado = @move_uploaded_file($_FILES["imgContrato"]["tmp_name"], $ruta);
            $actualiza = $db->prepare("UPDATE `vehiculodueno` SET fichaRuc = :fichaRuc WHERE documento = :documento ");
            $actualiza->bindParam(':documento',$documento);
            $actualiza->bindParam(':fichaRuc',$fichaRuc);
            $actualiza->execute();
            $resultAdjunto = 2;
          } else {
            $resultAdjunto = -1;
            //return -3; // este archivo existe";
          }
        } else {
          $resultAdjunto = -2;
        }        
      }
      /////////////////
      if ($nroRegs == 1) $rptaEdita = 2; else $rptaEdita = 0;
      $response = "$rptaEdita|$resultAdjunto";
      return $response;
    }
  }

  function eliminaRegProgram($db, $dataPost){
    $idRegistro = $dataPost['idRegistro'];
    $elimina = $db->prepare(" DELETE FROM `programdespacho` WHERE `id` = :idRegistro ");
    $elimina->bindParam(':idRegistro',$idRegistro);
    $elimina->execute();
    if($elimina->rowCount() == 1){
      $elimPers = $db->prepare(" DELETE FROM `programdesppers` WHERE `idProgram` = :idRegistro ");
      $elimPers->bindParam(':idRegistro',$idRegistro);
      $elimPers->execute();
    }
    return $elimina->rowCount();
  }

  function buscarRegProveeVehiculo($db,$dataPost){
    $documento = $dataPost['documento'];

    $consulta = $db->prepare("SELECT  `documento`, `tipoDocumento`, `nombreCompleto`, `nroTelefono`, `eMail`, `estadoTercero`, `clasificacion`, `bancoNombre`, `bancoCuentaNro`, `bancoCuentaTipo`, `bancoCuentaMoneda`, `cuentaDetraccion`, `fichaRuc`, `usuario`, `fchCreacion` FROM `vehiculodueno` WHERE `documento` LIKE :documento ");
    $consulta->bindParam(':documento',$documento);
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    $cadena = $todo[0]['fichaRuc'];
    //echo "Cadena $cadena";
    if($cadena != ""){
        $nombre = strtok($cadena,".");
        $extension = strtok(".");
        //echo "Extension $extension";
        if ($extension == "pdf" ){ 
          $cadHtml = "<embed src='$cadena' type='application/pdf' width='350' height='250px' /> ";
        } else if ($extension == "jpeg" or $extension == "jpg" or $extension == "gif"  or $extension == "png"  ){
          $cadHtml = "<img src='$cadena' width='350' height='250' >";
        } else {
          $cadHtml = "";
        }
    } else {
      $cadHtml = "";
    }


    $arreglo = Array(
      "nombreProvee"  => utf8_encode($todo[0]['nombreCompleto']),
      "tipoDocumento"  => $todo[0]['tipoDocumento'],
      "nroTelefono"  => $todo[0]['nroTelefono'],
      "eMail"  => $todo[0]['eMail'],
      "estadoTercero"  => $todo[0]['estadoTercero'],
      "clasificacion"  => $todo[0]['clasificacion'],
      "bancoNombre"  => utf8_encode($todo[0]['bancoNombre']),
      "bancoCuentaNro"  => $todo[0]['bancoCuentaNro'],
      "bancoCuentaTipo"  => $todo[0]['bancoCuentaTipo'],
      "bancoCuentaMoneda"  => $todo[0]['bancoCuentaMoneda'],
      "cuentaDetraccion"  => $todo[0]['cuentaDetraccion'],
      "fichaRuc"  => $cadHtml
    );
    return json_encode($arreglo);
  }


  function eliminarProveeVehiculo($db,$dataPost){
    $documento = $dataPost['documento'];    

    $elimina = $db->prepare("DELETE FROM `vehiculodueno` WHERE `documento` = :documento");
    $elimina->bindParam(':documento',$documento);
    $elimina->execute();
    return $elimina->rowCount();
  }

  function validarTelefonoAntesDeGuardar($db,$dataPost){
    $nroTelefono = $dataPost['nroTelefono'];
    $opcionRegistro =  $dataPost['opcionRegistro'];

    $bienNroTelefono = "Si"; $marca = "";
    if ($opcionRegistro == 'Crear'){
      $consulta = $db->prepare("SELECT `idNroTelefono`, `estado`, `fchAdquisicion`, `usuario`, `fchCreacion`, `usuarioUltimoCambio`, `fchUltimoCambio` FROM `telefono`  WHERE `idNroTelefono` LIKE :nroTelefono ");
      $consulta->bindParam(':nroTelefono',$nroTelefono);
      $consulta->execute();
      $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);

      if (count($todo) == 1 ){
        $bienNroTelefono = "No";
      }
    } 

    $arreglo = Array(
      "bienNroTelefono" => $bienNroTelefono
    );

    return json_encode($arreglo);
  }
  
  function creaEditaTelefono($db,$dataPost){
    $usuario = $_SESSION["usuario"];
    $opcionRegistro = $dataPost['opcionRegistro'];
    $nroTelefono = $dataPost['nroTelefono'];
    $considPropio = $dataPost['considPropio'];
    $operador = $dataPost['operador'];
    $plan = $dataPost['plan'];
    $estadoTelf = $dataPost['estadoTelf'];
    $fchEntrega = $dataPost['fchEntrega'];

    if ($opcionRegistro == "Crear"){
      $inserta = $db->prepare("INSERT INTO `telefono` (`idNroTelefono`, `estado`, `considPropio`, `operador`, `plan`,`fchEntrega`,  `usuario`, `fchCreacion`) VALUES (:nroTelefono, :estadoTelf, :considPropio, :operador, :plan, :fchEntrega, :usuario, curdate());");
      $inserta->bindParam(':nroTelefono',$nroTelefono);;
      $inserta->bindParam(':estadoTelf',$estadoTelf);
      $inserta->bindParam(':considPropio',$considPropio);
      $inserta->bindParam(':operador',$operador);
      $inserta->bindParam(':plan',$plan);
      $inserta->bindParam(':fchEntrega',$fchEntrega);
      $inserta->bindParam(':usuario',$usuario);
      $inserta->execute();
      return $inserta->rowCount();
    }
    else if ($opcionRegistro == 'Editar') {
      $actualiza = $db->prepare("UPDATE `telefono` SET  considPropio = :considPropio , operador = :operador, plan = :plan, fchEntrega = :fchEntrega, adicionalTelf = :adicional, `estado` = :estadoTelf,  usuarioUltimoCambio = :usuario, fchUltimoCambio = curdate() WHERE `idNroTelefono` = :nroTelefono; ");
      $actualiza->bindParam(':nroTelefono',$nroTelefono);
      $actualiza->bindParam(':considPropio',$considPropio);
      $actualiza->bindParam(':operador',$operador);
      $actualiza->bindParam(':plan',$plan);
      $actualiza->bindParam(':fchEntrega',$fchEntrega);
      $actualiza->bindParam(':estadoTelf',$estadoTelf);  
      $actualiza->bindParam(':usuario',$usuario);
      $actualiza->execute();
      if ($actualiza->rowCount() == 1) return 2; else return 0;
    }
  }

  function buscarTelefono($db,$dataPost){
    $nroTelefono = $dataPost['nroTelefono'];
    $consulta = $db->prepare("SELECT `idNroTelefono`, `estado`, `considPropio`, `operador`, `plan`, `fchEntrega`, `usuario`, `fchCreacion`, `usuarioUltimoCambio`, `fchUltimoCambio` FROM `telefono` WHERE `idNroTelefono` LIKE :nroTelefono ");
    $consulta->bindParam(':nroTelefono',$nroTelefono);
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $arreglo = Array(
      "nroTelefono"  => $todo[0]['idNroTelefono'],
      "operador"  => $todo[0]['operador'],
      "plan"  => $todo[0]['plan'],
      "fchEntrega"  => $todo[0]['fchEntrega'],
      "estado"  => $todo[0]['estado']
    );
    return json_encode($arreglo);
  }

  function eliminaTelefono($db, $dataPost){
    $nroTelefono = $dataPost['nroTelefono'];
    $elimina = $db->prepare(" DELETE FROM `telefono` WHERE `idNroTelefono` = :nroTelefono ");
    $elimina->bindParam(':nroTelefono',$nroTelefono);
    $elimina->execute();
    return $elimina->rowCount();
  }


  function nuevaSuperCuenta($db,$dataPost){
    $usuario = $_SESSION["usuario"];
    $idCliente = $dataPost['idCliente'];
    $superCuenta = utf8_decode($dataPost['superCuenta']);

    $inserta = $db->prepare("INSERT INTO `clientesupercuenta` (`idCliente`, `superCuenta`, `estado`, `creacFch`, `creacUsuario`) VALUES (:idCliente, :superCuenta, 'Activo', curdate(), :usuario) ");
    $inserta->bindParam(':idCliente',$idCliente);
    $inserta->bindParam(':superCuenta',$superCuenta);  
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }


  function editaSuperCuenta($db,$dataPost){
    $usuario = $_SESSION["usuario"];
    $idCliente = $dataPost['idCliente'];
    $superCuenta = utf8_decode($dataPost['superCuenta']);
    $estadoCuenta = $dataPost['estadoCuenta'];

    $actualiza = $db->prepare("UPDATE `clientesupercuenta` SET `estado` = :estadoCuenta, editaFch = curdate(), editaUsuario = :usuario WHERE `idCliente` = :idCliente AND `superCuenta` = :superCuenta ");
    $actualiza->bindParam(':idCliente',$idCliente);
    $actualiza->bindParam(':superCuenta',$superCuenta);
    $actualiza->bindParam(':estadoCuenta',$estadoCuenta);  
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->execute();
    return $actualiza->rowCount();

  }

  function eliminaSuperCuenta($db,$dataPost){
    $idCliente = $dataPost['idCliente'];
    $superCuenta = utf8_decode($dataPost['superCuenta']);
    $elimina = $db->prepare("DELETE FROM  `clientesupercuenta` WHERE`idCliente` = :idCliente AND `superCuenta` = :superCuenta ");
    $elimina->bindParam(':idCliente',$idCliente);
    $elimina->bindParam(':superCuenta',$superCuenta);  
    $elimina->execute();
    return $elimina->rowCount();
  }

  function verificarVehiculosEnUso($db,$dataPost){
    /*echo "<pre>";
    var_dump($dataPost);
    echo "</pre>";*/
    $idProveed = $dataPost["idProveed"];
    $consulta = $db->prepare("SELECT min(des.fchDespacho) AS minimo, max(des.fchDespacho) AS maximo, GROUP_CONCAT(DISTINCT concat(\"'\",veh.idPlaca,\"'\" )) AS placas, count(*) AS cant, sum(if( des.concluido = 'No',1,0 )) AS noConcluidos , sum(if( des.concluido = 'Si',1,0 )) AS concluidos FROM `vehiculo` AS veh LEFT JOIN despacho AS des ON `veh`.`idPlaca` = des.placa AND veh.estado = 'Activo' AND DATEDIFF(curdate(), des.fchDespacho) < 20 WHERE `rznSocial` LIKE :rznSocial GROUP BY rznSocial");
    $consulta->bindParam(':rznSocial',$idProveed);
    $consulta->execute();
    return $consulta->fetch(PDO::FETCH_ASSOC);
    
  }

    function nvoIdEficiencia($db){

    $consulta = $db->prepare("SELECT id  FROM `vehiculoeficsoleskm` ORDER BY `id` DESC LIMIT 1 ");
    $consulta->bindParam(':idProducto',$idProducto); 
    $consulta->execute();
    $data = $consulta->fetchAll();
    $correlOtro = 1;
    foreach ($data as $key => $value) {
      $correlOtro = $value['id'] + 1;
    }
    return $correlOtro;
  }

    function eliminaEfic($db, $dataPost){
    $tipoCombust = $dataPost['tipoCombust'];
    $elimina = $db->prepare(" DELETE FROM `vehiculoeficsoleskm` WHERE `tipoCombust` = :tipoCombust ");
    $elimina->bindParam(':tipoCombust',$tipoCombust);
    $elimina->execute();
    return $elimina->rowCount();
  }

  
  function creaEficiencia($db,$dataPost){
    $usuario = $_SESSION["usuario"]; 
    $id =  nvoIdEficiencia($db); // "120";
    $tipoCombust = utf8_decode($dataPost['tipoCombust']);
    $nombre = utf8_decode($dataPost['nombre']);
    $descripcion = utf8_decode($dataPost['descripcion']);
    $valorEsperado = $dataPost['valorEsperado'];
    $inserta = $db->prepare("INSERT INTO `vehiculoeficsoleskm` (`id`,`tipoCombust`, `nombre`, `descripcion`, `valorEsperado`, `usuario`, `fchCreacion`) VALUES (:id, :tipoCombust, :nombre, :descripcion, :valorEsperado, :usuario, curdate());");
    $inserta->bindParam(':id',$id);
    $inserta->bindParam(':tipoCombust',$tipoCombust);
    $inserta->bindParam(':nombre',$nombre);
    $inserta->bindParam(':descripcion',$descripcion);
    $inserta->bindParam(':valorEsperado',$valorEsperado);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function editarEfic($db,$dataPost) {

    $usuario = $_SESSION["usuario"];
    $id = $dataPost['id'];
    $tipoCombust = $dataPost['tipoCombust'];
    $nombre = utf8_decode($dataPost['nombre']);
    $descripcion = utf8_decode($dataPost['descripcion']);
    $valorEsperado = $dataPost['valorEsperado'];

    $actualiza = $db->prepare("UPDATE `vehiculoeficsoleskm` SET `tipoCombust`= :tipoCombust,  `nombre` = :nombre,  `descripcion` = :descripcion, `valorEsperado` = :valorEsperado, `usuario` = :usuario, `fchCreacion` = curdate() WHERE `id` = :id ");

      $actualiza->bindParam(':id',$id);
      $actualiza->bindParam(':tipoCombust',$tipoCombust);
      $actualiza->bindParam(':nombre',$nombre);
      $actualiza->bindParam(':descripcion',$descripcion);
      $actualiza->bindParam(':valorEsperado',$valorEsperado);
      $actualiza->bindParam(':usuario',$usuario);
      $actualiza->execute();
      return $actualiza->rowCount();
  }

  function buscarUsuAsignables($db,$dato){
    $datos = array();
    $consulta = $db->prepare("SELECT usu.`idUsuario`, `trab`.idTrabajador, `trab`.apPaterno, `trab`.apMaterno, `trab`.nombres, `trab`.tipoTrabajador ,
      concat(usu.`idUsuario`,'|', `trab`.apPaterno, ' ', `trab`.apMaterno, ', ',`trab`.nombres) AS dato FROM `usuario` AS usu, trabajador AS trab WHERE  `usu`.dni = `trab`.idTrabajador AND `trab`.estadoTrabajador = 'Activo' AND usu.`asignDespacho` = 'Si' AND usu.`estado` = 'Activo' AND `usu`.fchVencimiento >= curdate() AND  `tipoTrabajador` IN ('Conductor','Auxiliar','Coordinador')  AND (`apPaterno` LIKE '$dato%' OR `idTrabajador` LIKE '$dato%' OR `nombres` LIKE '$dato%' OR usu.`idUsuario` LIKE '$dato%' ) ");
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => utf8_encode($item['dato']));
    }
    return $datos;

  }

  function buscarIdTrabajador($db,$usuario){
    $consulta = $db->prepare("(SELECT dni FROM usuario WHERE idUsuario = :usuario LIMIT 1)");
    $consulta->bindParam(':usuario',$usuario);
    $consulta->execute();
    $data = $consulta->fetchAll();
    $id = "";
    foreach ($data as $key => $value) {
      $id = $value['dni'];
    }
    return $id;
  }

  function registrarAbast($db,$dataPost) {
    $fecha_actual = date("d") . "-" . date("m") . "-" . date("Y");
    $time = time();
    $hora_actual = date("H:i:s", $time);
    //
    $usuario = $_SESSION["usuario"];
    $fecha = $dataPost['txtFecha'];
    $placa = $dataPost['txtPlaca'];
    $idabastecimiento = buscarIDAbastecimiento($db,$fecha,$placa,"add","");
    $dataConductor = $dataPost['txtConductor'];
    $parts = explode("-", $dataConductor);
    $idConductor = $parts[0];
    $grifo = $dataPost['cmbGrifos'];
    $tipo_combustible = $dataPost['txtTCombustible'];
    $kilometrajeAnt = $dataPost['txtKilometrajeAnt'];
    $kilometrajeAct = $dataPost['txtKilometrajeAct'];
    $und_medida = $dataPost['txtUMedida'];
    $cantidad = $dataPost['txtCantidad'];
    $importe = $dataPost['txtImporte'];
    $idTrabajador = buscarIdTrabajador($db,$usuario);
    $RESULT = "";
    //
    $mes = ["ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SETIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE"][date("n") - 1];
    $anio = date("Y");
    $foto1 = "";
    $foto2 = "";
    if ($_FILES["myPhoto1"]["error"] == 0){
      $auxExtension1 = explode('.',$_FILES['myPhoto1']['name']);
      $extension1 = end($auxExtension1);
      $foto1 = "/ocurrenciamoy/imagenes_incidencias_abastecimiento/$anio/$mes/ODOMETRO_" . $placa . "_" . $fecha_actual .  "_" . $hora_actual . ".$extension1";
      @move_uploaded_file($_FILES["myPhoto1"]["tmp_name"],"../..".$foto1);
    }
    if ($_FILES["myPhoto2"]["error"] == 0){
      $auxExtension2 = explode('.',$_FILES['myPhoto2']['name']);
      $extension2 = end($auxExtension2);
      $foto2 = "/ocurrenciamoy/imagenes_incidencias_abastecimiento/$anio/$mes/COMPROBANTE_" . $placa . "_" . $fecha_actual . "_" . $hora_actual . ".$extension2";
      @move_uploaded_file($_FILES["myPhoto2"]["tmp_name"],"../..".$foto2);
    }

    $inserta = $db->prepare("INSERT INTO `incidencias_abastecimiento` (`fecha`,`placa`, `idConductor`, `grifo`, `tipo_combustible`, `kilometraje_anterior`, `kilometraje_actual`, `und_medida`, `cantidad`, `importe`, `foto1`, `foto2`, `estado`, `modoCreacion`, `idTrabajador`) VALUES (:fecha, :placa, :idConductor, :grifo, :tipo_combustible, :kilometrajeAnt, :kilometrajeAct, :und_medida, :cantidad, :importe, :foto1, :foto2, 'Pendiente', 'Web', :idTrabajador);");
    $inserta->bindParam(':fecha',$fecha);
    $inserta->bindParam(':placa',$placa);
    $inserta->bindParam(':idConductor',$idConductor);
    $inserta->bindParam(':grifo',$grifo);
    $inserta->bindParam(':tipo_combustible',$tipo_combustible);
    $inserta->bindParam(':kilometrajeAnt',$kilometrajeAnt);
    $inserta->bindParam(':kilometrajeAct',$kilometrajeAct);
    $inserta->bindParam(':und_medida',$und_medida);
    $inserta->bindParam(':cantidad',$cantidad);
    $inserta->bindParam(':importe',$importe);
    $inserta->bindParam(':foto1',$foto1);
    $inserta->bindParam(':foto2',$foto2);
    $inserta->bindParam(':idTrabajador',$idTrabajador);
    $inserta->execute();
    $RESULT = $inserta->rowCount();

    if($RESULT == 1 && $idabastecimiento != ""){
      $actualiza = $db->prepare("UPDATE `incidencias_abastecimiento` SET `kilometraje_anterior`= :kilometrajeAct WHERE `idabastecimiento` = :idabastecimiento");
      $actualiza->bindParam(':idabastecimiento',$idabastecimiento);
      $actualiza->bindParam(':kilometrajeAct',$kilometrajeAct);
      $actualiza->execute();
      $RESULT = $actualiza->rowCount();
    }

    return $RESULT;
  }

function buscarIDAbastecimiento($db,$fecha,$placa,$action,$position){
  $id = "";
  if ($action == "add") {

    $consulta = $db->prepare("(SELECT ia1.idabastecimiento, IF(ia1.idabastecimiento IS NULL, '','ARRIBA') AS position FROM `incidencias_abastecimiento` ia1 WHERE ia1.placa = :placa AND ia1.fecha >= :fecha order by ia1.fecha asc limit 1) UNION (SELECT ia2.idabastecimiento, IF(ia2.idabastecimiento IS NULL, '','ABAJO') AS position FROM `incidencias_abastecimiento` ia2 WHERE ia2.placa = :placa AND ia2.fecha < :fecha order by ia2.fecha desc limit 1)");
    $consulta->bindParam(':fecha', $fecha);
    $consulta->bindParam(':placa', $placa);
    $consulta->execute();
    $data = $consulta->fetchAll();

    foreach ($data as $key => $value) {
      if ($value['position'] == "ARRIBA") {
        $id = $value['idabastecimiento'];
      }
    }
  } else if ($action == "edit") {

    $consulta = $db->prepare("(SELECT ia1.idabastecimiento, IF(ia1.idabastecimiento IS NULL, '','ARRIBA') AS position FROM `incidencias_abastecimiento` ia1 WHERE ia1.placa = :placa AND ia1.fecha > :fecha order by ia1.fecha asc limit 1) UNION (SELECT ia2.idabastecimiento, IF(ia2.idabastecimiento IS NULL, '','ABAJO') AS position FROM `incidencias_abastecimiento` ia2 WHERE ia2.placa = :placa AND ia2.fecha < :fecha order by ia2.fecha desc limit 1)");
    $consulta->bindParam(':fecha', $fecha);
    $consulta->bindParam(':placa', $placa);
    $consulta->execute();
    $data = $consulta->fetchAll();

    if ($position == "ambos") {
      foreach ($data as $key => $value) {
        $id .= $value['idabastecimiento'] . "_" . $value['position'] . "_";
      }
    } else if ($position == "arriba") {
      foreach ($data as $key => $value) {
        if ($value['position'] == "ARRIBA") {
          $id = $value['idabastecimiento'];
        }
      }
    } else if ($position == "abajo") {
      foreach ($data as $key => $value) {
        if ($value['position'] == "ABAJO") {
          $id = $value['idabastecimiento'];
        }
      }
    }
  }
  return $id;
  }

  function editar_validarAbast($db,$dataPost) {
    $usuario = $_SESSION["usuario"];
    $idabastecimiento = $dataPost['id'];
    $fecha = $dataPost['fecha'];
    $placa = $dataPost['placa'];
    $kilometrajeActOriginal = $dataPost['kilometrajeActOriginal'];
    $kilometrajeAct = $dataPost['kilometrajeAct'];
    $cantidad = $dataPost['cantidad'];
    $importe = $dataPost['importe'];
    $tipo = $dataPost['tipo'];
    $id = "";

    $estado = "Pendiente";
    if($tipo == "validar"){
      $estado = "Validado";
    }

    $actualiza = $db->prepare("UPDATE `incidencias_abastecimiento` SET `kilometraje_actual` = :kilometrajeAct, `cantidad` = :cantidad, `importe` = :importe, `estado` = :estado, `usuario` = :usuario WHERE `idabastecimiento` = :idabastecimiento");
    $actualiza->bindParam(':idabastecimiento',$idabastecimiento);
    $actualiza->bindParam(':kilometrajeAct',$kilometrajeAct);
    $actualiza->bindParam(':cantidad',$cantidad);
    $actualiza->bindParam(':importe',$importe);
    $actualiza->bindParam(':estado',$estado);
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->execute();

    if($kilometrajeAct != $kilometrajeActOriginal){
      $consulta = $db->prepare("SELECT idabastecimiento FROM `incidencias_abastecimiento` WHERE fecha >= :fecha AND placa = :placa AND kilometraje_anterior = :kilometrajeActOriginal");
      $consulta->bindParam(':fecha', $fecha);
      $consulta->bindParam(':placa', $placa);
      $consulta->bindParam(':kilometrajeActOriginal', $kilometrajeActOriginal);
      $consulta->execute();
      $data = $consulta->fetchAll();

      foreach ($data as $key => $value) {
        $id = $value['idabastecimiento'];
      }
    }

    if($actualiza->rowCount() == 1 && $id != ""){
      $actualiza = $db->prepare("UPDATE `incidencias_abastecimiento` SET `kilometraje_anterior` = :kilometrajeAct, `usuario` = :usuario WHERE `idabastecimiento` = :id");
      $actualiza->bindParam(':id',$id);
      $actualiza->bindParam(':kilometrajeAct',$kilometrajeAct);
      $actualiza->bindParam(':usuario',$usuario);
      $actualiza->execute();
    }
    return $actualiza->rowCount();
  }

  /*function editar_validarAbast($db,$dataPost) {
    $usuario = $_SESSION["usuario"];
    $idabastecimiento = $dataPost['id'];
    $fecha = $dataPost['fecha'];
    $placa = $dataPost['placa'];
    $idConductor = $dataPost['idConductor'];
    $grifo = $dataPost['grifo'];
    $tipo_combustible = $dataPost['tipo_combustible'];
    $kilometrajeAnt = $dataPost['kilometrajeAnt'];
    $kilometrajeAct = $dataPost['kilometrajeAct'];
    $und_medida = $dataPost['und_medida'];
    $cantidad = $dataPost['cantidad'];
    $importe = $dataPost['importe'];
    $position = $dataPost['position'];
    $tipo = $dataPost['tipo'];
    $dataID = buscarIDAbastecimiento($db,$fecha,$placa,"edit",$position);

    $estado = "Pendiente";
    if($tipo == "validar"){
      $estado = "Validado";
    }

    $actualiza = $db->prepare("UPDATE `incidencias_abastecimiento` SET `placa`= :placa, `idConductor`= :idConductor, `grifo`= :grifo, `tipo_combustible`= :tipo_combustible, `kilometraje_anterior` = :kilometrajeAnt, `kilometraje_actual` = :kilometrajeAct, `und_medida` = :und_medida, `cantidad` = :cantidad, `importe` = :importe, `estado` = :estado, `usuario` = :usuario WHERE `idabastecimiento` = :idabastecimiento");
    $actualiza->bindParam(':idabastecimiento',$idabastecimiento);
    $actualiza->bindParam(':placa',$placa);
    $actualiza->bindParam(':idConductor',$idConductor);
    $actualiza->bindParam(':grifo',$grifo);
    $actualiza->bindParam(':tipo_combustible',$tipo_combustible);
    $actualiza->bindParam(':kilometrajeAnt',$kilometrajeAnt);
    $actualiza->bindParam(':kilometrajeAct',$kilometrajeAct);
    $actualiza->bindParam(':und_medida',$und_medida);
    $actualiza->bindParam(':cantidad',$cantidad);
    $actualiza->bindParam(':importe',$importe);
    $actualiza->bindParam(':estado',$estado);
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->execute();

    //
    if($actualiza->rowCount() == 1 && $dataID != ""){
      $partsIdAbastecimiento = explode("_", $dataID);
      $cantVueltas = count($partsIdAbastecimiento) - 1;

      if($position == "ambos"){
        if($cantVueltas == 4){
          $idabastecimiento = $partsIdAbastecimiento[0];
          $actualiza = $db->prepare("UPDATE `incidencias_abastecimiento` SET `kilometraje_anterior`= :kilometrajeAct WHERE `idabastecimiento` = :idabastecimiento");
          $actualiza->bindParam(':idabastecimiento',$idabastecimiento);
          $actualiza->bindParam(':kilometrajeAct',$kilometrajeAct);
          $actualiza->execute();
          $idabastecimiento = $partsIdAbastecimiento[2];
          $actualiza = $db->prepare("UPDATE `incidencias_abastecimiento` SET `kilometraje_actual`= :kilometrajeAnt WHERE `idabastecimiento` = :idabastecimiento");
          $actualiza->bindParam(':idabastecimiento',$idabastecimiento);
          $actualiza->bindParam(':kilometrajeAnt',$kilometrajeAnt);
          $actualiza->execute();
        }
        else if($cantVueltas == 2){
          if($partsIdAbastecimiento[1] == "ARRIBA"){
            $idabastecimiento = $partsIdAbastecimiento[0];
            $actualiza = $db->prepare("UPDATE `incidencias_abastecimiento` SET `kilometraje_anterior`= :kilometrajeAct WHERE `idabastecimiento` = :idabastecimiento");
            $actualiza->bindParam(':idabastecimiento',$idabastecimiento);
            $actualiza->bindParam(':kilometrajeAct',$kilometrajeAct);
            $actualiza->execute();
          }
          if($partsIdAbastecimiento[1] == "ABAJO"){
            $idabastecimiento = $partsIdAbastecimiento[0];
            $actualiza = $db->prepare("UPDATE `incidencias_abastecimiento` SET `kilometraje_actual`= :kilometrajeAnt WHERE `idabastecimiento` = :idabastecimiento");
            $actualiza->bindParam(':idabastecimiento',$idabastecimiento);
            $actualiza->bindParam(':kilometrajeAnt',$kilometrajeAnt);
            $actualiza->execute();
          }
        }
      }
      else if($position == "arriba"){
        $idabastecimiento = $partsIdAbastecimiento[0];
        $actualiza = $db->prepare("UPDATE `incidencias_abastecimiento` SET `kilometraje_anterior`= :kilometrajeAct WHERE `idabastecimiento` = :idabastecimiento");
        $actualiza->bindParam(':idabastecimiento',$idabastecimiento);
        $actualiza->bindParam(':kilometrajeAct',$kilometrajeAct);
        $actualiza->execute();
      }
      else if($position == "abajo"){
        $idabastecimiento = $partsIdAbastecimiento[0];
        $actualiza = $db->prepare("UPDATE `incidencias_abastecimiento` SET `kilometraje_actual`= :kilometrajeAnt WHERE `idabastecimiento` = :idabastecimiento");
        $actualiza->bindParam(':idabastecimiento',$idabastecimiento);
        $actualiza->bindParam(':kilometrajeAnt',$kilometrajeAnt);
        $actualiza->execute();
      }

    }
    //echo $actualiza->rowCount();
    return $actualiza->rowCount();
  }*/

  function buscarTrabajNoQuincena($db,$dato){
    $datos = array();

    $consulta = $db->prepare("SELECT concat(`idTrabajador`,'-',`apPaterno`,' ',`apMaterno`,', ',`nombres`) as dato FROM `trabajador` as dato WHERE estadoQuincena != 'Si' AND   `apPaterno` LIKE '$dato%' OR `idTrabajador` LIKE '$dato%' OR `nombres` LIKE '$dato%'");
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => utf8_encode($item['dato']));
    } 
    return $datos;
  }

  function diferenciaEntreFechas($fchIni, $fchFin){
    $datetime1 = date_create($fchIni);
    $datetime2 = date_create($fchFin);
    //$datetime2 = date_create('201-11-21');
    //$datetime1 = date_create('1963-04-22');
    //$datetime2 = date_create('2021-02-09');
    $interval = date_diff($datetime1, $datetime2);
    
    /*echo "<pre>";
    print_r( $interval );
    echo "</pre>";*/
    $totEnDias = $interval->format('%a');

    $resultado = "";
    if($interval->y == 1)$resultado .= "1 año";
    else if($interval->y > 1)$resultado .= $interval->y." años";  
    if ($interval->m == 1){
      if($resultado != "" ) $resultado .= ", 1 mes";
      else $resultado .= "1 mes";  
    } else if ($interval->m > 1){
      if($resultado != "" ) $resultado .= ", ".$interval->m." meses";
      else $resultado .= $interval->m." meses"; 
    }
    if ($interval->d == 1){
      if($resultado != "" ) $resultado .= ", 1 día";
      else $resultado .= "1 día";  
    } else if ($interval->d > 1){
      if($resultado != "" ) $resultado .= ", ".$interval->d." días";
      else $resultado .= $interval->d." días"; 
    }
    
    $arrIntervalo = array(
      "anhio" => $interval->y,
      "meses"   => $interval->m,
      "dias"   => $interval->d,
      "tpoLaboral" => utf8_encode($resultado),
      "totEnDias" => utf8_encode($totEnDias)
    );

    return $arrIntervalo;
  }

  function calculoSueldoIndemniz($db, $fchInicio, $fchUltDiaTrab, $idTrabajador){
    $diaUlt =  substr($fchUltDiaTrab,8,2);
    if($diaUlt < 29 ){
      $auxFch = strtotime($fchUltDiaTrab."- 1 months");
      $auxFch = Date("Y-m-d", $auxFch);
      $fchUltDiaTrab = substr($auxFch,0,8)."28";
    }
    $date = date($fchUltDiaTrab);
    $fchAux6mesesAntes = strtotime($date."- 5 months");
    $fch6mesesAntes = Date("Y-m-d",$fchAux6mesesAntes);
    $fchInicio = ( $fch6mesesAntes < $fchInicio ) ? $fchInicio : $fch6mesesAntes;
    
    $consulta = $db->prepare("SELECT count(*) AS nroMeses, sum(valor) AS sumTotal, round(avg(valor),2) AS promedTotal FROM ( SELECT substring(`quindetcon`.fchQuincena,1,7), round(sum( if( `plaite`.partidaContab = 'D', valor, -1*valor )),2) AS valor, `plaite`.grupoNro, `plaite`.partidaContab, `quindetcon`.fchQuincena FROM quincenadetallecontab AS quindetcon, planillaitems AS plaite WHERE `quindetcon`.id = `plaite`.id AND `plaite`.grupoNro = '1' AND `quindetcon`.idTrabajador = :idTrabajador AND `quindetcon`.fchQuincena BETWEEN :fchInicio AND :fchUltDiaTrab GROUP BY substring(`quindetcon`.fchQuincena,1,7) ) AS t1");
    $consulta->bindParam(':fchInicio',$fchInicio);
    $consulta->bindParam(':fchUltDiaTrab',$fchUltDiaTrab);
    $consulta->bindParam(':idTrabajador',$idTrabajador);
    $consulta->execute();
    return $consulta->fetch();
  }

  function refrescarDatosBBSS($db,$dataPost){
    $idTrabajador = $dataPost['idTrabajador'];

    //Consulta Altas y Bajas
    $consulta = $db->prepare("SELECT `idTrabajador`, `fchInicio`, `tipo`, `fchUltDiaTrab`, `fchFin`, `regimen`, `duracion`, `creacUsuario`, `creacFch`, `estado`, `observacion` FROM `altasbajas` WHERE `idTrabajador` LIKE :idTrabajador ORDER BY `altasbajas`.`fchInicio` DESC LIMIT 1  ");
    $consulta->bindParam(':idTrabajador',$idTrabajador);
    $consulta->execute();
    $dataAltasyBajas = $consulta->fetch();

    if($dataAltasyBajas["idTrabajador"] == ""){
      $arreglo = Array(
        "fchInicio"  => "No hay datos",
        "fchFin" => "No hay datos",
        "fchUltDiaTrab" => "No hay datos",
        "regimen" => "No hay datos"
      );
    } else {
      $consulta = $db->prepare("SELECT `idTrabajador`, `tipoTrabajador`, `categTrabajador`, `ruc`, `modoSueldo`, `estadoTrabajador`, `remuneracionBasica`, `asignacionFamiliar` FROM `trabajador` WHERE idTrabajador LIKE :idTrabajador LIMIT 1 ");
      $consulta->bindParam(':idTrabajador',$idTrabajador);
      $consulta->execute();
      $dataTrabajador = $consulta->fetch();

      $fchInicio = $dataAltasyBajas["fchInicio"];
      $fchUltDiaTrab = $dataAltasyBajas["fchUltDiaTrab"] == "0000-00-00" ? Date("Y-m-d") : $dataAltasyBajas["fchUltDiaTrab"];
      //echo "$fchInicio, $fchUltDiaTrab";
      $codPlameCTS = "'0904'"; //Para CTS
      $dataCTS = buscarCodPlameEnPlanilla($db, $idTrabajador, $codPlameCTS, $fchInicio, $fchUltDiaTrab);
      $codPlameVac = "'0118'"; //Para Vac. nromales
      $dataVacac = buscarCodPlameEnPlanilla($db, $idTrabajador, $codPlameVac, $fchInicio, $fchUltDiaTrab);
      $codPlameGrat = "'0407', '0313'";//Gratific profesional fiestas patrias
      $dataGratif = buscarCodPlameEnPlanilla($db, $idTrabajador, $codPlameGrat, $fchInicio, $fchUltDiaTrab);
      /*$codPlameGrat02 = "'0313'";//Gratific profesional fiestas patrias
      $dataGratif02 = buscarCodPlameEnPlanilla($db, $idTrabajador, $codPlameGrat02, $fchInicio, $fchUltDiaTrab);*/

      $arrAnhioMesDia = diferenciaEntreFechas($fchInicio,$fchUltDiaTrab);
      $auxSueldoIndemniz = calculoSueldoIndemniz($db, $fchInicio,$fchUltDiaTrab,$idTrabajador);
      $sueldoIndemniz = $auxSueldoIndemniz["promedTotal"];

      //Datos de CTS
      $tblCTS = "<table width = '100%'>";
      $tblCTS .= utf8_encode("<tr><th width = '230px' align = 'center' >Quincena</th><th width = '60px' align = 'center'></th><th>Observación</th></tr>");
      if(count($dataCTS) > 0){
        $suma = 0;
        foreach ($dataCTS as $key => $value) {
          $anhio = substr($value["fchQuincena"],0,4);
          $mes   = substr($value["fchQuincena"],5,2);
          $dia   = substr($value["fchQuincena"],8,2);
          if($mes == '05'){
            $anhioInicio = $anhio - 1;
            $mesInicio   = "11";
            $diaInicio   = "01";

            $anhioFin = $anhio;
            $mesFin   = "04";
            $diaFin   = "30";
          } else {
            $anhioInicio = $anhio;
            $mesInicio   = "05";
            $diaInicio   = "01";

            $anhioFin = $anhio;
            $mesFin   = "10";
            $diaFin   = "31";
          }
          $fchInicioPeriodo = "$anhioInicio-$mesInicio-$diaInicio";
          if($fchInicioPeriodo < $fchInicio) $fchInicioPeriodo = $fchInicio;
          $fchFinPeriodo = "$anhioFin-$mesFin-$diaFin";
          $periodo = " CTS: del $fchInicioPeriodo al $fchFinPeriodo";
          
          $tblCTS .= "<tr>";
          $tblCTS .= "<td>".$periodo."</td><td align = 'right'></td><td>Pagado en planilla: ".substr($value["fchQuincena"],0,7)."</td>";
          $tblCTS .= "</tr>";
          $suma += $value["valor"];
        }
        /*$tblCTS .= "<tr>";
        $tblCTS .= "<td>TOTAL</td><td align = 'right' >".number_format($suma,2)."</td><td></td>";
        $tblCTS .= "</tr>";*/
        $fchInicioPeriodo = $fchFinPeriodo;
        $periodo = " CTS: del $fchInicioPeriodo al $fchUltDiaTrab";
        $arrAnhioMesDiaCts = diferenciaEntreFechas($fchInicioPeriodo,$fchUltDiaTrab);
        $calculoCts = round(($sueldoIndemniz/2 + $sueldoIndemniz/12)/12/30*$arrAnhioMesDiaCts["totEnDias"],2);
        $tblCTS .= "<tr>";
        $tblCTS .= "<td>".$periodo."</td><td align = 'right'></td><td>".$calculoCts."</td>";
        $tblCTS .= "</tr>";

      } else {
        $tblCTS .= "<tr>";
        $tblCTS .= "<td>NO HAY DATOS</td><td>NO HAY DATOS</td><td>$codPlameCTS</td>";
        $tblCTS .= "</tr>";
      }
      $tblCTS .= "</table>";

      //Datos de Vacaciones
      $tblVacac = "<table width = '100%'>";
      $tblVacac .= utf8_encode("<tr><th width = '230px' align = 'center' >Quincena</th><th width = '60px' align = 'center'></th><th>Observación</th></tr>");

      if(count($dataVacac) > 0){
        //$suma = 0;
        //$fchInicioVac = $fchInicio;
        foreach ($dataVacac as $key => $value) {
          $anhio = substr($value["fchQuincena"],0,4);
          $tblVacac .= "<tr>";

          $fchInicioPeriodo =  $anhio. substr($fchInicio,4,6);
          $fchFinPeriodo =   ( $anhio +1 ). substr($fchInicio,4,6);

          $periodo = " Vacaciones: del $fchInicioPeriodo al $fchFinPeriodo";
          
          $tblVacac .= "<td>".$periodo."</td><td align = 'right'></td><td>Pagado en planilla: ".substr($value["fchQuincena"],0,7)."</td>";
          $tblVacac .= "</tr>";
        }

        $periodo = " Vacaciones: del $fchFinPeriodo al $fchUltDiaTrab";
        $arrAnhioMesDiaVacTruncas = diferenciaEntreFechas($fchFinPeriodo,$fchUltDiaTrab);
        
        $meses = $arrAnhioMesDiaVacTruncas["meses"];
        $dias  = $arrAnhioMesDiaVacTruncas["dias"];
        $mntPorMes = $sueldoIndemniz/2/12;
        $mntVacTruncMeses = round($mntPorMes*$meses,2);
        $mntVacTruncDias  = round(($mntPorMes/30)*$dias,2);

        $calculoVacTrunc = $mntVacTruncMeses + $mntVacTruncDias;
        $tblVacac .= "<tr>";
        $tblVacac .= "<td>".$periodo."</td><td align = 'right'></td><td>".$calculoVacTrunc."</td>";
        $tblVacac .= "</tr>";

        /*$tblVacac .= "<tr>";
        $tblVacac .= "<td>TOTAL</td><td align = 'right' >".number_format($suma,2)."</td><td></td>";
        $tblVacac .= "</tr>";*/
      } else {
        $tblVacac .= "<tr>";
        $tblVacac .= "<td>NO HAY DATOS</td><td>NO HAY DATOS</td><td>$codPlameVac</td>";
        $tblVacac .= "</tr>";
      }
      $tblVacac .= "</table>";

      //Gratif
      $tblGratif = "<table width = '100%'>";
      $tblGratif .= utf8_encode("<tr><th width = '230px' align = 'center' >Quincena</th><th width = '60px' align = 'center'></th><th>Observación</th></tr>");

      $sumaGratif = 0;

      if(count($dataGratif) > 0){
        foreach ($dataGratif as $key => $value) {
          $tblGratif .= "<tr>";          
          $fchInicioPeriodo = "xx";
          $fchFinPeriodo = "yy";

          $periodo = " Gratificaciones: del $fchInicioPeriodo al $fchFinPeriodo";
          
          $tblGratif .= "<td>".$periodo."</td><td align = 'right'></td><td>Pagado en planilla: ".substr($value["fchQuincena"],0,7)."</td>";
          $tblGratif .= "</tr>";


          $sumaGratif += $value["valor"];
        }
      } else {
        $tblGratif .= "<tr>";
        $tblGratif .= "<td>NO HAY DATOS</td><td>NO HAY DATOS</td><td>$codPlameGrat01</td>";
        $tblGratif .= "</tr>";
      }


      if(count($dataGratif) > 0){
        $tblGratif .= "<tr>";
        $tblGratif .= "<td>TOTAL</td><td align = 'right' >".number_format($sumaGratif,2)."</td><td></td>";
        $tblGratif .= "</tr>";
      }
      $tblGratif .= "</table>";


      $arreglo = Array(
        "fchInicio" => $dataAltasyBajas["fchInicio"],
        "fchFin"    => $dataAltasyBajas["fchFin"],
        "fchUltDiaTrab" => $dataAltasyBajas["fchUltDiaTrab"],
        "regimen"   => $dataAltasyBajas["regimen"],
        "asignacionFamiliar" => $dataTrabajador["asignacionFamiliar"],
        "tblCTS"    => $tblCTS,
        "tblGratif" => $tblGratif,
        "tblVacac"  => $tblVacac,
        "nroMeses"  => $auxSueldoIndemniz["nroMeses"],
        "sueldoIndemniz" => $sueldoIndemniz,
        "arrAnhioMesDia" => $arrAnhioMesDia,
        "calculoCts"=> $calculoCts
      );
    }

    return json_encode($arreglo);
  }

  function crearTicket($db,$dataPost){
    $usuario = $_SESSION["usuario"];
    $titulo = utf8_decode($dataPost['txtTitulo']);
    $descripcion = utf8_decode($dataPost['txtDescripcion']);
    $dprueba = utf8_decode($dataPost['txtDPrueba']);
    $categoria = utf8_decode($dataPost['cmbCategoria']);
    $url = utf8_decode($dataPost['txtUrl']);

    $estado = "Pendiente";
    $categYonan = array("Problemas con Internet", "Revisar dispositivo", "Problemas con datos");
    if (in_array($categoria, $categYonan)) {
      $asignado = 'smith';
    } else {
      $asignado = 'lescobedo';
    }

    $inserta = $db->prepare("INSERT INTO `helpdesk` (`titulo`, `descripProblema`, `datosprueba`, `categoria`, `categOriginal`, `estado`, `asignadoA`, `creacUsuario`, `creacFch`) VALUES (:titulo, :descripcion, :dprueba, :categoria, :categoria, :estado, :asignado, :usuario, now());");
    $inserta->bindParam(':titulo',$titulo);
    $inserta->bindParam(':descripcion',$descripcion);
    $inserta->bindParam(':dprueba',$dprueba);
    $inserta->bindParam(':categoria',$categoria);
    $inserta->bindParam(':estado',$estado);
    $inserta->bindParam(':asignado',$asignado);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();

    $consulta = $db->prepare("SELECT LAST_INSERT_ID() AS id");
    $consulta->execute();
    $auxData = $consulta->fetch();

    $id = $auxData["id"];

    if ($_FILES["myPhoto1"]["error"] == 0){
      $auxExtension = explode('.',$_FILES['myPhoto1']['name'] );
      $extension = end($auxExtension);
      $foto01  = "imagenes/data/helpdesk/".$id."_1.$extension";
      $resultado = @move_uploaded_file($_FILES["myPhoto1"]["tmp_name"],"../".$foto01);
      $actualiza = $db->prepare("UPDATE `helpdesk` SET foto01 = :foto01 WHERE nroTicket = :nroTicket ");
      $actualiza->bindParam(':nroTicket',$id);
      $actualiza->bindParam(':foto01',$foto01);
      $actualiza->execute();
    }

    if ($_FILES["myPhoto2"]["error"] == 0){
      $auxExtension = explode('.',$_FILES['myPhoto2']['name'] );
      $extension = end($auxExtension);
      $foto02  = "imagenes/data/helpdesk/".$id."_2.$extension";
      @move_uploaded_file($_FILES["myPhoto2"]["tmp_name"],"../".$foto02);
      $actualiza = $db->prepare("UPDATE `helpdesk` SET foto02 = :foto02 WHERE nroTicket = :nroTicket ");
      $actualiza->bindParam(':nroTicket',$id);
      $actualiza->bindParam(':foto02',$foto02);
      $actualiza->execute();
    }
    return $inserta->rowCount();
  }

  function editarHelpDeskTicket($db,$dataPost){
    $usuario = $_SESSION["usuario"];
    $nroTicket = $dataPost['nroTicket'];
    $categoria = utf8_decode($dataPost['cmbCategoria']);
    $estado = utf8_decode($dataPost['cmbEstado']);
    $resumen = utf8_decode($dataPost['txtResumenSolucion']);
    $fchTerminado  = $dataPost['txtFchFin'];
    $categOriginal = utf8_decode($dataPost['categoria']);

    if($categoria == '-' ) $categoria = $categOriginal;

    //echo "nroTicket: $nroTicket  ,categoria: $categoria  ,estado: $estado  ,resumen: $resumen  ,fchTerminado: $fchTerminado";
    $actualiza = $db->prepare("UPDATE `helpdesk` SET categoria = :categoria, `estado` = :estado, resumenSolucion = :resumen, fchTerminado = :fchTerminado WHERE `nroTicket` = :nroTicket ");
    $actualiza->bindParam(':nroTicket',$nroTicket);
    $actualiza->bindParam(':categoria',$categoria);
    $actualiza->bindParam(':estado',$estado);
    $actualiza->bindParam(':resumen',$resumen);
    $actualiza->bindParam(':fchTerminado',$fchTerminado);
    $actualiza->execute();

    if ($actualiza->rowCount() == 1) {
      $actualiza = $db->prepare("UPDATE `helpdesk` SET editaFch = now(), editaUsuario = :usuario WHERE `nroTicket` = :nroTicket ");
      $actualiza->bindParam(':nroTicket',$nroTicket);
      $actualiza->bindParam(':usuario',$usuario);
      $actualiza->execute();
      return $actualiza->rowCount();
    } else return 0;
  }

  function generaCorrelHelpDesk($db,$nroTicket){
    $consulta = $db->prepare("SELECT `correl` FROM `helpdeskdetalle` WHERE nroTicket = :nroTicket ORDER BY correl DESC LIMIT 1 ");
    $consulta->bindParam(':nroTicket',$nroTicket);
    $consulta->execute();
    $data = $consulta->fetchAll();
    $correl = 1;
    foreach ($data as $key => $value) {
      $correl = $value['correl'] + 1;
    }
    return $correl;
  }

  function helpDeskNuevoDetalle($db,$dataPost){
    $usuario  = $_SESSION["usuario"];
    $fchTarea = $dataPost["txtFchTarea"];
    $descrip  = utf8_decode($dataPost["txtDescrip"]);
    $nroTicket= $dataPost["nroTicketDetalle"];
    $correl = generaCorrelHelpDesk($db,$nroTicket);
    //echo "CORREL $correl";

    $inserta = $db->prepare("INSERT INTO `helpdeskdetalle` (`nroTicket`, `correl`, `fchRealizado`, `tareasRealizadas`, `creacUsuario`, `creacFch`) VALUES (:nroTicket, :correl, :fchTarea, :descrip, :usuario, NOW())");
    $inserta->bindParam(':nroTicket',$nroTicket);
    $inserta->bindParam(':correl',$correl);
    $inserta->bindParam(':fchTarea',$fchTarea);
    $inserta->bindParam(':descrip',$descrip);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function buscarDetalleTicket($db, $dataPost){
    $nroTicket = $dataPost["nroTicket"];
    $correl = $dataPost["correl"];

    $consulta = $db->prepare("SELECT `nroTicket`, `correl`, `fchRealizado`, `tareasRealizadas`, `foto01`, `creacUsuario`, `creacFch`, `editaFch`, `editaUsuario` FROM `helpdeskdetalle` WHERE `nroTicket` = :nroTicket AND `correl` = :correl ");
    $consulta->bindParam(':nroTicket',$nroTicket);
    $consulta->bindParam(':correl',$correl);
    $consulta->execute();
    //return $consulta->fetchAll();
    return $consulta->fetch();
  }

  function helpDeskEditaDetalle($db,$dataPost){
    $usuario  = $_SESSION["usuario"];
    $nroTicket= $dataPost["nroTicketEditaDetalle"];
    $correl   = $dataPost["txtEditaCorrel"];
    $fchTarea = $dataPost["txtEditaFchTarea"];
    $descrip  = utf8_decode($dataPost["txtEditaDescrip"]);

    $actualiza = $db->prepare("UPDATE `helpdeskdetalle` SET `fchRealizado` = :fchTarea, `tareasRealizadas` = :descrip WHERE `nroTicket` = :nroTicket AND `correl` = :correl ");
    $actualiza->bindParam(':nroTicket',$nroTicket);
    $actualiza->bindParam(':correl',$correl);
    $actualiza->bindParam(':fchTarea',$fchTarea);
    $actualiza->bindParam(':descrip',$descrip);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function helpDeskEliminaDetalle($db,$dataPost){
    $nroTicket= $dataPost["nroTicketEliminaDetalle"];
    $correl   = $dataPost["txtEliminaCorrel"];
    $fchTarea = $dataPost["txtEliminaFchTarea"];

    $elimina = $db->prepare("DELETE FROM `helpdeskdetalle` WHERE `nroTicket` = :nroTicket AND `correl` = :correl AND  `fchRealizado` = :fchTarea");
    $elimina->bindParam(':nroTicket',$nroTicket);
    $elimina->bindParam(':correl',$correl);
    $elimina->bindParam(':fchTarea',$fchTarea);
    $elimina->execute();
    return $elimina->rowCount();
  }

  function nvoIdCotizacion($db){
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

  function crearCotizacion($db,$dataPost) {
    $usuario = $_SESSION["usuario"];
    $id_cotizacion = "index";
    if($usuario != ""){
      $id_cotizacion = nvoIdCotizacion($db);
      $cliente = utf8_decode($dataPost['cliente']);
      $ruc_dni = $dataPost['ruc_dni'];
      $direccion = utf8_decode($dataPost['direccion']);
      $condicion_pago = utf8_decode($dataPost['condicion_pago']);
      $contacto = utf8_decode($dataPost['contacto']);
      $telefono = utf8_decode($dataPost['telefono']);
      $email = utf8_decode($dataPost['email']);
      $fecha = $dataPost['fecha'];
      $subtotal = $dataPost['subtotal'];
      $opcIGV = $dataPost['opcIGV'];
      $igv = $dataPost['montoIGVFinal'];
      $total = $dataPost['totalFinal'];
      $servicios = $dataPost['servicios'];
      $incluye = $dataPost['dataID'];

      $inserta = $db->prepare("INSERT INTO `cotizacion` (`id_cotizacion`,`cliente`, `ruc_dni`, `direccion`, `condicion_pago`, `contacto`, `telefono`, `email`, `fecha`, `subtotal`, `opcIGV`, `igv`, `total`, `usuario`) VALUES (:id_cotizacion, :cliente, :ruc_dni, :direccion, :condicion_pago, :contacto, :telefono, :email, :fecha, :subtotal, :opcIGV, :igv, :total, :usuario);");
      $inserta->bindParam(':id_cotizacion',$id_cotizacion);
      $inserta->bindParam(':cliente',$cliente);
      $inserta->bindParam(':ruc_dni',$ruc_dni);
      $inserta->bindParam(':direccion',$direccion);
      $inserta->bindParam(':condicion_pago',$condicion_pago);
      $inserta->bindParam(':contacto',$contacto);
      $inserta->bindParam(':telefono',$telefono);
      $inserta->bindParam(':email',$email);
      $inserta->bindParam(':fecha',$fecha);
      $inserta->bindParam(':subtotal',$subtotal);
      $inserta->bindParam(':opcIGV',$opcIGV);
      $inserta->bindParam(':igv',$igv);
      $inserta->bindParam(':total',$total);
      $inserta->bindParam(':usuario',$usuario);
      $inserta->execute();

      if($inserta->rowCount() == 1){
        $partsServicios = explode("|", $servicios);
        $cantVueltas = count($partsServicios) - 1;
        for($i = 0; $i < $cantVueltas; $i++){
          $partsDataServicio = explode("_",$partsServicios[$i]);
          $cantDataServicio = count($partsDataServicio);
          $direccionSalida = utf8_decode($partsDataServicio[0]);
          $referenciaSalida = utf8_decode($partsDataServicio[1]);
          $fechaSalida = $partsDataServicio[2];
          $horaSalida = $partsDataServicio[3];
          $contactoSalida = utf8_decode($partsDataServicio[4]);
          $telefonoSalida = utf8_decode($partsDataServicio[5]);
          $emailSalida = utf8_decode($partsDataServicio[6]);
          $direccionLlegada = "";
          $referenciaLlegada = "";
          $fechaLlegada = "";
          $horaLlegada = "";
          $contactoLlegada = "";
          $telefonoLlegada = "";
          $emailLlegada = "";
          $direccionRetorno = "";
          $referenciaRetorno = "";
          $fechaRetorno = "";
          $horaRetorno = "";
          $contactoRetorno = "";
          $telefonoRetorno = "";
          $emailRetorno = "";
          if($cantDataServicio > 8){
            $direccionLlegada = utf8_decode($partsDataServicio[7]);
            $referenciaLlegada = utf8_decode($partsDataServicio[8]);
            $fechaLlegada = $partsDataServicio[9];
            $horaLlegada = $partsDataServicio[10];
            $contactoLlegada = utf8_decode($partsDataServicio[11]);
            $telefonoLlegada = utf8_decode($partsDataServicio[12]);
            $emailLlegada = utf8_decode($partsDataServicio[13]);
          }
          if($cantDataServicio > 15){
            $direccionRetorno = utf8_decode($partsDataServicio[14]);
            $referenciaRetorno = utf8_decode($partsDataServicio[15]);
            $fechaRetorno = $partsDataServicio[16];
            $horaRetorno = $partsDataServicio[17];
            $contactoRetorno = utf8_decode($partsDataServicio[18]);
            $telefonoRetorno = utf8_decode($partsDataServicio[19]);
            $emailRetorno = utf8_decode($partsDataServicio[20]);
          }
          $detalles = utf8_decode($partsDataServicio[$cantDataServicio-1]);
          $inserta = $db->prepare("INSERT INTO `cotiz_servicios` (`direccionSalida`,`referenciaSalida`, `fechaSalida`, `horaSalida`, `contactoSalida`, `telefonoSalida`, `emailSalida`, `direccionLlegada`, `referenciaLlegada`, `fechaLlegada`, `horaLlegada`, `contactoLlegada`, `telefonoLlegada`, `emailLlegada`, `direccionRetorno`, `referenciaRetorno`, `fechaRetorno`, `horaRetorno`, `contactoRetorno`, `telefonoRetorno`, `emailRetorno`, `detalles`, `id_cotizacion`) VALUES (:direccionSalida, :referenciaSalida, :fechaSalida, :horaSalida, :contactoSalida, :telefonoSalida, :emailSalida, :direccionLlegada, :referenciaLlegada, :fechaLlegada, :horaLlegada, :contactoLlegada, :telefonoLlegada, :emailLlegada, :direccionRetorno, :referenciaRetorno, :fechaRetorno, :horaRetorno, :contactoRetorno, :telefonoRetorno, :emailRetorno, :detalles, :id_cotizacion);");
          $inserta->bindParam(':direccionSalida',$direccionSalida);
          $inserta->bindParam(':referenciaSalida',$referenciaSalida);
          $inserta->bindParam(':fechaSalida',$fechaSalida);
          $inserta->bindParam(':horaSalida',$horaSalida);
          $inserta->bindParam(':contactoSalida',$contactoSalida);
          $inserta->bindParam(':telefonoSalida',$telefonoSalida);
          $inserta->bindParam(':emailSalida',$emailSalida);
          $inserta->bindParam(':direccionLlegada',$direccionLlegada);
          $inserta->bindParam(':referenciaLlegada',$referenciaLlegada);
          $inserta->bindParam(':fechaLlegada',$fechaLlegada);
          $inserta->bindParam(':horaLlegada',$horaLlegada);
          $inserta->bindParam(':contactoLlegada',$contactoLlegada);
          $inserta->bindParam(':telefonoLlegada',$telefonoLlegada);
          $inserta->bindParam(':emailLlegada',$emailLlegada);
          $inserta->bindParam(':direccionRetorno',$direccionRetorno);
          $inserta->bindParam(':referenciaRetorno',$referenciaRetorno);
          $inserta->bindParam(':fechaRetorno',$fechaRetorno);
          $inserta->bindParam(':horaRetorno',$horaRetorno);
          $inserta->bindParam(':contactoRetorno',$contactoRetorno);
          $inserta->bindParam(':telefonoRetorno',$telefonoRetorno);
          $inserta->bindParam(':emailRetorno',$emailRetorno);
          $inserta->bindParam(':detalles',$detalles);
          $inserta->bindParam(':id_cotizacion',$id_cotizacion);
          $inserta->execute();
          if($inserta->rowCount() != 1){
            $id_cotizacion = "";
            break;
          }
        }
        if($id_cotizacion != ""){
          $partsIncluye = explode("_", $incluye);
          $cantVueltas = count($partsIncluye) - 1;
          for($i = 0; $i < $cantVueltas; $i++){
            $id_incluye = $partsIncluye[$i];
            $inserta = $db->prepare("INSERT INTO `cotiz_incluye` (`id_incluye`,`id_cotizacion`) VALUES (:id_incluye, :id_cotizacion);");
            $inserta->bindParam(':id_incluye',$id_incluye);
            $inserta->bindParam(':id_cotizacion',$id_cotizacion);
            $inserta->execute();
            if($inserta->rowCount() != 1){
              $id_cotizacion = "";
              break;
            }
          }
        }
      }
      else{
        $id_cotizacion = "";
      }
    }
    return $id_cotizacion;
  }

  function editarCotizacion($db,$dataPost) {
    $usuario = $_SESSION["usuario"];
    $id_cotizacion = "index";
    if($usuario != ""){
      $id_cotizacion = $dataPost['id_cotizacionEdit'];
      $cliente = utf8_decode($dataPost['cliente']);
      $ruc_dni = $dataPost['ruc_dni'];
      $direccion = utf8_decode($dataPost['direccion']);
      $condicion_pago = utf8_decode($dataPost['condicion_pago']);
      $contacto = utf8_decode($dataPost['contacto']);
      $telefono = utf8_decode($dataPost['telefono']);
      $email = utf8_decode($dataPost['email']);
      $fecha = $dataPost['fecha'];
      $subtotal = $dataPost['subtotal'];
      $opcIGV = $dataPost['opcIGV'];
      $igv = $dataPost['montoIGVFinal'];
      $total = $dataPost['totalFinal'];
      $usuario = $_SESSION["usuario"];
      $servicios = $dataPost['servicios'];
      $incluye = $dataPost['dataID'];
  
      $elimina = $db->prepare("DELETE FROM  `cotiz_incluye` WHERE `id_cotizacion` = :id_cotizacion");
      $elimina->bindParam(':id_cotizacion',$id_cotizacion);
      $elimina->execute();
      //
       $elimina->rowCount();
      $elimina = $db->prepare("DELETE FROM  `cotiz_servicios` WHERE `id_cotizacion` = :id_cotizacion");
      $elimina->bindParam(':id_cotizacion',$id_cotizacion);
      $elimina->execute();
      //echo $elimina->rowCount();
      $elimina = $db->prepare("DELETE FROM  `cotizacion` WHERE `id_cotizacion` = :id_cotizacion");
      $elimina->bindParam(':id_cotizacion',$id_cotizacion);
      $elimina->execute();
      //return $elimina->rowCount();
  
      //echo $elimina->rowCount();
  
      $inserta = $db->prepare("INSERT INTO `cotizacion` (`id_cotizacion`,`cliente`, `ruc_dni`, `direccion`, `condicion_pago`, `contacto`, `telefono`, `email`, `fecha`, `subtotal`, `opcIGV`, `igv`, `total`, `usuario`) VALUES (:id_cotizacion, :cliente, :ruc_dni, :direccion, :condicion_pago, :contacto, :telefono, :email, :fecha, :subtotal, :opcIGV, :igv, :total, :usuario);");
      $inserta->bindParam(':id_cotizacion',$id_cotizacion);
      $inserta->bindParam(':cliente',$cliente);
      $inserta->bindParam(':ruc_dni',$ruc_dni);
      $inserta->bindParam(':direccion',$direccion);
      $inserta->bindParam(':condicion_pago',$condicion_pago);
      $inserta->bindParam(':contacto',$contacto);
      $inserta->bindParam(':telefono',$telefono);
      $inserta->bindParam(':email',$email);
      $inserta->bindParam(':fecha',$fecha);
      $inserta->bindParam(':subtotal',$subtotal);
      $inserta->bindParam(':opcIGV',$opcIGV);
      $inserta->bindParam(':igv',$igv);
      $inserta->bindParam(':total',$total);
      $inserta->bindParam(':usuario',$usuario);
      $inserta->execute();
  
      if($inserta->rowCount() == 1){
        $partsServicios = explode("|", $servicios);
        $cantVueltas = count($partsServicios) - 1;
        for($i = 0; $i < $cantVueltas; $i++){
          $partsDataServicio = explode("_",$partsServicios[$i]);
          $cantDataServicio = count($partsDataServicio);
          $direccionSalida = utf8_decode($partsDataServicio[0]);
          $referenciaSalida = utf8_decode($partsDataServicio[1]);
          $fechaSalida = $partsDataServicio[2];
          $horaSalida = $partsDataServicio[3];
          $contactoSalida = utf8_decode($partsDataServicio[4]);
          $telefonoSalida = utf8_decode($partsDataServicio[5]);
          $emailSalida = utf8_decode($partsDataServicio[6]);
          $direccionLlegada = "";
          $referenciaLlegada = "";
          $fechaLlegada = "";
          $horaLlegada = "";
          $contactoLlegada = "";
          $telefonoLlegada = "";
          $emailLlegada = "";
          $direccionRetorno = "";
          $referenciaRetorno = "";
          $fechaRetorno = "";
          $horaRetorno = "";
          $contactoRetorno = "";
          $telefonoRetorno = "";
          $emailRetorno = "";
          if($cantDataServicio > 8){
            $direccionLlegada = utf8_decode($partsDataServicio[7]);
            $referenciaLlegada = utf8_decode($partsDataServicio[8]);
            $fechaLlegada = $partsDataServicio[9];
            $horaLlegada = $partsDataServicio[10];
            $contactoLlegada = utf8_decode($partsDataServicio[11]);
            $telefonoLlegada = utf8_decode($partsDataServicio[12]);
            $emailLlegada = utf8_decode($partsDataServicio[13]);
          }
          if($cantDataServicio > 15){
            $direccionRetorno = utf8_decode($partsDataServicio[14]);
            $referenciaRetorno = utf8_decode($partsDataServicio[15]);
            $fechaRetorno = $partsDataServicio[16];
            $horaRetorno = $partsDataServicio[17];
            $contactoRetorno = utf8_decode($partsDataServicio[18]);
            $telefonoRetorno = utf8_decode($partsDataServicio[19]);
            $emailRetorno = utf8_decode($partsDataServicio[20]);
          }
          $detalles = utf8_decode($partsDataServicio[$cantDataServicio-1]);
          $inserta = $db->prepare("INSERT INTO `cotiz_servicios` (`direccionSalida`,`referenciaSalida`, `fechaSalida`, `horaSalida`, `contactoSalida`, `telefonoSalida`, `emailSalida`, `direccionLlegada`, `referenciaLlegada`, `fechaLlegada`, `horaLlegada`, `contactoLlegada`, `telefonoLlegada`, `emailLlegada`, `direccionRetorno`, `referenciaRetorno`, `fechaRetorno`, `horaRetorno`, `contactoRetorno`, `telefonoRetorno`, `emailRetorno`, `detalles`, `id_cotizacion`) VALUES (:direccionSalida, :referenciaSalida, :fechaSalida, :horaSalida, :contactoSalida, :telefonoSalida, :emailSalida, :direccionLlegada, :referenciaLlegada, :fechaLlegada, :horaLlegada, :contactoLlegada, :telefonoLlegada, :emailLlegada, :direccionRetorno, :referenciaRetorno, :fechaRetorno, :horaRetorno, :contactoRetorno, :telefonoRetorno, :emailRetorno, :detalles, :id_cotizacion);");
          $inserta->bindParam(':direccionSalida',$direccionSalida);
          $inserta->bindParam(':referenciaSalida',$referenciaSalida);
          $inserta->bindParam(':fechaSalida',$fechaSalida);
          $inserta->bindParam(':horaSalida',$horaSalida);
          $inserta->bindParam(':contactoSalida',$contactoSalida);
          $inserta->bindParam(':telefonoSalida',$telefonoSalida);
          $inserta->bindParam(':emailSalida',$emailSalida);
          $inserta->bindParam(':direccionLlegada',$direccionLlegada);
          $inserta->bindParam(':referenciaLlegada',$referenciaLlegada);
          $inserta->bindParam(':fechaLlegada',$fechaLlegada);
          $inserta->bindParam(':horaLlegada',$horaLlegada);
          $inserta->bindParam(':contactoLlegada',$contactoLlegada);
          $inserta->bindParam(':telefonoLlegada',$telefonoLlegada);
          $inserta->bindParam(':emailLlegada',$emailLlegada);
          $inserta->bindParam(':direccionRetorno',$direccionRetorno);
          $inserta->bindParam(':referenciaRetorno',$referenciaRetorno);
          $inserta->bindParam(':fechaRetorno',$fechaRetorno);
          $inserta->bindParam(':horaRetorno',$horaRetorno);
          $inserta->bindParam(':contactoRetorno',$contactoRetorno);
          $inserta->bindParam(':telefonoRetorno',$telefonoRetorno);
          $inserta->bindParam(':emailRetorno',$emailRetorno);
          $inserta->bindParam(':detalles',$detalles);
          $inserta->bindParam(':id_cotizacion',$id_cotizacion);
          $inserta->execute();
          if($inserta->rowCount() != 1){
            $id_cotizacion = "";
            break;
          }
        }
        if($id_cotizacion != ""){
          $partsIncluye = explode("_", $incluye);
          $cantVueltas = count($partsIncluye) - 1;
          for($i = 0; $i < $cantVueltas; $i++){
            $id_incluye = $partsIncluye[$i];
            $inserta = $db->prepare("INSERT INTO `cotiz_incluye` (`id_incluye`,`id_cotizacion`) VALUES (:id_incluye, :id_cotizacion);");
            $inserta->bindParam(':id_incluye',$id_incluye);
            $inserta->bindParam(':id_cotizacion',$id_cotizacion);
            $inserta->execute();
            if($inserta->rowCount() != 1){
              $id_cotizacion = "";
              break;
            }
          }
        }
      }
      else{
        $id_cotizacion = "";
      }
    }
    return $id_cotizacion;
  }

  function crearCapacitacion($db,$dataPost) {
    $usuario = $_SESSION["usuario"];
    $data = explode("-",utf8_decode($dataPost['txtCapacitador']));
    $capacitador = $data[0];
    $titulo = utf8_decode($dataPost['txtTitulo']);
    $descripcion = utf8_decode($dataPost['txtDescripcion']);
    $video = $dataPost['txtVideo'];
    
    if ($_FILES["myDocumento"]["error"] == 0){
      $documento = utf8_decode($_FILES["myDocumento"]["name"]);
      $url = "../../ocurrenciamoy/material_capacitacion/".$_FILES["myDocumento"]["name"];
      @move_uploaded_file($_FILES["myDocumento"]["tmp_name"],$url);
    }

    $inserta = $db->prepare("INSERT INTO `capacitacion` (`capacitador`,`titulo`,`descripcion`, `video`, `documento`, `usuario_registro`) VALUES (:capacitador, :titulo, :descripcion, :video, :documento, :usuario);");
    $inserta->bindParam(':capacitador',$capacitador);
    $inserta->bindParam(':titulo',$titulo);
    $inserta->bindParam(':descripcion',$descripcion);
    $inserta->bindParam(':video',$video);
    $inserta->bindParam(':documento',$documento);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function editarCapacitacion($db,$dataPost) {
    $usuario = $_SESSION["usuario"];
    $id = $dataPost['txtId'];
    $data = explode("-",utf8_decode($dataPost['txtCapacitadorEditar']));
    $capacitador = $data[0];
    $titulo = utf8_decode($dataPost['txtTituloEditar']);
    $descripcion = utf8_decode($dataPost['txtDescripcionEditar']);
    $video = $dataPost['txtVideoEditar'];
    $documento = $dataPost['txtDocumentoEditar'];

    if ($_FILES["myDocumentoEditar"]["name"] != ""){
      if ($_FILES["myDocumentoEditar"]["error"] == 0){
        unlink("../../ocurrenciamoy/material_capacitacion/".$documento);
        $documento = utf8_decode($_FILES["myDocumentoEditar"]["name"]);
        $url = "../../ocurrenciamoy/material_capacitacion/".$_FILES["myDocumentoEditar"]["name"];
        @move_uploaded_file($_FILES["myDocumentoEditar"]["tmp_name"],$url);
      }
    }

    $actualiza = $db->prepare("UPDATE `capacitacion` SET `capacitador` = :capacitador, `titulo` = :titulo, `descripcion` = :descripcion, `video` = :video, `documento` = :documento, `usuario_edicion` = :usuario WHERE `id` = :id");
    $actualiza->bindParam(':id',$id);
    $actualiza->bindParam(':capacitador',$capacitador);
    $actualiza->bindParam(':titulo',$titulo);
    $actualiza->bindParam(':descripcion',$descripcion);
    $actualiza->bindParam(':video',$video);
    $actualiza->bindParam(':documento',$documento);
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function eliminarCapacitacion($db, $dataPost){
    $id = $dataPost["id"];
    $documento = $dataPost["documento"];

    unlink("../../ocurrenciamoy/material_capacitacion/".$documento);

    $elimina = $db->prepare("DELETE FROM `capacitacion` WHERE `id` = :id");
    $elimina->bindParam(':id',$id);
    $elimina->execute();

    return $elimina->rowCount();
  }
  function buscarCuentaInfraccion($db,$dato){
    $datos = array();
    $consulta = $db->prepare("SELECT nombre FROM `cliente` WHERE estadoCliente = 'activo' and nombre LIKE '%$dato%'");
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => utf8_encode($item['nombre']));
    }
    return $datos;
  }

  function buscarInfraccion($db,$dato){
    $datos = array();
    $consulta = $db->prepare("SELECT falta FROM `infracciones` WHERE  falta LIKE '%$dato%'");
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => utf8_encode($item['falta']));
    }
    return $datos;
  }

  function registrarPapeleta($db,$dataPost) {
    $fecha_actual = date("d") . "-" . date("m") . "-" . date("Y");
    $time = time();
    $hora_actual = date("H:i:s", $time);
    //
    $usuario = $_SESSION["usuario"];
    $fecha = $dataPost['txtFecha'];
    $placa = $dataPost['txtPlaca'];
    $conductor = $dataPost['txtConductor'];
    $distrito = $dataPost['txtDistrito'];
    $direccion= $dataPost['txtDireccion'];
    $cuenta = $dataPost['txtCuenta'];
    $infraccion = $dataPost['txtInfraccion'];
    $descripcion = $dataPost['txtDescripcion'];
    $monto = $dataPost['txtMonto'];
    $descuento = $dataPost['txtDescuento'];
    $idTrabajador = buscarIdTrabajador($db,$usuario);
    $RESULT = "";
    //
    $mes = ["ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SETIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE"][date("n") - 1];
    $anio = date("Y");
    $foto1 = "";
    if ($_FILES["myPhoto1"]["error"] == 0){
      $auxExtension1 = explode('.',$_FILES['myPhoto1']['name']);
      $extension1 = end($auxExtension1);
      $conductor2 = explode("-", $conductor);
      $foto1 = "/ocurrenciamoy/imagenes_incidencias_papeleta/$anio/$mes/". $conductor2[1] . "_" . $placa . "_" . $fecha_actual . ".$extension1";
      @move_uploaded_file($_FILES["myPhoto1"]["tmp_name"],"../..".$foto1);
    }

    $inserta = $db->prepare("INSERT INTO `incidencias_infraccion` (`fecha_infraccion`, `placa`, `conductor`, `distrito`, `direccion`, `cuenta`, `foto`, `infraccion`, `descripcion`, `monto`, `descuento`,  `idTrabajador`) VALUES (:fecha, :placa, :conductor, :distrito, :direccion, :cuenta, :foto1, :infraccion, :descripcion, :monto, :descuento, :idTrabajador)");
    $inserta->bindParam(':fecha',$fecha);
    $inserta->bindParam(':placa',$placa);
    $inserta->bindParam(':conductor',$conductor);
    $inserta->bindParam(':distrito',$distrito);
    $inserta->bindParam(':direccion',$direccion);
    $inserta->bindParam(':cuenta',$cuenta);
    $inserta->bindParam(':foto1',$foto1);
    $inserta->bindParam(':infraccion',$infraccion);
    $inserta->bindParam(':descripcion',$descripcion);
    $inserta->bindParam(':monto',$monto);
    $inserta->bindParam(':descuento',$descuento);
    $inserta->bindParam(':idTrabajador',$idTrabajador);
    $inserta->execute();
    $RESULT = $inserta->rowCount();
    return $RESULT;
  }
  function buscarTelefonoR($db,$dato){
    $datos = array();
    $consulta = $db->prepare("SELECT `idNroTelefono` AS dato FROM `telefono` WHERE estado = 'Activo' and `idNroTelefono` LIKE '%$dato%'");
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => utf8_encode($item['dato']));
    } 
    return $datos;
  }

  function registrarTelefonoR($db,$dataPost) {
    //
    $usuario = $_SESSION["usuario"];
    $fecha = $dataPost['txtFecha'];
    $cliente = $dataPost['txtCliente'];
    $distrito = $dataPost['txtDistrito'];
    $direccion= $dataPost['txtDireccion'];
    $telefono = $dataPost['txtTelefono'];
    $trabajador = $dataPost['txtTrabajador'];
    $idTrabajador = buscarIdTrabajador($db,$usuario);

    $inserta = $db->prepare("INSERT INTO `incidencias_telefono` (`fecha_robo`, `cliente`, `distrito_r`, `direccion`, `telefono`, `trabajador`, `idTrabajador`) VALUES (:fecha, :cliente, :distrito, :direccion, :telefono, :trabajador,:idTrabajador)");
    $inserta->bindParam(':fecha',$fecha);
    $inserta->bindParam(':cliente',$cliente);
    $inserta->bindParam(':distrito',$distrito);
    $inserta->bindParam(':direccion',$direccion);
    $inserta->bindParam(':telefono',$telefono);
    $inserta->bindParam(':trabajador',$trabajador);
    $inserta->bindParam(':idTrabajador',$idTrabajador);
    $inserta->execute();
    $RESULT = $inserta->rowCount();
    return $RESULT;
  }

  function editar_TelfR($db,$dataPost) {
    $fecha_actual = date("d") . "-" . date("m") . "-" . date("Y");
    //
    $usuario = $_SESSION["usuario"];
    $idTrabajador = buscarIdTrabajador($db,$usuario);
    $id = $dataPost['txtIdEditar'];
    $fchDenuncia = $dataPost['txtFchDenunciaEditar'];
    $distrito = $dataPost['txtDistritoREditar'];
    $comisaria = utf8_decode($dataPost['cmbComisariaEditar']);
    $estado = "Arreglado";
    //
    $mes = ["ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SETIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE"][date("n") - 1];
    $anio = date("Y");
    $foto = "/ocurrenciamoy/imagenes_incidencias_telefono/$anio/$mes/" . $fecha_actual .  "_" . $comisaria . ".jpg";
    
    $actualiza = $db->prepare("UPDATE `incidencias_telefono` SET `fecha_denuncia` = :fchDenuncia, `distrito_d` = :distrito, `comisaria` = :comisaria, `foto` = :foto, `estado_actual` = :estado, `idTrabajador_editor` = :idTrabajador WHERE `idtelefono` = :id");
    $actualiza->bindParam(':id',$id);
    $actualiza->bindParam(':fchDenuncia',$fchDenuncia);
    $actualiza->bindParam(':distrito',$distrito);
    $actualiza->bindParam(':comisaria',$comisaria);
    $actualiza->bindParam(':foto',$foto);
    $actualiza->bindParam(':estado',$estado);
    $actualiza->bindParam(':idTrabajador',$idTrabajador);
    $actualiza->execute();

    if ($actualiza->rowCount() == 1) {
      if ($_FILES["myPhoto"]["error"] == 0){
        @move_uploaded_file($_FILES["myPhoto"]["tmp_name"],"../..".utf8_encode($foto));
      }

      $descripcion = utf8_encode("Se editó registro de teléfono robado | Fecha: " . $fchDenuncia . " | Distrito: " . $distrito . " | Comisaria: " . utf8_encode($comisaria) . ".");
      logAccion($db, $descripcion, "", "");
    }

    return $actualiza->rowCount();
  }
?>