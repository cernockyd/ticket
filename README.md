# Ticket

> Legacy code warning!

> Jednoduchá služba pro nahlašování závad a požadavků pro správce školní sítě.

![Screenshot](https://cdn-images-1.medium.com/max/2000/0*9Bk7hSaFni6FDGei.)


## Předpoklady projektu

- [MariaDB](http://mariadb.org/) nebo [MySQL](http://www.mysql.com/)
- [PHP](http://php.net/downloads.php) 7+
- [composer](http://getcomposer.org/)
- [node.js](http://nodejs.org) Node 6+
- [npm](http://docs.npmjs.com/getting-started/installing-node)

## Techniky

- server side rendering s routingem a jednoduchým MVC frameworkem
- asynchronní zpracování formulářů a jejich výsledků
- minifikace a zpracování javascriptu a css pomocí [Webpacku](https://webpack.github.io/docs/)
- moderní javascript s podporou starších prohlížečů [(Babel)](https://babeljs.io/)
- moderní css s proměnnýma, funkcema a dědičností [(Sass)](http://sass-lang.com/)

## Knihovny

- [jQuery](https://jquery.com/) - práce s DOMem
- [bootstrap 4](https://getbootstrap.com/) - moderní css komponenty
- [bootstrap select](https://github.com/silviomoreto/bootstrap-select)
- [moment](https://github.com/moment/moment)
- [select2](https://select2.github.io/)
- [autosize](https://github.com/jackmoore/autosize) - automatické zvětšování textového pole
- [FastRoute](https://github.com/nikic/FastRoute)
- [tracy](https://github.com/nette/tracy) - debugging tool
- [plates](http://platesphp.com/) - renderování šablon

## Struktura aplikace

```
.
├── app                 - jádro serverové části aplikace
│   ├── config
│   │   └── config.php  - pole s konfigurací aplikace (přístupové údaje atd.)
│   │
│   ├── init.php        - autoloading tříd podle namespaces
│   ├── lib …           - třídy pro nižší procesy (validace, databáze …)
│   ├── models …        - třídy pro práci s daty (uživatelé, karty …)
│   └── presenters …    - presentery zpracují modely a vykreslí šablony, případně
│       │                 vrátí denormalizovaná data pro zpracování v prohlížeči
│       └── templates … - šablony, které budou prezentovány uživateli
│
├── browser             - moduly klientské části
│   ├── styles          - zdrojové soubory kaskádových stylů
│   └── Main.js         - hlavní soubor klientské části (js + scss)
│
├── www
│   ├── app.css         - css podoba v prohlížeči (všechny minifikované moduly)
│   ├── app.js          - funkcionalita v prohlížeči (všechny minifikované moduly)
│   ├── assets          - obrázky a ikony
│   │   ├── favicons …
│   │   ├── icons …
│   │   └── img …
│   └── index.php       - vstupní soubor, načte základní soubory z jádra serverové
│                         části a podle povahy požadavku rozhodne o tom, jaký presenter
│                         a metoda budou použity pro odpověď na požadavek.
│
├── .babelrc            - konfigurační soubor babelu
├── .gitignore          - konfigurační soubor verzovacího systému
├── .htaccess           - konfigurační webového serveru
├── composer.json       - konfigurační soubor composeru (správce php závislostí)
├── package.json        - konfigurační soubor npm (správce js závislostí)
├── README.md           - důležité informace o projektu
└── webpack.config.js   - konfigurační soubor webpacku (module bundler)
```

## Instalace a spuštění aplikace

1. Je potřeba nainstalovat php a javascriptové knihovny, na kterých je aplikace závislá.
Zadejte nad složkou projektu následující příkazy:

  - `composer install` nainstaluje php závislosti
  - `npm install` nainstaluje js závislosti


2. V projektu přibyly složky `vendor` a `node_modules`

3. Nakonfigurujte php aplikaci
v souboru `app/config/config.php`

4. Do zvolené databáze importujte tabulky z `database.sql`

## Příkazy pro vývoj na straně klienta:

Pro okamžitý vývoj klientské části ve složce `browser` (javascript + css)
použijte následující příkazy:

- `npm start` automatické zpracování s reagováním na změny v souborech
- `npm run webpack` jednorázové zpracování javascriptu a scss


## Vývoj strany serveru

Pro vývoj serverové části ve složkách `www` a `app` si prosím pozorně přečtěte dokumentaci.

Vstupním soubor webové aplikace je `www/index.php`
