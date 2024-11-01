
<div style="text-align: center;">
  <h3 style="text-align: center;">Your site <?php echo site_url(); ?> is currently <span class="error">NOT</span> connected with PremiumWPSuite Manager.</h3>

  <form method="POST" action="" id="register-box">
    <div class="field-wrapper">
      <input type="text" name="username" placeholder="Username..." value="" class="username" />
      <span class="fa fa-user"></span>
    </div>
    <div class="field-wrapper">
      <input type="text" name="email" placeholder="E-mail..." value="" class="email" />
      <span class="fa fa-envelope"></span>
    </div>
    <div class="field-wrapper">
      <input type="password" name="password" placeholder="Password..." value="" class="password" />
      <span class="fa fa-asterisk"></span>
    </div>
    <div class="field-wrapper">
      <input type="password" name="password-repeat" placeholder="Password confirmation..." value="" class="password" />
      <span class="fa fa-asterisk"></span>
    </div>
    <input type="submit" name="submit" value="Connect with manager, it's FREE!"/>
    <a href="#" class="already-have-account">I already have an account!</a>
  </form>
  
  <form method="POST" action="" id="connect-box" style="display: none;">
    <div class="field-wrapper">
      <input type="text" name="username" placeholder="Username or E-Mail..." value="" class="email" />
      <span class="fa fa-envelope"></span>
    </div>
    <div class="field-wrapper">
      <input type="password" name="password" placeholder="Password..." value="" class="password" />
      <span class="fa fa-asterisk"></span>
    </div>
    <input type="submit" name="submit" value="Connect!"/>
    <a href="#" class="dont-have-account">I don't have an account!</a>
  </form>

</div>