<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <link rel =stylesheet href="librerias/estilos.css" type ="text/css">
  <link rel =stylesheet href="librerias/DataTables/datatables.min.css" type ="text/css">
  <script language="JavaScript" type="text/javascript" src="librerias/DataTables/datatables.min.js"></script>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <title></title>
  
  <script type="text/javascript">
    function otroTrab(){
      document.forma.action= "index.php?controlador=trabajadores&accion=editatrabajador";
      document.forma.method= 'POST';
      document.forma.submit();
    }

  function validaTrab(forma){
    
    if (forma.txtApPaterno.value == ''){
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
    } else if (forma.cmbModoContratacion.value == ''){
      alert("El Modo de Contratación es obligatorio");
      forma.cmbModoContratacion.focus();
      return false;
    }
    
    if(forma.cmbCategTrab.value == '-'){
      alert("Debe elegir una categoría para el trabajador");
      forma.cmbCategTrab.focus();
      return false;
    } else if(forma.cmbCategTrab.value == '4ta' ){
      rpta = confirm('Para los trabajadores de 4ta se desactivan los valores de\nEsSaludVida,  AFP/SNP, Tipo Comisión, Dscto ONP\ny Pensión asume empresa.\nPresione Aceptar para continuar o Cancelar para modificar.')
      if (!rpta) return false
      ruc = forma.txtRuc.value;
      if (ruc.length != 11){
        alert("Si el trabajador es de 4ta categoria\nel Ruc el obligatorio y debe tener 11 dígitos");
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
    } else if(forma.cmbCategTrab.value == '5ta'){
        if(forma.cmbEntidadPension.value != 'ONP'){
          /*if(forma.cmbDescontarAporteAfp.value == ''){
            alert("Debe especificar si se va a descontar el aporte AFP")
            return false; 
          }*/
          if(forma.cmbDescontarSeguro.value == ''){
            alert("Debe especificar si va a descontar la prima del seguro")
            return false; 
          }

          if(forma.cmbDescontarComisionAfp.value == ''){
            alert("Debe especificar si se va a descontar la comisión AFP")
            return false; 
          }
        }
      if (forma.cmbDiasVacacAnual.value == '-'){
        alert("Debe especificar la duración del periodo vacacional anual")
        return false;  
      }
      //alert(forma.cmbPoliza.value)
      if(forma.cmbPoliza.value == '-'){
        alert("Debe elegir una póliza para el trabajador");
        forma.cmbPoliza.focus();
        return false;
      }
    } else if(forma.cmbCategTrab.value == 'Practicante'){
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

    if(forma.cmbModoSueldo.value == '-'){
      alert("Debe elegir un Modo Sueldo");
      forma.cmbModoSueldo.focus();
      return false;
    } else if (forma.cmbTipoTrabajador.value == '-'){
      alert("Debe elegir el Tipo de Trabajador");
      forma.cmbTipoTrabajador.focus();
      return false;
    }
    
    tipoTrabaj = forma.cmbTipoTrabajador.value
    if (tipoTrabaj == '-'){
      alert("Debe elegir el Tipo de Trabajador");
      forma.cmbTipoTrabajador.focus();
      return false;
    }

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

  $(document).ready(function(){

    $("#txtNvoContrFchIni").datepicker({
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
      dateFormat:"yy-mm-dd",     
    });

    $("#txtNvoContrFchFin").datepicker({
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
      //maxDate: "0M +0D",
      dateFormat:"yy-mm-dd",     
    });

    $("#txtFchNac").datepicker({
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
      maxDate: "0M +0D",
      dateFormat:"yy-mm-dd",     
    });

    $('#txtBuscarTrab').autocomplete({
      minLength: 2,
      source : 'trabajadoresAjax.php'
      //source : ['Maria','Jose','Rocio']  
    });

    $("#txtFchVigencia").datepicker({
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
      minDate: "0M +0D",
      dateFormat:"yy-mm-dd",     
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
    $(".paraPracticante").hide(400)

    var valorCategTrab = $("#cmbCategTrab").val()
      if(valorCategTrab == '4ta'){
        $('#adic01').addClass('rojo');
        $('#adic02').addClass('rojo');
        $(".4ta").show(500)
        $(".5ta").hide(400)
        $("#adic01").html('RUC (*)');
        $(".infoBanco").show(500)
        $(".tipoComision").hide(400)
        $(".paraPracticante").hide(400)

      } else if(valorCategTrab == 'Ef'){
        $(".4ta").show(500)
        $(".5ta").hide(400)
        $('#adic01').removeClass('rojo');
        $('#adic02').removeClass('rojo');
        $("#adic01").html('RUC');
        $(".infoBanco").show(500);
        $(".tipoComision").hide(400);
        $(".paraPracticante").hide(400)
        
      } else if(valorCategTrab == '5ta' ){
        $(".5ta").show(500)
        $(".4ta").hide(400)
        $('#adic01').removeClass('rojo');
        $('#adic02').removeClass('rojo');
        $("#adic01").html('Renta 5ta');
        $(".infoBanco").show(500)
        $(".paraPracticante").hide(400)

        valorEntidadPension = $("#cmbEntidadPension").val();
        if(valorEntidadPension == 'ONP' || valorEntidadPension  == '' ){
          $(".tipoComision").hide(400)
        } else {
          $(".tipoComision").show(500)
        }

      }else if(valorCategTrab == 'Practicante'){
        $('#adic01').addClass('rojo');
        $('#adic02').addClass('rojo');
        $(".4ta").show(500)
        $(".5ta").hide(400)
        $("#adic01").html('RUC (*)');
        $(".infoBanco").show(500)
        $(".tipoComision").hide(400)
        $(".paraPracticante").show(500)
      } else { //Tercero
        $(".4ta").hide(400)
        $(".5ta").hide(400)
        $(".infoBanco").hide(400)
        $('#adic01').removeClass('rojo');
        $('#adic02').removeClass('rojo');
        $("#adic01").html('');
        $(".tipoComision").hide(400)
        $(".paraPracticante").hide(400)
      }

      valorTipoTrab =  $("#cmbTipoTrabajador").val()
      if(valorTipoTrab == 'Conductor'){
        $(".puedeManejar").show(500)
        $('.adicManejar').addClass('rojo');
      } else if(valorTipoTrab == 'Coordinador'){
        $(".puedeManejar").show(500)
        $('.adicManejar').removeClass('rojo');
      } else {
        $(".puedeManejar").hide(400)
        $('.adicManejar').removeClass('rojo');
      }

      valorEntidadPension = $("#cmbEntidadPension").val()
      if(valorEntidadPension == 'ONP' || valorEntidadPension == '' ){
        $(".tipoComision").hide(400)
      } else {
        $(".tipoComision").show(500)
      }
    
      valorTipoTrabajador = $("#cmbTipoTrabajador").val();
      if(valorTipoTrabajador == 'Conductor'){
        $(".puedeManejar").show(500)
        $('.adicManejar').addClass('rojo');
        if ($("#cmbCategTrab").val() == '4ta' || $("#cmbCategTrab").val() == '5ta' || $("#cmbCategTrab").val() == 'Ef'){
          $(".cajaChica").show(500)
        } else {
          $(".cajaChica").hide(400)
        }
      } else if(valorTipoTrabajador == 'Coordinador'){
        $(".puedeManejar").show(500)
        $('.adicManejar').removeClass('rojo');
        $(".cajaChica").hide(400)
      } else {
        $(".puedeManejar").hide(400)
        $('.adicManejar').removeClass('rojo');
        $(".cajaChica").hide(400)
      }

      if ($("#cmbSaludTieneEnfCronica").val() == 'Si'){  
        $(".enfermedadCronica").show(500)

      } else {
        $(".enfermedadCronica").hide(400)
      }

      if ($("#cmbSaludTieneAlergia").val() == 'Si'){  
        $(".alergia").show(500)
      } else {
        $(".alergia").hide(400)
      }


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
        $(".cajaChica").show(500)

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
        $(".paraPracticante").show(500)
        $("#cmbModoContratacion").html("<option>modalidad</option>")
        $("#cmbModoSueldo").html("<option>Fijo</option>")
      } else { //Tercero
        $(".4ta").hide(400)
        $(".5ta").hide(400)
        $(".infoBanco").hide(400)
        $('#adic01').removeClass('rojo');
        $('#adic02').removeClass('rojo');
        $("#adic01").html('');
        $(".tipoComision").hide(400)
        $(".cajaChica").hide(400)
        $(".paraPracticante").hide(400)
        $("#cmbModoContratacion").html("<option>-</option><option>independiente</option><option>intermitente</option><option>modalidad</option><option>tercero</option>")
        $("#cmbModoSueldo").html("<option>-</option><option>Fijo</option><option>Variable</option><option>Preferencial</option>")
      }
    })

    $( "#tabsDocs" ).tabs();

    ///////////USANDO DATATABLE//////////
      var tContrat = $('#dataContratos').DataTable({
        "dom": '<"toolbarC">frtip',
        "scrollCollapse": true,
        "lengthMenu": [ 5 ],
        "oLanguage": {
        "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ entradas",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
        "sZeroRecords": "No hay registros coincidentes",
        },
        "bLengthChange" : false,
        "responsive": true,
        "processing": true,
        "serverSide": true,
        //"ajax": "vistas/ladoServidor/ladoServidor.php?opcion=listarTrabajadores",
        "ajax":{
          "url" :  "vistas/ladoServidor/ladoServidor.php?opcion=listarContratos&idTrabajador="+ '<?php echo $dni; ?>' +"&admin="+'<?php echo $_SESSION['admin']; ?>'+ "&edicion="+'<?php echo $_SESSION['edicion']; ?>',
        /*  "type" : 'POST',
          "data": function (d) {
                    d.opcion = 'listarTrabajadores';                    
                }*/
               },
        "columnDefs": [
          {"width": "25", "targets": [0, 1]},
          {"width": "35", "targets": [2,3,4, 5, 6,7,8, 9]},
          //{"width": "80", "targets": [6]},
          //{"width": "48", "targets": [1]},
          {"targets": [ 1,2 ], "visible": false, "searchable": false},
          //{"className": "dt-body-right", "targets": [4, 7]}
        ]
      });

      tContrat.columns().eq( 0 ).each( function ( colIdx ) {
          $( 'input', tContrat.column( colIdx ).footer() ).on( 'keyup change', function(){
            tContrat
              .column( colIdx )
              .search( this.value )
              .draw();
          });       
      });


      $('#dataContratos_filter').hide()

      // Setup - add a text input to each footer cell
      $('#dataContratos tfoot th').each( function () {
          //alert($(this).index() )
          var title = $('#dataContratos thead th').eq( $(this).index() ).text();
          if (title != 'Acción' ){ 
              $(this).html('<input type="text" placeholder=" ' + title + '" style="width:100%"  />');              
            //  $(this).html('<input type="text" placeholder=" ' + title + '" style="width:100%"  />');                 
          }
      });

      $("div.toolbarC").addClass("fg-toolbar ui-widget-header ui-helper-clearfix ui-corner-tl ui-corner-tr")
      $("div.toolbarC").css({"padding":"5px"});
      $("div.toolbarC").html("<b>Contratos</b><img src = 'imagenes/mas.png'  width='20' height='20' align='right' >");
      $("div.toolbarC").find("img").off().on("click", function (e) {
        //alert("Cliqueado");

        idTrabajador = '<?php echo $dni ?>' 
        //alert(idContrato)
        var datos = new FormData();
        datos.append("idTrabajador", idTrabajador);

        $.ajax({
          url:'./vistas/ajaxVarios_v3.php?opc=buscarSiHayContratoActivo',
          method: "POST",
          data:datos,
          cache:false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(respuesta){
            if(respuesta["cant"] == 0){
              dialogNuevoContrato.dialog( "open" );
            } else {
              swal({
                type: "error",
                title: "ˇYa existe un contrato activo en alta o renovado!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              })

            }
          }
        });
      });


      dialogNuevoContrato = $( "#dialogNuevoContrato" ).dialog({
        autoOpen: false,
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        position: [30,80],
        buttons: {
          "Guardar": addContrato,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function addContrato(){
        regimen = $("#cmbNvoContrRegimen").val()
        fchIni  = $("#txtNvoContrFchIni").val()
        fchFin  = $("#txtNvoContrFchFin").val()
        tipoContrato = $("#cmbNvoContrTipo").val()
        
        if ( regimen == "-"  ){
          alert("Elija un régimen")
        } else if (tipoContrato == '-'){
          alert("Debe indicar el tipo de contrato")
        } else if ( fchIni == ""  ){
          alert("Elija una fecha de inicio")
        } else if ( tipoContrato != 'Indeterminado' && fchFin == ""  ){
          alert("Este tipo de contrato requiere una fecha fin")
        } else if (fchFin != '' && fchIni >=  fchFin ){
          alert("Verifique las fechas de Inicio y Fin")
        } else {
          var formData = new FormData($("#formNuevoContrato")[0]);
          $.ajax({
            data:  formData,
            url:   './vistas/ajaxVarios_v3.php?opc=insertaNvoContrato',
            timeout: 15000,
            method: "POST",
            processData: false, 
            contentType: false,
            success:  function (response) {
              if (response == 1){
                $("#dialogNuevoContrato").dialog( "close" );
                swal({
                  type: "success",
                  title: "ˇEl contrato ha sido guardado correctamente!",
                  showConfirmButton:true,
                  confirmButtonText:"Cerrar",
                  closeOnConfirm: false
                }).then((result)=>{
                  if(result.value){
                    tContrat.draw()
                  }
                });              
              } else {
                if (response == 0){
                  var mensaje = "Falta la imagen"
                } else if (response == -2){
                  var mensaje = "Ocurrió un error al mover el archivo"
                } else if (response == -3){
                  var mensaje = "Este archivo existe"
                } else if (response == -4){
                  var mensaje = "Archivo no permitido. Es un tipo de archivo prohibido o excede el tamańo de 500 Kilobytes"          
                }
                swal({
                  type: "error",
                  title: mensaje,
                  showConfirmButton:true,
                  confirmButtonText:"Cerrar",
                  closeOnConfirm: false
                });
              }
            }
          })
        }
      }

      $("#imgContrato").on('change',function(){
        var fileName = this.files[0].name;
        var fileSize = this.files[0].size;
        if(fileSize > 1000000){
          alert('El archivo no debe superar los 1MB');
          this.value = '';
          this.files[0].name = '';
        }
      })

      ///////////////////
      $('#dataContratos tbody').on( 'click', 'tr .editaContrato', function(){
        idContrato = $(this).attr("idContrato")
        //alert(idContrato)
        var datos = new FormData();
        datos.append("idContrato", idContrato);

        $.ajax({
          url:'./vistas/ajaxVarios_v3.php?opc=buscarContrato',
          method: "POST",
          data:datos,
          cache:false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(respuesta){
            //console.log("respuesta", respuesta);
            $("#editaIdContrato").val(respuesta["idContrato"]);
            $("#txtEditaContrNro").val(respuesta["nroContrato"]);
            $("#txtEditaContrFchIni").val(respuesta["fchInicio"]);
            $("#txtEditaContrFchFin").val(respuesta["fchFin"]);
            $("#txtEditaContrObserv").val(respuesta["observacion"]);            
            $("#cmbEditaContrRegimen").val(respuesta["regimen"]);
            $("#cmbEditaContrTipo").val(respuesta["tipoContrato"]);
            $("#cmbEditaContrModo").val(respuesta["modo"]);
            $("#cmbEditaContrEstado").val(respuesta["estado"]);
            $("#editaIdTrabajador").val(respuesta["idTrabajador"]);
            $("#rutaEditaOriginal").val(respuesta["rutaArchivo"]);
            dialogEditaContrato.dialog( "open" );
          }
        })
      });

      dialogEditaContrato = $( "#dialogEditaContrato" ).dialog({
        autoOpen: false,
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        position: [30,80],
        buttons: {
          "Guardar": editaContrato,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function editaContrato(){
        //nroContrato = $("#txtEditaContrNro").val()
        fchIni  = $("#txtEditaContrFchIni").val()
        fchFin  = $("#txtEditaContrFchFin").val()
        tipoContrato = $("#cmbEditaContrTipo").val()
        
        if ( fchIni == ""  ){
          alert("Elija una fecha de inicio")
        } else if (tipoContrato != 'Indeterminado' && (fchFin == '0000-00-00' || fchFin == ''  )){
          alert("Este tipo de contrato requiere fecha fin")
        } else if (fchFin != '0000-00-00' &&  fchIni >=  fchFin ){
          alert("Verifique las fechas de Inicio y Fin")
        } else {
          var formData = new FormData($("#formEditaContrato")[0]);
          $.ajax({
            data:  formData,
            url:   './vistas/ajaxVarios_v3.php?opc=editaContrato',
            timeout: 15000,
            method: "POST",
            processData: false, 
            contentType: false,
            success:  function (response) {
              if (response == 1){
                $("#dialogEditaContrato").dialog( "close" );
                swal({
                  type: "success",
                  title: "ˇEl contrato ha sido editado correctamente!",
                  showConfirmButton:true,
                  confirmButtonText:"Cerrar",
                  closeOnConfirm: false
                }).then((result)=>{
                  if(result.value){
                    tContrat.draw()
                  }
                });              
              } else {
                if (response == 0){
                  var mensaje = "Falta la imagen"
                } else if (response == -2){
                  var mensaje = "Ocurrió un error al mover el archivo"
                } else if (response == -3){
                  var mensaje = "Este archivo existe"
                } else if (response == -4){
                  var mensaje = "Archivo no permitido. Es un tipo de archivo prohibido o excede el tamańo de 500 Kilobytes"          
                }
                swal({
                  type: "error",
                  title: mensaje,
                  showConfirmButton:true,
                  confirmButtonText:"Cerrar",
                  closeOnConfirm: false
                });
              }
            }
          })
        }
      }

      $("#imgEditaContrato").on('change',function(){
        var fileName = this.files[0].name;
        var fileSize = this.files[0].size;
        if(fileSize > 1000000){
          alert('El archivo no debe superar los 1MB');
          this.value = '';
          this.files[0].name = '';
        }
      })


      $('#dataContratos tbody').on( 'click', 'tr .eliminaContrato', function(){
        idContrato = $(this).attr("idContrato")
        //alert(idContrato)
        var datos = new FormData();
        datos.append("idContrato", idContrato);

        $.ajax({
          url:'./vistas/ajaxVarios_v3.php?opc=buscarContrato',
          method: "POST",
          data:datos,
          cache:false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(respuesta){
            //console.log("respuesta", respuesta);
            $("#eliminaIdContrato").val(respuesta["idContrato"]);
            $("#txtEliminaContrNro").val(respuesta["nroContrato"]);
            $("#txtEliminaContrFchIni").val(respuesta["fchInicio"]);
            $("#rutaEliminaOriginal").val(respuesta["rutaArchivo"]);
            
            dialogEliminaContrato.dialog( "open" );
          }
        })
      });

      dialogEliminaContrato = $( "#dialogEliminaContrato" ).dialog({
        autoOpen: false,
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        position: [30,80],
        buttons: {
          "Eliminar": eliminaContrato,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function eliminaContrato(){
        var formData = new FormData($("#formEliminaContrato")[0]);
        $.ajax({
          data:  formData,
          url:   './vistas/ajaxVarios_v3.php?opc=eliminaContrato',
          timeout: 15000,
          method: "POST",
          processData: false, 
          contentType: false,
          success:  function (response) {
            if (response == 1){
              $("#dialogEliminaContrato").dialog( "close" );
              swal({
                type: "success",
                title: "ˇEl contrato ha sido eliminado correctamente!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                if(result.value){
                  tContrat.draw()
                }
              });              
            } else {
              swal({
                type: "error",
                title: "No se pudo eliminar el contrato",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              });
            }
          }
        })
      }

      ///////////////////

      var t = $('#dataDocums').DataTable({
        "dom": '<"toolbar">frtip',
        "scrollCollapse": true,
        "lengthMenu": [ 5 ],
        "oLanguage": {
        "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ entradas",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
        "sZeroRecords": "No hay registros coincidentes",
        },
        "bLengthChange" : false,
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax":{
          "url" :  "vistas/ladoServidor/ladoServidor.php?opcion=listarDocsIdentidad&idTrabajador="+ '<?php echo $dni; ?>' +"&admin="+'<?php echo $_SESSION['admin']; ?>'+ "&edicion="+'<?php echo $_SESSION['edicion']; ?>',
               },
        "columnDefs": [
          {"width": "25", "targets": [0]},
          {"width": "35", "targets": [1,2,3]},
          {"width": "80", "targets": [4]},
          {"targets": [ -2 ], "visible": false, "searchable": false},
          //{"className": "dt-body-right", "targets": [4, 7]}
        ]
      });

      t.columns().eq( 0 ).each( function ( colIdx ) {

         if (colIdx == 4){
          $( 'select', t.column( colIdx ).footer() ).on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            this.value
                        );
                        t
                          .column(colIdx)
                          .search( val, true, false )
                          .draw();
                    });
        } else {
          $( 'input', t.column( colIdx ).footer() ).on( 'keyup change', function(){
            t
              .column( colIdx )
              .search( this.value )
              .draw();
          });
        }
      });

      $('#dataDocums_filter').hide()

      // Setup - add a text input to each footer cell
      $('#dataDocums tfoot th').each( function () {
          //alert($(this).index() )
          var title = $('#dataDocums thead th').eq( $(this).index() ).text();
          if (title != 'Acción' ){
            if($(this).index() == 4){
              $(this).html('<select><option></option><option>Predeterminado</option><<option>Activo</option><option>Inactivo</option>'); 
            } else {  
              $(this).html('<input type="text" placeholder=" ' + title + '" style="width:100%"  />');              
            }                
          }
      } );

      $("div.toolbar").addClass("fg-toolbar ui-widget-header ui-helper-clearfix ui-corner-tl ui-corner-tr")
      $("div.toolbar").css({"padding":"5px"});

      $("div.toolbar").html("<b>Documentos de Identidad</b><img src = 'imagenes/mas.png'  width='20' height='20' align='right' >");
    

      $("div.toolbar").find("img").off().on("click", function (e) {
        dialogNuevoDocumIdentidad.dialog( "open" );
      });

      dialogNuevoDocumIdentidad = $( "#dialogNuevoDocumIdentidad" ).dialog({
        autoOpen: false,
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        position: [30,80],
        buttons: {
          "Guardar": addDocumIdentidad,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function addDocumIdentidad(){
        tipoDocIdentidad = $("#cmbTipoDocIdentidad").val()
        docIdentidad = $("#txtNvoDocIdentidad").val()
        fchIniDocIdentidad = $("#txtFchIniDocIdentidad").val()
        fchFinDocIdentidad = $("#txtFchFinDocIdentidad").val()
        estadoDocIdentidad = $("#cmbEstadoDocIdentidad").val()
        
        if(tipoDocIdentidad == "-"){
          alert("Debe ingresar el tipo de documento")
        } else if (((tipoDocIdentidad == "DNI" || tipoDocIdentidad == "PAS" ) && docIdentidad.length != 8) || docIdentidad.length < 5  ){
          alert("El ancho del nro. de documento de Identidad no es compatible con el tipo de documento ")
        } else if ( fchIniDocIdentidad.length != 10  ){
          alert("Verifique la fecha de inicio")

        } else if ( fchFinDocIdentidad.length != 10  ){
          alert("Verifique la fecha de fin")

        } else if ( fchIniDocIdentidad >=  fchFinDocIdentidad ){
          alert("Verifique las fechas de Inicio y Fin")

        } else if(estadoDocIdentidad == "-"){
          alert("Debe ingresar el estado del documento")
        } else {
          var formData = new FormData($("#formNuevo")[0]);
          $.ajax({
            data:  formData,
            url:   './vistas/ajaxVarios_v2.php?opc=insertaDocIdentidad',
            timeout: 15000,
          //  type:  'post',
            method: "POST",
            //data:datos,
            processData: false, 
            contentType: false,
            success:  function (response) {
              if (response == 1){
                $("#dialogNuevoDocumIdentidad").dialog( "close" );
                swal({
                  type: "success",
                  title: "ˇEl documento de identidad ha sido guardado correctamente!",
                  showConfirmButton:true,
                  confirmButtonText:"Cerrar",
                  closeOnConfirm: false
                }).then((result)=>{
                  if(result.value){
                    //t.draw()
                    window.location = "index.php?controlador=trabajadores&accion=editatrabajador&dni=" + "<?php echo $dni;  ?>"
                  }
                });              
              } else {
                if (response == 0){
                  var mensaje = "Falta la imagen"
                } else if (response == -2){
                  var mensaje = "Ocurrió un error al mover el archivo"
                } else if (response == -3){
                  var mensaje = "Este archivo existe"
                } else if (response == -4){
                  var mensaje = "Archivo no permitido. Es un tipo de archivo prohibido o excede el tamańo de 500 Kilobytes"          
                }
                swal({
                  type: "error",
                  title: mensaje,
                  showConfirmButton:true,
                  confirmButtonText:"Cerrar",
                  closeOnConfirm: false
                });
              }
            }
          })
        }
      }

      $( "#txtFchIniDocIdentidad" ).datepicker({
                        changeMonth: true,
                        changeYear: true,
                        dateFormat: 'yy-mm-dd',maxDate: "+0D",
                        onClose: function( selectedDate ) {
                                    $( "#txtFchFinDocIdentidad" ).datepicker( "option", "minDate", selectedDate );
                                 }
                        });
      $( "#txtFchFinDocIdentidad" ).datepicker({
                        changeMonth: true,
                        changeYear: true,
                        dateFormat: 'yy-mm-dd',
                        onClose: function( selectedDate ) {
                                    $( "#txtFchIniDocIdentidad" ).datepicker( "option", "maxDate", selectedDate );
                                  }
                        });


      $('#dataDocums tbody').on( 'click', 'tr .editaDocIdentidad', function(){
        tipoDoc = $(this).attr("tipoDoc")
        nroDoc = $(this).attr("nroDoc")
        fchIni = $(this).attr("fchIni")

        var datos = new FormData();
        datos.append("tipoDoc", tipoDoc);
        datos.append("nroDoc", nroDoc);
        datos.append("fchIni", fchIni);

        $.ajax({
          url:'./vistas/ajaxVarios_v2.php?opc=buscarDocIdentidad',
          method: "POST",
          data:datos,
          cache:false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(respuesta){
            //console.log("respuesta", respuesta);
            $("#txtEditaTipoDocIdentidad").val(respuesta["tipoDocTrab"]);
            $("#txtEditaDocIdentidad").val(respuesta["nroDocTrab"]);
            $("#fchIniEditaDocIdentidad").val(respuesta["fchIni"]);
            $("#rutaEditaOriginal").val(respuesta["adjunto"]);            
            $('#cmbEditaEstadoDocIdentidad').append('<option>Activo</option>');
            $('#cmbEditaEstadoDocIdentidad').children('option:not(:first)').remove();
            <?php if($verificarPredeterminado == "No"){ ?>
              $('#cmbEditaEstadoDocIdentidad').append('<option>Predeterminado</option>');
            <?php } ?>

            $('#cmbEditaEstadoDocIdentidad').append('<option>Inactivo</option>');
            if(respuesta["estado"] == "Predeterminado")
              $('#cmbEditaEstadoDocIdentidad').append('<option>Predeterminado</option>');
            $("#cmbEditaEstadoDocIdentidad").val(respuesta["estado"]);
            $("#txtEditaObserv").val(respuesta["observacion"]);     
            dialogEditaDocumIdentidad.dialog( "open" );
          }
        })
      });


      dialogEditaDocumIdentidad = $( "#dialogEditaDocumIdentidad" ).dialog({
        autoOpen: false,
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        position: [30,80],
        buttons: {
          "Guardar cambios": editaDocumIdentidad,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function editaDocumIdentidad(){
        var formData = new FormData($("#formEdita")[0]);
        $.ajax({
          data:  formData,
          url:   './vistas/ajaxVarios_v2.php?opc=editaDocIdentidad',
          timeout: 15000,
          method: "POST",
          //data:datos,
          processData: false, 
          contentType: false,
          success:  function (response) {
            console.log("response", response)
            if (response == 1){
              $("#dialogEditaDocumIdentidad").dialog( "close" );
              swal({
                type: "success",
                title: "ˇEl documento de identidad ha sido editado correctamente!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                if(result.value){
                  //t.draw()
                  window.location = "index.php?controlador=trabajadores&accion=editatrabajador&dni=" + "<?php echo $dni;  ?>"
                }
              });              
            } else {
              if (response == -2){
                var mensaje = "Ocurrió un error al mover el archivo"
              } else if (response == -3){
                var mensaje = "Este archivo existe"
              } else if (response == -4){
                var mensaje = "Archivo no permitido. Es un tipo de archivo prohibido o excede el tamańo de 500 Kilobytes"          
              } else {
                var mensaje = "Error. Se ha presentado un error inesperado. Vuelva a intentarlo."
              }
              swal({
                type: "error",
                title: mensaje,
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              });
            }
          }
        })
      }


      ////////
      $('#dataDocums tbody').on( 'click', 'tr .eliminaDocIdentidad', function(){
        tipoDoc = $(this).attr("tipoDoc")
        nroDoc = $(this).attr("nroDoc")
        fchIni = $(this).attr("fchIni")

        var datos = new FormData();
        datos.append("tipoDoc", tipoDoc);
        datos.append("nroDoc", nroDoc);
        datos.append("fchIni", fchIni);

        $.ajax({
          url:'./vistas/ajaxVarios_v2.php?opc=buscarDocIdentidad',
          method: "POST",
          data:datos,
          cache:false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(respuesta){
            //console.log("respuesta", respuesta);
            $("#txtElimTipoDocIdentidad").val(respuesta["tipoDocTrab"]);
            $("#txtElimDocIdentidad").val(respuesta["nroDocTrab"]);      
            $("#txtElimFchIniDocIdentidad").val(respuesta["fchIni"]);
            $("#adjuntoElim").val(respuesta["adjunto"]);          
            dialogElimDocumIdentidad.dialog( "open" );
          }
        })
      });


      dialogElimDocumIdentidad = $( "#dialogElimDocumIdentidad" ).dialog({
        autoOpen: false,
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        position: [30,80],
        buttons: {
          "Eliminar Doc. Identidad": elimDocumIdentidad,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function elimDocumIdentidad(){
        var formData = new FormData($("#formElimina")[0]);
        $.ajax({
          data:  formData,
          url:   './vistas/ajaxVarios_v2.php?opc=elimDocIdentidad',
          timeout: 15000,
          method: "POST",
          //data:datos,
          processData: false, 
          contentType: false,
          success:  function (response) {
            console.log("response", response)
            if (response == 1){
              $("#dialogElimDocumIdentidad").dialog( "close" );
              swal({
                type: "success",
                title: "ˇEl documento de identidad ha sido eliminado correctamente!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                if(result.value){
                  //t.draw()
                  window.location = "index.php?controlador=trabajadores&accion=editatrabajador&dni=" + "<?php echo $dni;  ?>"
                }
              });              
            } else {
              if (response == -2){
                var mensaje = "Ocurrió un error al mover el archivo"
              } else if (response == -3){
                var mensaje = "Este archivo existe"
              } else if (response == -4){
                var mensaje = "Archivo no permitido. Es un tipo de archivo prohibido o excede el tamańo de 500 Kilobytes"          
              } else {
                var mensaje = "Error. Se ha presentado un error inesperado. Vuelva a intentarlo."
              }
              swal({
                type: "error",
                title: mensaje,
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              });
            }
          }
        })
      }
    ///////////////////

      $("#nuevaFoto").change(function(){
        var imagen = this.files[0];
        //console.log("foto", imagen["type"] )
        if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png" ){
          $("#nuevaFoto").val("");
          swal({
            type: "error",
            title: "Error al subir a imagen",
            text: "ˇLa imagen debe estar en formato JPG o PNG",
            confirmButtonText:"Cerrar",
          })
        } else if(imagen["size"] > 2000000){
          $("#nuevaFoto").val("");
          swal({
            type: "error",
            title: "Error al subir a imagen",
            text: "ˇLa imagen no debe pesar más de 2Mb",
            confirmButtonText:"Cerrar",
          })
        } else {
          var datosImagen = new FileReader;
          datosImagen.readAsDataURL(imagen);
          $(datosImagen).on("load", function(event){
            var rutaImagen = event.target.result;
            $(".previsualizar").attr("src", rutaImagen);
          })
        }
      })


      $('#lnkFoto').on( 'click', function(){
        //alert("prueba")
        dialogSubirFoto.dialog( "open" );
      });


    dialogSubirFoto = $( "#dialogSubirFoto" ).dialog({
      autoOpen: false,
      resizable: false,
      height: "auto",
      width: 400,
      modal: true,
      position: [30,80],
      buttons: {
        "Guardar": subirFoto,
        Cerrar: function() {
          $( this ).dialog( "close" );
        }
      }
    });

    function subirFoto(){
      if(nuevaFoto == ""){
        alert("Debe elegir una imagen a subir")
      } else {
        var formData = new FormData($("#formSubirFoto")[0]);
        $.ajax({
          data:  formData,
          url:   './vistas/ajaxVarios_v2.php?opc=subirFotoServidor',
          timeout: 15000,
          method: "POST",
          processData: false, 
          contentType: false,
          success:  function (response) {
            if (response == 1){
              swal({
                  type: "success",
                  title: "La foto subió correctamente!",
                  showConfirmButton:true,
                  confirmButtonText:"Cerrar",
                  closeOnConfirm: false
                }).then((result)=>{
                  if(result.value){
                    //t.draw()
                    window.location = "index.php?controlador=trabajadores&accion=editatrabajador&dni=" + "<?php echo $dni;  ?>"
                  }
                });
            } else {
              swal({
                type: "error",
                title: "Error inesperado al subir la imagen",
                text: "ˇRevise su procedimiento",
                confirmButtonText:"Cerrar",
              })
            }
          }
        })
      }
    }

    ////////////////

  });
  
 </script> 

  </head>
  <body>

    <div id="dialogNuevoContrato" title="Nuevo Contrato">
      <div style="padding: 5px;">
        <form name = "formNuevoContrato" id = "formNuevoContrato" method="POST" enctype="multipart/form-data" >
          <fieldset>
            <table width="350" border = '0'>
              <tr>
                <td width = "80">Nro. Contrato</td>
                <td>
                  <input type="text" name="txtNvoContrNro" id="txtNvoContrNro"  size="20" >
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Regimen</td>
                <td>
                  <select id = "cmbNvoContrRegimen" name = "cmbNvoContrRegimen" style="padding: 3px; width: 160px;" required>
                    <option value = "-">-</option>
                    <option>Mype</option>
                    <option>General</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Tipo contrato</td>
                <td>
                  <select id = "cmbNvoContrTipo" name = "cmbNvoContrTipo" style="padding: 3px; width: 160px;" required>
                    <option value = "-">-</option>
                    <option>Indeterminado</option>
                    <option>Intermitente</option>
                    <option>Contrato por modalidad</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Fch Inicio</td>
                <td>
                  <input type="text" name="txtNvoContrFchIni" id="txtNvoContrFchIni" size="20">
                </td>
              </tr>
              <tr>
                <td>Fch Fin</td>
                <td>
                  <input type="text" name="txtNvoContrFchFin" id="txtNvoContrFchFin" size="20">
                </td>
              </tr>
              <tr>
                <td>Observación</td>
                <td rowspan = '2'>
                  <textarea id = 'txtNvoContrObserv' name = 'txtNvoContrObserv' cols = '31' rows = '3' ></textarea>
                </td>
              </tr>
              <tr>
                <td></td>
              </tr>
              <tr>
                <td>Docum Escaneado - max. 1Mb (*)</td>
                <td><input type="file" name="imgContrato" id="imgContrato" /></td>
              </tr>        
            </table>

            <input type='hidden' name= 'nvoContrIdTrabaj' id= 'nvoContrIdTrabaj' value = '<?php echo $dni;   ?>' >      
          </fieldset>
        </form>
      </div>
    </div>

    <div id="dialogEditaContrato" title="Edita Contrato">
      <div style="padding: 5px;">
        <form name = "formEditaContrato" id = "formEditaContrato" method="POST" enctype="multipart/form-data" >
          <fieldset>
            <table width="350" border = '0'>
              <tr>
                <td width = "80">Nro. Contrato</td>
                <td>
                  <input type="text" name="txtEditaContrNro" id="txtEditaContrNro"  size="20" required>
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Regimen</td>
                <td>
                  <select id = "cmbEditaContrRegimen" name = "cmbEditaContrRegimen" style="padding: 3px; width: 160px;">
                    <option>Mype</option>
                    <option>General</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Tipo contrato</td>
                <td>
                  <select id = "cmbEditaContrTipo" name = "cmbEditaContrTipo" style="padding: 3px; width: 160px;" required>
                    <option value = "-">-</option>
                    <option>Indeterminado</option>
                    <option>Intermitente</option>
                    <option>Contrato por modalidad</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Modo</td>
                <td>
                  <select id = "cmbEditaContrModo" name = "cmbEditaContrModo" style="padding: 3px; width: 160px;">
                    <option>Alta</option>
                    <option>Renovación</option>
                    <option>Baja</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Estado</td>
                <td>
                  <select id = "cmbEditaContrEstado" name = "cmbEditaContrEstado" style="padding: 3px; width: 160px;">
                    <option>Activo</option>
                    <option>Inactivado</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Fch Inicio</td>
                <td>
                  <input type="text" name="txtEditaContrFchIni" id="txtEditaContrFchIni" size="20" readonly>
                </td>
              </tr>
              <tr>
                <td>Fch Fin</td>
                <td>
                  <input type="text" name="txtEditaContrFchFin" id="txtEditaContrFchFin" size="20">
                </td>
              </tr>
              <tr>
                <td>Observación</td>
                <td rowspan = '2'>
                  <textarea id = 'txtEditaContrObserv' name = 'txtEditaContrObserv' cols = '31' rows = '3' ></textarea>
                </td>
              </tr>
              <tr>
                <td></td>
              </tr>
              <tr>
                <td>Docum Escaneado - max. 1 Mb (*)</td>
                <td><input type="file" name="imgEditaContrato" id="imgEditaContrato" /></td>
              </tr>
            </table>      
            <input type='hidden' name= 'editaIdContrato' id= 'editaIdContrato' value = '' >
            <input type='hidden' name= 'editaIdTrabajador' id= 'editaIdTrabajador' value = '' >
            <input type='hidden' name= 'rutaEditaOriginal' id= 'rutaEditaOriginal' value = '' >
          </fieldset>
        </form>
      </div>
    </div>

    <div id="dialogEliminaContrato" title="Elimina Contrato">
      <div style="padding: 5px;">
        <form name = "formEliminaContrato" id = "formEliminaContrato" method="POST" enctype="multipart/form-data" >
          <fieldset>
            <table width="350" border = '0'>
              <tr>
                <td width = "80" class = 'rojo'>Nro. Contrato</td>
                <td>
                  <input type="text" name="txtEliminaContrNro" id="txtEliminaContrNro"  size="20" readonly>
                </td>
              </tr>              
              <tr>
                <td class = 'rojo'>Fch Inicio</td>
                <td>
                  <input type="text" name="txtEliminaContrFchIni" id="txtEliminaContrFchIni" size="20" readonly>
                </td>
              </tr>
                <input type='hidden' name= 'eliminaIdContrato' id= 'eliminaIdContrato' value = '' >
                <input type='hidden' name= 'rutaEliminaOriginal' id= 'rutaEliminaOriginal' value = '' >
            </table>      
          </fieldset>
        </form>
      </div>
    </div>

    <div id="dialogNuevoDocumIdentidad" title="Nuevo Documento de Identidad">
      <div style="padding: 5px;">
        <form name = "formNuevo" id = "formNuevo" method="POST" enctype="multipart/form-data" >
          <fieldset>
            <table width="350" border = '0'>
              <tr>
                <td width = "85" class = 'rojo'>Tipo</td>
                <td>
                  <select id = "cmbTipoDocIdentidad" name = "cmbTipoDocIdentidad">
                    <option value = "-">-</option>
                    <?php
                      foreach ($trabajTipoDoc as $key => $value) {
                        echo "<option value = '".$value["idTipoDoc"]."|".$value["codPlame"]."'>".$value["descripTipoDoc"]."</option>";
                      }
                    ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Nro Documento</td>
                <td>
                  <input type="text" name="txtNvoDocIdentidad" id="txtNvoDocIdentidad"  size="20">
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Fch Inicio</td>
                <td>
                  <input type="text" name="txtFchIniDocIdentidad" id="txtFchIniDocIdentidad"  size="12">
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Fch Fin</td>
                <td>
                  <input type="text" name="txtFchFinDocIdentidad" id="txtFchFinDocIdentidad"  size="12">
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Estado</td>
                <td>
                  <select id = "cmbEstadoDocIdentidad" name = "cmbEstadoDocIdentidad">
                    <option value = "-">-</option>
                    <?php if($verificarPredeterminado == "No")  echo "<option>Predeterminado</option>";  ?>
                    
                    <option>Activo</option>
                    <option>Inactivo</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td>Observación</td>
                <td rowspan = '2'>
                  <textarea id = 'txtObserv' name = 'txtObserv' cols = '35' rows = '1' ></textarea>
                </td>
              </tr>
              <tr>
                <td></td>
              </tr>
              <tr>
                <td  class = 'rojo'>Docum Escaneado - max. 1 Mb (*)</td>
                <td><input type="file" name="imagen" id="imagen" /></td>
              </tr>        
            </table>

            <input type='hidden' name= 'idTrabajador' id= 'idTrabajador' value = '<?php echo $dni;   ?>' >
            <input type='hidden' name= 'auxiliar2' id= 'auxiliar2' value = '' >
      
          </fieldset>
        </form>
      </div>
    </div>

    <div id="dialogEditaDocumIdentidad" title="Edita Documento de Identidad">
      <div style="padding: 5px;">
        <form name = "formEdita" id = "formEdita" method="POST" enctype="multipart/form-data" >
          <fieldset>
              <table width="350" border = '0'>
                <tr>
                  <td width = "85" class = 'rojo'>Tipo</td>
                  <td>
                    <input type="text" name="txtEditaTipoDocIdentidad" id="txtEditaTipoDocIdentidad"  size="20" readonly>
                  </td>
                </tr>
                <tr>
                  <td class = 'rojo'>Nro Documento</td>
                  <td>
                    <input type="text" name="txtEditaDocIdentidad" id="txtEditaDocIdentidad"  size="20" readonly>
                  </td>
                </tr>
                <tr>
                  <td class = 'rojo'>Estado</td>
                  <td>
                    <select id = "cmbEditaEstadoDocIdentidad" name = "cmbEditaEstadoDocIdentidad">
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>Observación</td>
                  <td rowspan = '2'>
                    <textarea id = 'txtEditaObserv' name = 'txtEditaObserv' cols = '35' rows = '1' ></textarea>
                  </td>
                </tr>
                <tr>
                  <td></td>
                </tr>
                <tr>
                  <td  class = 'rojo'>Docum Escaneado - max. 500 Kb (*)</td>
                  <td><input type="file" name="imagenEdita" id="imagenEdita" /></td>
                </tr>        
              </table>
              <input type='hidden' name= 'fchIniEditaDocIdentidad' id= 'fchIniEditaDocIdentidad' value = '' >
              <input type='hidden' name= 'rutaEditaOriginal' id= 'rutaEditaOriginal' value = '' >
              <input type='hidden' name= 'idEditaTrabajador' id= 'idEditaTrabajador' value = '<?php echo $dni;   ?>' >
        
          </fieldset>
        </form>
      </div>
    </div>

    <div id="dialogElimDocumIdentidad" title="Elimina Documento de Identidad">
      <div style="padding: 5px;">
        <form name = "formElimina" id = "formElimina" method="POST" enctype="multipart/form-data" >
          <fieldset>
            <table width="350" border = '0'>
              <tr>
                <td width = "85" class = 'rojo'>Tipo</td>
                <td>
                  <input type="text" name="txtElimTipoDocIdentidad" id="txtElimTipoDocIdentidad"  size="20" readonly>
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Nro Documento</td>
                <td>
                  <input type="text" name="txtElimDocIdentidad" id="txtElimDocIdentidad"  size="20" readonly>
                </td>
              </tr>
                      <tr>
                <td class = 'rojo'>Fch Inicio</td>
                <td>
                  <input type="text" name="txtElimFchIniDocIdentidad" id="txtElimFchIniDocIdentidad"  size="20" readonly>
                </td>
              </tr>
              <tr>
                <td></td>
              </tr>       
            </table>
            <input type='hidden' name= 'rutaElimOriginal' id= 'rutaElimOriginal' value = '' >
            <input type='hidden' name= 'idElimTrabajador' id= 'idElimTrabajador' value = '<?php echo $dni; ?>' >
            <input type='hidden' name= 'adjuntoElim' id= 'adjuntoElim' value = '' >
          </fieldset>
        </form>
      </div>
    </div>


  <div id="dialogSubirFoto" title="Subir Foto">
    <div style="padding: 5px;">
      <form name = "formSubirFoto" id = "formSubirFoto" method="POST" enctype="multipart/form-data" >
        <fieldset>
          <table width="350" border = '0'>
            <tr>
              <td width="90"></td>
              <td>
                <input type = "file" name = "nuevaFoto" id = "nuevaFoto" >
              </td>
            </tr>
            <tr>
              <td colspan="2" >
                <img src="imagenes/data/trabajador/anonymous.png" class = "previsualizar" width = "90px">
              
              </td>
            </tr>
          </table>
          <input type='hidden' name= 'idFotoTrabajador' id= 'idFotoTrabajador' value = '<?php echo $dni; ?>' >
        </fieldset>
      </form>
    </div>
  </div>
        


  <?php  foreach($trabajador as $item) {
    $idTrab =  $item['idTrabajador']; 
    require_once 'barramenu.php';  ?>
    <form method="POST" name='forma'  enctype="multipart/form-data" action="_editaTrabajador.php" OnSubmit= "return validaTrab(this);">
      <div class = 'dosCol700x2150'>
        <table width = '100%' border = '1'>
          <tr>
            <th class = 'pagina' colspan = '4'>
              <img border="0" src="imagenes/moyTrans.png" width="25" height="25" align="right" onclick = "abrirventana('index.php?controlador=aplicacion&accion=verprincipal')">
              <img border="0" src="imagenes/volver.png" width="25" height="25" align="right" onclick = "abrirventana('index.php?controlador=trabajadores&accion=listar') ">
              EDITA TRABAJADOR: <?php echo $idTrab."-".$item['apPaterno']." ".$item['apMaterno']." ".$item['nombres']  ?></th>
          </tr>
          <tr>
            <th height="23" valign="top" colspan="4">
              Cambiar Trabajador <input type="text" name="txtBuscarTrab" id="txtBuscarTrab"  value = "<?php echo $auxTrab;?>" size="60"><img border="0" src="imagenes/refrescar2.png" width="18" height="18" align="right" onclick = "otroTrab()">
            </th>
          </tr>
          <tr>
            <th height="23" valign="top" colspan="4">Datos Personales</th>
          </tr>
          <tr>
            <td height="20" width="120" valign="top">Id Trabajador</td>
            <td height="20"  width="220" valign="top"><?php echo $item['idTrabajador']?></td>
            <td height="20" valign="top" colspan="2">

              Imagen <?php  echo "<a id = 'lnkFoto' name = 'lnkFoto' >Cambiar foto</a>";
               
              if ($item['imgTrabajador'] != ''){ ?>
                &nbsp;&nbsp;&nbsp;
                <img class ="sinTitulo" title ="Imprimir FotoCheck|Genera pdf para imprimir Fotocheck" border="0" src="imagenes/icoFoto.png" width="15" height="15" onclick =  "abrirventanaflotantemediana('vistas/pdfFotoCheck.php?id=<?php echo $item['idTrabajador'] ?>')">

                <?php
              }
               ?>
            </td>
          </tr>
          <tr>
            <td height="20" valign="top">Tipo Documento</td>
            <td valign="top"><?php echo $item['tipoDocTrab']?></td>
            <td valign="top" colspan="2" rowspan = '5'>
              <img src="imagenes/data/trabajador/<?php echo $item['imgTrabajador'];?>" width="110" height="110">
            </td>
          </tr>
          <tr>
            <td height="20" width="100" valign="top"></td>
            <td height="20"  valign="top"></td>
            
          </tr>
          <tr>
            <td height="20" valign="top" class = 'rojo'>Ap Paterno</td>
            <td height="20" valign="top"><input type="text" name="txtApPaterno" value ="<?php echo $item['apPaterno'];?>"  size="20"></td>
            
            
          </tr>
          
          <tr>
            <td height="20" valign="top" class = 'rojo'>Ap Materno</td>
            <td height="20" valign="top"><input type="text" name="txtApMaterno" value = "<?php echo $item['apMaterno'];?>"  size="20"></td>
          </tr>
          <tr>
            <td height="20" valign="top" class = 'rojo'>Nombres</td>
            <td height="20" valign="top"><input type="text" name="txtNombres" value = "<?php echo $item['nombres'];?>" size="20"></td>
          </tr>
          <tr>
            <td height="20" valign="top" class = 'rojo'>Fch Nacimiento</td>
            <td height="20" valign="top"><input type="text" name="txtFchNac" id="txtFchNac"  value = "<?php echo $item['fchNacimiento'];?>" size="20"></td>

            <?php if($_SESSION["admPlanilla"] == 'Si'){   ?>
            <td width="120" valign="top" class = 'rojo'>Descontar seguro</td>
            <td valign="top">
              

            <?php } else {  ?>
              <td colspan = '2'></td>


            <?php }  ?>

            </td>            
          </tr>
          <tr>
            <td height="20" valign="top">Estado Trabajador</td>
            <td height="20" valign="top">
              <select size="1" name="cmbEstadoTrab">
                <option <?php echo $item['estadoTrabajador']=='Activo'?'SELECTED':'';  ?>>Activo</option>
                <option <?php echo $item['estadoTrabajador']=='Inactivo'?'SELECTED':'';  ?>>Inactivo</option>
                <option <?php echo $item['estadoTrabajador']=='Latente'?'SELECTED':'';  ?>>Latente</option>
              </select>
            </td>
            <td height="20" valign="top" colspan="2"></td>
          </tr>
          <tr>
            <td height="20" valign="top">Nro Hijos</td>
            <td height="20"  valign="top"><input type="text" name="cmbNroHijos" value = "<?php echo $item['nroHijos'];?>"  size="20"></td>
            <td class = 'rojo' width="90">Sexo</td>
            <td>
              <select size="1" name="cmbSexo" id="cmbSexo">
              <option>-</option>
              <option <?php if ($item['sexo'] == 'Masculino') echo 'selected' ?> >Masculino</option>
              <option <?php if ($item['sexo'] == 'Femenino') echo 'selected' ?> >Femenino</option>
              </select> 
           </td>
          </tr>
          <tr>
            <td height="20" valign="top">Estado Civil</td>
            <td height="20"  valign="top">
              <select size="1" name="cmbEstadoCivil">
                <option selected value= "<?php echo $item['estadoCivil']; ?>" ><?php echo $item['estadoCivil']; ?> </option>
                <option>-</option>
                <option <?php if ($item['estadoCivil'] == 'Soltero') echo 'selected' ?> >Soltero</option>
                <option <?php if ($item['estadoCivil'] == 'Casado') echo 'selected' ?> >Casado</option>
                <option <?php if ($item['estadoCivil'] == 'Conviviente') echo 'selected' ?> >Conviviente</option>
                </select>
            </td>
            <td height="20" valign="top">Casado Desde</td>
            <td height="20" valign="top"><input type="text" name="txtEstadoCivilDesde"  value = "<?php echo $item['estadoCivilDesde'];?>" size="20" maxlength = '10'></td>
          </tr>
          <tr>
            <td height="113" valign="top" colspan="4" >  
                <table border="1" width="100%" height="20" >
                  <tr>
                    <th class = "principal"  colspan="4" align="center">Hijos
                      <img border="0" src="imagenes/mas.png" width="20" height="20" align="right" onclick =  "abrirventanaflotante('index.php?controlador=trabajadores&accion=nuevohijo&dni=<?php echo $dni ?>') ">
                    </th>
                  </tr>
                  <tr>
                    <th width="140" align="center"><b>Nombre</b></th>
                    <th width="100" align="center"><b>F. Nacim.</b></th>
                    <th align="center"><b>Sexo</b></th>
                    <th width="40" align="center">.</th>
                  </tr>
                  <?php
                    $color = 'celeste';
                    foreach($hijos as $hijo){
                      $color = ($color == 'plomo') ? 'celeste' : 'plomo';
                      echo "<tr>";
                      echo "<td class = '$color' >".$hijo['idNombreHijo']."</td>";
                      echo "<td class = '$color' >".$hijo['fchaNacimiento']."</td>";
                      echo "<td class = '$color' >".$hijo['sexo']."</td>";
                      echo "<td class = '$color' >";
                  ?>
                        <img border="0" src="imagenes/lapiz.jpg" width="15" height="15" onclick =  "abrirventanaflotante('index.php?controlador=trabajadores&accion=editahijo&dni=<?php echo $dni ?>&hijo=<?php echo $hijo['idNombreHijo'] ?>&fnac=<?php echo $hijo['fchaNacimiento'] ?>&sexo=<?php echo $hijo['sexo'] ?> ')"> 
                        <img border="0" src="imagenes/menos.jpg" width="15" height="15" onclick =  "abrirventanaflotante('index.php?controlador=trabajadores&accion=eliminahijo&dni=<?php echo $dni ?>&hijo=<?php echo $hijo['idNombreHijo'] ?> ')"></td>  
                  <?php  echo "</tr>";
                     } ?>   
                </table>
            </td>
          </tr>

          <tr>
            <th height="20" valign="top" colspan="4">Tallas para Uniforme</th>
          </tr>    
          <tr>
            <td height="20" valign="top" class="rojo">Polo</td>
            <td height="20" valign="top"><input type="text" name="txtPolo"  value = "<?php echo $item['tallaPolo'];?>"  size="20"></td>
            <td height="20"  valign="top" class="rojo">Pantalón</td>
            <td height="20"valign="top"><input type="text" name="txtPantalon" value = "<?php echo $item['tallaPantalon'];?>" size="20"></td>
          </tr>
          <tr>
            <td height="20" valign="top" class="rojo">Botas</td>
            <td height="20" valign="top"><input type="text" name="txtBotas" value = "<?php echo $item['tallaBotas'];?>"  size="20"></td>
            <td class="rojo">Casaca</td>
            <td><input type="text" name="txtCasaca" value = "<?php echo $item['tallaCasaca'];?>"  size="20"></td>
          </tr>
          <tr>
            <th height="20"  valign="top" colspan="4">Info. para Pago</th>
          </tr>
          <tr>
            <td  class="rojo"  valign="top">Categ Trabajador</td>
            <td  valign="top">
              <select size="1" name="cmbCategTrab" id="cmbCategTrab">
                <option <?php if ($item['categTrabajador'] == '5ta') echo 'selected' ?>>5ta</option>
                <option <?php if ($item['categTrabajador'] == '4ta') echo 'selected' ?>>4ta</option>
                <option <?php if ($item['categTrabajador'] == 'Ef') echo 'selected' ?>>Ef</option>  
                <option <?php if ($item['categTrabajador'] == 'Tercero') echo 'selected' ?>>Tercero</option>
                <option <?php if ($item['categTrabajador'] == 'Practicante') echo 'selected' ?>>Practicante</option>
              </select>
            </td>
            <td  id = 'adic02' >
              <div class="4ta">RUC</div>
            </td>
            <td height="20" valign="top">
              <div class="4ta"><input type="text" name="txtRuc" size="14" maxlength = '11'  value = "<?php echo $item['ruc'];?>" ></div>
            </td>
          </tr>
          <tr>
            <td class="rojo"  align="top">Modo Sueldo</td>
            <td  valign="top">
              <select size="1" name="cmbModoSueldo" id="cmbModoSueldo">
                <option <?php if ($item['modoSueldo'] == 'Fijo') echo 'selected' ?>>Fijo</option>
                <?php if ( $item['categTrabajador'] != 'Practicante'  ){  ?>
                <option <?php if ($item['modoSueldo'] == 'Variable') echo 'selected' ?>>Variable</option>
                <option <?php if ($item['modoSueldo'] == 'Preferencial') echo 'selected' ?>>Preferencial</option>
              <?php } ?>
              </select>
            </td>
            <td class="rojo" height="20" valign="top">Modo Contratac.</td>
            <td height="20" valign="top">
              <select size="1" name="cmbModoContratacion" id="cmbModoContratacion" style="position: relative; width: 110" >
                <option value="">-</option>
                <?php foreach($modosContratacion as $modo) { ?>
                  <option <?php echo ($item['modoContratacion'] == $modo['identificador'])?'SELECTED':'';  ?>  >
                    <?php echo $modo['identificador'];?>
                  </option>
                <?php }; ?>
              </select>
            </td>
          </tr>
          <tr>
            <td class="rojo">Tipo Trabajador</td>
            <td height="20" valign="top">
              <select size="1" name="cmbTipoTrabajador" id="cmbTipoTrabajador" style="position: relative; width: 115" >
                <option <?php if ($item['tipoTrabajador'] == 'Conductor') echo 'selected' ?>>Conductor</option>
                <option <?php if ($item['tipoTrabajador'] == 'Auxiliar') echo 'selected' ?>>Auxiliar</option>
                <option <?php if ($item['tipoTrabajador'] == 'Coordinador') echo 'selected' ?>>Coordinador</option>
                <option <?php if ($item['tipoTrabajador'] == 'Administrativo') echo 'selected' ?>>Administrativo</option>
                <option <?php if ($item['tipoTrabajador'] == 'Otro') echo 'selected' ?>>Otro</option>
              </select>
            </td>
            <td><div class = 'cajaChica'>Caja Chica</div></td>
            <td>
              <div class = 'cajaChica'>
                <input type="text" name="txtCajaChica" size="14"  value = "<?php echo $item['cajaChica'];?>" >
              </div>
            </td>
          </tr>
          <tr class ='puedeManejar'>
            <td class ='adicManejar'>Categ. Licencia</td>
            <td>
              <select size="1" name="txtCategoria" id="txtCategoria"  style="position: relative; width: 120" >
                <option value="-">-</option>
                <?php foreach($categorias as $categoria) { ?>
                <option  <?php if ($item['licenciaCategoria'] == $categoria['idCategoria']) echo 'selected' ?>     value="<?php echo $categoria['idCategoria']; ?>">
                  <?php echo $categoria['idCategoria']." ".$categoria['descripcion'] ;?>
                </option>
              <?php }; ?>
              </select>
            </td>
            <td class ='adicManejar'>Nro. Licencia</td>
            <td><input type="text" name="txtNroLicencia" size="14" value = "<?php echo $item['licenciaNro'];?>" ></td>
          </tr>

          <tr class ='paraPracticante'>
            <td class ='rojo'>Subvención mensual</td>
            <td>
              <input type="text" name="txtSubvencionMensual" size="14" value = "<?php  echo   ($item['categTrabajador'] == 'Practicante') ? $item['remuneracionBasica'] : '' ;?>" >
            </td>
            <td class =''></td>
            <td></td>
          </tr>

          <tr class = 'infoBanco' >
            <td>Moneda</td>
            <td><input type="text" name="txtMoneda" size="14" value = "<?php echo $item['bancoMoneda'];?>" ></td>
            <td>Forma Pago</td>
            <td><input type="text" name="txtFormaPago" size="14" value = "<?php echo $item['formaPago'];?>" ></td>
          </tr>

          <tr class = 'infoBanco'>
            <td><div class = 'infoBanco'>Banco</div></td>
            <td>
              <div class = 'infoBanco'>
                <select size="1" name="cmbBanco" id="cmbBanco">
                  <?php       
                    echo "<option>-</option>";
                    foreach($arrBancos AS $banco){
                      echo "<option ".(($item['bancoNombre'] == $banco)?'SELECTED':'').">".$banco."</option>";
                    }
                  ?>
                </select>
              </div>
            </td>
            <td><div class = 'infoBanco'>Nro Cuenta</div></td>
            <td><div class = 'infoBanco'><input type="text" name="txtNroCuenta" size="14" value = "<?php echo $item['bancoNroCuenta'];?>" ></div></td>
          </tr>

          <tr  class = 'infoBanco'>
            <td>Tipo Cuenta</td>
            <td><input type="text" name="txtTipoCuenta" size="14" value = "<?php echo $item['bancoTipoCuenta'];?>" ></td>
            <th colspan="2">Datos de Pensiones</th>
          </tr>

          <tr class='5ta'>
            <td>Remun. Básica</td>
            <td>
              <input type="text" name="txtRemunBasica" size="14" value = "<?php  echo   ($item['categTrabajador'] == 'Practicante') ? "" : $item['remuneracionBasica'];?>" >
            </td>
            <td colspan="2">
              Desea Dscto EsSalud Vida
              <select size="1" name="cmbDeseaDscto">
                <option <?php if ($item['deseaDscto'] == 'Si') echo 'selected' ?>>Si</option>
                <option <?php if ($item['deseaDscto'] == 'No') echo 'selected' ?>>No</option>
              </select>
            </td>
          </tr>
          <tr class='5ta'>
            <td>Asig. Familiar</td>
            <td>
              <input type="text" name="txtAsigFamiliar" size="14" value = "<?php echo $item['asignacionFamiliar'];?>" >
            </td>
            <td>AFP / SNP</td>
            <td>
              <select size="1" name="cmbEntidadPension" id="cmbEntidadPension" style="position: relative; width: 80" >
                <option value="">-</option>
                <?php foreach($entidadPensiones as $itemPens) { ?>
                  <option value="<?php echo $itemPens['nombre'] ; ?>"  <?php if ($item['entidadPension'] == $itemPens['nombre']) echo 'selected' ?>          >
                  <?php echo $itemPens['nombre'];?>
                  </option>
                <?php }; ?>
              </select>
            </td>
          </tr>
          <tr class='5ta'>
            <td>
              Vacaciones Anual
            </td> 
            <td>
              <select name = 'cmbDiasVacacAnual' id = 'cmbDiasVacacAnual'>
                <option>-</option>
                <option  <?php if ($item['diasVacacAnual'] == '15') echo 'selected' ?>>15</option>
                <option  <?php if ($item['diasVacacAnual'] == '30') echo 'selected' ?>>30</option>
              </select>
              días         
            </td>
            <td><div class="tipoComision">CUSPP</div></td>
            <td><div class="tipoComision"><input type="text" name="txtCuspp" size="14"  value = "<?php echo $item['cuspp'];?>" ></div></td>
          </tr>
          <tr  class='5ta'>
            <td id = 'adic02' >Renta 5ta</td>
            <td>
              <input type="text" name="txtRta5ta" size="14"   value = "<?php echo $item['renta5ta'];?>" >
            </td>
            <td><div class="tipoComision">Tipo Comisión</div></td>
            <td>
              <div class="tipoComision">
                <select size="1" name="cmbTipoComision" id="cmbTipoComision">
                  <option <?php if ($item['tipoComision'] == 'Flujo') echo 'selected' ?>>Flujo</option>
                  <option <?php if ($item['tipoComision'] == 'Mixto') echo 'selected' ?>>Mixto</option>
                </select>
              </div>
            </td>
          </tr>
          <tr class='5ta'>
            <td>Seguro Vida Ley</td>
            <td>
              <select id = "cmbPoliza" name = "cmbPoliza">
                <option value = "-">-</option>
                <?php
                  foreach ($polizas as $key => $value) {
                    echo "<option  ".( $item["idPoliza"] == $value["idPoliza"] ? "SELECTED": "" )." value = '".$value["idPoliza"]."'>".$value["nombPoliza"]."->". $value["valorPoliza"]  ."</option>";
                  }
                ?>
              </select>

            </td>
            <td>
              <div class="tipoComision">
                Desc. prima de Seguro
              </div>
            </td>
            <td>
              <div class="tipoComision">
                <select name = 'cmbDescontarSeguro' id = 'cmbDescontarSeguro'>
                  <option <?php echo $item['descontarSeguro']==''?'SELECTED':'';  ?> ></option>
                  <option <?php echo $item['descontarSeguro']=='Si'?'SELECTED':'';?> >Si</option>
                  <option <?php echo $item['descontarSeguro']=='No'?'SELECTED':'' ?> >No</option>
                </select>
              </div>
            </td>
          </tr>
          <tr class='5ta'>
            <td>Movilidad Mensual</td>
            <td>
              <input type="text" name="txtMovilMensual" size="14"   value = "<?php echo $item['movilidadAsignadaMes'];?>" >              
            </td>
            <td>
              <div class="tipoComision">
                Descontar Aporte
              </div>  
            </td>
            <td>
              <div class="tipoComision">
                <select name = 'cmbDescontarAporteAfp' id = 'cmbDescontarAporteAfp'>
                  <option <?php echo $item['descontarAporteAfp']==''?'SELECTED':'';  ?> ></option>
                  <option <?php echo $item['descontarAporteAfp']=='Si'?'SELECTED':'';?> >Si</option>
                  <option <?php echo $item['descontarAporteAfp']=='No'?'SELECTED':'';?> >No</option>
                </select>
              </div>
            </td>
          </tr>

          <tr class='5ta'>
            <td>Bono de Productividad</td>
            <td><input type="text" name="txtBonoProducMes" size="14" value = "<?php echo $item['bonoProducMes'];?>" >
            <td>
              <div class="tipoComision">Desc. Comisión AFP</div>
            </td>  
            <td>
              <div class="tipoComision">
                <select name = 'cmbDescontarComisionAfp' id = 'cmbDescontarComisionAfp'>
                  <option <?php echo $item['descontarComisionAfp']==''?'SELECTED':'';  ?> ></option>
                  <option <?php echo $item['descontarComisionAfp']=='Si'?'SELECTED':'';?> >Si</option>
                  <option <?php echo $item['descontarComisionAfp']=='No'?'SELECTED':'';?> >No</option>                
                </select>
              </div>
            </td>
          </tr>

          <tr>
            <th  valign="top" colspan="4">Info Master</th>
          </tr>
          <tr>
            <td  valign="top">Es Master</td>
            <td  valign="top">
              <select size="1" name="cmbEsMaster">
                <option selected><?php echo $item['esMaster'];?></option>
                <option>Si</option>
                <option>No</option>
              </select></td>
            <td  valign="top">Precio Master</td>
            <td  valign="top">
            <input type="text" name="txtPrecioMaster"  value = "<?php echo $item['precioMaster'];?>"  size="20"></td>
          </tr>
          <tr>
            <th  valign="top" colspan="4">Datos Familiares</th>
          </tr>
          <tr>
            <th colspan = '4'>Padre</th>
          </tr>
          <tr>
            <td >Ap. Paterno</td>
            <td><input type="text" name="txtApPaternoPadre" size="14" value = "<?php echo $item['apPaternoPadre'];?>"  ></td>
            <td >Ap. Materno</td>
            <td><input type="text" name="txtApMaternoPadre" size="14" value = "<?php echo $item['apMaternoPadre'];?>"  ></td>
          </tr>
          <tr>
            <td >Nombres</td>
            <td><input type="text" name="txtNombresPadre" size="14" value = "<?php echo $item['nombresPadre'];?>"  ></td>
            <td >Ocupación</td>
            <td><input type="text" name="txtOcupacionPadre" size="14" value = "<?php echo $item['ocupacionPadre'];?>"  ></td>
          </tr>
          <tr>
            <th colspan = '4'>Madre</th>
          </tr>
          <tr>
            <td >Ap. Paterno</td>
            <td><input type="text" name="txtApPaternoMadre" size="14" value = "<?php echo $item['apPaternoMadre'];?>"  ></td>
            <td >Ap. Materno</td>
            <td><input type="text" name="txtApMaternoMadre" size="14" value = "<?php echo $item['apMaternoMadre'];?>"  ></td>
          </tr>
          <tr>
            <td >Nombres</td>
            <td><input type="text" name="txtNombresMadre" size="14" value = "<?php echo $item['nombresMadre'];?>"  ></td>
            <td >Ocupación</td>
            <td><input type="text" name="txtOcupacionMadre" size="14" value = "<?php echo $item['ocupacionMadre'];?>"  ></td>
          </tr>

          <tr>
            <th colspan = '4'>Conyugue o Conviviente</th>
          </tr>
          <tr>
            <td >Ap. Paterno</td>
            <td><input type="text" name="txtApPaternoConyu" size="14" value = "<?php echo $item['apPaternoConyu'];?>"  ></td>
            <td >Ap. Materno</td>
            <td><input type="text" name="txtApMaternoConyu" size="14" value = "<?php echo $item['apMaternoConyu'];?>"  ></td>
          </tr>
          <tr>
            <td >Nombres</td>
            <td><input type="text" name="txtNombresConyu" size="14" value = "<?php echo $item['nombreConyuge'];?>"  ></td>
            <td >Ocupación</td>
            <td><input type="text" name="txtOcupacionConyu" size="14" value = "<?php echo $item['ocupacionConyu'];?>"  ></td>
          </tr>


          <tr>
            <th height="23"  valign="top" colspan="4">Datos de Ubicación</th>
          </tr>
          <tr>
            <td height="26" valign="top">Av. Jr. Calle</td>
            <td height="26" valign="top" colspan = '2'><input type="text" name="txtDirCalle" value = "<?php echo $item['dirCalleyNro'];?>"  size="40" maxlength = '200'></td>
            <td>Nro / Interior <input type="text" name="txtDirNro" size="5" value = "<?php echo $item['dirNro'];?>" ></td>

          </tr>
          <tr>
            <td height="26" valign="top">Urbaniz.</td>
            <td height="26" valign="top"><input type="text" name="txtDirUrb" value = "<?php echo $item['dirUrb'];?>" size="20" maxlength = '20'></td>
            <td height="26" valign="top">Distrito</td>
            <td height="26" valign="top"><input type="text" name="txtDirDistrito" value = "<?php echo $item['dirDistrito'];?>" size="20" maxlength = '20'></td>
          </tr>
          <tr>
            <td height="26" valign="top">Provincia</td>
            <td height="26" valign="top">
              <input type="text" name="txtDirProvincia" value = "<?php echo $item['dirProvincia'];?>" size="20" maxlength = '50'></td>
            <td height="26" valign="top">Departamento</td>
            <td height="26" valign="top">
              <input type="text" name="txtDirDepartamento" value = "<?php echo $item['dirDepartamento'];?>" size="20" maxlength = '50'></td>    
          </tr>
          <tr>
            <td>Ref. para llegar</td><td colspan="3"><input type="text" name="txtDirReferencia" size="60" value = "<?php echo $item['dirObservacion'];?>" ></td>
          </tr>

          <tr>
            <td height="27" valign="top">eMail </td>
            <td height="27" valign="top" colspan = '3'><input type="text" name="txtEmail" value = "<?php echo $item['eMail'];?>"  size="60" maxlength = '100'></td>
          </tr>

          <tr>
            <td height="26" valign="top">Teléfono Fijo</td>
            <td height="26" valign="top"><input type="text" name="txtTelfFijo" value = "<?php echo $item['dirTelefono'];?>" size="20"></td>
            <td height="11" valign="top">Teléfono Móvil</td>
            <td height="11" valign="top"><input type="text" name="txtTelfMovil"  value = "<?php echo $item['telfMovil'];?>" size="20"></td>
          </tr>
          <tr>
            <td>Telf Adicional</td><td><input type="text" name="txtTelfAdicional" size="20"  value = "<?php echo $item['telfAdicional'];?>" ></td>
            <td></td><td></td>
          </tr>
          <tr>
            <td>Telf. Referencia</td><td><input type="text" name="txtTelfReferencia" size="11"  value = "<?php echo $item['telfReferencia'];?>" ></td>
            <td>Preguntar por</td><td><input type="text" name="txtTelfRefContacto" size="11"  value = "<?php echo $item['telfRefContacto'];?>" ></td>
            
          </tr>
          <tr>
            <td>Parentesco</td><td><input type="text" name="txtTelfRefParentesco" size="11"  value = "<?php echo $item['telfRefParentesco'];?>" ></td>
            <td></td><td></td>
          </tr>

          <tr>
            <th colspan="4">
              Estudios Relizados
            </th>
          </tr>
          <tr>
            <th colspan="4">Secundarios</th>
          </tr>
          <tr>
            <th>Ańo</th><td><input type="text" name="txtSec01Anhio" size="12" value = "<?php echo $item['sec01Anhio'];?>" ></td>
            <td>Grado</td><td><input type="text" name="txtSec01Grado" size="12" value = "<?php echo $item['sec01Grado'];?>" ></td>
          </tr>
          <tr>    
            <td>Centro Estudios</td><td colspan="3"><input type="text" name="txtSec01Centro" size="40" value = "<?php echo $item['sec01Centro'];?>" ></td>
          </tr>
          <tr>
            <th>Ańo</th><td><input type="text" name="txtSec02Anhio" size="12" value = "<?php echo $item['sec02Anhio'];?>" ></td>
            <td>Grado</td><td><input type="text" name="txtSec02Grado" size="12" value = "<?php echo $item['sec02Grado'];?>" ></td>
          </tr>
          <tr>        
            <td>Centro Estudios</td><td colspan="3"><input type="text" name="txtSec02Centro" size="40" value = "<?php echo $item['sec02Centro'];?>" ></td>
          </tr>
          <tr>
            <th colspan="4">Estudios Superiores</th>
          </tr>
          <tr>
            <th>Desde</th><td><input type="text" name="txtSup01Desde" size="12" value = "<?php echo $item['sup01Desde'];?>" ></td>
            <td>Hasta</td><td><input type="text" name="txtSup01Hasta" size="12" value = "<?php echo $item['sup01Hasta'];?>" ></td>    
          </tr>
          <tr>        
            <td>Carrera</td><td><input type="text" name="txtSup01Carrera" size="30" value = "<?php echo $item['sup01Carrera'];?>" ></td>
            <td>Institución</td><td><input type="text" name="txtSup01Centro" size="12" value = "<?php echo $item['sup01Centro'];?>" ></td>
          </tr>
          <tr>
            <td>Avance</td>
            <td>
              <select size="1" name="cmbSup01Avance" id="cmbSup01Avance">
              <option>-</option>
              <option <?php if ($item['sup01Avance'] == 'Completo') echo 'selected' ?> >Completo</option>
              <option <?php if ($item['sup01Avance'] == 'Incompleto') echo 'selected' ?>  >Incompleto</option>
              </select>
            </td>
            <td>Grado</td><td><input type="text" name="txtSup01Grado" size="12" value = "<?php echo $item['sup01Grado'];?>" ></td>
          </tr>
          <tr>
            <th>Desde</th><td><input type="text" name="txtSup02Desde" size="12" value = "<?php echo $item['sup02Desde'];?>" ></td>
            <td>Hasta</td><td><input type="text" name="txtSup02Hasta" size="12" value = "<?php echo $item['sup02Hasta'];?>" ></td>
          </tr>
          <tr>
            <td>Carrera</td><td><input type="text" name="txtSup02Carrera" size="30" value = "<?php echo $item['sup02Carrera'];?>" ></td>
            <td>Institución</td><td><input type="text" name="txtSup02Centro" size="12" value = "<?php echo $item['sup02Centro'];?>" ></td>
          </tr>          
          <tr>
            <td>Avance</td>
            <td>
              <select size="1" name="cmbSup02Avance" id="cmbSup02Avance">
              <option>-</option>
              <option <?php if ($item['sup02Avance'] == 'Completo') echo 'selected' ?> >Completo</option>
              <option <?php if ($item['sup02Avance'] == 'Incompleto') echo 'selected' ?>  >Incompleto</option>
              </select>
            </td>
            <td>Grado</td><td><input type="text" name="txtSup02Grado" size="12" value = "<?php echo $item['sup02Grado'];?>" ></td>
          </tr>
          <tr>
            <th colspan="4">
              Experiencia Laboral (Las 3 últimas)
            </th>
          </tr>
          <tr>
            <th>Nombre Empresa 01</th><td><input type="text" name="txtExp01Empresa" size="12" value = "<?php echo $item['exp01Empresa'];?>" ></td>
            <td>Cargo</td><td><input type="text" name="txtExp01Cargo" size="12" value = "<?php echo $item['exp01Cargo'];?>" ></td>
          </tr>  
          <tr>
            <td>Desde</td><td><input type="text" name="txtExp01Desde" size="12" value = "<?php echo $item['exp01Desde'];?>" ></td>
            <td>Hasta</td><td><input type="text" name="txtExp01Hasta" size="12" value = "<?php echo $item['exp01Hasta'];?>" ></td>
          </tr>
          <tr>
            <td>Sueldo</td><td><input type="text" name="txtExp01Sueldo" size="12" value = "<?php echo $item['exp01Sueldo'];?>" ></td>
            <td>Jefe inmediato</td><td><input type="text" name="txtExp01Jefe" size="12" value = "<?php echo $item['exp01Jefe'];?>" ></td>
          </tr>  
          <tr>
            <td>Jefe Puesto</td><td><input type="text" name="txtExp01JefePuesto" size="12" value = "<?php echo $item['exp01JefePuesto'];?>" ></td>
            <td>Jefe Telf</td><td><input type="text" name="txtExp01Telf" size="12" value = "<?php echo $item['exp01Telf'];?>" ></td>
          </tr>
          <tr>
            <th>Nombre Empresa 02</th><td><input type="text" name="txtExp02Empresa" size="12" value = "<?php echo $item['exp02Empresa'];?>" ></td>
            <td>Cargo</td><td><input type="text" name="txtExp02Cargo" size="12" value = "<?php echo $item['exp02Cargo'];?>" ></td>
          </tr>  
          <tr>
            <td>Desde</td><td><input type="text" name="txtExp02Desde" size="12" value = "<?php echo $item['exp02Desde'];?>" ></td>
            <td>Hasta</td><td><input type="text" name="txtExp02Hasta" size="12" value = "<?php echo $item['exp02Hasta'];?>" ></td>
          </tr>
          <tr>
            <td>Sueldo</td><td><input type="text" name="txtExp02Sueldo" size="12" value = "<?php echo $item['exp02Sueldo'];?>" ></td>
            <td>Jefe inmediato</td><td><input type="text" name="txtExp02Jefe" size="12" value = "<?php echo $item['exp02Jefe'];?>" ></td>
          </tr>  
          <tr>
            <td>Jefe Puesto</td><td><input type="text" name="txtExp02JefePuesto" size="12" value = "<?php echo $item['exp02JefePuesto'];?>" ></td>
            <td>Jefe Telf</td><td><input type="text" name="txtExp02Telf" size="12" value = "<?php echo $item['exp02Telf'];?>" ></td>
          </tr>
          <tr>
            <th>Nombre Empresa 03</th><td><input type="text" name="txtExp03Empresa" size="12" value = "<?php echo $item['exp03Empresa'];?>" ></td>
            <td>Cargo</td><td><input type="text" name="txtExp03Cargo" size="12" value = "<?php echo $item['exp03Cargo'];?>" ></td>
          </tr>  
          <tr>
            <td>Desde</td><td><input type="text" name="txtExp03Desde" size="12" value = "<?php echo $item['exp03Desde'];?>" ></td>
            <td>Hasta</td><td><input type="text" name="txtExp03Hasta" size="12" value = "<?php echo $item['exp03Hasta'];?>" ></td>
          </tr>
          <tr>
            <td>Sueldo</td><td><input type="text" name="txtExp03Sueldo" size="12" value = "<?php echo $item['exp03Sueldo'];?>" ></td>
            <td>Jefe inmediato</td><td><input type="text" name="txtExp03Jefe" size="12" value = "<?php echo $item['exp03Jefe'];?>" ></td>
          </tr>  
          <tr>
            <td>Jefe Puesto</td><td><input type="text" name="txtExp03JefePuesto" size="12" value = "<?php echo $item['exp03JefePuesto'];?>" ></td>
            <td>Jefe Telf</td><td><input type="text" name="txtExp03Telf" size="12" value = "<?php echo $item['exp03Telf'];?>" ></td>
          </tr>

        </table> 
      </div>

      
      <div class = 'dosCol700x2150'>
        <table width = '100%' border = '0'>
          <tr>
            <th class = 'pagina' colspan = '4'>
              <img border="0" src="imagenes/moyTrans.png" width="25" height="25" align="right" onclick = "abrirventana('index.php?controlador=aplicacion&accion=verprincipal')">
              <img border="0" src="imagenes/volver.png" width="25" height="25" align="right" onclick = "abrirventana('index.php?controlador=trabajadores&accion=listar') ">
              EDITA TRABAJADOR (cont.)</th>
          </tr>
          <tr>
            <td height="210" valign="top" colspan="4" >    
              <div class = "varios3">
              <table border="1" width="100%" height="50" >
                <tr>
                  <th class = "principal"  colspan="8" align="center">Entrega Uniformes
                    <img border="0" src="imagenes/mas.png" width="20" height="20" align="right" onclick =  "abrirventanaflotantesupergrande('index.php?controlador=trabajadores&accion=entregauniformes&dni=<?php echo $dni ?>') ">
                  </th>
                </tr>
                <tr>
                  <th width="80" align="center"><b>Id</b></th>
                  <th width="220" align="center"><b>Prenda</b></th>
                  <th width="60" align="center"><b>F.Entrega</b></th>
                  <th width="45" align="center"><b>Precio</b></th>
                  <th width="40" align="center"><b>Tipo</b></th>
                  <th width="70" align="center"><b>Creador</b></th>
                  <th width="60" align="center"><b>F.Creacion</b></th>
                  <th align="center">Acción</th>
                </tr>
                <?php
                  $color = 'celeste';   
                  foreach($prendas as $prenda) {
                    $color = ($color == 'plomo') ? 'celeste' : 'plomo';
                    echo "<tr>";
                    echo "<td class = '$color' >".$prenda['idMov']."-".$prenda['correlativo']."</td>";
                    echo "<td class = '$color' >".$prenda['descripcion']."</td>";
                    echo "<td class = '$color' >".$prenda['fchRealiz']."</td>";
                    echo "<td class = '$color' >".number_format($prenda['precUnit'],2)."</td>";
                    echo "<td class = '$color' aling = 'right' >".$prenda['tipoEntrega']."</td>";
                    echo "<td class = '$color' >".$prenda['creacUsuario']."</td>";
                    echo "<td class = '$color' >".substr($prenda['creacFch'],0,10)."</td>";
                    echo "<td class = '$color' >";
                ?>
                <img border="0" src="imagenes/menos.jpg" width="12" height="12" onclick =  "abrirventanaflotante('index.php?controlador=trabajadores&accion=eliminaentrega&id=<?php echo $prenda['idMov']."-".$prenda['correlativo']; ?>')"></td>  
                <?php  echo "</tr>";
                   } ?>   
              </table>      
              </div>
            </td>
          </tr>

          <tr>
            <td height="190" valign="top" colspan="4" >    
              <div class = "varios2c">
              <table border="1" width="100%" height="15" >
                <tr>
                  <th class = "noEditable"  colspan="8" align="center">Uniformes Formato Antiguo (no es editable, usar el panel de arriba)</th>
                </tr>
                <tr>
                  <th class = "noEditableEncab" width="35" align="center"><b>Id</b></th>
                  <th class = "noEditableEncab" width="65" align="center"><b>Prenda</b></th>
                  <th class = "noEditableEncab" width="60" align="center"><b>F.Entrega</b></th>
                  <th class = "noEditableEncab" width="280" align="center"><b>Observacion</b></th>
                  <th class = "noEditableEncab" width="70" align="center"><b>Creador</b></th>
                  <th class = "noEditableEncab" width="60" align="center"><b>F.Creacion</b></th>
                  <th class = "noEditableEncab" align="center"></th>
                </tr>
                <?php
                  $color = 'celeste';   
                  foreach($prendasAnt as $prenda) {
                    $color = ($color == 'plomo') ? 'celeste' : 'plomo';
                    echo "<tr>";
                    echo "<td class = '$color' >".$prenda['id']."</td>";
                    echo "<td class = '$color' >".$prenda['idDetalle']."</td>";
                    echo "<td class = '$color' >".$prenda['fchEntrega']."</td>";
                    echo "<td class = '$color' >".$prenda['observacion']."</td>";
                    echo "<td class = '$color' >".$prenda['creacUsuario']."</td>";
                    echo "<td class = '$color' >".$prenda['creacFch']."</td>";
                    echo "<td class = '$color' >";
                    echo "</tr>";
                   } ?>   
              </table>      
              </div>
            </td>
          </tr>      
          <tr>
            <td height="800" valign="top" colspan="4">
            <div id="tabsDocs" >
              <ul>
                <li><a href="#tbDoc1">Contratos</a></li>
                <li><a href="#tbDoc2">Telfs. Asignados</a></li>
                <li><a href="#tbDoc3">Vencimientos</a></li>
                <li><a href="#tbDoc4">Docs de identidad</a></li>
                <li><a href="#tbDoc5">Otros docs adjuntos</a></li>
              </ul>

              <div id="tbDoc1"  style="width: 90% ; height: 300px;">
                <div align="left"  style="width: 100%; height: 400px;  overflow:auto">
                  <table id="dataContratos" class="display compact" cellspacing="0" width="100%">
                    <thead>
                      <tr>
                        <th>Id Contrato</th>
                        <th>Creac Fch</th>
                        <th>Ruta Archivo</th>
                        <th>Nro Contrato</th>
                        <th>Régimen</th>
                        <th>Modo</th>
                        <th>Fch Inicio</th>
                        <th>Fch Fin</th>
                        <th>Fch Ult Día Trab</th>
                        <th>Estado</th>
                        <th>Acción</th>
                      </tr>
                    </thead>
                    <tfoot>
                      <tr>
                        <th>Id Contrato</th>
                        <th>Creac Fch</th>
                        <th>Ruta Archivo</th>
                        <th>Nro Contrato</th>
                        <th>Régimen</th>
                        <th>Modo</th>
                        <th>Fch Inicio</th>
                        <th>Fch Fin</th>
                        <th>Fch Ult Día Trab</th>
                        <th>Estado</th>
                        <th>Acción</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>

              <div id="tbDoc2" class = 'varios'>
                <table border="1" width="100%" height="20" >
                  <tr>
                    <th class = "principal" align="center" colspan="5">Teléfonos Asignados
                      <img border="0" src="imagenes/mas.png" width="20" height="20" align="right" onclick =  "abrirventanaflotante('index.php?controlador=trabajadores&accion=nuevoasigtelefono&dni=<?php echo $dni ?>') ">
                    </th>
                  </tr>
                  <tr>
                    <th width="65" align="center"><b>Telf.</b></th>
                    <th width="65" align="center"><b>F. Inic</b></th>
                    <th width="70" align="center"><b>F. Fin</b></th>
                    <th width="200" align="center"><b>Observ</b></th>
                    <th width="40" align="center">.</th>
                  </tr>
                  <?php
            
                    $color = 'celeste';   
                    foreach($telfAsignados as $telefono) {
                      $color = ($color == 'plomo') ? 'celeste' : 'plomo';
                  echo "<tr>";
                  echo "<td class = '$color' >".$telefono['idNroTelefono']."</td>";
                  echo "<td class = '$color' >".$telefono['fchInicio']."</td>";
                  echo "<td class = '$color' >".$telefono['fchFin']."</td>";
                  echo "<td class = '$color' >".$telefono['observacion']."</td>";
                  echo "<td class = '$color' >";
                ?>    
                  <img border="0" src="imagenes/lapiz.jpg" width="15" height="15" onclick =  "abrirventanaflotante('index.php?controlador=trabajadores&accion=editaasigtelefono&dni=<?php echo $dni ?>&nrotelefono=<?php echo $telefono['idNroTelefono']?>&fchini=<?php echo $telefono['fchInicio']?> &fchfin=<?php echo $telefono['fchFin']?> &observ=<?php echo $telefono['observacion']?>   ')"> 
                  <img border="0" src="imagenes/menos.jpg" width="15" height="15" onclick =  "abrirventanaflotante('index.php?controlador=trabajadores&accion=eliminaasigtelefono&dni=<?php echo $dni ?>&nrotelefono=<?php echo $telefono['idNroTelefono']?>&fchini=<?php echo $telefono['fchInicio']?> &fchfin=<?php echo $telefono['fchFin']?> &observ=<?php echo $telefono['observacion']?>   ')">  
                  <?php  echo "</tr>";
                     } ?>   
                </table>
              </div>

              <div id="tbDoc3" class = 'varios'>
                <table border="1" width="100%" height="20" >
                  <tr>
                    <th class = "principal" colspan="5" align="center">
                      <img border="0" src="imagenes/mas.png" width="20" height="20" align="right" onclick =  "abrirventanaflotantemediana('index.php?controlador=trabajadores&accion=nuevovencimiento&id=<?php echo $dni ?>') ">
                      Vencimientos
                    </th>
                  </tr>
                  <tr>
                    <th width="85" align="center"><b>Nombre</b></th>
                    <th width="65" align="center"><b>Ini</b></th>
                    <th width="65" align="center"><b>Fin</b></th>
                    <th width="150" align="center"><b>Observación</b></th>
                    <th  align="center">Acción</th>
                  </tr>
                    <?php
                    $color = 'celeste';   
                    foreach($vencimientos as $vencimiento) {
                      $color = ($color == 'plomo') ? 'celeste' : 'plomo';
                      $escaneo = $vencimiento['escaneo'];
                      echo "<tr>";
                      echo "<td class = '$color' >".$vencimiento['nombre']."</td>";
                      echo "<td class = '$color' >".$vencimiento['fchInicio']."</td>";
                      echo "<td class = '$color' >".$vencimiento['fchFin']."</td>";
                      echo "<td class = '$color' >".$vencimiento['observacion']."</td>";
                      echo "<td class = '$color' >";
                    ?>
                      <img border="0" src="imagenes/lapiz.jpg" width="13" height="13" onclick = "abrirventanaflotante('index.php?controlador=trabajadores&accion=editavencimtrab&dni=<?php echo $dni ?>&fchinicio=<?php echo $vencimiento['fchInicio'] ?>&nombre=<?php echo $vencimiento['nombre'] ?> ')">
                      <img border="0" src="imagenes/menos.jpg" width="13" height="13" onclick =  "abrirventanaflotante('index.php?controlador=trabajadores&accion=eliminavencimtrab&dni=<?php echo $dni ?>&fchinicio=<?php echo $vencimiento['fchInicio'] ?>&nombre=<?php echo $vencimiento['nombre'] ?> ')">
                      <?php
                        if ($escaneo == "")
                          echo "ND";
                        else { ?>
                          <img class="sinTitulo" title="Descargar el documento escaneado" border="0" src="imagenes/descargar.png" width="13" height="13" onclick =  "abrirventanaflotante('vistas/descargarEscaneoTrab.php?id=<?php echo $escaneo ?>')">
                        <?php 
                             } ?>
                      <img class="sinTitulo" title="Para subir y reemplazar| el documento escaneado" border="0" src="imagenes/subir.png" width="13" height="13" onclick =  "abrirventanaflotante('index.php?controlador=trabajadores&accion=cambiarAdjuntoTrab&nombre=<?php echo urlencode($vencimiento['nombre']) ?>&fchInicio=<?php echo $vencimiento['fchInicio'] ?>&dni=<?php echo $dni ?> ')">  
                      </td>  
                    <?php  echo "</tr>";
                     } ?>   
                </table>
              </div>

              <div id="tbDoc4"  class = 'variosAncho90p'>
                <div align="left"  style="width: 100%; height: 190px;  overflow:auto">
                  <table id="dataDocums" class="display compact" cellspacing="0" width="100%">
                    <thead>
                      <tr>
                        <th>Tipo</th>
                        <th>Nro</th>
                        <th>Ini</th>
                        <th>Fin</th>
                        <th>Estado</th>
                        <th>Adjunto</th>
                        <th>Acción</th>
                      </tr>
                    </thead>
                    <tfoot>
                      <tr>
                        <th>Tipo</th>
                        <th>Nro</th>
                        <th>Ini</th>
                        <th>Fin</th>
                        <th>Estado</th>
                        <th>Adjunto</th>
                        <th>Acción</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>  


              <div id="tbDoc5" style="height: 500px;">
                <table border="1" width="100%">
                <tr>
                  <th class = "principal" colspan="4" align="center">
                    <img border="0" src="imagenes/mas.png" width="20" height="20" align="right" onclick =  "abrirventanaflotantemediana('index.php?controlador=trabajadores&accion=nuevodocumentoadjunto&id=<?php echo $dni ?>') ">
                    Documentos Varios Adjuntos
                  </th>
                </tr>
                <tr>
                  <th width="50" align="center"><b>Código</b></th>
                  <th width="85" align="center"><b>Nombre</b></th>
                  <th width="290" align="center"><b>Descripción</b></th>
                  <th  align="center">Acción</th>
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
                    <img border="0" src="imagenes/lapiz.jpg" width="13" height="13" onclick = "abrirventanaflotantemediana('index.php?controlador=trabajadores&accion=editadocumentoadjunto&id=<?php echo$docItem['id'] ?>')">
                    <img border="0" src="imagenes/menos.jpg" width="13" height="13" onclick =  "abrirventanaflotante('index.php?controlador=trabajadores&accion=eliminadocumentoadjunto&id=<?php echo$docItem['id'] ?>')">
                    <?php
                      if ($escaneo == "")
                        echo "ND";
                      else { ?>
                        <img class="sinTitulo" title="Descargar el documento escaneado" border="0" src="imagenes/descargar.png" width="13" height="13" onclick =  "abrirventanaflotante('vistas/descargarEscaneoTrab.php?id=<?php echo $escaneo ?>')">

                        <?php
                        $acceso = array("gpicasso", "rrhh_zoila", "mromacca");
                        if(in_array($_SESSION['usuario'], $acceso)){
                        ?>
                          <img class="sinTitulo" title="Enviar boleta de pago" border="0" src="imagenes/gmail.png" width="13" height="13" onclick =  "abrirventanaflotante('vistas/enviarBoletaTrab.php?id=<?php echo $escaneo ?>'+'&idtrab=<?php echo $dni ?>'+'&idTabla=<?php echo $docItem['id'] ?>')">
                        <?php } ?>


                      <?php 
                           } ?>
                    <img class="sinTitulo" title="Para subir y reemplazar| el documento escaneado" border="0" src="imagenes/subir.png" width="13" height="13" onclick =  "abrirventanaflotante('index.php?controlador=trabajadores&accion=cambiarAdjuntoDocumento&id=<?php echo  $docItem['id'] ?>')">
                    </td>  
                  <?php  echo "</tr>";
                   } ?>   
                </table>
              </div>
            </div>
            </td>
          </tr>
          <tr>
            <td  valign="top"></td>
            <td  valign="top" colspan = '2'>
            </td>
            <td>&nbsp;</td>            
          </tr>


          <tr>
            <th colspan="4">
              Relación con Inversiones Moy
            </th>
          </tr>
          <tr>
            <td colspan="2" >Laboró anteriormente en Inversiones Moy &nbsp;&nbsp;&nbsp;&nbsp;
              <select size="1" name="cmbLaboroEnMoy" id="cmbLaboroEnMoy">
              <option <?php if ($item['laboroEnMoy'] == NULL) echo 'selected' ?> >-</option>
              <option <?php if ($item['laboroEnMoy'] == 'Si') echo 'selected' ?> >Si</option>
              <option <?php if ($item['laboroEnMoy'] == 'No') echo 'selected' ?> >No</option>
              </select>
            </td>
            <td width="90" ><div class= 'laboroEnMoy'>Puesto</div></td>
            <td width="150"><div class= 'laboroEnMoy'><input type="text" name="txtLaboroEnMoyPuesto" size="12"  value = "<?php echo $item['laboroEnMoyPuesto'];?>" ></div></td>
          </tr>

          <tr>
            <td width="90" ><div class= 'laboroEnMoy'>Sucursal</div></td>
            <td><div class= 'laboroEnMoy'><input type="text" name="txtLaboroEnMoySucursal" size="12" value = "<?php echo $item['laboroEnMoySucursal'];?>" ></div></td>
            <td width="150" ><div class= 'laboroEnMoy'>Motivo de Cese</div></td>
            <td><div class= 'laboroEnMoy'><input type="text" name="txtLaboroEnMoyMotivoCese" size="15" value = "<?php echo $item['laboroEnMoyMotivoCese'];?>" ></div></td>
          </tr>
          
          <tr>
            <td  width="90" ><div class= 'laboroEnMoy'>Fecha de Cese</div></td>
            <td><div class= 'laboroEnMoy'><input type="text" name="txtLaboroEnMoyFchCese" id="txtLaboroEnMoyFchCese"  size="12" value = "<?php echo $item['laboroEnMoyFchCese'];?>" ></div></td>
            <td></td><td></td>
          </tr>
          <tr>
            <td colspan="2">Tiene familiar o amigo que labore en Inv. Moy 
              <select size="1" name="cmbFamiliarEnMoy" id="cmbFamiliarEnMoy">
              <option <?php if ($item['familiarEnMoy'] == NULL) echo 'selected' ?> >-</option>
              <option <?php if ($item['familiarEnMoy'] == 'Si') echo 'selected' ?> >Si</option>
              <option <?php if ($item['familiarEnMoy'] == 'No') echo 'selected' ?> >No</option>
              </select>
            </td>
            <td></td><td></td>
          </tr>
          <tr>
            <td><div class = 'familiarEnMoy'>Nombre y Apellido</div></td>
            <td><div class = 'familiarEnMoy'>
              <input type="text" name="txtFamiliarEnMoyNombre" size="12"  value = "<?php echo $item['familiarEnMoyNombre'];?>" ></div>
            </td>
            <td><div class = 'familiarEnMoy'>Parentesco</div></td>
            <td><div class = 'familiarEnMoy'>
              <input type="text" name="txtFamiliarEnMoyParentesco" size="12" value = "<?php echo $item['familiarEnMoyParentesco'];?>" ></div>
            </td>
          </tr>  
          <tr>
            <th colspan="4">
              En caso de Emergencia avisar a
            </th>
          </tr>
          <tr>
            <td width="90" >Nombres y Apellidos</td>
            <td width="150" ><input type="text" name="txtEmergenciaNombre" size="12" value = "<?php echo $item['emergenciaNombre'];?>" ></td>
            <td width="90" >Parentesco</td>
            <td width="150" ><input type="text" name="txtEmergenciaParentesco" size="12" value = "<?php echo $item['emergenciaParentesco'];?>" ></td>
          </tr>
          <tr>
            <td width="90" >Teléfono Fijo</td>
            <td><input type="text" name="txtEmergenciaTelfFijo" size="12"  value = "<?php echo $item['emergenciaTelfFijo'];?>" ></td>
            <td>Teléfono Celular</td>
            <td><input type="text" name="txtEmergenciaTelfCelular" size="12" value = "<?php echo $item['emergenciaTelfCelular'];?>" ></td>
          </tr>
          <tr>
            <th colspan="4">
              Salud
            </th>
          </tr>
          <tr>
            <td>Grupo Sanguineo</td><td><input type="text" name="txtSaludGrupoSanguineo" size="12"  value = "<?php echo $item['saludGrupoSanguineo'];?>" ></td>
            <td></td><td></td>
          </tr>
          <tr>
            <td colspan="2">żTiene alguna enfermedad crónica? 
              <select size="1" name="cmbSaludTieneEnfCronica" id="cmbSaludTieneEnfCronica">
                <option <?php if ($item['saludTieneEnfCronica'] == NULL) echo 'selected' ?> >-</option>
                <option <?php if ($item['saludTieneEnfCronica'] == 'Si') echo 'selected' ?> >Si</option>
                <option <?php if ($item['saludTieneEnfCronica'] == 'No') echo 'selected' ?> >No</option>
              </select>
            </td>
          </tr>
          <tr>  
            <td align="right"> <div class = 'enfermedadCronica'>Especificar</div></td>
            <td colspan="3">
              <div class = 'enfermedadCronica'>
              <textarea rows = '2' cols="50"  name="txtSaludEnfCronica" ><?php echo $item['saludEnfCronica'];?></textarea>
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="2">żPadece algún tipo de Alergia? 
              <select size="1" name="cmbSaludTieneAlergia" id="cmbSaludTieneAlergia">
                <option <?php if ($item['saludTieneAlergia'] == NULL) echo 'selected' ?> >-</option>
                <option <?php if ($item['saludTieneAlergia'] == 'Si') echo 'selected' ?> >Si</option>
                <option <?php if ($item['saludTieneAlergia'] == 'No') echo 'selected' ?> >No</option>
              </select>
            </td>
          </tr>
          <tr>  
            <td align="right"> <div class = 'alergia'>Especificar</div></td>
            <td colspan="3">
              <div class = 'alergia'>
              <textarea rows = '2' cols="50"  name="txtSaludAlergia"><?php echo $item['saludAlergia'];?></textarea>
              </div>
            </td>
          </tr>
          <tr>
          </tr>

          <tr>
            <td  valign="top">&nbsp;</td>
            <td  valign="top"><input type="reset" value="Limpiar" name="B2"><input type="submit" value="Enviar" name="btnEnviar"></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table> 

  <input type=hidden name= idTrabajador size=50 value = "<?php echo $item['idTrabajador'] ?>">
  <input type=hidden name= tipoDocTrab size=50 value = "<?php echo $item['tipoDocTrab'] ?>">
  <input type=hidden name= tipoTrabajador value = "<?php echo $item['tipoTrabajador']?>">
  <input type=hidden name= remuneracionOriginal size=20 value= "<?php echo $item['remuneracionBasica']?>">
  <input type=hidden name= estado size=20 value = "<?php echo $item['estadoTrabajador']?>">
  <input type=hidden name= categTrabajador value = "<?php echo $item['categTrabajador']?>">
  <input type=hidden name= descontarSeguro value = "<?php echo $item['descontarSeguro']?>">

  <input type=hidden name= poliza value = "<?php echo $item["idPoliza"]?>">

  <input type=hidden name= descontarComisionAfp value = "<?php echo $item['descontarComisionAfp']?>">
  <input type=hidden name= descontarAporteAfp value = "<?php echo $item['descontarAporteAfp']?>">
  <input type=hidden name= deseaDscto value = "<?php echo $item['deseaDscto']?>">
  <input type=hidden name= entidadPension value = "<?php echo $item['entidadPension']?>">
  <input type=hidden name= cuspp value = "<?php echo $item['cuspp']?>">
  <input type=hidden name= tipoComision value = "<?php echo $item['tipoComision']?>">

</form>

<?php } ?>

  </body>
</html>