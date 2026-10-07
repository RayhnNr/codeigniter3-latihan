<!-- DIUBAH: index.php — tabel list jurnal + modal LIHAT (tabel saja, tanpa form) -->
<div class="card">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title mr-auto">Daftar Jurnal</h3>
        <div class="card-tools">
            <a href="<?= base_url('journal/create'); ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Jurnal
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-sm table-bordered table-striped" id="table-journal" style="width:100%">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th>No Jurnal</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th class="text-right">Total Debit</th>
                    <th class="text-right">Total Kredit</th>
                    <th>Status</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($content as $row): ?>
                    <?php
                        $is_draft  = (strtolower($row->product_status_name) === 'draft');
                        $jid       = (int) $row->journal_id;
                        $badge_cls = $is_draft ? 'badge-warning' : 'badge-success';
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo html_escape($row->journal_no); ?></td>
                        <td><?php echo date('d-m-Y', strtotime($row->journal_date)); ?></td>
                        <td><?php echo html_escape($row->description); ?></td>
                        <td class="text-right"><?php echo number_format((float) $row->total_debit,  2, ',', '.'); ?></td>
                        <td class="text-right"><?php echo number_format((float) $row->total_credit, 2, ',', '.'); ?></td>
                        <td>
                            <span class="badge <?php echo $badge_cls; ?>">
                                <?php echo html_escape($row->product_status_name); ?>
                            </span>
                        </td>
                        <td>
                            <!-- Lihat: selalu tampil (buka modal tabel) -->
                            <button class="btn btn-sm btn-secondary" title="Lihat Detail"
                                    onclick="viewJournal('<?php echo $jid; ?>')">
                                <i class="fas fa-eye"></i>
                            </button>

                            <?php if ($is_draft): ?>
                                <!-- Edit: link ke halaman form -->
                                <a href="<?php echo base_url('journal/edit/' . $jid); ?>"
                                   class="btn btn-sm btn-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <!-- Post -->
                                <button class="btn btn-sm btn-success" title="Posting"
                                        onclick="postJournal('<?php echo $jid; ?>')">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                                <!-- Hapus -->
                                <button class="btn btn-sm btn-danger" title="Hapus"
                                        onclick="deleteJournal('<?php echo $jid; ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===== MODAL LIHAT JURNAL (tabel detail saja, tanpa form) ===== -->
<div class="modal fade" id="modal-view-journal" tabindex="-1" role="dialog"
     aria-labelledby="modal-view-title">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modal-view-title">
                    <i class="fas fa-file-alt mr-1"></i>
                    Detail Jurnal — <span id="view-journal-no"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- Info header jurnal -->
                <div class="row mb-3" id="view-header-info">
                    <div class="col-md-3">
                        <label class="text-muted small mb-0">Tanggal</label>
                        <p class="font-weight-bold mb-1" id="view-date">—</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-0">Keterangan</label>
                        <p class="font-weight-bold mb-1" id="view-description">—</p>
                    </div>
                    <div class="col-md-3 text-right">
                        <label class="text-muted small mb-0">Status</label><br>
                        <span id="view-status-badge" class="badge">—</span>
                    </div>
                </div>

                <hr class="mt-0">

                <!-- Loading spinner -->
                <div class="text-center py-4" id="view-loading">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2 text-muted">Memuat data...</p>
                </div>

                <!-- Tabel detail -->
                <div class="table-responsive d-none" id="view-detail-wrap">
                    <table class="table table-sm table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th width="4%">No</th>
                                <th>Akun</th>
                                <th>Keterangan Baris</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Kredit</th>
                            </tr>
                        </thead>
                        <tbody id="view-detail-tbody"></tbody>
                        <tfoot>
                            <tr class="table-active font-weight-bold">
                                <th colspan="3" class="text-right">Total</th>
                                <th class="text-right" id="view-foot-debit">0,00</th>
                                <th class="text-right" id="view-foot-credit">0,00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Error -->
                <div class="alert alert-danger d-none" id="view-error">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <span id="view-error-msg"></span>
                </div>

            </div><!-- /.modal-body -->

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
</div>