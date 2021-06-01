<html lang="es">
<head>
<?php
	session_start();
	include("../../portal/verificaRegistro2.php"); 
	include("conectaBD.php"); 
	include("funciones.php"); 	
	$conexion = conectar();
	
	//SE INCLUYE EN SCRIPT, PARA EL ENVIO DEL EMAIL
	include ("../../portal/fncEnviaMailPEAR.php");

	//VERIFICA SI LA VARIALBE DE SESSION, DEL PERFIL DE USUARIO HA SIDO DEFINIDO, ESTO CUANDO SE INGRESA A LA APLICACION 
	if(!isset($_SESSION["sesPerfilUsuarioExpFir"]))
	{
		//ARCHIVO ENCARGADO INICIALMENTE, DE CARGAR EL PERFIL DEL USUARIO 
		include("carga_info.php");
	}
//echo " ************* ".$_SESSION["sesPerfilUsuarioExpFir"];
?>	
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="img/icoIngetec.ico" > 

    
    <title>Experiencia de la Firma</title>
    <link href="css/bootstrap.css" rel="stylesheet">
   
<!--    <link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'> -->
    <link href="css/jquerysctipttop.css" rel="stylesheet" type="text/css">
      <!--  <link href="http://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css"> -->
		<!--<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css">-->
      <!-- 		<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script> -->
	<script type="text/javascript" src="js/jquery.easing.min.js"></script>
	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
    
	<script type="text/javascript" src="js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" href="css/bootstrap-datepicker3.css"/>
   	<script type="text/javascript" src="js/bootstrap.min.js"></script>

    <script type="text/javascript" src="js/script.js"></script>    
    <link rel="stylesheet" href="css/estilos.css" type="text/css"> 

	<script src=”js/prefixfree.min.js” type="text/javascript"></script>    
</head>
<body>


<?php
	//FUNCION QUE VISUALIZA DEN LA DIFERENTES PAGINA DE LA HERRAMIENTA, EL VANER CORRESPONDIENTE
	//CUANDO 1= VENTANA PRINCIPAL, 2= VENTANA EMERGENTE
	function banner($tipo, $titulo)
	{
		//VENTANAS PRINCIPALES
		if($tipo==1)
		{
?>
			<script type="text/javascript" >
                window.name="EF";
            </script>
    
            <div class="container">
              <div style="text-align:left; " class="linea"> 
                 <img src="img/Logo-INGETEC.jpg" >  
                
                </div>
              <div style="text-align:right;" class="linea"  ></div>                
            </div>
            <div class="espacioAzul espacioAzulTexto" style="height:50px;">
	           <span style="vertical-align:middle">Experiencia de la firma</span> 
            </div>
            <br>
            <br>
<?php
		}
		
		//VENTANAS EMERGENTES
		if($tipo==2)
		{
?>
			<script type="text/javascript" >
                window.name="EF2";
            </script>
    
    
            <div style="text-align:left;" class="linea2"  >Experiencia de la firma - <?=$titulo ?></div>
            
            <div class="espacioAzul">
            </div>
            <br>
		    <div class="container ">
<?php			
		}
		
	}
?>   