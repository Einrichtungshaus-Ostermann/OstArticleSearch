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

namespace OstArticleSearch\Bundle\SearchBundleDBAL\FacetHandler;

use Enlight_Components_Snippet_Namespace as SnippetNamespace;
use OstArticleSearch\Bundle\SearchBundle\Facet\HasPseudoPriceFacet;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\FacetInterface;
use Shopware\Bundle\SearchBundle\FacetResult\BooleanFacetResult;
use Shopware\Bundle\SearchBundle\FacetResultInterface;
use Shopware\Bundle\SearchBundleDBAL\PartialFacetHandlerInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware_Components_Snippet_Manager as SnippetManager;

class HasPseudoPriceFacetHandler implements PartialFacetHandlerInterface
{
    /**
     * ...
     *
     * @var SnippetNamespace
     */
    private $snippet;

    /**
     * ...
     *
     * @param SnippetManager $snippetManager
     */
    public function __construct(SnippetManager $snippetManager)
    {
        // set params
        $this->snippet = $snippetManager->getNamespace('frontend/ost-article-search/facets');
    }

    /**
     * ...
     *
     * @param FacetInterface $facet
     *
     * @return bool
     */
    public function supportsFacet(FacetInterface $facet)
    {
        // return
        return  $facet instanceof HasPseudoPriceFacet;
    }

    /**
     * ...
     *
     * @param FacetInterface              $facet
     * @param Criteria                    $reverted
     * @param Criteria                    $criteria
     * @param Struct\ShopContextInterface $context
     *
     * @return FacetResultInterface
     */
    public function generatePartialFacet(FacetInterface $facet, Criteria $reverted, Criteria $criteria, Struct\ShopContextInterface $context)
    {
        // return the facet
        return new BooleanFacetResult(
            $facet->getName(),
            $facet->getName(),
            $criteria->hasCondition($facet->getName()),
            $this->snippet->get('has_pseudo_price', 'Sonderangebot', true)
        );
    }
}
