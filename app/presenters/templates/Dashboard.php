<div class="block-dashboard px-3">
<div class="container-fluid">
<div class="row">
  <div class="col-md-2">
    <div class="dashboard-sidebar">
      <div class="sidebar-widget">
        <div class="widget-headline">Základní</div>
        <ul class="widget-navigation">
          <li><a class="history-link history-link-count" href="/d"><span class="card-icon icon-blue">A</span>Vše</a></li>
          <li><a class="history-link history-link-count" href="/d/1"><span class="card-icon icon-orange">!</span>Naléhavé</a></li>
        </ul>
      </div>
      <?php if ($user->isAdmin()) { ?>
      <div class="sidebar-widget">
        <div class="widget-headline">Štítky</div>
        <ul class="widget-navigation">
          <li><a class="history-link" href="/d/2"><span class="card-icon icon-default">H</span>Hardware</a></li>
          <li><a class="history-link" href="/d/3"><span class="card-icon icon-default">S</span>Software</a></li>
          <li><a class="history-link" href="/d/4"><span class="card-icon icon-default">N</span>Školní síť</a></li>
          <li><a class="history-link" href="/d/5"><span class="card-icon icon-default">?</span>Jiné</a></li>
        </ul>
      </div>
      <div class="sidebar-widget">
        <ul class="widget-navigation">
          <li><a class="history-link" href="/d/archive"><span class="card-icon icon-default">A</span>Archiv</a></li>
        </ul>
      </div>
      <?php } ?>
    </div>
  </div>
  <div class="col-md-10 pl-0">
  <?php if ($user->isAdmin()) { ?>
  <ul class="nav justify-content-end mb-3">
    <li class="nav-item">
      <a class="btn btn-secondary ml-2" href="/api/get_cards/export/all">Exportovat</a>
    </li>
    <li class="nav-item">
      <a class="btn btn-secondary ml-2" href="/newitem"><img class="mr-1 icon" style="position: relative; top: -2px; opacity: .6;" src="/assets/icons/ic_plus.svg" width="12"> Přidat</a>
    </li>
  </ul>
  <?php } ?>
    <div>
      <div class="spinner-list spinner"></div>
      <ul class="list-cards"></ul>
      <div class="text-center">
        <a href="#" class="btn btn-lg btn-load-more btn-secondary">Načíst další (16)</a>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<div class="window-overlay" style="display: none;">
  <div class="window card">
    <a href="#" class="btn-close" data-context="card"></a>
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
            <textarea class="form-control-newcomment form-control autogrow form-control-autogrow autogrow-short" name="comment" placeholder="Napište komentář..." rows="2" style="min-height: 58px;"></textarea>
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
          <div class="text-center module-activities-more">
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
</div>
<input class="store-taxonomies" type="hidden" value="[]">
<input class="store-users" type="hidden" value="[]">