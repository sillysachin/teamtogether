/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

(function() {
  'use strict';

  App.CommonView__FormView = App.CommonView.extend({
    photos: [],

    didInsertElement: function() {
      this._super();
      this._setupDescriptionEditor();
      this.set('photos', []);
    },

    transitionToList: function() {
      App.Router.router.transitionTo(this.pluralName);
    },

    transitionToNew: function() {
      App.Router.router.transitionTo(this.pluralName + '.new');
    },

    save: function() {
      this._save();
    },

    saveAndClose: function() {
      if (this._save()) {
        this.transitionToList();
      }
    },

    saveAndNew: function() {
      if (this._save()) {

        this.set('controller.content', this.model.create({
          status: "1",
          access: "0",
          featured: "0",
          language: "0"
        }));

        jQuery('#js-item-form-description').setCode('');
        this.get('photos').clear();

        this.transitionToNew();
      }
    },

    _save: function() {
      var that = this;
      var item_id, i = 0;

      while (i < this.fieldsRequired.length) {
        /** validation **/
        var field = this.get('controller.content.' + this.fieldsRequired[i]);

        if (this.fieldsRequired[i] === 'category' && !this.get('controller.content.category')) field = this.get('controller.content.categoryId');

        if (!field) {
          alert(this.fieldsRequired[i] + ' is required');
          return;
        }

        i++;
      }

      /** editor special case **/
      this.set('controller.content.description', jQuery('#js-item-form-description').getCode());

      this.get('controller.content').saveResource(function(item_id) {
        App.showSuccessMessage({
          title: 'Success!',
          text: 'Item saved'
        });
      });

      return true;
    },

    _setupDescriptionEditor: function _setupDescriptionEditor() {
      jQuery('#js-item-form-description').redactor({ minHeight: 200 }); //initialize editor
      if (this.get('controller.content.description')) {
        jQuery('#js-item-form-description').setCode(this.get('controller.content.description'));
      }
    },

    removePhoto: function removePhoto(photo) {
      if (photo.isDefault) {
        alert('Cannot delete default photo');
        return;
      }
      this.get('photos').removeObject(photo);
      photo.destroyResource();
    },

    removeFile: function removeFile(file) {
      this.get('files').removeObject(file);
      file.destroyResource();
    },

    setPhotoAsDefault: function(photo) {
      this.set('controller.content.defaultPhoto', photo.name);
      this.photos.forEach(function(aPhoto) {
        if (aPhoto.name === photo.name) {
          aPhoto.set('isDefault', true);
        } else {
          aPhoto.set('isDefault', false);
        }
      })

      if (this.get('controller.content.id')) { //existing item
        this._save();
      }
    },

    _setupImages: function() {
      var that = this;
      var random = Math.floor(Math.random()*5000);
      this.set('controller.content.random', random);

      if (that.get('controller.content.id')) {
        //TODO: recupero foto da AJAX call e metto in photos per mostrarle
      }

      function removePhoto(photo, i){
        photos = removeItems(photos, photo);

        if (that.get('controller.content.id')) {
          var set = {
            action:'deleteimage',
            target_type: '<?=$name?>_image',
            target_id: that.get('controller.content.id'),
            name: photo
          }
        } else {
          var set = {
            action:'deleteimage',
            target_type: 'temp_<?=$name?>_image',
            target_id: random,
            name: photo
          }
        }

        jQuery("#edit-form").ajaxForm({
          url: 'index.php?option=com_ohanah&view=attachment',
          data: set
        }).submit();
      }

      function createPhotoToolbars(){
        //PHOTO TOOLBARS
        jQuery('.photoOver').mouseenter(function(index) {
          jQuery(this).find('.photoButtons').css('opacity', '1');
          jQuery(this).find('.photoButtons').css('top', jQuery(this).find('.photo').width()/-2+12+'px');
          jQuery(this).find('.photoButtons').css('left', jQuery(this).find('.photo').height()/2-28+'px');
        });
        jQuery('.photoOver').mouseleave(function(index) {
          jQuery(this).find('.photoButtons').css('opacity', '0');
        });
      }

      function removeItems(array, item) {
        var i = 0;
        while (i < array.length) {
          if (array[i] == item) {
            array.splice(i, 1);
          } else {
            i++;
          }
        }
        return array;
      }

      jQuery('#image-upload-input').live('change', function() {
        var set = {};

        if (that.get('controller.content.id')) {
          set.target_type = that.singularName + '_image';
          set.target_id = that.get('controller.content.id');
        } else {
          set.target_type = 'temp_' + that.singularName + '_image';
          set.target_id = random;
        }

        jQuery("#image-upload").ajaxForm({
          url: 'index.php?option=com_jsonexport&task=attachment.save',
          data: {
            target_type: set.target_type,
            target_id: set.target_id
          },
          success: function processPhoto(attachment)  {
            if (typeof attachment === 'string') {
              attachment = JSON.parse(attachment);
            }
            that.photos.pushObject(App.Attachment.create({
              id: attachment.id,
              target_type: attachment.target_type,
              target_id: attachment.target_id,
              name: attachment.name
            }));
          }
        }).submit();
      });

      jQuery('#file-upload-input').live('change', function() {
        var set = {};

        if (that.get('controller.content.id')) {
          set.target_type = that.singularName + '_file';
          set.target_id = that.get('controller.content.id');
        } else {
          set.target_type = 'temp_' + that.singularName + '_file';
          set.target_id = random;
        }

        jQuery("#file-upload").ajaxForm({
          url: 'index.php?option=com_jsonexport&task=attachment.save',
          data: {
            target_type: set.target_type,
            target_id: set.target_id
          },
          success: function processFile(attachment)  {
            if (typeof attachment === 'string') {
              attachment = JSON.parse(attachment);
            }
            that.files.pushObject(App.Attachment.create({
              id: attachment.id,
              target_type: attachment.target_type,
              target_id: attachment.target_id,
              name: attachment.name
            }));
          }
        }).submit();
      });

      jQuery('.photoOver').mouseenter(function(index) {
        jQuery(this).find('.photoButtons').css('opacity', '1');
        jQuery(this).find('.photoButtons').css('top', jQuery(this).find('.photo').width()/-2+12+'px');
        jQuery(this).find('.photoButtons').css('left', jQuery(this).find('.photo').height()/2-28+'px');
      });

      jQuery('.photoOver').mouseleave(function(index) {
        jQuery(this).find('.photoButtons').css('opacity', '0');
      });
    }

  });
}());
