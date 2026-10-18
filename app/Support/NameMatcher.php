<?php

namespace App\Support;
 
/**
 * One shared rule for "do these two names belong to the same person?".
 *
 * Ignored: capital letters, punctuation, extra spaces, titles (Mr., Mrs., Dr. ...)
 * and word order.
 *
 * Two names match when:
 *   - they agree on any Snr / Jnr style suffix (a father and son are never merged), and
 *   - either they are exactly the same words, or
 *   - the shorter name is fully contained in the longer one and they share at least
 *     two words.
 *
 * A word counts as the same word when it is identical, or when it is 5+ letters long
 * and differs by one letter. Words containing digits (phone numbers) must be identical.
 *
 *   "TERRY ABEIKU"            matches  "ABEIKU TERRY"
 *   "MICHAEL NII OKAI  ADDY"  matches  "MR. MICHAEL NII OKAI ADDY"
 *   "LAUREEN ADDAE DARKO"     matches  "LAUREEN ADDAE DARKU"
 *   "PHYLLIS GYAMERA GYIMAH"  matches  "PHYLLIS GYAMERA GYIMAH / 0244254114"
 *   "JOHN MENSAH"             does NOT match "JOHN ASARE"
 *   "KOFI MENSAH"             does NOT match "KOFI MENSAH JNR"
 *   "DOMOD ROOF LIMITED"      does NOT match "DOMOD ALUMINIUM LIMITED"
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
 
        // Exactly the same words (in any order)
        $sortedA = $wordsA;
        $sortedB = $wordsB;
        sort($sortedA);
        sort($sortedB);
 
        if ($sortedA === $sortedB) {
            return true;
        }
 
        // Otherwise the shorter name must sit fully inside the longer one
        [$short, $long] = count($wordsA) <= count($wordsB) ? [$wordsA, $wordsB] : [$wordsB, $wordsA];
 
        $found = 0;
 
        foreach ($short as $word) {
            foreach ($long as $key => $candidate) {
                if (self::sameWord($word, $candidate)) {
                    $found++;
                    unset($long[$key]); // each word can only be used once
                    break;
                }
            }
        }
 
        return $found >= 2 && $found === count($short);
    }
 
    private static function sameWord(string $a, string $b): bool
    {
        if ($a === $b) {
            return true;
        }
 
        // Phone numbers and other words with digits must match exactly
        if (preg_match('/\d/', $a) || preg_match('/\d/', $b)) {
            return false;
        }
 
        // Allow one letter of difference, but only in longer words
        if (strlen($a) < 5 || strlen($b) < 5) {
            return false;
        }
 
        return levenshtein($a, $b) <= 1;
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