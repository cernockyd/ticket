import $ from 'jquery';

export function connection_error() {
  $('.ajax-error-message').css('display', 'block');
}

var csrf_meta = $('meta[name=csrf_token]');

function watchform() {
  $('.form').on('submit', function(e) {
    console.log('submit');
    e.preventDefault();
    var csrf = csrf_meta.attr("content");
    var $form = $(this);
    var api = $form.find('.btn').data('api');
    console.log(api);
    $($form).find('.form-control-feedback').hide();
    var formData = $form.serialize();
    $.ajax({
      url: api,
      method: "POST",
      data: formData + '&csrf=' + csrf,
      dataType: "json"
    }).done(function(data) {
      console.log(data);
      var key = null;
      for (key in data) {
        if (!data.hasOwnProperty(key)) continue;
        var array = data[key];
        //console.log(data[key]);
        presentResults($form, key, data[key]);
      }
    })
    .fail(function(e) {
      console.log(e);
      connection_error();
    });

  });
}

function presentResults(form, type, data) {
  switch(type) {
    case 'redirect':
      window.location.href = data[0];
    break;
    case 'validation':
      for (var j = 0; j < data[0].length; j++) {
        presentValidation(form, data[0][j]);
      }
    break;
    case 'error':
      var ms = $(form).find('.form-error-feedback');
      ms.text(data[0]);
      ms.css('display', 'block');
    break;
    case 'success_message':
      presentSuccessMessage(form, data[0]);
    break;
    case 'csrf_token':
      csrf_meta.attr('content', data[0]);
  }
}

function presentSuccessMessage(form, message) {
  var ms = $(form).find('.form-control-feedback');
  if (form.hasClass('form-password')) {
    form.find('input').value('');
  }
  ms.text(message);
  ms.css('display', 'block');
}

function presentValidation(form, errors) {
  for (var name in errors) {
    var message = errors[name];
    if (form.hasClass('form-error-control')) {
      var ms = $(form).find('.form-control-feedback');
    } else {
      var ms = $(form).find('.form-control-feedback[name='+name+']');
    }
    ms.text(message);
   ms.css('display', 'block');
  }
}

watchform();