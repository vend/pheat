# Pheat

## PHP 5.4+ Feature Manager

[![Latest Stable Version](https://poser.pugx.org/vend/pheat/v/stable.svg)](https://packagist.org/packages/vend/pheat) [![Latest Unstable Version](https://poser.pugx.org/vend/pheat/v/unstable.svg)](https://packagist.org/packages/vend/pheat) [![License](https://poser.pugx.org/vend/pheat/license.svg)](https://packagist.org/packages/vend/pheat)

Pheat is a simple implementation of a feature manager for PHP 5.4+. The main abstractions it uses are:

* A `Provider` knows about the status of a list of `Feature` instances
* A `Provider` can vary its list of features based on the `Context`
* A `Manager` can tell you if a specific feature is active or inactive, based on a list of `Provider` instances, by merging their statuses

## Installation

Pheat uses PSR4 for autoloading. It's available as `vend/pheat` on Packagist.

## Usage

### Checking for Features

The main instance you'll use for feature management is the `Pheat\Manager`. At its simplest, the manager tells you whether a feature should be treated as active or inactive:

```php
if ($manager->resolve('fancy_graphics')) {
    // Output the fancy graphics
}
```

#### Boolean Semantics of Status

The `resolve` method always returns a boolean or null:
 * `true` if the feature should be considered active
 * `false` if the feature should be considered inactive
 * `null` if nothing is known about the state of the feature

Most of the time, if nothing is known about a feature, you don't want to enable it. So, you can just do a loose "falsish" check on the return value from `resolve`.


#### Advanced Status Information

If you call the manager's `resolveFeature` method you'll receive a `FeatureInterface` instance (rather than a status value). This can be helpful if you need to know *why* a feature is active, because it can tell you which provider is marking it as active.

## Configuration

### Manager

When you create a `Manager`, you'll usually give it a `Context` and a list of `Providers`.

#### Context

The `Context` is a collection of circumstances under which the feature manager is running. The information in the context is what the manager uses to decide whether a feature should be enabled. So, for example, you might have your end-users' usernames in the context: that way, you'd be able to manage features for specific users (or across the pool of users as a percentage).

The `Context` is passed to the `Manager`. Once the `Manager` resolves the current feature list once (and caches it), the manager is locked, and changes to the context won't affect the features. Create a new `Manager` to refresh the features.

You can use the default `Context` implementation (which is a simple array-backed attribute bag), or your own implementation of `ContextInterface`.

#### Providers

Providers tell the manager which features exist, and when they should be enabled. Providers must implement `Pheat\ProviderInterface`. Providers should be instantiated and added to the manager either when you create it:

```php
$manager = new Pheat\Manager($context, [
    new My\Custom\Provider(),
    new My\Other\Provider()
]);
```

Or after it's created:

```php
$manager->addProvider(new My\Custom\Provider())
```

Providers are kept and processed in an ordered list (so the order in which you call `addProvider` does matter).







