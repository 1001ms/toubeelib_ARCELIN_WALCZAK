<?php
namespace gateway\application\actions;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class GetRendezVousActionGateway extends AbstractAction
{
    private ClientInterface $toubeelibClient;

    public function __construct(ClientInterface $client) {
        $this->toubeelibClient = $client;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $id = $args['id'];
        if (empty($id)) {
            $response->getBody()->write(json_encode(['error' => "L'ID du rendez-vous est requis."]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
        try {
            $queryParams = $request->getQueryParams();
            $response = $this->toubeelibClient->get("rdvs/$id",['query' => $queryParams]);
        } catch (ClientException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }
        return $response;
    }
}
