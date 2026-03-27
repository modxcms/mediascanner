# Media Scanner

Media Scanner is a tool that attempts to scan your site for linked assets. This tool
is primarily intended as a reference for what assets are in use. It does a best guess
effort to find all linked assets and provides a media browser to review them.

Media Scanner is limited in that it can not detect the original URL of a masked asset,
like those using pThumb or similar to generate a scaled image. It also can not parse
through subsections of dynamically generated pages, like those built with getPage.

With that in mind, it can be very helpful with identifying possible unused assets.

## Installation

Install via package management. To generate the initial link stats, go to the Media
Scanner manager page found under the "Media" drop down and click on the "Scan Media"
button. This will take a while, depending on the size of your site. If this fails,
you may need to manually run the included scanning script via CLI or cron.

## Manual Asset Scanning

If the automatic scanning fails, you can run the included scanning script manually.
This is useful if you have a large site, or if you want to run the scanning script
via cron.

To run the scanning script, you need to SSH into your server and run the following
command:

    php /path/to/your/modx/core/components/mediascanner/cron/generate.2x.php

Or for MODX 3.x Versions:

    php /path/to/your/modx/core/components/mediascanner/cron/generate.3x.php

Once completed, this will output the number of resources indexed. You can then
go to the Media Scanner manager page and verify it is showing the newly
discovered links.

## Automatic Scanning

In general, the automatic scanning should work fine. It renders a page on save,
and scans it for links. However, the links can change if you make adjustments
to a template or chunk in your site that would affect the frontend links. If
this happens, you can run the scanning script manually to update the links,
or go to the manager page and click "Regenerate Links".

## Disable Automatic Scanning

Due to the way automatic scanning works, it may slow down the save event on
your site depending on how your site is set up. If you want to disable automatic
scanning, you can do so by setting the `mediascanner.allow_regenerate_onsave`
system setting to `No`. It is recommended you then run the scanning script
manually via cron.

If you are just having issues with scanning a specific page, Media Scanner will
first attempt to automatically resolve it by skipping the resource. If that fails
you can manually add the Resource ID to the `mediascanner.skip_skan` system
setting. This is a comma-separated list of resources to skip when scanning for
new assets.

## Disable "Regenerate Links" Button

In the event you have a large site that consistently fails to generate links
with the provided button. You can disable it in system settings. This will
prevent the button from showing up in the manager page. It is recommended you
then run the scanning script manually via cron.

To disable the button, set the system setting
`mediascanner.allow_regenerate_button` to `No`.