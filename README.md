EnhController
=============

Enhanced controller for Laravel 4


# Usage #


Declare controller class

```
namespace MyNamespace;

use Selket\EnhController;

Class MyController extends EnhController\Controller implements EnhController\ControllerInterface {

	public $some = "World";

	public $map = [
		1 => 'MyNamespace\MyAction1',
		2 => 'MyNamespace\MyAction2',
	];

    	function __construct(EnhController\Mapper $mapper)
	{
		parent::__construct($mapper);
	}
}
```

Declare accepted action classes

```
namespace MyNamespace;

use Selket\EnhController;

Class MyAction1 extends EnhController\Action implements EnhController\ActionInterface {

	public $my = 1;

	public function call() {
		return 'Hello ' . $this->controller->some . ' ' .$my;
	}
}

Class MyAction2 extends EnhController\Action implements EnhController\ActionInterface {

	protected $my = 2;

	public function call() {
		return 'Hello ' . $this->controller->some . ' ' .$my;
	}
}
```

Define routing

```
// call action by class name
Route::get('foo', 'MyNamespace\MyController@MyNamespace\MyAction2');

// call action by numeric key
Route::get('foo', 'MyNamespace\MyController@2');
```
