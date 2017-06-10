<?php defined('_JEXEC') or die('Restricted access');
$owner = JRequest::getVar("owner", "", "GET");
$id = JRequest::getVar("id", "", "GET");
JFactory::getLanguage()->load("contentmap", JPATH_LIBRARIES . "/contentmap");

$params=$this->Params;
?>

function init_<?php echo $owner; ?>_<?php echo $id; ?>()
{

	if (!data_<?php echo $owner; ?>_<?php echo $id; ?>.places.length)
	{
		// There is no places viewable in this module
		document.getElementById('contentmap_<?php echo $owner; ?>_<?php echo $id; ?>').innerHTML += '<?php echo str_replace("'", "\\'", JText::_("CONTENTMAP_TAGS_NO_DATA")); ?>';
		return;
	}
	
	

	/*
	// Map creation
	var map = new google.maps.Map(document.getElementById('contentmap_<?php echo $owner; ?>_<?php echo $id; ?>'),
	{
		zoom: <?php echo $this->Params->get("zoom", 0); ?>,
	});
	
	
	*/
		
	google.load('visualization', '1', {'callback':drawVisualization_<?php echo $owner; ?>_<?php echo $id; ?>, packages: ['geochart']});
}

function drawVisualization_<?php echo $owner; ?>_<?php echo $id; ?>() {

<?php
echo 'var enable_gradation=!('.json_encode(intval($params->get('geo_disableGradation',0))).');';
?>

	if (enable_gradation){
		var lista_citta=[[<?php echo json_encode($params->get('geo_resolution','countries')=='provinces'?'Province':'City'); ?>,<?php echo json_encode(JText::_('CONTENTMAP_GEOCHART_ELEMENTS'));?>]];
		
		for (var i = 0; i < data_<?php echo $owner; ?>_<?php echo $id; ?>.places.length; ++i)
		{
			
			lista_citta.push([data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].title,parseInt(data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].tag_count)]);

		}
	}else{
		var lista_citta=[[<?php echo json_encode($params->get('geo_resolution','countries')=='provinces'?'Province':'City'); ?>]];
		
		for (var i = 0; i < data_<?php echo $owner; ?>_<?php echo $id; ?>.places.length; ++i)
		{
			
			lista_citta.push([data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].title]);
		}
	}

	var data = google.visualization.arrayToDataTable(lista_citta);

<?php
	
	$options = new stdClass();	
	$options->displayMode = $params->get('geo_displayMode','regions');
	$options->region = $params->get('geo_region','world');
	$options->resolution = $params->get('geo_resolution','countries');
	$options->enableRegionInteractivity = $params->get('geo_enableRegionInteractivity', 1) ? true : false;
	$options->markerOpacity = (float) $params->get('geo_markerOpacity', 1.0);
	$options->colorAxis = new stdClass();
	$minValue = $params->get('geo_colorAxis_minValue', null);
	$maxValue = $params->get('geo_colorAxis_maxValue', null);
	if(is_null($minValue)) {
		$options->colorAxis->minValue = $minValue;
	}
	if(is_null($maxValue)) {
		$options->colorAxis->maxValue = $maxValue;
	}
	$options->colorAxis->colors = array($params->get('geo_colorAxis_fromColor', '#FFFFFF'), $params->get('geo_colorAxis_toColor', '#35A339'));
	$options->datalessRegionColor = $params->get('geo_datalessRegionColor', '#F5F5F5');
	$options->backgroundColor = $params->get('geo_backgroundColor', '#666');
	
	$options->legend='none';
?>
	var options = <?php echo json_encode($options);?>;
	
	var geochart = new google.visualization.GeoChart(document.getElementById('contentmap_<?php echo $owner; ?>_<?php echo $id; ?>'));
	google.visualization.events.addListener(geochart, 'select', function() {
		var selectionIdx = geochart.getSelection()[0].row;
		location.href=data_<?php echo $owner; ?>_<?php echo $id; ?>.places[selectionIdx].tag_link;
		
	});

	document.getElementById('contentmap_<?php echo $owner; ?>_<?php echo $id; ?>').className = "";
	geochart.draw(data, options);

}


function preload_<?php echo $owner; ?>_<?php echo $id; ?>()
{

}


