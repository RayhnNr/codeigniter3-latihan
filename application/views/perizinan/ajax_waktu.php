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
            <div>
                <span class="badge badge-primary">
                    <?= ucfirst($perizinan->status) ?>
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>Tanggal</label>
            <div class="form-control bg-light">
                <?= date('d-m-Y', strtotime($perizinan->tanggal_mulai)) ?>
            </div>
        </div>
    </div>

    <?php if ($perizinan->jenis_perizinan_code === 'GET_PASS'): ?>
        <div class="col-md-3">
            <div class="form-group">
                <label>Jam Mulai</label>
                <div class="form-control bg-light">
                    <?= $perizinan->jam_mulai ?>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label>Jam Selesai</label>
                <div class="form-control bg-light">
                    <?= $perizinan->jam_selesai ?>
                </div>
            </div>
        </div>
    <?php elseif ($perizinan->jenis_perizinan_code === 'PULANG_AWAL'): ?>
        <div class="col-md-6">
            <div class="form-group">
                <label>Jam Pulang</label>
                <div class="form-control bg-light">
                    <?= $perizinan->jam_selesai ?>
                </div>
            </div>
        </div>
    <?php elseif ($perizinan->jenis_perizinan_code === 'TERLAMBAT_MASUK_KERJA'): ?>
        <div class="col-md-6">
            <div class="form-group">
                <label>Jam Masuk</label>
                <div class="form-control bg-light">
                    <?= $perizinan->jam_mulai ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-md-12">
        <div class="form-group">
            <label>Alasan</label>
            <div class="form-control bg-light" style="height:auto; min-height:80px;">
                <?= nl2br(htmlspecialchars($perizinan->alasan)) ?>
            </div>
        </div>
    </div>
</div>