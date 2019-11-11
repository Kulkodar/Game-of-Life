<?php

namespace GOL\Output;

require_once "seasonal.php";

use GOL\Boards\Board;
use Ulrichsg\Getopt;

/**
 * Saves the Board as a png sequence.
 *
 * Use write() to write the Board data to the output.
 * and flush() to write it on the disc.
 */
class PNG extends Output
{
    private $buffer = [];
    private $cellSize = 1;
    private $backgroundColor = [];
    private $cellColor = [];

    /**
     * Writes the current board to the Output.
     *
     * @param Board $_board Board to output.
     */
    public function write(Board $_board): void
    {
        $this->buffer[] = $_board->getGrid();
    }

    /**
     * Writes the data to disk.
     */
    public function flush(): void
    {
        if (!is_dir("out/"))
            mkdir("out/", 0755);

        foreach ($this->buffer as $index => $board)
        {
            $width = count($board);
            $height = count($board[0]);
            $cellSize = $this->cellSize;

            $image = imagecreate($width * $cellSize, $height * $cellSize);
            imagecolorallocate($image, $this->backgroundColor[0], $this->backgroundColor[1], $this->backgroundColor[2]);
            $cellColor = imagecolorallocate($image, $this->cellColor[0], $this->cellColor[1], $this->cellColor[2]);

            for ($y = 0; $y < $height; $y++)
            {
                for ($x = 0; $x < $width; $x++)
                {
                    if ($board[$x][$y] == 1)
                        imagefilledrectangle($image, $x * $cellSize, $y * $cellSize,
                                             $x * $cellSize + $cellSize - 1, $y * $cellSize + $cellSize - 1, $cellColor);
                }
            }

            imagepng($image, "out/" . sprintf("img-%03d", $index) . ".png");
        }
        $this->buffer = [];
    }

    /**
     * Checks for optional parameters.
     * @param Getopt $_getopt Option manager to check for optional parameters.
     */
    public function checkParamerters(Getopt $_getopt): void
    {
        $this->cellSize = intval($_getopt->getOption("pngCellSize"));
        if ($this->cellSize <= 0)
            $this->cellSize = 1;

        $seasonalColor = getHolidayColor();

        if (count($seasonalColor) == 6)
        {
            $this->backgroundColor[0] = $seasonalColor[0];
            $this->backgroundColor[1] = $seasonalColor[1];
            $this->backgroundColor[2] = $seasonalColor[2];
            $this->cellColor[0] = $seasonalColor[3];
            $this->cellColor[1] = $seasonalColor[4];
            $this->cellColor[2] = $seasonalColor[5];
        }

        foreach (explode(",", $_getopt->getOption("pngBackgroundColor")) as $item => $value)
        {
            if ($value == "")
                break;

            $this->backgroundColor[$item] = $value;
        }
        foreach (explode(",", $_getopt->getOption("pngCellColor")) as $item => $value)
        {
            if ($value == "")
                break;

            $this->cellColor[$item] = $value;
        }

        if (empty($this->backgroundColor))
        {
            $this->backgroundColor[0] = 0;
            $this->backgroundColor[1] = 0;
            $this->backgroundColor[2] = 0;
        }
        if (empty($this->cellColor))
        {
            $this->cellColor[0] = 255;
            $this->cellColor[1] = 255;
            $this->cellColor[2] = 255;
        }
    }

    /**
     * Register all optional parameters the Output.
     * @param Getopt $_getopt Option manager to add the options
     */
    public function register(Getopt $_getopt): void
    {
        $_getopt->addOptions(
            [
                [null, "pngCellColor", Getopt::REQUIRED_ARGUMENT, "Sets the color of living cells. 'r,g,b' 0-255."],
                [null, "pngBackgroundColor", Getopt::REQUIRED_ARGUMENT, "Sets the background color. 'r,g,b' 0-255."],
                [null, "pngCellSize", Getopt::REQUIRED_ARGUMENT, "Sets the size of the cells in pixel."]
            ]);
    }

    /**
     * Returns the description of the Output.
     * @return string description.
     */
    public function description(): string
    {
        return "Outputs the Board as a png sequence.";
    }
}