# Studyportals' CMS

The routing and page compositing framework behind
**[Mastersportal.com](https://www.mastersportal.com)** _et al._

- [Development](#development)
  - [Git-hooks](#git-hooks)
- [Using the CMS](#using-the-cms)
- [Origins of the CMS](#origins-of-the-cms)

## Development

Requires [PHP 7.2](https://www.php.net/downloads.php#v7.2.32).

```shell
composer install
```

Test-coverage has been worked up substantially over the past years, but is not
yet complete. A broad set of integration-tests is available; many unit-tests are
still missing...

```shell
composer run phpunit
```

Apart from [PHPUnit](https://phpunit.de/), there's full compliance with
[PHPStan](https://github.com/phpstan/phpstan),
[PSR-12](https://www.php-fig.org/psr/psr-12/) and a subset of
[PHPMD](https://phpmd.org/):

```shell
composer run phpstan
composer run phpstan:tests # slightly relaxed for tests/**

composer run phpcs

composer run phpmd
composer run phpmd:tests # slightly (more) relaxed for tests/**
```

### Git-hooks

This project uses
[composer-git-hooks](https://github.com/BrainMaestro/composer-git-hooks) to
enable the following Git-hooks:

- `pre-push` &ndash; run all of the tests listed above.
- `checkout` &ndash; upon switching branches, run an
  [auto-update script](https://github.com/studyportals/repo-template-php#automatic-updates)
  and check if the repository is still in-sync with
  [repo-template-php](https://github.com/studyportals/repo-template-php).
- `pre-commit` &ndash; prevent committing directly in the `master`- and
  `develop`-branches; use pull-requests instead.

## Using the CMS

With exception of the single code-snippet below, no documentation is available
as of yet... 😇

The routing logic (e.g. translating `path/to/a/page.html` to the actual page to
be displayed) is part of the CMS. Each individual implementation has to provide
its own logic to handle the broader environment though. Things such as picking
up signals from the HTTP-server, gracefully handling error conditions, issuing
HTTP-redirects and managing client-side caching behaviour are not managed by the
CMS.

The most basic implementation of the CMS looks something like this:

```php
$site = $this->siteHandler->createSite();
$this->templateHandler->initTemplate($site);

// Set page according to virtual-path (e.g. via Apache RewriteRules)

$virtual_path = trim(
    $this->inputHandler->get(INPUT_GET, 'virtual_path') ?? ''
);

$site->getPageTree()->setPageByPath($virtual_path);

// Show page

echo (string) $site->getPage()->display();
```

## Origins of the CMS

The CMS traces its origins back to around 2004. It has been powering
[Mastersportal.com](https://www.mastersportal.com) (and its predecessor
[MastersPortal.eu](https://web.archive.org/web/20070601000000*/www.mastersportal.eu))
since mid-2007.

It started out as a fully fledged _Content Management System_ (hence its name
and that of many of its components), but over the years evolved into the routing
and page compositing framework it currently is.

Notable features that got _**removed**_ over the years:

- A drag-and-drop WYSIWYG administrative interface allowing for on the fly
  modifictions of the site, with configuration stored in an SQL-database &ndash;
  replaced with a static page-tree defined using PHP classes.
- Multi-language support (via the `Accept-Language`-header) including an
  interactive WYSIWYG editor enabling all static interface elements to be
  translated _in situ_ &ndash; the sheer complexity of this approach never
  matched any of its returns; given the current structure of CMS, a simpler and
  equally effective approach can be constructed on top of this package.
- A dynamic (i.e. runtime) asset "packer", reducing a multitude of JavaScript-
  and CSS-files to a limited set of asset "packs" (both site-wide and at the
  level of individual pages) &ndash; replaced by a
  [Webpack](https://webpack.js.org/)-powered build-process (not part of this
  repository).

Notable contributors over the years:

- [\@Alfalfamale](https://github.com/Alfalfamale)
- [\@braaibander](https://github.com/braaibander)
- [\@robjanssen](https://github.com/robjanssen)
- [\@CZYK](https://github.com/CZYK)
- [\@iliqportals](https://github.com/iliqportals)
- [\@NikolaNikushev](https://github.com/NikolaNikushev)
- [\@dee-me-tree-or-love](https://github.com/dee-me-tree-or-love)
- [\@thijsputman](https://github.com/thijsputman)
