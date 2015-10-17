/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

(function() {
  'use strict';

  App.DeferredHelper = {

    /**
     * Check if an array has elements on the App global object if object
     * is not set.
     * If object is set, check on that object.
     */
    arrayContainsElements: function arrayContainsElements(arrayName, object) {
      var dfd = jQuery.Deferred();
      if (!object) object = App;

      var interval = setInterval(function() {
        var element = object.get(arrayName);
        if (element && element.length > 0) {
          clearInterval(interval);
          dfd.resolve();
        }
      }, 50);

      return dfd.promise();
    },

    /**
     * Check if a variable is set on the App global object if object
     * is not set.
     * If object is set, check on that object.
     */
    variableIsSet: function variableIsSet(variableName, object) {
      var dfd = jQuery.Deferred();
      if (!object) object = App;

      var interval = setInterval(function() {
        if (object.get(variableName) !== undefined) {
          clearInterval(interval);
          dfd.resolve();
        }
      }, 50);

      return dfd.promise();
    },

    /**
     * Check if the DOM contains the element specified
     */
    domElementIsPresent: function domElementIsPresent(domElement) {
      var dfd = jQuery.Deferred();

      var interval = setInterval(function() {
        if (jQuery(domElement)) {
          clearInterval(interval);
          dfd.resolve();
        }
      }, 50);

      return dfd.promise();
    }

  };

}());
