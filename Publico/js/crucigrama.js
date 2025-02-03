url = window.location.href;
if (url.includes('modulo=crucigrama')) {
    document.addEventListener("DOMContentLoaded",function(){
        // Deshabilitar todos los campos

        for(fila=1; fila<=8; fila++){
            for(columna=1;columna<=8;columna++){
            document.getElementById("fila"+fila+"C"+columna).readOnly=true;
            }
        }

        //Palabra Thor empieza en fila3C3 y termina en fila3C6
        var palabra1_letra1 = document.getElementById("fila3C3");
        var palabra1_letra2 = document.getElementById("fila3C4");
        var palabra1_letra3 = document.getElementById("fila3C5");
        var palabra1_letra4 = document.getElementById("fila3C6");

        //Palabra FURIOUS empieza en fila1C6 y termina en fila7C6
        var palabra2_letra1 = document.getElementById("fila1C6");
        var palabra2_letra2 = document.getElementById("fila2C6");
        var palabra2_letra3 = document.getElementById("fila3C6");
        var palabra2_letra4 = document.getElementById("fila4C6");
        var palabra2_letra5 = document.getElementById("fila5C6");
        var palabra2_letra6 = document.getElementById("fila6C6");
        var palabra2_letra7 = document.getElementById("fila7C6");

        //Palabra simpsons empieza en fila5C1 y termina en fila5C8
        var palabra3_letra1 = document.getElementById("fila5C1");
        var palabra3_letra2 = document.getElementById("fila5C2");
        var palabra3_letra3 = document.getElementById("fila5C3");
        var palabra3_letra4 = document.getElementById("fila5C4");
        var palabra3_letra5 = document.getElementById("fila5C5");
        var palabra3_letra6 = document.getElementById("fila5C6");
        var palabra3_letra7 = document.getElementById("fila5C7");
        var palabra3_letra8 = document.getElementById("fila5C8");

        //Palabra moon empieza en fila5C3 y termina en fila8C3
        var palabra4_letra1 = document.getElementById("fila5C3");
        var palabra4_letra2 = document.getElementById("fila6C3");
        var palabra4_letra3 = document.getElementById("fila7C3");
        var palabra4_letra4 = document.getElementById("fila8C3");
        

        //Habilitar las casillas necesarias (horizontales)
        palabra1_letra1.readOnly =false;
        palabra1_letra2.readOnly =false;
        palabra1_letra3.readOnly =false;
        palabra1_letra4.readOnly =false;

        palabra2_letra1.readOnly =false;
        palabra2_letra2.readOnly =false;
        palabra2_letra3.readOnly =false;
        palabra2_letra4.readOnly =false;
        palabra2_letra5.readOnly =false;
        palabra2_letra6.readOnly =false;
        palabra2_letra7.readOnly =false;

        //Habilitar las casillas necesarias (verticales)
        palabra3_letra1.readOnly =false;
        palabra3_letra2.readOnly =false;
        palabra3_letra3.readOnly =false;
        palabra3_letra4.readOnly =false;
        palabra3_letra5.readOnly =false;
        palabra3_letra6.readOnly =false;
        palabra3_letra7.readOnly =false;
        palabra3_letra8.readOnly =false;

        palabra4_letra1.readOnly =false;
        palabra4_letra2.readOnly =false;
        palabra4_letra3.readOnly =false;
        palabra4_letra4.readOnly =false;

        for(fila=1; fila<=8; fila++){
            for(columna=1;columna<=8;columna++){
                if(document.getElementById("fila"+ fila +"C" + columna).readOnly==false){
                    document.getElementById("fila"+ fila +"C" + columna).style.backgroundColor="#07c7e6";
                }
            }
        }
    })

    function verificar(){
        //Palabra Thor empieza en fila3C3 y termina en fila3C6
        var palabra1_letra1 = document.getElementById("fila3C3");
        var palabra1_letra2 = document.getElementById("fila3C4");
        var palabra1_letra3 = document.getElementById("fila3C5");
        var palabra1_letra4 = document.getElementById("fila3C6");

        //Palabra FURIOUS empieza en fila1C6 y termina en fila7C6
        var palabra2_letra1 = document.getElementById("fila1C6");
        var palabra2_letra2 = document.getElementById("fila2C6");
        var palabra2_letra3 = document.getElementById("fila3C6");
        var palabra2_letra4 = document.getElementById("fila4C6");
        var palabra2_letra5 = document.getElementById("fila5C6");
        var palabra2_letra6 = document.getElementById("fila6C6");
        var palabra2_letra7 = document.getElementById("fila7C6");

        //Palabra simpsons empieza en fila5C1 y termina en fila5C8
        var palabra3_letra1 = document.getElementById("fila5C1");
        var palabra3_letra2 = document.getElementById("fila5C2");
        var palabra3_letra3 = document.getElementById("fila5C3");
        var palabra3_letra4 = document.getElementById("fila5C4");
        var palabra3_letra5 = document.getElementById("fila5C5");
        var palabra3_letra6 = document.getElementById("fila5C6");
        var palabra3_letra7 = document.getElementById("fila5C7");
        var palabra3_letra8 = document.getElementById("fila5C8");

        //Palabra moon empieza en fila5C3 y termina en fila8C3
        var palabra4_letra1 = document.getElementById("fila5C3");
        var palabra4_letra2 = document.getElementById("fila6C3");
        var palabra4_letra3 = document.getElementById("fila7C3");
        var palabra4_letra4 = document.getElementById("fila8C3");

        document.getElementById("mensaje").innerHTML="";
        //var palabra1 = document.ge
        palabra1 = palabra1_letra1.value + palabra1_letra2.value + palabra1_letra3.value + palabra1_letra4.value; 
        palabra2 = palabra2_letra1.value + palabra2_letra2.value + palabra2_letra3.value + palabra2_letra4.value + palabra2_letra5.value + palabra2_letra6.value + palabra2_letra7.value; 
        palabra3 = palabra3_letra1.value + palabra3_letra2.value + palabra3_letra3.value + palabra3_letra4.value + palabra3_letra5.value + palabra3_letra6.value + palabra3_letra7.value + palabra3_letra8.value; 
        palabra4 = palabra4_letra1.value + palabra4_letra2.value + palabra4_letra3.value + palabra4_letra4.value;
        
        if(palabra1.toLowerCase()=="thor" && palabra2.toLowerCase()=="furious" && palabra3.toLowerCase()=="simpsons" && palabra4.toLowerCase()=="moon"){
        document.getElementById("mensaje").innerHTML="You won, Congratulations!";
        document.getElementById("mensaje").style.fontSize="24px";
        document.getElementById("mensaje").className="mx-auto mt-3  alert alert-success";
        }else{
        if(palabra1.toLowerCase()!="thor"){
            palabra1_letra1.value="";
            palabra1_letra2.value="";
            palabra1_letra3.value="";
            palabra1_letra4.value="";
            error();
        }

        if(palabra2.toLowerCase()!="furious"){
            palabra2_letra1.value="";
            palabra2_letra2.value="";
            palabra2_letra3.value="";
            palabra2_letra4.value="";
            palabra2_letra5.value="";
            palabra2_letra6.value="";
            error();
        }

        if(palabra3.toLowerCase()!="simpsons"){
            palabra3_letra1.value="";
            palabra3_letra2.value="";
            palabra3_letra3.value="";
            palabra3_letra4.value="";
            error();
        }

        if(palabra4.toLowerCase()!="moon"){
            palabra4_letra1.value="";
            palabra4_letra2.value="";
            palabra4_letra3.value="";
            error();
        }

        //corrector de palabras
        if(palabra1.toLowerCase()=="thor"){
            palabra1_letra4.value="e";
        }

        if(palabra2.toLowerCase()=="furious"){
            palabra2_letra4.value="r";
        }

        if(palabra3.toLowerCase()=="simpsons"){
            palabra3_letra2.value="e";
            palabra3_letra4.value="r";
        }

        if(palabra2.toLowerCase()=="moon"){
            palabra4_letra3.value="t";
        }

        }
    }

    var errorActivo=0;
    function error(){
        document.getElementById("mensaje").innerHTML="There are errors in the words, try again!";
        document.getElementById("mensaje").className="mx-auto mt-3 alert alert-danger";
        errorActivo=1;
    }

    //esta funcion es para ejecutarse cada 5 segundos
    setInterval('ocultarError()',5000);

    function ocultarError(){
        if(errorActivo==1){
        document.getElementById("mensaje").innerHTML="";
        document.getElementById("mensaje").className="";
        errorActivo=0;
        }
    }

    // reproducir audio
    function reproducirAudioCrucigrama(aud) {
        console.log("entrando a la funcion reproducirAudioCrucigrama");
        if(aud==1){
            var audio= new Audio("../Publico/audios/thor.mp3");
            audio.play();
        }
        if(aud==2){
            var audio= new Audio("../Publico/audios/furious.mp3");
            audio.play();
        }
        if(aud==3){
            var audio= new Audio("../Publico/audios/simpsons.mp3");
            audio.play();
        }
        if(aud==4){
            var audio= new Audio("../Publico/audios/moon.mp3");
            audio.play();
        }
    }
}