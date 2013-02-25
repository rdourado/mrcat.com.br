=== Photopress ===
Contributors: isaacwedin
Tags: images, album, photos, gallery
Requires at least: 2.8
Tested up to 2.9.1
Stable tag: 1.8

== Description ==

Photopress is a plugin that adds some image-related features to WordPress. A new button on the toolbar launches a pop-up image uploader and browser which can insert code for tagged thumbnails or full images into posts. In addition, the plugin includes some template functions, a simple album, some image management tools, and a widget. A couple of database tables are added to store categories and descriptions.

== Credits ==

Photopress uses code and ideas from Florian Jung's Image Browser, the Anarchy Media Player, Kimili Flash Embed, the built-in Wordpress upload functions, and codex.wordpress.org. Many people have contributed bug reports, fixes and suggestions, including Randy, RobotDan, Alex, Jono, Paula, Frank, Roge, SHRIKEE, Hans, Marcus, and Claus. I appreciate your help!

== Installation ==

1. Either use ( Plugins -> Add New -> Upload ) or install manually by extracting the archive into the wp-content/plugins folder on your server, or FTP the extracted folder and files there. The files should all end up in a single "photopress" folder.
2. Create a wp-content/photos folder and make it writable by your server. You can try to use the tool at Tools:Photopress to do this, but it may fail depending on your server setup.
3. Activate Photopress at the Plugins page and configure Settings:Photopress to suit.
4. (Optional) If you chose to turn on the album in the options, you need to create a page for it. Create a new WordPress page the usual way and, using the HTML editor, insert the text "<!--photopress_album-->" in the page where you want the album to appear. Enter the name of the page at Settings:Photopress. The album should work with most any theme, but for best results use a custom template and add some CSS (or edit pp_album_css.php). Refresh your permalinks at Settings:Permalinks - failing to do this is a very common cause of problems. If you are not using permalinks there's an option to use a page's ID number to identify the album instead of the page slug.

== Upgrading ==

1. Deactivate the plugin.
2. Replace all of the old plugin files with the new ones.
3. Re-activate the plugin.
4. Check for any new options to configure at Settings:Photopress.
5. If you're using permalinks you may need to refresh your permalinks at Settings:Permalinks to get the album working again.

== Random Images ==

There are at least 3 ways to add random Photopress images to your blog:

1. You can check a box at Settings:Photopress to insert a random image into your sidebar's Meta section using a plugin hook.

2. There is a widget included. Enable it at Plugins then add it to your
widget-ready theme at Appearance:Widgets.

3. For maximum control you can use the template function. Add it where you want the random image(s) to appear, such as in your sidebar.php. The function has several options:

pp_random_image_bare($number_of_images,$before,$after,$class,$category);

Here are the default options, which will be used if you don't specify anything:

pp_random_image_bare(1,'','<br />','random');

This dispays a single image with nothing before it and a break after. 'random' isn't a class but a keyword that will use the random image class you set in Options. If you leave out the category the function will choose from all of your images.

I highly recommend checking for the function before calling it like this:

if (function_exists(pp_random_image_bare)) { pp_random_image_bare();  }

Now if you disable the plugin your blog won't break. It's a good idea to do this for all plugin functions you use in your theme.

== Album Appearance ==

The appearance of the album can be customized by using a custom template file (such as photos.php) and by editing the album style in pp_album_css.php. Photopress looks for pp_album_css.php in your theme's folder first, so if you modify it to suit your theme you can keep it there. If you want to really tinker, you can modify pp_album.php directly.

== Localization ==

I believe I have all of the strings in Photopress set up for localization, but I'm not an expert about generating translation templates so you'll need to do that yourself.

== Migrating ==

There are a couple of tools at Tools:Photopress:Maintain that give you two migration options:

1. You can first migrate your images into the Media Library then replace your Photopress tags with links to the migrated images. Migrating images is done in batches based on the setting at Settings:Photopress - if you have plenty of server resources you might want to raise the number done per batch.

2. You can replace your Photopress tags with links that point directly at images in the Photopress folder. The images won't be in the Media Library, but your posts with Photopress images won't be broken if you disable Photopress.

If you have not been using Photopress tags but have been linking to the album I don't have a good migration option for you yet...

== Frequently Asked Questions ==

= The album breaks my template/theme!? =

It's designed to work well with the default theme but should work with most themes, as long as they allow enough space for post/page content. To use your custom template you'll need to tell WordPress about it by editing your the album page you created. You may also need to edit pp_album_css.php to suit your theme.

= Why add the random image to the Meta list? =

Because there's a plugin hook in Wordpress to do that. If you don't like that, disable it on the Settings:Photopress page and use the widget or the template function instead.

= I've already got a bunch of images in a folder, how can I add them to Photopress? =

At Tools:Photopress:Maintain there's a simple import tool that'll find images in the folder that aren't in the database and add them, resizing and creating thumbnails too. Check the permissions or sizes of your images if something doesn't work. If it doesn't finish because PHP times out try running it again - it should start up where it left off.

= How do I delete an image? =

Go to Tools:Photopress and click through to an image. Depending on how you've configured Settings:Photopress, there could be a delete button or a list of posts that use the image, or both. You really should remove the image from any posts using it before deleting it, but that's up to you. You can delete multiple images in the Mass View at Tools:Photopress (if you've set the option to do so).

= Why aren't the Photo Album link and random thumb showing up in Meta? =

You probably haven't uploaded any images yet, or you need to re-activate the plugin (or use the maintenance tools at Tools:Photopress) to import your existing images.

= Permalinks aren't working! =

If you just installed or upgraded, go refresh your WP permalinks.

= I can't upload huge images!? =

Most hosts set limits on both file upload sizes and the amount of memory a script can consume. If you exceed the allowed file size you should get a notice. Rarely, an image may be within the file size limit, but upon expansion for resizing it may exceed PHP's memory limit. Sadly, I haven't figured out how to produce an error message when this happens.

= My images disappeared from my posts!? =

If your host changes the paths on your server, Photopress probably won't be
able to find your photos anymore. Go to the settings page to verify that the
paths there are correct and update them if not.
