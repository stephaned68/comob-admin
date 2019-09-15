$(function() {

  var $fullPath = $("#fullPath");
  $fullPath.blur(function() {
    $fullPath.val(cleanText($fullPath.val()));
  });

});