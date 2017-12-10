<?php

namespace Larabase54\Repository\SQL;

use Closure;
use Exception;
use Illuminate\Database\Connection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use PDO;
use DB;
use Larabase54\Logger;

class AutoCloseConnection
{
    const TAG = "[AutoCloseConnection]";
    
    public static function callStoredProcedure($query, $bindings = [], Connection $connection = null)
    {
        if ($connection == null) {
            $connection = DB::connection();
        }
        return static::run($connection, $query, $bindings, function (Connection $connection, $query, $bindings) {
            // Set this attribute to avoid packets out of order error from MySQL. This should
            // be a bug of MySQL.
            $connection->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            
            $statement = $connection->getPdo()->prepare($query);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            
            try {
                $statement->execute($connection->prepareBindings($bindings));

//                $fetchArgument = $connection->getFetchArgument();
//
//                return isset($fetchArgument) ?
//                    $statement->fetchAll($connection->getFetchMode(), $fetchArgument, $connection->getFetchConstructorArgument()) :
//                    $statement->fetchAll($connection->getFetchMode());
                return $statement->fetchAll();
                
            } finally {
                // If we are dealing with MySQL Stored Procedure, we should close the statement. Otherwise, the
                // statement will keep opened in MySQL and result in
                // Can't create more than max_prepared_stmt_count statements
                $statement->closeCursor();
            }
        });
    }
    
    private static function run(Connection $connection, $query, $bindings, Closure $callback)
    {
        static::reconnectIfMissingConnection($connection);
        
        $start = microtime(true);
        
        // Here we will run this query. If an exception occurs we'll determine if it was
        // caused by a connection that has been lost. If that is the cause, we'll try
        // to re-establish connection and re-run the query with a fresh connection.
        try {
            $result = static::runQueryCallback($connection, $query, $bindings, $callback);
        } catch (QueryException $e) {
            $result = static::tryAgainIfCausedByLostConnection(
                $e, $connection, $query, $bindings, $callback
            );
        }
        
        // Once we have run the query we will calculate the time that it took to run and
        // then log the query, bindings, and execution time so we will report them on
        // the event that the developer needs them. We'll log time in milliseconds.
        $time = static::getElapsedTime($start);
        
        static::logQuery($query, $bindings, $time);
        
        return $result;
    }
    
    private static function runQueryCallback(Connection $connection, $query, $bindings, Closure $callback)
    {
        // To execute the statement, we'll simply call the callback, which will actually
        // run the SQL against the PDO connection. Then we can calculate the time it
        // took to execute and log the query SQL, bindings and time in our memory.
        try {
            $result = $callback($connection, $query, $bindings);
        }
            
            // If an exception occurs when attempting to run a query, we'll format the error
            // message to include the bindings with SQL, which will make this exception a
            // lot more helpful to the developer instead of just the database's errors.
        catch (Exception $e) {
            throw new QueryException(
                $query, $connection->prepareBindings($bindings), $e
            );
        }
        
        return $result;
    }
    
    private static function reconnectIfMissingConnection(Connection $connection)
    {
        if (is_null($connection->getPdo())) {
            $connection->reconnect();
        }
    }
    
    private static function tryAgainIfCausedByLostConnection(QueryException $e, Connection $connection, $query, $bindings, Closure $callback)
    {
        if (static::causedByLostConnection($e->getPrevious())) {
            $connection->reconnect();
            
            return static::runQueryCallback($connection, $query, $bindings, $callback);
        }
        
        throw $e;
    }
    
    private static function causedByLostConnection(Exception $e)
    {
        $message = $e->getMessage();
        
        return Str::contains($message, [
            'server has gone away',
            'no connection to the server',
            'Lost connection',
            'is dead or not enabled',
            'Error while sending',
            'decryption failed or bad record mac',
            'SSL connection has been closed unexpectedly',
            'Deadlock found when trying to get lock',
        ]);
    }
    
    private static function getElapsedTime($start)
    {
        return round((microtime(true) - $start) * 1000, 2);
    }
    
    private static function logQuery($query, $bindings, $time = null)
    {
        $implodedBindings = implode(',', $bindings);
        Logger::debug(self::TAG, "Query => $query, Bindings => $implodedBindings, Time => $time");
    }
}