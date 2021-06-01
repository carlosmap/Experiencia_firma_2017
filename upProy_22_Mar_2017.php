<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente
if($acc==2)
	$mens_v="Actualizar Proyecto";	   		
if($acc==3)
	$mens_v="Eliminar Proyecto";	   
banner(2,$mens_v);

//include ("../../verificaRegistro2.php");
if($recarga==2)
{
	if($acc==2)
	{
			$cant_nom=0;
			//VERIFICA QUE NO EXISTA UN PROYECTO, CON EL MIMSO NOMBRE LARGO, REGISTRADO
			$sql_veri="select * from EFNombres_Proyectos where nombre_largo_proyecto='".$ProyL."' and nombre_actual=1 and id_proyecto <> ".$Proy;
			$cur_veri=mssql_query($sql_veri);
			$cant_nom=mssql_num_rows($cur_veri);
		//echo mssql_num_rows($cur_veri)." *****";
		
		//echo $SEVENN." --- ".$SEVENNAnt."<br>";
			$cant_veri_seven=0;
			//SI SE HA MODIFICADO EL CODIGO SEVEN, SE VERIFICA SI EXISTE UNO CON EL MISMO CODIGO
			if($SEVENN!=$SEVENNAnt)
			{
				$sql_veri="select * from EFProyectos where codigo_SEVEN='".$SEVENN."' and id_proyecto <> ".$Proy;
				$cur_veri_seven=mssql_query($sql_veri);
				$cant_veri_seven=mssql_num_rows($cur_veri_seven);
		//echo "INGRESAA ".$sql_veri." <BR>";		
			}
		//echo "cur_veri=".mssql_num_rows($cur_veri)." cur_veri_seven=".mssql_num_rows($cur_veri_seven)."<br>";	
		//echo "cant_nom = ".$cant_nom." cant_veri_seven = ".$cant_veri_seven."<br>";
			if(($cant_nom==0) && ($cant_veri_seven==0) )
			{
					
				mssql_query("begin transaction");			
						
				//INFO BASICA DEL PROYECTO
				$sqlIn1 = " update EFProyectos  set";
				$sqlIn1 = $sqlIn1 . " certificado_SPEEDWARE='".$SPEE."',  certificado_SEVEN='".$SEVE."', codigo='".$Cod."', codigo_SEVEN='".$SEVENN."', codigo_SEVEN_anterior='".$SEVENA."', numero_contrato='".$NoContrato."',  id_tipo_proyecto=".$TipoP.", id_moneda=".$Moneda.", TRM=".$TRM.", id_tipo_ejecucion=".$Ejecucion.", id_empresa=".$Empresa.", id_forma_pago=".$FPago.", usuarioMod=".$_SESSION["sesUnidadUsuario"].",  fechaMod=getdate() where id_proyecto= ".$Proy;
		
				$cursorIn1 = mssql_query($sqlIn1);					
		//echo "1. EFProyectos <br>".$sqlIn1." ------ ".mssql_get_last_message()." <br>";		
				if($cursorIn1!="")
				{
		
					//SI SE HA CAMBIADO EN NOMBRE LARGO O CORTO DEL PROYECTO
					if( ($ProyL!=$ProyLAnt) || ($ProyC!=$ProyCAnt) )
					{
						//ACTUALIZA EL nombre_actual DEL LOS REGISTROS VIEJOS
						$sqlIn1 =" UPDATE EFNombres_Proyectos SET nombre_actual=0 where id_proyecto=".$Proy;
						$cursorIn1 = mssql_query($sqlIn1);					
						if($cursorIn1!="")
						{					
							$sqlIn1 = " INSERT INTO EFNombres_Proyectos (id_nombres_proyecto, id_proyecto,nombre_largo_proyecto, nombre_corto_proyecto, nombre_actual, usuarioGraba, fechaGraba) VALUES";
							$sqlIn1 = $sqlIn1." ( (select isnull(MAX(id_nombres_proyecto),0)+1 id_proye  from EFNombres_Proyectos WHERE id_proyecto=".$Proy." ), ".$Proy.", '".$ProyL."', '".$ProyC."', 1 , ".$_SESSION["sesUnidadUsuario"].", getdate() ) ";
							$cursorIn1 = mssql_query($sqlIn1);
		//echo "2. EFNombres_Proyectos <br>".$sqlIn1." ------ ".mssql_get_last_message()." <br>";							
						}				
					}
					
					//SI SE HA CAMBIADO EL CLIENTE DEL PROYECTO
					if(($Cliente!=$ClienteAnt) && ($cursorIn1!=""))
					{
						$sql_veri="delete from EFClientes_Proyectos WHERE id_proyecto=".$Proy;
						$cursorIn1=mssql_query($sql_veri);						
						
						if($cursorIn1!="")
						{				
							//CLIENTE DEL PROYECTO
							$sqlIn1 ="insert into EFClientes_Proyectos (id_cliente,id_proyecto) values( ".$Cliente.", ".$Proy.")";
							$cursorIn1 = mssql_query($sqlIn1);					
		
		//echo "3. EFClientes_Proyectos <br>".$sqlIn1." ------ ".mssql_get_last_message()." <br>";
						}
					}
		
					//SI SE HA CAMBIADO LAS FECHAS INICIO / FINALIZACION
					if( ( ($Finicio!=$FinicioAnt)||($Final!=$FinalAnt) ) && ($cursorIn1!=""))
					{		
						//ACTUALIZA LAS fechas_actuales DEL LOS REGISTROS VIEJOS
						$sqlIn1 =" UPDATE EFFechas_Proyecto SET fechas_actuales=0 where id_proyecto=".$Proy;
						$cursorIn1 = mssql_query($sqlIn1);					
						if($cursorIn1!="")
						{								
							//FECHA DE INICIO Y FINAL
							$sqlIn1 ="insert into EFFechas_Proyecto (id_fecha_proyecto, id_proyecto, id_prorroga, fecha_inicio_proyecto, fecha_final_proyecto, fechas_actuales ,usuarioGraba,fechaGraba) 
							values( (select isnull(MAX(id_fecha_proyecto),0)+1 id_proye  from EFFechas_Proyecto WHERE id_proyecto=".$Proy." ) , ".$Proy." ,NULL, '".$_POST["Finicio"]."', '".$_POST["Final"]."', 1 ," . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
							$cursorIn1 = mssql_query($sqlIn1);		
		
		//echo "4. EFFechas_Proyecto <br>".$sqlIn1." ------ ".mssql_get_last_message()." <br>";											
						}
					}
					
					//SI SE HA CAMBIADO EL PAIS, DEPT O MUNICIPIO
					if( ( ($Pais!=$PaisAnt)||($Depto!=$DeptoAnt) ||($Municipio!=$MunicipioAnt) ) && ($cursorIn1!=""))
					{		
		
						$sql_veri="delete from EFUbicacion_Proyectos WHERE id_proyecto=".$Proy;
						$cursorIn1=mssql_query($sql_veri);						
		//echo "5.1  ".$sql_veri." ------ ".mssql_get_last_message()." <br>";							
						if($cursorIn1!="")
						{					
								//CAMBIA UBICACION DEL PROY
								$sqlIn1 ="insert into EFUbicacion_Proyectos ( id_proyecto, id_pais, id_departamento, id_municipio, usuarioGraba,fechaGraba) 
								values(".$Proy." , '".$Pais."' ,'".$Depto."', '".$Municipio."', " . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
								$cursorIn1 = mssql_query($sqlIn1);	
								
		//echo "5. EFUbicacion_Proyectos <br>".$sqlIn1." ------ ".mssql_get_last_message()." <br>";																		
						}
					}
					
					//SI LA FORMA DE EJECUCION ES INDIVIDUAL, Y SE HA CAMBIADO LA EMPRESA ENCARGADA
					if(($Ejecucion==1)&&($Empresa!=$EmpresaAnt) && ($cursorIn1!=""))
					{ 				
							$sqlIn1 =" UPDATE EFConsorcios_Empresas SET id_empresa=".$Empresa.", usuarioMod=" . $_SESSION["sesUnidadUsuario"] . ", fechaMod=getdate() where id_proyecto=".$Proy." and valores_actuales=1 and id_empresa=".$EmpresaAnt;
							$cursorIn1 = mssql_query($sqlIn1);
							
		//echo "6. EFConsorcios_Empresas <br>".$sqlIn1." ------ ".mssql_get_last_message()." <br>";																													
					}
		//echo "<BR>INGRESAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA<BR>"			;
					//SI SE HA CAMBIADO LA FORMA DE EJECUCION
					if(($EjecucionAnt!=$Ejecucion)&& ($cursorIn1!=""))
					{
						//SI LA FORMA DE EJECUCION ANTERIOR ERA INDIVIDUAL
						if($EjecucionAnt==1)
						 {
							 //SE ELIMINA EL REGISTRO DE DE LA TABLA CONSORCIOS EMPRESAS 
							$sql_veri="delete from EFConsorcios_Empresas WHERE id_proyecto=".$Proy;
							$cursorIn1=mssql_query($sql_veri);			
		
		//echo "7. EFConsorcios_Empresas <br>".$sqlIn1." ------ ".mssql_get_last_message()." <br>";
							 
						 }
						//SI LA FORMA DE EJECUCION ANTERIOR ERA CONSORCIO / UNION TEMPORAL Y NO SE HA CAMBIADO EL VALOR DEL PROYECTO
						if((($EjecucionAnt==2)||($EjecucionAnt==3)) && ($Valor==$ValorAnt) )
						 {
							//SI LA FORMA DE EJECUCION ACTUAL ES INDIVIDUAL
							if($Ejecucion==1)
							 {		
		/*					 
								//ACTUALIZA LAS valores_actuales DEL LOS REGISTROS VIEJOS DE LA TABLA EFConsorcios_Empresas
								$sqlIn1 =" UPDATE EFConsorcios_Empresas SET valores_actuales=0 where id_proyecto=".$Proy; 
								$cursorIn1 = mssql_query($sqlIn1);									
		*/						
								if($cursorIn1!="")
								{									
									//SE REGISTRA EN LA TABLA CONSORCIOS LA EMPRESA CON EL % PARTICIPACION DEL 100%
									$sqlIn1 ="insert into EFConsorcios_Empresas ( id_consorcios_empresas, id_proyecto, id_empresa, porcentaje_participacion, empresa_lider, valor_contrato_porcentaje, valor_final_contrato, valor_final_porcentaje, valores_actuales, usuarioGraba,fechaGraba) 
									values(1, '".$Proy."', ".$Empresa.", 100, 1, ".$Valor.", ".$Valor.", ".$Valor.", 1, " . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
									$cursorIn1 = mssql_query($sqlIn1);	
									
		//echo "8. EFConsorcios_Empresas <br>".$sqlIn1." ------ ".mssql_get_last_message()." <br>";
								}			 			 
							 }
						 }
					}	
		
					if(($Valor!=$ValorAnt) && ($cursorIn1!="") )
					{
						//ACTUALIZA LAS valores_actuales DEL LOS REGISTROS VIEJOS
						$sqlIn1 =" UPDATE EFValores_Contrato SET valores_actuales=0 where id_proyecto=".$Proy;
						$cursorIn1 = mssql_query($sqlIn1);					
						if($cursorIn1!="")
						{											
							//VALOR DEL PROYECTO
							$sqlIn1 ="insert into EFValores_Contrato ( id_valores_proyecto, id_proyecto, tipo, valor_contrato, valores_actuales, usuarioGraba,fechaGraba) 
							values( (select isnull(MAX(id_valores_proyecto),0)+1 id_proye  from EFValores_Contrato WHERE id_proyecto=".$Proy." ) , '".$Proy."', 3 ,".$Valor.", 1, " . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
							$cursorIn1 = mssql_query($sqlIn1);			
							
		//echo "9. EFValores_Contrato <br>".$sqlIn1." ------ ".mssql_get_last_message()." <br>";
							if($cursorIn1!="")
							{					
								//SI LA FORMA DE EJECUCION ES INDIVIDUAL
								if($Ejecucion==1)	
								{
									//ACTUALIZA LAS valores_actuales DEL LOS REGISTROS VIEJOS DE LA TABLA EFConsorcios_Empresas
									$sqlIn1 =" UPDATE EFConsorcios_Empresas SET valores_actuales=0 where id_proyecto=".$Proy; 
									$cursorIn1 = mssql_query($sqlIn1);									
									if($cursorIn1!="")
									{									
										//SE REGISTRA EN LA TABLA CONSORCIOS LA EMPRESA CON EL % PARTICIPACION DEL 100%
										$sqlIn1 ="insert into EFConsorcios_Empresas ( id_consorcios_empresas, id_proyecto, id_empresa, porcentaje_participacion, empresa_lider, valor_contrato_porcentaje, valor_final_contrato, valor_final_porcentaje, valores_actuales, usuarioGraba,fechaGraba) 
										values( (select isnull(MAX(id_consorcios_empresas),0)+1 id_proye  from EFConsorcios_Empresas WHERE id_proyecto=".$Proy." ) , '".$Proy."', ".$Empresa.", 100, 1, ".$Valor.", ".$Valor.", ".$Valor.", 1, " . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
										$cursorIn1 = mssql_query($sqlIn1);	
										
		//echo "10. EFConsorcios_Empresas <br>".$sqlIn1." ------ ".mssql_get_last_message()." <br>";
									}
								}
							}
						}
					}
					
					if(($Estado!=$EstadoAnt) && ($cursorIn1!=""))
					{
						//ACTUALIZA LAS valores_actuales DEL LOS REGISTROS VIEJOS DE LA TABLA EFEstados_Proy_Proyectos
						$sqlIn1 =" UPDATE EFEstados_Proy_Proyectos SET estado_actual=0 where id_proyecto=".$Proy; 
						$cursorIn1 = mssql_query($sqlIn1);									
						if($cursorIn1!="")
						{													
							//ESTADO DEL PROYECTO
							$sqlIn1 ="insert into EFEstados_Proy_Proyectos ( id_estados_proy_proyectos, id_estado_proy, id_proyecto, fecha_estado, estado_actual, usuarioGraba,fechaGraba) 
							values(  (select isnull(MAX(id_estados_proy_proyectos),0)+1 id_proye  from EFEstados_Proy_Proyectos WHERE id_proyecto=".$Proy." ), ".$Estado." , '".$Proy."' ,getdate(), 1, " . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
							$cursorIn1 = mssql_query($sqlIn1);	
		//echo "11. EFEstados_Proy_Proyectos <br>".$sqlIn1." ------ ".mssql_get_last_message()." <br>";
						}
					}
					
				}
							
				if  (trim($cursorIn1) != "")  {
					mssql_query("commit transaction");		
					echo ("<script>alert('Operaci\u00f3n realizada con exito');</script>"); 					
				} 
				else {
					mssql_query("rollback  transaction");		
					echo ("<script>alert('Error durante la operaci\u00f3n');</script>");
				}			
			
				echo ("<script>window.close(); MM_openBrWindow('infoG.php?Proy=".$Proy."','EF','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");			
		
				
			}
			else
			{
		//echo "INGRESSA cant_nom = ".$cant_nom." cant_veri_seven = ".$cant_veri_seven."<br>";
				$msg="";
				if( $cant_nom>0)
					$msg="Ya existe un proyecto con ese nombre (Nombre largo), por favor asign\u00e9 un nombre diferente. \\n";		 
					
				if($cant_veri_seven>0)
					$msg.="Ya se le ha asignado este c\u00f3digo seven nuevo a un proyecto, por favor asign\u00e9 un c\u00f3digo diferente.";		
				
				echo ("<script>alert('".$msg."');</script>"); 						
			}
	}
	//ELIMINA EL PROYECTO
	if($acc==3)
	{
 		mssql_query("begin transaction");				
		
		$cursorIn1 =mssql_query(" delete from EFEstados_Proy_Proyectos  where id_proyecto=".$Proy); 
		if($cursorIn1!="")	
		{
			$cursorIn1 =mssql_query(" delete from EFValores_Contrato  where id_proyecto=".$Proy); 
			if($cursorIn1!="")	
			{
				$cursorIn1 =mssql_query(" delete from EFConsorcios_Empresas  where id_proyecto=".$Proy); 
				if($cursorIn1!="")	
				{
					$cursorIn1 =mssql_query(" delete from EFConsorcios  where id_proyecto=".$Proy); 
					if($cursorIn1!="")	
					{
						$cursorIn1 =mssql_query(" delete from EFUbicacion_Proyectos  where id_proyecto=".$Proy); 
						if($cursorIn1!="")	
						{
							$cursorIn1 =mssql_query(" delete from EFFechas_Proyecto  where id_proyecto=".$Proy); 
							if($cursorIn1!="")	
							{
								$cursorIn1 =mssql_query(" delete from EFClientes_Proyectos  where id_proyecto=".$Proy); 
								if($cursorIn1!="")	
								{
									$cursorIn1 =mssql_query(" delete from EFNombres_Proyectos  where id_proyecto=".$Proy); 
									if($cursorIn1!="")	
									{
										$cursorIn1 =mssql_query(" delete from EFProyectos  where id_proyecto=".$Proy); 											
									}																		
								}									
							}								
						}							
					}						
				}				
			}						
		}
		if  (trim($cursorIn1) != "")  {
			mssql_query("commit transaction");		
			echo ("<script>alert('Operaci\u00f3n realizada con exito');</script>"); 					
		} 
		else {
			mssql_query("rollback  transaction");		
			echo ("<script>alert('Error durante la operaci\u00f3n');</script>");
		}			
	
		echo ("<script>window.close(); MM_openBrWindow('index.php','EF','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");				
	}
}

$dis="";
if($acc==3)
	$dis="disabled";

?>

<script type="text/javascript">
function valida()
{
 
	var campos_tex = ["ProyL","ProyC","SPEE","SEVE","Cod","SEVENN","SEVENA","Cliente","NoContrato","Finicio","Final","Pais","Depto","Municipio","Ejecucion","Empresa","TipoP","Moneda","Valor","FPago","TRM","Estado"];	
	
	var error=0;
	
	error=valida_campos(campos_tex,1);
	
	error+=valida_campos("",5);
//alert(" ------ "+error)	;
	//SI NO SE PRESENTARON PROBLEMAS DE VALIDACION
	if(error==0)
	{
		document.formulario.recarga.value="2";
		document.formulario.submit();
	}
	
}
</script>

<?php
if($recarga=="")
{
	//CONSULTA LA INFO DEL PROYECTO
	$sql_info_proy="select EFProyectos.*, nombre_largo_proyecto, nombre_corto_proyecto, EFClientes_Proyectos.id_cliente, EFEstados_Proy_Proyectos.id_estado_proy, EFUbicacion_Proyectos.*,  CONVERT(varchar, EFFechas_Proyecto.fecha_inicio_proyecto,101) fecha_inicio_proyecto, CONVERT(varchar, EFFechas_Proyecto.fecha_final_proyecto,101)  fecha_final_proyecto, EFValores_Contrato.valor_contrato
	from EFProyectos
	inner join EFNombres_Proyectos on EFProyectos.id_proyecto=EFNombres_Proyectos.id_proyecto and nombre_actual=1
	inner join EFClientes_Proyectos on EFClientes_Proyectos.id_proyecto=EFProyectos.id_proyecto
	inner join EFEstados_Proy_Proyectos on EFProyectos.id_proyecto=EFEstados_Proy_Proyectos.id_proyecto and EFEstados_Proy_Proyectos.estado_actual=1
	inner join EFUbicacion_Proyectos on EFUbicacion_Proyectos.id_proyecto=EFProyectos.id_proyecto
	inner join EFValores_Contrato on EFValores_Contrato.id_proyecto=EFProyectos.id_proyecto and EFValores_Contrato.valores_actuales=1
	inner join EFFechas_Proyecto on EFFechas_Proyecto.id_proyecto=EFProyectos.id_proyecto and fechas_actuales=1	
	WHERE EFProyectos.id_proyecto=".$Proy;
	$cur_info_proy=mssql_query($sql_info_proy);
	
	//echo $sql_info_proy." --- ".mssql_get_last_message();
	
	$datos_info_proy=mssql_fetch_array($cur_info_proy);
//	$ID=$datos_info_proy["id_proyecto"];
	$ProyL=$datos_info_proy["nombre_largo_proyecto"];
	$ProyC=$datos_info_proy["nombre_corto_proyecto"];
	$SPEE=$datos_info_proy["certificado_SPEEDWARE"];
	$SEVE=$datos_info_proy["certificado_SEVEN"];
	$Cod=$datos_info_proy["codigo"];
	$SEVENN=$datos_info_proy["codigo_SEVEN"];
	$SEVENA=$datos_info_proy["codigo_SEVEN_anterior"];
	$Cliente=$datos_info_proy["id_cliente"];
	
	$NoContrato=$datos_info_proy["numero_contrato"];
	$Finicio=$datos_info_proy["fecha_inicio_proyecto"];
	$Final=$datos_info_proy["fecha_final_proyecto"];
	$Pais=$datos_info_proy["id_pais"];
	$Depto=$datos_info_proy["id_departamento"];
	
	$Municipio=$datos_info_proy["id_municipio"];
	$Ejecucion=$datos_info_proy["id_tipo_ejecucion"];
	$Empresa=$datos_info_proy["id_empresa"];
	$TipoP=$datos_info_proy["id_tipo_proyecto"];
	$Moneda=$datos_info_proy["id_moneda"];
	
	$Valor=$datos_info_proy["valor_contrato"];
	$FPago=$datos_info_proy["id_forma_pago"];
	$TRM=$datos_info_proy["TRM"];
	$Estado=$datos_info_proy["id_estado_proy"];
	
	//SE CARGAN LOS VALORES DE LAS VARIABLES QUE ALMACENARAN LOS DATOS INICIALES
	$ProyLAnt=$datos_info_proy["nombre_largo_proyecto"];
	$ProyCAnt=$datos_info_proy["nombre_corto_proyecto"];
	$SPEEAnt=$datos_info_proy["certificado_SPEEDWARE"];
	$SEVEAnt=$datos_info_proy["certificado_SEVEN"];
	$CodAnt=$datos_info_proy["codigo"];
	$SEVENNAnt    =$datos_info_proy["codigo_SEVEN"];
	$SEVENAAnt=$datos_info_proy["codigo_SEVEN_anterior"];
	$ClienteAnt=$datos_info_proy["id_cliente"];
	$NoContratoAnt        =$datos_info_proy["numero_contrato"];
	$FinicioAnt=$datos_info_proy["fecha_inicio_proyecto"];
	$FinalAnt  =$datos_info_proy["fecha_final_proyecto"];
	$PaisAnt=$datos_info_proy["id_pais"];
	$DeptoAnt   =$datos_info_proy["id_departamento"];
	$MunicipioAnt    =$datos_info_proy["id_municipio"];
	$EjecucionAnt  =$datos_info_proy["id_tipo_ejecucion"];	
	$EmpresaAnt  =$datos_info_proy["id_empresa"];
	$MonedaAnt=$datos_info_proy["id_moneda"];
	$ValorAnt    =$datos_info_proy["valor_contrato"];
	$FPagoAnt=$datos_info_proy["id_forma_pago"];	
	$TRMAnt=$datos_info_proy["TRM"];
	$EstadoAnt=$datos_info_proy["id_estado_proy"];
}
/*
else
{	  

  $SEVENN=$SEVENNAnt;
  $ProyL=$ProyLAnt;
}
*/
//CONSULTA SI EL PROYECTO TIENE ASOCIADO: ADICIONES, PRORROGAS Y VALORES FACTURADOS
$dato_val_if=mssql_fetch_array(mssql_query(" select COUNT(*) cantA from EFAdicionales WHERE id_proyecto=".$Proy));
$cant_adiciones=( (int) $dato_val_if["cantA"]);

$dato_val_if=mssql_fetch_array(mssql_query(" select COUNT(*) cantA from EFValores_Facturados WHERE id_proyecto=".$Proy));
$cant_valores_f=( (int) $dato_val_if["cantA"]);			

$dato_val_if=mssql_fetch_array(mssql_query(" select COUNT(*) cantA from EFProrrogas WHERE id_proyecto=".$Proy));
$cant_prorrogas=( (int) $dato_val_if["cantA"]);


//SI EL TIPO DE EJECUCION ES CONSORCIO Y/O UNION TEMPORAL
if(($datos_info_proy["id_tipo_ejecucion"]==2) || ($datos_info_proy["id_tipo_ejecucion"]==3) )
{
	//CONSULTA SI EXISTE ASOCIADO UN CONSORCIO AL PROYECTO  
	$dato_val_if=mssql_fetch_array(mssql_query("select COUNT(*) cantA from EFConsorcios WHERE id_proyecto=".$Proy));
	$cant_consorc=( (int) $dato_val_if["cantA"]);			
	/*
	$dato_val_if=mssql_fetch_array(mssql_query("select COUNT(*) cantA from EFConsorcios_Empresas WHERE id_proyecto=".$Proy));
	$cant_consorcEm=( (int) $dato_val_if["cantA"]);			
	*/
	$cant_consorcios=0;
	//$cant_consorcios= $cant_consorcEm+$cant_consorc;
	$cant_consorcios= $cant_consorc;
}


//REALIZA LA SUMATORIA DEL LA CANTIDAD DE PRORROGAS, ADICIONES Y VALORES FACTURADOS
$tota_valoresF_prorroga_adiciones=$cant_valores_f+$cant_prorrogas+$cant_adiciones;
?>

<form id="formulario" name="formulario" method="POST">

  <div class="form-group" id="">
    <label for="">Id</label>
    <div class="desabilitados">
		<?=$Proy ?> 
    </div>
  </div>
  
  <div class="form-group" id="divProyL">
    <label for="">Proyecto (Nombre largo)</label>
    <input type="text" class="form-control" id="ProyL" name="ProyL" value="<?=$ProyL ?>" placeholder="Proyecto (Nombre largo)" size="20px" autofocus <?=$dis ?>>
     <span id="helpProyL" class="help-block" style="display:none;" >El nombre del proyecto es obligatorio.</span>
    <input  type="hidden" id="ProyLAnt" name="ProyLAnt" value="<?=$ProyLAnt ?>">
  </div>

  <div class="form-group" id="divProyC">
    <label for="">Proyecto (Nombre corto)</label>
    <input type="text" class="form-control" id="ProyC" name="ProyC" value="<?=$ProyC ?>"  placeholder="Proyecto (Nombre corto)"  <?=$dis ?>>
     <span id="helpProyC" class="help-block" style="display:none;" >El nombre del proyecto es obligatorio.</span>
    
    <input  type="hidden" id="ProyCAnt" name="ProyCAnt" value="<?=$ProyCAnt ?>">
  </div>
  
    <div class="form-group" id="divSPEE">
    <label for="">Certificado  SPEEDWARE</label>
    <input type="text" class="form-control" id="SPEE" name="SPEE" value="<?=$SPEE ?>"  placeholder="Cert. SPEEDWARE" onKeyPress="return acceptNum(event)"  <?=$dis ?>>
     <span id="helpSPEE" class="help-block" style="display:none;" >El Cert. SPEEDWARE es obligatorio.</span>
     <input  type="hidden" id="SPEEAnt" name="SPEEAnt" value="<?=$SPEEAnt ?>">
  </div>
  
    <div class="form-group" id="divSEVE">
    <label for="">Certificado  SEVEN</label>
    <input type="text" class="form-control" id="SEVE" name="SEVE" value="<?=$SEVE ?>"  placeholder="Cert. SEVEN" onKeyPress="return acceptNum(event)"  <?=$dis ?>>
     <span id="helpSEVE" class="help-block" style="display:none;" >El Cert. SEVEN es obligatorio.</span>
     <input  type="hidden" id="SEVEAnt" name="SEVEAnt" value="<?=$SEVEAnt ?>">
  </div>
  
    <div class="form-group" id="divCod">
     <label for="">C&oacute;digo</label>
       <input type="text" class="form-control" id="Cod" name="Cod" value="<?=$Cod ?>"  placeholder="C&oacute;digo" onKeyPress="return acceptNum(event)"  <?=$dis ?>>
     <span id="helpCod" class="help-block" style="display:none;" >El C&oacute;d. es obligatorio.</span>
     <input  type="hidden" id="CodAnt" name="CodAnt" value="<?=$CodAnt ?>">
  </div>
  
    <div class="form-group" id="divSEVENN">
    <label for="">C&oacute;digo SEVEN nuevo</label>
    <input type="text" class="form-control" id="SEVENN"  name="SEVENN" value="<?=$SEVENN ?>"  placeholder="C&oacute;digo SEVEN nuevo" onKeyPress="return acceptNum(event)"  <?=$dis ?>>
     <span id="helpSEVENN" class="help-block" style="display:none;" >El C&oacute;digo SEVEN nuevo es obligatorio.</span>
     <input  type="hidden" id="SEVENNAnt" name="SEVENNAnt" value="<?=$SEVENNAnt ?>">
  </div>

    <div class="form-group" id="divSEVENA">
    <label for="">C&oacute;digo SEVEN anterior</label>
    <input type="text" class="form-control" id="SEVENA"  name="SEVENA" value="<?=$SEVENA ?>"  placeholder="C&oacute;digo SEVEN anterior" onKeyPress="return acceptNum(event)"  <?=$dis ?>>
     <span id="helpSEVENA" class="help-block" style="display:none;" >El C&oacute;digo SEVEN anterior es obligatorio.</span>
     <input  type="hidden" id="SEVENAAnt" name="SEVENAAnt" value="<?=$SEVENAAnt ?>">
  </div>

    <div class="form-group" id="divCliente">
    <label for="">Cliente</label>
        <select class="form-control" id="Cliente" name="Cliente"  <?=$dis ?> >
          <option selected value="">Seleccione el cliente</option>        
        <?php
        	$cur_clie=mssql_query("SELECT * from EFClientes where estado=1 order by cliente ");
			while($datos_cli=mssql_fetch_array($cur_clie))
			{
				$sel="";
				if($Cliente== $datos_cli["id_cliente"])
					$sel="selected";
		?>
          		<option value="<?=$datos_cli["id_cliente"]; ?>" <?=$sel ?> ><?=$datos_cli["cliente"]; ?></option>
        <?php
			}
		?>  
        </select>
     <span id="helpCliente" class="help-block" style="display:none;" >Por favor seleccione el cliente.</span>
     <input  type="hidden" id="ClienteAnt" name="ClienteAnt" value="<?=$ClienteAnt ?>">
  </div>
  
    <div class="form-group" id="divNoContrato">
    <label for="">No. Contrato</label>
    <input type="text" class="form-control" id="NoContrato" name="NoContrato" value="<?=$NoContrato ?>"  placeholder="No. Contrato"  <?=$dis ?>>
     <span id="helpNoContrato" class="help-block" style="display:none;" >El No. Contrato es obligatorio.</span>
     <input  type="hidden" id="NoContratoAnt" name="NoContratoAnt" value="<?=$NoContratoAnt ?>">
  </div>
  
    <div class="form-group" id="divFinicio">
    <label for="">Fecha de Inicio</label>    
	<div class="container-fluid">
      <div class="col-sm-3">
       <div class="input-group">
<?PHP
		$disab="";
		
		//SI EL PROYECTO TIENE ASOCIADOS REGISTROS DE PRORROGAS, ADICIONES Y VALORES FACTURADOS		
		if($tota_valoresF_prorroga_adiciones>0)
		{
			//SE DEJARA EL CAMPO COMO SOLO LECTURA Y NO SE PODRA CMABIAR LA FORMA FECHA DE INICIO Y FINALIZACION
			$disab="disabled";
		}			

		//SI SE PERMITE CAMBIAR LA FECHA
		if($disab=="")
		{
?>       
     	   <input class="form-control" id="Finicio" name="Finicio" onClick="FUNC();" placeholder="MM/DD/YYYY" value="<?=$Finicio ?>" readonly type="text"  <?=$dis ?> />
            <div class="input-group-addon">
              <i class="glyphicon glyphicon-calendar">
              </i>
            </div>           
<?PHP
		}
		else
		{		
?>
            <div class="desabilitados">
                <?=$Finicio ?>&nbsp; 
                  <i class="glyphicon glyphicon-info-sign" title="No se permite cambiar la fecha, porque ya se han registrado prorrogas, adiciones o valores facturados. ">
                  </i>            
            </div>
            
	        <input  type="hidden" id="Finicio" name="Finicio" value="<?=$Finicio ?>">
<?php			
		}
?>        
        <input  type="hidden" id="FinicioAnt" name="FinicioAnt" value="<?=$FinicioAnt ?>">
        
       </div>
      </div>
    </div>   

     <span id="helpFinicio" class="help-block" style="display:none;" >La fecha de inicio es obligatoria.</span>
  </div>


    <div class="form-group" id="divFinal">
    <label for="">Fecha de Finalizaci&oacute;n</label>    
	<div class="container-fluid">
      <div class="col-sm-3">
       <div class="input-group">
       
<?PHP
		//SI SE PERMITE CAMBIAR LA FECHA
		if($disab=="")
		{
?>              
     	   <input class="form-control" id="Final" name="Final" placeholder="MM/DD/YYYY"  value="<?=$Final ?>" readonly type="text"  <?=$dis ?> />
            <div class="input-group-addon">
              <i class="glyphicon glyphicon-calendar">
              </i>
            </div>               
<?PHP
		}	
		else
		{
?>
            <div class="desabilitados">
                <?=$Final ?>&nbsp; 
                  <i class="glyphicon glyphicon-info-sign" title="No se permite cambiar la fecha, porque ya se han registrado prorrogas, adiciones o valores facturados. ">
                  </i>            
            </div>

	        <input  type="hidden" id="Final" name="Final" value="<?=$Final ?>">
           
<?php			
		}
?>       
        <input  type="hidden" id="FinalAnt" name="FinalAnt" value="<?=$FinalAnt ?>">
    
       </div>
      </div>
    </div>   
        
     <span id="helpFinal" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n es obligatoria.</span>
     <span id="helpFinal2" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n del proyecto es menor a la fecha de inicio, por favor verifique.</span>     
          
  </div>
   
  <div class="form-group" id="divPais">
    <label for="">Pa&iacute;s</label>
    
    <select class="form-control" id="Pais"  name="Pais" onChange="document.formulario.submit()"  <?=$dis ?>>
      <option selected value="">Seleccione el Pa&iacute;s</option>        
    <?php
        $cur_clie=mssql_query("SELECT * from EFPaises where estado=1 order by pais ");
        while($datos_cli=mssql_fetch_array($cur_clie))
        {
				$sel="";
				if($Pais== $datos_cli["id_pais"])
					$sel="selected";			
    ?>
            <option value="<?=$datos_cli["id_pais"]; ?>" <?=$sel ?> ><?=$datos_cli["pais"]; ?></option>
    <?php
        }
    ?>  
    </select>    
     <span id="helpPais" class="help-block" style="display:none;" >Por favor seleccione el pa&iacute;s.</span>
    <input  type="hidden" id="PaisAnt" name="PaisAnt" value="<?=$PaisAnt ?>">
  </div>
  

  <div class="form-group" id="divDepto">
    <label for="">Departamento</label>
    
    <select class="form-control" id="Depto" name="Depto" onChange="document.formulario.submit()"  <?=$dis ?>>
      <option selected value="">Seleccione el Departamento</option>        
    <?php
        $cur_clie=mssql_query("SELECT * from EFDepartamentos where estado=1 and id_pais =".$Pais." order by  departamento ");
        while($datos_cli=mssql_fetch_array($cur_clie))
        {
			$sel="";
			if($Depto== $datos_cli["id_departamento"])
				$sel="selected";		
    ?>
            <option value="<?=$datos_cli["id_departamento"]; ?>" <?=$sel ?> ><?=$datos_cli["departamento"]; ?></option>
    <?php
        }
    ?>  
    </select>    
     <span id="helpDepto" class="help-block" style="display:none;" >Por favor seleccione el Departamento.</span>
    <input  type="hidden" id="DeptoAnt" name="DeptoAnt" value="<?=$DeptoAnt ?>">
  </div>
  

  <div class="form-group" id="divMunicipio">
    <label for="">Municipio</label>
    
    <select class="form-control" id="Municipio" name="Municipio"  <?=$dis ?>>
      <option selected value="">Seleccione el Municipio</option>        
    <?php
        $cur_clie=mssql_query("SELECT * from EFMunicipios where estado=1 and id_pais =".$Pais." and id_departamento=".$Depto." order by  municipio ");
		
        while($datos_cli=mssql_fetch_array($cur_clie))
        {
			$sel="";
			if($Municipio== $datos_cli["id_municipio"])
				$sel="selected";		
    ?>
            <option value="<?=$datos_cli["id_municipio"]; ?>" <?=$sel ?> ><?=$datos_cli["municipio"]; ?></option>
    <?php
        }
    ?>  
    </select>    
    
     <span id="helpMunicipio" class="help-block" style="display:none;" >Por favor seleccione el Municipio.</span>
    <input  type="hidden" id="MunicipioAnt" name="MunicipioAnt" value="<?=$MunicipioAnt ?>">
  </div>  

	<div class="form-group" id="divTipoP" >
    <label for="">Tipo de proyecto</label>
    
    <select class="form-control" id="TipoP" name="TipoP"  <?=$dis ?>>
      <option selected value="">Seleccione el tipo de proyecto</option>        
    <?php
        $cur_clie=mssql_query("SELECT * from EFTipos_Proyecto where estado=1 ");
		
        while($datos_cli=mssql_fetch_array($cur_clie))
        {
			$sel="";
			if($TipoP== $datos_cli["id_tipo_proyecto"])
				$sel="selected";		
    ?>
            <option value="<?=$datos_cli["id_tipo_proyecto"]; ?>" <?=$sel ?> ><?=$datos_cli["tipo_proyecto"]; ?></option>
    <?php
        }
    ?>  
    </select>    
    
     <span id="helpTipoP" class="help-block" style="display:none;" >Por favor seleccione el tipo de proyecto.</span>
     <input  type="hidden" id="TipoPAnt" name="TipoPAnt" value="<?=$TipoPAnt ?>">
  </div>     

  <div class="form-group" id="divEjecucion">
    <label for="">Forma de Ejecuci&oacute;n</label>

<?PHP
		$mensa="";
		$disab="";
		//SIE EL TIPO DE EJECUCION ES INDIVIDUAL
		if($datos_info_proy["id_tipo_ejecucion"]==1)
		{	
			//SI EL PROYECTO TIENE ASOCIADOS REGISTROS DE PRORROGAS, ADICIONES Y VALORES FACTURADOS		
			if($tota_valoresF_prorroga_adiciones>0)
			{
				//SE DEJARA EL CAMPO COMO SOLO LECTURA Y NO SE PODRA CMABIAR LA FORMA DE EJECUCION
				$disab="readonly ";
				$mensa="No se permite cambiar la forma de ejecuci&oacute;n, porque ya se han registrado prorrogas, adiciones o valores facturados.";
			}			
		}
		
		//SI EL TIPO DE EJECUCION ES CONSORCIO Y/O UNION TEMPORAL
		if(($datos_info_proy["id_tipo_ejecucion"]==2)||($datos_info_proy["id_tipo_ejecucion"]==3))
		{	
			//SI EL PROYECTO TIENE ASOCIADOS REGISTROS DE PRORROGAS, ADICIONES Y VALORES FACTURADOS		
			// O SI YA EXISTE UN CONSORCIO DEFINIDO
			if(($tota_valoresF_prorroga_adiciones>0)|| ($cant_consorcios>0))
			{
				//SE DEJARA EL CAMPO COMO SOLO LECTURA Y NO SE PODRA CMABIAR LA FORMA DE EJECUCION
				$disab="readonly ";
				$mensa="No se permite cambiar la forma de ejecuci&oacute;n, porque ya se han registrado prorrogas, adiciones, valores facturados o un consorcio.";				
			}					
		}


		$sql_eje="SELECT * from EFTipos_Ejecucion where estado=1 ";
		if($disab!="")		
			$sql_eje.=" and id_tipo_ejecucion=".$Ejecucion;		
			
         $cur_clie=mssql_query($sql_eje);	
		 //SI NO SE PERMITE EDITAR
		 if($disab!="")				
		 {
			 $datos_cli=mssql_fetch_array($cur_clie);
?>
            <div class="desabilitados">
                <?=$datos_cli["ejecucion"]; ?>&nbsp; 
                  <i class="glyphicon glyphicon-info-sign" title="<?=$mensa ?>">
                  </i>                
            </div>
	        <input  type="hidden" id="Ejecucion" name="Ejecucion" value="<?=$Ejecucion ?>">          			 
<?php            
		 }
		 else //SI SE PERMITE EDITAR
		 {
?>    

    <select class="form-control" id="Ejecucion" name="Ejecucion" <?=$disab ?>  <?=$dis ?> >
      <option selected value="">Seleccione forma de ejecuci&oacute;n</option>        
            <?php			
                
                while($datos_cli=mssql_fetch_array($cur_clie))
                {
                    $sel="";
                    if($Ejecucion== $datos_cli["id_tipo_ejecucion"])
                        $sel="selected";		
            ?>
      <option value="<?=$datos_cli["id_tipo_ejecucion"]; ?>" <?=$sel ?>  ><?=$datos_cli["ejecucion"]; ?></option>
            <?php
                }
		 }
            ?>  
    </select>    
    <input type="hidden" name="EjecucionAnt" id="EjecucionAnt" value="<?=$EjecucionAnt ?>" >
     <span id="helpEjecucion" class="help-block" style="display:none;" >Por favor seleccione la forma de Ejecuci&oacute;n.</span>
  </div>   

 <div class="form-group" id="divEmpresa">
    <label for="">Empresa encargada</label>
<?php


		$disab="";
		$mensa="";
		//SI EL PROYECTO TIENE ASOCIADOS REGISTROS DE CONSORCIOS	
		if($cant_consorcios>0)
		{
			//SE DEJARA EL CAMPO COMO SOLO LECTURA Y NO SE PODRA CMABIAR LA EMPRESA ENCARGADA
			$disab="disabled";
			$mensa="No se permite cambiar la empresa encargada, porque ya se ha definido un consorcio.";
		}	

		$sql_clien="SELECT * from EFEmpresas where estado=1 and tipo=1 ";
		if($disab!="")		
			$sql_clien.=" and  id_empresa=".$Empresa;				
		$cur_clie=mssql_query($sql_clien);

		 //SI NO SE PERMITE EDITAR
		 if($disab!="")				
		 {
			 $datos_cli=mssql_fetch_array($cur_clie);
?>
            <div class="desabilitados">
                <?=$datos_cli["empresa"]; ?>&nbsp; 
                  <i class="glyphicon glyphicon-info-sign" title="<?=$mensa ?>">
                  </i>                
            </div>
    <input  type="hidden" id="Empresa" name="Empresa" value="<?=$Empresa ?>">          			 
<?php  
		 }
		 else  //SI SE PERMITE EDITAR
		 {
?>		 
            <select class="form-control" id="Empresa" name="Empresa" <?=$disab ?>  <?=$dis ?> >
              <option selected value="">Seleccione la empresa</option>        
            <?php
                
                
                while($datos_cli=mssql_fetch_array($cur_clie))
                {
                    $sel="";
                    if($Empresa== $datos_cli["id_empresa"])
                        $sel="selected";		
            ?>
                    <option value="<?=$datos_cli["id_empresa"]; ?>" <?=$sel ?> ><?=$datos_cli["empresa"]; ?></option>
            <?php
                }
            ?>  
            </select> 
<?php
		 }
?>               
    
     <span id="helpEmpresa" class="help-block" style="display:none;" >Por favor seleccione la Empresa encargada.</span>
     <input  type="hidden" id="EmpresaAnt" name="EmpresaAnt" value="<?=$EmpresaAnt ?>">
 </div>  
  
  
   <div class="form-group" id="divMoneda">
    <label for="">Moneda</label>
    
    <select class="form-control" id="Moneda" name="Moneda"  <?=$dis ?>>
      <option selected value="">Seleccione el tipo de moneda</option>        
    <?php
        $cur_clie=mssql_query("select * from EFMonedas where estado=1   order by  moneda");
		
        while($datos_cli=mssql_fetch_array($cur_clie))
        {
			$sel="";
			if($Moneda== $datos_cli["id_moneda"])
				$sel="selected";		
    ?>
            <option value="<?=$datos_cli["id_moneda"]; ?>" <?=$sel ?> ><?=$datos_cli["moneda"]; ?></option>
    <?php
        }
    ?>  
    </select>    
    
     <span id="helpMoneda" class="help-block" style="display:none;" >Por favor seleccione el tipo de Moneda.</span>
     <input  type="hidden" id="MonedaAnt" name="MonedaAnt" value="<?=$MonedaAnt ?>">
  </div>      

    <div class="form-group" id="divValor">
    <label for="">Valor del proyecto</label>

<?PHP
		$disab="";

		//SI EL PROYECTO TIENE ASOCIADOS REGISTROS DE PRORROGAS, ADICIONES Y VALORES FACTURADOS		
		// O SI YA EXISTE UN CONSORCIO DEFINIDO
		if(($tota_valoresF_prorroga_adiciones>0)|| ($cant_consorcios>0))
		{
			//SE DEJARA EL CAMPO COMO SOLO LECTURA Y NO SE PODRA CMABIAR EL VALOR DEL PROYECTO
			$disab="readonly";
		}			

		//SI SE PERMITE CAMBIAR EL VALOR
		if($disab=="")
		{
?>       
		      <input type="text" class="form-control" id="Valor" name="Valor" value="<?=$Valor ?>"  placeholder="Valor" onKeyPress="return acceptNum(event)"  <?=$dis ?> >
<?PHP
		}
		else
		{
?>
            <div class="desabilitados">
                <?=$Valor ?>&nbsp; 
                  <i class="glyphicon glyphicon-info-sign" title="No se permite cambiar el valor, porque ya se han registrado prorrogas, adiciones, valores facturados o un consorcio. ">
                  </i>                
            </div>

	        <input  type="hidden" id="Valor" name="Valor" value="<?=$Valor ?>">                          
<?php			
		}
?>    
    

     <span id="helpValor" class="help-block" style="display:none;" >El Valor del proyecto es obligatorio.</span>
     <input  type="hidden" id="ValorAnt" name="ValorAnt" value="<?=$ValorAnt ?>">
  </div>  
  

   <div class="form-group" id="divFPago">
    <label for="">Forma de pago</label>
    
    <select class="form-control" id="FPago" name="FPago" <?=$dis ?>>
      <option selected value="">Seleccione la forma de pago</option>        
    <?php
        $cur_clie=mssql_query("select * from EFFormas_pago where estado=1 ");
		
        while($datos_cli=mssql_fetch_array($cur_clie))
        {
			$sel="";
			if($FPago== $datos_cli["id_forma_pago"])
				$sel="selected";		
    ?>
            <option value="<?=$datos_cli["id_forma_pago"]; ?>" <?=$sel ?> ><?=$datos_cli["forma_pago"]; ?></option>
    <?php
        }
    ?>  
    </select>    

     <span id="helpFPago" class="help-block" style="display:none;" >Por favor seleccione la Forma de pago.</span>
     <input  type="hidden" id="FPagoAnt" name="FPagoAnt" value="<?=$FPagoAnt ?>">
  </div>      
  
    <div class="form-group" id="divTRM">
    <label for="">TRM</label>
    
    <input type="text" class="form-control" id="TRM" name="TRM" value="<?=$TRM ?>"  placeholder="TRM" onKeyPress="return acceptNumD(event)" <?=$dis ?>>
     <span id="helpTRM" class="help-block" style="display:none;" >El Valor del TRM es obligatorio.</span>
     <input  type="hidden" id="TRMAnt" name="TRMAnt" value="<?=$TRMAnt ?>">
  </div>    
<div class="form-group" id="divEstado">
    <label for="">Estado</label>

    <select class="form-control" id="Estado" name="Estado" <?=$dis ?>>
      <option selected value="">Seleccione el estado</option>        
    <?php

        $cur_esta=mssql_query("select * from EFEstados_Proy where estado=1  order by estado_proyecto");
		
        while($datos_cli=mssql_fetch_array($cur_esta))
        {
			$sel="";
			if($Estado== $datos_cli["id_estado_proy"])
				$sel="selected";		
    ?>
            <option value="<?=$datos_cli["id_estado_proy"]; ?>" <?=$sel ?> ><?=$datos_cli["estado_proyecto"]; ?></option>
    <?php
        }
    ?>  
    </select>    
    
     <span id="helpEstado" class="help-block" style="display:none;" >Por favor seleccione el estado del proyecto.</span>
    <input  type="hidden" id="EstadoAnt" name="EstadoAnt" value="<?=$EstadoAnt ?>">
</div>       
   <div style="text-align:right" >
   <?php
   		$mens_b="";
	   if($acc==2)
	   		$mens_b="Actualizar";	   		
	   if($acc==3)
	   		$mens_b="Eliminar";	   
	   
   ?>
      <button type="button" class="btn btn-primary" onClick="valida()" ><?=$mens_b ?></button>  
       <input name="recarga" type="hidden" id="recarga" value="1">
   </div>
   
</form>

    
<?php
	include("inferior.php"); 
?>