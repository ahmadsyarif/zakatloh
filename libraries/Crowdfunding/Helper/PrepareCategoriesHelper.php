<?php
/**
 * @package      Crowdfunding
 * @subpackage   Helpers
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Crowdfunding\Helper;

use Prism\Helper\HelperInterface;
use Prism\Constants;
use Prism\Utilities;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Crowdfunding;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality to prepare the category items.
 *
 * @package      Crowdfunding
 * @subpackage   Helpers
 */
final class PrepareCategoriesHelper implements HelperInterface
{
    /**
     * Prepare category items.
     *
     * @param array $data
     * @param array $options
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function handle(&$data, array $options = array())
    {
        // Count projects.
        $projectNumber = array();
        if (array_key_exists('count_projects', $options) and (bool)$options['count_projects']) {
            $ids         = Utilities\ArrayHelper::getIds($data);

            $categories  = new Crowdfunding\Category\Categories();
            $categories->setDb(\JFactory::getDbo());

            $projectState  = ArrayHelper::getValue($options, 'project_state', array(), 'array');
            $projectNumber = $categories->getProjectsNumber($ids, $projectState);
        }

        foreach ($data as $key => $item) {
            // Decode parameters
            if ($item->params !== null and $item->params !== '') {
                $params = new Registry();
                $params->loadString($item->params);

                $item->params = $params;

                // Generate a link to the picture.
                if ($item->params->get('image')) {
                    $item->image_link = \JUri::base().$item->params->get('image');
                }
            }

            // Set project number.
            $item->project_number = array_key_exists($item->id, $projectNumber) ? (int)$projectNumber[$item->id] : 0;
        }
    }
}
