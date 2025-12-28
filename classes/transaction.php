<?php

class Transaction
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function deposer(Compte $compte, float $montant): void
    {
        if ($montant <= 0) {
            throw new Exception("Montant invalide");
        }

        try {
            $this->pdo->beginTransaction();

            // update balance
            $nouveauSolde = $compte->getSolde() + $montant;
            $sql = "UPDATE compte SET solde = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nouveauSolde, $compte->getId()]);

            // insert 
            $sql = "INSERT INTO operation (montant, type, compte_id)
                    VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$montant, 'Depot', $compte->getId()]);

            $this->pdo->commit();

            $compte->setSolde($nouveauSolde);

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erreur dépôt : " . $e->getMessage());
        }
    }

    public function retirer(Compte $compte, float $montant): void
    {
        if ($montant <= 0) {
            throw new Exception("Montant invalide");
        }

        if ($compte->getSolde() < $montant) {
            throw new Exception("Solde insuffisant");
        }

        try {
            $this->pdo->beginTransaction();

            // update balance
            $nouveauSolde = $compte->getSolde() - $montant;
            $sql = "UPDATE compte SET solde = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nouveauSolde, $compte->getId()]);

            // insert
            $sql = "INSERT INTO operation (montant, type, compte_id)
                    VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$montant, 'Retrait', $compte->getId()]);

            $this->pdo->commit();

            $compte->setSolde($nouveauSolde);

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erreur retrait : " . $e->getMessage());
        }
    }
}
