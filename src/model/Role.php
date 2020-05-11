<?php

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

require_once __DIR__.'/../config/doctrine.php';

/**
 * @ORM\Entity(repositoryClass="RoleRepository")
 * @ORM\Table(name="roles")
 */
class Role {
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
    public $name;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public $parent_role;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public $index_within_parent;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public $featured_index;
    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    public $can_have_child_roles;
    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
     */
    private $users;

    public function __construct() {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getUsers() {
        return $this->users;
    }
}

class RoleRepository extends EntityRepository {
    public function getRolesWithParent($roleId, $limit = 100) {
        if ($roleId === null) {
            $dql = "SELECT r FROM Role r WHERE r.parent_role IS NULL ORDER BY r.index_within_parent ASC";
            $query = $this->getEntityManager()->createQuery($dql);
        } else {
            $dql = "SELECT r FROM Role r WHERE r.parent_role = ?1 ORDER BY r.index_within_parent ASC";
            $query = $this->getEntityManager()->createQuery($dql)->setParameter(1, $roleId);
        }
        $query->setMaxResults($limit);
        return $query->getResult();
    }
}
