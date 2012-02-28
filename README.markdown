# Resources Plugin

Resources is an [YPFramework](https://github.com/yonpols/ypframework) plugin that
allows you to serve static assets like images, stylesheets, javascripts and other
binary resources in a controlled, cached and processed way.

## Installation

To install resources plugin on your application simply download this repo and put it
under your applications plugins directory ({APP_PATH}/extensions/plugins/resources).
Make a *resources* directory under your app path and put there your assets.

## Configuration

Resources works out of the box when you put it in your applications plugin path.
However if you want to customize it's settings you can. Take a look at resources.config.yml.
This file contains all settings you can customize. Simply copy the file to your applications
path, include it on your main config.yml and voila!

## Stylesheets and Javascripts Syntax

Each processed .css or .js file will be parsed to check for a special syntax that allows
to include other files. If the file contains a /**/ comment starting on the first line
it will be parsed. The syntax is as follows:

`/*`<br />
` *=require main.css`<br />
` *=require_tree special`<br />
` *=require_all plugins`<br />

`*/`

One line starting the comment, one or more lines specifying which files to include, and
finally one line ending the comment. There are three different commands to include files:

- `*=require <file_name>` To include a file present at the same level of the parent file. This file will be searched accross all components paths of the application. The first that is found will be included.
- `*=require_tree <path>` To include a list of files present under the path specified. The first path that is found will be included.
- `*=require_all <path>` This is similiar to `require_tree` except that **all** paths found will be included. This is useful for plugins that publish assets files that must be included.

## CoffeeScript and Less Compilation

Resources includes content filters to compile coffee and less files into their respective
javascript and css counterparts. In order for this functionality to work you need to install
[node.js](http://nodejs.org/). Once installed type in your terminal

`npm install -g coffee-script`

`npm install -g less`
