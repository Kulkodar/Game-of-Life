<?php

namespace GOL\Boards;

/**
 * Represents a Game of Life world.
 *
 * Use countLivingNeighbours() to calculate the number of living neighbours and compare to compare two boards.
 */
class Board
{
    protected $grid;
    protected $width;
    protected $height;

    /**
     * @param int $_width Width of the Board.
     * @param int $_height Height af the Board.
     */
    public function __construct($_width, $_height)
    {
        $this->width = $_width;
        $this->height = $_height;

        // initialize the board
        for ($y = 0; $y < $_height + 2; $y++)
        {
            for ($x = 0; $x < $_width + 2; $x++)
            {
                $this->grid[$x][$y] = new Field($this, $x - 1, $y - 1);
            }
        }
    }

    /**
     * Runs the Game of Life algorithm.
     */
    public function nextGeneration()
    {
        $buffer = $this->getGrid();

        for ($y = 0; $y < $this->height(); $y++)
        {
            for ($x = 0; $x < $this->width(); $x++)
            {
                $cell = $this->getCell($x,$y);
                $nextState = $this->applyRule($this->countLivingNeighbours($cell), $this->grid[$x+1][$y+1]->value());
                $buffer[$x][$y] = $nextState;
            }
        }

        for ($y = 0; $y < $this->height(); $y++)
        {
            for ($x = 0; $x < $this->width(); $x++)
            {
                $this->setCell($x, $y, $buffer[$x][$y]);
            }
        }

    }

    /**
     * @param int $_numNeighbours Number of living cells in the neighbourhood.
     * @param bool $_isAlive State of the current cell.
     * @return int State of the cell in the next generation.
     */
    private function applyRule($_numNeighbours, $_isAlive)
    {
        $survival = [2, 3];
        $birth = [3];

        if ($_isAlive)
        {
            foreach ($survival as $s)
            {
                if ($_numNeighbours == $s)
                    return 1;
            }
        }
        else
        {
            foreach ($birth as $b)
            {
                if ($_numNeighbours == $b)
                    return 1;
            }
        }

        return 0;
    }

    /**
     * Returns the amount of living cells in the neighbourhood of a specific cell.
     *
     * No out of bound check due to the margin.
     *
     * @param Field $_field Field who's neighbours should be calculated.
     * @return int amount of living cells and -1 if given cell is out of bounds.
     */
    public function countLivingNeighbours(Field $_field)
    {
        $livingNeighbourCount = -1;
        $x = $_field->x();
        $y = $_field->y();
        // out of bounds and margin check
        if (!$this->isOutOfBounds($x, $y))
        {
            $relativeNeighbourIndices = [[-1, -1], [0, -1], [1, -1], [-1, 0], [1, 0], [-1, 1], [0, 1], [1, 1]];
            $livingNeighbourCount++;

            foreach ($relativeNeighbourIndices as $relativeNeighbour)
            {
                if ($this->grid[$x + $relativeNeighbour[0] + 1][$y + $relativeNeighbour[1] + 1]->value() == 1)
                    $livingNeighbourCount++;
            }
        }
        return $livingNeighbourCount;
    }

    /**
     * Compares the current board with the history.
     *
     * @param Board $_board board to check.
     * @return bool true if one of the previous boards is equal to to current board, false otherwise.
     */
    public function compare(Board $_board)
    {
        $equal = true;

        if ($_board->height() != $this->height() || $_board->width() != $this->width())
            return false;

        for ($y = 1; $y < $_board->height() + 1; $y++)
        {
            for ($x = 1; $x < $_board->width() + 1; $x++)
            {
                if ($this->grid[$x][$y]->value() != $_board->getCell($x - 1, $y - 1)->value())
                    $equal = false;
            }
        }

        return $equal;
    }

    /**
     * Changes the value of a cell.
     * @param int $_x X position of the cell.
     * @param int $_y Y position of the cell.
     * @param int $_value new value of the cell.
     */
    public function setCell($_x, $_y, $_value)
    {
        if ($this->isOutOfBounds($_x, $_y))
            return;

        $this->grid[$_x + 1][$_y + 1]->setValue($_value);
    }

    /**
     * Returns a cell at the given point.
     * @param int $_x X position of the cell.
     * @param int $_y Y position of the cell.
     * @return Field|null The cell or null pointer on invalid coordinates.
     */
    public function getCell(int $_x, int $_y): ?Field
    {
        if ($this->isOutOfBounds($_x, $_y))
            return null;

        return $this->grid[$_x + 1][$_y + 1];
    }

    /**
     * Returns a copy of the grid data
     * @return array Grid of the Board.
     */
    public function getGrid(): array
    {
        $result = array();

        for ($y = 1; $y < $this->height + 1; $y++)
        {
            for ($x = 1; $x < $this->width + 1; $x++)
            {
                $result[$x - 1][$y - 1] = $this->grid[$x][$y]->value();
            }
        }

        return $result;
    }

    /**
     * Returns the width of the board.
     * @return int Width of the board.
     */
    public function width()
    {
        return $this->width;
    }

    /**
     * Returns the height of the board.
     * @return int height of the board.
     */
    public function height()
    {
        return $this->height;
    }

    /**
     * Checks if a coordinate is out of bounds.
     * @param int $_x X position to check.
     * @param int $_y Y position to check.
     * @return bool True on out of bounds, otherwise false.
     */
    private function isOutOfBounds(int $_x, int $_y): bool
    {
        return $_x < 0 || $_y < 0 || $_x >= $this->width || $_y >= $this->height;
    }
}