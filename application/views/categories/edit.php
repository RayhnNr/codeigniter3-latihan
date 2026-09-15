<form id="formCategory">
    <div class="modal-header">
        <h5 class="modal-title">Edit Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <input type="hidden" name="category_id" value="<?= $row->category_id ?>">

        <div class="form-group">
            <label>Nama Category</label>
            <input type="text" name="category_name" class="form-control" value="<?= $row->category_name ?>">
            <small class="text-danger" id="error_category_name"></small>
        </div>

        <div class="form-group">
            <label>Status</label><br>
            <input type="hidden" name="status" id="categories_status_value" value="<?= (int) $row->status ?>">
            <input type="checkbox" 
                id="categories_status" 
                data-active-value="<?= (int) $active_status_id ?>" 
                data-inactive-value="<?= (int) $inactive_status_id ?>" 
                data-toggle="toggle" 
                data-on="Aktif" 
                data-off="Nonaktif" 
                data-onstyle="success" 
                data-offstyle="secondary" 
                data-width="110" 
                <?= (int) $row->status === (int) $active_status_id ? 'checked' : '' ?>>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>