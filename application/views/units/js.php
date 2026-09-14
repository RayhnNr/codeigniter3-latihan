<script>
var table;

window.addEventListener('load', function () {

    table = $('#table-unit').DataTable({
        processing: true,
        serverSide: false,
        responsive: true,
        ajax: {
            url: '<?= base_url('units/get_data') ?>',
            type: 'POST',
            dataSrc: 'data'
        },
        columns: [
            { data: 'no', orderable: false, searchable: false },
            { data: 'unit_name' },
            { data: 'status'},
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    function initunitStatusToggle() {
        var toggle = $('#units_status');

        if (!toggle.length) {
            return;
        }

        toggle.bootstrapToggle();
        toggle.trigger('change');
    }

    $(document).on('change', '#units_status', function() {
        var activeValue = $(this).data('active-value');
        var inactiveValue = $(this).data('inactive-value');

        $('#units_status_value').val(
            $(this).prop('checked') ? activeValue : inactiveValue
        );
    });

    // tombol Tambah - load form.php ke modal
    $('#btn_add').on('click', function () {
        $.ajax({
            url: '<?= base_url('units/ajax_form') ?>',
            type: 'POST',
            success: function (response) {
                $('#modalFormContent').html(response);
                initunitStatusToggle();
                $('#modalForm').modal('show');
            },
            error: function () {
                Swal.fire('Gagal', 'Gagal memuat form.', 'error');
            }
        });
    });

    // tombol Edit - load edit.php ke modal
    $(document).on('click', '.btn-edit', function () {
        var id = $(this).data('id');

        $.ajax({
            url: '<?= base_url('units/ajax_edit') ?>',
            type: 'POST',
            data: { id: id },
            success: function (response) {
                $('#modalFormContent').html(response);
                initunitStatusToggle();
                $('#modalForm').modal('show');
            },
            error: function () {
                Swal.fire('Gagal', 'Gagal memuat data.', 'error');
            }
        });
    });

    // tombol Delete
    $(document).on('click', '.btn-delete', function () {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Hapus data ini?',
            text: 'Data yang dihapus tidak bisa dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545',
            reverseButtons: true
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('units/delete') ?>',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire('Berhasil', response.message, 'success');
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus.', 'error');
                    }
                });
            }
        });
    });

    // submit form (dipakai bareng untuk TAMBAH & EDIT, karena keduanya id="formunit")
    $(document).on('submit', '#formunit', function (e) {
        e.preventDefault();
        $('.text-danger').text('');

        var formData = $(this).serialize();

        $.ajax({
            url: '<?= base_url('units/save') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    Swal.fire('Berhasil', response.message, 'success');
                    $('#modalForm').modal('hide');
                    table.ajax.reload(null, false);
                } else if (response.errors) {
                    $.each(response.errors, function (field, message) {
                        $('#error_' + field).text(message);
                    });
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            },
            error: function () {
                Swal.fire('Gagal', 'Terjadi kesalahan pada server.', 'error');
            }
        });
    });

});
</script>