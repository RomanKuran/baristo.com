var cl = null;
var statusburger = false;
$(document).ready(function () {
    var x = document.getElementById("myTopnav");
    cl = x.className;
});

$("#burger").click(function(e) {
  if (statusburger) {
    $('#myTopnav').slideToggle(150); //Hide
    statusburger = false;
  } else {
    $('#myTopnav').slideToggle(150); //show
    $("#myTopnav").css("display", "flex");
    statusburger = true;
  }
});

$(document).mouseup(function (e){
  var x = $("#burger");
  if (!x.is(e.target) && x.has(e.target).length === 0 && statusburger) { 
    $('#myTopnav').slideToggle(150); //Hide
    statusburger = false;
  }
});