<?php
/*
 * @User: EricGU178
 * @LastEditors: EricGU178
 * @Date: 2019-03-30 16:57:54
 * @LastEditTime: 2019-07-22 14:36:54
 */
namespace tool;


class Base
{
    static public function instance()
    {
        return new static;
    }
}