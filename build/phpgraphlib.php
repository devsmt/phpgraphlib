<?php
require_once ($GLOBALS["HACKLIB_ROOT"]);
class PHPGraphLib {
  const TITLE_CHAR_WIDTH = 6;
  const TITLE_CHAR_HEIGHT = 12;
  const TEXT_WIDTH = 6;
  const TEXT_HEIGHT = 12;
  const LEGEND_TEXT_WIDTH = 6;
  const LEGEND_TEXT_HEIGHT = 12;
  const DATA_VALUE_TEXT_WIDTH = 6;
  const DATA_VALUE_TEXT_HEIGHT = 12;
  const AXIS_VALUE_PADDING = 5;
  const DATA_VALUE_PADDING = 5;
  const X_AXIS_MARGIN_PERCENT = 12;
  const Y_AXIS_MARGIN_PERCENT = 8;
  const RANGE_DIVISOR_FACTOR = 25;
  const GRADIENT_MAX_COLORS = 200;
  const MULTI_OFFSET_TWO = 24;
  const MULTI_OFFSET_THREE = 15;
  const MULTI_OFFSET_FOUR = 10;
  const MULTI_OFFSET_FIVE = 9;
  const LEGEND_PADDING = 4;
  const LEGEND_MAX_CHARS = 15;
  protected $height = 300;
  protected $width = 400;
  protected $bool_bars = true;
  protected $bool_bar_outline = true;
  protected $bool_x_axis = true;
  protected $bool_y_axis = true;
  protected $bool_x_axis_values = true;
  protected $bool_y_axis_values = true;
  protected $bool_grid = true;
  protected $bool_line = false;
  protected $bool_data_values = false;
  protected $bool_x_axis_values_vert = true;
  protected $bool_data_points = false;
  protected $bool_title_left = false;
  protected $bool_title_right = false;
  protected $bool_title_center = true;
  protected $bool_background = false;
  protected $bool_title = false;
  protected $bool_ignore_data_fit_errors = false;
  protected $data_point_width = 6;
  protected $x_axis_value_interval = false;
  protected $data_set_count = 0;
  protected $data_min = 0;
  protected $data_max = 0;
  protected $data_count = 0;
  protected $bool_data = false;
  protected $bool_bars_generate = true;
  protected $bool_all_negative = false;
  protected $bool_all_positive = false;
  protected $bool_gradient = false;
  protected $bool_user_data_range = false;
  protected $all_zero_data = false;
  protected $bool_gradient_colors_found = array();
  protected $bool_y_axis_setup = false;
  protected $bool_x_axis_setup = false;
  protected $background_color;
  protected $grid_color;
  protected $bar_color;
  protected $outline_color;
  protected $x_axis_text_color;
  protected $y_axis_text_color;
  protected $title_color;
  protected $x_axis_color;
  protected $y_axis_color;
  protected $data_point_color;
  protected $data_value_color;
  protected $line_color = array();
  protected $line_color_default;
  protected $goal_line_color;
  protected $gradient_color_array;
  protected $gradient_handicap = array();
  protected $goal_line_custom_color;
  protected $image;
  protected $output_file;
  protected $error;
  protected $data_array;
  protected $actual_displayed_max_value;
  protected $actual_displayed_min_value;
  protected $data_currency;
  protected $data_format_array = array();
  protected $data_additional_length = 0;
  protected $data_format_generic;
  protected $bar_width;
  protected $space_width;
  protected $unit_scale;
  protected $goal_line_array = array();
  protected $horiz_grid_lines = array();
  protected $vert_grid_lines = array();
  protected $horiz_grid_values = array();
  protected $vert_grid_values = array();
  protected $x_axis_x1;
  protected $x_axis_y1;
  protected $x_axis_x2;
  protected $x_axis_y2;
  protected $y_axis_x1;
  protected $y_axis_y1;
  protected $y_axis_x2;
  protected $y_axis_y2;
  protected $lowest_x;
  protected $highest_x;
  protected $x_axis_margin;
  protected $y_axis_margin;
  protected $data_range_max;
  protected $data_range_min;
  protected $top_margin = 0;
  protected $right_margin = 0;
  protected $data_point_array = array();
  protected $multi_gradient_colors_1 = array();
  protected $multi_gradient_colors_2 = array();
  protected $multi_bar_colors = array();
  protected $color_darken_factor = 30;
  protected $bool_legend = false;
  protected $legend_total_chars = array();
  protected $legend_width;
  protected $legend_height;
  protected $legend_x;
  protected $legend_y;
  protected $legend_color;
  protected $legend_text_color;
  protected $legend_outline_color;
  protected $legend_swatch_outline_color;
  protected $legend_titles = array();
  public function __construct($width, $height, $output_file = null) {
    $this->width = $width;
    $this->height = $height;
    $this->output_file = $output_file;
    $this->initialize();
    $this->allocateColors();
  }
  protected function initialize() {
    if (!\hacklib_cast_as_boolean($this->output_file)) {
      header("Content-type: image/png");
    }
    if (!\hacklib_cast_as_boolean(function_exists("imagecreate"))) {
      $msg = sprintf("Errore %s ", "GD not enabled");
      throw new \Exception($msg);
    }
    $this->image = imagecreate($this->width, $this->height);
  }
  public function createGraph() {
    if (\hacklib_cast_as_boolean($this->bool_data)) {
      $this->analyzeData();
      if (!\hacklib_cast_as_boolean($this->bool_x_axis_setup)) {
        $this->setupXAxis();
      }
      if (!\hacklib_cast_as_boolean($this->bool_y_axis_setup)) {
        $this->setupYAxis();
      }
      $this->calcTopMargin();
      $this->calcRightMargin();
      $this->calcCoords();
      $this->setupData();
      if (\hacklib_cast_as_boolean($this->bool_background)) {
        $this->generateBackgound();
      }
      $this->setupGrid();
      if (\hacklib_cast_as_boolean($this->bool_bars_generate)) {
        $this->generateBars();
      }
      if (\hacklib_cast_as_boolean($this->bool_data_points)) {
        $this->generateDataPoints();
      }
      if (\hacklib_cast_as_boolean($this->bool_legend)) {
        $this->generateLegend();
      }
      if (\hacklib_cast_as_boolean($this->bool_title)) {
        $this->generateTitle();
      }
      if (\hacklib_cast_as_boolean($this->bool_x_axis)) {
        $this->generateXAxis();
      }
      if (\hacklib_cast_as_boolean($this->bool_y_axis)) {
        $this->generateYAxis();
      }
    } else {
      $this->error[] =
        "No valid data added to graph. Add data with the addData() function.";
    }
    $this->displayErrors();
    if (\hacklib_cast_as_boolean($this->output_file)) {
      imagepng($this->image, $this->output_file);
    } else {
      imagepng($this->image);
    }
    imagedestroy($this->image);
  }
  protected function setupData() {
    $unit_width =
      (($this->width - $this->y_axis_margin) - $this->right_margin) /
      (($this->data_count * 2) + $this->data_count);
    if (($unit_width < 1) &&
        (!\hacklib_cast_as_boolean($this->bool_ignore_data_fit_errors))) {
      $this->bool_bars_generate = false;
      $this->error[] = "Graph too small or too many data points.";
    } else {
      $this->bar_width = 2 * $unit_width;
      $this->space_width = $unit_width;
      $availVertSpace =
        ($this->height - $this->x_axis_margin) - $this->top_margin;
      if ($availVertSpace < 1) {
        $this->bool_bars_generate = false;
        $this->error[] = "Graph height not tall enough.";
      } else {
        if (\hacklib_cast_as_boolean($this->bool_user_data_range)) {
          if (\hacklib_cast_as_boolean($this->all_zero_data)) {
            if ($this->data_range_min > $this->data_min) {
              $this->data_min = $this->data_range_min;
            }
            if ($this->data_range_max < $this->data_max) {
              $this->data_max = $this->data_range_max;
            }
          }
          $graphTopScale = $this->data_range_max;
          $graphBottomScale = $this->data_range_min;
          $graphScaleRange = $graphTopScale - $graphBottomScale;
          $this->unit_scale = $availVertSpace / $graphScaleRange;
          $this->data_max = $this->data_range_max;
          $this->data_min = $this->data_range_min;
          if ($this->data_min < 0) {
            $this->x_axis_y1 -=
              round($this->unit_scale * abs($this->data_min));
            $this->x_axis_y2 -=
              round($this->unit_scale * abs($this->data_min));
          }
          if (\hacklib_equals($graphScaleRange, 0)) {
            $graphScaleRange = 100;
          }
        } else {
          $graphBottomScale = ($this->data_min < 0) ? $this->data_min : 0;
          $graphTopScale = ($this->data_max < 0) ? 0 : $this->data_max;
          $graphScaleRange = $graphTopScale - $graphBottomScale;
          if (\hacklib_equals($graphScaleRange, 0)) {
            $graphScaleRange = 100;
          }
          $this->unit_scale = $availVertSpace / $graphScaleRange;
          if ($this->data_min < 0) {
            $this->x_axis_y1 -=
              round($this->unit_scale * abs($this->data_min));
            $this->x_axis_y2 -=
              round($this->unit_scale * abs($this->data_min));
          }
        }
      }
    }
  }
  protected function generateBars() {
    $this->finalizeColors();
    $barCount = 0;
    $adjustment = 0;
    if (\hacklib_cast_as_boolean($this->bool_user_data_range) &&
        ($this->data_min >= 0)) {
      $adjustment = $this->data_min * $this->unit_scale;
    }
    $this->data_array = array_reverse($this->data_array);
    $dataset_offset = 0;
    switch ($this->data_set_count) {
      case 2:
        $dataset_offset = $this->bar_width * (self::MULTI_OFFSET_TWO / 100);
        break;
      case 3:
        $dataset_offset = $this->bar_width * (self::MULTI_OFFSET_THREE / 100);
        break;
      case 4:
        $dataset_offset = $this->bar_width * (self::MULTI_OFFSET_FOUR / 100);
        break;
      case 5:
        $dataset_offset = $this->bar_width * (self::MULTI_OFFSET_FIVE / 100);
        break;
    }
    foreach ($this->data_array as $data_set_num => $data_set) {
      $lineX2 = null;
      $lineY2 = null;
      reset($data_set);
      $xStart =
        $this->y_axis_x1 +
        ($this->space_width / 2) +
        ((key($data_set) - $this->lowest_x) *
         ($this->bar_width + $this->space_width));
      foreach ($data_set as $key => $item) {
        $hideBarOutline = false;
        $x1 = round($xStart + ($dataset_offset * $data_set_num));
        $x2 = round(
          $xStart + $this->bar_width + ($dataset_offset * $data_set_num)
        );
        $y1 = round(
          ($this->x_axis_y1 - ($item * $this->unit_scale)) + $adjustment
        );
        $y2 = round($this->x_axis_y1);
        if (\hacklib_cast_as_boolean($this->bool_user_data_range)) {
          if ($item <= $this->data_range_min) {
            $y1 = $y2;
            $hideBarOutline = true;
          } else {
            if ($item >= $this->data_range_max) {
              $y1 =
                ($this->x_axis_y1 -
                 ($this->actual_displayed_max_value * $this->unit_scale)) +
                $adjustment;
            }
          }
        }
        if (\hacklib_cast_as_boolean($this->bool_bars)) {
          if (\hacklib_cast_as_boolean($this->bool_gradient)) {
            $this->drawGradientBar(
              $x1,
              $y1,
              $x2,
              $y2,
              $this->multi_gradient_colors_1[$data_set_num],
              $this->multi_gradient_colors_2[$data_set_num],
              $data_set_num
            );
          } else {
            imagefilledrectangle(
              $this->image,
              $x1,
              $y1,
              $x2,
              $y2,
              $this->multi_bar_colors[$data_set_num]
            );
          }
          if (\hacklib_cast_as_boolean($this->bool_bar_outline) &&
              (!\hacklib_cast_as_boolean($hideBarOutline))) {
            imagerectangle(
              $this->image,
              $x1,
              $y2,
              $x2,
              $y1,
              $this->outline_color
            );
          }
        }
        if (\hacklib_cast_as_boolean($this->bool_line)) {
          $lineX1 = $x1 + ($this->bar_width / 2);
          $lineY1 = $y1;
          if (!((!isset($lineX2)) || \hacklib_equals($lineX2, false))) {
            imageline(
              $this->image,
              $lineX2,
              $lineY2,
              $lineX1,
              $lineY1,
              $this->line_color[$data_set_num]
            );
            $lineX2 = $lineX1;
            $lineY2 = $lineY1;
          } else {
            $lineX2 = $lineX1;
            $lineY2 = $lineY1;
          }
        }
        if (\hacklib_cast_as_boolean($this->bool_data_points)) {
          $pointX = $x1 + ($this->bar_width / 2);
          $this->data_point_array[] = array($pointX, $y1);
        }
        if (\hacklib_cast_as_boolean($this->bool_data_values)) {
          $dataX =
            ($x1 + ($this->bar_width / 2)) -
            ((strlen($item) * self::DATA_VALUE_TEXT_WIDTH) / 2);
          if ($item >= 0) {
            $dataY =
              ($y1 - self::DATA_VALUE_PADDING) - self::DATA_VALUE_TEXT_HEIGHT;
          } else {
            if (\hacklib_cast_as_boolean($this->bool_user_data_range) &&
                ($item <= $this->data_range_min)) {
              $dataY =
                ($y1 - self::DATA_VALUE_PADDING) -
                self::DATA_VALUE_TEXT_HEIGHT;
            } else {
              $dataY = $y1 + self::DATA_VALUE_PADDING;
            }
          }
          if (\hacklib_cast_as_boolean($this->data_format_array)) {
            $item = $this->applyDataFormats($item);
          }
          if (\hacklib_cast_as_boolean($this->data_currency)) {
            $item = $this->applyDataCurrency($item);
          }
          $dataX -=
            ($this->data_additional_length * self::DATA_VALUE_TEXT_WIDTH) / 2;
          imagestring(
            $this->image,
            2,
            $dataX,
            $dataY,
            $item,
            $this->data_value_color
          );
        }
        if (\hacklib_cast_as_boolean($this->bool_x_axis_values)) {
          if (\hacklib_cast_as_boolean($this->bool_x_axis_values_vert)) {
            if (\hacklib_cast_as_boolean($this->bool_all_negative)) {
              $textVertPos =
                round($this->y_axis_y2 - self::AXIS_VALUE_PADDING);
            } else {
              $textVertPos = round(
                $this->y_axis_y1 +
                (strlen($key) * self::TEXT_WIDTH) +
                self::AXIS_VALUE_PADDING
              );
            }
            $textHorizPos = round(
              ($xStart + ($this->bar_width / 2)) - (self::TEXT_HEIGHT / 2)
            );
            if (\hacklib_cast_as_boolean($this->x_axis_value_interval)) {
              if ($key % $this->x_axis_value_interval) {
              } else {
                imagestringup(
                  $this->image,
                  2,
                  $textHorizPos,
                  $textVertPos,
                  $key,
                  $this->x_axis_text_color
                );
              }
            } else {
              imagestringup(
                $this->image,
                2,
                $textHorizPos,
                $textVertPos,
                $key,
                $this->x_axis_text_color
              );
            }
          } else {
            if (\hacklib_cast_as_boolean($this->bool_all_negative)) {
              $textVertPos = round(
                ($this->y_axis_y2 - self::TEXT_HEIGHT) -
                self::AXIS_VALUE_PADDING
              );
            } else {
              $textVertPos = round(
                ($this->y_axis_y1 + ((self::TEXT_HEIGHT * 2) / 3)) -
                self::AXIS_VALUE_PADDING
              );
            }
            $textHorizPos = round(
              ($xStart + ($this->bar_width / 2)) -
              ((strlen($key) * self::TEXT_WIDTH) / 2)
            );
            if (\hacklib_cast_as_boolean($this->x_axis_value_interval)) {
              if ($key % $this->x_axis_value_interval) {
              } else {
                imagestring(
                  $this->image,
                  2,
                  $textHorizPos,
                  $textVertPos,
                  $key,
                  $this->x_axis_text_color
                );
              }
            } else {
              imagestring(
                $this->image,
                2,
                $textHorizPos,
                $textVertPos,
                $key,
                $this->x_axis_text_color
              );
            }
          }
        }
        $xStart += $this->bar_width + $this->space_width;
      }
    }
  }
  protected function finalizeColors() {
    if (\hacklib_cast_as_boolean($this->bool_gradient)) {
      $num_set = count($this->multi_gradient_colors_1);
      if (\hacklib_not_equals($num_set, $this->data_set_count)) {
        $color_darken_decimal = (100 - $this->color_darken_factor) / 100;
        while ($num_set < $this->data_set_count) {
          $color_ref_1 = $this->multi_gradient_colors_1[$num_set - 1];
          $color_ref_2 = $this->multi_gradient_colors_2[$num_set - 1];
          $this->multi_gradient_colors_1[] = array(
            round($color_ref_1[0] * $color_darken_decimal),
            round($color_ref_1[1] * $color_darken_decimal),
            round($color_ref_1[2] * $color_darken_decimal)
          );
          $this->multi_gradient_colors_2[] = array(
            round($color_ref_2[0] * $color_darken_decimal),
            round($color_ref_2[1] * $color_darken_decimal),
            round($color_ref_2[2] * $color_darken_decimal)
          );
          $num_set++;
        }
      }
      while (count($this->multi_gradient_colors_1) > $this->data_set_count) {
        $temp = array_pop($this->multi_gradient_colors_1);
      }
      while (count($this->multi_gradient_colors_2) > $this->data_set_count) {
        $temp = array_pop($this->multi_gradient_colors_2);
      }
      $this->multi_gradient_colors_1 =
        array_reverse($this->multi_gradient_colors_1);
      $this->multi_gradient_colors_2 =
        array_reverse($this->multi_gradient_colors_2);
    } else {
      if (!\hacklib_cast_as_boolean($this->bool_gradient)) {
        $num_set = count($this->multi_bar_colors);
        if (\hacklib_equals($num_set, 0)) {
          $this->multi_bar_colors[0] = $this->bar_color;
          $num_set = 1;
        }
        while ($num_set < $this->data_set_count) {
          $color_ref = $this->multi_bar_colors[$num_set - 1];
          $color_parts = imagecolorsforindex($this->image, $color_ref);
          $color_darken_decimal = (100 - $this->color_darken_factor) / 100;
          $this->multi_bar_colors[$num_set] = imagecolorallocate(
            $this->image,
            round($color_parts[\hacklib_id("red")] * $color_darken_decimal),
            round(
              $color_parts[\hacklib_id("green")] * $color_darken_decimal
            ),
            round($color_parts[\hacklib_id("blue")] * $color_darken_decimal)
          );
          $num_set++;
        }
        while (count($this->multi_bar_colors) > $this->data_set_count) {
          $temp = array_pop($this->multi_bar_colors);
        }
        $this->multi_bar_colors = array_reverse($this->multi_bar_colors);
      }
    }
    if (\hacklib_cast_as_boolean($this->bool_line)) {
      if (!\hacklib_cast_as_boolean($this->bool_bars)) {
        $num_set = count($this->line_color);
        if (\hacklib_equals($num_set, 0)) {
          $this->line_color[0] = $this->line_color_default;
          $num_set = 1;
        }
        while ($num_set < $this->data_set_count) {
          $color_ref = $this->line_color[$num_set - 1];
          $color_parts = imagecolorsforindex($this->image, $color_ref);
          $color_darken_decimal = (100 - $this->color_darken_factor) / 100;
          $this->line_color[$num_set] = imagecolorallocate(
            $this->image,
            round($color_parts[\hacklib_id("red")] * $color_darken_decimal),
            round(
              $color_parts[\hacklib_id("green")] * $color_darken_decimal
            ),
            round($color_parts[\hacklib_id("blue")] * $color_darken_decimal)
          );
          $num_set++;
        }
      } else {
        $num_set = count($this->line_color);
        while ($num_set < $this->data_set_count) {
          $this->line_color[$num_set] = $this->line_color_default;
          $num_set++;
        }
      }
      while (count($this->line_color) > $this->data_set_count) {
        $temp = array_pop($this->line_color);
      }
      $this->line_color = array_reverse($this->line_color);
    }
  }
  protected function drawGradientBar(
    $x1,
    $y1,
    $x2,
    $y2,
    $colorArr1,
    $colorArr2,
    $data_set_num
  ) {
    if ((!\hacklib_cast_as_boolean(
           isset($this->bool_gradient_colors_found[$data_set_num])
         )) ||
        \hacklib_equals(
          $this->bool_gradient_colors_found[$data_set_num],
          false
        )) {
      $this->gradient_handicap[$data_set_num] = 0;
      $numLines = abs($x1 - $x2) + 1;
      while (($numLines * $this->data_set_count) >
             self::GRADIENT_MAX_COLORS) {
        $numLines /= 2;
        $this->gradient_handicap[$data_set_num]++;
      }
      $color1R = $colorArr1[0];
      $color1G = $colorArr1[1];
      $color1B = $colorArr1[2];
      $color2R = $colorArr2[0];
      $color2G = $colorArr2[1];
      $color2B = $colorArr2[2];
      $rScale = ($color1R - $color2R) / $numLines;
      $gScale = ($color1G - $color2G) / $numLines;
      $bScale = ($color1B - $color2B) / $numLines;
      $this->allocateGradientColors(
        $color1R,
        $color1G,
        $color1B,
        $rScale,
        $gScale,
        $bScale,
        $numLines,
        $data_set_num
      );
    }
    $numLines = abs($x1 - $x2) + 1;
    if ($this->gradient_handicap[$data_set_num] > 0) {
      $interval = $this->gradient_handicap[$data_set_num];
      for ($i = 0; $i < $numLines; $i++) {
        $adjusted_index = ceil($i / pow(2, $interval)) - 1;
        if ($adjusted_index < 0) {
          $adjusted_index = 0;
        }
        imageline(
          $this->image,
          $x1 + $i,
          $y1,
          $x1 + $i,
          $y2,
          $this->gradient_color_array[$data_set_num][$adjusted_index]
        );
      }
    } else {
      for ($i = 0; $i < $numLines; $i++) {
        imageline(
          $this->image,
          $x1 + $i,
          $y1,
          $x1 + $i,
          $y2,
          $this->gradient_color_array[$data_set_num][$i]
        );
      }
    }
  }
  protected function setupGrid() {
    $adjustment = 0;
    if (\hacklib_cast_as_boolean($this->bool_user_data_range) &&
        ($this->data_min >= 0)) {
      $adjustment = $this->data_min * $this->unit_scale;
    }
    $this->calculateGridHoriz($adjustment);
    $this->calculateGridVert();
    $this->generateGrids();
    $this->generateGoalLines($adjustment);
  }
  protected function calculateGridHoriz($adjustment = 0) {
    $horizGridArray = array();
    $min =
      \hacklib_cast_as_boolean($this->bool_user_data_range)
        ? $this->data_min
        : 0;
    $horizGridArray[] = $min;
    $intervalFromZero =
      $this->determineAxisMarkerScale($this->data_max, $this->data_min);
    $cur = $min;
    while ($cur < $this->data_max) {
      $cur += $intervalFromZero;
      $horizGridArray[] = $cur;
    }
    $cur = $min;
    while ($cur > $this->data_min) {
      $cur -= $intervalFromZero;
      $horizGridArray[] = $cur;
    }
    sort($horizGridArray);
    $this->actual_displayed_max_value =
      $horizGridArray[count($horizGridArray) - 1];
    $this->actual_displayed_min_value = $horizGridArray[0];
    $yValue = 0;
    foreach ($horizGridArray as $value) {
      $yValue = round(
        ($this->x_axis_y1 - ($value * $this->unit_scale)) + $adjustment
      );
      if (\hacklib_cast_as_boolean($this->bool_grid)) {
        $this->horiz_grid_lines[] = array(
          "x1" => $this->y_axis_x1,
          "y1" => $yValue,
          "x2" => $this->x_axis_x2,
          "y2" => $yValue,
          "color" => $this->grid_color
        );
      }
      if (\hacklib_cast_as_boolean($this->bool_y_axis_values)) {
        $adjustedYValue = $yValue - (self::TEXT_HEIGHT / 2);
        $adjustedXValue =
          ($this->y_axis_x1 -
           ((strlen($value) + $this->data_additional_length) *
            self::TEXT_WIDTH)) -
          self::AXIS_VALUE_PADDING;
        if (\hacklib_cast_as_boolean($this->data_format_array)) {
          $value = $this->applyDataFormats($value);
        }
        if (\hacklib_cast_as_boolean($this->data_currency)) {
          $value = $this->applyDataCurrency($value);
        }
        $this->horiz_grid_values[] = array(
          "size" => 2,
          "x" => $adjustedXValue,
          "y" => $adjustedYValue,
          "value" => $value,
          "color" => $this->y_axis_text_color
        );
      }
    }
    if ((!\hacklib_cast_as_boolean($this->bool_all_positive)) &&
        (!\hacklib_cast_as_boolean($this->bool_user_data_range))) {
      $this->y_axis_y1 =
        $this->x_axis_y1 - ($horizGridArray[0] * $this->unit_scale);
    }
    $this->y_axis_y2 = $yValue;
  }
  protected function calculateGridVert() {
    $vertGridArray = array();
    $vertGrids = $this->data_count + 1;
    $interval = $this->bar_width + $this->space_width;
    for ($i = 1; $i < $vertGrids; $i++) {
      $vertGridArray[] = $this->y_axis_x1 + ($interval * $i);
    }
    if (\hacklib_cast_as_boolean($this->bool_grid)) {
      $xValue = $this->y_axis_y1;
      foreach ($vertGridArray as $value) {
        $this->vert_grid_lines[] = array(
          "x1" => $value,
          "y1" => $this->y_axis_y2,
          "x2" => $value,
          "y2" => $xValue,
          "color" => $this->grid_color
        );
      }
    }
  }
  protected function imagelinedashed(
    &$image_handle,
    $x_axis_x1,
    $yLocation,
    $x_axis_x2,
    $color
  ) {
    $step = 3;
    for ($i = $x_axis_x1; $i < ($x_axis_x2 - 1); $i += $step * 2) {
      imageline(
        $this->image,
        $i,
        $yLocation,
        ($i + $step) - 1,
        $yLocation,
        $color
      );
    }
  }
  protected function generateGrids() {
    foreach ($this->horiz_grid_lines as $line) {
      imageline(
        $this->image,
        $line[\hacklib_id("x1")],
        $line[\hacklib_id("y1")],
        $line[\hacklib_id("x2")],
        $line[\hacklib_id("y2")],
        $line[\hacklib_id("color")]
      );
    }
    foreach ($this->vert_grid_lines as $line) {
      imageline(
        $this->image,
        $line[\hacklib_id("x1")],
        $line[\hacklib_id("y1")],
        $line[\hacklib_id("x2")],
        $line[\hacklib_id("y2")],
        $line[\hacklib_id("color")]
      );
    }
    foreach ($this->horiz_grid_values as $value) {
      imagestring(
        $this->image,
        $value[\hacklib_id("size")],
        $value[\hacklib_id("x")],
        $value[\hacklib_id("y")],
        $value[\hacklib_id("value")],
        $value[\hacklib_id("color")]
      );
    }
    foreach ($this->vert_grid_values as $value) {
      imagestring(
        $this->image,
        $value[\hacklib_id("size")],
        $value[\hacklib_id("x")],
        $value[\hacklib_id("y")],
        $value[\hacklib_id("value")],
        $value[\hacklib_id("color")]
      );
    }
  }
  protected function generateGoalLines($adjustment = 0) {
    foreach ($this->goal_line_array as $goal_line_data) {
      $yLocation = $goal_line_data[\hacklib_id("yValue")];
      $style = $goal_line_data[\hacklib_id("style")];
      $color =
        \hacklib_cast_as_boolean($goal_line_data[\hacklib_id("color")])
          ? $goal_line_data[\hacklib_id("color")]
          : $this->goal_line_color;
      $yLocation = round(
        ($this->x_axis_y1 - ($yLocation * $this->unit_scale)) + $adjustment
      );
      if (\hacklib_equals($style, "dashed")) {
        $this->imagelinedashed(
          $this->image,
          $this->x_axis_x1,
          $yLocation,
          $this->x_axis_x2,
          $color
        );
      } else {
        imageline(
          $this->image,
          $this->x_axis_x1,
          $yLocation,
          $this->x_axis_x2,
          $yLocation,
          $color
        );
      }
    }
  }
  protected function generateDataPoints() {
    foreach ($this->data_point_array as $pointArray) {
      imagefilledellipse(
        $this->image,
        $pointArray[0],
        $pointArray[1],
        $this->data_point_width,
        $this->data_point_width,
        $this->data_point_color
      );
    }
  }
  protected function generateXAxis() {
    imageline(
      $this->image,
      $this->x_axis_x1,
      $this->x_axis_y1,
      $this->x_axis_x2,
      $this->x_axis_y2,
      $this->x_axis_color
    );
  }
  protected function generateYAxis() {
    imageline(
      $this->image,
      $this->y_axis_x1,
      $this->y_axis_y1,
      $this->y_axis_x2,
      $this->y_axis_y2,
      $this->y_axis_color
    );
  }
  protected function generateBackgound() {
    imagefilledrectangle(
      $this->image,
      0,
      0,
      $this->width,
      $this->height,
      $this->background_color
    );
  }
  protected function generateTitle() {
    $highestElement =
      ($this->top_margin < $this->y_axis_y2)
        ? $this->top_margin
        : $this->y_axis_y2;
    $textVertPos = ($highestElement / 2) - (self::TITLE_CHAR_HEIGHT / 2);
    $titleLength = strlen($this->title_text);
    $title_x = 0;
    $title_y = 0;
    if (\hacklib_cast_as_boolean($this->bool_title_center)) {
      $title_x =
        ($this->width / 2) - (($titleLength * self::TITLE_CHAR_WIDTH) / 2);
      $title_y = $textVertPos;
    } else {
      if (\hacklib_cast_as_boolean($this->bool_title_left)) {
        $title_x = $this->y_axis_x1;
        $title_y = $textVertPos;
      } else {
        if (\hacklib_cast_as_boolean($this->bool_title_right)) {
          $title_x =
            $this->x_axis_x2 - ($titleLength * self::TITLE_CHAR_WIDTH);
          $title_y = $textVertPos;
        }
      }
    }
    imagestring(
      $this->image,
      2,
      $title_x,
      $title_y,
      $this->title_text,
      $this->title_color
    );
  }
  protected function calcTopMargin() {
    if (\hacklib_cast_as_boolean($this->bool_title)) {
      $this->top_margin =
        ($this->height * (self::X_AXIS_MARGIN_PERCENT / 100)) +
        self::TITLE_CHAR_HEIGHT;
    } else {
      $this->top_margin = $this->height * (self::X_AXIS_MARGIN_PERCENT / 100);
    }
  }
  protected function calcRightMargin() {
    $this->right_margin = $this->width * (self::Y_AXIS_MARGIN_PERCENT / 100);
  }
  protected function calcCoords() {
    $this->x_axis_x1 = $this->y_axis_margin;
    $this->x_axis_y1 = $this->height - $this->x_axis_margin;
    $this->x_axis_x2 = $this->width - $this->right_margin;
    $this->x_axis_y2 = $this->height - $this->x_axis_margin;
    $this->y_axis_x1 = $this->y_axis_margin;
    $this->y_axis_y1 = $this->height - $this->x_axis_margin;
    $this->y_axis_x2 = $this->y_axis_margin;
    $this->y_axis_y2 = $this->top_margin;
  }
  protected function determineAxisMarkerScale($max, $min, $axis = "y") {
    switch ($axis) {
      case "y":
        $space = $this->height;
        break;
      case "x":
        $space = $this->width;
        break;
    }
    if (\hacklib_cast_as_boolean($this->bool_user_data_range)) {
      $range = abs($max - $min);
    } else {
      $range =
        (abs($max - $min) > abs($max - 0)) ? abs($max - $min) : abs($max - 0);
    }
    if (\hacklib_equals($range, 0)) {
      $range = 10;
    }
    $count = 0;
    while (abs($range) < 100) {
      $range *= 10;
      $count++;
    }
    $divisor = round($space / self::RANGE_DIVISOR_FACTOR);
    $divided = round($range / $divisor);
    $result = $this->roundUpOneExtraDigit($divided);
    if (($result / $divided) >= 2) {
      $result = $this->roundUpSameDigits($divided);
    }
    for ($i = 0; $i < $count; $i++) {
      $result /= 10;
    }
    return $result;
  }
  protected function roundUpSameDigits($num) {
    $len = strlen($num);
    if (\hacklib_equals(round($num, (-1) * ($len - 1)), $num)) {
      return $num;
    } else {
      $firstDig = substr($num, 0, 1);
      $secondDig = substr($num, 1, 1);
      $rest = substr($num, 2);
      $secondDig = 5;
      $altered = $firstDig.$secondDig.$rest;
      return round((int) $altered, (-1) * ($len - 1));
    }
  }
  protected function roundUpOneExtraDigit($num) {
    $len = strlen($num);
    $firstDig = substr($num, 0, 1);
    $rest = substr($num, 1);
    $firstDig = 5;
    $altered = $firstDig.$rest;
    return round((int) $altered, (-1) * $len);
  }
  protected function displayErrors() {
    if (count($this->error) > 0) {
      $lineHeight = 12;
      $errorColor = imagecolorallocate($this->image, 0, 0, 0);
      $errorBackColor = imagecolorallocate($this->image, 255, 204, 0);
      imagefilledrectangle(
        $this->image,
        0,
        0,
        $this->width - 1,
        2 * $lineHeight,
        $errorBackColor
      );
      imagestring(
        $this->image,
        3,
        2,
        0,
        "!!----- PHPGraphLib Error -----!!",
        $errorColor
      );
      $key = 0;
      foreach ($this->error as $key => $errorText) {
        imagefilledrectangle(
          $this->image,
          0,
          ($key * $lineHeight) + $lineHeight,
          $this->width - 1,
          ($key * $lineHeight) + (2 * $lineHeight),
          $errorBackColor
        );
        imagestring(
          $this->image,
          2,
          2,
          ($key * $lineHeight) + $lineHeight,
          "[".($key + 1)."] ".$errorText,
          $errorColor
        );
      }
      $errorOutlineColor = imagecolorallocate($this->image, 255, 0, 0);
      imagerectangle(
        $this->image,
        0,
        0,
        $this->width - 1,
        ($key * $lineHeight) + (2 * $lineHeight),
        $errorOutlineColor
      );
    }
  }
  public function addData(
    $data,
    $data2 = "",
    $data3 = "",
    $data4 = "",
    $data5 = ""
  ) {
    $data_sets = array($data, $data2, $data3, $data4, $data5);
    foreach ($data_sets as $set) {
      if (\hacklib_cast_as_boolean(is_array($set))) {
        $this->data_array[] = $set;
      }
    }
    $low_x = 0;
    $high_x = 0;
    $force_set_x = 1;
    foreach ($this->data_array as $data_set_num => $data_set) {
      foreach ($data_set as $key => $item) {
        if (\hacklib_cast_as_boolean($force_set_x)) {
          $low_x = $key;
          $high_x = $key;
          $force_set_x = 0;
        }
        if ($key < $low_x) {
          $low_x = $key;
        }
        if ($key > $high_x) {
          $high_x = $key;
        }
        if (!\hacklib_cast_as_boolean(is_numeric($item))) {
          unset($this->data_array[$data_set_num][$key]);
          continue;
        }
        if ($item < $this->data_min) {
          $this->data_min = $item;
        }
        if ($item > $this->data_max) {
          $this->data_max = $item;
        }
      }
      $count = count($this->data_array[$data_set_num]);
      if ($count > $this->data_count) {
        $this->data_count = $count;
      }
    }
    $this->lowest_x = $low_x;
    $this->highest_x = $high_x;
    $raw_size = ($high_x - $low_x) + 1;
    if ($raw_size > $this->data_count) {
      $this->data_count = $raw_size;
    }
    $this->data_set_count = count($this->data_array);
    if (\hacklib_equals($this->data_set_count, 0)) {
      $this->error[] = "No valid datasets added in adddata() function.";
      return;
    }
    $this->bool_data = true;
  }
  protected function analyzeData() {
    if ($this->data_min >= 0) {
      $this->bool_all_positive = true;
    } else {
      if ($this->data_max <= 0) {
        $this->bool_all_negative = true;
      }
    }
    if (($this->data_min >= 0) && \hacklib_equals($this->data_max, 0)) {
      $this->data_min = 0;
      $this->data_max = 10;
      $this->all_zero_data = true;
    }
  }
  public function setupXAxis($percent = 0, $color = "") {
    $this->bool_x_axis_setup = true;
    if ((!isset($percent)) || \hacklib_equals($percent, false)) {
      $this->bool_x_axis = false;
    } else {
      $this->bool_x_axis = true;
    }
    if ((!((!isset($color)) || \hacklib_equals($color, false))) &&
        \hacklib_cast_as_boolean($arr = $this->returnColorArray($color))) {
      $this->x_axis_color =
        imagecolorallocate($this->image, $arr[0], $arr[1], $arr[2]);
    }
    if (\hacklib_cast_as_boolean(is_numeric($percent)) && ($percent > 0)) {
      $percent = $percent / 100;
      $this->x_axis_margin = round($this->height * $percent);
    } else {
      $percent = self::X_AXIS_MARGIN_PERCENT / 100;
      $this->x_axis_margin = round($this->height * $percent);
    }
  }
  public function setupYAxis($percent = 0, $color = "") {
    $this->bool_y_axis_setup = true;
    if ((!isset($percent)) || \hacklib_equals($percent, false)) {
      $this->bool_y_axis = false;
    } else {
      $this->bool_y_axis = true;
    }
    if ((!((!isset($color)) || \hacklib_equals($color, false))) &&
        \hacklib_cast_as_boolean($arr = $this->returnColorArray($color))) {
      $this->y_axis_color =
        imagecolorallocate($this->image, $arr[0], $arr[1], $arr[2]);
    }
    if (\hacklib_cast_as_boolean(is_numeric($percent)) && ($percent > 0)) {
      $this->y_axis_margin = round($this->width * ($percent / 100));
    } else {
      $percent = self::Y_AXIS_MARGIN_PERCENT / 100;
      $this->y_axis_margin = round($this->width * $percent);
    }
  }
  public function setRange($min, $max) {
    if ($min > $max) {
      $this->data_range_max = $min;
      $this->data_range_min = $max;
    } else {
      $this->data_range_max = $max;
      $this->data_range_min = $min;
    }
    $this->bool_user_data_range = true;
  }
  public $title_text = "";
  public function setTitle($title) {
    if (!((!isset($title)) || \hacklib_equals($title, false))) {
      $this->title_text = $title;
      $this->bool_title = true;
    } else {
      $this->error[] = "String arg for setTitle() not specified properly.";
    }
  }
  public function setTitleLocation($location) {
    $this->bool_title_left = false;
    $this->bool_title_right = false;
    $this->bool_title_center = false;
    switch (strtolower($location)) {
      case "left":
        $this->bool_title_left = true;
        break;
      case "right":
        $this->bool_title_right = true;
        break;
      case "center":
        $this->bool_title_center = true;
        break;
      default:
        $this->error[] =
          "String arg for setTitleLocation() not specified properly.";
    }
  }
  public function setBars($bool) {
    if (\hacklib_cast_as_boolean(is_bool($bool))) {
      $this->bool_bars = $bool;
    } else {
      $this->error[] = "Boolean arg for setBars() not specified properly.";
    }
  }
  public function setGrid($bool) {
    if (\hacklib_cast_as_boolean(is_bool($bool))) {
      $this->bool_grid = $bool;
    } else {
      $this->error[] = "Boolean arg for setGrid() not specified properly.";
    }
  }
  public function setXValues($bool) {
    if (\hacklib_cast_as_boolean(is_bool($bool))) {
      $this->bool_x_axis_values = $bool;
    } else {
      $this->error[] = "Boolean arg for setXValues() not specified properly.";
    }
  }
  public function setYValues($bool) {
    if (\hacklib_cast_as_boolean(is_bool($bool))) {
      $this->bool_y_axis_values = $bool;
    } else {
      $this->error[] = "Boolean arg for setYValues() not specified properly.";
    }
  }
  public function setXValuesHorizontal($bool) {
    if (\hacklib_cast_as_boolean(is_bool($bool))) {
      $this->bool_x_axis_values_vert = !\hacklib_cast_as_boolean($bool);
    } else {
      $this->error[] =
        "Boolean arg for setXValuesHorizontal() not specified properly.";
    }
  }
  public function setXValuesVertical($bool) {
    if (\hacklib_cast_as_boolean(is_bool($bool))) {
      $this->bool_x_axis_values_vert = $bool;
    } else {
      $this->error[] =
        "Boolean arg for setXValuesVertical() not specified properly.";
    }
  }
  public function setXValuesInterval($value) {
    if (\hacklib_cast_as_boolean(is_int($value)) && ($value > 0)) {
      $this->x_axis_value_interval = $value;
    } else {
      $this->error[] =
        "Value arg for setXValuesInterval() not specified properly.";
    }
  }
  public function setBarOutline($bool) {
    if (\hacklib_cast_as_boolean(is_bool($bool))) {
      $this->bool_bar_outline = $bool;
    } else {
      $this->error[] =
        "Boolean arg for setBarOutline() not specified properly.";
    }
  }
  public function setDataPoints($bool) {
    if (\hacklib_cast_as_boolean(is_bool($bool))) {
      $this->bool_data_points = $bool;
    } else {
      $this->error[] =
        "Boolean arg for setDataPoints() not specified properly.";
    }
  }
  public function setDataPointSize($size) {
    if (\hacklib_cast_as_boolean(is_numeric($size))) {
      $this->data_point_width = $size;
    } else {
      $this->error[] =
        "Data point size in setDataPointSize() not specified properly.";
    }
  }
  public function setDataValues($bool) {
    if (\hacklib_cast_as_boolean(is_bool($bool))) {
      $this->bool_data_values = $bool;
    } else {
      $this->error[] =
        "Boolean arg for setDataValues() not specified properly.";
    }
  }
  public function setDataCurrency($currency_type = "dollar") {
    switch (strtolower($currency_type)) {
      case "dollar":
        $this->data_currency = "\044";
        break;
      case "yen":
        $this->data_currency = "\302\245";
        break;
      case "pound":
        $this->data_currency = "\302\243";
        break;
      case "lira":
        $this->data_currency = "\302\243";
        break;
      default:
        $this->data_currency = $currency_type;
        break;
    }
    $this->data_additional_length += strlen($this->data_currency);
  }
  protected function applyDataCurrency($input) {
    return $this->data_currency.$input;
  }
  public function setDataFormat($format) {
    switch ($format) {
      case "comma":
        $this->data_format_array[] = "formatDataAsComma";
        $this->data_additional_length += floor(strlen($this->data_max) / 3);
        break;
      case "percent":
        $this->data_format_array[] = "formatDataAsPercent";
        $this->data_additional_length++;
        break;
      case "degrees":
        $this->data_format_array[] = "formatDataAsDegrees";
        $this->data_additional_length++;
        break;
      default:
        $this->data_format_array[] = "formatDataAsGeneric";
        $this->data_format_generic = $format;
        $this->data_additional_length += strlen($format);
        break;
    }
  }
  protected function applyDataFormats($input) {
    if (\hacklib_cast_as_boolean(
          $pos =
            array_search("formatDataAsComma", $this->data_format_array)
        )) {
      unset($this->data_format_array[$pos]);
      array_unshift($this->data_format_array, "formatDataAsComma");
    }
    foreach ($this->data_format_array as $format_type_callback) {
      eval("\044input=\044this->".$format_type_callback."(\044input);");
    }
    return $input;
  }
  protected function formatDataAsComma($input) {
    $sign_part = "";
    if (\hacklib_equals(substr($input, 0, 1), "-")) {
      $input = substr($input, 1);
      $sign_part = "-";
    }
    $decimal_part = "";
    if (($pos = strpos($input, ".")) !== false) {
      $decimal_part = substr($input, $pos);
      $input = substr($input, 0, $pos);
    }
    $parts = "";
    while (strlen($input) > 3) {
      $parts = ",".substr($input, -3).$parts;
      $input = substr($input, 0, strlen($input) - 3);
    }
    $currency_part = "";
    return $sign_part.$currency_part.$input.$parts.$decimal_part;
  }
  protected function formatDataAsPercent($input) {
    return $input."%";
  }
  protected function formatDataAsDegrees($input) {
    return $input."\302\260";
  }
  protected function formatDataAsGeneric($input) {
    return $input.$this->data_format_generic;
  }
  public function setLine($bool) {
    if (\hacklib_cast_as_boolean(is_bool($bool))) {
      $this->bool_line = $bool;
    } else {
      $this->error[] = "Boolean arg for setLine() not specified properly.";
    }
  }
  public function setGoalLine($yValue, $color = null, $style = "solid") {
    if (\hacklib_cast_as_boolean(is_numeric($yValue))) {
      if (\hacklib_cast_as_boolean($color)) {
        $this->setGenericColor(
          $color,
          "goal_line_custom_color",
          "Goal line color not specified properly."
        );
        $color = $this->goal_line_custom_color;
      }
      $this->goal_line_array[] = array(
        "yValue" => $yValue,
        "color" => $color,
        "style" => $style
      );
      if ($yValue > $this->data_max) {
        $this->data_range_max = $yValue;
        $this->bool_user_data_range = true;
      }
    } else {
      $this->error[] = "Goal line Y axis value not specified properly.";
    }
  }
  protected function allocateColors() {
    $this->background_color = imagecolorallocate($this->image, 255, 255, 255);
    $this->grid_color = imagecolorallocate($this->image, 220, 220, 220);
    $this->bar_color = imagecolorallocate($this->image, 200, 200, 200);
    $this->line_color_default =
      imagecolorallocate($this->image, 100, 100, 100);
    $this->x_axis_text_color = $this->line_color_default;
    $this->y_axis_text_color = $this->line_color_default;
    $this->data_value_color = $this->line_color_default;
    $this->title_color = imagecolorallocate($this->image, 0, 0, 0);
    $this->outline_color = $this->title_color;
    $this->data_point_color = $this->title_color;
    $this->x_axis_color = $this->title_color;
    $this->y_axis_color = $this->title_color;
    $this->goal_line_color = $this->title_color;
    $this->legend_outline_color = $this->grid_color;
    $this->legend_color = $this->background_color;
    $this->legend_text_color = $this->line_color_default;
    $this->legend_swatch_outline_color = $this->line_color_default;
  }
  protected function returnColorArray($color) {
    if (strpos($color, ",") !== false) {
      return explode(",", $color);
    } else {
      if (\hacklib_equals(substr($color, 0, 1), "#")) {
        if (\hacklib_equals(strlen($color), 7)) {
          $hex1 = hexdec(substr($color, 1, 2));
          $hex2 = hexdec(substr($color, 3, 2));
          $hex3 = hexdec(substr($color, 5, 2));
          return array($hex1, $hex2, $hex3);
        } else {
          if (\hacklib_equals(strlen($color), 4)) {
            $hex1 = hexdec(substr($color, 1, 1).substr($color, 1, 1));
            $hex2 = hexdec(substr($color, 2, 1).substr($color, 2, 1));
            $hex3 = hexdec(substr($color, 3, 1).substr($color, 3, 1));
            return array($hex1, $hex2, $hex3);
          }
        }
      }
    }
    switch (strtolower($color)) {
      case "black":
        return array(0, 0, 0);
        break;
      case "silver":
        return array(192, 192, 192);
        break;
      case "gray":
        return array(128, 128, 128);
        break;
      case "white":
        return array(255, 255, 255);
        break;
      case "maroon":
        return array(128, 0, 0);
        break;
      case "red":
        return array(255, 0, 0);
        break;
      case "purple":
        return array(128, 0, 128);
        break;
      case "fuscia":
        return array(255, 0, 255);
        break;
      case "green":
        return array(0, 128, 0);
        break;
      case "lime":
        return array(0, 255, 0);
        break;
      case "olive":
        return array(128, 128, 0);
        break;
      case "yellow":
        return array(255, 255, 0);
        break;
      case "navy":
        return array(0, 0, 128);
        break;
      case "blue":
        return array(0, 0, 255);
        break;
      case "teal":
        return array(0, 128, 128);
        break;
      case "aqua":
        return array(0, 255, 255);
        break;
    }
    $this->error[] = "Color name \"".$color."\" not recogized.";
    return false;
  }
  protected function allocateGradientColors(
    $color1R,
    $color1G,
    $color1B,
    $rScale,
    $gScale,
    $bScale,
    $num,
    $data_set_num
  ) {
    $this->gradient_color_array[$data_set_num] = array();
    for ($i = 0; $i <= ($num + 1); $i++) {
      $this->gradient_color_array[$data_set_num][$i] = imagecolorallocate(
        $this->image,
        $color1R - ($rScale * $i),
        $color1G - ($gScale * $i),
        $color1B - ($bScale * $i)
      );
    }
    $this->bool_gradient_colors_found[$data_set_num] = true;
  }
  protected function setGenericColor($inputColor, $var, $errorMsg) {
    if ((!((!isset($inputColor)) || \hacklib_equals($inputColor, false))) &&
        \hacklib_cast_as_boolean(
          $arr = $this->returnColorArray($inputColor)
        )) {
      if (!\hacklib_cast_as_boolean(property_exists($this, $var))) {
        $msg = sprintf("Errore %s not found in %s ", $var, __CLASS__);
        throw new \Exception($msg);
      }
      $color = imagecolorallocate($this->image, $arr[0], $arr[1], $arr[2]);
      if (\hacklib_cast_as_boolean(is_array($this->$var))) {
        $this->$var[] = $color;
      } else {
        $this->$var = $color;
      }
      return true;
    } else {
      $this->error[] = $errorMsg;
      return false;
    }
  }
  public function setBackgroundColor($color) {
    if (\hacklib_cast_as_boolean(
          $this->setGenericColor(
            $color,
            "background_color",
            "Background color not specified properly."
          )
        )) {
      $this->bool_background = true;
    }
  }
  public function setTitleColor($color) {
    $this->setGenericColor(
      $color,
      "title_color",
      "Title color not specified properly."
    );
  }
  public function setTextColor($color) {
    $this->setGenericColor(
      $color,
      "x_axis_text_color",
      "X axis text color not specified properly."
    );
    $this->setGenericColor(
      $color,
      "y_axis_text_color",
      "Y axis Text color not specified properly."
    );
  }
  public function setXAxisTextColor($color) {
    $this->setGenericColor(
      $color,
      "x_axis_text_color",
      "X axis text color not specified properly."
    );
  }
  public function setYAxisTextColor($color) {
    $this->setGenericColor(
      $color,
      "y_axis_text_color",
      "Y axis Text color not specified properly."
    );
  }
  public function setBarColor(
    $color1,
    $color2 = "",
    $color3 = "",
    $color4 = "",
    $color5 = ""
  ) {
    $bar_colors = array($color1, $color2, $color3, $color4, $color5);
    foreach ($bar_colors as $key => $color) {
      if (\hacklib_cast_as_boolean($color)) {
        $this->setGenericColor(
          $color,
          "multi_bar_colors",
          "Bar color ".($key + 1)." not specified properly."
        );
      }
    }
  }
  public function setGridColor($color) {
    $this->setGenericColor(
      $color,
      "grid_color",
      "Grid color not specified properly."
    );
  }
  public function setBarOutlineColor($color) {
    $this->setGenericColor(
      $color,
      "outline_color",
      "Bar outline color not specified properly."
    );
  }
  public function setDataPointColor($color) {
    $this->setGenericColor(
      $color,
      "data_point_color",
      "Data point color not specified properly."
    );
  }
  public function setDataValueColor($color) {
    $this->setGenericColor(
      $color,
      "data_value_color",
      "Data value color not specified properly."
    );
  }
  public function setLineColor(
    $color1,
    $color2 = "",
    $color3 = "",
    $color4 = "",
    $color5 = ""
  ) {
    $line_colors = array($color1, $color2, $color3, $color4, $color5);
    foreach ($line_colors as $key => $color) {
      if (\hacklib_cast_as_boolean($color)) {
        $this->setGenericColor(
          $color,
          "line_color",
          "Line color ".($key + 1)." not specified properly."
        );
      }
    }
  }
  public function setGoalLineColor($color) {
    $this->setGenericColor(
      $color,
      "goal_line_color",
      "Goal line color not specified properly."
    );
  }
  public function setGradient(
    $color1,
    $color2,
    $color3 = "",
    $color4 = "",
    $color5 = "",
    $color6 = "",
    $color7 = "",
    $color8 = "",
    $color9 = "",
    $color10 = ""
  ) {
    if ((!((!isset($color1)) || \hacklib_equals($color1, false))) &&
        (!((!isset($color2)) || \hacklib_equals($color2, false))) &&
        \hacklib_cast_as_boolean($arr1 = $this->returnColorArray($color1)) &&
        \hacklib_cast_as_boolean($arr2 = $this->returnColorArray($color2))) {
      $this->bool_gradient = true;
      $this->multi_gradient_colors_1[] = $arr1;
      $this->multi_gradient_colors_2[] = $arr2;
    } else {
      $this->error[] = "Gradient color(s) not specified properly.";
    }
    if ((!((!isset($color3)) || \hacklib_equals($color3, false))) &&
        (!((!isset($color4)) || \hacklib_equals($color4, false))) &&
        \hacklib_cast_as_boolean($arr1 = $this->returnColorArray($color3)) &&
        \hacklib_cast_as_boolean($arr2 = $this->returnColorArray($color4))) {
      $this->bool_gradient = true;
      $this->multi_gradient_colors_1[] = $arr1;
      $this->multi_gradient_colors_2[] = $arr2;
    }
    if ((!((!isset($color5)) || \hacklib_equals($color5, false))) &&
        (!((!isset($color6)) || \hacklib_equals($color6, false))) &&
        \hacklib_cast_as_boolean($arr1 = $this->returnColorArray($color5)) &&
        \hacklib_cast_as_boolean($arr2 = $this->returnColorArray($color6))) {
      $this->bool_gradient = true;
      $this->multi_gradient_colors_1[] = $arr1;
      $this->multi_gradient_colors_2[] = $arr2;
    }
    if ((!((!isset($color7)) || \hacklib_equals($color7, false))) &&
        (!((!isset($color8)) || \hacklib_equals($color8, false))) &&
        \hacklib_cast_as_boolean($arr1 = $this->returnColorArray($color7)) &&
        \hacklib_cast_as_boolean($arr2 = $this->returnColorArray($color8))) {
      $this->bool_gradient = true;
      $this->multi_gradient_colors_1[] = $arr1;
      $this->multi_gradient_colors_2[] = $arr2;
    }
    if ((!((!isset($color9)) || \hacklib_equals($color9, false))) &&
        (!((!isset($color10)) || \hacklib_equals($color10, false))) &&
        \hacklib_cast_as_boolean($arr1 = $this->returnColorArray($color9)) &&
        \hacklib_cast_as_boolean($arr2 = $this->returnColorArray($color10))) {
      $this->bool_gradient = true;
      $this->multi_gradient_colors_1[] = $arr1;
      $this->multi_gradient_colors_2[] = $arr2;
    }
  }
  public function setLegend($bool) {
    if (\hacklib_cast_as_boolean(is_bool($bool))) {
      $this->bool_legend = $bool;
    } else {
      $this->error[] = "Boolean arg for setLegend() not specified properly.";
    }
  }
  public function setLegendColor($color) {
    $this->setGenericColor(
      $color,
      "legend_color",
      "Legend color not specified properly."
    );
  }
  public function setLegendTextColor($color) {
    $this->setGenericColor(
      $color,
      "legend_text_color",
      "Legend text color not specified properly."
    );
  }
  public function setLegendOutlineColor($color) {
    $this->setGenericColor(
      $color,
      "legend_outline_color",
      "Legend outline color not specified properly."
    );
  }
  public function setSwatchOutlineColor($color) {
    $this->setGenericColor(
      $color,
      "legend_swatch_outline_color",
      "Swatch outline color not specified properly."
    );
  }
  public function setLegendTitle(
    $title1,
    $title2 = "",
    $title3 = "",
    $title4 = "",
    $title5 = ""
  ) {
    $title_array = array($title1, $title2, $title3, $title4, $title5);
    foreach ($title_array as $title) {
      if (\hacklib_cast_as_boolean($len = strlen($title))) {
        if ($len > self::LEGEND_MAX_CHARS) {
          $title = substr($title, 0, self::LEGEND_MAX_CHARS);
          $this->legend_total_chars[] = self::LEGEND_MAX_CHARS;
        } else {
          $this->legend_total_chars[] = $len;
        }
        $this->legend_titles[] = $title;
      }
    }
  }
  protected function generateLegend() {
    $swatchToTextOffset = (self::LEGEND_TEXT_HEIGHT - 6) / 2;
    $swatchSize = self::LEGEND_TEXT_HEIGHT - (2 * $swatchToTextOffset);
    $this->legend_height =
      self::LEGEND_TEXT_HEIGHT + (2 * self::LEGEND_PADDING);
    $totalChars = 0;
    for ($i = 0; $i < $this->data_set_count; $i++) {
      if (\hacklib_cast_as_boolean(isset($this->legend_total_chars[$i]))) {
        $totalChars += $this->legend_total_chars[$i];
      }
    }
    $this->legend_width =
      ($totalChars * self::LEGEND_TEXT_WIDTH) +
      (self::LEGEND_PADDING * 2.2) +
      ($this->data_set_count * ($swatchSize + (self::LEGEND_PADDING * 2)));
    $this->legend_x = $this->x_axis_x2 - $this->legend_width;
    $highestElement =
      ($this->top_margin < $this->y_axis_y2)
        ? $this->top_margin
        : $this->y_axis_y2;
    $this->legend_y = ($highestElement / 2) - ($this->legend_height / 2);
    imagefilledrectangle(
      $this->image,
      $this->legend_x,
      $this->legend_y,
      $this->legend_x + $this->legend_width,
      $this->legend_y + $this->legend_height,
      $this->legend_color
    );
    imagerectangle(
      $this->image,
      $this->legend_x,
      $this->legend_y,
      $this->legend_x + $this->legend_width,
      $this->legend_y + $this->legend_height,
      $this->legend_outline_color
    );
    $length_covered = 0;
    for ($i = 0; $i < $this->data_set_count; $i++) {
      $data_label = "";
      if (\hacklib_cast_as_boolean(isset($this->legend_titles[$i]))) {
        $data_label = $this->legend_titles[$i];
      }
      $yValue = $this->legend_y + self::LEGEND_PADDING;
      $xValue =
        $this->legend_x +
        self::LEGEND_PADDING +
        ($length_covered * self::LEGEND_TEXT_WIDTH) +
        ($i * 4 * self::LEGEND_PADDING);
      $length_covered += strlen($data_label);
      $color = 0;
      if (\hacklib_cast_as_boolean($this->bool_bars)) {
        if (\hacklib_cast_as_boolean($this->bool_gradient)) {
          $color = $this->gradient_color_array[($this->data_set_count - $i) -
          1][0];
        } else {
          $color = $this->multi_bar_colors[($this->data_set_count - $i) - 1];
        }
      } else {
        if (\hacklib_cast_as_boolean($this->bool_line) &&
            (!\hacklib_cast_as_boolean($this->bool_bars))) {
          $color = $this->line_color[($this->data_set_count - $i) - 1];
        }
      }
      imagefilledrectangle(
        $this->image,
        $xValue,
        $yValue + $swatchToTextOffset,
        $xValue + $swatchSize,
        $yValue + $swatchToTextOffset + $swatchSize,
        $color
      );
      imagerectangle(
        $this->image,
        $xValue,
        $yValue + $swatchToTextOffset,
        $xValue + $swatchSize,
        $yValue + $swatchToTextOffset + $swatchSize,
        $this->legend_swatch_outline_color
      );
      imagestring(
        $this->image,
        2,
        $xValue + (2 * self::LEGEND_PADDING) + 2,
        $yValue,
        $data_label,
        $this->legend_text_color
      );
    }
  }
  public function setIgnoreDataFitErrors($bool) {
    if (\hacklib_cast_as_boolean(is_bool($bool))) {
      $this->bool_ignore_data_fit_errors = $bool;
    } else {
      $this->error[] =
        "Boolean arg for setIgnoreDataFitErrors() not specified properly.";
    }
  }
}
