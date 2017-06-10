<?php
/**
 * @package      Crowdfunding
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\String\StringHelper;

// no direct access
defined('_JEXEC') or die;

/**
 * Crowdfunding backing raw controller
 *
 * @package     Crowdfunding
 * @subpackage  Components
 */
class CrowdfundingControllerBacking extends JControllerLegacy
{
    use Crowdfunding\Helper\MoneyHelper;

    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return    CrowdfundingModelBacking    The model.
     * @since    1.5
     */
    public function getModel($name = 'Backing', $prefix = 'CrowdfundingModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Process custom step.
     */
    public function processStep()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $response = new Prism\Response\Json();

        $context  = strtolower($this->input->getCmd('context'));

        // Get params
        $params  = JComponentHelper::getParams('com_crowdfunding');
        /** @var  $params Joomla\Registry\Registry */

        $responseData = null;

        // Trigger the event of a plugin that authorize payment process.
        JPluginHelper::importPlugin('crowdfundingpayment');
        $dispatcher = JEventDispatcher::getInstance();
        $results    = $dispatcher->trigger('onPaymentProcessStep', array('com_crowdfunding.payment.process.'.$context, &$params));

        // Get the result, that comes from the plugin.
        if (is_array($results) and count($results) > 0) {
            foreach ($results as $result) {
                if ($result !== null and is_object($result)) {
                    $responseData = $result;
                    break;
                }
            }
        }

        if ($responseData !== null) {
            if (isset($responseData->message)) {
                $response->setText($responseData->message);
            }

            if ($responseData->success) {
                $response->success();
            } else {
                $response->failure();
            }

            echo $response;
        }

        $app->close();
    }
}
