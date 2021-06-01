<?php 

include("encabezado.php"); 
//tipo=1 (para ventanas principales)
banner(1);
?>

<div class="container" >

		<? include ("menuAdmin.php"); ?>

        <div  class="titulo_secccion">
                M&oacute;dulo Administrativo
                <div class="espacioAzul">
                 </div>
        </div>
        <br>
        <div class="row col-md-12" >
        	<div class="col-md-4 col-md-offset-4 " >
            	<button class="btn btn-primary btn-lg btn-block"  onclick="location.href='usuAdmin.php'"><i class="glyphicon glyphicon-user"></i> Usuarios</button>
            </div>
       </div>        

</div>


<?php
	include("inferior.php"); 
?>