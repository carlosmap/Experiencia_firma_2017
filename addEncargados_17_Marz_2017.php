<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente
banner(2,"Informaci&oacute;n Adicional");


if($recarga==2)
{
}

//info proy
$sql_nom="select * from EFNombres_Proyectos where id_proyecto=".$Proy ." and nombre_actual=1";
$cur=mssql_query($sql_nom);
$datos_pro=mssql_fetch_array($cur);

//INFO USUARIOS
$sql_nom="select unidad,upper(nombre) nombre,upper(apellidos) apellidos from HojaDeTiempo.dbo.Usuarios where retirado is null and fechaRetiro is null order by apellidos";
$cur_USU=mssql_query($sql_nom);

?>

<script>
	function addFila()
	{

	
		  var div='';
		   div+="<input maxlength='5' name='inputTextMulti[]' size='6' type='text' />&nbsp;";	
	
		  document.getElementById("divMultiInputs").innerHTML=div;
	}
</script>
<form id="formulario" name="formulario" method="post">
    <div class="container-fluid">
    
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

          <div class="form-group" id="divDirector">
            <label for="">Director</label>
                <select name="Director" id="Director" class="form-control">
                        <option value="">Seleccione el Director</option>                
<?PHP
					while($datos_usu=mssql_fetch_array($cur_USU))
					{
?>             
                        <option value="<?=$datos_usu["unidad"] ?>"><?=$datos_usu["apellidos"]." ".$datos_usu["nombre"]." [".$datos_usu["unidad"]."]" ?></option>
<?php
					}
?>
                </select>
             <span id="helpDirector" class="help-block" style="display:none;" >El director del proyecto es obligatorio.</span>    
          </div>
          
          <div class="form-group" id="divCoordinador">
          	<label for="">Coordinador</label>
            	<select name="Coordinador" id="Coordinador" class="form-control">
          			<option value="">Seleccione el Coordinador</option>
<?PHP
					$cur_USU=mssql_query($sql_nom);
					while($datos_usu=mssql_fetch_array($cur_USU))
					{
?>             
                        <option value="<?=$datos_usu["unidad"] ?>"><?=$datos_usu["apellidos"]." ".$datos_usu["nombre"]." [".$datos_usu["unidad"]."]" ?></option>
<?php
					}
?>
          		</select>      
           </div>

          <div class="form-group" id="">
          	<label for="">Ordenadores de gasto</label>
           </div>           
           
          <div class="form-group" id="divOrdenador">
	          <div class="main row">
                    <div class="col-sm-11 ">
                        <select name="Ordenador" id="Ordenador" class="form-control">
                            <option value="">Seleccione el Ordenador de Gasto</option>
        <?PHP
                            $cur_USU=mssql_query($sql_nom);
                            while($datos_usu=mssql_fetch_array($cur_USU))
                            {
        ?>             
                                <option value="<?=$datos_usu["unidad"] ?>"><?=$datos_usu["apellidos"]." ".$datos_usu["nombre"]." [".$datos_usu["unidad"]."]" ?></option>
        <?php
                            }
        ?>
                        </select>      
                     </div>
                     <div class="col-sm-1 ">
                        
                        <i class="glyphicon glyphicon-minus-sign btn btn-danger" onClick="addFila()"></i>
                        
                     </div>  	
               </div>    
           </div>
            <div class="form-group" style="text-align:right;">
	            <i class="glyphicon glyphicon-plus-sign btn btn-success"></i>
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