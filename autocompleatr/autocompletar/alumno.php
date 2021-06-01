<?php
	
//	$conexion = new mysqli('servidor','usuario','password','basedatos',3306);

	include("encabezado.php"); 
	$matricula = $_POST['matricula'];
	//$consulta = "select nombre, paterno, materno FROM tblalumno WHERE matricula = '$matricula'";
	$consulta = "select * from HojaDeTiempo.dbo.Usuarios where retirado is null and nombre like '%".$matricula."%' ";

	//$result = $conexion->query($consulta);
	$result=mssql_query($consulta);
	
	$respuesta = new stdClass();
	if($result->num_rows > 0){
		$fila = $result->fetch_array();
		$respuesta->nombre = $fila['nombre'];
		$respuesta->paterno = $fila['apellidos'];
		$respuesta->materno = $fila['unidad'];		
	}
	echo json_encode($respuesta);

?>