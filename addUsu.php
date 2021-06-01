<?php 
include("encabezado.php"); 
//tipo=2 (para ventanas emergentes), titulo de la ventna emergente
banner(2,"Nuevo Usuario");


if($recarga==2)
{
	$mes = array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
	
	mssql_query("begin transaction");		
	
	$sql_inser="insert into EFUsuarios (unidad, estado, usuarioGraba, fechaGraba) values (".$Usuario.", 1, " . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
	$cursorIn1=mssql_query($sql_inser);
	if($cursorIn1!="")
	{		
		$sql_inser="insert into EFPerfiles_Usuarios (unidad, id_perfil, perfil_actual, fecha_inicio_activo, fecha_final_activo, usuarioGraba, fechaGraba) values (".$Usuario.",".$Perfil.", 1 ";
		
		if($Perfil==3)		
			$sql_inser.=" ,'".$Finicio."' , '".$Final."' ";
		
		else
			$sql_inser.=" ,NULL, NULL ";
		
		$sql_inser.=" ," . $_SESSION["sesUnidadUsuario"] . ",getdate() )";
		$cursorIn1=mssql_query($sql_inser);	
	}
	
//echo $sql_inser." --- ".mssql_get_last_message();
	if  (trim($cursorIn1) != "")  
	{
		mssql_query("commit transaction");		
		

		
			//ENVIA CORREO AL USUARIO
			$sql_usu="select U.email, EFPerfiles.perfil, (select (upper (apellidos+' '+nombre)) usu from HojaDeTiempo.dbo.Usuarios where unidad=" . $_SESSION["sesUnidadUsuario"] . ") usuAsig , day(getdate()) dia, month(getdate()) mes, year(getdate()) ano 
			, day(fecha_inicio_activo) dia_inicio, month(fecha_inicio_activo) mes_inicio, year(fecha_inicio_activo) ano_inicio
			, day(fecha_final_activo) dia_final, month(fecha_final_activo) mes_final, year(fecha_final_activo) ano_final

			from EFUsuarios  inner join HojaDeTiempo.dbo.Usuarios U on U.unidad=EFUsuarios.unidad
				inner join EFPerfiles_Usuarios on EFPerfiles_Usuarios.unidad=EFUsuarios.unidad and EFPerfiles_Usuarios.perfil_actual=1
				inner join EFPerfiles on EFPerfiles.id_perfil=EFPerfiles_Usuarios.id_perfil
			where  EFUsuarios.estado=1 and U.unidad=".$Usuario." ";
	
			$cur_usu=mssql_query($sql_usu);
							
			if($datos_usu=mssql_fetch_array($cur_usu))
			{
//echo $sql_usu." ".mssql_get_last_message()."<br>".$pTema;						
				$pTema='Usted ha sido asignado como usuario <b>'.$datos_usu["perfil"].'</b> en el m&oacute;dulo de <b>Experiencia de la firma</b>, por ['.$_SESSION["sesUnidadUsuario"].'] '.$datos_usu["usuAsig"].' el dia '.$datos_usu["dia"].' de '.$mes[$datos_usu["mes"]].' del '.$datos_usu["ano"].'.';
				
				//USUARIO DE CONSULTA
				if($Perfil==3)
				{
					$pTema.='<br><br><b>Vigencia:</b> Desde el '.$datos_usu["dia_inicio"].' de '.$mes[$datos_usu["mes_inicio"]].' del '.$datos_usu["ano_inicio"].' hasta el '.$datos_usu["dia_final"].' de '.$mes[$datos_usu["mes_final"]].' del '.$datos_usu["ano_final"].'.';
				}
				
				$pTema.=' <br><br> Para acceder al aplicativo, por favor utilice el enlace ubicado en la p&aacute;gina principal de la intranet.<br><br>';
//				$pTema.='<br><br><img src="img/mini.jpg">';

				$pPara= $datos_usu["email"]."@ingetec.com.co";		
			//	$pPara="carlosmaguirre@ingetec.com.co";		
				$pAsunto=utf8_decode("Asignación como usuario - Exp. Firma");
//echo "Ingresassss 1111111111111111111111";								
				enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);			
//echo "Ingresassss";				
			}
//		mssql_query("rollback  transaction");									
//			mssql_query("rollback  transaction");		
		echo ("<script>alert('Operaci\u00f3n realizada con exito');</script>"); 					
	} 
	else {
		mssql_query("rollback  transaction");		
		echo ("<script>alert('Error durante la operaci\u00f3n');</script>");
	}			

	echo ("<script>window.close(); MM_openBrWindow('usuAdmin.php','EF','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");				

}
?>

<script type="text/javascript">
	function valida()
	{
		var campos_tex = ["Perfil","Usuario"];	
		
		//SI EL PERFIL SELECCIONADO ES DE CONSULTA
		if(document.getElementById("Perfil").value==3)
		{		
			campos_tex.push("Finicio","Final");
		}
		
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
	
	function mostrar(valor)
	{
		//SI EL PERFIL SELECCIONADO ES DE CONSULTA
		if(valor.value==3)
		{
			document.getElementById("divFinicio").style.display="block";
			document.getElementById("divFinal").style.display="block";			
		}
		else
		{
			document.getElementById("divFinicio").style.display="none";
			document.getElementById("divFinal").style.display="none";						
		}
	}
 </script>

<form id="formulario" name="formulario" method="post">
  
   <div class="form-group" id="divUsuario">
    <label for="">Usuario</label>
    <select name="Usuario" id="Usuario" class="form-control">
    				<option value="">Seleccione un usuario</option>
                    <?
						$cur_usu=mssql_query("
						select (apellidos+' '+nombre) nombres, unidad from HojaDeTiempo.dbo.Usuarios 
						where retirado is null and unidad not in (
							select unidad from EFUsuarios
						)
						order by apellidos, nombre" );
						
						while($datos_u=mssql_fetch_array($cur_usu))
						{
						?>
                        	<option value="<?=$datos_u["unidad"] ?>"><? echo  ucwords(strtolower(($datos_u["nombres"]))); ?></option>
                        <?	
						}
                    ?>
     </select>
     <span id="helpUsuario" class="help-block" style="display:none;" >Por favor seleccione el usuario.</span>
  </div> 
  
  <div class="form-group" id="divPerfil">
    <label for="">Perfil</label>
        <select name="Perfil" id="Perfil" class="form-control" onChange="mostrar(this)" >
            <option value="">Seleccione un Perfil</option>                    
<?php
                $cur_per=mssql_query("select * from EFPerfiles where estado=1");
                while($datos_per=mssql_fetch_array($cur_per) )
                {
?>                        	
                    <option value="<?=$datos_per[id_perfil] ?>"><?=$datos_per[perfil] ?></option>
<?                                
                 }
?>							 
    </select>
     <span id="helpPerfil" class="help-block" style="display:none;" >Por favor seleccione el perfil.</span>
  </div>  

    <div class="form-group" id="divFinicio" style="display:none;" >
    <label for="">Fecha de Inicio</label>    
	<div class="container-fluid">
      <div class="col-sm-3">
       <div class="input-group">
        <input class="form-control" id="Finicio" name="Finicio" placeholder="MM/DD/YYYY" value="<?=$Finicio ?>"   readonly type="text"/>
        <div class="input-group-addon">
         <i class="glyphicon glyphicon-calendar">
         </i>
        </div>
       </div>
      </div>
    </div>   
     <span id="helpFinicio" class="help-block" style="display:none;" >La fecha de inicio es obligatoria.</span>
  </div>               


    <div class="form-group" id="divFinal" style=" display:none; " >
    <label for="">Fecha de Finalizacion</label>    
	<div class="container-fluid">
      <div class="col-sm-3">
       <div class="input-group">
        <input class="form-control" id="Final" name="Final" placeholder="MM/DD/YYYY" value="<?=$Final ?>"   readonly type="text"/>
        <div class="input-group-addon">
         <i class="glyphicon glyphicon-calendar">
         </i>
        </div>
       </div>
      </div>
    </div>   
   	<span id="helpFinal" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n es obligatoria.</span>
    <span id="helpFinal2" class="help-block" style="display:none;" >La fecha de finalizaci&oacute;n es menor a la fecha de inicio, por favor verifique.</span>    
  </div>    
  
  
   <div style="text-align:right" >
      <button type="button" class="btn btn-primary" onClick="valida()" >Grabar</button>  
       <input name="recarga" type="hidden" id="recarga" value="1">
   </div>
    
</form>
<?php
	include("inferior.php"); 
?>