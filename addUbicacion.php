<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente
banner(2,"Ubicaci&oacute;n");


if($recarga==2)
{

	mssql_query("begin transaction");	
	$cursorIn1="empieza";		
	//ALMACENA LOS ORDENADORES
	for($i=1; $i<=$cantUbica;$i++)
	{
		$Pais="Pais".$i;
		$Departamento="Departamento".$i;
		$Municipio="Municipio".$i;				
//echo $ord." **** ".$$ord;		
		if( ($$Pais!="")&&($$Departamento!="")&&($$Municipio!="") )
		{
			if($cursorIn1!="")
			{					
				//ALMACENA EL DIRECTOR
				$sqlIn1 = " INSERT INTO EFUbicacion_Proyectos";
				$sqlIn1 = $sqlIn1 . "( id_proyecto,id_pais,id_departamento,id_municipio,usuarioGraba,fechaGraba ) ";
				$sqlIn1 = $sqlIn1 . " values ( ".$Proy.", '".$$Pais."', '".$$Departamento."', '".$$Municipio."', ".$_SESSION["sesUnidadUsuario"].",getdate()  ) ";
				$cursorIn1 = mssql_query($sqlIn1);											
//echo $sqlIn1."<br>".mssql_get_last_message()."<br>";									
			}
		}
	}

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

//INFO soportes
$sql_soporte="select * from EFTipos_soporte where estado=1 order by tipo_soporte";
$cur_soporte=mssql_query($sql_soporte);

$cur_paises=mssql_query("SELECT * from EFPaises where estado=1 order by pais ");      

/*



*/

?>

<script>
	function addFila()
	{ 	
		var opciones= new Array();
		var cantUb=parseInt(document.getElementById("cantUbica").value);
		cantUb=cantUb+1;		

		opciones[0]='<option value="">Seleccione el Pais</option>';
<?php			
		$z=1;

		while($datos_paises=mssql_fetch_array($cur_paises))
		{
?>             
			opciones[<?=$z ?>]='<option value="<?=$datos_paises["id_pais"] ?>"><?=($datos_paises["pais"])  ?></option>';
<?php
			$z++;
		}
			?>	
		   var sel='', divI='', divF='', total='';
		   /*
		   divI='<div class="main row form-group" id="divUbicacion'+cantUb+'" name="divUbicacion'+cantUb+'"> <div class="col-sm-11 ">';

		   divF+='</div><div class="col-sm-1 "><i class="glyphicon glyphicon-minus-sign btn btn-danger" onClick="delFila('+cantUb+')"></i></div></div>    ';
		   */
		   divI='<tr id="divUbicacion'+cantUb+'" name="divUbicacion'+cantUb+'"><td>';		   
		   divF+='</td> <td><select name="Departamento'+cantUb+'" id="Departamento'+cantUb+'" class="form-control" disabled onChange="depto_municipio('+cantUb+',2)" > </select> </td> <td><select name="Municipio'+cantUb+'" id="Municipio'+cantUb+'" disabled class="form-control" ></select> </td> <td><i class="glyphicon glyphicon-minus-sign btn btn-danger" onClick="delFila('+cantUb+')"></i></td></tr>';
		   
		   sel+='<select name="Pais'+cantUb+'" id="Pais'+cantUb+'" class="form-control"  onChange="depto_municipio('+cantUb+',1)">';
		   for(var u=0;u<opciones.length;u++ )		
		   {
			   sel+=opciones[u];
		   }
		   sel+='</select>';
		   total=divI+sel+divF+"";
		  document.getElementById("divUbicacionT").innerHTML+=total;
		  document.getElementById("cantUbica").value=cantUb;
	}
	
	function delFila(fila)
	{
		 document.getElementById("divUbicacion"+fila).remove();		 		
	}
	
	function valida()
	{
		var error=0;					
		var ban=0;
		for(var i=0; i< document.getElementById("cantUbica").value; i++)
		{
			if(document.getElementById("Pais"+(i+1)))
			{
				if(document.getElementById("Pais"+(i+1)).value=="")
				{									
					error++;
				}
				if(document.getElementById("Departamento"+(i+1)).value=="")
				{									
					error++;
				}					
				if(document.getElementById("Municipio"+(i+1)).value=="")
				{									
					error++;
				}					
			}
			else
				ban++;
		}
		
		if ( (error==0) && (ban!=document.getElementById("cantUbica").value) )
		{
			document.formulario.recarga.value="2";
			document.formulario.submit();
		}
		else
		{
			document.getElementById("divUbicacion" ).className="form-group has-error";		
			document.getElementById("helpUbicacion").style.display="inline-block";			
		}
		
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
                        </table>
        </div>
<!--
		 <div class="form-group" id="divUbicacionT">          
         </div>      
-->         
      
         <div id="divUbicacion" class="form-group" x>
			<span id="helpUbicacion" class="help-block" style="display:none;" >Por favor seleccione el Pais, Departamento y Municipio, de cada registro.</span>         
		</div>        
            
        <div class="form-group" style="text-align:right;">
            <i class="glyphicon glyphicon-plus-sign btn btn-success" onClick="addFila();" title="Agregar Soporte"></i>
            
        </div>        
        
       <div style="text-align:right" >
          <button type="button" class="btn btn-primary" onClick="valida()" >Grabar</button>  
           <input name="recarga" type="hidden" id="recarga" value="1">
           <input type="hidden" id="cantUbica" name="cantUbica" value="0">
       </div>
     </div>
</form>     
<script type = "text/javascript">
function depto_municipio(valor, tip)
{
		var i=valor;
	if(tip==1)		
	{
		if($('#Pais'+i).val() == ""){
			
			$('#Departamento'+i).empty();
			$('<option value = "">Seleccione un Departamento</option>').appendTo('#Departamento'+i);
			$('#Departamento'+i).attr('disabled', 'disabled');
			
			$('#Municipio'+i).empty();
			$('<option value = "">Seleccione un Municipio</option>').appendTo('#Municipio'+i);
			$('#Municipio'+i).attr('disabled', 'disabled');			
			
		}else{
			$('#Departamento'+i).removeAttr('disabled', 'disabled');
			$('#Departamento'+i).load('depto_municipio.php?tipo=1&id_pais=' + $('#Pais'+i).val());
		}			
	}
	if(tip==2)
	{
		if($('#Departamento'+i).val() == ""){
			$('#Municipio'+i).empty();
			$('<option value = "">Seleccione un Municipio</option>').appendTo('#Municipio'+i);
			$('#Municipio'+i).attr('disabled', 'disabled');
		}else{
			$('#Municipio'+i).removeAttr('disabled', 'disabled');
			$('#Municipio'+i).load('depto_municipio.php?tipo=2&id_pais='+$('#Pais'+i).val()+'&id_depto=' + $('#Departamento'+i).val());
		}			
	}
}
addFila();
</script>
<?php
	
	

	include("inferior.php"); 
?>