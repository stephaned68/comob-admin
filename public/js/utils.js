/**
 * Slugify a string
 * @param s
 * @returns {string}
 */
function slugify(s) {
  return s.toString()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, "")  // remove diacritics
    .toLowerCase()
    .replace(' les ', ' ')
    .replace(' le ' , ' ')
    .replace(' la ' , ' ')
    .replace(' des ', ' ')
    .replace(' de ' , ' ')
    .replace(' du ' , ' ')
    .replace(' une ', ' ')
    .replace(' un ' , ' ')
    .replace(' a '  , ' ')
    .replace(' au ' , ' ')
    .replace(' (g)' , '-g')
    .replace(' (m)' , '-m')
    .replace(' (a)' , '-a')
    .replace(/\s+/g, '-')             // spaces to dashes
    .replace(/&/g, '-n-')             // ampersand to -n-
    .replace(/[^\w\-]+/g, '')         // remove non-words
    .replace(/\-\-+/g, '-')           // collapse multiple dashes
    .replace(/^-+/, '')               // trim starting dash
    .replace(/-+$/, '');              // trim ending dash
}

/**
 * Cleanup text
 * @param text
 * @returns {string}
 */
function cleanText(text) {
  if (text !== "") {
    text = text.replace(/\n\n/g, "§");
    text = text.replace(/\n/g, " ");
    text = text.replace(/'/g, "’");
    text = text.replace(/§/g, "\n");
    text = text.replaceAll("􀂄","");
    text = text[0].toUpperCase() + text.slice(1);
  }
  return text;
}

/**
 * Handle confirmation dialog for element deletion
 */
function confirmDelete() {
  $('.confirm-delete').confirm({
    title: 'Confirmation requise !',
    content: 'Confirmez-vous la suppression de cet élément ?',
    buttons: {
      confirm: {
        text: 'Oui, supprimer',
        action: function () {
          location.href = this.$target.attr('href');
          return true;
        }
      },
      cancel: {
        text: 'Non',
        action: function () {
          return true;
        }
      }
    }
  });
}

/**
 * Create entity identifier slug from name
 * @param idField
 * @param nameField
 */
function makeSlug(idField, nameField) {
  var $id = $(`#${idField}`);
  var $name = $(`#${nameField}`);
  $name.on("blur", function() {
    if ($name.val() !== '' && $id.val() === '') {
      $id.val(slugify($name.val()));
    }
  });
}

/**
 * Add an row to a parent table
 * @param tableId
 * @param resetFunction
 */
function addItem(tableId, resetFunction) {
  const $items = $(`#${tableId}`);
  let $newItem = $items.children().first().clone();
  $newItem = resetFunction($newItem);
  $items.append($newItem);
}

/**
 * Delete a row from a parent table
 * @param tableId
 * @param deleteBtClass
 * @param confirm
 */
function deleteItem(tableId, deleteBtClass, confirm) {
  confirm = confirm || false;
  $(`#${tableId}`).on("click",`.${deleteBtClass}`, function (event) {
    event.preventDefault();
    const itemCount = $(`#${tableId}`).children().length;
    if (itemCount === 1) { // do not destroy last entry
      return;
    }
    if (confirm) { // confirm deletion
      if (!window.confirm('Confirmez-vous la suppression de cet élément ?')) {
        return;
      }
    }
    const $row = $(this).parent().parent();
    $row.remove();
  });
}

/**
 * Load modal form with data
 * @param url
 * @param fields
 * @param formId
 */
function loadModalForm(url, fields, formId) {
  formId = formId || 'editPopup';
  $.get(url, function (response) {
    const modal = $(`#${ formId }`);
    const data = JSON.parse(response);
    fields.forEach(field => {
      modal.find(`.modal-body .form-group #${ field }`).val(data[field]);
    });
    // setup submit target
    let submitUrl=url;
    submitUrl = submitUrl.split('/');
    submitUrl[2]='edit';
    submitUrl = submitUrl.join('/');
    modal.find('.modal-body form').attr('action',submitUrl);
    // setup delete URL
    let deleteUrl = modal.find('.modal-body .form-group .btn-danger').attr('href');
    deleteUrl = deleteUrl.split('/')
    deleteUrl.pop();
    deleteUrl.push(url.split('/')[3]);
    deleteUrl = deleteUrl.join('/');
    modal.find('.modal-body .form-group .btn-danger').attr('href', deleteUrl);
  });
}