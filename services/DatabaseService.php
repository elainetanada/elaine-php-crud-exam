<?php

include_once('../config/database.php');
/*
 * Database Service class.
 * 
 * A class that will connect to a Database.
 * 
 */

class DatabaseService
{
	/** @var DatabaseService $instance */
    private static $instance = null;
	/** @var \PDO $connection */
    private $connection;

    /* 
	 * Private constructor to prevent direct object creation.
	 * 
	 * Database setup.
	 */
    private function __construct()
    {
		### Add try and catch to display the actual error.
        try {
			$dbHost = DB_HOST;
			$dbName = DB_NAME;
            $this->connection = new \PDO(
                "mysql:host={$dbHost};dbname={$dbName}",
                DB_USERNAME,
                DB_PASSWORD
            );
			
        } catch (\PDOException $e) {
            die("Database connection error: " . $e->getMessage()); // Stop if database is not connected
        }
    }

	/*
	 * Get the singleton instance of the database connection
	 * 
	 * @return DatabaseService
	 */
    public static function getInstance()
    {
        if (self::$instance === null) {
			### $c = __CLASS__; - No need to use this
            self::$instance = new self();
        }

        return self::$instance;
    }

    /*
	 * Get the database connection
	 * 
	 * @return \PDO
	 */
    public function getConnection()
    {
        return $this->connection;
    }

	/*
	 * Run Query for Fetching All Data.
	 * 
	 * @param string $sql
	 * @return \PDO
	 */
    ### public function select(string $sql) - rename to what to the method does.
	public function fetchAllQuery(string $sql) ### add datatype
	{
		### $sth = $this->connection->query($sql); - We can make this 1 line instead of assigning to a variable and return
		return $this->connection->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
	}

	/*
	 * Run Query for Fetching a Data.
	 * 
	 * @param integer $id
	 * @param string $sql
	 * @return mixed
	 */
	public function fetchQuery(string $sql)
	{
		return $this->connection->query($sql)->fetch(\PDO::FETCH_ASSOC);;
	}

	/*
	 * Execute Query
	 * 
	 * @param string $sql
	 * @return \PDO 
	 */
	### public function exec($sql) - rename method to be more understandable
	public function executeQuery(string $sql)
	{
		return $this->connection->exec($sql);
	}

	/*
	 * Get the Lasat Data Inserted
	 * 
	 * @param string $sql
	 * @return integer|null
	 */
	### public function lastInsertId() - rename method closer to what it does
	public function getLastInsertId()
	{
		return $this->connection->lastInsertId();
	}
}