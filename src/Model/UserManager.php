<?php

namespace App\Model;

class UserManager extends AbstractManager
{
    public const TABLE = 'user';

    public function selectOneByPseudo($pseudo, $like = false)
    {
        $pseudo = ucfirst(strtolower($pseudo));

        // prepared request
        $query = 'SELECT * FROM ' . self::TABLE . ' WHERE pseudo=:pseudo';
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':pseudo', $pseudo, \PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetch();
    }

    public function searchPseudo(string $pseudo)
    {
        $pseudo = ucfirst(strtolower($pseudo));

        $query = 'SELECT * FROM ' . self::TABLE . ' WHERE pseudo LIKE :pseudo';
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':pseudo', $pseudo . '%', \PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function getAvatarById($id)
    {
        $query = 'SELECT image FROM ' . self::TABLE .
                ' JOIN avatar ON avatar.id = ' . self::TABLE . '.avatar_id 
            WHERE ' . self::TABLE . '.id = :id';
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $id, \PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }

    public function isUsed(string $pseudo): bool
    {
        $query = 'SELECT pseudo FROM ' . self::TABLE . ' WHERE pseudo = :pseudo';
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':pseudo', $pseudo);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if (gettype($result) !== 'boolean') {
            $result = true;
        }
        return $result;
    }

    public function create(array $userData): array
    {
        $errors = [];
        if ($this->isUsed($userData['pseudo'])) {
            $errors['pseudo'] = 'Ce pseudo est déjà utilisé';
        } else {
            $query = 'INSERT INTO ' . self::TABLE . ' (pseudo, password, created_at, updated_at) 
            VALUES (:pseudo, :password, NOW(), NOW())';
            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':pseudo', $userData['pseudo'], \PDO::PARAM_STR);
            $statement->bindValue(':password', $userData['password'], \PDO::PARAM_STR);
            $statement->execute();
        }
        return $errors;
    }

    public function updateAvatarId($userId, $avatarId): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `avatar_id` = :avatar_id WHERE id=:id");
        $statement->bindValue('avatar_id', $avatarId, \PDO::PARAM_INT);
        $statement->bindValue('id', $userId, \PDO::PARAM_INT);

        return $statement->execute();
    }
    public function getIdByPseudo(string $pseudo)
    {
        $query = "SELECT id FROM user WHERE pseudo = :pseudo";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue('pseudo', $pseudo, \PDO::PARAM_STR);
        return $statement->fetch(\PDO::FETCH_COLUMN);
    }
}
