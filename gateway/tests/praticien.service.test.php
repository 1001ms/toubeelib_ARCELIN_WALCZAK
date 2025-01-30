<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Inclure le fichier de dépendances qui retourne le conteneur
$container = require_once __DIR__ . '/../config/dependencies.php'; // Remplace par le bon chemin

// Récupérer la Closure qui crée l'objet PDO et l'exécuter pour obtenir l'instance de PDO
$pdoClosure = $container[PDO::class];
$pdo = $pdoClosure(); // Appel de la closure pour obtenir l'objet PDO

// Créer le repository PDOPraticien en lui passant l'objet PDO
$pdoPraticienRepository = new \toubeelib\infrastructure\PDO\PDOPraticien($pdo);

// Créer le service
$service = new \toubeelib\core\services\praticien\ServicePraticien($pdoPraticienRepository);

// Test de création de PraticienDTO
$pdto1 = new \toubeelib\core\dto\InputPraticienDTO('néplin', 'jean', 'vandeuve', '06 07 08 09 11', 'A');
$pdto2 = new \toubeelib\core\dto\InputPraticienDTO('némar', 'jean', 'lassou', '06 07 08 09 12', 'B');

// Création de praticien avec des DTO
$pe1 = $service->createPraticien($pdto1);
echo "Création du praticien 1: \n";
print_r($pe1);

// $pe2 = $service->createPraticien($pdto2);
// echo "Création du praticien 2: \n";
// print_r($pe2);

// // Test de récupération par ID
// echo "\nRécupération des praticiens par ID:\n";
// $pe11 = $service->getPraticienById($pe1->ID);
// print_r($pe11);

// $pe22 = $service->getPraticienById($pe2->ID);
// print_r($pe22);

// // Test de récupération d'un praticien avec un ID invalide
// echo "\nTest récupération avec un ID invalide:\n";
// try {
//     $pe33 = $service->getPraticienById('ABCDE');
// } catch (\toubeelib\core\services\praticien\ServicePraticienInvalidDataException $e) {
//     echo 'Exception dans la récupération d\'un praticien : ' . PHP_EOL;
//     echo $e->getMessage() . PHP_EOL;
// }

// // Test de création d'un praticien avec des données invalides (même spécialité pour deux praticiens)
// echo "\nTest création avec une spécialité déjà existante:\n";
// try {
//     $pdto3 = new \toubeelib\core\dto\InputPraticienDTO('némar', 'jean', 'lassou', '06 07 08 09 12', 'A');
//     print_r($service->createPraticien($pdto3)); // Crée un doublon de spécialité
// } catch (\toubeelib\core\services\praticien\ServicePraticienInvalidDataException $e) {
//     echo 'Exception dans la création d\'un praticien : ' . PHP_EOL;
//     echo $e->getMessage() . PHP_EOL;
// }

// // Test avec des données invalides (champ vide)
// echo "\nTest création avec des données invalides (champs vides):\n";
// try {
//     $pdto4 = new \toubeelib\core\dto\InputPraticienDTO('', '', '', '', '');
//     print_r($service->createPraticien($pdto4)); // Essaye de créer un praticien avec des champs vides
// } catch (\toubeelib\core\services\praticien\ServicePraticienInvalidDataException $e) {
//     echo 'Exception dans la création d\'un praticien : ' . PHP_EOL;
//     echo $e->getMessage() . PHP_EOL;
// }

// // Test avec une spécialité inexistante
// echo "\nTest récupération avec une spécialité inexistante:\n";
// try {
//     print_r($service->getPraticiensBySpecialite('Cardiologue')); // Supposons qu'il n'y a pas de "Cardiologue"
// } catch (\toubeelib\core\services\praticien\ServicePraticienInvalidDataException $e) {
//     echo 'Exception dans la récupération des praticiens par spécialité : ' . PHP_EOL;
//     echo $e->getMessage() . PHP_EOL;
// }

// // Test avec une ville inexistante
// echo "\nTest récupération avec une ville inexistante:\n";
// try {
//     print_r($service->getPraticiensByCity('Paris123')); // Ville qui n'existe pas
// } catch (\toubeelib\core\services\praticien\ServicePraticienInvalidDataException $e) {
//     echo 'Exception dans la récupération des praticiens par ville : ' . PHP_EOL;
//     echo $e->getMessage() . PHP_EOL;
// }

// // Test avec une recherche de praticiens par ville
// echo "\nTest récupération avec une ville valide:\n";
// try {
//     print_r($service->getPraticiensByCity('vandeuve')); // Recherche par ville
// } catch (\toubeelib\core\services\praticien\ServicePraticienInvalidDataException $e) {
//     echo 'Exception dans la récupération des praticiens par ville : ' . PHP_EOL;
//     echo $e->getMessage() . PHP_EOL;
// }

// // Test de création avec des données valides mais vérification de l'ID
// echo "\nTest création et vérification de l'ID du praticien:\n";
// $pdto5 = new \toubeelib\core\dto\InputPraticienDTO('Dupont', 'Pierre', 'Paris', '01 02 03 04 05', 'C');
// $praticien5 = $service->createPraticien($pdto5);
// echo "Praticien 5 créé : \n";
// print_r($praticien5);
// echo "Vérification de l'ID : " . $praticien5->ID . PHP_EOL;

// // Test avec un ID invalide pour récupérer un praticien
// echo "\nTest récupération avec un ID qui n'existe pas:\n";
// try {
//     $pe99 = $service->getPraticienById('99999'); // ID qui n'existe pas
// } catch (\toubeelib\core\services\praticien\ServicePraticienInvalidDataException $e) {
//     echo 'Exception dans la récupération d\'un praticien avec ID inexistant: ' . PHP_EOL;
//     echo $e->getMessage() . PHP_EOL;
// }

// // Test de la gestion d'erreur complète
// echo "\nTest gestion d'erreur générale:\n";
// try {
//     // Essai avec des données invalides non capturées plus tôt
//     $invalidDTO = new \toubeelib\core\dto\InputPraticienDTO('', 'Invalid', '', '12345', ''); // Champs invalides
//     $service->createPraticien($invalidDTO);
// } catch (\toubeelib\core\services\praticien\ServicePraticienInvalidDataException $e) {
//     echo 'Exception dans la création d\'un praticien avec des données invalides : ' . PHP_EOL;
//     echo $e->getMessage() . PHP_EOL;
// }
