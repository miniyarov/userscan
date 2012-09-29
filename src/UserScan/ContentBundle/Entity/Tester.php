<?php

namespace UserScan\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserScan\ContentBundle\Entity\Tester
 *
 * @ORM\Table(name="testers", indexes={@ORM\index(columns={"sessionId"})})
 * @ORM\Entity
 */
class Tester
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $sessionId
     *
     * @ORM\Column(name="sessionId", type="string", length=255)
     */
    private $sessionId;

    /**
     * @ORM\ManyToOne(targetEntity="UserScan\ProjectBundle\Entity\Project", inversedBy="testers")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string $age
     *
     * @ORM\Column(name="age", type="string", length=255, nullable=true)
     */
    private $age;

    /**
     * @var string $income
     *
     * @ORM\Column(name="income", type="string", length=255, nullable=true)
     */
    private $income;

    /**
     * @var string $internetSkill
     *
     * @ORM\Column(name="internetSkill", type="string", length=255, nullable=true)
     */
    private $internetSkill;

    /**
     * @var string $userAgent
     *
     * @ORM\Column(name="userAgent", type="text")
     */
    private $userAgent;

    /**
     * @var string $ip
     *
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;

    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="uploaded", type="boolean")
     */
    private $uploaded;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sessionId
     *
     * @param string $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * Get sessionId
     *
     * @return string 
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set age
     *
     * @param string $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * Get age
     *
     * @return string 
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set income
     *
     * @param string $income
     */
    public function setIncome($income)
    {
        $this->income = $income;
    }

    /**
     * Get income
     *
     * @return string 
     */
    public function getIncome()
    {
        return $this->income;
    }

    /**
     * Set internetSkill
     *
     * @param string $internetSkill
     */
    public function setInternetSkill($internetSkill)
    {
        $this->internetSkill = $internetSkill;
    }

    /**
     * Get internetSkill
     *
     * @return string 
     */
    public function getInternetSkill()
    {
        return $this->internetSkill;
    }

    /**
     * Set userAgent
     *
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * Get userAgent
     *
     * @return string 
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set ip
     *
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set project
     *
     * @param UserScan\ProjectBundle\Entity\Project $project
     */
    public function setProject(\UserScan\ProjectBundle\Entity\Project $project)
    {
        $this->project = $project;
    }

    /**
     * Get project
     *
     * @return UserScan\ProjectBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    public function setUploaded($uploaded)
    {
        $this->uploaded = $uploaded;
    }

    public function getUploaded()
    {
        return $this->uploaded;
    }
}