<script>
// Token CSRF CI3
var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

var paymentTypes = ['Tunai', 'Transfer', 'Giro', 'Kartu Kredit', 'Kas Kecil'];

function updateCsrf(response) {
    if (response && response.csrf_token && response.csrf_hash) {
        csrfName = response.csrf_token;
        csrfHash = response.csrf_hash;
    }
}

// Format angka dengan pemisah ribuan titik (misal: 1000000 -> 1.000.000)
function formatThousand(val) {
    var str = (val || '').toString().replace(/[^0-9]/g, '');
    if (!str) return '';
    return parseInt(str, 10).toLocaleString('id-ID');
}

// Format Rupiah untuk tampilan
function formatRupiah(number) {
    var val = parseFloat(number) || 0;
    return 'Rp ' + val.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

// Ambil nilai numerik dari string yang berformat titik
function parseFormattedNumber(val) {
    if (!val) return 0;
    var clean = val.toString().replace(/\./g, '').replace(/,/g, '.').replace(/[^0-9.]/g, '');
    return parseFloat(clean) || 0;
}

$(function() {
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
    });
    // Inisialisasi Select2
    $('#employee_id, #department_id, .select-payment-type').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: "Pilih",
        allowClear: true
    });

    // Jika tabel detail masih kosong (mode Create), tambahkan 1 baris awal
    if ($('#wrapper-detail-rows tr').length === 0) {
        addDetailRow();
    } else {
        reindexRows();
        calculateTotal();
    }

    // Tombol tambah baris
    $('#btn_add_row').on('click', function() {
        addDetailRow();
    });

    // Tombol hapus baris
    $('#wrapper-detail-rows').on('click', '.btn-delete-row', function() {
        var rowCount = $('#wrapper-detail-rows tr').length;
        if (rowCount <= 1) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Detail budgeting minimal harus memiliki 1 baris.'
            });
            return;
        }

        $(this).closest('tr').remove();
        reindexRows();
        calculateTotal();
    });

    // Format harga satuan realtime saat mengetik (hanya angka dan titik ribuan)
    $('#wrapper-detail-rows').on('input', '.input-unit-price', function() {
        var cursorPosition = this.selectionStart;
        var rawVal = $(this).val();
        var formatted = formatThousand(rawVal);
        $(this).val(formatted);

        var $row = $(this).closest('tr');
        calculateRowAmount($row);
        calculateTotal();
    });

    // Input qty realtime (angka / desimal)
    $('#wrapper-detail-rows').on('input', '.input-qty', function() {
        var rawVal = $(this).val().replace(/[^0-9.]/g, '');
        $(this).val(rawVal);

        var $row = $(this).closest('tr');
        calculateRowAmount($row);
        calculateTotal();
    });

    // Bersihkan pesan error saat field diisi
    $('#form-budgeting').on('input change', 'input, select, textarea', function() {
        $(this).removeClass('is-invalid');
        var fieldName = $(this).attr('name');
        if (fieldName) {
            $('#error_' + fieldName.replace('[]', '')).text('');
        }
    });

    // Submit form dengan konfirmasi SweetAlert
    $('#form-budgeting').on('submit', function(e) {
        e.preventDefault();
        clearErrors();

        // Bersihkan baris detail kosong
        cleanEmptyDetailRows();

        var rowCount = $('#wrapper-detail-rows tr').length;
        if (rowCount === 0) {
            addDetailRow();
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Detail budgeting minimal harus memiliki 1 baris terisi.'
            });
            return;
        }

        // Munculkan konfirmasi SweetAlert sebelum simpan
        Swal.fire({
            title: 'Konfirmasi Simpan',
            text: 'Apakah Anda yakin ingin menyimpan data budgeting ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-save mr-1"></i> Ya, Simpan',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                submitBudgetingForm();
            }
        });
    });
});

// Proses AJAX Submit
function submitBudgetingForm() {
    var $btnSubmit = $('#btn_save_submit');
    $btnSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

    // Ambil form data dan bersihkan format titik pada unit_price & qty sebelum dikirim
    var formData = $('#form-budgeting').serializeArray();
    for (var i = 0; i < formData.length; i++) {
        if (formData[i].name === 'unit_price[]') {
            formData[i].value = parseFormattedNumber(formData[i].value);
        } else if (formData[i].name === 'qty[]') {
            formData[i].value = parseFormattedNumber(formData[i].value);
        }
    }
    formData.push({ name: csrfName, value: csrfHash });

    $.ajax({
        url: '<?= base_url("budgeting/save"); ?>',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            updateCsrf(response);
            $btnSubmit.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Budgeting');

            if (response.status === true) {
                window.location.href = response.redirect || '<?= base_url("budgeting"); ?>';
                // Swal.fire({
                //     icon: 'success',
                //     title: 'Berhasil',
                //     text: response.message || 'Data budgeting berhasil disimpan.',
                //     timer: 1500,
                //     showConfirmButton: false
                // }).then(function() {
                    
                // });
            } else {
                if (response.errors) {
                    displayValidationErrors(response.errors);
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: response.message || 'Terjadi kesalahan pada validasi formulir.'
                });
            }
        },
        error: function(xhr, status, error) {
            $btnSubmit.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Budgeting');
            Swal.fire({
                icon: 'error',
                title: 'Error Server',
                text: 'Terjadi kesalahan pada sistem: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : error)
            });
        }
    });
}

// Tambah baris detail
function addDetailRow(data) {
    data = data || {};
    var desc      = data.item_description || '';
    var pType     = data.payment_type || 'Tunai';
    var qty       = data.qty !== undefined ? data.qty : '1';
    var rawPrice  = data.unit_price !== undefined ? data.unit_price : '0';
    var formattedPrice = formatThousand(rawPrice);
    var amount    = (parseFormattedNumber(qty) * parseFormattedNumber(formattedPrice));

    var optionsHtml = '';
    paymentTypes.forEach(function(pt) {
        var selected = (pt === pType) ? 'selected' : '';
        optionsHtml += '<option value="' + pt + '" ' + selected + '>' + pt + '</option>';
    });

    var rowHtml = `
        <tr class="detail-row">
            <td class="text-center align-middle row-number"></td>
            <td class="align-middle">
                <input type="text" name="item_description[]" class="form-control form-control-sm input-item-desc" placeholder="Nama / deskripsi item..." value="${escapeHtml(desc)}" required>
            </td>
            <td class="align-middle">
                <select name="payment_type[]" class="form-control form-control-sm select-payment-type" required>
                    ${optionsHtml}
                </select>
            </td>
            <td class="align-middle">
                <input type="text" name="qty[]" class="form-control form-control-sm text-center input-qty" value="${qty}" placeholder="1" required>
            </td>
            <td class="align-middle">
                <input type="text" name="unit_price[]" class="form-control form-control-sm text-right input-unit-price" value="${formattedPrice}" placeholder="0" required>
            </td>
            <td class="text-right align-middle font-weight-bold row-amount">
                ${formatRupiah(amount)}
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row" title="Hapus Baris">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

    var $row = $(rowHtml).appendTo('#wrapper-detail-rows');
    $row.find('.select-payment-type').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Pilih',
        allowClear: true
    });
    reindexRows();
    calculateTotal();
}

// Hitung subtotal baris
function calculateRowAmount($row) {
    var qty = parseFormattedNumber($row.find('.input-qty').val());
    var unitPrice = parseFormattedNumber($row.find('.input-unit-price').val());
    var amount = qty * unitPrice;
    $row.find('.row-amount').text(formatRupiah(amount));
    return amount;
}

// Hitung grand total
function calculateTotal() {
    var total = 0;
    $('#wrapper-detail-rows tr').each(function() {
        var qty = parseFormattedNumber($(this).find('.input-qty').val());
        var unitPrice = parseFormattedNumber($(this).find('.input-unit-price').val());
        total += (qty * unitPrice);
    });
    $('#label-total-amount').text(formatRupiah(total));
}

// Reindex nomor baris
function reindexRows() {
    $('#wrapper-detail-rows tr').each(function(index) {
        $(this).find('.row-number').text(index + 1);
    });
}

// Hapus baris detail yang kosong sebelum submit
function cleanEmptyDetailRows() {
    var $rows = $('#wrapper-detail-rows tr');
    if ($rows.length > 1) {
        $rows.each(function() {
            var desc = $.trim($(this).find('.input-item-desc').val());
            var unitPrice = parseFormattedNumber($(this).find('.input-unit-price').val());
            if (desc === '' && unitPrice === 0) {
                $(this).remove();
            }
        });
        reindexRows();
        calculateTotal();
    }
}

function clearErrors() {
    $('.error-feedback').text('');
    $('.is-invalid').removeClass('is-invalid');
    $('#alert-error-container').addClass('d-none');
    $('#error-list').empty();
}

function displayValidationErrors(errors) {
    $('#error-list').empty();
    var hasError = false;

    $.each(errors, function(key, msg) {
        if (!msg) return;
        hasError = true;
        $('#error-list').append('<li>' + escapeHtml(msg) + '</li>');

        var cleanKey = key.replace('[]', '');
        var $input = $('[name="' + key + '"], [name="' + cleanKey + '"]');
        if ($input.length) {
            $input.addClass('is-invalid');
            $('#error_' + cleanKey).text(msg);
        }
    });

    if (hasError) {
        $('#alert-error-container').removeClass('d-none');
        $('html, body').animate({ scrollTop: $('#alert-error-container').offset().top - 80 }, 300);
    }
}

function escapeHtml(text) {
    if (!text) return '';
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>
