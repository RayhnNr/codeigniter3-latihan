<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-file-invoice-dollar mr-1"></i> <?= html_escape($title); ?>
                    </h3>
                    <a href="<?= base_url('budgeting'); ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            <form id="form-budgeting" autocomplete="off">
                <input type="hidden" name="budgeting_id" id="budgeting_id" value="<?= !empty($budgeting) ? (int) $budgeting->budgeting_id : ''; ?>">

                <div class="card-body">
                    <!-- Alert Error Validasi Server -->
                    <div class="alert alert-danger d-none" id="alert-error-container">
                        <h6 class="font-weight-bold"><i class="fas fa-exclamation-triangle mr-1"></i> Terjadi Kesalahan Validasi:</h6>
                        <ul class="mb-0 pl-3" id="error-list"></ul>
                    </div>

                    <!-- Header Inputs -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="budget_date">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="budget_date" id="budget_date" class="form-control" 
                                    value="<?= !empty($budgeting->budget_date) ? html_escape($budgeting->budget_date) : date('Y-m-d'); ?>" required>
                                <small class="text-danger error-feedback" id="error_budget_date"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="employee_id">Karyawan <span class="text-danger">*</span></label>
                                <select name="employee_id" id="employee_id" class="form-control select2" style="width: 100%;" required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    <?php if (!empty($employees)): ?>
                                        <?php foreach ($employees as $emp): ?>
                                            <?php 
                                                $selected = (!empty($budgeting) && (int) $budgeting->employee_id === (int) $emp->employee_id) ? 'selected' : '';
                                            ?>
                                            <option value="<?= (int) $emp->employee_id; ?>" <?= $selected; ?>>
                                                <?= html_escape($emp->employee_name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="text-danger error-feedback" id="error_employee_id"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="department_id">Departemen <span class="text-danger">*</span></label>
                                <select name="department_id" id="department_id" class="form-control select2" style="width: 100%;" required>
                                    <option value="">-- Pilih Departemen --</option>
                                    <?php if (!empty($departments)): ?>
                                        <?php foreach ($departments as $dept): ?>
                                            <?php 
                                                $selected = (!empty($budgeting) && (int) $budgeting->department_id === (int) $dept->department_id) ? 'selected' : '';
                                            ?>
                                            <option value="<?= (int) $dept->department_id; ?>" <?= $selected; ?>>
                                                <?= html_escape($dept->department_name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="text-danger error-feedback" id="error_department_id"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="description">Keterangan</label>
                                <textarea name="description" id="description" class="form-control" rows="2" placeholder="Catatan / keterangan budgeting (opsional)"><?= !empty($budgeting->description) ? html_escape($budgeting->description) : ''; ?></textarea>
                                <small class="text-danger error-feedback" id="error_description"></small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Detail Section -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="font-weight-bold mb-0 text-dark">
                            <i class="fas fa-list-ol mr-1 text-primary"></i> Detail Item Budgeting
                        </h5>
                        <button type="button" class="btn btn-sm btn-success" id="btn_add_row">
                            <i class="fas fa-plus mr-1"></i> Tambah Baris
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="table-budgeting-detail">
                            <thead class="thead-light">
                                <tr>
                                    <th width="4%" class="text-center">No</th>
                                    <th width="32%">Deskripsi Item <span class="text-danger">*</span></th>
                                    <th width="18%">Jenis Pembayaran <span class="text-danger">*</span></th>
                                    <th width="10%" class="text-center">Qty <span class="text-danger">*</span></th>
                                    <th width="16%" class="text-right">Harga Satuan (Rp) <span class="text-danger">*</span></th>
                                    <th width="15%" class="text-right">Subtotal</th>
                                    <th width="5%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="wrapper-detail-rows">
                                <!-- Baris dinamis akan di-render di sini -->
                                <?php if (!empty($detail)): ?>
                                    <?php $payment_types = ['Tunai', 'Transfer', 'Giro', 'Kartu Kredit', 'Kas Kecil']; ?>
                                    <?php foreach ($detail as $d): ?>
                                        <tr class="detail-row">
                                            <td class="text-center align-middle row-number"></td>
                                            <td>
                                                <input type="text" name="item_description[]" class="form-control form-control-sm input-item-desc" placeholder="Nama / deskripsi item..." value="<?= html_escape($d->item_description); ?>" required>
                                            </td>
                                            <td>
                                                <select name="payment_type[]" class="form-control form-control-sm select-payment-type" required>
                                                    <?php foreach ($payment_types as $pt): ?>
                                                        <option value="<?= $pt; ?>" <?= ($pt === $d->payment_type) ? 'selected' : ''; ?>><?= $pt; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="qty[]" class="form-control form-control-sm text-center input-qty" value="<?= (float) $d->qty; ?>" required>
                                            </td>
                                            <td>
                                                <input type="text" name="unit_price[]" class="form-control form-control-sm text-right input-unit-price" value="<?= number_format($d->unit_price, 0, ',', '.'); ?>" required>
                                            </td>
                                            <td class="text-right align-middle font-weight-bold row-amount">
                                                Rp <?= number_format($d->amount, 0, ',', '.'); ?>
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
                                <tr class="bg-light">
                                    <th colspan="5" class="text-right font-weight-bold py-3">Total Keseluruhan:</th>
                                    <th class="text-right font-weight-bold text-primary py-3 h5 mb-0" id="label-total-amount">
                                        Rp <?= !empty($budgeting->total_amount) ? number_format($budgeting->total_amount, 0, ',', '.') : '0'; ?>
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end">
                    <a href="<?= base_url('budgeting'); ?>" class="btn btn-secondary mr-2">
                        <i class="fas fa-times mr-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary" id="btn_save_submit">
                        <i class="fas fa-save mr-1"></i> Simpan Budgeting
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
