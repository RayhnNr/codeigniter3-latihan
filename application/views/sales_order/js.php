<script>
var base_url = '<?php echo base_url(); ?>';
var csrf = {
    name: '<?php echo $this->security->get_csrf_token_name(); ?>',
    hash: '<?php echo $this->security->get_csrf_hash(); ?>'
};

function ajaxPost(url, data, done) {
    data += '&' + encodeURIComponent(csrf.name) + '=' + encodeURIComponent(csrf.hash);
    return $.post(base_url + url, data, function (res) {
        if (res.csrf_hash) csrf.hash = res.csrf_hash;
        done(res);
    }, 'json').fail(function () {
        var message = 'Terjadi kesalahan pada server atau sesi berakhir. Muat ulang halaman.';
        if ($('#alert-error').length) {
            showError(message);
        } else {
            Swal.fire('Gagal', message, 'error');
        }
        $('#btn_save_submit').prop('disabled', false);
    });
}

function showError(msg) {
    $('#alert-error').text(msg).removeClass('d-none');
    $('html, body').animate({ scrollTop: 0 }, 200);
}

function normalizeDecimalInput(value, isPrice) {
    value = String(value === null || value === undefined ? '' : value).trim();
    value = value.replace(/[\s\u00a0]/g, '');
    if (isPrice) value = value.replace(/\./g, '');
    return value.replace(',', '.');
}

function numericValue(value, isPrice) {
    var normalized = normalizeDecimalInput(value, isPrice);
    var parsed = parseFloat(normalized);
    return Number.isFinite(parsed) ? parsed : 0;
}

function fmt(n) {
    return 'Rp ' + n.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatPriceInput(input, fromStoredValue) {
    var storedValue = String(input.value);
    var normalized = fromStoredValue
        ? (storedValue.indexOf(',') !== -1
            ? storedValue.replace(/\./g, '').replace(',', '.')
            : storedValue)
        : normalizeDecimalInput(input.value, true);
    if (normalized === '') {
        input.value = '';
        return;
    }
    var amount = Number(normalized);
    if (Number.isFinite(amount)) {
        input.value = amount.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
}

function sanitizeNumericInput(input) {
    var original = input.value;
    var caret = input.selectionStart === null ? original.length : input.selectionStart;
    var isPrice = input.classList.contains('input-price');
    var decimals = parseInt(input.getAttribute('data-decimals'), 10) || 0;
    var prefix = original.slice(0, caret);
    var tokensBeforeCaret = (isPrice ? prefix.replace(/\./g, '') : prefix)
        .replace(/[^0-9,]/g, '').length;

    var value = isPrice ? original.replace(/\./g, '') : original.replace(/\./g, ',');
    value = value.replace(/[^0-9,]/g, '');
    var commaIndex = value.indexOf(',');
    if (commaIndex !== -1) {
        value = value.slice(0, commaIndex + 1) + value.slice(commaIndex + 1).replace(/,/g, '');
        if (decimals === 0) value = value.replace(/,/g, '');
        else value = value.slice(0, commaIndex + 1) + value.slice(commaIndex + 1, commaIndex + 1 + decimals);
    }

    if (isPrice && value !== '') {
        var parts = value.split(',');
        parts[0] = parts[0].replace(/^0+(?=\d)/, '');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        value = parts.join(',');
    }

    input.value = value;
    var newCaret = 0;
    var tokensSeen = 0;
    while (newCaret < value.length && tokensSeen < tokensBeforeCaret) {
        if (/[0-9,]/.test(value.charAt(newCaret))) tokensSeen++;
        newCaret++;
    }
    input.setSelectionRange(newCaret, newCaret);
}

function round2(n) { 
    return Math.round((n + Number.EPSILON) * 100) / 100;
 }

function renumber() {
    $('#wrapper-detail-rows .detail-row').each(function (i) {
        $(this).find('.row-number').text(i + 1);
    });
}

function calcTotals() {
    var gross = 0, net = 0;
    $('#wrapper-detail-rows .detail-row').each(function () {
        var q = numericValue($(this).find('.input-qty').val());
        var p = numericValue($(this).find('.input-price').val(), true);
        var d = numericValue($(this).find('.input-disc').val());
        d = Math.min(Math.max(d, 0), 100);

        var lineNet = round2(q * p * (1 - d / 100));
        gross += round2(q * p);
        net   += lineNet;
        $(this).find('.row-subtotal').text(fmt(lineNet));
    });

    var tax = round2(net * numericValue($('#tax_percent').val()) / 100);
    $('#label-gross').text(fmt(round2(gross)));
    $('#label-discount').text(fmt(round2(gross - net)));
    $('#label-tax').text(fmt(tax));
    $('#label-grand').text(fmt(round2(net + tax)));
}

function addRow() {
    var $row = $($('#row-template').html());
    $('#wrapper-detail-rows').append($row);
    $row.find('.select-product').select2({ 
        theme: 'bootstrap4',
        placeholder: '-- Pilih Produk --',
        width: '100%',
        allowClear: true,
    });
    renumber();
    calcTotals();
}

$(function () {
    // ===== Halaman list =====
    if ($('#example1').length) {
        $('#example1').DataTable({
            scrollX: true,
            autoWidth: false,
            columnDefs: [{ 
                targets: 'no-sort', 
                orderable: false 
            }]
        });

        $('#example1').on('click', '.btn-delete', function () {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Sales Order ini?',
                text: 'Sales Order Draft beserta detailnya akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545'
            }).then(function (result) {
                if (!result.isConfirmed) return;

                ajaxPost('sales_order/delete', $.param({ id: id }), function (res) {
                    if (res.status) {
                        Swal.fire('Berhasil', res.message, 'success').then(function () {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                });
            });
        });
    }

    // ===== Halaman form =====
    if (!$('#form-sales-order').length) return;

    $('.select2').select2(
        {
            theme: 'bootstrap4',
            placeholder: '-- Pilih --',
            width: '100%',
            allowClear: true,
        }
    );
    $('.input-price').each(function () {
        formatPriceInput(this, true);
    });

    if ($('#wrapper-detail-rows .detail-row').length === 0) {
        addRow();
    }
    renumber();
    calcTotals();

    $('#btn_add_row').on('click', addRow);

    $('#wrapper-detail-rows').on('click', '.btn-delete-row', function () {
        if ($('#wrapper-detail-rows .detail-row').length <= 1) {
            alert('Minimal 1 baris produk.');
            return;
        }
        $(this).closest('tr').remove();
        renumber();
        calcTotals();
    });

    $('#form-sales-order').on('focus', '.input-price', function () {
        if (numericValue(this.value, true) === 0) this.select();
    });
    $('#wrapper-detail-rows').on('blur', '.input-price', function () {
        formatPriceInput(this);
    });

    $('#form-sales-order').on('beforeinput', '.numeric-only', function (event) {
        var originalEvent = event.originalEvent;
        if (!originalEvent || !originalEvent.data || !/^insert/.test(originalEvent.inputType)) return;
        var isPrice = this.classList.contains('input-price');
        var decimalAllowed = parseInt(this.getAttribute('data-decimals'), 10) > 0;
        var allowed = isPrice ? /^[0-9,]+$/ : (decimalAllowed ? /^[0-9.,]+$/ : /^[0-9]+$/);
        if (!allowed.test(originalEvent.data)) event.preventDefault();
    });

    $('#form-sales-order').on('input', '.numeric-only', function () {
        sanitizeNumericInput(this);
        calcTotals();
    });

    $('#customer_id').on('change', function () {
        if ($('#sales_order_id').val()) return;
        var term = $(this).find('option:selected').data('term');
        $('#payment_term_days').val(term !== undefined ? term : 0);
    });

    $('#form-sales-order').on('submit', function (e) {
        e.preventDefault();
        $('#alert-error').addClass('d-none');

        $('#wrapper-detail-rows .detail-row').each(function () {
            if (!$(this).find('.select-product').val()) $(this).remove();
        });
        renumber();

        if ($('#wrapper-detail-rows .detail-row').length === 0) {
            addRow();
            showError('Minimal 1 baris produk harus diisi.');
            return;
        }

        $('#btn_save_submit').prop('disabled', true);

        var formData = $(this).serializeArray();
        $.each(formData, function (_, field) {
            if (['payment_term_days', 'tax_percent', 'qty[]', 'unit_price[]', 'discount_percent[]'].indexOf(field.name) !== -1) {
                field.value = normalizeDecimalInput(field.value, field.name === 'unit_price[]');
            }
        });

        ajaxPost('sales_order/save', $.param(formData), function (res) {
            if (res.status) {
                window.location.href = base_url + 'sales_order';
            } else {
                showError(res.message);
                $('#btn_save_submit').prop('disabled', false);
            }
        });
    });

});
</script>