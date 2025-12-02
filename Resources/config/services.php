<?php

use Admingenerator\FormBundle\Twig\Extension\FormExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $container): void {
    $container->parameters()->set('admingenerator.twig.extension.form.class', FormExtension::class);

    $container->services()
        ->set('admingenerator.twig.extension.form', param('admingenerator.twig.extension.form.class'))
        ->tag('twig.extension')
        ->arg('$renderer', service('twig.form.renderer'));
};