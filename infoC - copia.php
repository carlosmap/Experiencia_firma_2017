<?php 

include("encabezado.php"); 
//tipo=1 (para ventanas principales)
banner(1);
include("menu.php"); 
			//TABLA DE RESUMEN
?>
		<div class="container" >
<?php        
			//$tabla VARIABLE UTILIZADA EN EL ARCHIVO tablas_info_general.php
			// 1= MOSTRARA LA TABLA DE INFORMACION GENERAL. CON LA INFORMACION COMPLETA
			// 2= MOSTRARA LA TABLA DE INFORMACION GENERAL. CON LA INFORMACION RESUMIDA
			$tabla=1;
			// tablas_info_general.php ARCHIVO QUE CONTIENE LAS TABLA DE INFORMACION GENERAL COMPLETA Y RESUMIDA
			include("tablas_info_general.php");
?>            

           <div class="titulos" >
             Informaci&oacute;n adicional
           </div>
           
  <table class="table table-bordered" width="100%">
            <tr>
                <th width="48%">Especialidades</th>
                <th width="48%">Clase</th>
                <td width="1%" rowspan="4" style="vertical-align:middle;"><a href="#" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');"> <i class="glyphicon glyphicon-pencil"></i> </a></td>
                <td width="1%" rowspan="4" style="vertical-align:middle;"><a href="#"  onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>
            </tr>
            <tr>
                <td>ddd</td>
                <td>sss</td>
    </tr>
            <tr>
                <th colspan="2">Objeto</th>
    </tr>
            <tr>
                <td colspan="2">rrr</td>
    </tr>
            <tr>
                <td colspan="4" align="right"><button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" >Informaci&oacute;n Adicional</button></td>
    </tr>
        </table>
        

	  <div class="titulos" >
           Encargados
       </div>    
       
       <table class="table table-bordered" width="100%"  >
       	<tr>
       		<th width="44%">Director</th>
       		<th width="44%">Coordinador</th>
       		<td width="1%" rowspan="4" style="vertical-align:middle;"> <a href="#" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');"> <i class="glyphicon glyphicon-pencil"></i> </a></td>
       		<td width="1%" rowspan="4" style="vertical-align:middle;"><a href="#"  onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>
       	</tr>
       	<tr>
       		<td>ddd</td>
       		<td></td>
   		 </tr>
       	<tr>
       		<th colspan="2">Ordenadores de gasto</th>
   		 </tr>
       	<tr>
       	  <td colspan="2">[1265] Juenaes Perilla</td>
   	     </tr>
       	<tr>
       		<td colspan="4" align="right">
              <button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" >Definir encargados</button>
            </td>
   		 </tr>
       </table>
       
		   <div class="titulos" >
             Certificados
           </div>       
           <table  class="table table-bordered" width="100%"> 
            <tr> 
                <th>Fecha de Solicitud</th>
                <th>Fecha de Recepci&oacute;n</th>
                <th colspan="2">Fecha de Facturaci&oacute;n</th>
                <td width="1%" rowspan="5" style="vertical-align:middle;"><a href="#" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');"> <i class="glyphicon glyphicon-pencil"></i> </a></td>
                <td width="1%" rowspan="5" style="vertical-align:middle;"><a href="#"  onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>
            </tr>
            <tr>
                <td>25/10/2017</td>
                <td>25/10/2017</td>
                <td colspan="2">25/10/2017</td>
             </tr>
            <tr>
                <th colspan="4">Documentos de Recepci&oacute;n</th>
             </tr>
            <tr>
                <td colspan="4"><table class="table table-bordered" width="100%">
                
                    <tr>
                      <td>aaa.txt</td>
                      <td width="1%"><a href="#"  onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>
                    </tr>
                    <tr>
                      <td>bb.doc</td>
                      <td><a href="#"  onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>
                    </tr>
                 
                </table></td>
             </tr>
            <tr>
                <td colspan="4" align="right"><button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" >Nuevo Documento</button></td>
             </tr>        
            <tr>
                <td colspan="6" align="right">              <button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" >Nuevo Certificado</button></td>
             </tr>
           </table>
           
       
           <div class="titulos" >
             Soportes
           </div>       
           
         <table  class="table table-bordered" width="100%"> 
            <tr>
              <td>ssss.pdf</td>
              <td width="1%" style="vertical-align:middle;"><a href="#"  onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>
           </tr>
            <tr>
              <td>aa.txt</td>
              <td style="vertical-align:middle;"><a href="#"  onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>
           </tr>
            <tr>
                <td colspan="2" align="right"><button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" >Nuevo Soporte</button></td>
            </tr>
  		</table>                          
  

          
          
              <div class="titulos" >
                       Valores del proyecto
          	  </div>    
                   
                <table width="100%"  class="table table-bordered">
                    <tr>
                        <th width="25%">Consorcio</th>
                        <th width="25%">Empresas</th>
                        <th width="15%">Empresa Lider</th>
                        <th width="15%">% Participaci&oacute;n</th>
                        <th width="20%">Valor % Participaci&oacute;n</th>
                        <td width="1%" rowspan="3" style="vertical-align:middle;"><a href="#" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');"> <i class="glyphicon glyphicon-pencil"></i> </a></td>
                        <td width="1%" rowspan="3" style="vertical-align:middle;"><a href="#"  onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>
                    </tr>
                    <tr>
                        <td rowspan="2">dd</td>
                        <td>x</td>
                        <td>60%</td>
                        <td>*</td>
                        <td>100</td>
                    </tr>
                    <tr>
                        <td>x</td>
                        <td>40%</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="7" align="right">
							<button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" >Nuevo Consorcio</button>                        
                        </td>
                    </tr>
                </table>
                
                <table width="100%"  class="table table-bordered">
                	<tr>
               		  <th>Empresa</th>
                		<th width="15%">Empresa Lider</th>
                		<th width="25%">% Participaci&oacute;n</th>
                		<th width="20%">Valor % Participaci&oacute;n</th>
               	  </tr>
                	<tr>
                		<td>dd</td>
                		<td>ss</td>
                		<td></td>
                		<td></td>
                	</tr>
                </table>


          <div class="titulos" >
                       Prorrogas
              </div>    

				<table width="100%"  class="table table-bordered">
					<tr>
					  <th width="1%">Id</th>
					  <th>Tipo Prorroga</th>
					  <th>Fecha Inicio</th>
						<th>Fecha Finalizaci&oacute;n</th>
					  <th>Tiempo (meses)</th>
					  <th>Documento Soporte</th>
					  <th>Valor</th>
					  <th>Observaciones</th>
					  <th>Usuario Registra</th>
						<td width="1%" style="vertical-align:middle;" ></td>
						<td width="1%" style="vertical-align:middle;"></td>
				  </tr>
					<tr>
						<td>1</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td width="1%" style="vertical-align:middle;" ><a href="#" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');"> <i class="glyphicon glyphicon-pencil"></i> </a></td>
					  <td width="1%" style="vertical-align:middle;"><a href="#"  onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>
					</tr>
					<tr>
						<td>44</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td width="1%" style="vertical-align:middle;" ><a href="#" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');"> <i class="glyphicon glyphicon-pencil"></i> </a></td>
					  <td width="1%" style="vertical-align:middle;"><a href="#"  onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>
					</tr>
					<tr>
						<td colspan="11" align="right"> <button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" >Nueva Prorroga</button>    </td>
					</tr>
				</table>
                

          <div class="titulos" >
                       Adicionales
          </div>    

				<table width="100%"  class="table table-bordered">
					<tr>
					  <th width="1%">Id</th>
					  <th>Tipo de Adici&oacute;n</th>
					  <th>Valor</th>
						<th>Fecha</th>
					  <th>Documento Soporte</th>
					  <th>Observaciones</th>
					  <th>Usuario Registra</th>                      
					  <td width="1%" style="vertical-align:middle;" ></td>
						<td width="1%" style="vertical-align:middle;"></td>
				  </tr>
					<tr>
						<td>1</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td width="1%" style="vertical-align:middle;" ><a href="#" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');"> <i class="glyphicon glyphicon-pencil"></i> </a></td>
						<td width="1%" style="vertical-align:middle;"><a href="#"  onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>
					</tr>
					<tr>
						<td>44</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td width="1%" style="vertical-align:middle;" ><a href="#" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');"> <i class="glyphicon glyphicon-pencil"></i> </a></td>
						<td width="1%" style="vertical-align:middle;"><a href="#"  onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>
					</tr>
					<tr>
						<td colspan="9" align="right"> <button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" >Nueva Adici&oacute;n</button>    </td>
					</tr>
				</table>                                          
                
          <div class="titulos" >
                       Valores facturados
              </div>    

				<table width="100%"  class="table table-bordered">
					<tr>
					  <th width="1%">Id</th>
					  <th>Fecha Inicio</th>
					  <th>Fecha Finalizaci&oacute;n</th>
						<th>Valor Facturado</th>
					  <th>Valor Facturado % Participaci&oacute;n</th>
					  <td width="1%" style="vertical-align:middle;" ></td>
						<td width="1%" style="vertical-align:middle;"></td>
				  </tr>
					<tr>
						<td>1</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td width="1%" style="vertical-align:middle;" ><a href="#" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');"> <i class="glyphicon glyphicon-pencil"></i> </a></td>
						<td width="1%" style="vertical-align:middle;"><a href="#"  onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>
					</tr>
					<tr>
						<td>44</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td width="1%" style="vertical-align:middle;" ><a href="#" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');"> <i class="glyphicon glyphicon-pencil"></i> </a></td>
						<td width="1%" style="vertical-align:middle;"><a href="#"  onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" > <i class="glyphicon glyphicon-remove"> </i> </a></td>
					</tr>
					<tr>
						<td colspan="7" align="right"> <button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=850,height=350');" >Nueva Facturaci&oacute;n</button>    </td>
					</tr>
				</table>                
          </div>                
<?php
	include("inferior.php"); 	
?>