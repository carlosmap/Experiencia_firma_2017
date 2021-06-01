<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
   		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
   		<link href="css/jqueryui.css" type="text/css" rel="stylesheet"/>
        <script>
	       	$(document).ready(function(){ 	
				$( "#matricula" ).autocomplete({
      				source: "buscaralumno.php",
      				minLength: 2
    			});
    			
    			$("#matricula").focusout(function(){
    				$.ajax({
    					url:'alumno.php',
    					type:'POST',
    					dataType:'json',
    					data:{ matricula:$('#matricula')}
    				}).done(function(respuesta){
    					$("#nombre").val(respuesta.nombre);
    					$("#paterno").val(respuesta.paterno);
    					$("#materno").val(respuesta.materno);
    				});
    			});    			    		
			});
        </script>
                    
    </head>
    <body>
        
       	<form>
       		<label for="matricula">Matricula:</label>
	    	<input type="text" id="matricula" name="matricula" value=""/>
		    <label for="nombre">Nombre:</label>
		    <input type="text" id="nombre" name="nombre" value=""/>
		    <label for="paterno">Paterno:</label>
    		<input type="text" id="paterno" name="paterno" value=""/>
    		<label for="materno">Materno:</label>
	    	<input type="text" id="materno" name="materno" value=""/>
		</form>
    </body>
</html>
