<?php
namespace Bear\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;

/**
 * Class SymfonyRoutingAdapter
 *
 * @package Bear\Routing
 */
class SymfonyRoutingAdapter extends AbstractRoutingAdapter
{
    /**
     * @var LoaderInterface $loader
     */
    private $loader;

    /**
     * @var mixed
     */
    private $resource;

    /**
     * @var Router|null
     */
    private $router;

    /**
     * @param LoaderInterface $loader
     * @param mixed           $resource
     */
    public function __construct(LoaderInterface $loader, $resource)
    {
        $this->loader   = $loader;
        $this->resource = $resource;
        $this->request  = Request::createFromGlobals();
    }

    /**
     * @param string $uri
     * @param string $method
     *
     * @return RoutingResolution
     *
     * @throws \Exception
     */
    public function resolve(string $uri, string $method): RoutingResolution
    {
        $routingResolution = new RoutingResolution();

        try {
            $info = $this->router->match($uri);
            if (!$info['_controller'] ?? null) {
                throw new \Exception('No controller defined. Please set the "_controller" route parameter.');
            }

            $routingResolution->setCode(RoutingResolution::FOUND);
            $routingResolution->setVars($info);
            $routingResolution->setController((string) $info['_controller']);
            // todo: allow _controller=MyController:action as well and make _action optional
            $routingResolution->setAction((string) $info['_action']);
        } catch (ResourceNotFoundException $e) {
            $routingResolution->setCode(RoutingResolution::NOT_FOUND);
        }

        return $routingResolution;
    }

    /**
     * @return void
     */
    public function init(): void
    {
    }

    /**
     * @return Router
     */
    public function getRouter(): Router
    {
        if (!$this->router) {
            $context = new RequestContext();
            $context->fromRequest($this->request);

            $this->router = new Router($this->loader, $this->resource, [], $context);
        }

        return $this->router;
    }
}
