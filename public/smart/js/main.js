// Inizalizzazione DatePicker //
$(function() {
  $.datepicker.setDefaults({
    showOn: 'both',
    changeMonth: true,
    changeYear: true,
    dateFormat: 'dd-mm-yy',
    minDate: "+1d",
    numberOfMonths: 1
  });

  var cal_inizio = "input[name*='data_arrivo']";
  var cal_fine = "input[name*='data_partenza']";
  var dates = $(cal_inizio + "," + cal_fine).datepicker({
    defaultDate: "+7",
    onSelect: function(selectedDate) {
      if (this.id.indexOf('data_arrivo') > -1) {
        var date = $(cal_inizio).datepicker('getDate', '+1d');
        date.setDate(date.getDate() + 1);
        $(cal_fine).datepicker('setDate', date);
        $(cal_fine).datepicker("option", "minDate", date);
      }
    }
  });

  // FORM 2
  var cal_inizio2 = "arrivo";
  var cal_fine2 = "partenza";
  var dates2 = $("#" + cal_inizio2 + ", #" + cal_fine2).datepicker({
    defaultDate: "+7",
    onSelect: function(selectedDate) {
      if (this.id == cal_inizio2) {
        var date2 = $('#' + cal_inizio2).datepicker('getDate', '+1d');
        date2.setDate(date2.getDate() + 1);
        $('#' + cal_fine2).datepicker('setDate', date2);
        $('#' + cal_fine2).datepicker("option", "minDate", date2);
      }
      bolcalendar();
    }
  });
});

// Countdown offerte //
var sr_second = 1000;
var sr_minute = sr_second * 60;
var sr_hour = sr_minute * 60;
var sr_day = sr_hour * 24;

function showRemaining(end, target, rand_id_timer) {
  var now = new Date();
  var distance = end - now;

  var days = Math.floor(distance / sr_day);
  var hours = Math.floor((distance % sr_day) / sr_hour);
  var minutes = Math.floor((distance % sr_hour) / sr_minute);
  var seconds = Math.floor((distance % sr_minute) / sr_second);
  var milliseconds = distance % sr_second;

  var str = "";
  if (days <= 0 && hours <= 0 && minutes <= 0 && seconds <= 0) {
    //TEMPO SCADUTO
    str = l30;
  } else {
    // GIORNI
    if (days > 1) {
      str += days + l37;
    } else if (days > 0) {
      str += days + l33;
    }

    // ORE
    if (days > 0 || hours >= 0) {
      if (hours >= 10) {
        str += hours + l38;
      } else if (hours > 0 && hours != 1) {
        str += "0" + hours + l38;
      } else if (hours == 1) {
        str += "0" + hours + l34;
      } else {
        str += "0" + l38;
      }
    }

    // MINUTI
    if (hours > 0 || minutes >= 0) {
      if (minutes >= 10) {
        str += minutes + l39;
      } else if (minutes > 0 && minutes != 1) {
        str += "0" + minutes + $l39;
      } else if (minutes == 1) {
        str += "0" + minutes + $l35;
      } else {
        str += "0" + l39;
      }
    }

    // SECONDI
    if (minutes > 0 || seconds >= 0) {
      if (seconds >= 10) {
        str += seconds + $l40;
      } else if (seconds > 0 && seconds != 1) {
        str += "0" + seconds + $l40;
      } else if (seconds == 1) {
        str += "0" + seconds + $l36;
      } else {
        str += "0" + l40;
      }
    }
  }

  $("#" + target).html(str);

  if (days <= 0 && hours <= 0 && minutes <= 0 && seconds <= 0) {
    clearInterval("aggio_" + rand_id_timer);
  }
}
//--------------------------------------------------//
//--------------------------------------------------//
$(document).ready(function(){
    // delegate calls to data-toggle="lightbox"
    $(document).delegate('*[data-gallery="multiimages"]', 'click', function(event) {
        event.preventDefault();
        return $(this).ekkoLightbox({
            always_show_close: true
        });
    });

$(".lightbox").lightbox();
    
});
//--------------------------------------------------//
//--------------------------------------------------//
// Funzione scroll //
function scroll_to(id, scarto, tempo) {
  if (scarto === null) {
    scarto = 0;
  }
  if (tempo === null) {
    tempo = 300;
  }
  $('html,body').animate({
    scrollTop: $('#' + id).offset().top - scarto
  }, {
    queue: false,
    duration: tempo
  });
}
//--------------------------------------------------//
//--------------------------------------------------//
// Movimenti interni //
$(function() {
  $('<div id="zero"></div>').prependTo('body');
  $('<div id="gozero"></div>').prependTo('body');
  $("#gostart").click(function() {
    scroll_to('start', 90, 600);
  });
  $("#gozero").click(function() {
    scroll_to('zero', 90, 600);
  });
});
$(document).scroll(function() {
  if ($(window).width() < 1000) {
    if ($(window).scrollTop() > 1000) {
      $('#gozero').css({
        'bottom': '-5px'
      });
    } else {
      $('#gozero').css({
        'bottom': '-100px'
      });
    }
  } else {
    if ($(window).scrollTop() > 1000) {
      $('#gozero').css({
        'bottom': '15px'
      });
    } else {
      $('#gozero').css({
        'bottom': '-100px'
      });
    }
  }
});


//--------------------------------------------------//
//--------------------------------------------------//
// Effetti allo scrool //
jQuery(document).ready(function() {
  wWINDOW = $(window).width();
  if (wWINDOW > 992) {
    jQuery('.fadein').addClass("hidden").viewportChecker({
      classToAdd: 'visible animated fadeIn',
      offset: 120
    });
    jQuery('.fadeinup').addClass("hidden").viewportChecker({
      classToAdd: 'visible animated fadeInUp',
      offset: 120
    });
    jQuery('.zoomin').addClass("hidden").viewportChecker({
      classToAdd: 'visible animated zoomIn',
      offset: 120
    });
    jQuery('.fadeindown').addClass("hidden").viewportChecker({
      classToAdd: 'visible animated fadeInDown',
      offset: 120
    });
    jQuery('.fromleft').addClass("hidden").viewportChecker({
      classToAdd: 'visible animated bounceInLeft',
      offset: 120
    });
    jQuery('.fromright').addClass("hidden").viewportChecker({
      classToAdd: 'visible animated bounceInRight',
      offset: 120
    });
    jQuery('.fadeinleft').addClass("hidden").viewportChecker({
      classToAdd: 'visible animated fadeInLeft',
      offset: 120
    });
    jQuery('.fadeinright').addClass("hidden").viewportChecker({
      classToAdd: 'visible animated fadeInRight',
      offset: 120
    });
    jQuery('.flipinx').addClass("hidden").viewportChecker({
      classToAdd: 'visible animated flipInX',
      offset: 120
    });
  }
});

//--------------------------------------------------//
//--------------------------------------------------//
// Equalize //
function EQ(id) {
  wWindow = $(window).width();
  HEQ = 0;
  $('.' + id).css({
    'height': 'auto'
  });
  $('.' + id).each(function() {
    HID = $(this).outerHeight();
    if (HID > HEQ) {
      HEQ = HID;
    }
  });
  $('.' + id).css({
    'height': HEQ
  });
}
//
function EQR() {
  EQ('m-eq');
  EQ('m-eq1');
  EQ('m-eq2');
  EQ('m-eq3');
  EQ('m-eq4');
  EQ('m-eq5');
  EQ('olm-eq');
  EQ('footer-eq2');
  EQ('footer-eq');
  EQ('bnf-eq');
  EQ('bnfb-eq');
  EQ('bnfb-eq2');
  EQ('cam-eq');
  EQ('info-eq');
  EQ('info-eq2');
  EQ('m-eqcamera');
  EQ('m-eqform');
}
EQR();
jQuery(document).ready(function() {
  EQR();
  setTimeout(EQR,1000);
  setTimeout(EQR,2000);
  setTimeout(EQR,3000);
  setTimeout(EQR,4000);
});
window.addEventListener("resize", EQR);
//--------------------------------------------------//
//--------------------------------------------------//
// IMAGEFILL // 
$('.m-img').imagefill();
//--------------------------------------------------//
////--------------------------------------------------//



//FUNZIONE OPEN /CLOSE
function foc(id,initialstatus,closeall,eccezione){
  var content=$("#"+id).find('.content');
  var oc=$("#"+id+' .box7 .oc');
  var frase1=oc.attr("frase1");
  var frase2=oc.attr("frase2");
  var openclose=oc.attr("oc");
  //closeall
  if(closeall==1){
    $('.oc').each(function(){
      var ifrase1=$(this).attr("frase1");
      var iid=$(this).attr("box");
      var icontent=$("#"+iid).find('.content');
      if(iid!=eccezione){
        icontent.slideUp(0);
        $(this).html(ifrase1+' <i class="fal fa-angle-double-down"></i>');
      }
    });
  }
  //status iniziale
  if(initialstatus==1){
    if(openclose=='1'){
      content.slideDown(0);
      oc.html(frase2+' <i class="fal fa-angle-double-up"></i>');
    }else{
      content.slideUp(0);
      oc.html(frase1+' <i class="fal fa-angle-double-down"></i>');
    }
  }else{
    content.slideToggle(200, function(){
      if(content.is(":visible")){
          oc.html(frase2+' <i class="fal fa-angle-double-up"></i>');
      }else{
          oc.html(frase1+' <i class="fal fa-angle-double-down"></i>');
      }
    });
  }
}
//FUNZIONE OPEN CLOSE SEMPLIFICATA
// 1=aperto / 2 chiuso
function foc2(id,oc){
  var content=$("#"+id).find('.content');
  var oc=$("#"+id+' .box7 .oc');
  var frase1=oc.attr("frase1");
  var frase2=oc.attr("frase2");
  if(oc==1){
      content.slideDown(200);
      oc.html(frase2+' <i class="fal fa-angle-double-up"></i>');
  }else{
      content.slideUp(200);
      oc.html(frase1+' <i class="fal fa-angle-double-down"></i>');
  }
}
//
//OC CLICK
$(".oc").click(function(){
  var id=$(this).attr("box");
  foc(id,0,0);
});
//OC INIZIALE
$(function() {
  $('.oc').each(function(){
    var id=$(this).attr("box");
    foc(id,1,0);
  });
});
//OC MENU
$(".vm").click(function() {
  section = $(this).attr('section');
  scroll_to(section, 200, 1000);
  foc(section,0,1);
});
//OC MENU MOBILE
$(".vmb").click(function() {
  section = $(this).attr('section');
  scroll_to(section, 120, 1000);
  foc(section,0,1);
  $('#menumb').removeClass('opened');
});
//
//PULSANTE con movimento
$(".pulsante").click(function() {
  section = $(this).attr('section');
  idproposta = $(this).attr('propostaid');
  idfproposta = 'fproposta'+idproposta;
  scroll_to(section, 200, 1000);
  foc(section,0,1,'proposte');
  ffproposta(idfproposta,idproposta)
});
//
//PROPOSTE
//
//
function fproposta(proposta){
  
  $('.tab').removeClass('checked');
  $('#tab'+proposta).addClass('checked');
  $(".contenitore").removeClass("selected");
  $("#contenitore"+proposta).addClass("selected");
  $(".camera").removeClass("selected");
  $("#camera"+proposta).addClass("selected");
  $(".conto").removeClass("selected");
  $("#conto"+proposta).addClass("selected");
};
$(".tab").click(function(){
  var proposta=$(this).attr('proposta');
  fproposta(proposta);
});
//
//
//FORM 
//
//funzione di selezione della proposta nel Form
function ffproposta(id,proposta){
  var proposta=$("#" + id);
  $('.fproposta').removeClass('selected');
  $('.fproposta').find('.specchietto').slideUp(100);
  proposta.addClass('selected');
  proposta.find('.specchietto').slideDown(100);
  idprop=proposta.attr('idprop');
  n=proposta.attr('propostaid');
  propostatitolo=proposta.attr('propostatitolo');
  $('.formproposta').addClass('choosen');
  $('.formproposta .conferma').html('<input type="hidden" name="proposta['+idprop+']" id="proposta'+n+'" value="'+propostatitolo+'" />'+propostatitolo);
}
$(".fproposta").click(function(){
  id=$(this).attr('id');
  proposta=$(this).attr('proposta');
  ffproposta(id,proposta);
})

$(function() {
  fproposta(1);
  ffproposta('fproposta1');
})
//controllo al click del pulsante se sono state scelte proposte

//
//--------------------------------------------------//
//--------------------------------------------------//
// Scrool MENU //
$(document).scroll(function() {
  if ($(window).scrollTop() > 100) {
    $("#menu").addClass('scrolled');
    $("#chat").addClass('scrolled');
    //foc2('chat',2);
  } else {
    $("#menu").removeClass('scrolled');
    $("#chat").removeClass('scrolled');

  }
});
//
//
//
//
  $("#color .quad").click(function(){
    var colore1=$(this).attr("colore1");
    var colore2=$(this).attr("colore2");
    var url = "index.php?colore1="+colore1+"&colore2="+colore2;    
    window.location.href = url;
  });
 //
//--------------------------------------------------//
//--------------------------------------------------//
// MENU MOBILE //
$('#menumb .hamburger').click(function(){
    $('#menumb').addClass('opened');
});
$('#menumb .closex').click(function(){
    $('#menumb').removeClass('opened');
})

//*******************************2019**/


//PROPOSTE
//EQUALIZZAZIONE
function fPROPOSTAEQ() {
  var PROPOSTAEQ = 0;
  $('.specchietto .contenitore').each(function() {
    HPROPOSTA = $(this).outerHeight();
    if (HPROPOSTA > PROPOSTAEQ) {
      PROPOSTAEQ = HPROPOSTA;
    }
  });
  newHPROPOSTA=PROPOSTAEQ +60;
  $('.specchietto').css({
    'height': PROPOSTAEQ + 60
  });
}
//
fPROPOSTAEQ();
jQuery(document).ready(function() {
  fPROPOSTAEQ();
});
window.addEventListener("resize", fPROPOSTAEQ);
//
//
//PROPOSTE
//
//
function fproposta(proposta){
  
  $('.tab2019').removeClass('selected');
  $('#tab'+proposta).addClass('selected');
  $(".contenitore").removeClass("selected");
  $("#contenitore"+proposta).addClass("selected");
  $(".camera").removeClass("selected");
  $("#camera"+proposta).addClass("selected");
  $(".conto").removeClass("selected");
  $("#conto"+proposta).addClass("selected");
};
$(".tab2019").click(function(){
  var proposta=$(this).attr('proposta');
  fproposta(proposta);
});