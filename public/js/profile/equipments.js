$(function () {

  // add a new row
  $("#btnAdd").click(function (event) {
    event.preventDefault();
    addItem("equipment", function(item) {
      item.find(".inp-number").val("1");
      item.find(".inp-text").val("");
      return item;
    });
  });

  // delete a row
  deleteItem("equipment", "btn-delete");

});