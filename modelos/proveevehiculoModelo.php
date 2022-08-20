<?php
global $servidor, $bd, $usuario, $contrasenia;
$db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);

  function buscarDataProveeVehiculos($db, $dataPost){

  	$where = " 1 ";
    $where .= $dataPost['Documento'] == "" ? "": " AND `documento` like :documento ";
    $where .= $dataPost['Nombre_Proveedor'] == "" ? "": " AND `nombreCompleto` like :nombreCompleto ";
    $where .= $dataPost['Teléfono'] == "" ? "": " AND `nroTelefono` like :nroTelefono ";
    $where .= $dataPost['Correo_Electrónico'] == "" ? "": " AND `eMail` like :eMail ";
    $where .= $dataPost['Estado_Proveed'] == "" ? "": " AND `estadoTercero` like :estadoTercero ";
    $where .= $dataPost['Usuario_Creador'] == "" ? "": " AND `usuario` like :usuario ";

    $where .= $dataPost['Fch_Creación'] == "" ? "": " AND `fchCreacion` like :fchCreacion ";
    $where .= $dataPost['Usuario_Editor'] == "" ? "": " AND `editaUsuario` like :editaUsuario ";
    $where .= $dataPost['Fch_Edición'] == "" ? "": " AND `editaFch` like :editaFch ";

    $consulta = $db->prepare("SELECT `documento`, `nombreCompleto`, `nroTelefono`, `eMail`, `estadoTercero`, `bancoNombre`, `bancoCuentaNro`, `bancoCuentaTipo`, `bancoCuentaMoneda`, `cuentaDetraccion`, `usuario`, `fchCreacion`, `editaUsuario`, `editaFch`FROM `vehiculodueno` WHERE $where");

    if ($dataPost['Documento'] != ""){
      $documento = '%'.$dataPost['Documento'].'%';
      $consulta->bindParam(':documento',$documento);
    }

    if ($dataPost['Nombre_Proveedor'] != ""){
      $nombreCompleto = '%'.$dataPost['Nombre_Proveedor'].'%';
      $consulta->bindParam(':nombreCompleto',$nombreCompleto);
    }
    if ($dataPost['Teléfono'] != ""){
      $nroTelefono = '%'.$dataPost['Teléfono'].'%';
      $consulta->bindParam(':nroTelefono',$nroTelefono);
    }

    if ($dataPost['Correo_Electrónico'] != ""){
      $eMail = '%'.$dataPost['Correo_Electrónico'].'%';
      $consulta->bindParam(':eMail',$eMail);
    }

    if ($dataPost['Estado_Proveed'] != ""){
      $estadoTercero = '%'.$dataPost['Estado_Proveed'].'%';
      $consulta->bindParam(':estadoTercero',$estadoTercero);
    }

    if ($dataPost['Usuario_Creador'] != ""){
      $usuario = '%'.$dataPost['Usuario_Creador'].'%';
      $consulta->bindParam(':usuario',$usuario);
    }

    if ($dataPost['Fch_Creación'] != ""){
      $fchCreacion = '%'.$dataPost['Fch_Creación'].'%';
      $consulta->bindParam(':fchCreacion',$fchCreacion);
    }

    if ($dataPost['Usuario_Editor'] != ""){
      $editaUsuario = '%'.$dataPost['Usuario_Editor'].'%';
      $consulta->bindParam(':editaUsuario',$editaUsuario);
    }

    if ($dataPost['Fch_Edición'] != ""){
      $editaFch = '%'.$dataPost['Fch_Edición'].'%';
      $consulta->bindParam(':editaFch',$editaFch);
    }


    $consulta->execute();
    return $consulta->fetchAll();  	
  }



  function buscarDataTelefonos($db, $dataPost){

    $where = " 1 ";
    $where .= $dataPost['Nro_Telefono'] == "" ? "": " AND `idNroTelefono` like :nroTelefono ";
    $where .= $dataPost['Considerar_Propio'] == "" ? "": " AND `considPropio` like :considPropio ";
    $where .= $dataPost['Operador'] == "" ? "": " AND `operador` like :operador ";
    $where .= $dataPost['Plan'] == "" ? "": " AND `plan` like :plan ";
    $where .= $dataPost['Marca'] == "" ? "": " AND `marca` like :marca ";
    $where .= $dataPost['Modelo'] == "" ? "": " AND `modelo` like :modelo ";
    $where .= $dataPost['Imei'] == "" ? "": " AND `imei` like :imei ";
    $where .= $dataPost['Estado'] == "" ? "": " AND `estado` like :estado ";
    $where .= $dataPost['Conductor'] == "" ? "": " AND `conductor` like :conductor ";
    $where .= $dataPost['Placa'] == "" ? "": " AND `unidad` like :unidad ";
    $where .= $dataPost['Fch_Entrega'] == "" ? "": " AND `fchEntrega` like :fchEntrega ";
    $where .= $dataPost['Usuario_Creador'] == "" ? "": " AND `usuario` like :usuarioCreador ";
    $where .= $dataPost['Fch_Creación'] == "" ? "": " AND `fchCreacion` like :fchCreacion ";
    $where .= $dataPost['Usuario_Editor'] == "" ? "": " AND `usuarioUltimoCambio` like :usuarioEditor ";
    $where .= $dataPost['Fch_Edición'] == "" ? "": " AND `fchUltimoCambio` like :fchEdicion ";

    $consulta = $db->prepare("SELECT `idNroTelefono`, `marca`, `modelo`, `sim`, `imei`, `estado`, `considPropio`, `operador`, `plan`, `conductor`, `unidad`, `fchEntrega`, `adjuntoTelf`, `adicionalTelf`, `fchAdquisicion`, `usuario`, `fchCreacion`, `usuarioUltimoCambio`, `fchUltimoCambio`  FROM `telefono` WHERE $where");

    if ($dataPost['Nro_Telefono'] != ""){
      $nroTelefono = '%'.$dataPost['Nro_Telefono'].'%';
      $consulta->bindParam(':nroTelefono',$nroTelefono);
    }

    if ($dataPost['Considerar_Propio'] != ""){
      $considPropio = '%'.$dataPost['Considerar_Propio'].'%';
      $consulta->bindParam(':considPropio',$considPropio);
    }

    if ($dataPost['Operador'] != ""){
      $operador = '%'.$dataPost['Operador'].'%';
      $consulta->bindParam(':operador',$operador);
    }

    if ($dataPost['Plan'] != ""){
      $plan = '%'.$dataPost['Plan'].'%';
      $consulta->bindParam(':plan',$plan);
    }

    if ($dataPost['Marca'] != ""){
      $marca = '%'.$dataPost['Marca'].'%';
      $consulta->bindParam(':marca',$marca);
    }
    if ($dataPost['Modelo'] != ""){
      $modelo = '%'.$dataPost['Modelo'].'%';
      $consulta->bindParam(':modelo',$modelo);
    }
   
    if ($dataPost['Imei'] != ""){
      $imei = '%'.$dataPost['Imei'].'%';
      $consulta->bindParam(':imei',$imei);
    }

    if ($dataPost['Estado'] != ""){
      $estado = '%'.$dataPost['Estado'].'%';
      $consulta->bindParam(':estado',$estado);
    }

    if ($dataPost['Conductor'] != ""){
      $conductor = '%'.$dataPost['Conductor'].'%';
      $consulta->bindParam(':conductor',$conductor);
    }

    if ($dataPost['Placa'] != ""){
      $unidad = '%'.$dataPost['Placa'].'%';
      $consulta->bindParam(':unidad',$unidad);
    }

    if ($dataPost['Fch_Entrega'] != ""){
      $fchEntrega = '%'.$dataPost['Fch_Entrega'].'%';
      $consulta->bindParam(':fchEntrega',$fchEntrega);
    }
    
    if ($dataPost['Usuario_Creador'] != ""){
      $usuarioCreador = '%'.$dataPost['Usuario_Creador'].'%';
      $consulta->bindParam(':usuarioCreador',$usuarioCreador);
    }
    if ($dataPost['Fch_Creación'] != ""){
      $fchCreacion = '%'.$dataPost['Fch_Creación'].'%';
      $consulta->bindParam(':fchCreacion',$fchCreacion);
    }

    if ($dataPost['Usuario_Editor'] != ""){
      $usuarioEditor = '%'.$dataPost['Usuario_Editor'].'%';
      $consulta->bindParam(':usuarioEditor',$usuarioEditor);
    }
    if ($dataPost['Fch_Edición'] != ""){
      $fchEdicion = '%'.$dataPost['Fch_Edición'].'%';
      $consulta->bindParam(':fchEdicion',$fchEdicion);
    }


    $consulta->execute();
    return $consulta->fetchAll();   
  }


  function buscarDataProgramacionPrevia($db, $dataPost){

    $where = "";
    $having = " HAVING 1 ";
    $having .= $dataPost['Cliente'] == "" ? "": " AND `idCliente` like '%".$dataPost['Cliente']."%' ";
    $having .= $dataPost['Conductor'] == "" ? "": " AND `idConductor` like  '%".$dataPost['Conductor']."%' ";
    $having .= $dataPost['Auxiliares'] == "" ? "": " AND `idAuxiliares` like  '%".$dataPost['Auxiliares']."%' ";

    $where .= $dataPost['Fch_Despacho'] == "" ? "": " AND `fchDespacho` like :fchDespacho ";
    $where .= $dataPost['Hra_Ini_Esperada'] == "" ? "": " AND `hraInicioEsperada` like :hraInicioEsperada ";
    $where .= $dataPost['Movil_Asignado'] == "" ? "": " AND `movilAsignado` like :movilAsignado ";
    $where .= $dataPost['Cuenta'] == "" ? "": " AND `programdespacho`.`cuenta` like :cuenta ";
    $where .= $dataPost['Producto'] == "" ? "": " AND `programdespacho`.`nombProducto` like :nombProducto ";
    $where .= $dataPost['Placa'] == "" ? "": " AND `placa` like :placa ";
    $where .= $dataPost['Estado'] == "" ? "": " AND `estadoProgram` like :estadoProgram ";
    $where .= $dataPost['Usuario'] == "" ? "": " AND `creacUsuario` like :creacUsuario ";

 /*   $where .= $dataPost['Fch_Edición'] == "" ? "": " AND `fchUltimoCambio` like :fchEdicion ";
*/
    
    $consulta = $db->prepare("SELECT  id, idCliente, fchDespacho, hraInicioEsperada, movilAsignado, cuenta, nombProducto, placa, idConductor, idAuxiliares, estadoProgram, creacUsuario , creacFch , observacion ,editaUsuario, editaFch  FROM  (SELECT id, fchDespacho, concat(`programdespacho`.idCliente, '-', cliente.nombre ) As idCliente,  `programdespacho`.cuenta,  `programdespacho`.nombProducto, concat(`programdespacho`.correlCuenta, '|', `programdespacho`.idProducto, '|', `programdespacho`.cuenta, '|', `programdespacho`.nombProducto, '|', `programdespacho`.tipoCuenta, '|', `programdespacho`.valorServicio ) As correlCuenta, placa, hraInicioEsperada, movilAsignado, GROUP_CONCAT( if(tipoRol = 'Conductor', nombCompleto,NULL)) AS idConductor,  group_concat(if(tipoRol = 'Auxiliar',  nombCompleto, NULL )  SEPARATOR ', ') AS idAuxiliares, estadoProgram, `programdespacho`.creacUsuario,  `programdespacho`.creacFch , `programdespacho`.observacion ,`programdespacho`.editaUsuario, `programdespacho`.editaFch FROM cliente, `programdespacho` LEFT JOIN (SELECT programdesppers.idProgram, tipoRol, concat( programdesppers.idTrabajador,'-', nombres,' ', apPaterno,' ', apMaterno ) AS nombCompleto FROM programdesppers, trabajador WHERE programdesppers.idTrabajador = trabajador.idTrabajador  ) AS tripulacion ON tripulacion.idProgram = programdespacho.id WHERE `programdespacho`.idCliente = cliente.idRuc $where GROUP BY id ) AS t1 $having ");



    if ($dataPost['Fch_Despacho'] != ""){
      $fchDespacho = '%'.$dataPost['Fch_Despacho'].'%';
      $consulta->bindParam(':fchDespacho',$fchDespacho);
    }
    if ($dataPost['Hra_Ini_Esperada'] != ""){
      $hraInicioEsperada = '%'.$dataPost['Hra_Ini_Esperada'].'%';
      $consulta->bindParam(':hraInicioEsperada',$hraInicioEsperada);
    }

    if ($dataPost['Movil_Asignado'] != ""){
      $movilAsignado = '%'.$dataPost['Movil_Asignado'].'%';
      $consulta->bindParam(':movilAsignado',$movilAsignado);
    }
    if ($dataPost['Cuenta'] != ""){
      $cuenta = '%'.$dataPost['Cuenta'].'%';
      $consulta->bindParam(':cuenta',$cuenta);
    }

    if ($dataPost['Producto'] != ""){
      $nombProducto = '%'.$dataPost['Producto'].'%';
      $consulta->bindParam(':nombProducto',$nombProducto);
    }
    if ($dataPost['Placa'] != ""){
      $placa = '%'.$dataPost['Placa'].'%';
      $consulta->bindParam(':placa',$placa);
    }

    if ($dataPost['Estado'] != ""){
      $estadoProgram = '%'.$dataPost['Estado'].'%';
      $consulta->bindParam(':estadoProgram',$estadoProgram);
    }
    if ($dataPost['Usuario'] != ""){
      $creacUsuario = '%'.$dataPost['Usuario'].'%';
      $consulta->bindParam(':creacUsuario',$creacUsuario);
    }

  /*  if ($dataPost['Usuario_Editor'] != ""){
      $usuarioEditor = '%'.$dataPost['Usuario_Editor'].'%';
      $consulta->bindParam(':usuarioEditor',$usuarioEditor);
    }
    if ($dataPost['Fch_Edición'] != ""){
      $fchEdicion = '%'.$dataPost['Fch_Edición'].'%';
      $consulta->bindParam(':fchEdicion',$fchEdicion);
    }
*/
    $consulta->execute();
    return $consulta->fetchAll();   
  }  

  function traerDatos($db){
    $consulta = $db->prepare("SELECT `documento`, `nombreCompleto`, `nroTelefono`, `eMail`, `estadoTercero`, `bancoNombre`, `bancoCuentaNro`, `bancoCuentaTipo`, `bancoCuentaMoneda`, `cuentaDetraccion`, `usuario`, `fchCreacion`, `editaUsuario`, `editaFch` FROM `vehiculodueno`");
    $consulta->execute();
    return $consulta->fetchAll();
  }









?>
