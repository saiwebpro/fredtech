<?php

namespace spark\drivers\Views;

use spark\drivers\Views\Weed;

/**
 * Renders templates using the Weed template system
 *
 */
class WeedView extends \Slim\View
{
    /**
     * @var Weed The weed instance for rendering templates.
     */
    private $parserInstance = null;

    protected $options = [
        'useCurrentNameSpace' => true,
        'autoEscape' => false
    ];

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function addFolder($namespace, $templatesPath)
    {
        return $this->getInstance()->addFolder($namespace, $templatesPath);
    }

    /**
     * Render Plates Template
     *
     * This method will output the rendered template content
     *
     * @param string $template The path to the template, relative to the templates directory.
     * @param array $data
     * @return string
     */
    public function render($template, $data = null)
    {
        $weed = $this->getInstance();
        $data = array_merge($this->all(), (array) $data);

        /**
         * @filter Filters currently rendering template name
         *
         * @var string
         */
        $template = apply_filters("template.name", $template);

        $route = get_current_route_name();

        /**
         * @filter Filters the template data
         *
         * @var array Template data
         *
         * @param string $template The template name
         */
        $data = apply_filters('template.data', $data, $template);

        if ($route) :
            /**
            * @filter Filters currently rendering template name based on route name.
            *         `$route` refers to current route name
            *
            * @var string
            */
            $template = apply_filters("{$route}.template.name", $template);


            /**
            * @filter Filters the template data based on current route name.
            *         `$route` refers to current route name
            *
            * @var array Template data
            *
            * @param string $template The template name
            */
            $data = apply_filters("{$route}.template.data", $data, $template);
        endif;


        // Quick readable JSON output
        if (JSON_REQUEST && JSON_API_ENABLED) {
            return json($data);
        }

        return $weed->render($template, $data);
    }

    /**
     * Creates new plates instance if it doesn't already exist, and returns it.
     *
     * @return Weed
     */
    public function getInstance()
    {
        if (!$this->parserInstance) {
            $options = $this->options;
            $this->parserInstance = new Weed($this->getTemplatesDirectory(), $options);
        }

        return $this->parserInstance;
    }
}
