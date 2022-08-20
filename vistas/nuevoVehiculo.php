<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <link rel =stylesheet href="librerias/estilos.css" type ="text/css">
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <title></title>
  <script type="text/javascript">
 
    function validaEntradas(forma){
      if(forma.txtRznSocial.value == '-'){
      	alert("Debe especificar la razón social");
        forma.txtRznSocial.focus();
        return false;
      }	else if(forma.txtPropietario.value == "" ){
        alert("Cuando ingresar el propietario");
        forma.txtPropietario.focus();
        return false;
      }	else if(forma.txtRznSocial.value != forma.propio.value && forma.txtCtoDia.value == 0 ){
        alert("Cuando se elije un tercero debe ingresar un Costo Día para la unidad");
        forma.txtCtoDia.focus();
        return false; 
      }	else if(forma.txtRznSocial.value != forma.propio.value && forma.cmbModoAcuerdo.value == '-' ){
        alert("Cuando se elije un tercero debe indicar la modalidad de acuerdo");
        forma.txtCtoDia.focus();
        return false; 
      } else if (forma.txtPlaca.value == ''){
        alert("Falta ingresar un número de Placa");
        forma.txtPlaca.focus();
        return false;
      } else if (forma.cmbConsiderarPropio.value == '-'){
        alert("Debe especificar si se debe considerar propio");
        forma.cmbConsiderarPropio.focus();
        return false;
      } else if (forma.txtInteriorLargo.value == '' || forma.txtInteriorLargo.value == '0'){
        alert("Falta ingresar el valor del Largo Interior");
        forma.txtInteriorLargo.focus();
        return false; 
      } else if (forma.txtInteriorAlto.value == '' || forma.txtInteriorAlto.value == '0'){
        alert("Falta ingresar el valor del Alto Interior");
        forma.txtInteriorAlto.focus();
        return false; 
      } else if (forma.txtInteriorLargo.value == '' || forma.txtInteriorLargo.value == '0'){
        alert("Falta ingresar el valor del Largo Interior");
        forma.txtInteriorLargo.focus();
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

  $(document).ready(function(){
    $(".classOperacion").hide("slow");
    $("#txtRznSocial").change(function () {
    	valor = $("#txtRznSocial").val();
      if (valor == 'INVERSIONES MOY S.A.C.'){
        $(".classOperacion").hide("slow");
      } else {
      	$(".classOperacion").show(350); 
      }  
    });
  });  


  </script>  
  </head>
  <body>
  
  <form method="POST" action="_nuevoVehiculo.php"  enctype="multipart/form-data" onSubmit ="return validaEntradas(this);" >
  <table border="1" width="840" height="437">
    <tr>
	   <th  class = "pagina" height="23" valign="top" colspan="6">
		<p align="center"><b>Insertar Nuevo Vehículo</b></th>
    </tr>
    <tr>
      <th height="23" width="300" valign="top" colspan="2"><b>Datos Generales</b><br></th>
      <td height="23" valign="top" colspan="4"><b>Descripción</b></td>
    </tr>
    <tr>
		  <td class = 'rojo' height="20" width="110" valign="top">Placa (*)</td>
		  <td height="20" width="190" valign="top"><input type="text" name="txtPlaca"  size="20"></td>
      <td height="63" valign="top" colspan="4" rowspan="3">
		<textarea rows="3" name="txtDescripcion" cols="45"></textarea></td>
 	  </tr>
	<tr>
		<td height="20" valign="top">Nro. Vehículo</td>
		<td height="20" valign="top"><input type="text" name="txtNroVehiculo" size="20"></td>
	</tr>
	<tr>
		<td class = 'rojo' height="19" valign="top">Propietario (*)</td>
		<td height="19" valign="top"><input type="text" name="txtPropietario" size="20"></td>
		
	</tr>
	<tr>
		<td class = 'rojo' height="23" valign="top">Rzn. Social (*)</td>
		<td height="23" valign="top">
		  <select size="1" name="txtRznSocial" id="txtRznSocial" style="position: relative; width: 220; height:22" >
		  	<option>-</option>
		    <option><?php echo $empresa; ?></option>
		    <?php foreach($duenos as $dueno) { ?>
			     <option value="<?php echo $dueno['documento']; ?>"><?php echo $dueno['documento'].'-'.$dueno['nombreCompleto']  ; ?></option>
            <?php }  ?>
		  </select>		
	  </td>
    <th height="23" valign="top" colspan="2"><b>Dimensiones según tarjeta (en mts.)</b></th>
    <th height="23" valign="top" colspan="2"><b>Dimensiones del espacio de carga (mts.)</b></th>
	</tr>
	<tr>
		<td class = 'rojo' height="23" valign="top">Considerar propio (*)</td>
		<td height="23" valign="top">			
			<select size="1" name="cmbConsiderarPropio" id="cmbConsiderarPropio" style="position: relative; width: 100; height:22" >
        <option>-</option>
        <option>Si</option>
        <option>No</option>
      </select>
		</td>
    <td height="23" width="120" valign="top">Largo</td>
    <td height="23" width="110" valign="top"><input type="text" name="txtLargo" size="10"></td>
		<td class = 'rojo' height="23" width="120" valign="top">Largo Interior (*)</td>
		<td height="23" valign="top"><input type="text" name="txtInteriorLargo" size="10"></td>

	</tr>
	<tr>
		<td height="23" valign="top">Nro. Motor</td>
		<td height="23" valign="top"><input type="text" name="txtNroMotor" size="20"></td>
    <td height="20" valign="top">Alto</td>
		<td height="20" valign="top"><input type="text" name="txtAlto" size="10"></td>
		<td class = 'rojo' height="23" valign="top">Alto Interior (*)</td>
		<td height="23" valign="top"><input type="text" name="txtInteriorAlto" size="10"></td>
	</tr>

	<tr>
		<td height="23" valign="top">Nro. Serie</td>
		<td height="23" valign="top"><input type="text" name="txtNroSerie" size="20"></td>
		<td height="20" valign="top">Ancho</td>
		<td height="20" valign="top"><input type="text" name="txtAncho" size="10"></td>
		<td class = 'rojo' height="23" valign="top">Ancho Interior (*)</td>
		<td height="23" valign="top"><input type="text" name="txtInteriorAncho" size="10"></td> 		
	</tr>
	<tr>
		<td height="23" valign="top">Marca</td>
		<td height="23" valign="top"><input type="text" name="txtMarca" size="20"></td>
		<th height="13" valign="top" colspan="4"><b>Otros</b></th>		
	</tr>
	<tr>
		<td height="23" valign="top">Modelo</td>
		<td height="23" valign="top"><input type="text" name="txtModelo"  size="20"></td>
		<td class = 'rojo' height="23" valign="top">Base Moy (*)</td>
		<td height="15"  valign="top">
		  <select size="1" name="txtBase" id="txtBase" style="position: relative; width: 100; height:22" >
		   <option value = '' ></option>	
       <option >VES</option>
		   <option >SAN MIGUEL</option>
		 </select>

		</td>
		<td class = 'rojo' height="23" valign="top">Costo Día (*)</td>
		<td height="23" valign="top"><input type="text" name="txtCtoDia" size="10"></td>
	</tr>
	<tr>
		<td class = 'rojo' height="11" valign="top">Ańo Fabricación (*)</td>
		<td height="11" valign="top"><input type="text" name="txtAnioFabricacion" size="20"></td>
		<td height="11" valign="top">Cilindros</td>
		<td height="15" valign="top"><input type="text" name="txtCilindros" size="10"></td>
		<td valign="top">C.c.</td>
    <td valign="top"><input type="text" name="txtCc" size="10"></td>
	</tr>
	<tr>
		<td class = 'rojo' height="13" valign="top">Color (*)</td>
		<td height="13" valign="top"><input type="text" name="txtColor" size="20"></td>
		<td height="13" valign="top">Hp</td>
		<td height="18" valign="top">
		  <input type="text" name="txtHp"  size="10"></td>
		<td height="26" valign="top">M3 Facturable<img width="25" height="25" border="0" align="right" src="imagenes/flechaAnimada.gif"></td>
    <td height="26" valign="top">
      <input type="text" name="txtM3Fact" size="10">
    </td>
	</tr>

	<tr>
		<td class = 'rojo' height="18" valign="top">Carrocería (*)</td>
		<td height="18" valign="top"><input type="text" name="txtCarroceria" size="20"></td>
		<td height="11" valign="top">Nro. Ruedas</td>
		<td height="15" valign="top">
		  <input type="text" name="txtNroRuedas" size="10"></td>
		<td height="26" valign="top">Llantas</td>
    <td height="26" valign="top"><input type="text" name="txtLlantas" size="10"></td>
	</tr>

	<tr>
		<td class = 'rojo' height="18" valign="top">Puerta Carrocería (*)</td>
		<td height="18" valign="top"><input type="text" name="txtPuertaCarr" size="20"></td>
				<td height="15" valign="top">Pulg. del Aro</td>
		<td height="1"  valign="top">
		  <input type="text" name="txtPulg"  size="10"></td>
		<td height="23" valign="top">
		  <div class = 'classOperacion'>
		    Con Personal Moy
		  </div>
		</td>
		<td height="23" valign="top"> 
		  <div class = 'classOperacion'>
		<?php
		  echo "<select size='1' name='cmbTerceroConPersonalMoy' style='position: relative; width:120; height:22' >";
      echo opcionesEnum($db,'vehiculotercero','terceroConPersonalMoy',NULL);
      echo "</select>";

		?>
		  </div>
		</td> 
	</tr>

	<tr>
		<td height="18" valign="top">Clase</td>
		<td height="18" valign="top"><input type="text" name="txtClase" size="20"></td>
		<td height="15" valign="top">Capac. Combust.</td>
		<td height="1" valign="top"><input type="text" name="txtCombustibleGal" size="5"> Galones</td>
    <td height="23" valign="top" class="rojo">
      <div class = 'classOperacion'>
        Modalidad Acuerdo
      </div>  
    </td>
		<td height="23" valign="top">
			<div class = 'classOperacion'>
  			<select size="1" name="cmbModoAcuerdo" id="cmbModoAcuerdo" style="position: relative; width: 100; height:22" >
          <option>-</option>
          <?php echo opcionesEnum($db,'vehiculotercero','modoAcuerdo',NULL);  ?>
        </select>
		</td>
	</tr>

	<tr>		
		<td height="13" valign="top">Estado</td>
		<td height="23" valign="top">
		 <select size="1" name="txtEstado" id="txtEstado" style="position: relative; width: 100; height:22" >
       <option >Activo</option>
		   <option >Inactivo</option>
		 </select>
		</td>
		<td height="23" valign="top">Rendimiento</td>
		<td height="23" valign="top"><input type="text" name="txtRendimiento" size="9"></td>
		<td height="23" valign="top"></td>
		<td height="23" valign="top"></td>  
	</tr>
	
	<tr>
		<td height="13" valign="top">Check Alertas</td>
		<td height="23" valign="top">
		 <select size="1" name="txtCheckAlertas" id="txtCheckAlertas" style="position: relative; width: 100; height:22" >
         <option >Si</option>
		 <option >No</option>
		 </select>
		</td>
		<td height="23" valign="top">Grupo Rendimiento</td>
	  <td height="23" valign="top">
		 <select size="1" name="txtGrupoRendimiento" id="txtGrupoRendimiento" style="position: relative; width: 80; height:22" >
       	<option>-</option>
       	<?php foreach($gruposRendimiento as $grupo) {  ?>
       	<option value="<?php echo $grupo['grupoRendimiento']; ?>"><?php echo $grupo['grupoRendimiento']; ?></option>
		   <?php }?>
		 </select>
    </td>   
		<td height="23" valign="top"></td>
		<td height="23" valign="top"></td>   
		
	</tr>

	<tr>
		<th height="11"  valign="top" colspan="2"><b>Capacidad de Carga (Kgs.)</b></th>	
		<td height="23" valign="top">Eficienc Combust.</td>
		<td height="23" valign="top">
		<select size="1" name="txtEficCombustible" id="txtEficCombustible" style="position: relative; width: 80; height:22" >
		    <option>Normal</option>
		    <option>Alto</option>
     	</select>
		</td>
		<td height="23" valign="top" ></td>
		<td height="23" valign="top"></td>    
	</tr>

	<tr>
		<td height="23" valign="top">Peso Seco</td>
		<td height="23" valign="top"><input type="text" name="txtPesoSeco" size="15"> Kgr</td>
		<td height="23" valign="top">&nbsp;</td>
		<td height="23" valign="top">&nbsp;</td>
		<td height="23" valign="top" ></td>
		<td height="23" valign="top"></td>
	</tr>
		
	<tr>	
    <td height="11" valign="top">Peso Util</td>
	  <td height="11" valign="top"><input type="text" name="txtPesoUtil" size="15"> Kgr</td>
	  <td height="23" valign="top">&nbsp;</td>
	  <td height="23" valign="top">&nbsp;</td>
		<td height="23" valign="top"></td>
		<td height="23" valign="top"></td> 
		
	</tr>

	<tr>
		<td height="13" valign="top">Peso Bruto</td>
		<td height="13" valign="top"><input type="text" name="txtPesoBruto" size="15"> Kgr</td>
		<td height="23" valign="top" colspan = '4'>
			Imagen <input type="file" name="imgVehiculo" id="imgVehiculo" />
		</td>
        
	</tr>
	<tr>
		<td class = 'rojo' height="13" valign="top">Peso Carga Real (*)</td>
		<td height="13" valign="top"><input type="text" name="txtPesoCargaReal" size="15"> Kgr</td>
    <td height="23" colspan= '2' valign="top"><input type="submit" value="Enviar" name="btnEnviar"><input type="reset" value="Limpiar" name="B2"></td>
		<td colspan = '2' class = 'rojo'>(*) Son campos obligatorios</td>
	</tr>

	</table>
	<input type=hidden name=propio size=94 value = '<?php echo $empresa ?>'>

</form>


  </body>
</html>
