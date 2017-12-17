import $ from 'jquery';
import {connection_error} from './Forms';
import moment from 'moment';
import {get_meta_data} from './Lists';



export default function Notifications() {

    function pull_notifications() {
      var navNotif = $('.nav-item-notification');
      var listWrapper = navNotif.find('.list-notifications');
      var notifId = parseInt(listWrapper.attr('data-id-count'));
      //console.log('notifID on pull: '+notifId);
      var btnPull = navNotif.find('.btn-notif-pull');
      var numCards = parseInt(btnPull.attr('data-cards'));
      var numActivities = parseInt(btnPull.attr('data-activities'));
      var newCards = 0;
      var newActivities = 0;
      $.ajax({
        type: "GET",
        url: '/api/get_notifications_for_user/'+numCards+'/'+numActivities,
        dataType: 'json',
        error: function(jqXHR, textStatus, errorThrown) {
          //console.log(textStatus, errorThrown);
        }
      })
      .done(function(data) {
        if (data === false) {
          btnPull.hide();
          return;
        }
        var length = data.length;
        var users = [];
        var active = false;
        for (var i = 0; i < length; i++) {
          notifId++;
          //console.log(notifId);
          if (data[i].type == 'card') {
            newCards++;
          }
          var user = data[i];
          users[notifId] = [data[i].user_id];
          if (data[i].seen === 0) {
            active = true;
          }
          render_notification(data[i], navNotif, notifId, listWrapper);
        }
        listWrapper.attr('data-id-count', notifId);
        //console.log('notifID after pull: '+notifId);
        newActivities = length - newCards;
        numActivities += newActivities;
        numCards += newCards;
        btnPull.attr('data-cards', numCards);
        btnPull.attr('data-activities', numActivities);
        render_notifications_users(users);
        //console.log('users:', users);
        var notifLink = navNotif.find('.nav-link-notification');
        if (active) {
          notifLink.addClass('notification-active');
        } else {
          notifLink.removeClass('notification-active');
        }
      });
    }

    $('.btn-notif-pull').click(function(e) {
      var $this = $(this);
      pull_notifications();
      return false;
    });

    pull_notifications(0, 0);

    /**
     * Notification
     * @param int notification_type {1:'normal', 2:'modal'}
     * @param int action            [description]
     */
    function render_notification(data, navNotif, i, wrapper) {
      var desc = '';
      switch (data.type) {
        case 'card':
          desc = 'vytvořil';
          break;
        case 'solve':
          desc = 'vyřešil';
          break;
        case 'comment':
          desc = 'okomentoval';
          break;
        case 'state':
          desc = 'změnil stav u';
          break;
      }
      var $html = $('<li class="nav-item"><a class="nav-link item-notif" data-id="'+i+'"><img class="rounded-circle n-picture notif-icon" src="/c/'+data.id+'" alt=""><p class="n-text-side"><b class="notif-user"></b> '+desc+' <b>'+data.name+'</b></p></a></li>');
      $html.appendTo(wrapper);
    }


    function delete_notification(id) {
      $.ajax({
        url: '/api/delete_notification',
        type: 'POST',
        data: 'id='+id,
        dataType: 'json',
        error: function(jqXHR, textStatus, errorThrown) {
          //console.log(jqXHR, textStatus, errorThrown);
        }
      });
    };

    function render_notifications_users(users) {
      get_meta_data('get_users', users, $('.store-users'), render_notifications_users_data);
    }

    function render_notifications_users_data(apiRoute, array, store) {
      var list = $('.list-notifications');
      var users_count = parseInt(list.attr('data-users-count'));

      array.forEach(function(key, i) {
        users_count++;
        key.forEach(function(num) {
          var user = store.filter(function (user) {
              return user.id === num.toString();
          })[0];
          if (typeof user !== 'undefined') {
            var notif = list.find('.item-notif[data-id="'+users_count+'"]');
            notif.find('.notif-user').text(user.name);
            notif.find('.notif-icon').attr('src', 'https://www.gravatar.com/avatar/'+user.hash+'?s=32&d=identicon');
          }
        });
      });

      list.attr('data-users-count', users_count);
    }

};