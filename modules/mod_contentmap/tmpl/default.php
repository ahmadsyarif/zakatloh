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
	if (empty($GLOBALS["contentmap"]["gapi"]))
	{
		if ($params->get("data_source", "0")==0){
			// Add Google api to the document only once
			$current_uri = JFactory::getURI();
			$document->addScript(($current_uri->isSSL()?'https':'http')."://maps.google.com/maps/api/js?sensor=false&amp;libraries=weather" . $language . $api_key);
			$GLOBALS["contentmap"]["gapi"] = true;
		}
	}
	if (empty($GLOBALS["contentmap"]["google_jsapi"]))
	{
		if ($params->get("data_source", "0")=='joomlatags'){
			// Add Google api to the document only once
			$current_uri = JFactory::getURI();
			$document->addScript("https://www.google.com/jsapi" );
			$GLOBALS["contentmap"]["google_jsapi"] = true;
		}
	}

	$stylesheet = pathinfo($params->get("css", "default"));
	$document->addStyleSheet($prefix . "&amp;type=css&amp;filename=" . $stylesheet["filename"] . $postfix);
	/*
	if ($params->get("data_source", NULL))
	$document->addScript($params->get("data_url") . "?source=custom" . $postfix);
	else
	$document->addScript($prefix . "&amp;type=json&amp;filename=articlesmarkers&amp;source=articles" . $postfix);
	*/

	$json_script='';
	
	$map_script=$prefix . "&amp;type=js&amp;filename=map" . $postfix;
	
	switch ($params->get("data_source", "0"))
	{
		case "0":
			$json_script=$prefix . "&amp;type=json&amp;filename=articlesmarkers&amp;source=articles" . $postfix;
			//$document->addScript($prefix . "&amp;type=json&amp;filename=articlesmarkers&amp;source=articles" . $postfix);
			break;
		case "joomlatags":
			$map_script=$prefix . "&amp;type=js&amp;filename=geochartmap" . $postfix;
			$json_script=$prefix . "&amp;type=json&amp;filename=articlesmarkers&amp;source=tags" . $postfix;
			//$document->addScript($prefix . "&amp;type=json&amp;filename=articlesmarkers&amp;source=articles" . $postfix);
			break;
		//case "1":
		//	$json_script=$params->get("data_url") . "?source=custom" . $postfix;
		//	//$document->addScript($params->get("data_url") . "?source=custom" . $postfix);
		//	break;
		//default:
		//	$json_script=JURI::base(true) . "/libraries/contentmap/json/" . $params->get("data_source") . ".php?source=custom" . $postfix;
			//$document->addScript(JURI::base(true) . "/libraries/contentmap/json/" . $params->get("data_source") . ".php?source=custom" . $postfix);
	}

	if ($params->get("cluster", "1"))
	{
		$document->addScript(JURI::root() . "media/contentmap/js/markerclusterer_compiled.js");
	}
	$document->addScript(JURI::root() . "media/contentmap/js/oms.min.js");

	
	//$document->addScript($prefix . "&amp;type=js&amp;filename=map" . $postfix);
	
	//$document->addScript($json_script);
	//$document->addScript($map_script);
	
	$ns='module_'.$module->id;
	
	$document->addScriptDeclaration('
	
	var lazy_load_loaded_'.$ns.'={"map":false,"json":false,"alreadyinit":false};
	
	function lazy_load_map_loaded_'.$ns.'(){
		lazy_load_loaded_'.$ns.'.map=true;
		lazy_load_do_init_'.$ns.'();
	}
	function lazy_load_json_loaded_'.$ns.'(){
		lazy_load_loaded_'.$ns.'.json=true;
		lazy_load_do_init_'.$ns.'();
	}
	function lazy_load_do_init_'.$ns.'(){
		if (lazy_load_loaded_'.$ns.'.map && lazy_load_loaded_'.$ns.'.json && !lazy_load_loaded_'.$ns.'.alreadyinit){
			lazy_load_loaded_'.$ns.'.alreadyinit=true;
			//init definito in map.php
			init_'.$ns.'();
			preload_'.$ns.'();
			
		}
	}
	
	function lazy_load_json_and_map_'.$ns.'() {
		document.getElementById("contentmap_'.$ns.'").className = "contentmap_loading";
		
		var json_element = document.createElement("script");
		json_element.src = \''.htmlspecialchars_decode($json_script).'\';
		json_element.onreadystatechange= function () {
			if (this.readyState == "complete") lazy_load_json_loaded_'.$ns.'();
		}
		json_element.onload= lazy_load_json_loaded_'.$ns.';		
		document.body.appendChild(json_element);
		
		var map_element = document.createElement("script");
		map_element.src = \''.htmlspecialchars_decode($map_script).'\';
		map_element.onreadystatechange= function () {
			if (this.readyState == "complete") lazy_load_map_loaded_'.$ns.'();
		}
		map_element.onload= lazy_load_map_loaded_'.$ns.';		
		document.body.appendChild(map_element);
	}
	if (window.addEventListener){
		window.addEventListener("load", lazy_load_json_and_map_'.$ns.', false);
	}else if (window.attachEvent){
		window.attachEvent("onload", lazy_load_json_and_map_'.$ns.');
	}else{
		window.onload = lazy_load_json_and_map_'.$ns.';
	}
	');	
	
	$module_class='contentmap_module'.$params->get("moduleclass_sfx", "");
?>

<div class="<?php echo $module_class;?>" id="contentmap_wrapper_module_<?php echo $module->id; ?>">
	<div id="contentmap_container_module_<?php echo $module->id; ?>">
		<div id="contentmap_module_<?php echo $module->id; ?>" class="contentmap_loading">
			<noscript><?php echo JText::_("CONTENTMAP_JAVASCRIPT_REQUIRED"); ?></noscript>
		</div>
	</div>
<?php if ($params->get("category_legend_filter", "0")==1) { ?>
	<div id="contentmap_legend_module_<?php echo $module->id; ?>">
	</div>
<?php }?>	
<?php if ($params->get("category_legend_filter", "0")==2) {  ?>
	<div id="contentmap_legend_tags_module_<?php echo $module->id; ?>">
	</div>
<?php }?>


<?php 
$testo_sotto_mappa=trim($params->get("bottom_description", ""));

if (!empty($testo_sotto_mappa)) {  ?>
	<div id="contentmap_description_module_<?php echo $module->id; ?>">
	<?php echo $testo_sotto_mappa; ?>
	</div>
<?php }?>


</div>
