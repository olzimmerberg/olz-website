<?php

function parse_screenshot_name($screenshot_path) {
    $has_name = preg_match('/(\/|^)([a-zA-Z\-_]+)(\.html)/i', $screenshot_path, $matches);
    if (!$has_name) {
        return $screenshot_path;
    }
    return $matches[2];
}

/**
 * Splits text into lines, without the line endings.
 *
 * @return string[]
 */
function split_into_lines($text) {
    $lines = explode("\n", str_replace("\r\n", "\n", $text));
    if (count($lines) > 0 && $lines[count($lines) - 1] === '') {
        array_pop($lines);
    }
    return $lines;
}

/**
 * Computes the line-based diff operations between two lists of lines.
 *
 * Returns a list of [operation, line] pairs, where operation is one of:
 * - 'same': the line is present on both sides
 * - 'remove': the line is only in $from_lines
 * - 'add': the line is only in $to_lines
 *
 * @param string[] $from_lines
 * @param string[] $to_lines
 * @param int      $max_matrix_cells maximum number of cells for the (quadratic)
 *                                   LCS matrix; larger diffs are reported as fully replaced
 *
 * @return array<int, array{string, string}>
 */
function compute_diff_operations($from_lines, $to_lines, $max_matrix_cells = 1000000) {
    $from_count = count($from_lines);
    $to_count = count($to_lines);

    // Strip the common prefix & suffix, so that only the actual changes need to
    // be diffed.
    $prefix_count = 0;
    while (
        $prefix_count < $from_count && $prefix_count < $to_count
        && $from_lines[$prefix_count] === $to_lines[$prefix_count]
    ) {
        $prefix_count++;
    }
    $suffix_count = 0;
    while (
        $suffix_count < $from_count - $prefix_count && $suffix_count < $to_count - $prefix_count
        && $from_lines[$from_count - 1 - $suffix_count] === $to_lines[$to_count - 1 - $suffix_count]
    ) {
        $suffix_count++;
    }

    $operations = [];
    for ($index = 0; $index < $prefix_count; $index++) {
        $operations[] = ['same', $from_lines[$index]];
    }
    $from_middle = array_slice($from_lines, $prefix_count, $from_count - $prefix_count - $suffix_count);
    $to_middle = array_slice($to_lines, $prefix_count, $to_count - $prefix_count - $suffix_count);
    foreach (compute_middle_diff_operations($from_middle, $to_middle, $max_matrix_cells) as $operation) {
        $operations[] = $operation;
    }
    for ($index = $from_count - $suffix_count; $index < $from_count; $index++) {
        $operations[] = ['same', $from_lines[$index]];
    }
    return $operations;
}

/**
 * Computes the diff operations for the (already prefix/suffix-trimmed) part that
 * actually differs. Uses a longest-common-subsequence (LCS) matrix.
 *
 * @param string[] $from_lines
 * @param string[] $to_lines
 *
 * @return array<int, array{string, string}>
 */
function compute_middle_diff_operations($from_lines, $to_lines, $max_matrix_cells = 1000000) {
    $from_count = count($from_lines);
    $to_count = count($to_lines);
    $operations = [];
    if ($from_count * $to_count > $max_matrix_cells) {
        // Too large for the quadratic LCS matrix below; report everything as replaced.
        foreach ($from_lines as $line) {
            $operations[] = ['remove', $line];
        }
        foreach ($to_lines as $line) {
            $operations[] = ['add', $line];
        }
        return $operations;
    }

    // $common_lengths[$i][$j] = LCS length of the lines starting at ($i, $j).
    $common_lengths = [];
    for ($i = $from_count; $i >= 0; $i--) {
        $common_lengths[$i] = array_fill(0, $to_count + 1, 0);
    }
    for ($i = $from_count - 1; $i >= 0; $i--) {
        for ($j = $to_count - 1; $j >= 0; $j--) {
            $common_lengths[$i][$j] = $from_lines[$i] === $to_lines[$j]
                ? $common_lengths[$i + 1][$j + 1] + 1
                : max($common_lengths[$i + 1][$j], $common_lengths[$i][$j + 1]);
        }
    }

    // Walk the LCS matrix to build the operations.
    $i = 0;
    $j = 0;
    while ($i < $from_count && $j < $to_count) {
        if ($from_lines[$i] === $to_lines[$j]) {
            $operations[] = ['same', $from_lines[$i]];
            $i++;
            $j++;
        } elseif ($common_lengths[$i + 1][$j] >= $common_lengths[$i][$j + 1]) {
            $operations[] = ['remove', $from_lines[$i]];
            $i++;
        } else {
            $operations[] = ['add', $to_lines[$j]];
            $j++;
        }
    }
    for (; $i < $from_count; $i++) {
        $operations[] = ['remove', $from_lines[$i]];
    }
    for (; $j < $to_count; $j++) {
        $operations[] = ['add', $to_lines[$j]];
    }
    return $operations;
}

/**
 * Formats a hunk range, the way `diff -u` does (omitting a count of 1).
 */
function format_diff_hunk_range($start, $count) {
    return $count === 1 ? "{$start}" : "{$start},{$count}";
}

/**
 * Renders a unified diff of two lists of lines, the way `diff -u` would.
 *
 * @param string[] $from_lines
 * @param string[] $to_lines
 *
 * @return string the empty string if there is no difference
 */
function render_unified_diff($from_lines, $to_lines, $from_label, $to_label, $context_lines = 3) {
    $operations = compute_diff_operations($from_lines, $to_lines);
    $change_indexes = [];
    foreach ($operations as $index => $operation) {
        if ($operation[0] !== 'same') {
            $change_indexes[] = $index;
        }
    }
    if (count($change_indexes) === 0) {
        return '';
    }

    // Group changes into hunks; changes close enough to each other share a hunk.
    $hunks = [];
    $hunk_start = $change_indexes[0];
    $hunk_end = $change_indexes[0];
    foreach ($change_indexes as $index) {
        if ($index - $hunk_end > 2 * $context_lines + 1) {
            $hunks[] = [$hunk_start, $hunk_end];
            $hunk_start = $index;
        }
        $hunk_end = $index;
    }
    $hunks[] = [$hunk_start, $hunk_end];

    // Line numbers (1-based) of every operation, in both versions.
    $from_line_numbers = [];
    $to_line_numbers = [];
    $from_line_number = 1;
    $to_line_number = 1;
    foreach ($operations as $index => $operation) {
        $from_line_numbers[$index] = $from_line_number;
        $to_line_numbers[$index] = $to_line_number;
        if ($operation[0] !== 'add') {
            $from_line_number++;
        }
        if ($operation[0] !== 'remove') {
            $to_line_number++;
        }
    }

    $out = "--- {$from_label}\n+++ {$to_label}\n";
    $prefixes = ['same' => ' ', 'remove' => '-', 'add' => '+'];
    foreach ($hunks as [$hunk_start, $hunk_end]) {
        $first_index = max(0, $hunk_start - $context_lines);
        $last_index = min(count($operations) - 1, $hunk_end + $context_lines);
        $from_count = 0;
        $to_count = 0;
        for ($index = $first_index; $index <= $last_index; $index++) {
            if ($operations[$index][0] !== 'add') {
                $from_count++;
            }
            if ($operations[$index][0] !== 'remove') {
                $to_count++;
            }
        }
        $from_start = $from_line_numbers[$first_index] - ($from_count === 0 ? 1 : 0);
        $to_start = $to_line_numbers[$first_index] - ($to_count === 0 ? 1 : 0);
        $from_range = format_diff_hunk_range($from_start, $from_count);
        $to_range = format_diff_hunk_range($to_start, $to_count);
        $out .= "@@ -{$from_range} +{$to_range} @@\n";
        for ($index = $first_index; $index <= $last_index; $index++) {
            [$operation, $line] = $operations[$index];
            $out .= "{$prefixes[$operation]}{$line}\n";
        }
    }
    return $out;
}

$local_dir = './screenshots/generated/';
if (!is_dir($local_dir)) {
    exit(11);
}
$local_paths = scandir($local_dir);
$local_screenshots = [];
$local_screenshot_files = [];
foreach ($local_paths as $local_path) {
    if ($local_path[0] != '.' && str_ends_with($local_path, '.html')) {
        $local_name = parse_screenshot_name($local_path);
        $local_screenshots[$local_name] = file_get_contents("{$local_dir}{$local_path}");
        $local_screenshot_files[$local_name] = $local_path;
    }
}

$remote_url = 'https://olzimmerberg.ch/';
$remote_content = '';
try {
    $remote_content = file_get_contents("{$remote_url}screenshots.json") ?? '';
} catch (Throwable $th) {
    // ignore
}
$remote_index = json_decode($remote_content, true);
if ($remote_index === null) {
    echo "No JSON screenshot index on main: {$remote_content}";
    exit(21);
}
if (!isset($remote_index['screenshot_paths'])) {
    echo "Invalid JSON screenshot index on main: {$remote_content}";
    exit(22);
}
$remote_paths = $remote_index['screenshot_paths'];
$remote_screenshots = [];
$remote_screenshot_files = [];
foreach ($remote_paths as $remote_path) {
    if (!str_ends_with($remote_path, '.html')) {
        continue;
    }
    $remote_name = parse_screenshot_name($remote_path);
    $remote_screenshots[$remote_name] = @file_get_contents("{$remote_url}screenshots/generated/{$remote_path}");
    $remote_screenshot_files[$remote_name] = $remote_path;
}

if (count($local_screenshots) > 0 && count($remote_screenshots) === 0) {
    // Main does not have any HTML screenshots yet (migration in progress).
    echo "No HTML screenshots on main yet; skipping screenshot comparison.\n";
    exit(0);
}

function parse_approvals($serialized_approvals) {
    $approvals = json_decode($serialized_approvals, true);
    if (!$approvals) {
        return [];
    }
    return $approvals;
}
$git_commit_message = shell_exec('git log -n 1 --format="%B"');
$has_approval = preg_match('/^SCREENSHOT_APPROVE=(.*)$/m', $git_commit_message, $matches);
$serialized_approvals = $has_approval ? $matches[1] : null;
echo "Approvals: {$serialized_approvals}\n";
$approvals = parse_approvals($serialized_approvals);

$all_screenshots = array_merge($local_screenshots, $remote_screenshots);
$print_name_width = 40;
$print_change_width = 8;
$print_status_width = 5;
echo "\n";
$all_approved = true;
$approvals_needed = [];
$diff = '';
foreach (array_keys($all_screenshots) as $screenshot_name) {
    if (substr($screenshot_name, 0, 13) === 'testing_error') {
        continue;
    }
    $has_local = isset($local_screenshots[$screenshot_name]);
    $has_remote = isset($remote_screenshots[$screenshot_name]);
    $change = 'UNKNOWN';
    $status = 'ERROR';
    if ($has_local && $has_remote) {
        $local_screenshot = $local_screenshots[$screenshot_name];
        $remote_screenshot = $remote_screenshots[$screenshot_name];
        if ($local_screenshot == $remote_screenshot) {
            $change = 'SAME';
            $status = '';
        } else {
            $change = 'MODIFIED';
            $remote_file = $remote_screenshot_files[$screenshot_name] ?? "{$screenshot_name}.html";
            $local_file = $local_screenshot_files[$screenshot_name] ?? "{$screenshot_name}.html";
            $diff .= render_unified_diff(
                split_into_lines($remote_screenshot),
                split_into_lines($local_screenshot),
                "main: screenshots/generated/{$remote_file}",
                "local: screenshots/generated/{$local_file}",
            );
        }
    } elseif ($has_local) {
        $change = 'ADDED';
    } elseif ($has_remote) {
        $change = 'DELETED';
    }
    if ($status == 'ERROR' && isset($approvals[$screenshot_name]) && $approvals[$screenshot_name] == 'all') {
        $status = 'OK';
    }

    if ($change != 'SAME') {
        $approvals_needed[$screenshot_name] = 'all';
    }
    if ($status == 'ERROR') {
        $all_approved = false;
    }

    $truncated_path = substr($screenshot_name, 0, $print_name_width);
    $path_for_print = str_pad($truncated_path, $print_name_width, ' ', STR_PAD_RIGHT);
    $change_for_print = str_pad($change, $print_change_width, ' ', STR_PAD_RIGHT);
    $status_for_print = str_pad($status, $print_status_width, ' ', STR_PAD_RIGHT);
    echo "{$path_for_print} {$change_for_print} {$status_for_print}\n";
}
echo "\n";
echo "To see the changes, see URL under\n";
echo "'Deploy to staging.olzimmerberg.ch' > 'Deploy'\n";
echo "and append '/screenshots'\n";
if (!$all_approved) {
    echo "\n";
    echo "Not all approvals received.\n";
    echo "To approve all, add this to the commit message:\n";
    echo "SCREENSHOT_APPROVE=".json_encode($approvals_needed);
    echo "\n";
    echo "{$diff}\n";
    exit(1);
}
