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
    /** @var string Extra CSS classes */
    public $cssClasses = '';
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
    /** @var bool This fields starts a new Bootstrap row */
    public $row = false;
    /** @var bool Whether or not the field should be hidden initially */
    public $visible = true;

    public function getColumnClasses() {
        $ret = [];
        foreach($this as $property => $value) {
            if (empty($value)) continue;
            if (!fnmatch('column*', $property)) continue;
            switch($property) {
                case 'columnsDefault': $ret[] = sprintf('col-xs-%d', $value); break;
                case 'columnsPhone': $ret[] = sprintf('col-sm-%d', $value); break;
                case 'columnsTablet': $ret[] = sprintf('col-md-%d', $value); break;
                case 'columnsDesktop': $ret[] = sprintf('col-lg-%d', $value); break;
                case 'columnsTv': $ret[] = sprintf('col-xl-%d', $value); break;
                case 'columnOffsetDefault': $ret[] = sprintf('col-xs-offset-%d', $value); break;
                case 'columnOffsetPhone': $ret[] = sprintf('col-sm-offset-%d', $value); break;
                case 'columnOffsetTablet': $ret[] = sprintf('col-md-offset-%d', $value); break;
                case 'columnOffsetDesktop': $ret[] = sprintf('col-lg-offset-%d', $value); break;
                case 'columnOffsetTv': $ret[] = sprintf('col-xl-offset-%d', $value); break;
            }

        }
        return implode(' ', $ret);
    }
}