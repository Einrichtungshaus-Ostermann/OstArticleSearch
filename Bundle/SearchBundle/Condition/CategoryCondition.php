<?php declare(strict_types=1);

/**
 * Einrichtungshaus Ostermann GmbH & Co. KG - Article Search
 *
 * @package   OstArticleSearch
 *
 * @author    Eike Brandt-Warneke <e.brandt-warneke@ostermann.de>
 * @copyright 2018 Einrichtungshaus Ostermann GmbH & Co. KG
 * @license   proprietary
 */

namespace OstArticleSearch\Bundle\SearchBundle\Condition;

use Enlight_Controller_Request_Request as Request;
use Shopware\Bundle\SearchBundle\ConditionInterface;

class CategoryCondition implements ConditionInterface
{
    /**
     * ...
     *
     * @var Request
     */
    private $request;

    /**
     * ...
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * ...
     *
     * @return string
     */
    public function getName()
    {
        // return name
        return 'ostas_category';
    }

    /**
     * ...
     *
     * @return array
     */
    public function getSelectedValues()
    {
        // did we even select this filter?
        if (!$this->request->has($this->getName())) {
            // we didnt
            return [];
        }

        // get all ids from the request
        $categories = explode(
            '|',
            $this->request->getParam($this->getName())
        );

        // return them
        return $categories;
    }
}
