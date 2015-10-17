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
			<div class="cover Kboxcaption">
            <div class="KboxInner">
                <?php echo modZKunenaLatestHelper::getSubject($item, $params, true); ?>
                <span class="zKlatestMessage"><?php echo modZKunenaLatestHelper::getMessage($item, $params ); ?></span>
                <div class="Kboxbottom">
                    <div class="Kboxbottomleft">
                        <?php echo modZKunenaLatestHelper::getTopic($item, $params); ?>
                    </div>
                    <div class="Kboxbottomright profilelink ">
                        <?php echo modZKunenaLatestHelper::getAuthor($item, $params);?>
                    </div>
                </div><!--bottomKbox -->
            </div> <!--Inner -->
            </div><!--Kboxcaption cover  -->