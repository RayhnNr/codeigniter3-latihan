<form id="form_product" enctype="multipart/form-data">
    <div class="card">
        <div class="card-body">
            
            <div class="form-group">
                <label>Product Code</label>
                <input type="text" class="form-control" value="<?= $product_code_preview ?>" disabled>
                <small class="text-muted">Product Code otomatis</small>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text" name="product_name" class="form-control">
                        <small class="text-danger" id="error_product_name"></small>
                    </div>

                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" class="form-control form-select2">
                            <option value="">-- Pilih Category --</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c->category_id ?>"><?= $c->category_name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-danger" id="error_category_id"></small>
                    </div>

                    <div class="form-group">
                        <label>Brand</label>
                        <select name="brand_id" class="form-control form-select2">
                            <option value="">-- Pilih Brand --</option>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?= $b->brand_id ?>"><?= $b->brand_name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-danger" id="error_brand_id"></small>
                    </div>
                    <div class="form-group">
                        <label for="status_toggle">Status</label><br>
                        <input type="hidden" name="status" id="status_hidden" value="<?= $active_status_id ?>">
                        <input type="checkbox" id="status_toggle" data-toggle="toggle" 
                            data-on="Aktif" data-off="Nonaktif" 
                            data-onstyle="success" data-offstyle="secondary" checked>
                        <small class="text-danger" id="error_status"></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Unit</label>
                        <select name="unit_id" class="form-control form-select2">
                            <option value="">-- Pilih Unit --</option>
                            <?php foreach ($units as $u): ?>
                                <option value="<?= $u->unit_id ?>"><?= $u->unit_name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-danger" id="error_unit_id"></small>
                    </div>

                    <div class="form-group">
                        <label>Product Type</label>
                        <select name="product_type" class="form-control form-select2">
                            <option value="">-- Pilih Product Type --</option>
                            <?php foreach ($product_type as $pt): ?>
                                <option value="<?= $pt->product_type_id ?>"><?= $pt->product_type_name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-danger" id="error_product_type"></small>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                        <small class="text-danger" id="error_description"></small>
                    </div>
                </div>
            </div>   
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-images text-primary"></i> Daftar Foto Produk</span>
            <button type="button" id="btnTambahRow" class="btn btn-primary btn-sm ml-auto">
                <i class="fas fa-plus"></i> Tambah Foto
            </button>
        </div>
        <div class="card-body" id="wrapperDetail">
            <!-- Row akan di-generate oleh JS, mulai dengan 1 baris -->
        </div>
        <div class="card-footer text-right">
            <a href="<?= base_url('product') ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </div>
</form>

<!-- Template row -->
<template id="rowTemplate">
    <div class="row-detail form-row align-items-center mb-3 pb-3 border-bottom">
        <div class="col-md-2 text-center">
            <img src="<?= base_url('assets/img/no-image.svg') ?>" class="img-preview img-thumbnail" alt="No Image Available" style="width: 100px; height: 80px; object-fit: cover;">
        </div>

        <div class="col-md-5">
            <label class="d-md-none">Pilih Gambar</label>
            <div class="custom-file">
                <input type="file" name="product_images[]" class="custom-file-input image-input" accept=".jpg,.jpeg,.png">
                <label class="custom-file-label file-label">Pilih file...</label>
            </div>
            <small class="text-danger error-image d-block"></small>
        </div>

        <div class="col-md-3 text-center">
            <label class="mb-1">Gambar Utama</label><br>
            <input type="checkbox" class="primary-toggle"
                   data-toggle="toggle" data-on="Ya" data-off="Tidak"
                   data-onstyle="success" data-offstyle="secondary" data-size="sm">
        </div>

        <div class="col-md-2 text-right">
            <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</template>

<script>
window.addEventListener('load', function () {
    function addRow() {
        let template = document.getElementById('rowTemplate').content.cloneNode(true);
        $('#wrapperDetail').append(template);

        let $lastRow = $('#wrapperDetail .row-detail').last();
        $lastRow.find('.primary-toggle').bootstrapToggle();

        // kalau ini baris PERTAMA, otomatis jadikan gambar utama
        if ($('#wrapperDetail .row-detail').length === 1) {
            $lastRow.find('.primary-toggle').bootstrapToggle('on');
        }
    }

    addRow();

    $('#btnTambahRow').on('click', function() {
        addRow();
    });

    // hapus baris
    $('#wrapperDetail').on('click', '.btn-remove-row', function() {
        if ($('.row-detail').length > 1) {
            var wasPrimary = $(this).closest('.row-detail').find('.primary-toggle').prop('checked');
            $(this).closest('.row-detail').remove();

            // kalau yang dihapus itu gambar utama, jadikan baris pertama sebagai gambar utama baru
            if (wasPrimary) {
                $('#wrapperDetail .row-detail').first().find('.primary-toggle').bootstrapToggle('on');
            }
        } else {
            Swal.fire('Info', 'Minimal harus ada 1 foto', 'info');
        }
    });

    // preview gambar saat file dipilih
    $('#wrapperDetail').on('change', '.image-input', function() {
        var $row = $(this).closest('.row-detail');
        var file = this.files[0];

        // update label nama file
        var fileName = file ? file.name : 'Pilih file...';
        $row.find('.file-label').text(fileName);

        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $row.find('.img-preview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // pastikan cuma 1 toggle "Gambar Utama" yang aktif (karena pakai radio behaviour manual)
    $('#wrapperDetail').on('change', '.primary-toggle', function() {
        var $thisRow = $(this).closest('.row-detail');

        if ($(this).prop('checked')) {
            // matikan toggle di baris lain
            $('#wrapperDetail .row-detail').not($thisRow).each(function() {
                $(this).find('.primary-toggle').bootstrapToggle('off');
            });
        }
    });

    $('.form-select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    $('#status_toggle').bootstrapToggle({
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

    $('#status_toggle').trigger('change');

    $('#form_product').on('submit', function(e) {
        e.preventDefault();
        $('.text-danger').text('');

        // tambahkan ini SEBELUM Swal konfirmasi
        $('input[name="is_primary_index"]').remove();
        $('#wrapperDetail .row-detail').each(function(index) {
            if ($(this).find('.primary-toggle').prop('checked')) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'is_primary_index',
                    value: index
                }).appendTo('#form_product');
            }
        });

        Swal.fire({
            title: 'Simpan data ini?',
            text: 'Pastikan data yang Anda isi sudah benar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, simpan',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                submitProduct();
            }
        });
    });

    function submitProduct() {
        var formData = new FormData(document.getElementById('form_product'));

        $.ajax({
            url: '<?= base_url('product/store') ?>',
            type: 'POST',
            data: formData,
            processData: false,   // WAJIB untuk FormData
            contentType: false,    // WAJIB untuk FormData
            dataType: 'json',
            beforeSend: function() {
                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if (response.status === 'success') {
                    window.location.href = '<?= base_url('product') ?>';
                } else if (response.status === 'failed' && response.errors) {
                    Swal.close();
                    $.each(response.errors, function(field, message) {
                        $('#error_' + field).text(message);
                    });
                } else {
                    Swal.fire('Gagal', response.message || 'Terjadi kesalahan', 'error');
                }
            },
            error: function() {
                Swal.fire('Gagal', 'Terjadi kesalahan pada server', 'error');
            }
        });
    }
});
</script>