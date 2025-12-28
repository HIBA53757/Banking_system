CREATE DATABASE bank_system

--@block
use bank_system 

--@block
CREATE TABLE client (
  id INT AUTO_INCREMENT PRIMARY KEY ,
  nom VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL
);

--@block
CREATE TABLE compte(
id INT AUTO_INCREMENT PRIMARY key ,
numero VARCHAR(50) NOT NULL UNIQUE,
solde DECIMAL(15, 2) NOT NULL DEFAULT 0,
type enum('Compte_Courant','Compte_Epargne')NOT NULL,
client_id INT NOT NULL,
FOREIGN KEY(client_id)REFERENCES client(id) ON DELETE CASCADE
);

--@block
CREATE TABLE operation (
id INT AUTO_INCREMENT PRIMARY KEY,
 montant DECIMAL(15,2)NOT NULL,
 type ENUM('Retrait','Depot') NOT NULL,
 date_operation DATETIME NOT NULL  DEFAULT CURRENT_TIMESTAMP,
 compte_id INT  NOT NULL,
 FOREIGN KEY (compte_id)REFERENCES compte(id) ON DELETE CASCADE
);