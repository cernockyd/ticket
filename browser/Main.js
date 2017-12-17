// JS part of application should be rewritten.
// Messing with jQuery isnt good way.
// Data pulling  from api routes / callbacks should be separated to functions and treated better way.

import './styles/app.scss';
import $ from 'jquery';
import 'tether';
import 'bootstrap';
import autosize from 'autosize';
import {
  restyle_state,
  get_cards,
  close_card,
  open_card,
  render_card
} from './Lists';
import {
  update_card,
  update_card_state,
  archive_card,
  delete_card,
  load_cards_count,
} from './Card';
import './Forms';
import Notifications from './Notifications';

$('[data-toggle="tooltip"]').tooltip();

Notifications();

var slug = $('body').data('slug');

var state = [];

/**
 * Open card / Show window
 */
var listCards = $('.list-cards');
var cardWindowOverlay = $('.window-overlay');
var cardWindow = cardWindowOverlay.find('.window');
var btnClose = cardWindow.find('.btn-close[data-context=card]');
$('.list-cards').on('click', '.item-card', function(e) {
  e.preventDefault();
  var id = $(this).data('id');
  open_card(id, cardWindow, cardWindowOverlay, listCards);
});

btnClose.click(function(e) {
  e.preventDefault();
  close_card();
});

cardWindowOverlay.click(function(e) {
  if ($(e.target).hasClass('window-overlay')) {
    close_card();
  }
});


/**
 * History links (sidebar menu)
 */
var historyLink = $('.history-link');
window.onpopstate = function (event) {
  var pathname = window.location.pathname;
  $('.item-card').remove();
  var path = pathname.split('/');
  var params = 'all';
  if (path[2]) {
    params = path[2];
  }
  get_cards(0, params);
}

historyLink.click(function(e) {
  var activehistoryLink = $('.history-link.active');
  if (!$(this).hasClass('active')) {
    $('.item-card').remove();
    var params = 'all';
    var href = $(this).attr('href');
    if (slug != '/d') {
      window.location.href = href;
    }
    window.history.pushState({}, 'Dashboard', href);
    var path = href.split('/');
    if (path[2]) {
      params = path[2];
    }
    $('.btn-load-more').attr('data-params', params).attr('data-offset', 0);
    get_cards(0, params);
    activehistoryLink.removeClass('active');
    activehistoryLink = $(this);
    $(this).addClass('active');
  }
  e.preventDefault();
});

if (slug == '/d') {
  var pathname = window.location.pathname;
  load_cards_count();
  var path = pathname.split('/');
  $('.history-link[href="'+(pathname == '/' ? "/d" : pathname)+'"]').addClass('active');
  var params = 'all';
  if (path[2]) {
    params = path[2];
  }
  $('.btn-load-more').attr('data-params', params).attr('data-offset', 0);
  get_cards(0, params);
}

/**
 * Window
 */
var Window = $('.window');

$('.btn-load-more').click(function(e) {
  e.preventDefault();
  var $btn = $(this);
  var offset = $btn.attr('data-offset');
  var params = $btn.attr('data-params');
  get_cards(offset, params);
});

/**
 * Dropdown close
 */
$('.btn-dropdown-window, .btn-close').click(function(e) {
  e.preventDefault();
  var context = $(this).data('context');
  var win = $('.dropdown-window[data-context='+context+']');
  if (win.hasClass('open')) {
    win.css('display', 'none');
    win.removeClass('open');
    if (context == 'stateChange') {
      console.log('restyle_state');
      // bring back old setting
      var btn = $('.btn-dropdown-window');
      var windowState = win;
      var state = Window.attr('data-state-id');
      var archived = Window.attr('data-archived');
      restyle_state(btn, windowState, state, archived);
    }
  } else {
    // don't need to call restyle_state()
    win.addClass('open');
    win.css('display', 'block');
  }
});

$('.btn-dropdown-window input[name=state]').remove();



/**
 * State checkboxes change
 */
$('.dropdown-window-state input[name=state]').on('change', function(e) {
  var newState = $(this).val();
  var actualState = parseInt(Window.attr('data-state-id'));
  var archiveControls = $('.archive-controls-wrapper');
  var stateComment = $('.state-comment-wrapper');
  switch(parseInt(newState)) {
    case 3:
      archiveControls.show();
      if (actualState !== 3) {
        stateComment.show();
        archiveControls.find('input[name=archive]').prop('checked', true);
      }
      break;
    default:
      archiveControls.hide();
      stateComment.hide();
      break;
  }
  console.log('radio btn zmenen');
});


/**
 * State save button clicked
 */
$('.btn-state-save').click(function(e) {
  var win = $('.dropdown-window[data-context=stateChange]');
  var state_id = parseInt(win.find('input[name=state]:checked').val());
  var archiveCheckbox = win.find('input[name=archive]');
  var archived = 0;
  var comment = '';
  if (state_id == 3) {
    if (archiveCheckbox.is(':checked')) {
      archived = 1;
    }
    var comment = win.find('.form-control-statecomment').val();
  }
  var id = Window.attr('data-id');
  var old_state = Window.attr('data-state-id');
  //console.log('btn-state-save: state_id: '+state_id+' archived:'+archived+' comment: '+comment);
  if (old_state != state_id) {
    update_card_state(id, state_id, archived, comment);
  }
  $('.dropdown-window[data-context=stateChange]').removeClass('open').hide();
  e.preventDefault();
});

function stateChange() {
  console.log('ahoj');
}

