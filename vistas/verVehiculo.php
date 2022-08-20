<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <script language="JavaScript" type="text/javascript" src="librerias/varios.js"></script>
  <link rel =stylesheet href="librerias/estilos.css" type ="text/css">
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <title></title>

  <script>
    function validaEntradas(forma){
    if(forma.txtRznSocial.value == '-'){
    	alert("Debe especificar la razón social");
      forma.txtRznSocial.focus();
      return false;
    }	else if(forma.txtPropietario.value == "" ){
      alert("Debe ingresar el propietario");
      forma.txtPropietario.focus();
      return false;
    } else if(forma.txtRznSocial.value != forma.propio.value && forma.txtCtoDia.value == 0 ){
      alert("Cuando se elije un tercero debe ingresar un Costo Día para la unidad");
      forma.txtCtoDia.focus();
      return false; 
    } else if (forma.txtInteriorLargo.value == '' || forma.txtInteriorLargo.value == '0'){
      alert("Falta ingresar el valor del Largo Interior");
      forma.txtInteriorLargo.focus();
      return false; 
    } else if (forma.txtInteriorAlto.value == '' || forma.txtInteriorAlto.value == '0'){
      alert("Falta ingresar el valor del Alto Interior");
      forma.txtInteriorAlto.focus();
      return false; 
    } else if (forma.txtInteriorAncho.value == '' || forma.txtInteriorAncho.value == '0'){
      alert("Falta ingresar el valor del Ancho Interior");
      forma.txtInteriorAncho.focus();
      return false; 
    } else if (forma.txtBase.value == ''){
      alert("Falta ingresar la Base Moy");
      forma.txtBase.focus();
      return false; 
    } else if (forma.txtAnioFabricacion.value == ''){
      alert("Falta ingresar el Ańo de Fabricación");
      forma.txtAnioFabricacion.focus();
      return false; 
    } else if (forma.txtColor.value == ''){
      alert("Falta ingresar el Color del Vehículo");
      forma.txtColor.focus();
      return false; 
    } else if (forma.txtPesoCargaReal.value == ''){
      alert("Falta ingresar el peso de carga real");
      forma.txtPesoCargaReal.focus();
      return false; 
    } else if (forma.txtCarroceria.value == ''){
      alert("Falta ingresar el tipo de Carrocería");
      forma.txtCarroceria.focus();
      return false; 
    } else if (forma.txtPuertaCarr.value == ''){
      alert("Falta indicar el tipo de Puertas");
      forma.txtPuertaCarr.focus();
      return false; 
    }
    return true;
  }

  function enviarAdjunto(id){
    email = prompt("Ingrese email")
    if (email != null && email != ''){
      mensaje = prompt("Si desea, ingrese un breve texto para el cuerpo del correo")
        if (mensaje != null )
          abrirventanaflotante('vistas/enviarEscaneoLicencia.php?id='+id+'&email='+email+'&mensaje='+mensaje)
        else
          alert("Usted ha cancelado el envío del correo")  	
    } else {
      alert("Debe ingresar un email")
    }
  }

  $(document).ready(function(){
    $( "#tabsDocs" ).tabs();
    $(".classOperacion").show(350);
    $("#txtRznSocial").change(function () {
    	valor = $("#txtRznSocial").val();
      if (valor == 'INVERSIONES MOY S.A.C.'){
        $(".classOperacion").hide("slow");
      } else {
      	$(".classOperacion").show(350); 
      }   
    });

    $('#txtEstado').change(function(){
      estado = jQuery(this).val()
      idPlaca = '<?php echo $idPlaca; ?>'
      if(estado == 'Activo'){
        var parametros = {
          "idPlaca":idPlaca,
        };

        $.ajax({
          data: parametros,
          url:  './vistas/ajaxVarios_v2.php?opc=verificarEstadoDueno',
          timeout: 15000,
          type: 'post',
          success:  function (response) {
            var obj = JSON.parse(response);
            if(obj.estadoTercero == 'Inactivado'){

              swal({
                type: "error",
                title: "ˇEl Propietario está inactivado!",
                text: "Active al Propietario y luego active el vehículo",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                $("#txtEstado").val("Inactivo")
              });

            }
            
          }
        })       

      }

    })

  });

  </script>

  </head>
  <body>
  <?php 
    require_once 'barramenu.php';
    foreach($vehiculo as $item) {
      $fecha=Date("Y-m-d"); 
  ?>
  <form method="POST" action="_editaVehiculo.php" enctype="multipart/form-data" onSubmit ="return validaEntradas(this);">
    <table border = '1' width="1210" height="660" class = 'mejor'>
    <tr>
      <th class = "pagina"  align="center" height="23"  colspan="6">
        <img border="0" src="imagenes/volver.jpg" width="25" height="25" align="right" onclick =  "abrirventana('index.php?controlador=vehiculos&accion=nuevolistarVehiculos') ">
      <b>Ver Vehículo <?php echo $idPlaca ?></b></th>
    </tr>
    <tr>
      <th height="23" width="400"  colspan="2"><a class='clueTituloBlanco' href="#" title="Datos Generales|Muestra los datos del vehículo.| Si desea modificar algún dato haga clic en el cuadro de texto que desea|modificar y luego presione Enviar. No se puede modificar el N° de placa."><b>Datos Generales</b></a><br></th>
	    <td height="151"  colspan="4" rowspan="6" width = '800'>
        <div class = "varios2b">
		    <table border="1" width="750" height="20" >
		      <tr>
				    <th class = "principal" colspan="5" width="400" align="center" ><a class='clueTituloBlanco' href="#" title="Permisos|Muestra los permisos del vehículo tales como SOAT entre otros.| Si desea ingresar un nuevo permiso haga clic en el ícono (+)y |luego presione F5 para actualizar.">
                <img border="0" src="imagenes/mas.jpg" width="20" height="20" align="right" onclick =  "abrirventanaflotantemediana('index.php?controlador=vehiculos&accion=nuevalicencia&id=<?php echo $idPlaca ?>') "> 
                 Permisos   </a>
            </th>
			    </tr>
			    <tr>
				    <th width="120" align="center"><b>Nombre</b></th>
				    <th width="80" align="center"><b>F. Inicio</b></th>
		    		<th width="80" align="center"><b>F. Venc.</b></th>
			    	<th width="180" align="center"><b>Observación</b></th>
				    <th align="center">.</th>
			    </tr>
			<?php
			$color = 'celeste';   
      foreach($licencias as $licencia){
        $color = ($color == 'plomo') ? 'celeste' : 'plomo';
        $escaneo =  $licencia['escaneo'];
			  echo "<tr>";
			  echo "<td class = '$color' width='120' >".$licencia['nombre']."</td>";
       	echo "<td class = '$color' align='center' width='80' >".$licencia['fchInicio']."</td>";
   	 	  if ($licencia['fchFin'] <= $fecha )
			    $colorFecha = 'rojo';
			  else   
			    $colorFecha = $color;
	  		echo "<td class = '$colorFecha' align='center' width='80' >".$licencia['fchFin']."</td>";
	    	echo "<td class = '$color' width='190' >".$licencia['observacion']."</td>";
	  	  echo "<td class = '$color' >";
			 ?>
        <img class="sinTitulo" title="Para dar por finalizado| el periodo de la licencia" border="0" src="imagenes/lapiz.jpg" width="13" height="13" onclick =  "abrirventanaflotante('index.php?controlador=vehiculos&accion=editalicencia&idplaca=<?php echo $licencia['idPlaca'] ?>&fchinicio=<?php echo $licencia['fchInicio'] ?>&nombre=<?php echo $licencia['nombre'] ?> ')">
        <img class="sinTitulo" title="Elimina el registro de la licencia" border="0" src="imagenes/menos.jpg" width="13" height="13" onclick =  "abrirventanaflotante('index.php?controlador=vehiculos&accion=eliminalicencia&idplaca=<?php echo $licencia['idPlaca'] ?>&fchinicio=<?php echo $licencia['fchInicio'] ?>&nombre=<?php echo $licencia['nombre'] ?> ')">
        <?php
        if ($escaneo == "")
          echo "ND";
        else { ?>
        <img class="sinTitulo" title="Descargar el documento escaneado" border="0" src="imagenes/descargar.png" width="13" height="13" onclick =  "abrirventanaflotante('vistas/descargarEscaneo.php?id=<?php echo $licencia['escaneo'] ?>')">
        <?php 
             } ?>     
        <img class="sinTitulo" title="Para subir y reemplazar| el documento escaneado" border="0" src="imagenes/subir.png" width="13" height="13" onclick =  "abrirventanaflotante('index.php?controlador=vehiculos&accion=cambiarAdjunto&idplaca=<?php echo $licencia['idPlaca'] ?>&fchinicio=<?php echo $licencia['fchInicio'] ?>&nombre=<?php echo urlencode($licencia['nombre']) ?> ')">
        <img class="sinTitulo" title="Enviar el documento escaneado|al email que se indique " border="0" src="imagenes/email.gif" width="13" height="13" onclick =  "enviarAdjunto('<?php echo $licencia['escaneo'] ?>')">

        </td>	 
        <?php echo "</tr>";
       } ?>   
		</table>
      </div>
      </td>
    </tr>
    <tr>
	  	<td class = 'rojo' height="23" width="150" >Placa (*)</td>
	  	<td height="23" width='250' ><?php echo $item['idPlaca'] ?></td>
 		</tr>
  	<tr>
      <td height="23" >Nro. Vehículo</td>
      <td height="23" ><input type="text" name="txtNroVehiculo" value = '<?php echo $item['nroVehiculo'] ?>' size="20"></td>
  	</tr>
	 <tr>
    <td class = 'rojo' height="23" >Propietario (*)</td>
		<td height="23" ><input type="text" name="txtPropietario" value = '<?php echo $item['propietario'] ?>' size="40"></td>
		
	</tr>
	<tr>
		<td class = 'rojo' height="5" >Rzn. Social (*)</td>
		<td height="5" >
      <select size="1" name="txtRznSocial" id="txtRznSocial" style="position: relative; width: 275; height:22" >
		    <option><?php echo $item['rznSocial']; ?></option>
		    <option><?php echo $empresa; ?></option>
		    <?php foreach($duenos as $dueno) { ?>
			     <option value="<?php echo $dueno['documento']; ?>"><?php echo $dueno['documento'].'-'.$dueno['nombreCompleto']  ; ?></option>
        <?php }  ?>
		  </select>
    
    </td>
	</tr>
	<tr>
		<td height="26" class = 'rojo' >Considerar Propio (*)</td>
		<td height="26" >
      <select size="1" name="cmbConsiderarPropio" id="cmbConsiderarPropio" style="position: relative; width: 100; height:22" >
        <option <?php if ($item['considerarPropio'] == 'Si') echo "SELECTED" ?> >Si</option>
        <option <?php if ($item['considerarPropio'] == 'No') echo "SELECTED" ?> >No</option>
      </select>
		
    </td>
		</tr>
	<tr>
		<td height="26"  >Nro. Motor</td>
    <td height="26" >
    <input type="text" name="txtNroMotor" value = '<?php echo $item['nroMotor'] ?>' size="20"></td>
    <th height="26"  colspan="2" width = '400'><a class='clueTituloBlanco' href="#" title="Dimensiones según tarjeta|Muestra las medidas del vehículo |según su tarjeta de circulación."><b>Dimensiones según tarjeta (en mts.)</b></a></th>
		<th height="26"  colspan="2" width = '400'> <a class='clueTituloBlanco' href="#" title="Dimensiones del espacio de carga|Muestra las medidas reales del espacio de carga del vehículo."><b>Dimensiones del espacio de carga (mts.)</b></a></th>	
	</tr>
	<tr>
		<td height="26" >Nro. Serie</td>
    <td height="26"  >
    <input type="text" name="txtNroSerie" value = '<?php echo $item['nroSerie'] ?>' size="20"></td>
    <td height="26" width="150" >Largo</td>
    <td height="26"  width="250">
		  <input type="text" name="txtLargo" value = '<?php echo $item['dimLargo'] ?>' size="20"></td>
    <td class = 'rojo' height="11" width="150" >Largo Interior (*)</td>
		<td height="15"  >
		<input type="text" name="txtInteriorLargo" value = '<?php echo $item['dimInteriorLargo'] ?>' size="20"></td>
	</tr>
	<tr>
		<td height="26" >Marca</td>
    <td height="26" ><input type="text" name="txtMarca" value = '<?php echo $item['marca'] ?>' size="20"></td>
    <td height="26" >Alto</td>
		<td height="26" ><input type="text" name="txtAlto" value = '<?php echo $item['dimAlto'] ?>' size="20"></td>
    <td class = 'rojo' height="11" >Alto Interior (*)</td>
		<td height="15"  >
		<input type="text" name="txtInteriorAlto" value = '<?php echo $item['dimInteriorAlto'] ?>' size="20"></td>
	</tr>
	<tr>
		<td height="26" >Modelo</td>
    <td height="26"  ><input type="text" name="txtModelo" value = '<?php echo $item['modelo'] ?>'  size="20"></td>
    <td height="26" >Ancho</td>
		<td height="26"  >
		  <input type="text" name="txtAncho" value = '<?php echo $item['dimAncho'] ?>' size="20"></td>
    <td class = 'rojo' height="13" >Ancho Interior (*)</td>
		<td height="18"  >
		  <input type="text" name="txtInteriorAncho" value = '<?php echo $item['dimInteriorAncho'] ?>' size="20"></td>
	</tr>
	<tr>
		<td class = 'rojo' height="26" >Ańo Fabricación (*)</td>
    <td height="26" ><input type="text" name="txtAnioFabricacion" value = '<?php echo $item['anioFabricacion'] ?>' size="20"></td>
    	
		<th height="26"  colspan="4"> <a class='clueTituloBlanco' href="#" title="Dimensiones del espacio de carga|Muestra las medidas reales del espacio de carga del vehículo.">
		<b>OTROS</b></a></th>	
	</tr>
	<tr>
		<td class = 'rojo' height="26" >Color (*)</td>
    <td height="26"  ><input type="text" name="txtColor" value = '<?php echo $item['color'] ?>' size="20"></td> 
    
		<td class = 'rojo' height="23" >Base Moy (*)</td>
		<td height="15"  >
		<select size="1" name="txtBase" id="txtBase" style="position: relative; width: 100; height:22" >
       <option <?php if ($item['base'] == 'VES') echo "SELECTED" ?> >VES</option>
		   <option <?php if ($item['base'] == 'SAN MIGUEL') echo "SELECTED" ?> >SAN MIGUEL</option>
		 </select>


	</td>

    <?php if ($item['rznSocial'] != $empresa) {   ?>
		<td  class = 'rojo' height="23" >Costo Día (*)</td>
		<td height="23" ><input type="text" name="txtCtoDia" value = '<?php echo $item['costoDia'] ?>' size="20"></td>
		<?php } else { ?>
		<td height="23" >&nbsp;</td>
		<td height="23"  >&nbsp;</td>
		<?php } ?>


	</tr>
	<tr>
		<td class = 'rojo' height="27" >Carrocería (*)</td>
    <td height="27"  ><input type="text" name="txtCarroceria" value = '<?php echo $item['carroceria'] ?>' size="20"></td>
    <td height="11" >Cilindros</td>
		<td height="15" >
		  <input type="text" name="txtCilindros" value = '<?php echo $item['cilindros'] ?>' size="20"></td>
		<td height="26" >C.c.</td>
    <td height="26"  ><input type="text" name="txtCc" value = '<?php echo $item['cc'] ?>' size="20"></td>
	</tr>
	<tr>
		<td class = 'rojo' height="18" >Puerta Carrocería (*)</td>
    <td height="18" ><input type="text" name="txtPuertaCarr" value = '<?php echo $item['puertaCarr'] ?>' size="20"></td>
    <td height="13"  >Hp</td>
		<td height="18"  >
		  <input type="text" name="txtHp" value = '<?php echo $item['hp'] ?>' size="20"></td>
		<td height="26" >M3 Facturable<img width="25" height="25" border="0" align="right" src="imagenes/flechaAnimada.gif"></td>
    <td height="26" >
      <input type="text" name="txtM3Fact" size="10" value = '<?php echo $item['m3Facturable'] ?>' ></td>
	</tr>

	<tr>
    <td height="18" >Clase</td>
    <td height="18"  ><input type="text" name="txtClase" value = '<?php echo $item['clase'] ?>' size="20">
      </td> 

		<td height="11"  >Nro. Ruedas</td>
		<td height="15"  >
		  <input type="text" name="txtNroRuedas" value = '<?php echo $item['nroRuedas'] ?>' size="20"></td>
		<td height="26" >Llantas</td>
    <td height="26"  ><input type="text" name="txtLlantas" value = '<?php echo $item['llantas'] ?>' size="20"></td>
	</tr>

	<tr>
		<td height="23" >Descripción</td>
    <td height="64"   rowspan="3">
      <textarea rows="3" name="txtDescripcion" cols="30"><?php echo $item['descripcion'] ?></textarea></td> 
		<td height="15" >Pulg. del Aro</td>
		<td height="1"  >
		  <input type="text" name="txtPulg" value = '<?php echo $item['pulg'] ?>' size="20"></td>
		<td height="23" >
		  <div class = 'classOperacion'>
		    Con Personal Moy
		  </div>
		</td>
		<td height="23" > 
		  <div class = 'classOperacion'>
		<?php

		  echo "<select size='1' name='cmbTerceroConPersonalMoy' style='position: relative; width:120; height:22' >";
      echo opcionesEnum($db,'vehiculotercero','terceroConPersonalMoy',$item['terceroConPersonalMoy']);
      echo "</select>";

		?>
		  </div>
		</td> 
	</tr>

	<tr>
		<td height="13"  ></td>	
		<td height="15" >Capac. Combustible Gal.</td>
		<td height="1"  >
		  <input type="text" name="txtCombustibleGal" value = '<?php echo $item['capCombustible'] ?>' size="20"></td>
		<td height="26" colspan= '2' >Img <input type="file" name="imgVehiculo" id="imgVehiculo"  /></td>	
	</tr>
  <tr>
		<td height="13"  ></td>
	  <td height="23"  >Rendimiento</td>
		<td height="23" >
		  <input type="text" name="txtRendimiento" value = '<?php echo $item['rendimiento'] ?>' size="20"></td>
		<td   colspan="2" rowspan="9">
      <?php 
        $cadena = $item['imgVehiculo'];
        $nombre = strtok($cadena,".");
        $extension = strtok(".");
        if ($extension == "pdf" ){ ?>
          <embed src="imagenes/data/vehiculo/<?php echo $item['imgVehiculo'];?>" type="application/pdf" width="350" height="250px" /> 
      <?php
        } else if ($extension == "jpeg" or $extension == "jpg" or $extension == "gif"  ){ ?>
          <img src="imagenes/data/vehiculo/<?php echo $item['imgVehiculo'];?>" width="350" height="250"  >
      <?php } ?>  
    </td>
	</tr>

	<tr>
    <td height="13"  >Estado</td>
    <td height="23" >
      <?php if(isset($_SESSION[$_SESSION['usuario']."MoyPermisos"]['admVehiculo']) && $_SESSION[$_SESSION['usuario']."MoyPermisos"]['admVehiculo'] > 0){ ?>
        <select size="1" name="txtEstado" id="txtEstado" style="position: relative; width: 100; height:22" >
          <option selected><?php echo $item['estado']; ?></option>
          <option <?php if ($item['estado'] =="Activo") {echo "selected" ;}?> >Activo</option>
          <option <?php if ($item['estado'] =="Inactivo") {echo "selected" ;}?> >Inactivo</option>
        </select>
      <?php } else { echo $item['estado']; ?>


      <?php }  ?>


    </td>
		
		<td height="23"  >Grupo Rendimiento</td>
	  	<td height="23"  >
		    <select size="1" name="txtGrupoRendimiento" id="txtGrupoRendimiento" style="position: relative; width: 100; height:22" >
         	<option><?php echo $item['grupoRendimiento']; ?></option>
         	<?php foreach($gruposRendimiento as $grupo) {  ?>
         	<option value="<?php echo $grupo['grupoRendimiento']; ?>"><?php echo $grupo['grupoRendimiento']; ?></option>
		      <?php }?>
		    </select>
   		</td>
		
	</tr>

	<tr>
    <td height="13" >Check Alertas</td>
    <td height="23" >
     <select size="1" name="txtCheckAlertas" id="txtCheckAlertas" style="position: relative; width: 100; height:22" >
        <option selected><?php echo $item['checkAlertas'];?></option>
        <option>Si</option>
        <option>No</option>
     </select>
    </td>
		
		<td height="11"  >Eficiencia Combustible</td>
		<td height="11"   >
      <select size="1" name="txtEficCombustible" id="txtEficCombustible" style="position: relative; width: 150; height:22" >
		    <option <?php if ($item['eficCombustible'] =="Normal") {echo "selected" ;}?>>Normal</option>
		    <option  <?php if ($item['eficCombustible'] =="Alto") {echo "selected" ;} ?>>Alto</option>
		  </select>
    </td>
	</tr>
	<tr>
    <td height="13" ></td>
    <td height="23" ></td>		
    <td height="30" >Km. Ultima Medición</td>
		<td height="30" >
      <?php
      //echo "El permiso ".$_SESSION[$_SESSION['usuario']."MoyPermisos"]['kmUltMedicion'];
      if(isset($_SESSION[$_SESSION['usuario']."MoyPermisos"]['kmUltMedicion']) && $_SESSION[$_SESSION['usuario']."MoyPermisos"]['kmUltMedicion'] > 0){
        $puedeEditarUltKm = "Si";
      } else {
        $puedeEditarUltKm = "No";
      }

      ?>
      <input type="text" name="txtKmUltimaMedicion" value = '<?php echo $item['kmUltimaMedicion'] ?>'  
      <?php
        $arrSuperAdmin = explode(",", $usuariosSuperAdmin);
        if (!(in_array($_SESSION['usuario'], $arrSuperAdmin) || $puedeEditarUltKm == "Si" )) echo " readonly ";
      ?>
      size="15"> Km</td>
	</tr>

	<tr>
    <td height="13" ></td>
    <td height="23" ></td>	
    <td height="30" >Eficiencia Esperada Soles/km</td>
		<td height="30" >
      <select size="1" name="cmbEficSolesKm" id="cmbEficSolesKm" style="position: relative; width: 150; height:22" >
        <<option value=""></option>
        <?php
          foreach ($eficSolesKm as $key => $value) {
            echo "<option value = ".$value["id"]." ".($item["idEficSolesKm"]==$value["id"] ? "SELECTED" : "" )." >".$value["nombre"]."</option>";
          }

        ?>

        </select>  
    </td>
	</tr>
  <tr>
    <td height="13" ></td>
    <td height="23" ></td>  
    <td height="26"  >
      <div class = 'classOperacion'>
        Modalidad Acuerdo
      </div>
      </td>
    <td height="26" >
      <div class = 'classOperacion'>
        <select size="1" name="cmbModoAcuerdo" id="cmbModoAcuerdo" style="position: relative; width: 100; height:22" >
          <?php echo opcionesEnum($db, 'vehiculotercero', 'modoAcuerdo', $item['modoAcuerdo']); ?>
        </select>
      </div>
    </td>
  </tr>
  <tr>
    <td height="13" ></td>
    <td height="23" ></td>  
    <td height="30" ></td>
    <td height="30" ></td>
  </tr>
  <tr>
    <td height="13" ></td>
    <td height="23" ></td>  
    <td height="30" ></td>
    <td height="30" ></td>
  </tr>
  <tr>
    <th height="23"  colspan="2"><a class='clueTituloBlanco' href="#" title="Capacidad de Carga|Muestra los datos del peso que tiene el vehículo."><b>Capacidad de Carga (Kgrs.)</b></a></th>
    <td height="30" >&nbsp;</td>
    <td height="30" >&nbsp;</td>
  </tr>

	<tr>
		<td height="23"  >Peso Seco</td>
    <td height="23" >
      <input type="text" name="txtPesoSeco" value = '<?php echo $item['pesoSeco'] ?>' size="15"> Kgr</td>
    <td height="146"  colspan="4" rowspan="8">
		  <div id="tabsDocs">
              <ul>
              <li><a href="#tbDoc1">Mantenimiento</a></li>
              <li><a href="#tbDoc2">Otros documentos</a></li>
              </ul>
              <div id="tbDoc1" class = 'varios'>
		    <table border="1" width="100%" height="20" >
		      <tr>
			    <th class = "principal" colspan="6" align="center">  <a class='clueTituloBlanco' href="#" title="Mantenimiento|Este listado contiene las alertas del vehículo, tales como|el cambio de aceite, llantas, etc.| Si desea ańadir una nueva alerta haga clic en el ícono (+)| Si ańade una nueva alerta, luego presione F5 para actualizar.">
            <img border="0" src="imagenes/mas.jpg" width="20" height="20" align="right" onclick =  "abrirventanaflotante('index.php?controlador=vehiculos&accion=nuevomantenimiento&id=<?php echo $idPlaca ?>') ">
            Mantenimiento</a>
          </th>
			  </tr>
			  <tr>
			    <th width="68" align="center"><b>Nombre</b></th>
				  <th width="44" align="center"><b>Uni</b></th>	
 		  	  <th width="50" align="center"><b>Fin</b></th>
	  			<th width="200" align="center"><b>Observación</b></th>
			  	<th  align="center">.</th>
				</tr>
				  <?php
				  $color = 'celeste';   
          foreach($mantenimientos as $mantenimiento) {
            $color = ($color == 'plomo') ? 'celeste' : 'plomo';
				    echo "<tr>";
					  echo "<td class = '$color' >".$mantenimiento['alerta']."</td>";
					  echo "<td class = '$color' >".$mantenimiento['unidad']."</td>";
					  echo "<td class = '$color' >".$mantenimiento['marcaFin']."</td>";
					  echo "<td class = '$color' >".$mantenimiento['observacion']."</td>";
					  echo "<td class = '$color' >";
				  ?>
          <img border="0" src="imagenes/lapiz.jpg" width="13" height="13" onclick =  "abrirventanaflotantegrande('index.php?controlador=vehiculos&accion=editamantenimiento&idplaca=<?php echo $mantenimiento['idPlaca'] ?>&marcaFin=<?php echo $mantenimiento['marcaFin'] ?>&alerta=<?php echo $mantenimiento['alerta'] ?>')">
          <img border="0" src="imagenes/menos.jpg" width="13" height="13" onclick =  "abrirventanaflotante('index.php?controlador=vehiculos&accion=eliminamantenimiento&idplaca=<?php echo $mantenimiento['idPlaca'] ?>&marcaFin=<?php echo $mantenimiento['marcaFin'] ?>&alerta=<?php echo $mantenimiento['alerta'] ?>')">	  
				  	 
          <?php  echo "</tr>";             
             } ?>   
			 </table>
      </div>
      <div id="tbDoc2" class = 'varios'>

      <table border="1" width="100%" height="20" >
        <tr>
          <th class = "principal" colspan="4" align="center">
            <img border="0" src="imagenes/mas.jpg" width="20" height="20" align="right" onclick =  "abrirventanaflotantemediana('index.php?controlador=vehiculos&accion=nuevodocumentoadjunto&id=<?php echo $idPlaca ?>') ">
            Documentos Varios Adjuntos
          </th>
        </tr>
        <tr>
          <th align="center"><b>Código</b></th>
          <th align="center"><b>Nombre</b></th>
          <th align="center"><b>Descripción</b></th>
          <th align="center">Acción</th>
        </tr>
          <?php
          $color = 'celeste';   
          foreach($docsvariosadjun as $docItem) {
            $color = ($color == 'plomo') ? 'celeste' : 'plomo';
            $escaneo = $docItem['escaneo'];
            echo "<tr>";
            echo "<td class = '$color' >".$docItem['id']."</td>";
            echo "<td class = '$color' >".$docItem['nombre']."</td>";
            echo "<td class = '$color' >".$docItem['descripcion']."</td>";
            echo "<td class = '$color' >";
          ?>
            <img border="0" src="imagenes/lapiz.jpg" width="13" height="13" onclick = "abrirventanaflotantemediana('index.php?controlador=vehiculos&accion=editadocumentoadjunto&id=<?php echo$docItem['id'] ?>')">
            <img border="0" src="imagenes/menos.jpg" width="13" height="13" onclick =  "abrirventanaflotante('index.php?controlador=vehiculos&accion=eliminadocumentoadjunto&id=<?php echo $docItem['id'] ?>')">
            <?php
              if ($escaneo == "")
                echo "ND";
              else { ?>
                <img class="sinTitulo" title="Descargar el documento escaneado" border="0" src="imagenes/descargar.png" width="13" height="13" onclick =  "abrirventanaflotante('vistas/descargarEscaneoVehiculo.php?id=<?php echo $escaneo ?>')">
              <?php 
                   } ?>
            <img class="sinTitulo" title="Para subir y reemplazar| el documento escaneado" border="0" src="imagenes/subir.png" width="13" height="13" onclick =  "abrirventanaflotante('index.php?controlador=vehiculos&accion=cambiarAdjuntoDocumento&id=<?php echo  $docItem['id'] ?>')">
            </td>  
          <?php  echo "</tr>";
           } ?>   
        </table>
      </div>

		</td>
	</tr>

	<tr>
    <td>Peso Util</td>
    <td><input type="text" name="txtPesoUtil" value = '<?php echo $item['pesoUtil'] ?>' size="15"> Kgr</td>
  </tr>

  <tr>
    <td>Peso Bruto</td>
    <td><input type="text" name="txtPesoBruto" value = '<?php echo $item['pesoBruto'] ?>' size="15"> Kgr</td>
  </tr>

  <tr>
    <td class = 'rojo' >Peso Carga Real (*)</td>
    <td><input type="text" name="txtPesoCargaReal" value = '<?php echo $item['pesoUtilReal'] ?>' size="15"> Kgr</td>
  </tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>	
	</tr>
    <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td> 
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>  
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>  
  </tr>


	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;<input type="submit" value="Enviar" name="btnEnviar"><input type="reset" value="Limpiar" name="B2"></td>
		<td colspan = '2' class = 'rojo'>(*) Son campos obligatorios</td>
	</tr>

	</table>
  <input type=hidden name=idPlaca size=94 value = '<?php echo $idPlaca?>'>
  <input type=hidden name=propio size=94 value = '<?php echo $empresa ?>'>
  <input type=hidden name=estadoAnterior size=94 value = '<?php echo $item['estado'] ?>'>

</form>
<?php } ?>

  </body>
</html>
