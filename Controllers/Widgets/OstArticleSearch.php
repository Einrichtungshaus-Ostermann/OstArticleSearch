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
use Shopware\Bundle\SearchBundle\ProductSearchResult;
use Shopware\Components\Compatibility\LegacyStructConverter;
use Shopware\Components\Routing\RouterInterface;

class Shopware_Controllers_Widgets_OstArticleSearch extends Enlight_Controller_Action
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
    public function listingCountAction()
    {
        // get services
        /* @var $contextService \Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService */
        $contextService = $this->get('shopware_storefront.context_service');

        /* @var $productSearch \Shopware\Bundle\SearchBundle\ProductSearch */
        $productSearch = $this->get('shopware_search.product_search');

        /* @var $criteriaFactory CriteriaFactory */
        $criteriaFactory = $this->get('ost_article_search.criteria_factory');

        // get criteria
        $criteria = $criteriaFactory->getListingCriteria($this->Request());

        // generate facets depending on listing reload mode
        $criteria->setGeneratePartialFacets(
            $this->container->get('config')->get('listingMode') === 'filter_ajax_reload'
        );

        // do we need to load facets
        if (!$this->Request()->get('loadFacets')) {
            // remove them
            $criteria->resetFacets();
        }

        // get the products
        $result = $productSearch->search(
            $criteria,
            $contextService->getShopContext()
        );

        // do we need to load them?
        if (!$this->Request()->getParam('loadProducts')) {
            // just one for total count
            $criteria->limit(1);
        }

        // create view array
        $body = [
            'totalCount' => $result->getTotalCount(),
        ];

        // do we need to load facets?
        if ($this->Request()->getParam('loadFacets')) {
            // assign them
            $body['facets'] = array_values($result->getFacets());
        }

        // do we need to load the products?
        if ($this->Request()->getParam('loadProducts')) {
            // set them
            $body['listing'] = $this->fetchListing($result);
            $body['pagination'] = $this->fetchPagination($result);
        }

        // disable renderer
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        // set json response
        $this->Response()->setBody(json_encode($body));
        $this->Response()->setHeader('Content-type', 'application/json', true);
    }

    /**
     * ...
     *
     * @param ProductSearchResult $result
     *
     * @return string
     */
    private function fetchListing(ProductSearchResult $result)
    {
        // convert articles to legacy
        $articles = $this->convertArticlesResult($result);

        // assign request parameters to the view
        $this->View()->assign($this->Request()->getParams());

        // load theme config
        $this->loadThemeConfig();

        // assign our articles
        $this->View()->assign([
            'sArticles' => $articles,
            'pageIndex' => $this->Request()->getParam('sPage'),
            'sCategoryContent' => [
                'template' => "listing_" . Shopware()->Container()->get( "ost_article_search.configuration" )['listingTemplate'] . ".tpl"
            ]
        ]);

        // fetch the listing
        $listing = $this->View()->fetch('frontend/listing/listing_ajax.tpl');

        // and return it
        return $listing;
    }

    /**
     * ...
     *
     * @param ProductSearchResult $result
     *
     * @return string
     */
    private function fetchPagination(ProductSearchResult $result)
    {
        // get per page param
        $sPerPage = $this->Request()->getParam('sPerPage');

        // assign the view
        $this->View()->assign([
            'sPage'           => $this->Request()->getParam('sPage'),
            'pages'           => ceil($result->getTotalCount() / $sPerPage),
            'baseUrl'         => $this->Request()->getBaseUrl() . $this->Request()->getPathInfo(),
            'pageSizes'       => [(integer) Shopware()->Container()->get( "ost_article_search.configuration" )['listingLimit']],
            'shortParameters' => $this->container->get('query_alias_mapper')->getQueryAliases(),
            'limit'           => $sPerPage,
        ]);

        // return the pagination
        return $this->View()->fetch('frontend/listing/actions/action-pagination.tpl');
    }

    /**
     * ...
     */
    private function loadThemeConfig()
    {
        /* @var $inheritance \Shopware\Components\Theme\Inheritance */
        $inheritance = $this->container->get('theme_inheritance');

        /* @var \Shopware\Models\Shop\Shop $shop */
        $shop = $this->container->get('Shop');

        // build the config
        $config = $inheritance->buildConfig($shop->getTemplate(), $shop, false);

        // add every plugin dir
        $this->get('template')->addPluginsDir(
            $inheritance->getSmartyDirectories(
                $shop->getTemplate()
            )
        );

        // assign the theme to the view
        $this->View()->assign('theme', $config);
    }

    /**
     * @param ProductSearchResult $result
     *
     * @return array
     */
    private function convertArticlesResult(ProductSearchResult $result)
    {
        /* @var LegacyStructConverter $converter */
        $converter = $this->get('legacy_struct_converter');

        /* @var RouterInterface $router */
        $router = $this->get('router');

        // convert articles
        $articles = $converter->convertListProductStructList($result->getProducts());

        // valid?!
        if (empty($articles)) {
            // just return
            return $articles;
        }

        // get article every url
        $urls = array_map(
            function ($article) { return $article['linkDetails']; },
            $articles
        );

        // get every seo url for them
        $rewrite = $router->generateList($urls);

        // loop every article to set the seo url
        foreach ($articles as $key => $article) {
            // do we have a seo url for this article?
            if (!array_key_exists($key, $rewrite)) {
                // we dont
                continue;
            }

            // add the seo url
            $articles[$key]['linkDetails'] = $rewrite[$key];
        }

        // return the articles
        return $articles;
    }
}
