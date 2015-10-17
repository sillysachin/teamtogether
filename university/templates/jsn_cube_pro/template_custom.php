<?php
$jsnutils	= JSNTplUtils::getInstance();
$doc		= $this->_document;

// Count module instances
$doc->hasRight		= $jsnutils->countModules('right');
$doc->hasLeft		= $jsnutils->countModules('left');
$doc->hasPromo		= $jsnutils->countModules('promo');
$doc->hasPromoLeft	= $jsnutils->countModules('promo-left');
$doc->hasPromoRight	= $jsnutils->countModules('promo-right');
$doc->hasInnerLeft	= $jsnutils->countModules('innerleft');
$doc->hasInnerRight	= $jsnutils->countModules('innerright');

// Define template colors
$doc->templateColors	= array('pink', 'red', 'green', 'violet', 'orange', 'cyan');

if (isset($doc->sitetoolsColorsItems))
{
	$this->_document->templateColors = $doc->sitetoolsColorsItems;
}

// Apply K2 style
if ($jsnutils->checkK2())
{
	$doc->addStylesheet($doc->templateUrl . "/ext/k2/jsn_ext_k2.css");
}
// Apply VM style
if ($jsnutils->checkVM())
{
	$doc->addStylesheet($doc->templateUrl . "/ext/vm/jsn_ext_vm.css");
}

// Start generating custom styles
$customCss	= '';

// Process TPLFW v2 parameter
if (isset($doc->customWidth))
{
	if ($doc->customWidth != 'responsive')
	{
		$customCss .= '
	#jsn-page,
	#jsn-menu.jsn-menu-sticky #jsn-menu_inner,
	#jsn-pos-topbar {
		width: ' . $doc->customWidth . ';
		min-width: ' . $doc->customWidth . ';
	}';
	}
}
// Backward compatible
elseif (isset($doc->templateWidth))
{
	$doc->columnPromoLeft	= $doc->params->get('columnPromoLeft', 23);
	$doc->columnPromoRight	= $doc->params->get('columnPromoRight', 23);
	$doc->columnLeft		= $doc->params->get('columnLeft', 23);
	$doc->columnRight		= $doc->params->get('columnRight', 23);
	$doc->columnInnerleft	= $doc->params->get('columnInnerleft', 28);
	$doc->columnInnerright	= $doc->params->get('columnInnerright', 28);

	$tw			= 100;
	$ieOffset	= 0;

	$customCss .= '
	#jsn-page {
		width: ' . $doc->templateWidth . ';
		min-width: ' . $doc->templateWidth . ';
	}';

	$customCss .= '
	#jsn-pos-promo-left {
		float: left;
		width: ' . $doc->columnPromoLeft . '%;
		left: -' . ($tw - $ieOffset) . '%;
	}
	#jsn-pos-promo {
		width: ' . ($tw - $ieOffset) . '%;
		left: '.($doc->hasPromoLeft ? $doc->columnPromoLeft . '%' : 0) . ';
	}
	#jsn-pos-promo-right {
		float: right;
		width: ' . $doc->columnPromoRight . '%;
	}';

	if ($doc->hasPromoRight)
	{
		$tw -= $doc->columnPromoRight;

		$customCss .= '
	#jsn-pos-promo {
		float: left;
		width: ' . ($tw - $ieOffset) . '%;
	}';
	}

	if ($doc->hasPromoLeft)
	{
		$tw -= $doc->columnPromoLeft;

		$customCss .= '
	#jsn-pos-promo {
		width: ' . ($tw - $ieOffset) . '%;
		float: right;
		left: auto;
	}
	#jsn-pos-promo-left {
		left: auto;
	}';
	}

	if ($doc->hasPromoLeft AND $doc->hasPromoRight)
	{
		$tw -= $doc->columnPromoLeft;

		$customCss .= '
	#jsn-pos-promo {
		float: left;
		left: ' . ($doc->hasPromoLeft ? $doc->columnPromoLeft . '%' : 0) . ';
	}
	#jsn-pos-promo-left {
		left: -' . ($tw + $doc->columnPromoLeft) . '%;
	}';
	}

	if ( ! $doc->hasPromo)
	{
		$customCss .= '
	#jsn-pos-promo-left {
		left: auto;
		display: auto;
	}';
	}

	// Setup width of content area
	$tw = 100;

	if ($doc->hasLeft)
	{
		$tw -= $doc->columnLeft;

		$customCss .= '
	#jsn-content_inner {
		right: '.(100 - $doc->columnLeft).'%;
	}
	#jsn-content_inner1 {
		left: '.(100 - $doc->columnLeft).'%;
	}';
	}

	if ($doc->hasRight)
	{
		$tw -= $doc->columnRight;

		$customCss .= '
	#jsn-content_inner2 {
		left: ' . (100 - $doc->columnRight) . '%;
	}
	#jsn-content_inner3 {
		right: ' . (100 - $doc->columnRight) . '%;
	}';
	}

	$customCss .= '
	#jsn-leftsidecontent {
		float: left;
		width: ' . $doc->columnLeft . '%;
		left: -' . ($tw - $ieOffset) . '%;
	}
	#jsn-maincontent {
		float: left;
		width: ' . ($tw - $ieOffset) . '%;
		left: ' . ($doc->hasLeft ? $doc->columnLeft . '%' : 0) . ';
	}
	#jsn-rightsidecontent {
		float: right;
		width: ' . $doc->columnRight . '%;
	}';

	$tw = 100;

	if ($doc->hasInnerLeft)
	{
		$tw -= $doc->columnInnerleft;
	}

	if ($doc->hasInnerRight)
	{
		$tw -= $doc->columnInnerright;
	}

	$customCss .= '
	#jsn-pos-innerleft {
		float: left;
		width: ' . $doc->columnInnerleft . '%;
		left: -' . ($tw - $ieOffset) . '%;
	}
	#jsn-centercol {
		float: left;
		width: ' . ($tw - $ieOffset) . '%;
		left: '.($doc->hasInnerLeft ? $doc->columnInnerleft . '%' : 0) . ';
	}
	#jsn-pos-innerright {
		float: right;
		width: ' . $doc->columnInnerright . '%;
	}';
}

// Setup main menu width parameter
if ($doc->mainMenuWidth)
{
	$menuMargin = $doc->mainMenuWidth;

	$customCss .= '
	div.jsn-modulecontainer ul.menu-mainmenu ul,
	div.jsn-modulecontainer ul.menu-mainmenu ul li {
		width: ' . $doc->mainMenuWidth . 'px;
	}
	div.jsn-modulecontainer ul.menu-mainmenu ul ul {';

	if ($doc->direction == 'ltr')
	{
		$customCss .= '
		margin-left: ' . $menuMargin . 'px;';
	}

	if ($doc->direction == 'rtl')
	{
		$customCss .= '
		margin-right: ' . $menuMargin . 'px;';
	}

	$customCss .= '
	}
	#jsn-pos-toolbar div.jsn-modulecontainer ul.menu-mainmenu ul ul {';

	if ($doc->direction == 'ltr')
	{
		$customCss .= '
		margin-right: ' . $menuMargin . 'px;
		margin-left : auto';
	}

	if ($doc->direction == 'rtl')
	{
		$customCss .= '
		margin-left : ' . $menuMargin . 'px;
		margin-right: auto';
	}

	$customCss .= '
	}';
}

// Setup slide menu width parameter
if ($doc->sideMenuWidth)
{
	$sideMenuMargin = $doc->sideMenuWidth + 6;

	$customCss .= '
	div.jsn-modulecontainer ul.menu-sidemenu ul,
	div.jsn-modulecontainer ul.menu-sidemenu ul li {
		width: ' . $sideMenuMargin . 'px;
	}
	div.jsn-modulecontainer ul.menu-sidemenu li ul {
		right: -' . $doc->sideMenuWidth . 'px;
	}
	body.jsn-direction-rtl div.jsn-modulecontainer ul.menu-sidemenu li ul {
		left: -' . $sideMenuMargin . 'px;
		right: auto;
	}
	div.jsn-modulecontainer ul.menu-sidemenu ul ul {';

	if ($doc->direction == 'ltr')
	{
		$customCss .= '
		margin-left: ' . $sideMenuMargin . 'px;';
	}

	if ($doc->direction == 'rtl')
	{
		$customCss .= '
		margin-right: ' . $sideMenuMargin . 'px;';
	}

	$customCss .= '
	}';
}

// Include CSS3 support for IE browser
if ($doc->isIE)
{
	$customCss .= '
	.text-box,
	.text-box-highlight,
	.text-box-highlight:hover,
	div[class*="box-"] div.jsn-modulecontainer_inner,
	div[class*="solid-"] div.jsn-modulecontainer_inner {
		behavior: url(' . $doc->rootUrl . '/templates/' . strtolower($doc->template) . '/css/PIE.htc);
	}
	.link-button {
		zoom: 1;
		position: relative;
		behavior: url(' . $doc->rootUrl . '/templates/' . strtolower($doc->template) . '/css/PIE.htc);
	}';
}

$doc->addStyleDeclaration(trim($customCss, "\n"));
