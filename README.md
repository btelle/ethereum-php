A PHP interface to the geth JSON-RPC API. All documented API functions are present.

## Usage
    // include the class file
    require 'ethereum.php';
    
    // create a new object
    $ethereum = new Ethereum('127.0.0.1', 8545);
    
    // do your thing
    echo $ethereum->net_version();

See `test/test.php` for a complete example. 

## Function documentation
For documentation on functionality, see the [Ethereum RPC documentation](http://ethereum.gitbooks.io/frontier-guide/content/rpc.html)

