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

namespace OstArticleSearch\Bundle\SearchBundle;

use Enlight_Controller_Request_RequestHttp as Request;
use OstArticleSearch\Bundle\SearchBundle\Condition\HasPseudoPriceCondition;
use OstArticleSearch\Bundle\SearchBundle\Condition\SearchCondition;
use OstArticleSearch\Bundle\SearchBundle\Facet\HasPseudoPriceFacet;
use OstArticleSearch\Bundle\SearchBundle\Facet\SearchFacet;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\CriteriaRequestHandlerInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class CriteriaRequestHandler implements CriteriaRequestHandlerInterface
{
    /**
     * ...
     *
     * @var array
     */
    protected $configuration;

    /**
     * ...
     *
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        // set params
        $this->configuration = $configuration;
    }

    /**
     * ...
     *
     * @param Request              $request
     * @param Criteria             $criteria
     * @param ShopContextInterface $context
     */
    public function handleRequest(Request $request, Criteria $criteria, ShopContextInterface $context)
    {
        // are we filtering for pseudo price?
        if ($request->has('ostas_has_pseudo_price')) {
            // add the condition
            $criteria->addCondition(
                new HasPseudoPriceCondition()
            );
        }

        // pseudo price active?!
        if ((bool) $this->configuration['hasPseudoPrice']) {
            // add facet
            $criteria->addFacet(
                new HasPseudoPriceFacet()
            );
        }

        // are we filtering for pseudo price?
        if ($request->has('ostas_search')) {
            // add the condition
            $criteria->addCondition(
                new SearchCondition($request->get('ostas_search'))
            );
        }

        // pseudo price active?!
        if ((bool) $this->configuration['searchStatus']) {
            // add facet
            $criteria->addFacet(
                new SearchFacet()
            );
        }
    }
}
