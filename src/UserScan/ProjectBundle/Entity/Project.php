<?php

namespace UserScan\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="projects", indexes={@ORM\index(columns={"url_id"})})
 */

class Project
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="url_id", type="string", length=255, unique=true)
     */
    protected $url_id;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(name="url", type="text")
     */
    protected $url;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="project", cascade={"remove"})
     */
    protected $tasks;

    /**
     * @ORM\OneToMany(targetEntity="UserScan\ContentBundle\Entity\Tester", mappedBy="project", cascade={"remove"})
     */
    protected $testers;

    /**
     * @ORM\ManyToOne(targetEntity="UserScan\UserBundle\Entity\User", inversedBy="projects")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(name="scenario", type="text", nullable=true)
     */
    protected $scenario;

    /**
     * @ORM\Column(name="completion_message", type="text", nullable=true)
     */
    protected $completion_message;

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
     * Set url
     *
     * @param text $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return text 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set scenario
     *
     * @param text $scenario
     */
    public function setScenario($scenario)
    {
        $this->scenario = $scenario;
    }

    /**
     * Get scenario
     *
     * @return text 
     */
    public function getScenario()
    {
        return $this->scenario;
    }

    /**
     * Set completion_message
     *
     * @param text $completionMessage
     */
    public function setCompletionMessage($completionMessage)
    {
        $this->completion_message = $completionMessage;
    }

    /**
     * Get completion_message
     *
     * @return text 
     */
    public function getCompletionMessage()
    {
        return $this->completion_message;
    }

    /**
     * Set url_id
     *
     * @param string $urlId
     */
    public function setUrlId($urlId)
    {
        $this->url_id = $urlId;
    }

    /**
     * Get url_id
     *
     * @return string 
     */
    public function getUrlId()
    {
        return $this->url_id;
    }
    public function __construct()
    {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add tasks
     *
     * @param UserScan\ProjectBundle\Entity\Task $tasks
     */
    public function addTasks(\UserScan\ProjectBundle\Entity\Task $tasks)
    {
        $this->tasks[] = $tasks;
    }

    /**
     * Get tasks
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Add tasks
     *
     * @param UserScan\ProjectBundle\Entity\Task $tasks
     */
    public function addTask(\UserScan\ProjectBundle\Entity\Task $tasks)
    {
        $this->tasks[] = $tasks;
    }

    /**
     * Set user
     *
     * @param UserBundle\Entity\User $user
     */
    public function setUser(\UserScan\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add testers
     *
     * @param UserScan\ContentBundle\Entity\Tester $testers
     */
    public function addTester(\UserScan\ContentBundle\Entity\Tester $testers)
    {
        $this->testers[] = $testers;
    }

    /**
     * Get testers
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTesters()
    {
        return $this->testers;
    }
}