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
Ordenador del Gasto Seleccionado
*/
$sqlOG = " SELECT A.*
FROM HojaDeTiempo.dbo.Usuarios A, GestiondeInformacionDigital.dbo.OrdenadorGasto B
WHERE A.unidad = B.unidadOrdenador
AND A.retirado IS NULL ";
$sqlOG = $sqlOG . " AND B.id_proyecto = " . $cualProy;
$sqlOG = $sqlOG . " AND A.unidad = " . $cualOrd;
$cursorOG = mssql_query($sqlOG);
if($regOG = mssql_fetch_array($cursorOG)){
	$ordenadorGasto = $regOG['unidad'] . " - " . ucwords(strtolower($regOG['nombre'] . " " . $regOG['apellidos']));
}


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
	Elimina el Ordenador del Gasto
	*/
	$sqlIn1 = " DELETE FROM GestiondeInformacionDigital.dbo.OrdenadorGasto ";
	$sqlIn1 = $sqlIn1 . " WHERE id_proyecto = " . $elProyecto . " ";
	$sqlIn1 = $sqlIn1 . " AND unidadOrdenador = " . $elOrdenador . " ";
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
	
	$Proy="";
	//CONSULTA EL ID DEL PROYECTO EN EXP FIRMA
	$sql_id_proy_exp="select id_proyecto from GestiondeInformacionDigital.dbo.EFProyectos WHERE id_proyecto_portal=".$elProyecto." ";
	$cursorIn1 = mssql_query($sql_id_proy_exp);
	$datos_id_proy_exp=mssql_fetch_array($cursorIn1);
	$Proy=$datos_id_proy_exp["id_proyecto"];
	
	if($Proy!="")
	{
		//ACTUALIZA LOS ORDENADORES
		$sqlIn1 = " UPDATE EFOrdenadores_gasto SET ordenador_actual=0 where id_proyecto=".$Proy." AND unidad_ordenador=" . $elOrdenador . "  and ordenador_actual=1 ";
		$cursorIn1 = mssql_query($sqlIn1);	

//echo $sqlIn1." *** ".mssql_get_last_message()."<br><br>";		
		if(trim($cursorIn1) == ""){
			$okGuardar = "No";
		}	
						
	}
	
	//Si los cursores no presentaron problema
	if  (trim($okGuardar) == "Si") {
		$cursorTran1 = mssql_query("COMMIT TRANSACTION");
		
		/*
		Envía los correos al director, al coordinador, a los ordenadores del gasto, a los programadores
		y a todo ese reguero de gente que está apuntada en la lista
		*/
		
		$elAsunto = "Portal Ingetec - Modificación Ordenadores de Gasto de Proyectos";
		$elCuerpo = "Se ha eliminado un Ordenador de Gasto para el Proyecto <strong>" . $nombreProyecto . " - [" . $codProyecto . "." . $cargoProyecto . "]</strong>";
		$elCuerpo = $elCuerpo . "<br><br>";
		$elCuerpo = $elCuerpo . "Ordenador de Gasto Retirado : <strong>" . $ordenadorGasto . "</strong><br>";
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

		echo ("<script>alert('La operación se realizó con éxito.');</script>");
	} 
	else {
		$cursorTran1 = mssql_query("ROLLBACK TRANSACTION");
		echo ("<script>alert('Error durante la operación');</script>");
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
	  <td class="TxtTabla"><? echo $ordenadorGasto; ?></td>
	</tr>
	<tr align="center">
	  <td colspan="2" class="TxtTabla"><strong>&iquest;Est&aacute; seguro que desea eliminar este Ordenador del Gasto?</strong></td>
    </tr>
	<tr align="right">
	  <td colspan="2" class="TxtTabla">
		  <input name="elOrdenador" type="hidden" id="elOrdenador" value="<? echo $cualOrd; ?>">
		  <input name="elProyecto" type="hidden" id="elProyecto" value="<? echo $cualProy; ?>">
		  <input name="recarga" type="hidden" id="recarga" value="2">
		  <input name="Submit" type="submit" class="Boton" value="Eliminar">
		  <input name="Submit2" type="button" class="Boton" onClick="MM_callJS('window.close();')" value="Cancelar">
	  </td>
  </tr>
</form>
</table>

 

</body>
</html>

<? mssql_close ($conexion); ?>	