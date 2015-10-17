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
  Ember.Handlebars.registerBoundHelper('calculateOrderTotal', function(products, options) {
    var out = 0, i = 0;

    while (i < products.length) {
      out += products[i].quantity * products[i].product.price;
      i++;
    }

    return new Handlebars.SafeString(out);
  });

}());
