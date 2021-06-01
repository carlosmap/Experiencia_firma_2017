<?php
	
	//$imen =PERMITE IDENTIFICAR DESDE QUE INTEM PRINCIPAL DEL MENU, SE ESTA HACIENDO EL LLAMADO. ESTO CON EL FIN DE MOSTRAR EL COLOR ROJO EN LA SECCION
	//SOBRE LA CUAL EL USUARIO HA DADO CLIC
	
	//SE CONSULTA EL TIPO DE PROYECTO, PARA MOSTRAR ALGUNAS DE LAS SECCIONES DEL MENU DE ACUERDO AL TIPO DE PROYECTO
	$sql_tipo_proy="select id_tipo_proyecto from EFProyectos where id_proyecto=".$Proy." ";
	$dato_tipo_proy=mssql_fetch_array(mssql_query($sql_tipo_proy));
	$tipo_proyecto=$dato_tipo_proy["id_tipo_proyecto"];
	?>
    	
	<div class="container" >  
		<nav class="navbar navbar-inverse" style="background-color:#0B426B; ">
		  <div class="container-fluid">
		  
			<ul class="nav navbar-nav">
			  <li class="dropdown" ><a href="index.php?Proyecto=<?=$Proy ?>" style="color:white;"  ><i class="glyphicon glyphicon-home"></i>  Inicio</a></li>

				<li class="dropdown"><a href="#" style="background-color:<?PHP echo (($imen==1)||($imen==""))?  "#E5321E" : "#0B426B";  ?>; color:white;">Experiencia de la firma<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                         <li class="dropdown-submenu">
                         	<a href="#" class="test" >Definici&oacute;n del proyecto</a>
                            <ul class="dropdown-menu">
                                <li><a href="infoG.php?sec=1-1-1&Proy=<?=$Proy ?>&imen=1" >Informaci&oacute;n general</a></li>
                                <li><a href="infoG.php?sec=1-1-2&Proy=<?=$Proy ?>&imen=1" >Encargados</a></li>
                                <li><a href="infoG.php?sec=1-1-3&Proy=<?=$Proy ?>&imen=1" >Certificados</a></li>
                                <li><a href="infoG.php?sec=1-1-4&Proy=<?=$Proy ?>&imen=1" >Soportes</a></li> 
                                <li><a href="infoG.php?sec=1-1-5&Proy=<?=$Proy ?>&imen=1" >Ubicaci&oacute;n</a></li>
<?php
								//LAS SUSPENCIONES Y REANUDACIONES SOLO APLICAN A LAOS PROYECTOS FACTURABLES
								if($tipo_proyecto==4)
								{
?>                                
                                    <li><a href="infoG.php?sec=1-1-6&Proy=<?=$Proy ?>&imen=1" >Suspensiones y Reanudaciones</a></li>
                                    <li><a href="infoG.php?sec=1-1-7&Proy=<?=$Proy ?>&imen=1" >Liquidaci&oacute;n</a></li>
<?php
								}
?>                                
                                                                                                                                
                            </ul>
                         </li>                
                         
                         <li class="dropdown-submenu" ><a href="#">Costos y fechas</a>
                            <ul class="dropdown-menu">
                                <li><a href="costosF.php?sec=1-2-1&Proy=<?=$Proy ?>&imen=1">Valores del proyecto</a></li>
                                <li><a href="costosF.php?sec=1-2-2&Proy=<?=$Proy ?>&imen=1">Prorrogas</a></li>
                                <li><a href="costosF.php?sec=1-2-3&Proy=<?=$Proy ?>&imen=1">Adicionales</a></li>
                                <li><a href="costosF.php?sec=1-2-4&Proy=<?=$Proy ?>&imen=1">Valores facturados</a></li>
                            </ul>                         
                         </li>                                                              
                    </ul>
			  </li>
			  <li class="dropdown">
              	<a href="infoC.php?sec=3&Proy=<?=$Proy ?>&imen=2" style="background-color:<?PHP echo (($imen==2))? "#E5321E" : "#0B426B";  ?>; color:white;"   >Informaci&oacute;n consolidada</a>

              </li>
			</ul>

		  </div>
		</nav>
	</div>    
<?php
?>