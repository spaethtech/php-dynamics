<?php
declare(strict_types=1);

namespace Tests\MVQN\Dynamics\Examples;

use MVQN\Dynamics\AutoObject;

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
     *
     */
    protected $test;




}
