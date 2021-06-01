<?php
/*
$conexion = new mysqli('servidor','usuario','password','basedatos',3306);
$matricula = $_GET['term'];
*/

include("encabezado.php");  

$matricula = $_GET['term'];

$consulta = "select nombre from HojaDeTiempo.dbo.Usuarios where retirado is null ";

$cursor=mssql_query($consulta);
while($fila=mssql_fetch_array($cursor))
{				
		$matriculas[] = $fila['nombre'];	
}
echo json_encode($matriculas);

/*
$result = $conexion->query($consulta);

if($result->num_rows > 0){
	while($fila = $result->fetch_array()){
		$matriculas[] = $fila['nombre'];		
	}
	echo json_encode($matriculas);
}
*/

?>