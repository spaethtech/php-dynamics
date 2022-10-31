<?php
declare(strict_types=1);
namespace SpaethTech\Dynamics\Examples;

use SpaethTech\Dynamics\AutoObject;
use SpaethTech\Dynamics\Annotations\AcceptsAnnotation as Accepts;

/**
 * Class Country
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@spaethtech.com>
 * @final
 *
 * @method string|null getName()
 * @method string|null getCode()
 * @method string|null getTest()
 * @method Country setTest(string $test)
 */
final class Country extends AutoObject
{
    // =================================================================================================================
    // PROPERTIES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     * @PostRequired
     */
    protected $name;

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var string
     * @PostRequired `$this->name === "United States"`
     */
    protected $code;

    /**
     * @var string
     * @Accepts [ "currency_code", "currency" ]
     */
    protected $currencyCode;

    /**
     * @var string
     *
     */
    protected $test;



}
