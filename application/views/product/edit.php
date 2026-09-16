```html
<form id="form_edit_product" enctype="multipart/form-data">
    <div class="card shadow-sm">

        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-edit text-primary mr-2"></i>
                Edit Product
            </h5>
        </div>

        <div class="card-body">
            <input type="hidden" name="product_id" value="<?= $product->product_id ?>">

            <div class="form-group">
                <label>Product Code</label>
                <input type="text" class="form-control" value="<?= $product->product_code ?>" disabled>
                <small class="text-muted">Kode produk tidak bisa diubah.</small>
            </div>

            <div class="row">
                <div class="col-md-6">

                    <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text"
                            name="product_name"
                            class="form-control"
                            value="<?= $product->product_name ?>">
                        <small class="text-danger" id="error_product_name"></small>
                    </div>

                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" class="form-control form-select2">
                            <option value="">-- Pilih Category --</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c->category_id ?>"
                                    <?= ($product->category_id == $c->category_id) ? 'selected' : '' ?>>
                                    <?= $c->category_name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-danger" id="error_category_id"></small>
                    </div>

                    <div class="form-group">
                        <label>Brand</label>
                        <select name="brand_id" class="form-control form-select2">
                            <option value="">-- Pilih Brand --</option>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?= $b->brand_id ?>"
                                    <?= ($product->brand_id == $b->brand_id) ? 'selected' : '' ?>>
                                    <?= $b->brand_name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-danger" id="error_brand_id"></small>
                    </div>

                    <div class="form-group mb-md-0">
                        <label>Status</label><br>

                        <input type="hidden"
                            name="status"
                            id="status_hidden"
                            value="<?= $product->status ?>">

                        <input type="checkbox"
                            id="status_toggle"
                            data-toggle="toggle"
                            data-on="Aktif"
                            data-off="Nonaktif"
                            data-onstyle="success"
                            data-offstyle="secondary"
                            <?= $product->status == $active_status_id ? 'checked' : '' ?>>

                        <small class="text-danger" id="error_status"></small>
                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group">
                        <label>Unit</label>
                        <select name="unit_id" class="form-control form-select2">
                            <option value="">-- Pilih Unit --</option>
                            <?php foreach ($units as $u): ?>
                                <option value="<?= $u->unit_id ?>"
                                    <?= ($product->unit_id == $u->unit_id) ? 'selected' : '' ?>>
                                    <?= $u->unit_name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-danger" id="error_unit_id"></small>
                    </div>

                    <div class="form-group">
                        <label>Product Type</label>
                        <select name="product_type" class="form-control form-select2">
                            <option value="">-- Pilih Product Type --</option>
                            <?php foreach ($product_type as $pt): ?>
                                <option value="<?= $pt->product_type_id ?>"
                                    <?= ($product->product_type == $pt->product_type_id) ? 'selected' : '' ?>>
                                    <?= $pt->product_type_name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-danger" id="error_product_type"></small>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description"
                            class="form-control"
                            rows="5"><?= $product->description ?></textarea>
                        <small class="text-danger" id="error_description"></small>
                    </div>

                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="mb-1 font-weight-bold">
                        <i class="fas fa-images text-primary mr-2"></i>
                        Foto Produk
                    </h6>
                    <small class="text-muted">
                        Kelola foto produk yang tersimpan atau tambahkan foto baru.
                    </small>
                </div>

                <button type="button"
                    id="btnTambahRow"
                    class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i>
                    Tambah Foto
                </button>
            </div>

            <div id="wrapperDetail">
                <!-- Form Multi Foto -->
            </div>

        </div>

        <div class="card-footer d-flex justify-content-end">
            <a href="<?= base_url('product') ?>"
                class="btn btn-secondary mr-2">
                Batal
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i>
                Update
            </button>
        </div>

    </div>
</form>
```

<template id="rowTemplateExisting">
    <div class="row-detail form-row align-items-center mb-3 pb-3 border-bottom" data-existing="1">
        <div class="col-md-2 text-center">
            <img src="" class="img-preview img-thumbnail existing-img preview-image" alt="Preview gambar" style="width: 100px; height: 80px; object-fit: cover; cursor: pointer;">
        </div>

        <div class="col-md-5">
            <small class="text-muted d-block mb-1">Ganti foto</small>
            <div class="custom-file">
                <input type="file" name="replace_images[]" class="custom-file-input replace-image-input" accept=".jpg,.jpeg,.png,.gif">
                <label class="custom-file-label replace-file-label">Pilih file...</label>
                <small class="text-muted">Format file: JPG, JPEG, PNG, dan GIF. Maksimal ukuran 2 MB.</small>
            </div>
            <input type="hidden" name="existing_image_id[]" class="existing-image-id" value="">
            <input type="hidden" name="replace_image_ids[]" class="replace-image-id" value="">
        </div>

        <div class="col-md-3 text-center">
            <label class="mb-1">Gambar Utama</label><br>
            <input type="checkbox" class="primary-toggle" data-existing-primary="1"
                   data-toggle="toggle" data-on="Ya" data-off="Tidak"
                   data-onstyle="success" data-offstyle="secondary" data-size="sm">
        </div>

        <div class="col-md-2 text-right">
            <button type="button" class="btn btn-success btn-sm btn-save-image d-none" title="Simpan foto">
                <i class="fas fa-check"></i>
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row" title="Hapus foto">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</template>
<template id="rowTemplateNew">
    <div class="row-detail form-row align-items-center mb-3 pb-3 border-bottom" data-existing="0">
        <div class="col-md-2 text-center">
            <img src="<?= base_url('assets/img/no-image.svg') ?>" class="img-preview img-thumbnail preview-image" alt="No Image Available" style="width: 100px; height: 80px; object-fit: cover; cursor: pointer;">
        </div>

        <div class="col-md-5">
            <label class="d-md-none">Pilih Gambar</label>
            <div class="custom-file">
                <input type="file" name="product_images[]" class="custom-file-input image-input" accept=".jpg,.jpeg,.png">
                <label class="custom-file-label file-label">Pilih file...</label>
                <small class="text-muted">Format file: JPG, JPEG, PNG, dan GIF. Maksimal ukuran 2 MB.</small>
            </div>
            <small class="text-danger error-image d-block"></small>
        </div>

        <div class="col-md-3 text-center primary-control invisible">
            <label class="mb-1">Gambar Utama</label><br>
            <input type="checkbox" class="primary-toggle"
                   data-toggle="toggle" data-on="Ya" data-off="Tidak"
                   data-onstyle="success" data-offstyle="secondary" data-size="sm">
        </div>

        <div class="col-md-2 text-right">
            <button type="button" class="btn btn-success btn-sm btn-save-image d-none" title="Simpan foto">
                <i class="fas fa-check"></i>
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row" title="Hapus foto">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</template>

<div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary border-0">
                <h5 class="modal-title text-white">Preview Gambar</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="imagePreview" src="" alt="Preview gambar produk" class="img-fluid" style="max-height: 75vh;">
            </div>
        </div>
    </div>
</div>

<script>
window.addEventListener('load', function () {
    $('.form-select2').select2({
        theme: 'bootstrap4',
        width: '100%',
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

    // data gambar lama dari Controller, dikirim sebagai JSON
    var existingImages = <?= json_encode($product_images ?? []) ?>;

    function addExistingRow(image) {
        let template = document.getElementById('rowTemplateExisting').content.cloneNode(true);
        $('#wrapperDetail').append(template);

        let $lastRow = $('#wrapperDetail .row-detail').last();
        $lastRow.find('.existing-img').attr('src', '<?= base_url('uploads/products/') ?>' + encodeURIComponent(image.file_name));
        $lastRow.find('.existing-image-id').val(image.product_image_id);
        $lastRow.find('.replace-image-id').val(image.product_image_id);
        $lastRow.find('.primary-toggle').bootstrapToggle();

        if (image.is_primary == 1) {
            $lastRow.find('.primary-toggle').bootstrapToggle('on');
        }
    }

    function addNewRow() {
        let template = document.getElementById('rowTemplateNew').content.cloneNode(true);
        $('#wrapperDetail').append(template);

        let $lastRow = $('#wrapperDetail .row-detail').last();
        $lastRow.find('.primary-toggle').bootstrapToggle();
    }

    // load semua gambar lama saat halaman pertama dibuka
    existingImages.forEach(function(img) {
        addExistingRow(img);
    });

    // kalau belum ada gambar sama sekali, langsung sediakan 1 baris kosong untuk upload baru
    if (existingImages.length === 0) {
        addNewRow();
        $('#wrapperDetail .row-detail').first().find('.primary-toggle').bootstrapToggle('on');
    }

    $('#btnTambahRow').on('click', function() {
        addNewRow();
    });

    // hapus baris (baik gambar lama maupun baru)
    $('#wrapperDetail').on('click', '.btn-remove-row', function() {
        var $row = $(this).closest('.row-detail');
        var isExisting = $row.data('existing') == 1;
        var imageId = $row.find('.existing-image-id').val();

        Swal.fire({
            title: 'Hapus foto ini?',
            text: isExisting ? 'Foto akan langsung dihapus dari database.' : 'Foto yang belum disimpan akan dibuang.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545',
            reverseButtons: true
        }).then(function(result) {
            if (!result.isConfirmed) {
                return;
            }

            if (!isExisting) {
                removeImageRow($row);
                return;
            }

            $.post('<?= base_url('product/delete_image') ?>/' + imageId, {
                product_id: '<?= $product->product_id ?>'
            }, function(response) {
                if (response.status === 'success') {
                    removeImageRow($row);
                    Swal.fire({ 
                        toast: true, 
                        position: 'top-end', 
                        icon: 'success', 
                        title: response.message, 
                        showConfirmButton: false, 
                        timer: 1800 
                    });
                } else {
                    Swal.fire('Gagal', response.message || 'Foto gagal dihapus.', 'error');
                }
            }, 'json').fail(function() {
                Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus foto.', 'error');
            });
        });
    });

    function removeImageRow($row) {
        var wasPrimary = $row.find('.primary-toggle').prop('checked');
        $row.remove();
        if ($('#wrapperDetail .row-detail').length === 0) {
            addNewRow();
        } else if (wasPrimary) {
            $('#wrapperDetail .row-detail').first().find('.primary-toggle').bootstrapToggle('on');
        }
    }

    // preview gambar BARU saat file dipilih
    $('#wrapperDetail').on('change', '.image-input', function() {
        var $row = $(this).closest('.row-detail');
        var file = this.files[0];

        var fileName = file ? file.name : 'Pilih file...';
        $row.find('.file-label').text(fileName);
        $row.find('.btn-save-image').toggleClass('d-none', !file);

        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $row.find('.img-preview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }

    });

    $('#wrapperDetail').on('change', '.replace-image-input', function() {
        var $row = $(this).closest('.row-detail');
        var file = this.files[0];

        $row.find('.replace-file-label').text(file ? file.name : 'Pilih file...');
        $row.find('.btn-save-image').toggleClass('d-none', !file);

        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $row.find('.img-preview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    $('#wrapperDetail').on('click', '.btn-save-image', function() {
        var $button = $(this);
        var $row = $button.closest('.row-detail');
        var isExisting = $row.data('existing') == 1;
        var input = isExisting ? $row.find('.replace-image-input')[0] : $row.find('.image-input')[0];
        var imageId = $row.find('.existing-image-id').val();

        if (!input || !input.files.length) {
            return;
        }

        if (!isExisting) {
            var newFormData = new FormData();
            newFormData.append('image', input.files[0]);
            newFormData.append('product_id', '<?= $product->product_id ?>');
            newFormData.append('is_primary', $row.find('.primary-toggle').prop('checked') ? '1' : '0');

            $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            $.ajax({
                url: '<?= base_url('product/add_image') ?>',
                type: 'POST',
                data: newFormData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $row.attr('data-existing', '1');
                        $row.data('existing', 1);
                        $row.find('.image-input')
                            .removeAttr('name')
                            .removeClass('image-input')
                            .addClass('replace-image-input');
                        $row.find('.file-label')
                            .removeClass('file-label')
                            .addClass('replace-file-label')
                            .text('Pilih file...');
                        $row.find('.primary-control').removeClass('invisible');
                        $('<input>').attr({ 
                            type: 'hidden', 
                            name: 'existing_image_id[]', 
                            value: response.image_id 
                        }).addClass('existing-image-id').appendTo($row.find('.col-md-5'));
                        $('<input>').attr({ 
                            type: 'hidden', 
                            name: 'replace_image_ids[]', 
                            value: response.image_id 
                        }).addClass('replace-image-id').appendTo($row.find('.col-md-5'));
                        input.value = '';
                        $button.addClass('d-none').prop('disabled', false).html('<i class="fas fa-check"></i>');
                        Swal.fire({ 
                            toast: true, 
                            position: 'top-end', 
                            icon: 'success', 
                            title: response.message, 
                            showConfirmButton: false, 
                            timer: 1800 
                        });
                    } else {
                        $button.prop('disabled', false).html('<i class="fas fa-check"></i>');
                        Swal.fire('Gagal', response.message || 'Foto gagal disimpan.', 'error');
                    }
                },
                error: function() {
                    $button.prop('disabled', false).html('<i class="fas fa-check"></i>');
                    Swal.fire('Gagal', 'Terjadi kesalahan saat menyimpan foto.', 'error');
                }
            });
            return;
        }

        var formData = new FormData();
        formData.append('image', input.files[0]);
        formData.append('product_id', '<?= $product->product_id ?>');

        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        $.ajax({
            url: '<?= base_url('product/replace_image') ?>/' + imageId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $row.find('.img-preview').attr('src', response.image_url + '?v=' + Date.now());
                    $row.find('.replace-file-label').text('Pilih file...');
                    input.value = '';
                    $row.find('.replace-image-id').val(imageId);
                    $button.addClass('d-none').prop('disabled', false).html('<i class="fas fa-check"></i>');
                    Swal.fire({ 
                        toast: true, 
                        position: 'top-end', 
                        icon: 'success', 
                        title: response.message, 
                        showConfirmButton: false, 
                        timer: 1800 
                    });
                } else {
                    $button.prop('disabled', false).html('<i class="fas fa-check"></i>');
                    Swal.fire('Gagal', response.message || 'Foto gagal diganti.', 'error');
                }
            },
            error: function() {
                $button.prop('disabled', false).html('<i class="fas fa-check"></i>');
                Swal.fire('Gagal', 'Terjadi kesalahan saat mengganti foto.', 'error');
            }
        });
    });

    $('#wrapperDetail').on('click', '.preview-image', function() {
        var imageSource = $(this).attr('src');
        if (imageSource && imageSource.indexOf('no-image.svg') === -1) {
            $('#imagePreview').attr('src', imageSource);
            $('#imagePreviewModal').modal('show');
        }
    });

    // pastikan cuma 1 toggle "Gambar Utama" yang aktif, baik gambar lama maupun baru
    var primaryToggleSyncing = false;
    $('#wrapperDetail').on('change', '.primary-toggle', function() {
        var $thisRow = $(this).closest('.row-detail');
        var isExisting = $thisRow.data('existing') == 1;

        if (primaryToggleSyncing) {
            return;
        }

        if (!$(this).prop('checked')) {
            if (isExisting) {
                primaryToggleSyncing = true;
                $(this).bootstrapToggle('on');
                primaryToggleSyncing = false;
            }
            return;
        }

        var previousPrimary = $('#wrapperDetail .row-detail').not($thisRow).filter(function() {
            return $(this).find('.primary-toggle').prop('checked');
        }).first();

        primaryToggleSyncing = true;
        $('#wrapperDetail .row-detail').not($thisRow).each(function() {
            $(this).find('.primary-toggle').bootstrapToggle('off');
        });
        primaryToggleSyncing = false;

        if (isExisting) {
            var imageId = $thisRow.find('.existing-image-id').val();
            $.post('<?= base_url('product/set_primary_image') ?>/' + imageId, {
                product_id: '<?= $product->product_id ?>'
            }, function(response) {
                if (response.status === 'success') {
                    Swal.fire({ 
                        toast: true, 
                        position: 'top-end', 
                        icon: 'success', 
                        title: response.message,
                        showConfirmButton: false, 
                        timer: 1400 
                    });
                } else {
                    restorePreviousPrimary(previousPrimary, $thisRow);
                    Swal.fire('Gagal', response.message || 'Gambar utama gagal diubah.', 'error');
                }
            }, 'json').fail(function() {
                restorePreviousPrimary(previousPrimary, $thisRow);
                Swal.fire('Gagal', 'Terjadi kesalahan saat mengubah gambar utama.', 'error');
            });
        }
    });

    function restorePreviousPrimary($previousPrimary, $currentRow) {
        primaryToggleSyncing = true;
        $currentRow.find('.primary-toggle').bootstrapToggle('off');
        if ($previousPrimary && $previousPrimary.length) {
            $previousPrimary.find('.primary-toggle').bootstrapToggle('on');
        }
        primaryToggleSyncing = false;
    }

    // ===== SUBMIT FORM =====
    $('#form_edit_product').on('submit', function(e) {
        e.preventDefault();

        $('.text-danger').text('');

        // hapus dulu hidden input primary lama kalau ada (jaga-jaga submit ulang)
        $('input[name="is_primary_row_type"], input[name="is_primary_value"]').remove();

        // tentukan baris mana yang jadi primary, kirim tipe + ID/index-nya
        var newImageIndex = 0;
        $('#wrapperDetail .row-detail').each(function() {
            var isExisting = $(this).data('existing') == 1;

            if ($(this).find('.primary-toggle').prop('checked')) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'is_primary_row_type',
                    value: isExisting ? 'existing' : 'new'
                }).appendTo('#form_edit_product');

                var value = isExisting
                    ? $(this).find('.existing-image-id').val()
                    : newImageIndex;

                $('<input>').attr({
                    type: 'hidden',
                    name: 'is_primary_value',
                    value: value
                }).appendTo('#form_edit_product');
            }

            if (!isExisting) {
                newImageIndex++;
            }
        });

        Swal.fire({
            title: 'Update data ini?',
            text: 'Pastikan data yang Anda ubah sudah benar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, update',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                submitEditProduct();
            }
        });
    });

    function submitEditProduct() {
        var formData = new FormData(document.getElementById('form_edit_product'));

        $.ajax({
            url: '<?= base_url('product/update') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() {
                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
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