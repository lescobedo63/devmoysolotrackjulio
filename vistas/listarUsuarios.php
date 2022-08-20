<?php
?>

<html>
<head>

    <script language="JavaScript" type="text/javascript" src="librerias/varios.js"></script>
    <link rel=stylesheet href="librerias/jquery-ui-1.8.9.custom.css" type="text/css">
    <link rel=stylesheet href="librerias/DataTables/datatables.min.css" type="text/css">
    <script language="JavaScript" type="text/javascript" src="librerias/DataTables/datatables.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

    <style>
        * {
            padding: 0px;
            margin: 0px;
        }
        .form-control {
            margin-bottom: -3%;
            font-size: 0.1em;
        }
        #data label{
            font-size: 3px;
        }
    </style>

    <script>
        $(document).ready(function() {

            $('#data tfoot th').each(function() {
                var title = $('#data thead th').eq($(this).index()).text();
                if (title != 'Acción') {
                    $(this).html('<input type="text" placeholder=" ' + title + '" style="width:100%"  />');
                }
            });

            var t = $('#data').DataTable({
                "scrollCollapse": true,
                "lengthMenu": [15, 30, 500],
                "oLanguage": {
                "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ entradas",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
                "sZeroRecords": "No hay registros coincidentes",
                },
                "responsive": true,
                "processing": true,
                "serverSide": true,
                "ajax": {
                "url": "vistas/ladoServidor/ladoServidor3.php?opcion=listarUsuarios&admin=" + '<?php echo $_SESSION['admin']; ?>' + "&edicion=" + '<?php echo $_SESSION['edicion']; ?>',
                },
                "columnDefs": [],
                'order': [
                    [0, 'asc']
                ],
                "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    if (aData[15] == "Validado") {
                        $('td', nRow).css('background-color', '#5EDF3E');
                    } else if (aData[15] == "Pendiente") {
                        $('td', nRow).css('background-color', '#FFF773');
                    }
                },
            });

            t.columns().eq(0).each(function(colIdx) {
                $('input', t.column(colIdx).footer()).on('keyup change', function() {
                t
                    .column(colIdx)
                    .search(this.value)
                    .draw();
                });
            });

            $('#data_filter').hide()

            $("#imgMas").click(function() {
                abrirventanaflotantegrande('index.php?controlador=aplicacion&accion=nuevousuario')
            });

            $('#data').on('click', 'tbody .editar', function() {
                idUsuario = t.row($(this).parents('tr')).data()["0"];
                abrirventana('index.php?controlador=aplicacion&accion=editausuario&idUsuario=' + idUsuario)
            });

            $('#data').on('click', 'tbody .eliminar', function() {
                idUsuario = t.row($(this).parents('tr')).data()["0"];
                abrirventanaflotante('index.php?controlador=aplicacion&accion=eliminausuario&idUsuario=' + idUsuario)
            });
        })
    </script>
</head>
<body>
    <?php require_once 'barramenu.php'; ?>
    <form name='frmFiltro' id='frmFiltro' method='POST'>
        <div style="background-color: rgb(3,32,100); text-align: center; margin-bottom: 4">
            <img border="0" src="imagenes/moy.png" title="Regresar al menú principal" onclick="abrirventana('index.php?controlador=aplicacion&accion=verprincipal')" width="25" height="25" align="right">
            <img border="0" id='imgMas' name='imgMas' src="imagenes/mas.png" title="Crear Cotizacion" width="24" height="24" align="right">
            <font color="white" size=4><strong>LISTADO DE USUARIOS</strong><font>
        </div>
        <div style="background-color: rgb(3,32,100); text-align: left; margin-bottom: 4">
        <font color="white" size=2><font>
        </div>
    </form>

    <div align="left" style="width: 100%; height: 520px; overflow:auto">
    <table id="data" class="display compact" cellspacing="0" width="100%">
      <thead>
        <tr>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>DNI</th>
            <th>Ingreso</th>
            <th>Edición</th>
            <th>Consulta</th>
            <th>Adm Costos</th>
            <th>Adm Articulo</th>
            <th>Admin</th>
            <th>Planilla</th>
            <th>Adm Planilla</th>
            <th>Adm Uniforme</th>
            <th>Plame</th>
            <th>Adm Móvil</th>
            <th>Tipo Usuario</th>
            <th>Carga Despacho</th>
            <th>Seguridad</th>
            <th>Invitado</th>
            <th>Fch Creación</th>
            <th>Fch Vencimiento</th>
            <th>Acción</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>DNI</th>
            <th>Ingreso</th>
            <th>Edición</th>
            <th>Consulta</th>
            <th>Adm Costos</th>
            <th>Adm Articulo</th>
            <th>Admin</th>
            <th>Planilla</th>
            <th>Adm Planilla</th>
            <th>Adm Uniforme</th>
            <th>Plame</th>
            <th>Adm Móvil</th>
            <th>Tipo Usuario</th>
            <th>Carga Despacho</th>
            <th>Seguridad</th>
            <th>Invitado</th>
            <th>Fch Creación</th>
            <th>Fch Vencimiento</th>
            <th>Acción</th>
        </tr>
      </tfoot>
    </table>
  </div>
</body>

</html>