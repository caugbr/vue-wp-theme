# Vue WP Theme
A starter theme using Vue.js 2 and WP REST API.
The frontend is a Vue app, all the displayed contents are loaded using the API and the routes are defined in Vue Router. 

**Already working**
* Router
* Vuex
* Sass

**Components**
* WpMenu - component to display any WP menu
* Loading - global loading layer
* LangMenu - a menu to switch the site language
* List - list posts by post type and taxonomy term

**Mixins**
* common.js - some helper functions that is available in all components
* post.js - helper functions for views that displays posts or post lists

**Other scripts**
* I18n - Vue plugin to internationalize the Vue app
   (edit / create translation files directly from WP admin)
* WpApi - script to interact with WP REST API

**Administration page**
In `Appearence > Vue WP Theme` you can access the theme options page. There you can edit the site settings and edit translation files or even create a new one.

**Settings**
The settings are stored in a php file, located in `settings/theme-settings.php`. The defined options will be editable in admin page and available in frontend as `this.info`.

**Translations**
The translation files are simple JSON files located in `vue-app/I18n/langs` and you can edit them directly, but there is a simple way to do this in the admin page. The set of strings displayed in admin is read from the code, so it's always up to date.

**Routes**
The current route schema is simple and you can change it as you wish. There is one component for each defined route.
* **/** - Home
* **/posts** - PostArchive
* **/posts/:slug** - Post
* **/pages** - PageArchive
* **/pages/:slug** - Page
* **/:postType/:taxonomy/:term** - TaxonomyArchive
* **/[anything else]** - NotFound

**The variable** `vueWpThemeInfo`

This is a JS variable that holds site information. It is available in Vue components as `this.info`.

	{
		"themeDirUrl": "Theme URL", // Theme directory URL
		"siteUrl": "Site URL", // Full site URL
		"basePath": "Path", // If WP base URL is a sub folder, contains the path to it
		"language": "WP language", // The language defined in Wordpress settings
		"settings": {}, //All settings defined in admin page
		"wpApiSettings": { // used by WpApi
			"root": "REST API base URL",
			"nonce": "WP nonce"
		}
	}
