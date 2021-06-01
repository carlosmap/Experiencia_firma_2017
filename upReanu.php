<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente

if($acc==2)
	$mens_v="Actualizar Reanudaci&oacute;n";	   		
if($acc==3)
	$mens_v="Eliminar Reanudaci&oacute;n";	 

banner(2,$mens_v);

//include ("../../verificaRegistro2.php");
if($recarga==2)
{
	
//	$cursorIn1="no error";
	
//echo "Ingresssaa ---cursorIn1=".$cursorIn1;	

	if(trim($Archivo_name)!="")
	{
		$Archivo_name=remplazaCaracteresEsp($Archivo_name);		
		$sql_doc="select * from EFReanudaciones where id_proyecto=".$Proy." and nombre_doc_reanudacion='".$Archivo_name."'";
		$cur_doc=mssql_query($sql_doc);
	}
//echo $sql_doc." *** ".mssql_num_rows($cur_doc);
	if(mssql_num_rows($cur_doc)==0)
	{		
		
		mssql_query("begin transaction");			
	
		if($acc==2)	
		{
			$sql_insert="update EFReanudaciones SET  observaciones='".$observaciones."' ";
			if(trim($Archivo_name)!="")
			{		 
				$sql_insert.=", nombre_doc_reanudacion='".$Archivo_name."' ,url_doc_reanudacion='files/".$Proy."/filesReanudaciones/".$id."/".$Archivo_name."' ";
			}
			$sql_insert.=", usuarioMod=  " . $_SESSION["sesUnidadUsuario"] . " , fechaMod=getdate()
						where id_proyecto=".$Proy ." and id_reanudacion=".$id." ";
			$cursorIn1=mssql_query($sql_insert);	
			
			if(trim($Archivo_name)!="")
			{		
				$path1 = "files/".$Proy."/filesReanudaciones/".$id;	
				if($cursorIn1!="")
				{
					//SUBE EL ARCHIVO DE LA REANUDACION
					if(!subirArchivo($path1,$Archivo_name,$Archivo))
					{	
						$cursorIn1="";	
					}
		
					//ELIMINA EL ACHIVO ANTERIOR
					if($cursorIn1!="")
					{			
						if(!del_arch($path1."/".$nomDocAnt))
						{
							$cursorIn1="";
						}
					}			
	//   $error = error_get_last();
	//    echo "<BR>".$error['message']."<br>";						
	//							echo $path1." ---- ".$Archivo_name." *** ".$Archivo."<br>";		
				}	
			}
		}
		
		if($acc==3)
		{
	
			$sql_insert="delete EFReanudaciones  where id_proyecto=".$Proy ." and id_reanudacion=".$id." ";
			$cursorIn1=mssql_query($sql_insert);
	//echo $sql_insert."<br>".mssql_get_last_message();		

			//ACTUALIZA LA SUSPENCION, QUE ESTABA ASOCIADA A LA REANUDACION
			if($cursorIn1!="")
			{	
				$sql_insert="update EFSuspenciones set id_reanudacion=null where id_reanudacion=".$id." and id_proyecto=".$Proy ."   ";
				$cursorIn1=mssql_query($sql_insert);	
			}
			if($cursorIn1!="")
			{
				//SE DESACTIVA EL ESTADO ANTERIOR						
				$sqlIn1 =" UPDATE EFEstados_Proy_Proyectos SET estado_actual=0 where id_proyecto=".$Proy;
				$cursorIn1 = mssql_query($sqlIn1);	
	//echo $sqlIn1."<br>".mssql_get_last_message()."<br><br>";										
				if($cursorIn1!="")
				{
					//SE REGISTRA EL ESTADO DEL PROYECTO A "SUSPENDIDO"
					$sqlIn1 ="insert into EFEstados_Proy_Proyectos ( id_estados_proy_proyectos, id_estado_proy, id_proyecto, fecha_estado, estado_actual, tipo_accion, id_consecutivo, usuarioGraba,fechaGraba) 
					values( (select MAX(id_estados_proy_proyectos)+1 from EFEstados_Proy_Proyectos where id_proyecto='".$Proy."') , 5 , '".$Proy."' ,getdate(), 1, NULL ,NULL," . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
					$cursorIn1=mssql_query($sqlIn1);
	//echo $sqlIn1."<br>".mssql_get_last_message()."<br><br>";														
				}		
				
				if($cursorIn1!="")
				{
	//	echo "<BR>iNGRESSA ---- "."files/".$Proy."/filesReanudaciones/".$id;			
					$path1 = "files/".$Proy."/filesReanudaciones/".$id;		
					
					if(!del_arch($path1."/".$nomDocAnt))
					{
						$cursorIn1="";
					}										
					
					if($cursorIn1!="")
					{				
						if(!delete_dir($path1))
						{
	//		echo "<BR>ERROR";
							$cursorIn1="";
						}
					}
	//	   $error = error_get_last();
	//		echo "<BR>".$error['message']."<br>";				
				}						
			}
	 
			
		}
		
		if  (trim($cursorIn1) != "")  {
			mssql_query("commit transaction");		
	//			mssql_query("rollback  transaction");		
			echo ("<script>alert('Operaci\u00f3n realizada con exito');</script>"); 					
		} 
		else {
			mssql_query("rollback  transaction");		
			echo ("<script>alert('Error durante la operaci\u00f3n');</script>");
		}			
	
		echo ("<script>window.close(); MM_openBrWindow('infoG.php?Proy=".$Proy."&sec=1-1-6&imen=1','EF','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");				
	}
	else
	{
		echo ("<script>alert('Ya existe un documento de reanudaci\u00f3n con en ese nombre, por favor adjunte uno diferente.');</script>");
	}	
}

//info proy
$sql_nom="select * from EFNombres_Proyectos where id_proyecto=".$Proy ." and nombre_actual=1";
$cur=mssql_query($sql_nom);
$datos_pro=mssql_fetch_array($cur);

//FECHA PROY
$sql_nom="select  CONVERT(varchar, EFFechas_Proyecto.fecha_inicio_proyecto,101) fecha_inicio_proyecto, CONVERT(varchar, EFFechas_Proyecto.fecha_final_proyecto,101) fecha_final_proyecto  from EFFechas_Proyecto where id_proyecto=".$Proy ." and fechas_actuales=1";
$cur_fecha=mssql_query($sql_nom);
$datos_pro_fecha=mssql_fetch_array($cur_fecha);

//CONSULTA LA INFO DE LA REANUDACION
$sql_FECHA_ULT_SUSp="select  CONVERT(varchar, fecha ,101) fechaR, * from EFReanudaciones WHERE id_reanudacion=".$id." and id_proyecto=".$Proy ."";
$cur_fecha_ult_susp=mssql_query($sql_FECHA_ULT_SUSp);

$datos_fecha_reanuda=mssql_fetch_array($cur_fecha_ult_susp);
$Finicio=$datos_fecha_reanuda["fechaR"];
$observaciones=$datos_fecha_reanuda["observaciones"];
$doc=$datos_fecha_reanuda["nombre_doc_reanudacion"];

$dis="";
if($acc==3)
{
	$dis="disabled";
}
?>


<script type="text/javascript">
function valida()
{

	document.getElementById("divFinicio").className="form-group";		
	document.getElementById("helpFinicio2").style.display="none";    
	
 	var campos_tex = ["observaciones","Finicio"];	
  
	var error=0;
	
	error=valida_campos(campos_tex,1);
    	
	if(document.getElementById("Finicio").value!="")
	{
		//SI LA FEHCA DE LA REANUDACION ES INFERIOR A LA FECHA DE INICIO DEL PROYECTO
		if (compare_fecha( document.getElementById("FinicioProy").value , document.getElementById("Finicio").value))
		{  
			document.getElementById("divFinicio").className="form-group has-error";		
			document.getElementById("helpFinicio2").style.display="inline-block";		
			error++;
		}	

		//SI LA FEHCA FINAL DE LA REANUDACION ES SUPERIOR A LA FECHA DE FINALIZACION DEL PROYECTO
		if (compare_fecha( document.getElementById("Finicio").value , document.getElementById("FinalProy").value))
		{  
			document.getElementById("divFinicio").className="form-group has-error";		
			document.getElementById("helpFinal3").style.display="inline-block";		
			error++;
		}			
	}


<?php        
		if($acc==2)
		{
?>
			if(document.formulario.Archivo.value!="")
			{	
				var archivo = document.formulario.Archivo.value;
				var extension_permit=[".pdf",".PDF",".DOC",".doc",".docx",".DOCX"];
				error+=valida_extension(archivo, extension_permit,"Archivo");
			}
<?php
		}
?>   
	//SI NO SE PRESENTARON PROBLEMAS DE VALIDACION
	if(error==0)
	{

		document.formulario.recarga.value="2";
		document.formulario.submit();
	}
	
}
</script>

<form  name="formulario" id="formulario" method="post" enctype="multipart/form-data" >

	<div class="form-group">
		<label for="">Proyecto</label>
		 <div class="desabilitados">
			 <?=$datos_pro["nombre_largo_proyecto"] ?>    	  
	    </div>
	</div>
	<div class="form-group">
		<label for="">Fecha de Inicio del proyecto</label>
		 <div class="desabilitados">
			 <?=$datos_pro_fecha["fecha_inicio_proyecto"] ?>    	  
             
			 <input type="hidden" id="FinicioProy" name="FinicioProy" value="<?=$datos_pro_fecha["fecha_inicio_proyecto"] ?>" >             
	    </div>
	</div>

	<div class="form-group">
		<label for="">Fecha de Finalizaci&oacute;n del proyecto</label>
		 <div class="desabilitados">
			 <?=$datos_pro_fecha["fecha_final_proyecto"] ?>    	  
			 <input type="hidden" id="FinalProy" name="FinalProy" value="<?=$datos_pro_fecha["fecha_final_proyecto"] ?>" >                          
	    </div>
	</div>
	<div class="form-group "  id="divArchivo">
		<label for="">Documento de Reanudaci&oacute;n</label>
        <span class="form-group">
        <label for="label"> (.PDF &oacute; .Doc)</label>
        </span><br>
        <span>Actual: <?=$doc ?> <input type="hidden" value="<?=$doc ?>" name="nomDocAnt" id="nomDocAnt"> </span>
<?php        
		if($acc==2)
		{
?>                
		<input type="file" class="file" id="Archivo" name="Archivo"  >
		 <span id="helpArchivoExt" class="help-block" style="display:none;" >Adjunte un archivo con extenci&oacute;n .pdf o .doc.</span>                         
<?php
		}
?>    
  </div>
	<div class="form-group"  id="divFinicio">
		<label for="">Fecha de la Reanudaci&oacute;n</label>

        <div class="container-fluid">
          <div class="col-sm-3">
           <div class="input-group">

                <input class="form-control" id="Finicio" name="Finicio"  placeholder="MM/DD/YYYY" value="<?=$Finicio ?>"   readonly type="text"/>

        	</div>
            </div>
            </div>
		<span class="help-block" id="helpFinicio" style="display:none;">La fecha de la reanudaci&oacute;n es obligatoria.</span>
        <span id="helpFinicio2" class="help-block" style="display:none;" >La fecha de la reanudaci&oacute;n, es inferior a la fecha de inicio del proyecto, por favor verifique.</span>   
        
		<span id="helpFinal3" class="help-block" style="display:none;" >La fecha de la reanudaci&oacute;n, es mayor a la fecha de finalizaci&oacute;n del proyecto, por favor verifique.</span>         
	</div>
	<div class="form-group" id="divobservaciones">
		<label for="">Observaciones</label>
		<textarea name="observaciones" class="form-control" id="observaciones"  cols="10" rows="3" <?=$dis ?>><?=$observaciones ?></textarea>
		<span class="help-block" id="helpobservaciones" style="display:none;">Las observaciones son obligatorias.</span>
        <input type="hidden" name="observaciones_ant" id="observaciones_ant" value="<?=$observaciones ?>">
	</div>

	<div class="form-group" align="right">
<?php        
		
		$mens="";
		if($acc==2)
			$mens="Actualizar";
		if($acc==3)
			$mens="Eliminar";			
			
?>
    
    	<button type="button" class="btn btn-primary" onClick="valida()"><?=$mens ?></button>
         <input name="recarga" type="hidden" id="recarga" value="1">
    </div> 
</form>

<?php
	include("inferior.php"); 
?>