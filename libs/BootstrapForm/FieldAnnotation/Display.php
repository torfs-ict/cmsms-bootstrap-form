<?php

namespace BootstrapForm\FieldAnnotation;
use BootstrapForm\FieldAnnotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Display extends FieldAnnotation {
    /** @var int col-xs */
    public $columnsDefault = 12;
    /** @var int col-sm */
    public $columnsPhone = null;
    /** @var int col-md */
    public $columnsTablet = null;
    /** @var int col-lg */
    public $columnsDesktop = null;
    /** @var int col-xl */
    public $columnsTv = null;
    /** @var int offset-xs */
    public $columnOffsetDefault = null;
    /** @var int offset-sm */
    public $columnOffsetPhone = null;
    /** @var int offset-md */
    public $columnOffsetTablet = null;
    /** @var int offset-lg */
    public $columnOffsetDesktop = null;
    /** @var int offset-xl */
    public $columnOffsetTv = null;
    /** @var string[] Extra CSS classes */
    public $cssClasses = [];
    /** @var string The field help text */
    public $helpText = null;
    /** @var bool Whether or not to hide the label (please use a placeholder when hiding) */
    public $hideLabel = false;
    /** @var string The input type */
    public $input = 'text';
    /** @var string The field label */
    public $label = '';
    /** @var string The placeholder text */
    public $placeholder = null;
    /** @var bool This fields ends the current Bootstrap row */
    public $rowEnd = false;
    /** @var bool This fields starts a new Bootstrap row */
    public $rowStart = false;
    /** @var bool Whether or not the field should be hidden initially */
    public $visible = true;
}