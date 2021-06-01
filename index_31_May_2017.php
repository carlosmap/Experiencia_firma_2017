<?php 

include("encabezado.php"); 
//tipo=1 (para ventanas principales)
banner(1);
?>

	<div class="container" style=" text-align:center"  >
		<form class="form-horizontal"  name="form1" id="form1" method="post" >
<?php
	//SI EL PERFIL ES DE ADMINISTRADOR
	if ($_SESSION["sesPerfilUsuarioExpFir"]==1)
	{
?>        
          <div class="form-group">
               <div style=" display:inline-block;">
                    <label for="exampleInputName2">Revisi&oacute;n</label> 
               </div>
               <div style=" display:inline-block;">     
				    <select class="form-control" id="Revision" name="Revision" onChange="document.form1.submit()">
                	      <option value="1" <? if( ($Revision==1) || ($Revision=="") ) { echo "selected"; } ?> >Todos</option>               
                	      <option value="2" <? if( ($Revision==2) ) { echo "selected"; } ?> >Pendientes de Revisi&oacute;n [P.R.]</option>                                         
                    </select>
               </div>
          </div>
<?php
	}
?>          
          <div class="form-group">
           <div style=" display:inline-block;">
           		<label for="exampleInputName2">Proyecto</label> 
           </div>
           <div style=" display:inline-block;">           
                <select class="form-control" id="Proyecto" name="Proyecto" onChange="document.form1.submit()">
                  <option>Seleccione un proyecto</option>
    <?php
				$sql_proy="select EFProyectos.id_proyecto, EFProyectos.revision, nombre_largo_proyecto, certificado_SEVEN from EFProyectos
    inner join EFNombres_Proyectos on EFProyectos.id_proyecto=EFNombres_Proyectos.id_proyecto and nombre_actual=1 ";
	
				//SI EL PERFIL DEL USUARIO ES 2=USUARIO DE TRABAJO, 3=USUARIO DE CONSULTA
				//CONSULTA LOS PROYECTOS QUE YA SE ENCUENTRAN REVISADOS
				//SI EL PERFIL DEL USUARIO ES  1=ADMINISTRADOR, CONSULTA TODOS LOS PROYECTOS
				if( ($_SESSION["sesPerfilUsuarioExpFir"]==2) || ($_SESSION["sesPerfilUsuarioExpFir"]==3) )				
					$sql_proy.=" and EFProyectos.revision=1";
					
				//PENDIENTES DE REVISION	
				if(($Revision==2))
					$sql_proy.=" and revision=0 ";
					
			//	$sql_proy.=" order by revision, nombre_largo_proyecto ";
			$sql_proy.=" order by certificado_SEVEN desc ";
			
                $cur_query=mssql_query($sql_proy);
                while($datos=mssql_fetch_array($cur_query))
                {
                    $sel="";
                    if($Proyecto== $datos["id_proyecto"])
                        $sel="selected";				
					
						
                    $revisio="";
					//SI EL PROYECTO ESTA PENDIENTE DE REVISION
                    if($datos["revision"]=="0")
                        $revisio="[P.R.]";													
    ?>
                  <option value="<?=$datos["id_proyecto"] ?>"  <?=$sel ?> ><?=$datos["certificado_SEVEN"] ?> - <?=$datos["nombre_largo_proyecto"] ?> <?=" ".$revisio ?></option>
    <?php				
                }
    ?>  
                </select>   
                <input type="hidden" value="18" id="Proy" name="Proy">        
            </div> 
<!--            <input type="text" class="form-control" id="exampleInputName2" placeholder="Nombre"> -->
          </div>
		</form>          
<!--          <button type="submit" class="btn btn-primary">Consultar</button> -->

   </div>
<!--
	<div class="container" style=" text-align:center"  >

          <div class="form-group">
            <div style=" display:inline-block;">
              <label for="exampleInputName2"> Revisi&oacute;n</label>
            </div>
            <div style=" display:inline-block; display:inline-block; width:276px; margin-top:10px;  ">
                <select class="form-control" id="Revision" name="Revision" onChange="document.form1.submit()" >
                  <option <? if($Revision=="") { echo "selected"; } ?> value="" >Todos</option>                          
                  <option value="0" <? if($Revision=="0") { echo "selected"; } ?>>Por Revisar</option>
                  <option value="1" <? if($Revision=="1") { echo "selected"; } ?>>Revisado</option>              
                </select>   
	            <input type="hidden" value="18" id="Proy" name="Proy">         
           </div>
          </div>

   </div>   
-->   
  <br>
    <br>  
   <div class="container" >
		<table width="80%" class="table table-bordered"   >
		  <thead>
			<tr>
			  <th width="2%" >Id</th>
			  <th >Proyecto</th>
			  <th width="13%" >C&oacute;d. SEVEN N.</th>
			  <th width="20%" >Cliente</th>
			  <th width="20%" >Empresa</th>
			  <th width="5%" >Revisi&oacute;n</th>
			  <th width="5%" >Estado</th>

		    </tr>

		  </thead>
		  <tbody>
<?php
			$cur_query=mssql_query("select revision, codigo_SEVEN, EFProyectos.id_proyecto, nombre_largo_proyecto, EFClientes.cliente ,EFEmpresas.empresa, EFEstados_Proy.estado_proyecto 
			from EFProyectos
			inner join EFNombres_Proyectos on EFProyectos.id_proyecto=EFNombres_Proyectos.id_proyecto and nombre_actual=1
			inner join EFClientes_Proyectos on EFClientes_Proyectos.id_proyecto=EFProyectos.id_proyecto and cliente_actual=1
			inner join EFClientes on EFClientes_Proyectos.id_cliente=EFClientes.id_cliente
			
			inner join EFEmpresas on EFProyectos.id_empresa=EFEmpresas.id_empresa and EFEmpresas.tipo=1
			inner join EFEstados_Proy_Proyectos on EFProyectos.id_proyecto=EFEstados_Proy_Proyectos.id_proyecto and EFEstados_Proy_Proyectos.estado_actual=1
			inner join EFEstados_Proy on EFEstados_Proy_Proyectos.id_estado_proy=EFEstados_Proy.id_estado_proy 
			WHERE EFProyectos.id_proyecto=".$Proyecto
			);
			while($datos=mssql_fetch_array($cur_query))
			{
?>          

                    <tr onclick="location.href='infoG.php?Proy=<?=$datos["id_proyecto"]; ?>'" class="TR"  style="cursor:pointer; "  >
	                    
                              <a href="ingo.php"><th scope="row"><?=$datos["id_proyecto"] ?></th></a> 
                              <td><?=$datos["nombre_largo_proyecto"] ?></td>
                              <td><?=$datos["codigo_SEVEN"] ?></td>
                              <td><?=$datos["cliente"] ?></td>
                              <td><?=$datos["empresa"] ?></td>
                              <td align="center">
								  <? if($datos["revision"]==1){ ?> 
										<i class="glyphicon glyphicon-ok-circle"  title="Revisado"> </i>                                    
                                  <?php } ?>
                              </td>
                              <td><?=$datos["estado_proyecto"] ?></td>                     
		                                          
                    </tr>

<?php
			}
?>			
		  </tbody>
	</table> 
<?php    
//echo "**********************".$_SESSION["sesPerfilUsuarioExpFir"];
	//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
	if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
	{
?>	

	<div class="container">    
    	<div style="text-align:right" >
	        <button type="button" class="btn btn-primary" onClick="MM_openBrWindow('xlsInfoGeneralExpF.php','EF2','scrollbars=yes,resizable=yes,width=600,height=650');" > Consolidado Total en XLS</button>
		    <button type="button" class="btn btn-primary" onClick="MM_openBrWindow('addProy.php','EF2','scrollbars=yes,resizable=yes,width=600,height=650');" >Nuevo Proyecto</button>
      </div>
	</div>      

<?php
	}
?>    
</div>

<?php
	include("inferior.php"); 
?>