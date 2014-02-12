<?php

namespace Selket\EnhController;



class Mapper
{
    /**
     * Action class map
     *
     * @var array
     */
    protected $map = [];

    /**
     * Flip map
     *
     * @var array
     */
    protected $flip = [];

    /**
     * Action map attributes
     *
     * @var array
     */
    protected $attr = [];

	/**
	 * @return array
	 */
	public function get()
	{
		return $this->map;
	}

	/**
	 * @param array $map
	 * @return \Selket\EnhController\Mapper
	 */
	public function set(array $map)
	{
		$this->map = array();
		return $this->add($map);
	}

	/**
	 * @param array $map
	 * @return \Selket\EnhController\Mapper
	 */
	public function add(array $map)
	{
		foreach ($map as $action => $class)
			$this->setAction($action, $class);
		return $this->prepare();
	}

	/**
	 * @param int $action
	 * @param string $class
	 * @return \Selket\EnhController\Mapper
	 */
	public function setAction($action, $class)
	{
		if (is_array($class))
		{
			$this->attr[$action] = $class;
			if (isset($class[0]))
				$class = $class[0];
		}

		$this->map[$action] = $class;

		return $this;
	}

	/**
	 * @param int $action
	 * @return string | null
	 */
	public function getClass($action)
	{
		return (isset($this->map[$action])) ? $this->map[$action] : null;
	}

	/**
	 * @param string $class
	 * @return int
	 */
	public function getAction($class)
	{
		return (isset($this->flip[$class])) ? $this->flip[$class] : 0;
	}

	/**
	 * @param int $action
	 * @param string | null $name
	 * @return mixed
	 */
	public function getAttr($action, $name=null)
	{
		if (isset($this->attr[$action]))
		{
			if (!is_null($name))
				return (isset($this->attr[$action][$name])) ? $this->attr[$action][$name] : null;
			else
				return $this->attr[$action];
		}

		return null;
	}

	/**
	 * @param int $action
	 * @return bool
	 */
	public function existsAction($action)
	{
		return isset($this->map[$action]);
	}

	/**
	 * @param int $more
	 * @return int
	 */
	public function getNextAction($more=0)
	{
		foreach ($this->flip as $action)
			if ($action > $more)
				return $action;

		return 0;
	}

	/**
	 * @return \Selket\EnhController\Mapper
	 */
	protected function prepare()
	{
		reset($this->map);
		ksort($this->map);
		$this->flip = array_flip($this->map);
		return $this;
	}
}