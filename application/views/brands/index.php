<div class="card">
    <div class="card-header">
        <button class="btn btn-primary btn-sm" id="btn_add">
            <i class="fas fa-plus"></i> Tambah Brand
        </button>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped" id="table-brand" style="width:100%">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama brand</th>
                    <th>Status</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal, isinya kosong, nanti ditempel dari form.php / edit.php -->
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="modalFormContent">
            <!-- konten form ditempel di sini via AJAX -->
        </div>
    </div>
</div>