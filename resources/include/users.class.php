<?php

function _all($a) {
    // Are all, some, or none of the elements in this array true?
    // Returns 0 (none), 1 (some), or 2 (all)

    $cnt = 0;
    foreach ($a as $i) {
        if ($i) $cnt++;
    }

    if ($cnt == 0) return 0;
    if ($cnt == count($a)) return 2;
    return 1;
}

function is_simple_perm($a1, $a2) {
    $b = array();

    foreach ($a1 as $i) {
        $b[] = $a2[$i];
    }

    return _all($b);
}

class User {
    public $name;
    public $host;
    public $permissions;
    public $simple_permissions;
  
    function all() {
        global $conn;
        $arrayOfUsers = array();
        $result = mysql_query("SELECT user, host FROM mysql.user", $conn);
        
        while ($row = mysql_fetch_row($result)) {
            $arrayOfUsers[] = new User($row[0], $row[1]);; // append 
        }
    
        sort($arrayOfUsers);
        return $arrayOfUsers;
    }
    
    function __construct($name, $host="") {
        global $conn;
        
        $this->name = mysql_real_escape_string($name);

        if ($host == "") {
            $result = mysql_query("SELECT host FROM mysql.user WHERE user = '" . $this->name . "'", $conn);

            if ($result) {
                $row = mysql_fetch_row($result);
                $this->host = $row[0];
            } else {
                $this->host = "%%";
            }
        } else {
            $this->host = mysql_real_escape_string($host);
        }

        $result = mysql_fetch_assoc(mysql_query("SELECT * FROM mysql.user"
            . " WHERE user = '" . $this->name . "' AND host = '"
            . $this->host . "'", $conn));

        $this->permissions = array(
            // Read
            "SELECT" => $result["Select_priv"] == 'Y' ? true : false,

            // Write
            "INSERT" => $result["Insert_priv"] == 'Y' ? true : false,
            "UPDATE" => $result["Update_priv"] == 'Y' ? true : false,
            "DELETE" => $result["Delete_priv"] == 'Y' ? true : false,
            "FILE" => $result["File_priv"] == 'Y' ? true : false,

            // Manage Tables
            "CREATE" => $result["Create_priv"] == 'Y' ? true : false,
            "ALTER" => $result["Alter_priv"] == 'Y' ? true : false,
            "INDEX" => $result["Index_priv"] == 'Y' ? true : false,
            "DROP" => $result["Drop_priv"] == 'Y' ? true : false,
            "CREATE TEMPORARY TABLE" => $result["Create_tmp_table_priv"] == 'Y' ? true : false,
            "CREATE VIEW" => $result["Create_view_priv"] == 'Y' ? true : false,
            "SHOW VIEW" => $result["Show_view_priv"] == 'Y' ? true : false,
            "CREATE ROUTINE" => $result["Create_routine_priv"] == 'Y' ? true : false,
            "ALTER ROUTINE" => $result["Alter_routine_priv"] == 'Y' ? true : false,
            "EXECUTE" => $result["Execute_priv"] == 'Y' ? true : false,

            // Manage Users
            "GRANT" => $result["Grant_priv"] == 'Y' ? true : false,
            "SUPER" => $result["Super_priv"] == 'Y' ? true : false,
            "CREATE USER" => $result["Create_user_priv"] == 'Y' ? true : false,

            // Administer Database
            "RELOAD" => $result["Reload_priv"] == 'Y' ? true : false,
            "SHUTDOWN" => $result["Shutdown_priv"] == 'Y' ? true : false,
            "PROCESS" => $result["Process_priv"] == 'Y' ? true : false,
            "REFERENCES" => $result["References_priv"] == 'Y' ? true : false,
            "SHOW DATABASES" => $result["Show_db_priv"] == 'Y' ? true : false,
            "LOCK TABLES" => $result["Lock_tables_priv"] == 'Y' ? true : false,
            "REPLICATION CLIENT" => $result["Repl_client_priv"] == 'Y' ? true : false,
            "REPLICATION SLAVE" => $result["Repl_slave_priv"] == 'Y' ? true : false,
            );

        $permRead = $this->permissions["SELECT"];
        $permWrite = is_simple_perm(array("INSERT", "UPDATE", "DELETE", "FILE"), $this->permissions);
        $permTables = is_simple_perm(array("CREATE", "INDEX", "ALTER",
            "DROP", "CREATE TEMPORARY TABLE", "CREATE VIEW", "SHOW VIEW",
            "CREATE ROUTINE", "ALTER ROUTINE", "EXECUTE"), $this->permissions);
        $permUsers = is_simple_perm(array("GRANT", "SUPER", "CREATE USER"),
            $this->permissions);
        $permAdmin = is_simple_perm(array("RELOAD", "SHUTDOWN", "PROCESS",
            "REFERENCES", "SHOW DATABASES", "LOCK TABLES",
                                          "REPLICATION CLIENT", "REPLICATION SLAVE"), $this->permissions);

        $this->simple_permissions = array(
            "Read" => $permRead,
            "Write" => $permWrite,
            "Tables" => $permTables,
            "Users" => $permUsers,
            "Admin" => $permAdmin
            );
    }

    function getPermString() {
        $r = array();
        foreach ($this->simple_permissions as $k => $v) {
            if ($k == "Tables") $k = "Manage Tables";
            else if ($k == "Users") $k = "Manage Users";
            else if ($k == "Admin") $k = "Administer Database";
            
            if ($v == 2) {
                $r[] = $k;
            }
        }

        return implode(", ", $r);
    }
}

?>