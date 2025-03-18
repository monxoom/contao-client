<?php

namespace Comolo\SuperLoginClient\ContaoEdition\ContaoManager;

use Comolo\SuperLoginClient\ContaoEdition\ComoloSuperLoginClientBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Plugin for the Contao Manager.
 *
 * @author Hendrik Obermayer <https://github.com/henobi>
 */
class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(ComoloSuperLoginClientBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class])
                ->setReplace(['superlogin_client']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel): ?RouteCollection
    {
        $loader = $resolver->resolve(__DIR__.'/../Resources/config/routing.yaml');
        
        return $loader?->load(__DIR__.'/../Resources/config/routing.yaml');
    }
}
