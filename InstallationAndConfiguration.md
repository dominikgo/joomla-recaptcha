# Installation #

Install via the standard Joomla installer.


# Configuration #

In order to properly use Recaptcha, you will need to [create an account at the recaptcha site](https://admin.recaptcha.net/accounts/login/?next=/recaptcha/sites/). Once you have finished, copy the public and private keys for your site and then visit the Joomla Administrator.

#### Joomla 1.0 ####
  * Go to Mambots > Site Mambots.
  * Click the System - Recaptcha plugin (you may have to go the last page)

#### Joomla 1.5 ####
  * Go to Extensions > Plugin Manager
  * Click the System - Recaptcha plugin.

The parameters on the right provide textfields for the public and private keys.

### Ajax mode ###
> Ajax mode is enabled by default. It helps avoid problems with
> "Operation Aborted" errors in IE6 and IE7. You can try to change it to Off and see what
> happens.

### Add to Contact Page ###
> Automatically adds recaptcha to the default contact page.
> If you are installing this for use on a Contact Page in Joomla 1.0, please see [this guide](AddToContactPageInJoomla10.md).