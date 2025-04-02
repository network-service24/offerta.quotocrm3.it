<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Come arrivare</title>
        <style type="text/css">
            body{
                margin:0; 
                padding:0;
                font-family:Verdana;
                font-size:14px;
            }
            .info_detail {
                filter:alpha(opacity=50);
                -moz-opacity:0.70;
                -khtml-opacity: 0.70;
                opacity: 0.70;
                position:absolute; 
                z-index:2000; 
                background-color:#60ce99; 
                border-top:2px solid #ffffff; 
                margin-top:-100px; 
                height:90px;
                color:#ffffff;
                width:100%;
            }           
            .bar_info_directions {
                filter:             alpha(opacity=90);
                -moz-opacity:       0.90;
                -khtml-opacity:     0.90;
                opacity:            0.90;
                position:           absolute; 
                z-index:            2100; 
                background-color:   #879eac;            
                margin-top:         -25px;              
                color:              #ffffff;
                width:              100%;
                padding:            4px 0;
                display:            none;               
            }           
            .bar_info_directions, .bar_info_directions a:link{
                color:          #232323;
                font-weight:    bold;
                font-size:      13px;
                text-align:     center;
            }           
            .nome_recapito{
                font-size:      14px;
                white-space:    nowrap;
            }
            .info_recapito{
                font-size:      0.8em;
                white-space:    nowrap;
            }
            #recapiti{
                padding:        6px; 
                position:       absolute; 
                margin-top:     -90px; 
                color:          #ffffff; 
                z-index:        3000;
            }
            #form_calcola{
                color:              #ffffff; 
                margin-top:         10px; 
                position:           absolute; 
                margin-top:         -160px; 
                width:              290px; 
                text-align:         right; 
                font-size:          12px;
                background-color:   white;
                padding: 10px;
                right:10px;
                border: 1px solid #01AEF0;               
                moz-box-shadow: 0px 0px 12px #4a4a4a;
                -webkit-box-shadow: 0px 0px 12px #4A4A4A;
                box-shadow: 0px 0px 12px #4A4A4A;
            }
            #directions{
                height:380px;
                background-color:#ffffff; 
                overflow-y:scroll;  
                overflow-x:hidden;
                color:#ff6600;
            }
            #directions table{
                width:100%;
            }
            #directions a{
                color:#ff6600;
            }
            #toolbar{
                font-size:14px; 
                position:absolute; 
                text-align:right; 
                width:99%; 
                margin-top:-15px; 
                margin-right:10px; 
                z-index:3000;
            }
            #toolbar a:link{
                /*text-shadow: #000000 1px 1px 3px;*/
            }
            .modulo{
                border:1px solid #BCC1C8;
                font-family: Verdana;
                font-weight: normal; 
                color:#ff6600;
                background-color:#ffffff;
            }
            .bottone{
                 cursor: pointer;
                border:1px solid #BCC1C8;
                font-family: Verdana;
                font-weight: normal; 
                color:#ffffff;
                background-color:#01AEF0;
            }
            a:link { font-weight: normal; font-style: normal; color: #ffffff; text-decoration:underline;}
            a:visited { font-weight: normal; font-style: normal; color: #ffffff; text-decoration:underline;}
            a:hover { font-weight: normal; font-style: normal; color: #ffffff; text-decoration:underline;}

        </style>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
        <!-- CHIAVE SUITEWEB GEOLOCALIZZAZIONE -->
        <!--<script type="text/javascript" src="https://maps.google.com/maps/api/js?libraries=geometry&key=AIzaSyBLHIBSnFd6u94zcsXqXwuHG5bAr5tOhKU"></script>-->
        <!-- CHIAVE GOOGLE MAP QUOTO API JAVASCRIPT E DIRECTION-->
        <script type="text/javascript" src="https://maps.google.com/maps/api/js?libraries=geometry&key=AIzaSyCEhD0s4UEJdItPacNMZNLE_aoyLYGAHL8"></script>
        
        <!--<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAUpReu4YFoa9jhCU-gTA6NUkoH0dgUl54&libraries=geometry" async defer></script>-->
        <script type="text/javascript">
            String.prototype.unescapeHtml = function () {
                var temp = document.createElement("div");
                temp.innerHTML = this;
                var result = temp.childNodes[0].nodeValue;
                temp.removeChild(temp.firstChild);
                return result;
            }
            function alertHtml(msg){
                alert(msg.unescapeHtml());
            }       

            var directionDisplay;
            var directionsService = new google.maps.DirectionsService();
            var map;
            var panorama;
            var myLatlng;    

            function toggleStreetView() {
              var toggle = panorama.getVisible();
              if (toggle == false) {
                panorama.setVisible(true);
              } else {
                panorama.setVisible(false);
              }
            }           

            function view_directions(info_tragitto, to_lat, to_lon, from_lat, from_lon, travelmode) {

                switch(travelmode){
                    case "DRIVING":
                        var travelmode = google.maps.DirectionsTravelMode.DRIVING;
                    break;
                    case "WALKING":
                        var travelmode = google.maps.DirectionsTravelMode.WALKING;
                    break;
                    case "TRANSIT":
                        var travelmode = google.maps.DirectionsTravelMode.TRANSIT;
                    break;
                    case "BICYCLING":
                        var travelmode = google.maps.DirectionsTravelMode.BICYCLING;
                    break;
                    default:
                        var travelmode = google.maps.DirectionsTravelMode.DRIVING;
                }

                var from_point = new google.maps.LatLng(from_lat,from_lon);
                var to_point = new google.maps.LatLng(to_lat,to_lon);
                //if(info_tragitto=='pda')from_point = myLatlng     

                var data = {
                    origin:from_point,
                    destination:to_point,
                    travelMode: travelmode
                };
                directionsService.route(data,

                    function(response, status) {
                        if (status == google.maps.DirectionsStatus.OK) {
                            directionsDisplay.setDirections(response);              
                            document.getElementById("info_directions").style.display = 'block';
                            document.getElementById("info_directions").innerHTML = '<span class="label_tragitto">Distanza: '+response.routes[0].legs[0].distance.text+' - Tempo di viaggio: '+response.routes[0].legs[0].duration.text+'</span>';

                            switch(info_tragitto){
                                case "pda":
                                    //
                                break;
                                case "pdp":
                                    var html_recapiti_partenza = $('recapiti_partenza').innerHTML;
                                    var html_recapiti_arrivo = $('recapiti_arrivo').innerHTML;
                                    $('recapiti_partenza').innerHTML = html_recapiti_arrivo;
                                    $('recapiti_arrivo').innerHTML = html_recapiti_partenza;
                                break;
                            }
                            //alertHtml(map.getZoom());
                            setTimeout("map.setZoom(parseFloat(map.getZoom()) - 1);",500);

                        } else {

                        }

                    });
                }

    function initialize(lat,lon) {
    //var map = new GMap2(document.getElementById("map_canvas"));
    //map.setCenter(new GLatLng(lat, lon), 16);
    myLatlng = new google.maps.LatLng(lat,lon);
    var myLatlng_pano = new google.maps.LatLng(<?=$latitudine?>,<?=$longitudine?>); 
    var myOptions = {
        zoom: 16,
        center: myLatlng,
        panControl: true,
        zoomControl: true,
        mapTypeControl: true,
        scaleControl: true,
        streetViewControl: true,
        overviewMapControl: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        scrollwheel: false
    }

    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    directionsDisplay = new google.maps.DirectionsRenderer();
    directionsDisplay.setMap(map);
    directionsDisplay.setPanel(document.getElementById('directions'));          

    panorama = map.getStreetView();
    panorama.setPosition(myLatlng_pano);
    panorama.setPov({
        heading: 90,
        zoom:1,
        pitch:-14.500256744050561}
      );

    var testo_fumetto = '<span style="font-family:Verdana"><strong><?=$NomeCliente?></strong><br><?=$Indirizzo?> <?=$Localita?> <br /><?=$Cap?> <?=$Provincia?></span>';
    var infowindow = new google.maps.InfoWindow({
            content: testo_fumetto
        });

        var image = '/img/ico.png';
        var marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            title:"<?=$NomeCliente?>",
            //icon: image
        });       
        infowindow.open(map,marker);                

    <? if(isset ($from_lati)) { ?>
        view_directions('pda', <?=$from_lati?>,<?=$from_long?>, <?=$latitudine?>, <?=$longitudine?>, '<?=$travelmode?>');
    <? } ?>                             

}

            function handleErrors(){
                if (gdir.getStatus().code == G_GEO_UNKNOWN_ADDRESS)
                    alertHtml("Indirizzo non trovato");
                else if (gdir.getStatus().code == G_GEO_SERVER_ERROR)
                    alertHtml("Si &egrave; verificato un errore nella geocodifica degli indirizzi");
                else if (gdir.getStatus().code == G_GEO_MISSING_QUERY)
                    alertHtml("Manca un parametro");
                else if (gdir.getStatus().code == G_GEO_BAD_KEY)
                    alertHtml("Errore nella Key Api.");
                else if (gdir.getStatus().code == G_GEO_BAD_REQUEST)
                    alertHtml("La richiesta non puo' essere correttamente risolta.");
                else alertHtml("Si &egrave; verificato un errore");
            }

            function setDirections(fromAddress) {
                Effect.Fade('map_canvas');
                hide_cartina();
                //locale="it";
                //gdir.load("from: " + fromAddress + " to: 44.085894,12.5387009");

                //qui setto le coordinate della partenza (geo position cliente)                
                var start = '<?=$latitudine?>,<?=$longitudine?>';  

                //qui setto coordinate arrivo (geo position arrivo)

                var end = '<?=$from_lati?>,<?=$from_long?>';
                var request = {
                    origin:start, 
                    destination:end,
                    travelMode: google.maps.DirectionsTravelMode.DRIVING
                };              

                directionsService.route(request, function(response, status) {

                    if (status == google.maps.DirectionsStatus.OK) {
                        directionsDisplay.setDirections(response);          
                    }
                });         
                Effect.Appear('directions');
            }           

            function hide_cartina(){

                $('map_canvas').hide();
                $('recapiti').hide();
                $('divopaco').hide();
                $('form_calcola').hide();
                $('toolbar').hide();
                $('info_directions').hide();

            }       

            function show_cartina(){                

                $('map_canvas').show();
                $('recapiti').show();
                $('divopaco').show();
                $('form_calcola').hide();
                $('toolbar').show();
                $('info_directions').show();

            }       

            function get_info_mappa(){              
                var info = '\n\nheading:'+panorama.getPov().heading+'\nzoom :'+panorama.getPov().zoom+'\npitch:'+panorama.getPov().pitch+'\nllatitudine,longitudine:'+panorama.getPosition()+'';
                alertHtml(info);
            }           

        </script>
    </head>   
    <!-- nel body c'è il centro mappa?? -->
    <body style="overflow:hidden" onload="initialize('<?=$latitudine?>','<?=$longitudine?>');" >
    <!--
    <? 
        #############################################################################

        # FUNZIONE CHE CREA STAMPA UN ARRAY CON LE GIUSTE INTERRUZIONI DI RIGA  #

        #############################################################################
        if (!function_exists('printR')) {
            function printR($array) {
                echo '<pre>';
                    print_r($array);
                echo '</pre>';
            }
        }    
    ?>
    -->          
        <div id="directions"  style="display:none">
            <a href="#" onclick="show_cartina();$('directions').hide();initialize('<?=$latitudine?>','<?=$longitudine?>');return false">Torna alla cartina</a> -
            <a href="#" onclick="window.print(); return false" >Stampa il percorso</a>
        </div>  
          
        <div id="map_canvas" style="height:334px; width:100%"></div>
        <div class="bar_info_directions" id="info_directions"></div>
        <div style="clear:both"></div>
        <div id="recapiti" style="display:block">
            <div id="recapiti_partenza" style="float:left;height:30px;overflow:hidden;"></div>
            <div style="clear:both"></div>
        </div>
            <div id="form_calcola"  style="display:none">
                <input id="location" type="text" class="modulo" onchange="setDirections(this.value);" value="Luogo di partenza" onclick="this.value=''"  />&nbsp;
                <input name="" type="button" class="bottone" value="Calcola percorso" onclick="setDirections($('location').value);"/>
            </div>

   </body>
</html>