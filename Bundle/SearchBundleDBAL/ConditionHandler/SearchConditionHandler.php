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

use OstArticleSearch\Bundle\SearchBundle\Condition\SearchCondition;
use Shopware\Bundle\SearchBundle\Condition\SearchTermCondition;
use Shopware\Bundle\SearchBundle\ConditionInterface;
use Shopware\Bundle\SearchBundleDBAL\ConditionHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilder;
use Shopware\Bundle\SearchBundleDBAL\SearchTermQueryBuilderInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SearchConditionHandler implements ConditionHandlerInterface
{
    /**
     * ...
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * ...
     *
     * @var array
     */
    private $configuration;

    /**
     * ...
     *
     * @param ContainerInterface $container
     * @param array              $configuration
     */
    public function __construct(ContainerInterface $container, array $configuration)
    {
        // ...
        $this->container = $container;
        $this->configuration = $configuration;
    }

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
        return  $condition instanceof SearchCondition;
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
        /** @var $condition SearchCondition */

        // get the search value
        $value = $condition->getValue();

        // do we have an empty search value?
        if (empty($value)) {
            // nothing to do
            return;
        }

        // do we want the default search?
        if ((bool) $this->configuration['shopwareSearchStatus'] === false) {
            // join supplier for the name
            $query->leftJoin(
                'product',
                's_articles_supplier',
                'ostasSearchSupplier',
                'ostasSearchSupplier.id = product.supplierID'
            );

            // join filter
            $query->leftJoin(
                'product',
                's_filter_articles',
                'ostasFilterArticles',
                'ostasFilterArticles.articleID = product.id'
            );

            // join filter values
            $query->leftJoin(
                'ostasFilterArticles',
                's_filter_values',
                'ostasFilterValues',
                'ostasFilterValues.id = ostasFilterArticles.valueID'
            );

            // we need the search string split by whitespace
            $split = explode(' ', $value);

            // unique alias
            $i = 0;

            // loop every single search term
            foreach ($split as $aktu) {
                // unique alias
                $param = 'ostasSearch_' . $i;

                // every search paramter
                $params = [
                    '( product.name LIKE :' . $param . ' )',
                    '( product.description LIKE :' . $param . ' )',
                    '( variant.ordernumber LIKE :' . $param . ' )',
                    '( ostasSearchSupplier.name LIKE :' . $param . ' )',
                    '( ostasFilterValues.value LIKE :' . $param . ' )'
                ];

                // combined with OR and every single search tearm combined with AND
                $query->andWhere('( ' . implode(' OR ', $params) . ' )');

                // set parameter for this term
                $query->setParameter($param, '%' . $aktu . '%');

                // next unique alias
                ++$i;
            }

            // and stop here
            return;
        }

        // get the shopware search query builder
        /* @var $searchTermQueryBuilder SearchTermQueryBuilderInterface */
        $searchTermQueryBuilder = $this->container->get('shopware_searchdbal.search_query_builder_dbal');

        /* @var SearchTermCondition $condition */
        $searchQuery = $searchTermQueryBuilder->buildQuery(
            $value
        );

        // no matching products found by the search query builder
        if ($searchQuery === null) {
            // add condition that the result contains no product
            $query->andWhere('0 = 1');

            // stop here
            return;
        }

        // get the search sub query
        $queryString = $searchQuery->getSQL();

        // add search select and state
        $query->addSelect('searchTable.*');
        $query->addState('ranking');

        // join the search
        $query->innerJoin(
            'product',
            '(' . $queryString . ')',
            'searchTable',
            'searchTable.product_id = product.id'
        );
    }
}
