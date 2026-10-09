<?php $is_edit = isset($is_edit) && $is_edit; ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= $is_edit ? 'Edit Customer' : 'Tambah Customer' ?></h3>
    </div>
    <div class="card-body">
        <form action="<?= base_url('customer/save' . ($is_edit ? '/' . $content->customer_id : '')) ?>" method="post">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="customer_name">Nama Customer</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name"
                           value="<?= set_value('customer_name', $content->customer_name) ?>" maxlength="150" required>
                    <?= form_error('customer_name', '<small class="text-danger">', '</small>') ?>
                </div>
                <div class="form-group col-md-6">
                    <label for="contact_person">Contact Person</label>
                    <input type="text" class="form-control" id="contact_person" name="contact_person"
                           value="<?= set_value('contact_person', $content->contact_person) ?>" maxlength="100" required>
                    <?= form_error('contact_person', '<small class="text-danger">', '</small>') ?>
                </div>
            </div>

            <div class="form-group">
                <label for="address">Alamat</label>
                <textarea class="form-control" id="address" name="address" rows="3" maxlength="255"><?= set_value('address', $content->address) ?></textarea>
                <?= form_error('address', '<small class="text-danger">', '</small>') ?>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="phone">No. Telepon</label>
                    <input type="text" class="form-control" id="phone" name="phone"
                           value="<?= set_value('phone', $content->phone) ?>" maxlength="20">
                    <?= form_error('phone', '<small class="text-danger">', '</small>') ?>
                </div>
                <div class="form-group col-md-6">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="<?= set_value('email', $content->email) ?>" maxlength="100">
                    <?= form_error('email', '<small class="text-danger">', '</small>') ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="payment_term_days">Terms Pembayaran (hari)</label>
                    <input type="number" class="form-control" id="payment_term_days" name="payment_term_days"
                           value="<?= set_value('payment_term_days', $content->payment_term_days) ?>" min="0" step="1" required>
                    <?= form_error('payment_term_days', '<small class="text-danger">', '</small>') ?>
                </div>
                <div class="form-group col-md-6">
                    <label for="credit_limit">Limit Kredit</label>
                    <input type="number" class="form-control" id="credit_limit" name="credit_limit"
                           value="<?= set_value('credit_limit', $content->credit_limit) ?>" min="0" step="0.01" required>
                    <?= form_error('credit_limit', '<small class="text-danger">', '</small>') ?>
                </div>
            </div>
            <?php if ($is_edit): ?>
                <div class="form-group">
                    <label>Status Customer</label>
                    <div>
                        <input type="checkbox" id="customer_status" data-on="Aktif" data-off="Nonaktif"
                               data-onstyle="success" data-offstyle="secondary" data-width="120"
                               <?= set_value('is_active', $content->is_active) ? 'checked' : '' ?>>
                        <input type="hidden" name="is_active" id="customer_status_hidden"
                               value="<?= set_value('is_active', $content->is_active) ?>">
                    </div>
                    <?= form_error('is_active', '<small class="text-danger">', '</small>') ?>
                </div>
            <?php endif; ?>

            <a href="<?= base_url('customer') ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?= $is_edit ? 'Perbarui Customer' : 'Simpan Customer' ?>
            </button>
        </form>
    </div>
</div>