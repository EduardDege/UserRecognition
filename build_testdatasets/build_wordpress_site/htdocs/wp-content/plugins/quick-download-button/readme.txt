=== Quick Download Button ===
Contributors: kusimo
Tags: media download, hide download link, download button
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.7
Tested up to: 5.5
Stable tag: 4.4.6
Requires PHP: 5.6 

Quick download button with block and shortcode support.

== Description ==
Quick download button is a download button for WordPress. You can easily add a better download link to your post with this plugin.


== Installation ==
1. Upload the quick-download-button folder to the /wp-content/plugins/ directory
2. Activate the Delete Quick Download Button plugin through the \'Plugins\' menu in WordPress

== Features ==

* Support for WordPress Gutenberg
* Display file size and file extension. 
* Create a download button link with shortcode with options
* Link your download button to anywhere on the web where it's publicly available.
* Allow free music download, video download, PDF download, spreadsheet file download and more.
* Hide download link
* Support for external download link 
* Shows download file extension for 'pdf','mp3','mov','zip','txt','doc','xml','mp4','ppt' and images ( png, gif, jpg, jpeg, bmp)
* Support for htm, html, ps, tex, xml, txt, csv, xlsx (Microsoft Excel), pptx (Microsoft PowerPoint), js, css, php
* Open external download in new tab
* Force file download


 == Basic Usage ==

 ** Gutenberg Block **

1. Open the post you want to add a download link to, click on add block icon (+).
2. Under Media, click on the Download Button icon.
3. Click on the button to change the title, click on the download icon next to the button to upload a file for download.
4. Enter a suitable title in the text box, the default title is download. All done!

** Shortcode **
Open the post or page you want to add a download button to, paste the shortcode example below

`
[quick_download_button title="Download" url="http://yoursite/wp-content/upload/fileto_download.pdf"]
`
The url value needs to be replaced with your media link URL. To change the title, enter a different text in the title value. The default value is Download.

== More Shortcode Usage ==

** Link to external URL **

To use external url, add url_external attribute

`
[quick_download_button title="Download" url_external="https://google.com"]
`

** Auto calculates file size. **

To let the plugin generate file size, make sure the file URL link is in the WordPress upload directory when using with shortcode e.g wp-content/upload and change filesize value to 1 like below

`
[quick_download_button filesize="1" title="Download" url="http://yoursite/wp-content/upload/fileto_download.pdf"]
`

** Add file size manually **

The plugin can calculate the file size for you by entering 1 in the filesize value. You don't have to enter it manually but in case, enter the file size value in the filesize attribute like below.

`
[quick_download_button filesize="14.5MB" title="Download" url="http://yoursite/wp-content/upload/fileto_download.pdf"]
`


** Hide icon for file extension **
To hide icon image for the file extension, set the extension value to 0. Note, this will also hide file extension text.

`
[quick_download_button  title="Download" filesize="1" extension="0" url="http://yoursite/wp-content/upload/fileto_download.pdf"]
`

** Show icon image and file extension text **

To show both file extension icon and text,  set extension value to 1 and extension_text to 1

`
[quick_download_button  title="Download" filesize="1" extension="1" extension_text="1"  url="http://yoursite/wp-content/upload/fileto_download.pdf"]
`



== To use in a theme file (Developers) ==

To use in your theme file, add the code below with necessary attributes and values.

`
echo do_shortcode('[quick_download_button title="Download" url="http://yoursite/wp-content/upload/fileto_download.pdf"]');
`

== Documentation ==
You can find out more on how to use this plugin on the [Documentation](https://basichow.com/quick-download-button-wordpress-plugin/). 
To contribute and improve this plugin please visit the [Git repo](https://github.com/kusimo/quick-download-button).

== Frequently Asked Questions ==

= Can this plugin be used to hide (protect) download link on my site? =

Yes.

= Can this plugin be used to display download file size on my site? =

Yes.
