<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente
banner(2,"Nueva Suspenci&oacute;n");

//include ("../../verificaRegistro2.php");
if($recarga==2)
{
	
//	$cursorIn1="no error";
	
//echo "Ingresssaa ---cursorIn1=".$cursorIn1;	
	$Archivo_name=remplazaCaracteresEsp($Archivo_name);		
	$sql_doc="select * from EFSuspenciones where id_proyecto=".$Proy." and nombre_doc_suspencion='".$Archivo_name."'";
	$cur_doc=mssql_query($sql_doc);
//echo $sql_doc." *** ".mssql_num_rows($cur_doc);
	if(mssql_num_rows($cur_doc)==0)
	{	
		mssql_query("begin transaction");			
		
		//	CONSULTA EL CONSECUTIVO DE LA SUSPENSION
		$sql_max_susp="select  isnull(max(id_suspencion),0)+1 max_suspencion from EFSuspenciones";
		$cur_max_susp=mssql_query($sql_max_susp);
		$datos_max_susp=mssql_fetch_array($cur_max_susp);			
		
		//CONSULTA SI LA ULTIMA SUSPENSION REGISTRADA, 
		$SQL_SUS_PADRE="select id_suspencion, id_reanudacion,id_suspencion_padre from EFSuspenciones 
		WHERE id_suspencion = ( 
			select MAX(id_suspencion) from EFSuspenciones WHERE id_proyecto=".$Proy ." 
		) AND id_proyecto=".$Proy ." ";
		
		$cursorIn1=mssql_query($SQL_SUS_PADRE);
		$datos_sus_padre=mssql_fetch_array($cursorIn1);		
			
	//echo $SQL_SUS_PADRE."<br>".mssql_get_last_message();			
		//SE ASIGNA LA SUSPENCION PADRE A LA SUSPENCION A REGISTRAR		
		//SI LA ULTIMA SUSPENSION REGISTRADA EN EL PROYECTO:
		
		//SI NO HAY INFORMACION DE SUSPENCIONES REGISTRADAS
		if (($datos_sus_padre["id_reanudacion"]=="") && ($datos_sus_padre["id_suspencion"]=="")  && ($datos_sus_padre["id_suspencion_padre"]=="") )
		{
			$suspencion_padre="NULL";
		}
		//SI LA SUSPENCION NO TIENE REGISTRO DE REANUDACION, NI SUSPENSION PADRE
		if (($datos_sus_padre["id_reanudacion"]=="") && ($datos_sus_padre["id_suspencion"]!="")  && ($datos_sus_padre["id_suspencion_padre"]=="") )
		{
			$suspencion_padre=$datos_sus_padre["id_suspencion"];
		}
		//SI LA SUSPENCION TIENE SUSPENCION PADRE Y NO TIENE REANUDACION
		if (($datos_sus_padre["id_reanudacion"]=="")  && ($datos_sus_padre["id_suspencion_padre"]!="") )
		{
				$suspencion_padre=$datos_sus_padre["id_suspencion"];
		}
		//SI LA SUSPENCION TIENE SUSPENCION PADRE Y  TIENE REANUDACION
		if (($datos_sus_padre["id_reanudacion"]!="")  && ($datos_sus_padre["id_suspencion_padre"]!="") )		
		{
			$suspencion_padre="NULL";			
		}
	
		//SI LA SUSPENCION TIENE REANUDACION, SUSPENCION Y NO TIENE SUSPENCION PADRE
		if (($datos_sus_padre["id_reanudacion"]!="") && ($datos_sus_padre["id_suspencion"]!="")  && ($datos_sus_padre["id_suspencion_padre"]=="") )		
		{
			$suspencion_padre="NULL";			
		}
	
	
		if($cursorIn1!="")
		{	
		
			$sql_insert="insert into EFSuspenciones (id_suspencion, id_proyecto,fecha_inicio,fecha_finalizacion,observaciones, nombre_doc_suspencion ,url_doc_suspencion, id_suspencion_padre ,fechaGraba, usuarioGraba)
						values (".$datos_max_susp["max_suspencion"].", ".$Proy .",'".$Finicio."', '".$Final."' ,'".$observaciones."', '".$Archivo_name."' , 'files/".$Proy."/filesSuspenciones/".$datos_max_susp["max_suspencion"]."/".$Archivo_name."', ".$suspencion_padre." , getdate() , " . $_SESSION["sesUnidadUsuario"] . "   ) ";
			$cursorIn1=mssql_query($sql_insert);
	//echo $sql_insert."<br>".mssql_get_last_message();						
			if($cursorIn1!="")
			{
				//SE DESACTIVA EL ESTADO ANTERIOR						
				$sqlIn1 =" UPDATE EFEstados_Proy_Proyectos SET estado_actual=0 where id_proyecto=".$Proy;
				$cursorIn1 = mssql_query($sqlIn1);											
	//echo $sqlIn1."<br>".mssql_get_last_message();										
				if($cursorIn1!="")
				{
					//SE REGISTRA EL ESTADO DEL PROYECTO A "SUSPENDIDO"
					$sqlIn1 ="insert into EFEstados_Proy_Proyectos ( id_estados_proy_proyectos, id_estado_proy, id_proyecto, fecha_estado, estado_actual, tipo_accion, id_consecutivo, usuarioGraba,fechaGraba) 
					values( (select MAX(id_estados_proy_proyectos)+1 from EFEstados_Proy_Proyectos where id_proyecto='".$Proy."') , 5 , '".$Proy."' ,getdate(), 1, 1,".$datos_max_susp["max_suspencion"]."," . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
						
					$cursorIn1 = mssql_query($sqlIn1);				
	//echo $sqlIn1."<br>".mssql_get_last_message();
	
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
	
						$path1 = "files/".$Proy."/filesSuspenciones";
						//SI NO EXISTE EL DIRECTORIO DE SUSPENCIONES EN EL PROYECTO
						if (!file_exists($path1)) 
						{
							//CREA LA CARPETA DE LAS SUSPENCIONES
							if(!mkdir($path1,0777))
							{
								$cursorIn1="";
							}		
						}
						
						
						if($cursorIn1!="")
						{	
							
							$path1.="/".$datos_max_susp["max_suspencion"];
							//CREA LA CARPETA DE LA SUSPENCION
							if(!mkdir($path1,0777))
							{
						//		$cursorIn1="";
							}		
						}
						
						if($cursorIn1!="")
						{
	//					echo $path1." ---- ".$Archivo_name." *** ".$Archivo."<br>";				
	//						$Archivo_name=remplazaCaracteresEsp($Archivo_name);	
							//SUBE EL ARCHIVO DE LA SUSPENSION
							if(!subirArchivo($path1,$Archivo_name,$Archivo))
							{	
								//SI SE PRESENTO UN ERROR, ELIMINA LA CARPETA DE LA SUSPENSION
								delete_dir($path1);
								$cursorIn1="";	
							}
	/*						
						echo $path1." ---- ".$Archivo_name." *** ".$Archivo."<br>";		
	   $error = error_get_last();
	   */
	//   echo "<BR>".$error['message']."<br>";							
						}		
					}
					
				}
			}
		}
		
		
		if  (trim($cursorIn1) != "")  {
			mssql_query("commit transaction");		
//				mssql_query("rollback  transaction");		
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

//VRIFICA SI LA UTIMA SUSPENCION NO TIENE REANUDACION
$sql_max_susp_padre="select  CONVERT(varchar, dateadd(dd,1,fecha_finalizacion) ,101) fecha_inicio_sig_suspencion,* from EFSuspenciones where id_proyecto=".$Proy ." and id_reanudacion is null and 
id_suspencion=( select max(id_suspencion) from EFSuspenciones where id_proyecto=".$Proy ."  ) ";
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
?>


<script type="text/javascript">
function valida()
{

	document.getElementById("divFinicio").className="form-group";		
	document.getElementById("helpFinicio2").style.display="none";    
	document.getElementById("helpFinicio3").style.display="none";    	
	
	document.getElementById("divFinal").className="form-group";		
	document.getElementById("helpFinal3").style.display="none";    	

 	var campos_tex = ["observaciones","Final","Finicio","Archivo"];	
  
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
	

	

	var archivo = document.formulario.Archivo.value;
	var extension_permit=[".pdf",".PDF",".DOC",".doc",".docx",".DOCX"];
	error+=valida_extension(archivo, extension_permit,"Archivo");
		
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
		<label for="">Documento de Suspenci&oacute;n <span class="form-group"> (.PDF &oacute; .Doc) </span></label>
		<input type="file" class="file" id="Archivo" name="Archivo" >
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
             <input class="form-control"  placeholder="MM/DD/YYYY" value="<?=$FIS ?>"   readonly type="text"/>
                <input type="hidden" id="Finicio" name="Finicio"  value="<?=$FIS ?>" >
<?php				
			}
			else
			{
?>			
             <input class="form-control" id="Finicio" name="Finicio" placeholder="MM/DD/YYYY" value=""   readonly type="text"/>
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
             <input class="form-control" id="Final" name="Final" placeholder="MM/DD/YYYY" value="<?=$Final ?>"   readonly type="text"/>
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