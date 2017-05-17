# Omnilance GraphQL PHP Client 

A simple client to help execute queries and mutations across GraphQL

-----------------------


## Installation

`composer require omnilance/graphql-php-client`
or 
`composer require "omnilance/graphql-php-client": ">=0.0.3"`


-----------------------


## Simple Usage

```
use Omnilance\GraphQL\Client;
$client = new Client($api_token);
```

additionaly you can set custom host
```
$client->setHost($host);
```

### Run query

```
$query = '{ 
	domain_check(domain: "rx-name.net" {
		cost,
		avail
	}
}';
$response = $client->response($query);

$query = '{
    	allDomains(first:10) {
		domain {
		    	id,
		    	name,
		    	register_date,
		    	expired_date,
		}
    }
}';

$response = $client->response($query);

foreach($response->allDomains->domain as $domain) {
    print $domain->name;
}
```


-----------------------



## Response class
### all()

Use `$response->all();` to get all of the data returned in the response

### errors()

Use `$response->errors();` to get all the errors returned in the response

### hasErrors()

Use `$response->hasErrors();` to check if the response contains any errors

