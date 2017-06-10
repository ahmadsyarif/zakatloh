<?php
defined('_JEXEC') or die();

jimport('joomla.form.formfield');

class JFormFieldMarkerpreview extends JFormField
{
	public $type = 'Markerpreview';

	protected function getInput()
	{
		$jlang = JFactory::getLanguage();
		$jlang->load('contentmap', JPATH_LIBRARIES.'/contentmap', 'en-GB', true);
		$jlang->load('contentmap', JPATH_LIBRARIES.'/contentmap', $jlang->getDefault(), true);

		$html   = '<div style="float:left">';
		$params = $this->form->getValue('params');
		$marker = isset($params->markers_icon) ? $params->markers_icon : '';

		// mhm.. no luck with plugin. Maybe I'm inside the article?
		if(!$marker)
		{
			$params = $this->form->getValue('metadata');
			$marker = isset($params->marker) ? $params->marker : '';
		}

		// Nothing, let's show the default one
		if(!$marker)
		{
			$marker = 'default.png';
		}

		$html .= '<img src="'.JUri::root().'media/contentmap/markers/icons/'.$marker.'" />';

		$html .= '<br/><small>'.JText::_('CONTENTMAP_PLEASE_SAVE').'</small>';
		$html .= '</div>';

		return $html;
	}
}
