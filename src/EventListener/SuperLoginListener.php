<?php

namespace Comolo\SuperLoginClient\ContaoEdition\EventListener;

use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Config\FileLocator;

class SuperLoginListener
{
    /**
     * @var Router
     */
    protected $router;
    
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function loadRoutes()
    {
        $loader = new YamlFileLoader(
            new FileLocator(__DIR__.'/../Resources/config')
        );
        
        $newRoutes = $loader->load('routing.yml');
        $routes = $this->router->getRouteCollection();
        $routes->addCollection($newRoutes);
        
        // Contao catchall route workaround
        // move contao_catch_all to end of routes array
        $catchAllRouteName = 'contao_catch_all';
        $catchAllRoute = $routes->get($catchAllRouteName);
        
        if ($catchAllRoute) {
            $routes->remove($catchAllRouteName);
            $routes->add($catchAllRouteName, $catchAllRoute);
        }
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->loadRoutes();
    }
}