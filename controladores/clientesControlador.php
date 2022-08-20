<?php

//require 'controladores/trabajadoresControlador.inc';

function listar() {
	//Incluye el modelo que corresponde
	require 'modelos/clientesModelo.php';
	//Le pide al modelo todos los items
	$query = ' WHERE 1 ';
	if (isset($_POST['txtRuc']) and $_POST['txtRuc'] <>''){
    //$dni = substr($_POST['txtRuc'],0,11);
    $dni = substr($_POST['txtRuc'],0, strpos($_POST['txtRuc'], '-')  ); 
    //$query = $query ." And idRuc = '$dni'";
    $items = buscarCliente($db,$dni);
  } else
	  $items = buscarClientesActivos($db);

	//Pasa a la vista toda la información que se desea representar
	require 'vistas/listarClientes.php';
}

function vercliente(){
  $id = $_GET['id'];
  require 'modelos/clientesModelo.php';
  $cliente = buscarCliente($db,$id);
  $contactos = buscarClienteContactos($db,$id);
  //$cuentas = buscarClienteCuentas($db,$id);
  $ubicaciones = buscarCliUbicaciones($db,$id);
  $cuentas02 = buscarClientesCuentas02($db,$id);
  $superCuentas = buscarClientesSuperCuentas($db,$id);
  $condsPago = buscarCondicionesDePago($db,"Activo");	
  require 'vistas/verCliente.php';
}

function nuevocliente() {
  //$id = $_GET['id'];
  require 'modelos/clientesModelo.php';
  //$cliente = buscarCliente($db,$id);
  //$contactos = buscarClienteContactos($db,$id);
  //$cuentas = buscarClienteCuentas($db,$id);
  ///////$telfAsignados = buscarConductorTelefonos($db,$dni);
  require 'vistas/nuevoCliente.php';
}

function editacliente() {
  $id = $_GET['id'];
  require 'modelos/clientesModelo.php';
  //require 'modelos/aplicacionModelo.php';
  //$categorias = buscarCategorias($db);
  //$entidadPensiones = buscarEntidadPensiones($db);
  $cliente = buscarCliente($db,$id);
  require 'vistas/editaCliente.php';
}

function eliminacliente(){
  $id = $_GET['id'];
  require 'modelos/clientesModelo.php';
  $cliente = buscarCliente($db,$id);
  require 'vistas/eliminaCliente.htm';
}

function nuevocontacto() {
  $id = $_GET['id'];
  require 'vistas/nuevoContacto.htm';  
}

function editacontacto() {
  require 'modelos/clientesModelo.php';
  $id = $_GET['id'];
  $nombre = isset($_GET['nombre'])?$_GET['nombre']:$_POST['TxtNombre'];
  $datosContacto=buscarContacto($db,$nombre);
  
  //$cargo = isset($_GET['cargo'])?$_GET['cargo']:$_POST['TxtCargo'];
  //$telefono = $_GET['telefono'];
  //$email = $_GET['email'];
  require 'vistas/editaContacto.htm';  
}

function eliminacontacto() {
  $id = $_GET['id'];
  $nombre = $_GET['nombre'];
  require 'vistas/eliminaContacto.htm';  
}

function nuevacuenta(){
  require 'modelos/clientesModelo.php';
  $id = $_REQUEST['id'];
  require 'vistas/nuevaCuenta.php';  
}

function editacuenta() {
  $id = $_REQUEST['id'];
  $nombreServicio = $_REQUEST['tiposervicio'];
  require 'modelos/clientesModelo.php';
  $clienteCuenta = buscarclientecuenta($db,$id,$nombreServicio);
  require 'vistas/editaCuenta.htm';  
}

function eliminacuenta() {
  $id = $_REQUEST['id'];
  $tipoServicio = $_REQUEST['tiposervicio'];
  require 'modelos/clientesModelo.php';
  //$clienteCuenta = buscarclientecuenta($db,$id,$tipoServicio);
  require 'vistas/eliminaCuenta.htm';  
}

function nuevaubicacion(){
  require 'modelos/clientesModelo.php';
  $id = $_REQUEST['id'];
  require 'vistas/nuevaCliUbicacion.php';
}


function editaubicacion(){
  require 'modelos/clientesModelo.php';
  $auxId = $_REQUEST['id'];
  $idCliente = strtok($auxId, "-");
  $correlativo = strtok("-");
  $items = buscarCliUbicaciones($db,$idCliente,$correlativo);
  require 'vistas/editaCliUbicacion.php';

}

function eliminaubicacion(){
  require 'modelos/clientesModelo.php';
  $auxId = $_REQUEST['id'];
  $idCliente = strtok($auxId, "-");
  $correlativo = strtok("-");
  $items = buscarCliUbicaciones($db,$idCliente,$correlativo);
  require 'vistas/eliminaCliUbicacion.php';
}



function ingresecuentaripley(){
  require 'modelos/clientesModelo.php';
  $ripleyCuentas= buscarClienteCuentas($db,'20337564373');
  require 'vistas/elijacuentaripley.htm';
}

function reportediarioripley(){
  $auxCuenta = $_POST['cmbCuenta'];
  //$cuenta = $_POST['cmbCuenta'];
  $cuenta = strtok($auxCuenta,":");
  $tipoCuentaPago = strtok(":");
  require 'modelos/clientesModelo.php';
  $ripleyCuentas= buscarClienteCuentas($db,'20337564373');
  $ripleySucursales = buscarSucursalesRipley($db);
  $items = buscarDespachosRipleyNoVerificados($db,$cuenta);
  require 'vistas/listarDespachosRipley.php';
}

function liquidacionripley(){
  $cuenta = $_POST['cmbCuenta'];
  require 'modelos/clientesModelo.php';
  $ripleyCuentas= buscarClienteCuentas($db,'20337564373');
  $ripleySucursales = buscarSucursalesRipley($db);
  $items = buscarDespachosRipleyNoVerificados($db,$cuenta);
  require 'vistas/listarDespachosRipley.php';
}

function reporteliquidarripley(){
  $cuenta = $_POST['cmbCuenta'];
  $fchIni = $_POST['txtFchIni'];
  $fchFin = $_POST['txtFchFin'];
  require 'modelos/clientesModelo.php';
  $ripleyCuentas= buscarClienteCuentas($db,'20337564373');
  $ripleySucursales = buscarSucursalesRipley($db);
  $items = buscarDespachosRipleyParaLiquidar($db,$cuenta,$fchIni,$fchFin);
  require 'vistas/listarDespachosRipleyParaLiquidar.php';
}
 
function ajustarripley(){
  require 'modelos/clientesModelo.php';
  $ripleyDespachosAjustar= buscarDespachosRipleyAjustar($db);
  require 'vistas/ajustarDespachosRipley.htm';
}

function otros(){//no estoy seguro si debe ir aquí
  require 'modelos/clientesModelo.php';
  require 'modelos/trabajadoresModelo.php';
  //$items = buscarTodosLosClientes($db);
  $items = buscarClientesActivos($db);
  $trabajadores = buscarTodosLosTrabajadores($db);
  $terceros = buscarTodosLosTerceros($db);
  require 'vistas/verOtros.htm';
}

function cobrar(){
  require 'librerias/config.ini.php';
  require 'modelos/despachosModelo.php';
  require 'modelos/clientesModelo.php';
  require 'modelos/zonasModelo.php';
  if (isset($_POST['cmbCliente'])) $cliente = $_POST['cmbCliente']; else $cliente = $_GET['idCliente'];
  $_SESSION['idCliente'] = $cliente;
  if (isset($_POST['cmbEstado'])) $estado = $_POST['cmbEstado']; else $estado = 'EmitidaLiq';
  if (isset($_POST['opcion'])){
    $valor = $_POST['opcion'];
    if ($_POST['opcion'] == 'Md'){    
      $nroRegDespachos = $_POST['cantPendiente'];
      for ($i = 0; $i < $nroRegDespachos; $i++ ){
        if(isset($_POST["item".$i])  &&   $_POST["item".$i] == 'ok' ){
          $fchDespacho =  $_POST["fchDespacho".$i];
          $correlativo =  $_POST["correlativo".$i];
          $codigo = $_POST["codigo".$i];
          procesarMarcaCobrarDespacho($db,$fchDespacho,$correlativo,$codigo,'Md');
        }     
      }
      $url= "index.php?controlador=clientes&accion=cobrar&idCliente=".$cliente ;//Esto es para que
      require 'vistas/redireccionar.php';                             //Cuando refresque la página no se vuelva a ejecutar
    } else if ($_POST['opcion']== 'No'){    
      $nroRegDespachos = $_POST['cantMd'];
      for ($i = 0; $i < $nroRegDespachos; $i++ ){
        if(isset($_POST["itemMd".$i])  &&   $_POST["itemMd".$i] == 'ok' ){
          $fchDespacho =  $_POST["fchDespMd".$i];
          $correlativo =  $_POST["correlMd".$i];
          $codigo = $_POST["codigoMd".$i];
          procesarMarcaCobrarDespacho($db,$fchDespacho,$correlativo,$codigo,'No');
        }     
      }
      $url= "index.php?controlador=clientes&accion=cobrar&idCliente=".$cliente ;
      require 'vistas/redireccionar.php';
    } else if ($_POST['opcion']== 'nuevaFactCli'){
      $nroFactura = $_POST['txtNroFactCli'];
      $fchFactura = $_POST['txtFchDocFactCli'];
      $monto      = $_POST['txtMontoFactCli'];
      $observ     = $_POST['txtObservFactCli'];

      $cant = insertaNuevaFactCli($db,$cliente,$nroFactura,$fchFactura, $monto, $observ);

      
      $url= "index.php?controlador=clientes&accion=cobrar&idCliente=".$cliente ;
      require 'vistas/redireccionar.php';



    }
  }
  $tipoReg =(!isset($_POST['cmbTipoReg'])||$_POST['cmbTipoReg']== '-')?NULL:$_POST['cmbTipoReg'];
  $regTipoDoc=(!isset($_POST['cmbRegTipoDoc'])||$_POST['cmbRegTipoDoc']== '-')?NULL:$_POST['cmbRegTipoDoc'];
  $estadoDoc = (!isset($_POST['cmbEstadoDoc'])||$_POST['cmbEstadoDoc']== '-')?NULL:$_POST['cmbEstadoDoc'];
  $bsqNroDoc = (!isset($_POST['txtRegNroDoc'])||$_POST['txtRegNroDoc']== '')?NULL:$_POST['txtRegNroDoc'];
  $bsqPlaca = (!isset($_POST['txtBsqPlaca'])||$_POST['txtBsqPlaca']== '')?NULL:$_POST['txtBsqPlaca'];
  $bsqCuenta = (!isset($_POST['txtBsqCuenta'])||$_POST['txtBsqCuenta']== '')?NULL:$_POST['txtBsqCuenta'];
  $bsqMonto = (!isset($_POST['txtBsqMonto'])||$_POST['txtBsqMonto']== '')?NULL:$_POST['txtBsqMonto'];

  $bsqDocCobranza = (!isset($_POST['txtCobrNro'])||$_POST['txtCobrNro']== '')?NULL:$_POST['txtCobrNro'];
  $bsqDocCobranzaMonto = (!isset($_POST['txtCobrMonto'])||$_POST['txtCobrMonto']== '')?NULL:$_POST['txtCobrMonto'];
  $bsqDocNroPreliquid = (!isset($_POST['txtCobrNroPreliquid'])||$_POST['txtCobrNroPreliquid']== '')?NULL:$_POST['txtCobrNroPreliquid'];

  /*if(isset($_GET['operacion']) && $_GET['operacion']=='confirmar' ){
    $nroDoc  = $_GET['nrodoc'];
    $tipoDoc = $_GET['tipodoc'];
    $aux = confirmarOperacion($db,$nroDoc,$tipoDoc);
  }*/
  $datosCliente = buscarCliente($db,$cliente);
  $fchIni = isset($_POST['txtFchIni'])?$_POST['txtFchIni']:NULL;
  $fchFin = isset($_POST['txtFchFin'])?$_POST['txtFchFin']:NULL;
  $items = cobrosDespachosPendientes($db,$cliente,$fchIni,$fchFin,$tipoReg,$regTipoDoc,$estadoDoc,$bsqNroDoc,$bsqPlaca,$bsqCuenta,$bsqMonto);
  $nroItems = count($items);
  $tipoRegMd =(!isset($_POST['cmbTipoRegMd'])||$_POST['cmbTipoRegMd']== '-')?NULL:$_POST['cmbTipoRegMd'];
  $bsqPlacaMd = (!isset($_POST['txtBsqPlacaMd'])||$_POST['txtBsqPlacaMd']== '')?NULL:$_POST['txtBsqPlacaMd'];
  $bsqCuentaMd=(!isset($_POST['txtBsqCuentaMd'])||$_POST['txtBsqCuentaMd']== '')?NULL:$_POST['txtBsqCuentaMd'];
  $bsqMontoMd = (!isset($_POST['txtBsqMontoMd'])||$_POST['txtBsqMontoMd']== '')?NULL:$_POST['txtBsqMontoMd'];
  $fchIniMd = isset($_POST['txtFchIniMd'])?$_POST['txtFchIniMd']:NULL;
  $fchFinMd = isset($_POST['txtFchFinMd'])?$_POST['txtFchFinMd']:NULL;
  $itemsMd = cobrosDespachosMarcados($db,$cliente,$fchIniMd,$fchFinMd,$tipoRegMd,$bsqPlacaMd,$bsqCuentaMd,$bsqMontoMd);
  $nroItemsMd = count($itemsMd);
  $docsCobranza = cobrosDocumentosCobranza($db,$cliente,$estado,$bsqDocCobranza,NULL,$bsqDocCobranzaMonto, $bsqDocNroPreliquid);
  $dataFactCliente = buscarFactCliente($db,$cliente);
  $nroFactCli = count($dataFactCliente);
  $ptosOrigen = buscarSedes($db);
  $distritos = buscarUbicaciones($db);
  $zonas = buscarUbiDetalles($db);

  $bsqNroFactCli = $bsqNroOperacion = NULL;
  /*echo "<pre>";
  print_r($ptosOrigen);
  print_r($distritos);
  print_r($zonas);
  echo "</pre>";
  */
  require 'vistas/cobrar.php';
}

function editaopcionesdocumento(){
  require 'modelos/despachosModelo.php';
  $nroDoc  = isset($_GET['nrodoc'])?$_GET['nrodoc']:$_POST['nrodoc'];
  $tipoDoc = isset($_GET['tipodoc'])?$_GET['tipodoc']:$_POST['tipodoc'];
  $docCobranza = cobroDocumentocobranza($db,$nroDoc,$tipoDoc);
  require 'vistas/editaDocumentoCobranza.htm';
}

function eliminadoccobranza(){
  require 'modelos/clientesModelo.php';
  $nroDoc = $_GET['nrodoc'];
  $tipoDoc = $_GET['tipodoc'];
  require 'vistas/eliminaDocCobranza.htm';
}

function nuevanota(){
  require 'modelos/clientesModelo.php';
  if (isset($_POST['nrodoc'])) $nroDoc = $_POST['nrodoc']; else $nroDoc = $_GET['nrodoc'];
  if (isset($_POST['cliente'])) $cliente = $_POST['cliente']; else $cliente = $_GET['cliente'];
  
  //if (isset($_POST['tipodoc'])) $tipoDoc = $_POST['tipodoc']; else $tipoDoc = $_GET['tipodoc'];  
  require 'vistas/nuevaNota.htm';
}

function eliminanota(){
  require 'modelos/clientesModelo.php';
  if (isset($_POST['nrodoc'])) $nroDoc = $_POST['nrodoc']; else $nroDoc = $_GET['nrodoc'];
  if (isset($_POST['tipodoc'])) $tipoDoc = $_POST['tipodoc']; else $tipoDoc = $_GET['tipodoc'];  
  require 'vistas/eliminaNota.htm';
}

function verpagotercero(){
  require 'modelos/clientesModelo.php';
  require 'modelos/trabajadoresModelo.php';//solo para usar el logAccion
  require 'modelos/tercerosModelo.php';
  require 'modelos/zonasModelo.php';

  $auxNroDoc = isset($_POST['cmbTerceros'])?$_POST['cmbTerceros']:$_GET['cmbTerceros'];
  $nroDoc = strtok($auxNroDoc,':');
  $nombreCompleto = strtok(':');
  $terceros = buscarTodosLosTerceros($db, 'Activo');
  //echo "Nro Doc  $nroDoc";
  
  if (isset($_POST['opcion'])||isset($_GET['opcion']) ){
    $valor = isset($_POST['opcion'])?$_POST['opcion']:$_GET['opcion'];
    //echo "VALOR $valor";
    if ($valor == 'Md' || $valor == 'No' ){ //esto es si desea procesar despachos
      $nroRegDespachos = ($valor == 'Md')?$_POST['nroRegDespachos']:$_POST['nroRegDespMarcados'];
      for ($i = 0; $i < $nroRegDespachos; $i++ ){
        if(isset($_POST["item".$i])  &&   $_POST["item".$i] == 'ok' ){
          if ($valor == 'Md'){
            $fchDespacho =  $_POST["fchDespacho".$i];
            $correlativo =  $_POST["correlativo".$i];
            $placa = $_POST["placa".$i];
          } else {
            $fchDespacho =  $_POST["fchDespM".$i];
            $correlativo =  $_POST["correlatM".$i];
            $placa = $_POST["placaM".$i];
          }     
          procesarMarcas($db,$fchDespacho,$correlativo,$valor,$placa,$nroDoc);
        }
      }  
    } else if ($valor == '0' || $valor == 'Null' ){ //aqui se procesan abastecimientos
      $nroRegAbast = ($valor == '0')?$_POST['nroRegAbast']:$_POST['nroRegAbastMd'];
      if ($valor == 'Null') $valor = null;
      for ($i = 0; $i < $nroRegAbast; $i++ ){
        if(isset($_POST["itemAb".$i])  &&   $_POST["itemAb".$i] == 'ok' ){
          if ($valor == '0'){
            $fchCreacion =  $_POST["fchCreacAb".$i];
            $hraCreacion =  $_POST["hraCreacAb".$i];
            $placa = $_POST["placaAb".$i];
          } else {
            $fchCreacion =  $_POST["fchCreacMdAb".$i];
            $hraCreacion =  $_POST["hraCreacMdAb".$i];
            $placa = $_POST["placaMdAb".$i];
          }
          procesarMarcasAbastecimiento($db,$fchCreacion,$hraCreacion,$valor,$placa);                
        }
      }    
    } else if ($valor == '0Ot' || $valor == 'NullOt' ){ //aqui se procesan otros
      if ($valor == '0Ot') $valor = '0'; else $valor = null;
      $nroRegOt = ($valor == '0')?$_POST['nroRegOtros']:$_POST['nroRegOtrosMd'];
      for ($i = 0; $i < $nroRegOt; $i++ ){
        if(isset($_POST["itemOt".$i])  &&   $_POST["itemOt".$i] == 'ok' ){
          if ($valor == '0'){
            $nroOrden =  $_POST["nroOt".$i];
            $correlativo =  $_POST["corrOt".$i];
          } else {
            $nroOrden =  $_POST["nroMdOt".$i];
            $correlativo =  $_POST["corrMdOt".$i];
          }
          //procesarMarcasCobrarOtros($db,$fchEvento,$grupo,$subGrupo,$tipoDoc,$nroDocOt,$valor);//hay que revisar el tema de la marca
          procesarMarcasCobrarOtros($db,$nroOrden,$correlativo,$valor);
        }
      }    
    } else if ($valor == '0Misc' || $valor == 'NullMisc' ){ //aqui se procesan misc
      if ($valor == '0Misc') $valor = '0'; else $valor = null;
      $nroRegMisc = ($valor == '0')?$_POST['nroRegMisc']:$_POST['nroRegMiscMd'];
      for ($i = 0; $i < $nroRegMisc; $i++ ){
        if(isset($_POST["itemMisc".$i])  &&   $_POST["itemMisc".$i] == 'ok' ){
          if ($valor == '0'){
            $id =  $_POST["idMisc".$i];
          } else {
            $id =  $_POST["idMdMisc".$i];
          }
          procesarMarcasMisc($db,$id,$valor);
        }
      }    
    } else if ($valor == 'MdPersMoy' || $valor == 'NoPersMoy' ){ //aqui se procesan abastecimientos
      if ($valor == 'MdPersMoy') $valor = 'Md'; else $valor = 'No';
      $nroReg = ($valor == 'Md')?$_POST['nroRegPersMoy']:$_POST['nroRegPersMoyMd'];
      for ($i = 0; $i < $nroReg; $i++ ){
        if(isset($_POST["itemPer".$i])  &&   $_POST["itemPer".$i] == 'ok' ){
          if ($valor == 'Md'){
            $fchDespacho = $_POST["moyFchDespacho".$i];
            $correlativo = $_POST["moyCorrelativo".$i];
            $corrOcurrencia = $_POST["moyCorrOcurrencia".$i];
          } else {
            $fchDespacho = $_POST["moyMdFchDespacho".$i];
            $correlativo = $_POST["moyMdCorrelativo".$i];
            $corrOcurrencia = $_POST["moyMdCorrOcurrencia".$i];
          }

          //echo "$fchDespacho,$correlativo,$corrOcurrencia";
          //procesarMarcasPersonalMoy($db,$fchDespacho,$correlativo,$descripcion,$nroDoc,$valor);//hay que revisar el tema de la marca
          procesarMarcasOcurrenciaMoy($db,$fchDespacho,$correlativo,$corrOcurrencia,$valor);

        }
      }    
    } else if ($valor == 'eliminaliq'){
      //echo "Si ha ingresado a eliminar";
      $docLiq = $_GET['docliq'];
      eliminaLiquidacion($db,$docLiq);
      logAccion($db,"Se solicito liberar liquidación $docLiq",null,null);
    } 
  }

  if (isset($_POST['txtIdDocumento']) && (isset($_POST['opcion']) && $_POST['opcion'] == '')  ){
    //  echo "Si ha ingresado a registrar";
    $idDocumento = $_POST['txtIdDocumento']; 
    $tipoDocLiq = isset($_POST['cmbTipoDocLiq'])?$_POST['cmbTipoDocLiq']:NULL;
    $nroDocLiq = isset($_POST['txtNroDocumLiq'])?$_POST['txtNroDocumLiq']:NULL;   
    $fchDocLiq = isset($_POST['txtFchDocumLiq'])?$_POST['txtFchDocumLiq']:NULL;

    
    $tipoDocAnexo = isset($_POST['cmbAnexo'])?$_POST['cmbAnexo']:"-";
    if ($tipoDocAnexo != "-") $idDocAnexo = isset($_POST['txtNroDocAnexo'])?$_POST['txtNroDocAnexo']:NULL;
    else {
      $tipoDocAnexo = $idDocAnexo = NULL;
    }


    $resultado = procesarGenerarDocumento($db, $nroDoc, $idDocumento, $tipoDocLiq, $nroDocLiq, $fchDocLiq, $tipoDocAnexo, $idDocAnexo);
    if($resultado == 0 ){  
      echo "<B>  NO SE HA CREADO NINGÚN DOCUMENTO </B> ";
    } 
  }
  $placa = $cliente = $ctoDia = $codDespacho = $conductor = $fchAb = $placaAb = $condAb = $grifoAb = $auxCliente = $auxConductor = $fchAb = $placaAb = $auxConductor = $grifoAb = null;
  $placaMd = $clienteMd = $ctoDiaMd = $codDespachoMd = $conductorMd = $fchAbMd = $placaAbMd = $condAbMd = $grifoAbMd = $auxClienteMd = $auxCondMd = $fchAbMd = $placaAbMd = $auxCondAbMd = $grifoAbMd = null;  
  $fchOt = $placaOt = $tipoOt = $nroDocOt = $fchOtMd = $placaOtMd = $tipoOtMd = $nroDocOtMd = null;
  $fchMisc = $placaMisc = $idMisc = $fchMiscMd = $placaMiscMd = $idMiscMd = $documPago = $montoOt =  $montoOtMd =  $montoMisc =  $montoMiscMd = null;
  $tipoMisc = $tipoMiscMd = $bsqNroDocLiq = null;

  if (isset($_POST['buscar']) && $_POST['buscar']=='Si') {
    $placa = ($_POST['txtBsqPlaca'] != "" )?$_POST['txtBsqPlaca']:null;
    $codDespacho = ($_POST['txtBsqCodigo'] != "" )?$_POST['txtBsqCodigo']:null;
    if($_POST['txtBsqCliente'] != "" ){
      $auxCliente = $_POST['txtBsqCliente'];
      $arrayCliente = explode("-",$auxCliente);
      $cliente = $arrayCliente[0];
    } else
      $cliente = null;                          
    /*if($_POST['txtBsqConductor'] != "" ){
      $auxConductor = $_POST['txtBsqConductor'];
      $arrayConductor = explode("-",$auxConductor);
      $conductor = $arrayConductor[0];
    } else*/
      $conductor = null;
    $ctoDia = ($_POST['txtBsqCtoDia'] != "" )?$_POST['txtBsqCtoDia']:null;
    
    //busquedas de abastecimiento
    $fchAb = ($_POST['txtBsqFchAb'] != "" )?$_POST['txtBsqFchAb']:null;
    $placaAb = ($_POST['txtBsqPlacaAb'] != "" )?$_POST['txtBsqPlacaAb']:null;
    if($_POST['txtBsqCondAb'] != "" ){
      $auxConductor = $_POST['txtBsqCondAb'];
      $arrayConductor = explode("-",$_POST['txtBsqCondAb']);
      $condAb = $arrayConductor[1];
      //echo "Conductor".$arrayConductor[1];
    } else
    $condAb = null;
    $grifoAb = ($_POST['txtBsqGrifoAb'] != "" )?$_POST['txtBsqGrifoAb']:null;
    
    //Para los ya marcados. nuevo 130510
    $placaMd = ($_POST['txtBsqPlacaMd'] != "" )?$_POST['txtBsqPlacaMd']:null;
    $codDespachoMd = ($_POST['txtBsqCodMd'] != "" )?$_POST['txtBsqCodMd']:null;
    if($_POST['txtBsqCliMd'] != "" ){
      $auxClienteMd = $_POST['txtBsqCliMd'];
      $arrayClienteMd = explode("-",$auxClienteMd);
      $clienteMd = $arrayClienteMd[0];
    } else
      $clienteMd = null;
      
    /*if($_POST['txtBsqCondMd'] != "" ){
      $auxCondMd = $_POST['txtBsqCondMd'];
      $arrayConductorMd = explode("-",$auxCondMd);
      $conductorMd = $arrayConductorMd[0];
    } else*/
      $conductorMd = null;  
         
    $ctoDiaMd = ($_POST['txtBsqCtoDiaMd'] != "" )?$_POST['txtBsqCtoDiaMd']:null;
    //Para los abastecimientos marcados. Nuevo 130510
    $fchAbMd = ($_POST['txtBsqFchAbMd'] != "" )?$_POST['txtBsqFchAbMd']:null;
    $placaAbMd = ($_POST['txtBsqPlacaAbMd'] != "" )?$_POST['txtBsqPlacaAbMd']:null;
    if($_POST['txtBsqCondAbMd'] != "" ){
      $auxCondAbMd = $_POST['txtBsqCondAbMd'];
      $arrayConductorMd = explode("-",$_POST['txtBsqCondAbMd']);
      $condAbMd = $arrayConductorMd[1];
      //echo "Conductor".$arrayConductor[1];
    } else
      $condAbMd = null;
    $grifoAbMd = ($_POST['txtBsqGrifoAbMd'] != "" )?$_POST['txtBsqGrifoAbMd']:null;      

    //Inicio agregado 130621
    $fchOt = ($_POST['txtBsqFchOt'] != "" )?$_POST['txtBsqFchOt']:null;
    $placaOt = ($_POST['txtBsqPlacaOt'] != "" )?$_POST['txtBsqPlacaOt']:null;
    $tipoOt = ($_POST['txtBsqTipoOt'] != "" )?$_POST['txtBsqTipoOt']:null;
    $nroDocOt = ($_POST['txtBsqNroDocPend'] != "" )?$_POST['txtBsqNroDocPend']:null;
    $montoOt = ($_POST['txtBsqMontoOt'] != "" )?$_POST['txtBsqMontoOt']:null;

    $fchOtMd = ($_POST['txtBsqFchOtMd'] != "" )?$_POST['txtBsqFchOtMd']:null;
    $placaOtMd = ($_POST['txtBsqPlacaOtMd'] != "" )?$_POST['txtBsqPlacaOtMd']:null;
    $tipoOtMd = ($_POST['txtBsqTipoOtMd'] != "" )?$_POST['txtBsqTipoOtMd']:null;
    $nroDocOtMd = ($_POST['txtBsqNroDocOtMd'] != "" )?$_POST['txtBsqNroDocOtMd']:null;
    $montoOtMd = ($_POST['txtBsqMontoOtMd'] != "" )?$_POST['txtBsqMontoOtMd']:null;

    $fchMisc = ($_POST['txtBsqFchMisc'] != "" )?$_POST['txtBsqFchMisc']:null;
    $placaMisc = ($_POST['txtBsqPlacaMisc'] != "" )?$_POST['txtBsqPlacaMisc']:null;
    $idMisc = ($_POST['txtBsqIdMisc'] != "" )?$_POST['txtBsqIdMisc']:null;
    $tipoMisc = ($_POST['txtBsqTipoMisc'] != "" )?$_POST['txtBsqTipoMisc']:null;
    $montoMisc = ($_POST['txtBsqMontoMisc'] != "" )?$_POST['txtBsqMontoMisc']:null;

    $fchMiscMd = ($_POST['txtBsqFchMiscMd'] != "" )?$_POST['txtBsqFchMiscMd']:null;
    $placaMiscMd = ($_POST['txtBsqPlacaMiscMd'] != "" )?$_POST['txtBsqPlacaMiscMd']:null;
    $idMiscMd = ($_POST['txtBsqIdMiscMd'] != "" )?$_POST['txtBsqIdMiscMd']:null;
    $tipoMiscMd = ($_POST['txtBsqTipoMiscMd'] != "" )?$_POST['txtBsqTipoMiscMd']:null;
    $montoMiscMd = ($_POST['txtBsqMontoMiscMd'] != "" )?$_POST['txtBsqMontoMiscMd']:null;

    $documPago = ($_POST['txtBsqDocumPago'] != "" )?$_POST['txtBsqDocumPago']:null;
    $bsqNroDocLiq = ($_POST['txtBsqNroDoc'] != "" )?$_POST['txtBsqNroDoc']:null;
  }
 
  $items = buscarPagosPendientesTerceros($db,$nroDoc,'No',$placa,$codDespacho,$cliente,$conductor,$ctoDia);
  $nroItems = count($items);
  $itemsMarcados = buscarPagosPendientesTerceros($db,$nroDoc,'Md',$placaMd,$codDespachoMd,$clienteMd,$conductorMd,$ctoDiaMd);
  $nroItemsMarcados = count($itemsMarcados);
  $abastecimientosSinCobrarTerceros = buscarAbastecimientosCobrarTerceros($db,$nroDoc,NULL,$fchAb,$placaAb,$condAb, $grifoAb);
  $nroAbastSinCob = count($abastecimientosSinCobrarTerceros);
  $abastecimientosMdCobrarTerceros =  buscarAbastecimientosCobrarTerceros($db,$nroDoc,'0',$fchAbMd,$placaAbMd,$condAbMd, $grifoAbMd);
  $nroAbastMdCob = count($abastecimientosMdCobrarTerceros);
  $otrosSinCobrarTerceros = buscarOtrosCobrarTerceros($db,$nroDoc,NULL,$fchOt,$placaOt,$tipoOt,$nroDocOt,$montoOt);
  $nroOtros =  count($otrosSinCobrarTerceros);
  $otrosMdCobrarTerceros =  buscarOtrosCobrarTerceros($db,$nroDoc,'0',$fchOtMd,$placaOtMd,$tipoOtMd,$nroDocOtMd,$montoOtMd);
  $nroOtrosMd = count($otrosMdCobrarTerceros);

  $miscSinCobrarTerceros = buscarMiscCobrarTerceros($db,$nroDoc,NULL,$fchMisc,$placaMisc,$idMisc,$montoMisc,$tipoMisc);
  $nroMisc =  count($miscSinCobrarTerceros);
  $miscMdCobrarTerceros =  buscarMiscCobrarTerceros($db,$nroDoc,'0',$fchMiscMd,$placaMiscMd,$idMiscMd,$montoMiscMd,$tipoMiscMd);
  $nroMiscMd = count($miscMdCobrarTerceros);


  $distritos = buscarUbicaciones($db);
  $zonas = buscarUbiDetalles($db);

  $arrDistritos = $arrZonas = array();
  foreach ($distritos as $key => $value) {
    $idUbicacion = $value['idUbicacion'];
    $arrDistritos[$idUbicacion] = $value['descripcion'];
  }

  foreach ($zonas as $key => $value) {
    $idDetalle = $value['idDetalle'];
    $arrZonas[$idDetalle] = $value['descripcion'];
  }

  $conPersonalMoy = buscarTerceroConPersonalMoy($db,$nroDoc,'No');
  $nroPersonalMoy = count($conPersonalMoy);
  $conPersonalMoyMd = buscarTerceroConPersonalMoy($db,$nroDoc,'Md');
  $nroPersonalMoyMd = count($conPersonalMoyMd);
  $docsPagoTerceros =buscarDocsPagosTerceros($db,$nroDoc,$documPago, $bsqNroDocLiq);
  $auxNroUltPagoTercero = buscarUltimoCodigo($db);
  $nroSgte = '00001';
  foreach($auxNroUltPagoTercero as $itemUltPago) {
    $aux = $itemUltPago['nro'];
    $nroSgte = 1*$aux + 1;
    $nroSgte = substr('0000'.$nroSgte,-5);
  }
  $nroSgte = date("Y").'-'.$nroSgte;
  require 'vistas/listarPagoTerceros.php';
}

function verpagotercero2(){
  require 'librerias/config.ini.php';
  require 'modelos/clientesModelo.php';
  require 'modelos/tercerosModelo.php';

  $auxNroDoc = isset($_POST['cmbTerceros2'])?$_POST['cmbTerceros2']:$_GET['cmbTerceros2'];
  $nroDoc = strtok($auxNroDoc,'-');
  $tipoDoc = strtok('-');
  $auxNroUltPagoTercero = buscarUltimoCodigo($db);
  $nroSgte = '00001';
  foreach($auxNroUltPagoTercero as $itemUltPago) {
    $aux = $itemUltPago['nro'];
    $nroSgte = 1*$aux + 1;
    $nroSgte = substr('0000'.$nroSgte,-5);
  }
  $nroSgte = date("Y").'-'.$nroSgte;
  $tipoOcurrencia = buscarTipoOcurrenciasTercero($db,NULL,'Activo');
  require 'vistas/listarPagoTerceros2.php';
}

function eliminardocpagotercero(){
  $docPagoTercero = $_GET['nrodoc'];
  require 'vistas/eliminaDocPagoTercero.htm';
}


//ME PARECE QUE ESTE NO SE UTILIZA
//*******************************
function cobrarimprime(){
  require('../fpdf16/fpdf.php');
  //$rznSocial = "Razon Social";
  //direccion = "Direccion";
  //$distrito = "Distrito";
  require 'modelos/despachosModelo.php';
  require 'modelos/clientesModelo.php';
  $cliente = $_POST['cmbCliente'];
  $datosCliente = buscarCliente($db,$cliente);
  foreach($datosCliente as $item) {
    $rznSocial = $item['rznSocial'];
    $direccion = $item['dirCalleNro'];
    $distrito = $item['dirDistrito'];
  }  
  $nroDoc = $_POST['txtNroDoc'];
  //echo "Nro de Documento  $nroDoc";
  require_once './librerias/conectar.php';
  global $servidor, $bd, $usuario, $contrasenia;
  $db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);
  $consulta = $db->prepare("SELECT count(*) as cant  FROM `doccobranza` WHERE `docCobranza` = :nroDoc AND (estado != 'Cancelado' or estado is null)   LIMIT 1");
  $consulta->bindParam(':nroDoc',$nroDoc);
  $consulta->execute();
  $data = $consulta->fetchAll();
  foreach ($data as $item){
    $cantidad = $item['cant'];
  }
  if ($cantidad == 1){
    $consulta = $db->prepare("SELECT *  FROM `despacho` WHERE `docCobranza` = :nroDoc");
    $consulta->bindParam(':nroDoc',$nroDoc);
    $consulta->execute();
    $data = $consulta->fetchAll();  
   
    //$items = cobrosDespachosPendientes($db,$cliente);}
    require 'vistas/cobrarImprime.php';
  }
}

function cerrarLiquidacion(){
  require 'modelos/clientesModelo.php';
  $nroLiquidTercero =  $_REQUEST['nroLiq'];  //  (isset($_GET['nroLiq']))?$_GET['nroLiq']:$_POST['nroLiq'];
  $datosLiquidTercero = buscarLiquidTercero($db,$nroLiquidTercero);
  require 'vistas/cerrarLiquidTercero.php';
}

function cambiarEstadoDocCobranza(){
  require 'modelos/clientesModelo.php';
  $aux = $_REQUEST['id'];
  $arrAux = explode('|',$aux);
  $estado = $arrAux['0'];
  $idCliente = $arrAux['1'];
  $nroDoc = $arrAux['2'];
  $tipoDoc = $arrAux['3'];
  $fchLimite = $arrAux['4'];

  $fch = isset($_POST['txtFchCambio'])?$_POST['txtFchCambio']:NULL;
  require 'vistas/cambiarEstadoDocCobranza.php';
}

function pagoDetraccion(){
  require 'modelos/clientesModelo.php';
  $aux = $_REQUEST['id'];
  $arrAux = explode('|',$aux);
  $idCliente = $arrAux['0'];
  $nroDoc = $arrAux['1'];
  $tipoDoc = $arrAux['2'];
  $fchLimite = $arrAux['3'];
  $pagoDetraccion = $arrAux['4'];
  $fchDetraccion = $arrAux['5'];
  $constancia = $arrAux['6'];

  $fch = isset($_POST['txtFchDetraccion'])?$_POST['txtFchDetraccion']:NULL;
  require 'vistas/pagoDetraccion.php';
}

function eliminaPagoDetraccion(){
  require 'modelos/clientesModelo.php';
  $aux = $_REQUEST['id'];
  $arrAux = explode('|',$aux);
  $idCliente = $arrAux['0'];
  $nroDoc = $arrAux['1'];
  $tipoDoc = $arrAux['2'];
  $fchLimite = $arrAux['3'];
  $pagoDetraccion = $arrAux['4'];
  $fchDetraccion = $arrAux['5'];
  $constancia = $arrAux['6'];

  $fch = isset($_POST['txtFchDetraccion'])?$_POST['txtFchDetraccion']:NULL;
  require 'vistas/eliminaPagoDetraccion.php';
}

function nuevoOtrosDocCobranza(){
  require 'modelos/despachosModelo.php';
  if (isset($_POST['enviado']) && $_POST['enviado'] == 'Si' ){
    $fchDespacho = $_POST['fch'];
    $correlativo = $_POST['co'];
  } else { 
    $fchDespacho = $_GET['fch'];
    $correlativo = $_GET['co'];
  }
  $despacho = buscarDespacho($db,$fchDespacho,$correlativo);
  $otrosConceptos = buscarOtrosConceptosCobrar($db, "Activo" );
  require 'vistas/nuevoDocCobranza.php';
}

function eliminaOtrosDocCobranza(){
  require 'modelos/despachosModelo.php';

  if (isset($_POST['enviado']) && $_POST['enviado'] == 'Si' ){
    $fchDespacho = $_POST['fch'];
    $correlativo = $_POST['co'];
    $codigo = $_POST['cd'];
  } else { 
    $fchDespacho = $_GET['fch'];
    $correlativo = $_GET['co'];
    $codigo = utf8_encode($_GET['cd']);
  }
  $despacho = buscarDespacho($db,$fchDespacho,$correlativo);
 

  require 'vistas/eliminaOtrosDocCobranza.php';
}

function editarDocumCobranza(){
  $idCliente = $_POST['idCliente'];
  require 'vistas/listadoEditarDocCobranza.php';
}

function editaDetalleCobranza(){
  require 'modelos/clientesModelo.php';
  $_SESSION['tipoDoc'] = isset($_GET['tipoDoc'])?$_GET['tipoDoc']:NULL;  //$tipoDoc
  $_SESSION['docCobranza'] = isset($_GET['docCobranza'])?$_GET['docCobranza']:NULL;  //$docCobranza
  $_SESSION['fchCreacion'] = isset($_GET['fchCreacion'])?$_GET['fchCreacion']:NULL;  //$fchCreacion  
  $dataCliente = buscarCliente($db,$_SESSION['idCliente']);
  require 'vistas/edicionDetalleCobranza.php';
}

function ingresosBanco(){
  require 'modelos/clientesModelo.php';
  require 'vistas/listarIngresosBanco.php';
}

function registrarIngresoBanco(){
  require 'modelos/clientesModelo.php';
  require 'modelos/despachosModelo.php';

  if(isset($_POST['txtBuscarCli'])){
    $auxCliente = $_POST['txtBuscarCli'];
    $idCliente = substr($auxCliente, 0,11);
  } else if(isset($_GET['id']) && $_GET['token'] == '1nv'){
    $auxCliente = $_GET['id'];
    $idCliente = substr($auxCliente, 0,11);
  } else {
    $auxCliente = "Elija un cliente";
    $idCliente = NULL;
  }

  if(isset($_POST['id']) && $_POST['opcion'] == 'anticipo'){
    $auxCliente = $_POST['id'];
    $idCliente = substr($auxCliente, 0,11);
    $mntAnticipo = $_POST['txtMntAnticipo'];
    $observacion = $_POST['txtObservAnticipo'];
    $rpta = crearAnticipo($db,$idCliente,$mntAnticipo,$observacion);
  } 
  if ($idCliente != NULL){
    $dataCliente = buscarCliente($db,$idCliente);
    $facturasPresentadas = cobrosDocumentosCobranza($db,$idCliente,'Presentada');
    $otrosDocums = buscarOtrosDocumsCobranza($db,$idCliente);
  }

  require 'vistas/registrarIngresoBanco.php';  
}

function movimientoFacturas(){
  require 'vistas/movimientoFacturas.php';
}

function editafactcli(){
  require 'modelos/clientesModelo.php';
  $nroFactura = isset($_GET['nrodoc'])?$_GET['nrodoc']:$_POST['nrodoc'];
  $cliente = isset($_GET['cliente'])?$_GET['cliente']:$_POST['cliente'];
  $dataFactura = buscarFactCliente($db,$cliente,$nroFactura);
  require 'vistas/editaFactCli.php';
}

function eliminafactcli(){
  require 'modelos/clientesModelo.php';
  $nroFactura = isset($_GET['nrodoc'])?$_GET['nrodoc']:$_POST['nrodoc'];
  $cliente = isset($_GET['cliente'])?$_GET['cliente']:$_POST['cliente'];
  $dataFactura = buscarFactCliente($db,$cliente,$nroFactura);
  require 'vistas/eliminaFactCli.php';
}

function listarDetallesCobrar(){
  require 'vistas/listarDetallesCobrar.php';
}

function registrarIngresoBanco2(){
  require 'librerias/config.ini.php';
  require 'modelos/clientesModelo.php';
  require 'modelos/despachosModelo.php';
  if(isset($_POST['txtBuscarCli'])){
    $auxCliente = $_POST['txtBuscarCli'];
    $idCliente = substr($auxCliente, 0,11);
  } else if(isset($_GET['id']) && $_GET['token'] == '1nv'){
    $auxCliente = $_GET['id'];
    $idCliente = substr($auxCliente, 0,11);
  } else {
    $auxCliente = "Elija un cliente";
    $idCliente = NULL;
  }
  if(isset($_POST['id']) && $_POST['opcion'] == 'anticipo'){
    $auxCliente = $_POST['id'];
    $idCliente = substr($auxCliente, 0,11);
    $mntAnticipo = $_POST['txtMntAnticipo'];
    $observacion = $_POST['txtObservAnticipo'];
    $rpta = crearAnticipo($db,$idCliente,$mntAnticipo,$observacion);
  } elseif(isset($_POST['id']) && $_POST['opcion'] == 'nota'){
    $auxCliente = $_POST['id'];
    $idCliente = substr($auxCliente, 0,11);
    $tipoNota = $_POST['cmbTipoNota'];
    $mntNota = $_POST['txtMntNota'];
    $nroNota = $_POST['txtNroDocNota'];
    $fchNota = $_POST['txtFchNota'];
    $observacion = $_POST['txtObservNota'];
    $rpta = insertaDocNota($db,  $nroNota, $tipoNota, "", $idCliente, $mntNota, $observacion, $fchNota);
  }  
  if ($idCliente != NULL){
    $dataCliente = buscarCliente($db,$idCliente);
    $facturasPresentadas = cobrosDocumentosCobranzaCalculado($db,$idCliente,'Presentada');
    $otrosDocums = buscarOtrosDocumsCobranza($db,$idCliente);
  }
  require 'vistas/registrarIngresoBanco2.php';
}

function actualizarEstadoFacturas(){
  require 'modelos/clientesModelo.php';
  $cant = actualizaEstadoFacturas($db);
  $url= "index.php?controlador=clientes&accion=movimientoFacturas";
  require 'vistas/redireccionar.php';
}

  function nuevacuentanew(){
    require 'modelos/clientesModelo.php';
    require 'modelos/zonasModelo.php';

    $id = $_REQUEST['id'];
    $dataCliente = buscarCliente($db,$id);
    $dataZonas = buscarUbiDetalles($db);
    $dataSuperCuentas = buscarClientesSuperCuentas($db,$id, null, "Activo");
    $corr = NULL;

    if (isset($_GET['corr'])){
      $corr = $_GET['corr'];
      $dataCuenta = buscarClientesCuentas02($db,$id, $corr);
      $nombreCuenta = $dataCuenta[0]['nombreCuenta'];
      $estadoCuenta = $dataCuenta[0]['estadoCuenta'];
      $paraMovil =  $dataCuenta[0]['paraMovil'];
      $tipoCuenta =  $dataCuenta[0]['tipoCuenta'];

      $opcionEditar = "Si";

      /*echo "<pre>";
      print_r($dataCuenta);
      echo "</pre>";*/

    } else {
      $nombreCuenta = $estadoCuenta = $paraMovil = $tipoCuenta = NULL;
      $opcionEditar = "No";
    }
    require 'vistas/nuevaCuentaNew.php';
  }

  function eliminacuentanew(){
    require 'modelos/clientesModelo.php';
    $id = $_REQUEST['id'];
    $corr = $_REQUEST['corr'];
    
    require 'vistas/eliminaCuentaNew.php';
  }


  function listarOrdenesCompraTerceros(){
    require 'vistas/listarOrdenesCompraTerceros.php';
  }

  function listarDetallesPorCobrar(){
    require 'librerias/config.ini.php';
    require 'modelos/clientesModelo.php';
    require 'modelos/tercerosModelo.php';
    require 'modelos/despachosModelo.php';

    $otrosConceptos = buscarOtrosConceptosCobrar($db, "Activo");
    $auxNroDoc = isset($_POST['txtCliCobBarr'])?$_POST['txtCliCobBarr']:$_GET['txtCliCobBarr'];
    $idCliente = strtok($auxNroDoc,'-');
    $cuentasCliente = buscarClientesCuentas02($db,$idCliente, NULL, NULL);
    $tipoDoc = strtok('-');
    $auxUltIdPreliquid = buscarUltimoIdPreliquidCobranza($db);
    $nroSgte = '00001';
    foreach($auxUltIdPreliquid as $itemUltIdPreliquid) {
      $aux = $itemUltIdPreliquid['nro'];
      $nroSgte = 1*$aux + 1;
      $nroSgte = substr('0000'.$nroSgte,-5);
    }
    $nroSgte = date("Y").'-'.$nroSgte;

    //$tipoOcurrencia = buscarTipoOcurrenciasTercero($db,NULL,'Activo');
    ////////////////////////
    require 'vistas/listarDetallesPorCobrar.php';
  }

  function listarCondPago(){
    require 'vistas/listarCondPagoDt.php';
  }

/*
SELECT `idCliente`, `correlativo`, `tipoCuenta`, `nombreCuenta`, `estadoCuenta`, `pa
*/



?>
