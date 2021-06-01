<?php
// path= RUTA EN EL SERVIDROR
// archivo_name = NOMBRE DEL ARCHIVO
// archivo= ARCHIVO COMO TAL
function subirArchivo($path,$archivo_name,$archivo)
{
		$error="no";
		//Carga de Archivo PDF
		//--------------------------------
		//Hace el upload del archivo
//echo "<br>Ingreso 2 -- archi ".$archivo_name."  archioss".$archivo."<br> URL ".$path;
		if (trim($archivo_name) != "")	{

			if (!copy($archivo, $path."/".$archivo_name)) {
				$copioarchivo = "NO";
/*				echo "<script>alert('Error al copiar el archivo.')</script>";
*/
				$error="si";
			}
		}
//echo "<br>Error funcion2 ".$error." ------------- <br>";
		if($error=="si")
		{
			return false;
		}
		if($error=="no")
		{
			return true;
		}
}

function delete_dir($path)
{
	$error = 'no';
	if( !(rmdir($path)))
	{
		$error = 'si';
	}	
	if($error=="no")
		return true;

	if($error=="si")
		return false;		
}

//ELIMINAR ARCHIVOS
function del_arch($path)
{		
	$error = 'no';
	if( !(unlink($path)))
	{
		$error = 'si';
	}		
/*		
		$directorio = opendir($path);
		while($archivoS = readdir($directorio))
		{
			if( (trim($archivoS)!="") and (trim($archivoS)!=".")and (trim($archivoS)!=".."))
			{
				
			}
		}
		*/
		if($error=="no")
			return true;

		if($error=="si")
			return false;
}

function remplazaCaracteresEsp($archivo_name)
{
			$carEspecial = array( 'á', 'é', 'í', 'ó', 'ú', #1
							  'ä', 'ë', 'ï', 'ö', 'ü', #2
							  'à', 'è', 'ì', 'ò', 'ù', #3
							  'â', 'ê', 'î', 'ô', 'û', #4	MAY
							  'Á', 'É', 'Í', 'Ó', 'Ú', #5
							  'Ä', 'Ë', 'Ï', 'Ö', 'Ü', #6
							  'À', 'È', 'Ì', 'Ò', 'Ù', #7
							  'Â', 'Ê', 'Î', 'Ô', 'Û', #8
							  '%', '|', '°', '¬', '"', #9
							  '#', '$', '%', '&', '(', #10
							  ')', '=', '?', '¡',  #11
							  '¿', '+', '{', '}', '[', #12
							  ']', ':', ',', '@', '~', 'ñ', 'Ñ',"'"," ",'´','~');

		$remplazar = array( 'a', 'e', 'i', 'o', 'u', #1
							'a', 'e', 'i', 'o', 'u', #2
							'a', 'e', 'i', 'o', 'u', #3
							'a', 'e', 'i', 'o', 'u', #4	MAY
							'A', 'E', 'I', 'O', 'U', #5
							'A', 'E', 'I', 'O', 'U', #6
							'A', 'E', 'I', 'O', 'U', #7
							'A', 'E', 'I', 'O', 'U', #8
							'-', '-', '-', '-', '-', #9
							'-', '-', '-', '-', '-', #10
							'-', '-', '-', '-', '-', #11
							'-', '-', '-',  '-', #12
							'-', '-', '-', '-', '-', 'n', 'N','',"_",'','' );	
//echo "<br>Error funcion1 ".$error." ------------- <br>";	

/*
$valor=utf8_encode ($archivo_name );
$valor=utf8_decode ($valor);

$valor=( (string) $archivo_name);

echo $valor." ****************** ".str_replace($carEspecial,$remplazar,(str_replace($carEspecial,$remplazar,$valor) ) )."<br>";
echo $archivo_name." Inicial <br>"		;
*/
		$archivo_name=str_replace('/',' ',$archivo_name);
		$archivo_name=str_replace($carEspecial,$remplazar,"'".$archivo_name."'");		
//echo str_replace($carEspecial,$remplazar,"'".$archivo_name."'");				
//echo str_replace($carEspecial,$remplazar,$archivo_name);				
/*
echo str_replace($carEspecial,$remplazar,'Sólicitúd de Servicios de Transporteñ#.pdf')."<br>";
//$valor=utf8_decode ($archivo_name );
//$valor=utf8_encode ($archivo_name );


//		$archivo_name=trim($archivo_name);		
echo $archivo_name." Final"		;		
*/
		return $archivo_name;
}

//$fecha=$fecha actual
//$fecha2=$fecha a revisar

//FORMATOS DE FECHAS mm/dd/aaaa
//COMPARA SI $fecha ES MAYOR A $fecha2
function compare_fecha($fecha, $fecha2)  
{  
  
	$xMonth=substr($fecha,0, 2);  
	$xDay=substr($fecha,3, 5);  
	$xYear=substr($fecha,6,10);  
	
	$yMonth=substr($fecha2,0, 2);  
	$yDay=substr($fecha2,3, 5);  
	$yYear=substr($fecha2,6,10);  
	//si el año de la $fecha ingresada es menor a la $fecha actual

	//alert("mes "+$xMonth+" Ano "+$xYear+" Dia "+$xDay+"\n mes2 "+$yMonth+" Ano2 "+$yYear+" Dia2 "+yDay);

	if ($xYear>$yYear)  
	{  
		return(true); 
	}  
	else  
	{  
	  if ($xYear == $yYear)  
	  {  
		//si el mes de la $fecha ingresada es menor  a la $fecha actual
		if ($xMonth> $yMonth)  
		{  
			return(true) ; 
		}  
		else  
		{   
			//si el mes ingresado y el actual son iguales			
		  if ($xMonth == $yMonth)  
		  {  
			//si el dia de la $fecha ingresada es menor a la de la $fecha actual
			if ($xDay> $yDay)  
			  return(true);  
			else  
			  return(false);  
		  }  			  
		  else  
			return(false);  
		}  
	  }
	  //si el año de la $fecha ingresada es mayor a la actual	  
	  else  
		return(false);  
	}  
} 

?>