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

?>

<?
//Establecer la conexión a la base de datos
$conexion = conectar();




//Seleccionar el registro de SolicitudCodigo
//Seleccionar los registros de SolicitudCodigo
$sql="select S.*, T.nomTipoProy ";
$sql= $sql. " from SolicitudCodigo S, tiposProyectos T ";
$sql= $sql. " where S.codTipoProy = T.codTipoProy ";
$sql= $sql. " and S.secuencia = " . $cualSec;
$cursor = mssql_query($sql);

//Seleccionar los registros relacionados al ordenaro del gasto
$sql3="select O.*, U.nombre, u.apellidos, u.email  ";
$sql3= $sql3. " from GestiondeInformacionDigital.dbo.OrdenadorGasto O,  ";
$sql3= $sql3. " HojaDeTiempo.dbo.USUARIOs U  ";
$sql3= $sql3. " where O.unidadOrdenador = U.unidad  ";
$sql3= $sql3. " and O.secuencia =" . $cualSec;
$cursor3 = mssql_query($sql3);

//Para validar que el detalle de la solicitud tenga por lo menos 1 ordenador del gasto
$numFilas = 0;
$numFilas = mssql_num_rows($cursor3);

//Seleccionar los registros relacionados al cargo
$sql4="Select * from CargosSolCodigo ";
$sql4= $sql4. " where secuencia =" . $cualSec;
$cursor4 = mssql_query($sql4);
//Para verificar qqué boton mostrar en Tramitar Autorización (Ingreso o modificación)
$numCargos = 0;
$numCargos = mssql_num_rows($cursor4);

//Seleccionar las personas  que aparecen para relacionar copias
$sql5="select A.* , U.nombre, u.apellidos, u.email ";
$sql5=$sql5." from GestiondeInformacionDigital.dbo.AutorizadosSolCodigo A,  ";
$sql5=$sql5." HojaDeTiempo.dbo.USUARIOs U ";
$sql5=$sql5." where A.unidad = U.unidad ";
$sql5=$sql5." order by u.apellidos ";
$cursor5 = mssql_query($sql5);

//Encontrar el consecutivo generado por el sistema para la respuesta 
//El consecutivo s reinicia anualmente
$sql6="select max(consecutivo) consecutivo ";
$sql6=$sql6." from SolicitudCodigo ";
$sql6=$sql6." where year(getdate()) = " .date ("Y");
$cursor6 = mssql_query($sql6);
if ($reg6=mssql_fetch_array($cursor6)) {
	$MaxCE = $reg6[consecutivo] + 1;
	}
else {
	$MaxCE = 1;
}

//Verifica si hizo submit
if ($pSolicitudNo != "") {

///////////////////////funcion de insercion de codigo de proyecto en seven y confact



//mssql_query("BEGIN TRANSACTION");
	//Verifica si aprobó o no la solicitud.
	//Si No aprueba sólo atualiza la tabla
	//Si Si aprueba entonces graba en la tabla y envia los correos electrónicos
	if ($pCompleto == "0") {
		//Realiza la actualización de la confirmación del codigo
		$query = "UPDATE  SolicitudCodigo SET "; 
		$query = $query . " validaContratos = '" . $pCompleto . "',  ";	
		$query = $query . " comentaContratos = '" . $pObserva . "'  ";	
		$query = $query . " WHERE secuencia = " . $pSolicitudNo ;
	//	echo $query ;
		$cursor = mssql_query($query) ;
		//Si los cursores no presentaron problema
		if  (trim($cursor) != "")   {
			echo ("<script>alert('La Actualización se realizó con éxito.');</script>");
		} 
		else {
			echo ("<script>alert('Error durante la grabación');</script>");
		};
		
	}
	else {
		$queryLogMy= "" ;
		$queryLog="";
	
		//Realiza la actualización de la confirmación del codigo
		$query = "UPDATE  SolicitudCodigo SET "; 
		$query = $query . " consecutivo = '" . $pConsecutivo . "',  ";	
		$query = $query . " fechaValidaContratos = '" . $pFecha . "',  ";	
		$query = $query . " validaContratos = '" . $pCompleto . "',  ";	
		$query = $query . " comentaContratos = '" . $pObserva . "'  ";	
		$query = $query . " WHERE secuencia = " . $pSolicitudNo ;
	//	echo $query ;
		$cursor = mssql_query($query) ;

		//***EnviarMailPEAR
		include("../fncEnviaMailPEAR.php");
		
		//05Feb2014
		//PBM
		//Envía Mail a encargados de impresión
		//El ultimo dígito 7 corresponde al cargo de impresión.
		$pAsunto = "Intranet Ingetec - Asignación de código NUEVO PROYECTO ";
//		$pTema = "Se informa que se realizó la asignación de código para/: <b>" . $cualNombre . "[". trim($pSevenEmpresa) .  trim($pIdenEstadoCP) . trim($pCodigoDef) . "." . trim($pCargoDef) . "7]" . "</b>";
//		$pTema = "Se informa que se realizó la asignación de código para/: <b>" . $cualNombre . "[" .  trim($pCodigoDef) . " " . trim($pCargoDef) . " 7]" . "</b>";
		$pTema = "Se informa que se realizó la asignación de código para: <b>" . $cualNombre . " [" .  trim($pCodigoDef) . " " . trim($pCargoDef) . "]" . "</b>";
		$pTema = $pTema . "<br><br>";
		$pTema = $pTema . "Cliente: " . $cualCliente ;
		$pTema = $pTema . "<br><br>";
		$pFirma = "Departamento Contratos - Sistemas ";
//		$pPara= "hector.buitrago@co.sonda.com";


//		$pPara= "lizeth.canas@sonda.com";
//		enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
//		$pPara= "jarodriguez@co.sonda.com";
//		$pPara= "robinsonquintero@ingetec.com.co";
//		enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);

		$pPara= "impresionxerox@ingetec.com.co";
		enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);

		$pPara= "pbaron@ingetec.com.co";
		enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
		//Cierra Mail de impresión 05Feb2014
		
		//12-Jun-2012
		//PBM
		//Solicitud de Liliana Patiño y María Antonieta Suarez
		//Se implementa nota requerida por SGC para que se solicite el personal necesario para la licitación y poder comprobarlo.
		//La nota sólo aplica para Propuestas.

		//07Jun2016
		//PBM
		//Se bloquea el siguiente código por instrucción de GRM - No discriminar nada - Según GRM Todo es la misma vaina
		/*
		if (trim($vMiTipoProy) != '3' ) {
			$pAsunto = "Intranet Ingetec - Asignación de código ";
//			$pTema = "Se informa que se realizó la asignación de código para: <b>" . $cualNombre . "[". trim($pCodigoDef) . "." . trim($pCargoDef) . "]" . "</b>";
//			$pTema = "Se informa que se realizó la asignación de código para: <b>" . $cualNombre . "[". trim($pSevenEmpresa) .  trim($pIdenEstadoCP) . trim($pCodigoDef) . "." . trim($pCargoDef) . "]" . "</b>";
			$pTema = "Se informa que se realizó la asignación de código para: <b>" . $cualNombre . "[". trim($pCodigoDef) . " " . trim($pCargoDef) . "]" . "</b>";
			$pTema = $pTema . "<br><br>";
			$pTema = $pTema . "Cliente: " . $cualCliente ;
			$pTema = $pTema . "<br><br>";
			$pTema = $pTema . "No olvide autorizar en el Portal <I>(Proyectos / Programación / icono de impresión)</I> las personas que podrán hacer impresiones en el Proyecto.";
			$pFirma = "Departamento Contratos - Sistemas ";
		}
		else {
			$pAsunto = "Intranet Ingetec - Asignación de código ";
//			$pTema = "Se informa que se realizó la asignación de código para: <b>" . $cualNombre . "[". trim($pCodigoDef) . "." . trim($pCargoDef) . "]" . "</b>";
			$pTema = "Se informa que se realizó la asignación de código para: <b>" . $cualNombre . "[". trim($pSevenEmpresa) .  trim($pIdenEstadoCP) . trim($pCodigoDef) . "." . trim($pCargoDef) . "]" . "</b>";
			$pTema = $pTema . "<br><br>";
			$pTema = $pTema . "Cliente: " . $cualCliente ;
			$pTema = $pTema . "<br><br>";
			$pTema = $pTema . "No olvide autorizar en el Portal <I>(Proyectos / Programación / icono de impresión)</I> las personas que podrán hacer impresiones en el Proyecto";
			$pTema = $pTema . "<br><br>";
			$pTema = $pTema . "IMPORTANTE. Para dar cumplimiento a una oportunidad de mejora del Sistema de Gestión de Calidad es indispensable que una vez se inicie ";
			$pTema = $pTema . "la elaboración de una propuesta, se realice la solicitud del personal requerido a través del link <I>(Solicitudes / Personal)</I> del Portal.";
			$pFirma = "Departamento Contratos - Sistemas ";
		}		
		*/
		
			$pAsunto = "Intranet INGETEC - Asignación de código ";
			$pTema = "Se informa que se realizó la asignación de código para: <b>" . $cualNombre . "[". trim($pCodigoDef) . " " . trim($pCargoDef) . "]" . "</b>";
			$pTema = $pTema . "<br><br>";
			$pTema = $pTema . "Cliente: " . $cualCliente ;
			$pTema = $pTema . "<br><br>";
			$pFirma = "Departamento Contratos  ";

			$pPara= "pbaron@ingetec.com.co";
			enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);

		//***FIN EnviarMailPEAR

		//Hace el envio de los correos de los Relacionados en Copias
		$cursor5 = mssql_query($sql5);
		while ($reg5=mssql_fetch_array($cursor5)) {
			if ($reg5[email] != "") {
				$pPara= trim($reg5[email]) . "@ingetec.com.co";
				enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);  //OJO ACTIVAR
			}
		}

		//Hace el envio de los correos de los ordenadores del gasto

		$cursor3 = mssql_query($sql3);
		while ($reg3=mssql_fetch_array($cursor3)) {
			if ($reg3[email] != "") {
				$pPara= trim($reg3[email]) . "@ingetec.com.co";
				enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
			}
		} 
		
		//Hace el envio al correo del Directo, el usuario y los programadores y el coordinador
		if ($pMailDirector != "") {
			$pPara= trim($pMailDirector) . "@ingetec.com.co";
			enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
		}
		if ($pMailUsuario != "") {
			$pPara= trim($pMailUsuario) . "@ingetec.com.co";
			enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
		}
		if ($pMailProg1 != "") {
			$pPara= trim($pMailProg1) . "@ingetec.com.co";
			enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
		}
		if ($pMailProg2 != "") {
			$pPara= trim($pMailProg2) . "@ingetec.com.co";
			enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
		}
		if ($miMailCoordina != "") {
			$pPara= trim($miMailCoordina) . "@ingetec.com.co";
			enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
		}

		//26Abr2010
		//Enviar copia del correo electrónico de la creación de un proyecto a Oscar Barbosa y Guillermo Bazurto
		//para creación del proyecto en la base de datos del PageDevice
		

		$pPara= "gbazurto@ingetec.com.co";
		enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);

		
		//$pPara= "oscarbarbosa@ingetec.com.co";
		//$pPara= "pbaron@ingetec.com.co";
		//enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
		//Cierra 26Abr2010

		//06-Oct-2010
		//Se inhabilitó por cambio de servidor de correo
		/*

		//Hace el envio de los correos de los Relacionados en Copias
		//***Arma el mensaje
		$msgM="<html>";
		$msgM=$msgM." <head>";
		$msgM=$msgM." <title></title>";
		$msgM=$msgM." <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>";
		$msgM=$msgM." <style type='text/css'> ";
		$msgM=$msgM." <!-- ";
		$msgM=$msgM." .Estilo1 { ";
		$msgM=$msgM." 	font-family: Verdana, Arial, Helvetica, sans-serif; ";
		$msgM=$msgM." 	font-weight: bold; ";
		$msgM=$msgM." 	font-size: 12px; ";
		$msgM=$msgM." 	color: #FFFFFF; ";
		$msgM=$msgM." } ";
		$msgM=$msgM." .Estilo2 { ";
		$msgM=$msgM." 	font-family: Verdana, Arial, Helvetica, sans-serif; ";
		$msgM=$msgM." 	color: #666666; ";
		$msgM=$msgM." 	font-size: 12px; ";
		$msgM=$msgM." } ";
		$msgM=$msgM." --> ";
		$msgM=$msgM." </style>";
		$msgM=$msgM." </head>";
		$msgM=$msgM." <body> ";
		$msgM=$msgM." <table width='100%'  border='0' cellspacing='0' cellpadding='0'>";
		$msgM=$msgM."   <tr> ";
		$msgM=$msgM."     <td height='10' bgcolor='#999999'></td>";
		$msgM=$msgM."   </tr>";
		$msgM=$msgM."   <tr>";
		$msgM=$msgM."     <td>&nbsp;</td>";
		$msgM=$msgM."   </tr>";
		$msgM=$msgM."   <tr>";
		$msgM=$msgM."     <td class='Estilo2'>Estimado usuario: </td> \n\n";
		$msgM=$msgM."   </tr>";
		$msgM=$msgM."   <tr>";
		$msgM=$msgM."     <td>&nbsp;</td>";
		$msgM=$msgM."   </tr>";
		$msgM=$msgM."   <tr>";
		$msgM=$msgM."     <td><span class='Estilo2'>Se informa que se realizó la asignación de código para: <b>" . $cualNombre . "[". trim($pCodigoDef) . "." . trim($pCargoDef) . "]" . "</b> </span></td> \n";
		$msgM=$msgM."   </tr>";
		$msgM=$msgM."   <tr>";
		$msgM=$msgM."     <td>&nbsp;</td>";
		$msgM=$msgM."   </tr>";
		$msgM=$msgM."   <tr>";
		$msgM=$msgM."     <td class='Estilo2'>Por favor consultar el Portal <a href='http://www.ingetec.com.co/portal' target='_blank'>www.ingetec.com.co/portal</a></td> \n";
		$msgM=$msgM."   </tr>";
		$msgM=$msgM."   <tr>";
		$msgM=$msgM."     <td>&nbsp;</td>";
		$msgM=$msgM."   </tr>";
		$msgM=$msgM."   <tr>";
		$msgM=$msgM."     <td class='Estilo2'>Atentamente,</td> \n\n";
		$msgM=$msgM."   </tr>";
		$msgM=$msgM."   <tr>";
		$msgM=$msgM."     <td>&nbsp;</td>";
		$msgM=$msgM."   </tr>";
		$msgM=$msgM."   <tr>";
		$msgM=$msgM."     <td class='Estilo2'>Departamento Contratos - Sistemas </td>";
		$msgM=$msgM."   </tr>";
		$msgM=$msgM."   <tr>";
		$msgM=$msgM."     <td>&nbsp;</td>";
		$msgM=$msgM."   </tr>";
		$msgM=$msgM."   <tr >";
		$msgM=$msgM."     <td bgcolor='#999999' ><span class='Estilo1'>Ingetec S.A. </span></td>";
		$msgM=$msgM."   </tr>";
		$msgM=$msgM." </table>";
		$msgM=$msgM." </body>";
		$msgM=$msgM." </html>";
		//***
		
		$cursor5 = mssql_query($sql5);
		$Asunto = "Asignación de código";
		//$Descripcion = "Se informa que se realizó la asignación de código para: " . $cualNombre . " por favor consulte el Portal " . $cualCliente . ". Departamento Contratos - Sistemas";
		$Descripcion = $msgM;
		//cABECERAS
		$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$cabeceras .= "From: Portal Ingetec S.A. <grodrig@ingetec.com.co>" . "\r\n";
		
		while ($reg5=mssql_fetch_array($cursor5)) {
			if ($reg5[email] != "") {
				$cualMail= trim($reg5[email]) . "@ingetec.com.co";
				@mail($cualMail,$Asunto,$Descripcion, $cabeceras); 
			}
		}
	

		//Hace el envio de los correos de los ordenadores del gasto
		$cursor3 = mssql_query($sql3);
		while ($reg3=mssql_fetch_array($cursor3)) {
			if ($reg3[email] != "") {
				$cualMail= trim($reg3[email]) . "@ingetec.com.co";
				mail($cualMail,$Asunto,$Descripcion,$cabeceras); 
			}
		} 
		
		//Hace el envio al correo del Directo, el usuario y los programadores y el coordinador
		if ($pMailDirector != "") {
			$cualMail= trim($pMailDirector) . "@ingetec.com.co";
			mail($cualMail,$Asunto,$Descripcion,$cabeceras); 
		}
		if ($pMailUsuario != "") {
			$cualMail= trim($pMailUsuario) . "@ingetec.com.co";
			mail($cualMail,$Asunto,$Descripcion,$cabeceras); 
		}
		if ($pMailProg1 != "") {
			$cualMail= trim($pMailProg1) . "@ingetec.com.co";
			mail($cualMail,$Asunto,$Descripcion,$cabeceras); 
		}
		if ($pMailProg2 != "") {
			$cualMail= trim($pMailProg2) . "@ingetec.com.co";
			mail($cualMail,$Asunto,$Descripcion,$cabeceras); 
		}
		if ($miMailCoordina != "") {
			$cualMail= trim($miMailCoordina) . "@ingetec.com.co";
			mail($cualMail,$Asunto,$Descripcion,$cabeceras); 
		}

		//26Abr2010
		//Enviar copia del correo electrónico de la creación de un proyecto a Oscar Barbosa y Guillermo Bazurto
		//para creación del proyecto en la base de datos del PageDevice
		$cualMail= "gbazurto@ingetec.com.co";
		mail($cualMail,$Asunto,$Descripcion,$cabeceras); 
		$cualMail= "oscarbarbosa@ingetec.com.co";
		mail($cualMail,$Asunto,$Descripcion,$cabeceras); 
		//Cierra 26Abr2010

		//Fin 06-Oct-2010
		*/	
			
		//Realiza la grabación del proyecto en la tabla Proyectos de la base de datos Hoja de tiempo
		//id_proyecto, codigo, cargo_defecto, nombre, id_director, id_coordinador, id_estado, 
		//especial, maxclase, codProyecto, idEmpresa, fechaCrea, descCargoDefecto
/*
		$sqlHT = "INSERT INTO HojaDeTiempo.dbo.PROYECTOS (codigo, cargo_defecto, nombre, " ;
		$sqlHT = $sqlHT . " id_director, id_coordinador, id_estado, idEmpresa, descCargoDefecto) " ;
*/
		$sqlHT = "INSERT INTO HojaDeTiempo.dbo.PROYECTOS (codigo, cargo_defecto, nombre, " ;
//		$sqlHT = $sqlHT . " id_director, id_coordinador, id_estado, idEmpresa, descCargoDefecto, idEstadoCP, erpNiv1CP, erpNiv2CP, esSubcodigo) " ;
		$sqlHT = $sqlHT . " id_director, id_coordinador, id_estado, idEmpresa, descCargoDefecto, idEstadoCP, erpNiv1CP, erpNiv2CP, esSubcodigo, codigoANT,  cargo_defectoANT, esCodLargo ) " ;
		$sqlHT = $sqlHT . " VALUES ('" . $pCodigoDef . "', " ;
		$sqlHT = $sqlHT . " '" . $pCargoDef . "', " ;
		$sqlHT = $sqlHT . " '" . $cualNombre . "', " ;
//		$sqlHT = $sqlHT . $pUniProg1 .", " ;
//		$sqlHT = $sqlHT . $pUniProg2 . ", " ;	
		$sqlHT = $sqlHT . $pUnidadDirector .", " ;
		if (trim($pUnidadCoordina) == "") {
			$sqlHT = $sqlHT . "NULL , " ;	
		}
		else {
			$sqlHT = $sqlHT . $pUnidadCoordina . ", " ;	
		}
		$sqlHT = $sqlHT . " 2 , " ; //2 = Activo
		$sqlHT = $sqlHT . $pLaEmpresa . ", " ; //el id de la empresa
		$sqlHT = $sqlHT . " '" . $pObservaDef . "', " ; //Descripción cargo defecto
		
		
		
		//Nuevo para ERP
		$sqlHT = $sqlHT . $pIdEstadoCP . ", " ; //el id del estado seleccionado en la estructura del proyecto
		$sqlHT = $sqlHT . " '" . $pSevenEmpresa . "', " ; //Estructura del proyecto ERP - Empresa
		$sqlHT = $sqlHT . " '" . $pIdenEstadoCP . "', " ; //Estructura del proyecto ERP - estado
		$sqlHT = $sqlHT . " '" . $pEsSubcodigo . "', " ; //Determina si el código de proyecto se genera a partir de una solicitud de código=0 o subcódigo=1
		//cierra Nuevo para ERP
		
		//03Jun2016
		//PBM
		//Se implementó Código y cargo Corto para Speedware por solicitud de GRM
		//Solicitud GRM - Poner un código corto
		//$sqlHT = $sqlHT . " '" . $cCodCorto . "', " ; //Código corto que corresponde a la equivalencia que usará Speedware y para que Contratos Facturación pueda entenderlo
		//$sqlHT = $sqlHT . " '" . $cCrgCorto . "', " ; //cargo corto que corresponde a la equivalencia que usará Speedware y para que Contratos Facturación pueda entenderlo
		//$sqlHT = $sqlHT . " '1' " ; //Para que en la tabla de Proyectos se identifique como código largo en adelante.
		
		//24Oct2016
		//PBM 
		//Se desactiva esta parte de código porque Felipe Durán solicitó que no se requieren en adelante los códigos cortos. 
		//Autorizado para hacer con urgencia GRM 24-Oct-2016
		$sqlHT = $sqlHT . " NULL, " ; //Código corto que corresponde a la equivalencia que usará Speedware y para que Contratos Facturación pueda entenderlo
		$sqlHT = $sqlHT . " NULL, " ; //cargo corto que corresponde a la equivalencia que usará Speedware y para que Contratos Facturación pueda entenderlo
		$sqlHT = $sqlHT . " '1' " ; //Para que en la tabla de Proyectos se identifique como código largo en adelante.
		//Cierra 24Oct2016
		
		$sqlHT = $sqlHT . " ) " ;	
		$sqlHT = $sqlHT." SELECT @@IDENTITY AS 'codProyecto' ";
//echo $sqlHT . "<BR>";
//exit;
		if($cursorHT=@mssql_query($sqlHT)){
			if ($regHT=mssql_fetch_array($cursorHT)) {
				$miCodigoProyec = $regHT[codProyecto];
			}
			else {
				$miCodigoProyec = 'NULL';
			}
		}	

		
		//18Oct2007
		//Realiza la grabación de los cargos adicionales si hay, en la tabla Cargo de la base de datos HojaDeTiempo
		//Seleccionar los registros relacionados al cargo
		$sqlCA="Select * from GestiondeInformacionDigital.dbo.CargosSolCodigo ";
		$sqlCA= $sqlCA. " where secuencia =" . $pSolicitudNo;
		//$sqlCA= $sqlCA. " and cargoDefecto <> 1 "; //05Feb2014 PBM - Se elimina porque aplican TODOS los cargos
		$cursorCA = mssql_query($sqlCA);
		while ($regCA=mssql_fetch_array($cursorCA)) {
			//Realiza la inserción de los cargos adicionales en la hoja de tiempo
//			$IsqlCA = "INSERT INTO HojaDeTiempo.dbo.Cargos (id_proyecto, cargos_adicionales, estado, descripcion ) " ;
//			$IsqlCA = "INSERT INTO HojaDeTiempo.dbo.Cargos (id_proyecto, cargos_adicionales, estado, descripcion, idCargoCP ) " ;
			$IsqlCA = "INSERT INTO HojaDeTiempo.dbo.Cargos (id_proyecto, cargos_adicionales, estado, descripcion, idCargoCP, estadoCargo ) " ;
			$IsqlCA = $IsqlCA . " VALUES (" . $miCodigoProyec . ", " ;
			$IsqlCA = $IsqlCA . " '" . $regCA[cargo] . "', '0', " ;
			$IsqlCA = $IsqlCA . " '" . $regCA[observacion] . "', " ;
			$IsqlCA = $IsqlCA . " " . $regCA[idCargoCP] . ", " ; //Estructura del proyecto ERP - corresponde al id del cargo que identifica el iden segun Contratos
			$IsqlCA = $IsqlCA . " 'A' " ; //Para que la Intranet sólo visualice los cargos que se encuentran Activos en la homologación de proyectos
			$IsqlCA = $IsqlCA . " )" ;
			$IcursorCA = mssql_query($IsqlCA);
		}
		//Cierra 18Oct2007

		//03Jun2016
		//PBM
		//Grabar la tabla de equivalencias con los códigos largos Vs los cortos para que Contratos - Facturación pueda entenderlos
		
		$equivaleCargos="select * ";
		$equivaleCargos=$equivaleCargos." FROM HojaDeTiempo.dbo.Cargos ";
		$equivaleCargos=$equivaleCargos." where id_proyecto = " . $miCodigoProyec ;
		$equivaleCargos=$equivaleCargos." and estadoCargo = 'A' ";
		$cursorequivaleCargos = mssql_query($equivaleCargos);
		
//		echo "-----------cargos <br> " . $equivaleCargos . "<br>" ;
		
		$secEC=1;
		while ($regEC=mssql_fetch_array($cursorequivaleCargos)) {
			//Inserta las equivalencias de los cargos en la tabla erpTBequivalenciaProyectos
			//id_proyecto, secuencia, codigoCorto, cargoCorto, nivel1, nivel2, nivel3, nivel4, nivel5, nomCompleto, nomCargo, usuarioEnvia, fechaEnvia, nomArchivo, 
			//version, estado, usuarioCrea, fechaCrea, usuarioMod, fechaMod
			$sqlInsEC="INSERT INTO HojaDeTiempo.dbo.erpTBequivalenciaProyectos ";
			$sqlInsEC=$sqlInsEC." (id_proyecto, secuencia, codigoCorto, cargoCorto, nivel1, nivel2, nivel3, nivel4, nivel5, nomCompleto, nomCargo, usuarioEnvia, fechaEnvia, nomArchivo, ";
			$sqlInsEC=$sqlInsEC." version, estado, usuarioCrea, fechaCrea ) ";
			$sqlInsEC=$sqlInsEC." VALUES ( ";
			$sqlInsEC=$sqlInsEC." " . $miCodigoProyec . ", ";
			$sqlInsEC=$sqlInsEC." " . $secEC . ", ";
			
			//$sqlInsEC=$sqlInsEC." '" . $cCodCorto . "', " ;
			//$sqlInsEC=$sqlInsEC." '" . $cCrgCorto . "', " ;
			
			//24Oct2016
			//PBM 
			//Se desactiva esta parte de código porque Felipe Durán solicitó que no se requieren en adelante los códigos cortos. 
			//Autorizado para hacer con urgencia GRM 24-Oct-2016
			$sqlInsEC=$sqlInsEC." NULL, " ;
			$sqlInsEC=$sqlInsEC." NULL, " ;
			//Cierra 24Oct2016
	
			$sqlInsEC=$sqlInsEC." '000', "; //Forzamos Empresa a 000 según instrucciones de equivalencias de Contratos -- Llenar todo con 0 a la izquierda
			$sqlInsEC=$sqlInsEC." '0', "; //Forzamos Empresa a 000 según instrucciones de equivalencias de Contratos -- Llenar todo con 0 a la izquierda
			$sqlInsEC=$sqlInsEC." '" . $pCodigoDef . "', " ;
			$sqlInsEC=$sqlInsEC." '" . $pCargoDef . "', " ;
			$sqlInsEC=$sqlInsEC." '" . $regEC[cargos_adicionales] . "', " ;
			$sqlInsEC=$sqlInsEC." '" . $cualNombre . "', " ;
			$sqlInsEC=$sqlInsEC." '" . $regEC[descripcion] . "', " ;
			$sqlInsEC=$sqlInsEC . $_SESSION["sesUnidadUsuario"] . ", ";
			$sqlInsEC=$sqlInsEC." '" .  gmdate ("m/d/Y") . "', ";
			$sqlInsEC=$sqlInsEC." 'Grabado al generar el código del proyecto mediante solicitud No. ". $pSolicitudNo ."', ";
			$sqlInsEC=$sqlInsEC." '1',  ";
			$sqlInsEC=$sqlInsEC." 'A', ";
			$sqlInsEC=$sqlInsEC . $_SESSION["sesUnidadUsuario"] . ", ";
			$sqlInsEC=$sqlInsEC." '" .  gmdate ("m/d/Y") . "' ";
			$sqlInsEC=$sqlInsEC." ) ";
//			echo "-----------Equivalencias <br> " . $sqlInsEC . "<br>" ;
			$IcursorsqlInsEC = mssql_query($sqlInsEC);

			$secEC=$secEC+1;		
		}
		
		
		//03Jun2016 - Cierra 
		
		//20Nov2007
		//PBM
		//Asigna automáticamente al proyecto el horario por defecto del proyecto Gastos generales
		//Encuentra el horario defecto = 1 del proyecto gastos generales = 42
		/*
		$hSQLHD = "SELECT * FROM HojaDeTiempo.dbo.HorariosProy Where id_proyecto =42 AND HorarioDefecto = 1 ";
		$hcursorHD = mssql_query($hSQLHD);
		if ($hregHD=mssql_fetch_array($hcursorHD)) {
			//Realiza el Insert del horario defecto para el proyecto que acaba de insertarse.
			$IhSQLHD="insert into HojaDeTiempo.dbo.HorariosProy (idhorario,id_proyecto,horariodefecto) values ( ";
			$IhSQLHD=$IhSQLHD. $hregHD[IDhorario] . ",  ";
			$IhSQLHD=$IhSQLHD. $miCodigoProyec . ", ";
			$IhSQLHD=$IhSQLHD." 1 ";
			$IhSQLHD=$IhSQLHD." ) ";
			$IhcursorHD= mssql_query($IhSQLHD);
		}	
		*/	

		//31 Oct 2016 CMA
		//ASOCIA LOS HORARIOS DEFECTO=1 AL TURNO 0
		$sql_insert_horario_turn_0="insert into HojaDeTiempo.dbo.HorariosProy (IDhorario,id_proyecto,HorarioDefecto,ubicacion,IDTurno,usuarioCrea,estadoHorarioProy) select HojaDeTiempo.dbo.Horarios.IDhorario,".$miCodigoProyec.",0,localiza,1,'".$_SESSION["sesUnidadUsuario"]."',1 from HojaDeTiempo.dbo.Horarios where HojaDeTiempo.dbo.Horarios.esDefectoT='1'  ";		
		$cur_insert_horario_turn_0 = mssql_query($sql_insert_horario_turn_0);

//echo $sql_insert_horario_turn_0." ---- ".mssql_get_last_message();
		
		//Asigna automáticamente al proyecto el multiplicador "Sin Multiplicador" 
		//Encuentra el código del Multiplicador = 'Sin Multiplicador'
		$mSqlMV="SELECT * FROM HojaDeTiempo.dbo.FraccionesV where Descripcion = 'Sin Multiplicador'  ";
		$mcursorMV = mssql_query($mSqlMV);
		if ($mregMV=mssql_fetch_array($mcursorMV)) {
			//Realiza el insert de multiplicador para el proyecto que acaba de insertarse
			$ImSqlMV="insert into HojaDeTiempo.dbo.fraccionesvproy (id_proyecto,idfraccion) values ( ";
			$ImSqlMV=$ImSqlMV. $miCodigoProyec . ", ";
			$ImSqlMV=$ImSqlMV. $mregMV[IDfraccion] ;
			$ImSqlMV=$ImSqlMV." ) ";
			$ImcursorMV= mssql_query($ImSqlMV);
		}

		//Cierra 20Nov2007
		
		//5Jun2008
		//Calcula la fecha final para la actividad
		$hoy = getdate();
		$fechaHoy = mktime(0,0,0,$hoy['mon'], $hoy['mday'], $hoy['year']); //fecha actual en número de segundos
		if (trim($pElPlazo) != "") {
			$laFechaFinEs = ($fechaHoy + 24*60*60*30*$pElPlazo); 
		}
		else {
			$laFechaFinEs = ($fechaHoy + 24*60*60*30*1); 
		}
/*		echo ("<script>alert('La fecha es ".gmdate("n/d/Y", $laFechaFinEs)." .');</script>");*/
//		$laFechaFinalEs = gmdate("n/d/Y", $laFechaFinEs) ;
		
		//Se genera de manera automática la actividad Dirección para el proyecto que acaba de crearse
		//HojaDeTiempo.dbo.Actividades
		//id_proyecto, id_actividad, nombre, fecha_inicio, fecha_fin, macroactividad, 
		//id_encargado, avance_reportado, resumen_avance, codigo_adp, dependeDe		
		$mSqlActiv="INSERT INTO HojaDeTiempo.dbo.Actividades (id_proyecto, id_actividad, nombre, fecha_inicio, fecha_fin, macroactividad,  ";
		$mSqlActiv=$mSqlActiv." id_encargado, avance_reportado, resumen_avance, codigo_adp, dependeDe, actPrincipal, tipoActividad, nivelesActiv ) ";
		$mSqlActiv=$mSqlActiv." VALUES ( " . $miCodigoProyec . ", " ;
		$mSqlActiv=$mSqlActiv." 1, 'Dirección', " ;
		$mSqlActiv=$mSqlActiv." '" . gmdate ("n/d/y") . "', "  ;
		$mSqlActiv=$mSqlActiv." '" . gmdate("n/d/Y", $laFechaFinEs) . "', " ;
		$mSqlActiv=$mSqlActiv." NULL, NULL, NULL, NULL, NULL, 0, 1, '1', '1-' " ;
		$mSqlActiv=$mSqlActiv." ) " ;
		$mCursorActiv= mssql_query($mSqlActiv);
		
		//27Nov2009
		//Se genera para todos los proyectos la actividad Costos SISSO (444)
		//05jUL2011PBM sE ELIMINA EL FILTRO PARA QUE SE CREE SISSO EN TODOS LOS TIPOS DE PROYECTO. Autorizado GRM
//		if (trim($vMiTipoProy) != '3' ) {
			$mSqlActiv2="INSERT INTO HojaDeTiempo.dbo.Actividades (id_proyecto, id_actividad, nombre, fecha_inicio, fecha_fin, macroactividad,  ";
			$mSqlActiv2=$mSqlActiv2." id_encargado, avance_reportado, resumen_avance, codigo_adp, dependeDe, actPrincipal, tipoActividad, nivelesActiv ) ";
			$mSqlActiv2=$mSqlActiv2." VALUES ( " . $miCodigoProyec . ", " ;
			$mSqlActiv2=$mSqlActiv2." 2, 'Costos SISSO', " ;
			$mSqlActiv2=$mSqlActiv2." '" . gmdate ("n/d/y") . "', "  ;
			$mSqlActiv2=$mSqlActiv2." '" . gmdate("n/d/Y", $laFechaFinEs) . "', " ;
			$mSqlActiv2=$mSqlActiv2." '444', NULL, NULL, NULL, NULL, 0, 2, '1', '2-' " ;
			$mSqlActiv2=$mSqlActiv2." ) " ;
			$mCursorActiv2= mssql_query($mSqlActiv2);
//		}
		
		//30Jun2011
		//PBM
		//Crear automáticamente la actividad Gerencia de proyectos 445 con los responsables registrados en la tabla 
		
		//Realiza la grabación de la actividad Gerencia de Proyectos
		$mSqlActiv3="INSERT INTO HojaDeTiempo.dbo.Actividades (id_proyecto, id_actividad, nombre, fecha_inicio, fecha_fin, macroactividad,  ";
		$mSqlActiv3=$mSqlActiv3." id_encargado, avance_reportado, resumen_avance, codigo_adp, dependeDe, actPrincipal, tipoActividad, nivelesActiv ) ";
		$mSqlActiv3=$mSqlActiv3." VALUES ( " . $miCodigoProyec . ", " ;
		$mSqlActiv3=$mSqlActiv3." 3, 'Gerencia de proyectos', " ;
		$mSqlActiv3=$mSqlActiv3." '" . gmdate ("n/d/y") . "', "  ;
		$mSqlActiv3=$mSqlActiv3." '" . gmdate("n/d/Y", $laFechaFinEs) . "', " ;
		$mSqlActiv3=$mSqlActiv3." '445', NULL, NULL, NULL, NULL, 0, 2, '1', '3-' " ;
		$mSqlActiv3=$mSqlActiv3." ) " ;
		$mCursorActiv3= mssql_query($mSqlActiv3);
		
		//PBM
		//24Feb2013
		//Por solicitud de CME se bloquea la creación de ResponsablesActividad para Gerencia de Proyectos
		//cuando la división que solicita es Geotécnia
		if ($vMiIDDivision != 9) {
		
		//Realiza la busqueda de las personas autorizadas para asignarlas como responsables en la actividad
		$mSqlActiv4=" SELECT unidad, estado, fechaGraba, usuarioGraba, fechaAct, usuarioAct ";
		$mSqlActiv4=$mSqlActiv4." FROM HojaDeTiempo.dbo.ResponsablesGerProyectos ";
		$mSqlActiv4=$mSqlActiv4." WHERE estado = 'A' ";
		$mCursorActiv4= mssql_query($mSqlActiv4);
		while ($regCursorActiv4=mssql_fetch_array($mCursorActiv4)) {
			//Inserta los responsables de la actividad
			$mSqlActiv5="INSERT INTO HojaDeTiempo.dbo.ResponsablesActividad (id_proyecto, id_actividad, unidad ) ";
			$mSqlActiv5=$mSqlActiv5." VALUES ( " . $miCodigoProyec . ", " ;
			$mSqlActiv5=$mSqlActiv5." 3, " ;
			$mSqlActiv5=$mSqlActiv5." " . $regCursorActiv4[unidad] . " "  ;
			$mSqlActiv5=$mSqlActiv5." ) " ;
			$mCursorActiv5= mssql_query($mSqlActiv5);
		}		
		
		} // Cierra if ($vMiIDDivision != 9)
		 
		//Cierra 30Jun2011

		//Se graban los programadores del proyecto, los cuales estarán asociados a la actividad de dirección
		//HojaDeTiempo.dbo.Programadores
		//id_proyecto, id_actividad, unidad, progProyecto
		//Programador1
		$mSqlP1="INSERT INTO HojaDeTiempo.dbo.Programadores (id_proyecto, id_actividad, unidad, progProyecto) ";
		$mSqlP1=$mSqlP1." VALUES ( "  . $miCodigoProyec . ", " ;
		$mSqlP1=$mSqlP1." 1, ";
		$mSqlP1=$mSqlP1. $pUniProg1 . ", ";
		$mSqlP1=$mSqlP1." 1 "; //1=eS PROGRAMADOR DEL PROYECTO
		$mSqlP1=$mSqlP1." ) ";
		$mCursorP1= mssql_query($mSqlP1);
		
		//Programador2
		$mSqlP2="INSERT INTO HojaDeTiempo.dbo.Programadores (id_proyecto, id_actividad, unidad, progProyecto) ";
		$mSqlP2=$mSqlP2." VALUES ( "  . $miCodigoProyec . ", " ;
		$mSqlP2=$mSqlP2." 1, "; //Actividad 1 = Dirección
		$mSqlP2=$mSqlP2. $pUniProg2 . ", ";
		$mSqlP2=$mSqlP2." 1 "; //1=eS PROGRAMADOR DEL PROYECTO
		$mSqlP2=$mSqlP2." ) ";
		$mCursorP2= mssql_query($mSqlP2);
		
		//Actualiza el identificador del proyecto en ordenadores del gasto
		$mSqlOG="UPDATE GestiondeInformacionDigital.dbo.OrdenadorGasto  ";
		$mSqlOG=$mSqlOG."SET id_proyecto = " . $miCodigoProyec ;
		$mSqlOG=$mSqlOG." WHERE secuencia = " . $pSolicitudNo ;
		$mCursorOG= mssql_query($mSqlOG);
		
		//Actualiza el identificador del proyecto en CargosSolCodigo
		$mSqlCC="UPDATE GestiondeInformacionDigital.dbo.CargosSolCodigo  ";
		$mSqlCC=$mSqlCC."SET id_proyecto = " . $miCodigoProyec ;
		$mSqlCC=$mSqlCC." WHERE secuencia = " . $pSolicitudNo ;
		$mCursorCC= mssql_query($mSqlCC);
		
		//05Feb2014
		//PBM
		//Actualiza el identificador del proyecto en SolicitudCodigoConsorcios
		$mSqlCCC="UPDATE GestiondeInformacionDigital.dbo.SolicitudCodigoConsorcios  ";
		$mSqlCCC=$mSqlCCC."SET id_proyecto = " . $miCodigoProyec ;
		$mSqlCCC=$mSqlCCC." WHERE secuencia = " . $pSolicitudNo ;
		$mCursorCCC= mssql_query($mSqlCCC);
		
		//05Feb2014
		//PBM
		//Actualiza el identificador del proyecto en SolicitudCodigo
		$mSqlSC1="UPDATE GestiondeInformacionDigital.dbo.SolicitudCodigo  ";
		$mSqlSC1=$mSqlSC1."SET id_proyecto = " . $miCodigoProyec ;
		$mSqlSC1=$mSqlSC1." WHERE secuencia = " . $pSolicitudNo ;
		$mCursorSC1= mssql_query($mSqlSC1);
		//Cierra 5Jun2008
		
		
		//***PBM - 26Oct2010
		//Para Page Device
		//Grabar en BD pcontrol en la tabla pc_bilcode la información del proyecto
		$mySqlconexion = conectarMySql();
		
		$nombreCargo = "";
		//Grabar todos los cargos creados para proyecto en la tabla  pc_bilcode 
		//bil_id, bil_code, client, matter, ident
		$ySqlCA="Select * from GestiondeInformacionDigital.dbo.CargosSolCodigo ";
		$ySqlCA= $ySqlCA. " where secuencia =" . $pSolicitudNo;
		$yCursorCA = mssql_query($ySqlCA);
		while ($yRegCA=mssql_fetch_array($yCursorCA)) {
			
			$nombreCargo = " [" . $pCodigoDef . "." . $yRegCA[cargo] . "] " . strtoupper($cualNombre) . "-" . strtoupper($yRegCA[observacion]) ;
			
			$nombreCargo = str_replace("Á", "A", $nombreCargo);
			$nombreCargo = str_replace("É", "E", $nombreCargo);
			$nombreCargo = str_replace("Í", "I", $nombreCargo);
			$nombreCargo = str_replace("Ó", "O", $nombreCargo);
			$nombreCargo = str_replace("Ú", "U", $nombreCargo);
			$nombreCargo = str_replace("Ñ", "N", $nombreCargo);
			$nombreCargo = str_replace("Ü", "U", $nombreCargo);
			$nombreCargo = str_replace(",", " ", $nombreCargo);
			$nombreCargo = str_replace("ñ", "N", $nombreCargo);
			$nombreCargo = str_replace("á", "A", $nombreCargo);
			$nombreCargo = str_replace("é", "E", $nombreCargo);
			$nombreCargo = str_replace("í", "I", $nombreCargo);
			$nombreCargo = str_replace("ó", "O", $nombreCargo);
			$nombreCargo = str_replace("ú", "U", $nombreCargo);
			
			$ySql00="INSERT INTO pc_bilcode (bil_code, client, matter, ident) ";
			$ySql00=$ySql00 . " VALUES (";
			$ySql00=$ySql00 . " '" . $pCodigoDef . "." . $yRegCA[cargo] . "', ";
			$ySql00=$ySql00 . " '" . $nombreCargo . "', " ;
			$ySql00=$ySql00 . " '', ";
			$ySql00=$ySql00 . " '" . $miCodigoProyec . "' ";
			$ySql00=$ySql00 . " ) ";

			$yCursor00 = mysql_query($ySql00);
			
			//echo " ySql00=" . $ySql00 . "<br>" ;
			$queryLogMy= $queryLogMy . $ySql00 ;
			
		}
		
		
		//Encontrar en la tabla pc_bilcode de Page Device los id de los proyectos/cargos para asociar automáticamente Director, 
		//Coordinador, Ordenador de gasto, Programadores 
		$idEnMySql="";
		$ySql01="SELECT * FROM pc_bilcode ";
		$ySql01=$ySql01." WHERE ident = '" . $miCodigoProyec . "' ";
		$yCursor01 = mysql_query($ySql01);
		while ($yReg01=mysql_fetch_array($yCursor01)) {
			$idEnMySql=$yReg01[bil_id];
			
			if ($pMailDirector != "") {
				/********
				MySql:
				1. Consulta en la tabla pc_user si el nombre del usuario ya está establecido como impresor. Si no se encuentra, se almacena la información
				en la tabla con valores por defecto
				2. Obtiene el valor user_id y lo asocia para posteriormente relacionarlo con los cargos autorizados
				********/
				$ySql02 = " SELECT user_id FROM pc_user WHERE user_name = '" . $pMailDirector . "' ";
				$ySql02 = $ySql02 . " AND descrip = '" . $pUnidadDirector . "' ";
				$yCursor02 = mysql_query($ySql02);
				if($yReg02 = mysql_fetch_array($yCursor02)){
					$yIdUsuarioDirector = $yReg02['user_id'];
				} else {
					$yIdUsuarioDirector = 0;
				}
				
				//echo " ySql02 =" . $ySql02 . "<br>" ;
				
				if($yIdUsuarioDirector == 0){
					$ySql02b = " INSERT INTO pc_user ( user_name, descrip, bil_codes ) ";
					$ySql02b = $ySql02b . " VALUES ( ";
					$ySql02b = $ySql02b . " '" . $pMailDirector . "', ";
					$ySql02b = $ySql02b . " '" . $pUnidadDirector . "', ";
					$ySql02b = $ySql02b . " 1 ";
					$ySql02b = $ySql02b . " ) ";
					//echo $ySql02b . "<br>";
					$yCursor02b = mysql_query($ySql02b);
					//echo " ySql02b =" . $ySql02b . "<br>" ;
					$queryLogMy= $queryLogMy . $ySql02b ;
					
					//Toma el último user_id generado por MySql para pc_user en la sesión activa (evita problemas de concurrencia)
					$ySql02c = " SELECT LAST_INSERT_ID() AS elIdUsuario FROM pc_user ";
					$yCursor02c = mysql_query($ySql02c);
					if($yReg02c = mysql_fetch_array($yCursor02c)){
						$yIdUsuarioDirector = $yReg02c['elIdUsuario'];
					}
					//echo " ySql02c =" . $ySql02c . "<br>" ;
				}
				
				
				/*****************
				MySql:
				2. Asocia en la tabla pc_user_bcode el usuario y el codigo del gasto
				*****************/
				$ySql02d = " INSERT INTO pc_user_bcode ( user_id, bil_id ) ";
				$ySql02d = $ySql02d . " VALUES ( ";
				$ySql02d = $ySql02d . " " . $yIdUsuarioDirector . ", ";
				$ySql02d = $ySql02d . " " . $idEnMySql . " ";
				$ySql02d = $ySql02d . " ) ";   
				$yCursor02d = mysql_query($ySql02d);
				//echo " ySql02d =" . $ySql02d . "<br>" ;
				$queryLogMy= $queryLogMy . $ySql02d ;
				
			}
			
			if ($pMailCoordina != "") {
				/********
				MySql:
				1. Consulta en la tabla pc_user si el nombre del usuario ya está establecido como impresor. Si no se encuentra, se almacena la información
				en la tabla con valores por defecto
				2. Obtiene el valor user_id y lo asocia para posteriormente relacionarlo con los cargos autorizados
				********/
				$ySql03 = " SELECT user_id FROM pc_user WHERE user_name = '" . $pMailCoordina . "' ";
				$ySql03 = $ySql03 . " AND descrip = '" . $pUnidadCoordina . "' ";
				$yCursor03 = mysql_query($ySql03);
				if($yReg03 = mysql_fetch_array($yCursor03)){
					$yIdUsuarioCoordina = $yReg03['user_id'];
				} else {
					$yIdUsuarioCoordina = 0;
				}
				//echo " ySql03 =" . $ySql03 . "<br>" ;
				if($yIdUsuarioCoordina == 0){
					$ySql03b = " INSERT INTO pc_user ( user_name, descrip, bil_codes ) ";
					$ySql03b = $ySql03b . " VALUES ( ";
					$ySql03b = $ySql03b . " '" . $pMailCoordina . "', ";
					$ySql03b = $ySql03b . " '" . $pUnidadCoordina . "', ";
					$ySql03b = $ySql03b . " 1 ";
					$ySql03b = $ySql03b . " ) ";
					//echo $ySql03b . "<br>";
					$yCursor03b = mysql_query($ySql03b);
					//echo " ySql03b =" . $ySql03b . "<br>" ;
					$queryLogMy= $queryLogMy . $ySql03b ;
					
					//Toma el último user_id generado por MySql para pc_user en la sesión activa (evita problemas de concurrencia)
					$ySql03c = " SELECT LAST_INSERT_ID() AS elIdUsuario FROM pc_user ";
					$yCursor03c = mysql_query($ySql03c);
					if($yReg03c = mysql_fetch_array($yCursor03c)){
						$yIdUsuarioCoordina = $yReg03c['elIdUsuario'];
					}
					//echo " ySql03c =" . $ySql03c . "<br>" ;
				}
		
				/*****************
				MySql:
				2. Asocia en la tabla pc_user_bcode el usuario y el codigo del gasto
				*****************/
				$ySql03d = " INSERT INTO pc_user_bcode ( user_id, bil_id ) ";
				$ySql03d = $ySql03d . " VALUES ( ";
				$ySql03d = $ySql03d . " " . $yIdUsuarioCoordina . ", ";
				$ySql03d = $ySql03d . " " . $idEnMySql . " ";
				$ySql03d = $ySql03d . " ) ";   
				$yCursor03d = mysql_query($ySql03d);
				//echo " ySql03d =" . $ySql03d . "<br>" ;
				$queryLogMy= $queryLogMy . $ySql03d ;

			}			
			
			
			if ($pMailProg1 != "") {
				/********
				MySql:
				1. Consulta en la tabla pc_user si el nombre del usuario ya está establecido como impresor. Si no se encuentra, se almacena la información
				en la tabla con valores por defecto
				2. Obtiene el valor user_id y lo asocia para posteriormente relacionarlo con los cargos autorizados
				********/
				$ySql04 = " SELECT user_id FROM pc_user WHERE user_name = '" . $pMailProg1 . "' ";
				$ySql04 = $ySql04 . " AND descrip = '" . $pUniProg1 . "' ";
				$yCursor04 = mysql_query($ySql04);
				if($yReg04 = mysql_fetch_array($yCursor04)){
					$yIdUsuarioProg1 = $yReg04['user_id'];
				} else {
					$yIdUsuarioProg1 = 0;
				}
				//echo " ySql04 =" . $ySql04 . "<br>" ;
				if($yIdUsuarioProg1 == 0){
					$ySql04b = " INSERT INTO pc_user ( user_name, descrip, bil_codes ) ";
					$ySql04b = $ySql04b . " VALUES ( ";
					$ySql04b = $ySql04b . " '" . $pMailProg1 . "', ";
					$ySql04b = $ySql04b . " '" . $pUniProg1 . "', ";
					$ySql04b = $ySql04b . " 1 ";
					$ySql04b = $ySql04b . " ) ";
					//echo $ySql04b . "<br>";
					$yCursor04b = mysql_query($ySql04b);
					$queryLogMy= $queryLogMy . $ySql04b ;
					
					//echo " ySql04b =" . $ySql04b . "<br>" ;
					//Toma el último user_id generado por MySql para pc_user en la sesión activa (evita problemas de concurrencia)
					$ySql04c = " SELECT LAST_INSERT_ID() AS elIdUsuario FROM pc_user ";
					$yCursor04c = mysql_query($ySql04c);
					if($yReg04c = mysql_fetch_array($yCursor04c)){
						$yIdUsuarioProg1 = $yReg04c['elIdUsuario'];
					}
					//echo " ySql04c =" . $ySql04c . "<br>" ;
					
				}
	
		
				/*****************
				MySql:
				2. Asocia en la tabla pc_user_bcode el usuario y el codigo del gasto
				*****************/
				$ySql04d = " INSERT INTO pc_user_bcode ( user_id, bil_id ) ";
				$ySql04d = $ySql04d . " VALUES ( ";
				$ySql04d = $ySql04d . " " . $yIdUsuarioProg1 . ", ";
				$ySql04d = $ySql04d . " " . $idEnMySql . " ";
				$ySql04d = $ySql04d . " ) ";   
				$yCursor04d = mysql_query($ySql04d);
				
				//echo " ySql04d =" . $ySql04d . "<br>" ;
				$queryLogMy= $queryLogMy . $ySql04d ;
			}			

			if ($pMailProg2 != "") {
				/********
				MySql:
				1. Consulta en la tabla pc_user si el nombre del usuario ya está establecido como impresor. Si no se encuentra, se almacena la información
				en la tabla con valores por defecto
				2. Obtiene el valor user_id y lo asocia para posteriormente relacionarlo con los cargos autorizados
				********/
				$ySql05 = " SELECT user_id FROM pc_user WHERE user_name = '" . $pMailProg2 . "' ";
				$ySql05 = $ySql05 . " AND descrip = '" . $pUniProg2 . "' ";
				$yCursor05 = mysql_query($ySql05);
				if($yReg05 = mysql_fetch_array($yCursor05)){
					$yIdUsuarioProg2 = $yReg05['user_id'];
				} else {
					$yIdUsuarioProg2 = 0;
				}
				//echo " ySql05 =" . $ySql05 . "<br>" ;
				
				if($yIdUsuarioProg2 == 0){
					$ySql05b = " INSERT INTO pc_user ( user_name, descrip, bil_codes ) ";
					$ySql05b = $ySql05b . " VALUES ( ";
					$ySql05b = $ySql05b . " '" . $pMailProg2 . "', ";
					$ySql05b = $ySql05b . " '" . $pUniProg2 . "', ";
					$ySql05b = $ySql05b . " 1 ";
					$ySql05b = $ySql05b . " ) ";
					//echo $ySql05b . "<br>";
					$yCursor05b = mysql_query($ySql05b);
					$queryLogMy= $queryLogMy . $ySql05b ;
					//echo " ySql05b =" . $ySql05b . "<br>" ;
					//Toma el último user_id generado por MySql para pc_user en la sesión activa (evita problemas de concurrencia)
					$ySql05c = " SELECT LAST_INSERT_ID() AS elIdUsuario FROM pc_user ";
					$yCursor05c = mysql_query($ySql05c);
					if($yReg05c = mysql_fetch_array($yCursor05c)){
						$yIdUsuarioProg2 = $yReg05c['elIdUsuario'];
					}
					//echo " ySql05c =" . $ySql05c . "<br>" ;
				}
								
		
				/*****************
				MySql:
				2. Asocia en la tabla pc_user_bcode el usuario y el codigo del gasto
				*****************/
				$ySql05d = " INSERT INTO pc_user_bcode ( user_id, bil_id ) ";
				$ySql05d = $ySql05d . " VALUES ( ";
				$ySql05d = $ySql05d . " " . $yIdUsuarioProg2 . ", ";
				$ySql05d = $ySql05d . " " . $idEnMySql . " ";
				$ySql05d = $ySql05d . " ) ";   
				$yCursor05d = mysql_query($ySql05d);
				//echo " ySql05d =" . $ySql05d . "<br>" ;
				$queryLogMy= $queryLogMy . $ySql05d ;

			}			
			
			//Hace el envio de los correos de los ordenadores del gasto
			$cursor3og = mssql_query($sql3);
			while ($reg3og=mssql_fetch_array($cursor3og)) {
			
				$ySql06 = " SELECT user_id FROM pc_user WHERE user_name = '" . trim($reg3og[email]) . "' ";
				$ySql06 = $ySql06 . " AND descrip = '" . trim($reg3og[unidadOrdenador]) . "' ";
				$yCursor06 = mysql_query($ySql06);
				if($yReg06 = mysql_fetch_array($yCursor06)){
					$yIdUsuarioOrdG = $yReg06['user_id'];
				} else {
					$yIdUsuarioOrdG = 0;
				}
				//echo " ySql06 =" . $ySql06 . "<br>" ;
				
				if($yIdUsuarioOrdG == 0){
					$ySql06b = " INSERT INTO pc_user ( user_name, descrip, bil_codes ) ";
					$ySql06b = $ySql06b . " VALUES ( ";
					$ySql06b = $ySql06b . " '" . trim($reg3og[email]) . "', ";
					$ySql06b = $ySql06b . " '" . trim($reg3og[unidadOrdenador]) . "', ";
					$ySql06b = $ySql06b . " 1 ";
					$ySql06b = $ySql06b . " ) ";
					//echo $ySql06b . "<br>";
					$yCursor06b = mysql_query($ySql06b);
					$queryLogMy= $queryLogMy . $ySql06b ;
					
					//echo " ySql06b =" . $ySql06b . "<br>" ;
					//Toma el último user_id generado por MySql para pc_user en la sesión activa (evita problemas de concurrencia)
					$ySql06c = " SELECT LAST_INSERT_ID() AS elIdUsuario FROM pc_user ";
					$yCursor06c = mysql_query($ySql06c);
					if($yReg06c = mysql_fetch_array($yCursor06c)){
						$yIdUsuarioOrdG = $yReg06c['elIdUsuario'];
					}
					//echo " ySql06c =" . $ySql06c . "<br>" ;
					
				}
	
				/*****************
				MySql:
				2. Asocia en la tabla pc_user_bcode el usuario y el codigo del gasto
				*****************/
				$ySql06d = " INSERT INTO pc_user_bcode ( user_id, bil_id ) ";
				$ySql06d = $ySql06d . " VALUES ( ";
				$ySql06d = $ySql06d . " " . $yIdUsuarioOrdG . ", ";
				$ySql06d = $ySql06d . " " . $idEnMySql . " ";
				$ySql06d = $ySql06d . " ) ";   
				$yCursor06d = mysql_query($ySql06d);		
				//echo " ySql06d =" . $ySql06d . "<br>" ;	
				$queryLogMy= $queryLogMy . $ySql06d ;
			}
		}
		
		//Hacer las grabaciones en AutorizadosImpresión del portal
		if ($pMailDirector != "") {
			//Autoriza en el Portal
			$sqlIn1Dir = " INSERT INTO HojaDeTiempo.dbo.AutorizadosImpresion ( id_proyecto, unidad, fechaAutoriza, estado, usuarioCrea, fechaCrea ) ";
			$sqlIn1Dir = $sqlIn1Dir . " VALUES ( ";
			$sqlIn1Dir = $sqlIn1Dir . " " . $miCodigoProyec . ", ";
			$sqlIn1Dir = $sqlIn1Dir . " " . $pUnidadDirector . ", ";
			$sqlIn1Dir = $sqlIn1Dir . " '" . date("m/d/Y H:i:s") . "', ";
			$sqlIn1Dir = $sqlIn1Dir . " 'A', ";
			$sqlIn1Dir = $sqlIn1Dir . " " . $_SESSION["sesUnidadUsuario"] . ", ";
			$sqlIn1Dir = $sqlIn1Dir . " '" . date("m/d/Y H:i:s") . "' ";
			$sqlIn1Dir = $sqlIn1Dir . " ) ";
			$cursorIn1Dir = mssql_query($sqlIn1Dir);
//			echo " sqlIn1Dir =" . $sqlIn1Dir . "<br>" ;
			$queryLog= $queryLog . $sqlIn1Dir ;
			
			//Inserto todos los cargos por cada persona
			$sqlAllCargos= "Select * from GestiondeInformacionDigital.dbo.CargosSolCodigo " ;
			$sqlAllCargos= $sqlAllCargos . " WHERE secuencia = " . $pSolicitudNo;
			$cursorAllCargos = mssql_query($sqlAllCargos);
			while ($regAllCargos=mssql_fetch_array($cursorAllCargos)) {
				$existeCargoDir = 0;
				//Verifica si el cargo ya existe  grabado para esa persona
				$sqlHayCargo="SELECT COUNT(*) existeCargo ";
				$sqlHayCargo=$sqlHayCargo." FROM HojaDeTiempo.dbo.AutorizadosImpresionCargos ";
				$sqlHayCargo=$sqlHayCargo."  where id_proyecto = " . $miCodigoProyec ;
				$sqlHayCargo=$sqlHayCargo."  and unidad =" . $pUnidadDirector . " ";
				$sqlHayCargo=$sqlHayCargo." and (cargo_defecto = '".$regAllCargos['cargo']."' or cargos_adicionales = '".$regAllCargos['cargo']."')";
				$cursorHayCargo = mssql_query($sqlHayCargo);
				if($regHayCargo = mssql_fetch_array($cursorHayCargo)){
					$existeCargoDir = $regHayCargo['existeCargo'];
				}
//				echo " sqlHayCargo =" . $sqlHayCargo . "<br>" ;

				if ($existeCargoDir == 0) {
					//Busca siguiente secuencia
					$sqlIn2Dir = " SELECT COALESCE(MAX(consecutivo), 0) AS elId FROM HojaDeTiempo.dbo.AutorizadosImpresionCargos ";
					$sqlIn2Dir = $sqlIn2Dir . " WHERE id_proyecto = " . $miCodigoProyec . " ";
					$sqlIn2Dir = $sqlIn2Dir . " AND unidad = " . $pUnidadDirector . " ";
					$cursorIn2Dir = mssql_query($sqlIn2Dir);
					if($regIn2Dir = mssql_fetch_array($cursorIn2Dir)){
						$elIdDir = $regIn2Dir['elId'] + 1;
					}
					//echo " sqlIn2Dir =" . $sqlIn2Dir . "<br>" ;
					
					//Inserta el cago
					$sqlIn3Dir = " INSERT INTO HojaDeTiempo.dbo.AutorizadosImpresionCargos ( id_proyecto, unidad, consecutivo, 
					cargo_defecto, cargos_adicionales, usuarioCrea, fechaCrea ) ";
					$sqlIn3Dir = $sqlIn3Dir . " VALUES ( ";
					$sqlIn3Dir = $sqlIn3Dir . " " . $miCodigoProyec . ", ";
					$sqlIn3Dir = $sqlIn3Dir . " " . $pUnidadDirector . ", ";
					$sqlIn3Dir = $sqlIn3Dir . " " . $elIdDir . ", ";
					if( $regAllCargos['cargoDefecto'] == 1){
						$sqlIn3Dir = $sqlIn3Dir . " '" . $regAllCargos['cargo'] . "', ";
						$sqlIn3Dir = $sqlIn3Dir . " NULL, ";
					} else if($regAllCargos['cargoDefecto'] == 0){
						$sqlIn3Dir = $sqlIn3Dir . " NULL, ";
						$sqlIn3Dir = $sqlIn3Dir . " '" . $regAllCargos['cargo'] . "', ";
					}
					$sqlIn3Dir = $sqlIn3Dir . " " . $_SESSION["sesUnidadUsuario"] . ", ";
					$sqlIn3Dir = $sqlIn3Dir . " '" . date("m/d/Y H:i:s") . "' ";
					$sqlIn3Dir = $sqlIn3Dir . " ) ";
					$cursorIn3Dir = mssql_query($sqlIn3Dir);	
//					echo " sqlIn3Dir =" . $sqlIn3Dir . "<br>" ;			
					$queryLog= $queryLog . $sqlIn3Dir ;		
				}		
			} //Si el cargo no existe
		}
		
		if ($pMailCoordina != "") {
			//Autoriza en el Portal
			$sqlIn1Coo = " INSERT INTO HojaDeTiempo.dbo.AutorizadosImpresion ( id_proyecto, unidad, fechaAutoriza, estado, usuarioCrea, fechaCrea ) ";
			$sqlIn1Coo = $sqlIn1Coo . " VALUES ( ";
			$sqlIn1Coo = $sqlIn1Coo . " " . $miCodigoProyec . ", ";
			$sqlIn1Coo = $sqlIn1Coo . " " . $pUnidadCoordina . ", ";
			$sqlIn1Coo = $sqlIn1Coo . " '" . date("m/d/Y H:i:s") . "', ";
			$sqlIn1Coo = $sqlIn1Coo . " 'A', ";
			$sqlIn1Coo = $sqlIn1Coo . " " . $_SESSION["sesUnidadUsuario"] . ", ";
			$sqlIn1Coo = $sqlIn1Coo . " '" . date("m/d/Y H:i:s") . "' ";
			$sqlIn1Coo = $sqlIn1Coo . " ) ";
			$cursorIn1Coo = mssql_query($sqlIn1Coo);
			$queryLog= $queryLog . $sqlIn1Coo ;		
			
			//Inserto todos los cargos por cada persona
			$sqlAllCargos= "Select * from GestiondeInformacionDigital.dbo.CargosSolCodigo " ;
			$sqlAllCargos= $sqlAllCargos . " WHERE secuencia = " . $pSolicitudNo;
			$cursorAllCargos = mssql_query($sqlAllCargos);
			while ($regAllCargos=mssql_fetch_array($cursorAllCargos)) {
			
				$existeCargoCoo = 0;
				//Verifica si el cargo ya existe  grabado para esa persona
				$sqlHayCargo="SELECT COUNT(*) existeCargo ";
				$sqlHayCargo=$sqlHayCargo." FROM HojaDeTiempo.dbo.AutorizadosImpresionCargos ";
				$sqlHayCargo=$sqlHayCargo."  where id_proyecto = " . $miCodigoProyec ;
				$sqlHayCargo=$sqlHayCargo."  and unidad =" . $pUnidadCoordina . " ";
				$sqlHayCargo=$sqlHayCargo." and (cargo_defecto = '".$regAllCargos['cargo']."' or cargos_adicionales = '".$regAllCargos['cargo']."')";
				$cursorHayCargo = mssql_query($sqlHayCargo);
				if($regHayCargo = mssql_fetch_array($cursorHayCargo)){
					$existeCargoCoo = $regHayCargo['existeCargo'];
				}

				if ($existeCargoCoo == 0) {			
			
					//Busca siguiente secuencia
					$sqlIn2Coo = " SELECT COALESCE(MAX(consecutivo), 0) AS elId FROM HojaDeTiempo.dbo.AutorizadosImpresionCargos ";
					$sqlIn2Coo = $sqlIn2Coo . " WHERE id_proyecto = " . $miCodigoProyec . " ";
					$sqlIn2Coo = $sqlIn2Coo . " AND unidad = " . $pUnidadCoordina . " ";
					$cursorIn2Coo = mssql_query($sqlIn2Coo);
					if($regIn2Coo = mssql_fetch_array($cursorIn2Coo)){
						$elIdCoo = $regIn2Coo['elId'] + 1;
					}
					
					//Inserta el cago
					$sqlIn3Coo = " INSERT INTO HojaDeTiempo.dbo.AutorizadosImpresionCargos ( id_proyecto, unidad, consecutivo, 
					cargo_defecto, cargos_adicionales, usuarioCrea, fechaCrea ) ";
					$sqlIn3Coo = $sqlIn3Coo . " VALUES ( ";
					$sqlIn3Coo = $sqlIn3Coo . " " . $miCodigoProyec . ", ";
					$sqlIn3Coo = $sqlIn3Coo . " " . $pUnidadCoordina . ", ";
					$sqlIn3Coo = $sqlIn3Coo . " " . $elIdCoo . ", ";
					if( $regAllCargos['cargoDefecto'] == 1){
						$sqlIn3Coo = $sqlIn3Coo . " '" . $regAllCargos['cargo'] . "', ";
						$sqlIn3Coo = $sqlIn3Coo . " NULL, ";
					} else if($regAllCargos['cargoDefecto'] == 0){
						$sqlIn3Coo = $sqlIn3Coo . " NULL, ";
						$sqlIn3Coo = $sqlIn3Coo . " '" . $regAllCargos['cargo'] . "', ";
					}
					$sqlIn3Coo = $sqlIn3Coo . " " . $_SESSION["sesUnidadUsuario"] . ", ";
					$sqlIn3Coo = $sqlIn3Coo . " '" . date("m/d/Y H:i:s") . "' ";
					$sqlIn3Coo = $sqlIn3Coo . " ) ";
					$cursorIn3Coo = mssql_query($sqlIn3Coo);				
					$queryLog= $queryLog . $sqlIn3Coo ;				
				}		
			} //Si existe el cargo
		}
		
		if ($pMailProg1 != "") {
			$sqlIn1P1 = " INSERT INTO HojaDeTiempo.dbo.AutorizadosImpresion ( id_proyecto, unidad, fechaAutoriza, estado, usuarioCrea, fechaCrea ) ";
			$sqlIn1P1 = $sqlIn1P1 . " VALUES ( ";
			$sqlIn1P1 = $sqlIn1P1 . " " . $miCodigoProyec . ", ";
			$sqlIn1P1 = $sqlIn1P1 . " " . $pUniProg1 . ", ";
			$sqlIn1P1 = $sqlIn1P1 . " '" . date("m/d/Y H:i:s") . "', ";
			$sqlIn1P1 = $sqlIn1P1 . " 'A', ";
			$sqlIn1P1 = $sqlIn1P1 . " " . $_SESSION["sesUnidadUsuario"] . ", ";
			$sqlIn1P1 = $sqlIn1P1 . " '" . date("m/d/Y H:i:s") . "' ";
			$sqlIn1P1 = $sqlIn1P1 . " ) ";
			$cursorIn1P1 = mssql_query($sqlIn1P1);
			$queryLog= $queryLog . $sqlIn1P1 ;				
			
			//Inserto todos los cargos por cada persona
			$sqlAllCargos= "Select * from GestiondeInformacionDigital.dbo.CargosSolCodigo " ;
			$sqlAllCargos= $sqlAllCargos . " WHERE secuencia = " . $pSolicitudNo;
			$cursorAllCargos = mssql_query($sqlAllCargos);
			while ($regAllCargos=mssql_fetch_array($cursorAllCargos)) {
			
				$existeCargoP1 = 0;
				//Verifica si el cargo ya existe  grabado para esa persona
				$sqlHayCargo="SELECT COUNT(*) existeCargo ";
				$sqlHayCargo=$sqlHayCargo." FROM HojaDeTiempo.dbo.AutorizadosImpresionCargos ";
				$sqlHayCargo=$sqlHayCargo."  where id_proyecto = " . $miCodigoProyec ;
				$sqlHayCargo=$sqlHayCargo."  and unidad =" . $pUniProg1 . " ";
				$sqlHayCargo=$sqlHayCargo." and (cargo_defecto = '".$regAllCargos['cargo']."' or cargos_adicionales = '".$regAllCargos['cargo']."')";
				$cursorHayCargo = mssql_query($sqlHayCargo);
				if($regHayCargo = mssql_fetch_array($cursorHayCargo)){
					$existeCargoP1 = $regHayCargo['existeCargo'];
				}

				if ($existeCargoP1 == 0) {			
					//Busca siguiente secuencia
					$sqlIn2P1 = " SELECT COALESCE(MAX(consecutivo), 0) AS elId FROM HojaDeTiempo.dbo.AutorizadosImpresionCargos ";
					$sqlIn2P1 = $sqlIn2P1 . " WHERE id_proyecto = " . $miCodigoProyec . " ";
					$sqlIn2P1 = $sqlIn2P1 . " AND unidad = " . $pUniProg1 . " ";
					$cursorIn2P1 = mssql_query($sqlIn2P1);
					if($regIn2P1 = mssql_fetch_array($cursorIn2P1)){
						$elIdP1 = $regIn2P1['elId'] + 1;
					}
					
					//Inserta el cago
					$sqlIn3P1 = " INSERT INTO HojaDeTiempo.dbo.AutorizadosImpresionCargos ( id_proyecto, unidad, consecutivo, 
					cargo_defecto, cargos_adicionales, usuarioCrea, fechaCrea ) ";
					$sqlIn3P1 = $sqlIn3P1 . " VALUES ( ";
					$sqlIn3P1 = $sqlIn3P1 . " " . $miCodigoProyec . ", ";
					$sqlIn3P1 = $sqlIn3P1 . " " . $pUniProg1 . ", ";
					$sqlIn3P1 = $sqlIn3P1 . " " . $elIdP1 . ", ";
					if( $regAllCargos['cargoDefecto'] == 1){
						$sqlIn3P1 = $sqlIn3P1 . " '" . $regAllCargos['cargo'] . "', ";
						$sqlIn3P1 = $sqlIn3P1 . " NULL, ";
					} else if($regAllCargos['cargoDefecto'] == 0){
						$sqlIn3P1 = $sqlIn3P1 . " NULL, ";
						$sqlIn3P1 = $sqlIn3P1 . " '" . $regAllCargos['cargo'] . "', ";
					}
					$sqlIn3P1 = $sqlIn3P1 . " " . $_SESSION["sesUnidadUsuario"] . ", ";
					$sqlIn3P1 = $sqlIn3P1 . " '" . date("m/d/Y H:i:s") . "' ";
					$sqlIn3P1 = $sqlIn3P1 . " ) ";
					$cursorIn3P1 = mssql_query($sqlIn3P1);						
					$queryLog= $queryLog . $sqlIn3P1 ;
				}		
			}	//Si existe el cargo
		}
		
		if ($pMailProg2 != "") {
			$sqlIn1P2 = " INSERT INTO HojaDeTiempo.dbo.AutorizadosImpresion ( id_proyecto, unidad, fechaAutoriza, estado, usuarioCrea, fechaCrea ) ";
			$sqlIn1P2 = $sqlIn1P2 . " VALUES ( ";
			$sqlIn1P2 = $sqlIn1P2 . " " . $miCodigoProyec . ", ";
			$sqlIn1P2 = $sqlIn1P2 . " " . $pUniProg2 . ", ";
			$sqlIn1P2 = $sqlIn1P2 . " '" . date("m/d/Y H:i:s") . "', ";
			$sqlIn1P2 = $sqlIn1P2 . " 'A', ";
			$sqlIn1P2 = $sqlIn1P2 . " " . $_SESSION["sesUnidadUsuario"] . ", ";
			$sqlIn1P2 = $sqlIn1P2 . " '" . date("m/d/Y H:i:s") . "' ";
			$sqlIn1P2 = $sqlIn1P2 . " ) ";
			$cursorIn1P2 = mssql_query($sqlIn1P2);
			$queryLog= $queryLog . $sqlIn1P2 ;
			
			//Inserto todos los cargos por cada persona
			$sqlAllCargos= "Select * from GestiondeInformacionDigital.dbo.CargosSolCodigo " ;
			$sqlAllCargos= $sqlAllCargos . " WHERE secuencia = " . $pSolicitudNo;
			$cursorAllCargos = mssql_query($sqlAllCargos);
			while ($regAllCargos=mssql_fetch_array($cursorAllCargos)) {
				$existeCargoP2 = 0;
				//Verifica si el cargo ya existe  grabado para esa persona
				$sqlHayCargo="SELECT COUNT(*) existeCargo ";
				$sqlHayCargo=$sqlHayCargo." FROM HojaDeTiempo.dbo.AutorizadosImpresionCargos ";
				$sqlHayCargo=$sqlHayCargo."  where id_proyecto = " . $miCodigoProyec ;
				$sqlHayCargo=$sqlHayCargo."  and unidad =" . $pUniProg2 . " ";
				$sqlHayCargo=$sqlHayCargo." and (cargo_defecto = '".$regAllCargos['cargo']."' or cargos_adicionales = '".$regAllCargos['cargo']."')";
				$cursorHayCargo = mssql_query($sqlHayCargo);
				if($regHayCargo = mssql_fetch_array($cursorHayCargo)){
					$existeCargoP2 = $regHayCargo['existeCargo'];
				}

				if ($existeCargoP2 == 0) {			
					//Busca siguiente secuencia
					$sqlIn2P2 = " SELECT COALESCE(MAX(consecutivo), 0) AS elId FROM HojaDeTiempo.dbo.AutorizadosImpresionCargos ";
					$sqlIn2P2 = $sqlIn2P2 . " WHERE id_proyecto = " . $miCodigoProyec . " ";
					$sqlIn2P2 = $sqlIn2P2 . " AND unidad = " . $pUniProg2 . " ";
					$cursorIn2P2 = mssql_query($sqlIn2P2);
					if($regIn2P2 = mssql_fetch_array($cursorIn2P2)){
						$elIdP2 = $regIn2P2['elId'] + 1;
					}
					
					//Inserta el cago
					$sqlIn3P2 = " INSERT INTO HojaDeTiempo.dbo.AutorizadosImpresionCargos ( id_proyecto, unidad, consecutivo, 
					cargo_defecto, cargos_adicionales, usuarioCrea, fechaCrea ) ";
					$sqlIn3P2 = $sqlIn3P2 . " VALUES ( ";
					$sqlIn3P2 = $sqlIn3P2 . " " . $miCodigoProyec . ", ";
					$sqlIn3P2 = $sqlIn3P2 . " " . $pUniProg2 . ", ";
					$sqlIn3P2 = $sqlIn3P2 . " " . $elIdP2 . ", ";
					if( $regAllCargos['cargoDefecto'] == 1){
						$sqlIn3P2 = $sqlIn3P2 . " '" . $regAllCargos['cargo'] . "', ";
						$sqlIn3P2 = $sqlIn3P2 . " NULL, ";
					} else if($regAllCargos['cargoDefecto'] == 0){
						$sqlIn3P2 = $sqlIn3P2 . " NULL, ";
						$sqlIn3P2 = $sqlIn3P2 . " '" . $regAllCargos['cargo'] . "', ";
					}
					$sqlIn3P2 = $sqlIn3P2 . " " . $_SESSION["sesUnidadUsuario"] . ", ";
					$sqlIn3P2 = $sqlIn3P2 . " '" . date("m/d/Y H:i:s") . "' ";
					$sqlIn3P2 = $sqlIn3P2 . " ) ";
					$cursorIn3P2 = mssql_query($sqlIn3P2);			
					$queryLog= $queryLog . $sqlIn3P2 ;			
				}			
			} //Si existe el cargo
		}
		
		$cursor3og = mssql_query($sql3);
		while ($reg3og=mssql_fetch_array($cursor3og)) {
			$sqlIn1OG = " INSERT INTO HojaDeTiempo.dbo.AutorizadosImpresion ( id_proyecto, unidad, fechaAutoriza, estado, usuarioCrea, fechaCrea ) ";
			$sqlIn1OG = $sqlIn1OG . " VALUES ( ";
			$sqlIn1OG = $sqlIn1OG . " " . $miCodigoProyec . ", ";
			$sqlIn1OG = $sqlIn1OG . " " . trim($reg3og[unidadOrdenador]) . ", ";
			$sqlIn1OG = $sqlIn1OG . " '" . date("m/d/Y H:i:s") . "', ";
			$sqlIn1OG = $sqlIn1OG . " 'A', ";
			$sqlIn1OG = $sqlIn1OG . " " . $_SESSION["sesUnidadUsuario"] . ", ";
			$sqlIn1OG = $sqlIn1OG . " '" . date("m/d/Y H:i:s") . "' ";
			$sqlIn1OG = $sqlIn1OG . " ) ";
			$cursorIn1OG = mssql_query($sqlIn1OG);
			$queryLog= $queryLog . $sqlIn1OG ;			
			
			//Inserto todos los cargos por cada persona
			$sqlAllCargos= "Select * from GestiondeInformacionDigital.dbo.CargosSolCodigo " ;
			$sqlAllCargos= $sqlAllCargos . " WHERE secuencia = " . $pSolicitudNo;
			$cursorAllCargos = mssql_query($sqlAllCargos);
			while ($regAllCargos=mssql_fetch_array($cursorAllCargos)) {
			
				$existeCargoOG = 0;
				//Verifica si el cargo ya existe  grabado para esa persona
				$sqlHayCargo="SELECT COUNT(*) existeCargo ";
				$sqlHayCargo=$sqlHayCargo." FROM HojaDeTiempo.dbo.AutorizadosImpresionCargos ";
				$sqlHayCargo=$sqlHayCargo."  where id_proyecto = " . $miCodigoProyec ;
				$sqlHayCargo=$sqlHayCargo."  and unidad =" . trim($reg3og[unidadOrdenador]) . " ";
				$sqlHayCargo=$sqlHayCargo." and (cargo_defecto = '".$regAllCargos['cargo']."' or cargos_adicionales = '".$regAllCargos['cargo']."')";
				$cursorHayCargo = mssql_query($sqlHayCargo);
				if($regHayCargo = mssql_fetch_array($cursorHayCargo)){
					$existeCargoOG = $regHayCargo['existeCargo'];
				}

				if ($existeCargoOG == 0) {			
					//Busca siguiente secuencia
					$sqlIn2OG = " SELECT COALESCE(MAX(consecutivo), 0) AS elId FROM HojaDeTiempo.dbo.AutorizadosImpresionCargos ";
					$sqlIn2OG = $sqlIn2OG . " WHERE id_proyecto = " . $miCodigoProyec . " ";
					$sqlIn2OG = $sqlIn2OG . " AND unidad = " . trim($reg3og[unidadOrdenador]) . " ";
					$cursorIn2OG = mssql_query($sqlIn2OG);
					if($regIn2OG = mssql_fetch_array($cursorIn2OG)){
						$elIdOG = $regIn2OG['elId'] + 1;
					}
					
					//Inserta el cago
					$sqlIn3OG = " INSERT INTO HojaDeTiempo.dbo.AutorizadosImpresionCargos ( id_proyecto, unidad, consecutivo, 
					cargo_defecto, cargos_adicionales, usuarioCrea, fechaCrea ) ";
					$sqlIn3OG = $sqlIn3OG . " VALUES ( ";
					$sqlIn3OG = $sqlIn3OG . " " . $miCodigoProyec . ", ";
					$sqlIn3OG = $sqlIn3OG . " " . trim($reg3og[unidadOrdenador]) . ", ";
					$sqlIn3OG = $sqlIn3OG . " " . $elIdOG . ", ";
					if( $regAllCargos['cargoDefecto'] == 1){
						$sqlIn3OG = $sqlIn3OG . " '" . $regAllCargos['cargo'] . "', ";
						$sqlIn3OG = $sqlIn3OG . " NULL, ";
					} else if($regAllCargos['cargoDefecto'] == 0){
						$sqlIn3OG = $sqlIn3OG . " NULL, ";
						$sqlIn3OG = $sqlIn3OG . " '" . $regAllCargos['cargo'] . "', ";
					}
					$sqlIn3OG = $sqlIn3OG . " " . $_SESSION["sesUnidadUsuario"] . ", ";
					$sqlIn3OG = $sqlIn3OG . " '" . date("m/d/Y H:i:s") . "' ";
					$sqlIn3OG = $sqlIn3OG . " ) ";
					$cursorIn3OG = mssql_query($sqlIn3OG);				
					$queryLog= $queryLog . $sqlIn3OG ;					
				}								
			} //Si existe el cargo
		}

		/*
		Almacenamiento en el Log de Auditoría
		Para facilitar la grabación, dentro del query del Log se suprimen las comillas simples
		*/
		$sqlInLog = " INSERT INTO HojaDeTiempo.dbo.AutorizadosImpresionLog ( id_proyecto, qryLog, qryLogPD, usuarioCrea, fechaCrea ) ";
		$sqlInLog = $sqlInLog . " VALUES ( ";
		$sqlInLog = $sqlInLog . " " . $miCodigoProyec . ", ";
		$sqlInLog = $sqlInLog . "  '" . ereg_replace("'", "", $queryLog) . "', ";
		$sqlInLog = $sqlInLog . "  '" . ereg_replace("'", "", $queryLogMy) . "', ";
		$sqlInLog = $sqlInLog . " " . $_SESSION["sesUnidadUsuario"] . ", ";
		$sqlInLog = $sqlInLog . " '" . date("m/d/Y H:i:s") . "' ";
		$sqlInLog = $sqlInLog . " ) ";
		$cursorInLog = mssql_query($sqlInLog);
		
		//***PBM - 26Oct2010 Cierra
		
//		echo $sqlHT ;
//		$cursorHT = mssql_query($sqlHT) ;
		//Si los cursores no presentaron problema
		if  ((trim($cursor) != "") AND (trim($cursorHT) != ""))  {
			echo ("<script>alert('La Asignación se realizó con éxito.');</script>");
		} 
		else {
			echo ("<script>alert('Error durante la grabación');</script>");
		};
		

/*

//22/05/2017 odof se realiza la invocación de las consultas ya que no se estaba llevando el codigo de proyecto 
*/		

include('funciones/integracion.php');
	certificadoConfact($cualSec);
	proyectoConFact($cualSec);
	WSSeven($cualSec);
	WSSevenAN($cualSec);
	Experiencia($cualSec);		
	ExperienciaResponsables($miCodigoProyec);
/*

//23Nov2007
//Se puso en comentario este bloque para que no se creen los mails del proyecto automáticamente.
//A partir de este momento la creación del mail va a ser manual a través de GRM o PBM

		//Enviar los correos a Sistemas para la creación de los mail del proyecto
		$miCorreoEnv = "enviados" . trim($pCodigoDef) . trim ($pCargoDef) .  trim($miCodigoProyec)  ;
		$miPassEnv = "passenv" . trim($pCodigoDef) . trim ($pCargoDef) .  trim($miCodigoProyec)  ;
		$miCorreoRec = "recibidos" . trim($pCodigoDef) . trim ($pCargoDef) .  trim($miCodigoProyec)  ;
		$miPassRec = "passrec" . trim($pCodigoDef) . trim ($pCargoDef) .  trim($miCodigoProyec) ;
		
		$cualMailProy="mhtiempo@ingetec.com.co";
		$AsuntoProy="Novedades de proyectos";
		$DescripcionProy="Proceso=Agregar
		Unidad=$miPassEnv
		Nombres=$cualNombre
		Apellidos=
		Email=$miCorreoEnv
		No responda este correo, se ha enviado automaticamente por
		motivo de creacion del proyecto
		
		Sistema Portal";
		
		mail($cualMailProy,$AsuntoProy,$DescripcionProy,"FROM: Sistema Portal <portal@ingetec.com.co>\n"); 
		
		$DescripcionProy="Proceso=Agregar
		Unidad=$miPassRec
		Nombres=$cualNombre
		Apellidos=
		Email=$miCorreoRec
		No responda este correo, se ha enviado automaticamente por
		motivo de creacion del proyecto
		
		Sistema Portal";
		mail($cualMailProy,$AsuntoProy,$DescripcionProy,"FROM: Sistema Portal <portal@ingetec.com.co>\n"); 
*/		

	}
//			mssql_query("ROLLBACK TRANSACTION");
	echo ("<script>window.close();MM_openBrWindow('solCodigo3.php','winSolCod','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

}




?>
<html>
<head>
<script>
var newwindow;
function muestraventana(url)
{
	newwindow=window.open(url,"name","height=400,width=650, resizable=yes, scrollbars=yes");
	if (window.focus) {newwindow.focus()}
}
function muestraventana2(url)
{
	newwindow=window.open(url,"name2","height=400,width=650, resizable=0, scrollbars=0");
	if (window.focus) {newwindow.focus()}
}
</script>
<title>Solicitud de C&oacute;digo</title>
<LINK REL="stylesheet" HREF="../css/estilo.css" TYPE="text/css">
<script language="JavaScript">
window.name="winSolCod";
</script>
<SCRIPT language=JavaScript>
<!--
function mOvr(src,clrOver) {
    if (!src.contains(event.fromElement)) {
	  src.style.cursor = 'hand';
	  src.bgColor = clrOver;
	}
  }
  function mOut(src,clrIn) {
	if (!src.contains(event.toElement)) {
	  src.style.cursor = 'default';
	  src.bgColor = clrIn;
	}
  }
  function mClk(src) {
    if(event.srcElement.tagName=='TD'){
	  src.children.tags('A')[0].click();
    }
  }

//-->
</SCRIPT>
<script language="JavaScript" type="text/JavaScript">
<!--

function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Solicitud de C&oacute;digo / Subcodigo</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<?  while ($reg=mssql_fetch_array($cursor)) {  ?>	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td width="12%" class="TituloTabla">Solicitud No </td>
        <td width="22%" class="TxtTabla"><? echo $reg[secuencia]; ?></td>
        <td width="12%" class="TituloTabla">Fecha</td>
        <td width="22%" class="TxtTabla"><? echo date("M d Y ", strtotime($reg[fechaSolicitud])); ?></td>
        <td width="12%" class="TituloTabla">Usuario</td>
        <td class="TxtTabla">
		<?
		$miUsuario = "";
		//Consulta para traer el nombre del jefe que autoriza
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidad]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miMailUsuario = $reg0[email] ;
			$miUsuario = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
		<? echo ucwords(strtolower($miUsuario)); ?>
		</td>
      </tr>
      <tr>
        <td width="12%" class="TituloTabla">Dependencia</td>
        <td width="22%" class="TxtTabla">
		<?

		$miDepto = "";
		$miDivision = "";
		$miDependencia = "";

		//Consulta para traer Dependencia, división, departamento
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql2="select u.unidad, u.nombre, u.apellidos, d.nombre as departamento, d.id_division,  ";
		$sql2= $sql2. " v.nombre as division, v.id_dependencia, x.nombre as dependencia ";
		$sql2= $sql2. " from usuarios u, departamentos d, divisiones v, dependencias x ";
		$sql2= $sql2. " where u.unidad =" . $reg[unidad]; 
		$sql2= $sql2. " and u.id_departamento = d.id_departamento";
		$sql2= $sql2. " and d.id_division = v.id_division ";
		$sql2= $sql2. " and v.id_dependencia = x.id_dependencia ";
		$cursor2 = mssql_query($sql2);
		if ($reg2=mssql_fetch_array($cursor2)) {
			$miDepto = $reg2[departamento];
			$miDivision = $reg2[division];
			$miDependencia = $reg2[dependencia];
		}
		?>
		<? echo ucwords(strtolower($miDependencia)); ?>	
		<? 
		  $estadoVU = $reg[validaUsuario];
		  //**Para habilitar los botones de autorización
  		  $reqUnidadJefe = $reg[unidadJefe]; 
		  $apruebaJefe = $reg[validaJefe];
		  $apruebaContratos = $reg[validaContratos];
		  $mObserva = $reg[comentaContratos];
		  //**
		
		//echo $reg[validaUsuario]; ?>	</td>
        <td width="12%" class="TituloTabla">Divisi&oacute;n</td>
        <td width="22%" class="TxtTabla">
		<? echo ucwords(strtolower($miDivision)); ?>
		</td>
        <td width="12%" class="TituloTabla">Departamento</td>
        <td class="TxtTabla">
		<? echo ucwords(strtolower($miDepto)); ?>
		</td>
      </tr>
    </table>
	<? if ($reg[esSubcodigo]==1)
		{ $msnSol="Subcódigo"; ; }
		else
		{ $msnSol="Código";
		}?>
		
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td width="12%" class="TituloTabla">Solicitud de:</td>
        <td class="TxtTabla"><? echo $msnSol; ?> </td>      	
        </tr>
		<? if ($reg[esSubcodigo]==1) {?>
      <tr>
        <td width="12%" class="TituloTabla">Subc&oacute;digo generado a partir del proyecto: </td>
        <td class="TxtTabla">
		<?
		$sqlSubCP="select * from HojaDeTiempo.dbo.Proyectos ";
		$sqlSubCP=$sqlSubCP." where id_proyecto =" . $reg[id_proyectoPadre];
		$cursorsqlSubCP = mssql_query($sqlSubCP);
		if ($regsqlSubCP=mssql_fetch_array($cursorsqlSubCP)) {
			echo "[" . $regsqlSubCP[codigo] . "." . $regsqlSubCP[cargo_defecto] . "] " . $regsqlSubCP[nombre];
		}

		?>
		</td>
      </tr>
	  <? }?>
    </table>
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td><img src="../images/Pixel.gif" width="4" height="4"></td>
            </tr>
      </table>
		  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td class="TituloTabla">&iquest;Solicitud enviada a jefe? </td>
        <td width="34%" class="TxtTabla">
		<? 
			$selSi = "";
			$selNo = "";
		
			if ($reg[enviaAJefe] == "1") {
				$selSi = "checked";
				$selNo = "";
			} 
			if ($reg[enviaAJefe] == "0") {
				$selSi = "";
				$selNo = "checked";
			} 
			$pEnviaJefe = $reg[enviaAJefe];
			
		?>
		<input name="radiobutton3" type="radio" value="radiobutton" <? echo $selSi; ?> disabled>
          Si&nbsp;&nbsp;
          <input name="radiobutton3" type="radio" value="radiobutton" <? echo $selNo; ?> disabled>
          No		</td>
        <td width="22%" class="TituloTabla">Observaciones del usuario</td>
        <td class="TxtTabla">
		<? echo $reg[observaUsu]; ?>		</td>
      </tr>
      <tr>
        <td width="12%" class="TituloTabla">&iquest;Solicitud autorizada? </td>
        <td class="TxtTabla"><? 
			$selSi = "";
			$selNo = "";
		
			if ($reg[validaJefe] == "1") {
				$selSi = "checked";
				$selNo = "";
			} 
			if ($reg[validaJefe] == "0") {
				$selSi = "";
				$selNo = "checked";
			} 
			
		?>
		<input name="radiobutton2" type="radio" value="radiobutton" <? echo $selSi; ?> disabled>
          Si&nbsp;&nbsp;
          <input name="radiobutton2" type="radio" value="radiobutton" <? echo $selNo; ?> disabled>
          No</td>
        <td class="TituloTabla">Solicitud enviada a encargado de autorizar?</td>
        <td class="TxtTabla"><? 
			$selSi = "";
			$selNo = "";
		
			if ($reg[validaContratosPre] == "1") {
				$selSi = "checked";
				$selNo = "";
			} 
			if ($reg[validaContratosPre] == "0") {
				$selSi = "";
				$selNo = "checked";
			} 
			
			
		?>
          <input name="radiobutton" type="radio" value="radiobutton" <? echo $selSi; ?> disabled>
Si &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input name="radiobutton" type="radio" value="radiobutton" <? echo $selNo; ?> disabled>
No
		</td>
      </tr>
      <tr>
        <td class="TituloTabla">Jefe que autoriza </td>
        <td class="TxtTabla">
		<?
		$miUsuarioJefe = "";
		//Consulta para traer el nombre del jefe que autoriza
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidadJefe]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miUsuarioJefe = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
		<? echo ucwords(strtolower($miUsuarioJefe)); ?>		</td>
        <td class="TituloTabla">Encargado de aprobar solicitud </td>
        <td class="TxtTabla"><?
		$miUsuarioAprueba = "";
		//Consulta para traer el nombre del jefe que autoriza
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidadContratos]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miUsuarioAprueba = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
          <? echo ucwords(strtolower($miUsuarioAprueba)); ?></td>
      </tr>
      <tr>
        <td width="12%" class="TituloTabla">Comentario quien autoriza solicitud </td>
        <td colspan="3" class="TxtTabla"><? echo $reg[comentaJefe]; ?></td>
        </tr>
    </table>
		  
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TxtTabla">&nbsp;</td>
            </tr>
          </table>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TituloUsuario">Informaci&oacute;n sobre el trabajo por ejecutarse </td>
            </tr>
          </table>		  
		  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td width="30%" class="TituloTabla">Tipo de proyecto </td>
              <td class="TxtTabla"><? echo $reg[nomTipoProy]; ?></td>
            </tr>
            <tr>
              <td width="30%" class="TituloTabla">&iquest;Trabajo facturable? </td>
              <td class="TxtTabla">
		<? if ($reg[facturable] == "1") { 
				$selSI = "checked";
				$selNO = "";
			}
			else {
				$selSI = "";
				$selNO = "checked";
			}
		
		?>			  <input name="factura" type="radio" value="radiobutton" <? echo $selSI; ?> disabled >
                Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                <input name="factura" type="radio" value="radiobutton" <? echo $selNO; ?> disabled >
                No</td>
            </tr>
            <tr>
              <td width="30%" class="TituloTabla">Director</td>
              <td class="TxtTabla"><?
		$miUsuarioDirector = "";
		//Consulta para traer el nombre del director
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidadDirector]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$mimailDirector = $reg0[email] ;
			$miUsuarioDirector = $reg0[nombre] . " " . $reg0[apellidos];
			$miUnidadDirector = $reg0[unidad] ;
		}
		?>
		<? echo ucwords(strtolower($miUsuarioDirector)); ?>
		<?
		
		//Consulta para traer el mail del programdor 1
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidadProg1]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miMailProg1 = $reg0[email] ;
			$miuniProg1 = $reg0[unidad] ;
		}
		?>
		<?
		//Consulta para traer el mail del programdor 2
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidadProg2]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miMailProg2 = $reg0[email] ;
			$miuniProg2 = $reg0[unidad] ;
		}
		?>
		</td>
            </tr>
            <tr>
              <td class="TituloTabla">Coordinador</td>
              <td class="TxtTabla">
			  <?
		$miUsuarioCoordina = "";
		//Consulta para traer el nombre del coordinador
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidadCoordinador]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miUsuarioCoordina = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
		<? echo ucwords(strtolower($miUsuarioCoordina)); ?>
		<?
		//Consulta para traer el mail del Coordinador
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidadCoordinador]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miMailCoordina = $reg0[email] ;
			$miuniCoordina = $reg0[unidad] ;
		}
		?>
			  </td>
            </tr>
            <tr>
              <td width="30%" class="TituloTabla">Cliente</td>
              <td class="TxtTabla"><? 
			  $cualCliente = $reg[cliente];
			  echo $reg[cliente]; ?></td>
            </tr>
            <tr>
              <td width="30%" class="TituloTabla">Nombre Proyecto</td>
              <td class="TxtTabla"><? 
			  $cualNombre = $reg[nombreCompleto];
			  echo $reg[nombreCompleto]; ?></td>
            </tr>
			<tr>
              <td class="TituloTabla">Objeto</td>
              <td class="TxtTabla"><? echo $reg[objeto]; ?></td>
            </tr>
            <tr>
              <td class="TituloTabla">Repuesta N&ordm; </td>
              <td class="TxtTabla">
			  <? 
//			  if (trim($reg[consecutivo]) != "") {

				$miCadena = str_pad($MaxCE, 4 , '0', STR_PAD_LEFT);
			  	echo "ADC-C-". $miCadena; 
//			  }
			  ?>
			  </td>
            </tr>
            <tr>
              <td class="TituloTabla">Fecha Respuesta </td>
              <td class="TxtTabla">
			  <? 
//			  if (trim($reg[fechaValidaContratos]) != "") {
				echo date("M d Y "); 
//			  }
			  ?>
			  </td>
            </tr>
            <tr>
              <td colspan="2">
			  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="TituloUsuario">Estructura del c&oacute;digo del proyecto </td>
                </tr>
              </table>
                <table width="100%"  border="0" cellspacing="1" cellpadding="0">
                  <tr>
                    <td width="7%" class="TituloTabla">Empresa</td>
                    <td width="10%" class="TxtTabla"><? //echo $reg[idEmpresa]; ?>
					<?
					/*
					$erpSql01="SELECT * ";
					$erpSql01=$erpSql01." FROM HojaDeTiempo.dbo.Empresas ";
					$erpSql01=$erpSql01." WHERE idEmpresa = " . $reg[idEmpresa] ;
					$cursorerpSql01 = mssql_query($erpSql01);
					if ($regerpSql01=mssql_fetch_array($cursorerpSql01)) {
						echo $regerpSql01[erpIdSeven];
					}
					*/
					//07Jun2016
					//PBM
					//Se bloque código anterior para mostrar el código con 000
					echo "000";
					?>					</td>
                    <td width="7%" class="TituloTabla">Estado</td>
                    <td width="10%" class="TxtTabla"><? //echo $reg[idEstadoCP]; ?>
					<?
					/*
					$erpSql02="SELECT * ";
					$erpSql02=$erpSql02." FROM HojaDeTiempo.dbo.erpTBestadosCodigoProyecto ";
					$erpSql02=$erpSql02." WHERE idEstadoCP = " . $reg[idEstadoCP] ;
					$cursorerpSql02 = mssql_query($erpSql02);
					if ($regerpSql02=mssql_fetch_array($cursorerpSql02)) {
						echo $regerpSql02[idenEstadoCP];
					}
					*/
					//07Jun2016
					//PBM
					//Se bloquea código anterior para mostrar el estado con 0
					echo "0";					
					?>					</td>
                    <td width="7%" class="TituloTabla">C&oacute;digo</td>
                    <td width="10%" class="TxtTabla"><? echo $reg[erpCodigo]; ?></td>
                    <td width="7%" class="TituloTabla">Subc&oacute;digo</td>
                    <td width="10%" class="TxtTabla"><? echo $reg[erpSubCodigo]; ?></td>
                    <td width="7%" class="TituloTabla">Cargo</td>
                    <td class="TxtTabla">
					<table width="100%"  border="0" cellspacing="1" cellpadding="0">
                    <? while ($reg4=mssql_fetch_array($cursor4)) { ?>            
					<tr class="TxtTabla">
                      <td width="5%">
					  <? 
					  //echo $reg4[codigo] . "." . $reg4[cargo]; 
					  echo $reg4[cargo]; 
					  ?>
					  </td>
                      <td><? echo $reg4[observacion]; ?></td>
                    </tr>
					<? } ?>
                  </table>					</td>
                  </tr>
                </table>
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="17%" class="TituloTabla">Descripci&oacute;n del subc&oacute;digo </td>
                    <td class="TxtTabla"><? echo $reg[descSubcodigo]; ?></td>
                  </tr>
                </table>
				<? 
	  //24Oct2016
	  //PBM 
	  //Se desactiva esta parte de código porque Felipe Durán solicitó que no se requieren en adelante los códigos cortos. 
	  //Autorizado para hacer con urgencia GRM 24-Oct-2016
	  $desactivaSeccion01="NO";
	  if ($desactivaSeccion01=="SI") { 
	  ?>
				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TituloUsuario">Equivalencia c&oacute;digo y cargo corto </td>
        </tr>
      </table>
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td width="20%" class="TituloTabla">C&oacute;digo Corto </td>
          <td class="TxtTabla"><? echo $reg[codigoCorto]; ?></td>
        </tr>
        <tr>
          <td width="20%" class="TituloTabla">Cargo Corto </td>
          <td class="TxtTabla"><? echo $reg[cargoCorto]; ?></td>
        </tr>
      </table>
	  <? 
	  }
	  //Cierra 24Oct-2016 ?>
			  </td>
            </tr>
            
            
          </table>		  
		  <? if($reg[aplicaConsorcio]==1){?>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td class="TituloUsuario">Consorcios </td>
		  </tr>
		</table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0"><?
		 $sqlConsor="SELECT * FROM [GestiondeInformacionDigital].[dbo].[SolicitudCodigoConsorcios] where secuencia=".$cualSec;
							$cursorConsor = mssql_query($sqlConsor);
							$i=1;
							while ($regConsor=mssql_fetch_array($cursorConsor)) 
							{ $Nfirm = "pNomFirmas".$i;
								?>
								<tr>
								  <td width="30%" class="TituloTabla"> Firma <? echo $i; ?></td>
								  <td width="70%" class="TxtTabla"><? echo $regConsor[nombreFirma]; ?></td>
								</tr>
								<?	
								$i++;		 
							}//cierre del while
							?>
		</table>
		<? }?>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><img src="../images/Pixel.gif" width="4" height="4"></td>
            </tr>
          </table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Sistema de cobro al cliente (&Uacute;nicamente en trabajos facturables) </td>
  </tr>
</table>
		  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
           <? /*?>
		   <tr class="TxtTabla">
              <td width="25%">
			  <? if ($reg[sistemaCobro] == "1") { 
				$selCasilla1 = "checked";
			}
			else {
				$selCasilla1 = "";
			}
		
		?>			  <input name="Casilla1" type="checkbox" id="Casilla1" value="checkbox" <? echo $selCasilla1; ?> disabled>
              Sueldos Topes </td>
              <td width="25%">
			  <? if ($reg[sistemaCobro] == "2") { 
				$selCasilla2 = "checked";
			}
			else {
				$selCasilla2 = "";
			}
		
		?>			  <input name="Casilla2" type="checkbox" id="Casilla2" value="checkbox" <? echo $selCasilla2; ?> disabled>
              Tarifas</td>
              <td width="25%">
			  <? if ($reg[sistemaCobro] == "3") { 
				$selCasilla3 = "checked";
			}
			else {
				$selCasilla3 = "";
			}
		
		?>			  <input name="Casilla3" type="checkbox" id="Casilla3" value="checkbox" <? echo $selCasilla3; ?> disabled>
              Tarifas con multiplicador </td>
              <td width="25%">
			  <? if ($reg[sistemaCobro] == "4") { 
				$selCasilla4 = "checked";
			}
			else {
				$selCasilla4 = "";
			}
		
		?>			  <input name="Casilla4" type="checkbox" id="Casilla4" value="checkbox" <? echo $selCasilla4; ?> disabled>
              Contrato a precio fijo </td>
            </tr>
			<? */?>
			             <tr>
          <td width="20%" class="TituloTabla">Sistema de cobro al cliente </td> 
     </tr>
     <tr>     
          <td class="TxtTabla">
         <?
         $sqlTF11="Select *   FROM [BDcontratosFactP].[dbo].[TipoFact] where TFcodPortal=" . $reg[sistemaCobro] ;
	
	
	$cursorTF11 = mssql_query($sqlTF11);
	if ($regTF11=mssql_fetch_array($cursorTF11)) {
		  $pSistema = $regTF11[IdTipoFact];
	}

		 ?>
         
		  <select name="pSistema" class="CajaTexto" id="pSistema" onChange="envia12();" disabled >
            <?
			//Muestra el listado de las empresas
     
		$sqlTF="select * FROM   [BDcontratosFactP].[dbo].[TipoFact] where TFFactCodEstado=1  "  ;
		if($reg[facturable]==1)
		{
			 $sqlTF=$sqlTF." and TFesFacturable=1";
			}
		if($reg[facturable]==0 or $reg[codTipoProy]==3)
		{
			 $sqlTF=$sqlTF." and TFesFacturable=2";
			}
		
		$cursorTF = mssql_query($sqlTF);
		while ($regTF=mssql_fetch_array($cursorTF)) {
		$empresaTF=$regTF[IdTipoFact];
		if ( $pSistema == $regTF[IdTipoFact]) {
			$selTipoP = "Selected";
			}
		else {
			$selTipoP = "";
		}
		?>    

            <option value="<? echo $regTF[IdTipoFact]; ?>" <? echo $selTipoP ; ?> ><? echo strtoupper($regTF[TFDescripcion]) ;  ?></option>
            <? } ?>
          </select>
          
                
          </td>
        </tr>
          </table>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><img src="../images/Pixel.gif" width="4" height="4"></td>
            </tr>
          </table> 
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TituloUsuario">Informaci&oacute;n sobre el contrato (&Uacute;nicamente en trabajos facturables) </td>
            </tr>
          </table>		  
		  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td width="30%" class="TituloTabla">
			  <?
			  if ($reg[facturable] == "1") { 
			  	echo "Contrato N&ordm; ";
			  }
			  else {
			  	echo "Concurso N&ordm; ";
			  }
			  ?>
		      </td>
              <td class="TxtTabla"><? echo $reg[contratoNo]; ?></td>
            </tr>
            <tr>
              <td width="30%" class="TituloTabla">Plazo del contrato en meses </td>
              <td class="TxtTabla"><? $miPlazo = $reg[plazo]; ?><? echo $reg[plazo]; ?> meses </td>
            </tr>
            <tr>
              <td width="30%" class="TituloTabla">Valor del contrato sin IVA </td>
              <td class="TxtTabla">$ <? echo number_format($reg[contratoValor], 0);  ?></td>
            </tr>
          </table>		  
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><img src="../images/Pixel.gif" width="4" height="4"></td>
            </tr>
          </table>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TituloUsuario">Empresa</td>
            </tr>
          </table>
          <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td width="30%" class="TituloTabla">Empresa</td>
              <td class="TxtTabla">
			  <?
			  $miEmpresa = "" ;
			  $miSevenEmpresa = "" ;
			  //Encuentra el nombre de la empresa seleccionada
			  $sqlE="select * from HojaDeTiempo.dbo.Empresas ";
			  $sqlE=$sqlE." where idEmpresa = " . $reg[idEmpresa] ;
			  $cursorE = mssql_query($sqlE);
			  if ($regE=mssql_fetch_array($cursorE)) {
			  	$miEmpresa = $regE[nombre] ;
				$miIDEmpresa = $regE[idEmpresa] ;
				
				$miSevenEmpresa = $regE[erpIdSeven] ;
			  }

			  ?>
			  <? echo $miEmpresa ; ?>
			  </td>
            </tr>
            <tr>
              <td class="TituloTabla">Divisi&oacute;n</td>
              <td class="TxtTabla">
			  <?
			  $miDivision = "" ;
			  $miIDDivision = $reg[id_division] ;
			  //Encuentra el nombre de la división seleccionada
			  $sqlD="select * from HojaDeTiempo.dbo.Divisiones ";
			  $sqlD=$sqlD." where id_division = " . $reg[id_division] ;
			  $cursorD = mssql_query($sqlD);
			  if ($regD=mssql_fetch_array($cursorD)) {
			  	$miDivision = $regD[nombre] ;
			  }

			  ?>
			  <? echo ucwords($miDivision) ; ?>
			  </td>
            </tr>
          </table>
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><img src="../images/Pixel.gif" width="4" height="4"></td>
  </tr>
</table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TituloUsuario">Programadores</td>
            </tr>
          </table>		  
		  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td width="30%" class="TituloTabla">Programador 1 </td>
              <td class="TxtTabla">
			  <?
		$miUsuarioProg1 = "";
		//Consulta para traer el nombre del Programador 1
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidadProg1]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miUsuarioProg1 = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
		<? echo ucwords(strtolower($miUsuarioProg1)); ?>
			  </td>
            </tr>
            <tr>
              <td width="30%" class="TituloTabla">Programador 2 </td>
              <td class="TxtTabla">
			  <?
		$miUsuarioProg2 = "";
		//Consulta para traer el nombre del director
		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $reg[unidadProg2]; 
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miUsuarioProg2 = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
		<? echo ucwords(strtolower($miUsuarioProg2)); ?>
			  </td>
            </tr>
      </table>


		  		  <? //} //While de reg ?>
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><img src="../images/Pixel.gif" width="4" height="4"></td>
            </tr>
          </table>          
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TituloUsuario">Ordenador autorizado del gasto (Diferente del Director) </td>
            </tr>
          </table>
          <table width="100%"  border="0" cellspacing="1" cellpadding="0">
		  <? while ($reg3=mssql_fetch_array($cursor3)) { ?>            
			<tr>
              <td class="TxtTabla"><? echo ucwords(strtolower($reg3[nombre] . " " . $reg3[apellidos])); ?></td>
            </tr>
			<? } ?>
          </table>
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><img src="../images/Pixel.gif" width="4" height="4"></td>
            </tr>
          </table>
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Relaci&oacute;n de usuarios con copias para las Solicitudes de c&oacute;digo </td>
  </tr>
</table>
		  <table width="100%" border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="8%">Unidad</td>
        <td>Nombre</td>
        </tr>
	  <?
	  while ($reg5=mssql_fetch_array($cursor5)) {
	  ?>
      <tr class="TxtTabla">
        <td width="8%"><? echo $reg5[unidad]; ?></td>
        <td><? echo ucwords(strtolower($reg5[apellidos])) . " " . ucwords(strtolower($reg5[nombre])); ?></td>
        </tr>
			  <?
	  }
	  ?>
	  <?
	  $cursor3 = mssql_query($sql3);
	  while ($reg3=mssql_fetch_array($cursor3)) {
	  ?>
      <tr class="TxtTabla">
        <td><? echo $reg3[unidadOrdenador]; ?></td>
        <td><? echo ucwords(strtolower($reg3[apellidos])) . " " . ucwords(strtolower($reg3[nombre])); ?></td>
      </tr>
	  <?
	  }
	  ?>
    </table>
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><img src="../images/Pixel.gif" width="4" height="4"></td>
            </tr>
          </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Aprobaci&oacute;n</td>
  </tr>
</table>
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
	<form name="form1" method="post" action="">
      <tr>
        <td width="30%" rowspan="2" class="TituloTabla">&iquest;Aprobar la asignaci&oacute;n de c&oacute;digo? </td>
        <td class="TxtTabla">
		<? if ($apruebaContratos == "1") { 
				$selSi = "checked";
				$selNo = "";
			}
			else {
				$selSi = "";
				$selNo = "checked";
			}
		
		?>
		<input name="pCompleto" type="radio" value="1" <? echo $selSi; ?> >
            Si
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="pCompleto" type="radio" value="0" <? echo $selNo; ?> >
            No&nbsp;&nbsp;&nbsp;
		<input name="pSolicitudNo" type="hidden" id="pSolicitudNo" value="<? echo $cualSec; ?>">
        <input name="pConsecutivo" type="hidden" id="pConsecutivo" value="<? echo $miCadena; ?>">	
        <input name="pFecha" type="hidden" id="pFecha" value="<? echo  gmdate ("n/d/y") ; ?>">	
        <input name="cualNombre" type="hidden" id="cualNombre" value="<? echo $cualNombre; ?>">
        <input name="cualCliente" type="hidden" id="cualCliente" value="<? echo $cualCliente; ?>">
        <input name="pMailDirector" type="hidden"  id="pMailDirector" value="<? echo $mimailDirector ; ?>">
        <input name="pMailUsuario" type="hidden" id="pMailUsuario" value="<? echo $miMailUsuario; ?>">
        <input name="pMailProg1" type="hidden" id="pMailProg1" value="<? echo $miMailProg1; ?>">
        <input name="pMailProg2" type="hidden" id="pMailProg2" value="<? echo $miMailProg2; ?>">
<!--
<?
/* 
        <input name="pCodigoDef" type="hidden" id="pCodigoDef" value="<? echo $miCodigoDef ; ?>">
        <input name="pCargoDef" type="hidden" id="pCargoDef" value="<? echo $miCargoDef  ; ?>">
		<input name="pObservaDef" type="hidden" id="pObservaDef" value="<? echo $miObservacionDef  ; ?>">       
*/
?>
//-->
        <input name="pCodigoDef" type="hidden" id="pCodigoDef" value="<? echo $reg[erpCodigo];  ?>">
        <input name="pCargoDef" type="hidden" id="pCargoDef" value="<? echo $reg[erpSubCodigo];  ?>">
		<input name="pObservaDef" type="hidden" id="pObservaDef" value="<? echo $reg[descSubcodigo] ;  ?>">       
		
		<input name="pUniProg1" type="hidden" id="pUniProg1" value="<? echo $miuniProg1; ?>">
        <input name="pUniProg2" type="hidden" id="pUniProg2" value="<? echo $miuniProg2; ?>">
        <input name="pMailCoordina" type="hidden" id="pMailCoordina" value="<? echo $miMailCoordina; ?>">
        <input name="pUnidadCoordina" type="hidden" id="pUnidadCoordina" value="<? echo $miuniCoordina; ?>">
        <input name="pLaEmpresa" type="hidden" id="pLaEmpresa" value="<? echo $miIDEmpresa; ?>">
        <input name="pUnidadDirector" type="hidden" id="pUnidadDirector" value="<? echo $miUnidadDirector; ?>">
        <input name="pElPlazo" type="hidden" id="pElPlazo" value="<? echo $miPlazo; ?>">        </td>
      </tr>
      <tr>
        <td class="TxtTabla">NOTA: Si escoge Si se confirmar&aacute; el env&iacute;o de las copias por correo electr&oacute;nico y la operaci&oacute;n ser&aacute; irreversible.</td>
      </tr>
      <tr>
        <td width="30%" class="TituloTabla">Observaciones:</td>
        <td class="TxtTabla">
		<textarea name="pObserva" cols="50" rows="4" class="CajaTexto" id="pObserva"><? echo $mObserva; ?></textarea>
		<input name="vMiTipoProy" type="hidden" id="vMiTipoProy" value="<? echo $reg[codTipoProy]; ?>">
		<input name="vMiIDDivision" type="hidden" id="vMiIDDivision" value="<? echo $miIDDivision; ?>">         </td>
      </tr>
      <tr>
        <td colspan="2" align="right" class="TxtTabla">
		<input name="pIdEstadoCP" type="hidden" id="pIdEstadoCP" value="<? echo $reg[idEstadoCP]; ?>">
          <input name="pSevenEmpresa" type="hidden" id="pSevenEmpresa" value="<? echo $miSevenEmpresa; ?>">
		  <?
		  	//Encontrar el identificador del estado en Seven
		  	$erpSql03="SELECT * from HojaDeTiempo.dbo.erpTBestadosCodigoProyecto ";
		  	$erpSql03=$erpSql03." WHERE idEstadoCP = " . $reg[idEstadoCP] ;
		  	$cursorerpSql03 = mssql_query($erpSql03);
			if ($regerpSql03=mssql_fetch_array($cursorerpSql03)) {
				$miIdenestadoCP = $regerpSql03[idenEstadoCP];
			}


		  ?>
		  <input name="pIdenEstadoCP" type="hidden" id="pIdenEstadoCP" value="<? echo $miIdenestadoCP; ?>">
		  <input name="pEsSubcodigo" type="hidden" id="pEsSubcodigo" value="<? echo $reg[esSubcodigo];  ?>">
		  <input name="cCodCorto" type="hidden" id="cCodCorto" value="<? echo $reg[codigoCorto]; ?>">
		  <input name="cCrgCorto" type="hidden" id="cCrgCorto" value="<? echo $reg[cargoCorto]; ?>">
          <input name="Submit" type="submit" class="Boton" value="Grabar"></td>
        </tr>
	  </form>
    </table></td>
  </tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td>&nbsp;</td>
      </tr>
</table>
		  		  <? } //While de reg ?>

    <p>&nbsp;</p>
</body>
</html>

<? mssql_close ($conexion); ?>	
