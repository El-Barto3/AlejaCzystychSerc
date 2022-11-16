$(document).ready(function() {
  const imgSource = $('#profilepic').attr("src");
  const videoSource = $.trim(imgSource.substr(0, imgSource.lastIndexOf('.')) + "-video.mp4");
  $.get(videoSource)
    .done(function() { $('#resurrect').on('click', function() { changeElement(imgSource, videoSource); }) })
    .fail(function() { $('#resurrect').remove();  $('#resurrect-div').remove(); })
})

const changeElement = (imgSource, videoSource) => {
    if ($('#profilepic').length) {
      let fake = document.createElement('video');
      fake.setAttribute("id", "deepfake");
      fake.setAttribute("src", videoSource);
      fake.setAttribute("autoplay", "true");
      fake.setAttribute("loop", "true");
      fake.classList.add('img-fluid', 'rounded-circle', 'shadow-lg', 'p-1', 'mb-3', 'mt-3', 'bg-warning', 'rounded', 'picture', 'picture_size');

      $("#profilepic").replaceWith(fake);
    }
    else if ($('#deepfake').length) {
      let image = document.createElement('img');
      image.setAttribute("id", "profilepic");
      image.setAttribute("src", imgSource);
      image.classList.add('img-fluid', 'rounded-circle', 'shadow-lg', 'p-1', 'mb-3', 'mt-3', 'bg-warning', 'rounded', 'picture', 'picture_size');

      $("#deepfake").replaceWith(image);
    }
}