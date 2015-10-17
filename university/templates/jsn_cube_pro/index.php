<?php
/**
 * @author    JoomlaShine.com http://www.joomlashine.com
 * @copyright Copyright (C) 2008 - 2011 JoomlaShine.com. All rights reserved.
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted index access');

// Load template framework
if (!defined('JSN_PATH_TPLFRAMEWORK')) {
	require_once JPATH_ROOT . '/plugins/system/jsntplframework/jsntplframework.defines.php';
	require_once JPATH_ROOT . '/plugins/system/jsntplframework/libraries/joomlashine/loader.php';
}

// Preparing template parameters
JSNTplTemplateHelper::prepare();

// Get template utilities
$jsnutils = JSNTplUtils::getInstance();
?>
<!DOCTYPE html>
<!-- <?php echo $this->template . ' ' . JSNTplHelper::getTemplateVersion($this->template); ?> -->
<html lang="<?php echo $this->language ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="head" />
	<!-- html5.js and respond.min.js for IE less than 9 -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<script src="<?php echo JURI::root(true) ?>/plugins/system/jsntplframework/assets/3rd-party/respond/respond.min.js"></script>
	<![endif]-->
	<?php
	/*====== Show analytics code configured in template parameter ======*/
	if ($this->codePosition == 0) echo $this->codeAnalytic;
	?>
  <style>.first{float:none !important;text-align:center !important;display:inline;}.nav_pills{list-style:none;}.last{float:none !important;text-align:center !important;display:inline;}.last>a{background: #7B6CCA;
color: white;
      text-decoration: none;}.first>a{background: #7B6CCA;
color: white;
      text-decoration: none;}#jsn-menu_inner{width: 149px;
height: 40px;
margin-left: auto;
margin-right: auto;
      display: block;}#jsn-menu div.jsn-modulecontainer {margin-left:-9%}li{display:none}#zKlatest{margin-left:auto;margin-right:auto;display:block}.general_recent{text-align:center !important}#jsn-menu{height:20px;}</style>
</head>
<body id="jsn-master" class="<?php echo $this->bodyClass ?>">
	<a name="top" id="top"></a>

	<?php
	/*====== Show modules in position "topbar" ======*/
	if ($jsnutils->countModules('topbar') > 0) {
	?>
	<div id="jsn-topbar">
		<div id="jsn-pos-topbar">
			<jdoc:include type="modules" name="topbar" style="jsnmodule" />
		</div>
		<div class="clearbreak"></div>
	</div>
	<?php } ?>
	<div id="jsn-page" class="container">
	<?php
		/*====== Show modules in position "stick-lefttop" ======*/
		if ($jsnutils->countModules('stick-lefttop') > 0) {
	?>
		<div id="jsn-pos-stick-lefttop">
			<jdoc:include type="modules" name="stick-lefttop" style="jsnmodule" />
		</div>
	<?php
		}

		/*====== Show modules in position "stick-leftmiddle" ======*/
		if ($jsnutils->countModules('stick-leftmiddle') > 0) {
	?>
		<div id="jsn-pos-stick-leftmiddle">
			<jdoc:include type="modules" name="stick-leftmiddle" style="jsnmodule" />
		</div>
	<?php
		}

		/*====== Show modules in position "stick-leftbottom" ======*/
		if ($jsnutils->countModules('stick-leftbottom') > 0) {
	?>
		<div id="jsn-pos-stick-leftbottom">
			<jdoc:include type="modules" name="stick-leftbottom" style="jsnmodule" />
		</div>
	<?php
		}

		/*====== Show modules in position "stick-righttop" ======*/
		if ($jsnutils->countModules('stick-righttop') > 0) {
	?>
		<div id="jsn-pos-stick-righttop">
			<jdoc:include type="modules" name="stick-righttop" style="jsnmodule" />
		</div>
	<?php
		}

		/*====== Show modules in position "stick-rightmiddle" ======*/
		if ($jsnutils->countModules('stick-rightmiddle') > 0) {
	?>
		<div id="jsn-pos-stick-rightmiddle">
			<jdoc:include type="modules" name="stick-rightmiddle" style="jsnmodule" />
		</div>
	<?php
		}

		/*====== Show modules in position "stick-rightbottom" ======*/
		if ($jsnutils->countModules('stick-rightbottom') > 0) {
	?>
		<div id="jsn-pos-stick-rightbottom">
			<jdoc:include type="modules" name="stick-rightbottom" style="jsnmodule" />
		</div>
	<?php
		}
	?>
		<div id="jsn-header">
			<div id="jsn-logo" class="pull-left">
			<?php
				/*====== Show modules in position "logo" ======*/
				if ($jsnutils->countModules('logo') > 0) {
			?>
				<div id="jsn-pos-logo">
					<jdoc:include type="modules" name="logo" style="jsnmodule" />
				</div>

			<?php
				/*====== If there are NO modules in position "logo", then show logo image file "logo.png" ======*/
				} else {
					/*====== Attach link to logo image ======*/
					if (!empty($this->logoLink)) {
						echo '<a href="' . $this->logoLink . '" title="' . $this->logoSlogan . '">';
					}

					/*====== Show mobile logo ======*/
					if (!empty($this->mobileLogo)) {
						echo '<img src="' . $this->mobileLogo . '" alt="' . $this->logoSlogan . '" id="jsn-logo-mobile" />';
					}

					/*====== Show desktop logo ======*/
					if (!empty($this->logoFile)) {
						echo '<img src="' . $this->logoFile . '" alt="' . $this->logoSlogan . '" id="jsn-logo-desktop" />';
					}

					if ($this->logoLink != "") {
						echo '</a>';
					}
				}
			?>
			</div>
			<div id="jsn-headerright" class="pull-right">
			<?php
				/*====== Show modules in position "top" ======*/
				if ($jsnutils->countModules('top') > 0) {
			?>
				<div id="jsn-pos-top" class="pull-left">
					<jdoc:include type="modules" name="top" style="jsnmodule" />
					<div class="clearbreak"></div>
				</div>
			<?php
				}

				/*====== Show social icons ======*/
				if (isset($this->socialIcons) AND count($this->socialIcons)) {
			?>
				<div id="jsn-social-icons" class="pull-right">
					<ul>
				<?php
					foreach ($this->socialIcons AS $name => $data) {
				?>
						<li class="<?php echo $name; ?>">
							<a href="<?php echo $data['link']; ?>" title="<?php echo JText::_($data['title']); ?>" target="_blank">
								<?php echo JText::_($data['title']); ?></a>
						</li>
				<?php
					}
				?>
					</ul>
				</div>
			<?php
				}

				/*====== Show modules in position "top" ======*/
				if ($jsnutils->countModules('top1') > 0) {
			?>
				<div id="jsn-pos-top1">
					<jdoc:include type="modules" name="top1" style="jsnmodule" />
					<div class="clearbreak"></div>
				</div>
			<?php
				}
			?>
			<?php
				/*====== Show modules in position "top" ======*/
				if ($jsnutils->countModules('top2') > 0) {
			?>
				<div id="jsn-pos-top2">
					<jdoc:include type="modules" name="top2" style="jsnmodule" />
					<div class="clearbreak"></div>
				</div>
			<?php
				}
			?>
			</div>
			<div class="clearbreak"></div>
		<?php
			if ($this->helper->countPositions('mainmenu', 'toolbar') || $this->colorSelector) {
		?>
			<div id="jsn-menu"><div id="jsn-menu_inner">
			<?php
				/*====== Show modules in position "mainmenu" ======*/
				if ($jsnutils->countModules('mainmenu') > 0) {
			?>
				<div id="jsn-pos-mainmenu">
					<jdoc:include type="modules" name="mainmenu" style="jsnmodule" />
				</div>
			<?php
				}
			?>
			<?php
				/*====== Show elements in Sitetools ======*/
				if ($this->sitetoolStyle) {
			?>
				<div id="jsn-sitetoolspanel" class="<?php echo ($this->colorSelector)?'jsn-include-color':''; ?> pull-right">
					<ul id="jsn-sitetools-<?php echo $this->sitetoolStyle; ?>">
						<li class="clearafter jsn-sitetool-control">
						<a href="javascript:void(0)"></a>
							<ul>
								<li class="clearafter jsn-selector-color">
					<?php
						foreach ($this->templateColors AS $tcolor) {
					?>
									<a id="jsn-selector-<?php echo $tcolor; ?>" title="<?php echo JText::_('JSN_TPLFW_SITETOOLS_SELECT_COLOR').': '.JText::_('JSN_TPLFW_COLOR_' . $tcolor); ?>" href="#" onclick="javascript: JSNUtils.setTemplateAttribute('<?php echo $this->templatePrefix ?>','color','<?php echo $tcolor; ?>'); return false;" <?php echo ($tcolor == $this->templateColor)?' class="current"':''; ?>></a>
					<?php
						}
					?>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			<?php
				}

				/*====== Show button to jump to mobile view if user is using mobile device ======*/
				if ($this->desktopSwitcher) {
			?>
				<span id="jsn-desktopswitch">
					<a href="#" onclick="javascript: JSNUtils.setTemplateAttribute('<?php echo $this->templatePrefix ?>','mobile','no'); return false;"></a>
				</span>
				<span id="jsn-mobileswitch">
					<a href="#" onclick="javascript: JSNUtils.setTemplateAttribute('<?php echo $this->templatePrefix ?>','mobile','yes'); return false;"></a>
				</span>
			<?php
				}

				/*====== Show modules in position "toolbar" ======*/
				if ($jsnutils->countModules('toolbar') > 0) {
			?>
				<div id="jsn-pos-toolbar">
					<jdoc:include type="modules" name="toolbar" style="jsnmodule" />
				</div>
			<?php
				}
			?>
            <div class="clearbreak"></div>
			</div></div>
		<?php
			}
		?>
		</div>
		<div id="jsn-body">
			<div id="jsn-content-top-wrapper">
				<div id="jsn-content-top-wrapper_inner">
					<div id="jsn-content-top-wrapper_inner1">
					<?php
						/*====== Show modules in content top area ======*/
						if ($this->helper->countPositions('promo-left', 'promo', 'promo-right', 'content-top')) {
					?>
						<div id="jsn-content-top" class="<?php echo (($this->hasPromoLeft)?'jsn-haspromoleft ':'') ?><?php echo (($this->hasPromoRight)?'jsn-haspromoright ':'') ?>">
							<div id="jsn-promo" class="row-fluid"><div id="jsn-promo_inner">
							<?php
								foreach ($this->promoColumns AS $id => $class) {
									/*====== Show modules in position "promo" ======*/
									if ($id == 'promo') {
							?>
				                    <div id="jsn-pos-promo" class="<?php echo $class['span']; ?> <?php echo $class['order']; ?> <?php echo $class['offset']; ?>">
				                        <jdoc:include type="modules" name="promo" style="jsnmodule" class="jsn-roundedbox" />
				                    </div>
							<?php
									}

									/*====== Show modules in position "promo-left" ======*/
									elseif ($id == 'promo-left') {
							?>
				                    <div id="jsn-pos-promo-left" class="<?php echo $class['span']; ?> <?php echo $class['order']; ?> <?php echo $class['offset']; ?>">
				                    	<div id="jsn-pos-promo-left_inner">
											<jdoc:include type="modules" name="promo-left" style="jsnmodule" class="jsn-roundedbox" />
										</div>
				                    </div>
							<?php
									}

									/*====== Show modules in position "promo-right" ======*/
									elseif ($id == 'promo-right') {
							?>
				                    <div id="jsn-pos-promo-right" class="<?php echo $class['span']; ?> <?php echo $class['order']; ?> <?php echo $class['offset']; ?>">
										<div id="jsn-pos-promo-right_inner">
											<jdoc:include type="modules" name="promo-right" style="jsnmodule" class="jsn-roundedbox" />
										</div>
				                    </div>
							<?php
									}
								}
							?>
									<div class="clearbreak"></div>
								</div></div>
						<?php
							/*====== Show modules in position "content-top" ======*/
							if ($jsnutils->countModules('content-top') > 0) {
						?>
							<div id="jsn-pos-content-top" class="jsn-modulescontainer jsn-horizontallayout jsn-modulescontainer<?php echo $jsnutils->countModules('content-top'); ?> row-fluid">
								<jdoc:include type="modules" name="content-top" style="jsnmodule" class="jsn-roundedbox" columnClass="span<?php echo ceil(12 / $jsnutils->countModules('content-top')); ?>" />
							</div>
						<?php
							}
						?>
						</div>
					<?php
						}
					?>
					</div>
				</div>
			</div>
			<div id="jsn-content" class="<?php echo (($this->hasLeft)?'jsn-hasleft ':'') ?><?php echo (($this->hasRight)?'jsn-hasright ':'') ?><?php echo (($this->hasInnerLeft)?'jsn-hasinnerleft ':'') ?><?php echo (($this->hasInnerRight)?'jsn-hasinnerright ':'') ?>">
				<div id="jsn-content_inner"><div id="jsn-content_inner1"><div id="jsn-content_inner2"><div id="jsn-content_inner3" class="row-fluid">
				<?php
					foreach ($this->mainColumns AS $id => $class) {
						if ($id == 'content') {
				?>
							<div id="jsn-maincontent" class="<?php echo $class['span']; ?> <?php echo $class['order']; ?> <?php echo $class['offset']; ?> row-fluid"><div id="jsn-maincontent_inner">
				<?php
							foreach($this->contentColumns AS $id2 => $class2) {
								if ($id2 == 'component') {
				?>
								<div id="jsn-centercol" class="<?php echo $class2['span']; ?> <?php echo $class2['order']; ?> <?php echo $class2['offset']; ?>"><div id="jsn-centercol_inner">
				<?php
					/*====== Show modules in position "breadcrumbs" ======*/
					if ($jsnutils->countModules('breadcrumbs') > 0) {
				?>
									<div id="jsn-breadcrumbs">
											<jdoc:include type="modules" name="breadcrumbs" />
										</div>
				<?php
					}

					/*====== Show modules in position "user-top" ======*/
					if ($jsnutils->countModules('user-top') > 0) {
				?>
									<div id="jsn-pos-user-top" class="jsn-modulescontainer jsn-horizontallayout jsn-modulescontainer<?php echo $jsnutils->countModules('user-top'); ?> row-fluid">
											<jdoc:include type="modules" name="user-top" style="jsnmodule" class="jsn-roundedbox" columnClass="span<?php echo ceil(12 / $jsnutils->countModules('user-top')); ?>" />
										</div>
				<?php
					}

					/*====== Show modules in position "user1" and "user2" ======*/
					$positionCount = $this->helper->countPositions('user1', 'user2');
					if ($positionCount)
					{
						$grid_suffix = $positionCount;
				?>
									<div id="jsn-usermodules1" class="jsn-modulescontainer clearafter">
					<?php
						/*====== Show modules in position "user1" ======*/
						if ($jsnutils->countModules('user1') > 0) {
					?>
											<div id="jsn-pos-user1" class="span<?php echo ceil(12 / $grid_suffix); ?>">
												<jdoc:include type="modules" name="user1" style="jsnmodule" class="jsn-roundedbox" />
											</div>
					<?php
						}

						/*====== Show modules in position "user2" ======*/
						if ($jsnutils->countModules('user2') > 0) {
					?>
											<div id="jsn-pos-user2" class="span<?php echo ceil(12 / $grid_suffix); ?>">
												<jdoc:include type="modules" name="user2" style="jsnmodule" class="jsn-roundedbox" />
											</div>
					<?php
						}
					?>
										</div>
				<?php
					}
				?>
									<div id="jsn-mainbody-content" class="<?php echo ($jsnutils->countModules('mainbody-top') > 0)?' jsn-hasmainbodytop':'' ?><?php echo ($jsnutils->countModules('mainbody-bottom') > 0)?' jsn-hasmainbodybottom':'' ?><?php echo ($this->showFrontpage)?' jsn-hasmainbody':'' ?>">
				<?php
					/*====== Show modules in position "mainbody-top" ======*/
					if ($jsnutils->countModules('mainbody-top') > 0) {
				?>
											<div id="jsn-pos-mainbody-top" class="jsn-modulescontainer jsn-horizontallayout jsn-modulescontainer<?php echo $jsnutils->countModules('mainbody-top'); ?> row-fluid">
												<jdoc:include type="modules" name="mainbody-top" style="jsnmodule" class="jsn-roundedbox" columnClass="span<?php echo ceil(12 / $jsnutils->countModules('mainbody-top')); ?>" />
											</div>
				<?php
					}

					/*====== Show mainbody ======*/
					if ($this->showFrontpage) {
				?>
											<div id="jsn-mainbody">
												<jdoc:include type="message" />
												<jdoc:include type="component" />
											</div>
				<?php
					}

					/*====== Show modules in position "mainbody-bottom" ======*/
					if ($jsnutils->countModules('mainbody-bottom') > 0) {
				?>
											<div id="jsn-pos-mainbody-bottom" class="jsn-modulescontainer jsn-horizontallayout jsn-modulescontainer<?php echo $jsnutils->countModules('mainbody-bottom'); ?> row-fluid">
												<jdoc:include type="modules" name="mainbody-bottom" style="jsnmodule" class="jsn-roundedbox" columnClass="span<?php echo ceil(12 / $jsnutils->countModules('mainbody-bottom')); ?>" />
											</div>
				<?php
					}
				?>
										</div>
				<?php
					/*====== Show modules in position "user3" and "user4" ======*/
					$positionCount = $this->helper->countPositions('user3', 'user4');
					if ($positionCount) {
						$grid_suffix = $positionCount;
				?>
									<div id="jsn-usermodules2" class="jsn-modulescontainer jsn-modulescontainer<?php echo $grid_suffix; ?>">
											<div id="jsn-usermodules2_inner_grid<?php echo $grid_suffix; ?>" class="row-fluid">
					<?php
						/*====== Show modules in position "user3" ======*/
						if ($jsnutils->countModules('user3') > 0) {
					?>
												<div id="jsn-pos-user3" class="span<?php echo ceil(12 / $grid_suffix); ?>">
													<jdoc:include type="modules" name="user3" style="jsnmodule" class="jsn-roundedbox" />
												</div>
					<?php
						}

						/*====== Show modules in position "user4" ======*/
						if ($jsnutils->countModules('user4') > 0) { ?>
												<div id="jsn-pos-user4" class="span<?php echo ceil(12 / $grid_suffix); ?>">
													<jdoc:include type="modules" name="user4" style="jsnmodule" class="jsn-roundedbox" />
												</div>
					<?php
						}
					?>
												<div class="clearbreak"></div>
											</div>
										</div>
				<?php
					}

					/*====== Show modules in position "user-bottom" ======*/
					if ($jsnutils->countModules('user-bottom') > 0) { ?>
									<div id="jsn-pos-user-bottom" class="jsn-modulescontainer jsn-horizontallayout jsn-modulescontainer<?php echo $jsnutils->countModules('user-bottom'); ?> row-fluid">
											<jdoc:include type="modules" name="user-bottom" style="jsnmodule" class="jsn-roundedbox" columnClass="span<?php echo ceil(12 / $jsnutils->countModules('user-bottom')); ?>" />
										</div>
				<?php
					}

					/*====== Show modules in position "banner" ======*/
					if ($jsnutils->countModules('banner') > 0) {
				?>
									<div id="jsn-pos-banner">
											<jdoc:include type="modules" name="banner" style="jsnmodule" />
										</div>
				<?php
					}
				?>
								</div></div> <!-- end centercol -->
				<?php
								} elseif ($id2 == 'innerleft') {
								/*====== Show modules in position "innerleft" ======*/
				?>
								<div id="jsn-pos-innerleft" class="<?php echo $class2['span']; ?> <?php echo $class2['order']; ?> <?php echo $class2['offset']; ?>">
									<div id="jsn-pos-innerleft_inner">
										<jdoc:include type="modules" name="innerleft" style="jsnmodule" class="jsn-roundedbox" />
									</div>
								</div>
				<?php
								} elseif ($id2 == 'innerright') {
								/*====== Show modules in position "innerright" ======*/
				?>
								<div id="jsn-pos-innerright" class="<?php echo $class2['span']; ?> <?php echo $class2['order']; ?> <?php echo $class2['offset']; ?>">
									<div id="jsn-pos-innerright_inner">
										<jdoc:include type="modules" name="innerright" style="jsnmodule" class="jsn-roundedbox" />
									</div>
								</div>
				<?php
								}
							}
				?>
							</div></div> <!-- end jsn-maincontent -->
				<?php
						} elseif ($id == 'left') {
					/*====== Show modules in position "left" ======*/
				?>
							<div id="jsn-leftsidecontent" class="<?php echo $class['span']; ?> <?php echo $class['order']; ?> <?php echo $class['offset']; ?>">
								<div id="jsn-leftsidecontent_inner">
									<div id="jsn-pos-left">
										<jdoc:include type="modules" name="left" style="jsnmodule" class="jsn-roundedbox" />
									</div>
								</div>
							</div>
				<?php
						} elseif ($id == 'right') {
					/*====== Show modules in position "right" ======*/
				?>
							<div id="jsn-rightsidecontent" class="<?php echo $class['span']; ?> <?php echo $class['order']; ?> <?php echo $class['offset']; ?>">
								<div id="jsn-rightsidecontent_inner">
									<div id="jsn-pos-right">
										<jdoc:include type="modules" name="right" style="jsnmodule" class="jsn-roundedbox" />
									</div>
								</div>
							</div>
				<?php
						}
					}

				?>
			</div></div></div></div>
		<?php
			/*====== Show elements in content bottom area ======*/
			if ($this->helper->countPositions('content-bottom', 'user5', 'user6', 'user7')) {
		?>
			<div id="jsn-content-bottom">
			<?php
				/*====== Show modules in position "content-bottom" ======*/
				if ($jsnutils->countModules('content-bottom') > 0) {
			?>
                <div id="jsn-pos-content-bottom" class="jsn-modulescontainer jsn-horizontallayout jsn-modulescontainer<?php echo $jsnutils->countModules('content-bottom'); ?> row-fluid">
                	<jdoc:include type="modules" name="content-bottom" style="jsnmodule" class="jsn-roundedbox" columnClass="span<?php echo ceil(12 / $jsnutils->countModules('content-bottom')); ?>" />
                </div>
			<?php
				}
				if ($this->helper->countPositions('user5', 'user6', 'user7')) {
			?>
				<div id="jsn-usermodules3" class="jsn-modulescontainer jsn-modulescontainer<?php echo $this->helper->countPositions('user5', 'user6', 'user7'); ?> row-fluid">
			<?php

				/*====== Show modules in position "user5", "user6", "user7" ======*/
				foreach ($this->userColumns AS $id => $class) {

					/*====== Show modules in position "user5" ======*/
					if ($id == 'user5') {
				?>
					<div id="jsn-pos-user5" class="<?php echo $class['span']; ?> <?php echo $class['order']; ?> <?php echo $class['offset']; ?>">
						<jdoc:include type="modules" name="user5" style="jsnmodule" class="jsn-roundedbox" />
					</div>
				<?php
					}

					/*====== Show modules in position "user6" ======*/
					elseif ($id =='user6') {
				?>
					<div id="jsn-pos-user6" class="<?php echo $class['span']; ?> <?php echo $class['order']; ?> <?php echo $class['offset']; ?>">
						<jdoc:include type="modules" name="user6" style="jsnmodule" class="jsn-roundedbox" />
					</div>
				<?php
					}

					/*====== Show modules in position "user7" ======*/
					elseif ($id =='user7') {
				?>
					<div id="jsn-pos-user7" class="<?php echo $class['span']; ?> <?php echo $class['order']; ?> <?php echo $class['offset']; ?>">
						<jdoc:include type="modules" name="user7" style="jsnmodule" class="jsn-roundedbox" />
					</div>
				<?php
					}
				?>
			<?php
				}
			?>
				</div>
			<?php
				}
			?>
            </div>
		<?php
			}
		?>
		</div>
		<?php
			/*====== Show modules in position "footer" and "bottom" ======*/
			$positionCount = $this->helper->countPositions('footer', 'bottom');
			if ($positionCount) {
				$grid_suffix = $positionCount;
		?>
			<div id="jsn-footer">
				<div id="jsn-footermodules" class="jsn-modulescontainer jsn-modulescontainer<?php echo $grid_suffix; ?> row-fluid">
			<?php
				/*====== Show modules in position "footer" ======*/
				if ($jsnutils->countModules('footer') > 0) {
			?>
					<div id="jsn-pos-footer" class="span<?php echo ceil(12 / $grid_suffix); ?>">
						<jdoc:include type="modules" name="footer" style="jsnmodule" />
					</div>
			<?php
				}

				/*====== Show modules in position "bottom" ======*/
				if ($jsnutils->countModules('bottom') > 0) {
			?>
					<div id="jsn-pos-bottom" class="span<?php echo ceil(12 / $grid_suffix); ?>">
						<jdoc:include type="modules" name="bottom" style="jsnmodule" />
					</div>
			<?php
				}
			?>
					<div class="clearbreak"></div>
				</div>
			</div>
		<?php
			}
		?>
	</div>
	<?php
		/*====== Show "Go to top" link ======*/
		if ($this->gotoTop) {
			// Get rid of hitcount=0 that may prevent the button's action
			$hitcount = JRequest::getVar('hitcount', null, 'GET');
			if ($hitcount === null) {
				$this->uri->delVar('hitcount');
			}

			$return = $this->uri->toString();
	?>
		<a id="jsn-gotoplink" href="<?php echo isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''; ?>#top">
			<span><?php echo JText::_('JSN_TPLFW_GOTO_TOP'); ?></span>
		</a>
	<?php
		}

		/*====== Show modules in position "background" ======*/
		if ($jsnutils->countModules('background') > 0) {
	?>
			<div id="jsn-pos-background">
				<jdoc:include type="modules" name="background" style="jsnmodule" />
			</div>
	<?php
		}
?>
<jdoc:include type="modules" name="debug" />
<?php
	/*====== Show analytics code configured in template parameter ======*/
	if ($this->codePosition == 1) {
		echo $this->codeAnalytic;
	}
?>
</div>
</body>
</html>
