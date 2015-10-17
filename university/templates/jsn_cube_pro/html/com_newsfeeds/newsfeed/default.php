<?php 
	// no direct access
	defined('_JEXEC') or die('Restricted access');

	// Load template framework
if (!defined('JSN_PATH_TPLFRAMEWORK')) {
	require_once JPATH_ROOT . '/plugins/system/jsntplframework/jsntplframework.defines.php';
	require_once JPATH_ROOT . '/plugins/system/jsntplframework/libraries/joomlashine/loader.php';
}

	JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
	$app 		= JFactory::getApplication();
	$template 	= $app->getTemplate();
	$jsnUtils   = JSNTplUtils::getInstance();
?>
<?php if ($jsnUtils->isJoomla3()): ?>
<?php JHtml::_('behavior.caption'); ?>
<div class="blog-featured<?php echo $this->pageclass_sfx;?>">
<?php if ($this->params->get('show_page_heading') != 0) : ?>
<div class="page-header">
	<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
</div>
<?php endif; ?>
<?php
if (!empty($this->msg))
{
	echo $this->msg;
}
else
{
	$lang = JFactory::getLanguage();
	$myrtl = $this->newsfeed->rtl;
	$direction = " ";

		if ($lang->isRTL() && $myrtl == 0)
		{
			$direction = " redirect-rtl";
		}
		elseif ($lang->isRTL() && $myrtl == 1)
		{
				$direction = " redirect-ltr";
		}
		elseif ($lang->isRTL() && $myrtl == 2)
		{
			$direction = " redirect-rtl";
		}
		elseif ($myrtl == 0)
		{
			$direction = " redirect-ltr";
		}
		elseif ($myrtl == 1)
		{
			$direction = " redirect-ltr";
		}
		elseif ($myrtl == 2)
		{
			$direction = " redirect-rtl";
		}
		$images  = json_decode($this->item->images);
	?>
	<div class="newsfeed<?php echo $this->pageclass_sfx?><?php echo $direction; ?>">
	<?php if ($this->params->get('display_num')) :  ?>
	<h1 class="<?php echo $direction; ?>">
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>
	<h2 class="<?php echo $direction; ?>">
		<?php if ($this->item->published == 0): ?>
			<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
		<?php endif; ?>
		<a href="<?php echo $this->item->link; ?>" target="_blank">
		<?php echo str_replace('&apos;', "'", $this->item->name); ?></a>
	</h2>
	<!-- Show Images from Component -->
	<?php  if (isset($images->image_first) and !empty($images->image_first)) : ?>
	<?php $imgfloat = (empty($images->float_first)) ? $this->params->get('float_first') : $images->float_first; ?>
	<div class="img-intro-<?php echo htmlspecialchars($imgfloat); ?>"> <img
		<?php if ($images->image_first_caption):
			echo 'class="caption"'.' title="' .htmlspecialchars($images->image_first_caption) .'"';
		endif; ?>
		src="<?php echo htmlspecialchars($images->image_first); ?>" alt="<?php echo htmlspecialchars($images->image_first_alt); ?>"/> </div>
	<?php endif; ?>

	<?php  if (isset($images->image_second) and !empty($images->image_second)) : ?>
	<?php $imgfloat = (empty($images->float_second)) ? $this->params->get('float_second') : $images->float_second; ?>
	<div class="pull-<?php echo htmlspecialchars($imgfloat); ?> item-image"> <img
	<?php if ($images->image_second_caption):
		echo 'class="caption"'.' title="' .htmlspecialchars($images->image_second_caption) .'"';
	endif; ?>
	src="<?php echo htmlspecialchars($images->image_second); ?>" alt="<?php echo htmlspecialchars($images->image_second_alt); ?>"/> </div>
	<?php endif; ?>
	<!-- Show Description from Component -->
<?php echo $this->item->description; ?>
	<!-- Show Feed's Description -->

	<?php if ($this->params->get('show_feed_description')) : ?>
		<div class="feed-description">
			<?php echo str_replace('&apos;', "'", $this->rssDoc->description); ?>
		</div>
	<?php endif; ?>

	<!-- Show Image -->
	<?php if (isset($this->rssDoc->image) && isset($this->rssDoc->imagetitle) && $this->params->get('show_feed_image')) : ?>
	<div>
			<img src="<?php echo $this->rssDoc->image; ?>" alt="<?php echo $this->rssDoc->image->decription; ?>" />
</div>
<?php endif; ?>

	<!-- Show items -->
	<?php if (!empty($this->rssDoc[0])){ ?>
	<ol>
		<?php for ($i = 0; $i < $this->item->numarticles; $i++) {  ?>

	<?php
		$uri = !empty($this->rssDoc[$i]->guid) || !is_null($this->rssDoc[$i]->guid) ? $this->rssDoc[$i]->guid : $this->rssDoc[$i]->uri;
		$uri = substr($uri, 0, 4) != 'http' ? $this->item->link : $uri;
		$text = !empty($this->rssDoc[$i]->content) ||  !is_null($this->rssDoc[$i]->content) ? $this->rssDoc[$i]->content : $this->rssDoc[$i]->description;
	?>
			<li>
				<?php if (!empty($uri)) : ?>
					<a href="<?php echo $uri; ?>" target="_blank">
					<?php  echo $this->rssDoc[$i]->title; ?></a>
				<?php else : ?>
					<h3><?php  echo $this->rssDoc[$i]->title; ?></h3>
				<?php  endif; ?>
				<?php if ($this->params->get('show_item_description') && !empty($text)) : ?>
					<div class="feed-item-description">
					<?php if($this->params->get('show_feed_image', 0) == 0)
					{
						$text = JFilterOutput::stripImages($text);
					}
					$text = JHtml::_('string.truncate', $text, $this->params->get('feed_character_count'));
						echo str_replace('&apos;', "'", $text);
					?>

					</div>
				<?php endif; ?>
				</li>
			<?php } ?>
			</ol>
		<?php } ?>
	</div>
<?php } ?>
<?php else : ?>
<?php
$lang = JFactory::getLanguage();
$myrtl = $this->newsfeed->rtl;
$direction = " ";

if ($lang->isRTL() && $myrtl == 0) {
	$direction = " redirect-rtl";
} else
	if ($lang->isRTL() && $myrtl == 1) {
		$direction = " redirect-ltr";
	} else
		if ($lang->isRTL() && $myrtl == 2) {
			$direction = " redirect-rtl";
		} else
			if ($myrtl == 0) {
				$direction = " redirect-ltr";
			} else
				if ($myrtl == 1) {
					$direction = " redirect-ltr";
				} else
					if ($myrtl == 2) {
						$direction = " redirect-rtl";
					}
?>
<div class="com-newsfeed <?php echo $this->pageclass_sfx?><?php echo $direction; ?>">
	<div class="news-feed">

		<?php if ($this->params->get('show_page_heading', 1)) : ?>
		<h2 class="componentheading <?php echo $direction; ?>">
			<?php echo $this->escape($this->params->get('page_heading')); ?>
			<a href="<?php echo $this->newsfeed->channel['link']; ?>" target="_blank">
				<?php echo str_replace('&apos;', "'", $this->newsfeed->channel['title']); ?>
			</a>
		</h2>
		<?php endif; ?>

		<!-- Show Description -->
		<?php if ($this->params->get('show_feed_description')) : ?>
			<div class="contentdescription clearafter">
				<?php echo str_replace('&apos;', "'", $this->newsfeed->channel['description']); ?>
			</div>
		<?php endif; ?>

		<!-- Show Image -->
		<?php if (isset($this->newsfeed->image['url']) && isset($this->newsfeed->image['title']) && $this->params->get('show_feed_image')) : ?>
			<img src="<?php echo $this->newsfeed->image['url']; ?>" alt="<?php echo $this->newsfeed->image['title']; ?>" />
		<?php endif; ?>

		<!-- Show items -->
		<ul>
			<?php foreach ($this->newsfeed->items as $item) :  ?>
				<li>
					<?php if (!is_null($item->get_link())) : ?>
						<a href="<?php echo $item->get_link(); ?>" target="_blank">
							<?php echo $item->get_title(); ?></a>
					<?php endif; ?>
					<?php if ($this->params->get('show_item_description') && $item->get_description()) : ?>
						<br/>
						<?php $text = $item->get_description();
						if($this->params->get('show_feed_image', 0) == 0)
						{
							$text = JFilterOutput::stripImages($text);
						}
						$text = JHTML::_('string.truncate', $text, $this->params->get('feed_character_count'));
							echo str_replace('&apos;', "'", $text);
						?>
					<br/><br/>
					<?php endif; ?>
					</li>
				<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php endif; ?>