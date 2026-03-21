function openDeleteModal(url) {
  document.getElementById('deleteModalConfirm').href = url;
  document.getElementById('deleteModalBackdrop').classList.add('open');
}

document.addEventListener('DOMContentLoaded', function () {

  document.getElementById('deleteModalCancel').addEventListener('click', function () {
    document.getElementById('deleteModalBackdrop').classList.remove('open');
  });

  document.getElementById('deleteModalBackdrop').addEventListener('click', function (e) {
    if (e.target === this) this.classList.remove('open');
  });

});