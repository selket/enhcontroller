<?php

namespace Selket\EnhController;



abstract class Action
{
    /**
     * Controller instance called the action
     *
     * @var \Selket\EnhController\ControllerInterface
     */
    public $controller;

    /**
     * Constructor
     *
     * @param ControllerInterface $controller
     */
    public function __construct(ControllerInterface $controller=null)
   	{
   		$this->controller = $controller;
   	}

    /**
     * Call action
     *
     * @return mixed
     */
    abstract public function call();
}