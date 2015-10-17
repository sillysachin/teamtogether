/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

(function() {
  'use strict';

  /**
    Process the product featured state
    @param {object} activity The activity.
  */
  Ember.Handlebars.registerBoundHelper('printFeaturedIcon', function(featured, options) {
    var out = '';
    if (featured === "1" || featured === 1) {
      out = '<i class="icon-star"></i>';
    } else {
      out = '<i class="icon-unstar"></i>';
    }

    return new Handlebars.SafeString(out);
  });

}());
