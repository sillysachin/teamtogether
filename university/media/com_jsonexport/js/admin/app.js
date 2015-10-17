/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

var App = Ember.Application.create({
  rootElement: '#ember',
  LOG_TRANSITIONS: true
});

App.reopen({

  ready: function ready() {
    this.loadSettings();

    setInterval(function() { //prevent timeout
      jQuery.get('index.php');
    }, 4000);
    jQuery('body>.subhead-collapse').remove();
  },

  showSuccessMessage: function(message) {
    App.set('message', {
      isSuccess: true,
      title: message.title,
      text: message.text
    });

    setTimeout(function() {
      jQuery('#message-container').slideUp('slow');
    }, 2000);

    setTimeout(function() {
      App.set('message', null);
    }, 3000);
  },

  showErrorMessage: function(message) {
    App.set('message', {
      isError: true,
      title: message.title,
      text: message.text
    });

    setTimeout(function() {
      jQuery('#message-container').slideUp('slow');
    }, 2000);

    setTimeout(function() {
      App.set('message', null);
    }, 3000);
  },

  loadSettings: function() {
    jQuery.getJSON('index.php?option=com_jsonexport&view=settings&format=json')
    .success(function(response) {

      function isEmpty(obj) {
        return Object.keys(obj).length === 0;
      }

      var i = 0, item;

      if (isEmpty(response) || response == "") {
        App.restoreFactorySettings();
      } else {
        App.set('settings', JSON.parse(response.settings));
      }

    });
  },

  restoreFactorySettings: function() {
    var that = this;

    var settings = {
      general: {
        yesOrNo: '0',
        requiredUsername: '',
        requiredPassword: ''
      }
    };

    jQuery.ajax({
      url: 'index.php?option=com_jsonexport&task=settings.save',
      data: {
        settings: JSON.stringify(settings)
      }
    })
    .success(function() {
      that.loadSettings();
    })
  },

  saveSettings: function() {
    var settings = App.get('settings');
    var i = 0;

    jQuery.ajax({
      url: 'index.php?option=com_jsonexport&task=settings.save',
      data: {
        settings: JSON.stringify(settings)
      }
    })
    .success(function() {
      console.log('success');
    })
    .error(function() {
      console.log('error');
    })
  }

});
