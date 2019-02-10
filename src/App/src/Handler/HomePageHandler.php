<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Plates\PlatesRenderer;
use Zend\Expressive\Router;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Expressive\Twig\TwigRenderer;
use Zend\Expressive\ZendView\ZendViewRenderer;

class HomePageHandler implements RequestHandlerInterface
{
    /** @var string */
    private $containerName;

    /** @var Router\RouterInterface */
    private $router;

    /** @var null|TemplateRendererInterface */
    private $template;

    public function __construct(
        string $containerName,
        Router\RouterInterface $router,
        ?TemplateRendererInterface $template = null
    ) {
        $this->containerName = $containerName;
        $this->router        = $router;
        $this->template      = $template;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        if ($this->template === null) {
            return new JsonResponse([
                'welcome' => 'Congratulations! You have installed the zend-expressive skeleton application.',
                'docsUrl' => 'https://docs.zendframework.com/zend-expressive/',
            ]);
        }

        $data = [];

        switch ($this->containerName) {
            case 'Aura\Di\Container':
                $data['containerName'] = 'Aura.Di';
                $data['containerDocs'] = 'http://auraphp.com/packages/2.x/Di.html';
                break;
            case 'Pimple\Container':
            case 'Pimple\Psr11\Container':
                $data['containerName'] = 'Pimple';
                $data['containerDocs'] = 'https://pimple.symfony.com/';
                break;
            case 'Zend\ServiceManager\ServiceManager':
                $data['containerName'] = 'Zend Servicemanager';
                $data['containerDocs'] = 'https://docs.zendframework.com/zend-servicemanager/';
                break;
            case 'Auryn\Injector':
            case 'Northwoods\Container\InjectorContainer':
                $data['containerName'] = 'Auryn';
                $data['containerDocs'] = 'https://github.com/rdlowrey/Auryn';
                break;
            case 'Symfony\Component\DependencyInjection\ContainerBuilder':
                $data['containerName'] = 'Symfony DI Container';
                $data['containerDocs'] = 'https://symfony.com/doc/current/service_container.html';
                break;
            case 'Zend\DI\Config\ContainerWrapper':
            case 'DI\Container':
                $data['containerName'] = 'PHP-DI';
                $data['containerDocs'] = 'http://php-di.org';
                break;
        }

        if ($this->router instanceof Router\AuraRouter) {
            $data['routerName'] = 'Aura.Router';
            $data['routerDocs'] = 'http://auraphp.com/packages/2.x/Router.html';
        } elseif ($this->router instanceof Router\FastRouteRouter) {
            $data['routerName'] = 'FastRoute';
            $data['routerDocs'] = 'https://github.com/nikic/FastRoute';
        } elseif ($this->router instanceof Router\ZendRouter) {
            $data['routerName'] = 'Zend Router';
            $data['routerDocs'] = 'https://docs.zendframework.com/zend-router/';
        }

        if ($this->template instanceof PlatesRenderer) {
            $data['templateName'] = 'Plates';
            $data['templateDocs'] = 'http://platesphp.com/';
        } elseif ($this->template instanceof TwigRenderer) {
            $data['templateName'] = 'Twig';
            $data['templateDocs'] = 'http://twig.sensiolabs.org/documentation';
        } elseif ($this->template instanceof ZendViewRenderer) {
            $data['templateName'] = 'Zend View';
            $data['templateDocs'] = 'https://docs.zendframework.com/zend-view/';
        }

        return new HtmlResponse($this->template->render('app::home-page', $data));
    }
}
