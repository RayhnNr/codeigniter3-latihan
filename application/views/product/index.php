<div class="card">
    <div class="card-header">
        <div class="card-tools">
            <a href="<?= base_url('product/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Product
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3 align-items-end">
            <div class="col-md-2">
                <label for="filter_status" class="small mb-1">Status</label>
                <select id="filter_status" class="form-control select2">
                    <option value="">-- Semua Status --</option>
                    <?php foreach ($product_status as $ps): ?>
                        <option value="<?= $ps->product_status_id ?>"><?= $ps->product_status_name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_category" class="small mb-1">Category</label>
                <select id="filter_category" class="form-control select2" style="width: 100%;">
                    <option value="">-- Semua Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category->category_id ?>"><?= $category->category_name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_brand" class="small mb-1">Brand</label>
                <select id="filter_brand" class="form-control select2" style="width: 100%;">
                    <option value="">-- Semua Brand --</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?= $brand->brand_id ?>"><?= $brand->brand_name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filter_type" class="small mb-1">Type</label>
                <select id="filter_type" class="form-control select2" style="width: 100%;">
                    <option value="">-- Semua Type --</option>
                    <?php foreach ($product_type as $type): ?>
                        <option value="<?= $type->product_type_id ?>"><?= $type->product_type_name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button id="btn_filter" class="btn btn-primary w-100">
                    <i class="fa fa-filter"></i> Filter
                </button>
            </div>
        </div>
        <table id="table-product" class="table table-bordered table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Code Product</th>
                    <th>Nama Product</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <!-- <th>Unit</th>
                    <th>Type</th> -->
                    <th>Barcode</th>
                    <th>Status</th>
                    <th>Dibuat Oleh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-box mr-2"></i> Detail Product 
                    <span class="badge badge-light ml-2" id="detail-code-header"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <th width="150" class="text-muted">Code Product</th>
                                <td width="10">:</td>
                                <td id="detail-code" class="font-weight-bold"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Nama Product</th>
                                <td>:</td>
                                <td id="detail-name" class="font-weight-bold"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Category</th>
                                <td>:</td>
                                <td id="detail-category"></td>
                            </tr>
                            
                            <tr>
                                <th class="text-muted">Brand</th>
                                <td>:</td>
                                <td id="detail-brand"></td>
                            </tr>
                            <tr>
                            <th class="text-muted">Unit</th>
                                <td>:</td>
                                <td id="detail-unit"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Product Type</th>
                                <td>:</td>
                                <td id="detail-type"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Status</th>
                                <td>:</td>
                                <td id="detail-status"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Dibuat Oleh</th>
                                <td>:</td>
                                <td id="detail-create-by"></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <hr class="my-2">
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-light py-2">
                                <strong>
                                    <i class="fas fa-qrcode text-primary mr-1"></i>
                                    QR Code
                                </strong>
                            </div>

                            <div class="card-body text-center py-3">
                                <div id="detail-qrcode" class="d-flex justify-content-center"></div>
                                <small class="text-muted d-block mt-2">
                                    Scan QR Code untuk melihat detail produk
                                </small>
                            </div>
                        </div>

                        <div class="card shadow-sm">
                            <div class="card-header bg-light py-2">
                                <strong>
                                    <i class="fas fa-align-left text-primary mr-1"></i>
                                    Deskripsi
                                </strong>
                            </div>

                            <div class="card-body">
                                <div id="detail-deskripsi" class="text-muted" style="line-height: 1.7;"></div>
                            </div>
                        </div>

                    </div>
                </div>
                
                <!-- ===== TAMBAHAN: GALERI FOTO ===== -->
                <div class="card mt-3">
                    <div class="card-header bg-light py-2">
                        <strong><i class="fas fa-images mr-1"></i> Foto Produk</strong>
                    </div>
                    <div class="card-body py-2">
                        <div class="row" id="detail-images-wrapper">
                            <!-- diisi otomatis via JS -->
                        </div>
                        <p class="text-muted mb-0 d-none" id="detail-no-image">Belum ada foto produk.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
window.addEventListener('load', function () {
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    var table = $('#table-product').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        order: [],
        ajax: {
            url: '<?= base_url('product/get_data') ?>',
            type: 'POST',
            data: function(data){
                data.status         = $('#filter_status').val();
                data.category_id    = $('#filter_category').val();
                data.brand_id       = $('#filter_brand').val();
                data.product_type   = $('#filter_type').val();
            }
        },
        columns: [
            { data: 'no', orderable: false, searchable: false },
            { data: null,
                render: function (data, type, row){
                    return `<button type="button" class="btn btn-sm btn-block btn-info" onclick="detailData('${row.product_id}')"><i class="fas fa-eye"></i> ${row.product_code}</button>`;
                }
            },
            // { data: 'product_code' },
            { data: 'product_name' },
            { data: 'category_name' },
            { data: 'brand_name' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<div class="row-qrcode" data-code="${row.product_code}" id="qrcode-${row.product_id}"></div>`;
                }
            },
            {
                data: 'status',
                render: function(data, type, row) {
                    return data == 1
                        ? '<span class="badge badge-success">Aktif</span>'
                        : '<span class="badge badge-danger">Tidak Aktif</span>';
                }
            },
            {
                data: 'created_by_username',
                render: function(data, type, row) {
                    return data || '-';
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                width: "150px",
                render: function (data, type, row) {
                    return `
                        <div class="d-flex flex-wrap mb-n2">
                            <a href="<?= base_url('product/edit/') ?>${row.product_id}" class="btn btn-sm btn-warning mr-2 mb-2">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-danger btn-delete" onclick="tombolDelete(this)" data-id="${row.product_id}" data-code="${row.product_code}">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>`;
                }
            }
        ],
        drawCallback: function() {
            $('.row-qrcode').each(function() {
                var code = $(this).data('code');

                $(this).empty();

                try {
                    new QRCode(this, {
                        text: code,
                        width: 80,
                        height: 80
                    });
                } catch (e) {
                    console.error('Gagal generate QR Code untuk', code);
                }
            });
        }
    });

    $('#btn_filter').on('click', function(){
        table.ajax.reload();
    });

    // taruh function delete di dalam sini supaya bisa akses variabel 'table'
    window.tombolDelete = function(btn) {
        const id = $(btn).data('id');
        const code = $(btn).data('code');

        Swal.fire({
            title: 'Hapus product ini?',
            text: 'Product ' + code + ' akan dihapus permanen dan tidak bisa dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('product/delete') ?>',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Menghapus...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Berhasil', response.message, 'success');
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Gagal', 'Terjadi kesalahan pada server', 'error');
                    }
                });
            }
        });
    };
});

function detailData(id) {
    $.ajax({
        url: '<?= base_url('product/get_detail/') ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            const data = response.product;
            const images = response.product_images;

            $('#detail-code-header').text(data.product_code);
            $('#detail-code').text(data.product_code);
            $('#detail-name').text(data.product_name);
            $('#detail-category').text(data.category_name);
            $('#detail-brand').text(data.brand_name);
            $('#detail-unit').text(data.unit_name);
            $('#detail-type').text(data.product_type_name);

            const statusBadge = data.status == 1
                ? '<span class="badge badge-success">Aktif</span>'
                : '<span class="badge badge-danger">Tidak Aktif</span>';
            $('#detail-status').html(statusBadge);

            $('#detail-create-by').text(data.created_by_username ? data.created_by_username : '-');
            $('#detail-deskripsi').text(data.description);
            try {
                $('#detail-qrcode').empty();

                new QRCode(document.getElementById('detail-qrcode'), {
                    text: data.product_code,
                    width: 150,
                    height: 150
                });
            } catch (e) {
                console.error('Gagal generate QR Code:', e);
                $('#detail-qrcode').closest('.card').hide();
            }

            // ===== TAMBAHAN: render galeri gambar =====
            const $wrapper = $('#detail-images-wrapper');
            $wrapper.empty();

            if (images && images.length > 0) {
                $('#detail-no-image').addClass('d-none');

                // urutkan: gambar primary duluan, sisanya ikut sort_order
                var sortedImages = images.slice().sort(function(a, b) {
                    if (a.is_primary != b.is_primary) {
                        return b.is_primary - a.is_primary;
                    }
                    return (a.sort_order || 0) - (b.sort_order || 0);
                });

                sortedImages.forEach(function(img) {
                    var primaryBadge = img.is_primary == 1
                        ? '<span class="badge badge-success position-absolute" style="top:5px; right:5px;">Utama</span>'
                        : '';

                    $wrapper.append(`
                        <div class="col-4 col-md-3 mb-3">
                            <div class="position-relative">
                                <img src="<?= base_url('uploads/products/') ?>${img.file_name}" 
                                     class="img-thumbnail w-100" 
                                     style="height: 100px; object-fit: cover; cursor: pointer;"
                                     onclick="window.open(this.src, '_blank')">
                                ${primaryBadge}
                            </div>
                        </div>
                    `);
                });
            } else {
                $('#detail-no-image').removeClass('d-none');
            }

            $('#modalDetail').modal('show');
        },
        error: function () {
            Swal.fire('Gagal', 'Data tidak ditemukan', 'error');
        }
    });
}


</script>