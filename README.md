#Usage
##Installation
###Step 1. Put our library into your repository
Inside `lib/`, lives:
* `BillForward.php`
* `BFPHPClient/`

Put these files in your repository.


###Step 2.  Include our library from your code
Point to where `BillForward.php` is in your repository:
```
require_once('path/to/BillForward.php');
```
This file will autoload the library.


###Step 3. Use credentials to connect to BillForward
####Step 3.1. Getting BillForward credentials
Login to (or Register) your BillForward Sandbox account:

https://app-sandbox.billforward.net/login/#/

https://app-sandbox.billforward.net/register/#/

Grab an API token from:

https://app-sandbox.billforward.net/setup/#/personal/api-keys

####Step 3.2. Connect to BillForward using BfClient
Having included `BillForward.php`, you can now make an instance of BfClient:

```
$access_token = 'YOUR ACCESS TOKEN HERE';
// example urlRoot (version number subject to change):
// https://api-sandbox.billforward.net/2014.223.0/
$urlRoot = 'BILLFORWARD API URL';
$client = new BfClient($access_token, $urlRoot);
```

####Step 3.3. Make API calls using BfClient
You can now make requests using the `$client` instance.
```
$accounts = $client
->accounts
->getAll();
```

##Examples
See `ExampleUsage.php` for a full use-case of interacting with the API.

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

UNIX users will already have curl. Windows users might want to look here:

http://curl.haxx.se/dlwiz/?type=bin&os=Win32&flav=-&ver=*

###Step 2. Installing gradle.
This step installs our build system, Gradle.

*Brave users can skip to Step 3; gradle will self-install itself if it needs to*


(Requires Java on PATH).

We provide a Gradle self-installer for Windows and UNIX.

It is in the `build` directory.

Invoke with:

```
gradlew
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
gradlew setup
```


#Running tests (for devs)
##Setup
Tests connect to the user defined in `TestConfig.php`.

Most tests require data to exist on the account you log into.

###Step 1. Register on Sandbox
Register a user on Sandbox, that you are happy with using for tests.

Get an access token for this user.

###Step 2. Point TestConfig at this user
Provide an access token and API URL for connecting.

```
$this->access_token = '';
// example urlRoot (version number subject to change):
// https://api-sandbox.billforward.net/2014.223.0/
$this->urlRoot = '';
```

You will notice also many blank `usual` variables (for example `$this->usualLoginUserID = '';`). They are IDs for real entities. Tests are invited to play with these entities.

This way we avoid hardcoding 'magic numbers' into tests; instead they get() an entity advertised in the config.


You will need to populate these variables with IDs from real data. We will generate that dataset now.

###Step 3. Run the one-off `BuildSampleDataTest`
This test — `BuildSampleDataTest_OneOff.php` — creates many entities on the BillForward system, and prints out their IDs in a format that can be copy-pasted into your TestConfig.

Running this test is the standard way of preparing a new user account to have tests run against it.


We use phpunit as our testrunner. If you have run `gradlew setup`, you will find already a phpunit binary in `vendor/bin/phpunit`.

(Requires phpunit on PATH).

You can run this particular test by invoking:

```
phpunit BuildSampleDataTest_OneOff.php
```

Provided this test succeeds, you will see it print a copy-pasteable set of IDs you can use in your `TestConfig.php`.

##Invocation
Setup data on your test user (see previous section), and copy-paste it into your `TestConfig.php`, otherwise tests will receive no data and fail.


(Requires phpunit on PATH).

To run all tests, cd to the repository root and invoke:
```
phpunit
```

phpunit will read `phpunit.xml` to find tests.

The tests themselves rely on `vendor/bin/autoload.php` (generated by Composer) to find their dependencies.


#Making changes (for devs)
It is a good idea to run `gradlew setup` after adding, renaming or moving a class; this will regenerate the autoloader `BillForward.php` and its classmap. The new `BillForward.php` should be part of any commit.

Confirm also that the usual unit test run passes (run `phpunit` from the project root). You will need data in your `TestConfig.php` for these tests to pass.
