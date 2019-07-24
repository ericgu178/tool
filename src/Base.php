<?php
/*
 * @User: EricGU178
 * @LastEditors: EricGU178
 * @Date: 2019-03-30 16:57:54
 * @LastEditTime: 2019-07-24 10:54:11
 */
namespace tool;


class Base
{
    static protected $instance = null;

    static public function instance()
    {
        if (static::$instance === null) {
            static::$instance = new static;
        }
        
        return static::$instance;
    }
}