<?php
/**
 * @package      Crowdfunding\Data
 * @subpackage   Transformer\Chart
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Crowdfunding\Data\Transformer\Chart;

use League\Fractal\TransformerAbstract;
use Prism\Money\Money;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing a country.
 *
 * @package      Crowdfunding\Data
 * @subpackage   Transformer\Chart
 */
class DailyFunds extends TransformerAbstract
{
    protected $money;

    public function __construct(Money $money)
    {
        $this->money  = $money;
    }

    public function transform(array $data)
    {
        $data['formatted_amount'] = $this->money->setAmount($data['amount'])->formatCurrency();

        return $data;
    }
}
