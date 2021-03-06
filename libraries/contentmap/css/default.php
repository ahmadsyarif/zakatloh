<?php defined('_JEXEC') or die('Restricted access');
/*
Do not edit this file or it will be overwritten at the first upgrade
Copy from this source using another file name and select your own new created css in the module or plugin options
*/
$owner = JRequest::getVar("owner", "", "GET");
$id = JRequest::getVar("id", "", "GET");
?>
#contentmap_wrapper_plugin_<?php echo $id ?> img{max-width:none}

<?php
	$position=trim(substr($this->Params->get('position', 'AC').' ',2,1));
?>

#contentmap_wrapper_<?php echo $owner; ?>_<?php echo $id; ?>
{
<?php
	if (!empty($position)){
		$map_width_unit=$this->Params->get("map_width_unit", "%");
		$map_width=$this->Params->get("map_width", "100");
		if ($map_width_unit=='%'){
			$map_width_unit='px';
			$map_width='300';
		}
		echo 'width: '.$map_width.$map_width_unit.';';
	}else{
?>	
		width: <?php echo $this->Params->get("map_width", "100"); ?><?php echo $this->Params->get("map_width_unit", "%"); ?>;
		margin: auto; /*Horizontally center the map*/
		clear: both; /*Avoid overlapping with joomla article image, but it can create problems with some templates*/
<?php } ?>
}

#contentmap_wrapper_plugin_<?php echo $id; ?>
{
<?php
	if (!empty($position)){
		echo 'float: '.($position=='L'?'left':'right').';';
		
		$fullposition=$this->Params->get('position', 'AC');
		if ($fullposition=='BCR'){
			echo 'margin: 3px 0px 6px 24px;';
		}else if ($fullposition=='BCL'){
			echo 'margin: 3px 24px 6px 0px;';
		}
	}
?>
}

#contentmap_container_<?php echo $owner; ?>_<?php echo $id; ?>
{
	padding: 6px; /*inner spacer*/
	margin: 24px 1px 0px 1px; /*outer spacer*/
	border-width: 1px;
	border-style: solid;
	border-color: #ccc #ccc #999 #ccc;
	-webkit-box-shadow: 0 2px 5px rgba(64, 64, 64, 0.5);
	-moz-box-shadow: 0 2px 5px rgba(64, 64, 64, 0.5);
	box-shadow: 0 2px 5px rgba(64, 64, 64, 0.5);
}
#contentmap_container_module_<?php echo $id; ?>
{
	margin-top:0px;
}
#contentmap_container_plugin_<?php echo $id; ?>
{
<?php
	if (!empty($position)){
		echo 'margin: 0;';
	}
?>
}


#contentmap_<?php echo $owner; ?>_<?php echo $id; ?>
{
<?php if ($this->Params->get("data_source", "0")!='joomlatags'){?>
	height: <?php echo $this->Params->get("map_height", "400"); ?>px;
<?php }?>	
	color: #505050;
}

#contentmap_<?php echo $owner; ?>_<?php echo $id; ?> a, #contentmap_<?php echo $owner; ?>_<?php echo $id; ?> a:hover, #contentmap_<?php echo $owner; ?>_<?php echo $id; ?> a:visited
{
	color: #0055ff;
}

/* Prevent max-width styles*/
#contentmap_<?php echo $owner; ?>_<?php echo $id; ?> img
{
    max-width:none;
}

/* Article image inside the balloon */
.intro_image
{
	margin: 8px;
}

/* Author alias inside the balloon */
.created_by_alias
{
	font-size: 0.8em;
	font-style:italic;
}

/* Creation date inside the balloon */
.created
{
	font-size: 0.8em;
	font-style:italic;
}


.contentmap-checkcontainer {
    margin-right: 30px;
    white-space: nowrap;
}
.contentmap-checkcontainer > span {
}

.contentmap-checkcontainer > .checkboxxx {
    margin: 0;
    margin-right:5px;
}
#contentmap_legend_<?php echo $owner; ?>_<?php echo $id; ?>{
    margin-top: 5px;
}


.contentmap-m {
}
.contentmap-m img {
    max-width: none;
}
.contentmap-m-actbar-list {
    border-top: 1px solid #CCCCCC;
    margin-top: 10px;
    padding-top: 5px;
}
.contentmap-m-info {
}
.contentmap-m-info h1 {
}
.contentmap-m-act {
}
.contentmap-m-act a {
    cursor: pointer;
    display: inline-block;
    margin: 5px;
}
.dirform-inner {
}

.contentmap_loading{
	background: url(<?php echo json_encode(JURI::root()."media/contentmap/images/ajax-loader.gif"); ?>) no-repeat scroll 50% 50%; 
}

#contentmap_container_<?php echo $owner; ?>_<?php echo $id; ?> img
{
    max-width:none !important;
}

.contentmap_lengend_title{
	font-weight: bold;
}

.contentmap_overlay_controlDiv {
    padding: 5px;
}