<?php

namespace gateway\application\actions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class ModifRendezVousActionGateway extends AbstractAction
{
    private ClientInterface $toubeelibClient;

    public function __construct(ClientInterface $client) {
        $this->toubeelibClient = $client;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $id = $args['id'] ?? null;
        if (empty($id)) {
            $response->getBody()->write(json_encode(['error' => "L'ID du rendez-vous est requis."]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
        try {
            $response = $this->toubeelibClient->patch("rdvs/$id",['json'=>$request->getParsedBody()]);
        } catch (ClientException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }
        return $response;
    }
}
           