jQuery(document).ready(function($) {
  $('#upload_gravatar_button').click(function() {
    tb_show('Upload a logo', 'media-upload.php?referer=vdtestim-settings&type=image&TB_iframe=true&post_id=0', false);
    return false;
  });
  window.send_to_editor = function(html) {
    var image_url = $('img',html).attr('src');
    $('#default_gravatar_url').val(image_url);
    tb_remove();
  };
    window.send_to_editor = function(html) {
    var image_url = $('img',html).attr('src');
    $('#default_gravatar_url').val(image_url);
    tb_remove();
    $('#gravatar_upload_preview img').attr('src',image_url);

    $('#vdtestim_submit_options').trigger('click');
  };

  var objheight = $(".slide").height();
  $('.slider').height(objheight+30);

  $(window).resize(function() {
    var objheight = $(".slide").height();
    $(".slider").height(objheight+30);
  });
    
});
