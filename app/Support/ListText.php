<?php

namespace App\Support;

/**
 * Converts structured lists to and from the plain-text format used in the
 * admin forms: one item per line, and a line ending in ":" starts a new
 * titled group.
 */
class ListText
{
    /**
     * @param  list<array{heading: string, items: list<string>}>|null  $groups
     */
    public static function groupsToText(?array $groups): string
    {
        return collect($groups ?? [])
            ->map(fn (array $group): string => trim(($group['heading'] !== '' ? $group['heading'].":\n" : '').implode("\n", $group['items'])))
            ->implode("\n\n");
    }

    /**
     * @return list<array{heading: string, items: list<string>}>
     */
    public static function textToGroups(?string $text): array
    {
        $groups = [];
        $current = null;

        foreach (self::lines($text) as $line) {
            if (str_ends_with($line, ':')) {
                if ($current !== null) {
                    $groups[] = $current;
                }
                $current = ['heading' => rtrim($line, ': '), 'items' => []];

                continue;
            }

            $current ??= ['heading' => '', 'items' => []];
            $current['items'][] = ltrim($line, '-•* ');
        }

        if ($current !== null) {
            $groups[] = $current;
        }

        return array_values(array_filter($groups, fn (array $group): bool => $group['items'] !== []));
    }

    /**
     * @param  list<string>|null  $items
     */
    public static function itemsToText(?array $items): string
    {
        return implode("\n", $items ?? []);
    }

    /**
     * @return list<string>
     */
    public static function textToItems(?string $text): array
    {
        return array_map(fn (string $line): string => ltrim($line, '-•* '), self::lines($text));
    }

    /**
     * @return list<string>
     */
    private static function lines(?string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $text) ?: []), 'strlen'));
    }
}
