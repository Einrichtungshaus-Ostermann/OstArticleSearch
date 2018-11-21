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

namespace OstArticleSearch\Services;

use OstArticleSearch\Bundle\SearchBundle\Condition as CustomCondition;
use OstArticleSearch\Bundle\SearchBundle\Facet as CustomFacet;
use Shopware\Bundle\SearchBundle\Condition as CoreCondition;
use Shopware\Bundle\SearchBundle\Criteria;
use Enlight_Controller_Request_Request as Request;
use Shopware\Components\DependencyInjection\Container;



class CriteriaFactory
{

    /**
     * ...
     *
     * @var array
     */

    protected $configuration;



    /**
     * DI container.
     *
     * @var Container
     */

    protected $container;





    /**
     * ...
     *
     * @param array   $configuration
     * @param Container   $container
     */

    public function __construct( array $configuration, Container $container )
    {
        // set params
        $this->configuration = $configuration;
        $this->container = $container;
    }





    /**
     * ...
     *
     * @param Request   $request
     *
     * @return Criteria
     */

    public function getListingCriteria( Request $request )
    {
        // get services
        /* @var $contextService \Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService */
        $contextService  = $this->container->get('shopware_storefront.context_service');

        /* @var $criteriaFactory \Shopware\Bundle\SearchBundle\StoreFrontCriteriaFactory */
        $criteriaFactory = $this->container->get('shopware_search.store_front_criteria_factory');

        // get product contenxt
        $context = $contextService->getProductContext();

        // create criteria
        $criteria = $criteriaFactory->createListingCriteria(
            $request,
            $context
        );

        // add plugin criteria
        $this->addCriteria( $criteria, $request );

        // return it
        return $criteria;
    }






    /**
     * ...
     *
     * @param Request   $request
     *
     * @return Criteria
     */

    public function getAjaxCountCriteria( Request $request )
    {
        // get services
        /* @var $contextService \Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService */
        $contextService  = $this->container->get('shopware_storefront.context_service');

        /* @var $criteriaFactory \Shopware\Bundle\SearchBundle\StoreFrontCriteriaFactory */
        $criteriaFactory = $this->container->get('shopware_search.store_front_criteria_factory');

        // get product contenxt
        $context = $contextService->getProductContext();

        // create criteria
        $criteria = $criteriaFactory->createAjaxCountCriteria(
            $request,
            $context
        );

        // add plugin criteria
        $this->addCriteria( $criteria, $request );

        // return it
        return $criteria;
    }



    /**
     * ...
     *
     * @param Criteria $criteria
     * @param Request $request
     *
     * @return void
     */

    private function addCriteria( Criteria $criteria, Request $request )
    {
        // get services
        /* @var $contextService \Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService */
        $contextService  = $this->container->get('shopware_storefront.context_service');

        // get shop context
        $shopContext = $contextService->getShopContext();



        // get main category id
        $categoryId = $shopContext->getShop()->getCategory()->getId();

        // add it as condition to show only products from this shop
        $criteria->addCondition(
            new CoreCondition\CategoryCondition( array( $categoryId ) )
        );





        // are we filtering categories?
        if ( $request->has( "ostas_category" ) )
            // add the condition
            $criteria->addCondition(
                new CustomCondition\CategoryCondition( $request )
            );

        // add our category facet
        $criteria->addFacet(
            new CustomFacet\CategoryFacet()
        );


        // remove free shipping facet
        $criteria->removeFacet( "shipping_free" );



        // default per page
        $criteria->limit(
            (integer) $this->configuration[ "listingLimit" ]
        );
    }



}



