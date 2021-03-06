<?php

namespace GOL\Output;

use Exception;
use GetOpt\Getopt;
use GetOpt\Option;
use GifCreator\AnimGif;
use GOL\Boards\Board;
use GOL\Seasonal;

/**
 * Saves the board as a gif.
 *
 * Use write() to write the Board data to the buffer.
 * and flush() to write it on the disk.
 */
class Gif extends Output
{
    private $index = 0;
    private $delay = 1;
    private $cellColor = [];
    private $backgroundColor = [];
    private $cellSize = 1;

    /**
     * Writes the current board to the Output.
     *
     * @param Board $_board Board to output.
     */
    public function write(Board $_board): void
    {
        $board = $_board->getGrid();

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
        imagepng($image, "out/" . sprintf("img-%03d", $this->index) . ".png");
        $this->index++;
    }

    /**
     * Creates a gif.
     */
    public function flush(): void
    {
        $animGif = new AnimGif();
        try
        {
            $animGif->create("out/", $this->delay);
            $animGif->save("out/output.gif");
            foreach (glob("out/img*") as $filename)
            {
                unlink($filename);
            }
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }
    }

    /**
     * Checks for optional parameters.
     * @param Getopt $_getopt Option manager to check for optional parameters.
     */
    public function checkParameters(Getopt $_getopt): void
    {
        $this->delay = intval($_getopt->getOption("gifDelay"));
        if ($this->delay <= 0)
            $this->delay = 1;

        $this->cellSize = $_getopt->getOption("gifCellSize");
        if ($this->cellSize <= 0)
            $this->cellSize = 1;

        $seasonalColor = Seasonal::getHolidayColor();

        if (count($seasonalColor) == 6)
        {
            $this->backgroundColor[0] = $seasonalColor[0];
            $this->backgroundColor[1] = $seasonalColor[1];
            $this->backgroundColor[2] = $seasonalColor[2];
            $this->cellColor[0] = $seasonalColor[3];
            $this->cellColor[1] = $seasonalColor[4];
            $this->cellColor[2] = $seasonalColor[5];
        }

        foreach (explode(",", $_getopt->getOption("gifCellColor")) as $item => $value)
        {
            if ($value == "")
                break;

            $this->cellColor[$item] = $value;
        }
        if (empty($this->cellColor))
        {
            $this->cellColor[0] = 255;
            $this->cellColor[1] = 255;
            $this->cellColor[2] = 255;
        }

        foreach (explode(",", $_getopt->getOption("gifBackgroundColor")) as $item => $value)
        {
            if ($value == "")
                break;

            $this->backgroundColor[$item] = $value;
        }
        if (empty($this->backgroundColor))
        {
            $this->backgroundColor[0] = 0;
            $this->backgroundColor[1] = 0;
            $this->backgroundColor[2] = 0;
        }

        is_dir("out/") ? null : mkdir("out/", 0755);
    }

    /**
     * Register all optional parameters of an Input, if any.
     * @return Option[] Array of options.
     */
    public function register(): array
    {
        $result[] = new Option(null, "gifDelay", Getopt::REQUIRED_ARGUMENT);
        end($result)->setDescription("Sets the delay between frames (in 1/100s).");

        $result[] = new Option(null, "gifCellColor", Getopt::REQUIRED_ARGUMENT);
        end($result)->setDescription("Sets the color of living cells. 'r,g,b' 0-255.");

        $result[] = new Option(null, "gifBackgroundColor", Getopt::REQUIRED_ARGUMENT);
        end($result)->setDescription("Sets the background color. 'r,g,b' 0-255.");

        $result[] = new Option(null, "gifCellSize", Getopt::REQUIRED_ARGUMENT);
        end($result)->setDescription("Sets the size of the cells in pixel.");

        return $result;
    }

    /**
     * Returns the description of the Output.
     * @return string description.
     */
    public function description(): string
    {
        return "Outputs the Board as a gif.";
    }
}