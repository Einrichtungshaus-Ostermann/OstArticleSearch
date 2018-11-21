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

use OstArticleSearch\Bundle\SearchBundle\Condition\NewCondition;
use Shopware\Bundle\SearchBundleDBAL\ConditionHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilder;
use Shopware\Bundle\SearchBundle\ConditionInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;


class NewConditionHandler implements ConditionHandlerInterface
{

	/**
	 * ...
	 *
	 * @var array   $configuration
	 */

	private $configuration;



	/**
	 * ...
	 *
	 * @param array   $configuration
	 */

	public function __construct( array $configuration )
	{
		// set params
		$this->configuration = $configuration;
	}



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
		return ( $condition instanceof NewCondition );
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
	    // get new days parameter
        $days = (integer) $this->configuration[ "newConditionDays" ];

		// only new articles
		$query->andWhere( "product.datum BETWEEN DATE_SUB(NOW(), INTERVAL " . $days . " DAY) AND NOW()" );
	}

}


