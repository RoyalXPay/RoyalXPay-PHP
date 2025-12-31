<?php echo view('admin/layouts/header');  ?> 

<?php echo $contents ?>
<?php echo view('admin/layouts/footer'); ?>
 </div>
</div>
	<!-- JAVASCRIPT -->
	<script src="<?php echo base_url(PUBLIC_FOLDER.'admin/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
	<script src="<?php echo base_url(PUBLIC_FOLDER.'admin/libs/metismenu/metisMenu.min.js'); ?>"></script>
	<script src="<?php echo base_url(PUBLIC_FOLDER.'admin/libs/simplebar/simplebar.min.js'); ?>"></script>
	<script src="<?php echo base_url(PUBLIC_FOLDER.'admin/libs/node-waves/waves.min.js'); ?>"></script>

	<script src="<?php echo base_url(PUBLIC_FOLDER.'admin/js/app.js'); ?>"></script>

	<script src="<?php echo base_url(PUBLIC_FOLDER.'admin/js/custom.js'); ?>"></script>
	
    
    <!-- Required datatable js -->
    <script src="<?= base_url(PUBLIC_FOLDER . 'admin/libs/datatables.net/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= base_url(PUBLIC_FOLDER . 'admin/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>

    <!-- Buttons examples -->
    <script src="<?= base_url(PUBLIC_FOLDER . 'admin/libs/datatables.net-buttons/js/dataTables.buttons.min.js'); ?>"></script>
    <script src="<?= base_url(PUBLIC_FOLDER . 'admin/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js'); ?>"></script>
    <script src="<?= base_url(PUBLIC_FOLDER . 'admin/libs/jszip/jszip.min.js'); ?>"></script>
    <script src="<?= base_url(PUBLIC_FOLDER . 'admin/libs/pdfmake/build/pdfmake.min.js'); ?>"></script>
    <script src="<?= base_url(PUBLIC_FOLDER . 'admin/libs/pdfmake/build/vfs_fonts.js'); ?>"></script>
    <script src="<?= base_url(PUBLIC_FOLDER . 'admin/libs/datatables.net-buttons/js/buttons.html5.min.js'); ?>"></script>
    <script src="<?= base_url(PUBLIC_FOLDER . 'admin/libs/datatables.net-buttons/js/buttons.print.min.js'); ?>"></script>
    <script src="<?= base_url(PUBLIC_FOLDER . 'admin/libs/datatables.net-buttons/js/buttons.colVis.min.js'); ?>"></script>

	<!-- Sweet Alerts js -->
	<script src="<?php echo base_url(PUBLIC_FOLDER.'admin/libs/sweetalert2/sweetalert2.min.js'); ?>"></script>

	<!-- Sweet alert init js-->
	<script src="<?php echo base_url(PUBLIC_FOLDER.'admin/js/pages/sweet-alerts.init.js'); ?>"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

	<!-- JAVASCRIPT -->

	<?php echo view('admin/_calendar'); ?>

	</body>
</html>