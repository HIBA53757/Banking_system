<?php
require_once "compte.php";

class Compte_Epargne extends Compte
{
    public function deposer($montant)
    {
        $this->solde += $montant;
        $this->update();
    }

    public function retirer($montant)
    {
        if ($this->solde < $montant) {
            throw new Exception("Retrait refusé : solde insuffisant.");
        }
        $this->solde -= $montant;
        $this->update();
    }
}
///
?>

