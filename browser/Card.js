import $ from 'jquery';
import {connection_error} from './Forms';
import {get_meta_data, get_state_text, restyle_state} from './Lists';
import moment from 'moment';
import 'moment/locale/cs';
// Functions inside card interface

export function get_cards_count(params, render_function, element) {
  $.ajax({
    url: '/api/get_cards_count/'+params,
    type: 'GET',
    dataType: 'json',
  })
  .done(function(count) {
    render_function(count, element);
  })
  .fail(function() {
    connection_error();
  });
}

export function load_cards_count() {
  $('.history-link-count').each(function(i) {
    var href = $(this).attr('href');
    var path = href.split('/');
    var params = 'all';
    if (path[2]) {
      params = path[2];
    }
    get_cards_count(params, render_cards_count, $(this));
  });
}

function render_cards_count(count, element) {
  element.find('.badge').remove();
  var $badge = $('<span class="badge ml-1 badge-pill badge-default" style="display: none;">'+count+'</span>');
  $badge.appendTo(element).fadeIn('fast');
}

export function add_activity(card_id, message = null, forceEmail, callback, cardStateId = null) {
  var type = '';
  if (cardStateId === null) {
    type = 'comment';
  } else {
    type = 'state';
  }
  $.ajax({
    url: '/api/create_activity',
    type: 'POST',
    data: 'card_id='+card_id+'&message='+message+'&force_email='+forceEmail+'&type='+type+'&card_state_id='+cardStateId,
    dataType: 'json',
  })
  .done(function(activity) {
    var wrapper = $('.window-module-activities');
    var list = wrapper.find('.list-activities');
    var user = [];
    user[activity.id] = [activity.user_id];
    render_activity(activity, list, true);
    render_activities_users(user);
  })
  .fail(function() {
    connection_error();
  });
}

export function get_activities(card_id, offset) {
  $.ajax({
    url: '/api/get_card_activities/'+card_id+'/'+offset,
    type: 'GET',
    dataType: 'json',
  })
  .done(function(data) {
    process_activities(data);
  })
  .fail(function() {
    connection_error();
  });

}

export function process_activities(activities) {
  var wrapper = $('.window-module-activities');
  var spinner = wrapper.find('.spinner-activities');
  var list = wrapper.find('.list-activities');
  var btn =  wrapper.find('.btn-activities-more');
  var users = [];
  var limit = 10;
  var newoffset = 0;
  var $length = activities.length;
  if ($length < limit) {
    btn.css('display', 'none');
  } else {
    var curr_offset = btn.attr('data-offset');
    if (typeof curr_offset !== 'undefined') {
      newoffset = parseInt(curr_offset) + $length;
    } else {
      newoffset = $length;
    }
    btn.attr('data-offset', newoffset).css('display', 'inline-block');
  }
  for (var i = 0; i < $length; i++) {
    render_activity(activities[i], list, false);
    users[activities[i].id] = [activities[i].user_id];
  }
  if (typeof $length != 'undefined') {
    spinner.css('display', 'none');
  }
  if (typeof users != 'undefined') {
    render_activities_users(users);
  }
}

export function render_activities_users(users) {
  get_meta_data('get_users', users, $('.store-users'), render_activities_users_data);
}

export function render_activities_users_data(apiRoute, array, store) {
  var wrapper = $('.window-module-activities');
  var list = wrapper.find('.list-activities');
  array.forEach(function(key, i) {
    var activity_id = i;
    key.forEach(function(num) {
      var user = store.filter(function (user) {
          return user.id === num.toString();
      })[0];
      if (typeof user !== 'undefined') {
        var activity = list.find('.item-activity[data-id="'+activity_id+'"]');
        activity.find('.data-author').text(user.name);
        activity.find('.activity-icon').attr('src', 'https://www.gravatar.com/avatar/'+user.hash+'?s=32&d=identicon');
      }
    });
  });
}

export function render_activity(activity, list, isnew) {
  //console.log(activity);
  var date = moment(activity.date);
  var content = '';
  var stateText = '';
  var badgeClass = '';

  switch(activity.card_state_id) {
    case 1:
      stateText = 'Přečteno';
      badgeClass = 'badge-default';
      break;
    case 2:
      stateText = 'Označeno jako v řešení';
      badgeClass = 'badge-info';
      break;
    case 3:
      stateText = 'Vyřešeno';
      badgeClass = 'badge-success';
      break;
  }

  switch(activity.type_id) {
    case 3:
      content = '<div class="activity-author data-author"></div><div class="activity-message">'+activity.message+'</div>';
      break;
    case 1:
    case 2:
      var messagePart = '';
      if (typeof activity.message !== 'undefined' && activity.message !== '' && activity.message) {
        var messagePart = '<div class="activity-message mt-1">'+activity.message+'</div>';
      }
      content = '<div class="activity-text"><span class="badge badge-pill '+badgeClass+'">'+stateText+'</span> uživatelem <b class="data-author"></b></div>'+messagePart;
      break;
    case 0:
      $('.window-module-activities .btn-load-more').css('display', 'none');
      content = '<div class="activity-text">Vytvořeno uživatelem <b class="data-author"></b></div>';
      break;
  }
  var $html = $('<li class="item-activity item-normal" data-id="'+activity.id+'"><img class="activity-icon rounded-circle" src="" alt=""><div class="activity-con">'+content+'<div class="activity-description">'+date.locale('cs').format('LLL')+'</div></div></li>');
  if (!isnew) {
    $html.appendTo(list);
  } else {
    $html.prependTo(list);
  }
}

export function update_card(id, name, value) {
  $.ajax({
    url: '/api/update_card',
    type: 'POST',
    // data: formData + '&csrf=' + csrf,
    data: 'id='+id+'&col='+name+'&value=' + value,
    dataType: 'json',
  }).done(function(data) {
    console.log(data);
    if (data == 'false') {
      connection_error();
    }
  }).fail(function() {
    connection_error();
  });
}

export function update_card_state(id, state_id, archived, comment = '') {
  console.log('update_card_state: id: '+id+' state_id: '+state_id+' archived: '+archived+' message: '+comment);
  $.ajax({
    url: '/api/create_activity',
    type: 'POST',
    // data: formData + '&csrf=' + csrf,
    data: 'card_id='+id+'&type=state&card_state_id='+state_id+'&archived='+archived+'&message='+comment,
    dataType: 'json',
  }).done(function(data) {
    console.log(data);

    if (!data) {
      return false;
    }

    var wrapper = $('.window-module-activities');
    var list = wrapper.find('.list-activities');
    var user = [];
    user[data.id] = [data.user_id];
    render_activity(data, list, true);
    render_activities_users(user);

    $('.window').attr('data-state-id', state_id);

    var windowState = $('.dropdown-window[data-context=stateChange]');
    var btn = $('.btn-dropdown-window[data-context=stateChange]')
    restyle_state(btn, windowState, state_id, archived)

    var state_text = get_state_text(state_id);
    var listCards = $(".list-cards");
    var card = listCards.find('.item-card[data-id="'+id+'"]');

    card.find('.card-icon').removeClass('icon-state-0 icon-state-1 icon-state-2 icon-state-3').addClass('icon-state-'+state_id);
    card.find('.item-labels .badge-state').removeClass('icon-state-0 icon-state-1 icon-state-2 icon-state-3').addClass('icon-state-'+state_id).text(state_text);


  }).fail(function() {
    connection_error();
  });
}

export function archive_card(id) {
  $.ajax({
    url: '/api/archive_card',
    type: 'POST',
    // data: formData + '&csrf=' + csrf,
    data: 'id='+id+'&archived=1',
    dataType: 'json',
  }).done(function(data) {
    if (data != 'false') {
      $('.list-cards').find('.item-card[data-id="'+id+'"]').remove();
      load_cards_count();
    }
  }).fail(function() {
    connection_error();
  });
}

export function delete_card(id) {
  $.ajax({
    url: '/api/delete_card',
    type: 'POST',
    // data: formData + '&csrf=' + csrf,
    data: 'id='+id,
    dataType: 'json',
  }).done(function(data) {
    if (data != 'false') {
      $('.list-cards').find('.item-card[data-id="'+id+'"]').remove();
    }
  }).fail(function() {
  });
}