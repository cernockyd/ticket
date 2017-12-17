<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="csrf_token" content="<?=$this->e($csrf_token)?>">
    <meta name="author" content="">

    <link rel="apple-touch-icon" sizes="57x57" href="/assets/favicons/apple-touch-icon-57x57.png"/><link data-react-helmet="true" rel="apple-touch-icon" sizes="60x60" href="/assets/favicons/apple-touch-icon-60x60.png"/><link data-react-helmet="true" rel="apple-touch-icon" sizes="72x72" href="/assets/favicons/apple-touch-icon-72x72.png"/><link data-react-helmet="true" rel="apple-touch-icon" sizes="76x76" href="/assets/favicons/apple-touch-icon-76x76.png"/><link data-react-helmet="true" rel="apple-touch-icon" sizes="114x114" href="/assets/favicons/apple-touch-icon-114x114.png"/><link data-react-helmet="true" rel="apple-touch-icon" sizes="120x120" href="/assets/favicons/apple-touch-icon-120x120.png"/><link data-react-helmet="true" rel="apple-touch-icon" sizes="144x144" href="/assets/favicons/apple-touch-icon-144x144.png"/><link data-react-helmet="true" rel="apple-touch-icon" sizes="152x152" href="/assets/favicons/apple-touch-icon-152x152.png"/><link data-react-helmet="true" rel="apple-touch-icon" sizes="180x180" href="/assets/favicons/apple-touch-icon-180x180.png"/><link data-react-helmet="true" rel="icon" type="image/png" href="/assets/favicons/favicon-32x32.png" sizes="32x32"/><link data-react-helmet="true" rel="icon" type="image/png" href="/assets/favicons/android-chrome-192x192.png" sizes="192x192"/><link data-react-helmet="true" rel="icon" type="image/png" href="/assets/favicons/favicon-16x16.png" sizes="16x16"/>

    <title><?=$this->e($title)?></title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="/app.css">
  </head>

  <body data-slug="<?=$slug?>">
  <div id="content">
  <?php if ($user->isLoggedIn() AND $user->isComplete()) { ?>
    <nav class="navbar navbar-toggleable-md navbar-light bg-faded container-fluid mx-3">
      <div class="row">
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon "></span>
      </button>
      <div class="col-md-2">
        <a class="navbar-brand <?php if ($user->isAdmin()) { ?><?php } ?>" href="/"><span class="card-icon icon-default">TS</span>SŠIEŘ</a>
      </div>
      <?php if ($user->isAdmin()) { ?><div class="col-md-5">
        <div class="form-group form-search" style="display: none;">
          <select class="form-control-search form-control" name="search"></select>
          <input type="text" class="form-control-search form-control" name="search">
        </div>
      </div>
      <?php } ?>
      <div class="<?php echo (!$user->isAdmin()) ? 'col-md-10' : 'col-md-5'; ?>">
        <div class="collapse navbar-collapse" id="navbarNav">
          <?php if (!$user->isAdmin()) { ?>
          <div class="btn-group btn-group-header btn-group-lg mr-auto ml-auto" style="<?php echo ($slug=='/newitem') ? 'padding-left: 40px;' : ''; ?>" role="group" aria-label="Basic example">
            <a href="/d" class="history-link btn">Moje požadavky</a>
            <a href="/d/archive" class="history-link btn">Archiv</a>
            <a href="/settings" class="btn <?php echo ($slug=='/settings') ? 'active' : '' ?>">Nastavení</a>
          </div>
          <a href="/auth/logout" class="nav-link text-muted mr-1">Odhlásit se</a>
            <?php if ($slug != '/newitem') { ?>
              <a href="/newitem" class="btn btn-lg" style="background-color: #f47932; border-color: #f47932; color: #fff; font-weight: 600; border-bottom: 2px solid #cc6022;">Vystavit požadavek</a>
            <?php } ?>
          <?php } else { ?>
          <ul class="mr-auto"></ul>

          <ul class="float-lg-right navbar-nav">
            <?php ?><li class="nav-item dropdown nav-item-notification">
              <ul class="dropdown">
                 <a class="nav-link nav-link-notification notification-active" href="#" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 <img class="mr-1 icon icon-notification icon-attention" src="/assets/icons/ic_notification.svg" width="16">
                </a>
                <div class="dropdown-menu dropdown-notifications dropdown-arrow" aria-labelledby="dropdownMenuButton">
                  <div class="nav-item notifications-headline">Oznámení</div>
                  <ul class="list-notifications" data-id-count="0" data-users-count="0">

                  </ul>
                  <div class="nav-item notifications-btn notifications-delete"><a href="#" class="btn-block btn-notif-pull" data-cards="0" data-activities="0">Načíst další</a></div>
                </div>
              </ul>
            </li>
            <?php  ?>
            <li class="nav-item dropdown ml-3">
              <ul class="dropdown-profile">
                 <a class="nav-link" href="#" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <img class="rounded-circle profile-picture" src="https://www.gravatar.com/avatar/<?php echo md5($user->data()->email) ?>?s=180&d=identicon" alt="">
                  <?php
                  if (!empty($user->data()->fb_id)) {
                  } echo (!empty($user->data()->name)) ? $user->data()->name : $user->data()->email ; ?>
                </a>
                <ul class="dropdown-menu dropdown-arrow" aria-labelledby="dropdownMenuButton">
                  <li class="nav-item"><a class="nav-item dropdown-item" href="/settings">Nastavení</a></li>
                  <li class="dropdown-divider"></li>
                  <li class="nav-item"><a class="nav-item dropdown-item" href="/auth/logout">Odhlásit se</a></li>
                </ul>
              </ul>
            </li>
          </ul>
          <?php } ?>
        </div>
      </div>
    </div>
  </nav>
<?php } ?>