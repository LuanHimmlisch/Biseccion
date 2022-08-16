<?php

namespace LuanHimmlisch\Biseccion;

use Exception;

class Biseccion
{
    protected ?float $result_xi;
    protected ?float $result_xu;
    protected ?float $result_xr;
    protected ?float $xr;
    protected ?array $export;

    protected const COLUMNS = [
        'Xi',
        'Xu',
        'Xr',
        'Result'
    ];
    protected const MAX_ITERATIONS = 100;
    protected const NAPIER = 2.7182818284590;

    public function __construct(
        public float $xi = 0,
        public float $xu = 0,
        public string $function = '',
    ) {
    }

    public static function make(): static
    {
        return new static;
    }

    public function setXi(float $val): self
    {
        $this->xi = $val;
        return $this;
    }

    public function setXu(float $val): self
    {
        $this->xu = $val;
        return $this;
    }

    public function setFunction(string $val): self
    {
        $this->function = $val;
        return $this;
    }

    public function execute()
    {
        $result = null;
        $iteration = 0;
        $export = [];

        do {
            $data = array_fill(0, count(self::COLUMNS), null);

            // Step one
            if (is_null($result)) {
                $this->result_xi = math_eval($this->function, [
                    'x' => $this->xi,
                    'e' => self::NAPIER
                ]);

                $this->result_xu = math_eval($this->function, [
                    'x' => $this->xu,
                    'e' => self::NAPIER
                ]);

                if ($this->result_xi * $this->result_xu >= 0) {
                    break;
                }
            }

            $data[array_search('Xi', self::COLUMNS)] = $this->xi;
            $data[array_search('Xu', self::COLUMNS)] = $this->xu;

            // Step two
            $data[array_search('Xr', self::COLUMNS)] = $this->xr = ($this->xi + $this->xu) / 2;

            $this->result_xr = math_eval($this->function, [
                'x' => $this->xr,
                'e' => self::NAPIER
            ]);

            $data[array_search('Result', self::COLUMNS)] = $result = $this->result_xi * $this->result_xr;
            // Step three
            if ($result < 0) {
                $this->xu = $this->xr;
            } elseif ($result > 0) {
                $this->xi = $this->xr;
            }

            $export[] = $data;
            $iteration = $iteration + 1;
            echo "Iteration $iteration done!\nResult: $result \n\n";
        } while ($result != 0 && $iteration < self::MAX_ITERATIONS);

        $this->export = $export;

        return $this;
    }

    public function export($path = '.')
    {
        if (!isset($this->export)) {
            throw new Exception("Use the 'execute' function first!", 1);
        }

        array_unshift($this->export, self::COLUMNS);
        $filename = readline('Filename [example.csv]: ');
        $filename = empty($filename) ? 'example.csv' : $filename;
        $filename = str_ends_with($filename, '.csv') ? $filename : $filename . '.csv';
        $fp = fopen($path . "/$filename", "w");

        foreach ($this->export as $index => $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
    }

    public function toArray()
    {
        return $this->export ?? [];
    }
}
