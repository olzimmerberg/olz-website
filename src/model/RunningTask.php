<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="RunningTaskRepository")
 * @ORM\Table(
 *     name="running_tasks",
 * )
 */
class RunningTask {
    /**
     * @ORM\Id @ORM\Column(type="string", nullable=false)
     */
    public $id;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    public $task_class;
    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    public $is_currently_executing;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $state;
    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getTaskClass() {
        return $this->task_class;
    }

    public function setTaskClass($new_task_class) {
        $this->task_class = $new_task_class;
    }

    public function getIsCurrentlyExecuting() {
        return $this->is_currently_executing;
    }

    public function setIsCurrentlyExecuting($new_is_currently_executing) {
        $this->is_currently_executing = $new_is_currently_executing;
    }

    public function getState() {
        return $this->state;
    }

    public function setState($new_state) {
        $this->state = $new_state;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($new_created_at) {
        $this->created_at = $new_created_at;
    }
}
