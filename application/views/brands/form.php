<form id="formbrand">
    <div class="modal-header">
        <h5 class="modal-title">Tambah brand</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <input type="hidden" name="brand_id" value="">

        <div class="form-group">
            <label>Nama brand</label>
            <input type="text" name="brand_name" class="form-control" value="">
            <small class="text-danger" id="error_brand_name"></small>
        </div>

        <div class="form-group">
            <label>Status</label>
            <input type="hidden" name="status" id="brands_status_value" value="<?= (int) $active_status_id ?>">
            <input type="checkbox" 
                id="brands_status" 
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