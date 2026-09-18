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
        <div class="form-group">
            <label>Tanggal</label>
            <div class="form-control bg-light">
                <?= date('d-m-Y', strtotime($perizinan->tanggal_mulai)) ?>
            </div>
        </div>
        <div class="row">
            <?php if ($perizinan->jenis_perizinan_code === 'GET_PASS'): ?>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jam Mulai</label>
                        <div class="form-control bg-light">
                            <?= $perizinan->jam_mulai ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
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