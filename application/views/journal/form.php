<!-- BARU: form.php — halaman create/edit jurnal (gaya budgeting) -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-file-alt text-primary mr-1"></i>
                        <?= html_escape($title); ?>
                    </h3>
                    <?php if (!empty($journal)): ?>
                        <span class="badge badge-secondary">
                            <?= html_escape($journal->journal_no); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <form id="form-journal" autocomplete="off">
                <input type="hidden" name="journal_id" id="journal_id"
                       value="<?= !empty($journal) ? (int) $journal->journal_id : '0'; ?>">

                <div class="card-body">

                    <!-- Alert error -->
                    <div class="alert alert-danger d-none" id="alert-error-container">
                        <h6 class="font-weight-bold">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Terjadi Kesalahan:
                        </h6>
                        <p class="mb-0" id="alert-error-msg"></p>
                    </div>

                    <!-- Header -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="journal_date">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="journal_date" id="journal_date" class="form-control"
                                       value="<?= !empty($journal->journal_date)
                                           ? html_escape($journal->journal_date)
                                           : date('Y-m-d'); ?>">
                                <small class="text-danger" id="error_journal_date"></small>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="journal_description">Keterangan <span class="text-danger">*</span></label>
                                <input type="text" name="description" id="journal_description"
                                       class="form-control" maxlength="255"
                                       placeholder="Keterangan / deskripsi jurnal..."
                                       value="<?= !empty($journal->description) ? html_escape($journal->description) : ''; ?>">
                                <small class="text-danger" id="error_description"></small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Detail Section -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-weight-bold mb-0 text-dark">
                            <i class="fas fa-list mr-1 text-primary"></i> Detail Baris Jurnal
                        </h5>
                        <button type="button" class="btn btn-sm btn-success" id="btn_add_row">
                            <i class="fas fa-plus mr-1"></i> Tambah Baris
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="table-journal-detail">
                            <thead class="thead-light">
                                <tr>
                                    <th width="4%" class="text-center">No</th>
                                    <th width="35%">Akun <span class="text-danger">*</span></th>
                                    <th>Keterangan Baris</th>
                                    <th width="17%" class="text-right">Debit</th>
                                    <th width="17%" class="text-right">Kredit</th>
                                    <th width="5%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="wrapper-detail-rows">
                                <?php if (!empty($detail)): ?>
                                    <?php foreach ($detail as $d): ?>
                                        <tr class="detail-row">
                                            <td class="text-center align-middle row-number"></td>
                                            <td class="align-middle">
                                                <select name="coa_id[]" class="form-control form-control-sm sel-coa select2" required>
                                                    <option value="">-- Pilih Akun --</option>
                                                    <?php foreach ($accounts as $a): ?>
                                                        <option value="<?= (int) $a->coa_id; ?>"
                                                            <?= ((int) $a->coa_id === (int) $d->coa_id) ? 'selected' : ''; ?>>
                                                            <?= html_escape($a->coa_code . ' - ' . $a->coa_name); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td class="align-middle">
                                                <input type="text" name="detail_description[]"
                                                       class="form-control form-control-sm"
                                                       maxlength="255"
                                                       value="<?= html_escape($d->description ?? ''); ?>">
                                            </td>
                                            <td class="align-middle">
                                                <input type="number" name="debit[]"
                                                       class="form-control form-control-sm text-right inp-debit"
                                                       step="0.01" min="0"
                                                       value="<?= ($d->debit > 0) ? (float) $d->debit : ''; ?>">
                                            </td>
                                            <td class="align-middle">
                                                <input type="number" name="credit[]"
                                                       class="form-control form-control-sm text-right inp-credit"
                                                       step="0.01" min="0"
                                                       value="<?= ($d->credit > 0) ? (float) $d->credit : ''; ?>">
                                            </td>
                                            <td class="text-center align-middle">
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row" title="Hapus Baris">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-light font-weight-bold">
                                    <th colspan="3" class="text-right py-3">Total</th>
                                    <th class="text-right py-3 text-primary" id="foot-debit">0,00</th>
                                    <th class="text-right py-3 text-primary" id="foot-credit">0,00</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right text-muted border-0">Selisih</td>
                                    <td colspan="2" class="text-right border-0">
                                        <span id="foot-diff">0,00</span>
                                        &nbsp;
                                        <span id="foot-balance-indicator" class="badge badge-secondary">—</span>
                                    </td>
                                    <td class="border-0"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div><!-- /.card-body -->

                <div class="card-footer d-flex justify-content-end">
                    <a href="<?= base_url('journal'); ?>" class="btn btn-danger mr-2">
                        <i class="fas fa-times mr-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary" id="btn_save_submit">
                        <i class="fas fa-save mr-1"></i> Simpan Jurnal
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Data akun untuk JS (emit sekali dari PHP) -->
<script>
var journalAccounts = <?php
    $acc = [];
    foreach ($accounts as $a) {
        $acc[] = [
            'id'   => (int) $a->coa_id,
            'text' => $a->coa_code . ' - ' . $a->coa_name,
        ];
    }
    echo json_encode($acc, JSON_UNESCAPED_UNICODE);
?>;
</script>
