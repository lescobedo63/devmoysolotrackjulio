<?php
  session_start();
  require '../librerias/conectar.php';  
  require '../modelos/ajaxModelo_v2.php';
  require_once '../librerias/config.ini.php';

  $opcion = isset($_GET['opc']) ? $_GET['opc'] :  $_POST['opc'];

  if ($opcion == 'tercerosOrdenP'){
    echo tercerosOrdenP($db);
  } else if ($opcion == 'insertaDocIdentidad'){
    echo insertaDocIdentidad($db, $_POST);
  } else if ($opcion == 'buscarDocIdentidad'){
    echo json_encode(buscarDocIdentidad($db));
  } else if ($opcion == 'editaDocIdentidad'){
    echo editaDocIdentidad($db);
  } else if ($opcion == 'generarPagoTercero'){
    echo generarPagoTercero($db);
  } else if ($opcion == 'generarPagoTercElemento'){
    echo generarPagoTercElemento($db);
  } else if ($opcion == 'generarDetallePagoTercero'){

    $rptaDatosEmpresa = $rptaDatosFactura = $rptaDatosNotaCredito = $rptaDatosFactMoy = $rptaTotDocumento = "";

    $_SESSION['idRegistro'] = $_POST["idRegistro"];
    $_SESSION['tipoDocTerc'] = $_POST["tipoDocTerc"];

    $docPagoTercero = buscarDocPagoTercero($db,$_POST["idRegistro"]);

    $_SESSION['docPagoTercero'] = $docPagoTercero;

/*    $_SESSION['nroDoc']   = $docPagoTercero["nroDocLiq"];
    $_SESSION['notaCrdt'] = $docPagoTercero["nroNotaCredito"];
    $_SESSION['factMoy']  = $docPagoTercero["nroFactMoy"];
    $_SESSION['fchCreacion']  = $docPagoTercero["fchCreacion"];
*/
    $_SESSION['dataFactura'] = buscarPagoTerceroDetalle($db,'FACT',$_POST["idRegistro"]);
    $_SESSION['dataNotaCredito'] = buscarPagoTerceroDetalle($db,'NCRD',$_POST["idRegistro"]);
    $_SESSION['dataFactMoy'] = buscarPagoTerceroDetalle($db,'FMOY',$_POST["idRegistro"]);

    $rptaDatosEmpresa = "<div>
    <table>
      <tr>
        <td width = '100'>OC Nro.</td>
        <td width = '250'>".$_POST["idRegistro"]."</td>
        <td width = '50'></td>
        <td colspan = '2' width = '300'>Datos de Facturación</td>
        <td width = '50'  rowspan = '2' ><img src='imagenes/hoja.png' width='25' height='25'  onclick = 'descargarExcel()'></td>
      </tr>
      <tr>
        <td>Fecha</td>
        <td>".$docPagoTercero["fchCreacion"]."</td>
        <td></td>
        <td>RUC</td>
        <td>20297421035</td>
      </tr>
      <tr>
        <td>Tercero</td>
        <td rowspan= '2'>".utf8_decode($docPagoTercero["nombreCompleto"])."</td>
        <td></td>
        <td>Razón Social</td>
        <td>Inversiones Moy S.A.C.</td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td>Dirección</td>
        <td>Calle 6 Mz K Lote 1A</td>
      </tr>
    </table>
    </div>
    <br>";


    $montoBruto = $montoNeto = 0;
    $montoBrutoNC = $montoNetoNC = 0;
    $montoBrutoFMoy = $montoNetoFMoy = 0;

    //var_dump($dataFactura);

    $count = 1;

    $rptaDatosFactura = "<div>
    <table>
      <tr>
        <td colspan = '3'>Factura: ".$docPagoTercero["nroDocLiq"]."</td>
        <td width = '60'></td>
        <td width = '200'></td>
        <td width = '130'></td>
        <td width = '150'></td>
        <td width = '200'></td>
        <td width = '80'></td>
      </tr>
      <tr>
        <th width = '40' align = 'center'>Nro.</th>
        <th width = '65' align = 'center'>Fecha</th>
        <th width = '65' align = 'center'>Tipo</th>
        <th align = 'center'>Placa</th>
        <th align = 'center'>Conductor</th>
        <th colspan = '2' align = 'center'>Cliente</th>
        <th colspan = '1' align = 'center'>Observaciones</th>
        <th align = 'center'>Monto</th>
      </tr>";

      $contAlquiler = 0;
      $contIntegral = 0;

      foreach ($_SESSION['dataFactura'] as $key => $value) {
        $fchDespacho = $value["fchDespacho"];
        $tipo = $value["tipo"];
        $placa = $value["placa"];
        $nombConductor = utf8_decode($value["nombConductor"]);
        $nombCliente = $value["nombre"];
        $cuenta = $value["cuenta"];
        $observacion = utf8_decode($value["observ"]);

        $observacion = explode("|", $observacion);
        if(!empty($observacion[2])) { }
        else {
          $observacion[2]="";
        }
        if($tipo=="Despacho"){
          $comentario = $value["comentario"];
          $valor = $comentario;
        }
        else if($value['nombre'] == "nombre"){
          $valor= $value['observ'];
        }
        else{
          $valor = $observacion[2];
        }
        $monto = $value["costoDia"];
        $origen = $value["origen"];
        if ($value["modoAcuerdo"] == "Alquiler") $contAlquiler++;
        else if ($value["modoAcuerdo"] == "Integral") $contIntegral++;


        if($origen == 'combust' || $origen == 'ccmovim' || $tipo == 'Dsct' ) $monto = -1*$monto;


        $rptaDatosFactura .= "<tr>
          <td>$count</td>
          <td>$fchDespacho</td>
          <td>$tipo</td>
          <td>$placa</td>
          <td>$nombConductor</td>
          <td>$nombCliente</td>
          <td>$cuenta</td>
          <td>".utf8_decode($valor)."</td>
          <td align = 'right'>".number_format($monto,2)."</td>
        </tr>";
        $montoBruto += $monto;
        $count++;
      }

      if($_POST["tipoDocTerc"] == "Factura"){

        $igv = $porcIgv*$montoBruto;
        $montoNeto = $montoBruto + $igv;

        $rptaDatosFactura .= "<tr>
          <td colspan = '7'></td>
            <td align = 'right'>Subtotal</td>
          <td align = 'right'>".number_format($montoBruto,2)."</td>
          </tr>
          <tr>
            <td colspan = '7'></td>
            <td align = 'right'>IGV</td>
            <td align = 'right'>".number_format($igv,2)."</td>
          </tr>
          <tr>
            <td colspan = '7'></td>
            <td align = 'right'>Total OC</td>
            <td align = 'right'>".number_format($montoNeto,2)."</td>
          </tr>
          <tr>";

        if ($montoNeto >= 400){

          if ($contAlquiler > 0 and $contIntegral > 0 ){

            $rptaDatosFactura .= "<tr>
              <td colspan = '3'></td>
              <td colspan = '4'>ERROR</td>
              <td align = 'right'>Detracción</td>
              <td align = 'right' class = 'rojo' >No puede tener vehículos en alquiler e integral en una misma factura</td>
            </tr>";

          } else {

            if($contAlquiler > 0 and $contIntegral == 0 ){
              $detraccion = -1*round($montoNeto * 0.10);
            } else if($contAlquiler == 0 and $contIntegral > 0 ){
              $detraccion = -1*round($montoNeto * 0.04);
            }

            $montoNeto = $montoNeto + $detraccion;
            $rptaDatosFactura .= "<tr>
              <td colspan = '3'></td>
              <td colspan = '4'>Cuenta de Detracción ".$docPagoTercero["cuentaDetraccion"]."</td>
              <td align = 'right'>Detracción</td>
              <td align = 'right' class = 'rojo' >".number_format($detraccion,2)."</td>
            </tr>";

          }


        }

          $rptaDatosFactura .= "<tr>
            <td colspan = '7'></td>
            <td align = 'right'>Total</td>
            <td align = 'right'>".number_format($montoNeto,2)."</td>
          </tr>";

      } else if ($_POST["tipoDocTerc"] == "RRHH"){
        //$igv = $porcIgv*$montoBruto;
        $montoNeto = $montoBruto; // + $igv;

        $rptaDatosFactura .= "<tr>
          <td colspan = '7'></td>
            <td align = 'right'>Subtotal</td>
          <td align = 'right'>".number_format($montoBruto,2)."</td>
          </tr>";
      }


      $rptaDatosFactura .= "</table>
          </div>
        <br>";

      if ( count($_SESSION['dataNotaCredito']) > 0 ){
        $count = 1;
        $rptaDatosNotaCredito = "<div>
        <table>
          <tr>
            <td colspan = '3'>Nota de Crédito: ".$docPagoTercero["nroNotaCredito"]."</td>
            <td width = '60'></td>
            <td width = '200'></td>
            <td width = '480'></td>
            <td width = '80'></td>
          </tr>
          <tr>
            <th width = '40' align = 'center'>Nro.</th>
            <th width = '65' align = 'center'>Fecha</th>
            <th width = '65' align = 'center'>Tipo</th>
            <th align = 'center'>Placa</th>
            <th align = 'center'>Conductor</th>
            <th>Descripcion</th>
            <th align = 'center'>Monto</th>
          </tr>";

          foreach ($_SESSION['dataNotaCredito'] as $key => $value) {
            $fchDespacho = $value["fchDespacho"];
            $tipo = $value["tipo"];
            $placa = $value["placa"];
            $nombConductor = $value["nombConductor"];
            $nombCliente = $value["nombre"];
            $cuenta = $value["cuenta"];
            $observacion = $value["observ"];
            $observacion = explode("|", $observacion);
            if(!empty($observacion[2])) { }
            else {
              $observacion[2]="";
            }
            if($tipo=="Despacho"){
              $comentario = $value["comentario"];
              $valor = $comentario;
            }
            else if($value['nombre'] == "nombre"){
              $valor= $value['observ'];
            }
            else{
              $valor = $observacion[2];
            }

            $monto = $value["costoDia"];

            $origen = $value["origen"];
            if($origen == 'combust' || $origen == 'ccmovim' || $tipo == 'Dsct' ) $monto = -1*$monto;

            $rptaDatosNotaCredito .= "<tr>
              <td>$count</td>
              <td>$fchDespacho</td>
              <td>$tipo</td>
              <td>$placa</td>
              <td>$nombConductor</td>
              <td>$valor</td>
              <td align = 'right'>".number_format($monto,2)."</td>
            </tr>";
            $montoBrutoNC += $monto;
            $count++;
          }

        if($_POST["tipoDocTerc"] == "Factura"){

          $igvNC = $porcIgv*$montoBrutoNC;
          $montoNetoNC = $montoBrutoNC + $igvNC;

          $rptaDatosNotaCredito .= "<tr>
                <td colspan = '5'></td>
                <td align = 'right'>Subtotal</td>
                <td align = 'right'>".number_format($montoBrutoNC,2)."</td>
              </tr>
              <tr>
                <td colspan = '5'></td>
                <td align = 'right'>IGV</td>
                <td align = 'right'>".number_format($igvNC,2)."</td>
              </tr>
              <tr>
                <td colspan = '5'></td>
                <td align = 'right'>Total</td>
                <td align = 'right'>".number_format($montoNetoNC,2)."</td>
              </tr>
            </table>
          </div>
          <br>";

        } else if($_POST["tipoDocTerc"] == "RRHH"){

          // $igvNC = $porcIgv*$montoBrutoNC;
          $montoNetoNC = $montoBrutoNC; // + $igvNC;

          $rptaDatosNotaCredito .= "<tr>
                <td colspan = '5'></td>
                <td align = 'right'>Subtotal</td>
                <td align = 'right'>".number_format($montoBrutoNC,2)."</td>
              </tr>
            </table>
          </div>
          <br>";
        }    
      }

    if( count($_SESSION['dataFactMoy']) >0 ){


      $rptaDatosFactMoy = "<div>
      <table>
        <tr>
          <td colspan = '3'>Factura Moy: ".$docPagoTercero["nroFactMoy"]."</td>
          <td width = '60'></td>
          <td width = '200'></td>
          <td width = '480'></td>
          <td width = '80'></td>
        </tr>
        <tr>
          <th width = '40' align = 'center'>Nro.</th>
          <th width = '65' align = 'center'>Fecha</th>
          <th width = '65' align = 'center'>Tipo</th>
          <th align = 'center'>Placa</th>
          <th align = 'center'>Conductor</th>
          <th>Descripcion</th>
          <th align = 'center'>Monto</th>
        </tr>";

        $count = 0;

        foreach ($_SESSION['dataFactMoy'] as $key => $value) {
          $fchDespacho = $value["fchDespacho"];
          $tipo = $value["tipo"];
          $placa = $value["placa"];
          $nombConductor = $value["nombConductor"];
          $nombCliente = $value["nombre"];
          $cuenta = $value["cuenta"];
          $observacion = $value["observ"];
          $observacion = explode("|", $observacion);
          if(!empty($observacion[2])) { }
          else {
            $observacion[2]="";
          }
          if($tipo=="Despacho"){
            $comentario = $value["comentario"];
            $valor = $comentario;
          }
          else if($value['nombre'] == "nombre"){
            $valor= $value['observ'];
          }
          else{
            $valor = $observacion[2];
          }

          $monto = $value["costoDia"];

          $origen = $value["origen"];
          if($origen == 'combust' || $origen == 'ccmovim' || $tipo == 'Dsct' ) $monto = -1*$monto;

          $rptaDatosFactMoy .= "<tr>
            <td>$count</td>
            <td>$fchDespacho</td>
            <td>$tipo</td>
            <td>$placa</td>
            <td>$nombConductor</td>
            <td>$valor</td>
            <td align = 'right'>".number_format($monto,2)."</td>
          </tr>";
          $montoBrutoFMoy += $monto;
          $count++;
        }

        if($_POST["tipoDocTerc"] == "Factura"){


          $igv = $porcIgv*$montoBrutoFMoy;
          $montoNetoFMoy = $montoBrutoFMoy + $igv;
          $rptaDatosFactMoy .= "<tr>
                <td colspan = '5'></td>
                <td align = 'right'>Subtotal</td>
                <td align = 'right'>".number_format($montoBrutoFMoy,2)."</td>
              </tr>
              <tr>
                <td colspan = '5'></td>
                <td align = 'right'>IGV</td>
                <td align = 'right'>".number_format($igv,2)."</td>
              </tr>
              <tr>
                <td colspan = '5'></td>
                <td align = 'right'>Total</td>
                <td align = 'right'>".number_format($montoNetoFMoy,2)."</td>
              </tr>
            </table>
            </div>
          <br>";

        } else if($_POST["tipoDocTerc"] == "RRHH"){

          //$igv = $porcIgv*$montoBrutoFMoy;
          $montoNetoFMoy = $montoBrutoFMoy; // + $igv;

          $rptaDatosFactMoy .= "<tr>
                <td colspan = '5'></td>
                <td align = 'right'>Subtotal</td>
                <td align = 'right'>".number_format($montoBrutoFMoy,2)."</td>
              </tr>
            </table>
          </div>
          <br>";
        } 
    }

    $montoTotalLiq = $montoNeto + $montoNetoNC + $montoNetoFMoy;

    $rptaTotDocumento = "<table>

          <tr>
            <td width = '775'></td>
            <th align = 'right'  width = '140'>Total Factura&nbsp;</th>
            <td align = 'right'  width = '80'>".number_format($montoNeto,2)."</td>
          </tr>
          <tr>
            <td></td>
            <th align = 'right'>Total NC&nbsp;</th>
            <td align = 'right' class = 'rojo'>".number_format($montoNetoNC,2)."</td>
          </tr>
          <tr>
            <td></td>
            <th align = 'right'>Total Fact. Moy&nbsp;</th>
            <td align = 'right' class = 'rojo'>".number_format(-1*$montoNetoFMoy,2)."</td>
          </tr>

          <tr>
            <td></td>
            <td></td>
            <td></td>
          </tr>

          <tr>
            <td></td>
            <th align = 'right'>Pago Liq.&nbsp;</th>
            <td align = 'right'>".number_format($montoTotalLiq,2)."</td>
          </tr>

        </table>
      </div>
      <br>";

    $rpta = utf8_encode($rptaDatosEmpresa .$rptaDatosFactura. $rptaDatosNotaCredito. $rptaDatosFactMoy. $rptaTotDocumento ) ;

    echo $rpta;

  } else if ($opcion == 'insertaNuevaOcurrencia'){
    echo insertaNuevaOcurrencia($db);
  } else if ($opcion == 'cambiaRuc'){
    echo cambiaRuc($db);
  } else if ($opcion == 'actualizaMonto'){
    echo actualizaMonto($db);
  } else if ($opcion == 'eliminaOcurrencia'){
    echo eliminaOcurrencia($db);
  } else if ($opcion == 'actualizaMontoLiqTercero'){
    echo actualizaMontoLiqTercero($db);
  } else if ($opcion == 'eliminaTercMiscelaneo'){
    echo eliminaTercMiscelaneo($db);
  } else if ($opcion == 'marcaMasivaDespachos'){
    echo json_encode(marcaMasivaDespachos($db));
  } else if ($opcion == 'editaDocPagoTercero'){
    echo json_encode(editaDocPagoTercero($db));
  } else if ($opcion == 'eliminaDocPagoTercero'){
    echo json_encode(eliminaDocPagoTercero($db));
  } else if ($opcion == 'addNuevoMisc'){
    echo addNuevoMisc($db);
  } else if ($opcion == 'eliminaOtrosGastos'){
    echo eliminaOtrosGastos($db);
  } else if ($opcion == 'elimOCTercero'){
    echo elimOCTercero($db);
  } else if ($opcion == 'elimDocIdentidad'){
    echo json_encode(elimDocIdentidad($db));
  } else if ($opcion == 'buscarCliCuentas'){
    echo buscarCliCuentas($db);
  } else if ($opcion == 'buscarCliCueProd'){
    echo buscarCliCueProd($db);
  } else if ($opcion == 'buscarDespacho'){
    echo json_encode(buscarDespacho($db));
  } else if ($opcion == 'verDetallesDespacho'){

  } else if ($opcion == 'insertaNuevaGuiaPorte'){
    echo insertaNuevaGuiaPorte($db);
  } else if ($opcion == 'editaGuiaPorte'){
    echo editaGuiaPorte($db);
  } else if ($opcion == 'eliminaGuiaPorte'){
    echo eliminaGuiaPorte($db);
  } else if ($opcion == 'nuevoPunto'){
    echo nuevoPunto($db);
  } else if ($opcion == 'editaPunto'){
    echo editaPunto($db);
  } else if ($opcion == 'eliminaPunto'){
    echo eliminaPunto($db);
  } else if ($opcion == 'puntosEstados'){
    echo puntosEstados($db);
  } else if ($opcion == 'puntosSubestados'){
    echo puntosSubestados($db);
  } else if ($opcion == 'buscarDespPuntos'){
    echo json_encode(buscarDespPuntos($db));
  } else if ($opcion == 'buscarDataIdProducto'){
    echo json_encode(buscarDataIdProducto($db));
  } else if ($opcion == 'editaDespacho'){
    echo json_encode(editaDespacho($db));
  } else if ($opcion == 'insertaTrip'){
    echo json_encode(insertaTrip($db));
  } else if ($opcion == 'buscarDatosTripulante'){
    echo json_encode(buscarDatosTripulante($db));
  } else if ($opcion == 'editaTrip'){
    echo json_encode(editaTrip($db));
  } else if ($opcion == 'eliminaTrip'){
    echo json_encode(eliminaTrip($db));
  } else if ($opcion == 'despachosDestinos'){
    echo json_encode(despachosDestinos($db));
  } else if ($opcion == 'traspasarPunto'){
    echo json_encode(traspasarPunto($db));
  } else if ($opcion == 'verificarEstadoDueno'){
    echo json_encode(verificarEstadoDueno($db, $_POST));
  } else if ($opcion == 'asignarTelefono'){
    echo json_encode(buscarAsignarTelefono($db,$_GET['term']));
  } else if ($opcion == 'repoEficienciaVehiculo'){
    //echo "FECHA INI ".$_POST["fchIni"];
    //echo "FECHA FIN ".$_POST["fchFin"];
    $ratioMejora = 1.05;
    $ratioEmpeora = 0.95;
    
    $fchIni = isset($_POST["fchIni"]) ? $_POST["fchIni"] : '2019-10-01';
    $fchFin = isset($_POST["fchFin"]) ? $_POST["fchFin"] : '2020-02-01';
    $arrDatos = array();
    $_SESSION["dataCombust"] = buscarDataCombust($db, $fchIni, $fchFin);
    $_SESSION["dataDespxMes"] = buscarDataDespachosxMes($db, $fchIni, $fchFin);

    $arrPlacas = array();
    $arrAnhiosMeses = array();



    foreach ($_SESSION["dataCombust"] as $key => $value) {
      if (!in_array($value["idPlaca"], $arrPlacas)) $arrPlacas[] = $value["idPlaca"];
      if (!in_array($value["anhioMes"], $arrAnhiosMeses)) $arrAnhiosMeses[] = $value["anhioMes"];

      if (!isset($arrDatos[$value["idPlaca"]][$value["anhioMes"]])){
        $arrDatos[$value["idPlaca"]]['eficPeriodo'] = 0;
        $arrDatos[$value["idPlaca"]]['observacion'] = "";
        $arrDatos[$value["idPlaca"]]["claseColor"] = "";
        $arrDatos[$value["idPlaca"]]['eficEsperada'] = $value["ratioSolesKm"];
        $arrDatos[$value["idPlaca"]][$value["anhioMes"]]["contador"] = 0;
        $arrDatos[$value["idPlaca"]][$value["anhioMes"]]["contBruto"] = 0;
        $arrDatos[$value["idPlaca"]][$value["anhioMes"]]["recorridBruto"] = 0;
        $arrDatos[$value["idPlaca"]][$value["anhioMes"]]["sumEfic"] = 0;
        $arrDatos[$value["idPlaca"]][$value["anhioMes"]]["eficMes"] = 0;
        $arrDatos[$value["idPlaca"]][$value["anhioMes"]]["ratioConMesAnterior"] = 0;
        $arrDatos[$value["idPlaca"]][$value["anhioMes"]]["cantDespachos"] = 0;
      }

      $arrDatos[$value["idPlaca"]][$value["anhioMes"]]["recorridBruto"] +=  1*$value["recorrido"];
      $arrDatos[$value["idPlaca"]][$value["anhioMes"]]["contBruto"] += 1;


      if ($value["eficSoles"] > 0  ){
        $arrDatos[$value["idPlaca"]][$value["anhioMes"]]["contador"] += 1;
        $arrDatos[$value["idPlaca"]][$value["anhioMes"]]["sumEfic"] += 1*$value["eficSoles"];
      }
    }

    //$primero = "Si";
    foreach ($arrDatos as $idPlaca => $unaLinea) {
      $sumEficTotal = 0;
      $contMeses = 0;      
      $eficMesAnterior = 1;
      $aux01 = $aux02 = "";
      foreach ($unaLinea as $anhioMes => $value) {
        if(is_array($value)){
          if ($value["contador"] == 0){
            $eficMes = 0;
          } else {
            $eficMes = $value["sumEfic"]/$value["contador"];
          }

          $arrDatos[$idPlaca][$anhioMes]["eficMes"] = $eficMes;

          if($eficMesAnterior == 0)
            $arrDatos[$idPlaca][$anhioMes]["ratioConMesAnterior"] = 0;
          else { 
            $arrDatos[$idPlaca][$anhioMes]["ratioConMesAnterior"] = $eficMes/$eficMesAnterior;
          }

          if($arrDatos[$idPlaca][$anhioMes]["ratioConMesAnterior"] == 0 ){
            $hayMejora = "No hay datos";
            $claseColor = " class = 'Rojo' ";
          } 
          else if($arrDatos[$idPlaca][$anhioMes]["ratioConMesAnterior"] >= $ratioMejora){
            $hayMejora = "Mejora";
            $claseColor = " class = 'Verde' ";

          } 
          else if($arrDatos[$idPlaca][$anhioMes]["ratioConMesAnterior"] <= $ratioEmpeora){
            $hayMejora = "Empeora";
            $claseColor = " class = 'Rojo' ";
          } 
          else {
            $hayMejora = "";
            $claseColor = "";
          }
          
          $aux01 = $aux02;
          $aux02 = $hayMejora;

          $eficMesAnterior = $eficMes;
          $sumEficTotal += $eficMes;
          $contMeses++;          
        } else {

        }
      }
      $arrDatos[$idPlaca]["eficPeriodo"] = $sumEficTotal / $contMeses;
      if($aux01 == $aux02){
        $arrDatos[$idPlaca]["observacion"] = $aux01;
        $arrDatos[$idPlaca]["claseColor"] = $claseColor;
      }
    }

    foreach ($_SESSION["dataDespxMes"] as $key => $value) {
      //if(isset($arrDatos[$value["placa"]][$value["anhioMes"]]["cantDespachos"]))
        $arrDatos[$value["placa"]][$value["anhioMes"]]["cantDespachos"] = $value["cantDespachos"];      
    }

    //var_dump($arrDatos);
    $lineaSup = $lineaInf = "";
    $reporte = "<table>";
    $reporte .= "<thead><tr>";
    $reporte .= "<th rowspan= '2'>Placa</th>";
    $primero = "Si";
    foreach ($arrAnhiosMeses as $key => $anhioMes) {
      $lineaSup .= "<th colspan = '4'>$anhioMes</th>";
      $lineaInf .= "<th width = '50'>Nro. Despachos</th><th width = '50'>Cant. Abast</th><th width = '50'>Reco rrido</th><th  width = '50'>Efic. (Soles/Km)</th>";
      if($primero == "Si"){
        $primero = "No";
        $comparaInf = "";
        $comparaSup = "";
        $anhioMesAnt = $anhioMes;
      } else {
        $comparaSup .= "<th  width = '50'>Ratio</th>";
        $comparaInf .= "<th>$anhioMes / $anhioMesAnt</th>";
        $anhioMesAnt = $anhioMes;
      }
    }

    $reporte .= $lineaSup."<th width = '50' rowspan= '2'>Efic. Promedio</th><th width = '50' rowspan= '2'>Efic. Esperada</th>$comparaSup</th><th width = '100' rowspan = '2' > Observaciones</th></tr>";
    $reporte .= $lineaInf.$comparaInf;

    asort($arrPlacas);
    $reporte .= "</thead>";
    
    foreach ($arrPlacas as $indice => $idPlaca) {
      $reporte .= "<tr>";
      $reporte .= "<td>$idPlaca</td>";
      $primero = "Si";
      foreach ($arrAnhiosMeses as $key => $anhioMes) {
        if(!isset($arrDatos[$idPlaca][$anhioMes]["cantDespachos"])) $reporte .= "<td class = 'rojo'></td>";
        else $reporte .= "<td align = 'right' >".$arrDatos[$idPlaca][$anhioMes]["cantDespachos"]."</td>";
        if(!isset($arrDatos[$idPlaca][$anhioMes]["contBruto"])) $reporte .= "<td class = 'rojo'></td>";
        else $reporte .= "<td align = 'right' >".$arrDatos[$idPlaca][$anhioMes]["contBruto"]."</td>";
        if(!isset($arrDatos[$idPlaca][$anhioMes]["recorridBruto"])) $reporte .= "<td class = 'rojo'></td>";
        else {
          if ($arrDatos[$idPlaca][$anhioMes]["recorridBruto"] <= 0) $classCelda = " class = 'rojo' ";
          else $classCelda = ""; 
          $reporte .= "<td align = 'right' $classCelda>".$arrDatos[$idPlaca][$anhioMes]["recorridBruto"]."</td>";
        }

        if(!isset($arrDatos[$idPlaca][$anhioMes]["eficMes"])) $reporte .= "<td class = 'rojo'></td>";
        else $reporte .= "<td align = 'right'>".round($arrDatos[$idPlaca][$anhioMes]["eficMes"],3)."</td>";
        if ($primero == "Si"){
          $primero = "No";
          $comparaciones = "";

        } else {
          if(!isset($arrDatos[$idPlaca][$anhioMes]["ratioConMesAnterior"])) $comparaciones .= "<td class = 'rojo'></td>";
          else {
            if ($arrDatos[$idPlaca][$anhioMes]["ratioConMesAnterior"] < $ratioEmpeora) $classCelda = " class = 'rojo' ";
            elseif ($arrDatos[$idPlaca][$anhioMes]["ratioConMesAnterior"] > $ratioMejora )  $classCelda = " class = 'verde' ";
            else $classCelda = "";
           $comparaciones .= "<td align = 'right' $classCelda >".round($arrDatos[$idPlaca][$anhioMes]["ratioConMesAnterior"],2)."</td>";
          }

          //$comparaciones .= "<td>".$arrDatos[$idPlaca][$anhioMes]["ratioConMesAnterior"]."</td>";
        }

        # code...
      }
      if(!isset($arrDatos[$idPlaca]["eficPeriodo"])) $reporte .= "<td></td>";
      else {
        if($arrDatos[$idPlaca]["eficPeriodo"] < 0.75* $arrDatos[$idPlaca]["eficEsperada"] ) $classCelda = " class = 'rojo' ";
        else if($arrDatos[$idPlaca]["eficPeriodo"] <= 0.9* $arrDatos[$idPlaca]["eficEsperada"] ) $classCelda = " class = 'amarillo' ";
        else if($arrDatos[$idPlaca]["eficPeriodo"] > 1.1* $arrDatos[$idPlaca]["eficEsperada"] ) $classCelda = " class = 'verde' ";
        else  $classCelda = "";
        $reporte .= "<td align = 'right' $classCelda >".round($arrDatos[$idPlaca]["eficPeriodo"],3)."</td>";
      }

      if(!isset($arrDatos[$idPlaca]["eficEsperada"])) $reporte .= "<td></td>";
      else $reporte .= "<td align = 'right'>".round($arrDatos[$idPlaca]["eficEsperada"],3)."</td>";
      $reporte .= $comparaciones;


      $reporte .= "<td ".$arrDatos[$idPlaca]["claseColor"]." >".$arrDatos[$idPlaca]["observacion"]."</td>";
      $reporte .= "</tr>";
    }
    $reporte .= "</table>";

    echo $reporte;

  } else if ($opcion == 'graficaEficienciaSolesKm'){
    $fchIni = isset($_POST["fchIni"]) ? $_POST["fchIni"] : '2019-10-01';
    $fchFin = isset($_POST["fchFin"]) ? $_POST["fchFin"] : '2020-02-01';
    $arrDatos = array();
    //echo "Fch ini: $fchIni, Fch Fin: $fchFin";
    $_SESSION["dataGrafica"] = buscarDataEficSolesKm($db, $fchIni, $fchFin);
    //$_SESSION["dataDespxMes"] = buscarDataDespachosxMes($db, $fchIni, $fchFin);

    $arrDatos = array();
    //$arrAnhiosMeses = array();
    //print_r($_SESSION["dataGrafica"]);

    foreach ($_SESSION["dataGrafica"] as $key => $value) {
      $arrDatos[] = array(
        "placa" => $value["idPlaca"],
        "despachos" => 0,
        "recorrido" => $value["recorrido"],
        "eficiencia" => $value["eficSoles"],
        "eficEsperada" =>  $value["ratioSolesKm"]
      );
    }
    echo json_encode($arrDatos);

  } else if ($opcion == 'cambiarEstadoDespacho'){
    echo cambiarEstadoDespacho($db, $_POST);
  } else if ($opcion == 'validarIngresoAuxiliares'){
    $usuario = $_SESSION["usuario"];
    //echo validarIngresoAuxiliares($db, $_POST);

    $rpta = validarIngresoAuxiliares($db, $_POST);

    if($rpta != "Si|"){
      $usuario = $_SESSION["usuario"];
    }

    echo $rpta;
  } else if ($opcion == 'buscarInfoParaCerrarForzoso'){
    $arrDatos = buscarInfoParaCerrarForzoso($db, $_POST);
    echo json_encode($arrDatos);
  } else if ($opcion == 'cerrarForzoso'){
    $arrDatos = cerrarForzoso($db, $_POST);
    echo json_encode($arrDatos);
  } else if ($opcion == 'buscarDespVal'){
    $arrDatos = buscarDespVal($db, $_POST);
    echo json_encode($arrDatos);
  } else if ($opcion == 'buscarInfoPagosyCobranzas'){
    $arrDatos = buscarInfoPagosyCobranzas($db, $_POST);
    echo json_encode($arrDatos);
  } else if ($opcion == 'guardarValTripulante'){
    echo guardarValTripulante($db, $_POST);
  } else if ($opcion == 'refrescarValDespDatosGen'){
    $arrDatos = refrescarValDespDatosGen($db);
    echo json_encode($arrDatos);
  } else if ($opcion == 'guardarValidaDespacho'){
    echo guardarValidaDespacho($db);
  } else if ($opcion == 'refrescarValidaTrip'){
    $fchDespacho = $_POST["fchDespacho"];
    $hraIniTrab = $_POST["hraIni"];
    $fchFinTrab = $_POST["fchFin"];
    $hraFinTrab = $_POST["hraFin"];


    $vRol  = $_POST["vRol"];
    $vAdic = $_POST["vAdic"];
    $vHEx  = $_POST["vHEx"];


    $dataProducto = buscarDataIdProducto($db);
    $dataTrabajador = buscarDataTrabajador($db);
    //echo "Categ Trabajador " . $dataTrabajador["categTrabajador"];
    $categTrab = $dataTrabajador["categTrabajador"];
    if($_POST["tipoRol"] == "Conductor" ){
      $valorTrip = $dataProducto["valConductor"];
      $hrasNormales = $dataProducto["hraNormalConductor"];
      $toleranCobroHraExtra = $dataProducto["tolerHraCond"];
      $valHraAdic = $dataProducto["valHraAdicCond"];
    } else if($_POST["tipoRol"] == "Auxiliar"){
      $valorTrip = $dataProducto["valAuxiliar"];
      $hrasNormales = $dataProducto["hraNormalAux"];
      $toleranCobroHraExtra = $dataProducto["tolerHraAux"];
      $valHraAdic = $dataProducto["valHraAdicAux"];
    } else {
      $valorTrip = 0;
      $hrasNormales = 0;
      $toleranCobroHraExtra = 0;
      $valHraAdic = 0;
    }
    $valTotHraExtra = 0;

    //echo "valorHra: $valorHra, hrasNormales: $hrasNormales, tolerHra: $tolerHra, valHraAdic: $valHraAdic";

    if ($hrasNormales != '00:00:00'){
      $hraIniFch      = $fchDespacho." ".$hraIniTrab;
      $hraFinCalculo  = $fchFinTrab." ".$hraFinTrab;
      $duracionhhmmss = restahhmmss($hraIniFch,$hraFinCalculo);
      $tpoExtraHrasDecimalTrab = difEnHorasDecim($hrasNormales,$duracionhhmmss);
      if($toleranCobroHraExtra == "" || $toleranCobroHraExtra == null) $toleranCobroHraExtra = '00:00:00';
      $arrHraTolerancia = explode(":", $toleranCobroHraExtra);
      $tiempoToleranciaEnSegundos = $arrHraTolerancia[0]*3600 + $arrHraTolerancia[1]*60 + $arrHraTolerancia[2];
      $tpoExtraHrasDecimalTrab = ($tpoExtraHrasDecimalTrab <= ($tiempoToleranciaEnSegundos/60))?0:$tpoExtraHrasDecimalTrab;

      $valTotHraExtra = $tpoExtraHrasDecimalTrab * $valHraAdic;
    }

    $usoMaster =  $dataProducto["usoMaster"];
    $esMaster  =  $dataTrabajador["esMaster"];
    if($usoMaster == "Si" && $esMaster == "Si" ){
      $valorTrip = ($valorTrip > $dataTrabajador["precioMaster"]) ? $valorTrip : $dataTrabajador["precioMaster"]; 
    } 

    if ( $categTrab == "Tercero" ){
      $valorTrip = 0;
      $hrasNormales = 0;
      $toleranCobroHraExtra = 0;
      $valHraAdic = 0;
      $valTotHraExtra = 0;
    }

    /*
    echo "<pre>";
    print_r($dataProducto);
    echo "</pre>";
    */
    //echo refrescarValidaTrip($db);
    $arrData = array(
      "valorTrip"  => $valorTrip,
      "valTotHraExtra" => $valTotHraExtra,
    );
    
    echo json_encode($arrData);
  } else if($opcion == 'ctaPdtos'){
    echo ctaPdtos($db);
  } else if ($opcion == 'prepararTripulacionDespacho'){
    echo prepararTripulacionDespacho($db);
  } else if ($opcion == 'guardarOcurrenciaDespacho'){
    echo guardarOcurrenciaDespacho($db);
  } else if ($opcion == 'subirFotoServidor'){
    echo subirFotoServidor($db);
  } else if ($opcion == 'elimDespacho'){
    echo elimDespacho($db);
  } else if ($opcion == 'validarPasarEstadoAnterior'){
    $arrDatos = validarPasarEstadoAnterior($db);
    echo json_encode($arrDatos);
  } else if ($opcion == 'pasarValidadoATerminado'){
    echo pasarValidadoATerminado($db);
  } else if ($opcion == 'nuevoTCombustible'){
    echo nuevoTCombustible($db);
  } else if ($opcion == 'editarTCombustible'){
    echo editarTCombustible($db);
  } else if ($opcion == 'eliminarTCombustible'){
    echo eliminarTCombustible($db);
  } else if ($opcion == 'tCombustible'){
    echo tCombustible($db);
  } else if ($opcion == 'uMedida'){
    echo uMedida($db);
  } else if ($opcion == 'grifos'){
    echo grifos($db);
  } else if ($opcion == 'buscarDataPlacaAbast'){
    echo json_encode(buscarDataPlacaAbast($db));
  } else if ($opcion == 'getIdCotizacion'){
    echo getIdCotizacion($db);
  } else if ($opcion == 'getEmail'){
    echo getEmail($db);
  } else if ($opcion == 'contactosCliente'){
    echo contactosCliente($db);
  } else if ($opcion == 'buscarDataServicios'){
    echo json_encode(buscarDataServicios($db));
  } else if ($opcion == 'buscarDataCliente'){
    echo json_encode(buscarDataCliente($db));
  } else if ($opcion == 'buscarDataContactoCliente'){
    echo json_encode(buscarDataContactoCliente($db));
  } else if ($opcion == 'buscarDataIncluye'){
    echo json_encode(buscarDataIncluye($db));
  } else if ($opcion == 'actualizaComentario'){
    echo actualizaComentario($db);
  } else if ($opcion == 'nuevoNPeaje'){
    echo nuevoNPeaje($db);
  } else if ($opcion == 'editarNPeaje'){
    echo editarNPeaje($db);
  } else if ($opcion == 'eliminarNPeaje'){
    echo eliminarNPeaje($db);
  } else if ($opcion == 'nvoIdOT'){
    echo nvoIdOT($db);
  } else if ($opcion == 'buscarDataServicio'){
    echo json_encode(buscarDataServicio($db));
  } else if ($opcion == 'talleres'){
    echo talleres($db);
  } else if ($opcion == 'buscarDetPreliquid'){
    echo json_encode(buscarDetPreliquid($db));
  }else if ($opcion == 'buscarDataInfraccion'){
    echo json_encode(buscarDataInfraccion($db));
  }else if ($opcion == 'buscarDataTelf'){
    echo json_encode(buscarDataTelf($db));
  }else if ($opcion == 'buscarComisariaTelf'){
    echo buscarComisariaTelf($db);
  }else if ($opcion == 'buscarDataDespachoIdProgram'){
    echo json_encode(buscarDataDespachoIdProgram($db, $_GET['idProgramacion']));
  }

  //buscarDetPreliquid
?>

