<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Premium Multipurpose Admin & Dashboard Template" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= base_url('assets/images/favicon.png'); ?>">

    <!-- Title and Meta tags -->
    <?= include_title(); ?>
    <?= include_metas(); ?>

    <!-- Include CSS files -->
    <link href="<?= base_url('assets/libs/select2/css/select2.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css'); ?>" rel="stylesheet" />
     <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link href="<?= base_url('assets/libs/magnific-popup/magnific-popup.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" id="bootstrap-style" rel="stylesheet" />
    <link href="<?= base_url('assets/libs/datatables.select/css/bootstrap-select.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/icons.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/app.min.css'); ?>" id="app-style" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <link href="<?= base_url('assets/css/custom.css'); ?>" rel="stylesheet" />
    <!-- JavaScript Variables -->
    <script>
        var baseUrl = "<?= base_url('assets'); ?>";
    </script>

    <!-- Include JavaScript files -->
    <script src="<?= base_url('assets/libs/jquery/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/plugin.js'); ?>"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
      <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <style>
        #sidebar-menu ul li a {
    display: block;
    padding: .625rem 1.5rem;
    color: #545a6d;
    position: relative;
    font-size: 12px;
    -webkit-transition: all .4s;
    transition: all .4s;
}
        </style>
</head>