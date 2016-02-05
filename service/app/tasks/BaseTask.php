<?php

/**
 * Class BaseTask
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */
abstract class BaseTask extends \Phalcon\CLI\Task
{
    /**
     * Gets an exclusive lock to prevent multiple processes from doing the same work
     * @param string $lockfile
     * @return bool | mixed
     */
    protected function getLock($lockfile)
    {
        $lock = fopen($lockfile, 'w+');

        if (flock($lock, LOCK_EX | LOCK_NB)) {
            return $lock;
        }

        fclose($lock);
        return false;
    }

    /**
     * Releases exclusive lock
     * @param $lock
     */
    protected function releaseLock($lock)
    {
        if (flock($lock, LOCK_EX | LOCK_NB)) {
            flock($lock, LOCK_UN);
        }

        fclose($lock);
    }
}