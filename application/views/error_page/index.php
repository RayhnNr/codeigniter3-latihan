<?php
$color = [404 => 'warning', 403 => 'danger', 500 => 'danger'];
$icon  = [404 => 'fa-search', 403 => 'fa-lock', 500 => 'fa-exclamation-triangle'];
$c = isset($color[$error_code]) ? $color[$error_code] : 'danger';
$i = isset($icon[$error_code])  ? $icon[$error_code]  : 'fa-exclamation-triangle';
?>
<div class="text-center py-5">
    <h1 class="text-<?php echo $c; ?>" style="font-size:6rem; font-weight:700;">
        <?php echo (int) $error_code; ?>
    </h1>
    <h3 class="mt-2">
        <i class="fas <?php echo $i; ?> text-<?php echo $c; ?>"></i>
        <?php echo html_escape($title); ?>
    </h3>
    <p class="text-muted mt-3"><?php echo html_escape($message); ?></p>

    <!-- <div class="mt-4">
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <a href="<?php echo base_url(); ?>" class="btn btn-primary">
            <i class="fas fa-home"></i> Ke Dashboard
        </a>
    </div> -->
</div>