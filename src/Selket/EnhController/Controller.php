<?php

namespace Selket\EnhController;

use Exception, ReflectionClass;
use Illuminate\Routing\Controller as IlluminateRoutingController;
use Symfony\Component\HttpFoundation\Response;
use App;



abstract class Controller extends IlluminateRoutingController implements ControllerInterface
{
    /**
     * Controller name (by default a class name formatted in underscore present)
     *
     * @var String
     */
    public $name;

    /**
     * Controller class name
     *
     * @var string
     */
    public $className;

    /**
     * Mapped action ID
     *
     * @var int
     */
    public $action = 0;

    /**
     * ActionID-className relation holder: id -> action class name
     *
     * @var \Selket\EnhController\Mapper
     */
    public $mapper;

    /**
     * Action map array passing to mapper
     *
     * @var null|array
     */
    public $map;

    /**
     * A response of the action execution
     *
     * @var \Symfony\Component\HttpFoundation\Response
     */
    public $response;

    /**
     * Constructor
     *
     * @param \Selket\EnhController\Mapper $mapper
     */
    function __construct(Mapper $mapper)
	{
		$this->className = get_class($this);

		if (is_null($this->name))
            $this->name = str_replace('\\', '', snake_case($this->className));

        $this->mapper = $mapper;
        if (!is_null($this->map))
            $this->mapper->set($this->map);
	}

    /**
     * Call action
     *
     * @param int $action
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function callAction($action, $parameters=array())
	{
        if (is_null($action))
            throw new Exception('No actions in class '.$this->className);

        $this->action = $action;

        $response = $this->beforeAction();

        if (is_null($response))
        {
            $this->setupLayout();

            $response = $this->afterAction($this->getAction($this->action, $parameters));
        }

        return $response;
	}

    /**
     * Call next action in order increase
     *
     * @param int $action
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callNextAction($action=0, $parameters=array())
	{
		return $this->callAction($this->mapper->getNextAction($action), $parameters);
	}

    /**
     * Execute and response action
     *
     * @param int $action
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction($action, $parameters=array())
	{
        return $this->getActionResponse($this->getActionInstance($action,$parameters));
    }

    /**
     * Get action instance
     *
     * @param int $action
     * @param array $parameters
     * @return \Selket\EnhController\Action
     * @throws \Exception
     */
    public function getActionInstance($action, $parameters=array())
	{
		$actionClassName = $this->getActionClassName($action);

		if (!$actionClassName)
			throw new Exception('Action #'.$action.' not found in class '.$this->className);

        if (empty($parameters))
            $parameters = [$this];

		return App::make($actionClassName,$parameters);
	}

    /**
     * Proceed action response
     *
     * @param \Selket\EnhController\Action $actionInstance
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getActionResponse(Action $actionInstance)
	{
        $response = $actionInstance->call();

        if (is_null($response) && !is_null($this->layout))
            $response = $this->layout;

        return ($response instanceof Response) ? $response : Response::create($response);
    }

    /**
     * Before action event handler
     *
     * @return null
     */
    protected function beforeAction()
    {
        return null;
    }

    /**
     * After action event handler
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function afterAction(Response $response)
    {
        return $response;
    }

    /**
     * Find action class name in this class and parents through action ID
     *
     * @param int $action
     * @return bool|null|string
     */
    protected function getActionClassName($action)
	{
		$actionClassName = $this->mapper->getClass($action);

		if (class_exists($actionClassName))
        {
            return $actionClassName;
        }
		else
		{
			$parents = class_parents($this);

			foreach ($parents as $class)
			{
                $reflector = new ReflectionClass($class);

                if ($reflector->implementsInterface('Selket\EnhController\ControllerInterface') && !$reflector->isAbstract())
                {
					$actionClassName = App::make($class)->mapper->getClass($action);

					if (class_exists($actionClassName))
                    {
                        return $actionClassName;
                    }
				}
			}
		}

		return false;
	}

    /**
     * Call action by ID or class name
     *
     * @param mixed $method
     * @param array $args
     * @return mixed|Response
     */
    public function __call($method, $args)
   	{
        if (is_numeric($method))
        {
            $action = $method;
        }
        elseif(strpos($method,'\\') === false)
        {
            $actionClassName = substr($this->className,0,strrpos($this->className,'\\')+1).$method;
            $action = $this->mapper->getAction($actionClassName);
        }
        else
        {
            $action = $this->mapper->getAction($method);
        }

   		return $this->callAction($action);
   	}
}