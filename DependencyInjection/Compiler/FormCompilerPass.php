<?php

namespace Admingenerator\FormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Processes twig configuration
 *
 * @author Piotr Gołębiewski <loostro@gmail.com>
 */
class FormCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // Used templates
        $templates = ['@AdmingeneratorForm/form_js.html.twig', '@AdmingeneratorForm/form_css.html.twig'];

        $resources = $container->getParameter('twig.form.resources');
        $alreadyImported = count(array_intersect($resources, $templates)) == count($templates);

        if (!$alreadyImported) {
            // Insert right after form_div_layout.html.twig if exists
            if (($key = array_search('form_div_layout.html.twig', $resources)) !== false) {
                array_splice($resources, ++$key, 0, $templates);
            } else {
                // Put it in first position
                array_unshift($resources, $templates);
            }

            $container->setParameter('twig.form.resources', $resources);
        }
    }
}
