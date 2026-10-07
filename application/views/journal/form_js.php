<script>
/* BARU: form_js.php — JS khusus halaman form create/edit jurnal */

var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

function updateCsrf(res) {
    if (res && res.csrf_hash) { csrfHash = res.csrf_hash; }
}

/* --------------------------------------------------------------- */
/* DOCUMENT READY                                                   */
/* --------------------------------------------------------------- */
$(function () {
    $('.sel-coa').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: '-- Pilih Akun --',
        allowClear: true
    });
    /* Jika tidak ada baris (mode create), tambah 2 baris awal */
    if ($('#wrapper-detail-rows tr').length === 0) {
        addDetailRow();
        addDetailRow();
    } else {
        reindexRows();
        calcTotal();
    }

    /* Tombol tambah baris */
    $('#btn_add_row').on('click', function () {
        addDetailRow();
    });

    /* Tombol hapus baris (delegasi event) */
    $('#wrapper-detail-rows').on('click', '.btn-delete-row', function () {
        var rowCount = $('#wrapper-detail-rows tr').length;
        if (rowCount <= 2) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Jurnal minimal harus memiliki 2 baris.'
            });
            return;
        }
        var $row = $(this).closest('tr');
        $row.find('.sel-coa').select2('destroy');
        $row.remove();
        reindexRows();
        calcTotal();
    });

    /* Debit → kosongkan kredit di baris yang sama (dan sebaliknya) */
    $('#wrapper-detail-rows').on('input', '.inp-debit', function () {
        if (parseFloat($(this).val()) > 0) {
            $(this).closest('tr').find('.inp-credit').val('');
        }
        calcTotal();
    });

    $('#wrapper-detail-rows').on('input', '.inp-credit', function () {
        if (parseFloat($(this).val()) > 0) {
            $(this).closest('tr').find('.inp-debit').val('');
        }
        calcTotal();
    });

    /* Submit form */
    $('#form-journal').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        /* Validasi balance di browser sebelum kirim */
        var t = calcTotal();
        if (!t.balanced) {
            showError('Debit dan kredit tidak seimbang. Periksa kembali.');
            return;
        }
        if (t.totalDebit <= 0) {
            showError('Total jurnal tidak boleh nol.');
            return;
        }

        /* Hapus baris kosong sebelum kirim */
        cleanEmptyRows();

        /* Konfirmasi SweetAlert */
        Swal.fire({
            title: 'Konfirmasi Simpan',
            text: 'Apakah Anda yakin ingin menyimpan jurnal ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-save mr-1"></i> Ya, Simpan',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(function (result) {
            if (result.isConfirmed) {
                submitJournalForm();
            }
        });
    });
});

/* --------------------------------------------------------------- */
/* Submit AJAX                                                      */
/* --------------------------------------------------------------- */
function submitJournalForm() {
    var $btn = $('#btn_save_submit');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

    var formData = $('#form-journal').serializeArray();
    formData.push({ name: csrfName, value: csrfHash });

    $.ajax({
        url: '<?= base_url("journal/save"); ?>',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function (res) {
            updateCsrf(res);
            $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Jurnal');

            if (res.status) {
                window.location.href = res.redirect || '<?= base_url("journal"); ?>';
            } else {
                showError(res.message || 'Gagal menyimpan.');
            }
        },
        error: function (xhr) {
            $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Jurnal');
            var msg = 'Terjadi kesalahan pada server.';
            try { var r = JSON.parse(xhr.responseText); if (r.message) msg = r.message; } catch (e) {}
            Swal.fire({ icon: 'error', title: 'Error Server', text: msg });
        }
    });
}

/* --------------------------------------------------------------- */
/* Baris detail                                                     */
/* --------------------------------------------------------------- */
function buildSelectOptions(selectedId) {
    var html = '<option value="">-- Pilih Akun --</option>';
    $.each(journalAccounts, function (_, a) {
        var sel = (selectedId && a.id == selectedId) ? 'selected' : '';
        html += '<option value="' + a.id + '" ' + sel + '>' + escHtml(a.text) + '</option>';
    });
    return html;
}

function addDetailRow(data) {
    data = data || {};

    var rowHtml = '<tr class="detail-row">' +
        '<td class="text-center align-middle row-number"></td>' +
        '<td class="align-middle">' +
            '<select name="coa_id[]" class="form-control form-control-sm sel-coa" required>' +
                buildSelectOptions(data.coa_id || '') +
            '</select>' +
        '</td>' +
        '<td class="align-middle">' +
            '<input type="text" name="detail_description[]" class="form-control form-control-sm"' +
            ' maxlength="255" value="' + escHtml(data.description || '') + '">' +
        '</td>' +
        '<td class="align-middle">' +
            '<input type="number" name="debit[]" class="form-control form-control-sm text-right inp-debit"' +
            ' step="0.01" min="0" value="' + (data.debit > 0 ? data.debit : '') + '">' +
        '</td>' +
        '<td class="align-middle">' +
            '<input type="number" name="credit[]" class="form-control form-control-sm text-right inp-credit"' +
            ' step="0.01" min="0" value="' + (data.credit > 0 ? data.credit : '') + '">' +
        '</td>' +
        '<td class="text-center align-middle">' +
            '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-row" title="Hapus Baris">' +
                '<i class="fas fa-trash"></i>' +
            '</button>' +
        '</td>' +
    '</tr>';

    var $row = $(rowHtml).appendTo('#wrapper-detail-rows');
    $row.find('.sel-coa').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: '-- Pilih Akun --',
        allowClear: true
    });
    reindexRows();
    calcTotal();
}

function reindexRows() {
    $('#wrapper-detail-rows tr').each(function (i) {
        $(this).find('.row-number').text(i + 1);
    });
}

function cleanEmptyRows() {
    var $rows = $('#wrapper-detail-rows tr');
    if ($rows.length > 2) {
        $rows.each(function () {
            var coa = $(this).find('.sel-coa').val();
            var d   = parseFloat($(this).find('.inp-debit').val())  || 0;
            var c   = parseFloat($(this).find('.inp-credit').val()) || 0;
            if (!coa && d === 0 && c === 0) {
                $(this).find('.sel-coa').select2('destroy');
                $(this).remove();
            }
        });
        reindexRows();
    }
}

/* --------------------------------------------------------------- */
/* Hitung total & indikator balance                                 */
/* --------------------------------------------------------------- */
function calcTotal() {
    var td = 0, tc = 0;

    $('#wrapper-detail-rows tr').each(function () {
        td += parseFloat($(this).find('.inp-debit').val())  || 0;
        tc += parseFloat($(this).find('.inp-credit').val()) || 0;
    });

    td = Math.round(td * 100) / 100;
    tc = Math.round(tc * 100) / 100;
    var diff = Math.round(Math.abs(td - tc) * 100) / 100;

    $('#foot-debit').text(fmtNum(td));
    $('#foot-credit').text(fmtNum(tc));
    $('#foot-diff').text(fmtNum(diff));

    var balanced = (td > 0) && (tc > 0) && (td === tc);
    var $ind = $('#foot-balance-indicator');
    var $btn = $('#btn_save_submit');

    if (balanced) {
        $ind.removeClass('badge-secondary badge-danger').addClass('badge-success').text('Seimbang');
        $btn.prop('disabled', false);
    } else if (td === 0 && tc === 0) {
        $ind.removeClass('badge-success badge-danger').addClass('badge-secondary').text('—');
        $btn.prop('disabled', true);
    } else {
        $ind.removeClass('badge-secondary badge-success').addClass('badge-danger').text('Tidak Seimbang');
        $btn.prop('disabled', true);
    }

    return { totalDebit: td, totalCredit: tc, balanced: balanced };
}

function fmtNum(n) {
    return n.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

/* --------------------------------------------------------------- */
/* Util                                                             */
/* --------------------------------------------------------------- */
function showError(msg) {
    $('#alert-error-msg').text(msg);
    $('#alert-error-container').removeClass('d-none');
    $('html, body').animate({ scrollTop: $('#alert-error-container').offset().top - 80 }, 300);
}

function clearErrors() {
    $('#alert-error-container').addClass('d-none');
    $('#alert-error-msg').text('');
}

function escHtml(text) {
    if (!text) return '';
    return text.toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
</script>
