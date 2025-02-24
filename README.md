# Ultimate Member - GTranslate

Integrates the **Ultimate Member** plugin with the **GTranslate** plugin.

__Note:__ The GTranslate plugin can translate pages without any integration, but can not translate emails.

## Key features

- Ability to translate email templates in Ultimate Member settings.
- Send translated emails. The mail language is selected depending on the user's locale (if set) or using the GTranslate language switcher.
- Set user locale using GTranslate language switcher. Can be disabled using the `um_gtranslate_update_user_locale` filter hook.

## Installation

__Note:__ This plugin requires the [Ultimate Member](https://wordpress.org/plugins/ultimate-member/) and [GTranslate](https://wordpress.org/plugins/gtranslate/) plugins to be installed first.

### How to install from GitHub

Open git bash, navigate to the **plugins** folder and execute this command:

`git clone --branch=main git@github.com:umdevelopera/um-gtranslate.git um-gtranslate`

Once the plugin is cloned, enter your site admin dashboard and go to _wp-admin > Plugins > Installed Plugins_. Find the **Ultimate Member - GTranslate** plugin and click the **Activate** link.

### How to install from ZIP archive

You can install the plugin from this [ZIP file](https://drive.google.com/file/d/1uK_3C0aLfN6y2DcsOB5XYJEKkMK6x7rB/view) as any other plugin. Follow [this instruction](https://wordpress.org/support/article/managing-plugins/#upload-via-wordpress-admin).

## How to use

### How to translate E-mails

Go to *wp-admin > Ultimate Member > Settings > Email* to translate email templates. Click the "+" icon unter the flag to translate a template. The plugin saves translated email templates to locale subfolders in the theme, see [Email Templates](https://docs.ultimatemember.com/article/1335-email-templates).

![UM Settings, Email](https://github.com/user-attachments/assets/73931b94-ed64-4130-bb13-2774eefd7f41)

The extension determines the current language as follows:
1. from the `lang` URL parameter if it exists.
2. from the `X_GT_LANG` header if it exists (for the paid version).
3. from the `googtrans` cookie if it exists (for the free version).
4. from the WordPress locale.

## Support

This is a free extension created for the community. The Ultimate Member team does not provide support for this extension.
Open new [issue](https://github.com/umdevelopera/um-gtranslate/issues) if you are facing a problem or have a suggestion.

**Give a star if you think this extension is useful. Thanks.**

## Useful links

[Ultimate Member core plugin info and download](https://wordpress.org/plugins/ultimate-member)

[Documentation for Ultimate Member](https://docs.ultimatemember.com)

[Official extensions for Ultimate Member](https://ultimatemember.com/extensions/)

[Free extensions for Ultimate Member](https://docs.google.com/document/d/1wp5oLOyuh5OUtI9ogcPy8NL428rZ8PVTu_0R-BuKKp8/edit?usp=sharing)

[Code snippets for Ultimate Member](https://docs.google.com/document/d/1_bikh4JYlSjjQa0bX1HDGznpLtI0ur_Ma3XQfld2CKk/edit?usp=sharing)
