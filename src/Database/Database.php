<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Emblaze\Database;

use PDO;
use Exception;
use PDOException;
use Emblaze\Url\Url;
use Emblaze\Http\Request;
use Emblaze\Bootstrap\App;

class Database
{
    /**
     * Database instance
     * 
     */
    protected static $instance;

    /**
     * Database connection
     */
    protected static $connection;

    /**
     * Query
     * 
     * @var string
     */
    protected static $query;
     /**
     * Setter
     * 
     * @var string
     */
    protected static $setter;

    /**
     * Table name
     * 
     * @var string
     */
    protected static $table;

    /**
     * Primary ID name
     * 
     * @var string
     */
    protected static $primary_id;
    /**
     * Select data
     * 
     * @var array
     */
    protected static $select;
    /**
     * Join data
     * 
     * @var string
     */
    protected static $join;
     /**
     * where data
     * 
     * @var string
     */
    protected static $where;
     /**
     * Where binding
     * 
     * @var array
     */
    protected static $where_binding = [];
     /**
     * Group by
     * 
     * @var string
     */
    protected static $group_by;
     /**
     * Having
     * 
     * @var string
     */
    protected static $having;
    /**
     * Having binding
     * 
     * @var array
     */
    protected static $having_binding = [];
     /**
     * Order by data
     * 
     * @var string
     */
    protected static $order_by;
     /**
     * Limit 
     * 
     * @var string
     */
    protected static $limit;
     /**
     * Offset
     * 
     * @var string
     */
    protected static $offset;
     /**
     * All biding
     * 
     * @var array
     */
    protected static $binding;
    

     /**
      * Database constructor
      *
      * @param string $table
      * @param string $primary_id
      */
    private function __construct(
        $table = '',
        $primary_id = '') {

        static::$table = $table;
        static::$primary_id = $primary_id;
    }

    /**
     * Connect to database
     */
    private static function connect()
    {
        if(!static::$connection) {
            
            // Get database config file
            // $database_config = File::require_file('config/database.php');
            
            // extract() will convert the associative array names to be variable names with there value.
            // e.g. 'host'=>'127.0.0.1' will be $host = '127.0.0.1'
            
            // for App run here
            // need to fix this. this config should be also work on webscoket data manipulation.
            // extract(App::$app->config['database']);

            // for websocket, direct details.
            $driver = 'mysql';
            $host = '127.0.0.1';
            $port = '3306';
            $database = 'emblaze';
            $username = 'root';
            $password = '';
            $charset = 'utf8';
            $collation = 'utf8_general_ci';

            // dsn setting
            $dsn = $driver.":host=".$host.";port=".$port.";dbname=".$database;

            // options setting
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_PERSISTENT => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "set NAMES ".$charset." COLLATE ".$collation
            ];
            
            try {
                // This will create a new instance of PDO Object
                static::$connection = new PDO($dsn, $username, $password, $options);
                
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
            
        }
    }

    

     /**
     * Get connecition.
     * 
     * @param string $table
     * @return mixed
     */
    public static function connection($table = null)
    {
        // This will create a new instance of PDO Object
        static::connect();
        if(!self::$instance) {
            self::$instance = new Database($table);
        }
        return static::$connection;
    }

    /**
     * Get the instance of the class
     */
    private static function instance()
    {
        // This will create a new instance of PDO Object
        static::connect();
        
        $table = static::$table;
        
        if(!self::$instance) {
            self::$instance = new Database($table);
        }

        return self::$instance;
    }

    /**
     * query function
     * 
     * @param string $query
     * @return string
     */
    public static function query($query = null)
    {
        // create a new instance of Database $connection
        static::instance();

        if($query == null) {
            
            if(!static::$table) {
                throw new Exception("Unknown table");
            }

            // SELECT * FROM user JOIN roles roles.id = user.role_id WHERE id > 1 HAVING id > 1 limit 1 offset 2
            $query = "SELECT ";
            $query .= static::$select ?: '*';
            $query .= " FROM ".static::$table." ";
            $query .= static::$join." ";
            $query .= static::$where." ";
            $query .= static::$group_by." ";
            $query .= static::$having." ";
            $query .= static::$order_by." ";
            $query .= static::$limit." ";
            $query .= static::$offset." ";
            
        }


        static::$query = $query;

        static::$binding = array_merge(static::$where_binding,static::$having_binding);

        return static::instance();
    }

    /**
     * Select data from table
     * 
     * @return object $instance
     */
    public static function select()
    {
        // get the select(args/params) funciton args/params
        $select = func_get_args();
        
        // implode the args/params
        $select = implode(', ', $select);

        static::$select = $select;

        return static::instance();
    }

    /**
     * Define table
     * 
     * @param string $table
     * 
     * @return object $instance
     */
    public static function table($table)
    {
        static::$table = $table;
        return static::instance();
    }


    // (INNER) JOIN: Returns records that have matching values in both tables
    // LEFT (OUTER) JOIN: Returns all records from the left table, and the matched records from the right table
    // RIGHT (OUTER) JOIN: Returns all records from the right table, and the matched records from the left table
    // FULL (OUTER) JOIN: Returns all records when there is a match in either left or right table

    /**
     * Join Table
     * 
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @param string $type
     * 
     * @return object $type
     */
    public static function join($table, $first, $operator, $second, $type = "INNER")
    {
        // (INNER) JOIN: Returns records that have matching values in both tables
        static::$join .= " ".$type." JOIN ".$table." ON ".$first.$operator.$second." ";
        return static::instance();
    }

    /**
     * RIGHT Join Table
     * 
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * 
     * @return object $type
     */
    public static function rightJoin($table, $first, $operator, $second)
    {
        // RIGHT (OUTER) JOIN: Returns all records from the right table, and the matched records from the left table
        
        // static::$join .= " ".$type." JOIN ".$table." ON ".$first.$operator.$second." ";
        static::join($table,$first,$operator,$second,"RIGHT");
        return static::instance();
    }

    /**
     * Left Join Table
     * 
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * 
     * @return object $type
     */
    public static function leftJoin($table, $first, $operator, $second)
    {
        // LEFT (OUTER) JOIN: Returns all records from the left table, and the matched records from the right table
        
        // static::$join .= " ".$type." JOIN ".$table." ON ".$first.$operator.$second." ";
        static::join($table,$first,$operator,$second,"LEFT");
        return static::instance();
    }
    
    /**
     * Full Join Table
     * 
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * 
     * @return object $type
     */
    public static function fullJoin($table, $first, $operator, $second)
    {
        // FULL (OUTER) JOIN: Returns all records when there is a match in either left or right table
        
        // static::$join .= " ".$type." JOIN ".$table." ON ".$first.$operator.$second." ";
        static::join($table,$first,$operator,$second,"FULL");
        return static::instance();
    }

    /**
     * Where data
     * 
     * @param string $column
     * @param string $operator
     * @param string $value
     * @param string $type
     * 
     * @return object $instance
     */
    public static function where($column,$operator,$value,$type=null)
    {
        $where = '`'.$column.'` '.$operator.' ? ';
        if(! static::$where) {
            $statement = " WHERE ".$where;
        } else {
            if($type == null) {
                $statement = " AND ".$where;
            } else {
                $statement = " ".$type." ".$where;
            }
        }
        static::$where .= $statement;
        static::$where_binding[] = htmlspecialchars($value);

        return static::instance();
    }

    /**
     * Or where
     * 
     * @param string $column
     * @param string $operator
     * @param string $value
     * 
     * @return object $value
     */
    public static function orWhere($column,$operator,$value)
    {
        static::where($column,$operator,$value,"OR");

        return static::instance();
        
    }

    /**
     * Group by
     * 
     * @return object $instance
     */
    public static function groupBy() {
        $group_by = func_get_args();
        $group_by = "GROUP BY ".implode(', ', $group_by) . " ";

        static::$group_by = $group_by;
        
        return static::instance();
    }

    /**
     * Having data
     * 
     * @param string $column
     * @param string $operator
     * @param string $value
     * @param string $type
     * 
     * @return object $instance
     */
    public static function having($column,$operator,$value)
    {
        $having = '`'.$column.'`'.$operator.' ? ';
        if(! static::$having) {
            $statement = " HAVING ".$having;
        } else {
            $statement = " AND ".$having;
        }
        static::$having .= $statement;
        static::$having_binding[] = htmlspecialchars($value);

        return static::instance();
    }

    /**
     * Order by
     * 
     * @param string $column
     * @param string $type
     * 
     * @return object $instance
     */
    public static function orderBy($column, $type = null)
    {
        $sep = static::$order_by ? " , " : " ORDER BY ";
        $type = strtoupper($type);
        $type = ($type != null && in_array($type, ['ASC','DESC'])) ? $type : 'ASC';
        $statement = $sep.$column." ".$type." ";

        static::$order_by .= $statement;

        return static::instance();
    }

    /**
     * Limit
     * 
     * @param string $limit
     * 
     * @return object $instance
     */
    public static function limit($limit)
    {
        static::$limit = "LIMIT ".$limit." ";
        
        return static::instance();
    }

    /**
     * Offset
     * 
     * @param string $offset
     * 
     * @return object $instance
     */
    public static function offset($offset)
    {
        static::$offset = "OFFSET ".$offset." ";
        
        return static::instance();
    }

    /**
     * Fetch execute
     * 
     * @return object $data
     */
    private static function fetchExecute($fetch_type = 'fetchAll')
    {
        try {
            static::query(static::$query);

            $query = trim(static::$query);
    
            $statement = static::prepare($query);
          
            // bind the data params using execute.
            $statement->execute(static::$binding);
            
            // fetchAll
            // You can customize this e.g. fetchObject(class_with_user_properties)
            // Or e.g. fetchColumn()
            $data = $statement->{$fetch_type}();
            
            // clear/reset properties
            static::clear();
            
            return $data;
        } catch (\Throwable $th) {
            var_dump($th->getMessage());
            return false;
        }

        
    }

    /**
     * Prepare SQL Query
     * 
     * @param string $sqlQuery
     * 
     * @return mixed
     */
    private static function prepare($sqlQuery)
    {
        return static::$connection->prepare($sqlQuery);
    }

    /**
     * Get records
     * 
     * @return object $result
     */
    public static function get()
    {
        return static::fetchExecute('fetchAll');
    }

    /**
     * Get record
     * 
     * @return object $result
     */
    public static function first()
    {
        return static::fetchExecute('fetch');
    }

    /**
     * Custom Execute SQL Query
     * 
     * @param array $data
     * @param string $query
     * @param bool $where
     */
    private static function execute(array $data, $query, $where = null)
    {
        try {
            static::instance();
            if(! static::$table) {
                throw new Exception("Unknown table");
            }

            foreach($data as $key => $value) {
                static::$setter .= '`'.$key.'` = ?, ';
                static::$binding[] = filter_var($value, FILTER_SANITIZE_STRING);
            }
            static::$setter = trim(static::$setter, ', ');

            $query .= static::$setter;
            $query .= $where != null ? static::$where." " : '';

            static::$binding = $where != null ? array_merge(static::$binding, static::$where_binding) : static::$binding;
            
            $statement = static::prepare($query);
            
            $statement->execute(static::$binding);

            $statement->closeCursor();
            
            static::clear();
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }

    /**
     * Insert to table
     * 
     * @param array $data
     * 
     * @return object
     */
    public static function insert($data)
    {
        try {
            $table = static::$table;
            $query = "INSERT INTO ".$table. " SET ";
            static::execute($data, $query);

            // get last inserted Id
            $object_id = static::$connection->lastInsertId();

            // find that last inserted data using the object_id
            $object = static::table($table)->where(static::$primary_id, '=', $object_id)->first();

            // then return that newly inserted data.
            return $object;
        } catch (\Throwable $th) {
            var_dump($th->getMessage());
            return false;
        }

        
    }

    /**
     * Update record on table
     * 
     * @param array $data
     * 
     * @return bool
     */
    public static function update($data)
    {
        $query = "UPDATE ". static::$table." SET ";
        static::execute($data, $query, true);

        return true;
    }

    /**
     * Delete record on table
     * 
     * @param array $data
     * 
     * @return bool
     */
    public static function delete()
    {
        $query = "DELETE FROM ". static::$table." ";
        static::execute([], $query, true);

        return true;
    }

    /**
     * Pagination
     * 
     * @return mixed result
     */
    public static function paginate($items_per_page = 15)
    {
        static::query(static::$query);
        $query = trim(static::$query, ' ');

        $data = static::prepare($query);

        $data->execute();
        
        // ceil — Round fractions up
        // echo ceil(4.3);    // 5
        // echo ceil(9.999);  // 10
        // echo ceil(-3.14);  // -3
        $pages = ceil($data->rowCount() / $items_per_page);

        $page = Request::get('page');
        $current_page = (! is_numeric($page) || Request::get('page') < 1) ? "1" : $page;

        $offset = ($current_page - 1) * $items_per_page;
        
        static::limit($items_per_page);
        static::offset($offset);
        static::query();

        $result = static::fetchExecute();

        $response = [
            'data' => $result,
            'items_per_page' => $items_per_page,
            'pages' => $pages,
            'current_page' => $current_page
        ];
        
        return $response;
    }

    /**
     * Get pagination links
     * 
     * @param int $current_page
     * @param int $pages
     * 
     * @return string $result
     */
    public static function links($current_page,$pages)
    {
        $links = '';
        $from = $current_page - 2;
        $to = $current_page + 2;
        if($from < 2) {
            $from = 2;
            $to = $from + 4;
        }

        if($to >= $pages) {
            $diff = $to - $pages + 1;
            $from = ($from > 2) ? $from - $diff : 2;
            $to = $pages - 1;
        }

        if($from < 2) {
            $from = 1;
        }

        if($to >= $pages) {
            $to = ($pages - 1);
        }

        if($pages > 1) {
            $links .= "<ul class='pagination'>";
            $full_link = Url::path(Request::full_url());
            $full_link = preg_replace('/\?page=(.*)/','', $full_link);
            $full_link = preg_replace('/\?&page=(.*)/','', $full_link);

            $current_page_active = $current_page == 1 ? 'active' : '';
            $href = strpos($full_link, '?') ? ($full_link.'&page=1') : ($full_link.'?page=1');
            $links .= "<li class='link' $current_page_active><a href='$href'>First</a></li>";

            for($i = $from; $i<= $to; $i++) {
                $current_page_active = $current_page == $i ? 'active' : '';
                $href = strpos($full_link, '?') ? ($full_link.'&page='.$i) : ($full_link.'?page='.$i);
                $links .= "<li class='link' $current_page_active><a href='$href'>$i</a></li>";
            }

            if($pages > 1) {
                $current_page_active = $current_page == $pages ? 'active' : '';
                $href = strpos($full_link, '?') ? ($full_link.'&page='.$pages) : ($full_link.'?page='.$pages);

                $links .= "<li class='link' $current_page_active><a href='$href'>Last</a></li>";
            }

            return $links;
            
        }
    }

    /**
     * Clear the properties
     * 
     * @return void
     */
    private static function clear()
    {
        static::$select = '';
        static::$join = '';
        static::$where = '';
        static::$where_binding = [];
        static::$group_by = '';
        static::$having = '';
        static::$having_binding = [];
        static::$order_by = '';
        static::$limit = '';
        static::$offset = '';
        static::$query = '';
        static::$binding = [];
        static::$instance = '';
        static::$setter = '';
        // static::$table = '';
        // static::$primary_id = '';
    }


    /**
     * Get query
     */
    public static function getQuery()
    {
        static::query(static::$query);
        
        return static::$query;
    }
}