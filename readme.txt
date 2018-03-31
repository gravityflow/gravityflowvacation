=== Gravity Flow Vacation Requests Extension ===
Contributors: stevehenty
Tags: gravity forms, approvals, workflow
Requires at least: 4.0
Tested up to: 4.4
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Manage Vacation Requests in Gravity Flow.

== Description ==

The Gravity Flow Vacation Requests Extension is an extension for Gravity Flow.

Gravity Flow is a commercial Add-On for [Gravity Forms](https://gravityflow.io/gravityforms)

Facebook: [Gravity Flow](https://www.facebook.com/gravityflow.io)

= Requirements =

1. [Purchase and install Gravity Forms](https://gravityflow.io/gravityforms)
1. [Purchase and install Gravity Flow](https://gravityflow.io)
1. Wordpress 4.3+
1. Gravity Forms 1.9.4+
1. Gravity Flow 1.1.0.4+


= Support =
If you find any that needs fixing, or if you have any ideas for improvements, please get in touch:
https://gravityflow.io/contact/


== Installation ==

1.  Download the zipped file.
1.  Extract and upload the contents of the folder to /wp-contents/plugins/ folder
1.  Go to the Plugin management page of WordPress admin section and enable the 'Gravity Flow Vacation Requests Extension' plugin.

== Frequently Asked Questions ==

= Which license of Gravity Flow do I need? =
The Gravity Flow Vacation Requests Extension will work with any license of [Gravity Flow](https://gravityflow.io).


== ChangeLog ==

= 1.2 =
- Added the merge tag {workflow_vacation} with modifiers, including: pto, comp_days, hr_adjustment, carry, approved and balance.
- Added the shortcode [gravityflow page="vacation"] which takes "user_id" and "data" attributes.

= 1.1 =
- Added the gravityflowvacation_start_month filter.
- Added the gravityflowvacation_balance filter.
- Updated the vacation days calculation to search for the final status of entries instead of the status of the last approval step.
- Updated Members 2.0 integration to use human readable labels for the capabilities. Requires Gravity Flow 1.8.1 or greater.

= 1.0.0.6 =
- Fixed an issue with the calculation of the balance where only the first 20 entries are counted.

= 1.0.0.5 =
- Added logging statements.

= 1.0.0.4 =
- Rolled back removal of the current balance from the entry detail page.

= 1.0.0.3 =
- Fixed an issue where the balance shows the balance for the current users instead of the submitter.
- Removed the current balance from the entry detail page.

= 1.0.0.2 =
- Fixed an issue where capabilities are not available to roles.


= 1.0.0.1 =
- Fixed an issue where the approved calculation ignores workflow which don't end in an approval step.

= 1.0 =
All new!
