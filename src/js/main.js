$(function(){

  $('button').on('click', function(e) {
  	e.preventDefault();
  	alert('RADII');
  });

  $('.searchInput').focusout(function(){
    
    var text_val = $(this).val();
    
    if(text_val === "") {
      $(this).removeClass('has-value');
    } else {
      $(this).addClass('has-value');
    }
    
  });

});
