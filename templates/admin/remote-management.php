<div class="wrap">

  <h2 class="page-title">Remote Management</h2>
  
  <hr/>
  
  <p>Your remote mangagement key is:</p>
  <?php
    $creds = get_option(WPS_WSW_CREDS);
  ?>
  <strong><?php echo $creds['key']; ?></strong>
  <p class="desc">Copy this key to your WP Management Suite.</p>

</div>