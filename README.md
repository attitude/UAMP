UAMP
====

***Universal Analytics Measurement Protocol v1 Component in PHP***

Server-side implementation [Universal Analytics Measurement Protocol](https://developers.google.com/analytics/devguides/collection/protocol/v1/) according documentation in PHP.

v0.1.0 – Proof of concept
-------------------------

* All Measurement Protocol [parameters](https://developers.google.com/analytics/devguides/collection/protocol/v1/parameters)
* Data payload transport using [POST method](https://developers.google.com/analytics/devguides/collection/protocol/v1/reference#transport)
* Universally Unique IDentifier using [Anders Dahnielson's UUID Gist](https://gist.github.com/dahnielson/508447)

### Usage

Easiest way to use the UAMP is by constructing a **hit** and then post it using the **connection**. As more than one hits can occur during one server request a **CID** serves as a glue to tie them together. You can use static method `UAMPv1_Connection::getUUID()` to generate one. You can also Extract UUID from UA cookie and use it.

#### Example:


```php
// You can use any request helper you wish or any at all
$request = Dependency_Container::get('REQUEST');

// Create a UAMP connection
$ua_connection = new \attitude\UAMPv1_Connection;

// Generate UUID
$hit_UUID = $ua_connection::getUUID();

// Prepare array of hits
$hits = array();

// Generate a hit
$hits[] = new \attitude\UAMPv1_Hit(array(
    // Required parameters
    'tid' =>'UA-43852975-2',
    'cid' => $hit_UUID,
    't'   => 'pageview',

    // Document Location
    'dl'  => $request->getLocation()
));

// Send all hits to Google
try {
    foreach ($hits as &$hit) {
        $ua_connection->post($hit);
    }
} catch (\Exception $e) {
    trigger_error($e->getMessage(), E_USER_WARNING);
}

// Done.
```

About
-----

If you like the component, please contribute. Please let me know how it worked for your scenarios. Thanks for your reports, improvements and suggestions.

[@martin_adamko](http://twitter.com/martin_adamko)  
*say hi on Twitter*