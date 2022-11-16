$(document).ready(function() {
  resizeMap();
  $(window).on("resize", function() { resizeMap(); })
});

function resizeMap() {
  if ($(window).width() > 1337) $('#mapa').css("margin-left", ($(window).width() - 1337)/2 + "px");
  if ($(window).height() > 433) $('#mapa').css("margin-top", ($(window).height() - 433)/4 + "px");
}

// ### Informacje o koordynatach punktu na mapie ### //
function getInfo(e, elmId) {
  var elementMouseIsOver = document.elementFromPoint(e.pageX, e.pageY);
  if (elementMouseIsOver == null) return false;
  var realX = e.pageX;
  var realY = e.pageY;

  var offset = $('#' + elmId).offset();
  var imgX = Math.round((realX - offset.left));
  var imgY = Math.round((realY - offset.top));

  $('#point-info').html("Informacje o wybranym punkcie: </br>" +
    "Koordynat X (na stronie): " + realX + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + "Koordynat X (na mapie): " + imgX + "</br>" +
    "Koordynat Y (na stronie): " + realY + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + "Koordynat Y (na mapie): " + imgY + "</br></br>" +
    "W przyszłości opcja posłuży do wchodzenia w interakcję z nagrobkami na mapie.");
  /*
  $('#coordinates').html('coordinates: ' + 'x: ' + realX + ' y : ' + realY);
  $('#img-coordinates').html('coordinates-img: ' + '\'' + imgX + '|' + imgY + "\'");
  $('#type').html('type: ' + elementMouseIsOver);
  */
}

// ### Wybranie miejsca do przeniesienia sie na mapie ### //
var lock = 0;

function soundChange() {
  if (lock == 0) {
    lock = 1;
    $('#soundChange').html("🔊 Włącz dźwięk 🔊");
  } else if (lock == 1) {
    lock = 0;
    $('#soundChange').html("🔇 Wyłącz dźwięk 🔇");
  }
}

function choseLoc() {
  if ($('.selection-mark')) $('.selection-mark').remove();
  $('#drogi-glow').off();


  var mapa = document.getElementById("mapa-cmentarza");
  var drogi = document.getElementById("drogi-glow");

  $('#drogi-glow').on("mouseenter", function(e) {
    $('#drogi-glow').css("cursor", "url('images/mark.png') 15 25, auto");
  });
  $('#drogi-glow').on("mouseleave", function(e) {
    $('#drogi-glow').css("cursor", "auto");
  });

  drogi.addEventListener("click", function(e) {
    var rem = document.getElementsByClassName('sektor');
    while (rem[0]) rem[0].parentNode.removeChild(rem[0]);
    var realX, realY, part;
    var offset = $('#' + drogi.id).offset();
    var x = (e.pageX - offset.left);
    var y = (e.pageY - offset.top);

    // Okreslenie obszaru - duzy x w lewo, ujemny w prawo - duzy y w dol, maly do gory
    if (x > 0 && x < 267) part = 1;
    if (x > 267 && x < 534) part = 2;
    if (x > 534 && x < 801) part = 3;
    if (x > 801 && x < 1068) part = 4;
    if (x > 1058 && x < 1337) part = 5;

    switch (part) {
      case 1:
        if (x < 133) realX = 1200;
        else realX = 900;

        if (y > 0 && y < 144) realY = 250;
        else if (y > 144 && y < 288) realY = 0;
        else realY = -250;
        break;

      case 2:
        if (x < 400) realX = 675;
        else realX = 450;

        if (y > 0 && y < 144) realY = 250;
        else if (y > 144 && y < 288) realY = 0;
        else realY = -250;
        break;

      case 3:
        if (x < 623) realX = 337;
        else if (x > 712) realX = -337;
        else realX = 0;

        if (y > 0 && y < 144) realY = 250;
        else if (y > 144 && y < 288) realY = 0;
        else realY = -250;
        break;

      case 4:
        if (x > 935) realX = -675;
        else realX = -450;

        if (y > 0 && y < 144) realY = 250;
        else if (y > 144 && y < 288) realY = 0;
        else realY = -250;
        break;

      case 5:
        if (x > 1200) realX = -1200;
        else realX = -900;

        if (y > 0 && y < 144) realY = 250;
        else if (y > 144 && y < 288) realY = 0;
        else realY = -250;
        break;

      default:
        realX = 0;
        realY = 0;
        break;
    }

    $('*').css("cursor", "none");
    drogi.style.filter = "blur(2px)";
    drogi.style.transform = "translate(" + realX + "px, " + realY + "px) scale(3, 3)";
    mapa.style.filter = "blur(2px)";
    mapa.style.transform = "translate(" + realX + "px, " + realY + "px) scale(3, 3)";
    if (lock == 0) new Audio("sounds/woosh-sound.mp3").play();

    setTimeout(function() {
      document.write("<a href='./index.html' style='font-size: 50px; margin-top: 150px; color: black; text-align: center; text-decoration: none; display: block;'>Tutaj pojawi się zdjęcie w 360 stopniach! :) </br> Kliknij, aby wrócić do mapy.</a>");
    }, 900);
  });
}

// ### Efekt rozmycia mapy ### //
function blurMap() {
  var mapa = document.getElementById("mapa-cmentarza");
  if (mapa.style.filter == "blur(3px)") mapa.style.filter = "blur(0px)";
  else mapa.style.filter = "blur(3px)";
  for (var j, i = 0; i < 46; i++) {
    if (i + 1 < 35) j = i + 1;
    else if (i + 1 > 34 && i + 1 < 38) j = 35;
    else if (i + 1 > 37 && i + 1 < 46) j = i - 1;
    else j = i - 2;
    displaySektor(i + 1, j);
  }
}

// ### Działania na sektorach ### //
function displaySektor(ktory, tresc) {
  if ($('.selection-mark')) $('.selection-mark').remove();
  $('#drogi-glow').off();

  // Tworzenie sektora jesli nie istnieje
  if (!document.getElementById(ktory + "-sektor")) {
    var x, y;
    var sektor = document.createElement('div');
    var jakiSektor = sektory[Object.keys(sektory)[ktory - 1]];
    var charSektor = jakiSektor.indexOf("|");
    sektor.classList.add("sektor");
    sektor.id = ktory + "-sektor";
    x = jakiSektor.substring(0, charSektor);
    y = jakiSektor.substring(charSektor + 1, jakiSektor.length);
    // dodanie zera przed pojedyncza liczba
    if (tresc < 10) sektor.innerHTML = "0" + tresc;
    else sektor.innerHTML = tresc;

    sektor.style.left = (x - 8) + "px";
    sektor.style.top = (y - 15) + "px";
    document.getElementById("sector-container").appendChild(sektor);
    $('.sektor').attr("draggable", false);
    // Za szybko kod sie wykonywal i potrzebowal opoznienia
    setTimeout(function() {
      $("#" + ktory + "-sektor").css("opacity", 1);
    }, 10);
    return;
  }
  // Pojawianie/znikanie sektorow
  if (document.getElementById("mapa-cmentarza").style.filter == "blur(3px)") $("#" + ktory + "-sektor").css("opacity", 1);
  if (document.getElementById("mapa-cmentarza").style.filter == "blur(0px)") $("#" + ktory + "-sektor").css("opacity", 0);
}

// ### Umieszczenie punktu  + informacje ### //
function putMark() {
  $('#drogi-glow').off();
  if ($('.selection-mark')) $('.selection-mark').remove();
  $('#putMark').attr("disabled", true);


  var drogi = $('#drogi-glow');
  var img = document.createElement('img');
  img.src = "images/mark.png";
  img.classList.add("selection-mark");

  drogi.on("click", function(e) {
    drogi.off("click");
    var offset = $('#' + drogi.attr("id")).offset();
    var x = e.pageX;
    var y = e.pageY;
    img.style.position = "absolute";
    img.style.left = (x - offset.left - 16) + "px";
    img.style.top = (y - offset.top - 30) + "px";
    $("#mapa")[0].appendChild(img);
    $('.selection-mark').attr("draggable", false);
    $('#putMark').attr("disabled", false);
    getInfo(e, $('#drogi-glow').attr("id"));
    if (lock == 0) new Audio("sounds/mark-sound.mp3").play();
  });
}
