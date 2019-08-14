// slugify description for new path

$(function() {

  var $voie = $("#voie");
  var $nom = $('#nom');
  $nom.blur(function() {
    if ($nom.val() !== '' && $voie.val() === '') {
      $voie.val(slugify($nom.val()));
    }
  });

});