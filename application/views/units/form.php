<form id="formunit">
    <div class="modal-header">
        <h5 class="modal-title">Tambah unit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <input type="hidden" name="unit_id" value="">

        <div class="form-group">
            <label>Nama unit</label>
            <input type="text" name="unit_name" class="form-control" value="">
            <small class="text-danger" id="error_unit_name"></small>
        </div>

        <div class="form-group">
            <label>Status</label><br>
            <input type="hidden" name="status" id="units_status_value" value="<?= (int) $active_status_id ?>">
            <input type="checkbox" 
                id="units_status" 
                data-active-value="<?= (int) $active_status_id ?>" 
                data-inactive-value="<?= (int) $inactive_status_id ?>" 
                data-toggle="toggle" 
                data-on="Aktif" 
                data-off="Nonaktif" 
                data-onstyle="success" 
                data-offstyle="secondary" 
                data-width="110" 
                checked>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>