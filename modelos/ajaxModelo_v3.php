<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  # Cargar librerias y cosas necesarias
  require_once('../librerias/PhpSpreadsheet-master1_12/vendor/autoload.php');
  # Indicar que usaremos el IOFactory
  use PhpOffice\PhpSpreadsheet\IOFactory;

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

  function resumenQuincenaPlanillaVarios($db,$fchQuincena,$entregado = 'Si', $idTrabajador = NULL){
    //OJO: le he añadido pagoEsSalud (similar a pagoEsSaludVida) por compatibilidad con otra función que necesitaba
    $where = ($idTrabajador == NULL)?"":" AND idTrabajador = :idTrabajador "; 
    $consulta = $db->prepare("SELECT `idTrabajador`, `fchPago`, sum(if(`tipoItem`= 'Gratificacion',`monto`,0)) AS pagoGratificacion
      , sum(if(`tipoItem`= 'Vacaciones',`monto`,0)) AS pagoVacaciones, sum(if(`tipoItem`= 'AjusteVacac',`monto`,0)) AS ajusteVacac
      , sum(if(`tipoItem`= 'CTS',`monto`,0)) AS pagoCts             , sum(if(`tipoItem`= 'Pension',`monto`,0)) AS pagoPension
      , sum(if(`tipoItem`= 'EsSalud',`monto`,0)) AS pagoEsSaludVida , sum(if(`tipoItem`= 'Prestamo',`monto`,0)) AS reciboPrestamo
      , sum(if(`tipoItem`= 'EsSalud',`monto`,0)) AS pagoEsSalud     , sum(if(`tipoItem`= 'HaberBasico',`monto`,0)) AS haberBasico
      , sum(if(`tipoItem`= 'MovilSuped',`monto`,0)) AS movilSuped   , sum(if(`tipoItem`= 'DiasMovilSuped',`monto`,0)) AS diasMovilSuped
      , sum(if(`tipoItem`= 'BonifVarias',`monto`,0)) AS bonifVarias , sum(if(`tipoItem`= 'TotalGravable',`monto`,0)) AS totalGravable
      , sum(if(`tipoItem`= 'CostoDia',`monto`,0)) AS costoDia       , sum(if(`tipoItem`= 'ServAdicional',`monto`,0)) AS servAdicional
      , sum(if(`tipoItem`= 'DescMedico',`monto`,0)) AS descMedico   , sum(if(`tipoItem`= 'Inasistencia',`monto`,0)) AS inasistencia
      , sum(if(`tipoItem`= 'AsigFam',`monto`,0)) AS asigFam         , sum(if(`tipoItem`= 'Reembolso',`monto`,0)) AS reembolso
      , sum(if(`tipoItem`= 'Bono',`monto`,0)) AS bono               , sum(if(`tipoItem`= 'Movilidad',`monto`,0)) AS movilidad 
      , sum(if(`tipoItem`= 'Adelanto',`monto`,0)) AS adelanto       , sum(if(`tipoItem`= 'Indemnizacion',`monto`,0)) AS pagoIndemnizacion
      , sum(if(`tipoItem`= 'Participacion',`monto`,0)) AS pagoParticipacion , sum(if(`tipoItem`= 'AdelantoQ',`monto`,0)) AS pagoAdelantoQ
      , sum(if(`tipoItem`= 'DevolGarantia',`monto`,0)) AS devGarantia , sum(if(`tipoItem`= 'DomFeriado',`monto`,0)) AS domFeriado
      , sum(if(`tipoItem`= 'PnsPriPrima',`monto`,0)) AS pnsPriPrima   , sum(if(`tipoItem`= 'PnsPriComis',`monto`,0)) AS pnsPriComis
      , sum(if(`tipoItem`= 'PnsPriAport',`monto`,0)) AS pnsPriAport   , sum(if(`tipoItem`= 'PnsNacONP',`monto`,0)) AS pnsNacONP
      , sum(if(`tipoItem`= 'LicGoce',`monto`,0)) AS licGoce, sum(if(`tipoItem`= 'MovilAsig',`monto`,0)) AS movilAsig
      , sum(if(`tipoItem`= 'BonoProducMes',`monto`,0)) AS bonoProducMes 
       FROM `prestamo` WHERE `fchQuincena` = :fchQuincena $where AND  `entregado`= :entregado group by `idTrabajador` order by `idTrabajador` ");
    $consulta->bindParam(':fchQuincena',$fchQuincena);
    $consulta->bindParam(':entregado',$entregado);
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

  function resumenQuincenaPlanillaDespachos($db,$fchPago,$pagado = 'Si', $categ = NULL, $idTrabajador = NULL){
    $where = "";
    $where .= ($categ == NULL)?"":" AND `categTrabajador` = :categ "; 
    $where .= ($idTrabajador == NULL)?"":" AND `despachopersonal`.`idTrabajador` = :idTrabajador "; 

    $consulta = $db->prepare("SELECT count(DISTINCT `despachopersonal`.fchDespacho) AS diasTrabaj, SEC_TO_TIME(SUM(TIME_TO_SEC( timediff(concat( `despachopersonal`.trabFchDespachoFin,' ',`despachopersonal`.trabHraFin) , concat(`despachopersonal`.fchDespacho,' ',`despachopersonal`.trabHraInicio))))) AS hrsTrabaj , `despachopersonal`.`idTrabajador`, concat(`trabajador`.`apPaterno`,' ',`trabajador`.`apMaterno`,', ', `trabajador`.`nombres` ) as nombreCompleto, count(*) as viajes , sum(`valorRol` + `valorAdicional`) as valorRol, `despachopersonal`.`fchPago`, `categTrabajador`, `modoSueldo`, entidadPension, remuneracionBasica, asignacionFamiliar, entidadPension, tipoComision, sum(valorHraExtra) AS valorHraExtra FROM `despachopersonal`, trabajador WHERE `despachopersonal`.`idTrabajador` = `trabajador`.`idTrabajador` AND  `despachopersonal`.`pagado`= :pagado AND `despachopersonal`.`fchPago` = :fchPago $where  group by  `trabajador`.`idTrabajador` order by `trabajador`.`apPaterno` ");
    $consulta->bindParam(':fchPago',$fchPago);
    $consulta->bindParam(':pagado',$pagado);
    if ($categ != NULL) $consulta->bindParam(':categ',$categ);
    if ($idTrabajador != NULL) $consulta->bindParam(':idTrabajador',$idTrabajador);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function buscarTrabajador($db,$dni){
    $consulta = $db->prepare("SELECT `idTrabajador`, `tipoDocTrab`,  `tipoTrabajador`, `categTrabajador`, `modoSueldo` , `apPaterno`, `apMaterno`, `nombres`, concat( `nombres`,' ',`apPaterno`,' ', `apMaterno`) as nombreCompleto , `ruc`, `estadoTrabajador`, `fchNacimiento`, `imgTrabajador`, `telfMovil`, `dirCalleyNro`, `dirDistrito`, `dirProvincia`, `dirDepartamento`, `dirTelefono`, `dirObservacion`,  `dirNro`, `dirUrb`, `telfAdicional`, `telfReferencia`, `telfRefContacto`, `telfRefParentesco`, `sexo`, `apPaternoPadre`, `apMaternoPadre`, `nombresPadre`, `ocupacionPadre`, `apPaternoMadre`, `apMaternoMadre`, `nombresMadre`, `ocupacionMadre`, `apPaternoConyu`, `apMaternoConyu`, `ocupacionConyu`, `eMail`, `nombreConyuge`, `estadoCivil`, `estadoCivilDesde`, `nroHijos`, `sec01Anhio`, `sec01Grado`, `sec01Centro`, `sec02Anhio`, `sec02Grado`, `sec02Centro`, `sup01Desde`, `sup01Hasta`, `sup01Carrera`, `sup01Centro`, `sup01Avance`, `sup01Grado`, `sup02Desde`, `sup02Hasta`, `sup02Carrera`, `sup02Centro`, `sup02Avance`, `sup02Grado`, `exp01Empresa`, `exp01Cargo`, `exp01Sueldo`, `exp01Desde`, `exp01Hasta`, `exp01Jefe`, `exp01JefePuesto`, `exp01Telf`, `exp02Empresa`, `exp02Cargo`, `exp02Sueldo`, `exp02Desde`, `exp02Hasta`, `exp02Jefe`, `exp02JefePuesto`, `exp02Telf`, `exp03Empresa`, `exp03Cargo`, `exp03Sueldo`, `exp03Desde`, `exp03Hasta`, `exp03Jefe`, `exp03JefePuesto`, `exp03Telf`, `laboroEnMoy`, `laboroEnMoyPuesto`, `laboroEnMoySucursal`, `laboroEnMoyMotivoCese`, `laboroEnMoyFchCese`, `familiarEnMoy`, `familiarEnMoyNombre`, `familiarEnMoyParentesco`, `emergenciaNombre`, `emergenciaParentesco`, `emergenciaTelfFijo`, `emergenciaTelfCelular`, `saludGrupoSanguineo`, `saludTieneEnfCronica`, `saludTieneAlergia`, `saludAlergia`, `saludEnfCronica`, `gradoInstruccion`, `licenciaNro`, `licenciaVigencia`, `licenciaCategoria`, `modoContratacion`, `bancoNombre`, `bancoNroCuenta`, `bancoTipoCuenta`, `bancoMoneda`, `remuneracionBasica`, `asignacionFamiliar`, `cajaChica`, `renta5ta`, `diasVacacAnual`, `manejaLiquidacion`, `fondoGarantia`, `fondoGaranRetenido`, `esMaster`, `precioMaster`, `deseaDscto`, `deseaDsctoOnp`, `entidadPension`, `tipoComision`, `trabajador`.idPoliza, `asumeEmpresa`, `cuspp`, `codTrabajador`, `formaPago`, `tallaCamisa`, `tallaPantalon`, `tallaBotas`, `tallaPolo`, `tallaCasaca`, `comisionFlujo`, `comisionMixta`, `primaSeg`, `porcentObligat`, descontarSeguro, descontarComisionAfp, descontarAporteAfp, `polizas`.valorPoliza  FROM `trabajador` LEFT JOIN pension ON `pension`.nombre = `trabajador`.entidadPension LEFT JOIN polizas ON `polizas`.idPoliza = `trabajador`.idPoliza  WHERE idTrabajador = :dni ");
    $consulta->bindParam(':dni',$dni);
    $consulta->execute();
    return $consulta->fetchAll();
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
    
      //if ($key == 'LicGoce')  echo " codContable $codContable  codPlame $codPlame valor $valor";

    }

    $cadCompleta = "INSERT INTO `quincenadetallecontab` (`fchQuincena` ,`idTrabajador` ,`id` ,`codContable` ,`codPlame` ,`valor` ,`creacFch`) VALUES $data ";
    $cant = $db->exec($cadCompleta);

    if ($quin == '2'){
    
    }

    $cadCompleta = "DELETE FROM `quincenadetallecontab` WHERE id = 'AsigFam' AND valor = 0 AND fchQuincena = '$fchQuincena' ";
    $cant = $db->exec($cadCompleta);

    return 1;
  }



  function calcularQuincenaContab($db,$idTrabajador,$anhio, $mes, $quin, $comFlujo, $comMixta, $primaSeg, $porcOblig){
    if($quin == '1') $fchQuincena = $anhio.'-'.$mes.'-14';
    else  $fchQuincena = $anhio.'-'.$mes.'-28';

    $arrData = array(
      'hrsExtra25' => array(
          'cod01' => '621102',  'cod02' => '0105',  'valor' => 0
      ),
      'hrsExtra35' => array(
          'cod01' => '611102',  'cod02' => '0106',  'valor' => 0
      ),
      'remDomFer' => array(
          'cod01' => '621201',  'cod02' => '0115',  'valor' => 0
      ),
      'vacTruncas' => array(
          'cod01' => '621503',  'cod02' => '0114',  'valor' => 0
      ),
      'remVacac' => array(
          'cod01' => '621501',  'cod02' => '0118',  'valor' => 0
      ),
      'habBasico' => array(
          'cod01' => '621101',  'cod02' => '0121',  'valor' => 0
      ),
      'asigFam' => array(
          'cod01' => '621103',  'cod02' => '0201',  'valor' => 0
      ),
      'asigVacac' => array(
          'cod01' => '621502',  'cod02' => '0210',  'valor' => 0
      ),
      'bonifCts' => array(
          'cod01' => '629101',  'cod02' => '0305',  'valor' => 0
      ),
      'servAdic' => array(
          'cod01' => '621301',  'cod02' => '1004',  'valor' => 0
      ),
      'bonifVar' => array(
          'cod01' => '621301',  'cod02' => '1004',  'valor' => 0
      ),
      'subsidio' => array(
          'cod01' => '162902',  'cod02' => '0916',  'valor' => 0
      ),
      'licGoce' => array(
          'cod01' => '621105',  'cod02' => '0907',  'valor' => 0
      ),
      'inasisten' => array(
          'cod01' => '621101',  'cod02' => '0705',  'valor' => 0
      ),
      'bonifExtra' => array(
          'cod01' => '621401',  'cod02' => '0312',  'valor' => 0
      ),
      'bonifExPro' => array(
          'cod01' => '621401',  'cod02' => '0313',  'valor' => 0
      ),
      'gratiFPyNv' => array(
          'cod01' => '621401',  'cod02' => '0406',  'valor' => 0
      ),
      'gratiFPPro' => array(
          'cod01' => '621401',  'cod02' => '0407',  'valor' => 0
      ),
      'bonoAlim' => array(
          'cod01' => '621109',  'cod02' => '0917',  'valor' => 0
      ),
      'bonoProd' => array(
          'cod01' => '621104',  'cod02' => '0902',  'valor' => 0
      ),
      'canastaNav' => array(
          'cod01' => '621107',  'cod02' => '0903',  'valor' => 0
      ),
      'ctsMayNov' => array(
          'cod01' => '629101',  'cod02' => '0904',  'valor' => 0
      ),
      'movilSuped' => array(
          'cod01' => '621106',  'cod02' => '0909',  'valor' => 0
      ),
      'utilidad' => array(
          'cod01' => '621108',  'cod02' => '0911',  'valor' => 0
      ),
      'bonoPrefer' => array(
          'cod01' => '621109',  'cod02' => '1005',  'valor' => 0
      ),
      'devFondoG' => array(
          'cod01' => '411201',  'cod02' => '1001',  'valor' => 0
      ),
      'adelQ' => array(
          'cod01' => '141201',  'cod02' => '0701',  'valor' => 0
      ),
      'adelVacac' => array(
          'cod01' => '411501',  'cod02' => '0706',  'valor' => 0
      ),
      'adelGratif' => array(
          'cod01' => '411401',  'cod02' => '0706',  'valor' => 0
      ),
      'adelCts' => array(
          'cod01' => '415101',  'cod02' => '0706',  'valor' => 0
      ),
      'adelCanNav' => array(
          'cod01' => '419101',  'cod02' => '0706',  'valor' => 0
      ),
      'adelUtil' => array(
          'cod01' => '413101',  'cod02' => '0706',  'valor' => 0
      ),
      'dsctoResp' => array(
          'cod01' => '759901',  'cod02' => '0706',  'valor' => 0
      ),
      'dsctoPrest' => array(
          'cod01' => '141101',  'cod02' => '0706',  'valor' => 0
      ),
      'dsctoCompra' => array(
          'cod01' => '122103',  'cod02' => '0706',  'valor' => 0
      ),
      'dsctoFndGar' => array(
          'cod01' => '411201',  'cod02' => '0706',  'valor' => 0
      ),
      'esSaludVid' => array(
          'cod01' => '403501',  'cod02' => '0604',  'valor' => 0
      ),
      'renta5ta' => array(
          'cod01' => '401731',  'cod02' => '0605',  'valor' => 0
      ),
      'onp' => array(
          'cod01' => '403201',  'cod02' => '0607',  'valor' => 0
      ),
      'comision' => array(
          'cod01' => '407101',  'cod02' => '0601',  'valor' => 0
      ),
      'seguro' => array(
          'cod01' => '407101',  'cod02' => '0606',  'valor' => 0
      ),
      'aporte' => array(
          'cod01' => '407101',  'cod02' => '0608',  'valor' => 0
      ),
      'sumaEsS' => array(
          'cod01' => '403101',  'cod02' => '0804',  'valor' => 0
      ),
      'polizDeSeg' => array(
          'cod01' => '182141',  'cod02' => '0803',  'valor' => 0
      ),
      'sobreElTrabajador' => array(
          'diasTrabaj' => 0,  'hrsTrabaj' => '',  'nombreComp' => '',
          'viajes' => 0,      'valorRol' => 0,    'categTrabaj' => '',
          'modoSueldo' => '', 'idtrabajador' => '',
          'remuneracionBasica' =>0, 'asignacionFamiliar' => 0
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
      $arrData['subsidio']['valor'] = $item['descMedico'];
      $arrData['licGoce']['valor'] = $item['licGoce'];
      $arrData['inasisten']['valor'] = $item['inasistencia'];

      //Ingreso No Gravable
      $arrData['bonoProd']['valor'] = $item['bono'] + $item['bonoProducMes'];
      $arrData['devFondoG']['valor'] = $item['devGarantia'];
      $arrData['movilSuped']['valor'] += $item['movilidad'] + $item['movilAsig'] ;

      //Otros Dsctos No Deduc
      $arrData['dsctoPrest']['valor'] += $item['adelanto'];
      $arrData['esSaludVid']['valor'] += $item['pagoEsSaludVida'];
    }

    foreach ($dataEnPrestamoDetalle as $key => $item){
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
          //if ($idTrabajador == '08023091') echo "HABER $haberBasico";
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
    /*
    if ($idTrabajador == '46151123' ) echo "saldo valor rol  $saldoValorRol  sobre movilidad $auxMovilSupedOriginal > $topeMovilSuped, aux movil suped   $auxMovilSuped, haber basico $haberBasico  ";
    */
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
    //if ($arrData['sobreElTrabajador']['descontarAporteAfp'] == 'No') $dsctoAportObl = 0; //Por ahora no

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
  
    } else if ($item['modoSueldo'] == 'Preferencial'){
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
  
    $cant = insertaDataQuincenaContab($db,$anhio,$mes,$quin,$arrData);
    return $cant;
  }




  function buscarDatosPlanillaQuincena($db){
    $quincena = $_POST["quincena"];

    $consulta = $db->prepare("SELECT `quincena`, `estadoQuincena`, `estadoRendicion`, `fchCreacion`, `quinFchIni`, `quinFchFin` FROM `quincena` WHERE `quincena` = :quincena AND estadoQuincena IN ('Siguiente','Md') ");
    $consulta->bindParam(':quincena',$quincena);
    $consulta->execute();
    return $consulta->fetch();
  }

  function guardaEditarDatosPlanilla($db){
    $usuario = $_SESSION["usuario"];
    $quincena = $_POST["quincenaEdita"];
    $quinFchIni = $_POST["txtEditaFchIni"];
    $quinFchFin = $_POST["txtEditaFchFin"];

    $actualiza = $db->prepare("UPDATE `quincena` SET `quinFchIni` = :quinFchIni, `quinFchFin` = :quinFchFin,  `ultCambioUsuario` = :usuario WHERE `quincena` = :quincena AND estadoQuincena IN ('Siguiente', 'Md')");
    $actualiza->bindParam(':quinFchIni',$quinFchIni);
    $actualiza->bindParam(':quinFchFin',$quinFchFin);
    $actualiza->bindParam(':quincena',$quincena);
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function buscarUltQuincena($db){
    $consulta = $db->prepare("SELECT quincena, `estadoQuincena`, `quinFchIni`, `quinFchFin`  FROM `quincena` ORDER BY `quincena` DESC LIMIT 1  ");
    $consulta->execute();
    return $consulta->fetch();
  }

  function agregarTrabajQuincena($db){
    $usuario = $_SESSION["usuario"];
    $estadoQuinTrab = "";
    $estadoQuincena = "";
    $nombreProceso = "Trabajador se añade directamente";

    $auxTrab = $_POST["txtNvoTrabajador"];
    $idTrabajador = strtok($auxTrab, "-");
    $quincena = $_POST["addTrabQuin"];

    $consulta = $db->prepare("SELECT idTrabajador, `estadoQuincena` FROM `trabajador` WHERE  `idTrabajador` = :idTrabajador ");
    $consulta->bindParam(':idTrabajador',$idTrabajador);
    $consulta->execute();
    $auxTrabaj = $consulta->fetch();
    $estadoQuinTrab = $auxTrabaj["estadoQuincena"];
    
    //Aqui buscar los datos de fechas de la quincena
    $consulta = $db->prepare("SELECT quincena, `estadoQuincena`, `quinFchIni`, `quinFchFin`  FROM `quincena` WHERE quincena = :quincena ");
    $consulta->bindParam(':quincena',$quincena);
    $consulta->execute();
    $auxQuin = $consulta->fetch();

    $fchIni = $auxQuin["quinFchIni"];
    $fchFin = $auxQuin["quinFchFin"];
    $estadoQuincena = $auxQuin["estadoQuincena"]; 

    //Valida si el trabajador y la quincena cumplen los requisitos
    if($estadoQuinTrab == "No" && $estadoQuincena == "Md" ){
      $anhio = strtok($quincena, "-");
      $mes   = strtok("-");

      /////////////////
      $auxDia= strtok("-");
      $quin = ($auxDia == '14') ? "1" : "2";
      $valorMovDiaAux = $_SESSION['valorMovDiaAux'];
      $valorMovDiaCon = $_SESSION['valorMovDiaCon'];
      /////////////////

      //Actualizando datos en despachopersonal
      $actualiza = $db->prepare("UPDATE `despachopersonal`, `trabajador`, `despacho` set `despachopersonal`.`pagado` = 'Md', `fchPago` = '$quincena', `anhio` = '$anhio', `mes` = '$mes', `ultProcesoUsuario` = :usuario, `ultProceso` = :nombreProceso,  `ultProcesoFch` = curdate() WHERE `despachopersonal`.`fchDespacho` = `despacho`.`fchDespacho` AND `despachopersonal`.`correlativo` = `despacho`.`correlativo` AND `despachopersonal`.`idTrabajador` = `trabajador`.`idTrabajador` AND `trabajador`.`estadoQuincena` = 'No' AND `despachopersonal`.`pagado` = 'No' AND `tipoRol` != 'Coordinador' AND (`despachopersonal`.`fchDespacho` >= :fchIni AND `despachopersonal`.`fchDespacho` <= :fchFin ) AND `concluido` = 'Si' AND `fchDesmarca` IS NULL AND `despachopersonal`.`idTrabajador` = :idTrabajador ");
      $actualiza->bindParam(':idTrabajador',$idTrabajador);
      $actualiza->bindParam(':fchIni',$fchIni);
      $actualiza->bindParam(':fchFin',$fchFin);
      $actualiza->bindParam(':nombreProceso',$nombreProceso);
      $actualiza->bindParam(':usuario',$usuario);
      $actualiza->execute();
      $cantActDespPers = $actualiza->rowCount();

      //Actualiza tabla prestamo
      $tiposItem = "'HraExtra', 'Prestamo', 'Adelanto', 'Bono', 'BonoDiario', 'Movilidad', 'Reembolso', 'Compra', 'DevolGarantia', 'DomFeriado', 'DsctoTrabaj', 'DsctoUnif', 'FondoGarantia', 'Ajuste', 'DescMedico', 'LicGoce' , 'ServAdicional', 'Inasistencia'";
      $actualiza = $db->prepare("UPDATE `prestamo`, `trabajador` set `entregado` = 'Md', `fchQuincena` = '$quincena', `anhio` = '$anhio', `mes` = '$mes', `ultProcesoUsuario` = :usuario, `ultProceso` = :nombreProceso,  `ultProcesoFch` = curdate()  WHERE  `prestamo`.`idTrabajador` = `trabajador`.`idTrabajador` AND `trabajador`.`estadoQuincena` = 'No' AND `entregado` = 'No' AND (`fchPago` >= :fchIni AND `fchPago` <= :fchFin) AND tipoItem IN ($tiposItem) AND `fchDesmarca` IS NULL AND `prestamo`.`idTrabajador` = :idTrabajador ");
      $actualiza->bindParam(':idTrabajador',$idTrabajador);
      $actualiza->bindParam(':fchIni',$fchIni);
      $actualiza->bindParam(':fchFin',$fchFin);
      $actualiza->bindParam(':nombreProceso',$nombreProceso);
      $actualiza->bindParam(':usuario',$usuario);
      $actualiza->execute();
      $cantActPrest = $actualiza->rowCount();

      //Actualiza tabla prestamodetalle
      $tiposItem2 = "'Prestamo', 'Ajuste', 'Compra', 'DsctoTrabaj', 'DsctoUnif', 'FondoGarantia'";
      $actualiza = $db->prepare("UPDATE  `prestamodetalle`, `trabajador` set `pagado` = 'Md', `fchQuincena` = '$quincena', `anhio` = '$anhio', `mes` = '$mes', `ultProcesoUsuario` = :usuario, `ultProceso` = :nombreProceso,  `ultProcesoFch` = curdate()  WHERE  `prestamodetalle`.`idTrabajador` = `trabajador`.`idTrabajador` AND `trabajador`.`estadoQuincena` = 'No' AND `pagado` = 'No' AND (`fchPago` >= :fchIni AND `fchPago` <= :fchFin) AND tipoItem IN ($tiposItem2) AND `fchDesmarca` IS NULL AND `prestamodetalle`.`idTrabajador` = :idTrabajador");
      $actualiza->bindParam(':idTrabajador',$idTrabajador);
      $actualiza->bindParam(':fchIni',$fchIni);
      $actualiza->bindParam(':fchFin',$fchFin);
      $actualiza->bindParam(':nombreProceso',$nombreProceso);
      $actualiza->bindParam(':usuario',$usuario);
      $actualiza->execute();
      $cantActPrestDet = $actualiza->rowCount();

      //Actualiza tabla ocurrenciaconsulta
      $tiposOcurrencia = "'Caja Chica', 'Descuento', 'Devolucion', 'Gastos Varios', 'Movilidad'"; 
      $actualiza = $db->prepare("UPDATE `ocurrenciaconsulta`, `trabajador` set `pagado` = 'Md', `fchQuincena` = :fchQuincena, `anhio` = '$anhio', `mes` = '$mes', `ultProcesoUsuario` = :usuario, `ultProceso` = :nombreProceso,  `ultProcesoFch` = curdate() WHERE `ocurrenciaconsulta`.`idTrabajador` = `trabajador`.`idTrabajador` AND `trabajador`.`estadoQuincena` = 'No' AND `pagado` = 'No' AND `fchPago` >= :fchIni AND `fchPago` <= :fchFin AND tipoOcurrencia IN ($tiposOcurrencia) AND `fchDesmarca` is null AND `ocurrenciaconsulta`.`idTrabajador` = :idTrabajador");
      $actualiza->bindParam(':idTrabajador',$idTrabajador);
      $actualiza->bindParam(':fchIni',$fchIni);
      $actualiza->bindParam(':fchFin',$fchFin);
      $actualiza->bindParam(':nombreProceso',$nombreProceso);
      $actualiza->bindParam(':fchQuincena',$quincena);
      $actualiza->bindParam(':usuario',$usuario);
      $actualiza->execute();
      $cantActOcurCons = $actualiza->rowCount();


      ////////////////////////////////////////////////////////
      //Se crean registros relacionados con el área contable//
      ////////////////////////////////////////////////////////
      //Verifico si es un trabajador de 5ta para continuar
      $consulta = $db->prepare("SELECT `idTrabajador`, `tipoTrabajador`, `categTrabajador`, `asumeEmpresa`, `modoSueldo`, `apPaterno`, `apMaterno`, `nombres`, `modoContratacion`, `remuneracionBasica` AS remunBasica , `asignacionFamiliar` , `cajaChica`, `renta5ta` ,`fondoGarantia`, `deseaDscto`, `entidadPension`, `tipoComision`,`porcent`, `porcentMixto`, `comisionFlujo`, `comisionMixta`, `primaSeg`, `porcentObligat`, `estadoQuincena`, `movilidadAsignadaMes`, `bonoProducMes`  FROM trabajador LEFT JOIN pension ON `trabajador`.entidadPension = `pension`.nombre  WHERE  `idTrabajador` = :idTrabajador ");
      $consulta->bindParam(':idTrabajador',$idTrabajador);
      $consulta->execute();
      $dataTrab = $consulta->fetch();

      if ($dataTrab["categTrabajador"] == '5ta' && $dataTrab["estadoQuincena"] == 'No' ){
        $consulta = $db->prepare("SELECT `nombre`, `porcent`, `porcentMixto`, `comisionFlujo`, `comisionMixta`, `primaSeg`, `porcentObligat` FROM `pension`");
        $consulta->execute();
        $infoAFPs = $consulta->fetchAll();

        foreach($infoAFPs as $afp) {//se esta haciendo innecesario
          $nombre = $afp['nombre'];
          $comFlujo["$nombre"]  = $afp['comisionFlujo'];
          $comMixta["$nombre"]  = $afp['comisionMixta'];
          $primaSeg["$nombre"]  = $afp['primaSeg'];
          $porcOblig["$nombre"] = $afp['porcentObligat'];
        }

        //Datos Generales de la quincena
        $consulta = $db->prepare("SELECT count(DISTINCT `despachopersonal`.fchDespacho) AS diasTrabaj, SEC_TO_TIME(SUM(TIME_TO_SEC( timediff(concat( `despachopersonal`.trabFchDespachoFin,' ',`despachopersonal`.trabHraFin) , concat(`despachopersonal`.fchDespacho,' ',`despachopersonal`.trabHraInicio))))) AS hrsTrabaj , `despachopersonal`.`idTrabajador`, concat(`trabajador`.`apPaterno`,' ',`trabajador`.`apMaterno`,', ', `trabajador`.`nombres` ) as nombreCompleto, count(*) as viajes , sum(`valorRol` + `valorAdicional`) as valorRol, `despachopersonal`.`fchPago`, `categTrabajador`, `modoSueldo`, entidadPension, remuneracionBasica, asignacionFamiliar, entidadPension, tipoComision, sum(valorHraExtra) AS valorHraExtra,  `deseaDscto`, `tipoTrabajador` FROM `despachopersonal`, trabajador WHERE `despachopersonal`.`idTrabajador` = `trabajador`.`idTrabajador` AND `despachopersonal`.`pagado`= 'Md' AND `despachopersonal`.`fchPago` = :fchPago AND `trabajador`.`idTrabajador` = :idTrabajador group by `trabajador`.`idTrabajador`"); 
        $consulta->bindParam(':fchPago',$quincena);
        $consulta->bindParam(':idTrabajador',$idTrabajador);
        $consulta->execute();
        $dataTrabQuin = $consulta->fetch();

        $arrDataTrabAuxiliar = array();
        $arrDataTrabAuxiliar[$idTrabajador]['diasTrabaj'] = $dataTrabQuin['diasTrabaj'];
        $arrDataTrabAuxiliar[$idTrabajador]['valorRol'] = $dataTrabQuin['valorRol'];
        $arrDataTrabAuxiliar[$idTrabajador]['posibleMovilSuped'] = 0;

        $entidadPension = $dataTrab['entidadPension'];
        $deseaDscto     = $dataTrab['deseaDscto'];
        $tipoTrabajador = $dataTrab['tipoTrabajador'];
        $valorMovilDia = ($tipoTrabajador == 'Auxiliar')?$valorMovDiaAux:$valorMovDiaCon;
        $diasTrabaj = isset($arrDataTrabAuxiliar[$idTrabajador]['diasTrabaj'])?$arrDataTrabAuxiliar[$idTrabajador]['diasTrabaj']:0;

        $movilAsigQuincena = $dataTrab['movilidadAsignadaMes'] / 2;
        $bonoProducQuincena = $dataTrab['bonoProducMes'] / 2; //LAEM 211218

        //Cálculo EsSalud
        $esSalud = $_SESSION['esSalud'];
        if($dataTrab['deseaDscto'] == 'Si'){
          /* 201012 Los administrativos pasan a ser igual que los demás en cuanto a pago quincenal
          $dsctoEsSalud = ($item['tipoTrabajador'] == 'Administrativo')?$esSalud:$esSalud*0.5;*/
          $dsctoEsSalud = $esSalud*0.5;
        } else 
          $dsctoEsSalud = 0;
        $dsctoEsSalud = round($dsctoEsSalud,2);
        if ($dsctoEsSalud > 0){
          insertaRegPrestamoAuto($db, $idTrabajador,$anhio,$mes,'dsctoEsSalud',$dsctoEsSalud,'1',$quincena,'EsSalud',$nombreProceso);
        }

        //////
        $asigFam = $dataTrab['asignacionFamiliar'];
        if ($asigFam > 0){
          /* 201012 La asignación familiar se pasa a pagar en la segunda quincena
          if ($quin == '1' || ($quin == '2' && verificaAsignacionFamiliar($db, $idTrabajador, $anhio.'-'.$mes.'-14') == 0) ){
          insertaRegPrestamoAuto($db, $idTrabajador,$anhio,$mes,'Asign Familiar',$asigFam,'1',$fchQuincena,'AsigFam',$nombreProceso);
          }*/
          if ($quin == '2' ){
            insertaRegPrestamoAuto($db, $idTrabajador,$anhio,$mes,'Asign Familiar',$asigFam,'1',$quincena,'AsigFam',$nombreProceso);
          }  
        }

        if($movilAsigQuincena > 0){
          insertaRegPrestamoAuto($db, $idTrabajador,$anhio,$mes,'Movilidad Asignada Quincena',$movilAsigQuincena,'1',$quincena,'MovilAsig',$nombreProceso);
        }

        if($bonoProducQuincena > 0){//LAEM 211218
          insertaRegPrestamoAuto($db, $idTrabajador,$anhio,$mes,'Bono Productividad Quincena',$bonoProducQuincena,'1',$fchQuincena,'BonoProducMes',$nombreProceso);
        }

        //Cálculo quincena contab
        $rpta = calcularQuincenaContab($db,$idTrabajador,$anhio, $mes, $quin, $comFlujo, $comMixta, $primaSeg, $porcOblig);
        //////
      }

      //Cambiando el estado quincena del trabajador
      $actualiza = $db->prepare("UPDATE `trabajador` SET `estadoQuincena` = 'Si', fchDesmarcaEstadoQ = NULL,  `usuarioUltimoCambio` = :usuario, `fchUltimoCambio` = curdate() WHERE `idTrabajador` LIKE :idTrabajador");
      $actualiza->bindParam(':idTrabajador',$idTrabajador);
      $actualiza->bindParam(':usuario',$usuario);
      $actualiza->execute();
      $cantActTrabaj = $actualiza->rowCount();

      if($actualiza->rowCount() >0 ){
        $descripcion = "Se ha añadido manualmente el idTrab $idTrabajador a la quincena $quincena.";
        logAccion($db, $descripcion, $idTrabajador, NULL);
      }

    }

  }

  function generaIdPoliza($db){
    $anhio = Date("Y");
    $consulta = $db->prepare("SELECT idPoliza FROM polizas ORDER BY idPoliza DESC limit 1");
    $consulta->execute();
    $result = $consulta->fetchAll();
    $nvoId = "";
    // Se obtiene el resultado de la consulta
    foreach($result as $fila) {
      $ultId = $fila['idPoliza'];
      $anhioId = substr($ultId, 0,4);
      if ($anhioId == $anhio){
        $nvoId = 1*$ultId + 1;
      }
    }
    if ($nvoId == "")  $nvoId = $anhio."000001";
    return $nvoId;

  }

  function crearPoliza($db){
    $creacUsuario = $_SESSION['usuario'];
    $idPoliza = generaIdPoliza($db);
    $nombPoliza = $_POST["txtNombPoliza"];
    $valorPoliza = $_POST["txtValorPoliza"];
    $descripPoliza  = $_POST["txtDescrip"];
    $inserta = $db->prepare("INSERT INTO `polizas` (`idPoliza`, `nombPoliza`, `valorPoliza`, `descripPoliza`, `creacUsuario`, `creacFch`) VALUES (:idPoliza, :nombPoliza, :valorPoliza, :descripPoliza, :creacUsuario, now())");
    $inserta->bindParam(':idPoliza',$idPoliza);
    $inserta->bindParam(':nombPoliza',$nombPoliza);
    $inserta->bindParam(':valorPoliza',$valorPoliza);
    $inserta->bindParam(':descripPoliza',$descripPoliza);
    $inserta->bindParam(':creacUsuario',$creacUsuario);
    $inserta->execute();
    return $inserta->rowCount();

  }

  function elimPoliza($db){
    $idPoliza = $_POST["txtElimIdPoliza"];
    $auxIdPoliza = $_POST["elimIdPoliza"];
    if ($idPoliza == $auxIdPoliza){
      $elimina = $db->prepare("DELETE FROM `polizas` WHERE `idPoliza` = :idPoliza");
      $elimina->bindParam(':idPoliza',$idPoliza);
      $elimina->execute(); 
      return $elimina->rowCount();
    } else {
      return -1;
    }

  }

  function editarPoliza($db){
    $editaUsuario = $_SESSION["usuario"];
    $idPoliza = $_POST["txtEditaIdPoliza"];
    $auxIdPoliza = $_POST["editaIdPoliza"];
    $nombPoliza = $_POST["txtEditaNombPoliza"];
    $estadoPoliza = $_POST["cmbEditaEstadoPoliza"];
    $valorPoliza = $_POST["txtEditaValorPoliza"];
    $descripPoliza = $_POST["txtEditaDescrip"];

    if($idPoliza == $auxIdPoliza){
      $actualiza = $db->prepare("UPDATE `polizas` SET `nombPoliza` = :nombPoliza, `valorPoliza` = :valorPoliza, `descripPoliza` = :descripPoliza, estadoPoliza = :estadoPoliza, editaUsuario = :editaUsuario, editaFch = curdate() WHERE `idPoliza` = :idPoliza");
      $actualiza->bindParam(':idPoliza',$idPoliza);
      $actualiza->bindParam(':nombPoliza',$nombPoliza);
      $actualiza->bindParam(':valorPoliza',$valorPoliza);
      $actualiza->bindParam(':estadoPoliza',$estadoPoliza);
      $actualiza->bindParam(':descripPoliza',$descripPoliza);
      $actualiza->bindParam(':editaUsuario',$editaUsuario);
      $actualiza->execute();
      return $actualiza->rowCount();
    } else {
      return -1;
    }
  }

  function buscarPoliza($db){
    $idPoliza = $_POST["idPoliza"];

    $consulta = $db->prepare("SELECT `idPoliza`, `nombPoliza`, `valorPoliza`, `descripPoliza`, `estadoPoliza`, `creacUsuario`, `creacFch`  FROM `polizas` WHERE `idPoliza` = :idPoliza ");
    $consulta->bindParam(':idPoliza',$idPoliza);
    $consulta->execute();
    return $consulta->fetch();
  }


  function editaDespFchPago($db){
    if( $_POST["fchDespacho"] == $_POST["txtFchDespacho"] &&
        $_POST["correlativo"] == $_POST["txtCorrelativo"]
      ){
      $fchIni = $_POST["quinFchIni"];
      $fchFin = $_POST["quinFchFin"];
      $fchPago = $_POST["txtFchPago"];
      $fchQuincena = $_POST["despFchQuincena"];
      if($fchPago >= $fchIni && $fchPago <= $fchFin ) $fchPago = $fchQuincena;
      $fchDespacho = $_POST["fchDespacho"];
      $correlativo = $_POST["correlativo"];
      $idTrabajador = $_POST["txtIdTrabajador"];
      $tipoRol = $_POST["tipoRol"];

      $actualiza = $db->prepare("UPDATE `despachopersonal` SET `fchPago` = :fchPago WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `idTrabajador` = :idTrabajador AND `tipoRol` = :tipoRol");
      $actualiza->bindParam(':fchPago',$fchPago);
      $actualiza->bindParam(':fchDespacho',$fchDespacho);
      $actualiza->bindParam(':correlativo',$correlativo);
      $actualiza->bindParam(':idTrabajador',$idTrabajador);
      $actualiza->bindParam(':tipoRol',$tipoRol);
      //$actualiza->bindParam(':editaUsuario',$editaUsuario);
      $actualiza->execute();

      if($actualiza->rowCount() >0 ){
        $descripcion = "Se ha modificado la fecha de pago a $fchPago del despacho ".$fchDespacho."-".$correlativo;
        logAccion($db, $descripcion, $idTrabajador, NULL);
      }

      return $actualiza->rowCount();

    } else {
      return -1;
    }
  }

  function editaOcurFchPago($db){
    if( $_POST["ocurFchDespacho"] == $_POST["txtOcurFchDespacho"] &&
        $_POST["ocurCorrelativo"] == $_POST["txtOcurCorrelativo"]
      ){
      $fchDespacho = $_POST["ocurFchDespacho"];
      $correlativo = $_POST["ocurCorrelativo"];
      $idTrabajador = $_POST["txtOcurIdTrabajador"];
      $nroCuota = $_POST["txtOcurNroCuota"];
      $codOcurrencia = $_POST["codOcurrencia"];

      $fchPago = $_POST["txtOcurFchPago"];

      //echo "$fchDespacho, $correlativo, $idTrabajador, $nroCuota, $codOcurrencia, $fchPago";

      $actualiza = $db->prepare("UPDATE `ocurrenciaconsulta` SET `fchPago` = :fchPago WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `codOcurrencia` = :codOcurrencia AND `nroCuota` = :nroCuota AND `idTrabajador` = :idTrabajador");
      $actualiza->bindParam(':fchPago',$fchPago);
      $actualiza->bindParam(':fchDespacho',$fchDespacho);
      $actualiza->bindParam(':correlativo',$correlativo);
      $actualiza->bindParam(':codOcurrencia',$codOcurrencia);
      $actualiza->bindParam(':nroCuota',$nroCuota);
      $actualiza->bindParam(':idTrabajador',$idTrabajador);
      $actualiza->execute();

      if($actualiza->rowCount() >0 ){
        $descripcion = "Se ha modificado la fecha de pago a $fchPago de la ocurrencia $codOcurrencia relacionada al despacho ".$fchDespacho."-".$correlativo;
        logAccion($db, $descripcion, $idTrabajador, NULL);
      }

      return $actualiza->rowCount();

    } else {
      return -1;
    }
  }

  function editaDescFchPago($db){

    $fchPago = $_POST["txtDescFchPago"];
    $monto = $_POST["descMonto"];
    $nroCuota = $_POST["txtDescNroCuota"];
    $idTrabajador = $_POST["txtDescIdTrabajador"];
    $descripcion = $_POST["txtDescDescripcion"];
    $tipoItem = $_POST["descTipoItem"];
    $fchCreacion = $_POST["descFchCreac"];

    $actualiza = $db->prepare("UPDATE `prestamodetalle` SET `fchPago` = :fchPago WHERE `monto` = :monto AND `nroCuota` = :nroCuota AND `idTrabajador` = :idTrabajador AND `descripcion` = :descripcion AND `tipoItem` = :tipoItem AND `fchCreacion` = :fchCreacion");
    $actualiza->bindParam(':fchPago',$fchPago);
    $actualiza->bindParam(':monto',$monto);
    $actualiza->bindParam(':nroCuota',$nroCuota);
    $actualiza->bindParam(':idTrabajador',$idTrabajador);
    $actualiza->bindParam(':descripcion',$descripcion);
    $actualiza->bindParam(':tipoItem',$tipoItem);
    $actualiza->bindParam(':fchCreacion',$fchCreacion);
    $actualiza->execute();

    if($actualiza->rowCount() >0 ){
      $descripcion = "Se ha modificado la fecha de pago a $fchPago del préstamo '$descripcion', número de cuota $nroCuota ";
      logAccion($db, $descripcion, $idTrabajador, NULL);
    }

    return $actualiza->rowCount();
  }

  function eliminaOcurrencia($db){
    if( $_POST["elimOcurFchDespacho"] == $_POST["txtElimOcurFchDespacho"] &&
        $_POST["elimOcurCorrelativo"] == $_POST["txtElimOcurCorrelativo"]
      ){
      $fchDespacho = $_POST["elimOcurFchDespacho"];
      $correlativo = $_POST["elimOcurCorrelativo"];
      $idTrabajador = $_POST["txtElimOcurIdTrabajador"];
      $nroCuota = $_POST["txtElimOcurNroCuota"];
      $codOcurrencia = $_POST["elimCodOcurrencia"];
      $fchPago = $_POST["txtElimOcurFchPago"];

      $consulta = $db->prepare("SELECT count(*) AS cant FROM ocurrenciaconsulta WHERE fchDespacho = :fchDespacho AND correlativo = :correlativo AND idTrabajador = :idTrabajador AND codOcurrencia = :codOcurrencia AND pagado = 'Si' ");
      $consulta->bindParam(':fchDespacho',$fchDespacho);
      $consulta->bindParam(':correlativo',$correlativo);
      $consulta->bindParam(':idTrabajador',$idTrabajador);
      $consulta->bindParam(':codOcurrencia',$codOcurrencia);
      $consulta->execute();
      $auxData = $consulta->fetch();
      if($auxData["cant"] > 0 ){
        return -2;//Ya hay ocurrencias pagadas
      } else {
        $elimina = $db->prepare("DELETE FROM ocurrenciaconsulta WHERE fchDespacho = :fchDespacho AND correlativo = :correlativo AND idTrabajador = :idTrabajador AND codOcurrencia = :codOcurrencia  ");
        $elimina->bindParam(':fchDespacho',$fchDespacho);
        $elimina->bindParam(':correlativo',$correlativo);
        $elimina->bindParam(':idTrabajador',$idTrabajador);
        $elimina->bindParam(':codOcurrencia',$codOcurrencia);
        $elimina->execute();

        if($actualiza->rowCount() >0 ){
          $descripcion = "Se han eliminado los detalles de la ocurrencia $codOcurrencia. Fch Despacho $fchDespacho, correlativo $correlativo ";
          logAccion($db, $descripcion, $idTrabajador, NULL);
        }

        return $elimina->rowCount();
      }
    } else {
      return -1; //Error en los datos
    }
  }

  function eliminaDescuento($db){
    //echo "Llega hasta aqui";
    $idTrabajador = $_POST["txtElimDescIdTrabajador"];
    $descripcion = $_POST["txtElimDescDescripcion"];
    $monto       = $_POST["elimDescMonto"];
    $tipoItem    = $_POST["elimDescTipoItem"];
    $fchCreacion = $_POST["elimDescFchCreac"];

    /*
    DELETE FROM `prestamodetalle` WHERE `monto` = '433.4' AND `nroCuota` = 20 AND `idTrabajador` = '48415930' AND `descripcion` = 'DESCUENTO POR MULTA EN LA VICTORIA POR ESTACIONAR VEHICULO UNIDAD C7L921 DIA 26/11/2020' AND `tipoItem` = 'DsctoTrabaj' AND `fchCreacion` = '2020-12-11'
    */

    $consulta = $db->prepare("SELECT count(*) AS cant FROM `prestamodetalle` WHERE `monto` = :monto AND `idTrabajador` = :idTrabajador AND `descripcion` = :descripcion AND `tipoItem` = :tipoItem AND `fchCreacion` = :fchCreacion AND pagado = 'Si'");
    $consulta->bindParam(':idTrabajador',$idTrabajador);
    $consulta->bindParam(':descripcion',$descripcion);
    $consulta->bindParam(':monto',$monto);
    $consulta->bindParam(':tipoItem',$tipoItem);
    $consulta->bindParam(':fchCreacion',$fchCreacion);
    $consulta->execute();

    $auxData = $consulta->fetch();
    if($auxData["cant"] > 0 ){
      return -2;//Ya hay descuentos pagados
    } else {

      $elimina = $db->prepare("DELETE FROM `prestamodetalle` WHERE `monto` = :monto AND `idTrabajador` = :idTrabajador AND `descripcion` = :descripcion AND `tipoItem` = :tipoItem  AND `fchCreacion` = :fchCreacion ");
      $elimina->bindParam(':idTrabajador',$idTrabajador);
      $elimina->bindParam(':descripcion',$descripcion);
      $elimina->bindParam(':monto',$monto);
      $elimina->bindParam(':tipoItem',$tipoItem);
      $elimina->bindParam(':fchCreacion',$fchCreacion);
      $elimina->execute();
      if($elimina->rowCount() >0 ){
        $descripcion = "Se han eliminado los detalles del descuento $descripcion, tipoItem $tipoItem, fchCreacion $fchCreacion ";
        logAccion($db, $descripcion, $idTrabajador, NULL);
      }
      return $elimina->rowCount();
    }

  }

  function editaReembFchPago($db){
    $fchPago = $_POST["txtReembFchPago"];
    $monto = $_POST["txtReembMonto"];
    $idTrabajador = $_POST["txtReembIdTrabajador"];
    $descripcion = $_POST["txtReembDescrip"];
    $tipoItem = $_POST["txtReembTipoItem"];
    $fchCreacion = $_POST["reembFchCreac"];

    $actualiza = $db->prepare("UPDATE `prestamo` SET `fchPago` = :fchPago WHERE `monto` = :monto AND `idTrabajador` = :idTrabajador AND `descripcion` = :descripcion AND `tipoItem` = :tipoItem AND `fchCreacion` = :fchCreacion");
    $actualiza->bindParam(':fchPago',$fchPago);
    $actualiza->bindParam(':monto',$monto);
    $actualiza->bindParam(':idTrabajador',$idTrabajador);
    $actualiza->bindParam(':descripcion',$descripcion);
    $actualiza->bindParam(':tipoItem',$tipoItem);
    $actualiza->bindParam(':fchCreacion',$fchCreacion);
    $actualiza->execute();

    if($actualiza->rowCount() >0 ){
      $descripcion = "Se ha modificado la fecha de pago a $fchPago del préstamo '$descripcion'";
      logAccion($db, $descripcion, $idTrabajador, NULL);
    }

    return $actualiza->rowCount();
  }

  function elimReembFchPago($db){
    $fchPago = $_POST["txtElimReembFchPago"];
    $monto = $_POST["txtElimReembMonto"];
    $idTrabajador = $_POST["txtElimReembIdTrabajador"];
    $descripcion = $_POST["txtElimReembDescrip"];
    $tipoItem = $_POST["txtElimReembTipoItem"];
    $fchCreacion = $_POST["elimReembFchCreac"];

    $elimina = $db->prepare("DELETE FROM  `prestamo` WHERE `monto` = :monto AND `idTrabajador` = :idTrabajador AND `descripcion` = :descripcion AND `tipoItem` = :tipoItem AND `fchCreacion` = :fchCreacion");
    $elimina->bindParam(':monto',$monto);
    $elimina->bindParam(':idTrabajador',$idTrabajador);
    $elimina->bindParam(':descripcion',$descripcion);
    $elimina->bindParam(':tipoItem',$tipoItem);
    $elimina->bindParam(':fchCreacion',$fchCreacion);
    $elimina->execute();

    if($elimina->rowCount() >0 ){
      $descripcion = "Se ha eliminado el reembolso '$descripcion' de fecha de pago $fchPago";
      logAccion($db, $descripcion, $idTrabajador, NULL);
    }

    return $elimina->rowCount();
  }

  function generaIdContrato($db){
    $anhio = Date("Y");
    $consulta = $db->prepare("SELECT idContrato FROM trabajcontratos ORDER BY idContrato DESC limit 1");
    $consulta->execute();
    $result = $consulta->fetchAll();
    $nvoId = "";
    // Se obtiene el resultado de la consulta
    foreach($result as $fila) {
      $ultId = $fila['idContrato'];
      $anhioId = substr($ultId, 0,4);
      if ($anhioId == $anhio){
        $nvoId = 1*$ultId + 1;
      }
    }
    if ($nvoId == "")  $nvoId = $anhio."000001";
    return $nvoId;
  }

  function generaIdVacacion($db){
    //$anhio = Date("Y");
    $consulta = $db->prepare("SELECT idVacacion FROM trabajcontratovacacs ORDER BY idVacacion DESC limit 1");
    $consulta->execute();
    $result = $consulta->fetchAll();
    $nvoId = 1;
    // Se obtiene el resultado de la consulta
    foreach($result as $fila) {
      $ultId = $fila['idVacacion'];
      $nvoId = 1 * $ultId + 1; 
    }
    return $nvoId;
  }

  function insertaNvoContrato($db){
    $creacUsuario = $_SESSION['usuario'];
    $idContrato   = generaIdContrato($db);
    $nroContrato  = $_POST["txtNvoContrNro"];
    $regimen = $_POST["cmbNvoContrRegimen"];
    $tipoContrato = $_POST["cmbNvoContrTipo"];

    $modo    = "Alta";
    $estado  = "Activo";
    //$estado  = $_POST["cmbNvoContrEstado"]
    $fchInicio  = $_POST["txtNvoContrFchIni"];
    $fchFin  = $_POST["txtNvoContrFchFin"];
    $observacion = $_POST["txtNvoContrObserv"];
    $idTrabajador= $_POST["nvoContrIdTrabaj"];

    $inserta = $db->prepare("INSERT INTO `trabajcontratos` (`idContrato`, `nroContrato`, `idTrabajador`, `regimen`, tipoContrato,  `modo`, `estado`, `fchInicio`, `fchFin`, `observacion`, `creacUsuario`, `creacFch`) VALUES (:idContrato, :nroContrato, :idTrabajador, :regimen, :tipoContrato, :modo, :estado, :fchInicio, :fchFin, :observacion, :creacUsuario, now())");
    $inserta->bindParam(':idContrato',$idContrato);
    $inserta->bindParam(':nroContrato',$nroContrato);
    $inserta->bindParam(':idTrabajador',$idTrabajador);
    $inserta->bindParam(':regimen',$regimen);
    $inserta->bindParam(':tipoContrato',$tipoContrato);
    $inserta->bindParam(':modo',$modo);
    $inserta->bindParam(':estado',$estado);
    $inserta->bindParam(':fchInicio',$fchInicio);
    $inserta->bindParam(':fchFin',$fchFin);
    $inserta->bindParam(':observacion',$observacion);
    $inserta->bindParam(':creacUsuario',$creacUsuario);
    $inserta->execute();

    if($inserta->rowCount() >0 ){
      $descripcion = "Se han insertado un nuevo contrato $idContrato";
      logAccion($db, $descripcion, $idTrabajador, NULL);
      //Se genera el primer periodo vacacional
      if($regimen == "Mype") $dias = 15; else $dias = 30;
      $fchIniVacaciones = strtotime('+365 day', strtotime($fchInicio));
      $fchFinLimite = strtotime('+364 day', $fchIniVacaciones);
      $fchIniLimite = strtotime('-'.$dias.' day', $fchFinLimite);
      $fchPuedeIniciar = date('Y-m-d', $fchIniVacaciones);
      $fchFinLimite = date('Y-m-d', $fchFinLimite);
      $fchPuedeIniciarLimite = date('Y-m-d', $fchIniLimite);
      /*echo "Fecha Ini Vacaciones: $fchPuedeIniciar<br>";
      echo "Fecha fin Límite Vacaciones: $fchFinLimite<br>";
      echo "Fecha Ini Límite Vacaciones: $fchPuedeIniciarLimite";*/

      $idVacacion = generaIdVacacion($db);
      //echo "idVacacion: $idVacacion, idContrato: $idContrato, creacUsuario: $creacUsuario";
      $insertVac = $db->prepare("INSERT INTO `trabajcontratovacacs` (`idVacacion`, `idContrato`, `fchIniPeriodo`, `fchPuedeIniciar`, `fchPuedeIniciarLimite`, `diasTotales`, `diasUsados`, `estado`, `creacUsuario`, `creacFch`) VALUES (:idVacacion, :idContrato, :fchIniPeriodo, '$fchPuedeIniciar', '$fchPuedeIniciarLimite' , '$dias', 0, 'Pendiente', :creacUsuario, curdate())");
      $insertVac->bindParam(':idVacacion',$idVacacion);
      $insertVac->bindParam(':idContrato',$idContrato);
      $insertVac->bindParam(':fchIniPeriodo',$fchInicio);
      $insertVac->bindParam(':creacUsuario',$creacUsuario);
      $insertVac->execute();
      ////////////////////
      if ($_FILES["imgContrato"]["error"] == 0){

        $permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png","application/pdf");
        $limite_kb = 1000;
        if (in_array($_FILES['imgContrato']['type'], $permitidos) && $_FILES['imgContrato']['size'] <= $limite_kb * 1024){
          $auxNombre = $idTrabajador.$idContrato;
          $arrayBuscar = array(".", " ");
          $nombreArch =  str_replace($arrayBuscar, '_', $auxNombre);
          $auxExtension = explode('.',$_FILES['imgContrato']['name'] );
          $extension = end($auxExtension);
          $escaneo =  "$nombreArch.$extension";
          $ruta = "../imagenes/data/trabajador/contratos/".$escaneo;
          //echo $ruta;
          if (!file_exists($ruta)){
            $resultado = @move_uploaded_file($_FILES["imgContrato"]["tmp_name"], $ruta);
          } else {
            return -3; // este archivo existe";
          }
          $actualiza = $db->prepare("UPDATE `trabajcontratos` SET `rutaArchivo` = :rutaArchivo WHERE `idContrato` = :idContrato");
          $actualiza->bindParam(':idContrato',$idContrato);
          $actualiza->bindParam(':rutaArchivo',$ruta);
          $actualiza->execute();
        }        
      }
      ////////////////////
    }
    return $inserta->rowCount();
  }

  function buscarContrato($db){
    $idContrato = $_POST["idContrato"];
    $consulta = $db->prepare("SELECT `idContrato`, `nroContrato`, `idTrabajador`, `regimen`, tipoContrato, `modo`, `estado`, `fchInicio`, `fchFin`, `fchUltDiaTrabajado`, `observacion`, rutaArchivo, `creacUsuario`, `creacFch` FROM `trabajcontratos` WHERE idContrato = :idContrato LIMIT 1  ");
    $consulta->bindParam(':idContrato',$idContrato);
    $consulta->execute();
    return $consulta->fetch();
  }


  function editaContrato($db){
    $editaUsuario= $_SESSION['usuario'];
    $idContrato  = $_POST["editaIdContrato"];
    $nroContrato = $_POST["txtEditaContrNro"];
    $regimen = $_POST["cmbEditaContrRegimen"];
    $tipoContrato = $_POST["cmbEditaContrTipo"];
    $modo    = $_POST["cmbEditaContrModo"];
    $estado  = $_POST["cmbEditaContrEstado"];
    $fchFin  = $_POST["txtEditaContrFchFin"];
    $observacion = $_POST["txtEditaContrObserv"];
    $idTrabajador = $_POST["editaIdTrabajador"];
    $rutaOriginal = $_POST["rutaEditaOriginal"];

    //echo "idContrato: $idContrato, nroContrato: $nroContrato, regimen: $regimen, modo: $modo, estado: $estado, fchFin: $fchFin, observacion: $observacion, editaUsuario: $editaUsuario";

    ////////////////////
    $ruta = "";
    if ($_FILES["imgEditaContrato"]["error"] == 0){
      $permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png","application/pdf");
      $limite_kb = 1000;
      if (in_array($_FILES['imgEditaContrato']['type'], $permitidos) && $_FILES['imgEditaContrato']['size'] <= $limite_kb * 1024){
        if (file_exists($rutaOriginal)){
          unlink($rutaOriginal);
        }  
        //echo "Prueba";
        $auxNombre = $idTrabajador.$idContrato;
        $arrayBuscar = array(".", " ");
        $nombreArch =  str_replace($arrayBuscar, '_', $auxNombre);
        $auxExtension = explode('.',$_FILES['imgEditaContrato']['name'] );
        $extension = end($auxExtension);
        $escaneo =  "$nombreArch.$extension";
        $ruta = "../imagenes/data/trabajador/contratos/".$escaneo;
        $resultado = @move_uploaded_file($_FILES["imgEditaContrato"]["tmp_name"], $ruta);
      }
    }

    if($ruta == "") $ruta = $rutaOriginal;

    $actualiza = $db->prepare("UPDATE `trabajcontratos` SET `nroContrato` = :nroContrato, `regimen` = :regimen , `tipoContrato` = :tipoContrato, `modo` = :modo, `estado` = :estado, `fchFin` = :fchFin, rutaArchivo = :rutaArchivo, `observacion` = :observacion, editaUsuario = :editaUsuario, editaFch = curdate() WHERE `idContrato` = :idContrato");
    $actualiza->bindParam(':idContrato',$idContrato);
    $actualiza->bindParam(':nroContrato',$nroContrato);
    $actualiza->bindParam(':regimen',$regimen);
    $actualiza->bindParam(':tipoContrato',$tipoContrato);
    $actualiza->bindParam(':modo',$modo);
    $actualiza->bindParam(':estado',$estado);
    $actualiza->bindParam(':fchFin',$fchFin);
    $actualiza->bindParam(':rutaArchivo',$ruta);
    $actualiza->bindParam(':observacion',$observacion);
    $actualiza->bindParam(':editaUsuario',$editaUsuario);
    $actualiza->execute();

    if($actualiza->rowCount() == 1){
      $descripcion = "Se ha actualizado el contrato $idContrato";
      logAccion($db, $descripcion, $idTrabajador, NULL);
    }
    return $actualiza->rowCount();
  }

  function eliminaContrato($db){
    //$elimina= $_SESSION['usuario'];
    $idContrato  = $_POST["eliminaIdContrato"];
    $nroContrato = $_POST["txtEliminaContrNro"];
    $rutaOriginal = $_POST["rutaEliminaOriginal"];

    $elimina = $db->prepare("DELETE FROM `trabajcontratos` WHERE `idContrato` = :idContrato AND nroContrato = :nroContrato");
    $elimina->bindParam(':idContrato',$idContrato);
    $elimina->bindParam(':nroContrato',$nroContrato);
    $elimina->execute();

    if($elimina->rowCount() == 1){
      $descripcion = "Se ha eliminado el contrato $idContrato";
      logAccion($db, $descripcion, NULL, NULL);
      if (file_exists($rutaOriginal)){
          unlink($rutaOriginal);
      }
    }
    return $elimina->rowCount();
  }

  function buscarSiHayContratoActivo($db){
    $idTrabajador = $_POST["idTrabajador"];
    $renov = utf8_encode("Renovación");
    $consulta = $db->prepare("SELECT count(*) AS cant FROM `trabajcontratos` WHERE `idTrabajador` LIKE :idTrabajador AND modo IN ('Alta','$renov') AND estado = 'Activo' ");
    $consulta->bindParam(':idTrabajador',$idTrabajador);
    $consulta->execute();
    return $consulta->fetch();
  }

  function buscarVacacion($db){
    $idVacacion = $_POST['idVacacion'];
  /*  $consulta = $db->prepare("SELECT `idVacacion`, `idContrato`, `fchPuedeIniciar`, `fchPuedeIniciarLimite`, `diasTotales`, `diasUsados`, `estado`, `fchIniSalida`, `fchFinSalida`, `creacUsuario`, `creacFch` FROM `trabajcontratovacacs` WHERE idVacacion like :idVacacion  ");
*/
    $consulta = $db->prepare("SELECT `idVacacion`, `idContrato`, `fchPuedeIniciar`, `fchPuedeIniciarLimite`, `diasTotales`, `diasUsados`, `estado`, `creacUsuario`, `creacFch` FROM `trabajcontratovacacs` WHERE idVacacion like :idVacacion  ");
    $consulta->bindParam(':idVacacion',$idVacacion);
    $consulta->execute();
    return $consulta->fetch();
  }

  function generaIdSalida($db){
    //$anhio = Date("Y");
    $consulta = $db->prepare("SELECT idSalida FROM trabajcontratovacacusadas ORDER BY idSalida DESC limit 1");
    $consulta->execute();
    $result = $consulta->fetchAll();
    $nvoId = 0;
    // Se obtiene el resultado de la consulta
    foreach($result as $fila) {
      $ultId = $fila['idSalida'];
      $nvoId = 1*$ultId + 1;
      
    }
    if ($nvoId == 0)  $nvoId = 1;
    return $nvoId;

  }

  /////
  function obtenerIdTrabajadorDeContrato($db, $idVacacion){
    $consulta = $db->prepare("SELECT idTrabajador FROM trabajcontratovacacs AS trabconvac, trabajcontratos AS trabcon WHERE `trabconvac`.idContrato = `trabcon`.idContrato AND idVacacion = :idVacacion ");
    $consulta->bindParam(':idVacacion',$idVacacion);
    $consulta->execute();
    return $consulta->fetch();
  }


  function calcularPagoVacac($db, $fchIni, $fchFin, $idTrabajador ){
    $consulta = $db->prepare("SELECT fchQuincena, quindet.id, idTrabajador, round(sum(valor)/6,2) AS pagoVacac  FROM quincenadetallecontab AS quindet, planillaitems AS plaitem WHERE plaitem.id = quindet.id AND grupoNro = '1' AND plaitem.id NOT IN ('inasisten') AND fchQuincena BETWEEN :fchIni AND :fchFin AND idTrabajador = :idTrabajador ");
    $consulta->bindParam(':fchIni',$fchIni);
    $consulta->bindParam(':fchFin',$fchFin);
    $consulta->bindParam(':idTrabajador',$idTrabajador);
    $consulta->execute();
    return $consulta->fetch();
  }
  /////

  function programarSalida($db){
    $idSalida = generaIdSalida($db);
    $creacUsuario = $_SESSION['usuario'];
    $idVacacion = $_POST["txtIdVacacion"];
    $idVacacionVerif = $_POST["idVacacion"];
    $diasTotales = $_POST["txtDiasTotales"];
    $fchPuedeIniciar = $_POST["txtFchPuedeIniciar"];
    $fchPuedeIniciarLimite = $_POST["txtFchPuedeIniciarLimite"];
    $fchInicio = $_POST["txtFchIniSalida"];
    $fchFin = $_POST["txtFchFinSalida"];
    $fchDiaIni = substr($fchInicio,5,2);
    $fchAuxiliar = strtotime ('-1 day', strtotime($fchInicio));

    //////////////////////
    /*$fechaActual =  "2021-06-01"; // date('Y-m-d');
    $fechaMesPasado = strtotime ('-5 month', strtotime($fechaActual));
    $fechaMesPasadoDate = date('Y-m-d', $fechaMesPasado);
    $fchIniPago = $fechaMesPasadoDate;
    $fchFinPago = substr("$fechaActual",0,8)."28";*/
    //////////////////////

    $fchDiaIni = substr($fchInicio,8,2);
    if($fchDiaIni == '01') $fchQuincVacac = substr($fchInicio,0,8)."14";
    else $fchQuincVacac = substr($fchInicio,0,8)."28";
    $fchAuxiliar = date('Y-m-d',strtotime ('-18 days', strtotime($fchInicio)));

    $fchQuincFinCalculo = substr($fchAuxiliar,0,8)."28";
    $fchAuxiliar = date('Y-m-d',strtotime ('-5 months', strtotime($fchQuincFinCalculo)));
    $fchQuincIniCalculo = substr($fchAuxiliar,0,8)."01";

    //Validar 
    if($idVacacionVerif != $idVacacion ) return -1; //Id Vacación manipulada
    else {
      $pagoVacac = 0;
      $auxIdTrab = obtenerIdTrabajadorDeContrato($db, $idVacacion);
      $idTrabajador = $auxIdTrab["idTrabajador"];

      ///////////
      //echo "idSalida: $idSalida, idVacacion: $idVacacion, fchInicio: $fchInicio, fchFin: $fchFin, pagoVacac: $pagoVacac, fchQuincIniCalculo: $fchQuincIniCalculo, fchQuincFinCalculo: $fchQuincFinCalculo, fchQuincVacac: $fchQuincVacac, creacUsuario: $creacUsuario";
      ///////////

      $inserta = $db->prepare("INSERT INTO `trabajcontratovacacusadas` (`idSalida`, `idVacacion`, `fchInicio`, `fchFin`, `pagoVacac`, `fchQuincIniCalculo`, `fchQuincFinCalculo`, `fchQuincVacac`  ,`estado`, `creacUsuario`, `creacFch`) VALUES (:idSalida, :idVacacion, :fchInicio, :fchFin, :pagoVacac, :fchQuincIniCalculo, :fchQuincFinCalculo, :fchQuincVacac , 'Programado', :creacUsuario, CURRENT_DATE())");
      $inserta->bindParam(':idSalida',$idSalida);
      $inserta->bindParam(':idVacacion',$idVacacion);
      $inserta->bindParam(':fchInicio',$fchInicio);
      $inserta->bindParam(':fchFin',$fchFin);
      $inserta->bindParam(':pagoVacac',$pagoVacac);
      $inserta->bindParam(':fchQuincIniCalculo',$fchQuincIniCalculo);
      $inserta->bindParam(':fchQuincFinCalculo',$fchQuincFinCalculo);
      $inserta->bindParam(':fchQuincVacac',$fchQuincVacac);
      $inserta->bindParam(':creacUsuario',$creacUsuario);
      //$inserta->bindParam(':estado',$estado);
      $inserta->execute();
      if($inserta->rowCount() == 1 ){
        $actualiza = $db->prepare("UPDATE `trabajcontratovacacs` SET `estado` = 'Programado' WHERE `idVacacion` = :idVacacion");
        $actualiza->bindParam(':idVacacion',$idVacacion); 
        $actualiza->execute();

        return $inserta->rowCount();
      } else {
        return 0;
      }
    }
  }

  function generarPreliquidCobranza($db){
    $creacUsuario = $_SESSION["usuario"];
    $id = $_POST['idPreliquid']; 
    $idCliente = $_POST['idCliente'];

    $inserta = $db->prepare("INSERT INTO `preliquidacioncobranza` (`id`, `creacUsuario`, `creacFch`) VALUES (:id, :creacUsuario, NOW())");
    $inserta->bindParam(':id',$id);
    $inserta->bindParam(':creacUsuario',$creacUsuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function asociarPreliquidADetallesPorCobrar($db){
    $usuario = $_SESSION["usuario"];
    $fecha = Date("Y-m-d");
    $idPreliquid = utf8_decode($_POST['idPreliquid']);
    $atributosElem = utf8_decode($_POST['atributosElem']);

    $auxAtributos = explode("|",$atributosElem);

    $fchDespacho = $auxAtributos[1];
    $correlativo = $auxAtributos[2];
    $codigo = $auxAtributos[3];

    //echo "idPreliquid: $idPreliquid, fchDespacho: $fchDespacho, correlativo: $correlativo, codigo: $codigo";

    $actualiza = $db->prepare("UPDATE `despachodetallesporcobrar` SET `idPreliquid` = :idPreliquid WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `codigo` = :codigo");
    $actualiza->bindParam(':idPreliquid',$idPreliquid);
    $actualiza->bindParam(':fchDespacho',$fchDespacho);
    $actualiza->bindParam(':correlativo',$correlativo);
    $actualiza->bindParam(':codigo',$codigo);
    $actualiza->execute();

    if($actualiza->rowCount() == 1){
      $descripcion = "Se ha asociado el detalle por cobrar $atributosElem a la preliquidación $idPreliquid";
      logAccion($db, $descripcion, null, NULL);
    }
    return $actualiza->rowCount();

  }

  function buscarPreliquid($db){
    $idPreliquid = $_POST["idPreliquid"];
    $consulta = $db->prepare("SELECT `id`, `estado`, `creacUsuario`, `creacFch`, `editaUsuario`, `editaFch` FROM `preliquidacioncobranza` WHERE id = :idPreliquid");
    $consulta->bindParam(':idPreliquid',$idPreliquid);
    $consulta->execute();
    return $consulta->fetch();
  }

  function buscarDetPreliquid($db){
    $idPreliquid = $_POST["idPreliquid"];
    $consulta = $db->prepare("SELECT `fchDespacho`, `correlativo`, `codigo`, `costoUnit`, `cantidad`, `docCobranza`, `tipoDoc`, `pagado`, `idPreliquid`, `observDetallePorCobrar`, `usuario`, `fchCreacion`, `usuarioCreaCobranza`, `fchCreaCobranza` FROM `despachodetallesporcobrar` WHERE `idPreliquid` LIKE :idPreliquid");
    $consulta->bindParam(':idPreliquid',$idPreliquid);
    $consulta->execute();
    return $consulta->fetchAll();
  }


  function verDetallePreliquid($db){
    $_SESSION["dataEncabPreliquid"] = buscarPreliquid($db);
    $_SESSION["dataDetallePreliquid"] = buscarDetPreliquid($db);

    $rptaEncab = "";
    $rptaEncab .= "<div>
    <table>
      <tr>
        <td colspan = '6'><b>Detalles de la Preliquidación</b></td>
      </tr>
      <tr>
        <td colspan = '2'>Id Preliquidacion</td>
        <td colspan = '2'>".$_SESSION["dataEncabPreliquid"]['id']."</td>
        <td rowspan = '2' ><img src='imagenes/hoja.png' width='25' height='25'  onclick = 'descargarExcel()'></td>
      </tr>
      <tr>
        <td colspan = '2'>Estado</td>
        <td colspan = '2'>".$_SESSION["dataEncabPreliquid"]['estado']."</td>
      </tr>
      <tr>
        <td colspan = '2'>Fecha Creación</td>
        <td colspan = '2'>".$_SESSION["dataEncabPreliquid"]['creacFch']."</td>
      </tr>
      <tr>
        <td colspan = '6'></td>
      </tr>
      ";

    $rptaEncab .= "<tr>";
    $rptaEncab .= "<th width = '40'>Nro.</th>";
    $rptaEncab .= "<th width = '80'>Fch. Despacho</th>";
    $rptaEncab .= "<th width = '70'>Correlativo</th>";
    $rptaEncab .= "<th width = '75'>Codigo</th>";
    $rptaEncab .= "<th width = '70'>Costo Unit</th>";
    $rptaEncab .= "<th width = '60'>Cantidad</th>";
    $rptaEncab .= "<th width = '70'>Monto</th>";
    $rptaEncab .= "</tr>";  
    $total = $nro = 0;

    foreach ($_SESSION["dataDetallePreliquid"] as $key => $value) {
      $total += $value["costoUnit"] * $value["cantidad"];
      $rptaEncab .= "<tr>";
      $rptaEncab .= "<td>".(++$nro)."</td>";
      $rptaEncab .= "<td>".$value["fchDespacho"]."</td>";
      $rptaEncab .= "<td>".$value["correlativo"]."</td>";
      $rptaEncab .= "<td>".$value["codigo"]."</td>";
      $rptaEncab .= "<td>".$value["costoUnit"]."</td>";
      $rptaEncab .= "<td>".$value["cantidad"]."</td>";
      $rptaEncab .= "<td align = 'right' >".number_format($value["costoUnit"] * $value["cantidad"],2)."</td>";
      $rptaEncab .= "</tr>";      
    }
    $rptaEncab .= "<tr>";
    $rptaEncab .= "<td colspan= '5'></td>";
    $rptaEncab .= "<th align = 'right' >Total</th>";
    $rptaEncab .= "<td align = 'right' >".number_format($total,2)."</td>";
    $rptaEncab .= "</tr>";
    $rptaEncab .= "</table>";
    return utf8_encode($rptaEncab);
  }

  function elimPreliquid($db){
    $auxElimIdPreliquid = $_POST["txtElimPreliquid"];
    $elimIdPreliquid = $_POST['elimIdPreliquid'];

    if($elimIdPreliquid != "" && $auxElimIdPreliquid == $elimIdPreliquid){
      $actualiza = $db->prepare("SELECT `fchDespacho`, `correlativo`, `codigo`, costoUnit * cantidad AS monto FROM  `despachodetallesporcobrar` WHERE  `idPreliquid` = :idPreliquid ");
      $actualiza->bindParam(':idPreliquid',$elimIdPreliquid);
      $actualiza->execute();
      $_SESSION['desmarcarDespDetCobrar'] = $actualiza->fetchAll();
      $_SESSION['desmarcarIdPreliquid'] = $elimIdPreliquid;

      $cadVistaPrevia = "";
      foreach ($_SESSION['desmarcarDespDetCobrar'] as $key => $value) {
        //echo "Valor: ". $value["fchDespacho"];
        /*
         "<tr class = 'tblVPPreliquid' ><td class= 'dataVistaPrevia' cp = 'Preliquid|"+ fchDespacho + "|"+ correl+ "|"+ codigo +"'>"+fchDespacho + "-"+ correl+"-"+codigo +"</td><td align = 'right' class = 'montoPrevio'>"+monto+"</td></tr>";
         */
         $cadVistaPrevia .= "<tr class = 'tblVPPreliquid' ><td class= 'dataVistaPrevia' cp = 'Preliquid|". $value["fchDespacho"] . "|". $value["correlativo"]. "|". $value["codigo"] ."'>".$value["fchDespacho"] . "-". $value["correlativo"]."-".$value["codigo"] ."</td><td align = 'right' class = 'montoPrevio'>".$value["monto"]."</td></tr>";

      }

      //Liberar los detallesporcobrar
      $actualiza = $db->prepare("UPDATE `despachodetallesporcobrar` SET  `idPreliquid` = NULL WHERE  `idPreliquid` = :idPreliquid ");
      $actualiza->bindParam(':idPreliquid',$elimIdPreliquid);
      $actualiza->execute();

      //Se elimina la preliquidación
      $elimina = $db->prepare("DELETE FROM `preliquidacioncobranza` WHERE `id` = :id");
      $elimina->bindParam(':id',$elimIdPreliquid);
      $elimina->execute();

      if($elimina->rowCount() == 1){
        $descripcion = "Se ha eliminado la preliquidación de cobranza $elimIdPreliquid";
        logAccion($db, $descripcion, NULL, NULL);
      }
      return $elimina->rowCount()."@".$cadVistaPrevia;
    } else {
      return -1;
    }
  }

  function marcaMasivaDetalles($db){
    $fchIni = $_POST["txtMasivoFchInicio"];
    $fchFin = $_POST["txtMasivoFchFin"];
    $correlCuenta = $_POST["cmbMasivoCuenta"];
    $idCliente = $_POST['masivoTercero'];

    $where = "";
    if($fchIni != "")
      if($fchFin != "") $where .= " AND `desdet`.fchDespacho BETWEEN :fchIni AND :fchFin";
      else $where .= " AND `desdet`.fchDespacho >= :fchIni ";
    else
      if($fchFin != "") $where .= " AND `desdet`.fchDespacho <= :fchFin ";

    if($correlCuenta != "-") $where .= " AND correlCuenta = :correlCuenta ";


    $consulta = $db->prepare("SELECT `desdet`.fchDespacho, `desdet`.correlativo, `desdet`.costoUnit, `desdet`.cantidad ,`desdet`.codigo FROM despacho AS des, despachodetallesporcobrar AS desdet WHERE `des`.fchDespacho = `desdet`.fchDespacho AND `des`.correlativo = `desdet`.correlativo AND `des`.idCliente = :idCliente   AND idPreliquid IS NULL  $where ");
    //$consulta->bindParam(':fchIni',$fchIni);
    //$consulta->bindParam(':fchFin',$fchFin);
    $consulta->bindParam(':idCliente',$idCliente);
    if($fchIni != "") $consulta->bindParam(':fchIni',$fchIni);
    if($fchFin != "") $consulta->bindParam(':fchFin',$fchFin);
    if($correlCuenta != "-") $consulta->bindParam(':correlCuenta',$correlCuenta);
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function editarDetPreliquid($db){
    $costoUnit  = $_POST["txtEditDetPreliquidCtoUnit"];
    $cantidad = $_POST["txtEditDetPreliquidCant"];
    $observ = $_POST["txtEditDetPreliquidObs"];

    $fchDespacho = $_POST['editDetFchDespacho'];
    $correlativo = $_POST['editDetCorrelativo'];
    $codigo = $_POST['editDetCodigo'];

    $actualiza = $db->prepare("UPDATE `despachodetallesporcobrar` SET `costoUnit` = :costoUnit, `cantidad` = :cantidad, `observDetallePorCobrar` = :observ WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `codigo` = :codigo ");
    $actualiza->bindParam(':costoUnit',$costoUnit);
    $actualiza->bindParam(':cantidad',$cantidad);
    $actualiza->bindParam(':observ',$observ);
    $actualiza->bindParam(':fchDespacho',$fchDespacho);
    $actualiza->bindParam(':correlativo',$correlativo);
    $actualiza->bindParam(':codigo',$codigo);
    $actualiza->execute();

    if($actualiza->rowCount() >0 ){
      $descripcion = "Se ha modificado el siguiente detalle por cobrar: $fchDespacho-$correlativo-$codigo por los valores $costoUnit- $cantidad- $observ";
      logAccion($db, $descripcion, NULL, NULL);
    }
    return $actualiza->rowCount();
  }

  function aprobarPreliquid($db){
    $idPreliquid  = $_POST["txtAprobPreliquid"];
    $idPredAux = $_POST["aprobIdPreliquid"];

    if($idPreliquid == $idPredAux){
      $actualiza = $db->prepare("UPDATE `preliquidacioncobranza` SET `estado` = 'Aprobada' WHERE `id` = :id");
      $actualiza->bindParam(':id',$idPreliquid);
      $actualiza->execute();
      if($actualiza->rowCount() >0 ){
        $descripcion = "Se ha modificado ha aprobada el estado de la preliquidación de cobranza $idPreliquid";
        logAccion($db, $descripcion, NULL, NULL);
      }
      return $actualiza->rowCount();
    } else {
      return -1;
    }
  }

  function verificarFacturaIdPreliquidCob($db){
    $docCobranza = $_POST["valor"];
    $idCliente = $_POST["idCliente"];
    $tipoDoc = $_POST["tipoDoc"];
    
    $consulta = $db->prepare("SELECT count(*) AS cant FROM doccobranza WHERE docCobranza = :docCobranza AND tipoDoc = :tipoDoc AND cobIdCliente = :idCliente");
    $consulta->bindParam(':docCobranza',$docCobranza);
    $consulta->bindParam(':tipoDoc',$tipoDoc);
    $consulta->bindParam(':idCliente',$idCliente);
    $consulta->execute();
    return $consulta->fetch();
  }

  function facturarPreliquid($db){
    $usuario = $_SESSION["usuario"];
    $idCliente = $_POST["factIdCliente"];
    $consulta = $db->prepare("SELECT `cliente`.idRuc, rznSocial, nombre, dirCalleNro, `cliente`.dirDistrito, actividad, maximoHras, idCoordinador, concat(`trabajador`.nombres,' ',`trabajador`.apPaterno,' ',`trabajador`.apMaterno) as nombreCompleto , estadoCliente, categCliente, pagWeb, fax, diasPago, `cliente`.formaPago, `cliente`.bancoNombre, `cliente`.bancoCuentaNro, `cliente`.bancoCuentaTipo, `cliente`.idCondicion, `cliente`.bancoCuentaMoneda, `clicondPag`.nroDias, `clicondPag`.nombCondicion FROM cliente LEFT JOIN trabajador ON `cliente`.idCoordinador = `trabajador`.idTrabajador LEFT JOIN clientecondicionespago AS clicondpag ON `cliente`.idCondicion = `clicondpag`.idCondicion WHERE `cliente`.idRuc = :idCliente ");
    $consulta->bindParam(':idCliente',$idCliente);
    $consulta->execute();
    $auxCli = $consulta->fetch();

    $nroDias = $auxCli["nroDias"] == NULL ? 0 : $auxCli["nroDias"];

/*    
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    `fchDocCobranza`, `ordenCompra`
*/
    
    $mntTot = 0;
    $yaExiste = "No";
    $coma = $losValores = "";

    $losCampos = "INSERT INTO `doccobranza` (`docCobranza`, `tipoDoc`, `estado`, `fchDocCobranza`, `fchVencDocCobranza`, `ordenCompra`, `mntDoc`, `observacion`,  `cobIdCliente`,  `idPreliquid`, `usuario`, `fchCreacion`, `hraCreacion`) VALUES ";

    foreach ($_POST as $campo => $dato) {
      if(substr($campo,0,24) == "txtFactPreliquidSerieNro" ){
        $nroFactura = $dato;
        $consulta = $db->prepare("SELECT count(*) AS cant FROM doccobranza WHERE docCobranza = :docCobranza AND cobIdCliente = :idCliente");
        $consulta->bindParam(':docCobranza',$dato);
        $consulta->bindParam(':idCliente',$factIdCliente);
        $consulta->execute();
        $auxCant = $consulta->fetch();
        if($auxCant["cant"] > 0) $yaExiste = "Si";
      } else if(substr($campo,0,23) == "cmbFactPreliquidTipoDoc" ){
        $tipoDoc = $dato;
      } else if(substr($campo,0,19) == "txtFactPreliquidFch" ){
        $fchDoc = $dato;//Hay que generar la fecha de vencimiento

        $mod_date = strtotime($fchDoc."+ ".$nroDias." days");
        $fchVenc = date("Y-m-d",$mod_date);

      } else if(substr($campo,0,18) == "txtFactPreliquidOc" ){
        $ordenCompra = $dato;
      } else if(substr($campo,0,23) == "txtFactPreliquidDescrip" ){
        $descrip = $dato;
      } else if(substr($campo,0,21) == "txtFactPreliquidMonto" ){
        $monto = $dato;
        if(!is_numeric($dato)) $dato = 0;
        $mntTot += 1*$dato;
        if($nroFactura != ""  ){
          //$losValores .= "$coma('$nroFactura', 'Factura', 'Presentada', $monto , '$descrip', '$factIdCliente' , '$txtFacturarPreliquid', '$usuario', curdate(), curtime())";
          $losValores .= "$coma('$nroFactura', '$tipoDoc', 'Emitida', '$fchDoc', '$fchVenc', '$ordenCompra' , $monto , '$descrip', '$factIdCliente' , '$txtFacturarPreliquid', '$usuario', curdate(), curtime())";
          if($coma == "")$coma = ", ";
        }
      }
       //else {
      $$campo = $dato;
        
    }

    if($yaExiste == "No" && $mntTot == $txtFacturarPreliquidValor ){
      $instruccion = $losCampos.$losValores;
      //echo $instruccion."<>";
      $nro = $db->exec($instruccion);
      if($nro > 0){
        $actualiza = $db->prepare("UPDATE `preliquidacioncobranza` SET `estado` = 'Facturada' WHERE `id` = :id");
        $actualiza->bindParam(':id',$txtFacturarPreliquid);
        $actualiza->execute();
        if($actualiza->rowCount() >0 ){
          $descripcion = "Se ha modificado ha facturada el estado de la preliquidación de cobranza $txtFacturarPreliquid";
          logAccion($db, $descripcion, NULL, NULL);
        }
        return $actualiza->rowCount();
      }
    } else {
      return -1;
    }
    //echo $completo;
  }

  function buscarTipoCobrarotros($db,$dato){
    $datos = array();
    $consulta = $db->prepare("SELECT concat(`idConcepto`,'|', `concepto`  ) AS `dato`  , `concepto`, `estadoCobrarOtros` FROM `despachocobrarotros` WHERE estadoCobrarOtros = 'Activo' AND ( `idConcepto` LIKE '$dato%' OR `concepto` LIKE '$dato%' )");
    $consulta->execute();
    $todo = $consulta->fetchAll(PDO::FETCH_ASSOC);
    foreach($todo as $item){
      $datos[] = array("value" => $item['dato']);
    }
    return $datos;
  }

  function addDocPorCobrar($db){
    /*
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    */
    $fchDespacho = $_POST["addDocCobrarFchDespacho"];
    $correlativo = $_POST["addDocCobrarCorrelativo"];
    $auxCodigo =  $_POST["txtAddTipoDocCobrar"];
    $codigo = strtok($auxCodigo,"|");
    $cantidad = 1;
    $ctoUnitario = $_POST["txtAddMontoDocCobrar"];
    $docCobranza = NULL;
    $pagado = "No";
    $observDetalle = $_POST["txtAddDescripDocCobrar"];
    $tipoDoc = "";
    $idPreliquid = ($_POST["addDocCobrarIdPreliquid"] == "") ? NULL : $_POST["addDocCobrarIdPreliquid"];
    
    $usuario = $_SESSION['usuario'];

    $inserta = $db->prepare("INSERT INTO `despachodetallesporcobrar` (`fchDespacho`, `correlativo`, `codigo`, `costoUnit`, `cantidad`, `docCobranza`, `pagado`, `idPreliquid`, `observDetallePorCobrar`, `tipoDoc`, `usuario`, `fchCreacion`) VALUES (:fchDespacho, :correlativo, :codigo, :ctoUnitario, :cantidad, :docCobranza, :pagado,  :idPreliquid, :observDetalle ,:tipoDoc, :usuario, curdate());");
    $inserta->bindParam(':fchDespacho',$fchDespacho);
    $inserta->bindParam(':correlativo',$correlativo);
    $inserta->bindParam(':codigo',$codigo);
    $inserta->bindParam(':cantidad',$cantidad);
    $inserta->bindParam(':ctoUnitario',$ctoUnitario);
    $inserta->bindParam(':docCobranza',$docCobranza);
    $inserta->bindParam(':pagado',$pagado);
    $inserta->bindParam(':idPreliquid',$idPreliquid);
    $inserta->bindParam(':observDetalle',$observDetalle);
    $inserta->bindParam(':tipoDoc',$tipoDoc);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function eliminaDocPorCobrar($db){
    $fchDespacho = $_POST["elimDocCobrarFchDespacho"];
    $correlativo = $_POST["elimDocCobrarCorrelativo"];
    $codigo =  $_POST["elimDocCobrarCodigo"];

    $elimina = $db->prepare("DELETE FROM `despachodetallesporcobrar` WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `codigo` = :codigo");
    $elimina->bindParam(':fchDespacho',$fchDespacho);
    $elimina->bindParam(':correlativo',$correlativo);
    $elimina->bindParam(':codigo',$codigo);
    $elimina->execute();
    return $elimina->rowCount();

  }

  function cargaFacturasPreliquid($db){
/*  
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
*/
    $docYaExiste = "No";
    $idCliente = $_POST['idClienteCarga'];
    $mntValor = round($_POST["txtFacturarPreliquidValorCarga"],2);

    # Recomiendo poner la ruta absoluta si no está junto al script
    # Nota: no necesariamente tiene que tener la extensión XLSX
    $objPHPExcel = IOFactory::load($_FILES['archFacturas']['tmp_name']);
    # Recuerda que un documento puede tener múltiples hojas
    # obtener conteo e iterar
    //$totalDeHojas = $objPHPExcel->getSheetCount();
    $hojaActual = $objPHPExcel->getSheet(0);    
    # Calcular el máximo valor de la fila como entero, es decir, el
    # límite de nuestro ciclo
    $nroMayorDeFila = $hojaActual->getHighestRow(); // Numérico
    $letraMayorDeColumna = $hojaActual->getHighestColumn(); // Letra
    # Convertir la letra al número de columna correspondiente
    $nroMayorDeColumna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($letraMayorDeColumna);

    //echo "Mayor fila: $nroMayorDeFila, Letra Maoyr Columna: $letraMayorDeColumna, Mayor Columna: $nroMayorDeColumna";
    //$indiceColumna = 2;
    $mntTotCargado = 0;
    $resultado = "<table>";
    /*$resultado .= "<thead>";
    $resultado .= "<tr>";
    $resultado .= "<th>Tipo Doc</th><th>Serie-Número</th><th>Fecha</th>";
    $resultado .= "<th>O/C</th><th>Descripcion</th><th>Monto</th>";
    $resultado .= "</tr>";
    $resultado .= "</thead>";
    $resultado .= "<tbody>";*/

    for ($fila=1; $fila <= $nroMayorDeFila ; $fila++) {
      $resultado .= "<tr>";
      for ($columna=1; $columna <= $nroMayorDeColumna; $columna++) { 
        $celda = $hojaActual->getCellByColumnAndRow($columna, $fila);
        # Y ahora que tenemos una celda trabajamos con ella igual que antes
        # El valor, así como está en el documento
        //$valorRaw = $celda->getValue();
        $valorFormateado = $celda->getFormattedValue();
  /*      if($fila == 1 )
          if($columna == 1) $ancho = " width = '62px' ";
          else if($columna == 2) $ancho = " width = '77px' ";
          else if($columna == 3) $ancho = " width = '65px' ";
          else if($columna == 4) $ancho = " width = '65px' ";
          else if($columna == 5) $ancho = " width = '270px' ";
          else $ancho = ""; 
*/
        if($fila == 1 )
          if($columna == 1) $resultado .= "<th width = '62px'>$valorFormateado</th>";
          else if($columna == 2) $resultado .= "<th width = '77px'>$valorFormateado</th>";
          else if($columna == 3) $resultado .= "<th width = '65px'>$valorFormateado</th>";
          else if($columna == 4) $resultado .= "<th width = '65px'>$valorFormateado</th>";
          else if($columna == 5) $resultado .= "<th width = '400px'>$valorFormateado</th>";
          else  $resultado .= "<th width = '100px'>$valorFormateado</th>";
        else
          if($columna == 1){
            $tipoDoc = $valorFormateado;
            $resultado .= "<td width = '62px'>$valorFormateado</td>";
          } else if($columna == 2) {
            $docCobranza = $valorFormateado;
            //$idCliente = $_POST["idCliente"];
            $consulta = $db->prepare("SELECT count(*) AS cant FROM doccobranza WHERE docCobranza = :docCobranza AND tipoDoc = :tipoDoc AND cobIdCliente = :idCliente");
            $consulta->bindParam(':docCobranza',$docCobranza);
            $consulta->bindParam(':tipoDoc',$tipoDoc);
            $consulta->bindParam(':idCliente',$idCliente);
            $consulta->execute();
            $auxData = $consulta->fetch();

            if($auxData["cant"] == 1 ){
              $resultado .= "<td width = '77px' class = 'rojo' >$docCobranza</td>";
              $docYaExiste = "Si";
            } 
            else $resultado .= "<td width = '77px'>$docCobranza</td>";

            //$resultado .= "<td width = '77px'>$docCobranza</td>";
          } else if($columna == 3) {
            $resultado .= "<td width = '65px'>$valorFormateado</td>";
          } else if($columna == 4) {
            $resultado .= "<td width = '65px'>$valorFormateado</td>";
          } else if($columna == 5) {
            $resultado .= "<td width = '400px'>$valorFormateado</td>";
          } else {
            $mntTotCargado = $mntTotCargado + $valorFormateado; 
            $resultado .= "<td width = '100px'>$valorFormateado</td>";

          }
          //$resultado .= "<td>$valorFormateado</td>";

        //echo "Celda $fila, $indiceColumna -->$valorRaw, ";
      }
      //echo "<br>";
      $resultado .= "</tr>";
    }
    $resultado .= "<tr><td colspan = '5'></td><td>$mntTotCargado</td></tr>";
    //$resultado .= "</tbody>";
    $resultado .= "</table>";
    $mntTotCargado = round($mntTotCargado,2);
    if($mntTotCargado == $mntValor && $docYaExiste == "No" ) $resultado .= "|Ok";
    else $resultado .= "|Error";

    return $resultado;
    //////////////
  }

  function guardarCargaFacturasPreliquid($db){
    /*
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
    */

    ////////////////////////////
    $usuario = $_SESSION["usuario"];
    $idCliente = $_POST['idClienteCarga'];
    $mntValor =  round($_POST["txtFacturarPreliquidValorCarga"],2);
    $idPreliquid = $_POST["txtFacturarPreliquidCarga"];

    # Recomiendo poner la ruta absoluta si no está junto al script
    # Nota: no necesariamente tiene que tener la extensión XLSX
    $objPHPExcel = IOFactory::load($_FILES['archFacturas']['tmp_name']);
    $hojaActual = $objPHPExcel->getSheet(0);    
    # Calcular el máximo valor de la fila como entero, es decir, el
    # límite de nuestro ciclo
    $nroMayorDeFila = $hojaActual->getHighestRow(); // Numérico
    $letraMayorDeColumna = $hojaActual->getHighestColumn(); // Letra
    # Convertir la letra al número de columna correspondiente
    $nroMayorDeColumna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($letraMayorDeColumna);

    $mntTotCargado = 0;
    $docYaExiste = "No";

    $losCampos = "INSERT INTO `doccobranza` (`docCobranza`, `tipoDoc`, `estado`, `fchDocCobranza`, `ordenCompra`, `mntDoc`, `observacion`,  `cobIdCliente`,  `idPreliquid`, `usuario`, `fchCreacion`, `hraCreacion`) VALUES ";

    $losValores = $coma = "";

    for ($fila=2; $fila <= $nroMayorDeFila ; $fila++) {
      for ($columna=1; $columna <= $nroMayorDeColumna; $columna++) { 
        $celda = $hojaActual->getCellByColumnAndRow($columna, $fila);
        # Y ahora que tenemos una celda trabajamos con ella igual que antes
        # El valor, así como está en el documento
        //$valorRaw = $celda->getValue();
        $valorFormateado = $celda->getFormattedValue();
        if($columna == 1){
          $tipoDoc = $valorFormateado;
        } else if($columna == 2) {
          $docCobranza = $valorFormateado;
          //$idCliente = $_POST["idCliente"];
          $consulta = $db->prepare("SELECT count(*) AS cant FROM doccobranza WHERE docCobranza = :docCobranza AND tipoDoc = :tipoDoc AND cobIdCliente = :idCliente");
          $consulta->bindParam(':docCobranza',$docCobranza);
          $consulta->bindParam(':tipoDoc',$tipoDoc);
          $consulta->bindParam(':idCliente',$idCliente);
          $consulta->execute();
          $auxData = $consulta->fetch();

          if($auxData["cant"] == 1 ){
            $docYaExiste = "Si";
          }
        } else if($columna == 3) {
          $fchDoc = $valorFormateado;
        } else if($columna == 4) {
          $ordenCompra = $valorFormateado;
        } else if($columna == 5) {
          $descrip = $valorFormateado;
        } else {
          $monto = $valorFormateado;
          $mntTotCargado = $mntTotCargado + $valorFormateado;
        }
        
      }
      $mntTotCargado = round($mntTotCargado,2);
      $losValores .= "$coma('$docCobranza', '$tipoDoc', 'Presentada', '$fchDoc', '$ordenCompra' , $monto , '$descrip', '$idCliente' , '$idPreliquid', '$usuario', curdate(), curtime())";
      if($coma == "")$coma = ", ";
    }
    if($mntTotCargado == $mntValor && $docYaExiste == "No" ){
      $instruccion = $losCampos.$losValores;
      //echo $instruccion;
      $nro = $db->exec($instruccion);
      if( $nro > 0){
        $actualiza = $db->prepare("UPDATE `preliquidacioncobranza` SET `estado` = 'Facturada' WHERE `id` = :idPreliquid");
        $actualiza->bindParam(':idPreliquid',$idPreliquid);
        $actualiza->execute();
        return $actualiza->rowCount();
      } else {
        return -1;
      }

    } else {
      return -1;

    };
    ////////////////////////////
  }


  function insertaUsuPermiso($db, $idUsuario , $nombPermiso, $nivel = 1){

    $creacUsuario = $_SESSION["usuario"];
    $inserta = $db->prepare("INSERT INTO `usupermisos` ( `idUsuario`, `nombPermiso`, `nivel`, `creacUsuario`, `creacFch`) VALUES (:idUsuario, :nombPermiso, :nivel, :creacUsuario, now())");

    $inserta->bindParam(':idUsuario',$idUsuario);
    $inserta->bindParam(':nombPermiso',$nombPermiso);
    $inserta->bindParam(':nivel',$nivel);
    $inserta->bindParam(':creacUsuario',$creacUsuario);
    $inserta->execute();
    return $inserta->rowCount();

  }

  function editarUsuario($db){
/*    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
*/
    $cant = 0;
    $usuario = $_SESSION['usuario'];
    $nombre = $_POST['txtNombre'];
    $idUsuario = $_POST['idUsuario'];
    //$pass = $_POST['txtPass'];
    $chkInvitado = (isset($_POST['chkInvitado']))?$_POST['chkInvitado']:null;

    if ($chkInvitado == 'Si'){
      $chkIngreso = $chkEdicion = $chkConsulta = $chkAdmCostos = $chkAdmin = $chkAdmArt = NULL;
      $chkPlanilla = $chkAdmPlanilla = $chkAdmUnif = $chkPlame = $chkAdmMovil = $chkSeguridad = NULL;
    } else {
      //echo "Prueba no es invitado";
      $chkIngreso = isset($_POST['chkIngreso'])?$_POST['chkIngreso']:null;
      $chkEdicion = isset($_POST['chkEdicion'])?$_POST['chkEdicion']:null;
      $chkConsulta = isset($_POST['chkConsulta'])?$_POST['chkConsulta']:null;
      $chkAdmCostos = (isset($_POST['chkAdmCostos']))?$_POST['chkAdmCostos']:null;
      $chkAdmin = isset($_POST['chkAdmin'])?$_POST['chkAdmin']:null;
      $chkAdmArt = isset($_POST['chkAdmArt'])?$_POST['chkAdmArt']:null;
      $chkPlanilla = isset($_POST['chkPlanilla'])?$_POST['chkPlanilla']:null;
      $chkAdmPlanilla = isset($_POST['chkAdmPlanilla'])?$_POST['chkAdmPlanilla']:null;
      $chkAdmUnif = isset($_POST['chkAdmUnif'])?$_POST['chkAdmUnif']:null;
      $chkPlame = isset($_POST['chkPlame'])?$_POST['chkPlame']:null;
      $chkSeguridad = isset($_POST['chkSeguridad'])?$_POST['chkSeguridad']:null;
      $chkAdmMovil = (isset($_POST['chkAdmMovil']))?$_POST['chkAdmMovil']:null;
      $chkAsignDespacho = isset($_POST['chkAsignDespacho'])?$_POST['chkAsignDespacho']:null;
      $chkEditaUltKm = isset($_POST['chkEditaUltKm'])?$_POST['chkEditaUltKm']:null;
      $chkAdmVehiculo = isset($_POST['chkAdmVehiculo'])?$_POST['chkAdmVehiculo']:null;

      ////// Alertas //////
      $chkAlertVehVenc = isset($_POST['chkAlertVehVenc'])?$_POST['chkAlertVehVenc']:null;
      $chkAlertPersVenc = isset($_POST['chkAlertPersVenc'])?$_POST['chkAlertPersVenc']:null;
      $chkAlertCli = isset($_POST['chkAlertCli'])?$_POST['chkAlertCli']:null;
      $chkAlertCliCobr = isset($_POST['chkAlertCliCobr'])?$_POST['chkAlertCliCobr']:null;
      $chkAlertOpVeh = isset($_POST['chkAlertOpVeh'])?$_POST['chkAlertOpVeh']:null;
      $chkAlertOpDesp = isset($_POST['chkAlertOpDesp'])?$_POST['chkAlertOpDesp']:null;
      $chkAlertOpPers = isset($_POST['chkAlertOpPers'])?$_POST['chkAlertOpPers']:null;
      $chkAlertMantTbl = isset($_POST['chkAlertMantTbl'])?$_POST['chkAlertMantTbl']:null;
      $chkAlertMantPlani = isset($_POST['chkAlertMantPlani'])?$_POST['chkAlertMantPlani']:null;
      $chkAlertMantFact = isset($_POST['chkAlertMantFact'])?$_POST['chkAlertMantFact']:null;
      ///////////////////
    }

    $dni = (isset($_POST['txtDni']))?$_POST['txtDni']:null;
    $fchVencimiento = $_POST['txtFchVenc'];
    $tipoUsuario =  $_POST['cmbTipoUsuario'];
    $estado =  $_POST['cmbEstado'];
    $email =  $_POST['txtEmail'];
    $estadoOriginal = $_POST["estadoOriginal"];
    $cargaDespachos =  ($_POST['cmbCargaDespachos'] == 'No')?NULL:$_POST['cmbCargaDespachos'];

    /////////////////
    //echo "nombre: $nombre, chkAdmCostos: $chkAdmCostos, chkAdmArt: $chkAdmArt, chkAdmin: $chkAdmin, chkEdicion: $chkEdicion, chkIngreso: $chkIngreso, chkConsulta: $chkConsulta, chkPlanilla: $chkPlanilla, chkAsignDespacho: $chkAsignDespacho, chkAdmPlanilla: $chkAdmPlanilla, chkAdmUnif: $chkAdmUnif, cargaDespachos: $cargaDespachos, chkSeguridad: $chkSeguridad, chkInvitado: $chkInvitado, chkPlame: $chkPlame, chkAdmMovil: $chkAdmMovil, tipoUsuario: $tipoUsuario, dni: $dni, fchVencimiento: $fchVencimiento, email: $email, idUsuario: $idUsuario, usuario: $usuario";
      /////////////////
    $actualiza = $db->prepare("UPDATE `usuario` SET `nombre` = :nombre, `admin` = :chkAdmin, `admCostos` = :chkAdmCostos, `admArt` = :chkAdmArt, `edicion` = :chkEdicion, `ingreso` = :chkIngreso, `consulta` = :chkConsulta, `planilla` = :chkPlanilla, `asignDespacho` = :chkAsignDespacho,  `admPlanilla` = :chkAdmPlanilla,  `admUnif` = :chkAdmUnif,  `plame` = :chkPlame, `admMovil` = :chkAdmMovil, `tipoUsuario` = :tipoUsuario, `cargaDespachos` = :cargaDespachos, `seguridad` = :chkSeguridad,  `invitado` = :chkInvitado, `dni` = :dni, `fchVencimiento` = :fchVencimiento, `email` = :email, `estado` = :estado, `usuario` = :usuario   WHERE `idUsuario` = :idUsuario");
    $actualiza->bindParam(':nombre',$nombre);
    $actualiza->bindParam(':chkAdmCostos',$chkAdmCostos);
    $actualiza->bindParam(':chkAdmArt',$chkAdmArt);
    $actualiza->bindParam(':chkAdmin',$chkAdmin);
    $actualiza->bindParam(':chkEdicion',$chkEdicion);
    $actualiza->bindParam(':chkIngreso',$chkIngreso);
    $actualiza->bindParam(':chkConsulta',$chkConsulta);
    $actualiza->bindParam(':chkPlanilla',$chkPlanilla);
    $actualiza->bindParam(':chkAsignDespacho',$chkAsignDespacho);
    $actualiza->bindParam(':chkAdmPlanilla',$chkAdmPlanilla);
    $actualiza->bindParam(':chkAdmUnif',$chkAdmUnif);
    $actualiza->bindParam(':cargaDespachos',$cargaDespachos);
    $actualiza->bindParam(':chkSeguridad',$chkSeguridad);
    $actualiza->bindParam(':chkInvitado',$chkInvitado);
    $actualiza->bindParam(':chkPlame',$chkPlame);
    $actualiza->bindParam(':chkAdmMovil',$chkAdmMovil);
    $actualiza->bindParam(':tipoUsuario',$tipoUsuario);
    $actualiza->bindParam(':dni',$dni);
    $actualiza->bindParam(':fchVencimiento',$fchVencimiento);
    $actualiza->bindParam(':email',$email);
    $actualiza->bindParam(':estado',$estado);
    $actualiza->bindParam(':idUsuario',$idUsuario);
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->execute();
    $cant += $actualiza->rowCount();

    if($actualiza->rowCount() >0 && $estadoOriginal != $estado ){
      $descripcion = "Se ha modificado el estado de $idUsuario de $estadoOriginal a $estado.";
      logAccion($db, $descripcion, NULL, NULL);
    }

    //Elimina registros de permisos del usuario
    $elimina = $db->prepare("DELETE FROM `usupermisos` WHERE `idUsuario` = :idUsuario");
    $elimina->bindParam(':idUsuario',$idUsuario);    
    $elimina->execute();

    //Insertar alertas
    if($chkAlertVehVenc != NULL) $cant += insertaUsuPermiso($db, $idUsuario ,'alertVehVenc','1');
    if($chkAlertPersVenc != NULL) $cant += insertaUsuPermiso($db, $idUsuario ,'alertPersVenc','1');
    if($chkAlertCli != NULL)     $cant += insertaUsuPermiso($db, $idUsuario ,'alertCli','1');
    if($chkAlertCliCobr != NULL) $cant += insertaUsuPermiso($db, $idUsuario ,'alertCliCobr','1');
    if($chkAlertOpVeh != NULL)   $cant += insertaUsuPermiso($db, $idUsuario ,'alertOpVeh','1');
    if($chkAlertOpDesp != NULL)  $cant += insertaUsuPermiso($db, $idUsuario ,'alertOpDesp','1');
    if($chkAlertOpPers != NULL)  $cant += insertaUsuPermiso($db, $idUsuario ,'alertOpPers','1');
    if($chkAlertMantTbl != NULL)  $cant += insertaUsuPermiso($db, $idUsuario ,'alertMantTbl','1');
    if($chkAlertMantPlani != NULL) $cant += insertaUsuPermiso($db, $idUsuario ,'alertMantPlani','1');
    if($chkAlertMantFact != NULL) $cant += insertaUsuPermiso($db, $idUsuario ,'alertMantFact','1');

    //Insertar permiso
    if($chkEditaUltKm != NULL)  $cant += insertaUsuPermiso($db, $idUsuario ,'kmUltMedicion','1');
    if($chkAdmVehiculo != NULL) $cant += insertaUsuPermiso($db, $idUsuario ,'admVehiculo','1');
    /////////////////
    return $cant;
  }

  function mdlIdNvoCondPago($db){
    $prefijo = "CP";
    $consulta = $db->prepare("SELECT idCondicion FROM `clientecondicionespago` ORDER BY idCondicion DESC LIMIT 1  ");
    $consulta->execute();
    $result = $consulta->fetchAll();
    $nvoId = "";
    $ultId = "";
    // Se obtiene el resultado de la consulta
    foreach($result as $fila) {
      $ultId = $fila['idCondicion'];
      $parteNro = substr($ultId, 2,4);
      $nvoId = 1*$parteNro + 1;
      $nvoId = substr("0000".$nvoId,-4);
      $nvoId = $prefijo.$nvoId;
    }
    if ($ultId == "")  $nvoId = $prefijo."0001";
    return $nvoId;
  }

  function crearCondPago($db){
    /*
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    echo "<br>Id Condicion $idCondicion";
    */

    $idCondicion = mdlIdNvoCondPago($db);
    $nombCondPago = $_POST["txtNombCondicion"];
    $nroDias = $_POST["txtNroDias"];
    $descripcion = $_POST["txtDescrip"];
    $estado = "Activo";
    $usuario = $_SESSION["usuario"];

    $inserta = $db->prepare("INSERT INTO `clientecondicionespago` (`idCondicion`, `nombCondicion`, `nroDias`, `descripCondicion`, `estadoCondicion`, `creacUsuario`, `creacFch`) VALUES (:idCondicion, :nombCondPago, :nroDias, :descripcion, :estado, :usuario, CURRENT_TIMESTAMP)");
    $inserta->bindParam(':idCondicion',$idCondicion);
    $inserta->bindParam(':nombCondPago',$nombCondPago);
    $inserta->bindParam(':nroDias',$nroDias);
    $inserta->bindParam(':descripcion',$descripcion);
    $inserta->bindParam(':estado',$estado);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function buscarCondPago($db){
    $idCondicion = $_POST["idCondicion"];
    $consulta = $db->prepare("SELECT `idCondicion`, `nombCondicion`, `descripCondicion`, `nroDias`, `estadoCondicion`, `creacUsuario`, `creacFch`, `editaUsuario`, `editaFch` FROM `clientecondicionespago` WHERE idCondicion = :idCondicion");
    $consulta->bindParam(':idCondicion',$idCondicion);
    $consulta->execute();
    return $consulta->fetch();
  }

  function editarCondPago($db){
    $idCondicion = $_POST["txtEditarIdCondicion"];
    $nombCondicion = $_POST["txtEditarNombCondicion"];
    $nroDias = $_POST["txtEditarNroDias"];
    $descripcion = $_POST["txtEditarDescrip"];
    $estado = $_POST["cmbEditarEstadoCondicion"];
    $usuario = $_SESSION["usuario"];
    $auxIdCondicion = $_POST["editaIdCondicion"];

    if ($idCondicion == $auxIdCondicion){
      $actualiza = $db->prepare("UPDATE `clientecondicionespago` SET `nombCondicion` = :nombCondicion, `descripCondicion` = :descripcion, `estadoCondicion` = :estado, `nroDias` = :nroDias, `editaUsuario` = :usuario, editaFch = curdate() WHERE `idCondicion` = :idCondicion");
      $actualiza->bindParam(':idCondicion',$idCondicion);
      $actualiza->bindParam(':nombCondicion',$nombCondicion);
      $actualiza->bindParam(':nroDias',$nroDias);
      $actualiza->bindParam(':descripcion',$descripcion);
      $actualiza->bindParam(':estado',$estado);
      $actualiza->bindParam(':usuario',$usuario);
      $actualiza->execute();
      return $actualiza->rowCount();
    }
  }

  function elimCondPago($db){
    $idCondicion = $_POST["txtElimIdCondicion"];
    $auxIdCondicion = $_POST["elimIdCondicion"];
    if ($idCondicion == $auxIdCondicion){
      $elimina = $db->prepare("DELETE FROM `clientecondicionespago` WHERE `idCondicion` = :idCondicion");
      $elimina->bindParam(':idCondicion',$idCondicion);
      $elimina->execute();
      return $elimina->rowCount();
    }
  }

  function elimSalidaProgram($db){
    $idVacacion = $_POST["txtElimIdVacacion"];
    $fchInicio  = $_POST["txtElimFchIni"];
    $idSalida = $_POST["elimIdSalida"];

    $elimina = $db->prepare("DELETE FROM `trabajcontratovacacusadas` WHERE `idSalida` = :idSalida AND `idVacacion` = :idVacacion AND `fchInicio` = :fchInicio ");
    $elimina->bindParam(':idSalida',$idSalida);
    $elimina->bindParam(':idVacacion',$idVacacion);
    $elimina->bindParam(':fchInicio',$fchInicio);
    $elimina->execute();
    return $elimina->rowCount();
  }

  function crearFactCliente($db){
    $usuario = $_SESSION["usuario"];
    $nroFactura  = $_POST["txtNroFactCliente"];
    $fchFactura  = $_POST["txtFchFactCliente"];
    $mntFactura  = $_POST["txtMntFactCliente"];
    $glosaFactura  = $_POST["txtDescFactCliente"];
    $idCliente  = $_POST["idFactCliente"];

    $inserta = $db->prepare("INSERT INTO `docfacturacliente` (`nroFactura`, `idCliente`, `fchFactura`, `monto`, `glosa`, `creacUsuario`, `creacFch`) VALUES (:nroFactura, :idCliente, :fchFactura, :mntFactura, :glosaFactura, :usuario, now())");
    $inserta->bindParam(':nroFactura',$nroFactura);
    $inserta->bindParam(':idCliente',$idCliente);
    $inserta->bindParam(':fchFactura',$fchFactura);
    $inserta->bindParam(':mntFactura',$mntFactura);
    $inserta->bindParam(':glosaFactura',$glosaFactura);
    $inserta->bindParam(':usuario',$usuario);
    $inserta->execute();
    return $inserta->rowCount();
  }

  function buscarFactCliente2($db){
    $idCliente = $_POST['idCliente'];
    $nroFactura = $_POST['nroFactura'];
    $consulta = $db->prepare("SELECT `nroFactura`, `idCliente`, `fchFactura`, `monto`, `glosa`, `nroOperacion`, `banco`, `creacUsuario`, `creacFch`, `editaUsuario` FROM `docfacturacliente` WHERE `nroFactura` = :nroFactura AND `idCliente` LIKE :idCliente  ");
    $consulta->bindParam(':idCliente',$idCliente);  
    $consulta->bindParam(':nroFactura',$nroFactura);
    $consulta->execute();
    return $consulta->fetch();
  }

  function elimFactCliente($db){
    $nroFactura = $_POST["txtElimNroFactura"];
    $nroFactVerif = $_POST["elimNroFactCliente"];
    $idCliente = $_POST["elimIdFactCliente"];

    if($nroFactura == $nroFactVerif ){
      $elimina = $db->prepare("DELETE FROM `docfacturacliente` WHERE `nroFactura` = :nroFactura AND `idCliente` = :idCliente ");
      $elimina->bindParam(':nroFactura',$nroFactura);
      $elimina->bindParam(':idCliente',$idCliente);
      $elimina->execute();
      return $elimina->rowCount();
    }    
  }

  function editarFactCliente($db){
    $usuario = $_SESSION["usuario"];
    $nroFactura  = $_POST["txtEditaNroFactCliente"];
    $fchFactura  = $_POST["txtEditaFchFactCliente"];
    $mntFactura  = $_POST["txtEditaMntFactCliente"];
    $glosaFactura  = $_POST["txtEditaDescFactCliente"];
    $idCliente  = $_POST["idEditaFactCliente"];

    $actualiza = $db->prepare("UPDATE `docfacturacliente` SET `fchFactura` = :fchFactura, `monto` = :mntFactura, `glosa` = :glosaFactura, `editaUsuario` = :usuario WHERE `nroFactura` = :nroFactura AND `idCliente` = :idCliente");
    $actualiza->bindParam(':fchFactura',$fchFactura);
    $actualiza->bindParam(':mntFactura',$mntFactura);
    $actualiza->bindParam(':glosaFactura',$glosaFactura);
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->bindParam(':nroFactura',$nroFactura);
    $actualiza->bindParam(':idCliente',$idCliente);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function cambiarEstadoFact($db){
    $editUsuario = $_SESSION["usuario"];
    $estado = $_POST["txtEstadoFact"];
    $docCobranza = $_POST["nroDocEstadoFact"];
    $tipoDoc = $_POST["tipoDocEstadoFact"];
    $idPreliquid = $_POST["idPreliquidEstadoFact"];

    if ($estado == "Presentada") $where = " AND estado = 'Emitida' ";
    else if ($estado == "Cancelada") $where = " AND estado = 'Presentada' ";
    else $where = "";

    $actualiza = $db->prepare("UPDATE `doccobranza` SET `estado` = :estado, `editUsuario` = :editUsuario, `editFch` = curdate() WHERE `docCobranza` = :docCobranza AND `tipoDoc` = :tipoDoc AND `idPreliquid` = :idPreliquid $where ");
    $actualiza->bindParam(':estado',$estado);
    $actualiza->bindParam(':docCobranza',$docCobranza);
    $actualiza->bindParam(':tipoDoc',$tipoDoc);
    $actualiza->bindParam(':idPreliquid',$idPreliquid);
    $actualiza->bindParam(':editUsuario',$editUsuario);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function registrarDetraccion($db){
    $editUsuario = $_SESSION["usuario"];
    $tipoDetraccion = $_POST["cmbDetraccTipo"];
    $constancia = $_POST["txtDetraccConstancia"];
    $fchDetraccion = $_POST["txtDetraccFchPago"];
    $monto = $_POST["txtDetraccMonto"];

    $docCobranza = $_POST["nroDocDetraccFact"];
    $tipoDoc = $_POST["tipoDocDetraccFact"];
    $idPreliquid = $_POST["idPreliquidDetraccFact"];

    //echo "tipoDetraccion: $tipoDetraccion, constancia: $constancia, fchDetraccion: $fchDetraccion, monto: $monto, docCobranza: $docCobranza, tipoDoc: $tipoDoc, idPreliquid: $idPreliquid";
    $actualiza = $db->prepare("UPDATE `doccobranza` SET `detraccion` = :monto, `tipoDetraccion` = :tipoDetraccion, `constanciaDetraccion` = :constancia , `fchDetraccion` = :fchDetraccion, `editUsuario` = :editUsuario, `editFch` = CURRENT_DATE() WHERE `docCobranza` = :docCobranza AND `tipoDoc` = :tipoDoc AND `idPreliquid` = :idPreliquid ");
    $actualiza->bindParam(':monto',$monto);
    $actualiza->bindParam(':tipoDetraccion',$tipoDetraccion);
    $actualiza->bindParam(':fchDetraccion',$fchDetraccion);
    $actualiza->bindParam(':editUsuario',$editUsuario);
    $actualiza->bindParam(':docCobranza',$docCobranza);
    $actualiza->bindParam(':tipoDoc',$tipoDoc);
    $actualiza->bindParam(':constancia',$constancia);
    $actualiza->bindParam(':idPreliquid',$idPreliquid);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function buscarDetraccFactura($db){
    $docCobranza = $_POST['docCobranza'];
    $tipoDoc = $_POST['tipoDoc'];
    $consulta = $db->prepare("SELECT `docCobranza`, `tipoDoc`, `mntDoc`, `observacion`, `detraccion`, `tipoDetraccion`, `fchDetraccion`, `constanciaDetraccion`, `idPreliquid` FROM `doccobranza` WHERE `docCobranza` = :docCobranza AND `tipoDoc` = :tipoDoc ");
    $consulta->bindParam(':docCobranza',$docCobranza);  
    $consulta->bindParam(':tipoDoc',$tipoDoc);
    $consulta->execute();
    return $consulta->fetch();
  }

  function editarDetraccion($db){
    //Es similar al registrar
    $editUsuario = $_SESSION["usuario"];
    $tipoDetraccion = $_POST["cmbEditDetraccTipo"];
    $constancia = $_POST["txtEditDetraccConstancia"];
    $fchDetraccion = $_POST["txtEditDetraccFchPago"];
    $monto = $_POST["txtEditDetraccMonto"];

    $docCobranza = $_POST["nroEditDocDetraccFact"];
    $tipoDoc = $_POST["tipoEditDocDetraccFact"];
    $idPreliquid = $_POST["idPreliquidEditDetraccFact"];

    //echo "tipoDetraccion: $tipoDetraccion, constancia: $constancia, fchDetraccion: $fchDetraccion, monto: $monto, docCobranza: $docCobranza, tipoDoc: $tipoDoc, idPreliquid: $idPreliquid";
    $actualiza = $db->prepare("UPDATE `doccobranza` SET `detraccion` = :monto, `tipoDetraccion` = :tipoDetraccion, `constanciaDetraccion` = :constancia , `fchDetraccion` = :fchDetraccion, `editUsuario` = :editUsuario, `editFch` = CURRENT_DATE() WHERE `docCobranza` = :docCobranza AND `tipoDoc` = :tipoDoc AND `idPreliquid` = :idPreliquid ");
    $actualiza->bindParam(':monto',$monto);
    $actualiza->bindParam(':tipoDetraccion',$tipoDetraccion);
    $actualiza->bindParam(':fchDetraccion',$fchDetraccion);
    $actualiza->bindParam(':editUsuario',$editUsuario);
    $actualiza->bindParam(':docCobranza',$docCobranza);
    $actualiza->bindParam(':tipoDoc',$tipoDoc);
    $actualiza->bindParam(':constancia',$constancia);
    $actualiza->bindParam(':idPreliquid',$idPreliquid);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  
  function elimDataDetraccion($db){
    //Es parecido al registrar
    $editUsuario = $_SESSION["usuario"];
    $docCobranza = $_POST["nroElimDocDetraccFact"];
    $tipoDoc = $_POST["tipoElimDocDetraccFact"];
    $idPreliquid = $_POST["idPreliquidElimDetraccFact"];

    //echo "tipoDetraccion: $tipoDetraccion, constancia: $constancia, fchDetraccion: $fchDetraccion, monto: $monto, docCobranza: $docCobranza, tipoDoc: $tipoDoc, idPreliquid: $idPreliquid";
    $actualiza = $db->prepare("UPDATE `doccobranza` SET `tipoDetraccion` = NULL, `detraccion` = NULL, `fchDetraccion` = NULL, `constanciaDetraccion` = NULL, `editUsuario` = :editUsuario, `editFch` = CURRENT_DATE() WHERE `docCobranza` = :docCobranza AND `tipoDoc` = :tipoDoc AND `idPreliquid` = :idPreliquid ");
    $actualiza->bindParam(':docCobranza',$docCobranza);
    $actualiza->bindParam(':tipoDoc',$tipoDoc);
    $actualiza->bindParam(':idPreliquid',$idPreliquid);
    $actualiza->bindParam(':editUsuario',$editUsuario);
    $actualiza->execute();
    return $actualiza->rowCount();
  }

  function buscarPreliquidDocCobranza($db){
    $idPreliquid = $_POST['idPreliquid'];

    $consulta = $db->prepare("SELECT `idPreliquid`, `idOpcion`, `creacFch`, `creacUsuario` FROM `preliquidcobranzaopciones` WHERE `idPreliquid` LIKE :idPreliquid ");
    $consulta->bindParam(':idPreliquid',$idPreliquid);  
    $consulta->execute();
    return $consulta->fetchAll();
  }

  function descargarPreliquid($db){
    $usuario = $_SESSION["usuario"];
    $valores = "";
    $separador = "";
    $ubicacion = 0;
    $instruccion = "INSERT INTO `preliquidcobranzaopciones` (`idPreliquid`, `idOpcion`, `ubicacion`, `creacFch`, `creacUsuario`) VALUES ";

    foreach ($_POST as $campo => $dato) {
      //echo "CAMPO :$campo, VALOR :$dato";
      if(substr($campo,0,3) == "chk" ){
        $valores .= "$separador('$txtDescargarPreliquid', '$campo', $ubicacion , curdate(), '$usuario')";
        if($separador =="") $separador = ", ";
        $ubicacion++;
      } else {
        $$campo = $dato;
      }
    }
    if ($txtDescargarPreliquid == $descIdPreliquid && $txtDescargarPreliquid != '' ){
      $cant = $db->exec( "DELETE FROM `preliquidcobranzaopciones` WHERE `idPreliquid` = '$txtDescargarPreliquid' " );
      $cant = $db->exec( $instruccion . $valores );
      return $cant;
    }
    return 0;
  }

  function buscarDatosFactura($db){
    $docCobranza = $_POST["docCobranza"];
    $tipoDoc = $_POST["tipoDoc"];
    $idPreliquid = $_POST["idPreliquid"];

    $consulta = $db->prepare("SELECT `docCobranza`, `tipoDoc`, `fchDocCobranza`, `fchVencDocCobranza`, `estado`, `mntDoc`, `observacion`, `fchPreliquid`, `detraccion`, `tipoDetraccion`, `fchDetraccion`, `fchRegDetraccion`, `constanciaDetraccion`, `cobIdCliente`, `idPreliquid` FROM `doccobranza` WHERE `docCobranza` = :docCobranza AND `tipoDoc` = :tipoDoc AND `idPreliquid` LIKE :idPreliquid ");
    $consulta->bindParam(':docCobranza',$docCobranza);
    $consulta->bindParam(':tipoDoc',$tipoDoc);
    $consulta->bindParam(':idPreliquid',$idPreliquid);
    $consulta->execute();
    return $consulta->fetch();
  }

  function editaObservFact($db){
    $editUsuario = $_SESSION["usuario"];
    $observacion = $_POST["txtEditaObservFact"];
    $tipoDoc = $_POST["tipoEditaObservFact"];
    $docCobranza = $_POST["nroDocEditaObservFact"];
    $idPreliquid = $_POST["idPreliquidEditaObservFact"];

    //echo "docCobranza :$docCobranza, tipoDoc :$tipoDoc, idPreliquid :$idPreliquid, observacion :$observacion,  editUsuario :$editUsuario";
    $actualiza = $db->prepare("UPDATE `doccobranza` SET `observacion` = :observacion, `editUsuario` = :editUsuario, `editFch` = CURRENT_DATE() WHERE `docCobranza` = :docCobranza AND `tipoDoc` = :tipoDoc AND `idPreliquid` = :idPreliquid ");
    $actualiza->bindParam(':docCobranza',$docCobranza);
    $actualiza->bindParam(':tipoDoc',$tipoDoc);
    $actualiza->bindParam(':idPreliquid',$idPreliquid);
    $actualiza->bindParam(':observacion',$observacion);
    $actualiza->bindParam(':editUsuario',$editUsuario);
    $actualiza->execute();
    return $actualiza->rowCount();
  }


?>
