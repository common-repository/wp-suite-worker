<div class="wps-tabs">
  <ul>
    <?php do_action('wps_addon_tabs'); ?>
    <li><a href="#connect-manager">Remote Manager</a></li>
  </ul>

  <div id="connect-manager">
    <?php wps_wsw_pages::site_clones_table(); ?>
  </div>

  <?php do_action('wps_addon_tab_content'); ?>

</div>