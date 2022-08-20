<?php

function recibirTripulacion(){
	if (isset($_POST)){
		$fch = Date("Y-m-d");
		require 'modelos/movilModelo.php';
	  $cadena2 = json_decode(utf8_decode($_POST['cadjson2']));
      $cadena3 = serialize($cadena2);
	  //logCadena($db,$cadena3,'cadena recibida por tripulacion');
	  logCadena($db,$cadena3,'Caso 110. Se recibe tripulación');

	  $cadError = "";
	  $keyAux = $_POST['txtValor01']; 
	  foreach ($cadena2 as $idDespacho => $data) {
	  	if ($keyAux != "") $idDespacho = $keyAux;

	  	$fchDespacho = substr($idDespacho, 0, 10);
	  	$correlativo = substr($idDespacho, 11);
	  	$verifEstado = verificaEstadoDespacho($db,$fchDespacho,$correlativo);
	  	  if ($verifEstado == 'Programado' || $verifEstado == 'EnRuta' ){
	  	    $dataDespacho = buscarDataDespacho($db,$fchDespacho, $correlativo);
	  	    foreach ($dataDespacho AS $llave => $itemDespacho){
	  	      $hraInicioBase = $itemDespacho['hraInicioBase'];
	  	      $hraFinBase = $itemDespacho['hraFin'];
	  	    }

		  	foreach ($data as $item => $valItem) {
		  	  if ($item == 'Personal'){
		  	  	//Se elimina tripulacion si la había
		  	  	$cant = eliminaTripulacion($db, $fchDespacho, $correlativo);
		  	  	if ($cant == 0) $cadError .= "Correcto. Es un proceso de inserción de Tripulación";
	            else $cadError .= "Correcto. Es un proceso de edición de Tripulación";
		  	  	foreach ($valItem as $idTrabajador => $tipoRol) {
		  	  		//echo "DNI $idTrabajador  ROL $tipoRol";
		  	  		$rpta = insertaTripulacion($db,$fchDespacho,$correlativo,$idTrabajador,$tipoRol, $hraInicioBase, $hraFinBase);
		  	  		//echo "$rpta<br>";
		  	  		if ($rpta != 1){
		  	  			$cadError .= "No se insertó $idTrabajador, con Rol $tipoRol. ";
		  	  		}
		  	  	}
		  	  } else {
		  	  	  //echo "$item  $valItem";
		  	  }
		  	}
	    } else {
	      $cadError .= "No se puede editar el Despacho";
	    }
	  }

	  if ($cadError == ""){
	  	$arrRpta = array(
			"error" => utf8_encode(""),
			"statusCode" => "200",
			"success" => True,
			"token" => "queva"
		);
		$rpta = json_encode($arrRpta);

	  } else {
	  	$arrRpta = array(
			"error" => utf8_encode($cadError),
			"statusCode" => "",
			"success" => False,
			"token" => ""
		);
		$rpta = json_encode($arrRpta);

	  }
	}



	print_r($rpta);
}


function enviarInfoTrabajadores() {
	require 'modelos/movilModelo.php';
	$dataTrabajadores = buscarInfoTrabajadores($db);

  $arrData = array();
  //$cont = 1;
	foreach ($dataTrabajadores AS $key => $item) {
		$idTrabajador = $item['idTrabajador'];
		$apPaterno = $item['apPaterno'];
		$apMaterno = $item['apMaterno'];
		$nombres    = $item['nombres'];
		$tipoTrabajador    = $item['tipoTrabajador'];
		//echo "$idTrabajador $rznSocial  $nombres $cuentas ";
		$arrData[$idTrabajador] = array(
			              "apPaterno" => utf8_encode($apPaterno),
			              "apMaterno" => utf8_encode($apMaterno),
			              "nombres" => utf8_encode($nombres),
			              "tipoTrabajador" => utf8_encode($tipoTrabajador)
			            );
		//$cont++;
	}

	$cadjson = json_encode($arrData);

  print_r($cadjson);
}

function enviarInfoVehiculos() {
	require 'modelos/movilModelo.php';
	$dataVehiculos = buscarInfoVehiculos($db);

  $arrData = array();
  //$cont = 1;
	foreach ($dataVehiculos AS $key => $item) {
		$idPlaca = $item['idPlaca'];
		$m3Facturable = $item['m3Facturable'];
		$arrData[$idPlaca] = $m3Facturable;
	}

	$cadjson = json_encode($arrData);
  print_r($cadjson);
}

function enviarInfoUbicaciones() {
	require 'modelos/movilModelo.php';
	$dataUbicaciones = buscarInfoUbicaciones($db);
  $arrData = array();
  //$cont = 1;
	foreach ($dataUbicaciones AS $key => $item) {
		$idUbicacion = $item['idUbicacion'];
		$descripcion = utf8_encode($item['descripcion']);
		$arrData[$idUbicacion] = $descripcion;
	}

	$cadjson = json_encode($arrData);
  print_r($cadjson);
}


function guardarLogError(){
	if (isset($_POST)){
	  $fch = Date("Y-m-d");
	  $rpta = 0;
	  require 'modelos/movilModelo.php';
	  $cadena2 = json_decode(utf8_decode($_POST['cadjson2']));
	  //print_r($cadena2);
	  $cadena3 = serialize($cadena2);
  	foreach ($cadena2 as $campo => $data) {
  		$$campo = $data;
  		//echo "Item $campo valor $data<br>";	
  	}
	  logCadena($db,$cadena3,'Caso 900. Cadena recibida como error',$usuario);
  	$rpta = guardarLog($db, $deviceId, $descripcion, $usuario, $fecha);
  	//echo "RPTA $rpta";
  }

}

function enviarPuntosEntrega(){
	if (isset($_POST)){
		date_default_timezone_set('America/Lima');
	  $fch = Date("Y-m-d");
	  require 'modelos/movilModelo.php';
    /*print_r($_POST['cadjson2']);*/
    $cadena2 = json_decode(utf8_decode($_POST['cadjson2']));
    $cadena3 = serialize($cadena2);	  
    foreach ($cadena2 as $campo => $data) {
    	$$campo = $data;    	
    }
    $idProgramacion = (isset($idProgramacion)) ? $idProgramacion : ""; 
    logCadena($db,$cadena3,'Caso 230. Se solicitan puntos de entrega de un despacho', $usuario);
    $auxVerifica = verificaAccesoDespacho($db, $placa, $cliente, $idProgramacion ,$fch);
    $userDesp = "";
    $correlativo = $estado = $cantPuntos = 0;
    foreach ($auxVerifica as $value) {
    	$correlativo = $value['correlativo'];
    	$estado =  $value['estadoDespacho'];
    	$userDesp =  $value['usuario'];
    	$cantPuntos =  $value['cantPuntos'];
    }

    //echo "FECHA $fch, Correlativo $correlativo";

    /*if ($correlativo != 0 && $estado == "EnRuta" && $cantPuntos > 0 && $userDesp == $usuario ){*/
    if ($correlativo != 0 && $estado == "EnRuta" && $cantPuntos > 0 ){	
    	$puntosEntrega = buscarPuntosEntrega($db,$fch,$correlativo);
    	$arrRpta = array(
    		"success" => true,
    		"observacion" => "",
    	);

     	foreach ($puntosEntrega as $item) {
    		//$correlPunto = $item['correlPunto'];
    		//$tipoPunto = $item['tipoPunto'];
    		$arrAux = array(
		                  "idPunto" => 1*$item['idPunto'], // $correlPunto,
		                  "correlPunto" =>  $item['correlPunto'],
		                  "idRuta"  => 1*$item['idRuta'],
		                  "ordenPunto"  =>  $item['ordenPunto'],
		                  "tipoPunto"  =>  $item['tipoPunto'],
		                  "hraLlegada"  =>  $item['hraLlegada'],
		                  "nombre"  => utf8_encode($item['nombComprador']),
		                  "direccion"   => utf8_encode($item['direccion']),
		                  "distrito"    => utf8_encode($item['distrito']),
		                  "provincia"   => utf8_encode($item['provincia']),
		                  "guiaCliente" => utf8_encode($item['guiaCliente']),
		                  "adic_01" => utf8_encode($item['adic_01']),
		                  "adic_02" => utf8_encode($item['adic_02']),
		                  "adic_03" => utf8_encode($item['adic_03']),

		                  "estado"     => utf8_encode($item['estado']),
		                  "subEstado"  => utf8_encode($item['subEstado']),
		                  "hraLlegada" => utf8_encode($item['hraLlegada']),
		                  "hraSalida"  => utf8_encode($item['hraSalida']),
		                  "idDistrito" => utf8_encode($item['idDistrito']),
		                  "guiaMoy"    => utf8_encode($item['guiaMoy']),
		                  "referencia" => utf8_encode($item['referencia']),
		                  "telfReferencia"   => utf8_encode($item['telfReferencia']),
		                  "idCarga"    => utf8_encode($item['idCarga']),
		                  "observCargaPunto" => utf8_encode($item['observCargaPunto']),
		                  "observacion" => utf8_encode($item['observacion']),
		                  "foto_1"     => utf8_encode($item['foto_1']),
		                  "foto_2" => utf8_encode($item['foto_2']),
		                  "foto_3" => utf8_encode($item['foto_3']),
		                  "foto_4" => utf8_encode($item['foto_4']),
			                  "longitud" => $item['longitud'],
		                  "latitud" => $item['latitud'],

		               );
    		$arrRpta["puntos"][] = $arrAux;
    	}
    } else {
    	if ($correlativo == 0) $observ = "Error. No se pudo encontrar el despacho";
    	else if ($estado != "EnRuta" ) $observ = "Error. El estado es $estado y debe ser EnRuta";
    	else if ($cantPuntos == 0) $observ = "Error. No hay puntos registrados";
    	$arrRpta = array(
    		"success" => false,
    		"observacion" => $observ,
    	);
    }
    $rpta = json_encode($arrRpta);
    logCadenaEnviada($db,$cadena3,utf8_decode(serialize($arrRpta)), "Caso 230. Se solicitan puntos de entrega de un despacho", $usuario);
	
    print_r($rpta);
  }
}


function cerrarDespacho(){
  if (isset($_POST)){
		$fch = Date("Y-m-d");
		require 'modelos/movilModelo.php';
		//print_r($_POST['cadjson2']);
		$cadena2 = json_decode(utf8_decode($_POST['cadjson2']));	  
		$cadena3 = serialize($cadena2);
	  $cadError = "";
  	foreach ($cadena2 as $item => $valItem) {
  	  $$item = utf8_encode($valItem);
  	}
		logCadena($db,$cadena3,'Caso 260. Se solicita cerrar un despacho', $usuario);
  	//Por ahora, luego debe eliminarse
  	$correlativo = "";
  	//Fin de Por ahora, luego debe eliminarse
  	//210217. Esto se usará hasta que Ramiro confirme el cambio en el valor que envía $puntoOrigen.
  	$puntoOrigen = "0002|0041";
  	//Fn de 210217. Esto se usará ...
  	$idSedeOrigen = strtok($puntoOrigen, "|");
  	$puntoOrigen  = strtok("|");
  	$usaReten     = "No"; //Es por ahora 

  	$puedeCerrarse = verificarDatosDespachoParaCerrar($db,$idProgramacion);
  	if($puedeCerrarse == "No") $msjError = "Ya está cerrado";
  	//echo "Puede Cerrarse 1 :$puedeCerrarse<br>";
  	if($puedeCerrarse == "Si"){
  		$puedeCerrarse = verificarDatosPuntosParaCerrar($db,$idProgramacion);
  		if($puedeCerrarse == "No") $msjError = "Hay puntos pendientes";
  	} 
  	//echo "Puede Cerrarse 2 :$puedeCerrarse<br>";
  	if($puedeCerrarse == "Si"){
  		$puedeCerrarse = verificarDatosKilometraje($db, $idProgramacion, $kmInicio, $kmInicioCliente, $kmFinCliente, $kmFin);
  		if($puedeCerrarse == "No") $msjError = "Error en los kilometrajes";
  	} 
  	//echo "Puede Cerrarse 3 :$puedeCerrarse<br>";
  	

  	if ($puedeCerrarse == "Si"){
  		if(!isset($observacion)) $observacion = "";
  		$observacion = utf8_decode($observacion);
  		//echo "OBSERV $observacion";
    	$rpta = llenarCamposCerrarDespacho($db, $idProgramacion, $horaInicio, $horaFinCliente, $fechaFinCliente, $fechaFin, $horaFin, $kmInicio, $kmInicioCliente, $kmFinCliente, $lugarFinCliente, $kmFin, $puntoOrigen, $idSedeOrigen, $observacion, $usaReten, $usuario);
    	if($rpta == 1){
    		$rpta = llenarGuiasMoy($db, $idProgramacion, $guiasMoy, $usuario);
    	}

    	$estado = "Terminado";
  	
    	$rpta = cambiarEstadoDespacho($db,$idProgramacion,$fechaDespacho, $correlativo, $estado);
    	if ($rpta == 1){
    	  $arrRpta = array(
    			"error" => "",
      		"success" => True,
      		"token" => "queva"
	      );
    	} else {
    	  $arrRpta = array(
    			"error" => utf8_encode("No se modificó estado"),
      		"success" => False,
      		"token" => "queva"
	      );
  	  }
  	} else if ($puedeCerrarse == "No") {
  		$arrRpta = array(
    			"error" => utf8_encode($msjError),
      		"success" => False,
      		"token" => "queva"
	      );
  	} else {
  		$arrRpta = array(
    			"error" => utf8_encode("Revise puntos"),
      		"success" => False,
      		"token" => "queva"
	      );
  	}

  	logCadenaEnviada($db,$cadena3,utf8_decode(serialize($arrRpta)), "Caso 260. Se solicita cerrar un despacho", $usuario);
  	$rpta = json_encode($arrRpta);
    print_r($rpta);


	}
}


function listarPuntos(){
	require 'vistas/movilListarPuntos.php';
}


function listarMercaderia(){
	require 'vistas/movilListarMercaderia.php';
}

function listarChoque(){
	require 'vistas/movilListarChoque.php';
}

function listarTelefonoR(){
	require 'vistas/movilListarTelefonoR.php';
}

function listarTelefonoS(){
	require 'vistas/movilListarTelefonoS.php';
}

function listarPapeleta(){
	require 'vistas/movilListarPapeleta.php';
}

function listarAbastecimiento(){
	require 'vistas/movilListarAbastecimiento.php';
}

function listarPeaje(){
	require 'vistas/movilListarPeaje.php';
}

function listarDataCapacitacion(){
	require 'vistas/movilListarDataCapacitacion.php';
}

function listarCorreos(){
	require 'vistas/movilListarCorreos.php';
}

function verFotoMercaderia1() {
	$idmerca = $_GET['id'];
	//require 'modelos/movilModelo.php';
	require 'modelos/vehiculosModelo.php';
	$foto1 = buscarFotoMercaderia1($db,$idmerca);
	//$entidadPensiones = buscarEntidadPensiones($db);
	require 'vistas/verFotoMercaderia1.htm';
}
function verFotoMercaderia2() {
	$idmerca = $_GET['id'];
	//require 'modelos/movilModelo.php';
	require 'modelos/vehiculosModelo.php';
	$foto2 = buscarFotoMercaderia2($db,$idmerca);
	//$entidadPensiones = buscarEntidadPensiones($db);
	require 'vistas/verFotoMercaderia2.htm';
}
function verFotoMercaderia3() {
	$idmerca = $_GET['id'];
	//require 'modelos/movilModelo.php';
	require 'modelos/vehiculosModelo.php';
	$foto3 = buscarFotoMercaderia3($db,$idmerca);
	//$entidadPensiones = buscarEntidadPensiones($db);
	require 'vistas/verFotoMercaderia3.htm';
}

function verFotoChoque1() {
	$idincidencia = $_GET['id'];
	//require 'modelos/movilModelo.php';
	require 'modelos/vehiculosModelo.php';
	$foto1 = buscarFotoChoque1($db,$idincidencia);
	//$entidadPensiones = buscarEntidadPensiones($db);
	require 'vistas/verFotoChoque1.htm';
}
function verFotoChoque2() {
	$idincidencia = $_GET['id'];
	//require 'modelos/movilModelo.php';
	require 'modelos/vehiculosModelo.php';
	$foto2 = buscarFotoChoque2($db,$idincidencia);
	//$entidadPensiones = buscarEntidadPensiones($db);
	require 'vistas/verFotoChoque2.htm';
}
function verFotoChoque3() {
	$idincidencia = $_GET['id'];
	//require 'modelos/movilModelo.php';
	require 'modelos/vehiculosModelo.php';
	$foto3 = buscarFotoChoque3($db,$idincidencia);
	//$entidadPensiones = buscarEntidadPensiones($db);
	require 'vistas/verFotoChoque3.htm';
}

function verFotoTelefono() {
	$idtelefono = $_GET['id'];
	//require 'modelos/movilModelo.php';
	require 'modelos/vehiculosModelo.php';
	$foto = buscarFotoTelefono($db,$idtelefono);
	//$entidadPensiones = buscarEntidadPensiones($db);
	require 'vistas/verFotoTelefono.htm';
}

function verFotoPapeleta() {
	$idinfraccion = $_GET['id'];
	//require 'modelos/movilModelo.php';
	require 'modelos/vehiculosModelo.php';
	$foto = buscarFotoPapeleta($db,$idinfraccion);
	//$entidadPensiones = buscarEntidadPensiones($db);
	require 'vistas/verFotoPapeleta.htm';
}

function verFotoPeaje()
{
	$idpeaje = $_GET['id'];
	//require 'modelos/movilModelo.php';
	require 'modelos/vehiculosModelo.php';
	$foto = buscarFotoPeaje($db, $idpeaje);
	//$entidadPensiones = buscarEntidadPensiones($db);
	require 'vistas/verFotoPeaje.htm';
}

function verFotoAbastecimiento() {
	$idabastecimiento = $_GET['id'];
	$foto = $_GET['foto'];
	//require 'modelos/movilModelo.php';
	require 'modelos/vehiculosModelo.php';
	$foto = buscarFotoAbastecimiento($db,$idabastecimiento,$foto);
	//$entidadPensiones = buscarEntidadPensiones($db);
	require 'vistas/verFotoAbastecimiento.htm';
}


function listarDespachosTripulacion(){
	require 'barramenu.php';
	$estado = "noprogramacion";
	require 'vistas/movilListarDespachosTripulacion.php';
}

  function guardarAbastecimiento(){
  	if (isset($_POST)){
  	  $fch = Date("Y-m-d");
  	  //$usuario = $_SESSION['usuario'];
  	  require 'modelos/movilModelo.php';
        /*print_r($_POST['cadjson2']);*/
	    $cadena2 = json_decode(utf8_decode($_POST['cadjson2']));
	    $cadena3 = serialize($cadena2);

	    logCadena($db,$cadena3,'cadena recibida por abastecimiento');

  	  $rpta = manejarCombustible($db, $cadena2);
  	  $hora = strtok($rpta, "|");
  	  $placa = strtok("|");
  	  $rptaMsj =  strtok("|");

  	  if($rptaMsj == "Ok") $success = true;
      else  $success = false;
    	$arrRpta = array(
      	"fchCreacion" => $fch,
      	"hraCreacion" => $hora,
  	  	"placa" => $placa,
  		 	"error" => utf8_encode($rptaMsj),
	  	  "success" => $success,
			);

    } else {
  	  $arrRpta = array(
  			"fchCreacion" => "",
  			"hraCreacion" => "",
  			"idPlaca" => "",
  			"error" => "No hay datos que guardar",
	  	  "success" => False,
			 );
    }
  	$rpta = json_encode($arrRpta);
  	print_r($rpta);
  }



  function enviarInfoClienteCuentas() {
   	require 'modelos/movilModelo.php'; 
    $dataClienteCuentas = buscarInfoClienteCuentas($db);

    $arrData = array();
    $arrCuentas = array();
    $arrCuentasPdtos = array();
    $cont = 1;
    $idClienteAnt = $idAnt = "";
    $primero = "Si";
	  foreach ($dataClienteCuentas AS $key => $item) {
	  	$idCliente = $item['idCliente'];
	  	$id = $item["correlativo"];	  	

	  	if ($primero == "Si"){
	  		$primero = "No";
	  		$idClienteAnt = $idCliente;
	  		$idAnt = $id;
	  	}
	  	if ($idCliente != $idClienteAnt){
	  		$arrCuentas[] = array("id" => $idAnt, "nombre" => $nombreCuenta, "productos" => $arrCuentasPdtos);
	      $idAnt = $id;
	      $arrCuentasPdtos = array();

	  		$arrData[$idClienteAnt] = array(
			                  "nombre" => utf8_encode($nombre),
			                  "cuentas" => $arrCuentas
			                );
	  		$arrCuentas = array();
	    	$idClienteAnt = $idCliente;
	    	//$idAnt = $id;
	    } else {

	    };
	    
      if ($id != $idAnt){
	      $arrCuentas[] = array("id" => $idAnt, "nombre" => $nombreCuenta, "productos" => $arrCuentasPdtos);
	      $arrCuentasPdtos = array();
	      $idAnt = $id;      	
      }
      ////
	  	$idProducto = $item["idProducto"];
	  	$nombProducto = $item["nombProducto"];	  	
	  	$arrCuentasPdtos[] = array(
	  		        "id"=> 1*$idProducto,
	  		        "nombre" => $nombProducto
	  		      );
	  	////
	    $rznSocial = $item['rznSocial'];
	  	$nombre    = $item['nombre'];
	  	$nombreCuenta = $item["nombreCuenta"];
  	}

  	//es el ultimo dato

  	$arrCuentas[] = array("id" => $id, "nombre" => $nombreCuenta, "productos" => $arrCuentasPdtos);
	  //$arrCuentasPdtos = array();
	  //$idAnt = $id;
  	$arrData[$idClienteAnt] = array(
			        "nombre" => utf8_encode($nombre),
			        "cuentas" => $arrCuentas
			      );
 
  	$cadjson = json_encode($arrData);
    print_r($cadjson); 
  }



  //////////////////////////////////////////////////////////////
  ////////////// AQUI VA A IR LO QUE YA SE VA REVISANDO ////////

  function acceder(){
    require 'modelos/movilModelo.php';
    $usuario = $_POST['txtUsuario'];
    $pass = $_POST['txtPass'];
    $cadena = $usuario."-".$pass;
    $verificar = verificarUsuario($db,$usuario,$pass);
    logCadena($db,$cadena,'Caso 040. Se verifica el usuario', $usuario);

    $cadjson = json_encode($verificar);
    print_r($cadjson);
  }

  function programDespacho(){
    //Y el usuario???
  	$fch = Date("Y-m-d");
  	$error = "";
  	$movilAsignado = "";
  	$conductor = "";

	  require 'modelos/movilModelo.php';
	  $correlativo = $cantConductor =	$cantAux = $cantTotAux = 0;
  	$cadena2 = json_decode(utf8_decode($_POST['cadjson2']));
	  $cadena3 = serialize($cadena2); 
	  
	  $arrTripulacion = array();

	  foreach ($cadena2 as $campo => $data) {
	  	if($campo == "auxiliares"){
	  		foreach ($data as $key => $idTrab) {
	  			$arrTripulacion[] = $idTrab;
	  			//echo "Trab: ".$idTrab."<br>";
	  			# code...
	  		}
	  	} else {
      	$$campo = $data;
      }	
	  }

	  if(isset($observacion)) $observacion = utf8_decode($observacion);

	  if ($accion == "crear"){
	    logCadena($db,$cadena3,'Caso 330. Se crea un despacho', $usuario);
	  } else {
	  	logCadena($db,$cadena3,'Caso 340. Se edita un despacho', $usuario);
	  }


	  if(isset($cuenta)) $correlCuenta = $cuenta;//Ajuste en el nombre
	  if(!isset($correlativo)) $correlativo = "";
	  if(!isset($observacion)) $observacion = "";

	  if ($accion == "crear"){
	  	/////////////////////////////////////////////////////////////
	  	//Esta parte es temporal hasta que se decida como va el tema de la tripulación
	  	/////////////////////////////////////////////////////////////
	  	$dataUsuAsignado = buscarInfoAsignableDespacho($db,$usuarioAsignado);
	  	//echo "Tipo Trabajador asignado ".$dataUsuAsignado["tipoTrabajador"];
	  	$dataCuenta = buscarInfoCuenta($db,$cliente,$cuenta);

	  	$nombreCuenta = $dataCuenta["nombreCuenta"];
	  	$tipoCuenta   = $dataCuenta["tipoCuenta"];
	  	$movilAsignado = ($dataUsuAsignado["idNroTelefono"] == NULL) ? "": $dataUsuAsignado["idNroTelefono"];

	  	/*echo "<pre>";
	  	var_dump($dataCuenta);
	  	echo "</pre>";*/

	  	if ($dataUsuAsignado["tipoTrabajador"] == "Coordinador" OR $dataUsuAsignado["tipoTrabajador"] == "Conductor" ){
	  		$conductor = $dataUsuAsignado["idTrabajador"];
	  	} else if ( $dataUsuAsignado["tipoTrabajador"] == "Auxiliar"){
	  		$arrTripulacion[] = $dataUsuAsignado["idTrabajador"];
	  	}
	  	//Fin temporal tripulación
	  	//////////////////////////////

	  	$correlativo = generaCorrelDesp($db, $fechaDespacho);
	  	$idProgramacion = idNvoProgramDespacho($db, $fechaDespacho, $horaInicio, $placa, $cliente, $cuenta, $producto);

	  	//echo "correlativo, $correlativo, idProgramacion $idProgramacion";
	  	$nroAuxiliares = count($arrTripulacion);
  	  //echo "idProgramacion  $idProgramacion ";
  	  $cant = insertaDespacho($db, $fechaDespacho, $correlativo, $idProgramacion , $horaInicio, $placa, $cliente, $correlCuenta, $nombreCuenta, $tipoCuenta, $producto, $nroAuxiliares, '', '', $usuarioAsignado , $movilAsignado , $observacion, $usuario );
  	  //Por ahora no está en uso $aux = completaCamposDespacho($db,$idProgramacion);
	  	  //echo "paso02 $cant";
  	  if ($cant == 1){
  	    $cantProgram = insertaDespachoEnProgramDespacho($db, $idProgramacion ,$fechaDespacho, $horaInicio, $placa, $cliente, $cuenta, $producto);
  	  	$cantTotAux = 0;
  	  	if ($conductor != ""){
  	  	  $cantConductor = insertaTripulacion($db, $fechaDespacho, $correlativo,$conductor,"Conductor", $horaInicio, '00:00:00', $usuario);
  	  	}

  	  	//echo "cant conductor  $cantConductor";
  	  	foreach ($arrTripulacion as $key => $value) {
  	  		$cantAux = insertaTripulacion($db, $fechaDespacho, $correlativo, $value,"Auxiliar", $horaInicio, '00:00:00');
  	  		$cantTotAux = $cantTotAux + $cantAux;
  	  	}
  	  } else {
  	  	$idProgramacion = $correlativo = "";
  	  	$error .= "No se pudo crear el despacho.";
  	  }

  	  if ($cantConductor == 0){
  	  	$error .= "No se pudo crear el conductor.";
  	  }

  	  if ($cantTotAux != $nroAuxiliares){
  	  	$error .= "No se pudieron crear a todos los auxiliares";
  	  }

    	$arrData = array(
    		 "extId" => $idProgramacion,
         "correlativo" => $correlativo,
         "error" => $error,
      );

	  } else if ($accion == 'editar'){
   	  $verifEstado = verificaEstadoDespacho($db, $extId);
	 	  if ($verifEstado != 'Terminado'){

	 	  	/////////////////////////////////////////////////////////////
  	  	//Esta parte es temporal hasta que se decida como va el tema de la tripulación
  	  	/////////////////////////////////////////////////////////////
  	  	$dataUsuAsignado = buscarInfoAsignableDespacho($db,$usuarioAsignado);
  	  	//echo "Tipo Trabajador asignado ".$dataUsuAsignado["tipoTrabajador"];

  	  	if ($dataUsuAsignado["tipoTrabajador"] == "Coordinador" OR $dataUsuAsignado["tipoTrabajador"] == "Conductor" ){
  	  		$conductor = $dataUsuAsignado["idTrabajador"];
  	  	} else if ( $dataUsuAsignado["tipoTrabajador"] == "Auxiliar"){
  	  		$arrTripulacion[] = $dataUsuAsignado["idTrabajador"];
  	  	}

  	  	//Fin temporal tripulación
  	  	$movilAsignado = ($dataUsuAsignado["idNroTelefono"] == NULL) ? "": $dataUsuAsignado["idNroTelefono"];
        


	 	  	$rpta = modificaInicioDespacho($db,$extId, $cliente, $cuenta, $producto, $horaInicio, $movilAsignado, $placa, $estado, $observacion, $usuario);
	 	  	//echo "Respuesta $rpta";
	 	  	if ($rpta == 1){


	 	  		$cantActcProgramDespacho = actualizaProgramDespacho($db,$extId, $cliente, $cuenta, $producto, $horaInicio, $movilAsignado, $placa, $estado, $observacion, $usuarioAsignado, $usuario);
	 	  		$cantTripulacion = eliminaTripulacion($db, $fechaDespacho, $correlativo);
	 	  		$cantConductor   = insertaTripulacion($db, $fechaDespacho, $correlativo, $conductor, "Conductor", $horaInicio, '00:00:00');
     	  	//echo "cant conductor  $cantConductor";
       	  foreach ($arrTripulacion as $key => $value) {
   	      	$cantAux = insertaTripulacion($db, $fechaDespacho, $correlativo, $value, "Auxiliar", $horaInicio, '00:00:00');
   	      	$cantTotAux = $cantTotAux + $cantAux;
   	  	  }
	 	  		$arrData = array(
         		"extId" => $extId,
             "correlativo" => $correlativo,
             "error" => "",
           );
	 	  	} else {
	 	  		$arrData = array(
         		"extId" => $extId,
            "correlativo" => $correlativo,
            "error" => "Error. No se pudo editar el despacho"
          );
	  	  }
	  	} else {
	  	 	$arrRpta = array(
		   	  "extId" => $idProgramacion,
           "correlativo" => $correlativo,
           "error" => "El despacho no existe o ya tiene estado Terminado",
		   	);
  	  }
	  }
	  $cadjson = json_encode($arrData);

	  if ($accion == "crear"){
	    logCadenaEnviada($db,$cadena3,utf8_decode(serialize($arrData)), "Caso 330. Se crea un despacho", $usuario);
      
	  } else {
	  	logCadenaEnviada($db,$cadena3,utf8_decode(serialize($arrData)), "Caso 340. Se edita un despacho", $usuario);      
	  }

    print_r($cadjson);

  }

  function enviarInfoPuntosOrigen(){
	  require 'modelos/movilModelo.php';
	  $dataPuntosOrigen = buscarInfoPuntosOrigen($db);

    $arrData = array();
    //$cont = 1;
  	foreach ($dataPuntosOrigen AS $key => $item) {
	  	$idSede = $item['idSede'];
	  	$descripcion = $item['descripcion'];
	  	$idUbicacion = $item['idUbicacion'];
	  	$arrData[$idSede] = array(
			              "descripcion" => utf8_encode($descripcion),
			              "idUbicacion" => utf8_encode($idUbicacion),
			            );
  		//$cont++;
  	}

  	$cadjson = json_encode($arrData);

    print_r($cadjson);
  }

  function enviarUsuariosAAsignar() {
  	require 'modelos/movilModelo.php';
  	$dataTrabajadores = buscarInfoAsignableDespacho($db);

    $arrData = array();
    //$cont = 1;
  	foreach ($dataTrabajadores AS $key => $item) {
  		$idTrabajador = $item['idTrabajador'];
  		$apPaterno = $item['apPaterno'];
  		$apMaterno = $item['apMaterno'];
  		$nombres    = $item['nombres'];
  		$idUsuario    = $item['idUsuario'];
  		//echo "$idTrabajador $rznSocial  $nombres $cuentas ";
  		$arrData[$idTrabajador] = array(
			              "apPaterno" => utf8_encode($apPaterno),
			              "apMaterno" => utf8_encode($apMaterno),
			              "nombres" => utf8_encode($nombres),
			              "idUsuario" => utf8_encode($idUsuario)
			            );
  		//$cont++;
  	}

  	$cadjson = json_encode($arrData);
    print_r($cadjson);
  }

  function enviarProgramCoordinador(){
  	if (isset($_POST)){
  		$fch = Date("Y-m-d");
  		require 'modelos/movilModelo.php';
  		//print_r($_POST['cadjson2']);
  		$cadena2 = json_decode(utf8_decode($_POST['cadjson2']));
		  
  		/*echo "<pre>";
  		print_r($cadena2);
  		echo "</pre>";*/

  		$cadena3 = serialize($cadena2);
		  
  	  $cadError = "";
    	foreach ($cadena2 as $item => $valItem) {
    	  $$item = utf8_encode($valItem);
    	  //echo "ITEM $item, VALITEM $valItem";
    	}
  		logCadena($db,$cadena3,'Caso 320. Se solicita listado de despachos creados por el usuario', $coordinador);

    	$data = buscarProgramCoordinador($db,$coordinador,$fchIni,$fchFin);

    	//print_r($data);

    	$arrData = array();
      //$cont = 1;
    	foreach ($data AS $key => $item) {
    		$idProgramacion = $item['idProgramacion'];
    		$fchDespacho = $item['fchDespacho'];
    		$correlativo = $item['correlativo'];
    		$hraInicio   = $item['hraInicio'];
    		$idCliente   = $item['idCliente'];
    		$cuenta    = $item['cuenta'];
    		$correlCuenta  = $item['correlCuenta'];
    		$idProducto    = 1*$item['idProducto'];
    		$placa    = $item['placa'];
    		$estado    = $item['estadoDespacho'];
    		$movilAsignado    = $item['movilAsignado'];
    		$usuarioAsignado  = $item['usuarioAsignado'];
    		$tripulacion    = $item['tripulacion'];
    		$puntos    = $item['puntos'];
    		//$tipoTrabajador6    = $item['tipoTrabajador6'];
    		//$tipoTrabajador7    = $item['tipoTrabajador7'];

    		$arrAuxTrip = explode(",", $tripulacion);
    		$arrTripulacion = array();

    		foreach ($arrAuxTrip as $key => $value) {
    			$idTrab = strtok($value, ":");
    			$rol    = strtok(":");
    			$arrTripulacion[$idTrab] = $rol;
    			# code...
    		}

    		$arrAuxPuntos = explode(",", $puntos);
    		$arrPuntos = array();

    		if(count($arrAuxPuntos) == 0){

      		foreach ($arrAuxPuntos as $key => $value) {
      			$idPunto = strtok($value, ":");
      			$ordenPunto  = strtok(":");
      			$tipoPunto  = strtok(":");
      			$hraLlegada  = strtok(":");
      			$estado  = strtok(":");
      			$subEstado  = strtok(":");
      			$arrPuntos[] = array(
      				"idPunto" => utf8_encode($idPunto),
      				"ordenPunto" => utf8_encode($ordenPunto),
      				"tipoPunto" => utf8_encode($tipoPunto),
      				"hraLlegada" => utf8_encode($hraLlegada),
      				"estado" => utf8_encode($estado),
      				"subEstado" => utf8_encode($subEstado), 
      			);
      			# code...
      		}

    	  }

    	  $idProgramSinCeros = 1*$idProgramacion;
    	  $hraInicio = substr($hraInicio,0,5);


    		$arrData[$idProgramSinCeros] = array(
			              "fchDespacho" => utf8_encode($fchDespacho),
			              "correlativo" => utf8_encode($correlativo),
			              "hraInicio" => utf8_encode($hraInicio),
			              "idCliente" => utf8_encode($idCliente),
			              "cuenta" => utf8_encode($cuenta),
			              "correlCuenta" => utf8_encode($correlCuenta),
			              "idProducto" => utf8_encode($idProducto),
			              "placa" => utf8_encode($placa),
			              "estado" => utf8_encode($estado),
			              "movilAsignado" => utf8_encode($movilAsignado),
			              "usuarioAsignado" => utf8_encode($usuarioAsignado),
			              "tripulacion" => $arrTripulacion,
			              "puntos" => $arrPuntos
			              //"tipoTrabajador6" => utf8_encode($tipoTrabajador6)
			              //"tipoTrabajador7" => utf8_encode($tipoTrabajador7)
			            );
	  	//$cont++;
	    }
	    $cadjson = json_encode($arrData);
      print_r($cadjson);

	  }
  }

  function solicitarProgramacion(){
    if (isset($_POST)){
  		$fch = Date("Y-m-d");
  		require 'modelos/movilModelo.php';
  		//print_r($_POST['cadjson2']);
  		$cadena2 = json_decode(utf8_decode($_POST['cadjson2']));
		  
  		/*echo "<pre>";
  		print_r($cadena2);
  		echo "</pre>";*/

  		$cadena3 = serialize($cadena2);
		  
  	  $cadError = "";
    	foreach ($cadena2 as $item => $valItem) {
    	  $$item = utf8_encode($valItem);
    	  //echo "ITEM $item, VALITEM $valItem";
    	}
  		logCadena($db,$cadena3,'Caso 210. Se solicitan programaciones asignadas del día', $usuarioAsignado);

    	$data = buscarDataProgramada($db, $fchDespacho, $usuarioAsignado);

    	//print_r($data);

    	$arrData = array();
      //$cont = 1;
    	foreach ($data AS $key => $item) {
    		$idProgramacion = 1*$item['idProgramacion'];
    		$fchDespacho = $item['fchDespacho'];
    		$correlativo = $item['correlativo'];
    		$hraInicio    = $item['hraInicio'];
    		$idCliente    = $item['idCliente'];
    		$cuenta    = $item['cuenta'];
    		$correlCuenta  = $item['correlCuenta'];
    		$idProducto    = 1*$item['idProducto'];
    		$placa    = $item['placa'];
    		$movilAsignado = $item['movilAsignado'];
    		$tripulacion   = $item['tripulacion'];
    		$puntos    = $item['puntos'];
    		//$tipoTrabajador6    = $item['tipoTrabajador6'];
    		//$tipoTrabajador7    = $item['tipoTrabajador7'];

    		$arrAuxTrip = explode(",", $tripulacion);
    		$arrTripulacion = array();

    		foreach ($arrAuxTrip as $key => $value) {
    			$idTrab = strtok($value, ":");
    			$rol    = strtok(":");
    			$arrTripulacion[$idTrab] = $rol;
    			# code...
    		}

    		$arrAuxPuntos = explode(",", $puntos);
    		$arrPuntos = array();

    		if(count($arrAuxPuntos) == 0){

	    		foreach ($arrAuxPuntos as $key => $value) {
	    			$idPunto = strtok($value, "|");
	    			$ordenPunto  = strtok("|");
	    			$tipoPunto  = strtok("|");
	    			$hraLlegada  = strtok("|");
	    			$estado  = strtok("|");
	    			$subEstado  = strtok("|");
	    			$arrPuntos[] = array(
	    				"idPunto" => utf8_encode($idPunto),
	    				"ordenPunto" => utf8_encode($ordenPunto),
	    				"tipoPunto" => utf8_encode($tipoPunto),
	    				"hraLlegada" => utf8_encode($hraLlegada),
	    				"estado" => utf8_encode($estado),
	    				"subEstado" => utf8_encode($subEstado)
	    			);
	    			# code...
	    		}
    		}


    		$arrData[$idProgramacion] = array(
			              "fchDespacho" => utf8_encode($fchDespacho),
			              "correlativo" => utf8_encode($correlativo),
			              "hraInicio" => utf8_encode($hraInicio),
			              "idCliente" => utf8_encode($idCliente),
			              "cuenta" => utf8_encode($cuenta),
			              "correlCuenta" => utf8_encode($correlCuenta),
			              "idProducto" => utf8_encode($idProducto),
			              "placa" => utf8_encode($placa),
			              "movilAsignado" => utf8_encode($movilAsignado),
			              "tripulacion" => $arrTripulacion,
			              "puntos" => $arrPuntos
			              //"tipoTrabajador6" => utf8_encode($tipoTrabajador6)
			              //"tipoTrabajador7" => utf8_encode($tipoTrabajador7)
			            );
	  	//$cont++;
	    }
	    $cadjson = json_encode($arrData);
	    logCadenaEnviada($db,$cadena3,utf8_decode(serialize($arrData)), "Caso 210. Se solicitan programaciones asignadas del día", $usuarioAsignado);
      print_r($cadjson);

	  }
  }

  function enviarEstadoDespacho(){
  	if (isset($_POST)){
  		$fch = Date("Y-m-d");
  		require 'modelos/movilModelo.php';
  		//print_r($_POST['cadjson2']);
  		$cadena2 = json_decode(utf8_decode($_POST['cadjson2']));
  		/*echo "<pre>";
  		print_r($cadena2);
  		echo "</pre>";*/
  		//$cadena2 = json_decode(utf8_decode($_POST['cadjson2']));
  		$cadena3 = serialize($cadena2);
		  
  	  $cadError = "";
    	foreach ($cadena2 as $item => $valItem) {
    	  $$item = utf8_encode($valItem);
    	  //echo "ITEM $item, VALITEM $valItem";
    	}

    	$rpta = cambiarEstadoDespacho($db,$idProgramacion,$fechaDespacho, $correlativo, $estado);
    	if ($rpta == 1){
    	  $arrRpta = array(
    			"error" => "",
      		"success" => True,
      		"token" => "queva"
	      );
  	  } else {
    	  $arrRpta = array(
    			"error" => utf8_encode("No se modificó el estado"),
      		"success" => False,
      		"token" => "queva"
	      );
  	  }
    	$rpta = json_encode($arrRpta);
      print_r($rpta);      
	  }
  }

  function guardarPuntoEntrega(){
  	if (isset($_POST)){
  	  $fch = Date("Y-m-d");
  	  $rpta = 0;

  	  require 'modelos/movilModelo.php';
  	  $cadena2 = json_decode(utf8_decode($_POST['cadjson2']));
  	  //var_dump($cadena2);
  	  $cadena3 = serialize($cadena2);
      //$cadena4 = serialize($cadena2,true);	  
      //print_r($cadena2);
	    //echo "<br>";

      foreach ($cadena2 as $idProgramacion => $dataAux){
      	/*echo "<pre>";
      	print_r($dataAux);
        echo "</pre>";
        */
        //if (isset($_POST['txtEncab']) && $_POST['txtEncab'] != '') $encab = $_POST['txtEncab'];	
	    	foreach ($dataAux as $encabTipo => $data) {
	    		if ($encabTipo != 'accion'){
		    		foreach ($data as $item => $valItem) {
		    	    //echo "Item $item valor $valItem<br>";
		    	    $$item = utf8_decode($valItem);
		    		}
	    		} else {
	    			$$encabTipo = $data;
	    			//echo "Item $encabTipo valor $data<br>";
	    		}
	    	}
	    }

	    if(!isset($guiaMoy)) $guiaMoy = "";

	    if(!isset($ordenPunto)) $ordenPunto = "";
	    if(!isset($longitud)) $longitud = "";
	    if(!isset($latitud)) $latitud = "";

	    if($accion == "crear"){
	    	logCadena($db,$cadena3,'Caso 240. Se crea un punto');
	    } else {
	    	logCadena($db,$cadena3,'Caso 250. Se editan datos de un punto',$usuario);
	    }	
  	  //echo "idProgramacion $idProgramacion";
      $verifEstado = verificaEstadoDespacho($db,$idProgramacion);
      //echo "verif estado $verifEstado";

      if($accion == "crear"){
      	if($verifEstado == 'EnRuta' || $verifEstado == 'Programado'){
      		if ($idPunto == ""){
      			//echo "$idProgramacion,$nombreComprador,$distrito, $nroGuiaPorte, $estado, $nroGuiasCliente, $horaLlegada, $horaSalida, $observacion, $tipo";
      			$rpta = insertaPuntoEntrega($db,$idProgramacion,$nombreComprador,$distrito, $guiaCliente, $estado, $horaLlegada, $horaSalida, $observacion, $tipo, $guiaMoy);
      			if ($rpta != 0){
      				$idPunto = $rpta;
      				$statusCode = "200";
      				$success = True;
      				$token = "";
      				$error = "";
      			} else {
      				$idPunto = "";
      				$statusCode = "";
      				$success = False;
      				$token = "";
      				$error = "Error. No se realizó la inserción del punto";
      			}
      		} else {
      			$idPunto = "";
      		  $statusCode = "";
      			$success = False;
      			$token = "";
      			$error = "Error. El idPunto debe ir vacío";
      		}
      	} else {
      		$idPunto = "";
      		$statusCode = "";
      		$success = False;
      		$token = "";
      		$error = "Error. El estado no es EnRuta ni Programado";
      	}
      } else if ($accion == "editar"){
      	if ($idPunto == ""){
      		$idPunto = "";
      		$statusCode = "";
      		$success = False;
      		$token = "";
      		$error = "Error. el IdPunto no puede ir vacío en la acción editar";
      	} else if($verifEstado == 'Programado' && $estado != 'Pendiente'){
      		$idPunto = "";
      		$statusCode = "";
      		$success = False;
      		$token = "";
      		$error = "Error. No puede cambiar el estado si no está en ruta";
      	} else if ($verifEstado == 'Programado' || $verifEstado == 'EnRuta') {
      		$rpta = modificaPuntoEntrega($db, $idProgramacion, $idPunto, $nombreComprador, $distrito, $guiaCliente, $estado, $subestado, $horaLlegada, $horaSalida, $ordenPunto, $observacion, $tipo, $longitud, $latitud, $guiaMoy);
          if ($rpta == 1){

      			$statusCode = "200";
      			$success = True;
      			$token = "";
      			$error = "";
          } else {

          	$statusCode = "";
      			$success = False;
      			$token = "";
      			$error = "Advertencia. No ha habido ningún cambio";
          }


      	}	else {
      		$statusCode = "";
      		$success = False;
      		$token = "";
      		$error = "Error. Verifique el estado";
      	}
      } else if ($accion == 'eliminar'){
      	/*Aquí voy*/
      	if ($idPunto == ""){
      		$idPunto = "";
      		$statusCode = "";
      		$success = False;
      		$token = "";
      		$error = "Error. el IdPunto no puede ir vacío en la acción eliminar";
      	} else if ($verifEstado == 'Programado' || $verifEstado == 'EnRuta') {
      		$rpta = eliminaPuntoEntrega($db,$idProgramacion,$idPunto);
      		if ($rpta == 1){

      			$statusCode = "200";
      			$success = True;
      			$token = "";
      			$error = "";
          } else {

          	$statusCode = "";
      			$success = False;
      			$token = "";
      			$error = "Error. No ha eliminado el registro";
          }
      	} else {
      		$statusCode = "";
      		$success = False;
      		$token = "";
      		$error = "Error. Verifique el idPunto y el estado";
      	}
      } else {
      	$statusCode = "";
     		$success = False;
     		$token = "";
     		$error = "Error. Verifique el idPunto y el estado";
      }

      $arrRpta = array(
     				"idPunto" => $idPunto,
     				"statusCode" => $statusCode,
     				"success" => $success,
     				"token" => "",
     				"error" => utf8_encode($error)
   		);
   	} else {
  		$arrRpta = array(
  			"idRuta" => "",
  			"statusCode" => "",
  			"success" => False,
  			"token" => "",
  			"error" => "No hay datos que procesar"
			 );
	  }
   	$rpta = json_encode($arrRpta);

   	if($accion == "crear"){
	  	logCadenaEnviada($db,$cadena3,utf8_decode(serialize($arrRpta)), "Caso 240. Se crea un punto");
	  } else {
	  	logCadenaEnviada($db,$cadena3,utf8_decode(serialize($arrRpta)), "Caso 250. Se editan datos de un punto", $usuario);
	  }

	  print_r($rpta);
  }

  function enviarEstadosPuntos() {
   	require 'modelos/movilModelo.php'; 
    $dataEstadosPuntos = buscarEstadosPuntos($db);

    /*  echo "<pre>";
    var_dump($dataEstadosPuntos);
    echo "</pre>";
    */
    $arrData = array();
    $arrSubEstados = array();
    $estadoAnt = "";

    foreach ($dataEstadosPuntos AS $key => $item) {
    	//echo "ITEM". var_dump($item);
    	$estado = $item["estado"];
    	if ($estadoAnt != "" && $estadoAnt != $estado ){
    		//echo "ingreso";
    		$arrData[] = array(
    			              "estado" => utf8_encode($estadoAnt),
    			              "subestados" => $arrSubEstados
    		                  );
    		$arrSubEstados = array();
    		//var_dump($arrData);
    	}
    	$arrSubEstados[] = utf8_encode($item["subestado"]);
    	$estadoAnt = $estado;
    }
    if (count($arrSubEstados) > 0 ){
      $arrData[] = array(
    	              "estado" => utf8_encode($estadoAnt),
    	              "subestados" => $arrSubEstados
                );
    }
    //echo "Final";
    //var_dump($arrData);

  	$cadjson = json_encode($arrData);
    print_r($cadjson); 

  }


/*
  function guardarPuntoEntrega(){
  	if (isset($_POST)){
  	  $fch = Date("Y-m-d");
  	  $rpta = 0;
  	  require 'modelos/movilModelo.php';
  	  $cadena2 = json_decode(utf8_decode($_POST['cadjson2']));
  	  //var_dump($cadena2);
  	  $cadena3 = serialize($cadena2);
        //$cadena4 = serialize($cadena2,true);	  
      //  print_r($cadena2);
	  //  echo "<br>";
	    logCadena($db,$cadena3,'cadena recibida como punto de entrega');

      foreach ($cadena2 as $encab => $dataAux){
      //	echo "<pre>";
     // 	print_r($dataAux);
     //   echo "</pre>";
        if (isset($_POST['txtEncab']) && $_POST['txtEncab'] != '') $encab = $_POST['txtEncab'];	
	    	foreach ($dataAux as $encabTipo => $data) {
	    		if ($encabTipo != 'accion'){
		    		foreach ($data as $item => $valItem) {
		    	    //echo "Item $item valor $valItem<br>";
		    	    $$item = utf8_decode($valItem);
		    		}
	    		} else {
	    			$$encabTipo = $data;
	    			//echo "Item $encabTipo valor $data<br>";
	    		}
	    	}
	    }
  	  echo "encab $encab";
  	  if(strlen($encab) >= "12"){
  	  	$fchDespacho = substr($encab,0,10);
  	  	$correlativo = substr($encab,11,2);
  	  	//$idRuta = "";

        $verifEstado = verificaEstadoDespacho($db,$fchDespacho,$correlativo);
        //echo "PASO 01";
  	  	if ($verifEstado == 'EnRuta'){
  	  		//echo "PASO 02";

    	  	if ($idRuta == "" && $accion == 'crear' ){
    	  		//echo " $fchDespacho,$correlativo,$nombreComprador,$distrito, $nroGuiaPorte, $estado, $nroGuiasCliente, $horaLlegada, $horaSalida, $observacion ";
    	  	  $rpta = insertaPuntoEntrega($db,$fchDespacho,$correlativo,$nombreComprador,$distrito, $nroGuiaPorte, $estado, $nroGuiasCliente, $horaLlegada, $horaSalida, $observacion, $tipo);
    	  	  if ($rpta == 0) $rpta = 'Mal insercion'; else $idRuta = $rpta;
    	  	} else if ($accion == 'editar' ){
    	  		//echo "$fchDespacho,$correlativo,idRuta = $idRuta,$nombreComprador,$distrito, $nroGuiaPorte, $estado, $nroGuiasCliente, $horaLlegada, $observacion";
    	  	  $rpta = modificaPuntoEntrega($db,$fchDespacho,$correlativo,$idRuta,$nombreComprador,$distrito, $nroGuiaPorte, $estado, $subestado, $nroGuiasCliente, $horaLlegada, $horaSalida, $observacion);
              if ($rpta == 1) $rpta = 'Bien edicion'; else  $rpta = 'Mal edicion';
	      	} else if ($accion == 'eliminar')  {
	      	  $rpta = eliminaPuntoEntrega($db,$fchDespacho,$correlativo,$idRuta);
	      	  if ($rpta == 1) $rpta = 'Bien eliminacion'; else  $rpta = 'Mal eliminacion';
	      	}

    	  	if ($rpta == 'Mal insercion'){
    	    	$arrRpta = array(
      				"idRuta" => "",
      				"statusCode" => "",
      				"success" => False,
      				"token" => "",
      				"error" => utf8_encode("Error, no se insertó registro")
    				 );
    			  $rpta = json_encode($arrRpta);

    	    } else if ($rpta == 'Mal edicion'){
    	    	$arrRpta = array(
      				"idRuta" => "$idRuta",
      				"statusCode" => "",
      				"success" => False,
      				"token" => "",
      				"error" => utf8_encode("Error, no se editó registro")
    				 );
    			  $rpta = json_encode($arrRpta);

    	    } else if ($rpta == 'Mal eliminacion'){
    	    	$arrRpta = array(
      				"idRuta" => "",
	      			"statusCode" => "",
		      		"success" => False,
		      		"token" => "",
			      	"error" => utf8_encode("Error, no se eliminó registro")
			  	   );
			      $rpta = json_encode($arrRpta);
  	      } else {
  	      	$arrRpta = array(
	        		"idRuta" => "$idRuta",
    		  		"statusCode" => "200",
	    	  		"success" => True,
		      		"token" => "",
		      		"error" => utf8_encode("")
			    	 );
			      $rpta = json_encode($arrRpta);
	        }

    	  } else {
    	  	$arrRpta = array(
      			"idRuta" => "",
      			"statusCode" => "",
      			"success" => False,
      			"token" => "",
      		  "error" => utf8_encode("Error, no se puede editar este despacho")
      		);
  	    	$rpta = json_encode($arrRpta);
  	    } 
	    } else {
	    	$placa = $encab;
	    }
  	} else {
  		$arrRpta = array(
  			"idRuta" => "",
  			"statusCode" => "",
  			"success" => False,
  			"token" => "",
  			"error" => "No hay datos que guardar"
			 );
  		$rpta = json_encode($arrRpta);
	  }
	  print_r($rpta);
  }
*/



?>
