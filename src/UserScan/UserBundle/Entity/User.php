<?php

namespace UserScan\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Entity
* @ORM\Table(name="users",
 *  indexes={ @ORM\index(name="", columns={"email", "recoverHash", "activationHash"}) })
*/
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
    * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @Assert\Email(message = "Please enter you email")
    * @Assert\NotBlank(message = "Please enter your email")
    *
    */
    protected $username;

    /**
     * @ORM\OneToMany(targetEntity="UserScan\ProjectBundle\Entity\Project", mappedBy="user")
     */
    protected $projects;

    /**
     * @ORM\Column(name="roles", type="array")
     */
    protected $roles;

    /**
     * @ORM\Column(name="salt", type="string", length=255)
     */
    protected $salt;

    /**
    * @ORM\Column(name="password", type="string", length=255)
    * @Assert\NotBlank(message = "Please enter your password")
    */
    protected $password;

    /**
     * @ORM\Column(name="fullname", type="string", length=255)
     * @Assert\NotBlank(message = "Please enter your fullname")
     */
    protected $fullname;

    /**
     * @ORM\Column(name="recoverHash", type="string", nullable=true, unique=true, length=255)
     */
    protected $recoverHash;

    /**
     * @ORM\Column(name="activationHash", type="string", nullable=true, unique=true, length=255)
     */
    protected $activationHash;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive;

    public function __construct()
    {
        $this->isActive = false;
        $this->salt = md5(time());//hash('sha512', time());
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function equals(UserInterface $user)
    {
        return $this->username === $user->getUsername();
    }

    public function eraseCredentials()
    {

    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getPassword()
    {
        return $this->password;
    }


    /**
     * Get id
     *
     * @return bigint 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Set salt
     *
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Set fullname
     *
     * @param string $fullname
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }

    /**
     * Get fullname
     *
     * @return string 
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set recoverHash
     *
     * @param string $recoverHash
     */
    public function setRecoverHash($recoverHash)
    {
        $this->recoverHash = $recoverHash;
    }

    /**
     * Get recoverHash
     *
     * @return string 
     */
    public function getRecoverHash()
    {
        return $this->recoverHash;
    }

    public function setActivationHash($activationHash)
    {
        $this->activationHash = $activationHash;
    }

    public function getActivationHash()
    {
        return $this->activationHash;
    }

    /**
     * Add projects
     *
     * @param ProjectBundle\Entity\Project $projects
     */
    public function addProject(\UserScan\ProjectBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;
    }

    /**
     * Get projects
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Set roles
     *
     * @param string $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }
}