<?php
  if (!isset($_POST['btnEnviar'])){
    foreach ($dataCliente as $key => $value) {
      $nombreCliente = $value['nombre'];
    }
  ?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <link rel =stylesheet href="librerias/estilos.css" type ="text/css">
  <script language="JavaScript" type="text/javascript" src="librerias/varios.js"></script>
  <link rel =stylesheet href="librerias/DataTables/datatables.min.css" type ="text/css">
  <link rel =stylesheet href="librerias/DataTables/Button-1.4.2/dataTables.buttons.min.css" type ="text/css">
  <script language="JavaScript" type="text/javascript" src="librerias/DataTables/datatables.min.js">

  </script>
  <script language="JavaScript" type="text/javascript" src="librerias/DataTables/Button-1.4.2/dataTables.buttons.min.js"></script>

  <style>
   .pointer { cursor: pointer; }
  </style>
  <title></title>

  <script type="text/javascript">
    function validaEntradas(forma){

    }

    $(document).ready(function(){
      $("#tabCuenta").tabs();
      $("#tabPdto").tabs();
      <?php if ($opcionEditar == "No"){ ?>
        $("#tabCuenta").tabs( "disable", "#tabProductos");
      <?php }   ?>

      $( "#txtM3Facturable" ).spinner({
        min: 2,
        max: 1000
      });

      $( "#txtPuntos" ).spinner({
        min: 0,
        max: 255
      });

      $('.tiempo').timepicker({
        timeFormat: 'HH:mm:ss',
        stepHour: 1,
        stepMinute: 1,
        stepSecond: 10
      });

      if ($("#txtNombCuenta").val() != ""){
        $('#celdaCuenta').html($("#txtNombCuenta").val());
        $("#cmbPdtos").html("<?php cargarCmbPdtos($db, $id, $corr); ?>");
        $('#txtCorrelativo').val('<?php echo $corr; ?>');
      }  

      $("#btnCrear").click(function() {
        alert($("#txtCorrelativo").val())
        var id = '<?php echo $id; ?>'
        var nombre = $("#txtNombCuenta").val()
        var estadoCuenta = $("#cmbEstadoCuenta").val()
        var paraMovil = $("#cmbParaMovil").val()
        var correl = $("#txtCorrelativo").val()
        var tipoCuenta = $("#cmbTipoCuenta").val()
        if (nombre.length <=3){
          $("#mensajes").css("background-color",'#EF4E45');/*rojo*/
          $('#mensajes').html('*** ERROR. El nombre de la cuenta es muy corto ***');
        } else {
          var parametros = {
              "usuario" : '<?php echo $_SESSION['usuario'];  ?>',
              "opcion"    : 'crearCuenta',
              "id"    : id,
              "nombre": nombre,
              "estadoCuenta": estadoCuenta,
              "paraMovil": paraMovil,
              "correl": correl,
              "tipoCuenta": tipoCuenta,
            };
          $.ajax({
            data:  parametros,
            url:   'ajaxManejoCuentas.php',
            timeout: 15000,
            type:  'post',
            beforeSend: function () {
              $('#mensajes').html('<img src="./imagenes/precarga.gif">');
            },
            success:  function (response) {
              arrRpta = response.split("|")
              cant = arrRpta[0]
              correl = arrRpta[1]
              accion = arrRpta[2]
              if (cant == 1 && accion == "Crear"){
                $("#mensajes").css("background-color",'#0CC918');/*verde*/
                $('#mensajes').html('Se insertó nueva cuenta');
                $('#celdaCuenta').html(nombre);
                $('#txtCorrelativo').val(correl);
                $("#tabCuenta").tabs( "enable", "#tabProductos");

              } else if (cant == 1 && accion == "Editar"){
                $("#mensajes").css("background-color",'#0CC918');/*verde*/
                $('#mensajes').html('Se editó la cuenta');
                $('#celdaCuenta').html(nombre);
                $('#txtCorrelativo').val(correl);
                $("#tabCuenta").tabs( "enable", "#tabProductos");

              } else {
                $("#mensajes").css("background-color",'#FFCC00');/*amarillo*/
                $("#mensajes").css("color",'#000000');/*negro*/
                $('#mensajes').html('*** Advertencia, no se insertó ningún registro. Puede tratarse de una edición o el nombre de la cuenta ya existe para este cliente ***');
              }
            }
          });
        }

      });

      $("#btnProducto").click(function() {
        var nombProducto = $("#txtNombProducto").val()        
      //  var estadoCuenta = $("#cmbEstadoCuenta").val()
      //  var paraMovil = $("#cmbParaMovil").val()
        if (nombProducto.length <=3){
          $("#mensajes").css("background-color",'#EF4E45');/*rojo*/
          $('#mensajes').html('*** ERROR. El nombre del producto es muy corto ***');
          $("#txtNombProducto").css("background-color",'#EF4E45');/*rojo*/
        } else if($("#cmbCuentaDespacho").val() == '-'){
          $("#mensajes").css("background-color",'#EF4E45');/*rojo*/
          $('#mensajes').html('*** ERROR. No ha seleccionado un valor en Cuenta Despacho ***');
          $("#cmbCuentaDespacho").css("background-color",'#EF4E45');/*rojo*/
        }  else if($("#cmbPdtos").val() == null){
          $("#mensajes").css("background-color",'#EF4E45');/*rojo*/
          $('#mensajes').html('*** ERROR. No ha seleccionado ningún producto ***');
          $("#cmbPdtos").css("background-color",'#EF4E45');/*rojo*/
        } else if($("#txtM3Facturable").val() ==""){
          $("#mensajes").css("background-color",'#EF4E45');/*rojo*/
          $('#mensajes').html('*** ERROR. No ha indicado M3 facturable ***');
          $("#txtM3Facturable").css("background-color",'#EF4E45');/*rojo*/
        } else if($("#txtPuntos").val() ==""){
          $("#mensajes").css("background-color",'#EF4E45');/*rojo*/
          $('#mensajes').html('*** ERROR. No ha indicado los puntos. indique 0 si no es importante en este producto ***');
          $("#txtPuntos").css("background-color",'#EF4E45');/*rojo*/
        } else if($("#txtZona").val() =="-"){
          $("#mensajes").css("background-color",'#EF4E45');/*rojo*/
          $('#mensajes').html('*** ERROR. No ha indicado la Zona. indique LOCAL si no es importante en este producto ***');
          $("#txtZona").css("background-color",'#EF4E45');/*rojo*/
        } else if($("#txtHraNormalConductor").val() =="00:00:00"){
          $("#mensajes").css("background-color",'#EF4E45');/*rojo*/
          $('#mensajes').html('*** ERROR. Debe ingresar la hora normal del Conductor ***');
          $("#txtHraNormalConductor").css("background-color",'#EF4E45');/*rojo*/
        } else if($("#txtHraNormalAux").val() =="00:00:00"){
          $("#mensajes").css("background-color",'#EF4E45');/*rojo*/
          $('#mensajes').html('*** ERROR. Debe ingresar la hora normal del Auxiliar ***');
          $("#txtHraNormalAux").css("background-color",'#EF4E45');/*rojo*/
        } else {
          if(1*$("#txtPrecServicio").val() == 0){
            $("#mensajes").css("background-color",'#FFCC00');/*amarillo*/
            $('#mensajes').html('*** ADVERTENCIA. Prec Servicio es cero ***');
            $("#mensajes").css("color",'#000000');/*negro*/
            $("#txtPrecServicio").css("background-color",'#FFCC00');/*amarillo*/
            rpta = confirm("El valor del Precio del Producto está  en cero.\nżAcepta dicho valor?")
            if (!rpta){
              alert("Operación Cancelada")
              exit
            }
          }

          if(1*$("#txtKmEsperado").val() == 0){
            $("#mensajes").css("background-color",'#FFCC00');/*amarillo*/
            $("#mensajes").css("color",'#000000');/*negro*/
            $('#mensajes').html('*** ADVERTENCIA. Km. esperado es cero ***');
            $("#txtKmEsperado").css("background-color",'#FFCC00');/*amarillo*/
            rpta = confirm("El valor KM. Esperado está en cero.\nżAcepta dicho valor?")
            if (!rpta){
              alert("Operación Cancelada")
              exit
            }
          } 
          if(1*$("#txtHrasNormales").val() == 0){
            $("#mensajes").css("background-color",'#FFCC00');/*amarillo*/
            $("#mensajes").css("color",'#000000');/*negro*/
            $('#mensajes').html('*** ADVERTENCIA. Hras normales es cero ***');
            $("#txtHrasNormales").css("background-color",'#FFCC00');/*amarillo*/
            rpta = confirm("El valor Hras. Normales está en cero.\nżAcepta dicho valor?")
            if (!rpta){
              alert("Operación Cancelada")
              exit
            }
          }

          if(1*$("#txtValConductor").val() == 0){
            $("#mensajes").css("background-color",'#FFCC00');/*amarillo*/
            $("#mensajes").css("color",'#000000');/*negro*/
            $('#mensajes').html('*** ADVERTENCIA. Valor Conductor es cero ***');
            $("#txtValConductor").css("background-color",'#FFCC00');/*amarillo*/
            rpta = confirm("El valor del Conductor está en cero.\nżAcepta dicho valor?")
            if (!rpta){
              $( "#txtValConductor" ).focus();
              alert("Operación Cancelada")
              exit
            }

          }

          if(1*$("#txtValAuxiliar").val() == 0){
            $("#mensajes").css("background-color",'#FFCC00');/*amarillo*/
            $("#mensajes").css("color",'#000000');/*negro*/
            $('#mensajes').html('*** ADVERTENCIA. Valor Auxiliar es cero ***');
            $("#txtValAuxiliar").css("background-color",'#FFCC00');/*amarillo*/
            rpta = confirm("El valor del Auxiliar está en cero.\nżAcepta dicho valor?")
            if (!rpta){
              $( "#txtValAuxiliar" ).focus();
              alert("Operación Cancelada")
              exit
            }
          }

          if(1*$("#txtNroAuxiliares").val() == 0){
            $("#mensajes").css("background-color",'#FFCC00');/*amarillo*/
            $("#mensajes").css("color",'#000000');/*negro*/
            $('#mensajes').html('*** ADVERTENCIA. El nro. de Auxiliares es cero ***');
            $("#txtNroAuxiliares").css("background-color",'#FFCC00');/*amarillo*/
            rpta = confirm("El nro. de Auxiliares es cero.\nżAcepta dicho valor?")
            if (!rpta){
              $( "#txtNroAuxiliares" ).focus();
              alert("Operación Cancelada")
              exit
            }
          }

          if(1*$("#txtValUnidadTercCC").val() == 0){
            $("#mensajes").css("background-color",'#FFCC00');/*amarillo*/
            $("#mensajes").css("color",'#000000');/*negro*/
            $('#mensajes').html('*** ADVERTENCIA. Valor Unidad Tercero C/Conductor es cero ***');
            $("#txtValUnidadTercCC").css("background-color",'#FFCC00');/*amarillo*/
            rpta = confirm("El valor Unidad Tercero C/Conductor está en cero.\nżAcepta dicho valor?")
            if (!rpta){
              $( "#txtValUnidadTercCC" ).focus();
              alert("Operación Cancelada")
              exit
            }
          }

          if(1*$("#txtValUnidadTercSC").val() == 0){
            $("#mensajes").css("background-color",'#FFCC00');/*amarillo*/
            $("#mensajes").css("color",'#000000');/*negro*/
            $('#mensajes').html('*** ADVERTENCIA. Valor Unidad Tercero S/Conductor es cero ***');
            $("#txtValUnidadTercSC").css("background-color",'#FFCC00');/*amarillo*/
            rpta = confirm("El valor Unidad Tercero S/Conductor está en cero.\nżAcepta dicho valor?")
            if (!rpta){
              $( "#txtValUnidadTercSC" ).focus();
              alert("Operación Cancelada")
              exit
            }
          }

          if(1*$("#txtValAuxiliarTercero").val() == 0){
            $("#mensajes").css("background-color",'#FFCC00');/*amarillo*/
            $("#mensajes").css("color",'#000000');/*negro*/
            $('#mensajes').html('*** ADVERTENCIA. Valor Auxiliar Tercero es cero ***');
            $("#txtValAuxiliarTercero").css("background-color",'#FFCC00');/*amarillo*/
            rpta = confirm("El valor Auxiliar Tercero está en cero.\nżAcepta dicho valor?")
            if (!rpta){
              $( "#txtValAuxiliarTercero" ).focus();
              alert("Operación Cancelada")
              exit
            }
          }

          var parametros = {
              "opcion"    : 'crearEditarCuentaProducto',
              "usuario" : '<?php echo $_SESSION['usuario'];  ?>',
              "idCliente" :'<?php echo $id; ?>',
              "idPdto" : $("#cmbPdtos").val(),
              "correl" : $("#txtCorrelativo").val(),
              "nombProducto" : nombProducto,
              "m3Facturable" : $("#txtM3Facturable").val(),
              "puntos" :  $("#txtPuntos").val(),
              "idZona" :  $("#txtZona").val(),
              "zona" : $("#txtZona :selected").text(),
              "estadoProducto" : $("#cmbEstadoProducto :selected").text(),
              "precioServ" :  $("#txtPrecServicio").val(),
              "kmEsperado" :  $("#txtKmEsperado").val(),
              "tolerKmEsperado" :  $("#txtTolerKmEsperado").val(),
              "valKmAdic" :  $("#txtValKmAdicional").val(),
              "hrasNormales" :  $("#txtHrasNormales").val(),
              "tolerHrasNormales" :  $("#txtTolerHrasNormal").val(),
              "valHraAdic" :  $("#txtValHraAdic").val(),
              "hraIniEsperado" :  $("#txtHraIniEsperado").val(),
              "tolerHraIniEsperado" :  $("#txtTolerHraIniEsperado").val(),
              "valAdicHraIniEsper" :  $("#txtValAdicHraIniEsp").val(),
              "hraFinEsperado" :  $("#txtHraFinEsperado").val(),
              "tolerHraFinEsperado" :  $("#txtTolerHraFinEsperado").val(),
              "valAdicHraFinEsper" :  $("#txtValAdicHraFinEsp").val(),
              "nroAuxiliares" :  $("#txtNroAuxiliares").val(),
              "valAuxiliarAdic" :  $("#txtValAuxAdic").val(),
              "cobrarPeaje" :  $("#cmbPeaje").val(),
              "cobrarRecojoDevol" :  $("#txtRecojoDev").val(),
              "valConductor" :  $("#txtValConductor").val(),
              "valAuxiliar" :  $("#txtValAuxiliar").val(),
              "usoMaster" :  $("#cmbMaster").val(),
              "valUnidTercCCond" :  $("#txtValUnidadTercCC").val(),
              "valUnidTercSCond" :  $("#txtValUnidadTercSC").val(),
              "hrasNormalTerc" :  $("#txtHrasNormalTerc").val(),
              "tolerHrasNormalTerc" :  $("#txtTolerHrasNormalTerc").val(),
              "valHraExtraTerc" :  $("#txtValHraExtraTerc").val(),
              "valKmAdicTerc" :  $("#txtValKmAdicTerc").val(),
              "hraNormalConductor" : $("#txtHraNormalConductor").val(),
              "tolerHraConductor" : $("#txtTolerHraConductor").val(),
              "valHraExtraCond" : $("#txtValHraExtraCond").val(),
              "hraNormalAux" : $("#txtHraNormalAux").val(),
              "tolerHraAux" : $("#txtTolerHraAux").val(),
              "valHraExtraAux" : $("#txtValHraExtraAux").val(),
              "valAuxiliarTercero" : $("#txtValAuxiliarTercero").val(),
              "valContarDespacho" : $("#cmbCuentaDespacho").val(),
              "valSuperCuenta" : $("#txtSuperCuenta").val(),
              "tipoProducto" : $("#cmbTipoCuenta").val(),
            };
          $.ajax({
            data:  parametros,
            url:   'ajaxManejoCuentas.php',
            timeout: 15000,
            type:  'post',
            beforeSend: function () {
              $('#mensajes').html('<img src="./imagenes/precarga.gif">');
            },
            success:  function (response) {
              if (response == 1){
                $("#mensajes").css("background-color",'#0CC918');/*verde*/
                $('#mensajes').html('Se insertó / editó el Producto satisfactoriamente');

                $.post("ajaxManejoCuentas.php", {opcion: "cargarCmbPdtos",idCliente: "<?php echo $id; ?>", correl : $('#txtCorrelativo').val() }, function(htmlOpciones){
                  $("#cmbPdtos").css("background-color",'#FFFFFF');/*blanco*/
                  $("#cmbPdtos").html(htmlOpciones);
                });   
              } else {
                $("#mensajes").css("background-color",'#EF4E45');/*rojo*/
                $("#mensajes").css("color",'#000000');/*negro*/
                $('#mensajes').html('*** Advertencia, no se insertó/editó ningún registro.***');
              }
            }
          });
        }
      });

      $("#cmbPdtos").change(function () {
        $("#dataOtros").dataTable().fnDestroy();
        valor = $(this).val();
        //t.destroy();
        var t = $('#dataOtros').DataTable({
          "paging": false,
          "searching": false,
          "scrollCollapse": true,
          "lengthMenu": [ 14, 28 ],
          "oLanguage": {
            "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ entradas",
            "sLengthMenu": "Mostrar _MENU_ registros",
          },
          "responsive": true,
          "processing": true,
          "serverSide": true,
          "columnDefs": [
              {"width": "15", "targets": [0]},
              {"width": "40", "targets": [2]},
              {"width": "100", "targets": [1]},
              {"width": "60", "targets": [3]},
              {"width": "160", "targets": [4]},
            ],

        "ajax":{
              "url": "vistas/ladoServidor/ladoServidor.php?opcion=cargaCuentaPdtoOtros",
              "data": function ( d ) {
                d.idProducto = valor; 
              }
        },
        dom: '<"toolbar">frtip',
      });

      $('#dataOtros_wrapper .toolbar').on( 'click', '#btnMas', function(){
        //alert("Prueba")
        dialogOtro.dialog( "open" );
      });

      $("div.toolbar").html("<span name = 'btnMas' id = 'btnMas' ><img border='0' src='imagenes/mas.jpg' width='20' height='20' align='left' ></span>");

      $('#dataOtros_filter').hide()

        if (valor == 'Nuevo Producto'){
          $('#txtNombProducto').val('')
          $('#txtM3Facturable').val('')
          $('#txtPuntos').val('')

          $("#txtNombProducto").css("background-color",'#FFFFFF');/*blanco*/
          $("#cmbPdtos").css("background-color",'#FFFFFF');/*blanco*/
          $("#txtM3Facturable").css("background-color",'#FFFFFF');/*blanco*/
          $("#txtPuntos").css("background-color",'#FFFFFF');/*blanco*/
          $(".cliCuenta input").each(function(){
              $(this).val("")
          });
          $("#mensajes").css("background-color",'#FFFFFF');/*Blanco*/
          $('#mensajes').html('');
        } else {
          var parametros = {
            "opcion"    : 'buscarDataPdto',
            "idProducto" : valor,              
          }

          $.ajax({
            data:  parametros,
            url:   'ajaxManejoCuentas.php',
            timeout: 15000,
            type:  'post',
            beforeSend: function () {
              $('#mensajes').html('<img src="./imagenes/precarga.gif">');
            },
            success:  function (response) {
              if (response.length > 1){
                arrValores = response.split("|")
                $('#txtNombProducto').val(arrValores[0])
                $('#txtM3Facturable').val(arrValores[1])
                $('#txtPuntos').val(arrValores[2])
                $('#txtZona').val(arrValores[3])
                $('#txtPrecServicio').val(arrValores[4])
                $('#txtKmEsperado').val(arrValores[5])
                $('#txtTolerKmEsperado').val(arrValores[6])
                $('#txtValKmAdicional').val(arrValores[7])
                $('#txtHrasNormales').val(arrValores[8])
                $('#txtTolerHrasNormal').val(arrValores[9])
                $('#txtValHraAdic').val(arrValores[10])
                $('#txtHraIniEsperado').val(arrValores[11])
                $('#txtTolerHraIniEsperado').val(arrValores[28])

                $('#txtValAdicHraIniEsp').val(arrValores[12])
                $('#txtHraFinEsperado').val(arrValores[13])
                $('#txtTolerHraFinEsperado').val(arrValores[29])

                $('#txtValAdicHraFinEsp').val(arrValores[14])
                $('#txtNroAuxiliares').val(arrValores[15])
                $('#txtValAuxAdic').val(arrValores[16])

                $('#cmbPeaje').val(arrValores[17])
                $('#txtRecojoDev').val(arrValores[18])
                $('#txtValConductor').val(arrValores[19])
                $('#txtValAuxiliar').val(arrValores[20])
                $('#cmbMaster').val(arrValores[21])
                $('#txtValUnidadTercCC').val(arrValores[22])
                $('#txtValUnidadTercSC').val(arrValores[23])
                $('#txtHrasNormalTerc').val(arrValores[24])

                $('#txtTolerHrasNormalTerc').val(arrValores[25])
                $('#txtValHraExtraTerc').val(arrValores[26])
                $('#txtValKmAdicTerc').val(arrValores[27])

                $("#txtTolerHraIniEsperado").val(arrValores[28])
                $("#txtTolerHraFinEsperado").val(arrValores[29])

                $("#cmbEstadoProducto").val(arrValores[30])
                $("#txtHraNormalConductor").val(arrValores[31])
                $("#txtTolerHraConductor").val(arrValores[32])
                $("#txtValHraExtraCond").val(arrValores[33])
                $("#txtHraNormalAux").val(arrValores[34])
                $("#txtTolerHraAux").val(arrValores[35])
                $("#txtValHraExtraAux").val(arrValores[36])
                $("#txtValAuxiliarTercero").val(arrValores[37])

                $("#cmbCuentaDespacho").val(arrValores[38])
                $("#txtSuperCuenta").val(arrValores[39])

                /*   txtRecojoDev   */
                $("#mensajes").css("background-color",'#0CC918');/*verde*/
                $('#mensajes').html('Se recuperaron datos del producto '+ arrValores[0] );
              } else {
               
              }
            }
          });
        }
      })

      dialogOtro = $( "#dialogOtro" ).dialog({
        autoOpen: false,
        resizable: false,
        height: "auto",
        width: 450,
        modal: true,
        buttons: {
          "Crear Otro Criterio": addOtro,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function addOtro(){
        tipo = $("#cmbTipo").val()
        nombre = $("#txtNombre").val()
        tolerancia = $('#txtTolerancia').val()
        costoPorUnidad = $('#txtCostoPorUnidad').val()
        idProducto = $('#cmbPdtos').val()
        correlOtro = $('#correlOtro').val()

        //alert(idProducto)
        
        var parametros = {
          "idProducto":idProducto,
          "tipo":tipo , 
          "nombre":nombre , 
          "tolerancia":tolerancia , 
          "costoPorUnidad":costoPorUnidad,
          "correlOtro":correlOtro,
        }
        
        $.ajax({
          data:  parametros,
          url:   './vistas/ajaxVarios.php?opc=insertaOtro',
          timeout: 15000,
          type:  'post',
          success:  function (response) {
            //alert(response)
            $("#cmbTipo").val('-')
            $("#txtNombre").val('')
            $('#txtTolerancia').val('')
            $('#txtCostoPorUnidad').val('')
            $('#correlOtro').val('')

            $("#dialogOtro").dialog( "close" );
            t.ajax.reload();
          }
        });
      }

      $("#dialogOtro").dialog( "close" );


      dialogElimOtro = $( "#dialogElimOtro" ).dialog({
        autoOpen: false,
        resizable: false,
        height: "auto",
        width: 450,
        modal: true,
        buttons: {
          "Eliminar Otro Criterio": elimOtro,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function elimOtro(){
        correlOtro =  $('#txtCorrelElimOtro').val()
        idProducto = $('#cmbPdtos').val()
        //alert(idProducto)
        
        var parametros = {
          "idProducto":idProducto,
          "correlOtro":correlOtro,
        }
        
        $.ajax({
          data:  parametros,
          url:   './vistas/ajaxVarios.php?opc=elimOtro',
          timeout: 15000,
          type:  'post',
          success:  function (response) {
           /* $("#cmbTipo").val('-')
            $("#txtNombre").val('')
            $('#txtTolerancia').val('')
            $('#txtCostoPorUnidad').val('')  
            */
            $('#correlOtro').val('')      
            $("#dialogElimOtro").dialog( "close" );
            t.ajax.reload();
          }
        });
      }



      /*$('#tabProductos #dataOtros').on( 'click', 'tbody .editar',function(){
        alert("prueba");
      })
      */

      $('#tabProductos #dataOtros').on( 'click', 'tbody .editar',function(){
        correlOtro = $(this).parent().parent().find("td:first").text();
        idProducto = $('#cmbPdtos').val()

        var parametros = {
          "correlOtro":correlOtro,
          "idProducto":idProducto,

        };

        $.ajax({
          data:  parametros,
          url:   './vistas/ajaxVarios.php?opc=recuperaOtro',
          timeout: 15000,
          type:  'post',
          success:  function (response) {
              var obj = JSON.parse(response);
              $("#cmbTipo").val(obj.tipo);
              $("#txtNombre").val(obj.nombre);
              $("#txtTolerancia").val(obj.tolerancia);
              $("#txtCostoPorUnidad").val(obj.costoUnidad);
              $("#correlOtro").val(correlOtro);
              dialogOtro.dialog( "open" );            
          }  
        });
      });

      $('#tabProductos #dataOtros').on( 'click', 'tbody .eliminar',function(){
        correlOtro = $(this).parent().parent().find("td:first").text();
        //idProducto = $('#cmbPdtos').val()

        $('#txtCorrelElimOtro').val(correlOtro)

        dialogElimOtro.dialog( "open" );

        //alert(idProducto)
/*
        var parametros = {
          "correlOtro":correlOtro,
          "idProducto":idProducto,
        };

        $.ajax({
          data:  parametros,
          url:   './vistas/ajaxVarios.php?opc=eliminaOtro',
          timeout: 15000,
          type:  'post',
          success:  function (response) {
              var obj = JSON.parse(response);
              $("#cmbTipo").val(obj.tipo);
              $("#txtNombre").val(obj.nombre);
              $("#txtTolerancia").val(obj.tolerancia);
              $("#txtCostoPorUnidad").val(obj.costoUnidad);
              $("#correlOtro").val(correlOtro);

              dialogOtro.dialog( "open" );            
          }  
        });

        */
      });
    
    })

  </script>

  <style>
    .nav {
        float: left;
        max-width: 160px;
        margin: 0;
        padding: 1em;
        height: 300px;
    }

    .contenido {
        margin-left: 250px;
        border-left: 1px solid gray;
        padding: 1em;
        overflow: hidden;
        height: 360px;
    }
  </style>
 
  </head>
  <body>

  <div class = 'mensajes' id = "mensajes"></div>

    <div id="dialogOtro" title="Nuevo/Edita Otros Criterio">
      <div style="padding: 5px;">
      <form>
      <fieldset>
        <label>
          <select name="cmbTipo" id="cmbTipo" style="padding: 5px; font-size: 13px; width: 250px;">
              <option value = '-'>Elija el tipo de criterio:</option>
              <option value = 'Horas'>Horas</option>
              <option value = 'Paquete'>Paquete</option>
              <option value = 'Kms'>Kms</option>
              <option value = 'Peso'>Peso</option>
          </select>
        </label><br/>

        <label>
          <input style="padding: 5px; width: 250px" id="txtNombre" name="txtNombre" type="text"  placeholder="Nombre del Criterio" >
        </label><br/>
        <label>
          <input style="padding: 5px; width: 250px" id="txtTolerancia" name="txtTolerancia" type="text"  placeholder="Tolerancia (Puede ser cero)" >
        </label><br/>

        <label>
          <input style="padding: 5px; width: 250px" id="txtCostoPorUnidad" name="txtCostoPorUnidad" type="text"  placeholder="Costo por Unidad" >
        </label>
      </fieldset>
      <input type='hidden' name= 'correlOtro' id= 'correlOtro' value = '' >
      </form>
      </div>
    </div>

    <div id="dialogElimOtro" title="Elimina Otro Criterio">
      <div style="padding: 5px;">
      <form>
      <fieldset>
        
        <label>
          Id Correlativo <input style="padding: 5px; width: 100px" id="txtCorrelElimOtro" name="txtCorrelElimOtro" type="text" readonly>
        </label><br/>      
      </fieldset>
      </form>
      </div>
    </div>

  <div id="tabCuenta" class = 'nuevaForma'>
  <ul>
    <li><a href="#tabDataGral">Datos Cuenta</a></li>
    <li><a href="#tabProductos">Datos Productos</a></li>
  </ul>
  <div id="tabDataGral" class = 'nuevaForma'>
    <table border = '0' width="80%" class = 'nuevaForma'>
      <tr>
        <th width = '20%' >Cliente</th>
        <td> <?php echo "$id-$nombreCliente";  ?>  </td>
      </tr>
      <tr>
        <th>Correlativo</th>
        <td>
          <?php 
            if (isset($_GET['corr'])) echo $_GET['corr'];
            else echo "Lo generará el sistema";
          ?>
        </td>
      </tr>
      <tr>
        <th>Nombre Cuenta</th>
        <td>
          <input type="text" name="txtNombCuenta" id="txtNombCuenta" size="50" maxlength="30"  placeholder = 'Ingrese nombre de la cuenta (Máximo 30 caracteres)' value = '<?php echo $nombreCuenta; ?>' >
        </td>
      </tr>
      <tr>
        <th>Estado Cuenta</th>
        <td>
          <SELECT name = 'cmbEstadoCuenta' id = 'cmbEstadoCuenta'>
            <option <?php echo $estadoCuenta == "Activo" ? "SELECTED" : ""; ?> >Activo</option>
            <option <?php echo  $estadoCuenta == "Inactivo" ? "SELECTED" : ""; ?>>Inactivo</option>
          </SELECT>
        </td>
      </tr>
      <tr>
        <th>Tipo Cuenta</th>
        <td>         
          <SELECT name = 'cmbTipoCuenta' id = 'cmbTipoCuenta' <?php echo $opcionEditar == "Si" ? "disabled":"";  ?>   >
            <option <?php echo $tipoCuenta == "Normal" ? "SELECTED" : ""; ?> >Normal</option>
            <option <?php echo $tipoCuenta == "SoloPersonal" ? "SELECTED" : ""; ?> >SoloPersonal</option>
            <option <?php echo $tipoCuenta == "SoloVehiculo" ? "SELECTED" : ""; ?> >SoloVehiculo</option>
          </SELECT>                     
        </td>
      </tr>
      <tr>
        <th>Para Movil</th>
        <td>
          <SELECT name = 'cmbParaMovil' id = 'cmbParaMovil'>
            <option <?php echo $paraMovil == "No" ? "SELECTED" : ""; ?> >No</option>
            <option <?php echo $paraMovil == "Si" ? "SELECTED" : ""; ?> >Si</option>
          </SELECT>
        </td>
      </tr>
      <tr>
        <th></th>
        <td>
          <button name = 'btnCrear' id= 'btnCrear' class = 'btn' >Crear / Editar Cuenta</button>
          
        </td>
      </tr>
    </table>
  </div>
  <div id="tabProductos">
    <div class="nav">
      <table border = '0' width = '200' class = 'nuevaForma'>
        <tr>
          <th>Cuenta</th>
        </tr>
        <tr>
          <th id='celdaCuenta'></th>
        </tr>
        <tr>
          <th>Productos</th>
        </tr>
        <tr>
          <td>
            <SELECT size = '10' name = 'cmbPdtos' id = 'cmbPdtos'  style="position: relative; width:200; height:200">
              <option selected>Nuevo Producto</option>
            </SELECT>
          </td>
        </tr>
      </table>
    </div>
    <div class="contenido">
      <table border= '0' width="750" class = 'nuevaForma'>
        <tr>
          <td width="100">Producto</td>
          <td colspan="3">
            <input type="text" name="txtNombProducto" id="txtNombProducto" size="30" maxlength="55"  placeholder = 'Nombre (Máx. 30 caracteres)' >                  
          </td>
          <td  width="100">Correlat Cuenta</td>
          <td><input name="txtCorrelativo" id="txtCorrelativo" size="5" readonly> (Solo lectura)</td>
        </tr>
        <tr>
          <td  width="100">M3 Facturable</td>
          <td  width="100"><input name="txtM3Facturable" id="txtM3Facturable" size="5"></td>
          <td  width="100">Puntos</td>
          <td  width="100"><input name="txtPuntos" id="txtPuntos" size="5" ></td>
          <td  width="100">Zona</td>
          <td>
            <select name = 'txtZona' id = 'txtZona' >
              <option>-</option>
              <?php
                foreach ($dataZonas as $key => $value) {
                  echo "<option value = '".$value['idDetalle']."'>".$value['descripcion']."</option>";
                }
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td  width="100">Estado</td>
          <td  width="100">
            <select name = 'cmbEstadoProducto' id = 'cmbEstadoProducto' >
              <option>Activo</option>
              <option>Inactivo</option>
            </select>
            
          </td>
          <td>Cuenta Despacho</td>
          <td>
            <select name = 'cmbCuentaDespacho' id = 'cmbCuentaDespacho' >
              <option>-</option>
              <option>1</option>
              <option>0</option>
            </select>           

          </td>
          <td>Super Cuenta</td>
          <td>
            <inputsize="35" maxLength = '30' >

            <select  name="txtSuperCuenta" id="txtSuperCuenta"  >
            <?php

              foreach ($dataSuperCuentas as $key => $value) {
                echo "<option>".$value["superCuenta"]."</option>";
              }


            ?>

            </select>


          </td>
        </tr>
        <tr>
          <td colspan="6" >
            <div id="tabPdto" class = 'cliCuenta'>
              <ul>
                <li><a href="#tabPdtoServicio">Datos Producto</a></li>
                <li><a href="#tabPdtoPersonal">Datos Personal</a></li>
                <li><a href="#tabPdtoTercero">Datos Tercero</a></li>
                <li><a href="#tabPdtoOtros">Datos Otros</a></li>
              </ul>
              <div id="tabPdtoServicio">
                <table width="700" border = '0'>
                  <tr>
                    <td width="100">Precio Producto</td>
                    <td width="90">
                      <input name="txtPrecServicio" id="txtPrecServicio" size="8" >
                    </td>
                    <td width="100"></td>
                    <td width="90"></td>
                    <td width="130"></td><td ></td>
                  </tr>
                  <tr>
                    <td>Km Esperado</td>
                    <td><input name="txtKmEsperado" id="txtKmEsperado" size="8" ></td>
                    <td>Toler. Km. Esper</td>
                    <td><input name="txtTolerKmEsperado" id="txtTolerKmEsperado" size="8" ></td>
                    <td>Valor Km. Adic.</td>
                    <td><input name="txtValKmAdicional" id="txtValKmAdicional" size="8" ></td>
                  </tr>
                  <tr>
                    <td>Hras Normales</td>
                    <td><input class = 'tiempo' name="txtHrasNormales" id="txtHrasNormales" placeholder="HH:MM:SS" size="8" ></td>
                    <td>Toler. Hras Normal</td>
                    <td><input class = 'tiempo'  name="txtTolerHrasNormal" id="txtTolerHrasNormal"  placeholder="HH:MM:SS" size="8" ></td>
                    <td>Valor Hra Adic</td>
                    <td><input name="txtValHraAdic" id="txtValHraAdic" size="8" ></td>
                  </tr>
                  <tr>
                    <td>Hra Ini Esperado</td>
                    <td><input  class = 'tiempo' name="txtHraIniEsperado" id="txtHraIniEsperado"  placeholder="HH:MM:SS" size="8" ></td>
                    <td>Toler. Hra Ini Esper</td>
                    <td><input  class = 'tiempo' name="txtTolerHraIniEsperado" id="txtTolerHraIniEsperado"  placeholder="HH:MM:SS" size="8" ></td>
                    <td>Valor Adic Hra Ini Esper</td>
                    <td><input name="txtValAdicHraIniEsp" id="txtValAdicHraIniEsp" size="8" ></td>
                  </tr>
                  <tr>  
                    <td>Hra Fin Esperado</td>
                    <td><input  class = 'tiempo' name="txtHraFinEsperado" id="txtHraFinEsperado"  placeholder="HH:MM:SS" size="8" ></td>
                    <td>Toler. Hra Fin Esper</td>
                    <td><input  class = 'tiempo' name="txtTolerHraFinEsperado" id="txtTolerHraFinEsperado"  placeholder="HH:MM:SS" size="8" ></td>
                    <td>Valor Adic Hra Fin Esper</td>
                    <td><input name="txtValAdicHraFinEsp" id="txtValAdicHraFinEsp" size="8" ></td>
                  </tr>
                  <tr>
                    <td>Nro. Auxiliares</td>
                    <td><input name="txtNroAuxiliares" id="txtNroAuxiliares" size="8" ></td>
                    <td></td><td></td>
                    <td>Valor Auxiliar Adic</td>
                    <td><input name="txtValAuxAdic" id="txtValAuxAdic" size="8" ></td>
                  </tr>
                  <tr>
                    <td>Cobrar Peaje</td>
                    <td>
                      <SELECT name = 'cmbPeaje' id = 'cmbPeaje'>
                        <option>No</option>
                        <option>Si</option>
                      </SELECT>
                    </td>
                    <td>Recojo/Dev</td>
                    <td>
                      <SELECT name = 'txtRecojoDev' id = 'txtRecojoDev'>
                        <option>No</option>
                        <option>Si</option>
                      </SELECT>

                    </td>
                    <td></td><td></td>
                  </tr>
                </table>
              </div>
              <div id="tabPdtoPersonal">
                <table width="700" border = '0'>
                  <tr>
                    <td width="85">Valor Conductor</td>
                    <td width="75"><input name="txtValConductor" id="txtValConductor" size="6" ></td>
                    <td width="85">Hra Normal Cond</td>
                    <td width="90">
                      <input  class = 'tiempo' name="txtHraNormalConductor" id="txtHraNormalConductor"  placeholder="HH:MM:SS" size="8" >
                    </td>
                    <td width="60">Toler. Cond.</td>
                    <td width="90">
                      <input  class = 'tiempo' name="txtTolerHraConductor" id="txtTolerHraConductor"  placeholder="HH:MM:SS" size="8" >                      
                    </td>
                    <td width="95">Val Hra Extra Cond</td>
                    <td><input name="txtValHraExtraCond" id="txtValHraExtraCond" size="6" ></td>
                  </tr>
                  <tr>
                    <td>Valor Auxiliar</td>
                    <td><input name="txtValAuxiliar" id="txtValAuxiliar" size="6" ></td>
                    <td>Hra Normal Aux</td>
                    <td>
                      <input  class = 'tiempo' name="txtHraNormalAux" id="txtHraNormalAux"  placeholder="HH:MM:SS" size="8" >
                    </td>
                    <td>Toler. Aux.</td>
                    <td>
                      <input  class = 'tiempo' name="txtTolerHraAux" id="txtTolerHraAux"  placeholder="HH:MM:SS" size="8" >                      
                    </td>
                    <td>Val Hra Extra Aux</td>
                    <td><input name="txtValHraExtraAux" id="txtValHraExtraAux" size="6" ></td>
                  </tr>
                  <tr>
                    <td>Usar Master</td>
                    <td>
                      <SELECT name = 'cmbMaster' id = 'cmbMaster'>
                        <option>No</option>
                        <option>Si</option>
                      </SELECT>
                    </td>
                    <td></td><td></td><td></td><td></td>
                  </tr>
                </table>
              </div>
              <div id="tabPdtoTercero">
                <table width="700" border = '0'>
                  <tr>
                    <td colspan="2" width="190">Valor Unidad Tercero C/conductor</td>
                    <td width="100"><input name="txtValUnidadTercCC" id="txtValUnidadTercCC" size="8" ></td>
                    <td width="90"></td><td width="130"></td><td></td>
                  </tr>
                  <tr>
                    <td colspan="2" >Valor Unidad Tercero S/conductor</td>
                    <td><input name="txtValUnidadTercSC" id="txtValUnidadTercSC" size="8" ></td>
                    <td></td><td></td><td></td>
                  </tr>
                  <tr>
                    <td colspan="2" >Valor Auxiliar Tercero</td>
                    <td><input name="txtValAuxiliarTercero" id="txtValAuxiliarTercero" size="8" ></td>
                    <td></td><td></td><td></td>
                  </tr>
                  <tr>
                    <td  width="100">Hras Normales</td>
                    <td  width="90"><input name="txtHrasNormalTerc" id="txtHrasNormalTerc"   placeholder="HH:MM:SS" size="8" ></td>
                    <td>Toler. Hras Normal</td>
                    <td><input name="txtTolerHrasNormalTerc" id="txtTolerHrasNormalTerc"   placeholder="HH:MM:SS" size="8" ></td>
                    <td>Valor Hra Extra</td>
                    <td><input name="txtValHraExtraTerc" id="txtValHraExtraTerc" size="8" ></td>
                  </tr>
                  <tr>
                    <td></td><td></td><td></td><td></td>
                    <td>Valor Km Adic</td>
                    <td><input name="txtValKmAdicTerc" id="txtValKmAdicTerc" size="8" ></td>
                  </tr>
                </table>
              </div>
              <div id="tabPdtoOtros">

                <div align="left"  style="width: 100%; height: 520px;  overflow:auto">
                  <table id="dataOtros" class="display compact" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>Correl</th>
                      <th>Nombre</th>
                      <th>Tipo</th>
                      <th>Tolerancia</th>
                      <th>Costo</th>
                      <th>Acción</th>

                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>Correl</th>
                      <th>Nombre</th>
                      <th>Tipo</th>
                      <th>Tolerancia</th>
                      <th>Costo</th>
                      <th>Acción</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
                
              </div>
            </div>
          </td>
        </tr>


      </table>
      <button class = 'btn' align = 'right' name = 'btnProducto' id= 'btnProducto' >Crear / Editar Producto</button>
    </div>

  </div>
</div>

  </body>
</html>

<?php
 } else {
 
 } ?>