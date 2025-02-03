<h1>Evaluación de respuestas</h1>

<?php
// Array de preguntas y respuestas


// Obtener las respuestas enviadas por el formulario
$respuestasUsuario = array();
for ($i = 0; $i < count($preguntas); $i++) {
    $respuesta = $_POST["pregunta-" . $i];
    $respuestasUsuario[] = $respuesta;
}

// Evaluar cada respuesta
$respuestasIncorrectas = array();
for ($i = 0; $i < count($respuestas); $i++) {
    if ($respuestasUsuario[$i] !== $respuestas[$i]) {
        $respuestasIncorrectas[] = array(
            "pregunta" => $preguntas[$i],
            "respuestaCorrecta" => $respuestas[$i],
            "respuestaUsuario" => $respuestasUsuario[$i]
        );
    }
}

// Mostrar las respuestas incorrectas
if (count($respuestasIncorrectas) === 0) {
    echo "<p>Todas las respuestas son correctas. ¡Felicidades!</p>";
} else {
    echo "<h2>Respuestas incorrectas:</h2>";
    foreach ($respuestasIncorrectas as $respuestaIncorrecta) {
        echo "<p><strong>" . $respuestaIncorrecta['pregunta'] . "</strong><br>";
        echo "Respuesta correcta: " . $respuestaIncorrecta['respuestaCorrecta'] . "<br>";
        echo "Tu respuesta: " . $respuestaIncorrecta['respuestaUsuario'] . "</p>";
    }
}
?>