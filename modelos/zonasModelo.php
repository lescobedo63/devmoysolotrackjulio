<?php
global $servidor, $bd, $usuario, $contrasenia;
$db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);


function buscarUbicaciones($db,$idUbicacion =null,$idZona = null){
  $where = "";
  if ($idUbicacion != null){
    $auxArray = explode("-", $idUbicacion);
    $idUbicacion = $auxArray[0];
    $where .= " AND idUbicacion = :idUbicacion ";
  }

  if ($idZona != null){
    $auxArray = explode("-", $idZona);
    $idZona = $auxArray[0];
    $where .= " AND ubizona.idZona = :idZona ";
  } 

  if ($idZona != null) $where .= " AND ubizona.idZona = :idZona "; 
  
  $consulta = $db->prepare("SELECT `idUbicacion`, ubicacion.`descripcion`, `codPostal`,`fchCreacion`, `ubicacion`.`idZona`, `ubizona`.`descripcion` as `zona` FROM `ubicacion`, `ubizona` WHERE `ubicacion`.idZona = `ubizona`.idZona  $where  ORDER BY  `idUbicacion` "); 
  if ($idUbicacion != null) $consulta->bindParam(':idUbicacion',$idUbicacion);
  if ($idZona != null) $consulta->bindParam(':idZona',$idZona);

  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarDistancias($db,$idOrigen){
  $consulta = $db->prepare("SELECT `idOrigen`, `idDestino`, `distaKm`, `idDetalle`, `ubicacion`.`descripcion`, `ubizona`.`descripcion` as zona FROM `ubidista`, `ubicacion`, `ubizona` WHERE `ubidista`.idDestino = `ubicacion`.idUbicacion AND `ubicacion`.`idZona` = `ubizona`.`idZona` AND `idOrigen` = :idOrigen  ORDER BY  `idDestino` "); 
  $consulta->bindParam(':idOrigen',$idOrigen);
  $consulta->execute();
  return $consulta->fetchAll();
};

function  buscarUltIdUbicacion($db){
  $consulta = $db->prepare("SELECT idUbicacion FROM `ubicacion` ORDER BY idUbicacion Desc Limit 1");
  $consulta->execute();
  return $consulta->fetchAll();
};

function buscarZonas($db){
  $consulta = $db->prepare("SELECT idZona, descripcion FROM `ubizona`");
  $consulta->execute();
  return $consulta->fetchAll();
}

function insertarNuevaUbicacion($db,$idUbicacion,$descrip,$codPostal,$zona){
  $inserta = $db->prepare("INSERT INTO `ubicacion` (`idUbicacion`, `descripcion`, `codPostal`, `idZona`, `fchCreacion`) VALUES (:idUbicacion, :descripcion, :codPostal, :zona, NOW())");
  $inserta->bindParam(':idUbicacion',$idUbicacion);
  $inserta->bindParam(':descripcion',$descrip);
  $inserta->bindParam(':codPostal',$codPostal);
  $inserta->bindParam(':zona',$zona);
  $inserta->execute();
}

function eliminaUnaUbicacion($db,$idUbicacion){
  $elimina = $db->prepare(" DELETE FROM `ubicacion` WHERE `idUbicacion` = :ubicacion ");
  $elimina->bindParam(':ubicacion',$idUbicacion);
  $elimina->execute();
}

function buscarSedes($db,$idSede = null,$idZona = null){
  $where = "";
  if ($idSede != null){
    $auxArray = explode("-", $idSede);
    $idSede = $auxArray[0];
    $where .= " AND ubisede.idSede = :idSede ";
  }
  //$consulta = $db->prepare("SELECT `idSede`, `descripcion`, `idUbicacion` FROM `ubisede` ");
  $consulta = $db->prepare("SELECT `ubisede`.`idSede`, `ubisede`.`descripcion`, concat(`ubisede`.`idUbicacion`,' - ',`ubicacion`.`descripcion`) AS descripUbi,`ubisede`.`idUbicacion`, `ubisede`.`creacUsuario`, `ubisede`.`creacFch`, `ubizona`.`descripcion` AS descripZona, ubisede.creacUsuario  FROM `ubisede`, `ubicacion`, `ubizona` WHERE `ubisede`.`IdUbicacion` =  `ubicacion`.`IdUbicacion` AND `ubicacion`.`IdZona` = `ubizona`.`IdZona` $where");  
  if ($idSede != null) $consulta->bindParam(':idSede',$idSede);

  $consulta->execute();
  return $consulta->fetchAll();
};

function  buscarUltIdSede($db){
  $consulta = $db->prepare("SELECT idSede FROM `ubisede` ORDER BY idSede Desc Limit 1");
  $consulta->execute();
  return $consulta->fetchAll();
};

function insertarNuevaSede($db,$idSede,$descrip,$ubicacion){
  $usuario = $_SESSION['usuario'];
  $inserta = $db->prepare("INSERT INTO `ubisede` (`idSede`, `descripcion`, `idUbicacion`, `creacUsuario`, `creacFch`) VALUES (:idSede, :descrip, :ubicacion, :usuario, NOW())");
  $inserta->bindParam(':idSede',$idSede);
  $inserta->bindParam(':descrip',$descrip);
  $inserta->bindParam(':ubicacion',$ubicacion);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->execute();
}

function editaUnaSede($db,$idSede,$descrip,$ubicacion){
  $actualiza = $db->prepare("UPDATE `ubisede` SET `descripcion` = :descripcion, idUbicacion = :idUbicacion WHERE `idSede` = :idSede");
  $actualiza->bindParam(':descripcion',$descrip);
  $actualiza->bindParam(':idUbicacion',$ubicacion);  
  $actualiza->bindParam(':idSede',$idSede);
  $actualiza->execute(); 
};


function eliminaUnaSede($db,$idSede){
  $elimina = $db->prepare("DELETE FROM `ubisede` WHERE `idSede` = :ubisede ");
  $elimina->bindParam(':ubisede',$idSede);
  $elimina->execute();
}

function buscarUbiDetalles($db){
  $consulta = $db->prepare("SELECT `idDetalle`, `descripcion`, `distaMax` FROM `ubidetalle`");
  $consulta->execute();
  return $consulta->fetchAll();
}

function buscarDistancia($db,$puntoOrigen,$puntoDestino){
  $consulta = $db->prepare("SELECT `idOrigen` , `idDestino` , `distaKm` , `idDetalle` , `creacUsuario` , `creacFch` FROM `ubidista` WHERE `idOrigen` LIKE :idOrigen AND `idDestino` LIKE :idDestino");
  $consulta->bindParam(':idOrigen',$puntoOrigen);
  $consulta->bindParam(':idDestino',$puntoDestino);
  $consulta->execute();
  return $consulta->fetchAll();
}

?>
