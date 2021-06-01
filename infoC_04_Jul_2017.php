<?php 

include("encabezado.php"); 
//tipo=1 (para ventanas principales)
banner(1);
include("menu.php"); 
			//TABLA DE RESUMEN
?>
		<div class="container" >
        
            <div  class="titulo_secccion">
                    Informaci&oacute;n Consolidada
                    <div class="espacioAzul">
                     </div>
            </div>        
<?php        
			//$tabla VARIABLE UTILIZADA EN EL ARCHIVO tablas_info_general.php
			// 1= MOSTRARA LA TABLA DE INFORMACION GENERAL. CON LA INFORMACION COMPLETA
			// 2= MOSTRARA LA TABLA DE INFORMACION GENERAL. CON LA INFORMACION RESUMIDA
			$tabla=1;
			// tablas_info_general.php ARCHIVO QUE CONTIENE LAS TABLA DE INFORMACION GENERAL COMPLETA Y RESUMIDA
			include("tablas_info_general.php");
?>            




   <?
		{

			
			
			$sql_espe="select EFEspecialidades.especialidad from EFEspecialidades_Proyectos
			inner join EFEspecialidades on EFEspecialidades.id_especialidad=EFEspecialidades_Proyectos.id_especialidad
			  where id_proyecto=".$Proy." ORDER BY especialidad";
			$cur_espe1=mssql_query($sql_espe);			
			
			$sql_espe="select EFClases.clase from EFClases_Proyectos
			inner join EFClases on EFClases.id_clase=EFClases_Proyectos.id_clase
			where id_proyecto=".$Proy." ORDER BY clase";
			$cur_espe2=mssql_query($sql_espe);		
			
		   $cant_reg=mssql_num_rows($cur_espe1)+mssql_num_rows($cur_espe2);				
   ?>


           <div class="titulos" >
             Informaci&oacute;n adicional
           </div>
           
		  <table class="table table-bordered" width="100%">
            <tr>
                <th width="48%">Especialidades</th>
                <th width="48%">Clase</th>
<?php   
/* 
			//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
			if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
			{
				//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
				if($Revision==1)
				{
?>	                
                    <td width="1%" rowspan="4" style="vertical-align:middle;">
    <?php
                    //SI HAY REGISTR, HABILITA EL BOTON 
                    if($cant_reg>0)
                    {
    
    ?>	
                        <a href="#" onClick="MM_openBrWindow('upinfoA.php?Proy=<?=$Proy ?>&acc=2','EF2','scrollbars=yes,resizable=yes,width=780,height=580');"> <i class="glyphicon glyphicon-pencil"></i> </a>
    <?php					
                    }
?>                
                    
              	   </td>
<?php					
				}

			}

			//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
			if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
			{
				//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
				if($Revision==1)
				{				
?>	
                    <td width="1%" rowspan="4" style="vertical-align:middle;">
    <?php
                    //SI HAY REGISTR, HABILITA EL BOTON 
                    if($cant_reg>0)
                    {
                    ?>	
                    <a href="#"  onClick="MM_openBrWindow('upinfoA.php?Proy=<?=$Proy ?>&acc=3','EF2','scrollbars=yes,resizable=yes,width=780,height=580');" > <i class="glyphicon glyphicon-remove"> </i> </a>
    <?php			
                        
                    }
				}
?>  
                
                </td>
<?php					
			}
			*/
?>                 
            </tr>
            <tr>
                <td>
<?php

				   while($datos_esp=mssql_fetch_array($cur_espe1))
				   {
					   echo $datos_esp["especialidad"]."<br>";
				   }
?>					  
                </td>
                <td>               
<?php

				   while($datos_esp=mssql_fetch_array($cur_espe2))
				   {
					   echo $datos_esp["clase"]."<br>";
				   }

?>					  
                </td>
            </tr>
            <tr>
                <th colspan="2">Objeto</th>
            </tr>
            <tr>
                <td colspan="2"><?=$datos_info["objeto"]; ?></td>
            </tr>
<?php
/*
				//SI NO HAY REGISTROS DE INFORMACION ADICIONAL,M HABILITA EL BOTON DE ADICION
				if($cant_reg==0)
				{
?>            
<?php    
					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
						if($Revision==1)
						{						
?>	
	                 <tr>
                        <td colspan="4" align="right"><button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addinfoA.php?Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=780,height=580');" >Informaci&oacute;n Adicional</button></td>
                     </tr>
<?php
						}
					}
				}
*/				
?>             
        </table>
    
<?PHP
		}
		
		{		
			//CONSULTA EL DIRECTOR Y CORRDINADOR DEL PROYECTO ACTUALES
			$sql_dir_or="
			select upper(U.apellidos+' '+U.nombre) nombre, U.unidad, Z.tipo  from (
				select unidad_director_coordinador,tipo from  EFDirector_Coordinador where tipo=1 and director_coordinador_actual=1 and id_proyecto=".$Proy."
				union
				select unidad_director_coordinador,tipo from  EFDirector_Coordinador where tipo=2 and director_coordinador_actual=1 and id_proyecto=".$Proy."
			) Z
			inner join HojaDeTiempo.dbo.Usuarios U on U.unidad=Z.unidad_director_coordinador
			order by tipo";
			$cursorIn1 = mssql_query($sql_dir_or);						
			$datos_esp=mssql_fetch_array($cursorIn1);
			
			$cant_reg=0;
			$cant_reg=mssql_num_rows($cursorIn1);
			
			//CONSULTA LOS ORDENADORES DE GASTO ACTUALES
			$sql_dir_or2="select  upper(U.apellidos+' '+U.nombre) nombre, U.unidad from EFOrdenadores_gasto 
			inner join HojaDeTiempo.dbo.Usuarios U on U.unidad=EFOrdenadores_gasto.unidad_ordenador
			WHERE id_proyecto=".$Proy." and ordenador_actual=1
			order by nombre ";					
			$cursorInOr = mssql_query($sql_dir_or2);				
			
?>    
              <div class="titulos" >
                   Encargados
               </div>    
               
               <table class="table table-bordered" width="100%"  >
                <tr>
                    <th width="44%">Director</th>
                    <th width="44%">Coordinador</th>
<?php
/*
			//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
			if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
			{
				//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
				if($Revision==1)
				{				
?>                    
                        <td width="1%" rowspan="<?=(mssql_num_rows($cursorInOr)+3) ?>" style="vertical-align:middle;"> 
    <?php
                    //SI HAY REGISTROS DE INFORMACION DE ENCARGADOS, HABILITA EL BOTON DE ADICION
                    if($cant_reg>0)
                    {
    
    
    ?>	              
                        <a href="#" onClick="MM_openBrWindow('upEncargados.php?Proy=<?=$Proy ?>&acc=2','EF2','scrollbars=yes,resizable=yes,width=780,height=500');"> <i class="glyphicon glyphicon-pencil"></i> </a>
    <?php
                        
                    }
				}
?>                                   
                    </td>
<?php
			}
			//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
			if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
			{			
?>                    
                    
                    <td width="1%" rowspan="<?=(mssql_num_rows($cursorInOr)+3) ?>" style="vertical-align:middle;">
<?php
				//SI HAY REGISTROS DE INFORMACION DE ENCARGADOS, HABILITA EL BOTON DE ADICION
				if($cant_reg>0)
				{
					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
						if($Revision==1)
						{						
?>	           
                    <a href="#"  onClick="MM_openBrWindow('upEncargados.php?Proy=<?=$Proy ?>&acc=3','EF2','scrollbars=yes,resizable=yes,width=780,height=500');" > <i class="glyphicon glyphicon-remove"> </i> </a>
<?php
						}
					}
				}
?>                    
                    </td>
<?php
					
			}
*/			
?>                       
                </tr>
                <tr>
                    <td><?
					if($datos_esp["unidad"]!="")
                     echo "[".$datos_esp["unidad"]."] ".$datos_esp["nombre"];
				?></td>
<?PHP
					$datos_esp=mssql_fetch_array($cursorIn1);					
?>                  
                    <td><?php
					if($datos_esp["unidad"]!="")
                    	echo "[".$datos_esp["unidad"]."] ".$datos_esp["nombre"] ?>
                    </td>
                 </tr>
                <tr>
                    <th colspan="2">Ordenadores de gasto</th>
                 </tr>
<?php
					
					while($datos_esp=mssql_fetch_array($cursorInOr))
					{
?>                 
                        <tr>
                          <td colspan="2"><?="[".$datos_esp["unidad"]."] ".$datos_esp["nombre"] ?></td>
                         </tr>
<?php
					}
?>                 
<?php
/*
				//SI NO HAY REGISTROS DE INFORMACION DE ENCARGADOS, HABILITA EL BOTON DE ADICION
				if($cant_reg==0)
				{
   
					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
						if($Revision==1)
						{						
?>	
                <tr>
                    <td colspan="4" align="right">
                      <button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addEncargados.php?Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=780,height=500');" >Definir encargados</button>
                    </td>
                 </tr>
<?php
						}
					}
				}
*/				
?>		                 
               </table>
<?
		}
		

		{
?>			
		   <div class="titulos" >
             Certificados
           </div> 
                 
           <table  class="table table-bordered" width="100%"> 
            <tr>
              <th>No</th>
              <th>Fecha de Solicitud</th> 
                <th>Fecha de Recepci&oacute;n</th>
                <th >Documentos de Recepci&oacute;n</th>
                <!--
                <td></td>
                <td></td>
                -->
            </tr>
    <?php
			$sql_doc="select *, CONVERT(varchar, fecha_solicitud_certificado,101) fs, CONVERT(varchar,fecha_recepcion_certificado,101) fr, (select max(id_certificado) max_cer from EFCertificados  where id_proyecto=".$Proy.") max_cer from EFCertificados where id_proyecto=".$Proy;
			$cur_certi= mssql_query($sql_doc);		        
			$cant_certi=mssql_num_rows($cur_certi);
			while($datos_certi=mssql_fetch_array($cur_certi))
			{
    ?>                  
    
            <tr>
              <td><?=$datos_certi["id_certificado"] ?></td>
              <td><?=$datos_certi["fs"] ?></td>
                <td><?=$datos_certi["fr"] ?></td>
                <td>
                        <table class="table table-bordered" width="100%">
    <?php
					$sql_doc="select * from EFDocumentos_Recepcion_Certificado where id_proyecto=".$Proy." and  id_certificado=".$datos_certi["id_certificado"];
					$cur_doc= mssql_query($sql_doc);		        
		
					while($datos_doc=mssql_fetch_array($cur_doc))
					{
    ?>                                               
                            <tr>                        
                              <td  onclick="window.open('<?=$datos_doc["url_doc"]; ?>','_blank')"  class="TR"  style="cursor:pointer; " >
                                <i class="glyphicon glyphicon-file"></i>
                                <?=$datos_doc["nombre_doc"] ?> 
                                
                              </td>
        <?php    
/*
                            //SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
                            if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
                            {
                                //SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
                                if($Revision==1)
                                {						
									//SI SE ESTA CONSULTANDO EL ULTIMO CERTIFICADO REGISTRADO
									if($datos_certi["max_cer"]==$datos_certi["id_certificado"])
									{								
        ?>	                      
                                         <td width="1%">
        <?
        ?>                              
                                              <a href="#"  onClick="MM_openBrWindow('delDocumento.php?Proy=<?=$Proy ?>&doc=<?=$datos_doc["id_documento_recepcion_certificado"] ?>&no=<?=$datos_certi["id_certificado"]	 ?>','EF2','scrollbars=yes,resizable=yes,width=580,height=460');" > <i class="glyphicon glyphicon-remove"> </i> </a>
        <?
                                            
        ?>                                      
                                       </td>
        <?php
									}
                                }
                            }
							*/
        ?>                                      
                            </tr>
        <?php
                    }

				/*
					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
						if($Revision==1)
						{			
							//SI SE HA DEFINIDO UNA FECHA DE RECEPCION		
							if($datos_certi["fecha_recepcion_certificado"]!="")
							{
								//SI SE ESTA CONSULTANDO EL ULTIMO CERTIFICADO REGISTRADO
								if($datos_certi["max_cer"]==$datos_certi["id_certificado"])
								{								
		?>	                    
						<tr>
							<td colspan="5" align="right">
                             
  		                          	<button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addDocumento.php?Proy=<?=$Proy ?>&no=<?=$datos_certi["id_certificado"]	 ?>','EF2','scrollbars=yes,resizable=yes,width=580,height=5	20');" >Nuevo Documento</button>
<?
								
?>                                  
                                </td>
						 </tr>  
		<?php
								}
							}
						}				
					}
				*/
?>  
                        </table>                                      
                </td>
<?php
/*
				//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
				if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
				{
					//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
					if($Revision==1)
					{				
	?>                
					<td width="1%" rowspan="<?=$cant_filas ?>" style="vertical-align:middle;">
	<?php
						//SI SE ESTA CONSULTANDO EL ULTIMO CERTIFICADO REGISTRADO
						if($datos_certi["max_cer"]==$datos_certi["id_certificado"])
						{		
						
							if(($datos_certi["fecha_solicitud_certificado"]!="") || ($datos_certi["fecha_recepcion_certificado"]!=""))
							{
								//ALMACENA L RECHA DE RECEPCION DEL ULTIMO CERTIFICADO REGISTRADO
								$fecha_recepcio_ulti_certifi=$datos_certi["fr"];
		?>	
							<a href="#" onClick="MM_openBrWindow('upCertificado.php?Proy=<?=$Proy ?>&acc=2&no=<?=$datos_certi["id_certificado"]	 ?>','EF2','scrollbars=yes,resizable=yes,width=480,height=420');"> <i class="glyphicon glyphicon-pencil"></i> </a>
		<?php
								
							}
						}
					}
					
		?>                    
						</td>
		<?php
				 }
				 */
				/*
				//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
				if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
				{			
					//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
					if($Revision==1)
					{
										
		?>
						<td width="1%" rowspan="<?=$cant_filas ?>" style="vertical-align:middle;">
		<?php
						//SI SE HAN ASOCIADO FECHAS 
						if(($datos_certi["fecha_solicitud_certificado"]!="") || ($datos_certi["fecha_recepcion_certificado"]!="") || ($datos_certi["fecha_facturacion_certificado"]!=""))
						{
							//SI SE ESTA CONSULTANDO EL ULTIMO CERTIFICADO REGISTRADO
							if($datos_certi["max_cer"]==$datos_certi["id_certificado"])
							{									
								//SI NO EXISTEN DOCUMENTOS ASOCIADOS, SE PERMITE ELIMINAR 
								if((mssql_num_rows($cur_doc)==0))
								{
			?>                                
								<a href="#"  onClick="MM_openBrWindow('upCertificado.php?Proy=<?=$Proy ?>&acc=3&no=<?=$datos_certi["id_certificado"]	 ?>','EF2','scrollbars=yes,resizable=yes,width=480,height=420');" > <i class="glyphicon glyphicon-remove"> </i> </a>
			<?php
								}
								else
								{
			?>                   
			
								   <i class="glyphicon glyphicon-remove" style="cursor:pointer; " title="No se permite eliminar la informaci&oacute;n de los certificados, porque existen documentos de recepci&oacute;n asociados. "> </i> 		
			
			<?php
								}
							}
	//					}
					}
	?>                    
					</td>
	<?php					
				}
				*/
			
?>                
             </tr>
		<?php
			
			}
				/*		
			//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
			if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
			{
				//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
				if($Revision==1)
				{					
					//SI SE HA REGISTRADO, LA FECHA DE RECEPCION DEL ULTIMO CERTIFICADO, O NO EXISTEN CERTIFICADOS ASOCIADOS
					if(($fecha_recepcio_ulti_certifi!="")|| ($cant_certi==0))
					{
?>	      
		<tr>
			<td colspan="8" align="right">              <button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addCertificado.php?Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=480,height=420');" >Nuevo Certificado</button></td>
		 </tr>
<?php
					}
					
				}
			}
			*/
?>             
           </table>
       
<?php
		}
		
		

		{					
?>              
       
           <div class="titulos" >
             Soportes
           </div>       
           
         <table  class="table table-bordered" width="100%"> 
<?php
				$sql_soporte="select EFTipos_soporte.tipo_soporte, EFTipos_soporte.id_tipo_soporte, EFSoportes.id_soporte from EFSoportes
								inner join EFTipos_soporte on EFSoportes.id_tipo_soporte=EFTipos_soporte.id_tipo_soporte
								where EFSoportes.id_proyecto=".$Proy." order by tipo_soporte";
				$cur_soporte= mssql_query($sql_soporte);	
			
				while($datos_soporte=mssql_fetch_array($cur_soporte))
				{

					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
						if($Revision==1)
						{						
					
?>               
                    <tr>
                      <td><?=$datos_soporte["tipo_soporte"] ?></td>
<?php
/*
?>                      
                      <td width="1%" style="vertical-align:middle;"><a href="#"  onClick="MM_openBrWindow('delSoporte.php?Proy=<?=$Proy ?>&sop=<?=$datos_soporte["id_soporte"] ?>','EF2','scrollbars=yes,resizable=yes,width=780,height=350');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>
<?php
*/
?>                      
                   </tr>
<?php
						}
					}
				}
/*
				//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
				if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
				{
					//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
					if($Revision==1)
					{					
?>	
            <tr>
                <td colspan="2" align="right"><button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addSoporte.php?Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=780,height=350');" >Nuevo Soporte</button></td>
            </tr>
<?php
					}
				}
*/				
?>            
  </table>
<?php
		}
?>       
       
 <div class="titulos" >
             Ubicaci&oacute;n
           </div>  

			<table  class="table table-bordered" id="divUbicacionT">
                <tr>
                    <th>Pa&iacute;s</th>
                    <th>Departamento</th>
                    <th>Municipio</th>
<!--                    <th width="1%">&nbsp;</th> -->
                </tr>
					<?php
                    $cur_ubica=mssql_query("select  EFPaises.pais , EFDepartamentos.departamento, EFMunicipios.municipio from 	EFUbicacion_Proyectos 
                    
                    inner join EFPaises on  EFPaises.id_pais=EFUbicacion_Proyectos.id_pais 
                    inner join EFDepartamentos on  EFDepartamentos.id_pais=EFUbicacion_Proyectos.id_pais and EFDepartamentos.id_departamento=EFUbicacion_Proyectos.id_departamento
                    inner join EFMunicipios on  EFMunicipios.id_pais=EFUbicacion_Proyectos.id_pais and EFMunicipios.id_departamento=EFUbicacion_Proyectos.id_departamento
                    and EFMunicipios.id_municipio=EFUbicacion_Proyectos.id_municipio
                    where EFUbicacion_Proyectos.id_proyecto=".$Proy);
					
					$z=0;
                    while($datos=mssql_fetch_array($cur_ubica))
                    {
                    ?>                        
                                    <tr>
                                        <td><?=$datos["pais"] ?></td>
                                        <td><?=$datos["departamento"] ?></td>
                                        <td><?=$datos["municipio"] ?></td>               
<?php
/*
									if($z==0)
									{
?>                                                                 
                                        <td rowspan="<?=mssql_num_rows($cur_ubica); ?>" style="vertical-align:middle;" >
	                                        <a href="#" onclick="MM_openBrWindow('upUbicacion.php?acc=3&Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=780,height=370');"> 
											   <i class="glyphicon glyphicon-remove" > </i> 		                                        
                                           </a>
                                        </td>
<?php
									}
*/									
?>                                        
                                    </tr>
                    <?php
						$z=1;
                    }
	/*				
					if(mssql_num_rows($cur_ubica)==0)
					{
                    ?>  
                    
                        <tr>
                            <td colspan="4" align="right"><button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addUbicacion.php?Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=780,height=450');" >Definir Ubicaci&oacute;n</button></td>
                        </tr>                                          
<?php
					}
					*/
?>                        
             </table>

     
            
<?php        

			{
?>
              <div class="titulos" >
                       Valores del proyecto
          	  </div>    
<?php
				//SI LA FORMA DE EJECUCION ES EN CONSORCIO O UNION TEMPORAL
				if(($datos_info["id_tipo_ejecucion"]==2) || ($datos_info["id_tipo_ejecucion"]==3) )
				{

					$sql_nom_fir="select * from EFConsorcios where id_proyecto=".$Proy." and nombre_actual_consorcio=1";
					$cur_nom_fir=mssql_query($sql_nom_fir);
					$datos_nom_fir=mssql_fetch_array($cur_nom_fir);


					$sql_empr_consr="select EFEmpresas.id_empresa,EFEmpresas.empresa, EFConsorcios_Empresas.empresa_lider,EFConsorcios_Empresas.porcentaje_participacion,
					 EFConsorcios_Empresas.valor_contrato_porcentaje
					 from EFConsorcios_Empresas
					inner join EFEmpresas on EFEmpresas.id_empresa=EFConsorcios_Empresas.id_empresa
					where EFConsorcios_Empresas.id_proyecto=".$Proy." and EFConsorcios_Empresas.valores_actuales=1";
					$cur_empr_consr=mssql_query($sql_empr_consr);
?>                   
          <table width="100%"  class="table table-bordered">
                    <tr>
                        <th width="25%">Consorcio</th>
                        <th width="25%">Empresas</th>
                        <th width="15%">Empresa Lider</th>
                        <th width="15%">% Participaci&oacute;n</th>
                        <th width="20%">Valor % Participaci&oacute;n</th>
<?php
/*
					//SHO  SE HAN DEFINIDO EMPRESAS, SE HABILITAN LOS BOTONES DE UP
					if( (mssql_num_rows($cur_empr_consr))>0)			
					{
?>                      
<?php    
						//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
						if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
						{
							//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
							if($Revision==1)
							{
			?>	        
                        
                        <td width="1%" rowspan="<?=mssql_num_rows($cur_empr_consr)+2 ?>" style="vertical-align:middle;"><a href="#" onClick="MM_openBrWindow('upConsorcio.php?Proy=<?=$Proy ?>&acc=2','EF2','scrollbars=yes,resizable=yes,width=850,height=700');"> <i class="glyphicon glyphicon-pencil"></i> </a></td>
                        
                      <td width="1%" rowspan="<?=mssql_num_rows($cur_empr_consr)+2 ?>" style="vertical-align:middle;"><a href="#"  onClick="MM_openBrWindow('upConsorcio.php?Proy=<?=$Proy ?>&acc=3','EF2','scrollbars=yes,resizable=yes,width=850,height=700');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>

<?php
							}
						}
					}
*/					
?>                      
                    </tr>
<?php

					$z=0;
					$porce_tota=0; $valor_tota=0;
					while($datos_empre_cons=mssql_fetch_array($cur_empr_consr))
					{
?>                    
                        <tr>
<?php
							if($z==0)
							{
?>                      
                            <td rowspan="<?=mssql_num_rows($cur_empr_consr) ?>"><?=$datos_nom_fir["nombre_consorcio"]; ?></td>                        
<?php
							}							
?>                            
                            <td><?=$datos_empre_cons["empresa"] ?></td>
                          <td align="center" ><? 
							if($datos_empre_cons["empresa_lider"]==1) { ?>
								<i class="glyphicon glyphicon-ok-circle"  title="Empresa Lider"> </i>
                            <?php    
							} ?></td>
                            <td><?=number_format($datos_empre_cons["porcentaje_participacion"], 2, ',', '.')  ?> %</td>
                            <td>$ <?=number_format ($datos_empre_cons["valor_contrato_porcentaje"], 2, ',', '.'); ?></td>
                        </tr>
<?php
						$porce_tota+=((float) $datos_empre_cons["porcentaje_participacion"]);
						$valor_tota+=((float) $datos_empre_cons["valor_contrato_porcentaje"]);
						$z++;
					}
?>                    
                    <tr>
                        <th colspan="3" >Totales</th>

                        <td><?=$porce_tota ?>  %</td>
                        <td>$ <?=number_format ($valor_tota, 0, ',', '.'); ?></td>
                    </tr>
<?php    
/*
					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
						if($Revision==1)
						{			
							//SI NO SE HAN DEFINIDO EMPRESAS PARTICIPANTES, SE HABILITA EL BOTON DE NUEVO CONSORCIO
							if( (mssql_num_rows($cur_empr_consr))==0)			
							{
?>	                    	
            <tr>
                        <td colspan="7" align="right">
							<button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addConsorcio.php?Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=850,height=700');" >Nuevo Consorcio</button>                        
                        </td>
                    </tr>
<?php
							}
						}
					}
*/					
?>                    
          </table>
<?PHP
				}
				//SI LA FORMA DE EJECUCION ES INDIVIDUAL
				if($datos_info["id_tipo_ejecucion"]==1)
				{
					
					$sql_empre="select EFEmpresas.empresa,EFConsorcios_Empresas.empresa_lider,EFConsorcios_Empresas.porcentaje_participacion, EFConsorcios_Empresas.valor_contrato_porcentaje, EFConsorcios_Empresas.valores_actuales from EFConsorcios_Empresas
					INNER JOIN EFEmpresas on EFEmpresas.id_empresa=EFConsorcios_Empresas.id_empresa
					where EFConsorcios_Empresas.id_proyecto=".$Proy." and EFConsorcios_Empresas.valores_actuales=1	";
					$cur_empre= mssql_query($sql_empre);	
				
					$datos_empre=mssql_fetch_array($cur_empre);					
?>                
                <table width="100%"  class="table table-bordered">
                	<tr>
               		  <th>Empresa</th>
                		<th width="15%">Empresa Lider</th>
                		<th width="25%">% Participaci&oacute;n</th>
                		<th width="20%">Valor % Participaci&oacute;n</th>
               	  </tr>
                  
                	<tr>
                		<td><?=$datos_empre["empresa"]; ?></td>
                		<td align="center">
						<? if($datos_empre["empresa_lider"] == 1)							
							{
						?>
								<i class="glyphicon glyphicon-ok-circle"  title="Empresa lider. "> </i>                        

                        <?php		
							}
						?></td>
                		<td><?=$datos_empre["porcentaje_participacion"]; ?></td>
                		<td>$ <?=number_format ($datos_empre["valor_contrato_porcentaje"]); ?></td>
                	</tr>
                </table>
<?PHP			
				}
			}
			
			{
				
				//CONSULTA LAS PRORROGAS DEL PROYCTO
				$sql_proffo="select (U.apellidos+' '+U.nombre) nombreU,U.unidad, EFProrrogas.*, EFItems_Prorrogas_Adicionales.prorroga_adicion, EFDocumentos_Soporte.documento_soporte from EFProrrogas  
				inner join EFItems_Prorrogas_Adicionales on EFItems_Prorrogas_Adicionales.id_item_prorroga_adicion=EFProrrogas.id_item_prorroga_adicion
				inner join EFDocumentos_Soporte on EFDocumentos_Soporte.id_documento_soporte=EFProrrogas.id_documento_soporte
				inner join HojaDeTiempo.dbo.Usuarios U on U.unidad=EFProrrogas.usuarioGraba				
				where id_proyecto=".$Proy."
				order by EFProrrogas.id_prorroga ";
				
				$cur_proffo=mssql_query($sql_proffo);
					
?>

          <div class="titulos" >
                       Prorrogas
              </div>    

				<table width="100%"  class="table table-bordered">
					<tr>
					  <th width="1%">Id</th>
					  <th>Tipo Prorroga</th>
					  <th>Fecha Inicio</th>
						<th>Fecha Finalizaci&oacute;n</th>
<!--					  <th>Tiempo (meses)</th> -->
					  <th>Documento Soporte</th>
					  <th>Valor</th>
					  <th>Observaciones</th>
					  <th>Usuario Registra</th>
<!--                      
						<td width="1%" style="vertical-align:middle;" ></td>
-->                        
<?php    
/*
					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
						if($Revision==1)
						{		
?>		     
						<td width="1%" style="vertical-align:middle;"></td>
<?php
						}
					}
*/					
?>                        
				  </tr>
<?PHP
					$cant_reg=mssql_num_rows($cur_proffo);
					$z=1;
					while($datos_proffo=mssql_fetch_array($cur_proffo))
					{
?>                  
					<tr>
						<td width="1%"><?=$datos_proffo["id_prorroga"] ?></td>
						<td width="20%"><?=$datos_proffo["prorroga_adicion"] ?></td>
						<td width="10%"><?=$datos_proffo["fecha_inicio"] ?></td>
						<td width="10%"><?=$datos_proffo["fecha_final"] ?></td>
<!--						<td width="1%"><?=$datos_proffo[""] ?></td> -->
						<td width="15%"><?=$datos_proffo["documento_soporte"] ?></td>
						<td width="15%"> <? if(($datos_proffo["valor_prorroga"]!="")&&($datos_proffo["valor_prorroga"]!=0)){ echo "$ ".number_format ($datos_proffo["valor_prorroga"], 0, ',', '.'); }  ?></td>
						<td width="20%"><?=$datos_proffo["observaciones"] ?></td>
                        
						<td width="20%"><?="".$datos_proffo["nombreU"]." [".$datos_proffo["unidad"]."]" ?></td>
<!--                        
						<td width="1%" style="vertical-align:middle;" ><a href="#" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');"> <i class="glyphicon glyphicon-pencil"></i> </a></td>
-->                        
<?php
/*
						//SI SE ESTA RECORRIENDO EL ULTIMO REGISTRO, SE MUESTRA EL BOTON DE ELMINIACION
						if($cant_reg==$z)
						{
?>
<?php    
							//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
							if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
							{
								//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
								if($Revision==1)
								{		
?>		     
						  <td width="1%" style="vertical-align:middle;"><a href="#"  onClick="MM_openBrWindow('upProrroga.php?Proy=<?=$Proy ?>&prorro=<?=$datos_proffo["id_prorroga"] ?>&acc=3','EF2','scrollbars=yes,resizable=yes,width=780,height=650');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>
<?php
								}
							}
						}
*/						
						$z++;
?>                          
                      
					</tr>
<?php
					}
?>                    
<?php    
/*
					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
						if($Revision==1)
						{		
?>											
                            <tr>
                                <td colspan="11" align="right"> <button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addProrroga.php?Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=780,height=650');" >Nueva Prorroga</button>    </td>
                            </tr>
<?php
						}
					}
*/					
?>                    
				</table>
<?php				
			}
			
			{
?>

          <div class="titulos" >
                       Adicionales
          </div>    

<?php
				//CONSULTA LAS ADICIONES DEL PROYCTO
				$sql_adicionales="
					select (U.apellidos+' '+U.nombre) nombreU,U.unidad, EFAdicionales.*, EFItems_Prorrogas_Adicionales.prorroga_adicion
					, EFDocumentos_Soporte.documento_soporte from EFAdicionales  
					inner join EFItems_Prorrogas_Adicionales on EFItems_Prorrogas_Adicionales.id_item_prorroga_adicion=EFAdicionales.id_item_prorroga_adicion
					inner join EFDocumentos_Soporte on EFDocumentos_Soporte.id_documento_soporte=EFAdicionales.id_documento_soporte
					inner join HojaDeTiempo.dbo.Usuarios U on U.unidad=EFAdicionales.usuarioGraba				
					where id_proyecto=".$Proy."
					order by EFAdicionales.id_adicional	";
				
				$cur_adicionales=mssql_query($sql_adicionales);
					
?>
		  <table width="100%"  class="table table-bordered">
					<tr>
					  <th width="1%">Id</th>
					  <th>Tipo de Adici&oacute;n</th>
					  <th>Valor</th>
						<th>Fecha</th>
					  <th>Documento Soporte</th>
					  <th>Observaciones</th>
					  <th>Usuario Registra</th>    
<?php    
/*
					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
						if($Revision==1)
						{		
?>		                                             
					  <td width="1%" style="vertical-align:middle;" ></td>
<?php
						}
					}
*/					
?>                      
				  </tr>
<?PHP
					$cant_reg=mssql_num_rows($cur_adicionales);
					$z=1;
					while($datos_proffo=mssql_fetch_array($cur_adicionales))
					{
?>                   
					<tr>
						<td><?=$datos_proffo["id_adicional"]; ?></td>
					  <td width="20%"><?=$datos_proffo["prorroga_adicion"] ?></td>
					  <td width="10%"><?="$ ".number_format ($datos_proffo["valor_adicion"], 0, ',', '.'); ?></td>
					  <td width="10%"><?=$datos_proffo["fecha_adicion"] ?></td>
					  <td width="15%"><?=$datos_proffo["documento_soporte"] ?></td>
					  <td width="15%"><?=$datos_proffo["observaciones"] ?></td>
					  <td width="20%"><?="".$datos_proffo["nombreU"]." [".$datos_proffo["unidad"]."]" ?></td>

<?php
/*
						//SI SE ESTA RECORRIENDO EL ULTIMO REGISTRO, SE MUESTRA EL BOTON DE ELMINIACION
						if($cant_reg==$z)
						{
?>
<?php    
							//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
							if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
							{
								//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
								if($Revision==1)
								{		
?>		     
                                                
						  <td width="1%" style="vertical-align:middle;"><a href="#"  onClick="MM_openBrWindow('upAdicion.php?Proy=<?=$Proy ?>&add=<?=$datos_proffo["id_adicional"] ?>&acc=3','EF2','scrollbars=yes,resizable=yes,width=850,height=550');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>
<?PHP
								}
							}
						}
*/						
						$z++;
?>
					</tr>
<?php
					}
?>                    

<?php    
/*
					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
						if($Revision==1)
						{		
?>			

					<tr>
						<td colspan="9" align="right"> <button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addAdicion.php?Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=850,height=550');" >Nueva Adici&oacute;n</button>    </td>
					</tr>
<?php
						}
					}
*/					
?>                    
		  </table>
<?php				
			}			

			
			{
?>

          <div class="titulos" >
                       Valores facturados
              </div>    
<?php
					//XCONSULTA LA EMPRESA LIDER DEL CONSORCIO ACTUAL
					$sql_empr_consr="select EFEmpresas.id_empresa,EFEmpresas.empresa, EFConsorcios_Empresas.empresa_lider,EFConsorcios_Empresas.porcentaje_participacion,
					 EFConsorcios_Empresas.valor_contrato_porcentaje
					 from EFConsorcios_Empresas
					inner join EFEmpresas on EFEmpresas.id_empresa=EFConsorcios_Empresas.id_empresa
					where EFConsorcios_Empresas.id_proyecto=".$Proy." and EFConsorcios_Empresas.valores_actuales=1  and EFConsorcios_Empresas.empresa_lider=1";
					$cur_empr_consr=mssql_query($sql_empr_consr);
					
					$datos_empre_lider=mssql_fetch_array($cur_empr_consr);
?>
				<table width="100%"  class="table table-bordered">
					<tr>
					  <th width="1%">Id</th>
					  <th>Fecha Inicio</th>
					  <th>Fecha Finalizaci&oacute;n</th>
						<th>Valor Facturado</th>
					  <th>Valor Facturado % Participaci&oacute;n 
                      <i class="glyphicon glyphicon-info-sign" title="Valor calculado con el % de participacion de la empresa lider. &#13; Empresa lider: <?=$datos_empre_lider["empresa"] ?> &#13; % participaci&oacute;n: <?=$datos_empre_lider["porcentaje_participacion"] ?> % &#13; Formula: &#13;  Valor Facturado % Participaci&oacute;n= ((Valor Facturado)*(% participaci&oacute;n))/100 "></i> </th>

<?php    
/*
					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
						if($Revision==1)
						{		
?>	                      
					  <td width="1%" style="vertical-align:middle;"></td>
					  <td width="1%" style="vertical-align:middle;"></td>
<?
						}
					}
*/					
?>                      
				  </tr>
<?php

					$total_fact=0;
					$total_porcentaje=0;
					$cur_facturado=mssql_query("select * from EFValores_Facturados where id_proyecto=".$Proy." order by fecha_inicio_facturacion ");
					$cant_reg=mssql_num_rows($cur_facturado);
					$z=1;
//echo $sqlIn1."<br>".mssql_get_last_message()."-----<BR>";							
					while($datos_facturado=mssql_fetch_array($cur_facturado))
					{
						$total_fact+=( (int) $datos_facturado["valor_facturado"] );
						$total_valorF_porce=( ( (int) $datos_facturado["valor_facturado"] ) * ( (float) $datos_empre_lider["porcentaje_participacion"]) )/100;
						$total_porcentaje+=$total_valorF_porce;
?>                  
					<tr>
						<td><?=$datos_facturado["id_valor_facturado"] ?></td>                    
						<td><?=$datos_facturado["fecha_inicio_facturacion"] ?></td>
						<td><?=$datos_facturado["fecha_ultima_facturacion"] ?></td>
						<td>$ <?=number_format ($datos_facturado["valor_facturado"], 0, ',', '.'); ?></td>
						<td>$ <?=number_format ($total_valorF_porce, 0, ',', '.'); ?></td>
<?php    
/*
					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
						if($Revision==1)
						{		
?>		                             
						<td width="1%" style="vertical-align:middle;">
								<a href="#" onClick="MM_openBrWindow('upFacturacion.php?Proy=<?=$Proy ?>&acc=2&idF=<?=$datos_facturado["id_valor_facturado"] ?>','EF2','scrollbars=yes,resizable=yes,width=600,height=650');"> <i class="glyphicon glyphicon-pencil"></i> </a>                        
                        
                        </td>
<?php    
						}
					}
				
				//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
				if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
				{
					//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
					if($Revision==1)
					{		
?>		                             
						<td width="1%" style="vertical-align:middle;">
<?php
						//SI SE ESTA RECORRIENDO EL ULTIMO REGISTRO, SE MUESTRA EL BOTON DE ELMINIACION
						if($cant_reg==$z)
						{
?>
                                           
     	                   <a href="#"  onClick="MM_openBrWindow('upFacturacion.php?Proy=<?=$Proy ?>&acc=3&idF=<?=$datos_facturado["id_valor_facturado"] ?>','EF2','scrollbars=yes,resizable=yes,width=600,height=650');" > <i class="glyphicon glyphicon-remove"> </i> </a>
<?php
						}
?>                        
                        </td>
<?php
					}
				}
*/				
?>                        
					</tr>
<?php
						$z++;
					}
?>                    
					<tr>
						<th colspan="3">Totales</th>
<?php
						$style="";
						//SI EL VALOR DEL CONTRATO ES MENOR A AL FACTURADO
						if( ((int) $datos_info["valor_contrato"])< $total_fact)
							$style='style="color:red;"';
?>                        
						<td <?=$style ?> >$ <?=number_format ($total_fact, 0, ',', '.'); 
						
						if( ((int) $datos_info["valor_contrato"])< $total_fact)
						{
						?>
							<i class="glyphicon glyphicon-info-sign" title="El valor facturado ha superado el valor del proyecto. "></i>                        <?php
						}
						?> 
                        
                        </td>
						<td>$ <?=number_format ($total_porcentaje, 0, ',', '.'); ?></td>
<?php    
/*
					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
						if($Revision==1)
						{		
?>	                        
						<td colspan="2" style="vertical-align:middle;"></td>
<?php
						}
					}
*/					
?>                        
					</tr>
<?php    
/*
					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
						if($Revision==1)
						{	
						
?>		                    
					<tr>
						<td colspan="7" align="right"> 
<?
							$cant_consor=0;
							//SI EL TIPO DE EJECUCION ES CONSORCIO O UNION TEMPORAL
							if(($datos_info["id_tipo_ejecucion"]==2) || ($datos_info["id_tipo_ejecucion"]==3))
							{
								//SE VALIDA QUE SE HALLA DEFINIDO EL CONSORCIO O UNION TEMPORAL
								$cur_consor=mssql_query("select * from EFConsorcios_Empresas  where id_proyecto=".$Proy." and valores_actuales=1 ");
								$cant_consor=mssql_num_rows($cur_consor);																
							}
							else //SI EL TIPO DE EJECUCION E INDIVIDUAL
								$cant_consor=1;
							
							//SI S DEFINIO EL CONSORCIO
							if($cant_consor>0)
							{
?>                        
  		                      	<button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addFacturacion.php?Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=600,height=650');" >Nueva Facturaci&oacute;n</button>    
<?
							}
							else
							{
?>
								<button type="button" class="btn" title="No se puede asociar facturaci&oacute;n, por que no se ha definido el consorcio en el proyecto." >Nueva Facturaci&oacute;n</button>  
<?php						
							}
					
?>                            
                         </td>
					</tr>
<?php
						}
					}
*/					
?>                        
				</table>
<?php				
			}			
						
?>
          </div>                
<?php
	include("inferior.php"); 	
?>