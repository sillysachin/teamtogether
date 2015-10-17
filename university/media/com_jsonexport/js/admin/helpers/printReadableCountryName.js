/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

(function() {
  'use strict';

  /**
    Given a country code, output a readable name
    @param {object} activity The activity.
  */
  Handlebars.registerHelper('printReadableCountryName', function(country, options) {
    var countryCode = options.data.view.content;
    var out = App.getCountryWithCode(countryCode);
    return new Handlebars.SafeString(out);
  });

}());


