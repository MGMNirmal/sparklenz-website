<?php

namespace App\Support;

use App\Models\Project;
use App\Models\ProjectMedia;
use Illuminate\Support\Collection;

/**
 * Arranges portfolio images into the columns of the animated project wall
 * on the home page. Each column holds every tile in a rotated order, so
 * columns never line up, and is padded to a minimum length so its looping
 * copy is always taller than the visible window.
 */
class ProjectWall
{
    /**
     * Aspect ratios cycled through to give the columns varied heights.
     */
    private const SHAPES = ['tall', 'square', 'wide', 'portrait', 'square', 'tall', 'wide'];

    /**
     * @param  Collection<int, Project>  $projects
     * @return list<list<array{slug: string, title: string, category: string, src: ?string, alt: string, shape: string, design: bool}>>
     */
    public static function columns(Collection $projects, int $columnCount = 5, int $minimumPerColumn = 7): array
    {
        $tiles = self::tiles($projects);

        if ($tiles === []) {
            return [];
        }

        $columns = [];
        $tileCount = count($tiles);
        $step = $tileCount % 2 === 1 ? 2 : 1;

        for ($column = 0; $column < $columnCount; $column++) {
            $rotation = ($column * $step) % $tileCount;
            $ordered = array_merge(array_slice($tiles, $rotation), array_slice($tiles, 0, $rotation));

            $items = [];
            for ($i = 0; count($items) < max($minimumPerColumn, count($tiles)); $i++) {
                $items[] = $ordered[$i % count($ordered)] + [
                    'shape' => self::SHAPES[($i + $column * 3) % count(self::SHAPES)],
                ];
            }

            $columns[] = $items;
        }

        return $columns;
    }

    /**
     * One tile per cover (or design artwork) and per gallery image.
     *
     * @param  Collection<int, Project>  $projects
     * @return list<array{slug: string, title: string, category: string, src: ?string, alt: string, design: bool}>
     */
    private static function tiles(Collection $projects): array
    {
        return $projects->flatMap(function (Project $project): array {
            $base = ['slug' => $project->slug, 'title' => $project->title, 'category' => $project->category];
            $tiles = [$base + [
                'src' => $project->coverThumbUrl(),
                'alt' => (string) ($project->cover_alt ?: $project->title),
                'design' => $project->coverThumbUrl() === null,
            ]];

            foreach ($project->media as $item) {
                /** @var ProjectMedia $item */
                $tiles[] = $base + ['src' => $item->url(), 'alt' => $item->alt, 'design' => false];
            }

            return $tiles;
        })->values()->all();
    }
}
