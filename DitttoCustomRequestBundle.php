<?php
namespace Dittto\CustomRequestBundle;

use Dittto\CustomRequestBundle\DependencyInjection\{DitttoRequestPass, DitttoRequestFilterPass};
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DitttoCustomRequestBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DitttoRequestPass());
        $container->addCompilerPass(new DitttoRequestFilterPass());
    }
}
