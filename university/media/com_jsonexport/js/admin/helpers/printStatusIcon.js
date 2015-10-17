/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

(function() {
  'use strict';

  /**
    Process the product published state
    @param {object} activity The activity.
  */
  Ember.Handlebars.registerBoundHelper('printStatusIcon', function(status, options) {
    var out = '';
    if (status === "1" || status === 1) {
      out = '<i class="icon-publish"></i>';
    } else {
      out = '<i class="icon-unpublish"></i>';
    }

    return new Handlebars.SafeString(out);
  });

}());
