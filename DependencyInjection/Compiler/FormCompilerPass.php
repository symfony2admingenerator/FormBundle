<?php

namespace Admingenerator\FormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

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
        $resources = $container->getParameter('twig.form.resources');
        $alreadyImported = in_array('AdmingeneratorFormBundle::form_js.html.twig', $resources)
                        && in_array('AdmingeneratorFormBundle::form_css.html.twig', $resources);

        if (!$alreadyImported) {
            // Insert right after form_div_layout.html.twig if exists
            if (($key = array_search('form_div_layout.html.twig', $resources)) !== false) {
                array_splice($resources, ++$key, 0, array(
                    'AdmingeneratorFormBundle::form_js.html.twig',
                    'AdmingeneratorFormBundle::form_css.html.twig'
                ));
            } else {
                // Put it in first position
                array_unshift($resources, array(
                    'AdmingeneratorFormBundle::form_js.html.twig',
                    'AdmingeneratorFormBundle::form_css.html.twig'
                ));
            }

            $container->setParameter('twig.form.resources', $resources);
        }
    }
}
