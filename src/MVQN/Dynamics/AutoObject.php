<?php
declare(strict_types=1);

namespace MVQN\Dynamics;


use MVQN\Annotations\AnnotationReader;
use MVQN\Collections\Collectible;
use MVQN\Common\Strings;

/**
 * Class AutoObject
 *
 * @package MVQN\Common
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
class AutoObject extends Collectible
{
    /**
     * @var array An array of cached annotations for this class to be used to speed up look-ups in future calls.
     */
    private static $annotationCache = null;

    /**
     * @const array a list of methods for which to ignore when parsing annotations.
     */
    private const IGNORE_METHODS = [
        "__construct",
        "__toString",
        "jsonSerialize"
    ];

    /**
     * @const array a list of properties for which to ignore when parsing annotations.
     */
    private const IGNORE_PROPERTIES = [
        "annotationCache"
    ];





    private function buildAnnotationCache(): void
    {
        self::$annotationCache = [];

        // Instantiate an Annotation Reader!
        $annotationReader = new AnnotationReader(get_class($this));
        self::$annotationCache = $annotationReader->getAnnotations();
    }

    private static function buildStaticAnnotationCache(): void
    {
        self::$annotationCache = [];

        $class = get_called_class();

        // Instantiate an Annotation Reader!
        $annotationReader = new AnnotationReader($class);
        self::$annotationCache = $annotationReader->getAnnotations();
    }




    private $firstCallOcurred = false;



    public function __call(string $name, array $args)
    {


        // Check to see if a real method already exists for the requested __call()...
        if(method_exists($this, $name))
            return $name($args);

        $class = get_class($this);

        // Build the cache for this class, if it has not already been done!
        if(self::$annotationCache === null)
        {
            $this->buildAnnotationCache();
        }

        // Handle the cases, where the method called begins with 'get'...
        if(Strings::startsWith($name, "get"))
        {
            $property = lcfirst(str_replace("get", "", $name));

            if(!array_key_exists("class", self::$annotationCache) ||
                !array_key_exists("method", self::$annotationCache["class"]))
                throw new \Exception("Method '$name' was either not defined or does not have an annotation in class '".
                    $class."'!");

            $regex = "/^(?:[\w\|\[\]]*)?\s+(get\w*)\s*\(.*\).*$/";
            $found = false;

            foreach (self::$annotationCache["class"]["method"] as $annotation)
            {
                if(Strings::startsWith($annotation["name"], "get"))
                    //if(preg_match($regex, $annotation, $matches))
                {
                    //if(in_array($name, $matches))
                    {
                        $found = true;
                        break;
                    }
                }
            }

            if(!$found)
                throw new \Exception("Method '$name' was either not defined or does not have an annotation in class '".
                    $class."'!");

            // Should be a valid method by this point!

            if(!property_exists($this, $property))
                throw new \Exception("Property '$property' was not found in class '$class', so method '$name' could ".
                    "not be called!");

            return $this->{$property};
        }

        if(Strings::startsWith($name, "set"))
        {
            $property = lcfirst(str_replace("set", "", $name));

            if(!array_key_exists("class", self::$annotationCache) ||
                !array_key_exists("method", self::$annotationCache["class"]))
                throw new \Exception("Method '$name' was either not defined or does not have an annotation in class '".
                    $class."'!");

            //$regex = "/^(?:[\w\|\[\]]*)?\s+(set\w*)\s*\(.*\).*$/";
            $found = false;

            foreach (self::$annotationCache["class"]["method"] as $annotation)
            {
                if(Strings::startsWith($annotation["name"], "set"))
                    //if(preg_match($regex, $annotation, $matches))
                {
                    //if(in_array($name, $matches))
                    {
                        $found = true;
                        break;
                    }
                }
            }

            if(!$found)
                throw new \Exception("Method '$name' was either not defined or does not have an annotation in class '".
                    $class."'!");

            // Should be a valid method by this point!

            if(!property_exists($this, $property))
                throw new \Exception("Property '$property' was not found in class '$class', so method '$name' could ".
                    "not be called!");

            $this->{$property} = $args[0];
            return $this;
        }

        throw new \Exception("Method '$name' was either not defined or does not have an annotation in class '".
            $class."'!");
    }



    private static $_beforeFirstStaticCallOccurred = false;
    private static $_afterFirstStaticCallOccurred = false;

    public static function __callStatic(string $name, array $args)
    {
        $class = get_called_class();

        if(!self::$_beforeFirstStaticCallOccurred)
        {
            if(method_exists($class, "__beforeFirstStaticCall"))
                $class::__beforeFirstStaticCall();

            self::$_beforeFirstStaticCallOccurred = true;
        }

        if(method_exists($class, "__beforeStaticCall"))
            $class::__beforeStaticCall();

        // Check to see if a real method already exists for the requested __call()...
        if(method_exists($class, $name))
            return $name($args);

        $object = new $class();

        // Build the cache for this class, if it has not already been done!
        if(self::$annotationCache === null)
        {
            self::buildStaticAnnotationCache();
        }

        // Handle the cases, where the method called begins with 'get'...
        if(Strings::startsWith($name, "get"))
        {
            $property = lcfirst(str_replace("get", "", $name));

            if(!array_key_exists("class", self::$annotationCache) ||
                !array_key_exists("method", self::$annotationCache["class"]))
                throw new \Exception("Method '$name' was either not defined or does not have an annotation in class '".
                    $class."'!");

            $regex = "/^(?:[\w\|\[\]]*)?\s+(get\w*)\s*\(.*\).*$/";
            $found = false;

            foreach (self::$annotationCache["class"]["method"] as $annotation)
            {
                $methodName = $annotation["name"];

                if(Strings::startsWith($annotation["name"], "get"))
                    //if(preg_match($regex, $annotation, $matches))
                {
                    //if(in_array($name, $matches))
                    {
                        $found = true;
                        break;
                    }
                }
            }

            if(!$found)
                throw new \Exception("Method '$name' was either not defined or does not have an annotation in class '".
                    $class."'!");

            // Should be a valid method by this point!

            if(!property_exists($class, $property))
                throw new \Exception("Property '$property' was not found in class '$class', so method '$name' could ".
                    "not be called!");

            $return = $class::$$property;

            if(!self::$_afterFirstStaticCallOccurred)
            {
                if (method_exists($class, "__afterFirstStaticCall"))
                    $return = $class::__afterFirstStaticCall($return);

                self::$_afterFirstStaticCallOccurred = true;
            }

            if(method_exists($class, "__afterStaticCall"))
                $return = $class::__afterStaticCall($return);

            return $return;
        }
        elseif(Strings::startsWith($name, "set"))
        {
            $property = lcfirst(str_replace("set", "", $name));

            if(!array_key_exists("class", self::$annotationCache) ||
                !array_key_exists("method", self::$annotationCache["class"]))
                throw new \Exception("Method '$name' was either not defined or does not have an annotation in class '".
                    $class."'!");

            //$regex = "/^(?:[\w\|\[\]]*)?\s+(set\w*)\s*\(.*\).*$/";
            $found = false;

            foreach (self::$annotationCache["class"]["method"] as $annotation)
            {
                if(Strings::startsWith($annotation["name"], "set"))
                    //if(preg_match($regex, $annotation, $matches))
                {
                    //if(in_array($name, $matches))
                    {
                        $found = true;
                        break;
                    }
                }
            }

            if(!$found)
                throw new \Exception("Method '$name' was either not defined or does not have an annotation in class '".
                    $class."'!");

            // Should be a valid method by this point!

            if(!property_exists($class, $property))
                throw new \Exception("Property '$property' was not found in class '$class', so method '$name' could ".
                    "not be called!");

            $value = $args[0];

            if(!self::$_afterFirstStaticCallOccurred)
            {
                if (method_exists($class, "__afterFirstStaticCall"))
                    $value = $class::__afterFirstStaticCall($value);

                self::$_afterFirstStaticCallOccurred = true;
            }

            if(method_exists($class, "__afterStaticCall"))
                $value = $class::__afterStaticCall($value);

            $class::$$property = $value;
        }
        else
        {
            throw new \Exception("Method '$name' was either not defined or does not have an annotation in class '".
                $class."'!");
        }

        //return null;
    }



}