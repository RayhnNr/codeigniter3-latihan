<div class="card">
    <div class="card-header">
        <div class="card-tools">
            <button class="btn btn-primary btn-sm" id="btn_add">
                <i class="fas fa-plus"></i> Tambah Akun
            </button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-sm table-bordered table-striped" id="table-coa" style="width:100%">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Kode</th>
                    <th>Nama Akun</th>
                    <th>Jenis</th>
                    <th>Parent</th>
                    <th>Status</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($content as $row): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo html_escape($row->coa_code); ?></td>
                        <td>
                            <?php if ($row->is_header): ?>
                                <strong><?php echo html_escape($row->coa_name); ?></strong>
                            <?php else: ?>
                                <span class="pl-4"><?php echo html_escape($row->coa_name); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo html_escape($row->coa_type); ?></td>
                        <td>
                            <?php echo $row->parent_code
                                ? html_escape($row->parent_code . ' - ' . $row->parent_name)
                                : '-'; ?>
                        </td>
                        <td>
                            <?php if ($row->is_active): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editCoa('<?php echo (int) $row->coa_id; ?>')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php if (!$row->is_header): ?>
                                <button class="btn btn-sm btn-danger" onclick="deleteCoa('<?php echo (int) $row->coa_id; ?>')">
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
<div class="modal fade" id="modal-coa" tabindex="-1">
    <div class="modal-dialog">
        <form id="form-coa" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-coa-title">Tambah Akun</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="coa_id" id="coa_id">

                <div class="form-group">
                    <label>Parent (Header)</label>
                    <select name="parent_id" id="parent_id" class="form-control">
                        <option value="">-- Pilih parent --</option>
                        <?php foreach ($parents as $p): ?>
                            <option value="<?php echo (int) $p->coa_id; ?>">
                                <?php echo html_escape($p->coa_code . ' - ' . $p->coa_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label>Kode Akun</label>
                        <input type="text" name="coa_code" id="coa_code" class="form-control" readonly>
                    </div>
                    <div class="form-group col-6">
                        <label>Jenis Akun</label>
                        <input type="text" name="coa_type" id="coa_type" class="form-control" readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nama Akun</label>
                    <input type="text" name="coa_name" id="coa_name" class="form-control" maxlength="150">
                </div>

                <div class="form-group mb-0">
                    <label for="is_active">Status</label><br>
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>