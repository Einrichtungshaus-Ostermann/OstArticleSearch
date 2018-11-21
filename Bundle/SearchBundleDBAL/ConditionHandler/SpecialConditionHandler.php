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

namespace OstArticleSearch\Bundle\SearchBundleDBAL\ConditionHandler;

use OstArticleSearch\Bundle\SearchBundle\Condition\SpecialCondition;
use Shopware\Bundle\SearchBundleDBAL\ConditionHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilder;
use Shopware\Bundle\SearchBundle\ConditionInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class SpecialConditionHandler implements ConditionHandlerInterface
{

	/**
	 * ...
	 *
	 * @param ConditionInterface   $condition
	 *
	 * @return boolean
	 */

	public function supportsCondition( ConditionInterface $condition )
	{
		// return
		return ( $condition instanceof SpecialCondition );
	}



	/**
	 * ...
	 *
	 * @param ConditionInterface     $condition
	 * @param QueryBuilder           $query
	 * @param ShopContextInterface   $context
	 *
	 * @return void
	 */

	public function generateCondition( ConditionInterface $condition, QueryBuilder $query, ShopContextInterface $context )
	{
		// join special price
		$query->leftJoin(
			"product",
			"s_articles_prices",
			"ostasSpecialPrice",
			"ostasSpecialPrice.articleID = product.id AND ostasSpecialPrice.pricegroup = 'EK' AND ostasSpecialPrice.to = 'beliebig'" )
		;

		// set as condition
		$query->andWhere( "( ( ostasSpecialPrice.id IS NOT NULL ) AND ( ostasSpecialPrice.pseudoprice > 0 ) )" );
	}

}


