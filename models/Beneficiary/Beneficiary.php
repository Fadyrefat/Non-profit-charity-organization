<?php

class Beneficiary
{
    protected $id;
    protected $name;
    // Removed the update() method as it's not clear in this context

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    // Getters and setters
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}
?>