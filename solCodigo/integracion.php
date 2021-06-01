<?php
//////////////////////////esta funci�n ingresa un certificado en la aplicaci�n confact
header("Content-Type: text/html;charset=utf-8");
function certificadoConfact($cualSec)
{

	$sql="select S.*, T.nomTipoProy ";
	$sql= $sql. " from SolicitudCodigo S, tiposProyectos T ";
	$sql= $sql. " where S.codTipoProy = T.codTipoProy ";
	$sql= $sql. " and S.secuencia = " . $cualSec;
	$cursor = mssql_query($sql);
	while ($reg=mssql_fetch_array($cursor)) { 
	   $Certificado= $reg[erpCodigo];
	   $contratoNo= $reg[contratoNo];
	   $contratoValor= $reg[contratoValor];
	   $nombreCompleto= $reg[nombreCompleto];
	   $Certificado= $reg[erpCodigo];
	   $objeto= $reg[objeto];
	   $contratoValor= $reg[contratoValor];
	   $idEmpresa= $reg[idEmpresa];
	   $id_clienteEF= $reg[id_clienteEF];
	   
	}
	/////////////////////////////////
	/////////////////////////////////validacion de si el certificado esta creado en confact
	$sqlCon="select  count(*) as val  from [BDcontratosFactP].[dbo].[Contratos]  where ContrNumcert='$Certificado' and ContrCodEstado=1";
	
	$cursorCon = mssql_query($sqlCon);
	while ($regCon=mssql_fetch_array($cursorCon)) { 
	   $val= $regCon[val];
	}
	////////////////////////////////
	if($val==0 and   $Certificado<>'')
	{
	 
			
	 $query2 = "insert into  [BDcontratosFactP].[dbo].Contratos(ContrNumcert,NumContrato,
			ContrNombreCorto,
			ContrNombreLargo,ContrObjeto,CodEmpresaCli,CodEmpresa,ContrValorContr,ContrCodEstado) ";
			$query2 = $query2 . " VALUES (" ;
			$query2 = $query2 . "'".$Certificado . "', " ; 
			$query2 = $query2 . "'".$contratoNo . "', " ; 
			$query2 = $query2 . "'".$nombreCompleto. "', " ; 
			$query2 = $query2 . "'".$nombreCompleto . "', " ; 
			$query2 = $query2 . "'".$objeto . "', " ; 
			$query2 = $query2 . "'".$id_clienteEF . "', " ; 
			$query2 = $query2 . "'".$idEmpresa . "', " ; 
			$query2 = $query2 . "'".$contratoValor . "', " ;
			$query2 = $query2 . " 1 ";	
			$query2 = $query2 . " ) ";
			$cursorIn = mssql_query($query2) ;  
	}
}
//////////////////////////esta funci�n ingresa un Proyecto en la aplicaci�n confact
function proyectoConFact($cualSec)
{

	$sql="select S.*, T.nomTipoProy ";
	$sql= $sql. " from SolicitudCodigo S, tiposProyectos T ";
	$sql= $sql. " where S.codTipoProy = T.codTipoProy ";
	$sql= $sql. " and S.secuencia = " . $cualSec;
	$cursor = mssql_query($sql);
	while ($reg=mssql_fetch_array($cursor)) { 
	   $Certificado= $reg[erpCodigo];
	   $erpSubCodigo=$reg[erpSubCodigo];
	   $contratoNo= $reg[contratoNo];
	   $contratoValor= $reg[contratoValor];
	   $nombreCompleto= $reg[nombreCompleto];
	   $Certificado= $reg[erpCodigo];
	   $objeto= $reg[objeto];
	   $contratoValor= $reg[contratoValor];
	    $idEmpresa= $reg[idEmpresa];
		  $tipoFact= $reg[sistemaCobro];
		
	   
	}
	$proyecto=$Certificado.$erpSubCodigo;
	
  
	
	    /////////////////////////////////
	/////////////////////////////////validacion de si el proyecto esta creado en confact
	$sqlCon1="select  count(*) as val1  from [BDcontratosFactP].[dbo].[Proyectos]  where ProyCodCompleto='$proyecto' and ProyCodEstado=1";
	
	$cursorCon1 = mssql_query($sqlCon1);
	while ($regCon1=mssql_fetch_array($cursorCon1)) { 
	   $val1= $regCon1[val1];
	}
	////////////////////////////////
	
	
	
	
	
	 if($val1==0)
	{
	 
	  $sqlMax = "SELECT MAX(IdProyecto) maxSec FROM [BDcontratosFactP].[dbo].Proyectos";	
			$cursorMax = mssql_query($sqlMax);
			$regMax = mssql_fetch_array($cursorMax);
			if($regMax[maxSec]){
			$sigConsec = $regMax[maxSec]+1;}
			else
			{$sigConsec = 1;}		 
			 
	////////////////////ingreso de proyecto		 
	 $query2 = "insert into  [BDcontratosFactP].[dbo].Proyectos(IdProyecto,ProyDescripcion,
			ProyNumCertificado,
			ProySubCodigo,ProyCodCompleto,Registro,TipoRegistro,FacturaCom,ProyCodEstado) ";
			$query2 = $query2 . " VALUES (" ;
			$query2 = $query2 . "'".$sigConsec . "', " ; 
			$query2 = $query2 . "'".$nombreCompleto . "', " ; 
			$query2 = $query2 . "'".$Certificado. "', " ; 
			$query2 = $query2 . "'".$erpSubCodigo . "', " ; 
			$query2 = $query2 . "'".$proyecto . "', " ; 
			$query2 = $query2 . "'1', " ; 
			$query2 = $query2 . "'1', " ;
			$query2 = $query2 . " '0', ";
			$query2 = $query2 . " 1 ";		
		    $query2 = $query2 . " ) ";
			$cursorIn = mssql_query($query2) ; 
					
					
		
	
			//////////////////////////// conversión de tipo de facturación del portal a confact
			
			 $sqlTF= " select *   FROM [BDcontratosFactP].[dbo].[TipoFact] where TFcodPortal= " . $tipoFact;
			$cursorTF = mssql_query($sqlTF);
			while ($regTF=mssql_fetch_array($cursorTF)) {
				
			   $Tfact= $regTF[IdTipoFact];	
			}

			
			
			////////////////////////////traer el numero de facturación vs proyecto mas grande
			
			
			 $sql1="Select max(IdProyFact) MaxCodigo FROM [BDcontratosFactP].[dbo].[ProyFact] ";
			$cursor1 = mssql_query($sql1);
			if ($reg1=mssql_fetch_array($cursor1)) {
				$pSolicitudNo = $reg1[MaxCodigo] + 1;
				}
			else {
				$pSolicitudNo = 1;
			}
			
			
			////////////////////////// ingreso de tipo de facturación por proyecto
			
			$query21 = "insert into  [BDcontratosFactP].[dbo].ProyFact(IdProyFact,CodTipoFact,
					ProyFactCodEstado,
					PFCert,PFSub) ";
					$query21 = $query21 . " VALUES (" ;
					$query21 = $query21 . "'".$pSolicitudNo . "', " ; 
					$query21 = $query21 . "'".$Tfact . "', " ; 
					$query21 = $query21 . "'1', " ; 
					$query21 = $query21 . "'".$Certificado . "', " ; 
					$query21 = $query21 . "'".$erpSubCodigo . "' " ;
					$query21 = $query21 . " ) ";			
					$cursorIn2 = mssql_query($query21) ; 
			
			
			/////////////////////////	
			
			include('funciones.php');
			horario($Certificado,$erpSubCodigo,$proyecto);
			
			
		
	}

}

function WSSeven($cualSec)
{
	$sql="select S.*, T.nomTipoProy ";
	$sql= $sql. " from SolicitudCodigo S, tiposProyectos T ";
	$sql= $sql. " where S.codTipoProy = T.codTipoProy ";
	$sql= $sql. " and S.secuencia = " . $cualSec;
	$cursor = mssql_query($sql);
	while ($reg=mssql_fetch_array($cursor)) { 
	   $Certificado= $reg[erpCodigo];
	   $erpSubCodigo=$reg[erpSubCodigo];
	   $contratoNo= $reg[contratoNo];
	   $contratoValor= $reg[contratoValor];
	   $nombreCompleto= $reg[nombreCompleto];
	   $Certificado= $reg[erpCodigo];
	   $objeto= $reg[objeto];
	   $contratoValor= $reg[contratoValor];
	   $idEmpresa= $reg[idEmpresa];
	   $sistemaCobro= $reg[sistemaCobro];	
	   
	}
	$proyecto=$Certificado.$erpSubCodigo;
	
		require_once("lib/nusoap.php");	
		//////////datos quemados 
			
		$sub=$erpSubCodigo;
		$p_emp_codi1=378;
		$p_emp_codi2=379;
		$p_emp_codi3=380;
		$p_tar_codi=4;
		$p_arb_codi1='0000'.$Certificado;
		$p_arb_codi2='0000'.$Certificado.$erpSubCodigo;	
		$p_arb_codi31='0000'.$Certificado.$erpSubCodigo.'0';
		$p_arb_codi32='0000'.$Certificado.$erpSubCodigo.'1';
		$p_arb_codi33='0000'.$Certificado.$erpSubCodigo.'8';
		$p_arb_codi34='0000'.$Certificado.$erpSubCodigo.'9';	
		$p_arb_nomb1=$nombreCompleto;
		$p_arb_nomb2=$sub;
		$p_arb_nomb31=$nombreCompleto.'-'.$sub.'-'.'Cf';//cargo0
		$p_arb_nomb32=$nombreCompleto.'-'.$sub.'-'.'Rb';//cargo1
		$p_arb_nomb33=$nombreCompleto.'-'.$sub.'-'.'Rc';//cargo8	
		$p_arb_nomb34=$nombreCompleto.'-'.$sub.'-'.'Nc';//cargo9
		$p_cod_padr1='0000';
		$p_cod_padr2='0000'.$Certificado;
		$p_cod_padr3='0000'.$Certificado.$erpSubCodigo;				
		$p_nar_codi1=3;
		$p_nar_codi2=4;
		$p_nar_codi3=5;
		$arb_cvaf='N';
		$arb_coan=0;
		$arb_movi1='N';
		$arb_movi2='S';
	
	
		$wsdl="http://192.168.30.169/SEVEN/WEBSERVICESophelia/WGnArbol.asmx?WSDL";
		$client=new nusoap_client($wsdl,true);
	 
	//////////////parametros para nivel 3
		  $parametros1=array(
		'p_emp_codi' => $p_emp_codi1,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi1,
		'p_arb_nomb' => $p_arb_nomb1,
		'p_cod_padr' => $p_cod_padr1,
		'p_nar_codi' => $p_nar_codi1,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);
		
		 $parametros2=array(
		'p_emp_codi' => $p_emp_codi2,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi1,
		'p_arb_nomb' => $p_arb_nomb1,
		'p_cod_padr' => $p_cod_padr1,
		'p_nar_codi' => $p_nar_codi1,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);
		 $parametros3=array(
		'p_emp_codi' => $p_emp_codi3,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi1,
		'p_arb_nomb' => $p_arb_nomb1,
		'p_cod_padr' => $p_cod_padr1,
		'p_nar_codi' => $p_nar_codi1,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);
	///////////////////////////////////////
	
	//////parametros para nivel 4
	
	
	$parametros11=array(
		'p_emp_codi' => $p_emp_codi1,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi2,
		'p_arb_nomb' => $p_arb_nomb2,
		'p_cod_padr' => $p_cod_padr2,
		'p_nar_codi' => $p_nar_codi2,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);
		
		 $parametros12=array(
		'p_emp_codi' => $p_emp_codi2,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi2,
		'p_arb_nomb' => $p_arb_nomb2,
		'p_cod_padr' => $p_cod_padr2,
		'p_nar_codi' => $p_nar_codi2,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);
		 $parametros13=array(
		'p_emp_codi' => $p_emp_codi3,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi2,
		'p_arb_nomb' => $p_arb_nomb2,
		'p_cod_padr' => $p_cod_padr2,
		'p_nar_codi' => $p_nar_codi2,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);
	
	/////////////////////parmetros nivel 5 
	
	//////////////empresa1
	
	
	$parametros111=array(
		'p_emp_codi' => $p_emp_codi1,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi31,
		'p_arb_nomb' => $p_arb_nomb31,
		'p_cod_padr' => $p_cod_padr3,
		'p_nar_codi' => $p_nar_codi3,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi2
		);
		
		 $parametros112=array(
		'p_emp_codi' => $p_emp_codi1,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi32,
		'p_arb_nomb' => $p_arb_nomb32,
		'p_cod_padr' => $p_cod_padr3,
		'p_nar_codi' => $p_nar_codi3,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi2
		);
		 $parametros113=array(
		'p_emp_codi' => $p_emp_codi1,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi33,
		'p_arb_nomb' => $p_arb_nomb33,
		'p_cod_padr' => $p_cod_padr3,
		'p_nar_codi' => $p_nar_codi3,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi2
		);
			 $parametros114=array(
		'p_emp_codi' => $p_emp_codi1,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi34,
		'p_arb_nomb' => $p_arb_nomb34,
		'p_cod_padr' => $p_cod_padr3,
		'p_nar_codi' => $p_nar_codi3,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi2
		);
	
	////////////////////////////////////////////
	////////////empresa2
	
	
	$parametros211=array(
		'p_emp_codi' => $p_emp_codi2,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi31,
		'p_arb_nomb' => $p_arb_nomb31,
		'p_cod_padr' => $p_cod_padr3,
		'p_nar_codi' => $p_nar_codi3,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi2
		);
		
		 $parametros212=array(
		'p_emp_codi' => $p_emp_codi2,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi32,
		'p_arb_nomb' => $p_arb_nomb32,
		'p_cod_padr' => $p_cod_padr3,
		'p_nar_codi' => $p_nar_codi3,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi2
		);
		 $parametros213=array(
		'p_emp_codi' => $p_emp_codi2,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi33,
		'p_arb_nomb' => $p_arb_nomb33,
		'p_cod_padr' => $p_cod_padr3,
		'p_nar_codi' => $p_nar_codi3,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi2
		);
		 $parametros214=array(
		'p_emp_codi' => $p_emp_codi2,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi34,
		'p_arb_nomb' => $p_arb_nomb34,
		'p_cod_padr' => $p_cod_padr3,
		'p_nar_codi' => $p_nar_codi3,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi2
		);
	///////////////
	/////////////////////empresa3
	
	$parametros311=array(
		'p_emp_codi' => $p_emp_codi3,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi31,
		'p_arb_nomb' => $p_arb_nomb31,
		'p_cod_padr' => $p_cod_padr3,
		'p_nar_codi' => $p_nar_codi3,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi2
		);
		
		 $parametros312=array(
		'p_emp_codi' => $p_emp_codi3,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi32,
		'p_arb_nomb' => $p_arb_nomb32,
		'p_cod_padr' => $p_cod_padr3,
		'p_nar_codi' => $p_nar_codi3,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi2
		);
		 $parametros313=array(
		'p_emp_codi' => $p_emp_codi3,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi33,
		'p_arb_nomb' => $p_arb_nomb33,
		'p_cod_padr' => $p_cod_padr3,
		'p_nar_codi' => $p_nar_codi3,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi2
		);
		$parametros314=array(
		'p_emp_codi' => $p_emp_codi3,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi34,
		'p_arb_nomb' => $p_arb_nomb34,
		'p_cod_padr' => $p_cod_padr3,
		'p_nar_codi' => $p_nar_codi3,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi2
		);
	

	//////////inserta arbol de nivel 3
	$respuesta1=$client->call('InsertarCodigos',$parametros1);
	$respuesta2=$client->call('InsertarCodigos',$parametros2);
	$respuesta3=$client->call('InsertarCodigos',$parametros3);
	
	/////////inserta arbol de nivel 4
	$respuesta11=$client->call('InsertarCodigos',$parametros11);
	$respuesta12=$client->call('InsertarCodigos',$parametros12);
	$respuesta13=$client->call('InsertarCodigos',$parametros13);
	
	
	///////////inserta arbol de nivel 5
	
	
	///////////validación
	
	if($sistemaCobro<>4 and  $sistemaCobro<>5 and $sistemaCobro<>7 and $sistemaCobro<>9)
	{
	/////////////////////////empresa 1
	$respuesta111=$client->call('InsertarCodigos',$parametros111);//cargo 0
	$respuesta112=$client->call('InsertarCodigos',$parametros112);//cargo 1
	$respuesta113=$client->call('InsertarCodigos',$parametros113);//cargo 8
	$respuesta114=$client->call('InsertarCodigos',$parametros114);//cargo 9
	
	/////////////////////////empresa 2
	$respuesta211=$client->call('InsertarCodigos',$parametros211);
	$respuesta212=$client->call('InsertarCodigos',$parametros212);
	$respuesta213=$client->call('InsertarCodigos',$parametros213);
	$respuesta214=$client->call('InsertarCodigos',$parametros214);
	
	/////////////////////////empresa 3
	$respuesta311=$client->call('InsertarCodigos',$parametros311);
	$respuesta312=$client->call('InsertarCodigos',$parametros312);
	$respuesta313=$client->call('InsertarCodigos',$parametros313);
	$respuesta314=$client->call('InsertarCodigos',$parametros314);
	}///////////cierre de sistema de facturación
	
	if($sistemaCobro==4)
	{
	 $respuesta111=$client->call('InsertarCodigos',$parametros111);//cargo 0
	 $respuesta211=$client->call('InsertarCodigos',$parametros211);
	 $respuesta311=$client->call('InsertarCodigos',$parametros311);
	 
	}
	if($sistemaCobro==5 or $sistemaCobro==7 or $sistemaCobro==9)
	{
	 $respuesta114=$client->call('InsertarCodigos',$parametros114);//cargo 9
	 $respuesta214=$client->call('InsertarCodigos',$parametros214);
	 $respuesta314=$client->call('InsertarCodigos',$parametros314);
	 
	}
	
}

function WSSevenAN($cualSec)
{
	$sql="select S.*, T.nomTipoProy ";
	$sql= $sql. " from SolicitudCodigo S, tiposProyectos T ";
	$sql= $sql. " where S.codTipoProy = T.codTipoProy ";
	$sql= $sql. " and S.secuencia = " . $cualSec;
	$cursor = mssql_query($sql);
	while ($reg=mssql_fetch_array($cursor)) { 
	   $Certificado= $reg[erpCodigo];
	   $erpSubCodigo=$reg[erpSubCodigo];
	   $contratoNo= $reg[contratoNo];
	   $contratoValor= $reg[contratoValor];
	   $nombreCompleto= $reg[nombreCompleto];
	   $Certificado= $reg[erpCodigo];
	   $objeto= $reg[objeto];
	   $contratoValor= $reg[contratoValor];
	   $idEmpresa= $reg[idEmpresa];
	   $sistemaCobro= $reg[sistemaCobro];	
	   
	}
	$proyecto=$Certificado.$erpSubCodigo;
	
		require_once("lib/nusoap.php");	


	
		$sub=$erpSubCodigo;
		$p_emp_codi1=378;
		$p_emp_codi2=379;
		$p_emp_codi3=380;
		$p_tar_codi=1;
		$p_arb_codi1=$Certificado.$erpSubCodigo;
		$p_arb_codi2=$Certificado.$erpSubCodigo."00";	
		$p_arb_codi3=$Certificado.$erpSubCodigo."00"."00";	
		$p_arb_codi4=$Certificado.$erpSubCodigo."00"."00"."00";	
		$p_arb_codi5=$Certificado.$erpSubCodigo."00"."00"."00"."00";		
		$p_arb_nomb1=$nombreCompleto.'-'.$sub;
		$p_arb_nomb2="LC";
		$p_arb_nomb3="LT";
		$p_arb_nomb4="DI";
		$p_arb_nomb5=$nombreCompleto.'-'.$sub.'-'.'LC'.'-'.'LT'.'-'.'DI'.'-'.'MA';//cargo0
		$p_cod_padr1='-1';
		$p_cod_padr2=$Certificado.$erpSubCodigo;
		$p_cod_padr3=$Certificado.$erpSubCodigo.'00';
		$p_cod_padr4=$Certificado.$erpSubCodigo.'00'.'00';
		$p_cod_padr5=$Certificado.$erpSubCodigo.'00'.'00'.'00';				
		$p_nar_codi1=1;
		$p_nar_codi2=2;
		$p_nar_codi3=3;
		$p_nar_codi4=4;
		$p_nar_codi5=5;
		$arb_cvaf='N';
		$arb_coan=0;
		$arb_movi1='N';
		$arb_movi2='S';
	
	   	$wsdl="http://192.168.30.169/SEVEN/WEBSERVICESophelia/WGnArbol.asmx?WSDL";
		
		$client=new nusoap_client($wsdl,true);
			


	 
	//////////////parametros para Empresa 1
		  $parametros11=array(
		'p_emp_codi' => $p_emp_codi1,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi1,
		'p_arb_nomb' => $p_arb_nomb1,
		'p_cod_padr' => $p_cod_padr1,
		'p_nar_codi' => $p_nar_codi1,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);
		
		 $parametros12=array(
		'p_emp_codi' => $p_emp_codi1,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi2,
		'p_arb_nomb' => $p_arb_nomb2,
		'p_cod_padr' => $p_cod_padr2,
		'p_nar_codi' => $p_nar_codi2,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);
		 $parametros13=array(
		'p_emp_codi' => $p_emp_codi1,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi3,
		'p_arb_nomb' => $p_arb_nomb3,
		'p_cod_padr' => $p_arb_codi2,
		'p_nar_codi' => $p_nar_codi3,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);


		 $parametros14=array(
		'p_emp_codi' => $p_emp_codi1,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi4,
		'p_arb_nomb' => $p_arb_nomb4,
		'p_cod_padr' => $p_cod_padr4,
		'p_nar_codi' => $p_nar_codi4,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);

		 var_dump($parametros14);

		 $parametros15=array(
		'p_emp_codi' => $p_emp_codi1,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi5,
		'p_arb_nomb' => $p_arb_nomb5,
		'p_cod_padr' => $p_cod_padr5,
		'p_nar_codi' => $p_nar_codi5,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi2
		);
	///////////////////////////////////////
	
	
	
		//////////////parametros para Empresa 2
		  $parametros21=array(
		'p_emp_codi' => $p_emp_codi2,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi1,
		'p_arb_nomb' => $p_arb_nomb1,
		'p_cod_padr' => $p_cod_padr1,
		'p_nar_codi' => $p_nar_codi1,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);
		
		 $parametros22=array(
		'p_emp_codi' => $p_emp_codi2,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi2,
		'p_arb_nomb' => $p_arb_nomb2,
		'p_cod_padr' => $p_cod_padr2,
		'p_nar_codi' => $p_nar_codi2,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);
		 $parametros23=array(
		'p_emp_codi' => $p_emp_codi2,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi3,
		'p_arb_nomb' => $p_arb_nomb3,
		'p_cod_padr' => $p_cod_padr3,
		'p_nar_codi' => $p_nar_codi3,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);
		 $parametros24=array(
		'p_emp_codi' => $p_emp_codi2,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi4,
		'p_arb_nomb' => $p_arb_nomb4,
		'p_cod_padr' => $p_cod_padr4,
		'p_nar_codi' => $p_nar_codi4,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);
		 $parametros25=array(
		'p_emp_codi' => $p_emp_codi2,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi5,
		'p_arb_nomb' => $p_arb_nomb5,
		'p_cod_padr' => $p_cod_padr5,
		'p_nar_codi' => $p_nar_codi5,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi2
		);
	///////////////////////////////////////
	

	//////////////parametros para Empresa 3
		  $parametros31=array(
		'p_emp_codi' => $p_emp_codi3,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi1,
		'p_arb_nomb' => $p_arb_nomb1,
		'p_cod_padr' => $p_cod_padr1,
		'p_nar_codi' => $p_nar_codi1,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);
		
		 $parametros32=array(
		'p_emp_codi' => $p_emp_codi3,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi2,
		'p_arb_nomb' => $p_arb_nomb2,
		'p_cod_padr' => $p_cod_padr2,
		'p_nar_codi' => $p_nar_codi2,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);
		 $parametros33=array(
		'p_emp_codi' => $p_emp_codi3,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi3,
		'p_arb_nomb' => $p_arb_nomb3,
		'p_cod_padr' => $p_cod_padr3,
		'p_nar_codi' => $p_nar_codi3,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);
		 $parametros34=array(
		'p_emp_codi' => $p_emp_codi3,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi4,
		'p_arb_nomb' => $p_arb_nomb4,
		'p_cod_padr' => $p_cod_padr4,
		'p_nar_codi' => $p_nar_codi4,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi1
		);
		 $parametros35=array(
		'p_emp_codi' => $p_emp_codi3,
		'p_tar_codi' => $p_tar_codi,
		'p_arb_codi' => $p_arb_codi5,
		'p_arb_nomb' => $p_arb_nomb5,
		'p_cod_padr' => $p_cod_padr5,
		'p_nar_codi' => $p_nar_codi5,
		'arb_cvaf' => $arb_cvaf,
		'arb_coan' => $arb_coan,
		'arb_movi' => $arb_movi2
		);
	///////////////////////////////////////

	

	//////////inserta arbol para empresa1
	//////////////
	$respuesta11=$client->call('InsertarCodigos',$parametros11);
	$respuesta12=$client->call('InsertarCodigos',$parametros12);
	$respuesta13=$client->call('InsertarCodigos',$parametros13);
	$respuesta14=$client->call('InsertarCodigos',$parametros14);
	$respuesta15=$client->call('InsertarCodigos',$parametros15);
	/////////////////////


	
		//////////inserta arbol para empresa2
	//////////////
	$respuesta21=$client->call('InsertarCodigos',$parametros21);
	$respuesta22=$client->call('InsertarCodigos',$parametros22);
	$respuesta23=$client->call('InsertarCodigos',$parametros23);
	$respuesta24=$client->call('InsertarCodigos',$parametros24);
	$respuesta25=$client->call('InsertarCodigos',$parametros25);
	/////////////////////
	
		//////////inserta arbol para empresa3
	//////////////
	$respuesta31=$client->call('InsertarCodigos',$parametros31);
	$respuesta32=$client->call('InsertarCodigos',$parametros32);
	$respuesta33=$client->call('InsertarCodigos',$parametros33);
	$respuesta34=$client->call('InsertarCodigos',$parametros34);
	$respuesta35=$client->call('InsertarCodigos',$parametros35);

		
}
		
function Experiencia($cualSec)
{

 $sql="select S.*, T.nomTipoProy ";
	$sql= $sql. " from SolicitudCodigo S, tiposProyectos T ";
	$sql= $sql. " where S.codTipoProy = T.codTipoProy ";
	$sql= $sql. " and S.secuencia = " . $cualSec;
	$cursor = mssql_query($sql);
	while ($reg=mssql_fetch_array($cursor)) { 
	   $Certificado= $reg[erpCodigo];
	   $contratoNo= $reg[contratoNo];
	   $contratoValor= $reg[contratoValor];
	   $nombreCompleto= $reg[nombreCompleto];
	   $objeto= $reg[objeto];
	   $aplicaConsorcio1= $reg[aplicaConsorcio];
	   $contratoValor= $reg[contratoValor];
	   $idEmpresa= $reg[idEmpresa];
	   $id_clienteEF= $reg[id_clienteEF];
	   $tipoFact= $reg[sistemaCobro];
	   $erpSubCodigo=$reg[erpSubCodigo];
	   $facturable1=$reg[facturable];
	   $idEmpresa=$reg[idEmpresa];
	   $id_proy=$reg[id_proyecto];
	   
	   
	}
	
	
		
	
	/////////////////////////////////////////////////id maximo de proyecto
	
		 $sql11="Select max(id_proyecto) MaxCodigo FROM [EFProyectos] ";
	$cursor11 = mssql_query($sql11);
	if ($reg11=mssql_fetch_array($cursor11)) {
		$id_proyecto = $reg11[MaxCodigo] + 1;
		}
	else {
		$id_proyecto = 1;
	}
	///////////////////////////////////////////////////////verificación del consorcio
	
	$codigo_SEVEN="0000".$Certificado.$erpSubCodigo;
    if($aplicaConsorcio1==1)
	{
		$aplicaConsorcio=2;
		
	}
	if($aplicaConsorcio1==0)
	{
		
		echo $aplicaConsorcio=1;
		
	}
	
	if($facturable1==1)
	{
		$facturable=4;
	}
	if($facturable1==0)
	{
		$facturable=5;
	}

/////////////ingreso del proyecto	

if($facturable1==1)
	{
 ////////////////insert cuando el proyecto es facturable
 $sqlEF="	
	insert into EFProyectos (id_proyecto,certificado_SEVEN,codigo_SEVEN,numero_contrato
	,objeto,id_tipo_proyecto,id_tipo_ejecucion,id_empresa,id_forma_pago
	,id_proyecto_portal,revision,usuarioGraba,fechaGraba) 
	values('$id_proyecto ',
	'$Certificado ',
	'$codigo_SEVEN ',
	'$contratoNo ',
	'$objeto ',
	'$facturable ',
	'$aplicaConsorcio ',
	'$idEmpresa ',
	'$tipoFact ',
	'$id_proy ',
	'0',
	'".$_SESSION[ "sesUnidadUsuario" ]." ',
	'".gmdate ("n/d/y")." ')
	";
	}
	
	if($facturable1==0)
	{
	///////////////////////////////insert cuando el proyecto no es facturable	
		
		 $sqlEF="	
		insert into EFProyectos (id_proyecto,certificado_SEVEN,codigo_SEVEN,numero_contrato
		,objeto,id_tipo_proyecto,id_tipo_ejecucion,id_empresa,id_forma_pago
		,id_proyecto_portal,revision, fechaRevision, usuarioRevision,usuarioGraba,fechaGraba) 
		values('$id_proyecto ',
		'$Certificado ',
		'$codigo_SEVEN ',
		'$contratoNo ',
		'$objeto ',
		'$facturable ',
		'$aplicaConsorcio ',
		'$idEmpresa ',
		'$tipoFact ',
		'$id_proy ',
        '1',
		'".gmdate ("n/d/y").",
	    '".$_SESSION[ "sesUnidadUsuario" ]." ',		
		'".$_SESSION[ "sesUnidadUsuario" ]." ',
		'".gmdate ("n/d/y")." ')
		";
		
		
	}
	$cursorEF = mssql_query($sqlEF);
////////////////////nombre del proyecto


	$sqlNombre="
	insert into EFNombres_Proyectos (id_nombres_proyecto,id_proyecto,nombre_corto_proyecto,nombre_largo_proyecto,nombre_actual,usuarioGraba,fechaGraba)
	values('1',
	'$id_proyecto ',
	'$nombreCompleto ',
	'$nombreCompleto ',
	'1',
	'".$_SESSION[ "sesUnidadUsuario" ]." ',
	'".gmdate ("n/d/y")." ')
	";
	$cursorEFN = mssql_query($sqlNombre);
	///////////////////////////////////////////////////////////////// valor del proyecto
	
	$sqlValor="
	insert into EFValores_Contrato (id_valores_proyecto,id_proyecto,tipo,valor_contrato,valores_actuales,usuarioGraba,fechaGraba) values('1',
	'$id_proyecto ',

	'3 ',
	'$contratoValor ',
	'1 ',
	'".$_SESSION[ "sesUnidadUsuario" ]." ',
		'".gmdate ("n/d/y")." ')
		";
	
	$cursorEFV = mssql_query($sqlValor);
	
////////////relación de clientes 


	 $sqlrelClien="
	insert into EFClientes_Proyectos (id_cliente_proyecto,id_cliente,id_proyecto,cliente_actual,usuarioGraba,fechaGraba) values('1',
	'$id_clienteEF ',
	'$id_proyecto ',
	'1 ',
	'".$_SESSION[ "sesUnidadUsuario" ]." ',
		'".gmdate ("n/d/y")." ')

	";	
	$cursorEFRC = mssql_query($sqlrelClien);


///////////////ajuste del estado actual	
	if($facturable1==1)
	{
		$estado_actual=2;	
	}
	if($facturable1==0)
	{
		
		//////////////////////////// conversión de tipo de facturación del portal a confact
	
			 $sqlTF= " 	SELECT CONVERT(varchar, DATEADD(MONTH, +6, GETDATE()),101) fecha_fin , CONVERT(varchar, GETDATE(),101) fecha_inicio ";
			$cursorTF = mssql_query($sqlTF);
			while ($regTF=mssql_fetch_array($cursorTF)) {
				
			   $fecha_fin_NF= $regTF[fecha_fin];
			   $fecha_inicio_NF= $regTF[fecha_inicio];		   
			}
			////////////////////////////
			
		$sqlFecha="
		insert into EFFechas_Proyecto (id_fecha_proyecto,id_proyecto,fecha_inicio_proyecto,fecha_final_proyecto,fechas_actuales,usuarioGraba,fechaGraba) 
		values('1 ',
		'$id_proyecto ',
		'$fecha_inicio_NF ',
		'$fecha_fin_NF ',
		'1 ',
		'".$_SESSION[ "sesUnidadUsuario" ]." ',
			'".gmdate ("n/d/y")." ')
			 
		";
		$cursorEFF = mssql_query($sqlFecha);	
		
		
		
		 if($tipoFact==5)
		 {		 
			$estado_actual=1;
		 }
		 else{
			$estado_actual=10;	
		 }
	}

	
////////////////estado de proyecto	


			 $sqlEsta="

			insert into EFEstados_Proy_Proyectos (id_estados_proy_proyectos,id_estado_proy,id_proyecto,fecha_estado,estado_actual,usuarioGraba,fechaGraba) values('1',
			'$estado_actual ',
			
			'$id_proyecto ',
			'".gmdate ("n/d/y")." ',
			'1 ',
			'".$_SESSION[ "sesUnidadUsuario" ]." ',
				'".gmdate ("n/d/y")." ')
			";			
	
		$cursorEFE = mssql_query($sqlEsta);	
}	

function ExperienciaResponsables($Proy)
{
	$sql_proy="select id_director,id_coordinador from HojaDeTiempo.dbo.PROYECTOS where id_proyecto=".$Proy;
	$cur_proy=mssql_query($sql_proy);
	$datos_proy=mssql_fetch_array($cur_proy);
	
	//ALMACENA EL DIRECTOR
	$sqlIn1 = " INSERT INTO GestiondeInformacionDigital.dbo.EFDirector_Coordinador";
	$sqlIn1 = $sqlIn1 . "( id_director_coordinador, id_proyecto, unidad_director_coordinador, tipo, director_coordinador_actual, usuarioGraba, fechaGraba ) ";
	$sqlIn1 = $sqlIn1 . " values ( (select isnull(MAX(id_director_coordinador),0)+1 id_proye  from EFDirector_Coordinador WHERE id_proyecto=".$Proy."), ".$Proy.", ".$datos_proy["id_director"].", 1, 1 ,".$_SESSION["sesUnidadUsuario"].",getdate()  ) ";
	$cursorIn1 = mssql_query($sqlIn1);		
	
	if(trim($datos_proy["id_coordinador"])!="")
	{
		//ALMACENA EL NUEVO CORRDINADOR
		$sqlIn1 = " INSERT INTO GestiondeInformacionDigital.dbo.EFDirector_Coordinador";
		$sqlIn1 = $sqlIn1 . "( id_director_coordinador,id_proyecto  , unidad_director_coordinador, tipo, director_coordinador_actual, usuarioGraba, fechaGraba ) ";
		$sqlIn1 = $sqlIn1 . " values ( (select isnull(MAX(id_director_coordinador),0)+1 id_proye  from EFDirector_Coordinador WHERE id_proyecto=".$Proy."), ".$Proy.", ".$datos_proy["id_coordinador"].", 2, 1 ,".$_SESSION["sesUnidadUsuario"].",getdate()  ) ";
		$cursorIn1 = mssql_query($sqlIn1);			
	}
	
	//ORDENADORES DE GASTO
	$sql_proy="select unidadOrdenador from GestiondeInformacionDigital.dbo.OrdenadorGasto where id_proyecto=".$Proy;
	$cur_proy=mssql_query($sql_proy);
	while($datos_proy=mssql_fetch_array($cur_proy))
	{
		$sqlIn1 = " INSERT INTO GestiondeInformacionDigital.dbo.EFOrdenadores_gasto ";
		$sqlIn1 = $sqlIn1 . "( id_ordenadores_gasto, id_proyecto, unidad_ordenador, ordenador_actual, usuarioGraba, fechaGraba ) ";
		$sqlIn1 = $sqlIn1 . " values ( (select isnull(MAX(id_ordenadores_gasto),0)+1 id   from EFOrdenadores_gasto WHERE id_proyecto=".$Proy." ), ".$Proy.", ".$datos_proy["unidadOrdenador"].", 1 ,".$_SESSION["sesUnidadUsuario"].",getdate()  ) ";
		$cursorIn1 = mssql_query($sqlIn1);					
	}

}

?>