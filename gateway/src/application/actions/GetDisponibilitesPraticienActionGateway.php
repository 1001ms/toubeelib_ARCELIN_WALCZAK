<?php

namespace gateway\application\actions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class GetDisponibilitesPraticienActionGateway extends AbstractAction
{
    private ClientInterface $toubeelibClient;

    public function __construct(ClientInterface $client) {
        $this->toubeelibClient = $client;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $id = $args['id'] ?? null;
        if (empty($id)) {
            $response->getBody()->write(json_encode(['error' => "L'ID du praticien est requis."]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        try {
            $queryParams = $request->getQueryParams();
            $apiResponse = $this->toubeelibClient->get("praticiens/$id/disponibilites", [
                'query' => !empty($queryParams) ? $queryParams : []
            ]);

            // Retourner la réponse du backend
            $response->getBody()->write($apiResponse->getBody()->getContents());
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus($apiResponse->getStatusCode());
        } catch (ClientException $e) {
            // Gestion des erreurs 4xx ou 5xx du backend
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus($e->getResponse()->getStatusCode());
        } catch (\Exception $e) {
            // Gestion des erreurs internes
            $response->getBody()->write(json_encode(['error' => 'Erreur interne du serveur']));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
