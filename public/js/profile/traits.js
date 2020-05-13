$(function () {

  // re-format traits description
  $('#traits').on("blur",'.inp-text', function (event) {
    $(this).val(cleanText($(this).val()));
  });

  // add a new row
  $("#btnAdd").click(function (event) {
    event.preventDefault();
    addItem("traits", function(item) {
      item.find(".inp-text").val("");
      return item;
    });
  });

  // delete a row
  deleteItem("traits", "btn-delete", true);

});