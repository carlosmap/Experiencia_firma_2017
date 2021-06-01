<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente
banner(2,"Nuevo Proyecto");

//include ("../../verificaRegistro2.php");
if($recarga==2)
{
//	 $ProyL
//	ProyC
//or nombre_corto_proyecto='INUNDACION BIOFILM'


	


	$cant_nom=0;
	//VERIFICA QUE NO EXISTA UN PROYECTO, CON EL MIMSO NOMBRE LARGO, REGISTRADO
	$sql_veri="select * from EFNombres_Proyectos where  nombre_largo_proyecto='".$ProyL."' and nombre_actual=1 ";
	$cur_veri=mssql_query($sql_veri);
	$cant_nom=mssql_num_rows($cur_veri);	
//echo mssql_num_rows($cur_veri)." *****";

	$cant_veri_seven=0;
	$sql_veri="select * from EFProyectos where codigo_SEVEN='".$SEVENN."' ";
	$cur_veri_seven=mssql_query($sql_veri);
	$cant_veri_seven=mssql_num_rows($cur_veri_seven);	
//echo $cant_veri_seven." <br> ".$sql_veri;
//	if(mssql_num_rows($cur_veri)==0)
//	if((mssql_num_rows($cur_veri)==0) && (mssql_num_rows($cur_veri_seven)==0) )	
	if(($cant_nom==0) && ($cant_veri_seven==0) )
	{
		$curqlIn1 = mssql_query(" select isnull(MAX(id_proyecto),0)+1 id_proye  from EFProyectos");	
		$datos_pro=mssql_fetch_array($curqlIn1 );
			
		mssql_query("begin transaction");			
		
		$curqlIn1 = mssql_query(" select isnull(MAX(id_proyecto),0)+1 id_proye  from EFProyectos");	
		$datos_pro=mssql_fetch_array($curqlIn1 );
		$id_proye=$datos_pro["id_proye"];

		$cursorIn1="no error";
		//Crea la carpeta del proyecto
		$path1 = "files/" . $id_proye;
//echo $path1." --- ";
		if(is_dir($path1) == false)
		{
//echo "ingresaa<br>";			
			if(mkdir($path1,0777))
			{
//echo "ingresaa 2222 ";							
		//			echo "Directorio creado con éxito: " . $path . "<br>";
				//CREA LA CARPETA DE LOS ARCHIVOS DE RECEPCION
				$path2=$path1."/filesRecepcion";
				if(mkdir($path2,0777)){
				}
				else{
					//SI SE PRESENTO UN ERROR, ELIMINA LA CARPETA DEL PROYECTO
					delete_dir($path1);
					$cursorIn1="";
				}			
			}
			else{
		//			echo "Error: No fué posible crear el Directorio: " . $path . "<br>";
					$cursorIn1="";
			}
		}
		if($cursorIn1!="")
		{		
				if($TRM=="")
					$TRM="NULL";
					
				//INFO BASICA DEL PROYECTO
				$sqlIn1 = " INSERT INTO EFProyectos";
				$sqlIn1 = $sqlIn1 . "( 
				id_proyecto, certificado_SEVEN,  codigo, codigo_SEVEN, numero_contrato,  id_tipo_proyecto, id_moneda, TRM, id_tipo_ejecucion, id_empresa, id_forma_pago, usuarioGraba,  fechaGraba ";
				
				//SI EL TIPO DE POYECTO ES NO FACTURABLE Y EL ESTADO ES PROPUESTA  o CODIGO INTRNO
				if ($TipoP==5) 
				{
					if( ($Estado==10) || ($Estado==1) )
					{
						$sqlIn1 = $sqlIn1 . ",revision,fechaRevision,usuarioRevision";				
					}
				}
				$sqlIn1 = $sqlIn1 . ") values ( ".$id_proye." ,	 '".$SEVE."',	
				'".$Cod."','".$SEVENN."', '".$NoContrato."',".$TipoP.",".$Moneda.",".$TRM.",".$Ejecucion.",".$Empresa.",".$FPago."," . $_SESSION["sesUnidadUsuario"] . ",getdate()  ";
				
				//SI EL TIPO DE POYECTO ES NO FACTURABLE Y EL ESTADO ES PROPUESTA
				if ($TipoP==5) 
				{
					if( ($Estado==10) || ($Estado==1) )
					{
						$sqlIn1 = $sqlIn1 . ",1, getdate() , " . $_SESSION["sesUnidadUsuario"] . " ";				
					}
				}
				
				$sqlIn1 = $sqlIn1 . ") ";
				
				$cursorIn1 = mssql_query($sqlIn1);					
//echo $sqlIn1."<br>".mssql_get_last_message();		
				if($cursorIn1!="")
				{
					//CLIENTE DEL PROYECTO
					$sqlIn1 ="insert into EFClientes_Proyectos ( id_cliente_proyecto,id_cliente,id_proyecto ,cliente_actual, usuarioGraba, fechaGraba
) values( 1, ".$Cliente.", ".$id_proye.", 1 ," . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
					$cursorIn1 = mssql_query($sqlIn1);					
		//echo $sqlIn1."<br>";					
					if($cursorIn1!="")
					{
						$ProyC=$ProyL;
						//NOMBRE CORTO Y LARGO DEL PROYECTO
						$sqlIn1 ="insert into EFNombres_Proyectos (id_nombres_proyecto,id_proyecto, nombre_corto_proyecto,nombre_largo_proyecto, nombre_actual,usuarioGraba,fechaGraba) 
						values( 1 , ".$id_proye." ,  '".$ProyC."' ,'".$ProyL."', 1 ," . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
						$cursorIn1 = mssql_query($sqlIn1);												
		//echo $sqlIn1."<br>";		
						if($cursorIn1!="")
						{				
							//FECHA DE INICIO Y FINAL
							$sqlIn1 ="insert into EFFechas_Proyecto (id_fecha_proyecto, id_proyecto, id_prorroga, fecha_inicio_proyecto, fecha_final_proyecto, fechas_actuales ,usuarioGraba,fechaGraba) 
							values(1 , ".$id_proye." ,NULL, '".$Finicio."', '".$Final."', 1 ," . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
							$cursorIn1 = mssql_query($sqlIn1);																
		//echo $sqlIn1."<br>";							
							if($cursorIn1!="")
							{				
		
								if($cursorIn1!="")
								{				
									//SI EL TIPO DE POYECTO ES FACTURABLE, SE LE ASIGNA EL ESTADO ADJUDICADO, DE FORMA AUTOMATICA
									if($TipoP==4)
										$Estado=2;
								
									//ESTADO DEL PROYECTO
									$sqlIn1 ="insert into EFEstados_Proy_Proyectos ( id_estados_proy_proyectos, id_estado_proy, id_proyecto, fecha_estado, estado_actual, usuarioGraba,fechaGraba, observaciones) 
									values(1, ".$Estado." , '".$id_proye."' ,getdate(), 1, " . $_SESSION["sesUnidadUsuario"] . ",getdate(), '".$observacion."' )";
									$cursorIn1 = mssql_query($sqlIn1);																				
//echo "***** ".$observacion."<br>";
//echo $sqlIn1."<br>";									
									if($cursorIn1!="")
									{				
										//VALOR DEL PROYECTO
										$sqlIn1 ="insert into EFValores_Contrato ( id_valores_proyecto, id_proyecto, tipo, valor_contrato, valores_actuales, usuarioGraba,fechaGraba) 
										values(1, '".$id_proye."', 3 ,".$Valor.", 1, " . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
										$cursorIn1 = mssql_query($sqlIn1);																
										
										//VALORES DEL PROYECTO_CONSORCIOS
										if($cursorIn1!="")
										{	
											//SI LA FORMA DE EJECUCION ES INDIVIDUAL		
											if($Ejecucion==1)	
											{
												//SE REGISTRA EN LA TABLA CONSORCIOS LA EMPRESA CON EL % PARTICIPACION DEL 100%
												$sqlIn1 ="insert into EFConsorcios_Empresas ( id_consorcios_empresas, id_proyecto, id_empresa, porcentaje_participacion, empresa_lider, valor_contrato_porcentaje, valor_final_contrato, valor_final_porcentaje, valores_actuales, id_valores_proyecto, usuarioGraba,fechaGraba) 
												values(1, '".$id_proye."', ".$Empresa.", 100, 1, ".$Valor.", ".$Valor.", ".$Valor.", 1, 1, " . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
												$cursorIn1 = mssql_query($sqlIn1);																
//echo $sqlIn1."<br>".mssql_get_last_message();																					
											}

										}													
			//echo $sqlIn1."<br>".mssql_get_last_message();									
									}					
							
		//echo $sqlIn1."<br>".mssql_get_last_message();									
								}
								
								
								
							}					
							
						}
						
						
						
					}
				}
		}
		if  (trim($cursorIn1) != "")  {
			mssql_query("commit transaction");		
//			mssql_query("rollback  transaction");		
			echo ("<script>alert('Operación realizada con exito');</script>"); 					
		} 
		else {
			mssql_query("rollback  transaction");		
			echo ("<script>alert('Error durante la operación');</script>");
		}			
	
		echo ("<script>window.close(); MM_openBrWindow('index.php?Proyecto=".$id_proye."','EF','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");			
	}
	else
	{
		$msg="";
		if( $cant_nom>0)
			$msg="Ya existe un proyecto con ese nombre (Nombre largo), por favor asign\u00e9 un nombre diferente. \\n";		 
			
		if($cant_veri_seven>0)
			$msg.="Ya se le ha asignado este c\u00f3digo seven a un proyecto, por favor asign\u00e9 un c\u00f3digo diferente.";	
		
		echo ("<script>alert('".$msg."');</script>"); 						
	}	
//		echo ("<script>alert('Ya existe un proyecto con ese nombre registrado (Nombre largo), por asigné uno diferente.');</script>"); 						
}



?>

<script type="text/javascript">
function valida()
{
 var campos_tex = ["ProyL","Cod","SEVE","SEVENN","Cliente","NoContrato","Finicio","Final","Ejecucion","Empresa","TipoP","Moneda","Valor","FPago"];	
 
 <?php
	//SE SOLICITA LA TRM, CUANDO LA MONEDA ES DOLLAR
	if($Moneda==2)
		echo 'campos_tex.push("TRM");';

	//SI EL TIPO DE PAGO ES DIFERENTE A FACTURABLE		
	if(($TipoP!=4)&& ($TipoP!=""))
		echo 'campos_tex.push("Estado");';	
	
?>  
 
	var error=0;
	
	error=valida_campos(campos_tex,1);
	
	error+=valida_campos("",5);
//alert(" ------ "+error)	;
	//SI NO SE PRESENTARON PROBLEMAS DE VALIDACION
	if(error==0)
	{
		document.formulario.recarga.value="2";
		document.formulario.submit();
	}
	
}

function res_fechas()
{
	document.formulario.Finicio.value="";
	document.formulario.Final.value="";	
}

</script>
<form id="formulario" name="formulario" method="post" >

  <div class="form-group" id="divProyL">
    <label for="">Proyecto (Nombre largo)</label>
    <input type="text" class="form-control" id="ProyL" name="ProyL" value="<?=$ProyL ?>" placeholder="Proyecto (Nombre largo)" size="20px" autofocus>
     <span id="helpProyL" class="help-block" style="display:none;" >El nombre del proyecto es obligatorio.</span>
  </div>

  
    <div class="form-group" id="divSEVE">
    <label for="">Certificado SEVEN</label>
    <input type="text" class="form-control" id="SEVE" name="SEVE" value="<?=$SEVE ?>"  placeholder="Certificado SEVEN" onKeyPress="return acceptNum(event)">
     <span id="helpSEVE" class="help-block" style="display:none;" >El Certificado SEVEN es obligatorio.</span>
  </div>

  
    <div class="form-group" id="divCod">
    <label for="">C&oacute;digo Antiguo</label>
    <input type="text" class="form-control" id="Cod" name="Cod" value="<?=$Cod ?>"  placeholder="C&oacute;digo Antiguo" onKeyPress="return acceptNum(event)">
     <span id="helpCod" class="help-block" style="display:none;" >El C&oacute;d Antiguo. es obligatorio.</span>
  </div>
  
    <div class="form-group" id="divSEVENN">
    <label for="">C&oacute;digo SEVEN </label>
    <input type="text" class="form-control" id="SEVENN"  name="SEVENN" value="<?=$SEVENN ?>"  placeholder="C&oacute;digo SEVEN" onKeyPress="return acceptNum(event)">
     <span id="helpSEVENN" class="help-block" style="display:none;" >El C&oacute;digo SEVEN es obligatorio.</span>
  </div>
  

  <div class="form-group" id="divCliente">
    <label for="">Cliente</label>
        <select class="form-control" id="Cliente" name="Cliente"  >
          <option selected value="">Seleccione el cliente</option>        
        <?php
        	$cur_clie=mssql_query("SELECT * from EFClientes where estado=1 order by cliente");
			while($datos_cli=mssql_fetch_array($cur_clie))
			{
				$sel="";
				if($Cliente== $datos_cli["id_cliente"])
					$sel="selected";
		?>
          		<option value="<?=$datos_cli["id_cliente"]; ?>" <?=$sel ?> ><?=$datos_cli["cliente"]; ?></option>
        <?php
			}
		?>  
        </select>
     <span id="helpCliente" class="help-block" style="display:none;" >Por favor seleccione el cliente.</span>
  </div>
  
    <div class="form-group" id="divNoContrato">
    <label for="">No. Contrato</label>
    <input type="text" class="form-control" id="NoContrato" name="NoContrato" value="<?=$NoContrato ?>"  placeholder="No. Contrato">
     <span id="helpNoContrato" class="help-block" style="display:none;" >El No. Contrato es obligatorio.</span>
  </div>

	<div class="form-group" id="divTipoP" >
    <label for="">Tipo de proyecto</label>
    
    <select class="form-control" id="TipoP" name="TipoP" onChange=" res_fechas(); document.formulario.submit();">
      <option selected value="">Seleccione el tipo de proyecto</option>        
    <?php
        $cur_clie=mssql_query("SELECT * from EFTipos_Proyecto where estado=1 ");
		
        while($datos_cli=mssql_fetch_array($cur_clie))
        {
			$sel="";
			if( ($TipoP== $datos_cli["id_tipo_proyecto"]) && ( isset($TipoP)) )
				$sel="selected";		
    ?>
            <option value="<?=$datos_cli["id_tipo_proyecto"]; ?>" <?=$sel ?> ><?=$datos_cli["tipo_proyecto"]; ?></option>
    <?php
        }
    ?>  
    </select>    
    
     <span id="helpTipoP" class="help-block" style="display:none;" >Por favor seleccione el tipo de proyecto.</span>
  </div>     
  
<?php
	//SI EL TIPO DE PROYECTO ES DIFRENTE A FACTURABLE
	if( ($TipoP!=4) && ( isset($TipoP))  )
	{
?>  
      <div class="form-group" id="divEstado" >
        <label for="">Estado</label>
        
        <select class="form-control" id="Estado" name="Estado">
          <option value="">Seleccione el Estado</option>        
        <?php
            $cur_clie=mssql_query("select *  from EFEstados_Proy where estado=1 and id_tipo_proyecto=".$TipoP." order by estado_proyecto");
            
            while($datos_cli=mssql_fetch_array($cur_clie))
            {
                $sel="";
                if($Estado== $datos_cli["id_estado_proy"])
                    $sel="selected";		
        ?>
                <option value="<?=$datos_cli["id_estado_proy"]; ?>" <?=$sel ?> ><?=$datos_cli["estado_proyecto"]; ?></option>
        <?php
            }
        ?>  
        </select>    
        
         <span id="helpEstado" class="help-block" style="display:none;" >Por favor seleccione el estado del proyecto.</span>
      </div>     
<?php
	}
?>    

  <div class="form-group" id="divobservacion">
    <label for="">Observaciones</label>            
    <textarea name="observacion" class="form-control" id="observacion" cols="30" rows="1"></textarea>
  </div>

    <div class="form-group" id="divFinicio">
    <label for="">Fecha de Inicio</label>    
	<div class="container-fluid">
      <div class="col-sm-3">
       <div class="input-group">
<?php
	//SI EL TIPO DE PROYECTO ES NO FACTURABLE, no PERMITE SELECCIONAR LA FECHA
	if( ($TipoP==5) && ( isset($TipoP))  )
	{
		$dato_fecha_ini_fin=mssql_fetch_array(mssql_query("SELECT CONVERT(varchar, DATEADD(MONTH, +6, GETDATE()),101) fecha_fin , CONVERT(varchar, GETDATE(),101) fecha_inicio "));	
?>		
		<div class="input-group">
			<input class="form-control" id="" name="" value="<?=$dato_fecha_ini_fin["fecha_inicio"] ?>"  size="26" readonly  type="text"/>
			<input type="hidden" id="Finicio" name="Finicio" value="<?=$dato_fecha_ini_fin["fecha_inicio"] ?>" >
		</div>		
<?PHP        
	}
//	if( ($TipoP!=5) && ( isset($TipoP))  )
	else
	{
?>         
        <input class="form-control" id="Finicio" name="Finicio" placeholder="MM/DD/YYYY" value="<?=$Finicio ?>"   readonly type="text"/>
        <div class="input-group-addon">
         <i class="glyphicon glyphicon-calendar">
         </i>
        </div> 
<?php
	}
?>        
               
       </div>
      </div>
    </div>   
     <span id="helpFinicio" class="help-block" style="display:none;" >La fecha de inicio es obligatoria.</span>
  </div>


    <div class="form-group" id="divFinal">
    <label for="">Fecha de Finalizaci&oacute;n</label>    
	<div class="container-fluid">
      <div class="col-sm-3">
       <div class="input-group">
<?php
	//SI EL TIPO DE PROYECTO ES NO FACTURABLE, no PERMITE SELECCIONAR LA FECHA
	if( ($TipoP==5) && ( isset($TipoP))  )
	{
?>	        
		<div class="input-group">
			<input class="form-control" id="" name="" value="<?=$dato_fecha_ini_fin["fecha_fin"] ?>"  size="26" readonly  type="text"/>
			<input type="hidden" id="Final" name="Final" value="<?=$dato_fecha_ini_fin["fecha_fin"] ?>" >
		</div>		
<?PHP        
	}
//	if( ($TipoP!=5) && ( isset($TipoP))  )
	else
	{
?>        
        <input class="form-control" id="Final" name="Final" placeholder="MM/DD/YYYY"  value="<?=$Final ?>"  readonly type="text"/>
        <div class="input-group-addon">
         <i class="glyphicon glyphicon-calendar">
         </i>
        </div>   
<?php
	}
?> 
             
       </div>
      </div>
    </div>   
        
     <span id="helpFinal" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n es obligatoria.</span>
     <span id="helpFinal2" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n del proyecto es menor a la fecha de inicio, por favor verifique.</span>     
          
  </div>
  

  <div class="form-group" id="divEjecucion">
    <label for="">Forma de Ejecuci&oacute;n</label>
    
    <select class="form-control" id="Ejecucion" name="Ejecucion">
      <option selected value="">Seleccione forma de ejecución</option>        
    <?php
        $cur_clie=mssql_query("SELECT * from EFTipos_Ejecucion where estado=1 ");
		
        while($datos_cli=mssql_fetch_array($cur_clie))
        {
			$sel="";
			if($Ejecucion== $datos_cli["id_tipo_ejecucion"])
				$sel="selected";		
    ?>
            <option value="<?=$datos_cli["id_tipo_ejecucion"]; ?>" <?=$sel ?> ><?=$datos_cli["ejecucion"]; ?></option>
    <?php
        }
    ?>  
    </select>    
    
     <span id="helpEjecucion" class="help-block" style="display:none;" >Por favor seleccione la forma de Ejecuci&oacute;n.</span>
  </div>   
  
  
 <div class="form-group" id="divEmpresa">
    <label for="">Empresa encargada</label>
    
    <select class="form-control" id="Empresa" name="Empresa">
      <option selected value="">Seleccione la empresa</option>        
    <?php
        $cur_clie=mssql_query("SELECT * from EFEmpresas where estado=1 and tipo=1 ");
		
        while($datos_cli=mssql_fetch_array($cur_clie))
        {
			$sel="";
			if($Empresa== $datos_cli["id_empresa"])
				$sel="selected";		
    ?>
            <option value="<?=$datos_cli["id_empresa"]; ?>" <?=$sel ?> ><?=$datos_cli["empresa"]; ?></option>
    <?php
        }
    ?>  
    </select>    
    
     <span id="helpEmpresa" class="help-block" style="display:none;" >Por favor seleccione la Empresa encargada.</span>
  </div>  
  
  
   <div class="form-group" id="divMoneda">
    <label for="">Moneda</label>
    
    <select class="form-control" id="Moneda" name="Moneda"  onChange="document.formulario.submit();  ">
      <option selected value="">Seleccione el tipo de moneda</option>        
    <?php
        $cur_clie=mssql_query("select * from EFMonedas where estado=1  order by  moneda");
		
        while($datos_cli=mssql_fetch_array($cur_clie))
        {
			$sel="";
			if( ($Moneda== $datos_cli["id_moneda"]) && (isset($Moneda)) )
				$sel="selected";		
    ?>
            <option value="<?=$datos_cli["id_moneda"]; ?>" <?=$sel ?> ><?=$datos_cli["moneda"]; ?></option>
    <?php
        }
    ?>  
    </select>    
    
     <span id="helpMoneda" class="help-block" style="display:none;" >Por favor seleccione el tipo de Moneda.</span>
  </div>      
  
    <div class="form-group" id="divValor">
    <label for="">Valor del proyecto</label>
    <input type="text" class="form-control" id="Valor" name="Valor" value="<?=$Valor ?>"  placeholder="Valor" onKeyPress="return acceptNum(event)">
     <span id="helpValor" class="help-block" style="display:none;" >El Valor del proyecto es obligatorio.</span>
  </div>  
  

   <div class="form-group" id="divFPago"><label for="">Tipo de facturaci&oacute;n</label>
     <select class="form-control" id="FPago" name="FPago">
      <option selected value="">Seleccione tipo de facturación</option>        
    <?php
        $cur_clie=mssql_query("select * from EFFormas_pago where estado=1 order by forma_pago ");
		
        while($datos_cli=mssql_fetch_array($cur_clie))
        {
			$sel="";
			if($FPago== $datos_cli["id_forma_pago"])
				$sel="selected";		
    ?>
            <option value="<?=$datos_cli["id_forma_pago"]; ?>" <?=$sel ?> ><?=$datos_cli["forma_pago"]; ?></option>
    <?php
        }
    ?>  
    </select>    
    
     <span id="helpFPago" class="help-block" style="display:none;" >Por favor seleccione la Forma de pago.</span>
  </div>      

<?php
	//SE SOLICITA LA TRM, CUANDO LA MONEDA ES DOLLAR
	if($Moneda==2)
	{
?>  
        <div class="form-group" id="divTRM">
        <label for="">TRM</label>
        
        <input type="text" class="form-control" id="TRM" name="TRM" value="<?=$TRM ?>"  placeholder="TRM" onKeyPress="return acceptNumD(event)">
         <span id="helpTRM" class="help-block" style="display:none;" >El Valor del TRM es obligatorio.</span>
      </div>    
<?php
	}
?>    
   <div style="text-align:right" >
      <button type="button" class="btn btn-primary" onClick="valida()" >Grabar</button>  
       <input name="recarga" type="hidden" id="recarga" value="1">
   </div>

</form>

    
<?php
	include("inferior.php"); 
?>