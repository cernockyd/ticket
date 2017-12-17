<section class="page-newitem mt-3">
  <div class="container">
    <div class="row">
    <div class="col-md-6 offset-md-3">
    <form class="form form-addproduct form-error-control" method="post">
      <div class="newitem-lead col-lg-8 text-center offset-lg-2">
        <div class="card" style="border-color: #fff; display: block; width: 100%;">
          <h4 class="card-title mt-3">Vyplňte formulář</h4>
        </div>
      </div>
      <div class="form-group pb-3">
        <label for="subject">Předmět</label>
        <input type="text" class="form-control" name="subject" placeholder="Např.: Nefunkční projektor v učebně 203 ">
        <small class="form-text text-muted">Zadejte předmět tak, aby z něj bylo okamžitě patrné o co se jedná.</small>
      </div>
      <div class="form-group">
        <label for="categories">Brání Vám závada ve výuce nebo naléhavé práci?</label>
        <ul class="list-unstyled">
          <li>
            <label class="custom-control custom-radio">
              <input type="radio" name="urgent" checked value="0" class="custom-control-input">
              <span class="custom-control-indicator"></span>
              <span class="custom-control-description">Ne</span>
            </label>
            <label class="custom-control custom-radio">
              <input type="radio" name="urgent" value="1" class="custom-control-input">
              <span class="custom-control-indicator"></span>
              <span class="custom-control-description">Ano, závada je urgentní</span>
            </label>
          </li>
        </ul>
      </div>
      <div class="form-group">
        <label for="categories">Typ problému</label>
        <ul class="list-unstyled">
          <li>
            <label class="custom-control custom-radio">
              <input type="radio" name="category" value="3" class="custom-control-input">
              <span class="custom-control-indicator"></span>
              <span class="custom-control-description">Software</span>
            </label>
            <label class="custom-control custom-radio">
              <input type="radio" name="category" value="2" class="custom-control-input">
              <span class="custom-control-indicator"></span>
              <span class="custom-control-description">Hardware</span>
            </label>
            <label class="custom-control custom-radio">
              <input type="radio" name="category" value="4" class="custom-control-input">
              <span class="custom-control-indicator"></span>
              <span class="custom-control-description">Školní síť</span>
            </label>
            <label class="custom-control custom-radio">
              <input type="radio" name="category" checked value="5" class="custom-control-input">
              <span class="custom-control-indicator"></span>
              <span class="custom-control-description">Jiné</span>
            </label>
          </li>
        </ul>
      </div>
      <div class="form-group pb-3">
        <label for="number">Číslo učebny <i>(nepovinné)</i></label>
        <input type="number" class="form-control" min="0" max="500" step='1' pattern="\d+" name="number" style="max-width: 100px;">
      </div>
      <div class="form-group">
        <label for="description">Podrobnosti</label>
        <textarea class="form-control form-control-autogrow" name="description" rows="3"></textarea>
        <small class="form-text text-muted">Nezapomeňte popsat všechny okolnosti. Například umístění závadného počítače.</small>
      </div>
      <div class="form-group">
        <input type="hidden" name="taxonomies" style="display: none;">
        <button type="submit" data-api="/add_card" class="btn btn-primary btn-lg btn-block" style="background-color: #4078c0;border-color: #4078c0;">Odeslat požadavek</button>
      </div>
      <div class="form-control-feedback form-error-feedback mt-3" style="display: none;"></div>
    </form>
    </div>
    </div>
    <p class="lead text-center" style="margin: 16px 0 0;">Máte dotaz? <a href="/about" style="font-weight: 600; text-decoration: underline;">Často kladené otázky.</a></p>
  </div>
</section>