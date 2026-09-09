<div class="card">
    <div class="card-header"><h5>Tambah Supplier</h5></div>
    <div class="card-body">
        <form id="form-supplier">
            <div class="form-group">
                <label>Code Supplier</label>
                <input type="text" class="form-control" value="<?= $supplier_code_preview ?>" disabled>
                <small class="text-muted">Code supplier otomatis.</small>
            </div>
            <div class="form-group">
                <label>Nama Supplier</label>
                <input type="text" name="nama_supplier" class="form-control" value="<?= set_value('nama_supplier') ?>">
                <small class="text-danger" id="error_nama_supplier"></small>
            </div>
            <div class="form-group">
                <label for="">Status</label>
                <input type="hidden" name="status" id="status_hidden" value="<?= $active_status_id ?>">
                <input type="checkbox" id="status_toggle" data-toggle="toggle" 
                    data-on="Aktif" data-off="Nonaktif" 
                    data-onstyle="success" data-offstyle="secondary" checked>
                <small class="text-danger" id="error_status"></small>
            </div>
            <a href="<?= base_url('supplier') ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
<script>
window.addEventListener('load', function () {
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    $('#status_toogle').bootstrapToggle({
        on: 'Aktif',
        off: 'Nonaktif',
        onstyle: 'success',
        offstyle: 'secondary'
    });

    $('#status_toggle').change(function() {
        if($(this).is(':checked')) {
            $('#status_hidden').val('<?= $active_status_id ?>');
        } else {
            $('#status_hidden').val('<?= $inactive_status_id ?>');
        }
    });
    $('#form-supplier').on('submit', function (event) {
        event.preventDefault();
        $('.text-danger').text('');
        Swal.fire({
            title: 'Simpan supplier?',
            text: 'Pastikan data supplier sudah benar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, simpan',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $.post('<?= base_url('supplier/store') ?>', $('#form-supplier').serialize(), function (response) {
                if (response.status === 'success') {
                    window.location.href = '<?= base_url('supplier') ?>';
                } else if (response.errors) {
                    $.each(response.errors, function (field, message) { $('#error_' + field).text(message); });
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            }, 'json').fail(function () { Swal.fire('Gagal', 'Terjadi kesalahan pada server.', 'error'); });
        });
    });
});
</script>
