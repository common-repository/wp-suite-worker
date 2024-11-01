<table class="wp-list-table widefat fixed striped pages">
  <thead>
    <tr>
      <th>...</th>
      <th>Number of files</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <?php
    var_dump(wps_wsw_ajax::ajax_generate_excludes());
    
    #$files = wps_wsw_ajax::ajax_generate_excludes();
/*
    $files = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator(ABSPATH, RecursiveDirectoryIterator::SKIP_DOTS),
      RecursiveIteratorIterator::SELF_FIRST
    );


    foreach ($files as $file) {
      $current_depth = $files->getDepth();

      if ( (is_dir($file) && $current_depth == 0) || (is_file($file) && $current_depth == 0)) {
        $path = substr($file, strlen($_SERVER['DOCUMENT_ROOT']) + 1);
        $indent = str_repeat('-', $current_depth);
        ?>
        <tr>
          <td><?php echo $indent; ?><?php echo $path; ?></td>
          <td>files</td>
          <td>exclude?</td>
        </tr>
        <?php
      }
    }*/
    ?>
  </tbody>
</table>