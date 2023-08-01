/**
 * Display confirmation dialog on delete
 */

$(function () {
  confirmDelete();
});

/**
 * Load data into modal form
 */
$('#editPopup').on('show.bs.modal', function (event) {
  const href = $(event.relatedTarget);
  const id = href.data('id');
  loadModalForm('/path/get/' + id, [
    'voie',
    'nom',
    'notes',
    'equipement',
    'type',
    'pfx_deladu',
  ]);
});
