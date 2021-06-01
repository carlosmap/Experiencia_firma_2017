<?PHP
//CONSULTA LA INFORMACION DEL USUARIO

$SQL_INF_USU="select EFUsuarios.*, U.nombre, U.apellidos, EFPerfiles_Usuarios.id_perfil, case when fecha_final_activo<CONVERT(varchar, GETDATE(),101) then 1 else 0 end  fecha_vencida_final ,case when fecha_inicio_activo> CONVERT(varchar, GETDATE(),101) then 1 else 0 end  fecha_vencida_inicial  from EFUsuarios
INNER JOIN HojaDeTiempo.dbo.Usuarios U ON U.unidad=EFUsuarios.unidad
inner join EFPerfiles_Usuarios on EFPerfiles_Usuarios.unidad=EFUsuarios.unidad  and EFPerfiles_Usuarios.perfil_actual=1
where EFUsuarios.unidad=".$_SESSION["sesUnidadUsuario"]. " and EFUsuarios.estado=1";

$cur_query=mssql_query($SQL_INF_USU);

if(mssql_num_rows($cur_query)>0)
{
	$datos=mssql_fetch_array($cur_query);
	
	//ALMACENA EL PERFIL DEL USUARIO EN LA VARIABLE DE SESSION
	$_SESSION["sesPerfilUsuarioExpFir"]=$datos["id_perfil"];
	
	//SI EL PREFIL DEL USUARIO ES EL DE CONSULTA
	if($datos["id_perfil"]==3)
	{
		//Y LA FECHA DE ACTIVIDAD HA VENCIDO
		if($datos["fecha_vencida_final"]==1)
		{
			//CAMBIA EL ESTADO DEL USUARIO A INACTIVO
			$sql_inser="update EFUsuarios set estado='0' , fechaMod=getdate() where unidad= ".$_SESSION["sesUnidadUsuario"]. " ";
			$cursorIn1=mssql_query($sql_inser);		
			//DESTRUYE LA VARIABLE DE SESSION
			unset($_SESSION["sesPerfilUsuarioExpFir"]);
			
			header('Location: ../../portal/indiceGeneral.php');			
		}
		
		//SI LA FECHA DE INICIO DE ACTIVIDAD ANUN NO HA COMENZADO 
		if($datos["fecha_vencida_inicial"]==1)
		{
			unset($_SESSION["sesPerfilUsuarioExpFir"]);			
			header('Location: ../../portal/indiceGeneral.php');
		}
	}
}
else
	header('Location: ../../portal/indiceGeneral.php');
?>