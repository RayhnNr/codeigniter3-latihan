<script>
$(function() {
    if ($('#example1').length) {
        $('#example1').DataTable({
            scrollX: true,
            autoWidth: false,
            columnDefs: [{
                targets: 'no-sort',
                orderable: false
            }]
        });
    }

    if ($('#customer_status').length) {
        $('#customer_status').bootstrapToggle();
        $('#customer_status').on('change', function() {
            var status = $(this).prop('checked') ? 1 : 0;
            $('#customer_status_hidden').val(status);
        });
    }

});
</script>