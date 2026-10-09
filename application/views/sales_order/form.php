<?php $so = !empty($sales_order) ? $sales_order : NULL; ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-file-invoice-dollar text-primary mr-1"></i> <?= html_escape($title); ?>
                </h3>
            </div>

            <form id="form-sales-order" autocomplete="off">
                <input type="hidden" name="sales_order_id" id="sales_order_id" value="<?= $so ? (int) $so->sales_order_id : ''; ?>">

                <div class="card-body">
                    <div class="alert alert-danger d-none" id="alert-error"></div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="so_date">Tanggal SO <span class="text-danger">*</span></label>
                                <input type="date" name="so_date" id="so_date" class="form-control"
                                       value="<?= $so ? html_escape($so->so_date) : date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="delivery_date">Tanggal Kirim</label>
                                <input type="date" name="delivery_date" id="delivery_date" class="form-control"
                                       value="<?= $so && $so->delivery_date ? html_escape($so->delivery_date) : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_id">Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" id="customer_id" class="form-control select2" style="width:100%;">
                                    <option value="">-- Pilih Customer --</option>
                                    <?php foreach ($customers as $c): ?>
                                        <option value="<?= (int) $c->customer_id; ?>"
                                                data-term="<?= (int) $c->payment_term_days; ?>"
                                                <?= ($so && (int) $so->customer_id === (int) $c->customer_id) ? 'selected' : ''; ?>>
                                            <?= html_escape($c->customer_code . ' - ' . $c->customer_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="payment_term_days">Termin Bayar (hari) <span class="text-danger">*</span></label>
                                <input type="text" inputmode="numeric" data-decimals="0" name="payment_term_days" id="payment_term_days" class="form-control numeric-only"
                                       value="<?= $so ? (int) $so->payment_term_days : 0; ?>">
                                <small class="text-muted">0 = bayar langsung. Terisi otomatis dari customer.</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tax_percent">PPN (%) <span class="text-danger">*</span></label>
                                <input type="text" inputmode="decimal" data-decimals="2" name="tax_percent" id="tax_percent" class="form-control numeric-only"
                                       value="<?= $so ? (float) $so->tax_percent : 11; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="notes">Catatan</label>
                                <textarea name="notes" id="notes" class="form-control" rows="2"
                                          placeholder="Catatan (opsional)"><?= $so && $so->notes ? html_escape($so->notes) : ''; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-weight-bold mb-0 text-dark">
                            <i class="fas fa-list mr-1 text-primary"></i> Detail Produk
                        </h5>
                        <button type="button" class="btn btn-sm btn-success" id="btn_add_row">
                            <i class="fas fa-plus mr-1"></i> Tambah Baris
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="table-so-detail">
                            <thead class="thead-light">
                                <tr>
                                    <th width="4%" class="text-center">No</th>
                                    <th width="30%">Produk <span class="text-danger">*</span></th>
                                    <th width="9%" class="text-center">Qty <span class="text-danger">*</span></th>
                                    <th width="15%" class="text-right">Harga (Rp) <span class="text-danger">*</span></th>
                                    <th width="9%" class="text-center">Diskon (%)</th>
                                    <th width="15%" class="text-right">Subtotal</th>
                                    <th width="5%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="wrapper-detail-rows">
                                <?php if (!empty($detail)): foreach ($detail as $d): ?>
                                    <tr class="detail-row">
                                        <td class="text-center align-middle row-number"></td>
                                        <td class="align-middle">
                                            <select name="product_id[]" class="form-control form-control-sm select-product select2" style="width:100%;">
                                                <option value="">-- Pilih Produk --</option>
                                                <?php foreach ($products as $p): ?>
                                                    <option value="<?= (int) $p->product_id; ?>"
                                                            <?= ((int) $d->product_id === (int) $p->product_id) ? 'selected' : ''; ?>>
                                                        <?= html_escape($p->product_code . ' - ' . $p->product_name); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="align-middle">
                                            <input type="text" inputmode="decimal" data-decimals="4" name="qty[]" class="form-control form-control-sm text-center input-qty numeric-only"
                                                   value="<?= (float) $d->qty; ?>">
                                        </td>
                                        <td class="align-middle">
                                            <input type="text" inputmode="decimal" data-decimals="2" name="unit_price[]" class="form-control form-control-sm text-right input-price numeric-only"
                                                   value="<?= html_escape($d->unit_price); ?>">
                                        </td>
                                        <td class="align-middle">
                                            <input type="text" inputmode="decimal" data-decimals="2" name="discount_percent[]" class="form-control form-control-sm text-center input-disc numeric-only"
                                                   value="<?= html_escape($d->discount_percent); ?>">
                                        </td>
                                        <td class="text-right align-middle font-weight-bold row-subtotal">0</td>
                                        <td class="text-center align-middle">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row" title="Hapus Baris">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="5" class="text-right">Subtotal (sebelum diskon)</th>
                                    <th class="text-right" id="label-gross">0</th><th></th>
                                </tr>
                                <tr class="bg-light">
                                    <th colspan="5" class="text-right">Total Diskon</th>
                                    <th class="text-right" id="label-discount">0</th><th></th>
                                </tr>
                                <tr class="bg-light">
                                    <th colspan="5" class="text-right">PPN</th>
                                    <th class="text-right" id="label-tax">0</th><th></th>
                                </tr>
                                <tr class="bg-light">
                                    <th colspan="5" class="text-right h5 mb-0">Grand Total</th>
                                    <th class="text-right text-primary h5 mb-0" id="label-grand">0</th><th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end">
                    <a href="<?= base_url('sales_order'); ?>" class="btn btn-danger mr-2">
                        <i class="fas fa-times mr-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary" id="btn_save_submit">
                        <i class="fas fa-save mr-1"></i> Simpan Sales Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Template baris baru (tidak ikut terkirim, karena di dalam <template>) -->
<template id="row-template">
    <tr class="detail-row">
        <td class="text-center align-middle row-number"></td>
        <td class="align-middle">
            <select name="product_id[]" class="form-control form-control-sm select-product" style="width:100%;">
                <option value="">-- Pilih Produk --</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= (int) $p->product_id; ?>">
                        <?= html_escape($p->product_code . ' - ' . $p->product_name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="align-middle">
            <input type="text" inputmode="decimal" data-decimals="4" name="qty[]" class="form-control form-control-sm text-center input-qty numeric-only" value="1">
        </td>
        <td class="align-middle">
            <input type="text" inputmode="decimal" data-decimals="2" name="unit_price[]" class="form-control form-control-sm text-right input-price numeric-only" value="0">
        </td>
        <td class="align-middle">
            <input type="text" inputmode="decimal" data-decimals="2" name="discount_percent[]" class="form-control form-control-sm text-center input-disc numeric-only" value="0">
        </td>
        <td class="text-right align-middle font-weight-bold row-subtotal">0</td>
        <td class="text-center align-middle">
            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row" title="Hapus Baris">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>