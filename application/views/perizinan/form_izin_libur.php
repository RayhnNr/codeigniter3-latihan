<?php $is_edit = !empty($perizinan); ?>

<div class="row">
    <div class="col-lg-6 pr-lg-4">

        <div class="form-group">
            <label for="tanggal_mulai">
                Tanggal Mulai <span class="text-danger">*</span>
            </label>

            <div class="input-group date" id="tanggal_mulai_picker" data-target-input="nearest">
                <input type="text"
                       class="form-control datetimepicker-input"
                       id="tanggal_mulai"
                       name="tanggal_mulai"
                       data-target="#tanggal_mulai_picker"
                       value="<?= $is_edit ? html_escape($perizinan->tanggal_mulai) : '' ?>"
                       autocomplete="off">

                <div class="input-group-append" data-target="#tanggal_mulai_picker" data-toggle="datetimepicker">
                    <div class="input-group-text">
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>
            </div>

            <small class="text-danger" id="error_tanggal_mulai"></small>
        </div>

        <div class="form-group">
            <label for="tanggal_selesai">
                Tanggal Selesai <span class="text-danger">*</span>
            </label>

            <div class="input-group date" id="tanggal_selesai_picker" data-target-input="nearest">
                <input type="text"
                       class="form-control datetimepicker-input"
                       id="tanggal_selesai"
                       name="tanggal_selesai"
                       data-target="#tanggal_selesai_picker"
                       value="<?= $is_edit ? html_escape($perizinan->tanggal_selesai) : '' ?>"
                       autocomplete="off">

                <div class="input-group-append" data-target="#tanggal_selesai_picker" data-toggle="datetimepicker">
                    <div class="input-group-text">
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>
            </div>

            <small class="text-danger" id="error_tanggal_selesai"></small>
        </div>

        <div class="form-group">
            <label for="duration_info">Durasi</label>
            <div class="input-group">
                <input type="text"
                       class="form-control"
                       id="duration_info"
                       name="duration"
                       readonly>
                <div class="input-group-append">
                    <span class="input-group-text">Hari</span>
                </div>
            </div>

            <small class="text-danger" id="error_duration"></small>
        </div>

    </div>

    <div class="col-lg-6 pl-lg-4 border-lg-left">

        <div class="form-group">
            <label for="alasan">
                Alasan <span class="text-danger">*</span>
            </label>

            <textarea class="form-control"
                      id="alasan"
                      name="alasan"
                      rows="5"
                      placeholder="Masukkan alasan perizinan"><?= $is_edit ? html_escape($perizinan->alasan) : '' ?></textarea>

            <small class="text-danger" id="error_alasan"></small>
        </div>

        <div class="form-group">
            <label for="attachment">Lampiran</label>

            <div class="custom-file">
                <input type="file"
                       class="custom-file-input"
                       id="attachment"
                       name="attachment"
                       accept=".jpg,.jpeg,.png,.pdf">
                <label class="custom-file-label" for="attachment">
                    Pilih file
                </label>
            </div>

            <small class="form-text text-muted">
                Format: JPG, JPEG, PNG, PDF
            </small>

            <small class="text-danger d-block" id="error_attachment"></small>

            <div id="current_attachment" class="mt-1"></div>
            <?php if ($is_edit && $perizinan->attachment): ?>
                <a href="<?= base_url('uploads/perizinan/' . rawurlencode($perizinan->attachment)) ?>" target="_blank">Lihat lampiran saat ini</a>
            <?php endif; ?>
        </div>

    </div>
</div>