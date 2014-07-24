<?php

namespace Admingenerator\FormBundle;

use Admingenerator\FormBundle\DependencyInjection\Compiler\FormCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Form extension for Symfony2 Admingenerator project
 *
 * @author Piotr Gołębiewski <loostro@gmail.com>
 */
class AdmingeneratorFormBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FormCompilerPass());
    }
}
