<?php

function parse_screenshot_name($screenshot_path) {
    $has_name = preg_match('/(\/|^)([a-zA-Z\-_]+)(\.png|\.jpg|\.jpeg)/i', $screenshot_path, $matches);
    if (!$has_name) {
        return $screenshot_path;
    }
    return $matches[2];
}

$should_create_diff_image = false;
$local_dir = './screenshots/';
if (!is_dir($local_dir)) {
    exit(11);
}
$local_paths = scandir($local_dir);
$local_screenshots = [];
foreach ($local_paths as $local_path) {
    if ($local_path[0] != '.') {
        $local_name = parse_screenshot_name($local_path);
        $local_screenshots[$local_name] = file_get_contents("{$local_dir}{$local_path}");
    }
}

$remote_url = 'https://olzimmerberg.ch/';
$remote_index = json_decode(
    file_get_contents("{$remote_url}screenshots/index.json.php"), true);
if ($remote_index === null) {
    echo 'No JSON screenshot index on main';
    exit(21);
}
if (!isset($remote_index['screenshot_paths'])) {
    echo 'Invalid JSON screenshot index on main';
    exit(22);
}
$remote_paths = $remote_index['screenshot_paths'];
$remote_screenshots = [];
foreach ($remote_paths as $remote_path) {
    $remote_name = parse_screenshot_name($remote_path);
    $remote_screenshots[$remote_name] = file_get_contents("{$remote_url}screenshots/generated/{$remote_path}");
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
foreach (array_keys($all_screenshots) as $screenshot_name) {
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
            if ($should_create_diff_image) {
                $local_img = imagecreatefromstring($local_screenshot);
                $remote_img = imagecreatefromstring($remote_screenshot);
                $local_wid = imagesx($local_img);
                $remote_wid = imagesx($remote_img);
                $local_hei = imagesy($local_img);
                $remote_hei = imagesy($remote_img);
                $max_wid = max($local_wid, $remote_wid);
                $max_hei = max($local_hei, $remote_hei);
                $min_wid = min($local_wid, $remote_wid);
                $min_hei = min($local_hei, $remote_hei);
                $diff_img = imagecreatetruecolor($max_wid, $max_hei);
                $diff_color = imagecolorallocate($diff_img, 255, 100, 100);
                for ($y = 0; $y < $max_hei; $y++) {
                    for ($x = 0; $x < $max_wid; $x++) {
                        if ($x >= $min_wid || $y >= $min_hei) {
                            imagesetpixel($diff_img, $x, $y, $diff_color);
                        } else {
                            $local_rgb = imagecolorat($local_img, $x, $y);
                            $remote_rgb = imagecolorat($remote_img, $x, $y);
                            if ($local_rgb != $remote_rgb) {
                                imagesetpixel($diff_img, $x, $y, $diff_color);
                            }
                        }
                    }
                }
                imagepng($diff_img, "{$local_dir}.diff_{$screenshot_name}.png");
            }
        }
    } elseif ($has_local) {
        $change = 'ADDED';
    } elseif ($has_local) {
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
    exit(1);
}
