<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  
function redirectTrab($url,$seconds){
$ss = $seconds * 1000;
$comando = "<script>window.setTimeout('window.location=".chr(34).$url.chr(34).";',".$ss.");</script>";
echo ($comando);
}

function nuevohijo(){
  $dni = $_GET['dni'];
  require 'vistas/nuevoHijo.htm';
}

function eliminahijo(){
  $dni = $_GET['dni'];
  $hijo = $_GET['hijo'];
  require 'vistas/eliminaHijo.htm';
}

function editahijo(){
  $dni = $_GET['dni'];
  $hijo = $_GET['hijo'];
  $fchNac = $_GET['fnac'];
  $sexo = $_GET['sexo'];
  require 'vistas/editaHijo.htm';
}

function nuevaalta() {
  require 'modelos/trabajadoresModelo.php';
  $dni = isset($_POST['dni'])?$_POST['dni']:$_GET['dni'];
  $fchActual = Date("Y-m-d");
  require 'vistas/nuevaAlta.php';  
}

function editaaltabaja(){
  $dni = $_GET['dni'];
  $fchAlta = $_GET['fchalta'];
  $fchBaja = $_GET['fchbaja'];
  $observ = $_GET['observ'];
  require 'vistas/editaAltaBaja.htm';
}

function eliminaalta(){
  if (isset($_POST['dni'])){
    $dni = $_POST['dni'];
    $fchInicio = $_POST['fchInicio'];
    $tipo = $_POST['tipo'];
  } else {
    $dni = $_GET['dni'];
    $fchInicio = $_GET['fchInicio'];
    $tipo = $_GET['tipo'];
  }
  require 'vistas/eliminaAlta.htm';
}

function editavencimtrab(){
  require 'modelos/trabajadoresModelo.php';
  $dni = $_REQUEST['dni'];
  $fchInicio = $_REQUEST['fchinicio'];
  $nombre = $_REQUEST['nombre'];
  $opcion = isset($_POST['opcion'])?$_POST['opcion']:'';
  echo "Opcion $opcion";
  if($opcion == ''){  
    require 'vistas/editaVencimientoTrab.php';
  } else if($opcion == 'No') {
    actualizaUnVencimTrab($db,$dni,$fchInicio,$nombre);
    redirectTrab("vistas/terminarVentana.htm",1);//La funcion esta arriba
  } else if ($opcion == 'Si'){
    actualizaUnVencimTrab($db,$dni,$fchInicio,$nombre);
    redirectTrab("http://localhost/gtcmoy/index.php?controlador=trabajadores&accion=nuevovencimiento&id=$dni",1);
  }
}

function eliminavencimtrab(){
  require 'modelos/trabajadoresModelo.php';
  $dni = $_REQUEST['dni'];
  $fchInicio = $_REQUEST['fchinicio'];
  $tipoAlerta = $_REQUEST['nombre'];
  $opcion = isset($_POST['opcion'])?$_POST['opcion']:'';
  if($opcion == ''){  
    require 'vistas/eliminaVencimTrab.php';
  } else {
    eliminaUnVencimTrab($db,$dni,$fchInicio,$tipoAlerta);
    redirectTrab("vistas/terminarVentana.htm",1);//La funcion esta arriba
  }
}

  function listar2() {  //Es la original
	//Incluye el modelo que corresponde
	require 'modelos/trabajadoresModelo.php';
	$query = ' WHERE 1 ';
  $dni = $tipo = $categ = $contrata = $licencia = $estado = $edad= $categTrabaj  = '';
	if (isset($_POST['txtDni']) and $_POST['txtDni'] <>''){
    $dni = substr($_POST['txtDni'],0,8);
    $query .= " And idTrabajador = '$dni'";
  }                                                                       
  if (isset($_POST['txtTipo']) and $_POST['txtTipo'] <>''){
    $tipo = $_POST['txtTipo'];
    if (strpos ($tipo, "$")==0 and strpos ($tipo, "*") ==0) $query .= " And tipoTrabajador 	= '$tipo'";
  }

  if (isset($_POST['txtCategTrabaj']) and $_POST['txtCategTrabaj'] <>''){
    $categTrabaj = $_POST['txtCategTrabaj'];
    if (strpos ($categTrabaj, "$")==0 and strpos ($categTrabaj, "*") ==0) $query .= " And categTrabajador   like '$categTrabaj'";
  }

  if (isset($_POST['txtCategoria']) and $_POST['txtCategoria'] <>''){
    $categ = $_POST['txtCategoria'];
    if (strpos ($categ, "$")==0 and strpos ($categ, "*") ==0) $query .= " And licenciaCategoria 	like '$categ'";
  }
  if (isset($_POST['txtContrata']) and $_POST['txtContrata'] <>''){
    $contrata = $_POST['txtContrata'];
    if (strpos ($contrata, "$")==0 and strpos ($contrata, "*") ==0 and strpos ($contrata, ";") ==0) $query .= " And modoContratacion 	like '$contrata'";
  }
  
  if (isset($_POST['txtLicencia']) and $_POST['txtLicencia'] <>''){
    $licencia = $_POST['txtLicencia'];
    if (strpos ($licencia, "$")==0 and strpos ($licencia, "*") ==0 and strpos ($licencia, ";") ==0) $query .= " And licenciaNro	like '$licencia'";
  }
  
  if (isset($_POST['txtEstado']) and $_POST['txtEstado'] <>''){
    $estado = $_POST['txtEstado'];
    if (strpos ($estado, "$")==0 and strpos ($estado, "*") ==0 and strpos ($estado, ";") ==0) $query .= " And estadoTrabajador	like '$estado'";
  }
  
  if (isset($_POST['txtEdad']) and $_POST['txtEdad'] <>''){
    $edad = $_POST['txtEdad'];
    if (strpos ($edad, "$")==0 and strpos ($edad, "*") ==0 and strpos ($edad, ";") ==0) $query .= " having edad like '$edad'";
  }        
  
	//Le pide al modelo todos los items
	$items = buscarTrabajadores($db,$query);
	//Pasa a la vista toda la información que se desea representar
	require 'vistas/listarTrabajadores.php';
} 


function listar(){
  require 'vistas/listarTrabajadoresDt.php';
}
                                 
function vertrabajador() {
  $dni = $_GET['dni'];
  require 'modelos/trabajadoresModelo.php'; 
  $conductor = buscarConductor($db,$dni);
  $hijos = buscarConductorHijos($db,$dni);
  $altasbajas = buscarConductorIngresos($db,$dni);
  $telfAsignados = buscarConductorTelefonos($db,$dni);  
  require 'vistas/verTrabajador.php';
}

function nuevotrabajador(){
  require 'modelos/trabajadoresModelo.php';
  require 'modelos/aplicacionModelo.php';
  require 'librerias/config.ini.php';
  $arrNroCuotasFondo = explode(",",$nroCuotasFondo);
  $categorias = buscarCategorias($db);
  $entidadPensiones = buscarEntidadPensiones($db);
  $modosContratacion = buscarModosContratacion($db); 
  $arrBancos = explode(",",$bancosDisponibles);
  require 'vistas/nuevoTrabajador.php';
}

function eliminaTrabajador(){
  require 'vistas/eliminaTrabajador.php';
}

function editatrabajador(){
  if (isset($_POST['txtBuscarTrab']) && $_POST['txtBuscarTrab'] != "" ){
    $auxTrab = $_POST['txtBuscarTrab'];
    $dni = substr($auxTrab, 0,8);
  } else {
    $auxTrab = NULL;
    $dni = $_GET['dni'];
  }
  require 'modelos/trabajadoresModelo.php';
  require 'modelos/aplicacionModelo.php';
  require 'modelos/prendasModelo.php';
  require 'librerias/config.ini.php';
  $modosContratacion = buscarModosContratacion($db);
  $hijos = buscarConductorHijos($db,$dni);
  $prendasAnt = buscarPrendas($db,$dni,20);
  $prendas = buscarDetalleEntregaPrendas($db,NULL,NULL,$dni,NULL,20);
  $categorias = buscarCategorias($db);
  $entidadPensiones = buscarEntidadPensiones($db);
  $trabajador = buscarTrabajador($db,$dni);
  $trabajTipoDoc = buscarTrabajTipoDoc($db, "Activo");
  //$altasbajas = buscarConductorIngresos($db,$dni);
  $telfAsignados = buscarConductorTelefonos($db,$dni);
  $vencimientos = buscarTrabajadorVencimientos($db,$dni);
  $docsvariosadjun = buscarTrabajadorDocsVarios($db,NULL,$dni);
  $arrBancos = explode(",",$bancosDisponibles);
  $verificarPredeterminado = buscarPredeterminado($db,$dni);
  $polizas = buscarPolizas($db);

  require 'vistas/editaTrabajador.php';
}

function cambiarAdjuntoTrab(){
  require 'modelos/trabajadoresModelo.php';
  $dni = $_REQUEST['dni'];
  $fchInicio = $_REQUEST['fchInicio'];
  $nombre = urldecode($_REQUEST['nombre']);
  require 'vistas/cambiarAdjuntoLicenciaTrab.php';
}

function nuevoasigtelefono() {
  require 'modelos/trabajadoresModelo.php';
  $nrosTelefono = buscarTelefonosLibres($db);
  $dni = $_GET['dni'];
  require 'vistas/nuevoAsigTelefono.htm';
}

function editaasigtelefono(){
  $dni = $_GET['dni'];
  $nroTelefono = $_GET['nrotelefono'];
  $fchInicio = $_GET['fchini'];
  $fchFin =  $_GET['fchfin'];
  $observacion =  $_GET['observ'];  
  require 'vistas/editaAsigTelefono.htm';
}

function eliminaasigtelefono(){
  $dni = $_GET['dni'];
  $nroTelefono = $_GET['nrotelefono'];
  $fchInicio = $_GET['fchini'];
  $fchFin =  $_GET['fchfin'];
  $observacion =  $_GET['observ'];
  require 'vistas/eliminaAsigTelefono.htm';
}

function detallepagotrabajador(){
  $dni = $_POST['dni'];
  $fchInicio = $_POST['txtFchInicio'];
  $fchFin = $_POST['txtFchFin'];
  require 'modelos/trabajadoresModelo.php';
  $detalles = buscarDetallesPagoTrabajador($db,$dni,$fchInicio,$fchFin);
  require 'vistas/reporteDetallePagoTrabajador.php';
}

function detallepagoRango(){
  $dni = $_GET['dni'];
  $fchInicio = $_GET['fchInicio'];
  $fchFin = $_GET['fchFin'];
  require 'vistas/detallePagoRango.htm';
}

function boletamesanio(){
  $dni = $_GET['dni'];
  $fchInicio = $_GET['fchInicio'];
  require 'modelos/trabajadoresModelo.php';
  require 'librerias/config.ini.php';
  $trabajador = buscarTrabajador($db,$dni);
  $ultimoIngresoActivo = buscarTrabajadorUltimaAltaActiva($db,$dni);
  $ingresosDelMes = buscarTrabajadorIngresosDelMes($db,$dni,$fchInicio);
  $usoCajaChicaDelMes = buscarUsoCajaChicaDelMes($db,$dni,$fchInicio);
  $basicoPorDia = basicoPorDia($db,$dni);  
  require 'vistas/boletaTrabajador.htm';
}

function prestamo(){
  $dni = $_GET['dni'];
  $opcion = isset($_GET['opcion'])?$_GET['opcion']:"normal";
  require 'modelos/trabajadoresModelo.php';
  require 'librerias/config.ini.php';
  $trabajador = buscarTrabajador($db,$dni);
  $prestamos = buscarTodosLosPrestamosPendientesTrabajador($db,$dni);
  require 'vistas/prestamoTrabajador.htm';
}

function planillaMesAnhio(){
  require 'librerias/config.ini.php';
  $dni = $_GET['dni'];
  if(empty($_POST['txtAnhio'])) $anhio = date('Y'); else $anhio = $_POST['txtAnhio'];
  if(empty($_POST['cmbMes'])) $mes = date('m'); else $mes = $_POST['cmbMes']; 
  require 'modelos/trabajadoresModelo.php';
  $trabajador = buscarTrabajador($db,$dni);
  $trabajadores = buscarTodosLosTrabajadores($db);
  $cuentasContab = buscarTodasLasCuentas($db);
  $infoAFPs = buscarInfoAfp($db);
  $otros = buscarOtros($db,$dni,$mes,$anhio);
  $ocurrencias = buscarOcurrenciasAgrupadas($db,$dni,$mes,$anhio);
  $prestamos = buscarPrestamosAgrupados($db,$dni,$mes,$anhio);
  require 'vistas/planillaMesAnhio.htm';
}

function verificarplanilla(){
  $pagados = 0;
  if (isset($_GET['dni']))
    $dni = $_GET['dni'];
  else {
    if (isset($_POST['cmbTrabajAdmPlan']))
      $aux = $_POST['cmbTrabajAdmPlan'];
    else
      $aux = $_POST['cmbTrabajadorConsulta'];  
    if(strpos($aux,'-'))
      $dni = strtok($aux,'-');
    else   
      $dni = $aux;
  }
  if(isset($_GET['anhio'])){
    $anhio = $_GET['anhio'];
    $mes = $_GET['mes'];
    $quin = $_GET['quin'];
  } else {
    if(empty($_POST['txtAnhio'])) $anhio = $_SESSION['Anhio'] ; else $anhio = $_POST['txtAnhio'];
    if(empty($_POST['cmbMes'])){
     $aux = $_SESSION['Quincena'];
     $mes = strtok($aux, '-');
     $quin = strtok('-');
    } else {
     $aux = $_POST['cmbMes'];
     $mes = strtok($aux, '-');
     $quin = strtok('-');  
    }
  }
  
  require 'modelos/trabajadoresModelo.php';
  require 'modelos/planillasModelo.php';
  require_once 'librerias/config.ini.php';

  $_SESSION['valorMovDiaAux'] =$valorMovDiaAux;
  $_SESSION['valorMovDiaCon'] =$valorMovDiaCon;
  $_SESSION['topeMovilSuped'] =$topeMovilSuped;
    
  if (isset($_GET['elimina'])){
    $tipoItem =  $_GET['tipoItem'];
    $monto =     $_GET['monto'];
    $fchCreacion = $_GET['fchCreacion'];
    $descripcion = $_GET['descripcion'];
    if(isset($_GET['esSalud']))
      modificaEsSalud($db,$dni,'No');
    eliminaPrestamo($db,$dni,$tipoItem,$monto,$fchCreacion,$descripcion);
  }
  
  if (isset($_GET['eliminaEnPrestamo'])){
    $dni = $_GET['dni'];
    $tipoItem =  $_GET['tipoItem'];
    $monto =     $_GET['monto'];
    $fchCreacion = $_GET['fchCreacion'];
    $descripcion = $_GET['descripcion'];
    $nroPagados = buscaPagadosEnPrestamo($db,$dni,$monto,$descripcion,$tipoItem,$fchCreacion);
    if(count($nroPagados) == 0){
      eliminaPrestamoDetalle($db,$dni,$tipoItem,$monto,$fchCreacion,$descripcion);
      eliminaPrestamo($db,$dni,$tipoItem,$monto,$fchCreacion,$descripcion);
    } else {
      $pagados = count($nroPagados);
    }
  }
  
  if (isset($_GET['eliminaOcurrencia'])){
    $fchDespacho = $_GET['fchDespacho'];
    $correlativo = $_GET['correlativo'];
    $tipoOcurrencia =  $_GET['tipoOcurrencia'];
    $descripcion = $_GET['descripcion'];
    $monto =     $_GET['monto'];
    $nroPagados = buscaPagadosEnOcurrencia($db,$fchDespacho,$correlativo,$monto,$tipoOcurrencia,$descripcion);
    if(count($nroPagados) == 0){
      eliminaOcurrenciaDetalle($db,$fchDespacho,$correlativo,$monto,$tipoOcurrencia,$descripcion);
      eliminaOcurrencia($db,$fchDespacho,$correlativo,$monto,$tipoOcurrencia,$descripcion);
    } else {
      $pagados = count($nroPagados);
    }
  }

  if (isset($_POST['btnEnviar']) ){
    require_once 'librerias/conectar.php';
    
    global $servidor, $bd, $usuario, $contrasenia;
    $db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);
    $nroReg = $_POST['nroReg'];
    $nroRegPde = $_POST['nroRegPde'];
    $nroRegPres = $_POST['nroRegPres'];
    $nroRegPresPdo = $_POST['nroRegPpdo'];
    $usuario = $_SESSION["usuario"];
    $anhio = $_POST['txtAnhio'];

    $entidadPension = $_POST['entidadPension'];
    $dsctoAportObl = $_POST['dsctoAportObl'];
    $dsctoComis = $_POST['dsctoComis'];
    $dsctoPrimaSeg = $_POST['dsctoPrimaSeg'];

    $esSalud = $_POST['dsctoEsSalud'];
    $asigFam = $_POST['asigFam'];
    $categTrabajador = $_POST['categTrabajador'];

    $aux = $_POST['cmbMes'];//?????
    $mes = strtok($aux, '-');
    $quin = strtok('-'); 
    $quincena = ($quin == '1')?$anhio.'-'.$mes.'-14':$anhio.'-'.$mes.'-28';
    $idTrabajador = $_POST['cmbTrabajAdmPlan'];
    //echo "Descto EsSalud $esSalud";
    $actualiza = $db->prepare("UPDATE `despachopersonal` SET `pagado` = 'No', `fchPago` = NULL, `Anhio` = '', `Mes` = '', `fchDesmarca` = now() WHERE  `despachopersonal`.`idTrabajador` = :idTrabajador AND `anhio` = :anhio AND `mes` = :mes AND  `pagado` != 'Si'  ");
    $actualiza->bindParam(':anhio',$anhio);
    $actualiza->bindParam(':mes',$mes);
    $actualiza->bindParam(':idTrabajador',$idTrabajador);
    $actualiza->execute();

    $actualiza = $db->prepare("UPDATE `ocurrenciaconsulta` SET `pagado` = 'No', `anhio` = '', `mes` = '', `fchDesmarca` = now()  WHERE  `ocurrenciaconsulta`.`idTrabajador` = :idTrabajador AND `anhio` = :anhio AND `mes` = :mes AND  `pagado` != 'Si'  ");
    $actualiza->bindParam(':anhio',$anhio);
    $actualiza->bindParam(':mes',$mes);
    $actualiza->bindParam(':idTrabajador',$idTrabajador);
    $actualiza->execute();
   
    
    $actualiza = $db->prepare("UPDATE `prestamo` SET `entregado` = 'No', `anhio` = '', `mes` = '', `fchDesmarca` = now()  WHERE  `prestamo`.`idTrabajador` = :idTrabajador AND `anhio` = :anhio AND `mes` = :mes AND  `entregado` != 'Si'  AND `tipoItem` != 'BonoDiario' AND `tipoItem` != 'Vacaciones' ");
    $actualiza->bindParam(':anhio',$anhio);
    $actualiza->bindParam(':mes',$mes);
    $actualiza->bindParam(':idTrabajador',$idTrabajador);
    $actualiza->execute();
    
    $actualiza = $db->prepare("UPDATE `prestamodetalle` SET `pagado` = 'No', `anhio` = '', `mes` = '', `fchQuincena` = null, `fchDesmarca` = now()   WHERE  `idTrabajador` = :idTrabajador AND `anhio` = :anhio AND `mes` = :mes AND  `pagado` != 'Si'  ");
    $actualiza->bindParam(':anhio',$anhio);
    $actualiza->bindParam(':mes',$mes);
    $actualiza->bindParam(':idTrabajador',$idTrabajador);
    $actualiza->execute();
    
    for ($i= 1 ; $i <= $nroReg ; $i++ ) {  
      if(isset($_POST["item".$i])  &&   $_POST["item".$i] == 'ok' ){
        $fchDespacho = $_POST["fchDespacho".$i];
        $correlativo = $_POST["correlativo".$i];
        $actualiza = $db->prepare("UPDATE `despachopersonal` SET `pagado` = 'Md', `anhio` = :anhio, `mes` = :mes , `fchPago` = :fchPago, fchDesmarca = null  WHERE `fchDespacho` = :fchDespacho AND `despachopersonal`.`correlativo` = :correlativo AND `despachopersonal`.`idTrabajador` = :idTrabajador");
        $actualiza->bindParam(':anhio',$anhio);
        $actualiza->bindParam(':mes',$mes);
        $actualiza->bindParam(':correlativo',$correlativo);
        $actualiza->bindParam(':fchDespacho',$fchDespacho);
        $actualiza->bindParam(':idTrabajador',$idTrabajador);
        $actualiza->bindParam(':fchPago',$quincena);
        $actualiza->execute();     
      }             
    }   
    
    for ($i= 1 ; $i <= $nroRegPde ; $i++ ) {
      if(isset($_POST["itemPde".$i])  &&  $_POST["itemPde".$i] == 'ok' ){
        
        $fchDespacho = $_POST["fchDespachoPde".$i];
        $correlativo = $_POST["correlativoPde".$i];
        $nroCuota = $_POST["nroCuotaPde".$i];
        $montoTotal = $_POST["mtoTotalPde".$i];
        $tipoOcurrencia = $_POST["tipoOcurrenciaPde".$i];
        $ddescripcion =  $_POST["descripcionPde".$i];
        $actualiza = $db->prepare("UPDATE `ocurrenciaconsulta` SET `anhio` = :anhio, `mes` = :mes, `pagado` = 'Md', `fchQuincena` = :fchQuincena,  fchDesmarca = null  WHERE `fchDespacho` = :fchDespacho AND `correlativo` = :correlativo AND `nroCuota` = :nroCuota AND `montoTotal` = :mtoTotal AND `tipoOcurrencia` = :tipoOcurrencia AND `descripcion` = :descripcion AND `idTrabajador` = :idTrabajador");
        $actualiza->bindParam(':anhio',$anhio);
        $actualiza->bindParam(':mes',$mes);
        $actualiza->bindParam(':correlativo',$correlativo);
        $actualiza->bindParam(':fchDespacho',$fchDespacho);
        $actualiza->bindParam(':fchQuincena',$quincena);
        $actualiza->bindParam(':nroCuota',$nroCuota);
        $actualiza->bindParam(':mtoTotal',$montoTotal);
        $actualiza->bindParam(':tipoOcurrencia',$tipoOcurrencia);        
        $actualiza->bindParam(':idTrabajador',$idTrabajador);
        $actualiza->bindParam(':descripcion',$ddescripcion);
        
        $actualiza->execute();     
      }             
    }
    //echo "Nro de:".$nroRegPresPdo;
    for ($i= 1 ; $i <= $nroRegPresPdo ; $i++ ) {
      if(isset($_POST["itemPpdo".$i])  && $_POST["itemPpdo".$i] == 'ok' ){
        $monto = $_POST["pPdoMonto".$i];
        $fchCreacion = $_POST["pPdoFchCreacion".$i];
        $tipoItem = $_POST["pPdoTipoItem".$i];
        $actualiza = $db->prepare("UPDATE `prestamo` SET `anhio` = :anhio, `mes` = :mes, `entregado` = 'Md', `fchQuincena` = '$quincena' , fchDesmarca = null  ,`usuarioUltimoCambio` = :usuario, `fchUltimoCambio` = CURDATE() WHERE `monto` = :monto AND  `idTrabajador` = :idTrabajador AND `fchCreacion` = :fchCreacion AND `tipoItem` = :tipoItem ");
        $actualiza->bindParam(':anhio',$anhio);
        $actualiza->bindParam(':mes',$mes);
        $actualiza->bindParam(':monto',$monto);
        $actualiza->bindParam(':fchCreacion',$fchCreacion);
        $actualiza->bindParam(':idTrabajador',$idTrabajador);
        $actualiza->bindParam(':tipoItem',$tipoItem);
        $actualiza->bindParam(':usuario',$usuario);
        $actualiza->execute();     
      }             
    }
            
    
    for ($i= 1 ; $i <= $nroRegPres ; $i++ ) {
      if( isset($_POST["itemPres".$i]) && $_POST["itemPres".$i] == 'ok' ){        
        $monto = $_POST["montoPres".$i];
        $fchCreacion = $_POST["fchCreacionPres".$i];
        $nroCuota = $_POST["nroCuotaPres".$i];
        $tipoItem = $_POST["tipoItem".$i];
        $descripItem = $_POST["descripItem".$i];
        $actualiza = $db->prepare("UPDATE `prestamodetalle` SET `anhio` = :anhio, `mes` = :mes, `pagado` = 'Md', `fchQuincena` = :fchQuincena, fchDesmarca = null,  `usuarioUltimoCambio` = :usuario, `fchUltimoCambio` = CURDATE() WHERE `monto` = :monto AND `nroCuota` = :nroCuota AND `tipoItem` = :tipoItem AND `idTrabajador` = :idTrabajador AND `fchCreacion` = :fchCreacion AND descripcion = :descripItem ");
        $actualiza->bindParam(':anhio',$anhio);
        $actualiza->bindParam(':mes',$mes);
        $actualiza->bindParam(':monto',$monto);
        $actualiza->bindParam(':fchCreacion',$fchCreacion);
        $actualiza->bindParam(':nroCuota',$nroCuota);
        $actualiza->bindParam(':tipoItem',$tipoItem);
        $actualiza->bindParam(':descripItem',$descripItem);
        $actualiza->bindParam(':fchQuincena',$quincena);
        $actualiza->bindParam(':idTrabajador',$idTrabajador);
        $actualiza->bindParam(':usuario',$usuario);
        $actualiza->execute();     
      }             
    }    
      
  } 
  require 'librerias/config.ini.php';


  ///Para el calculo de la planilla contab
  //Guardar el dato de Pension. Se usa la tabla prestamo
  $infoAFPs = buscarInfoAfp($db); 
  foreach($infoAFPs as $afp) {       //se esta haciendo innecesario
    $nombre = $afp['nombre'];
    $comFlujo["$nombre"] = $afp['comisionFlujo'];
    $comMixta["$nombre"] = $afp['comisionMixta'];
    $primaSeg["$nombre"] = $afp['primaSeg'];
    $porcOblig["$nombre"] = $afp['porcentObligat'];
  }

  $rpta = verCasosEspeciales($db,$dni,$anhio,$mes,$quin);

  calcularQuincenaContab($db,$dni,$anhio, $mes, $quin, $comFlujo, $comMixta, $primaSeg, $porcOblig);
  ///
  
  $trabajador = buscarTrabajador($db,$dni);
  $bonoDiario = buscarBonoDiarioTrabajador($db,$dni,$mes,$anhio,$quin);
  $trabajadores  = buscarTodosLosTrabActivos($db);
  $cuentasContab = buscarTodasLasCuentas($db);
  $despachos =  buscarDespachosTrabajador($db,$dni,$mes,$anhio,$quin);
  $pendientes = buscarPagosPendientesTrabajador($db,$dni,$mes,$anhio,$quin);
  $prestamos =  buscarPrestamosPendientesTrabajador($db,$dni,$mes,$anhio,$quin);
  $prestamosPedidos = buscarPrestamosPedidosTrabajador($db,$dni,$mes,$anhio,$quin,$dataItemsTrabajador);//ahora incluye bonos y reembolsos
  $lineaContab = resumenDsctosAporteEtcContab($db,$anhio,$mes,$quin,$dni);    //verif en quincenadetallecontab
  $lineaHrasExtra = buscarHorasExtraTrabajador($db, $dni, $mes, $anhio, $quin);
  /*echo "LINEA HRAS EXTRA";
  echo "<pre>";
  print_r($lineaHrasExtra);
  echo "</pre>";
  */
  $infoAFPs = buscarInfoAfp($db);
  $otros = buscarOtros($db,$dni,$mes,$anhio,$quin);
  require 'vistas/datosPlanilla.htm';  
}

function consultasplanilla(){
  if (isset($_POST['cmbTrabajador']) AND $_POST['cmbTrabajador'] != '' )
    $aux = $_POST['cmbTrabajador'];  
  else if (isset($_GET['dni']))
    $aux = $_GET['dni'];  
  else if (isset($_POST['cmbTrabajadorConsulta']) && $_POST['cmbTrabajadorConsulta'] != '' )
    $aux = $_POST['cmbTrabajadorConsulta'];
  else
    $aux = $_POST['cmbTrabajadorConsultaBarr'];
  
  if(strpos($aux,'-'))
    $dni = strtok($aux,'-');
  else   
    $dni = $aux;
  
  if(isset($_POST['txtAnhio']) AND $_POST['txtAnhio'] != '' )
    $anhio = $_POST['txtAnhio'];
  else if (isset($_GET['anhio']) AND $_GET['anhio'] != '' )
    $anhio = $_GET['anhio'];
  else
    $anhio = $_SESSION['Anhio'] ; 
  
  if(isset($_POST['cmbMes'])){
   $aux = $_POST['cmbMes'];
   $mes = strtok($aux, '-');
   $quin = strtok('-');
  } else if (isset($_GET['mes'])) {
   $mes = $_GET['mes'];
   $quin = $_GET['quin'];
  } else {
   $aux = $_SESSION['Quincena'];
   $mes = strtok($aux, '-');
   $quin = strtok('-');
  }

  require 'librerias/config.ini.php';
  
  require 'modelos/trabajadoresModelo.php';
  $trabajador = buscarTrabajador($db,$dni);
  $trabajadores = buscarTodosLosTrabActivos($db);
  $cuentasContab = buscarTodasLasCuentas($db);
  $despachos = buscarDespachosTrabajadorPagados($db,$dni,$mes,$anhio,$quin);
  $pendientes = buscarPagosPendientesTrabajadorPagados($db,$dni,$mes,$anhio,$quin);
  $prestamos = buscarPrestamosPendientesTrabajadorPagados($db,$dni,$mes,$anhio,$quin);
  $ajustes = buscarAjustesPendientesTrabajadorPagados($db,$dni,$mes,$anhio,$quin);
  $prestamosPedidos = buscarPrestamosPedidosTrabajadorPagados($db,$dni,$mes,$anhio,$quin,$dataItemsTrabajador );//Incluye reembolso y bono
  $lineaContab = resumenDsctosAporteEtcContab($db,$anhio,$mes,$quin,$dni);    //verif en quincenadetallecontab
  $lineaHrasExtra = buscarHorasExtraTrabajadorPagados($db, $dni, $mes, $anhio, $quin);
  require 'vistas/consultasPlanilla.htm';
}

function listardetalletrabajotrabajador(){
  require 'modelos/trabajadoresModelo.php';
  require 'modelos/despachosModelo.php';
  require 'librerias/config.ini.php';
  if (isset($_POST['txtFchIniBarr']) && $_POST['txtFchIniBarr'] != '')
    $fchIni =  $_POST['txtFchIniBarr'];
  else if (isset($_POST['txtFchIni']))
    $fchIni = $_POST['txtFchIni'];
  else
    $fchIni = NULL;

  if (isset($_POST['txtFchFinBarr']) && $_POST['txtFchFinBarr'] != '')
    $fchFin =  $_POST['txtFchFinBarr'];
  else if (isset($_POST['txtFchFin']))
    $fchFin = $_POST['txtFchFin'];
  else
    $fchFin = NULL;
  
  $correl = isset($_POST['txtCorrel'])?$_POST['txtCorrel']:NULL;
  if(isset($_POST['txtTrabajador'])){
    $aux = $_POST['txtTrabajador'];
    $trabajador = strtok($aux,'-');
  } else 
    $trabajador = NULL;  
  $cliente = isset($_POST['txtCliente'])?$_POST['txtCliente']:NULL;
  $idCliente = strtok($cliente,'-');
  $placa  = isset($_POST['cmbPlaca'])?$_POST['cmbPlaca']:"";
  $tipoRol = isset($_POST['txtTipoRol'])?$_POST['txtTipoRol']:"";
  $orden  = isset($_GET['orden'])?$_GET['orden']:"";
  $pagado = isset($_POST['txtPagado'])?$_POST['txtPagado']:"";
  $cuenta = isset($_POST['txtCuenta'])?$_POST['txtCuenta']:"";
  $guiaPorte = isset($_POST['txtGuiaPorte'])?$_POST['txtGuiaPorte']:"";
  $observ = isset($_POST['txtObserv'])?$_POST['txtObserv']:NULL; 
  $monto = isset($_POST['txtMonto'])?$_POST['txtMonto']:"";
  $valAdic = isset($_POST['txtAdic'])?$_POST['txtAdic']:"";
  $usaReten = (isset($_POST['txtUsaReten']) && ($_POST['txtUsaReten'] == 'Si' || $_POST['txtUsaReten'] == 'si'))?$_POST['txtUsaReten']:NULL;
  $limite = isset($_POST['txtLimite'])?$_POST['txtLimite']:'500';
    
  $items = buscarDetalleTrabajoTrabajador($db,$fchIni,$fchFin,$correl,$trabajador,$placa,$idCliente,$tipoRol,$orden,$pagado,$cuenta,$guiaPorte,$monto,$valAdic,$usaReten,$observ,$limite);
  require 'vistas/listarDetalleTrabajoTrabajador.htm';
}

function procesosLoteTrabajador(){
  require 'modelos/trabajadoresModelo.php';
 /* if(empty($_POST['txtAnhio'])) $anhio = $_SESSION['Anhio'] ; else $anhio = $_POST['txtAnhio'];
  if(empty($_POST['cmbMes'])){
   $aux = $_SESSION['Quincena'];
   $mes = strtok($aux, '-');
   $quin = strtok('-');
  } else {
   $aux = $_POST['cmbMes'];
   $mes = strtok($aux, '-');
   $quin = strtok('-');  
  }   */
 //$trabajador = buscarTrabajador($db,$dni);
  $trabajadores = buscarTodosLosTrabActivosMail($db);
  $nroTrab = count($trabajadores);
  require 'vistas/procesosLoteTrabajador.htm';
}

function nuevovencimiento() {
  $dni = $_GET['id'];
  require 'modelos/vehiculosModelo.php'; // la funcion se declaró allí
  $tipoMantenimiento = buscarTipoAlerta($db,'Persona');
  require 'vistas/nuevoVencimiento.htm';
}


function repoHistoricodescuentosvarios(){
  $aux = (isset($_POST['cmbTrabajadorHistBarr']) && $_POST['cmbTrabajadorHistBarr'] != '' )?$_POST['cmbTrabajadorHistBarr']:$_POST['cmbTrabajador'];
  $dni = strtok($aux,'-');
  $descripcion = (isset($_POST['txtDescripcion']))?$_POST['txtDescripcion']:null;                                               
  $tipoItem = (isset($_POST['txtTipoItem']))?$_POST['txtTipoItem']:null;                                               
  $monto = (isset($_POST['txtMonto']))?$_POST['txtMonto']:null;
  $pagado = (isset($_POST['cmbPagado']))?$_POST['cmbPagado']:null;
  $fchCreacion = isset($_POST['txtFchCreacion'])?$_POST['txtFchCreacion']:null;
  $fchQuincena = isset($_POST['txtFchQuincena'])?$_POST['txtFchQuincena']:null;
  
  $limite = isset($_POST['txtLimite'])?$_POST['txtLimite']:'100';
                                                            
  //echo "DNI  $dni";
  require 'modelos/trabajadoresModelo.php';
  $items = buscarHistoricoDescuentosVarios($db,$dni,$tipoItem,$fchCreacion,$monto,$pagado,$descripcion,$fchQuincena,$limite);
  require 'vistas/repoHistoricoDescuentosVarios.php';

}


function repoHistoricoocurrencias(){
  $aux = $_POST['cmbTrabajador2'];
  $dni = strtok($aux,'-');
  $descripcion = (isset($_POST['txtDescripcion']))?$_POST['txtDescripcion']:null;                                               
  $tipoOcurrencia = (isset($_POST['txtTipoOcurrencia']))?$_POST['txtTipoOcurrencia']:null;                                               
  $monto = (isset($_POST['txtMonto']))?$_POST['txtMonto']:null;
  $pagado = (isset($_POST['cmbPagado']))?$_POST['cmbPagado']:null;
  $fchCreacion = isset($_POST['txtFchCreacion'])?$_POST['txtFchCreacion']:null;
  $fchQuincena = isset($_POST['txtFchQuincena'])?$_POST['txtFchQuincena']:null;
  $fchDespacho = isset($_POST['txtFchDespacho'])?$_POST['txtFchDespacho']:null;
  
  $limite = isset($_POST['txtLimite'])?$_POST['txtLimite']:'100';
                                                            
  //echo "DNI  $dni";
  require 'modelos/trabajadoresModelo.php';
  $items = buscarHistoricoocurrencias($db,$dni,$tipoOcurrencia,$fchCreacion,$monto,$pagado,$descripcion,$fchQuincena,$fchDespacho,$limite);
  require 'vistas/repoHistoricoOcurrencias.php';

}


function verresumenquincena() {
  $anhio = (isset($_POST['txtAnhioBarr']) && $_POST['txtAnhioBarr'] != '' )?$_POST['txtAnhioBarr']:$_POST['txtAnhio'];
  if (isset($_POST['cmbMes'])){
    $aux = $_POST['cmbMes'];
    $mes = strtok($aux, '-');
    $quin = strtok('-'); 
  } else {
    $aux = $_POST['cmbMesResumen'];
    if(empty($_POST['cmbMesResumen'])){
     $mes = date('m');
     $quin = '1';
    } else {
     $aux = $_POST['cmbMesResumen'];
     $mes = strtok($aux, '-');
     $quin = strtok('-');  
    }
  }
  require 'modelos/trabajadoresModelo.php';
  //$lineaTrabajadoresActivos = buscarTrabajadores($db,' WHERE 1 ');//verifica en trabajador
  $lineaQuincena = resumenQuincenaDetalle($db,$anhio,$mes,$quin,null); //verif en quincenadetalle
  $lineaDespachos  = resumenDespachos($db,$anhio,$mes,$quin);     //verif en despachopersonal
  $lineaOcurrencia = resumenOcurrencia($db,$anhio,$mes,$quin);    //verif en ocurrenciaconsulta
  $lineaVarios = resumenVarios($db,$anhio,$mes,$quin);            //verif en prestamo
  $lineaPagoPrestamos = resumenPagoPrestamos($db,$anhio,$mes,$quin);//verif en prestamodetalle
  require 'vistas/verResumenQuincena.htm';
}

function vernuevoresumenquincena(){
  $anhio = isset($_POST['txtNuevoAnhio'])?$_POST['txtNuevoAnhio']:DATE("Y");
  if (isset($_POST['cmbNuevoMesResumen'])){
    $aux = $_POST['cmbNuevoMesResumen'];
    $mes = strtok($aux, '-');
    $quin = strtok('-'); 
  }

  if (isset($_GET['id']) && $_GET['id'] == 'u'){
    $anhio = $_POST['txtAnhio'];
    $aux = $_POST['cmbMes'];
    $mes = strtok($aux, '-');
    $quin = strtok('-');
  }
  
  require 'modelos/trabajadoresModelo.php';
  //$lineaTrabajadoresActivos = buscarTrabajadores($db,' WHERE 1 ');//verifica en trabajador
  $lineaQuincena = resumenQuincenaDetalle($db,$anhio,$mes,$quin,null); //verif en quincenadetalle
  $lineaDespachos  = resumenDespachos($db,$anhio,$mes,$quin);     //verif en despachopersonal
  $lineaOcurrencia = resumenOcurrencia($db,$anhio,$mes,$quin);    //verif en ocurrenciaconsulta
  $lineaVarios = resumenVarios($db,$anhio,$mes,$quin);            //verif en prestamo
  $lineaPagoPrestamos = resumenPagoPrestamos($db,$anhio,$mes,$quin);//verif en prestamodetalle
  require 'vistas/verNuevoResumenQuincena.htm';
}

function verresumenquincenaborrador(){
  $anhio = $_SESSION['Anhio'];
  $aux = $_SESSION['Quincena'];
  $mes = strtok($aux, '-');
  $quin = strtok('-');
  $quincena = ($quin == '1')?" PRIMERA QUINCENA ":" SEGUNDA QUINCENA "; 
  require 'modelos/trabajadoresModelo.php';
  require 'modelos/despachosModelo.php';
  if(isset($_GET['editarValor'])){
    $fchDespacho = $_GET['fch'];
    $corr = $_GET['corr'];
    $idTrab = $_GET['dni'];
    //$tipo = $_GET['tipo'];
    $nvoValor = $_GET['nvoValor'];
    editaValor($db,$fchDespacho,$corr,$idTrab,$nvoValor); 
    logAccion($db,"Se modificó en despacho $fchDespacho-$corr el valorRol a $nvoValor",$idTrab,null);
  }
  $lineaTrabajadoresActivos = buscarTrabajadores($db,' WHERE 1 ');    //verifica en trabajador
  $lineaDespachos  = resumenDespachosMarcados($db,$anhio,$mes,$quin); //verif en despachopersonal
  $lineaOcurrencia = resumenOcurrenciaMarcados($db,$anhio,$mes,$quin);//verif en ocurrenciaconsulta
  $lineaVarios = resumenVariosMarcados($db,$anhio,$mes,$quin);              //verif en prestamo
  $lineaPagoPrestamos = resumenPagoPrestamosMarcados($db,$anhio,$mes,$quin);//verif en prestamodetalle
  $lineaContab = resumenDsctosAporteEtcContab($db,$anhio,$mes,$quin);    //verif en quincenadetallecontab

  require 'vistas/adicionales/resumenQuincenaBorradorProcesoDeDatos.php';
  require 'vistas/verResumenQuincenaBorrador.htm';
}


function automarcadoquincena(){
  require 'modelos/trabajadoresModelo.php';
  require 'modelos/planillasModelo.php';
  require_once 'librerias/config.ini.php';  
  $usuario = $_SESSION["usuario"];
  $_SESSION['valorMovDiaAux'] =$valorMovDiaAux;
  $_SESSION['valorMovDiaCon'] =$valorMovDiaCon;
  $_SESSION['topeMovilSuped'] =$topeMovilSuped;

  $fchIni = isset($_POST['txtAutoFchIni'])?$_POST['txtAutoFchIni']:NULL;
  $fchFin = isset($_POST['txtAutoFchFin'])?$_POST['txtAutoFchFin']:NULL;
  $nombreProceso = isset($_POST['txtNombreProceso'])?$_POST['txtNombreProceso']:NULL;
  if (isset($_POST['btnEnviar'])){
    $aux = $_SESSION['Quincena'];
    $mes = strtok($aux, '-');
    $quin = strtok('-');
    /* 201012 Los administrativos pasan a ser igual que los demás en cuanto a pago quincenal
    if($quin == '1') cambiarEstadoAdministrativos($db,'Activo','Latente');
    else cambiarEstadoAdministrativos($db,'Latente','Activo');
    */

    $tarea = autoMarcadoDespachoPersonal($db,$fchIni,$fchFin,$nombreProceso,$usuario); 
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'HraExtra');
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'Prestamo');    
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'Adelanto');    
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'Bono');    
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'BonoDiario');    
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'Movilidad');    
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'Reembolso');    
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'Compra');    
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'DevolGarantia');    
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'DomFeriado');    
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'DsctoTrabaj');
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'DsctoUnif');//Añadido
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'FondoGarantia');    
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'Ajuste');    
    $tarea = autoMarcadoOcurrenciaTipoOcurrencia($db,$fchIni,$fchFin,$nombreProceso,'Caja Chica'); 
    $tarea = autoMarcadoOcurrenciaTipoOcurrencia($db,$fchIni,$fchFin,$nombreProceso,'Descuento'); 
    $tarea = autoMarcadoOcurrenciaTipoOcurrencia($db,$fchIni,$fchFin,$nombreProceso,'Devolucion'); 
    $tarea = autoMarcadoOcurrenciaTipoOcurrencia($db,$fchIni,$fchFin,$nombreProceso,'Gastos Varios'); 
    $tarea = autoMarcadoOcurrenciaTipoOcurrencia($db,$fchIni,$fchFin,$nombreProceso,'Movilidad'); 
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'DescMedico');
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'LicGoce');
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'ServAdicional');    
    $tarea = autoMarcadoPrestamoTipoItem($db,$fchIni,$fchFin,$nombreProceso,'Inasistencia');    
    $_SESSION['esSalud'] = $esSalud;
    $tarea = autoMarcadoEsSaludyPensionPartes($db,$nombreProceso,'EsSalud'); 
    $tarea = autoMarcadoEsSaludyPensionPartes($db,$nombreProceso,'Pension'); 
    $tarea = autoMarcadoEsSaludyPensionPartes($db,$nombreProceso,'AsigFam'); 


    autoMarcadoGenerarQuincena($db,$nombreProceso,$fchIni,$fchFin);     
    ?>
    <html>
      <head>
      <meta http-equiv="content-type" content="text/html; charset=windows-1250">
      <meta name="generator" content="PSPad editor, www.pspad.com">
      <meta HTTP-EQUIV="REFRESH" content="5; url=index.php?controlador=trabajadores&accion=verresumenquincenaborrador">
      <title></title>
      </head>
      <body>
        Automarcado ha sido ejecutado. Espere por favor
      </body>
      </html>
    <?php  
  
  } else {  
  require 'vistas/automarcadoEnviar.htm';
  }
}


function automarcadoquincenaconfirmar(){
  set_time_limit(0);

  require_once 'librerias/varios.php';
  require 'modelos/trabajadoresModelo.php';
  require 'modelos/aplicacionModelo.php';
  require 'modelos/gerencialModelo.php';
  require 'modelos/plameModelo.php';
  require_once 'librerias/config.ini.php';
  require_once 'librerias/variosPHPExcel.php';

  require 'librerias/PHPMailer6_0_5/src/Exception.php';
  require 'librerias/PHPMailer6_0_5/src/PHPMailer.php';
  require 'librerias/PHPMailer6_0_5/src/SMTP.php';
   
  $usuario = $_SESSION["usuario"];
  $fchIni = isset($_POST['txtAutoFchIni'])?$_POST['txtAutoFchIni']:NULL;
  $fchFin = isset($_POST['txtAutoFchFin'])?$_POST['txtAutoFchFin']:NULL;
  $nombreProceso = isset($_POST['txtNombreProceso'])?$_POST['txtNombreProceso']:NULL;
  if (isset($_POST['btnEnviar'])){
    if(isset($_POST['cerrarQuincena']) && $_POST['cerrarQuincena'] == 'Si'){
      $fchQuincena = "";
      $responsablesAlertas = buscarResponsablesAlertasGerencial($db);
      generarQuincenaDetalle($db,$nombreProceso,$fchQuincena);
      generarIndicesPersonalQuincena($db,$fchQuincena);     
      cambiarEstadoQuincena($db,$nombreProceso,'Cerrado');
      $quincenas = buscar6Quincenas($db,$fchQuincena);
      sort($quincenas);
      $dataParaAnalisis = buscarDataIndicadoresPersonal($db,$fchQuincena);
      $arrNombres = $arrTotal = array();
      foreach ($dataParaAnalisis as $key => $value){
        $idTrabajador = $value['idTrabajador'];
        $arrNombres[$idTrabajador] = utf8_encode($value['nombCompleto']);
        $quincena = $value['quincena'];
        $arrTotal[$idTrabajador]['PORCENTAJE DE ASISTENCIA QUINCENAL'][$quincena] = $value['porcAsistencia'];
        $arrTotal[$idTrabajador]['INDICADOR DE ESFUERZO LABORAL'][$quincena] = $value['indEsfLaboral'];
        $arrTotal[$idTrabajador]['INDICADOR HORAS LABORADAS'][$quincena] = $value['indHrasLaboradas'];
        $arrTotal[$idTrabajador]['INDICADOR HORAS EXTRAS'][$quincena] = $value['indHrasExtra'];
        $arrTotal[$idTrabajador]['INDICADOR DE ANTIGUEDAD DE PERSONAL'][$quincena] = $value['indAntiguedad'];
        $arrTotal[$idTrabajador]['FACTOR HISTORICO DE DESCUENTOS'][$quincena] = $value['factHistDscto'];
        $arrTotal[$idTrabajador]['FACTOR DE ENDEUDAMIENTO'][$quincena] = $value['factEndeudamiento'];
        $arrTotal[$idTrabajador]['PORCENTAJE DE COMPRA'][$quincena] = $value['porcCompra'];
        $arrTotal[$idTrabajador]['DIAS TOTALES'][$quincena] = $value['diasTotales'];
        $arrTotal[$idTrabajador]['DIAS NO HABILES'][$quincena] = $value['diasNoHabiles'];
        $arrTotal[$idTrabajador]['DIAS ASISTIDOS'][$quincena] = $value['diasAsistidos'];
        $arrTotal[$idTrabajador]['VIAJES REALIZADOS'][$quincena] = $value['viajesRealizados'];
        $arrTotal[$idTrabajador]['HORAS LABORADAS'][$quincena] = $value['hrasLaboradas'];
        $arrTotal[$idTrabajador]['MONTO ADEUDADO'][$quincena] = $value['montoAdeudado'];
        $arrTotal[$idTrabajador]['FCH ULTIMO PAGO'][$quincena] = $value['fchUltimoPago'];
        $arrTotal[$idTrabajador]['MONTO PAGADO'][$quincena] = $value['montoPagado'];
        $arrTotal[$idTrabajador]['SALDO TOTAL DEUDA'][$quincena] = $value['saldoTotalDeuda'];
      }
     //ob_clean(); 
      require 'vistas/armarReporteIndicadoresPersonal.php';
      // get the content
      @ob_start();
      $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
      $writer->save("php://output");
      $data = @ob_get_contents();
      @ob_end_clean(); 

      //ob_end_clean();
      $nombreNuevo = "indicadoresPersonal";
      
      $message = "Se le envía el Reporte de Indices de Personal<br>
      <b>Favor, ver adjunto</b>"; 

      $mail = new PHPMailer();
      $mail->IsSMTP();
      $mail->SMTPAuth = true;

      $mail->Host = "localhost";
      $mail->Username = "alertas@inversionesmoy.com.pe";
      $mail->Password = "Alertas20297421035";

      $mail->From = "alertas@inversionesmoy.com.pe";
      $mail->FromName = "Alertas";

      $mail->Subject = "Envío Reporte de Indicadores de Personal";
      $mail->AddAddress('lescobedo63@yahoo.com',"Luis Escobedo");
      /*$mail->AddAddress('mvelasquez@inversionesmoy.com.pe',"Moises Velasquez");
      $mail->AddAddress('asistente02@inversionesmoy.com.pe',"Guianella Cuba");*/

      foreach($responsablesAlertas AS $responsable){
        $email = $responsable['email'];
        $nombre = $responsable['nombreCompleto'];
        $mail->AddAddress($email,$nombre);
      }

      $mail->AddStringAttachment($data, $nombreNuevo.'.xlsx');
      $mail->MsgHTML($message);

      if(!$mail->Send()) {
        echo "Error, no se realizó el envío: " . $mail->ErrorInfo;
      } else {
        echo "Mensaje Enviado!";
      }
    }
  /*  if(isset($_POST['chkDespacho']) && $_POST['chkDespacho'] == 'ON'){
      $tarea = autoMarcadoConfirmarDespachoPersonal($db,$nombreProceso,$usuario); 
    }
    if(isset($_POST['chkPrestamo']) && $_POST['chkPrestamo'] == 'ON'){
      $tarea = autoMarcadoConfirmarPrestamo($db,$nombreProceso,$usuario); 
    }
    if(isset($_POST['chkOcurrencia']) && $_POST['chkOcurrencia'] == 'ON'){
      $tarea = autoMarcadoConfirmarOcurrencia($db,$nombreProceso,$usuario); 
    }
*/

    $tarea = autoMarcadoConfirmarDespachoPersonal($db,$nombreProceso,$usuario); 
    $tarea = autoMarcadoConfirmarPrestamo($db,$nombreProceso,$usuario); 
    $tarea = autoMarcadoConfirmarOcurrencia($db,$nombreProceso,$usuario);




    // echo  "Que quincena es".substr($fchQuincena,8,2);

    if (substr($fchQuincena,8,2) == '28'){
      completarAdelQ($db, substr($fchQuincena,0,8) );
    }

    
    $url = "index.php?controlador=aplicacion&accion=verprincipal";
    require 'vistas/redireccionar.php';
  
  } else {
    require 'vistas/automarcadoConfirmar.htm';
  }
}

function historicoTrabajador(){
  if (isset($_POST['cmbTrabajador3']) AND $_POST['cmbTrabajador3'] != '' )
    $aux = $_POST['cmbTrabajador3'];  
  else
    $aux = $_POST['cmbTrabajador3ConsultaBarr'];

  $dni = strtok($aux,'-');
  $descripcion = (isset($_POST['txtDescripcion']))?$_POST['txtDescripcion']:null;                                               
  $tipoOcurrencia = (isset($_POST['txtTipoOcurrencia']))?$_POST['txtTipoOcurrencia']:null;                                               
  $monto = (isset($_POST['txtMonto']))?$_POST['txtMonto']:null;
  $pagado = (isset($_POST['cmbPagado']))?$_POST['cmbPagado']:null;
  $fchCreacion = isset($_POST['txtFchCreacion'])?$_POST['txtFchCreacion']:null;
  $fchQuincena = isset($_POST['txtFchQuincena'])?$_POST['txtFchQuincena']:null;
  $fchDespacho = isset($_POST['txtFchDespacho'])?$_POST['txtFchDespacho']:null;
  $tipoItem = isset($_POST['txtTipoItem'])?$_POST['txtTipoItem']:null;
  $limite = isset($_POST['txtLimite'])?$_POST['txtLimite']:null;
  require 'modelos/trabajadoresModelo.php';
  $items = buscarHistoricoocurrencias($db,$dni,$tipoOcurrencia,$fchCreacion,$monto,$pagado,$descripcion,$fchQuincena,$fchDespacho,$limite);
  $itemsDsctos = buscarHistoricoDescuentosVarios($db,$dni,$tipoItem,$fchCreacion,$monto,$pagado,$descripcion,$fchQuincena,$limite);
  $itemsAFavor = buscarHistoricoAFavor($db,$dni);
  require 'vistas/historicoTrabajador.php';
}

function verhrasextra(){
  require 'modelos/trabajadoresModelo.php';
  require_once 'librerias/config.ini.php';  
  
  $fch = (isset($_POST['txtHraExtraFch']) && $_POST['txtHraExtraFch']!= ''  )?$_POST['txtHraExtraFch']:Date("Y-m-d");
  $dia = Date("d",strtotime($fch));
  $mes = Date("m",strtotime($fch));
  $anho = Date("Y",strtotime($fch));
  if (isset($_POST['txtTrabajador'])){
    $aux = $_POST['txtTrabajador'];
    $auxArreglo = explode("-", $aux);
    $idTrab = $auxArreglo[0];
  } else 
    $idTrab = "";

  if (isset($_POST['txtCliente'])){
    $cliente = $_POST['txtCliente'];
    $auxArreglo = explode("-", $cliente);
    $idCliente = $auxArreglo[0];
  } else {
    $cliente = $idCliente = "";
  }    
   
  if (isset($_POST['txtCuenta'])){
    $cuenta = $_POST['txtCuenta'];
  } else {
    $cuenta = "";
  }

  if (isset($_POST['txtPagado'])){
    $pagado = $_POST['txtPagado'];
  } else {
    $pagado = "";
  }       
  $items = buscarHrasExtra($db,$idTrab,$idCliente,$cuenta,$pagado,$anho,$mes,$dia);
  require 'vistas/listarHrasExtra.php';
}

function listarDescuentosPendientes(){
  require 'modelos/trabajadoresModelo.php';
  $auxTrabaj = isset($_POST['cmbTrabajador'])?$_POST['cmbTrabajador']:"";
  $arrTrabaj = explode("-",$auxTrabaj);
  $dni = $arrTrabaj[0];
  $fchPago = isset($_POST['txtFchPago'])?$_POST['txtFchPago']:NULL;
  $nroCuota = isset($_POST['txtNroCuota'])?$_POST['txtNroCuota']:NULL;
  $tipoItem = isset($_POST['txtTipo'])?$_POST['txtTipo']:NULL;
  $mntCuota = isset($_POST['txtMntCuota'])?$_POST['txtMntCuota']:NULL;
  $mntTotal = isset($_POST['txtMntTot'])?$_POST['txtMntTot']:NULL;
  $descrip = isset($_POST['txtDescrip'])?$_POST['txtDescrip']:NULL;
  $items = buscarDsctosPendientes($db,$dni,$fchPago,$tipoItem,$nroCuota,$mntCuota,$mntTotal,$descrip);
  require 'vistas/listarDescuentosPendientes.php';
}

function vacaciones(){
  require 'modelos/trabajadoresModelo.php';
  if(isset($_POST['txtDni'])){
    $aux = $_POST['txtDni'];
    $dni = strtok($aux, '-');
  } else {
    $aux = $dni = null;
  }

  $items = buscarTrabajVacac($db,$dni);
  require 'vistas/listarTrabajVacaciones.php';
}

function adicvacaciones(){
  require 'modelos/trabajadoresModelo.php';
  if(isset($_POST['txtDni'])){
    $aux = $_POST['txtDni'];
    $dni = strtok($aux, '-');
  } else {
    $aux = null;
    $dni = $_GET['dni'];
  }
  $items = buscarTrabajador($db,$dni);
  require 'vistas/addVacaciones.php';
}

function reportevacaciones(){
  require 'modelos/trabajadoresModelo.php';
  if(isset($_POST['txtDni'])){
    $aux = $_POST['txtDni'];
    $dni = strtok($aux, '-');
  } else {
    $aux = $dni = null;
  }
  $items = buscarDetallesVacaciones($db,$dni);
  require 'vistas/reporteVacaciones.php';

}

function generaPeriodoVacac(){
  require 'modelos/trabajadoresModelo.php';
  $dni =  $_GET['dni'];
  $fchalta =  $_GET['fchalta'];
  //$item = generaPeriodoVac($db,$dni,$fchIni);
  require 'vistas/reporteGeneraPeriodoVacac.php';
}

function entregauniformes(){
  require 'librerias/config.ini.php';
  require 'modelos/trabajadoresModelo.php';
  require 'modelos/prendasModelo.php';
  $arrNroCuotasPrenda = explode(",",$nroCuotasPrenda);
  $dni =  $_GET['dni'];
  $itemTrab =  buscarConductor($db,$dni);
  //$tipoPrendas = buscarTipoPrendas($db);
  $prendas = buscarPrendasEnt($db);
  require 'vistas/entregaUniformes.php';
}

function editaentrega(){
  require 'modelos/trabajadoresModelo.php';
  $id =  $_REQUEST['id'];
  $prenda = buscarEntrega($db,$id);
  $tipoPrendas = buscarTipoPrendas($db);
  require 'vistas/editaEntrega.php';
}

function eliminaentrega(){
  require 'modelos/trabajadoresModelo.php';
  require 'modelos/prendasModelo.php';
  $id =  $_REQUEST['id'];
  require 'vistas/eliminaEntrega.php';
}

function historicoTrabajPrendas(){
  require 'modelos/trabajadoresModelo.php';
  if(isset($_POST['cmbTrabajPrendaBarr'])){
    $aux = $_POST['cmbTrabajPrendaBarr'];
    $dni = strtok($aux, '-');
  } else if(isset($_POST['txtTrabajPrenda'])){
    $aux = $_POST['txtTrabajPrenda'];
    $dni = strtok($aux, '-');
  } else {
    $dni = null;
  }

  $fchEntrega = (isset($_POST['txtFchEntrega'])?$_POST['txtFchEntrega']:null);
  $prenda = (isset($_POST['txtPrenda'])?$_POST['txtPrenda']:null);
  $usuario = (isset($_POST['txtUsuario'])?$_POST['txtUsuario']:null);

  $_SESSION['prendaFchEntrega'] = $fchEntrega;
  $_SESSION['prendaPrenda'] = $prenda;
  $_SESSION['prendaUsuario'] = $usuario;


  $items = buscarEntregaPrendas($db,$dni,$fchEntrega,$prenda,$usuario);
  //$tipoPrendas = buscarTipoPrendas($db);
  require 'vistas/listarHistoricoTrabajPrendas.php';
}

function nuevodocumentoadjunto(){
  require 'modelos/trabajadoresModelo.php';
  $dni = $_GET['id'];
  require 'vistas/nuevoDocAdjunto.php';
}

function editadocumentoadjunto(){
  require 'modelos/trabajadoresModelo.php';
  $id = $_REQUEST['id'];
  $datos = buscarTrabajadorDocsVarios($db,$id);
  require 'vistas/editaDocAdjunto.php';
}

function eliminadocumentoadjunto(){
  require 'modelos/trabajadoresModelo.php';
  $id = $_REQUEST['id'];
  $datos = buscarTrabajadorDocsVarios($db,$id);
  require 'vistas/eliminaDocAdjunto.php';
}

function cambiarAdjuntoDocumento(){
  require 'modelos/trabajadoresModelo.php';
  $id = $_REQUEST['id'];
  $datos = buscarTrabajadorDocsVarios($db,$id);
  require 'vistas/cambiarAdjuntoDocumento.php';
}

function liquidarTrabajador(){
  require 'modelos/trabajadoresModelo.php';
  if (isset($_POST['cmbTrabajador3']) AND $_POST['cmbTrabajador3'] != '' )
    $aux = $_POST['cmbTrabajador3'];  
  else
    $aux = $_POST['txtLiquidTrabaj'];

  $dni = strtok($aux,'-');
  $dataPendientes =  buscarParaLiquidar($db,$dni);
  $dataFdoGarantia = buscarDevFondoGarantia($db,$dni);

  
  require 'vistas/liquidarTrabajador.php';
}

function repoDeudasPendientes(){
  require 'modelos/trabajadoresModelo.php';
  $dataPendientes = buscarDeudasPendientesDelTrabajador($db);
  require 'vistas/adicionales/repoDeudasPendientesProcesoDeDatos.php';
  require 'vistas/repoDeudasPendientes.php';
}
?>
