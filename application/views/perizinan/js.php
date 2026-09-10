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

			if (!startDate || !endDate) {
				$('#duration_info').addClass('d-none').removeClass('text-danger').html('');
				return;
			}

			var duration = 0;
			var currentDate = new Date(startDate);

			while (currentDate <= endDate) {
				var day = currentDate.getDay();

				// Minggu = 0, tidak dihitung
				if (day !== 0) {
					duration++;
				}

				currentDate.setDate(currentDate.getDate() + 1);
			}

			$('#duration_info')
				.removeClass('d-none text-danger')
				.addClass('text-info')
				.html('<i class="fas fa-info-circle"></i> Durasi: <strong>' + duration + ' hari</strong>.');
		}

        var today = new Date();
        var tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1);
        var stringBesok = formatDateToInput(tomorrow);
        $('#tanggal_mulai').attr('min', stringBesok);
        $('#tanggal_selesai').attr('min', stringBesok);

        $('#tanggal_mulai').on('change input', function() {
            var startDate = parseDate($(this).val());
            var todayCheck = new Date();
            todayCheck.setHours(0, 0, 0, 0);

            if (startDate && startDate < todayCheck) {
                var nextDay = new Date();
                nextDay.setDate(nextDay.getDate());
                $(this).val(formatDateToInput(nextDay));
                $(this).removeClass('is-invalid');
                $('#error_tanggal_mulai').text('');
            }

            if ($(this).val()) {
                $('#tanggal_selesai').attr('min', $(this).val());
                var endDate = parseDate($('#tanggal_selesai').val());
                var currentStartDate = parseDate($(this).val());
                if (endDate && currentStartDate && endDate < currentStartDate) {
                    $('#tanggal_selesai').val($(this).val());
                }
            }
        });

        $('#tanggal_selesai').on('change input', function() {
            var startDate = parseDate($('#tanggal_mulai').val());
            var endDate = parseDate($(this).val());
            var todayCheck = new Date();
            todayCheck.setHours(0, 0, 0, 0);

            if (endDate && endDate < todayCheck) {
                var nextDay = new Date();
                nextDay.setDate(nextDay.getDate());
                $(this).val(formatDateToInput(nextDay));
                $(this).removeClass('is-invalid');
                $('#error_tanggal_selesai').text('');
                endDate = parseDate($(this).val());
            }

            if (startDate && endDate && endDate < startDate) {
                $(this).val($('#tanggal_mulai').val());
                $(this).removeClass('is-invalid');
                $('#error_tanggal_selesai').text('');
            }
        });

		$('#form-perizinan').on('submit', function (event) {
			event.preventDefault();
			$('#form-perizinan small.text-danger').text('');
			$('.is-invalid').removeClass('is-invalid');

			var form = this;
			var isValid = true;
			var fields = [
				{ selector: '#jenis_perizinan_id', message: 'Jenis perizinan wajib dipilih.' },
				{ selector: '#tanggal_mulai', message: 'Tanggal mulai wajib diisi.' },
				{ selector: '#tanggal_selesai', message: 'Tanggal selesai wajib diisi.' },
				{ selector: '#alasan', message: 'Deskripsi atau alasan wajib diisi.' }
			];

			$.each(fields, function (_, field) {
				var input = $(field.selector);
				if (!input.val() || !input.val().trim()) {
					$('#error_' + input.attr('id')).text(field.message);
					input.addClass('is-invalid');
					isValid = false;
				}
			});

			if ($('#jenis_perizinan_id').val() == '6') {

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

			if (tglMulai && tglSelesai && tglMulai > tglSelesai) {
				$('#error_tanggal_selesai').text('Tanggal mulai tidak boleh lebih besar dari tanggal selesai.');
				$('#tanggal_selesai').addClass('is-invalid');
				isValid = false;
			}

			if (!isValid) {
				return;
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

        $('#tombol-hapus').click(function(e){
			e.preventDefault();
			var deleteUrl = $(this).attr('href');

			Swal.fire({
				title: 'Hapus pengajuan ini?',
				text: 'Data yang dihapus tidak dapat dikembalikan.',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Ya, hapus',
				cancelButtonText: 'Batal',
				confirmButtonColor: '#d33',
				reverseButtons: true
			}).then(function (result) {
				if (result.isConfirmed) {
					window.location.href = deleteUrl;
				}
			});
		});

		$('#form-perizinan input, #form-perizinan textarea').on('input change', function () {
			var id = $(this).attr('id');
			if ($(this).val()) {
				$('#error_' + id).text('');
				$(this).removeClass('is-invalid');
			}
		});

		$('#jenis_perizinan_id').on('change', function () {
			$('#error_jenis_perizinan_id').text('');
			$(this).removeClass('is-invalid');
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
			width: '100%'
		});

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
			updateDuration();
		});
		$('#tanggal_selesai_picker').on('change.datetimepicker', updateDuration);
		$('#tanggal_mulai, #tanggal_selesai').on('keyup input change', updateDuration);
		updateDuration();

	}
});
</script>
