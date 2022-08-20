<?php
 if (empty($_POST['btnEnviar'])){ ?>

<head>
  <script language="JavaScript" type="text/javascript" src="librerias/varios.js"></script>
  <link rel =stylesheet href="librerias/estilos.css" type ="text/css">

  <script type="text/javascript">

    function validaEntradas(forma){
      //cuenta = forma.cmbCondPago.value; 
      //alert(cuenta)
      if (forma.cmbCondPago.value == '-' ){
          alert("La condición de pago es un campo obligatorio");
          return false;  
        }

      return true;
    }




  $(document).ready(function(){
    $( "#tabsDocs" ).tabs();

    dlgNuevaSuperCuenta = $( "#dlgNuevaSuperCuenta" ).dialog({
      autoOpen: false,
      resizable: false,
      height: "auto",
      width: 450,
      modal: true,
      position: [60,180],
      buttons: {
        "Crear Super Cuenta": addSuperCuenta,
        Cerrar: function() {
          $( this ).dialog( "close" );
        }
      }
    });

    dlgEditaSuperCuenta = $( "#dlgEditaSuperCuenta" ).dialog({
      autoOpen: false,
      resizable: false,
      height: "auto",
      width: 450,
      modal: true,
      position: [60,180],
      buttons: {
        "Guardar Cambios": editaSuperCuenta,
        Cerrar: function() {
          $( this ).dialog( "close" );
        }
      }
    });

    dlgEliminaSuperCuenta = $( "#dlgEliminaSuperCuenta" ).dialog({
      autoOpen: false,
      resizable: false,
      height: "auto",
      width: 450,
      modal: true,
      position: [60,180],
      buttons: {
        "Eliminar SuperCuenta": eliminaSuperCuenta,
        Cerrar: function() {
          $( this ).dialog( "close" );
        }
      }
    });

    $("#btnCrearSuperCuenta").click(function(){
      //alert("prueba")
      dlgNuevaSuperCuenta.dialog( "open" );
    })

    $(".editaSuperCuenta").click(function(){
      var superCuenta =  $(this).parent().parent().find(".superCuenta").text()
      var estadoCuenta =  $(this).parent().parent().find(".estadoCuenta").text()
      $("#txtEditaSuperCuenta").val(superCuenta)
      $("#cmbEditaEstado").val(estadoCuenta)
      dlgEditaSuperCuenta.dialog( "open" );

    })

    $(".eliminaSuperCuenta").click(function(){
      var superCuenta =  $(this).parent().parent().find(".superCuenta").text()
      $("#txtEliminaSuperCuenta").val(superCuenta)
      dlgEliminaSuperCuenta.dialog( "open" );
    })

    function addSuperCuenta(){
      superCuenta = $("#txtSuperCuenta").val()
        
      var parametros = {
        "idCliente": '<?php echo $id;  ?>',
        "superCuenta":superCuenta, 
      }
        
      $.ajax({
        data:  parametros,
        url:   './vistas/ajaxVarios.php?opc=nuevaSuperCuenta',
        timeout: 15000,
        type:  'post',
        success:  function (response) {
          console.log("response", response)
          if (response == 1){      
            $("#dlgNuevaSuperCuenta").dialog( "close" )
            swal({
              type: "success",
              title: "¡La Super Cuenta ha sido guardada correctamente!",
              showConfirmButton:true,
              confirmButtonText:"Cerrar",
              closeOnConfirm: false

            }).then((result)=>{
              if(result.value){
                window.location = "index.php?controlador=clientes&accion=vercliente&id="+<?php echo $id;  ?>
              }
            });
          } else {
            swal({
              type: "error",
              title: "¡No se guardó el registro. Puede ser que la Super Cuenta ya exista!",
              showConfirmButton:true,
              confirmButtonText:"Cerrar",
              closeOnConfirm: false

            })
          }
        }
      });
    }

    function editaSuperCuenta(){
      estadoCuenta = $("#cmbEditaEstado").val() 
      superCuenta = $("#txtEditaSuperCuenta").val()
        
      var parametros = {
        "idCliente": '<?php echo $id;  ?>',
        "superCuenta":superCuenta, 
        "estadoCuenta":estadoCuenta, 
      }
        
      $.ajax({
        data:  parametros,
        url:   './vistas/ajaxVarios.php?opc=editaSuperCuenta',
        timeout: 15000,
        type:  'post',
        success:  function (response) {
          console.log("response", response)
          if (response == 1){      
            $("#dlgEditaSuperCuenta").dialog( "close" )
            swal({
              type: "success",
              title: "¡La Super Cuenta ha sido editada correctamente!",
              showConfirmButton:true,
              confirmButtonText:"Cerrar",
              closeOnConfirm: false

            }).then((result)=>{
              if(result.value){
                window.location = "index.php?controlador=clientes&accion=vercliente&id="+<?php echo $id;  ?>
              }
            });
          } else {

            swal({
              type: "warning",
              title: "¡No se editó la Super Cuenta!",
              showConfirmButton:true,
              confirmButtonText:"Cerrar",
              closeOnConfirm: false
            })
          }
        }
      });
    }

    function eliminaSuperCuenta(){
      superCuenta = $("#txtEliminaSuperCuenta").val()
        
      var parametros = {
        "idCliente": '<?php echo $id;  ?>',
        "superCuenta":superCuenta, 
      }
        
      $.ajax({
        data:  parametros,
        url:   './vistas/ajaxVarios.php?opc=eliminaSuperCuenta',
        timeout: 15000,
        type:  'post',
        success:  function (response) {
          console.log("response", response)
          if (response == 1){      
            $("#dlgEliminaSuperCuenta").dialog( "close" )
            swal({
              type: "success",
              title: "¡La Super Cuenta ha sido eliminada correctamente!",
              showConfirmButton:true,
              confirmButtonText:"Cerrar",
              closeOnConfirm: false
            }).then((result)=>{
              if(result.value){
                window.location = "index.php?controlador=clientes&accion=vercliente&id="+<?php echo $id;  ?>
              }
            });
          } else {
            swal({
              type: "error",
              title: "¡No se eliminó la SuperCuenta!",
              showConfirmButton:true,
              confirmButtonText:"Cerrar",
              closeOnConfirm: false
            })
          }
        }
      });
    }
  });

  </script> 

</head>
<body>


  <div id="dlgNuevaSuperCuenta" title="Nueva Super Cuenta">
    <div style="padding: 5px;">
    <form>
    <fieldset>
      <label>
        <input style="padding: 5px; width: 260px" id="txtSuperCuenta" name="txtSuperCuenta" type="text"  placeholder="Ingrese la Super Cuenta (máx 30 caracteres)" maxlength = '30' >
      </label><br/>
    </fieldset>
    </form>
    </div>
  </div>

  <div id="dlgEditaSuperCuenta" title="Edita Super Cuenta">
    <div style="padding: 5px;">
    <form>
    <fieldset>
      <label>
        <input style="padding: 5px; width: 260px" id="txtEditaSuperCuenta" name="txtEditaSuperCuenta" type="text" maxlength = '30' readonly >
      </label><br/><br/>
      <label>
        <select name = 'cmbEditaEstado' id = 'cmbEditaEstado'>
          <option>Activo</option>
          <option>Inactivado</option>
        </select>
        

      </label><br/>

    </fieldset>
    </form>
    </div>
  </div>

  <div id="dlgEliminaSuperCuenta" title="Elimina Super Cuenta">
    <div style="padding: 5px;">
    <form>
    <fieldset>
      <label>
        <input style="padding: 5px; width: 260px" id="txtEliminaSuperCuenta" name="txtEliminaSuperCuenta" type="text"   maxlength = '30' readonly>
      </label><br/>
    </fieldset>
    </form>
    </div>
  </div>



<table width="1300" border="0" bgcolor=#032064>
  <tr>
		<td align = 'center' bgcolor=#032064><?php require_once 'barramenu.php';  ?></td>
	</tr>
</table>
<form  method="POST" action="index.php?controlador=clientes&accion=vercliente&id=<?php echo $id ?>"  id= 'forma' onSubmit="return validaEntradas(this);" >
<?php foreach($cliente as $item) {  ?>
  <div class = 'dosCol700x1200'>
  	<table width = '100%' border = '1'>
    	<tr>
        <th class = 'pagina' colspan = '2'>
          <img border="0" src="imagenes/moy.jpg" width="25" height="25" align="right" onclick = "abrirventana('index.php?controlador=aplicacion&accion=verprincipal')">
          <img border="0" src="imagenes/volver.jpg" width="25" height="25" align="right" onclick = "abrirventana('index.php?controlador=clientes&accion=listar') ">
          EDITA CLIENTE</th>
      </tr>
      <tr>
        <th height="23" valign="top" colspan="2">Datos Generales</th>
      </tr>
      <tr>
  	    <td width="120">Código</td>
  	  	<td><?php echo $item['idRuc']; ?></td>
  	  </tr>
    	<tr>
    		<td width="120">Rzn. Social</td>
    		<td rowspan= '2'><textarea rows="2" name="txtRznSocial" cols="45"><?php echo $item['rznSocial']; ?></textarea></td>
    	</tr>
      <tr>
    		<td></td>
    	</tr>
    	<tr>
    		<td width="120">Nombre</td>
    		<td><input type="text" name="txtNombre" value = "<?php echo $item['nombre']; ?>" size="20"></td>
    	</tr>
    	<tr>
    		<td width="120">Actividad</td>
    		<td rowspan= '2'><textarea rows="2" name="txtActividad" cols="45"><?php echo $item['actividad']; ?></textarea></td>
    	</tr>
    	<tr>
    	  <td width="120">&nbsp;</td>
    	</tr>
    	<tr>
    		<th class = "principal" width="426" colspan="2">Ubicación</th>
    	</tr>
    	<tr>
    		<td>Calle y Nro</td>
    		<td><textarea rows="2" name="txtCalleNro" cols="45"><?php echo $item['dirCalleNro']; ?></textarea></td>
    	</tr>
    	<tr>
    		<td>Distrito</td>
    		<td><input type="text" name="txtDistrito" value = "<?php echo $item['dirDistrito']; ?>" size="20"></td>
    	</tr>
    	<tr>
    		<td>Fax</td>
    		<td><input type="text" name="txtFax" value = "<?php echo $item['fax']; ?>" size="20"></td>
    	</tr>
    	<tr>
    		<td>Web</td>
    		<td><input type="text" name="txtWeb" value = "<?php echo $item['pagWeb']; ?>" size="20"></td>
    	</tr>
    	<tr>
    		<th class = "principal" width="426" colspan="2">Información Bancaria</th>
    	</tr>
    	<tr>
    		<td width="120">Nombre Banco</td>
    		<td width="300"><textarea rows="2" name="txtNombBanco" cols="20"><?php echo $item['bancoNombre']; ?></textarea></td>
    	</tr>
    	<tr>
    		<td width="120">Nro. Cuenta</td>
    		<td width="300"><input type="text" name="txtNroCuenta" value = "<?php echo $item['bancoCuentaNro']; ?>" size="20"></td>
    	</tr>
    	<tr>
    		<td width="120">Tipo Cuenta</td>
    		<td width="300"><input type="text" name="txtTipoCuenta" value = "<?php echo $item['bancoCuentaTipo']; ?>" size="20"></td>
    	</tr>
    	<tr>
    		<td width="120">Moneda</td>
    		<td width="300"><input type="text" name="txtMoneda" value = "<?php echo $item['bancoCuentaMoneda']; ?>" size="20"></td>
    	</tr>
      <tr>
    		<td width="120">&nbsp;</td>
    		<td width="300"><input type="submit" value="Enviar" name="btnEnviar"><input type="reset" value="Limpiar" name="btnLimpiar"></td>
    	</tr>	
  	</table>
  </div>

  <div class = 'dosCol700x1200'>
  	<table width = '100%' border = '1'>
    	<tr>
        <th class = 'pagina' colspan = '2'>
          <img border="0" src="imagenes/moy.jpg" width="25" height="25" align="right" onclick = "abrirventana('index.php?controlador=aplicacion&accion=verprincipal')">
          <img border="0" src="imagenes/volver.jpg" width="25" height="25" align="right" onclick = "abrirventana('index.php?controlador=clientes&accion=listar') ">
          EDITA CLIENTE (cont.)</th>
      </tr>
      <tr>
        <td width="120">Estado</td>
  	  	<td width="300">
          <select size="1" name="cmbEstado" style="position: relative; width:200; height:22" >
    		    <option><?php echo $item['estadoCliente']; ?></option>
    		    <option>-</option>
    		    <option>activo</option>
    		    <option>inactivo</option>
          </select>
        </td>
      </tr>
      <tr>
    	  <td width="120">Categoría</td>
    		<td width="300">
          <select size="1" name="cmbCategoria" style="position: relative; width:200; height:22" >
      		  <option><?php echo $item['categCliente']; ?></option>
      		  <option>-</option>
      		  <option>buen pagador</option>
      		  <option>mal pagador</option>
          </select>  
        </td>
    	</tr>
      <tr>
        <td width="120">Condición de Pago</td>
        <td width="300">
          <select size="1" name="cmbCondPago" id="cmbCondPago" style="position: relative; width:200; height:22" >
            <option>-</option>
            <?php
              foreach ($condsPago as $key => $condPago) {
                echo "<option value = ".$condPago["idCondicion"]." ".($condPago["idCondicion"] == $item["idCondicion"] ? "SELECTED" : ""   )."  >".utf8_decode($condPago["nombCondicion"])."</option>";
              }
            ?>
          </select>  
        </td>
      </tr>
    	<tr>
    		<td width="120">Máximo x Día</td>
    		<td width="300"><input type="text" name="txtMaxHoras" value = "<?php echo $item['maximoHras']; ?>"  size="15"> horas</td>
    	</tr>
      <tr>
    		<td width="120">Coordinador</td>
  	  	<td width="300"><input type="text" name="txtCoordinador" value = "<?php echo $item['idCoordinador']; ?>" size="40"></td>
    	</tr>
    	<tr>
    	  <td width="470" colspan="2">
          <div class = "varios3">
  	        <table border="1" width="650" height="20" >
      	      <tr>
      				  <th class = "principal" colspan="5" align="center">
                  <img border="0" src="imagenes/mas.jpg" width="20" height="20" align="right" onclick =  "abrirventanaflotante('index.php?controlador=clientes&accion=nuevocontacto&id=<?php echo $item['idRuc']; ?>') ">
                  Contactos
                </th>
      			  </tr>
  	    		  <tr>
  		     		  <th width="120" align="center"><b>Nombre Completo</b></th>
      				  <th width="200" align="center"><b>Cargo</b></th>
  	    			  <th width="50" align="center"><b>Telefono</b></th>
  		    		  <th width="160" align="center"><b>E mail</b></th>
  			    	  <th  align="center">.</th>
      			  </tr>
  	    		  <?php
  			        $color = 'celeste';
                foreach($contactos as $contacto) {
                  $color = ($color == 'plomo') ? 'celeste' : 'plomo';
  			          echo "<tr>";
        				  echo "<td class = '$color' >".$contacto['idNombreCompleto']."</td>";
  		      		  echo "<td class = '$color' >". $contacto['cargo']."</td>";
  				        echo "<td class = '$color' >". $contacto['telefono']."</td>";
  				        echo "<td class = '$color' >". $contacto['email']."</td>";
  				        echo "<td class = '$color' >";
  			      ?>	  
          			  <img border="0" src="imagenes/lapiz.jpg" width="15" height="15" onclick =  "abrirventanaflotante('index.php?controlador=clientes&accion=editacontacto&id=<?php echo $item['idRuc'] ?>&nombre=<?php echo $contacto['idNombreCompleto'] ?>')">
                  <img border="0" src="imagenes/menos.jpg" width="10" height="10" onclick =  "abrirventanaflotante('index.php?controlador=clientes&accion=eliminacontacto&id=<?php echo $item['idRuc'] ?>&nombre=<?php echo $contacto['idNombreCompleto'] ?>')"></td>	 
               <?php  echo "</tr>";
              } ?>   
  		      </table>
          </div>
        </td>
  	  </tr>
      <tr>
        <td height="131" valign="top" colspan="2">
        <div id="tabsDocs">
          <ul>
            <li><a href="#tbDoc1">CuentasNew</a></li>
            <li><a href="#tbDoc2">Super Cuentas</a></li>
            <li><a href="#tbDoc3">Ubicaciones</a></li>
          </ul>

          <div id="tbDoc1" class = 'varios3'>
            <table border="1" width="650" height="20" >
              <tr>
                <th class = "principal" colspan="10" align="center">
                  <img border="0" src="imagenes/mas.jpg" width="20" height="20" align="right" onclick =  "abrirventanaflotantecompleta('index.php?controlador=clientes&accion=nuevacuentanew&id=<?php echo $id ?>') ">
                  Cuentas
                </th>
              </tr>
              <tr>
                <th width="30" align="center"><b>Corre lativo</b></th>
                <th width="200" align="center"><b>Nombre Cuenta</b></th>
                <th width="50" align="center"><b>Estado</b></th>
                <th width="50" align="center"><b>Para Movil</b></th>
                <th align="center">.</th>
              </tr>
              <?php
                $color = 'celeste';   
                foreach($cuentas02 as $cuenta) {
                  $color = ($color == 'plomo') ? 'celeste' : 'plomo';
                  echo "<tr>";
                  echo "<td class = '$color' >".$cuenta['correlativo']."</td>";
                  echo "<td class = '$color' >". $cuenta['nombreCuenta']."</td>";
                  echo "<td class = '$color' >". $cuenta['estadoCuenta']."</td>";
                  echo "<td class = '$color' >". $cuenta['paraMovil']."</td>";
                  echo "<td class = '$color' >";
              ?>
              <img border="0" src="imagenes/lapiz.png" width="14" height="14" onclick =  "abrirventanaflotantecompleta('index.php?controlador=clientes&accion=nuevacuentanew&id=<?php echo $id;?>&corr=<?php echo $cuenta['correlativo']; ?>')">
              <img border="0" src="imagenes/menos.png" width="14" height="14" onclick =  "abrirventanaflotante('index.php?controlador=clientes&accion=eliminacuentanew&id=<?php echo $id;?>&corr=<?php echo $cuenta['correlativo']; ?>')"></td>   
            <?php  echo "</tr>";
               } ?>   
            </table>
          </div>

      <div id="tbDoc2" class = 'varios3'>
        <table border="1" width="650" height="20" >
          <tr>
            <th class = "principal" colspan="10" align="center">
              <img id= "btnCrearSuperCuenta" border="0" src="imagenes/mas.jpg" width="20" height="20" align="right" >
              Super Cuentas
            </th>
          </tr>
          <tr>
            <th width="150" align="center"><b>Super Cuenta</b></th>
            <th width="60" align="center"><b>Estado</b></th>
            <th width="80" align="center"><b>Creador</b></th>
            <th width="70" align="center"><b>Fch Creación</b></th>
            <th width="80" align="center"><b>Editor</b></th>
            <th width="70" align="center"><b>Fch Edición</b></th>
            <th align="center">.</th>
          </tr>
          <?php
            $color = 'celeste';   
            foreach($superCuentas as $superCuenta) {
              $color = ($color == 'plomo') ? 'celeste' : 'plomo';
              echo "<tr>";
              echo "<td class = '$color  superCuenta' >".$superCuenta['superCuenta']."</td>";
              echo "<td class = '$color estadoCuenta' >". $superCuenta['estado']."</td>";
              echo "<td class = '$color' >". $superCuenta['creacUsuario']."</td>";
              echo "<td class = '$color' >". $superCuenta['creacFch']."</td>";
              echo "<td class = '$color' >". $superCuenta['editaUsuario']."</td>";
              echo "<td class = '$color' >". $superCuenta['editaFch']."</td>";
              echo "<td class = '$color' >";
            ?>
              <img border="0" src="imagenes/lapiz.png" width="14" height="14" class = 'editaSuperCuenta'>
              <img border="0" src="imagenes/menos.png" width="14" height="14" class = 'eliminaSuperCuenta'></td>   
            <?php  echo "</tr>";
               } ?>   
        </table>
      </div>

      <div id="tbDoc3" class = 'varios3'>
      <table border="1" width="100%" height="20" >
      <tr>
        <th class = "principal" colspan="4" align="center">
          <img border="0" src="imagenes/mas.jpg" width="20" height="20" align="right" onclick =  "abrirventanaflotantemediana('index.php?controlador=clientes&accion=nuevaubicacion&id=<?php echo $id ?>') ">
          Ubicaciones
        </th>
      </tr>
      <tr>
        <th width="20" align="center"><b>Nro</b></th>
        <th width="120" align="center"><b>Nombre</b></th>
        <th width="400" align="center"><b>Descripción</b></th>
        <th  align="center">Acción</th>
      </tr>
        <?php
        $color = 'celeste';   
        foreach($ubicaciones as $ubicacion) {
          $color = ($color == 'plomo') ? 'celeste' : 'plomo';
          echo "<tr>";
          echo "<td class = '$color' >".$ubicacion['correlativo']."</td>";
          echo "<td class = '$color' >".$ubicacion['nombUbicacion']."</td>";
          echo "<td class = '$color' >".$ubicacion['descripcion']."</td>";
          echo "<td class = '$color' >";
        ?>
          <img border="0" src="imagenes/lapiz.jpg" width="13" height="13" onclick = "abrirventanaflotantemediana('index.php?controlador=clientes&accion=editaubicacion&id=<?php echo $ubicacion['idCliente'].'-'.$ubicacion['correlativo'] ?>')">
          <img border="0" src="imagenes/menos.jpg" width="13" height="13" onclick =  "abrirventanaflotante('index.php?controlador=clientes&accion=eliminaubicacion&id=<?php echo $ubicacion['idCliente'].'-'.$ubicacion['correlativo'] ?>')">
          </td>  
        <?php  echo "</tr>";
         } ?>   
      </table>
      </div>


    </div>
    </td>
    </tr>
  	</table>
  </div>

<?php } ?>
</form>
</div>
</body>
</html>

<?php } else {

  // session_start();
  require_once './librerias/conectar.php';
  global $servidor, $bd, $usuario, $contrasenia;
  $db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);
  
  $usuario = $_SESSION["usuario"];  
  //$idRuc = $_POST['txtCodigo'];
  $rznSocial = $_POST['txtRznSocial'];
  $nombre = $_POST['txtNombre'];
  $dirCalleNro = $_POST['txtCalleNro'];
  $dirDistrito = $_POST['txtDistrito'];
  $actividad = $_POST['txtActividad'];
  $maximoHras = $_POST['txtMaxHoras'];
  $idCoordinador = $_POST['txtCoordinador'];
  //$estadoCliente = $_POST['txtEstado'];
  $categCliente = $_POST['cmbCategoria'];
  $pagWeb = $_POST['txtWeb'];
  $fax = $_POST['txtFax'];
  $diasPago = "";  //$_POST['txtDiasPago'];
  $formaPago = ""; //$_POST['txtFormaPago'];
  $bancoNombre = $_POST['txtNombBanco'];
  $cuentaNro = $_POST['txtNroCuenta'];
  $cuentaTipo = $_POST['txtTipoCuenta'];
  $cuentaMoneda = $_POST['txtMoneda'];
  $estadoCliente = $_POST['cmbEstado'];
  $idCondicion = $_POST['cmbCondPago'];

  //echo "idRuc: $id, rznSocial: $rznSocial, nombre: $nombre, dirCalleNro: $dirCalleNro, dirDistrito: $dirDistrito, actividad: $actividad, maximoHras: $maximoHras, idCoordinador: $idCoordinador, estadoCliente: $estadoCliente, idCondicion: $idCondicion, categCliente:  $categCliente, pagWeb: $pagWeb, fax: $fax, diasPago: $diasPago, formaPago: $formaPago, bancoNombre: $bancoNombre, bancoCuentaNro: $cuentaNro, bancoCuentaTipo: $cuentaTipo, bancoCuentaMoneda: $cuentaMoneda, usuario: $usuario";

  //Actualizando Cliente
  $actualiza = $db->prepare("UPDATE `cliente` SET `rznSocial` = :rznSocial, `nombre` = :nombre, `dirCalleNro` = :dirCalleNro, `dirDistrito` = :dirDistrito, `actividad` = :actividad, `idCoordinador` = :idCoordinador, `maximoHras` = :maximoHras , `estadoCliente` = :estadoCliente, `idCondicion` = :idCondicion,  `categCliente` = :categCliente, `pagWeb` = :pagWeb, `fax` = :fax, `diasPago` = :diasPago, `formaPago` = :formaPago, `bancoNombre` = :bancoNombre, `bancoCuentaNro` = :bancoCuentaNro, `bancoCuentaTipo` = :bancoCuentaTipo, `bancoCuentaMoneda` = :bancoCuentaMoneda,  `usuarioUltimoCambio` = :usuario, `fchUltimoCambio` = CURDATE() WHERE `cliente`.`idRuc` = :idRuc;");
  
  $actualiza->bindParam(':idRuc',$id);
  $actualiza->bindParam(':rznSocial',$rznSocial);
  $actualiza->bindParam(':nombre',$nombre);
  $actualiza->bindParam(':dirCalleNro',$dirCalleNro);
  $actualiza->bindParam(':dirDistrito',$dirDistrito);
  $actualiza->bindParam(':actividad',$actividad);
  $actualiza->bindParam(':maximoHras',$maximoHras);   //
  $actualiza->bindParam(':idCoordinador',$idCoordinador);
  $actualiza->bindParam(':estadoCliente',$estadoCliente);
  $actualiza->bindParam(':idCondicion',$idCondicion);
  $actualiza->bindParam(':categCliente', $categCliente);
  $actualiza->bindParam(':pagWeb', $pagWeb);
  $actualiza->bindParam(':fax',$fax);
  $actualiza->bindParam(':diasPago',$diasPago);
  $actualiza->bindParam(':formaPago', $formaPago);
  $actualiza->bindParam(':bancoNombre',$bancoNombre);
  $actualiza->bindParam(':bancoCuentaNro',$cuentaNro);
  $actualiza->bindParam(':bancoCuentaTipo',$cuentaTipo);
  $actualiza->bindParam(':bancoCuentaMoneda',$cuentaMoneda);
  $actualiza->bindParam(':usuario',$usuario);
  $actualiza->execute();

?>

<html>
  <head>
  
  <script type="text/javascript">
<!--
     var ventana = window.self; 
     ventana.opener = window.self; 
//-->
</script>

  
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <meta HTTP-EQUIV="REFRESH" content="5; url=index.php?controlador=clientes&accion=listar">
  <title></title>
  </head>
  <body>

    <?php
    if($actualiza->rowCount() == 1){
      echo "<h2>Su registro fue actualizado. Espere un momento a que sea redirigido.</h2>";
    } else {
      echo "<h2>Advertencia.Su registro no ha sido modificado. Espere un momento a que sea redirigido.</h2>";
    }
    ?>
  </body>
</html>

<?php

}  ?>



