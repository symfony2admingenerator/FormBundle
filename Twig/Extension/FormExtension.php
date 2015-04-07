<?php

namespace Admingenerator\FormBundle\Twig\Extension;

use Symfony\Component\Form\FormView;
use Symfony\Bridge\Twig\Form\TwigRendererInterface;

/**
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 * @author Piotr Gołębiewski <loostro@gmail.com>
 */
class FormExtension extends \Twig_Extension
{
    /**
     * This property is public so that it can be accessed directly from compiled
     * templates without having to call a getter, which slightly decreases performance.
     *
     * @var \Symfony\Component\Form\FormRendererInterface
     */
    public $renderer;

    public function __construct(TwigRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'form_js' => new \Twig_Function_Method($this, 'renderJavascript', array('is_safe' => array('html'))),
            'form_css' => new \Twig_Function_Node('Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode', array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'e4js'      => new \Twig_Filter_Method($this, 'escape_for_js'),
            'nowrap'    => new \Twig_Filter_Method($this, 'nowrap'),
        );
    }

    /**
     * Render Function Form Javascript
     *
     * @param FormView $view
     * @param bool $prototype
     *
     * @return string
     */
    public function renderJavascript(FormView $view, $prototype = false)
    {
        $block = $prototype ? 'js_prototype' : 'js';

        return $this->renderer->searchAndRenderBlock($view, $block);
    }

    /**
     * Removes newlines from string
     * 
     * @return string
     */
    public function nowrap($var)
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
     *
     * @param string $var
     * @return string
     */
    public function escape_for_js($var)
    {
        $functionPattern = "%^\\s*function\\s*\\(%is";
        $callPattern = "%^\w+\((\w+(,\\s\w+)*)?\)$%is";
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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admingenerator.twig.extension.form';
    }
}
