<?php
declare(strict_types=1);
namespace rspaeth\Dynamics\Examples;

use rspaeth\Dynamics\AutoObject;
use rspaeth\Dynamics\Annotations\AcceptsAnnotation as Accepts;

/**
 * Class Country
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
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
