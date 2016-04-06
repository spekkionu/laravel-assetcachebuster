[![Build Status](https://travis-ci.org/spekkionu/laravel-assetcachebuster.png?branch=master)](https://travis-ci.org/spekkionu/laravel-assetcachebuster)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/spekkionu/laravel-assetcachebuster/badges/quality-score.png?s=3f8e68ec5a903cbe6934fe6acfc40c0a751ec3f6)](https://scrutinizer-ci.com/g/spekkionu/laravel-assetcachebuster/)
[![Code Coverage](https://scrutinizer-ci.com/g/spekkionu/laravel-assetcachebuster/badges/coverage.png?s=6466dda4d16f042564234456347581aca7cae9dc)](https://scrutinizer-ci.com/g/spekkionu/laravel-assetcachebuster/)

Asset Cache Buster
==================

This package prefixes asset urls with md5 hashes so that changing the hash results in the url for all asset files to change.
This forces browsers to download new versions of the asset files before the browser cache is expired.

This is especially useful when using a CDN that sets far future expires headers on files.

It works with any static files like stylesheets, javascript files, and images.

Installation
============

The 2.x branch is only compatible with Laravel 5.x. 
If you need Laravel 4.x compatibility use the 1.x branch.

Add `spekkionu\assetcachebuster` as a requirement to composer.json:

```javascript
{
    "require": {
        "spekkionu/assetcachebuster": "2.*"
    }
}
```

Update your packages with `composer update` or install with `composer install`.

Once Composer has installed or updated your packages you need to register the service provider with the application.
Open up `config/app.php` and find the `providers` key.

```php
'providers' => array(
    Spekkionu\Assetcachebuster\AssetcachebusterServiceProvider::class,
)
```

In order to generate an asset url the asset url generation facade must be registered.
You can register the facade via the `aliases` key of your `config/app.php` file.

```php
'aliases' => array(
    'Asset' => Spekkionu\Assetcachebuster\Facades\Cachebuster::class
)
```

There are other packages that want to register the Asset facade.  If this is the case you can change the Asset key to be something else
to prevent a collision.

For the asset urls to function the following will need to be added to the apache .htaccess file.
Add the following to your .htaccess file **before** the Laravel rewrite rule:

```ApacheConf
# ------------------------------------------------------------------------------
# | Filename-based cache busting                                               |
# ------------------------------------------------------------------------------

# Rewrite assets/hash/file.js to assets/file.js
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Remove prefix from asset files
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^[a-f0-9]{32}/(.*)$ $1 [L]

</IfModule>
```

For Nginx, add the following to your virtual host file

```Nginx
# ------------------------------------------------------------------------------
# | Filename-based cache busting                                               |
# ------------------------------------------------------------------------------
# Rewrite assets/hash/file.js to assets/file.js
location ~* "^\/[a-f0-9]{32}(\/*\.*(.*))$" {
    try_files $uri $1;
}
```

Configuration
=============

In order to generate new hashes to invalidate the cache you must publish the package configuration by running the following artisan command.

```
php artisan vendor:publish --provider="Spekkionu\Assetcachebuster\AssetcachebusterServiceProvider" --tag=config
```

This will create a config file at `config/assetcachebuster.php`
Use this file to configure the package.

Set the `enabled` flag in the config to `true` in order for asset urls to be prefixed.

Usage
=====

For any asset urls you want to be able to cache you must use the `Asset::url($url)` facade rather than directly outputting the url.
For example if you wanted to link to a stylesheet located at `/css/stylesheet.css` you would add the following to your view
```html
<link rel="stylesheet" href="<?php echo Asset::url('/css/stylesheet.css');?>">
```

If you are using a blade template the following will work instead.
```html
<link rel="stylesheet" href="{{ Asset::url('/css/stylesheet.css') }}">
```

Invalidating the Cache
======================

To generate a new hash to invalidate caches and force browsers to download new versions of asset files run the following artisan command.

```
php artisan assetcachebuster:generate
```

This will generate a new hash and update the config file.
It is important that you do not change the hash line of the config file manually or this command may no longer function.
If you must manually update the hash make sure it is a valid md5 hash or exceptions will be thrown when generating a hashed url.
A md5 hash is a 32 character string with only the characters 0-9 and a-f.

Using with a CDN
================

To use with a CDN such as [Cloudfront](http://aws.amazon.com/cloudfront/) set the cdn key in the config to the cdn url.

Now any asset url will begin with the cdn url.

This will only work with a CDN that supports origin pull as it does not push any asset files to the cdn.

Setting Far Future Expires Headers
===================================

In order to actually receive a benefit from the package asset files should be set with far future expires headers.

To do this add the following to your apache .htaccess file.

You might need to configure your cdn to set these headers if you are using one.


```ApacheConf
# From the html5 boilerplate .htaccess
# https://raw.github.com/h5bp/html5-boilerplate/master/.htaccess

# ------------------------------------------------------------------------------
# | ETag removal                                                               |
# ------------------------------------------------------------------------------

# Since we're sending far-future expires headers (see below), ETags can
# be removed: http://developer.yahoo.com/performance/rules.html#etags.

# `FileETag None` is not enough for every server.
<IfModule mod_headers.c>
    Header unset ETag
</IfModule>

FileETag None

# ------------------------------------------------------------------------------
# | Expires headers (for better cache control)                                 |
# ------------------------------------------------------------------------------

# The following expires headers are set pretty far in the future. If you don't
# control versioning with filename-based cache busting, consider lowering the
# cache time for resources like CSS and JS to something like 1 week.

<IfModule mod_expires.c>

    ExpiresActive on
    ExpiresDefault                                      "access plus 1 month"

  # CSS
    ExpiresByType text/css                              "access plus 1 year"

  # Data interchange
    ExpiresByType application/json                      "access plus 0 seconds"
    ExpiresByType application/xml                       "access plus 0 seconds"
    ExpiresByType text/xml                              "access plus 0 seconds"

  # Favicon (cannot be renamed!) and cursor images
    ExpiresByType image/x-icon                          "access plus 1 week"

  # HTML components (HTCs)
    ExpiresByType text/x-component                      "access plus 1 month"

  # HTML
    ExpiresByType text/html                             "access plus 0 seconds"

  # JavaScript
    ExpiresByType application/javascript                "access plus 1 year"

  # Manifest files
    ExpiresByType application/x-web-app-manifest+json   "access plus 0 seconds"
    ExpiresByType text/cache-manifest                   "access plus 0 seconds"

  # Media
    ExpiresByType audio/ogg                             "access plus 1 month"
    ExpiresByType image/gif                             "access plus 1 month"
    ExpiresByType image/jpeg                            "access plus 1 month"
    ExpiresByType image/png                             "access plus 1 month"
    ExpiresByType video/mp4                             "access plus 1 month"
    ExpiresByType video/ogg                             "access plus 1 month"
    ExpiresByType video/webm                            "access plus 1 month"

  # Web feeds
    ExpiresByType application/atom+xml                  "access plus 1 hour"
    ExpiresByType application/rss+xml                   "access plus 1 hour"

  # Web fonts
    ExpiresByType application/font-woff                 "access plus 1 month"
    ExpiresByType application/vnd.ms-fontobject         "access plus 1 month"
    ExpiresByType application/x-font-ttf                "access plus 1 month"
    ExpiresByType font/opentype                         "access plus 1 month"
    ExpiresByType image/svg+xml                         "access plus 1 month"

</IfModule>
```
For Nginx, add the following to your virtual host file

```Nginx
# ------------------------------------------------------------------------------
# | Expires headers (for better cache control)                                 |
# ------------------------------------------------------------------------------

# Sets the expires header to 1 year in the furture for javascript, css, and images.

location ~* .(js|css|png|jpg|jpeg|gif|ico)$ {
    expires 1y;
    log_not_found off;
}
```
