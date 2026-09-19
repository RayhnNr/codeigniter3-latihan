<div class="card">
    <div class="card-header">
        <div class="card-tools">
            <a href="<?= base_url('perizinan/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Pengajuan
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3 align-items-end">
            <div class="col-md-3 mb-2">
                <label for="filter_jenis_perizinan" class="small mb-1">Jenis Perizinan</label>
                <select id="filter_jenis_perizinan" class="form-control select2">
                    <option value="">-- Semua Jenis Perizinan --</option>
                    <?php foreach ($jenis_perizinan as $p): ?>
                        <option value="<?= $p->jenis_perizinan_id ?>"><?= $p->jenis_perizinan_name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <label for="filter_date_range" class="small mb-1">Periode Tanggal</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fa fa-calendar"></i>
                        </span>
                    </div>
                    <input type="text" id="filter_date_range" class="form-control" autocomplete="off">
                </div>
            </div>
            <div class="col-md-1 mb-2">
                <button id="btn_filter" class="btn btn-primary btn-block">
                    <i class="fa fa-filter"></i> Filter
                </button>
            </div>
        </div>
        <table id="table-perizinan" class="table table-bordered table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. Pengajuan</th>
                    <th>Jenis</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">
                    <i class="fas fa-file-alt mr-2"></i> Detail Perizinan
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" id="detailPerizinan">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

