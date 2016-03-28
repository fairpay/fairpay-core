<?php


namespace Fairpay\Util\Twig;


use Symfony\Bundle\FrameworkBundle\Routing\Router;

class HtmlExtension extends \Twig_Extension
{
    /** @var Router */
    private $router;

    /**
     * HtmlExtension constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('path_tmpl', array($this, 'pathTmpl'), array('is_safe' => ['html'])),
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('label', array($this, 'label'), array('is_safe' => ['html'])),
        );
    }

    public function pathTmpl($name, $parameters = [])
    {
        $route = $this->router->getRouteCollection()->get($name);
        $replace = [];

        foreach ($route->getRequirements() as $requirement => $schema) {
            if (!key_exists($requirement, $parameters)) {
                $replace[md5($requirement)] = "[[:$requirement]]";
                $parameters[$requirement] = md5($requirement);
            }
        }

        $path = $this->router->generate($name, $parameters);

        foreach ($replace as $md5 => $replaceWith) {
            $path = str_replace($md5, $replaceWith, $path);
        }

        return $path;
    }

    public function label($value)
    {
        if (is_bool($value)) {
            return $value ? '<span class="label success">Oui</span>' : '<span class="label danger">Non</span>';
        }

        return '<span class="label">' . $value . '</span>';
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'html_extension';
    }
}