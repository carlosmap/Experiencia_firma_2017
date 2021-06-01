<?
	session_start();
	//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
	include "funciones.php";
	include "validacion.php";
	include "validaUsrBd.php";
	#/*		
	header("Content-Type: application/ms-excel");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=Consolidado-Proyecto(" . date('Y-m-d') .  ")(".date('H-i').").xls");
	#*/
	$sql02="SELECT DISTINCT A.unidad, B.nombre, B.apellidos, C.nombre nomCat ";
	$sql02=$sql02." FROM  FacturacionProyectos A, Usuarios B, Categorias C ";
	$sql02=$sql02." WHERE A.unidad = B.unidad ";
	$sql02=$sql02." AND A.id_categoria = C.id_categoria ";
	$sql02=$sql02." AND A.id_proyecto = ".$cualProyecto;
	$sql02=$sql02." AND A.vigencia = ".$pAno;
	$sql02=$sql02." AND A.mes = ".$pMes;
	#echo $sql02;
	$cursor02=mssql_query($sql02);
?>


<table width="100%"  border="1" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
  <tr class="TituloTabla2">
	<td colspan="3">EMPLEADO</td>
	<td colspan="6">INFORMACI&Oacute;N REPORTADA </td>
	<td width="30%" colspan="3">PLANEACI&Oacute;N</td>
  </tr>
  <tr class="TituloTabla2">
	<td width="5%">Unidad</td>
	<td width="15%">Usuarios que reportaron al proyecto </td>
	<td width="5%">Categor&iacute;a</td>
	<td>Actividad</td>
	<td>Loc</td>
	<td>Clase de tiempo </td>
	<td>Cargo</td>
	<td>Horas reportadas </td>
	<td>Valor <br /> 
	reportado
</td>
	<td width="15%">Actividad </td>
	<td width="15%">Horas planeadas </td>
	<td width="30%">Valor <br />
planeado</td>
  </tr>
  <?
  while ($reg02=mssql_fetch_array($cursor02)) {
	  
		#	Facturación
		$sql03="SELECT A.unidad, A.id_proyecto, A.id_actividad, A.localizacion, A.clase_tiempo, A.cargo, SUM(A.horasMesF) horasFacturadas,   " ;
		$sql03=$sql03." SUM(A.valorFacturado) valorFacturadoAct, B.nombre nomActividad, B.macroactividad " ;
		$sql03=$sql03." FROM FacturacionProyectos A, ActividadesEDT B " ;
		$sql03=$sql03." WHERE A.id_proyecto = B.id_proyecto " ;
		$sql03=$sql03." and A.id_actividad = B.id_actividad " ;
		$sql03=$sql03." and A.id_proyecto = " . $cualProyecto ;
		$sql03=$sql03." and A.unidad = "  . $reg02[unidad] ;
		
		//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
		//sino con lo seleccionado en las listas mes y año
		if ($pMes == "") {
			$sql03=$sql03." and  A.vigencia = year(getdate()) " ;
			$sql03=$sql03." and  A.mes = month(getdate()) " ;
		}
		else {
			$sql03=$sql03." and  A.vigencia =  " . $pAno;
			$sql03=$sql03." and  A.mes =  " . $pMes;
		}
		
	
		$sql03=$sql03." GROUP BY A.unidad, A.id_proyecto, A.id_actividad, A.localizacion, A.clase_tiempo, A.cargo, B.nombre, B.macroactividad " ;
		
		$cursor03 = mssql_query($sql03);
		
		$nReg3=mssql_num_rows($cursor03);
		#	Planeación
		//--Planeación por persona
		$sql04="SELECT A.* , B.macroactividad, B.nombre nomActividad " ;
		$sql04=$sql04."  FROM PlaneacionProyectos A, ActividadesEDT B " ;
		$sql04=$sql04."  WHERE A.id_proyecto = B.id_proyecto " ;
		$sql04=$sql04."  and A.id_actividad = B.id_actividad " ;
		$sql04=$sql04."  AND A.id_proyecto = " . $cualProyecto ;
		$sql04=$sql04." and A.unidad = " . $reg02[unidad] ;
		
		//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
		//sino con lo seleccionado en las listas mes y año
		if ($pMes == "") {
			$sql04=$sql04." and  A.vigencia = year(getdate()) " ;
			$sql04=$sql04." and  A.mes = month(getdate()) " ;
		}
		else {
			$sql04=$sql04." and  A.vigencia =  " . $pAno;
			$sql04=$sql04." and  A.mes =  " . $pMes;
		}
		$cursor04 = mssql_query($sql04);
		$nReg4=mssql_num_rows($cursor04);
		
		if($nReg3<$nReg4)
		{
			$f1=$nReg4;
		}
		else
		{
			$f2=$nReg3;
		}
			
  ?>
  <tr class="TxtTabla">
	<td width="5%" valign="top"><? echo $reg02[unidad]; ?></td>
	<td width="15%" valign="top"><? echo ucwords( strtolower($reg02[apellidos] . ", " . strtolower($reg02[nombre]) )); ?></td>
	<td width="5%" align="center" valign="top"><? echo $reg02[nomCat]; ?></td>
	<td colspan="6" valign="top"><?
	

	?>
	  <table width="100%"  border="1" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
		<?
			while ($reg03=mssql_fetch_array($cursor03)) 
			{
        ?>
		<tr class="TxtTabla">
		  <td><? echo "[" . $reg03[macroactividad] . "] " . $reg03[nomActividad]; ?></td>
		  <td width="8%" align="right"><? echo $reg03[localizacion]; ?></td>
		  <td width="8%" align="right"><? echo $reg03[clase_tiempo]; ?></td>
		  <td width="8%" align="right"><? echo $reg03[cargo]; ?></td>
		  <td width="8%" align="right"><? echo $reg03[horasFacturadas]; ?></td>
		  <td width="12%" align="right">$ <? echo number_format($reg03[valorFacturadoAct], 0, ",", "."); ?></td>
		</tr>
		<? 
			}
			if($nReg3<$nReg4)
			{
				while($nReg3<$f1)
				{
		?>
            <tr class="TxtTabla">
              <td>&nbsp;</td>
              <td width="8%" align="right">&nbsp;</td>
              <td width="8%" align="right">&nbsp;</td>
              <td width="8%" align="right">&nbsp;</td>
              <td width="8%" align="right">&nbsp;</td>
              <td width="12%" align="right">&nbsp;</td>
            </tr>        
        <?
					$nReg3++;
				}
			}		
		?>
	  </table></td>
	<td width="30%" colspan="3" valign="top"><?

	?>
	  <table width="100%"  border="1" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
		<?
	        while ($reg04=mssql_fetch_array($cursor04)) 
			{
        ?>
		<tr class="TxtTabla">
		  <td><? echo "[" . $reg04[macroactividad] . "] " . $reg04[nomActividad]; ?></td>
		  <td width="12%" align="right"><? echo $reg04[horasMes]; ?></td>
		  <td width="15%" align="right">$ <? echo number_format($reg04[valorPlaneado], 0, ",", "."); ?></td>
		</tr>
		<? 
			}
			#/*
			if($nReg4<$nReg3)
			{
				while($nReg4<$f2)
				{
		?>
            <tr class="TxtTabla">
              <td>&nbsp;</td>
              <td width="8%" align="right">&nbsp;</td>
              <td width="8%" align="right">&nbsp;</td>
            </tr>        
        <?
					$nReg4++;
				}
			}
			#*/
		?>		
	  </table>
      </td>
  </tr>
  <?
  }
  ?>
</table>
