<?php defined('_JEXEC') or die('Restricted access');
	/*
	This file is part of "Content Map Joomla Extension".
	Author: Open Source solutions http://www.opensourcesolutions.es

	You can redistribute and/or modify it under the terms of the GNU
	General Public License as published by the Free Software Foundation,
	either version 2 of the License, or (at your option) any later version.

	GNU/GPL license gives you the freedom:
	* to use this software for both commercial and non-commercial purposes
	* to share, copy, distribute and install this software and charge for it if you wish.

	Under the following conditions:
	* You must attribute the work to the original author

	This software is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this software.  If not, see http://www.gnu.org/licenses/gpl-2.0.html.

	@copyright Copyright (C) 2012 Open Source Solutions S.L.U. All rights reserved.
	*/

	require_once(JPATH_ROOT . '/' . "libraries" . '/' . "contentmap" . '/' . "language" . '/' . "contentmap.inc");
	$language = JFactory::getLanguage();
	$language->load("com_contentmap.sys", realpath(dirname(__FILE__)));
	$langcode = preg_replace("/-.*/", "", $language->get("tag"));
?>

<h1><?php echo($language->_("COM_CONTENTMAP")); ?></h1>

<?php if ($GLOBALS["contentmap"]["version"][strlen($GLOBALS["contentmap"]["version"]) - 1] == " ") { ?>
	<div style="float:left;margin-right:16px;margin-left:10px;">
		<a href="http://www.opensourcesolutions.es/ext/contentmap.html" target="_blank">
			<img src="../media/contentmap/images/buy_now.jpg" border="0" alt="Buy now">
		</a>
	</div>
	<p><strong><?php echo($language->_("COM_CONTENTMAP_PURCHASE")); ?></strong></p>
	<br style="clear:both;" />
	<?php } ?>

<table  width="90%">
    <tr>
		<td align="left" valign="bottom">

			<a href="https://plus.google.com/communities/114304425977801029322" target="blank" class="btn btn-large btn-warning"><i class="icon-users"></i>  COMMUNITY</a>
		    <a href="https://github.com/AlexRed/contentmap" target="blank" class="btn btn-large btn-inverse"><i class="icon-cogs"></i>  GitHub</a>
			<a href="http://www.opensourcesolutions.es/ext/contentmap.html" target="blank" class="btn btn-large btn-success"><i class="icon-download"></i>  INFO & DOWNLOAD</a>
			<a href="http://www.opensourcesolutions.es/ext/contentmap.html" target="blank" class="btn btn-large btn-info"><i class="icon-eye"></i>  DEMO</a>
			<br /><br />
		</td>
	</tr>


	<tr><td>
		<h1><?php echo($language->_("COM_CONTENTMAP_INSTRUCTIONS_LBL")); ?></h1>
		<h2><?php echo($language->_("COM_CONTENTMAP_INSTRUCTIONS_TITLE")); ?></h2>
		<?php echo($language->_("COM_CONTENTMAP_INSTRUCTIONS_DSC")); ?>
		<h2><?php echo($language->_("MOD_CONTENTMAP_INSTRUCTIONS_TITLE")); ?></h2>
		<?php echo($language->_("MOD_CONTENTMAP_INSTRUCTIONS_DSC")); ?>
	</td></tr>
</table>
<p><?php echo(sprintf($language->_("JGLOBAL_ISFREESOFTWARE"), "ContentMap")); ?></p>



