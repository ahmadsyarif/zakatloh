<?php
/**
 * @package      Crowdfunding
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * These class contains methods using for upgrading the extension
 */
class CrowdfundingInstallHelper
{
    public static function startTable()
    {
        echo '
        <div style="width: 600px;">
        <table class="table table-bordered table-striped">';
    }

    public static function endTable()
    {
        echo '</table></div>';
    }

    public static function addRowHeading($heading)
    {
        echo '
	    <tr class="info">
            <td colspan="3">' . $heading . '</td>
        </tr>';
    }

    /**
     * Display an HTML code for a row
     *
     * <code>
     * $result =  array(
     *    type => success, important, warning,
     *    text => yes, no, off, on, warning,...
     * );
     * </code>
     *
     * @param string $title
     * @param array  $result
     * @param string $info
     *
     * @throws \InvalidArgumentException
     */
    public static function addRow($title, $result, $info)
    {
        $outputType = Joomla\Utilities\ArrayHelper::getValue($result, 'type');
        $outputText = Joomla\Utilities\ArrayHelper::getValue($result, 'text');

        $output = '';
        if ($outputType and $outputText) {
            $output = '<span class="label label-' . $outputType . '">' . $outputText . '</span>';
        }

        echo '
	    <tr>
            <td>' . $title  . '</td>
            <td>' . $output .'</td>
            <td>' . $info  .'</td>
        </tr>';
    }

    public static function createFolder($imagesPath)
    {
        // Create image folder
        if (true !== JFolder::create($imagesPath)) {
            JLog::add(JText::sprintf('COM_CROWDFUNDING_ERROR_CANNOT_CREATE_FOLDER', $imagesPath));
        } else {
            // Copy index.html
            $indexFile = $imagesPath . DIRECTORY_SEPARATOR . 'index.html';
            $html      = '<html><body style="background-color: #fff;"></body></html>';
            if (true !== JFile::write($indexFile, $html)) {
                JLog::add(JText::sprintf('COM_CROWDFUNDING_ERROR_CANNOT_SAVE_FILE', $indexFile));
            }
        }
    }
}