<?php declare(strict_types=1);

/*
 * Einrichtungshaus Ostermann GmbH & Co. KG - Article Search
 *
 * @package   OstArticleSearch
 *
 * @author    Eike Brandt-Warneke <e.brandt-warneke@ostermann.de>
 * @copyright 2018 Einrichtungshaus Ostermann GmbH & Co. KG
 * @license   proprietary
 */

use OstArticleSearch\Services\CriteriaFactory;

class Shopware_Controllers_Frontend_OstArticleSearch extends Enlight_Controller_Action
{
    /**
     * ...
     *
     * @throws Exception
     */
    public function preDispatch()
    {
        // ...
        $viewDir = $this->container->getParameter('ost_article_search.view_dir');
        $this->get('template')->addTemplateDir($viewDir);
        parent::preDispatch();
    }

    /**
     * ...
     *
     * @return array
     */
    public function getWhitelistedCSRFActions()
    {
        // return all actions
        return array_values(array_filter(
            array_map(
                function ($method) { return (substr($method, -6) === 'Action') ? substr($method, 0, -6) : null; },
                get_class_methods($this)
            ),
            function ($method) { return  !in_array((string) $method, ['', 'index', 'load', 'extends'], true); }
        ));
    }

    /**
     * ...
     *
     * @throws Exception
     */
    public function indexAction()
    {
        // get services
        /* @var $contextService \Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService */
        $contextService = $this->get('shopware_storefront.context_service');

        /* @var $productSearch \Shopware\Bundle\SearchBundle\ProductSearch */
        $productSearch = $this->get('shopware_search.product_search');

        /* @var $structConverter \Shopware\Components\Compatibility\LegacyStructConverter */
        $structConverter = $this->get('legacy_struct_converter');

        // get type and current sorting to set default sorting
        $sort = $this->Request()->getParam('sSort');

        // default sorting for different types
        if ($sort === null) {
            $sort = 5;
        }

        // set the default settings
        $this->Request()->setParam('sSort', $sort);

        // get criteria factory
        /* @var $criteriaFactory CriteriaFactory */
        $criteriaFactory = $this->get('ost_article_search.criteria_factory');

        // get criteria
        $criteria = $criteriaFactory->getListingCriteria($this->Request());

        // get products
        $result = $productSearch->search(
            $criteria,
            $contextService->getProductContext()
        );

        // convert them
        $products = $structConverter->convertListProductStructList(
            $result->getProducts()
        );

        // get request parameters
        $sort = $this->Request()->getParam('sSort');
        $page = $this->Request()->getParam('sPage', 1);

        /** @var \Shopware\Bundle\StoreFrontBundle\Service\CustomSortingServiceInterface $service */
        $service = $this->get('shopware_storefront.custom_sorting_service');

        // get default sorting
        $sortings = $service->getAllCategorySortings($contextService->getShopContext());

        // assign the view
        $this->View()->assign([
            // default data
            'sBanner'            => [],
            'sBreadcrumb'        => [],
            'emotions'           => [],
            'hasEmotion'         => false,
            'showListing'        => true,
            'showListingDevices' => [0, 1, 2, 3, 4],
            'isHomePage'         => false,
            'criteria'           => $criteria,
            'facets'             => $result->getFacets(),
            'sPage'              => $page,
            'pageIndex'          => $page,
            'pageSizes'          => [(integer) Shopware()->Container()->get( "ost_article_search.configuration" )['listingLimit']],
            'sPerPage'           => (integer) Shopware()->Container()->get( "ost_article_search.configuration" )['listingLimit'],
            'sTemplate'          => null,
            'sortings'           => $sortings,
            'sNumberArticles'    => $result->getTotalCount(),
            'sArticles'          => $products,
            'shortParameters'    => $this->get('query_alias_mapper')->getQueryAliases(),
            'sSort'              => $sort,

            // additional parameters
            'ostArticleSearchStatus' => 'true',
            'ostArticleSearchSeoTitle' => 'Artikel Suche',
            'ostArticleSearchBoxTemplate' => Shopware()->Container()->get( "ost_article_search.configuration" )['listingTemplate'],

            // infinite scrolling wont work without category id and &c= will be transmitted to ajaxListing() and automatically set as criteria
            'sCategoryContent' => [
                'id' => $contextService->getShopContext()->getShop()->getCategory()->getId(),
                'template' => "listing_" . Shopware()->Container()->get( "ost_article_search.configuration" )['listingTemplate'] . ".tpl",
                'productBoxLayout' => "listing_" . Shopware()->Container()->get( "ost_article_search.configuration" )['listingTemplate'] . ".tpl"
            ],
        ]);
    }

    /**
     * ...
     */
    public function searchAction()
    {
    }
}
