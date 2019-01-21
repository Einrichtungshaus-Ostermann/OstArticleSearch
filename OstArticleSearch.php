<?php declare(strict_types=1);

/**
 * Einrichtungshaus Ostermann GmbH & Co. KG - Article Search
 *
 * Article Search
 *
 * 1.0.0
 * - initial release
 *
 * 1.0.1
 * - fixed plugin name
 *
 * 1.0.2
 * - beautified search form
 *
 * @package   OstArticleSearch
 *
 * @author    Eike Brandt-Warneke <e.brandt-warneke@ostermann.de>
 * @copyright 2018 Einrichtungshaus Ostermann GmbH & Co. KG
 * @license   proprietary
 */

namespace OstArticleSearch;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Enlight_Event_EventArgs as EventArgs;
use Doctrine\Common\Collections\ArrayCollection;

class OstArticleSearch extends Plugin
{
    /**
     * ...
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        // set plugin parameters
        $container->setParameter('ost_article_search.plugin_dir', $this->getPath() . '/');
        $container->setParameter('ost_article_search.view_dir', $this->getPath() . '/Resources/views/');

        // call parent builder
        parent::build($container);
    }

    /**
     * Return the subscribed controller events.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        /*
        // only if we have a session and a shop
        if ( ( !Shopware()->Container()->initialized( "session" ) ) or ( !Shopware()->Container()->initialized( "shop" ) ) )
            // we might be in the backend or in console command
            return array();
        */

        // return the events
        return [
            'Shopware_SearchBundleDBAL_Collect_Facet_Handlers'        => 'registerFacetHandler',
            'Shopware_SearchBundleDBAL_Collect_Condition_Handlers'    => 'registerConditionHandler',
            'Shopware_SearchBundleDBAL_Collect_Sorting_Handlers'      => 'registerSortingHandlers',
            'Shopware_SearchBundle_Collect_Criteria_Request_Handlers' => 'registerCriteriaRequestHandlers',
        ];
    }

    /**
     * ...
     *
     * @param EventArgs $arguments
     *
     * @return ArrayCollection
     */
    public function registerFacetHandler(EventArgs $arguments)
    {
        // our plugin handlers
        $handlers = [
            new Bundle\SearchBundleDBAL\FacetHandler\CategoryFacetHandler(
                $this->container->get('shopware_searchdbal.dbal_query_builder_factory'),
                $this->container->get('snippets')
            ),
            new Bundle\SearchBundleDBAL\FacetHandler\HasPseudoPriceFacetHandler(
                $this->container->get('snippets')
            ),
            new Bundle\SearchBundleDBAL\FacetHandler\SearchFacetHandler(
                $this->container->get('snippets')
            ),
        ];

        // return array collection
        return new ArrayCollection($handlers);
    }

    /**
     * ...
     *
     * @param EventArgs $arguments
     *
     * @return ArrayCollection
     */
    public function registerConditionHandler(EventArgs $arguments)
    {
        // our plugin handlers
        $handlers = [
            new Bundle\SearchBundleDBAL\ConditionHandler\NewConditionHandler(
                $this->container->get( "ost_article_search.configuration" )
            ),
            new Bundle\SearchBundleDBAL\ConditionHandler\SpecialConditionHandler(),
            new Bundle\SearchBundleDBAL\ConditionHandler\CategoryConditionHandler(),
            new Bundle\SearchBundleDBAL\ConditionHandler\HasPseudoPriceConditionHandler(),
            new Bundle\SearchBundleDBAL\ConditionHandler\SearchConditionHandler(
                $this->container,
                $this->container->get( "ost_article_search.configuration" )
            )
        ];

        // return array collection
        return new ArrayCollection($handlers);
    }

    /**
     * ...
     *
     * @param EventArgs $arguments
     *
     * @return ArrayCollection
     */
    public function registerSortingHandlers(EventArgs $arguments)
    {
        // our plugin handlers
        $handlers = [
        ];

        // return array collection
        return new ArrayCollection($handlers);
    }

    /**
     * ...
     *
     * @param EventArgs $arguments
     *
     * @return ArrayCollection
     */
    public function registerCriteriaRequestHandlers(EventArgs $arguments)
    {
        // our plugin handlers
        $handlers = [
            new Bundle\SearchBundle\CriteriaRequestHandler(
                $this->container->get( "ost_article_search.configuration" )
            )
        ];

        // return array collection
        return new ArrayCollection($handlers);
    }

    /**
     * Activate the plugin.
     *
     * @param Context\ActivateContext $context
     */
    public function activate(Context\ActivateContext $context)
    {
        // clear complete cache after we activated the plugin
        $context->scheduleClearCache($context::CACHE_LIST_ALL);
    }

    /**
     * Install the plugin.
     *
     * @param Context\InstallContext $context
     *
     * @throws \Exception
     */
    public function install(Context\InstallContext $context)
    {
        // install the plugin
        $installer = new Setup\Install(
            $this,
            $context,
            $this->container->get('models'),
            $this->container->get('shopware_attribute.crud_service')
        );
        $installer->install();

        // update it to current version
        $updater = new Setup\Update(
            $this,
            $context
        );
        $updater->install();

        // call default installer
        parent::install($context);
    }

    /**
     * Update the plugin.
     *
     * @param Context\UpdateContext $context
     */
    public function update(Context\UpdateContext $context)
    {
        // update the plugin
        $updater = new Setup\Update(
            $this,
            $context
        );
        $updater->update($context->getCurrentVersion());

        // call default updater
        parent::update($context);
    }

    /**
     * Uninstall the plugin.
     *
     * @param Context\UninstallContext $context
     *
     * @throws \Exception
     */
    public function uninstall(Context\UninstallContext $context)
    {
        // uninstall the plugin
        $uninstaller = new Setup\Uninstall(
            $this,
            $context,
            $this->container->get('models'),
            $this->container->get('shopware_attribute.crud_service')
        );
        $uninstaller->uninstall();

        // clear complete cache
        $context->scheduleClearCache($context::CACHE_LIST_ALL);

        // call default uninstaller
        parent::uninstall($context);
    }
}
