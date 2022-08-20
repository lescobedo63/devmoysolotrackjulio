<?php 
  if(!isset($_POST['btnEnviar'])){ ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <link rel =stylesheet href="librerias/estilos.css" type ="text/css">
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <title></title>
  <script language="JavaScript" type="text/javascript" src="librerias/varios.js"></script>
  <link rel="stylesheet" type="text/css" href="librerias/fullPage_js/jquery.fullpage.css" />
  <style>

    #fp-nav ul li .fp-tooltip{
      color:#fff;
      font-size: 12px;
      background: rgba(3,32, 100, .5);
      font-family: "Arial", Georgia, Serif;
      padding: 3px 5px 3px 5px;
    }

    .fp-tableCell {  
      vertical-align: top;
      height:300px;
    }

  </style>

  <!--[if IE]>
    <script type="text/javascript">
       var console = { log: function() {} };
    </script>
  <![endif]-->

  <script type="text/javascript" src="librerias/fullPage_js/jquery.fullpage.js"></script>

  <script language="JavaScript" type="text/javascript">
  
  function calcular_edad(fecha){
    //calculo la fecha de hoy
    hoy=new Date()
    //La fecha que recibo la descompongo en un array.La fecha llega en formato AAAA-MM-DD
    var array_fecha = fecha.split("-")
    //si el array no tiene tres partes, la fecha es incorrecta
    if (array_fecha.length!=3)
       return false
    //compruebo que los ano, mes, dia son correctos
    var ano = parseInt(array_fecha[0]);
    if (isNaN(ano))
       return false

    var mes = parseInt(array_fecha[1]);
    if (isNaN(mes))
       return false

    var dia = parseInt(array_fecha[2]);  
    if (isNaN(dia))
       return false

    //si el ańo de la fecha que recibo solo tiene 2 cifras hay que cambiarlo a 4
    if (ano<=99)
       ano +=1900

    //resto los ańos de las dos fechas
    edad=hoy.getFullYear()- ano - 1; //-1 porque no se si ha cumplido ańos ya este ańo
    //si resto los meses y me da menor que 0 entonces no ha cumplido ańos. Si da mayor si ha cumplido
    if (hoy.getMonth() + 1 - mes < 0) //+ 1 porque los meses empiezan en 0
       return edad
    if (hoy.getMonth() + 1 - mes > 0)
       return edad+1

    //entonces es que eran iguales. miro los dias
    //si resto los dias y me da menor que 0 entonces no ha cumplido ańos. Si da mayor o igual si ha cumplido
    if (hoy.getUTCDate() - dia >= 0)
       return edad + 1
    return edad
} 
  
  function validaTrab(forma){
    var nroDocTrab = forma.txtNroDocTrab.value
    if(forma.cmbTipoDocum.value == '-'){
      alert("Debe indicar el Tipo de Documento");
      forma.cmbTipoDocum.focus();
      return false;
    } else if( nroDocTrab == ''){
      alert("El documento de identidad es obligatorio");
      forma.txtNroDocTrab.focus();
      return false;
    } else if (nroDocTrab.length != 8 ){
      alert("El documento de identidad debe tener 8 caracteres");
      forma.txtNroDocTrab.focus();
      return false;
    } else if(forma.cmbSexo.value == '-'){
      alert("Debe elegir un valor para Sexo");
      forma.cmbSexo.focus();
      return false;
    } else if (forma.txtApPaterno.value == ''){
      alert("El Apellido Paterno es obligatorio");
      forma.txtApPaterno.focus();
      return false;
    } else if (forma.txtApMaterno.value == ''){
      alert("El Apellido Materno es obligatorio");
      forma.txtApMaterno.focus();
      return false;
    } else if (forma.txtNombres.value == ''){
      alert("El Nombre es obligatorio");
      forma.txtNombres.focus();
      return false;
    } else if (forma.txtFchNac.value == ''){
      alert("La Fecha de Nacimiento es Obligatoria");
      forma.txtFchNac.focus();
      return false;
    } else if (forma.cmbNroHijos.value == '-'){
      alert("Debe especificar el Nro. de hijos");
      forma.cmbNroHijos.focus();
      return false;
    } else if (forma.txtPolo.value == ''){
      alert("Las Tallas son Obligatorias");
      forma.txtPolo.focus();
      return false;
    } else if (forma.txtPantalon.value == ''){
      alert("Las Tallas son Obligatorias");
      forma.txtPantalon.focus();
      return false;
    } else if (forma.txtBotas.value == ''){
      alert("Las Tallas son Obligatorias");
      forma.txtBotas.focus();
      return false;
    } else if (forma.txtCasaca.value == ''){
      alert("Las Tallas son Obligatorias");
      forma.txtCasaca.focus();
      return false;
    } else if (forma.cmbModoContratacion.value == ''){
      alert("El Modo de Contratación es obligatorio");
      forma.cmbModoContratacion.focus();
      return false;
    }
      
    if(forma.cmbCategTrab.value == '-'){
      alert("Debe elegir una categoría para el trabajador");
      forma.cmbCategTrab.focus();
      return false;
    } else if(forma.cmbCategTrab.value == '4ta'){
      ruc = forma.txtRuc.value;
      if (ruc.length != 11){
        alert("Si el trabajador es de 4ta categoria\nel Ruc es obligatorio y debe tener 11 dígitos");
        forma.txtRuc.focus();
        return false;
      }
    } else if(forma.cmbCategTrab.value == 'Ef'){
      ruc = forma.txtRuc.value;
      if (ruc.length != 11){
        rpta = confirm("En el caso de la categoría efectivo el RUC es opcional.\nConfirme si desea seguir con el proceso o Cancele si desea ingresar un RUC válido")
        if (!rpta){
          forma.txtRuc.focus();
          return false;
        }
      }
    } if(forma.cmbCategTrab.value == 'Practicante'){
      if(forma.txtSubvencionMensual.value == ''){
        alert("Debe ingresar la subvención mensual del practicante")
        return false; 
      }
      ruc = forma.txtRuc.value;
      if (ruc.length != 11){
        rpta = confirm("En el caso de la categoría efectivo el RUC es opcional.\nConfirme si desea seguir con el proceso o Cancele si desea ingresar un RUC válido")
        if (!rpta){
          forma.txtRuc.focus();
          return false;
        }       
      }
    }

    tipoTrabaj = forma.cmbTipoTrabajador.value
    if(forma.cmbModoSueldo.value == '-'){
      alert("Debe elegir un Modo Sueldo");
      forma.cmbModoSueldo.focus();
      return false;
    } else if (tipoTrabaj == '-'){
      alert("Debe elegir el Tipo de Trabajador");
      forma.cmbTipoTrabajador.focus();
      return false;
    } else if (forma.cmbCuotasFondo.value == '-' && (tipoTrabaj == 'Conductor' || tipoTrabaj == 'Auxiliar') && forma.cmbCategTrab.value != 'Tercero'){
      alert("Debe indicar en cuantas cuotas se le descontará el Fondo de Garantía");
      forma.cmbCuotasFondo.focus();
      return false;
    } else if (forma.txtFchIniDscto.value == '' && (tipoTrabaj == 'Conductor' || tipoTrabaj == 'Auxiliar') && forma.cmbCategTrab.value != 'Tercero'){
      alert("Debe ingresar la fecha de inicio del descuento");
      forma.txtFchIniDscto.focus();
      return false;
    }

    if (tipoTrabaj == 'Conductor'){
      if(forma.txtCategoria.value == '-'){
        alert("Si el Trabajador es Conductor debe Indicar la Categoría de la Licencia");
        forma.txtCategoria.focus();
        return false;
      } else if(forma.txtNroLicencia.value == ''){
        alert("Si el Trabajador es Conductor debe Indicar la Categoría de la Licencia");
        forma.txtNroLicencia.focus();
        return false;
      }
    }

    /* else if ((tipoTrabaj == 'Conductor' || tipoTrabaj == 'Auxiliar') && forma.cmbCategTrab.value != 'Tercero' ){
      alert('Se le hará un descuento por Concepto de Fondo de Garantía: 300 si es Conductor y 200 si es Auxiliar')
    }*/

    var fchNac = forma.txtFchNac.value
    if(calcular_edad(fchNac)< 18){
      var answer = confirm("El nuevo trabajador es menor de edad. Presione Aceptar para continuar o Cancelar para detener el proceso")
    	if (!answer){
  	  	forma.txtFchNac.focus();
        return false;
  	  }
    }
    return true;
  } 
  $(document).ready(function() {
    // alert("prueba")


    $('#fullpage').fullpage({
      anchors: ['primera', 'segunda', 'tercera', 'cuarta'],
      sectionsColor: ['#CECCCC', '#DDE6FB', '#CECCCC', '#DDE6FB'],
      navigation: true,
      navigationPosition: 'left',
      navigationTooltips: ['Datos Básicos', 'Familia y Ubicación', 'Estudios y Exper. Laboral', 'Relación, Emergencias y Salud']
    });

    
    $("#txtFchNac").datepicker({
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
      maxDate: "0M +0D",
      dateFormat:"yy-mm-dd" 
    }); 

    $("#txtFchIniDscto").datepicker({
      minDate: "-10D",
      maxDate: "+10D",
      dateFormat:"yy-mm-dd" 
    });

    $("#txtLaboroEnMoyFchCese").datepicker({
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
      maxDate: "0M +0D",
      dateFormat:"yy-mm-dd" 
    });  









    $(".asumeEmpresa").hide(400)
    $(".tipoComision").hide(400)
    $(".4ta").hide(400)
    $(".5ta").hide(400)
    $(".estadoCasado").hide(400)
    $(".laboroEnMoy").hide(400)
    $(".familiarEnMoy").hide(400)
    $(".enfermedadCronica").hide(400)
    $(".alergia").hide(400)
    $(".puedeManejar").hide(400)
    $(".infoBanco").hide(400)
    $(".cajaChica").hide(400)
    $(".paraPracticante").hide(400)


    $("#cmbModoSueldo").change(function () {
      valor = $(this).val();
      if(valor == 'Variable' ){
        $(".asumeEmpresa").show(500)
      } else {
        //$("#idFch").show(500)
        $(".asumeEmpresa").hide(400)
      }
    })

    $("#cmbEntidadPension").change(function () {
      valor = $(this).val();
      if(valor == 'ONP' || valor == '' ){
        $(".tipoComision").hide(400)
      } else {
        $(".tipoComision").show(500)
      }
    })

    $("#cmbEstadoCivil").change(function () {
      valor = $(this).val();
      if(valor == 'Casado' || valor == 'Conviviente'  ){
        $(".estadoCasado").show(500)
      } else {
        $(".estadoCasado").hide(400)
      }
    })

    $("#cmbLaboroEnMoy").change(function () {
      valor = $(this).val();
      if(valor == 'Si'){
        $(".laboroEnMoy").show(500)
      } else {
        $(".laboroEnMoy").hide(400)
      }
    })

    $("#cmbFamiliarEnMoy").change(function () {
      valor = $(this).val();
      if(valor == 'Si'){
        $(".familiarEnMoy").show(500)
      } else {
        $(".familiarEnMoy").hide(400)
      }
    })

    $("#cmbSaludTieneEnfCronica").change(function () {
      valor = $(this).val();
      if(valor == 'Si'){
        $(".enfermedadCronica").show(500)
      } else {
        $(".enfermedadCronica").hide(400)
      }
    })

    $("#cmbSaludTieneAlergia").change(function () {
      valor = $(this).val();
      if(valor == 'Si'){
        $(".alergia").show(500)
      } else {
        $(".alergia").hide(400)
      }
    })


    $("#cmbTipoTrabajador").change(function () {
      valor = $(this).val();
      if(valor == 'Conductor'){
        $(".puedeManejar").show(500)
        $('.adicManejar').addClass('rojo');
        if ($("#cmbCategTrab").val() == '4ta' || $("#cmbCategTrab").val() == '5ta'  || $("#cmbCategTrab").val() == 'Ef'){
          $(".cajaChica").show(500)
        } else {
          $(".cajaChica").hide(400)

        }


      } else if(valor == 'Coordinador'){
        $(".puedeManejar").show(500)
        $('.adicManejar').removeClass('rojo');
        $(".cajaChica").hide(400)
      } else {
        $(".puedeManejar").hide(400)
        $('.adicManejar').removeClass('rojo');
        $(".cajaChica").hide(400)
      }
    })


    $("#cmbCategTrab").change(function () {
      valor = $(this).val();
      if(valor == '4ta'){
        $('#adic01').addClass('rojo');
        $('#adic02').addClass('rojo');
        $(".4ta").show(500)
        $(".5ta").hide(400)
        $("#adic01").html('RUC (*)');
        $(".infoBanco").show(500)
        $(".tipoComision").hide(400)
        $(".paraPracticante").hide(400)

        if ($("#cmbTipoTrabajador").val() == 'Conductor'){
          $(".cajaChica").show(500)
        } else {
          $(".cajaChica").hide(400)
        }

        $("#cmbModoContratacion").html("<option>-</option><option>independiente</option><option>intermitente</option><option>modalidad</option><option>tercero</option>")
        $("#cmbModoSueldo").html("<option>-</option><option>Fijo</option><option>Variable</option><option>Preferencial</option>")
      } else if(valor == 'Ef'){
        $(".4ta").show(500)
        $(".5ta").hide(400)
        $('#adic01').removeClass('rojo');
        $('#adic02').removeClass('rojo');
        $("#adic01").html('RUC');
        $(".infoBanco").show(500)
        $(".tipoComision").hide(400)
        $(".paraPracticante").hide(400)

        if ($("#cmbTipoTrabajador").val() == 'Conductor'){
          $(".cajaChica").show(500)
        } else {
          $(".cajaChica").hide(400)
        }
        $("#cmbModoContratacion").html("<option>-</option><option>independiente</option><option>intermitente</option><option>modalidad</option><option>tercero</option>")
        $("#cmbModoSueldo").html("<option>-</option><option>Fijo</option><option>Variable</option><option>Preferencial</option>")
      } else if(valor == '5ta' ){
        $(".5ta").show(500)
        $(".4ta").hide(400)
        $('#adic01').removeClass('rojo');
        $('#adic02').removeClass('rojo');
        $("#adic01").html('Renta 5ta');
        $(".infoBanco").show(500)
        $(".paraPracticante").hide(400)
        if ($("#cmbTipoTrabajador").val() == 'Conductor'){
          $(".cajaChica").show(500)
        } else {
          $(".cajaChica").hide(400)
        }
	      valorEntidadPension = $("#cmbEntidadPension").val();
	      if(valorEntidadPension == 'ONP' || valor == '' ){
	        $(".tipoComision").hide(400)
	      } else {
	        $(".tipoComision").show(500)
	      }
        $("#cmbModoContratacion").html("<option>-</option><option>independiente</option><option>intermitente</option><option>modalidad</option><option>tercero</option>")
        $("#cmbModoSueldo").html("<option>-</option><option>Fijo</option><option>Variable</option><option>Preferencial</option>")
      } else if(valor == 'Practicante'){
        $(".4ta").show(500)
        $(".5ta").hide(400)
        $('#adic01').removeClass('rojo');
        $('#adic02').removeClass('rojo');
        $("#adic01").html('RUC');
        $(".infoBanco").show(500)
        $(".tipoComision").hide(400)

        $("#cmbModoContratacion").html("<option>modalidad</option>")
        $("#cmbModoSueldo").html("<option>Fijo</option>")

        if ($("#cmbTipoTrabajador").val() == 'Conductor'){
          $(".cajaChica").show(500)
        } else {
          $(".cajaChica").hide(400)
        }
        $(".paraPracticante").show(500)
        
      } else { //Tercero
        $(".4ta").hide(400)
        $(".5ta").hide(400)
        $(".infoBanco").hide(400)
        $('#adic01').removeClass('rojo');
        $('#adic02').removeClass('rojo');
        $("#adic01").html('');
        $(".cajaChica").hide(400)
        $(".paraPracticante").hide(400)
        $("#cmbModoContratacion").html("<option>-</option><option>independiente</option><option>intermitente</option><option>modalidad</option><option>tercero</option>")
        $("#cmbModoSueldo").html("<option>-</option><option>Fijo</option><option>Variable</option><option>Preferencial</option>")

      }
    })
  });

  </script>

  </head>
  <body>

  <div id="fullpage">
  <form method="POST" name='forma' enctype="multipart/form-data" action="index.php?controlador=aplicacion&accion=nuevotrabajador" OnSubmit= "return validaTrab(this);"  >
  <div class="section " id="section0">
    <div style="float:right; text-align:right;">
    <table border = '0' width="800px" style="border:black 1px solid;">
      <tr>
        <th colspan="6">
          Datos Personales
        </th>
      </tr>
      <tr>
        <td width="110" valign="top" class = 'rojo'>Tipo Documento</td>
        <td width="150" valign="top">
          <select name = 'cmbTipoDocum' id = 'cmbTipoDocum'>
            <option>-</option>
            <option value = 'DNI'>DNI</option>
            <option value = 'PAS'>Pasaporte</option>
          </select>


        </td>
        <td width="90" valign="top" class = 'rojo'>Nro. Doc.
        <img border="0" class ="titulo" title ="Verificar DNI RENIEC|Esta opción te permite verificar|el DNI de un nuevo Trabajador." src="imagenes/reniec.jpg" width="25" height="25" align="right" onclick =  "abrirventanaflotantegrande('https://cel.reniec.gob.pe/valreg/valreg.do')">
        </td>
        <td width="150" valign="top">
          <input type="text" name="txtNroDocTrab" size="12"  maxlength="8"></td>
        <td>Estado Trabaj.</td>
        <td>
          <select size="1" name="cmbEstadoTrab">
            <option selected>Activo</option>
            <option>Inactivo</option>
          </select>
        </td>
      </tr>
      <tr>
        <td class = 'rojo'>Ap. Paterno (*)</td><td><input type="text" name="txtApPaterno" size="12"></td>
        <td class = 'rojo'>Ap. Materno (*)</td><td><input type="text" name="txtApMaterno" size="12"></td>
        <td class = 'rojo'>Nombres (*)</td><td><input type="text" name="txtNombres" size="12"></td>
      </tr>
      <tr>
        <td class = 'rojo'>Fch Nac (*)</td>
        <td>
          <input type="text" name="txtFchNac" id="txtFchNac"  size="12">
        </td>
        <td  class = 'rojo' width="90">Sexo (*)</td>
        <td>
          <select size="1" name="cmbSexo" id="cmbSexo">
          <option>-</option>
          <option>Masculino</option>
          <option>Femenino</option>
          </select> 
       </td>
        <td></td><td></td>
      </tr>
      <tr>
        <td valign="top">Estado Civil</td>
        <td valign="top">
          <select size="1" name="cmbEstadoCivil" id="cmbEstadoCivil">
          <option>-</option>
          <option>Soltero</option>
          <option>Casado</option>
          <option>Conviviente</option>
          </select>
        </td>
        <td><div class = 'estadoCasado'>Desde</div></td><td><div class = 'estadoCasado'><input type="text" name="txtEstadoCivilDesde" size="11"></div></td>
        <td class = 'rojo'>Nro Hijos (*)</td>
        <td>
          <select size="1" name="cmbNroHijos" id="cmbNroHijos">
            <option>-</option>
            <option>0</option><option>1</option><option>2</option><option>3</option><option>4</option>
            <option>5</option><option>6</option><option>7</option><option>8</option><option>9</option>
          </select> Legalmente reconocidos         
        </td>
      </tr>

            <tr>
        <th colspan="6">
          Tallas para Uniforme
        </th>
      </tr>
      <tr>
        <td width="90" class = 'rojo'>Polo (*)</td><td width="150" ><input type="text" name="txtPolo" id="txtPolo" size="10"></td>
        <td width="90" class = 'rojo'>Pantalón (*)</td><td width="150" ><input type="text" name="txtPantalon" id="txtPantalon" size="10"></td>
        <td width="90" class = 'rojo'>Botas (*)</td><td><input type="text" name="txtBotas" id="txtBotas" size="10"></td>
      </tr>
      <tr>
        <td width="90" class = 'rojo'>Casaca (*)</td><td width="150" ><input type="text" name="txtCasaca" id="txtCasaca" size="10"></td>
        <td></td>
        <td></td><td></td>
      </tr>
      <tr>
        <th colspan="6">
          Info Para Pago
        </th>
      </tr>
      <tr>
        <td class = 'rojo'>Categ Trabaj (*)</td>
        <td>
          <select size="1" name="cmbCategTrab" id="cmbCategTrab">
            <option>-</option>
            <option>5ta</option>
            <option>4ta</option>
            <option>Ef</option> 
            <option>Tercero</option>
            <option>Practicante</option>
          </select>
        </td>
        <td class = 'rojo'>Modo Contrat (*)</td>
        <td>
          <select size="1" name="cmbModoContratacion" id="cmbModoContratacion" style="position: relative; width: 100" >
            <option value="">-</option>
            <?php foreach($modosContratacion as $item) { ?>
              <option>
                <?php echo $item['identificador'];?>
              </option>
            <?php }; ?>   
          </select>
        </td>
        <td class = 'rojo'>Modo Sueldo</td>
        <td>
          <select size="1" name="cmbModoSueldo" id="cmbModoSueldo">
            <option>-</option>
            <option>Fijo</option>
            <option>Variable</option>
            <option>Preferencial</option>
          </select>
        </td>
      </tr>
      <tr>
        <td class = 'rojo'>Tipo Trabaj. (*)</td>
        <td>
          <select size="1" name="cmbTipoTrabajador" id="cmbTipoTrabajador">
            <option selected>-</option>
            <option>Conductor</option>
            <option>Auxiliar</option>
            <option>Coordinador</option>
            <option>Otro</option>
          </select>
        </td>
        <td class ='adicManejar'><div class = 'puedeManejar'>Categ. Licencia</div></td>
        <td>
          <div class = 'puedeManejar'>
          <select size="1" name="txtCategoria" id="txtCategoria"  style="position: relative; width: 80" >
            <option value="-">-</option>
            <?php foreach($categorias as $categoria) { ?>
              <option value="<?php echo $categoria['idCategoria']; ?>">
                <?php echo $categoria['idCategoria']." ".$categoria['descripcion'] ;?>
              </option>
            <?php }; ?>
          </select>
          </div>
        </td>
        <td class ='adicManejar'><div class = 'puedeManejar'>Nro. Licencia</div></td>
        <td><div class = 'puedeManejar'><input type="text" name="txtNroLicencia" size="10"></div></td>
      </tr>

      <tr class ='paraPracticante'>
        <td class ='rojo'>Subvención mensual</td>
        <td>
          <input type="text" name="txtSubvencionMensual" size="10" >
        </td>
        <td></td><td></td>
        <td></td><td></td>
      </tr>

      <tr>
        <td><div class = 'infoBanco'>Banco</div></td>
        <td>
          <div class = 'infoBanco'>
          <select size="1" name="cmbBanco" id="cmbBanco">
            <?php       
                echo "<option selected>-</option>";
                foreach($arrBancos AS $banco){
                  echo "<option>".$banco."</option>";
                }
            ?>
          </select>
          </div>

        </td>
        <td><div class = 'infoBanco'>Nro Cuenta</div></td>
        <td><div class = 'infoBanco'><input type="text" name="txtNroCuenta" size="10"></div></td>
        <td><div class = 'infoBanco'>Tipo Cuenta</div></td>
        <td><div class = 'infoBanco'><input type="text" name="txtTipoCuenta" size="10"></div></td>
      </tr>
      <tr>
        <td><div class = 'infoBanco'>Moneda</div></td>
        <td><div class = 'infoBanco'><input type="text" name="txtMoneda" size="10"></div></td>
        <td><div class = 'infoBanco'>Forma Pago</div></td>
        <td><div class = 'infoBanco'><input type="text" name="txtFormaPago" size="10"></div></td>
        <td><div class = 'cajaChica'>Caja Chica</div></td>
        <td><div class = 'cajaChica'><input type="text" name="txtCajaChica" size="10"></div></td>
        
      </tr>

      <tr>
        <td><div class='5ta'>Remun. Básica</div></td>
        <td><div class='5ta'><input type="text" name="txtRemunBasica" size="11"></div></td>
        <td><div class='5ta'>Asig. Familiar</div></td>
        <td><div class='5ta'><input type="text" name="txtAsigFamiliar" size="11"></div></td>
        <td><div class='5ta'>Vacac. Anual</div></td>
        <td><div class='5ta'>
          <select name = 'cmbDiasVacacAnual' id = 'cmbDiasVacacAnual'>
            <option>-</option>
            <option>15</option>
            <option>30</option>
          </select>
           días
        </div></td>
      </tr>
      <tr>
        <td colspan="2"><div class='5ta'>Desea Dscto EsSalud Vida</div></td>
        <td><div class='5ta'>
          <select size="1" name="cmbDeseaDscto">
            <option>Si</option>
            <option selected>No</option>
          </select>
          </div>
        </td>
        
      </tr>
      <tr>
        <td><div class='5ta'>AFP / SNP</div></td>
        <td><div class='5ta'>
          <select size="1" name="cmbEntidadPension" id="cmbEntidadPension" style="position: relative; width: 80" >
            <option value="">-</option>
            <?php foreach($entidadPensiones as $item) { ?>
              <option value="<?php echo $item['nombre'] ; ?>">
              <?php echo $item['nombre'];?>
              </option>
            <?php }; ?>
          </select>
          </div>   
        </td>
        <td><div class="tipoComision">Tipo Comisión</div></td>
        <td>
          <div class="tipoComision">
          <select size="1" name="cmbTipoComision" id="cmbTipoComision">
          <option selected>-</option> 
          <option>Flujo</option>
          <option>Mixto</option>
          </select>   
          </div>
        </td>
        <td><div class="tipoComision">CUSPP</div></td>
        <td><div class="tipoComision"><input type="text" name="txtCuspp" size="10"></div></td>
      </tr>
      <tr>
      	<td  id = 'adic02' >
      	  <div class="4ta">RUC</div>
      	  <div class='5ta'>Renta 5ta</div>
      	</td>
      	<td>
      	  <div class="4ta"><input type="text" name="txtRuc" size="10" maxlength = '11'></div>
      	  <div class="5ta"><input type="text" name="txtRta5ta" size="10"></div>	

      	</td>
      </tr>


      <tr>
        <th colspan="6">
          Cuotas Para el Fondo de Garantía y Uso de Master
        </th>
      </tr>
      <tr>
        <td class="rojo">Cuotas</td>
        <td>
          <select size="1" name="cmbCuotasFondo">
            <option>-</option>
            <?php
            foreach($arrNroCuotasFondo AS $fondo){
              echo "<option>".$fondo."</option>";
            }
            ?>
          </select>
        </td>
        <td class = 'rojo'>Fch Inicio Dscto</td>
        <td><input type="text" name="txtFchIniDscto" id="txtFchIniDscto"  size="11"></td>
        <td></td><td></td>
      </tr>
      <tr>
        <td>Es Master</td>
        <td>
          <select size="1" name="cmbEsMaster">
            <option>Si</option>
            <option selected>No</option>
          </select>
        </td>
        <td>Precio Master</td><td><input type="text" name="txtPrecioMaster" size="11"></td>
        <td></td><td></td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td><td><input type="submit" value="Enviar" name="btnEnviar"></td>
      </tr>

    </table>
    </div>
  </div>
  <div class="section" id="section1">
    <div style="float:right; text-align:right;">
    <table border = '0' width="800px" style="border:black 1px solid;">
      <tr>
        <th colspan="6">
          Datos Familiares
        </th>
      </tr>
      <tr>
        <td colspan="6">Padre</td>
      </tr>
      <tr>
        <td width="90" >Ap. Paterno</td>
        <td width="150" ><input type="text" name="txtApPaternoPadre" size="12"></td>
        <td width="90" >Ap. Materno</td>
        <td width="150" ><input type="text" name="txtApMaternoPadre" size="12"></td>
        <td width="90" >Nombres</td>
        <td><input type="text" name="txtNombresPadre" size="12"></td>
      </tr>
      <tr>
        <td>Ocupación</td><td><input type="text" name="txtOcupacionPadre" size="12"></td>
        <td></td><td></td>
        <td></td><td></td>
      </tr>
      <tr>
        <td colspan="6">Madre</td>
      </tr>
      <tr>
        <td width="90" >Ap. Paterno</td>
        <td width="150" ><input type="text" name="txtApPaternoMadre" size="12"></td>
        <td width="90" >Ap. Materno</td>
        <td width="150" ><input type="text" name="txtApMaternoMadre" size="12"></td>
        <td width="90" >Nombres</td>
        <td><input type="text" name="txtNombresMadre" size="12"></td>
      </tr>
      <tr>
        <td>Ocupación</td><td><input type="text" name="txtOcupacionMadre" size="12"></td>
        <td></td><td></td>
        <td></td><td></td>
      </tr>
      <tr>
        <td colspan="6"><div class = 'estadoCasado'>Conyugue o conviviente</div></td>
      </tr>
      <tr>
        <td width="90" ><div class = 'estadoCasado'>Ap. Paterno</div></td>
        <td width="150" ><div class = 'estadoCasado'><input type="text" name="txtApPaternoConyu" size="12"></div></td>
        <td width="90" ><div class = 'estadoCasado'>Ap. Materno</div></td>
        <td width="150" ><div class = 'estadoCasado'><input type="text" name="txtApMaternoConyu" size="12"></div></td>
        <td width="90" ><div class = 'estadoCasado'>Nombres</div></td>
        <td><div class = 'estadoCasado'><input type="text" name="txtNombresConyu" size="12"></div></td>
      </tr>
      <tr>
        <td><div class = 'estadoCasado'>Ocupación</div></td><td><div class = 'estadoCasado'><input type="text" name="txtOcupacionConyu" size="12"></div></td>
        <td></td><td></td>
        <td></td><td></td>
      </tr>
      <tr>
        <th colspan="6">Datos de Ubicación</th>
      </tr>
      <tr>
        <td colspan="2">Dirección Actual Av. Jr. Calle:</td>
        <td colspan="2"><input type="text" name="txtDirCalle" size="28"></td>
        <td>Nro / Interior</td><td><input type="text" name="txtDirNro" size="11"></td>
      </tr>
      <tr>
        <td>Urbanización</td><td colspan="2"><input type="text" name="txtDirUrb" size="35"></td>
        <td>Distrito</td><td colspan="2"><input type="text" name="txtDirDistrito" size="35"></td>
      </tr>
      <tr>
        <td>Provincia</td><td colspan="2"><input type="text" name="txtDirProvincia" size="35"></td>
        <td>Departamento</td><td colspan="2"><input type="text" name="txtDirDepartamento" size="35"></td>        
      </tr>
      <tr>
        <td colspan="2">Referencia para llegar</td><td colspan="4"><input type="text" name="txtDirReferencia" size="80"></td>
      </tr>
      <tr>
        <td valign="top">eMail </td>
        <td valign="top" colspan="3">
          <input type="text" name="txtEmail" size="60">
        </td>
        <td></td><td></td>
      </tr>
      <tr>
        <td>Telf. Fijo</td><td><input type="text" name="txtTelfFijo" size="11"></td>
        <td>Telf. Móvil</td><td><input type="text" name="txtTelfMovil" size="11"></td>
        <td>Telf Adicional</td><td><input type="text" name="txtTelfAdicional" size="11"></td>
      </tr>
      <tr>
        <td>Telf. Referencia</td><td><input type="text" name="txtTelfReferencia" size="11"></td>
        <td>Preguntar por</td><td><input type="text" name="txtTelfRefContacto" size="11"></td>
        <td>Parentesco</td><td><input type="text" name="txtTelfRefParentesco" size="11"></td>
      </tr>
    </table> 
    </div>  
  </div>

  <div class="section" id="section2">
    <div style="float:right; text-align:right;">
    <table border = '0' width="800px" style="border:black 1px solid;">
      <tr>
        <th colspan="6">
          Estudios Relizados
        </th>
      </tr>
      <tr>
        <th colspan="6">Secundarios</th>
      </tr>
      <tr>
        <th width="90" >Ańo</th><td width="150" ><input type="text" name="txtSec01Anhio" size="12"></td>
        <td width="90" >Grado</td><td width="150" ><input type="text" name="txtSec01Grado" size="12"></td>
        <td width="90" >Centro Estudios</td><td><input type="text" name="txtSec01Centro" size="12"></td>
      </tr>
      <tr>
        <th width="90" >Ańo</th><td width="150" ><input type="text" name="txtSec02Anhio" size="12"></td>
        <td width="90" >Grado</td><td width="150" ><input type="text" name="txtSec02Grado" size="12"></td>
        <td width="90" >Centro Estudios</td><td><input type="text" name="txtSec02Centro" size="12"></td>
      </tr>
      <tr>
        <th colspan="6">Estudios Superiores</th>
      </tr>
      <tr>
        <th width="90" >Desde</th><td width="150" ><input type="text" name="txtSup01Desde" size="12"></td>
        <td width="90" >Hasta</td><td width="150" ><input type="text" name="txtSup01Hasta" size="12"></td>
        <td width="90" >Carrera</td><td><input type="text" name="txtSup01Carrera" size="12"></td>
      </tr>
      <tr>
        <td width="90" >Institución</td><td width="150" ><input type="text" name="txtSup01Centro" size="12"></td>
        <td width="90" >Avance</td>
        <td width="150" >
          <select size="1" name="cmbSup01Avance" id="cmbSup01Avance">
          <option>-</option>
          <option>Completo</option>
          <option>Incompleto</option>
          </select>
        </td>
        <td width="90" >Grado</td><td><input type="text" name="txtSup01Grado" size="12"></td>
      </tr>
      <tr>
        <th width="90" >Desde</th><td width="150" ><input type="text" name="txtSup02Desde" size="12"></td>
        <td width="90" >Hasta</td><td width="150" ><input type="text" name="txtSup02Hasta" size="12"></td>
        <td width="90" >Carrera</td><td><input type="text" name="txtSup02Carrera" size="12"></td>
      </tr>
      <tr>
        <td width="90" >Institución</td><td width="150" ><input type="text" name="txtSup02Centro" size="12"></td>
        <td width="90" >Avance</td>
        <td width="150" >
          <select size="1" name="cmbSup02Avance" id="cmbSup02Avance">
          <option>-</option>
          <option>Completo</option>
          <option>Incompleto</option>
          </select>
        </td>
        <td width="90" >Grado</td><td><input type="text" name="txtSup02Grado" size="12"></td>
      </tr>
      <tr>
        <th colspan="6">
          Experiencia Laboral (Las 3 últimas)
        </th>
      </tr>
      <tr>
        <th width="90" >Empresa 01</th><td width="150" ><input type="text" name="txtExp01Empresa" size="12"></td>
        <td width="90" >Cargo</td><td width="150" ><input type="text" name="txtExp01Cargo" size="12"></td>
        <td width="90" >Sueldo</td><td><input type="text" name="txtExp01Sueldo" size="12"></td>
      </tr>
      <tr>
        <td width="90" >Desde</td><td width="150" ><input type="text" name="txtExp01Desde" size="12"></td>
        <td width="90" >Hasta</td><td width="150" ><input type="text" name="txtExp01Hasta" size="12"></td>
        <td width="90" ></td><td></td>
      </tr>
      <tr>
        <td width="90" >Jefe inmediato</td><td width="150" ><input type="text" name="txtExp01Jefe" size="12"></td>
        <td width="90" >Puesto</td><td width="150" ><input type="text" name="txtExp01JefePuesto" size="12"></td>
        <td width="90" >Teléfono</td><td><input type="text" name="txtExp01Telf" size="12"></td>
      </tr>
      <tr>
        <th width="90" >Empresa 02</th><td width="150" ><input type="text" name="txtExp02Empresa" size="12"></td>
        <td width="90" >Cargo</td><td width="150" ><input type="text" name="txtExp02Cargo" size="12"></td>
        <td width="90" >Sueldo</td><td><input type="text" name="txtExp02Sueldo" size="12"></td>
      </tr>
      <tr>
        <td width="90" >Desde</td><td width="150" ><input type="text" name="txtExp02Desde" size="12"></td>
        <td width="90" >Hasta</td><td width="150" ><input type="text" name="txtExp02Hasta" size="12"></td>
        <td width="90" ></td><td></td>
      </tr>
      <tr>
        <td width="90" >Jefe inmediato</td><td width="150" ><input type="text" name="txtExp02Jefe" size="12"></td>
        <td width="90" >Puesto</td><td width="150" ><input type="text" name="txtExp02JefePuesto" size="12"></td>
        <td width="90" >Teléfono</td><td><input type="text" name="txtExp02Telf" size="12"></td>
      </tr>
      <tr>
        <th width="90" >Empresa 03</th><td width="150" ><input type="text" name="txtExp03Empresa" size="12"></td>
        <td width="90" >Cargo</td><td width="150" ><input type="text" name="txtExp03Cargo" size="12"></td>
        <td width="90" >Sueldo</td><td><input type="text" name="txtExp03Sueldo" size="12"></td>
      </tr>
      <tr>
        <td width="90" >Desde</td><td width="150" ><input type="text" name="txtExp03Desde" size="12"></td>
        <td width="90" >Hasta</td><td width="150" ><input type="text" name="txtExp03Hasta" size="12"></td>
        <td width="90" ></td><td></td>
      </tr>
      <tr>
        <td width="90" >Jefe inmediato</td><td width="150" ><input type="text" name="txtExp03Jefe" size="12"></td>
        <td width="90" >Puesto</td><td width="150" ><input type="text" name="txtExp03JefePuesto" size="12"></td>
        <td width="90" >Teléfono</td><td><input type="text" name="txtExp03Telf" size="12"></td>
      </tr>    
    </table> 
    </div> 
  </div>

  <div class="section" id="section3">

    <div style="float:right; text-align:right;">
    <table border = '0' width="800px" style="border:black 1px solid;">
      <tr>
        <th colspan="6">
          Relación con Inversiones Moy
        </th>
      </tr>
      <tr>
        <td colspan="2" >Laboró anteriormente en Inversiones Moy &nbsp;&nbsp;&nbsp;&nbsp;
          <select size="1" name="cmbLaboroEnMoy" id="cmbLaboroEnMoy">
          <option>-</option>
          <option>Si</option>
          <option>No</option>
          </select>
        </td>
        <td width="90" ><div class= 'laboroEnMoy'>Puesto</div></td>
        <td width="150"><div class= 'laboroEnMoy'><input type="text" name="txtLaboroEnMoyPuesto" size="12"></div></td>
        <td width="90" ><div class= 'laboroEnMoy'>Sucursal</div></td>
        <td><div class= 'laboroEnMoy'><input type="text" name="txtLaboroEnMoySucursal" size="12"></div></td>
      </tr>
      <tr>
        <td width="150" ><div class= 'laboroEnMoy'>Motivo de Cese</div></td>
        <td><div class= 'laboroEnMoy'><input type="text" name="txtLaboroEnMoyMotivoCese" size="15"></div></td>
        <td  width="90" ><div class= 'laboroEnMoy'>Fecha de Cese</div></td>
        <td><div class= 'laboroEnMoy'><input type="text" name="txtLaboroEnMoyFchCese" id="txtLaboroEnMoyFchCese"  size="12"></div></td>
        <td></td><td></td>
      </tr>
      <tr>
        <td colspan="2">Tiene familiar o amigo que labore en Inv. Moy 
          <select size="1" name="cmbFamiliarEnMoy" id="cmbFamiliarEnMoy">
          <option>-</option>
          <option>Si</option>
          <option>No</option>
          </select>
        </td>
        <td><div class = 'familiarEnMoy'>Nombre y Apellido</div></td>
        <td><div class = 'familiarEnMoy'>
          <input type="text" name="txtFamiliarEnMoyNombre" size="12"></div>
        </td>
        <td><div class = 'familiarEnMoy'>Parentesco</div></td>
        <td><div class = 'familiarEnMoy'>
          <input type="text" name="txtFamiliarEnMoyParentesco" size="12"></div>
        </td>
      </tr>
      <tr>
        <th colspan="6">
          En caso de Emergencia avisar a
        </th>
      </tr>
      <tr>
        <td width="90" >Nombres y Apellidos</td><td width="150" ><input type="text" name="txtEmergenciaNombre" size="12"></td>
        <td width="90" >Parentesco</td><td width="150" ><input type="text" name="txtEmergenciaParentesco" size="12"></td>
        <td width="90" >Teléfono Fijo</td><td><input type="text" name="txtEmergenciaTelfFijo" size="12"></td>
      </tr>
      <tr>
        <td>Teléfono Celular</td><td><input type="text" name="txtEmergenciaTelfCelular" size="12"></td>
        <td></td><td></td>
        <td></td><td></td>
      </tr>
      
      <tr>
        <th colspan="6">
          Salud
        </th>
      </tr>
      <tr>
        <td>Grupo Sanguineo</td><td><input type="text" name="txtSaludGrupoSanguineo" size="12"></td>
        <td></td><td></td>
        <td></td><td></td>
      </tr>
      <tr>
        <td colspan="2">żTiene alguna enfermedad crónica? 
          <select size="1" name="cmbSaludTieneEnfCronica" id="cmbSaludTieneEnfCronica">
          <option>-</option>
          <option>Si</option>
          <option>No</option>
          </select>
        </td>
        <td colspan="4" rowspan="2">
          <div class = 'enfermedadCronica'>
          <textarea rows = '2' cols="50"  name="txtSaludEnfCronica"></textarea>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2" align="right"> <div class = 'enfermedadCronica'>Especificar</div></td>
      </tr>

      <tr>
        <td colspan="2">żPadece algún tipo de Alergia? 
          <select size="1" name="cmbSaludTieneAlergia" id="cmbSaludTieneAlergia">
          <option>-</option>
          <option>Si</option>
          <option>No</option>
          </select>
        </td>
        <td colspan="4" rowspan="2">
          <div class = 'alergia'>
          <textarea rows = '2' cols="50"  name="txtSaludAlergia"></textarea>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2" align="right"> <div class = 'alergia'>Especificar</div></td>
      </tr>
    </table> 
    </div> 
  </div>
  </form>
  </div>
  </body>
</html>

<?php
 } else {
  $usuario = $_SESSION["usuario"];//
  $nroDocTrab = $_POST['txtNroDocTrab'];//
  $tipoDocTrab = $_POST['cmbTipoDocum'];
  $sexo = $_POST['cmbSexo'];//
  $apPaterno = $_POST['txtApPaterno'];//
  $apMaterno = $_POST['txtApMaterno'];//
  $nombres = $_POST['txtNombres'];//
  $fchNac = $_POST['txtFchNac'];//
  $estadoTrab = $_POST['cmbEstadoTrab'];//
  $dirCalle = $_POST['txtDirCalle'];//
  $dirNro = $_POST['txtDirNro'];//
  $dirUrb = $_POST['txtDirUrb'];//
  $dirDistrito = $_POST['txtDirDistrito'];//
  $dirReferencia = $_POST['txtDirReferencia'];
  $eMail = $_POST['txtEmail'];//
  $telfFijo = $_POST['txtTelfFijo'];//
  $telfMovil = $_POST['txtTelfMovil'];//
  $telfAdicional = $_POST['txtTelfAdicional'];//
  $telfReferencia = $_POST['txtTelfReferencia'];//
  $telfRefContacto = $_POST['txtTelfRefContacto'];//
  $telfRefParentesco = $_POST['txtTelfRefParentesco'];//
  $nroHijos = $_POST['cmbNroHijos'];//
  $apPaternoPadre = $_POST['txtApPaternoPadre'];//
  $apMaternoPadre = $_POST['txtApMaternoPadre'];//
  $nombresPadre = $_POST['txtNombresPadre'];//
  $ocupacionPadre = $_POST['txtOcupacionPadre'];//
  $apPaternoMadre = $_POST['txtApPaternoMadre'];//
  $apMaternoMadre = $_POST['txtApMaternoMadre'];//
  $nombresMadre = $_POST['txtNombresMadre'];//
  $ocupacionMadre = $_POST['txtOcupacionMadre'];//
  $apPaternoConyu = $_POST['txtApPaternoConyu'];//
  $apMaternoConyu = $_POST['txtApMaternoConyu'];//
  $nombresConyu = $_POST['txtNombresConyu'];//
  $ocupacionConyu = $_POST['txtOcupacionConyu'];//


  $sec01Anhio = $_POST['txtSec01Anhio'];
  $sec01Grado = $_POST['txtSec01Grado'];
  $sec01Centro = $_POST['txtSec01Centro'];
  $sec02Anhio = $_POST['txtSec02Anhio'];
  $sec02Grado = $_POST['txtSec02Grado'];
  $sec02Centro = $_POST['txtSec02Centro'];
  $sup01Desde = $_POST['txtSup01Desde'];
  $sup01Hasta = $_POST['txtSup01Hasta'];
  $sup01Carrera = $_POST['txtSup01Carrera'];
  $sup01Centro = $_POST['txtSup01Centro'];
  $sup01Avance = $_POST['cmbSup01Avance'];
  $sup01Grado = $_POST['txtSup01Grado'];
  $sup02Desde = $_POST['txtSup02Desde'];
  $sup02Hasta = $_POST['txtSup02Hasta'];
  $sup02Carrera = $_POST['txtSup02Carrera'];
  $sup02Centro = $_POST['txtSup02Centro'];
  $sup02Avance = $_POST['cmbSup02Avance'];
  $sup02Grado = $_POST['txtSup02Grado'];        
  $exp01Empresa = $_POST['txtExp01Empresa'];
  $exp01Cargo = $_POST['txtExp01Cargo'];
  $exp01Sueldo = $_POST['txtExp01Sueldo'];    
  $exp01Desde = $_POST['txtExp01Desde'];
  $exp01Hasta = $_POST['txtExp01Hasta'];
  $exp01Jefe = $_POST['txtExp01Jefe'];
  $exp01JefePuesto = $_POST['txtExp01JefePuesto'];
  $exp01Telf = $_POST['txtExp01Telf'];
  $exp02Empresa = $_POST['txtExp02Empresa'];
  $exp02Cargo = $_POST['txtExp02Cargo'];
  $exp02Sueldo = $_POST['txtExp02Sueldo'];    
  $exp02Desde = $_POST['txtExp02Desde'];
  $exp02Hasta = $_POST['txtExp02Hasta'];
  $exp02Jefe = $_POST['txtExp02Jefe'];
  $exp02JefePuesto = $_POST['txtExp02JefePuesto'];
  $exp02Telf = $_POST['txtExp02Telf'];
  $exp03Empresa = $_POST['txtExp03Empresa'];
  $exp03Cargo = $_POST['txtExp03Cargo'];
  $exp03Sueldo = $_POST['txtExp03Sueldo'];    
  $exp03Desde = $_POST['txtExp03Desde'];
  $exp03Hasta = $_POST['txtExp03Hasta'];
  $exp03Jefe = $_POST['txtExp03Jefe'];
  $exp03JefePuesto = $_POST['txtExp03JefePuesto'];
  $exp03Telf = $_POST['txtExp03Telf'];
        
  $laboroEnMoy = $_POST['cmbLaboroEnMoy'];
  $laboroEnMoyPuesto = $_POST['txtLaboroEnMoyPuesto'];
  $laboroEnMoySucursal = $_POST['txtLaboroEnMoySucursal'];
  $laboroEnMoyMotivoCese = $_POST['txtLaboroEnMoyMotivoCese'];
  $laboroEnMoyFchCese = $_POST['txtLaboroEnMoyFchCese'];
  $familiarEnMoy = $_POST['cmbFamiliarEnMoy'];
  $familiarEnMoyNombre = $_POST['txtFamiliarEnMoyNombre'];
  $familiarEnMoyParentesco = $_POST['txtFamiliarEnMoyParentesco'];
  $emergenciaNombre = $_POST['txtEmergenciaNombre'];
  $emergenciaParentesco = $_POST['txtEmergenciaParentesco'];
  $emergenciaTelfFijo = $_POST['txtEmergenciaTelfFijo'];
  $emergenciaTelfCelular = $_POST['txtEmergenciaTelfCelular'];
  $saludGrupoSanguineo = $_POST['txtSaludGrupoSanguineo'];
  $saludTieneEnfCronica = $_POST['cmbSaludTieneEnfCronica'];
  $saludEnfCronica = $_POST['txtSaludEnfCronica'];
  $saludTieneAlergia = $_POST['cmbSaludTieneAlergia'];//+
  $saludAlergia = $_POST['txtSaludAlergia'];//+
  $polo = $_POST['txtPolo'];//+
  $pantalon = $_POST['txtPantalon'];
  $botas = $_POST['txtBotas'];
  $casaca = $_POST['txtCasaca'];//+
  $categTrab = $_POST['cmbCategTrab'];
  $modoContratacion = $_POST['cmbModoContratacion'];
  $modoSueldo = $_POST['cmbModoSueldo'];
  $tipoTrabajador = $_POST['cmbTipoTrabajador'];
  $nroLicencia = $_POST['txtNroLicencia'];

  $esMaster = $_POST['cmbEsMaster'];

  $bancoNombre = $_POST['cmbBanco'];
  $bancoNroCuenta = $_POST['txtNroCuenta'];
  $bancoTipoCuenta = $_POST['txtTipoCuenta'];
  $bancoMoneda = $_POST['txtMoneda'];

  $deseaDscto = $_POST['cmbDeseaDscto'];

  $idTrabajador = $nroDocTrab;

  //$tipoTrabajador = "";
  $imgTrabajador =  "";
  $codTrabajador =  $camisa = "";
  $deseaDsctoOnp = "";
  $asumeEmpresa = "";
  $calleyNro =  "";

  if ($_POST['cmbEstadoCivil']== '-') $estadoCivil = NULL; else $estadoCivil = $_POST['cmbEstadoCivil'];
  if ($_POST['txtEstadoCivilDesde']== '') $estadoCivilDesde = NULL; else $estadoCivilDesde = $_POST['txtEstadoCivilDesde'];


  if (empty($_POST['cmbEntidadPension'])) $entidadPension = NULL; else $entidadPension = $_POST['cmbEntidadPension'];
  if ($_POST['cmbTipoComision']=='-') $tipoComision = NULL; else $tipoComision = $_POST['cmbTipoComision'];
  if ($_POST['txtCategoria'] == '-') $categoria = NULL; else $categoria = $_POST['txtCategoria'];
  if (empty($_POST['txtRemunBasica'])) $remunBasica = 0; else $remunBasica = $_POST['txtRemunBasica'];
  if (empty($_POST['txtAsigFamiliar'])) $asigFamiliar = 0; else $asigFamiliar = $_POST['txtAsigFamiliar'];
  if (empty($_POST['txtCuspp'])) $cuspp = NULL; else $cuspp = $_POST['txtCuspp'];
  //if (empty($_POST['txtCodTrabajador'])) $codTrabajador = NULL; else $codTrabajador = $_POST['txtCodTrabajador'];
  if (empty($_POST['txtCajaChica'])) $cajaChica = NULL; else $cajaChica = $_POST['txtCajaChica'];
  if ($_POST['cmbModoSueldo']== '-') $modoSueldo = NULL; else $modoSueldo = $_POST['cmbModoSueldo'];
  if (empty($_POST['txtProvincia'])) $dirProvincia = NULL; else $dirProvincia = $_POST['txtProvincia'];  
  if (empty($_POST['txtDepartamento'])) $dirDepartamento = NULL; else $dirDepartamento = $_POST['txtDepartamento'];
  if (empty($_POST['txtPrecioMaster'])) $precioMaster = NULL; else $precioMaster = $_POST['txtPrecioMaster'];
  //if (empty($_POST['cmbDeseaDsctoOnp'])) $deseaDsctoOnp = NULL; else $deseaDsctoOnp = $_POST['cmbDeseaDsctoOnp'];
  if (empty($_POST['txtRta5ta'])) $rta5ta = NULL; else $rta5ta = $_POST['txtRta5ta'];
  if (empty($_POST['txtRuc'])) $ruc = NULL; else $ruc = $_POST['txtRuc'];
  if (empty($_POST['txtFormaPago'])) $formaPago = NULL; else $formaPago = $_POST['txtFormaPago'];

  if ($_POST['cmbDiasVacacAnual'] == '-') $diasVacacAnual = NULL; else $diasVacacAnual = $_POST['cmbDiasVacacAnual'];


  if ($categTrab != '5ta'){
    $renta5ta = NULL;
    $diasVacacAnual = NULL;
    $remunBasica = NULL;
    $asigFamiliar = NULL;

  } 

  //Es un ajuste para ańadir al practicante. Debe ir en esta posición
  if($categTrab == 'Practicante'){
    $remunBasica = $_POST['txtSubvencionMensual'];
  }

  if ($saludTieneEnfCronica != 'Si') $saludEnfCronica = NULL;
  if ($saludTieneAlergia != 'Si') $saludAlergia = NULL;
  

  /*
  echo "<pre>";
  print_r($_POST);
  echo "</pre>";
  */

  //echo "ańo $sec01Anhio, grado $sec01Grado, centro $sec01Centro ";

  $inserta = $db->prepare("INSERT INTO trabajador (idTrabajador,  `tipoDocTrab`, tipoTrabajador, apPaterno, apMaterno, nombres, fchNacimiento, imgTrabajador, telfMovil, dirCalleyNro, dirDistrito, dirProvincia, dirDepartamento ,dirTelefono, dirObservacion, eMail, estadoCivil,  licenciaCategoria, modoContratacion, bancoNombre, bancoNroCuenta, bancoTipoCuenta, bancoMoneda, categTrabajador, ruc, estadoTrabajador, modoSueldo, remuneracionBasica, asignacionFamiliar, cajaChica,esMaster ,precioMaster , deseaDscto, deseaDsctoOnp, entidadPension, tipoComision, asumeEmpresa, cuspp, formaPago, tallaPolo, tallaPantalon, tallaBotas, tallaCasaca, licenciaNro, sexo, renta5ta,
  dirNro, dirUrb, telfAdicional, telfReferencia, telfRefContacto, telfRefParentesco, estadoCivilDesde, nroHijos, apPaternoPadre, apMaternoPadre, nombresPadre, ocupacionPadre, apPaternoMadre, apMaternoMadre, nombresMadre, ocupacionMadre, apPaternoConyu, apMaternoConyu, nombreConyuge, ocupacionConyu, 
  sec01Anhio, sec01Grado, sec01Centro, sec02Anhio, sec02Grado, sec02Centro, sup01Desde, sup01Hasta, sup01Carrera, sup01Centro, sup01Avance, sup01Grado, sup02Desde, sup02Hasta, sup02Carrera, sup02Centro, sup02Avance, sup02Grado, exp01Empresa, exp01Cargo, exp01Sueldo, exp01Desde, exp01Hasta, exp01Jefe, exp01JefePuesto, exp01Telf, exp02Empresa, exp02Cargo, exp02Sueldo, exp02Desde, exp02Hasta, exp02Jefe, exp02JefePuesto, exp02Telf, exp03Empresa, exp03Cargo, exp03Sueldo, exp03Desde, exp03Hasta, exp03Jefe, exp03JefePuesto, exp03Telf, 
   `laboroEnMoy`, `laboroEnMoyPuesto`, `laboroEnMoySucursal`, `laboroEnMoyMotivoCese`, `laboroEnMoyFchCese`, `familiarEnMoy`, `familiarEnMoyNombre`, `familiarEnMoyParentesco`, `emergenciaNombre`, `emergenciaParentesco`, `emergenciaTelfFijo`, `emergenciaTelfCelular`, `saludGrupoSanguineo`, `saludTieneEnfCronica`, `saludTieneAlergia`, `saludAlergia`, `saludEnfCronica` , diasVacacAnual, usuario, fchCreacion) VALUES ( :idTrabajador, :tipoDocTrab,  :tipoTrabajador, :apPaterno, :apMaterno, :nombres, :fchNacimiento, :imgTrabajador, :telfMovil, :dirCalleyNro, :dirDistrito, :dirProvincia, :dirDepartamento, :dirTelefono, :dirObservacion, :eMail, :estadoCivil, :licenciaCategoria, :modoContratacion, :bancoNombre, :bancoNroCuenta, :bancoTipoCuenta, :bancoMoneda, :categTrabajador, :ruc, :estadoTrabajador , :modoSueldo, :remunBasica, :asigFamiliar, :cajaChica, :esMaster, :precioMaster, :deseaDscto, :deseaDsctoOnp, :entidadPension, :tipoComision, :asumeEmpresa, :cuspp, :formaPago, :tallaPolo, :tallaPantalon, :tallaBotas,  :tallaCasaca, :licenciaNro, :sexo, :renta5ta,
    :dirNro, :dirUrb, :telfAdicional, :telfReferencia, :telfRefContacto, :telfRefParentesco, :estadoCivilDesde, :nroHijos, :apPaternoPadre, :apMaternoPadre, :nombresPadre, :ocupacionPadre, :apPaternoMadre, :apMaternoMadre, :nombresMadre, :ocupacionMadre, :apPaternoConyu, :apMaternoConyu, :nombreConyuge, :ocupacionConyu, 
    :sec01Anhio, :sec01Grado, :sec01Centro, :sec02Anhio, :sec02Grado, :sec02Centro, :sup01Desde, :sup01Hasta, :sup01Carrera, :sup01Centro, :sup01Avance, :sup01Grado, :sup02Desde, :sup02Hasta, :sup02Carrera, :sup02Centro, :sup02Avance, :sup02Grado, :exp01Empresa, :exp01Cargo, :exp01Sueldo, :exp01Desde, :exp01Hasta, :exp01Jefe, :exp01JefePuesto, :exp01Telf, :exp02Empresa, :exp02Cargo, :exp02Sueldo, :exp02Desde, :exp02Hasta, :exp02Jefe, :exp02JefePuesto, :exp02Telf, :exp03Empresa, :exp03Cargo, :exp03Sueldo, :exp03Desde, :exp03Hasta, :exp03Jefe, :exp03JefePuesto, :exp03Telf, 
     :laboroEnMoy, :laboroEnMoyPuesto, :laboroEnMoySucursal, :laboroEnMoyMotivoCese, :laboroEnMoyFchCese, :familiarEnMoy, :familiarEnMoyNombre, :familiarEnMoyParentesco, :emergenciaNombre, :emergenciaParentesco, :emergenciaTelfFijo, :emergenciaTelfCelular, :saludGrupoSanguineo, :saludTieneEnfCronica, :saludTieneAlergia, :saludAlergia, :saludEnfCronica , :diasVacacAnual, :usuario, curdate() )");
 
  $inserta->bindParam(':idTrabajador',$idTrabajador);//ok

  $inserta->bindParam(':tipoDocTrab',$tipoDocTrab);//ok


  $inserta->bindParam(':tipoTrabajador',$tipoTrabajador);//ok
  $inserta->bindParam(':apPaterno',$apPaterno);//ok
  $inserta->bindParam(':apMaterno',$apMaterno);//ok
  $inserta->bindParam(':nombres',$nombres);//ok
  $inserta->bindParam(':fchNacimiento',$fchNac);//ok
  $inserta->bindParam(':imgTrabajador',$imgTrabajador);
  $inserta->bindParam(':telfMovil',$telfMovil);//ok
  $inserta->bindParam(':dirCalleyNro',$dirCalle);//ok
  $inserta->bindParam(':dirDistrito',$dirDistrito);//ok
  $inserta->bindParam(':dirProvincia',$dirProvincia);//ok
  $inserta->bindParam(':dirDepartamento',$dirDepartamento);//ok
  $inserta->bindParam(':dirTelefono',$telfFijo);//ok
  $inserta->bindParam(':dirObservacion',$dirReferencia);
  $inserta->bindParam(':eMail',$eMail);//ok
  $inserta->bindParam(':estadoCivil',$estadoCivil);//ok
  $inserta->bindParam(':sexo',$sexo);//ok
  $inserta->bindParam(':dirNro',$dirNro);//ok
  $inserta->bindParam(':dirUrb',$dirUrb);//ok
  $inserta->bindParam(':telfAdicional',$telfAdicional);//ok
  $inserta->bindParam(':telfReferencia',$telfReferencia);//ok
  $inserta->bindParam(':telfRefContacto',$telfRefContacto);//ok
  $inserta->bindParam(':telfRefParentesco',$telfRefParentesco);//ok
  $inserta->bindParam(':estadoCivilDesde',$estadoCivilDesde);//ok
  $inserta->bindParam(':nroHijos',$nroHijos);//ok
  $inserta->bindParam(':apPaternoPadre',$apPaternoPadre);//ok
  $inserta->bindParam(':apMaternoPadre',$apMaternoPadre);//ok
  $inserta->bindParam(':nombresPadre',$nombresPadre);//ok
  $inserta->bindParam(':ocupacionPadre',$ocupacionPadre);//ok
  $inserta->bindParam(':apPaternoMadre',$apPaternoMadre);//ok
  $inserta->bindParam(':apMaternoMadre',$apMaternoMadre);//ok
  $inserta->bindParam(':nombresMadre',$nombresMadre);//ok
  $inserta->bindParam(':ocupacionMadre',$ocupacionMadre);//ok
  $inserta->bindParam(':apPaternoConyu',$apPaternoConyu);//ok
  $inserta->bindParam(':apMaternoConyu',$apMaternoConyu);//ok
  $inserta->bindParam(':nombreConyuge',$nombresConyu);//ok
  $inserta->bindParam(':ocupacionConyu',$ocupacionConyu);//ok
  $inserta->bindParam(':ocupacionConyu',$ocupacionConyu);//ok

  $inserta->bindParam(':sec01Anhio', $sec01Anhio);//ok
  $inserta->bindParam(':sec01Grado', $sec01Grado);//ok
  $inserta->bindParam(':sec01Centro', $sec01Centro);//ok
  $inserta->bindParam(':sec02Anhio', $sec02Anhio);
  $inserta->bindParam(':sec02Grado', $sec02Grado);
  $inserta->bindParam(':sec02Centro', $sec02Centro);
  $inserta->bindParam(':sup01Desde', $sup01Desde);
  $inserta->bindParam(':sup01Hasta', $sup01Hasta);
  $inserta->bindParam(':sup01Carrera', $sup01Carrera);
  $inserta->bindParam(':sup01Centro', $sup01Centro);
  $inserta->bindParam(':sup01Avance', $sup01Avance);
  $inserta->bindParam(':sup01Grado', $sup01Grado);
  $inserta->bindParam(':sup02Desde', $sup02Desde);
  $inserta->bindParam(':sup02Hasta', $sup02Hasta);
  $inserta->bindParam(':sup02Carrera', $sup02Carrera);
  $inserta->bindParam(':sup02Centro', $sup02Centro);
  $inserta->bindParam(':sup02Avance', $sup02Avance);
  $inserta->bindParam(':sup02Grado', $sup02Grado); 
  $inserta->bindParam(':exp01Empresa', $exp01Empresa);//ok
  $inserta->bindParam(':exp01Cargo', $exp01Cargo);
  $inserta->bindParam(':exp01Sueldo', $exp01Sueldo);//ok
  $inserta->bindParam(':exp01Desde', $exp01Desde);//ok
  $inserta->bindParam(':exp01Hasta', $exp01Hasta);//ok
  $inserta->bindParam(':exp01Jefe', $exp01Jefe);
  $inserta->bindParam(':exp01JefePuesto', $exp01JefePuesto);
  $inserta->bindParam(':exp01Telf', $exp01Telf);
  $inserta->bindParam(':exp02Empresa', $exp02Empresa);
  $inserta->bindParam(':exp02Cargo', $exp02Cargo);
  $inserta->bindParam(':exp02Sueldo', $exp02Sueldo); 
  $inserta->bindParam(':exp02Desde', $exp02Desde);
  $inserta->bindParam(':exp02Hasta', $exp02Hasta);
  $inserta->bindParam(':exp02Jefe', $exp02Jefe);
  $inserta->bindParam(':exp02JefePuesto', $exp02JefePuesto);
  $inserta->bindParam(':exp02Telf', $exp02Telf);
  $inserta->bindParam(':exp03Empresa', $exp03Empresa);
  $inserta->bindParam(':exp03Cargo', $exp03Cargo);
  $inserta->bindParam(':exp03Sueldo', $exp03Sueldo);
  $inserta->bindParam(':exp03Desde', $exp03Desde);
  $inserta->bindParam(':exp03Hasta', $exp03Hasta);
  $inserta->bindParam(':exp03Jefe', $exp03Jefe);
  $inserta->bindParam(':exp03JefePuesto', $exp03JefePuesto);
  $inserta->bindParam(':exp03Telf', $exp03Telf);


  $inserta->bindParam(':laboroEnMoy', $laboroEnMoy);
  $inserta->bindParam(':laboroEnMoyPuesto', $laboroEnMoyPuesto);
  $inserta->bindParam(':laboroEnMoySucursal', $laboroEnMoySucursal);
  $inserta->bindParam(':laboroEnMoyMotivoCese', $laboroEnMoyMotivoCese);
  $inserta->bindParam(':laboroEnMoyFchCese', $laboroEnMoyFchCese);
  $inserta->bindParam(':familiarEnMoy', $familiarEnMoy);
  $inserta->bindParam(':familiarEnMoyNombre', $familiarEnMoyNombre);
  $inserta->bindParam(':familiarEnMoyParentesco', $familiarEnMoyParentesco);
  $inserta->bindParam(':emergenciaNombre', $emergenciaNombre);
  $inserta->bindParam(':emergenciaParentesco', $emergenciaParentesco);
  $inserta->bindParam(':emergenciaTelfFijo', $emergenciaTelfFijo);
  $inserta->bindParam(':emergenciaTelfCelular', $emergenciaTelfCelular);
  $inserta->bindParam(':saludGrupoSanguineo', $saludGrupoSanguineo);
  $inserta->bindParam(':saludTieneEnfCronica', $saludTieneEnfCronica);
  $inserta->bindParam(':saludTieneAlergia', $saludTieneAlergia);
  $inserta->bindParam(':saludAlergia', $saludAlergia);
  $inserta->bindParam(':saludEnfCronica', $saludEnfCronica);

  //$inserta->bindParam(':gradoInstruccion',$gradoInstruc);
  $inserta->bindParam(':licenciaCategoria',$categoria);
  $inserta->bindParam(':modoContratacion',$modoContratacion);
  $inserta->bindParam(':bancoNombre',$bancoNombre);
  $inserta->bindParam(':bancoNroCuenta',$bancoNroCuenta);
  $inserta->bindParam(':bancoTipoCuenta',$bancoTipoCuenta);
  $inserta->bindParam(':bancoMoneda',$bancoMoneda);
  $inserta->bindParam(':remunBasica',$remunBasica);
  $inserta->bindParam(':categTrabajador',$categTrab);
  $inserta->bindParam(':ruc',$ruc);
  $inserta->bindParam(':estadoTrabajador',$estadoTrab); //ok
  $inserta->bindParam(':modoSueldo',$modoSueldo);  
  $inserta->bindParam(':asigFamiliar',$asigFamiliar);
  $inserta->bindParam(':cajaChica',$cajaChica);
  $inserta->bindParam(':esMaster',$esMaster);
  $inserta->bindParam(':precioMaster',$precioMaster);
  $inserta->bindParam(':deseaDscto',$deseaDscto);
  $inserta->bindParam(':entidadPension',$entidadPension);
  $inserta->bindParam(':cuspp',$cuspp);   
  $inserta->bindParam(':formaPago',$formaPago);
  //$inserta->bindParam(':tallaCamisa',$camisa);
  $inserta->bindParam(':tallaPolo',$polo);
  $inserta->bindParam(':tallaPantalon',$pantalon);
  $inserta->bindParam(':tallaBotas',$botas);
  $inserta->bindParam(':tallaCasaca',$casaca);
  $inserta->bindParam(':licenciaNro',$nroLicencia);
  $inserta->bindParam(':usuario',$usuario);
  $inserta->bindParam(':deseaDsctoOnp',$deseaDsctoOnp);
  $inserta->bindParam(':asumeEmpresa',$asumeEmpresa);
  $inserta->bindParam(':tipoComision',$tipoComision);
  $inserta->bindParam(':renta5ta',$renta5ta);
  $inserta->bindParam(':diasVacacAnual',$diasVacacAnual);
   
  $inserta->execute();
  $cant = $inserta->rowCount();

  print_r($inserta->errorInfo());


  if ($cant == '1'){
   // echo "Hay que guardar el registro  $nroDocTrab, $nombre, $fchNac, $sexo";
    if (($tipoTrabajador == 'Conductor' || $tipoTrabajador == 'Auxiliar') && $categTrab != 'Tercero' ){
      if ($tipoTrabajador == 'Conductor'){       
        $monto = $mntFondoConductor;
      } else if ($tipoTrabajador == 'Auxiliar' ){
        $monto = $mntFondoAuxiliar;
      }
      $nroPartes = $_POST['cmbCuotasFondo'];
      $mntCuota = round($monto/$nroPartes);
      $descripcion = "Descontar S/.$monto en $nroPartes partes";
      //$fch = Date("Y-m-d");
      $fch = $_POST['txtFchIniDscto'];

      $tipoItem = 'FondoGarantia';
      insertaRegEnPrestamo($db, $nroDocTrab,$descripcion,$monto,$nroPartes,null,$tipoItem,$usuario);
      for ($j = 1; $j <= $nroPartes; $j++ ){
         if ($j == $nroPartes){
           //echo "monto $monto  monto cuota $mntCuota, jota $j  ";
           $mntCuota = round($monto - $mntCuota*($j - 1),2); 
         } 
         $anio = substr($fch,0,4);
         $mes = substr($fch,5,2);
         $dia = substr($fch,8,2);
         //echo "$j, $nroDocTrab, $tipoItem, $descripcion, $fch, $mntCuota, $usuario";               
         $inserta = $db->prepare("INSERT INTO `prestamodetalle` (`monto`, `nroCuota`, `idTrabajador`, `tipoItem`, `descripcion`, `fchPago`, `montoCuota`, `pagado`, `usuario`, `fchCreacion`, `hraCreacion`) VALUES (:monto, :nroCuota, :idTrabajador, :tipoItem, :descripcion, :fchPago, :mntCuota, 'No', :usuario, CURDATE(), CURTIME());");
         $inserta->bindParam(':monto',$monto);
         //$inserta->bindParam(':correlativo',$correlativo);
         //$inserta->bindParam(':mntTotal',$mntTotal);
         $inserta->bindParam(':nroCuota',$j);
         $inserta->bindParam(':idTrabajador',$nroDocTrab);
         $inserta->bindParam(':tipoItem',$tipoItem);
         $inserta->bindParam(':descripcion',$descripcion);
         $inserta->bindParam(':fchPago',$fch);
         $inserta->bindParam(':mntCuota',$mntCuota);
         $inserta->bindParam(':usuario',$usuario);
         $inserta->execute();
         $fch = date("Y-m-d",mktime(0,0,0,$mes,$dia+15,$anio));  
       }    
       $actualiza = $db->prepare("UPDATE `prestamodetalle` SET `descripcion` = replace(`descripcion`,'&#209;','Ń'), `descripcion` = replace(`descripcion`,'&#241;','ń')  WHERE `idTrabajador` = :idTrabajador AND  `fchCreacion` = curdate() AND  `monto` = :monto  AND `tipoItem` = :tipoItem ");
       $actualiza->bindParam(':idTrabajador',$nroDocTrab);
       $actualiza->bindParam(':monto',$monto);
       $actualiza->bindParam(':tipoItem',$tipoItem);
       $actualiza->execute();   
    }

    /*if ($fchCaducidad != ''){
      insertaRegEnVencimiento();
      insertaRegEnTrabajadorLicencias($db,$nroDocTrab,$marcaIni,$marcaFin,$alerta,$plazo,$observacion);
    }*/
    $msg = "<br><b>Proceso terminado.</b> Espere un momento para ser redireccionado";
    $duracion = 5000;


    ?>

    <script type="text/javascript">

    swal({
      type: "success",
      title: "ˇSe registró al nuevo trabajador!",
      showConfirmButton:true,
      confirmButtonText:"Cerrar",
      closeOnConfirm: false

    })

    <!--
    setTimeout("window.opener.location.reload(); window.close();", 4000 );
    //-->
    </script>

    <?php

  } else {
    ?>

    <script type="text/javascript">

    swal({
      type: "error",
      title: "ˇNo se registró al nuevo trabajador!. Revise no existía anteriomente.",
      showConfirmButton:true,
      confirmButtonText:"Cerrar",
      closeOnConfirm: false

    })

    <!--
    setTimeout("window.opener.location.reload(); window.close();", 4000 );
    //-->
  </script>

    <?php
  }

  } ?>





