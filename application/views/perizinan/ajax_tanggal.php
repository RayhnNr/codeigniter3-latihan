<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Jenis Perizinan</label>
            <div class="form-control bg-light">
                <?= $perizinan->jenis_perizinan_name ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>Status</label>
            <div id="detail-status"></div>
        </div>
    </div>

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

    <div class="col-md-6">
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

    <div class="col-md-12">
        <div class="form-group">
            <label>Alasan</label>
            <div class="form-control bg-light" style="height:auto; min-height:80px;">
                <?= nl2br(htmlspecialchars($perizinan->alasan)) ?>
            </div>
        </div>
    </div>
</div>