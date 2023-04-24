<?php

namespace spark\drivers\Plugin;

use \Slim\Slim;
use spark\models\Model;

/**
* A Tiny Driver to import SQL Databases
*/
class ImportSQL
{
    protected $filePath;

    /**
     * Create a SQL import instance
     *
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        if (!is_file($filePath)) {
            throw new \InvalidArgumentException("No SQL File found at {$filePath}");
        }

        $this->filePath = $filePath;
    }

    /**
     * Run the import
     *
     * @param  array  $replacements
     * @return boolean
     */
    public function run(array $replacements = [])
    {
        $replacements['__db_prefix'] = Model::getPrefix();
        $db = Slim::getInstance()->db;
        $tempLine = null;
        $sql = file_get_contents($this->filePath);
        // Make custom replacements
        $sql = strtpl($sql, $replacements);
        $sql = explode("\n", $sql);
        foreach ($sql as $line) {
            // Skip it if it's a comment
            $beginning = (string) mb_substr($line, 0, 2);
            if ($beginning === '--' || $beginning === '/*' || trim($line) === '') {
                continue;
            }
            // Add this line to the current segment
            $tempLine .= $line;
            $ending = (string) mb_substr(trim($line), -1, 1);
            // If it has a semicolon at the end, it's the end of the query
            if ($ending === ';') {
                // Perform the query
                $db->exec($tempLine);
                $tempLine = '';
            }
        }

        return true;
    }
}
