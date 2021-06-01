<?php 
include("encabezado.php"); 

//tipo=2 (para ventanas emergentes), titulo de la ventna emergente

if($acc==2)
	$mens_v="Actualizar Liquidaci&oacute;n";	   		
if($acc==3)
	$mens_v="Eliminar Liquidaci&oacute;n";	 

banner(2,$mens_v);

//include ("../../verificaRegistro2.php");
if($recarga==2)
{
	
//	$cursorIn1="no error";
	
//echo "Ingresssaa ---cursorIn1=".$cursorIn1;	
	
	mssql_query("begin transaction");			
	
	if($acc==2)
	{	
		$Archivo_name=remplazaCaracteresEsp($Archivo_name);		
	
		$sql_insert="update EFLiquidacion SET fecha ='".$Finicio."', observaciones = '".$observaciones."', ";
	
		if(trim($Archivo_name)!="")
		{	
			 $sql_insert.="nombre_doc_liquidacion='".$Archivo_name."' , url_doc_liquidacion='files/".$Proy."/filesLiquidaciones/".$id."/".$Archivo_name."' , ";
		}
		
		$sql_insert.="valor=".$Valor.", fechaMod=getdate(), usuarioMod=" . $_SESSION["sesUnidadUsuario"]." WHERE id_liquidacion=".$id." AND id_proyecto=".$Proy." ";
		$cursorIn1=mssql_query($sql_insert);
//	echo $sql_insert."<br>".mssql_get_last_message();						
										
			if($cursorIn1!="")
			{												
				if(trim($Archivo_name)!="")
				{		
					$path1 = "files/".$Proy."/filesLiquidaciones/".$id;	
					if($cursorIn1!="")
					{
						//SUBE EL ARCHIVO DE LA LIQUIDACION
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
//		   $error = error_get_last();
//			echo "<BR>".$error['message']."<br>";						
//									echo $path1." ---- ".$Archivo_name." *** ".$Archivo."<br>";		
					}	
				}
			}
			
		
	}
	if($acc==3)
	{
		$sql_insert="delete from EFLiquidacion WHERE id_liquidacion=".$id." AND id_proyecto=".$Proy." ";
		$cursorIn1=mssql_query($sql_insert);		


		//SE DESACTIVA EL ESTADO ANTERIOR						
		$sqlIn1 =" UPDATE EFEstados_Proy_Proyectos SET estado_actual=0 where id_proyecto=".$Proy;
		$cursorIn1 = mssql_query($sqlIn1);	
//echo $sqlIn1."<br>".mssql_get_last_message()."<br><br>";										
		if($cursorIn1!="")
		{
			//SE REGISTRA EL ESTADO QUE TUVO EL PROYECTO ANTES DE LA LIQUIDACION ("EN EJECUCION" O "TERMINADO")
			$sqlIn1 ="insert into EFEstados_Proy_Proyectos ( id_estados_proy_proyectos, id_estado_proy, id_proyecto, fecha_estado, estado_actual, tipo_accion, id_consecutivo, usuarioGraba,fechaGraba) 
			values( (select MAX(id_estados_proy_proyectos)+1 from EFEstados_Proy_Proyectos where id_proyecto='".$Proy."') , (
				select id_estado_proy from EFEstados_Proy_Proyectos where id_proyecto= '".$Proy."'
and id_estados_proy_proyectos=( select MAX(id_estados_proy_proyectos) from EFEstados_Proy_Proyectos where id_proyecto= '".$Proy."' and id_estado_proy<>11 )

			) , '".$Proy."' ,getdate(), 1, NULL ,NULL," . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
			$cursorIn1=mssql_query($sqlIn1);
//echo $sqlIn1."<br>".mssql_get_last_message()."<br><br>";														
		}	

		
		if($cursorIn1!="")
		{
//	echo "<BR>iNGRESSA ---- "."files/".$Proy."/filesReanudaciones/".$id;			
			$path1 = "files/".$Proy."/filesLiquidaciones/".$id;		
			
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
//   $error = error_get_last();
//		echo "<BR>".$error['message']."<br>";				
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

	echo ("<script>window.close(); MM_openBrWindow('infoG.php?Proy=".$Proy."&sec=1-1-7&imen=1','EF','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");				

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
$sql_FECHA_ULT_SUSp="select  CONVERT(varchar, fecha_finalizacion ,101) fecha_fin_ul_susp from EFSuspenciones where id_proyecto=".$Proy ." and id_suspencion =(
select MAX(id_suspencion) from EFSuspenciones where id_proyecto=".$Proy .")";
$cur_fecha_ult_susp=mssql_query($sql_FECHA_ULT_SUSp);

$datos_fecha_ult_susp=mssql_fetch_array($cur_fecha_ult_susp);
$fecha_fin_ul_susp=$datos_fecha_ult_susp["fecha_fin_ul_susp"];

//CONSULTA LA FECHA DE LA ULTIMA REANUDACION
$sql_FECHA_ULT_SUSp="select CONVERT(varchar, fecha ,101) fecha_finalizaciR from EFReanudaciones where id_proyecto=".$Proy ." and  id_reanudacion=(
	select max(id_reanudacion) from EFReanudaciones where id_proyecto=".$Proy .")";
$cur_fecha_ult_susp=mssql_query($sql_FECHA_ULT_SUSp);

$datos_fecha_ult_susp=mssql_fetch_array($cur_fecha_ult_susp);
$fecha_fin_ul_reanuda=$datos_fecha_ult_susp["fecha_finalizaciR"];


?>

<?php

		$sql_liq="select *, CONVERT(varchar, fecha ,101) fecha_L from EFLiquidacion WHERE id_proyecto=".$Proy;
		$cursor_liq=mssql_query($sql_liq);

		$datos_liq=mssql_fetch_array($cursor_liq);
		
		$Finicio=$datos_liq["fecha_L"];
		$Valor=$datos_liq["valor"];
		$observaciones=$datos_liq["observaciones"];
		$doc=$datos_liq["nombre_doc_liquidacion"];

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
	document.getElementById("helpFinal3").style.display="none";		
	document.getElementById("helpFinal4").style.display="none";	
	
 	var campos_tex = ["observaciones","Finicio","Valor"];	
  
	var error=0, fec_max_reanud="", fec_max_suspe="";
	
	fec_max_reanud="<?=$fecha_fin_ul_reanuda ?>";
	fec_max_suspe="<?=$fecha_fin_ul_susp ?>";

	error=valida_campos(campos_tex,1);
    	
	if(document.getElementById("Finicio").value!="")
	{
		//SI LA FEHCA DE LA LIQUIDACION ES INFERIOR A LA FECHA DE INICIO DEL PROYECTO
		if (compare_fecha( document.getElementById("FinicioProy").value , document.getElementById("Finicio").value))
		{  
			document.getElementById("divFinicio").className="form-group has-error";		
			document.getElementById("helpFinicio2").style.display="inline-block";		
			error++;
		}	

		//SI LA FEHCA DE LA LIQUIDACION ES SUPERIOR A LA FECHA DE FINALIZACION DEL PROYECTO
		if (compare_fecha( document.getElementById("Finicio").value , document.getElementById("FinalProy").value))
		{  
			document.getElementById("divFinicio").className="form-group has-error";		
			document.getElementById("helpFinal3").style.display="inline-block";		
			error++;
		}			
		
		if( (fec_max_reanud!="") || (fec_max_suspe!="") )
		{
			//SI LA FEHCA DE LA LIQUIDACION ES INFERIOR A LA FECHA DE LA ULTIMA REANUDACION REGISTRADA
			// O
			//SI LA FEHCA DE LA LIQUIDACION ES INFERIOR A LA FECHA DE LA ULTIMA SUSPENCION
			if((fec_max_reanud!=""))
			{
				if ( (compare_fecha( fec_max_reanud , document.getElementById("Finicio").value)) )
				{
					document.getElementById("divFinicio").className="form-group has-error";		
					document.getElementById("helpFinal4").style.display="inline-block";		
					error++;				
				}
			}

			if((fec_max_suspe!=""))
			{			
				if( compare_fecha( fec_max_suspe , document.getElementById("Finicio").value) )
				{
					document.getElementById("divFinicio").className="form-group has-error";		
					document.getElementById("helpFinal4").style.display="inline-block";		
					error++;				
				}
			}
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
		<label for="">Documento de Liquidaci&oacute;n <span class="form-group"> (.PDF &oacute; .Doc) </span></label>
        <br>
        <span>Actual: <?=$doc ?> <input type="hidden" value="<?=$doc ?>" name="nomDocAnt" id="nomDocAnt"> </span>
<?php        
		if($acc==2)
		{
?>                
		<input type="file" class="file" id="Archivo" name="Archivo"  >

		<span id="helpArchivo" class="help-block" style="display:none;">Por favor adjunte el documento de liquidaci&oacute;n.</span>
		 <span id="helpArchivoExt" class="help-block" style="display:none;" >Adjunte un archivo con extenci&oacute;n .pdf o .doc.</span>                          
<?php
		}
?>    
        


	</div>
	<div class="form-group"  id="divFinicio">
		<label for="">Fecha de la Liquidaci&oacute;n</label>

        <div class="container-fluid">
          <div class="col-sm-3">
           <div class="input-group">

				<input class="form-control" id="Finicio" name="Finicio" placeholder="MM/DD/YYYY" value="<?=$Finicio ?>" <?=$dis ?>  readonly type="text"/>
                <div class="input-group-addon">
                 <i class="glyphicon glyphicon-calendar">
                 </i>
	            </div>       

        	</div>
            </div>
            </div>
		<span class="help-block" id="helpFinicio" style="display:none;">La fecha de inicio de la liquidaci&oacute;n es obligatoria.</span>
        <span id="helpFinicio2" class="help-block" style="display:none;" >La fecha de la liquidaci&oacute;n, es inferior a la fecha de inicio del proyecto, por favor verifique.</span>           
		<span id="helpFinal3" class="help-block" style="display:none;" >La fecha de la liquidaci&oacute;n, es mayor a la fecha de finalizaci&oacute;n del proyecto, por favor verifique.</span>         
        	
	
        <span id="helpFinal4" class="help-block" style="display:none;" >La fecha de la liquidaci&oacute;n, es inferior a la fecha de finalizaci&oacute;n de la ultima suspenci&oacute;n <? if($fecha_fin_ul_susp!="") { echo "(".$fecha_fin_ul_susp.")"; } ?> o reanudaci&oacute;n <? if($fecha_fin_ul_susp!="") { echo "(".$fecha_fin_ul_reanuda.")"; } ?> registrada, por favor verifique.</span>         
  </div>

    <div class="form-group" id="divValor">
    <label for="">Valor</label>
    <input type="text" class="form-control" id="Valor" name="Valor" value="<?=$Valor ?>"  placeholder="Valor" onKeyPress="return acceptNum(event)" <?=$dis ?>>
     <span id="helpValor" class="help-block" style="display:none;" >El Valor de la liquidaci&oacute;n es obligatorio.</span>
  </div>    
  
  <div class="form-group" id="divobservaciones">
		<label for="">Observaciones</label>
		<textarea name="observaciones" class="form-control" id="observaciones" cols="10" rows="3" <?=$dis ?>><?=$observaciones ?></textarea>
		<span class="help-block" id="helpobservaciones" style="display:none;">Las observaciones son obligatorias.</span>
	</div>

<?php        
		
		$mens="";
		if($acc==2)
			$mens="Actualizar";
		if($acc==3)
			$mens="Eliminar";			
			
?>

	<div class="form-group" align="right">
    	<button type="button" class="btn btn-primary" onClick="valida()"><?=$mens ?></button>
         <input name="recarga" type="hidden" id="recarga" value="1">
    </div> 
</form>

<?php
	include("inferior.php"); 
?>