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

class Migration
{

    /**
     * Apply Migration
     *
     * @return void
     */
    public static function applyMigration()
    {

        $pdo = Database::connection();
        
        // 1.) If the migrations table are not exists create it first.
        $sql = "CREATE TABLE IF NOT EXISTS `". config('database.database'). "`.`migrations` (
            `id` INT NOT NULL AUTO_INCREMENT ,  
            `migration` VARCHAR(255) NOT NULL ,  
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,    
            PRIMARY KEY  (`id`)) ENGINE = InnoDB;";

        // Execute the sql query
        $pdo->exec($sql);


        // 2.) Get the already applied migrations
        $statement = $pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();
        
        $appliedMigrations = $statement->fetchAll(\PDO::FETCH_COLUMN);

        // 3.) Scan the database/migrations files AND trigger the new migrations
        // scan the files from migrations folder,
        // this should return an array of file names
        $files = scandir(ROOT.'/database/migrations');

        // array_diff: Compares array against one or more other arrays and returns the values in array that are not present in any of the other arrays.
        $toApplyMigrations = array_diff($files, $appliedMigrations);

     
        $newMigrations = [];

        foreach ($toApplyMigrations as $migration) {
            if($migration === "." || $migration === "..") {
                // continue the foreach loop
                continue;
            }

            // Require the file migrations. (e.g. m0001_initial.php)
            require_once ROOT.'/database/migrations/'.$migration;

            // Get the className of migration file
            // pathinfo: in here we will only get the FILENAME and not the extention .php,
            //  e.g. "m0001_initial.php" will now be "m0001_initial", so the class name should be m0001_initial
            $className = pathinfo($migration, PATHINFO_FILENAME);

            // Now we can create a new instance of migrations files from migrations folder, and we can call the up() or down() method
            $newInstance_of_migration_class = new $className;

            // $this->_log("Applying migration $migration");
            echo "Applying migration $migration<br/>";

            // Trigger/Call the up() method
            $newInstance_of_migration_class->up();

            // $this->_log("Applied migration $migration");
            echo "Applied migration $migration<br/>";

            // Save/add to array the new $migration
            // Save the new migration in $newMigrations for later use.
            $newMigrations[] = $migration;
            
        }


        // 4.) Save the migrations to database.
        // if the $newMigrations is not empty let save the migrations to database.
        if(!empty($newMigrations)) {
            // BASIC INSERT.
            // foreach ($migrations as $migration) {
            //     $sql = "INSERT INTO `migrations` (`migration`) 
            //     VALUES ('$migration');";
            //     $pdo->exec($sql);
            // }

            // OR you can save using this:
        
            // Using array_map we can update the value of migratioins array and add something like:
            // e.g. m0001_initial.php then it should now be ('m0001_initial.php')
            $newMigrations = array_map(fn($m) => "('$m')",$newMigrations);

            // we can use implodde to array
            $str = implode(',', $newMigrations); // output: e.g. "('m0001_initial.php'),('m0002_something.php')"
            
            // lets insert the value,
            // this will insert all of the $str to `migration` 
            $statement = $pdo->prepare("INSERT INTO `migrations` (`migration`) VALUES $str;");

            // $newRecord = Database::table('users')->insert($data);


            $statement->execute();
            
        } else {
            // $this->_log("All migrations are applied.");
            echo "All migrations are applied.<br/>";
        }

        
        
    }

    /**
     * Apply Rollback on last migrated data.
     *
     * @return void
     */
    public static function applyRollback()
    {
        $pdo = Database::connection();
        
        // LOGIC:
       // First we need to check from migration table if there is a migrated data
       
       // Get the last migration data:
       $statement = $pdo->prepare("SELECT * FROM `migrations` ORDER BY id DESC LIMIT 1");
       $statement->execute();

       $lastMigrations = $statement->fetchAll(\PDO::FETCH_ASSOC);
       
       // Remove the row from that table.
       $statement = $pdo->prepare("DELETE FROM `migrations` WHERE `migrations`.`id` = " . $lastMigrations[0]['id']);
       $statement->execute();

       // Require the file migrations first before creating a new instance of this migration.
       require_once ROOT.'/database/migrations/'.$lastMigrations[0]['migration'];

       // then use the name of that migration
       // notes: this will remove the .php from the migration name.
       $className = pathinfo($lastMigrations[0]['migration'], PATHINFO_FILENAME);

       // and create a new instance of that class, from migrations folder
       $newInstance_of_LAST_migration_class = new $className;


       // $this->_log("Applying Rollback migration $className");
       echo "Applying rollback migration $className<br/>";
       // then trigger the down() method.
       $newInstance_of_LAST_migration_class->down();

       // $this->_log("Applied Rollback migration $className");
       echo "Applied rollback migration $className<br/>";
    }
}