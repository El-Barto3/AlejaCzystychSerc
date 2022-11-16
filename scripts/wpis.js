// JavaScript Document

$(document).ready(function () {

  var current_fs, next_fs, previous_fs; //fieldsets
  var opacity;
  var current = 1;
  var steps = $("fieldset").length;

  setProgressBar(current);

  $(".next").click(function () {

    current_fs = $(this).parent();
    next_fs = $(this).parent().next();

    //Add Class Active
    $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

    //show the next fieldset
    next_fs.show();
    //hide the current fieldset with style
    current_fs.animate({
      opacity: 0
    }, {
      step: function (now) {
        // for making fielset appear animation
        opacity = 1 - now;

        current_fs.css({
          'display': 'none',
          'position': 'relative'
        });
        next_fs.css({
          'opacity': opacity
        });
      },
      duration: 500
    });
    setProgressBar(++current);
  });

  $(".previous").click(function () {

    current_fs = $(this).parent();
    previous_fs = $(this).parent().prev();

    //Remove class active
    $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

    //show the previous fieldset
    previous_fs.show();

    //hide the current fieldset with style
    current_fs.animate({
      opacity: 0
    }, {
      step: function (now) {
        // for making fielset appear animation
        opacity = 1 - now;

        current_fs.css({
          'display': 'none',
          'position': 'relative'
        });
        previous_fs.css({
          'opacity': opacity
        });
      },
      duration: 500
    });
    setProgressBar(--current);
  });

  function setProgressBar(curStep) {
    var percent = parseFloat(100 / steps) * curStep;
    percent = percent.toFixed();
    $(".progress-bar")
      .css("width", percent + "%")
  }

  const allTags = document.getElementsByTagName("td");
  for (var i = 0; i < allTags.length; i++) {
    if (allTags[i].innerHTML == "") allTags[i].innerHTML = ("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-");
  }


  $(".submit").click(function () {
    return false;
  })

});


// Blokowanie przyciskow


/*

Smietnisko (pozniej usune)


        function btnActivation(){

            if(!document.getElementById('myText').value.length){
                document.getElementById("entryButtonOne").disabled = true;            
            }else{
                document.getElementById("entryButtonOne").disabled = false;

            }           
        }   

*/


/*

$(document).ready(function() {
	
    $('#msform > input').keyup(function() {
	
        var empty = false;
        $('form > input.cant_be_empty').each(function() {
            if ($(this).val() == '') {
                empty = true;
            }
        });

        if (empty) {
            $('#entryButtonOne').attr('disabled', true);
        } else {
            $('#entryButtonOne').attr('disabled', false);
        }
    });
});




$(document).ready(function(){
	

       
	
    $('#entryButtonOne').attr('disabled',true);
   
	 $('#eventName').keyup(function(){
        if($(this).val().length !=0){
				
            $('#entryButtonOne').attr('disabled', false);
        }
        else
        {

            $('#entryButtonOne').attr('disabled', true);        
        }
    })
	
}); 
*/


//entry_event

function compInput() {


  var empty = false;
  $('#msform > #jeden > #dwa > input.compInput').each(function () {
    if ($(this).val() == '') {
      empty = true;

    }
  });

  if (empty) {
    $('#eventButtonOne').attr('disabled', 'disabled');
  } else {
    $('#eventButtonOne').removeAttr('disabled');
  }

}


$(document).ready(function () {


  $('#msform > #jeden > #dwa > input').keyup(function () {

    compInput();

  });

  $('#msform > #jeden > #dwa > input').change(function () {

    compInput();

  });

});


// zdjecie

$(document).on('change', '.file-input', function () {


  var filesCount = $(this)[0].files.length;

  var textbox = $(this).prev();

  if (filesCount === 1) {
    var fileName = $(this).val().split('\\').pop();
    textbox.text(fileName);
  } else {
    textbox.text(filesCount + ' wybranych plików');
  }


  if (typeof (FileReader) != "undefined") {
    var dvPreview = $("#divImageMediaPreview");
    dvPreview.html("");
    $($(this)[0].files).each(function () {
      var file = $(this);
      var reader = new FileReader();
      reader.onload = function (e) {
        var img = $("<img />");
        img.attr("style", "width: 100%; auto; padding: 10px; border: 3px solid #ecb21f; border-radius: 5%; margin-top: 10px;");
        img.attr("src", e.target.result);
		
        dvPreview.append(img);
      }
      reader.readAsDataURL(file[0]);
    });
  } else {
    alert("Ta przeglądarka nie wspiera HTML5 FileReader.");
  }


});
