<?php

require_once __DIR__ . '/../vendor/autoload.php';

$service = new toubeelib\core\services\rdv\ServiceRendezVous(new \toubeelib\infrastructure\repositories\ArrayRdvRepository(),
                                                             new \toubeelib\infrastructure\repositories\ArrayPraticienRepository());

$rdvdto = new \toubeelib\core\dto\InputRendezVousDTO('p1', 'pa1', 'A', '2024-09-02T09:00');

print_r('Test 1 - Creation Rendez Vous ##########################################################');

try {

    print_r($service->listeDisposPraticien('p1','2024-09-02T09:00','2024-09-06T09:00'));
    $rdv1 = $service->creerRendezvous($rdvdto);
} catch (\toubeelib\core\services\rdv\ServiceRendezVousIncorrectDataException $e){
    echo 'exception dans la récupération d\'un praticien :' . PHP_EOL;
    echo $e->getMessage(). PHP_EOL;
}



print_r('Test 1 bis - Planning ##########################################################');

try {

    print_r($service->planningPraticien('p1','2024-09-02T09:00','2024-10-02T09:00'));
} catch (\toubeelib\core\services\rdv\ServiceRendezVousIncorrectDataException $e){
    echo 'exception dans la récupération d\'un praticien :' . PHP_EOL;
    echo $e->getMessage(). PHP_EOL;
}

print_r('Test 2 - Change patient ##########################################################"');

try {

    print_r('Avant changement');
    
    print_r($service->getRendezvousById('r1'));

    print_r('Après changement');

    $service->changePatient('r1','new_patient');

    print_r($service->getRendezvousById('r1'));

} catch (\toubeelib\core\services\rdv\ServiceRendezVousIncorrectDataException $e){
    echo 'exception dans le changement d\'un patient  :' . PHP_EOL;
    echo $e->getMessage(). PHP_EOL;
}

print_r('Test 3 - Change spe ##########################################################"');

try {

    print_r('Avant changement');
    
    print_r($service->getRendezvousById('r1'));

    print_r('Après changement');

    $service->changeSPecialite('r1','new_specialite');

    print_r($service->getRendezvousById('r1'));

} catch (\toubeelib\core\services\rdv\ServiceRendezVousIncorrectDataException $e){
    echo 'exception dans le changement d\'une specialite  :' . PHP_EOL;
    echo $e->getMessage(). PHP_EOL;
}

print_r('Test 4 - Marque rdv Honoré ##########################################################"');

try {
    print_r('Avant changement');
    print_r($service->getRendezvousById('r1'));
    print_r('Après changement');
    $service->marquerRendezvousHonore('r1');
    print_r($service->getRendezvousById('r1'));
} catch (\toubeelib\core\services\rdv\ServiceRendezVousIncorrectDataException $e) {
    echo 'exception dans le changement du statut  :' . PHP_EOL;
    echo $e->getMessage(). PHP_EOL;
}

print_r('Test 5 - Annulation Rendez Vous ##########################################################');

try {


    print_r('Avant annulation');
    
    print_r($service->getRendezvousById('r1'));

    print_r('Après annulation');

    $service->annulerRendezvous('r1', 'patient');

    print_r($service->getRendezvousById('r1'));


} catch (\toubeelib\core\services\rdv\ServiceRendezVousIncorrectDataException $e){
    echo 'exception dans la récupération d\'un praticien :' . PHP_EOL;
    echo $e->getMessage(). PHP_EOL;
}