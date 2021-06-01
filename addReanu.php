<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente
banner(2,"Nueva Reanudaci&oacute;n");

//include ("../../verificaRegistro2.php");
if($recarga==2)
{
	
//	$cursorIn1="no error";
	
//echo "Ingresssaa ---cursorIn1=".$cursorIn1;	

	$Archivo_name=remplazaCaracteresEsp($Archivo_name);		
	$sql_doc="select * from EFReanudaciones where id_proyecto=".$Proy." and nombre_doc_reanudacion='".$Archivo_name."'";
	$cur_doc=mssql_query($sql_doc);
//echo $sql_doc." *** ".mssql_num_rows($cur_doc);
	if(mssql_num_rows($cur_doc)==0)
	{	
		
		mssql_query("begin transaction");			
		
		//	CONSULTA EL CONSECUTIVO DE LA SUSPENSION
		$sql_max_susp="select  isnull(max(id_reanudacion),0)+1 max_reanudacion from EFReanudaciones";
		$cursorIn1=mssql_query($sql_max_susp);
		$datos_max_susp=mssql_fetch_array($cursorIn1);			
		
		if($cursorIn1!="")
		{	
		
//			$Archivo_name=remplazaCaracteresEsp($Archivo_name);		
	
			$sql_insert="insert into EFReanudaciones (id_reanudacion, id_proyecto, fecha, observaciones, nombre_doc_reanudacion, url_doc_reanudacion, fechaGraba, usuarioGraba )
						values (".$datos_max_susp["max_reanudacion"].", ".$Proy .",'".$Finicio."' ,'".$observaciones."', '".$Archivo_name."' , 'files/".$Proy."/filesReanudaciones/".$datos_max_susp["max_reanudacion"]."/".$Archivo_name."' , getdate() , " . $_SESSION["sesUnidadUsuario"] . "   ) ";
			$cursorIn1=mssql_query($sql_insert);
	//echo $sql_insert."<br>".mssql_get_last_message();						
			if($cursorIn1!="")
			{
				//AOSICIA AL LA ULTIMA SUSPENCION REGISTRADA, LA REANUDACION
				$sql_susp="update EFSuspenciones set id_reanudacion=".$datos_max_susp["max_reanudacion"]." where id_suspencion=( select max(id_suspencion) from EFSuspenciones where id_proyecto=".$Proy ."  )	";
				$cursorIn1 = mssql_query($sql_susp);														
				if($cursorIn1!="")
				{			
					//SE DESACTIVA EL ESTADO ANTERIOR						
					$sqlIn1 =" UPDATE EFEstados_Proy_Proyectos SET estado_actual=0 where id_proyecto=".$Proy;
					$cursorIn1 = mssql_query($sqlIn1);											
	//	echo $sqlIn1."<br>".mssql_get_last_message();										
					if($cursorIn1!="")
					{
						//SE REGISTRA EL ESTADO DEL PROYECTO A "EN EJECUCION"
						$sqlIn1 ="insert into EFEstados_Proy_Proyectos ( id_estados_proy_proyectos, id_estado_proy, id_proyecto, fecha_estado, estado_actual, tipo_accion, id_consecutivo, usuarioGraba,fechaGraba) 
						values( (select MAX(id_estados_proy_proyectos)+1 from EFEstados_Proy_Proyectos where id_proyecto='".$Proy."') , 3 , '".$Proy."' ,getdate(), 1, 2,".$datos_max_susp["max_reanudacion"]."," . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
							
						$cursorIn1 = mssql_query($sqlIn1);				
	//	echo $sqlIn1."<br>".mssql_get_last_message();
		
						if($cursorIn1!="")
						{
		
							//SI NO EXISTE EL DIRECTORIO DEL PROYECTO
							if (!file_exists("files/".$Proy."")) 
							{
								//CREA LA CARPETA DEL PROYECTO
								if(!mkdir("files/".$Proy."",0777))
								{
									$cursorIn1="";
								}		
							}
								
							$path1 = "files/".$Proy."/filesReanudaciones";
							//SI NO EXISTE EL DIRECTORIO DE SUSPENCIONES EN EL PROYECTO
							if (!file_exists($path1)) 
							{
								//CREA LA CARPETA DE LAS REANUDACION
								if(!mkdir($path1,0777))
								{
									$cursorIn1="";
								}		
							}
							
							
							if($cursorIn1!="")
							{	
								
								$path1.="/".$datos_max_susp["max_reanudacion"];
								//CREA LA CARPETA DE LA REANUDACION
								if(!mkdir($path1,0777))
								{
							//		$cursorIn1="";
								}		
							}
							
							if($cursorIn1!="")
							{
		//					echo $path1." ---- ".$Archivo_name." *** ".$Archivo."<br>";				
		//						$Archivo_name=remplazaCaracteresEsp($Archivo_name);	
								//SUBE EL ARCHIVO DE LA REANUDACION
								if(!subirArchivo($path1,$Archivo_name,$Archivo))
								{	
									//SI SE PRESENTO UN ERROR, ELIMINA LA CARPETA DE LA REANUDACION
									delete_dir($path1);
									$cursorIn1="";	
								}
		//					echo $path1." ---- ".$Archivo_name." *** ".$Archivo."<br>";		
							}		
						}
						
					}
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

//CONSULTA LA FECHA DE LA ULTIMA SUSPENCION
$sql_FECHA_ULT_SUSp="select CONVERT(varchar, dateadd(dd,1,fecha_finalizacion) ,101) fecha_fin_ul_susp from EFSuspenciones where id_proyecto=".$Proy ." and id_suspencion =(
select MAX(id_suspencion) from EFSuspenciones where id_proyecto=".$Proy .")";
$cur_fecha_ult_susp=mssql_query($sql_FECHA_ULT_SUSp);

$datos_fecha_ult_susp=mssql_fetch_array($cur_fecha_ult_susp);
$fecha_fin_ul_susp=$datos_fecha_ult_susp["fecha_fin_ul_susp"];

?>


<script type="text/javascript">
function valida()
{

	document.getElementById("divFinicio").className="form-group";		
	document.getElementById("helpFinicio2").style.display="none";    
	
 	var campos_tex = ["observaciones","Finicio","Archivo"];	
  
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

	var archivo = document.formulario.Archivo.value;
	var extension_permit=[".pdf",".PDF",".DOC",".doc",".docx",".DOCX"];
	error+=valida_extension(archivo, extension_permit,"Archivo");

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
		<label for="">Documento de Reanudaci&oacute;n <span class="form-group"> (.PDF &oacute; .Doc) </span></label>
		<input type="file" class="file" id="Archivo" name="Archivo" >
		<span id="helpArchivo" class="help-block" style="display:none;">Por favor adjunte el documento de reanudaci&oacute;n.</span>
		 <span id="helpArchivoExt" class="help-block" style="display:none;" >Adjunte un archivo con extenci&oacute;n .pdf o .doc.</span>                 
	</div>
	<div class="form-group"  id="divFinicio">
		<label for="">Fecha de la Reanudaci&oacute;n</label>

        <div class="container-fluid">
          <div class="col-sm-3">
           <div class="input-group">

                <input class="form-control" id="Finicio" name="Finicio"  placeholder="MM/DD/YYYY" value="<?=$fecha_fin_ul_susp ?>"   readonly type="text"/>

        	</div>
            </div>
            </div>
		<span class="help-block" id="helpFinicio" style="display:none;">La fecha de la reanudaci&oacute;n es obligatoria.</span>
        <span id="helpFinicio2" class="help-block" style="display:none;" >La fecha de la reanudaci&oacute;n, es inferior a la fecha de inicio del proyecto, por favor verifique.</span>   
        
		<span id="helpFinal3" class="help-block" style="display:none;" >La fecha de la reanudaci&oacute;n, es mayor a la fecha de finalizaci&oacute;n del proyecto, por favor verifique.</span>         
  </div>
  <div class="form-group" id="divobservaciones">
		<label for="">Observaciones</label>
		<textarea name="observaciones" class="form-control" id="observaciones" cols="10" rows="3"><?=$observaciones ?></textarea>
		<span class="help-block" id="helpobservaciones" style="display:none;">Las observaciones son obligatorias.</span>
	</div>

	<div class="form-group" align="right">
    	<button type="button" class="btn btn-primary" onClick="valida()">Grabar</button>
         <input name="recarga" type="hidden" id="recarga" value="1">
    </div> 
</form>

<?php
	include("inferior.php"); 
?>