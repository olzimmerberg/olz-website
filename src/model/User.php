<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\Table(
 *     name="users",
 *     indexes={
 *         @ORM\Index(name="username_index", columns={"username"}),
 *     },
 * )
 */
class User {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    public $id;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    public $username;
    /**
     * @ORM\Column(type="string", nullable=true)
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
     * @ORM\Column(type="boolean", nullable=false)
     */
    public $email_is_verified;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public $email_verification_token;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $first_name;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $last_name;
    /**
     * @ORM\Column(type="string", length=2, nullable=true, options={"comment": "M(ale), F(emale), or O(ther)"})
     */
    public $gender;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public $street;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public $postal_code;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public $city;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public $region;
    /**
     * @ORM\Column(type="string", length=3, nullable=true, options={"comment": "two-letter code (ISO-3166-alpha-2)"})
     */
    public $country_code;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    public $birthdate;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public $phone;
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
    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    protected $created_at;
    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    protected $last_modified_at;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_login_at;

    public function __construct() {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($new_username) {
        $this->username = $new_username;
    }

    public function getOldUsername() {
        return $this->old_username;
    }

    public function setOldUsername($new_old_username) {
        $this->old_username = $new_old_username;
    }

    public function getPasswordHash() {
        return $this->password;
    }

    public function setPasswordHash($new_password_hash) {
        $this->password = $new_password_hash;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($new_email) {
        $this->email = $new_email;
    }

    public function isEmailVerified() {
        return $this->email_is_verified;
    }

    public function setEmailIsVerified($new_email_is_verified) {
        $this->email_is_verified = $new_email_is_verified;
    }

    public function getEmailVerificationToken() {
        return $this->email_verification_token;
    }

    public function setEmailVerificationToken($new_email_verification_token) {
        $this->email_verification_token = $new_email_verification_token;
    }

    public function getFirstName() {
        return $this->first_name;
    }

    public function setFirstName($new_first_name) {
        $this->first_name = $new_first_name;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function setLastName($new_last_name) {
        $this->last_name = $new_last_name;
    }

    public function getFullName() {
        return "{$this->getFirstName()} {$this->getLastName()}";
    }

    public function getGender() {
        return $this->gender;
    }

    public function setGender($new_gender) {
        $this->gender = $new_gender;
    }

    public function getStreet() {
        return $this->street;
    }

    public function setStreet($new_street) {
        $this->street = $new_street;
    }

    public function getPostalCode() {
        return $this->postal_code;
    }

    public function setPostalCode($new_postal_code) {
        $this->postal_code = $new_postal_code;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity($new_city) {
        $this->city = $new_city;
    }

    public function getRegion() {
        return $this->region;
    }

    public function setRegion($new_region) {
        $this->region = $new_region;
    }

    public function getCountryCode() {
        return $this->country_code;
    }

    public function setCountryCode($new_country_code) {
        $this->country_code = $new_country_code;
    }

    public function getBirthdate() {
        return $this->birthdate;
    }

    public function setBirthdate($new_birthdate) {
        $this->birthdate = $new_birthdate;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($new_phone) {
        $this->phone = $new_phone;
    }

    public function getZugriff() {
        return $this->zugriff;
    }

    public function setZugriff($new_zugriff) {
        $this->zugriff = $new_zugriff;
    }

    public function getRoot() {
        return $this->root;
    }

    public function setRoot($new_root) {
        $this->root = $new_root;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($new_created_at) {
        $this->created_at = $new_created_at;
    }

    public function getLastModifiedAt() {
        return $this->last_modified_at;
    }

    public function setLastModifiedAt($new_last_modified_at) {
        $this->last_modified_at = $new_last_modified_at;
    }

    public function getLastLoginAt() {
        return $this->last_login_at;
    }

    public function setLastLoginAt($new_last_login_at) {
        $this->last_login_at = $new_last_login_at;
    }

    public function __toString() {
        $username = $this->getUsername() ?? '-';
        $id = $this->getId() ?? '-';
        return "{$username} (ID:{$id})";
    }
}
