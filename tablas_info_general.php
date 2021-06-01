<?php

	//CONSULTA LA INFORMACION GENERAL DEL PROYECTO
	$SQL_INFOG="select EFProyectos.id_tipo_proyecto,  EFEstados_Proy_Proyectos.id_estado_proy, EFProyectos.id_proyecto, nombre_largo_proyecto, nombre_corto_proyecto, EFClientes.cliente ,EFEmpresas.empresa, EFEstados_Proy.estado_proyecto, EFTipos_Proyecto .tipo_proyecto,EFTipos_Ejecucion.ejecucion
	,certificado_SPEEDWARE,codigo, codigo_SEVEN, codigo_SEVEN_anterior,  numero_contrato, TRM, CONVERT(varchar, EFFechas_Proyecto.fecha_inicio_proyecto,101) fecha_inicio_proyecto, CONVERT(varchar, EFFechas_Proyecto.fecha_final_proyecto,101) fecha_final_proyecto  
	, EFMonedas.moneda, EFFormas_pago.forma_pago, EFValores_Contrato.valor_contrato, certificado_SEVEN
	,EFProyectos.objeto, EFProyectos.id_tipo_ejecucion, EFProyectos.revision, EFEstados_Proy_Proyectos.observaciones
	 from EFProyectos
	inner join EFNombres_Proyectos on EFProyectos.id_proyecto=EFNombres_Proyectos.id_proyecto and nombre_actual=1
	inner join EFClientes_Proyectos on EFClientes_Proyectos.id_proyecto=EFProyectos.id_proyecto and cliente_actual=1
	inner join EFClientes on EFClientes_Proyectos.id_cliente=EFClientes.id_cliente
	inner join EFEmpresas on EFProyectos.id_empresa=EFEmpresas.id_empresa and EFEmpresas.tipo=1
	inner join EFEstados_Proy_Proyectos on EFProyectos.id_proyecto=EFEstados_Proy_Proyectos.id_proyecto and EFEstados_Proy_Proyectos.estado_actual=1
	inner join EFEstados_Proy on EFEstados_Proy_Proyectos.id_estado_proy=EFEstados_Proy.id_estado_proy
	inner join EFTipos_Proyecto on EFTipos_Proyecto.id_tipo_proyecto=EFProyectos.id_tipo_proyecto
	inner join EFTipos_Ejecucion on EFTipos_Ejecucion.id_tipo_ejecucion=EFProyectos.id_tipo_ejecucion
	left join EFFechas_Proyecto on EFFechas_Proyecto.id_proyecto=EFProyectos.id_proyecto and fechas_actuales=1

	left join EFMonedas ON EFMonedas.id_moneda=EFProyectos.id_moneda
	inner join EFFormas_pago ON EFFormas_pago.id_forma_pago=EFProyectos.id_forma_pago
	inner join EFValores_Contrato on EFValores_Contrato.id_proyecto=EFProyectos.id_proyecto and EFValores_Contrato.valores_actuales=1
	WHERE EFProyectos.id_proyecto=".$Proy;
	$cur_info=mssql_query($SQL_INFOG);
	$datos_info=mssql_fetch_array($cur_info);
	
	//$Revision, CAMPO QUE IDENTIFICA SI SE HA REVISADO EL PROYECTO
	$Revision=$datos_info["revision"];
	$Tipo_Proyecto=$datos_info["id_tipo_proyecto"];
	$Estado_Proyecto=$datos_info["id_estado_proy"];

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
                  <th width="29%" >Proyecto (Nombre largo)</th>
                  <th >Proyecto (Nombre corto)</th>
                  <?php
					//SI LA SECCION DESDE DONDE SE VISUALIZA LA TABLA ES (Experiencia de la firma - Definici&oacute;n del proyecto - Informaci&oacute;n general), SE HABILITARAN 
					// LOS BOTONES DE EDICION Y ELIMINACION
					if(($sec=="1-1-1")|| ($sec==""))
					{
						//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
						if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
						{						
							///****************** TEMPORAR PÁRA DESCOMENTARIAR (06 jUL 2017) *********************** 
													
							//SI EL PROYECTO ESTA ADJUDICADO O EN EJECUCION O SUSPENDIDO o PROPUESTA O CÓDIGO INTERNO, PERMITE EDITAR LA INFORMACION DEL PROYECTO
//							if((($Estado_Proyecto==2) )|| ($Estado_Proyecto==3)|| ($Estado_Proyecto==5)|| ($Estado_Proyecto==1)|| ($Estado_Proyecto==10))							
							{
?>                  
                      <th width="1%" rowspan="18" style="vertical-align:middle;">
                         <a href="#" onClick="MM_openBrWindow('upProy.php?acc=2&Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=600,height=650');"> <i class="glyphicon glyphicon-pencil"></i> </a>
                      </th>
<?php
							}
/*							
							///****************** TEMPORAR PÁRA DESCOMENTARIAR (06 jUL 2017) *********************** 
							
							//SI EL PROYECTO ESTA TERMINADO (FPx P) O TERMINADO O LIQUIDADO, NO PERMITE MODIFICAR LA INFORMACION DEL PROYECTO
							if((($Estado_Proyecto==7) )|| ($Estado_Proyecto==9)|| ($Estado_Proyecto==11))							
							{
?>
                                  <th width="1%" rowspan="18" style="vertical-align:middle; " >
                                     <i class="glyphicon glyphicon-pencil" style="cursor:pointer; " title="No se permite modificar el proyecto, porque el estado del proyecto es: terminado, terminado (FPx P) o liquidado. "> </i> 		
                                  </th>
<?php								
							}
*/							
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
							UNION
							select COUNT(*)  from EFUbicacion_Proyectos WHERE id_proyecto=".$Proy."	
							UNION
							select COUNT(*)  from EFCertificados WHERE id_proyecto=".$Proy."
							UNION
							select COUNT(*) from EFLiquidacion WHERE id_proyecto=".$Proy."
							UNION
							select COUNT(*) from EFReanudaciones WHERE id_proyecto=".$Proy."
							UNION
							select COUNT(*) from EFSuspenciones	WHERE id_proyecto=".$Proy."
							) A	";							
						$cur_cna_r=mssql_query($sql_canr_r);
						$datos_r=mssql_fetch_array($cur_cna_r);
//echo $sql_canr_r." ***** ".$datos_r["canti"];		
						//SI NO HA REGISTR, HABILITA EL BOTON DE ELIMINACION				
						//SI EL ESTADO DEL PROYECTO ES ADJUDICADO O EN EJECUCION O SUSPENDIDO o PROPUESTA O CÓDIGO INTERNO, PERMITE EDITAR LA INFORMACION DEL PROYECTO
						if( (( (int) $datos_r["canti"])==0) && ((($Estado_Proyecto==2) )|| ($Estado_Proyecto==3)|| ($Estado_Proyecto==5)|| ($Estado_Proyecto==1)|| ($Estado_Proyecto==10)) )
						{			
							//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
							if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
							{
							
?>                     
                          <th width="1%" rowspan="18" style="vertical-align:middle; " >
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
								
								///****************** TEMPORAR PÁRA DESCOMENTARIAR (06 jUL 2017) *********************** 								
								/*
								//SI EL PROYECTO ESTA TERMINADO (FPx P) O TERMINADO O LIQUIDADO, NO PERMITE ELIMINAR LA INFORMACION DEL PROYECTO
								if( ($Estado_Proyecto==7 )|| ($Estado_Proyecto==9)|| ($Estado_Proyecto==11) )								
									$mensa="No se permite eliminar el proyecto, porque el estado del proyecto es: terminado, terminado (FPx P) o liquidado. ";
								else	*/
									$mensa="No se permite eliminar el proyecto, porque existe informaci&oacute;n asociada de prorrogas, adicionales, suspensiones, reanudaciones, liquidaciones, consorcios, valores facturados, informaci&oacute;n adicional, encargados del proyecto o documentos de recepci&oacute;n.";
							
?>                     
                          <th width="1%" rowspan="18" style="vertical-align:middle; " >
                             <i class="glyphicon glyphicon-remove" style="cursor:pointer; " title="<?=$mensa ?>"> </i> 		
                          </th>
<?php							
							}
						}
					}
?>                   
                </tr>
                <tr>
                  <td ><?=$datos_info["nombre_largo_proyecto"] ?></td>
                  <td ><?=$datos_info["nombre_corto_proyecto"] ?></td>
                </tr>
                <tr>
                  <th >Forma de Ejecuci&oacute;n</th>
                  <th width="29%" >No. Contrato</th>
                </tr>
                <tr>
                  <td ><?=$datos_info["ejecucion"] ?></td>
                  <td ><?=$datos_info["numero_contrato"] ?></td>
                </tr>
                <tr>
                  <th >C&oacute;digo SEVEN</th>
                  <th >Certificado SEVEN</th>
                </tr>
                <tr>
                  <td ><?=$datos_info["codigo_SEVEN"] ?></td>
                  <td ><?=$datos_info["certificado_SEVEN"] ?></td>
                </tr>                
                <tr>
                  <th >Tipo de proyecto</th>
                  <th >Estado</th>
                </tr>
                <tr>
                  <td ><?=$datos_info["tipo_proyecto"] ?></td>
                  <td ><?=$datos_info["estado_proyecto"] ?></td>
                </tr>
                <tr>
                  <th >Fecha Inicio</th>
                  <th >Fecha Finalizaci&oacute;n</th>
                </tr>
                <tr>
                  <td ><?=$datos_info["fecha_inicio_proyecto"] ?></td>
                  <td ><?=$datos_info["fecha_final_proyecto"] ?></td>
                </tr>
                <tr>
                  <th >Valor del proyecto</th>
                  <th >Moneda</th>
                </tr>

                <tr>
                  <td >$
                  <?=number_format ($datos_info["valor_contrato"], 0, ',', '.'); ?></td>
                  <td ><?=$datos_info["moneda"] ?></td>
                </tr>
                <tr>
                  <th >Empresa encargada</th>
                  <th >Cliente</th>
                </tr>
                <tr>
                  <td ><?=$datos_info["empresa"] ?></td>
                  <td ><?=$datos_info["cliente"] ?></td>
                </tr>
                <tr>
                  <th >Tipo de Facturaci&oacute;n</th>
                  <th ><span class="form-group">
                  <label for="label">C&oacute;digo Antiguo</label>
                  </span></th>
                </tr>
                <tr>
                  <td ><?=$datos_info["forma_pago"] ?></td>
                  <td ><?=$datos_info["codigo"] ?></td>
                </tr>
                <tr>
                  <th colspan="2" >Observaciones</th>
                </tr>
                <tr>
                  <td colspan="2" ><?=$datos_info["observaciones"] ?></td>
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
              <th colspan="5" >Proyecto (Nombre largo)</th>
            </tr>
            <tr>
              <td colspan="5" ><?=$datos_info["nombre_largo_proyecto"] ?></td>
            </tr>
            <tr>
              <th width="29%" >Tipo de proyecto</th>
              <th width="29%" >Estado</th>
              <th width="39%" colspan="3" >Empresa encargada</th>
            </tr>
            <tr>
              <td ><?=$datos_info["tipo_proyecto"] ?></td>
              <td ><?=$datos_info["estado_proyecto"] ?></td>
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
         