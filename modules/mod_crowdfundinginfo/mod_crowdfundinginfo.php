<?php
/**
 * @package      Crowdfunding
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('Prism.init');
jimport('Crowdfunding.init');

$moduleclassSfx = htmlspecialchars($params->get('moduleclass_sfx'));

$option = $app->input->get('option');
$view   = $app->input->get('view');

// If option is not 'com_crowdfunding' and view is not 'details',
// do not display anything.
if ((strcmp($option, 'com_crowdfunding') !== 0) or (strcmp($view, 'details') !== 0)) {
    echo JText::_('MOD_CROWDFUNDINGINFO_ERROR_INVALID_VIEW');
    return;
}

$projectId = $app->input->getInt('id');
if (!$projectId) {
    echo JText::_('MOD_CROWDFUNDINGINFO_ERROR_INVALID_PROJECT');
    return;
}

$componentParams = JComponentHelper::getParams('com_crowdfunding');
/** @var  $componentParams Joomla\Registry\Registry */

$container       = Prism\Container::getContainer();
/** @var  $container Joomla\DI\Container */

$containerHelper = new Crowdfunding\Container\Helper();

// Get Project object from the container.
$project     = $containerHelper->fetchProject($container, $projectId);

$money       = $containerHelper->fetchMoneyFormatter($container, $componentParams);
$goal        = $money->setAmount($project->getGoal())->formatCurrency();
$funded      = $money->setAmount($project->getFunded())->formatCurrency();

// Validate end date.
$dateValidator    = new Prism\Validator\Date($project->getFundingEnd());
$isValidEndDate   = $dateValidator->isValid();

require JModuleHelper::getLayoutPath('mod_crowdfundinginfo', $params->get('layout', 'default'));