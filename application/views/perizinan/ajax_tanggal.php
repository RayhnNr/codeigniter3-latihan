<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label>No Perizinan</label>
            <div class="form-control bg-light">
                <?= $perizinan->perizinan_no ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Jenis Perizinan</label>
            <div class="form-control bg-light">
                <?= $perizinan->jenis_perizinan_name ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tanggal Mulai</label>
                    <div class="form-control bg-light">
                        <?= date('d-m-Y', strtotime($perizinan->tanggal_mulai)) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tanggal Selesai</label>
                    <div class="form-control bg-light">
                        <?= date('d-m-Y', strtotime($perizinan->tanggal_selesai)) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Durasi</label>
            <div class="input-group">
                <div class="form-control bg-light">
                    <?= $perizinan->durasi ?>
                </div>
                <div class="input-group-append">
                    <span class="input-group-text">hari</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>Status Pengajuan</label>
            <div class="border rounded bg-light p-3 d-flex align-items-center">
                <span class="mr-3">
                    <i class="fas fa-info-circle text-muted"></i>
                </span>
                <div>
                    <div id="detail-status"></div>
                    <small class="text-muted d-block mt-1">
                        Status pengajuan saat ini.
                    </small>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Alasan</label>
            <div class="form-control bg-light" style="height:auto; min-height:80px;">
                <?= nl2br(htmlspecialchars($perizinan->alasan)) ?>
            </div>
        </div>
    </div>
</div>