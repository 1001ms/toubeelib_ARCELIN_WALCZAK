<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Inclure le fichier de dépendances qui retourne le conteneur
$container = require_once __DIR__ . '/../config/dependencies.php'; // Remplace par le bon chemin

// Récupérer la Closure qui crée l'objet PDO et l'exécuter pour obtenir l'instance de PDO
$pdoClosure = $container[PDO::class];
$pdo = $pdoClosure(); // Appel de la closure pour obtenir l'objet PDO

// Créer le repository PDOPraticien en lui passant l'objet PDO
$pdoPatientRepository = new \toubeelib\infrastructure\PDO\PDOPatient($pdo);

// Créer le service
$service = new \toubeelib\core\services\patient\ServicePatient($pdoPatientRepository);

// Test de création de PraticienDTO
$pdto1 = new \toubeelib\core\dto\InputPatientDTO('néplin', 'jean', 'vandeuve', '06 07 08 09 11', '12/02/12');

// Création de praticien avec des DTO
$pe1 = $service->createPatient($pdto1);
echo "Création du patient 1: \n";
print_r($pe1);
