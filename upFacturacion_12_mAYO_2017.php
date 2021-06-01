<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente

if($acc==2)
	$mens_v="Actualizar Facturaci&oacute;n";	   		
if($acc==3)
	$mens_v="Eliminar Facturaci&oacute;n";	   

banner(2,$mens_v);


if($recarga==2)
{	

	mssql_query("begin transaction");		
	
	if($acc==2)
	{
		$sqlIn1 = " UPDATE  EFValores_Facturados SET valor_facturado=".$Valor.",  usuarioMod=" . $_SESSION["sesUnidadUsuario"] . ", fechaMod=getdate() WHERE id_valor_facturado=".$idF." and id_proyecto=".$Proy." ";
		$cursorIn1 = mssql_query($sqlIn1);						
	}

	if($acc==3)
	{
		$sqlIn1 = " DELETE FROM EFValores_Facturados WHERE id_valor_facturado=".$idF." and id_proyecto=".$Proy." ";
		$cursorIn1 = mssql_query($sqlIn1);								
	}
		
//echo $sqlIn1."<br>".mssql_get_last_message()."-----<BR>";		

	if  (trim($cursorIn1) != "")  {
//		mssql_query("rollback  transaction");			
		mssql_query("commit transaction");		
		echo ("<script>alert('Operaci\u00f3n realizada con exito');</script>"); 					
	} 
	else {
		mssql_query("rollback  transaction");		
		echo ("<script>alert('Error durante la operaci\u00f3n');</script>");
	}			

	echo ("<script>window.close(); MM_openBrWindow('costosF.php?Proy=".$Proy."&sec=1-2-4&imen=1','EF','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");		

}

//info proy
$sql_nom="select * from EFNombres_Proyectos where id_proyecto=".$Proy ." and nombre_actual=1";
$cur=mssql_query($sql_nom);
$datos_pro=mssql_fetch_array($cur);

//FECHA PROY
$sql_nom="select  CONVERT(varchar, EFFechas_Proyecto.fecha_inicio_proyecto,101) fecha_inicio_proyecto, CONVERT(varchar, EFFechas_Proyecto.fecha_final_proyecto,101) fecha_final_proyecto  from EFFechas_Proyecto where id_proyecto=".$Proy ." and fechas_actuales=1";
$cur_fecha=mssql_query($sql_nom);
$datos_pro_fecha=mssql_fetch_array($cur_fecha);

$sql_nom="select  *  from EFValores_Contrato where id_proyecto=".$Proy ." and valores_actuales=1";
$cur_fecha=mssql_query($sql_nom);
$datos_provalor=mssql_fetch_array($cur_fecha);

//CONSULTA LA FECHA FINAL DE LA ULTIMA FACURACION REGISTRADA EN EL PROYECTO
$sql_max_fech_prorroga="select CONVERT(varchar,fecha_ultima_facturacion,101) fechafinal from EFValores_Facturados  where id_proyecto=".$Proy ." and id_valor_facturado=(select MAX(id_valor_facturado) from EFValores_Facturados  where id_proyecto=".$Proy .")";
$cur_max_fech_prorroga=mssql_query($sql_max_fech_prorroga);
$datos_max_fech_prorroga=mssql_fetch_array($cur_max_fech_prorroga);

//info del registro
$cur_facturado=mssql_query("select CONVERT(varchar,fecha_inicio_facturacion,101) fecha_inicio_facturacionP ,CONVERT(varchar, fecha_ultima_facturacion,101) fecha_ultima_facturacionP,* from EFValores_Facturados where id_proyecto=".$Proy." and id_valor_facturado=".$idF." order by fecha_inicio_facturacion ");
$datos_facturado=mssql_fetch_array($cur_facturado);
$Finicio=$datos_facturado["fecha_inicio_facturacionP"];
$Final=$datos_facturado["fecha_ultima_facturacionP"];
$Valor=$datos_facturado["valor_facturado"];

$dis="";
if($acc==3)
	$dis="disabled";
	
?>

<script type="text/javascript">
function valida()
{
 
 	document.getElementById("divFinicio").className="form-group";		
	document.getElementById("helpFinicioProrr").style.display="none";	
	document.getElementById("divFinal").className="form-group";	
	document.getElementById("helpFinal3").style.display="none";		
	
 
	var campos_tex = ["Finicio","Final","Valor"];		
	var error=0;	
/*	
	error=valida_campos(campos_tex,1);	
	error+=valida_campos("",5);

//alert(" -- "+document.getElementById("Finicio").value+ " *** "+document.getElementById("FechaIniProy").value) ;	
			
	if(document.getElementById("Finicio").value!="")
	{
		//SI LA FEHCA DE INICIO DE FACTURACION ES INFERIOR A LA FECHA DE INICIO DEL PROYECTO
		if (compare_fecha( document.getElementById("FechaIniProy").value , document.getElementById("Finicio").value))
		{  
	//alert("Ingresaa ");
			document.getElementById("divFinicio").className="form-group has-error";		
			document.getElementById("helpFinicioProrr").style.display="inline-block";		
			error++;				  
		}
	}
	
	if(document.getElementById("FechaUltProrroga").value!="")
	{
		document.getElementById("divFinicio").className="form-group";		
		document.getElementById("helpFinicioProrr2").style.display="none";
			
		//SI LA FEHCA DE INICIO DEL PRORROGA ES INFERIOR A LA FECHA DE FINALIZACION DE LA ULTIMA PRORROGA RGISTRADA
		if (compare_fecha( document.getElementById("FechaUltProrroga").value , document.getElementById("Finicio").value))
		{  
	//alert("Ingresaa ");
			document.getElementById("divFinicio").className="form-group has-error";		
			document.getElementById("helpFinicioProrr2").style.display="inline-block";		
			error++;				  
		}		
	}

	if( (document.getElementById("Finicio").value!="") && (document.getElementById("Final").value!="") )
	{
		
		//SI LA FEHCA DE FINALIZACION DE LA FACTURACION ES SUPERIOR A LA FECHA DE FINALIZACION DEL PROYECTO
		if (compare_fecha( document.getElementById("Final").value , document.getElementById("FechaFinaProy").value))
		{  
	//alert("Ingresaa ");
			document.getElementById("divFinal").className="form-group has-error";		
			document.getElementById("helpFinal3").style.display="inline-block";		
			error++;				  
		}		
				
		
	}
*/
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

  <div class="form-group" id="">
    <label for="">Valor del Proyecto</label>
    <div class="desabilitados">
		 $ <?=number_format ($datos_provalor["valor_contrato"], 0, ',', '.')  ?>
         <input type="hidden" value="<?=$datos_provalor["valor_contrato"] ?>" name="valorProy" id="valorProy">         
    </div>
  </div>  
    
     <div class="form-group" id="divFinicio">
    <label for="">Fecha de Inicio</label>    
	<div class="container-fluid">
      <div class="col-sm-3">
       <div class="input-group">
        <input class="form-control" id="Finicio" name="Finicio" placeholder="MM/DD/YYYY" value="<?=$Finicio ?>"  disabled  type="text"/>
    
       </div>
      </div>
    </div>   
        
     <span id="helpFinicio" class="help-block" style="display:none;" >La fecha de inicio es obligatoria.</span>

     <span id="helpFinicioProrr" class="help-block" style="display:none;" >La fecha de inicio, no puede ser inferior a la fecha de inicio del proyecto.</span>     
     
	 <span id="helpFinicioProrr2" class="help-block" style="display:none;" >La fecha de inicio, no puede ser inferior a la fecha de finalizaci&oacute;n de la ultima facturaci&oacute;n registrada (<?=$datos_max_fech_prorroga["fechafinal"] ?>).</span>          
  </div>


    <div class="form-group" id="divFinal">
    <label for="">Fecha de Finalizaci&oacute;n</label>    
	<div class="container-fluid">
      <div class="col-sm-3">
       <div class="input-group">
        <input class="form-control" id="Final" name="Final" placeholder="MM/DD/YYYY"  value="<?=$Final ?>"  disabled type="text"/>
       
       </div>
      </div>
    </div>   
        
     <span id="helpFinal" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n es obligatoria.</span>
     <span id="helpFinal2" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n es menor a la fecha de inicio, por favor verifique.</span>     
	 <span id="helpFinal3" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n, no puede ser superior a la fecha de finalizaci&oacute;n del proyecto.</span>          
    </div>       
           

  
  <div class="form-group" id="divValor">
    <label for="">Valor Facturado</label>
    <input type="text" class="form-control" id="Valor" name="Valor" value="<?=$Valor ?>"  placeholder="Valor"  <?=$dis ?> onKeyPress="return acceptNum(event)">
    <span id="helpValor" class="help-block" style="display:none;" >El valor facturado es obligatorio.</span>                            
  </div>  
<!--  
  <div class="form-group" id="divValor">
	<label for="">Valor Facturado % Participaci&oacute;n</label>
	$ <input type="text" class="form-control" name="valorParticipacionF" id="valorParticipacionF" size="25"   readonly >  
  </div>    
--->  

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