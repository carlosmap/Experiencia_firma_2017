<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?php
session_start();
include("../verificaRegistro2.php");
include('../conectaBD.php');
include("../fncEnviaMailPEAR.php");
include('Fun_Adicionales_Solicitudes.php');
//Establecer la conexión a la base de datos
$conexion = conectar();

/*
2010-11-30
Daniel Felipe Rentería Martínez
Edición de Información de Proyectos
*/

/*
Datos de Parámetro del Proyecto
*/
$sql1 = " SELECT A.* FROM HojaDeTiempo.dbo.Proyectos A WHERE A.id_proyecto =  " . $cualProy;
$cursor1 = mssql_query($sql1);
if($reg1 = mssql_fetch_array($cursor1)){
	$codProyecto = $reg1['codigo'];
	$cargoProyecto = $reg1['cargo_defecto'];
	$nombreProyecto = ucwords(strtolower($reg1['nombre']));
}

/*
Usuarios de la Hoja de Tiempo que no son ordenadores
*/
$sql2 = " SELECT A.* FROM HojaDeTiempo.dbo.Usuarios A WHERE unidad NOT IN ( ";
$sql2 = $sql2 . " SELECT A.unidad
FROM HojaDeTiempo.dbo.Usuarios A, GestiondeInformacionDigital.dbo.OrdenadorGasto B
WHERE A.unidad = B.unidadOrdenador AND A.retirado IS NULL ";
$sql2 = $sql2 . " AND B.id_proyecto = " . $cualProy . " ) ";
$sql2 = $sql2 . " AND A.retirado IS NULL ";
$sql2 = $sql2 . " ORDER BY A.apellidos ";
$cursor2 = mssql_query($sql2);


/*
Grabación del Registro
*/
if ($recarga == 2) {
	
	$okGuardar = "Si";
	$textoQuery = "";
	
	$cursorTran1 = mssql_query("BEGIN TRANSACTION");
	if(trim($cursorTran1) == ""){
		$okGuardar = "No";
	}
	
	/*
	Obtiene la Secuencia del Ordenador del Gasto
	*/
	/*
	$sqlIn1a = " SELECT COALESCE(MAX(secuencia), 0) AS elMax FROM GestiondeInformacionDigital.dbo.OrdenadorGasto ";
	$sqlIn1a = $sqlIn1a . " WHERE id_proyecto = " . $elProyecto;
	$cursorIn1a = mssql_query($sqlIn1a);
	if($regIn1a = mssql_fetch_array($cursorIn1a)){
		$secOrdenador = $regIn1a['elMax'] + 1;
	}
	*/
	
	//Obtiene la secuencia de la solicitud de código
	$sqlIn1a="select distinct secuencia ";
	$sqlIn1a=$sqlIn1a." from GestiondeInformacionDigital.dbo.CargosSolCodigo ";
	$sqlIn1a=$sqlIn1a." where id_proyecto = " . $elProyecto;
	$cursorIn1a = mssql_query($sqlIn1a);
	if($regIn1a = mssql_fetch_array($cursorIn1a)){
		$secOrdenador = $regIn1a['secuencia'] ;
	}
	else {
		$secOrdenador = "1" ;
	}
	
	/*
	Elimina el Ordenador del Gasto
	*/
	$sqlIn1 = " INSERT INTO GestiondeInformacionDigital.dbo.OrdenadorGasto ( secuencia, unidadOrdenador, id_proyecto ) ";
	$sqlIn1 = $sqlIn1 . " VALUES ( ";
	$sqlIn1 = $sqlIn1 . " " . $secOrdenador . ", ";
	$sqlIn1 = $sqlIn1 . " " . $ordenador . ", ";
	$sqlIn1 = $sqlIn1 . " " . $elProyecto . " ";
	$sqlIn1 = $sqlIn1 . " ) ";
	$textoQuery = $textoQuery . $sqlIn1 . "; ";
	//echo $sqlIn1 . "<br>";
	$cursorIn1 = mssql_query($sqlIn1);
	if(trim($cursorIn1) == ""){
		$okGuardar = "No";
	}
	
	/*
	Almacena el Log de la Edición
	*/
	$sqlIn3 = " INSERT INTO HojaDeTiempo.dbo.LogEdicionInfoProyectos ( unidad, queryUp, fechaCrea ) ";
	$sqlIn3 = $sqlIn3 . " VALUES ( ";
	$sqlIn3 = $sqlIn3 . " " . $_SESSION["sesUnidadUsuario"] . ", ";
	$sqlIn3 = $sqlIn3 . " '" . ereg_replace("'", "", $textoQuery) . "', ";
	$sqlIn3 = $sqlIn3 . " '" . date("m/d/Y H:i:s") . "' ";
	$sqlIn3 = $sqlIn3 . " ) ";
	//echo $sqlIn3 . "<br>";
	$cursorIn3 = mssql_query($sqlIn3);
	if(trim($cursorIn3) == ""){
		$okGuardar = "No";
	}
	
//---
	$Proy="";
	//CONSULTA EL ID DEL PROYECTO EN EXP FIRMA
	$sql_id_proy_exp="select id_proyecto from GestiondeInformacionDigital.dbo.EFProyectos WHERE id_proyecto_portal=".$elProyecto." ";
	$cursorIn1 = mssql_query($sql_id_proy_exp);
	$datos_id_proy_exp=mssql_fetch_array($cursorIn1);
	$Proy=$datos_id_proy_exp["id_proyecto"];
	
	if($Proy!="")
	{	
		//ALMACENA LOS ORDENADORES
		$sqlIn1 = " INSERT INTO EFOrdenadores_gasto";
		$sqlIn1 = $sqlIn1 . "( id_ordenadores_gasto, id_proyecto, unidad_ordenador, ordenador_actual, usuarioGraba, fechaGraba ) ";
		$sqlIn1 = $sqlIn1 . " values ( (select isnull(MAX(id_ordenadores_gasto),0)+1 id   from EFOrdenadores_gasto WHERE id_proyecto=".$Proy." ), ".$Proy.", ".$ordenador.", 1 ,".$_SESSION["sesUnidadUsuario"].",getdate()  ) ";
		$cursorIn1 = mssql_query($sqlIn1);	
		
//echo $sqlIn1." *** ".mssql_get_last_message()."<br><br>";		
		
		if(trim($cursorIn1) == ""){
			$okGuardar = "No";
		}	
	}
//---			
	
	//Si los cursores no presentaron problema
	if  (trim($okGuardar) == "Si") {
		$cursorTran1 = mssql_query("COMMIT TRANSACTION");
		
		//Ordenador del Gasto
		$sqlOr = " SELECT * FROM HojaDeTiempo.dbo.Usuarios WHERE unidad = " . $ordenador;
		$cursorOr = mssql_query($sqlOr);
		if($regOr = mssql_fetch_array($cursorOr)){
			$ordenadorGasto = $regOr['unidad'] . " - " . ucwords(strtolower($regOr['apellidos'] . " " . $regOr['nombre']));
		}
		
		/*
		Envía los correos al director, al coordinador, a los ordenadores del gasto, a los programadores
		y a todo ese reguero de gente que está apuntada en la lista
		*/

		$elAsunto = "Portal Ingetec - Modificación Ordenadores de Gasto de Proyectos";
		$elCuerpo = "Se ha asignado un Ordenador de Gasto para el Proyecto <strong>" . $nombreProyecto . " - [" . $codProyecto . "." . $cargoProyecto . "]</strong>";
		$elCuerpo = $elCuerpo . "<br><br>";
		$elCuerpo = $elCuerpo . "Ordenador de Gasto Asignado : <strong>" . $ordenadorGasto . "</strong><br>";
		$laFirma = "Portal Ingetec - Contratos";
		
		//Al Director y al coordinador de Proyecto
		$sqlDir = " SELECT email FROM HojaDeTiempo.dbo.Usuarios WHERE unidad IN ( " . $directorProyecto . ", " . $coordProyecto . " ) ";
		$cursorDir = mssql_query($sqlDir);
		while($regDir = mssql_fetch_array($cursorDir)){
			$aQuien = $regDir['email'] . "@ingetec.com.co";
			enviarCorreo($aQuien, $elAsunto, $elCuerpo, $laFirma);
		}
		
		//A los Ordenadores del Gasto
		$sqlOrd = " SELECT A.email
		FROM HojaDeTiempo.dbo.Usuarios A, GestiondeInformacionDigital.dbo.OrdenadorGasto B
		WHERE A.unidad = B.unidadOrdenador
		AND A.retirado IS NULL ";
		$sqlOrd = $sqlOrd . " AND B.id_proyecto = " . $elProyecto;
		$cursorOrd = mssql_query($sqlOrd);
		while($regOrd = mssql_fetch_array($cursorOrd)){
			$aQuien = $regOrd['email'] . "@ingetec.com.co";
			enviarCorreo($aQuien, $elAsunto, $elCuerpo, $laFirma); 
		}
		
		//A los programadores
		$sqlProg = " SELECT A.email
		FROM HojaDeTiempo.dbo.Usuarios A, HojaDeTiempo.dbo.Programadores B
		WHERE A.unidad = B.unidad
		AND A.retirado IS NULL ";
		$sqlProg = $sqlProg . " AND B.id_proyecto = " . $elProyecto;
		$cursorProg = mssql_query($sqlProg);
		while($regProg = mssql_fetch_array($cursorProg)){
			$aQuien = $regProg['email'] . "@ingetec.com.co";
			enviarCorreo($aQuien, $elAsunto, $elCuerpo, $laFirma); 
		}
		
		//Al reguero de gente
		$sqlNotif = " SELECT A.email
		FROM HojaDeTiempo.dbo.Usuarios A, GestiondeInformacionDigital.dbo.AutorizadosSolCodigo B
		WHERE A.unidad = B.unidad
		AND A.retirado IS NULL ";	
		$cursorNotif = mssql_query($sqlNotif);
		while($regNotif = mssql_fetch_array($cursorNotif)){
			$aQuien = $regNotif['email'] . "@ingetec.com.co";
			enviarCorreo($aQuien, $elAsunto, $elCuerpo, $laFirma);
		}

		
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		$cursorTran1 = mssql_query("ROLLBACK TRANSACTION");
		echo ("<script>alert('Error durante la grabación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('solListaProyectos.php','winSolCod','toolbar=yes,scrollbars=yes,resizable=yes,width=700,height=400');</script>");	

}

?>
<html>
<head>
<script language="JavaScript" type="text/JavaScript">
<!--
<!--

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
  } if (errors) alert('The following error(s) occurred:\n'+errors);
  document.MM_returnValue = (errors == '');
}
//-->

function envia1(){
	document.form1.recarga.value = 1;
	document.form1.submit();
}

function envia2(){
	var error = 'n';
	var mensaje = 'Validación:\n';
	
	if(document.form1.ordenador.selectedIndex == 0){
		error = 's';
		mensaje = mensaje + 'Debe seleccionar un usuario ordenador del gasto';
	}
	
	if(error == 's'){
		alert(mensaje);
	} else {
		if(confirm('Desea guardar esta información para el proyecto?')){
			document.form1.recarga.value = 2;
			document.form1.submit();
		}
	}
	
}

function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>


<title>Proyectos</title>
<LINK REL="stylesheet" HREF="../css/estilo.css" TYPE="text/css">

</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Proyectos - Editar Informaci&oacute;n </td>
  </tr>
</table>
<table width="100%" cellspacing="1" class="fondo">
<form name="form1" method="post" action="">
	<tr>
		<td width="25%" class="TituloTabla">C&oacute;digo - Cargo del Proyecto </td>
	    <td class="TxtTabla"><? //echo $codProyecto . "." . $cargoProyecto;
		echo codigo_subCodigo($cualProy);
		 ?>
        <input name="codProyecto" type="hidden" id="codProyecto" value="<? echo $codProyecto; ?>">
        <input name="cargoProyecto" type="hidden" id="cargoProyecto" value="<? echo $cargoProyecto; ?>"></td>
	</tr>
	<tr>
	  <td class="TituloTabla">Nombre del Proyecto </td>
	  <td class="TxtTabla"><? echo $nombreProyecto; ?></td>
  </tr>
	<tr>
	  <td class="TituloTabla">Ordenador del Gasto </td>
	  <td class="TxtTabla"><select name="ordenador" class="CajaTexto" id="ordenador">
	  <option value="0">::: Seleccione un Ordenador de Gasto :::</option>
	  <? while($reg2 = mssql_fetch_array($cursor2)){ ?>
	  <option value="<? echo $reg2['unidad']; ?>"><? echo ucwords(strtolower($reg2['apellidos'] . " " . $reg2['nombre'])); ?></option>
	  <? } ?>
      </select></td>
	</tr>
	<tr align="right">
	  <td colspan="2" class="TxtTabla">
		  <input name="elProyecto" type="hidden" id="elProyecto" value="<? echo $cualProy; ?>">
		  <input name="recarga" type="hidden" id="recarga" value="1">
		  <input name="Submit" type="button" class="Boton" value="Grabar" onClick="envia2();">
		  <input name="Submit2" type="button" class="Boton" onClick="MM_callJS('window.close();')" value="Cancelar">
	  </td>
  </tr>
</form>
</table>

 

</body>
</html>

<? mssql_close ($conexion); ?>	