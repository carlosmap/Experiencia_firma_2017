// JavaScript Document
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}


//VALIDA VALORES ENTEROS
var nav4 = window.Event ? true : false;
function acceptNum(evt){   
var key = nav4 ? evt.which : evt.keyCode;   
return (key <= 13 || (key>= 48 && key <= 57));
}

//VALIDA VALORES DECIMALES
var nav4 = window.Event ? true : false;
function acceptNumD(evt){   
var key = nav4 ? evt.which : evt.keyCode;   
return (key <= 13 || (key>= 48 && key <= 57)|| key == 46 );
}

//FUNCIONES PARA MOSTRAR EL CALENDARIO EN LOS CAMPOS DE FECHA "Finicio"
	$(document).ready(function(){

		var date_input=$('input[name="Finicio"]'); //our date input has the name "date"
		var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
		date_input.datepicker({
			format: 'mm/dd/yyyy',
			container: container,
			todayHighlight: true,
			autoclose: true,
		})
	})

//FUNCIONES PARA MOSTRAR EL CALENDARIO EN LOS CAMPOS DE FECHA "Final"	
	$(document).ready(function(){

		var date_input=$('input[name="Final"]'); //our date input has the name "date"
		var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
		date_input.datepicker({
			format: 'mm/dd/yyyy',
			container: container,
			todayHighlight: true,
			autoclose: true,
		})
	})	

//FUNCION PARA LA COMPARACION DE FECHAS
function compare_fecha(fecha, fecha2)  
{  
	var xMonth=fecha.substring(0, 2);  
	var xDay=fecha.substring(3, 5);  
	var xYear=fecha.substring(6,10);  
	var yMonth=fecha2.substring(0, 2);  
	var yDay=fecha2.substring(3, 5);  
	var yYear=fecha2.substring(6,10);  
	//si el año de la fecha ingresada es menor a la fecha actual

	//alert("mes "+xMonth+" Ano "+xYear+" Dia "+xDay+"\n mes2 "+yMonth+" Ano2 "+yYear+" Dia2 "+yDay);

	if (xYear>yYear)  
	{  
		return(true)  
	}  
	else  
	{  
	  if (xYear == yYear)  
	  {  
		//si el mes de la fecha ingresada es menor  a la fecha actual
		if (xMonth> yMonth)  
		{  
			return(true)  
		}  
		else  
		{   
			//si el mes ingresado y el actual son iguales			
		  if (xMonth == yMonth)  
		  {  
			//si el dia de la fecha ingresada es menor a la de la fecha actual
			if (xDay> yDay)  
			  return(true);  
			else  
			  return(false);  
		  }  
		  
		  else  
			return(false);  
		}  
	  }
	  //si el año de la fecha ingresada es mayor a la actual	  
	  else  
		return(false);  
	}  
} 


///FUNCION PARA VALIDAR LOS CAMPOS DE UN FORMULARIO
function valida_campos(array_campos,tipo, cantidad_items)
{
	var error=0;

//alert("Ingresssaaa: "+error)	;		
	//VALIDA CAMPOS DE TEXTO
	if(tipo==1)
	{
		/* REINICIA LAS CLASES DE LOS ELEMENTOS */
		for(var u=0; u<array_campos.length;u++)
		{
			document.getElementById("div"+array_campos[u]).className="form-group";		
			document.getElementById("help"+array_campos[u]).style.display="none";	
		}
	
		/* VALIDA LOS CAMPOS DE TEXTO */
		for(var u=0; u<array_campos.length;u++)
		{	
			if(document.getElementById(array_campos[u]).value=="")
			{
				document.getElementById("div"+array_campos[u]).className="form-group has-error";		
				document.getElementById("help"+array_campos[u]).style.display="inline-block";					
				error++;
			}
			
		}				
	}
	//VALIDA CAMPOS TIPO CHECKBOX
	if(tipo==2)
	{
		
		for(var u=0; u<array_campos.length;u++)
		{
//			document.getElementById("div"+array_campos[u]).className="form-group";		
			document.getElementById("help"+array_campos[u]).style.display="none";	
		}

		/* VALIDA LOS CAMPOS CHECKBOX */
		for(var u=0; u<array_campos.length;u++)
		{	
//alert(cantidad_items+" -- "+array_campos[u]);
			var cont_che=0;
			for(i=0;i<cantidad_items;i++)
			{
				window['campo']=array_campos[u]+i;
				if(document.getElementById(campo).checked)
				{	
					cont_che++;
				}
				//alert(cont_che + " - " +document.getElementById(afectaf).checked+" - "+document.getElementById(afectaf).value);
			}
			
			if(cont_che==0)
			{
//				document.getElementById("div"+array_campos[u]).className="form-group has-error";		
				document.getElementById("help"+array_campos[u]).style.display="inline-block";					
				error++;
			}			
		}
	}
	
//alert("funci: "+error)	;	
	//VALIDA RANGO DE FECHAS
	if(tipo==5)
	{
		if( (document.getElementById("Finicio").value!="") && (document.getElementById("Final").value!="" ) )
		{
			document.getElementById("divFinal").className="form-group";		
			document.getElementById("helpFinal2").style.display="none";	
			
			//VALIDACION DE FECHAS
			if (compare_fecha( document.getElementById("Finicio").value , document.getElementById("Final").value))
			{  
		//	  alert("");  
				document.getElementById("divFinal").className="form-group has-error";		
				document.getElementById("helpFinal2").style.display="inline-block";		
				error++;				  
			}
		}
	}
	
	return error;
}