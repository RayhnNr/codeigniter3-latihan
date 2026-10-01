<script>
// Token CSRF CI3
var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

function updateCsrf(response) {
    if (response && response.csrf_token && response.csrf_hash) {
        csrfName = response.csrf_token;
        csrfHash = response.csrf_hash;
    }
}

function formatRupiah(number) {
    var val = parseFloat(number) || 0;
    return 'Rp ' + val.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
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

$(function() {
    // Inisialisasi DataTable
    $("#table-budgeting").DataTable({
        "responsive": true,
        "autoWidth": false,
        "columnDefs": [{
            "targets": [0, 6],
            "orderable": false,
        }],
        "order": [[0, "asc"]]
    });
});

// Fungsi untuk membuka Modal Detail Budgeting
function viewDetail(id) {
    Swal.fire({
        title: 'Memuat Rincian...',
        allowOutsideClick: false,
        didOpen: function() {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '<?= base_url("budgeting/get_data/"); ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            Swal.close();
            updateCsrf(response);

            if (response.status === true && response.header) {
                var h = response.header;
                var noBdg = h.budgeting_no || h.budget_no || '-';

                $('#detail-no-budgeting').text(noBdg);
                $('#detail-employee-name').text(h.employee_name || '-');
                $('#detail-department-name').text(h.department_name || '-');
                $('#detail-description').text(h.description ? h.description : '-');

                // Format tanggal
                if (h.budget_date) {
                    var parts = h.budget_date.split('-');
                    if (parts.length === 3) {
                        $('#detail-budget-date').text(parts[2] + '-' + parts[1] + '-' + parts[0]);
                    } else {
                        $('#detail-budget-date').text(h.budget_date);
                    }
                } else {
                    $('#detail-budget-date').text('-');
                }

                // Status Badge
                var statusId   = parseInt(h.status_id || h.product_status_id) || 0;
                var statusName = (h.product_status_name || '').toLowerCase().trim();
                var badgeClass = 'badge-secondary';
                var statusText = h.product_status_name || 'Pending';

                if (statusId === 17 || statusName === 'pending') {
                    badgeClass = 'badge-warning';
                    statusText = 'Pending';
                } else if (statusId === 18 || statusName === 'approved' || statusName === 'disetujui') {
                    badgeClass = 'badge-success';
                    statusText = 'Approved';
                } else if (statusId === 19 || statusName === 'rejected' || statusName === 'reject' || statusName === 'ditolak') {
                    badgeClass = 'badge-danger';
                    statusText = 'Rejected';
                }

                $('#detail-status-badge').html('<span class="badge ' + badgeClass + ' px-2 py-1">' + escapeHtml(statusText) + '</span>');

                // Render Baris Item Detail
                var $tbody = $('#detail-items-tbody');
                $tbody.empty();

                if (response.detail && response.detail.length > 0) {
                    var totalAmount = 0;
                    response.detail.forEach(function(item, idx) {
                        var qty       = parseFloat(item.qty) || 0;
                        var unitPrice = parseFloat(item.unit_price) || 0;
                        var amount    = parseFloat(item.amount) || (qty * unitPrice);
                        totalAmount += amount;

                        var rowHtml = `
                            <tr>
                                <td class="text-center align-middle">${idx + 1}</td>
                                <td class="align-middle font-weight-bold">${escapeHtml(item.item_description)}</td>
                                <td class="align-middle">${escapeHtml(item.payment_type)}</td>
                                <td class="text-center align-middle">${qty}</td>
                                <td class="text-right align-middle">${formatRupiah(unitPrice)}</td>
                                <td class="text-right align-middle font-weight-bold text-dark">${formatRupiah(amount)}</td>
                            </tr>
                        `;
                        $tbody.append(rowHtml);
                    });

                    $('#detail-total-amount').text(formatRupiah(h.total_amount ? h.total_amount : totalAmount));
                } else {
                    $tbody.html('<tr><td colspan="6" class="text-center py-3 text-muted">Tidak ada rincian item.</td></tr>');
                    $('#detail-total-amount').text('Rp 0');
                }

                $('#modal-detail-budgeting').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.message || 'Gagal memuat rincian budgeting.'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat memuat data dari server: ' + error
            });
        }
    });
}

// Fungsi Hapus Budgeting dengan konfirmasi SweetAlert
function deleteBudgeting(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus data budgeting ini? Data yang dihapus tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then(function(result) {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghapus...',
                allowOutsideClick: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });

            var postData = {};
            postData[csrfName] = csrfHash;

            $.ajax({
                url: '<?= base_url("budgeting/delete/"); ?>' + id,
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function(response) {
                    updateCsrf(response);
                    if (response.status === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || 'Data budgeting berhasil dihapus.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus',
                            text: response.message || 'Gagal menghapus data budgeting.'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan pada server saat menghapus: ' + error
                    });
                }
            });
        }
    });
}
</script>