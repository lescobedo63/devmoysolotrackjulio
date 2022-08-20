<?php
global $servidor, $bd, $usuario, $contrasenia;
$db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);

function buscarListadoRespuestasCheckListReporte($db,$fchIni,$fchFin,$idPlusPlus,$placa){
    if($fchIni != '' && $fchFin == ''){
      // $consulta = $db->prepare("SELECT clm.placa, LEFT((clm.fecha_registro),10) AS fecha, pcm.pregunta, prm.respuesta, prm.observacion 
      //                           FROM pregunta_respuestaMantenimiento prm 
      //                           INNER JOIN preguntasCheckMantenimiento pcm ON prm.idpregunta = pcm.idpregunta 
      //                           INNER JOIN checkListMantenimiento clm ON clm.id_checklist_mantenimiento = prm.id_checklist_mantenimiento
      //                           WHERE LEFT((clm.fecha_registro),10) = :fchIni");

      if($placa != ''){
        $consulta = $db->prepare("SELECT clm.placa, 
                                  LEFT((clm.fecha_registro),10) AS fecha, 
                                  MAX(IF(prm.idpregunta = :idPlusPlus, pcm.pregunta, NULL)) as `pregunta$idPlusPlus`, 
                                  MAX(IF(prm.idpregunta = :idPlusPlus, prm.respuesta, NULL)) AS `respuesta$idPlusPlus`, 
                                  MAX(IF(prm.idpregunta = :idPlusPlus, prm.observacion, NULL)) AS `observacion$idPlusPlus`
                                  FROM 
                                  pregunta_respuestaMantenimiento prm 
                                  LEFT JOIN preguntasCheckMantenimiento pcm ON prm.idpregunta = pcm.idpregunta 
                                  LEFT JOIN checkListMantenimiento clm ON clm.id_checklist_mantenimiento = prm.id_checklist_mantenimiento
                                  WHERE 
                                  LEFT((clm.fecha_registro),10) = :fchIni AND clm.placa = :placa");
        $consulta->bindParam(':idPlusPlus',$idPlusPlus);
        $consulta->bindParam(':placa',$placa);
        $consulta->bindParam(':fchIni',$fchIni);
      }
      else{
        $consulta = $db->prepare("SELECT placa, LEFT((fecha_registro),10) AS fecha FROM checkListMantenimiento WHERE LEFT((fecha_registro),10) = :fchIni");
        $consulta->bindParam(':fchIni',$fchIni);
      }
      $consulta->execute();
    }
    else if($fchIni != '' && $fchFin != ''){
      // $consulta = $db->prepare("SELECT clm.placa, LEFT((clm.fecha_registro),10) AS fecha, pcm.pregunta, prm.respuesta, prm.observacion 
      //                           FROM pregunta_respuestaMantenimiento prm 
      //                           INNER JOIN preguntasCheckMantenimiento pcm ON prm.idpregunta = pcm.idpregunta 
      //                           INNER JOIN checkListMantenimiento clm ON clm.id_checklist_mantenimiento = prm.id_checklist_mantenimiento
      //                           WHERE LEFT((clm.fecha_registro),10) >= :fchIni AND LEFT((clm.fecha_registro),10) <= :fchFin");
      if($placa != ''){
        $consulta = $db->prepare("SELECT clm.placa, 
                                  LEFT((clm.fecha_registro),10) AS fecha, 
                                  MAX(IF(prm.idpregunta = :idPlusPlus, pcm.pregunta, NULL)) as `pregunta$idPlusPlus`, 
                                  MAX(IF(prm.idpregunta = :idPlusPlus, prm.respuesta, NULL)) AS `respuesta$idPlusPlus`, 
                                  MAX(IF(prm.idpregunta = :idPlusPlus, prm.observacion, NULL)) AS `observacion$idPlusPlus`
                                  FROM 
                                  pregunta_respuestaMantenimiento prm 
                                  LEFT JOIN preguntasCheckMantenimiento pcm ON prm.idpregunta = pcm.idpregunta 
                                  LEFT JOIN checkListMantenimiento clm ON clm.id_checklist_mantenimiento = prm.id_checklist_mantenimiento
                                  WHERE 
                                  LEFT((clm.fecha_registro),10) = :fchIni AND clm.placa = :placa");
        $consulta->bindParam(':idPlusPlus',$idPlusPlus);
        $consulta->bindParam(':placa',$placa);
        $consulta->bindParam(':fchIni',$fchIni);
      }
      else{
        // $consulta = $db->prepare("SELECT clm.placa, 
        //                           LEFT((clm.fecha_registro),10) AS fecha
        //                           FROM 
        //                           pregunta_respuestaMantenimiento prm 
        //                           LEFT JOIN preguntasCheckMantenimiento pcm ON prm.idpregunta = pcm.idpregunta 
        //                           LEFT JOIN checkListMantenimiento clm ON clm.id_checklist_mantenimiento = prm.id_checklist_mantenimiento
        //                           WHERE 
        //                           LEFT((clm.fecha_registro),10) >= :fchIni AND LEFT((clm.fecha_registro),10) <= :fchFin GROUP BY clm.placa");

        $consulta = $db->prepare("SELECT placa, LEFT((fecha_registro),10) AS fecha FROM checkListMantenimiento WHERE LEFT((fecha_registro),10) >= :fchIni AND LEFT((fecha_registro),10) <= :fchFin");
        $consulta->bindParam(':fchIni',$fchIni);
        $consulta->bindParam(':fchFin',$fchFin);
      }
      $consulta->execute();
    }
    return $consulta->fetchAll();
}

function buscarListadoCheckListReporte($db,$fchIni,$fchFin){
    if($fchIni != '' && $fchFin == ''){
      $consulta = $db->prepare("SELECT p.fchDespacho, v.idPlaca, IF(p.placa=c.placa, 'COMPLETADO', 'PENDIENTE') AS checklist,
                                pd.NombresO AS trabAsignado,
                                IF(RIGHT((c.fecha_registro),8) IS NULL, '', RIGHT((c.fecha_registro),8)) AS Hora_Registro,
                                c.km_anterior_checklist_mantenimiento,
                                c.km_actual_checklist_mantenimiento,
                                c.km_actual_checklist_mantenimiento - c.km_anterior_checklist_mantenimiento AS diferencia,
                                IF(concat(t.apPaterno,' ',t.apMaterno,', ',t.nombres) IS NULL, '', concat(t.apPaterno,' ',t.apMaterno,', ',t.nombres)) AS trabCompletado
                                FROM vehiculo v
                                INNER JOIN programdespacho p ON v.idPlaca = p.placa
                                JOIN (SELECT pd.idProgram, pd.idTrabajador,
                                IF(concat(t.apPaterno,' ',t.apMaterno,', ',t.nombres) IS NULL, '', concat(t.apPaterno,' ',t.apMaterno,', ',t.nombres)) AS NombresO
                                FROM  programdesppers pd INNER JOIN trabajador t ON t.idTrabajador = pd.idTrabajador WHERE tipoRol = 'Conductor') AS pd ON pd.idProgram = p.id
                                LEFT JOIN checkListMantenimiento c ON p.placa = c.placa AND p.fchDespacho = LEFT((c.fecha_registro),10)
                                LEFT JOIN trabajador t ON t.idTrabajador = c.idTrabajador
                                WHERE
                                v.estado = 'Activo' AND v.considerarPropio = 'Si' AND p.fchDespacho = :fchIni");
      $consulta->bindParam(':fchIni',$fchIni);
      $consulta->execute();
    }
    else if($fchIni != '' && $fchFin != ''){
      $consulta = $db->prepare("SELECT p.fchDespacho, v.idPlaca, IF(p.placa=c.placa, 'COMPLETADO', 'PENDIENTE') AS checklist,
                                pd.NombresO AS trabAsignado,
                                IF(RIGHT((c.fecha_registro),8) IS NULL, '', RIGHT((c.fecha_registro),8)) AS Hora_Registro,
                                c.km_anterior_checklist_mantenimiento,
                                c.km_actual_checklist_mantenimiento,
                                c.km_actual_checklist_mantenimiento - c.km_anterior_checklist_mantenimiento AS diferencia,
                                IF(concat(t.apPaterno,' ',t.apMaterno,', ',t.nombres) IS NULL, '', concat(t.apPaterno,' ',t.apMaterno,', ',t.nombres)) AS trabCompletado
                                FROM vehiculo v
                                INNER JOIN programdespacho p ON v.idPlaca = p.placa
                                JOIN (SELECT pd.idProgram, pd.idTrabajador,
                                IF(concat(t.apPaterno,' ',t.apMaterno,', ',t.nombres) IS NULL, '', concat(t.apPaterno,' ',t.apMaterno,', ',t.nombres)) AS NombresO
                                FROM  programdesppers pd INNER JOIN trabajador t ON t.idTrabajador = pd.idTrabajador WHERE tipoRol = 'Conductor') AS pd ON pd.idProgram = p.id
                                LEFT JOIN checkListMantenimiento c ON p.placa = c.placa AND p.fchDespacho = LEFT((c.fecha_registro),10)
                                LEFT JOIN trabajador t ON t.idTrabajador = c.idTrabajador
                                WHERE
                                v.estado = 'Activo' AND v.considerarPropio = 'Si' AND p.fchDespacho >= :fchIni AND p.fchDespacho <= :fchFin");
      $consulta->bindParam(':fchIni',$fchIni);
      $consulta->bindParam(':fchFin',$fchFin);
      $consulta->execute();
    }
    return $consulta->fetchAll();
}

function buscarListadoFallasCheckListReporte($db){
  $consulta = $db->prepare("SELECT chlm.placa, pchlm.pregunta, prchlm.respuesta, prchlm.observacion
                            FROM checkListMantenimiento chlm 
                            INNER JOIN pregunta_respuestaMantenimiento prchlm 
                            ON chlm.id_checklist_mantenimiento = prchlm.id_checklist_mantenimiento 
                            INNER JOIN preguntasCheckMantenimiento pchlm 
                            ON prchlm.idpregunta = pchlm.idpregunta
                            WHERE 
                            LEFT((chlm.fecha_registro),10) = curdate() AND prchlm.respuesta = 'NO' 
                            OR 
                            LEFT((chlm.fecha_registro),10) = curdate() AND prchlm.respuesta = 'REGULAR'
                            OR 
                            LEFT((chlm.fecha_registro),10) = curdate() AND prchlm.respuesta = 'MAL'  
                            ORDER BY `chlm`.`placa` ASC");
  $consulta->execute();
  return $consulta->fetchAll();
}

?>