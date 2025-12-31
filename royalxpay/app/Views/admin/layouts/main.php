<!doctype html>
<html lang="en">
<?php
$session = session();
/* 
Include the header view file
This will load the content from header.php and insert it at this point
*/
echo $this->include('admin/layouts/header');
?>

<body data-sidebar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper"> <!-- Main layout wrapper -->
        <?= view('admin/layouts/topmenu'); ?>

        <?= view('admin/layouts/leftpanel'); ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content"> <!-- Main content area -->
            <?php
            /* 
            Render the 'content' section
            This is where the specific content from the extending view (e.g., home.php) will be inserted
            */
            echo $this->renderSection('content');
            ?>
        </div><!-- end main content -->

    </div><!-- END layout-wrapper -->
<script>

document.addEventListener("DOMContentLoaded", function () {

    function updateUAEDateTime() {
        const now = new Date();

        const optionsDate = {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric',
            timeZone: 'Asia/Dubai'
        };
        const dayYear = new Intl.DateTimeFormat('en-US', optionsDate).format(now);

        document.getElementById('day-yearDisplay').innerText = dayYear;
    }

    updateUAEDateTime();
    setInterval(updateUAEDateTime, 1000);

});
</script>




    <?php
    /* 
    Include the footer view file
    This will load the content from footer.php and insert it at this point
    */
    echo $this->include('admin/layouts/footer');
    ?>
</body>

</html>