<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente

if($acc==2)
	$mens_v="Actualizar Prorroga";	   		
if($acc==3)
	$mens_v="Eliminar Prorroga";	   

banner(2,$mens_v);


//CONSULTA LA INFO DE LA PRORROGA
$sql_info_prorrog="select (U.apellidos+' '+U.nombre) nombreU,U.unidad, EFProrrogas.*, EFItems_Prorrogas_Adicionales.prorroga_adicion, EFDocumentos_Soporte.documento_soporte, CONVERT(varchar, EFProrrogas.fecha_inicio,101) fecha_inicios , CONVERT(varchar, EFProrrogas.fecha_final,101) fecha_finals  from 
EFProrrogas  
inner join EFItems_Prorrogas_Adicionales on EFItems_Prorrogas_Adicionales.id_item_prorroga_adicion=EFProrrogas.id_item_prorroga_adicion
inner join EFDocumentos_Soporte on EFDocumentos_Soporte.id_documento_soporte=EFProrrogas.id_documento_soporte
inner join HojaDeTiempo.dbo.Usuarios U on U.unidad=EFProrrogas.usuarioGraba	
where id_proyecto=".$Proy." and id_prorroga=".$prorro."
order by EFProrrogas.id_prorroga ";
$cur_proffo=mssql_query($sql_info_prorrog);

if($datos_proffo=mssql_fetch_array($cur_proffo))
{
	$Prorroga=$datos_proffo["id_item_prorroga_adicion"];
	$Finicios=$datos_proffo["fecha_inicios"];
	$Final=$datos_proffo["fecha_finals"];
	$Documento=$datos_proffo["id_documento_soporte"];
	$Valor=$datos_proffo["valor_prorroga"];
	$Observaciones=$datos_proffo["observaciones"];
}


if($recarga==2)
{	

	mssql_query("begin transaction");		
	
	//SI NO SE HA ASIGNADO UN VALOR A LA PRORROGA
	if($Valor=="")
		$Valor=0;
	
//echo $Prorroga.",'".$Finicios."','".$Final."',".$Documento." ,".$Valor.",'".$Observaciones."'," . $_SESSION["sesUnidadUsuario"] . ",getdate() <br>"  ;

	//CONSULTA LA INFO DEL PROYECTO
	$sql_proyect="select * from EFProyectos WHERE id_proyecto=".$Proy;
	$cursorproyect= mssql_query($sql_proyect);		
	$datos_proyect=mssql_fetch_array($cursorproyect );	
	$tipo_ejecucion=$datos_proyect["id_tipo_ejecucion"];

	
//if($acc==3)

	//ELIMINA la proRROGA	
	$sqlIn1 = " DELETE FROM EFProrrogas WHERE id_proyecto=".$Proy." AND id_prorroga=".$prorro;
	$cursorIn1 = mssql_query($sqlIn1);				
//echo $sqlIn1."<br>".mssql_get_last_message()."-----<BR>";	
	if($cursorIn1!="")
	{
		//SI LA PRORROGA TIENE ASOCIADO UN VALOR
		if(($Valor!=0)&&($Valor!=""))
		{
		
			//CONSULTA LA INFO DEL VALOR DEL CONTRATO MAS RECIENTE, ASOCIADA AL PROYECTO 	
			$sql_prorroga="select * from EFValores_Contrato where id_proyecto=".$Proy."
			AND id_valores_proyecto=( select MAX(id_valores_proyecto) from EFValores_Contrato where id_proyecto=".$Proy.")";
			$cursorIn1 = mssql_query($sql_prorroga);		
			$datos_prorroga=mssql_fetch_array($cursorIn1 );	
//echo $sql_prorroga."<br>".mssql_get_last_message()."-----<BR>";																
			if($cursorIn1!="")
			{
				//SI EL id_prorroga_adicion ES EL MISMO DE LA PRORROGA Y ES DE TIPO PRORROGA
				//SE IDENTIFICA QUE LA PRORROGA ES EL VALOR MAS RECIENTE (ULTIMO), QUE AFECTO EL VALOR DEL PROYECTO
				if(($datos_prorroga["id_prorroga_adicion"]==$prorro) && ($datos_prorroga["tipo"]=="1") )
				{
					//ELIMINA EEL VALOR DE la proRROGA	ACTUAL, QUE AFECTO EL VALOR DEL PROYECTO
					$sqlIn1 = " DELETE FROM EFValores_Contrato WHERE id_proyecto=".$Proy." AND id_prorroga_adicion=".$prorro." AND tipo=1 ";
					$cursorIn1 = mssql_query($sqlIn1);		
//echo $sqlIn1."<br>".mssql_get_last_message()."-----<BR>";																								
					if($cursorIn1!="")
					{
						//ACTUALIZA EL CAMPO valores_actuales=1 DEL ULTIMO  REGISTRO CON EL VALOR DEL PROYECTO
						$sqlIn1 = " UPDATE EFValores_Contrato SET valores_actuales=1 WHERE id_proyecto=".$Proy." AND id_valores_proyecto=( select MAX(id_valores_proyecto) from EFValores_Contrato where id_proyecto=".$Proy.")";
						$cursorIn1 = mssql_query($sqlIn1);								
//echo $sqlIn1."<br>".mssql_get_last_message()."-----<BR>";																								
						if($cursorIn1!="")
						{				
							//CONSULTA LAS EMPRESAS PERTENECIENTES A EL CONSORCIO, CUYO valores_actuales=1, CON EL FIN DE REALIZAR LOS CALCULOS CON EL 
							//EL VALOR ACTUAL DEL PROYECTO Y LOS % DE PARTICIPACION DEFINIDOS 
							$sql_empresas_consorcios="select *  from EFConsorcios_Empresas  WHERE id_proyecto=".$Proy." AND valores_actuales=1 ";
							$cursor_empresas_consorcios= mssql_query($sql_empresas_consorcios);
							
							//CONSULTA LA INFO DEL VALOR DEL CONTRATO MAS RECIENTE, ASOCIADA AL PROYECTO (LA QUE QUEDO ACUTALMENTE, DESPUES DE HABER ELIMINADO LA ANTERIOR)
							$sql_info_prorro_recien="select valor_contrato, id_valores_proyecto from EFValores_Contrato where id_proyecto=".$Proy." AND valores_actuales=1 ";
//							AND id_valores_proyecto=( select MAX(id_valores_proyecto) from EFValores_Contrato where id_proyecto=".$Proy.")";
							$cursor_info_prorro_recien= mssql_query($sql_info_prorro_recien);		
							$datos_info_prorro_recien=mssql_fetch_array($cursor_info_prorro_recien );
							$valor_proyecto=$datos_info_prorro_recien["valor_contrato"];
							$id_valores_proyecto=$datos_info_prorro_recien["id_valores_proyecto"];							
							
														
							//ELIMINA LOS REGISTROS ASOCIADOS, DE LAS EMPRESAS CUYO VALOR FUEA AFECTADO POR LA PRORROGA
							$sqlIn1 = " DELETE FROM EFConsorcios_Empresas WHERE id_proyecto=".$Proy." AND id_valores_proyecto=".$datos_prorroga["id_valores_proyecto"]." ";
							$cursorIn1 = mssql_query($sqlIn1);		
//echo $sqlIn1."<br>".mssql_get_last_message()."-----<BR>";																																							
							if($cursorIn1!="")
							{		
								while($datos_empresas_consorcios=mssql_fetch_array($cursor_empresas_consorcios ))
								{							
									if($cursorIn1!="")
									{									
										//CALCULA EL VALOR DEL % DE PARTICIPACION, DEACUERDO AL NUEVO VALOR DEL PROYECTO
										$valorParticipacion=(($valor_proyecto)*( (float) $datos_empresas_consorcios["porcentaje_participacion"]))/100;
										
//echo " valorParticipacion=".$valorParticipacion." =. valor_proyecto ".$valor_proyecto." ".( (float) $datos_empresas_consorcios["porcentaje_participacion"])."/100";
										//SI LA FORMA DE EJECUCION ES INDIVIDUAL
										if(($tipo_ejecucion==1) )
										{
											$datos_empresas_consorcios["id_consorcio"]="NULL";
											$datos_empresas_consorcios["id_consecutivo"]="NULL";									
										}
										
										$sqlIn1 = " INSERT INTO EFConsorcios_Empresas (id_consorcios_empresas
										, id_proyecto, id_empresa, id_consorcio, id_consecutivo , porcentaje_participacion, empresa_lider
										, valor_contrato_porcentaje, valor_final_contrato, valor_final_porcentaje, valores_actuales, id_valores_proyecto, usuarioGraba, fechaGraba) VALUES";
										
										$sqlIn1 = $sqlIn1." (  (select isnull(MAX(id_consorcios_empresas),0)+1 id_consorc  from EFConsorcios_Empresas WHERE id_proyecto=".$Proy." ), 
										".$Proy.", ".$datos_empresas_consorcios["id_empresa"]." ,".$datos_empresas_consorcios["id_consorcio"].", ".$datos_empresas_consorcios["id_consecutivo"]." , ".$datos_empresas_consorcios["porcentaje_participacion"]." , ".$datos_empresas_consorcios["empresa_lider"]." ,
										".$valorParticipacion.", ".($valor_proyecto).", ".$valorParticipacion.", 1, ".$id_valores_proyecto.", ".$_SESSION["sesUnidadUsuario"].", getdate() ) ";
										$cursorIn1 = mssql_query($sqlIn1);		
										
		
//echo $sqlIn1."<br>".mssql_get_last_message()."-----<BR>";													
									}
								}
							}
						}					
						
					}
				}
				else //SI LA PRORROGA NO ES EL VALOR MAS RECIENTE ASIGNADO AL VALOR DEL PROYECTO, YA QUE TAMBIEN PUEDEN EXISITIR ADICIONES, QUE AFECTARON EL VALOR DEL PROYECTO
				{
					//CONSULTA EL ID DEL ULTIMO REGISTRO ASOCIADO A LA EMPRESA PERTENIECIENTE AL PROYECTO, QUE FUE AFECTADO POR LA PRORROGA					
					//Y EL ID DEL REGISTRO DEL VALOR DEL PROYECTO, QUE FUE AFECTADO CON LA PRORROGA
					$sql_max_consorc="select max(id_consorcios_empresas) max_id,
					 (select id_valores_proyecto from EFValores_Contrato where id_proyecto=".$Proy." and id_prorroga_adicion=".$prorro." and tipo=1) max_id_valores_proyecto from EFConsorcios_Empresas where id_proyecto=".$Proy." and id_valores_proyecto =(
										select id_valores_proyecto from EFValores_Contrato where id_prorroga_adicion=".$prorro." and tipo=1 and id_proyecto=".$Proy.")	";
					$cursorIn1 = mssql_query($sql_max_consorc);		
					$datos_max_consorc=mssql_fetch_array($cursorIn1 );								
					$id_max_consorc=$datos_max_consorc["max_id"];
					$d_max_id_valores_proyecto=$datos_max_consorc["max_id_valores_proyecto"];
	
					

//echo $sql_max_consorc."<br>".mssql_get_last_message()."-----<BR>";
					if($cursorIn1!="")
					{
						
						//ELIMINA EEL VALOR DE la proRROGA	ACTUAL, QUE AFECTO EL VALOR DEL PROYECTO
						$sqlIn1 = " DELETE FROM EFValores_Contrato WHERE id_proyecto=".$Proy." AND id_prorroga_adicion=".$prorro." AND tipo=1 ";
						$cursorIn1 = mssql_query($sqlIn1);		
//	echo $sqlIn1."<br>".mssql_get_last_message()."-----<BR>";	
						if($cursorIn1!="")
						{	
							//CONSULTA LOS REGISTROS DEL VALOR DEL PROYECTO, QUE FUERON ASOCIADOS, DESPUES DE LA PRORROGA, CON EL FIN DE ACTUALIZAR SUS
							//VALORES
							$sql_empre_desp="select * from EFValores_Contrato where id_proyecto=".$Proy." and id_valores_proyecto>".$d_max_id_valores_proyecto;
							$cursorIn1 = mssql_query($sql_empre_desp);
							$cursorIn1_del="sin error";
//	echo $sql_empre_desp."<br>".mssql_get_last_message()."--cant reg = ".mssql_num_rows($cursorIn1)."---<BR>";													
							while($datos_empre_desp=mssql_fetch_array($cursorIn1))
							{
//echo "Ingresaa ".$cursorIn1." <br>";
								if($cursorIn1_del!="")
								{										
									//SE RECALCULA EL VALOR DEL CONTRATO EN EL REGISTO, RESTANDO EL VALOR DE LA PRORROGA CONTRA EL valor_contrato
									$valor_final_contrato=$datos_empre_desp["valor_contrato"]-$Valor;
									//ACTUALIZA EL REGISTRO, CON EL NUEVO VALOR CALCULADO
									
									$sql_up=" UPDATE EFValores_Contrato set  valor_contrato=".$valor_final_contrato."  where id_valores_proyecto=".$datos_empre_desp["id_valores_proyecto"]." and id_proyecto=".$Proy." ";
									$cursorIn1_del = mssql_query($sql_up);		
//echo $sql_up."<br>".mssql_get_last_message()."-----<BR>";												
								}
								else	//SI HAY UN ERROR
									$cursorIn1="";
									
							}
						}

						if($cursorIn1!="")
						{																				
							//ELIMINA LOS REGISTROS ASOCIADOS, DE LAS EMPRESAS CUYO VALOR FUEA AFECTADO POR LA PRORROGA
							$sqlIn1_del = "DELETE FROM EFConsorcios_Empresas WHERE id_proyecto=".$Proy." AND  id_valores_proyecto =".$d_max_id_valores_proyecto;
							$cursorIn1 = mssql_query($sqlIn1_del);	
//	echo $sqlIn1_del."<br>".mssql_get_last_message()."-----<BR>";	
							if($cursorIn1!="")
							{					
		
								//CONSULTA LOS REGISTROS DE LOS CONSORCIOS, QUE FUERON ASOCIADOS, DESPUES DE LA PRORROGA, CON EL FIN DE ACTUALIZAR SUS
								//VALORES, CON SUS % DE ARTICIPACION Y EL VALOR DEL PROYECTO 
								$sql_empre_desp="select * from EFConsorcios_Empresas where id_proyecto=".$Proy." and id_consorcios_empresas>".$id_max_consorc;
								$cursor_empre_desp = mssql_query($sql_empre_desp);
//echo $sql_empre_desp."<br>".mssql_get_last_message()." **** ".mssql_num_rows($cursorIn1)." -----<BR>";													
								if($cursor_empre_desp!="")
								{			
									while($datos_empre_desp=mssql_fetch_array($cursor_empre_desp))
									{
										if($cursorIn1!="")
										{										
											//SE RECALCULA EL VALOR DEL CONTRATO EN EL REGISTO, RESTANDO EL VALOR DE LA PRORROGA CONTRA EL valor_final_contrato
											$valor_final_contrato=$datos_empre_desp["valor_final_contrato"]-$Valor;
											
											//CALCULA EL VALOR DEL % DE PARTICIPACION, DEACUERDO AL VALOR DEL PROYECTO QUE SE HA RECALCULADO
											$valorParticipacion=(($valor_final_contrato)*( (float) $datos_empre_desp["porcentaje_participacion"]))/100;		
																						
						
/*											
									$valor_contrato_porcentaje=$datos_empre_desp["valor_contrato_porcentaje"]-$Valor;
									$valor_final_porcentaje=$valor_contrato_porcentaje; 		
	echo "valor_final_contrato=$valor_final_contrato = ".$datos_empre_desp["valor_final_contrato"]."-".$Valor."<BR> *** ";
	echo $valor_contrato_porcentaje." =".$datos_empre_desp["valor_contrato_porcentaje"]."-".$Valor." <br>";					
*/	
//echo "".$valorParticipacion."= ".$valor_final_contrato." * ".$datos_empre_desp["porcentaje_participacion"]."/100 <br>";

											//ACTUALIZA EL REGISTRO DEL CONSORCIO, CON LOS NUEVOS VALORES CALCULADOS
											$sql_up=" UPDATE EFConsorcios_Empresas set  valor_final_contrato=".$valor_final_contrato.", valor_contrato_porcentaje=".$valorParticipacion.", valor_final_porcentaje=".$valorParticipacion." where id_consorcios_empresas=".$datos_empre_desp["id_consorcios_empresas"]." and id_proyecto=".$Proy." ";
											$cursorIn1 = mssql_query($sql_up);		
//	echo $sql_up."<br>".mssql_get_last_message()."-----<BR>";																					 
										}
									}
								}
								else
									$cursorIn1="";
							}
						}
						
					}
				}					
			}	
		} 
		if($cursorIn1!="")
		{		
			//VERIFICA LAS FECHAS 		
			//CONSULTA SI LA PRORROGA AFECTO LA FECHA DE FINALIZACION DEL PROYECTO, Y ESTA FECHA ES LA QUE APLICA ACTUALMNTE EN EL PROYECTO
			$sql_fecha_proy="select * from EFFechas_Proyecto  where id_proyecto=".$Proy." AND id_prorroga=".$prorro."  and fechas_actuales=1";
			$cursorIn1 = mssql_query($sql_fecha_proy);
			
			//SI HAY RGISTROS
			if(mssql_num_rows($cursorIn1)>0)
			{
				//ELIMINA EL REGISTRO DE LA PRORROGA, QUE AFECTO LA FECHA DEL PROYECTO
				$sql_fecha_proy="delete from EFFechas_Proyecto  where id_proyecto=".$Proy." AND id_prorroga=".$prorro."  and fechas_actuales=1";
				$cursorIn1 = mssql_query($sql_fecha_proy);		
					
//	echo $sql_fecha_proy."<br>".mssql_get_last_message()."-----<BR>";																	
				if($cursorIn1!="")
				{
					//CAMBIA EL ESTADO DEL REGISTRO MAS RECIENTE QUE AFECTO LAS FECHAS DEL PROYECTO
					$sql_fecha_proy="update EFFechas_Proyecto set fechas_actuales=1 where id_fecha_proyecto=(SELECT MAX(id_fecha_proyecto) from EFFechas_Proyecto  where id_proyecto=".$Proy.") ";
					$cursorIn1 = mssql_query($sql_fecha_proy);		
					
//	echo $sql_fecha_proy."<br>".mssql_get_last_message()."-----<BR>";																								
				}
			}		
		}
	}
	if  (trim($cursorIn1) != "")  {
	//	mssql_query("rollback  transaction");			
		mssql_query("commit transaction");		
		
		echo ("<script>alert('Operaci\u00f3n realizada con exito');</script>"); 					
	} 
	else {
		mssql_query("rollback  transaction");		
		echo ("<script>alert('Error durante la operaci\u00f3n');</script>");
	}		
	

	echo ("<script>window.close(); MM_openBrWindow('costosF.php?Proy=".$Proy."&sec=1-2-2&imen=1','EF','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");		

}

//info proy
$sql_nom="select * from EFNombres_Proyectos where id_proyecto=".$Proy ." and nombre_actual=1";
$cur=mssql_query($sql_nom);
$datos_pro=mssql_fetch_array($cur);

$sql_nom="select  CONVERT(varchar, EFFechas_Proyecto.fecha_inicio_proyecto,101) fecha_inicio_proyecto, CONVERT(varchar, EFFechas_Proyecto.fecha_final_proyecto,101) fecha_final_proyecto  from EFFechas_Proyecto where id_proyecto=".$Proy ." and fechas_actuales=1";
$cur_fecha=mssql_query($sql_nom);
$datos_pro_fecha=mssql_fetch_array($cur_fecha);

//CONSULTA LA FECHA FINAL DE LA ULTIMA PRORROGA REGISTRADA EN EL PROYECTO
$sql_max_fech_prorroga="select CONVERT(varchar,fecha_final,101) fechafinal from EFProrrogas  where id_proyecto=".$Proy ." and id_prorroga=(select MAX(id_prorroga) from EFProrrogas  where id_proyecto=".$Proy .")";
$cur_max_fech_prorroga=mssql_query($sql_max_fech_prorroga);
$datos_max_fech_prorroga=mssql_fetch_array($cur_max_fech_prorroga);



?>

<script type="text/javascript">
function valida()
{
	
	document.getElementById("divFinicios").className="form-group";		
	document.getElementById("helpFiniciosProrr").style.display="none";	
//	document.getElementById("helpFiniciosProrr3").style.display="none";		
 
	var campos_tex = ["Prorroga","Finicios","Final","Documento","Observaciones"];	
	
	var error=0;
	
	error=valida_campos(campos_tex,1);	
//	error+=valida_campos("",5);

	if( (document.getElementById("Finicios").value!="") && (document.getElementById("Final").value!="" ) )
	{
		document.getElementById("divFinal").className="form-group";		
		document.getElementById("helpFinal2").style.display="none";	
		
		//VALIDACION DE FECHAS
		if (compare_fecha( document.getElementById("Finicios").value , document.getElementById("Final").value))
		{  
	//	  alert("");  
			document.getElementById("divFinal").className="form-group has-error";		
			document.getElementById("helpFinal2").style.display="inline-block";		
			error++;				  
		}
	}

//alert(" -- "+document.getElementById("Finicios").value+ " *** "+document.getElementById("FechaIniProy").value) ;	

	
	if( (document.getElementById("Finicios").value!="") && (document.getElementById("Finicios").value!="") )
	{				
		//SI LA FEHCA DE INICIO DEL PRORROGA ES INFERIOR A LA FECHA DE INICIO DEL PROYECTO
		if (compare_fecha( document.getElementById("FechaIniProy").value , document.getElementById("Finicios").value))
		{  
	//alert("Ingresaa ");
			document.getElementById("divFinicios").className="form-group has-error";		
			document.getElementById("helpFiniciosProrr").style.display="inline-block";		
			error++;				  
		}
		
/*
		if(document.getElementById("FechaUltProrroga").value!="")
		{
			document.getElementById("divFinicios").className="form-group";		
			document.getElementById("helpFiniciosProrr2").style.display="none";
				
			//SI LA FEHCA DE INICIO DEL PRORROGA ES INFERIOR A LA FECHA DE FINALIZACION DE LA ULTIMA PRORROGA RGISTRADA
			if (compare_fecha( document.getElementById("FechaUltProrroga").value , document.getElementById("Finicios").value))
			{  
		//alert("Ingresaa ");
				document.getElementById("divFinicios").className="form-group has-error";		
				document.getElementById("helpFiniciosProrr2").style.display="inline-block";		
				error++;				  
			}		
		}
*/

/*
		//SI LA FEHCA DE INICIO DE LA PRORROGA ES SUPERIOR A LA FECHA DE FINALIZACION DEL PROYECTO 
		if (compare_fecha( document.getElementById("Finicios").value , document.getElementById("FechaFinaProy").value))
		{  
	//alert("Ingresaa ");
			document.getElementById("divFinicios").className="form-group has-error";		
			document.getElementById("helpFiniciosProrr3").style.display="inline-block";		
			error++;				  
		}		
*/
	}

	if(error==0)
	{
		document.formulario.recarga.value="2";
		document.formulario.submit();
	}
	
}
</script>


<form id="formulario" name="formulario" method="POST">

  <div class="form-group" id="">
    <label for="">Proyecto</label>
    <div class="desabilitados">
		 <?=$datos_pro["nombre_largo_proyecto"] ?>
    </div>
  </div>
  
  <div class="form-group" id="">
    <label for="">Fecha de Inicio</label>
    <div class="desabilitados">
		 <?=$datos_pro_fecha["fecha_inicio_proyecto"] ?>
         <input type="hidden" value="<?=$datos_pro_fecha["fecha_inicio_proyecto"] ?>" name="FechaIniProy" id="FechaIniProy">
         <input type="hidden" value="<?=$datos_max_fech_prorroga["fechafinal"] ?>" name="FechaUltProrroga" id="FechaUltProrroga">
         
    </div>
  </div>  

  <div class="form-group" id="">
    <label for="">Fecha de Finalizaci&oacute;n</label>
    <div class="desabilitados">
		 <?=$datos_pro_fecha["fecha_final_proyecto"] ?>
         <input type="hidden" value="<?=$datos_pro_fecha["fecha_final_proyecto"] ?>" name="FechaFinaProy" id="FechaFinaProy">         
    </div>
  </div>  
  
  <div class="form-group" id="divProrroga">
    <label for="">Tipo de Prorroga</label>
    <select class="form-control" id="Prorroga" name="Prorroga" disabled  >
      <option selected value="">Seleccione Prorroga</option>
      <?php
        	$cur_prorr=mssql_query("SELECT   * FROM EFItems_Prorrogas_Adicionales where estado=1 and tipo_prorroga_adicion=1 order by prorroga_adicion");
			while($datos_prorro=mssql_fetch_array($cur_prorr))
			{
				$sel="";
 				if($Prorroga==$datos_prorro["id_item_prorroga_adicion"])
					$sel="selected";
				
		?>
               <option value="<?=$datos_prorro["id_item_prorroga_adicion"]; ?>" <?=$sel ?>   >
                        <?=$datos_prorro["prorroga_adicion"]; ?>
              </option>
      <?php
			}
		?>
    </select>
    <span id="helpProrroga" class="help-block" style="display:none;" >El tipo de prorroga es obligatoria.</span>
  </div>

    <div class="form-group" id="divFinicios">
    <label for="">Fecha de Inicio</label>    
	<div class="container-fluid">
      <div class="col-sm-3">
       <div class="input-group">
        <input class="form-control" id="Finicios" name="Finicios" placeholder="MM/DD/YYYY" value="<?=$Finicios ?>"   disabled type="text"/>
<!--        
        <div class="input-group-addon">
         <i class="glyphicon glyphicon-calendar">
         </i>
        </div>        
-->        
       </div>
      </div>
    </div>   
        
     <span id="helpFinicios" class="help-block" style="display:none;" >La fecha de inicio es obligatoria.</span>

     <span id="helpFiniciosProrr" class="help-block" style="display:none;" >La fecha de inicio, no puede ser inferior a la fecha de inicio del proyecto.</span>     

	 <span id="helpFiniciosProrr2" class="help-block" style="display:none;" >La fecha de inicio, no puede ser inferior a la fecha de finalizaci&oacute;n de la ultima prorroga registrada (<?=$datos_max_fech_prorroga["fechafinal"] ?>).</span>          
<!--     
	<span id="helpFiniciosProrr3" class="help-block" style="display:none;" >La fecha de inicio, no puede ser superior a la fecha de finalizaci&oacute;n del proyecto.</span>
-->                    
  </div>


    <div class="form-group" id="divFinal">
    <label for="">Fecha de Finalizaci&oacute;n</label>    
	<div class="container-fluid">
      <div class="col-sm-3">
       <div class="input-group">
        <input class="form-control" id="Final" name="Final" placeholder="MM/DD/YYYY"  value="<?=$Final ?>"  disabled type="text"/>
<!--        
        <div class="input-group-addon">
         <i class="glyphicon glyphicon-calendar">
         </i>
        </div>        
-->        
       </div>
      </div>
    </div>   
        
     <span id="helpFinal" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n es obligatoria.</span>
     <span id="helpFinal2" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n es menor a la fecha de inicio, por favor verifique.</span>     
    </div>       
           
   <div class="form-group" id="divDocumento">
    <label for="">Documento de Soporte</label>
    <select class="form-control" id="Documento" name="Documento" disabled >
      <option selected value="">Seleccione Documento</option>
      <?php
        	$cur_doc=mssql_query("select * from EFDocumentos_Soporte where estado=1 and tipo_prorroga_adicion=1 order by documento_soporte");
			while($datos_doc=mssql_fetch_array($cur_doc))
			{
				$sel="";
 				if($Documento==$datos_doc["id_documento_soporte"])
					$sel="selected";				
 
		?>
                  <option value="<?=$datos_doc["id_documento_soporte"]; ?>"  <?=$sel ?>  >
                    <?=$datos_doc["documento_soporte"]; ?>
                  </option>
      <?php
			}
		?>
    </select>
    <span id="helpDocumento" class="help-block" style="display:none;" >El documento de soporte es obligatorio.</span>

  </div>
  
  <div class="form-group" id="divValor">
    <label for="">Valor</label>
    <input type="text" class="form-control" id="Valor" name="Valor" value="<?=$Valor ?>"  placeholder="Valor" onKeyPress="return acceptNum(event)" disabled>     
  </div>  
  
    <div class="form-group" id="divObservaciones">
        <div class="col-sm-12 main row">
            <label for="">Observaciones</label>
        </div>
        <div class="col-sm-12 main row form-group">
        <textarea name="Observaciones" id="Observaciones" cols="140" rows="3" class="form-control" disabled><?=$Observaciones ?></textarea>
	    <span id="helpObservaciones" class="help-block" style="display:none;" >Las observaciones son obligatorias.</span>                   
        </div>         
    </div> 
    
<?php
	$mensaje="";
	if($acc==2)	
		$mensaje="Actualizar";
	else
		$mensaje="Eliminar";		
?>       
  
  <div class="form-group" style="text-align:right">
                <button type="button" class="btn btn-primary" onClick="valida()"><?=$mensaje ?></button>
		       <input name="recarga" type="hidden" id="recarga" value="1">                
		</div>      
  
</form>  

<?php
	include("inferior.php"); 
?>