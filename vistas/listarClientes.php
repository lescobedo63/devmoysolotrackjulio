<html>
<head>
  <script language="JavaScript" type="text/javascript" src="librerias/varios.js"></script>
  <link rel =stylesheet href="librerias/jquery-ui-1.8.9.custom.css" type ="text/css">
  <link rel =stylesheet href="librerias/estilos.css" type ="text/css">
   <script>
  $(document).ready(function(){
    //alert("Todo Ok");
    $('#txtRuc').autocomplete({
      minLength: 2,
      source : 'clientesAjax.php'
      //source : ['Maria','Jose','Rocio']  
    });
  });  
 </script>
  
</head>
<body>
  <table class = 'fullRD' >
    <tr><th align = 'center'><?php require_once 'barramenu.php';  ?></th></td>
  </table>
  <table class = 'fullRD' >
    <tr>
      <th  class = "pagina"  align="center" colspan = '8' >
      
      <?php if($_SESSION["ingreso"] == "Si"  ) { ?> 
      <img border="0" src="imagenes/mas.jpg" width="25" height="25" align="right" onclick =  "abrirventanaflotantegrande('index.php?controlador=clientes&accion=nuevocliente') ">
      <img border="0" src="imagenes/volver.jpg" width="25" height="25" align="right" onclick =  "abrirventana('index.php?controlador=clientes&accion=listar')">

      <?php } ?>
      LISTADO DE CLIENTES : <?php echo count($items)?>
      </th>
    </tr>   
    <tr>
      <form method="POST" action="index.php?controlador=clientes&accion=listar">
  	   <th colspan = "3" align = "left">
	      RUC o Nombre&nbsp;<input type="text" name="txtRuc" id="txtRuc" size="40">
	     </th> 
       <th width="300"></th>
       <th width="100"></th>
       <th colspan = "3">
       <input type="submit" value="Enviar" name="B1"><input type="reset" value="Limpiar" name="B2">
       </th>
      </form> 
    </tr>
	  <tr>
	   <th width="70">RUC</th>
     <th width="175">Nombre</th>
     <th width="270">Rz. Social</th>
     <th width="310">Dirección</th>
     <th width="100">Distrito</th>
     <th width="100">Cond. Pago</th>
     <th width="70">Estado</th>
     <th>Acción</th>
    </tr>
  </table>
  <div align="left" class= 'fullRD'>
    <table class = 'fullRD'>
      <?php
        $color = 'celeste';    
      	foreach($items as $item) { 
      	$color = ($color == 'plomo') ? 'celeste' : 'plomo';
	        echo "<tr>";
		      echo "<td class = '$color' width='70'>".$item['idRuc']."</td>";
		      echo "<td class = '$color' width='175'>".$item['nombre']."</td>";
          echo "<td class = '$color' width='270'>".$item['rznSocial']."</td>";
		      echo "<td class = '$color' width='310'>".$item['dirCalleNro']."</td>";
		      echo "<td class = '$color' width='100'>".substr($item['dirDistrito'],0,15)."</td>";
		      echo "<td class = '$color' width='100'>".$item['nombCondicion']."</td>";
		      echo "<td class = '$color' width='70'>".$item['estadoCliente']."</td>";
		      echo "<td class = '$color' >";
		      if($_SESSION["edicion"] == "Si"    ) {
		    ?>  
            <img class ="titulo" title ="Editar|Permite editar los campos del Cliente." border="0" src="imagenes/lapiz.jpg" width="15" height="15" onclick =  "abrirventana('index.php?controlador=clientes&accion=vercliente&id=<?php echo $item['idRuc'] ?> ') "  >
           <?php }
           if($_SESSION["admin"] == "Si"){ ?> 
            <img class ="titulo" title ="Eliminar|Elimina el registro del Cliente." border="0" src="imagenes/menos.jpg" width="15" height="15" onclick =  "abrirventanaflotante('index.php?controlador=clientes&accion=eliminacliente&id=<?php echo $item['idRuc'] ?>')">
            <?php } ?>
          </td>
       </tr>
	   <?php
	     }
	   ?>
    </table>     
  </div>
      <div align="left" class= 'movil'>
     <table class = 'fullRD'>
      <?php
        $color = 'celeste';   
        foreach($items as $item) { 
          $color = ($color == 'plomo') ? 'celeste' : 'plomo';
          echo "<tr>";
          echo "<td class = '$color'>";
          echo "<table class = 'fullRD'>";
          echo "<tr>";
          echo "<td class = 'celdaTit' width = '22%' >RUC</td>";
          echo "<td>".$item['idRuc']."</td>";
          echo "</tr>";
          echo "<tr>";
          echo "<td class = 'celdaTit'>Nombre</td>";
          echo "<td>".$item['nombre']."</td>";
          echo "</tr>";
          echo "<tr>";
          echo "<td class = 'celdaTit'>Rzn. Social</td>";
          echo "<td>".$item['rznSocial']."</td>";
          echo "</tr>";
          echo "<tr>";
          echo "<td class = 'celdaTit'>Dirección</td>";
          echo "<td>".$item['dirCalleNro']."</td>";
          echo "</tr>";
          echo "<tr>";
          echo "<td class = 'celdaTit'>Distrito</td>";
          echo "<td>".substr($item['dirDistrito'],0,15)."</td>";
          echo "</tr>";
          echo "<tr>";
          echo "<td class = 'celdaTit'>Contacto</td>";
          echo "<td>".$item['idNombreCompleto']."</td>";
          echo "</tr>";
          echo "<tr>";
          echo "<td class = 'celdaTit'>Teléfono</td>";
          echo "<td>".$item['telefono']."</td>";
          echo "</tr>";
          echo "<tr>";
          echo "<td colspan = '2'>";

          if($_SESSION["edicion"] == "Si"    ) { 
          ?>
            <img class ="titulo" title ="Editar|Permite editar los campos del Cliente." border="0" src="imagenes/lapiz.jpg" width="15" height="15" onclick =  "abrirventana('index.php?controlador=clientes&accion=vercliente&id=<?php echo $item['idRuc'] ?> ') "  >
          <?php }
          if($_SESSION["admin"] == "Si"){ 
           ?>  
            <img class ="titulo" title ="Eliminar|Elimina el registro del Cliente." border="0" src="imagenes/menos.jpg" width="15" height="15" onclick =  "abrirventanaflotante('index.php?controlador=clientes&accion=eliminacliente&id=<?php echo $item['idRuc'] ?>')">
          <?php }         

          echo "</td>";
          echo "</tr>";
          echo "</table>";
          echo "<tr>";

      }?>
    </table> 
    </div>

</body>
</html>
