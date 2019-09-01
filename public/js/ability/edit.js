// slugify description for new ability

$(function() {

  var $capacite = $("#capacite");
  var $nom = $('#nom');
  $nom.blur(function() {
    if ($nom.val() !== '' && $capacite.val() === '') {
      $capacite.val(slugify($nom.val()));
    }
  });

  var $description = $("#description");
  $description.blur(function() {
    $description.val(cleanText($description.val()));
  });

});