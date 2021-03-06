<?php

namespace Boards;

use GOL\Boards\Board;
use GOL\Boards\Field;
use PHPUnit\Framework\TestCase;

class BoardTest extends TestCase
{
    protected $board;

    protected function setUp(): void
    {
        $this->board = new Board(5, 5);
    }

    private function isGridZero($_grid)
    {
        $isEmpty = true;
        foreach ($_grid as $row)
        {
            foreach ($row as $cell)
            {
                if ($cell != 0)
                    $isEmpty = false;
            }
        }
        return $isEmpty;
    }

    /**
     * @test
     */
    public function constructedWidthIsActualWidth()
    {
        $this->assertEquals(5, $this->board->width());
    }

    /**
     * @test
     */
    public function constructedHeightIsActualHeight()
    {
        $this->assertEquals(5, $this->board->height());
    }

    /**
     * @test
     */
    public function constructedBoardIsEmpty()
    {
        $grid = $this->board->getGrid();

        $isEmpty = $this->isGridZero($grid);

        $this->assertTrue($isEmpty);
    }

    /**
     * @test
     */
    public function setCellCreatesNonEmptyGrid()
    {
        $this->board->setFieldValue(0, 0, 1);
        $grid = $this->board->getGrid();

        $isEmpty = $this->isGridZero($grid);

        $this->assertNotTrue($isEmpty);
    }

    /**
     * @test
     */
    public function setCellOutOfBoundCreatesNonEmptyGrid()
    {
        $this->board->setFieldValue(-1, 0, 1);
        $grid = $this->board->getGrid();

        $isEmpty = $this->isGridZero($grid);

        $this->assertTrue($isEmpty);
    }

    /**
     * @test
     */
    public function compareEqualBoardsReturnsTrue()
    {
        $emptyBoard = new Board(5, 5);
        $this->assertTrue($this->board->compare($emptyBoard));
    }

    /**
     * @test
     */
    public function compareNotEqualBoardsReturnsTrue()
    {
        $nonEmptyBoard = new Board(5, 5);
        $nonEmptyBoard->setFieldValue(0, 0, 1);
        $this->assertNotTrue($this->board->compare($nonEmptyBoard));
    }

    /**
     * @test
     */
    public function compareSmallerBoardsReturnsFalse()
    {
        $nonEmptyBoard = new Board(4, 4);
        $this->assertNotTrue($this->board->compare($nonEmptyBoard));
    }

    /**
     * @test
     */
    public function compareBiggerBoardsReturnsFalse()
    {
        $nonEmptyBoard = new Board(6, 6);
        $this->assertNotTrue($this->board->compare($nonEmptyBoard));
    }

    /**
     * @test
     */
    public function getEmptyCell()
    {
        $this->board->field(0, 0);
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function getOutOfBoundsCellReturnsNull()
    {
        $this->assertNull($this->board->field(-1, -1));
        $this->assertNull($this->board->field(10, 10));
    }

    /**
     * @test
     */
    public function getNeighbours()
    {
        $this->board->setFieldValue(0, 0, 1);
        $cell = $this->board->field(1, 1);

        $this->assertEquals(1, $this->board->countLivingNeighbours($cell));
    }

    /**
     * @test
     */
    public function getNeighboursOfAnOutOfBoundsCell()
    {
        $cell = new Field($this->board, -1, -1);

        $this->assertEquals(-1, $this->board->countLivingNeighbours($cell));
    }
}
