/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

(function() {
  'use strict';

  App.SettingsView = App.CommonView.extend({
    name: 'settings',

    didInsertElement: function() {
      this._super();
      this._setPageTitle(this.get('name').charAt(0).toUpperCase() + this.get('name').slice(1));
    },

    save: function() {
      App.saveSettings();
    },

    rollbackChanges: function() {
      App.loadSettings();
    }

  });
}());
