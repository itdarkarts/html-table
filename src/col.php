<?php
namespace ITDarkArts\HTML\Table;

/** API for building a table data cell and export to HTML. */
final class col {
    private array $attributes = [];
    private string $tag = "";
    private string $datapoint = "";

    public function __construct(
        string $datapoint,
        string $class = "",
        string $id = "",
        array $attributes = [],
        string $tag = "td"
    ) {
        if ($class !== "") $this->attributes['class'] = $class;
        if ($id !== "") $this->attributes['id'] = $id;
        $this->attributes = array_merge($this->attributes, $attributes);

        if ($tag !== "td" && $tag !== "th") $tag = "td";
        $this->tag = $tag;

        $this->datapoint = $datapoint;
    }

    public function html(): string {
        $attributes = "";
        foreach ($this->attributes as $attribute => $value)
            $attributes .= " {$attribute}='{$value}'";

        return "<{$this->tag}{$attributes}>{$this->datapoint}</{$this->tag}>\n";
    }
}

// EOF
