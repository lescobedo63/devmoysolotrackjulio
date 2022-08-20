<?php
  session_start();
  require '../librerias/conectar.php';  
  require '../modelos/ajaxModelo.php';  

  $opcion = $_GET['opc'];

  if ($opcion == 'insertaPunto'){
    echo insertaPunto($db,$_POST);
  } else if ($opcion == 'eliminaPunto'){
    echo eliminaPunto($db,$_POST);
  } else if ($opcion == 'recuperaPunto'){
    echo recuperaPunto($db,$_POST);
  } else if ($opcion == 'buscaDatosParaValidar'){
    echo recuperaPunto($db,$_POST);
  } else if ($opcion == 'distritos'){
    echo json_encode(buscarDistritos($db,$_GET['term']));
  } else if ($opcion == 'procesarDatosDespacho'){
    echo procesarDatosDespacho($db,$_POST);
  } else if ($opcion == 'calculoVistaPrevia'){
    echo calculoVistaPrevia($db,$_POST);
  } else if ($opcion == 'insertaOtro'){
    echo insertaOtro($db,$_POST);
  } else if ($opcion == 'recuperaOtro'){
    echo recuperaOtro($db,$_POST);
  } else if ($opcion == 'elimOtro'){
    echo elimOtro($db,$_POST);
  } else if ($opcion == 'generaDocsDespacho'){
    echo generaDocsDespacho($db,$_POST);
  } else if ($opcion == 'recuperaValDespacho'){
    echo recuperaValDespacho($db,$_POST);
  } else if ($opcion == 'calcVistaPrevTrabaj'){
    echo calcVistaPrevTrabaj($db,$_POST);
  } else if ($opcion == 'generarDocsTrabaj'){
    echo generarDocsTrabaj($db,$_POST);
  } else if ($opcion == 'generarTripulacionVistaPrevia'){
    echo generarTripulacionVistaPrevia($db,$_POST);
  } else if ($opcion == 'cuentaProducto'){
    echo json_encode(buscarCuentaProducto($db,$_GET['term'],$_GET['idCli']));
  } else if ($opcion == 'buscarTelefonos'){
    echo json_encode(buscarTelefonos($db,$_GET['term']));
  } else if ($opcion == 'subirAProduccion'){
    echo subirAProduccion($db,$_POST);
  } else if ($opcion == 'buscarPlaca'){
    echo json_encode(buscarPlaca($db,$_GET['term'],$_GET['estado']));
  } else if ($opcion == 'buscarConductor'){
    echo json_encode(buscarConductor($db,$_GET['term'],$_GET['estado']));
  } else if ($opcion == 'buscarConductorAbast'){
    echo json_encode(buscarConductorAbast($db,$_GET['term']));
  } else if ($opcion == 'validarAntesDeGuardar'){
    echo validarAntesDeGuardar($db,$_POST);
  } else if ($opcion == 'predictivoProgram'){
    echo predictivoProgram($db,$_POST);
  } else if ($opcion == 'buscarCuenta'){
    echo json_encode(buscarCuenta($db,$_GET['term'],$_GET['idCli']));
  } else if ($opcion == 'buscarProducto'){
    echo json_encode(buscarProducto($db,$_GET['term'],$_GET['idCli'],$_GET['idCuenta']));
  } else if ($opcion == 'creaEditaRegProgram'){
    echo json_encode(creaEditaRegProgram($db,$_POST));
  } else if ($opcion == 'buscarRegProgram'){
    echo buscarRegProgram($db,$_POST);
  } else if ($opcion == 'eliminaRegProgram'){
    echo eliminaRegProgram($db,$_POST);
  } else if ($opcion == 'subirAProduccion2'){
    echo subirAProduccion2($db,$_POST);
  } else if ($opcion == 'validarProveeVehiculoAntesDeGuardar'){
    echo validarProveeVehiculoAntesDeGuardar($db,$_POST);
  } else if ($opcion == 'creaEditaProveeVehiculo'){
    echo creaEditaProveeVehiculo($db,$_POST);
  } else if ($opcion == 'buscarRegProveeVehiculo'){
    echo buscarRegProveeVehiculo($db,$_POST);
  } else if ($opcion == 'eliminarProveeVehiculo'){
    echo eliminarProveeVehiculo($db,$_POST);
  } else if ($opcion == 'validarTelefonoAntesDeGuardar'){
    echo validarTelefonoAntesDeGuardar($db,$_POST);
  } else if ($opcion == 'creaEditaTelefono'){
    echo creaEditaTelefono($db,$_POST);
  } else if ($opcion == 'buscarTelefono'){
    echo buscarTelefono($db,$_POST);
  } else if ($opcion == 'eliminaTelefono'){
    echo eliminaTelefono($db,$_POST);
  } else if ($opcion == 'nuevaSuperCuenta'){
    echo nuevaSuperCuenta($db,$_POST);
  } else if ($opcion == 'editaSuperCuenta'){
    echo editaSuperCuenta($db,$_POST);
  } else if ($opcion == 'eliminaSuperCuenta'){
    echo eliminaSuperCuenta($db,$_POST);
  } else if ($opcion == 'verificarVehiculosEnUso'){
    echo json_encode(verificarVehiculosEnUso($db,$_POST));
  } else if ($opcion == 'creaEficiencia'){
    echo creaEficiencia($db,$_POST);
  } else if ($opcion == 'eliminaEfic'){
    echo eliminaEfic($db,$_POST);
  } else if ($opcion == 'editarEfic'){
    echo editarEfic($db,$_POST);
  } else if ($opcion == 'buscarUsuAsignables'){
    echo json_encode(buscarUsuAsignables($db,$_GET['term']));
  } else if ($opcion == 'editar_validarAbast'){
    echo editar_validarAbast($db,$_POST);
  } else if ($opcion == 'registrarAbast'){
    echo registrarAbast($db,$_POST);
  } else if ($opcion == 'crearServ'){
    echo crearServ($db,$_POST);
  } else if ($opcion == 'editarServ'){
    echo editarServ($db,$_POST);
  } else if ($opcion == 'eliminarServ'){
    echo eliminarServ($db,$_POST);
  } else if ($opcion == 'trabajNoQuincena'){
    echo json_encode(buscarTrabajNoQuincena($db,$_GET['term']));
  } else if ($opcion == 'editarChecklist'){
    echo editarChecklist($db,$_POST);
  } else if ($opcion == 'buscarTrabajador'){
    echo json_encode(buscarTrabajador($db,$_GET['term'],$_GET['estado']));
  } else if ($opcion == 'crearUsuOcu'){
    echo crearUsuOcu($db,$_POST);
  } else if ($opcion == 'editarUsuOcu'){
    echo editarUsuOcu($db,$_POST);
  } else if ($opcion == 'refrescarDatosBBSS'){
    echo refrescarDatosBBSS($db,$_POST);
  } else if ($opcion == 'crearTicket'){
    echo crearTicket($db,$_POST);
  } else if ($opcion == 'editarHelpDeskTicket'){
    echo editarHelpDeskTicket($db,$_POST);
  } else if ($opcion == 'helpDeskNuevoDetalle'){
    echo helpDeskNuevoDetalle($db,$_POST);
  } else if ($opcion == 'buscarDetalleTicket'){
    $arrAux = buscarDetalleTicket($db,$_POST);
    $arrPreparado = array(
      "nroTicket" => $arrAux["nroTicket"],
      "correl" => $arrAux["correl"],
      "fchRealizado" => $arrAux["fchRealizado"],
      "tareasRealizadas" => utf8_encode($arrAux["tareasRealizadas"]),
    );
    echo json_encode($arrPreparado);
  } else if ($opcion == 'helpDeskEditaDetalle'){
    echo helpDeskEditaDetalle($db,$_POST);
  } else if ($opcion == 'helpDeskEliminaDetalle'){
    echo helpDeskEliminaDetalle($db,$_POST);
  } else if ($opcion == 'crearGrifo'){
    echo crearGrifo($db,$_POST);
  } else if ($opcion == 'editarGrifo'){
    echo editarGrifo($db,$_POST);
  } else if ($opcion == 'crearCotizacion'){
    echo crearCotizacion($db,$_POST);
  } else if ($opcion == 'editarCotizacion'){
    echo editarCotizacion($db,$_POST);
  } else if ($opcion == 'crearCapacitacion'){
    echo crearCapacitacion($db,$_POST);
  } else if ($opcion == 'editarCapacitacion'){
    echo editarCapacitacion($db,$_POST);
  } else if ($opcion == 'eliminarCapacitacion'){
    echo eliminarCapacitacion($db, $_POST);
  } else if ($opcion == 'crearRedPeaje') {
    echo crearRedPeaje($db, $_POST);
  } else if ($opcion == 'editarRedPeaje') {
    echo editarRedPeaje($db, $_POST);
  } else if ($opcion == 'buscarCapacitador'){
    echo json_encode(buscarCapacitador($db,$_GET['term']));
  } else if ($opcion == 'buscarServicio'){
    echo json_encode(buscarServicio($db,$_GET['term']));
  } else if ($opcion == 'crearMantenimiento'){
    echo crearMantenimiento($db,$_POST);
  } else if ($opcion == 'crearTaller'){
    echo crearTaller($db,$_POST);
  } else if ($opcion == 'eliminarAbast'){
    echo eliminarAbast($db,$_POST);
  } else if ($opcion == 'crear_editarInfracion'){
    echo crear_editarInfracion($db,$_POST);
  }else if ($opcion == 'buscarInfraccion'){
    echo json_encode(buscarInfraccion($db,$_GET['term']));
  } else if ($opcion == 'buscarCuentaInfraccion'){
    echo json_encode(buscarCuentaInfraccion($db,$_GET['term']));
  } else if ($opcion == 'registrarPapeleta'){
    echo registrarPapeleta($db,$_POST);
  } else if ($opcion == 'buscarTelefonoR'){
    echo json_encode(buscarTelefonoR($db,$_GET['term']));
  } else if ($opcion == 'registrarTelefonoR'){
    echo registrarTelefonoR($db,$_POST);
  }else if ($opcion == 'editar_TelfR'){
    echo editar_TelfR($db,$_POST);
  }

  //
  //

?>

