<?php 

function logged()
{
    return isset($_COOKIE['logged']);
}


function correctTimeFormat($data)
{

    // Checking whether or not the time format, format should be: HH:MM:SS
        if (sizeof(explode(':', $data)) < 3) // If string does not match any ss:ss:ss
        {
            return $data . ":00";
        } else {
            return $data;
        }

}

function correctDateTimeFormat($date, $time)
{
    // Parse the date to MySQL format switching dd/mm/yyyy to yyyy/mm/dd
        $tmp = explode('/', $date);
        return "$tmp[2]-$tmp[1]-$tmp[0] $time";
}

function redirect($dir)
{
    header("location: $dir");
}

?>