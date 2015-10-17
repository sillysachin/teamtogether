<?php
/**
 * @author    JoomlaShine.com
 * @copyright JoomlaShine.com
 * @link      http://joomlashine.com/
 * @package   JSN Poweradmin
 * @version   $Id: default.php 14872 2012-08-09 03:08:52Z cuongnm $
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Display config form
echo "<script src='components/com_poweradmin/assets/js/joomlashine/config.js'></script>";
$customScript = "<script>
                    var baseUrl       = '".JURI::root()."';
                    var pop ='';

                    (function ($){
                        $(document).ready(function  (){
                            $('#logo-select').click(function (){
                                pop = $.JSNUIWindow
                                (
                                    baseUrl + 'administrator/index.php?option=com_media&view=images&tmpl=component&asset=com_poweradmin&author=&fieldid=logo_file',
                                    {
                                        modal  : true,
                                        width  : 800,
                                        height : 550,
                                        title : '".JText::_('JSN_POWERADMIN_CONFIG_SELECT_LOGO_FILE')."',
                                        buttons: {
                                            'Close': function(){
                                                $(this).dialog('close');
                                            }
                                        }
                                    }
                                );
                            });
                        });

                    })(JoomlaShine.jQuery);
                    function jInsertFieldValue(value, id) {
                        var old_id = document.getElementById(id).value;
                        if (old_id != id) {
                            var elem = document.getElementById(id);
                            elem.value = value;
                            elem.fireEvent('change');
                        }
                        pop.close();
                        var trackButtons = JoomlaShine.jQuery('.form-actions button[track-change=\"yes\"]');
                        trackButtons.removeAttr('disabled');
                    }
                </script>
                ";
//echo $customScript;
JSNConfigHelper::render($this->config);
$products	=	JSNPaExtensionsHelper::getDependentExtensions();
// Display footer
JSNHtmlGenerate::footer($products);
