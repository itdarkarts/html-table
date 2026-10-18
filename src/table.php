<?php
namespace ITDarkArts\HTML\Table;
require "row.php";

/** API for building tables and exporting to HTML. */
final class table {
    private array $attributes = [];
    private string $section = "";
    private \stdClass $caption;
    private \stdClass $thead;
    private \stdClass $tbody;
    private \stdClass $tfoot;

    public function __construct(
        string $class = "",
        string $id = "",
        array $table_attributes = [],
        string $caption = "",
        array $caption_attributes = []
    ) {
        $this->attributes = $this->set_attributes($class, $id, $table_attributes);

        $this->caption = new \stdClass();
        $this->caption->caption = $caption;
        $this->caption->attributes = $caption_attributes;

        foreach(["thead", "tbody", "tfoot"] as $section) {
            $this->$section = new \stdClass();
            $this->$section->attributes = [];
            $this->$section->rows = [];
            $this->$section->index = -1;
        }

        $this->section = "thead";
    }

    /**
     * Set attributes for `<thead>`.  New rows are created within `<thead>`.
     *
     * @param array $attributes (optional) `<thead>` tag attributes in an array of `[$attribute => $value, etc.]`
     */
    public function thead(string $class = "", string $id = "", array $attributes = []): void {
        $this->section = "thead";
        $this->thead->attributes = $this->set_attributes($class, $id, $attributes);
    }

    /**
     * Set attributes for `<tbody>`.  New rows are created within `<tbody>`.
     *
     * @param array $attributes (optional) `<tbody>` tag attributes in an array of `[$attribute => $value, etc.]`
     */
    public function tbody(string $class = "", string $id = "", array $attributes = []): void {
        $this->section = "tbody";
        $this->tbody->attributes = $this->set_attributes($class, $id, $attributes);
    }

    /**
     * Set attributes for `<tfoot>`.  New rows are created within `<tfoot>`.
     *
     * @param array $attributes `<tfoot>` (optional) tag attributes in an array of `[$attribute => $value, etc.]`
     */
    public function tfoot(string $class = "", string $id = "", array $attributes = []): void {
        $this->section = "tfoot";
        $this->tfoot->attributes = $this->set_attributes($class, $id, $attributes);
    }

    /**
     * Create a new data row within the previously set `<thead>`, `<tbody>`, or `<tfoot>`.
     *
     * @param array $attributes (optional) `<tr>` tag attributes in an array of `[$attribute => $value, etc.]`
     * @param array $datapoints (optional) row data.
     * @param string $col_tag (optional) `th` or `td`.  Default is based on `thead` (`th`), `tbody` (`td`), `tfoot` (`td`).
     */
    public function row(array $datapoints = [], array $attributes = [], string $col_tag = ""): void {
        // What section are we on (thead, tbody, or tfoot)?
        $section = $this->section;
        $this->$section->index++;
        if ($col_tag !== "td" && $col_tag !== "th")
            $col_tag = $this->default_col_tag();

        // Create row
        $this->$section->rows[] = new row(datapoints: $datapoints, row_attributes: $attributes, cols_tag: $col_tag);
    }

    /**
     * Add a new *singular* datapoint column to the most recently created row.  q.v. `$this->add_cols()`
     *
     * @param string $datapoint singular datapoint to append to most recently added row.
     * @param array $attributes (optional) `<tr>` or `<th>` tag attributes in an array of `[$attribute => $value, etc.]`
     * @param string $tag (optional) `th` or `td`.  Default is based on `thead` (`th`), `tbody` (`td`), `tfoot` (`td`).
     */
    public function col(string $datapoint, array $attributes = [], $tag = ""): void {
        // What section are we on (thead, tbody, or tfoot)?
        $section = $this->section;
        if ($tag !== "th" && $tag !== "td")
            $tag = $this->default_col_tag();

        // We need the current row index.
        $index = $this->$section->index;

        // Add a col to the current row.
        $this->$section->rows[$index]->col(datapoint: $datapoint, attributes: $attributes, tag: $tag);
    }

    /** Get HTML block of the table */
    public function html(): string {
        $table_attributes = $this->html_attributes($this->attributes);
        $caption = $this->html_caption();
        $sections = "";

        foreach (["thead", "tbody", "tfoot"] as $section) {
            if ($this->$section->index >= 0) {
                $rows = "";
                foreach($this->$section->rows as $row)
                    $rows .= $row->html();

                $section_attributes = $this->html_attributes($this->$section->attributes);
                $sections .= "<{$section}{$section_attributes}>\n";
                $sections .= $rows;
                $sections .= "</{$section}>\n";
            }
        }

        $html = "<table{$table_attributes}>\n";
        $html .= $caption;
        $html .= $sections;
        $html .= "</table>\n";
        return $html;
    }

    /**
     * Helper method to determine the default column tag by section.
     *
     * `thead` -> `th` / `tbody` or `tfoot` -> `td`
     *
     * @return string `th` or `td`
     */
    private function default_col_tag(): string {
        if ($this->section === "thead")
            return "th";

        return "td";
    }

    /** Helper method to build attributes array */
    private function set_attributes(string $class, string $id, array $other_attributes): array {
        $attributes = [];
        if ($class !== "") $attributes['class'] = $class;
        if ($id !== "") $attributes['id'] = $id;
        return array_merge($attributes, $other_attributes);
    }

    /** Helper method for $this->html() */
    private function html_attributes(array $attributes): string {
        $html = "";
        foreach ($attributes as $attribute => $value)
            $html .= " {$attribute}='{$value}'";

        return $html;
    }

    /** Helper method for $this->html() */
    private function html_caption(): string {
        $attributes = $this->html_attributes($this->caption->attributes);
        $caption = $this->caption->caption;
        return "<caption{$attributes}>{$caption}</caption>\n";
    }
}

// EOF
