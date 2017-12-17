<div style="padding-top: 100px;">
<div class="container">
  <div class="row">
  <div class="col-md-6 offset-md-3">
    <p class="h3 text-center mb-3">Přihlaste se pro nahlášení závady</p>
    <p class="text-muted lead text-center">Po přihlášení budete moci nahlásit závadu v učebně, sledovat <br>průběh řešení a dostávat oznámení emailem.</p>
    <form class="form form-signin form-error-control mr-auto ml-auto" style="max-width: 455px;" method="post">
      <div class="form-group">
        <label style="color: #333; font-weight: 600;" for="name">Školní účet <span class="tooltip-question tooltip-question-black" data-toggle="tooltip" data-placement="right" title="Vaše jméno, kterým se přihlašujete do školní sítě např. při zapínání počítače.">?</span></label>
        <input type="text" name="name" class="form-control form-control-lg" placeholder="např. jannovak" required="" autofocus="">
      </div>
      <div class="form-group" style="display: none;">
        <label for="inputPassword">Příjmení</label>
        <input type="text" name="surname" class="form-control form-control-lg" placeholder="Surname">
      </div>
      <div class="form-group">
        <label style="color: #333; font-weight: 600;" for="password">Vaše heslo <span class="tooltip-question tooltip-question-black" data-toggle="tooltip" data-placement="right" title="Heslo, se kterým se přihlašujete do školní sítě např. při zapínání počítače.">?</span></label>
        <input type="password" name="password" class="form-control form-control-lg" placeholder="např. ••••••••••••" required="">
      </div>
      <button class="btn btn-primary btn-lg btn-block" data-api="/auth/ldap/login" type="submit">Přihlásit se</button>
      <div class="form-control-feedback form-error-feedback mt-3" style="display: none;"></div>
      <p class="lead text-center" style="margin: 40px 0 0;">Jste tu poprvé? <a href="/about" style="font-weight: 600; text-decoration: underline;">Přečtěte si, co služba dokáže.</a></p>
      <p class="lead text-center" style="margin: 16px 0 0;">Nevíte si rady? <a href="/faq" style="font-weight: 600; text-decoration: underline;">Často kladené otázky.</a></p>
    <footer>
      <p class="text-muted text-center">Copyright &copy; 2017 - <?php echo(Date('Y')); ?> SŠIEŘ - Rožnov pod Radhoštěm</p>
    </footer>
    </form>
  </div>
  </div>
</div>
</div>