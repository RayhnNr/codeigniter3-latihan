<script>

var table;

function initTablePerizinan() {
    table = $('#table-perizinan').DataTable({
        processing: true,
        serverSide: false,
        responsive: true,
        autoWidth: false,
        ajax: {
            url: '<?= base_url('perizinan/get_data') ?>',
            type: 'POST',
            dataSrc: 'data'
        },
        columns: [
            { data: 'no', orderable: false, searchable: false },
            { data: 'perizinan_no' },
            { data: 'jenis_perizinan_name' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        ${row.tanggal_mulai || '-'}
                        ${row.tanggal_selesai ? ' - ' + row.tanggal_selesai : ''}
                    `;
                }
            },
            { data: 'product_status_name' },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    var status = (row.product_status_name || '').toLowerCase().trim();

                    if (status === 'pending') {
                        return `
                            <a href="<?= base_url('perizinan/edit/') ?>${row.perizinan_id}"
                               class="btn btn-sm btn-warning"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <a href="<?= base_url('perizinan/delete/') ?>${row.perizinan_id}"
                               class="btn btn-sm btn-danger btn-delete"
                               title="Hapus">
                                <i class="fas fa-trash"></i>
                            </a>
                        `;
                    }

                    return '-';
                }
            }
        ]
    });
}

function editPerizinan(id) {
    window.location.href = '<?= base_url('perizinan/edit/') ?>' + id;
}

function hapusPerizinan(id) {
    Swal.fire({
        title: 'Hapus data ini?',
        text: 'Data yang dihapus tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33',
        reverseButtons: true
    }).then(function (result) {
        if (result.isConfirmed) {
            window.location.href = '<?= base_url('perizinan/delete/') ?>' + id;
        }
    });
}

function gantiFormPerizinan(jenisId) {
    $('#error_jenis_perizinan_id').text('');
    $('#jenis_perizinan_id').removeClass('is-invalid');

    $('#wrapperFormPerizinan').html('');

    if (!jenisId) {
        return;
    }

    $.ajax({
        url: '<?= base_url('perizinan/ajax_get_form') ?>',
        type: 'POST',
        data: {
            jenis_perizinan_id: jenisId,
            perizinan_id: $('#form-perizinan input[name="perizinan_id"]').val() || ''
        },

        beforeSend: function () {
            $('#wrapperFormPerizinan').html(`
                <div class="text-center py-3">
                    <i class="fas fa-spinner fa-spin mr-1"></i>
                    Memuat form...
                </div>
            `);
        },

        success: function (response) {
            $('#wrapperFormPerizinan').html(response);

            initFormPerizinan();
        },

        error: function () {
            $('#wrapperFormPerizinan').html(`
                <div class="alert alert-danger">
                    Gagal memuat form perizinan.
                </div>
            `);
        }
    });
}

function initFormPerizinan() {

    /*
     * Hanya initialize component yang ada
     * di dalam form hasil AJAX.
     */

    $('#tanggal_mulai_picker, #tanggal_selesai_picker').each(function () {

        var picker = $(this);

        if (picker.data('DateTimePicker')) {
            picker.datetimepicker('destroy');
        }

        picker.datetimepicker({
            format: 'YYYY-MM-DD',
            useCurrent: false,
            allowInputToggle: true,
            buttons: {
                showToday: true,
                showClear: true,
                showClose: true
            },
            icons: {
                time: 'far fa-clock',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'far fa-calendar-check',
                clear: 'far fa-trash-alt',
                close: 'fa fa-times'
            }
        });
    });

    $('#jam_mulai_picker, #jam_selesai_picker').each(function () {

        var picker = $(this);

        if (picker.data('DateTimePicker')) {
            picker.datetimepicker('destroy');
        }

        picker.datetimepicker({
            format: 'HH:mm',
            stepping: 5,
            useCurrent: false,
            allowInputToggle: true,
            buttons: {
                showToday: true,
                showClear: true,
                showClose: true
            },
            icons: {
                time: 'far fa-clock',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'far fa-calendar-check',
                clear: 'far fa-trash-alt',
                close: 'fa fa-times'
            }
        });
    });

    var today = new Date();

    today.setHours(0, 0, 0, 0);

    var todayString = formatDateToInput(today);

    $('#tanggal_mulai').attr('min', todayString);
    $('#tanggal_selesai').attr('min', todayString);

    updateDuration();
}

function parseDate(value) {
    if (!value) {
        return null;
    }

    var parts = value.split('-');

    if (parts.length !== 3) {
        return null;
    }

    var parsed = new Date(
        Number(parts[0]),
        Number(parts[1]) - 1,
        Number(parts[2])
    );

    return isNaN(parsed.getTime()) ? null : parsed;
}

function formatDateToInput(date) {
    var day = ('0' + date.getDate()).slice(-2);
    var month = ('0' + (date.getMonth() + 1)).slice(-2);
    var year = date.getFullYear();

    return year + '-' + month + '-' + day;
}

function updateDuration() {
    var startDate = parseDate($('#tanggal_mulai').val());
    var endDate = parseDate($('#tanggal_selesai').val());

    if (!$('#duration_info').length) {
        return;
    }

    if (!startDate || !endDate) {
        $('#duration_info').val('');
        return;
    }

    if (endDate < startDate) {
        $('#duration_info').val('');
        return;
    }

    var duration = 0;
    var currentDate = new Date(startDate);

    while (currentDate <= endDate) {

        if (currentDate.getDay() !== 0) {
            duration++;
        }

        currentDate.setDate(currentDate.getDate() + 1);
    }

    $('#duration_info').val(duration);
}

function validateTanggalMulai() {
    var input = $('#tanggal_mulai');

    if (!input.length) {
        return true;
    }

    var value = input.val();
    var startDate = parseDate(value);

    var today = new Date();
    today.setHours(0, 0, 0, 0);

    if (!value) {
        return false;
    }

    if (!startDate) {
        input.addClass('is-invalid');

        $('#error_tanggal_mulai').text(
            'Format tanggal tidak valid.'
        );

        return false;
    }

    /*
     * Tanggal mulai sebelum hari ini
     */
    if (startDate < today) {
        var todayString = formatDateToInput(today);

        input.val(todayString);

        setTimeout(function () {
            input.addClass('is-invalid');
            $('#error_tanggal_mulai').text(
                'Tanggal mulai tidak boleh sebelum hari ini.'
            );
        }, 0);

        /*
         * Update minimum tanggal selesai
         */
        if ($('#tanggal_selesai').length) {
            $('#tanggal_selesai').attr('min', todayString);
        }

        updateDuration();

        return false;
    }

    input.removeClass('is-invalid');
    $('#error_tanggal_mulai').text('');

    /*
     * Update minimum tanggal selesai
     */
    if ($('#tanggal_selesai').length) {

        $('#tanggal_selesai').attr('min', value);

        var endDate = parseDate(
            $('#tanggal_selesai').val()
        );

        if (endDate && endDate < startDate) {

            $('#tanggal_selesai').addClass('is-invalid');

            $('#error_tanggal_selesai').text(
                'Tanggal selesai tidak boleh sebelum tanggal mulai.'
            );
        }
    }

    updateDuration();

    return true;
}

function validateTanggalSelesai() {
    var input = $('#tanggal_selesai');

    if (!input.length) {
        return true;
    }

    var value = input.val();

    var endDate = parseDate(value);

    var startDate = parseDate(
        $('#tanggal_mulai').val()
    );

    var today = new Date();
    today.setHours(0, 0, 0, 0);

    if (!value) {
        return false;
    }

    if (!endDate) {

        input.addClass('is-invalid');

        $('#error_tanggal_selesai').text(
            'Format tanggal tidak valid.'
        );

        return false;
    }

    /*
     * Tanggal selesai sebelum hari ini
     */
    if (endDate < today) {
        var todayString = formatDateToInput(today);

        input.val(todayString);

        setTimeout(function () {
            input.addClass('is-invalid');
            $('#error_tanggal_selesai').text(
                'Tanggal selesai tidak boleh sebelum hari ini.'
            );
        }, 0);

        updateDuration();

        return false;
    }

    /*
     * Tanggal selesai sebelum tanggal mulai
     */
    if (startDate && endDate < startDate) {
        var startString = formatDateToInput(startDate);

        input.val(startString);

        setTimeout(function () {
            input.addClass('is-invalid');
            $('#error_tanggal_selesai').text(
                'Tanggal selesai tidak boleh sebelum tanggal mulai.'
            );
        }, 0);

        updateDuration();

        return false;
    }

    input.removeClass('is-invalid');

    $('#error_tanggal_selesai').text('');

    updateDuration();

    return true;
}

function simpanPerizinan() {

    var form = document.getElementById('form-perizinan');

    if (!form) {
        return;
    }

    /*
     * Bersihkan error lama.
     */

    $('#form-perizinan small.text-danger').text('');
    $('#form-perizinan .is-invalid').removeClass('is-invalid');

    var isValid = true;

    var jenisId = $('#jenis_perizinan_id').val();

    var tanggalMulai = $('#tanggal_mulai').val();

    var tanggalSelesai = $('#tanggal_selesai').val();

    var alasan = $('#alasan').val();

    /*
     * JENIS PERIZINAN
     */

    if (!jenisId) {

        $('#error_jenis_perizinan_id').text(
            'Jenis perizinan wajib dipilih.'
        );

        $('#jenis_perizinan_id').addClass('is-invalid');

        isValid = false;
    }

    /*
     * TANGGAL MULAI
     */

    if ($('#tanggal_mulai').length && !tanggalMulai) {

        $('#error_tanggal_mulai').text(
            'Tanggal mulai wajib diisi.'
        );

        $('#tanggal_mulai').addClass('is-invalid');

        isValid = false;
    }

    /*
     * TANGGAL SELESAI
     *
     * Hanya ada pada template form_tanggal.
     */

    if ($('#tanggal_selesai').length && !tanggalSelesai) {

        $('#error_tanggal_selesai').text(
            'Tanggal selesai wajib diisi.'
        );

        $('#tanggal_selesai').addClass('is-invalid');

        isValid = false;
    }

    /*
     * VALIDASI TANGGAL MULAI
     */

    if ($('#tanggal_mulai').length && tanggalMulai) {

        if (!validateTanggalMulai()) {
            isValid = false;
        }
    }

    /*
     * VALIDASI TANGGAL SELESAI
     */

    if ($('#tanggal_selesai').length && tanggalSelesai) {

        if (!validateTanggalSelesai()) {
            isValid = false;
        }
    }

    /*
     * ALASAN
     */

    if ($('#alasan').length && !alasan.trim()) {

        $('#error_alasan').text(
            'Deskripsi atau alasan wajib diisi.'
        );

        $('#alasan').addClass('is-invalid');

        isValid = false;
    }

    /*
     * JAM
     */

    var jamMulai = $('#jam_mulai').val();

    var jamSelesai = $('#jam_selesai').val();

    /*
     * JAM MULAI
     */

    if ($('#jam_mulai').length && !jamMulai) {

        $('#error_jam_mulai').text(
            'Jam mulai wajib diisi.'
        );

        $('#jam_mulai').addClass('is-invalid');

        isValid = false;
    }

    /*
     * JAM SELESAI
     */

    if ($('#jam_selesai').length && !jamSelesai) {

        $('#error_jam_selesai').text(
            'Jam selesai wajib diisi.'
        );

        $('#jam_selesai').addClass('is-invalid');

        isValid = false;
    }

    /*
     * BATAS JAM MULAI
     */

    if (
        $('#jam_mulai').length &&
        jamMulai &&
        (jamMulai < '08:00' || jamMulai > '16:00')
    ) {

        $('#error_jam_mulai').text(
            'Jam mulai harus berada dalam jam kantor (08:00 - 16:00).'
        );

        $('#jam_mulai').addClass('is-invalid');

        isValid = false;
    }

    /*
     * BATAS JAM SELESAI
     */

    if (
        $('#jam_selesai').length &&
        jamSelesai &&
        (jamSelesai < '08:00' || jamSelesai > '16:00')
    ) {

        $('#error_jam_selesai').text(
            'Jam selesai harus berada dalam jam kantor (08:00 - 16:00).'
        );

        $('#jam_selesai').addClass('is-invalid');

        isValid = false;
    }

    /*
     * JAM MULAI >= JAM SELESAI
     */

    if (
        $('#jam_mulai').length &&
        $('#jam_selesai').length &&
        jamMulai &&
        jamSelesai &&
        jamMulai >= jamSelesai
    ) {

        $('#error_jam_selesai').text(
            'Jam selesai harus lebih besar dari jam mulai.'
        );

        $('#jam_selesai').addClass('is-invalid');

        isValid = false;
    }

    /*
     * Kalau masih ada error,
     * jangan lanjut.
     */

    if (!isValid) {
        return;
    }

    /*
     * KONFIRMASI
     */

    Swal.fire({
        title: 'Simpan pengajuan?',
        text: 'Pastikan data perizinan sudah benar.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, simpan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then(function (result) {

        if (!result.isConfirmed) {
            return;
        }

        var formData = new FormData(form);

        $.ajax({
            url: '<?= base_url('perizinan/save') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',

            success: function (response) {

                if (response.status === 'success') {

                    window.location.href =
                        '<?= base_url('perizinan') ?>';

                    return;
                }

                if (response.errors) {

                    $.each(
                        response.errors,
                        function (field, message) {

                            $('#' + field).addClass('is-invalid');

                            $('#error_' + field).text(message);
                        }
                    );

                    return;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.message ||
                        'Gagal menyimpan data.'
                });
            },

            error: function () {

                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Terjadi kesalahan saat menyimpan data.'
                });
            }
        });
    });
}

/*
 * =========================
 * TANGGAL
 * =========================
 */

$(document).on(
    'input',
    '#tanggal_mulai',
    function () {
        validateTanggalMulai();
    }
);

$(document).on(
    'input',
    '#tanggal_selesai',
    function () {
        validateTanggalSelesai();
    }
);

$(document).on(
    'change.datetimepicker',
    '#tanggal_mulai_picker',
    function () {
        validateTanggalMulai();
    }
);

$(document).on(
    'change.datetimepicker',
    '#tanggal_selesai_picker',
    function () {
        validateTanggalSelesai();
    }
);

/*
 * =========================
 * BUKA DATE / TIME PICKER
 * =========================
 */

$(document).on(
    'click focus',
    '#tanggal_mulai, #tanggal_selesai, #jam_mulai, #jam_selesai',
    function () {

        var pickerId = $(this).data('target');

        if (pickerId) {
            $(pickerId).datetimepicker('show');
        }
    }
);

/*
 * =========================
 * CLEAR ERROR INPUT
 * =========================
 */

$(document).on(
    'input change',
    '#form-perizinan input, #form-perizinan textarea',
    function () {

        var id = $(this).attr('id');

        /*
         * Jangan clear error tanggal otomatis.
         * Karena validasi tanggal mempunyai aturan sendiri.
         */

        if (
            id === 'tanggal_mulai' ||
            id === 'tanggal_selesai'
        ) {
            return;
        }

        if ($(this).val()) {

            $('#error_' + id).text('');

            $(this).removeClass('is-invalid');
        }
    }
);

/*
 * =========================
 * ATTACHMENT
 * =========================
 */

$(document).on(
    'change',
    '#attachment',
    function () {

        var fileName = this.files.length
            ? this.files[0].name
            : 'Pilih file';

        $(this)
            .next('.custom-file-label')
            .text(fileName);

        if (this.files.length) {

            $('#current_attachment').text(
                'File baru dipilih: ' + fileName
            );
        } else {

            $('#current_attachment').text('');
        }
    }
);

/*
 * =========================
 * INPUT FOCUS
 * =========================
 */

$(document).on(
    'focus',
    '.form-control',
    function () {

        $(this)
            .closest('.input-group')
            .addClass('border-primary');
    }
);

$(document).on(
    'blur',
    '.form-control',
    function () {

        $(this)
            .closest('.input-group')
            .removeClass('border-primary');
    }
);

/*
 * =========================
 * DOCUMENT READY
 * =========================
 */

$(function () {

    /*
     * DataTable
     */

    if ($('#table-perizinan').length) {
        initTablePerizinan();
    }

    /*
     * Select2 JENIS PERIZINAN
     *
     * Hanya initialize sekali.
     */

    if ($('#jenis_perizinan_id').length) {

        $('#jenis_perizinan_id').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Pilih Jenis Perizinan',
            allowClear: true
        });

        var jenisId =
            $('#jenis_perizinan_id').val();

        if (jenisId) {
            gantiFormPerizinan(jenisId);
        }
    }

});

</script>