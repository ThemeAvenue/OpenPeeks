OpenPeeks
---

## API

### File Structure

OpenPeeks was designed to be easily extendable to new APIs. In order to do so, a new class must be created per extra API to query. The class has to extend the main `OpenPeek` class and the file name must be formatted as follows:

    openpeek-site-name-class.php

The class itself must be named after the file name. If the class name is composed of more than one word, it must be separated by `-` in the file name and `_` in the class name.

#### Example

For a site called Super Pics, the new class should be named `Super_Pics` (each word should start by a capital letter) and the file name should be `openpeek-super-pics-class.php`.

An alternative would be to merge the name in one word:

* Class name: `Superpics`
* File name: `openpeek-superpics-class.php`

### Data Structure

This tool gathers images from various external APIs from free photos sites. Each API returns data formatted differently, which is why we need a new unique data structure and adapt each API results to this new format.

As of version 0.1.0, the data available for each photo is formatted in an array with the following pairs of `key => value` (not all pairs are mandatory):

* `name` *(string)* The name of the photo
* `source` *(string)* The source URL
* `link` *(string)* The download link
* `tags` *(array)(optional)* A list of tags associated with the photo

#### Example

    $images = array(
        array(
            'name'   => 'Photo One',
            'source' => 'http://myphotos.com/strawberry.jpg',
            'link'   => 'http://myphotos.com/strawberry.jpg',
            'tags'   => array( 'fruit', 'strawberry', 'sun' )
        )
    );