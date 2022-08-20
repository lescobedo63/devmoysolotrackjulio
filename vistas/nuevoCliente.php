<head>
  <script language="JavaScript" type="text/javascript" src="librerias/varios.js"></script>
  <link rel =stylesheet href="librerias/estilos.css" type ="text/css">
  
  <script type="text/javascript">  
  
  function validaEntradas(forma){
     
    if(forma.txtCodigo.value == ''  ){
      alert("Debe ingresar el Nro. de RUC.");
      forma.txtCodigo.focus();
      return false; 
    }   
    
    if(forma.txtNombre.value == ''){
      alert("Debe ingresar el Nombre del Cliente.");
      forma.txtNombre.focus();
      return false;
    }
    
    return true;
  }
  </script>
  
</head>
<body>

<form action="_nuevoCliente.php" method="post" onSubmit="return validaEntradas(this);"> 
<table border="1" width="603">
	<tr>
		<th  class = "pagina"  colspan="4">Datos del Cliente</th>
	</tr>
	<tr>
		<th class = "principal" colspan="2">Datos Generales</th>
		<th class = "principal" colspan="2">Otros</th>
	</tr>
	<tr>
		<td width="85">RUC/DNI</td>
		<td width="188"><input type="text" name="txtCodigo" size="20"></td>
		<td width="112">Estado</td>
		<td width="190"><input type="text" name="txtEstado" size="20" value = 'activo' readonly="" ></td>
	</tr>
	<tr>
		<td width="85">Rzn. Social</td>
		<td width="188" rowspan="2"><textarea rows="2" name="txtRznSocial" cols="20"></textarea></td>
		<td width="112">Categoría</td>
		<td width="190"><input type="text" name="txtCategoria" size="20"></td>
	</tr>
	<tr>
		<td width="85">&nbsp;</td>
		<td width="112">Máximo x Día</td>
		<td width="190"> <input type="text" name="txtMaxHoras" size="15">horas</td>
	</tr>
	<tr>
		<td width="85">Nombre</td>
		<td width="188"><input type="text" name="txtNombre" size="20"></td>
		<td width="112">Coordinador</td>
		<td width="190"><input type="text" name="txtCoordinador" size="20"></td>
	</tr>
	<tr>
		<td width="85">Actividad</td>
		<td width="188"><input type="text" name="txtActividad" size="20"></td>
		<td width="112">&nbsp;</td>
		<td width="190">&nbsp;</td>
	</tr>
	<tr>
		<th class = "principal" width="279" colspan="2">Ubicación</th>
		<th class = "principal" width="308" colspan="2">Información Pagos</th>
	</tr>
	<tr>
		<td width="85">Calle y Nro</td>
		<td width="188" rowspan="2"><textarea rows="2" name="txtCalleNro" cols="20"></textarea></td>
		<td width="112">Nombre Banco</td>
		<td width="190"><input type="text" name="txtNombreBanco" size="20">&nbsp;</td>
	</tr>
	<tr>
		<td width="85">&nbsp;</td>
		<td width="112">Nro. Cuenta</td>
		<td width="190"><input type="text" name="txtNroCuenta" size="20"></td>
	</tr>
	<tr>
		<td width="85">Distrito</td>
		<td width="188"><input type="text" name="txtDistrito" size="20"></td>
		<td width="112">Tipo Cuenta</td>
		<td width="190"><input type="text" name="txtTipoCuenta" size="20"></td>
	</tr>
	<tr>
		<td width="85">Fax</td>
		<td width="188"><input type="text" name="txtFax" size="20"></td>
		<td width="112">Moneda</td>
		<td width="190"><input type="text" name="txtMoneda" size="20"></td>
	</tr>
	<tr>
		<td width="85">Web</td>
		<td width="188"><input type="text" name="txtWeb" size="20"></td>
		<td width="112">Días Pago</td>
		<td width="190"><input type="text" name="txtDiasPago" size="20"></td>
	</tr>
	<tr>
		<td width="85">&nbsp;</td>
		<td width="188">&nbsp;</td>
		<td width="112">Forma Pago</td>
		<td width="190"><input type="text" name="txtFormaPago" size="20"></td>
	</tr>
	<tr>
		<td width="85">&nbsp;</td>
		<td width="188">&nbsp;</td>
		<td width="112">&nbsp;</td>
		<td width="190"><input type="submit" value="Enviar" name="btnEnviar"><input type="reset" value="Limpiar" name="btnLimpiar"></td>
		</tr>
	</table>
</form>





</body>
</html>
