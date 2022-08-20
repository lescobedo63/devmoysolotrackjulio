<?php
  global $servidor, $bd, $usuario, $contrasenia;
  $db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);

  function buscarColores($db, $idColor = NULL){
    $where = " WHERE 1 ";
    $where .= ($idColor != NULL)?" AND idColor = :idColor  ":"";
	  $consulta = $db->prepare("SELECT `idColor`, `descripColor`, `creacUsuario`, `creacFch`, `editaUsuario`, `editaFch` FROM `unifcolores` $where  ");
    if ($idColor != NULL) $consulta->bindParam(':idColor',$idColor);
	  $consulta->execute();
	  return $consulta->fetchAll();
  }

  function generaIdColor($db){
    $consulta = $db->prepare("SELECT `idColor` FROM `unifcolores` ORDER BY `idColor` DESC LIMIT 1 ");
    $consulta->execute();
    $result = $consulta->fetchAll();
    $nvoNro = "000";
    // Se obtiene el resultado de la consulta
    foreach($result as $fila) {
      $ultId = $fila['idColor'];
      $nvoNro = 1*$ultId + 1;
    }
    return substr("000".$nvoNro,-3);
  }

  function insertaColor($db,$id,$nombColor){
    $usuario = $_SESSION['usuario'];
    $inserta = $db->prepare("INSERT INTO `unifcolores` (`idColor`, `descripColor`, `creacUsuario`, `creacFch`) VALUES (:id, :nombColor, :usuario, now());");
    $inserta->bindParam(':id',$id);
    $inserta->bindParam(':nombColor',$nombColor);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function actualizaColor($db,$id,$nombColor){
    $usuario = $_SESSION['usuario'];
    $actualiza = $db->prepare("UPDATE `unifcolores` SET `descripColor` = :nombColor, `editaUsuario` = :usuario, `editaFch` = curdate() WHERE `idColor` = :id;");
    $actualiza->bindParam(':id',$id);
    $actualiza->bindParam(':nombColor',$nombColor);
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->execute();
    return $actualiza->rowCount();
  }
  
  function eliminarColor($db,$id){
    $elimina = $db->prepare("DELETE FROM  `unifcolores` WHERE `idColor` = :id;");
    $elimina->bindParam(':id',$id);
    $elimina->execute();
    return $elimina->rowCount();
  }

  function buscarTallas($db, $idTalla = NULL){
    $where = " WHERE 1 ";
    $where .= ($idTalla != NULL)?" AND idTalla = :idTalla  ":"";
	  $consulta = $db->prepare("SELECT `idTalla`, `descripTalla`, `creacUsuario`, `creacFch`, `editaUsuario`, `editaFch` FROM `uniftallas` $where ");
    if ($idTalla != NULL) $consulta->bindParam(':idTalla',$idTalla);
	  $consulta->execute();
	  return $consulta->fetchAll();
  }

  function generaIdTalla($db){
    $consulta = $db->prepare("SELECT `idTalla` FROM `uniftallas` ORDER BY `idTalla` DESC LIMIT 1 ");
    $consulta->execute();
    $result = $consulta->fetchAll();
    $nvoNro = "000";
    // Se obtiene el resultado de la consulta
    foreach($result as $fila) {
      $ultId = $fila['idTalla'];
      $nvoNro = 1*$ultId + 1;
    }
    return substr("000".$nvoNro,-3);
  }

  function insertaTalla($db,$id,$talla){
    $usuario = $_SESSION['usuario'];
    $inserta = $db->prepare("INSERT INTO `uniftallas` (`idTalla`, `descripTalla`, `creacUsuario`, `creacFch`) VALUES (:id, :talla, :usuario, now());");
    $inserta->bindParam(':id',$id);
    $inserta->bindParam(':talla',$talla);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }  

  function actualizaTalla($db,$id,$talla){
    $usuario = $_SESSION['usuario'];
    $actualiza = $db->prepare("UPDATE `uniftallas` SET `descripTalla` = :talla, `editaUsuario` = :usuario, `editaFch` = curdate() WHERE `idTalla` = :id;");
    $actualiza->bindParam(':id',$id);
    $actualiza->bindParam(':talla',$talla);
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function eliminarTalla($db,$id){
    $elimina = $db->prepare("DELETE FROM  `uniftallas` WHERE `idTalla` = :id;");
    $elimina->bindParam(':id',$id);
    $elimina->execute();
    return $elimina->rowCount();
  }


  function buscarTiposPrenda($db,$id = NULL){
    $where = ($id == NULL)?"":" AND idTipo = :idTipo ";
	  $consulta = $db->prepare("SELECT `idTipo`, `nombreTipo`, `descripcion`, `creacUsuario`, `creacFch`, `editaUsuario`, `editaFch` FROM `uniftiposprenda` WHERE 1 $where ");
    if ($id != NULL)$consulta->bindParam(':idTipo',$id);
	  $consulta->execute();
	  return $consulta->fetchAll();
  }

  function generaIdTipoPrenda($db){
    $consulta = $db->prepare("SELECT `idTipo` FROM `uniftiposprenda` ORDER BY `idTipo` DESC LIMIT 1 ");
    $consulta->execute();
    $result = $consulta->fetchAll();
    $nvoNro = "000";
    // Se obtiene el resultado de la consulta
    foreach($result as $fila) {
      $ultId = $fila['idTipo'];
      $nvoNro = 1*$ultId + 1;
    }
    return substr("000".$nvoNro,-3);
  }

  function insertaTipoPrenda($db,$nvoNro,$nombreTipoPrenda,$descripcion){
    $usuario = $_SESSION['usuario'];
    $inserta = $db->prepare("REPLACE INTO `uniftiposprenda` (`idTipo`, `nombreTipo`, `descripcion`,`creacUsuario`, `creacFch`) VALUES (:idTipo, :nombre, :descripcion, :usuario, now());");
    $inserta->bindParam(':idTipo',$nvoNro);
    $inserta->bindParam(':nombre',$nombreTipoPrenda);
    $inserta->bindParam(':descripcion',$descripcion);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();

  }

  function insertaColorTipoPrenda($db,$idTipoPrenda,$idColor){
    $usuario = $_SESSION['usuario'];
    $inserta = $db->prepare("REPLACE INTO `uniftpcolores` (`idTipoPrenda`, `idColor`, `creacUsuario`, `creacFch`) VALUES (:idTipoPrenda, :idColor, :usuario, now());");
    $inserta->bindParam(':idTipoPrenda',$idTipoPrenda);
    $inserta->bindParam(':idColor',$idColor);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function insertaTallaTipoPrenda($db,$idTipoPrenda,$idTalla){
    $usuario = $_SESSION['usuario'];
    $inserta = $db->prepare("REPLACE INTO `uniftptallas` (`idTipoPrenda`, `idTalla`, `creacUsuario`, `creacFch`) VALUES (:idTipoPrenda, :idTalla, :usuario, now() );");
    $inserta->bindParam(':idTipoPrenda',$idTipoPrenda);
    $inserta->bindParam(':idTalla',$idTalla);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function eliminaColorTipoPrenda($db,$idTipoPrenda = NULL){
    $where = ($idTipoPrenda != NULL)?" AND idTipoPrenda = :idTipoPrenda ":"";
    $elimina = $db->prepare("DELETE FROM `uniftpcolores` WHERE 1 $where");
    if ($idTipoPrenda != NULL) $elimina->bindParam(':idTipoPrenda',$idTipoPrenda);
    $elimina->execute();
    return $elimina->rowCount();

  }
    
  function eliminaTallaTipoPrenda($db,$idTipoPrenda = NULL){
    $where = ($idTipoPrenda != NULL)?" AND idTipoPrenda = :idTipoPrenda ":"";
    $elimina = $db->prepare("DELETE FROM `uniftptallas` WHERE 1 $where");
    if ($idTipoPrenda != NULL) $elimina->bindParam(':idTipoPrenda',$idTipoPrenda);
    $elimina->execute();
    return $elimina->rowCount();

  }

  function buscarTpColores($db,$id){
    $consulta = $db->prepare("SELECT `uniftpcolores`.`idTipoPrenda`, `uniftpcolores`.`idColor`, descripColor, `uniftpcolores`.`creacUsuario`, `uniftpcolores`.`creacFch`  FROM `uniftpcolores`, unifcolores WHERE  `uniftpcolores`.idColor = unifcolores.idColor  AND `idTipoPrenda` = :idTipoPrenda");
    $consulta->bindParam(':idTipoPrenda',$id);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function buscarTpTallas($db,$id){
    $consulta = $db->prepare("SELECT `uniftptallas`.`idTipoPrenda`, `uniftptallas`.`idTalla`, descripTalla, `uniftptallas`.`creacUsuario`, `uniftptallas`.`creacFch`  FROM `uniftptallas`, uniftallas WHERE  `uniftptallas`.idTalla = uniftallas.idTalla  AND `idTipoPrenda` = :idTipoPrenda");
    $consulta->bindParam(':idTipoPrenda',$id);
    $consulta->execute();
    return $consulta->fetchAll();
  }


  function buscarTpColoresNoUsados($db,$id){
    $consulta = $db->prepare("SELECT `unifcolores`.idColor AS idCol, `unifcolores`.descripColor, `uniftpcolores`.idColor FROM unifcolores LEFT JOIN `uniftpcolores` ON `unifcolores`.idColor = `uniftpcolores`.idColor AND `uniftpcolores`.idTipoPrenda = :idTipoPrenda WHERE `uniftpcolores`.idColor IS  null");
    $consulta->bindParam(':idTipoPrenda',$id);
    $consulta->execute();
    return $consulta->fetchAll();    
  }

  function buscarTpTallasNoUsadas($db,$id){
    $consulta = $db->prepare("SELECT `uniftallas`.idTalla AS idTal, `uniftallas`.descripTalla, `uniftptallas`.idTalla FROM uniftallas LEFT JOIN `uniftptallas` ON `uniftallas`.idTalla = `uniftptallas`.idTalla AND `uniftptallas`.idTipoPrenda = :idTipoPrenda WHERE `uniftptallas`.idTalla IS  null");
    $consulta->bindParam(':idTipoPrenda',$id);
    $consulta->execute();
    return $consulta->fetchAll();    
  }

  function eliminaTPrenda($db,$idTp){
    $elimina = $db->prepare("DELETE FROM `uniftiposprenda` WHERE idTipo = :idTipoPrenda  ");
    $elimina->bindParam(':idTipoPrenda',$idTp);
    $elimina->execute();
    return $elimina->rowCount();
  }

  function insertaUnifEntrega($db,$idArt,$fchMov,$entSal,$tipo,$docum,$detalle,$cant){
  $idMov = generaIdUnifEntrega($db);
  $valorSaldoAnt = $cantSaldoAnt = $ctoUnitSaldoAnt = 0;
  $ultMovim = buscarUltimoUnifEntr($db,$idArt);
  $ctoUnit = $_SESSION['ctoUnit'];

  foreach ($ultMovim As $item){
    $cantSaldoAnt =  $item['cantSaldo'];
    $valorSaldoAnt =  $item['valorSaldo'];
    $ctoUnitSaldoAnt = round($valorSaldoAnt/$cantSaldoAnt,2);
    $ctoUnitSaldoAnt = round($valorSaldoAnt/$cantSaldoAnt,2);
  }
  if ($entSal == 'Ent'){
    $cantSaldo = $cantSaldoAnt + $cant;
    $valorSaldo = round($valorSaldoAnt + $ctoUnit*$cant,2);
  } else {
    $cantSaldo = $cantSaldoAnt - $cant;
    $ctoUnit = $ctoUnitSaldoAnt;
    $valorSaldo = round($valorSaldoAnt - $ctoUnit*$cant,2);
  }
  $entSalTipo = $tipo.'-'.$entSal;
  $usuario = $_SESSION["usuario"];
  //echo "idMov $idMov, idArt $idArt,fchmov $fchMov, entSalTipo $entSalTipo, docum $docum, detalle $detalle, ctounit $ctoUnit, cant $cant, cantSaldo $cantSaldo, valorSaldo $valorSaldo";

  $inserta = $db->prepare("INSERT INTO `unifentregas` (`idMov`, `idArt`, `fchMov`, `documtoMov`, `detalleMov`, `ctoUnit`, `cant`, `cantRecibido`,`entSal`, `tipo`,`entSalTipo`, `cantSaldo`,`valorSaldo` ,`creacFch`, `creacUsuario`) VALUES (:idMov,:idArt, :fchMov, :docum, :detalle, :ctoUnit,:cant,:cant,:entSal,:tipo, :entSalTipo,:cantSaldo,  :valorSaldo,  NOW(), :usuario)");
  $inserta->bindParam(':idMov',$idMov);
  $inserta->bindParam(':idArt',$idArt);
  $inserta->bindParam(':fchMov',$fchMov);
  $inserta->bindParam(':docum',$docum);
  $inserta->bindParam(':detalle',$detalle);
  $inserta->bindParam(':tipo',$tipo);
  $inserta->bindParam(':entSal',$entSal);
  $inserta->bindParam(':entSalTipo',$entSalTipo);
  $inserta->bindParam(':ctoUnit',$ctoUnit);
  $inserta->bindParam(':cant',$cant);
  $inserta->bindParam(':cantSaldo',$cantSaldo);
  $inserta->bindParam(':valorSaldo',$valorSaldo);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();
  return $inserta->rowCount(); 
};

function generaIdUnifEntrega($db){
  $anhio = DATE('Y');
  $consulta = $db->prepare("SELECT `idMov` FROM `unifentregas` ORDER BY `idMov` DESC Limit 1");
  $consulta->execute();
  $resultado = $consulta->fetchAll();
  foreach($resultado as $item) {
    $ultIdMov = $item['idMov'];
    $anhioMov = substr($ultIdMov,0,4);
    $nroMov   = 1*substr($ultIdMov,4,6);
    if ($anhio == $anhioMov)
      return $anhioMov.substr("000000".(1+$nroMov),-6);
  }
  return $anhio."000001";
};

function buscarUltimoUnifEntr($db,$idArt){
  $consulta = $db->prepare("SELECT `idMov`, `fchMov`, `documtoMov`, `detalleMov`, `ctoUnit`, `cant`, `cantSaldo`,  `valorSaldo` FROM `unifentregas` WHERE idArt  = :idArt ORDER BY `idMov` DESC Limit 1  ");
  $consulta->bindParam(':idArt',$idArt);
  $consulta->execute();
  return $consulta->fetchAll(); 
};

function buscarDetalleEntregaPrendas($db,$idMov = NULL,$idArt = NULL,$idTrabaj = NULL, $fchRealiz = NULL,$limite = NULL){
  $where = "";
  $where .= ($idTrabaj != NULL)?" AND  `unifentregasdetalle`.`idTrabajador` = :idTrabaj ":"";
  $where .= ($idArt != NULL)?" AND   `unifentregas`.idArt = :idArt ":"";
  $where .= ($fchRealiz != NULL)?" AND  fchRealiz = :fchRealiz ":"";


  if ($limite == null) $lim = ""; else $lim = " LIMIT $limite ";


  $consulta = $db->prepare("SELECT `unifentregasdetalle`.`idMov`, `correlativo`, `unifentregas`.idArt, `articulo`.descripcion , `unifentregasdetalle`.`idTrabajador`, concat(apPaterno,' ',apMaterno,' ',nombres) AS nombCompleto , `fchRealiz`, `unifentregasdetalle`.`cantidad`, `unifentregas`.`cant`, `unifentregas`.`cantRecibido`, `unifentregasdetalle`.`tipoEntrega`,`unifentregasdetalle`.`precUnit`, `unifentregasdetalle`.`creacUsuario`, `unifentregasdetalle`.`creacFch` FROM `unifentregasdetalle`, unifentregas, trabajador, articulo WHERE `unifentregas`.idArt = `articulo`.idArt AND `unifentregasdetalle`.idMov = `unifentregas`.idMov AND `unifentregasdetalle`.idTrabajador = `trabajador`.idTrabajador $where ORDER BY  `articulo`.idArt, `unifentregasdetalle`.`idMov`, `correlativo` $lim");
  if ($idTrabaj != NULL) $consulta->bindParam(':idTrabaj',$idTrabaj);
  if ($idArt != NULL) $consulta->bindParam(':idArt',$idArt);
  if ($fchRealiz != NULL) $consulta->bindParam(':fchRealiz',$fchRealiz);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarPrendasEnt($db){
  $consulta = $db->prepare("SELECT `idMov`, `unifentregas`.`idArt`, `articulo`.descripcion,  `fchMov`, `documtoMov`, `detalleMov`, `ctoUnit`, `cant`, `cantSaldo`, `articulo`.`precUnit`, `articulo`.`precAux` FROM `unifentregas`, articulo WHERE `unifentregas`.idArt = `articulo`.idArt AND `articulo`.auxSubgrupo = 'UNIFORMES'  AND `cant` > 0  ORDER BY `idArt`, `idMov`");
  $consulta->execute();
  return $consulta->fetchAll();

}

function insertaEntregaDetalle($db,$idMov,$idTrabajador,$fchEnt,$nroDoc,$precUnit,$tipoEntrega){
  $usuario = $_SESSION['usuario'];
  $consulta = $db->prepare("SELECT `correlativo`  FROM `unifentregasdetalle` WHERE `idMov` LIKE :idMov ORDER BY `correlativo` DESC LIMIT 1");
  $consulta->bindParam(':idMov',$idMov);
  $consulta->execute();
  $result = $consulta->fetchAll();
  $correl = 1;
  foreach ($result AS $item) {
    $antCorr = 1*$item['correlativo'];
    $correl = 1+ $antCorr;
  }

  $nroDoc = str_replace("\r\n","",$nroDoc);
  $inserta = $db->prepare("INSERT INTO `unifentregasdetalle` (`idMov`, `correlativo`, `idTrabajador`, `fchRealiz`, `precUnit`, `tipoEntrega`, `cantidad`, `nroDoc`, `creacUsuario`, `creacFch`) VALUES (:idMov, '$correl', :idTrabajador, :fchEnt, :precUnit, :tipoEntrega, '1', :nroDoc, :usuario, now())");
  $inserta->bindParam(':idMov',$idMov);
  $inserta->bindParam(':idTrabajador',$idTrabajador);
  $inserta->bindParam(':fchEnt',$fchEnt);
  $inserta->bindParam(':nroDoc',$nroDoc);
  $inserta->bindParam(':precUnit',$precUnit);
  $inserta->bindParam(':tipoEntrega',$tipoEntrega);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();
  
  if ($inserta->rowCount() == '1'){
    $actualiza = $db->prepare("UPDATE unifentregas set cant=cant-1 where idMov = $idMov");
    $actualiza->execute();
    return $actualiza->rowCount(); 
  }
  return 0;
}

function paraEliminarEntrega($db,$idMov,$correlativo){
  $consulta = $db->prepare("SELECT `unifentregasdetalle`.precUnit, `unifentregasdetalle`.tipoEntrega, `unifentregasdetalle`.nroDoc , `articulo`.idArt, `articulo`.descripcion AS descripArticulo,  `prestamodetalle`.monto, `prestamodetalle`.nroCuota, `prestamodetalle`.descripcion, `prestamodetalle`.fchPago, `prestamodetalle`.montoCuota, `prestamodetalle`.pagado, `prestamo`.idTrabajador FROM unifentregas, articulo,  `unifentregasdetalle` LEFT JOIN prestamo  ON `unifentregasdetalle`.nroDoc = `prestamo`.codigo LEFT JOIN prestamodetalle ON `prestamo`.descripcion = `prestamodetalle`.descripcion AND `prestamo`.monto = `prestamodetalle`.monto AND `prestamo`.idTrabajador = `prestamodetalle`.idTrabajador AND `prestamo`.tipoItem = `prestamodetalle`.tipoItem WHERE `unifentregasdetalle`.idMov = `unifentregas`.idMov AND `unifentregas`.idArt = `articulo`.idArt  AND  `unifentregasdetalle`.`idMov` LIKE :idMov AND `correlativo` = :correlativo");
  $consulta->bindParam(':idMov',$idMov);
  $consulta->bindParam(':correlativo',$correlativo);
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscaIdDespachoTercero($db,$dni,$fchEnt){
  $fchIni = date('Y-m-d',time()-(20*24*60*60));  //20 días hacia atrás
  $consulta = $db->prepare("SELECT placa, `despachovehiculotercero`.fchDespacho, `despachovehiculotercero`.correlativo FROM `despachovehiculotercero` , `despachopersonal` WHERE  `despachovehiculotercero`.fchDespacho = `despachopersonal`.fchDespacho AND  `despachovehiculotercero`.correlativo = `despachopersonal`.correlativo AND despachovehiculotercero.`fchDespacho` >= '$fchIni' AND despachovehiculotercero.`fchDespacho` <= :fchEnt AND `despachovehiculotercero`.`docPagoTercero` IS NULL AND `idTrabajador` LIKE :dni order by fchDespacho Desc,correlativo LIMIT 1");
  $consulta->bindParam(':dni',$dni);
  $consulta->bindParam(':fchEnt',$fchEnt);
  $consulta->execute();
  return $consulta->fetchAll();
}

?>
