# Objective PHP / Matcher [![Build Status](https://secure.travis-ci.org/objective-php/matcher.png?branch=master)](http://travis-ci.org/objective-php/matcher)

## Library topic

Matcher is a simple but powerful key matching engine meant for components using key based identification (events handler, dependencies injector...)

The idea behind os to offer a flexible and performing way to match individual keys as well as subsets in a collection. The first use case is our own EventHandler component. We thought that using the Matcher to attach callbacks to events would lead to a much more uniform and flexible mechanism than any other event handling component.

## Concept


### Sections

The identifiers that Matcher can work with are supposed to be built by concatenating `sections`, more or less as namespaces work:

```php
$id1 = 'key'; // root level key
$id2 = 'section.key'; // namespaced section
$id3 = 'section.sub-section.key'; // multi-level namespaced key
$id4 = 'section.other-sub-section.key'; // other multi-level namespaced key
```

Sections are, by default, separated by a dot. This is however configurable in the component itself. Keys are supposed to be lowered case, but that's not mandatory. Just a good practice to keep keys uniform.

Matcher is aware of the section concept, and will rely on it when dealing with wildcards (see next chapter).
  
### Wildcards

When comparing an identifier to another, either can contain one or more wildcards to symbolize sections: a question mark (?) will replace one section or key, while a star (*) will replace any number of sections and/or key.

### Alternatives
 
In some case, wildcards are kindof too flexible... to restrict matching pattern, you can also use alternatives by providing several values for a single section of the identifier using the square brackets ([ and ])
 
## Usage
 
Now that you are aware of what both section and wildcards are, let's see some examples, using realistic event identifiers coming from an MVC workflow sample:

```php
$matcher = new Matcher();

$matcher->match('engine.extensions.load', 'engine.extensions.load'); // returns TRUE, of course
$matcher->match('engine.extensions.load', 'engine.?.load'); // also returns TRUE
$matcher->match('engine.resource.load', 'engine.?.load'); // returns TRUE as well
$matcher->match('engine.extensions.load', '?.load'); // returns FALSE, since the question mark only replaces one section!
$matcher->match('engine.extensions.load', '*.load'); // returns TRUE, since the star replaces any number of sections sections!
```

In the above example, you can see how the Matcher can help easily binding a callback to several events at once. And if you need to me bore selective, this is where alternatives can help you:

```php
$matcher->match('engine.extensions.load', 'engine.[extensions|resource].load'); // also returns TRUE
$matcher->match('engine.resource.load', 'engine.[extensions|resource].load'); // returns TRUE as well
$matcher->match('engine.service.load', 'engine.[extensions|resource].load'); // this one returns FALSE
```

To see more example, please take a look at the unit tests!

## Performance

The matching algorithm has been rewritten several times to reach more than decent performances. With dozen of thousands call looping on large identifiers stack (also several thousands entries, including wildcards), the total user time consumed was above 100ms... Given that this is really improbable that a real application make use of that many identifiers (at least, for events or services for instance), we consider that the matching engine is very transparent from a performance perspective.
 
 
