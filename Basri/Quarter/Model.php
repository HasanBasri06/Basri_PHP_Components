<?php 

namespace Basri\Quarter;

class Model {
    private $where = [];
    private $ordered = "";
    private $limit = "";
    private $take = "";
    private $select = null;
    private $tableName;
    private $pdo;
    public function __construct() {
        try {
            $pdo = new \PDO("mysql:host=".env('DB_HOST').";dbname=".env('DB_NAME'), env('DB_USERNAME'), env('DB_PASSWORD'));
            $this->pdo = $pdo;
        } catch (\PDOException $e) {
            die($e->getMessage());
        }

        $table = new \ReflectionClass(get_called_class());
        $tableName = strtolower($table->getShortName()."s");
        
        if ($table->hasProperty('table')) {
            $tableName = $table->getProperty('table')->getValue(new $table->name);
        }

        $this->tableName = $tableName;
    }
    public function where(string $column, string $compare, string|bool|int $value) {
        $whereSQL = " $column $compare $value ";
        array_push($this->where, $whereSQL);

        return $this;
    }
    public function order(string $column, string $ordered = 'ASC') {
        $orderedSQL = " ORDER BY $column $ordered";
        $this->ordered .= $orderedSQL;

        return $this;
    }
    public function limit(string|int $limit) {
        $limitSQL = " LIMIT $limit";
        $this->limit .= $limitSQL;
        
        return $this;
    }
    public function take(string|int $start, string|int $limit) {
        $limitSQL = " OFFSET $start $limit";
        $this->take .= $limitSQL;
        
        return $this;
    }
    public function select(string ...$column) {
        $this->select .= implode(', ', $column);
        
        return $this;
    }
    public function get() {

        $columnName = "*";

        if (!is_null($this->select)) {
            $columnName = $this->select;
        }
        
        $sql = "SELECT $columnName FROM $this->tableName";
        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }
        if (!empty($this->ordered)) {
            $sql .= $this->ordered;
        }
        if (!empty($this->limit)) {
            $sql .= $this->limit;
        }
        if (!empty($this->take)) {
            $sql .= $this->take;
        }
        $sql = trim($sql);

        $qry = $this->pdo->prepare($sql);
        $qry->execute();

        return $qry->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function save(array $columns) {
        $inserts = [];
        foreach ($columns as $key => $value) {
            $inserts[] = ["name" => $key, 'value' => $value, 'hashName' => ':'.$key];
        }

        $names = "";
        $values = "";
        foreach ($inserts as $value) {
            $names .= $value['name'] . ",";
            $values .= $value['hashName'] . ",";
        }

        $names = rtrim($names, ',');
        $values = rtrim($values, ',');
        $sql = "INSERT INTO $this->tableName (".$names.",created_at) VALUES (".$values.",:created_at)";
        $qry = $this->pdo->prepare($sql);

        foreach ($inserts as $value) {
            $qry->bindValue($value['hashName'], $value['value']);
        }
        $qry->bindValue(':created_at', date('Y-m-d H:i:s'));

        $qry->execute();
    }
}   