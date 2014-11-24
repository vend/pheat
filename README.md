# Pheat
## Feature manager for PHP 5.4+

### Installation

Pheat uses PSR4 for autoloading. It's available as `vend/pheat` on Packagist.

### Usage

#### Checking for Features

The main instance you'll use for feature management is the `Pheat\Manager`. At
its simplest, the manager tells you whether a feature should be enabled or
disabled:

```php
if ($manager->resolve('fancy_graphics')) {
    // Output the fancy graphics
}
```

The `resolve` method always returns a boolean: true if the feature should be
considered enabled, and false if the feature should not. This is a simple
contract, but the logic that actually makes the decision can be quite
complicated...

#### Providers

Providers tell the manager which features exist, and when they should be
enabled. Providers should be instantiated and added to the manager when you
create it:

```php
$manager->addProvider(new My\Custom\Provider());
```

#### Context

The context is a collection of circumstances under which the feature manager is
running. The information in the context is what the manager uses to decide
whether a feature should be enabled.

So, for example, you might have your end-users' usernames in the context: that
way, you'd be able to manage features for specific users (or across the pool of
users as a percentage).

```php
$manager->addContext('environment', $environment);
$manager->addContext('user',        $session->getUsername());
```



