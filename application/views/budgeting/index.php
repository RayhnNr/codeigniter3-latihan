<div class="card">
    <div class="card-header">
        <h3 class="card-title font-weight-bold">
            <i class="fas fa-wallet mr-1"></i> Data Budgeting
        </h3>
        <div class="card-tools">
            <a href="<?= base_url('budgeting/create'); ?>" class="btn btn-primary btn-sm" id="btn_add">
                <i class="fas fa-plus mr-1"></i> Tambah Budgeting
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover" id="table-budgeting" style="width:100%">
            <thead class="thead-light">
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="18%">No Budgeting</th>
                    <th>Nama Karyawan</th>
                    <th width="12%">Tanggal</th>
                    <th class="text-right" width="16%">Nominal</th>
                    <th width="10%" class="text-center">Status</th>
                    <th width="12%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($content as $row): ?>
                    <?php 
                        $statusId   = (int) ($row->status_id ?? $row->product_status_id ?? 0);
                        $statusName = strtolower(trim($row->product_status_name ?? ''));

                        // Status approval: 17 = Pending, 18 = Approved, 19 = Rejected
                        if ($statusId === 17 || $statusName === 'pending') {
                            $badgeClass    = 'badge-warning';
                            $displayStatus = 'Pending';
                            $canAction     = true; // Tombol aksi aktif
                        } elseif ($statusId === 18 || in_array($statusName, ['approved', 'disetujui'])) {
                            $badgeClass    = 'badge-success';
                            $displayStatus = 'Approved';
                            $canAction     = false; // Tombol aksi hilang
                        } elseif ($statusId === 19 || in_array($statusName, ['rejected', 'reject', 'ditolak'])) {
                            $badgeClass    = 'badge-danger';
                            $displayStatus = 'Rejected';
                            $canAction     = false; // Tombol aksi hilang
                        } else {
                            $badgeClass    = 'badge-secondary';
                            $displayStatus = !empty($row->product_status_name) ? $row->product_status_name : 'Pending';
                            $canAction     = true;
                        }
                        $noBdg = html_escape($row->budgeting_no ?? $row->budget_no ?? '-');
                    ?>
                    <tr>
                        <td class="text-center align-middle"><?php echo $no++; ?></td>
                        <td class="align-middle">
                            <!-- Nomor Budgeting berbentuk Button btn-block untuk membuka modal detail -->
                            <button type="button" class="btn btn-primary btn-sm btn-block font-weight-bold text-left" 
                                onclick="viewDetail('<?php echo (int) $row->budgeting_id; ?>')" title="Klik untuk lihat detail">
                                <i class="fas fa-file-invoice mr-1"></i> <?php echo $noBdg; ?>
                            </button>
                        </td>
                        <td class="align-middle"><?php echo html_escape($row->employee_name ?? '-'); ?></td>
                        <td class="align-middle"><?php echo !empty($row->budget_date) ? date('d-m-Y', strtotime($row->budget_date)) : '-'; ?></td>
                        <td class="text-right align-middle font-weight-bold">Rp <?php echo number_format($row->total_amount ?? 0, 0, ',', '.'); ?></td>
                        <td class="text-center align-middle">
                            <span class="badge <?php echo $badgeClass; ?> px-2 py-1"><?php echo html_escape($displayStatus); ?></span>
                        </td>
                        <td class="text-center align-middle">
                            <?php if ($canAction): ?>
                                <a href="<?php echo base_url('budgeting/edit/' . (int) $row->budgeting_id); ?>" class="btn btn-sm btn-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteBudgeting('<?php echo (int) $row->budgeting_id; ?>')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail Budgeting -->
<div class="modal fade" id="modal-detail-budgeting" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="modalDetailLabel">
                    <i class="fas fa-file-invoice-dollar mr-1"></i> Detail Budgeting: <span id="detail-no-budgeting">-</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Header Info Box -->
                <div class="card card-outline card-secondary mb-3 shadow-none border">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mb-2">
                                <label class="text-muted small mb-0 d-block">Tanggal Budgeting</label>
                                <span class="font-weight-bold text-dark" id="detail-budget-date">-</span>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <label class="text-muted small mb-0 d-block">Nama Karyawan</label>
                                <span class="font-weight-bold text-dark" id="detail-employee-name">-</span>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <label class="text-muted small mb-0 d-block">Departemen</label>
                                <span class="font-weight-bold text-dark" id="detail-department-name">-</span>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <label class="text-muted small mb-0 d-block">Status</label>
                                <span id="detail-status-badge">-</span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <label class="text-muted small mb-0 d-block">Keterangan / Catatan</label>
                                <span class="text-dark" id="detail-description">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Items Table -->
                <h6 class="font-weight-bold mb-2 text-dark">
                    <i class="fas fa-list-ul mr-1 text-primary"></i> Rincian Item Budgeting
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover" id="table-detail-items">
                        <thead class="thead-light">
                            <tr>
                                <th width="4%" class="text-center">No</th>
                                <th>Deskripsi Item</th>
                                <th width="18%">Jenis Pembayaran</th>
                                <th width="10%" class="text-center">Qty</th>
                                <th width="18%" class="text-right">Harga Satuan</th>
                                <th width="20%" class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detail-items-tbody">
                            <!-- Diisi via AJAX -->
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th colspan="5" class="text-right font-weight-bold py-2">Total Amount:</th>
                                <th class="text-right font-weight-bold text-primary py-2 h6 mb-0" id="detail-total-amount">Rp 0</th>
                            </tr>
                        </tfoot>
                    </table>
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