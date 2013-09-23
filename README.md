PHP EPUB Reader
===============

Reading epub format with php

==========================================

Start Include Class file to your project :

    require_once('epub.class.php');


Declare Book for Class :

    $epub = new pages('BookName');
    
##Read Options :
================

read page 10 of book :

    echo $epub->read('10');
    
Option List :

-------------------------------------------
| Symbol  | Meaning                       |
|------------------------------------------
|    *    | Load All Pages                |
|------------------------------------------
|   X>5   | Select Pages Greater Than 5   |
|------------------------------------------
|   X<5   | Select Pages Smaller Than 5   |
|------------------------------------------
|    5    | Select Page 5                 |
|------------------------------------------
|   5-10  | Select Pages Between 5 and 10 |
|------------------------------------------


    

