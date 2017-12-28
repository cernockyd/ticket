<section class="page-profile">
  <form class="form form-error-control form-settings" method="post">
  <div class="container">

    <div class="row form-profile">
      <div class="col-lg-3 offset-lg-2">
        <h4>Osobní údaje</h4>
        <p class="text-muted">Na tuto adresu Vám prodávající zašle koupený produkt.</p>
      </div>
      <div class="col-lg-5">
        <div class="form-group">
          <label for="name">Celé jméno</label>
          <input type="text" class="form-control" name="name" placeholder="Jméno a příjmení" value="<?php echo (!empty($user->data()->name)) ? $user->data()->name : '' ?>">
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="text" class="form-control" name="email" placeholder="Váš e-mail" value="<?php echo (!empty($user->data()->name)) ? $user->data()->email : '' ?>">
        </div>
        <div class="form-group" style="display: none;">
          <label for="surname" class="sr-only">Příjmení</label>
          <input type="text" name="surname" class="form-control" placeholder="Surname">
        </div>
      </div>
    </div>

    <div class="row form-profile">
      <div class="col-lg-3 offset-lg-2">
        <h4>Oznámení</h4>
        <p class="text-muted">Podle tohoto nastavení Vám budete získávat oznámení a upozornění.</p>
      </div>
      <div class="col-lg-5">
          <div class="form-group">
            <?php foreach ($settings_arr as $key => $setting) { ?>
              <div>
              <label class="custom-control custom-checkbox">
                <input type="checkbox" name="<?php echo $key ?>" <?php echo ($setting['value']) ? 'checked' : ''; ?> value="1" class="custom-control-input">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description"><?php echo $setting['label'] ?></span>
              </label>
              </div>
            <?php } ?>
          </div>
          <div class="form-group" style="display: none;">
            <label for="surname" class="sr-only">Příjmení</label>
            <input type="text" name="surname" class="form-control" placeholder="Surname">
          </div>
          <div class="form-group">
            <button type="submit" data-api="/profile/save_settings" class="btn btn-primary float-lg-right" style="font-weight: 600;">Uložit</button>
          </div>
          <div class="form-control-feedback form-error-feedback mt-3" style="display: none;"></div>
      </div>
    </div>
  </div>

  </form>
</section>