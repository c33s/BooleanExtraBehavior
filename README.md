# BooleanExtraBehavior

[![Build Status](https://secure.travis-ci.org/havvg/BooleanExtraBehavior.png?branch=master)](http://travis-ci.org/havvg/BooleanExtraBehavior)

See the Propel documentation on how to [install a third party behavior](http://propelorm.org/documentation/07-behaviors.html#using_thirdparty_behaviors)

## Usage

Just add the following XML tag in your `schema.xml` file:

```xml
<behavior name="boolean_extra" />
```

The behavior will add additional methods for `boolean` columns. Those methods are solely for the purpuse of better readibility when writing code.
