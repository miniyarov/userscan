<?php

namespace UserScan\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserScan\ProjectBundle\Entity\Task
 *
 * @ORM\Table(name="tasks")
 * @ORM\Entity
 */
class Task
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="tasks")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;


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
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
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
}