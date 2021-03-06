<?php

namespace GOL\Input;

use GetOpt\Getopt;
use GOL\Boards\Board;
use GOL\Output\Console;
use Icecave\Isolator\IsolatorTrait;

/**
 * User input
 * Allows user to set cells alive manually by using the x- and y-coords of the cells.
 * Use prepareBoard() to prepare a Board and register() to register optional arguments.
 */
class User extends Input
{
    use IsolatorTrait;

    /**
     * Prepares a Board for usage.
     * @param Board $_board Board to prepare.
     * @param Getopt $_getopt Option manager to check for optional arguments.
     */
    public function prepareBoard(Board &$_board, Getopt $_getopt): void
    {
        echo "Enter the x-coordinates, the y-coordinates and the state of your cell to 0(dead) or, by default, to 1(alive), seperated by a comma.\n" .
            "Example: \"25,10,1\" will set the cell at x:25 and y:10 to the state \"alive\".\n\n" .
            "(Enter \"finish\" to continue.)\n";

        $output = new Console();

        while (true)
        {
            $output->write($_board);
            $line = $this->isolator()->readline("Cell >> ");

            if ($line == "finish")
                break;
            $coordinates = explode(",", $line);

            if (count($coordinates) == 3)
            {
                $_board->setFieldValue($coordinates[0], $coordinates[1], $coordinates[2]);
            }

            if (count($coordinates) == 2)
            {
                $_board->setFieldValue($coordinates[0], $coordinates[1], 1);
            }
        }

    }

    /**
     * Returns the description of the Input.
     * @return string description.
     */
    public function description(): string
    {
        return "Allows to set the state of specific cells.";
    }
}