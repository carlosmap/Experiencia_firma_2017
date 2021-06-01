<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente
banner(2,"Actualizar Certificado");


if($recarga==2)
{
	if($acc==2)
	{
		if($FSolicitud=="")
			$FSolicitud="NULL";
		else
			$FSolicitud=" '".$FSolicitud."' ";
			
		if($FRecepcion=="")
			$FRecepcion="NULL";
		else
			$FRecepcion=" '".$FRecepcion."' ";			
			
		if($FFacturacion=="")
			$FFacturacion="NULL";
		else
			$FFacturacion=" '".$FFacturacion."' ";			
	}

	if($acc==3)	
	{
		$FSolicitud="NULL";
		$FRecepcion="NULL";
		$FFacturacion="NULL";		
	}
	
		$sql_inse="update EFProyectos set fecha_solicitud_certificado=".$FSolicitud.", fecha_recepcion_certificado=".$FRecepcion.", fecha_facturacion_certificado=".$FFacturacion." where id_proyecto=".$Proy ." ";
		$cursorIn1=mssql_query($sql_inse);		
//echo $sql_inse;		
		if  (trim($cursorIn1) != "")  {

		//	mssql_query("rollback  transaction");			
			echo ("<script>alert('Operaci\u00f3n realizada con exito');</script>"); 					
		} 
		else {
			echo ("<script>alert('Error durante la operaci\u00f3n');</script>");
		}			

	echo ("<script>window.close(); MM_openBrWindow('infoG.php?Proy=".$Proy."&sec=1-1-3&imen=1','EF','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");				
}

//info proy
$sql_nom="select * from EFNombres_Proyectos where id_proyecto=".$Proy ." and nombre_actual=1";
$cur=mssql_query($sql_nom);
$datos_pro=mssql_fetch_array($cur);

$sql_certi="select  CONVERT(varchar, fecha_solicitud_certificado,101) fecha_solicitud_certificado,  CONVERT(varchar, fecha_recepcion_certificado,101) fecha_recepcion_certificado,  CONVERT(varchar, fecha_facturacion_certificado,101) fecha_facturacion_certificado from EFProyectos where id_proyecto=".$Proy." ";					
$cur_certi= mssql_query($sql_certi);		
$datos_certi=mssql_fetch_array($cur_certi);

?>
<script type="text/javascript">
function valida()
{
	var campos_tex = ["FSolicitud","FRecepcion","FFacturacion"];		
	var error=0;
	
	error=valida_campos(campos_tex,1);

	if(error==0)
	{
		document.formulario.recarga.value="2";
		document.formulario.submit();
	}
	
	//error+=valida_campos("",5);	
}
</script>

<form id="formulario" name="formulario" method="post">
  
    <div class="container-fluid">
<!--    
    	<div class="main row" >
        	<div class="form-group ">
              <div class="col-sm-6 table-bordered" >
                <b>Proyecto (Nombre largo)</b>
               </div>
               <div class="col-sm-6 table-bordered"  >
                <span>
                    <?=$datos_pro["nombre_largo_proyecto"] ?>
                </span>
               </div>
			</div>               
        </div>  
-->    
      <div class="form-group" id="">
        <label for="">Proyecto</label>
        <div class="desabilitados">
             <?=$datos_pro["nombre_largo_proyecto"] ?>
        </div>
      </div>    

    <div class="form-group" id="divFSolicitud">
        <label for="">Fecha de Solicitud</label>    
        <div class="container-fluid">
          <div class="col-sm-3">
           <div class="input-group">
            <input class="form-control" id="FSolicitud" name="FSolicitud" placeholder="MM/DD/YYYY" value="<?=$datos_certi["fecha_solicitud_certificado"] ?>" onClick=""   readonly type="text"/>
            <div class="input-group-addon">
             <i class="glyphicon glyphicon-calendar">
             </i>
            </div>        
           </div>
          </div>
        </div>   
            
         <span id="helpFSolicitud" class="help-block" style="display:none;" >La fecha de solicitud es obligatoria.</span>
  </div>
  


    <div class="form-group" id="divFRecepcion">
    <label for="">Fecha de Recepci&oacute;n</label>    
	<div class="container-fluid">
      <div class="col-sm-3">
       <div class="input-group">
        <input class="form-control" id="FRecepcion" name="FRecepcion" placeholder="MM/DD/YYYY"  value="<?=$datos_certi["fecha_recepcion_certificado"] ?>"  onClick=" " readonly type="text"/>
        <div class="input-group-addon">
         <i class="glyphicon glyphicon-calendar">
         </i>
        </div>        
       </div>
      </div>
    </div>   
        
     <span id="helpFRecepcion" class="help-block" style="display:none;" >La fecha de recepci&oacute;n es obligatoria.</span>
<!--     <span id="helpFinal2" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n del proyecto es menor a la fecha de inicio, por favor verifique.</span>     
    -->      
  </div>
  
    <div class="form-group" id="divFFacturacion">
        <label for="">Fecha de Facturaci&oacute;n</label>    
        <div class="container-fluid">
          <div class="col-sm-3">
           <div class="input-group">
            <input class="form-control" id="FFacturacion" name="FFacturacion" placeholder="MM/DD/YYYY" value="<?=$datos_certi["fecha_facturacion_certificado"] ?>" onClick=" "   readonly type="text"/>
            <div class="input-group-addon">
             <i class="glyphicon glyphicon-calendar">
             </i>
            </div>        
           </div>
          </div>
        </div>   
            
         <span id="helpFFacturacion" class="help-block" style="display:none;" >La fecha de facturaci&oacute;n es obligatoria.</span>
  </div>

   <?php
   		$mens_b="";
	   if($acc==2)
	   		$mens_b="Actualizar";	   		
	   if($acc==3)
	   		$mens_b="Eliminar";	   
	   
   ?>
  
   <div style="text-align:right" >
      <button type="button" class="btn btn-primary" onClick="valida()" ><?=$mens_b ?></button>  
       <input name="recarga" type="hidden" id="recarga" value="1">
   </div>
   </div>
</form>
    
<?php
	include("inferior.php"); 
?>