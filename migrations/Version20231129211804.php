<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231129211804 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE coins (id INT AUTO_INCREMENT NOT NULL, actionby_id INT DEFAULT NULL, name VARCHAR(30) NOT NULL, abbr VARCHAR(10) NOT NULL, address VARCHAR(255) NOT NULL, mininvest DOUBLE PRECISION NOT NULL, mindeposit DOUBLE PRECISION NOT NULL, minwithdraw DOUBLE PRECISION NOT NULL, withdrawfee DOUBLE PRECISION NOT NULL, web VARCHAR(255) DEFAULT NULL, inwallet DOUBLE PRECISION DEFAULT NULL, INDEX IDX_E818BD58123EB11 (actionby_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE deposit (id INT AUTO_INCREMENT NOT NULL, userid_id INT NOT NULL, coin_id INT NOT NULL, actionby_id INT DEFAULT NULL, amount DOUBLE PRECISION DEFAULT NULL, date DATETIME NOT NULL, transhash VARCHAR(255) NOT NULL, status VARCHAR(50) NOT NULL, INDEX IDX_95DB9D3958E0A285 (userid_id), INDEX IDX_95DB9D3984BBDA7 (coin_id), INDEX IDX_95DB9D398123EB11 (actionby_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE history (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, description VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, cssclass VARCHAR(255) DEFAULT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE history_user (history_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_86A6A6351E058452 (history_id), INDEX IDX_86A6A635A76ED395 (user_id), PRIMARY KEY(history_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE investment (id INT AUTO_INCREMENT NOT NULL, userid_id INT DEFAULT NULL, coin_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, paid DOUBLE PRECISION NOT NULL, status TINYINT(1) NOT NULL, paid_byday LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', date DATETIME NOT NULL, goal DOUBLE PRECISION NOT NULL, paidpercent DOUBLE PRECISION NOT NULL, INDEX IDX_43CA0AD658E0A285 (userid_id), INDEX IDX_43CA0AD684BBDA7 (coin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, paidby_id INT NOT NULL, percent DOUBLE PRECISION NOT NULL, date DATETIME NOT NULL, INDEX IDX_6D28840DEB65E43D (paidby_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, referal_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, role VARCHAR(20) NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(30) NOT NULL, hash VARCHAR(50) NOT NULL, referrals INT NOT NULL, active TINYINT(1) NOT NULL, emailhash VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, commission LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', lastlogin DATETIME NOT NULL, lastlogout DATETIME NOT NULL, totalearnings LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649C28095E8 (referal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE util (id INT AUTO_INCREMENT NOT NULL, editedby_id INT DEFAULT NULL, paymentby_id INT DEFAULT NULL, uplimit DOUBLE PRECISION NOT NULL, downlimit DOUBLE PRECISION NOT NULL, date DATETIME NOT NULL, lastpaymentdate DATETIME DEFAULT NULL, lastpayment DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_DE66B2A36CFC7E1E (editedby_id), UNIQUE INDEX UNIQ_DE66B2A3D641C76B (paymentby_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wallet (id INT AUTO_INCREMENT NOT NULL, userid_id INT NOT NULL, btc DOUBLE PRECISION NOT NULL, eth DOUBLE PRECISION NOT NULL, usdt DOUBLE PRECISION NOT NULL, ltc DOUBLE PRECISION NOT NULL, xrp DOUBLE PRECISION NOT NULL, bch DOUBLE PRECISION NOT NULL, trx DOUBLE PRECISION NOT NULL, doge DOUBLE PRECISION NOT NULL, dash DOUBLE PRECISION NOT NULL, nano DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_7C68921F58E0A285 (userid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE withdrawl (id INT AUTO_INCREMENT NOT NULL, userid_id INT NOT NULL, coin_id INT NOT NULL, actionby_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, date DATETIME NOT NULL, status VARCHAR(50) NOT NULL, address VARCHAR(255) NOT NULL, INDEX IDX_8104A2E258E0A285 (userid_id), INDEX IDX_8104A2E284BBDA7 (coin_id), INDEX IDX_8104A2E28123EB11 (actionby_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE coins ADD CONSTRAINT FK_E818BD58123EB11 FOREIGN KEY (actionby_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE deposit ADD CONSTRAINT FK_95DB9D3958E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE deposit ADD CONSTRAINT FK_95DB9D3984BBDA7 FOREIGN KEY (coin_id) REFERENCES coins (id)');
        $this->addSql('ALTER TABLE deposit ADD CONSTRAINT FK_95DB9D398123EB11 FOREIGN KEY (actionby_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE history_user ADD CONSTRAINT FK_86A6A6351E058452 FOREIGN KEY (history_id) REFERENCES history (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE history_user ADD CONSTRAINT FK_86A6A635A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE investment ADD CONSTRAINT FK_43CA0AD658E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE investment ADD CONSTRAINT FK_43CA0AD684BBDA7 FOREIGN KEY (coin_id) REFERENCES coins (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DEB65E43D FOREIGN KEY (paidby_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C28095E8 FOREIGN KEY (referal_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE util ADD CONSTRAINT FK_DE66B2A36CFC7E1E FOREIGN KEY (editedby_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE util ADD CONSTRAINT FK_DE66B2A3D641C76B FOREIGN KEY (paymentby_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921F58E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE withdrawl ADD CONSTRAINT FK_8104A2E258E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE withdrawl ADD CONSTRAINT FK_8104A2E284BBDA7 FOREIGN KEY (coin_id) REFERENCES coins (id)');
        $this->addSql('ALTER TABLE withdrawl ADD CONSTRAINT FK_8104A2E28123EB11 FOREIGN KEY (actionby_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE deposit DROP FOREIGN KEY FK_95DB9D3984BBDA7');
        $this->addSql('ALTER TABLE investment DROP FOREIGN KEY FK_43CA0AD684BBDA7');
        $this->addSql('ALTER TABLE withdrawl DROP FOREIGN KEY FK_8104A2E284BBDA7');
        $this->addSql('ALTER TABLE history_user DROP FOREIGN KEY FK_86A6A6351E058452');
        $this->addSql('ALTER TABLE coins DROP FOREIGN KEY FK_E818BD58123EB11');
        $this->addSql('ALTER TABLE deposit DROP FOREIGN KEY FK_95DB9D3958E0A285');
        $this->addSql('ALTER TABLE deposit DROP FOREIGN KEY FK_95DB9D398123EB11');
        $this->addSql('ALTER TABLE history_user DROP FOREIGN KEY FK_86A6A635A76ED395');
        $this->addSql('ALTER TABLE investment DROP FOREIGN KEY FK_43CA0AD658E0A285');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DEB65E43D');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C28095E8');
        $this->addSql('ALTER TABLE util DROP FOREIGN KEY FK_DE66B2A36CFC7E1E');
        $this->addSql('ALTER TABLE util DROP FOREIGN KEY FK_DE66B2A3D641C76B');
        $this->addSql('ALTER TABLE wallet DROP FOREIGN KEY FK_7C68921F58E0A285');
        $this->addSql('ALTER TABLE withdrawl DROP FOREIGN KEY FK_8104A2E258E0A285');
        $this->addSql('ALTER TABLE withdrawl DROP FOREIGN KEY FK_8104A2E28123EB11');
        $this->addSql('DROP TABLE coins');
        $this->addSql('DROP TABLE deposit');
        $this->addSql('DROP TABLE history');
        $this->addSql('DROP TABLE history_user');
        $this->addSql('DROP TABLE investment');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE util');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP TABLE withdrawl');
    }
}
