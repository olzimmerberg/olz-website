<?php

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

require_once __DIR__.'/../config/doctrine.php';

/**
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\Table(
 *     name="users",
 * )
 */
class User {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    public $id;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $username;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public $old_username;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $password;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $email;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $first_name;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $last_name;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $zugriff;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public $root;
    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @ORM\JoinTable(name="users_roles")
     */
    private $roles;

    public function __construct() {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getFirstName() {
        return $this->first_name;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function getFullName() {
        return "{$this->getFirstName()} {$this->getLastName()}";
    }
}

class UserRepository extends EntityRepository {
    public function getUsersForRole($roleId) {
        $dql = "SELECT r, u FROM Role r JOIN r.users u ORDER BY u.first_name, u.last_name ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
}
