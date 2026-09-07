<form action="<?= site_url('perizinan_tipe/save') ?>" method="post">
	<input type="hidden" name="id_perizinan_tipe" value="<?= $row ? (int) $row->id_perizinan_tipe : '' ?>">
	<?php $status = $row ? (int) $row->status : 2; ?>
	<div class="form-group">
		<label for="nama_tipe">Nama Tipe</label>
		<input type="text" name="nama_tipe" id="nama_tipe" class="form-control" maxlength="50" value="<?= $row ? html_escape($row->nama_tipe) : '' ?>" required autofocus>
	</div>
	<div class="form-group">
		<label>Status</label>
		<input type="hidden" name="status" id="perizinan_tipe_status_value" value="<?= $status === 1 ? 1 : 2 ?>">
		<input type="checkbox" id="perizinan_tipe_status" data-toggle="toggle" data-on="Aktif" data-off="Nonaktif" data-onstyle="success" data-offstyle="secondary" data-width="110" <?= $status === 1 ? 'checked' : '' ?>>
	</div>
	<div class="modal-footer px-0 pb-0">
		<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
		<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
	</div>
</form>
