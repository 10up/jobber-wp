# Jobber WordPress Plugin

[![Support Level](https://img.shields.io/badge/support-active-green.svg)](#support-level) ![Required PHP Version](https://img.shields.io/wordpress/plugin/required-php/jobber?label=Requires%20PHP) ![Required WP Version](https://img.shields.io/wordpress/plugin/wp-version/jobber?label=Requires%20WordPress) ![WordPress tested up to version](https://img.shields.io/wordpress/plugin/tested/jobber?label=WordPress) [![GPL-2.0-or-later License](https://img.shields.io/github/license/10up/jobber-wp.svg)](https://github.com/10up/jobber-wp/blob/develop/LICENSE.md) [![WordPress Playground Demo](https://img.shields.io/wordpress/plugin/v/jobber?logo=wordpress&logoColor=FFFFFF&label=Playground%20Demo&labelColor=3858E9&color=3858E9)](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/10up/jobber-wp/develop/.wordpress-org/blueprints/blueprint.json)

[![Run E2E tests](https://github.com/10up/jobber-wp/actions/workflows/e2e.yml/badge.svg)](https://github.com/10up/jobber-wp/actions/workflows/e2e.yml) [![CodeQL](https://github.com/10up/jobber-wp/actions/workflows/codeql-analysis.yml/badge.svg)](https://github.com/10up/jobber-wp/actions/workflows/codeql-analysis.yml) [![WordPress Plugin Checks](https://github.com/10up/jobber-wp/actions/workflows/plugin-check.yml/badge.svg)](https://github.com/10up/jobber-wp/actions/workflows/plugin-check.yml) [![Dependency Review](https://github.com/10up/jobber-wp/actions/workflows/dependency-review.yml/badge.svg)](https://github.com/10up/jobber-wp/actions/workflows/dependency-review.yml)

[![JS Linting](https://github.com/10up/jobber-wp/actions/workflows/eslint.yml/badge.svg)](https://github.com/10up/jobber-wp/actions/workflows/eslint.yml) [![PHP Linting](https://github.com/10up/jobber-wp/actions/workflows/phpcs.yml/badge.svg)](https://github.com/10up/jobber-wp/actions/workflows/phpcs.yml) [![PHPStan](https://github.com/10up/jobber-wp/actions/workflows/phpstan.yml/badge.svg)](https://github.com/10up/jobber-wp/actions/workflows/phpstan.yml) [![PHP Compatibility](https://github.com/10up/jobber-wp/actions/workflows/php-compat.yml/badge.svg)](https://github.com/10up/jobber-wp/actions/workflows/php-compat.yml)

## Overview

Jobber keeps your home service business running, even when you're on the move. From capturing leads to getting paid, it's everything you need to stay organized, close jobs faster, and keep your cash flow strong—all in one place.

Together Jobber and WordPress help you get discovered, build credibility, and convert more website visitors into paying clients.

**Embed Jobber's powerful online booking and request forms directly into your WordPress site.**

With the Jobber WordPress plugin, home service pros can add fully integrated client intake forms in just a few clicks. Choose between Booking or Request modes and turn website visitors into clients fast.

All submissions are automatically synced to Jobber to create clients, requests, and bookings, making it easy for potential customers to get a quote quickly, and book your services with confidence.

## Requirements

* PHP 7.4+
* [WordPress](http://wordpress.org/) 6.6+
* An active Jobber account with a Core or higher price plan. Don't have a Jobber account? [Sign up](https://getjobber.com/plp/wordpress) to receive 20% off your first 6 months, and a free 14-day trial.
* Your site needs to be publicly accessible on the internet in order for authentication to work.

## Installation

Install through the WordPress directory or download, unzip and upload the files to your `/wp-content/plugins/` directory.

## Usage

1. Install the plugin.
2. Activate in WordPress.
3. Visit the plugin Settings page.
4. Click on the "Connect to Jobber" button.
5. Follow the prompts to authorize the connection between your WordPress site and your Jobber account. Note your site needs to be publicly accessible for this to work.
6. Once connected, you will see a confirmation message.

![The Jobber settings page with connection instructions.](/.wordpress-org/screenshot-2.png)
![The Jobber settings page after successfully connecting to Jobber.](/.wordpress-org/screenshot-3.png)

## Using the Jobber Block

### Adding the Block

1. Open the WordPress block editor (Gutenberg).
2. Click the "+" button to add a new block.
3. Search for "Jobber".
4. Click on the Jobber block to insert it into your page or post.

### Customizing the Block

The Jobber block includes a form type selection option:

**Form Type Selection**

Choose between "Booking Form" or "Request Form". Each form type serves different purposes:

* Booking Form: Allows customers to directly book services.
* Request Form: Enables customers to submit service requests.

![The Jobber block in the editor, showing the Request form type.](/.wordpress-org/screenshot-4.png)
![The Jobber block in the editor, showing the Booking form type.](/.wordpress-org/screenshot-5.png)

## Development

### Install Dependencies & Build

To build the assets, follow these steps:

* Ensure you have the proper version of Node.js installed.
* Run `npm install` to install the dependencies.
* Run `npm run build` to build the asset files.

You can find the source files in the `blocks` directory.

We also rely on composer for autoloading. To set this up properly:

* Ensure you have the latest version of Composer installed.
* Run `composer install --no-dev -o`.

## Support Level

**Active:** Jobber and 10up are actively working on this, and we expect to continue work for the foreseeable future including keeping tested up to the most recent version of WordPress. Bug reports, feature requests, questions, and pull requests are welcome.
