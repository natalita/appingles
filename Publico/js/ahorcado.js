/* Variables */
var ctx;
var canvas;
var palabra;
var letras = "QWERTYUIOPASDFGHJKLZXCVBNM";
//"QWERTYUIOPASDFGHJKLÑZXCVBNM"
var colorTecla = "#585858";
var colorMargen = "red";
var inicioX = 200;
var inicioY = 300;
var lon = 35;
var margen = 20;
var pistaText = "";

/* Arreglos */
var teclas_array = new Array();
var letras_array = new Array();
var palabras_array = new Array();

/* Variables de control */
var aciertos = 0;
var errores = 0;

/* Palabras */

// Primeras palabras relacionadas a las temáticas
palabras_array.push("invaded");
palabras_array.push("surrendered");
palabras_array.push("launched");
palabras_array.push("capitalized");
palabras_array.push("resilient");
palabras_array.push("dominant");
palabras_array.push("exterminated");
palabras_array.push("swift");
palabras_array.push("strategic");
palabras_array.push("brutal");
palabras_array.push("liberated");
palabras_array.push("collapsed");
palabras_array.push("devastating");
palabras_array.push("unexpected");
palabras_array.push("persistent");
palabras_array.push("decisive");
palabras_array.push("unsuccessful");
palabras_array.push("ambitious");
palabras_array.push("avoid");
palabras_array.push("gain");
palabras_array.push("disappointed");
palabras_array.push("support");
palabras_array.push("consume");
palabras_array.push("fat");
palabras_array.push("order");
palabras_array.push("warn");
palabras_array.push("react");
palabras_array.push("sprain");
palabras_array.push("stick");
palabras_array.push("risk");
palabras_array.push("health");
palabras_array.push("plan");
palabras_array.push("encourage");
palabras_array.push("junk");
palabras_array.push("record");
palabras_array.push("vegetables");
palabras_array.push("rag");
palabras_array.push("calories");
palabras_array.push("stomach");
palabras_array.push("deliver");
palabras_array.push("fortunate");
palabras_array.push("recuperation");
palabras_array.push("assign");
palabras_array.push("silo");
palabras_array.push("heroic");
palabras_array.push("science");
palabras_array.push("executive");
palabras_array.push("toddler");
palabras_array.push("explode");
palabras_array.push("recuperate");
palabras_array.push("scrapper");
palabras_array.push("diploma");
palabras_array.push("path");
palabras_array.push("boy");
palabras_array.push("mail");
palabras_array.push("study");
palabras_array.push("quote");
palabras_array.push("explosion");
palabras_array.push("recovery");
palabras_array.push("celebrate");
palabras_array.push("historic");
palabras_array.push("manned");
palabras_array.push("lunar");
palabras_array.push("landing");
palabras_array.push("mission");
palabras_array.push("orbit");
palabras_array.push("astronaut");
palabras_array.push("achievement");
palabras_array.push("anniversary");
palabras_array.push("significant");
palabras_array.push("commit");
palabras_array.push("goal");
palabras_array.push("decade");
palabras_array.push("technological");
palabras_array.push("exploration");
palabras_array.push("select");
palabras_array.push("referred");
palabras_array.push("claim");
palabras_array.push("lack");
palabras_array.push("appear");
palabras_array.push("initiate");
palabras_array.push("demonstrate");
palabras_array.push("assemble");
palabras_array.push("infiltrating");
palabras_array.push("protect");
palabras_array.push("experiment");
palabras_array.push("enhanced");
palabras_array.push("primary");
palabras_array.push("unworthy");
palabras_array.push("heavy");
palabras_array.push("global");
palabras_array.push("artificial");
palabras_array.push("shapeless");
palabras_array.push("vulnerable");
palabras_array.push("central");
palabras_array.push("retrieve");
palabras_array.push("rescue");
palabras_array.push("apprehend");
palabras_array.push("gather");
palabras_array.push("confront");
palabras_array.push("battle");
palabras_array.push("transform");
palabras_array.push("interrogate");
palabras_array.push("create");
palabras_array.push("destroy");
palabras_array.push("reveal");
palabras_array.push("volunteer");
palabras_array.push("decide");
palabras_array.push("split");
palabras_array.push("hunted");
palabras_array.push("offer");
palabras_array.push("ensure");
palabras_array.push("cooperation");
palabras_array.push("arrange");
palabras_array.push("sequence");
palabras_array.push("pregnant");
palabras_array.push("celebratory");
palabras_array.push("revelation");
palabras_array.push("agile");
palabras_array.push("violent");
palabras_array.push("diplomatic");
palabras_array.push("manipulation");
palabras_array.push("trinket");
palabras_array.push("suicide");
palabras_array.push("vault");
palabras_array.push("narrow");
palabras_array.push("remote");

/* Objetos */
function Tecla(x, y, ancho, alto, letra){
    this.x = x;
    this.y = y;
    this.ancho = ancho;
    this.alto = alto;
    this.letra = letra;
    this.dibuja = dibujaTecla;
}

function Letra(x, y, ancho, alto, letra){
    this.x = x;
    this.y = y;
    this.ancho = ancho;
    this.alto = alto;
    this.letra = letra;
    this.dibuja = dibujaCajaLetra;
    this.dibujaLetra = dibujaLetraLetra;
}

/* Funciones */

/* Dibujar Teclas*/
function dibujaTecla(){
    ctx.fillStyle = colorTecla;
    ctx.strokeStyle = colorMargen;
    ctx.fillRect(this.x, this.y, this.ancho, this.alto);
    ctx.strokeRect(this.x, this.y, this.ancho, this.alto);
    
    ctx.fillStyle = "white";
    ctx.font = "bold 20px courier";
    ctx.fillText(this.letra, this.x+this.ancho/2-5, this.y+this.alto/2+5);
}

/* Dibua la letra y su caja */
function dibujaLetraLetra(){
    var w = this.ancho;
    var h = this.alto;
    ctx.fillStyle = "black";
    ctx.font = "bold 40px Courier";
    ctx.fillText(this.letra.toUpperCase(), this.x+w/2-12, this.y+h/2+14);
}
function dibujaCajaLetra(){
    ctx.fillStyle = "white";
    ctx.strokeStyle = "black";
    ctx.fillRect(this.x, this.y, this.ancho, this.alto);
    ctx.strokeRect(this.x, this.y, this.ancho, this.alto);
}


/// Funcion para dar una pista la usuario ////
function pistaFunction(palabra){
    let pista = ""; // Se crea la variable local pista que contendra nuestra frase de pista
    switch (palabra) {
        case 'invaded':
            pista = "Entered a place by force.";
            break;
        case 'surrendered':
            pista = "Gave up or yielded.";
            break;
        case 'launched':
            pista = "Started or initiated.";
            break;
        case 'capitalized':
            pista = "Took advantage of a situation.";
            break;
        case 'resilient':
            pista = "Able to recover quickly.";
            break;
        case 'dominant':
            pista = "Having power or influence.";
            break;
        case 'exterminated':
            pista = "Destroyed completely.";
            break;
        case 'swift':
            pista = "Happening quickly.";
            break;
        case 'strategic':
            pista = "Planned for long-term goals.";
            break;
        case 'brutal':
            pista = "Extremely harsh or cruel.";
            break;
        case 'liberated':
            pista = "Freed from control.";
            break;
        case 'collapsed':
            pista = "Fell apart or failed.";
            break;
        case 'devastating':
            pista = "Causing great destruction.";
            break;
        case 'unexpected':
            pista = "Not anticipated.";
            break;
        case 'persistent':
            pista = "Continuing firmly or obstinately.";
            break;
        case 'decisive':
            pista = "Determining the outcome.";
            break;
        case 'unsuccessful':
            pista = "Not achieving the goal.";
            break;
        case 'ambitious':
            pista = "Having a strong desire to succeed.";
            break;
        case 'avoid':
            pista = "Stay away from something.";
            break;
        case 'gain':
            pista = "Increase in size or amount.";
            break;
        case 'disappointed':
            pista = "Feeling let down.";
            break;
        case 'support':
            pista = "Provide assistance or approval.";
            break;
        case 'consume':
            pista = "Eat or drink.";
            break;
        case 'fat':
            pista = "Having excess body weight.";
            break;
        case 'order':
            pista = "Arrange in sequence.";
            break;
        case 'warn':
            pista = "Advise of danger or risk.";
            break;
        case 'react':
            pista = "Respond to something.";
            break;
        case 'sprain':
            pista = "Injury to a ligament.";
            break;
        case 'stick':
            pista = "A thin, straight piece of wood.";
            break;
        case 'risk':
            pista = "Exposure to danger.";
            break;
        case 'health':
            pista = "State of physical well-being.";
            break;
        case 'plan':
            pista = "A detailed proposal for achieving something.";
            break;
        case 'encourage':
            pista = "Give support or confidence.";
            break;
        case 'junk':
            pista = "Something worthless or unhealthy.";
            break;
        case 'record':
            pista = "Highest or best achievement.";
            break;
        case 'vegetables':
            pista = "Edible plant parts.";
            break;
        case 'rag':
            pista = "A piece of old cloth.";
            break;
        case 'calories':
            pista = "Units of energy in food.";
            break;
        case 'stomach':
            pista = "Part of the body where food is digested.";
            break;
        case 'deliver':
            pista = "To transport to someone.";
            break;
        case 'fortunate':
            pista = "Lucky or well-off.";
            break;
        case 'recuperation':
            pista = "Recovery after an illness or injury.";
            break;
        case 'assign':
            pista = "To give a task or duty.";
            break;
        case 'silo':
            pista = "A tall storage tower for grain.";
            break;
        case 'heroic':
            pista = "Brave or noble.";
            break;
        case 'science':
            pista = "Study of the natural world.";
            break;
        case 'executive':
            pista = "A high-ranking official.";
            break;
        case 'toddler':
            pista = "A young child learning to walk.";
            break;
        case 'explode':
            pista = "To burst violently.";
            break;
        case 'recuperate':
            pista = "To heal or recover.";
            break;
        case 'scrapper':
            pista = "A determined fighter.";
            break;
        case 'diploma':
            pista = "Certificate of graduation.";
            break;
        case 'path':
            pista = "A track or route.";
            break;
        case 'boy':
            pista = "A young male child.";
            break;
        case 'mail':
            pista = "System for sending letters.";
            break;
        case 'study':
            pista = "To learn or gain knowledge.";
            break;
        case 'quote':
            pista = "A repeated statement.";
            break;
        case 'explosion':
            pista = "A violent burst of energy.";
            break;
        case 'recovery':
            pista = "Process of returning to normal.";
            break;
        case 'celebrate':
            pista = "To honor or commemorate an event or person.";
            break;
        case 'historic':
            pista = "Famous or important in history.";
            break;
        case 'manned':
            pista = "Carried out by or involving a human presence.";
            break;
        case 'lunar':
            pista = "Relating to the moon.";
            break;
        case 'landing':
            pista = "The act of arriving on a surface, especially from flight.";
            break;
        case 'mission':
            pista = "An important assignment or task, often involving travel.";
            break;
        case 'orbit':
            pista = "The curved path of a celestial object around a star, planet, or moon.";
            break;
        case 'astronaut':
            pista = "A person trained for spaceflight.";
            break;
        case 'achievement':
            pista = "A thing done successfully with effort or skill.";
            break;
        case 'anniversary':
            pista = "The annual recurrence of a date marking a notable event.";
            break;
        case 'significant':
            pista = "Having great meaning or importance.";
            break;
        case 'commit':
            pista = "To dedicate oneself to a course of action or policy.";
            break;
        case 'goal':
            pista = "An objective or desired result.";
            break;
        case 'decade':
            pista = "A period of ten years.";
            break;
        case 'technological':
            pista = "Relating to technology or advancements in applied science.";
            break;
        case 'exploration':
            pista = "The action of traveling through or studying an unfamiliar area.";
            break;
        case 'select':
            pista = "To choose something or someone.";
            break;
        case 'referred':
            pista = "To mention or allude to.";
            break;
        case 'claim':
            pista = "To assert something as a fact.";
            break;
        case 'lack':
            pista = "To be without something.";
            break;
        case 'appear':
            pista = "To come into view or become visible.";
            break;
        case 'initiate':
            pista = "To start or begin something.";
            break;
        case 'demonstrate':
            pista = "To show or explain something clearly.";
            break;
        case 'assemble':
            pista = "To gather or bring together.";
            break;
        case 'infiltrating':
            pista = "To secretly enter a place or group.";
            break;
        case 'protect':
            pista = "To keep safe from harm or damage.";
            break;
        case 'experiment':
            pista = "To test or try something new.";
            break;
        case 'enhanced':
            pista = "Improved or intensified.";
            break;
        case 'primary':
            pista = "First or most important.";
            break;
        case 'unworthy':
            pista = "Not deserving respect or attention.";
            break;
        case 'heavy':
            pista = "Having great weight; difficult to lift.";
            break;
        case 'global':
            pista = "Relating to the whole world.";
            break;
        case 'artificial':
            pista = "Made by humans, not natural.";
            break;
        case 'shapeless':
            pista = "Lacking a clear or definite form.";
            break;
        case 'vulnerable':
            pista = "Open to harm or damage.";
            break;
        case 'central':
            pista = "In the middle or of key importance.";
            break;
        case 'retrieve':
            pista = "To get or bring something back.";
            break;
        case 'rescue':
            pista = "To save someone from danger.";
            break;
        case 'apprehend':
            pista = "To arrest or seize someone.";
            break;
        case 'gather':
            pista = "To collect or bring together.";
            break;
        case 'confront':
            pista = "To face or oppose boldly.";
            break;
        case 'battle':
            pista = "To fight against an opponent.";
            break;
        case 'transform':
            pista = "To change in form or appearance.";
            break;
        case 'interrogate':
            pista = "To question someone thoroughly.";
            break;
        case 'create':
            pista = "To bring something into existence.";
            break;
        case 'destroy':
            pista = "To damage something completely.";
            break;
        case 'reveal':
            pista = "To make something known or visible.";
            break;
        case 'volunteer':
            pista = "To offer to do something willingly.";
            break;
        case 'decide':
            pista = "To make a choice or come to a conclusion.";
            break;
        case 'split':
            pista = "To divide or break into parts.";
            break;
        case 'hunted':
            pista = "Being pursued or chased, often with intent to capture.";
            break;
        case 'offer':
            pista = "To present or propose something for acceptance.";
            break;
        case 'ensure':
            pista = "To make certain or guarantee.";
            break;
        case 'cooperation':
            pista = "The process of working together toward a common goal.";
            break;
        case 'arrange':
            pista = "To organize or put in order.";
            break;
        case 'sequence':
            pista = "A specific order in which related events follow each other.";
            break;
        case 'pregnant':
            pista = "Carrying a developing embryo or fetus within the body.";
            break;
        case 'celebratory':
            pista = "Expressing celebration or joy.";
            break;
        case 'revelation':
            pista = "A surprising or previously unknown fact made known.";
            break;
        case 'agile':
            pista = "Able to move quickly and easily.";
            break;
        case 'violent':
            pista = "Involving physical force intended to harm or damage.";
            break;
        case 'diplomatic':
            pista = "Involving negotiation or tact to manage relationships.";
            break;
        case 'manipulation':
            pista = "Controlling or influencing something in an unscrupulous way.";
            break;
        case 'trinket':
            pista = "A small, decorative item of little value.";
            break;
        case 'suicide':
            pista = "The act of intentionally ending one's own life";
            break;
        case 'vault':
            pista = "A secure room or chamber for storing valuable items.";
            break;
        case 'narrow':
            pista = "Limited in width or scope.";
            break;
        case 'remote':
            pista = "Far away or distant; also used for electronic devices.";
            break;

        default: // El default se puede omitir
            $pista = "Ther's no hint for this word";
    }
    // Pintamos la palabra en el canvas , en este ejemplo se pinta arriba a la izquierda //
    ctx.fillStyle = "black";  // Aqui ponemos el color de la letra
    ctx.font = "bold 20px Courier";  // aqui ponemos el tipo y tamaño de la letra
    ctx.fillText(pista, 10, 15);  // aqui ponemos la frase en nuestro caso la variable pista , seguido de la posx y posy
}

        
    /* Distribuir nuestro teclado con sus letras respectivas al acomodo de nuestro array */
function teclado(){
    var ren = 0;
    var col = 0;
    var letra = "";
    var miLetra;
    var x = inicioX;
    var y = inicioY;
    for(var i = 0; i < letras.length; i++){
        letra = letras.substr(i,1);
        miLetra = new Tecla(x, y, lon, lon, letra);
        miLetra.dibuja();
        teclas_array.push(miLetra);
        x += lon + margen;
        col++;
        if(col==10){
            col = 0;
            ren++;
            if(ren==2){
                x = 280;
            } else {
                x = inicioX;
            }
        }
        y = inicioY + ren * 50;
    }
}


/* aqui obtenemos nuestra palabra aleatoriamente y la dividimos en letras */
function pintaPalabra(){
    var p = Math.floor(Math.random()*palabras_array.length);
    palabra = palabras_array[p];
    console.log(palabra);

    pistaFunction(palabra);

    var w = canvas.width;
    var len = palabra.length;
    var ren = 0;
    var col = 0;
    var y = 230;
    var lon = 50;
    var x = (w - (lon+margen) *len)/2;
    for(var i=0; i<palabra.length; i++){
        letra = palabra.substr(i,1);
        miLetra = new Letra(x, y, lon, lon, letra);
        miLetra.dibuja();
        letras_array.push(miLetra);
        x += lon + margen;
    }
}

/* dibujar cadalzo y partes del pj segun sea el caso */
function horca(errores){
    var imagen = new Image();
    imagen.src = "../Publico/img/ahorcado/ahorcado"+errores+".png";
    imagen.onload = function(){
        ctx.drawImage(imagen, 390, 0, 230, 230);
    }
    /*************************************************
    // Imagen 2 mas pequeña a un lado de la horca //       
    var imagen = new Image();
    imagen.src = "imagenes/ahorcado"+errores+".png";
    imagen.onload = function(){
        ctx.drawImage(imagen, 620, 0, 100, 100);
    }
    *************************************************/
}

/* ajustar coordenadas */
function ajusta(xx, yy){
    var posCanvas = canvas.getBoundingClientRect();
    var x = xx-posCanvas.left;
    var y = yy-posCanvas.top;
    return{x:x, y:y}
}

/* Detecta tecla clickeada y la compara con las de la palabra ya elegida al azar */
function selecciona(e){
    var pos = ajusta(e.clientX, e.clientY);
    console.log("Coordenadas del clic del ratón: ", e.clientX, e.clientY);
    console.log("Posición ajustada: ", pos.x, pos.y);
    var x = pos.x;
    var y = pos.y;
    var tecla;
    var bandera = false;
    
    for (var i = 0; i < teclas_array.length; i++){
        tecla = teclas_array[i];
        if (tecla.x > 0){
            if ((x > tecla.x) && (x < tecla.x + tecla.ancho) && (y > tecla.y) && (y < tecla.y + tecla.alto)){
                console.log("Tecla encontrada: ", tecla);
                break;
            }
        }
    }
    if (i < teclas_array.length){
        console.log("Tecla seleccionada: ", tecla.letra);
        for (var i = 0 ; i < palabra.length ; i++){ 
            letra = palabra.substr(i, 1);
            console.log("Letra de la palabra: ", letra);
            if (letra == tecla.letra.toLowerCase()){ /* comparamos y vemos si acerto la letra */
                
                caja = letras_array[i];
                caja.dibujaLetra();
                aciertos++;
                bandera = true;
            }
        }
        if (bandera == false){ /* Si falla aumenta los errores y checa si perdio para mandar a la funcion gameover */
            errores++;
            console.log("Error al seleccionar la letra.");
            horca(errores);
            if (errores==3) {
                document.getElementById("hangButn2").hidden=false;
            }
            if (errores == 5) gameOver(errores);
        }
        /* Borra la tecla que se a presionado */
        ctx.clearRect(tecla.x - 1, tecla.y - 1, tecla.ancho + 2, tecla.alto + 2);
        tecla.x - 1;
        /* checa si se gano y manda a la funcion gameover */
        if (aciertos == palabra.length) gameOver(errores);
    }
}

/* Borramos las teclas y la palabra con sus cajas y mandamos msj segun el caso si se gano o se perdio */
function gameOver(errores){
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = "black";

    ctx.font = "bold 50px Courier";
    if (errores < 5){
        ctx.fillText("Very well, the word is: ", 110, 280);
    } else {
        ctx.fillText("Sorry, the word was: ", 110, 280);
    }
    
    ctx.font = "bold 80px Courier";
    lon = (canvas.width - (palabra.toUpperCase().length*48))/2;
    ctx.fillText(palabra.toUpperCase(), lon, 400);
    horca(errores);
}

/* Detectar si se a cargado nuestro contexco en el canvas, iniciamos las funciones necesarias para jugar o se le manda msj de error segun sea el caso */
window.onload = function(){
    canvas = document.getElementById("pantalla");
    if (canvas && canvas.getContext){
        ctx = canvas.getContext("2d");
        if(ctx){
            teclado();
            pintaPalabra();
            horca(errores);
            canvas.addEventListener("click", selecciona, false);
        } else {
            alert ("Error loading context!");
        }
    }
}

function reproducirAudio() {
    console.log("Esta llegando a la función de reproducir audio");
    
    // Ruta del archivo de audio
    var rutaAudio = "../Publico/audios/" + palabra + ".mp3";
    console.log("Ruta del archivo de audio:", rutaAudio);
    
    // Verificar la disponibilidad del archivo de audio
    fetch(rutaAudio)
        .then(response => {
            if (response.ok) {
                var audio = new Audio(rutaAudio);
                audio.play();
            } else {
                alert("The audio file is not available.");
            }
        })
        .catch(error => {
            console.error("Error al verificar la disponibilidad del archivo de audio:", error);
        });
}