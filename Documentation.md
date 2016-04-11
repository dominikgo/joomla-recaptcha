# Introduction #

The Recaptcha plugin can be used in your own components easily. The only requirement is that the plugin is installed and enabled.

The API is exposed through a singleton object, `ReCaptcha`. The processing happens automatically, so all that you need to do as a programmer is access its properties. All properties are accessed through the `get` method.

## Methods ##

### `get` ###

This method must be called statically. An example would be:
```
ReCaptcha::get('html');
```

## Properties ##

### `html` ###
The html string to add to your form.

### `submitted` ###
A boolean that indicates whether or not the form which the ReCaptcha is in has been submitted.

### `success` ###
A boolean that indicates if the user entered the phrase correctly.