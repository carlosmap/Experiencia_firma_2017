<?php

	//CONSULTA LA INFORMACION GENERAL DEL PROYECTO
	$SQL_INFOG="select EFProyectos.id_proyecto, nombre_largo_proyecto, nombre_corto_proyecto, EFClientes.cliente ,EFEmpresas.empresa, EFEstados_Proy.estado_proyecto, EFTipos_Proyecto .tipo_proyecto,EFTipos_Ejecucion.ejecucion
	,certificado_SPEEDWARE,codigo, codigo_SEVEN, codigo_SEVEN_anterior,  numero_contrato, TRM, CONVERT(varchar, EFFechas_Proyecto.fecha_inicio_proyecto,101) fecha_inicio_proyecto, CONVERT(varchar, EFFechas_Proyecto.fecha_final_proyecto,101) fecha_final_proyecto  
	, EFPaises.pais , EFDepartamentos.departamento, EFMunicipios.municipio, EFMonedas.moneda, EFFormas_pago.forma_pago, EFValores_Contrato.valor_contrato, certificado_SEVEN
	,EFProyectos.objeto, EFProyectos.id_tipo_ejecucion, EFProyectos.revision
	 from EFProyectos
	inner join EFNombres_Proyectos on EFProyectos.id_proyecto=EFNombres_Proyectos.id_proyecto and nombre_actual=1
	inner join EFClientes_Proyectos on EFClientes_Proyectos.id_proyecto=EFProyectos.id_proyecto and cliente_actual=1
	inner join EFClientes on EFClientes_Proyectos.id_cliente=EFClientes.id_cliente
	inner join EFEmpresas on EFProyectos.id_empresa=EFEmpresas.id_empresa and EFEmpresas.tipo=1
	inner join EFEstados_Proy_Proyectos on EFProyectos.id_proyecto=EFEstados_Proy_Proyectos.id_proyecto and EFEstados_Proy_Proyectos.estado_actual=1
	inner join EFEstados_Proy on EFEstados_Proy_Proyectos.id_estado_proy=EFEstados_Proy.id_estado_proy
	inner join EFTipos_Proyecto on EFTipos_Proyecto.id_tipo_proyecto=EFProyectos.id_tipo_proyecto
	inner join EFTipos_Ejecucion on EFTipos_Ejecucion.id_tipo_ejecucion=EFProyectos.id_tipo_ejecucion
	inner join EFFechas_Proyecto on EFFechas_Proyecto.id_proyecto=EFProyectos.id_proyecto and fechas_actuales=1
	inner join EFUbicacion_Proyectos on EFUbicacion_Proyectos.id_proyecto=EFProyectos.id_proyecto
	inner join EFPaises on  EFPaises.id_pais=EFUbicacion_Proyectos.id_pais 
	inner join EFDepartamentos on  EFDepartamentos.id_pais=EFUbicacion_Proyectos.id_pais and EFDepartamentos.id_departamento=EFUbicacion_Proyectos.id_departamento
	inner join EFMunicipios on  EFMunicipios.id_pais=EFUbicacion_Proyectos.id_pais and EFMunicipios.id_departamento=EFUbicacion_Proyectos.id_departamento
	 and EFMunicipios.id_municipio=EFUbicacion_Proyectos.id_municipio
	inner join EFMonedas ON EFMonedas.id_moneda=EFProyectos.id_moneda
	inner join EFFormas_pago ON EFFormas_pago.id_forma_pago=EFProyectos.id_forma_pago
	inner join EFValores_Contrato on EFValores_Contrato.id_proyecto=EFProyectos.id_proyecto and EFValores_Contrato.valores_actuales=1
	WHERE EFProyectos.id_proyecto=".$Proy;
	$cur_info=mssql_query($SQL_INFOG);
	$datos_info=mssql_fetch_array($cur_info);
	
	//$Revision, CAMPO QUE IDENTIFICA SI SE HA REVISADO EL PROYECTO
	$Revision=$datos_info["revision"];
	
	//TABLA CON TODA LA INFORMACION 
	if($tabla==1)
	{
//echo $_SESSION["sesPerfilUsuarioExpFir"]." *********** "		;
?>
<div class="titulos" >
               Informaci&oacute;n general
           </div>
           
            <table width="100%" class="table table-bordered"   >
              <thead>
                <tr>
                  <th width="29%" >Id</th>
                  <th >Proyecto (Nombre largo)</th>
                  <th colspan="3" >Proyecto (Nombre corto)</th>
                  <?php
					//SI LA SECCION DESDE DONDE SE VISUALIZA LA TABLA ES (Experiencia de la firma - Definici&oacute;n del proyecto - Informaci&oacute;n general), SE HABILITARAN 
					// LOS BOTONES DE EDICION Y ELIMINACION
					if(($sec=="1-1-1")|| ($sec==""))
					{
						//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
						if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
						{						
?>                  
                      <th width="1%" rowspan="14" style="vertical-align:middle;">
                         <a href="#" onClick="MM_openBrWindow('upProy.php?acc=2&Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=600,height=650');"> <i class="glyphicon glyphicon-pencil"></i> </a>
                      </th>
<?php
						}
						//VERIFICA SI NO EXISTEN REGISTROS EN LA TABLAS 
						$sql_canr_r="select SUM(cant) canti from(
							select COUNT(*) cant from EFProrrogas where id_proyecto=".$Proy."
							union
							select COUNT(*) from EFAdicionales where id_proyecto=".$Proy."
							union
							select COUNT(*) from EFConsorcios where id_proyecto=".$Proy."
							union
							select COUNT(*) from EFValores_Facturados where id_proyecto=".$Proy."
							union
							select COUNT(*) from EFClases_Proyectos where id_proyecto=".$Proy."
							union
							select COUNT(*) from EFEspecialidades_Proyectos where id_proyecto=".$Proy."
							union
							select COUNT(*) from EFOrdenadores_gasto where id_proyecto=".$Proy."
							union
							select COUNT(*) from EFDocumentos_Recepcion_Certificado where id_proyecto=".$Proy."							
							) A	";							
						$cur_cna_r=mssql_query($sql_canr_r);
						$datos_r=mssql_fetch_array($cur_cna_r);
//echo $sql_canr_r." ***** ".$datos_r["canti"];		
						//SI NO HA REGISTR, HABILITA EL BOTON DE ELIMINACION				
						if(( (int) $datos_r["canti"])==0)
						{

		
							//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
							if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
							{
							
?>                     
                          <th width="1%" rowspan="14" style="vertical-align:middle; " >
                             <a href="#"  onClick="MM_openBrWindow('upProy.php?acc=3&Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=600,height=650');" > <i class="glyphicon glyphicon-remove"> </i> </a>
                          </th>
<?php
							}
						}
						else
						{
		
							//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
							if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
							{
							
?>                     
                          <th width="1%" rowspan="14" style="vertical-align:middle; " >
                             <i class="glyphicon glyphicon-remove" style="cursor:pointer; " title="No se permite eliminar el proyecto, porque existe informaci&oacute;n asociada de prorrogas, adicionales, consorcios, valores facturados, informaci&oacute;n adicional, encargados del proyecto o documentos de recepci&oacute;n. "> </i> 		
                          </th>
<?php							
							}
						}
					}
?>                   
                </tr>
                <tr>
                  <td ><?=$datos_info["id_proyecto"] ?></td>
                  <td ><?=$datos_info["nombre_largo_proyecto"] ?></td>
                  <td colspan="3" ><?=$datos_info["nombre_corto_proyecto"] ?></td>
                </tr>
                <tr>
                  <th >Forma de Ejecuci&oacute;n</th>
                  <th width="29%" >Estado</th>
                  <th colspan="3" >Tipo de proyecto</th>
                </tr>
                <tr>
                  <td ><?=$datos_info["ejecucion"] ?></td>
                  <td ><?=$datos_info["estado_proyecto"] ?></td>
                  <td colspan="3" ><?=$datos_info["tipo_proyecto"] ?></td>
                </tr>
                <tr>
                  <th >Certificado SPEEDWARE</th>
                  <th >Certificado SEVEN</th>
                  <th colspan="3" >C&oacute;digo</th>
                </tr>
                <tr>
                  <td ><?=$datos_info["certificado_SPEEDWARE"] ?></td>
                  <td ><?=$datos_info["certificado_SEVEN"] ?></td>
                  <td colspan="3" ><?=$datos_info["codigo"] ?></td>
                </tr>
                <tr>
                  <th >C&oacute;digo SEVEN nuevo</th>
                  <th >C&oacute;digo SEVEN anterior</th>
                  <th colspan="3" >Cliente</th>
                </tr>
                <tr>
                  <td ><?=$datos_info["codigo_SEVEN"] ?></td>
                  <td ><?=$datos_info["codigo_SEVEN_anterior"] ?></td>
                  <td colspan="3" ><?=$datos_info["cliente"] ?></td>
                </tr>
                <tr>
                  <th >No. Contrato</th>
                  <th >Empresa encargada</th>
                  <th colspan="3" >TRM</th>
                </tr>
                <tr>
                  <td ><?=$datos_info["numero_contrato"] ?></td>
                  <td ><?=$datos_info["empresa"] ?></td>
                  <td colspan="3" ><?=$datos_info["TRM"] ?></td>
                </tr>
                <tr>
                  <th >Fecha Inicio</th>
                  <th >Fecha Finalizaci&oacute;n</th>
                  <th >Pa&iacute;s</th>
                  <th >Departamento</th>
                  <th >Municipio</th>
                </tr>
                <tr>
                  <td ><?=$datos_info["fecha_inicio_proyecto"] ?></td>
                  <td ><?=$datos_info["fecha_final_proyecto"] ?></td>
                  <td ><?=$datos_info["pais"] ?></td>
                  <td ><?=$datos_info["departamento"] ?></td>
                  <td ><?=$datos_info["municipio"] ?></td>
                </tr>
                <tr>
                  <th >Valor del proyecto</th>
                  <th >Moneda</th>
                  <th colspan="3" >Forma de pago</th>
                </tr>
                <tr>

                  <td width="29%" >$ <?=number_format ($datos_info["valor_contrato"], 0, ',', '.'); ?></td>
                  <td ><?=$datos_info["moneda"] ?></td>
                  <td width="13%" colspan="3" ><?=$datos_info["forma_pago"] ?></td>
                </tr>
    
              </thead>
              <tbody>
                        
              </tbody>
        </table> 
<?
	}
	//TABLA CON INFORMACION RESUMIDA
	if($tabla==2)
	{
?>
		  <div class="titulos" >
               Informaci&oacute;n general
</div>
        <table width="100%" class="table table-bordered"   >
          <thead>
            <tr>
              <th width="29%" >Id</th>
              <th colspan="4" >Proyecto (Nombre largo)</th>
            </tr>
            <tr>
              <td ><?=$datos_info["id_proyecto"] ?></td>
              <td colspan="4" ><?=$datos_info["nombre_largo_proyecto"] ?></td>
            </tr>
            <tr>
              <th >Estado</th>
              <th width="29%" >Cliente</th>
              <th width="39%" colspan="3" >Empresa encargada</th>
            </tr>
            <tr>
              <td ><?=$datos_info["estado_proyecto"] ?></td>
              <td ><?=$datos_info["cliente"] ?></td>
              <td colspan="3" ><?=$datos_info["empresa"] ?></td>
            </tr>
            <tr>
              <th >Fecha Inicio</th>
              <th >Fecha Finalizaci&oacute;n</th>
              <th colspan="3" >Forma de Ejecuci&oacute;n</th>
            </tr>
            <tr>
              <td ><?=$datos_info["fecha_inicio_proyecto"] ?></td>
              <td ><?=$datos_info["fecha_final_proyecto"] ?></td>
              <td colspan="3" ><?=$datos_info["ejecucion"] ?></td>
            </tr>
            <tr>
              <th colspan="2" >Valor del proyecto</th>
              <th colspan="3" >Moneda</th>
            </tr>
            <tr>
              <td colspan="2" >$ <?=number_format ($datos_info["valor_contrato"], 0, ',', '.') ?></td>
              <td colspan="3" ><?=$datos_info["moneda"] ?></td>
            </tr>
          </thead>
          <tbody>
                    
          </tbody>
</table>
<?
	}
?>
         