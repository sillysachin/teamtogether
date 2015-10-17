/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

(function() {
  'use strict';

  App.CommonController = Ember.ResourceController.extend({
    filter: {},

    /**
     * Parse the JSON in the object
     */
    load: function load(json) {
      var item = this.get('resourceType').create().deserialize(json);
      item = this.preprocess(item);
      this.pushObject(item);
    },

    preprocess: function(item) {
      return item;
    },

    _prepareResourceRequest: function(params) {
      if (this.get('filter.search')) {
        params.url += '&search=' + this.get('filter.search');
      }
      if (this.get('filter.status') || this.get('filter.status') === 0) {
        params.url += '&status=' + this.get('filter.status');
      }
      if (this.get('filter.ordering')) {
        params.url += '&ordering=' + this.get('filter.ordering');
      }
      if (this.get('filter.direction')) {
        params.url += '&direction=' + this.get('filter.direction');
      }
      if (this.get('filter.limitstart')) {
        params.url += '&limitstart=' + this.get('filter.limitstart');
      }
    },

    loadAll: function(json) {
      for (var i=0; i < json.items.length; i++) {
        this.load(json.items[i]);
      }

      var pagination = json.pagination;
      var pages = [];
      var page = {};

      for (i = pagination.pagesStart; i <= pagination.pagesStop; i++) {
        page = {
          number: i,
          limitStart: (i-1) * pagination.limit,
        }

        if (pagination.pagesCurrent === i) {
          page.isCurrent = true;
        }

        pages.push(page);
      }

      this.set('pages', pages);
    },

  });

}());
