<?php
   require 'librerias/config.ini.php';

  $usuariosAdminProgram = "";

  foreach ($adminAlertasProgram as $key => $value) {
    if ($usuariosAdminProgram != "") $usuariosAdminProgram .= ",";
    $usuariosAdminProgram .= strtoupper($key);
  }

?>
<html>
<head>
  <script language="JavaScript" type="text/javascript" src="librerias/varios.js"></script>
  <link rel =stylesheet href="librerias/jquery-ui-1.8.9.custom.css" type ="text/css">
  <link rel =stylesheet href="librerias/estilos.css" type ="text/css">
  <link rel =stylesheet href="librerias/DataTables/datatables.min.css" type ="text/css">
  <script language="JavaScript" type="text/javascript" src="librerias/DataTables/datatables.min.js"></script>

  <script>

    $(document).ready(function(){    

      $("#txtFchAnterior" ).datepicker({
          minDate: "-1M 0D",
          maxDate: "-1D",
          dateFormat:"yy-mm-dd" 
      });

      $("#txtFchNueva" ).datepicker({
          minDate: "0D",
          maxDate: "7D",
          dateFormat:"yy-mm-dd" 
      });

      dialogEditar = $( "#dialogEditar" ).dialog({
        autoOpen: false,
        resizable: false,
        height: "470",
        width: 525,
        modal: true,
        
        buttons: {
          "Guardar": addRegistroProgram,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function addRegistroProgram(){
        correlCuenta = nombCuenta = tipoCuenta = idProducto = ""
        auxId = $("#txtIdRegistro").val()
        auxIdCliente = $("#txtIdCliente").val()
        fchDespacho = $('#txtFchDespacho').val()
        hraIniEsperada = $('#txtHrainiEsperada').val()
        //movilAsignado = $('#txtMovilAsignado').val()
        auxCuenta = $('#txtCuenta').val()
        auxProducto = $('#txtProducto').val()
        estadoProgram = $('#cmbEstado').val()
        auxUsuAsignado = $('#txtUsuAsignado').val()
        observacion = $('#txtObservacion').val()
        aux = auxCuenta.split("|")
        correlCuenta = aux[0]
        nombCuenta = aux[1]
        tipoCuenta = aux[2]
        auxConductor = "-"
        auxAuxiliares = ""
        aux = auxUsuAsignado.split("|")
        usuAsignado = aux[0]
        if (tipoCuenta == 'Normal'){
          placa = $('#txtPlaca').val()
          auxConductor = $('#txtConductor').val()
          auxAuxiliares = $('#txtAuxiliares').val()
        } else if (tipoCuenta == 'SoloPersonal'){
          placa = ""
          auxAuxiliares = $('#txtAuxiliares').val()
        } else if (tipoCuenta == 'SoloVehiculo'){
          placa = $('#txtPlaca').val()
        }

        id = auxId.substring(auxId.indexOf("id: ") + 4)
        idCliente = auxIdCliente.substr(0, auxIdCliente.indexOf('-'));
        aux = auxProducto.split("|")
        idProducto = aux[0]
        nombProducto = aux[1]
        precioServ = aux[2]
        idConductor = auxConductor.substr(0, auxConductor.indexOf('-'));
        //alert("*"+id+"*")

        var arrTipoCuenta = ['Normal','SoloPersonal','SoloVehiculo']
        if (idCliente == '') {
          //data.form.find('input[name="idCliente"]').addClass("rojo");
          $("#txtIdCliente").addClass("rojo");
          alert ('El idCliente debe estar lleno');
          return false; 
        } else if (fchDespacho == '') {
          $('#txtFchDespacho').addClass("rojo")
          alert ('La fchDespacho debe estar llena');
          return false;
        } else if (!arrTipoCuenta.includes(tipoCuenta)) {
          $('#txtCuenta').addClass("rojo");
          alert ('El tipo de Cuenta es incorrecto.\nVuelva a elegir la cuenta');
          return false;
        } else if (idProducto == '') {
          $('#txtProducto').addClass("rojo")
          alert ('Elija un Producto');
          return false;
        } else if (tipoCuenta == 'Normal' && idConductor == '') {
          $('#txtConductor').addClass("rojo");
          alert ('Este tipo de Cuenta requiere Conductor');
          return false; 
        } else if ((tipoCuenta == 'Normal' || tipoCuenta == 'SoloVehiculo') && placa == ''){
          $('#txtPlaca').addClass("rojo");
          alert ('Este tipo de Cuenta requiere Placa');
          return false; 
        } else if (tipoCuenta == 'SoloPersonal' && auxAuxiliares == '') {
          $('#txtAuxiliares').addClass("rojo");
          alert ('Este tipo de Cuenta requiere Auxiliares');
          return false; 
        } else if (hraIniEsperada == '' || hraIniEsperada == '00:00:00') {
          $('#txtHrainiEsperada').addClass("rojo");
          alert ('La Hra Inicio Esperada es Obligatoria');
          return false;
        } else if (usuAsignado == '' ) {
          $('#txtUsuAsignado').addClass("rojo");
          alert ('El Usuario Asignado es Obligatorio');
          return false;
        }
        //
        var parametros = {
          "id":id,
          "idCliente":idCliente,
          "fchDespacho":fchDespacho,
          "idConductor":idConductor,
          "auxIdAuxiliares":auxAuxiliares,
          "hraInicioEsperada":hraIniEsperada,
          "correlCuenta":correlCuenta,
          "idProducto":idProducto,
          "tipoCuenta":tipoCuenta,
          "placa":placa,
          "usuAsignado":usuAsignado
        };

        var rpta = "OK"
        //alert("Llego")
        $.ajax({
          data:  parametros,
          url:   './vistas/ajaxVarios.php?opc=validarAntesDeGuardar',
          timeout: 15000,
          type:  'post',
          success:  function (response) {
            var obj = JSON.parse(response);
            //console.log("repuesta",response)
            //console.log("sobre usu asignado",obj.sobreUsuAsignado);
            if (obj.sobreCliente == 'No'){
              alert("Error. Hay un error en el Id del Cliente")
              $("#txtIdCliente").addClass("rojo");
              rpta = "Error";
            }

            if (obj.sobreProducto == 'No'){
              alert("Error.\nHay una inconsistencia con los datos de la Cuenta o de Producto, recargue los datos.")
              $('#txtCuenta').addClass("rojo");
              $('#txtProducto').addClass("rojo")
              rpta = "Error";
            }

            if (obj.sobreConductor == 'No'){
              alert("Error.\nEl conductor ya está involucrado en otro despacho.")
              $('#txtConductor').addClass("rojo");
              rpta = "Error";
            }

            if (obj.sobreAuxiliares == 'No'){
              alert("Error.\nAl menos un auxiliar ya está involucrado en otro despacho el día de hoy en esta misma hora.")
              $('#txtAuxiliares').addClass("rojo");
              rpta = "Error";
            }

            if (obj.sobrePlaca == 'No'){
              alert("Error.\nLa placa ya está involucrada en otro despacho a esa misma hora.")
              $('#txtPlaca').addClass("rojo");
              rpta = "Error";
            }

            if (obj.sobreUsuAsignado != 'Si'){
              alert(obj.sobreUsuAsignado)
              $("#txtUsuAsignado").addClass("rojo");
              rpta = "Error";
            }

            //alert(rpta)
            if (rpta == 'OK'){
              var parametros = {
                "id":id , 
                "idCliente":idCliente , 
                "idProducto":idProducto , 
                "fchDespacho":fchDespacho,
                "hraIniEsperada":hraIniEsperada,
                //"movilAsignado":movilAsignado,
                "nombProducto":nombProducto,
                "precioServ":precioServ,
                "idConductor":idConductor,
                "correlCuenta":correlCuenta,
                "nombCuenta":nombCuenta,
                "tipoCuenta":tipoCuenta,
                "placa":placa,
                "estadoProgram":estadoProgram,
                "auxAuxiliares":auxAuxiliares,
                "observacion":observacion,
                "usuAsignado":usuAsignado
              };

              $.ajax({
                data:  parametros,
                url:   './vistas/ajaxVarios.php?opc=creaEditaRegProgram',
                timeout: 15000,
                type:  'post',
                success:  function (response) {
                  //alert(response)
                  if (response == 1){
                    //$('#mensajeSistema').css('background-color', 'blue');
                    $('#mensajeSistema').addClass('verde');
                    $("#mensajeSistema").html("Se insertó registro de programación de fecha "+fchDespacho + ", hora " + hraIniEsperada)
                  } else {
                    if (response == 2){
                      $('#mensajeSistema').addClass('verde');
                      $("#mensajeSistema").html("Se actualizó el registro de programación de fecha "+fchDespacho + ", hora " + hraIniEsperada)
                    }
                  }
                  
                  $("#dialogEditar").dialog( "close" );
                  $("#txtIdRegistro").val('')
                  $("#txtIdCliente").val('')
                  $('#txtFchDespacho').val('')
                  $('#txtHrainiEsperada').val('')
                  $('#txtMovilAsignado').val('')
                  $('#txtCuenta').val('')
                  $('#txtProducto').val('')
                  $('#cmbEstado').val('EnProceso')
                  $('#txtPlaca').val('')
                  $('#txtConductor').val('')
                  $('#txtAuxiliares').val('')
                  $('#txtObservacion').val('')
                  $('#txtUsuAsignado').val('')
                  t.ajax.reload();
                }
              });
            }        
          }
        })
      }

      dialogPredictivo = $( "#dialogPredictivo" ).dialog({
        autoOpen: false,
        resizable: false,
        height: "250",
        width: 525,
        modal: true,
        buttons: {
          "Guardar": addPredictivoProgram,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }        
      });

      function addPredictivoProgram(){
        filtro = $("#cmbFiltro").val()
        fchAnterior = $("#txtFchAnterior").val()
        idCliente = $('#txtIdCliente2').val()
        fchNueva = $('#txtFchNueva').val()        
        //alert(idProducto)
        var parametros = {
          "filtro":filtro,
          "fchAnterior":fchAnterior , 
          "idCliente":idCliente , 
          "fchNueva":fchNueva 
        }

        if (filtro == '-'){
          $('#cmbFiltro').addClass("rojo");
          alert("Debe elegir una opción para los estados")
          return false;
        } else if (fchAnterior == ''){
          $('#txtFchAnterior').addClass("rojo");
          alert("Debe elegir una fecha para copiar")
          return false;
        }

        if (idCliente == ''){
          if (!confirm("Si no especifica un cliente se copiarán todos.\nPresione Ok si desea continuar")) {
            return false
          }
        }

        if (fchNueva == ''){
          $('#txtFchNueva').addClass("rojo");
          alert("Debe elegir una fecha hacia donde copiar")
          return false;
        }
        
        $.ajax({
          data:  parametros,
          url:   './vistas/ajaxVarios.php?opc=predictivoProgram',
          timeout: 15000,
          type:  'post',
          success:  function (response) {
            //alert(response)
            $("#cmbFiltro").val('-')
            $("#txtFchAnterior").val('')
            $('#txtIdCliente2').val('')
            $('#txtFchNueva').val('')
            $("#dialogPredictivo").dialog( "close" );
            t.ajax.reload();
          }
        });
      }

      dialogEliminar = $( "#dialogEliminar" ).dialog({
        autoOpen: false,
        resizable: false,
        height: 250,
        width: 480,
        modal: true,
        
        buttons: {
          "Eliminar": elimRegProgram,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function elimRegProgram(){
        //Capturar los campos
        idRegistro = $("#txtIdRegistroElim").val()
        //nombreProvee = $("#txtNombreElim").val()
        var parametros = {
          "idRegistro":idRegistro
        };

        $.ajax({
          data:  parametros,
          url:   './vistas/ajaxVarios.php?opc=eliminaRegProgram',
          timeout: 15000,
          type:  'post',
          success:  function (response) {
            if (response == 1){
              $('#mensajeSistema').removeClass('rojo');
              $('#mensajeSistema').addClass('verde');
              $("#mensajeSistema").html("Se ha eliminado el registro "+ idRegistro)
            } else {
              $('#mensajeSistema').removeClass('verde');
              $('#mensajeSistema').addClass('rojo');
              $("#mensajeSistema").html("Error. No se ha podido eliminar el registro "+ idRegistro )
            }
            $("#dialogEliminar").dialog( "close" );
            t.ajax.reload();

          }
        })
      }

      $('#txtIdCliente').autocomplete({
          minLength: 2,
          source : 'clientesAjax.php'
      });

      $('#txtIdCliente2').autocomplete({
          minLength: 2,
          source : 'clientesAjax.php'
      });

      $('#txtFchDespacho').datepicker({
          minDate: "0M 0D",
          maxDate: "0M +7D",
          dateFormat:"yy-mm-dd" 
      });

      $('#txtHrainiEsperada').timepicker();

      $('#txtMovilAsignado').autocomplete({
        minLength: 2,
        source : './vistas/ajaxVarios.php?opc=buscarTelefonos'
      });

      $('#txtPlaca').autocomplete({
        minLength: 2,
        source : './vistas/ajaxVarios.php?opc=buscarPlaca&estado=Activo'
        //source : 'placasAjax.php'
      });

      $('#txtConductor').autocomplete({
        minLength: 2,
        source : './vistas/ajaxVarios.php?opc=buscarConductor&estado=Activo'
        //source : 'conductoresAjax.php'
      });

      $('#txtUsuAsignado').autocomplete({
        minLength: 2,
        source : './vistas/ajaxVarios.php?opc=buscarUsuAsignables'
        //source : 'conductoresAjax.php'
      });

      function split( val ) {
        return val.split( /\n/ );
      }

      function extractLast( term ) {
        return split( term ).pop();
      }


      $( "#txtAuxiliares" )
        .bind( "keydown", function( event ) {
          if ( event.keyCode === $.ui.keyCode.TAB && $( this ).autocomplete( "instance" ).menu.active ) {
            event.preventDefault();
          }
        })
        .autocomplete({
          source: function( request, response ) {
                   $.getJSON( "variosAjax.php?opc=trabajParaAuxiliar", {
                     term: extractLast( request.term )
                   }, response );
              },
          search: function() {
            // custom minLength
            var term = extractLast( this.value );
            if ( term.length < 2 ) {
              return false;
            }
          },
          focus: function() {
            // prevent value inserted on focus
            return false;
          },
          select: function( event, ui ) {
            var terms = split( this.value );
            // remove the current input
            terms.pop();
            // add the selected item
            terms.push( ui.item.value );
            // add placeholder to get the comma-and-space at the end
            terms.push( "" );
            this.value = terms.join( "\n" );
            return false;
          }
        });

      $('#txtIdCliente').on('change', function() {
        elegido = $(this).val()
        //alert(elegido);
        if ( elegido != ""){
          $('#txtCuenta').val("")
          aux = elegido.split("-")
          idCliente = aux[0]
          //alert(idCliente)

          $('#txtCuenta').autocomplete({
            minLength: 2,
            //source : 'clientesAjax.php'
            source : './vistas/ajaxVarios.php?opc=buscarCuenta&idCli='+idCliente
          });
        }       
      });

      $('#txtCuenta').on('change', function() {
        valor = $("#txtIdCliente").val()
        aux = valor.split("-")
        idCliente = aux[0]

        elegido = $(this).val()
        if ( elegido != ""){
          $('#txtProducto').val("")
          aux = elegido.split("|")
          idCuenta = aux[0]
          tipoCuenta = aux[2]

          if (tipoCuenta == "Normal"){
            $("#txtConductor").prop('disabled', false);
            $("#txtAuxiliares").prop('disabled', false);
          } else if(tipoCuenta == 'SoloPersonal'){
            $("#txtConductor").prop('disabled', true);
            $("#txtAuxiliares").prop('disabled', false);
          } else if(tipoCuenta == 'SoloVehiculo'){
            $("#txtConductor").prop('disabled', true);
            $("#txtAuxiliares").prop('disabled', false);
          } else {
            $("#txtConductor").prop('disabled', true);
          }
          
          $('#txtProducto').autocomplete({
            minLength: 2,
            //source : 'clientesAjax.php'
            source : './vistas/ajaxVarios.php?opc=buscarProducto&idCli=' + idCliente + '&idCuenta=' + idCuenta
          });
          
        }       
      });

      $('#data').on( 'click', 'tbody .editar',function(){
        id = t.row( $(this).parents('tr')).data()["0"];
        idCliente = t.row( $(this).parents('tr')).data()["1"];
        conductor = t.row( $(this).parents('tr')).data()["8"];
        auxiliares = t.row( $(this).parents('tr')).data()["9"];
        //movilAsignado = t.row( $(this).parents('tr')).data()["4"];

        var parametros = {
          "id":id,
        };

        $.ajax({
          data:  parametros,
          url:   './vistas/ajaxVarios.php?opc=buscarRegProgram',
          timeout: 15000,
          type:  'post',
          success:  function (response) {
              var obj = JSON.parse(response);

              $('#txtIdRegistro').val("id: "+id)
              $("#txtIdCliente").val(idCliente)
              $('#txtFchDespacho').val(obj.fchDespacho)
              $('#txtHrainiEsperada').val(obj.hraInicioEsperada)
              $('#txtMovilAsignado').val(obj.movilAsignado)
              $('#txtCuenta').val(obj.correlCuenta + '|' + obj.cuenta + '|' + obj.tipoCuenta)
              $('#txtProducto').val(obj.idProducto + '|' + obj.nombProducto + '|' + obj.valorServicio)
              $('#cmbEstado').val(obj.estadoProgram)
              $('#txtPlaca').val(obj.placa)
              $('#txtObservacion').val(obj.observacion)
              $('#txtConductor').val(conductor)
              $('#txtAuxiliares').val(auxiliares)
              $('#txtUsuAsignado').val(obj.usuAsignado)

              dialogEditar.dialog( "open" );
          }  
        });
      });

      $('#data').on( 'click', 'tbody .eliminar',function(){
        id = t.row( $(this).parents('tr')).data()["0"];
        idCliente = t.row( $(this).parents('tr')).data()["1"];
        fchDespacho = t.row( $(this).parents('tr')).data()["2"];  
        $('#txtIdRegistroElim').val(id)
        $('#txtIdClienteElim').val(idCliente)
        $('#txtFchDespachoElim').val(fchDespacho)
        dialogEliminar.dialog( "open" );
      });

      $("#imgMas").click(function () {
        $('#txtIdRegistro').val("id: Lo genera el sistema")
        dialogEditar.dialog( "open" );
      });

      $("#imgPredictivo").click(function () {
        dialogPredictivo.dialog( "open" );
      });

      $("#imgSubir").click(function (){
        <?php
          $arrAdmin = explode(",",$usuariosAdminProgram);
          if (in_array(strtoupper($_SESSION['usuario']), $arrAdmin))  $esAdmin = "Si"; else $esAdmin = "No";
        ?>
        //alert('<?php  echo $esAdmin;  ?>')
        if ('<?php echo $esAdmin; ?>' == 'Si') esAdmin = "Si"; else esAdmin = "No";

        if (esAdmin == 'Si'){
          var r = confirm("Usted tiene nivel Administrador.\nPresione Aceptar si desea procesar los Confirmados de TODOS\ny presione Cancelar si desea procesar solo los PROPIOS");
          if (r != true) esAdmin = "No";
        }

        var r = confirm("Solo subirán los de Estado Confirmado, con Fech mayor o igual a la actual y creados por usted");
        if (r == true) {
          var parametros = {
            "porcIgv":'<?php echo $porcIgv;  ?>',
            "email":'<?php echo $_SESSION['email'];  ?>',
            "esAdmin":esAdmin
          };

          $.ajax({
            data:  parametros,
            url:   './vistas/ajaxVarios.php?opc=subirAProduccion2',
            timeout: 15000,
            type:  'post',
            success:  function (response) {
              t.ajax.reload();
              if (response == 1){
                //No devuelve 1
                //$('#programacion').jtable('reload');
              }
            }
          })
        } else {
          alert("Usted ha cancelado la operación !!");
        } 

      });

      // Setup - add a text input to each footer cell
      $('#data tfoot th').each( function () {
          //var title = $('#data thead th').eq( $(this).index() ).text();
          var title = $('#data thead th').eq( $(this).index() ).text();
          if (title != 'Acción')
            title = title.replace(" ", "_")
            //$(this).html('<input type="text" placeholder=" ' + title + '" style="width:100%"  />');
            $(this).html('<input type="text" placeholder=" ' + title + '" style="width:100%"  id  = "'+ title +'"  name  = "'+ title +'" />');
      } );

      var t = $('#data').DataTable({
        "scrollCollapse": true,
        "lengthMenu": [ 12, 24, 100 ],
        "oLanguage": {
        "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ entradas",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
        "sZeroRecords": "No hay registros coincidentes",
        },
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "columns": [
          null,
          {className:"idCliente"},
          {className:"fchDespacho"},
          {className:"hraInicioEsperada"},
          {className:"movilAsignado"},
          {className:"cuenta"},
          {className:"producto"},
          {className:"placa"},
          {className:"idConductor"},
          {className:"idAuxiliares"},
          {className:"estadoProgram"},
          null,
          null,
        ],
        
        "columnDefs": [
          {"width": "22", "targets": [0,2,3,4,7]},
          {"width": "65", "targets": [10,11]},
          {"width": "140", "targets": [1,5,6]},
          {"width": "180", "targets": [8]},
          {"width": "250", "targets": [9]},

          {"targets": [ 0 ],"visible": false,"searchable": false},
        ],

        "order": [[ 2, "desc" ]],

        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          if ( aData[10] == "Confirmado" ) {
            $('td', nRow).css('background-color', '#CCFF99' );
          } else if ( aData[10] == "Procesado" ){
            $('td', nRow).css('background-color', '#ff7070');
          }
        },
        //"ajax": "vistas/ladoServidor/ladoServidor.php?opcion=listarProgram"
        "ajax":{
              "url": "vistas/ladoServidor/ladoServidor.php?opcion=listarProgram",
              "data": function ( d ) {
                d.usuario = '<?php echo $_SESSION['usuario']  ?>';
                d.admin = '<?php echo $usuariosAdminProgram;  ?>';       
              }
        },
        
      });

      t.columns().eq( 0 ).each( function ( colIdx ) {
        $( 'input', t.column( colIdx ).footer() ).on( 'keyup change', function(){
          t
            .column( colIdx )
            .search( this.value )
            .draw();
        });
      });

      $('#data_filter').hide()

    })

  function descargarExcel(){
    //alert("No implementado")
    //document.frmFiltro.action= "index.php?controlador=proveevehiculo&accion=descargarExcelProveeVehiculo"
    document.frmFiltro.action= "vistas/descargarExcelProgramacionPrevia.php"
    document.frmFiltro.method= 'POST';
    document.frmFiltro.submit(); 
  }


  </script>  
  
</head>
<body>
   <?php require_once 'barramenu.php';  ?>

  <table class = 'fullRD' >
    <tr>
      <th  class = "pagina"  align="center" colspan = '6' >
      <img border="0" src="imagenes/moy.png" width="23" height="23" align="right" onclick =  "abrirventana('index.php?controlador=aplicacion&accion=verprincipal') ">
      <img class="sinTitulo" align="right" href="#" title="Descargar |en Formato Excel" border="0" src="imagenes/hoja.png" width="25" height="25" align="right" onclick = "descargarExcel() " >
      <?php if($_SESSION["ingreso"] == "Si" ) { ?>
        <img class="sinTitulo" border="0" id = 'imgMas' name = 'imgMas' src="imagenes/mas.png" title="Crear nuevo registro"  width="24" height="24" align="right" >
        <img class="sinTitulo" border="0" id = 'imgSubir' name = 'imgSubir' src="imagenes/subir.png" title="Subir a Producción"  width="24" height="24" align="right" >
        <img class="sinTitulo" border="0" id = 'imgPredictivo' name = 'imgPredictivo' src="imagenes/predictivo.png" title="Predictivo Programación"  width="24" height="24" align="right" >

      <?php } ?>
      PROGRAMACION PREVIA
      </th>
    </tr>
    </table>

    <div id="dialogEditar" title="Crear / Editar Programación">
      <div style="padding: 5px;">
        <form>
          <table width="480px">
            <tr>
              <td colspan="2">
                <input style="padding: 5px; width: 470px" id="txtIdRegistro" name="txtIdRegistro" type="text" readonly tabIndex = -1 >
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <input style="padding: 5px; width: 470px" id="txtIdCliente" name="txtIdCliente" type="text" tabindex = '1' placeholder="Id Cliente. Digite los primeros caracteres del nombre" >            
              </td>
            </tr>
            <tr>
              <td>
                <input style="padding: 5px; width: 100px" id="txtFchDespacho" name="txtFchDespacho" type="text"  placeholder="Fcha Despacho" tabindex = '2'>
              </td>
              <td>
                <input style="padding: 5px; width: 365px" id="txtCuenta" name="txtCuenta" type="text"  placeholder="Digite Cuenta" tabindex = '7'>            
              </td>
            </tr>
            <tr>
              <td>
                <input style="padding: 5px; width: 100px" id="txtHrainiEsperada" name="txtHrainiEsperada" type="text"  placeholder="Hra Inicio" tabindex = '3'>            
              </td>
              <td>
                <input style="padding: 5px; width: 365px" id="txtProducto" name="txtProducto" type="text"  placeholder="Digite Producto" tabindex = '8'>            
              </td>
            </tr>
            <tr>
              <td>
                <input style="padding: 5px; width: 100px" id="txtPlaca" name="txtPlaca" type="text"  placeholder="Placa" tabindex = '5'>
              </td>
              <td>
                <input style="padding: 5px; width: 365px" id="txtConductor" name="txtConductor" type="text"  placeholder="Digite Conductor" tabindex = '9'>
              </td>
            </tr>
            <tr>
              <td>
                <select name = 'cmbEstado' id = 'cmbEstado' style="position: relative; width:100; height:28" tabindex = '6'>
                  <option>EnProceso</option>
                  <option>Confirmado</option>
                  <option>Cancelado</option>
                </select>
              </td>
              <td rowspan="3">
                <textarea rows="3" name="txtAuxiliares" id="txtAuxiliares" cols="46"  placeholder=" Ingrese Auxiliares" tabindex = '10'></textarea>
              </td>
            </tr>
            <tr>
              <td> </td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td></td>
              <td>
                <input style="padding: 5px; width: 360px" id="txtUsuAsignado" name="txtUsuAsignado" type="text"  placeholder="Usuario Asignado" tabindex = '5'>     
              </td>
            </tr>
            <tr>
              <td colspan = '2'>
                <textarea name="txtObservacion" id="txtObservacion" placeholder="Observación" rows="4" cols="61"></textarea>
              </td>
            </tr>
          </table>
          <input type='hidden' name= 'correlOtro' id= 'correlOtro' value = '' >
          <input type='hidden' name= 'uAsig' id= 'uAsig' value = '' >
        </form>
      </div>
    </div>

    <div id="dialogPredictivo" title="Predictivo Programación">
      <div style="padding: 5px;">
        <form>
          <fieldset>
            <label>
              <select name="cmbFiltro" id="cmbFiltro" style="padding: 5px; font-size: 13px; width: 250px;">
                <option value = '-'>Elija el Estado:</option>
                <option value = 'Todos'>Todos</option>
                <option value = 'EnProceso, Confirmado y Procesado'>
                Solo EnProceso, Confirmado y Procesado</option>
                <option value = 'Confirmado'>Solo Confirmado</option>
                <option value = 'Procesado'>Solo Procesado</option>
              </select>
            </label><br/>
            <label>
              <input style="padding: 5px; width: 250px" id="txtFchAnterior" name="txtFchAnterior" type="text"  placeholder="Ingrese la Fecha Anterior" >
            </label><br/>
            <label>
              <input style="padding: 5px; width: 400px" id="txtIdCliente2" name="txtIdCliente2" type="text" placeholder="Id Cliente. Digite los primeros caracteres del nombre" >        
            </label><br/>
            <label>
              <input style="padding: 5px; width: 250px" id="txtFchNueva" name="txtFchNueva" type="text"  placeholder="Ingrese la Nueva Fecha" >
            </label>
          </fieldset>
          <input type='hidden' name= 'correlOtro' id= 'correlOtro' value = '' >
        </form>
      </div>
    </div>

    <div id="dialogEliminar" title="Eliminar Programación">
      <div style="padding: 5px;">
        <table border="1" width="450" id="table1">
          <tr>
            <td width="116">Id</td>
            <td>
              <input type="text" name="txtIdRegistroElim" id="txtIdRegistroElim" size="20" maxlength="20" readonly>
            </td>
          </tr>
          <tr>
            <td>Cliente</td>
            <td>
              <input type="text" name="txtIdClienteElim" id="txtIdClienteElim" size="50" maxlength="80"  readonly>
            </td>
          </tr>
      <tr>
      <td>Fch. Despacho</td>
      <td><input type="text" name="txtFchDespachoElim" id="txtFchDespachoElim" size="50" maxlength="80"  readonly></td>
      </tr>
      </table>
    </div>
    </div>

    <div id="mensajeSistema" align="left"  style="width: 100%; " ></div>

    <div align="left"  style="width: 100%; height: 620px;  overflow:auto">
      <form name = 'frmFiltro' id = 'frmFiltro' method ='POST'>
        <table id="data" class="display compact" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Id</th>
              <th>Cliente</th>
              <th>Fch Despacho</th>
              <th>Hra Ini Esperada</th>
              <th>Movil Asignado</th>
              <th>Cuenta</th>
              <th>Producto</th>
              <th>Placa</th>
              <th>Conductor</th>
              <th>Auxiliares</th>
              <th>Estado</th>
              <th>Usuario</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>Id</th>
              <th>Cliente</th>
              <th>Fch Despacho</th>
              <th>Hra Ini Esperada</th>
              <th>Movil Asignado</th>
              <th>Cuenta</th>
              <th>Producto</th>
              <th>Placa</th>
              <th>Conductor</th>
              <th>Auxiliares</th>
              <th>Estado</th>
              <th>Usuario</th>
              <th>Acción</th>
            </tr>
          </tfoot>
        </table>
      </form>
    </div>
    
</body>
</html>
