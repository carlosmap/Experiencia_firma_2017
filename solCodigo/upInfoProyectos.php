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


//Establecer la conexión a la base de datos
$conexion = conectar();
//$conexionMySql = conectarMySql();	

/*
2010-11-30
Daniel Felipe Rentería Martínez
Edición de Información de Proyectos
*/

/*
Funcion Caracteres Especiales
*/
function cambiarCaracteresEspeciales($cadenaACambiar){
	
	//Arreglos de Vocales y Ñ que deben ser reemplazadas
	$aMin = array("á", "à", "ä");
	$aMay = array("Á", "À", "Á");
	$eMin = array("é", "è", "ë");
	$eMay = array("É", "È", "Ë");
	$iMin = array("í", "ì", "ï");
	$iMay = array("Í", "Ì", "Ï");
	$oMin = array("ó", "ò", "ö");
	$oMay = array("Ó", "Ò", "Ö");
	$uMin = array("ú", "ù", "ü");
	$uMay = array("Ú", "Ù", "	Ü");
	
	$cadenaACambiar = str_replace($aMin, "a", $cadenaACambiar);
	$cadenaACambiar = str_replace($aMay, "A", $cadenaACambiar);
	$cadenaACambiar = str_replace($eMin, "e", $cadenaACambiar);
	$cadenaACambiar = str_replace($eMay, "E", $cadenaACambiar);
	$cadenaACambiar = str_replace($iMin, "i", $cadenaACambiar);
	$cadenaACambiar = str_replace($iMay, "I", $cadenaACambiar);
	$cadenaACambiar = str_replace($oMin, "o", $cadenaACambiar);
	$cadenaACambiar = str_replace($oMay, "O", $cadenaACambiar);
	$cadenaACambiar = str_replace($uMin, "u", $cadenaACambiar);
	$cadenaACambiar = str_replace($uMay, "U", $cadenaACambiar);
	$cadenaACambiar = str_replace("ñ", "n", $cadenaACambiar);
	$cadenaACambiar = str_replace("Ñ", "N", $cadenaACambiar);
	$cadenaACambiar = str_replace(",", " ", $cadenaACambiar);
	
	return $cadenaACambiar;
}


/*
Datos de Parámetro del Proyecto
*/
$sql1 = " SELECT A.* FROM HojaDeTiempo.dbo.Proyectos A WHERE A.id_proyecto = " . $cualProy;
//echo $sql1;
$cursor1 = mssql_query($sql1);
if(isset($recarga) == false){
	if($reg1 = mssql_fetch_array($cursor1)){
		$codProyecto = $reg1['codigo'];
		$cargoProyecto = $reg1['cargo_defecto'];
		$nombreProyecto = $reg1['nombre'];
		$directorProyecto = $reg1['id_director'];
		$coordProyecto = $reg1['id_coordinador'];
		$empresaProyecto = $reg1['idEmpresa'];
	}
}

/*
Verifica Que el proyecto tenga una solicitud de código asociada.
Si la tiene, es posible cambiar la división.
*/
$sql1div = " SELECT A.*, B.id_division
FROM GestiondeInformacionDigital.dbo.CargosSolCodigo A, 
GestiondeInformacionDigital.dbo.SolicitudCodigo B
WHERE A.secuencia = B.secuencia
AND id_proyecto = " . $cualProy;
$cursor1div = mssql_query($sql1div);
if(isset($recarga) == false){
	if($reg1Div = mssql_fetch_array($cursor1div)){
		$secSolicitud = $reg1Div['secuencia'];
		$divProyecto = $reg1Div['id_division'];
	} else {
		$secSolicitud = "";
	}
}

/*
Usuarios de la Hoja de Tiempo
*/
$sql2 = " SELECT * FROM HojaDeTiempo.dbo.Usuarios 
WHERE retirado IS NULL 
ORDER BY apellidos ";
$cursor2a = mssql_query($sql2);
$cursor2b = mssql_query($sql2);

/*
Empresas
*/
$sql3 = " SELECT * FROM HojaDeTiempo.dbo.Empresas ";
$cursor3 = mssql_query($sql3);

/*
Divisiones
*/
$sql4 = " SELECT * FROM HojaDeTiempo.dbo.Divisiones
WHERE estadoDiv = 'A'
ORDER BY nombre ";
$cursor4 = mssql_query($sql4);


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
	Actualiza la información del proyecto
	*/
	$sqlIn1 = " UPDATE HojaDeTiempo.dbo.Proyectos SET ";
	$sqlIn1 = $sqlIn1 . " nombre = '" . $nombreProyecto . "', ";
	$sqlIn1 = $sqlIn1 . " id_director = " . $directorProyecto . ", ";
	if($coordProyecto != 0){
		$sqlIn1 = $sqlIn1 . " id_coordinador = " . $coordProyecto . ", ";
	} else {
		$sqlIn1 = $sqlIn1 . " id_coordinador = NULL, ";
	}
	$sqlIn1 = $sqlIn1 . " idEmpresa = " . $empresaProyecto . " ";
	$sqlIn1 = $sqlIn1 . " WHERE id_proyecto = " . $elProyecto . " ";
	$sqlIn1 = $sqlIn1 . " AND codigo = '" . $codProyecto . "' ";
	$sqlIn1 = $sqlIn1 . " AND cargo_defecto = '" . $cargoProyecto . "' ";
	$textoQuery = $textoQuery . $sqlIn1 . "; ";
	//echo $sqlIn1 . "<br>";
	$cursorIn1 = mssql_query($sqlIn1);
	if(trim($cursorIn1) == ""){
		$okGuardar = "No";
	}

//-----
	$Proy="";
	//CONSULTA EL ID DEL PROYECTO EN EXP FIRMA
	$sql_id_proy_exp="select id_proyecto from GestiondeInformacionDigital.dbo.EFProyectos WHERE id_proyecto_portal=".$elProyecto." ";
	$cursorIn1 = mssql_query($sql_id_proy_exp);
	$datos_id_proy_exp=mssql_fetch_array($cursorIn1);
	$Proy=$datos_id_proy_exp["id_proyecto"];
	
//echo $sql_id_proy_exp." *** ".mssql_get_last_message()."<br><br>";
	if($Proy!="")
	{
		if($directorProyecto!="")
		{
			///ACTUALIZA EL REGISTRO DEL DIRECTOR ACTUAL
			$sqlIn1 = " UPDATE GestiondeInformacionDigital.dbo.EFDirector_Coordinador SET director_coordinador_actual=0 where id_proyecto=".$Proy." and tipo=1 ";
			$cursorIn1 = mssql_query($sqlIn1);	
			if(trim($cursorIn1) == ""){
				$okGuardar = "No";
			}			

//echo $sqlIn1." *** ".mssql_get_last_message()."<br><br>";								
			if($cursorIn1!="")
			{			
				//ALMACENA EL NUEVO DIRECTOR
				$sqlIn1 = " INSERT INTO GestiondeInformacionDigital.dbo.EFDirector_Coordinador";
				$sqlIn1 = $sqlIn1 . "( id_director_coordinador, id_proyecto, unidad_director_coordinador, tipo, director_coordinador_actual, usuarioGraba, fechaGraba ) ";
				$sqlIn1 = $sqlIn1 . " values ( (select isnull(MAX(id_director_coordinador),0)+1 id_proye  from EFDirector_Coordinador WHERE id_proyecto=".$Proy."), ".$Proy.", ".$directorProyecto.", 1, 1 ,".$_SESSION["sesUnidadUsuario"].",getdate()  ) ";
				$cursorIn1 = mssql_query($sqlIn1);								

				if(trim($cursorIn1) == ""){
					$okGuardar = "No";
				}		
//echo $sqlIn1." *** ".mssql_get_last_message()."<br><br>";						
			}
		}
	
//	echo $sqlIn1."<br>".mssql_get_last_message()."<br>";			
		if($cursorIn1!="")
		{	

			if($coordProyecto != 0)
			{
				///ACTUALIZA EL REGISTRO DEL CORRDINADOR AIGNADO ANTERIORMENTE A INACTIVO
				$sqlIn1 = " UPDATE GestiondeInformacionDigital.dbo.EFDirector_Coordinador SET director_coordinador_actual=0 where id_proyecto=".$Proy." and tipo=2 ";
				$cursorIn1 = mssql_query($sqlIn1);	

//echo $sqlIn1." *** ".mssql_get_last_message()."<br><br>";
				if(trim($cursorIn1) == ""){
					$okGuardar = "No";
				}		
									
				if($cursorIn1!="")
				{		
//echo $Coordinador." ----------------------- ";									
					//ALMACENA EL NUEVO CORRDINADOR
					$sqlIn1 = " INSERT INTO GestiondeInformacionDigital.dbo.EFDirector_Coordinador";
					$sqlIn1 = $sqlIn1 . "( id_director_coordinador,id_proyecto  , unidad_director_coordinador, tipo, director_coordinador_actual, usuarioGraba, fechaGraba ) ";
					$sqlIn1 = $sqlIn1 . " values ( (select isnull(MAX(id_director_coordinador),0)+1 id_proye  from EFDirector_Coordinador WHERE id_proyecto=".$Proy."), ".$Proy.", ".$coordProyecto.", 2, 1 ,".$_SESSION["sesUnidadUsuario"].",getdate()  ) ";
					$cursorIn1 = mssql_query($sqlIn1);	
					
//echo $sqlIn1." *** ".mssql_get_last_message()."<br><br>";
					if(trim($cursorIn1) == ""){
						$okGuardar = "No";
					}							
				}
			}
		}
	}
//-----
	
	/*
	Actualiza la información de la división
	*/
	if(isset($divProyecto)){		
		$sqlIn2 = " UPDATE GestiondeInformacionDigital.dbo.SolicitudCodigo SET ";
		if($divProyecto != 0){
			$sqlIn2 = $sqlIn2 . " id_division = " . $divProyecto . " ";
		} else {
			$sqlIn2 = $sqlIn2 . " id_division = NULL ";
		}
		$sqlIn2 = $sqlIn2 . " WHERE secuencia = " . $secSolicitud . " ";
		$textoQuery = $textoQuery . $sqlIn2 . "; ";
		//echo $sqlIn2 . "<br>";
		$cursorIn2 = mssql_query($sqlIn2);
		if(trim($cursorIn2) == ""){
			$okGuardar = "No";
		}
	}
	
	//exit;
	
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
	
	//Si los cursores no presentaron problema
	if  (trim($okGuardar) == "Si") {
		
		$cursorTran1 = mssql_query("COMMIT TRANSACTION");
		
		//Cambia el nombre del Proyecto en Page Device, si el nombre del Proyecto Cambió
		if($nombreAntiguo != $nombreProyecto){
			
			$sql1a = " SELECT * FROM (
				SELECT A.id_proyecto, A.nombre, A.codigo, A.cargo_defecto AS cargo, COALESCE(A.descCargoDefecto, '') AS descripcion, '0' AS esAdicional
				FROM HojaDeTiempo.dbo.Proyectos A
				where A.id_estado = 2
				and A.id_proyecto not in (
					SELECT id_proyecto FROM HojaDeTiempo.dbo.Proyectos
					where especial = 1
					and LEN(codigo) > 2
				)
				UNION
				select A.id_proyecto, A.nombre, A.codigo, B.cargos_adicionales AS cargo, COALESCE(B.descripcion, '') AS descripcion, '1' AS esAdicional
				from HojaDeTiempo.dbo.Proyectos A, HojaDeTiempo.dbo.Cargos B
				where A.id_proyecto = B.id_proyecto
				and A.id_estado = 2
				and A.id_proyecto not in (
					SELECT id_proyecto FROM HojaDeTiempo.dbo.Proyectos
					where especial = 1
					and LEN(codigo) > 2
				) 
			) X ";
			$sql1a = $sql1a . " WHERE X.id_proyecto = " . $elProyecto;
			$sql1a = $sql1a . " ORDER BY X.id_proyecto ";
			$cursor1a = mssql_query($sql1a);
			
			$okGuardarMy = "Si";
			
			/*
			while($reg1a = mssql_fetch_array($cursor1a)){
				//Efectúa el Update de la tabla pc_bilcode
				
				$bil_code = $reg1a['codigo'] . "." . $reg1a['cargo'];
				
				$client = "[" . $reg1a['codigo'] . "." . $reg1a['cargo'] . "] ";
				$client = $client . strtoupper(cambiarCaracteresEspeciales($nombreProyecto));
				$client = $client . " - ";
				$client = $client . strtoupper(cambiarCaracteresEspeciales($reg1a['descripcion']));
				
				$sqlMy2 = " UPDATE pc_bilcode SET client = '" . $client . "' ";
				$sqlMy2 = $sqlMy2 . " WHERE bil_code = '" . $bil_code . "' ";
				$sqlMy2 = $sqlMy2 . " AND ident = '" . $elProyecto . "' ";
				$cursorMy2 = mysql_query($sqlMy2);
				if(trim($cursorMy2) == ""){
					$okGuardarMy = "No";
				}
				
				if(trim($okGuardarMy) == "Si"){
					echo "PageDevice actualizado OK <br>";
				} else {
					echo "Fallo al actualizar PageDevice <br>";
				}
				
			}
			*/
			
		}
		
	
		//Obtiene la Información del nombre del Director, del Coordinador
		$sqlJ1 = " SELECT * FROM HojaDeTiempo.dbo.Usuarios WHERE unidad = " . $directorProyecto . " ";
		$cursorJ1 = mssql_query($sqlJ1);
		if($regJ1 = mssql_fetch_array($cursorJ1)){
			$nomDirProyecto = $regJ1['unidad'] . " - " . ucwords(strtolower($regJ1['nombre'] . " " . $regJ1['apellidos']));
		}
		$sqlJ2 = " SELECT * FROM HojaDeTiempo.dbo.Usuarios WHERE unidad = " . $coordProyecto . " ";
		$cursorJ2 = mssql_query($sqlJ2);
		if($regJ2 = mssql_fetch_array($cursorJ2)){
			$nomCoordProyecto = $regJ2['unidad'] . " - " . ucwords(strtolower($regJ2['nombre'] . " " . $regJ2['apellidos']));
		}
		//Empresa
		$sqlEm = " SELECT * FROM HojaDeTiempo.dbo.Empresas WHERE idEmpresa = " . $empresaProyecto;
		$cursorEm = mssql_query($sqlEm);
		if($regEm = mssql_fetch_array($cursorEm)){
			$nomEmpresa = $regEm['nombre'];
		}
		//División
		if($divProyecto != 0){
			$sqlDiv = " SELECT * FROM HojaDeTiempo.dbo.Divisiones WHERE id_division = " . $divProyecto;
			$cursorDiv = mssql_query($sqlDiv);
			if($regDiv = mssql_fetch_array($cursorDiv)){
				$nomDivision = ucwords(strtolower($regDiv['nombre']));
			}
		} else {
			$nomDivision = "(Sin división asignada)";
		}
		
		/*
		Envía los correos al director, al coordinador, a los ordenadores del gasto, a los programadores
		y a todo ese reguero de gente que está apuntada en la lista
		*/

		$elAsunto = "Portal Ingetec - Modificación de Información de Proyectos";
		$elCuerpo = "Se ha efectuado una modificaci&oacute;n de informaci&oacute;n en el Proyecto <strong>" . $nombreProyecto . " - [" . $codProyecto . "." . $cargoProyecto . "]</strong>";
		$elCuerpo = $elCuerpo . "<br><br>";
		$elCuerpo = $elCuerpo . "Director de Proyecto: <strong>" . $nomDirProyecto . "</strong><br>";
		$elCuerpo = $elCuerpo . "Coordinador de Proyecto: <strong>" . $nomCoordProyecto . "</strong><br>";
		$elCuerpo = $elCuerpo . "Empresa: <strong>" . $nomEmpresa . "</strong><br>";
		$elCuerpo = $elCuerpo . "División: <strong>" . $nomDivision . "</strong><br>";
		$laFirma = "Intranet INGETEC - Contratos";
		
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
	
	if(document.form1.nombreProyecto.value == ''){
		error = 's';
		mensaje = mensaje + 'El nombre del proyecto es obligatorio\n';
	}
	
	if(document.form1.directorProyecto.selectedIndex == 0){
		error = 's';
		mensaje = mensaje + 'Debe seleccionar un director de proyecto\n';
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
	    <td class="TxtTabla"><? echo $codProyecto . "." . $cargoProyecto; ?>
        <input name="codProyecto" type="hidden" id="codProyecto" value="<? echo $codProyecto; ?>">
        <input name="cargoProyecto" type="hidden" id="cargoProyecto" value="<? echo $cargoProyecto; ?>"></td>
	</tr>
	<tr>
	  <td class="TituloTabla">Nombre del Proyecto </td>
	  <td class="TxtTabla"><input name="nombreProyecto" type="text" class="CajaTexto" id="nombreProyecto" value="<? echo $nombreProyecto; ?>" size="60">
      <input name="nombreAntiguo" type="hidden" id="nombreAntiguo" value="<? echo $nombreProyecto; ?>"></td>
  </tr>
	<tr>
	  <td class="TituloTabla">Director</td>
	  <td class="TxtTabla"><select name="directorProyecto" class="CajaTexto" id="directorProyecto">
	  <option value="0">::: Seleccione un Director :::</option>
	  <? 
	  while($reg2a = mssql_fetch_array($cursor2a)){ 
	  	$optDir = "";
		if($reg2a['unidad'] == $directorProyecto){
			$optDir = "selected";
		}
	  ?>
	  	<option value="<? echo $reg2a['unidad']; ?>" <? echo $optDir; ?>><? echo ucwords(strtolower($reg2a['apellidos'] . " " . $reg2a['nombre'])); ?></option>
	  <? } ?>
      </select></td>
  </tr>
	<tr>
	  <td class="TituloTabla">Coordinador</td>
	  <td class="TxtTabla"><select name="coordProyecto" class="CajaTexto" id="coordProyecto">
	  <option value="0">::: Seleccione un Coordinador :::</option>
	  <? 
	  while($reg2b = mssql_fetch_array($cursor2b)){ 
	  	$optCoord = "";
		if($reg2b['unidad'] == $coordProyecto){
			$optCoord = "selected";
		}
	  ?>
	  <option value="<? echo $reg2b['unidad']; ?>" <? echo $optCoord; ?>><? echo ucwords(strtolower($reg2b['apellidos'] . " " . $reg2b['nombre'])); ?></option>
	  <? } ?>
      </select></td>
  </tr>
	<tr>
	  <td class="TituloTabla">Empresa</td>
	  <td class="TxtTabla"><select name="empresaProyecto" class="CajaTexto" id="empresaProyecto">
	  <? 
	  while($reg3 = mssql_fetch_array($cursor3)){ 
	  	$optEmp= "";
		if($reg3['idEmpresa'] == $empresaProyecto){
			$optEmp= "selected";
		}
	  ?>
	  <option value="<? echo $reg3['idEmpresa']; ?>" <? echo $optEmp; ?>><? echo $reg3['nombre']; ?></option>
	  <? } ?>
      </select></td>
  </tr>
	<? if(mssql_num_rows($cursor1div) != 0){ ?>
	<tr>
	  <td class="TituloTabla">Divisi&oacute;n</td>
	  <td class="TxtTabla"><select name="divProyecto" class="CajaTexto" id="divProyecto">
	  <option value="0">::: Seleccione una divisi&oacute;n :::</option>
	  <? 
	  while($reg4 = mssql_fetch_array($cursor4)){ 
	  	$optDiv = "";
		if($reg4['id_division'] == $divProyecto){
			$optDiv = "selected";
		}
	  ?>
	  <option value="<? echo $reg4['id_division']; ?>" <? echo $optDiv; ?>><? echo ucwords(strtolower($reg4['nombre'])); ?></option>
	  <? } ?>
      </select></td>
  </tr>
  <? } // Fin del If de $secSolicitud?>
	<tr align="right">
	  <td colspan="2" class="TxtTabla">
		  <input name="secSolicitud" type="hidden" id="secSolicitud" value="<? echo $secSolicitud; ?>">
		  <input name="elProyecto" type="hidden" id="elProyecto" value="<? echo $cualProy; ?>">
		  <input name="recarga" type="hidden" id="recarga" value="1">
		  <input name="Submit" type="button" class="Boton" onClick="envia2();" value="Guardar">
	  </td>
  </tr>
</form>
</table>

 

</body>
</html>

<? 
mssql_close ($conexion); 
mysql_close ($conexionMySql);
?>	