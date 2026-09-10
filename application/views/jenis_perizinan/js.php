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

    $(document).on('click', '.btn-delete', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        Swal.fire({
            title: 'Hapus data ini?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33'
        }).then(function (result) {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});

function openForm(id) {
    var title = id ? 'Edit Jenis Perizinan' : 'Tambah Jenis Perizinan';

    $('#modal-perizinan-tipe-title').text(title);
    $('#modal-perizinan-tipe-body').html('<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Memuat form...</div>');
    $('#modal-perizinan-tipe').modal('show');

    $.post('<?= site_url('jenis_perizinan/ajax_form') ?>', { jenis_perizinan_id: id })
        .done(function (html) {
            $('#modal-perizinan-tipe-body').html(html);
            $('#jenis_perizinan_status').bootstrapToggle();

            $('#jenis_perizinan_status').on('change', function () {
                var value = $(this).prop('checked')
                    ? $(this).data('active-value')
                    : $(this).data('inactive-value');
                $('#jenis_perizinan_status_value').val(value);
            });

            $('#jenis_perizinan_name').on('input', function () {
                var code = $(this).val().trim().toUpperCase().replace(/\s+/g, '_');
                $('#jenis_perizinan_code').val(code);
                $('#jenis_perizinan_code_error').addClass('d-none');
                $('#jenis_perizinan_name_error').addClass('d-none');
                $(this).removeClass('is-invalid');
            });
            
            $('#form-jenis-perizinan').on('submit', function (event) {
                var form = this;
                var nameInput = $('#jenis_perizinan_name');

                if (!nameInput.val().trim()) {
                    nameInput.addClass('is-invalid').focus();
                    $('#jenis_perizinan_name_error').removeClass('d-none');
                    event.preventDefault();
                    return;
                }

                event.preventDefault();
                $.post('<?= site_url('jenis_perizinan/check_code') ?>', {
                    jenis_perizinan_name: nameInput.val(),
                    jenis_perizinan_id: $(form).find('[name="jenis_perizinan_id"]').val()
                }, function (response) {
                    if (response.exists) {
                        $('#jenis_perizinan_code_error').removeClass('d-none');
                        $('#jenis_perizinan_code').addClass('is-invalid');
                        return;
                    }

                    form.submit();
                }, 'json');
            });
            $('#jenis_perizinan_name').trigger('focus');
        })
        .fail(function () {
            $('#modal-perizinan-tipe-body').html('<div class="alert alert-danger mb-0">Form tidak dapat dimuat.</div>');
        });
}
</script>