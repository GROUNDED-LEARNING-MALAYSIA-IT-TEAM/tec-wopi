<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

Router::plugin(
    'EaglenavigatorSystem/Wopi',
    ['path' => '/wopi'],
    function (RouteBuilder $routes) {
        //-- use prefix wopi


            /**
             * url : /wopi/files/{fileId} - get request .
             * - routing in router , user prefix `wopi` and controller `CheckFileInfo` and action `index`
             * - response must be in json format
             */
            $routes->connect(
                '/files/:fileId',
                [
                    'controller' => 'Files',
                    'action' => 'index',
                ]
            )->setMethods(['GET'])
            ->setPass(['fileId']);
            /**
             * url : /wopi/files/{fileId}/contents - get request .
             * - routing in router , user prefix `wopi` and controller `GetFile` and action `getFile`
             * - response must be in json format
             */
            $routes->connect(
                '/files/:fileId/contents',
                [
                    'controller' => 'Files',
                    'action' => 'index',
                ])->setMethods(['GET'])
                ->setPass(['fileId']);

                //edit action
                $routes->connect(
                    '/files/:fileId/contents',
                    [
                        'controller' => 'Files',
                        'action' => 'index',
                    ])->setMethods(['POST'])
                    ->setPass(['fileId']);

                        //GET /wopi/files/(file_id) - CheckFileInfo
                        //GET /wopi/files/(file_id)/contents - GetFile

                        //POST (/wopi/files/(file_id) - LOCK
                        //POST /wopi/files/(file_id) - GET_LOCK
                        //POST /wopi/files/(file_id) - REFRESH_LOCK
                        //POST /wopi/files/(file_id) - UNLOCK
                        //POST /wopi/files/(file_id) - UNLOCK_AND_RELOCK
                        //POST /wopi/files/(file_id) - PUT_RELATIVE
                        //POST /wopi/files/(file_id) - DELETE
                        //POST /wopi/files/(file_id) - PUT_USER_INFO
                        //POST /wopi/files/(file_id) - PUT_FILE
                        //POST /wopi/files/(file_id) - RENAME_FILE
                        //POST /wopi/files/(file_id) - PUT_RELATIVE
                        //POST /wopi/files/(file_id)  RenameFile






        $routes->fallbacks(DashedRoute::class);
    }
);
