<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente
banner(2,"Eliminar Archivo de Recepci&oacute;n");

$sql_doc="select * from EFDocumentos_Recepcion_Certificado where id_proyecto=".$Proy." and id_documento_recepcion_certificado=".$doc."  and id_certificado=".$no;;
$cur_doc= mssql_query($sql_doc);		
$datos_doc=mssql_fetch_array($cur_doc);
$path=$datos_doc["url_doc"];

if($recarga==2)
{
	
		if(del_arch($path))
		{
			$sqlIn1 = " DELETE FROM EFDocumentos_Recepcion_Certificado WHERE id_documento_recepcion_certificado=".$doc." AND id_proyecto=".$Proy."  and id_certificado=".$no;
			$cursorIn1 = mssql_query($sqlIn1);			
		}
		
		if  (trim($cursorIn1) != "")  
		{
			echo ("<script>alert('Operaci\u00f3n realizada con exito');</script>"); 					
		} 
		else 
		{
			echo ("<script>alert('Error durante la operaci\u00f3n');</script>");
		}			
	
		echo ("<script>window.close(); MM_openBrWindow('infoG.php?Proy=".$Proy."&sec=1-1-3&imen=1','EF','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");					
}

//info proy
$sql_nom="select * from EFNombres_Proyectos where id_proyecto=".$Proy ." and nombre_actual=1";
$cur=mssql_query($sql_nom);
$datos_pro=mssql_fetch_array($cur);

$sql_certi="select * from EFCertificados where id_proyecto=".$Proy." and id_certificado=".$no;
$cur_certi= mssql_query($sql_certi);		
$datos_certi=mssql_fetch_array($cur_certi);


?>


<script type="text/javascript">
function valida()
{
 	var error=0;
	//SI NO SE PRESENTARON PROBLEMAS DE VALIDACION
	if(error==0)
	{
		document.formulario.recarga.value="2";
		document.formulario.submit();
	}
	
}
</script>


<form id="formulario" name="formulario" method="post" enctype="multipart/form-data">
  
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

      <div class="form-group" id="">
        <label for="">Fecha de Solicitud</label>
        <div class="desabilitados">
              <?=$datos_certi["fecha_solicitud_certificado"] ?>
        </div>
      </div>      
      
      <div class="form-group" id="">
        <label for="">Fecha de Recepci&oacute;n</label>
        <div class="desabilitados">
              <?=$datos_certi["fecha_recepcion_certificado"] ?>
        </div>
      </div> 
<!--  
    	<div class="main row" >
        	<div class="form-group " id="divArchivo">
              <div class="col-sm-6 table-bordered" >
                <label>
                	Archivo
                 </label>   
               </div>
               <div class="col-sm-6 table-bordered"  >
                <span>
                   <?=$datos_doc["nombre_doc"]; ?>
                </span>
               </div>
				<span id="helpArchivo" class="help-block" style="display:none;" >Por favor adjunte el archivo correspondiente.</span>                       
				<span id="helpArchivoExt" class="help-block" style="display:none;" >Adjunte un archivo con extenci&oacute;n .pdf o .doc.</span>                                       
			</div>        

        </div>    
-->        
    <div class="form-group" id="divArchivo">
     <label for="">Archivo</label>
      <label for="" class="desabilitados" ><?=$datos_doc["nombre_doc"]; ?></label>
  	</div> 
                   
	         
<!--

  <div class="form-group" id="divArchivo">
    <label for="">Archivo</label>
    <input type="file" class="" id="Archivo" name="Archivo" >
     <span id="helpArchivo" class="help-block" style="display:none;" >Por favor adjunte el archivo correspondiente.</span>
    
  </div>         
  -->   <br>             
       <div style="text-align:right" >
          <button type="button" class="btn btn-primary" onClick="valida()" >Eliminar</button>  
           <input name="recarga" type="hidden" id="recarga" value="1">
       </div>    
    </div>
</form>
    
<?php
	include("inferior.php"); 
?>