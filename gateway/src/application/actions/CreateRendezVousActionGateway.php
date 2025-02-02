<?php

namespace gateway\application\actions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class CreateRendezVousActionGateway extends AbstractAction
{
    private ClientInterface $toubeelibClient;

    public function __construct(ClientInterface $client) {
        $this->toubeelibClient = $client;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        try {
            $parsedBody = $request->getParsedBody();
            if (empty($parsedBody) || !is_array($parsedBody)) {
                $response->getBody()->write(json_encode(['error' => "Le corps de la requête est vide ou mal formaté."]));
                return $response->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            }
            $requiredFields = ['praticien', 'patient', 'specialite', 'date'];
            foreach ($requiredFields as $field) {
                if (empty($parsedBody[$field])) {
                    $response->getBody()->write(json_encode(['error' => "Le champ '$field' est requis."]));
                    return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
                }
            }

            // Envoi de la requête POST vers le backend
            $apiResponse = $this->toubeelibClient->post("rdvs", ['json' => $parsedBody]);

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
