<?php
	include("encabezado.php"); 
	
	$id_pais=intval($_REQUEST['id_pais']);
	$id_depto=intval($_REQUEST['id_depto']);
	$tipo=intval($_REQUEST['tipo']);
	if($tipo==1)	
	{
		$cur_depto=mssql_query("SELECT * from EFDepartamentos where estado=1 and id_pais =".$id_pais." order by  departamento ");        
		echo '<option value = "">Seleccione un Departamento </option>';
		while($datos_depto=mssql_fetch_array($cur_depto))
		{
			echo '<option value = "'.$datos_depto['id_departamento'].'">'.utf8_encode( $datos_depto['departamento']).'</option>';
		}
	
	}
	
	if($tipo==2)	
	{
		
		$cur_depto=mssql_query("SELECT * from EFMunicipios where estado=1 and id_pais =".$id_pais." and id_departamento=".$id_depto."  order by  municipio ");        
		echo '<option value = "">Seleccione un Municipio </option>';
		while($datos_depto=mssql_fetch_array($cur_depto))
		{
			echo '<option value = "'.$datos_depto['id_municipio'].'">'.utf8_encode( $datos_depto['municipio']).'</option>';
		}
			
	}
//        $cur_clie=mssql_query("SELECT * from EFMunicipios where estado=1 and id_pais =".$Pais." and id_departamento=".$Depto."  order by  municipio ");       
/*
	$municipios = $conn->prepare("SELECT * FROM toes WHERE does_toes_id = '$id_pais'") or die(mysqli_error());
		echo '<option value = "">Selecciona un municipio  </option>';
	if($municipios->execute()){
		$a_result = $municipios->get_result();
	}
		while($row = $a_result->fetch_array()){
			echo '<option value = "'.$row['toes_id'].'">'.utf8_encode( $row['toes_name']).'</option>';
		}
*/
?>