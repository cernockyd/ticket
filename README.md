# Ticket

> Legacy code warning!

> Simple service for reporting bugs and sharing requirements between school network/tech administrators and school network users.

![Screenshot](https://cdn-images-1.medium.com/max/2000/0*9Bk7hSaFni6FDGei.)


## Prerequisites

- [MariaDB](http://mariadb.org/) nebo [MySQL](http://www.mysql.com/)
- [PHP](http://php.net/downloads.php) 7+
- [composer](http://getcomposer.org/)
- [node.js](http://nodejs.org) Node 6+
- [npm](http://docs.npmjs.com/getting-started/installing-node)

## Used technics

- server side rendering with routing and simple MVC framework
- asynchronous forms processing (very badly implemented)
- javascript and css minification using [Webpack](https://webpack.github.io/docs/)
- modern javascript with support for older browsers [(Babel)](https://babeljs.io/)
- modern css with variables, functions etc. [(Sass)](http://sass-lang.com/)

## Libraries

- [jQuery](https://jquery.com/) - manipulating with DOM
- [bootstrap 4](https://getbootstrap.com/) - modern css components
- [bootstrap select](https://github.com/silviomoreto/bootstrap-select)
- [moment](https://github.com/moment/moment)
- [select2](https://select2.github.io/)
- [autosize](https://github.com/jackmoore/autosize) - atomatic textarea resizing
- [FastRoute](https://github.com/nikic/FastRoute)
- [tracy](https://github.com/nette/tracy) - debugging tool
- [plates](http://platesphp.com/) - php templates rendering


## Installation

1. First of all You need to install php and javascript dependencies.
Run following commands in the project folder:

  - `composer install` downloads and installs php dependencies
  - `npm install` installs javascript dependencies


2. New folders have been added to the project: `vendor` and `node_modules`

3. Configure php application
in file `app/config/config.php`

4. Import/create database from `database.sql`

## Commands for frontend development

For instant development for frontend (javascript + css) open `browser` folder
and run these commands:

- `npm start` automatically processes javascript and scss and watches files for changes
- `npm run webpack` one-time process of javascript and scss


## Serverside development

For the development of the server parts in `www` and` app` folders, please read the [docs](https://medium.com/@cernockyd/dokumentace-maturitn%C3%AD-pr%C3%A1ce-ticket-system-822cc08aa029) carefully.

The web application starting file is `www/index.php`
