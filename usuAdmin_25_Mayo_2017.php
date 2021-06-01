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
                    <select name="Perfil" id="Perfil" class="form-control">
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
                	<select name="Estado" id="Estado" class="form-control">
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
        
       	  <table width="100%" class="table table-bordered"  >
        		<tr>
        			<th >Unidad</th>                
        			<th>Nombre</th>
        			<th>Perfil</th>
        			<th>Estado</th>
        			<th>Fecha Inicio</th>
        			<th>Fecha Finalizaci&oacute;n</th>
        			<th></th>
        		</tr>
                
<?php
				$cur_usu=mssql_query("select (U.apellidos+' '+U.nombre) nombre, U.unidad, U.retirado, CONVERT(varchar, fecha_inicio_activo,101) fecha_inicio_activ,  
				CONVERT(varchar, fecha_final_activo,101) fecha_final_activ , EFPerfiles.perfil, EFUsuarios.estado from EFUsuarios 
				inner join HojaDeTiempo.dbo.Usuarios U on EFUsuarios.unidad=U.unidad
				inner join EFPerfiles_Usuarios U2 on U2.unidad=EFUsuarios.unidad and perfil_actual=1
				inner join EFPerfiles on U2.id_perfil=EFPerfiles.id_perfil
				order by nombre");
				while($datos_usu=mssql_fetch_array($cur_usu) )
				{
?>                        	
					
                    <tr class="TR">
                        <td><?=$datos_usu["unidad"] ?></td>
                        <td><?=ucwords(strtolower($datos_usu["nombre"])) ?></td>
                        <td><?=$datos_usu["perfil"] ?></td>
                        <? 
							if($datos_usu["estado"]=='1')
							{
?>
								<td align="center" class="alert-success"  ><span class="glyphicon glyphicon-ok-circle" title="Activo"></span></td>
<?
							}
							else
							{
?>
								<td align="center" class="alert-danger"><span class="glyphicon glyphicon-ban-circle" title="Inactivo"></span></td>
<?
							}
						 ?>
                        <td><?=$datos_usu["fecha_inicio_activ"] ?></td>
                        <td><?=$datos_usu["fecha_final_activ"] ?></td>
                        <td><a href="#" onclick="MM_openBrWindow('upProy.php?acc=2&amp;Proy=3188','EF2','scrollbars=yes,resizable=yes,width=600,height=650');"> <i class="glyphicon glyphicon-pencil"></i> </a> </td>
                    </tr>
<?                                
                }
?>		                    
        	</table>
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

</div>


<?php
	include("inferior.php"); 
?>