<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;
use EaglenavigatorSystem\Wopi\Middleware\WopiDiscoveryMiddleware;
use EaglenavigatorSystem\Wopi\Middleware\ValidateProofMiddleware;
use EaglenavigatorSystem\Wopi\Middleware\WopiOperationMiddleware;


Router::plugin(
    'EaglenavigatorSystem/Wopi',
    ['path' => '/wopi'],
    function (RouteBuilder $routes) {

        $routes->registerMiddleware('wopiDiscovery', new WopiDiscoveryMiddleware());
        $routes->registerMiddleware('validateProof', new ValidateProofMiddleware());
        $routes->registerMiddleware('wopiOperation', new WopiOperationMiddleware());

        $routes->middlewareGroup('wopi', ['wopiDiscovery', 'validateProof', 'wopiOperation']);

        // Define your routes here
        $routes->connect(
            '/files/:file_id',

            [
                'controller' => 'CheckFileInfo',
                'action' => 'index'
            ],
            [
                'pass' => ['file_id'],
                'name' => 'checkFileInfo'
            ]
        );
        $routes->connect(
            '/files/:file_id/contents',
            [
                'controller' => 'GetFile',
                'action' => 'index'
            ],
            [
                'pass' => ['file_id'],
                'name' => 'getFile'
            ]
        );
        $routes->connect(
            '/files/:file_id/contents',
            [
                'controller' => 'PutFile',
                'action' => 'index'
            ],
            [
                'pass' => ['file_id'],
                'name' => 'putFile'
            ]
        )->setMethods(['POST']);
        $routes->connect(
            '/files/:file_id',
            [
                'controller' => 'Files',
                'action' => 'index'
            ],
            [
                'pass' => ['file_id'],
                'name' => 'postRouter'
            ]
        )->setMethods(['POST']);

        $routes->fallbacks(DashedRoute::class);
    }
);
