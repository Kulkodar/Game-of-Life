<?php

namespace GOL\Output;

use GetOpt\Getopt;
use GetOpt\Option;
use GOL\Boards\Board;
use GOL\Seasonal;

/**
 * Saves the Board as a video.
 *
 * Use write() to write the Board data to the output.
 * and flush() to write it on the disc.
 */
class Video extends Output
{
    private $buffer = [];
    private $fps = 30.0;
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
        $this->buffer[] = $_board->getGrid();
    }

    /**
     * Writes the data to disk.
     */
    public function flush(): void
    {
        is_dir("out/") ? null : mkdir("out/", 0755);

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

        exec("ffmpeg -framerate " . $this->fps . " -i out/img-%03d.png out/video.avi");
        foreach (glob("out/img*") as $filename)
        {
            unlink($filename);
        }

        $this->buffer = [];
    }

    /**
     * Checks for optional parameters.
     * @param Getopt $_getopt Option manager to check for optional parameters.
     */
    public function checkParameters(Getopt $_getopt): void
    {
        $this->fps = $_getopt->getOption("videoFPS");
        if ($this->fps <= 0.0)
            $this->fps = 30.0;

        $this->cellSize = $_getopt->getOption("videoCellSize");
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

        foreach (explode(",", $_getopt->getOption("videoCellColor")) as $item => $value)
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

        foreach (explode(",", $_getopt->getOption("videoBackgroundColor")) as $item => $value)
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
    }

    /**
     * Register all optional parameters of an Input, if any.
     * @return Option[] Array of options.
     */
    public function register(): array
    {
        $result[] = new Option(null, "videoFPS", Getopt::REQUIRED_ARGUMENT);
        end($result)->setDescription("Sets the framerate of the video.");

        $result[] = new Option(null, "videoCellColor", Getopt::REQUIRED_ARGUMENT);
        end($result)->setDescription("Sets the color of living cells. 'r,g,b' 0-255.");

        $result[] = new Option(null, "videoBackgroundColor", Getopt::REQUIRED_ARGUMENT);
        end($result)->setDescription("Sets the background color. 'r,g,b' 0-255.");

        $result[] = new Option(null, "videoCellSize", Getopt::REQUIRED_ARGUMENT);
        end($result)->setDescription("Sets the size of the cells in pixel.");

        return $result;
    }

    /**
     * Returns the description of the Output.
     * @return string description.
     */
    public function description(): string
    {
        return "Outputs the Board as a video.";
    }
}