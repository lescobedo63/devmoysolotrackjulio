<?php
  $usuario = $_SESSION["usuario"];
  $fecha=Date("Y-m-d");
  $mes = Date("m");
  $anho = Date("Y");
  $dia = Date("d");
   ?>
<html>
  <head>
    <link rel =stylesheet href="librerias/estilos.css" type ="text/css">
    <meta name="generator" content="PSPad editor, www.pspad.com">
    <script language="JavaScript" type="text/javascript" src="librerias/varios.js"></script>
    <script language="JavaScript">
    function enviarAlertas(){
      if(confirm(" Seguro que desea enviar las alertas? \n \n Presione Aceptar para confirmar \n o Cancelar para abortar la operación."))
        abrirventanaflotantemediana('vistas/enviarAlertas.php')
    }
    </script>
    <title>
    </title>
  </head>
  <body>
  
    <?php require_once 'barramenu.php'; 

    if ($_SESSION['ingreso'] == 'Si'){ ?>
 
<section id="prinOpciones">
<div class = 'prinCol'>
 <table width = '100%'>
   <tr>
   	<th>Opciones de Menú:</th>
   </tr>	
   <tr>
   	<td><?php echo "<p>Fecha :$fecha</p><p>Hola, $usuario:" ?></p>
		<p>Usted tiene acceso a cada una de las opciones que puede observar en 
		la parte inferior. Haga clic en la opción que desea utilizar<p></td>
   </tr>	
   <tr>
   	<td align = 'center'>
      <a href="index.php?controlador=aplicacion&accion=mantenimiento">
      <img border="0" src="imagenes/cajaherramientas.jpg" width="80" height="80"></a>
      <a href="index.php?controlador=vehiculos&accion=listar">
      <img border="0" src="imagenes/vehiculo.jpg" width="80" height="80"></a> 
      <a href="index.php?controlador=trabajadores&accion=listar">
      <img border="0" src="imagenes/conductor.jpg" width="80" height="80"></a>
    </td>
   </tr>
   <tr>
    <td align = 'center'>
     <a href="index.php?controlador=clientes&accion=listar">
      <img border="0" src="imagenes/cliente.jpg" width="80" height="80"></a>
    <a href="index.php?controlador=despachos&accion=listar">
      <img border="0" src="imagenes/despacho.jpg" width="80" height="80"></a>
    <a href="index.php?controlador=clientes&accion=otros">
      <img border="0" src="imagenes/otros.jpg" width="80" height="80"></a>
    </td>
   </tr>
   <tr>
    <td align = 'center'>
      <a href="index.php?controlador=clientes&accion=ingresecuentaripley">
      <img border="0" src="imagenes/ripley.png" width="80" height="80"></a>
      <a href="index.php?controlador=aplicacion&accion=ingresemes">    
      <img border="0" src="imagenes/reporte.jpg" width="80" height="80"></a>
    </td>
   </tr>
 </table>	

</div>
<div class = 'prinCol'>
<table width = '100%'>
   <tr>
   	<th>Advertencias Trabajador</th>
   </tr>	
   <tr>
   	<td>Vencimiento Por Días</td>
   </tr>
   <tr>
    <td>
      <div align="left"  style="width: 100%; height: 170px;  overflow:auto">
        <?php 
        foreach($advertVencTrabajadores as $advertencia){
          if ($advertencia['falta'] > 0){
            $resaltar = "resaltarNaranja";
            $texto = " faltan ";       
          } else {
            $resaltar = "resaltarRojo";
            $texto = " hace ";
          }
        //$resaltar = $advertencia['falta'] > 0?"resaltarNaranja":"resaltarRojo";
      echo "<FONT class = '$resaltar'>".$advertencia['nombCompleto'].", ".$advertencia['descripcion']." vence el ".$advertencia['fchFin'].", $texto ". $advertencia['falta']." dias</FONT>
      <A HREF='index.php?controlador=trabajadores&accion=editatrabajador&dni=".$advertencia['idTrabajador']."'>ir</A><br>";
        } ?>
        </div>



    </td>
   </tr>

   <tr>
   	<td>Cumpleaños del Mes</td>
   </tr>	
   <tr>
    <td>

      <div align="left"  style="width: 100%; height: 190px;  overflow:auto">
    <?php 
    foreach($onomasticosdelmes as $onomastico){
      //$resaltar = Date("d",$onomastico['fchNacimiento']) > $dia?"resaltarNaranja":"resaltarRojo";
      echo "<FONT class = 'resaltarNaranja'>".$onomastico['fchNacimiento']." ".$onomastico['nombreCompleto']."</FONT><br>";
    } ?>
    
    </div>
    </td>
   </tr>

 </table>	

</div>
<div class = 'prinCol'>
 <table width = '100%'>
   <tr>
   	<th>Advertencias Vehículo</th>
   </tr>	
   <tr>
   	<td>Vencimiento Licencias Por Días</td>
   </tr>
   <tr>
    <td>
   <div align="left"  style="width:100%; height: 170px;  overflow:auto">
      <?php 
      foreach($advertenciasVehiculos as $advertencia){
        if ($advertencia['falta'] > 0){
          $resaltar = "resaltarNaranja";
          $texto = " faltan ";       
        } else {
          $resaltar = "resaltarRojo";
          $texto = " hace ";
        }
        //$resaltar = $advertencia['falta'] > 0?"resaltarNaranja":"resaltarRojo";
        echo "<FONT class = '$resaltar'>Placa: ".$advertencia['idPlaca'].", ".$advertencia['nombre']." vence el ".$advertencia['fchFin'].", $texto ". $advertencia['falta']." dias</FONT>
        <A HREF='index.php?controlador=vehiculos&accion=vervehiculo&id=".$advertencia['idPlaca']."'>ir</A><br>";
      } ?>
      </div>
      </td>
   </tr>


   <tr>
   	<td>Vencimiento Mantenimientos por Kms.</td>
   </tr>
   <tr>
    <td>
    <div align="left"  style="width: 100%; height: 170px;  overflow:auto">
       <?php 
       foreach($advertenciasVehiculosMantenimientoKms as $advertenciaKms){
         if ($advertenciaKms['falta'] > 0){
          $resaltar = "resaltarNaranja";
          $texto = " faltan ";       
         } else {
          $resaltar = "resaltarRojo";
          $texto = " venció hace ";
         }
         echo "<FONT class = '$resaltar'>Placa: ".$advertenciaKms['idPlaca'].", ".$advertenciaKms['alerta']." vence en ".$advertenciaKms['marcaFin']. " Kms, $texto". $advertenciaKms['falta']." Kms </FONT><A HREF='index.php?controlador=vehiculos&accion=vervehiculo&id=".$advertenciaKms['idPlaca']."'>ir</A><br>";
       } ?>
       </div>
    </td>
   </tr>

   <tr>
    <td>Vencimiento Mantenimientos por Días.</td>
   </tr>
   <tr>
    <td>
    <div align="left"  style="width: 100%; height: 170px;  overflow:auto">
       <?php 
       foreach($advertenciasVehiculosMantenimientoDias as $advertenciaDias){
         if ($advertenciaDias['falta'] > 0){
          $resaltar = "resaltarNaranja";
          $texto = " faltan ";       
         } else {
          $resaltar = "resaltarRojo";
          $texto = " venció hace ";
         }
         echo "<FONT class = '$resaltar'>Placa: ".$advertenciaDias['idPlaca'].", ".$advertenciaDias['alerta']." vence el ".$advertenciaDias['marcaFin']. ", $texto". $advertenciaDias['falta']." Días </FONT><A HREF='index.php?controlador=vehiculos&accion=vervehiculo&id=".$advertenciaDias['idPlaca']."'>ir</A><br>";
       } ?>
       </div>
    </td>
   </tr>
 </table>	

</div>


<div class = 'prinCol'>
  <table width = '100%'>
    <tr>
      <th>Advertencias Abastecimientos</th>
    </tr>
    <tr>
   	  <td>Solicitudes Pendientes</td>
    </tr>
    <tr>
      <td>
        <div align="left"  style="width:100%; height: 170px;  overflow:auto">
          <?php
            foreach($advertenciasSolicitudesAbastecimientos as $solicitudes){
              echo "<FONT class = 'resaltarRojo'>Fecha: " . $solicitudes['fecha'] . 
                                              ", Placa: " . $solicitudes['placa'] . 
                                              ", Conductor: " . $solicitudes['conductor'] . 
                                              ", Grifo: " . $solicitudes['grifo'] . 
                                              ", Estado: ". $solicitudes['estado']."</FONT><br>";
            }
          ?>
        </div>
      </td>
   </tr>
 </table>
</div>


</section>
<?php } ?>
</body>
</html>
