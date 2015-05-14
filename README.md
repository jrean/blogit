# jrean/blogit

**jrean/blogit** is a PHP wrapper for the Github API (not yet Framework
Agnostic) built for Laravel/Lumen to publish easily your content and "Blog with
Git".

## Goals

- Leverage the power of the Github API
- No database, no backoffice, no forms, ...
- Open source your content and allow easy contributions
- Write, Commit, Push, Live...

## Installation

This project can be installed via [Composer](http://getcomposer.org)
To get the latest version of Blogit, simply add the following line to
the require block of your composer.json file:

    "jrean/blogit": "~0.1.0"
    // or
    "jrean/blogit": "dev-master"

You'll then need to run `composer install` or `composer update` to download it and
have the autoloader updated.

### Add the Service Provider

Once Blogit is installed, you need to register the service provider.

#### Laravel

Open up `config/app.php` and add the following to the `providers` key:

* `'Jrean\Blogit\BlogitServiceProvider'`

#### Lumen

Open up `bootstrap/app.php` and add the following:

* `$app->register('Jrean\Blogit\BlogitServiceProvider');`

### Enable Dotenv File (Lumen only)

Uncomment the following line in `bootstrap/app.php`:

    // Dotenv::load(__DIR__.'/../');

## Configuration

Update your `.env` file with the following keys and assign your
values:

    GITHUB_USER                    =your_github_user_name
    GITHUB_TOKEN                   =your_github_token
    GITHUB_REPOSITORY              =your_repository_name
    GITHUB_ARTICLES_DIRECTORY_PATH =content_root_directory_name

* `GITHUB_TOKEN`

Your Github username.

* `GITHUB_TOKEN`

Visit [Github](https://github.com/settings/tokens) and create a `Personal
Access Tokens`

* `GITHUB_REPOSITORY`

Create a new `Public` repository or use an existing one.

* `GITHUB_ARTICLES_DIRECTORY_PATH`

Inside your repository create a directory (for instance `articles`) where you
will push your files.

## Basic usage

### Without Controller

Update `app/Http/routes.php`:

    use Jrean\Blogit\Blogit;

    $app->get('/blogit', function() use($app) {

        // Jrean\Blogit\Blogit instance
        $blogit   = $app->make('blogit');

        // Jrean\Blogit\BlogitCollection
        $articles = $blogit->getArticles();

        // Last 3 created Articles
        $news     = $articles->sortByCreatedAtDesc()->take(3);

        // Last 3 updated Articles
        $updates  = $articles->sortByUpdatedAtDesc()->take(3);

        return view('blogit.index', compact('news', 'updates')); });

    $app->get('/blogit/{slug}', function($slug) use($app) {

        // Jrean\Blogit\Blogit instance
        $blogit  = $app->make('blogit');

        // Jrean\Blogit\Document\Article
        $article = $blogit->getArticleBySlug($slug);

        if ($article === null) abort(404);

        return view('blogit.show', compact('article'));
    });

### With Controller

Update `app/Http/routes.php`:

    $app->get('/', [
        'uses' => 'App\Http\Controllers\YourController@index',
        'as'   => 'index'
    ]);

    $app->get('/{slug}', [
        'uses' => 'App\Http\Controllers\YourController@show',
        'as'   => 'show'
    ]);

Update `app/Http/Controllers/YourController.php`:

    <?php namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use Jrean\Blogit\Repository\DocumentRepositoryInterface;

    class YourController extends Controller {

        private $blogit;

        public function __construct(DocumentRepositoryInterface $blogit)
        {
            $this->blogit = $blogit;
        }

        public function index()
        {
            $articles = $this->blogit->getArticles();
            $news     = $articles->sortByCreatedAtDesc()->take(3);
            $updates  = $articles->sortByUpdatedAtDesc()->take(3);

            return view('blogit.index', compact('news', 'updates'));
        }

        public function show($slug)
        {
            $article = $this->blogit->getArticleBySlug($slug);

            if ($article === null) abort(404);

            return view('blogit.show', compact('article'));
        }
    }

### Views (Blade for instance)

    @foreach($articles as $article)
        {{ $article->getTitle() }}
        {{ $article->getSlug() }}
        {{ $article->getLastCommitUrl() }}
        {{ $article->getUpdatedAtDiff() }}
        {{ $article->getTags() }}
        {{ $article->getHtml() }}
        {{ $article->getContributors() }}
        ...
    @endforeach

## Basic File Format

I recommand to stick with `.md` files:

    title: My Article Title
    slug: my-custom-slug
    tags: [tag, tag, tag]
    -------
    ## My Markdown Content 

The `.md` file contains two sections separated by `-------`. One for the
`metadata`, one for the `content`.

### Metadata

Metada will be parsed as `Yaml` so feel free and creative because
you'll have access to that metadata as an `array`. The only required key is the
`title`. `slug` and `tags` are optionnals. If you don't provide a custom
`slug`, one will be auto generated based on the `title` value...

### Content

Everything under the `-------` delimiters will be parsed as
`Markdown`. Again, fell free and creative.

## Extending and Customize Blogit

I try and will try my best to give you the
ability to extend and override `Blogit`. For now you can easily hack and extend
`Jrean\Blogit\Blogit.php` and `Jrean\Blogit\BlogitCollection.php` to bring
your own logic, methods and attributes.

## Contribute

This package is (yet) under active development and refactoring.
Please, feel free to comment, contribute and help. I would like/love to bring
`Unit tests`.

## Example

I will write soon a dedicated article for `Blogit` which is now used
on `Production` for [Askjong.com](http://www.askjong.com "AskJong, Quick Updates and Practical Approaches about anything Tech., Laravel, Vim, Php, DigitalOcean and Web Programming.")

## License

Blogit is licensed under [The MIT License (MIT)](LICENSE).
