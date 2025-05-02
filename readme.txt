=== Jobber ===
Contributors: 10up
Tags:         jobber, booking, request, form
Tested up to: 6.8
Stable tag:   1.0.0
License:      GPL-2.0-or-later
License URI:  https://spdx.org/licenses/GPL-2.0-or-later.html

Add a Jobber form block to your WordPress site to easily collect booking requests and manage appointments.

== Description ==

The Jobber plugin seamlessly integrates Jobber's booking and request forms into your WordPress site using blocks. This integration allows your customers to easily schedule appointments and submit service requests directly from your website.

**Key Features:**

* Easy-to-use Jobber form block
* Choose between booking or request forms
* Seamless integration with your Jobber account
* Mobile-responsive forms
* Clean, modern design that works with any theme

== Account Setup ==

To use the Jobber block, you need to have a Jobber account. If you don't have one, you can follow [these steps](https://help.getjobber.com/hc/en-us/articles/360042653674-First-Steps-Basic-Account-Set-Up) to create that.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/jobber` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings->Jobber screen to configure the plugin.
4. Add the Jobber block to any page or post where you want to display a form.

== Screenshots ==

1. Jobber block in the editor
2. Booking form example
3. Request form example
4. Block settings panel

== Development ==

= Install Dependencies & Build =

To build the assets, follow these steps:

- Ensure you have the proper version of Node.js installed.
- Run `npm install` to install the dependencies.
- Run `npm run build` to build the asset files.

You can find the source files in the `blocks` directory.

We also rely on composer for autoloading. To set this up properly:

- Ensure you have the latest version of Composer installed.
- Run `composer install --no-dev -o`.

== Frequently Asked Questions ==

= Do I need a Jobber account to use this plugin? =

Yes, you need an active Jobber account to use this plugin. The forms will connect to your Jobber account to process bookings and requests.

= Can I customize the appearance of the forms? =

The forms are designed to work with any WordPress theme. Basic styling options are available through the block editor.

= Is the plugin GDPR compliant? =

Yes, the plugin handles data in compliance with GDPR requirements. All data is processed according to Jobber's privacy policy.

= Do I need to create a Jobber app? =

To connect your Jobber account to your WordPress site, you only need to click the "Connect to Jobber" button within the Jobber settings and follow the prompts. Behind the scenes this will utilize an existing Jobber app to authenticate your account.

= Does the plugin use any external services? =

We connect to [Jobber](https://www.getjobber.com/) ([privacy policy](https://www.getjobber.com/privacy-policy/)) to get your forms. This connection is faciliated through a middleware service hosted by [10up](https://10up.com/) ([privacy policy](https://10up.com/privacy-policy/)) and located at https://jobber-prod.10upmanaged.io. Authentication is done via OAuth through this service and any Jobber API requests are also filtered through this service.

= Can I test this locally? =

When you connect your site to your Jobber account, the connection is made through a middleware service that will communicate back with your site to validate the request. This request will fail if the middleware can't communicate with your site. If you're testing locally, you can use a service like [ngrok](https://ngrok.com/) to expose your site to the internet.

== Changelog ==

= 0.1.0 - 2024-03-21 =
* Initial release
* Added Jobber forms block
* Support for booking and request forms

== Upgrade Notice ==

= 0.1.0 =
Initial release of the Jobber WordPress plugin.
