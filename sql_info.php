<?php
//	include("encabezado.php"); 
	include("conectaBD.php"); 
	include("funciones.php"); 	
	$conexion = conectar();
	
	$pagina=($_REQUEST['pagina']);
	
	//SI SE LLAMA DESDE LA PAGINA usuAdmin.php
	if($pagina==1)
	{
		$id_perfil=($_REQUEST['perfil']);
		$id_estado=($_REQUEST['estado']);	
	
		$sql_usu="select (U.apellidos+' '+U.nombre) nombre, U.unidad, U.retirado, CONVERT(varchar, fecha_inicio_activo,101) fecha_inicio_activ,  
		CONVERT(varchar, fecha_final_activo,101) fecha_final_activ  , EFPerfiles.id_perfil , EFPerfiles.perfil, EFUsuarios.estado
		, case when fecha_final_activo<GETDATE() then 1 else 0 end  fecha_vencida
		 from EFUsuarios 
		inner join HojaDeTiempo.dbo.Usuarios U on EFUsuarios.unidad=U.unidad
		inner join EFPerfiles_Usuarios U2 on U2.unidad=EFUsuarios.unidad and perfil_actual=1
		inner join EFPerfiles on U2.id_perfil=EFPerfiles.id_perfil
		where U.unidad is not null ";
		if(trim($id_perfil)!="")
		{
			$sql_usu.=" and U2.id_perfil=".$id_perfil;
		}
		if( (trim($id_estado)!=""))
		{	
			
			$sql_usu.=" and EFUsuarios.estado=".$id_estado."";
		}
		
		$sql_usu.=" order by nombre";
	
	//echo $sql_usu;	
	
	?>
			  <table width="100%" class="table table-bordered"  id="Usuarios"  >
	
			<tr>
			  <th >&nbsp;</th>
				<th >Unidad</th>                
				<th>Nombre</th>
				<th>Perfil</th>
				<th>Estado</th>
				<th>Fecha Inicio</th>
				<th>Fecha Finalizaci&oacute;n</th>
				<th></th>
			</tr>		
	
	<?php
		$cur_usu=mssql_query($sql_usu);
		while($datos_usu=mssql_fetch_array($cur_usu) )
		{
			//echo '<tr class="TR"><td>'.$datos_usu["unidad"].'</td> </tr>';
			
	?>                        	
			<tr class="TR table " >
	
	<?php
				if($datos_usu["retirado"]==1)
				{
	?>   
					 <td class="table-bordered alert-danger	" title="Retirado de la compañ&iacute;a" width="1%" ><i class="glyphicon glyphicon-user glyphicon-user"></i></td>       
					 
	<?php
				}
				else
				{
	?>          
					<td class="table-bordered alert-success" title="Activo en el portal" width="1%" ><i class="glyphicon glyphicon glyphicon-user"></i></td>
	<?php				
				}
	?>                
	
				<td class="table-bordered" ><?=$datos_usu["unidad"] ?></td>
				<td><?=ucwords(strtolower(utf8_encode($datos_usu["nombre"]))) ?></td>
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
	<?
								/// SI EL USUARIO ES DE CONSULTA, ESTA ACTIVO Y LA FECHA DE FINALIZACION SE ENCUENTRA CADUCADA, SE VISUALIZA LA CELDA EN ROJO
	?>            
				<td class=' <?=( ($datos_usu["id_perfil"]==3)&&($datos_usu["estado"]=='1')&&($datos_usu["fecha_vencida"]==1) ) ? "alert-danger": "";  ?> ' ><?=$datos_usu["fecha_final_activ"] ?></td>
				<td><a href="#" onclick="MM_openBrWindow('upUsu.php?acc=2&uni=<?=$datos_usu["unidad"] ?>','EF2','scrollbars=yes,resizable=yes,width=450,height=470');"> <i class="glyphicon glyphicon-pencil"></i> </a> </td>
			</tr>
			
	<?    
									
		}
	?>
				</table>
<?

	}
	
	//SI SE LLAMA DESDE LA PAGINA usuAdmin.php
	if($pagina==2)
	{	
	}
?>                