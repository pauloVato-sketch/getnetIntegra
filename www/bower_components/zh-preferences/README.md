# Zeedhi Preferences

## Introduction
Provides the methods to create and save preferences on Zeedhi projects.

**zh-preferences** provides the following features:

 - Save preferences for each user
 - Allow the user to define a default preference for a widget

## Requirements

This project requires:

 - **[Zeedhi Framework][FRAMEWORK_FRONT]** v1.48.1 or higher
 - A backend project that uses **[zh-preferences][BACKEND_API]** backend library

## How to start
In order to start using preferences you need to add the *JS* and *CSS* files into the `index.html` file of your project.
The files are located inside your project's **bower_components** folder in `zh-preferences/dist` directory.

**JS:**
```html
<script src="bower_components/zh-preferences/dist/preferences.js" />
```
You can also use the minified version by changing the file name to **preferences.min.js**.

**CSS:**
```html
<link rel="stylesheet" href="bower_components/zh-preferences/dist/style/style.css">
```
There are a few **CSS** files with **customized colors**, they can be found **[here][CSS_FILES]**. In order to use a customized template just change the file name to the color file that you want.

## Configuration
**Zh Preferences** require some package configuration.

You need to define the **metadataUrl** package that will be used to retrieve the datasources files.
**E.g.:**
```
window.metadataUrls.push({
    package: 'zh-preferences',
    baseUrl: 'bower_components/zh-preferences/dist/assets/'
});
```

You also need to define the **serviceUrl** that will be used to retrieve the preferences.
**E.g.:**
```
window.serviceUrls.push({
    package: 'zh-preferences',
    baseUrl: '../backend/service/index.php'
});
```
The path used on the **serviceUrl** must be a Zeedhi Project that uses the **zh-preferences** backend library. More info can be found [**here**][BACKEND_API].


> The library already register it's packages using the **default** configuration shown on the examples above.
>
> If you need to override any configuration you must add the package into the **window** variables before adding the JS preference file.

**Note** that all packages must be named **zh-preferences**.

## Handle user authenticated
**zh-preferences** binds each preference to a user, so it is necessary to inform which user is currently authenticated. To archieve that you have to call `ZhPreferences.setUserID(String userID)` passing the currently authenticated user unique identifier.

That are **three methods** that handle user information:
**unsetUser**(): *void*
Remove all user data locally saved

**setUserID**(*String* userID): *void*
Set currently user authenticated ID

**getUserID**(): *Promise.&lt;String&gt;*
Retrieve the currently authenticated user ID

## Advanced Usage
For advanced usage you can visit the **[API documentation][DOC]**.

[FRAMEWORK_FRONT]: http://code.zeedhi.com/zeedhi/frameworkFrontend/tree/master
[CSS_FILES]: http://code.zeedhi.com/zeedhi-components/zh-customizations-service/tree/master/dist/style/colors
[BACKEND_API]: http://code.zeedhi.com/zeedhi/zh-preference-backend/tree/master
[DOC]: http://docs.zeedhi.com/docs/preferences/frontend-api