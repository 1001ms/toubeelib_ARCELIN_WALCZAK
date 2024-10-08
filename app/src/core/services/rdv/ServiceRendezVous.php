<?php

namespace toubeelib\core\services\rdv;


use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeImmutable;

use toubeelib\core\domain\entities\rdv\RendezVous;
use toubeelib\core\dto\InputRendezVousDTO;
use toubeelib\core\dto\RendezVousDTO;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use toubeelib\core\services\rdv\ServiceRendezVousNotDataFoundException;

use Monolog\Logger; // Use the correct namespace for Logger
use Monolog\Handler\StreamHandler; // Use the correct namespace for StreamHandler
use Monolog\Handler\FirePHPHandler; // Use the correct namespace for FirePHPHandler


class ServiceRendezVous implements ServiceRendezVousInterface
{

    //constantes
    const HEURE_RDV_DEBUT = '08:00';
    const HEURE_RDV_FIN = '18:00';
    const INTERVAL_HEURE = 'PT30M';

    //attributs
    private RdvRepositoryInterface $rendezvousRepository;
    private PraticienRepositoryInterface $praticienRepository;


    public function __construct(RdvRepositoryInterface $rdvRepository, PraticienRepositoryInterface $praticienRepository)
    {
        $this->rendezvousRepository = $rdvRepository;
        $this->praticienRepository = $praticienRepository;
    }

    public function creerRendezvous(InputRendezVousDTO $rdv): RendezVousDTO
    {

        //praticien ID valide ?
        if($this->praticienRepository->getPraticienById($rdv->praticien)==null)throw new ServiceRendezVousNotDataFoundException('invalid Praticien ID');

        //Specialite valide ?
        if($this->praticienRepository->getPraticienById($rdv->praticien)->getSpecialite()->getId()!=$rdv->specialite)throw new ServiceRendezVousIncorrectDataException('invalid Specialite');

        $retour = new RendezVous($rdv->praticien, $rdv->patient, $rdv->specialite, new \DateTimeImmutable($rdv->date));

        $this->displayInLogger('Rendez-vous créer : Praticien -> '.$rdv->praticien.' / Patient -> '.$rdv->patient.' / Specialite -> '.$rdv->specialite);

        $this->rendezvousRepository->save($retour);
    
        return new RendezVousDTO($retour);

    }

    
    public function listeDisposPraticien(string $id_prat, string $date_deb, string $date_fin) : array{

        //retour
        $retour = [];

        //day
        $deb_day = new DateTimeImmutable($date_deb);
        $fin_day = new DateTimeImmutable($date_fin);
        $interval_day = new DateInterval('P1D');

        //hour
        $deb_hour = new DateTimeImmutable(self::HEURE_RDV_DEBUT);
        $fin_hour = new DateTimeImmutable(self::HEURE_RDV_FIN);
        $interval_hour = new DateInterval(self::INTERVAL_HEURE);
    
        $period_day = new DatePeriod($deb_day, $interval_day, $fin_day->modify('+1 day'));
    
        foreach ($period_day as $day) {
    
            // Vérifie si le jour est un samedi (6) ou un dimanche (7)
            if (in_array($day->format('N'), [6, 7]))
                continue; // Saute ce jour
            
            $deb_hour_of_day = $day->setTime($deb_hour->format('H'), $deb_hour->format('i'));
            $fin_hour_of_day = $day->setTime($fin_hour->format('H'), $fin_hour->format('i'));
            $period_hour = new DatePeriod($deb_hour_of_day, $interval_hour, $fin_hour_of_day);
    
            foreach ($period_hour as $hour) {
    
                // Exclure la pause de midi (entre 12h00 et 14h00)
                if ($hour->format('H') >= 12 && $hour->format('H') < 14)
                    continue; // Saute cet horaire
    
                if ($this->praticienEstDisponible($id_prat, $hour))
                    array_push($retour, $hour);
                
            }
        }

        return $retour;
    }


    public function planningPraticien(string $id_prat, string $date_deb, string $date_fin) : array {

        $deb_day = new DateTimeImmutable($date_deb);
        $fin_day = new DateTimeImmutable($date_fin);
    
        $retour = [];
    
        $liste_rdvs = $this->rendezvousRepository->getRendezvousByPraticienId($id_prat);
    
        foreach ($liste_rdvs as $rdv) {

            if ($rdv->date >= $deb_day && $rdv->date <= $fin_day) {
                $retour[] = [
                    'date' => $rdv->date->format('Y-m-d H:i:s'),
                    'specialite' => $this->praticienRepository->getSpecialiteById($rdv->specialite),
                ];
            }
        }
    
        return $retour;
    }
    
    

    
    


    public function praticienEstDisponible($id_prat, DateTimeImmutable $date): bool {
        
        $res = true;

        $liste_rdv_prat = $this->rendezvousRepository->getRendezvousByPraticienId($id_prat);

        foreach($liste_rdv_prat as $rdv){

            if($rdv->date == $date)
                $res=false;
        }

        return $res;
    }



    public function changePatient( string $id, string $new_patient ){

        $rdv = $this->rendezvousRepository->getRendezvousById($id);
        
        $rdv->setPatient($new_patient);

        $this->displayInLogger('Le patient du rendez-vous *'.$id.'* devient : *'.$new_patient.'*');

    }


    public function displayInLogger(string $message){
        $logger = new Logger('logger');

        $logger->pushHandler(new StreamHandler('../logs/app.log'));

        $logger->info($message);
    }

    public function changeSpecialite( string $id, string $new_spe ){

        $rdv = $this->rendezvousRepository->getRendezvousById($id);
        
        $rdv->setSpecialite($new_spe);

        $this->displayInLogger('La specialite du rendez-vous *'.$id.'* devient : *'.$new_spe.'*');

    }


    public function getRendezvousById(string $id): RendezVousDTO
    {
        try {
            $rdv = $this->rendezvousRepository->getRendezvousById($id);
            return new RendezVousDTO($rdv);
        } catch(RepositoryEntityNotFoundException $e) {

            throw new ServiceRendezVousNotDataFoundException("Invalid RDV ID", 404, $e);
        }
    }


    public function annulerRendezvous(string $id_rdv, string $annulePar)
    {
        $rdv = $this->rendezvousRepository->getRendezvousById($id_rdv);
        if ($rdv->getStatut() === 'Honoré') {
            throw new \DomainException('Le rendez-vous étant déjà honoré il ne peut pas être annulé');
        }
        if ($annulePar === 'patient') {
            $rdv->setStatut('Annulé par le patient');
        } elseif ($annulePar === 'praticien') {
            $rdv->setStatut('Annulé par le praticien');
        } else {
            throw new \InvalidArgumentException('Annulation invalide');
        }
        $this->rendezvousRepository->save($rdv);
        $this->displayInLogger('Le rendez-vous *'.$id_rdv.'* a été annuler.');
    }

    public function marquerRendezvousHonore(string $id_rdv)
    {
        $rdv = $this->rendezvousRepository->getRendezvousById($id_rdv);
        if ($rdv->getStatut() === 'Annulé par le patient' || $rdv->getStatut() === 'Annulé par le praticien') {
            throw new \DomainException('Le rendez-vous étant déjà annulé il ne peut pas être honoré');
        }
        $rdv->setStatut('Honoré');
        $this->rendezvousRepository->save($rdv);
        $this->displayInLogger('Le rendez-vous *'.$id_rdv.'* a été honorer.');
    }

    public function marquerRendezvousNonHonore(string $id_rdv)
    {
        $rdv = $this->rendezvousRepository->getRendezvousById($id_rdv);
        $rdv->setStatut('Non honoré');
        $this->rendezvousRepository->save($rdv);
        $this->displayInLogger('Le rendez-vous *'.$id_rdv.'* n\'est pas honoré.');
    }

    public function marquerRendezvousPaye(string $id_rdv)
    {
        $rdv = $this->rendezvousRepository->getRendezvousById($id_rdv);
        if ($rdv->getStatut() !== 'Honoré') {
            throw new \DomainException('Le rendez-vous doit être honoré avant d\'être payé.');
        }
        $rdv->setStatut('Payé');
        $this->rendezvousRepository->save($rdv);
        $this->displayInLogger('Le rendez-vous *'.$id_rdv.'* a été payer.');
    }

    public function transmettreAuxOrganismes(string $id_rdv)
    {
        $rdv = $this->rendezvousRepository->getRendezvousById($id_rdv);
        if ($rdv->getStatut() !== 'Payé') {
            throw new \DomainException('Le rendez-vous doit être payé avant transmission aux organismes sociaux.');
        }
        $rdv->setStatut('Transmis aux organismes sociaux');
        $this->rendezvousRepository->save($rdv);
        $this->displayInLogger('Le rendez-vous *'.$id_rdv.'* a été transmis aux organismes sociaux.');

    }





}