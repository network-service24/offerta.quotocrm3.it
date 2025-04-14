<?php App::setLocale($Lingua); ?>
<!DOCTYPE html>
<html lang="{{ $Lingua }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Marcello Visigalli">
    <meta name="copyright" content="Network Service srl">
    <meta name="generator" content="Laravel 10 | editor VsCode">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('checkin.meta.TITLE2') }}</title>
    <meta name="keywords" content="{{ __('checkin.meta.KEY2') }}" />
    <meta name="description" content="{{ __('checkin.meta.DESC2') }}" /> 
    <link rel="stylesheet" type="text/css"  href="{{asset('checkin/css/smart-forms.css')}}">
    <link rel="stylesheet" type="text/css"  href="{{asset('checkin/css/component.css')}}">
    <script src="https://use.fontawesome.com/da6d3ea52f.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script src="{{asset('checkin/js/custom-file-input.js')}}"></script>
    <script src="{{asset('checkin/js/jquery.custom-file-input.js')}}"></script>
    {{--[if lte IE 9]>
        <script type="text/javascript" src="{{asset('checkin/js/jquery-1.9.1.min.js')}}"></script>    
        <script type="text/javascript" src="{{asset('checkin/js/jquery.placeholder.min.js')}}"></script>
    <![endif]--}}    
    
    {{--[if lte IE 8]>
        <link type="text/css" rel="stylesheet" href="{{asset('checkin/css/smart-forms-ie8.css')}}">
    <![endif]--}}    
    {{-- Bootstrap --}}
    <link href="{{asset('checkin/css/bootstrap.min.css')}}" rel="stylesheet">
    {{-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries --}}
    {{-- WARNING: Respond.js doesn't work if you view the page via file:// --}}
    {{--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]--}}
    <style>
        .clear{
            clear:both;
            padding-top:8px;
                
            }
          #clear{
            clear:both;
            padding-top:20px;
                
            }
            .nowrap {
                overflow-x:auto;
                overflow-y:hidden;
                white-space: nowrap;
            } 
            #box{
                background: none repeat scroll 0% 0% #F7F7F7;
                position: relative;
                vertical-align: top;
                border: 2px solid #BDC3C7;
                display: inline-block;
                color: #34495E;
                outline: medium none;
                height: 42px;
                width: 100%;
            }       
    </style>
    {{-- Include all compiled plugins (below), or include individual files as needed --}}
    <script src="{{asset('checkin/js/bootstrap.js')}}"></script>
    <script src="{{asset('checkin/js/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('checkin/js/functionJS.inc.js')}}" type="text/javascript"></script>    
   
  </head>
  <body class="darkbg" onload="ctrl();">
 
    <div class="smart-wrap">
        <div class="smart-forms smart-container wrap-1">
        
            <div class="form-header header-primary">
            <?=($Logo ==''?'<i class="fa fa-bed fa-5x fa-fw"></i>':'<img src="'.config('global.settings.BASE_URL_IMG').'uploads/loghi/'.$Logo.'" />')?><br><br>
                <h4><i class="fa fa-pencil-square"></i>
                <?=ucfirst($Nome)?> <?=ucfirst($Cognome)?><br>                    
                    <div style="padding-left:50px">{{ __('checkin.titoli.TITOLO2') }}<br>{{ __('checkin.titoli.STRILLO2') }} <?=$nome?>  <?=$cognome?><br>
                        <small style="color:#F1F1F1">{{ __('checkin.titoli.TITOLO3') }}  <?=$Nprenotazione?> - N° <?=$NumeroPersone?> {{ __('checkin.termina.PERSONE') }} - {{ __('checkin.termina.DATARICHIESTA') }} <?=$DataRichiesta?> </small>
                </h4>
            </div>  
               <div class="form-body">                     
                  <div class="spacer-b30">
                        <div class="tagline"><span>{{__('checkin.etichette.STEP')}} {{$step}} {{__('checkin.etichette.DI')}} {{session('NumeroPersone')}}</span></div><!-- .tagline -->
                    </div>                             
        
                   <form id="checkin_form" class="form-horizontal" role="form" method="post" action="/insertStep" enctype="multipart/form-data" >
                        <div class="frm-row"> <!--apertura riga-->                   
                           <div class="section colm colm6"> <!--apertura colonna-->
                               <label for="TipoComponente" class="field select">               
                                    <select data-toggle="tooltip" data-trigger="hover" data-placement="top" title="{{ __('checkin.etichette.TOOLTIP_COMPONENTI') }}" id="foo" tabindex="1"  tabindex="1" class="form-control"  name="TipoComponente" id="TipoComponente" >
                                      <option value="" selected="selected">{{ __('checkin.tipologia_componente.TIPOLOGIA_COMPONENTE') }}</option>                                       
                                      <option value="Capo Famiglia">{{ __('checkin.tipologia_componente.CAPO_FAMIGLIA') }}</option>
                                      <option value="Familiare">{{ __('checkin.tipologia_componente.FAMIGLIARE') }}</option>
                                      <option value="Capo Gruppo">{{ __('checkin.tipologia_componente.CAPO_GRUPPO') }}</option>
                                      <option value="Membro Gruppo">{{ __('checkin.tipologia_componente.MEMBRO_GRUPPO') }}</option>
                                      <option value="Ospite Singolo">{{ __('checkin.tipologia_componente.COMPONENTE_SINGOLO') }}</option>
                                  </select>
                                  <i class="arrow double"></i>           
                               </label>
                            </div><!--chiusura colonna-->
                           <div class="section colm colm6"> <!--apertura colonna-->
                               <span id="helpBlock" class="help-block"><?=nl2br(__('checkin.etichette.TXT_HELP3'))?></span>
                            </div><!--chiusura colonna--> 
                         </div><!--chiusura riga-->                        

            <div class="CF" style="display: none;">
       
                          <div class="frm-row"> <!--apertura riga-->                   
                           <div class="section colm colm6"> <!--apertura colonna-->
                            <label for="TipoDocumento" class="field select">                         
                              <select class="form-control"  name="TipoDocumento" id="TipoDocumento" tabindex="2">
                                  <option value="" selected="selected">{{ __('checkin.documenti.TIPO_DOCUMENTO') }}</option>
                                  <option value="Carta di Identità">{{ __('checkin.documenti.CARTA_IDENTITA') }}</option>
                                  <option value="Passaporto">{{ __('checkin.documenti.PASSAPORTO') }}</option>
                                  <option value="Patente">{{ __('checkin.documenti.PATENTE') }}</option>
                              </select>
                              <i class="arrow double"></i>
                              </label>
                            </div><!--chiusura colonna-->
                     <div class="section colm colm6"><!-- apertura  colonna-->    
                            <label for="NumeroDocumento" class="field prepend-icon">
                              <input type="text" class="gui-input" placeholder="{{ __('checkin.documenti.NUMERO_DOCUMENTO') }}" name="NumeroDocumento" id="NumeroDocumento" tabindex="3">
                              <label for="cardno" class="field-icon"><i class="fa fa-barcode"></i></label>  
                          </label>                                           
                        </div><!-- chiusura colonna-->  
                 </div>           
                   <div class="frm-row"><!-- apertura riga -->  
                       <div class="section colm colm6"><!-- apertura colonna -->            
 
                             <label for="ComuneEmissione" class="field prepend-icon">
                                <input class="gui-input" id="ComuneEmissione" name="ComuneEmissione" type="text" placeholder="{{ __('checkin.documenti.COMUNE_EMISSIONE') }}" tabindex="14" >
                                <label for="city" class="field-icon"><i class="fa fa-map-marker"></i></label>
                              </label>
                  
                       </div> <!-- chiusura colonna-->   
                     <div class="section colm colm6"><!-- apertura quarta colonna a destra-->  
                            <label for="DataRilascio" class="field prepend-icon">
                               <input class="gui-input" id="DataRilascio" placeholder="{{ __('checkin.documenti.DATA_RILASCIO') }}" onMouseOver="(this.type= 'date');" name="DataRilascio" type="text" tabindex="5" title="{{ __('checkin.documenti.DATA_RILASCIO') }}"> 
                               <label for="date" class="field-icon"><i class="fa fa-calendar"></i></label> 
                             </label>                                  
                      </div><!-- chiusura  colonna-->              
                    </div><!-- chiusura riga--> 
                     <div class="frm-row"><!-- apertura riga -->  
                         <div class="section colm colm6"><!-- apertura terza colonna a sinistra-->   
                            <label for="StatoEmissione" class="filed select">
                                    <select name="StatoEmissione" id="StatoEmissione" class="form-control" tabindex="6">
                                        <option value="" selected="selected">{{ __('checkin.documenti.STATO_EMISSIONE') }}</option>
                                        <option value="Italia">ITALIA</option>
                                        @php echo $list_stato @endphp
                                    </select>
                                    <i class="arrow double"></i>
                             </label> 
                           </div>
                           <div class="section colm colm6"><!-- apertura terza colonna a sinistra-->   
                            <label for="DataScadenza" class="field prepend-icon">
                              <input class="gui-input" placeholder="{{ __('checkin.documenti.DATA_SCADENZA') }}" onMouseOver="(this.type= 'date');" id="DataScadenza" type="text"  name="DataScadenza" tabindex="7" title="{{ __('checkin.documenti.DATA_SCADENZA') }}">
                              <label for="date" class="field-icon"><i class="fa fa-calendar"></i></label>
                           </label>                                        
                      </div><!-- chiusura quarta colonna a destra-->   
                </div> <!-- chiusura riga -->    
                <div class="frm-row">
                <div class="section colm colm12">
                    <div class="box">
                        <div class="input-group">
                            <span class="input-group-addon bg-navy"><i class="fa fa-fw fa-file-o"></i></span>
                            <input type="file" class="form-control gui-input"  name="file_logo" id="file">
                        </div>
                      <div id="result_file" class="text-center"></div>
                      <input type="hidden"  id="Documento" name="Documento" />
                      <script>
                        $(function(){                               
                          //CARICO IL LOGO
                          $("#file").on("change",function(){
                              formdata = new FormData();
                              if($("#file").prop('files').length > 0)
                              {
                                  file =$("#file").prop('files')[0];
                                  formdata.append("file", file);
                              }
                              $.ajaxSetup({
                                  headers: {
                                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                  }
                              });
                              $.ajax({
                                  url: "/UpFile?idsito=<?=$idsito?>",
                                  type: "POST",
                                  data: formdata,
                                  processData: false,
                                  contentType: false,
                                  success: function (res) {
                                      console.log(res);
                                      if(res != ""){
                                          $("#Documento").val(res);
                                          $("#result_file").html("<small style=\"color:green\">Il file è stato caricato con successo!</small>");
                                      }else{
                                          $("#result_file").html("<small style=\"color:red\">Qualcosa è andato storto! Controlla l'estensione del file e riprova!!</small>");
                                      }
                                  }
                              });
                              return false;
                          }) ;
                        })
                      </script>
                  </div>
                  <small>Formati accettati: PDF, jpg, jpeg, gif, png</small>
              </div>
            </div>                                      
       </div> <!-- chiusura div nascosto campi relativi al documento di riconoscimento --> 
 
       <div class="spacer-t20 spacer-b30">
            <div class="tagline"><span>{{ __('checkin.anagrafica.ANAGRAFICA') }}</span></div><!-- .tagline -->
        </div> 
        
            <div class="frm-row"><!-- apertura riga -->         
              <div class="section colm colm6"><!-- apertura colonna -->
                    <label for="Nome" class="field prepend-icon">
                      <input type="text" class="gui-input" placeholder="{{ __('checkin.anagrafica.NOME') }}" name="Nome" id="Nome" value="{{$nome}}" tabindex="8">                    
                            <label for="names" class="field-icon"><i class="fa fa-user"></i></label>  
                    </label>
                </div>    <!-- chiusura colonna --> 
                <div class="section colm colm6"><!-- apertura colonna --> 
                    <label for="Cognome" class="field prepend-icon">
                      <input type="text" class="gui-input" placeholder="{{ __('checkin.anagrafica.COGNOME') }}" name="Cognome" value="{{$cognome}}" tabindex="9">
                      <label for="lastname" class="field-icon"><i class="fa fa-user"></i></label>  
                    </label>     
                 </div> <!-- chiusura colonna -->   
              </div>  <!-- chiusura riga -->     
                    
            <div class="frm-row"><!-- apertura riga -->         
              <div class="section colm colm6"><!-- apertura colonna -->
                    <label for="Sesso" class="field select">
                      <select name="Sesso" tabindex="10">
                          <option value="" selected="selected">{{ __('checkin.anagrafica.SESSO') }}</option>
                          <option value="Maschio">{{ __('checkin.anagrafica.MASCHIO') }}</option>
                          <option value="Femmina">{{ __('checkin.anagrafica.FEMMINA') }}</option>
                      </select>
                      <i class="arrow double"></i>
                    </label>
                </div>    <!-- chiusura colonna --> 
                <div class="section colm colm6"><!-- apertura colonna --> 
                    <label for="Cittadinanza" class="field select">
                            <select name="Cittadinanza" id="Cittadinanza" onchange="ctrl();" tabindex="11">
                                <option value="" selected="selected">{{ __('checkin.anagrafica.CITTADINANZA') }}</option> 
                                <option value="Italia" {{($Cittadinanza=='Italia'?'selected="selected"':'')}}>ITALIA</option>
                                @php echo $list_stato @endphp
                            </select>
                            <i class="arrow double"></i>                      
                   </label>   
                 </div> <!-- chiusura colonna -->   
              </div>  <!-- chiusura riga -->                     
                    
             <div class="frm-row"><!-- apertura riga -->         
              <div class="section colm colm6"><!-- apertura colonna -->
                    <div class="reg"><!-- innerhtml per nascondere il div se non cittadino italiano -->           
                    <label for="nome_regione" class="field select">
                        <select  id="id_regione" name="id_regione"  tabindex="12">
                            <option value="" selected="selected">{{ __('checkin.anagrafica.REGIONE') }}</option>
                            <option value="{{ $id_regione }}" selected="selected">{{ $NomeRegione }}</option>  
                        </select> 
                        <i class="arrow double"></i>  
                   </label>
                   </div>
                  <div class="cit_bis"  style="display: none;">
                       <label for="Citta" class="field prepend-icon">
                        <input class="gui-input" id="CittaBis" name="CittaBis" type="text" placeholder="{{ __('checkin.anagrafica.CITTA') }}" value="{{$Citta}}" tabindex="14" >
                        <label for="city" class="field-icon"><i class="fa fa-map-marker"></i></label>
                        </label>
                   </div> 
                </div>    <!-- chiusura colonna --> 
                <div class="section colm colm6"><!-- apertura colonna --> 
                      <div class="prov"><!-- innerhtml per nascondere il div se non cittadino italiano -->
                        <label for="Provincia" class="field select">
                        <select  id="Provincia" name="Provincia"  tabindex="13">
                         <option value="" selected="selected">{{ __('checkin.anagrafica.PROVINCIA') }}</option>    
                         <option value="{{ $Provincia }}" selected="selected">{{ $Provincia }}</option>    
                        </select>
                        <i class="arrow double"></i> 
                        </label>
                    </div> 
                    <div class="prov_bis"  style="display: none;">
                        <label for="Provincia" class="field">
                        <input type="text" class="gui-input" placeholder="{{ __('checkin.anagrafica.PROVINCIA') }}" name="ProvinciaBis" value="{{ $Provincia }}" tabindex="13">
                        </label>
                    </div>  
                 </div> <!-- chiusura colonna -->   
              </div>  <!-- chiusura riga -->                    
                    
      
            <div class="frm-row"><!-- apertura riga -->         
              <div class="section colm colm6"><!-- apertura colonna -->
                     <label for="Indirizzo" class="field prepend-icon">
                      <input type="text" class="gui-input" placeholder="{{ __('checkin.anagrafica.INDIRIZZO') }}" name="Indirizzo" value="{{ $Indirizzo}}" tabindex="14">
                      <label for="firstaddr" class="field-icon"><i class="fa fa-building-o"></i></label>   
                    </label>                                  
                </div>    <!-- chiusura colonna --> 
                <div class="section colm colm6"><!-- apertura colonna --> 
                    <div class="cit"><!-- innerhtml per nascondere il div se non cittadino italiano -->
                            <label for="Citta" class="field select">
                       <select name="Citta" id="Citta" tabindex="15" >
                           <option value="" selected="selected">{{ __('checkin.anagrafica.CITTA') }}</option>
                           <option value="{{ $Citta }}" selected="selected">{{$Citta}}</option>                           
                       </select> 
                       <i class="arrow double"></i>  
                       </label>
                    </div>
                    <div class="vuoto"  id="box" style="display: none;"></div>  
                 </div> <!-- chiusura colonna -->   
              </div>  <!-- chiusura riga -->        
      
            <div class="frm-row"><!-- apertura riga -->         
              <div class="section colm colm6"><!-- apertura colonna -->
                  <div class="cp">  
                    <label for="Cap" class="field prepend-icon">           
                       <input type="text" class="gui-input" placeholder="{{ __('checkin.anagrafica.CAP') }}" name="Cap" value="{{ $Cap }}" tabindex="16" maxlength="5">
                       <label for="zip" class="field-icon"><i class="fa fa-certificate"></i></label>             
                   </label>
                  </div> 
                   <div class="vuoto"  id="box" style="display: none;"></div>                             
                </div>    <!-- chiusura colonna --> 
                <div class="section colm colm6"><!-- apertura colonna --> 
                     <label for="DataNascita" class="field prepend-icon">
                        <input placeholder="{{ __('checkin.anagrafica.DATA_NASCITA') }}" class="gui-input" onMouseOver="(this.type= 'date');" id="DataNascita" name="DataNascita" type="text" tabindex="17" title="{{ __('checkin.anagrafica.DATA_NASCITA') }}" >
                        <label for="date" class="field-icon"><i class="fa fa-calendar"></i></label>
                    </label>
                 </div> <!-- chiusura colonna -->   
              </div>  <!-- chiusura riga -->       
      
            <div class="frm-row"><!-- apertura riga -->         
              <div class="section colm colm6"><!-- apertura colonna -->
                      <label for="StatoNascita" class="field select">
                            <select name="StatoNascita" id="StatoNascita"  onchange="ctrl2();" tabindex="18">
                                <option value="" selected="selected">{{ __('checkin.anagrafica.STATO_NASCITA') }}</option> 
                                <option value="Italia">ITALIA</option>
                                @php echo $list_stato @endphp
                            </select>
                      <i class="arrow double"></i>  
                  </label>                           
                </div>    <!-- chiusura colonna --> 
                <div class="section colm colm6"><!-- apertura colonna --> 
                      <div class="reg2"><!-- innerhtml per nascondere il div se non cittadino italiano -->  
                        <label for="Regione2" class="field select">
                        
                            <select id="id_regione2" name="id_regione2" tabindex="19">
                                <option value="" selected="selected">{{ __('checkin.anagrafica.REGIONE_NASCITA') }}</option>
                            </select>
                            <i class="arrow double"></i> 
                        </label>
                  </div> 
                   <div class="vuoto2"  id="box" style="display: none;"></div> 
                 </div> <!-- chiusura colonna -->   
              </div>  <!-- chiusura riga -->  


            <div class="frm-row"><!-- apertura riga -->         
              <div class="section colm colm6"><!-- apertura colonna -->
                         <div class="prov2"><!-- innerhtml per nascondere il div se non cittadino italiano --> 
                            <label for="Provincia" class="field select">
                            <select id="ProvinciaNascita" name="ProvinciaNascita" tabindex="20">
                              <option value="" selected="selected">{{ __('checkin.anagrafica.PROVINCIA_NASCITA') }}</option>  
                            </select>
                            <i class="arrow double"></i>
                            </label>
                      </div>
                         <div class="prov2_bis" style="display:none;"><!-- innerhtml per nascondere il div se non cittadino italiano --> 
                             <label for="Provincia" class="field">
                            <input class="gui-input" id="ProvinciaNascitaBis" name="ProvinciaNascitaBis" type="text" placeholder="{{ __('checkin.anagrafica.PROVINCIA_NASCITA') }}" tabindex="20" >
                            </label>
                      </div>                          
                </div>    <!-- chiusura colonna --> 
                <div class="section colm colm6"><!-- apertura colonna --> 
                        <div class="cit2"><!-- innerhtml per nascondere il div se non cittadino italiano --> 
                            <label for="LuogoNascita" class="field select">
                            <select name="LuogoNascita"  id="LuogoNascita" tabindex="21">
                             <option value="" selected="selected">{{ __('checkin.anagrafica.LUOGO_NASCITA') }}</option>                                 
                            </select>
                            <i class="arrow double"></i>
                            </label>
                       </div> 
                         <div class="cit2_bis" style="display:none;"><!-- innerhtml per nascondere il div se non cittadino italiano -->
                             <label for="LuogoNascita" class="field prepend-icon"> 
                            <input class="gui-input" id="LuogoNascitaBis" name="LuogoNascitaBis" type="text" placeholder="{{ __('checkin.anagrafica.LUOGO_NASCITA') }}" tabindex="21" >
                            <label for="city" class="field-icon"><i class="fa fa-map-marker"></i></label>
                            </label>
                       </div>  
                 </div> <!-- chiusura colonna -->   
              </div>  <!-- chiusura riga --> 


             <div class="frm-row"><!-- apertura riga -->         
                  <div class="section colm colm6"><!-- apertura colonna a sinistra-->
                      @if(session('NumeroPersone') > $step)                                                                      
                              <button type="submit" class="btn btn-primary">{{ __('checkin.altro.AVANTI') }}</button>    
                              <div id="clear"></div>
                              <small>{{ __('checkin.altro.PROCEDI_TXT') }}</small>                                       
                       @else                      
                            <label for="Note" class="field prepend-icon">
                              <textarea class="gui-textarea" rows="3"  name="Note" tabindex="22"></textarea>
                              <b class="tooltip tip-left-top"><em>{{ __('checkin.altro.TOOLTIP_NOTE') }}</em></b>
                            <label for="comment" class="field-icon"><i class="fa fa-comments"></i></label> 
                           </label>
                            <div id="clear"></div>
                            <input name="policy_soggiorno" id="policy_soggiorno" type="radio" value="1"  required><br> @php echo __('checkin.privacy.ACCONSENTI_PRIVACY_POLICY_SOGGIORNO') @endphp
                                  <div id="politiche_soggiorno" style="display:none">
                                        <small>
                                        <br>
                                           <?=str_replace('[struttura]','<b>'.$hotel.'</b>',__('checkin.privacy.INFORMATIVA_PRIVACY'));?>                                   
                                        </small>
                                 </div> 
                                 <script>
                                      $(document).ready(function() {
                                            $("#sblocca_politiche_soggiorno").click(function(){
                                                $( "#politiche_soggiorno" ).toggle();
                                            }); 
                                        });           
                                  </script>
                          <div id="clear"></div>                                                                        
                            <button type="submit" class="btn btn-primary">{{ __('checkin.termina.TERMINA') }}</button>                                                                 
                    @endif
                  </div> <!-- chiusura colonna a sinistra-->            
                   <div class="section colm colm6"><!-- apertura colonna a sinistra-->
                   </div> <!-- chiusura colonna a destra-->
               </div>   <!-- chiusura riga--> 
                <input type="hidden" value="{{$Lingua}}" name="lang">
                <input type="hidden" value="{{$step}}" name="step"> 
                <input type="hidden" value="{{$NumeroPersone}}" name="NumeroPersone">   
                <input type="hidden" name="Prenotazione" value="{{$Nprenotazione}}">
                <input type="hidden" name="idsito" value="{{$idsito}}">
                <input type="hidden" name="params" value="{{$params}}">
                <input type="hidden" name="directory" value="{{$directory}}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" value="insert" name="action">
            </form>
                <div class="text-right">
                  <span style="color:#EF4047">{{$hotel}}</span><br>
                  {{$indirizzo}} - {{$cap}} - {{$comune}} ({{$prov}})<br>
                  Tel. {{$tel}} Email: {{$email}}<br>
                  {{$SitoWeb}}
                </div>   
           </div>
        </div>
        <p style="width:100%;font-size:11px;line-height:14px;text-align:center;color:#FFFFFF;"><em>Powered By <img src="/img/logo_quoto.png" style="width:100px">  <a href="https://www.network-service.it" target="_blank" style="color:#FFFFFF;">Network Service s.r.l.</a></small></em></p>
    </div>
    <script>
        $(function(){
            // AJAX STATI-REGIONI-PROVINCE-COMUNI
            function aggiornaRegioni() {
                var id_stato = $("#Cittadinanza option:selected").val();
                if (id_stato) { // Controlla che ci sia un valore selezionato
                    $.ajax({
                        url: "/listaRegioni",
                        type: "POST",
                        data: {"_token": "<?php echo csrf_token() ?>", id_stato: id_stato},
                        success: function(response){
                            $('#id_regione').html(response);
                        },
                        error: function(response){
                            console.log(response);
                        }
                    });
                }
            }
            // Lancia la funzione al caricamento della pagina
            //aggiornaRegioni();

            // Lancia la funzione al cambio di valore del select
            $("#Cittadinanza").on("change", function(){
                aggiornaRegioni();
            });

            $("#id_regione").on("change",function(){
                var id_regione = $("#id_regione option:selected").val();
                    $.ajax({
                        url: "/listaProvince",
                        type: "POST",
                        data: {"_token": "<?php echo csrf_token() ?>",id_regione: id_regione},
                        success: function(response){
                                $('#Provincia').html(response);
                        },
                        error: function(response){
                            console.log(response);
                        }
                    });
                    return false;
            });
    
            $("#Provincia").on("change",function(){
                var sigla_provincia = $("#Provincia option:selected").val();
                    $.ajax({
                        url: "/listaComuni",
                        type: "POST",
                        data: {"_token": "<?php echo csrf_token() ?>",sigla_provincia: sigla_provincia},
                        success: function(response){
                                $('#Citta').html(response);
                        },
                        error: function(response){
                            console.log(response);
                        }
                    });
                    return false;
            });
            $("#StatoNascita").on("change",function(){
                var id_stato = $("#StatoNascita option:selected").val();
                    $.ajax({
                        url: "/listaRegioni",
                        type: "POST",
                        data: {"_token": "<?php echo csrf_token() ?>",id_stato: id_stato},
                        success: function(response){
                                $('#id_regione2').html(response);
                        },
                        error: function(response){
                            console.log(response);
                        }
                    });
                    return false;
            });
    
            $("#id_regione2").on("change",function(){
                var id_regione = $("#id_regione2 option:selected").val();
                    $.ajax({
                        url: "/listaProvince",
                        type: "POST",
                        data: {"_token": "<?php echo csrf_token() ?>",id_regione: id_regione},
                        success: function(response){
                                $('#ProvinciaNascita').html(response);
                        },
                        error: function(response){
                            console.log(response);
                        }
                    });
                    return false;
            });
    
            $("#ProvinciaNascita").on("change",function(){
                var sigla_provincia = $("#ProvinciaNascita option:selected").val();
                    $.ajax({
                        url: "/listaComuni",
                        type: "POST",
                        data: {"_token": "<?php echo csrf_token() ?>",sigla_provincia: sigla_provincia},
                        success: function(response){
                                $('#LuogoNascita').html(response);
                        },
                        error: function(response){
                            console.log(response);
                        }
                    });
                    return false;
            });
        })  
        </script> 
    @php echo $contentBanner @endphp 
  </body>
</html>