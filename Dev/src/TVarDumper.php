<?php
namespace Phalconeer\Dev;

use Phalcon\Config;
use Phalconeer\Data;

/**
 * TVarDumper class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2013 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id$
 * @package System.Util
 */
/**
 * TVarDumper class.
 *
 * TVarDumper is intended to replace the buggy PHP function var_dump and print_r.
 * It can correctly identify the recursively referenced objects in a complex
 * object structure. It also has a recursive depth control to avoid indefinite
 * recursive display of some peculiar variables.
 *
 * TVarDumper can be used as follows,
 * <code>
 *   echo TVarDumper::dump($var);
 * </code>
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package System.Util
 * @since 3.0
 */
class TVarDumper
{
    private static $_objects;
    private static $_output;
    private static $_depth;
    /**
     * Converts a variable into a string representation.
     * This method achieves the similar functionality as var_dump and print_r
     * but is more robust when handling complex objects such as PRADO controls.
     * @param mixed variable to be dumped
     * @param integer maximum depth that the dumper should go into the variable. Defaults to 10.
     * @return string the string representation of the variable
     */
    public static function dump($var,$depth=10,$highlight=false)
    {
        self::$_output='';
        self::$_objects=array();
        self::$_depth=$depth;
        self::dumpInternal($var,0);
        if($highlight)
        {
            $result=highlight_string("<?php\n".self::$_output,true);
            return preg_replace('/&lt;\\?php<br \\/>/','',$result,1);
        }
        else
            return self::$_output;
    }
    private static function dumpInternal($var,$level)
    {
        switch(gettype($var))
        {
            case 'boolean':
                self::$_output.=$var?'true':'false';
                break;
            case 'integer':
                self::$_output.="$var";
                break;
            case 'double':
                self::$_output.="$var";
                break;
            case 'string':
                self::$_output.="'$var'";
                break;
            case 'resource':
                self::$_output.='{resource}';
                break;
            case 'NULL':
                self::$_output.="null";
                break;
            case 'unknown type':
                self::$_output.='{unknown}';
                break;
            case 'array':
                if(self::$_depth<=$level)
                    self::$_output.='array(...)';
                else if(empty($var))
                    self::$_output.='array()';
                else
                {
                    $keys=array_keys($var);
                    $spaces=str_repeat(' ',$level*4);
                    self::$_output.="array\n".$spaces.'(';
                    foreach($keys as $key)
                    {
                        self::$_output.="\n".$spaces."    [$key] => ";
                        self::$_output.=self::dumpInternal($var[$key],$level+1);
                    }
                    self::$_output.="\n".$spaces.')';
                }
                break;
            case 'object':
                if(($id=array_search($var,self::$_objects,true))!==false)
                    self::$_output.=get_class($var).'#'.($id+1).'(...)';
                else if(self::$_depth<=$level)
                    self::$_output.=get_class($var).'(...)';
                else
                {
                    $id=array_push(self::$_objects,$var);
                    $className=get_class($var);
                    if ($var instanceof Config\Config) {
                        $members = $var->toArray();
                    } elseif ($var instanceof \Memcached) {
                        $members = $var->getAllKeys();
                        $members[] = [
                            'servers'           => $var->getServerList(),
                            'isPersistent'      => $var->isPersistent(),
                            'stats'             => $var->getStats(),
                        ];
                    } elseif ($var instanceof Data\ImmutableData) {
                        $reflect = new \ReflectionClass(get_class($var));
                        $members = [];
                        $staticProperties = array_keys($reflect->getStaticProperties());
                        foreach($reflect->getProperties(\ReflectionProperty::IS_PROTECTED) as $property){
                            if (in_array($property->name, $staticProperties)) {
                                $members[$property->name] = $reflect->getStaticPropertyValue($property->name);
                            } else {
                                if (is_callable([$var, $property->name])) {
                                    $members[$property->name] = $var->{$property->name}();
                                }
                            }
                        }
                        foreach($reflect->getProperties(\ReflectionProperty::IS_PUBLIC) as $property){
                            if (in_array($property->name, $staticProperties)) {
                                $members[$property->name] = $reflect->getStaticPropertyValue($property->name);
                            } else {
                                $members[$property->name] = $var->{$property->name};
                            }
                        }
                    } elseif ($var instanceof Data\ImmutableCollection) {
                        $iterator = $var->getIterator();
                        $members = [];
                        while($iterator->valid()){
                            $members[] = $iterator->current();
                            $iterator->next();
                        }
                    } elseif ($var instanceof \ArrayObject) {
                        $members = $var->getArrayCopy();
                    } elseif ($var instanceof \SplDoublyLinkedList) {
                        $clone = clone $var;
                        $clone->rewind();
                        $members = [];
                        while ($current = $clone->current()) {
                            $clone->next();
                            $members[] = $current;
                        }
                    } else {
                        $members=(array)$var;
                    }
                    $keys=array_keys($members);
                    $spaces=str_repeat(' ',$level*4);
                    self::$_output.="$className#$id\n".$spaces.'(';
                    foreach($keys as $key)
                    {
                        $keyDisplay=strtr(trim($key),array("\0"=>':'));
                        self::$_output.="\n".$spaces."    [$keyDisplay] => ";
                        self::$_output.=self::dumpInternal($members[$key],$level+1);
                    }
                    self::$_output.="\n".$spaces.')';
                }
                break;
        }
    }
}
