# Pheat

## PHP 5.6+ Feature Manager

[![Build Status](https://travis-ci.org/vend/pheat.svg?branch=master)](https://travis-ci.org/vend/pheat)
[![Code Coverage](https://scrutinizer-ci.com/g/vend/pheat/badges/coverage.png?b=master&s=704647138ef760ac320f01d0eb40231f9a4082c3)](https://scrutinizer-ci.com/g/vend/pheat/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/vend/pheat/badges/quality-score.png?b=master&s=7f41f355e36b0f7a733a6291ecac525abc752ed8)](https://scrutinizer-ci.com/g/vend/pheat/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/vend/pheat/v/stable.svg)](https://packagist.org/packages/vend/pheat)
[![Latest Unstable Version](https://poser.pugx.org/vend/pheat/v/unstable.svg)](https://packagist.org/packages/vend/pheat)
[![License](https://poser.pugx.org/vend/pheat/license.svg)](https://packagist.org/packages/vend/pheat)

Pheat is a simple implementation of a feature manager for PHP 5.6+. The main abstractions it uses are:

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

This is also where you'd implement *variants*, *ratios* or *buckets*: more complex ways of assigning features. The `FeatureInterface` ensures the information about why a feature is active or inactive can be interrogated in more detail.

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

### Feature

The `Feature` object holds the status for a named feature. It also holds a reference to the provider which gave the information about the feature.

#### `::resolve()`

When two or more providers give information about the same feature (according to the feature name), their statuses are merged to find the provider which should 'win' (and control the final value of whether the feature is enabled.) This is done by passing the previously controlling feature into the 'new' feature to be merged's `resolve()` method.

The `resolve()` method returns the `Feature` instance that should now be considered in control. This makes it a good place to implement complex logic, like enabling a feature for a ratio or sample of users.

##### Default Resolution

When two features are resolved together, in the default implementation, the `Feature` that will end up controlling the status is shown in bold.

Previous Status | New Status
------------ | -------------
**Active**   | Active
Active       | **Inactive**
**Active**   | Unknown
**Inactive** | Active
**Inactive** | Inactive
**Inactive** | Unknown
Unknown      | **Active**
Unknown      | **Inactive**
**Unknown**  | Unknown
