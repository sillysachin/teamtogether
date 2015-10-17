/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

define([
	'jquery',
	'jsn/core'
], 
function ($) {
	return function (params) {
		$(function () {
			$('#jsn-button-refresh')
				.unbind('linkBeforeRequest')
				.bind('linkBeforeRequest', function () {
					var form = $(this).closest('form'),
						url = 'index.php?option=' + params.option + '&view=configuration&s=configuration&g=msgs&msg_screen=' + form.find('#msg_screen').val();

					$(this).attr('href', url);
				});

			$('#msg_screen').change(function () {
				$('#jsn-button-refresh').trigger('click');
			});

			$('a.jsn-close-message[data-message-id]').on('click', function () {
				$(this).parent().hide();
				$.ajax({ url: 'index.php?option=' + params.option + '&view=configuration&tmpl=component&task=hideMsg&msgId=' + $(this).attr('data-message-id') });
			});
		});
	};
});