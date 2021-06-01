<?php 

include("encabezado.php"); 
//tipo=1 (para ventanas principales)
banner(1);
?>

<div class="container" >

		<? include ("menuAdmin.php"); ?>

        <div  class="titulo_secccion">
                M&oacute;dulo Administrativo - Usuarios
                <div class="espacioAzul">
                 </div>
        </div>
        
        <br>

       <div class="col-md-12">
       		<div class="row">
                <div class="col-md-1 col-md-offset-4"><label for="">Perfil</label></div>
                <div class="col-md-3">
                    <select name="Perfil" id="Perfil" class="form-control" onChange="cargaInfo()" >
						<option value="">Seleccione un Perfil</option>                    
<?php
							$cur_per=mssql_query("select * from EFPerfiles where estado=1");
							while($datos_per=mssql_fetch_array($cur_per) )
							{
?>                        	
		                        <option value="<?=$datos_per[id_perfil] ?>"><?=$datos_per[perfil] ?></option>
<?                                
                             }
?>							 
                    </select>
                </div>
            </div> 	
            <div class="row">
            	<div class="col-md-12">&nbsp;</div>
            </div>
       		<div class="row">            
                <div class="col-md-1 col-md-offset-4"><label for="">Estado</label></div>
                <div class="col-md-3">
                	<select name="Estado" id="Estado" class="form-control" onChange="cargaInfo()" >
						<option value="">Seleccione Estado</option>
						<option value="1"  >Activo</option>
						<option value="0"  >Inactivo</option>                    
                    </select>

                </div>
			</div>
       </div>    
       
       <div class="col-md-12">
       <br>

       </div>
		       
       <div class="col-md-12">
       
       	<div class="row">
        	<div id="Usuarios" class="col-md-12">
            </div>
        	<!--
       		<div class="col-md-1 table-bordered" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-size: 16px; font-weight:bold;
            " >Unidad</div>
       		<div class="col-md-4 table-bordered"><b>Nombre</b></div>
       		<div class="col-md-1 table-bordered">Perfil</div>
       		<div class="col-md-1 table-bordered">Estado</div>
       		<div class="col-md-2 table-bordered">Fecha Inicio</div>
       		<div class="col-md-2 table-bordered">Fecha Finalizaci&oacute;n</div>
       		<div class="col-md-1 table-bordered">.</div>
       	</div>
       	<div class="row">
       		<div class="col-md-1 table-bordered">.</div>
       		<div class="col-md-4 table-bordered"></div>
       		<div class="col-md-1 table-bordered"></div>
       		<div class="col-md-1 table-bordered"></div>
       		<div class="col-md-2 table-bordered"></div>
       		<div class="col-md-2 table-bordered"></div>
       		<div class="col-md-1 table-bordered"></div>
             -->
       	</div>
       
       </div>
       
       <div class="row">
       	<div class="col-md-12" style="text-align:right;"  ><button class="btn btn-primary" onClick="MM_openBrWindow('addUsu.php','EF2','scrollbars=yes,resizable=yes,width=450,height=470');" >Nuevo Usuario</button></div>
       </div>
       
</div>

<script type="text/javascript">
	function cargaInfo()
	{
				
//		$('#Usuarios').empty();
		$('#Usuarios').load('sql_info.php?perfil='+$('#Perfil').val()+'&estado='+ $('#Estado').val());		
			
	}
	cargaInfo();
</script>

<?php
	include("inferior.php"); 
?>