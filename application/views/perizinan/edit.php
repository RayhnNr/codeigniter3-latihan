<div class="card">
    <style>
        #perizinan-fields.perizinan-fields-pending > .col-lg-6:first-child > :not(.form-group),
        #perizinan-fields.perizinan-fields-pending > .col-lg-6:last-child {
            display: none;
        }

        #perizinan-fields.perizinan-single-day #field_tanggal_selesai,
        #perizinan-fields.perizinan-no-time #field_waktu {
            display: none;
        }
    </style>
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-signature mr-2"></i>Edit Perizinan</h3>
    </div>
    <div class="card-body">
        <form action="<?= site_url('perizinan/save/' . $perizinan->perizinan_id) ?>" method="post" enctype="multipart/form-data" id="form-perizinan" novalidate>
            <input type="hidden" name="perizinan_id" value="<?= (int) $perizinan->perizinan_id ?>">
            <div id="perizinan-fields" class="row<?php
                $selected_jenis_id = (int) set_value('jenis_perizinan_id', $perizinan->jenis_perizinan_id);
                echo empty($selected_jenis_id) ? ' perizinan-fields-pending' : '';
                echo in_array($selected_jenis_id, [6, 7, 8], true) ? ' perizinan-single-day' : '';
                echo !in_array($selected_jenis_id, [6, 7, 8], true) ? ' perizinan-no-time' : '';
            ?>">
                <div class="col-lg-6 pr-lg-4">
                    <div class="form-group">
                        <label for="jenis_perizinan_id">Jenis Perizinan <span class="text-danger">*</span></label>
                        <select name="jenis_perizinan_id" id="jenis_perizinan_id" class="form-control form-select2" data-placeholder="Pilih Jenis Perizinan">
                            <option value=""></option>
                            <?php foreach ($jenis_perizinan as $item): ?>
                                <option value="<?= (int) $item->jenis_perizinan_id ?>" <?= set_select('jenis_perizinan_id', $item->jenis_perizinan_id, (int) $item->jenis_perizinan_id === (int) $perizinan->jenis_perizinan_id) ?>>
                                    <?= html_escape($item->jenis_perizinan_name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-danger" id="error_jenis_perizinan_id"></small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_mulai">Tanggal Mulai <span class="text-danger">*</span></label>
                                <div class="input-group date" id="tanggal_mulai_picker" data-target-input="nearest">
                                    <input type="text" name="tanggal_mulai" id="tanggal_mulai" class="form-control datetimepicker-input" value="<?= html_escape(set_value('tanggal_mulai', $perizinan->tanggal_mulai)) ?>" data-target="#tanggal_mulai_picker" autocomplete="off" placeholder="YYYY-MM-DD">
                                    <div class="input-group-append" data-target="#tanggal_mulai_picker" data-toggle="datetimepicker"><div class="input-group-text"><i class="far fa-calendar-alt"></i></div></div>
                                </div>
                                <small class="text-danger" id="error_tanggal_mulai"></small>
                            </div>
                        </div>
                        <div class="col-md-6" id="field_tanggal_selesai">
                            <div class="form-group">
                                <label for="tanggal_selesai">Tanggal Selesai <span class="text-danger">*</span></label>
                                <div class="input-group date" id="tanggal_selesai_picker" data-target-input="nearest">
                                    <input type="text" name="tanggal_selesai" id="tanggal_selesai" class="form-control datetimepicker-input" value="<?= html_escape(set_value('tanggal_selesai', $perizinan->tanggal_selesai)) ?>" data-target="#tanggal_selesai_picker" autocomplete="off" placeholder="YYYY-MM-DD">
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
                    <!-- <input type="text" id="duration_info" class="form-control d-none mb-3" disabled readonly aria-label="Durasi"> -->
                    <div class="row" id="field_waktu">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jam_mulai">Jam Mulai</label>
                                <div class="input-group date" id="jam_mulai_picker" data-target-input="nearest">
                                    <input type="text" name="jam_mulai" id="jam_mulai" class="form-control datetimepicker-input" value="<?= html_escape(set_value('jam_mulai', $perizinan->jam_mulai)) ?>" data-target="#jam_mulai_picker" placeholder="HH:mm" autocomplete="off">
                                    <div class="input-group-append" data-target="#jam_mulai_picker" data-toggle="datetimepicker"><div class="input-group-text"><i class="far fa-clock"></i></div></div>
                                </div>
                                <small class="text-danger" id="error_jam_mulai"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jam_selesai">Jam Selesai</label>
                                <div class="input-group date" id="jam_selesai_picker" data-target-input="nearest">
                                    <input type="text" name="jam_selesai" id="jam_selesai" class="form-control datetimepicker-input" value="<?= html_escape(set_value('jam_selesai', $perizinan->jam_selesai)) ?>" data-target="#jam_selesai_picker" placeholder="HH:mm" autocomplete="off">
                                    <div class="input-group-append" data-target="#jam_selesai_picker" data-toggle="datetimepicker"><div class="input-group-text"><i class="far fa-clock"></i></div></div>
                                </div>
                                <small class="text-danger" id="error_jam_selesai"></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 pl-lg-4 border-lg-left">
                    <div class="form-group">
                        <label for="alasan">Deskripsi / Alasan <span class="text-danger">*</span></label>
                        <textarea name="alasan" id="alasan" class="form-control" rows="6" placeholder="Tuliskan alasan pengajuan..."><?= html_escape(set_value('alasan', $perizinan->alasan)) ?></textarea>
                        <small class="text-danger" id="error_alasan"></small>
                    </div>
                    <div class="form-group">
                        <label for="attachment">Lampiran</label>
                        <div class="custom-file">
                            <input type="file" name="attachment" id="attachment" class="custom-file-input" accept=".jpg,.jpeg,.png,.pdf">
                            <label class="custom-file-label" for="attachment">Pilih file</label>
                        </div>
                        <?php if (!empty($perizinan->attachment)): ?>
                            <small class="form-text text-muted" id="current_attachment">
                                File saat ini:
                                <a href="<?= base_url('uploads/perizinan/' . rawurlencode($perizinan->attachment)) ?>" target="_blank" rel="noopener">
                                    <?= html_escape($perizinan->attachment) ?>
                                </a>
                            </small>
                        <?php endif; ?>
                        <small class="form-text text-muted">JPG, JPEG, PNG, atau PDF. Maksimal 2 MB.</small>
                        <small class="text-danger d-block" id="error_attachment"></small>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="d-flex justify-content-end">
                <a href="<?= site_url('perizinan') ?>" class="btn btn-secondary mr-2"><i class="fas fa-arrow-left mr-1"></i> Batal</a>
                <button type="submit" id="btn-simpan" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Pengajuan</button>
            </div>
        </form>
    </div>
</div>
