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

$resource = 'routes.yml';
$locator = new \Symfony\Component\Config\FileLocator([__DIR__]);
$loader = new \Symfony\Component\Routing\Loader\YamlFileLoader($locator);

$config = [
    'routing' => new \Bear\Routing\SymfonyRoutingAdapter($loader, $resource),
    'service_manager' => [
        'services' => [
            TestController::class => new TestController(),
        ],
    ],
];

\Bear\App::init($config);
