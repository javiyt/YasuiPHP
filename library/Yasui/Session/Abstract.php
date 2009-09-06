<?php
abstract class Yasui_Session_Abstract
{
    public function open($save_path, $session_name)
    {
        return true;
    }

    abstract public function close();
    abstract public function read($session_id);
    abstract public function write($session_id,$session_data);
    abstract public function destroy($session_id);
    abstract public function gc($max_lifetime);
}

