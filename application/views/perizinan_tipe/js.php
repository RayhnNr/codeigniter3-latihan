<script>
window.addEventListener('load', function () {
    $('#table-perizinan-tipe').DataTable({
        responsive: true,
        autoWidth: false,
        columnDefs: [{
            targets: 0,
            orderable: false,
            searchable: false
        }, {
            targets: 3,
            orderable: false,
            searchable: false
        }]
    });

    $('#btn-add').on('click', function () {
        openForm('');
    });

    $(document).on('click', '.btn-edit', function () {
        openForm($(this).data('id'));
    });
});

function openForm(id) {
    var title = id ? 'Edit Jenis Perizinan' : 'Tambah Jenis Perizinan';

    $('#modal-perizinan-tipe-title').text(title);
    $('#modal-perizinan-tipe-body').html('<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Memuat form...</div>');
    $('#modal-perizinan-tipe').modal('show');

    $.post('<?= site_url('perizinan_tipe/ajax_form') ?>', { id: id })
        .done(function (html) {
            $('#modal-perizinan-tipe-body').html(html);
            $('#perizinan_tipe_status').bootstrapToggle();
            $('#perizinan_tipe_status').on('change', function () {
                $('#perizinan_tipe_status_value').val($(this).prop('checked') ? 1 : 2);
            });
            $('#nama_tipe').trigger('focus');
        })
        .fail(function () {
            $('#modal-perizinan-tipe-body').html('<div class="alert alert-danger mb-0">Form tidak dapat dimuat.</div>');
        });
}
</script>