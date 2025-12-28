<?php
abstract class Compte
{
    protected $id;
    protected $numero;
    protected $solde;
    protected $client_id;
    protected $pdo;

    public function __construct($pdo, $numero, $solde, $client_id, $id = null)
    {
        $this->pdo = $pdo;
        $this->numero = $numero;
        $this->solde = $solde;
        $this->client_id = $client_id;
        $this->id = $id;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNumero() { return $this->numero; }
    public function getSolde() { return $this->solde; }
    public function getClientId() { return $this->client_id; }

    // Setters
    public function setNumero($numero) { $this->numero = $numero; }
    public function setSolde($solde) { $this->solde = $solde; }

    // CRUD
    public function create()
    {
        $stmt = $this->pdo->prepare("INSERT INTO compte (numero, solde, type, client_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$this->numero, $this->solde, static::class, $this->client_id]);
        $this->id = $this->pdo->lastInsertId();
    }

    public function update()
    {
        $stmt = $this->pdo->prepare("UPDATE compte SET numero = ?, solde = ? WHERE id = ?");
        $stmt->execute([$this->numero, $this->solde, $this->id]);
    }

    public function delete()
    {
        if ($this->solde != 0) {
            throw new Exception("Impossible de supprimer un compte avec un solde non nul.");
        }
        $stmt = $this->pdo->prepare("DELETE FROM compte WHERE id = ?");
        $stmt->execute([$this->id]);
    }

    // Transactions
    abstract public function deposer($montant);
    abstract public function retirer($montant);

    // Récupérer comptes par client
    public static function getComptesByClient($pdo, $client_id)
    {
        $stmt = $pdo->prepare("SELECT * FROM compte WHERE client_id = ? AND type = ?");
        $stmt->execute([$client_id, static::class]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
