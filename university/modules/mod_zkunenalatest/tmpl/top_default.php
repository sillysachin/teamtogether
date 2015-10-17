<?php
/**
 * @version		0.40
 * @package		zKunenaLatest
 * @author		Aaron Gilbert {@link http://www.nzambi.braineater.ca}
 * @author		Created on 02-Feb-2012
 * @copyright	Copyright (C) 2009 - 2012 Aaron Gilbert. All rights reserved.
 * @license		GNU/GPL, see http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>			
            <div class="topcover topKboxcaption">
				<?php if($params->get('showAvatar', 1) == 1 ) :?>
                    <div class="zKlatestAvatar_left">
                        <?php echo modZKunenaLatestHelper::getAvatar( $item->userid, $params ); ?>
                    </div>
                <?php elseif($params->get('showAvatar') == 2) :?>
                    <div class="zKlatestAvatar_left">
                        <?php echo modZKunenaLatestHelper::getTopicIcon( $item, $params ); ?>
                    </div>
                <?php endif ;?>
            	<?php echo CKunenaLink::GetProfileLink ($item->userid, $item->name);?>
            </div>