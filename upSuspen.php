<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente

if($acc==2)
	$mens_v="Actualizar Suspenci&oacute;n";	   		
if($acc==3)
	$mens_v="Eliminar Suspenci&oacute;n";	 
	
banner(2,$mens_v);

//include ("../../verificaRegistro2.php");
if($recarga==2)
{
	
//	$cursorIn1="no error";
	
//echo "Ingresssaa ---cursorIn1=".$cursorIn1;	

	if(trim($Archivo_name)!="")
	{
		$Archivo_name=remplazaCaracteresEsp($Archivo_name);		
		$sql_doc="select * from EFSuspenciones where id_proyecto=".$Proy." and nombre_doc_suspencion='".$Archivo_name."'";
		$cur_doc=mssql_query($sql_doc);
	}
//echo $sql_doc." *** ".mssql_num_rows($cur_doc);
	if(mssql_num_rows($cur_doc)==0)
	{	
			
		mssql_query("begin transaction");			
		
		if($acc==2)	
		{
//			$Archivo_name=remplazaCaracteresEsp($Archivo_name);			
		
		//nomDocAnt
			$sql_insert="update EFSuspenciones SET fecha_inicio='".$Finicio."',fecha_finalizacion='".$Final."',observaciones='".$observaciones."' ";
			if(trim($Archivo_name)!="")
			{
				$sql_insert.=", nombre_doc_suspencion='".$Archivo_name."' ,url_doc_suspencion='files/".$Proy."/filesSuspenciones/".$id."/".$Archivo_name."' ";
			}
			$sql_insert.=", usuarioMod=  " . $_SESSION["sesUnidadUsuario"] . " , fechaMod=getdate()
						where id_proyecto=".$Proy ." and id_suspencion=".$id." ";
			$cursorIn1=mssql_query($sql_insert);
	//echo $sql_insert."<br>".mssql_get_last_message();	
		
			if($cursorIn1!="")
			{
				//SI SE HA ADJUNTADO UN DOCUMENTO
				if($Archivo_name!="")
				{		
					$path1 = "files/".$Proy."/filesSuspenciones/".$id;						
		
					//SUBE EL ARCHIVO DE LA SUSPENSION
					if(!subirArchivo($path1,$Archivo_name,$Archivo))
					{	
						//SI SE PRESENTO UN ERROR, ELIMINA LA CARPETA DE LA SUSPENSION
						//delete_dir($path1);
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
				}
			}
		}
		if($acc==3)
		{
			$cursorIn1="No_error";
			
			//CONSULTA EL ID DE LA PRIMERA SUSPENCION REGISTRADA EN EL PROYECTO
			$sql_vr_max_sus="select MIN(id_suspencion) min_sus from EFSuspenciones where id_proyecto=".$Proy." ";
			$cursorIn1=mssql_query($sql_vr_max_sus);
			$datos_vr_min_sus=mssql_fetch_array($cursorIn1);
			
			//si la suspencion a eliminar es LA PRIMERA SUSPENCION registrada EN EL PROYECTO		
			if($datos_vr_min_sus["min_sus"]==$id)		
			{
				//SE DESACTIVA EL ESTADO ANTERIOR						
				$sqlIn1 =" UPDATE EFEstados_Proy_Proyectos SET estado_actual=0 where id_proyecto=".$Proy;
				$cursorIn1 = mssql_query($sqlIn1);	
	//echo $sqlIn1."<br>".mssql_get_last_message()."<br><br>";										
				if($cursorIn1!="")
				{
					//SE REGISTRA EL ESTADO DEL PROYECTO A "EJECUCION"
					$sqlIn1 ="insert into EFEstados_Proy_Proyectos ( id_estados_proy_proyectos, id_estado_proy, id_proyecto, fecha_estado, estado_actual, tipo_accion, id_consecutivo, usuarioGraba,fechaGraba) 
					values( (select MAX(id_estados_proy_proyectos)+1 from EFEstados_Proy_Proyectos where id_proyecto='".$Proy."') , 3 , '".$Proy."' ,getdate(), 1, NULL ,NULL," . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
					$cursorIn1=mssql_query($sqlIn1);
	//echo $sqlIn1."<br>".mssql_get_last_message()."<br><br>";														
				}		
			}
			
			
			if($cursorIn1!="")
			{			
				//ELIMINA LA SUSPENCION
				$sql_insert="delete EFSuspenciones  where id_proyecto=".$Proy ." and id_suspencion=".$id." ";
				$cursorIn1=mssql_query($sql_insert);
	//echo $sql_insert."<br>".mssql_get_last_message();		
			
				if($cursorIn1!="")
				{	
					//CONSULTA SI LA SUSPENCION ANTERIOR A LA ELIMINADA, TIENE ASOCIADA UNA REANUDACION
					$sq_ver_susp="select id_reanudacion from EFSuspenciones WHERE id_proyecto=".$Proy ." and id_suspencion=(select MAX(id_suspencion) from EFSuspenciones WHERE id_proyecto=".$Proy .")";
					$cursorIn1=mssql_query($sq_ver_susp);
					$dato_susp_ant=mssql_fetch_array($cursorIn1);
					
					//SI TIENE UNA REANUDACION
					if($dato_susp_ant["id_reanudacion"]!="")
					{
						//SE DESACTIVA EL ESTADO ANTERIOR						
						$sqlIn1 =" UPDATE EFEstados_Proy_Proyectos SET estado_actual=0 where id_proyecto=".$Proy;
						$cursorIn1 = mssql_query($sqlIn1);	
	//		echo $sqlIn1."<br>".mssql_get_last_message()."<br><br>";						
						if($cursorIn1!="")
						{	
							//SE REGISTRA EL ESTADO DEL PROYECTO A "EJECUCION"
							$sqlIn1 ="insert into EFEstados_Proy_Proyectos ( id_estados_proy_proyectos, id_estado_proy, id_proyecto, fecha_estado, estado_actual, tipo_accion, id_consecutivo, usuarioGraba,fechaGraba) 
							values( (select MAX(id_estados_proy_proyectos)+1 from EFEstados_Proy_Proyectos where id_proyecto='".$Proy."') , 3 , '".$Proy."' ,getdate(), 1, NULL ,NULL," . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
							$cursorIn1=mssql_query($sqlIn1);
	//		echo $sqlIn1."<br>".mssql_get_last_message()."<br><br>";						
						}
					}
				}
				
			}
			if($cursorIn1!="")
			{
	//echo "<BR>iNGRESSA ---- "."files/".$Proy."/filesSuspenciones/".$id;			
				$path1 = "files/".$Proy."/filesSuspenciones/".$id;		
				
				if(!del_arch($path1."/".$nomDocAnt))
				{
					$cursorIn1="";
				}										
				
				if($cursorIn1!="")
				{				
					if(!delete_dir($path1))
					{
	//	echo "<BR>ERROR";
						$cursorIn1="";
					}
				}
	//   $error = error_get_last();
	//    echo "<BR>".$error['message']."<br>";				
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
		echo ("<script>alert('Ya existe un documento de suspenci\u00f3n con en ese nombre, por favor adjunte uno diferente.');</script>");
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

//CONSULTA LA INFORMACION DE LA SUSPENCION

$sql_info_susp="SELECT id_suspencion, id_proyecto,  CONVERT(varchar, fecha_inicio,101) fecha_inici, CONVERT(varchar, fecha_finalizacion,101) fecha_finalizaci, observaciones, nombre_doc_suspencion, url_doc_suspencion, id_suspencion_padre, id_reanudacion, usuarioGraba, fechaGraba FROM EFSuspenciones where id_proyecto=".$Proy ."  and id_suspencion=".$id;
$cur_info_susp=mssql_query($sql_info_susp);

//VARIABLE QUE CARGA LA FECHA DE INICIO DE LA SUSPENCION, CUANDO LA UTIMA SUSPENCION NO TIENE REANUDACION
$FIS="";
if($datos_info_susp=mssql_fetch_array($cur_info_susp))
{
	$observaciones=$datos_info_susp["observaciones"];
	
	$Finicio=$datos_info_susp["fecha_inici"];	
	$Final=$datos_info_susp["fecha_finalizaci"];	
	$reanudacion=$datos_info_susp["id_reanudacion"];
	$doc=$datos_info_susp["nombre_doc_suspencion"];
	
	//SI LA SUSPENCIONA A ACTUALIZAR, TIENE UNA SUSPENCION PADRE, O A LA CUAL EXTIENDE, SE CARGA LA VARIABLE $FIS
	//PARA QUE NO PRMITA MODIFICAR LA FECHA DE INICIO
	if($datos_info_susp["id_suspencion_padre"]!="")
		$FIS=$datos_info_susp["fecha_inici"];
	else
		$Finicio=$datos_info_susp["fecha_inici"];

}


//VRIFICA SI LA UTIMA SUSPENCION NO TIENE REANUDACION
$sql_max_susp_padre="select  CONVERT(varchar, dateadd(dd,1,fecha_finalizacion) ,101) fecha_inicio_sig_suspencion,* from EFSuspenciones where id_proyecto=".$Proy ." and id_reanudacion is null and 
id_suspencion=( select max(id_suspencion) from EFSuspenciones where id_proyecto=".$Proy ." and id_suspencion <>".$id."   ) ";
$cur_max_susp_padre=mssql_query($sql_max_susp_padre);

//VARIABLE QUE CARGA LA FECHA DE INICIO DE LA SUSPENCION, CUANDO LA UTIMA SUSPENCION NO TIENE REANUDACION
$FIS="";
if(mssql_num_rows($cur_max_susp_padre)>0)
{
	$datos_max_sus=mssql_fetch_array($cur_max_susp_padre);
	$FIS=$datos_max_sus["fecha_inicio_sig_suspencion"];
}
else //SI LA ULTIMA SUSPENCION TIENE ASOCIADA UNA REANUDACION
{
	//CONSULTA LA FECHA de la UlTIMA REANUDACION
	$sq_ver_susp="select   CONVERT(varchar,fecha,101)  fecha_R from EFReanudaciones WHERE id_proyecto=".$Proy ." and id_reanudacion=(select MAX(id_reanudacion) from EFReanudaciones WHERE id_proyecto=".$Proy .")";
	$cursorIn1=mssql_query($sq_ver_susp);
	$dato_ult_reanudacion=mssql_fetch_array($cursorIn1);	
	
	$fecha_reanudacion=$dato_ult_reanudacion["fecha_R"];
}

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
	document.getElementById("helpFinicio3").style.display="none";    	
	
	document.getElementById("divFinal").className="form-group";		
	document.getElementById("helpFinal3").style.display="none";    	

 	var campos_tex = ["observaciones","Final","Finicio"];	
  
	var error=0;
	
	error=valida_campos(campos_tex,1);

	//fechas
	error+=valida_campos("",5);
    	
	if(document.getElementById("Finicio").value!="")
	{
		//SI LA FEHCA DE LA SUSPENCION ES INFERIOR A LA FECHA DE INICIO DEL PROYECTO
		if (compare_fecha( document.getElementById("FinicioProy").value , document.getElementById("Finicio").value))
		{  
			document.getElementById("divFinicio").className="form-group has-error";		
			document.getElementById("helpFinicio2").style.display="inline-block";		
			error++;
		}	
		
<?php
		//SI HAY UNA REANUDACION ANTES
		if($fecha_reanudacion!="")
		{
	?>
			//SI LA FEHCA DE LA SUSPENCION ES INFERIOR A LA FECHA DE LA REANUDACION REGISTRADA CON ANTERIORIDAD
			if (compare_fecha( '<?=$fecha_reanudacion ?>', document.getElementById("Finicio").value))
			{  
				document.getElementById("divFinicio").className="form-group has-error";		
				document.getElementById("helpFinicio3").style.display="inline-block";		
				error++;
			}	
	<?PHP		
		}
?>			
	}
	if(document.getElementById("Final").value!="")
	{
		//SI LA FEHCA FINAL DE LA SUSPENCION ES INFERIOR A LA FECHA DE INICIO DEL PROYECTO
		if (compare_fecha( document.getElementById("Final").value , document.getElementById("FinalProy").value))
		{  
			document.getElementById("divFinal").className="form-group has-error";		
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
//alert(" ------ "+error)	;
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
		<label for="">Documento de Suspenci&oacute;n </label>
        <span class="form-group">
        <label for="label"> (.PDF &oacute; .Doc)</label>
        </span><br>
        <span>Actual: <?=$doc ?> <input type="hidden" value="<?=$doc ?>" name="nomDocAnt" id="nomDocAnt"> </span>
<?php        
		if($acc==2)
		{
?>
			<input type="file" class="file" id="Archivo" name="Archivo"  >
<?php
		}
?>        
	  <span id="helpArchivo" class="help-block" style="display:none;">Por favor adjunte el documento de suspenci&oacute;n.</span>
		 <span id="helpArchivoExt" class="help-block" style="display:none;" >Adjunte un archivo con extenci&oacute;n .pdf o .doc.</span>               
	</div>
	<div class="form-group"  id="divFinicio">
		<label for="">Fecha de Inicio de la Suspenci&oacute;n</label>

        <div class="container-fluid">
          <div class="col-sm-3">
           <div class="input-group">
<?php
			if($FIS!="")
			{
?>
                <input class="form-control"  placeholder="MM/DD/YYYY" value="<?=$FIS ?>"   readonly type="text" <?=$dis ?> />
                <input type="hidden" id="Finicio" name="Finicio"  value="<?=$FIS ?>" >
<?php				
			}
			else
			{
?>			
                <input class="form-control" id="Finicio" name="Finicio" placeholder="MM/DD/YYYY" value="<?=$Finicio ?>"  <?=$dis ?>  readonly type="text"/>
                <div class="input-group-addon">
                 <i class="glyphicon glyphicon-calendar">
                 </i>
	            </div>                  
<?php
			}
?>             

        	</div>
            </div>
            </div>
		<span class="help-block" id="helpFinicio" style="display:none;">La fecha de inicio de la suspenci&oacute;n es obligatoria.</span>
        <span id="helpFinicio2" class="help-block" style="display:none;" >La fecha de inicio de la suspenci&oacute;n, es inferior a la fecha de inicio del proyecto, por favor verifique.</span>   
   		<span class="help-block" id="helpFinicio3" style="display:none;">La fecha de inicio de la suspenci&oacute;n no puede ser inferior a la fecha de la reanudaci&oacute;n (<?=$fecha_reanudacion ?>) previamente registrada.</span>        
	</div>
	<div class="form-group" id="divFinal">
		<label for="">Fecha de Finalizaci&oacute;n de la Suspenci&oacute;n</label>

        <div class="container-fluid">
          <div class="col-sm-3">
           <div class="input-group">
                <input class="form-control" id="Final" name="Final" placeholder="MM/DD/YYYY" value="<?=$Final ?>"   readonly type="text" <?=$dis ?> />
                <div class="input-group-addon">
                 <i class="glyphicon glyphicon-calendar">
        		 </i>
        	</div>
            </div>
            </div>                 
        </div>         
		<span class="help-block"  id="helpFinal" style="display:none;">La fecha de finalizaci&oacute;n de la suspenci&oacute;n es obligatoria.</span>
	    <span id="helpFinal2" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n de la suspenci&oacute;n, es menor a la fecha de inicio de la suspenci&oacute;n, por favor verifique.</span>             
	    <span id="helpFinal3" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n de la suspenci&oacute;n, es mayor a la fecha de finalizaci&oacute;n del proyecto, por favor verifique.</span>                     
	</div>
	<div class="form-group" id="divobservaciones">
		<label for="">Observaciones</label>
		<textarea name="observaciones" class="form-control" id="observaciones" cols="10" rows="3" <?=$dis ?>><?=$observaciones ?></textarea>
		<span class="help-block" id="helpobservaciones" style="display:none;">Las observaciones son obligatorias.</span>
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