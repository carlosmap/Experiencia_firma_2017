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
            <div class="form-group centrar">     
            	<div class="col-sm-4">
                    <div class="input-group">
                      <input type="text" class="form-control" placeholder="Buscar"  id="buscar" name="buscar" value="<?=$buscar ?>" >
                      <span class="input-group-addon" id="basic-addon1"><i class="glyphicon glyphicon-search"></i></span>
                    </div>             
				</div>           
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
			  <th width="2%" >Id</th>
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
	                    
                              <a href="ingo.php"><th scope="row"><?=$datos["id_proyecto"] ?></th></a> 
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
					<td align="center" colspan="7" style="text-align:center;">No se encontraron registros</td>
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

			$('<li><a href="index_paginacion.php?pagina=1" class="first_link"><</a></li>').appendTo(paginador);
			$('<li><a href="index_paginacion.php?pagina=<?=($pagina-1) ?>" class="prev_link">&laquo;</a></li>').appendTo(paginador);

			var pag = 0;
			while(totalPaginas > pag)
			{
				$('<li><a href="index_paginacion.php?pagina='+(pag+1)+'" class="page_link">'+(pag+1)+'</a></li>').appendTo(paginador);
				pag++;
			}


			if(numerosPorPagina > 1)
			{
				$(".page_link").hide();
				$(".page_link").slice(0,numerosPorPagina).show();
			}

			$('<li><a href="index_paginacion.php?pagina=<?=($pagina+1) ?>" class="next_link">&raquo;</a></li>').appendTo(paginador);
			$('<li><a href="index_paginacion.php?pagina='+totalPaginas+'" class="last_link">></a></li>').appendTo(paginador);

			paginador.find(".page_link:first").addClass("active");
			paginador.find(".page_link:first").parents("li").addClass("active");

			paginador.find(".prev_link").hide();

			cargaPagina(<?=($pagina-1) ?>);
		}

		function cargaPagina(pagina)
		{
//			var desde = pagina * itemsPorPagina;

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
	include("inferior.php"); 
?>