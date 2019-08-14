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
    text = $description.val();
    if (text !== "") {
      text = text.replace(/\n/g, " ");
      text = text.replace(/'/g, "’");
      $description.val(text[0].toUpperCase() + text.slice(1));
    }
  });

});