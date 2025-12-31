<!doctype html>
<html lang="en">
<?php
$session = session();
/* 
Include the header view file
This will load the content from header.php and insert it at this point
*/
echo $this->include('frontend/layouts/header');
?>

<body >
     <?= view('frontend/layouts/topmenu'); ?>
   
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content"><!-- Main content area -->
            <?php
            /* 
            Render the 'content' section
            This is where the specific content from the extending view (e.g., home.php) will be inserted
            */
            echo $this->renderSection('content');
            ?>
        </div><!-- end main content -->

    
    <?php
    /* 
    Include the footer view file
    This will load the content from footer.php and insert it at this point
    */
    echo $this->include('frontend/layouts/footer');
    ?>
</body>

</html>