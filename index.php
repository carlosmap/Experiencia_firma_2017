<?php 

include("encabezado.php"); 
//tipo=1 (para ventanas principales)
banner(1);
?>

<?php    

//SI EL PRFIL DEL USUARIO ES ADMINISTRADOR O USUARIO DE TRABAJO
if( ($_SESSION["sesPerfilUsuarioExpFir"]==1) || ($_SESSION["sesPerfilUsuarioExpFir"]==2) )			    
{
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
 	
   <br>
   <br>  
   <div class="container" >
<?
	if($_SESSION["sesPerfilUsuarioExpFir"]==1) 
	{
?>   
   		<div class="form-group " style="text-align:right" title="M&oacute;dulo Administrativo" >
            <button class="btn btn-primary  btn-lg" onClick="location.href='modAdmin.php'" ><li class="glyphicon glyphicon-cog"></li></button>
   		</div>	
<?
	}
?>        
		<table width="80%" class="table table-bordered"   >
		  <thead>
			<tr>
			  <th >Proyecto</th>
			  <th width="13%" >Certificado SEVEN</th>
			  <th width="20%" >Cliente</th>
			  <th width="20%" >Empresa</th>
			  <th width="5%" >Revisi&oacute;n</th>
			  <th width="5%" >Estado</th>

		    </tr>

		  </thead>
		  <tbody>
<?php
			$cur_query=mssql_query("select certificado_SEVEN, revision, codigo_SEVEN, EFProyectos.id_proyecto, nombre_largo_proyecto, EFClientes.cliente ,EFEmpresas.empresa, EFEstados_Proy.estado_proyecto 
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
	                    
                              <a href="ingo.php"></a> 
                              <td><?=$datos["nombre_largo_proyecto"] ?></td>
                              <td><?=$datos["certificado_SEVEN"] ?></td>
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
}
//SI EL PRFIL DEL USUARIO DE CONSULTA
if( ($_SESSION["sesPerfilUsuarioExpFir"]==3))			    
{
?>
<div class="container" style="text-align:center"   >
        <form class="form-horizontal"  name="form1" id="form1" method="post" action="index.php?pagina=1&buscar=<?=$buscar ?>" >                
            <div class="form-group centrar">     
<!--                <div class="col-sm-4"> -->
                    <div class="input-group">
                      <input type="text" class="form-control" placeholder="Buscar"  id="buscar" name="buscar" value="<?=$buscar ?>" autofocus >
                      <span class="input-group-addon" id="basic-addon1"><i class="glyphicon glyphicon-search"></i></span>
                    </div>             
<!--                </div>           -->
            </div>        
        </form>          
    </div>
<?php

		if(trim($pagina) == ""){
			$pagina = 1;
			$inicio = 0;
		}
		else{
			$inicio = 30*($pagina - 1);
		}
		
		$fin=$inicio+30 ;

		$sql_proy="WITH proyectos AS ( 
		
		select *, ROW_NUMBER() OVER (ORDER BY  t.nombre_largo_proyecto ) AS RowNumber  from 
		(";
			$sql_proy_t="select certificado_SEVEN, revision, codigo_SEVEN, EFProyectos.id_proyecto, nombre_largo_proyecto, EFClientes.cliente ,EFEmpresas.empresa, EFEstados_Proy.estado_proyecto 
			from EFProyectos
			inner join EFNombres_Proyectos on EFProyectos.id_proyecto=EFNombres_Proyectos.id_proyecto and nombre_actual=1
			inner join EFClientes_Proyectos on EFClientes_Proyectos.id_proyecto=EFProyectos.id_proyecto and cliente_actual=1
			inner join EFClientes on EFClientes_Proyectos.id_cliente=EFClientes.id_cliente
			
			inner join EFEmpresas on EFProyectos.id_empresa=EFEmpresas.id_empresa and EFEmpresas.tipo=1
			inner join EFEstados_Proy_Proyectos on EFProyectos.id_proyecto=EFEstados_Proy_Proyectos.id_proyecto and EFEstados_Proy_Proyectos.estado_actual=1
			inner join EFEstados_Proy on EFEstados_Proy_Proyectos.id_estado_proy=EFEstados_Proy.id_estado_proy 
			 where EFProyectos.revision=1
			";
			
			if($buscar!="")
				$sql_proy_t.="  and ( nombre_largo_proyecto like '%".$buscar."%' or certificado_SEVEN like '%".$buscar."%' or cliente like '%".$buscar."%' or empresa like '%".$buscar."%' ) ";
			
			$sql_proy.=$sql_proy_t;

		$sql_proy.="	 ) 
			as t 
		) SELECT RowNumber,* FROM proyectos WHERE RowNumber BETWEEN ".$inicio." and  ".$fin;
		$cur_query=mssql_query($sql_proy);			
		$cur_query_t=mssql_query($sql_proy_t);			
						
		$totalRegistros = mssql_num_rows($cur_query_t);
//echo $sql_proy;							
?>
<div class="container" style="text-align:center;" id="" >
 
        <div class="navegacion">
            <nav aria-label="">
              <ul class="pagination" id="paginador">

              </ul>
            </nav>                
        </div>
      
	    <br>
        <br>                     
	 <table width="80%" class="table table-bordered"   >
	   <thead>
		 <tr>
		   <th >Proyecto</th>
		   <th width="13%" >Certificado SEVEN</th>
		   <th width="20%" >Cliente</th>
		   <th width="20%" >Empresa</th>
		   <th width="5%" >Revisi&oacute;n</th>
		   <th width="5%" >Estado</th>

         </tr>

       </thead>
	   <tbody>

<?php			

			while($datos=mssql_fetch_array($cur_query))
			{
?>          

                 <tr onclick="location.href='infoG.php?Proy=<?=$datos["id_proyecto"]; ?>'" class="TR"  style="cursor:pointer; "  >
	                    
                           <a href="ingo.php"></a> 
                           <td><?=$datos["nombre_largo_proyecto"] ?></td>
                           <td><?=$datos["certificado_SEVEN"] ?></td>
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

			if($totalRegistros==0)
			{
?>
			 <tr>
				 <td align="center" colspan="6" style="text-align:center;">No se encontraron registros</td>
		     </tr>
<?php				
			}
?>  
           </tbody>
     </table> 
  
  </div>

	<script type="text/javascript">

		var paginador;
		var totalPaginas
		var itemsPorPagina = 30;
		var numerosPorPagina = 4;

		function creaPaginador(totalItems)
		{
			paginador = $(".pagination");

			totalPaginas = Math.ceil(totalItems/itemsPorPagina);
//alert(totalPaginas+" **** "+totalItems)			;


			if(totalPaginas>4)
			{

			}
			$('<li><a href="index.php?pagina=1&buscar=<?=$buscar ?>" class="first_link">&laquo;</a></li>').appendTo(paginador);							
			$('<li><a href="index.php?pagina=<?=($pagina-1) ?>&buscar=<?=$buscar ?>" class="prev_link"><</a></li>').appendTo(paginador);
			
			var pag = 0;
			while(totalPaginas > pag)
			{
				$('<li><a href="index.php?pagina='+(pag+1)+'&buscar=<?=$buscar ?>" class="page_link">'+(pag+1)+'</a></li>').appendTo(paginador);
				pag++;
			}


			if(numerosPorPagina > 1)
			{
				$(".page_link").hide();
				$(".page_link").slice(0,numerosPorPagina).show();
			}
			

			if(totalPaginas>4)
			{			
			}
			$('<li><a href="index.php?pagina=<?=($pagina+1) ?>&buscar=<?=$buscar ?>" class="next_link">></a></li>').appendTo(paginador);			
			$('<li title="'+totalPaginas+'" ><a href="index.php?pagina='+totalPaginas+'&buscar=<?=$buscar ?>" class="last_link">&raquo;</a></li>').appendTo(paginador);						
			
			paginador.find(".page_link:first").addClass("active");
			paginador.find(".page_link:first").parents("li").addClass("active");


			if(<?=($pagina) ?>==1)
			{
				paginador.find(".prev_link").hide();
				paginador.find(".first_link").hide();
			}
			
			if(<?=($pagina) ?>==totalPaginas)
			{
				paginador.find(".next_link").hide();
				paginador.find(".last_link").hide();				
			}
			
			paginador.find(".prev_link").hide();

			cargaPagina(<?=($pagina-1) ?>, totalPaginas);
		}

		function cargaPagina(pagina, totPaginas)
		{
//			var desde = pagina * itemsPorPagina;
//alert(totPaginas)			;
//			if(totPaginas>4)
			{
				if(pagina >= 1)
				{
					paginador.find(".prev_link").show();
	
				}
				else
				{
					paginador.find(".prev_link").hide();
				}
	
	
				if(pagina <(totalPaginas- numerosPorPagina))
				{
					paginador.find(".next_link").show();
				}else
				{
					paginador.find(".next_link").hide();
				}
			

				paginador.data("pag",pagina);
			}
			if(numerosPorPagina>1)
			{
				$(".page_link").hide();
				if(pagina < (totalPaginas- numerosPorPagina))
				{
					$(".page_link").slice(pagina,numerosPorPagina + pagina).show();
				}
				else{
					if(totalPaginas > numerosPorPagina)
						$(".page_link").slice(totalPaginas- numerosPorPagina).show();
					else
						$(".page_link").slice(0).show();

				}
			}

			paginador.children().removeClass("active");
			paginador.children().eq(pagina+2).addClass("active");


		}
				
		creaPaginador(<?=$totalRegistros ?>);
	</script>

<?php
}
	include("inferior.php"); 
?>