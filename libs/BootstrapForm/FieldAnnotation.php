<?php

namespace BootstrapForm;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use ReflectionProperty;

abstract class FieldAnnotation {
    /**
     * @param Form $form
     * @param string $field
     * @return static
     */
    public static function GetFor(Form $form, $field) {
        //AnnotationRegistry::registerAutoloadNamespace('\\BootstrapForm\\FieldAnnotation', __DIR__ . '/FieldAnnotation/');
        AnnotationRegistry::registerLoader('class_exists');
        $reader = new AnnotationReader();
        $prop = new ReflectionProperty(get_class($form), $field);
        $ret = $reader->getPropertyAnnotation($prop, get_called_class());
        return $ret;
    }
}