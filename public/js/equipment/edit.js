
$(function() {

  // slugify description for new family
  var $code = $("#code");
  var $designation = $('#designation');
  $designation.blur(function() {
    if ($designation.val() !== '' && $code.val() === '') {
      $code.val(slugify($designation.val()));
    }
  });

});