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

namespace OstArticleSearch\Listeners\Controllers;

use Enlight_Controller_Action as Controller;
use Enlight_Event_EventArgs as EventArgs;

class Frontend
{
    /**
     * ...
     *
     * @var string
     */
    protected $viewDir;

    /**
     * ...
     *
     * @var array
     */
    protected $configuration;

    /**
     * ...
     *
     * @var array
     */
    protected $blacklist = [
        'frontend' => [
            'account',
            'address',
            'csrftoken',
            'detail',
            'custom',
            'checkout',
            'forms',
            'media',
            'newsletter',
            'note',
            'payment',
            'register',
            'robotstxt',
            'sitemap',
            'sitemapmobilexml',
            'sitemapxml',
            'tellafriend',
            'ticket',
            'tracking'
        ],
        'widgets' => [
            'captcha',
            'checkout',
            'compare',
            'recommendation'
        ]
    ];

    /**
     * ...
     *
     * @param string $viewDir
     * @param array  $configuration
     */
    public function __construct($viewDir, array $configuration)
    {
        // set params
        $this->viewDir = $viewDir;
        $this->configuration = $configuration;
    }

    /**
     * ...
     *
     * @param EventArgs $arguments
     */
    public function onPostDispatch(EventArgs $arguments)
    {
        /* @var $controller Controller */
        $controller = $arguments->get('subject');
        $request = $controller->Request();
        $view = $controller->View();

        // in frontend and listing, search -or- widget and listing
        if ((($request->getModuleName() === 'frontend') && (!in_array(strtolower($request->getControllerName()), $this->blacklist['frontend']))) || (($request->getModuleName() === 'widgets') && (!in_array(strtolower($request->getControllerName()), $this->blacklist['widgets'])))) {
            // add our template dir
            $view->addTemplateDir(
                $this->viewDir
            );
        }
    }
}
