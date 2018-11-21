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

namespace OstArticleSearch\Bundle\SearchBundle\Facet;

class SearchFacet implements \Shopware\Bundle\SearchBundle\FacetInterface
{
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
}
