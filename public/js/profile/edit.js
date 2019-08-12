// slugify description for new profile

$(function() {

  var $profil = $("#profil");
  var $nom = $('#nom');
  $nom.blur(function() {
    if ($nom.val() !== '' && $profil.val() === '') {
      $profil.val(slugify($nom.val()));
    }
  });

});