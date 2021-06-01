<?PHP
//CONSULTA LA INFORMACION DEL USUARIO
$SQL_INF_USU="select EFUsuarios.*, U.nombre, U.apellidos, EFPerfiles.perfil from EFUsuarios
INNER JOIN HojaDeTiempo.dbo.Usuarios U ON U.unidad=EFUsuarios.unidad
inner join EFPerfiles on EFPerfiles.id_perfil=EFUsuarios.id_perfil 
where EFUsuarios.unidad=".$_SESSION["sesUnidadUsuario"]. " and EFUsuarios.estado=1";

$cur_query=mssql_query($SQL_INF_USU);

if(mssql_num_rows($cur_query)>0)
{
	$datos=mssql_fetch_array($cur_query);
	
	//ALMACENA EL PERFIL DEL USUARIO EN LA VARIABLE DE SESSION
	$_SESSION["sesPerfilUsuarioExpFir"]=$datos["id_perfil"];
}
else
	header('Location: ../../portal/indiceGeneral.php');
?>