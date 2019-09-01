// slugify description for new path

$(function() {

  var $voie = $("#voie");
  var $nom = $('#nom');
  $nom.blur(function() {
    if ($nom.val() !== '' && $voie.val() === '') {
      $voie.val(slugify($nom.val()));
    }
  });

  var $notes = $("#notes");
  $notes.blur(function() {
    $notes.val(cleanText($notes.val()));
  });

});