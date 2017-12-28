<div class="block-dashboard px-3">
<div class="container">
<?php if($slug == '/') { ?>
<div class="row">
  obrazek
</div>
</div>
</div>
<?php } else { ?>
<div class="row">
  <div class="col-md-12">
    <div>
      <div class="spinner-list spinner" style="display: none;"></div>
      <ul class="list-cards"></ul>
      <div class="text-center">
        <a href="#" class="btn btn-lg btn-load-more btn-secondary" style="display: none;">Načíst další (16)</a>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<div class="window-overlay" style="display: none;">
  <div class="window card">
    <a href="#" class="btn-close"  data-context="card"></a>
    <div class="container-fluid">
      <div class="row">
      <div class="col-md-9 window-main-col">
        <div class="window-module">
          <div class="window-text window-title">
          </div>
          <div class="window-text window-description">
            <a href="#" class="btn btn-primary btn-save" style="display: none; font-weight: 600;">Uložit</a>
            <a href="#" class="btn btn-exit" style="display: none;"></a>
          </div>
        </div>
        <div class="window-module">
          <h3 class="window-module-headline">Přidat komentář</h3>
          <div class="window-module-newcoment">
            <img class="rounded-circle profile-picture" src="https://www.gravatar.com/avatar/<?=$user_hash?>?s=32&d=identicon" alt="">
            <textarea class="form-control-newcomment form-control form-control-autogrow autogrow-short" name="comment" placeholder="Napište komentář..." rows="2" style="min-height: 58px;"></textarea>
            <div>
              <a href="#" class="btn btn-primary btn-comment" style="font-weight: 600;">Odeslat</a>
              <label class="custom-control custom-checkbox ml-3">
                <input type="checkbox" name="notifications-force" value="1" class="custom-control-input">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Vynutit oznámení emailem <span class="tooltip-question tooltip-question-default" data-toggle="tooltip" data-placement="right" title="Druhá strana obdrží email i v případě, že má emaily vypnuty.">?</span></span>
              </label>
            </div>
          </div>
        </div>
        <div class="window-module window-module-activities">
          <h3 class="window-module-headline">Aktivity</h3>
          <div class="spinner-activities spinner-small"></div>
          <ul class="list-activities">
          </ul>
          <div class="text-center">
            <a href="#" class="btn btn-lg btn-load-more btn-secondary" style="display: none;">Načíst další</a>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="window-sidebar">
          <div class="window-sidebar-heaadline">Stav</div>

          <div class="btn-window-settings">
            <a href="#" class="btn btn-secondary btn-state btn-dropdown-window btn-block" data-context="stateChange"><span class="span-btn">Žádný stav</span><img class="ml-3" src="/assets/icons/ic_options.svg" alt=""></a>
            <div class="dropdown-window dropdown-window-state" data-layer="stateChange" data-context="stateChange" style="display: none;">
              <a href="#" class="btn-close" data-context="stateChange"></a>
              <label for="state">Vyberte stav</label>
              <div class="custom-controls-stacked mb-2 state-controls-wrapper">
                <input type="hidden" name="savedstate" value="0">
                <label class="custom-control custom-radio">
                  <input name="state" type="radio" value="1" class="custom-control-input">
                  <span class="custom-control-indicator"></span>
                  <span class="custom-control-description">Přečteno</span>
                </label>
                <label class="custom-control custom-radio">
                  <input name="state" type="radio" value="2" class="custom-control-input">
                  <span class="custom-control-indicator"></span>
                  <span class="custom-control-description">V řešení</span>
                </label>
                <label class="custom-control custom-radio">
                  <input name="state" type="radio" value="3" class="custom-control-input">
                  <span class="custom-control-indicator"></span>
                  <span class="custom-control-description">Vyřešeno</span>
                </label>
              </div>
              <div class="mb-1 archive-controls-wrapper">
                <label class="custom-control custom-checkbox">
                  <input name="archive" type="checkbox" value="1" class="custom-control-input">
                  <span class="custom-control-indicator"></span>
                  <span class="custom-control-description">Archivováno</span>
                </label>
              </div>
              <div class="state-comment-wrapper" style="display: none;">
                <label for="">Povinný komentář <span class="tooltip-question tooltip-question-default" data-toggle="tooltip" data-placement="right" title="Tento komentář bude zobrazen u oznámení o vyřešení závady.">?</span></label>
                <textarea class="form-control form-control-statecomment mb-2 autogrow autogrow-short" rows="1" placeholder="Popis řešení závady" data-text="Popis řešení závady" style="height: 38px; overflow: hidden; word-wrap: break-word;"></textarea>
              </div>
              <div>
                <a href="#" class="btn btn-primary btn-state-save mb-2" style="font-weight: 600;">Uložit</a>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>
<input class="store-taxonomies" type="hidden" value="[]">
<input class="store-users" type="hidden" value="[]">
<?php } ?>