<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile text-center">
                <?php if (!empty($employee->photo) && is_file('./uploads/employees/photo/' . basename($employee->photo))): ?>
                    <a href="<?= base_url('uploads/employees/photo/' . rawurlencode(basename($employee->photo))) ?>" data-lightbox="profile-photo">
                        <img src="<?= base_url('uploads/employees/photo/' . rawurlencode(basename($employee->photo))) ?>" alt="Foto <?= htmlspecialchars($employee->employee_name ?? $user->username, ENT_QUOTES, 'UTF-8') ?>" class="profile-user-img img-fluid img-circle mb-3" style="width:100px;height:100px;object-fit:cover;cursor:pointer">
                    </a>
                <?php else: ?>
                    <img src="<?= base_url('assets/adminlte/dist/img/user2-160x160.jpg') ?>" alt="Foto profil" class="profile-user-img img-fluid img-circle mb-3" style="width:100px;height:100px;object-fit:cover">
                <?php endif; ?>
                <h4 class="profile-username"><?= htmlspecialchars($employee->employee_name ?? $user->username, ENT_QUOTES, 'UTF-8') ?></h4>
                <p class="text-muted mb-2">@<?= htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8') ?></p>
                <p class="text-muted">
                    <?php
                    $role_label = !empty($user->role_name) ? $user->role_name : (!empty($user->role) ? ucfirst($user->role) : 'User');
                    $is_admin   = (isset($user->role_slug) && $user->role_slug === 'admin') || (isset($user->role) && $user->role === 'admin') || (isset($user->role_id) && $user->role_id == 1);
                    ?>
                    <span class="badge badge-<?= $is_admin ? 'danger' : 'info' ?> px-3 py-1" style="font-size:.85rem;">
                        <?= htmlspecialchars($role_label) ?>
                    </span>
                </p>
                <ul class="list-group list-group-unbordered mt-3 text-left">
                    <li class="list-group-item">
                        <b><i class="fas fa-envelope mr-2 text-primary"></i> Email</b>
                        <span class="float-right"><?= htmlspecialchars($user->email ?? '-') ?></span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-phone mr-2 text-success"></i> Nomor HP</b>
                        <span class="float-right"><?= htmlspecialchars($user->nomor_hp ?? '-') ?></span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-circle mr-2 <?= $user->status == 1 ? 'text-success' : 'text-danger' ?>"></i> Status</b>
                        <span class="float-right">
                            <span class="badge badge-<?= $user->status == 1 ? 'success' : 'danger' ?>">
                                <?= $user->status == 1 ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-primary card-outline mb-2">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-edit mr-2"></i> Edit Profil</h5>
            </div>
            <div class="card-body">
                <?= form_open_multipart('profile/update', ['id' => 'form-edit-profile']) ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="profile-username">Username</label>
                            <input type="text" class="form-control" id="profile-username" name="username" maxlength="50" value="<?= htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8') ?>">
                            <small class="text-danger" id="error_username"></small>
                        </div>
                        <div class="form-group">
                            <label for="profile-email">Email</label>
                            <input type="email" class="form-control" id="profile-email" name="email" maxlength="255" value="<?= htmlspecialchars($user->email ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <small class="text-danger" id="error_email"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="profile-phone">Nomor HP</label>
                            <input type="text" class="form-control" id="profile-phone" name="nomor_hp" maxlength="20" value="<?= htmlspecialchars($user->nomor_hp ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <small class="text-danger" id="error_nomor_hp"></small>
                        </div>
                        <div class="form-group">
                            <label for="profile-name">Nama</label>
                            <input type="text" class="form-control" id="profile-name" name="employee_name" maxlength="100" value="<?= htmlspecialchars($employee->employee_name ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <small class="text-danger" id="error_employee_name"></small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="profile-photo">Foto</label>
                            <div class="row align-items-center">
                                <div class="col-sm-4 col-md-3 mb-2 mb-sm-0" id="profile-photo-preview">
                                    <?php if (!empty($employee->photo) && is_file('./uploads/employees/photo/' . basename($employee->photo))): ?>
                                        <a href="<?= base_url('uploads/employees/photo/' . rawurlencode(basename($employee->photo))) ?>" data-lightbox="profile-photo">
                                            <img src="<?= base_url('uploads/employees/photo/' . rawurlencode(basename($employee->photo))) ?>" alt="Foto profil" class="img-thumbnail" style="width:120px;height:120px;object-fit:cover;cursor:pointer">
                                        </a>
                                    <?php else: ?>
                                        <img src="<?= base_url('assets/adminlte/dist/img/user2-160x160.jpg') ?>" alt="Foto profil" class="img-thumbnail" style="width:120px;height:120px;object-fit:cover">
                                    <?php endif; ?>
                                </div>
                                <div class="col-sm-8 col-md-9">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="profile-photo" name="photo" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                                        <label class="custom-file-label file-label" for="profile-photo">Pilih foto...</label>
                                    </div>
                                    <small class="text-muted d-block mt-1">Format JPG, JPEG, atau PNG. Maksimal 2 MB.</small>
                                    <small class="text-danger d-block" id="error_photo"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <small class="text-danger d-block mb-2" id="error_profile"></small>
                <button type="submit" class="btn btn-primary" id="btn-save-profile"><i class="fas fa-save mr-1"></i> Simpan Profil</button>
                <?= form_close() ?>
            </div>
        </div>
    <!-- Kartu Ubah Password -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-lock mr-2"></i> Ubah Password</h5>
            </div>
            <div class="card-body">
                <?= form_open('profile/change_password', ['id' => 'form-change-pw']) ?>

                <div class="form-group">
                    <label>Password Saat Ini <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="current_password" id="inp-current-pw" class="form-control" placeholder="Masukkan password saat ini">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary btn-toggle-pw" data-target="inp-current-pw">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <small class="text-danger" id="error_current_password"></small>
                </div>

                <div class="form-group">
                    <label>Password Baru <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="new_password" id="inp-new-pw" class="form-control" placeholder="Minimal 6 karakter">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary btn-toggle-pw" data-target="inp-new-pw">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <small class="text-danger" id="error_new_password"></small>
                </div>

                <div class="form-group">
                    <label>Konfirmasi Password Baru <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="confirm_password" id="inp-confirm-pw" class="form-control" placeholder="Ulangi password baru">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary btn-toggle-pw" data-target="inp-confirm-pw">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <small class="text-danger" id="error_confirm_password"></small>
                </div>

                <!-- Strength indicator -->
                <div class="form-group">
                    <small class="text-muted">Kekuatan password:</small>
                    <div class="progress mt-1" style="height:6px;">
                        <div class="progress-bar" id="pw-strength-bar" role="progressbar" style="width:0%"></div>
                    </div>
                    <small id="pw-strength-label" class="text-muted"></small>
                </div>

                <hr>
                <button type="submit" class="btn btn-primary" id="btn-save-pw">
                    <i class="fas fa-key mr-1"></i> Ubah Password
                </button>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<script>
window.addEventListener('load', function(){

    $('#profile-photo').on('change', function(){
        const file = this.files[0];
        const $fileLabel = $(this).siblings('.file-label');
        $('#error_photo').text('');
        $fileLabel.text(file ? file.name : 'Pilih foto...');
        if (!file) return;
        if (!['image/jpeg', 'image/png'].includes(file.type)) {
            $('#error_photo').text('Format foto harus JPG, JPEG, atau PNG.');
            this.value = '';
            $fileLabel.text('Pilih foto...');
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            $('#error_photo').text('Ukuran foto maksimal 2 MB.');
            this.value = '';
            $fileLabel.text('Pilih foto...');
            return;
        }
        const previewUrl = URL.createObjectURL(file);
        $('#profile-photo-preview').html(`<a href="${previewUrl}" data-lightbox="profile-photo"><img src="${previewUrl}" alt="Pratinjau foto profil" class="img-thumbnail" style="width:120px;height:120px;object-fit:cover;cursor:pointer"></a>`);
    });

    $('#form-edit-profile').on('submit', function(e){
        e.preventDefault();
        $('#form-edit-profile .text-danger').text('');
        $.ajax({
            url: '<?= base_url('profile/update') ?>',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(){
                $('#btn-save-profile').prop('disabled', true);
                Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            },
            success: function(res){
                if (res.status) {
                    Swal.fire('Berhasil', 'Profil berhasil diperbarui.', 'success').then(() => window.location.reload());
                } else {
                    Swal.close();
                    $.each(res.errors || {}, (field, message) => $('#error_' + field).text(message));
                }
            },
            error: function(){
                Swal.fire('Gagal', 'Terjadi kesalahan pada server.', 'error');
            },
            complete: function(){
                $('#btn-save-profile').prop('disabled', false);
            }
        });
    });

    // Toggle show/hide password
    $(document).on('click', '.btn-toggle-pw', function(){
        const targetId = $(this).data('target');
        const inp = $('#' + targetId);
        const ico = $(this).find('i');
        if (inp.attr('type') === 'password'){
            inp.attr('type', 'text');
            ico.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            inp.attr('type', 'password');
            ico.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Password strength meter
    $('#inp-new-pw').on('input', function(){
        const pw = $(this).val();
        let score = 0;
        if (pw.length >= 6) score++;
        if (pw.length >= 10) score++;
        if (/[A-Z]/.test(pw)) score++;
        if (/[0-9]/.test(pw)) score++;
        if (/[^A-Za-z0-9]/.test(pw)) score++;

        const levels = [
            { pct: 0,   cls: '',        label: '' },
            { pct: 20,  cls: 'bg-danger',  label: 'Sangat lemah' },
            { pct: 40,  cls: 'bg-warning', label: 'Lemah' },
            { pct: 60,  cls: 'bg-info',    label: 'Cukup' },
            { pct: 80,  cls: 'bg-primary', label: 'Kuat' },
            { pct: 100, cls: 'bg-success', label: 'Sangat kuat' },
        ];
        const lv = levels[score] || levels[0];
        $('#pw-strength-bar').css('width', lv.pct + '%').attr('class', 'progress-bar ' + lv.cls);
        $('#pw-strength-label').text(lv.label);
    });

    // Submit ubah password
    $('#form-change-pw').on('submit', function(e){
        e.preventDefault();
        $('.text-danger').text('');

        $.ajax({
            url: '<?= base_url('profile/change_password') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function(){
                Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            },
            success: function(res){
                if (res.status){
                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                        $('#form-change-pw')[0].reset();
                        $('#pw-strength-bar').css('width','0%').attr('class','progress-bar');
                        $('#pw-strength-label').text('');
                    });
                } else if (res.errors){
                    Swal.close();
                    $.each(res.errors, (field, msg) => $('#error_' + field).text(msg));
                } else {
                    Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                }
            },
            error: function(){
                Swal.fire('Gagal', 'Terjadi kesalahan pada server', 'error');
            }
        });
    });
});
</script>
