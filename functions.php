<?php
defined('ABSPATH') or die('Nice Try!');

/**
 * Only for developer
 * @author Fazle Bari
 */
if (! function_exists('dd')) {
    function dd(...$vals)
    {
        if (! empty($vals) && is_array($vals)) {
            ob_start(); // Start output buffering
            foreach ($vals as $val) {
                echo "<pre>";
                var_dump($val);
                echo "</pre>";
            }
            $output = ob_get_clean(); // Get the buffered output and clear the buffer
            echo $output; // Output the buffered content
        }
    }
}

// Write all your custom codes here if you don't want to use OOP 
