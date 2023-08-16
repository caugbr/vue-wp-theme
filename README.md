# Vue WP Theme

A starter theme using Vue.js 2 and WP REST API.
The frontend is a Vue app, all the displayed contents are loaded using the API and the routes are defined in Vue Router. 

### Already working
* Router
* Vuex
* Sass

### Components
* WpMenu - component to display any WP menu
* Loading - global loading layer
* LangMenu - a menu to switch the site language
* List - list posts by post type and taxonomy term
* Search - search form
* Thumbnail - render the featured image for a post

### Mixins
* common.js - some helper functions that is available in all components
* post.js - helper functions for views that displays posts or post lists

### Other scripts
* I18n - Vue plugin to internationalize the Vue app
* WpApi - script to interact with WP REST API
* Grid - Simple CSS grid made with SASS

### Administration page
The theme has an admistration page. You can access it in `Appearence > Vue WP Theme`. In admin page you'll find:
* Site options\
  You can edit the options. They are defined in `/settings/theme-settings.php`.
* Translations on frontend\
  Edit / create translation files for your Vue app directly from admin page
* Routes\
  Manage Vue routes directly from admin page

### Settings
The settings are stored in a php file, located in `settings/theme-settings.php`. The defined options will be editable in admin page and available in frontend as `this.info`. You can edit it and add new settings.
There are some built in options:
 * Use WP language - Use WP language?
 * Hide WP admin bar - Override the user setting and hide admin bar for everyone
 * WP menu in header - Menu do display in header position
 * WP menu in sidebar - Menu do display in sidebar position
 * Sidebar position - Right, left or disable sidebar
 * Post formats support - Enable support for marked formats

### Translations
The translation files are simple JSON files located in `vue-app/I18n/langs` and you can edit them directly, but there is a simple way to do this in the admin page. The set of strings displayed in admin is read from the code, so it's always up to date with possible new added strings.

### Routes
The current route schema is simple and you can change it as you wish. There is one component for each defined route.

* [route] - [component name]
* **/** - Home
* **/posts** - PostArchive
* **/posts/:slug** - Post
* **/pages** - PageArchive
* **/pages/:slug** - Page
* **/:postType/:taxonomy/:term** - TaxonomyArchive
* **/search/:term** - SearchResults
* **/[anything else]** - NotFound

### The variable `vueWpThemeInfo`
This is a JS variable that holds site information. It is available in Vue components as `this.info`.

	{
		"themeDirUrl": "Theme URL", // Theme directory URL
		"siteUrl": "Site URL",      // Full site URL
		"basePath": "Path",         // Path if site URL contains a sub folder
		"language": "WP language",  // Language defined in Wordpress settings
		"settings": {},             // All settings defined in admin page
		"contentWidth": 900,        // $content_width value
		"loggedUser": false,        // User data object or FALSE if not logged in 
		"wpApiSettings": {          // used by WpApi
			"root": "REST API base URL",
			"nonce": "WP nonce",
			"formats": false // true if some post format is enabled
		}
	}

### Widgets
The Vue WP theme supports widgets. There are two widget areas available. They are located in sidebar and in footer. But you can remove them or add more.
The theme adds the widget `Copyright`, that you can use in footer, to add a text (or link) like '&copy; 2023 Your name'.

### Theme hooks
The theme adds the new hooks `register_vuewp_theme` and `unregister_vuewp_theme`, that works like the functions `register_activation_hook()` and  `register_activation_hook()` works for plugins.

### Install
Paste the theme folder into ```.../wp-content/themes/```. Open a terminal, navigate to ```wp-vue-theme/vue-app``` and run:

	npm install

It can take a while. Than you can start the development server:

	npm run serve

Before you can see the frontend working, there is a little edition on ```wp-config.php```. 
We'll check your config file and add this constant on theme activation, but only if it's not already in the file. So there is a small chance of this value to be 'production' after theme activation. In this case, add the following command to your config file:

	define( 'WP_ENVIRONMENT_TYPE', 'development' );

This constant will define whether our theme loads the development files or the built production version.
The default value is 'production' (if it isn't in your config file), but the theme defaults to 'development'. In other words, while you don't build a package, you need that constant to be 'development' or you won't see the theme frontend.

You'll use ```WP_ENVIRONMENT_TYPE``` as 'development' only while you are editing the files.\
After build your production package, don't forget to change it to 'production'.

### Wordpress tags
 * Grid Layout
 * One Column
 * Left Sidebar
 * Right Sidebar
 * Custom Logo
 * Custom Menu
 * Featured Images
 * Footer Widgets
 * Front Page Posting
 * Full Width Template
 * Theme Options
 * Translation Ready
 * Content Width
