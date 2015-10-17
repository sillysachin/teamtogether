/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

(function() {
  'use strict';

  App.TablesView__SingleItemView = App.CommonView.extend({

    click: function(event) {

      if (event.target.className === 'icon-unpublish' || event.target.className === 'icon-publish') {
        this.set('item.status', this.get('item.status') === '1' ? "0" : "1");
        this.get('item').saveResource();
        return;
      }

      if (event.target.className === 'ember-view ember-checkbox') {
        this.set('item.isSelected', this.get('item.isSelected') === true ? true : false);
        return;
      }

    }

  });

}());


