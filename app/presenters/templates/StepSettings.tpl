<div style="padding-top: 40px;">
<div class="container">
  <div class="row">
  <div class="col-md-6 offset-md-3">
    <div class="progress" style="max-width: 400px; margin: 0 auto 40px;">
      <div class="progress-bar" role="progressbar" style="width: 50%; height: 28px; background-color: #eceeef; line-height: 28px; font-size: 16px; font-weight: 600; color: #444;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">1. Nový uživatel</div>
      <div class="progress-bar" role="progressbar" style="width: 50%; height: 28px; background-color: #ccc; line-height: 28px; font-size: 16px; font-weight: 600; color: #444;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">2. Nastavení</div>
    </div>
    <p class="h3 text-center" style="margin-top: 48px">Vítejte,</p>
    <p class="text-muted lead text-center">než začnete službu naplno používat, vyplňte prosím následující údaje.</p>
    <form class="form form-signin form-error-control mr-auto ml-auto" style="max-width: 455px;" method="post">
      <div class="form-group">
        <label style="color: #333; font-weight: 600;" for="name">Celé jméno</label>
        <input type="text" name="name" class="form-control form-control-lg" placeholder="např. Jan Novák" required="" autofocus="">
      </div>
      <div class="form-group" style="display: none;">
        <label for="surname">Příjmení</label>
        <input type="text" name="surname" class="form-control form-control-lg" placeholder="Surname">
      </div>
      <div class="form-group">
        <label style="color: #333; font-weight: 600;" for="email">Email</label>
        <input type="email" name="email" class="form-control form-control-lg" placeholder="např. novak@email.cz" required="">
        <p class="form-text text-muted">Slibujeme, že nebudete dostávat nevyžádanou poštu.</p>
      </div>
      <div class="form-group">
        <button class="btn btn-primary btn-lg btn-block" data-api="/profile/save_step_settings" type="submit">Pokračovat</button>
        <div class="form-control-feedback form-error-feedback mt-3" style="display: none;"></div>
        <p class="form-text text-muted text-center mt-3 mb-3">nebo <a href="/auth/logout">odhlásit se</a>.</p>
      </div>
    </form>
  </div>
  </div>
</div>
</div>