<?php
  require_once 'librerias/config.ini.php';

?>

<html>
<head>
  <script language="JavaScript" type="text/javascript" src="librerias/varios.js"></script>
  <link rel =stylesheet href="librerias/jquery-ui-1.8.9.custom.css" type ="text/css">
  <link rel =stylesheet href="librerias/estilos.css" type ="text/css">
  <link rel =stylesheet href="librerias/DataTables/datatables.min.css" type ="text/css">
  <script language="JavaScript" type="text/javascript" src="librerias/DataTables/datatables.min.js"></script>
  <script language="JavaScript" type="text/javascript" src="librerias/jquery-datatables-checkboxes-1.2.11/js/dataTables.checkboxes.min.js"></script>
  <script type="text/javascript" src="librerias/chartjs/graficos.js"></script>
  <script type="text/javascript" src='librerias/Chart.js-2.9.4/dist/Chart.min.js'></script>

  <link href="librerias/select2-4.0.13/css/select2.min.css" rel="stylesheet" />
  <script src="librerias/select2-4.0.13/js/select2.min.js"></script>

  <script src="https://www.gstatic.com/firebasejs/9.8.1/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.8.1/firebase-database-compat.js"></script>
  <script src ="librerias/firebase/config.js"></script>   
  <style>

    *{padding:0px; margin:0px;}
    .ui-autocomplete{
      position:relative;
      cursor:default;
      z-index:9999 !important;     //Where 9999 is just an arbitrary number superior to your form's z-indexes
    }

    #contenedorPhotoMain{
      width: 100%;
      background-color: #2156AB;
      position:absolute; 
      bottom:0; 
      left:0;
      text-align: center;
    }

    .button {
      border: none;
      color: white;
      padding: 8px 20px;
      text-align: center;
      width: 90px;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      margin: 4px 2px;
      cursor: pointer;
      color: #000000;
      background-color: #ffffff;
      border-radius: 6px;
      border: 2px solid #739AD8;
    }
    .button:hover{
        color: #ffffff;
        background-color: #2156AB;
    }

    .resultado{ border-style: solid; vertical-align: top; border-color:black; border-width: 1px;}

    .select2-selection--multiple {padding:0px !important;}

  </style>

  <script>

    function paramWindow(idProgramacion,idPunto,pos){
      abrirventanaflotantegrande("./vistas/nuevaFotoDespachoPunto.php?varidProg="+idProgramacion+"&varidPun="+idPunto+"&varposFoto="+pos);
    }

    $(document).ready(function(){
      $.post("vistas/ajaxVarios_v2.php", {opc: 'puntosEstados'}, function(data){
        $("#cmbNuevoPuntoEstado").html(data);
      });

      $('#cmbValReten').select2({
        allowClear: true,
        placeholder: 'Retenes ...',
      });

      $('#cmbCFReten').select2({
        allowClear: true,
        placeholder: 'Retenes ...',
      });

      $("#cmbValReten").change(function(){
        aux = $("#cmbValReten").val()
        //nroAuxReten = 0
        if(aux === null){
          nroAuxReten = 0
        } else {
          nroAuxReten = aux.length
        }
        //console.log("tam:", nroAuxReten)
        //alert(nroAuxReten)
        nroAuxTotal = $("#txtValAuxTotal").val()
        //alert("prueba")
        nroAuxEsperado = $("#txtValAuxEsperados").val()
        costoAuxAdic = $("#txtValValorAuxAdic").val()
        if( nroAuxTotal - nroAuxReten - nroAuxEsperado > 0 ){
          nroAuxAdic = nroAuxTotal - nroAuxReten - nroAuxEsperado
        } else {
          nroAuxAdic = 0
        }

        costoTotAuxAdic = nroAuxAdic * costoAuxAdic

        $("#txtValAuxReten").val(nroAuxReten)
        $("#txtValAuxNoReten").val( nroAuxTotal - nroAuxReten )
        $("#txtValAuxAdicional").val(nroAuxAdic)
        $("#txtValTotAuxAdic").val(costoTotAuxAdic)

        //alert(costoTotAuxAdic)


        
        //nroRetenes = 
        //alert("Refrescar")
        /*$("#cmbNuevoPuntoEstado option:selected").each(function () {
          elegido=$(this).val();
          $.post("vistas/ajaxVarios_v2.php", { opc: "puntosSubestados", elegido: elegido}, function(data){
            $("#cmbNuevoPuntoSubestado").html(data);
          });
        });*/
      })


      

      $("#cmbNuevoPuntoEstado").change(function(){
        $("#cmbNuevoPuntoEstado option:selected").each(function () {
          elegido=$(this).val();
          $.post("vistas/ajaxVarios_v2.php", { opc: "puntosSubestados", elegido: elegido}, function(data){
            $("#cmbNuevoPuntoSubestado").html(data);
          });
        });
      })

      $("#txtValCuenta").change(function(){
        $("#txtValCuenta option:selected").each(function () {
          auxCliente = $("#txtValCliente").val()
          var arrayAux = auxCliente.split("-")
          idCliente = arrayAux[0]
          //alert(idCliente)
          idCuenta=$(this).val();
          //alert(idCuenta)
          $.post("vistas/ajaxVarios_v2.php", { opc: "ctaPdtos", idCuenta: idCuenta, idCliente: idCliente}, function(data){
            $("#txtValProducto").html(data);
          });
        });
      })

      $('#txtEditaPuntoDist').autocomplete({
        minLength: 2,
        source : './vistas/ajaxVarios.php?opc=distritos'
        //source : 'placasAjax.php'
      });

      $('#txtValPlaca').autocomplete({
        minLength: 2,
        source : './vistas/ajaxVarios.php?opc=buscarPlaca&estado=Activo'
        //source : 'placasAjax.php'
      });

      $('#txtCFplaca').autocomplete({
        minLength: 2,
        source : './vistas/ajaxVarios.php?opc=buscarPlaca&estado=Activo'
        //source : 'placasAjax.php'
      });

      $('#txtCFhraInicio').timepicker({
        timeFormat: 'HH:mm:ss',
      });
      $('#txtCFhraFinCliente').timepicker({
        timeFormat: 'HH:mm:ss',
      });
      $('#txtCFhraFin').timepicker({
        timeFormat: 'HH:mm:ss',
      });

      $('#txtEditaHraLlegada').timepicker({
        timeFormat: 'HH:mm:ss',
      });

      $('#txtEditaHraSalida').timepicker({
        timeFormat: 'HH:mm:ss',
      });

      $("#txtCFfchFinCli").datepicker({
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        maxDate: "0M +0D",
        dateFormat:"yy-mm-dd" 
      }); 

      $("#txtCFfchFin").datepicker({
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        maxDate: "0M +0D",
        dateFormat:"yy-mm-dd" 
      });

      $("#txtValFchFinCli").datepicker({
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        maxDate: "0M +0D",
        dateFormat:"yy-mm-dd" 
      });

      $("#txtValFchFin").datepicker({
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        maxDate: "0M +0D",
        dateFormat:"yy-mm-dd" 
      });


      $('#txtValHraInicioBase').timepicker({
        timeFormat: 'HH:mm:ss',
      });

      $('#txtValHraInicio').timepicker({
        timeFormat: 'HH:mm:ss',
      })

      $('#txtValHraFinCliente').timepicker({
        timeFormat: 'HH:mm:ss',
      })

      $('#txtValHraFin').timepicker({
        timeFormat: 'HH:mm:ss',
      })


      //function detalles(fchDespacho, correlativo){
      function detalles(idProgramacion, fchDespacho, correlativo, estado){
        //alert("Las tablas se van a refrescar")

        setTimeout(function(){
          $("#dataPuntos").dataTable().fnDestroy();
          var tP = $('#dataPuntos').DataTable({
            "dom": '<"toolbar">frtip',
            "scrollCollapse": true,
            "lengthMenu": [ 10, 30, 500 ],
            "oLanguage": {
              "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ entradas",
              "sLengthMenu": "Mostrar _MENU_ registros",
              "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
              "sZeroRecords": "No hay registros coincidentes",
            },
            "responsive": true,
            "processing": true,
            "serverSide": true,
            "ajax":{
              "url" :  "vistas/ladoServidor/ladoServidor.php?opcion=despachoPuntos&idProgramacion="+idProgramacion + "&estadoDesp=" + estado,
             },
            "columnDefs": [
              {"width": "40", "targets": [0,1,2,9,10,11,12,13]},
              {"width": "70", "targets": [3,4, 5,7,8]},
              {"width": "110", "targets": [6]},
              {"width": "200", "targets": [15]},
              {"targets": [-2,-3], "visible": false },
              {"className": "idPunto", "targets": [0] },
              {"className": "tipoPunto", "targets": [2] },
              {"className": "nombComprador", "targets": [3] },
              {"className": "distrito", "targets": [4] },
            ],
            'order': [[0, 'asc'], [1, 'asc']]
          });

          $('#dataPuntos_filter').hide()

          $("#infPuntos div.toolbar").addClass("fg-toolbar ui-widget-header ui-helper-clearfix ui-corner-tl ui-corner-tr")
          $("#infPuntos div.toolbar").css({"padding":"5px"});
          if(estado == "Concluido"){
            $("#infPuntos div.toolbar").html("<b>Puntos Asignados de:</b> Id Programación: "+idProgramacion+" Fch. Despacho: "+ fchDespacho + " Correlativo" + correlativo );
          } else {
            $("#infPuntos div.toolbar").html("<b>Puntos Asignados de:</b> Id Programación: "+idProgramacion+" Fch. Despacho: "+ fchDespacho + " Correlativo" + correlativo  + " <img align = 'right' class = 'icoMasPunto' fchDespacho="+ fchDespacho +" correlativo ="+ correlativo + " idProgramacion =" + idProgramacion  + " estadoDesp = " + estado +" src='imagenes/mas.png' width='18' height= '18'>");
          }
          

          $('#infPuntos div.toolbar').on( 'click', '.icoMasPunto', function(){
            var fchDespacho = $(this).attr("fchDespacho")
            var correlativo = $(this).attr("correlativo")
            var estadoDesp =  $(this).attr("estadoDesp")  
            $("#fchDespachoNvoPunto").val(fchDespacho)
            $("#correlativoNvoPunto").val(correlativo)
            $("#idProgramacionNvoPunto").val(idProgramacion)
            $("#estadoDespNvoPunto").val(estadoDesp)
            dlgNuevoPunto.dialog("open")
          });

          $.post("vistas/ajaxVarios_v2.php", {opc: 'puntosEstados'}, function(data){
            $("#cmbEditaPuntoEstado").html(data);
          });

          $("#cmbEditaPuntoEstado").change(function () {
            $("#cmbEditaPuntoEstado option:selected").each(function () {
              elegido=$(this).val();
              $.post("vistas/ajaxVarios_v2.php", { opc: "puntosSubestados", elegido: elegido}, function(data){
                $("#cmbEditaPuntoSubestado").html(data);
              });
            });
          })

          $('#dataPuntos').on( 'click', '.editar', function(){
            var idPunto = $(this).attr("idPunto")
            var estadoDesp = $(this).attr("estadoDesp")
            $("#estadoDespEditatPto").val(estadoDesp)
            /*var correlPunto = $(this).attr("correlPunto")*/
            //alert(idPunto)
            var param = {
              "idPunto":idPunto,
              //"correlativo":correlativo,
              //"correlPunto":correlPunto
            }
            $.ajax({
              data:param,
              url: './vistas/ajaxVarios_v2.php?opc=buscarDespPuntos',
              timeout:15000,
              type: 'post',
              success: function(response){
                var obj = JSON.parse(response);
                $("#cmbEditaTipoPunto").val(obj.tipoPunto)        
                $("#txtEditaPuntoNombCli").val(obj.nombComprador)
                $("#txtEditaPuntoDist").val(obj.idDistrito + '-' + obj.distrito)

                $("#cmbEditaPuntoEstado").val(obj.estado)

                $.post("vistas/ajaxVarios_v2.php", { opc: "puntosSubestados", elegido: obj.estado}, function(data){
                  $("#cmbEditaPuntoSubestado").html(data);
                  $("#cmbEditaPuntoSubestado").val(obj.subEstado)             
                });
                $("#txtEditaObservacion").val(obj.observacion)

                $("#txtEditaHraLlegada").val(obj.hraLlegada)
                $("#txtEditaHraSalida").val(obj.hraSalida)
                $("#idPuntoEdita").val(idPunto)
                dlgEditaPunto.dialog( "open" );
              }
            })
          });

          $('#dataPuntos').on( 'click', '.eliminar', function(){
            var idPunto = $(this).attr("idPunto")
            var nombComprador =  $(this).parent().parent().find(".nombComprador").text()
            var distrito =  $(this).parent().parent().find(".distrito").text()
            var tipoPunto =  $(this).parent().parent().find(".tipoPunto").text()
            $("#txtEliminaIdPunto").val(idPunto)
            $("#txtEliminaPuntoTipo").val(tipoPunto)
            $("#txtEliminaPuntoNombCli").val(nombComprador)
            $("#txtEliminaPuntoDist").val(distrito)
            dlgEliminaPunto.dialog( "open" );
          });

          $('#dataPuntos').on( 'click', '.traspasar', function(){
            var idPunto = $(this).attr("idPunto")
            var tipoPunto =  $(this).parent().parent().find(".tipoPunto").text()
            var nombComprador =  $(this).parent().parent().find(".nombComprador").text()
            var distrito =  $(this).parent().parent().find(".distrito").text()

            $.post("vistas/ajaxVarios_v2.php", {opc: 'despachosDestinos', idPunto: idPunto }, function(data){
              $("#cmbTraspasarDespachoDestino").html(data);
            });

            $("#txtTraspasaIdPunto").val(idPunto)
            $("#txtTraspasaPuntoTipo").val(tipoPunto)
            $("#txtTraspasaPuntoNombCli").val(nombComprador)
            $("#txtTraspasaPuntoDist").val(distrito)
            dlgTraspasarPunto.dialog( "open" );
          });

          $('#dataPuntos').on( 'click', '.verFotos', function(){
            var idProgramacion = $(this).attr("idProgramacion") 
            var idPunto = $(this).attr("idPunto")
            img01  = 'imagenes/data/puntosdespacho/'+ (1*idProgramacion)+"-" + ( 1*idPunto )+ "_1.jpg";
            img02  = 'imagenes/data/puntosdespacho/'+ (1*idProgramacion)+"-" + ( 1*idPunto )+ "_2.jpg";
            img03  = 'imagenes/data/puntosdespacho/'+ (1*idProgramacion)+"-" + ( 1*idPunto )+ "_3.jpg";
            img04  = 'imagenes/data/puntosdespacho/'+ (1*idProgramacion)+"-" + ( 1*idPunto )+ "_4.jpg";
            var cadena = ""
            $.ajax({ url:img01, type:'HEAD', success: function() {
              cadena = cadena +  "<img  src='"+img01+"' style='width:190px;float:left;height:240px;'>"
              setTimeout(function(){
                $.ajax({ url:img02, type:'HEAD', success: function() {
                  cadena = cadena +  "<img src='"+img02+"' style='width:190px;float:left;height:240px;'>"
                } });
              }, 200);
            } });

            setTimeout(function(){
              $.ajax({ url:img03, type:'HEAD', success: function() {
                cadena = cadena +  "<img src='"+img03+"' style='width:190px;float:left;height:240px;'>"
              } });
            }, 400);

            setTimeout(function(){
              $.ajax({ url:img04, type:'HEAD', success: function() {
                cadena = cadena +  "<img src='"+img04+"' style='width:190px;float:left;height:240px;'>"
              } });
            }, 600);

            setTimeout(function(){
                //cadena = cadena + '<br><button onClick="paramWindow('+1*idProgramacion+','+1*idPunto+')">Click me</button>'
                cadena = cadena + '<div id="contenedorPhotoMain">'+ '<button class="button" onClick="paramWindow('+1*idProgramacion+','+1*idPunto+','+1+')">FOTO 1</button>'+ '<button class="button" onClick="paramWindow('+1*idProgramacion+','+1*idPunto+','+2+')">FOTO 2</button>'+ '<button class="button" onClick="paramWindow('+1*idProgramacion+','+1*idPunto+','+3+')">FOTO 3</button>'+ '<button class="button" onClick="paramWindow('+1*idProgramacion+','+1*idPunto+','+4+')">FOTO 4</button>'+ '</div>' 


                $("#dlgVerFotos").html(cadena);
                dlgVerFotos.dialog( "open" );
            }, 800);
          });

          $("#dataTrip").dataTable().fnDestroy();
          var tTrip = $('#dataTrip').DataTable({
            "dom": '<"toolbar">frtip',
            "scrollCollapse": true,
            "lengthMenu": [ 10, 30, 500 ],
            "oLanguage": {
              "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ entradas",
              "sLengthMenu": "Mostrar _MENU_ registros",
              "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
              "sZeroRecords": "No hay registros coincidentes",
            },
            "responsive": true,
            "processing": true,
            "serverSide": true,
            "ajax":{
              "url" :  "vistas/ladoServidor/ladoServidor.php?opcion=listarPersonal&fchDespacho="+fchDespacho +  "&admin="+'<?php echo $_SESSION['admin']; ?>'+ "&edicion="+'<?php echo $_SESSION['edicion']; ?>'  + "&correlativo="+ correlativo + "&estadoDesp=" + estado,
             },
            "columnDefs": [
              {"width": "15", "targets": [1]},
              {"width": "20", "targets": [0]},
              {"width": "40", "targets": [2,4,5,7]},
              {"width": "50", "targets": [6]},
              {"width": "200", "targets": [3]},
              {"width": "350", "targets": [8]},
              {"className": "nombTrab", "targets": [3] },
              {"className": "tipoRol", "targets": [4] },
            ],
            'order': [[0, 'desc']]                
          });

          $('#dataTrip_filter').hide()

          $("#infTrip div.toolbar").addClass("fg-toolbar ui-widget-header ui-helper-clearfix ui-corner-tl ui-corner-tr")
          $("#infTrip div.toolbar").css({"padding":"5px"});
          if(estado == 'Concluido'){
            $("#infTrip div.toolbar").html("<b>Tripulación de:</b> Id Programación: "+idProgramacion+" Fch. Despacho: "+ fchDespacho + " Correlativo: " + correlativo );

          } else {
            $("#infTrip div.toolbar").html("<b>Tripulación de:</b> Id Programación: "+idProgramacion+" Fch. Despacho: "+ fchDespacho + " Correlativo: " + correlativo +  "<img align = 'right' class = 'icoMasTrip' fchDespacho="+ fchDespacho +" correlativo ="+ correlativo+" src='imagenes/mas.png' width='18' height= '18'>");

          }

          $('#infTrip div.toolbar').on( 'click', '.icoMasTrip', function(){
            var fchDespacho = $(this).attr("fchDespacho")
            var correlativo = $(this).attr("correlativo")
            $("#fchDespachoNvoTrip").val(fchDespacho)
            $("#correlativoNvoTrip").val(correlativo)
            $("#estadoDespNvoTrip").val(estado)
            dlgNuevoTripulante.dialog("open")
          });
        }, 300);
      }
      ///////////////////
      ///////////////////

      // Setup - add a text input to each footer cell
      $('#dataDespTConcluido tfoot th').each( function (){
        //alert($(this).index() )
        var title = $('#dataDespTConcluido thead th').eq( $(this).index() ).text();
        if (title != 'Acción' ){
          $(this).html('<input type="text" placeholder=" ' + title + '" style="width:100%"  />');                 
        }
      });

      var tValid = $('#dataDespTConcluido').DataTable({
        "dom": '<"toolbar">frtip', 
        "scrollCollapse": true,
        "lengthMenu": [ 10, 30, 500 ],
        "oLanguage": {
        "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ entradas",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
        "sZeroRecords": "No hay registros coincidentes",
        },
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax":{
          "url" :  "vistas/ladoServidor/ladoServidor.php?opcion=listarDespachos&admin="+'<?php echo $_SESSION['admin']; ?>'+ "&edicion="+'<?php echo $_SESSION['edicion']; ?>' + "&estadoDespacho=Validado",
               },
        "columnDefs": [
          {"targets": [0], "visible": false },
          {"width": "20", "targets": [0]},
          {"width": "30", "targets": [3]},
          {"width": "45", "targets": [1,4]},
          {"width": "55", "targets": [2, 5, 6,10, 11, 12]},
          {"width": "140", "targets": [8]},
          {"width": "150", "targets": [9]},
          {"width": "230", "targets": [7]},
          {"className": "idProgramacion", "targets": [1] },
          {"className": "fchDespacho", "targets": [2] },
          {"className": "correlativo", "targets": [3] },
          {"className": "dataCliente", "targets": [7] },
        ],
        'order': [[1, 'desc'], [2, 'asc']]
      });

      tValid.columns().eq( 0 ).each( function ( colIdx ) {
        if (colIdx == 5){
          $( 'select', tValid.column( colIdx ).footer() ).on( 'change', function(){
                      var val = $.fn.dataTable.util.escapeRegex(
                          this.value
                      );
                      tValid
                        .column(colIdx)
                        .search( val, true, false )
                        .draw();
                  });
        } else {
          $( 'input', tValid.column( colIdx ).footer() ).on( 'keyup change', function(){
            tValid
              .column( colIdx )
              .search( this.value )
              .draw();
          });  
        }
      });

      $('#dataDespTConcluido_filter').hide()

      $('#dataDespTConcluido tbody').on( 'click', 'tr', function(){
        if ( $(this).hasClass('selected') ) {
          $(this).removeClass('selected');
        } else {
          tValid.$('tr.selected').removeClass('selected');
          $(this).addClass('selected');
          var idProgramacion = $(this).children('.idProgramacion').html();          
          var fchDespacho = $(this).children('.fchDespacho').html();
          var correlativo = $(this).children('.correlativo').html();
          detalles(idProgramacion, fchDespacho, correlativo, "Concluido")
        }
      });

      $('#dataDespTConcluido tbody').on( 'click', '.descargaInfoDespacho', function(){
        var id = $(this).parent().parent().children('.idProgramacion').html()
        var f = $(this).parent().parent().children('.fchDespacho').html()
        var c = $(this).parent().parent().children('.correlativo').html()
        //alert(id)
        <?php if (PHP_VERSION_ID > 70000){   ?>
          abrirventana('vistas/descargarExcelInfoDespacho.php?id='+id+'&f='+f+'&c='+c)
        <?php  } else {   ?>
          abrirventana('vistas/descargarExcelInfoDespacho5_0.php?id='+id+'&f='+f+'&c='+c)
        <?php } ?>
      });

      $('#dataDespTConcluido').on( 'click', 'tbody .editarDespacho',function(){
        var fchDespacho =$(this).attr("fchDespacho")
        var correlativo =$(this).attr("correlativo")
        var idProgramacion =$(this).attr("id")
        //alert(idProgramacion)
        //dlgValDespacho.dialog( "open" )
          
        //var dataCliente = $(this).parent().parent().children('.dataCliente').html()
        var parametros = {
          "fchDespacho":fchDespacho,
          "correlativo":correlativo,
          "idProgramacion":idProgramacion,
        };

        $.ajax({
          data: parametros,
          url:  './vistas/ajaxVarios_v2.php?opc=buscarInfoParaCerrarForzoso',
          timeout: 15000,
          type:  'post',
          success:  function (response) {
            var obj = JSON.parse(response);
            kmRecorrido = obj.kmFin - obj.kmInicio
            if(kmRecorrido > obj.kmEsperado )
              kmAdicional = kmRecorrido - obj.kmEsperado
            else
              kmAdicional = 0

            if(obj.nroAuxiliaresCuenta < obj.nroAuxSinReten)
              nroAuxAdicionales = obj.nroAuxSinReten - obj.nroAuxiliaresCuenta
            else
              nroAuxAdicionales = 0

            //alert(obj.fchDespachoFinCli)
            $("#txtConcluUsuario").val(obj.usuario)
            $("#txtConcluFchCreacion").val(obj.fchCreacion)
            $("#txtConcluCliente").val(obj.rznCliente)
            $("#txtConcluFchDespacho").val(obj.fchDespacho)
            $("#txtConcluHraInicio").val(obj.hraInicio)
            $("#txtConcluCuenta").val(obj.correl_cuenta)
            $("#txtConcluIdProgramacion").val(obj.idProgramacion)
            $("#txtConcluCorrelativo").val(obj.correlativo)
            $("#txtConcluProducto").val(obj.producto)
            $("#txtConcluPlaca").val(obj.placa)
            $("#txtConcluHraInicioBase").val(obj.hraInicioBase)
            $("#txtConcluKmInicio").val(obj.kmInicio)
            $("#txtConcluKmInicioCliente").val(obj.kmInicioCliente)
            $("#txtConcluHraFinCliente").val(obj.hraFinCliente)
            $("#txtConcluKmFinCliente").val(obj.kmFinCliente)
            $("#txtConcluFchFinCli").val(obj.fchDespachoFinCli)
            $("#txtConcluFchFin").val(obj.fchDespachoFin)
            $("#txtConcluHraFin").val(obj.hraFin)
            $("#txtConcluKmFin").val(obj.kmFin)
            $("#txtConcluEstadoDespacho").val(obj.estadoDespacho)
            $("#txtConcluObservacion").val(obj.observacion)
            $("#txtConcluPuntoOrigen").val(obj.sedeElegida)
            $("#txtConcluLugarFinCli").val(obj.lugarFinCliente)
            $("#txtConcluDuracionhhmmss").val(obj.duracionhhmmss)
            $("#txtConcluKmTotal").val(obj.kmFacturable)
            $("#txtConcluHrasNormales").val(obj.hrasNormales)
            $("#txtConcluKmEsperado").val(obj.kmEsperado)
            $("#txtConcluReten").val(obj.usaReten)
            $("#txtConcluKmAdicional").val(kmAdicional)
            $("#txtConcluAuxNoReten").val(obj.nroAuxSinReten)
            $("#txtConcluAuxEsperados").val(obj.nroAuxiliaresCuenta)
            $("#txtConcluValorKmAdic").val(obj.valKmAdic)
            $("#txtConcluValTotKmAdic").val( kmAdicional * obj.valKmAdic)
            //obj.tripulantes
            $("#infConcluGuiaPorte").html(obj.guias)
            $("#infConcluTrip").html(obj.tripulantes)
            $("#tabConcluDesPuntos").html(obj.puntos)

            $("#txtConcluValAuxAdicional").val(nroAuxAdicionales)
            $("#txtConcluValValorAuxAdic").val(obj.valorAuxAdicional)
            $("#txtConcluValTotAuxAdic").val(nroAuxAdicionales * obj.valorAuxAdicional)

            $("#fchDespachoNvoGuiaPte").val(obj.fchDespacho)
            $("#correlativoNvoGuiaPte").val(obj.correlativo)
            $("#tabValDesPuntos").html(obj.puntos)

            $("#txtConcluModoCreacion").val(obj.modoCreacion)
            $("#txtConcluModoTerm").val(obj.modoTerminado)
            $("#txtConcluModoConclu").val(obj.modoConcluido)
            $("#txtConcluFchTerm").val(obj.fchTerminado)
            $("#txtConcluUsuarioTerm").val(obj.usuarioTerminado)
            $("#txtConcluFchConclu").val(obj.fchGrabaFin)
            $("#txtConcluHraConclu").val(obj.hraGrabaFin)
            $("#txtConcluUsuarioConclu").val(obj.usuarioGrabaFin)

            $.ajax({
              data: parametros,
              url:  './vistas/ajaxVarios_v2.php?opc=buscarInfoPagosyCobranzas',
              timeout: 15000,
              type:  'post',
              success:  function (response) {
                var objPagosCobranzas = JSON.parse(response);

                $("#txtConcluPrecioBase").val(objPagosCobranzas.precioBaseServicio)
                $("#txtConcluPrecBase2").val(objPagosCobranzas.precioBaseServicio)
                $("#txtConcluTpoAdic").val(objPagosCobranzas.hraAdicCant)
                $("#txtConcluHraAdic").val(objPagosCobranzas.hraAdicCtoUnit)
                $("#txtConcluUnidTerc").val(objPagosCobranzas.unidadTercero)
                $("#txtConcluValorUnTer").val(objPagosCobranzas.costoDia)
                $("#txtConcluTotUnTer").val(objPagosCobranzas.costoDia)
                var totValHraAdic = Math.round(objPagosCobranzas.hraAdicCant * objPagosCobranzas.hraAdicCtoUnit*100)/100
                $("#txtConcluTotValHraAdic").val(totValHraAdic)

                $("#txtConcluAuxAdicional").val(objPagosCobranzas.auxAdicCant)
                $("#txtConcluValorAuxAdic").val(objPagosCobranzas.auxAdicCtoUnit)

                var totValAuxAdic = Math.round(objPagosCobranzas.auxAdicCant * objPagosCobranzas.auxAdicCtoUnit*100)/100
                $("#txtConcluTotAuxAdic").val(totValAuxAdic)             
                                
                $("#pl").val("")
                $("#vha").val("")
                $("#dataValTrip").html(objPagosCobranzas.cadenaTrip)
                $("#dataGuiasPorte").dataTable().fnDestroy();
                var tGuiPor = $('#dataGuiasPorte').DataTable({
                  "dom": '<"toolbar">frtip', 
                  "scrollCollapse": true,
                  "oLanguage": {
                    "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ entradas",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
                    "sZeroRecords": "No hay registros coincidentes",
                  },
                  "responsive": true,
                  "processing": true,
                  "serverSide": true,
                  "ajax":{
                    "url" : "vistas/ladoServidor/ladoServidor.php?opcion=listarGuiasPorte&fchDespacho="+obj.fchDespacho+"&correlativo="+obj.correlativo  ,
                        },
                  "columnDefs": [
                    {"width": "50", "targets": [0,1]},
                  ],
                  'order': [[0, 'desc']]
                });

                $("#infGuiaPorte div.toolbar").addClass("fg-toolbar ui-widget-header ui-helper-clearfix ui-corner-tl ui-corner-tr")
                $("#infGuiaPorte div.toolbar").css({"padding":"5px"});
                $("#infGuiaPorte div.toolbar").html("Guías Porte <img align = 'right' class = 'icoMasGuiaPorte' src='imagenes/mas.png' width='18' height= '18'> ");
                $('#infGuiaPorte div.toolbar').on( 'click', '.icoMasGuiaPorte', function(){
                  dlgNuevaGuiaPorte.dialog( "open" );
                });
                $('#dataGuiasPorte_filter').hide()
                
                $("#subTabConcluOtCobrosPagos").html(objPagosCobranzas.cadDataIngEgr)


                $("#subTabConcluOtOcurrencias").html(objPagosCobranzas.cadOcurrenTodo)

                $("#subTabConcluOtCobPagGraf").html("<canvas id='myChart'></canvas>")


                var pagoTrip = objPagosCobranzas.totDataTrip
                var pagoTerc = objPagosCobranzas.totDataEgrTerc
                var saldo = objPagosCobranzas.totDataSaldo
                var porcTrip = Math.round(pagoTrip/(pagoTrip + pagoTerc + saldo )*100)
                var porcTerc = Math.round(pagoTerc/(pagoTrip + pagoTerc + saldo )*100)
                var porcSaldo = 100 - (porcTrip + porcTerc)
                
                //var porcTrip = 10
                //var porcTerc = 20
                //var porcSaldo = 70

                var ctx = document.getElementById('myChart').getContext('2d');
                var myChart = new Chart(ctx, {
                  type: 'doughnut',
                  data: {
                    "labels": ["Tripulación "+ porcTrip + "%" ,"Tercero "+ porcTerc + "%","Saldo "+ porcSaldo + "%"],
                    datasets: [{
                      data: [pagoTrip, pagoTerc, saldo],
                      backgroundColor: [
                        "rgba(255, 99, 132, 0.8)",
                        "rgba(255, 206, 86, 0.8)",
                        "rgba(54, 162, 235, 0.8)"
                      ],
                      borderColor: [
                        "rgba(255, 99, 132, 1)",
                        "rgba(54, 162, 235, 1)",
                        "rgba(255, 206, 86, 1)"
                      ],
                      borderWidth: 1
                    }]
                  },
                  options: {
                    title: {
                      display: true,
                      text: 'Distribución de Costos y Saldo'
                    },
                  }
                });

                dlgConcluDespacho
                .dialog({
                  title: "Despacho Concluído. Fch Desp.: Correl.: "+ fchDespacho + " : "+ correlativo + ", IdProgram.: " + idProgramacion
                })
                .dialog('open');
              }
            })
          }  
        });
      });

      ////////////////////
      ////////////////////

      // Setup - add a text input to each footer cell
      $('#dataDespTerm tfoot th').each( function (){
        //alert($(this).index() )
        var title = $('#dataDespTerm thead th').eq( $(this).index() ).text();
        if (title != 'Acción' ){
          $(this).html('<input type="text" placeholder=" ' + title + '" style="width:100%"  />');                 
        }
      });

      var t = $('#dataDespTerm').DataTable({
        "dom": '<"toolbar">frtip', 
        "scrollCollapse": true,
        "lengthMenu": [ 8, 30, 500 ],
        "oLanguage": {
        "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ entradas",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
        "sZeroRecords": "No hay registros coincidentes",
        },
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax":{
          "url" :  "vistas/ladoServidor/ladoServidor.php?opcion=listarDespachos&admin="+'<?php echo $_SESSION['admin']; ?>'+ "&edicion="+'<?php echo $_SESSION['edicion']; ?>' + "&estadoDespacho=Terminado",
               },
        "columnDefs": [
          {"targets": [0], "visible": false },
          {"width": "20", "targets": [0]},
          {"width": "30", "targets": [3]},
          {"width": "45", "targets": [1,4]},
          {"width": "55", "targets": [2, 5, 6,10, 11, 12]},
          {"width": "140", "targets": [8]},
          {"width": "150", "targets": [9]},
          {"width": "230", "targets": [7]},
          {"className": "idProgramacion", "targets": [1] },
          {"className": "fchDespacho", "targets": [2] },
          {"className": "correlativo", "targets": [3] },
          {"className": "dataCliente", "targets": [7] },
        ],
        'order': [[1, 'desc'], [2, 'asc']],
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          var fecha01 = new Date(aData[2])
          var fecha02 = new Date()
          var tiempo = fecha02.getTime() - fecha01.getTime();
          var tiempoEnDias =  Math.floor(tiempo / (1000 * 60 * 60 * 24));
          //alert(tiempoEnDias)
          if ( tiempoEnDias > <?php echo $alertasLimiteEstados["Terminado"]; ?>  ) {
            $('td', nRow).css('background-color', '#ff7070' );
          }
          
        },

      });

      t.columns().eq( 0 ).each( function ( colIdx ) {
        if (colIdx == 5){
          $( 'select', t.column( colIdx ).footer() ).on( 'change', function(){
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

      $('#dataDespTerm_filter').hide()

      $('#dataDespTerm tbody').on( 'click', 'tr', function(){
        if ( $(this).hasClass('selected') ) {
          $(this).removeClass('selected');
        } else {
          t.$('tr.selected').removeClass('selected');
          $(this).addClass('selected');
          var idProgramacion = $(this).children('.idProgramacion').html();          
          var fchDespacho = $(this).children('.fchDespacho').html();
          var correlativo = $(this).children('.correlativo').html();
          detalles(idProgramacion, fchDespacho, correlativo, "Terminado")
        }
      });


      ///////////////////
      $('#dataDespTerm').on( 'click', 'tbody .ocurrenciaDespacho',function(){
        $("#btnGuardar").button('enable')
        var fchDespacho =$(this).attr("fchDespacho")
        var correlativo =$(this).attr("correlativo")
        var idProgramacion =$(this).attr("id")
        elDespacho = " IdProgramacion: " + idProgramacion + ", Fch-Correlativo: " + fchDespacho + "-" + correlativo
        var parametros = {
          "fchDespacho":fchDespacho,
          "correlativo":correlativo,
          "idProgramacion":idProgramacion
        };

        //alert(idProgramacion)
        $.ajax({
          data: parametros,
          url:  './vistas/ajaxVarios_v2.php?opc=prepararTripulacionDespacho',
          timeout: 15000,
          type:  'post',
          success:  function (response) {
            //alert(response)
            //var obj = JSON.parse(response);
            $("#elDespacho").val(elDespacho) 
            $("#fchDespachoOcurr").val(fchDespacho) 
            $("#correlativoOcurr").val(correlativo) 
            $("#tblOcurrTrip").html(response)
            $(".fchOcurrenciaTrip").datepicker({
              changeMonth: true,
              changeYear: true,
              showButtonPanel: true,
              minDate: "0M +0D",
              dateFormat:"yy-mm-dd" 
            });
            dlgOcurrenciaDespacho.dialog( "open" )
          }
        })     
      });


      ///////////////////




      // Setup - add a text input to each footer cell
      $('#dataDespEnRuta tfoot th').each( function () {
          var title = $('#dataDespEnRuta thead th').eq( $(this).index() ).text();
          if (title != 'Acción' ){
            $(this).html('<input type="text" placeholder=" ' + title + '" style="width:100%"  />');                 
          }
      } );

      var tEnR = $('#dataDespEnRuta').DataTable({
        "dom": '<"toolbar">frtip', 
        "scrollCollapse": true,
        "lengthMenu": [ 8, 30, 500 ],
        "oLanguage": {
        "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ entradas",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
        "sZeroRecords": "No hay registros coincidentes",
        },
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax":{
          "url" :  "vistas/ladoServidor/ladoServidor.php?opcion=listarDespachos&admin="+'<?php echo $_SESSION['admin']; ?>'+ "&edicion="+'<?php echo $_SESSION['edicion']; ?>' + "&estadoDespacho=EnRuta",
               },
        "columnDefs": [
          {"targets": [0], "visible": false },
          {"width": "20", "targets": [0]},
          {"width": "30", "targets": [3]},
          {"width": "45", "targets": [1,4]},
          {"width": "55", "targets": [2, 5, 6,10, 11, 12]},
          {"width": "140", "targets": [8]},
          {"width": "150", "targets": [9]},
          {"width": "230", "targets": [7]},
          {"className": "idProgramacion", "targets": [1] },
          {"className": "fchDespacho", "targets": [2] },
          {"className": "correlativo", "targets": [3] },
          {"className": "dataCliente", "targets": [5] },
        ],
        'order': [[1, 'desc'], [2, 'asc']],
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          var fecha01 = new Date(aData[2])
          var fecha02 = new Date()
          var tiempo = fecha02.getTime() - fecha01.getTime();
          var tiempoEnDias =  Math.floor(tiempo / (1000 * 60 * 60 * 24));
          //alert(tiempoEnDias)
          if ( tiempoEnDias > <?php echo $alertasLimiteEstados["EnRuta"]; ?>  ) {
            $('td', nRow).css('background-color', '#ff7070' );
          }
          
        },
      });

      tEnR.columns().eq( 0 ).each( function ( colIdx ) {
         if (colIdx == 5){
          $( 'select', tEnR.column( colIdx ).footer() ).on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            this.value
                        );
                        tEnR
                          .column(colIdx)
                          .search( val, true, false )
                          .draw();
                    });
        } else {
          $( 'input', tEnR.column( colIdx ).footer() ).on( 'keyup change', function(){
            tEnR
              .column( colIdx )
              .search( this.value )
              .draw();
          });
        }     
      });

      $('#dataDespEnRuta_filter').hide()

      $('#dataDespEnRuta tbody').on( 'click', 'tr', function(){
        if ( $(this).hasClass('selected') ) {
          $(this).removeClass('selected');
        } else {
          tEnR.$('tr.selected').removeClass('selected');
          $(this).addClass('selected');
          var idProgramacion = $(this).children('.idProgramacion').html();
          var fchDespacho = $(this).children('.fchDespacho').html();
          var correlativo = $(this).children('.correlativo').html();
          detalles(idProgramacion, fchDespacho, correlativo, "EnRuta")          
        }
      });

      $('#dataDespEnRuta tbody').on( 'click', '.descargaInfoDespacho', function(){
        var id = $(this).parent().parent().children('.idProgramacion').html()
        var f = $(this).parent().parent().children('.fchDespacho').html()
        var c = $(this).parent().parent().children('.correlativo').html()
        //alert(id)

        <?php if (PHP_VERSION_ID > 70000){   ?>
          abrirventana('vistas/descargarExcelInfoDespacho.php?id='+id+'&f='+f+'&c='+c)
        <?php  } else {   ?>
          abrirventana('vistas/descargarExcelInfoDespacho5_0.php?id='+id+'&f='+f+'&c='+c)
        <?php } ?>
      });

      $('#dataDespEnRuta tbody').on( 'click', '.cerrarForzoso', function(){
        var id = $(this).parent().parent().children('.idProgramacion').html()
        var f = $(this).parent().parent().children('.fchDespacho').html()
        var c = $(this).parent().parent().children('.correlativo').html()
        //alert("Va bien")
        
        /////////////////
        var param = {
          "fchDespacho":f,
          "correlativo":c,
          "idProgramacion":id
        }
        $.ajax({
          data:param,
          url: './vistas/ajaxVarios_v2.php?opc=buscarInfoParaCerrarForzoso',
          timeout:15000,
          type: 'post',
          success: function(response){
            //alert(response)
            var obj = JSON.parse(response);
            $("#txtCFusuario").val(obj.usuario)
            $("#txtCFfchCreacion").val(obj.fchCreacion)
            $("#txtCFcliente").val(obj.rznCliente)
            $("#txtCFfchDespacho").val(obj.fchDespacho)
            $("#txtCFhraInicio").val(obj.hraInicio)
            //$("#txtCFcuenta").val(obj.correl_cuenta)
            $("#txtCFcuenta").html(obj.cuentas)
            $("#txtCFidProgramacion").val(obj.idProgramacion)
            $("#txtCFcorrelativo").val(obj.correlativo)
            //$("#txtCFproducto").val(obj.producto)
            $("#txtCFproducto").html(obj.ctaPdtos)
            $("#txtCFplaca").val(obj.placa)
            $("#txtCFhraInicioBase").val(obj.hraInicioBase)
            $("#txtCFkmInicio").val(obj.kmInicio)
            $("#txtCFfchFinCli").val(obj.fchDespachoFinCli)
            $("#txtCFfchFin").val(obj.fchDespachoFin)
            $("#txtCFkmInicioCliente").val(obj.kmInicioCliente)
            $("#txtCFhraFinCliente").val(obj.hraFinCliente)
            $("#txtCFkmFinCliente").val(obj.kmFinCliente)
            $("#txtCFhraFin").val(obj.hraFin)
            $("#txtCFkmFin").val(obj.kmFin)
            $("#txtCFobservacion").val(obj.observacion)
            $("#cmbCFPuntoOrigen").html(obj.sedes)
            $("#cmbCFFinCli").html(obj.lugarFinCli)            
            $("#cmbCFReten").html(obj.reten)
            $("#tabTrip").html(obj.tripulantes)
            var cadena = $("#tabTrip").html()
            //alert(cadena.includes("POR LO MENOS SE REQUIERE UN TRIPULANTE"))
            if(cadena.includes("POR LO MENOS SE REQUIERE UN TRIPULANTE")){
              $("#dlgCerrarForzoso").parent().find("button").each(function() {
                if( $(this).text() == 'Guardar' ) {
                  $(this).attr('disabled', true);
                }
              });
            }
            $("#tabPuntos").html(obj.puntos)

            ////////////
            $("#txtCFcuenta").change(function(){
              $("#txtCFcuenta option:selected").each(function () {
                auxCliente = $("#txtCFcliente").val()
                var arrayAux = auxCliente.split("-")
                idCliente = arrayAux[0]
                //alert(idCliente)
                idCuenta=$(this).val();
                //alert(idCuenta)
                $.post("vistas/ajaxVarios_v2.php", { opc: "ctaPdtos", idCuenta: idCuenta, idCliente: idCliente}, function(data){
                  $("#txtCFproducto").html(data);
                });
              });
            })
            ////////////
            
            dlgCerrarForzoso
                .dialog({
                  title: "Cerrar Forzoso. Fch Desp.: Correl.: "+ f + " : "+ c + ", IdProgram.: " + id
                })
                .dialog('open');
            
          }
        })
      });

      $('#txtCFcuenta').on('change', function(){
        valor = $("#txtCFcliente").val()
        aux = valor.split("-")
        idCliente = aux[0]
        elegido = $(this).val()
        if ( elegido != ""){
          $('#txtCFproducto').val("")
          aux = elegido.split("|")
          idCuenta = aux[0]
          tipoCuenta = aux[2]
          $('#txtCFproducto').autocomplete({
            minLength: 2,
            //source : 'clientesAjax.php'
            source : './vistas/ajaxVarios.php?opc=buscarProducto&idCli=' + idCliente + '&idCuenta=' + idCuenta
          });
        }       
      });

      $("#tabsCerrarForzoso").tabs();

      $(".aLimpiarDetalles").on('click',function(){
        //alert("Cambió pestaña")
        $("#infTrip div.toolbar").html("<b>Tripulación de:</b>");
        $("#infPuntos div.toolbar").html("<b>Puntos Asignados de:</b>");

        $('#dataPuntos tbody').empty();
        $('#dataTrip tbody').empty();    
      })
      

      dlgCerrarForzoso = $("#dlgCerrarForzoso").dialog({
        autoOpen: false,
        resizable: false,
        height: 500,
        width: 800,
        modal: true,        
        buttons: {
          "Guardar": cerrarForzoso,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });
      
      function cerrarForzoso(){
        //alert("llegó")
        cfCuenta = $("#txtCFcuenta").val()
        if( cfCuenta == "" ){
          alert("Debe elegir una cuenta")
          return false;
        }

        if( $("#txtCFproducto").val() == "" ){
          alert("Debe elegir un producto")
          return false;
        }

        arrCuenta = cfCuenta.split("|");
        tipoCuenta = arrCuenta[2]
        //alert(tipoCuenta)

        if( $("#txtCFplaca").val() == "" && tipoCuenta != 'SoloPersonal' ){
          alert("Ingrese una placa")
          return false;
        }

        if($("#cmbCFPuntoOrigen").val() ==""){
          alert("Ingrese Punto Origen")
          return false;
        }

        if($("#cmbCFFinCli").val() == "-"){
          alert("Ingrese Final en")
          return false; 
        }

        if($("#txtCFfchFinCli").val() == "" ){
          alert("Debe ingresar la fecha fin del cliente")
          return false;
        }

        if($("#txtCFfchFin").val() == "" ){
          alert("Debe ingresar la fecha fin")
          return false;
        }

        if($("#txtCFfchFinCli").val() > $("#txtCFfchFin").val() ){
          alert("Hay inconsistencia entre la fecha fin cliente y fecha fin")
          return false;
        }

        if($("#txtCFfchDespacho").val() > $("#txtCFfchFin").val() ){
          alert("Hay inconsistencia entre la fecha despacho y fecha fin")
          return false;
        }

        if( $("#txtCFhraInicioBase").val() > $("#txtCFhraInicio").val() ){
          alert("La hora inicio cliente no puede ser menor que la hora inicio base")
          return false;
        }

        if(  $("#txtCFhraInicio").val() > $("#txtCFhraFinCliente").val()){
          alert("La hora fin cliente no puede ser menor que la hora inicio cliente")
          return false;
        }

        if( $("#txtCFhraFinCliente").val() > $("#txtCFhraFin").val()){
          alert("La hora fin no puede ser menor que la hora fin cliente")
          return false;
        }

        if( $("#txtCFkmInicio").val() > $("#txtCFkmInicioCliente").val() ){
          alert("El km inicio cliente no puede ser menor que el km inicio")
          return false;
        }

        if(  $("#txtCFkmInicioCliente").val() > $("#txtCFkmFinCliente").val()){
          alert("El km fin cliente no puede ser menor que el km inicio cliente")
          return false;
        }

        if( $("#txtCFkmFinCliente").val() > $("#txtCFkmFin").val()){
          alert("El km fin no puede ser menor que el km fin cliente")
          return false;
        }
  
        var datos = new FormData($("#formCerrarForzoso")[0]);
        $.ajax({
          data:datos,
          url: './vistas/ajaxVarios_v2.php?opc=cerrarForzoso',
          timeout:15000,
          method: "POST",
          processData: false, 
          contentType: false,
          success: function(response){
          //alert(response)    
            if (response == 1){      
              swal({
                type: "success",
                title: "¡Cerrar Forzoso se ejecutó correctamente!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                $("#dlgCerrarForzoso").dialog( "close" )
                $('#dataDespEnRuta').DataTable().ajax.reload();
              });
            } else {
              swal({
                type: "error",
                title: "¡No se pudo ejecutar Cerrar Forzoso.",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              })
            }
          }
        })        
      }

      // Setup - add a text input to each footer cell
      $('#dataDespProgram tfoot th').each( function () {
          var title = $('#dataDespProgram thead th').eq( $(this).index() ).text();
          if (title != 'Acción' ){
            $(this).html('<input type="text" placeholder=" ' + title + '" style="width:100%"  />');                 
          }
      } );

      var tProg = $('#dataDespProgram').DataTable({
        "dom": '<"toolbar">frtip', 
        "scrollCollapse": true,
        "lengthMenu": [ 10, 30, 500 ],
        "oLanguage": {
        "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ entradas",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
        "sZeroRecords": "No hay registros coincidentes",
        },
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax":{
          "url" :  "vistas/ladoServidor/ladoServidor.php?opcion=listarDespachos&admin="+'<?php echo $_SESSION['admin']; ?>'+ "&edicion="+'<?php echo $_SESSION['edicion']; ?>' + "&estadoDespacho=Programado",
               },
        "columnDefs": [
          {targets: 0, 'checkboxes': {'selectRow': true } },
          {"width": "20", "targets": [0]},
          {"width": "30", "targets": [3]},
          {"width": "45", "targets": [1,4]},
          {"width": "55", "targets": [2, 5, 6,10, 11, 12]},
          {"width": "140", "targets": [8]},
          {"width": "150", "targets": [9]},
          {"width": "230", "targets": [7]},
          {"className": "idProgramacion", "targets": [1] },
          {"className": "fchDespacho", "targets": [2] },
          {"className": "correlativo", "targets": [3] },
          {"className": "dataCliente", "targets": [5] },
        ],
        'select': { 'style': 'multi' },
        'order': [[1, 'desc'], [2, 'asc']],
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          var fecha01 = new Date(aData[2])
          var fecha02 = new Date()
          var tiempo = fecha02.getTime() - fecha01.getTime();
          var tiempoEnDias =  Math.floor(tiempo / (1000 * 60 * 60 * 24));
          //alert(tiempoEnDias)
          if ( tiempoEnDias > <?php echo $alertasLimiteEstados["Programado"]; ?>  ) {
            $('td', nRow).css('background-color', '#ff7070' );
          }
        },
      });

      tProg.columns().eq( 0 ).each( function ( colIdx ) {

         if (colIdx == 5){
          $( 'select', tProg.column( colIdx ).footer() ).on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            this.value
                        );
                        tProg
                          .column(colIdx)
                          .search( val, true, false )
                          .draw();
                    });

        } else {
          $( 'input', tProg.column( colIdx ).footer() ).on( 'keyup change', function(){
            tProg
              .column( colIdx )
              .search( this.value )
              .draw();
          });
          
        }
          
      });

      $('#dataDespProgram_filter').hide()

      $('#dataDespProgram tbody').on( 'click', 'tr', function(){
        if ( $(this).hasClass('selected') ) {
          $(this).removeClass('selected');
        } else {
          tProg.$('tr.selected').removeClass('selected');
          $(this).addClass('selected');

          var idProgramacion = $(this).children('.idProgramacion').html();
          var fchDespacho = $(this).children('.fchDespacho').html();
          var correlativo = $(this).children('.correlativo').html();
          detalles(idProgramacion, fchDespacho, correlativo, "Programado")
        }
      });

      $( "#tabsDespachos" ).tabs();

      $( "#tabValDesp" ).tabs();

      $( "#tabConcluDesp" ).tabs();
      $( "#subTabConcluOtros" ).tabs();
      

      $('#txtEditaPlaca').autocomplete({
        minLength: 2,
        source : 'placasAjax.php'
        //source : ['Maria','Jose','Rocio']  
      });

      $('#txtEditaCliente').autocomplete({
        minLength: 2,
        source : 'clientesAjax.php',
        select: function( event, ui ) {
          auxIdCliente = ui.item.value
          var n = auxIdCliente.indexOf("-");
          idCliente = auxIdCliente.substr(0, n);
          $("#cmbEditaCliCuenta").html("")
          $("#cmbEditaCliProducto").html("")

          $("#txtEditaCambioValorServ").val("")
          $("#txtEditaCambioValorConductor").val("")
          $("#txtEditaCambioValorAuxiliar").val("")

          var param2 = {
            "idCliente":idCliente,
            "estadoCuenta":'Activo'
          }
          $.ajax({
            data:param2,
            url: './vistas/ajaxVarios_v2.php?opc=buscarCliCuentas',
            timeout:15000,
            type: 'post',
            success: function(respons2){            
              $("#cmbEditaCliCuenta").html(respons2)
            }
          })
        }
      });
      
      $("#cmbEditaCliCuenta").change(function () {
        correlativo = $(this).val();
        auxIdCliente = $('#txtEditaCliente').val()

        var n = auxIdCliente.indexOf("-");
        idCliente = auxIdCliente.substr(0, n);
        $("#cmbEditaCliProducto").html("")
        
        var param = {
          "correlativo":correlativo,
          "idCliente":idCliente,
          "estadoProducto":'Activo'
        }
        $.ajax({
          data:param,
          url: './vistas/ajaxVarios_v2.php?opc=buscarCliCueProd',
          timeout:15000,
          type: 'post',
          success: function(response){            
            $("#cmbEditaCliProducto").html(response)
          }
        })
      })

      $("#cmbEditaCliProducto").change(function () {
        idProducto = $(this).val();
        //alert(idProducto)

        /*var n = auxIdCliente.indexOf("-");
        idCliente = auxIdCliente.substr(0, n);
        $("#cmbEditaCliProducto").html("")
        */
        
        var param = {
          "idProducto":idProducto,
         /* "idCliente":idCliente,
          "estadoProducto":'Activo'*/
        }
        $.ajax({
          data:param,
          url: './vistas/ajaxVarios_v2.php?opc=buscarDataIdProducto',
          timeout:15000,
          type: 'post',
          success: function(response){
            var obj = JSON.parse(response);
            $("#txtEditaCambioValorServ").val(obj.precioServ)
            $("#txtEditaCambioValorConductor").val(obj.valConductor)
            $("#txtEditaCambioValorAuxiliar").val(obj.valAuxiliar)
          }
        })  
      })

      dlgValDespacho = $( "#dlgValDespacho" ).dialog({
        autoOpen: false,
        resizable: false,
        height: 600,
        width: 950,
        modal: true,
        
        buttons: {
          "Guardar": guardarValidaDespacho,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function guardarValidaDespacho(){
        //alert($("#txtValPrecBase2").val())
        swal({
          type: "warning",
          title: 'Desea concluir el despacho validado?',
          text: "Este cambio no se podrá revertir!",
          showCancelButton: true,
          //confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, concluir el despacho!'
        }).then((result) => {
          if (result.value) {
            if($("#txtValPrecBase2").val() == 0){
              ////////
              swal({
                type: "warning",
                title: "El precio del servicio está en cero\n y no generará documento de cobranza",
                text: "Desea continuar?",
                showCancelButton: true,
                //confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                background: '#Fe0000',
                confirmButtonText: 'Si, continuar!'
              }).then((result2) => {
                if (result2.value) {
                  var datos = new FormData($("#formValDespacho")[0]);
                  $.ajax({
                    data:datos,
                    url: './vistas/ajaxVarios_v2.php?opc=guardarValidaDespacho',
                    timeout:15000,
                    method: "POST",
                    processData: false,
                    contentType: false,
                    success: function(response){
                      if(response == -1){
                        //Ya existen documentos asociados
                        swal({
                          type: "error",
                          title: "¡No se pudo hacer la validación satisfactoriamente!",
                          showConfirmButton:true,
                          confirmButtonText:"Cerrar",
                          closeOnConfirm: false
                        })
                      } else if (response == 1){      
                        $("#dlgValDespacho").dialog( "close" )
                        swal({
                          type: "success",
                          title: "¡La validación se llevó a cabo satisfactoriamente!",
                          showConfirmButton:true,
                          confirmButtonText:"Cerrar",
                          closeOnConfirm: false
                        }).then((result)=>{
                            //$('#dataGuiasPorte').DataTable().ajax.reload();
                            t.draw()
                        });
                      } else {
                        swal({
                          type: "error",
                          title: "¡Ha ocurrido un error inesperado. Revise los resultados!",
                          showConfirmButton:true,
                          confirmButtonText:"Cerrar",
                          closeOnConfirm: false
                        })
                      }
                    }
                  })
                }
              })


              ////////
            } else {
              //////////////
              var datos = new FormData($("#formValDespacho")[0]);
              $.ajax({
                data:datos,
                url: './vistas/ajaxVarios_v2.php?opc=guardarValidaDespacho',
                timeout:15000,
                method: "POST",
                processData: false,
                contentType: false,
                success: function(response){
                  if(response == -1){
                    //Ya existen documentos asociados
                    swal({
                      type: "error",
                      title: "¡No se pudo hacer la validación satisfactoriamente!",
                      showConfirmButton:true,
                      confirmButtonText:"Cerrar",
                      closeOnConfirm: false
                    })
                  } else if (response == 1){      
                    $("#dlgValDespacho").dialog( "close" )
                    swal({
                      type: "success",
                      title: "¡La validación se llevó a cabo satisfactoriamente!",
                      showConfirmButton:true,
                      confirmButtonText:"Cerrar",
                      closeOnConfirm: false
                    }).then((result)=>{
                        //$('#dataGuiasPorte').DataTable().ajax.reload();
                        t.draw()
                    });
                  } else {
                    swal({
                      type: "error",
                      title: "¡Ha ocurrido un error inesperado. Revise los resultados!",
                      showConfirmButton:true,
                      confirmButtonText:"Cerrar",
                      closeOnConfirm: false
                    })
                  }
                }
              })
              //////////////
            }         
          }
        })        
      }

      dlgNuevaGuiaPorte = $( "#dlgNuevaGuiaPorte" ).dialog({
        autoOpen: false,
        resizable: false,
        height: 180,
        width: 220,
        modal: true,
        
        buttons: {
          "Guardar": nuevaGuiaPorte,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });


      function nuevaGuiaPorte(){
        var datos = new FormData($("#formNuevaGuiaPorte")[0]);
        $.ajax({
          data:datos,
          url: './vistas/ajaxVarios_v2.php?opc=insertaNuevaGuiaPorte',
          timeout:15000,
          method: "POST",
          processData: false, 
          contentType: false,
          success: function(response){
            //console.log("response", response)
            if (response == 1){      
              $("#dlgNuevaGuiaPorte").dialog( "close" )
              swal({
                type: "success",
                title: "¡La guía porte ha sido guardada correctamente!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                  $('#dataGuiasPorte').DataTable().ajax.reload();
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
        })
      }
      
      dlgEditaGuiaPorte = $( "#dlgEditaGuiaPorte" ).dialog({
        autoOpen: false,
        resizable: false,
        height: 180,
        width: 220,
        modal: true,
        buttons: {
          "Guardar": editaGuiaPorte,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function editaGuiaPorte(){
        var datos = new FormData($("#formEditaGuiaPorte")[0]);
        //console.log("Data",datos)
        $.ajax({
          data:datos,
          url: './vistas/ajaxVarios_v2.php?opc=editaGuiaPorte',
          timeout:15000,
          method: "POST",
          processData: false, 
          contentType: false,

          success: function(response){
            //console.log("response", response)         
            if (response == 1){      
              $("#dlgEditaGuiaPorte").dialog( "close" )
              swal({
                type: "success",
                title: "¡La guía porte ha sido editada correctamente!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                  $('#dataGuiasPorte').DataTable().ajax.reload();
              });
            } else {
              swal({
                type: "error",
                title: "¡No se editó el registro. Puede ser que el número de guía porte ya exista!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              })
            }
          }
        })
      }


      $('#dataGuiasPorte').on( 'click', '.editar', function(){
        var auxGuiaPorte = $(this).attr("guiaPorte")
        posicion = auxGuiaPorte.indexOf("-")
        nroSerie = auxGuiaPorte.substring(0,posicion)
        correlativo = auxGuiaPorte.substring(posicion+1) 

        $("#txtEditaGuiaPorteNroSerie").val(nroSerie)
        $("#txtEditaGuiaPorteCorrelativo").val(correlativo)
        $("#guiaPorteIni").val(auxGuiaPorte)
        dlgEditaGuiaPorte.dialog( "open" );
      });
 
      dlgEliminaGuiaPorte = $( "#dlgEliminaGuiaPorte" ).dialog({
        autoOpen: false,
        resizable: false,
        height: 180,
        width: 220,
        modal: true,
        buttons: {
          "Eliminar": eliminaGuiaPorte,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function eliminaGuiaPorte(){
        var datos = new FormData($("#formEliminaGuiaPorte")[0]);
        //console.log("Data",datos)
        $.ajax({
          data:datos,
          url: './vistas/ajaxVarios_v2.php?opc=eliminaGuiaPorte',
          timeout:15000,
          method: "POST",
          processData: false, 
          contentType: false,

          success: function(response){
            //console.log("response", response)         
            if (response == 1){      
              $("#dlgEliminaGuiaPorte").dialog( "close" )
              swal({
                type: "success",
                title: "¡La guía porte ha sido eliminada correctamente!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                  $('#dataGuiasPorte').DataTable().ajax.reload();
              });
            } else {
              swal({
                type: "error",
                title: "¡No se pudo eliminar la guía porte.",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              })
            }
          }
        })
      }

      $('#dataGuiasPorte').on( 'click', '.eliminar', function(){
        var auxGuiaPorte = $(this).attr("guiaPorte")
        //alert(auxGuiaPorte)
        posicion = auxGuiaPorte.indexOf("-")
        nroSerie = auxGuiaPorte.substring(0,posicion)
        correlativo = auxGuiaPorte.substring(posicion+1) 

        $("#txtEliminaGuiaPorteNroSerie").val(nroSerie)
        $("#txtEliminaGuiaPorteCorrelativo").val(correlativo)

        $("#guiaPorteIniElim").val(auxGuiaPorte)


        dlgEliminaGuiaPorte.dialog( "open" );
      });


      $("#txtNuevoDistCli").autocomplete({
        minLength: 2,
        source : 'vistas/ajaxVarios.php?opc=distritos',
      });

      dlgNuevoPunto = $( "#dlgNuevoPunto" ).dialog({
        autoOpen: false,
        resizable: false,
        height: 450,
        width: 800,
        modal: true,
        
        buttons: {
          "Guardar": nuevoPunto,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function nuevoPunto(){
        if( $("#cmbNuevoTipoPunto").val() == "-" ){
          alert("Debe elegir el tipo de punto")
          return false;
        }
        if( $("#txtGuiaCliente").val() == "" ){
          alert("Debe ingresar la guía del Cliente")
          return false;
        }

        if( $("#txtNuevoNombCli").val() == "" ){
          alert("Ingrese el nombre del Cliente")
          return false;
        }
        if( $("#txtDirCliente").val() == "" ){
          alert("Debe ingresar la dirección")
          return false;
        }

        if( $("#txtNuevoDistCli").val() == "" ){
          alert("Debe ingresar el distrito")
          return false;
        }

        if( $("#cmbNuevoPuntoEstado").val() == "-" ){
          alert("Debe ingresar el Estado")
          return false;
        }

        if( $("#cmbNuevoPuntoSubestado").val() == "-" ){
          alert("Debe ingresar el Subestado")
          return false;
        } 

        var datos = new FormData($("#formNuevoPunto")[0]);
        $.ajax({
          data:datos,
          url: './vistas/ajaxVarios_v2.php?opc=nuevoPunto',
          timeout:15000,
          method: "POST",
          processData: false, 
          contentType: false,
          success: function(response){      
            if (response == 1){      
              $("#dlgNuevoPunto").dialog( "close" )
              swal({
                type: "success",
                title: "¡El nuevo punto ha sido creado correctamente!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                  $('#dataPuntos').DataTable().ajax.reload();
              });
            } else {
              swal({
                type: "error",
                title: "¡No se pudo crear el punto.",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              })
            }
          }
        })
      }

      dlgEditaPunto = $( "#dlgEditaPunto" ).dialog({
        autoOpen: false,
        resizable: false,
        height: 330,
        width: 600,
        modal: true,
        
        buttons: {
          "Guardar": editaPunto,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function editaPunto(){
        //alert("Presionó")
        var datos = new FormData($("#formEditaPunto")[0]);
        $.ajax({
          data:datos,
          url: './vistas/ajaxVarios_v2.php?opc=editaPunto',
          timeout:15000,
          method: "POST",
          processData: false, 
          contentType: false,
          success: function(response){      
            if (response == 1){      
              $("#dlgEditaPunto").dialog( "close" )
              swal({
                type: "success",
                title: "¡El punto ha sido editado correctamente!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                  $('#dataPuntos').DataTable().ajax.reload();
              });
            } else {
              swal({
                type: "error",
                title: "¡No se pudo editar el punto.",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              })
            }
          }
        })

      }

      dlgEliminaPunto = $( "#dlgEliminaPunto" ).dialog({
        autoOpen: false,
        resizable: false,
        height: 300,
        width: 450,
        modal: true,        
        buttons: {
          "Eliminar": eliminaPunto,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function eliminaPunto(){
        var datos = new FormData($("#formEliminaPunto")[0]);
        $.ajax({
          data:datos,
          url: './vistas/ajaxVarios_v2.php?opc=eliminaPunto',
          timeout:15000,
          method: "POST",
          processData: false, 
          contentType: false,
          success: function(response){      
            if (response == 1){      
              $("#dlgEliminaPunto").dialog( "close" )
              swal({
                type: "success",
                title: "¡El punto ha sido eliminado correctamente!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                  $('#dataPuntos').DataTable().ajax.reload();
              });
            } else {
              swal({
                type: "error",
                title: "¡No se pudo eliminar el punto.",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              })
            }
          }
        })
      }

      dlgVerFotos = $( "#dlgVerFotos" ).dialog({
        autoOpen: false,
        resizable: false,
        height: 600,
        width: 425,
        modal: true,
        
        buttons: {
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });      

      dlgTraspasarPunto = $( "#dlgTraspasarPunto" ).dialog({
        autoOpen: false,
        resizable: false,
        height: 300,
        width: 450,
        modal: true,
        
        buttons: {
          "Traspasar": traspasarPunto,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function traspasarPunto(){
        var datos = new FormData($("#formTraspasarPunto")[0]);
        $.ajax({
          data:datos,
          url: './vistas/ajaxVarios_v2.php?opc=traspasarPunto',
          timeout:15000,
          method: "POST",
          processData: false, 
          contentType: false,
          success: function(response){      
            if (response == 1){      
              $("#dlgTraspasarPunto").dialog( "close" )
              swal({
                type: "success",
                title: "¡El punto ha sido trasladado correctamente!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                  $('#dataPuntos').DataTable().ajax.reload();
              });
            } else {
              swal({
                type: "error",
                title: "¡No se pudo trasladar el punto.",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              })
            }
          }
        })
      }


      dlgNuevoTripulante = $( "#dlgNuevoTripulante" ).dialog({
        autoOpen: false,
        resizable: false,
        height: 330,
        width: 500,
        modal: true,     
        buttons: {
          "Guardar": nuevoTripulante,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function nuevoTripulante(){
        //alert("Prueba")
        var datos = new FormData($("#formNuevoTripulante")[0]);
        $.ajax({
          data:datos,
          url: './vistas/ajaxVarios_v2.php?opc=insertaTrip',
          timeout:15000,
          method: "POST",
          processData: false, 
          contentType: false,
          success: function(response){      
            if (response == 1){      
              $("#dlgNuevoTripulante").dialog( "close" )
              swal({
                type: "success",
                title: "¡El tripulante ha sido creado correctamente!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                $('#dataTrip').DataTable().ajax.reload();
              });
            } else {
              swal({
                type: "error",
                title: "¡No se pudo crear el tripulante.",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              })
            }
          }
        })
      }

      dlgConcluDespacho = $( "#dlgConcluDespacho" ).dialog({
        autoOpen: false,
        resizable: false,
        height: 470,
        width: 890,
        modal: true,
        
        buttons: {
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      $('#dataDespTerm tbody').on( 'click', '.descargaInfoDespacho', function(){
        var id = $(this).parent().parent().children('.idProgramacion').html()
        var f = $(this).parent().parent().children('.fchDespacho').html()
        var c = $(this).parent().parent().children('.correlativo').html()
        //alert(id)

        <?php if (PHP_VERSION_ID > 70000){   ?>
          abrirventana('vistas/descargarExcelInfoDespacho.php?id='+id+'&f='+f+'&c='+c)
        <?php  } else {   ?>
          abrirventana('vistas/descargarExcelInfoDespacho5_0.php?id='+id+'&f='+f+'&c='+c)
        <?php } ?>

      });
      
      $('#dataDespTerm').on( 'click', 'tbody .editarDespacho',function(){
        var fchDespacho =$(this).attr("fchDespacho")
        var correlativo =$(this).attr("correlativo")
        var idProgramacion =$(this).attr("id")
        var parametros = {
          "fchDespacho":fchDespacho,
          "correlativo":correlativo,
          "idProgramacion":idProgramacion,
        };

        $.ajax({
          data: parametros,
          url:  './vistas/ajaxVarios_v2.php?opc=buscarInfoParaCerrarForzoso',
          timeout: 15000,
          type:  'post',
          success:  function (response) {
            var obj = JSON.parse(response);
            //alert(obj.alertaPlaca)
            //kmRecorrido = obj.kmFacturable
            if(obj.alertaPlaca != 'Inactivo' ){
              if(obj.kmFacturable > obj.kmEsperado )
                kmAdicional = obj.kmFacturable - obj.kmEsperado
              else
                kmAdicional = 0
              //alert(obj.fchDespachoFinCli)
              $("#txtValUsuario").val(obj.usuario)
              $("#txtValFchCreacion").val(obj.fchCreacion)
              $("#txtValCliente").val(obj.rznCliente)
              $("#txtValFchDespacho").val(obj.fchDespacho)
              $("#txtValHraInicio").val(obj.hraInicio)
              $("#txtValIdProgramacion").val(obj.idProgramacion)
              $("#txtValCorrelativo").val(obj.correlativo)
              $("#txtValPlaca").val(obj.placa)
              $("#txtValHraInicioBase").val(obj.hraInicioBase)
              $("#txtValKmInicio").val(obj.kmInicio)
              $("#txtValKmInicioCliente").val(obj.kmInicioCliente)
              $("#txtValHraFinCliente").val(obj.hraFinCliente)
              $("#txtValKmFinCliente").val(obj.kmFinCliente)
              $("#txtValFchFinCli").val(obj.fchDespachoFinCli)
              $("#txtValFchFin").val(obj.fchDespachoFin)
              $("#txtValHraFin").val(obj.hraFin)
              $("#txtValKmFin").val(obj.kmFin)
              $("#txtValEstadoDespacho").val(obj.estadoDespacho)
              $("#txtValObservacion").val(obj.observacion)
              $("#cmbValPuntoOrigen").html(obj.sedes)
              $("#cmbValLugarFinCli").html(obj.lugarFinCli)
              $("#txtDuracionhhmmss").val(obj.duracionhhmmss)
              $("#txtValKmTotal").val(obj.kmFacturable) //val(obj.kmFin - obj.kmInicio)
              $("#txtValHrasNormales").val(obj.hrasNormales)
              $("#txtValKmEsperado").val(obj.kmEsperado)
              $("#cmbValReten").html(obj.reten)
              $("#txtValCuenta").html(obj.cuentas)
              $("#txtValProducto").html(obj.ctaPdtos)
              $("#txtValKmAdicional").val(kmAdicional)
              $("#txtValValorKmAdic").val(obj.valKmAdic)
              $("#txtValTotKmAdic").val(1 * kmAdicional * obj.valKmAdic)
              $("#txtValAuxTotal").val(obj.nroAuxTotal)
              $("#txtValAuxReten").val(obj.nroAuxReten)

              $("#txtValAuxNoReten").val(obj.nroAuxSinReten)
              $("#txtValAuxEsperados").val(obj.nroAuxiliaresCuenta)                        
              $("#fchDespachoNvoGuiaPte").val(obj.fchDespacho)
              $("#correlativoNvoGuiaPte").val(obj.correlativo)
              $("#tabValDesPuntos").html(obj.puntos)

              $("#txtValTercValorAuxTerc").val(obj.valAuxTerceroDesp)
              $("#txtValHraAdicTercAdic").val(obj.valHraExtraTerc)

              $("#txtValModoCreacion").val(obj.modoCreacion)
              $("#txtValModoTerm").val(obj.modoTerminado)
              $("#txtValModoConclu").val(obj.modoConcluido)
              $("#txtValFchTerm").val(obj.fchTerminado)
              $("#txtValUsuarioTerm").val(obj.usuarioTerminado)
              $("#txtValFchConclu").val(obj.fchGrabaFin)
              $("#txtValHraConclu").val(obj.hraGrabaFin)
              $("#txtValUsuarioConclu").val(obj.usuarioGrabaFin)
              $.ajax({
                data: parametros,
                url:  './vistas/ajaxVarios_v2.php?opc=buscarInfoPagosyCobranzas',
                timeout: 15000,
                type:  'post',
                success:  function (response) {
                  var objPagosCobranzas = JSON.parse(response);
                  $("#txtValPrecioBase").val(objPagosCobranzas.precioBaseServicio)
                  $("#txtValPrecBase2").val(objPagosCobranzas.precioBaseServicio)
                  $("#valPrecioBaseOriginal").val(objPagosCobranzas.precioBaseServicio)
                          
                  $("#txtValTpoAdic").val(objPagosCobranzas.hraAdicCant)
                  $("#txtValHraAdic").val(objPagosCobranzas.hraAdicCtoUnit)
                  $("#txtValUnidTerc").val(objPagosCobranzas.unidadTercero)
                  $("#txtValValorUnTer").val(objPagosCobranzas.costoDia)
                  $("#txtValTotUnTer").val(objPagosCobranzas.costoDia)
                  var totValHraAdic = Math.round(objPagosCobranzas.hraAdicCant * objPagosCobranzas.hraAdicCtoUnit*100)/100
                  $("#txtValTotValHraAdic").val(totValHraAdic)
                  $("#txtValAuxAdicional").val(objPagosCobranzas.auxAdicCant)
                  $("#txtValValorAuxAdic").val(objPagosCobranzas.auxAdicCtoUnit)
                  $("#txtValTercHraExtraTotal").val(objPagosCobranzas.ocurrHraExtraMntTotal)
                  $("#txtValTercPersAdicTotal").val(objPagosCobranzas.ocurrPersAdicMntTotal)

                  var totValAuxAdic = Math.round(objPagosCobranzas.auxAdicCant * objPagosCobranzas.auxAdicCtoUnit*100)/100
                  $("#txtValTotAuxAdic").val(totValAuxAdic)
                  $("#txtValDurAdicTerc").val(objPagosCobranzas.ocurrHraExtraCant)
                  $("#txtValTercPersAdic").val(objPagosCobranzas.ocurrPersAdicCant)
                
                  $("#pl").val("")
                  $("#vha").val("")
                  $("#dataValTrip").html(objPagosCobranzas.cadenaTrip)
                  $("#dataGuiasPorte").dataTable().fnDestroy();
                  var tGuiPor = $('#dataGuiasPorte').DataTable({
                    "dom": '<"toolbar">frtip', 
                    "scrollCollapse": true,
                    "oLanguage": {
                      "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ entradas",
                      "sLengthMenu": "Mostrar _MENU_ registros",
                      "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
                      "sZeroRecords": "No hay registros coincidentes",
                    },
                    "responsive": true,
                    "processing": true,
                    "serverSide": true,
                    "ajax":{
                      "url" : "vistas/ladoServidor/ladoServidor.php?opcion=listarGuiasPorte&fchDespacho="+obj.fchDespacho+"&correlativo="+obj.correlativo  ,
                          },
                    "columnDefs": [
                      {"width": "50", "targets": [0,1]},
                    ],
                    'order': [[0, 'desc']]
                  });

                  $("#infGuiaPorte div.toolbar").addClass("fg-toolbar ui-widget-header ui-helper-clearfix ui-corner-tl ui-corner-tr")
                  $("#infGuiaPorte div.toolbar").css({"padding":"5px"});
                  $("#infGuiaPorte div.toolbar").html("Guías Porte <img align = 'right' class = 'icoMasGuiaPorte' src='imagenes/mas.png' width='18' height= '18'> ");
                  $('#infGuiaPorte div.toolbar').on( 'click', '.icoMasGuiaPorte', function(){
                    dlgNuevaGuiaPorte.dialog( "open" );
                  });
                  $('#dataGuiasPorte_filter').hide()
                  dlgValDespacho
                  .dialog({
                    title: "Validar Despacho. Fch Desp.: Correl.: "+ fchDespacho + " : "+ correlativo + ", IdProgram.: " + idProgramacion
                  })
                  .dialog('open');
                }
              })
            } else {
              swal({
                type: "warning",
                title: "¡La placa relacionada al despacho requiere actualización.",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              })
            }
          }
        });
      });

      $('#dataDespTerm').on( 'click', 'tbody .eliminarDespacho',function(){
        var fchDespacho =$(this).attr("fchDespacho")
        var correlativo =$(this).attr("correlativo")
        var idProgramacion =$(this).attr("id")
        //alert(idProgramacion)
        var parametros = {
          "fchDespacho":fchDespacho,
          "correlativo":correlativo,
          "idProgramacion":idProgramacion,
        };
        $.ajax({
          data: parametros,
          url:  './vistas/ajaxVarios_v2.php?opc=buscarInfoParaCerrarForzoso',
          timeout: 15000,
          type:  'post',
          success:  function (response) {
            var obj = JSON.parse(response);
            $("#txtElimUsuario").val(obj.usuario)
            $("#txtElimFchCreacion").val(obj.fchCreacion)
            $("#txtElimCliente").val(obj.rznCliente)
            $("#txtElimFchDespacho").val(obj.fchDespacho)
            $("#txtElimIdProgramacion").val(obj.idProgramacion)
            $("#txtElimCorrelativo").val(obj.correlativo)
            $("#txtElimEstado").val(obj.estadoDespacho)
            dlgElimDespacho.dialog( "open" )
          }  
        });
      });

      $('#dataDespProgram').on( 'click', 'tbody .eliminarDespacho',function(){
        var fchDespacho =$(this).attr("fchDespacho")
        var correlativo =$(this).attr("correlativo")
        var idProgramacion =$(this).attr("id")
        //alert(idProgramacion)
        var parametros = {
          "fchDespacho":fchDespacho,
          "correlativo":correlativo,
          "idProgramacion":idProgramacion,
        };
        $.ajax({
          data: parametros,
          url:  './vistas/ajaxVarios_v2.php?opc=buscarInfoParaCerrarForzoso',
          timeout: 15000,
          type:  'post',
          success:  function (response) {
            var obj = JSON.parse(response);
            $("#txtElimUsuario").val(obj.usuario)
            $("#txtElimFchCreacion").val(obj.fchCreacion)
            $("#txtElimCliente").val(obj.rznCliente)
            $("#txtElimFchDespacho").val(obj.fchDespacho)
            $("#txtElimIdProgramacion").val(obj.idProgramacion)
            $("#txtElimCorrelativo").val(obj.correlativo)
            $("#txtElimEstado").val(obj.estadoDespacho)
            dlgElimDespacho.dialog( "open" )
          }  
        });
      });


      dlgElimDespacho = $( "#dlgElimDespacho" ).dialog({
        autoOpen: false,
        resizable: false,
        height: 230,
        width: 600,
        modal: true,        
        buttons: {
          "Eliminar": elimDespacho,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });


      function elimDespacho(){
        var datos = new FormData($("#formElimDespacho")[0]);
        $.ajax({
          data:datos,
          url: './vistas/ajaxVarios_v2.php?opc=elimDespacho',
          timeout:15000,
          method: "POST",
          processData: false, 
          contentType: false,
          success: function(response){      
            if (response == 1){      
              $("#dlgElimDespacho").dialog( "close" )
              swal({
                type: "success",
                title: "¡El despacho ha sido eliminado correctamente!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                $('#dataDespTerm').DataTable().ajax.reload();
                $('#dataDespProgram').DataTable().ajax.reload();
              });
            } else {
              swal({
                type: "error",
                title: "¡No se pudo eliminar el despacho.",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              })
            }
          }
        })
      }

      ////////////////////////
      $('#dataDespTConcluido').on( 'click', 'tbody .ocurrenciaDespacho',function(){
        $("#btnGuardar").button('enable')
        var fchDespacho =$(this).attr("fchDespacho")
        var correlativo =$(this).attr("correlativo")
        var idProgramacion =$(this).attr("id")
        elDespacho = " IdProgramacion: " + idProgramacion + ", Fch-Correlativo: " + fchDespacho + "-" + correlativo
        var parametros = {
          "fchDespacho":fchDespacho,
          "correlativo":correlativo,
          "idProgramacion":idProgramacion
        };

        //alert(idProgramacion)
        $.ajax({
          data: parametros,
          url:  './vistas/ajaxVarios_v2.php?opc=prepararTripulacionDespacho',
          timeout: 15000,
          type:  'post',
          success:  function (response) {
            //alert(response)
            //var obj = JSON.parse(response);
            $("#elDespacho").val(elDespacho) 
            $("#fchDespachoOcurr").val(fchDespacho) 
            $("#correlativoOcurr").val(correlativo) 
            $("#tblOcurrTrip").html(response)
            $(".fchOcurrenciaTrip").datepicker({
              changeMonth: true,
              changeYear: true,
              showButtonPanel: true,
              minDate: "0M +0D",
              dateFormat:"yy-mm-dd" 
            });
            dlgOcurrenciaDespacho.dialog( "open" )
          }
        })
      });

      
      dlgOcurrenciaDespacho = $( "#dlgOcurrenciaDespacho" ).dialog({
        autoOpen: false,
        resizable: false,
        height: 400,
        width: 560,
        modal: true,
        buttons:[
          {
            id: "btnGuardar",
            text: "Guardar",
            click: function(){
              guardarOcurrenciaDespacho()
            }
          },
          {
            id: "btnCerrar",
            text: "Cerrar",
            click: function() {
              $( this ).dialog( "close" );
            }
          }
        ]
        
        /*buttons: {
          "Guardar": guardarOcurrenciaDespacho,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }*/
      });

      function guardarOcurrenciaDespacho(){        
       
        if( $("#cmbTipoOcurrencia").val() == "-" ){
          alert("Falta elegir Tipo Ocurrencia")
          return false;
        }

        if( $("#cmbTipoDoc").val() == "-" ){
          alert("Indique el tipo de documento")
          return false;
        }

        if( $("#cmbTipoDoc").val() == "01" && $("#txtNroDoc").val() == "" ){
          alert("Ingrese el nro. del documento")
          $("#txtNroDoc").focus()
          return false;
        }

        if($("#txtMntTotal").val() <= 0){
          alert("El monto total debe ser mayor a cero")
          $("#txtMntTotal").focus()
          return false;
        }

        if($("#txtMntDistribuir").val() <= 2){
          alert("Aqui no se ingresa nro de partes")
          $("#txtMntDistribuir").focus()
          return false;
        }

        if(1*$("#txtMntTotal").val() < 1*$("#txtMntDistribuir").val()){
          alert("El monto a distribuir no puede ser mayor al monto total")
          $("#txtMntDistribuir").focus()
          return false;
        }

        $("#btnGuardar").button('disable')

        var datos = new FormData($("#formOcurrenciaDespacho")[0]);
        $.ajax({
          data:datos,
          url: './vistas/ajaxVarios_v2.php?opc=guardarOcurrenciaDespacho',
          timeout:15000,
          method: "POST",
          processData: false, 
          contentType: false,
          success: function(response){
            if (response == 1){      
              $("#dlgOcurrenciaDespacho").dialog( "close" )
              swal({
                type: "success",
                title: "¡La ocurrencia se registró satisfactoriamente!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                  //$('#dataGuiasPorte').DataTable().ajax.reload();
                  t.draw()
              });
            } else {
              swal({
                type: "error",
                title: "¡Ha ocurrido un error inesperado. Revise los resultados!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              })
            }
          }
        })  
      }

      $("#autogenera").hide();

      $("#cmbTipoDoc").change(function () {
        var tipoDocMoy = $(this).val();
        var tipoDoc =  tipoDocMoy.substring(0,2)
       
        if(tipoDoc == '00'){         
          $("#autogenera").show();
          $("#txtNroDoc").hide();
          //$(".cargaPersonal").hide();
        } else {                      //Oficina
          $("#txtNroDoc").show();
          $("#autogenera").hide();
        }      
      })

      $("#cmbTipoOcurrencia").change(function () {   
        $("#subTipoItem").val($("#cmbTipoOcurrencia :selected").text()) ;
        //alert($("#subTipoItem").val())
      })

      ////////////////////////
      $('#dataDespTConcluido').on( 'click', 'tbody .pasarEstadoAnterior',function(){
        
        var fchDespacho =$(this).attr("fchDespacho")
        var correlativo =$(this).attr("correlativo")
        var idProgramacion =$(this).attr("id")
        //alert(fchDespacho)
        $("#txtFchDepachoVaT").val(fchDespacho)
        $("#txtCorrelativoVaT").val(correlativo)
        $("#txtIdProgramacionVaT").val(idProgramacion)

        $("#fchDespachoVaT").val(fchDespacho)
        $("#correlativoVaT").val(correlativo)

        var parametros = {
          "fchDespacho":fchDespacho,
          "correlativo":correlativo,
          "idProgramacion":idProgramacion,
        };
        $.ajax({
          data: parametros,
          url:  './vistas/ajaxVarios_v2.php?opc=validarPasarEstadoAnterior',
          timeout: 150000,
          type:  'post',
          success:  function (response) {
            var obj = JSON.parse(response);

            $("#txtSobreTripulacionVaT").val(obj.rptaPersonal)
            $("#txtSobreCobranzasVaT").val(obj.rptaCobranza)
            $("#txtSobreTercerosVaT").val(obj.rptaVehTer)

            $("#txtSobreOcurrVaT").val(obj.rptaOcurr)
            $("#txtSobreOcurrTerVaT").val(obj.rptaOcurrTer)
            if(obj.deshabilitar == "Si" ) $("#btnProceder").button("disable");
            else $("#btnProceder").button("enable")

            dlgValidadoATerminado
                .dialog({
                  title: "Volver a Terminado. Fch Desp.: Correl.: "+ fchDespacho + " : "+ correlativo + ", IdProgram.: " + idProgramacion
                })
                .dialog('open');
          }  
        });   
      });

      ///    
      dlgValidadoATerminado = $( "#dlgValidadoATerminado" ).dialog({
        autoOpen: false,
        resizable: false,
        height: 330,
        width: 500,
        modal: true,
        
        buttons: [
          {
            id   : "btnProceder",
            text : "Proceder",
            click: function(){
              pasarValidadoATerminado()
            },
          },
          {
            id   : "btnCancel",
            text : "Cerrar",
            click: function(){
              $( this ).dialog( "close" );
            },
          },
        ]
      });

      function pasarValidadoATerminado(){
        var datos = new FormData($("#formValidadoATerminado")[0]);
        $.ajax({
          data:datos,
          url: './vistas/ajaxVarios_v2.php?opc=pasarValidadoATerminado',
          timeout:15000,
          method: "POST",
          processData: false,
          contentType: false,
          success: function(response){
            if(response == -1){
              //Ya existen documentos asociados
              swal({
                type: "error",
                title: "¡Hay inconsistencia en la clave primaria!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              })
            } else if (response == 1){      
              $("#dlgValidadoATerminado").dialog( "close" )
              swal({
                type: "success",
                title: "¡El proceso se realizó satisfactoriamente!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                  //$('#dataGuiasPorte').DataTable().ajax.reload();
                  tValid.draw()
              });
            } else {
              swal({
                type: "error",
                title: "¡Ha ocurrido un error inesperado. Revise los resultados!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              })
            }
          }
        })
      }

      ///

      ////////////////////////

      $("#txtValPlaca").on('change', function(){
        $("#pl").val("Si")
      })

      $("#txtValHraAdic").on('change', function(){
        $("#vha").val("Si")
      })      

      
      $('#refrescarDespacho').on( 'click', function(){
        valCuenta    = $("#txtValCuenta").val()
        valProducto  = $("#txtValProducto").val()
        valPrecioBase = $("#txtValPrecioBase").val()
        valPrecBase2 = $("#txtValPrecBase2").val()
        valPlaca     = $("#txtValPlaca").val()
        valFchDesp   = $("#txtValFchDespacho").val()
        valCorrel    = $("#txtValCorrelativo").val()

        valHraIniBase = $("#txtValHraInicioBase").val()
        valHraIni    = $("#txtValHraInicio").val()
        valLugFinCli = $("#cmbValLugarFinCli").val()
        valHraFinCli = $("#txtValHraFinCliente").val()
        valHraFin    = $("#txtValHraFin").val()
        valFchFinCli = $("#txtValFchFinCli").val()
        valFchFin    = $("#txtValFchFin").val()
        valKmInicio  = $("#txtValKmInicio").val()
        valKmIniCli  = $("#txtValKmInicioCliente").val()
        valKmFinCli  = $("#txtValKmFinCliente").val()
        valKmFin     = $("#txtValKmFin").val()
        valUnTer    = $("#txtValValorUnTer").val()
        valTotTer   = $("#txtValTotUnTer").val()
        valHraAdic  = $("#txtValHraAdic").val()
        //valUnidad
        valAuxAdic  = $("#txtValAuxAdicional").val()//Es el número de auxiliares adicionales
        valValorAuxAdic  = $("#txtValValorAuxAdic").val()//Es el valor del auxiliar adicional
        valReten = $("#cmbValReten").val()
        cambioPlaca = $("#pl").val()
        cambioValHraAdic = $("#vha").val()
        var parametros = {
          "valCuenta":valCuenta,
          "valProducto":valProducto,
          "valPlaca" :valPlaca,
          "cambioPlaca":cambioPlaca,
          "cambioValHraAdic":cambioValHraAdic,
          "valFchDesp":valFchDesp,
          "valCorrel":valCorrel,
          "valHraIniBase":valHraIniBase,
          "valHraIni":valHraIni,
          "valLugFinCli":valLugFinCli,
          "valHraFinCli":valHraFinCli,
          "valHraFin":valHraFin,
          "valFchFinCli":valFchFinCli,
          "valFchFin":valFchFin,
          "valKmInicio":valKmInicio,
          "valKmIniCli":valKmIniCli,
          "valKmFinCli":valKmFinCli,
          "valKmFin":valKmFin,
          "valHraAdic":valHraAdic,
        };

        $.ajax({
          data: parametros,
          url: './vistas/ajaxVarios_v2.php?opc=refrescarValDespDatosGen',
          timeout: 15000,
          type:  'post',
          /*method: "POST",
          processData: false, 
          contentType: false,*/
          success: function(response){
            //alert(response)
            var obj = JSON.parse(response);
            
            //alert(valReten)
            if( valPrecioBase == valPrecBase2 || valPrecioBase == ''){
              $("#txtValPrecioBase").val(obj.precioBaseServicio)
              $("#txtValPrecBase2").val(obj.precioBaseServicio)
            } else {
              $("#txtValPrecBase2").val(valPrecioBase)
            }
            if(obj.esPlacaPropia == "Si"){
              $("#txtValUnidTerc").val(0)
              $("#txtValValorUnTer").val(0)
              $("#txtValTotUnTer").val(0)
            } else {
              if( valUnTer == valTotTer || valUnTer == ''){
                $("#txtValUnidTerc").val(1)
                $("#txtValValorUnTer").val(obj.valUnidadTercero)
                $("#txtValTotUnTer").val(obj.valUnidadTercero)
              } else {
                $("#txtValTotUnTer").val(valUnTer)
              }
            }
            if(valReten === null) valReten = 'No'
            if(valReten != "" && valReten != 'No' && valNroAuxSinReten > 0 ) valNroAuxSinReten = 1*obj.nroAuxDesp - 1
            else valNroAuxSinReten = 1*obj.nroAuxDesp
            //alert("prueba")

            if(valValorAuxAdic == '' && valValorAuxAdic == 0 ) valValorAuxAdicATomar = obj.valAuxiliarAdic
            else valValorAuxAdicATomar = valValorAuxAdic
            //  valValorAuxAdicATomar = obj.valAuxiliarAdic

            if(1*valNroAuxSinReten > 1*obj.nroAuxiliaresCuenta) valNroAuxAdic = 1*valNroAuxSinReten - 1*obj.nroAuxiliaresCuenta
            else valNroAuxAdic = 0

            valTotValorAuxAdic = valNroAuxAdic * valValorAuxAdicATomar
            
            $("#txtValAuxNoReten").val(valNroAuxSinReten)
            $("#txtValAuxEsperados").val(obj.nroAuxiliaresCuenta)
            $("#txtValAuxAdicional").val(valNroAuxAdic)
            $("#txtValValorAuxAdic").val(valValorAuxAdicATomar)
            $("#txtValTotAuxAdic").val(valTotValorAuxAdic)

            $("#txtValHrasNormales").val(obj.hrasNormales)
            $("#txtValKmEsperado").val(obj.kmEsperado)
            $("#txtValTpoAdic").val(obj.tpoExtraHrasDecimalTrab)
            $("#txtValKmTotal").val(obj.kmFacturable)

            $("#txtValKmAdicional").val(obj.kmAdicional)
            $("#txtValValorKmAdic").val(obj.valKmAdic)
            $("#txtValTotKmAdic").val(obj.valTotKmAdic)

            $("#txtValHraAdic").val(obj.valHraAdic)
            $("#txtValTotValHraAdic").val(obj.valTotValHraAdic)
            $("#txtDuracionhhmmss").val(obj.duracionhhmmss)
            //alert(obj.duracionhhmmss)
            $("#txtValTercHraExtraTotal").val(obj.valTercHraExtraTotal)

            $("#txtValDurAdicTerc").val(obj.valDurAdicTerc)
            $("#txtValHraAdicTercAdic").val(obj.valHraAdicTercAdic)

            $("#txtValTercPersAdic").val(obj.valTercPersAdic)
            $("#txtValTercValorAuxTerc").val(obj.valTercValorAuxTerc)
            $("#txtValTercPersAdicTotal").val(obj.valTercPersAdicTotal)        

          }
        })
      })


      $('#dataTrip').on( 'click', '.editar', function(){
        var idTrabajador = $(this).attr("id")
        var fchDespacho = $(this).attr("fchDespacho")
        var correlativo = $(this).attr("correlativo")
        var estadoDesp = $(this).attr("estadoDesp")
        $("#estadoDespEditaTrip").val(estadoDesp)
        var datos = new FormData();
        datos.append("idTrabajador", idTrabajador);
        datos.append("fchDespacho", fchDespacho);
        datos.append("correlativo", correlativo);
        $.ajax({
          data:datos,
          url: './vistas/ajaxVarios_v2.php?opc=buscarDatosTripulante',
          timeout:15000,
          method: "POST",
          processData: false, 
          contentType: false,
          success: function(response){
            var obj = JSON.parse(response);
            $("#cmbEditaTrabTipoRol").val(obj.tipoRol)
            $("#tipoRolOrig").val(obj.tipoRol)
            $("#idTrabOrig").val(obj.idTrabajador)
            
            $("#txtEditaTrabajador").val(obj.nombCompleto)
            $("#txtEditaTrabHraInicio").val(obj.trabHraInicio)
            $("#txtEditaTrabFchFin").val(obj.trabFchDespachoFin)
            $("#txtEditaTrabHraFin").val(obj.trabHraFin)
            $("#txtEditaObservTrip").val(obj.observPersonal)
            $("#fchDespachoEditaTrip").val(obj.fchDespacho)
            $("#correlativoEditaTrip").val(obj.correlativo)
            dlgEditaTripulante.dialog( "open" );
          }
        })
      });


      dlgEditaTripulante = $( "#dlgEditaTripulante" ).dialog({
        autoOpen: false,
        resizable: false,
        height: 330,
        width: 500,
        modal: true,        
        buttons: {
          "Editar": editaTrip,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function editaTrip(){
        var datos = new FormData($("#formEditaTripulante")[0]);
        $.ajax({
          data:datos,
          url: './vistas/ajaxVarios_v2.php?opc=editaTrip',
          timeout:15000,
          method: "POST",
          processData: false, 
          contentType: false,
          success: function(response){      
            if (response == 1){      
              $("#dlgEditaTripulante").dialog( "close" )
              swal({
                type: "success",
                title: "¡El tripulante ha sido editado correctamente!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                  $('#dataTrip').DataTable().ajax.reload();
              });
            } else {
              swal({
                type: "error",
                title: "¡No se pudo editar el tripulante.",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              })
            }
          }
        })
      }

      $('#dataTrip').on( 'click', '.eliminar', function(){
        var nombTrab = $(this).parent().parent().children('.nombTrab').html()
        var tipoRol = $(this).parent().parent().children('.tipoRol').html()
        var fchDespacho = $(this).attr("fchDespacho")
        var correlativo = $(this).attr("correlativo")
        var idTrabajador = $(this).attr("id")
        var estadoDesp  = $(this).attr("estadoDesp")

        $("#txtElimTrabajador").val(nombTrab)
        $("#txtElimTrabTipoRol").val(tipoRol)
        $("#idTrabElimTrab").val(idTrabajador)
        $("#fchDespachoElimTrab").val(fchDespacho)
        $("#correlativoElimTrab").val(correlativo)
        $("#estadoDespElimTrab").val(estadoDesp)

        dlgEliminaTripulante.dialog( "open" );
      });

      dlgEliminaTripulante = $( "#dlgEliminaTripulante" ).dialog({
        autoOpen: false,
        resizable: false,
        height: 250,
        width: 400,
        modal: true,        
        buttons: {
          "Eliminar": eliminaTrip,
          Cerrar: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      function eliminaTrip(){
        var datos = new FormData($("#formEliminaTripulante")[0]);
        $.ajax({
          data:datos,
          url: './vistas/ajaxVarios_v2.php?opc=eliminaTrip',
          timeout:15000,
          method: "POST",
          processData: false, 
          contentType: false,
          success: function(response){      
            if (response == 1){      
              $("#dlgEliminaTripulante").dialog( "close" )
              swal({
                type: "success",
                title: "¡El tripulante ha sido eliminado correctamente!",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              }).then((result)=>{
                  $('#dataTrip').DataTable().ajax.reload();
              });
            } else {
              swal({
                type: "error",
                title: "¡No se pudo eliminar el tripulante.",
                showConfirmButton:true,
                confirmButtonText:"Cerrar",
                closeOnConfirm: false
              })
            }
          }
        })
      }

      $('.horas').timepicker({
        timeFormat: 'HH:mm:ss',
        stepHour: 1,
        stepMinute: 1,
        stepSecond: 10
      });

      $('#dataDespProgram tbody').on( 'click', '.descargaInfoDespacho', function(){
        var id = $(this).parent().parent().children('.idProgramacion').html()
        var f = $(this).parent().parent().children('.fchDespacho').html()
        var c = $(this).parent().parent().children('.correlativo').html()
        //alert(id)

        <?php if (PHP_VERSION_ID > 70000){   ?>
          abrirventana('vistas/descargarExcelInfoDespacho.php?id='+id+'&f='+f+'&c='+c)
        <?php  } else {   ?>
          abrirventana('vistas/descargarExcelInfoDespacho5_0.php?id='+id+'&f='+f+'&c='+c)
        <?php } ?>
      });

      $('#dataDespProgram tbody').on( 'click', '.pasarAEnRuta', function(){
        //alert("Pasar a en Ruta")
        var id = $(this).parent().parent().children('.idProgramacion').html()
        
        swal({
          type: "warning",
          title: 'Cambiar estado de\nId Programación: ' + id +'\n a EnRuta?',
          text: "Este cambio no se podrá revertir!",
          showCancelButton: true,
          //confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, cambiar estado!'
        }).then((result) => {
          if (result.value) {
            var datos = new FormData();
            datos.append("id", id);
            datos.append("es1", "Programado");
            datos.append("es2", "EnRuta");
            
            $.ajax({
              url:'./vistas/ajaxVarios_v2.php?opc=cambiarEstadoDespacho',
              method: "POST",
              data:datos,
              cache:false,
              contentType: false,
              processData: false,
              //dataType: "json",
              success: function(respuesta){

                registrarDespachoFirebase(id)

                $('#dataDespProgram').DataTable().ajax.reload();
                swal(
                  'Hecho!',
                  'Estado modificado.',
                  'success'
                )
              }          
            })
          }
        })
      });

      $('#descExcel').on('click', function(e){
        swal({
          type: "warning",
          title: 'Seguro que desea cambiar a EnRuta los despachos seleccionados?',
          text: "Este cambio no se podrá revertir!",
          showCancelButton: true,
          //confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, cambiar estado!'
        }).then((result) => {
          if (result.value) {
            var rows_selected = tProg.column(0).checkboxes.selected();
            $.each(rows_selected, function(index, rowId){
              var datos = new FormData();
              datos.append("id", rowId);
              datos.append("es1", "Programado");
              datos.append("es2", "EnRuta");
              $.ajax({
                url:'./vistas/ajaxVarios_v2.php?opc=cambiarEstadoDespacho',
                method: "POST",
                data:datos,
                cache:false,
                contentType: false,
                processData: false,
                success: function(respuesta){
                }
              })
              registrarDespachoFirebase(rowId)
                         
            });
            swal({
              title: "Procesando!",
              text: "Esta ventana se cerrará en unos 10 segundos.",
              timer: 9000,
              showConfirmButton: false
            }).then((result) => {
              $('#dataDespProgram').DataTable().ajax.reload();
            })
          }
        })
      })

      $('#txtHraEsperada').timepicker({});
      $('#txtNvoTrabHraInicio').timepicker({});
      $('#txtNvoTrabHraFin').timepicker({});
      $("#txtNvoTrabFchFin").datepicker({
        showButtonPanel: true,
        minDate: "0M -15D",
        maxDate: "0M +15D",
        dateFormat:"yy-mm-dd"
      });

      $('#txtNvoTrabajador').autocomplete({
        minLength: 2,
        source : 'variosAjax.php?opc=trabajParaAuxiliar'
        //source : ['Maria','Jose','Rocio']  
      });

      $('#txtEditaTrabajador').autocomplete({
        minLength: 2,
        source : 'variosAjax.php?opc=trabajParaAuxiliar'
        //source : ['Maria','Jose','Rocio']  
      });
      
      $('#infValTrip  #dataValTrip ').on( 'click', 'tbody .editar ',  function(){
        var idTrabajador = $(this).attr("id")
        //alert(idTrabajador)
        $('#vHrIni'+idTrabajador).prop('readonly', false);
        $('#vFcFin'+idTrabajador).prop('readonly', false);
        $('#vHrFin'+idTrabajador).prop('readonly', false);
        $('#vRol'+idTrabajador).prop('readonly', false);
        $('#vAdic'+idTrabajador).prop('readonly', false);
        $('#vHEx'+idTrabajador).prop('readonly', false);

      });

      $('#infValTrip  #dataValTrip ').on( 'click', 'tbody .refrescar ',  function(){
        var idTrabajador = $(this).attr("id")
        var fchDespacho = $(this).attr("fd")
        var correlativo = $(this).attr("c")
        var tipoRol  = $(this).attr("trl")

        var hraIni = $("#vHrIni"+ idTrabajador).val()
        var fchFin = $("#vFcFin"+ idTrabajador).val()
        //var hraIni = $("#vHrIni"+ idTrabajador).val()
        var hraFin = $("#vHrFin"+ idTrabajador).val()
        var vRol = $("#vRol"+ idTrabajador).val()
        var vAdic = $("#vAdic"+ idTrabajador).val()
        var vHEx = $("#vHEx"+ idTrabajador).val()

        var auxProducto = $("#txtValProducto").val()
        arrProducto = auxProducto.split("|")
        idProducto = arrProducto[0]
        //alert(hraFin)

        var parametros = {
          "fchDespacho":fchDespacho,
          "correlativo":correlativo,
          "idTrabajador":idTrabajador,
          "idProducto":idProducto,
          "tipoRol":tipoRol,
          "hraIni":hraIni,
          "fchFin":fchFin,
          "hraFin":hraFin,
          "vRol":vRol,
          "vAdic":vAdic,
          "vHEx":vHEx,
        };

        $.ajax({
          data: parametros,
          url:  './vistas/ajaxVarios_v2.php?opc=refrescarValidaTrip',
          timeout: 15000,
          type:  'post',
          success:  function (response) {
            var obj = JSON.parse(response);
            //idTrabajador =  'vRol".$value["idTrabajador"].
            $("#vRol"+ idTrabajador).val(obj.valorTrip)
            $("#vHEx"+ idTrabajador).val(obj.valTotHraExtra)

          }
        })  

      });

    })

  function descargarExcel(){
    alert("No implementado")
  }


  function registrarDespachoFirebase(idProgramacion){

    let firebaseApp = connectionFirebase();
    
    $.ajax({
      url:`./vistas/ajaxVarios_v2.php?opc=buscarDataDespachoIdProgram&idProgramacion=${idProgramacion}`,
      method: "GET",
      cache:false,
      contentType: false,
      processData: false,
      success: function(respuesta){
        respuesta = JSON.parse(respuesta)

        let {
          cuenta,
          idProgramacion,
          nombre,
          placa,
          fchDespacho,
          considerarPropio,
          estadoDespacho,
          idCliente,
          usuarioAsignado
        } = respuesta

        let id = parseInt(idProgramacion)

        let dispatchForFirebase = {
          cuenta,
          idProgramacion,
          nombre_cliente:nombre,
          placa,
          fchDespacho: new Date(fchDespacho+" 00:00:00").getTime(),
          considerarPropio,
          estadoDespacho,
          idCliente,
          conductor: usuarioAsignado
        }

        const dispatch = firebaseApp.database().ref(`/dispatch/`);	
        dispatch.child(id).set(dispatchForFirebase)
      }
    }) 
  }


  </script>  
  
</head>
<body>
   <?php require_once 'barramenu.php';  ?>
    <form name = 'frmFiltro' id = 'frmFiltro' method ='POST'>
      <table class = 'fullRD' >
        <tr> 
          <th  class = "pagina"  align="center">
            <img border="0" src="imagenes/moy.png" width="25" height="25" align="right" onclick =  "abrirventana('index.php?controlador=aplicacion&accion=verprincipal') ">

          <img class="sinTitulo" align="right" href="#" title="Cambiar a EnRuta" border="0" src="imagenes/enRutaGrande.png" width="25" height="25" align="right" id= 'descExcel' name= 'descExcel'>
          LISTADO DE DESPACHOS
        </th>
      </tr>
      </table>
    </form>
    
    <div id="dlgNuevaGuiaPorte" title="Nueva Guía Porte">
      <div style="padding: 5px;">
        <form name = "formNuevaGuiaPorte" id = "formNuevaGuiaPorte" method="POST" >
        <fieldset>
        <table border="0" width="180" id="table2">
          <tr>
            <th>Nro. Serie</th>
            <td>
              <input type="text" name="txtNvoGuiaPorteNroSerie" id="txtNvoGuiaPorteNroSerie" size="12" >
            </td>
          </tr>
          <tr>
            <th>Correlativo</th>
            <td>
              <input type="text" name="txtNvoGuiaPorteCorrelativo" id="txtNvoGuiaPorteCorrelativo" size="12" >
            </td>
          </tr>
        </table>
        <input type='hidden' name= 'fchDespachoNvoGuiaPte' id= 'fchDespachoNvoGuiaPte' value = '' >
        <input type='hidden' name= 'correlativoNvoGuiaPte' id= 'correlativoNvoGuiaPte' value = '' >
        </fieldset>
        </form>
      </div>
    </div>

    <div id="dlgEditaGuiaPorte" title="Edita Guía Porte">
      <div style="padding: 5px;">
        <form name = "formEditaGuiaPorte" id = "formEditaGuiaPorte" method="POST" >
        <fieldset>
        <table border="0" width="180" id="table2">
          <tr>
            <th>Nro. Serie</th>
            <td>
              <input type="text" name="txtEditaGuiaPorteNroSerie" id="txtEditaGuiaPorteNroSerie" size="12" >
            </td>
          </tr>
          <tr>
            <th>Correlativo</th>
            <td>
              <input type="text" name="txtEditaGuiaPorteCorrelativo" id="txtEditaGuiaPorteCorrelativo" size="12" >
            </td>
          </tr>
        </table>
        <input type='hidden' name= 'guiaPorteIni' id= 'guiaPorteIni' value = '' >
        </fieldset>
        </form>
      </div>
    </div>

    <div id="dlgEliminaGuiaPorte" title="Elimina Guía Porte">
      <div style="padding: 5px;">
        <form name = "formEliminaGuiaPorte" id = "formEliminaGuiaPorte" method="POST" >
        <fieldset>
        <table border="0" width="180" id="table2">
          <tr>
            <th>Nro. Serie</th>
            <td>
              <input type="text" name="txtEliminaGuiaPorteNroSerie" id="txtEliminaGuiaPorteNroSerie" size="12" >
            </td>
          </tr>
          <tr>
            <th>Correlativo</th>
            <td>
              <input type="text" name="txtEliminaGuiaPorteCorrelativo" id="txtEliminaGuiaPorteCorrelativo" size="12" >
            </td>
          </tr>
        </table>
        <input type='hidden' name= 'guiaPorteIniElim' id= 'guiaPorteIniElim' value = '' >
        </fieldset>
        </form>
      </div>
    </div>

    <!--Puntos -->
    <div id="dlgNuevoPunto" title="Nuevo Punto">
      <div style="padding: 5px;">
        <form name = "formNuevoPunto" id = "formNuevoPunto" method="POST" >
          <fieldset>
            <table border="0" width="780">
              <tr>
                <td width = '90' class = 'rojo'>Tipo de Punto</td>
                <td width = '250'>
                  <select name="cmbNuevoTipoPunto" id="cmbNuevoTipoPunto" style="padding: 5px; font-size: 13px; width: 225px;">
                    <option value = '-'>Elija el tipo de punto:</option>
                    <option value = 'Carga'>Carga</option>
                    <option value = 'Descarga'>Descarga</option>
                    <option value = 'Recojo'>Recojo</option>
                  </select>
                </td>
                <td width = '120' >Hra. Esperada</td>
                <td ><input style="padding: 5px; width: 225px;" type="text" id="txtHraEsperada" name="txtHraEsperada" ></td>
              </tr>
              <tr>
                <td class = 'rojo' >Guía Cliente</td>
                <td ><input style="padding: 5px; width: 225px;" type="text" id="txtGuiaCliente" name="txtGuiaCliente" ></td>

                <td class = 'rojo'>Nombre del Cliente</td>
                <td>
                  <input style="padding: 5px; width: 225px;" id="txtNuevoNombCli" name="txtNuevoNombCli" type="text"  placeholder="Nombre del Cliente" >
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Dirección Cliente</td>
                <td colspan="3" ><input style="padding: 5px; width: 600px" type="text" id="txtDirCliente" name="txtDirCliente" ></td>
              </tr>
              <tr>
                <td>Referencia Dirección</td>
                <td colspan="3" ><input style="padding: 5px; width: 600px" type="text" id="txtReferencia" name="txtReferencia" ></td>
              </tr>
              <tr>
                <td class = 'rojo'>Distrito del Cliente</td>
                <td>
                  <input style="padding: 5px; width: 225px;" id="txtNuevoDistCli" name="txtNuevoDistCli" type="text"  placeholder="Distrito del Cliente" >
                </td>
                <td class = 'rojo'>Provincia</td>
                <td>
                  <input style="padding: 5px; width: 225px;" id="txtNuevoProvCli" name="txtNuevoProvCli" type="text"  placeholder="Provincia del Cliente" value = 'Lima' readonly="" >
                </td>
              </tr>
              <tr>
                <td>Telefono</td>
                <td>
                  <input style="padding: 5px; width: 225px;" id="txtNuevoTelfCli" name="txtNuevoTelfCli" type="text"  placeholder="Teléfono del Cliente" >
                </td>
                <td>Id Carga</td>
                <td>
                  <input style="padding: 5px; width: 225px;" id="txtNuevoIdCargaCli" name="txtNuevoIdCargaCli" type="text"  placeholder="Id Carga" >
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Estado del Punto</td>
                <td>
                  <select name="cmbNuevoPuntoEstado" id="cmbNuevoPuntoEstado" style="padding: 5px; font-size: 13px; width: 225px;" >
                  </select>
                </td>
                <td class = 'rojo'>Subestado del Punto</td>
                <td>
                  <select name="cmbNuevoPuntoSubestado" id="cmbNuevoPuntoSubestado" style="padding: 5px; font-size: 13px; width: 225px;">
                  </select>
                </td>
              </tr>
              <tr>
                <td>Observación</td>
                <td>
                  <textarea rows="2" name="txtObservacion" cols="25"></textarea>
                </td>
              </tr>    
              <tr>
                <td>.</td>
              </tr>
            </table>
          </fieldset>
          <input type='hidden' name= 'fchDespachoNvoPunto' id= 'fchDespachoNvoPunto' value = '' >
          <input type='hidden' name= 'correlativoNvoPunto' id= 'correlativoNvoPunto' value = '' >
          <input type='hidden' name= 'idProgramacionNvoPunto' id= 'idProgramacionNvoPunto' value = '' >
          <input type='hidden' name= 'estadoDespNvoPunto' id= 'estadoDespNvoPunto' value = '' >
          
        </form>
      </div>
    </div>

    <div id="dlgEditaPunto" title="Edita Punto">
      <div style="padding: 5px;">
        <form name = "formEditaPunto" id = "formEditaPunto" method="POST" >
          <fieldset>
            <table border="0" width="100%">
              <tr>
                <td width = '90' class = 'rojo'>Tipo de Punto</td>
                <td width = '180'>
                  <select name="cmbEditaTipoPunto" id="cmbEditaTipoPunto" style="padding: 5px; font-size: 13px; width: 180px;">
                    <option value = '-'>Elija el tipo de punto:</option>
                    <option value = 'Carga'>Carga</option>
                    <option value = 'Descarga'>Descarga</option>
                    <option value = 'Recojo'>Recojo</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Nombre Cliente</td>
                <td>
                  <input style="padding: 5px; width: 180px" id="txtEditaPuntoNombCli" name="txtEditaPuntoNombCli" type="text"  placeholder="Nombre del Cliente" >
                </td>
                <td width="90" class = 'rojo'>Distrito Cliente</td>
                <td>
                  <input style="padding: 5px; width: 180px" id="txtEditaPuntoDist" name="txtEditaPuntoDist" type="text"  placeholder="Distrito del Cliente" readonly>
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Estado del Punto</td>
                <td>
                  <select name="cmbEditaPuntoEstado" id="cmbEditaPuntoEstado" style="padding: 5px; font-size: 13px; width: 180px;">
                  </select>
                </td>
                <td class = 'rojo'>Subestado Punto</td>
                <td>
                  <select name="cmbEditaPuntoSubestado" id="cmbEditaPuntoSubestado" style="padding: 5px; font-size: 13px; width: 180px;">
                  </select>
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Hora Llegada</td>
                <td>
                  <input style="padding: 5px; width: 180px" id="txtEditaHraLlegada" name="txtEditaHraLlegada" type="text"  placeholder="Hora de Llegada" >
                </td>
                <td class = 'rojo'>Hora Salida</td>
                <td>
                  <input style="padding: 5px; width: 180px" id="txtEditaHraSalida" name="txtEditaHraSalida" type="text"  placeholder="Hora de Salida" >
                </td>
              </tr>
              <tr>
                <td>Observación</td>
                <td colspan="3">
                  <textarea rows="2" name="txtEditaObservacion" id="txtEditaObservacion" cols="60"></textarea>
                </td>
              </tr>
              <tr>
                <td>.</td>
              </tr>
            </table>
          </fieldset>
          <input type='hidden' name= 'idPuntoEdita' id= 'idPuntoEdita' value = '' >
          <input type='hidden' name= 'estadoDespEditatPto' id= 'estadoDespEditatPto' value = '' >
        </form>
      </div>
    </div>

    <div id="dlgEliminaPunto" title="Elimina Punto">
      <div style="padding: 5px;">
        <form name = "formEliminaPunto" id = "formEliminaPunto" method="POST" >
          <fieldset>
            <table border="0" width="400">
              <tr>
                <td width = '130' class = 'rojo'>Correl. Punto</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtEliminaIdPunto" name="txtEliminaIdPunto" type="text"  readonly >
                </td>
              </tr>
              <tr>
                <td width = '130' class = 'rojo'>Tipo de Punto</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtEliminaPuntoTipo" name="txtEliminaPuntoTipo" type="text"  readonly >
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Nombre del Cliente</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtEliminaPuntoNombCli" name="txtEliminaPuntoNombCli" type="text"  readonly >
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Distrito del Cliente</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtEliminaPuntoDist" name="txtEliminaPuntoDist" type="text"  readonly >
                </td>
              </tr>
            </table>
          </fieldset>
        </form>
      </div>
    </div>

    <div id="dlgVerFotos" title="Ver Fotos">
    </div>

    <div id="dlgTraspasarPunto" title="Traspasar Punto">
      <div style="padding: 5px;">
        <form name = "formTraspasarPunto" id = "formTraspasarPunto" method="POST" >
          <fieldset>
            <table border="0" width="400">
              <tr>
                <td width = '130' class = 'rojo'>Id Punto</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtTraspasaIdPunto" name="txtTraspasaIdPunto" type="text"  readonly >
                </td>
              </tr>
              <tr>
                <td width = '130' class = 'rojo'>Tipo de Punto</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtTraspasaPuntoTipo" name="txtTraspasaPuntoTipo" type="text"  readonly >
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Nombre del Cliente</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtTraspasaPuntoNombCli" name="txtTraspasaPuntoNombCli" type="text"  readonly >
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Distrito del Cliente</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtTraspasaPuntoDist" name="txtTraspasaPuntoDist" type="text"  readonly >
                </td>
              </tr>

              <tr>
                <td class = 'rojo'>Destino</td>
                <td>
                  <select name="cmbTraspasarDespachoDestino" id="cmbTraspasarDespachoDestino" style="padding: 5px; font-size: 13px; width: 250px;">
                  </select>
                </td>
              </tr>
            </table>
          </fieldset>
          <input type='hidden' name= 'fchDespachoEliminaPunto' id= 'fchDespachoEliminaPunto' value = '' >
          <input type='hidden' name= 'correlativoEliminaPunto' id= 'correlativoEliminaPunto' value = '' >
        </form>
      </div>
    </div>

    <!--Fin puntos -->

    <!--Tripulación -->
    <div id="dlgNuevoTripulante" title="Nuevo Tripulante">
      <div style="padding: 5px;">
        <form name = "formNuevoTripulante" id = "formNuevoTripulante" method="POST" >
          <fieldset>
            <table border="0" width="400">
              <tr>
                <td width = '130' class = 'rojo'>Tipo de Rol</td>
                <td>
                  <select name="cmbNvoTrabTipoRol" id="cmbNvoTrabTipoRol" style="padding: 5px; font-size: 13px; width: 250px;">
                    <option value = '-'>Elija el tipo de rol:</option>
                    <option>Conductor</option>
                    <option>Auxiliar</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Trabajador</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtNvoTrabajador" name="txtNvoTrabajador" type="text"  placeholder="Nombre del Trabajador" >
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Hra. Inicio</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtNvoTrabHraInicio" name="txtNvoTrabHraInicio" type="text"  placeholder="Hora Inicio" >
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Fch. Fin</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtNvoTrabFchFin" name="txtNvoTrabFchFin" type="text"  placeholder="Fecha Fin" >
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Hra. Fin</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtNvoTrabHraFin" name="txtNvoTrabHraFin" type="text"  placeholder="Hora Fin" >
                </td>
              </tr>
              <tr>
                <td>Observación</td>
                <td>
                  <textarea rows="2" name="txtObservacion" cols="40"></textarea>
                </td>
              </tr>    
              <tr>
                <td>.</td>
              </tr>
            </table>
          </fieldset>
          <input type='hidden' name= 'fchDespachoNvoTrip' id= 'fchDespachoNvoTrip' value = '' >
          <input type='hidden' name= 'correlativoNvoTrip' id= 'correlativoNvoTrip' value = '' >
          <input type='hidden' name= 'estadoDespNvoTrip' id= 'estadoDespNvoTrip' value = '' >
          
        </form>
      </div>
    </div>

    <div id="dlgEditaTripulante" title="Edita Tripulante">
      <div style="padding: 5px;">
        <form name = "formEditaTripulante" id = "formEditaTripulante" method="POST" >
          <fieldset>
            <table border="0" width="400">
              <tr>
                <td width = '130' class = 'rojo'>Tipo de Rol</td>
                <td>
                  <select name="cmbEditaTrabTipoRol" id="cmbEditaTrabTipoRol" style="padding: 5px; font-size: 13px; width: 250px;">
                    <option value = '-'>Elija el tipo de rol:</option>
                    <option>Conductor</option>
                    <option>Auxiliar</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Trabajador</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtEditaTrabajador" name="txtEditaTrabajador" type="text"  placeholder="Nombre del Trabajador" >
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Hra. Inicio</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtEditaTrabHraInicio" name="txtEditaTrabHraInicio" type="text"  placeholder="Hora Inicio" >
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Fch. Fin</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtEditaTrabFchFin" name="txtEditaTrabFchFin" type="text"  placeholder="Fecha Fin" >
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Hra. Fin</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtEditaTrabHraFin" name="txtEditaTrabHraFin" type="text"  placeholder="Hora Fin" >
                </td>
              </tr>
              <tr>
                <td>Observación</td>
                <td>
                  <textarea rows="2" id ="txtEditaObservTrip" name="txtEditaObservTrip" cols="40"></textarea>
                </td>
              </tr>
            </table>
          </fieldset>
          <input type='hidden' name= 'fchDespachoEditaTrip' id= 'fchDespachoEditaTrip' value = '' >
          <input type='hidden' name= 'correlativoEditaTrip' id= 'correlativoEditaTrip' value = '' >
          <input type='hidden' name= 'estadoDespEditaTrip' id= 'estadoDespEditaTrip' value = '' >
          <input type='hidden' name= 'tipoRolOrig' id= 'tipoRolOrig' value = '' >
          <input type='hidden' name= 'idTrabOrig' id= 'idTrabOrig' value = '' >
        </form>
      </div>
    </div>

    <div id="dlgEliminaTripulante" title="Elimina Tripulante">
      <div style="padding: 5px;">
        <form name = "formEliminaTripulante" id = "formEliminaTripulante" method="POST" >
          <fieldset>
            <table border="0" width="350">
              <tr>
                <td class = 'rojo'>Trabajador</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtElimTrabajador" name="txtElimTrabajador" type="text" readonly >
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Tipo Rol</td>
                <td>
                  <input style="padding: 5px; width: 250px" id="txtElimTrabTipoRol" name="txtElimTrabTipoRol" type="text" readonly >
                </td>
              </tr>   
              <tr>
                <td>.</td>
              </tr>
            </table>
          </fieldset>
          <input type='hidden' name= 'idTrabElimTrab' id= 'idTrabElimTrab' value = '' >
          <input type='hidden' name= 'fchDespachoElimTrab' id= 'fchDespachoElimTrab' value = '' >
          <input type='hidden' name= 'correlativoElimTrab' id= 'correlativoElimTrab' value = '' >
          <input type='hidden' name= 'estadoDespElimTrab' id= 'estadoDespElimTrab' value = '' >
        </form>
      </div>
    </div>
    <!--Fin Tripulación -->

    <!--Validar Despacho -->
    <div id="dlgValDespacho">
      <div style="padding: 5px;">
        <form name = "formValDespacho" id = "formValDespacho" method="POST" >
          <fieldset>
            <div id = "tabValDesp">
              <ul>
                <li><a href="#tabValDespInfo">Inf. General</a></li>
                <li><a href="#tabValDespDatosGen">Despacho</a></li>
                <li><a href="#tabValDesTrip">Tripulación</a></li>
                <li><a href="#tabValDesGuiasPorte">Guías Porte</a></li>
                <li><a href="#tabValDesPuntos">Puntos</a></li>
              </ul>

              <div id= "tabValDespInfo">
                <table>
                  <tr>
                    <td >Fch. Despacho</td>
                    <td ><input size = '12' type="text" name="txtValFchDespacho" id="txtValFchDespacho" readonly></td>
                    <td>Correlativo</td>
                    <td><input size = '12' type="text" name="txtValCorrelativo" id="txtValCorrelativo" readonly></td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td>Cliente</td><td colspan = '5'>
                      <input size = '67' type="text" name="txtValCliente" id="txtValCliente" readonly>
                    </td>
                  </tr>
                  <tr>
                    <td>IdProgramacion</td>
                    <td><input size = '12' type="text" name="txtValIdProgramacion" id="txtValIdProgramacion" readonly></td>
                    <td>Estado</td>
                    <td>
                      <input size = '12' type="text" name="txtValEstadoDespacho" id="txtValEstadoDespacho" readonly>
                    </td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td width = '85 px'>Modo Creación</td>
                    <td width = '110 px'><input size = '12' type="text" name="txtValModoCreacion" id="txtValModoCreacion" readonly></td>
                    <td width = '85 px'>Fch. Creación</td>
                    <td width = '110 px'><input size = '12' type="text" name="txtValFchCreacion" id="txtValFchCreacion" readonly></td>              
                    <td width = '85 px'>Usuario Creac.</td>
                    <td><input size = '12' type="text" name="txtValUsuario" id="txtValUsuario" readonly></td>
                  </tr>

                  <tr>
                    <td width = '85 px'>Modo Terminado</td>
                    <td width = '110 px'><input size = '12' type="text" name="txtValModoTerm" id="txtValModoTerm" readonly></td>
                    <td width = '85 px'>Fch. Terminado</td>
                    <td width = '110 px'><input size = '12' type="text" name="txtValFchTerm" id="txtValFchTerm" readonly></td>
                    <td width = '85 px'>Usuario Termi.</td>
                    <td><input size = '12' type="text" name="txtValUsuarioTerm" id="txtValUsuarioTerm" readonly></td>
                  </tr>


                  <tr>
                    <td width = '85 px'>Modo Concluído</td>
                    <td width = '110 px'><input size = '12' type="text" name="txtValModoConclu" id="txtValModoConclu" readonly></td>
                    <td width = '85 px'>Fch. Concluído</td>
                    <td width = '110 px'><input size = '12' type="text" name="txtValFchConclu" id="txtValFchConclu" readonly></td>
                    <td width = '85 px'>Usuario Concl.</td>
                    <td><input size = '12' type="text" name="txtValUsuarioConclu" id="txtValUsuarioConclu" readonly></td>
                    <td width = '85 px'>Hra Concluído</td>
                    <td><input size = '12' type="text" name="txtValHraConclu" id="txtValHraConclu" readonly></td>
                  </tr>
                </table>
              </div>

              <div id= "tabValDespDatosGen" style="padding-left: 5px; padding-right: 5px;">
                <table width="100%" border="0" >
                  <tr>
                    <td width = '75 px'>Cuenta</td>
                    <td colspan="3" >
                      <select name="txtValCuenta" id="txtValCuenta" style="position: relative; width: 270">
                      </select>
                    </td>
                    <td rowspan="11" valign="top">
                      <div class= 'resultado'>
                        <table width="100%" border = '0'>
                          <tr>
                            <td colspan="7">
                              <b> Resultados del Despacho </b>
                            </td>
                          </tr>
                          <tr>
                            <td width="108px"></td>
                            <td width="75px"></td>
                            <td width="108px">Precio Base Serv.</td>
                            <td width="75px">
                              <input size = '7' type="text" name="txtValPrecioBase" id="txtValPrecioBase" >
                            </td>
                            <td width="40px">------></td>
                            <td width="75px">
                              <input size = '7' type="text" name="txtValPrecBase2" id="txtValPrecBase2" readonly></td>
                            <td></td>
                          </tr>
                          <tr>
                            <td>Duración Total</td>
                            <td>
                              <input size = '7' type="text" name="txtDuracionhhmmss" id="txtDuracionhhmmss" readonly>
                            </td>
                            <td>Hras. Normales</td>
                            <td>
                              <input size = '7' type="text" name="txtValHrasNormales" id="txtValHrasNormales" readonly>
                            </td>
                            <td colspan="3"></td>
                          </tr>
                          <tr>
                            <td>Durac. Adicional</td>
                            <td>
                              <input size = '7' type="text" name="txtValTpoAdic" id="txtValTpoAdic" readonly>
                            </td>
                            <td>Val. Hr. Adic.</td>
                            <td>
                              <input size = '7' type="text" name="txtValHraAdic" id="txtValHraAdic">
                            </td>
                            <td>------></td>
                            <td><input size = '7' type="text" name="txtValTotValHraAdic" id="txtValTotValHraAdic" readonly></td>
                            <td></td>
                          </tr>
                          <tr>
                            <td>Km. Facturable</td>
                            <td>
                              <input size = '7' type="text" name="txtValKmTotal" id="txtValKmTotal" readonly>
                            </td>
                            <td>Km. Esperado</td>
                            <td>
                              <input size = '7' type="text" name="txtValKmEsperado" id="txtValKmEsperado" readonly>
                            </td>
                            <td colspan="3"></td>
                          </tr>
                          <tr>
                            <td>Km. Adicional</td>
                            <td>
                              <input size = '7' type="text" name="txtValKmAdicional" id="txtValKmAdicional" readonly></td>
                            <td>Val. Km. Adicional</td>
                            <td>
                              <input size = '7' type="text" name="txtValValorKmAdic" id="txtValValorKmAdic" readonly>
                            </td>
                            <td>------></td>
                            <td><input size = '7' type="text" name="txtValTotKmAdic" id="txtValTotKmAdic" readonly></td>
                            <td></td>
                          </tr>
                          <tr>
                            <td>Auxiliares Total</td>
                            <td>
                              <input size = '7' type="text" name="txtValAuxTotal" id="txtValAuxTotal" readonly></td>
                            <td>Auxiliares Retén</td>
                            <td>
                              <input size = '7' type="text" name="txtValAuxReten" id="txtValAuxReten" readonly>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                          </tr>
                          <tr>
                            <td>Auxiliares (No Retén)</td>
                            <td>
                              <input size = '7' type="text" name="txtValAuxNoReten" id="txtValAuxNoReten" readonly></td>
                            <td>Auxiliares Esperados</td>
                            <td>
                              <input size = '7' type="text" name="txtValAuxEsperados" id="txtValAuxEsperados" readonly>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                          </tr>
                          <tr>
                            <td>Auxiliar Adicional</td>
                            <td>
                              <input size = '7' type="text" name="txtValAuxAdicional" id="txtValAuxAdicional" readonly></td>
                            <td>Val. Aux. Adic.</td>
                            <td>
                              <input size = '7' type="text" name="txtValValorAuxAdic" id="txtValValorAuxAdic">
                            </td>
                            <td>------></td>
                            <td><input size = '7' type="text" name="txtValTotAuxAdic" id="txtValTotAuxAdic" readonly></td>
                            <td></td>
                          </tr>
                          <tr>
                            <td colspan="7">
                              <b>Resultados sobre uso de terceros</b>
                            </td>
                          </tr>
                          <tr>
                            <td>Unidad Tercero</td>
                            <td>
                              <input size = '7' type="text" name="txtValUnidTerc" id="txtValUnidTerc" readonly></td>
                            <td>Val Unidad</td>
                            <td>
                              <input size = '7' type="text" name="txtValValorUnTer" id="txtValValorUnTer" readonly>
                            </td><td>----></td>
                            <td>
                              <input size = '7' type="text" name="txtValTotUnTer" id="txtValTotUnTer" readonly>
                            </td><td></td>
                          </tr>
                          <tr>
                            <td>Durac. Adicional</td>
                            <td>
                              <input size = '7' type="text" name="txtValDurAdicTerc" id="txtValDurAdicTerc" readonly>
                            </td>
                            <td>Val Hra. Adic.</td>
                            <td>
                              <input size = '7' type="text" name="txtValHraAdicTercAdic" id="txtValHraAdicTercAdic" readonly>
                            </td><td>----></td>
                            <td>
                              <input size = '7' type="text" name="txtValTercHraExtraTotal" id="txtValTercHraExtraTotal" readonly>                        
                            </td>
                          </tr>
                          <tr>
                            <td>Auxiliares Tercero</td>
                            <td>
                              <input size = '7' type="text" name="txtValTercPersAdic" id="txtValTercPersAdic" readonly>                            
                            </td>
                            <td>Val Aux. Tercero</td>
                            <td>
                              <input size = '7' type="text" name="txtValTercValorAuxTerc" id="txtValTercValorAuxTerc" readonly>                            
                            </td><td>----></td>
                            <td>
                              <input size = '7' type="text" name="txtValTercPersAdicTotal" id="txtValTercPersAdicTotal" readonly>

                            </td>
                            <td rowspan="2">
                              <img align="right" href="#" border="0" src="imagenes/btnRefrescarT.png" width="25" height="25" align="right" id= 'refrescarDespacho' name= 'refrescarDespacho'>
                          </td>
                          </tr>
                          <tr>
                            <td>Apoyo Personal</td><td></td>
                            <td>Val Apoyo Pers.</td><td></td><td>----></td><td>S/. S/.</td>
                          </tr>
                        </table>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>Producto</td>
                    <td colspan="3" >
                      <select name="txtValProducto" id="txtValProducto" style="position: relative; width: 270">
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td width="85px">Placa</td>
                    <td width="95px"><input size = '12' type="text" name="txtValPlaca" id="txtValPlaca"></td>
                    <td width="85px"></td>
                    <td width="95px"></td>
                  </tr>
                  <tr>
                    <td class = 'rojo'>Retén</td>
                    <td colspan="3">
                      <select size="1" name="cmbValReten[]" id="cmbValReten" multiple = 'multiple' style="position: relative; width: 270; height:22" >
                      </select>  
                    </td>
                  </tr>
                  <tr>
                    <td class = 'rojo'>Punto Origen</td>
                    <td colspan="3">
                      <select size="1" name="cmbValPuntoOrigen" id="cmbValPuntoOrigen" style="position: relative; width: 270; height:22"  >
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class = 'rojo'>Final en</td>
                    <td colspan="3">
                      <select size="1" name="cmbValLugarFinCli" id="cmbValLugarFinCli" style="position: relative; width: 270; height:22"  >
                      </select>  
                    </td>
                  </tr>
                  <tr>
                    <td class = 'rojo'>Fch. Fin Cli</td>
                    <td><input size = '12' type="text" name="txtValFchFinCli" id="txtValFchFinCli" ></td>
                    <td class = 'rojo'>Fch. Fin</td>
                    <td><input size = '12' type="text" name="txtValFchFin" id="txtValFchFin"></td>
                  </tr>
                  <tr>
                    <td class = 'rojo'>Hra. Inicio</td>
                    <td><input size = '12' type="text" name="txtValHraInicioBase" id="txtValHraInicioBase"  readonly></td>
                    <td>Km. Inicio</td>
                    <td><input size = '12' type="text" name="txtValKmInicio" id="txtValKmInicio"></td>
                  </tr>
                  <tr>
                    <td class = 'rojo'>Hra. Inicio Cli</td>
                    <td><input size = '12' type="text" name="txtValHraInicio" id="txtValHraInicio"></td>
                    <td>Km. Inicio Cli</td>
                    <td><input size = '12' type="text" name="txtValKmInicioCliente" id="txtValKmInicioCliente"></td>
                  </tr>
                  <tr>
                    <td class = 'rojo'>Hra. Fin Cli</td>
                    <td><input size = '12' type="text" name="txtValHraFinCliente" id="txtValHraFinCliente"></td>
                    <td>Km. Fin Cli</td>
                    <td><input size = '12' type="text" name="txtValKmFinCliente" id="txtValKmFinCliente"></td>
                  </tr>
                  <tr>
                    <td class = 'rojo'>Hra. Fin Base</td>
                    <td ><input size = '12' type="text" name="txtValHraFin" id="txtValHraFin"></td>
                    <td>Km. Fin Base</td>
                    <td><input size = '12' type="text" name="txtValKmFin" id="txtValKmFin"></td>
                  </tr>
                  <tr>
                    <td colspan="5">Observación</td>
                  </tr>
                  <tr>
                    <td colspan="5">
                      <textarea id = 'txtValObservacion' name = 'txtValObservacion' rows = '2' cols="110" ></textarea>                   
                    </td>
                  </tr>
                </table>
                <input type='hidden' name= 'pl' id= 'pl' value = '' >
                <input type='hidden' name= 'vha' id= 'vha' value = '' >
              </div>

              <div id= "tabValDesTrip">
                <div id = "infValTrip" >
                  <table border = '0' id = 'dataValTrip'  class="display compact" cellspacing="0" width="100%">
                    
                    
                  </table>                
                </div>
              </div>
              <div id = "tabValDesGuiasPorte">
                <b>NOTA IMPORTANTE:</b> Los cambios en esta pestaña son directos en la base de datos.<br>
                <div id = "infGuiaPorte">
                <table id="dataGuiasPorte" class="display compact" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>Nro Serie - Correlativo</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>Nro Serie - Correlativo</th>
                      <th>Acciones</th>
                    </tr>
                  </tfoot>
                </table>
                </div>            
              </div>
              <div id= "tabValDesPuntos">
              </div>

            </div>
            <input type='hidden' name= 'valPrecioBaseOriginal' id= 'valPrecioBaseOriginal' value = '' >            
          </fieldset>
       </form>
      </div>
    </div>
    <!--Fin validar Despacho -->


    <!--Ver info de Despachos Concluídos -->
    <div id="dlgConcluDespacho">
      <div style="padding: 5px;">
        <form name = "formConcluDespacho" id = "formConcluDespacho" method="POST" >
        <fieldset>
          <div id = "tabConcluDesp">
            <ul>
              <li><a href="#tabConcluDespInfo">Inf. General</a></li>
              <li><a href="#tabConcluDespDatosGen">Despacho</a></li>
              <li><a href="#tabConcluDesTrip">Tripulación</a></li>
              <li><a href="#tabConcluDesGuiasPorte">Guías Porte</a></li>
              <li><a href="#tabConcluDesPuntos">Puntos</a></li>
              <li><a href="#tabConcluDesOtros">Otros</a></li>
            </ul>

            <div id= "tabConcluDespInfo">
              <table>
                <tr>
                  <td >Fch. Despacho</td>
                  <td ><input size = '12' type="text" name="txtConcluFchDespacho" id="txtConcluFchDespacho" readonly></td>
                  <td>Correlativo</td><td><input size = '12' type="text" name="txtConcluCorrelativo" id="txtConcluCorrelativo" readonly></td>
                  <td></td>
                  <td></td>
                </tr>
                <tr>
                  <td>Cliente</td><td colspan = '5'>
                    <input size = '67' type="text" name="txtConcluCliente" id="txtConcluCliente" readonly>
                  </td>
                </tr>
                <tr>
                  <td>IdProgramacion</td>
                  <td><input size = '12' type="text" name="txtConcluIdProgramacion" id="txtConcluIdProgramacion" readonly></td>
                  <td>Estado</td>
                  <td>
                    <input size = '12' type="text" name="txtConcluEstadoDespacho" id="txtConcluEstadoDespacho" readonly>
                  </td>
                </tr>

                <tr>
                  <td width = '85 px'>Modo Creación</td>
                  <td width = '110 px'><input size = '12' type="text" name="txtConcluModoCreacion" id="txtConcluModoCreacion" readonly></td>
                  <td width = '85 px'>Fch. Creación</td>
                  <td width = '110 px'><input size = '12' type="text" name="txtConcluFchCreacion" id="txtConcluFchCreacion" readonly></td>
                  <td width = '85 px'>Usuario Creac.</td>
                  <td><input size = '12' type="text" name="txtConcluUsuario" id="txtConcluUsuario" readonly></td>
                </tr>

                <tr>
                  <td width = '85 px'>Modo Terminado</td>
                  <td width = '110 px'><input size = '12' type="text" name="txtConcluModoTerm" id="txtConcluModoTerm" readonly></td>
                  <td width = '85 px'>Fch. Terminado</td>
                  <td width = '110 px'><input size = '12' type="text" name="txtConcluFchTerm" id="txtConcluFchTerm" readonly></td>
                  <td width = '85 px'>Usuario Termi.</td>
                  <td><input size = '12' type="text" name="txtConcluUsuarioTerm" id="txtConcluUsuarioTerm" readonly></td>
                </tr>

                <tr>
                  <td width = '85 px'>Modo Concluído</td>
                  <td width = '110 px'><input size = '12' type="text" name="txtConcluModoConclu" id="txtConcluModoConclu" readonly></td>
                  <td width = '85 px'>Fch. Concluído</td>
                  <td width = '110 px'><input size = '12' type="text" name="txtConcluFchConclu" id="txtConcluFchConclu" readonly></td>
                  <td width = '85 px'>Usuario Concl.</td>
                  <td><input size = '12' type="text" name="txtConcluUsuarioConclu" id="txtConcluUsuarioConclu" readonly></td>
                  <td width = '85 px'>Hra. Concluído</td>
                  <td width = '110 px'><input size = '12' type="text" name="txtConcluHraConclu" id="txtConcluHraConclu" readonly></td>
                </tr>
              </table>
            </div>

            <div id= "tabConcluDespDatosGen" style="padding-left: 5px; padding-right: 5px;">
              <table width="100%" border="0" >
                <tr>
                  <td>Cuenta</td>
                  <td colspan="3" ><input size = '40' type="text" name="txtConcluCuenta" id="txtConcluCuenta" readonly></td>
                  <td rowspan="11" valign="top">
                    <div class= 'resultado'>
                      <table width="100%" border = '0'>
                        <tr>
                          <td colspan="6">
                            <b> Resultados del Despacho </b>
                          </td>
                        </tr>
                        <tr>
                          <td width="108px"></td>
                          <td width="75px"></td>
                          <td width="108px">Precio Base Serv.</td>
                          <td width="75px">
                                <input size = '7' type="text" name="txtConcluPrecioBase" id="txtConcluPrecioBase" readonly >
                          </td>
                          <td width="20px">--></td>
                          <td width="75px">
                            <input size = '7' type="text" name="txtConcluPrecBase2" id="txtConcluPrecBase2" readonly></td>
                        </tr>
                        <tr>
                          <td>Duración Total</td>
                          <td>
                            <input size = '7' type="text" name="txtConcluDuracionhhmmss" id="txtConcluDuracionhhmmss" readonly>
                          </td>
                          <td>Hras. Normales</td>
                          <td>
                            <input size = '7' type="text" name="txtConcluHrasNormales" id="txtConcluHrasNormales" readonly>
                          </td>
                          <td colspan="2"></td>
                        </tr>
                        <tr>
                          <td>Durac. Adicional</td>
                          <td>
                            <input size = '7' type="text" name="txtConcluTpoAdic" id="txtConcluTpoAdic" readonly>
                          </td>
                          <td>Val. Hr. Adic.</td>
                          <td>
                            <input size = '7' type="text" name="txtConcluHraAdic" id="txtConcluHraAdic" readonly>
                          </td>
                          <td>--></td>
                          <td><input size = '6' type="text" name="txtConcluTotValHraAdic" id="txtConcluTotValHraAdic" readonly></td>
                        </tr>
                        <tr>
                          <td>Km. Facturable</td>
                          <td>
                            <input size = '7' type="text" name="txtConcluKmTotal" id="txtConcluKmTotal" readonly>
                          </td>
                          <td>Km. Esperado</td>
                          <td>
                            <input size = '7' type="text" name="txtConcluKmEsperado" id="txtConcluKmEsperado" readonly>
                          </td>
                          <td colspan="2"></td>
                        </tr>
                        <tr>
                          <td>Km. Adicional</td>
                          <td>
                            <input size = '7' type="text" name="txtConcluKmAdicional" id="txtConcluKmAdicional" readonly></td>
                          <td>Val. Km. Adicional</td>
                          <td>
                            <input size = '7' type="text" name="txtConcluValorKmAdic" id="txtConcluValorKmAdic" readonly>
                          </td>
                          <td>--></td>
                          <td><input size = '6' type="text" name="txtConcluValTotKmAdic" id="txtConcluValTotKmAdic" readonly></td>
                        </tr>
                        <tr>
                          <td>Auxilia. (No Retén)</td>
                          <td>
                            <input size = '7' type="text" name="txtConcluAuxNoReten" id="txtConcluAuxNoReten" readonly></td>
                          <td>Auxili. Esperados</td>
                          <td>
                            <input size = '7' type="text" name="txtConcluAuxEsperados" id="txtConcluAuxEsperados" readonly>
                          </td>
                          <td></td>
                          <td></td>
                        </tr>
                        <tr>
                          <td>Auxiliar Adicional</td>
                          <td>
                            <input size = '7' type="text" name="txtConcluValAuxAdicional" id="txtConcluValAuxAdicional" readonly></td>
                          <td>Val. Aux. Adic.</td>
                          <td>
                            <input size = '7' type="text" name="txtConcluValValorAuxAdic" id="txtConcluValValorAuxAdic">
                          </td>
                          <td>--></td>
                          <td><input size = '6' type="text" name="txtConcluValTotAuxAdic" id="txtConcluValTotAuxAdic" readonly></td>
                        </tr>
                        <tr>
                          <td colspan="6">
                            <b> Resultados sobre uso de terceros</b>
                          </td>
                        </tr>
                        <tr>
                          <td>Unidad Tercero</td>
                          <td>
                            <input size = '7' type="text" name="txtConcluUnidTerc" id="txtConcluUnidTerc" readonly></td>
                          <td>Val Unidad</td>
                          <td>
                            <input size = '7' type="text" name="txtConcluValorUnTer" id="txtConcluValorUnTer" >
                          </td><td>--></td>
                          <td>
                            <input size = '6' type="text" name="txtConcluTotUnTer" id="txtConcluTotUnTer" readonly>
                          </td>
                        </tr>
                        <tr>
                          <td>Durac. Adicional</td><td></td>
                          <td>Val Hra. Adic.<td><td>--></td><td>S/. S/.</td>
                        </tr>
                        <tr>
                          <td>Auxiliares Tercero</td><td></td>
                          <td>Val Aux. Tercero</td><td></td><td>--></td><td>S/. S/.</td>
                        </tr>
                        <tr>
                          <td>Apoyo Personal</td><td></td>
                          <td>Val Apoyo Pers.</td><td></td><td>--></td><td>S/. S/.</td>
                        </tr>
                      </table>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>Producto</td>
                  <td colspan="3" ><input size = '40' type="text" name="txtConcluProducto" id="txtConcluProducto" readonly></td>
                </tr>
                <tr>
                  <td width="85px">Placa</td>
                  <td width="95px"><input size = '12' type="text" name="txtConcluPlaca" id="txtConcluPlaca" readonly=""></td>
                  <td width="85px"></td>
                  <td width="95px"></td>
                </tr>
                <tr>
                  <td>Retén</td>
                  <td colspan="3">
                    <input size = '40' type="text" name="txtConcluReten" id="txtConcluReten" readonly="">
                  </td>
                </tr>
                <tr>
                  <td>Punto Origen</td>
                  <td colspan="3">
                    <input size = '40' type="text" name="txtConcluPuntoOrigen" id="txtConcluPuntoOrigen" readonly="">
                  </td>
                </tr>
                <tr>
                  <td>Final en</td>
                  <td colspan="3">
                    <input size = '9' type="text" name="txtConcluLugarFinCli" id="txtConcluLugarFinCli" readonly="">
                  </td>
                </tr>
                <tr>
                  <td>Fch. Fin Cli</td>
                  <td><input size = '12' type="text" name="txtConcluFchFinCli" id="txtConcluFchFinCli" readonly="" ></td>
                  <td>Fch. Fin</td>
                  <td><input size = '12' type="text" name="txtConcluFchFin" id="txtConcluFchFin" readonly=""></td>
                </tr>
                <tr>
                  <td>Hra. Inicio</td>
                  <td><input size = '12' type="text" name="txtConcluHraInicioBase" id="txtConcluHraInicioBase"  readonly></td>
                  <td>Km. Inicio</td>
                  <td><input size = '12' type="text" name="txtConcluKmInicio" id="txtConcluKmInicio" readonly=""></td>
                </tr>
                <tr>
                  <td>Hra. Inicio Cli</td>
                  <td><input size = '12' type="text" name="txtConcluHraInicio" id="txtConcluHraInicio" readonly=""></td>
                  <td>Km. Inicio Cli</td>
                  <td><input size = '12' type="text" name="txtConcluKmInicioCliente" id="txtConcluKmInicioCliente" readonly=""></td>
                </tr>
                <tr>
                  <td>Hra. Fin Cli</td>
                  <td><input size = '12' type="text" name="txtConcluHraFinCliente" id="txtConcluHraFinCliente" readonly=""></td>
                  <td>Km. Fin Cli</td>
                  <td><input size = '12' type="text" name="txtConcluKmFinCliente" id="txtConcluKmFinCliente" readonly=""></td>
                </tr>
                <tr>
                  <td>Hra. Fin Base</td>
                  <td ><input size = '12' type="text" name="txtConcluHraFin" id="txtConcluHraFin" readonly=""></td>
                  <td>Km. Fin Base</td>
                  <td><input size = '12' type="text" name="txtConcluKmFin" id="txtConcluKmFin" readonly=""></td>
                </tr>
                <tr>
                  <td colspan="5">Observación</td>
                </tr>
                <tr>
                  <td colspan="5">
                    <textarea id = 'txtConcluObservacion' name = 'txtConcluObservacion' rows = '2' cols="102" readonly=""></textarea>                   
                  </td>
                </tr>
              </table>
              <input type='hidden' name= 'pl' id= 'pl' value = '' >
              <input type='hidden' name= 'vha' id= 'vha' value = '' >
               
            </div>

            <div id= "tabConcluDesTrip">
              <div id = "infConcluTrip" >
              </div>
            </div>
            <div id = "tabConcluDesGuiasPorte">
              <div id = "infConcluGuiaPorte">
              </div>            
            </div>

            <div id= "tabConcluDesPuntos">
            </div>

            <div id= "tabConcluDesOtros">
              <div id = "subTabConcluOtros">
                <ul>
                  <li><a href="#subTabConcluOtCobPagGraf">Cobros / Pagos (Gráfica)</a></li>
                  <li><a href="#subTabConcluOtCobrosPagos">Cobros / Pagos</a></li>
                  <li><a href="#subTabConcluOtOcurrencias">Ocurrencias</a></li>
                </ul>

                <div id = 'subTabConcluOtCobPagGraf' style="width: 450px; height: 220px;">
                </div>

                <div id= "subTabConcluOtCobrosPagos">
                </div>


                <div id= "subTabConcluOtOcurrencias">
                </div>
              </div>
            </div>
          </div>          
        </fieldset>
       </form>
      </div>
    </div>
    <!--Fin ver info de Despachos Concluídos -->


    <!--Cerrar Forzoso -->
    <div id="dlgCerrarForzoso">
      <div style="padding: 5px;">
        <form name = "formCerrarForzoso" id = "formCerrarForzoso" method="POST" >
        <fieldset>
          <div id="tabsCerrarForzoso">
            <ul>
              <li><a href="#tabInfo">Inf. General</a></li>
              <li><a href="#tabDespacho">Despacho</a></li>
              <li><a href="#tabTrip">Tripulación</a></li>
              <li><a href="#tabPuntos">Puntos</a></li>
            </ul>
            <div id="tabInfo">
              <table>
                <tr>
                  <td width = '85 px'>IdProgramacion</td>
                  <td width = '110 px'>
                    <input size = '12' type="text" name="txtCFidProgramacion" id="txtCFidProgramacion" readonly></td>
                  <td width = '85 px'>Creador</td>
                  <td><input size = '12' type="text" name="txtCFusuario" id="txtCFusuario" readonly></td>
                </tr>
                <tr>
                  <td>Fch. Creación</td><td ><input size = '12' type="text" name="txtCFfchCreacion" id="txtCFfchCreacion" readonly></td>
                  <td>Correlativo</td><td><input size = '12' type="text" name="txtCFcorrelativo" id="txtCFcorrelativo" readonly></td>
                </tr>
                <tr>
                  <td>Cliente</td><td colspan = '3'>
                    <input size = '50' type="text" name="txtCFcliente" id="txtCFcliente" readonly>
                  </td>
                </tr>
                <tr>
                  <td >Fch. Despacho</td>
                  <td ><input size = '12' type="text" name="txtCFfchDespacho" id="txtCFfchDespacho" readonly></td>
                  <td>Estado</td>
                  <td class = 'rojo'>Terminado</td>
                </tr>
              </table>
            </div>
            <div id="tabDespacho">
              <table width="100%">
                <tr>
                  <td width = '85 px'>Cuenta</td>
                  <td colspan="4" >
                    <select name="txtCFcuenta" id="txtCFcuenta" style="position: relative; width: 270"></select>
                  </td>
                </tr>
                <tr>
                  <td>Producto</td>
                  <td colspan="4" >
                    <select name="txtCFproducto" id="txtCFproducto" style="position: relative; width: 270">
                    </select>
                  </td>
                </tr>
                <tr>
                  <td width="85px">Placa</td>
                  <td width="90px" ><input size = '11' type="text" name="txtCFplaca" id="txtCFplaca"></td>
                  <td width="85px"></td>
                  <td width="90px"></td>
                  <td>Observación</td>
                </tr>
                <tr>
                  <td >Retén</td>
                  <td colspan="3">
                    <select size="1" name="cmbCFReten[]" id="cmbCFReten" multiple="multiple" style="position: relative; width: 270; height:22"  >
                    </select>  
                  </td>
                  <td rowspan="8">
                    <textarea id = 'txtCFobservacion' name = 'txtCFobservacion' rows = '10' cols="30" ></textarea>
                  </td>
                </tr>
                <tr>
                  <td class = 'rojo'>Punto Origen</td>
                  <td colspan="3">
                    <select size="1" name="cmbCFPuntoOrigen" id="cmbCFPuntoOrigen" style="position: relative; width: 178; height:22"  >
                    </select>  
                  </td>
                </tr>
                <tr>
                  <td class = 'rojo'>Final en</td>
                  <td colspan="3">
                    <select size="1" name="cmbCFFinCli" id="cmbCFFinCli" style="position: relative; width: 178; height:22"  >
                    </select>  
                  </td>
                </tr>
                <tr>
                  <td class = 'rojo'>Fch. Fin Cli</td>
                  <td><input size = '11' type="text" name="txtCFfchFinCli" id="txtCFfchFinCli" ></td>
                  <td class = 'rojo'>Fch. Fin</td>
                  <td><input size = '11' type="text" name="txtCFfchFin" id="txtCFfchFin"></td>
                </tr>
                <tr>
                  <td class = 'rojo'>Hra. Inicio</td>
                  <td><input size = '11' type="text" name="txtCFhraInicioBase" id="txtCFhraInicioBase"  readonly></td>
                  <td>Km. Inicio</td>
                  <td><input size = '11' type="text" name="txtCFkmInicio" id="txtCFkmInicio"></td>
                </tr>
                <tr>
                  <td class = 'rojo'>Hra. Inicio Cli</td>
                  <td><input size = '11' type="text" name="txtCFhraInicio" id="txtCFhraInicio"></td>
                  <td>Km. Inicio Cli</td>
                  <td><input size = '11' type="text" name="txtCFkmInicioCliente" id="txtCFkmInicioCliente"></td>
                </tr>
                <tr>
                  <td class = 'rojo'>Hra. Fin Cli</td>
                  <td><input size = '11' type="text" name="txtCFhraFinCliente" id="txtCFhraFinCliente"></td>
                  <td>Km. Fin Cli</td>
                  <td><input size = '11' type="text" name="txtCFkmFinCliente" id="txtCFkmFinCliente"></td>
                </tr>
                <tr>
                  <td class = 'rojo'>Hra. Fin Base</td>
                  <td ><input size = '11' type="text" name="txtCFhraFin" id="txtCFhraFin"></td>
                  <td>Km. Fin Base</td>
                  <td><input size = '11' type="text" name="txtCFkmFin" id="txtCFkmFin"></td>
                </tr>
              </table>
            </div>

            <div id="tabTrip">
              
            </div>
            <div id="tabPuntos">
            </div>
          </div>
        </fieldset>
       </form>
      </div>
    </div>
    <!--Fin Cerrar Forzoso -->

    <div id = "dlgValidadoATerminado">
      <div style="padding: 10px;">
        <form name = "formValidadoATerminado" id = "formValidadoATerminado" method="POST" >
          <table border="0" width="470">
            <tr>
              <td width = '100px'>Fch Despacho</td>
              <td width = '105px'><input type="text" style="padding: 5px; width: 110px" name="txtFchDepachoVaT" id ="txtFchDepachoVaT" readonly></td>
              <td width = '70px'>Correlativo</td>
              <td><input type="text" style="padding: 5px; width: 110px"  name="txtCorrelativoVaT" id="txtCorrelativoVaT" readonly></td>
            </tr>
            <tr>
              <td>Id Programación</td>
              <td colspan="3">
                <input type="text" style="padding: 5px; width: 300px"  name="txtIdProgramacionVaT" id="txtIdProgramacionVaT" readonly>
              </td>
            </tr>
            <tr>
              <td>Sobre Tripulación</td>
              <td colspan="3">
                <input type="text" style="padding: 5px; width: 300px"  name="txtSobreTripulacionVaT" id="txtSobreTripulacionVaT" readonly>
              </td>
            </tr>
            <tr>
              <td>Sobre Terceros</td>
              <td colspan="3">
                <input type="text" style="padding: 5px; width: 300px"  name="txtSobreTercerosVaT" id="txtSobreTercerosVaT" readonly>
              </td>
            </tr>
            <tr>
              <td>Sobre Cobranzas</td>
              <td colspan="3">
                <input type="text" style="padding: 5px; width: 300px"  name="txtSobreCobranzasVaT" id="txtSobreCobranzasVaT" readonly>
              </td>
            </tr>

            <tr>
              <td>Sobre Ocurrencias</td>
              <td colspan="3">
                <input type="text" style="padding: 5px; width: 300px"  name="txtSobreOcurrVaT" id="txtSobreOcurrVaT" readonly>
              </td>
            </tr>
            <tr>
              <td>Sobre Ocurr Tercero</td>
              <td colspan="3">
                <input type="text" style="padding: 5px; width: 300px"  name="txtSobreOcurrTerVaT" id="txtSobreOcurrTerVaT" readonly>
              </td>
            </tr>
          </table>
          <input type='hidden' name= 'fchDespachoVaT' id= 'fchDespachoVaT' value = '' >
          <input type='hidden' name= 'correlativoVaT' id= 'correlativoVaT' value = '' >
        </form>
      </div>      
    </div>


    <!--Ocurrencia Despacho -->
    <div id="dlgOcurrenciaDespacho" title="Ocurrencia Despacho">
      <div style="padding: 10px;">
        <form name = "formOcurrenciaDespacho" id = "formOcurrenciaDespacho" method="POST" >
            <table border="0" width="530">
              <tr>
                <td class = 'rojo'>Despacho</td>
                <td colspan="3">
                  <input style="padding: 5px; width: 401px" id="elDespacho" name="elDespacho" type="text"  placeholder="Despacho" readonly >
                </td>
              </tr>
              <tr>
                <td width="90" class = 'rojo'>Tipo Ocurrencia</td>
                <td width="160">
                  <select size="1" name="cmbTipoOcurrencia" id="cmbTipoOcurrencia" style="padding: 5px; width: 150px" >
                    <option selected>-</option>
                    <option value = 'Descuento' >Dscto por Responsab</option>
                    <option value = 'Descuento' >Dscto por Documento</option>
                    <option value = 'Descuento' >Dscto por Papeleta</option>
                    <option value = 'Descuento' >Dscto Otros</option>
                    <option>Caja Chica</option>
                    <option disabled>Devolucion</option>
                    <option>Movilidad</option>
                    <option disabled>Gastos Varios</option>
                  </select>
                </td>
                <td width="90" class = 'rojo'>Tipo Documento</td>
                <td>
                  <select size="1" name="cmbTipoDoc" id="cmbTipoDoc" style="padding: 5px; width: 150px" >
                    <option selected>-</option>
                    <option value = '00'>Otros</option>
                    <option value = '01'>Factura</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Nro. Documento</td>
                <td colspan="3">
                  <input type="text" style="padding: 5px; width: 150px" name="txtNroDoc" id="txtNroDoc" size="15">
                  <div id = "autogenera">El sistema autogenera el número de vale</div>
                </td>
              </tr>
              <tr>
                <td class = 'rojo'>Monto Total</td>
                <td ><input type="text" style="padding: 5px; width: 150px" name="txtMntTotal" id ="txtMntTotal" ></td>
                <td class = 'rojo'>Mnt. a Distribuir</td>
                <td><input type="text" style="padding: 5px; width: 150px"  name="txtMntDistribuir" id="txtMntDistribuir" ></td>
              </tr>
              <tr>
                <td>Descripcion <br>(Máx. 300 caract.) </td>
                <td colspan="3">
                  <textarea rows="2" name="txtDescripcion" cols="52"></textarea>
                </td>
              </tr>

              <tr>
                <td colspan="4">
                  <table width="500" id = 'tblOcurrTrip'>

                  </table>
                </td>
              </tr>

            </table>
          <input type='hidden' name= 'fchDespachoOcurr' id= 'fchDespachoOcurr' value = '' >
          <input type='hidden' name= 'correlativoOcurr' id= 'correlativoOcurr' value = '' >
          <input type='hidden' name=subTipoItem id=subTipoItem size=30 value = ''>
        </form>
      </div>
    </div>
    <!--Fin Ocurrencia Despacho -->


    <!--Eliminar Despacho -->
    <div id="dlgElimDespacho" title="Eliminar Despacho">
      <div style="padding: 5px;">
        <form name = "formElimDespacho" id = "formElimDespacho" method="POST" >
          <fieldset>
            <table>
              <tr>
                <td width = '85 px'>IdProgramacion</td>
                <td width = '110 px'><input size = '12' type="text" name="txtElimIdProgramacion" id="txtElimIdProgramacion" readonly></td>
              </tr>
              <tr>
                <td width = '85 px'>Creador</td><td><input size = '12' type="text" name="txtElimUsuario" id="txtElimUsuario" readonly></td>
                <td>Fch. Creación</td><td><input size = '12' type="text" name="txtElimFchCreacion" id="txtElimFchCreacion" readonly></td>
              </tr>
              <tr>
                 <td >Fch. Despacho</td>
                <td >
                  <input size = '12' type="text" name="txtElimFchDespacho" id="txtElimFchDespacho" readonly>
                </td>
                <td>Correlativo</td><td><input size = '12' type="text" name="txtElimCorrelativo" id="txtElimCorrelativo" readonly></td>
              </tr>
              <tr>
                <td>Cliente</td><td colspan = '3'>
                  <input size = '50' type="text" name="txtElimCliente" id="txtElimCliente" readonly>
                </td>
              </tr>
              <tr>
                <td>Estado</td>
                <td>
                  <input size = '12' type="text" name="txtElimEstado" id="txtElimEstado" readonly>
                </td>
              </tr>
            </table>
          </fieldset>
        </form>
      </div>
    </div>
    <!--Fin Eliminar Despacho -->

    <div  id = "contenedor"  align="left"  style="width: 100%; height: 420px;  overflow:auto">

      <div id="tabsDespachos">
        <ul>
          <li><a class = 'aLimpiarDetalles' href="#tabDespTConcluido">Despachos T Concluidos</a></li>
          <li><a class = 'aLimpiarDetalles' href="#tabDespTerminados">Despachos Terminados</a></li>
          <li><a class = 'aLimpiarDetalles' href="#tabDespRuta">Despachos en Ruta</a></li>
          <li><a class = 'aLimpiarDetalles' href="#tabDespProgram">Despachos Programados</a></li>
        </ul>

        <div id = "tabDespTConcluido">
          <div  id = "infDespValidado">
            <table id="dataDespTConcluido" class="display compact" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th></th>
                  <th>Id Programacion</th>
                  <th>Fch Despacho</th>
                  <th>Correl</th>
                  <th>Placa</th>
                  <th>Movil</th>
                  <th>U. Asignado</th>
                  <th>Cliente</th>
                  <th>Cuenta</th>
                  <th>Producto</th>
                  <th>Tipo Servicio</th>
                  <th>Fch Creacion</th>
                  <th>Creado Por</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th></th>
                  <th>Id Programacion</th>
                  <th>Fch Despacho</th>
                  <th>Correl</th>
                  <th>Placa</th>
                  <th>Movil</th>
                  <th>U. Asignado</th>
                  <th>Cliente</th>
                  <th>Cuenta</th>
                  <th>Producto</th>
                  <th>Tipo Servicio</th>
                  <th>Fch Creacion</th>
                  <th>Creado Por</th>
                  <th>Acción</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <div id = "tabDespTerminados">
          <div  id = "infDespTerm">
            <table id="dataDespTerm" class="display compact" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th></th>
                <th>Id Programacion</th>
                <th>Fch Despacho</th>
                <th>Correl</th>
                <th>Placa</th>
                <th>Movil</th>
                <th>U. Asignado</th>
                <th>Cliente</th>
                <th>Cuenta</th>
                <th>Producto</th>
                <th>Tipo Servicio</th>
                <th>Fch Creacion</th>
                <th>Creado Por</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th></th>
                <th>Id Programacion</th>
                <th>Fch Despacho</th>
                <th>Correl</th>
                <th>Placa</th>
                <th>Movil</th>
                <th>U. Asignado</th>
                <th>Cliente</th>
                <th>Cuenta</th>
                <th>Producto</th>
                <th>Tipo Servicio</th>
                <th>Fch Creacion</th>
                <th>Creado Por</th>
                <th>Acción</th>
              </tr>
            </tfoot>
            </table>
          </div>
        </div>

        <div id = "tabDespRuta">
          <div id = "infDespEnRuta">
            <table id="dataDespEnRuta" class="display compact" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th></th>
                <th>Id Programacion</th>
                <th>Fch Despacho</th>
                <th>Correl</th>
                <th>Placa</th>
                <th>Movil</th>
                <th>U. Asignado </th>
                <th>Cliente</th>
                <th>Cuenta</th>
                <th>Producto</th>
                <th>Tipo Servicio</th>
                <th>Fch Creacion</th>
                <th>Creado Por</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th></th>
                <th>Id Programacion</th>
                <th>Fch Despacho</th>
                <th>Correl</th>
                <th>Placa</th>
                <th>Movil</th>
                <th>U. Asignado </th>
                <th>Cliente</th>
                <th>Cuenta</th>
                <th>Producto</th>
                <th>Tipo Servicio</th>
                <th>Fch Creacion</th>
                <th>Creado Por</th>
                <th>Acción</th>
              </tr>
            </tfoot>
            </table>
          </div>
        </div>

        <div id = "tabDespProgram">
          <div id = "infDespProgram">
            <table id="dataDespProgram" class="display compact" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th></th>
                  <th>Id Programacion</th>
                  <th>Fch Despacho</th>
                  <th>Correl</th>
                  <th>Placa</th>
                  <th>Movil</th>
                  <th>U. Asignado</th>
                  <th>Cliente</th>
                  <th>Cuenta</th>
                  <th>Producto</th>
                  <th>Tipo Servicio</th>
                  <th>Fch Creacion</th>
                  <th>Creado Por</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th></th>
                  <th>Id Programacion</th>
                  <th>Fch Despacho</th>
                  <th>Correl</th>
                  <th>Placa</th>
                  <th>Movil</th>
                  <th>U. Asignado</th>
                  <th>Cliente</th>
                  <th>Cuenta</th>
                  <th>Producto</th>
                  <th>Tipo Servicio</th>
                  <th>Fch Creacion</th>
                  <th>Creado Por</th>
                  <th>Acción</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div id = 'divDetalles'>

      <div id = "infTrip">
        <table id="dataTrip" class="display compact" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Fch. Despacho</th>
              <th>Correl</th>
              <th>Id Trabaj.</th>
              <th>Nomb. Tripulante</th>
              <th>Tipo Rol</th>
              <th>Hra. Inicio</th>
              <th>Fch. Fin</th>
              <th>Hra. Fin</th>
              <th>Observación</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>Fch. Despacho</th>
              <th>Correl</th>
              <th>Id Trabaj.</th>
              <th>Nomb. Tripulante</th>
              <th>Tipo Rol</th>
              <th>Hra. Inicio</th>
              <th>Fch. Fin</th>
              <th>Hra. Fin</th>
              <th>Observación</th>
              <th>Acción</th>
            </tr>
          </tfoot>
        </table>
      </div>

      <div id = "infPuntos">
        <table id="dataPuntos" class="display compact" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Id Punto</th>
              <th>Orden Punto</th>
              <th>Tipo Punto</th>
              <th>Nomb Comprador</th>
              <th>Distrito</th>
              <th>Provincia</th>
              <th>Dirección</th>
              <th>Guía Cliente</th>
              <th>Guía Moy</th>
              <th>Id Carga</th>
              <th>Estado</th>
              <th>Sub Estado</th>
              <th>Hra. Llegada</th>
              <th>Hra. Salida</th>
              <th>Observación</th>
              <th>Productos</th>
              <th>FchDespacho</th>
              <th>Correlativo</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>Id Punto</th>
              <th>Orden Punto</th>
              <th>Tipo Punto</th>
              <th>Nomb Comprador</th>
              <th>Distrito</th>
              <th>Provincia</th>
              <th>Dirección</th>
              <th>Guía Cliente</th>
              <th>Guía Moy</th>
              <th>Id Carga</th>
              <th>Estado</th>
              <th>Sub Estado</th>
              <th>Hra. Llegada</th>
              <th>Hra. Salida</th>
              <th>Observación</th>
              <th>Productos</th>
              <th>FchDespacho</th>
              <th>Correlativo</th>
              <th>Acción</th>
            </tr>
          </tfoot>
        </table>
      </div>

    </div>

</body>
</html>