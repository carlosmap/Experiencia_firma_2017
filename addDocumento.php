<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente
banner(2,"Nuevo Archivo de Recepci&oacute;n");


if($recarga==2)
{
	
	$path="files/".$Proy."/filesRecepcion/".$no;
	//remplazaCaracteresEsp= funcion para remplzar los caracteres especiales del nombre del archivo
	$Archivo_name=remplazaCaracteresEsp($Archivo_name);	
	
	$sql_doc="select * from EFDocumentos_Recepcion_Certificado where id_proyecto=".$Proy." and nombre_doc='".$Archivo_name."'";
	$cur_doc=mssql_query($sql_doc);
	if(mssql_num_rows($cur_doc)==0)
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
		
		// path= RUTA EN EL SERVIDROR
		// archivo_name = NOMBRE DEL ARCHIVO
		// archivo= ARCHIVO COMO TAL	
		if(subirArchivo($path,$Archivo_name,$Archivo))
		{
			$path.="/".$Archivo_name;
	//		echo "Subiooooo";
			$sqlIn1 = " INSERT INTO EFDocumentos_Recepcion_Certificado";
			$sqlIn1 = $sqlIn1 . "( 
			id_documento_recepcion_certificado, id_proyecto, id_certificado, nombre_doc, url_doc, usuarioGraba, fechaGraba) values ( (select isnull(MAX(id_documento_recepcion_certificado),0)+1 id_doc  from EFDocumentos_Recepcion_Certificado WHERE id_proyecto=".$Proy."), ".$Proy.", ".$no." , '".$Archivo_name."', '".$path."'," . $_SESSION["sesUnidadUsuario"] . ",getdate())  ";
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
	else
	{
				echo ("<script>alert('Ya existe un archivo en ese nombre, por favor adjunte uno diferente.');</script>");
	}
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

	var campos_tex = ["Archivo"];	
	error=valida_campos(campos_tex,1);

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
    	<div class="main row" >
        	<div class="form-group ">
              <div class="col-sm-6 table-bordered" >
                <b>Fecha de Recepci&oacute;n</b>
               </div>
               <div class="col-sm-6 table-bordered"  >
                <span>
                    <?=$datos_certi["fecha_recepcion_certificado"] ?>
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
        <label for="">No</label>
        <div class="desabilitados">
              <?=$datos_certi["id_certificado"] ?>
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
                   <input type="file" id="Archivo" name="Archivo" >
                </span>
               </div>
				<span id="helpArchivo" class="help-block" style="display:none;" >Por favor adjunte el archivo correspondiente.</span>                       
				<span id="helpArchivoExt" class="help-block" style="display:none;" >Adjunte un archivo con extenci&oacute;n .pdf o .doc.</span>                                       
			</div>        
        </div> 
-->                    
        
    <div class="form-group" id="divArchivo">
     <label for="">Archivo (.PDF &oacute; .Doc)</label>
       <input type="file" id="Archivo" name="Archivo" >
        <span id="helpArchivo" class="help-block" style="display:none;" >Por favor adjunte el archivo correspondiente. </span>                       
        <span id="helpArchivoExt" class="help-block" style="display:none;" >Adjunte un archivo con extenci&oacute;n .pdf o .doc.</span> 
  </div>        
	         
<!--

  <div class="form-group" id="divArchivo">
    <label for="">Archivo</label>
    <input type="file" class="" id="Archivo" name="Archivo" >
     <span id="helpArchivo" class="help-block" style="display:none;" >Por favor adjunte el archivo correspondiente.</span>
    
  </div>         
  -->   <br>             
       <div style="text-align:right" >
          <button type="button" class="btn btn-primary" onClick="valida()" >Grabar</button>  
           <input name="recarga" type="hidden" id="recarga" value="1">
       </div>    
    </div>
</form>
    
<?php
	include("inferior.php"); 
?>