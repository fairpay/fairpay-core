<?php


namespace Fairpay\Util\Command;

use Fairpay\Util\Tests\Helpers\UrlHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddRouteForTests extends ContainerAwareCommand
{
    /** @var  Router */
    private $router;

    /**
     * AddRouteForTests constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        parent::__construct();
        $this->router = $router;
    }

    protected function configure()
    {
        $this
            ->setName('tests:add-route')
            ->setDescription('Add a route to UrlHelper and RedirectedHelper')
            ->addArgument(
                'route',
                InputArgument::OPTIONAL,
                'The route to create'
            )
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'The method name, based on the route name by default'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $routes = UrlHelper::$routes;
        $routeName = $input->getArgument('route');

        if ($routeName) {
            $name = $this->getRouteName($routeName, $input->getArgument('name'));

            $path = $this->router->getRouteCollection()->get($routeName)->getPath();
            $requirements = $this->getRequirements($path);

            if (count($requirements) > 1) {
                $output->write(sprintf('<error>This route has %d arguments, maximum 1 allowed.</error>', count($requirements)));
            }

            if (count($requirements)) {
                $routes[$name] = [$routeName, $requirements[0]];
            } else {
                $routes[$name] = $routeName;
            }
        }

        ksort($routes);

        $routesPhpCode = 'static public $routes = array(';
        foreach ($routes as $key => $value) {
            $routesPhpCode .= "\n        '$key' => ";
            if (is_array($value)) {
                $routesPhpCode .= "['{$value[0]}', '{$value[1]}'],";
            } else {
                $routesPhpCode .= "'$value',";
            }
        }
        $routesPhpCode .= "\n    );";

        $phpDocUrlHelper = '';
        foreach ($routes as $key => $value) {
            $phpDocUrlHelper .= "\n * @method string $key(";
            if (is_array($value)) {
                $phpDocUrlHelper .= '$'.$value[1];
            }
            $phpDocUrlHelper .= ')';
        }

        $phpDocRedirectedHelper = preg_replace('/@method string/', '@method', $phpDocUrlHelper);

        $UrlHelperPhp = file_get_contents(__DIR__.'/../Tests/Helpers/UrlHelper.php');
        $UrlHelperPhp = preg_replace('/(\n \* @method string \w+\((\$\w+)?\)\r?)+/', $phpDocUrlHelper, $UrlHelperPhp);
        $UrlHelperPhp = preg_replace('/static public \$routes = array\([^;]+\);/', $routesPhpCode, $UrlHelperPhp);
        file_put_contents(__DIR__.'/../Tests/Helpers/UrlHelper.php', $UrlHelperPhp);
        $output->writeln('Updated UrlHelper.php');

        $RedirectedHelperPhp = file_get_contents(__DIR__.'/../Tests/Helpers/RedirectedHelper.php');
        $RedirectedHelperPhp = preg_replace('/(\n \* @method \w+\((\$\w+)?\)\r?)+/', $phpDocRedirectedHelper, $RedirectedHelperPhp);
        file_put_contents(__DIR__.'/../Tests/Helpers/RedirectedHelper.php', $RedirectedHelperPhp);
        $output->writeln('Updated RedirectedHelper.php');
    }

    private function getRouteName($route, $name)
    {
        if ($name) {
            return $name;
        }

        $route = preg_replace('/^fairpay_/', '', $route);

        return preg_replace_callback('/_(\w)/', function($m) {
            return strtoupper($m[1]);
        }, $route);
    }

    private function getRequirements($path)
    {
        preg_match_all('/\{([^\}]+)\}/', $path, $requirements);

        return $requirements[1];
    }
}