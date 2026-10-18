<?php

namespace App\Support;

/**
 * One shared rule for "do these two names belong to the same person?".
 *
 * Ignored: capital letters, punctuation, extra spaces, titles (Mr., Mrs., Dr. ...)
 * and word order.
 *
 * Two names match when:
 *   - the shorter name is fully contained in the longer one,
 *   - they share at least two words, and
 *   - they agree on any Snr / Jnr style suffix (a father and son are never merged).
 *
 *   "TERRY ABEIKU"            matches  "ABEIKU TERRY"
 *   "MICHAEL NII OKAI  ADDY"  matches  "MR. MICHAEL NII OKAI ADDY"
 *   "PHYLLIS GYAMERA GYIMAH"  matches  "PHYLLIS GYAMERA GYIMAH / 0244254114"
 *   "JOHN MENSAH"             does NOT match "JOHN ASARE"
 *   "KOFI MENSAH"             does NOT match "KOFI MENSAH JNR"
 */
class NameMatcher
{
    private const TITLES = ['mr', 'mrs', 'ms', 'miss', 'dr', 'prof', 'rev', 'hon', 'madam', 'mdm', 'chief', 'sir'];

    private const SUFFIXES = ['jr', 'jnr', 'junior', 'sr', 'snr', 'senior', 'ii', 'iii'];

    public static function matches(?string $a, ?string $b): bool
    {
        [$wordsA, $suffixesA] = self::split($a);
        [$wordsB, $suffixesB] = self::split($b);

        if (empty($wordsA) || empty($wordsB)) {
            return false;
        }

        sort($suffixesA);
        sort($suffixesB);

        if ($suffixesA !== $suffixesB) {
            return false;
        }

        $common = count(array_intersect($wordsA, $wordsB));

        return $common >= 2 && $common === min(count($wordsA), count($wordsB));
    }

    /**
     * Returns [name words, suffix words].
     */
    private static function split(?string $name): array
    {
        $name = mb_strtolower((string) $name);
        $name = preg_replace('/[^a-z0-9\s]/u', ' ', $name);

        $tokens = array_filter(
            preg_split('/\s+/', trim($name)),
            fn($t) => $t !== '' && ! in_array($t, self::TITLES, true)
        );

        $suffixes = array_values(array_unique(array_filter($tokens, fn($t) => in_array($t, self::SUFFIXES, true))));
        $words    = array_values(array_unique(array_filter($tokens, fn($t) => ! in_array($t, self::SUFFIXES, true))));

        return [$words, $suffixes];
    }
}