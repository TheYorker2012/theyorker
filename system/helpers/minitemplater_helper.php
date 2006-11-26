<?php

// SANITISE INPUT BEFORE USING THIS FUNCTION!!
function apinc ($filename,$replace='') {
    
    if ($fc = @file_get_contents ($filename)) {
        // File read worked, lets have a look at some find and replace, motherfuckers!

        // replace whatever with whatever else and stuff....
        foreach ($replace as $key => $val) {
            $fc = str_replace ($key,$val,$fc);
        }
        
        // Get rid of any %%vars%% that someone either forgot to or
        // chose not to bother to specify
        $fc = ereg_replace ("%%[A-Za-z0-9_.]+%%","",$fc);
        
        // Done :D
        return $fc;
        
    }
    else {
        return "Template not found (function: apinc in minitemplater_helper.php";
    }
}

?>