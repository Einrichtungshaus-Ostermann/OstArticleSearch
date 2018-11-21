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

use OstArticleSearch\Bundle\SearchBundle\Facet\CategoryFacet;
use OstArticleSearch\Bundle\SearchBundle\Condition\CategoryCondition;
use Shopware\Bundle\SearchBundleDBAL\PartialFacetHandlerInterface;
use Shopware\Bundle\SearchBundle\FacetInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilderFactory;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\Bundle\SearchBundle\FacetResult\ValueListFacetResult;
use Shopware\Bundle\SearchBundle\FacetResult\ValueListItem;
use Shopware\Bundle\SearchBundle\FacetResultInterface;



class CategoryFacetHandler implements PartialFacetHandlerInterface
{

	/**
	 * ...
	 *
	 * @var QueryBuilderFactory
	 */

	private $queryBuilderFactory;



	/**
	 * ...
	 *
	 * @var $snippet \Enlight_Components_Snippet_Namespace
	 */

	private $snippet;



	/**
	 * ...
	 *
	 * @param QueryBuilderFactory                    $queryBuilderFactory
	 * @param \Shopware_Components_Snippet_Manager   $snippetManager
	 */

	public function __construct( QueryBuilderFactory $queryBuilderFactory, \Shopware_Components_Snippet_Manager $snippetManager )
	{
		// set params
		$this->queryBuilderFactory = $queryBuilderFactory;
		$this->snippet             = $snippetManager->getNamespace( "frontend/ost-article-search/facets" );
	}



	/**
	 * ...
	 *
	 * @param FacetInterface $facet
	 *
	 * @return boolean
	 */

	public function supportsFacet(FacetInterface $facet)
	{
		// return
		return ( $facet instanceof CategoryFacet );
	}



    /**
     * ...
     *
     * @param FacetInterface                $facet
     * @param Criteria                      $reverted
     * @param Criteria                      $criteria
     * @param Struct\ShopContextInterface   $context
     *
     * @return FacetResultInterface
     */

    public function generatePartialFacet( FacetInterface $facet, Criteria $reverted, Criteria $criteria, Struct\ShopContextInterface $context )
	{
        // get the category id by shop id - either automatic or manual for alphacool because "shop" is a sub category
        $categoryId = $context->getShop()->getCategory()->getId();



		// we need the root to find the children
		/* @var $root \Shopware\Models\Category\Category */
		$root = Shopware()->Models()->find( '\Shopware\Models\Category\Category', $categoryId );

		// get all categories
		/* @var $categories \Shopware\Models\Category\Category[] */
		$categories = Shopware()->Models()
			->getRepository( '\Shopware\Models\Category\Category' )
			->findBy( array( 'parent' => $root, 'active' => true, 'blog' => false ) );



		// get our condition
		/* @var $condition CategoryCondition */
		$condition = $criteria->getCondition( "ostas_category" );

		// get selected values
		$selectedValues = ( $condition instanceof CategoryCondition )
			? $condition->getSelectedValues()
			: array();



		// our list items
		$listItems = array();

		// loop the categories
		foreach ( $categories as $category )
		{
			// create a new list item
			$item = new ValueListItem(
				$category->getId(),
				$category->getName(),
				in_array( $category->getId(), $selectedValues )
			);

			// add it
			array_push( $listItems, $item );
		}



		// return the value list
		return new ValueListFacetResult(
			$facet->getName(),
			$criteria->hasCondition( $facet->getName() ),
			$this->snippet->get( "category", "Kategorie" ),
			$listItems,
			$facet->getName()
		);
	}

}


