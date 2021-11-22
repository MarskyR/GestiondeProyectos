<!DOCTYPE html>
<html>
<head>
	 <meta charset="utf-8"/>
     <title>CEDEPAS Norte</title>
	 <link rel="shortcut icon" href="/img/LogoCedepas.png" type="image/png">
  
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta content="width=device-width, initial-scale=1.0" name="viewport"/>      
     
     <link rel="stylesheet" href="/adminlte/plugins/fontawesome-free/css/all.min.css">
     <link rel="stylesheet" href="/adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
     <link rel="stylesheet" href="/adminlte/dist/css/adminlte.min.css">
     <link rel="stylesheet" href="/stylebonis.css">
     <link rel="stylesheet" href="css/login.css">
     <link rel="stylesheet" href="css/style.css">
     <link  rel="stylesheet" href="public/uicons-regular-rounded/css/uicons-regular-rounded.css">


     <script src="/adminlte/plugins/jquery/jquery.min.js"></script>
    <style>
        .loader {
          left: 0px;
          top: 0px;
          width: 100%;
          height: 100%;
          z-index: 9999;
          background: url('/img/PuntosVolando.gif') 50% 50% no-repeat rgb(249,249,249);
          background-size: 10%;
          opacity: .8;
        }
        .login-html {
            padding: 110px 50px 30px 50px !important;
        }

        #linke:hover{
            color: rgb(0, 219, 0);
            font-size: 17pt;
        }

        .gestion{
            color: rgb(49,169,100);
        }
        
        .numero{
            font-size: 14pt;
            color: rgb(49,169,100);
        }
    </style>
</head>
<body onload="iniciarContador()">
    

    <div class="">
        <br>
        <div class="login-wrap">

            <div class="login-html">
                <div class="text-align:center" >
                    <img src="/img/SitioEnConstruccion.png" alt="" width="100%">
                </div>
                <br>
                <div class="card" style="background-color: rgb(25, 73, 46)">
                    <div style="padding: 8px;padding-top:6px;padding-botton:6px">
                        <div style="text-align: center;font-size: large;color: white;line-height : 25px;">
                            Nos hemos mudado a 
                            <a href="http://gestion.cedepas.org" id="linke" class="gestion">
                                gestión.cedepas.org
                            </a>
                            <br>
                            serás redirigido en  
                            <em id="contador" class="numero"
                            >10</em>
                            segundos.
                        </div>
                    </div>
                </div>
               

            </div>

        </div>
        
    </div>
    


</body>


<script>
    console.log("I am the first log");
    
    function iniciarContador() {

        /* para setear el tiempo en segundos a esperar, modificar el id contador */
        var l = document.getElementById("contador");
        //console.log(l.innerHTML)
        var n = parseInt(l.innerHTML);
        window.setInterval(function(){
            l.innerHTML = n;
            n--;
            if(n==-1){
                
                location.href = "http://gestion.cedepas.org/";
                
            }

        },1000);

    }

    
</script>
</html>
