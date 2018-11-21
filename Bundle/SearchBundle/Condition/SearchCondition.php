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

class SearchCondition implements \Shopware\Bundle\SearchBundle\ConditionInterface
{
    /**
     * ...
     *
     * @var string
     */
    private $value;

    /**
     * ...
     *
     * @param string $value
     */
    public function __construct($value)
    {
        // ...
        $this->value = trim($value);
    }

    /**
     * ...
     *
     * @return string
     */
    public function getName()
    {
        // return name
        return 'ostas_search';
    }

    /**
     * ...
     *
     * @return string
     */
    public function getValue()
    {
        // return name
        return $this->value;
    }
}
