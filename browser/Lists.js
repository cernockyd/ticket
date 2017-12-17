import $ from 'jquery';
import autosize from 'autosize';
import moment from 'moment';
import 'moment/locale/cs';
import {
  update_card,
  archive_card,
  delete_card,
  add_activity,
  get_activities,
  update_card_state
} from './Card';
import {connection_error} from './Forms';

export function render_card(card) {
  var special = '';
  var moment_date = moment(card.date);
  var state_text = get_state_text(card.state_id);
  var name = card.name;
  if (name.length > 50) name = name.substring(0, 50) + '...';
  // console.log(card.id);
  var $html = $('<li data-id="'+card.id+'" class="item-card"><p class="col-md-5 col-card item-name"><span class="card-icon icon-state-'+card.state_id+'">'+card.old+'</span><a class="item-link item-link-wrap" href="/">'+name+'</a></p><p class="item-fromnow col-md-2 col-card pr-0">'+moment_date.locale('cs').fromNow()+'</p><p class="col-md-5 col-card item-labels"><span class="badge badge-state ml-3 badge-pill icon-state-'+card.state_id+'">'+state_text+'</span></p></li>');
  $html.appendTo(".list-cards");
}

export function get_state_text(state) {
  var state_text = 'Nepřečteno';
  switch(parseInt(state)) {
    case 1:
    state_text = 'Přečteno'; break;
    case 2:
    state_text = 'V řešení'; break;
    case 3:
    state_text = 'Vyřešeno'; break;
  }
  return state_text;
}

export function render_taxonomies(cards_taxonomies) {
  get_meta_data('get_categories', cards_taxonomies, $('.store-taxonomies'), render_list_meta_data);
}

export function render_users(users) {
  get_meta_data('get_users', users, $('.store-users'), render_list_meta_data);
}

export function get_meta_data(apiRoute, array, storeElement, callback) {
  if (apiRoute != '.') {
  var storeVal = storeElement.val();
  var store = JSON.parse(storeVal);
  var arr = [];

  array.forEach(function(key, i) {
    for (var n = 0; n < key.length; n++) {
      var id = parseInt(key[n]);
      var obj = store.filter(function (obj) {
        return obj.id === id.toString();
      })[0];
      if (typeof obj === 'undefined') {
        if ($.inArray(key[n], arr) == -1) {
          arr.push(key[n]);
        }
      }
    }
  });

  var params = arr.join('&');
  if (params != '') {
    $.ajax({
      url: '/api/'+apiRoute+'/'+params,
      type: 'GET',
      dataType: 'json',
    })
    .done(function(data) {
      for (var i = 0; i < data.length; i++) {
        store.push(data[i]);
      }
      storeElement.val(JSON.stringify(store));
      callback(apiRoute, array, store);
    })
    .fail(function() {
      connection_error();
    });
  } else {
    callback(apiRoute, array, store);
  }

  }
}

export function render_list_meta_data(apiRoute, array, store) {
  var listCards = $('.list-cards');
  //console.log(array);
  //console.log(store);
  array.forEach(function(key, i) {
    var card_id = i;
    var number = 1;
    key.forEach(function(num) {
      var obj = store.filter(function (obj) {
          return obj.id === num.toString();
      })[0];
      if (typeof obj !== 'undefined') {
        var card_labels = listCards.find('.item-card[data-id="'+card_id+'"]');
        card_labels.find('.item-labels').prepend('<span class="badge badge-pill badge-card mr-2" data-object-id="'+obj.id+'">'+obj.name+'</span>');
      }
    });
  });
}

export function get_obj_from_store(store_name, id) {
  //
}

export function get_cards(offset, params) {
  // console.log('/api/get_cards/'+offset+'/'+params);
  var loadMoreBtn = $('.btn-load-more');
  $.ajax({
    url: '/api/get_cards/'+offset+'/'+params,
    type: 'GET',
    dataType: 'json',
  })
  .done(function(data) {
    //console.log(data);
    if (data != false) {
      var cards_taxonomies = [];
      var users = [];
      var $length = data.length;
      var newoffset = parseInt(offset)+$length;
      $('.spinner-list').remove();
      for (var i = 0; i <= $length - 1; i++) {
        var card = data[i];
        render_card(card);
        users[card.id] = [card.user_id];
        cards_taxonomies[card.id] = card.taxonomies;
      }
      if (cards_taxonomies.length) {
        render_taxonomies(cards_taxonomies);
      }
      if (users.length) {
        render_users(users);
      }
      if ($length < 16) {
        loadMoreBtn.css('display', 'none');
      } else {
        loadMoreBtn.css('display', 'inline-block');
      }
      loadMoreBtn.attr('data-params', params).attr('data-offset', newoffset);
    } else {
      loadMoreBtn.css('display', 'none');
    }
  })
  .fail(function() {
  });
}

export function restyle_state(btn, windowState, state, archived) {
  var radioWrapper = windowState.find('.state-controls-wrapper');
  var archiveWrapper = windowState.find('.archive-controls-wrapper');
  var commentWrapper = windowState.find('.state-comment-wrapper');
  var radio = radioWrapper.find('input[name=state][value='+state+']');
  var comment = commentWrapper.find('textarea');
  radio.prop('checked', true);

  var checkboxArchive = archiveWrapper.find('input[name=archive]');
  var controlDelete = archiveWrapper.find('.control-delete');
  if (archived == 0) {
    checkboxArchive.prop('checked', false);
    controlDelete.hide();
  } else {
    checkboxArchive.prop('checked', true);
    controlDelete.show();
  }

  switch (parseInt(state)) {
    case 1:
      btn.addClass('btn-secondary');
      btn.removeClass('btn-info btn-success');
      btn.find('.span-btn').text('Přečteno');
      commentWrapper.hide();
      archiveWrapper.hide();
      break;
    case 2:
      btn.addClass('btn-info');
      btn.removeClass('btn-secondary btn-success');
      btn.find('.span-btn').text('V řešení');
      commentWrapper.hide();
      archiveWrapper.hide();
      break;
    case 3:
      btn.addClass('btn-success');
      btn.removeClass('btn-info btn-secondary');
      btn.find('.span-btn').text('Vyřešeno');
      archiveWrapper.show();
      commentWrapper.hide();
      break;
  }
  // Set comment
  comment.val('').text();
}

export function close_card() {
  var cardWindowOverlay = $('.window-overlay');
  var cardWindow = cardWindowOverlay.find('.window');
  cardWindowOverlay.css('display', 'none');
  cardWindow.find('.window-title .form-control').remove();
  cardWindow.find('.window-description .form-control').remove();
  cardWindow.find('.window-description .btn').css('display', 'none');
  cardWindow.find('.list-activities .item-activity').remove();
  cardWindow.find('.btn-activities-more').remove();
}

export function open_card(id, cardWindow, cardWindowOverlay, listCards) {
  $.ajax({
    url: '/api/get_card/'+id,
    type: 'GET',
    dataType: 'json',
  })
  .done(function(data) {
    if (data != false) {
      var selectState = cardWindow.find('.btn-state');
      var stateRadioWrapper = cardWindow.find('.dropdown-window-state');
      var archiveRadioWrapper = cardWindow.find('.archive-controls-wrapper');
      var newState;
      cardWindow.attr('data-id', id);
      cardWindow.attr('data-state-id', data.state_id);
      cardWindow.attr('data-archived', data.archived);
      cardWindow.find('.window-title').prepend('<textarea class="form-control window-module-headline autogrow" placeholder="Předmět" rows="1">'+data.name+'</textarea>');
      cardWindow.find('.window-description').prepend('<textarea class="form-control autogrow" rows="1" placeholder="Popis">'+data.description+'</textarea>');
      $('.window-text .form-control').each(function(i, el) {
        var div = $("#content").append('<div class="heightdiv-'+i+' descriptionheight">'+$(el).val().replace(/\r?\n/g,'<br/>')+'</div>');
        var heightdiv = $('.heightdiv-'+i);
        var h = heightdiv.height();
        heightdiv.remove();
        $(el).css('height', h+6+'px');
      });
      if (data.state_id != '0') {
        newState = data.state_id;
      } else {
        newState = 1;
      }
      cardWindow.attr('data-state-id', newState);
      restyle_state(selectState, stateRadioWrapper, newState, data.archived);
      autosize(cardWindow.find('.autogrow'));

      var description = $('.window-description');
      var descriptionTextarea = description.find('.form-control');
      description.find('.btn').click(function(e) {e.preventDefault()});

      //description
      descriptionTextarea.click(function(e) {
        console.log('click');
        var text = $(this).val();
        $(this).attr('data-text', text);
        description.find('.btn').css('display', 'inline-block');
      });

      descriptionTextarea.focusout(function(e) {
        var text = $(this).val();
        if ($(this).attr('data-text') != text) {
          $(this).addClass('focused');
        } else {
          $(this).removeClass('focused');
          description.find('.btn').css('display', 'none');
        }
      });

      // save description
      description.find('.btn-save').click(function(e) {
        var text = descriptionTextarea.val();
        var id = cardWindow.attr('data-id');
        update_card(id, 'description', text);
        descriptionTextarea.attr('data-text', text);
        description.find('.btn').css('display', 'none');
        descriptionTextarea.removeClass('focused');
      });

      // exit description
      description.find('.btn-exit').click(function(e) {
        var oldtext = descriptionTextarea.attr('data-text');
        descriptionTextarea.val(oldtext);
        descriptionTextarea.removeClass('focused');
        description.find('.btn').css('display', 'none');
      });

      // title
      var title = $('.window-title');
      var titleTextarea = title.find('.form-control');
      titleTextarea.bind('change', function() {
        console.log('click');
        var id = cardWindow.attr('data-id');
        var text = $(this).val();
        var name = text;
        if (name.length > 38) name = name.substring(0,38) + '...';
        listCards.find('.item-card[data-id="'+id+'"] .item-link').html(name);
        update_card(id, 'name', text);
      });
      cardWindowOverlay.css('display', 'block');

      // comment
      var commentModule = $('.window-module-newcoment');
      var messageTextarea = commentModule.find('.form-control-newcomment');
      var forceEmailCheckbox = commentModule.find('input[name="notifications-force"]');
      commentModule.find('.btn-comment').click(function(e) {
        e.preventDefault();
        var forceEmail = 0;
        if (forceEmailCheckbox.is(':checked')) {
          forceEmail = 1;
        }
        if (messageTextarea.val() != '') {
          add_activity(id, messageTextarea.val(), forceEmail, messageTextarea.val(''));
        }
        return false;
      });
      if (data.state_id == '0') {
        update_card_state(id, newState, 0);
      }
      var $moreActivitiesBtn = $('<a href="#" class="btn btn-lg btn-activities-more btn-secondary" style="display: none;">Načíst další</a>');
      $moreActivitiesBtn.appendTo('.module-activities-more');
      cardWindow.find('.window-module-activities .btn-activities-more').click(function(e) {
        var offset = $(this).attr('data-offset');
        console.log(offset);
        get_activities(id, offset);
        e.preventDefault();
        return false;
      });
      get_activities(id, 0);
    }
  })
  .fail(function() {
    connection_error();
  });
}
