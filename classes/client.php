<?php
// Contient les entités métier uniquement
// Propriétés privées
// Constructeurs
// Getters / Setters



class Client
{
    private $pdo;
    private $id;
    private $name;
    private $email;

    public function __construct(PDO $pdo, string $name, string $email)
    {
        $this->pdo = $pdo;
        $this->setName($name);
        $this->setEmail($email);
    }
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }
    public function getEmail()
    {
        return $this->email;
    }

    public function setName(string $name)
{
    if (empty($name)) {
        throw new Exception("Nom invalide");
    }
    $this->name = htmlspecialchars($name);
}

     public function setEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email invalide !");
        }
        $this->email = $email;
    }

    private function emailExists($email)
    {
        $db = Database::get_instance()->connection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM client WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    public function save()
    {
        $db = Database::get_instance()->connection();
        if ($this->emailExists($this->email)) {
            throw new Exception("Email déjà utilisé !");
        }
        $sql = "INSERT INTO client (nom, email) VALUES (?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$this->name, $this->email]);
        $this->id = $db->lastInsertId();
    }
    

     public static function show() {
        $db = Database::get_instance()->connection();
        $sql = "SELECT * FROM client";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

       public function update($client_id) {
        $db = Database::get_instance()->connection();
        if ($this->emailExists($this->email)) {
            throw new Exception("Email déjà utilisé !");
        }
        $sql = "UPDATE client SET nom = ?, email = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$this->name, $this->email, $client_id]);
    }

       public static function delete($client_id) {
        $db = Database::get_instance()->connection();
        $sql = "DELETE FROM client WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$client_id]);
    }
}
