<script>
window.addEventListener('load', function () {

	if ($('#table-perizinan').length) {
	$('#table-perizinan').DataTable({
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
			{ data: 'jenis_perizinan_name', defaultContent: '-' },
			{ data: 'tanggal_pengajuan', defaultContent: '-' },
			{ data: 'tanggal_mulai', defaultContent: '-' },
			{ data: 'tanggal_selesai', defaultContent: '-' },
			{
				data: 'product_status_name',
				defaultContent: '-',
				render: function (data) {
					var status = (data || '').toLowerCase().trim();
					if (status === 'approved') {
						return '<span class="badge badge-success">Approved</span>';
					} else if (status === 'rejected') {
						return '<span class="badge badge-danger">Rejected</span>';
					} else {
						return '<span class="badge badge-secondary">' + data + '</span>';
					}
				}
			},
			{
				data: null,
				orderable: false,
				searchable: false,
				className: 'text-center',
				render: function (data, type, row) {
					var status = (row.product_status_name || '').toLowerCase().trim();

					if (status === 'pending') {
						return '<a href="<?= base_url('perizinan/edit/') ?>' + row.perizinan_id + '" class="btn btn-sm btn-warning" title="Edit">' +
							'<i class="fas fa-edit"></i></a> ' +
							'<a href="<?= base_url('perizinan/delete/') ?>' + row.perizinan_id + '" class="btn btn-sm btn-danger btn-delete" title="Hapus">' +
							'<i class="fas fa-trash"></i></a>';
					}

					return '-';
				}
			}
		]
	});
	}

	$(document).on('click', '.btn-delete', function (e) {
		e.preventDefault();
		var url = $(this).attr('href');
		Swal.fire({
			title: 'Hapus data perizinan?',
			text: 'Data yang dihapus tidak dapat dikembalikan.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Ya, hapus',
			cancelButtonText: 'Batal',
			confirmButtonColor: '#d33',
			reverseButtons: true
		}).then(function (result) {
			if (result.isConfirmed) {
				window.location.href = url;
			}
		});
	});


	if ($('#form-perizinan').length) {


		function updatePerizinanFields() {
			var jenisId = $('#jenis_perizinan_id').val();
			var isSingleDay = ['6', '7', '8'].indexOf(jenisId) !== -1;
			var isTimeBased = ['6', '7', '8'].indexOf(jenisId) !== -1;

			$('#perizinan-fields > .col-lg-6').first().children().slice(1).toggleClass('d-none', !jenisId);
			$('#perizinan-fields > .col-lg-6').last().toggleClass('d-none', !jenisId);
			$('#perizinan-fields')
				.toggleClass('perizinan-fields-pending', !jenisId)
				.toggleClass('perizinan-single-day', isSingleDay)
				.toggleClass('perizinan-no-time', !isTimeBased);
			$('#btn-simpan').toggleClass('d-none', !jenisId);
			$('#field_tanggal_selesai').toggleClass('d-none', isSingleDay);
			$('#field_waktu').toggleClass('d-none', !isTimeBased);
			$('#duration_field').toggleClass('d-none', isSingleDay);

			if (isSingleDay) {
				$('#tanggal_selesai').val('');
			}

			if (!isTimeBased) {
				$('#jam_mulai, #jam_selesai').val('');
			}
		}

		function parseDate(value) {
			if (!value) {
				return null;
			}

			var parts = value.split('-');
			if (parts.length !== 3) {
				return null;
			}

			var parsed = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
			return isNaN(parsed.getTime()) ? null : parsed;
		}

		function formatDateToInput(date) {
            var day = ("0" + date.getDate()).slice(-2);
            var month = ("0" + (date.getMonth() + 1)).slice(-2);
            var year = date.getFullYear();
            return year + "-" + month + "-" + day;
        }

		function updateDuration() {
			var startDate = parseDate($('#tanggal_mulai').val());
			var endDate = parseDate($('#tanggal_selesai').val());
			var durationInfo = $('#duration_info');
			var durationField = $('#duration_field');
			var isSingleDay = ['6', '7', '8'].indexOf($('#jenis_perizinan_id').val()) !== -1;

			if (!$('#jenis_perizinan_id').val() || isSingleDay) {
				durationField.addClass('d-none');
				durationInfo.val('');
				return;
			}

			if (!startDate || !endDate) {
				durationField.removeClass('d-none');
				durationInfo.val('');
				return;
			}

			var duration = 0;
			var currentDate = new Date(startDate);

			while (currentDate <= endDate) {
				var day = currentDate.getDay();

				if (day !== 0) {
					duration++;
				}

				currentDate.setDate(currentDate.getDate() + 1);
			}

			durationField.removeClass('d-none');
			durationInfo.val(duration);
		}

		var today = new Date();
		today.setHours(0, 0, 0, 0);
		var stringHariIni = formatDateToInput(today);
		$('#tanggal_mulai').attr('min', stringHariIni);
		$('#tanggal_selesai').attr('min', stringHariIni);

		var isAutoCorrectingDates = false;

		function validateTanggalMulai() {

			if (isAutoCorrectingDates) {
				return;
			}

			var input = $('#tanggal_mulai');
			var startDate = parseDate(input.val());

			var todayCheck = new Date();
			todayCheck.setHours(0, 0, 0, 0);

			// Jika tanggal mulai sebelum hari ini
			if (startDate && startDate < todayCheck) {
				isAutoCorrectingDates = true;
				input.val(formatDateToInput(todayCheck));
				isAutoCorrectingDates = false;

				// Tampilkan error setelah event datepicker selesai diproses.
				setTimeout(function () {
					input.addClass('is-invalid');
					$('#error_tanggal_mulai').text(
						'Tanggal mulai tidak boleh sebelum hari ini.'
					);
				}, 0);
			} else if (startDate) {

				// Kalau sudah valid, hapus error
				input.removeClass('is-invalid');
				$('#error_tanggal_mulai').text('');
			}

			// Atur tanggal minimal tanggal selesai
			if (input.val()) {

				$('#tanggal_selesai').attr('min', input.val());

				var endDate = parseDate($('#tanggal_selesai').val());
				var currentStartDate = parseDate(input.val());

				if (endDate && currentStartDate && endDate < currentStartDate) {
					isAutoCorrectingDates = true;
					$('#tanggal_selesai').val(input.val());
					isAutoCorrectingDates = false;

					setTimeout(function () {
						$('#tanggal_selesai').addClass('is-invalid');
						$('#error_tanggal_selesai').text(
							'Tanggal selesai tidak boleh sebelum tanggal mulai.'
						);
					}, 0);
				}
			}

			updateDuration();
		}

		function validateTanggalSelesai() {

			if (isAutoCorrectingDates) {
				return;
			}

			var input = $('#tanggal_selesai');
			var startDate = parseDate($('#tanggal_mulai').val());
			var endDate = parseDate(input.val());

			var todayCheck = new Date();
			todayCheck.setHours(0, 0, 0, 0);

			var tanggalSelesaiSebelumHariIni = endDate && endDate < todayCheck;

			// Jika tanggal selesai sebelum hari ini
			if (endDate && endDate < todayCheck) {

				// Mental ke hari ini
				isAutoCorrectingDates = true;
				input.val(formatDateToInput(todayCheck));
				isAutoCorrectingDates = false;

				endDate = parseDate(input.val());

			} else if (endDate) {

				// Kalau sudah valid, hapus error
				input.removeClass('is-invalid');
				$('#error_tanggal_selesai').text('');
			}

			// Tanggal selesai tidak boleh sebelum tanggal mulai
			if (startDate && endDate && endDate < startDate) {

				isAutoCorrectingDates = true;
				input.val($('#tanggal_mulai').val());
				isAutoCorrectingDates = false;

				setTimeout(function () {
					input.addClass('is-invalid');
					$('#error_tanggal_selesai').text(
						'Tanggal selesai tidak boleh sebelum tanggal mulai.'
					);
				}, 0);
			} else if (tanggalSelesaiSebelumHariIni) {
				setTimeout(function () {
					input.addClass('is-invalid');
					$('#error_tanggal_selesai').text(
						'Tanggal selesai tidak boleh sebelum hari ini.'
					);
				}, 0);
			}

			updateDuration();
		}

		$('#tanggal_mulai').on('input', validateTanggalMulai);
		$('#tanggal_selesai').on('input', validateTanggalSelesai);

		$('#form-perizinan').on('submit', function (event) {
			event.preventDefault();
			$('#form-perizinan small.text-danger').text('');
			$('.is-invalid').removeClass('is-invalid');

			var form = this;
			var isValid = true;
			var jenisId = $('#jenis_perizinan_id').val();
			var isSingleDay = ['6', '7', '8'].indexOf(jenisId) !== -1;
			var isTimeBased = ['6', '7', '8'].indexOf(jenisId) !== -1;
			var fields = [
				{ selector: '#jenis_perizinan_id', message: 'Jenis perizinan wajib dipilih.' },
				{ selector: '#tanggal_mulai', message: 'Tanggal mulai wajib diisi.' },
				{ selector: '#alasan', message: 'Deskripsi atau alasan wajib diisi.' }
			];

			if (!isSingleDay) {
				fields.splice(2, 0, { selector: '#tanggal_selesai', message: 'Tanggal selesai wajib diisi.' });
			}

			$.each(fields, function (_, field) {
				var input = $(field.selector);
				if (!input.val() || !input.val().trim()) {
					$('#error_' + input.attr('id')).text(field.message);
					input.addClass('is-invalid');
					isValid = false;
				}
			});

			if (isTimeBased) {

				var jamMulai = $('#jam_mulai');
				var jamSelesai = $('#jam_selesai');

				var nilaiJamMulai = jamMulai.val();
				var nilaiJamSelesai = jamSelesai.val();

				$('#error_jam_mulai').text('');
				$('#error_jam_selesai').text('');

				jamMulai.removeClass('is-invalid');
				jamSelesai.removeClass('is-invalid');

				if (!nilaiJamMulai) {
					$('#error_jam_mulai').text('Jam mulai wajib diisi.');
					jamMulai.addClass('is-invalid');
					isValid = false;
				}

				if (!nilaiJamSelesai) {
					$('#error_jam_selesai').text('Jam selesai wajib diisi.');
					jamSelesai.addClass('is-invalid');
					isValid = false;
				}

				if (nilaiJamMulai && nilaiJamSelesai) {

					if (nilaiJamMulai < '08:00' || nilaiJamMulai > '16:00') {
						$('#error_jam_mulai').text('Jam mulai harus berada dalam jam kantor (08:00 - 16:00).');
						jamMulai.addClass('is-invalid');
						isValid = false;
					}

					if (nilaiJamSelesai < '08:00' || nilaiJamSelesai > '16:00') {
						$('#error_jam_selesai').text('Jam selesai harus berada dalam jam kantor (08:00 - 16:00).');
						jamSelesai.addClass('is-invalid');
						isValid = false;
					}

					if (nilaiJamMulai >= nilaiJamSelesai) {
						$('#error_jam_selesai').text('Jam selesai harus lebih besar dari jam mulai.');
						jamSelesai.addClass('is-invalid');
						isValid = false;
					}
				}
			}

			var tglMulai = $('#tanggal_mulai').val();
			var tglSelesai = $('#tanggal_selesai').val();

			if (!isSingleDay && tglMulai && tglSelesai && tglMulai > tglSelesai) {
				$('#error_tanggal_selesai').text('Tanggal mulai tidak boleh lebih besar dari tanggal selesai.');
				$('#tanggal_selesai').addClass('is-invalid');
				isValid = false;
			}

			if (!isValid) {
				return;
			}

			if (isSingleDay) {
				$('#tanggal_selesai').val('');
			}

			if (!isTimeBased) {
				$('#jam_mulai, #jam_selesai').val('');
			}

			Swal.fire({
				title: 'Simpan pengajuan?',
				text: 'Pastikan data perizinan sudah benar.',
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: 'Ya, simpan',
				cancelButtonText: 'Batal',
				reverseButtons: true
			}).then(function (result) {
				if (result.isConfirmed) {
					$.ajax({
						url: $(form).attr('action'),
						type: 'POST',
						data: new FormData(form),
						processData: false,
						contentType: false,
						dataType: 'json',
						success: function(response) {
							if (response.status === 'success') {
								window.location.href = '<?= base_url('perizinan') ?>';
							} else if (response.errors) {
								$.each(response.errors, function (field, message) {
									$('#' + field).addClass('is-invalid');
									$('#error_' + field).text(message);
								});
							} else if (response.message) {
								Swal.fire({
									icon: 'error',
									title: 'Gagal',
									text: response.message
								});
							}
						},
						error: function() {
							Swal.fire({
								icon: 'error',
								title: 'Terjadi Kesalahan',
								text: 'Terjadi kesalahan saat menyimpan data.'
							});
						}
					});
				}
			});
		});

        

		$('#form-perizinan input, #form-perizinan textarea').on('input change', function () {
			var id = $(this).attr('id');
			if (id === 'tanggal_mulai' || id === 'tanggal_selesai') {
				return;
			}

			if ($(this).val()) {
				$('#error_' + id).text('');
				$(this).removeClass('is-invalid');
			}
		});

		$('#jenis_perizinan_id').on('change', function () {
			$('#error_jenis_perizinan_id').text('');
			$(this).removeClass('is-invalid');
			updatePerizinanFields();
			updateDuration();
		});

		$('#tanggal_mulai').on('change input', function () {
			if (['6', '7', '8'].indexOf($('#jenis_perizinan_id').val()) !== -1) {
				$('#tanggal_selesai').val($(this).val());
			}
		});

		$('#attachment').on('change', function () {
			var fileName = this.files.length ? this.files[0].name : 'Pilih file';
			$(this).next('.custom-file-label').text(fileName);
			if (this.files.length) {
				$('#current_attachment').text('File baru dipilih: ' + fileName);
			}
		});

		$('.form-control').on('focus', function () {
			$(this).closest('.input-group').addClass('border-primary');
		}).on('blur', function () {
			$(this).closest('.input-group').removeClass('border-primary');
		});

		$('.form-select2').select2({
			theme: 'bootstrap4',
			width: '100%',
			placeholder: 'Pilih Jenis Perizinan',
			allowClear: true
		});

		updatePerizinanFields();


		$('#tanggal_mulai_picker, #tanggal_selesai_picker').datetimepicker({
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


		$('#jam_mulai_picker, #jam_selesai_picker').datetimepicker({
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

		$('#tanggal_mulai, #tanggal_selesai, #jam_mulai, #jam_selesai').on('click focus', function () {
			var pickerId = $(this).data('target');
			if (pickerId) {
				$(pickerId).datetimepicker('show');
			}
		});
		$('#tanggal_mulai_picker').on('change.datetimepicker', function () {
			validateTanggalMulai();
		});
		$('#tanggal_selesai_picker').on('change.datetimepicker', function () {
			validateTanggalSelesai();
		});
		updateDuration();

	}
});
</script>