// slugify description for new profile

$(function() {

  makeSlug("profil", "nom");

  var $description = $("#description");
  $description.blur(function() {
    $description.val(cleanText($description.val()));
  });

});