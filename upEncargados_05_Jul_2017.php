<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente
if($acc==2)
	$mens_v="Actualizar Encargados";	   		
if($acc==3)
	$mens_v="Eliminar Encargados";	   
banner(2,$mens_v);

//include ("../../verificaRegistro2.php");
if($recarga==2)
{	
	mssql_query("begin transaction");		
	if($acc==2)
	{
		$cursorIn1="transaccion";	
		if($DirectorAnt!=$Director)
		{
			///ACTUALIZA EL REGISTRO DEL DIRECTOR ACTUAL
			$sqlIn1 = " UPDATE EFDirector_Coordinador SET director_coordinador_actual=0 where id_proyecto=".$Proy." and tipo=1 ";
			$cursorIn1 = mssql_query($sqlIn1);						
			if($cursorIn1!="")
			{			
				//ALMACENA EL NUEVO DIRECTOR
				$sqlIn1 = " INSERT INTO EFDirector_Coordinador";
				$sqlIn1 = $sqlIn1 . "( id_director_coordinador, id_proyecto, unidad_director_coordinador, tipo, director_coordinador_actual, usuarioGraba, fechaGraba ) ";
				$sqlIn1 = $sqlIn1 . " values ( (select isnull(MAX(id_director_coordinador),0)+1 id_proye  from EFDirector_Coordinador WHERE id_proyecto=".$Proy."), ".$Proy.", ".$Director.", 1, 1 ,".$_SESSION["sesUnidadUsuario"].",getdate()  ) ";
				$cursorIn1 = mssql_query($sqlIn1);				
				
///----
//echo $sqlIn1." *** ".mssql_get_last_message()."<br><br>";
///---								
			}
		}

		//CONSULTA EL ID DEL PROYECTO EN EXP FIRMA
		$sql_id_proy_exp="select id_proyecto_portal from GestiondeInformacionDigital.dbo.EFProyectos WHERE id_proyecto=".$Proy." ";
		$cursorIn1 = mssql_query($sql_id_proy_exp);
		$datos_id_proy_exp=mssql_fetch_array($cursorIn1);
		$elProyecto=$datos_id_proy_exp["id_proyecto_portal"];
		
		if($elProyecto!="")
		{		
			$sql1 = " SELECT A.* FROM HojaDeTiempo.dbo.Proyectos A WHERE A.id_proyecto = " . $elProyecto;
			//echo $sql1;
			$cursor1 = mssql_query($sql1);
			$reg1 = mssql_fetch_array($cursor1);
			$codProyecto = $reg1['codigo'];
			$cargoProyecto = $reg1['cargo_defecto'];
			$nombreProyecto = $reg1['nombre'];			
		}
		
//echo $sql_id_proy_exp." *** ".mssql_get_last_message()."<br><br>";
		if(($CoordinadorAnt!=$Coordinador)||($DirectorAnt!=$Director))
		{
			//REGISTRO DEL DIRECTOR Y COORDINADOR EN LA HOJA DE TIEMPO
			if($cursorIn1!="")
			{	
	
				if($elProyecto!="")
				{
						
					$sqlIn1 = " UPDATE HojaDeTiempo.dbo.Proyectos SET ";		
					$sqlIn1 = $sqlIn1 . " id_director = " . $Director . ", ";
					if($Coordinador != ""){
						$sqlIn1 = $sqlIn1 . " id_coordinador = ".$Coordinador." ";
					} else {
						$sqlIn1 = $sqlIn1 . " id_coordinador = NULL ";
					}
					$sqlIn1 = $sqlIn1 . " WHERE id_proyecto = " . $elProyecto . " ";
					//echo $sqlIn1 . "<br>";
					$cursorIn1 = mssql_query($sqlIn1);
					if($cursorIn1!="")
					{
								
							//Obtiene la Información del nombre del Director, del Coordinador
							$sqlJ1 = " SELECT * FROM HojaDeTiempo.dbo.Usuarios WHERE unidad = " . $Director . " ";
							$cursorJ1 = mssql_query($sqlJ1);
							if($regJ1 = mssql_fetch_array($cursorJ1)){
								$nomDirProyecto = $regJ1['unidad'] . " - " . ucwords(strtolower($regJ1['nombre'] . " " . $regJ1['apellidos']));
							}
							
							if($Coordinador != "")
							{
								$sqlJ2 = " SELECT * FROM HojaDeTiempo.dbo.Usuarios WHERE unidad = " . $Coordinador . " ";
								$cursorJ2 = mssql_query($sqlJ2);
								if($regJ2 = mssql_fetch_array($cursorJ2)){
									$nomCoordProyecto = $regJ2['unidad'] . " - " . ucwords(strtolower($regJ2['nombre'] . " " . $regJ2['apellidos']));
								}
		 
							}
							
							/*
							Envía los correos al director, al coordinador, a los ordenadores del gasto, a los programadores
							y a todo ese reguero de gente que está apuntada en la lista
							*/
					
							$elAsunto = "Portal Ingetec - Modificación de Información de Proyectos";
							$elCuerpo = "Se ha efectuado una modificaci&oacute;n de informaci&oacute;n en el Proyecto <strong>" . $nombreProyecto . " - [" . $codProyecto . "." . $cargoProyecto . "]</strong>";
							$elCuerpo = $elCuerpo . "<br><br>";
							$elCuerpo = $elCuerpo . "Director de Proyecto: <strong>" . $nomDirProyecto . "</strong><br>";
							$elCuerpo = $elCuerpo . "Coordinador de Proyecto: <strong>" . $nomCoordProyecto . "</strong><br>";
		//					$elCuerpo = $elCuerpo . "Empresa: <strong>" . $nomEmpresa . "</strong><br>";
		//					$elCuerpo = $elCuerpo . "División: <strong>" . $nomDivision . "</strong><br>";
							$laFirma = "Intranet INGETEC - Contratos";
		
							enviarCorreo('carlosmaguirre@ingetec.com.co', $elAsunto, $elCuerpo, $laFirma);

							//Al Director y al coordinador de Proyecto
							$sqlDir = " SELECT email FROM HojaDeTiempo.dbo.Usuarios WHERE unidad IN ( " . $Director . ", " . $Coordinador . " ) ";
							$cursorDir = mssql_query($sqlDir);
							while($regDir = mssql_fetch_array($cursorDir)){
								$aQuien = $regDir['email'] . "@ingetec.com.co";
								enviarCorreo($aQuien, $elAsunto, $elCuerpo, $laFirma);
							}
		
							//A los Ordenadores del Gasto
							$sqlOrd = " SELECT A.email
							FROM HojaDeTiempo.dbo.Usuarios A, GestiondeInformacionDigital.dbo.OrdenadorGasto B
							WHERE A.unidad = B.unidadOrdenador
							AND A.retirado IS NULL ";
							$sqlOrd = $sqlOrd . " AND B.id_proyecto = " . $elProyecto;
							$cursorOrd = mssql_query($sqlOrd);
							while($regOrd = mssql_fetch_array($cursorOrd)){
								$aQuien = $regOrd['email'] . "@ingetec.com.co";
								enviarCorreo($aQuien, $elAsunto, $elCuerpo, $laFirma); 
							}
							
							//A los programadores
							$sqlProg = " SELECT A.email
							FROM HojaDeTiempo.dbo.Usuarios A, HojaDeTiempo.dbo.Programadores B
							WHERE A.unidad = B.unidad
							AND A.retirado IS NULL ";
							$sqlProg = $sqlProg . " AND B.id_proyecto = " . $elProyecto;
							$cursorProg = mssql_query($sqlProg);
							while($regProg = mssql_fetch_array($cursorProg)){
								$aQuien = $regProg['email'] . "@ingetec.com.co";
								enviarCorreo($aQuien, $elAsunto, $elCuerpo, $laFirma); 
							}
							
							//Al reguero de gente
							$sqlNotif = " SELECT A.email
							FROM HojaDeTiempo.dbo.Usuarios A, GestiondeInformacionDigital.dbo.AutorizadosSolCodigo B
							WHERE A.unidad = B.unidad
							AND A.retirado IS NULL ";	
							$cursorNotif = mssql_query($sqlNotif);
							while($regNotif = mssql_fetch_array($cursorNotif)){
								$aQuien = $regNotif['email'] . "@ingetec.com.co";
								enviarCorreo($aQuien, $elAsunto, $elCuerpo, $laFirma);
							}
		
		//--
			
					}
				}			
		//echo $sqlIn1."<br>".mssql_get_last_message()."<br>";					
			}	
		}
//echo $sqlIn1."<br>".mssql_get_last_message()."<br>";			
		if($cursorIn1!="")
		{	

			if($CoordinadorAnt!=$Coordinador)
			{
				
				///ACTUALIZA EL REGISTRO DEL CORRDINADOR AIGNADO ANTERIORMENTE A INACTIVO
				$sqlIn1 = " UPDATE EFDirector_Coordinador SET director_coordinador_actual=0 where id_proyecto=".$Proy." and tipo=2 ";
				$cursorIn1 = mssql_query($sqlIn1);						
				if($cursorIn1!="")
				{		
//echo $Coordinador." ----------------------- ";									
					if($Coordinador!="")
					{
						//ALMACENA EL NUEVO CORRDINADOR
						$sqlIn1 = " INSERT INTO EFDirector_Coordinador";
						$sqlIn1 = $sqlIn1 . "( id_director_coordinador,id_proyecto  , unidad_director_coordinador, tipo, director_coordinador_actual, usuarioGraba, fechaGraba ) ";
						$sqlIn1 = $sqlIn1 . " values ( (select isnull(MAX(id_director_coordinador),0)+1 id_proye  from EFDirector_Coordinador WHERE id_proyecto=".$Proy."), ".$Proy.", ".$Coordinador.", 2, 1 ,".$_SESSION["sesUnidadUsuario"].",getdate()  ) ";
						$cursorIn1 = mssql_query($sqlIn1);								
					}
				}
			}
//echo $sqlIn1."<br>".mssql_get_last_message()."<br>";						

	
			if($cursorIn1!="")
			{	
//	echo $cantOrd." ****** <br>";
				//ACTUALIZA EL REGISTRO DE LOS ORDENADORES A INACTIVO
				$sqlIn1 = " UPDATE EFOrdenadores_gasto SET ordenador_actual=0 where id_proyecto=".$Proy." ";
				$cursorIn1 = mssql_query($sqlIn1);			
				if($cursorIn1!="")
				{				

					//SI EL PROYECTO ESTA HOMOLOGADO EN LA EXPERIENCIA DE LA FIRMA, SE ELIMINAN LOS ORDENADORES DEL PROYECTO
					//EN EL PORTAL, PARA VOLVEL A ASOCIARLOS, DEACUERDO A LA CANTIDAD QUE SE HALLAN ASOCIADO
					if(($elProyecto!=""))
					{

						//Obtiene la secuencia de la solicitud de código
						$sqlIn1a="select distinct secuencia ";
						$sqlIn1a=$sqlIn1a." from GestiondeInformacionDigital.dbo.CargosSolCodigo ";
						$sqlIn1a=$sqlIn1a." where id_proyecto = " . $elProyecto;
						$cursorIn1a = mssql_query($sqlIn1a);
						if($regIn1a = mssql_fetch_array($cursorIn1a)){
							$secOrdenador = $regIn1a['secuencia'] ;
						}
						else {
							$secOrdenador = "1" ;
						}

						
						$sqlIn1 = " delete from  GestiondeInformacionDigital.dbo.OrdenadorGasto where id_proyecto=" . $elProyecto . " and secuencia = " . $secOrdenador . "";
						$cursorIn1 = mssql_query($sqlIn1);
		//echo $sqlIn1."<br>".mssql_get_last_message()."<br>";					
					}
				
					//ALMACENA LOS ORDENADORES
					for($i=1; $i<=$cantOrd;$i++)
					{
						$ord="Ordenador".$i;
						if($$ord!="")
						{
							if($cursorIn1!="")
							{					
								//ALMACENA LOS ORDENADORES
								$sqlIn1 = " INSERT INTO EFOrdenadores_gasto";
								$sqlIn1 = $sqlIn1 . "( id_ordenadores_gasto, id_proyecto, unidad_ordenador, ordenador_actual, usuarioGraba, fechaGraba ) ";
								$sqlIn1 = $sqlIn1 . " values ( (select isnull(MAX(id_ordenadores_gasto),0)+1 id   from EFOrdenadores_gasto WHERE id_proyecto=".$Proy." ), ".$Proy.", ".$$ord.", 1 ,".$_SESSION["sesUnidadUsuario"].",getdate()  ) ";
								$cursorIn1 = mssql_query($sqlIn1);											
//echo $sqlIn1."<br>".mssql_get_last_message()."<br>";			

								//ALMACENA EL ORDEADOR EN EL PORTAL
								if($elProyecto!="")
								{
									$sqlIn1 = " INSERT INTO GestiondeInformacionDigital.dbo.OrdenadorGasto ( secuencia, unidadOrdenador, id_proyecto ) ";
									$sqlIn1 = $sqlIn1 . " VALUES ( ";
									$sqlIn1 = $sqlIn1 . " " . $secOrdenador . ", ";
									$sqlIn1 = $sqlIn1 . " ".$$ord.", ";
									$sqlIn1 = $sqlIn1 . " " . $elProyecto . " ";
									$sqlIn1 = $sqlIn1 . " ) ";
									$textoQuery = $textoQuery . $sqlIn1 . "; ";
									//echo $sqlIn1 . "<br>";
									$cursorIn1 = mssql_query($sqlIn1);						
		
									//Ordenador del Gasto
									$sqlOr = " SELECT * FROM HojaDeTiempo.dbo.Usuarios WHERE unidad = " . $$ord;
									$cursorOr = mssql_query($sqlOr);
									if($regOr = mssql_fetch_array($cursorOr)){
										$ordenadorGasto = $regOr['unidad'] . " - " . ucwords(strtolower($regOr['apellidos'] . " " . $regOr['nombre']));
									}
									
									/*
									Envía los correos al director, al coordinador, a los ordenadores del gasto, a los programadores
									y a todo ese reguero de gente que está apuntada en la lista
									*/
							
									$elAsunto = "Portal Ingetec - Modificación Ordenadores de Gasto de Proyectos";
									$elCuerpo = "Se ha asignado un Ordenador de Gasto para el Proyecto <strong>" . $nombreProyecto . " - [" . $codProyecto . "." . $cargoProyecto . "]</strong>";
									$elCuerpo = $elCuerpo . "<br><br>";
									$elCuerpo = $elCuerpo . "Ordenador de Gasto Asignado : <strong>" . $ordenadorGasto . "</strong><br>";
									$laFirma = "Portal Ingetec - Contratos";
		
									enviarCorreo('carlosmaguirre@ingetec.com.co', $elAsunto, $elCuerpo, $laFirma);							
									

									//Al Director y al coordinador de Proyecto
									$sqlDir = " SELECT email FROM HojaDeTiempo.dbo.Usuarios WHERE unidad IN ( " . $Director . ", " . $Coordinador. " ) ";
									$cursorDir = mssql_query($sqlDir);
									while($regDir = mssql_fetch_array($cursorDir)){
										$aQuien = $regDir['email'] . "@ingetec.com.co";
										enviarCorreo($aQuien, $elAsunto, $elCuerpo, $laFirma);
									}
									
									//A los Ordenadores del Gasto
									$sqlOrd = " SELECT A.email
									FROM HojaDeTiempo.dbo.Usuarios A, GestiondeInformacionDigital.dbo.OrdenadorGasto B
									WHERE A.unidad = B.unidadOrdenador
									AND A.retirado IS NULL ";
									$sqlOrd = $sqlOrd . " AND B.id_proyecto = " . $elProyecto;
									$cursorOrd = mssql_query($sqlOrd);
									while($regOrd = mssql_fetch_array($cursorOrd)){
										$aQuien = $regOrd['email'] . "@ingetec.com.co";
										enviarCorreo($aQuien, $elAsunto, $elCuerpo, $laFirma); 
									}
									
									//A los programadores
									$sqlProg = " SELECT A.email
									FROM HojaDeTiempo.dbo.Usuarios A, HojaDeTiempo.dbo.Programadores B
									WHERE A.unidad = B.unidad
									AND A.retirado IS NULL ";
									$sqlProg = $sqlProg . " AND B.id_proyecto = " . $elProyecto;
									$cursorProg = mssql_query($sqlProg);
									while($regProg = mssql_fetch_array($cursorProg)){
										$aQuien = $regProg['email'] . "@ingetec.com.co";
										enviarCorreo($aQuien, $elAsunto, $elCuerpo, $laFirma); 
									}
									
									//Al reguero de gente
									$sqlNotif = " SELECT A.email
									FROM HojaDeTiempo.dbo.Usuarios A, GestiondeInformacionDigital.dbo.AutorizadosSolCodigo B
									WHERE A.unidad = B.unidad
									AND A.retirado IS NULL ";	
									$cursorNotif = mssql_query($sqlNotif);
									while($regNotif = mssql_fetch_array($cursorNotif)){
										$aQuien = $regNotif['email'] . "@ingetec.com.co";
										enviarCorreo($aQuien, $elAsunto, $elCuerpo, $laFirma);
									}
		
								}
						
							}
						}
					}
				}
			}
		}
	}
	
	if($acc==3)
	{
		/*
			///ACTUALIZA EL REGISTRO DEL DIRECTOR ACTUAL
			$sqlIn1 = " UPDATE EFDirector_Coordinador SET director_coordinador_actual=0 where id_proyecto=".$Proy." and tipo=1 ";
			$cursorIn1 = mssql_query($sqlIn1);						
			if($cursorIn1!="")
			{			
				///ACTUALIZA EL REGISTRO DEL CORRDINADOR AIGNADO ANTERIORMENTE A INACTIVO
				$sqlIn1 = " UPDATE EFDirector_Coordinador SET director_coordinador_actual=0 where id_proyecto=".$Proy." and tipo=2 ";
				$cursorIn1 = mssql_query($sqlIn1);						
				if($cursorIn1!="")
				{	
					//ACTUALIZA LOS ORDENADORES
					$sqlIn1 = " UPDATE EFOrdenadores_gasto SET ordenador_actual=0 where id_proyecto=".$Proy." ";
					$cursorIn1 = mssql_query($sqlIn1);					
				}
			}
			*/
	}
	
	
	if  (trim($cursorIn1) != "")  {
		mssql_query("commit transaction");		
	//	mssql_query("rollback  transaction");			
		echo ("<script>alert('Operaci\u00f3n realizada con exito');</script>"); 					
	} 
	else {
		mssql_query("rollback  transaction");		
		echo ("<script>alert('Error durante la operaci\u00f3n');</script>");
	}			

	echo ("<script>window.close(); MM_openBrWindow('infoG.php?Proy=".$Proy."&sec=1-1-2&imen=1','EF','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");			
}


//info proy
$sql_nom="select * from EFNombres_Proyectos where id_proyecto=".$Proy ." and nombre_actual=1";
$cur=mssql_query($sql_nom);
$datos_pro=mssql_fetch_array($cur);

//INFO USUARIOS
$sql_nom="select unidad,upper(nombre) nombre,upper(apellidos) apellidos from HojaDeTiempo.dbo.Usuarios where retirado is null and fechaRetiro is null order by apellidos";
$cur_USU=mssql_query($sql_nom);

//CONSULTA EL DIRECTOR Y CORRDINADOR DEL PROYECTO ACTUALES
$sql_dir_or="
select * from (
	select unidad_director_coordinador,tipo from  EFDirector_Coordinador where tipo=1 and director_coordinador_actual=1 and id_proyecto=".$Proy ."
	union
	select unidad_director_coordinador,tipo from  EFDirector_Coordinador where tipo=2 and director_coordinador_actual=1 and id_proyecto=".$Proy ."
)Z order by tipo";
$cursorIn1 = mssql_query($sql_dir_or);						
$datos_esp=mssql_fetch_array($cursorIn1);
$uni_dir=$datos_esp["unidad_director_coordinador"];

$datos_esp=mssql_fetch_array($cursorIn1);
$uni_coor=$datos_esp["unidad_director_coordinador"];

$dis="";
if($acc==3)
	$dis="disabled";

?>
                      
<script>
	function addFila()
	{ 	
		var opciones= new Array();
		var cantOr=parseInt(document.getElementById("cantOrd").value);
		cantOr=cantOr+1;		
<?PHP
		$cur_USU=mssql_query($sql_nom);
?>			
		opciones[0]='<option value="">Seleccione el Ordenador de Gasto</option>';
<?php			
		$z=1;
		//se carga la lISTA CON LAS PERSONAS ACTIVAS EN EL PORTAL
		while($datos_usu=mssql_fetch_array($cur_USU))
		{
?>             
			opciones[<?=$z ?>]='<option value="<?=$datos_usu["unidad"] ?>"><?=$datos_usu["apellidos"]." ".$datos_usu["nombre"]." [".$datos_usu["unidad"]."]" ?></option>';
<?php
			$z++;
				}
			?>	
		   var sel='', divI='', divF='', total='';
		   divI='<div class="main row form-group" id="divOrdenador2'+cantOr+'" name="divOrdenador2'+cantOr+'"> <div class="col-sm-11 ">';
		   divF='</div><div class="col-sm-1 "><i class="glyphicon glyphicon-minus-sign btn btn-danger" onClick="delFila('+cantOr+')"></i></div></div>    ';
		   sel+='<select name="Ordenador'+cantOr+'" id="Ordenador'+cantOr+'" class="form-control">';
		   for(var u=0;u<opciones.length;u++ )		
		   {
			   sel+=opciones[u];
		   }
		   sel+='</select>';
		   total=divI+sel+divF+"";
		  document.getElementById("divOrdenador").innerHTML+=total;
		  document.getElementById("cantOrd").value=cantOr;
	}
	
	function delFila(fila)
	{
		 document.getElementById("divOrdenador2"+fila).remove();		 		
	}
	
	function valida()
	{

		var campos_sele = ["Director"];
		var error=0;		
		// campos_sele FUNCION PARA LA VALIDACION DE CAMPOS DE TEXTO Y SELECT
		error=valida_campos (campos_sele,1);		
		
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
          <div class="form-group" id="divDirector">
            <label for="">Director</label>
                <select name="Director" id="Director" class="form-control" <?=$dis ?>>
                        <option value="">Seleccione el Director</option>                
<?PHP
					$cur_USU=mssql_query($sql_nom);
					while($datos_usu=mssql_fetch_array($cur_USU))
					{
						$sel="";
						if($uni_dir==$datos_usu["unidad"])
							$sel="selected";
?>             
                        <option value="<?=$datos_usu["unidad"] ?>" <?=$sel ?> ><?=$datos_usu["apellidos"]." ".$datos_usu["nombre"]." [".$datos_usu["unidad"]."]" ?></option>
<?php
					}
?>
                </select>
             <span id="helpDirector" class="help-block" style="display:none;" >El director del proyecto es obligatorio.</span>    
			<input  type="hidden" id="DirectorAnt" name="DirectorAnt" value="<?=$uni_dir ?>">             
          </div>
          
          <div class="form-group" id="divCoordinador">
          	<label for="">Coordinador</label>
            	<select name="Coordinador" id="Coordinador" class="form-control" <?=$dis ?>>
          			<option value="">Seleccione el Coordinador</option>
<?PHP
					$cur_USU=mssql_query($sql_nom);
					while($datos_usu=mssql_fetch_array($cur_USU))
					{
						$sel="";
						if($uni_coor==$datos_usu["unidad"])
							$sel="selected";						
?>             
                        <option value="<?=$datos_usu["unidad"] ?>" <?=$sel ?> ><?=$datos_usu["apellidos"]." ".$datos_usu["nombre"]." [".$datos_usu["unidad"]."]" ?></option>
<?php
					}
?>
          		</select>     

			<input  type="hidden" id="CoordinadorAnt" name="CoordinadorAnt" value="<?=$uni_coor ?>">                              
           </div>

          <div class="form-group" id="">
          	<label for="">Ordenadores de gasto</label>
           </div>           
           
          <div class="form-group" id="divOrdenador">
<?php
		//SI ES LA PRIMERA VEZ QUE SE CARGA LA PAGINA, SE CONSULTAN LOS ORDENADORES DE GASTO Y SE GENERAN LAS LISTAS DESPLEGABLES
		if($recarga=="")
		{
			//CONSULTA LOS ORDENADORES DE GASTO ACTUALES
			$sql_ordena="select * from EFOrdenadores_gasto where id_proyecto=".$Proy ." and ordenador_actual=1";
			$cur_ordena=mssql_query($sql_ordena);
			
			$z=1;			
			while($datos_ordena=mssql_fetch_array($cur_ordena))			
			{

				$cur_USU=mssql_query($sql_nom);            
?>
               <div class="main row form-group" id="divOrdenador2<?=$z ?>" name="divOrdenador2<?=$z ?>"> 
                   <div class="col-sm-11 ">
                        <select name="Ordenador<?=$z ?>" id="Ordenador<?=$z ?>" class="form-control" <?=$dis ?>>
			<?php
                            //se carga la lISTA CON LAS PERSONAS ACTIVAS EN EL PORTAL
                            while($datos_usu=mssql_fetch_array($cur_USU))
                            {
                                $sel="";
                                if($datos_ordena["unidad_ordenador"]==$datos_usu["unidad"])
                                    $sel="selected";					
            ?>
                                <option value="<?=$datos_usu["unidad"] ?>" <?=$sel ?>><?=$datos_usu["apellidos"]." ".$datos_usu["nombre"]." [".$datos_usu["unidad"]."]" ?></option>'
            <?PHP					
                            }				
            ?>
                       </select>                   
                   </div>
             <?php
					 if($acc==2)
					 {
			 ?>      
                       <div class="col-sm-1 ">
                            <i class="glyphicon glyphicon-minus-sign btn btn-danger" onClick="delFila(<?=$z ?>)"></i>
                       </div>
<?php
					 }
?>                   
               </div>
<?php				
				$z++;
			}
		}
			
?>
           </div>
 <?php
         if($acc==2)
         {
 ?>                 
            <div class="form-group" style="text-align:right;">
	            <i class="glyphicon glyphicon-plus-sign btn btn-success" onClick="addFila();"></i>
            </div>
<?php
		 }
?>                                
           <div style="text-align:right" >
   <?php
   		$mens_b="";
	   if($acc==2)
	   		$mens_b="Actualizar";	   		
	   if($acc==3)
	   		$mens_b="Eliminar";	   
	   
   ?>
           
              <button type="button" class="btn btn-primary" onClick="valida()" ><?=$mens_b ?></button>  
               <input name="recarga" type="hidden" id="recarga" value="1">
               <input type="hidden" id="cantOrd" name="cantOrd" value="<?=mssql_num_rows($cur_ordena) ?>">
           </div>
    </div>
</form>     

<?php
	include("inferior.php"); 
?>