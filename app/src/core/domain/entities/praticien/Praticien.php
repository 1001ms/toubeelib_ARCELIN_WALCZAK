<?php

namespace toubeelib\core\domain\entities\praticien;

use PHPUnit\Util\PHP\JobRunner;
use toubeelib\core\domain\entities\Entity;
use toubeelib\core\dto\PraticienDTO;

class Praticien extends Entity
{
    protected string $nom;
    protected string $prenom;
    protected string $adresse;
    protected string $tel;

    protected array $vacance = [];
    protected array $jour_non_travail_recurrent = [];
    protected string $heure_debut = '08:00', $heure_fin = '18:00';

    protected ?Specialite $specialite = null; // version simplifiée : une seule spécialité

    public function __construct(string $nom, string $prenom, string $adresse, string $tel)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->adresse = $adresse;
        $this->tel = $tel;
    }


    public function setSpecialite(Specialite $specialite): void
    {
        $this->specialite = $specialite;
    }


    public function setHeureDebut(string $heure): void
    {
        $this->heure_debut = $heure;
    }


    public function addVacance(string $jour_debut, string $jour_fin){

    }


    public function addJourNonTravail(int $jour){
        if(!in_array($jour,$this->jour_non_travail_recurrent))
            array_push($this->jour_non_travail_recurrent, $jour);

    }

    public function setHeureFin(string $heure): void
    {
        $this->heure_fin = $heure;
    }

    public function getSpecialite(): Specialite
    {
        return $this->specialite;
    }

    public function toDTO(): PraticienDTO
    {
        return new PraticienDTO($this);
    }
}