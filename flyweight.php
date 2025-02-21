<?php
header("Content-Type: application/json");

interface TreeFlyweight {
    public function getType();
}

class TreeType implements TreeFlyweight {
    private string $name;
    private string $color;

    public function __construct($name, $color) {
        $this->name = $name;
        $this->color = $color;
    }

    public function getType() {
        return ["name" => $this->name, "color" => $this->color];
    }
}

class TreeFactory {
    private static array $treeTypes = [];

    public static function getTreeType($name, $color): TreeFlyweight {
        $key = md5($name . $color);
        if (!isset(self::$treeTypes[$key])) {
            self::$treeTypes[$key] = new TreeType($name, $color);
        }
        return self::$treeTypes[$key];
    }
}

class Tree {
    private int $x;
    private int $y;
    private TreeFlyweight $treeType;

    public function __construct($x, $y, TreeFlyweight $treeType) {
        $this->x = $x;
        $this->y = $y;
        $this->treeType = $treeType;
    }

    public function toArray() {
        return [
            "x" => $this->x,
            "y" => $this->y,
            "type" => $this->treeType->getType()
        ];
    }
}

class Forest {
    private array $trees = [];

    public function plantTree($x, $y, $name, $color) {
        $treeType = TreeFactory::getTreeType($name, $color);
        $this->trees[] = new Tree($x, $y, $treeType);
    }

    public function getTrees() {
        return array_map(fn($tree) => $tree->toArray(), $this->trees);
    }
}

// Vytvoření lesa s náhodně rozmístěnými stromy
$forest = new Forest();
$types = [
    ["Dub", "#228B22"],
    ["Borovice", "#2E8B57"],
    ["Bříza", "#D2B48C"]
];

for ($i = 0; $i < 50; $i++) {
    $type = $types[array_rand($types)];
    $forest->plantTree(rand(10, 490), rand(10, 290), $type[0], $type[1]);
}

echo json_encode(["trees" => $forest->getTrees()]);
?>
