#Usage
##Install (via Composer)
Our library can be installed via the [Composer](http://getcomposer.org/) package manager.

Add us to your `composer.json`:
```
{
    "require": {
        "billforward/bf-php": "1.*"
    }
}
```

Install the new package:
```
composer.phar install
```

Now include our code.
Either use Composer's [autoloader](https://getcomposer.org/doc/00-intro.md#autoloading):
```
require_once('vendor/autoload.php');
```

Or include manually:
```
require_once('/path/to/vendor/billforward/bf-php/lib/BillForward.php');
```

##Install (manually)
####Step 1. Put our library into your repository
Inside `lib/`, lives:
* `BillForward.php`
* `BFPHPClient/`

Put these files in your repository.

####Step 2. Include our library from your code
Point to where `BillForward.php` is in your repository:
```
require_once('path/to/BillForward.php');
```
This file will autoload the library.

##Invocation
###Use credentials to connect to BillForward
####Step 1. Getting BillForward credentials
[Login](https://app-sandbox.billforward.net/login/#/) to (or [Register](https://app-sandbox.billforward.net/register/#/)) your BillForward Sandbox account.

[Grab a Private API token](https://app-sandbox.billforward.net/setup/#/personal/api-keys).

####Step 2. Connect to BillForward using BillForwardClient
Having included `BillForward.php`, you can now make an instance of BillForwardClient.

It can be used as the default client for all requests:

```
$access_token = 'YOUR ACCESS TOKEN HERE';
$urlRoot = 'https://api-sandbox.billforward.net:443/v1/';
$client = new BillForwardClient($access_token, $urlRoot);
BillForwardClient::setDefaultClient($client);
```

####Step 3. Make API calls using BillForwardClient
Construction of any BillForwardClient automatically registers that client as the 'default client'.

Requests can now be made. These use the 'default client' implicitly:

```
$accounts = Bf_Account::getAll();
```

You can also explicitly specify a client. This is useful when connecting using multiple clients -- i.e. when migrating a user's data:

```
// use null queryparams, and specify the client to use for the request
$accounts = Bf_Account::getAll(null, $client);
```

##Examples
See `ExampleUsage.php` for a full use-case of interacting with the API.

View the examples in our [PHP Client Library Docs](https://app-sandbox.billforward.net/api/#/method/accounts/POST?path=%2Faccounts&api=PHP).

We provide also in the `test` folder some tests that show usage.


##Compatibility
The BillForward PHP Client Library is expected to work on PHP 5.3+.
Required PHP extensions include:

```
cURL
json_decode
```


#Building (for devs)
We use a build system. This is Gradle.

We use a package manager. This is Composer.

Mostly the build system is used for interacting with / invoking Composer.


The build system provides these powers:
* Ability to run tests
  * Composer installs phpunit
  * Composer generates classmap for tests to find our source code
* Ability to regenerate project's 'autoload' classmap (for example if you add a class)
  * Composer generates classmap
  * Build system redraws `Billforward.php` autoloader from that classmap

##How to build
To get the workspace setup, there are three steps:

1. Install and add to PATH: java, curl, php
2. Install gradle
2. Run gradle setup

###Step 1. Installing Java, curl and PHP.
These are well-documented; follow normal installation process.

UNIX users will already have curl. Windows users might want to look [http://curl.haxx.se/dlwiz/?type=bin&os=Win32&flav=-&ver=*](here).

###Step 2. Installing gradle.
This step installs our build system, Gradle.

*Brave users can skip to Step 3; gradle will self-install itself if it needs to*


(Requires Java on PATH).

We provide a Gradle self-installer for Windows and UNIX.

It is in the `build` directory.

Invoke with:

```
cd build
./gradlew
```

###Step 3. Run gradle setup
This step runs a 'setup' build, which:

1. Self-installs Composer (if necessary)
2. Installs Composer packages (if necessary). [Classmap is also generated at this time]
3. Creates `BillForward.php` autoloader from that classmap


(Requires CURL on PATH: to self-install Composer).

(Requires PHP on PATH: to invoke Composer).

We provide a Composer self-installer for Windows and UNIX.
Invoke with:

```
cd build
./gradlew setup
```


#Running tests (for devs)
##Setup
Tests login using the credentials you define in: `test/BFPHPClientTest/new/config.php`. A `config.example.php` is provided to guide you in creating this file.

Ideally the credentials should refer to an account that exists solely for testing, as tests cause side-effects (entities created upon your organization).

###Step 1. Register on Sandbox
[Login](https://app-sandbox.billforward.net/login/#/) to (or [Register](https://app-sandbox.billforward.net/register/#/)) a Sandbox account, that you are happy with using for tests.

[Grab a Private API token](https://app-sandbox.billforward.net/setup/#/personal/api-keys).

###Step 2. Create a `config.php` inside `test/BFPHPClientTest/new/`

```php
<?php
$credentials = array(
	'urlRoot' => 'https://api-sandbox.billforward.net:443/v1/',
	'access_token' => 'INSERT_ACCESS_TOKEN_HERE'
	);
```

We use phpunit as our testrunner. If you have run `./gradlew setup`, you will find already a phpunit binary in `vendor/bin/phpunit`.

##Invocation
(Requires phpunit on PATH).

To run all tests, cd to the repository root and invoke:
```
phpunit
```

phpunit will read `phpunit.xml` to find tests and find which classes to autoload.

The tests themselves rely on `vendor/bin/autoload.php` (generated by Composer) to find their dependencies.

###Run individual file
A custom Sublime script can be used, for example (requires `phpunit` on PATH):

```
{
	"cmd": ["phpunit", "$file"],
	"selector": "source.php"
}
```

Or just invoke normally from anywhere using the terminal:

```
phpunit AccountTest.php
```

As with the full run, phpunit will climb the project structure looking for `phpunit.xml`.

#Making changes (for devs)
After adding, renaming or moving a class (ie, changing the classmap), use the build system to regenerate the autoloader `BillForward.php` and its classmap.

Specifically, cd into `/build` and invoke:

```
./gradlew regenerateClassmap recompileClasspath
```

The newly-generated `BillForward.php` should be part of any commit.

Confirm also that the usual unit test run passes (run `phpunit` from the project root). Refer to the 'Setup' section for instructions on configuring your test run.

#Release automation

Calculates date and major/minor version of release, generates and names zipped release in root dir, tags commit with version string, pushes tag.

You will still need to go to GitHub and draft a release of the tagged commit (so you can add release notes and attach the zipped file).

New major version:

```bash
cd build
sh ./releaseHelpers/release incrementMajorVer
```

Same major version:

```bash
cd build
sh ./releaseHelpers/release
```

Untag latest release:

```bash
cd build
sh ./releaseHelpers/delete
```