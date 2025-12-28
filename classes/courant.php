<?php
require_once "compte.php";

class Compte_Courant extends Compte
{
    const FRAIS_DEPOT = 1;
    const DECOUVERT_MAX = -500;

    public function deposer($montant)
    {
        $this->solde += ($montant - self::FRAIS_DEPOT);
        $this->update();
    }

    public function retirer($montant)
    {
        if (($this->solde - $montant) < self::DECOUVERT_MAX) {
            throw new Exception("Retrait refusé : découvert maximum atteint.");
        }
        $this->solde -= $montant;
        $this->update();
    }
}
?>
