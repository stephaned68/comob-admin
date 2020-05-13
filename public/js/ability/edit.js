// slugify description for new ability

$(function() {

  makeSlug("capacite", "nom");

  var $description = $("#description");
  $description.blur(function() {
    $description.val(cleanText($description.val()));
  });

});