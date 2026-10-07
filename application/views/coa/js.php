<script>
var base_url = <?php echo json_encode(base_url()); ?>;

$(function() {
    $("#table-coa").DataTable({
        "responsive": true,
        "autoWidth": false,
        "ordering": false,
        "paging": false
    });

    $('#is_active').bootstrapSwitch({
        onText: 'Aktif',
        offText: 'Nonaktif',
        onColor: 'success',
        offColor: 'default',
        size: 'small'
    });

    $('#btn_add').on('click', function () {
        resetCoaForm();
        $('#modal-coa-title').text('Tambah Akun');
        $('#modal-coa').modal('show');
    });

    $('#parent_id').on('change', function () {
        if ($('#coa_id').val()) return;

        if (!$(this).val()) {
            $('#coa_code, #coa_type').val('');
            return;
        }

        $.get(base_url + 'coa/generate_code', { parent_id: $(this).val() }, function (res) {
            if (res.status) {
                $('#coa_code').val(res.coa_code);
                $('#coa_type').val(res.coa_type);
            } else {
                $('#coa_code, #coa_type').val('');
                alert(res.message || 'Gagal membuat kode akun.');
            }
        }, 'json').fail(function () {
            $('#coa_code, #coa_type').val('');
            alert('Terjadi kesalahan pada server.');
        });
    });

    $('#form-coa').on('submit', function (e) {
        e.preventDefault();

        $.post(base_url + 'coa/save', $(this).serialize(), function (res) {
            if (res.status) {
                $('#modal-coa').modal('hide');
                location.reload();
            } else {
                alert(res.message);
            }
        }, 'json').fail(function () {
            alert('Terjadi kesalahan pada server.');
        });
    });
});

function resetCoaForm() {
    $('#coa_id').val('');
    $('#coa_code, #coa_type').val('');
    $('#coa_name').val('');
    $('#parent_id').val('');
    $('#parent_id').prop('disabled', false);
    $('#is_active').bootstrapSwitch('state', true, true);
}

function editCoa(id) {
    $.get(base_url + 'coa/get_data/' + id, function (res) {
        if (!res.status) { alert(res.message); return; }

        resetCoaForm();
        $('#coa_id').val(res.data.coa_id);
        $('#parent_id').val(res.data.parent_id || '').prop('disabled', true); // parent tidak diubah
        $('#coa_code').val(res.data.coa_code);
        $('#coa_type').val(res.data.coa_type);
        $('#coa_name').val(res.data.coa_name);
        $('#is_active').bootstrapSwitch('state', res.data.is_active == 1, true);

        $('#modal-coa-title').text('Edit Akun');
        $('#modal-coa').modal('show');
    }, 'json').fail(function () {
        alert('Terjadi kesalahan pada server.');
    });
}

function deleteCoa(id) {
    if (!confirm('Hapus akun ini?')) return;

    $.post(base_url + 'coa/delete/' + id, {}, function (res) {
        if (res.status) {
            location.reload();
        } else {
            alert(res.message);
        }
    }, 'json').fail(function () {
        alert('Terjadi kesalahan pada server.');
    });
}
</script>