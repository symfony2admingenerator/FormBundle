<?php

namespace Admingenerator\FormBundle\Twig\Extension;

use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 * @author Piotr Gołębiewski <loostro@gmail.com>
 */
class FormExtension extends AbstractExtension
{
    /**
     * This property is public so that it can be accessed directly from compiled
     * templates without having to call a getter, which slightly decreases performance.
     */
    public function __construct(public FormRendererInterface $renderer)
    {
    }

    public function getFunctions(): array
    {
        return [
            'form_js' => new TwigFunction('form_js', $this->renderJavascript(...), ['is_safe' => ['html']]),
            'form_css' => new TwigFunction('form_css', null, ['node_class' => 'Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode', 'is_safe' => ['html']]),
        ];
    }

    public function getFilters(): array
    {
        return [
            'e4js'      => new TwigFilter('e4js', $this->escape_for_js(...)),
            'nowrap'    => new TwigFilter('nowrap', $this->nowrap(...)),
        ];
    }

    /**
     * Render Function Form Javascript
     */
    public function renderJavascript(FormView $view, bool $prototype = false): string
    {
        $block = $prototype ? 'js_prototype' : 'js';

        return $this->renderer->searchAndRenderBlock($view, $block);
    }

    /**
     * Removes newlines from string
     * 
     * @return string
     */
    public function nowrap(string $var): string
    {
        return preg_replace('(\r\n|\r|\n)', '', $var);
    }

    /**
     * The default twig "escape('js')" filter does not recognize various
     * patterns in string and breaks the code (eg. anonymous functions).
     *
     * This filter recognizes these patterns and treats them accordingly:
     *
     * Value               | behaviour
     * ====================|======================================
     * Anonymous function  > output raw
     * Function call       > output raw
     * Json object         > recursively iterate over properties,
     *                     |   and escape each value individually
     * Json array          > recursively iterate over values,
     *                     |   and escape each individually
     * Boolean             > output true or false
     * Null                > output null
     * Undefined           > output undefined
     * Other strings       > replace quotes with &quot; and output
     *                     |   wrapped in quotes
     */
    public function escape_for_js(mixed $var): string
    {
        $functionPattern = "%^\\s*function\\s*\\(%is";
        $callPattern = "%^\w+\((['\w\d]+(,\\s['\w\d]+)*)?\)(\.\w+\((['\w\d]+(,\\s['\w\d]+)*)?\))?$%is";
        $jsonPattern = "%^\\s*\\{.*\\}\\s*$%is";
        $arrayPattern = "%^\\s*\\[.*\\]\\s*$%is";

        if (is_bool($var)) {
            return $var ? 'true' : 'false';
        }

        if (is_null($var)) {
            return 'null';
        }

        if ('undefined' === $var) {
            return 'undefined';
        }

        if (is_string($var)
            && !preg_match($functionPattern, $var) 
            && !preg_match($callPattern, $var)
            && !preg_match($jsonPattern, $var)
            && !preg_match($arrayPattern, $var)
        ) {
            $var = preg_replace('(\r\n|\r|\n)', '', $var);
            return '"'.str_replace('"', '&quot;', $var).'"';
        }

        if (is_array($var)) {
            $is_assoc = function ($array) {
                return (bool)count(array_filter(array_keys($array), 'is_string'));
            };

            if ($is_assoc($var)) {
                $items = array();
                foreach($var as $key => $val) {
                    $items[] = '"'.$key.'": '.$this->escape_for_js($val);
                }
                return '{'.implode(',', $items).'}';
            } else {
                $items = array();
                foreach($var as $val) {
                    $items[] = $this->escape_for_js($val);
                }
                return '['.implode(',', $items).']';
            }
        }

        return $var;
    }

    public function getName(): string
    {
        return 'admingenerator.twig.extension.form';
    }
}
