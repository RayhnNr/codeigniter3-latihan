<div class="card">
    <div class="card-header">
        <div class="card-tools">
            <a class="btn btn-primary" href="<?= base_url('sales_order/form') ?>">
                <i class="fas fa-plus"></i> Tambah Sales Order
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-sm table-bordered table-striped" id="example1" style="width:100%">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>No. Sales Order</th>
                    <th>Tanggal Sales Order</th>
                    <th>Tanggal Pengiriman</th>
                    <th>Terms Pembayaran</th>
                    <th>Pajak</th>
                    <th>Catatan</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th width="7%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($content as $row): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo html_escape($row->so_no); ?></td>
                        <td><?php echo html_escape($row->so_date); ?></td>
                        <td><?php echo html_escape($row->delivery_date); ?></td>
                        <td><?php echo html_escape($row->payment_term_days); ?> hari</td>
                        <td><?php echo html_escape($row->tax_percent); ?>%</td>
                        <td><?php echo html_escape($row->notes); ?></td>
                        <td><?php echo html_escape($row->customer_name); ?></td>
                        <td><?php echo html_escape($row->product_status_name); ?></td>
                        <td>
                            <?php if (strtolower($row->product_status_name) === 'draft'): ?>
                                <a class="btn btn-sm btn-info" title="Edit"
                                   href="<?= base_url('sales_order/form/' . $row->sales_order_id) ?>">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger btn-delete" title="Hapus"
                                        data-id="<?php echo (int) $row->sales_order_id; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>