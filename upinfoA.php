<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente
if($acc==2)
	$mens_v="Actualizar Informaci&oacute;n Adicional";	   		
if($acc==3)
	$mens_v="Eliminar Informaci&oacute;n Adicional";	   
	
banner(2,$mens_v);


if($recarga==2)
{
	mssql_query("begin transaction");				
	if($acc==2)
	{	
			$cursorIn1=mssql_query("DELETE from EFEspecialidades_Proyectos where id_proyecto=".$Proy);	
			for($i=0;$i<$cantEspecialidades;$i++)
			{
				if($cursorIn1!="")
				{		
					$valor="Especialidades".$i;
					if($$valor!="")
					{
						$sql_inse="insert into EFEspecialidades_Proyectos  (id_proyecto,id_especialidad,usuarioGraba, fechaGraba) values(".$Proy .",".$$valor.",".$_SESSION["sesUnidadUsuario"].",getdate() )";
						$cursorIn1=mssql_query($sql_inse);		
					}
				}
			}
		
			if($cursorIn1!="")
			{		
				$cursorIn1=mssql_query("DELETE from EFClases_Proyectos where id_proyecto=".$Proy);	
			}
		//echo $sql_inse.mssql_get_last_message()."<br>";
			for($i=0;$i<$cantClases;$i++)
			{
				if($cursorIn1!="")
				{		
					$valor="clase".$i;
					if($$valor!="")
					{			
						$sql_inse="insert into EFClases_Proyectos  (id_proyecto,id_clase,usuarioGraba, fechaGraba) values(".$Proy .",".$$valor.",".$_SESSION["sesUnidadUsuario"].",getdate() )";
						$cursorIn1=mssql_query($sql_inse);		
					}
				}
			}
			
		//echo $sql_inse.mssql_get_last_message()."<br>";	
			if($cursorIn1!="")
			{	
				
				if($Objeto=="")
					$Objeto="NULL";
				else
					$Objeto="'".$Objeto."'";	
					
				$sql_inse="update EFProyectos set objeto=".$Objeto." where id_proyecto=".$Proy ." ";
				$cursorIn1=mssql_query($sql_inse);		
			}	
	}
	if($acc==3)
	{
			$cursorIn1=mssql_query("DELETE from EFEspecialidades_Proyectos where id_proyecto=".$Proy);		
			if($cursorIn1!="")
			{				
				$cursorIn1=mssql_query("DELETE from EFClases_Proyectos where id_proyecto=".$Proy);					
				if($cursorIn1!="")
				{								
					$sql_inse="update EFProyectos set objeto=NULL where id_proyecto=".$Proy ." ";
					$cursorIn1=mssql_query($sql_inse);		
				}
			}
	}
//echo $sql_inse.mssql_get_last_message()."<br>";

	if  (trim($cursorIn1) != "")  {
		mssql_query("commit transaction");		
		echo ("<script>alert('Operaci\u00f3n realizada con exito');</script>"); 					
	} 
	else {
		mssql_query("rollback  transaction");		
		echo ("<script>alert('Error durante la operaci\u00f3n');</script>");
	}			

	echo ("<script>window.close(); MM_openBrWindow('infoG.php?Proy=".$Proy."','EF','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
	
}



//info proy
$sql_nom="select * from EFNombres_Proyectos where id_proyecto=".$Proy ." and nombre_actual=1";
$cur=mssql_query($sql_nom);
$datos_pro=mssql_fetch_array($cur);

//ESPECIALIDADES
$cur=mssql_query("select * from EFEspecialidades where estado=1 order by especialidad");

$cur_cla=mssql_query("select * from EFClases where estado=1 order by clase");

$cur_proyO=mssql_query("select objeto from EFProyectos where id_proyecto=".$Proy);
$datos_proO=mssql_fetch_array($cur_proyO);


//ALMACENA EN UN ARRAY LAS ESPECIALIDADES ASOCIADAS AL PROYECTO
$i=0;
$sql_espe="select * from EFEspecialidades_Proyectos
  where id_proyecto=".$Proy." ";
$cur_espe1=mssql_query($sql_espe);
while($datos_esp=mssql_fetch_array($cur_espe1))
{
    $especialidades[$i]=$datos_esp["id_especialidad"];
	$i++;
}

//ALMACENA EN UN ARRAY LAS CLASES ASOCIADAS AL PROYECTO
$i=0;
$sql_espe="select * from EFClases_Proyectos
  where id_proyecto=".$Proy." ";
$cur_espe1=mssql_query($sql_espe);
while($datos_esp=mssql_fetch_array($cur_espe1))
{
    $clases[$i]=$datos_esp["id_clase"];
	$i++;
}

$dis="";
if($acc==3)
	$dis="disabled";
?>

<script type="text/javascript">
function valida()
{
 
	var campos_check = ["Especialidades"];		
	var error=0;	
	error=valida_campos(campos_check,2,document.formulario.cantEspecialidades.value);
	
	campos_check = ["clase"];	
	
	error+=valida_campos(campos_check,2,document.formulario.cantClases.value);
	
//	error+=valida_campos("",5);
//alert(" ------ "+error)	;
	//SI NO SE PRESENTARON PROBLEMAS DE VALIDACION
	if(error==0)
	{
		document.formulario.recarga.value="2";
		document.formulario.submit();
	}
	
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
        <br>
        <div class="main row table-bordered ">
            <div class="form-group "  >
              <div class="col-sm-6 " >
                <label for="">Especialidades</label>
                
               </div>
               <div class="col-sm-6 scroll" style="height: 100px; "  >
    <?PHP           
                $i=0;
               while($datos_esp=mssql_fetch_array($cur))
               {
				   $chek="";
				   foreach($especialidades as $valor)
				   {
					   if($valor==$datos_esp["id_especialidad"])
					   		$chek="checked";
				   }				   	
    ?>		   
                    <div class="main row   TR table-bordered"    >
                          <div  class="col-sm-11 table-bordered" >
                              <?=$datos_esp["especialidad"] ?>
                          </div>
                          <div  class="col-sm-1  " >
                              <span>
                                <input type="checkbox" id="Especialidades<?=$i ?>" name="Especialidades<?=$i ?>" value="<?=$datos_esp["id_especialidad"] ?>" class="checkbox" <?=$chek ?> <?=$dis ?>>	
                              </span>                          
                          </div>     
                    </div>
    <?php
                    $i++;
               }
    ?>           
                <input type="hidden" value="<?=$i ?>" name="cantEspecialidades" id="cantEspecialidades">
               </div>
            </div>
		</div>
        
        <div class="form-group has-error">
	        <span class="help-block " id="helpEspecialidades"  style="display:none;">Por favor seleccione almenos una especialidad.</span>                                
        </div>          

        <div class="main row table-bordered ">
            <div class="form-group " >
              <div class="col-sm-6 " >        
                   <label for="">Clase</label>          
               </div>
               <div class="col-sm-6 scroll" style="height: 100px; "  >
    <?PHP           
                    $i=0;
                   while($datos_esp=mssql_fetch_array($cur_cla))
                   {
					   $chek="";
					   foreach($clases as $valor)
					   {
						   if($valor==$datos_esp["id_clase"])
								$chek="checked";
					   }						   
        ?>		   
                        <div class="main row  TR  table-bordered"   >
                              <div  class="col-sm-11 table-bordered" >
                              
                                  <?=$datos_esp["clase"] ?>
                              </div>
                              <div  class="col-sm-1 " >
                                <span>
                                  <input type="checkbox" id="clase<?=$i ?>" name="clase<?=$i ?>" value="<?=$datos_esp["id_clase"] ?>" class="checkbox"  <?=$chek ?> <?=$dis ?>>	
                                </span>
                              </div>              
                        
                        </div>
        <?php
                        $i++;
                   }
        ?>              
                <input type="hidden" value="<?=$i ?>" name="cantClases" id="cantClases">    
               </div>
            </div>
		</div>   
              
        <div class="form-group has-error">
	        <span class="help-block " id="helpclase"  style="display:none;">Por favor seleccione almenos una clase.</span>                                
        </div>           
           
        <div class="form-group" >
        	<div class="col-sm-12 main row">
            	<label for="">Objeto</label>
            </div>
        	<div class="col-sm-12 main row form-group">
            <textarea name="Objeto" id="Objeto" cols="140" rows="3" class="form-control" <?=$dis ?> ><?=$datos_proO["objeto"] ?></textarea>
            </div>            
        </div>
   <?php
   		$mens_b="";
	   if($acc==2)
	   		$mens_b="Actualizar";	   		
	   if($acc==3)
	   		$mens_b="Eliminar";	   
	   
   ?>        
        <div class="main row " style="text-align:right">
            <div class="col-sm-12 ">
                <button type="button" class="btn btn-primary" onClick="valida()"><?=$mens_b ?></button>
		       <input name="recarga" type="hidden" id="recarga" value="1">                
            </div>
		</div>    
       
                        
     
    </div>       

  
</form>  
  
<?php
	include("inferior.php"); 
?>