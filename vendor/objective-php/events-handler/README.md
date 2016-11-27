# Objective PHP / Events Handler [![Build Status](https://secure.travis-ci.org/objective-php/events-handler.png?branch=master)](http://travis-ci.org/objective-php/events-handler)

## Library topic

Simple events handler meant to work together with our objective-php/matcher.

It allows to bind events using patterns as defined in Matcher documentation (using wildcards and alternatives...)

## Concept

Nothing really new here from the events handling point of view. The real specific feature is more related to the way the callbacks are bound to events, thanks to Matcher.

## Usage

### Callback binding

Binding a callback to an event is quite straight forward:

```php
$eventsHandler = new EventsHandler();
$eventsHandler->bind('event.name', function($event) { 
    echo 'Event ' . $event->getName() . ' was just fired!');
    }
  );
```

### Event triggering

Once again, this is very simple:

```php
$eventsHandler->trigger('event.name'); // will echo 'Event event.name was just fired!'
```

More documentation to come soon!
