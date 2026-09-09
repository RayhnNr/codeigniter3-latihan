<form action="<?= site_url('jenis_perizinan/save') ?>" method="post" id="form-jenis-perizinan" novalidate>
	<input type="hidden" name="jenis_perizinan_id" value="<?= $row ? (int) $row->jenis_perizinan_id : '' ?>">
	<?php $status = $row ? (int) $row->status : (int) $active_status_id; ?>
	<div class="form-group">
		<label for="jenis_perizinan_code">Kode Jenis Perizinan</label>
		<input type="text" id="jenis_perizinan_code" class="form-control" maxlength="20" value="<?= $row ? html_escape($row->jenis_perizinan_code) : '' ?>" readonly>
		<small class="form-text text-muted">Kode akan dibuat otomatis dari nama jenis perizinan.</small>
		<small class="text-danger d-none" id="jenis_perizinan_code_error">Kode ini sudah digunakan.</small>
	</div>
	<div class="form-group">
		<label for="jenis_perizinan_name">Jenis Perizinan</label>
		<input type="text" name="jenis_perizinan_name" id="jenis_perizinan_name" class="form-control" maxlength="50" value="<?= $row ? html_escape($row->jenis_perizinan_name) : '' ?>" autofocus>
		<small class="text-danger d-none" id="jenis_perizinan_name_error">Nama jenis perizinan wajib diisi.</small>
	</div>
	<div class="form-group">
		<label for="description">Deskripsi</label>
		<textarea name="description" id="description" class="form-control" maxlength="50" rows="3"><?= $row ? html_escape($row->description) : '' ?></textarea>
	</div>
	<div class="form-group">
		<label>Status</label>
		<input type="hidden" name="status" id="jenis_perizinan_status_value" value="<?= $status ?>">
		<input type="checkbox" id="jenis_perizinan_status" data-active-value="<?= (int) $active_status_id ?>" data-inactive-value="<?= (int) $inactive_status_id ?>" data-toggle="toggle" data-on="Aktif" data-off="Nonaktif" data-onstyle="success" data-offstyle="secondary" data-width="110" <?= $status === (int) $active_status_id ? 'checked' : '' ?>>
	</div>
	<div class="modal-footer px-0 pb-0">
		<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
		<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
	</div>
</form>
