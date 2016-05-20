# Catalyst Collective's Injector

This component acts as a adapter for any class and switches the visibility of properties and methods to public without
modifying the targeted object.

### Accessing Properties

```php
use Catalyst\Injector\InjectorFactory;

class GreetUtility {
    private $name = 'World';
    
    protected greet()
    {
        return sprintf('Hello, %s!', $this->name);
    }
}

$instance = new GreetUtility();
$impersonatedInstance = InjectorFactory::createFrom($instance);
$impersonatedInstance->name = 'John Doe';

echo $impersonatedInstance->greet();
```

### Iterating over properties

```php
use Catalyst\Injector\InjectorFactory;

class Foo {
    private $a = 1;
    private $b = 2;
    private $c = 3;
}

$instance = new Foo();
$impersonatedInstance = InjectorFactory::createFrom($instance);

foreach ( $impersonatedInstance as $property => $value ) {
    echo sprintf('Property "%s" with value: %s'.PHP_EOL, $property, $value);
}
```