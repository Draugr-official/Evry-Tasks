<?php
$csv_content = file_get_contents( "db.csv" );
$connection = new mysqli( "localhost", "username", "pass", "dbname" );

if ( $mysqli->connect_errno ) {
    printf( "Connect failed: %s\n", $mysqli->connect_error );
    exit( );
}

class CSVParser
{
    // Var decl
    protected $filecontent;
    
    /**
     *
     * Parses a CSV file into a 2D array
     *
     * @return   2D array
     *
     */
    public function parseCSVFile( ) 
    {
        $lines = explode( "\n", str_replace( '"', "", $this->$filecontent ) );
        $result = array( );
        
        for($i = 0; $i < sizeof( $lines ); $i++)
        {
            $result[$i] = explode( ",", $lines[$i] );
        }
        
        return $result;
    }
    
    /**
     *
     * General constructor
     *
     * @param    $filecontent -> CSV file content
     * @return   2D array
     *
     */
    public function __construct( string $csv ) 
    {
        $this->$filecontent = $csv;
    }
}

class Person
{
    public $FirstName;
    public $LastName;
    public $Age;
    
    public function __construct( string $firstname, string $lastname, int $age ) 
    {
        $this->FirstName = $firstname;
        $this->LastName = $lastname;
        $this->Age = $age;
    }
}

/* Parsing the CSV file into the table 
$parser = new CSVParser( $csv_content );
$csv = $parser->parseCSVFile( );

var_dump(mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM `fact_people`")));

for($i = 1; $i < sizeof($csv); $i++)
{
    mysqli_query($connection, "INSERT INTO `fact_people` VALUES ('" . $csv[$i][0] . "','" . $csv[$i][1] . "','" . $csv[$i][2] . "')");
    echo $csv[$i][0] . "," . $csv[$i][1] . "," . $csv[$i][2] . "<br>";
}
*/

$rowCount = mysqli_num_rows( $connection->query( "SELECT * FROM fact_people" ) );
echo "This table contains " . $rowCount . " rows<br><br>";


echo 'Here is a list of the youngest people<br>';
$youngest = $connection->query( "SELECT * FROM fact_people WHERE Age = (SELECT MIN(Age) FROM fact_people)" );
$personYoungest;

if ( $youngest->num_rows > 0 ) 
{
    while( $rad = $youngest->fetch_assoc( ) )
    {
        echo $rad['First Name'] . ' at ' . $rad['Age'] . ' years old<br>';
        $personYoungest = new Person( $rad['First Name'], $rad['Last Name'], intval( $rad['Age'] ) );
    }
}
else
{
    echo "No results<br>";
}

$oldest = $connection->query( "SELECT * FROM fact_people WHERE Age = (SELECT MAX(Age) FROM fact_people)" );
$personOldest;

if ( $oldest->num_rows > 0 ) 
{
    while( $rad = $oldest->fetch_assoc( ) )
    {
        $personOldest = new Person( $rad['First Name'], $rad['Last Name'], intval( $rad['Age'] ) );
    }
}
else
{
    echo "No results<br>";
}


echo '<br>Here is a list of everyone named Blake<br>';
$theBlakes = $connection->query( "SELECT `Last Name` FROM fact_people WHERE `First Name` = 'Blake'" );
if ( $theBlakes->num_rows > 0 ) 
{
    while( $rad = $theBlakes->fetch_assoc( ) )
    {
        echo 'Blake ' . $rad['Last Name'] . '<br>';
    }
}
else
{
    echo "No results<br>";
}

echo '<br>The youngest person is called ' . $personYoungest->FirstName . ' ' . $personYoungest->LastName . ' and is ' . $personYoungest->Age . ' years old';
echo '<br>The oldest person is called ' . $personOldest->FirstName . ' ' . $personOldest->LastName . ' and is ' . $personOldest->Age . ' years old';
echo '<br>The age gap between these two people is ' . ($personOldest->Age - $personYoungest->Age) . ' years';

$average = $connection->query( "SELECT avg(Age) as avgAge FROM fact_people" );
if ( $average->num_rows > 0 ) 
{
    while( $rad = $average->fetch_assoc( ) )
    {
        echo '<br>Finally, the average age is ' . $rad['avgAge'] . '<br>';
    }
}
else
{
    echo "No results<br>";
}
?>
