// slugify description for new ability

$(function() {

  var $code = $("#code");
  var $libelle = $('#libelle');
  $libelle.blur(function() {
    if ($libelle.val() !== '' && $code.val() === '') {
      $code.val(slugify($libelle.val()));
    }
  });

  $('.select-2').select2();

});