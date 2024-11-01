<?php
global $maintenance_options;
?>
<!doctype html>
<title><?php echo $maintenance_options['page-title']; ?></title>
<style>
  body { text-align: center; padding: 150px; }
  h1 { font-size: 50px; }
  body { font: 20px Helvetica, sans-serif; color: #333; }
  article { display: block; text-align: left; width: 650px; margin: 0 auto; }
  a { color: #dc8100; text-decoration: none; }
  a:hover { color: #333; text-decoration: none; }
</style>

<article>
    <h1><?php echo $maintenance_options['header-text']; ?></h1>
    <div>
        <p><?php echo $maintenance_options['text']; ?></p>
        <img src="<?php echo ( $this->options['img_url'] );?>" ></img>
        <p>
          <?php
            if ( $this->options['status_time'] == 1)
                echo 'Maintenance until:' .$this->options['time']. '!!!';
          ?>
        </p>
        <p><?php echo ($this->options['template']);?></p>
    </div>
</article>