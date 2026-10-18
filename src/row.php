<?php
namespace ITDarkArts\HTML\Table;
require "col.php";

/** API for building a table row and export to HTML; */
final class row {
    private array $cols = [];
    private array $attributes = [];

    public function __construct(
        array $datapoints,
        string $class = "",
        string $id = "",
        array $row_attributes = [],
        array $cols_attributes = [],
        string $cols_tag = "td"
    ) {
        if ($class !== "") $this->attributes['class'] = $class;
        if ($id !== "") $this->attributes['id'] = $id;
        $this->attributes = array_merge($this->attributes, $row_attributes);

        foreach ($datapoints as $datapoint)
            $this->col(datapoint: $datapoint, attributes: $cols_attributes, tag: $cols_tag);
    }

    public function col(string $datapoint, array $attributes = [], $tag = "td"): void {
        $this->cols[] = new col(datapoint: $datapoint, attributes: $attributes, tag: $tag);
    }

    public function html(): string {
        $attributes = "";
        foreach($this->attributes as $attribute => $value)
            $attributes .= " {$attribute}='{$value}'";

        $cols = "";
        foreach($this->cols as $col)
            $cols .= $col->html();

        $html = "<tr{$attributes}>\n";
        $html .= $cols;
        $html .= "</tr>\n";
        return $html;
    }
}

// EOF
