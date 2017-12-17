<?php

Session_start();

error_reporting(-1);
ini_set('display_errors', 'On');
set_error_handler("var_dump");

require __DIR__ . '/app/init.php';

use Tracy\Debugger;
use App\Lib\Config;
use App\Lib\DB;

use App\Presenters\TextpagePresenter;

if (Config::get('env/mode') === 'developement') {
  Debugger::enable();
}

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', 'HomepagePresenter/render');
    $r->addRoute('GET', '/d[/{suffix}]', 'HomepagePresenter/render');
    $r->addRoute('GET', '/c/{id:\d+}', 'HomepagePresenter/render');
    $r->addRoute('GET', '/settings', 'ProfilePresenter/renderProfileSettingsPage');
    $r->addRoute('POST', '/auth/ldap/login', 'AuthPresenter/login');
    //$r->addRoute('POST', '/auth/ldap/register', 'AuthPresenter/register');
    $r->addRoute('GET', '/auth/logout', 'AuthPresenter/logout');
    $r->addRoute('GET', '/login', 'AuthPresenter/renderLoginPage');
    $r->addRoute('GET', '/auth/facebook/callback', 'AuthPresenter/fb_callback');
    $r->addRoute('GET', '/newitem', 'CardsPresenter/renderAddPage');
    $r->addRoute('GET', '/newitem/thanks', 'CardsPresenter/renderThanksPage');
    $r->addRoute('GET', '/test', 'CardsPresenter/test');

    $r->addRoute('GET', '/api/get_cards/{offset}[/{labels}]', 'ApiPresenter/get_cards');
    $r->addRoute('GET', '/api/get_card/{id:\d+}', 'ApiPresenter/get_card');
    $r->addRoute('GET', '/api/get_categories/{cats}', 'ApiPresenter/get_categories');
    $r->addRoute('GET', '/api/get_users/{users}', 'ApiPresenter/get_users');
    $r->addRoute('GET', '/api/get_card_activities/{id:\d+}[/{offset:\d+}]', 'ApiPresenter/get_card_activities');
    $r->addRoute('GET', '/api/get_cards_count/{labels}', 'ApiPresenter/get_cards_count');

    $r->addRoute('GET', '/api/get_notifications_for_user/{cards_offset}/{notifications_offset}', 'ApiPresenter/get_notifications_for_user');

    $r->addRoute('POST', '/api/update_card', 'ApiPresenter/update_card');
    $r->addRoute('POST', '/api/archive_card', 'ApiPresenter/archive_card');
    $r->addRoute('POST', '/api/delete_card', 'ApiPresenter/delete_card');
    $r->addRoute('POST', '/api/update_card_state', 'ApiPresenter/update_card_state');

    $r->addRoute('POST', '/api/create_activity', 'ApiPresenter/create_activity');
    $r->addRoute('POST', '/api/delete_activity', 'ApiPresenter/delete_activity');
    $r->addRoute('POST', '/api/update_activity', 'ApiPresenter/update_activity');

    $r->addRoute('POST', '/profile/save_settings', 'ProfilePresenter/save_settings');
    $r->addRoute('POST', '/profile/save_step_settings', 'ProfilePresenter/save_step_settings');
    $r->addRoute('POST', '/add_card', 'CardsPresenter/add_card_form');
    // $r->addRoute('POST', '/add_card/upload_photo', 'CardsPresenter/upload_photo');
    // $r->addRoute('POST', '/add_card/remove_photo', 'CardsPresenter/remove_photo');
    // TODO data pro HP
    $r->addRoute('GET', '/about', 'TextpagePresenter/render_about');
    $r->addRoute('GET', '/faq', 'TextpagePresenter/render_faq');
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $p = new TextpagePresenter();
        $p->render_404();
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo ':( 405 Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = 'App\\Presenters\\' . $routeInfo[1];
        $vars = $routeInfo[2];
        list($class, $method, ) = explode("/", $handler, 2);
        call_user_func_array(array(new $class, $method), $vars);
        break;
}