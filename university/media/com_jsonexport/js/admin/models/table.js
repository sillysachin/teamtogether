/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

(function() {
  'use strict';

  App.Table = App.CommonModel.extend({
    resourceUrl: 'index.php?option=com_jsonexport',
    resourceName: 'table',
    resourceNamePlural: 'tables',
    resourceProperties: [
      'id',
      'title',
      'description',
      'status'
    ]
  });

}());
