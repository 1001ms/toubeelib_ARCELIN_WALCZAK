<?php

declare(strict_types=1);

use gateway\application\actions\GatewayGetAllPraticiensAction;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use gateway\application\actions\CreatePraticienActionGateway;
use gateway\application\actions\CreateRendezVousActionGateway;
use gateway\application\actions\GatewayGetPraticienByIdAction;
use gateway\application\actions\GetDisponibilitesPraticienActionGateway;
use gateway\application\actions\GetRendezVousActionGateway;
use gateway\application\actions\ModifRendezVousActionGateway;
use gateway\application\actions\DeleteRendezVousActionGateway;

return function (\Slim\App $app): \Slim\App {
    $app->add(\gateway\middlewares\CorsMiddleware::class);
    $app->post('/praticiens', CreatePraticienActionGateway::class);
    $app->get('/praticiens', GatewayGetAllPraticiensAction::class);
    $app->get('/praticiens/{id}', GatewayGetPraticienByIdAction::class);
    $app->get('/praticiens/{id}/disponibilites', GetDisponibilitesPraticienActionGateway::class);
    $app->post('/rdvs', CreateRendezVousActionGateway::class);
    $app->get('/rdvs/{id}', GetRendezVousActionGateway::class);
    $app->patch('/rdvs/{id}', ModifRendezVousActionGateway::class);
    $app->delete('/rdvs/{id}', DeleteRendezVousActionGateway::class);
    $app->options('/{routes:.+}', function (Request $request, Response $response) {
        return $response;
    });

    return $app;
};
