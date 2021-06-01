<?php 

include("encabezado.php"); 
//tipo=1 (para ventanas principales)
banner(1);
include("menu.php"); 

	//TITULOS ASOCIADOS A CADA SECCION, ACORDE CON EL VALOR DE LA VARIABLE $sec
	// 1 (OPCION DEL MENU PRINCIPAL) - 1 (ITEM DEL MENU) - 1 (ITEM DEL SUB MENU)
	$titulo="";
	if(($sec=="1-1-1")||( $sec==""))
		$titulo="Informaci&oacute;n general";
	if($sec=="1-1-2")
		$titulo="Encargados";
	if($sec=="1-1-3")
		$titulo="Certificados";
	if($sec=="1-1-4")
		$titulo="Soportes";		
	if($sec=="1-1-5")
		$titulo="Ubicaci&oacute;n";				
	if($sec=="1-1-6")
		$titulo="Suspensiones y Reanudaciones";		
	if($sec=="1-1-7")
		$titulo="Liquidaci&oacute;n";					


?>

<div class="container" >

        <div  class="titulo_secccion">
                Definici&oacute;n del proyecto - <?=$titulo ?>
                <div class="espacioAzul">
                 </div>
        </div>


   <?
   		//SECCION DE LA PAGINA QUE SE DEBE MOSTRAR, DEPENDIENDO DE LA OPCION SELECCIONADA EN EL MENU
		//$sec = 1-1-1 "Informacion general"
		//$sec = 1-1-2 "Encargados"
		//$sec = 1-1-3 "Certificados"
		//$sec = 1-1-4 "Soportes"						
   		if(($sec=="1-1-1")|| ($sec=="") )
		{

			$permitir="";						
			//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
			if($Revision==1)
			{
				//SI EL TIPO DE PROYECTO ES FACTURABLE
				if($Tipo_Proyecto==4)
				{
					//EL ESTADO DEL PROYECTO ES EN EJECUCION
					if($Estado_Proyecto==3)
					{
						$permitir="si";
					}
					else
						$permitir="no";
				}		
				else
				{
					$permitir="si";
				}				
			}
			else
			{
				$permitir="no";
			}			
			
			//$tabla VARIABLE UTILIZADA EN EL ARCHIVO tablas_info_general.php
			// 1= MOSTRARA LA TABLA DE INFORMACION GENERAL. CON LA INFORMACION COMPLETA
			// 2= MOSTRARA LA TABLA DE INFORMACION GENERAL. CON LA INFORMACION RESUMIDA
			$tabla=1;
			// tablas_info_general.php ARCHIVO QUE CONTIENE LAS TABLA DE INFORMACION GENERAL COMPLETA Y RESUMIDA
			include("tablas_info_general.php");
			
			
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
			//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
			if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
			{
				//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
				if($permitir=="si")				
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
				}
?>                
                    
                 </td>
<?php
			}

			//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
			if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
			{
				//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
				if($permitir=="si")								
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
				//SI NO HAY REGISTROS DE INFORMACION ADICIONAL,M HABILITA EL BOTON DE ADICION
				if($cant_reg==0)
				{
?>            
<?php    
					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
						if($permitir=="si")				
						{					
?>	
	                 <tr>
                        <td colspan="4" align="right"><button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addinfoA.php?Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=780,height=580');" >Informaci&oacute;n Adicional</button></td>
                     </tr>
<?php
						}
					}
				}
?>             
        </table>
    
<?PHP
		}
		
		//SE MUESTRA LA TABLA DE RESUMEN, CUANDO NO SE ESTA EN LA OPCION "infromacion general" DEL MENU
		if(( $sec!="") && ($sec!="1-1-1") )
		{
			//$tabla VARIABLE UTILIZADA EN EL ARCHIVO tablas_info_general.php
			// 1= MOSTRARA LA TABLA DE INFORMACION GENERAL. CON LA INFORMACION COMPLETA
			// 2= MOSTRARA LA TABLA DE INFORMACION GENERAL. CON LA INFORMACION RESUMIDA
			$tabla=2;
			// tablas_info_general.php ARCHIVO QUE CONTIENE LA TABLA DE INFORMACION GENERAL COMPLETA Y RESUMIDA
			include("tablas_info_general.php");			
		}
				
		//$sec = 1-1-2 "Encargados"		
   		if($sec=="1-1-2")
		{	
			$permitir="";						
			//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
			if($Revision==1)
			{
				//SI EL TIPO DE PROYECTO ES FACTURABLE
				if($Tipo_Proyecto==4)
				{
					//EL ESTADO DEL PROYECTO ES EN EJECUCION
					if($Estado_Proyecto==3)
					{
						$permitir="si";
					}
					else
						$permitir="no";
				}		
				else
				{
					$permitir="si";
				}				
			}
			else
			{
				$permitir="no";
			}
		
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
			//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
			if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
			{
		
					//SI POR PERFILAMIENTO SE HABILITA EL BOTON
					if($permitir=="si")				
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
/*			
			//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
			if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
			{			
?>                    
                    
                    <td width="1%" rowspan="<?=(mssql_num_rows($cursorInOr)+3) ?>" style="vertical-align:middle;">
<?php
				//SI HAY REGISTROS DE INFORMACION DE ENCARGADOS, HABILITA EL BOTON DE ADICION
				if($cant_reg>0)
				{

	
							//SI POR PERFILAMIENTO SE HABILITA EL BOTON
							if($permitir=="si")				
							{										
?>	           
                    <a href="#"  onClick="MM_openBrWindow('upEncargados.php?Proy=<?=$Proy ?>&acc=3','EF2','scrollbars=yes,resizable=yes,width=780,height=500');" > <i class="glyphicon glyphicon-remove"> </i> </a>
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
				//SI NO HAY REGISTROS DE INFORMACION DE ENCARGADOS, HABILITA EL BOTON DE ADICION
				if($cant_reg==0)
				{
   
					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI POR PERFILAMIENTO SE HABILITA EL BOTON
						if($permitir=="si")				
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
?>		                 
               </table>
<?
		}
		
		//$sec = 1-1-3 "Certificados"		
   		if($sec=="1-1-3")
		{
			
			$permitir="";						
			//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
			if($Revision==1)
			{
				//SI EL TIPO DE PROYECTO ES FACTURABLE
				if($Tipo_Proyecto==4)
				{
					//EL ESTADO DEL PROYECTO ES EN EJECUCION
					if($Estado_Proyecto==3)
					{
						$permitir="si";
					}
					else
						$permitir="no";
				}		
				else
				{
					$permitir="si";
				}				
			}
			else
			{
				$permitir="no";
			}			
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
                <td></td>
                <td></td>
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

                            //SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
                            if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
                            {
								//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
								if($permitir=="si")				
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
        ?>                                      
                            </tr>
        <?php
                    }


					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
						if($permitir=="si")				
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
                             
  		                          	<button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addDocumento.php?Proy=<?=$Proy ?>&no=<?=$datos_certi["id_certificado"]	 ?>','EF2','scrollbars=yes,resizable=yes,width=580,height=520');" >Nuevo Documento</button>
<?
								
?>                                  
                                </td>
						 </tr>  
		<?php
								}
							}
						}				
					}
				
?>  
                        </table>                                      
                </td>
<?php

				//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
				if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
				{
					//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
					if($permitir=="si")				
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
				//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
				if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
				{			
					//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
					if($permitir=="si")				
					{
										
		?>
						<td width="1%" rowspan="<?=$cant_filas ?>" style="vertical-align:middle;">
		<?php
						//SI SE HAN ASOCIADO FECHAS 
						if(($datos_certi["fecha_solicitud_certificado"]!="") || ($datos_certi["fecha_recepcion_certificado"]!="") || ($datos_certi["fecha_facturacion_certificado"]!=""))
						{
		/*
							//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
							if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
							{
								*/
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
			}
?>                
             </tr>
		<?php
			
			}
						
			//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
			if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
			{
				//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
				if($permitir=="si")				
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
			
?>             
           </table>
       
<?php
		}
		
		
		//$sec = 1-1-4 "Soportes"	
   		if($sec=="1-1-4")
		{	
			$permitir="";						
			//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
			if($Revision==1)
			{
				//SI EL TIPO DE PROYECTO ES FACTURABLE
				if($Tipo_Proyecto==4)
				{
					//EL ESTADO DEL PROYECTO ES EN EJECUCION
					if($Estado_Proyecto==3)
					{
						$permitir="si";
					}
					else
						$permitir="no";
				}		
				else
				{
					$permitir="si";
				}				
			}
			else
			{
				$permitir="no";
			}							
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

?>
                    <tr>
                      <td><?=$datos_soporte["tipo_soporte"] ?></td>
<?php
					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
						if($permitir=="si")				
						{					
					
?>               

                      <td width="1%" style="vertical-align:middle;"><a href="#"  onClick="MM_openBrWindow('delSoporte.php?Proy=<?=$Proy ?>&sop=<?=$datos_soporte["id_soporte"] ?>','EF2','scrollbars=yes,resizable=yes,width=780,height=350');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>

<?php
						}
					}
?>
                   </tr>
<?php					
				}

				//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
				if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
				{
					//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
					if($permitir=="si")				
					{				
?>	
            <tr>
                <td colspan="2" align="right"><button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addSoporte.php?Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=780,height=350');" >Nuevo Soporte</button></td>
            </tr>
<?php
					}
				}
?>            
  </table>
<?php
		}
		
		//UBICACION 
   		if($sec=="1-1-5")
		{
			
			$permitir="";						
			//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
			if($Revision==1)
			{
				//SI EL TIPO DE PROYECTO ES FACTURABLE
				if($Tipo_Proyecto==4)
				{
					//EL ESTADO DEL PROYECTO ES EN EJECUCION
					if($Estado_Proyecto==3)
					{
						$permitir="si";
					}
					else
						$permitir="no";
				}		
				else
				{
					$permitir="si";
				}				
			}
			else
			{
				$permitir="no";
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
                    <th width="1%">&nbsp;</th>
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
?>                                        
                                    </tr>
                    <?php
						$z=1;
                    }
					
					if(mssql_num_rows($cur_ubica)==0)
					{
						//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
						if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
						{
							//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
							if($permitir=="si")				
							{						
                    ?>  
                    
                        <tr>
                            <td colspan="4" align="right"><button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addUbicacion.php?Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=780,height=450');" >Definir Ubicaci&oacute;n</button></td>
                        </tr>                                          
<?php
							}
						}
					}
?>                        
             </table>

<?php					
		}

		//$sec = 1-1-6 "Suspensiones", SI EL TIPO DE PROYECTO ES FACTURABLE
   		if(($sec=="1-1-6") && ($Tipo_Proyecto==4))
		{	
		
			$permitir="";						
			//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
			if($Revision==1)
			{
				//SI EL TIPO DE PROYECTO ES FACTURABLE
				if($Tipo_Proyecto==4)
				{
					//EL ESTADO DEL PROYECTO ES EN EJECUCION O SUSPENDIDO
					if(($Estado_Proyecto==3)||($Estado_Proyecto==5))
					{
						$permitir="si";
					}
					else
						$permitir="no";
				}		
				else
				{
					$permitir="si";
				}				
			}
			else
			{
				$permitir="no";
			}			
?>						
  <div class="titulos">
				Suspensiones y Reanudaciones
			</div>
	  <table width="100%" class="table table-bordered" >
            	<tr>
            		<th width="1%">Tipo</th>
            		<th width="20%">Documento</th>
            		<th width="15%">Fecha de Inicio <br>Suspenci&oacute;n</th>
            		<th width="15%">Fecha Finalizaci&oacute;n <br>Suspenci&oacute;n</th>
            		<th width="10%">Fecha de Reanudaci&oacute;n</th>
            		<th>Observaciones</th>
            		<th width="1%"></th>
            		<th width="1%"></th>
            	</tr>
<?php
					//CONUSLTA LAS Suspensiones y Reanudaciones 
					$sql_suspen_eanu="select * from (
						select 1 tipo, id_suspencion id,id_proyecto, CONVERT(varchar, fecha_inicio,101) fecha_inic, CONVERT(varchar,fecha_finalizacion,101) fecha_finalizaci, '' fech_reanud,observaciones,url_doc_suspencion,id_suspencion_padre,id_reanudacion,fechaGraba, nombre_doc_suspencion nombre_doc 
						 from EFSuspenciones where id_proyecto=".$Proy."
						union
						select 2 tipo,id_reanudacion,id_proyecto, '' , '' ,CONVERT(varchar,fecha,101) fech_reanud,observaciones,url_doc_reanudacion,'','',fechaGraba, nombre_doc_reanudacion from EFReanudaciones where id_proyecto=".$Proy."
					)A order by fechaGraba";
					$cursor_suspen_eanu=mssql_query($sql_suspen_eanu);
					$cant_reg=mssql_num_rows($cursor_suspen_eanu);
					$z=1;
					$tipo_ult_rgistro=0;
					while($datos_suspen_eanu=mssql_fetch_array($cursor_suspen_eanu))
					{
?>                

                        <tr class="<? if($datos_suspen_eanu["tipo"]==1) { echo "warning"; } if($datos_suspen_eanu["tipo"]==2) { echo ""; } ?> TR" >
                            <td><? if($datos_suspen_eanu["tipo"]==1) { echo "S"; }
								   if($datos_suspen_eanu["tipo"]==2) { echo "R"; }
							 ?></td>
                            <td onclick="window.open('<?=$datos_suspen_eanu["url_doc_suspencion"]; ?>','_blank')"   >
                            	<span style="cursor:pointer; " >
                                    <i class="glyphicon glyphicon-file"></i>
                                        <?=$datos_suspen_eanu["nombre_doc"]; ?>
								</span>
                            </td>
                            <td><?=$datos_suspen_eanu["fecha_inic"]; ?></td>
                            <td><?=$datos_suspen_eanu["fecha_finalizaci"]; ?></td>
                            <td><?=$datos_suspen_eanu["fech_reanud"]; ?></td>
                            <td><?=$datos_suspen_eanu["observaciones"]; ?></td>

	                            <?php 
									//SUSPENCION
									if($datos_suspen_eanu["tipo"]==1)
									{
										$pagina="upSuspen.php";
									}
									//REANUDACION
									if($datos_suspen_eanu["tipo"]==2)
									{
										$pagina="upReanu.php";
									}
								 ?>                            
                            <td>
<?php
									if($cant_reg==$z)
									{
										//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
										if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
										{
											//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
											if($permitir=="si")				
											{		
												//SUSPENCION
												if($datos_suspen_eanu["tipo"]==1)
												{
													//SI EL ESTADO DEL PROYECTO ES "SUSPENDIDO" Y EL TIPO DE PROYECTO ES "FACTURABLE"
//													if((($Estado_Proyecto==5) )&& ($Tipo_Proyecto==4))	
													{
	?>
											<a href="#" onClick="MM_openBrWindow('<?=$pagina ?>?Proy=<?=$Proy ?>&acc=2&id=<?=$datos_suspen_eanu["id"]	 ?>','EF2','scrollbars=yes,resizable=yes,width=480,height=685');"> <i class="glyphicon glyphicon-pencil"></i> </a>                            
	<?php
													}
												}
												//REANUDACION
												if($datos_suspen_eanu["tipo"]==2)
												{			
													//SI EL ESTADO DEL PROYECTO ES "EN EJECUCION" Y EL TIPO DE PROYECTO ES "FACTURABLE"
//													if((($Estado_Proyecto==3) )&& ($Tipo_Proyecto==4))	
													{
	?>
														<a href="#" onClick="MM_openBrWindow('<?=$pagina ?>?Proy=<?=$Proy ?>&acc=2&id=<?=$datos_suspen_eanu["id"]	 ?>','EF2','scrollbars=yes,resizable=yes,width=480,height=640');"> <i class="glyphicon glyphicon-pencil"></i> </a>                            
	<?php																																				
													}
												}
											}
										}
										
									}
?>                                    
                            </td>
                            <td>
<?php
									if($cant_reg==$z)
									{
										//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
										if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
										{
											//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
											if($permitir=="si")				
											{		
												//SUSPENCION
												if($datos_suspen_eanu["tipo"]==1)
												{
													//SI EL ESTADO DEL PROYECTO ES "SUSPENDIDO" Y EL TIPO DE PROYECTO ES "FACTURABLE"
													if((($Estado_Proyecto==5) )&& ($Tipo_Proyecto==4))	
													{																					
?>                            
														<a href="#" onClick="MM_openBrWindow('<?=$pagina ?>?Proy=<?=$Proy ?>&acc=3&id=<?=$datos_suspen_eanu["id"]	 ?>','EF2','scrollbars=yes,resizable=yes,width=480,height=685');"> <i class="glyphicon glyphicon-remove"></i> </a>                              
<?php
													}
												}

												//REANUDACION
												if($datos_suspen_eanu["tipo"]==2)
												{		
												/*	
													//SI EL ESTADO DEL PROYECTO ES  "EN EJECUCION" Y EL TIPO DE PROYECTO ES "FACTURABLE"
													if((($Estado_Proyecto==3) )&& ($Tipo_Proyecto==4))	
													*/
													//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
													if($permitir=="si")															
													{
	?>
														<a href="#" onClick="MM_openBrWindow('<?=$pagina ?>?Proy=<?=$Proy ?>&acc=3&id=<?=$datos_suspen_eanu["id"]	 ?>','EF2','scrollbars=yes,resizable=yes,width=480,height=640');"> <i class="glyphicon glyphicon-remove"></i> </a>                         
	<?php																																				
													}
												}												
												
											}
										}
									}
									$z++;
?>  
                            </td>
                        </tr>
<?php
						//ALMACENA EL TIPO "1=SUSPENCION, 2=REANUDACION" DEL ULTIMO REGISTRO ALMACENADO
						$tipo_ult_rgistro=$datos_suspen_eanu["tipo"];
					}
?>                
            	<tr>
            	  <td colspan="8" align="right">
            	    <?php


					//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
					if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
					{
						//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
						if($permitir=="si")				
						{				
							//SI EL ESTADO DEL PROYECTO ES "EN EJECUCION" O "SUSPENDIDO" Y EL TIPO DE PROYECTO ES "FACTURABLE"
							if((($Estado_Proyecto==3)|| ($Estado_Proyecto==5) )&& ($Tipo_Proyecto==4))	
							{											
?>                                        
            	    <button class="btn btn-primary" onClick="MM_openBrWindow('addSuspen.php?Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=480,height=685');" >Nueva Suspenci&oacute;n</button> 
            	    <?php
							}
						}
					}
?>                                      
          	    </td>
           	  </tr>

<?php
//echo "Estado_Proyecto ".$Estado_Proyecto." Tipo_Proyecto".$Tipo_Proyecto;
					//SI EL ULTIMO REGISTRO ES UNA SUSPENCION, SE HABILITA EL BOTON DE REANUDACION
					if($tipo_ult_rgistro==1)
					{
						//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
						if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
						{
							//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
							if($permitir=="si")				
							{			
								//SI EL ESTADO DEL PROYECTO ES "SUSPENDIDO" Y EL TIPO DE PROYECTO ES "FACTURABLE"
								if((($Estado_Proyecto==5) )&& ($Tipo_Proyecto==4))	
								{												
?>                    
            	<tr>
            		<td colspan="8" align="right">
                    <button class="btn btn-primary" onClick="MM_openBrWindow('addReanu.php?Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=480,height=640');">Nueva Reanudaci&oacute;n</button>
                    </td>
           		</tr>                    
<?php
								}
							}
						}
					}
					
?>					

            </table>
<?            
		}


		
		//$sec = 1-1-7 "Liquidaciones"	
   		if( ($sec=="1-1-7")  && ($Tipo_Proyecto==4) )
		{					

			$permitir="";						
			//SI SE HA REALIZADO LA REVISION DEL PROYECTO // VARIABLE DEL ARCHIVO tablas_info_general.php
			if($Revision==1)
			{
				//SI EL TIPO DE PROYECTO ES FACTURABLE
				if($Tipo_Proyecto==4)
				{
					//EL ESTADO DEL PROYECTO ES EN EJECUCION, TERMINADO o LIQUIDADO
					if(($Estado_Proyecto==3)||($Estado_Proyecto==9)||($Estado_Proyecto==11))
					{
						$permitir="si";
					}
					else
						$permitir="no";
				}		
				else
				{
					$permitir="si";
				}				
			}
			else
			{
				$permitir="no";
			}	

?>
<?php

		$sql_liq="select *, CONVERT(varchar, fecha ,101) fecha_L from EFLiquidacion WHERE id_proyecto=".$Proy;
		$cursor_liq=mssql_query($sql_liq);
		
		$datos_liq=mssql_fetch_array($cursor_liq);
?>   
  <div class="titulos">Liquidaci&oacute;n</div>          
  <table class="table table-bordered" width="100%">
             	<tr>
             		<th width="15%">Documento</th>
<?php
					if($datos_liq["url_doc_liquidacion"]!="")
					{
?>                    
             		<td onclick="window.open('<?=$datos_liq["url_doc_liquidacion"]; ?>','_blank')"   >
                        <span style="cursor:pointer; " >
                            <i class="glyphicon glyphicon-file"></i>
                                <?=$datos_liq["nombre_doc_liquidacion"]; ?>
                        </span>
                    </td>
<?php
					}
					else
						echo "<td></td>";
?>                    
             		<td width="1%" rowspan="4" style="vertical-align:middle;">
<?
			if(mssql_num_rows($cursor_liq)!=0)
			{
				//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
				if($permitir=="si")				
				{					
?>                          
						<a href="#" onClick="MM_openBrWindow('upLiquid.php?Proy=<?=$Proy ?>&acc=2&id=<?=$datos_liq["id_liquidacion"]	 ?>','EF2','scrollbars=yes,resizable=yes,width=480,height=685');"> <i class="glyphicon glyphicon-pencil"></i> </a>                        
<?
				}
			}
?>                        
                    </td>
             		<td width="1%" rowspan="4"  style="vertical-align:middle;">
<?
			if(mssql_num_rows($cursor_liq)!=0)
			{
				//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
				if($permitir=="si")				
				{					
?>                          
						<a href="#" onClick="MM_openBrWindow('upLiquid.php?Proy=<?=$Proy ?>&acc=3&id=<?=$datos_liq["id_liquidacion"]	 ?>','EF2','scrollbars=yes,resizable=yes,width=480,height=685');"> <i class="glyphicon glyphicon-remove"></i> </a>                      
<?
				}
			}
?>                        
                    </td>
             	</tr>
             	<tr>
             		<th width="15%">Fecha</th>
             		<td><?=$datos_liq["fecha_L"]	 ?></td>
           		</tr>
             	<tr>
             		<th width="15%">Valor</th>
             		<td>                    
						<?php
							if($datos_liq["valor"]!="")
							  echo "$ ".number_format ($datos_liq["valor"], 0, ',', '.'); 
						?>
                    </td>
           		</tr>
             	<tr>
             		<th width="15%">Observaciones</th>
             		<td><?=$datos_liq["observaciones"]	 ?></td>
           		</tr>
<?
			if(mssql_num_rows($cursor_liq)==0)
			{
				//SI POR EL ESTADO DEL PROYECTO SE HABILITA EL BOTON
				if($permitir=="si")				
				{					
?>                
             	<tr>
             		<td colspan="4" align="right">
                    <button class="btn btn-primary" onClick="MM_openBrWindow('addLiquid.php?Proy=<?=$Proy ?>','EF2','scrollbars=yes,resizable=yes,width=480,height=640');">Nueva Liquidaci&oacute;n</button>	</td>
           		</tr>
<?php
				}
			}
		}				
?>  
        </table> 
   </div>
<?php
	include("inferior.php"); 
?>