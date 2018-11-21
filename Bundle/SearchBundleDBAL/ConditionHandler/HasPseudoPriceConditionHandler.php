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

use OstArticleSearch\Bundle\SearchBundle\Condition\HasPseudoPriceCondition;
use Shopware\Bundle\SearchBundle\ConditionInterface;
use Shopware\Bundle\SearchBundleDBAL\ConditionHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilder;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class HasPseudoPriceConditionHandler implements ConditionHandlerInterface
{
    /**
     * ...
     *
     * @param ConditionInterface $condition
     *
     * @return bool
     */
    public function supportsCondition(ConditionInterface $condition)
    {
        // return
        return  $condition instanceof HasPseudoPriceCondition;
    }

    /**
     * ...
     *
     * @param ConditionInterface   $condition
     * @param QueryBuilder         $query
     * @param ShopContextInterface $context
     */
    public function generateCondition(ConditionInterface $condition, QueryBuilder $query, ShopContextInterface $context)
    {
        // join special price
        $query->leftJoin(
            'product',
            's_articles_prices',
            'ostasHasPseudoPrice',
            "ostasHasPseudoPrice.articleID = product.id AND ostasHasPseudoPrice.pricegroup = 'EK' AND ostasHasPseudoPrice.to = 'beliebig'"
        );

        // set as condition
        $query->andWhere('( ( ostasHasPseudoPrice.id IS NOT NULL ) AND ( ostasHasPseudoPrice.pseudoprice > 0 ) )');
    }
}
