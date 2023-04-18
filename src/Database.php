<?php

namespace beratt087\Database;

class Database
{
    private \PDO $db;
    public static string $table;
    public array $where = [];
    protected string $sql = '';
    public static string $queryFormat;
    public array $data = [];

    public function __construct()
    {
        $dsn = sprintf("mysql:host=%s;dbname=%s;charset=%s", $_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_CHARSET']);
        $this->db = new \PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function table(string $name): self
    {
        self::$table = $name;
        return new self();
    }

    /**
     * @param string $column
     * @param string $value
     * @param string $operation
     * @return $this
     */

    public function where(string $column, string $value, string $operation = '='): static
    {
        $this->where[] = $column . ' ' . $operation . ' "' . $value . '"';
        return $this;
    }

    /**
     * @return void
     */

    protected function prepareSql(): void
    {
        $queryFormat = self::$queryFormat;
        switch ($queryFormat) {
            case 'select':
                $this->sql = sprintf('SELECT * FROM %s', self::$table);
                if (count($this->where)) {
                    $this->sql .= ' WHERE ' . implode(' && ', $this->where);
                }
                break;
            case 'update':
                $this->sql = sprintf('UPDATE %s SET', self::$table);
                if (count($this->data)) {
                    foreach ($this->data as $key => $value) {
                        if (array_key_last($this->data) == $key) {
                            $this->sql .= ' ' . $key . ' = :' . $key;
                        } else {
                            $this->sql .= ' ' . $key . ' = :' . $key . ', ';
                        }
                    }
                    if (count($this->where)) {
                        $this->sql .= ' WHERE ' . implode(' && ', $this->where);
                    }
                }
                break;
            case 'insert':
                $this->sql = sprintf("INSERT INTO %s (", self::$table);
                $valueString = "";
                foreach ($this->data as $key => $value) {
                    if (array_key_last($this->data) == $key) {
                        $valueString .= "?";
                        $this->sql .= $key . ')';
                    } else {
                        $this->sql .= $key . ', ';
                        $valueString .= "?, ";
                    }
                }
                $this->sql .= " VALUES(" . $valueString . ")";
                break;
            case 'selectCount':
                $this->sql = sprintf('SELECT COUNT(*) FROM %s', self::$table);
                if (count($this->where)) {
                    $this->sql .= ' WHERE ' . implode(' && ', $this->where);
                }
                break;

            case 'delete':
                $this->sql = sprintf('DELETE FROM %s', self::$table);
                if (count($this->where)) {
                    $this->sql .= ' WHERE ' . implode(' && ', $this->where);
                }
                break;
            default:
                break;
        }
    }

    /**
     * @return array|bool
     */
    public function get(): array|bool
    {
        self::$queryFormat = 'select';
        $this->prepareSql();
        $query = $this->db->prepare($this->sql);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * @return mixed
     */
    public function first(): mixed
    {
        self::$queryFormat = 'select';
        $this->prepareSql();
        $query = $this->db->prepare($this->sql);
        $query->execute();
        return $query->fetch(\PDO::FETCH_OBJ);
    }

    /**
     * @param array $values
     * @return int
     */
    public function update(array $values): int
    {
        self::$queryFormat = 'update';
        $this->data = $values;
        $this->prepareSql();
        $query = $this->db->prepare($this->sql);
        $query->execute($this->data);
        return $query->rowCount();
    }


    /**
     * @param array $values
     * @return bool
     */
    public function insert(array $values): int
    {
        self::$queryFormat = 'insert';
        $this->data = $values;
        $this->prepareSql();
        $query = $this->db->prepare($this->sql);
        $query->execute(array_values($this->data));
        return $query->rowCount();
    }

    public function getCount(): int
    {
        self::$queryFormat = 'selectCount';
        $this->prepareSql();
        $query = $this->db->prepare($this->sql);
        $query->execute();
        return $query->fetchColumn();
    }

    public function delete(): int
    {
        self::$queryFormat = 'delete';
        $this->prepareSql();
        $query = $this->db->prepare($this->sql);
        $query->execute();
        return $query->rowCount();
    }
}
