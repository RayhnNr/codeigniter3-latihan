<script>
/* DIUBAH: js.php — JS halaman list jurnal */
/* base_url diperiksa agar tidak bentrok dengan template */
if (typeof base_url === 'undefined') {
    var base_url = <?php echo json_encode(base_url()); ?>;
}

var csrfName = <?php echo json_encode($this->security->get_csrf_token_name()); ?>;
var csrfHash = <?php echo json_encode($this->security->get_csrf_hash()); ?>;

/* Helper: POST AJAX dengan CSRF */
function ajaxPost(url, data, callback) {
    data[csrfName] = csrfHash;
    $.ajax({
        url: base_url + url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function (res) {
            if (res.csrf_hash) { csrfHash = res.csrf_hash; }
            callback(res);
        },
        error: function (xhr) {
            var msg = 'Terjadi kesalahan pada server.';
            try { var r = JSON.parse(xhr.responseText); if (r.message) msg = r.message; } catch(e){}
            Swal.fire({ icon: 'error', title: 'Error', text: msg });
        }
    });
}

$(function () {

    /* DataTable */
    $('#table-journal').DataTable({
        responsive  : true,
        autoWidth   : false,
        order       : [],
        columnDefs  : [
            { orderable: false, targets: [0, 7] }  /* No & Aksi */
        ],
        language: {
            url: base_url + 'assets/adminlte/plugins/datatables/i18n/Indonesian.json'
        }
    });

});

/* --------------------------------------------------------------- */
/* LIHAT DETAIL — modal tabel saja                                  */
/* --------------------------------------------------------------- */
function viewJournal(id) {
    /* Reset state modal */
    $('#view-journal-no, #view-date, #view-description').text('—');
    $('#view-status-badge').text('—').removeClass('badge-warning badge-success badge-secondary');
    $('#view-detail-tbody').empty();
    $('#view-foot-debit, #view-foot-credit').text('0,00');
    $('#view-detail-wrap').addClass('d-none');
    $('#view-loading').removeClass('d-none');
    $('#view-error').addClass('d-none');

    $('#modal-view-journal').modal('show');

    $.get(base_url + 'journal/get_data/' + id, function (res) {
        $('#view-loading').addClass('d-none');

        if (!res.status) {
            $('#view-error-msg').text(res.message || 'Data tidak ditemukan.');
            $('#view-error').removeClass('d-none');
            return;
        }

        var h = res.header;

        /* Isi info header */
        $('#view-journal-no').text(h.journal_no || '');
        $('#view-date').text(h.journal_date ? formatTanggal(h.journal_date) : '—');
        $('#view-description').text(h.description || '—');

        var isDraft = res.is_draft;
        $('#view-status-badge')
            .text(h.status_name || '—')
            .addClass(isDraft ? 'badge-warning' : 'badge-success');

        /* Isi baris tabel detail */
        var td = 0, tc = 0;
        if (res.detail && res.detail.length > 0) {
            $.each(res.detail, function (i, d) {
                var debit  = parseFloat(d.debit)  || 0;
                var credit = parseFloat(d.credit) || 0;
                td += debit;
                tc += credit;

                var tr = '<tr>' +
                    '<td class="text-center">' + (i + 1) + '</td>' +
                    '<td><strong>' + escHtml(d.coa_code || '') + '</strong> — ' + escHtml(d.coa_name || '') + '</td>' +
                    '<td>' + escHtml(d.description || '') + '</td>' +
                    '<td class="text-right">' + (debit  > 0 ? fmtNum(debit)  : '') + '</td>' +
                    '<td class="text-right">' + (credit > 0 ? fmtNum(credit) : '') + '</td>' +
                '</tr>';
                $('#view-detail-tbody').append(tr);
            });
        } else {
            $('#view-detail-tbody').append('<tr><td colspan="5" class="text-center text-muted">Tidak ada detail</td></tr>');
        }

        /* Footer total */
        $('#view-foot-debit').text(fmtNum(Math.round(td * 100) / 100));
        $('#view-foot-credit').text(fmtNum(Math.round(tc * 100) / 100));

        $('#view-detail-wrap').removeClass('d-none');

    }, 'json').fail(function () {
        $('#view-loading').addClass('d-none');
        $('#view-error-msg').text('Terjadi kesalahan saat memuat data.');
        $('#view-error').removeClass('d-none');
    });
}

/* --------------------------------------------------------------- */
/* POSTING                                                          */
/* --------------------------------------------------------------- */
function postJournal(id) {
    Swal.fire({
        title : 'Posting Jurnal?',
        text  : 'Jurnal yang sudah diposting tidak dapat diubah atau dihapus.',
        icon  : 'warning',
        showCancelButton : true,
        confirmButtonText: 'Ya, Posting',
        cancelButtonText : 'Batal',
        reverseButtons   : true,
        confirmButtonColor: '#28a745'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        ajaxPost('journal/post/' + id, {}, function (res) {
            if (res.status) {
                Swal.fire({
                    icon: 'success', title: 'Berhasil',
                    text: res.message, timer: 1500, showConfirmButton: false
                });
                setTimeout(function () { location.reload(); }, 1600);
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
            }
        });
    });
}

/* --------------------------------------------------------------- */
/* HAPUS                                                            */
/* --------------------------------------------------------------- */
function deleteJournal(id) {
    Swal.fire({
        title : 'Hapus Jurnal?',
        text  : 'Data yang dihapus tidak dapat dikembalikan.',
        icon  : 'warning',
        showCancelButton : true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText : 'Batal',
        reverseButtons   : true,
        confirmButtonColor: '#dc3545'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        ajaxPost('journal/delete/' + id, {}, function (res) {
            if (res.status) {
                Swal.fire({
                    icon: 'success', title: 'Dihapus',
                    text: res.message, timer: 1500, showConfirmButton: false
                });
                setTimeout(function () { location.reload(); }, 1600);
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
            }
        });
    });
}

/* --------------------------------------------------------------- */
/* Util                                                             */
/* --------------------------------------------------------------- */
function fmtNum(n) {
    return parseFloat(n).toLocaleString('id-ID', {
        minimumFractionDigits: 2, maximumFractionDigits: 2
    });
}

function formatTanggal(ymd) {
    /* yyyy-mm-dd → dd-mm-yyyy */
    var parts = ymd.split('-');
    if (parts.length === 3) return parts[2] + '-' + parts[1] + '-' + parts[0];
    return ymd;
}

function escHtml(text) {
    if (!text) return '';
    return text.toString()
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
</script>