/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

(function() {
  'use strict';

  App.CommonView__IndexView = App.CommonView.extend({

    didInsertElement: function() {
      var that = this;
      this._super();
      this.set('controller.filter.status', '');
      this.set('controller.isLoading', true);
      setTimeout(function() {
        that.get('controller').findAll();
      }, 500);
      this._setPageTitle(this.get('name').charAt(0).toUpperCase() + this.get('name').slice(1));
      App.set(this.get('name') + 'Controller', this.get('controller'));
    },

    transitionToNew: function() {
      App.Router.router.transitionTo(this.get('name') + '.new');
    },

    publishItems: function() {
      this.get('controller.content').filterProperty('isSelected', true).forEach(function(item) {
        item.set('status', '1');
        item.saveResource();
      });
    },

    unpublishItems: function() {
      this.get('controller.content').filterProperty('isSelected', true).forEach(function(item) {
        item.set('status', '0');
        item.saveResource();
      });
    },

    featureItems: function() {
      this.get('controller.content').filterProperty('isSelected', true).forEach(function(item) {
        item.set('featured', '1');
        item.saveResource();
      });
    },

    deleteItems: function() {
      var that = this;
      this.get('controller.content').filterProperty('isSelected', true).forEach(function(item) {
        item.destroyResource();
        that.get('controller').removeObject(item);
      });
    },

    checkAllItems: function() {
      this.get('controller.content').forEach(function(item) {
        item.set('isSelected', true);
      });
    },

    filterSearch: function() {
      var searchValue = jQuery.trim(jQuery('#js-filter_search').val());

      if (!searchValue) {
        this.clearSearch();
        return;
      }

      this.set('controller.filter.search', searchValue);
      this.get('controller').findAll();
    },

    clearSearch: function() {
      jQuery('#js-filter_search').val('');
      this.set('controller.filter.search', '');
      this.get('controller').findAll();
    },

    _toggleDirection: function(ordering) {
      if (this.get('controller.filter.ordering') === ordering) {
        if (this.get('controller.filter.direction') === 'ASC') {
          this.set('controller.filter.direction', 'DESC');
        } else {
          this.set('controller.filter.direction', 'ASC');
        }
      } else {
        this.set('controller.filter.direction', 'ASC');
      }

      if (!this.get('controller.filter.ordering')) { //defaults to title
        this.set('controller.filter.direction', 'DESC');
      }
    },

    _processOrdering: function(ordering) {
      this._toggleDirection(ordering);
      this.set('controller.filter.ordering', ordering);
    },

    filterByStatus: function() {
      this._processOrdering('status')
    },

    filterByTitle: function() {
      this._processOrdering('title')
    },

    goToPage: function(page) {
      this.set('controller.filter.limitstart', page.limitStart);
    }

  });
}());
