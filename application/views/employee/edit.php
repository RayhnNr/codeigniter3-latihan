<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-user-edit mr-2"></i>Edit Employee</h5>
    </div>
    <div class="card-body">
        <?= form_open_multipart('employee/update/' . $employee->employee_id, ['id' => 'form-employee-edit']) ?>

        <!-- ===== DATA EMPLOYEE ===== -->
        <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-id-badge mr-1"></i> Data Employee</h6>
        <div class="form-group">
            <label>Employee Code</label>
            <input type="text" class="form-control" value="<?= $employee->employee_code ?>" disabled>
            <small class="text-muted">Employee Code otomatis</small>
        </div>

        <div class="row">
            <!-- Kolom Kiri: Input Data Employee -->
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nama Employee <span class="text-danger">*</span></label>
                    <input type="text" name="employee_name" id="employee_name"
                           class="form-control"
                           value="<?= set_value('employee_name', $employee->employee_name) ?>">
                    <small class="text-danger" id="error_employee_name"></small>
                </div>

                <div class="form-group">
                    <label>Salary <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                        <input type="text" id="salary" name="salary" class="form-control"
                               inputmode="numeric" autocomplete="off"
                               value="<?= isset($employee) ? number_format($employee->salary, 0, ',', '.') : '' ?>">
                    </div>
                    <small class="text-danger" id="error_salary"></small>
                </div>

                <div class="form-group">
                    <label>Departemen <span class="text-danger">*</span></label>
                    <select name="departemen_id" id="departemen_id" class="form-control form-select2">
                        <option value="">-- Pilih Departemen --</option>
                        <?php foreach ($departments as $d): ?>
                            <option value="<?= $d->department_id ?>"
                                <?= set_select('departemen_id', $d->department_id, ($employee->department_id == $d->department_id)) ?>>
                                <?= $d->department_name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-danger" id="error_departemen_id"></small>
                </div>

                <div class="form-group">
                    <label>Posisi <span class="text-danger">*</span></label>
                    <select name="position_id" id="position_id" class="form-control form-select2">
                        <option value="">-- Pilih Posisi --</option>
                        <?php foreach ($positions as $p): ?>
                            <option value="<?= $p->position_id ?>"
                                <?= set_select('position_id', $p->position_id, ($employee->position_id == $p->position_id)) ?>>
                                <?= $p->position_name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-danger" id="error_position_id"></small>
                </div>

                <div class="form-group">
                    <label>Sub Department</label>
                    <select name="sub_department_id" id="sub_department_id" class="form-control select2">
                        <option value="">-- Pilih Department dulu --</option>
                    </select>
                    <small class="text-danger" id="error_sub_department_id"></small>
                </div>

                <div class="form-group">
                    <label>Status Employee</label>
                    <div>
                        <input type="checkbox" id="employee_status" data-on="Aktif" data-off="Nonaktif"
                               data-onstyle="success" data-offstyle="secondary" data-width="120"
                               <?= ($employee->status == 1) ? 'checked' : '' ?>>
                        <input type="hidden" name="status" id="employee_status_hidden" value="<?= (int) $employee->status ?>">
                    </div>
                    <small class="text-danger" id="error_status"></small>
                </div>
            </div>

            <!-- Kolom Kanan: Foto Employee & BPJS -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="photo">Foto Employee</label>
                    <div class="custom-file">
                        <input type="file"
                            name="photo"
                            id="photo"
                            class="custom-file-input"
                            accept=".jpg,.jpeg,.png,.webp">
                        <label class="custom-file-label" for="photo">Pilih foto baru...</label>
                    </div>
                    <small class="form-text text-muted">
                        JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB. Kosongkan jika tidak diubah.
                    </small>
                    <small class="text-danger d-block" id="error_photo"></small>
                    <div id="photo_preview" class="mt-2">
                        <?php if (!empty($employee->photo) && file_exists('./uploads/employees/photo/' . $employee->photo)): ?>
                            <div id="old_photo_box">
                                <small class="text-muted d-block mb-1">Foto saat ini:</small>
                                <img src="<?= base_url('uploads/employees/photo/' . $employee->photo) ?>" alt="Foto Employee" class="img-thumbnail img-preview-clickable shadow-sm" style="width: 160px; height: 160px; object-fit: cover; cursor: pointer;" title="Klik untuk memperbesar">
                                <small class="text-muted d-block mt-1"><i class="fas fa-search-plus mr-1"></i>Klik foto untuk memperbesar</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <hr class="my-3">

                <div class="form-group">
                    <label>BPJS</label>
                    <div>
                        <input type="checkbox" id="has_bpjs_toggle" data-on="Ada" data-off="Tidak"
                               data-onstyle="success" data-offstyle="secondary" data-width="120"
                               <?= (!empty($employee->has_bpjs) && $employee->has_bpjs == 1) ? 'checked' : '' ?>>
                        <input type="hidden" name="has_bpjs" id="has_bpjs_hidden" value="<?= (!empty($employee->has_bpjs) && $employee->has_bpjs == 1) ? 1 : 0 ?>">
                    </div>
                    <small class="text-muted">
                        Aktifkan jika employee memiliki BPJS.
                    </small>
                </div>

                <div class="form-group <?= (!empty($employee->has_bpjs) && $employee->has_bpjs == 1) ? '' : 'd-none' ?>" id="bpjs_field">
                    <label for="bpjs_card">Kartu BPJS</label>
                    <div class="custom-file">
                        <input type="file"
                            name="bpjs_card"
                            id="bpjs_card"
                            class="custom-file-input"
                            accept=".jpg,.jpeg,.png,.webp,.pdf">
                        <label class="custom-file-label" for="bpjs_card">Pilih file kartu BPJS baru...</label>
                    </div>
                    <small class="form-text text-muted">
                        JPG, JPEG, PNG, WEBP, atau PDF. Maksimal 2 MB. Kosongkan jika tidak diubah.
                    </small>
                    <small class="text-danger d-block" id="error_bpjs_card"></small>
                    <div id="bpjs_preview" class="mt-2">
                        <?php if (!empty($employee->has_bpjs) && !empty($employee->bpjs_card) && file_exists('./uploads/employees/bpjs/' . $employee->bpjs_card)): ?>
                            <?php 
                                $ext = strtolower(pathinfo($employee->bpjs_card, PATHINFO_EXTENSION));
                                $file_url = base_url('uploads/employees/bpjs/' . $employee->bpjs_card);
                            ?>
                            <div id="old_bpjs_box">
                                <small class="text-muted d-block mb-1">Kartu BPJS saat ini:</small>
                                <?php if ($ext === 'pdf'): ?>
                                    <a href="<?= htmlspecialchars($file_url) ?>" target="_blank" rel="noopener noreferrer" class="p-2 border rounded bg-light d-block text-decoration-none text-dark">
                                        <i class="fas fa-file-pdf text-danger mr-2 fa-lg"></i>
                                        <strong><?= htmlspecialchars($employee->bpjs_card) ?></strong>
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-external-link-alt mr-1"></i>
                                            Klik untuk membuka file PDF
                                        </small>
                                    </a>
                                <?php else: ?>
                                    <img src="<?= $file_url ?>" alt="Kartu BPJS" class="img-thumbnail img-preview-clickable shadow-sm" style="max-height: 160px; cursor: pointer;" title="Klik untuk memperbesar">
                                    <small class="text-muted d-block mt-1"><i class="fas fa-search-plus mr-1"></i>Klik kartu untuk memperbesar</small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <!-- ===== AKUN USER ===== -->
        <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-user-lock mr-1"></i> Akun User & Hak Akses</h6>

        <?php if ($account): ?>
            <div class="alert alert-info py-2 mb-3">
                <i class="fas fa-info-circle mr-1"></i> Employee ini telah memiliki akun user. Anda dapat mengubah data akun, hak akses, atau mereset password di bawah.
            </div>
            <input type="hidden" name="user_id" value="<?= $account->user_id ?>">
        <?php else: ?>
            <div class="alert alert-warning py-2 mb-3">
                <i class="fas fa-exclamation-triangle mr-1"></i> Employee ini <strong>belum memiliki akun user</strong>. Lengkapi form akun di bawah untuk membuat akun baru.
            </div>
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Username <span class="text-danger">*</span></label>
                <input type="text" name="username" id="username" class="form-control"
                       placeholder="min. 4 karakter" autocomplete="off"
                       value="<?= isset($account) ? htmlspecialchars($account->username) : '' ?>">
                <small class="text-danger" id="error_username"></small>
            </div>
            <div class="form-group col-md-6">
                <label>Role / Hak Akses <span class="text-danger">*</span></label>
                <select name="role_id" id="role_id" class="form-control form-select2">
                    <option value="">-- Pilih Role --</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r->id ?>"
                            <?= (isset($account->role_id) && $account->role_id == $r->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-danger" id="error_role_id"></small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email" class="form-control"
                       placeholder="email@domain.com" autocomplete="off"
                       value="<?= isset($account) ? htmlspecialchars($account->email) : '' ?>">
                <small class="text-danger" id="error_email"></small>
            </div>
            <div class="form-group col-md-6">
                <label>Nomor HP</label>
                <input type="text" name="nomor_hp" id="nomor_hp" class="form-control"
                       placeholder="08xxxxxxxxxx"
                       value="<?= isset($account) ? htmlspecialchars($account->nomor_hp ?? '') : '' ?>">
                <small class="text-danger" id="error_nomor_hp"></small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label>
                    Password
                    <?php if ($account): ?>
                        <span class="text-muted small font-weight-normal">(Kosongkan jika tidak ingin mengubah password)</span>
                    <?php else: ?>
                        <span class="text-danger">*</span>
                    <?php endif; ?>
                </label>
                <div class="input-group">
                    <input type="password" name="password" id="inp-password" class="form-control"
                           placeholder="<?= $account ? 'Kosongkan jika tidak diubah' : 'min. 6 karakter' ?>"
                           autocomplete="new-password">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-secondary" id="btn-toggle-pw" tabindex="-1">
                            <i class="fas fa-eye" id="ico-pw"></i>
                        </button>
                    </div>
                </div>
                <small class="text-danger" id="error_password"></small>
            </div>
            <div class="form-group col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-secondary btn-block" id="btn-gen-pw">
                    <i class="fas fa-random"></i> Generate
                </button>
            </div>

            <div class="form-group col-md-3">
                <label>Status Akun User</label>
                <br>
                <input type="checkbox" id="user_status_toggle" data-on="Aktif" data-off="Nonaktif"
                        data-onstyle="success" data-offstyle="secondary" data-width="120"
                        <?= (!isset($account) || $account->status == 1) ? 'checked' : '' ?>>
                <input type="hidden" name="user_status" id="user_status_hidden" value="<?= (!isset($account) || (int) $account->status === 1) ? 1 : 0 ?>">
                <small class="text-danger" id="error_user_status"></small>
            </div>

        </div>

        <div class="mt-3">
            <a href="<?= base_url('employee') ?>" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary" id="btn-update">
                <span class="spinner-border spinner-border-sm d-none" id="btn-spinner" role="status"></span>
                Update
            </button>
        </div>

        <!-- Modal Popup Preview Gambar -->
        <div class="modal fade" id="modalImagePreview" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white py-2">
                        <h5 class="modal-title" id="modalImageTitle"><i class="fas fa-image mr-2"></i>Preview Gambar</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center p-3 bg-light">
                        <img src="" id="modalPreviewImg" class="img-fluid rounded shadow-sm" style="max-height: 75vh; object-fit: contain;">
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <?= form_close() ?>
    </div>
</div>

<script>
window.addEventListener('load', function () {
    $('.form-select2').select2({ theme: 'bootstrap4', width: '100%' });
    $('#sub_department_id').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: '-- Pilih Sub Department --',
        allowClear: true
    });

    $('#employee_status').bootstrapToggle();
    $('#employee_status').bootstrapToggle((<?= (int) $employee->status ?> === 1) ? 'on' : 'off');
    $('#employee_status').on('change', function () {
        $('#employee_status_hidden').val($(this).prop('checked') ? 1 : 0);
    });

    $('#user_status_toggle').bootstrapToggle();
    $('#user_status_toggle').bootstrapToggle((<?= (!isset($account) || (int) $account->status === 1) ? 1 : 0 ?>) === 1 ? 'on' : 'off');
    $('#user_status_toggle').on('change', function () {
        $('#user_status_hidden').val($(this).prop('checked') ? 1 : 0);
    });

    function loadSubDepartments(departmentId) {
        $('#sub_department_id').empty();

        if (!departmentId) {
            $('#sub_department_id').append('<option value="">-- Pilih Department dulu --</option>');
            $('#sub_department_id').prop('disabled', true);
            $('#sub_department_id').trigger('change');
            return;
        }

        $.ajax({
            url: '<?= base_url('sub_department/get_by_department/') ?>' + departmentId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#sub_department_id').empty();

                if (!response || response.length === 0) {
                    $('#sub_department_id').append('<option value="">Tidak ada sub department aktif</option>');
                    $('#sub_department_id').prop('disabled', true);
                    $('#sub_department_id').trigger('change');
                    return;
                }

                $('#sub_department_id').append('<option value="">-- Pilih Sub Department --</option>');
                $.each(response, function(i, item) {
                    $('#sub_department_id').append(
                        '<option value="' + item.sub_department_id + '">' + item.sub_department_name + '</option>'
                    );
                });

                var selectedSubDepartmentId = '<?= !empty($employee->sub_department_id) ? (int) $employee->sub_department_id : 0 ?>';
                if (selectedSubDepartmentId && $('#sub_department_id option[value="' + selectedSubDepartmentId + '"]').length) {
                    $('#sub_department_id').val(selectedSubDepartmentId);
                }

                $('#sub_department_id').prop('disabled', false);
                $('#sub_department_id').trigger('change');
            },
            error: function() {
                Swal.fire('Gagal', 'Gagal memuat sub department.', 'error');
            }
        });
    }

    var currentDepartmentId = $('#departemen_id').val();
    if (currentDepartmentId) {
        loadSubDepartments(currentDepartmentId);
    } else {
        $('#sub_department_id').append('<option value="">-- Pilih Department dulu --</option>');
        $('#sub_department_id').prop('disabled', true);
    }

    // ---- Format / Parse Money ----
    function formatMoney(v) {
        if (v === null || v === undefined || v === '') return '';
        return parseFloat(v).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }
    function parseMoney(v) {
        if (v === null || v === undefined || v === '') return 0;
        var c = v.toString().replace(/\D/g, '');
        return c === '' ? 0 : parseInt(c, 10);
    }

    $('#salary').on('input keyup', function () {
        var input = this;
        var origStart = input.selectionStart;
        var value = $(input).val();
        var digitsBeforeCursor = value.substring(0, origStart).replace(/\D/g, '').length;
        var formatted = formatMoney(parseMoney(value));
        $(input).val(formatted);
        var newPos = 0, digitCount = 0;
        for (var i = 0; i < formatted.length; i++) {
            if (/\d/.test(formatted[i])) digitCount++;
            if (digitCount === digitsBeforeCursor) { newPos = i + 1; break; }
        }
        if (digitsBeforeCursor === 0) newPos = 0;
        input.setSelectionRange(newPos, newPos);
    });

    $('#departemen_id').on('change', function() {
        loadSubDepartments($(this).val());
    });

    // ---- Toggle password visibility ----
    $('#btn-toggle-pw').on('click', function () {
        var inp = $('#inp-password'), ico = $('#ico-pw');
        if (inp.attr('type') === 'password') {
            inp.attr('type', 'text');
            ico.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            inp.attr('type', 'password');
            ico.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // ---- Generate random password ----
    $('#btn-gen-pw').on('click', function () {
        var chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789@#!';
        var pw = '';
        for (var i = 0; i < 10; i++) pw += chars.charAt(Math.floor(Math.random() * chars.length));
        $('#inp-password').val(pw).attr('type', 'text');
        $('#ico-pw').removeClass('fa-eye').addClass('fa-eye-slash');
        Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Password baru: ' + pw, showConfirmButton: false, timer: 4000 });
    });

    // ---- Preview Foto Employee (FileReader) + Custom File Label ----
    $('#photo').on('change', function () {
        var file = this.files[0];
        $('#error_photo').text('');

        var fileName = file ? file.name : 'Pilih foto baru...';
        $(this).next('.custom-file-label').text(fileName);

        if (!file) {
            $('#photo_preview .new-preview').remove();
            $('#old_photo_box').show();
            return;
        }

        var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            $('#error_photo').text('Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau WEBP.');
            $(this).val('');
            $(this).next('.custom-file-label').text('Pilih foto baru...');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            $('#error_photo').text('Ukuran file foto maksimal 2 MB.');
            $(this).val('');
            $(this).next('.custom-file-label').text('Pilih foto baru...');
            return;
        }

        $('#old_photo_box').hide();
        $('#photo_preview .new-preview').remove();

        var reader = new FileReader();
        reader.onload = function (e) {
            $('#photo_preview').append(
                '<div class="new-preview mt-2">' +
                    '<small class="text-success d-block mb-1 font-weight-bold"><i class="fas fa-check-circle mr-1"></i>Foto baru yang dipilih:</small>' +
                    '<img src="' + e.target.result + '" alt="Foto Employee Baru" class="img-thumbnail img-preview-clickable shadow-sm" style="width: 160px; height: 160px; object-fit: cover; cursor: pointer;" title="Klik untuk memperbesar">' +
                    '<small class="text-muted d-block mt-1"><i class="fas fa-search-plus mr-1"></i>Klik foto untuk memperbesar</small>' +
                '</div>'
            );
        };
        reader.readAsDataURL(file);
    });

    // ---- BPJS Toggle (bootstrapToggle) & Preview ----
    $('#has_bpjs_toggle').bootstrapToggle();
    $('#has_bpjs_toggle').on('change', function () {
        var isChecked = $(this).prop('checked');
        $('#has_bpjs_hidden').val(isChecked ? 1 : 0);
        if (isChecked) {
            $('#bpjs_field').removeClass('d-none');
        } else {
            $('#bpjs_field').addClass('d-none');
            $('#bpjs_card').val('');
            $('#bpjs_card').next('.custom-file-label').text('Pilih file kartu BPJS baru...');
            $('#bpjs_preview .new-preview').remove();
            $('#error_bpjs_card').text('');
        }
    });

    $('#bpjs_card').on('change', function () {
        var file = this.files[0];
        $('#error_bpjs_card').text('');

        var fileName = file ? file.name : 'Pilih file kartu BPJS baru...';
        $(this).next('.custom-file-label').text(fileName);

        if (!file) {
            $('#bpjs_preview .new-preview').remove();
            $('#old_bpjs_box').show();
            return;
        }

        var isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
        var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

        if (!allowedTypes.includes(file.type) && !isPdf) {
            $('#error_bpjs_card').text('Format file tidak didukung. Gunakan JPG, JPEG, PNG, WEBP, atau PDF.');
            $(this).val('');
            $(this).next('.custom-file-label').text('Pilih file kartu BPJS baru...');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            $('#error_bpjs_card').text('Ukuran file kartu BPJS maksimal 2 MB.');
            $(this).val('');
            $(this).next('.custom-file-label').text('Pilih file kartu BPJS baru...');
            return;
        }

        $('#old_bpjs_box').hide();
        $('#bpjs_preview .new-preview').remove();

        if (isPdf) {
            $('#bpjs_preview').append(
                '<div class="new-preview mt-2">' +
                    '<small class="text-success d-block mb-1 font-weight-bold"><i class="fas fa-check-circle mr-1"></i>Kartu BPJS baru (PDF):</small>' +
                    '<div class="p-2 border rounded bg-light">' +
                        '<i class="fas fa-file-pdf text-danger mr-2 fa-lg"></i>' +
                        '<strong>' + file.name + '</strong> <small class="text-muted">(' + (file.size / 1024).toFixed(1) + ' KB)</small>' +
                    '</div>' +
                '</div>'
            );
        } else {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#bpjs_preview').append(
                    '<div class="new-preview mt-2">' +
                        '<small class="text-success d-block mb-1 font-weight-bold"><i class="fas fa-check-circle mr-1"></i>Kartu BPJS baru yang dipilih:</small>' +
                        '<img src="' + e.target.result + '" alt="Kartu BPJS Baru" class="img-thumbnail img-preview-clickable shadow-sm" style="max-height: 160px; cursor: pointer;" title="Klik untuk memperbesar">' +
                        '<small class="text-muted d-block mt-1"><i class="fas fa-search-plus mr-1"></i>Klik kartu untuk memperbesar</small>' +
                    '</div>'
                );
            };
            reader.readAsDataURL(file);
        }
    });

    // ---- Popup Modal Preview Gambar Saat Diklik ----
    $(document).on('click', '.img-preview-clickable', function () {
        var src = $(this).attr('src');
        var title = $(this).attr('alt') || 'Preview Gambar';
        $('#modalPreviewImg').attr('src', src);
        $('#modalImageTitle').html('<i class="fas fa-image mr-2"></i>' + title);
        $('#modalImagePreview').modal('show');
    });

    // ---- Submit ----
    $('#form-employee-edit').on('submit', function (e) {
        e.preventDefault();
        $('.text-danger').text('');

        Swal.fire({
            title: 'Update Employee?',
            text: 'Pastikan data employee dan akun user sudah benar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(function (result) {
            if (result.isConfirmed) {
                updateData();
            }
        });
    });

    function updateData() {
        var $btn = $('#btn-update');
        $btn.prop('disabled', true);
        $('#btn-spinner').removeClass('d-none');

        var form = document.getElementById('form-employee-edit');
        var formData = new FormData(form);

        var rawSalary = parseMoney($('#salary').val());
        formData.set('salary', rawSalary);

        $.ajax({
            url: '<?= base_url('employee/update/' . $employee->employee_id) ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    window.location.href = '<?= base_url('employee') ?>';
                } else if (response.status === 'failed' && response.errors) {
                    $.each(response.errors, function (field, message) {
                        $('#error_' + field).text(message);
                    });
                    Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: 'Periksa kembali form!', showConfirmButton: false, timer: 2500 });
                } else {
                    Swal.fire('Gagal', response.message || 'Terjadi kesalahan', 'error');
                }
            },
            error: function () {
                Swal.fire('Gagal', 'Terjadi kesalahan pada server', 'error');
            },
            complete: function () {
                $btn.prop('disabled', false);
                $('#btn-spinner').addClass('d-none');
            }
        });
    }
});
</script>