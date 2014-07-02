<?php
Namespace Database
{
    Class PDOConfig Extends \PDO
    {
        private $engine     = null,
                $host       = null,
                $database   = null,
                $user       = null,
                $pass       = null;

        public function __construct ()
        {
            $c = require_once APP_PATH . '/src/config/config.php';

            $this->engine   = $c->engine;
            $this->host     = $c->host;
            $this->database = $c->database;
            $this->user     = $c->user;
            $this->pass     = $c->pass;

            $dns = $this->engine.':dbname='.$this->database.";host=".$this->host;

                parent::__construct($dns, $this->user, $this->pass);

            return $this; // don't think this returns what I'm thinking
        }
    }

    Class DataMapper Extends PDOConfig
    {
        public static $db = null;

        public static function init()
        {
            if (null === self::$db)
            {
                self::$db = New PDOConfig(); // prob not right
            }
        }
    }

    Class QueryMapper extends DataMapper
    {
        public static function get($data)
        {
            $st = self::$db->prepare("SELECT * FROM search WHERE term = :term");

                $st->execute([
                    ':term'   => $data->term,
                ]);

            return $st->fetchAll(); // no trap, i know...
        }
    }
}