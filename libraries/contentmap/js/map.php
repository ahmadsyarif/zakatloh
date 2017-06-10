<?php defined('_JEXEC') or die('Restricted access');
$owner = JRequest::getVar("owner", "", "GET");
$id = JRequest::getVar("id", "", "GET");
JFactory::getLanguage()->load("contentmap", JPATH_LIBRARIES . "/contentmap");
?>
imageObjs = new Array();
globaldata_<?php echo $owner; ?>_<?php echo $id; ?>={};

function init_<?php echo $owner; ?>_<?php echo $id; ?>()
{
	var g_category=[];
	var g_tags=[];
	var g_next_category_id=1;
	var g_next_tag_id=1;

	if (!data_<?php echo $owner; ?>_<?php echo $id; ?>.places.length)
	{
		// There is no places viewable in this module
		document.getElementById('contentmap_<?php echo $owner; ?>_<?php echo $id; ?>').innerHTML += '<?php echo str_replace("'", "\\'", JText::_("CONTENTMAP_NO_DATA")); ?>';
		return;
	}
	
	document.getElementById('contentmap_<?php echo $owner; ?>_<?php echo $id; ?>').className = "";

<?php if ($center = $this->Params->get("center", NULL)) {
	$coordinates = explode(",", $center);
	// Google map js needs them as two separate values (See constructor: google.maps.LatLng(lat, lon))
	$center = new stdClass();
	$center->latitude = floatval($coordinates[0]);
	$center->longitude = floatval($coordinates[1]);
 ?>
 	var center = new google.maps.LatLng(<?php echo $center->latitude; ?>, <?php echo $center->longitude; ?>);
<?php } else { ?>
	var center = new google.maps.LatLng(data_<?php echo $owner; ?>_<?php echo $id; ?>.places[0].latitude, data_<?php echo $owner; ?>_<?php echo $id; ?>.places[0].longitude);
<?php } ?>

	// Map creation
	var map = new google.maps.Map(document.getElementById('contentmap_<?php echo $owner; ?>_<?php echo $id; ?>'),
	{
		zoom: <?php echo $this->Params->get("zoom", 0); ?>,
		center: center,
		mapTypeId: google.maps.MapTypeId.<?php echo $this->Params->get("map_type", "ROADMAP"); ?>,
		scrollwheel: false,
		
		//scaleControl: false,
		//mapTypeControl: false,
		//navigationControl: false,
		streetViewControl:<?php echo $this->Params->get('hideStreetViewControl', 0)==0?'true':'false';?>,
		zoomControl: <?php echo $this->Params->get('hideZoomControl', 0)==0?'true':'false';?>
	});
	
	var map_json_style=[];
	
	<?php 
	$map_style_json =trim( $this->Params->get("map_style_json", ""));
	
	if (!empty($map_style_json)){
		echo 'map_json_style='.$map_style_json.';'."\n";
	}
	
	
	?>

	if (map_json_style==null){
		map_json_style=[];
	}
	
<?php if ($this->Params->get("hide_poi", 0)) { ?>
	
	map_json_style.push(
	{
		featureType: "poi",
		stylers: [
		  { visibility: "off" }
		]   
	  });
<?php } ?>	

	if (map_json_style.length>0){
		map.setOptions({styles: map_json_style});
	}
	
	//image overlay
	var overlay_options={
		imageindex: 1,
		imageurl: <?php echo json_encode(trim($this->Params->get('watermark_url', '')));?>,
		imageposition: <?php echo json_encode($this->Params->get('watermark_position', 'RIGHT_TOP'));?>
	};
	if (overlay_options.imageurl!=''){
		var controlDiv = document.createElement('div');
		controlDiv.setAttribute('class', 'contentmap_overlay_controlDiv');
		//if (overlay_options.imageindex) controlDiv.index = parseInt(overlay_options.imageindex);
		//else controlDiv.index = 1;
		var controlUI = document.createElement('div');
		controlUI.setAttribute('class', 'contentmap_overlay_controlBorderDiv');
		controlDiv.appendChild(controlUI);
		var controlIMG = document.createElement('img');
		controlIMG.setAttribute('src', overlay_options.imageurl);
		//controlIMG.setAttribute('height', overlay_options.imageheight);
		//controlIMG.setAttribute('width', overlay_options.imagewidth);
		controlUI.appendChild(controlIMG);
		map.controls[eval('google.maps.ControlPosition.' + overlay_options.imageposition.toUpperCase())].push(controlDiv)
	}
<?php if ($this->Params->get("show_weather", 0)==1) {
?>
	
var weatherLayer = new google.maps.weather.WeatherLayer({
    temperatureUnits: google.maps.weather.TemperatureUnit.CELSIUS
  });
  weatherLayer.setMap(map);
<?php } ?>
	
	var oms = new OverlappingMarkerSpiderfier(map);
	
	globaldata_<?php echo $owner; ?>_<?php echo $id; ?>.map=map;
	globaldata_<?php echo $owner; ?>_<?php echo $id; ?>.directionsService = new google.maps.DirectionsService();
	globaldata_<?php echo $owner; ?>_<?php echo $id; ?>.directionsDisplay = new google.maps.DirectionsRenderer();
	globaldata_<?php echo $owner; ?>_<?php echo $id; ?>.directionsDisplay.setMap(map);
	
	globaldata_<?php echo $owner; ?>_<?php echo $id; ?>.panorama = map.getStreetView();
	globaldata_<?php echo $owner; ?>_<?php echo $id; ?>.panorama.setPov(({
		heading: 265,
		pitch: 0
	  }));
	
	
	
<?php echo 'var kml_url='.json_encode(trim($this->Params->get("kml_url"))).';'; ?>
	if (kml_url!=''){
		var ctaLayer = new google.maps.KmlLayer({
	    	url: kml_url
	  	});
	  	ctaLayer.setMap(map);	
	}

<?php if (!$center) {
// Used only by the module which contains more than one marker but only when a center is not defined
?>
/*
	if (data_<?php echo $owner; ?>_<?php echo $id; ?>.places.length > 1)
	{
		// Automatic scale and center the map based on the marker points
		var bounds = new google.maps.LatLngBounds();
		var pmin = new google.maps.LatLng(data_<?php echo $owner; ?>_<?php echo $id; ?>.minlatitude, data_<?php echo $owner; ?>_<?php echo $id; ?>.minlongitude);
		var pmax = new google.maps.LatLng(data_<?php echo $owner; ?>_<?php echo $id; ?>.maxlatitude, data_<?php echo $owner; ?>_<?php echo $id; ?>.maxlongitude);
		bounds.extend(pmin);
		bounds.extend(pmax);
		map.fitBounds(bounds);
	}
*/
<?php } ?>

	// InfoWindow creation
	var infowindow = new google.maps.InfoWindow({maxWidth: <?php echo $this->Params->get("infowindow_width", "200"); ?>});

	// Markers creation
	var markers = [];
	var minlatitude = 90.0;
	var maxlatitude = -90.0;
	var minlongitude = 180.0;
	var maxlongitude = -180.0;

	var tags=[];
	for (var i = 0; i < data_<?php echo $owner; ?>_<?php echo $id; ?>.places.length; ++i)
	{
		if (typeof data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].tags!=="undefined"){
			for (var t=0;t<data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].tags.length;t++){
				var title=data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].tags[t].title;
				var lft=data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].tags[t].lft;
				if (typeof tags[title] === "undefined"){
					tags[title]=lft;
				}
			}
		}
	}
	
	var sortable = [];
	for (var k in tags){
		if (tags.hasOwnProperty(k)){
			sortable.push([k, tags[k]]);
	    }
	}
	sortable.sort(function(a, b) {return a[1] - b[1]})	;
	
<?php if ($this->Params->get("category_legend_filter", "0")==2 && $this->Params->get("show_deselect_all",0)==1) { ?>
	
	if (sortable.length>0){
		
			var deselect_all = document.createElement('a');
			deselect_all.className ="contentmap-checkcontainer";
			deselect_all.appendChild(document.createTextNode(<?php echo json_encode(JText::_("CONTENTMAP_DESELECT_ALL")); ?>));
			deselect_all.onclick=function(){
				var checkboxes = document.getElementById('contentmap_legend_tags_<?php echo $owner; ?>_<?php echo $id; ?>').getElementsByTagName("input");
				for (var ci = 0; ci<checkboxes.length; ci++){
					checkboxes[ci].checked = false;
					checkboxes[ci].onchange();
				}
				return false;
			};
		
			document.getElementById('contentmap_legend_tags_<?php echo $owner; ?>_<?php echo $id; ?>').appendChild(deselect_all);
			document.getElementById('contentmap_legend_tags_<?php echo $owner; ?>_<?php echo $id; ?>').appendChild(document.createTextNode(" "));
		
	}
<?php } ?>	
	
	for (var i = 0; i < sortable.length; ++i)
	{
		addTagsMarker_<?php echo $owner; ?>_<?php echo $id; ?>(sortable[i][0]);
	}
	
	//category sort category_lft
	var categories=[];
	for (var i = 0; i < data_<?php echo $owner; ?>_<?php echo $id; ?>.places.length; ++i)
	{
		var title=data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].category;
		var lft=data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].category_lft;
		if (typeof categories[title] === "undefined"){
			categories[title]=lft;
		}
	}
	var sortable = [];
	for (var k in categories){
		if (categories.hasOwnProperty(k)){
			sortable.push([k, categories[k]]);
	    }
	}
	sortable.sort(function(a, b) {return a[1] - b[1]})	;
	
<?php if ($this->Params->get("category_legend_filter", "0")==1 && $this->Params->get("show_deselect_all",0)==1) { ?>
	if (sortable.length>0){
		
			var deselect_all = document.createElement('a');
			deselect_all.className ="contentmap-checkcontainer";
			deselect_all.appendChild(document.createTextNode(<?php echo json_encode(JText::_("CONTENTMAP_DESELECT_ALL")); ?>));
			deselect_all.onclick=function(){
				var checkboxes = document.getElementById('contentmap_legend_<?php echo $owner; ?>_<?php echo $id; ?>').getElementsByTagName("input");
				for (var ci = 0; ci<checkboxes.length; ci++){
					checkboxes[ci].checked = false;
					checkboxes[ci].onchange();
				}
				return false;
			};
			document.getElementById('contentmap_legend_<?php echo $owner; ?>_<?php echo $id; ?>').appendChild(deselect_all);
			document.getElementById('contentmap_legend_<?php echo $owner; ?>_<?php echo $id; ?>').appendChild(document.createTextNode(" "));		
		
	}
<?php } ?>	
	
	for (var i = 0; i < sortable.length; ++i)
	{
		addCategoryMarker_<?php echo $owner; ?>_<?php echo $id; ?>(sortable[i][0]);
	}
	
	
	<?php if ($this->Params->get("infowindow_event", "click")!='never'){ ?>
	oms.addListener('click', function(marker, event) {
<?php if ($this->Params->get("markers_action") == "infowindow") { ?>
			// InfoWindow handling event
			infowindow.setContent(data_<?php echo $owner; ?>_<?php echo $id; ?>.places[marker.cmapdata.zIndex].html);
			infowindow.open(map, marker);
<?php } else { ?>
			// Redirect handling event
			location.href = data_<?php echo $owner; ?>_<?php echo $id; ?>.places[marker.cmapdata.zIndex].article_url;
<?php } ?>
	});
	<?php } /*chiusura != never*/?>
	
	
	for (var i = 0; i < data_<?php echo $owner; ?>_<?php echo $id; ?>.places.length; ++i)
	{
		// Compute bounds rectangle
		minlatitude = Math.min(data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].latitude, minlatitude);
		maxlatitude = Math.max(data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].latitude, maxlatitude);
		minlongitude = Math.min(data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].longitude, minlongitude);
		maxlongitude = Math.max(data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].longitude, maxlongitude);

		// Set marker position
		var pos = new google.maps.LatLng(data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].latitude, data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].longitude);
		//alert("lat "+data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].latitude+" long "+data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].longitude);
		// Marker creation
		
		var marker_tags=[];
		if (typeof data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].tags!=="undefined"){
			for (var t=0;t<data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].tags.length;t++){
				marker_tags[marker_tags.length]=data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].tags[t].title;
			}
		}
		
		var marker = new google.maps.Marker(
		{
			map: map,
			position: pos,
			title: data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].title,
			zIndex: i,
			cmapdata: {category:data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].category,zIndex:i,tags:marker_tags,is_visible:true}
		});

		// Custom marker icon if present
		if (data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].marker) {
		    marker.setIcon(data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].marker);
		} else {
		if ("icon" in data_<?php echo $owner; ?>_<?php echo $id; ?>)
		marker.setIcon(data_<?php echo $owner; ?>_<?php echo $id; ?>.icon);
		}
		<?php if ($this->Params->get("infowindow_event", "click")=='mouseover'){ ?>
		var f=function (marker){
			google.maps.event.addListener(marker,'mouseover',function(ev){ if( marker._omsData == undefined ){ google.maps.event.trigger(marker,'click'); }});
			};
			f(marker);
		<?php } ?>
		
		if (marker.cmapdata.category){
			addCategoryMarker_<?php echo $owner; ?>_<?php echo $id; ?>(marker.cmapdata.category);
		}
		if (marker.cmapdata.tags.length>0){
			for (var t=0;t<marker.cmapdata.tags.length;t++){
				addTagsMarker_<?php echo $owner; ?>_<?php echo $id; ?>(marker.cmapdata.tags[t]);
			}
		}
		oms.addMarker(marker);
		markers.push(marker);
	}

<?php if (!$center) {
// Set bounds rectangle
// Used only by the module which contains more than one marker but only when a center is not defined
?>
	if (data_<?php echo $owner; ?>_<?php echo $id; ?>.places.length > 1 && !data_<?php echo $owner; ?>_<?php echo $id; ?>.places[0].hasOwnProperty('center_this'))
	{
		// Automatic scale and center the map based on the marker points
		var bounds = new google.maps.LatLngBounds();
		var pmin = new google.maps.LatLng(minlatitude, minlongitude);
		var pmax = new google.maps.LatLng(maxlatitude, maxlongitude);
		bounds.extend(pmin);
		bounds.extend(pmax);
		map.fitBounds(bounds);
	}
<?php } ?>

<?php if ($this->Params->get("cluster", "1")) { ?>
	// Marker Cluster creation
	var markerCluster = new MarkerClusterer(map, markers,{maxZoom: 15});
<?php } ?>

<?php if($this->Params->get('streetView', 0)):?>
	var panoramaOptions = {
	position: center,
	scrollwheel: false,
	pov: {
		heading: 0,
		pitch: 0
		}
	};

	var panorama = new google.maps.StreetViewPanorama(document.getElementById("contentmap_plugin_streetview_<?php echo $id; ?>"), panoramaOptions);
	//map.setStreetView(panorama);
<?php endif; ?>

	function addCategoryMarker_<?php echo $owner; ?>_<?php echo $id; ?>(name){
		if (typeof g_category[name] === "undefined"){
			g_category[name]={};
			g_category[name].checked=true;
			g_category[name].num=-1;
			g_category[name].id=g_next_category_id;
			g_next_category_id+=1;
<?php if ($this->Params->get("category_legend_filter", "0")==1) { ?>
			var checkbox = document.createElement('input');
			checkbox.type="checkbox";
			checkbox.checked="checked";
			checkbox.className ="checkboxxx";
			
			checkbox.onchange=function(){
				g_category[name].checked=checkbox.checked;
				updateMarkersVisibility_<?php echo $owner; ?>_<?php echo $id; ?>();
			}
			
			var div = document.createElement('span');
			div.className ="contentmap-checkcontainer";

			var categoryname=document.createElement('span');
			categoryname.appendChild(document.createTextNode(name));
			
			var categorynumphotos=document.createElement('span');
			categorynumphotos.appendChild(document.createTextNode(' (0)'));
			categorynumphotos.setAttribute("id",'cmap_<?php echo $owner; ?>_<?php echo $id; ?>_category_num_photos_'+g_category[name].id);
			
			div.appendChild(checkbox);
			div.appendChild(categoryname);
			div.appendChild(categorynumphotos);
			
			document.getElementById('contentmap_legend_<?php echo $owner; ?>_<?php echo $id; ?>').appendChild(div);
			document.getElementById('contentmap_legend_<?php echo $owner; ?>_<?php echo $id; ?>').appendChild(document.createTextNode(" "));
<?php } ?>
			
		}
		g_category[name].num+=1;
<?php if ($this->Params->get("category_legend_filter", "0")==1) { ?>
		var num_p=document.getElementById('cmap_<?php echo $owner; ?>_<?php echo $id; ?>_category_num_photos_'+g_category[name].id);
		num_p.innerHTML='';
		num_p.appendChild(document.createTextNode(' ('+g_category[name].num+')'));
<?php } ?>
	}

	function updateMarkersVisibility_<?php echo $owner; ?>_<?php echo $id; ?>(){
       	for (var i=0;i<markers.length;i++){
			var category_visible=false;
			var tag_visible=false;
			var prev_visible=markers[i].cmapdata.is_visible;
<?php if ($this->Params->get("category_legend_filter", "0")==1) { ?>
			category_visible=g_category[markers[i].cmapdata.category].checked;
<?php } ?>			
<?php if ($this->Params->get("category_legend_filter", "0")==2) { ?>
			tag_visible=false;
			for (var t=0;t<markers[i].cmapdata.tags.length;t++){
				if (g_tags[markers[i].cmapdata.tags[t]].checked){
					tag_visible=true;
					break;
				}
			}
<?php } ?>			
			var next_visible=category_visible || tag_visible;
			if (next_visible!=prev_visible){
				//visualizzo o nascondo
				markers[i].cmapdata.is_visible=next_visible;
				markers[i].setVisible(next_visible);
<?php			if ($this->Params->get("cluster", "1")) { ?>
					if (next_visible){
						markerCluster.addMarker(markers[i]);
					}else{
						markerCluster.removeMarker(markers[i]);
					}
<?php			} ?>
				
			}
		}
		
	}
	
	function addTagsMarker_<?php echo $owner; ?>_<?php echo $id; ?>(name){
		if (typeof g_tags[name] === "undefined"){
			g_tags[name]={};
			g_tags[name].checked=true;
			g_tags[name].num=-1;
			g_tags[name].id=g_next_tag_id;
			g_next_tag_id+=1;
<?php if ($this->Params->get("category_legend_filter", "0")==2) { ?>
			var checkbox = document.createElement('input');
			checkbox.type="checkbox";
			checkbox.checked="checked";
			checkbox.className ="checkboxxx";
			
			checkbox.onchange=function(){
				g_tags[name].checked=checkbox.checked;
				updateMarkersVisibility_<?php echo $owner; ?>_<?php echo $id; ?>();
			}
			
			var div = document.createElement('span');
			div.className ="contentmap-checkcontainer";

			var tagname=document.createElement('span');
			tagname.appendChild(document.createTextNode(name));
			
			var tagnumphotos=document.createElement('span');
			tagnumphotos.appendChild(document.createTextNode(' (0)'));
			tagnumphotos.setAttribute("id",'cmap_<?php echo $owner; ?>_<?php echo $id; ?>_tag_num_photos_'+g_tags[name].id);
			
			div.appendChild(checkbox);
			div.appendChild(tagname);
			div.appendChild(tagnumphotos);
			
			document.getElementById('contentmap_legend_tags_<?php echo $owner; ?>_<?php echo $id; ?>').appendChild(div);
			document.getElementById('contentmap_legend_tags_<?php echo $owner; ?>_<?php echo $id; ?>').appendChild(document.createTextNode(" "));
<?php } ?>
			
		}
		g_tags[name].num+=1;
<?php if ($this->Params->get("category_legend_filter", "0")==2) { ?>
		var num_p=document.getElementById('cmap_<?php echo $owner; ?>_<?php echo $id; ?>_tag_num_photos_'+g_tags[name].id);
		num_p.innerHTML='';
		num_p.appendChild(document.createTextNode(' ('+g_tags[name].num+')'));
<?php } ?>
	}	
	
}


// Preload article images shown inside the infowindows
function preload_<?php echo $owner; ?>_<?php echo $id; ?>()
{
	for (var i = 0; i < data_<?php echo $owner; ?>_<?php echo $id; ?>.places.length; ++i)
	{
		if (data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].image)
		{
			imageObj = new Image();
			//imageObj.src = data_<?php echo $owner; ?>_<?php echo $id; ?>.baseurl + data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].image;
			imageObj.src = data_<?php echo $owner; ?>_<?php echo $id; ?>.places[i].image;
			imageObjs.push(imageObj);
		}
	}

}

function findDirFromAddr_<?php echo $owner; ?>_<?php echo $id; ?>(fromLatLong){
	newAdd = document.getElementById('contentmap_input_<?php echo $owner; ?>_<?php echo $id; ?>').value;
		
	if(newAdd == "")
		return false;
	
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode( {
		'address': newAdd
	}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			globaldata_<?php echo $owner; ?>_<?php echo $id; ?>.map.setCenter(results[0].geometry.location);
			createMarker_<?php echo $owner; ?>_<?php echo $id; ?>(results[0].geometry.location,newAdd);
			showDirections_<?php echo $owner; ?>_<?php echo $id; ?>(fromLatLong,newAdd);
		} else {
			alert("Geocode was not successful for the following reason: " + status);
		}
	});
	
	return false;
}

function createMarker_<?php echo $owner; ?>_<?php echo $id; ?>(latlng,address){
	var contentString = '<div class="noo-m-info">'+
	  '<h1>'+address+'</h1>';

	var infowindow = new google.maps.InfoWindow({
	  content: contentString,
	  maxWidth: 300
	});

	var marker = new google.maps.Marker({
	  position: latlng,
	  map: globaldata_<?php echo $owner; ?>_<?php echo $id; ?>.map,
	  title: address,
	  draggable: false,
	  animation: google.maps.Animation.DROP
	});


	google.maps.event.addListener(marker, 'click', function() {
	  infowindow.open(globaldata_<?php echo $owner; ?>_<?php echo $id; ?>.map,this);
	});

	if(infowindow){
	  setTimeout(function(){google.maps.event.trigger(marker, 'click')},2000);
	}
}

function showDirections_<?php echo $owner; ?>_<?php echo $id; ?>(formAdd,toAdd){
	var request = {
		  origin:formAdd,
		  destination:toAdd,
		  travelMode: google.maps.DirectionsTravelMode.DRIVING
	  };
	  
	globaldata_<?php echo $owner; ?>_<?php echo $id; ?>.directionsService.route(request, function(response, status) {
		if (status == google.maps.DirectionsStatus.OK) {
			globaldata_<?php echo $owner; ?>_<?php echo $id; ?>.directionsDisplay.setDirections(response);
		}
	  });
}

function toggleStreetView_<?php echo $owner; ?>_<?php echo $id; ?>(latitude,longitude){
	var Latlng = new google.maps.LatLng(latitude,longitude);
   globaldata_<?php echo $owner; ?>_<?php echo $id; ?>.panorama.setPosition(Latlng);
   var toggle = globaldata_<?php echo $owner; ?>_<?php echo $id; ?>.panorama.getVisible();
   if (toggle == false) {
	   globaldata_<?php echo $owner; ?>_<?php echo $id; ?>.panorama.setVisible(true);
   } else {
	   globaldata_<?php echo $owner; ?>_<?php echo $id; ?>.panorama.setVisible(false);
   }
}

//google.maps.event.addDomListener(window, 'load', init_<?php echo $owner; ?>_<?php echo $id; ?>);
//google.maps.event.addDomListener(window, 'load', preload_<?php echo $owner; ?>_<?php echo $id; ?>);
//window.onload = preload_<?php echo $owner; ?>_<?php echo $id; ?>;
//google.maps.event.addDomListener(document.getElementById("contentmap_<?php echo $owner; ?>_<?php echo $id; ?>"), 'mouseover', preload_<?php echo $owner; ?>_<?php echo $id; ?>);



