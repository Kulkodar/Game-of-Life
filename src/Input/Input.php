<?php

namespace GOL\Input;

use GetOpt\Getopt;
use GetOpt\Option;
use GOL\Boards\Board;

/**
 * Baseclass for pluggable inputs.
 *
 * Implement prepareBoard() to fill a Board in a specific way
 * and register() to register optional arguments.
 * @codeCoverageIgnore
 */
abstract class Input
{
    /**
     * Prepares a Board for usage.
     * @param Board $_board Board to prepare.
     * @param Getopt $_getopt Option manager to check for optional arguments.
     */
    abstract public function prepareBoard(Board &$_board, Getopt $_getopt): void;

    /**
     * Register all optional parameters of an Input, if any.
     * @return Option[] Array of options.
     */
    public function register(): array
    {
        return [];
    }

    /**
     * Returns the description of the Input.
     * This is used to list all inputs if the argument inputList is set.
     * @return string description.
     */
    public function description(): string
    {
        return "";
    }
}