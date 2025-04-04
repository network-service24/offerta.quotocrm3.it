<!DOCTYPE html>
<html lang="<?=$Lingua?>">
	<head>
		<meta charset="utf-8">

		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">

        <meta name="description" content="QUOTO!: software CRM per la gestione di preventivi, conferme e prenotazioni in hotel.">
		<!-- Autore e Proprietario intellettuale -->
		<meta name="author" content="Marcello Visigalli">
		<!-- Gestore del Software -->
		<meta name="copyright" content="Network Service srl">
        <!-- Editor usato -->
		<meta name="generator" content="Laravel 10 | editor VsCode">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{$NomeCliente}} | {{env('APP_NAME')}}</title>

		<!-- ANIMATE -->
		<link rel="stylesheet" href="{{asset('v3/css/animate_3.5.2.min.css')}}" >
		<!-- /ANIMATE -->
		<!-- FONT AWESOME -->
		<script defer src="{{asset('v3/js/fontawesome-all.min.js')}}"></script>
		<!-- /FONT AWESOME -->
		<!--JQUERY -->
		<script src="{{asset('v3/js/jquery-3.1.1.min.js')}}"></script>
		<!-- UI -->
		<link type="text/css" href="{{asset('v3/jquery_ui/jquery-ui.min.css')}}" rel="Stylesheet" />
		<link type="text/css" href="{{asset('v3/jquery_ui/jquery-ui.structure.min.css')}}" rel="Stylesheet" />
		<link type="text/css" href="{{asset('v3/jquery_ui/jquery-ui.theme.min.css')}}" rel="Stylesheet" />
		<script src="{{asset('v3/jquery_ui/jquery-ui.min.js')}}"></script>
		<!-- /UI -->
		<!-- VIEWPORTCHECKER -->
		<script type="text/javascript" src="{{asset('v3/js/viewportchecker.1.8.7.min.js')}}" ></script>
		<!-- /VIEWPORTCHECKER -->

		<!-- JQUERY PER COOKIES -->
		<script type="text/javascript" src="{{asset('v3/js/jquery.cookie.min.js')}}"></script>
		<!-- /JQUERY PER COOKIES -->

		<!-- IMAGEFILL -->
		<script src="{{asset('v3/js/imagesloaded.4.1.1.min.js')}}"></script>
		<script src="{{asset('v3/js/jquery-imagefill.min.js')}}"></script>
		<!-- /IMAGEFILL -->

		<!-- Google Font -->
		<link href="https://fonts.googleapis.com/css?family=<?=$font_libreria?>" rel="stylesheet">
		<!-- /Google Font -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" ></script>

		<script>
			$(function () {
				$('[data-toggle="tooltip"]').tooltip()
			})
		</script>
		<style>
			/*
			* font-family: 'Open Sans', sans-serif 
			*/

			* {
				box-sizing: border-box;
			}

			html {
				overflow-x: hidden !important;
			}

			body {
				font-family: <?=$font?>;
				font-size: 20px;
				line-height: 1.3;
				color: #666;
				background-color: <?=$coloresfondo?>;
				text-rendering: optimizeLegibility;
				-webkit-text-size-adjust: 100%;
				font-weight: 300;
			}

			html,
			body {
				margin: 0;
				padding: 0;
			}

			*:focus,
			*:active {
				outline: none;
			}

			a:link,
			a:visited,
			a:active {
				-webkit-transition: all .4s ease;
				transition: all .4s ease;
				text-decoration: none;
				color: #666;
			}

			a:hover {
				color: #666;
			}

			img {
				border: 0;
			}

			p {
				margin: 2px 0;
				padding: 0;
			}

			ul,
			ol {
				margin: 0;
				padding: 15px 20px;
			}

			li {
				margin-bottom: 3px;
				padding: 0;
			}

			.ulrev {
				padding-left: 0;
				list-style-type: none;
				text-align: right;
			}

			.ulrev li {
				padding-right: 20px;
				background-image: url(/smart/img/bull.png);
				background-repeat: no-repeat;
				background-position: right center;
			}

			.z0 {
				z-index: 0;
			}

			.z1 {
				z-index: 1;
			}

			.z2 {
				z-index: 2;
			}

			.z3 {
				z-index: 3;
			}

			.z4 {
				z-index: 4;
			}

			.z5 {
				z-index: 5;
			}

			.z6 {
				z-index: 6;
			}

			.z7 {
				z-index: 7;
			}

			.z8 {
				z-index: 8;
			}

			.z9 {
				z-index: 9;
			}

			.z10 {
				z-index: 10;
			}

			hr {
				margin: 10px 0;
				color: #666;
				border-style: solid;
			}

			.h0,
			h1,
			h2,
			h3,
			h4,
			h5 {
				font-weight: normal;
				line-height: 1;
				margin: 0;
				padding: 0;
			}

			h1 {
				font-size: 70px;
				font-weight: 700;
				letter-spacing: -1pt;
				text-transform: uppercase;
			}

			h2 {
				font-size: 60px;
			}

			h3 {
				font-size: 30px;
				font-weight: 300;
				letter-spacing: -1pt;
			}

			h4 {
				font-size: 24px;
				font-weight: 300;
			}

			h5 {
				font-size: 22px;
				font-weight: 700;
				margin-bottom: 10px;
				letter-spacing: -1pt;
				text-transform: uppercase;
			}

			@media screen and (max-width: 1200px) {
				h1 {
					font-size: 60px;
				}

				h2 {
					font-size: 50px;
				}

				h3 {
					font-size: 28px;
				}

				h4 {
					font-size: 24px;
				}

				h5 {
					font-size: 22px;
				}
			}

			@media screen and (max-width: 992px) {
				h1 {
					font-size: 50px;
				}

				h2 {
					font-size: 40px;
				}

				h3 {
					font-size: 26px;
				}

				h4 {
					font-size: 24px;
				}

				h5 {
					font-size: 22px;
				}
			}

			@media screen and (max-width: 768px) {
				h1 {
					font-size: 46px;
				}

				h2 {
					font-size: 38px;
				}

				h3 {
					font-size: 24px;
				}

				h4 {
					font-size: 22px;
				}

				h5 {
					font-size: 20px;
				}
			}

			@media screen and (max-width: 576px) {
				h1 {
					font-size: 40px;
				}

				h2 {
					font-size: 36px;
				}

				h3 {
					font-size: 22px;
				}

				h4 {
					font-size: 20px;
				}

				h5 {
					font-size: 20px;
				}
			}

			.ui-datepicker-trigger {
				display: none;
			}

			.ca,
			.ca1,
			.ca2,
			.ca5,
			.ca10,
			.ca20,
			.ca30,
			.ca40,
			.ca50,
			.ca60,
			.ca70 {
				position: relative;
				clear: both;
				width: 100%;
			}

			.ca1 {
				height: 1px;
			}

			.ca2 {
				height: 2px;
			}

			.ca5 {
				height: 5px;
			}

			.ca10 {
				height: 10px;
			}

			.ca20 {
				height: 20px;
			}

			.ca30 {
				height: 30px;
			}

			.ca40 {
				height: 40px;
			}

			.ca50 {
				height: 50px;
			}

			.ca60 {
				height: 60px;
			}

			.ca70 {
				height: 70px;
			}

			.h100 {
				min-height: 100px !important;
			}

			.h150 {
				min-height: 150px !important;
			}

			.h200 {
				min-height: 200px !important;
			}

			.h250 {
				min-height: 250px !important;
			}

			.h300 {
				min-height: 300px !important;
			}

			.h350 {
				min-height: 350px !important;
			}

			.h400 {
				min-height: 400px !important;
			}

			.h450 {
				min-height: 450px !important;
			}

			.h500 {
				min-height: 500px !important;
			}

			.h550 {
				min-height: 550px !important;
			}

			.h600 {
				min-height: 600px !important;
			}

			.height550 {
				height: 550px !important;
			}

			.height600 {
				height: 600px !important;
			}

			.overflow_auto {
				overflow: auto !important;
			}

			.t10 {
				font-size: 10px;
			}

			.t11 {
				font-size: 11px;
			}

			.t12 {
				font-size: 12px;
			}

			.t13 {
				font-size: 13px;
			}

			.t14 {
				font-size: 14px;
			}

			.t15 {
				font-size: 15px;
			}

			.t16 {
				font-size: 16px;
			}

			.t18 {
				font-size: 18px;
			}

			.t20 {
				font-size: 20px;
			}

			.t22 {
				font-size: 22px;
			}

			.t23 {
				font-size: 23px;
			}

			.t24 {
				font-size: 24px;
			}

			.t25 {
				font-size: 25px;
			}

			.t30 {
				font-size: 30px;
			}

			.t35 {
				font-size: 35px;
			}

			.t40 {
				font-size: 40px;
			}

			.t50 {
				font-size: 50px;
			}

			.w100 {
				font-weight: 100;
			}

			.w300 {
				font-weight: 300;
			}

			.w400 {
				font-weight: 400;
			}

			.w700 {
				font-weight: 700;
			}

			.w900 {
				font-weight: 900;
			}

			.twhite,
			.twhite a:link,
			.twhite a:active,
			.twhite a:visited {
				text-decoration: none;
				color: #fff;
			}

			.twhite a:hover {
				color: #fff;
			}

			.tblack,
			.tblack a:link,
			.tblack a:active,
			.tblack a:visited {
				text-decoration: none;
				color: #000;
			}

			.tblack a:hover {
				color: #333;
			}

			.tcolor,
			.tcolor a:link,
			.tcolor a:active,
			.tcolor a:visited,
			.tcolor a:hover {
				color: <?=$colore1?>;
			}

			.tcolor2,
			.tcolor2 a:link,
			.tcolor2 a:active,
			.tcolor2 a:visited,
			.tcolor2 a:hover {
				color: <?=$colore2?>;
			}

			.bcolor {
				background-color: <?=$colore1?>;
			}

			.bcolor2 {
				background-color: <?=$colore2?>;
			}

			.bcolor3 {
				background-color: #E2E2E2;
			}

			.bcolorwhite {
				background-color: #FFF;
			}

			.bcolorblack {
				background-color: #000
			}

			.border {
				border-style: dotted;
				border-color: #999;
				border-width: 0px;
			}

			.tlh1 {
				line-height: 1;
			}

			.tls-1 {
				letter-spacing: -1pt;
			}

			.tl {
				text-align: left;
			}

			.tr {
				text-align: right;
			}

			.tc {
				text-align: center;
			}

			.tj {
				text-align: justify;
			}

			.fl {
				float: left;
			}

			.fr {
				float: right;
			}

			.alignw {
				-webkit-transform: translateX(50%);
				transform: translateX(50%);
			}

			.alignh {
				-webkit-transform: translateY(-50%);
				transform: translateY(-50%);
			}

			.barrato {
				text-decoration: line-through;
			}

			.tshadoww {
				text-shadow: 0 0 10px #fff;
			}

			.tshadowb {
				text-shadow: 0 0 10px #000;
			}

			.hidden {
				opacity: 0;
			}

			.visible {
				opacity: 1;
			}

			.scrolloff {
				pointer-events: none;
			}

			.pulsing {
				-webkit-animation: pulsatilla .7s ease-out infinite alternate running;
				animation: pulsatilla .7s ease-out infinite alternate running;
			}

			@keyframes pulsatilla {
				0% {
					opacity: .5;
				}

				100% {
					opacity: 1;
				}
			}

			@-webkit-keyframes pulsatilla {
				0% {
					opacity: .5;
				}

				100% {
					opacity: 1;
				}
			}

			.del1 {
				-webkit-animation-delay: .2s;
				animation-delay: .2s;
			}

			.del2 {
				-webkit-animation-delay: .3s;
				animation-delay: .3s;
			}

			.del3 {
				-webkit-animation-delay: .4s;
				animation-delay: .4s;
			}

			.del4 {
				-webkit-animation-delay: .5s;
				animation-delay: .5s;
			}

			.del5 {
				-webkit-animation-delay: .6s;
				animation-delay: .6s;
			}

			.del6 {
				-webkit-animation-delay: .7s;
				animation-delay: .7s;
			}

			#boxsent {
				display: none;
			}





			/****************************************************IN POINT**/

			#zero {
				position: relative;
				clear: both;
				width: 100%;
				height: 0;
			}

			#start {
				position: relative;
				clear: both;
				width: 100%;
				height: 100px;
			}

			#gostart {
				position: absolute;
				z-index: 1001;
				bottom: 180px;
				left: 50%;
				width: 51px;
				height: 34px;
				margin-left: -25.5px;
				cursor: pointer;
				-webkit-animation: pulsatilla .7s ease-out infinite alternate running;
				animation: pulsatilla .7s ease-out infinite alternate running;
				background-image: url(/img/gostart.png);
				background-repeat: no-repeat;
				background-position: center center;
			}

			#gostart:hover {
				opacity: 1;
			}

			#gozero {
				position: fixed;
				z-index: 10000;
				right: 15px;
				bottom: -100px;
				width: 51px;
				height: 34px;
				cursor: pointer;
				-webkit-animation: gopuls .7s ease-out infinite alternate running;
				animation: gopuls .7s ease-out infinite alternate running;
				background-image: url(/smart/img/gozero.png);
				background-repeat: no-repeat;
				background-position: center center;
			}

			@keyframes gopuls {
				0% {
					opacity: 0;
				}

				70% {
					opacity: 1;
				}

				100% {
					opacity: 1;
				}
			}

			@-webkit-keyframes gopuls {
				0% {
					opacity: 0;
				}

				70% {
					opacity: 1;
				}

				100% {
					opacity: 1;
				}
			}

			@media (max-width: 992px) {
				#gozero {
					display: none;
				}
			}













			/******************************************** PARALLAX *******/

			.plx {
				position: relative;
				width: 100%;
				height: 300px;
				-webkit-transition: all .4s ease;
				transition: all .4s ease;
				background-repeat: no-repeat;
				background-attachment: fixed;
				background-size: cover;
			}

			.plx200 {
				height: 200px !important;
			}

			.plx400 {
				height: 400px !important;
			}

			.plx500 {
				height: 500px !important;
			}

			.plx600 {
				height: 600px !important;
			}

			.plxinfinity {
				height: auto !important;
			}

			.plxcb1 {
				background-image: url('/smart/img/cb1.jpg');
			}

			.plxcb2 {
				background-image: url('/smart/img/cb2.jpg');
			}

			.plxcb3 {
				background-image: url('/smart/img/cb3.jpg');
			}








			/*************** GRID *************************/

			.middle {
				position: relative;
				clear: both;
				width: 100%;
				max-width: 1800px;
				margin: 0 auto;
			}

			.m {
				position: relative;
				float: left;
			}

			.mbr {
				border-radius: 5px;
				overflow: hidden;
			}

			.m-x-0 {
				overflow: hidden !important;
				width: 0;
			}

			.m-x-1 {
				width: 8.3333333333%;
			}

			.m-x-2 {
				width: 16.666666667%;
			}

			.m-x-3 {
				width: 25%;
			}

			.m-x-4 {
				width: 33.33333333%;
			}

			.m-x-5 {
				width: 41.66666667%;
			}

			.m-x-6 {
				width: 50%;
			}

			.m-x-7 {
				width: 58.33333333%;
			}

			.m-x-8 {
				width: 66.66666667%;
			}

			.m-x-9 {
				width: 75%;
			}

			.m-x-10 {
				width: 83.33333333%;
			}

			.m-x-11 {
				width: 91.66666667%;
			}

			.m-x-12 {
				width: 100%;
			}

			.m-x-ha {
				height: auto !important;
				min-height: 0 !important;
			}

			.m-x-tl {
				text-align: left;
			}

			.m-x-tr {
				text-align: right;
			}

			.m-x-tc {
				text-align: center;
			}

			.m-x-tj {
				text-align: justify;
			}

			.m-x-h100 {
				height: 100px !important;
			}

			.m-x-h200 {
				height: 200px !important;
			}

			.m-x-h300 {
				height: 300px !important;
			}

			.m-x-h400 {
				height: 400px !important;
			}

			.m-x-h500 {
				height: 500px !important;
			}

			.m-x-h600 {
				height: auto !important;
				min-height: 600px !important;
			}

			.m-x-h800 {
				height: auto !important;
				min-height: 800px !important;
			}

			.m-x-h900 {
				height: auto !important;
				min-height: 900px !important;
			}

			.m-x-bl {
				border-left-width: 1px;
			}

			.m-x-br {
				border-right-width: 1px;
			}

			.m-x-bb {
				border-bottom-width: 1px;
			}

			.m-x-bt {
				border-top-width: 1px;
			}

			.m-x-nb {
				border: none !important;
			}

			.box,
			.box2,
			.box3,
			.box4,
			.box5,
			.box0 {
				position: relative;
				width: 100%;
				height: 100%;
				padding: 100px;
				-webkit-transition: background .4s ease;
				transition: background .4s ease;
			}

			.box2 {
				padding: 80px;
			}

			.box3 {
				padding: 60px;
			}

			.box4 {
				padding: 40px;
			}

			.box5 {
				padding: 20px;
			}

			.box6 {
				padding: 15px;
			}

			.box7 {
				padding: 3px 15px;
			}

			.box0 {
				padding: 0;
			}

			.col3,
			.col2 {
				-webkit-column-count: 3;
				-moz-column-count: 3;
				column-count: 3;
				-webkit-column-gap: 50px;
				-moz-column-gap: 50px;
				column-gap: 50px;
				-webkit-column-rule: 1px solid #ededed;
				-moz-column-rule: 1px solid #ededed;
				column-rule: 1px solid #ededed;
			}

			.col2 {
				-webkit-column-count: 2;
				-moz-column-count: 2;
				column-count: 2;
			}

			@media screen and (max-width: 1500px) {
				.box {

					padding: 100px;
				}

				.box2 {
					padding: 80px;
				}

				.box3 {
					padding: 60px;
				}

				.box4 {
					padding: 40px;
				}

				.box5 {
					padding: 20px;
				}

				.box0 {
					padding: 0;
				}
			}

			@media screen and (max-width: 1200px) {
				.box {

					padding: 80px;
				}

				.box2 {
					padding: 70px;
				}

				.box3 {
					padding: 60px;
				}

				.box4 {
					padding: 40px;
				}

				.box5 {
					padding: 20px;
				}

				.box0 {
					padding: 0;
				}
			}

			@media screen and (max-width: 992px) {
				.box {

					padding: 70px;
				}

				.box2 {
					padding: 60px;
				}

				.box3 {
					padding: 50px;
				}

				.box4 {
					padding: 40px;
				}

				.box5 {
					padding: 20px;
				}

				.box0 {
					padding: 0;
				}
			}


			@media screen and (max-width: 768px) {
				.box {

					padding: 60px;
				}

				.box2 {
					padding: 50px;
				}

				.box3 {
					padding: 40px;
				}

				.box4 {
					padding: 30px;
				}

				.box5 {
					padding: 20px;
				}

				.box0 {
					padding: 0;
				}
			}

			@media screen and (max-width: 576px) {

				.box,
				.box2,
				.box3,
				.box4,
				.box5 {
					padding: 25px 20px;
				}

				.box0 {
					padding: 0;
				}
			}







			/*************** PULSANTE ********************/

			a.pulsante {
				color: #fff;
			}

			.pulsante,
			.SW-submit {
				color: #FFF;
				font-size: 20px;
				position: relative;
				display: inline-block;
				overflow: hidden;
				padding: 0px 20px;
				height: 60px;
				cursor: pointer;
				-webkit-transition: all .3s ease-in;
				transition: all .3s ease-in;
				-webkit-transform: translateZ(0);
				transform: translateZ(0);
				vertical-align: middle;
				border: 1px solid <?=$colore2?>;
				background-color: <?=$colore2?>;
				border-radius: 5px;
				-webkit-backface-visibility: hidden;
				backface-visibility: hidden;
				-moz-osx-font-smoothing: grayscale;
				line-height: 60px;
			}

			.pulsante:before,
			.SW-submit:before {
				position: absolute;
				z-index: -1;
				right: 100%;
				bottom: 0;
				left: 0;
				height: 100%;
				content: '';
				-webkit-transition-timing-function: ease-out;
				transition-timing-function: ease-out;
				-webkit-transition-duration: .6s;
				transition-duration: .6s;
				-webkit-transition-property: right;
				transition-property: right;
				background: #fff;
			}

			.pulsante:hover,
			.SW-submit:hover,
			.pulsante:hover a:link,
			.pulsante:hover a:active,
			.pulsante:hover a:visited,
			.pulsante:hover a:hover {
				color: <?=$colore2?> !important;
				border: 1px solid <?=$colore2?>;
			}

			.SW-submit:hover:before,
			.pulsante:hover:before,
			.pulsante:focus:before,
			.pulsante:active:before {
				right: 0;
				-webkit-transition-duration: .1s;
				transition-duration: .1s;
			}















			/*************************STILE TABELLA SUITEWEB**/

			.suite_table {
				font-family: 'Lato', sans-serif;
				font-size: 15px;
				width: 100%;
				padding: 10px;
				border: 2px solid #999;
			}

			.suite_table td {
				padding: 8px;
			}

			.suite_col_pari {
				text-align: center;
				color: #615955;
			}

			.suite_col_dispari {
				text-align: center;
				color: #615955;
			}

			.suite_col_0 {
				font-size: 15px;
				font-weight: bold;
				color: #615955;
			}

			.suite_row_0 {
				font-weight: bold;
				text-align: center;
				color: #615955;
			}

			.suite_row_pari {
				text-align: center;
				background-color: #e6d6a4;
			}

			.suite_row_dispari {
				text-align: center;
			}








			/***************** PRIVACY ************/

			.privacy,
			.cookies {
				position: fixed;
				font-size: 14px;
				font-family: verdana;
				z-index: 10000000;
				bottom: 0;
				left: -120%;
				overflow: hidden;
				overflow-y: visible;
				width: 100%;
				height: 100%;
				padding: 50px;
				-webkit-transition: all .5s ease;
				-o-transition: all .5s ease;
				transition: all .5s ease;
				color: #666;
				background: rgba(255, 255, 255, 0.95);
			}

			.privacy #close,
			.cookies #close {
				position: absolute;
				top: 10px;
				right: 10px;
				cursor: pointer;
				color: #666;
			}

			.privacy h2,
			.privacy a,
			.cookies h2,
			.cookies a {
				color: #fff;
			}

			.privacybtn {
				cursor: pointer;
				transition: all .4s ease;
			}

			.privacybtn:hover {
				color: #000;
			}

			@media (max-width: 768px) {

				.privacy,
				.cookies {
					padding: 15px !important;
				}
			}








			/*************************STILE FORMS**/

			select[class*='ui-datepicker'] {
				padding: 5px;
				color: #000;
				background: inherit;
			}

			input,
			textarea,
			select {
				font-size: 14px;
				width: 100%;
				margin-top: 3px;
				padding: 10px;
				transition: all .4s ease;
				color: #666;
				border: 1px solid #AAA;
				border-radius: 5px;
				background: #ededed;
				border-bottom: 1px solid <?=$colore2?>;
				font-family: <?=$font?>
			}

			select {
				appearance: none;
				-moz-appearance: none;
				-webkit-appearance: none;
			}

			input[req] {
				border-bottom: 2px solid #f00;
			}

			input[req].error {
				border: 2px solid #f00;
			}

			@media (min-width: 576px) and (max-width: 992px) {

				.box input,
				.box select {
					width: calc(50% - 10px);
					margin: 5px;
				}
			}

			.box input:last-child {
				margin-bottom: none;
			}

			textarea {
				font-size: 15px;
				font-family: <?=$font?>;
				width: 100%;
				height: 80px;
				text-align: left;
			}

			.formprivacy,
			.forminvio {
				position: relative;
				float: left;
				width: 100%;
			}

			.responseForm {
				font-size: 20px;
				position: relative;
				float: left;
				width: 100%;
				padding: 2rem;
				text-align: center;
				color: #fff;
				background: rgba(166, 198, 219, 1);
			}

			@media (max-width: 768) {
				.responseForm {
					font-size: 16px;
					padding: 1rem;
					text-align: left;
				}
			}

			input:focus,
			textarea:focus,
			select:focus,
			select:focus option {
				background: <?=$colore1?>;
				color: #FFF;
			}

			::placeholder {
				color: #999;
			}

			::-moz-placeholder {
				opacity: 1;
			}

			.SW-submit {
				float: left;
				margin-top: 10px;
				text-transform: uppercase;
				color: #fff;
				background: transparent;
			}

			input[type='checkbox'],
			input[type='radio'] {
				max-width: 20px !important;
			}

			@media only screen and (max-width: 600px) {

				#rc-imageselect,
				.g-recaptcha {
					-webkit-transform: scale(.85);
					transform: scale(.85);
					-webkit-transform-origin: 0 0;
					transform-origin: 0 0;
				}
			}

			.modalDialog {
				font-family: 'Doris', sans-serif;
				position: fixed;
				z-index: 100000000000000000;
				top: 0;
				right: 0;
				bottom: 0;
				left: 0;
				box-sizing: border-box;
				width: 100%;
				padding: 10px;
				-webkit-transition: opacity 400ms ease-in;
				-moz-transition: opacity 400ms ease-in;
				transition: opacity 400ms ease-in;
				pointer-events: none;
				opacity: 0;
				background: rgba(0, 0, 0, .6);
			}

			.modalDialog>div {
				font-size: 17px;
				position: relative;
				z-index: 100000000000000000;
				box-sizing: border-box;
				width: 600px;
				margin: 15% auto;
				padding: 40px;
				border-radius: 4px;
				background: #fff;
				color: #666 !important;
			}

			.modalDialog>div a:link,
			.modalDialog>div a:active,
			.modalDialog>div a:visited {
				transition: all .4s ease;
				color: #666;
			}

			.modalDialog>div a:hover {
				color: #ffca00;
			}

			.close {
				font-weight: bold;
				line-height: 25px;
				position: absolute;
				top: -10px;
				right: -12px;
				width: 24px;
				cursor: pointer;
				text-align: center;
				text-decoration: none;
				color: #fff;
				-webkit-border-radius: 12px;
				-moz-border-radius: 12px;
				border-radius: 12px;
				background: #606061;
				-webkit-box-shadow: 1px 1px 3px #000;
				-moz-box-shadow: 1px 1px 3px #000;
				box-shadow: 1px 1px 3px #000;
			}

			.close:hover {
				background: #ffca00;
			}

			@media (max-width: 1000px) {
				.modalDialog>div {
					font-size: 15px;
					width: 100%;
				}

				.close {
					right: -2px;
				}
			}







			/***********************PHOTOGALLERY**/

			.FGcat {
				position: relative;
				z-index: 1;
				float: left;
				overflow: hidden;
				width: 32.9%;
				margin: .2%;
				-webkit-transition: all .3s;
				transition: all .3s;
				background-color: #e4dfd3;
			}

			.FGcat:hover {
				z-index: 10;
				background-color: #d8c5c5;
				box-shadow: 0 0 12px #666;
			}

			.FGcat-titolo {
				font-family: 'Open Sans';
				font-size: 20px;
				font-weight: 300;
				position: absolute;
				bottom: 5px;
				left: 5px;
				padding: 8px 18px;
				text-align: left;
				color: #666;
				background-color: rgba(255, 255, 255, .9);
			}

			.FGcat-titolo a:link,
			.FGcat-titolo a:active,
			.FGcat-titolo a:visited,
			.FGcat-titolo a:hover {
				color: #666;
			}





			/***************** GRID/TABLE *******/

			.row,
			.row-nb {
				width: 100%;
				display: table;
				table-layout: fixed;
				float: left;
				border-bottom: 2px solid #fff;
			}

			.row-nb {
				border-bottom: none;
			}

			.col,
			.col-nb,
			.col-vtop {
				display: table-cell;
				border-left: 2px solid #fff;
				padding: 25px;
				vertical-align: middle;
			}

			.col-vtop {
				vertical-align: top;
			}

			.col-nb {
				border-left: none;
			}

			.col img,
			.col-vtop img {
				width: 100%;
				height: auto;
			}

			@media (max-width: 1200px) {

				.col,
				.col-nb,
				.col-vtop {
					padding: 20px;
				}
			}

			@media (max-width: 576px) {

				.col,
				.col-nb,
				.col-vtop {
					padding: 15px;
					display: inherit;
				}

				.col-nb {
					border-left: 2px solid #fff;
				}
			}






			/***************************** MEDIA ***************************/

			@media screen and (max-width: 1500px) {
				.m-xl-0 {
					display: none !important;
					overflow: hidden !important;
					width: 0;
				}

				.m-xl-1 {
					width: 8.3333333333%;
				}

				.m-xl-2 {
					width: 16.666666667%;
				}

				.m-xl-3 {
					width: 25%;
				}

				.m-xl-4 {
					width: 33.33333333%;
				}

				.m-xl-5 {
					width: 41.66666667%;
				}

				.m-xl-6 {
					width: 50%;
				}

				.m-xl-7 {
					width: 58.33333333%;
				}

				.m-xl-8 {
					width: 66.66666667%;
				}

				.m-xl-9 {
					width: 75%;
				}

				.m-xl-10 {
					width: 83.33333333%;
				}

				.m-xl-11 {
					width: 91.66666667%;
				}

				.m-xl-12 {
					width: 100%;
				}

				.m-xl-ha {
					height: auto !important;
					min-height: 0 !important;
				}

				.m-xl-tl,
				.m-xl-tl p {
					text-align: left !important;
				}

				.m-xl-tr,
				.m-xl-tr p {
					text-align: right !important;
				}

				.m-xl-tc,
				.m-xl-tc p {
					text-align: center !important;
				}

				.m-xl-tj,
				.m-xl-tj p {
					text-align: justify !important;
				}

				.m-xl-h100 {
					height: 100px !important;
					min-height: 0 !important;
				}

				.m-xl-h200 {
					height: 200px !important;
					min-height: 0 !important;
				}

				.m-xl-h300 {
					height: 300px !important;
					min-height: 0 !important;
				}

				.m-xl-h400 {
					height: 400px !important;
					min-height: 0 !important;
				}

				.m-xl-h500 {
					height: 500px !important;
					min-height: 0 !important;
				}

				.m-xl-h600 {
					height: 600px !important;
					min-height: 0 !important;
				}

				.m-xl-nb {
					border: none !important;
				}

				.m-xl-bl {
					border-width: 0px !important;
					border-left-width: 1px !important;
				}

				.m-xl-br {
					border-width: 0px !important;
					border-right-width: 1px !important;
				}

				.m-xl-bb {
					border-width: 0px !important;
					border-bottom-width: 1px !important;
				}

				.m-xl-bt {
					border-width: 0px !important;
					border-top-width: 1px !important;
				}

				.col3 {
					-webkit-column-count: 2;
					-moz-column-count: 2;
					column-count: 2;
				}

				.m-xl-noalign {
					top: auto !important;
					left: auto !important;
					transform: translate(0, 0) !important;
				}
			}

			@media screen and (max-width: 1200px) {
				.m-l-0 {
					display: none !important;
					overflow: hidden !important;
					width: 0;
				}

				.m-l-1 {
					width: 8.3333333333%;
				}

				.m-l-2 {
					width: 16.666666667%;
				}

				.m-l-3 {
					width: 25%;
				}

				.m-l-4 {
					width: 33.33333333%;
				}

				.m-l-5 {
					width: 41.66666667%;
				}

				.m-l-6 {
					width: 50%;
				}

				.m-l-7 {
					width: 58.33333333%;
				}

				.m-l-8 {
					width: 66.66666667%;
				}

				.m-l-9 {
					width: 75%;
				}

				.m-l-10 {
					width: 83.33333333%;
				}

				.m-l-11 {
					width: 91.66666667%;
				}

				.m-l-12 {
					width: 100%;
				}

				.m-l-ha {
					height: auto !important;
					min-height: 0 !important;
				}

				.m-l-tl {
					text-align: left;
				}

				.m-l-tr {
					text-align: right;
				}

				.m-l-tc {
					text-align: center;
				}

				.m-l-tj {
					text-align: justify;
				}

				.m-l-h100 {
					height: 100px !important;
					min-height: 0 !important;
				}

				.m-l-h200 {
					height: 200px !important;
					min-height: 0 !important;
				}

				.m-l-h300 {
					height: 300px !important;
					min-height: 0 !important;
				}

				.m-l-h400 {
					height: 400px !important;
					min-height: 0 !important;
				}

				.m-l-h500 {
					height: 500px !important;
					min-height: 0 !important;
				}

				.m-l-h600 {
					height: 600px !important;
					min-height: 0 !important;
				}

				.m-l-nb {
					border: none !important;
				}

				.m-l-bl {
					border-width: 0px !important;
					border-left-width: 1px !important;
				}

				.m-l-br {
					border-width: 0px !important;
					border-right-width: 1px !important;
				}

				.m-l-bb {
					border-width: 0px !important;
					border-bottom-width: 1px !important;
				}

				.m-l-bt {
					border-width: 0px !important;
					border-top-width: 1px !important;
				}

				.col3 {
					-webkit-column-count: 2;
					-moz-column-count: 2;
					column-count: 2;
				}

				.m-l-noalign {
					top: auto !important;
					left: auto !important;
					transform: translate(0, 0) !important;
				}
			}

			@media screen and (max-width: 992px) {
				.ulrev {
					margin: 0;
					padding: 15px 0;
					padding-left: 20px;
					list-style-type: disc;
					text-align: left;
				}

				.ulrev li {
					padding-right: 0;
					background-image: none;
				}

				.plx {
					display: none;
					margin: 20px 0;
				}

				.m-m-0 {
					display: none !important;
					overflow: hidden !important;
					width: 0;
				}

				.m-m-1 {
					width: 8.3333333333%;
				}

				.m-m-2 {
					width: 16.666666667%;
				}

				.m-m-3 {
					width: 25%;
				}

				.m-m-4 {
					width: 33.33333333%;
				}

				.m-m-5 {
					width: 41.66666667%;
				}

				.m-m-6 {
					width: 50%;
				}

				.m-m-7 {
					width: 58.33333333%;
				}

				.m-m-8 {
					width: 66.66666667%;
				}

				.m-m-9 {
					width: 75%;
				}

				.m-m-10 {
					width: 83.33333333%;
				}

				.m-m-11 {
					width: 91.66666667%;
				}

				.m-m-12 {
					width: 100%;
				}

				.m-m-ha {
					height: auto !important;
					min-height: 0 !important;
				}

				.m-m-boxg .box {
					color: #fff;
					background: rgba(0, 0, 0, .4);
				}

				.m-m-tl {
					text-align: left;
				}

				.m-m-tr {
					text-align: right;
				}

				.m-m-tc {
					text-align: center;
				}

				.m-m-tj {
					text-align: justify;
				}

				.m-m-h50 {
					height: 50px !important;
					min-height: 0 !important;
				}

				.m-m-h100 {
					height: 100px !important;
					min-height: 0 !important;
				}

				.m-m-h200 {
					height: 200px !important;
					min-height: 0 !important;
				}

				.m-m-h300 {
					height: 300px !important;
					min-height: 0 !important;
				}

				.m-m-h400 {
					height: 400px !important;
					min-height: 0 !important;
				}

				.m-m-h500 {
					height: 500px !important;
					min-height: 0 !important;
				}

				.m-m-h600 {
					height: 600px !important;
					min-height: 0 !important;
				}

				.m-m-nb {
					border: none !important;
				}

				.m-m-bl {
					border-width: 0px !important;
					border-left-width: 1px !important;
				}

				.m-m-br {
					border-width: 0px !important;
					border-right-width: 1px !important;
				}

				.m-m-bb {
					border-width: 0px !important;
					border-bottom-width: 1px !important;
				}

				.m-m-bt {
					border-width: 0px !important;
					border-top-width: 1px !important;
				}

				.FGcat-titolo {
					font-size: 18px;
				}

				.m-m-noalign {
					top: auto !important;
					left: auto !important;
					transform: translate(0, 0) !important;
				}
			}

			@media screen and (max-width: 768px) {
				#imgslider {
					margin-top: 0 !important;
				}

				.m-s-0 {
					display: none !important;
					overflow: hidden !important;
					width: 0;
				}

				.m-s-1 {
					width: 8.3333333333%;
				}

				.m-s-2 {
					width: 16.666666667%;
				}

				.m-s-3 {
					width: 25%;
				}

				.m-s-4 {
					width: 33.33333333%;
				}

				.m-s-5 {
					width: 41.66666667%;
				}

				.m-s-6 {
					width: 50%;
				}

				.m-s-7 {
					width: 58.33333333%;
				}

				.m-s-8 {
					width: 66.66666667%;
				}

				.m-s-9 {
					width: 75%;
				}

				.m-s-10 {
					width: 83.33333333%;
				}

				.m-s-11 {
					width: 91.66666667%;
				}

				.m-s-12 {
					width: 100%;
				}

				.m-s-ha {
					height: auto !important;
					min-height: 0 !important;
				}

				.m-s-tl {
					text-align: left;
				}

				.m-s-tr {
					text-align: right;
				}

				.m-s-tc {
					text-align: center;
				}

				.m-s-tj {
					text-align: justify;
				}

				.m-s-h100 {
					height: 100px !important;
					min-height: 0 !important;
				}

				.m-s-h200 {
					height: 200px !important;
					min-height: 0 !important;
				}

				.m-s-h300 {
					height: 300px !important;
					min-height: 0 !important;
				}

				.m-s-h400 {
					height: 400px !important;
					min-height: 0 !important;
				}

				.m-s-h500 {
					height: 500px !important;
					min-height: 0 !important;
				}

				.m-s-h600 {
					height: 600px !important;
					min-height: 0 !important;
				}

				.m-s-nb {
					border: none !important;
				}

				.m-s-bl {
					border-width: 0px !important;
					border-left-width: 1px !important;
				}

				.m-s-br {
					border-width: 0px !important;
					border-right-width: 1px !important;
				}

				.m-s-bb {
					border-width: 0px !important;
					border-bottom-width: 1px !important;
				}

				.m-s-bt {
					border-width: 0px !important;
					border-top-width: 1px !important;
				}

				.col3,
				.col2 {
					-webkit-column-count: 1;
					-moz-column-count: 1;
					column-count: 1;
				}

				#SLOGAN {
					font-size: 30px;
				}

				.FGcat-titolo {
					font-size: 18px;
				}

				.m-s-noalign {
					top: auto !important;
					left: auto !important;
					transform: translate(0, 0) !important;
				}
			}

			@media screen and (max-width: 576px) {
				#imgslider {
					height: 500px;
					margin-top: 50px !important;
				}

				#gostart {
					bottom: 95px;
				}

				.m-xs-0 {
					display: none !important;
					overflow: hidden !important;
					width: 0;
				}

				.m-xs-1 {
					width: 8.3333333333%;
				}

				.m-xs-2 {
					width: 16.666666667%;
				}

				.m-xs-3 {
					width: 25%;
				}

				.m-xs-4 {
					width: 33.33333333%;
				}

				.m-xs-5 {
					width: 41.66666667%;
				}

				.m-xs-6 {
					width: 50%;
				}

				.m-xs-7 {
					width: 58.33333333%;
				}

				.m-xs-8 {
					width: 66.66666667%;
				}

				.m-xs-9 {
					width: 75%;
				}

				.m-xs-10 {
					width: 83.33333333%;
				}

				.m-xs-11 {
					width: 91.66666667%;
				}

				.m-xs-12 {
					width: 100%;
				}

				.m-xs-ha {
					height: auto !important;
					min-height: 0 !important;
				}

				.m-xs-tl {
					text-align: left;
				}

				.m-xs-tr {
					text-align: right;
				}

				.m-xs-tc {
					text-align: center;
				}

				.m-xs-tj {
					text-align: justify;
				}

				.m-xs-h100 {
					height: 100px !important;
					min-height: 0 !important;
				}

				.m-xs-h200 {
					height: 200px !important;
					min-height: 0 !important;
				}

				.m-xs-h300 {
					height: 300px !important;
					min-height: 0 !important;
				}

				.m-xs-h400 {
					height: 400px !important;
					min-height: 0 !important;
				}

				.m-xs-h500 {
					height: 500px !important;
					min-height: 0 !important;
				}

				.m-xs-h600 {
					height: 600px !important;
					min-height: 0 !important;
				}

				.m-xs-nb {
					border: none !important;
				}

				.m-xs-bl {
					border-width: 0px !important;
					border-left-width: 1px !important;
				}

				.m-xs-br {
					border-width: 0px !important;
					border-right-width: 1px !important;
				}

				.m-xs-bb {
					border-width: 0px !important;
					border-bottom-width: 1px !important;
				}

				.m-xs-bt {
					border-width: 0px !important;
					border-top-width: 1px !important;
				}

				#SLOGAN {
					font-size: 20px;
				}

				.m-xs-noalign {
					top: auto !important;
					left: auto !important;
					transform: translate(0, 0) !important;
				}
			}

			#menu {
				top: 0;
				position: fixed;
				height: 100px;
				width: 100%;
				background-color: #FFF;
				border-bottom: 2px solid <?=$colore1?>;
				z-index: 10000;
			}

			#menu.scrolled {
				height: 60px;
				box-shadow: 0 0 40px #333;
			}

			.menu {
				position: relative;
				max-width: 1500px;
				margin: 0 auto;
				height: 100%;
				text-align: right;
				padding: 20px 10px;
			}

			#menu.scrolled .menu {
				padding: 5px 10px;
			}

			.menu .logo {
				position: absolute;
				left: 0;
				top: 50%;
				transform: translateY(-50%);
				height: 80%;
			}

			#menu.scrolled .logo {
				height: 90%
			}

			.vm {
				position: relative;
				font-size: 15px;
				cursor: pointer;
				padding: 0px 10px;
				transition: all .6s ease;
				display: inline-block;
				border-radius: 5px;
				vertical-align: top;
				height: 60px;
				line-height: 60px;
			}

			.vm.telefono {
				border: 1px dotted <?=$colore2?>;
				font-size: 20px;
				padding: 0px 20px;
				height: 60px;
				line-height: 60px;
			}

			.vm.preno {
				border: 1px solid <?=$colore2?>;
				font-size: 20px;
				padding: 0px 20px;
				height: 60px;
				line-height: 60px;
				background-color: <?=$colore2?>;
				color: #FFF;
			}

			.vm.preno:hover {
				-webkit-animation: shadowpulsing .3s ease-out infinite alternate running;
				animation: shadowpulsing .3s ease-out infinite alternate running;
			}

			.vm:hover {
				color: #FFF;
				transition: all .1s ease;
				background-color: <?=$colore2?>;
			}

			#menu.scrolled .vm {
				height: 50px;
				line-height: 50px;
			}


			@keyframes shadowpulsing {
				0% {
					box-shadow: 0 0 10px #000;
				}

				100% {
					box-shadow: 0 0 2px #FFF;
				}
			}

			@-webkit-keyframes shadowpulsing {
				0% {
					box-shadow: 0 0 10px #000;
				}

				100% {
					box-shadow: 0 0 2px #FFF;
				}
			}

			.boxquoto {
				position: relative;
				margin: 20px auto 0px auto;
				width: 100%;
				max-width: 1500px;
				border-radius: 5px;
				background-color: #FFF;

				border-bottom: 2px solid <?=$colore2?>;
			}

			.boxquoto:hover {

				border-bottom: 2px solid #FFF;
				box-shadow: 0 0 15px #333;
			}

			#owner {
				position: absolute !important;
				bottom: -10px;
				right: 20px;
				width: 120px;
				height: 120px;
			}

			#owner .img {
				position: absolute !important;
				width: 100%;
				height: 100%;
				top: 0;
				left: 0;
				z-index: 1;
				border-radius: 100%;
				z-index: 10;
				border: 4px solid <?=$colore1?>;
			}

			#owner .riga1 {
				position: absolute;
				white-space: nowrap;
				right: 130px;
				z-index: 2;
				top: 20px;
			}

			#owner .riga2 {
				position: absolute;
				white-space: nowrap;
				right: 130px;
				z-index: 2;
				top: 50px;
			}

			footer {
				margin-top: 20px;
				width: 100%;
				border-top: 2px solid <?=$colore1?>;
				background-color: #FFF;
			}

			.footer {
				width: 100%;
				position: relative;
				max-width: 1500px;
				margin: 0 auto;
				height: auto;
			}

			.footer .logo {
				position: relative;
				left: 0;
				width: auto;
				max-width: 300px;
				max-height: 100px;
				display: inline-block;
			}

			.footer .indirizzo {
				position: relative;
				display: inline-block;
			}

			.bollino {
				position: absolute;
				background-color: #DDDDDD;
				color: <?=$colore2?>;
				font-size: 23px;
				width: 60px;
				height: 60px;
				border-radius: 60px;
				text-align: center;
				top: 50%;
				transform: translateY(-50%);
				right: -80px;
				padding-top: 15px;
				cursor: pointer;
			}






			/****************************************************/

			@media screen and (max-width: 1500px) {
				.vm {
					font-size: 14px;
					padding: 0px 8px;
				}

				.vm.telefono {
					font-size: 16px;
					padding: 0px 10px;
				}

				.vm.preno {
					font-size: 16px;
					padding: 0px 10px;
				}
			}

			@media screen and (max-width: 1200px) {
				.menu .logo {
					height: 50%;
				}

				.vm {
					font-size: 13px;
					padding: 0px 5px;
				}

				.vm.telefono {
					font-size: 14px;
					padding: 0px 10px;
				}

				.vm.preno {
					font-size: 14px;
					padding: 0px 10px;
				}
			}

			@media screen and (max-width: 992px) {
				#start {
					height: 60px;
				}

				#menu {
					display: none !important;
				}

				#menumb {
					display: block !important;
				}

				#owner {
					position: relative !important;
					bottom: 0px;
					right: auto;
					width: 120px;
					height: 120px;
					width: 100%;
					border-bottom: 1px solid #FFF;
				}

				#owner .img {
					position: absolute !important;
					width: 100px;
					height: 100px;
					top: 10px;
					left: 10px;
					z-index: 1;
					border-radius: 100%;
					z-index: 10;
					border: 4px solid <?=$colore1?>;
				}

				#owner .riga1 {
					position: absolute;
					white-space: nowrap;
					right: auto;
					left: 120px;
					z-index: 2;
					top: 40px;
					color: #FFF !important;
				}

				#owner .riga2 {
					position: absolute;
					white-space: nowrap;
					right: auto;
					left: 120px;
					z-index: 2;
					top: 60px;
				}
			}

			@media screen and (max-width: 768px) {}

			@media screen and (max-width: 576px) {}

			.centered-btns_nav {
				z-index: 3;
				position: absolute;
				-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
				top: 50%;
				left: 0;
				opacity: 0.7;
				text-indent: -9999px;
				overflow: hidden;
				text-decoration: none;
				height: 61px;
				width: 38px;
				background: transparent url("/img/arrow.png") no-repeat left top;
				margin-top: -45px;
			}

			.centered-btns_nav:active {
				opacity: 1.0;
			}

			.centered-btns_nav.next {
				left: auto;
				background-position: right top;
				right: 0;
			}

			.transparent-btns_nav {
				z-index: 3;
				position: absolute;
				-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
				top: 0;
				left: 0;
				display: block;
				background: #fff;
				/* Fix for IE6-9 */
				opacity: 0;
				filter: alpha(opacity=1);
				width: 48%;
				text-indent: -9999px;
				overflow: hidden;
				height: 91%;
			}

			.transparent-btns_nav.next {
				left: auto;
				right: 0;
			}

			.large-btns_nav {
				z-index: 3;
				position: absolute;
				-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
				opacity: 0.6;
				text-indent: -9999px;
				overflow: hidden;
				top: 0;
				bottom: 0;
				left: 0;
				background: #000 url("/img/arrow.png") no-repeat left 50%;
				width: 38px;
			}

			.large-btns_nav:active {
				opacity: 1.0;
			}

			.large-btns_nav.next {
				left: auto;
				background-position: right 50%;
				right: 0;
			}

			.centered-btns_nav:focus,
			.transparent-btns_nav:focus,
			.large-btns_nav:focus {
				outline: none;
			}

			.centered-btns_tabs,
			.transparent-btns_tabs,
			.large-btns_tabs {
				margin-top: 10px;
				text-align: center;
			}

			.centered-btns_tabs li,
			.transparent-btns_tabs li,
			.large-btns_tabs li {
				display: inline;
				float: none;
				_float: left;
				*float: left;
				margin-right: 5px;
			}

			.centered-btns_tabs a,
			.transparent-btns_tabs a,
			.large-btns_tabs a {
				text-indent: -9999px;
				overflow: hidden;
				-webkit-border-radius: 15px;
				-moz-border-radius: 15px;
				border-radius: 15px;
				background: #ccc;
				background: rgba(0, 0, 0, .2);
				display: inline-block;
				_display: block;
				*display: block;
				-webkit-box-shadow: inset 0 0 2px 0 rgba(0, 0, 0, .3);
				-moz-box-shadow: inset 0 0 2px 0 rgba(0, 0, 0, .3);
				box-shadow: inset 0 0 2px 0 rgba(0, 0, 0, .3);
				width: 9px;
				height: 9px;
			}

			.centered-btns_here a,
			.transparent-btns_here a,
			.large-btns_here a {
				background: #222;
				background: rgba(0, 0, 0, .8);
			}

			#map-container {
				width: 100%;
				height: 650px;
			}

			.LineHeight18 {
				line-height: 1.8;
			}

			.img-responsive {
				display: block;
				max-width: 100%;
				height: auto;
			}

			.img_gallery {
				display: inline-block !important;
				width: calc(33% - 10px) !important;
				margin: 5px !important;
			}

			@media screen and (min-width: 480px) and (max-width: 768px) {
				.img_gallery {
					display: inline-block !important;
					width: calc(49% - 10px) !important;
					margin: 5px !important;
				}
			}

			@media screen and (min-width: 380px) and (max-width: 479px) {
				.img_gallery {
					display: inline-block !important;
					width: calc(99% - 10px) !important;
					margin: 5px !important;
				}
			}

			.text-aqua {
				color: #00c0ef !important;
			}

			.text-blue {
				color: #0073b7 !important;
			}

			.text-black {
				color: #111 !important;
			}

			.text-light-blue {
				color: #3c8dbc !important;
			}

			.text-orange {
				color: #ff851b !important;
			}

			.text-yellow {
				color: #f39c12 !important;
			}

			.ocontent {
				position: relative;
				display: inline-block;
				font-size: 13px;
				font-weight: 700;
				cursor: pointer;
				background-color: #ededed;
				-webkit-border-bottom-right-radius: 5px;
				-webkit-border-bottom-left-radius: 5px;
				-moz-border-radius-bottomright: 5px;
				-moz-border-radius-bottomleft: 5px;
				border-bottom-right-radius: 5px;
				border-bottom-left-radius: 5px;
			}

			.iconaDimension {
				position: relative !important;
				width: 32px !important;
				height: 32px !important;
				top: 0px !important;
			}

			.pad-left {
				padding-left: 10px !important;
			}

			.bg-transparent {
				background: transparent !important;
				background-color: transparent !important;
			}

			.small-padding {
				padding: 2px !important;
			}

			.no_border {
				border: 0px !important;
			}

			.tabella_servizi {
				width: 50% !important;
				border: 1px solid <?=$colore1?>;
				!important;
				float: right !important;
				padding-right: 2px !important;
				
			}

			.nowrap {
				white-space: nowrap !important;
			}

			.boxservizi {
				padding: 10px;
				height: auto;
			}
			.boxservizi.prezzo{
				white-space:nowrap;
			}
			.boxservizi.titolo{
				padding-left:40px;
			}
			.rigaservizi{
				position: relative;
				margin: 5px 8px 0px 5px;
				border-radius: 5px;
				background-color:#e7e7e7;
				box-sizing: border-box !important;
				width: calc(100% - 10px);
				min-height:30px;

			}
			.iconaservizi{
				position:absolute !important;
				top:50%!important;
				transform:translateY(-50%)!important;
				width:30px !important;
				left:5px!important;
				height:auto !important;
			}

			@media screen and (max-width: 1500px) {}

			@media screen and (max-width: 1200px) {}

			@media screen and (max-width: 992px) {
				.boxservizi {}
			}

			@media screen and (max-width: 768px) {
				.rigaservizi{
					padding:8px 5px;
				}
				.boxservizi {
				padding: 1px 8px;
				height: auto;
			}
			.boxservizi.prezzo{
				border-top:1px dotted #999;
				width:100%;
				padding-top:4px;
			}
			}

			@media screen and (max-width: 576px) {}

		</style>
		
        <script src="{{ asset('smart/js/_alert.js')}}"></script>

        <link href="{{ asset('smart/css/_alert.css')}}" rel="stylesheet" />
		
		<script language="javascript">
        function controlla()
        {          
          var d = window.document.reg
          var title_error = "ERRORE! Impossibile inviare il modulo! \n\n"
          var error = ""

          <?=$valori_ctrl_script?>

          if (error) {
           alert(title_error + error); 
           return(false);
          } else {
            return(true);
          }                   
        }
    </script>
	<?=$head_tagmanager?>   
	</head>
	<body>
		<?=$body_tagmanager?>

        @include('smart_template/include/inc_MENU_QUEST')

		<div id="start"></div>

        @include('smart_template/include/inc_QUESTIONARIO')

	@if($tot_cs > 0)
		<div class="m m-x-3 m-x-tr m-m-6 m-xs-12 m-xs-tc">
			<div class="box4">
				<?=$logo?>
			</div>
		</div>
		<div class="m m-x-9 m-x-tl m-m-6 m-xs-12 m-xs-tc">
			<div class="box4 t14 w400 twhite">
				<b><?=$NomeCliente?></b><br> <?=$Indirizzo?> <?=$Localita?> - <?=$Cap?> (<?=$Provincia?>)
				<br><?=$SitoWeb?>
				 <div>
			                        <?=$Facebook?>
                                     <?=$Twitter?>
                                     <?=$GooglePlus?>
                                     <?=$Instagram?>
                                     <?=$Linkedin?>
                                     <?=$Pinterest?> 
                </div>	
			</div>
		</div>
	@else
			@include('smart_template/include/inc_FOOTER')
	@endif
    <script src="{{asset('smart/js/main.js')}}"></script>

	</body>
</html>