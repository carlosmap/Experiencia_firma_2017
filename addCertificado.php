<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente
banner(2,"Nuevo Certificado");


if($recarga==2)
{

	
		$datos_cert_max=mssql_fetch_array(mssql_query("select isnull(MAX(id_certificado),0)+1 no	 from EFCertificados where id_proyecto=".$Proy .""));
	
		$cursorIn1="no error";

//echo $path1." --- ";

		//SI NO EXISTE EL DIRECTORIO DEL PROYECTO
		if (!file_exists("files/".$Proy."")) 
		{
			//CREA LA CARPETA DEL PROYECTO
			if(!mkdir("files/".$Proy."",0777))
			{
				$cursorIn1="";
			}		
		}

		$path1 = "files/".$Proy."/filesRecepcion";
		//SI NO EXISTE EL DIRECTORIO DE LIQUIDACIONES EN EL PROYECTO
		if (!file_exists($path1)) 
		{
			//CREA LA CARPETA DE  LIQUIDACIONES
			if(!mkdir($path1,0777))
			{
				$cursorIn1="";
			}		
		}

		$path1 = "files/" . $Proy."/filesRecepcion/".$datos_cert_max["no"];
		
		if(is_dir($path1) == false)
		{
//echo "ingresaa<br>";			
			if(mkdir($path1,0777))
			{		
			}
			else{
		//			echo "Error: No fué posible crear el Directorio: " . $path . "<br>";
					$cursorIn1="";
			}
		}

		if($cursorIn1!="")
		{					
			$sql_inse="insert into  EFCertificados (id_proyecto, id_certificado, fecha_solicitud_certificado,fecha_recepcion_certificado, fechaGraba, usuarioGraba) values (
			".$Proy .", ".$datos_cert_max["no"].", '".$FSolicitud."'
			";
			
			if($FRecepcion!="")
				$sql_inse.=", '".$FRecepcion."'";
			else	
				$sql_inse.=", NULL";
			
			$sql_inse.=" , getdate(), " . $_SESSION["sesUnidadUsuario"] . " ) ";
			$cursorIn1=mssql_query($sql_inse);		
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

//CONSULTA LA fecha_solicitud_certificado DEL ULTIMO CERTIFICADO REGISTRADO
$sql_fecha_sol_ant="select CONVERT(varchar, fecha_recepcion_certificado,101) fecha_recepcion_certificad from EFcertificados 
where id_proyecto =".$Proy ." and id_certificado=( select MAX(id_certificado) from EFcertificados where id_proyecto =".$Proy ." ) ";
$cur_fecha_sol_ant=mssql_query($sql_fecha_sol_ant);
$datos_fecha_sol_ant=mssql_fetch_array($cur_fecha_sol_ant);



?>
<script type="text/javascript">
function valida()
{
	var campos_tex = ["FSolicitud"];		
	var error=0;
	
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

	//SI LA FEHCA DE FSolicitud ES INFERIOR A LA FECHA DE FRecepcion
	if (compare_fecha( document.getElementById("fecha_cert_ant").value , document.getElementById("FSolicitud").value))
	{  
		document.getElementById("divFSolicitud").className="form-group has-error";		
		document.getElementById("helpFSolicitudAnt").style.display="inline-block";		
		error++;
	}	
	            
            
	
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
            <input class="form-control" id="FSolicitud" name="FSolicitud" placeholder="MM/DD/YYYY" value="" onClick=""   readonly type="text"/>
            <div class="input-group-addon">
             <i class="glyphicon glyphicon-calendar">
             </i>
             <input type="hidden" name="fecha_cert_ant" id="fecha_cert_ant" value="<?=$datos_fecha_sol_ant["fecha_recepcion_certificad"] ?>">
             
            </div>        
           </div>
          </div>
        </div>   

         <span id="helpFSolicitud" class="help-block" style="display:none;" >La fecha de solicitud es obligatoria.</span>
         <span id="helpFSolicitudAnt" class="help-block" style="display:none;" >La fecha de solicitud no puede ser inferior a la fecha de recepci&oacute;n del certificado anterior (<?=$datos_fecha_sol_ant["fecha_recepcion_certificad"] ?>).</span>         
  </div>
  


    <div class="form-group" id="divFRecepcion">
    <label for="">Fecha de Recepci&oacute;n</label>    
	<div class="container-fluid">
      <div class="col-sm-3">
       <div class="input-group">
        <input class="form-control" id="FRecepcion" name="FRecepcion" placeholder="MM/DD/YYYY"  value=""  onClick=" " readonly type="text"/>
        <div class="input-group-addon">
         <i class="glyphicon glyphicon-calendar">
         </i>
        </div>        
       </div>
      </div>
    </div>   
        
     <span id="helpFRecepcion" class="help-block" style="display:none;" >La fecha de recepci&oacute;n es obligatoria.</span>
     <span id="helpFRecepcion2" class="help-block" style="display:none;" >La fecha de recepci&oacute;n no puede ser inferior a la fecha de solicitud.</span>
<!--     <span id="helpFinal2" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n del proyecto es menor a la fecha de inicio, por favor verifique.</span>     
    -->      
  </div>
  
     
   <div style="text-align:right" >
      <button type="button" class="btn btn-primary" onClick="valida()" >Grabar</button>  
       <input name="recarga" type="hidden" id="recarga" value="1">
   </div>
   </div>
</form>
    
<?php
	include("inferior.php"); 
?>