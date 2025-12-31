<?php   // echo "<pre>"; print_r($paginate);
if (!empty($paginate['haveToPaginate'])) { ?>

  <div class="row">

    <div class="col-sm-12 col-md-5">
      <div class="dataTables_info">Showing <?php echo $paginate['getFirstIndice']; ?> to <?php echo $paginate['getLastIndice']; ?> of <?php echo $paginate['getNbResults']; ?> entries</div>
    </div>
    <div class="col-sm-12 col-md-7">
      <div class="dataTables_paginate paging_simple_numbers">
        <ul class="pagination">

          <?php

          $extraVars = '';
          if (is_array($varExtra)) {
            foreach ($varExtra as $key => $value) {
              $extraVars .= '&' . $key . '=' . $value;
            }
          }

          if ($paginate['getPage'] == 1) {
            echo ("<li class='paginate_button page-item previous disabled'><a href='javascript::void(0)' title='Previous' class='page-link'>" . '< Prev' . "</a></li>");
          } else {
            echo ("<li class='paginate_button page-item previous'><a href='" . site_url($siteurl) . '?page=' . $paginate['getPreviousPage'] . $extraVars . "' title='Previous' class='page-link'>" . '< Prev' . "</a></li>");
          }

          ?>

          <?php
          $links = $paginate['getLinks'];
          foreach ($links as $page) {
            echo (($page == $paginate['getPage']) ? "<li class='paginate_button page-item active' ><a href='#' class='page-link'>" . $page . "</a></li>" : "<li class='paginate_button page-item'><a href='" . site_url($siteurl) . "?page=" . $page . $extraVars . "' class='page-link'>" . $page . "</a></li>");
          }
          ?>
          <?php

          if ($paginate['getPage'] != $paginate['getLastPage']) {
            echo ("<li class='paginate_button page-item next'><a href='" . site_url($siteurl) . "?page=" . $paginate['getNextPage'] . $extraVars . "', title='Next' class='page-link'>" . 'Next > ' . "</a></li>");
          } else {

            echo ("<li class='paginate_button page-item next disabled'><a href='javascript::void(0);', title='Next' class='page-link'>" . 'Next > ' . "</a></li>");
          }

          ?>
        </ul>

      </div>
    </div>
  </div>
<?php } ?>