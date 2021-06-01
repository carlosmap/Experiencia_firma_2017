<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente
banner(2,"Nueva Prorroga");


if($recarga==2)
{	

	mssql_query("begin transaction");		
	
	//SI NO SE HA ASIGNADO UN VALOR A LA PRORROGA
	if($Valor=="")
		$Valor=0;
	
	//CONSULTA EL ID DE LA PRORROGA MAS RECIENTE, ASOCIADA AL PROYECTO 	
	$sql_prorroga="select isnull(MAX(id_prorroga),0)+1 idProrroga   from EFProrrogas WHERE id_proyecto=".$Proy;
	$cursorIn1 = mssql_query($sql_prorroga);		
	$datos_prorroga=mssql_fetch_array($cursorIn1 );
	
	//CONSULTA LA INFO DEL PROYECTO
	$sql_proyect="select * from EFProyectos WHERE id_proyecto=".$Proy;
	$cursorproyect= mssql_query($sql_proyect);		
	$datos_proyect=mssql_fetch_array($cursorproyect );	
	$tipo_ejecucion=$datos_proyect["id_tipo_ejecucion"];
	
	$idProrroga=$datos_prorroga["idProrroga"];
//echo $idProrroga." *** <br> ";
	//INFO de la proRROGA
	$sqlIn1 = " INSERT INTO EFProrrogas";
	$sqlIn1 = $sqlIn1 . "( 
	id_prorroga, id_proyecto, id_item_prorroga_adicion, fecha_inicio, fecha_final, id_documento_soporte, valor_prorroga, observaciones, usuarioGraba,  fechaGraba) values ( 
	".$idProrroga." ,".$Proy." ,		
	".$Prorroga.",'".$Finicios."','".$Final."',".$Documento." ,".$Valor.",'".$Observaciones."'," . $_SESSION["sesUnidadUsuario"] . ",getdate())  ";
	$cursorIn1 = mssql_query($sqlIn1);				
		
//echo $sqlIn1."<br>".mssql_get_last_message()."-----<BR>";		

	if($cursorIn1!="")
	{	
		//SI SE HA DEFINIDO UN VALOR A LA PRORROGA	
		if(($Valor!=0)&&($Valor!=""))
		{
			//consulta el VALOR DEL PROYECTO
			$sl_val_pro="select valor_contrato from EFValores_Contrato where id_proyecto=".$Proy." and valores_actuales=1";
			$cursor_val_pro= mssql_query($sl_val_pro);
			$datos_val_pro=mssql_fetch_array($cursor_val_pro );
			$valor_proyecto=( (int) $datos_val_pro["valor_contrato"] );

			//ACTUALIZA TODOS LOS REGISTROS DEL VALOR DEL CONTRATO A valores_actuales=0
			$sqlIn1 =" UPDATE EFValores_Contrato SET valores_actuales=0 where id_proyecto=".$Proy." ";						
			$cursorIn1 = mssql_query($sqlIn1);					
				
//echo $sqlIn1."<br>".mssql_get_last_message()."-----<BR>";		

			if($cursorIn1!="")
			{	
				//VALOR SI SE ASOCIA UN VALOR A LA ADICISON, SE SUMA ESTE VALOR AL VALOR DEL PROYECTO
				$sqlIn1 ="insert into EFValores_Contrato ( id_valores_proyecto, id_proyecto, id_prorroga_adicion, tipo, valor_contrato, valores_actuales, usuarioGraba,fechaGraba) 
				values((select max(id_valores_proyecto)+1 from EFValores_Contrato where id_proyecto=".$Proy." ) , ".$Proy.", ".$idProrroga." , 1 , ".($Valor+$valor_proyecto)." , 1, " . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
				$cursorIn1 = mssql_query($sqlIn1);		
				
//echo $sqlIn1."<br>".mssql_get_last_message()."-----<BR>";							
				
				if($cursorIn1!="")
				{			
					//CONSULTA LAS EMPRESAS PERTENECIENTES A EL CONSORCIO
					$sql_empresas_consorcios="select *  from EFConsorcios_Empresas  WHERE id_proyecto=".$Proy."  and valores_actuales=1";
					$cursor_empresas_consorcios= mssql_query($sql_empresas_consorcios);
					while($datos_empresas_consorcios=mssql_fetch_array($cursor_empresas_consorcios ))
					{
						if($cursorIn1!="")
						{							
							//ACTUALIZA LA EMPRESA DEL CONSORCIO A valores_actuales=0
							$sqlIn1 =" UPDATE EFConsorcios_Empresas SET valores_actuales=0 where id_proyecto=".$Proy." and id_empresa=".$datos_empresas_consorcios["id_empresa"]." ";						
							$cursorIn1 = mssql_query($sqlIn1);

//echo $sqlIn1."<br>".mssql_get_last_message()."-----<BR>";		
							
							if($cursorIn1!="")
							{									
								//CALCULA EL VALOR DEL % DE PARTICIPACION, DEACUERDO AL NUEVO VALOR DEL PROYECTO
								$valorParticipacion=(($Valor+$valor_proyecto)*( (float) $datos_empresas_consorcios["porcentaje_participacion"]))/100;
								
								//SI LA FORMA DE EJECUCION ES INDIVIDUAL
								if(($tipo_ejecucion==1) )
								{
									$datos_empresas_consorcios["id_consorcio"]="NULL";
									$datos_empresas_consorcios["id_consecutivo"]="NULL";									
								}
								
								$sqlIn1 = " INSERT INTO EFConsorcios_Empresas (id_consorcios_empresas
								, id_proyecto, id_empresa, id_consorcio, id_consecutivo , porcentaje_participacion, empresa_lider
								, valor_contrato_porcentaje, valor_final_contrato, valor_final_porcentaje, valores_actuales, usuarioGraba, fechaGraba, id_valores_proyecto ) VALUES";
								
								$sqlIn1 = $sqlIn1." (  (select isnull(MAX(id_consorcios_empresas),0)+1 id_consorc  from EFConsorcios_Empresas WHERE id_proyecto=".$Proy." ), 
								".$Proy.", ".$datos_empresas_consorcios["id_empresa"]." ,".$datos_empresas_consorcios["id_consorcio"].", ".$datos_empresas_consorcios["id_consecutivo"]." , ".$datos_empresas_consorcios["porcentaje_participacion"]." , ".$datos_empresas_consorcios["empresa_lider"]." ,
								".$valorParticipacion.", ".($Valor+$valor_proyecto).", ".$valorParticipacion.", 1,  ".$_SESSION["sesUnidadUsuario"].", getdate(), (select  id_valores_proyecto from EFValores_Contrato where id_proyecto=".$Proy." AND valores_actuales=1)  ) ";
								$cursorIn1 = mssql_query($sqlIn1);		
								

//echo $sqlIn1."<br>".mssql_get_last_message()."-----<BR>";												
							}
						}
					}
				}
			}
		}	
		
		if($cursorIn1!="")
		{		
			//CONSULTA LAS FECHAS ACTUALES DEL PROYECTO
			$sql_fec="select *, CONVERT(varchar, fecha_inicio_proyecto,101) fechaIniProy,  CONVERT(varchar, fecha_final_proyecto,101) fechaFinProy from EFFechas_Proyecto WHERE id_proyecto=".$Proy." and fechas_actuales=1";
			$cur_fec=mssql_query($sql_fec);
			$datos_fec=mssql_fetch_array($cur_fec);
			
			//SI LA FECHA DE FINALIZACION DEL PROY Y LA DE LA PRORROGA SON DIFERENTES
			if($datos_fec["fechaFinProy"]!=$Final)
			{
				//FORMATOS DE FECHAS mm/dd/aaaa
				//COMPARA SI $fecha1 ES MAYOR A $fecha2	
				//SI ES MAYOR		
				if(compare_fecha($Final, $datos_fec["fechaFinProy"]) )
				{
					//ACTUALIZA LAS fechas_actuales=0 DEL LOS REGISTROS VIEJOS EN LA TABLA EFFechas_Proyecto
					$sqlIn1 =" UPDATE EFFechas_Proyecto SET fechas_actuales=0 where id_proyecto=".$Proy;
					$cursorIn1 = mssql_query($sqlIn1);		
	
	//echo $sqlIn1."<br>".mssql_get_last_message()."-----<BR>";						
								
					if($cursorIn1!="")
					{
						//REGISTRA LA FECHA DE FINALIZACION, CON LA MISMA DE LA PRORROGA FECHA DE INICIO Y FINAL
						$sqlIn1 ="insert into EFFechas_Proyecto (id_fecha_proyecto, id_proyecto, id_prorroga, fecha_inicio_proyecto, fecha_final_proyecto, fechas_actuales ,usuarioGraba,fechaGraba) 
						values( (select isnull(MAX(id_fecha_proyecto),0)+1 id_proye  from EFFechas_Proyecto WHERE id_proyecto=".$Proy." ) , ".$Proy." ,".$idProrroga.", '".$datos_fec["fechaIniProy"]."', '".$Final."', 1 ," . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
						$cursorIn1 = mssql_query($sqlIn1);		
						
	//echo $sqlIn1."<br>".mssql_get_last_message()."-----<BR>";		
									
					}
				}
			}	
		}
	}
	

	if  (trim($cursorIn1) != "")  {
//		mssql_query("rollback  transaction");			
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



$sql_nom="select  CONVERT(varchar, EFFechas_Proyecto.fecha_inicio_proyecto,101) fecha_inicio_proyecto, CONVERT(varchar, EFFechas_Proyecto.fecha_final_proyecto,101) fecha_final_proyecto , CONVERT(varchar, DATEADD(day, +1, fecha_final_proyecto),101) fecha_final_proyecto_mas_dia from EFFechas_Proyecto where id_proyecto=".$Proy ." and fechas_actuales=1";
$cur_fecha=mssql_query($sql_nom);
$datos_pro_fecha=mssql_fetch_array($cur_fecha);

$Finicios=$datos_pro_fecha["fecha_final_proyecto_mas_dia"];

//CONSULTA LA FECHA FINAL DE LA ULTIMA PRORROGA REGISTRADA EN EL PROYECTO
$sql_max_fech_prorroga="select CONVERT(varchar,fecha_final,101) fechafinal from EFProrrogas  where id_proyecto=".$Proy ." and id_prorroga=(select MAX(id_prorroga) from EFProrrogas  where id_proyecto=".$Proy .")";
$cur_max_fech_prorroga=mssql_query($sql_max_fech_prorroga);
$datos_max_fech_prorroga=mssql_fetch_array($cur_max_fech_prorroga);

?>

<script type="text/javascript">
function valida()
{


	document.getElementById("divFinicios").className="form-group";		
	document.getElementById("helpFinicioProrr").style.display="none";	
//	document.getElementById("helpFinicioProrr3").style.display="none";		
	 
	var campos_tex = ["Prorroga","Finicios","Final","Documento","Observaciones"];	
	
	var error=0;
	
	error=valida_campos(campos_tex,1);	
//	error+=valida_campos("",5);

//alert(" -- "+document.getElementById("Finicios").value+ " *** "+document.getElementById("FechaIniProy").value) ;	
		
		
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
				
	if( (document.getElementById("Finicios").value!="") && (document.getElementById("Finicios").value!="") )
	{		
		//SI LA FEHCA DE INICIO DEL PRORROGA ES INFERIOR A LA FECHA DE INICIO DEL PROYECTO
		if (compare_fecha( document.getElementById("FechaIniProy").value , document.getElementById("Finicios").value))
		{  
	//alert("Ingresaa ");
			document.getElementById("divFinicios").className="form-group has-error";		
			document.getElementById("helpFinicioProrr").style.display="inline-block";		
			error++;				  
		}
		

		if(document.getElementById("FechaUltProrroga").value!="")
		{
		
			document.getElementById("helpFinicioProrr2").style.display="none";
//			document.getElementById("helpFinicioProrr3").style.display="none";			
			
				
			//SI LA FEHCA DE INICIO DEL PRORROGA ES INFERIOR A LA FECHA DE FINALIZACION DE LA ULTIMA PRORROGA RGISTRADA
			if (compare_fecha( document.getElementById("FechaUltProrroga").value , document.getElementById("Finicios").value))
			{  
		//alert("Ingresaa ");
				document.getElementById("divFinicios").className="form-group has-error";		
				document.getElementById("helpFinicioProrr2").style.display="inline-block";		
				error++;				  
			}		
		}
/*

		//SI LA FEHCA DE INICIO DE LA PRORROGA ES SUPERIOR A LA FECHA DE FINALIZACION DEL PROYECTO 
		if (compare_fecha( document.getElementById("Finicios").value , document.getElementById("FechaFinaProy").value))
		{  
	//alert("Ingresaa ");
			document.getElementById("divFinicios").className="form-group has-error";		
			document.getElementById("helpFinicioProrr3").style.display="inline-block";		
			error++;				  
		}	
		*/	
	
	}
//
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
    <select class="form-control" id="Prorroga" name="Prorroga"  >
      <option selected value="">Seleccione Prorroga</option>
      <?php
        	$cur_prorr=mssql_query("SELECT   * FROM EFItems_Prorrogas_Adicionales where estado=1 and tipo_prorroga_adicion=1 order by prorroga_adicion");
			while($datos_prorro=mssql_fetch_array($cur_prorr))
			{
 
		?>
               <option value="<?=$datos_prorro["id_item_prorroga_adicion"]; ?>"   >
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
        <input class="form-control" id="Finicios" name="Finicios" placeholder="MM/DD/YYYY" value="<?=$Finicios ?>"   readonly type="text"/>                
       </div>
      </div>
    </div>   
        
     <span id="helpFinicios" class="help-block" style="display:none;" >La fecha de inicio es obligatoria.</span>

     <span id="helpFinicioProrr" class="help-block" style="display:none;" >La fecha de inicio, no puede ser inferior a la fecha de inicio del proyecto.</span>     
     
	 <span id="helpFinicioProrr2" class="help-block" style="display:none;" >La fecha de inicio, no puede ser inferior a la fecha de finalizaci&oacute;n de la ultima prorroga registrada (<?=$datos_max_fech_prorroga["fechafinal"] ?>).</span>          
<!--
	<span id="helpFinicioProrr3" class="help-block" style="display:none;" >La fecha de inicio, no puede ser superior a la fecha de finalizaci&oacute;n del proyecto.</span>  -->  
     
  </div>


    <div class="form-group" id="divFinal">
    <label for="">Fecha de Finalizaci&oacute;n</label>    
	<div class="container-fluid">
      <div class="col-sm-3">
       <div class="input-group">
        <input class="form-control" id="Final" name="Final" placeholder="MM/DD/YYYY"  value="<?=$Final ?>"  readonly type="text"/>
        <div class="input-group-addon">
         <i class="glyphicon glyphicon-calendar">
         </i>
        </div>        
       </div>
      </div>
    </div>   
        
     <span id="helpFinal" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n es obligatoria.</span>
     <span id="helpFinal2" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n es menor a la fecha de inicio, por favor verifique.</span>     
    </div>       
           
   <div class="form-group" id="divDocumento">
    <label for="">Documento de Soporte</label>
    <select class="form-control" id="Documento" name="Documento"  >
      <option selected value="">Seleccione Documento</option>
      <?php
        	$cur_doc=mssql_query("select * from EFDocumentos_Soporte where estado=1 and tipo_prorroga_adicion=1 order by documento_soporte");
			while($datos_doc=mssql_fetch_array($cur_doc))
			{
 
		?>
      <option value="<?=$datos_doc["id_documento_soporte"]; ?>"  >
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
    <input type="text" class="form-control" id="Valor" name="Valor" value="0"  placeholder="Valor" onKeyPress="return acceptNum(event)">     
  </div>  
  
    <div class="form-group" id="divObservaciones">
        <div class="col-sm-12 main row">
            <label for="">Observaciones</label>
        </div>
        <div class="col-sm-12 main row form-group">
        <textarea name="Observaciones" id="Observaciones" cols="140" rows="3" class="form-control"></textarea>
	    <span id="helpObservaciones" class="help-block" style="display:none;" >Las observaciones son obligatorias.</span>                   
        </div>         
    </div>    
  
  <div class="form-group" style="text-align:right">
                <button type="button" class="btn btn-primary" onClick="valida()">Grabar</button>
		       <input name="recarga" type="hidden" id="recarga" value="1">                
		</div>      
  
</form>  

<?php
	include("inferior.php"); 
?>