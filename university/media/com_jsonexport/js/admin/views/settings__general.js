/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

(function() {
  'use strict';

  App.SettingsView__GeneralView = App.CommonView.extend({
    templateName: "settings/general",

    selectValues: {
      yesOrNo: [
        Ember.Object.create({label: 'Yes', value: '1'}),
        Ember.Object.create({label: 'No', value: '0'})
      ]
    }
  });

}());
