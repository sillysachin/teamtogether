<?php
/**
 * @version		0.40
 * @package		zKunenaLatest
 * @author		Aaron Gilbert {@link http://www.nzambi.braineater.ca}
 * @author		Created on 08-Dec-2010
 * @copyright	Copyright (C) 2009 - 2012 Aaron Gilbert. All rights reserved.
 * @license		GNU/GPL, see http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::stylesheet('zk_latest.css','modules/mod_zkunenalatest/assets/'); ?>
<?php JHtml::_('stylesheet', 'modules/mod_zkunenalatest/assets/zk_latest.css', array(), false);?>
<div id="zKlatest">
<?php if ($params->get('addHeader', 0)) : ?>
	<div id="zKlatestHeader"><?php echo $params->get('headerText', '') ?></div>
<?php endif; ?>
<div id="Kboxwrap<?php echo $module->id ?>" class="Kboxwrap kwrap" >
<?php foreach($items as $item) : ?>
	<div class="Kboxgridwrap" >
        <div class="Kboxgrid captionfull<?php echo $module->id ?>">
        <?php if($params->get('showAvatar', 1) == 1 ) :?>
        	<div class="zKlatestAvatar_left">
        		<?php echo modZKunenaLatestHelper::getAvatar( $item , $params ); ?>
            </div>
        <?php elseif($params->get('showAvatar') == 2) :?>
        	<div class="zKlatestAvatar_left">
        		<?php echo modZKunenaLatestHelper::getTopicIcon( $item, $params ); ?>
            </div>
        <?php endif ;?>
            <div class="zKlatestSubject <?php echo ($params->get('noWrap', 1) ? 'zNoWrap' : '') ?>">
                <?php echo modZKunenaLatestHelper::getSubject($item, $params); ?>
                <?php if (($params->get('showNew', 1))&& ($item->unread)) :?>
                <span class="zKlatestUnread">
                    <?php echo modZKunenaLatestHelper::getNew($item, $params);  ?>
                </span>
                <?php endif;?>
            </div>
            <?php if ($params->get('showAuthor', 1)) : ?>
            <div class="zKlatestAuthor <?php echo ($params->get('noWrap', 1) ? 'zNoWrap' : '') ?>">
				<?php 
                echo $params->get( 'authorPrefix', '' ). ' ';
                echo modZKunenaLatestHelper::getAuthor($item, $params);
                ?>
            </div>
            <?php endif; ?>

            <?php if ($params->get('showTime')) : ?>
            <div class="zKlatestDatetime <?php echo ($params->get('noWrap', 1) ? 'zNoWrap' : '') ?>">
                <?php echo modZKunenaLatestHelper::getPostDate($item, $params)?>
            </div>
            <?php endif; ?>
            <?php if($params->get('MooFX', 1 )) : ?>
            <?php require JModuleHelper::getLayoutPath('mod_zkunenalatest', $params->get('detailLayout', 'detail_default' ) );?>
            <?php endif; ?>
        </div>
    </div>	
<?php endforeach; ?>
</div>
<?php if ($params->get('allowLink',1)) : ?>
	<span class="small"><?php echo modZKunenaLatestHelper::getCredit() ;?></span>
<?php endif;?>
</div>