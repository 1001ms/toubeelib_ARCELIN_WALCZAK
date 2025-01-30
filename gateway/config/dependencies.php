<?php

use gateway\application\actions\GatewayGetAllPraticiensAction;
use gateway\application\actions\GatewayGetPraticienByIdAction;
use GuzzleHttp\Client;
use gateway\middlewares\CorsMiddleware;
use Psr\Container\ContainerInterface;
use gateway\application\actions\CreatePraticienActionGateway;
use gateway\application\actions\CreateRendezVousActionGateway;
use gateway\application\actions\GetDisponibilitesPraticienActionGateway;
use gateway\application\actions\GetRendezVousActionGateway;
use gateway\application\actions\ModifRendezVousActionGateway;
use gateway\application\actions\DeleteRendezVousActionGateway;

return [

    'toubeelibClient' => function(ContainerInterface $c) {
        return new Client([
            'base_uri' => $c->get('api.toubeelib'),
            'timeout' => 2.0
        ]);
    },
    CorsMiddleware::class => function(){
        return new CorsMiddleware();
    },
    GatewayGetAllPraticiensAction::class => function(ContainerInterface $container){
        return new GatewayGetAllPraticiensAction($container->get('toubeelibClient'));
    },
    GatewayGetPraticienByIdAction::class => function(ContainerInterface $container){
        return new GatewayGetPraticienByIdAction($container->get('toubeelibClient'));
    },
    CreatePraticienActionGateway::class => function(ContainerInterface $container){
        return new CreatePraticienActionGateway($container->get('toubeelibClient'));
    },
    CreateRendezVousActionGateway::class => function(ContainerInterface $container){
        return new CreateRendezVousActionGateway($container->get('toubeelibClient'));
    },
    GetDisponibilitesPraticienActionGateway::class => function(ContainerInterface $container){
        return new GetDisponibilitesPraticienActionGateway($container->get('toubeelibClient'));
    },
    GetRendezVousActionGateway::class => function(ContainerInterface $container){
        return new GetRendezVousActionGateway($container->get('toubeelibClient'));
    },
    ModifRendezVousActionGateway::class => function(ContainerInterface $container){
        return new ModifRendezVousActionGateway($container->get('toubeelibClient'));
    },
    DeleteRendezVousActionGateway::class => function(ContainerInterface $container){
        return new DeleteRendezVousActionGateway($container->get('toubeelibClient'));
    }

];
