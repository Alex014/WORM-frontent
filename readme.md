# WORM
## The idea
WORM is a shortcut for World Object MaRkup language, a markup language to describe all real world objects (product, hotel, article, drone, ... ) needed to be found.
In other words WORM is an HTML for searchable objects.
WORM is an XML extension.
In a larger scale it's a search system, which consists of 4 things
1. A WORM file.
2. A blockchain with a link to a WORM file or including WORM file.
3. A Data Collector, which collects data through blockchain and puts the data into database.
4. Frontend for selecting, sorting and displaying the data to the user.

Data Collector together with the Frontend is a Search Engine.
There can be many Search Engines for different types of objects.
In our case we have a Search Engine specialized in products.
It's not a final solution, but a technology demo.

This is a frontend part of a search system.
## Diagram
```
  [SITE 1]  [SITE 2]  [SITE 3]  ...  [SITE n]
  
     ||        ||        ||             ||
     ||        ||        ||             ||
     \/        \/        \/             \/
     
     =====================================
              [[[ BLOCKCHAIN  ]]]
     =====================================
  
      ||             ||                ||
      ||             ||                ||
      \/             \/                \/
     
   [SEARCH]      [SEARCH]           [SEARCH]
   [ENGINE]      [ENGINE]           [ENGINE]
   [  1   ]      [  2   ]           [  n   ]
                                       ||
                    ||=================||
                    ||      
                    \/                                 
             {DATA COLLECTOR}
                    ||
                    ||
                    \/      
                {DATABASE}
                    ||
                    ||
                    \/
                {FRONTEND}    
```
## Installation
Create session table in database
```sql
CREATE TABLE `sessions` (
  `session_id` char(48) NOT NULL,
  `init_unixtime` int(10) unsigned NOT NULL,
  `last_request_unixtime` int(10) unsigned NOT NULL,
  `expire_unixtime` int(10) unsigned DEFAULT NULL,
  `request_signature` varchar(96) DEFAULT NULL,
  `writes` int(4) unsigned DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`session_id`),
  UNIQUE KEY `session_id_UNIQUE` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```
### Dependencies
* MySQLSessionHandler at https://github.com/zpweber/MySQLSessionHandler
* MeekroDB at https://github.com/SergeyTsalkov/meekrodb
* MySQL compatible database
### Configuration
`config/db.php` - database config
## Test run
Run `public/test-data.php` to generate test data, which can be used to test Data Collector.
## Production
Run `public/index.php`
## Donations
* Bitcoin bc1qfw0aadqdxeqmuaqxxelracfnvw0la0h2d870al
* Emercoin ELdkWCGkU1dkUME41ksNVy4nXijf3BsnB9
## Contacts
Email: chosenone111@protonmail.com
