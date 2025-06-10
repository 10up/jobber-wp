=== Jobber ===
Contributors: jobberintegrations, 10up
Tags:         jobber, booking, request, form
Tested up to: 6.8
Stable tag:   1.0.0
License:      GPL-2.0-or-later
License URI:  https://spdx.org/licenses/GPL-2.0-or-later.html

Add a Jobber form block to your WordPress site.

== Description ==

The Jobber plugin allows you to add a Jobber form block to your WordPress site. This block can render either a bookings or request form.

== Account Setup ==

To use the Jobber block, you need to have a Jobber account. If you don't have one, you can follow [these steps](https://help.getjobber.com/hc/en-us/articles/360042653674-First-Steps-Basic-Account-Set-Up) to create that.

== Installation ==

Install through the WordPress directory or download, unzip and upload the files to your `/wp-content/plugins/` directory

== Using the Jobber Block ==

= Connect your Jobber Account =

1. Go to the Jobber settings page in your WordPress admin.
2. Click on the "Connect to Jobber" button.
3. Follow the prompts to authorize the connection between your WordPress site and your Jobber account. Note your site needs to be publicly accessible for this to work.
4. Once connected, you will see a confirmation message.

= Adding the Block =

1. Open the WordPress block editor (Gutenberg).
2. Click the "+" button to add a new block.
3. Search for "Jobber".
4. Click on the Jobber block to insert it into your page or post.

= Customizing the Block =

The Jobber block includes a form type selection option:

* Form Type Selection
  * Choose between "Booking Form" or "Request Form". Each form type serves different purposes:
    * Booking Form: Allows customers to directly book services.
    * Request Form: Enables customers to submit service requests.

== Frequently Asked Questions ==

= Do I need to create a Jobber app? =

To connect your Jobber account to your WordPress site, you only need to click the "Connect to Jobber" button within the Jobber settings and follow the prompts. Behind the scenes this will utilize an existing Jobber app to authenticate your account.

= Does the plugin use any external services? =

We connect to [Jobber](https://www.getjobber.com/) ([privacy policy](https://www.getjobber.com/privacy-policy/)) to get your forms. This connection is faciliated through a middleware service hosted by [10up](https://10up.com/) ([privacy policy](https://10up.com/privacy-policy/)) and located at https://jobber-prod.10upmanaged.io. Authentication is done via OAuth through this service and any Jobber API requests are also filtered through this service.

= Can I test this locally? =

When you connect your site to your Jobber account, the connection is made through a middleware service that will communicate back with your site to validate the request. This request will fail if the middleware can't communicate with your site. If you're testing locally, you can use a service like [ngrok](https://ngrok.com/) to expose your site to the internet.

== Screenshots ==

1. Admin notice after activating the plugin.
2. Settings page with connection instructions.
3. Settings page after successfully connecting to Jobber.
4. Jobber block rendering a Request form in the editor.
5. Jobber block rendering a Booking form in the editor.
6. Displaying a Request form on the front-end.
7. Displaying a Booking form on the front-end.

== Changelog ==

= 1.0.0 - YYYY-MM-DD =
* Initial release.

[View historical changelog details here](https://github.com/10up/jobber-wp/blob/develop/CHANGELOG.md).
