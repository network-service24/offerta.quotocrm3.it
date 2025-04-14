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
    <title>{{ __('checkin.meta.TITLE3') }}</title>
    <meta name="keywords" content="{{ __('checkin.meta.KEY3') }}" />
    <meta name="description" content="{{ __('checkin.meta.DESC3') }}" /> 
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
  <body class="darkbg">    
    <div class="smart-wrap">

        <div class="smart-forms smart-container wrap-1">
        
            <div class="form-header header-primary">
            <?=($Logo ==''?'<i class="fa fa-bed fa-5x fa-fw"></i>':'<img src="'.config('global.settings.BASE_URL_IMG').'uploads/loghi/'.$Logo.'" />')?><br><br>
                <h4><i class="fa fa-pencil-square"></i>
                <?=ucfirst($Nome)?> <?=ucfirst($Cognome)?><br>                     
                    <div style="padding-left:50px"><?= __('checkin.titoli.TITOLO3') ?> <?=$Nprenotazione?> <br> <?= __('checkin.titoli.STRILLO3') ?> </div>
                </h4>
                <br><br>
                  <div class="text-right">
                    <span style="color:#EF4047">{{$hotel}}</span><br>
                    {{$indirizzo}} - {{$cap}} - {{$comune}} ({{$prov}})<br>
                    Tel. {{$tel}} Email: {{$email}}<br>
                    {{$SitoWeb}}
                </div>

            </div>{{-- end .form-header section --}}  
                  
        </div>
        <p style="width:100%;font-size:11px;line-height:14px;text-align:center;color:#FFFFFF;"><em>Powered By <img src="/img/logo_quoto.png" style="width:100px">  <a href="https://www.network-service.it" target="_blank" style="color:#FFFFFF;">Network Service s.r.l.</a></small></em></p>
     </div> 
     @php echo $contentBanner @endphp  
  </body>
</html>