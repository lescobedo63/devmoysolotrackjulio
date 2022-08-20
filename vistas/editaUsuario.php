<?php
  if(!isset($_POST['btnEnviar'])){ ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
    <html>
      <head>
        <script language="JavaScript" type="text/javascript" src="librerias/varios.js"></script>
        <link rel =stylesheet href="librerias/estilos.css" type ="text/css">
        <meta http-equiv="content-type" content="text/html; charset=windows-1250">
        <meta name="generator" content="PSPad editor, www.pspad.com">
        <title></title>
  
        <script type="text/javascript">
          function cambiarPass(){
            alert("Prueba")
          }

          $(document).ready(function(){
            $("#txtFchVenc").datepicker({
              showButtonPanel: true,
              changeMonth: true,
              changeYear: true,
              minDate: "0M +0D",
              dateFormat:"yy-mm-dd" 
            });

            $("#frmEditaUsuario").submit(function(e){
              e.preventDefault();
              var eMail = $("#txtEmail").val()
              resultado = "Ok"
              $("input.classAlerta").each(function(){
                if( $(this).is(':checked') ) {
                  if(eMail == ""){
                    resultado = "Error"
                  }
                }
              })
              if (resultado == "Ok"){
                var datos = new FormData($("#frmEditaUsuario")[0]);
                $.ajax({
                  data: datos,
                  url:'./vistas/ajaxVarios_v3.php?opc=editarUsuario',
                  timeout: 15000,
                  method: "POST",
                  processData: false,
                  contentType: false,
                  success:  function (response) {
                    if(response > 0){
                      swal({
                        type: "success",
                        title: "El usuario fue editado correctamente!",
                        showConfirmButton:true,
                        confirmButtonText:"Cerrar",
                        closeOnConfirm: false
                      }).then((result)=>{
                        if(result.value){
                          window.location = "index.php?controlador=aplicacion&accion=listarusuario"
                        }
                      });
                    } else {
                      swal({
                        type: "warning",
                        title: "No se han modificado datos del usuario!",
                        showConfirmButton:true,
                        confirmButtonText:"Cerrar",
                        closeOnConfirm: false
                      })
                    }
                  }
                })
                ////////////////
              } else {
                swal({
                  type: "error",
                  title: "Revise los datos del usuario!",
                  showConfirmButton:true,
                  confirmButtonText:"Cerrar",
                  closeOnConfirm: false
                })
              }
            })        
            $("#tabOpciones").tabs();
          })
        </script>
      </head>
      <body>
        <?php  require_once 'barramenu.php'; ?>
        <table width="100%">
          <tr>
            <th class = 'pagina'>
              <b>Edita Usuario  <?php echo $idUsuario ?></b>
              <img border="0" src="imagenes/volver.jpg" width="25" height="25" align="right" onclick =  "abrirventana('index.php?controlador=aplicacion&accion=listarusuario') ">
            </th>
          </tr>
        </table>
    
        <form name = 'frmEditaUsuario' id= 'frmEditaUsuario' method="POST">
          <?php foreach($dataUsuario as $item){ ?>
            <div style="width:800px;" >
            <div id="tabOpciones">
              <ul>
                <li><a href="#tabOpcGeneral">Generales</a></li>
                <li><a href="#tabOpcAlertas">Alertas</a></li>
                <li><a href="#tabOpcPermisos">Permisos</a></li>
                <li><a href="#tabOpcReportes">Reportes</a></li>
              </ul>
              <div id="tabOpcGeneral">
                <table width="750px">
                  <tr>
                    <td width="80">Nombre</td>
                    <td width="280"><input type="text" value = '<?php echo $item['nombre']; ?>' name="txtNombre" size="35" maxlength="30"></td>
                    <td colspan="2" >Nombre de la persona (ejemplo Juan Perez)</td>
                  </tr>
                  <tr>
                    <td>Contraseńa</td>
                    <td>
                      <input type="button" value="Cambiar ..." onclick="abrirventanaflotantemediana('index.php?controlador=aplicacion&accion=cambiarpass&cambiar=<?php echo $idUsuario  ?>')"/>
                    </td>
                    <td colspan="2" >Accede a modificar la contraseńa actual</td>
                  </tr>
                  <tr>
                    <td >Fch. Venc.</td>
                    <td ><input type="text" value = '<?php echo $item['fchVencimiento']; ?>'  name="txtFchVenc" id="txtFchVenc" size="10"></td>
                    <td colspan="2" >Hasta que fecha tiene acceso el usuario</td>
                  </tr>
                  <tr>
                    <td >Estado</td>
                    <td >
                      <select name = 'cmbEstado'>
                        <option <?php echo  $item['estado'] == 'Activo'?'selected':'' ?> >Activo</option>
                        <option <?php echo  $item['estado'] == 'Inactivado'?'selected':'' ?> >Inactivado</option>
                      </select>
                    </td>
                    <td colspan="2" >Debe ser activo para tener acceso al sistema</td>
                  </tr>
                  <tr>
                    <th width="80"><b>Permite:</b></th>
                    <th><b>Observación</b></th>
                    <th width="80"><b>Permite:</b></th>
                    <th><b>Observación</b></th>
                  </tr>
                  <tr>
                    <td>Ingreso</td>
                    <td ><input type="checkbox" name="chkIngreso" value="Si" <?php if($item['ingreso']) echo "checked"; ?> >Ingresar nuevos datos.</td>
                    <td>AdmCostos</td>
                    <td><input type="checkbox" name="chkAdmCostos" value="Si" <?php if($item['admCostos']) echo "checked"; ?>>Ver costos asignados a todos los usuarios.</td>
                  </tr>
                  <tr>
                    <td>Edición</td>
                    <td>
                      <input type="checkbox" name="chkEdicion" value="Si" <?php if($item['edicion']) echo "checked"; ?>>Modificar datos ya ingresados.</td>
                    <td>AdmArt</td>
                    <td>
                      <input type="checkbox" name="chkAdmArt" value="Si" <?php if($item['admArt']) echo "checked"; ?>>Administrar artículos y sus kardex</td>
                  </tr>
                  <tr>
                    <td>Consulta</td>
                    <td>
                      <input type="checkbox" name="chkConsulta" value="Si" <?php if($item['consulta']) echo "checked"; ?>>Ver reportes.
                    </td>
                    <td>AdmUnif</td>
                    <td><input type="checkbox" name="chkAdmUnif" value="Si" <?php if($item['admUnif']) echo "checked"; ?>>Administrar uniformes</td>
                  </tr>
                  <tr>
                    <td>Admin</td>
                    <td><input type="checkbox" name="chkAdmin" value="Si" <?php if($item['admin']) echo "checked"; ?>>Eliminar registros y crear usuarios.</td>
                    <td>AdmMovil</td>
                    <td><input type="checkbox" name="chkAdmMovil" value="Si" <?php if($item['admMovil']) echo "checked"; ?>>Administrar Registro Móviles</td>
                  </tr>
                  <tr>
                    <td>Planilla</td>
                    <td><input type="checkbox" name="chkPlanilla" value="Si" <?php if($item['planilla']) echo "checked"; ?>>Eliminar registros de la planilla.</td>
                    <td>AdmPlanilla</td>
                    <td><input type="checkbox" name="chkAdmPlanilla" value="Si" <?php if($item['admPlanilla']) echo "checked"; ?>>Crear planillas y bloquear otros usuarios.</td>
                  </tr>
                  <tr>
                    <td>Plame</td>
                    <td><input type="checkbox" name="chkPlame" value="Si" <?php if($item['plame']) echo "checked"; ?>>Generar archivos para el Plame.</td>
                    <td>AsignDespacho</td>
                    <td><input type="checkbox" name="chkAsignDespacho" value="Si" <?php if($item['asignDespacho']) echo "checked"; ?>>Asignar despachos.</td>
                  </tr>
                  <tr>
                    <td>Seguridad</td>
                    <td><input type="checkbox" name="chkSeguridad" value="Si" <?php if($item['seguridad']) echo "checked"; ?>>Creación y el mantenimiento de usuarios.</td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <th colspan="2"><b>Adicionales</b></th>
                    <th colspan="2"><b>Observación</b></th>
                  </tr>
                  <tr>
                    <td>Tipo Usuario</td>
                    <td>
                      <select name = 'cmbTipoUsuario'>
                        <option <?php echo  $item['tipoUsuario'] == 'Web'?'selected':'' ?> >Web</option>
                        <option <?php echo  $item['tipoUsuario'] == 'Movil'?'selected':'' ?> >Movil</option>
                        <option <?php echo  $item['tipoUsuario'] == 'Mixto'?'selected':'' ?> >Mixto</option>
                      </select>
                      <input type="checkbox" name="chkInvitado" value="Si" <?php if($item['invitado']) echo "checked"; ?>>Invitado<br>
                    </td>
                    <td colspan="2">Web: Solo Web, Movil: Solo Movil, Mixto: Web y Movil</td>
                  </tr>
                  <tr>
                    <td>Carga</td>
                    <td>
                      <select name = 'cmbCargaDespachos'>
                        <option  <?php echo  $item['cargaDespachos'] == Null?'selected':'' ?>  >No</option>
                        <option  <?php echo  $item['cargaDespachos'] == 'Subir'?'selected':'' ?>>Subir</option>
                        <option  <?php echo  $item['cargaDespachos'] == 'Adm'?'selected':'' ?>>Adm</option>
                      </select>
                       Despachos
                    </td>
                    <td colspan="2">No: Sin acceso, Subir: Solo subir archivo, Adm: Crea Despachos</td>
                  </tr>
                  <tr>
                    <td>DNI</td>
                    <td><input type="text" name="txtDni" size="12" maxlength="8" value= '<?php echo $item['dni'] ; ?>'></td>
                    <td colspan="2">Es necesario solo si el usuario recibe costos o es invitado.</td>
                  </tr>
                  <tr>
                    <td>Email</td>
                    <td><input  type="email" name="txtEmail" id="txtEmail" size="35" maxlength="100" value= '<?php echo $item['email'] ; ?>'></td>
                    <td colspan="2">Es necesario solo si el usuario recibe correos.</td>
                  </tr>
                </table>
              </div>
              <div id="tabOpcAlertas">
                <table width="750px">
                  <tr>
                    <th width="280"><b>Nombre:</b></th>
                    <th><b>Observación</b></th>
                  </tr>
                  <tr>
                    <td>
                      <input class="classAlerta" type="checkbox" name="chkAlertVehVenc" value="Si" <?php if(isset($arrPermisos['alertVehVenc'])) echo "checked"; ?>>Vehículos vencimientos
                    </td>
                    <td rowspan="2" >Cada tipo tiene un grupo de alertas relacionado al mismo.<br>
                    Las alertas llegarán al correo registrado como dato del usuario</td>
                  </tr>
                  <tr>
                    <td>
                      <input class="classAlerta"  type="checkbox" name="chkAlertPersVenc" value="Si" <?php if(isset($arrPermisos['alertPersVenc'])) echo "checked"; ?>>Personal vencimientos
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input class="classAlerta"  type="checkbox" name="chkAlertCli" value="Si" <?php if(isset($arrPermisos['alertCli'])) echo "checked"; ?>>Clientes
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input class="classAlerta" type="checkbox" name="chkAlertCliCobr" value="Si" <?php if(isset($arrPermisos['alertCliCobr'])) echo "checked"; ?>>Clientes cobranzas
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input class="classAlerta" type="checkbox" name="chkAlertOpVeh" value="Si" <?php if(isset($arrPermisos['alertOpVeh'])) echo "checked"; ?>>Operaciones vehículo
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input class="classAlerta" type="checkbox" name="chkAlertOpDesp" value="Si" <?php if(isset($arrPermisos['alertOpDesp'])) echo "checked"; ?>>Operaciones despacho
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input class="classAlerta" type="checkbox" name="chkAlertOpPers" value="Si" <?php if(isset($arrPermisos['alertOpPers'])) echo "checked"; ?>>Operaciones personal
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input class="classAlerta" type="checkbox" name="chkAlertMantTbl" value="Si" <?php if(isset($arrPermisos['alertMantTbl'])) echo "checked"; ?>>Mantenimiento tablas
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input class="classAlerta" type="checkbox" name="chkAlertMantPlani" value="Si" <?php if(isset($arrPermisos['alertMantPlani'])) echo "checked"; ?>>Mantenimiento planilla
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input class="classAlerta" type="checkbox" name="chkAlertMantFact" value="Si" <?php if(isset($arrPermisos['alertMantFact'])) echo "checked"; ?>>Mantenimiento facturas
                    </td>
                  </tr>
                </table>
              </div>
              <div id="tabOpcPermisos">
                <table width="750px">
                  <tr>
                    <th width="280"><b>Nombre:</b></th>
                    <th><b>Observación</b></th>
                  </tr>
                  <tr>
                    <td>
                      <input type="checkbox" name="chkEditaUltKm" value="Si" <?php if(isset($arrPermisos['kmUltMedicion'])) echo "checked"; ?>>Editar campo "Km. Ult. Edición en editar vehículo"
                    </td>
                    <td rowspan="2" >Cada tipo tiene un grupo de alertas relacionado al mismo.<br>
                  Las alertas llegarán al correo registrado como dato del usuario</td>
                  </tr>
                  <tr>
                    <td>
                      <input type="checkbox" name="chkAdmVehiculo" value="Si" <?php if(isset($arrPermisos['admVehiculo'])) echo "checked"; ?>>Adm. campos controlados de los vehículos
                    </td>
                  </tr>
                </table>
              </div>
              <div id="tabOpcReportes">
              </div>
            </div>

            <div align="right">
              <input style="margin:4px;" type="submit" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" value=" &nbsp;Enviar&nbsp; " name = "btnEnviar" >
              <input type=hidden name=idUsuario size=94 value = '<?php echo  $idUsuario ?>'>
              <input type=hidden name=estadoOriginal size=94 value = '<?php echo  $item['estado'] ?>'>
            </div>
          </div>
         <?php  } ?> 
        </form>
      </body>
    </html>
<?php } else {
    //echo "TODO OK";
    require_once './librerias/conectar.php';
    global $servidor, $bd, $usuario, $contrasenia;
    $db = new PDO('mysql:host=' . $servidor . ';dbname=' . $bd, $usuario, $contrasenia);
    $usuario = $_SESSION['usuario'];
    $nombre = $_POST['txtNombre'];
    //$pass = $_POST['txtPass'];
    $chkInvitado = (isset($_POST['chkInvitado']))?$_POST['chkInvitado']:null;

    function insertaUsuPermiso($db, $idUsuario , $nombPermiso, $nivel = 1){
      $creacUsuario = $_SESSION["usuario"];
      $inserta = $db->prepare("INSERT INTO `usupermisos` ( `idUsuario`, `nombPermiso`, `nivel`, `creacUsuario`, `creacFch`) VALUES (:idUsuario, :nombPermiso, :nivel, :creacUsuario, now())");
      $inserta->bindParam(':idUsuario',$idUsuario);
      $inserta->bindParam(':nombPermiso',$nombPermiso);
      $inserta->bindParam(':nivel',$nivel);
      $inserta->bindParam(':creacUsuario',$creacUsuario);
      $inserta->execute();
      return $inserta->rowCount();
    }

    if ($chkInvitado == 'Si'){
      $chkIngreso = $chkEdicion = $chkConsulta = $chkAdmCostos = $chkAdmin = $chkAdmArt = NULL;
      $chkPlanilla = $chkAdmPlanilla = $chkAdmUnif = $chkPlame = $chkAdmMovil = $chkSeguridad = NULL;
    } else {
      $chkIngreso = isset($_POST['chkIngreso'])?$_POST['chkIngreso']:null;
      $chkEdicion = isset($_POST['chkEdicion'])?$_POST['chkEdicion']:null;
      $chkConsulta = isset($_POST['chkConsulta'])?$_POST['chkConsulta']:null;
      $chkAdmCostos = (isset($_POST['chkAdmCostos']))?$_POST['chkAdmCostos']:null;
      $chkAdmin = isset($_POST['chkAdmin'])?$_POST['chkAdmin']:null;
      $chkAdmArt = isset($_POST['chkAdmArt'])?$_POST['chkAdmArt']:null;
      $chkPlanilla = isset($_POST['chkPlanilla'])?$_POST['chkPlanilla']:null;
      $chkAdmPlanilla = isset($_POST['chkAdmPlanilla'])?$_POST['chkAdmPlanilla']:null;
      $chkAdmUnif = isset($_POST['chkAdmUnif'])?$_POST['chkAdmUnif']:null;
      $chkPlame = isset($_POST['chkPlame'])?$_POST['chkPlame']:null;
      $chkSeguridad = isset($_POST['chkSeguridad'])?$_POST['chkSeguridad']:null;
      $chkAdmMovil = (isset($_POST['chkAdmMovil']))?$_POST['chkAdmMovil']:null;
      $chkAsignDespacho = isset($_POST['chkAsignDespacho'])?$_POST['chkAsignDespacho']:null;
      $chkEditaUltKm = isset($_POST['chkEditaUltKm'])?$_POST['chkEditaUltKm']:null;
      $chkAdmVehiculo = isset($_POST['chkAdmVehiculo'])?$_POST['chkAdmVehiculo']:null;

      ////// Alertas //////
      $chkAlertVehVenc = isset($_POST['chkAlertVehVenc'])?$_POST['chkAlertVehVenc']:null;
      $chkAlertPersVenc = isset($_POST['chkAlertPersVenc'])?$_POST['chkAlertPersVenc']:null;
      $chkAlertCli = isset($_POST['chkAlertCli'])?$_POST['chkAlertCli']:null;
      $chkAlertCliCobr = isset($_POST['chkAlertCliCobr'])?$_POST['chkAlertCliCobr']:null;
      $chkAlertOpVeh = isset($_POST['chkAlertOpVeh'])?$_POST['chkAlertOpVeh']:null;
      $chkAlertOpDesp = isset($_POST['chkAlertOpDesp'])?$_POST['chkAlertOpDesp']:null;
      $chkAlertOpPers = isset($_POST['chkAlertOpPers'])?$_POST['chkAlertOpPers']:null;
      $chkAlertMantTbl = isset($_POST['chkAlertMantTbl'])?$_POST['chkAlertMantTbl']:null;
      $chkAlertMantPlani = isset($_POST['chkAlertMantPlani'])?$_POST['chkAlertMantPlani']:null;
      $chkAlertMantFact = isset($_POST['chkAlertMantFact'])?$_POST['chkAlertMantFact']:null;
      echo "Mantenimiento Planilla :$chkAlertMantPlani";
    }
    $dni = (isset($_POST['txtDni']))?$_POST['txtDni']:null;
    $fchVencimiento = $_POST['txtFchVenc'];
    $tipoUsuario =  $_POST['cmbTipoUsuario'];
    $email =  $_POST['txtEmail'];
    $cargaDespachos =  ($_POST['cmbCargaDespachos'] == 'No')?NULL:$_POST['cmbCargaDespachos'];
  
    $actualiza = $db->prepare("UPDATE `usuario` SET `nombre` = :nombre, `admin` = :chkAdmin, `admCostos` = :chkAdmCostos, `admArt` = :chkAdmArt, `edicion` = :chkEdicion, `ingreso` = :chkIngreso, `consulta` = :chkConsulta, `planilla` = :chkPlanilla, `asignDespacho` = :chkAsignDespacho,  `admPlanilla` = :chkAdmPlanilla,  `admUnif` = :chkAdmUnif,  `plame` = :chkPlame, `admMovil` = :chkAdmMovil, `tipoUsuario` = :tipoUsuario, `cargaDespachos` = :cargaDespachos, `seguridad` = :chkSeguridad,  `invitado` = :chkInvitado, `dni` = :dni, `fchVencimiento` = :fchVencimiento, `email` = :email, `usuario` = :usuario   WHERE `idUsuario` = :idUsuario");
    $actualiza->bindParam(':nombre',$nombre);
    //$actualiza->bindParam(':pass',$pass);
    $actualiza->bindParam(':chkAdmCostos',$chkAdmCostos);
    $actualiza->bindParam(':chkAdmArt',$chkAdmArt);
    $actualiza->bindParam(':chkAdmin',$chkAdmin);
    $actualiza->bindParam(':chkEdicion',$chkEdicion);
    $actualiza->bindParam(':chkIngreso',$chkIngreso);
    $actualiza->bindParam(':chkConsulta',$chkConsulta);
    $actualiza->bindParam(':chkPlanilla',$chkPlanilla);
    $actualiza->bindParam(':chkAsignDespacho',$chkAsignDespacho);
    $actualiza->bindParam(':chkAdmPlanilla',$chkAdmPlanilla);
    $actualiza->bindParam(':chkAdmUnif',$chkAdmUnif);
    $actualiza->bindParam(':cargaDespachos',$cargaDespachos);
    $actualiza->bindParam(':chkSeguridad',$chkSeguridad);
    $actualiza->bindParam(':chkInvitado',$chkInvitado);
    $actualiza->bindParam(':chkPlame',$chkPlame);
    $actualiza->bindParam(':chkAdmMovil',$chkAdmMovil);
    $actualiza->bindParam(':tipoUsuario',$tipoUsuario);
    $actualiza->bindParam(':dni',$dni);
    $actualiza->bindParam(':fchVencimiento',$fchVencimiento);
    $actualiza->bindParam(':email',$email);
    $actualiza->bindParam(':idUsuario',$idUsuario);
    $actualiza->bindParam(':usuario',$usuario);
    $actualiza->execute();

    //Elimina registros de permisos del usuario  
    $elimina = $db->prepare("DELETE FROM `usupermisos` WHERE `idUsuario` = :idUsuario");
    $elimina->bindParam(':idUsuario',$idUsuario);    
    $elimina->execute();

    //Insertar alertas
    if($chkAlertVehVenc != NULL) $cant = insertaUsuPermiso($db, $idUsuario ,'alertVehVenc','1');
    if($chkAlertPersVenc != NULL) $cant = insertaUsuPermiso($db, $idUsuario ,'alertPersVenc','1');
    if($chkAlertCli != NULL) $cant = insertaUsuPermiso($db, $idUsuario ,'alertCli','1');
    if($chkAlertCliCobr != NULL) $cant = insertaUsuPermiso($db, $idUsuario ,'alertCliCobr','1');
    if($chkAlertOpVeh != NULL) $cant = insertaUsuPermiso($db, $idUsuario ,'alertOpVeh','1');
    if($chkAlertOpDesp != NULL) $cant = insertaUsuPermiso($db, $idUsuario ,'alertOpDesp','1');
    if($chkAlertOpPers != NULL) $cant = insertaUsuPermiso($db, $idUsuario ,'alertOpPers','1');
    if($chkAlertMantTbl != NULL) $cant = insertaUsuPermiso($db, $idUsuario ,'alertMantTbl','1');
    if($chkAlertMantPlani != NULL) $cant = insertaUsuPermiso($db, $idUsuario ,'alertMantPlani','1');
    if($chkAlertMantFact != NULL) $cant = insertaUsuPermiso($db, $idUsuario ,'alertMantFact','1');

    //Insertar permiso
    if($chkEditaUltKm != NULL) $cant = insertaUsuPermiso($db, $idUsuario ,'kmUltMedicion','1');
    if($chkAdmVehiculo != NULL) $cant = insertaUsuPermiso($db, $idUsuario ,'admVehiculo','1');

 ?>

  <html>
    <head>
      <meta http-equiv="content-type" content="text/html; charset=windows-1250">
      <meta name="generator" content="PSPad editor, www.pspad.com">
      <meta HTTP-EQUIV="REFRESH" content="2; url=index.php?controlador=aplicacion&accion=listarusuario">
      <title></title>
    </head>
    <body>
      Su registro fue grabado. Espere por favor
    </body>
  </html>

<?php } ?>
