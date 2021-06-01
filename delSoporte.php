<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente
banner(2,"Eliminar Soporte");


if($recarga==2)
{
 				
		//ALMACENA EL DIRECTOR
		$sqlIn1 = " delete from  EFSoportes WHERE id_proyecto=".$Proy." and id_soporte=".$sop;
		$cursorIn1 = mssql_query($sqlIn1);											
//echo $sqlIn1."<br>".mssql_get_last_message()."<br>";									
 

	if  (trim($cursorIn1) != "")  {
//		mssql_query("commit transaction");		
	//	mssql_query("rollback  transaction");			
		echo ("<script>alert('Operaci\u00f3n realizada con exito');</script>"); 					
	} 
	else {
//		mssql_query("rollback  transaction");		
		echo ("<script>alert('Error durante la operaci\u00f3n');</script>");
	}			

	echo ("<script>window.close(); MM_openBrWindow('infoG.php?Proy=".$Proy."&sec=1-1-4&imen=1','EF','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");		

}

//info proy
$sql_nom="select * from EFNombres_Proyectos where id_proyecto=".$Proy ." and nombre_actual=1";
$cur=mssql_query($sql_nom);
$datos_pro=mssql_fetch_array($cur);

//INFO soporte
$sql_soporte="select EFTipos_soporte.tipo_soporte, EFTipos_soporte.id_tipo_soporte from EFSoportes
				inner join EFTipos_soporte on EFSoportes.id_tipo_soporte=EFTipos_soporte.id_tipo_soporte
				where EFSoportes.id_proyecto=".$Proy." and EFSoportes.id_soporte=".$sop."  ";
$cur_soporte= mssql_query($sql_soporte);	
$datos_soporte=mssql_fetch_array($cur_soporte);
//echo $sql_soporte;
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
	        <label for="">Soporte</label>
      </div>       
        <div class="desabilitados">
			<?=$datos_soporte["tipo_soporte"] ?>
        </div>
        <br>
       <div style="text-align:right" >
          <button type="button" class="btn btn-primary" onClick="valida()" >Eliminar</button>  
           <input name="recarga" type="hidden" id="recarga" value="1">
           <input type="hidden" id="cantOrd" name="cantOrd" value="0">
       </div>
     </div>
</form>     

<?php
	include("inferior.php"); 
?>