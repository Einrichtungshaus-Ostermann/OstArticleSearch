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

namespace OstArticleSearch\Bundle\SearchBundle\FacetResult;

use Shopware\Bundle\SearchBundle\FacetResultInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Attribute;
use Shopware\Bundle\StoreFrontBundle\Struct\Extendable;

class SearchFacetResult extends Extendable implements FacetResultInterface
{
    /**
     * ...
     *
     * @return string
     */
    private $facetName;

    /**
     * ...
     *
     * @return string
     */
    private $fieldName;

    /**
     * ...
     *
     * @return string
     */
    private $value;

    /**
     * ...
     *
     * @return string
     */
    private $label;

    /**
     * ...
     *
     * @return string
     */
    private $template = null;

    /**
     * ...
     *
     * @param string      $facetName
     * @param string      $fieldName
     * @param string      $value
     * @param string      $label
     * @param Attribute[] $attributes
     * @param string      $template
     */
    public function __construct($facetName, $fieldName, $value, $label, $attributes = [], $template = 'frontend/listing/filter/facet-search.tpl')
    {
        // ...
        $this->facetName = $facetName;
        $this->fieldName = $fieldName;
        $this->value = $value;
        $this->label = $label;
        $this->attributes = $attributes;
        $this->template = $template;
    }

    /**
     * ...
     *
     * @return string
     */
    public function getFacetName()
    {
        return $this->facetName;
    }

    /**
     * ...
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * ...
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * ...
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * ...
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * ...
     *
     * @return string
     */
    public function isActive()
    {
        return true;
    }
}
