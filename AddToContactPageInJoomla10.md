# Introduction #

In order to add a captcha to the Contact page in Joomla 1.0, you must add an event in your template file.

# Details #

The 1.0 implementation depends on a custom event 'onTemplateDisplay'. If you would like a captcha on a standard Joomla contact page, you must first enable the feature in the plugin parameters, and then add the following line at the beginning of your templates **index.php** file:

`<?php $_MAMBOTS->trigger('onTemplateDisplay'); ?>`