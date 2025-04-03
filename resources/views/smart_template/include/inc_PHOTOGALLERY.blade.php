<div class="boxquoto" id="photogallery">
	<div class="box6">
		<h3>PHOTOGALLERY</h3>
	</div>
	<?php
    $box     ='photogallery'; //ID del box contenitore
    $frase1  = dizionario('VISUALIZZA'). ' Photogallery';
    $frase2  = dizionario('NASCONDI'). ' Photogallery';
    $bollino ='<i class="fal fa-camera-alt"></i>'; //font awesome di riferimento
    $oc      ="0";//1 aperto - 0 chiuso
	?>
  @include('smart_template/include/inc_OC')
	<div class="box6 t14 content">
		<div class="m m-x-12">
			<?=$carosello?>
		</div>
		<div class="ca"></div>
	</div>
</div>
<script>

function openModal() {
  document.getElementById('myModal').style.display = "block";
}

function closeModal() {
  document.getElementById('myModal').style.display = "none";
}

var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("demo");
  var captionText = document.getElementById("caption");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
  captionText.innerHTML = dots[slideIndex-1].alt;
}	
</script>
<style>
/* gallery*/
/* The Modal (background) */
.modal {
  display: none;
  position: fixed;
  z-index: 9999999999999999;
  padding-top: 100px;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: black;
}

/* Modal Content */
.modal-content {
  position: relative;
  background-color: #fefefe;
  margin: auto;
  padding: 0;
  width: 90%;
  max-width: 1200px;
}

/* The Close Button */
.close {
  color: white;
  position: absolute;
  top: 10px;
  right: 25px;
  font-size: 35px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #999;
  text-decoration: none;
  cursor: pointer;
}

.mySlides {
  display: none;
}

.cursor {
  cursor: pointer
}

/* Next & previous buttons */
.prev,
.next {
  cursor: pointer;
  position: absolute;
  top: 50%;
  width: auto;
  padding: 16px;
  margin-top: -50px;
  color: white;
  font-weight: bold;
  font-size: 20px;
  transition: 0.6s ease;
  border-radius: 0 3px 3px 0;
  user-select: none;
  -webkit-user-select: none;
}

/* Position the "next button" to the right */
.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}

/* On hover, add a black background color with a little bit see-through */
.prev:hover,
.next:hover {
  background-color: rgba(0, 0, 0, 0.8);
}

/* Number text (1/3 etc) */
.numbertext {
  color: #f2f2f2;
  font-size: 12px;
  padding: 8px 12px;
  position: absolute;
  top: 0;
}

img {
  margin-bottom: -4px;
}

.caption-container {
  text-align: center;
  background-color: black;
  padding: 2px 16px;
  color: white;
}

.demo {
  opacity: 0.6;
}

.active,
.demo:hover {
  opacity: 1;
}

img.hover-shadow {
  transition: 0.3s;
  cursor:pointer;
}

.hover-shadow:hover {
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19)
}
/*fine gallery*/
.pgtmb{
	vertical-align: top;
    overflow: hidden;
    box-sizing: border-box;
    transition: all .6s ease;
    z-index: 1;
}
.pgtmb:hover{
	transform: scale(1.2, 1.2);
	transition: all .1s ease;
	z-index: 2;
	box-shadow: 0 0 20px #000;
	border: 4px solid #FFF;
}
.pgtmb::after{
	position: absolute;
	content: '';
	width: 100%;
	height: 0%;
	top: 0;
	left: 0;
	background-color: rgba(0,0,0,0.6);
	z-index: 5;
	opacity: 0;
	transition: all .6s ease
}
.pgtmb:hover::after{
	opacity: 1;
	transition: all .6s ease;
	height: 100%;
}
.pgtmb img{
transition: all .6s ease;	
}
.pgtmb:hover img{
	transform: scale(1.4, 1.4) rotate(12deg);
	transition: all .1s ease;

}
.pgtmb svg{
	position: absolute;
	top: 100%;
	left: 50%;
	color: #FFF;
	transform: translate(-50%, -50%);
	font-size: 45px;
	z-index: 10;
	opacity: 0;
	transition: all .6s ease;
}
.pgtmb:hover svg{
	opacity: 1;
	transition: all .6s ease;
	top: 50%;

}
</style>