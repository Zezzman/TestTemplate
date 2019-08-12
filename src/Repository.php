<?php
namespace App;

use App\Interfaces\IRepository;
use App\Providers\DatabaseProvider;
/**
 * 
 */
abstract class Repository implements IRepository
{
    protected $connection;

    public function __construct()
    {
        $this->connection = DatabaseProvider::connectMySQL();
    }
}