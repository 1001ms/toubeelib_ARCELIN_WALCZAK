<?php

namespace toubeelib\infrastructure\repositories;

use Ramsey\Uuid\Uuid;
use toubeelib\core\domain\entities\praticien\Praticien;
use toubeelib\core\domain\entities\rdv\RendezVous;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class ArrayRdvRepository implements RdvRepositoryInterface
{

    private array $rdvs = [];

    public function __construct() {

            $r1 = new RendezVous('p1', 'pa1', 'A', \DateTimeImmutable::createFromFormat('Y-m-d H:i','2024-09-02 09:00'));
            $r1->setID('r1');
            $r2 = new RendezVous('p1', 'pa1', 'A', \DateTimeImmutable::createFromFormat('Y-m-d H:i','2024-09-02 10:00'));
            $r2->setID('r2');
            $r3 = new RendezVous('p2', 'pa1', 'A', \DateTimeImmutable::createFromFormat('Y-m-d H:i','2024-09-02 09:30'));
            $r3->setID('r3');

        $this->rdvs  = ['r1'=> $r1, 'r2'=>$r2, 'r3'=> $r3 ];
    }




    public function getRendezvousById(string $id): Rendezvous{
        $rdv = $this->rdvs[$id] ??
        throw new RepositoryEntityNotFoundException("Rendezvous $id not found");

        return $rdv;

    }

    public function getRendezvousByPraticienId(string $id_prat): array {
        $retour = [];
    
        foreach ($this->rdvs as $value) {
            if ($value->praticien === $id_prat) {
                array_push($retour, $value);
            }
        }
    
        return $retour;
    }
    

    public function save(Rendezvous $rdv): string{
        $ID = Uuid::uuid4()->toString();
        $rdv->setID($ID);
        $this->rdvs[$ID] = $rdv;
        return $ID;
    }
    public function update(string $id, array $fields): RendezVous
    {
        if (!isset($this->rdvs[$id])) {
            throw new RepositoryEntityNotFoundException("Le rendez-vous avec l'ID $id n'a pas été trouvé.");
        }
        
        $rdv = $this->rdvs[$id];

        
        if (isset($fields['specialite'])) {
            $rdv->setSpecialite($fields['specialite']);
        }

        if (isset($fields['patient'])) {
            $rdv->setPatient($fields['patient']);
        }

        $this->rdvs[$id] = $rdv;

        return $rdv;
    }
}