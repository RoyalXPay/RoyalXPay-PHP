<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-content">
    <div class="container-fluid">
        <h4 class="mb-3"><?= esc($title) ?></h4>

        <div style="border:1px solid #ddd; height:80vh;">
            <iframe src="https://norkaroots.kerala.gov.in/" 
                    width="100%" 
                    height="100%" 
                    frameborder="0"></iframe>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
