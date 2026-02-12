<?php

namespace App\Constants;

class Redis
{
    public const CLIENT = 'predis';
    public const URL = '';
    public const HOST = '127.0.0.1';
    public const PORT = '6379';
    public const USER = '';
    public const PASSWORD = '';
    public const DB_INDEX = 0;
    public const CACHE_INDEX = 1;
    public const QUEUE_CONNECTION_NAME = 'default';
    public const CACHE_CONNECTION_NAME = 'cache';
}
