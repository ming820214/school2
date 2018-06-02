<?php
/**
 * redis连接
 * @access private
 * @return resource
 * @author bieanju
 */
 private function connectRedis(){
    $redis=new \Redis();
    $redis->connect(C("REDIS_HOST"),C("REDIS_PORT"));
    return $redis;
} 