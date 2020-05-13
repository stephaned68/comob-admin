
$(function() {

  makeSlug("code", "designation");

  var $notes = $("#notes");
  $notes.blur(function() {
    $notes.val(cleanText($notes.val()));
  });

});