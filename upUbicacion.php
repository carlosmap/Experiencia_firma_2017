<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente

if($acc==2)
	$mens_v="Actualizar Ubicaci&oacute;n";	   		
if($acc==3)
	$mens_v="Eliminar Ubicaci&oacute;n";	 
banner(2,$mens_v);



if($recarga==2)
{
	mssql_query("begin transaction");	
	
	$sqlIn1 = " delete from EFUbicacion_Proyectos where EFUbicacion_Proyectos.id_proyecto=".$Proy;
	$cursorIn1 = mssql_query($sqlIn1);	


	if  (trim($cursorIn1) != "")  {
		mssql_query("commit transaction");		
//		mssql_query("rollback  transaction");			
		echo ("<script>alert('Operaci\u00f3n realizada con exito');</script>"); 					
	} 
	else {
		mssql_query("rollback  transaction");		
		echo ("<script>alert('Error durante la operaci\u00f3n');</script>");
	}			

	echo ("<script>window.close(); MM_openBrWindow('infoG.php?Proy=".$Proy."&sec=1-1-5&imen=1','EF','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");		

}

//info proy
$sql_nom="select * from EFNombres_Proyectos where id_proyecto=".$Proy ." and nombre_actual=1";
$cur=mssql_query($sql_nom);
$datos_pro=mssql_fetch_array($cur);
?>

<script>
	function valida()
	{ 	
			document.formulario.recarga.value="2";
			document.formulario.submit();	
	}
</script>

<form id="formulario" name="formulario" method="post" enctype="multipart/form-data">
  
    <div class="container-fluid">
    
         
      <div class="form-group" id="">
        <label for="">Proyecto</label>
        <div class="desabilitados">
             <?=$datos_pro["nombre_largo_proyecto"] ?>
        </div>
      </div>
      
      <div class="form-group" id="">
	        <label for="">Ubicaci&oacute;n</label>
       </div>       

         <div class="form-group" id="">
                    <table  class="table table-bordered" id="divUbicacionT">
                        <tr>
                            <th>Pa&iacute;s</th>
                            <th>Departamento</th>
                            <th>Municipio</th>
                        </tr>
<?php
	$cur_ubica=mssql_query("select  EFPaises.pais , EFDepartamentos.departamento, EFMunicipios.municipio from 	EFUbicacion_Proyectos 

	inner join EFPaises on  EFPaises.id_pais=EFUbicacion_Proyectos.id_pais 
	inner join EFDepartamentos on  EFDepartamentos.id_pais=EFUbicacion_Proyectos.id_pais and EFDepartamentos.id_departamento=EFUbicacion_Proyectos.id_departamento
	inner join EFMunicipios on  EFMunicipios.id_pais=EFUbicacion_Proyectos.id_pais and EFMunicipios.id_departamento=EFUbicacion_Proyectos.id_departamento
	 and EFMunicipios.id_municipio=EFUbicacion_Proyectos.id_municipio
	where EFUbicacion_Proyectos.id_proyecto=".$Proy);
	while($datos=mssql_fetch_array($cur_ubica))
	{
?>                        
                        <tr>
                        	<td><?=$datos["pais"] ?></td>
                        	<td><?=$datos["departamento"] ?></td>
                        	<td><?=$datos["municipio"] ?></td>
                        </tr>
<?php
	}
?>                        
                        </table>
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