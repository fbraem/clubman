<?php

namespace Domain\News;

use League\Fractal;

class NewsStoryTransformer extends Fractal\TransformerAbstract
{
    private $filesystem;

    private static $type = 'stories';

    public static function createForItem(NewsStory $story, $filesystem)
    {
        return new Fractal\Resource\Item($story, new self($filesystem), self::$type);
    }

    public static function createForCollection(iterable $stories, $filesystem)
    {
        return new Fractal\Resource\Collection($stories, new self($filesystem), self::$type);
    }

    protected $defaultIncludes = [
        'contents',
        'category'
    ];

    public function __construct($filesystem = null)
    {
        $this->filesystem = $filesystem;
    }

    public function transform(NewsStory $story)
    {
        $result = $story->toArray();
        unset($result['_matchingData']);

        if ($this->filesystem) {
            $images = $this->filesystem->listContents('images/news/' . $story->id);
            if (count($images) > 0) {
                $result['images'] = [];
                foreach ($images as $image) {
                    $result['images'][$image['filename']] = '/files/' . $image['path'];
                }
            }
        }

        return $result;
    }

    public function includeContents(NewsStory $story)
    {
        $contents = $story->contents;
        if ($contents) {
            return \Domain\Content\ContentTransformer::createForCollection($contents);
        }
    }

    public function includeCategory(NewsStory $story)
    {
        $category = $story->category;
        if ($category) {
            return \Domain\Category\CategoryTransformer::createForItem($category, $this->filesystem);
        }
    }
}
