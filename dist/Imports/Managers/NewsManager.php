<?php
namespace Managers;

use BubbleORM\DatabaseAccessor;
use Models\News;

class NewsManager{
    public static function createNews(string $title, string $description, string $filePath) : News{
        return new News($title, $description, fopen($filePath, 'rb'), date("Y-m-d"));
    }

    public static function getAllNews(DatabaseAccessor $db) : mixed{
        return $db->createQuery(News::class)
                    ->orderBy(fn($x) => $x->date)
                    ->all();
    }
}