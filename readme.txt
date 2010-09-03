=== Thumbnail Updater ===
Contributors: Jessica Green
Donate link: http://dumpster-fairy.com/
Tags: post thumbnails, images, media library
Requires at least: 3.0
Tested up to: 3.0
Stable tag: trunk

A plugin for updating your thumbnails whenever a new thumbnail size is added with add_image_size()

== Description ==
Thumbnail Updater does exactly what it's name implies: it updates thumbnails. WordPress 2.9 introduced support for
post thumbnails but one problem is that the support is not backwards compatible—meaning that if you downloaded or created
a theme that supports post thumbnails, any images uploaded prior to the activation of that theme will not have the new thumbnail size.

== Installation ==
Installation is easy. Unzip into your wp-content/plugins directory and activate. Thumbnail Updater can be accessed through
the Media Library. Navigate to Media Library, click on any image and you should a button that says "Update Image Sizes." Clicking
on that will open a popup that lists all of the available image sizes. If a thumbnail size exists for that image, there will be a
check next it. If the thumbnail size doesn't exist, then a red exclamation mark will appear.

1. Upload the `thumbnail-update` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

That's all!

== Frequently Asked Questions ==
= Hey! Your plugin keeps saying Error! What's wrong? =
Possibly permission problems. If you're like me, you probably have a two-year-old blog and have switched hosts at least three times
in that last year. What does that have to do with permissions? Well, some of your older uploads—those dating prior to your last
webhost change—either A) don't have the folders and files set to 777 and 666 (respectively) or B) Owner:group isn't set to www-data:www-data.
The first problem is relatively easy to deal with using any good FTP program. The second problem requires that your web-host allows
shell-access to their servers. If not, you'll have to contact them to get the groups changed. Finally, I've run into some issues with
the oldest files on my server. It didn't matter if I changed permissions or owners on the directory or files, I still could not get thumbnails to generate.

== Screenshots ==

1. The location of the "Update Image Sizes" button.
2. Appearance of the Update Media Thumbnail interface.

== Changelog ==

= 1.0 =
* Plugin now has an interface instead of just a button that updates the thumbnail.
* Interface uses the same principles as the WordPress Image Editor interface.

= 0.1b =
* Plugin launch.