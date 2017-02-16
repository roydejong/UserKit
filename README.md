# UserKit

### :warning: Heads up
The information in this Readme guide is completly provisional; this is a concept project under development. It's not ready for real use yet, at all. So sorry! :broken_heart:

### What is UserKit?

**UserKit is a PHP library for server side analytics that helps you keep track of your users. A powerful drop in user admin panel. It's kind of like Intercom, but completely local.**

Here's some cool stuff you can do with UserKit:

- Customer intelligence: who's using your app, and what are they up to?
- Track events and custom properties for your users.
- Segment users get useful insights.

### Getting started

##### Requirements

- PHP 7.1 or newer
- [Composer](https://getcomposer.org/doc/00-intro.md)
- A database (MySQL, SQLite, MSSQL or Postgres)

##### Installation

You can install UserKit via composer. This will set up the library as a dependency and install the autoloader.

    composer require roydejong/userkit
    
Userkit currently uses Browscap to collect user agent data, for which you'll need to download up-to-date definitions:

    vendor/bin/browscap-php browscap:update
    
And that's it. You're ready to start integrating.
    
### Integrating into your app

Next, you'll need to integrate UserKit into your app so it can gather analytics.

##### Starting up

When your application starts, you'll need to provide your database configuration. Here's what that looks like when you're connecting to a MySQL database:

    UserKit::configure()
        ->setConnectionString('mysql://user:pass@127.0.0.1/dbname?charset=utf8');
        
UserKit will **automatically** install and upgrade itself onto the database you connect it to.

All of its tables will be prefixed with `userkit_`. You can create a separate database if you'd like, or use your existing database if you don't mind a few extra tables.
 
##### Gathering basic analytics
 
To capture a request, all you need to do is call on the `capture()` method:

    UserKit::capture()
    
This will then capture and log the current request and environment data as a single incoming request, which will start to give you some basic activity data.

You can enrich your capture with more data to get more useful insights. For example, if a user is logged in, you can attach some data:

    UserKit::capture()
        ->user([
            'id' => 1234,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com'
        ])
        
Note: You can call `capture()` multiple times to add more data to the capture. Your data will still be logged once. A capture is saved to the database when your script shuts down, or when you manually call `flush()` on the capture object.   
