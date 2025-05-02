# Jobber WordPress Plugin

> [!IMPORTANT]
> This repository is a work in progress.

[![GPL-2.0-or-later License](https://img.shields.io/github/license/10up/jobber-wp.svg)](https://github.com/10up/jobber-wp/blob/develop/LICENSE.md)

## Overview

This WordPress plugin connects a users Jobber account to WordPress through the Jobber API.
A separate middleware service is used to handle Oauth2 authentication and proxying API requests.

## Requirements

* PHP 7.4+
* [WordPress](http://wordpress.org/) 6.6+
* [Jobber](https://getjobber.com/) account
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
