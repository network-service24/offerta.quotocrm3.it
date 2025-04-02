jQuery(document).ready(function () {
    //IMAGEFILL
    jQuery('.fillimg').imagefill();
    //TOOLTIP
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    jQuery('[data-bs-toggle="tooltip"]').attr("data-bs-animation", true);
    //
    //SCROLL
    jQuery(document).ready(function () {
        function verifyscroll() {
            if (jQuery(window).scrollTop() > 10) {
                jQuery('body').addClass('scrolled');
            } else {
                jQuery('body').removeClass('scrolled');
            }
            if (jQuery(window).scrollTop() > 150) {
                jQuery('body').addClass('scrolled2');
            } else {
                jQuery('body').removeClass('scrolled2');
            }
        }
        verifyscroll();
        jQuery(document).scroll(function () {
            verifyscroll();
        });
    });
    function number_format(number, decimals, dec_point, thousands_point) {

        if (number == null || !isFinite(number)) {
            throw new TypeError("number is not valid");
        }
    
        if (!decimals) {
            var len = number.toString().split('.').length;
            decimals = len > 1 ? len : 0;
        }
    
        if (!dec_point) {
            dec_point = '.';
        }
    
        if (!thousands_point) {
            thousands_point = ',';
        }
    
        number = parseFloat(number).toFixed(decimals);
    
        number = number.replace(".", dec_point);
    
        var splitNum = number.split(dec_point);
        splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
        number = splitNum.join(dec_point);
    
        return number;
    }

    function fproposta(proposta,titolo,titolo2,prezzo,img,testo) {
        jQuery("body").attr("proposta", proposta);
        jQuery(".proposte-laterali .box").removeClass("open");
        jQuery(".proposte .box").removeClass("open");
        jQuery(".box[proposta='" + proposta + "']").addClass("open");
        jQuery(".boxproposta .sunto .titolo, .boxproposta .content .titolo").html(titolo);
        jQuery(".boxproposta .sunto .titolo2, .boxproposta .content .sottotitolo").html(titolo2);
        jQuery(".boxproposta .sunto .prezzo").html("€ " + prezzo);
        jQuery(".boxproposta .sunto .titolo2, .boxproposta .content .testo").html(testo);
        // compilo il campo hidden per il riepilogo della proposta
        jQuery(".mirrorbox .titolo").html(titolo);

        var totale_camere = jQuery(".box[proposta='" + proposta + "']").attr('totale_camere');
        jQuery(".totale_camere").html("€ " + totale_camere);
        var totale_servizi = jQuery(".proposta_container[proposta=" + proposta + "]").attr('totaleServizi');
        var format_totale_servizi = number_format(totale_servizi,2,',','.');
        jQuery(".totale_servizi").html("€ " + format_totale_servizi);

        var sconto = jQuery(".box[proposta='" + proposta + "']").attr('sconto');
        jQuery(".sconto").html(sconto);
        var valore_sconto = jQuery(".box[proposta='" + proposta + "']").attr('valore_sconto');
        jQuery(".valore_sconto").html("€ - " + valore_sconto);
        if (!sconto) {
           $(".SC").hide();
        } else {
            $(".SC").show();
        }
        jQuery(".totale").html("€ " + prezzo);
        var caparra = jQuery(".box[proposta='" + proposta + "']").attr('caparra');
        jQuery(".percentuale_caparra").html(caparra);
        var valore_caparra = jQuery(".box[proposta='" + proposta + "']").attr('valore_caparra');
        jQuery(".valore_caparra").html(valore_caparra);
        if (!valore_caparra) {
            $(".CP").hide();
         } else {
             $(".CP").show();
        }

        jQuery("#totale_servizi").val(totale_servizi);
        jQuery("#totale").val(prezzo);
        jQuery("#valore_caparra").val(valore_caparra);
        jQuery("#percentuale_caparra").val(caparra);

        var idprop          = jQuery(".proposta_container[proposta=" + proposta + "]").attr('idprop');
        var n               = proposta;
        var propostaContent = jQuery(".proposta_container[proposta=" + proposta + "]").attr('riepilogo');
        jQuery('.formproposta .riepilogoProposta').html('<input type="hidden" name="proposta[' + idprop + ']" id="proposta' + n + '" value="' + propostaContent + '" />');
        var propostaConf = jQuery(".proposta_container[proposta=" + proposta + "]").attr('confermaProposta');
        jQuery(".mirrorbox .confermaProposta").html(propostaConf);
        //    
        jQuery(".proposta_container").hide();     
        jQuery(".servizi_aggiuntivi_compresi").hide();
        jQuery(".servizi_aggiuntivi_facoltativi").hide();
        //
        jQuery(".proposta_container[proposta=" + proposta + "]").show();       
        jQuery(".servizi_aggiuntivi_compresi[proposta="+proposta+"]").show();
        jQuery(".servizi_aggiuntivi_facoltativi[proposta=" + proposta + "]").show();

    }

    //PROPOSTE BOX
    jQuery(".proposte-laterali .box, .proposte .box").click(function () {
        var proposta = jQuery(this).attr("proposta");
        var titolo = jQuery(this).attr("titolo");
        var titolo2 = jQuery(this).attr("titolo2");
        var prezzo = jQuery(this).attr("prezzo");
        var img = jQuery(this).attr("immagine");
        var testo = jQuery(this).attr("testo");
        fproposta(proposta,titolo,titolo2,prezzo,img,testo);
    });
    //inizializzo
    var proposta = jQuery( ".proposte .box" ).first().attr("proposta");
    var titolo = jQuery( ".proposte .box" ).first().attr("titolo");
    var titolo2 = jQuery( ".proposte .box" ).first().attr("titolo2");
    var prezzo = jQuery( ".proposte .box" ).first().attr("prezzo");
    var img = jQuery( ".proposte .box" ).first().attr("immagine");
    var testo = jQuery( ".proposte .box" ).first().attr("testo");
    fproposta(proposta,titolo,titolo2,prezzo,img,testo);
    //SERVIZI
/*     jQuery(".servizio").click(function () {
        jQuery(this).toggleClass("open");
    }) */

    $(window).on("scroll", function() {
        if ($(this).scrollTop() - 200 > 0) {
            $('#to-top').stop().slideDown('fast');
        } else {
            $('#to-top').stop().slideUp('fast');
        }
    });
    $("#to-top").on("click", function () {
        $("html, body").animate({
            scrollTop: 0
        }, 200);
    });
    $(".row-eq-height").each(function() {
        var heights = $(this).find(".col-eq-height").map(function() {
        return $(this).outerHeight();
            }).get(), maxHeight = Math.max.apply(null, heights);
            $(this).find(".col-eq-height").outerHeight(maxHeight);
    });

});

