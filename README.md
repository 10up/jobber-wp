# Jobber WordPress Plugin

> [!IMPORTANT]
> This repository is a work in progress.

## Overview

This WordPress plugin connects a users Jobber account to WordPress through the Jobber API.
Middleware is used to handle oauth2 authentication.

## Requirements

* PHP 7.4+
* [WordPress](http://wordpress.org/) 6.5+

## Installation

Install through the WordPress directory or download, unzip and upload the files to your `/wp-content/plugins/` directory.

## Usage

1. Install the plugin.
2. Activate in WordPress.
3. Visit the plugin Settings page.
4. Click the "Connect to Jobber" button.
5. Follow the prompts to log in to your Jobber account to grant the plugin access to make API requests.
6. Once connected, add the Jobber block to your post or page.

## Using the Jobber Block

### Adding the Block

1. Open the WordPress block editor (Gutenberg).
2. Click the "+" button to add a new block.
3. Search for "Jobber Forms" or find it in the "Widgets" category.
4. Click on the Jobber Forms block to insert it into your page or post.

### Customizing the Block

The Jobber Forms block includes a form type selection option:

**Form Type Selection**

Choose between "Booking Form" or "Request Form". Each form type serves different purposes:
   
 - Booking Form: Allows customers to directly book services.
 - Request Form: Enables customers to submit service requests.
