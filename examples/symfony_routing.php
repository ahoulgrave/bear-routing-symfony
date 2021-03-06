<?php
require __DIR__ . '/../vendor/autoload.php';

/**
 * Class TestController
 */
class TestController {
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testAction(\Symfony\Component\HttpFoundation\Request $request): \Symfony\Component\HttpFoundation\Response
    {
        return new \Symfony\Component\HttpFoundation\Response(sprintf('Hello %s!', $request->get('name', 'Stranger')));
    }
}

$resource = function () {
    $routes = new \Symfony\Component\Routing\RouteCollection();
    $route = new \Symfony\Component\Routing\Route('/{name}', ['_controller' => TestController::class, '_action' => 'test', 'name' => 'Stranger']);
    $routes->add('route_1', $route);
    $subCollection = new \Symfony\Component\Routing\RouteCollection();
    $subCollection->add('route_2', clone $route);
    $subCollection->addPrefix('/group');
    $routes->addCollection($subCollection);

    return $routes;
};
$loader = new \Symfony\Component\Routing\Loader\ClosureLoader();

$config = [
    'routing' => new \Bear\Routing\SymfonyRoutingAdapter($loader, $resource),
    'service_manager' => [
        'services' => [
            TestController::class => new TestController(),
        ],
    ],
];

\Bear\App::init($config);
