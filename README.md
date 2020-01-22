# Lanos 1.0 API Framework

Planning to build a SaaS package? Need to deploy a secure API for an idea you have and need to do it quickly.

### Look No Further!

This framework was designed to deploy quickly and easily. And has been used on all projects by NuLead and Lanos.

### What's Included?

- Eloquent Object Relationship Mapping
- oAuth 2.0, password and client_credentials flows.
- Easy Dependancy Injection
- Easy Auth Guard / Middleware Configuration
- Resource Owner Middleware protection
- Pre-configured for CORS.

# COMING SOON
    An azure container image for this framework!

## Installation

    php composer.phar create-project lanos/api-framework [my-app-name]

Replace `[my-app-name]` with the desired directory name for your new application. You'll want to:

* Point your virtual host document root to your new application's `public/` directory.
* Ensure `logs/` is web writeable.

### Database Creation

You need to create a database, ensure the app has access and configure it in src/settings.php.

Then inside that database create the following schema.

    CREATE TABLE IF NOT EXISTS `oauth_access_tokens` (
      `access_token` varchar(40) NOT NULL,
      `client_id` varchar(80) NOT NULL,
      `user_id` varchar(80) DEFAULT NULL,
      `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `scope` varchar(4000) DEFAULT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
     
    CREATE TABLE IF NOT EXISTS `oauth_authorization_codes` (
      `authorization_code` varchar(40) NOT NULL,
      `client_id` varchar(80) NOT NULL,
      `user_id` varchar(80) DEFAULT NULL,
      `redirect_uri` varchar(2000) DEFAULT NULL,
      `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `scope` varchar(4000) DEFAULT NULL,
      `id_token` varchar(1000) DEFAULT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
     
    CREATE TABLE IF NOT EXISTS `oauth_clients` (
      `client_id` varchar(80) NOT NULL,
      `client_secret` varchar(80) DEFAULT NULL,
      `redirect_uri` varchar(2000) DEFAULT NULL,
      `grant_types` varchar(80) DEFAULT NULL,
      `scope` varchar(4000) DEFAULT NULL,
      `user_id` varchar(80) DEFAULT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
         
    INSERT INTO `oauth_clients` (`client_id`, `client_secret`, `redirect_uri`, `grant_types`, `scope`, `user_id`) VALUES
    ('lanosTest', '098f6bcd4621d373cade4e832627b4f6', NULL, 'password client_credentials', 'user_read user_write', NULL);
         
    CREATE TABLE IF NOT EXISTS `oauth_jwt` (
      `client_id` varchar(80) NOT NULL,
      `subject` varchar(80) DEFAULT NULL,
      `public_key` varchar(2000) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
     
    CREATE TABLE IF NOT EXISTS `oauth_refresh_tokens` (
      `refresh_token` varchar(40) NOT NULL,
      `client_id` varchar(80) NOT NULL,
      `user_id` varchar(80) DEFAULT NULL,
      `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `scope` varchar(4000) DEFAULT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
     
    CREATE TABLE IF NOT EXISTS `oauth_scopes` (
      `scope` varchar(80) NOT NULL,
      `is_default` tinyint(1) DEFAULT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
     
    CREATE TABLE IF NOT EXISTS `oauth_users` (
      `username` varchar(80) NOT NULL DEFAULT '',
      `password` varchar(80) DEFAULT NULL,
      `first_name` varchar(80) DEFAULT NULL,
      `last_name` varchar(80) DEFAULT NULL,
      `email` varchar(80) DEFAULT NULL,
      `email_verified` tinyint(1) DEFAULT NULL,
      `scope` varchar(4000) DEFAULT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
     
    INSERT INTO `oauth_users` (`username`, `password`, `first_name`, `last_name`, `email`, `email_verified`, `scope`) VALUES
    ('test', '$2y$10$U3CMYLB/2xV3xI6lv3Kmfek.R.N.DD1GrFhv4PciknQ15Tcf889nu', 'robert', 'lane', 'test@test.com', 1, 'user_read user_write');
     
    ALTER TABLE `oauth_access_tokens`
    ADD PRIMARY KEY (`access_token`);
      
    ALTER TABLE `oauth_authorization_codes`
    ADD PRIMARY KEY (`authorization_code`);
      
    ALTER TABLE `oauth_clients`
    ADD PRIMARY KEY (`client_id`);
      
    ALTER TABLE `oauth_refresh_tokens`
    ADD PRIMARY KEY (`refresh_token`);
      
    ALTER TABLE `oauth_scopes`
    ADD PRIMARY KEY (`scope`);
        
    ALTER TABLE `oauth_users`
    ADD PRIMARY KEY (`username`);

### Running The Application

To run the application in development, you can run these commands 

	cd [my-app-name]
	php composer.phar start

Run this command in the application directory to run the test suite

	php composer.phar test

That's it! Now go build something cool.
