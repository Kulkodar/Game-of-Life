<?php

namespace Input;

use GetOptMock;
use GOL\Boards\Board;
use GOL\Input\Plaintext;
use PHPUnit\Framework\TestCase;

class PlaintextTest extends TestCase
{
    protected $input;
    protected $getOpt;

    protected function setUp(): void
    {
        $this->input = new Plaintext();
        $this->getOpt = new GetOptMock();

        $file = fopen("in/plain.cells", "w");
        fwrite($file,
               '!Name: Glider
!Author: Richard K. Guy
!The smallest, most common, and first discovered spaceship.
!www.conwaylife.com/wiki/index.php?title=Glider
.O
O.O
OOO');
        fclose($file);
    }

    /**
     * @test
     */
    public function prepareBoardWithEmptyArgument()
    {
        $board = new Board(5, 5);
        $array = [
            [0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0]];

        $this->input->prepareBoard($board, $this->getOpt);

        $this->assertEquals($array, $board->getGrid());
    }

    /**
     * @test
     */
    public function prepareBoardWithArgument()
    {
        $this->getOpt->setOptions(["plaintextFile" => "in/plain.cells"]);

        $board = new Board(5, 5);
        $array = [
            [0, 1, 1, 0, 0],
            [1, 0, 1, 0, 0],
            [0, 1, 1, 0, 0],
            [0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0]];

        $this->input->prepareBoard($board, $this->getOpt);

        $this->assertEquals($array, $board->getGrid());
    }

    /**
     * @test
     */
    public function description()
    {
        $this->assertIsString($this->input->description());
    }
}
