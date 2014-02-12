<?php

namespace Selket\EnhController;



interface ControllerInterface
{
    /**
     * Call action
     * @param int $action
     * @param array $parameters
     * @return mixed
     */
    public function callAction($action, $parameters=[]);

    /**
     * Call next action
     * @param int $action
     * @param array $parameters
     * @return mixed
     */
    public function callNextAction($action=0, $parameters=[]);
}