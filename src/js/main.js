$(function(){

  var search = $('#search');
  var searchVoice = $('.searchVoice');
  var searchBtn = $('.searchBtn');
  var searchLabel = $('.searchLabel');

  $('.navbarMenu').on('click', function() {
    $('.navMenu').toggleClass('is-visible');
  });

  $('button').on('click', function(e) {
  	e.preventDefault();
  	console.log('CLICKED');
  });

  var availableTags = [];

  $.ajax({
    type: 'GET',
    url: 'results.json',
    dataType: 'json',
    success: function(data) {
      $.each(data.posts, function(i, item) {
        console.log(item.title);
        availableTags.push(item.title);
      });
    },
    error: function() {
      console.log('Error');
    }
  });

  search.focusout(function(){
    var textVal = $(this).val();
    
    if(textVal === "") {
      $(this).removeClass('has-value');
    } else {
      $(this).addClass('has-value');
    }
  });

  search.autocomplete({
    source: function(request, response) {
      var results = $.ui.autocomplete.filter(availableTags, request.term);
      response(results.slice(0, 10));
    }
  });

  searchVoice.on('click', function(e) {
    e.preventDefault();
    search.addClass('has-value');
    searchLabel.text('Say something');
    startConverting();
  });

  var searchInput = document.getElementById('search');

  function startConverting ()  {
        if('webkitSpeechRecognition' in window){
          var speechRecognizer = new webkitSpeechRecognition();
          console.log('SPEEK');
          speechRecognizer.continuous = true;
          speechRecognizer.interimResults = true;
          speechRecognizer.lang = 'sr-RS';
          speechRecognizer.start();

          var finalTranscripts = '';

          speechRecognizer.onresult = function(event){
            var interimTranscripts = '';
            for(var i = event.resultIndex; i < event.results.length; i++){
              var transcript = event.results[i][0].transcript;
              transcript.replace("\n", "<br>");
              if (event.results[i].isFinal) {
                finalTranscripts += transcript;
              } else {
                interimTranscripts += transcript;
              }
            }
            // r.innerHTML = finalTranscripts + '<span style="color:#999">' + interimTranscripts + '</span>';
            console.log(finalTranscripts + interimTranscripts);
            searchInput.value = finalTranscripts + interimTranscripts;
          };
          speechRecognizer.onerror = function (event) {
          };
        }else{
          searchInput.innerHTML = 'Your browser is not supported. If google chrome, please upgrade!';
        }
      }


});
