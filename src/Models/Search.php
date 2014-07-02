<?php

Namespace Models
{
    USE Database\QueryMapper    AS QueryMapper,
        Database\DataMapper     AS DataMapper;

    Class Search Extends QueryMapper
    {
        protected $rawData = null,
                  $objData = null;

        public function __construct()
        {
            DataMapper::init();

            $this->rawData = file_get_contents('php://input');
            $this->objData = json_decode($this->rawData);
        }

        public function query()
        {
            // dynamic demo, not hooked up
            // return $this->get($this->objData->data);

            // static demo stuff
            return (! in_array($this->objData->data, ['php', 'mean', 'angular', 'js']))
                ? "{$this->objData->data} was not found."
                : "{$this->objData->data} was found!";
        }
    }
}