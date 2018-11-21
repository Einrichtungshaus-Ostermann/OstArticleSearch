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

use OstArticleSearch\Bundle\SearchBundle\Condition\CategoryCondition;
use Shopware\Bundle\SearchBundleDBAL\ConditionHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilder;
use Shopware\Bundle\SearchBundle\ConditionInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use Doctrine\DBAL\Connection;

class CategoryConditionHandler implements ConditionHandlerInterface
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
		return ( $condition instanceof CategoryCondition );
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
		// tell phpstorm we are using our condition
		/** @var $condition CategoryCondition */

		// join our category
		$query->innerJoin(
			"product",
			"s_articles_categories_ro",
			"ostasCategory",
			"ostasCategory.articleID = product.id AND ostasCategory.categoryID IN (:ostasCategory)"
		);

		// set parameter
		$query->setParameter(
			"ostasCategory",
			$condition->getSelectedValues(),
			Connection::PARAM_INT_ARRAY
		);
	}

}


