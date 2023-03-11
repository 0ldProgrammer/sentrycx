<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class InverseSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inverse_seed { table_name : Table that needs create a seeder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a seeder file';

    /**
     * Table name
     *
     * @var string
     */
    protected $tableName;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * Note : We have to use raw file access instead of view
     *      since the file we are creating is a php file
     *
     * @return mixed
     */
    public function handle()
    {
        // $table_name = $this -> argument('table_name');

        $this -> prepareSeederFile( );


    }

    /**
     * TODO : trasnfer to handle()
     * Prepares the directory of the seeder file
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function prepareSeederFile(  ){
        $table_name = $this -> argument('table_name');

        $this -> info("Creating seeder for " . $table_name );

        $class_name = $this -> getClass( $table_name );

        // $class_file = app()->basePath() . "/database/seeds/{$class_name}Seeder.php";
        $class_file = $this -> getClassPath($table_name);

        $json_file  = $this -> extractData( $table_name );

        // TODO : Transfer to inject
        $content = $this -> getStub();
        $content = str_replace("{{CLASS_NAME}}", $class_name, $content );
        $content = str_replace('{{TABLE_NAME}}', $table_name, $content );
        $content = str_replace('{{JSON_FILE}}', $json_file, $content );

        file_put_contents($class_file, $content);
        $this -> info("CLASS : ". $class_file);

        return $class_file;
    }

    /**
     *
     * Extract all the database data
     * and store it into json file
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function extractData($table_name){
        $json_file = resource_path() . "/seeds/{$table_name}.json";

        $data =  DB::table($table_name)->get();

        file_put_contents( $json_file, json_encode( $data ) );

        return "/seeds/{$table_name}.json";
    }

    /**
     *
     * Generate the class name based on the table name
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getClass($table_name){
        return Str::ucfirst( Str::camel($table_name ) );
    }

    /**
     *
     * Generate the path of the class
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getClassPath( $table_name  ){
        $class_name = $this -> getClass( $table_name );
        return app()->basePath() . "/database/seeds/{$class_name}Seeder.php";
    }

    /**
     *
     * Get the stub file
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getStub(){
        return file_get_contents( resource_path() . "/views/stubs/inverse_seed.stub");
    }

    /**
     *
     * Replace all
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function injectVariables( $table_name, $content ){
        $class_name = $this -> getClass( $table_name );

        $class_file = app()->basePath() . "/database/seeds/{$class_name}Seeder.php";

        $class_file = str_replace('{{CLASS_NAME}}', $class_name, $class_file );

        file_put_contents($class_file, $content);

        return $class_file;
    }
}
