<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente
banner(2,"Ubicaci&oacute;n");


if($recarga==2)
{

	mssql_query("begin transaction");	
	$cursorIn1="empieza";		
	//ALMACENA LOS ORDENADORES
	for($i=1; $i<=$cantOrd;$i++)
	{
		$ord="Ordenador".$i;
//echo $ord." **** ".$$ord;		
		if($$ord!="")
		{
			if($cursorIn1!="")
			{					
				//ALMACENA EL DIRECTOR
				$sqlIn1 = " INSERT INTO EFSoportes";
				$sqlIn1 = $sqlIn1 . "( id_soporte,id_proyecto,id_tipo_soporte, usuarioGraba, fechaGraba ) ";
				$sqlIn1 = $sqlIn1 . " values ( (select isnull(MAX(id_soporte),0)+1 id   from EFSoportes WHERE id_proyecto=".$Proy." ), ".$Proy.", ".$$ord.", ".$_SESSION["sesUnidadUsuario"].",getdate()  ) ";
				$cursorIn1 = mssql_query($sqlIn1);											
//echo $sqlIn1."<br>".mssql_get_last_message()."<br>";									
			}
		}
	}

	if  (trim($cursorIn1) != "")  {
		mssql_query("commit transaction");		
	//	mssql_query("rollback  transaction");			
		echo ("<script>alert('Operaci\u00f3n realizada con exito');</script>"); 					
	} 
	else {
		mssql_query("rollback  transaction");		
		echo ("<script>alert('Error durante la operaci\u00f3n');</script>");
	}			

	echo ("<script>window.close(); MM_openBrWindow('infoG.php?Proy=".$Proy."&sec=1-1-4&imen=1','EF','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");		

}

//info proy
$sql_nom="select * from EFNombres_Proyectos where id_proyecto=".$Proy ." and nombre_actual=1";
$cur=mssql_query($sql_nom);
$datos_pro=mssql_fetch_array($cur);

//INFO soportes
$sql_soporte="select * from EFTipos_soporte where estado=1 order by tipo_soporte";
$cur_soporte=mssql_query($sql_soporte);

?>

<script>
	function addFila()
	{ 	
		var opciones= new Array();
		var cantOr=parseInt(document.getElementById("cantOrd").value);
		cantOr=cantOr+1;		
<?PHP
		$cur_soporte=mssql_query($sql_soporte);
?>			
		opciones[0]='<option value="">Seleccione el Soporte</option>';
<?php			
		$z=1;
		//se carga la lISTA CON LAS PERSONAS ACTIVAS EN EL PORTAL
		while($datos_usu=mssql_fetch_array($cur_soporte))
		{
?>             
			opciones[<?=$z ?>]='<option value="<?=$datos_usu["id_tipo_soporte"] ?>"><?=$datos_usu["tipo_soporte"]  ?></option>';
<?php
			$z++;
				}
			?>	
		   var sel='', divI='', divF='', total='';
		   divI='<div class="main row form-group" id="divOrdenador'+cantOr+'" name="divOrdenador'+cantOr+'"> <div class="col-sm-11 ">';

		   divF+='</div><div class="col-sm-1 "><i class="glyphicon glyphicon-minus-sign btn btn-danger" onClick="delFila('+cantOr+')"></i></div></div>    ';
		   sel+='<select name="Ordenador'+cantOr+'" id="Ordenador'+cantOr+'" class="form-control">';
		   for(var u=0;u<opciones.length;u++ )		
		   {
			   sel+=opciones[u];
		   }
		   sel+='</select>';
		   total=divI+sel+divF+"";
		  document.getElementById("divOrdenadorT").innerHTML+=total;
		  document.getElementById("cantOrd").value=cantOr;
	}
	
	function delFila(fila)
	{
		 document.getElementById("divOrdenador"+fila).remove();		 		
	}
	
	function valida()
	{
		var error=0;					
		var ban=0;
		for(var i=0; i< document.getElementById("cantOrd").value; i++)
		{
			if(document.getElementById("Ordenador"+(i+1)))
			{
				if(document.getElementById("Ordenador"+(i+1)).value=="")
				{									
					error++;
				}						
			}
			else
				ban++;
		}
		
		if ( (error==0) && (ban!=document.getElementById("cantOrd").value) )
		{
			document.formulario.recarga.value="2";
			document.formulario.submit();
		}
		else
		{
			document.getElementById("divSoporte" ).className="form-group has-error";		
			document.getElementById("helpSoporte").style.display="inline-block";			
		}
		
	}
</script>

<form id="formulario" name="formulario" method="post" enctype="multipart/form-data">
  
    <div class="container-fluid">
    
        $cur_clie=mssql_query("SELECT * from EFPaises where estado=1 order by pais ");      
        $cur_clie=mssql_query("SELECT * from EFDepartamentos where estado=1 and id_pais =".$Pais." order by  departamento ");        
        $cur_clie=mssql_query("SELECT * from EFMunicipios where estado=1 and id_pais =".$Pais." and id_departamento=".$Depto."  order by  municipio ");        
      <div class="form-group" id="">
        <label for="">Proyecto</label>
        <div class="desabilitados">
             <?=$datos_pro["nombre_largo_proyecto"] ?>
        </div>
      </div>
      
      <div class="form-group" id="">
	        <table  class="table table-bordered">
            	<tr>
            		<th>Pa&iacute;s</th>
            		<th>Departamento</th>
            		<th>Municipio</th>
            	</tr>
	        	<tr>
	        		<td>
                    <select class="form-control" id="Pais"  name="Pais" onChange="document.formulario.submit();  ">
	        		  <option selected value="">Seleccione el Pa&iacute;s</option>
	        		  <?php

        while($datos_cli=mssql_fetch_array($cur_clie))
        {
				$sel="";
				if($Pais== $datos_cli["id_pais"])
					$sel="selected";			
    ?>
	        		  <option value="<?=$datos_cli["id_pais"]; ?>" <?=$sel ?> >
	        		    <?=$datos_cli["pais"]; ?>
        		      </option>
	        		  <?php
        }
    ?>
        		    </select></td>
	        		<td>
                    <select class="form-control" id="Depto" name="Depto" onChange="document.formulario.submit()">
	        		  <option selected value="">Seleccione el Departamento</option>
	        		  <?php

        while($datos_cli=mssql_fetch_array($cur_clie))
        {
			$sel="";
			if($Depto== $datos_cli["id_departamento"])
				$sel="selected";		
    ?>
	        		  <option value="<?=$datos_cli["id_departamento"]; ?>" <?=$sel ?> >
	        		    <?=$datos_cli["departamento"]; ?>
        		      </option>
	        		  <?php
        }
    ?>
        		    </select>
                    </td>
	        		<td><select class="form-control" id="Municipio" name="Municipio">
	        		  <option selected value="">Seleccione el Municipio</option>
	        		  <?php

		
        while($datos_cli=mssql_fetch_array($cur_clie))
        {
			$sel="";
			if($Municipio== $datos_cli["id_municipio"])
				$sel="selected";		
    ?>
	        		  <option value="<?=$datos_cli["id_municipio"]; ?>" <?=$sel ?> >
	        		    <?=$datos_cli["municipio"]; ?>
        		      </option>
	        		  <?php
        }
    ?>
        		    </select></td>
	        	</tr>
	        	<tr>
	        		<td><select name="" id="" class="form-control"></select></td>
	        		<td><select name="" id="" class="form-control"></select></td>
	        		<td><select name="" id="" class="form-control"></select></td>
	        	</tr>
	        	<tr>
	        		<td><select name="" id="" class="form-control"></select></td>
	        		<td><select name="" id="" class="form-control"></select></td>
	        		<td><select name="" id="" class="form-control"></select></td>
	        	</tr>
	        </table>
       </div>       

         <div id="divSoporte" class="form-group" x>
			<span id="helpSoporte" class="help-block" style="display:none;" >Por favor seleccione el tipo de soporte.</span>         
		</div>        
            
        <div class="form-group" style="text-align:right;">
            <i class="glyphicon glyphicon-plus-sign btn btn-success" onClick="addFila();" title="Agregar Soporte"></i>
            
        </div>        
        
       <div style="text-align:right" >
          <button type="button" class="btn btn-primary" onClick="valida()" >Grabar</button>  
           <input name="recarga" type="hidden" id="recarga" value="1">
           <input type="hidden" id="cantOrd" name="cantOrd" value="0">
       </div>
     </div>
</form>     

<?php
	include("inferior.php"); 
?>