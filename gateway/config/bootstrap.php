<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

// ⚙️ 1. Initialisation du conteneur de dépendances
$builder = new ContainerBuilder();

// Ajout des paramètres et dépendances
$builder->addDefinitions(__DIR__ . '/settings.php');
$builder->addDefinitions(__DIR__ . '/dependencies.php');

// Création du conteneur
$container = $builder->build();

// ⚙️ 2. Création de l'application Slim avec le conteneur
AppFactory::setContainer($container);
$app = AppFactory::create();

// ⚙️ 3. Middleware globaux
$app->addBodyParsingMiddleware(); // Middleware pour analyser le corps des requêtes (JSON, form, etc.)
$app->addRoutingMiddleware(); // Middleware de gestion des routes

// ⚙️ 4. Middleware de gestion des erreurs
$errorMiddleware = $app->addErrorMiddleware(
    $container->get('displayErrorDetails'), // Afficher les détails des erreurs
    false, // Logger les erreurs
    false  // Logger les requêtes avec erreur
);

// ⚙️ 5. Chargement des routes
(require_once __DIR__ . '/routes.php')($app);

// ⚙️ 6. Retour de l'application
return $app;
