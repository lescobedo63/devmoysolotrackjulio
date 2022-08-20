<?php

?>

<html>
<head>
  <script language="JavaScript" type="text/javascript" src="librerias/varios.js"></script>
  <link rel =stylesheet href="librerias/jquery-ui-1.8.9.custom.css" type ="text/css">
  <link rel =stylesheet href="librerias/estilos.css" type ="text/css">
  <link rel =stylesheet href="librerias/DataTables/datatables.min.css" type ="text/css">
  <script language="JavaScript" type="text/javascript" src="librerias/DataTables/datatables.min.js"></script>
  <style>

    *{padding:0px; margin:0px;}
  </style>

  <script>

    $(document).ready(function(){

      // Setup - add a text input to each footer cell
      $('#data tfoot th').each( function () {
          //alert($(this).index() )
          var title = $('#data thead th').eq( $(this).index() ).text();
          if (title != 'Accion' ){

            if($(this).index() == 3){
              $(this).html('<select><option></option><option>Activo</option><option>Inactivo</option>'); 

            } else {  
              $(this).html('<input type="text" placeholder=" ' + title + '" style="width:100%"  />');              
            }

          }
      } );

      var t = $('#data').DataTable({
        "scrollCollapse": true,
        "lengthMenu": [ 15, 30, 500 ],
        "oLanguage": {
        "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ entradas",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
        "sZeroRecords": "No hay registros coincidentes",
        },
        "responsive": true,
        "processing": true,
        "serverSide": true,
        //"ajax": "vistas/ladoServidor/ladoServidor.php?opcion=listarTrabajadores",
        "ajax":{
          "url" :  "vistas/ladoServidor/ladoServidor.php?opcion=nuevolistarVehiculos&admin="+'<?php echo $_SESSION['admin']; ?>'+ "&edicion="+'<?php echo $_SESSION['edicion']; ?>',
        /*  "type" : 'POST',
          "data": function (d) {
                    d.opcion = 'listarTrabajadores';                    
                }*/
               },
        "columnDefs": [
          {"width": "20", "targets": [0]},
          {"width": "55", "targets": [1,3,5,6,7,8,9,10]},
          //{"width": "90", "targets": [8]},
          {"width": "220", "targets": [2,4]},
          /*{"targets": [ -2 ], "visible": false, "searchable": false},*/
          //{"className": "dt-body-right", "targets": [4, 7]}
        ],
        'order': [[1, 'asc']]
      });

      t.columns().eq( 0 ).each( function ( colIdx ) {

         if (colIdx == 3){
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

      $('#data_filter').hide()

      $('#data tbody').on( 'click', 'tr', function(){
        if ( $(this).hasClass('selected') ) {
          $(this).removeClass('selected');
        } else {
          t.$('tr.selected').removeClass('selected');
          $(this).addClass('selected');
        }
      });
    })

  function descargarExcel(){
    alert("No implementado")
  }
  </script>  
  
</head>
<body>
   <?php require_once 'barramenu.php';  ?>
  <form name = 'frmFiltro' id = 'frmFiltro' method ='POST'>
    <table class = 'fullRD' >
      <tr>
        <th  class = "pagina"  align="center" >
          <img border="0" src="imagenes/moy.png" width="25" height="25" align="right" onclick =  "abrirventana('index.php?controlador=aplicacion&accion=verprincipal') ">
          <img class="sinTitulo" align="right" href="#" title="Descarga Listado de Vehículos Activos|en Formato Excel" border="0" src="imagenes/hoja.png" width="25" height="25" onclick =  "abrirventanaflotante('./vistas/descargarListadoVehiculos.php') " >

          <?php if($_SESSION["ingreso"] == "Si" ) { ?>
            <img border="0" src="imagenes/mas.png" width="25" height="25" align="right" onclick =  "abrirventanaflotantesupergrande('index.php?controlador=vehiculos&accion=nuevovehiculo') ">
            <img class ="titulo" title ="Reporte de Rendimientos|Da acceso a un reporte de rendimiento|para lo cual debe especificar una fecha|de inicio y una fecha fin." border="0" src="imagenes/combustible.png" width="25" height="25" align="right" onclick =  "abrirventana('index.php?controlador=vehiculos&accion=ingreseperiodo') ">
          <?php } ?>
          LISTADO DE VEHICULOS
        </th>
      </tr>
    </table>
    </form>

    <div align="left"  style="width: 100%; height: 520px;  overflow:auto">
      <table id="data" class="display compact" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th>Nro</th>
          <th>Placa</th>
          <th>Propietario</th>
          <th>Estado</th>
          <th>Razon Social</th>
          <th>P.Util.Nom</th>
          <th>P.Util.Real</th>
          <th>Cap.M3</th>
          <th>M3 Fact</th>
          <th>Usuario</th>
          <th>FchCreacion</th>
          <th>Accion</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th>Nro</th>
          <th>Placa</th>
          <th>Propietario</th>
          <th>Estado</th>
          <th>Razon Social</th>
          <th>P.Util.Nom</th>
          <th>P.Util.Real</th>
          <th>Cap.M3</th>
          <th>M3 Fact</th>
          <th>Usuario</th>
          <th>FchCreacion</th>
          <th>Accion</th>
        </tr>
      </tfoot>
      </table>
    </div>
</body>
</html>