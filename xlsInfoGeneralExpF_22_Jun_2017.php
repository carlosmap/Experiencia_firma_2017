<?php

	session_start();
	include("conectaBD.php"); 	
	$conexion = conectar();
	$mes = array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );	
	$fecha = date('d').'_'.$mes[date('n')].'_'.date('Y');	
	/** PHPExcel_IOFactory */
	/*
	require_once 'phpExcel/PHPExcel.php';
	
	$objPHPExcel = new PHPExcel();
	*/

	echo "<head>";
	header("Content-Type: application/ms-excel");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=Experiencia_Firma_" . $fecha . ".xls");
	echo "</head>";	

?>


<html>
<head>
<title>:::  :::</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <style type="text/css" >
		br
		{
			mso-data-placement:same-cell;
		}
    </style>
</head>

<body  leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" class="fondo" >
<table width="100%"  border="1" cellspacing="1">
<tr bordercolor="#000000">
 <!--     <td align="center" valign="middle"  width="145" height="49"><div align="center"><img src="img/Logo-INGETEC.jpg" width="140" height="49" align="absmiddle"></div></td>
 -->
      <td colspan="46" align="center" valign="middle" class="TituloFormato Estilo4"> <b> <h3>Experiencia de la firma <?=(date('d').' '.$mes[date('n')].' '.date('Y')) ?></h3> </b>
    
    </td>

</tr>

<tr align="center" valign="middle">
  <td colspan="18" class="TituloTabla2" ><strong>Informaci&oacute;n general</strong></td>
  <td colspan="3" class="TituloTabla2" ><strong>Ubicaci&oacute;n</strong></td>
  <td colspan="1" class="TituloTabla2" ><strong>Encargados</strong></td>
  <td colspan="5" class="TituloTabla2" ><strong>Consorcio</strong></td>
  <td colspan="6" class="TituloTabla2" ><strong>Adiciones</strong></td>
  <td colspan="6" class="TituloTabla2"><strong>Prorrogas</strong></td>
  <td colspan="4" class="TituloTabla2"><strong>Valores Facturados</strong></td>
  <td colspan="2" class="TituloTabla2"><strong>Certificados</strong></td>
  <td class="TituloTabla2"><strong>Soporte</strong></td>
</tr>
<tr align="center" valign="middle">
  <th class="TituloTabla2" >Certificado SEVEN</th>
<!--
	INFORMACION GENERAL
-->
  <td class="TituloTabla2" ><strong>C&oacute;digo Antiguo</strong></td>
  <td class="TituloTabla2" ><strong>Código SEVEN</strong></td>
  <td class="TituloTabla2" ><strong>Cliente</strong></td>
  <td class="TituloTabla2" ><strong>Nombre Corto de Proyecto</strong></td>
  <td class="TituloTabla2" ><strong>Nombre Largo de Proyecto</strong></td>
  <td class="TituloTabla2" ><strong># Contrato</strong></td>
  <td class="TituloTabla2" ><strong>Fecha Inicial</strong></td>  
  <td class="TituloTabla2" ><strong>Fecha Final</strong></td>
  <td class="TituloTabla2" ><strong>Estado</strong></td>
  <td class="TituloTabla2" ><strong>Moneda</strong></td>
  <td class="TituloTabla2" ><strong>TRM</strong></td>
  <td class="TituloTabla2" ><strong>Valor Contrato</strong></td>
  <td class="TituloTabla2" ><strong>Forma de Ejecución</strong></td>
  <td class="TituloTabla2" ><strong>Empresa encargada</strong></td>
  <th class="TituloTabla2" >Tipo de facturaci&oacute;n</th>
  <td class="TituloTabla2" ><strong>Especialidad</strong></td>
  <td class="TituloTabla2" ><strong>Clase</strong></td>

<!--
	UBICACION
-->		
  <td class="TituloTabla2" ><strong>Pais</strong></td>
  <td class="TituloTabla2" ><strong>Municipio</strong></td>
  <td class="TituloTabla2" ><strong>Departamento</strong></td>
  
<!--
	ENCARGADOS
<td class="TituloTabla2" ><strong>Tipo</strong></td>    
-->	
  
  <td class="TituloTabla2" ><strong>Nombre</strong></td>
  <th class="TituloTabla2" >Consorcio</th>
<!--
	CONSORCIOS
-->		
  <td class="TituloTabla2" ><strong>Empresa</strong></td>          
  <td class="TituloTabla2" ><strong>Empresa Lider</strong></td>          
  <td class="TituloTabla2" ><strong>% Participación	</strong></td>          
  <td class="TituloTabla2" ><strong>Valor % Participación</strong></td>          
  
<!--
	ADICION
-->		  
  <td class="TituloTabla2" ><strong>Tipo de Adición</strong></td>          
  <td class="TituloTabla2"><strong>Valor</strong></td>
  <td class="TituloTabla2"><strong>Fecha</strong></td>
  <td class="TituloTabla2"><strong>Documento Soporte</strong></td>
  <td class="TituloTabla2"><strong>Observaciones</strong></td>
  <td class="TituloTabla2"><strong>Usuario Registra</strong></td>

<!--
	PORROGA
-->		  
  <td class="TituloTabla2"><strong>Tipo de prorroga</strong></td>
  <td class="TituloTabla2"><strong>Fecha de Inicio</strong></td>
  <td class="TituloTabla2"><strong>Fecha de Finalización</strong></td>
  <td class="TituloTabla2"><strong>Documento de Soporte</strong></td>
  <td class="TituloTabla2"><strong>Valor</strong></td>
  <td class="TituloTabla2"><strong>Observaciones</strong></td>
  
<!---				
	VALORES FACTURADOS
  -->
  
  <td class="TituloTabla2"><strong>Fecha Inicio</strong></td>
  <td class="TituloTabla2"><strong>Fecha Finalización</strong></td>
  <td class="TituloTabla2"><strong>Valor Facturado</strong></td>  
  <td class="TituloTabla2"><strong>Valor Facturado % Participación</strong></td>
  		
<!---				
	CEERTIFICADOS
  -->
  
  <td class="TituloTabla2"><strong>Fecha de Solicitud</strong></td>
  <td class="TituloTabla2"><strong>Fecha de Recepción</strong></td>
  <td class="TituloTabla2"><strong>Soporte</strong></td>
        
</tr>
<!--
	INFORMACION GENERAL
-->
<?php


	$cur_proy=mssql_query("select  EFProyectos.*, EFMonedas.moneda , EFValores_Contrato.valor_contrato, EFTipos_Ejecucion.ejecucion , EFEmpresas.empresa,EFFormas_pago.forma_pago
	, year(fecha_solicitud_certificado) ano_cer, month(fecha_solicitud_certificado) mes_cer ,day(fecha_solicitud_certificado) dia_cer
	, year(fecha_recepcion_certificado) ano_rec, month(fecha_recepcion_certificado) mes_rec ,day(fecha_recepcion_certificado) dia_rec 
	from EFProyectos  
	inner join EFValores_Contrato on EFValores_Contrato.id_proyecto=EFProyectos.id_proyecto and EFValores_Contrato.id_valores_proyecto=1
	inner join EFMonedas on EFMonedas.id_moneda=EFProyectos.id_moneda
	inner join EFTipos_Ejecucion on EFTipos_Ejecucion.id_tipo_ejecucion=EFProyectos.id_tipo_ejecucion
	inner join EFEmpresas on EFEmpresas.id_empresa=EFProyectos.id_empresa and EFEmpresas.tipo=1
	inner join EFFormas_pago on EFFormas_pago.id_forma_pago=EFProyectos.id_forma_pago
	");
	while($dato_proy=mssql_fetch_array($cur_proy))
	{
		
		$cur_cliente=mssql_query("select cliente from EFClientes_Proyectos 
									inner join EFClientes on EFClientes.id_cliente=EFClientes_Proyectos.id_cliente
									 where EFClientes_Proyectos.id_proyecto=".$dato_proy["id_proyecto"]." and EFClientes_Proyectos.cliente_actual=1 ");

		$cur_nombre=mssql_query("select nombre_corto_proyecto,nombre_largo_proyecto from  EFNombres_Proyectos where id_proyecto=".$dato_proy["id_proyecto"]." and nombre_actual=1 ");
		$dato_nombre=mssql_fetch_array($cur_nombre);
		
		$cur_fehcas=mssql_query("select year(fecha_inicio_proyecto) ano_fi, month(fecha_inicio_proyecto) mes_fi ,day(fecha_inicio_proyecto) dia_fi,
		year(fecha_final_proyecto) ano_ff, month(fecha_final_proyecto) mes_ff, day(fecha_final_proyecto) dia_ff
		  from  EFFechas_Proyecto where id_proyecto=".$dato_proy["id_proyecto"]." and fechas_actuales=1");
		$dato_fechas=mssql_fetch_array($cur_fehcas);	
		
		$cur_estados=mssql_query("
									select estado_proyecto from EFEstados_Proy_Proyectos
									inner join EFEstados_Proy on EFEstados_Proy.id_estado_proy=EFEstados_Proy_Proyectos.id_estado_proy
									 where EFEstados_Proy_Proyectos.id_proyecto=".$dato_proy["id_proyecto"]." and EFEstados_Proy_Proyectos.estado_actual=1 ");
		$dato_estados=mssql_fetch_array($cur_estados);				
?>
<tr>
  <td><?="'".( (string) $dato_proy["certificado_SEVEN"])."" ?></td>
	<td><?
		if($dato_proy["codigo"]!="")
			echo "'".( (string) $dato_proy["codigo"])."" ?>
    </td>
	<td><?="'".( (string) $dato_proy["codigo_SEVEN"])."" ?></td>
	<td><?
		while($dato_cliente=mssql_fetch_array($cur_cliente) )
    		echo $dato_cliente["cliente"]."<br>";
	
	 ?></td>    
	<td><?=$dato_nombre["nombre_corto_proyecto"] ?></td>
	<td><?=$dato_nombre["nombre_largo_proyecto"] ?></td>
	<td><?=$dato_proy["numero_contrato"] ?></td>
	<td><?=$dato_fechas["dia_fi"]." ".$mes[$dato_fechas["mes_fi"]]." ".$dato_fechas["ano_fi"] ?></td>
	<td><?=$dato_fechas["dia_ff"]." ".$mes[$dato_fechas["mes_ff"]]." ".$dato_fechas["ano_ff"] ?></td>
	<td><?=$dato_estados["estado_proyecto"] ?></td>
	<td><?=$dato_proy["moneda"] ?></td>
	<td><?=$dato_proy["TRM"] ?></td>
	<td><?=number_format ($dato_proy["valor_contrato"], 0, ',', '.'); ?></td>
	<td><?=$dato_proy["ejecucion"] ?></td>
	<td><?=$dato_proy["empresa"] ?></td>
	<td><?=$dato_proy["forma_pago"] ?></td>
	<td>         	   
		 <?php
		 
		 		//ESPECIALIDADES
				$cur_especialidades=mssql_query("
							select especialidad from EFEspecialidades_Proyectos
							inner join EFEspecialidades on EFEspecialidades.id_especialidad=EFEspecialidades_Proyectos.id_especialidad
							 where EFEspecialidades_Proyectos.id_proyecto=".$dato_proy["id_proyecto"]."   ");
				$espe="";
				$cont=0;
				while($dato_especialidades=mssql_fetch_array($cur_especialidades))
				{
				//	$espe.=$dato_especialidades["especialidad"]."";
				//	$dato_especialidades["especialidad"]."\n";				 
//					echo $dato_especialidades["especialidad"]."\r";				 				
//				echo "<tr> <td>".$dato_especialidades["especialidad"]."\r</td></tr>";
//				$espe.= "<pre>".$dato_especialidades["especialidad"]."</pre>";				 						
/*
					if($cont==0)
						$espe.= "<pre>".$dato_especialidades["especialidad"]."</pre>";				 						
					else
						$espe.= "<pre> ".$dato_especialidades["especialidad"]."</pre>";				 						
*/	
/*					
					if($cont==0)
						$espe= sprintf("%s %s",$espe, $dato_especialidades["especialidad"]);
					else
						$espe= sprintf(",%s %s",$espe, $dato_especialidades["especialidad"]);
*/						
					if($cont==0)
						$espe.= "".$dato_especialidades["especialidad"].""; //.chr(13);				 						
					else
						$espe.= "".$dato_especialidades["especialidad"]."<br>"; //.chr(13);		
							
					$cont=1;	
				}
				echo $espe;
				
		 ?>
     </td>
	<td>

		 <?php
		 		//CLASES
				$cur_especialidades=mssql_query("
							select clase from EFClases_Proyectos
							inner join EFClases on EFClases.id_clase=EFClases_Proyectos.id_clase
							 where EFClases_Proyectos.id_proyecto=".$dato_proy["id_proyecto"]."   ");
				$info="";
				$cont=0;
				while($dato_especialidades=mssql_fetch_array($cur_especialidades))
				{
				//	$espe.=$dato_especialidades["especialidad"]."";
				//	$dato_especialidades["especialidad"]."\n";				 
//					echo $dato_especialidades["especialidad"]."\r";				 				
//				echo "<tr> <td>".$dato_especialidades["especialidad"]."\r</td></tr>";
					if($cont==0)
						$info.= "".$dato_especialidades["clase"]."";				 						
					else
						$info.= "".$dato_especialidades["clase"]."<br>";
						
					$cont=1;	
				}
				echo $info;
				
		 ?>    
    
    </td>
<!--
	UBICACION
-->		    
<?PHP
				$cur_UBIC=mssql_query("SELECT EFPaises.pais , EFDepartamentos.departamento, EFMunicipios.municipio FROM  EFUbicacion_Proyectos 
				inner join EFPaises on  EFPaises.id_pais=EFUbicacion_Proyectos.id_pais 
				inner join EFDepartamentos on  EFDepartamentos.id_pais=EFUbicacion_Proyectos.id_pais and EFDepartamentos.id_departamento=EFUbicacion_Proyectos.id_departamento
				inner join EFMunicipios on  EFMunicipios.id_pais=EFUbicacion_Proyectos.id_pais and EFMunicipios.id_departamento=EFUbicacion_Proyectos.id_departamento
				 and EFMunicipios.id_municipio=EFUbicacion_Proyectos.id_municipio
				WHERE EFUbicacion_Proyectos.id_proyecto=".$dato_proy["id_proyecto"]);

				$info_pais="";
				$info_municipio="";				
				$info_departamento="";				
				$cont=0;				
				while($dato_UBIC=mssql_fetch_array($cur_UBIC))
				{
					if($cont==0)
					{
						$info_pais.= "".$dato_UBIC["pais"]."<br>";	
						$info_departamento.= "".$dato_UBIC["departamento"]."<br>";	
						$info_municipio.= "".$dato_UBIC["municipio"]."<br>";	
					}
					else
					{
						$info_pais.= "".$dato_UBIC["pais"]."<br>";	
						$info_departamento.= "".$dato_UBIC["departamento"]."<br>";	
						$info_municipio.= "".$dato_UBIC["municipio"]."<br>";	
					}
?>
<?php					
				}
?>
                <td><?=$info_pais ?></td>
                <td><?=$info_departamento ?></td>
                <td><?=$info_municipio ?></td>
<!--
	ENCARGADOS
-->	    
<?php
			//CONSULTA EL DIRECTOR Y CORRDINADOR DEL PROYECTO ACTUALES
			$sql_dir_or="
			select upper(U.apellidos+' '+U.nombre) nombre, U.unidad, Z.tipo  from (
				select unidad_director_coordinador,tipo from  EFDirector_Coordinador where tipo=1 and director_coordinador_actual=1 and id_proyecto=".$dato_proy["id_proyecto"]."
				union
				select unidad_director_coordinador,tipo from  EFDirector_Coordinador where tipo=2 and director_coordinador_actual=1 and id_proyecto=".$dato_proy["id_proyecto"]."
			) Z
			inner join HojaDeTiempo.dbo.Usuarios U on U.unidad=Z.unidad_director_coordinador
			order by tipo";
			$cursorIn1 = mssql_query($sql_dir_or);						
			$datos_esp=mssql_fetch_array($cursorIn1	);
			
			$cant_reg=0;
			$cant_reg=mssql_num_rows($cursorIn1);
			
			//CONSULTA LOS ORDENADORES DE GASTO ACTUALES
			$sql_dir_or2="select  upper(U.apellidos+' '+U.nombre) nombre, U.unidad from EFOrdenadores_gasto 
			inner join HojaDeTiempo.dbo.Usuarios U on U.unidad=EFOrdenadores_gasto.unidad_ordenador
			WHERE id_proyecto=".$dato_proy["id_proyecto"]." and ordenador_actual=1
			order by nombre ";					
			$cursorInOr = mssql_query($sql_dir_or2);	
?>
            <td>
			<?php 
				if($datos_esp["unidad"]!="")
					echo "<strong>D:</strong> [".$datos_esp["unidad"]."] ".$datos_esp["nombre"]."<br>"; 
				$datos_esp=mssql_fetch_array($cursorIn1);	

				if($datos_esp["unidad"]!="")
					echo " <strong>C:</strong> [".$datos_esp["unidad"]."] ".$datos_esp["nombre"]."<br>"		;	
					
			while($datos_esp=mssql_fetch_array($cursorInOr))
			{
				echo " <strong>O:</strong> [".$datos_esp["unidad"]."] ".$datos_esp["nombre"]."<br>";
			}
			?></td>
            
     
<!--
       <td></td>
	CONSORCIOS
-->		   
<?php
			$info_empresa='';
			$info_empresa_lider='';			
			$participacion='';			
			$valor_partic='';		
			$cont=0;	
			//SI LA FORMA DE EJECUCION ES EN CONSORCIO O UNION TEMPORAL
			if(($dato_proy["id_tipo_ejecucion"]==2) || ($dato_proy["id_tipo_ejecucion"]==3) )
			{

				$sql_nom_fir="select * from EFConsorcios where id_proyecto=".$dato_proy["id_proyecto"]." and nombre_actual_consorcio=1";
				$cur_nom_fir=mssql_query($sql_nom_fir);
				$datos_nom_fir=mssql_fetch_array($cur_nom_fir);


				$sql_empr_consr="select EFEmpresas.id_empresa,EFEmpresas.empresa, EFConsorcios_Empresas.empresa_lider,EFConsorcios_Empresas.porcentaje_participacion,
				 EFConsorcios_Empresas.valor_contrato_porcentaje
				 from EFConsorcios_Empresas
				inner join EFEmpresas on EFEmpresas.id_empresa=EFConsorcios_Empresas.id_empresa
				where EFConsorcios_Empresas.id_proyecto=".$dato_proy["id_proyecto"]." and EFConsorcios_Empresas.valores_actuales=1";
				$cur_empr_consr=mssql_query($sql_empr_consr);
				while($datos_empre_cons=mssql_fetch_array($cur_empr_consr))
				{
					if($cont==0)
					{					
						$info_empresa.=$datos_empre_cons["empresa"]."<br>"	;
						
						if($datos_empre_cons["empresa_lider"]==1)
							$info_empresa_lider=$datos_empre_cons["empresa"];
							
						$participacion.=$datos_empre_cons["porcentaje_participacion"]."<br>"	;
						$valor_partic.=number_format ($datos_empre_cons["valor_contrato_porcentaje"], 0, ',', '.')."<br>"	; ;																		
					}
					else
					{
						$info_empresa.=$datos_empre_cons["empresa"]."<br>";

						if($datos_empre_cons["empresa_lider"]==1)
								$info_empresa_lider=$datos_empre_cons["empresa"];
								
							$participacion.=$datos_empre_cons["porcentaje_participacion"]."<br>";
							$valor_partic.=number_format ($datos_empre_cons["valor_contrato_porcentaje"], 0, ',', '.')."<br>"; ;																		
					}
					$cont=1;
				}
			}
			
			//SI LA FORMA DE EJECUCION ES INDIVIDUAL
			if($dato_proy["id_tipo_ejecucion"]==1)
			{
				
				$sql_empre="select EFEmpresas.empresa,EFConsorcios_Empresas.empresa_lider,EFConsorcios_Empresas.porcentaje_participacion, EFConsorcios_Empresas.valor_contrato_porcentaje, EFConsorcios_Empresas.valores_actuales from EFConsorcios_Empresas
				INNER JOIN EFEmpresas on EFEmpresas.id_empresa=EFConsorcios_Empresas.id_empresa
				where EFConsorcios_Empresas.id_proyecto=".$dato_proy["id_proyecto"]." and EFConsorcios_Empresas.valores_actuales=1	";
				$cur_empre= mssql_query($sql_empre);	
			
				$datos_empre_cons=mssql_fetch_array($cur_empre);		
				
				
				$info_empresa.=$datos_empre_cons["empresa"];
				
				if($datos_empre_cons["empresa_lider"]==1)
					$info_empresa_lider=$datos_empre_cons["empresa"];
					
				$participacion.=$datos_empre_cons["porcentaje_participacion"];
				if($participacion=='100.00')
					$participacion=100;
				$valor_partic.=number_format ($datos_empre_cons["valor_contrato_porcentaje"], 0, ',', '.'); ;																		
								
			}
?> 
	<td><?=$datos_nom_fir["nombre_consorcio"] ?></td>
	<td><?=$info_empresa ?></td>
	<td><?=$info_empresa_lider ?></td>
	<td><?=$participacion ?></td>
	<td><?=$valor_partic ?></td>
<!--
	ADICION
-->		   

<?php
		$tipo_adi="";
		$valor="";
		$fech="";
		$documento="";
		$observaciones="";
		$usuario="";
		//CONSULTA LAS ADICIONES DEL PROYCTO
		$sql_adicionales="
			select (U.apellidos+' '+U.nombre) nombreU,U.unidad, EFAdicionales.*, EFItems_Prorrogas_Adicionales.prorroga_adicion
			, EFDocumentos_Soporte.documento_soporte,
			year(fecha_adicion) ano_a, month(fecha_adicion) mes_a ,day(fecha_adicion) dia_a
			 from EFAdicionales  
			inner join EFItems_Prorrogas_Adicionales on EFItems_Prorrogas_Adicionales.id_item_prorroga_adicion=EFAdicionales.id_item_prorroga_adicion
			inner join EFDocumentos_Soporte on EFDocumentos_Soporte.id_documento_soporte=EFAdicionales.id_documento_soporte
			inner join HojaDeTiempo.dbo.Usuarios U on U.unidad=EFAdicionales.usuarioGraba				
			where id_proyecto=".$dato_proy["id_proyecto"]."
			order by EFAdicionales.id_adicional	";
		
		$cur_adicionales=mssql_query($sql_adicionales);
		$cont=1;
//echo $sql_adicionales."<br>";
		while($datos_proffo=mssql_fetch_array($cur_adicionales))
		{	
		/*			
			$tipo_adi.=$cont.". ".$datos_proffo["prorroga_adicion"];
			$valor.=$cont.". ".number_format ($datos_proffo["valor_adicion"], 0, ',', '.');
			$fech.=$cont.". ".$datos_proffo["dia_a"]." ".$mes[$datos_proffo["mes_a"]]." ".$datos_proffo["ano_a"];
			$documento.=$cont.". ".$datos_proffo["documento_soporte"];
			$observaciones.=$cont.". ".$datos_proffo["observaciones"];
			$usuario.=$cont.". "."[".$datos_proffo["unidad"]."] ".$datos_proffo["nombreU"];							
			
			$cont++;
			*/
			$tipo_adi.=$datos_proffo["prorroga_adicion"]."<br>";
			$valor.=number_format ($datos_proffo["valor_adicion"], 0, ',', '.')."<br>";
			$fech.=$datos_proffo["dia_a"]." ".$mes[$datos_proffo["mes_a"]]." ".$datos_proffo["ano_a"]."<br>";
			$documento.= $datos_proffo["documento_soporte"]."<br>";
			$observaciones.= $datos_proffo["observaciones"]."<br>";
			$usuario.="[".$datos_proffo["unidad"]."] ".$datos_proffo["nombreU"]."<br>";							
			
		}
?> 
	<td><?=$tipo_adi ?></td>
	<td><?=$valor ?></td>
	<td><?=$fech ?></td>
	<td><?=$documento ?></td>
	<td><?=$observaciones ?></td>
	<td><?=$usuario ?></td>
<!--
	PORROGA
-->	    
<?php

	$tipo_prorroga="";
	$valor="";
	$fech_i="";
	$fech_f="";	
	$documento="";
	$observaciones="";
	$usuario="";
		
	//CONSULTA LAS PRORROGAS DEL PROYCTO
	$sql_proffo="select (U.apellidos+' '+U.nombre) nombreU,U.unidad, EFProrrogas.*, EFItems_Prorrogas_Adicionales.prorroga_adicion, EFDocumentos_Soporte.documento_soporte 
	,year(fecha_inicio) ano_i, month(fecha_inicio) mes_i ,day(fecha_inicio) dia_i
	,year(fecha_final) ano_f, month(fecha_final) mes_f ,day(fecha_final) dia_f 
	from EFProrrogas  
	inner join EFItems_Prorrogas_Adicionales on EFItems_Prorrogas_Adicionales.id_item_prorroga_adicion=EFProrrogas.id_item_prorroga_adicion
	inner join EFDocumentos_Soporte on EFDocumentos_Soporte.id_documento_soporte=EFProrrogas.id_documento_soporte
	inner join HojaDeTiempo.dbo.Usuarios U on U.unidad=EFProrrogas.usuarioGraba				
	where id_proyecto=".$dato_proy["id_proyecto"]."
	order by EFProrrogas.id_prorroga ";
	
	$cur_proffo=mssql_query($sql_proffo);
	$cont=1;
	while($datos_proffo=mssql_fetch_array($cur_proffo))
	{
/*		
			$tipo_prorroga.=$cont.". ".$datos_proffo["prorroga_adicion"];
			$valor.=$cont.". ".number_format ($datos_proffo["valor_prorroga"], 0, ',', '.');
			$fech_i.=$cont.". ".$datos_proffo["dia_i"]." ".$mes[$datos_proffo["mes_i"]]." ".$datos_proffo["ano_i"];
			$fech_f.=$cont.". ".$datos_proffo["dia_f"]." ".$mes[$datos_proffo["mes_f"]]." ".$datos_proffo["ano_f"];						
			$documento.=$cont.". ".$datos_proffo["documento_soporte"];
			$observaciones.=$cont.". ".$datos_proffo["observaciones"];
			$cont++;				
*/			
//				$usuario.=$cont.". "."[".$datos_proffo["unidad"]."] ".$datos_proffo["nombreU"];		

			$tipo_prorroga.=$datos_proffo["prorroga_adicion"]."<br> ";
			$valor.=number_format ($datos_proffo["valor_prorroga"], 0, ',', '.')."<br> ";
			$fech_i.=$datos_proffo["dia_i"]." ".$mes[$datos_proffo["mes_i"]]." ".$datos_proffo["ano_i"]."<br> ";
			$fech_f.=$datos_proffo["dia_f"]." ".$mes[$datos_proffo["mes_f"]]." ".$datos_proffo["ano_f"]."<br> ";						
			$documento.=$datos_proffo["documento_soporte"]."<br> ";
			$observaciones.=$datos_proffo["observaciones"]."<br> ";		
			
	
	}
?>
	<td><?=$tipo_prorroga ?></td>
	<td><?=$fech_i ?></td>
	<td><?=$fech_f ?></td>
	<td><?=$documento ?></td>
	<td><?=$valor ?></td>
	<td><?=$observaciones ?></td>
<!---				
	VALORES FACTURADOS
  -->    
<?php

	$total_valorF_porce="";
	$fech_i="";
	$fech_f="";
	$valor="";
	
	$cur_facturado=mssql_query("select EFValores_Facturados.* ,year(fecha_inicio_facturacion) ano_i, month(fecha_inicio_facturacion) mes_i ,day(fecha_inicio_facturacion) dia_i
	,year(fecha_ultima_facturacion) ano_f, month(fecha_ultima_facturacion) mes_f ,day(fecha_ultima_facturacion) dia_f ,
	
	  (select EFConsorcios_Empresas.porcentaje_participacion from EFConsorcios_Empresas where EFConsorcios_Empresas.id_proyecto=".$dato_proy["id_proyecto"]." and EFConsorcios_Empresas.valores_actuales=1  and EFConsorcios_Empresas.empresa_lider=1) porcentaje_participacion 
	  
	  from EFValores_Facturados where id_proyecto=".$dato_proy["id_proyecto"]." order by fecha_inicio_facturacion ");
	  
	$cont=1;
	while($datos_proffo=mssql_fetch_array($cur_facturado))
	{
//echo $datos_proffo["porcentaje_participacion"]." *** <br>";		
/*
				$total_valorF_porce.="B".$cont.". ".( ( (int) $datos_proffo["valor_facturado"] ) * ( (float) $datos_proffo["porcentaje_participacion"]) )/100;
			
				$fech_i="B".$cont.". ".$datos_proffo["dia_i"]." ".$mes[$datos_proffo["mes_i"]]." ".$datos_proffo["ano_i"];
				$fech_f="B".$cont.". ".$datos_proffo["dia_f"]." ".$mes[$datos_proffo["mes_f"]]." ".$datos_proffo["ano_f"];				
				$valor="B".$cont.". ".number_format ($datos_proffo["valor_facturado"], 0, ',', '.');
*/				
							
//				$usuario=$cont.". "."[".$datos_proffo["unidad"]."] ".$datos_proffo["nombreU"];

				$total_valorF_porce.=number_format ( (( ( (int) $datos_proffo["valor_facturado"] ) * ( (float) $datos_proffo["porcentaje_participacion"]) )/100), 0, ',', '.')."<br> ";
			
				$fech_i=$datos_proffo["dia_i"]." ".$mes[$datos_proffo["mes_i"]]." ".$datos_proffo["ano_i"]."<br> ";
				$fech_f=$datos_proffo["dia_f"]." ".$mes[$datos_proffo["mes_f"]]." ".$datos_proffo["ano_f"]."<br> ";				
				$valor=number_format ($datos_proffo["valor_facturado"], 0, ',', '.')."<br> ";
							
			
			$cont++;		
	}

?>  
	<td><?=$fech_i ?></td>
	<td><?=$fech_f ?></td>
	<td><?=$valor ?></td>
	<td><?=$total_valorF_porce ?></td>
<!---				
	CEERTIFICADOS

  -->     
	<td><?=$dato_proy["dia_cer"]." ".$mes[$dato_proy["mes_cer"]]." ".$dato_proy["ano_cer"]."<br> " ?></td>
	<td><?=$dato_proy["dia_rec"]." ".$mes[$dato_proy["mes_rec"]]." ".$dato_proy["ano_rec"]."<br> " ?></td>
<!---				
	Soporte
  -->              
  
  <?php
  		$soportes="";
		$sql_soporte="select EFTipos_soporte.tipo_soporte, EFTipos_soporte.id_tipo_soporte, EFSoportes.id_soporte from EFSoportes
						inner join EFTipos_soporte on EFSoportes.id_tipo_soporte=EFTipos_soporte.id_tipo_soporte
						where EFSoportes.id_proyecto=".$dato_proy["id_proyecto"]." order by tipo_soporte";
		$cur_soporte= mssql_query($sql_soporte);	
		$cont=0;
		while($datos_soporte=mssql_fetch_array($cur_soporte))
		{
			if($cont==0)					
				$soportes.=$datos_soporte["tipo_soporte"]."<br> ";
			else
			$soportes.=", ".$datos_soporte["tipo_soporte"]."<br> ";
		}
?>
	<td><?=$soportes ?></td>

</tr>
	
<?php
	}
?>
</table>
</body>
</html>  