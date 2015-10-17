/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

(function() {
  'use strict';

  App.CommonModel = Ember.Resource.extend({

    saveResource: function(callback) {
      var set = {}, property, i = 0;

      while (i < this.resourceProperties.length) {
        property = this.resourceProperties[i];
        set[property] = this.get(property);
        i++;
      }

      jQuery.ajax({
        method: 'POST',
        url: this.resourceUrl + '&task=' + this.resourceName + '.save',
        data: {
          item: set
        }
      }).success(function(item_id) {
        if (callback) {
          callback(item_id);
        }
      });
    }
  });

}());
