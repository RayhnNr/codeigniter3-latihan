<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Jenis Perizinan</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" id="btn-add"><i class="fas fa-plus"></i> Tambah</button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped" id="table-perizinan-tipe" style="width: 100%">
            <thead>
                <tr>
                    <th style="width: 50px">No</th>
                    <th>Nama Tipe</th>
                    <th style="width: 110px">Status</th>
                    <th style="width: 100px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= html_escape($row->nama_tipe) ?></td>
                        <td>
                            <?php if ((int) $row->status === 1): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning btn-edit" data-id="<?= (int) $row->id_perizinan_tipe ?>" title="Edit"><i class="fas fa-edit"></i></button>
                            <a href="<?= site_url('perizinan_tipe/delete/' . (int) $row->id_perizinan_tipe) ?>" class="btn btn-sm btn-danger btn-delete" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-perizinan-tipe" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-perizinan-tipe-title">Tambah Jenis Perizinan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-perizinan-tipe-body"></div>
        </div>
    </div>
</div>

<?php $this->load->view('perizinan_tipe/js'); ?>