<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class OlzEntity {
    /**
     * @ORM\Column(type="integer", options={"default": 1})
     */
    protected $on_off;
    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="owner_user_id", referencedColumnName="id", nullable=true)
     */
    protected $owner_user;
    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="owner_role_id", referencedColumnName="id", nullable=true)
     */
    protected $owner_role;
    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    protected $created_at;
    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by_user_id", referencedColumnName="id", nullable=true)
     */
    protected $created_by_user;
    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    protected $last_modified_at;
    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="last_modified_by_user_id", referencedColumnName="id", nullable=true)
     */
    protected $last_modified_by_user;
}
