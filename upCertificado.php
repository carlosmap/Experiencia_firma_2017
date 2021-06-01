<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente
banner(2,"Actualizar Certificado");


if($recarga==2)
{
	if($acc==2)
	{
			
		if($FRecepcion=="")
			$FRecepcion="NULL";
		else
			$FRecepcion=" '".$FRecepcion."' ";			

		$sql_inse="update EFCertificados set fecha_solicitud_certificado='".$FSolicitud."', fecha_recepcion_certificado=".$FRecepcion." where id_proyecto=".$Proy ." and id_certificado=".$no;	
		$cursorIn1=mssql_query($sql_inse);				
	}

	if($acc==3)	
	{
		$sql_inse="delete from EFCertificados where id_proyecto=".$Proy ." and id_certificado=".$no;
		$cursorIn1=mssql_query($sql_inse);				
		if($cursorIn1!="")	
		{	
			//ELIMINA LA CARPETA DONDE SE ALMACENAN LOS DOCUMENTOS DE RECEPCION
			if((rmdir('files/'.$Proy."/filesRecepcion/".$no)))
			{															

			}
			else
				$cursorIn1="";
		}
	}
	


//echo $sql_inse." <br>".mssql_get_last_message();		
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

$sql_certi="select  CONVERT(varchar, fecha_solicitud_certificado,101) fecha_solicitud_certificado,  CONVERT(varchar, fecha_recepcion_certificado,101) fecha_recepcion_certificado from EFcertificados where id_proyecto=".$Proy." and id_certificado=".$no;					
$cur_certi= mssql_query($sql_certi);		
$datos_certi=mssql_fetch_array($cur_certi);

?>
<script type="text/javascript">
function valida()
{
	var campos_tex = ["FSolicitud"];		
	var error=0;

<?
	if($acc==2)
	{
?>	
		error=valida_campos(campos_tex,1);
	
		if(document.getElementById("FRecepcion").value!="")	
		{
			//SI LA FEHCA DE FSolicitud ES INFERIOR A LA FECHA DE FRecepcion
			if (compare_fecha( document.getElementById("FSolicitud").value , document.getElementById("FRecepcion").value))
			{  
				document.getElementById("divFRecepcion").className="form-group has-error";		
				document.getElementById("helpFRecepcion2").style.display="inline-block";		
				error++;
			}
		}
<?
	}
?>	
	
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
<?php
			if($acc==2)
			{
?>           
            <input class="form-control" id="FSolicitud" name="FSolicitud" placeholder="MM/DD/YYYY" value="<?=$datos_certi["fecha_solicitud_certificado"] ?>" onClick=""   readonly type="text"/>
            <div class="input-group-addon">
             <i class="glyphicon glyphicon-calendar">
             </i>
            </div>             
<?php
			}
			else
			{
?>           
			 <div class="desabilitados">
                <?=$datos_certi["fecha_solicitud_certificado"]  ?>&nbsp; 
                </div> 
<?php
			}
?>                       
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
<?php
			if($acc==2)
			{
?>        
        <input class="form-control" id="FRecepcion" name="FRecepcion" placeholder="MM/DD/YYYY"  value="<?=$datos_certi["fecha_recepcion_certificado"] ?>"  onClick=" " readonly type="text"/>
        <div class="input-group-addon">
         <i class="glyphicon glyphicon-calendar">
         </i>
        </div>        
<?php
			}
			else
			{
?>         
			 <div class="desabilitados">
<?php
				if($datos_certi["fecha_recepcion_certificado"]!="")
	                echo $datos_certi["fecha_recepcion_certificado"]  ;
				else
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"	;
				?>&nbsp; 
                </div> 
<?php
			}
?>    
 
       </div>
      </div>
    </div>   
        
     <span id="helpFRecepcion" class="help-block" style="display:none;" >La fecha de recepci&oacute;n es obligatoria.</span>
     <span id="helpFRecepcion2" class="help-block" style="display:none;" >La fecha de recepci&oacute;n no puede ser inferior a la fecha de solicitud.</span>     
<!--     <span id="helpFinal2" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n del proyecto es menor a la fecha de inicio, por favor verifique.</span>     
    -->      
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