<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmailSuppression;
use App\Models\OutreachCampaign;
use App\Models\OutreachProspect;
use Illuminate\Support\Str;
use Throwable;

/**
 * Brings a list of prospects in from a spreadsheet.
 *
 * Lists arrive as CSV from a directory, a trade show, or an export somebody made
 * in Excel — which means the file is never quite in the shape you asked for.
 * Headers vary in case, punctuation, delimiter and encoding, and the interesting
 * part of this class is not the creating of rows but the refusals: invalid
 * addresses, addresses on the opt-out list, and people who are already here.
 *
 * Everything is counted and reported. An import that silently drops a third of a
 * list is worse than one that refuses it.
 */
final class OutreachListImporter
{
    /** Accepted header names, normalised to snake_case. */
    private const HEADERS = [
        'email' => 'email',
        'e_mail' => 'email',
        'email_address' => 'email',
        'emailaddress' => 'email',
        'company' => 'company',
        'business' => 'company',
        'organisation' => 'company',
        'organization' => 'company',
        'company_name' => 'company',
        'name' => 'contact_name',
        'contact' => 'contact_name',
        'contact_name' => 'contact_name',
        'full_name' => 'contact_name',
        'first_name' => 'contact_name',
        'role' => 'role',
        'title' => 'role',
        'job_title' => 'role',
        'position' => 'role',
        'industry' => 'industry',
        'sector' => 'industry',
        'region' => 'region',
        'city' => 'region',
        'location' => 'region',
        'town' => 'region',
        'website' => 'website',
        'site' => 'website',
        'url' => 'website',
        'web' => 'website',
        'linkedin' => 'linkedin_url',
        'linkedin_url' => 'linkedin_url',
        'source' => 'source',
        'notes' => 'notes',
        'note' => 'notes',
    ];

    public function __construct(private readonly OutreachDispatcher $dispatcher) {}

    /**
     * @return array{
     *     imported: int, updated: int, suppressed: int, invalid: int,
     *     duplicates: int, enrolled: int, total: int, truncated: bool,
     *     problems: array<int, string>, missing_email_column: bool
     * }
     */
    public function import(string $path, ?OutreachCampaign $campaign = null): array
    {
        $result = [
            'imported' => 0,
            'updated' => 0,
            'suppressed' => 0,
            'invalid' => 0,
            'duplicates' => 0,
            'enrolled' => 0,
            'total' => 0,
            'truncated' => false,
            'problems' => [],
            'missing_email_column' => false,
        ];

        $handle = @fopen($path, 'r');

        if ($handle === false) {
            $result['problems'][] = 'The file could not be opened.';

            return $result;
        }

        try {
            $firstLine = fgets($handle);

            if ($firstLine === false) {
                $result['problems'][] = 'The file is empty.';

                return $result;
            }

            $delimiter = $this->delimiter($firstLine);

            rewind($handle);

            $header = $this->header(fgetcsv($handle, null, $delimiter, '"', '') ?: []);

            if (! in_array('email', $header, true)) {
                $result['missing_email_column'] = true;
                $result['problems'][] = 'No column could be read as an email address. Name one of the columns “email”.';
                fclose($handle);

                return $result;
            }

            $max = (int) config('outreach.import.max_rows');
            $toEnroll = [];

            while (($row = fgetcsv($handle, null, $delimiter, '"', '')) !== false) {
                if ($result['total'] >= $max) {
                    $result['truncated'] = true;

                    break;
                }

                $values = $this->values($header, $row);

                if ($values === []) {
                    continue;
                }

                $result['total']++;

                $outcome = $this->store($values, $result);

                if ($outcome !== null) {
                    $toEnroll[] = $outcome;
                }
            }

            if ($campaign !== null && $toEnroll !== []) {
                $enrolled = $this->dispatcher->enrollMany($toEnroll, $campaign);
                $result['enrolled'] = $enrolled['enrolled'];
            }
        } catch (Throwable $e) {
            report($e);
            $result['problems'][] = 'The file could not be read: '.$e->getMessage();
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }

        return $result;
    }

    /**
     * Create or top up a prospect, or explain why not.
     *
     * @param  array<string, string>  $values
     * @param  array<string, mixed>  $result
     */
    private function store(array $values, array &$result): ?OutreachProspect
    {
        $email = OutreachProspect::normaliseEmail($values['email'] ?? '');

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $result['invalid']++;

            if (count($result['problems']) < 8) {
                $result['problems'][] = 'Skipped “'.Str::limit($values['email'] ?? '', 40, '').'” — not a valid email address.';
            }

            return null;
        }

        // Checked before anything is created, so an opted-out address can never
        // enter the list through a fresh spreadsheet.
        if (EmailSuppression::covers($email)) {
            $result['suppressed']++;

            return null;
        }

        $existing = OutreachProspect::withTrashed()->where('email', $email)->first();

        if ($existing !== null) {
            $existing->restore();

            // Only blanks are filled: an imported file should never overwrite a
            // note or a status somebody set in the panel.
            $filled = 0;

            foreach (['company', 'contact_name', 'role', 'industry', 'region', 'website', 'linkedin_url', 'source', 'notes'] as $field) {
                if (blank($existing->{$field}) && filled($values[$field] ?? null)) {
                    $existing->{$field} = $values[$field];
                    $filled++;
                }
            }

            if ($filled > 0) {
                $existing->save();
                $result['updated']++;
            } else {
                $result['duplicates']++;
            }

            return $existing;
        }

        $prospect = OutreachProspect::create(array_filter([
            'email' => $email,
            'company' => $values['company'] ?? null,
            'contact_name' => $values['contact_name'] ?? null,
            'role' => $values['role'] ?? null,
            'industry' => $values['industry'] ?? null,
            'region' => $values['region'] ?? null,
            'website' => $values['website'] ?? null,
            'linkedin_url' => $values['linkedin_url'] ?? null,
            'source' => $values['source'] ?? 'imported list',
            'notes' => $values['notes'] ?? null,
            'status' => OutreachProspect::STATUS_NEW,
        ], fn (?string $value): bool => $value !== null && $value !== ''));

        $result['imported']++;

        return $prospect;
    }

    /**
     * Which character separates the columns. Excel in a European locale writes
     * semicolons, and guessing wrong produces one useless column named after an
     * entire row.
     */
    private function delimiter(string $line): string
    {
        $counts = [
            ',' => substr_count($line, ','),
            ';' => substr_count($line, ';'),
            "\t" => substr_count($line, "\t"),
        ];

        arsort($counts);

        return (string) array_key_first($counts);
    }

    /**
     * @param  array<int, string|null>  $cells
     * @return array<int, string>
     */
    private function header(array $cells): array
    {
        return array_map(function (?string $cell): string {
            // Excel prefixes the first cell with a UTF-8 byte-order mark, which
            // would otherwise make "email" unreadable as a header.
            $cell = preg_replace('/^[\x{FEFF}\x{200B}]+/u', '', (string) $cell) ?? '';

            return self::HEADERS[Str::snake(Str::squish(strtolower(trim($cell))), '_')] ?? Str::snake(Str::squish(strtolower(trim($cell))), '_');
        }, $cells);
    }

    /**
     * @param  array<int, string>  $header
     * @param  array<int, string|null>  $row
     * @return array<string, string>
     */
    private function values(array $header, array $row): array
    {
        $values = [];

        foreach ($header as $index => $field) {
            $cell = trim((string) ($row[$index] ?? ''));

            if ($cell === '' || $field === '') {
                continue;
            }

            $values[$field] = $cell;
        }

        return $values;
    }
}
