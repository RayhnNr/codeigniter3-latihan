<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-signature mr-2"></i>Tambah Perizinan</h3>
    </div>
    <div class="card-body">
        <form action="<?= site_url('perizinan/save') ?>" method="post" enctype="multipart/form-data" id="form-perizinan" novalidate>
            <div id="perizinan-fields" class="row">
                <div class="col-lg-6 pr-lg-4">
                    <div class="form-group">
                        <label for="jenis_perizinan_id">Jenis Perizinan <span class="text-danger">*</span></label>
                        <select name="jenis_perizinan_id" id="jenis_perizinan_id" class="form-control form-select2" data-placeholder="Pilih Jenis Perizinan">
                            <option value=""></option>
                            <?php foreach ($jenis_perizinan as $item): ?>
                                <option value="<?= (int) $item->jenis_perizinan_id ?>" <?= set_select('jenis_perizinan_id', $item->jenis_perizinan_id) ?>>
                                    <?= html_escape($item->jenis_perizinan_name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-danger" id="error_jenis_perizinan_id"></small>
                    </div>
                    <div class="row d-none">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_mulai">Tanggal Mulai <span class="text-danger">*</span></label>
                                <div class="input-group date" id="tanggal_mulai_picker" data-target-input="nearest">
                                    <input type="text" name="tanggal_mulai" id="tanggal_mulai" class="form-control datetimepicker-input" value="<?= set_value('tanggal_mulai') ?>" data-target="#tanggal_mulai_picker" autocomplete="off" placeholder="YYYY-MM-DD">
                                    <div class="input-group-append" data-target="#tanggal_mulai_picker" data-toggle="datetimepicker"><div class="input-group-text"><i class="far fa-calendar-alt"></i></div></div>
                                </div>
                                <small class="text-danger" id="error_tanggal_mulai"></small>
                            </div>
                        </div>
                        <div class="col-md-6" id="field_tanggal_selesai">
                            <div class="form-group">
                                <label for="tanggal_selesai">Tanggal Selesai <span class="text-danger">*</span></label>
                                <div class="input-group date" id="tanggal_selesai_picker" data-target-input="nearest">
                                    <input type="text" name="tanggal_selesai" id="tanggal_selesai" class="form-control datetimepicker-input" value="<?= set_value('tanggal_selesai') ?>" data-target="#tanggal_selesai_picker" autocomplete="off" placeholder="YYYY-MM-DD">
                                    <div class="input-group-append" data-target="#tanggal_selesai_picker" data-toggle="datetimepicker"><div class="input-group-text"><i class="far fa-calendar-check"></i></div></div>
                                </div>
                                <small class="text-danger" id="error_tanggal_selesai"></small>
                            </div>
                        </div>
                    </div>
                    <div id="duration_field" class="input-group mb-3 d-none">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Duration</span>
                        </div>
                        <input type="text" id="duration_info" class="form-control" disabled readonly aria-label="Durasi">
                        <div class="input-group-append">
                            <span class="input-group-text">Hari</span>
                        </div>
                    </div>
                    
                    <div class="row d-none" id="field_waktu">
                        <div class="col-md-6" id="field_jam_mulai">
                            <div class="form-group">
                                <label for="jam_mulai">Jam Mulai <span class="text-danger">*</span></label>
                                <div class="input-group date" id="jam_mulai_picker" data-target-input="nearest">
                                    <input type="text" name="jam_mulai" id="jam_mulai" class="form-control datetimepicker-input" value="<?= html_escape(set_value('jam_mulai')) ?>" data-target="#jam_mulai_picker" placeholder="HH:mm" autocomplete="off">
                                    <div class="input-group-append" data-target="#jam_mulai_picker" data-toggle="datetimepicker"><div class="input-group-text"><i class="far fa-clock"></i></div></div>
                                </div>
                                <small class="text-danger" id="error_jam_mulai"></small>
                            </div>
                        </div>
                        <div class="col-md-6" id="field_jam_selesai">
                            <div class="form-group">
                                <label for="jam_selesai">Jam Selesai <span class="text-danger">*</span></label>
                                <div class="input-group date" id="jam_selesai_picker" data-target-input="nearest">
                                    <input type="text" name="jam_selesai" id="jam_selesai" class="form-control datetimepicker-input" value="<?= html_escape(set_value('jam_selesai')) ?>" data-target="#jam_selesai_picker" placeholder="HH:mm" autocomplete="off">
                                    <div class="input-group-append" data-target="#jam_selesai_picker" data-toggle="datetimepicker"><div class="input-group-text"><i class="far fa-clock"></i></div></div>
                                </div>
                                <small class="text-danger" id="error_jam_selesai"></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 pl-lg-4 border-lg-left d-none">
                    <div class="form-group">
                        <label for="">Nama Karyawan</label>
                        <input type="text" class="form-control mb-3" value="<?= isset($employee_name) ? $employee_name : '' ?>" disabled readonly>
                    </div>
                    <div class="form-group">
                        <label for="alasan">Deskripsi / Alasan <span class="text-danger">*</span></label>
                        <textarea name="alasan" id="alasan" class="form-control" rows="6" placeholder="Tuliskan alasan pengajuan..."><?= set_value('alasan') ?></textarea>
                        <small class="text-danger" id="error_alasan"></small>
                    </div>
                    <div class="form-group">
                        <label for="attachment">Lampiran</label>
                        <div class="custom-file">
                            <input type="file" name="attachment" id="attachment" class="custom-file-input" accept=".jpg,.jpeg,.png,.pdf">
                            <label class="custom-file-label" for="attachment">Pilih file</label>
                        </div>
                        <small class="form-text text-muted">JPG, JPEG, PNG, atau PDF. Maksimal 2 MB.</small>
                        <small class="text-danger d-block" id="error_attachment"></small>
                    </div>
                    <!-- <div class="form-group mb-0">
                        <label>Status Pengajuan</label>
                        <div class="border rounded bg-light p-3 d-flex align-items-center">
                            <input type="hidden" name="status" value="<?= (int) $pending_status_id ?>">
                            <span class="badge badge-warning px-3 py-2"><i class="fas fa-clock mr-1"></i> Pending</span>
                            <small class="text-muted ml-3">Status awal otomatis Pending.</small>
                        </div>
                    </div> -->
                </div>
            </div>
            <hr class="my-4">
            <div class="d-flex justify-content-end">
                <a href="<?= site_url('perizinan') ?>" class="btn btn-secondary mr-2"><i class="fas fa-arrow-left mr-1"></i> Batal</a>
                <button type="submit" id="btn-simpan" class="btn btn-primary d-none"><i class="fas fa-save mr-1"></i> Simpan Pengajuan</button>
            </div>
        </form>
    </div>
</div>
