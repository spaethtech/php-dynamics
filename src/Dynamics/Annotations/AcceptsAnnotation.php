<?php
declare(strict_types=1);
namespace rspaeth\Dynamics\Annotations;

use rspaeth\Annotations\Annotation;
use rspaeth\Annotations\Exceptions\AnnotationParsingException;


final class AcceptsAnnotation extends Annotation
{
    /** @const int Denotes supported annotation targets, defaults to ANY when not explicitly provided! */
    public const SUPPORTED_TARGETS = Annotation::TARGET_PROPERTY;

    /** @const bool Denotes supporting multiple declarations of this annotation per block, defaults to TRUE! */
    public const SUPPORTED_DUPLICATES = false;

    private const ANNOTATION_PATTERN_ARRAY_LITERAL = '/(\[.+\])/';
    private const ANNOTATION_PATTERN_IDENTIFIER = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';
    private const ANNOTATION_PATTERN_IDENTIFIER_STRING = '/^[\"|\']([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)[\"\']$/';

    /**
     * @param array $existing Any existing annotations that were previously parsed from the same declaration.
     *
     * @return array Returns an array of keyword => value(s) parsed by this Annotation implementation.
     * @throws AnnotationParsingException
     */
    public function parse(array $existing = []): array
    {
        try
        {
            $value = $this->value;

            if (preg_match(self::ANNOTATION_PATTERN_IDENTIFIER, $value, $matches))
            {
                $value = [ "$value" ];
                //echo "LITERAL";
            }
            else if (preg_match(self::ANNOTATION_PATTERN_IDENTIFIER_STRING, $value, $matches))
            {
                $value = [ str_replace("'", "", $value) ]; // Remove extraneous single-quotes!
                //echo "STRING";
            }
            else
            {
                $value = eval("return {$value};");
                //echo "ARRAY";
            }

            //var_dump($value);

            $existing["Accepts"] = $value;

            return $existing;
        }
        catch(\Exception $e)
        {
            throw new AnnotationParsingException("\n".
                "Could not parse the ". get_class($this) ."\n".
                "    Class      : {$this->class}\n" .
                "    Property   : {$this->name}\n" .
                "    Annotation : {$this->keyword}\n" .
                "    Arguments  : {$this->value}\n" .
                "    Exception  : {$e}\n"
            );
        }



    }
}
