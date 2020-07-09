
window.onload=function(){
	document.getElementById("importarConcellos").addEventListener("click",anadirAyuntamientos,false);
	document.getElementById("listarAlertas").addEventListener("click",mostrarAlertas,false);
	document.getElementById("avisosPrueba").addEventListener("click",anadirAvisosPrueba,false);
	//document.getElementById("alertasPersonalizadasPrueba").addEventListener("click",anadirAlertasPersonalizadas,false);
    document.getElementById("alertasPersonalizadasPrueba").addEventListener("click",forzarExcepcion,false);


}

function anadirAyuntamientos(){
	let ayunt = listarAyuntamientos();

	for (var i = 0; i < ayunt.length; i++) {
		  $.ajax({
                    url: "http://127.0.0.1:8000/api/alertainformacion/newAyuntamiento",
                    method: 'post',
                    dataType: 'json', 
                    data: {"codConcello" : ayunt[i].codConcello, "area" : JSON.stringify(ayunt[i].area)}, 
                    success: function (data) {
 
                        alert("Exito");
             
                    },
                    error: function (err) {
                        alert(err);
                    }
 
                });
	}
}

function mostrarAlertas(){
	 $.ajax({
                    url: "http://127.0.0.1:8000/api/alertainformacion",
                    method: 'get', 
                    dataType: "json",
                    success: function (data) {
                    	for (var i = 0; i < data.data.length; i++) {
                    		console.log(data.data[i].cod_concello);
                    		console.log(JSON.parse(data.data[i].area));
                    	}
                        alert("Exito");
             
                    },
                    error: function (err) {
                        alert(err);
                    }
 
                });
}

function anadirAvisosPrueba(){
	let avisosMonforte= new Array();
	avisosMonforte.push({"latitud":42.527974,"longitud":-7.519026,"ip":"127.0.0.1","foto":"urlFoto","comentario":"Este aviso esta en monforte","tipo":"incendio","codconcello":"27031"});
	avisosMonforte.push({"latitud":42.513298,"longitud":-7.536449,"ip":"127.0.0.1","foto":"urlFoto","comentario":"Este aviso esta en monforte","tipo":"incendio","codconcello":"27031"});
	avisosMonforte.push({"latitud":42.522218,"longitud":-7.514992,"ip":"127.0.0.1","foto":"urlFoto","comentario":"Este aviso esta en monforte","tipo":"sospecha","codconcello":"27031"});
	avisosMonforte.push({"latitud":42.5201257,"longitud":-7.509499,"ip":"127.0.0.1","foto":"urlFoto","comentario":"Este aviso esta en monforte","tipo":"incendio","codconcello":"27031"});
	
	let avisosOrense= new Array();
	avisosOrense.push({"latitud":42.339641,"longitud":-7.880116,"ip":"127.0.0.1","foto":"urlFoto","comentario":"Este aviso esta en orense","tipo":"incendio","codconcello":"27054"});
	avisosOrense.push({"latitud":42.321366,"longitud":-7.863293,"ip":"127.0.0.1","foto":"urlFoto","comentario":"Este aviso esta en orense","tipo":"sospecha","codconcello":"27054"});
	avisosOrense.push({"latitud":42.354965,"longitud":-7.88887,"ip":"127.0.0.1","foto":"urlFoto","comentario":"Este aviso esta en orense","tipo":"incendio","codconcello":"27054"});
	

	for (var i = 0; i < avisosMonforte.length; i++) {
		  $.ajax({
                    url: "http://127.0.0.1:8000/api/aviso/new",
                    method: 'post',
                    dataType: 'json', 
                    data: {"latitud" : avisosMonforte[i].latitud, "longitud" : avisosMonforte[i].longitud, "ip" : avisosMonforte[i].ip,"foto" : avisosMonforte[i].foto,"comentario" : avisosMonforte[i].comentario,"tipo" : avisosMonforte[i].tipo, "codconcello" : avisosMonforte[i].codconcello},
                    success: function (data) {
 
                        alert("Exito");
             
                    },
                    error: function (err) {
                        alert(err);
                    }
 
                });
	}

		for (var i = 0; i < avisosOrense.length; i++) {
		  $.ajax({
                    url: "http://127.0.0.1:8000/api/aviso/new",
                    method: 'post',
                    dataType: 'json', 
                    data: {"latitud" : avisosOrense[i].latitud, "longitud" : avisosOrense[i].longitud, "ip" : avisosOrense[i].ip,"foto" : avisosOrense[i].foto,"comentario" : avisosOrense[i].comentario,"tipo" : avisosOrense[i].tipo, "codconcello" : avisosOrense[i].codconcello},
                    success: function (data) {
 
                        alert("Exito");
                        console.log(data);
                    },
                    error: function (err) {
                        alert(err);
                        console.log(err);
                    }
 
                });
	}
}

function anadirAlertasPersonalizadas(){
	let alertas = new Array();
	let mensaje = "";
	alertas.push({"area": [{"lat":42.517473,"lon":-7.521257},{"lat":42.531326,"lon":-7.524176},{"lat":42.53101,"lon":-7.495337},{"lat":42.512855,"lon":-7.486925}]});


	for (var i = 0; i < alertas.length; i++) {
		  $.ajax({
                    url: "http://127.0.0.1:8000/api/alertainformacion/newAyuntamiento",
                    method: 'post',
                    dataType: 'json', 
                    data: {"area" : JSON.stringify(alertas[i].area)}, 
                    success: function (data) {
 
                        mensaje="Exito";
             
                    },
                    error: function (err) {
                        alert(err);
                    }
 
                });
	}
	alert(mensaje);
}

function forzarExcepcion(){
    $.ajax({
        url:"http://127.0.0.1:8000/api/aviso/new",
        method: "post",
        dataType : "json",
        data: {"latitud":10, "longitud": 50},
        success: function(data){
            alert(data);
        },
        error: function (err) {
                        alert(err);
        }
    });
}