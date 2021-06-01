<?php
session_start();
include("verificaRegistro2.php");
include('conectaBD.php');
?>

<?
//Establecer la conexión a la base de datos
$conexion = conectar();

//Libera la sesión del proyecto seleccionado
$_SESSION["sesProyectoSelec"] = "";
$_SESSION["sesNombreProyecto"] = "" ;

// Selecciona los proyectos en los que el usuario activo Tiene participación como Director
//o encargado en la tabla AsignaProyectos
$sql="Select A.* , T.nomTipoProy, P.nombre, P.codigo, P.cargo_defecto ";
$sql= $sql. " from GestiondeInformacionDigital.dbo.asignaProyectos A, ";
$sql= $sql. " GestiondeInformacionDigital.dbo.TiposProyectos T,  ";
$sql= $sql. " HojaDeTiempo.dbo.Proyectos P ";
$sql= $sql. " where A.codTipoProy = T.codTipoProy ";
$sql= $sql. " and A.id_proyecto = P.id_proyecto ";
$sql= $sql. " AND (A.unidadDirector = ". $_SESSION["sesUnidadUsuario"] . " OR A.unidadEncargado = ". $_SESSION["sesUnidadUsuario"] . " ) ";
$cursor = mssql_query($sql);

// Selecciona los proyectos en los que el usuario activo Tiene participación como 
//usuario Normal en la tabla UsuariosProyectos
$sql2="Select U.*, P.nombre, P.codigo, P.cargo_defecto ";
$sql2= $sql2. " from GestiondeInformacionDigital.dbo.UsuariosProyectos U,  ";
$sql2= $sql2. " HojaDeTiempo.dbo.Proyectos P ";
$sql2= $sql2. " where U.id_proyecto = P.id_proyecto ";
$sql2= $sql2. " and U.unidad = " .  $_SESSION["sesUnidadUsuario"] ;
$sql2= $sql2. " and Not exists  ";
$sql2= $sql2. " ( Select * from GestiondeInformacionDigital.dbo.asignaProyectos ";
$sql2= $sql2. " 	where id_proyecto = U.Id_proyecto ";
$sql2= $sql2. " 	and (unidadDirector = U.unidad Or unidadEncargado = U.unidad) ";
$sql2= $sql2. " ) ";
//22Abr2008
//Para que no traiga el proyecto Formatos Calidad y obligar al usuario a ingresar por la opción del ícono
$sql2= $sql2. " and U.id_proyecto <> 646 ";

$cursor2 = mssql_query($sql2);

//Para visualizar la encuesta
//if (($_SESSION["sesUnidadUsuario"] == 15712) OR ($_SESSION["sesUnidadUsuario"] == 12974)) {
//	$cadenaLoadEnc= "MM_openBrWindow('RHumanos/medEncuesta.php','winMedEncuesta', 'scrollbars=yes,resizable=yes,width=600,height=500')"; 
	$cadenaLoadEnc= ""; 
//}
?>
<html>
<head>
<title>Gesti&oacute;n de Archivos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
window.name="winArchivos";
</script>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6" onLoad="<? echo trim($cadenaLoadEnc) ; ?>">
<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr valign="top">
    <td width="30%">
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td height="20" class="TituloUsuario">SISTEMA PARA TRANSFERENCIA DE ARCHIVOS </td>
		  </tr>
		</table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr class="TxtTabla">
          <td><div class="menu"  style="cursor: hand" onClick="MM_openBrWindow('sisFTP/solFTP.php','winSolFTP','scrollbars=yes,resizable=yes,height=650')">SISTEMA PARA TRANSFERENCIA DE ARCHIVOS</div></td>
          <td>&nbsp;</td>
          <td><a href="#" class="menu" onClick="MM_openBrWindow('Ayuda/sisFTP.pdf','wPDAut','scrollbars=yes,resizable=yes,height=400')">Manual</a></td>
        </tr>
      </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
		<?
//Para que sólo le aparezca a GRM y PBM, Silvia palacio, Patricia gutierrez y Guillermo bazurto
//if (($_SESSION["sesUnidadUsuario"] == 12974) OR ($_SESSION["sesUnidadUsuario"] == 15712) OR ($_SESSION["sesUnidadUsuario"] == 15850) OR ($_SESSION["sesUnidadUsuario"] == 16362) OR ($_SESSION["sesUnidadUsuario"] == 12947)) {
?><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="20" class="TituloUsuario">SELF SERVICE KACTUS -HR </td>
        </tr>
      </table>
	  
	  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr class="TxtTabla">
          <td><div class="menu"  style="cursor: hand" onClick="MM_openBrWindow('http://erp.ingetec.com.co/webkactusNew','winERP','scrollbars=yes,resizable=yes, width=1024,height=650')">SELF SERVICE DEL EMPLEADO - KACTUS-HR</div></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>
	  <? //} ?>
	  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table>
	  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="20" class="TituloUsuario"> DIVISI&Oacute;N ADMINISTRATIVA </td>
      </tr>
    </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr class="TxtTabla">
          <td><div class="menu"  style="cursor: hand" onClick="MM_openBrWindow('mnuDivAdmin.php','winMnuDivAdm','scrollbars=yes,resizable=yes, width=700,height=300')">Documentos Divisi&oacute;n Administrativa </div></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table>
	  <?
		//Visualizar esta información exclusivamente para GRM y PBM y El perfil de Calidad
		//if (($_SESSION["sesUnidadUsuario"] == 12974) OR ($_SESSION["sesUnidadUsuario"] == 15712) OR ($_SESSION["sesPerfilUsuario"] == 31)) {
		?>
	  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="20" class="TituloUsuario">CONTRATISTAS Y PROVEEDORES </td>
      </tr>
    </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr class="TxtTabla">
          <td><div class="menu"  style="cursor: hand" onClick="MM_openBrWindow('infContratistas/QHSE-03-1_REGISTRO_DE_CONTRATISTAS_Y_PROVEEDORESv2016_.xls','wininfRegistros','scrollbars=yes,resizable=yes, width=1100,height=600')">Registro de Contratistas y Proveedores</div></td>
          <td><div align="center"><a href="#" class="menu" onClick="MM_openBrWindow('Ayuda/videoRegProv/videoRegProv.html','wCPVideo','scrollbars=yes,resizable=yes, width=900,height=680')">Video</a></div></td>
        </tr>
        <tr class="TxtTabla">
          <td><div class="menu"  style="cursor: hand" onClick="MM_openBrWindow('infContratistas/QHSE-03-6_LISTA_DE_PROVEEDORES_NO_RECOMENDADOSv2016.xls','winMnuDivAdm','scrollbars=yes,resizable=yes, width=1100,height=600')">Lista de Proveedores No recomendados </div></td>
          <td>&nbsp;</td>
        </tr>
      </table>
	  <? //} ?>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table>
	  
	  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="20" class="TituloUsuario">HOJA DE TIEMPO </td>
      </tr>
    </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr class="TxtTabla">
          <td><div class="menu"  style="cursor: hand" onClick="MM_openBrWindow('../NuevaHojaTiempo/vtnMenuHT.php','winvtnMenuHT','scrollbars=yes,resizable=yes,width=650,height=480')">SOFTWARE NUEVA HOJA DE TIEMPO</div></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table>
	  <?
	  //11Dic2015
	  //PBM
	  //Traer el listado de unidades  que aparecen como usuario interno en el módulo de muestras para habilitarlo
	  $esClienteInternoLab=0;
	  $sqlIsuIntLab="SELECT distinct unidadClienteInt FROM GestiondeInformacionDigital.dbo.LEsiProyectos  ";
	  $sqlIsuIntLab=$sqlIsuIntLab." where unidadClienteInt is not null ";
	  $sqlIsuIntLab=$sqlIsuIntLab." and unidadClienteInt = " . $_SESSION["sesUnidadUsuario"];
	  $cursorsqlIsuIntLab = mssql_query($sqlIsuIntLab);
	  if ($regIsuIntLab = mssql_fetch_array($cursorsqlIsuIntLab)) {
	  		$esClienteInternoLab=1;
	  }

//	  if ( ($_SESSION["sesPerfilUsuario"] == "1") OR ($_SESSION["sesPerfilUsuario"] == "24") OR ($_SESSION["sesPerfilUsuario"] == "26")) {
	  if ( ($_SESSION["sesPerfilUsuario"] == "1") OR ($_SESSION["sesPerfilUsuario"] == "24") OR ($_SESSION["sesPerfilUsuario"] == "26") OR ($esClienteInternoLab==1))  {
	  ?>
	  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="20" class="TituloUsuario">MUESTRAS DE LABORATORIO </td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr class="TxtTabla">
          <td><div class="menu"  style="cursor: hand" onClick="MM_openBrWindow('modLaboratorioExt/mlExploraMuestra.php','winMueLab','scrollbars=yes,resizable=yes,width=1000,height=450')">MUESTRAS DE LABORATORIO </div></td>
          <td>&nbsp;</td>
        </tr>
      </table>
	  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table>	  <?
	  }
	  ?>
	  
	  
	  <?
	  //15Abr2016
	  //PBM
	  //Activación de módulo de Laboratorio de Materiales
	  //Habilitado para todo el mundo segun instrucciones de Luis osorio
//	  if ( ($_SESSION["sesPerfilUsuario"] == "1") )  {
	  ?>
	  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="20" class="TituloUsuario">LABORATORIO DE MATERIALES NTC - 17025 </td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr class="TxtTabla">
          <td><div class="menu"  style="cursor: hand" onClick="MM_openBrWindow('mnuLabMateriales.php','winMnuLab','scrollbars=yes,resizable=yes,width=1000,height=450')">LABORATORIO DE MATERIALES</div></td>
          <td>&nbsp;</td>
        </tr>
      </table>
	  <?
//	  }
	  ?>

		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>	</td>
    <td width="40%"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td align="center" valign="top">
        <!--
        <a href="http://www.hazquesuceda.com/zona-virtual/" target="_blank"><img src="imagenes/imgWebinar2016.png" width="450" height="290"></a>
        -->
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table></td>
    <td width="30%">
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="20" class="TituloUsuario">&nbsp;</td>
          </tr>
    </table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr class="TxtTabla">
              <td height="5" colspan="5" > </td>
        </tr>
            <tr class="TxtTabla">
              <td width="1%" valign="middle"><span class="menu"><img src="images/imgLogo.gif" width="16" height="25"></span></td>
              <td width="1%" valign="middle">&nbsp;</td>
              <td align="left"><a href="https://intranet.ingetec.com.co/ImagenCorporativa" target="_blank" class="menu">Imagen INGETEC </a></td>
              <td align="left">&nbsp;</td>
              <td width="3%" valign="middle">&nbsp;</td>
            </tr>
            <tr class="TxtTabla">
              <td height="5" colspan="5" valign="middle"> </td>
        </tr>
            <tr class="TxtTabla">
              <td height="1" colspan="5" valign="middle" class="TituloUsuario"> </td>
            </tr>
          </table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>
		<?
		//Visualizar esta información exclusivamente para GRM y PBM
		//if (($_SESSION["sesUnidadUsuario"] == 12974) OR ($_SESSION["sesUnidadUsuario"] == 15712)) {
		?>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr class="TxtTabla">
              <td height="5" colspan="5" > </td>
              </tr>
            <tr class="TxtTabla">
              <td width="1%" valign="middle"><span class="menu"><img src="images/icoCadd.gif" width="16" height="23"></span></td>
              <td width="1%" valign="middle">&nbsp;</td>
              <td align="left"><a href="validaRegistroCADD.php" target="_blank" class="menu">Estandarizaci&oacute;n CADD </a></td>
              <td align="left">&nbsp;</td>
              <td width="3%" valign="middle">&nbsp;</td>
            </tr>
            <tr class="TxtTabla">
              <td height="5" colspan="5" valign="middle"> </td>
              </tr>
            <tr class="TxtTabla">
              <td height="1" colspan="5" valign="middle" class="TituloUsuario"> </td>
            </tr>
          </table>
		  <? //} ?>	
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr class="TxtTabla">
              <td height="5" colspan="5" > </td>
              </tr>
            <tr class="TxtTabla">
              <td width="1%" valign="middle"><span class="menu"><img src="images/icoEmail.gif" width="16" height="15"></span></td>
              <td width="1%" valign="middle">&nbsp;</td>
              <td align="left"><a href="usoCorreo/Uso_Correo.pdf" target="_blank" class="menu"  width="500" height="375">Uso Correo</a></td>
              <td align="left">&nbsp;</td>
              <td width="3%" valign="middle">&nbsp;</td>
            </tr>
            <tr class="TxtTabla">
              <td height="5" colspan="5" valign="middle"> </td>
              </tr>
            <tr class="TxtTabla">
              <td height="1" colspan="5" valign="middle" class="TituloUsuario"> </td>
            </tr>
          </table>
		  <?
		//Visualizar esta información exclusivamente para GRM y PBM
		//if (($_SESSION["sesUnidadUsuario"] == 12974) OR ($_SESSION["sesUnidadUsuario"] == 15712)) {
		?>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr class="TxtTabla">
              <td height="5" colspan="5" > </td>
              </tr>
            <tr class="TxtTabla">
              <td width="1%" valign="middle"><span class="menu"><img src="images/alertaAzul.gif" width="15" height="16"></span></td>
              <td width="1%" valign="middle">&nbsp;</td>
              <td align="left"><a href="#"  class="menu" onClick="MM_openBrWindow('capacitarTicTac/manuales.php','winManuales','scrollbars=yes,resizable=yes,width=500,height=350')" >Material Capacitaci&oacute;n Tic-Tac </a></td>
              <td align="left">&nbsp;</td>
              <td width="3%" valign="middle">&nbsp;</td>
            </tr>
            <tr class="TxtTabla">
              <td height="5" colspan="5" valign="middle"> </td>
              </tr>
            <tr class="TxtTabla">
              <td height="1" colspan="5" valign="middle" class="TituloUsuario"> </td>
            </tr>
          </table>		  </td>
      </tr>
    </table>
	<? //} ?>	
	<?
		//Visualizar esta información exclusivamente para GRM y PBM
		//if (($_SESSION["sesUnidadUsuario"] == 12974) OR ($_SESSION["sesUnidadUsuario"] == 15712)) {
		?>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr class="TxtTabla">
              <td height="5" colspan="5" > </td>
              </tr>
            <tr class="TxtTabla">
              <td width="1%" valign="middle"><span class="menu"><img src="images/imgXerox.png" width="17" height="17"></span></td>
              <td width="1%" valign="middle">&nbsp;</td>
              <td align="left">
			  <a href="videoImpresion/" target="_blank"  class="menu"  >Video Capacitaci&oacute;n Impresi&oacute;n </a>
			  </td>
              <td align="left">&nbsp;</td>
              <td width="3%" valign="middle">&nbsp;</td>
            </tr>
            <tr class="TxtTabla">
              <td height="5" colspan="5" valign="middle"> </td>
              </tr>
            <tr class="TxtTabla">
              <td height="1" colspan="5" valign="middle" class="TituloUsuario"> </td>
            </tr>
          </table>		  </td>
      </tr>
    </table>
	<? //} ?>
	<?
		//Visualizar esta información exclusivamente para usuarios del ERP
		$esUsuarioERP=0;
		$sqlSeven="select count(*) existeUsu ";
		$sqlSeven=$sqlSeven." from HojaDeTiempo.dbo.UsuariosERP  ";
		$sqlSeven=$sqlSeven." where Aplicacion = 'SEVEN' ";
		$sqlSeven=$sqlSeven." AND Unidad = " . $_SESSION["sesUnidadUsuario"];
		$cursorSeven = mssql_query($sqlSeven);
		if ($regSeven=mssql_fetch_array($cursorSeven)) {
			$esUsuarioERP = $regSeven[existeUsu] ;
		}

		if ($esUsuarioERP > 0) {
		//if (($_SESSION["sesUnidadUsuario"] == 16362) OR ($_SESSION["sesUnidadUsuario"] == 14888) OR ($_SESSION["sesUnidadUsuario"] == 16035)) {
		
		?>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr class="TxtTabla">
              <td height="5" colspan="5" > </td>
              </tr>
            <tr class="TxtTabla">
              <td width="1%" valign="middle"><span class="menu"><img src="images/arrMedia.gif" width="15" height="15"></span></td>
              <td width="1%" valign="middle">&nbsp;</td>
              <td align="left"><a href="#"  class="menu" onClick="MM_openBrWindow('http://192.168.30.169/seven/','winERP','scrollbars=yes,resizable=yes,width=800,height=700')" >Usuarios SEVEN - PRODUCCIÓN </a></td>
              <td align="left">&nbsp;</td>
              <td width="3%" valign="middle">&nbsp;</td>
            </tr>
            <tr class="TxtTabla">
              <td height="5" colspan="5" valign="middle"> </td>
              </tr>
            <tr class="TxtTabla">
              <td height="1" colspan="5" valign="middle" class="TituloUsuario"> </td>
            </tr>
          </table>		  </td>
      </tr>
    </table>
	<? } ?>

<?
//Encuesta de servicio de impresión
//Modulo 4 - Encuestas
//Responsable : Patricia Gutiérrez

/*
* Documentación Funcionalidad Encuesta de Servicios de Impresión
* @param cual	7= Encuesta de Sonda
* @param tipoE	1= Tipo de Encabezado =1 Básico para usuarios registrados en el portal
* @exception	Se tienen en cuenta las fechas de publicación de la ficha. El link solo aparece si el usuario no ha contestado ninguna encuesta en el periodo de publicación 
* @return       No hay retorno de información
* @author  Patricia Gutiérrez Restrepo
* @version 1.0 mpgr 2015/07/30
*/
//Visualización unicamente GRM y PGR
//if (($_SESSION["sesUnidadUsuario"] == 12974) OR ($_SESSION["sesUnidadUsuario"] == 16362) OR ($_SESSION["sesUnidadUsuario"] == 17020) OR ($_SESSION["sesUnidadUsuario"] == 16176) OR ($_SESSION["sesUnidadUsuario"] == 18671)) 
//{
	//Session usada para otras encuestas
	$_SESSION["sisModulo"]=4;
	$_SESSION["sisEncUnidad"]=$_SESSION["sesUnidadUsuario"];
	
	//Obtener la fecha Maxima para ser publicada la Encuesta
	$sqlRta2= " SELECT CONVERT(VARCHAR(10),fechaFinPublicacion, 103) as Fecha,*";
	$sqlRta2= $sqlRta2 . " FROM tbFichas ";	  
	$sqlRta2= $sqlRta2 . " WHERE fechaFinPublicacion > = GETDATE()";	 
	$sqlRta2= $sqlRta2 . " AND fechaIniPublicacion < = GETDATE()";
	$sqlRta2= $sqlRta2 . " AND codFicha  = 7";
	$sqlRta2= $sqlRta2 . " AND estado = 1";	 	  
	$cursor2 = mssql_query($sqlRta2);
	if ($reg2=mssql_fetch_array($cursor2)) //Inicio If - Puede ser publicado
	{	
		if(trim($reg2[Fecha])!="")		   //Inicio if - Visualizar link dependiendo de fecha de publicación 
		{
			//Obtener el número de Encuestas realizadas por el usuario en el periodo de publicación	
			$qry3 = "SELECT COUNT(nroEncuesta) AS cantidad FROM EncFicha ";	
			$qry3 = $qry3. " WHERE codModulo=".$_SESSION["sisModulo"];
			$qry3 = $qry3. " AND codFicha=".$reg2[codFicha];
			$qry3 = $qry3. " AND usuario=".$_SESSION["sesUnidadUsuario"];
			$qry3 = $qry3. " AND ('".$reg2[fechaFinPublicacion]."'> = fechaGraba)" ;
			$qry3 = $qry3. " AND ('".$reg2[fechaIniPublicacion]."'< = fechaGraba)" ;
			$cursorIn3 = mssql_query($qry3) ;					
			if($reg3 = mssql_fetch_array($cursorIn3))
			{  $cuantos=$reg3[cantidad];
			}

			if($cuantos<=0)				//Inicio if - Visualizar link dependiendo de cantidad de encuestas respondidas por el usuario en el periódo de publicación de la encuesta  
			{ ?>
				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td>
							<table width="100%"  border="0" cellspacing="0" cellpadding="0">
								<tr class="TxtTabla">
								  	<td height="5" colspan="5" > </td>
								</tr>
								<tr class="TxtTabla">
									  <td width="1%" valign="middle"><span class="menu"><img src="images/imgPrint.gif" width="15" height="16"></span></td>
									  <td width="1%" valign="middle">&nbsp;</td>
									  <td align="left"><a href="#"  class="menu" onClick="MM_openBrWindow('sisEncuesta/cargaEncuesta.php?cual=7&tipoE=1','winManuales','scrollbars=yes,resizable=yes,width=500,height=350')" >Evaluación Servicio de Impresi&oacute;n</a></td>
									  <td align="left">&nbsp;</td>
									  <td width="3%" valign="middle">&nbsp;</td>
								</tr>
								<tr class="TxtTabla">
									  <td height="5" colspan="5" valign="middle"> </td>
							  </tr>
								<tr class="TxtTabla">
									  <td height="1" colspan="5" valign="middle" class="TituloUsuario"> </td>
								</tr>
						  </table>		  
						</td>
					</tr>
				</table>
				<? } //FIN if - Visualizar link dependiendo de cantidad de encuestas respondidas por el usuario en el periódo de publicación de la encuesta  ?>
                <? }  //FIN if - Visualizar link dependiendo de fecha de publicación ?>
                <? } //FIN If - Puede ser publicado?>
                <? //} //FIN Visualización unicamente GRM y PGR?>
                <?

//		if ($_SESSION["sesUnidadUsuario"] == 15712) {
		
		?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
<?php
		//CONSULTA SI EL USUARIO TIENE ACCESO A LA APLICACION DE EXP. FIRMA
		$sql_usuario="SELECT * from GestiondeInformacionDigital.dbo.EFUsuarios where unidad=".$_SESSION["sesUnidadUsuario"]." and estado=1";
		$cur_usuario=mssql_query($sql_usuario);
		if(mssql_num_rows($cur_usuario)>0)
		{
?>           
          <tr>
            <td  width="1%"  valign="middle" >
		         <span class="menu"><img src="../sistemas/expfirma/img/EF4.png" width="20" height="22"></span>
            </td>
            <td><a href="../sistemas/expfirma" target="_blank" class="menu">Experiencia de la firma</a></td>
	      </tr>
          <tr class="TxtTabla">
	          <td colspan="2" class="TituloUsuario" valign="middle" height="1"> </td>
           </tr>          
<?php
		}
?>          
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr class="TxtTabla">
              <td height="5" colspan="5" > </td>
              </tr>
            <tr class="TxtTabla">
              <td width="1%" valign="middle"><span class="menu"><img src="images/icoLC.gif" width="20" height="20"></span></td>
              <td width="1%" valign="middle">&nbsp;</td>
              <td align="left"><a href="#"  class="menu" onClick="MM_openBrWindow('capacitarAutenticacion/ManualAutenticacionv04.pdf','winAutentica','scrollbars=yes,resizable=yes,width=800,height=700')" >Autenticación de usuarios en la INTRANET </a></td>
              <td align="left">&nbsp;</td>
              <td width="3%" valign="middle"><a href="#" width="500" height="375"><img src="images/gifNuevo.gif" width="64" height="10" border="0" onClick="MM_openBrWindow('capacitarAutenticacion/ManualAutenticacionv04.pdf','winAutentica','scrollbars=yes,resizable=yes,width=500,height=350')" ></a></td>
            </tr>
            <tr class="TxtTabla">
              <td height="5" colspan="5" valign="middle"> </td>
              </tr>
            <tr class="TxtTabla">
              <td height="1" colspan="5" valign="middle" class="TituloUsuario"> </td>
            </tr>
          </table>		  </td>
      </tr>
    </table>
	<? // } ?>	  	  </td>
      </tr>
    </table>  	</td>
  </tr>
</table>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

<table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td class="TxtNota1" >Gesti&oacute;n de Proyectos </td>
            <td class="Fecha" >
			<? 
			echo strtoupper($_SESSION["sesNomApeUsuario"]) ;
			// echo strtoupper($nombreempleado." ".$apellidoempleado); ?>
			</td>
          </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
</table>




<?
//17Mar20110
//Para que Enrique angulo (5037) y Alvaro Castro (10636) NO VEAN ESTA SECCIÓN ya que ellos ven TODOS los proyectos de interventoría
//mas abajo
//if (($_SESSION["sesUnidadUsuario"] != 5037) AND ($_SESSION["sesUnidadUsuario"] != 10636)) {

?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Proyectos autorizados</td>
  </tr>
</table>
<table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td>
	<table width="100%"  border="0" cellpadding="0" cellspacing="1">
  <tr class="TituloTabla2">
    <td>Proyecto</td>
    <td width="5%">&nbsp;</td>
    <td width="12%">&nbsp;</td>
    <td width="12%">Ver Correo electr&oacute;nico hist&oacute;rico </td>
  </tr>
  	  <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

  <tr class="TxtTabla">
    <td>
	<?
	$codigo2 = $reg[codigo];
	$cargo2  = $reg[cargo_defecto];
	//Arma el código del proyecto
	$codProy = $codigo2.$cargo2; 

	?>
	<a href="sesProyectoElegido.php?codigo=<? echo $codProy ?>&nomProy=<? echo $reg[nombre]; ?>&cualProy=<? echo $reg[id_proyecto]; ?>"><? echo ucwords(strtolower($reg[nombre])); ?></a></td>
    <td width="5%" align="center"><a href="cargaProyecto.php?cualProy=<? echo $reg[id_proyecto] ?>"><img src="images/Aprobado.gif" alt="Director / Encargado" width="21" height="24" border="0"></a></td>
    <td width="12%"><a href="cargaProyecto.php?cualProy=<? echo $reg[id_proyecto] ?>"><img src="imagenes/btnDetalle.jpg" width="169" height="17" border="0"></a></td>
    <td width="12%" align="center">
	<?
	$wsQl="Select * from UsuariosProyectos ";
	$wsQl=$wsQl." where id_proyecto = " . $reg[id_proyecto];
	$wsQl=$wsQl." and unidad = " . $_SESSION["sesUnidadUsuario"]; 
	$cursorwsQl = mssql_query($wsQl);
	if ($regwsQl=mssql_fetch_array($cursorwsQl)) {
		$miMailProy = $regwsQl[verMail]; 
	}
	?>
	<? if ($miMailProy == "1") { ?>
	<a href="cargaProyectoMail.php?cualProy=<? echo $reg[id_proyecto] ?>"><img src="images/icoCorreoE.gif" alt="Consultar Correo electr&oacute;nico del proyecto" width="20" height="16" border="0"></a>
	<? } ?>
	</td>
  </tr>
  <? } ?>
  	  <?
	  $cursor2 = mssql_query($sql2);
	  while ($reg2=mssql_fetch_array($cursor2)) {
	  ?>
  
  <tr class="TxtTabla">
    <td>
	<?
	$codigo2 = $reg2[codigo];
	$cargo2  = $reg2[cargo_defecto];
	//Arma el código del proyecto
	$codProy = $codigo2.$cargo2; 
	?>
	<a href="sesProyectoElegido.php?codigo=<? echo $codProy ?>&nomProy=<? echo $reg2[nombre]; ?>&cualProy=<? echo $reg2[id_proyecto]; ?>"><? echo ucwords(strtolower($reg2[nombre])); ?></a>
	</td>
    <td align="center">&nbsp;</td>
    <td width="12%"><a href="cargaProyecto.php?cualProy=<? echo $reg2[id_proyecto] ?>"><img src="imagenes/btnDetalle.jpg" width="169" height="17" border="0"></a></td>
    <td width="12%" align="center">
	<?
	$wsQl="Select * from UsuariosProyectos ";
	$wsQl=$wsQl." where id_proyecto = " . $reg2[id_proyecto];
	$wsQl=$wsQl." and unidad = " . $_SESSION["sesUnidadUsuario"]; 
	$cursorwsQl = mssql_query($wsQl);
	if ($regwsQl=mssql_fetch_array($cursorwsQl)) {
		$miMailProy = $regwsQl[verMail]; 
	}
	?>
	<? if ($miMailProy == "1") { ?>
	<a href="cargaProyectoMail.php?cualProy=<? echo $reg2[id_proyecto] ?>"><img src="images/icoCorreoE.gif" alt="Consultar Correo electr&oacute;nico del proyecto" width="20" height="16" border="0"></a>
	<? } ?>
	</td>
  </tr>
  <? } ?>
</table>
	</td>
  </tr>
</table>
<? // } // CIERRE if de si no es enrique Angulo o Alvaro castro ?>    
	<table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td>&nbsp;</td>
      </tr>
</table>

<?
//12Jun2008
//Para que Camilo Marulanda pueda ver todos los proyectos
//if ($_SESSION["sesUnidadUsuario"] == 14384) {
//10Jul2008
//Para que Liliana Patiño, de calidad pueda revisar lo que hay incluido en los proyectos
if (($_SESSION["sesUnidadUsuario"] == 14384) OR ($_SESSION["sesUnidadUsuario"] == 13587)) {
	$cmSql="select B.*, P.nombre, P.codigo, P.cargo_defecto  ";
	$cmSql=$cmSql." from GestiondeInformacionDigital.dbo.asignaProyectos B, HojaDeTiempo.dbo.Proyectos P ";
	$cmSql=$cmSql." where NOT EXISTS ";
	$cmSql=$cmSql." 	( ";
	$cmSql=$cmSql." 	Select A.id_proyecto ";
	$cmSql=$cmSql." 	from GestiondeInformacionDigital.dbo.asignaProyectos A ";
	$cmSql=$cmSql."	    where A.id_proyecto = B.id_proyecto ";
	$cmSql=$cmSql." 	AND (A.unidadDirector = " . $_SESSION["sesUnidadUsuario"] . " OR A.unidadEncargado = " . $_SESSION["sesUnidadUsuario"] . ") ";
	$cmSql=$cmSql." 	UNION ";
	$cmSql=$cmSql." 	select U.id_proyecto  ";
	$cmSql=$cmSql." 	from GestiondeInformacionDigital.dbo.usuariosProyectos U ";
	$cmSql=$cmSql." 	where U.id_proyecto = B.id_proyecto ";
	$cmSql=$cmSql." 	and U.unidad = " . $_SESSION["sesUnidadUsuario"] ;
	$cmSql=$cmSql." 	) ";
	$cmSql=$cmSql." 	and B.id_proyecto = P.id_proyecto ";
	$cmCursor = mssql_query($cmSql);
?>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Proyectos que no se encuentran a su cargo </td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Proyecto</td>
        <td width="5%">&nbsp;</td>
        <td width="12%">&nbsp;</td>
        <td width="12%">Ver Correo eletr&oacute;nico hist&oacute;rico </td>
      </tr>
   	  <?  while ($cmReg=mssql_fetch_array($cmCursor)) {	  ?>
      <tr class="TxtTabla">
        <td>	
		<?
	$codigo2 = $cmReg[codigo];
	$cargo2  = $cmReg[cargo_defecto];
	//Arma el código del proyecto
	$codProy = $codigo2.$cargo2; 
	?>
	<a href="sesProyectoElegido.php?codigo=<? echo $codProy ?>&nomProy=<? echo $cmReg[nombre]; ?>&cualProy=<? echo $cmReg[id_proyecto]; ?>"><? echo ucwords(strtolower($cmReg[nombre])); ?></a></td>
        <td width="5%">&nbsp;</td>
        <td width="12%"><a href="cargaProyecto.php?cualProy=<? echo $cmReg[id_proyecto] ?>"><img src="imagenes/btnDetalle.jpg" width="169" height="17" border="0"></a></td>
        <td width="12%" align="center"><a href="cargaProyectoMail.php?cualProy=<? echo $cmReg[id_proyecto] ?>"><img src="images/icoCorreoE.gif" alt="Consultar Correo electr&oacute;nico del proyecto" width="20" height="16" border="0"></a></td>
      </tr>
	  <? } ?>
</table>
		</td>
      </tr>
    </table>
    
<? } // cierra if?>
<br>

<?
//17Mar20110
//Para que Enrique angulo (5037) y Alvaro Castro (10636) vean TODOS los proyectos de interventoría
if (($_SESSION["sesUnidadUsuario"] == 5037) OR ($_SESSION["sesUnidadUsuario"] == 10636)) {
	$inSql="Select A.* , T.nomTipoProy, P.nombre, P.codigo, P.cargo_defecto 
		from GestiondeInformacionDigital.dbo.asignaProyectos A, 
		GestiondeInformacionDigital.dbo.TiposProyectos T,  
		HojaDeTiempo.dbo.Proyectos P 
		where A.codTipoProy = T.codTipoProy 
		and A.id_proyecto = P.id_proyecto 
		and A.codTipoProy = 1
		";
	$inCursor = mssql_query($inSql);
?>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Proyectos de interventor&iacute;a </td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Proyecto</td>
        <td width="5%">&nbsp;</td>
        <td width="12%">&nbsp;</td>
        <td width="12%">Ver Correo eletr&oacute;nico hist&oacute;rico </td>
      </tr>
   	  <?  while ($inReg=mssql_fetch_array($inCursor)) {	  ?>
      <tr class="TxtTabla">
        <td>	
		<?
	$codigo2 = $inReg[codigo];
	$cargo2  = $inReg[cargo_defecto];
	//Arma el código del proyecto
	$codProy = $codigo2.$cargo2; 
	?>
	<a href="sesProyectoElegido.php?codigo=<? echo $codProy ?>&nomProy=<? echo $inReg[nombre]; ?>&cualProy=<? echo $inReg[id_proyecto]; ?>"><? echo ucwords(strtolower($inReg[nombre])); ?></a></td>
        <td width="5%">&nbsp;</td>
        <td width="12%"><a href="cargaProyecto.php?cualProy=<? echo $inReg[id_proyecto] ?>"><img src="imagenes/btnDetalle.jpg" width="169" height="17" border="0"></a></td>
        <td width="12%" align="center"><a href="cargaProyectoMail.php?cualProy=<? echo $inReg[id_proyecto] ?>"><img src="images/icoCorreoE.gif" alt="Consultar Correo electr&oacute;nico del proyecto" width="20" height="16" border="0"></a></td>
      </tr>
	  <? } ?>
</table>
		</td>
      </tr>
    </table>
    
<? } // cierra if?>

</body>
</html>

<? mssql_close ($conexion); ?>	
