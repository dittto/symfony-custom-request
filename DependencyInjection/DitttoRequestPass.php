<?php
namespace Dittto\CustomRequestBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DitttoRequestPass implements CompilerPassInterface
{
    const REQUEST_SERVICE_NAME = 'dittto_custom_request.requests';

    const TAG_NAME = 'dittto.request';

    public function process(ContainerBuilder $container):void
    {
        // drop out if for some reason the request class has gone missing
        if (!$container->has(self::REQUEST_SERVICE_NAME)) {
            return;
        }

        $definition = $container->findDefinition(self::REQUEST_SERVICE_NAME);
        $taggedServices = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addSupportedClass', [new Reference($id)]);
        }
    }
}
