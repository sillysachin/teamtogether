/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

(function() {
  'use strict';

  App.TablesController = App.CommonController.extend({
    resourceType: App.Table,

    filteringObserver: function() {
      this.findAll();
    }.observes(
      'this.filter.status',
      'this.filter.ordering',
      'this.filter.direction',
      'this.filter.limitstart'
    )
  });

}());
