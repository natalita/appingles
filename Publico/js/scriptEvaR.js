$(document).ready(function() {
    $("#openModalBtn").click(function() {
        $("#myModal").modal("show");
        $("#panelRespuestas").hide(); // Ocultar el panel de respuestas incorrectas
        $("#respuestasIncorrectas").empty(); // Borrar el contenido de las respuestas incorrectas
    });

    $("#myModal").on("hidden.bs.modal", function() {
        $("#listeningForm")[0].reset();
    });

    $("#listeningForm").submit(function(event) {
        event.preventDefault();

        var respuestasIncorrectas = [];

        for (var i = 0; i < respuestas.length; i++) {
            var pregunta = $('input[name="pregunta-' + i + '"]:checked');
            if (pregunta.length === 0) {
                respuestasIncorrectas.push({
                    pregunta: preguntas[i],
                    respuestaCorrecta: respuestas[i],
                    respuestaUsuario: "No respondida"
                });
            } else if (pregunta.val() !== respuestas[i]) {
                respuestasIncorrectas.push({
                    pregunta: preguntas[i],
                    respuestaCorrecta: respuestas[i],
                    respuestaUsuario: pregunta.val()
                });
            }
        }

        var panelRespuestas = $("#panelRespuestas");
        var respuestasIncorrectasDiv = $("#respuestasIncorrectas");
        respuestasIncorrectasDiv.empty();
        if (respuestasIncorrectas.length === 0) {
            respuestasIncorrectasDiv.html("¡Todas las respuestas son correctas!");
        } else {
            respuestasIncorrectasDiv.append('<ul>');
            respuestasIncorrectas.forEach(function(respuestaIncorrecta) {
                var preguntaItem = $("<li>").text(respuestaIncorrecta.pregunta);
                var respuestaCorrectaItem = $("<li>").text("Respuesta correcta: " + respuestaIncorrecta.respuestaCorrecta);
                var respuestaUsuarioItem = $("<li>").text("Tu respuesta: " + (respuestaIncorrecta.respuestaUsuario ? respuestaIncorrecta.respuestaUsuario : "No respondida"));
                respuestasIncorrectasDiv.append(preguntaItem, respuestaCorrectaItem, respuestaUsuarioItem);
            });
            respuestasIncorrectasDiv.append('</ul>');
        }
        panelRespuestas.show();

        $("#myModal").modal("hide");
    });
});










