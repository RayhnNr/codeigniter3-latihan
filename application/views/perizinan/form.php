<?php $is_edit = !empty($perizinan); ?>

<div class="card">
    <div class="card-header">
        <h5><?= $is_edit ? 'Edit Pengajuan Izin' : 'Tambah Pengajuan Izin' ?></h5>
    </div>

    <div class="card-body">
        <form action="<?= site_url('perizinan/save') ?>"
              method="post"
              enctype="multipart/form-data"
              id="form-perizinan">

                        <?php if ($is_edit): ?>
                                <input type="hidden" name="perizinan_id" value="<?= (int) $perizinan->perizinan_id ?>">
                        <?php endif; ?>

            <div class="form-group">
                <label>Jenis Perizinan</label>

                <select id="jenis_perizinan_id"
                        name="jenis_perizinan_id"
                        class="form-control"
                        onchange="gantiFormPerizinan(this.value)">
                    <option value="">-- Pilih Jenis --</option>

                    <?php foreach ($jenis_perizinan as $j): ?>
                        <option value="<?= $j->jenis_perizinan_id ?>"
                            <?= $is_edit && $j->jenis_perizinan_id == $perizinan->jenis_perizinan_id ? 'selected' : '' ?>>
                            <?= html_escape($j->jenis_perizinan_name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <small class="text-danger" id="error_jenis_perizinan_id"></small>
            </div>

            <div id="wrapperFormPerizinan"></div>

            <div class="mt-3">
                <button type="button"
                        class="btn btn-primary"
                        onclick="simpanPerizinan()">
                        <i class="fas fa-save mr-1"></i>
                        <?= $is_edit ? 'Simpan Perubahan' : 'Simpan' ?>
                </button>

                <a href="<?= site_url('perizinan') ?>"
                   class="btn btn-secondary">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>

<?php if ($is_edit): ?>
<script>
window.perizinanEditJenisId = <?= (int) $perizinan->jenis_perizinan_id ?>;

$(function () {
    gantiFormPerizinan(window.perizinanEditJenisId);
});
</script>
<?php endif; ?>