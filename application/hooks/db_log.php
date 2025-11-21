<?php
/**
 * File: db_log.php
 * Author: abdiIwan.
 * Date: 1/5/2017
 * Time: 5:47 PM
 * woowtime
 */
class Db_log {

    function __construct() {
    }

    // Name of function same as mentioned in Hooks Config
    function logQueries() {

        $CI = & get_instance();

        $filepath = APPPATH . 'logs/Query-log-' . date('Y-m-d') . '.php'; // Creating Query Log file with today's date in application/logs folder
        /* if (file_exists($filepath) && (filesize($filepath)> (1024 * 4)))
         {
             $fp = fopen($filepath, "r+");
             if (flock($fp, LOCK_EX)) {  // acquire an exclusive lock
                 ftruncate($fp, 0);      // truncate file
                 fflush($fp);            // flush output before releasing the lock
                 flock($fp, LOCK_UN);    // release the lock
             }

         }*/
        $handle = fopen($filepath, "a+");                 // Opening file with pointer at the end of the file

        $times = $CI->db->query_times;                   // Get execution time of all the queries executed by controller
        foreach ($CI->db->queries as $key => $query) {
            $sql = $query . "\n Execution Time:" . $times[$key]; // Generating SQL file alongwith execution time
            fwrite($handle, $sql . "\n\n");              // Writing it in the log file
        }

        fclose($handle);      // Close the file
    }

}