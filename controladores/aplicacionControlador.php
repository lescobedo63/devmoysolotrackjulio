<?php
require 'controladores/trabajadoresControlador.php';

function mantenimiento() {
	//Incluye el modelo que corresponde
	require 'modelos/aplicacionModelo.php';
  require 'modelos/despachosModelo.php';
  require 'modelos/clientesModelo.php';

	$categorias = buscarTodasLasCategorias($db);
	$telefonos =  buscarTodosLosTelefonos($db);
	$mudanzas =  buscarTodasLasMudanzas($db);
	$entidadPensiones = buscarEntidadPensiones($db);
	$sucursalesRipley = buscarSucursalesRipley($db);
	$zonasRipley = buscarZonasRipley($db);
  $zonasDespachos = buscarZonasDespacho($db);
	$terceros = buscarTerceros($db);
	$licenciasDefinir = buscarLicenciasDefinir($db);
	$gruposRendimiento= buscarTodoGrupoRendimiento($db);
  $dataIncluye= buscarDataIcnluye($db);
 	//Pasa a la vista toda la información que se desea representar
	require 'vistas/mantenTablas.php';
}

function nuevaDataIncluye() {
  require 'vistas/nuevaDataIncluye.htm';
}
function editaDataIncluye(){
  $id = $_GET['id'];
  $descripcion = $_GET['descripcion'];
  $precio = $_GET['precio'];
  $estado = $_GET['estado'];
  require 'vistas/editaDataIncluye.htm';
}
function eliminaDataIncluye(){
  $id = $_GET['id'];
  $descripcion = $_GET['descripcion'];
  require 'vistas/eliminaDataIncluye.htm';
}

function listaralertasusuarios() {
  require 'modelos/aplicacionModelo.php';
  $items=buscaralertasusuarios($db);
  require 'vistas/listarAlertaUsuarios.htm';
}

function nuevoalertausuario(){
 require 'vistas/nuevoAlertaUsuario.htm';
}

//function editaalertausuario(){
  //$item = $_GET['dni'];
  //$descrip = $_GET['descripcion'];
  //require 'vistas/editaAlertaUsuario.htm';
//}
               
function eliminaalertausuario(){
  $dni = isset ($_GET['dni'])?$_GET['dni']:$_POST['dni'];
  require 'vistas/eliminaAlertaUsuario.htm';
}

function nuevacategoria() {
  require 'vistas/nuevaCategoria.htm';
}

function editacategoria(){
  $idCategoria = $_GET['idcategoria'];
  $descrip = $_GET['descripcion'];
  require 'vistas/editaCategoria.htm';
}

function eliminacategoria(){
  $idCategoria = $_GET['idcategoria'];
  require 'vistas/eliminaCategoria.htm';
}

function nuevotelefono() {
  require 'vistas/nuevoTelefono.htm';
}

function editatelefono(){
  require 'modelos/aplicacionModelo.php';
  $estadosTelefono = buscarEstadosTelefonos($db);
  $idNroTelefono = $_GET['nrotelefono'];
  $estado = $_GET['estado'];
  $modelo = $_GET['modelo'];
  require 'vistas/editaTelefono.htm';
}

function eliminatelefono(){
  $idNroTelefono = $_GET['nrotelefono'];
  require 'vistas/eliminaTelefono.htm';
}

function verprincipal() {
  if ($_SESSION['cliente'] == ''){
    require 'modelos/aplicacionModelo.php';
    require 'modelos/trabajadoresModelo.php';
    require 'librerias/config.ini.php';
    $advertenciasVehiculos = buscarAdvertenciasVehiculos($db);
    $advertenciasVehiculosMantenimientoKms = buscarAdvertenciasVehiculosMantenimientoKms($db);
    $advertenciasVehiculosMantenimientoDias = buscarAdvertenciasVehiculosMantenimientoDias($db);
    $advertenciasSolicitudesAbastecimientos = buscarAdvertenciasSolicitudesAbastecimiento($db);

    $advertVencTrabajadores = buscarAlertasLicenciasVencidas($db);
    $onomasticosdelmes = buscarOnomasticos($db);
    require 'vistas/verPrincipal.php';
  } else {
    listardespachoscliente();
  }
}

function listardespachoscliente(){
  require 'modelos/despachosModelo.php';
  require 'modelos/zonasModelo.php';
  require 'modelos/vehiculosModelo.php';


  $arrayCuentas = $_SESSION['cuentas'];
  $idCliente =  $_SESSION['cliente'];
  //$items = buscarDespachosCliente($db,$idCliente,$arrayCuentas);
  $anhio = $mes = $dia = $correlativo= $guiaPorte = $placa = $conductor = $cuenta = $fchFin = $liquid = $docLiq = null;
  if (isset($_POST['txtCodigo'])) 
    $_SESSION['codigo'] = $_POST['txtCodigo'];
  $codigo = $_SESSION['codigo'];
  if ($codigo != NULL){
    $anhio = strtok($codigo, '-')==0?NULL:strtok($codigo, '-') ;
    $mes = strtok('-');
    $mes = $mes==0?NULL:$mes;
    $dia = strtok('-');
    $dia = $dia==0?NULL:$dia;
    $correlativo = strtok('-');
    $correlativo = $correlativo==0?NULL:$correlativo;  
  }

  if (isset($_POST['txtFchFin'])) 
    $_SESSION['fchFin'] = $_POST['txtFchFin'];
  $fchFin = $_SESSION['fchFin'];

  if (isset($_POST['txtPlaca']))  
    $_SESSION['placa'] = $_POST['txtPlaca'];
  $placa = $_SESSION['placa'];

  if (isset($_POST['txtConductor'])){
    $aux = $_POST['txtConductor'];  
    $_SESSION['conductor'] = strtok($aux,'-');
  }
  $conductor = $_SESSION['conductor'];

  if (isset($_POST['txtGuia'])) 
    $_SESSION['guiaPorte'] = $_POST['txtGuia'];
  $guiaPorte = $_SESSION['guiaPorte'];

  if (isset($_POST['txtCuenta'])) 
    $_SESSION['cuenta'] = $_POST['txtCuenta'];
  $cuenta = $_SESSION['cuenta'];

  if ($codigo == NULL AND $placa == NULL AND $guiaPorte == NULL AND $cuenta == NULL AND $conductor == NULL){
    $anhio = Date("Y");
    $mes = Date("m");
    $dia = Date("d");
    $correlativo = null;
    $_SESSION['codigo'] = "$anhio-$mes-$dia";
  }
  $items = buscarDespachos($db,$anhio,$mes,$dia,$correlativo,$guiaPorte,$placa,$conductor,$idCliente, $cuenta, $fchFin, $liquid,$docLiq);
  $nroItems = count($items);
  if ($placa == NULL && $conductor == NULL)
    $itemsSoloPersonal = buscarDespachosSoloPersonal($db,$anhio,$mes,$dia,$correlativo,$idCliente,$cuenta,$fchFin,$liquid,$docLiq);
  else
    $advertencia = "No aplicable";
  $nroItemsSP = isset($itemsSoloPersonal)?count($itemsSoloPersonal):0;

  $sedes = buscarSedes($db);
  $arraySedes = array();
  $arraySedes[""] = 'No definido'; 
  foreach ($sedes as $item) {
    $codigo = $item['idSede'];
    $sede   = $item['descripcion'];
    $arraySedes[$codigo] = $sede;
    $_SESSION['arraySedes'] = $arraySedes;
  }

  $ptosDestino =  buscarUbicaciones($db);
  $arrayDestinos = array();
  $arrayDestinos[""] = "No definido"; 
  foreach ($ptosDestino as $item) {
    $codigo  = $item['idUbicacion'];
    $destino = $item['descripcion'];
    $arrayDestinos[$codigo] = $destino;
    $_SESSION['arrayDestinos'] = $arrayDestinos;
  }

  $vehiculosActivos = buscarVehiculos($db," WHERE estado = 'Activo' ");
  $arrayVehiculos = array();
  foreach ($vehiculosActivos as $item) {
    $placa =  $item['idPlaca'];
    $m3 =  $item['dimInteriorAlto']*$item['dimInteriorLargo']*$item['dimInteriorAncho'];
    $arrayVehiculos[$placa] = $m3;
    $_SESSION['arrayVehiculos'] = $arrayVehiculos;
  }

  require 'vistas/listarDespachosCliente.php';
}

function editamudanza(){
  require 'modelos/aplicacionModelo.php';
  $tipoServicio = $_GET['tiposervicio'];
  $mudanza = buscarDatosMudanza($db,$tipoServicio);
  require 'vistas/editaMudanza.htm';
}

function ingresemes(){
  require 'vistas/ingresaMes.htm';
}

function reportepormes(){
  require 'modelos/aplicacionModelo.php';
  require 'modelos/despachosModelo.php';
  require 'modelos/trabajadoresModelo.php';
  if(isset($_GET['editarValor'])){
    $fchDespacho = $_GET['fch'];
    $corr = $_GET['corr'];
    $idTrab = $_GET['dni'];
    //$tipo = $_GET['tipo'];
    $nvoValor = $_GET['nvoValor'];
    editaValor($db,$fchDespacho,$corr,$idTrab,$nvoValor); 
    logAccion($db,"Se modificó en despacho $fchDespacho-$corr el valorRol a $nvoValor",$idTrab,null);
  }
  $hoy = Date("Y-m-d");
  if (isset($_POST['txtFchBarr']) && $_POST['txtFchBarr'] != '')
    $fch = $_POST['txtFchBarr'];
  else if (!isset($_POST['txtFch']) || $_POST['txtFch'] == '')
    $fch = $hoy;
  else
  $fch = $_POST['txtFch'];

  //$fch = (!isset($_POST['txtFch']) || $_POST['txtFch'] == '')?$hoy:$_POST['txtFch'];
  $dia = Date("d",strtotime($fch));
  $mes = Date("m",strtotime($fch));
  $anho = Date("Y",strtotime($fch));
  //$items = infoPorMes($db,$mes,$anho);
  //require 'vistas/reportePorMes.php';
  $quincena = ($dia > 15)?15:0;
  $items = infoPorQuincena($db,$mes,$anho,$quincena);
  require 'vistas/reportePorQuincena.php';
}

function nuevaentidadpension() {
  require 'vistas/nuevaEntidadPension.htm';
}

function editaentidadpension(){
  require 'modelos/aplicacionModelo.php'; 
  $nombre = $_REQUEST['nombre'];
  $items = buscarEntidadPensiones($db,$nombre);
  require 'vistas/editaEntidadPension.php';
}

function eliminaentidadpension(){
  $nombre = $_GET['nombre'];
  require 'vistas/eliminaEntidadPension.htm';
}

function nuevasucursalripley() {
  require 'vistas/nuevaSucursalRipley.htm';
}

function eliminasucursalripley(){
  $sucursal = $_GET['sucursal'];
  require 'vistas/eliminaSucursalRipley.htm';
}

function nuevazonaripley(){
  require 'vistas/nuevaZonaRipley.htm';
}

function eliminazonaripley(){
  $zona = $_GET['zona'];
  require 'vistas/eliminaZonaRipley.htm';
}

function nuevazonadespacho(){
  require 'vistas/nuevaZonaDespacho.htm';
}

function eliminazonadespacho(){
  $zona = $_GET['zona'];
  require 'vistas/eliminaZonaDespacho.htm';
}

function listarusuario(){
  require 'vistas/listarUsuarios.php';
}

function listarusuarioOcurrencia(){
  require 'vistas/listarUsuariosOcurrencia.php';
}

function nuevousuario(){
  require 'vistas/nuevoUsuario.htm';
}

function eliminausuario(){
  $idUsuario = $_GET['idUsuario'];
  require 'vistas/eliminaUsuario.htm';
}

function editausuario(){
  require 'modelos/aplicacionModelo.php';
  $idUsuario = $_GET['idUsuario'];
  $dataUsuario = buscarDatosUsuario($db,$idUsuario);
  $arrPermisos = buscarUsuPermisos($db, $idUsuario);
  require 'vistas/editaUsuario.php';
  //require 'vistas/editaUsuario.htm';
}

function cambiarPass(){
  $cambiar = $_REQUEST['cambiar'];
  require 'vistas/cambiarpass.php';
}

function nuevotercero(){
  //$idUsuario = $_GET['idUsuario'];
  require 'vistas/nuevoTercero.htm';
}

function eliminatercero(){
  $documento = $_GET['documento'];
  require 'vistas/eliminaTercero.htm';
}

function editatercero(){
  require 'modelos/aplicacionModelo.php';
  $documento = $_GET['documento'];
  $dataTercero = buscarDatosTercero($db,$documento);
  require 'vistas/editaTercero.htm';
}

function nuevalicenciadefinir(){
  require 'vistas/nuevaLicenciaDefinir.htm';
}

function editalicenciadefinir(){
  require 'modelos/aplicacionModelo.php';
  $nombLicencia = isset($_GET['nombLicencia'])?$_GET['nombLicencia']:$_POST['nombLicencia'];
  $dataLicencia = buscarNombLicencia($db,$nombLicencia);
  require 'vistas/editaLicenciaDefinir.htm';
}

function eliminalicenciadefinir(){
  $nombLicencia = isset($_GET['nombLicencia'])?$_GET['nombLicencia']:$_POST['nombLicencia'];
  require 'vistas/eliminaLicenciaDefinir.htm';
}

function nuevogruporendimiento() {
  require 'vistas/nuevoGrupoRendimiento.htm';
}

function editagruporendimiento(){
  require 'modelos/aplicacionModelo.php';
  $grupoRendimiento = isset($_GET['grupoRendimiento'])?$_GET['grupoRendimiento']:$_POST['grupoRendimiento'];
  $dataGrupoRendimiento = buscarGrupoRendimiento($db,$grupoRendimiento);
  require 'vistas/editaGrupoRendimiento.htm';
}

function eliminagruporendimiento(){
  $grupoRendimiento = isset($_GET['grupoRendimiento'])?$_GET['grupoRendimiento']:$_POST['grupoRendimiento'];
  require 'vistas/eliminaGrupoRendimiento.htm';
}


function configurarvariables(){
  //require 'modelos/aplicacionModelo.php';
  $mensaje = "";
  if(isset($_POST['btnEnviar'])){
    $contenido = trim($_POST['txtConfig']);
    @ $fp = fopen("librerias/config.ini.php", 'w');
    if(!$fp){
      echo '<p style="color: #ff0000">
      <strong>No se pudo abrir el archivo
      </strong>
      </p>';
      exit;
    } else {
      $mensaje = "Sus cambios fueron almacenados. Puede realizar otro cambio o salir de esta opción";
    }
    // para escribir en el archivo,
    //strlen($texto) nos da la longitud de la cadena del archivo
    fwrite($fp, $contenido, strlen($contenido));
    fclose($fp);
  } else if(isset($_POST['btnDefault'])){
    // Abrir el archivo
    $archivo = 'librerias/config.default.php';
    $abrir = fopen($archivo,'r+');
    $contenido = fread($abrir,filesize($archivo));
    $contenido = trim($contenido);
    fclose($abrir);
  } else {
    // Abrir el archivo
    $archivo = 'librerias/config.ini.php';
    $abrir = fopen($archivo,'r+');
    $contenido = fread($abrir,filesize($archivo));
    $contenido = trim($contenido);
    fclose($abrir);
  }      
  require 'vistas/configurarVariables.php';
}



function cargaRosen(){
  require_once 'librerias/config.ini.php';
  require 'modelos/aplicacionModelo.php';
  require 'modelos/clientesModelo.php';
  procesaInfo($db,$porcIgv);
  $dataRosen = leerDataRosen($db);
  require 'vistas/cargaRosen.php';
}

function listaralertasgerencialusuarios() {
  require 'modelos/aplicacionModelo.php';
  $items=buscaralertasgerencialusuarios($db);
  require 'vistas/listarAlertaGerencialUsuarios.php';
}

function nuevoalertagerencialusuario(){
  require 'vistas/nuevoAlertaGerencialUsuario.php';
}
               
function eliminaalertagerencialusuario(){
  $dni = isset ($_GET['dni'])?$_GET['dni']:$_POST['dni'];
  require 'vistas/eliminaAlertaGerencialUsuario.php';
}

function verificar(){
  require 'moy.htm';
}

function listarlog(){
  require 'vistas/listarLogAccionesDt.php';
}


?>
