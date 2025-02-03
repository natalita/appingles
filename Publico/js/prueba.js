url = window.location.href;
console.log("URL en prueba.js: ", url);
if (url.includes('modulo=prueba')) {
    console.log("Javascripts de prueba cargado");
    let questions;
    let unidadId;

    // En tu script JavaScript (prueba.js)
    document.addEventListener("DOMContentLoaded", function () {
        const currentPath = window.location.pathname; // Obtiene el pathname dinámicamente
        console.log("Path actual: ", currentPath);
        const isPruebaPage = currentPath.endsWith("panel.php") && url.includes("modulo=prueba");

        if (isPruebaPage) {
            console.log("11");
            const unidadButtons = document.querySelectorAll(".unidad-buttons .unidad-btn");
            const startBtn = document.querySelector(".start_btn");

            unidadButtons.forEach(function (button, index) {
                console.log("22");
                button.addEventListener("click", async function () {
                    unidadId = button.getAttribute("data-id");
                    console.log("Clic en la unidad con ID:", unidadId);


                    try{
                        // Realizar la consulta a la base de datos para verificar si el estudiante ya ha realizado las pruebas de la unidad
                        const response = await fetch('../Modelo/check_pruebas_realizadas.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ unidadId: unidadId, id_usuario: userId}),
                        });
                        
                        if (!response.ok) {
                            throw new Error('Error en la solicitud: ' + response.statusText);
                        }

                        const data = await response.json();
                        console.log("Respuesta de la consulta:", data);

                        // Filtrar preguntas por el id_unidad
                        const preguntasFiltradas = preguntas.filter(function (pregunta) {
                            console.log("0 Aqui la unidad: " + JSON.stringify(pregunta.id_unidad));

                            return data.remaining_activity_ids.includes(pregunta.id_actividad.toString());
                        });

                        console.log("Preguntas filtradas:", preguntasFiltradas);
                        console.log("length: " + preguntasFiltradas.length);

                        if (preguntasFiltradas.length > 0 && !data.pruebasRealizadas) {
                            // Oculta los demás botones de unidad
                            unidadButtons.forEach((otherButton, otherIndex) => {
                                if (otherIndex !== index) {
                                    otherButton.style.display = "none";
                                }
                            });

                            // Mostrar el botón de inicio si hay preguntas para la unidad y no se han realizado las pruebas
                            startBtn.removeAttribute("hidden");

                            // Obtener solo las primeras 10 preguntas después de reorganizarlas
                            let allQuestions = preguntasFiltradas.slice(); // Copiar el array para no modificar el original
                            let shuffledQuestions = shuffleArray(allQuestions);

                            // Seleccionar solo las primeras 10 preguntas después de reorganizarlas
                            let selectedQuestions = shuffledQuestions.slice(0, 10);

                            // Asignar las preguntas reorganizadas y seleccionadas a la variable "questions"
                            questions = selectedQuestions;
                            console.log("Preguntas reorganizadas y seleccionadas:", questions);
                            document.getElementById("sideBarHidden").setAttribute("hidden", "true");
                            document.getElementById("sideBarHidden2").setAttribute("hidden", "true");
                            document.getElementById("navBarHidden").setAttribute("hidden", "true");
                        } else {
                            // Mostrar la card que indica que no hay tests pendientes
                            document.getElementById("noTestsCard").style.display = "block";

                            // Ocultar la card después de 3 segundos
                            setTimeout(function() {
                                document.getElementById("noTestsCard").style.display = "none";
                            }, 3000);

                            // Ocultar el botón de inicio
                            startBtn.setAttribute("hidden", "true");
                        }
                    } catch (error) {
                        console.error("Error al procesar la respyesta:", error);
                    }
                });
            });
            console.log("33");
            // Obtener todas las actividades y reorganizar aleatoriamente
            let allQuestions = preguntas.slice(); // Copiar el array para no modificar el original
            let shuffledQuestions = shuffleArray(allQuestions);

            // Seleccionar solo las primeras 10 preguntas después de reorganizarlas
            let selectedQuestions = shuffledQuestions.slice(0, 10);

            // Asignar las preguntas reorganizadas y seleccionadas a la variable "questions"
            questions = selectedQuestions;
            console.log("zzzPreguntas reorganizadas y seleccionadas:", questions);
        }
    });


    // Función para reorganizar aleatoriamente un array (algoritmo de Fisher-Yates)
    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    }




    //seleccionando todos los elementos requeridos
    const start_btn = document.querySelector(".start_btn button");
    const info_box = document.querySelector(".info_box");
    const exit_btn = info_box.querySelector(".buttons .quit");
    const continue_btn = info_box.querySelector(".buttons .restart");
    const quiz_box = document.querySelector(".quiz_box");
    const result_box = document.querySelector(".result_box");
    const option_list = document.querySelector(".option_list");
    const time_line = document.querySelector("header .time_line");
    const timeText = document.querySelector(".timer .time_left_txt");
    const timeCount = document.querySelector(".timer .timer_sec");
    const videoPlayer = document.createElement("video");

    // si se hace clic en el botón Iniciar prueba

    start_btn.onclick = ()=>{
        info_box.classList.add("activeInfo"); //show info box
    }

    // si se hace clic en el botón Salir del cuestionario
    exit_btn.onclick = ()=>{
        info_box.classList.remove("activeInfo"); //ocultar cuadro de información
    }

    let timeValue = 90;
    let que_count = 0;
    let que_numb = 1;
    let userScore = 0;
    let counter;
    let counterLine;
    let widthValue = 0;

    // si se hace clic en el botón continuar prueba
    continue_btn.onclick = ()=>{
        info_box.classList.remove("activeInfo"); //hide info box
        quiz_box.classList.add("activeQuiz"); //show quiz box
        showQuestions(0); //calling showQestions function
        queCounter(1); //passing 1 parameter to queCounter
        startTimer(timeValue) //calling startTimer function
        startTimerLine(timeValue); //calling startTimerLine function
    }

    const refresh_page = result_box.querySelector(".buttons .restart");
    const quit_quiz = result_box.querySelector(".buttons .quit");

    // si se hace clic en el botón Refresh
    refresh_page.onclick = ()=>{
        location.reload();
    }

    // si se hace clic en el botón Salir del cuestionario
    quit_quiz.onclick = ()=>{
        window.location.reload(); //reload the current window
    }

    const next_btn = document.querySelector("footer .next_btn");
    const bottom_ques_counter = document.querySelector("footer .total_que");

    // si se hace clic en el botón Next Que
    next_btn.onclick = ()=>{
        if(que_count < questions.length - 1){ //if question count is less than total question length
            que_count++; //increment the que_count value
            que_numb++; //increment the que_numb value
            showQuestions(que_count); //calling showQestions function
            queCounter(que_numb); //passing que_numb value to queCounter
            clearInterval(counter); //clear counter
            clearInterval(counterLine); //clear counterLine
            startTimer(timeValue); //calling startTimer function
            console.log("Siguiente pregunta");
            time_line.style.width = "0px"; //reset time line width
            console.log("Nuevo valor de time_line: " + time_line.width);
            startTimerLine(timeValue); //calling startTimerLine function
            timeText.textContent = "Time left"; //change the timeText to Time Left
            next_btn.classList.remove("show"); //hide the next button
        }else{
            clearInterval(counter); //clear counter
            clearInterval(counterLine); //clear counterLine
            showResult(); //calling showResult function
        }
    }


    function showQuestions(index) {
        const que_text = document.querySelector(".que_text");
        const quiz_box = document.querySelector(".quiz_box");

        if (questions.length > 0 && questions[index]) {
            let que_tag = '<span>' + (index + 1) + "." + questions[index].pregunta + '</span>';

            // Crear la etiqueta de video de forma dinámica
            let video_tag = '<div class="video-container"><video class="video-player" controls autoplay>';
            video_tag += '<source src="' + questions[index].ruta_video + '" type="video/mp4">';
            video_tag += 'Tu navegador no soporta el tag de video.';
            video_tag += '</video></div>';

            let option_tag = video_tag +
                '<div class="option">' + questions[index].opciones[0] + '<span></span></div>'
                + '<div class="option">' + questions[index].opciones[1] + '<span></span></div>'
                + '<div class="option">' + questions[index].opciones[2] + '<span></span></div>'
                + '<div class="option">' + questions[index].opciones[3] + '<span></span></div>';

            que_text.innerHTML = que_tag;
            option_list.innerHTML = option_tag;

            const option = option_list.querySelectorAll(".option");
            for (let i = 0; i < option.length; i++) {
                option[i].setAttribute("onclick", "optionSelected(this)");
            }

            const videoContainer = document.querySelector(".video-container");
            const videoPlayer = document.querySelector(".video-player");
            resizeVideo(videoContainer, videoPlayer, quiz_box);
            window.addEventListener("resize", function() {
                resizeVideo(videoContainer, videoPlayer, quiz_box);
            });
        } else {
            console.error("No hay preguntas disponibles o el índice especificado está fuera de rango.");
        }
    }

    // creating the new div tags which for icons
    let tickIconTag = '<div class="icon tick"><i class="fas fa-check"></i></div>';
    let crossIconTag = '<div class="icon cross"><i class="fas fa-times"></i></div>';

    //if user clicked on option
    function optionSelected(answer){
        clearInterval(counter); //clear counter
        clearInterval(counterLine); //clear counterLine
        let userAns = answer.textContent; //getting user selected option
        let correcAns = questions[que_count].respuesta; //getting correct answer from array
        //Imprimir en consola el valor de mi objeto en questions[que_count], utilizando el formato  JSON.stringify
        console.log("aquiii: " + JSON.stringify(questions[que_count]));
        console.log("aquiii2: " + JSON.stringify(questions[que_count].respuesta));


        const allOptions = option_list.children.length; //getting all option items

        const videoPlayer = document.querySelector(".video-player");
        videoPlayer.pause();

        if(userAns == correcAns){ //if user selected option is equal to array's correct answer
            userScore += 1; //upgrading score value with 1
            answer.classList.add("correct"); //adding green color to correct selected option
            answer.insertAdjacentHTML("beforeend", tickIconTag); //adding tick icon to correct selected option
            console.log("Correct Answer");
            console.log("Your correct answers = " + userScore);
        }else{
            answer.classList.add("incorrect"); //adding red color to correct selected option
            answer.insertAdjacentHTML("beforeend", crossIconTag); //adding cross icon to correct selected option
            console.log("Wrong Answer");

            for(i=0; i < allOptions; i++){
                if(option_list.children[i].textContent == correcAns){ //if there is an option which is matched to an array answer 
                    option_list.children[i].setAttribute("class", "option correct"); //adding green color to matched option
                    option_list.children[i].insertAdjacentHTML("beforeend", tickIconTag); //adding tick icon to matched option
                    console.log("Auto selected correct answer.");
                }
            }
        }




        for(i=0; i < allOptions; i++){
            option_list.children[i].classList.add("disabled"); //once user select an option then disabled all options
        }
        next_btn.classList.add("show"); //show the next button if user selected any option
    }

    function showResult(){
        info_box.classList.remove("activeInfo"); //hide info box
        quiz_box.classList.remove("activeQuiz"); //hide quiz box
        result_box.classList.add("activeResult"); //show result box
        const scoreText = result_box.querySelector(".score_text");

        const percentage = (userScore / questions.length) * 100;
        scoreText.textContent = "Tu puntaje es " + percentage.toFixed(2) + "%";

        let message = "";
        if (percentage <= 25) {
            message = "Don't worry, keep practicing. 🙂";
        } else if (percentage <= 50) {
            message = "Good, but there's still room for improvement. 😄";
        } else if (percentage <= 75) {
            message = "Great job! You're on the right track. 😎";
        } else {
            message = "Congratulations! You're an expert on the topic. 🤩🎉";
        }

        // Crear el mensaje dinámico
        let scoreTag = `<span>${message}, Tienes ${userScore} de ${questions.length}</span>`;
        scoreText.innerHTML = scoreTag;

        
        // Aquí deberías realizar la consulta para guardar el valor del test para ese usuario
        // Puedes utilizar AJAX, Fetch u otra técnica según tu entorno y backend.
        // Aquí un ejemplo ficticio:
        console.log("questions:", questions);
        let actividadIds = questions.map(question => question.id_actividad);
        saveTestResult(userScore, questions.length, unidadId, actividadIds)
    }

    function startTimer(time){
        console.log("aqui: " + time);
        counter = setInterval(timer, 1000);
        function timer(){
            timeCount.textContent = time; //changing the value of timeCount with time value
            time--; //decrement the time value
            if(time < 9){ //if timer is less than 9
                let addZero = timeCount.textContent; 
                timeCount.textContent = "0" + addZero; //add a 0 before time value
            }
            if(time < 0){ //if timer is less than 0
                clearInterval(counter); //clear counter
                timeText.textContent = "Se acabo el tiempo"; //change the time text to time off
                onTimeOut();
            }
        }
    }

    function startTimerLine(time){
        counterLine = setInterval(timer, 1000);
        //utilizar solo el valor entero de la division 550/time
        let widthLine = 550/time;
        
        function timer(){
            time_line.style.width = widthLine + "px"; //increasing width of time_line with px by time value
            widthLine = widthLine + 550/time;
            if (parseFloat(time_line.style.width) > 549) {
                clearInterval(counterLine);
            }
        }
    }

    function queCounter(index){
        //creating a new span tag and passing the question number and total question
        let totalQueCounTag = '<span><p>'+ index +'</p> of <p>'+ questions.length +'</p> Preguntas</span>';
        bottom_ques_counter.innerHTML = totalQueCounTag;  //adding new span tag inside bottom_ques_counter
    }

    function playVideo(player, source) {
        player.src = source;
        player.load();
        player.play();
    }

    function resizeVideo(container, player, parentContainer) {
        const parentWidth = parentContainer.clientWidth;
        const maxVideoWidth = Math.min(0.65 * parentWidth, 640); // Ocupa solo el 90% del espacio
        const aspectRatio = 16 / 9; // Relación de aspecto típica para videos
        const newWidth = Math.min(maxVideoWidth, parentWidth);
        const newHeight = newWidth / aspectRatio;

        container.style.width = newWidth + "px";
        container.style.height = newHeight + "px";
        container.style.margin = "auto"; // Centrar el contenedor
        player.style.width = "100%";
        player.style.height = "100%";
    }

    function onTimeOut() {
        clearInterval(counterLine); // Limpiar el temporizador de la línea de tiempo
        showResult(); // Mostrar los resultados
    }

    function saveTestResult(userScore, totalQuestions, unidadId, actividadIds) {
        document.getElementById("sideBarHidden").removeAttribute("hidden");
        document.getElementById("sideBarHidden2").removeAttribute("hidden");
        document.getElementById("navBarHidden").removeAttribute("hidden");

        let userId = document.getElementById("userId").value;
        let porcentaje = (userScore / totalQuestions);
        let userNota = 10 * porcentaje;

        let userData = {
            id_usuario: userId,
            id_unidad: unidadId,
            nota: userNota.toFixed(2),
            tipo: "Test",
            actividadIds: actividadIds,
            puntosGanados: 20
        };
        //imprimir todos los valores en este punto
        console.log("userData:", userData);

        fetch('../Modelo/save_nota_test.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(userData),
        })
        .then(response => {
            console.log('Respuesta del servidor:', response);
            // Verificar si la respuesta es exitosa
            if (!response.ok) {
                throw new Error('Error en la solicitud: ' + response.statusText);
            }



            return response.json(); // Convertir la respuesta a JSON
        })
        .then(data => {
            console.log('Nota guardada exitosamente:', data);
            
            var puntosActuales = parseInt($('#puntos-counter').text());
            var nuevosPuntos = puntosActuales + data.puntosGanados;
            $('#puntos-counter').text(nuevosPuntos);

            if(data.nota == 10.00){
                console.log("Manda a llamar a la nueva función de la nota 10");
                save_nota_10(data.nota);
            }

            
            var mensaje = data.message;
            window.location.href = '../Vista/panel.php?modulo=prueba&mensaje=' + encodeURIComponent(mensaje);
        })
        .catch(error => {
            console.error('Error al procesar la respuesta:', error);
            console.log("error 1: " + JSON.stringify(userData) + "\n" + error);
            var mensaje = 'There was an error while saving the test result. Please try again later.';
            window.location.href = '../Vista/panel.php?modulo=prueba&mensaje=' + encodeURIComponent(mensaje);
        });
    }

    function save_nota_10(nota_actividad){
        console.log("Entra en la funcion para guardar la nota de 10");
        let userID = document.getElementById("userId").value;

        let userData = {
            id_usuario: userID,
            nota_actividad: nota_actividad
        };
        //imprimir todos los valores en este punto
        console.log("userData:", userData);

        fetch('../Modelo/nota10.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(userData),
        })
        .then(response => {
            console.log('Respuesta del servidor:', response);
            // Verificar si la respuesta es exitosa
            if (!response.ok) {
                throw new Error('Error en la solicitud: ' + response.statusText);
            }



            return response.json(); // Convertir la respuesta a JSON
        })
        .then(data => {
            console.log('Nota guardada exitosamente:', data);
            
            var puntosActuales = parseInt($('#puntos-counter').text());
            var nuevosPuntos = puntosActuales + data.puntosGanados;
            $('#puntos-counter').text(nuevosPuntos);

            if(data.nota == 10.00){
                console.log("Entra en la funcion para guardar la nota de 10");
                save_nota_10(data.nota);
            }

            
            var mensaje = data.message;
            window.location.href = '../Vista/panel.php?modulo=prueba&mensaje=' + encodeURIComponent(mensaje);
        })
        .catch(error => {
            console.error('Error al procesar la respuesta:', error);
            console.log("error 1: " + JSON.stringify(userData) + "\n" + error);
            var mensaje = 'There was an error while saving the test result. Please try again later.';
            window.location.href = '../Vista/panel.php?modulo=prueba&mensaje=' + encodeURIComponent(mensaje);
        });
    }
}