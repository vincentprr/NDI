<?php
namespace Models;

use BubbleORM\Attributes\Key;
use BubbleORM\Attributes\Name;
use BubbleORM\Attributes\Table;
use BubbleORM\Attributes\Unsigned;

#[Table("news")]
class News{
    #[Key, Unsigned, Name("Id")]
    public int $id;

    public function __construct(
        #[Name("Title")] public string $title,
        #[Name("Description")] public string $description,
        #[Name("Image")] public mixed $image,
        #[Name("Date")] public string $date
    ) {}
}