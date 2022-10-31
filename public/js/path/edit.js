/**
 * slugify description for new path
 */

$(function () {
  makeSlug('voie', 'nom');

  var $notes = $('#notes');
  $notes.on('blur', function () {
    $notes.val(cleanText($notes.val()));
  });
});
