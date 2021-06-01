<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente

if($acc==2)
	$mens_v="Actualizar Consorcio";	   		
if($acc==3)
	$mens_v="Eliminar Consorcio";	  

banner(2,$mens_v);


if($recarga==2)
{
	mssql_query("begin transaction");	
	
	$cursorIn1="Cursor de insercion";
	
	if($acc==2)
	{
		$consecutivo=0;
		
		//CONSULTA LA SECUENCIA INCREMENTAL DEL CONSORCIO
		$cursorsqlsecu= mssql_query(" select isnull(MAX(id_consecutivo),0) id_consecutivo   from EFConsorcios WHERE id_proyecto=".$Proy."");	
		$daot_secu=mssql_fetch_array($cursorsqlsecu);
		$consecutivo=( (int) $daot_secu["id_consecutivo"]);		
		
		//VERIFICA SI EL NOMBRE DEL CONSORCIO HA CAMBIADO
		if($nomConsorcio!=$NomConso)
		{
			//ACTUALIZA TODOS LOS NOMBRES DE LOS CONSORCIOS REGISTRADOS EN EL PORYECTO 
			$sqlIn1 =" UPDATE EFConsorcios SET nombre_actual_consorcio=0 where id_proyecto=".$Proy;
			$cursorIn1 = mssql_query($sqlIn1);				
			
			//SI CAMBIA EL NOMBRE DEL CONSORCIO, SE INCREMENTA LA SECUENCIA DE CAMBIOS DEL NOMBRE
			$consecutivo++;
			
			if($cursorIn1!="")
			{			
				$sqlIn1 = " INSERT INTO EFConsorcios (id_consorcio, id_proyecto, id_consecutivo, nombre_consorcio, nombre_actual_consorcio,  usuarioGraba, fechaGraba) VALUES";
				$sqlIn1 = $sqlIn1." ( ".$id_consorcio.", ".$Proy.", ".$consecutivo." , '".$NomConso."' , 1, ".$_SESSION["sesUnidadUsuario"].", getdate() ) ";
				$cursorIn1 = mssql_query($sqlIn1);		
			}
		}
	
		if($cursorIn1!="")
		{
			//ACTUALIZA TODAS LAS EMPRESAS DE LOS CONSORCIOS REGISTRADOS EN EL PORYECTO 		
			$sqlIn1 =" UPDATE EFConsorcios_Empresas SET valores_actuales=0 where id_proyecto=".$Proy;
			$cursorIn1 = mssql_query($sqlIn1);	
		}
	
	//echo $sqlIn1."<br>".mssql_get_last_message()."<br>";			
		if($cursorIn1!="")
		{	
			for($i=0;$i<=$cantEmpre;$i++)		
			{
				$Empresa="Empresa".$i;
				$porcentaje="porcentaje".$i;
				//$valorParticipacion="valorParticipacion".$i;
				$lider="0";
				///VERIFICA SI EL REGISTRO QUE SE ESTA RECORRIENDO ES EL SELECCIONADO
				if($filaLider==$i)
					$lider=1;
					
				if($$Empresa!="")
				{
					
					$valorParticipacion=($valorP*( (float) $$porcentaje))/100;
					$sqlIn1 = " INSERT INTO EFConsorcios_Empresas (id_consorcios_empresas
					, id_proyecto, id_empresa, id_consorcio, id_consecutivo , porcentaje_participacion, empresa_lider
					, valor_contrato_porcentaje, valor_final_contrato, valor_final_porcentaje, valores_actuales, id_valores_proyecto, usuarioGraba, fechaGraba) VALUES";
					
					$sqlIn1 = $sqlIn1." (  (select isnull(MAX(id_consorcios_empresas),0)+1 id_consorc  from EFConsorcios_Empresas WHERE id_proyecto=".$Proy." ), 
					".$Proy.", ".$$Empresa." ,".$id_consorcio.", ".$consecutivo." , ".$$porcentaje." , ".$lider." ,
					".$valorParticipacion.", ".$valorP.", ".$valorParticipacion.", 1,  (select id_valores_proyecto from EFValores_Contrato WHERE id_proyecto=".$Proy." and valores_actuales=1 ),  ".$_SESSION["sesUnidadUsuario"].", getdate() ) ";
					$cursorIn1 = mssql_query($sqlIn1);	
				}
	//echo $sqlIn1."<br>".mssql_get_last_message()."<br>";					
			}
		}
	}
	if($acc==3)
	{
		//ELIMINA TODAS LAS EMPRESAS DE LOS CONSORCIOS REGISTRADOS EN EL PORYECTO 		
		$sqlIn1 =" delete from EFConsorcios_Empresas  where id_proyecto=".$Proy;
		$cursorIn1 = mssql_query($sqlIn1);			
		
		if($cursorIn1!="")
		{
			//ELIMINA TODOS LOS NOMBRES DE LOS CONSORCIOS REGISTRADOS EN EL PORYECTO 
			$sqlIn1 =" delete from EFConsorcios where id_proyecto=".$Proy;
			$cursorIn1 = mssql_query($sqlIn1);	
		}
			
	}
	
	if  (trim($cursorIn1) != "")  {
		mssql_query("commit transaction");		
//		mssql_query("rollback  transaction");		
		echo ("<script>alert('Operaci\u00f3n realizada con exito');</script>"); 					
	} 
	else {
		mssql_query("rollback  transaction");		
		echo ("<script>alert('Error durante la operaci\u00f3n');</script>");
	}			

	echo ("<script>window.close(); MM_openBrWindow('costosF.php?Proy=".$Proy."&sec=1-2-1&imen=1','EF','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");				

}

//info proy
$sql_nom="select *, (select valor_contrato from EFValores_Contrato where id_proyecto=".$Proy ." and valores_actuales=1) valor_contrato from EFNombres_Proyectos where id_proyecto=".$Proy ." and nombre_actual=1";
$cur=mssql_query($sql_nom);
$datos_pro=mssql_fetch_array($cur);

//CONSULTA LA EMPRESA ENCARGADA DEL PROYECTO
$sql_encargada="select EFEmpresas.* from EFProyectos 
				inner join EFEmpresas on EFEmpresas.id_empresa=EFProyectos.id_empresa
				where id_proyecto=".$Proy." ";
$cur_query=mssql_query($sql_encargada);
$datos_encargada=mssql_fetch_array($cur_query);

//CONSULTA LAS EMPRESAS 
$cur_empresa=mssql_query("SELECT * from EFEmpresas where estado=1 and id_empresa <> ".$datos_encargada["id_empresa"]." order by empresa ");

//SQL DEL NOMBRE ACTUAL DEL CONSORCIO
$sql_nom_consr="select * from EFConsorcios where id_proyecto=".$Proy ." and nombre_actual_consorcio=1";
$cur_nom_consr=mssql_query($sql_nom_consr);
$datos_nom_consr=mssql_fetch_array($cur_nom_consr);

//INFO DE LAS EMPRESAS DEL CONSORCIO
$sql_empresas_conso="select EFEmpresas.empresa, EFConsorcios_Empresas.*, CASE EFConsorcios_Empresas.id_empresa when ".$datos_encargada["id_empresa"]." then '1' else '0' end encargada,
(select SUM(valor_contrato_porcentaje) from EFConsorcios_Empresas where id_proyecto=".$Proy." and EFConsorcios_Empresas.valores_actuales=1 ) valor_total_contrato, 
(select SUM(porcentaje_participacion) from EFConsorcios_Empresas where id_proyecto=".$Proy." and EFConsorcios_Empresas.valores_actuales=1 ) porcentaje_total
  from EFConsorcios_Empresas
				inner join EFEmpresas on EFEmpresas.id_empresa=EFConsorcios_Empresas.id_empresa
				where id_proyecto=".$Proy." and EFConsorcios_Empresas.valores_actuales=1
				order by encargada desc ";
$cur_empresas_conso=mssql_query($sql_empresas_conso);
//echo $sql_empresas_conso;
$dis="";
if($acc==3)
	$dis="disabled";

?>


<script type="text/javascript">
	function addFila()
	{ 	
		var opciones= new Array();
		var cantEmpre=parseInt(document.getElementById("cantEmpre").value);
		cantEmpre=cantEmpre+1;		
		var valor_porcentaje=0;
<?PHP
		
?>			
		opciones[0]='<option selected value="">Seleccione una empresa</option>';
<?php			
		$z=1;
		//se carga la lISTA CON LAS PERSONAS ACTIVAS EN EL PORTAL
		while($datos_empre=mssql_fetch_array($cur_empresa))
		{
?>             
			opciones[<?=$z ?>]='<option value="<?=$datos_empre["id_empresa"] ?>"><?=$datos_empre["empresa"]  ?></option>';
<?php
			$z++;
		}
		?>	
		   var sel='', divI='', divF='', total='', porcentaje="" , valor_porcentaje="", lider="";
		   divI='<tr id="empresa'+cantEmpre+'" name="empresa'+cantEmpre+'  ">';

		   divF+='<td width="10%"><i class="glyphicon glyphicon-minus-sign btn btn-danger" onClick="delFila('+cantEmpre+'); sumPorcentaje();"></i> </td></tr>    ';
		   
		   sel+='<td  width="30%" ><select name="Empresa'+cantEmpre+'" id="Empresa'+cantEmpre+'" class="form-control">';
		   for(var u=0;u<opciones.length;u++ )		
		   {
			   sel+=opciones[u];
		   }
		   sel+='</select></td>';
		   
		   porcentaje='<td  width="20%"><input type="text" name="porcentaje'+cantEmpre+'" id="porcentaje'+cantEmpre+'" size="4" maxlength="5" onKeyPress="return acceptNumD(event)" onBlur="sumPorcentaje()" > %</td>';
		   valor_porcentaje='<td  width="30%" >$ <input type="text" class="desabilitados2" name="valorParticipacion'+cantEmpre+'" id="valorParticipacion'+cantEmpre+'" size="25"  onKeyPress="return acceptNumD(event)"  disabled > </td>';
		   lider='<td width="10%"><input type="radio" name="lider" id="lider" onClick="regLider('+cantEmpre+')"></td>';
		   total=divI+sel+porcentaje+valor_porcentaje+lider+divF+"";
		  document.getElementById("divEmpresaT").innerHTML+=total;
		  document.getElementById("cantEmpre").value=cantEmpre;
	}
	
	function delFila(fila)
	{
		 document.getElementById("empresa"+fila).remove();		 		
	}
	
	function regLider(fila)
	{
		document.getElementById("filaLider").value=fila;
	}
	
	//FUNCION QUE REALIZA LA SUMATORIA DEL % INGRESADO
	function sumPorcentaje()
	{
		var TPorcentajePartici=0;
		var TValor=0;
		for(var i=0; i<= document.getElementById("cantEmpre").value; i++)
		{
			if(document.getElementById("porcentaje"+(i)))
				if(document.getElementById("porcentaje"+i).value!="")
				{
					//REALIZA LA SUMATORIA DEL %
					TPorcentajePartici+=parseFloat(document.getElementById("porcentaje"+i).value);			
					//CALCULA EL VALOR EN PESOS/DOLLAR DEL %
					valor_porcentaje=( (parseFloat(document.getElementById("porcentaje"+i).value)) * (parseFloat(document.getElementById("valorP").value)) )/100;
//alert("valor_porcentaje= "+valor_porcentaje+" - "+parseFloat(document.getElementById("porcentaje"+i).value+" * "+parseFloat(document.getElementById("valorP").value+ " /100" );					

//alert("valor_porcentaje= "+valor_porcentaje+" ("+parseFloat(document.getElementById("porcentaje"+i).value)+" * "+parseFloat(document.getElementById("valorP").value)+") / 100" );

					document.getElementById("valorParticipacion"+i).value=(new Intl.NumberFormat('de-DE').format(valor_porcentaje));
					TValor+=valor_porcentaje;
				}

		}		
		document.getElementById("porcentajeT").value=TPorcentajePartici;
//alert("TValor= "+TValor);		
		document.getElementById("valorParticipacionT").value= (new Intl.NumberFormat('de-DE').format(TValor)); //.replace(".", ",");
		document.getElementById("valorParticipacionTocult").value=TValor;

//		alert((new Intl.NumberFormat('de-DE').format(TValor))+" **** "+TValor );
	}
	
	function valida()
	{

		var error=0, errorT=0;					
		var ban=0;
		var TPorcentajePartici=0;		
		var seleLider="no";		
		
		var campos_tex = ["NomConso"];
		error=valida_campos(campos_tex,1);
		errorT+=error;
		
		document.getElementById("divError" ).className="form-group";		
		document.getElementById("helpPorcentaje").style.display="none";		
		document.getElementById("helpPorcentajeTotal").style.display="none";	
		document.getElementById("helpEmpresa").style.display="none";			
		document.getElementById("helpLider").style.display="none";	
		document.getElementById("helpEmpresaRep").style.display="none";		
		document.getElementById("helpValor").style.display="none";		
				

		for(var i=1; i<= document.getElementById("cantEmpre").value; i++)
		{
			if(document.getElementById("Empresa"+(i)))
			{
				for(var u=i+1; u<= document.getElementById("cantEmpre").value; u++)				
				{
					if(document.getElementById("Empresa"+u) )				
					{
						if(document.getElementById("Empresa"+i).value!="")
						{
							//SI LA MISMA EMPRESA ESTA SELECCIONADA MAS DE UNA VEZ
							if(document.getElementById("Empresa"+i).value==document.getElementById("Empresa"+u).value)
							{
								error++;
								break;
								break;
							}
						}
					}
				}
			}
		}

		if(error>0)
		{
			document.getElementById("divError" ).className="form-group has-error";		
			document.getElementById("helpEmpresaRep").style.display="inline-block";
		}
		errorT+=error;
		error=0;
				
		for(var i=0; i<= document.getElementById("cantEmpre").value; i++)
		{
			if(document.getElementById("porcentaje"+(i)))
			{
				if(document.getElementById("porcentaje"+(i)).value=="")
				{									
					error++;
				}
				else //REALIZA LA SUMATORIA DE LOS % INGRESADOS
					TPorcentajePartici+=parseFloat(document.getElementById("porcentaje"+i).value);							
			}
		}	

		if(error>0)
		{
			document.getElementById("divError" ).className="form-group has-error";		
			document.getElementById("helpPorcentaje").style.display="inline-block";									
		}
		//SI EL % DE PARTICIPACION TOTAL, ES DIFERENTE DE 100%
		if(TPorcentajePartici!=100)
		{
			error++;
			document.getElementById("divError" ).className="form-group has-error";		
			document.getElementById("helpPorcentajeTotal").style.display="inline-block";									
		}		
		
		errorT+=error;
		error=0;
		
		for(var i=1; i<= document.getElementById("cantEmpre").value; i++)
		{
			if(document.getElementById("Empresa"+(i)))
			{
				if(document.getElementById("Empresa"+(i)).value=="")
				{									
					error++;
				}						
			}
		}			
		
		if(error>0)
		{
			document.getElementById("divError" ).className="form-group has-error";		
			document.getElementById("helpEmpresa").style.display="inline-block";									
		}		
				
		errorT+=error;					
		
		//VALIDA VALORES DEL PROYECTO Y VALOR % PARTICIPACION
		if(document.getElementById("valorParticipacionTocult").value!="")
		{
//alert (" valorP= "+document.getElementById("valorP").value+" ---- valorParticipacionTocult= "+document.getElementById("valorParticipacionTocult").value);;
	
			if(document.getElementById("valorP").value!=document.getElementById("valorParticipacionTocult").value)
			{
				document.getElementById("divError" ).className="form-group has-error";		
				document.getElementById("helpValor").style.display="inline-block";				
				errorT+=1;
			}
		}
		

		for(var i=0; i<= document.getElementById("cantEmpre").value; i++)
		{			
			if(document.formulario.lider[i])
			{
				if(document.formulario.lider[i].checked)
				{
					seleLider="si";
					break;					
				}
			}
		}
		if(seleLider=="no")
		{
			document.getElementById("divError" ).className="form-group has-error";		
			document.getElementById("helpLider").style.display="inline-block";																
			errorT+=1;			
		}
		
		/*
		if((document.getElementById("filaLider").value=="" ))
		{
			document.getElementById("divError" ).className="form-group has-error";		
			document.getElementById("helpLider").style.display="inline-block";																
			errorT+=1;
		}
		*/		
		if ( (errorT==0) ) //&& (ban!=document.getElementById("cantOrd").value) )
		{
			document.formulario.recarga.value="2";
			document.formulario.submit();
		}		
	}
</script>	

<form id="formulario" name="formulario" method="POST">
<div class="container-fluid" >
   
  <div class="form-group" id="">
    <label for="">Proyecto</label>
    <div class="desabilitados">
		 <?=$datos_pro["nombre_largo_proyecto"] ?>
    </div>
  </div>
  
  <div class="form-group" id="">
    <label for="">Valor</label>
    <div class="desabilitados">
		 $ <?=number_format($datos_pro["valor_contrato"], 2, ',', '.'); ?>
         <input type="hidden" value="<?=$datos_pro["valor_contrato"] ?>" id="valorP" name="valorP">
    </div>
  </div>  


  
  <div class="form-group" id="divNomConso">
    <label for="">Nombre del consorcio</label>
    <input type="text" class="form-control" id="NomConso" name="NomConso" value="<?=$datos_nom_consr["nombre_consorcio"] ?>"  placeholder="Nombre del consorcio" size="20px" autofocus <?=$dis ?> >
     <span id="helpNomConso" class="help-block" style="display:none;" >El nombre del consorcio es obligatorio.</span>
      
     <input type="hidden" value="<?=$datos_nom_consr["nombre_consorcio"] ?>" id="nomConsorcio" name="nomConsorcio">
     <input type="hidden" value="<?=$datos_nom_consr["id_consorcio"] ?>" id="id_consorcio" name="id_consorcio">     
     
    </div>
  
  
<table width="80%"  class="table table-bordered" >
  <tbody>
    <tr>
      <th  width="30%">Empresas</th>
      <th width="20%">% de Participaci&oacute;n</th>
      <th width="30%">Valor % Participaci&oacute;n</th>
      <th width="10%"> <p>Lider</p></th>
      <td width="10%">&nbsp;</td>
    </tr>
<?PHP

	//SE RECORRE LA INFORMACION DE LA EMPRESA ENCARGADA
	if($datos_empresas_conso=mssql_fetch_array($cur_empresas_conso))
	{

		$valor_contrato_total=( (int) $datos_empresas_conso["valor_total_contrato"]);
//echo $valor_contrato_total." *** "		;		
		//SI EL % CALCULADO DESDE LA B.D. ES 100
		if(( (int) $datos_empresas_conso["porcentaje_total"])==100)
			$porcentaje=100;
		else	
			$porcentaje=$datos_empresas_conso["porcentaje_total"];
		
		$lider="";
		if($datos_empresas_conso["empresa_lider"]==1)	
			$lider="checked";
?>        
    <tr class="TR" >
    	<td>
			<?=$datos_empresas_conso["empresa"] ?>
            <input type="hidden" value="<?=$datos_encargada["id_empresa"] ?>" id="Empresa0" name="Empresa0">   
        </td>      
          <td><input type="text" name="porcentaje0" id="porcentaje0" value="<?=$datos_empresas_conso["porcentaje_participacion"] ?>" size="4" maxlength="5" onKeyPress="return acceptNumD(event)" onBlur="sumPorcentaje()" <?=$dis ?>  > 
          %</td>
          <td>$ 
            <input type="text" class="desabilitados2" name="valorParticipacion0"  value="<?=number_format($datos_empresas_conso["valor_contrato_porcentaje"], 2, ',', '.') ?>" id="valorParticipacion0" size="25"  onKeyPress="return acceptNumD(event)"  disabled ></td>
          <td><input type="radio" name="lider" onClick="regLider(0)" id="lider" <?=$lider ?> <?=$dis ?>  ></td>   
          <td>&nbsp;</td>  
    </tr>
                           
<?php
	}
?>
	</tbody>
</table>

<table id="divEmpresaT" width="80%"  class="table table-bordered table-hover">
		<?php	
			$z=0;
            while($datos_empresas_conso=mssql_fetch_array($cur_empresas_conso))
            {	   
                $z++;			
				$lider="";
				if($datos_empresas_conso["empresa_lider"]==1)	
					$lider="checked";			         
?>
	          <tr class="TR" id="empresa<?=$z ?>" name="empresa<?=$z ?>">
                <td  width="30%">
  				<select name="Empresa<?=$z ?>" id="Empresa<?=$z ?>" class="form-control" <?=$dis ?> >
        <?php 			
                        
                        //LISTA LAS EMPRESAS
						$sql_empre="SELECT * from EFEmpresas where estado=1 and id_empresa <> ".$datos_encargada["id_empresa"]." order by empresa ";
						$cur_empresa=mssql_query($sql_empre);
                        while($datos_empre=mssql_fetch_array($cur_empresa))
                        {
							$se="";
							if($datos_empre["id_empresa"]==$datos_empresas_conso["id_empresa"])
								$se="selected";
                ?>             
                            <option value="<?=$datos_empre["id_empresa"] ?>" <?=$se ?> ><?=$datos_empre["empresa"]  ?></option>';
                <?php
                            
                        }
         ?>
                 </select>                                     
                </td>
                
                <td  width="20%">
					<input type="text" name="porcentaje<?=$z ?>" id="porcentaje<?=$z ?>" value="<?=$datos_empresas_conso["porcentaje_participacion"] ?>" size="4" maxlength="5" onKeyPress="return acceptNumD(event)" onBlur="sumPorcentaje()" <?=$dis ?> > %
                </td>

				<td  width="30%">$ 
	            	<input type="text" class="desabilitados2" name="valorParticipacion<?=$z ?>"  value="<?=number_format($datos_empresas_conso["valor_contrato_porcentaje"], 2, ',', '.') ?>" id="valorParticipacion<?=$z ?>" size="25"  onKeyPress="return acceptNumD(event)"  disabled >
                </td>                

		         <td  width="10%"><input type="radio" name="lider" onClick="regLider(<?=$z ?>)" id="lider" <?=$lider ?> <?=$dis ?>  ></td>   	
                
                <td  width="10%">
                <?php
					if($acc==2)
					{
				?>
                	<i class="glyphicon glyphicon-minus-sign btn btn-danger" onClick="delFila(<?=$z ?>); sumPorcentaje();"></i>
                 <?php
					}
				 ?>   
                </td>                  
       
        <?php

            }
        ?>    
</table>

<table width="80%"  class="table table-bordered " >    
    <tr>
      <th  width="30">Totales</th>
      <td  width="20%">
      

	      <input type="text" class="desabilitados2" name="porcentajeT" id="porcentajeT" size="4" value="<?=$porcentaje ?>" maxlength="3" onKeyPress="return acceptNumD(event)"  disabled >        %
      </td>
      <td  width="30%">$
        <input type="text" class="desabilitados2" name="valorParticipacionT" id="valorParticipacionT" value="<?=number_format($valor_contrato_total, 0, ',', '.'); ?>" size="25"  onKeyPress="return acceptNumD(event)"  disabled >
        <input type="hidden"  id="valorParticipacionTocult" name="valorParticipacionTocult" value="<?=$valor_contrato_total ?>">   <? //$valor_contrato_total ?>     
	  </td>
      <td colspan="2" width="20%">&nbsp;</td>
      </tr>
  </tbody>
</table>

<div id="divError" class="form-group" x>
    <span id="helpPorcentaje" class="help-block" style="display:none;" >Por favor ingrese el % de participaci&oacute;n, en todas las empresas. </span>    
    <span id="helpPorcentajeTotal" class="help-block" style="display:none;" >El total del % de participaci&oacute;n, debe ser 100%. </span>        
        
    <span id="helpEmpresa" class="help-block" style="display:none;" >Por favor seleccione todas las empresas. </span>
    <span id="helpEmpresaRep" class="help-block" style="display:none;" >Por favor seleccione la empresa una sola vez. </span>         
    
    <span id="helpLider" class="help-block" style="display:none;" >Por favor seleccione la empresa lider. </span>

    <span id="helpValor" class="help-block" style="display:none;" >El valor total del % participacion debe ser igual al valor del proyecto. </span>    

</div>

<div class="form-group" style="text-align:right;">
<?php
	$mensaje="";
	if($acc==2)
	{
		$mensaje="Actualizar";
?>
    <i class="glyphicon glyphicon-plus-sign btn btn-success" onClick="addFila();" title="Agregar Empresa"></i>            
<?php
	}
	else
		$mensaje="Eliminar";
?>    
</div>              
        
        <div class="main row " style="text-align:right">
            <div class="col-sm-12 ">
                <button type="button" class="btn btn-primary" onClick="valida()"><?=$mensaje ?></button>
		       <input name="recarga" type="hidden" id="recarga" value="1">    
	           <input type="hidden" id="cantEmpre" name="cantEmpre" value="<?=$z ?>">         
	           <input type="hidden" id="filaLider" name="filaLider" value="">                        
          </div>
	</div>    
</div>  
</form> 

<?php
	include("inferior.php"); 
?>