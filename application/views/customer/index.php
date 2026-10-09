<div class="card">
    <div class="card-header">
        <div class="card-tools">
            <a class="btn btn-primary" href="<?= base_url('customer/form') ?>">
                <i class="fas fa-plus"></i> Tambah Customer
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-sm table-bordered table-striped" id="example1" style="width:100%">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Kode Customer</th>
                    <th>Nama Customer</th>
                    <th>Contact Person</th>
                    <!-- <th>Alamat</th> -->
                    <th>No. Telepon</th>
                    <th>Email</th>
                    <th>Terms Pembayaran</th>
                    <th>Limit Kredit</th>
                    <th>Status</th>
                    <th width="7%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($content as $row): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo html_escape($row->customer_code); ?></td>
                        <td><?php echo html_escape($row->customer_name); ?></td>
                        <td><?php echo html_escape($row->contact_person); ?></td>
                        <!-- <td><?php echo html_escape($row->address); ?></td> -->
                        <td><?php echo html_escape($row->phone); ?></td>
                        <td><?php echo html_escape($row->email); ?></td>
                        <td><?php echo html_escape($row->payment_term_days); ?> hari</td>
                        <td class="text-right"><?php echo number_format((float) $row->credit_limit, 2, ',', '.'); ?></td>
                        <td>
                            <?php if ($row->is_active): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- Edit -->
                            <a class="btn btn-sm btn-info" title="Edit"
                               href="<?= base_url('customer/form/' . $row->customer_id) ?>">
                                <i class="fas fa-edit"></i>
                            </a>

                            <!-- Hapus -->
                            <button class="btn btn-sm btn-danger btn-delete" title="Hapus"
                                    data-id="<?php echo $row->customer_id; ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>